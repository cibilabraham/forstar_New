<?php

require_once("libjs/xajax_core/xajax.inc.php");


$xajax = new xajax();	


$xajax->configure('statusMessages', true); // For display status
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

# Seal Number Exist
	function chksealNumberExist($sealNo, $mode, $cSOId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$sealNumberObj = new SealNumber($databaseConnect);
		//$objResponse->alert($sealNo);
		$chkSealNumExistSeal = $sealNumberObj->checkSealNumberExistSeal($sealNo, $cSOId);
		if ($chkSealNumExistSeal && $sealNo!="") {
			$objResponse->assign("status", "value", "Used");
			
		}
		else
		{
		$chkSealNumExist = $sealNumberObj->checkSealNumberExist($sealNo, $cSOId);
		
		if ($chkSealNumExist && $sealNo!="") {
			$objResponse->assign("status", "value", "Blocked");
			
		} else  {
			$objResponse->assign("status", "value", "Free");
			
		}
		}
		return $objResponse;
	}

$xajax->register(XAJAX_FUNCTION,'chksealNumberExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->ProcessRequest();
?>