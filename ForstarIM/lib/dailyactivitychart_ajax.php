<?php
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();
//$xajax->configure('defaultMode', 'synchronous'); // For return value	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}

		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}		
	}
	

	/**
	* Checking duplicate entry
	*/
	function chkDupEntry($selDate, $mode, $selEntryId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailyactivitychartObj	= new DailyActivityChart($databaseConnect);
		
		$dupEntry = $dailyactivitychartObj->checkDupEntry(mysqlDateFormat($selDate), $selEntryId);

		if ($dupEntry && $selDate!="") {
			$objResponse->assign("divDupExistMsg", "innerHTML", "Daily activity entry for the date already exist. So select a date, search and edit the entry.");
			$objResponse->script("disableBtn($mode);");
		} else  {
			$objResponse->assign("divDupExistMsg", "innerHTML", "");
			$objResponse->script("enableBtn($mode);");
		}
		return $objResponse;
	}
 


// showLoading, hideLoading
$xajax->register(XAJAX_FUNCTION, 'chkDupEntry', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
 

$xajax->ProcessRequest();
?>