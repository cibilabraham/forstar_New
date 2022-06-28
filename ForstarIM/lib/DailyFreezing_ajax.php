<?php
require_once("lib/databaseConnect.php");
require_once("DailyFreezing_class.php");
require_once("libjs/xajax_core/xajax.inc.php");
require_once("lib/config.php");

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

	
	# Check Entry Exist
	function chkEntryExist($fishId, $selectDate, $soakingId, $mode, $filterDate,$processCode)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailyFreezingObj 		= new DailyFreezing($databaseConnect);
		$dateS			= explode("/", $selectDate);
		$selPreProcessDate	= $dateS[2]."-".$dateS[1]."-".$dateS[0];
		//$objResponse->alert($mode);
		$filterSelDate	=	mysqlDateFormat($filterDate);
		if ($fishId!="" && $selPreProcessDate!="") {
			# Check entry exist
			$entryExist	= $soakingObj->chkDuplicateEntryExist($fishId, $selPreProcessDate, $soakingId,$processCode);
			# Checking date confirmed
			//$confirmed	= $soakingObj->chkDaysEntryConfirmed($selPreProcessDate);
		}
		//if ($entryExist || $confirmed) {
		if ($entryExist ) {	
			if ($entryExist  && $mode==1){
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected Soaking for the date already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");			
			} else if ($entryExist  && ($filterSelDate!=$selPreProcessDate) && $mode==0 ){
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected Soaking for the date already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");
			} else {
				$objResponse->assign("divEntryExistTxt", "innerHTML", "");
				$objResponse->script("enableDPPButton($mode);");
			}
		
		
			/*if ($confirmed) {
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The pre-process entry for the day already confirmed. so kindly select another date.");
				$objResponse->script("disableDPPButton($mode);");
			} else if ($entryExist && !$confirmed && $mode==1){
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected pre-process entry for the date already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");			
			} else if ($entryExist && !$confirmed && ($filterSelDate!=$selPreProcessDate) && $mode==0 ){
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected pre-process entry for the date already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");
			} else {
				$objResponse->assign("divEntryExistTxt", "innerHTML", "");
				$objResponse->script("enableDPPButton($mode);");
			}*/
		} else {
			$objResponse->assign("divEntryExistTxt", "innerHTML", "");
			$objResponse->script("enableDPPButton($mode);");
		}			
		return $objResponse;
	}
	
	
	
	
	
	
	/*# Check Entry Exist
	function chkEntryExist_old($fishId, $selectDate, $dailyPreProcessId, $mode, $filterDate)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailyFreezingObj 		= new DailyFreezing($databaseConnect);
		$dateS			= explode("/", $selectDate);
		$selPreProcessDate	= $dateS[2]."-".$dateS[1]."-".$dateS[0];
		//$objResponse->alert($fishId);
		$filterSelDate	=	mysqlDateFormat($filterDate);
		if ($fishId!="" && $selPreProcessDate!="") {
			# Check entry exist
			$entryExist	= $soakingObj->chkDuplicateEntryExist($fishId, $selPreProcessDate, $dailyPreProcessId);
			# Checking date confirmed
			$confirmed	= $soakingObj->chkDaysEntryConfirmed($selPreProcessDate);
		}
		if ($entryExist || $confirmed) {
			if ($confirmed) {
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The pre-process entry for the day already confirmed. so kindly select another date.");
				$objResponse->script("disableDPPButton($mode);");
			} else if ($entryExist && !$confirmed && $mode==1){
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected pre-process entry for the date already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");			
			} else if ($entryExist && !$confirmed && ($filterSelDate!=$selPreProcessDate) && $mode==0 ){
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected pre-process entry for the date already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");
			} else {
				$objResponse->assign("divEntryExistTxt", "innerHTML", "");
				$objResponse->script("enableDPPButton($mode);");
			}
		} else {
			$objResponse->assign("divEntryExistTxt", "innerHTML", "");
			$objResponse->script("enableDPPButton($mode);");
		}			
		return $objResponse;
	}*/
	

	
	

	# Function Confirm
	function confirmDailyPreProcessEntry($selectDate)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailyFreezingObj 		= new DailyFreezing($databaseConnect);
		$dateS			= explode("/", $selectDate);
		$selPreProcessDate	= $dateS[2]."-".$dateS[1]."-".$dateS[0];
		
		if ($selectDate!="") {
			# Processor entry exist check
			$processorEntryExist = $soakingObj->chkDailyPPProcessorEntry($selPreProcessDate);
			if (!$processorEntryExist) {
				$zeroAvailableQtyExist = $soakingObj->chkZeroAvailableQtyExist($selPreProcessDate);
				if (!$zeroAvailableQtyExist) {
					$confirmed = "Y";
					$updateDailyPreProcessEntry = $soakingObj->updateDailyPPEntryConfirm($selPreProcessDate, $confirmed);
					if ($updateDailyPreProcessEntry) $objResponse->script("disableConfirmBtn();"); 
				} else {
					$objResponse->alert("Please check day's available qty. \nZero Qty exist.");
				}
			} else {
				$objResponse->alert("Please check day's pre-processor processed qty.");
			}
		}
		return $objResponse;
	}

	# Check Entry Exist
	function chkDPPEntryExist($selectDate, $mode)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailyFreezingObj 		= new DailyFreezing($databaseConnect);
		$dateS			= explode("/", $selectDate);
		$selPreProcessDate	= $dateS[2]."-".$dateS[1]."-".$dateS[0];
		
		if ($selectDate!="") {
			list($entryExist, $confirmed) 	= $soakingObj->chkDupEntryExist($selPreProcessDate);	
		}
		if ($entryExist) {
			/*if ($confirmed=='Y') {
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The pre-process entry for the day already confirmed. so kindly select another date.");
			} else*/
			if ($mode==1) {
				$objResponse->assign("divEntryExistTxt", "innerHTML", " The selected pre-process entry for the date already exist. so select a date , search and edit the entry.");
				$objResponse->script("disableDPPButton($mode);");
			}
			// Please make sure the fish is not existing for the selected date
			//$objResponse->script("disableDPPButton($mode);");
		} else {
			$objResponse->assign("divEntryExistTxt", "innerHTML", "");
			$objResponse->script("enableDPPButton($mode);");
		}			
		return $objResponse;
	}

	# Update Days Avaialble RM Qty
	function uptdAvailableRMQty($selDate)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailyFreezingObj 		= new DailyFreezing($databaseConnect);

		# ---------------- backend updation starts here -----------
		$sDate = mysqlDateFormat($selDate);
		if ($selDate!="") $dppRecs = $soakingObj->getDailyPreProcessRecs($sDate);
		if (sizeof($dppRecs)>0) {
			$qtyArr	= array();
			$i = 0;
			$j = 0;
			$recUpdated = false;
			foreach ($dppRecs as $dppr) {
				$prevR = $dppRecs[$i-1];
				$prevActualUsedQty 	= $prevR[7];	
				$prevProcessFrom	= $prevR[6];	
		
				$dppEntryId		= $dppr[2];
				$processFrom 		= $dppr[6];
				$auQty			= $dppr[7];
				$pcCode			= $dppr[10];
				
				if ($qtyArr[$processFrom]=="") {
					$dcEntryWeight		= $soakingObj->dailyCatchRMArrivalQty($processFrom, $sDate);
					$totalPPMOBQty		= $soakingObj->getTotalPPMOBQty($processFrom, $sDate);
					$todaysProductionQty 	= $soakingObj->getPkgQty($processFrom, $sDate);
					$todaysPPMQty		= $soakingObj->getTodaysPPQty($processFrom, $sDate);
					$todaysRPMQty		= $soakingObj->getRPMQty($processFrom, $sDate);
					$totalCSQty 		= $soakingObj->getTotalCSQty($processFrom, $sDate);
					$todaysAvailableQty = ($totalPPMOBQty+$dcEntryWeight+$todaysPPMQty+$todaysRPMQty)-($todaysProductionQty+$totalCSQty);
					$qtyArr[$processFrom] = $todaysAvailableQty;
				} else $todaysAvailableQty -= $prevActualUsedQty;		

				if ($todaysAvailableQty!=0) {
					$updateDPPEntryRec = $soakingObj->updateDPPEntryAvailableQty($dppEntryId, $todaysAvailableQty);
					$recUpdated = true;
				} else if ($todaysAvailableQty==0) {
					$j++;
				}
				$i++;
			} // Loop Ends here
			if ($recUpdated) {
				$objResponse->alert("Successfully updated the selected day's available qty.");
				$objResponse->script("document.getElementById('frmDailyPreProcess').submit();");
			} else if (sizeof($dppRecs)==$j) {
				$objResponse->alert("RM qty is not available for the selected date.");
			}	
		# ---------------- backend updation endss here -----------
		} else {
			$objResponse->alert("Failed to update the available qty. No pre-process entry found.");
		}
		return $objResponse;
	}
	
	function getRMLotId($selectDate)
	{
		$cId='';
		$entryDate=mysqlDateFormat($selectDate);
	    $objResponse = new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyFreezingObj 		= new DailyFreezing($databaseConnect);
	    $data = $soakingObj->getLotIdAfterGrading($entryDate);
		if (sizeof($data)>0) $objResponse->addDropDownOptions("rm_lot_id", $data, $cId );
	    return $objResponse;
	}	
	
	function getGrade($soakType,$processId,$inputId)
	{	
		$cId="";
		$objResponse = new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyFreezingObj 		= new DailyFreezing($databaseConnect);
		//$objResponse->alert($soakType);
		##$soakType=1==>count , $soakType=2==>grade
	   	if($soakType==1)
		{
			$objResponse->script("assignSoakType($inputId,$soakType);");
		}
		else
		{
			$objResponse->script("assignSoakType($inputId,$soakType);");
			$data = $soakingObj->getProcessGrade($processId);
			if (sizeof($data)>0) 
			{
				$objResponse->addDropDownOptions("soak-inGrade_$inputId", $data, $cId );	
				$objResponse->addDropDownOptions("soak-outGrade_$inputId", $data, $cId );
			}				
			
		}		
		
	    return $objResponse;
	}
	
	


$xajax->register(XAJAX_FUNCTION, 'getGrade', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));




$xajax->registerFunction("chkEntryExist");
$xajax->registerFunction("chkDPPEntryExist");
//$xajax->registerFunction("confirmDailyPreProcessEntry");
$xajax->register(XAJAX_FUNCTION, 'confirmDailyPreProcessEntry', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'uptdAvailableRMQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->registerFunction("getRMLotId");
$xajax->ProcessRequest();
?>