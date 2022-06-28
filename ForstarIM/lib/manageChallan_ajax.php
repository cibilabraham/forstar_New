<?php

require_once("libjs/xajax_core/xajax.inc.php");


$xajax = new xajax();	

//$xajax->configure('defaultMode', 'synchronous'); // For return value
$xajax->configure('statusMessages', true); // For display status

//$objResponse->setReturnValue($chkRecExist); // Forretrun a value from ajax function

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
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
	
	

	# Alpha code  Exist
	function chkAlphaCodeExist($alphaId,  $mainId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$manageChallanObj = new ManageChallan($databaseConnect);

		$chkAlphaExist = $manageChallanObj->checkAlphaCodeExist($alphaId, $mainId);
		if ($chkAlphaExist && $alphaId!="") {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "$alphaId is already in use.");
			//$objResponse->script("disableSPOButton($mode);");
		} else  {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "");
			//$objResponse->script("enableSPOButton($mode);");
		}
		return $objResponse;
	}
	
	
$xajax->register(XAJAX_FUNCTION, 'chkAlphaCodeExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));


$xajax->ProcessRequest();
?>