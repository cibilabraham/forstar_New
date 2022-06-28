<?php
//require_once("lib/databaseConnect.php");
//require_once("PHTCertificate_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure('statusMessages', true);
	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}
			
		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	
	}
	
	function getSuplier($supplierGroupId,$selSupplierGroupId)
		{
		
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		
		$varianceReportObj       =	new VarianceReport($databaseConnect);
		$supplierRecs 			= $varianceReportObj->filterSupplierList($supplierGroupId);
		
		if (sizeof($supplierRecs)>0) addDropDownOptions("supplierName", $supplierRecs, $selSupplierGroupId, $objResponse);
		
		return $objResponse;
		}
	
	$xajax->register(XAJAX_FUNCTION, 'getSuplier', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	//$xajax->register(XAJAX_FUNCTION,'getField', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	$xajax->ProcessRequest();
?>