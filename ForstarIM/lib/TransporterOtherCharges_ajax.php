<?php
require_once("libjs/xajax_core/xajax.inc.php");

	$xajax = new xajax();	

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
	}
	
	
	# Get Transporter Rate List  
	function getTransporterRateRec($transporterId, $mode, $transporterFunctionType, $currentId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();		
		$transporterRateListObj		= new TransporterRateList($databaseConnect);
		$transporterOtherChargesObj    = new TransporterOtherCharges($databaseConnect);

		# Rate List Id
		$rateListId = $transporterRateListObj->latestRateList($transporterId, $transporterFunctionType);

		# Check Rec Exist
		$chkRecExist = $transporterOtherChargesObj->checkEntryExist($transporterId, $rateListId, $currentId);
		if ($chkRecExist) {
			$objResponse->assign("divRecExistTxt", "innerHTML", "Please make sure the selected record is not existing.");
			$objResponse->script("disableTransporterRateButton($mode);");
		} else  {
			$objResponse->assign("divRecExistTxt", "innerHTML", "");
			$objResponse->script("enableTransporterRateButton($mode);");
		}

		$objResponse->assign("transporterRateList", "value", $rateListId);
		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'getTransporterRateRec', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));	

$xajax->ProcessRequest();
?>