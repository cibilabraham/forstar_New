<?php
require_once("lib/databaseConnect.php");
require_once("dailyrates_class.php");
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

	# Get Received By Types
	function getReceivedByTypes($processCodeId, $mode, $cpyFromChk, $cpyReceivedBy)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$processcodeObj  = new ProcessCode($databaseConnect);
		$dailyratesObj	 =	new DailyRates($databaseConnect);
		$processCodeRec	 = $processcodeObj->find($processCodeId);
		$receivedBy	 = $processCodeRec[7];

		//$objResponse->alert("$processCodeId, $mode, $cpyFromChk, $cpyReceivedBy");	

		//$updateDisplay = true;	
		if ($cpyFromChk) {
			if ($cpyReceivedBy!=$receivedBy) {
				$objResponse->alert("Please check the Process Code received type.\n ");
				$objResponse->script("document.getElementById('processCode').value='';");
			}
			//$updateDisplay = false;
		}
	
		$displayTable = "";		
		if ($receivedBy!="" ) {
			$displayTable = "<table  cellspacing='1' bgcolor='#999999' cellpadding='3' id='tblAddRecivedType'>";
			$displayTable .= "<tr bgcolor='#f2f2f2' align='center'>";
			if ($receivedBy=='G'  || $receivedBy=='B'){ 
				$displayTable .= "<td class='listing-head'>*Grade</td>";
			}
			if ($receivedBy=='C' || $receivedBy=='B') {
				$displayTable .= "<td class='listing-head' nowrap>*Count<br>(Avg)</td>";
			}
			$displayTable .= "<td class='listing-head'>Rate <br>Increase for<br> Higher Count</td>";
			$displayTable .= "<td class='listing-head'>Rate <br>Decrease for <br>Lower Count</td>";
			$displayTable .= "<td class='listing-head'>Market Rate</td>";
			$displayTable .= "<td class='listing-head'>*Decl. Rate</td>";
			$displayTable .= "<td></td>";
			$displayTable .= "</tr>";
			$displayTable .= "</table>";
		}	

		if (!$cpyFromChk) $objResponse->assign("hidReceived", "value", $receivedBy);		
		if (!$cpyFromChk) $objResponse->assign("addTable", "innerHTML", $displayTable);	
		if (!$cpyFromChk) $objResponse->script("fieldId=0;");
		if (!$cpyFromChk) $objResponse->script("addNewDailyRateItemRow('tblAddRecivedType', '$receivedBy','','','','','','');");	
		# Get Grade
		$objResponse->script("xajax_getGradeRecords(0, '$processCodeId');");		
        	return $objResponse;
	}

	function getGradeRecords($tableRowCount, $processCodeId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$processcodeObj  = new ProcessCode($databaseConnect);
		$dailyratesObj	 =	new DailyRates($databaseConnect);
		$processCodeRec	 = $processcodeObj->find($processCodeId);
		$receivedBy	 = $processCodeRec[7];		
		if ($receivedBy=='G'  || $receivedBy=='B') {
			$gradeRecords     = $dailyratesObj->fetchSelectedGrade($processCodeId);
			for ($i=0; $i<=$tableRowCount; $i++) {			
				$objResponse->addCreateOptions("selGrade_".$i, $gradeRecords, "hidGradeId_".$i);
			}			
		}
		return $objResponse;
	}

	function assignSelGrade($gradeId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		if ($gradeId) $objResponse->assign("hidGradeId_".$rowId, "value", $gradeId);
		return $objResponse;
	}

	function getSupplierRecords($landingCenterId, $cId)
	{
 		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$dailyratesObj	 =	new DailyRates($databaseConnect);
		
		$supplierRecords = $dailyratesObj->fetchSupplierRecords($landingCenterId);
		$objResponse->createOptions("supplier", $supplierRecords, $cId);	
		return $objResponse;
 	}

	function getProcessCodeRecs($fishId, $cId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$dailyratesObj	 = new DailyRates($databaseConnect);
		
		$processCodeRecs = $dailyratesObj->processCodeRecFilter($fishId);
		$objResponse->createOptions("processCode", $processCodeRecs, $cId);	
		return $objResponse;	
	}

	function getCpyFrmFishRecs($selDate, $landingCenterId, $supplierId, $cId)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$dailyratesObj	 = new DailyRates($databaseConnect);
		$existingFishRecs = $dailyratesObj->getExistingFishRecs($selDate, $landingCenterId, $supplierId);
		$objResponse->createOptions("cpyFrmFish", $existingFishRecs, $cId);
		return $objResponse;
	}

	function getCpyFrmPcsCodeRecs($selDate, $landingCenterId, $supplierId, $fishId, $cId)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$dailyratesObj	 = new DailyRates($databaseConnect);
		
		$processCodeRecs = $dailyratesObj->getExistingPcsCodeRecs($selDate, $landingCenterId, $supplierId, $fishId);
		$objResponse->createOptions("cpyFrmProcessCode", $processCodeRecs, $cId);	
		return $objResponse;	
	}

	function getCpyFrmLandgCenters($selDate, $cId)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$dailyratesObj	 = new DailyRates($databaseConnect);
		$existingLCRecs = $dailyratesObj->getExistingLandingCenters($selDate);
		$objResponse->createOptions("cpyFrmLandingCenter", $existingLCRecs, $cId);
		return $objResponse;
	}

	function getCpyFrmSupplierRecs($selDate, $landingCenterId, $cId)
	{
		$selDate = mysqlDateFormat($selDate);
 		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$dailyratesObj	 = new DailyRates($databaseConnect);
		
		$existingSupplierRecords = $dailyratesObj->getExistingSupplierRecords($selDate, $landingCenterId);
		$objResponse->createOptions("cpyFrmSupplier", $existingSupplierRecords, $cId);	
		return $objResponse;
 	}


$xajax->registerFunction("getReceivedByTypes");
$xajax->registerFunction("getGradeRecords");
$xajax->registerFunction("assignSelGrade");
$xajax->registerFunction("getSupplierRecords");
$xajax->registerFunction("getProcessCodeRecs");
$xajax->registerFunction("getCpyFrmFishRecs");
$xajax->registerFunction("getCpyFrmPcsCodeRecs");
$xajax->registerFunction("getCpyFrmLandgCenters");
$xajax->registerFunction("getCpyFrmSupplierRecs");

$xajax->register(XAJAX_FUNCTION, 'getReceivedByTypes', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getGradeRecords', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignSelGrade', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSupplierRecords', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCodeRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCpyFrmFishRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCpyFrmPcsCodeRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCpyFrmLandgCenters', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCpyFrmSupplierRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>