<?php
require_once("lib/databaseConnect.php");
require_once('lib/dailycatchentry_class.php');
require('libjs/xajax_core/xajax.inc.php');// xajax related functions
require_once('config.php');

$xajax = new xajax(); // create xajax ref
$xajax->configure('statusMessages', true); // For display status


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

		// Multi Dimensional array
		function addDropDown($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
			$this->script("addOption('','".$sSelectId."','','--Select--');");
			if (sizeof($options) >0) {
				foreach ($options as $k) {
					//$option=>$val
					$option = $k[0];
					$val    = $k[1];
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
				}
			}
		}

		function addSubSuppDropDown($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
			$this->script("addOption('','".$sSelectId."','','SELF');");
			if (sizeof($options) >0) {
				foreach ($options as $k) {
					//$option=>$val
					$option = $k[0];
					$val    = $k[1];
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
				}
			}
		}
		
		
		
	}


	#Select distinct Supplier
	function getSupplier($selectId, $fromDate, $tillDate, $cId, $billingCompanyId, $filterType)
	{
	    $objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();
	    $dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
	
	   if ($filterType=='SW') $selBillingCompany = "";
	   else $selBillingCompany = $billingCompanyId;
	
	    $data = $dailyCatchEntryObj->getSupplierList($fromDate, $tillDate, $selBillingCompany);
	    $objResponse->addCreateOptions($selectId, $data, $cId);
	    return $objResponse;
	}

	#Fish Records for a date range	
	function getFish($selectId, $fromDate, $tillDate, $selectSupplier, $cId, $billingCompanyId)
	{
	    $objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();
	    $dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
	    $data = $dailyCatchEntryObj->getFishList($fromDate, $tillDate, $selectSupplier, $billingCompanyId);
	    $objResponse->addCreateOptions($selectId, $data, $cId);
	    return $objResponse;
	}

	#Process Code Records
	function getProcessCode($selectId, $fromDate, $tillDate, $selectSupplier, $fishId, $cId, $billingCompanyId)
	{
	    $objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();
	    $dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
	    $data = $dailyCatchEntryObj->getProcessCodeList($fromDate, $tillDate, $selectSupplier, $fishId, $billingCompanyId);
	    $objResponse->addCreateOptions($selectId, $data, $cId);
	    return $objResponse;
	}	

	#Checking Same Entry (Fish, Processcode, Count/Grade in Same Same Challan))
	function checkSameEntryExist($challanId, $fishId, $processCodeId, $count, $gradeId)
	{
	    $objResponse = new xajaxResponse();
	    $databaseConnect = new DatabaseConnect();
	    $dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
	    //$objResponse->alert("h");	
	    $result = $dailyCatchEntryObj->checkEntryExist($challanId, $fishId, $processCodeId, $count, $gradeId);
	    $objResponse->assign("hidSameEntryExist", "value", $result);
            return $objResponse;
	}

	#Checking Count Average Same (Fish, Processcode, Count Average))
	function checkCountAverageSame($challanId, $fishId, $processCodeId, $countAverage)
	{
	    $objResponse = new xajaxResponse();
	    $databaseConnect = new DatabaseConnect();
	    $dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
	    $result = $dailyCatchEntryObj->checkSameCountAverage($challanId, $fishId, $processCodeId, $countAverage);
	    $objResponse->assign("hidSameCountAverage", "value", $result);		
            return $objResponse;
	}

	/* Using in Modify challan Number*/
	function getChallanWiseFishList($selectId, $challanNo, $cId, $billingCompanyId)
	{
		$objResponse = new NxajaxResponse();
	    	$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);	
		$data = $dailyCatchEntryObj->getRMChallanWiseFishList(trim($challanNo), $billingCompanyId);
	    	$objResponse->addCreateOptions($selectId, $data, $cId);
	    	return $objResponse;
	}

	function getChallanWiseProcessCodeList($selectId, $challanNo, $fishId, $cId, $billingCompanyId)
	{
		$objResponse = new NxajaxResponse();
	    	$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);	
		$data = $dailyCatchEntryObj->getRMChallanWiseProcessCodeList(trim($challanNo), $fishId, $billingCompanyId);
	    	$objResponse->addCreateOptions($selectId, $data, $cId);
	    	return $objResponse;
	}
	
	function getRMEntryRecords($challanNo, $fishId, $processCodeId, $cId, $billingCompanyId)
	{
		$objResponse 	 	= new NxajaxResponse();
	    	$databaseConnect 	= new DatabaseConnect();
	    	$dailyCatchEntryObj 	= new DailyCatchEntry($databaseConnect);
		$processcodeObj	    	= new ProcessCode($databaseConnect);
		$processCodeRec		= $processcodeObj->find($processCodeId);
		$receivedBy		= $processCodeRec[7];
		$getRMEntryRecords	= $dailyCatchEntryObj->getRMEntryRecords(trim($challanNo), $fishId, $processCodeId, $billingCompanyId);

		$displayTable = "<table cellpadding='0' cellspacing='0'> <tr>"; 
		$displayTable .= "<td class='fieldName' style='padding-left:5px;' nowrap>* Entries:</td><td><select name='selEntry' id='selEntry'><option value=''>-- Select--</option>";
			foreach ($getRMEntryRecords as $rme) {
				$rMEntryId = $rme[0];
				$gradeId   = $rme[1];
				$gradeName = $rme[2];
				$countValue = $rme[3];
				$effectiveWt = $rme[4];	
				$displayEntry = "";
				if ($countValue!="") {
					$displayEntry = $countValue."&nbsp;(Eff.Wt:$effectiveWt)";
				} else if ($gradeId!=0) {
					$displayEntry = $gradeName."&nbsp;(Eff.Wt:$effectiveWt)";
				}
				$selected = "";
				if ($cId==$rMEntryId) $selected = "selected";
				$displayTable .= "<option value='$rMEntryId' $selected>$displayEntry</option>";
			}
			$displayTable .= "</select></td>";
		$displayTable .= "</tr></table>";
		
		$objResponse->assign("displayCG", "innerHTML", $displayTable);	
		return $objResponse;
	}

	# Get Billing Company Rec
	function getBillingCompanyRec($billingCompanyId)
	{
	    $objResponse = new xajaxResponse();
	    $databaseConnect = new DatabaseConnect();
	    $dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
	    $billingCompanyObj  = new BillingCompanyMaster($databaseConnect);

	    # Get Alpha Code	
	    $alphaCode	= $billingCompanyObj->getBillingCompanyAlphaCode($billingCompanyId);
	    if ($billingCompanyId=="") {
		$alphaCode	= $billingCompanyObj->getDefaultBillingCompany();
	    }	
	    if ($alphaCode=="") $objResponse->alert("Please define an Alpha code.");
	    $objResponse->assign("alphaCode", "value", $alphaCode);		
            return $objResponse;
	}

	function chkValidCNum__($billingCompanyId, $selChallanNumber, $selDate, $cId, $mode)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse = new xajaxResponse();
	    	$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
		$manageChallanObj   = new ManageChallan($databaseConnect);

		if ($selDate && $selChallanNumber && $billingCompanyId) {
			$validChallanNum = $dailyCatchEntryObj->chkValidChallanNum($selDate, $selChallanNumber, $billingCompanyId);
			if ($validChallanNum) {
				# Check challan number exist
				$challanExist = $dailyCatchEntryObj->chkChallanExist($selChallanNumber, $billingCompanyId, $cId);
				if ($challanExist) {
					$objResponse->assign("challanErrMsg", "innerHTML", "Challan Number you have entered is already in database.");
					
					$objResponse->script("disableDCEButton(".$mode.");");
				} else { 
					# Check for cancelled challan number
					$cancelledChallan = $manageChallanObj->checkCancelled($selChallanNumber, $billingCompanyId);
					if ($cancelledChallan) {
						$objResponse->assign("challanErrMsg", "innerHTML", "Challan Number you have entered is already cancelled.");			
						$objResponse->script("disableDCEButton(".$mode.");");	
					} else {
						$objResponse->script("enableDCEButton(".$mode.");");
						$objResponse->assign("challanErrMsg", "innerHTML", "");
					}
				}
			} else {
				$objResponse->assign("challanErrMsg", "innerHTML", "Challan Number you have entered is not valid.");
				$objResponse->script("disableDCEButton($mode);");
			}
		}
		return $objResponse;
	}

	#Fish Records for a date range	
	function getBillingCompany($fromDate, $tillDate, $cId, $selectSupplier, $filterType)
	{
	    $objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();
	    $dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);

	    if ($filterType=='SW') $selSupplier = $selectSupplier;
	    else $selSupplier = "";

	    $getRecords = $dailyCatchEntryObj->getBillingCompanyList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $selSupplier);
	    $objResponse->addCreateOptions('billingCompanyFilter', $getRecords, $cId);

	    return $objResponse;
	}

	function chkValidDate($selDate, $billingCompany, $mode)
	{
		$challanDate = mysqlDateFormat($selDate);
		$objResponse = new xajaxResponse();
	    	$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
		$manageChallanObj   = new ManageChallan($databaseConnect);

		list($startingNumber, $endingNumber, $challanDEntryLimitDays) = $manageChallanObj->getChallanRec($challanDate, $billingCompany);
		$dateDiff = $manageChallanObj->getDateDiff($challanDate);
		$calcDiff = $dateDiff-$challanDEntryLimitDays; 
		if ($calcDiff>0 && $mode==1) {
			$objResponse->assign("validDate", "value",'N');
		} else $objResponse->assign("validDate", "value",'Y');

		return $objResponse;
	}

	# Filter Process Codes
	function filterPC($selFishId, $selProcessCodeId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
	        $getRecords = $dailyCatchEntryObj->pcRecFilter($selFishId);
	    	$objResponse->addDropDown('processCode', $getRecords, $selProcessCodeId);	
		//$bWt$pcArr = array();
		$pcVal = "pc2WtArr = '";
		$i = 0;
		foreach ($getRecords as $gr) {
			$pcId = $gr[0];
			$bWt = $gr[2];
			if ($i!=0) $pcVal .= ",";
			$pcVal .= $bWt;
			$i++;
		}	
		$pcVal .= "'";

		$objResponse->script("$pcVal;");
	        return $objResponse;
	}

	# Filter Supplier
	function filterSupplier($landingCenterId, $selSupplierId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
		$supplierMasterObj = new SupplierMaster($databaseConnect);

	        $getRecords = $supplierMasterObj->getCenterWiseActiveSuppliers($landingCenterId);

	    	$objResponse->addDropDown('mainSupplier', $getRecords, $selSupplierId);		
	        return $objResponse;
	}

	# Filter Sub-Supplier
	function filterSubSupplier($supplierId, $landingCenterId, $selSubSupplierId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
		$subsupplierObj	 = new SubSupplier($databaseConnect);
		$supplierMasterObj = new SupplierMaster($databaseConnect);

		$subSupplierRecords = $subsupplierObj->filterSubSupplierRecords($supplierId, $landingCenterId);
		$objResponse->addSubSuppDropDown('subSupplier', $subSupplierRecords, $selSubSupplierId);		

		# Get Payment By
		$paymentBy = $supplierMasterObj->getSupplierPaymentBy($supplierId);
		if ($paymentBy=='D') {
			$objResponse->script("disableFields();");
		} else {
			$objResponse->script("enableFields();");
		}
		
		$objResponse->script("reloadDeclared();");
	        return $objResponse;
	}

	# Filter grade Recs
	function filterGrade($processCodeId, $selGradeId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);
		//$processcodeObj	 = new ProcessCode($databaseConnect);
		
		$receivedBy	= $dailyCatchEntryObj->pcReceivedType($processCodeId);
		//$objResponse->alert($receivedBy);
		$gradeMasterRecords = array();
		if ($receivedBy=='G' ||  $receivedBy=='B' ){
	        	$gradeMasterRecords = $dailyCatchEntryObj->gradeRecFilter($processCodeId);
		}
		
		/*
		$processBasketWt = "";
		$processCodeIdOnchange	= $processcodeObj->processCodeRecIdFilter($processCodeId);
		if (sizeof($processCodeIdOnchange)>0) {
			$processBasketWt	=	$processCodeIdOnchange[0][4];
		}
		*/
		
	    	$objResponse->addDropDown('selGrade', $gradeMasterRecords, $selGradeId);		
		$objResponse->script("displayReceivedType('".$receivedBy."');");
		$objResponse->assign("hidReceived", "value", $receivedBy);
		//$objResponse->assign("dailyBasketWt", "value", $processBasketWt);
		$objResponse->script("selEntryType();");
		//$objResponse->script("resetGrossWt($processBasketWt);");
	        return $objResponse;
	}

	/*
	function filterGrade2($processCodeId, $selGradeId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$processcodeObj	 = new ProcessCode($databaseConnect);
		
		$processBasketWt = "";
		$processCodeIdOnchange	= $processcodeObj->processCodeRecIdFilter($processCodeId);
		if (sizeof($processCodeIdOnchange)>0) {
			$processBasketWt	=	$processCodeIdOnchange[0][4];
		}
		$objResponse->assign("dailyBasketWt", "value", $processBasketWt);
		$objResponse->script("resetGrossWt($processBasketWt);");
	        return $objResponse;
	}
	*/

	# Save & Add New Raw Material in Challan
	# $saveType ==> NC=  New Challan, RM = RM in same challan
	# $mode 2: Edit mode, 1: Add Mode
	/*
	function saveRMInChallan($fish, $processCode, $count, $countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack, $peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt, $entryGrossNetWt, $selGrade, $dailyBasketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $mode, $saveType, $userId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);
		$grademasterObj	    = new GradeMaster($databaseConnect);
		
		if ($countAverage) {
			$fetchGradeRec		=	$grademasterObj->fetchGradeRecords($countAverage);
			$avergeGradeId		=	$fetchGradeRec[0];
		}
		$selectDate = mysqlDateFormat($selectDate);
		$gradeId    = ($selGrade=="")?$avergeGradeId:$selGrade;
		$receivedBy		=	"";
		if (($count!="" || $count!=0) && $selGrade=="") $receivedBy	= 'C';
		else if($selGrade!="" && ($count=="" || $count==0)) $receivedBy	= 'G';
		else $receivedBy	= 'B';
		$basketWt	= ($dailyBasketWt=="")?0:$dailyBasketWt;
		if ($entryOption=='N') {
			$dailyCatchEntryGrossRecDel	=	$dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
			$basketWt = 0;
		}
		$ice = 0;
		
		if ($entryId && $catchEntryNewId) {
			$updateDailyCatchEntryRec = $dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage,$reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy);

			if ($updateDailyCatchEntryRec) {

				if ($saveType=='NC') {
					$tempdataRecIns=$dailycatchentryObj->addTempMaster($userId);
					if ($tempdataRecIns!="") {				
						$entryId = $databaseConnect->getLastInsertedId();
					}
				}

				if ($saveType=='RM' || $saveType=='NC') {
					$tempRecDailyCatchEntry	=	$dailycatchentryObj->addTempRecDailyCatchEntry($entryId);
					if ($tempRecDailyCatchEntry!="") {
						$catchEntryNewId	=	$databaseConnect->getLastInsertedId();
						$objResponse->script("resetCatchEntryNewId('$entryId', '$catchEntryNewId')");
					}
				}				
				//$objResponse->script("clearRMFields('$saveType', '$mode');");
				//$objResponse->alert("$mode, $saveType");
			} else {
				if ($mode==2) $objResponse->alert("Failed to updated the Daily Raw Material entry. Please make sure the values you have entered is correct.");//$msg_failDailyCatchUpdate
				else $objResponse->alert("Failed to add Daily Raw Material Entry. Please make sure the values you have entered is correct.");//$msg_failAddDailyCatch
			}
		}
		return $objResponse;
	}
	*/

	# Save in Main Table
	function saveChallan($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode, $fish, $processCode, $count, $countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack, $peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt, $entryGrossNetWt, $selGrade, $dailyBasketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $mode, $saveType, $userId, $cntArrVal, $delArr, $noBilling,$cntArrValdel,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName,$cntArrQuantityStr)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);
		$grademasterObj	    = new GradeMaster($databaseConnect);
		// echo $billingCompany;die;
		$selectDate = mysqlDateFormat($selectDate);

		//die();
		
		if ($entryId) {
			$updateDRMMainRec = $dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$weighChallanNo,$selectDate, $selectTime,$entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName);
			/* $updateDRMMainRec = $dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode); */
		}
