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

	function chkValidCNum($billingCompanyId, $selChallanNumber, $selDate, $cId, $mode)
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
	function saveChallan($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode, $fish, $processCode, $count, $countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack, $peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt, $entryGrossNetWt, $selGrade, $dailyBasketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $mode, $saveType, $userId, $cntArrVal, $delArr, $noBilling,$cntArrValdel)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
	    	$dailycatchentryObj = new DailyCatchEntry($databaseConnect);
		$grademasterObj	    = new GradeMaster($databaseConnect);
		
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

		return $objResponse;
	}
	
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
	//$xajax->register(XAJAX_FUNCTION, 'filterGrade2', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION, 'saveRMInChallan', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));	
	//$xajax->register(XAJAX_FUNCTION, 'getOtherRMInChallan', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION, 'saveCountData', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION, 'deleteCountData', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));		
	//$xajax->register(XAJAX_FUNCTION, 'getCountData', array('onResponseDelay' => 'showFnLoading','onComplete' => 'setCountData'));		


	$xajax->processRequest(); // xajax end
?>