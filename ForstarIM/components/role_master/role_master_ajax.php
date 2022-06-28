<?php
require_once("libjs/xajax_core/xajax.inc.php");
require_once 'components/base/role_master_model.php';
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
		// For Edit Mode
		function addCityOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $ov) {
					$this->script("addOption('".$ov[2]."','".$sSelectId."','".$ov[0]."','".$ov[1]."');");
	       			}
	     		}
  		}	
	}

	# installed capacity Exist
	function chkRecExist($name, $selICId)
	{
		$objResponse = new NxajaxResponse();
		$rmmObj	= new role_master_model();
		
		$chkEntryExist = $rmmObj->chkEntryExist($name, $selICId);

		if ($chkEntryExist) {			
			$objResponse->assign("divEntryExistTxt", "innerHTML", "Role already exist.");
			$objResponse->assign("entryExist", "value", 1);
		} else  {
			$objResponse->assign("divEntryExistTxt", "innerHTML", "");
			$objResponse->assign("entryExist", "value", "");
		}		
		return $objResponse;
	}
	

$xajax->register(XAJAX_FUNCTION, 'chkRecExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>