//die();
		if ($countAverage) {
			$fetchGradeRec	= $grademasterObj->fetchGradeRecords($countAverage);
			$avergeGradeId	= $fetchGradeRec[0];
		}		
		$gradeId    = ($selGrade=="")?$avergeGradeId:$selGrade;
		$receivedBy		=	"";
		if (($count!="" || $count!=0) && $selGrade=="") $receivedBy	= 'C';
		else if($selGrade!="" && ($count=="" || $count==0)) $receivedBy	= 'G';
		else $receivedBy	= 'B';
		$basketWt	= ($dailyBasketWt=="")?0:$dailyBasketWt;
		if ($entryOption=='N') {
			$dailyCatchEntryGrossRecDel	=	$dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
			$basketWt = 0;
		}
		$ice = 0;
		
		if ($entryId && $catchEntryNewId) {
			//if ($entryTotalGrossWt!=0 || $entryTotalGrossWt!=""){
			$updateDailyCatchEntryRec = $dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage,$reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling);
			//}
			if ($updateDailyCatchEntryRec) {

			


				# Count Save Starts here
				
				if ($cntArrVal!="") {					
					$cntArr = explode(",",$cntArrVal);
					if (sizeof($cntArr)>0) {
						$no=sizeof($cntArr);
						for ($i=0;$i<sizeof($cntArr);$i++) {
							//$objResponse->alert($i);
							
							$cntDataArr = $cntArr[$i];
							$cntData = explode(":",$cntDataArr);
							$grossId	= $cntData[0];
							$grossWt	= $cntData[1];
							$basketWt	= $cntData[2];
							//$objResponse->alert($grossId);
							//$objResponse->alert($i." ".$grossWt."==".$basketWt);
							
							//if (($grossId==""||$grossId==0) && $catchEntryNewId!="" && $grossWt!="") {
								if (($grossId==""||$grossId==0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt!=0)) {
								$dailyGrossRecIns = $dailycatchentryObj->addGrossWt($grossWt, $basketWt, $catchEntryNewId);
							//} else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && $grossWt!="") {
								} else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt!=0)) {
								$grossUpdateRec = $dailycatchentryObj->updateGrossWt($grossId, $grossWt, $basketWt, $catchEntryNewId);
							}
							else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt==0)) {
								//$objResponse->alert($grossWt."==".$basketWt);
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
							}
							else if (($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && $grossWt=="") {
							
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
									}
						}
						


					}
					
				}
				



				# Count Save Ends here

				if ($cntArrValdel!="") {					
					$cntArrdel = explode(",",$cntArrValdel);
					if (sizeof($cntArrdel)>0) {
						$no=sizeof($cntArrdel);
						for ($i=0;$i<sizeof($cntArrdel);$i++) {
							//$objResponse->alert("fa--".$i.$grossId);
							
							$cntDataArr = $cntArrdel[$i];
							$cntData = explode(":",$cntDataArr);
							$grossId	= $cntData[0];
							$grossWt	= $cntData[1];
							$basketWt	= $cntData[2];
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}
					}
			}
			
			
			
			if ($cntArrQuantityStr!="") {					
					$cntArrQuantity = explode(",",$cntArrQuantityStr);
					if (sizeof($cntArrQuantity)>0) {
						$no=sizeof($cntArrQuantity);
						for ($i=0;$i<sizeof($cntArrQuantity);$i++) {
							//$objResponse->alert("fa--".$i.$grossId);
							
							$cntQuantityArr = $cntArrQuantity[$i];
							$cntQuantity = explode(":",$cntQuantityArr);
							$totalcnt	= $cntQuantity[0];
							$nameOfQuality	= $cntQuantity[1];
							$quality_wt	= $cntQuantity[2];
							$qualityPercent	= $cntQuantity[3];
							$reason	= $cntQuantity[4];
							
							$qualityId	= $cntQuantity[5];
							$weightmentStatus=$cntQuantity[6];
							$billing=$cntQuantity[7];
							$dailycatchentryObj->addEntryQuality($catchEntryNewId,$qualityId,$quality_wt,$qualityPercent,$reason,$weightmentStatus,$billing);
						}
					}
			}

			
			//die();
			
			
			
			
			

				# Delete Count data Starts here
					if ($delArr!= "") {						
						$delCountArr = explode(",",$delArr); 				
						if (sizeof($delCountArr)>0) {
							for ($i=0;$i<sizeof($delCountArr);$i++) {
								$grossId	= $delCountArr[$i];
								if ($grossId!="") $grossRecDel = $dailycatchentryObj->deleteGrossEntryWt($grossId);	
							}
						}
					}
				# Delete Count Ends here
				

				# Create New Id Section
				if ($saveType=='NC') {
					$tempdataRecIns=$dailycatchentryObj->addTempMaster($userId);
					if ($tempdataRecIns!="") {				
						$entryId = $databaseConnect->getLastInsertedId();
					}
				}

				if ($saveType=='RM' || $saveType=='NC') {
					$tempRecDailyCatchEntry	=	$dailycatchentryObj->addTempRecDailyCatchEntry($entryId);
					if ($tempRecDailyCatchEntry!="") {
						$catchEntryNewId	=	$databaseConnect->getLastInsertedId();
						$objResponse->script("resetCatchEntryNewId('$entryId', '$catchEntryNewId')");
					}
				}	

				# Other RM in challan Reload starts here -----------
				if ($entryId) $listRawRecords = $dailycatchentryObj->fetchAllRawMaterialDailyRecords($entryId);
				$resultArr = array(''=>'--Select--');
				if (sizeof($listRawRecords)>0) {
					foreach ($listRawRecords as $lrm) {
							$catchMainId		=	$lrm[0];
							$catchEntryChallanNo 	=	stripSlash($lrm[2]);
							$catchEntryProcessCode	=	stripSlash($lrm[9]);
							$rmReceivedBy		=	$lrm[11];
							$cEntryCount		=	stripSlash($lrm[12]);
							$gCode = "";
							$disGradeOrCount = $cEntryCount;
							if ($cEntryCount==""|| $cEntryCount==0 || $rmReceivedBy=='B' ) {
								$gCode	= $grademasterObj->findGradeCode($lrm[13]);
								$disGradeOrCount = $gCode;
							}
							$displayList = $catchEntryChallanNo."-".$catchEntryProcessCode."(".$disGradeOrCount.")";
							$listedDailyCatchEntryId	= $lrm[10];
							$resultArr[$listedDailyCatchEntryId] = $displayList;
					}
				}
				$objResponse->addCreateOptions("selRawMaterial", $resultArr, $cId);
				# Other RM in challan Reload ends here -------------------
			
				//$objResponse->script("clearRMFields('$saveType', '$mode');");
			} else {
				if ($mode==2) $objResponse->alert("Failed to updated the Daily Raw Material entry. Please make sure the values you have entered is correct.");//$msg_failDailyCatchUpdate
				else $objResponse->alert("Failed to add Daily Raw Material Entry. Please make sure the values you have entered is correct.");//$msg_failAddDailyCatch
			}
		}

		if($saveType=='RM')
		{
		//$objResponse->alert($rm_lot_id);
		//$objResponse->script("repeatRmLotId('$rm_lot_id');");
		$objResponse->script("repeatRmLotId();");
		$objResponse->script("fieldHidden();");
		}
		else
		{
		$objResponse->script("fieldHidden();");
		}
		
		return $objResponse;
	}
	/*function saveChallanold($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode, $fish, $processCode, $count, $countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack, $peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt, $entryGrossNetWt, $selGrade, $dailyBasketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $mode, $saveType, $userId, $cntArrVal, $delArr, $noBilling,$cntArrValdel,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);
		$grademasterObj	    = new GradeMaster($databaseConnect);
		// echo $billingCompany;die;
		$selectDate = mysqlDateFormat($selectDate);

		if ($entryId) {
			$updateDRMMainRec = $dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode);
		}

		if ($countAverage) {
			$fetchGradeRec	= $grademasterObj->fetchGradeRecords($countAverage);
			$avergeGradeId	= $fetchGradeRec[0];
		}		
		$gradeId    = ($selGrade=="")?$avergeGradeId:$selGrade;
		$receivedBy		=	"";
		if (($count!="" || $count!=0) && $selGrade=="") $receivedBy	= 'C';
		else if($selGrade!="" && ($count=="" || $count==0)) $receivedBy	= 'G';
		else $receivedBy	= 'B';
		$basketWt	= ($dailyBasketWt=="")?0:$dailyBasketWt;
		if ($entryOption=='N') {
			$dailyCatchEntryGrossRecDel	=	$dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
			$basketWt = 0;
		}
		$ice = 0;
		
		if ($entryId && $catchEntryNewId) {
			//if ($entryTotalGrossWt!=0 || $entryTotalGrossWt!=""){
			$updateDailyCatchEntryRec = $dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage,$reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling);
			//}
			if ($updateDailyCatchEntryRec) {

			


				# Count Save Starts here
				
				if ($cntArrVal!="") {					
					$cntArr = explode(",",$cntArrVal);
					if (sizeof($cntArr)>0) {
						$no=sizeof($cntArr);
						for ($i=0;$i<sizeof($cntArr);$i++) {
							//$objResponse->alert($i);
							
							$cntDataArr = $cntArr[$i];
							$cntData = explode(":",$cntDataArr);
							$grossId	= $cntData[0];
							$grossWt	= $cntData[1];
							$basketWt	= $cntData[2];
							//$objResponse->alert($grossId);
							//$objResponse->alert($i." ".$grossWt."==".$basketWt);
							
							//if (($grossId==""||$grossId==0) && $catchEntryNewId!="" && $grossWt!="") {
								if (($grossId==""||$grossId==0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt!=0)) {
								$dailyGrossRecIns = $dailycatchentryObj->addGrossWt($grossWt, $basketWt, $catchEntryNewId);
							//} else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && $grossWt!="") {
								} else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt!=0)) {
								$grossUpdateRec = $dailycatchentryObj->updateGrossWt($grossId, $grossWt, $basketWt, $catchEntryNewId);
							}
							else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt==0)) {
								//$objResponse->alert($grossWt."==".$basketWt);
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
							}
							else if (($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && $grossWt=="") {
							
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
									}
						}
						


					}
					
				}
				



				# Count Save Ends here

				if ($cntArrValdel!="") {					
					$cntArrdel = explode(",",$cntArrValdel);
					if (sizeof($cntArrdel)>0) {
						$no=sizeof($cntArrdel);
						for ($i=0;$i<sizeof($cntArrdel);$i++) {
							//$objResponse->alert("fa--".$i.$grossId);
							
							$cntDataArr = $cntArrdel[$i];
							$cntData = explode(":",$cntDataArr);
							$grossId	= $cntData[0];
							$grossWt	= $cntData[1];
							$basketWt	= $cntData[2];
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}
					}
			}


				# Delete Count data Starts here
					if ($delArr!= "") {						
						$delCountArr = explode(",",$delArr); 				
						if (sizeof($delCountArr)>0) {
							for ($i=0;$i<sizeof($delCountArr);$i++) {
								$grossId	= $delCountArr[$i];
								if ($grossId!="") $grossRecDel = $dailycatchentryObj->deleteGrossEntryWt($grossId);	
							}
						}
					}
				# Delete Count Ends here
				

				# Create New Id Section
				if ($saveType=='NC') {
					$tempdataRecIns=$dailycatchentryObj->addTempMaster($userId);
					if ($tempdataRecIns!="") {				
						$entryId = $databaseConnect->getLastInsertedId();
					}
				}

				if ($saveType=='RM' || $saveType=='NC') {
					$tempRecDailyCatchEntry	=	$dailycatchentryObj->addTempRecDailyCatchEntry($entryId);
					if ($tempRecDailyCatchEntry!="") {
						$catchEntryNewId	=	$databaseConnect->getLastInsertedId();
						$objResponse->script("resetCatchEntryNewId('$entryId', '$catchEntryNewId')");
					}
				}	

				# Other RM in challan Reload starts here -----------
				if ($entryId) $listRawRecords = $dailycatchentryObj->fetchAllRawMaterialDailyRecords($entryId);
				$resultArr = array(''=>'--Select--');
				if (sizeof($listRawRecords)>0) {
					foreach ($listRawRecords as $lrm) {
							$catchMainId		=	$lrm[0];
							$catchEntryChallanNo 	=	stripSlash($lrm[2]);
							$catchEntryProcessCode	=	stripSlash($lrm[9]);
							$rmReceivedBy		=	$lrm[11];
							$cEntryCount		=	stripSlash($lrm[12]);
							$gCode = "";
							$disGradeOrCount = $cEntryCount;
							if ($cEntryCount==""|| $cEntryCount==0 || $rmReceivedBy=='B' ) {
								$gCode	= $grademasterObj->findGradeCode($lrm[13]);
								$disGradeOrCount = $gCode;
							}
							$displayList = $catchEntryChallanNo."-".$catchEntryProcessCode."(".$disGradeOrCount.")";
							$listedDailyCatchEntryId	= $lrm[10];
							$resultArr[$listedDailyCatchEntryId] = $displayList;
					}
				}
				$objResponse->addCreateOptions("selRawMaterial", $resultArr, $cId);
				# Other RM in challan Reload ends here -------------------
			
				//$objResponse->script("clearRMFields('$saveType', '$mode');");
			} else {
				if ($mode==2) $objResponse->alert("Failed to updated the Daily Raw Material entry. Please make sure the values you have entered is correct.");//$msg_failDailyCatchUpdate
				else $objResponse->alert("Failed to add Daily Raw Material Entry. Please make sure the values you have entered is correct.");//$msg_failAddDailyCatch
			}
		}

		if($saveType=='RM')
		{
		//$objResponse->alert($rm_lot_id);
		//$objResponse->script("repeatRmLotId('$rm_lot_id');");
		$objResponse->script("repeatRmLotId();");
		}
		
		return $objResponse;
	}*/
	
	/*
	function saveChallanREMOVED($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);

		$selectDate = mysqlDateFormat($selectDate);

		if ($entryId) {
			$updateDRMMainRec = $dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode);
		}
		return $objResponse;
	}
	*/


	# Update basket Wt
	function updateBWt($catchEntryNewId, $bWt)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);

		if ($catchEntryNewId && $bWt!=0) $updateBasketWtRec = $dailycatchentryObj->updateBasketWt($bWt, $catchEntryNewId);

		return $objResponse;
	}

	/*
	function getOtherRMInChallan($lastId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);
		$grademasterObj	    = new GradeMaster($databaseConnect);

		if ($lastId) $listRawRecords = $dailycatchentryObj->fetchAllRawMaterialDailyRecords($lastId);
		$resultArr = array(''=>'--Select--');
		foreach ($listRawRecords as $lrm) {
				$catchMainId		=	$lrm[0];
				$catchEntryChallanNo 	=	stripSlash($lrm[2]);
				$catchEntryProcessCode	=	stripSlash($lrm[9]);
				$rmReceivedBy		=	$lrm[11];
				$cEntryCount		=	stripSlash($lrm[12]);
				$gCode = "";
				$disGradeOrCount = $cEntryCount;
				if ($cEntryCount==""|| $cEntryCount==0 || $rmReceivedBy=='B' ) {
					$gCode	= $grademasterObj->findGradeCode($lrm[13]);
					$disGradeOrCount = $gCode;
				}
				$displayList = $catchEntryChallanNo."-".$catchEntryProcessCode."(".$disGradeOrCount.")";
				$listedDailyCatchEntryId	= $lrm[10];
				$resultArr[$listedDailyCatchEntryId] = $displayList;
		}
		$objResponse->addCreateOptions("selRawMaterial", $resultArr, $cId);
		return $objResponse;
	}
	*/

	# Save Count Data	
	/*
	function saveCountData($grossId, $grossWt, $basketWt, $catchEntryNewId)
	{	
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);	

		if (($grossId==""||$grossId==0) && $catchEntryNewId!="" && $grossWt!="") {
			$dailyGrossRecIns = $dailycatchentryObj->addGrossWt($grossWt, $basketWt, $catchEntryNewId);
		} else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && $grossWt!="") {
			$grossUpdateRec = $dailycatchentryObj->updateGrossWt($grossId, $grossWt, $basketWt, $catchEntryNewId);
		}
		return $objResponse;
	}
	*/

	/*
	function deleteCountData($grossId)
	{	
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);	

		$grossRecDel = $dailycatchentryObj->deleteGrossEntryWt($grossId);			
		return $objResponse;
	}
	*/
	
	/*
	function getCountData($mainId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailyCatchEntryObj = new DailyCatchEntry($databaseConnect);

	        $getRecords = $dailyCatchEntryObj->fetchAllGrossRecords($mainId);
		//$bWt$pcArr = array();
		$gWtVal = "grossWtArr = '";
		$bWtVal = "bWtArr = '";		
		$idVal = "idArr = '";
		
		$i = 0;
		foreach ($getRecords as $gr) {
			$cEntryId = $gr[0];
			$gWt  = $gr[1];
			$bWt = $gr[2];
			if ($i!=0) {
				$bWtVal .= ",";
				$gWtVal .= ",";
				$idVal .= ",";
			}
			$gWtVal .= $gWt;
			$bWtVal .= $bWt;
			$idVal .= $cEntryId;
			$i++;
		}	
		$gWtVal .= "'";
		$bWtVal .= "'";
		$idVal .= "'";

		$objResponse->script("$gWtVal;");
		$objResponse->script("$bWtVal;");
		$objResponse->script("$idVal;");
	        return $objResponse;
	}
	*/
	function supplierDetails($rmLotId, $selDailycatchentryId)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		
		
		$dailycatchentryObj 	= new DailyCatchEntry($databaseConnect);		
		$supplierRecs 			= $dailycatchentryObj->getSupplierDetail($rmLotId);
		// $pondDetails = $supplierRecs[1];
		// $count_code = $supplierRecs[2];
		$vehicleNumber = $supplierRecs[1];
		//$companyName[$supplierRecs[2]] = $supplierRecs[5];
		$unit[$supplierRecs[3]] = $supplierRecs[6];
		//$supplierChallanNo = $supplierRecs[4];
		$supplierGroup=$supplierRecs[7];
		
		
		//$supplier[$supplierRecs[8]]=$supplierRecs[9];
		$supplierIds   = explode(',',$supplierRecs[8]);
		$supplierNames = explode(',',$supplierRecs[9]);
		$supplier[''] = '--Select--';
		$i=0;
		foreach($supplierIds as $v)
		{
			$supplier[$v] = $supplierNames[$i];
			$i++;
		}
		
		
		if (sizeof($vehicleNumber)>0) {
		$objResponse->assign("LotVechicleNo", "value", $vehicleNumber);
		}
		// if (sizeof($supplierChallanNo)>0) {
		// $objResponse->assign("supplyChallanNo", "value", $supplierChallanNo);
		// }
		if (sizeof($supplierGroup)>0) {
		$objResponse->assign("supplierGroup", "value", $supplierGroup);
		}
		
		// if (sizeof($companyName)>0) {
		//$objResponse->alert('aa');
		// addDropDownOptions("billingCompany", $companyName, $selDailycatchentryId, $objResponse);
		// }
		if (sizeof($unit)>0) {
		//$objResponse->alert('aa');
		addDropDownOptions("lotUnit", $unit, $selDailycatchentryId, $objResponse);
		}
		
		if (sizeof($supplier)>0) {
		//$objResponse->alert('aa');
		addDropDownOptions("payment", $supplier, $selDailycatchentryId, $objResponse);
		}
		
		return $objResponse;
	}
	
	function pondNames($supplier,$rmLotId, $selSupplier)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		//$objResponse->alert($rmLotId);
		//$objResponse->alert($gradeTypeLenghth);
		
		$dailycatchentryObj 	= new DailyCatchEntry($databaseConnect);		
		$pondRecs 			= $dailycatchentryObj->getPondName($supplier,$rmLotId);
		
		// $objResponse->alert(sizeof($pondRecs));
		if (sizeof($pondRecs)>1) {
		//$objResponse->alert('aa');
			addDropDownOptions("pondName", $pondRecs, $selSupplier, $objResponse);
		}
		else
		{
			$fish[''] = '--Select--';$selPond = '';
			$fishes = $dailycatchentryObj->getAllSpecies();
			if(sizeof($fishes) > 0)
			{
				foreach($fishes as $fishVal)
				{
					$fish[$fishVal[0]] = $fishVal[1];
				}
			}
			addDropDownOptions("fish", $fish, $selPond, $objResponse);
		}
		return $objResponse;
	}
	
	
	
	
	function getCountCode($pondName,$rmLotId,$supplier,$selPond)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		//$objResponse->alert($rmLotId);
		//$objResponse->alert($gradeTypeLenghth);
		$fish=array(); $process=array();
		$dailycatchentryObj 	= new DailyCatchEntry($databaseConnect);		
		$countRecs 			= $dailycatchentryObj->getCode($pondName,$rmLotId);
		
		//$fishRecs 			= $dailycatchentryObj->getCode($pondName,$rmLotId);
		
		 $countCode = $countRecs[0];
			
			
			if(isset($countRecs[1]) && $countRecs[1] != '')
			{
				//$fish[''] ='';
				$fish[$countRecs[1]] = $countRecs[2];
			}
			else
			{
				$fish[''] = '--Select--';
				$fishes = $dailycatchentryObj->getAllSpecies();
				if(sizeof($fishes) > 0)
				{
					foreach($fishes as $fishVal)
					{
						$fish[$fishVal[0]] = $fishVal[1];
					}
				}
			}
		 
			if(isset($countRecs[3]) && $countRecs[3] != '')
			{
				//$fish[''] ='';
				$process[$countRecs[3]] = $countRecs[4];
				
			}
			else
			{
			$process[''] = '--Select--';
			}
			
		
		if (sizeof($countCode)>0) {
		$objResponse->assign("count_code", "value", $countCode);
		}
		
		
		if (sizeof($fish)>0) {
		//$objResponse->alert('aa');
		addDropDownOptions("fish", $fish, $selPond, $objResponse);
		}
		
		if (sizeof($process)>0) {
		//$objResponse->alert('aa');
		addDropDownOptions("processCode", $process, $selPond, $objResponse);
		}
		
		
		$result = $dailycatchentryObj->getQualityDetails($supplier,$pondName,$rmLotId);
		//$returnVal = '<input type="hidden" name="total_new_entry" id="total_new_entry" value="'.sizeof($result).'" />';
		$resSize=sizeof($result);
		$objResponse->assign("total_new_entry", "value", $resSize);
		if(sizeof($result)>0)
		{
			$i = 0;
			foreach($result as $res)
			{
			
			 $returnVal= '<tr>
							  <td nowrap="" class="fieldName1">'.$res[1].'</td>
							  <td nowrap="" class="listing-item">
						     <input type="text" readonly="" style="text-align:right" size="3" value="0.00" id="qualityPercent_'.$i.'" name="qualityPercent_'.$i.'">&nbsp;%
							 <input type="hidden" name="quality_new_'.$i.'" id="quality_new_'.$i.'" value="'.ucfirst($res[1]).'" />
							 <input type="hidden" name="qualityId_'.$i.'" id="qualityId_'.$i.'" value="'.$res[0].'" />
							  <input type="hidden" name="weightmentStatus_'.$i.'" id="weightmentStatus_'.$i.'" value="1" />
							  <input type="hidden" name="Status_'.$i.'" id="Status_'.$i.'" value="" /> 
							  <input type="checkbox" name="billing_'.$i.'" id="billing_'.$i.'" value="" style="display:none;"  />
							 
							</td>
							<td nowrap="" class="listing-item">
							<table>
							<tbody><tr>
								<td nowrap="" class="listing-item">
									<input type="text" autocomplete="off" style="text-align: right" value="0" onkeyup="return effectiveWtNew();" size="5" id="qualityWeight_'.$i.'" name="qualityWeight_'.$i.'">&nbsp;Kg
								</td>
								<td nowrap="" class="fieldName1">Reason</td>
								<td class="listing-item"><input type="text" autocomplete="off" value="" tabindex="26" size="20" id="qualityReason_'.$i.'" name="qualityReason_'.$i.'"></td>
							</tr>
							</tbody></table>
							</td>		
							</tr>';
			
			//<input type="checkbox" name="billing_'.$i.'" id="billing_'.$i.'" checked="checked" value="1" style="display:none;"  />
				// $returnVal.= '<tr>
							  // <td nowrap="" class="fieldName1">'.$res[1].'</td>
							  // <td nowrap="" class="listing-item">
						     // <input type="text" readonly="" style="text-align:right" size="3" value="0.00" id="entry'.ucfirst($res[1]).'Percent" name="entry'.ucfirst($res[1]).'Percent">&nbsp;%
							 // <input type="hidden" name="entry_new_'.$i.'" id="entry_new_'.$i.'" value="'.ucfirst($res[1]).'" />
							 // <input type="hidden" name="qualityId_'.$i.'" id="qualityId_'.$i.'" value="'.$res[0].'" />
							// </td>
							// <td nowrap="" class="listing-item">
							// <table>
							// <tbody><tr>
								// <td nowrap="" class="listing-item">
									// <input type="text" autocomplete="off" style="text-align: right" value="0" onkeyup="return effectiveWtNew('.$res[0].');" size="5" id="entry'.ucfirst($res[1]).'" name="entry'.ucfirst($res[1]).'">&nbsp;Kg
								// </td>
								// <td nowrap="" class="fieldName1">Reason</td>
								// <td class="listing-item"><input type="text" autocomplete="off" value="" tabindex="26" size="20" id="reason'.ucfirst($res[1]).'" name="reason'.ucfirst($res[1]).'"></td>
							// </tr>
							// </tbody></table>
							// </td>		
							// </tr>';
				$i++;
			}
			// $objResponse->alert($returnVal);
			$objResponse->assign("additonalWastage", "innerHTML", $returnVal);
			$j=0;
			foreach($result as $res)
			{
			$objResponse->script("qualityCheckValue('$res[2]','$j');");
			//qualityCheckValue($val,$j);
			$j++;
			}
		}
		
		return $objResponse;
	}
	function getSuppierDetails($rm_lot_id)
	{
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		// $objResponse->alert($rm_lot_id);		
		$dailycatchentryObj 	= new DailyCatchEntry($databaseConnect);	
		$resVal  				= $dailycatchentryObj->getLotIdName($rm_lot_id);		
		$result  				= $dailycatchentryObj->getSuppierDetails($rm_lot_id);
		//print_r($result);
		$returnVal = '';
		$makePayment = 0;
		if(sizeof($result) > 0)
		{
			$i = 0;
			$returnVal.= '<table width="100%">';
			
			$returnVal.='<tr><td class="listing-head" colspan="4"> '.$resVal[0].' - Suppliers  </td></tr>';
			//$returnVal.='<tr><td class="listing-head" colspan="4"> '.$result[0][14].' - Suppliers  </td></tr>';
			$returnVal.='<tr>';
			foreach($result as $res)
			{ 
				if($i!=0)
				{
					if($i%4 == 0)
					{
						$returnVal.= '</tr><tr>';
					}
				}
				if($res[22] == 'receipt')
				{
					$supplierId = $res[6];
					$supplierName = $res[7];
					$unitId = $res[3];
					$companyId=$res[2];
					$unitName=$res[17];
					$pondId=0;
					$supplierChallanNo = $res[5];
					
					if($res[15] == 'D')
					{
						$makePayment = 1;
					}
				}
				else
				{
					$supplierId = $res[11];
					$supplierName = $res[12];
					$unitId = $res[9];
					$companyId=$res[8];
					$unitName=$res[18];
					$pondId=$res[21];
					$supplierChallanNo = $res[13];
					if($res[16] == 'D')
					{
						$makePayment = 1;
					}
				}
				 $returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.'</a></td>';
				// $returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$unitId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.'</a></td>';
				// $i++;
			}
			$returnVal.= '</tr></table>';
		}
		
		$objResponse->assign("popupcontent", "innerHTML", $returnVal);	
		return $objResponse;
	}
	function getSuppierDetails_athi($rm_lot_id)
	{
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		// $objResponse->alert($rm_lot_id);		
		$dailycatchentryObj 	= new DailyCatchEntry($databaseConnect);		
		$result  				= $dailycatchentryObj->getSuppierDetails($rm_lot_id);
		$returnVal = '';
		$makePayment = 0;
		if(sizeof($result) > 0)
		{
			$i = 0;
			$returnVal.= '<table width="100%">';
			$returnVal.='<tr><td class="listing-head" colspan="4"> '.$result[0][14].' - Suppliers  </td></tr>';
			$returnVal.='<tr>';
			foreach($result as $res)
			{
				if($i%4 == 0)
				{
					$returnVal.= '</tr><tr>';
				}
				if($res[1] == '')
				{
					$supplierId = $res[6];
					$supplierName = $res[7];
					$unitId = $res[3];
					$companyId=$res[2];
					$unitName=$res[17];
					$pondId=0;
					$supplierChallanNo = $res[5];
					
					if($res[15] == 'D')
					{
						$makePayment = 1;
					}
				}
				else
				{
					$supplierId = $res[11];
					$supplierName = $res[12];
					$unitId = $res[9];
					$companyId=$res[8];
					$unitName=$res[18];
					$pondId=$res[21];
					$supplierChallanNo = $res[13];
					if($res[16] == 'D')
					{
						$makePayment = 1;
					}
				}
				 $returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.'</a></td>';
				// $returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$unitId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.'</a></td>';
				// $i++;
			}
			$returnVal.= '</tr></table>';
		}
		
		$objResponse->assign("popupcontent", "innerHTML", $returnVal);	
		return $objResponse;
	}
	function getSuppierDetails_vel($rm_lot_id)
	{
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		// $objResponse->alert($rm_lot_id);		
		$dailycatchentryObj 	= new DailyCatchEntry($databaseConnect);		
		$result  				= $dailycatchentryObj->getSuppierDetails($rm_lot_id);
		$returnVal = '';
		$makePayment = 0;
		if(sizeof($result) > 0)
		{
			$i = 0;
			$returnVal.= '<table width="100%">';
			$returnVal.='<tr><td class="listing-head" colspan="4"> '.$result[0][14].' - Suppliers  </td></tr>';
			$returnVal.='<tr>';
			foreach($result as $res)
			{
				if($i%4 == 0)
				{
					$returnVal.= '</tr><tr>';
				}
				if($res[1] == '')
				{
					$supplierId = $res[6];
					$supplierName = $res[7];
					$unitId = $res[3];
					$supplierChallanNo = $res[5];
					if($res[15] == 'D')
					{
						$makePayment = 1;
					}
				}
				else
				{
					$supplierId = $res[11];
					$supplierName = $res[12];
					$unitId = $res[9];
					$supplierChallanNo = $res[13];
					if($res[16] == 'D')
					{
						$makePayment = 1;
					}
				}
				$returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$unitId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.'</a></td>';
				$i++;
			}
			$returnVal.= '</tr></table>';
		}
		
		$objResponse->assign("popupcontent", "innerHTML", $returnVal);	
		return $objResponse;
	}
	function getQualityDet($qualityId,$rowcnt)
	{
	$objResponse 			= new xajaxResponse();		
	$databaseConnect 		= new DatabaseConnect();
		// $objResponse->alert($rm_lot_id);		
	$dailycatchentryObj 	= new DailyCatchEntry($databaseConnect);	
	//	$objResponse->alert($qualityId);		
	$returnVal  				= $dailycatchentryObj->getSingleQuantityDet($qualityId);
	//$objResponse->alert($returnVal[0]);
	//$objResponse->assign("qualityId_.'$rowcnt'.", "value", $returnVal[0]);
	$qualityName=ucfirst($returnVal[1]);
	//$objResponse->alert($qualityName);
	$objResponse->assign("quality_new_$rowcnt", "value",$qualityName );
	$objResponse->assign("billing_$rowcnt", "value",$returnVal[2] );
	$objResponse->script("qualityCheckValue('$returnVal[2]','$rowcnt');");
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION,'getQualityDet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'supplierDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getCountCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'pondNames', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

	
	
	$xajax->register(XAJAX_FUNCTION, 'getSupplier', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getFish', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getProcessCode', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkSameEntryExist', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkCountAverageSame', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getChallanWiseFishList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getChallanWiseProcessCodeList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getRMEntryRecords', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getBillingCompanyRec', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkValidCNum', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getBillingCompany', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkValidDate', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'filterPC', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'filterSupplier', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'filterSubSupplier', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'filterGrade', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'saveChallan', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'updateBWt', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getSuppierDetails', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION, 'saveRMInChallan', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));	
	//$xajax->register(XAJAX_FUNCTION, 'getOtherRMInChallan', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION, 'saveCountData', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION, 'deleteCountData', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));		
	//$xajax->register(XAJAX_FUNCTION, 'getCountData', array('onResponseDelay' => 'showFnLoading','onComplete' => 'setCountData'));		


	$xajax->processRequest(); // xajax end
?>