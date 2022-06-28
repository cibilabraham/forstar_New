<?php
require_once("libjs/xajax_core/xajax.inc.php");

	$xajax = new xajax();	
	//$xajax->configure('defaultMode', 'synchronous' ); // For return value

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   				if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}				
	}

	# Get Process Code Records
	function getProcessCodeRecords($fishId, $rowId, $selPCId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();		
		$processcodeObj		= new ProcessCode($databaseConnect);
		
		
		# Process Code Records
		$pcRecords = $processcodeObj->getProcessCodeRecs($fishId);
		$objResponse->addCreateOptions("selProcessCode_".$rowId, $pcRecords,$selPCId);		
		return $objResponse;			
	} 

	# Get Brand Recs
	function getBrandRecs($customerId, $selBrandId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$brandObj	 = new Brand($databaseConnect);
		# get Recs
		$brandRecs     = $brandObj->getBrandRecords($customerId);
		$objResponse->addCreateOptions("brand", $brandRecs, $selBrandId);

		return $objResponse;
	}



$xajax->register(XAJAX_FUNCTION, 'getProcessCodeRecords', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>