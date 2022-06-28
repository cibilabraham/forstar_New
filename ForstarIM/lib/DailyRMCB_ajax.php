<?php
//require_once("lib/databaseConnect.php");
//require_once("DailyRMCB_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

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

		function createOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
				}
			}
		}	
	}

	
	# Get Process code Recs
	function getPCRecs($fishId, $rowId, $pcId, $cId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$dailyRMCBObj    = new DailyRMClosingBalance($databaseConnect);
		//$objResponse->alert($pcId);
		$processCodeRecs = $dailyRMCBObj->pcRecFilter($fishId, $pcId);
		$objResponse->createOptions("exptPCode_$rowId", $processCodeRecs, $cId);	
		return $objResponse;	
	}

	function getDailyClosingBalance($selectDate,$recordsFilterId,$companyId,$unitId,$mode)
	{
		$selectDate=mysqlDateFormat($selectDate);
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$dailyRMCBObj    = new DailyRMClosingBalance($databaseConnect);
		//$objResponse->alert($pcId);
		$entryExist = $dailyRMCBObj->dailyClosingBalance($selectDate,$recordsFilterId,$companyId,$unitId);
		if ($entryExist && $mode==1)
			{
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected daily closing balance for the date, company and unit already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");	
			} 
			else
			{
				$objResponse->assign("divEntryExistTxt", "innerHTML", "");
				$objResponse->script("enableDPPButton($mode);");
			}
		return $objResponse;	
	}



$xajax->register(XAJAX_FUNCTION, 'getDailyClosingBalance', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPCRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>