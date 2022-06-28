<?php
	require("include/include.php");
	require_once('lib/dailycatchentry_ajax.php');
	ob_start();	
//printr($p);
	$rmLotIds  = $dailycatchentryObj->getAllLotIds();
	
	$err			=	"";
	$errDel			=	"";
	$entryGrossNetWt	=	"";
	$addMode		=	false;
	$editMode		=	false;
	$addRawMaterial		=	false;
	$addRaw			=	false;	
	$basketWtChange 	= 	false;
	$addSubSupplier 	= 	false;
	$recordFish		=	"";
	$processCode		=	"";
	$receivedBy		=	"";
	$catchEntryNewId	=	"";
	$offset			=	"";
	$displayListEnabled 		= false;
	$rmLotIdRepeat=false;
	$dateSelection = "?supplyFrom=".$p["supplyFrom"]."&supplyTill=".$p["supplyTill"]."&pageNo=".$p["pageNo"]. "&supplier=".$p["supplier"]."&selFish=".$p["selFish"]."&selProcesscode=".$p["selProcesscode"]. "&selRecord=".$p["selRecord"]."&filterType=".$p["filterType"]."&billingCompanyFilter=".$p["billingCompanyFilter"];

	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false;

	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	//----------------------------------------------------------


	if ($p["cmdCancel"]!="") {
		$editId = "";
		$dailyCatchentryId	=	"";
		$editMode		=	false;
		$addMode		=	false;
		$addRawMaterial		=	false;
		$addRaw			=	false;
		$rmLotIdRepeat=false;
	}
	
	/*
	foreach($p as $val =>$key) {
	echo "<br>$val = $key";
	}
	*/

	#Clear All Form Fields
	function clearAllFields()
	{		
		$p["unit"]		= "";
		$p["vechicleNo"]	= "";
		$p["lotUnit"]	= "";
		$p["LotVechicleNo"]	= "";
		$p["lotIdAvailable"]	= "";
		$p["supplierGroup"]	= "";
		$p["pondName"]	= "";
		$p["landingCenter"] 	= "";
		$p["fish"]    		= "";
		$p["processCode"]     	= "";
		$p["supplyChallanNo"]	= "";
		$p["supplyLotChallanNo"]	= "";
		$p["weighChallanNo"]    =     "";
		$p["mainSupplier"]     	=     "";
		$p["subSupplier"]     	=     "";
		$p["ice"]     		=     "";
		$p["count"]     	=     "";
		$p["countAverage"]     	=     "";
		$p["entryLocal"]     	=     "";
		$p["entryWastage"]     	=     "";
		$p["entrySoft"]     	=     "";
		$p["reasonAdjust"]     	=     "";
		$p["entryAdjust"]     	=     "";
		$p["goodPack"]     	=     "";
		$p["peeling"]     	=     "";
		$p["entryRemark"]     	=     "";
		$p["entryActualWt"]     =     "";
		$p["entryEffectiveWt"]  =     "";
		$p["entryTotalGrossWt"] =     "";
		$p["entryTotalBasketWt"]=     "";	
		$p["entryGrossNetWt"]   =     "";
		$p["declWeight"]    	=     "";			
		$p["declCount"]     	=     "";
		$p["selectDate"]     	=     "";
		$p["entryId"]		=	  ""; 
		$p["catchEntryNewId"]	=	"";
		$recordId		=	"";
		$lastId			=	"";
		$entryId		=	"";
		$reasonLocal 		= 	"";
		$reasonWastage 		= 	"";
		$reasonSoft		= 	"";
		$catchEntryNewId	=	"";
		$entryActualWt 		=	"";
		$entryEffectiveWt 	= 	"";
		$p["gradeCountAdj"]	=	"";
		$p["gradeCountAdjReason"]	=	"";
		$landingCenter		=	"";
		$mainSupplier		=	"";
		$subSupplier		=	"";
		$unit			=	"";
	}


	# Add Catch Entry Start 
	if ($p["cmdAddNew"]!="") {
		
		$addMode	=	true;
		//$fflag=false;	// Added by AMP on Jan 29 2007 to prevent id reuse accross multiple sessions. This will introduce the need to cleanup incomplete records.

		if (list($mainId,$entryId) = $dailycatchentryObj->checkBlankRecord($userId) && $p["entryId"]=="" && $p["catchEntryNewId"]=="")	{
			list($mainId,$entryId) = $dailycatchentryObj->checkBlankRecord($userId);
			$lastId			= $mainId;
			$catchEntryNewId	= $entryId;
		} else {
			if ($p["entryId"]=="" && $p["catchEntryNewId"]=="") {
				$tempdataRecIns=$dailycatchentryObj->addTempMaster($userId);
				if ($tempdataRecIns!="") {				
					$lastId	= $databaseConnect->getLastInsertedId();
				}
				
				$tempRecDailyCatchEntry	=	$dailycatchentryObj->addTempRecDailyCatchEntry($lastId);
				if ($tempRecDailyCatchEntry!="") {
					$catchEntryNewId	=	$databaseConnect->getLastInsertedId();
				}
			} else {
				$lastId			=	$p["entryId"];
				$catchEntryNewId	= 	$p["catchEntryNewId"];
			}
		}
		
		if (list($mainId,$entryId) = $dailycatchentryObj->checkBlankRecord($userId) && $p["entryId"]=="" && $p["catchEntryNewId"]=="") {
			#Delete unwanted entries from gross table	
			$dailyCatchEntryGrossRecDel = $dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
			#delete unwanted etries from Qulaity table
			$dailyCatchEntryQualityRecDel	=	$dailycatchentryObj->deleteDailyCatchEntryQualityWt($catchEntryNewId);

			#Delete Declared Record
			$declaredRecDel		=	$dailycatchentryObj->deleteCatchEntryDeclaredRec($catchEntryNewId);
		}
		
		$qualityValues = $dailycatchentryObj->fetchAllQualityMasterRecords();	
		// json_encode($qualityValues);
		if(sizeof($qualityValues)>0)
		{
		 $allQualityhide= json_encode($qualityValues);
		}
		$pondRecs		=	$dailycatchentryObj->getPondNmAll();
	}
	
	#save & Add New Challan
	if ($p["cmdAddNewChallan"]!="") {
		
		$rm_lot_id	=	$p["rm_lot_id"];
		$supplyDetails=	$p["supplyDetails"];
		$make_payment=	$p["make_payment"];
		$payment=	$p["payment"];
		$count_code=	$p["count_code"];
		$lotUnit			=	$p["lotUnit"];
		$LotVechicleNo		=	$p["LotVechicleNo"];
		 $lotIdAvailable		=	$p["lotIdAvailable"];
		//die;
		$unit			=	$p["unit"];
		$vechicleNo		=	$p["vechicleNo"];
		$supplierGroup		=	$p["supplierGroup"];
		$pondName		=	$p["pondName"];
		$landingCenter		=	$p["landingCenter"];
		$fish			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$supplyChallanNo	=	$p["supplyChallanNo"];
		$supplyLotChallanNo	=	$p["supplyLotChallanNo"];
		$weighChallanNo		=	$p["weighChallanNo"];
		$mainSupplier		=	$p["mainSupplier"];
		$subSupplier		=	($p["subSupplier"]=="")?0:$p["subSupplier"];
		
		$count			=	$p["count"];
		$countAverage		=	($p["countAverage"]=="")?0:$p["countAverage"];
		
		if ($countAverage) {
			$fetchGradeRec		=	$grademasterObj->fetchGradeRecords($countAverage);
			$averge_gradeId		=	$fetchGradeRec[0];
		}
		
		$entryLocal		=	$p["entryLocal"];
		$entryWastage		=	$p["entryWastage"];
		$entrySoft		=	$p["entrySoft"];
		$reasonAdjust		=	$p["reasonAdjust"];
		$entryAdjust		=	$p["entryAdjust"];
		$goodPack		=	$p["goodPack"];
		$peeling		=	$p["peeling"];
		$entryRemark		=	$p["entryRemark"];
		$entryActualWt		=	$p["entryActualWt"];
		$entryEffectiveWt	=	$p["entryEffectiveWt"];
		
		$entryTotalGrossWt	=	($p["entryTotalGrossWt"]=="")?0:$p["entryTotalGrossWt"];
		$entryTotalBasketWt	=	($p["entryTotalBasketWt"]=="")?0:$p["entryTotalBasketWt"];	
		$entryGrossNetWt	=	$p["entryGrossNetWt"];
		//--------------Removed on 29-10-07
		$ice				=	($p["ice"]=="")?0:$p["ice"];
		$declWeight			=	($p["declWeight"]=="")?0:$p["declWeight"];			
		$declCount			=	($p["declCount"]=="")?0:$p["declCount"];
		
		$Date1			=	explode("/",$p["selectDate"]);
		$selectDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
		
		$gradeId			=	($p["selGrade"]=="")?$averge_gradeId:$p["selGrade"];
		
		$receivedBy		=	"";
		if (($p["count"]!=""||$p["count"]!=0) && $p["selGrade"]=="") {
			$receivedBy	=	'C';
		} else if($p["selGrade"]!="" && ($p["count"]=="" || $p["count"]==0)) {
			$receivedBy	=	'G';
		} else {
			$receivedBy	=	'B';
		}
		
		$basketWt		=	($p["dailyBasketWt"]=="")?0:$p["dailyBasketWt"];
		
		 $entryId		=	$p["entryId"];
		//die();
		$reasonLocal 	= $p["reasonLocal"];
		$reasonWastage 	= $p["reasonWastage"];
		$reasonSoft	= $p["reasonSoft"];
		
		$entryOption	=	$p["entryOption"];
		if ($p["entryOption"]=='N') {
			$dailyCatchEntryGrossRecDel	=	$dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
			$basketWt = 0;
		}
		
		$catchEntryNewId	= $p["catchEntryNewId"];
		
		$selectTimeHour		=	$p["selectTimeHour"];
		$selectTimeMints	=	$p["selectTimeMints"];
		$timeOption 		= 	$p["timeOption"];
		$selectTime	= $p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];
		
		$paymentBy	=	($p["paymentBy"]=="")?E:$p["paymentBy"];
		
		$gradeCountAdj		=	($p["gradeCountAdj"]=="")?0:$p["gradeCountAdj"];
		$gradeCountAdjReason	=	$p["gradeCountAdjReason"];

		$billingCompany	= $p["billingCompany"];
		$alphaCode 	= $p["alphaCode"];
		$noBilling	= ($p["noBilling"]=="")?'N':$p["noBilling"];
		
		if ($entryId!="" && $weighChallanNo!=""  && $catchEntryNewId!="") {
					
		
			if($lotIdAvailable==1){
		
				$DailyCatchEntryRecIns=$dailycatchentryObj->updateCurrentDailyCatch($lotUnit, $landingCenter, $mainSupplier, $LotVechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyLotChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName);
			}
			else
			{
				$DailyCatchEntryRecIns = $dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$weighChallanNo,$selectDate, $selectTime, $entryId,  $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName);
			
				
			}
			
		//die();
		
		
		
		
		// if($lotIdAvailable==1){
    
			// $DailyCatchEntryRecIns=$dailycatchentryObj->updateCurrentDailyCatch($lotUnit, $landingCenter, $mainSupplier, $LotVechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyLotChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
		// }
		// else
		// {
			// $DailyCatchEntryRecIns=$dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
			
		// }
		if ($DailyCatchEntryRecIns) {
			
			$getWeightmentId=$dailycatchentryObj->getWeightmentmentId($rm_lot_id);
			$weightmentId=$getWeightmentId[0];
			//if ($entryTotalGrossWt!=0 || $entryTotalGrossWt!=""){
			$updateWeightment=$dailycatchentryObj->updateWeightmentPond($pondName,$payment,$weightmentId);
			//die;
			$DailyCatchEntryRecIns=$dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage,$reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling	);
			
			//$DailyCatchEntryRecIns=$dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage,$reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling);
			//}
		
		
			if ($DailyCatchEntryRecIns) {
				$addMode=true;
				$sessObj->createSession("displayMsg",$msg_succAddDailyCatch);
				
				$p["unit"]		= 	  "";
				$p["vechicleNo"]	= 	  "";
				$lotUnit			=	$p["lotUnit"];
				$LotVechicleNo		=	$p["LotVechicleNo"];
				$lotIdAvailable		=	$p["lotIdAvailable"];
				$p["supplierGroup"]	= 	  "";
				$p["pondName"]	= 	  "";
				$p["landingCenter"] 	= 	  "";
				$p["fish"]    		=     "";
				$p["processCode"]     	=     "";
				$p["supplyChallanNo"]	=	  "";
				$p["supplyLotChallanNo"]	=	  "";
				$p["weighChallanNo"]    =     "";
				$p["mainSupplier"]     	=     "";
				$p["subSupplier"]     	=     "";
				$p["ice"]     		=     "";
				$p["count"]     	=     "";
				$p["countAverage"]     	=     "";
				$p["entryLocal"]     	=     "";
				$p["entryWastage"]     	=     "";
				$p["entrySoft"]     	=     "";
				$p["reasonAdjust"]     	=     "";
				$p["entryAdjust"]     	=     "";
				$p["goodPack"]     	=     "";
				$p["peeling"]     	=     "";
				$p["entryRemark"]     	=     "";
				$p["entryActualWt"]     =     "";
				$p["entryEffectiveWt"]  =     "";
				$p["entryTotalGrossWt"] =     "";
				$p["entryTotalBasketWt"]=     "";	
				$p["entryGrossNetWt"]   =     "";
				$p["declWeight"]    	=     "";			
				$p["declCount"]     	=     "";
				$p["selectDate"]     	=     "";
				$p["entryId"]		=     ""; 
				$p["catchEntryNewId"]	=     "";
				$recordId		=	"";
				$lastId			=	"";
				$entryId		=	"";
				$reasonLocal 		= 	"";
				$reasonWastage 		= 	"";
				$reasonSoft		= 	"";
				$catchEntryNewId	=	"";
				$entryActualWt 		=	"";
				$entryEffectiveWt 	= 	"";
				$p["gradeCountAdj"]	=	"";
				$p["gradeCountAdjReason"]	=	"";
				$landingCenter			=	"";
				$mainSupplier			=	"";
				$subSupplier			=	"";
				$unit				=	"";				
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddDailyCatch;
			}
		}
		$DailyCatchEntryRecIns		=	false;
	} else {
		
		$err 	 = $msgFailAddRMRecord;
		
		if ($p['editMode']=="1") $editMode = true;
		else $addMode = true;
	}
		
}
	
	#For cancel button
	if ($p["cmdAddCancel"]!="") {
		$addMode=false;
		$entryId	=	$p["entryId"]; // this is the main id.
		$catchEntryNewId = 	$p["catchEntryNewId"];
		
		# Delete entry and related records
		$delLastInsertRec = $dailycatchentryObj->delLastInsertId($catchEntryNewId); // daily catch entry
		$dailyCatchEntryGrossRecDel = $dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
		$dailyCatchEntryQualityRecDel = $dailycatchentryObj->deleteDailyCatchEntryQualityWt($catchEntryNewId);

		if ( !$dailycatchentryObj->moreEntriesExist($entryId) )	{
			# Check if other entries exist for this main record. If no, then only delete the main.	
			$delDailyCatchEntryMainRec	=	$dailycatchentryObj->delEntryMainId($entryId);
		}
		$lastId ="";
		$catchEntryNewId	=	"";
	}

	# Add daily catch Entry start here (Update the Daily catch entry in the main table (t_dailycatchentry))
	if ($p["cmdAddDailyCatch"]!="") {
		// echo '<pre>';
		// print_r($p);
		// echo '</pre>';
		// die;
		//echo "hii";
		
		$rm_lot_id	=	$p["rm_lot_id"];
		$supplyDetails=	$p["supplyDetails"];
		$make_payment=	$p["make_payment"];
		$payment=	$p["payment"];
		$count_code=	$p["count_code"];
	 	$unit			=	$p["unit"];
		$vechicleNo		=	$p["vechicleNo"];
	
		$lotUnit		=	$p["lotUnit"];
	//die();
		$LotVechicleNo		=	$p["LotVechicleNo"];
		$lotIdAvailable		=	$p["lotIdAvailable"];
		$supplierGroup		=	$p["supplierGroup"];
		$pondName		=	$p["pondName"];
	
		$landingCenter		=	$p["landingCenter"];
		$fish			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$supplyChallanNo	=	$p["supplyChallanNo"];
		$supplyLotChallanNo	=	$p["supplyLotChallanNo"];
		$weighChallanNo		=	$p["weighChallanNo"];
		$weightId			=	$p["weightmentId"];
		//die();
		$mainSupplier		=	$p["mainSupplier"];
		
		$subSupplier		=	($p["subSupplier"]=="")?0:$p["subSupplier"];
		$count			=	$p["count"];
		$countAverage		=	($p["countAverage"]=="")?0:$p["countAverage"];
		
		if ($countAverage) {
			$fetchGradeRec		=	$grademasterObj->fetchGradeRecords($countAverage);
			$averge_gradeId		=	$fetchGradeRec[0];
		}
		
		$entryLocal			=	$p["entryLocal"];
		$entryWastage		=	$p["entryWastage"];
		$entrySoft			=	$p["entrySoft"];
		$reasonAdjust		=	$p["reasonAdjust"];
		$entryAdjust		=	$p["entryAdjust"];
		$goodPack			=	$p["goodPack"];
		$peeling			=	$p["peeling"];
		$entryRemark		=	$p["entryRemark"];
		$entryActualWt		=	$p["entryActualWt"];
		$entryEffectiveWt	=	$p["entryEffectiveWt"];
		$entryTotalGrossWt	=	($p["entryTotalGrossWt"]=="")?0:$p["entryTotalGrossWt"];
		$entryTotalBasketWt	=	($p["entryTotalBasketWt"]=="")?0:$p["entryTotalBasketWt"];	
		$entryGrossNetWt	=	$p["entryGrossNetWt"];
		
		/*--------------Removed on 29-10-07*/
		$ice			=	($p["ice"]=="")?0:$p["ice"];
		$declWeight		=	($p["declWeight"]=="")?0:$p["declWeight"];		
		$declCount		=	($p["declCount"]=="")?0:$p["declCount"];
		
		$Date1			=	explode("/",$p["selectDate"]);
		$selectDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
		
		
		$gradeId		=	($p["selGrade"]=="")?$averge_gradeId:$p["selGrade"];
		
		$receivedBy		=	"";
		if (($p["count"]!=""||$p["count"]!=0) && $p["selGrade"]=="") {
			$receivedBy	=	'C';
		} else if ($p["selGrade"]!="" && ($p["count"]=="" || $p["count"]==0)) {
			$receivedBy	=	'G';
		} else {
			$receivedBy	=	'B';
		}
		
		
		$basketWt		=	($p["dailyBasketWt"]=="")?0:$p["dailyBasketWt"];
		
		$entryId		=	$p["entryId"];
		$reasonLocal 	= $p["reasonLocal"];
		$reasonWastage 	= $p["reasonWastage"];
		$reasonSoft		= $p["reasonSoft"];
		
		$entryOption	=	$p["entryOption"];
		if ($p["entryOption"]=='N') {
			$dailyCatchEntryGrossRecDel =  $dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
			$basketWt = 0;
		}
		$catchEntryNewId	= $p["catchEntryNewId"];
		
		$selectTimeHour		=	$p["selectTimeHour"];
		$selectTimeMints	=	$p["selectTimeMints"];
		$timeOption 		= 	$p["timeOption"];
		$selectTime		=	$p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];
		
		$paymentBy		=	($p["paymentBy"]=="")?E:$p["paymentBy"];
		
		$gradeCountAdj		=	($p["gradeCountAdj"]=="")?0:$p["gradeCountAdj"];
		$gradeCountAdjReason	=	$p["gradeCountAdjReason"];

		if($p["billingCompany"]!="")
		{
		$billingCompany	= $p["billingCompany"];
		}
		else
		{
		$billingCompany	= $p["billingCompanyLot"];
		}
		$alphaCode 	= $p["alphaCode"];
		$noBilling	= ($p["noBilling"]=="")?'N':$p["noBilling"];

		if ($entryId!="" && $weighChallanNo!=""  ) {
  	
		
		
		if($lotIdAvailable==1){
    
			$DailyCatchEntryRecIns=$dailycatchentryObj->updateCurrentDailyCatch($lotUnit, $landingCenter, $mainSupplier, $LotVechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyLotChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName);
		}
		
		else
		{
			$DailyCatchEntryRecIns = $dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$weighChallanNo,$selectDate, $selectTime, $entryId,  $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName);
		
			
		}
		
		//die();
		
		
		//, 
		// if($lotIdAvailable==1){
    
			// $DailyCatchEntryRecIns=$dailycatchentryObj->updateCurrentDailyCatch($lotUnit, $landingCenter, $mainSupplier, $LotVechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyLotChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
		// }
		// else
		// {
			// echo	$entryId;
			// $DailyCatchEntryRecIns=$dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
			
		// }
		//die();
		
			if ($DailyCatchEntryRecIns) {
			
			$getWeightmentId=$dailycatchentryObj->getWeightmentmentId($rm_lot_id);
			$weightmentId=$getWeightmentId[0];
			//if ($entryTotalGrossWt!=0 || $entryTotalGrossWt!=""){
			$updateWeightment=$dailycatchentryObj->updateWeightmentPond($pondName,$payment,$weightmentId);
			//die;
				//if ($entryTotalGrossWt!=0 || $entryTotalGrossWt!=""){
				//echo $weightId;	
   	 			$DailyCatchEntryRecIns=$dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage,$reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling,$weightId);
				//die;
				//}
				if(isset($p['total_new_entry']) && $p['total_new_entry'] > 0)
				{
					$totalEntry = $p['total_new_entry'];
					for($i=0;$i<$totalEntry;$i++)
					{
						$status = $p['Status_'.$i];
						if ($status!='N') 
						{
						
							$qualityId    = $p['qualityId_'.$i];
							$nameOfquality  = $p['quality_new_'.$i];
							$quality_wt     = $p['qualityWeight_'.$i];
							$qualityPercent = $p['qualityPercent_'.$i];
							$reason       = $p['qualityReason_'.$i];
							$weightmentStatus       = $p['weightmentStatus_'.$i];
							$billing       = $p['billing_'.$i];
							
							$dailycatchentryObj->addEntryQuality($catchEntryNewId,$qualityId,$quality_wt,$qualityPercent,$reason,$weightmentStatus,$billing);
						
						}
					}

				}
				///die();
				if ($entryOption=='B') {
					for ($i=1; $i<=300; $i++) {
						$grossId	= trim($p["grossId_".$i]);
						$grossWt	= trim($p["grossWt_".$i]);
						$basketWt	= trim($p["grossBasketWt_".$i]);		
						
						if ( ($grossId==""||$grossId==0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt!=0)) {
							$dailyGrossRecIns = $dailycatchentryObj->addGrossWt($grossWt, $basketWt, $catchEntryNewId);	
						} else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!=""  && $grossWt!=0)) {
							$grossUpdateRec = $dailycatchentryObj->updateGrossWt($grossId, $grossWt, $basketWt, $catchEntryNewId);
						}
						else if (($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt==0)) {
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}
						else if (($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && $grossWt=="") {
							
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}
					}
				} // Count save ends here
				
				//die();
				
				
				if ($DailyCatchEntryRecIns) {
					$addMode=false;
					$sessObj->createSession("displayMsg",$msg_succAddDailyCatch);
					header("location:DailyCatchEntry_New.php?printId=$entryId&catchEntryNewId=$catchEntryNewId");
					#Clear All field
					clearAllFields();
				} else {
					$addMode	=	true;
					$err		=	$msg_failAddDailyCatch;
				}
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddDailyCatch;
			}
			
			$DailyCatchEntryRecIns		=	false;
		} else {
			$err = $msgFailAddRMRecord;
			$addMode	=	true;
		}
	}


	#Add different raw material in a single chellan no
	if ($p["cmdAddRaw"]!="" || $p["cmdAddRawSelChallan"]!=""  || $p["cmdAddSubSupplier"]!="") {		
		//$sessObj->createSession("selMode", 2);	
			$rmLotIdRepeat=true;
			
			if ($p["cmdAddRaw"]!="") {
			
			
		//if ($p["cmdAddRawSelChallan"]=="") {
		$rm_lot_id	=	$p["rm_lot_id"];
		$supplyDetails=	$p["supplyDetails"];
		$make_payment=	$p["make_payment"];
		$payment=	$p["payment"];
		$count_code=	$p["count_code"];
			$unit			=	$p["unit"];
			$vechicleNo		=	$p["vechicleNo"];
			$lotUnit		=	$p["lotUnit"];
			$LotVechicleNo		=	$p["LotVechicleNo"];
			$lotIdAvailable		=	$p["lotIdAvailable"];
			$supplierGroup		=	$p["supplierGroup"];
			$pondName		=	$p["pondName"];
			$landingCenter		=	$p["landingCenter"];
			$fish			=	$p["fish"];
			$processCode		=	$p["processCode"];
			$supplyChallanNo	=	$p["supplyChallanNo"];
			$supplyLotChallanNo	=	$p["supplyLotChallanNo"];
			$weighChallanNo		=	$p["weighChallanNo"];
			$mainSupplier		=	$p["mainSupplier"];
			$subSupplier		=	($p["subSupplier"]=="")?0:$p["subSupplier"];
		
			$count			=	$p["count"];
			$countAverage		=	($p["countAverage"]=="")?0:$p["countAverage"];
		
			if ($countAverage) {
				$fetchGradeRec		=	$grademasterObj->fetchGradeRecords($countAverage);
				$averge_gradeId		=	$fetchGradeRec[0];
			}
		
			$entryLocal		=	$p["entryLocal"];
			$entryWastage		=	$p["entryWastage"];
			$entrySoft		=	$p["entrySoft"];
			$reasonAdjust		=	$p["reasonAdjust"];
			$entryAdjust		=	$p["entryAdjust"];
			$goodPack		=	$p["goodPack"];
			$peeling		=	$p["peeling"];
			$entryRemark		=	$p["entryRemark"];
			$entryActualWt		=	$p["entryActualWt"];
			$entryEffectiveWt	=	$p["entryEffectiveWt"];

			$entryTotalGrossWt	=	($p["entryTotalGrossWt"]=="")?0:$p["entryTotalGrossWt"];
			$entryTotalBasketWt	=	($p["entryTotalBasketWt"]=="")?0:$p["entryTotalBasketWt"];
			$entryGrossNetWt	=	$p["entryGrossNetWt"];
			//--------------Removed on 29-10-07
			$ice			=	($p["ice"]=="")?0:$p["ice"];
			$declWeight		=	($p["declWeight"]=="")?0:$p["declWeight"];
			$declCount		=	($p["declCount"]=="")?0:$p["declCount"];
		
			$Date1			=	explode("/",$p["selectDate"]);
			$selectDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
		
			$gradeId		=	($p["selGrade"]=="")?$averge_gradeId:$p["selGrade"];
		
			$receivedBy		=	"";
			if (($p["count"]!=""||$p["count"]!=0) && $p["selGrade"]=="") {
				$receivedBy	=	'C';
			} else if ($p["selGrade"]!="" && ($p["count"]=="" || $p["count"]==0)) {
				$receivedBy	=	'G';
			} else {
				$receivedBy	=	'B';
			}
		
			$basketWt		=	($p["dailyBasketWt"]=="")?0:$p["dailyBasketWt"];
		
			if ($p['editMode']=="1")	$entryId		=	$p["enteredRMId"];
			else $entryId		=	$p["entryId"];

			$reasonLocal 		= 	$p["reasonLocal"];
			$reasonWastage 		= 	$p["reasonWastage"];
			$reasonSoft			= 	$p["reasonSoft"];
		
			$entryOption	=	$p["entryOption"];
			$catchEntryNewId	= $p["catchEntryNewId"];
		
			if ($p["entryOption"]=='N') {
				$dailyCatchEntryGrossRecDel =	$dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
				$basketWt = 0;
			}
		
			$selectTimeHour		=	$p["selectTimeHour"];
			$selectTimeMints	=	$p["selectTimeMints"];
			$timeOption 		= 	$p["timeOption"];
			$selectTime		= $p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];
		
			$paymentBy		=	($p["paymentBy"]=="")?E:$p["paymentBy"];
		
			$gradeCountAdj		=	($p["gradeCountAdj"]=="")?0:$p["gradeCountAdj"];
			$gradeCountAdjReason	=	$p["gradeCountAdjReason"];

			$billingCompany	= $p["billingCompany"];
			$alphaCode 	= $p["alphaCode"];
			$noBilling	= ($p["noBilling"]=="")?'N':$p["noBilling"];
				
			if ($entryId!="" ) {

				//$RawMaterialRecIns = $dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code);
			if($lotIdAvailable==1){
    
			$RawMaterialRecIns=$dailycatchentryObj->updateCurrentDailyCatch($lotUnit, $landingCenter, $mainSupplier, $LotVechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyLotChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
		}
		else
		{
			$RawMaterialRecIns=$dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
			
		}
			
			
				if ($RawMaterialRecIns) {

					//if ($entryTotalGrossWt!=0 && $entryTotalGrossWt!="")
					
					//{
		    			$RawMaterialRecIns=$dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage,$reasonSoft, $entryOption, $selectDate, $entryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling);
					//}
				
				if ($RawMaterialRecIns)	{				
					$addRaw		=	true;
					if ($p["cmdAddSubSupplier"]!="") $addSubSupplier = true;
				
					if ( $p['editMode'] == "1" )  $sessObj->createSession("displayMsg",$msg_succDailyCatchUpdate);
					else $sessObj->createSession("displayMsg",$msg_succAddDailyCatch);
								
				} else	{
					$err		=	$msg_failAddDailyCatch;
				}			
				$RawMaterialRecIns		=	false;
			} else	{
				$addMode	=	true;
				$err		=	$msg_failAddDailyCatch;
			}		
		} else {
			$err 	 = $msgFailAddRMRecord;
			if ($p['editMode']=="1") $editMode = true;
			else $addMode = true;
		}
		
		
		
	}
	
	if ($p["cmdAddRawSelChallan"]!="") {
		$lastId	=	$p["selWtChallan"];
	} else if($p['editMode'] == "1"){
		$lastId	=	$p["enteredRMId"];
	} else {
		$lastId	=	$p["entryId"];
	}
	

	$tempRecDailyCatchEntry	=	$dailycatchentryObj->addTempRecDailyCatchEntry($lastId);
	if ($tempRecDailyCatchEntry!="") {
		$catchEntryNewId	=	$databaseConnect->getLastInsertedId();
	}
	$addMode	=	true;
	$addRawMaterial	=	false;

	
	# Read all variables that need to be filled from main record.
	if ($p["cmdAddRawSelChallan"]!="" )	{		
		$catchEntryRec		=	$dailycatchentryObj->findDailyCatchMainRec($lastId);		
		$unit			= $catchEntryRec[1];
		$recordVechNo		= $catchEntryRec[3];
		$recordSupplierGroup		= $catchEntryRec[14];
		$recordPondName		= $catchEntryRec[15];		
		$recordWeighNo		= $catchEntryRec[4];
		$landingCenter		= $catchEntryRec[5];		
		$mainSupplier		= $catchEntryRec[6];		
		$recordSelectDate	= dateFormat($catchEntryRec[7]);
		$selectTime		= explode("-",$catchEntryRec[8]);
		$selectTimeHour		= $selectTime[0];
		$selectTimeMints	= $selectTime[1];
		$timeOption 		= $selectTime[2];
		$paymentBy		= $catchEntryRec[9];
		$checked		= "";
		$disableSubSupplier 	= "";
		$disableField		= "";
		if ($paymentBy=='D') {
			$checked="Checked";
			$disableSubSupplier = "disabled";	
			$disableField	    = "readonly";
		}
		$subSupplier		= $catchEntryRec[10];
		$recordChallanNo	= $catchEntryRec[11];	
		$billingCompany		= $catchEntryRec[12];
		$alphaCode		= $catchEntryRec[13];
		$supplierGroup		=	$catchEntryRec[14];
		$pondName		=	$catchEntryRec[15];
		$lotIdAvailable=	$catchEntryRec[16];
	}
}

	if ($addMode==true) {	
		if ($addRaw == true)  { 
			$reasonLocal 	= "";
			$reasonWastage 	= "";
			$reasonSoft	= "";
		} else {		
			$reasonLocal 	= $p["reasonLocal"];
			$reasonWastage 	= $p["reasonWastage"];
			$reasonSoft	= $p["reasonSoft"];
		}
	}

	# Edit a Daily catch entry 
	if (($p["editId"]!="" || $p["selRawMaterial"]!="" ) && $p["cmdCancel"]=="") {

		$editMode	=	true;		
		$editId		=	$p["editId"];
		
		if ($editId=="") $editId = $p["enteredRMId"];
						
		if ($p["selRawMaterial"]!="") {
			clearAllFields();
			$dailyCatchentryId	=	$p["selRawMaterial"];
		}
		else $dailyCatchentryId	=	$p["dailyCatchentryId"];
		//echo $dailyCatchentryId;			
		$catchEntryRec			=	$dailycatchentryObj->find($editId,$dailyCatchentryId);

		$recordId			=	$catchEntryRec[0];
		$recordUnit			=	$catchEntryRec[1];
		$recordLotUnit		=	$catchEntryRec[1];
		$recordDate			=	$catchEntryRec[2];

		$qualityRecords	= $dailycatchentryObj->fetchAllQualityRecords($dailyCatchentryId);
		// echo '<pre>';print_r($qualityRecords);echo '</pre>';
		if ($p["editSelectionChange"]=='1'|| $p["vechicleNo"]=="") {
			$recordVechNo		=	$catchEntryRec[3];
		} else {
			$recordVechNo		=	$p["vechicleNo"];
		}
		
		if ($p["editSelectionChange"]=='1'|| $p["LotVechicleNo"]=="") {
			$recordVechLotNo		=	$catchEntryRec[3];
		} else {
			$recordVechLotNo		=	$p["LotVechicleNo"];
		}
		
		if ($p["editSelectionChange"]=='1'|| $p["supplierGroup"]=="") {
			$recordSupplierGroup		=	$catchEntryRec[49];
		} else {
			$recordSupplierGroup		=	$p["supplierGroup"];
		}
		
		// if ($p["editSelectionChange"]=='1'|| $p["pondName"]=="") {
			// $recordPondName		=	$catchEntryRec[50];
		// } else {
			// $recordPondName		=	$p["pondName"];
		// }
		
		if ($p["editSelectionChange"]=='1'|| $p["supplyChallanNo"]=="") {
			$recordChallanNo	=	$catchEntryRec[4];
		} else {
			$recordChallanNo	=	$p["supplyChallanNo"];
		}
		
		if ($p["editSelectionChange"]=='1'|| $p["supplyLotChallanNo"]=="") {
			$recordChallanLotNo	=	$catchEntryRec[4];
		} else {
			$recordChallanLotNo	=	$p["supplyLotChallanNo"];
		}
		
		
		if ($p["editSelectionChange"]=='1'|| $p["weighChallanNo"]=="") {
			$recordWeighNo		=	$catchEntryRec[5];
		} else {
			$recordWeighNo		=	$p["weighChallanNo"];
		}
	
		
		if ($p["editSelectionChange"]=='1'|| $p["landingCenter"]=="") {
			$recordLanding		=	$catchEntryRec[6];
		} else {
			$recordLanding		=	$p["landingCenter"];
		}
		
		if ($p["editSelectionChange"]=='1'|| $p["mainSupplier"]=="") {
			$recordMainSupply		=	$catchEntryRec[7];
		} else {
			$recordMainSupply	=	$p["mainSupplier"];
		}
		
		$recordSubSupply	=	$catchEntryRec[8];
				
		if ($p["editSelectionChange"]=='1'|| $p["fish"]=="" || $p["selRawMaterial"]!="") {
			$recordFish		=	$catchEntryRec[9];
		} else {
			$recordFish		=	$p["fish"];
		}
		
		
		if ($p["editSelectionChange"]=='1'||$p["processCode"]=="" || $p["selRawMaterial"]!="") {
			$recordProcessCode	=	$catchEntryRec[10];
		} else {
			$recordProcessCode	=	$p["processCode"];
		}
		
		if ($p["ice"]=="") {
			$recordIceWt		=	$catchEntryRec[11];
		} else {
			$recordIceWt		=	$p["ice"];
		}
		
		if ($p["count"]=="") {
			$recordCount		=	$catchEntryRec[12];
		} else {
			$recordCount		=	$p["count"];
		}
		
		if ($p["countAverage"]=="" || $p["countAverage"]==0) {
			$recordAverage		=	$catchEntryRec[13];
		} else {
			$recordAverage		=	$p["countAverage"];
		}
		

		$recordLocalQty		=	$catchEntryRec[14];
		$recordWastage		=	$catchEntryRec[15];
		
		$recordSoft		=	$catchEntryRec[16];
		$recordReason		=	$catchEntryRec[17];
		$recordAdjust		=	$catchEntryRec[18];
		$recordGood		=	$catchEntryRec[19];
		$recordPeeling		=	$catchEntryRec[20];
		$recordRemarks		=	$catchEntryRec[21];		
		$entryActualWt		=	$catchEntryRec[22];
		$entryEffectiveWt	=	$catchEntryRec[23];
		
		if ($p["declWeight"]=="") {
			$recordDeclWeight	=	$catchEntryRec[27];
		} else {
			$recordDeclWeight	=	$p["declWeight"];
		}
		
		if ($p["declCount"]=="") {
			$recordDeclCount	=	$catchEntryRec[28];
		} else {
			$recordDeclCount	=	$p["declCount"];
		}
		
		if ($p["editSelectionChange"]=='1'|| $p["selectDate"]=="") {
			$recordSelectDate	= dateFormat($catchEntryRec[29]);
		} else {
			$recordSelectDate	= $p["selectDate"];
		}
				
		$recordGradeId		=	$catchEntryRec[30];
		$recordBasketWt		=	$catchEntryRec[31];
		$reasonLocal		=	$catchEntryRec[32];
		$reasonWastage		=	$catchEntryRec[33];
		$reasonSoft			=	$catchEntryRec[34];
		
		if ($p["editSelectionChange"]=='1'|| $p["entryOption"]=="" || $p["selRawMaterial"]!="") {
			$entryOption		=	$catchEntryRec[35];
		} else {
			$entryOption		=	$p["entryOption"];
		}
		
		$catchEntryNewId		=	$catchEntryRec[36];
		
		$netGrossWt			=	$catchEntryRec[26];
		
		if ($p["editSelectionChange"]=='1' || $p["selectTimeHour"]=="" || $p["selectTimeMints"]=="" ||$p["timeOption"]=="") {
			$selectTime			=	explode("-",$catchEntryRec[37]);
			$selectTimeHour			=	$selectTime[0];
			$selectTimeMints		=	$selectTime[1];
			$timeOption 			= 	$selectTime[2];
		} else {
			$selectTimeHour			=	$p["selectTimeHour"];
			$selectTimeMints		=	$p["selectTimeMints"];
			$timeOption 			= 	$p["timeOption"];
		}
		
				
		if ($p["editSelectionChange"]=='1' || $p["paymentBy"]=="") {
			$paymentBy		=	$catchEntryRec[38];
		} else {
			$paymentBy		=	$p["paymentBy"];
		}
		$disableSubSupplier = "";
		$disableField	    = "";
		if ($paymentBy=='D') {
			$checked="Checked";
			$disableSubSupplier = "disabled";
			$disableField	    = "readonly";
		}
		
		$gradeCountAdj		=	$catchEntryRec[39];
		$gradeCountAdjReason	=	$catchEntryRec[40];
		
		//$supplierRecords	=	$supplierMasterObj->fetchSupplierRecords($recordLanding);
		# Supplier Sort Starts Here
		$defaultSuppliers	= $supplierMasterObj->getCenterWiseActiveSuppliers($recordLanding);
		$selSuppliers		= $dailycatchentryObj->getDateWiseSupplier(mysqlDateFormat($recordSelectDate), $recordLanding);
		$supplierRecords	= multi_unique(array_merge($defaultSuppliers, $selSuppliers));
		# sort by name asc
		usort($supplierRecords, 'cmp_name');
		#Sort Ends here
				
		$subSupplierRecords	=	$subsupplierObj->filterSubSupplierRecords($recordMainSupply, $recordLanding);
		//$processCodeRecords	=	$processcodeObj->processCodeRecFilter($recordFish);
		$processCodeRecords	=	$dailycatchentryObj->pcRecFilter($recordFish);
		//$gradeMasterRecords	=	$processcodeObj->fetchGradeRecords($recordProcessCode);
		$gradeMasterRecords = $dailycatchentryObj->gradeRecFilter($recordProcessCode);
		
		
		$receivedBy	= $dailycatchentryObj->pcReceivedType($recordProcessCode);
		$billingCompany		= $catchEntryRec[41];
		$alphaCode		= $catchEntryRec[42];
		$noBilling		= $catchEntryRec[43];
		$rm_lot_id	= $catchEntryRec[44];
		$supplyDetails	= $catchEntryRec[45];		
		$make_payment			=	$catchEntryRec[46];
		$payment		=	$catchEntryRec[47];
		$count_code		=	$catchEntryRec[48];
		$pondName		=	$catchEntryRec[50];
		$lotIdAvailable		=	$catchEntryRec[51];
		$weightmentId		=	$catchEntryRec[52];
		$supplierRecs	=	$dailycatchentryObj->getSupplierNm($rm_lot_id);
		$pondRecs	=	$dailycatchentryObj->getPondNm($rm_lot_id);
		$fishRecs=$dailycatchentryObj->getFishNm($pondName,$rm_lot_id);
		// $supplierRecs	=	$dailycatchentryObj->getSupplier1($rm_lot_id);
		// $pondRecs	=	$dailycatchentryObj->getPond($rm_lot_id);
		// $fishRecs=$dailycatchentryObj->getFish($pondName,$rm_lot_id);
		$noBillingChk		= "";
		if ($noBilling=='Y') $noBillingChk = "checked";

		$dailyQualityEntry	=	$dailycatchentryObj->getQualityAdd($dailyCatchentryId);
		$qualityValues = $dailycatchentryObj->fetchAllQualityMasterRecords();

		// json_encode($qualityValues);
		if(sizeof($qualityValues)>0)
		{
		 $allQualityhide= json_encode($qualityValues);
		}
		
	}

	#Update a Record
	if ($p["cmdDailySaveChange"]!="") {
		// echo '<pre>';
		// print_r($p);
		// echo '</pre>';
		// die;
		$rm_lot_id		= $p["rm_lot_id"];
		$supplyDetails	= $p["supplyDetails"];		
		$make_payment			=	$p["make_payment"];
		$payment		=	$p["payment"];
		$count_code		=	$p["count_code"];
		
		$catchEntryId		= $p["entryId"];
		$catchEntryNewId	= $p["hidCatchEntryNewId"];		
		$unit			=	$p["unit"];
		$vechicleNo		=	$p["vechicleNo"];
		$lotUnit		=	$p["lotUnit"];
		$LotVechicleNo		=	$p["LotVechicleNo"];
		$lotIdAvailable		=	$p["lotIdAvailable"];
		$weightmentId=	$p["weightmentId"];
		$supplierGroup		=	$p["supplierGroup"];
		$pondName		=	$p["pondName"];
		$landingCenter		=	$p["landingCenter"];
		$fish			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$supplyChallanNo	=	$p["supplyChallanNo"];
		$supplyLotChallanNo	=	$p["supplyLotChallanNo"];
		$weighChallanNo		=	$p["weighChallanNo"];
		$mainSupplier		=	$p["mainSupplier"];
		$subSupplier		=	($p["subSupplier"]=="")?0:$p["subSupplier"];
		//$ice			=	$p["ice"];
		$count			=	$p["count"];
		$countAverage		=	($p["countAverage"]=="")?0:$p["countAverage"];
		if ($countAverage) {
			$fetchGradeRec		=	$grademasterObj->fetchGradeRecords($countAverage);
			$averge_gradeId		=	$fetchGradeRec[0];
		}
		
		$Date1			=	explode("/",$p["selectDate"]);
		$selectDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
		
		$entryLocal		=	$p["entryLocal"];
		$entryWastage		=	$p["entryWastage"];
		$entrySoft		=	$p["entrySoft"];
		$reasonAdjust		=	$p["reasonAdjust"];
		$entryAdjust		=	$p["entryAdjust"];
		$goodPack		=	$p["goodPack"];
		$peeling		=	$p["peeling"];
		$entryRemark		=	$p["entryRemark"];
		$entryActualWt		=	$p["entryActualWt"];
		$entryEffectiveWt	=	$p["entryEffectiveWt"];
		
		$entryTotalGrossWt	=	($p["entryTotalGrossWt"]=="")?0:$p["entryTotalGrossWt"];
		$entryTotalBasketWt	=	($p["entryTotalBasketWt"]=="")?0:$p["entryTotalBasketWt"];	
		$entryGrossNetWt	=	$p["entryGrossNetWt"];
		
		//--------------Removed on 29-10-07
		$ice			=	($p["ice"]=="")?0:$p["ice"];
		$declWeight		=	($p["declWeight"]=="")?0:$p["declWeight"];			
		$declCount		=	($p["declCount"]=="")?0:$p["declCount"];
		
		$gradeId		=	($p["selGrade"]=="")?$averge_gradeId:$p["selGrade"];
			
		$receivedBy		=	"";
		if (($p["count"]!=""||$p["count"]!=0) && $p["selGrade"]=="") {
			$receivedBy	=	'C';
		} else if ($p["selGrade"]!="" && ($p["count"]=="" || $p["count"]==0)) {
			$receivedBy	=	'G';
		} else {
			$receivedBy	=	'B';
		}
		
		$basketWt		=	($p["dailyBasketWt"]=="")?0:$p["dailyBasketWt"];
		
		$reasonLocal 	= $p["reasonLocal"];
		$reasonWastage 	= $p["reasonWastage"];
		$reasonSoft		= $p["reasonSoft"];
		
		$entryOption	=	$p["entryOption"];
		if ($p["entryOption"]=='N') {
			$dailyCatchEntryGrossRecDel = $dailycatchentryObj->deleteDailyCatchEntryGrossWt($catchEntryNewId);
			$basketWt = 0;
		}
		$selectTimeHour			=	$p["selectTimeHour"];
		$selectTimeMints		=	$p["selectTimeMints"];
		$timeOption 			= 	$p["timeOption"];
		$selectTime		=	$p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];
		
		$paymentBy		=	($p["paymentBy"]=="")?E:$p["paymentBy"];
		
		$gradeCountAdj		=	($p["gradeCountAdj"]=="")?0:$p["gradeCountAdj"];
		$gradeCountAdjReason	=	$p["gradeCountAdjReason"];
		
		$billingCompany	= $p["billingCompany"];
		$alphaCode 	= $p["alphaCode"];
		$noBilling	= ($p["noBilling"]=="")?'N':$p["noBilling"];
		
		
		if ($catchEntryId!=""  && $weighChallanNo!="") {
		
			//$DailyCatchEntryRecUptd=$dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $catchEntryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code);
			if($lotIdAvailable==1){
    
			$DailyCatchEntryRecUptd=$dailycatchentryObj->updateCurrentDailyCatch($lotUnit, $landingCenter, $mainSupplier, $LotVechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $catchEntryId, $paymentBy, $subSupplier, $supplyLotChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
		}
		else
		{
			$DailyCatchEntryRecUptd=$dailycatchentryObj->updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$supplierGroup,$pondName, $weighChallanNo, $selectDate, $selectTime, $catchEntryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable);
			
		}
			
			
			
			if ($DailyCatchEntryRecUptd) {
				//if ($entryTotalGrossWt!=0 || $entryTotalGrossWt!=""){
				$RawMaterialRecIns=$dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $selectDate, $catchEntryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling,$weightmentId);	
   		 		//$RawMaterialRecIns=$dailycatchentryObj->addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack,$peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $selectDate, $catchEntryId, $catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling);
				//}
				
				
				if(isset($p['total_new_entry']) && $p['total_new_entry'] > 0)
				{
					$totalEntry = $p['total_new_entry'];
					//die();
					for($i=0;$i<$totalEntry;$i++)
					{
						$status = $p["Status_".$i];
						$id           = $p['quality_entry_id_'.$i];
						$qualityId    = $p['qualityId_'.$i];
						$nameOfquality  = $p['quality_new_'.$i];
						$quality_wt     = $p['qualityWeight_'.$i];
						$qualityPercent = $p['qualityPercent_'.$i];
						$reason       = $p['qualityReason_'.$i];
						$weightmentStatus       = $p['weightmentStatus_'.$i];
						$billing       = $p['billing_'.$i];
						
						
						if ($status!='N') 
						{
							
							if($id!="")
							{
							$dailycatchentryObj->updateEntryQuality($id,$catchEntryNewId,$qualityId,$quality_wt,$qualityPercent,$reason,$weightmentStatus,$billing);
							}
							elseif($id=="")
							{
							$dailycatchentryObj->addEntryQuality($catchEntryNewId,$qualityId,$quality_wt,$qualityPercent,$reason,$weightmentStatus,$billing);
							}
							
							
							
						}
						
						if ($status=='N' && $id!="") {
						$dailyCatchEntryRecDel=	$dailycatchentryObj->deleteDailyCatchEntryQualityWtSingle($id);
						//delete
						}
					}
					//die();
				}	
				
				
				
			
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				if ($entryOption=='B') {
					
					for ($i=1; $i<=300; $i++) {
						$grossId	= trim($p["grossId_".$i]);
						$grossWt	= trim($p["grossWt_".$i]);
						
						$basketWt	= trim($p["grossBasketWt_".$i]);
							
						if (($grossId==""||$grossId==0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt!=0)) {
							$dailyGrossRecIns = $dailycatchentryObj->addGrossWt($grossWt, $basketWt, $catchEntryNewId);
						} else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt!=0)) {
							$grossUpdateRec = $dailycatchentryObj->updateGrossWt($grossId, $grossWt, $basketWt, $catchEntryNewId);
						}
						else if ( ($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt!="" && $grossWt==0)) {
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}
						else if (($grossId!="" || $grossId!=0) && $catchEntryNewId!="" && ($grossWt=="")) {
							
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}

					}

					# Delete Count Details
					if ( $p["delArr"] != "" ) {						
						$delArr = $p["delArr"];		
						$delCountArr = explode(",",$delArr); 				
						if (sizeof($delCountArr)>0) {
							for ($i=0;$i<sizeof($delCountArr);$i++) {
								$grossId	= $delCountArr[$i];
								if ($grossId!="") $grossRecDel = $dailycatchentryObj->deleteGrossEntryWt($grossId);	
							}
						}
					}				
				} // Count save ends here			
			}
			if ($DailyCatchEntryRecUptd) {
				$sessObj->createSession("displayMsg",$msg_succDailyCatchUpdate);
				$sessObj->createSession("nextPage", $url_afterUpdateDailyCatch.$dateSelection);
			} else {
				$editMode	=	true;
				$err		=	$msg_failDailyCatchUpdate;
			}
		} else {
			$err 	 = $msgFailAddRMRecord;
			if ($p['editMode']=="1") $editMode = true;
			else $addMode = true;
		}
		//$DailyCatchEntryRecUptd	=	false;
	}


	#Reset the Basket Wt
	if ($p["cmdReset"]!="") {	
		$basketWtChange 	= true;
		$resetBasketWt		= $p["dailyBasketWt"];
		$newWt			= $resetBasketWt;
		$catchEntryNewId	= $p["catchEntryNewId"];

		if ($resetBasketWt!="" && $catchEntryNewId!="") {
			$updateBasketWtRec = $dailycatchentryObj->updateBasketWt($resetBasketWt, $catchEntryNewId);
		}	
	}

	#Delete a Catch entry 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
		
			$catchMainId		=	$p["delId_".$i];

			$dailyCatchEntryId	=	$p["dailyCatchEntryId_".$i];


			if ($catchMainId!="") {
				#Check the selected record updated for paid or settled
				$isCatchEntryRecUpdated	=	$dailycatchentryObj->checkEntryUpdatedRecord($catchMainId);
				
				if (!$isCatchEntryRecUpdated) {
				//echo "hii";
					#$dailyCatchEntryQualityRecDel	
					$dailyCatchEntryRecDel=	$dailycatchentryObj->deleteDailyCatchEntryQualityWt($dailyCatchEntryId);
			
					#$dailyCatchEntryGrossRecDel		
					$dailyCatchEntryRecDel=	$dailycatchentryObj->deleteDailyCatchEntryGrossWt($dailyCatchEntryId);	
			
					#$delLastInsertRec			
					$dailyCatchEntryRecDel		=	$dailycatchentryObj->delLastInsertId($dailyCatchEntryId);
					
					#Check Record Exist
					$exisitingRecords = $dailycatchentryObj->checkRecordsExist($catchMainId);
					if (sizeof($exisitingRecords)==0) {
						$dailyCatchEntryRecDel = $dailycatchentryObj->delEntryMainId($catchMainId);	
					}
				}
			}
		}
	//die();
		if ($dailyCatchEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCatchEntry);		
			$sessObj->createSession("nextPage",$url_afterDelCatchEntry.$dateSelection);
		} else {
			$errDel	=	$msg_failDelCatchEntry;
		}
		$dailyCatchEntryRecDel	=	false;
	}	


	#List All Daily catch Entry 		
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
		
	$offset = ($pageNo - 1) * $limit; 

	## ----------------- Pagination Settings I End ------------	


	# select record between selected date
	if ($g["supplyFrom"]!="" && $g["supplyTill"]!="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else if ($p["supplyFrom"]!="" && $p["supplyTill"]!="") {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}
	
	//$fromDate	= mysqlDateFormat($dateFrom);
	//$tillDate	= mysqlDateFormat($dateTill);

	// $dailyCatch=	$dailycatchentryObj->getSuppierDetails(3);
	////print_r($dailyCatch);
	// foreach($dailyCatch as $daly)
	// {
		// echo $daly[11].$daly[6].$daly[22];
	// }
	
	if ($g["supplier"]!="") $selSupplierId = $g["supplier"];
	else $selSupplierId = $p["supplier"];

	if ($g["selFish"]!="") $selFish = $g["selFish"];
	else $selFish       = $p["selFish"];

	if ($g["selProcesscode"]!="") $selProcesscode = $g["selProcesscode"];
	else $selProcesscode = $p["selProcesscode"];

	if ($g["selRecord"]!="") $selRecord = $g["selRecord"];
	else $selRecord = $p["selRecord"];

	if ($g["billingCompanyFilter"]!="") $fBillingCompany = $g["billingCompanyFilter"];
	else $fBillingCompany = $p["billingCompanyFilter"];
	
	if ($g["filterType"]!="") $filterType = $g["filterType"];
	else $filterType = $p["filterType"];		

	# List all records
	
	//if ((!$addMode && !$editMode) && ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!=""))) {	
	if (!$addMode && !$editMode) {		
		
		if ($p["cmdSearch"]) {
			$offset = 0;
			$page 	= 0;
		}	

		$fromDate	= mysqlDateFormat($dateFrom);
		$tillDate	= mysqlDateFormat($dateTill);
		
		# Get Pagining Records //$catchEntryRecords
		$catchEntryResultSetObj = $dailycatchentryObj->filterCatchEntryPagingRecords($fromDate, $tillDate, $selRecord, $offset, $limit, $selSupplierId, $selFish, $selProcesscode, $fBillingCompany);
		$catchEntryRecSize	  = $catchEntryResultSetObj->getNumRows();

		# Get Date Range Records (With out Pagination) Edited 12-06-08 $fetchDateRangeRecords
		$fetchDateRangeResultSetObj = $dailycatchentryObj->filterDateRangeCatchEntryRecords($fromDate, $tillDate, $selRecord, $selSupplierId, $selFish, $selProcesscode, $fBillingCompany);
		$fetchDateRangeRecordSize = $fetchDateRangeResultSetObj->getNumRows();
		
		# Get Distinct Catch Entry Records
		$distinctCatchEntryRecords = $dailycatchentryObj->filterDistinctDateRangeCatchEntryRecords($fromDate, $tillDate, $selRecord, $selSupplierId, $fBillingCompany);
		
		# Get Supplier Filter Recs
		if ($filterType=='SW') $selBillingCompany = "";
	   	else $selBillingCompany = $fBillingCompany;
	   	$supplierFilterRecs = $dailycatchentryObj->getSupplierList($dateFrom, $dateTill, $selBillingCompany);
		
		# Fish Filter Recs
		$fishFilterRecs = $dailycatchentryObj->getFishList($dateFrom, $dateTill, $selSupplierId, $fBillingCompany);
		# PC Filter Recs
		if ($selFish) $pcFilterRecs = $dailycatchentryObj->getProcessCodeList($dateFrom, $dateTill, $selSupplierId, $selFish, $fBillingCompany);

		# Billing company Filter Recs
		if ($filterType=='SW') $selSupplier = $selSupplierId;
	   	else $selSupplier = "";
		$billingCmpnyFilterRecs = $dailycatchentryObj->getBillingCompanyList($fromDate, $tillDate, $selSupplier);

		if ($p["cmdSearch"]) $p["selWtChallan"]	= "";
		$displayListEnabled = true;
	}
	## -------------- Pagination Settings II -------------------
	$numrows	=	$fetchDateRangeRecordSize;
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	#Find GrandTotal
	if ($fetchDateRangeRecordSize>0) {
		$catchEntryTotalEffectiveQty = "";
		$effectiveQty		=	"";
		$ceGradeCountWt = "";
		$ceTotalGradeCountAdjWt	= 0;	
		while ($cer=$fetchDateRangeResultSetObj->getRow()) {
			$effectiveQty	=		$cer[8];
			$catchEntryTotalEffectiveQty	+= $effectiveQty;
			$ceGradeCountWt		= 	$cer[11];
			$ceTotalGradeCountAdjWt	+= $ceGradeCountWt;
		}
	}

	#End Here

	#For selecting add Raw material 
	if ($p["editChellan"]!="") $weighChallanNo		= $p["editChellan"];
	else $weighChallanNo		= $p["weighChallanNo"];
	
	
	#Edit mode set the Daily catch entry ID
	if ($p["editId"]!="" || $p["selRawMaterial"]!="") {
		$entryId	=	$recordId;
		$lastId		=	$recordId;
	}

	if ($recordWeighNo) $weighChallanNo = $recordWeighNo;
	#Other RM in Same Challan	
	if ($lastId) $listRawRecords = $dailycatchentryObj->fetchAllRawMaterialDailyRecords($lastId);
	
	#count all Gross Records
	if ($catchEntryNewId) {
		$countGrossRecords = $dailycatchentryObj->fetchAllGrossRecords($catchEntryNewId);	
		if (sizeof($countGrossRecords)>0) {		
			foreach ($countGrossRecords as $cgr) {
				$cEntryId 		= 	$cgr[0];
				$countGrossWt		=	$cgr[1];
				$totalWt		+=	$countGrossWt;
				$countGrossBasketWt	=	$cgr[2];
				$grandTotalBasketWt	+=	$countGrossBasketWt;
				$netGrossWt		=	$totalWt - $grandTotalBasketWt;			
			} // Loop Ends here		
			//echo $gWtVal;
		}	
	} // Id check
	
	#Filter Process Code
	if ($addRaw==true)  $processId = "";
	else $processId	=	$p["processCode"];

	/*	
	if ($editMode==true && $recordBasketWt==0) {
		//$recordBasketWt
		if ($recordProcessCode) $processCodeIdOnchange	= $processcodeObj->processCodeRecIdFilter($recordProcessCode);
	} else if ($addMode==true) {
		if ($processId) $processCodeIdOnchange	= $processcodeObj->processCodeRecIdFilter($processId);		
	}
	if (sizeof($processCodeIdOnchange)>0) {
		$processBasketWt	=	$processCodeIdOnchange[0][4];
	} else if ($editMode && $recordBasketWt!=0) $processBasketWt =  $recordBasketWt;
	*/	
	if ($editMode) $processBasketWt =  $recordBasketWt;
	

	if ($addMode || $editMode) {

		#List All Plants
		//$plantRecords	= $plantandunitObj->fetchAllRecords();
	
		#List all Landing Centers
		//$landingCenterRecords	= $landingcenterObj->fetchAllRecords();
	
		#List All Fishes
		//$fishMasterRecords	= $fishmasterObj->fetchAllRecords();

		# Get Billing Comapany  Records
		//$billingCompanyRecords = $billingCompanyObj->fetchAllRecords();


		$plantRecords	= $plantandunitObj->fetchAllRecordsPlantsActive();
	
		#List all Landing Centers
		$landingCenterRecords	= $landingcenterObj->fetchAllRecordsActiveLanding();
	
		#List All Fishes
		$fishMasterRecords	= $fishmasterObj->fetchAllRecordsFishactive();

		# Get Billing Comapany  Records
		//$billingCompanyRecords = $billingCompanyObj->fetchAllRecordsActivebillingCompany();
		$billingCompanyRecords = $dailycatchentryObj->fetchAllRecordsActivebillingCompany();
		
		$allBillingCompanyRecords = $dailycatchentryObj->fetchAllActiveRecordsbillingCompany();

		$supplierRecs	= $supplierMasterObj->fetchAllRMSupplierActive();
	}


	if ($p["cmdReset"]!="") {
		$processBasketWt = $p["dailyBasketWt"];
	}	
		
	
	if ($editMode) { 
		$mode	 = 2;
		$heading = $label_editDailyCatchEntry;
	} else {
		$mode	 = 1;
		$heading = $label_addDailyCatchEntry;
	}
	
	
	$help_lnk="help/hlp_DailyCatchEntry.html";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS	= "libjs/dailycatchentry.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	if ($g["printId"]!="") {

		$printId		=	$g["printId"];
		$printCatchEntryId	=	$g["catchEntryNewId"];
?>
	
	<script language="javascript">
	printWindow('PrintDailyCatchEntry_New.php?printId=<?=$printId?>&catchEntryNewId=<?=$printCatchEntryId?>',700,600);
	</script>
<? } ?>
<?php
	if ($addMode || $editMode) {
		if($addMode==true){			
			$billingCompany	= ($p["billingCompany"]!="")?$p["billingCompany"]:$billingCompany;
			$recordWeighNo	= ($p["weighChallanNo"]!="")?$p["weighChallanNo"]:$recordWeighNo;
			if ($p["selectDate"]!="") $recordSelectDate = $p["selectDate"];
			if ($recordSelectDate=="") $recordSelectDate = date("d/m/Y");
		}
?>
	<script language="JavaScript" type="text/javascript">
		xajax_getBillingCompanyRec('<?=$billingCompany?>');
		xajax_chkValidCNum('<?=$billingCompany?>', '<?=$recordWeighNo?>', '<?=$recordSelectDate?>', '<?=$lastId?>', '<?=$mode?>');
		xajax_chkValidDate('<?=$recordSelectDate?>', '<?=$billingCompany?>', '<?=$mode?>');
	</script>
<?php
	} // ajax_load ends here
?>

<form name="frmDailyCatch" id="frmDailyCatch" action="DailyCatchEntry_New.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="500px" border="0">
	<? if($err!="" ){?> 
		<tr> 
     		<td height="10" align="center" class="err1" ><?=$err;?></td>
   		</tr>
	<? }?>
    <?
	if ($editMode || $addMode) 
	{
	?>
		<tr> 
			<td align="left" style="padding-left:10px; padding-right:10px;"> 
				<table cellpadding="0"  cellspacing="0" border="0" align="left"  width="50%"  bgcolor="#D3D3D3">
					<tr> 
						<td bgcolor="white" nowrap>
						<?php
							$bxHeader=$heading;
							include "template/boxTL.php";
						?>
							<table width="50%" align="left">
								<tr> 
									<td width="1" ></td>
									<td colspan="2" >
									<!-- Entry section starts here -->
										<table cellpadding="0"  width="60%" cellspacing="0" border="0" align="left">
											<tr> 
												<td width="18%" height="10" ></td>
											</tr>
											<tr>
												<td colspan="4" nowrap align="left" style="padding-left:10px; padding-right:10px;">
													<table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
														<tr>
															<TD align="left" nowrap>
															<? if($editMode){?>
																<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('DailyCatchEntry_New.php');" />
																&nbsp;&nbsp;
																<input type="submit" name="cmdDailySaveChange" id="cmdDailySaveChange" class="button" value=" Save Changes " onclick="return validateAddDailyCatchEntry(document.frmDailyCatch, null, null);" />
															<? if($add==true){?>
																&nbsp;&nbsp;
															<input type="button" name="cmdAddRaw" id="cmdAddRaw" class="button" value="Save & Add New Raw Material in Challan" onclick="return validateAddDailyCatchEntry(document.frmDailyCatch, '<?=$mode?>', 'RM');" tabindex="32" style="width:250px;">			
															<? }?>
															<?} else{?>
															<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onclick="return cancel('DailyCatchEntry_New.php');" />
															&nbsp;&nbsp;
															<input type="submit" name="cmdAddDailyCatch" id="cmdAddDailyCatch" class="button" value=" Save & Exit " onclick="return validateAddDailyCatchEntry(document.frmDailyCatch,null,null);" />
															&nbsp;&nbsp;
															<input type="button" name="cmdAddNewChallan" id="cmdAddNewChallan" value="save & Add New Challan" class="button" onclick="return recordSaved(document.frmDailyCatch, '<?=$mode?>', 'NC');" style="width:150px;" />
															<? if($add==true){?>
															&nbsp;&nbsp;
															<input type="button" name="cmdAddRaw" id="cmdAddRaw" class="button" value="Save & Add New Raw Material in Challan" onclick="return validateAddDailyCatchEntry(document.frmDailyCatch, '<?=$mode?>', 'RM');" tabindex="32" style="width:250px;">				
															<? }?>
															<?}?>
															<span class="fieldName1">Other RM in Same Challan</span>
															<?php
																$RawMarterialId	=	$p["selRawMaterial"];
															?>
																<select name="selRawMaterial" id="selRawMaterial" onchange="this.form.submit();">
																	<option value="">-- Select --</option>
																	<?php
																		foreach ($listRawRecords as $lrm) {
																			$catchMainId		=	$lrm[0];
																			$catchEntryChallanNo 	=	$lrm[2];
																			$catchEntryProcessCode	=	$lrm[9];
																			$rmReceivedBy		=	$lrm[11];
																			$cEntryCount		=	$lrm[12];
																			$gCode = "";
																			$disGradeOrCount = $cEntryCount;
																			if ($cEntryCount==""|| $cEntryCount==0 || $rmReceivedBy=='B' ) {
																				$gCode	= $grademasterObj->findGradeCode($lrm[13]);
																				$disGradeOrCount = $gCode;
																			}
																			$displayList = $catchEntryChallanNo."-".$catchEntryProcessCode."(".$disGradeOrCount.")";
																			$listedDailyCatchEntryId	= $lrm[10];
																			$selected	=	"";
																			if ($listedDailyCatchEntryId == $RawMarterialId) {
																				$selected	=	"selected";
																			}
																	?>
																	<option value="<?=$listedDailyCatchEntryId?>" <?=$selected?>><?=$displayList?></option>
																	<?php }?>
																</select>
															</TD>
														</tr>		
													</table>
													<input type="hidden" name="hidDailyCatchId" value="<?=$recordId;?>" title="hide Main Id not need" />
													<input type="hidden" name="hidCatchEntryNewId" id="hidCatchEntryNewId" value="<?=$catchEntryNewId?>" title="hide Entry Id not need">
												</td>
											</tr>    
											<tr>
												<TD colspan="4" id="challanErrMsg" class="err1" align="center"></TD>
											</tr>
											<tr>
												<td colspan="4" nowrap align="left">
													<table width="70%" border="0" cellpadding="4" cellspacing="0" align="left">
														<tr>
															<td width='30%' valign="top">
															<?php
																$left_l=true;
																$entryHead = "Supplier Challan Details";
																$rbTopWidth = "";
																require("template/rbTop.php");
															?>
																<table align="center" cellpadding="0" cellspacing="0" width="100%">
																	<tr>
																		<td class="fieldName" nowrap>If RM Lot Id available</td>																									
																		<TD  colspan="3" >																		
																			<input type="checkbox" name="lotIdAvailable" id="lotIdAvailable" value="1" onclick="lotIdAvlCheck(this.checked);"  />			
																		</TD>
																	</tr>	
																	<tr id="autoUpdate" class="autoUpdate">	
																		<td>
																			<table align="center" cellpadding="0" cellspacing="0">
																				<tr>
																					<td align="left"  class="fieldName1" nowrap>Unit</td>
																					<td align="left" nowrap>
																					<? 
																					if($addMode==true){ 
																					if($p["unit"]!="") $unit	=	$p["unit"]; 
																					}
																					?>
																						<select name="unit" id="unit" tabindex="1" onkeypress="return focusNextBox(event,'document.frmDailyCatch','landingCenter');" onchange="xajax_getChallanDetails(document.getElementById('unit').value,document.getElementById('billingCompany').value); xajax_chkValidCNum(document.getElementById('billingCompany').value, document.getElementById('weighChallanNo').value, document.getElementById('selectDate').value, document.getElementById('entryId').value,document.getElementById('unit').value, '<?=$mode?>');">
																							<option value="">--Select--</option>
																							<?php 
																							foreach($plantRecords as $pr) {
																								$plantId		=	$pr[0];
																								$plantName		=	stripSlash($pr[2]);
																								$selected="";
																								if ($plantId == $recordUnit || $plantId== $unit) {
																								$selected = "selected";
																							}
																							?>
																							<option value="<?=$plantId?>" <?=$selected?>> <?=$plantName?> </option>
																							<? }?>
																						</select>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName1" nowrap>Landing Center</td>
																					<td nowrap>
																						<?php 
																						if ($addMode==true) { 
																						if ($p["landingCenter"]!="") $landingCenter =	$p["landingCenter"];
																						if ($landingCenter) {
																						$supplierRecords = $supplierMasterObj->getCenterWiseActiveSuppliers($landingCenter);
																						}
																						}
																						?>
																						<select name="landingCenter" id="landingCenter" tabindex="2" onkeypress="return focusNextBox(event,'document.frmDailyCatch','mainSupplier');" onchange="xajax_filterSupplier(document.getElementById('landingCenter').value, '');">
																							<option value="">--Select--</option>
																							<?php
																							foreach ($landingCenterRecords as $fr)	{
																								$centerId	=	$fr[0];
																								$centerName	=	stripSlash($fr[1]);
																								$selected="";
																								if ($centerId==$recordLanding || $centerId==$landingCenter) {
																								$selected	=	"selected";
																								}
																							?>
																							<option value="<?=$centerId?>" <?=$selected?>> <?=$centerName?> </option>
																							<? } ?>
																						</select>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName1" nowrap>Main Supplier</td>
																					<td nowrap>
																						<?php 
																						if ($addMode==true) { 
																						if ($p["mainSupplier"]!="") $mainSupplier = $p["mainSupplier"];
																						if ($mainSupplier) {
																							$subSupplierRecords = $subsupplierObj->filterSubSupplierRecords($mainSupplier, $landingCenter);
																							# Get Payment By
																							$paymentBy = $supplierMasterObj->getSupplierPaymentBy($mainSupplier);
																							$checked 	= "";
																							$disableSubSupplier = "";
																							$disableField	    = "";	
																							if ($paymentBy=='D') {
																								$checked = "Checked";
																								$disableSubSupplier = "disabled";
																								$disableField	    = "readonly";
																								}
																							}
																						}
																						?>
																						<select name="mainSupplier" id="mainSupplier" tabindex="3" onkeypress="return focusNextBox(event,'document.frmDailyCatch','subSupplier');" onchange="xajax_filterSubSupplier(document.getElementById('mainSupplier').value, document.getElementById('landingCenter').value, '');" style="width:200px;">
																							<option value="">--Select--</option>
																							<?php
																							foreach ($supplierRecords as $fr) {								
																								$supplierId	= $fr[0];
																								$supplierName	= stripSlash($fr[1]);	
																								$selected	=	"";
																								if ($supplierId == $recordMainSupply ||$supplierId==$mainSupplier) {
																												$selected	=	"selected";
																							}
																							?>
																							<option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
																							<? } ?>
																						</select>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName1" nowrap>Sub Supplier</td>
																					<td nowrap>
																					<? 
																						if ($addMode==true) { 
																							if ($addSubSupplier!="") $subSupplier = "";
																							else if ($p["subSupplier"]!="") $subSupplier	=	$p["subSupplier"];
																							} 
																					?>
																						<select name="subSupplier" id="subSupplier" tabindex="4" onkeypress="return focusNextBox(event,'document.frmDailyCatch','vechicleNo');" <?=$disableSubSupplier?>>
																							<option value="">SELF</option>
																							<?php
																							foreach ($subSupplierRecords as $fr) {
																								$subSupplierId	= $fr[0];
																								$subSupplierName = stripSlash($fr[1]);
																								$selected	= "";
																								if ($subSupplierId == $recordSubSupply || $subSupplierId==$subSupplier) {
																									$selected = "selected";
																								}
																							?>
																							<option value="<?=$subSupplierId?>" <?=$selected?>> <?=$subSupplierName?> </option>
																							<? }?>
																						</select>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName1" nowrap>Vehicle No</td>
																					<td nowrap>
																						<? 
																						if($addMode==true) {
																							if ($p["vechicleNo"]!="") $recordVechNo	= $p["vechicleNo"];
																								}
																						?>
																						<input type="text" name="vechicleNo" id="vechicleNo" size="20" value="<?=$recordVechNo;?>" tabindex="5" onkeypress="return focusNextBox(event,'document.frmDailyCatch','supplyChallanNo');" autocomplete="off" />
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName1" nowrap>Suppliers Challan No</td>
																					<td nowrap>
																					<? if($addMode==true){
																						if ($addSubSupplier!="") $recordChallanNo = "";
																						else if ($p["supplyChallanNo"]!="") $recordChallanNo	=	$p["supplyChallanNo"];
																						}
																					?>
																						<input name="supplyChallanNo" type="text" id="supplyChallanNo" size="6" value="<?=$recordChallanNo?>" tabindex="6" onkeypress="return focusNextBox(event,'document.frmDailyCatch','weighChallanNo');" autocomplete="off" <?=$disableField?>/>
																					</td>
																				</tr>
																				<tr>
																					<TD class="fieldName1" nowrap>*Billing Company</TD>
																					<td nowrap>
																						<?php
																							if($addMode==true){
																							if ($addSubSupplier!="") $billingCompany = "";
																							else if ($p["billingCompany"]!="") $billingCompany	=	$p["billingCompany"];
																							}
																						?>
																						<select name="billingCompany" id="billingCompany" onchange="xajax_getBillingCompanyRec(document.getElementById('billingCompany').value); xajax_chkValidCNum(document.getElementById('billingCompany').value, document.getElementById('weighChallanNo').value, document.getElementById('selectDate').value, document.getElementById('entryId').value,document.getElementById('unit').value, '<?=$mode?>'); xajax_chkValidDate(document.getElementById('selectDate').value, document.getElementById('billingCompany').value, '<?=$mode?>');"  onchange="xajax_getChallanDetails(document.getElementById('unit').value,document.getElementById('billingCompany').value); ">
																							<option value="">--Select--</option>
																							<?php
																								foreach ($billingCompanyRecords as $bcr) {
																									$billingCompanyId	= $bcr[0];
																									$defaultChk		= $bcr[10];
																									$displayCName		= $bcr[9];
																									$selected = "";
																									if ($billingCompanyId==$billingCompany || ($billingCompany=="" && $defaultChk=='Y') ) $selected = "selected";
																							?>
																							<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
																							<?	
																							}	
																							?>
																							</select>
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>	
																		<tr id="autoUpdate2" class="autoUpdate2" style="display:none">
																			<td>
																				<table align="center" cellpadding="0" cellspacing="0">
																				<input type="hidden" name="notInWeightment" id='notInWeightment' value=''/>
																					<tr id="rmlotIdRow">
																						<td nowrap="" class="fieldName1"> RM LOT ID </td>
																						<td nowrap="">
																							<select id="rm_lot_id" name="rm_lot_id" >
																								<option value=""> -- Select Lot ID --</option>
																								<?php
																								if(sizeof($rmLotIds) > 0)
																								{
																									foreach($rmLotIds as $lotID)
																									{	
																										$sel = '';
																										if($rm_lot_id == $lotID[0]) $sel = 'selected="selected"';
																																										
																										echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																									}
																								}
																								?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td align="left"  class="fieldName1" nowrap>Unit</td>
																						<td align="left" nowrap>
																						<? 
																						if($addMode==true){ 
																							if($p["lotUnit"]!="") $lotUnit	=	$p["lotUnit"]; 
																						}
																						?>
																							<select name="lotUnit" id="lotUnit" tabindex="1" onkeypress="return focusNextBox(event,'document.frmDailyCatch','landingCenter');"  disabled="disabled">
																								<option value="">--Select--</option>
																								<?php 
																								foreach($plantRecords as $pr) {
																									$plantId		=	$pr[0];
																									$plantName		=	stripSlash($pr[2]);
																									$selected="";
																									if ($plantId == $recordLotUnit || $plantId== $lotUnit) {
																									$selected = "selected";
																									}
																								?>
																								<option value="<?=$plantId?>" <?=$selected?>> <?=$plantName?> </option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName1" nowrap>Suppliers Challan No</td>
																						<td nowrap>
																							<? if($addMode==true){
																								if ($addSubSupplier!="") $recordChallanNo = "";
																								else if ($p["supplyLotChallanNo"]!="") $recordChallanLotNo	=	$p["supplyLotChallanNo"];
																								}
																							?>
																							<input name="supplyLotChallanNo" type="text" id="supplyLotChallanNo" size="6" value="<?=$recordChallanLotNo?>" tabindex="6" onkeypress="return focusNextBox(event,'document.frmDailyCatch','weighChallanNo');" autocomplete="off" <?=$disableField?>/>
																						</td>
																					</tr>
																					<tr>
																						<TD class="fieldName1" nowrap>*Billing Company</TD>
																						<td nowrap>
																							<?php
																								if($addMode==true){
																								if ($addSubSupplier!="") $billingCompany = "";
																									else if ($p["billingCompany"]!="") $billingCompany	=	$p["billingCompany"];
																								}
																							?>
																							<select name="billingCompanyLot" id="billingCompanyLot" onchange="xajax_getBillingCompanyRec(document.getElementById('billingCompanyLot').value); xajax_chkValidCNum(document.getElementById('billingCompanyLot').value, document.getElementById('weighChallanNo').value, document.getElementById('selectDate').value, document.getElementById('entryId').value, '<?=$mode?>'); xajax_chkValidDate(document.getElementById('selectDate').value, document.getElementById('billingCompanyLot').value, '<?=$mode?>');" disabled="disabled">
																								<option value="">--Select--</option>
																								<?php
																								foreach ($allBillingCompanyRecords as $bcr) {
																									$billingCompanyId	= $bcr[0];
																									$defaultChk		= $bcr[10];
																									$displayCName		= $bcr[9];
																									$selected = "";
																									if ($billingCompanyId==$billingCompany || ($billingCompany=="" && $defaultChk=='Y') ) $selected = "selected";
																								?>
																								<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
																								<?	
																									}	
																								?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td nowrap="" class="fieldName1">Use Supplier challan to Make Payment</td>
																						<td nowrap="" class="listing-item">
																							<? 
																							if($make_payment==1)
																							{
																							?>
																								<input type="checkbox" value="1" id="make_payment" name="make_payment" checked=checked/>
																							<?
																							}
																							else
																							{
																							?>
																								<input type="checkbox" value="1" id="make_payment" name="make_payment" />
																							<?
																							}
																							?>
																						</td>
																					</tr>
																					<tr>
																						<td nowrap="" class="fieldName1">Payment to be made to Supplier Name</td>
																						<td nowrap>
																							<select name="payment" id="payment" onchange="xajax_pondNames(document.getElementById('payment').value,document.getElementById('rm_lot_id').value,'');"  disabled="disabled">
																								<option value="">-- Select --</option>
																								<?php 
																									foreach($supplierRecs as $sr)
																									{
																										$supplierNameId		=	$sr[0];
																										$supplierNameVal	=	stripSlash($sr[1]);
																										$selected="";
																										if($payment==$supplierNameId) echo $selected="Selected";
																								?>
																								<option value="<?=$supplierNameId?>" <?=$selected?>><?=$supplierNameVal?></option>
																								<? }
																								?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td nowrap="" class="fieldName1">Farm Name</td>
																						<td nowrap>
																							<select name="pondName" id="pondName" onchange="xajax_getCountCode(document.getElementById('pondName').value,document.getElementById('rm_lot_id').value,document.getElementById('payment').value,'');" disabled="disabled">
																								<option value="">-- Select --</option>
																								<?php 
																									foreach($pondRecs as $sr)
																									{
																										$pondNameId		=	$sr[0];
																										$pondNameVal	=	stripSlash($sr[1]);
																										$selected="";
																										if($pondName==$pondNameId) echo $selected="Selected";
																								?>
																								<option value="<?=$pondNameId?>" <?=$selected?>><?=$pondNameVal?></option>
																								<? }
																								?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td nowrap="" class="fieldName1"> Count Code</td>
																						<td nowrap="" class="listing-item">
																							<input type="text" size="8" value="<?=$count_code?>" id="count_code" name="count_code">
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																	<?php
																		require("template/rbBottom.php");
																	?>	
																</td>	
																<td width="40%" valign="top">
																<?php
																	$entryHead = "Weighment Challan Details";
																	$rbTopWidth = "";
																	require("template/rbTop.php");
																?>
																	<table align="center" width="96%" cellpadding="0" cellspacing="0">
																		<tr>
																			<td class="fieldName1" nowrap><!--Weighment-->Challan No</td>
																			<td nowrap="true">
																			<?php
																			if($addMode==true){
																				if($p["weighChallanNo"]!="")	$recordWeighNo	= $p["weighChallanNo"];
																				if ($p["alphaCode"]!="") $alphaCode = $p["alphaCode"];
																			}
																			?>
																				<input name="alphaCode" type="text" id="alphaCode" size="2" value="<?=$alphaCode?>"  style="text-align:center; width:30px; border:none;" readonly="true">&nbsp;:&nbsp;
																				<input name="weighChallanNo" type="text" id="weighChallanNo" size="8" value="<?=$recordWeighNo?>" tabindex="7" onkeypress="return focusNextBox(event,'document.frmDailyCatch','selectDate');" onchange="xajax_chkValidCNum(document.getElementById('billingCompany').value, document.getElementById('weighChallanNo').value, document.getElementById('selectDate').value, document.getElementById('entryId').value,document.getElementById('unit').value, '<?=$mode?>');" autocomplete="off"  />
																			</td>
																		</tr>
																		<tr>
																			<td class="fieldName1" nowrap>Entry Date</td>
																			<td nowrap>	
																				<?php 
																					if ($addMode==true) {
																						if ($p["selectDate"]!="") $recordSelectDate = $p["selectDate"];
																					}
																					if ($recordSelectDate=="") {
																						$recordSelectDate	=	date("d/m/Y");
																					}
																				?>
																				<input type="text" id="selectDate" name="selectDate" size="8" value="<?=$recordSelectDate?>" tabindex="8" onkeypress="return focusNextBox(event,'document.frmDailyCatch','selectTimeHour');" onchange="xajax_chkValidCNum(document.getElementById('billingCompany').value, document.getElementById('weighChallanNo').value, document.getElementById('selectDate').value, document.getElementById('entryId').value, '<?=$mode?>'); xajax_chkValidDate(document.getElementById('selectDate').value, document.getElementById('billingCompany').value, '<?=$mode?>');" autocomplete="off" />
																			</td>
																		</tr>
																		<tr>
																			<td class="fieldName1" nowrap>Entry Time </td>
																			<td nowrap>
																			<?
																			if ($addMode==true) {
																				if($p["selectTimeHour"]!="") $selectTimeHour = $p["selectTimeHour"];
																			}
																			if ($selectTimeHour=="") $selectTimeHour = date("g");			  
																			?>
																			<input type="text" id="selectTimeHour" name="selectTimeHour" size="1" value="<?=$selectTimeHour;?>" tabindex="9" onkeypress="return focusNextBox(event,'document.frmDailyCatch','selectTimeMints');" onchange="return timeCheck();" style="text-align:center;" autocomplete="off" /> :
																			<?
																			if($addMode==true){
																				if($p["selectTimeMints"]!="") $selectTimeMints	=	$p["selectTimeMints"];
																			}
																			if($selectTimeMints=="") {
																				$selectTimeMints		=	date("i");
																			 }
																			?>
																				<input type="text" id="selectTimeMints" name="selectTimeMints" size="1" value="<?=$selectTimeMints;?>" tabindex="10" onkeypress="return focusNextBox(event,'document.frmDailyCatch','timeOption');" onchange="return timeCheck();" style="text-align:center;" autocomplete="off" />
																			<? 
																				if($addMode==true){
																					if($p["timeOption"]!="") $timeOption = $p["timeOption"];
																				}
																				if($timeOption=="") {
																					$timeOption = date("A");
																				}
																			?>
																				<select name="timeOption" id="timeOption" tabindex="11" onkeypress="return focusNextBox(event,'document.frmDailyCatch','fish');">
																					<option value="AM" <? if($timeOption=='AM') echo "selected"?>>AM</option>
																					<option value="PM" <? if($timeOption=='PM') echo "selected"?>>PM</option>
																				</select>
																			</td>
																		</tr>
																		<tr>
																			<td class="fieldName1" nowrap>*Fish </td>
																			<td nowrap>
																			<?php 
																			   if ($addMode==true) {
																				if ($addRaw==true)  $fishId = "";
																				else $fishId 	=	$p["fish"];
																				if ($fishId!="") {
																					$processCodeRecords	=	$dailycatchentryObj->pcRecFilter($fishId);	
																				}
																			 }		
																			?>
																				<select name="fish" id="fish" style="width:70%;" tabindex="12" onkeypress="return focusNextBox(event,'document.frmDailyCatch','processCode');" onchange="xajax_filterPC(document.getElementById('fish').value, '');" >
																					<option value="">--Select--</option>
																					<?php
																					  
																					  if($lotIdAvailable==1)
																					  { 
																						  foreach ($fishRecs as $fr)
																						  {
																						$Id	=	$fr[0];
																						$fishName	=	stripSlash($fr[1]);
																						$selected	=	"";
																						if ( $recordFish == $Id || $fishId==$Id) {
																							$selected	=	"selected";
																						} 
																					?>
																					<option value="<?=$Id?>" <?=$selected?>> <?=$fishName?> </option>
																					  <? 
																						}
																						  
																					  }
																					 else { 
																						  foreach ($fishMasterRecords as $fr) 
																						  {
																						$Id	=	$fr[0];
																						$fishName	=	stripSlash($fr[1]);
																						$selected	=	"";
																						if ( $recordFish == $Id || $fishId==$Id) {
																							$selected	=	"selected";
																						} ?>
																						<option value="<?=$Id?>" <?=$selected?>> <?=$fishName?> </option>
																							  <? 
																						 }
																					  }
																					?>
																				</select>
																			</td>
																		</tr>
																		<tr>
																			<td class="fieldName1" nowrap>*Code </td>
																			<td nowrap>
																			<?php 
																			if ($addMode==true) {
																				if ($addRaw==true)  $processId = "";
																				else $processId	=	$p["processCode"];
																					if ($processId) {					
																						$gradeMasterRecords = $dailycatchentryObj->gradeRecFilter($processId);
																						$receivedBy	= $dailycatchentryObj->pcReceivedType($processId);
																					}
																			}
																			?>
																				<select name="processCode" id="processCode" tabindex="13" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryOption');" onchange="if (this.selectedIndex>0) {xajax_filterGrade(document.getElementById('processCode').value, ''); resetGrossWt(this.selectedIndex);}">
																					<option value="">--Select--</option>
																					<?php
																					if (sizeof($processCodeRecords)>0) {
																						foreach ($processCodeRecords as $fl) {
																							$processCodeId		=	$fl[0];
																							$processCode		=	$fl[1];
																							$selected	=	"";
																							if ($recordProcessCode == $processCodeId || $processId==$processCodeId) {
																								$selected	=	"selected";
																							}
																					?>
																					<option value="<?=$processCodeId;?>" <?=$selected;?>><?=$processCode;?></option>
																								<?php
																						}
																					}
																					?>
																				</select>
																			</td>
																		</tr>
																		<tr>
																			<td class="fieldName1" nowrap>Entry
																				<input type="hidden" name="hidReceived" id="hidReceived" value="<?=$receivedBy?>">
																				<input  type="hidden" name="saveChangesOk" size="2" value="<? if ($editMode==true) echo 'Y'; ?>" >
																				<input  type="hidden" name="codeChangedValue" size="2">
																			</td>
																			<td nowrap>
																			<?php
																				//Text box Focus Setting
																				if ($receivedBy=='C') {
																					$onKeyPressFocusNext = "return focusNextBox(event,'document.frmDailyCatch','count');";
																				} else if ($receivedBy=='G') {
																					$onKeyPressFocusNext = "return focusNextBox(event,'document.frmDailyCatch','selGrade');";
																				} else if ($receivedBy=='B') {
																					$onKeyPressFocusNext = "return focusNextBox(event,'document.frmDailyCatch','count');";
																				}
																				if ($addMode==true) {
																				  $entryOption	=	$p["entryOption"];			  
																				}
																			 ?>
																				<select name="entryOption" id="entryOption" onchange="selEntryType();" onkeypress="<?=$onKeyPressFocusNext?>" tabindex="14">
																					<option value="B" <? if($entryOption=='B') echo "Selected";?>>Basket Wt</option>
																					<option value="N" <? if($entryOption=='N') echo "Selected";?>>Net Wt</option>
																				</select>
																			</td>
																		</tr>	
																		<?php 
																		if ($receivedBy=='C' && $addMode!="") {
																			$onChangeCheck = "xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value, document.getElementById('processCode').value, document.getElementById('count').value,'');";
																		} else if ($receivedBy=='G' && $addMode!="") {
																			$onChangeCheck = "xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, '', document.getElementById('selGrade').value);";
																		} else if ($receivedBy=='B' && $addMode!="") {
																			$onChangeCheck = "xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('count').value, document.getElementById('selGrade').value);";
																		}
																				
																		if ( ($receivedBy=='C' || $receivedBy=='B') && $addMode!="") {
																			$onChanageCountAverage = "xajax_checkCountAverageSame(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('countAverage').value);";
																		}
																			$countRowDisplay = "style='display:none;'";
																		if ($receivedBy=='C' ||  $receivedBy=='B' ){
																		//if ($receivedBy=='C' ||  $receivedBy=='B' ||  $receivedBy=='') {
																			$countRowDisplay = "";
																		} 
																		?>
																		<tr id="countRow" <?=$countRowDisplay?>>
																			<td class="fieldName1" nowrap>Count </td>
																			<td nowrap>
																			<?php 
																				if ($addMode==true) {
																					if ($addRaw==true)  $recordCount = "";
																					else $recordCount = $p["count"];
																				}
																			?>
																			<input name="count" type="text" id="count" size="25" value="<?=$recordCount?>" onkeyup="<?=$onChangeCheck?> <?=$onChanageCountAverage?> findAverage();" tabindex="15" onkeypress="return focusNextBox(event,'document.frmDailyCatch','grossWt_1');" autocomplete="off" />
																			</td>
																		</tr>
																		<!--<tr id="countRowDesp" align="left" <?=$countRowDisplay?>>
																		<td colspan="4"  nowrap align="center" style="font-weight:bold"><font size="1" color="#24729f" >eg:-count1,count2</font></td>
																		</tr>-->
																		<?php
																			if($recordCount!="")
																			{
																				$recordCnt=sizeof(explode(",",$recordCount));	
																				if($recordCnt>1)
																				{
																					$countRowAvg = "";
																				}
																				else
																				{
																					$countRowAvg = "style='display:none;'";
																				}

																			}
																			else
																			{
																				$countRowAvg = "style='display:none;'";
																			}
																		?>
																		<tr id="countAvg" <?=$countRowAvg?>>
																			<td class="fieldName1" nowrap>Average</td>
																			<td nowrap>
																			<?php 
																			if($addMode==true) {
																				if ($addRaw==true)  $recordAverage = "";
																				else $recordAverage = $p["countAverage"];
																			}
																			?>
																			<input name="countAverage" type="text" id="countAverage" size="8" value="<?=$recordAverage?>" readonly>
																			</td>
																		</tr>
																		<?php 		
																		$gradeRowDisplay = "style='display:none;'";
																		if ($receivedBy=='G' ||  $receivedBy=='B' ){
																			$gradeRowDisplay = "";
																		}
																		?>
																		<tr id="gradeRow" <?=$gradeRowDisplay?>>
																			<td class="fieldName1" nowrap>Grade</td>
																			<td nowrap>
																			<?php 
																			if ($addRaw==true)  $gradeId = "";
																			else $gradeId = $p["selGrade"];
																			?>
																				<select name="selGrade" id="selGrade" tabindex="15" onchange="<?=$onChangeCheck?>">
																					<option value="" >Select Grade</option>
																					<? 
																					if (sizeof($gradeMasterRecords)> 0) {
																					foreach ($gradeMasterRecords as $gl) {
																						$id		= $gl[0];	
																						$displayGrade	= $gl[1];
																						$selected		=	"";
																						if ($recordGradeId== $id || $gradeId==$id) {
																							$selected	=	" selected ";
																						}
																					?>
																					<option value="<?=$id;?>" <?=$selected;?> > <?=$displayGrade;?> </option>
																					<?
																					}
																				}
																				?>
																				</select>
																			</td>
																		</tr>
																		<!--<tr><td>&nbsp;</td></tr>-->
																		<input type="hidden" name="weightmentId" id="weightmentId" value="<?=$weightmentId?>"/>
																<!-- Grade Ends here -->
																	</table>
																	<?php
																		require("template/rbBottom.php");
																	?>
																</td>
																<td width="30%" valign="top">
																	<?php
																	$entryHead = "";
																	$rbTopWidth = "";
																	require("template/rbTop.php");
																	?>
																	<table align="center" cellpadding="0" cellspacing="0" width="100%">			
																		<tr bgcolor="White">
																			<TD style="line-height:20px;">
																				<div style="height:100%; float: left; vertical-align:middle; padding-left:2px;">
																					<input name="paymentBy" type="checkbox" id="paymentBy" value="D" class="chkBox" <?=$checked;?>>
																				</div>
																				<div style="height:100%; float: left; vertical-align:middle; padding-left:3px;"><img src="images/topLink.jpg" border="0" /></div>
																				<div style="float: left;" class="rbTopHeadTxt">Declared</div>				
																			</TD>			
																		</tr>			
																		<tr>
																			<td align="center">
																				<? 					
																					if ($addMode==true) { 					
																						if ($p["paymentBy"]=='D') {
																							$checked="Checked";
																						}
																				  }
																				?>
																				<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
																					<tr>
																						<td nowrap align="center" valign="top">
																						<input type="hidden" name="totalDeclaredWt" id="totalDeclaredWt">
																						<iframe src="CatchEntryDeclaredItem.php?entryId=<?=$catchEntryNewId;?>&mainSupplier=<?=($mainSupplier=="")?$recordMainSupply:$mainSupplier?>&landingCenter=<?=($landingCenter=="")?$recordLanding:$landingCenter?>" width="457" frameborder="0" height="200" id="CatchEntryDeclaredItem"></iframe>
																						</td>
																					</tr>
																				</table>	
																			</td>
																		</tr>
																	</table>	
																	<?php
																	require("template/rbBottom.php");
																	?>
																</td>
															</tr>
														</table>		
													</td>
												</tr>
												<?php 
													if($addRaw==true) $p["processCode"]=""; 
													$cEntryGrossRowDisplay = "style='display:none;'";
													$catchEntryGrossSrc = "";
														if( ($p["processCode"]!=""&& $entryOption=='B')|| ($recordProcessCode!="" && $entryOption=='B') ){	
															$cEntryGrossRowDisplay = "";					
														}
												?>
												<tr id="cEntryGrossWtRow" <?=$cEntryGrossRowDisplay?>>
													<td colspan="4" align="left">
														<?php
															$rbTopWidth = "75%";
															require("template/rbTop.php");
														?>
														<table width="100%" cellpadding="0" cellspacing="0">
															<TR bgcolor="White">
																<td style="line-height:20px;">
																	<table cellpadding="0" cellspacing="0">
																		<TR bgcolor="White">
																			<td style="padding-left:2px;">
																				<img src="images/topLink.jpg" border="0" />
																			</td>
																			<TD class="rbTopHeadTxt" align="left" style="line-height:20px;">Count Details</TD>
																		</TR>
																	</table>
																</td>
															</TR>	
															<tr><TD height="10"></TD></tr>	
														</table>		
														<!-- Catch Entry Gross Starts here width="952" frameborder="0" height="400" width: 1196px 1100-->	
														<div style="width: 900px; height: 400px; overflow-x: scroll; overflow-y: scroll; scrollbar-arrow-color:blue; scrollbar-face-color: #e7e7e7; scrollbar-3dlight-color: #a0a0a0; scrollbar-darkshadow-color:#888888;" id="catchentrygrosswt">
															<table cellpadding="0" cellspacing="0" border="0" width="100%" align="left">	
																<tr>
																<?php
																	$col = 20;
																	$gWtVal = "grossWtArr = new Array(";
																	$bWtVal = "bWtArr = new Array(";		
																	$idVal = "idArr = new Array(";
																	$c = 0;
																	$k = 0;
																	$basktWt = ($recordBasketWt!=0)?$recordBasketWt:"''";
																	for($i=1;$i<=$col;$i++) {
																?>			
																	<td width="9%">
																		<table cellpadding="0" cellspacing="0" id="newspaper-dce">		
																			<tr align="center">
																				<td width="10%" class="countETxt">No:</td>
																				<td width="80%" class="countETxt">GWt</td>
																				<td width="5%" class="countETxt">BWt</td>
																			</tr>
																			<?php 
																				$row=15;
																				$sos	=	($i-1)*$row+1;
																				$totalGrossWt	=	"";
																				$totalBasketWt	=	"";
																				$netWt		=	"";			
																				for($j=1;$j<=$row;$j++) {
																					$id	=(($i-1)*$row)+$j;
																					$num	=	300;
																					$hidId=0;
																					$gwt="";
																					$bwt="";							
																					if ($id <= sizeof($countGrossRecords) ) {
																						$rec = $countGrossRecords[$id-1];
																						$hidId=$rec[0];
																						$gwt=$rec[1];
																						$bwt=$rec[2];					
																						$totalGrossWt	+= $gwt;
																						$totalBasketWt	+= $bwt;
																						$netWt	=	$totalGrossWt-$totalBasketWt;
																						if ($c!=0) {
																							$bWtVal .= ",";
																							$gWtVal .= ",";
																							$idVal .= ",";
																						}
																						$gWtVal .= $gwt;
																						$bWtVal .= $bwt;
																						$idVal .= $hidId;
																						$c++;
																					} else {
																						$bwt = $recordBasketWt;	
																						
																						if ($c>0) {
																							$bWtVal .= ",";
																							$gWtVal .= ",";
																							$idVal .= ",";
																						} else if ($k!=0) {
																							$bWtVal .= ",";
																							$gWtVal .= ",";
																							$idVal .= ",";
																						}
																						$gWtVal .= "''";
																						$bWtVal .= $basktWt;
																						$idVal .= 0;
																						$k++;
																						}
																						if ( $id < $num) $nextControl = "grossWt_".($id+1);
																						else $nextControl = "cmdSaveChange";
																						?>
																				<tr>
																					<td nowrap align="left">
																						<table cellpadding="0" cellspacing="0">
																							<TR>
																								<TD>
																									<input type="checkbox" name="delId_<?=$id;?>" id="delId_<?=$id;?>" class="chkBox" value="<?=$hidId;?>">
																								</TD>
																								<td class="listing-item" style="line-height:normal;" align="left">
																									<?=$id?>
																								</td>
																							</TR>
																						</table>
																					</td>
																					<td>
																						<?//=($id+$num);?>
																						<input type="hidden" name="grossId_<?=$id;?>" id="grossId_<?=$id;?>" value="<?=$hidId?>" readonly="true" />
																						<input type="text" name="grossWt_<?=$id;?>" id="grossWt_<?=$id;?>" value="<?=$gwt?>" size="3" style="text-align:right" tabindex="15" onkeypress="parent.document.frmDailyCatch.saveChangesOk.value='';return focusNext(event,'document.frmDailyCatch','<?=$nextControl?>','<?=$i?>','<?=$sos?>',<?=$row?>);totalWt('<?=$i?>','<?=$sos?>',<?=$row?>);" onchange="totalWt('<?=$i?>','<?=$sos?>',<?=$row?>);" autocomplete="off" />
																					</td>
																					<td>
																						<input type="text" name="grossBasketWt_<?=$id;?>" id="grossBasketWt_<?=$id;?>" value="<?=$bwt;?>"  size="3" style="text-align:right; width:30px;" maxlength="5" autocomplete="off" onchange="totalWt('<?=$i?>','<?=$sos?>',<?=$row?>);" />
																					</td>
																				</tr>
																				<?php 
																					}
																				?>
																				<tr>
																					<td class="fieldName1" style="text-align:left;" nowrap>Tot 
																						<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$id?>" />
																					</td>
																					<td class="listing-item">
																						<input type="text" size="3" name="totWt_<?=$i?>" id="totWt_<?=$i?>" style="border:none; text-align:right; background-color::#D0DAFD;" readonly value="<?=($totalGrossWt!=0)?number_format($totalGrossWt,2,'.',''):"";?>" />
																					</td>
																					<td class="listing-item" style="padding-right:1px;">
																						<input name="basketWt_<?=$i?>" id="basketWt_<?=$i?>" type="text" style="border:none; text-align:right; background-color::#D0DAFD;" size="3" readonly value="<?=($totalBasketWt!=0)?number_format($totalBasketWt,2,'.',''):"";?>" />
																					</td>
																				</tr>
																				<tr>
																					<td colspan="3" class="fieldName1" style="text-align:left;" nowrap>Net:<span class="listing-item"><input type="text" size="3" name="netWt_<?=$i?>" id="netWt_<?=$i?>" style="border:none; text-align:right; background-color::#D0DAFD;" readonly value="<?=($netWt!=0)?number_format($netWt,2,'.',''):"";?>" />&nbsp;Kg</span>
																					</td>
																				</tr>
																			</table>
																			<input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$i?>" />
																		</td>
																		<?php
																		 }
																		$gWtVal .= ")";
																		$bWtVal .= ")";
																		$idVal .= ")";
																		?>				
																	</tr>
																	<tr>
																		<td  colspan="14" align="center">
																			<input type="hidden" name="curBasketWt" id="curBasketWt" size="3" value="" />	
																			<input type="hidden" name="countSaved" id="countSaved" value="" />
																			<input type="hidden" name="isSaved" id="isSaved" value="" />
																			<input type="hidden" name="declNetWt" id="declNetWt" value="" />
																		</td>
																	</tr>
																</table>
															</div>	
															<div align="center" style="height:30px; line-height:30px;">
															<!-- return confirmDelete(this.form,'delId_',0); -->
																<input type="button" value=" Delete " name="cmdDelete" class="button" onClick="delCountData();">&nbsp;		
															</div>
															<!-- Catch Entry Gross Ends here  -->	
															<?php
																require("template/rbBottom.php");
															?>
														</td>
													</tr>
													<tr>
														<td colspan="4" align="left">
														<!-- Last section starts here  -->
														<input type="hidden" name="entryTotalGrossWt" id="entryTotalGrossWt" value="<?=$totalWt;?>">
														<input type="hidden" name="entryTotalBasketWt" id="entryTotalBasketWt" value="<?=$grandTotalBasketWt;?>">
															<table cellpadding="4" cellspacing="0">
																<tr>
																	<td valign="top">
																		<?php
																			$entryHead = "Weight Calculation";
																			$rbTopWidth = "";
																			require("template/rbTop.php");
																		?>		
																		<table align="center" >
																			<TR>
																				<TD>
																					<table width="200" border="0" cellpadding="0" cellspacing="0">
																						<?php
																							$wtCalcRow = "style='display:none;'";		
																							if($entryOption!='N') {			
																								$wtCalcRow = "";
																							}
																						?>
																						<tr id="wtCalcTotGrWt" <?=$wtCalcRow?> >
																							<td class="fieldName1" nowrap>Total Gross Wt </td>
																							<td nowrap>
																								<input name="totalGrossWt" id="totalGrossWt" type="text" value="<?=$totalWt;?>" size="8" style="text-align:right;" readonly>
																							</td>
																						</tr>
																						<tr id="wtCalcTotBsktWt" <?=$wtCalcRow?>>
																							<td class="fieldName1" nowrap>Total Basket Wt </td>
																							<td nowrap><input name="totalBasketWt" id="totalBasketWt" type="text" value="<?=$grandTotalBasketWt;?>" size="8" style="text-align:right;" readonly></td>
																						</tr>		
																						<tr>
																							<td class="fieldName1" nowrap>Net Wt</td>
																							<td class="listing-item" nowrap>
																								<? 
																									if($addMode==true){
																										if($addRaw == true)  $netGrossWt = "";
																										else $netGrossWt = $p["entryGrossNetWt"];
																									}
																								?>
																								<!--  tabindex="17"-->
																								<input type="text" name="entryGrossNetWt" id="entryGrossNetWt" value="<?=$netGrossWt?>" size="8" style="text-align: right" <? if($entryOption!='N'){?> readonly <?} ?>  onchange="Javascript:actualWt();" tabindex="15" autocomplete="off" />
																								Kg
																							</td>
																						</tr>
																					</table>	 	
																				</TD>
																			</TR>
																		</table>
																		<?php
																			require("template/rbBottom.php");
																		?>
																	</td>
																	<td valign="top">
																		<?php
																			$entryHead = "Final Weight";
																			$rbTopWidth = "";
																			require("template/rbTop.php");
																		?>		
																		<table width="200" align="center" border="0">
																			<tr>
																				<td>
																					<table border="0">
																						<tr id="bsktWtRow" <?=$wtCalcRow?> >
																							<td class="fieldName1" nowrap>Basket Weight</td>
																							<td class="listing-item" nowrap align="right" nowrap>
																								<input name="dailyBasketWt" id="dailyBasketWt" type="text" size="3" value="<?=$processBasketWt?>" style="text-align: right" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryAdjust');" autocomplete="off" />
																								Kg 
																							</td>
																							<td>&nbsp;
																								<input type="button" name="cmdReset" class="button" value="Reset" onclick="resetBWt(document.getElementById('dailyBasketWt').value);" />
																							</td>
																						</tr>	
																						<tr>
																							<td class="fieldName1" nowrap>Adjustment</td>
																							<td class="listing-item" nowrap align="right">
																							<? if($addMode==true){
																								if($addRaw == true)  $recordAdjust = "";
																								else $recordAdjust		=	$p["entryAdjust"];
																								}
																							?>
																							<input name="entryAdjust" id="entryAdjust" type="text" size="4" onkeyup="return actualWt();" onchange="return effectiveWt();" value="<?=$recordAdjust?>" style="text-align: right" tabindex="18" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonAdjust');" autocomplete="off" />&nbsp;Kg </td>
																							<td nowrap  align="left">
																								<table>
																									<TR>
																										<TD class="fieldName1" nowrap>
																										Reason
																										</TD>
																										<td nowrap>
																										<? if($addMode==true){
																											if($addRaw	==	true)  $recordReason = "";
																											else $recordReason		=	$p["reasonAdjust"];
																											}
																										?>
																										<input name="reasonAdjust" type="text" id="reasonAdjust" size="20" value="<?=$recordReason?>" tabindex="19" onkeypress="return focusNextBox(event,'document.frmDailyCatch','gradeCountAdj');" autocomplete="off" />
																										</td>
																									</TR>
																								</table>
																							</td>
																						</tr>
																						<tr>
																							<td nowrap class="fieldName1">Grade/Count Adj </td>
																							<td class="listing-item" align="right">
																							<? if($addMode==true){
																								if($addRaw	==	true)  $gradeCountAdj = "";
																								else $gradeCountAdj		=	$p["gradeCountAdj"];
																								}
																							?>
																							<input name="gradeCountAdj" type="text" id="gradeCountAdj" style="text-align: right" tabindex="20" onchange="return effectiveWt();" onkeypress="return focusNextBox(event,'document.frmDailyCatch','gradeCountAdjReason');" onkeyup="return actualWt();" value="<?=$gradeCountAdj?>" size="4" autocomplete="off" />&nbsp;Kg</td>
																							<td nowrap align="left">
																								<table>
																									<TR>
																										<TD class="fieldName1" nowrap>
																										Reason
																										</TD>
																										<td nowrap>
																											 <?
																											if ($addMode==true) {
																												if ($addRaw==true)  $gradeCountAdjReason = "";
																												else $gradeCountAdjReason =	$p["gradeCountAdjReason"];
																											}
																											?>
																											<input name="gradeCountAdjReason" type="text" id="gradeCountAdjReason" size="20" value="<?=$gradeCountAdjReason?>" tabindex="21" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryLocal');" autocomplete="off" />
																										</td>
																										<td nowrap>
																											<table>
																												<TR>
																													<TD nowrap>
																														<input name="noBilling" type="checkbox" id="noBilling" value="Y" class="chkBox" onclick="actualWt();" <?=$noBillingChk;?> />
																													</TD>
																													<td class="fieldName" nowrap>No Billing</td>
																												</TR>
																											</table>
																										</td>
																									</TR>
																								</table>
																							</td>
																						</tr>
																						<tr>
																							<td nowrap class="fieldName1">Actual Wt</td>
																							<td class="listing-item" align="left" nowrap="true" colspan="2">
																							<?
																							if($addRaw	==	true)  $entryActualWt = "";
																							?>
																							<input name="entryActualWt" id="entryActualWt" type="text" size="10" readonly style="text-align: right" value="<?=$entryActualWt;?>">
																							Kg
																							</td>
																						</tr>
																						<!-- Local Wastage section starts here  -->
																						<tr>
																							<td class="fieldName1" nowrap>Local Quantity</td>
																							<td class="listing-item" nowrap>
																								<input name="entryLocalPercent" type="text" id="entryLocalPercent" value="0.00" size="3" style="text-align:right" readonly>&nbsp;% </td>
																							<td class="listing-item" nowrap colspan="3" align="left">
																								<table>
																									<TR>
																										<TD class="listing-item" nowrap>
																										<? 
																											if($addMode==true){
																												if ($addRaw ==	true)  $recordLocalQty = "";
																												else $recordLocalQty = $p["entryLocal"];
																											}
																										?>
																										<input name="entryLocal" type="text" id="entryLocal" value="<? if($recordLocalQty=="") { echo 0; } else { echo $recordLocalQty;} ?>" onkeyup="return effectiveWt();" size="5" style="text-align: right" tabindex="22" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonLocal');" autocomplete="off" />&nbsp;Kg
																										</TD>
																										<td class="fieldName1" nowrap>Reason</td>
																										<td class="listing-item" nowrap>
																												<input name="reasonLocal" type="text" id="reasonLocal" size="20" tabindex="23" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryWastage');" value="<?=$reasonLocal;?>" autocomplete="off" />
																										</td>
																									</TR>
																								</table>			
																							</td>					
																						</tr>
																						<tr>
																							<td class="fieldName1" nowrap>Wastage</td>
																							<td class="listing-item" nowrap>
																								<input name="entryWastagePercent" type="text" id="entryWastagePercent" value="0.00" size="3" style="text-align:right" readonly>&nbsp;%
																							</td>
																							<td class="listing-item" nowrap colspan="3" align="left">
																								<table>
																									<TR>
																										<TD class="listing-item" nowrap>
																											<? 
																											if ($addMode==true) {
																												if ($addRaw==true)  $recordWastage = "";								
																												else $recordWastage = $p["entryWastage"];
																											}
																											?>
																										<input name="entryWastage" type="text" id="entryWastage" size="5" onkeyup="return effectiveWt();" value="<? if($recordWastage=="") { echo 0; } else { echo $recordWastage;} ?>" style="text-align: right"  tabindex="24" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonWastage');" autocomplete="off"/>&nbsp;Kg
																										</TD>
																										<td class="fieldName1" nowrap>Reason</td>
																										<td class="listing-item" nowrap>
																											<input name="reasonWastage" type="text" id="reasonWastage" size="20" tabindex="25" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entrySoft');" value="<?=$reasonWastage;?>" autocomplete="off"/>
																										</td>
																									</TR>
																								</table>
																							</td>
																						</tr>
																						<tr>
																							<td class="fieldName1" nowrap>Soft</td>
																							<td class="listing-item" nowrap>
																								<input name="entrySoftPercent" type="text" id="entrySoftPercent" value="0.00" size="3" style="text-align:right" readonly />&nbsp;%
																							</td>
																							<td class="listing-item" nowrap>
																								<table>
																									<TR>
																										<TD class="listing-item" nowrap>
																										<?
																										 if($addMode==true){
																											if($addRaw	==	true)  $recordSoft = "";								
																											else $recordSoft		=	$p["entrySoft"];
																										}
																										?>
																										<input name="entrySoft" type="text" id="entrySoft" size="5" onkeyup="return effectiveWt();" value="<? if($recordSoft=="") { echo 0; } else { echo $recordSoft;} ?>" style="text-align: right" tabindex="26" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonSoft');" autocomplete="off" />&nbsp;Kg
																										</TD>
																										<td class="fieldName1" nowrap>Reason</td>
																										<td class="listing-item"><input name="reasonSoft" type="text" id="reasonSoft" size="20" tabindex="26" onkeypress="return focusNextBox(event,'document.frmDailyCatch','goodPack');" value="<?=$reasonSoft;?>" autocomplete="off" /></td>
																									</TR>
																								</table>
																							</td>		
																						</tr>
																						<tr id="additonalWastage" width="500px">
																							<?php 
																								if(isset($qualityRecords) && sizeof($qualityRecords) > 0)
																								{
																								echo'<td colspan="3"><table>';								$l=0;
																									foreach($qualityRecords as $records)
																									{
																							?>
																								<tr>
																									<td class="fieldName1" nowrap>
																										<?php 
																										echo $records[3];
																										echo '<input type="hidden" name="quality_entry_id_'.$l.'" id="quality_entry_id_'.$l.'" value="'.$records[0].'" />';
																										echo '<input type="hidden" name="quality_new_'.$l.'" id="quality_new_'.$l.'" value="'.ucfirst($records[3]).'" />
																											 <input type="hidden" name="qualityId_'.$l.'" id="qualityId_'.$l.'" value="'.$records[1].'" />';
																										?>
																									</td>
																									<td class="listing-item" nowrap>
																										<input name="qualityPercent_<?php echo $l;?>" type="text" id="qualityPercent_<?php echo $l;?>" value="<?php echo $records[2];?>" size="3" style="text-align:right" readonly />&nbsp;%
																									</td>
																									<td class="listing-item" nowrap>
																										<table>
																											<TR>
																												<TD class="listing-item" nowrap>
																													  <input name="qualityWeight_<?php echo $l;?>" type="text" id="qualityWeight_<?php echo $l;?>" size="5" onkeyup="return effectiveWtNew('<?php echo $records[1];?>');" value="<?php echo $records[4];?>" style="text-align: right" tabindex="26" autocomplete="off" />&nbsp;Kg
																												</TD>
																												<td class="fieldName1" nowrap>Reason</td>
																												<td class="listing-item"><input name="qualityReason_<?php echo $l;?>" type="text" id="qualityReason_<?php echo $l;?>" size="20" value="<?php echo $records[5];?>" autocomplete="off" /></td>
																												
																												<input id="weightmentStatus_<?php echo $l;?>" type="hidden" value="1" name="weightmentStatus_<?php echo $l;?>">	
																												<input id="billing_<?php echo $l;?>" type="checkbox" <?php echo $records[6];?> name="billing_<?php echo $l;?>" style="display:none;">																											
																											</TR>
																										</table>
																									</td>
																								</tr>
																								<?php
																								$l++;
																								}
																								?>
																							</table>
																						</td>	
																						<script>
																						jQuery(document).ready(function(){
																							effectiveWtNew();
																						});
																						</script>
																						<?php
																							}
																						?>
																						</tr>
																						<tr>
																							<td colspan="4">
																								<table>
																									<tr>
																										<td>
																											<table width="100%" cellspacing="1" bgcolor="#999999" cellpadding="2" id="tblQuality" name="tblQuality">
																											<?php
																											if(sizeof($dailyQualityEntry)>0)
																											{
																												if(sizeof($qualityRecords)>0)
																												{
																												$m=sizeof($qualityRecords);
																												}
																												else{
																												$m=0;
																												}
																											$sizeval=sizeof($dailyQualityEntry)+$qualityDataSheetSize;
																										
																											?>	
																											<tbody>
																											<?php 
																											foreach ($dailyQualityEntry as $qlty )
																											//for($m=$qualityDataSheetSize; $m<$sizeval; $m++)
																											{	$qualId=$qlty[1];
																											?>
																											<tr id="Row_<?php echo $m;?>" class="whiteRow" align="center">
																												<td id="srNo_<?php echo $m;?>" class="listing-item" align="center">
																													<select id="qualityId_<?php echo $m;?>" onchange="xajax_getQualityDet(document.getElementById('qualityId_<?php echo $m;?>').value,<?php echo $m;?>);" name="qualityId_<?php echo $m;?>">
																														<option value="">--select--</option>
																																<?php
																																	
																																	foreach($qualityValues as $sr)
																																	{
																																		$qualityValuesId		=	$sr[0];
																																		$qualityValuesName	=	$sr[1];
																																		$sel  = ($qualityValuesId==$qualId)?"Selected":"";
																																?>
																														<option value="<?=$qualId?>" <?=$sel?>><?=$qualityValuesName?></option>
																														<?}?>
																													</select>
																													<?php if($qlty[7]==1)
																													{
																													$checked="checked=checked";
																													}
																													else{
																													$checked="";
																													}
																													?>
																													<input type="checkbox" name="billing_<?php echo $m;?>" id="billing_<?php echo $m;?>" <?php echo $checked;?> value="<?php echo $qlty[7];?>" onclick="checkStatus(<?php echo $m;?>)" />
																												</td>
																												<td class="listing-item" align="center">
																													<input id="qualityPercent_<?php echo $m;?>" type="text" value="<?php echo $qlty[2];?>" size="5" name="qualityPercent_<?php echo $m;?>">%
																												</td>
																												<td class="listing-item" align="center">
																													<input id="qualityWeight_<?php echo $m;?>" type="text" value="<?php echo $qlty[4];?>" onkeyup="return effectiveWtNew();" size="5" name="qualityWeight_<?php echo $m;?>">Kg
																												</td>
																												<td class="listing-item" align="center">
																													Reason<input id="qualityReason_<?php echo $m;?>" type="text" autocomplete="off" value="<?php echo $qlty[5];?>" size="20" name="qualityReason_<?php echo $m;?>">
																												</td>
																													<input type="hidden" name="quality_entry_id_<?php echo $m;?>" id="quality_entry_id_<?php echo $m;?>" value="<?php echo $qlty[0];?>" />
																												<td class="listing-item" align="center">
																													<a onclick="setTestRowItemStatusVal('<?php echo $m;?>');" href="###">
																													<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
																													</a>
																													<input id="Status_<?php echo $m;?>" type="hidden" value="" name="Status_<?php echo $m;?>">
																													<input id="IsFromDB_<?php echo $m;?>" type="hidden" value="N" name="IsFromDB_<?php echo $m;?>">
																													<input id="quality_new_<?php echo $m;?>" type="hidden" value="<?php echo $qlty[6];?>" name="quality_new_<?php echo $m;?>">
																													<input id="weightmentStatus_<?php echo $m;?>" type="hidden" value="0" name="weightmentStatus_<?php echo $m;?>">	
																												</td>
																											</tr>
																											<?php
																											$m++;
																											}
																											?>
																										<input type="hidden" name="total_new_entry" id="total_new_entry" value="<?php echo $m;?>" />	
																										</tbody>
																										<?php 
																										}		
																										elseif(sizeof($dailyQualityEntry)==0 && sizeof($qualityRecords)>0)	
																										{																						
																										?>
																											<input type="hidden" name="total_new_entry" id="total_new_entry" value="<?php echo sizeof($qualityRecords);?>" />
																										<?php
																										}
																										else
																										{
																										?>																						
																										   <input type="hidden" name="total_new_entry" id="total_new_entry" value="0" />	
																										<?php
																										}
																										?>																						
																									</table>
																								</td>
																							</tr>
																							<tr>
																								<td style="padding-left:5px;padding-right:5px;" colspan="2">
																								<a id="addRow" class="link1" title="Click here to add new quality." onclick="javascript:addAllQuality();" href="javascript:void(0);">
																								<img border="0" style="border:none;padding-right:4px;vertical-align:middle;" src="images/addIcon.gif">
																								Add New Quality
																								</a>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td nowrap class="fieldName1" colspan="2">Effective Weight</td>
																					<td colspan="2" align="left" class="listing-item">&nbsp;
																					<?php
																						if ($addRaw == true)  $entryEffectiveWt = "";
																					?>
																						<input name="entryEffectiveWt" type="text" id="entryEffectiveWt" size="8" readonly style="text-align: right" value="<?=$entryEffectiveWt;?>"  onchange="return effectiveWt();">
																							Kg
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
																<?php
																	require("template/rbBottom.php");
																?>
															</td>
															<td valign="top">
																<table align="center" cellpadding="0" cellspacing="0">
																	<tr><TD height="5"></TD></tr>
																	<TR>
																		<TD>
																			<?php
																				$entryHead = "";
																				$rbTopWidth = "";
																				require("template/rbTop.php");
																			?>
																			<table>
																				<tr>
																					<td>
																						<table>
																							<tr>
																								<td class="fieldName1" nowrap>Good for Packing</td>
																								<td class="listing-item" nowrap>
																								<? if($addMode==true){
																									if($addRaw	==	true)  $recordGood = "";
																									else $recordGood			=	$p["goodPack"];
																								}
																								?>
																									<input name="goodPack" type="text" id="goodPack" size="2" value="<? if($recordGood=="") { echo 100; } else { echo $recordGood;} ?>"  tabindex="28" onkeypress="return focusNextBox(event,'document.frmDailyCatch','peeling');" style="text-align:right;" onkeyup="return calcPeeling(document.frmDailyCatch);" autocomplete="off" />
																								 %
																								</td>
																							</tr>
																							<tr>
																								<td class="fieldName1" nowrap>For Peeling</td>
																								<td class="listing-item" nowrap><? 
																									if($addMode==true){
																										$recordPeeling =0;
																										if( $p["peeling"]!=0  )
																										{
																											if($addRaw	==	true)  $recordPeeling = "";
																											else $recordPeeling			=	$p["peeling"];
																											}
																										}
																									?>
																									<input name="peeling" type="text" id="peeling" size="2" value="<?=$recordPeeling?>" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryRemark');" readonly style="text-align:right;">
																								  %
																								</td>
																							</tr>
																							<tr>
																								<td class="fieldName1" nowrap>Remarks:</td>
																								<td class="listing-item" nowrap>
																								<? 
																								if($addMode==true) {
																									if ($addRaw ==	true)  $recordRemarks = "";
																									else $recordRemarks		=	$p["entryRemark"];
																								}
																								?>
																									<textarea name="entryRemark" cols="23" rows="4" id="entryRemark" tabindex="29" onkeypress="return focusNextBox(event,'document.frmDailyCatch','cmdAddRaw');"><?=$recordRemarks?></textarea>
																								</td>
																							</tr>
																						</table>	
																					</td>
																				</tr>
																			</table>
																			<?php
																				require("template/rbBottom.php");
																			?>
																		</TD>
																	</TR>
																</table>
															</td>
														</tr>
													</table>
												</td>
												<!-- RM Weighment challan End-->
												<td> &nbsp;&nbsp;</td>
												<td> &nbsp;&nbsp;</td>
											</tr>
										</table>
										<!-- Last section ends here	 -->
									</td>
								</tr>
								<tr>
									<td colspan="4"  height="10" ></td>
								</tr>
								<tr>
									 <? if($editMode){?>
									 <td align="center" nowrap colspan="2"><input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('DailyCatchEntry_New.php');" />
										&nbsp;&nbsp;
										<input type="submit" name="cmdDailySaveChange" id="cmdDailySaveChange1" class="button" value=" Save Changes " onclick="return validateAddDailyCatchEntry(document.frmDailyCatch,null,null);" />&nbsp;&nbsp;
										<? if($add==true){?>
										<input type="button" name="cmdAddRaw" id="cmdAddRaw1" class="button" value="Save & Add New Raw Material in Challan" onclick="return validateAddDailyCatchEntry(document.frmDailyCatch, '<?=$mode?>', 'RM');" tabindex="30" style="width:250px;">
										&nbsp;&nbsp;
									<? }?>
									 </td>
									 <?} else{?>
									 <td  colspan="2" align="center" nowrap><input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onclick="return cancel('DailyCatchEntry_New.php');" />
										&nbsp;&nbsp;
										<input type="submit" name="cmdAddDailyCatch" id="cmdAddDailyCatch1" class="button" value=" Save & Exit " onclick="return validateAddDailyCatchEntry(document.frmDailyCatch,null,null);" />&nbsp;&nbsp;
										<input type="button" name="cmdAddNewChallan" id="cmdAddNewChallan1" value="save & Add New Challan" class="button" onclick="return recordSaved(document.frmDailyCatch, '<?=$mode?>', 'NC');" style="width:150px;"> &nbsp;&nbsp;
										<input type="button" name="cmdAddRaw" id="cmdAddRaw1" class="button" value="Save & Add New Raw Material in Challan" onclick="return validateAddDailyCatchEntry(document.frmDailyCatch, '<?=$mode?>', 'RM');" tabindex="32" style="width:250px;">
										&nbsp;&nbsp;
									 </td>
									 <input type="hidden" name="cmdAddNew" value="1">
									 <?}?>
									 <? if($addRawMaterial){?>
										<input type="hidden" name="cmdAddRaw" value="1" />
									 <? }?>
								</tr>
							</table>
							<?php
								include "template/boxBR.php";
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		}		
		# Listing DailyCatchEntry Starts
		?> 
		<tr>
			<TD align="left">
				<table cellspacing="0" align="left" cellpadding="0" width="60%">
				<?php
					if ($displayListEnabled) {
				?>
					<tr> 
						<td> 
							<table cellpadding="0"  cellspacing="0" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
								<tr> 
									<td   bgcolor="white"  width="100%"> 
									<?	$bxHeader="DAILY RAW MATERIAL ENTRY";
										include "template/boxTL.php";
									?>
										<table width='100%'>
											<tr>
												<td colspan="4" align="center">
													<table width="50%" border="0" cellpadding="0" cellspacing="0" align="center">
														<tr>
															<td>
																<table border="0" cellpadding="2">
																	<tr>
																		<td>
																		<?php
																			$entryHead = "Data Search";
																			$rbTopWidth = "";
																			require("template/rbTop.php");
																		?>
																			<table cellpadding="3" cellspacing="0" width="100%" border="0">
																				<tr>
																					<TD valign="top">
																						<table>
																							<TR>
																								<td class="fieldName1">From</td>
																								<td nowrap="nowrap">
																								<? 
																									if($dateFrom=="") $dateFrom=date("d/m/Y");
																								?>
																								<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getSupplier('supplier', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.getElementById('supplier').value, document.getElementById('billingCompanyFilter').value, document.getElementById('filterType').value); xajax_getFish('selFish', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.getElementById('supplier').value, document.getElementById('selFish').value, document.getElementById('billingCompanyFilter').value); xajax_getBillingCompany(document.getElementById('supplyFrom').value, document.getElementById('supplyTill').value,'<?=$fBillingCompany?>', document.getElementById('supplier').value, document.getElementById('filterType').value);" autocomplete="off"></td>
																							</TR>
																							<tr>
																								<td class="fieldName1"> Till</td>
																								<td> 
																								<?
																									if($dateTill=="") $dateTill=date("d/m/Y");
																								?>
																									<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getSupplier('supplier', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.getElementById('supplier').value, document.getElementById('billingCompanyFilter').value, document.getElementById('filterType').value); xajax_getFish('selFish', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.getElementById('supplier').value, document.getElementById('selFish').value, document.getElementById('billingCompanyFilter').value); xajax_getBillingCompany(document.getElementById('supplyFrom').value, document.getElementById('supplyTill').value, document.getElementById('billingCompanyFilter').value, document.getElementById('supplier').value, document.getElementById('filterType').value);" autocomplete="off">
																								</td>
																							</tr>
																						</table>
																					</TD>
																					<td valign="top">
																						<table>
																							<TR>
																								<td class="fieldName1">Records</td>
																								<td class="listing-item">
																									<select name="selRecord" id="selRecord" style="width:70px;">
																										<option value="0" <? if($selRecord==0) echo "selected";?>>-- All --</option>
																										<option value="C" <? if($selRecord=='C') echo "selected";?>>Complete</option>
																										<option value="I" <? if($selRecord=='I') echo "selected";?>>Incomplete</option>
																										<option value="Z" <? if($selRecord=='Z') echo "selected";?>>Zero Entry</option>
																									</select>
																								</td>
																							</TR>
																							<tr>
																								<TD class="fieldName1">
																									Filter
																								</TD>
																								<td nowrap="true" style="padding-left:2px; padding-right:2px;">
																									<select name="filterType" id="filterType" style="width:70px;" onchange="this.form.submit();">
																										<option value="BW" <? if ($filterType=='BW') echo "selected";?>>Billing Company Wise</option>
																										<option value="SW" <? if ($filterType=='SW') echo "selected";?>>Supplier Wise</option>
																									</select>
																								</td>
																							</tr>
																						</table>	
																					</td>
																					<td valign="top">
																						<table>
																						<?php
																							if ($filterType=='SW') {
																						?>
																							<TR>
																								<TD class="fieldName1" style="padding-left:2px;padding-right:2px;">Supplier</TD>
																								<td nowrap="true" style="padding-left:2px;padding-right:2px;">
																									<select name="supplier" id="supplier" onchange="xajax_getFish('selFish', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.frmDailyCatch.supplier.value, document.getElementById('selFish').value, document.getElementById('billingCompanyFilter').value); xajax_getProcessCode('selProcesscode', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.frmDailyCatch.supplier.value, document.frmDailyCatch.selFish.value, document.getElementById('selProcesscode').value, document.getElementById('billingCompanyFilter').value); xajax_getBillingCompany(document.getElementById('supplyFrom').value, document.getElementById('supplyTill').value, '<?=$fBillingCompany?>', document.getElementById('supplier').value, document.getElementById('filterType').value);" style="width:70px;">									
																										<?php
																										foreach ($supplierFilterRecs as $suppFilterId=>$suppName) {
																											$selected = ($selSupplierId==$suppFilterId)?"selected":"";
																										?>
																										<option value="<?=$suppFilterId?>" <?=$selected?>><?=$suppName?></option>
																										<?php
																											}
																										?>
																									</select>
																								</td>
																							</TR>
																							<tr>
																								<td class="fieldName1" nowrap="true" style="padding-left:2px;padding-right:2px;">Company</td>
																								<td class="listing-item" nowrap="true" style="padding-left:2px;padding-right:2px;">
																									<select name="billingCompanyFilter" id="billingCompanyFilter" onchange="xajax_getFish('selFish', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.getElementById('supplier').value, document.getElementById('selFish').value, document.getElementById('billingCompanyFilter').value);" style="width:70px;">		
																										<?php
																											foreach ($billingCmpnyFilterRecs as $billCmpnyFilterId=>$billCmpnyName) {
																												$selected = ($fBillingCompany==$billCmpnyFilterId)?"selected":"";
																										?>
																										<option value="<?=$billCmpnyFilterId?>" <?=$selected?>><?=$billCmpnyName?></option>
																										<?php
																											}
																										?>
																									</select>
																								</td>
																							</tr>
																							<? } else {?>
																							<tr>
																								<td class="fieldName1" nowrap="true" style="padding-left:2px;padding-right:2px;">Company</td>
																								<td class="listing-item" nowrap="true" style="padding-left:2px;padding-right:2px;">
																									<select name="billingCompanyFilter" id="billingCompanyFilter" onchange="xajax_getSupplier('supplier', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.getElementById('supplier').value, document.getElementById('billingCompanyFilter').value, document.getElementById('filterType').value); xajax_getFish('selFish', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.getElementById('supplier').value, document.getElementById('selFish').value, document.getElementById('billingCompanyFilter').value);" style="width:70px;">		
																										<?php
																										foreach ($billingCmpnyFilterRecs as $billCmpnyFilterId=>$billCmpnyName) {
																											$selected = ($fBillingCompany==$billCmpnyFilterId)?"selected":"";
																										?>
																										<option value="<?=$billCmpnyFilterId?>" <?=$selected?>><?=$billCmpnyName?></option>
																										<?php
																										}
																										?>						
																									</select>
																								</td>
																							</tr>
																							<tr>
																								<TD class="fieldName1" style="padding-left:2px;padding-right:2px;">Supplier</TD>
																								<td nowrap="true" style="padding-left:2px;padding-right:2px;">
																								<select name="supplier" id="supplier" onchange="xajax_getFish('selFish', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.frmDailyCatch.supplier.value, document.getElementById('selFish').value, document.getElementById('billingCompanyFilter').value); xajax_getProcessCode('selProcesscode', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.frmDailyCatch.supplier.value, document.frmDailyCatch.selFish.value, document.getElementById('selProcesscode').value, document.getElementById('billingCompanyFilter').value);" style="width:70px;">		
																								<?php
																								 foreach ($supplierFilterRecs as $suppFilterId=>$suppName) {
																									$selected = ($selSupplierId==$suppFilterId)?"selected":"";
																								?>
																								<option value="<?=$suppFilterId?>" <?=$selected?>><?=$suppName?></option>
																								<?php
																									}
																								?>
																									</select>
																								</td>
																							</tr>
																							<? }?>
																						</table>
																					</td>
																					<td valign="top">
																						<table>
																							<TR>
																								<td class="fieldName1" style="padding-left:5px;">Fish</td>
																								<td>
																								<select name="selFish" id="selFish" onchange="xajax_getProcessCode('selProcesscode', document.frmDailyCatch.supplyFrom.value, document.frmDailyCatch.supplyTill.value, document.frmDailyCatch.supplier.value, document.frmDailyCatch.selFish.value, document.getElementById('selProcesscode').value, document.getElementById('billingCompanyFilter').value);" style="width:70px;">			
																								<?php
																								 foreach ($fishFilterRecs as $fishFilterId=>$fishName) {
																									$selected = ($selFish==$fishFilterId)?"selected":"";
																								?>
																								<option value="<?=$fishFilterId?>" <?=$selected?>><?=$fishName?></option>
																								<?php
																									}
																								?>
																									</select>
																								</td>
																							</TR>
																							<tr>
																								<td class="fieldName1" style="padding-left:5px;">Processcode</td>
																								<td>
																									<select name="selProcesscode" id="selProcesscode" style="width:70px;">
																									<?php if (!sizeof($pcFilterRecs)) {?><option value="">--Select All--</option> <?php }?>
																									<?php
																									foreach ($pcFilterRecs as $pcFilterId=>$pcName) {
																										$selected = ($selProcesscode==$pcFilterId)?"selected":"";
																									?>
																										<option value="<?=$pcFilterId?>" <?=$selected?>><?=$pcName?></option>
																									<?php
																										}
																									?>								
																									</select>
																								</td>
																							</tr>
																						</table>
																					</td>
																					<td valign="middle" style="padding-right:2px;"><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search " onclick="return validateSearchCatchEntry(document.frmDailyCatch);"></td>
																				</tr>
																				<?php
																					require("template/rbBottom.php");
																				?>
																			</td>
																		</tr>
																	</table>
																</td>
																<td>&nbsp;</td>
																<td align="left" valign="top">
																<? if($add==true){?>
																	<table width="200" border="0" cellpadding="2">
																		<tr>
																			<td>
																			<?php
																				$entryHead = "Add New RM In Challan";
																				$rbTopWidth = "";
																				require("template/rbTop.php");
																			?>
																				<table width="200" cellpadding="3" cellspacing="0" border="0">			
																					<tr>
																						<td class="fieldName1" nowrap="nowrap" height="25">Wt Challan No</td>
																						<td nowrap="nowrap">
																							<? $selWtChallan=$p["selWtChallan"];?>&nbsp;
																							<select name="selWtChallan" id="selWtChallan">
																								<option value="">-- Select --</option>
																								<?php
																								foreach($distinctCatchEntryRecords as $cer) {		
																									$catchEntryId		= $cer[0];
																									$catchEntryWeighChallanNo = stripSlash($cer[1]);
																									$ceAlphaCode		= $cer[2];
																									$ceDisplayChallanNo = $ceAlphaCode.$catchEntryWeighChallanNo;
																									$selected="";
																									if ($selWtChallan==$catchEntryId) $selected="Selected";
																								?>
																								<option value="<?=$catchEntryId?>" <?=$selected?>><?=$ceDisplayChallanNo?></option>
																								<?php
																									 }
																								?>
																							</select>
																						</td>
																						<td nowrap="nowrap">&nbsp;
																							<!--<input type="submit" name="cmdAddRawSelChallan" class="button" value="Add New Raw Material in Challan" onclick="return validateAddRawSelChallan(document.frmDailyCatch);" style="width:195px;" />-->
																						</td>
																						<td>&nbsp;</td>
																					</tr>
																					<tr>
																						<td class="listing-item" nowrap="nowrap">&nbsp;</td>
																						<td nowrap="nowrap" align="left">&nbsp;
																						<input type="submit" name="cmdAddRawSelChallan" class="button" value="Add New Raw Material in Challan" onclick="return validateAddRawSelChallan(document.frmDailyCatch);" style="width:195px;" />
																						</td>
																						<td nowrap="nowrap"></td>
																						<td>&nbsp;</td>
																					</tr>
																				</table>
																				<!--</fieldset>-->
																				<?php
																				require("template/rbBottom.php");
																				?>
																			</td>
																		</tr>
																	</table>
																	<? }?>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr> 
													<td colspan="4" height="10"></td>
												</tr>
												<tr> 
													<td colspan="4"> 
														<table cellpadding="0" cellspacing="0" align="center">
															<tr> 
																<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick=" return confirmDelete(this.form,'delId_',<?=$catchEntryRecSize?>);" ><? }?>
																	  &nbsp;
																	 <? if($add==true){?> <input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>
																	  &nbsp;<? if($print==true){?>
																	  <input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyCatch.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selSupplierId?>&selFish=<?=$selFish?>&selProcesscode=<?=$selProcesscode?>&selRecord=<?=$selRecord?>',700,600);"><? }?></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr> 
													<td colspan="3" height="5" ></td>
												</tr>
               									<?
												if($errDel!="")
												{
												?>
												<tr> 
													<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
												</tr>
												<?php
												}
												?>
												<tr> 
													<td colspan="4" align="center"> 
														<table  width="50%" border="0" cellpadding="2" cellspacing="1" align="center" id="newspaper-b1">
														<?php
														if ($catchEntryRecSize>0 ) {
															$i=0;
														?>
														<thead>
														<? if($maxpage>1){?>
														<tr  align="center" bgcolor="#f1f1f1">
															<td colspan="10" style="padding-right:10px;">
																<div align="right">
																  <?php 				 			  
																	 $nav  = '';
																	for($page=1; $page<=$maxpage; $page++) {
																		if ($page==$pageNo) {
																				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																		} else {
																				$nav.= " <a href=\"DailyCatchEntry_New.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page&supplier=$selSupplierId&selFish=$selFish&selProcesscode=$selProcesscode&selRecord=$selRecord\" class=\"link1\">$page</a> ";
																		//echo $nav;
																		}
																	}
																	if ($pageNo > 1) {
																		$page  = $pageNo - 1;
																		$prev  = " <a href=\"DailyCatchEntry_New.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page&supplier=$selSupplierId&selFish=$selFish&selProcesscode=$selProcesscode&selRecord=$selRecord\"  class=\"link1\"><<</a> ";
																	} else {
																		$prev  = '&nbsp;'; // we're on page one, don't print previous link
																	$first = '&nbsp;'; // nor the first page link
																	}
																	if ($pageNo < $maxpage) {
																		$page = $pageNo + 1;
																		$next = " <a href=\"DailyCatchEntry_New.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page&supplier=$selSupplierId&selFish=$selFish&selProcesscode=$selProcesscode&selRecord=$selRecord\"  class=\"link1\">>></a> ";
																	} else {
																		$next = '&nbsp;'; // we're on the last page, don't print next link
																		$last = '&nbsp;'; // nor the last page link
																	}
																	// print the navigation link
																	$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
																	echo $first . $prev . $nav . $next . $last . $summary; 
															  ?>
															</div>
														</td>
													</tr>
												   <? }?>
	
												  <tr align="center" bgcolor="White"> 
													<th width="5%" align="center"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">RM Lot ID</th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Weighment Challan No </th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier</th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Count</th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Grade</th>
													<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Quantity</th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count Adj </th>
													<? if($edit==true){?>
													<th width="50" class="listing-head" ></th>
													<? }?>
												  </tr>
											</thead>
											<tbody>
											<?php
											$gradeCode	=	"";
											$effectiveQty	=	"";
											$grandTotalEffectiveQty = "";
											$prevSupplierId = "";
											$prevCatchEntryDate = "";
											$prevEntryProcessCodeId = "";
											$prevEntryCountAverage = "";
											$prevCatchEntryCount  = "";
											$prevGradeCode = "";
											$prevCatchEntryWeighChallanNo = "";
											$totalGradeCtAdj = 0;			
											while ($cer=$catchEntryResultSetObj->getRow()) {
											$i++;
											$catchEntryId		=	$cer[0];
											$catchEntryWeighChallanNo =	stripSlash($cer[1]);
											$challanAlphaCode	=  $cer[18];
											$displayChallanNo 	= ($challanAlphaCode!="")?$challanAlphaCode.$catchEntryWeighChallanNo:$catchEntryWeighChallanNo;
											$catchEntryDate		=	stripSlash($cer[14]);
											$cEntryDate = "";
											if ($prevCatchEntryDate != $catchEntryDate) {
												$cEntryDate	= dateFormat($catchEntryDate);
											}
											$catchEntryFlag		=	$cer[3];
											$catchEntryCount	=	stripSlash($cer[4]);
											$gradeCode = "";
											$raWReceivedBy		=	$cer[10];
											if ($catchEntryCount==""|| $catchEntryCount==0 || $raWReceivedBy=='B' ) {					
												$gradeCode	= $grademasterObj->findGradeCode($cer[5]);
											}
											$recordDailyCatchentryId	=	$cer[6];				
											$processCode		= $processcodeObj->findProcessCode($cer[7]);
											$effectiveQty	=		$cer[8];
											$grandTotalEffectiveQty	+= $effectiveQty;
											$confirmed		=	$cer[9];
											$gradeCountWt		= 	$cer[11];
											$gradeCountReason	=	$cer[12];
											//$entrySupplierId 	=	$cer[13];
											$entrySupplierId 	=	$cer[19];
											$entrySupplierName = "";
											if ($prevSupplierId!=$entrySupplierId) {					
												$entrySupplierName	= $supplierMasterObj->getSupplierName($entrySupplierId);
											}
											$displayGradeCount	=	"";
											if ($gradeCountWt!=0) {
												$displayGradeCount = $gradeCountWt."&nbsp;(<span style=\"font-size:10px;\">".$gradeCountReason."</span>)";	
											}
											$totalGradeCtAdj	+= $gradeCountWt;
											$paidStatus 	=	$cer[15];
											$settledStatus 	=	$cer[16];
											$disabled = "";	
											if ($confirmed==1 && $reEdit==false) {
												$disabled = "disabled";
											}
											# If Re Edit true then check paid status and settled status release
											//if($confirmed==1 && $reEdit==true && $paidStatus=='Y' && $settledStatus=='Y') edited 08-01-08
											if (($paidStatus=='Y' || $settledStatus=='Y') && $confirmed==1 && $reEdit==true) {
												$disabled = "disabled";
											}	
											$entryProcessCodeId = $cer[7];
											$entryCountAverage  = $cer[17];
											$rmlotid  = $cer[20];
											$rowColor = "";
											if (($prevEntryProcessCodeId==$entryProcessCodeId && $prevEntryCountAverage== $entryCountAverage && $prevCatchEntryWeighChallanNo==$catchEntryWeighChallanNo) && $entryCountAverage!=0) {					
												$rowColor = "lightYellow";
											} else if (($prevEntryProcessCodeId==$entryProcessCodeId && $prevCatchEntryCount== $catchEntryCount && $prevCatchEntryWeighChallanNo==$catchEntryWeighChallanNo && $catchEntryCount!="") || ($prevEntryProcessCodeId==$entryProcessCodeId && $prevGradeCode==$gradeCode && $prevCatchEntryWeighChallanNo==$catchEntryWeighChallanNo && $gradeCode!="")) {
												$rowColor = "#FFFFCC";					
											} else {
												//$rowColor = "WHITE";
											}
											//These Transactions are incomplete
											//onMouseover="ShowTip('These Transactions are incomplete.');" onMouseout="UnTip();"
											$displayRowMsg = "";
											if ($catchEntryWeighChallanNo=="") $displayRowMsg = "onMouseover=\"ShowTip('These Transactions are incomplete.');\" onMouseout=\"UnTip();\"";
											?>
											<tr  bgcolor="<?=$rowColor?>" <?=$displayRowMsg?>> 
												<td align="center"><input type="checkbox" name="delId_<?=$i?>" id="delId_<?=$i?>" value="<?=$catchEntryId;?>" class="chkBox"><input type="hidden" name="dailyCatchEntryId_<?=$i?>" id="dailyCatchEntryId_<?=$i?>" value="<?=$recordDailyCatchentryId?>"></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$cEntryDate?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmlotid?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
												  <? if($catchEntryWeighChallanNo==""){?>
												  <img src="images/X_N.gif" width="20" height="20">
												  <? } else { echo $displayChallanNo;}?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$entrySupplierName?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$processCode;?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;" ><?=$catchEntryCount?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$gradeCode?></td>
												<td class="listing-item" nowrap="nowrap" align="right" style="padding-left:10px; padding-right:10px;"><?=$effectiveQty;?></td>
												<td class="listing-item" nowrap style="padding-left:7px; padding-right:7px;"><?=$displayGradeCount?></td>
												<? if($edit==true){?>
												<td class="listing-item" align="center" width="50"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$catchEntryId?>,'editId'); assignValue(this.form,'1','editSelectionChange');assignValue(this.form,<?=$recordDailyCatchentryId;?>,'dailyCatchentryId');" <?=$disabled?> ></td>
												<? }?>
											</tr>
											<?php
											$prevSupplierId=$entrySupplierId;
											$prevCatchEntryDate = $catchEntryDate;
											$prevEntryProcessCodeId = $entryProcessCodeId;
											$prevEntryCountAverage = $entryCountAverage;
											$prevCatchEntryCount  = $catchEntryCount;
											$prevGradeCode = $gradeCode;
											$prevCatchEntryWeighChallanNo = $catchEntryWeighChallanNo;
											  }
											?>	
											<tr>
												<td colspan="8" class="listing-head" align="right">Total:</td>
												<td class="listing-item" style="padding-right:10px;" nowrap="nowrap" align="right"><strong><? echo number_format($grandTotalEffectiveQty,2);?></strong></td>
												<td class="listing-item" style="padding-left:7px; padding-right:7px;" nowrap="nowrap" align="left">
													<?php
														if ($maxpage<=1) {
													?>
													<strong><? echo number_format($totalGradeCtAdj,2);?></strong>
													<?php
														}
													?>
												</td>
												<? if($edit==true){?>
												<td>&nbsp;</td>
												<? }?>
											</tr>
											<tr bgcolor="White">
												<td colspan="8" class="listing-head" align="right">Grand Total: </td>
												<td class="listing-item" style="padding-right:10px;" nowrap="nowrap" align="right"><b><? echo number_format($catchEntryTotalEffectiveQty,2);?></b></td>
												<td class="listing-item" style="padding-left:7px; padding-right:7px;" nowrap="nowrap" align="left">
													<?php
														if ($maxpage>1) {
													?>
													<b>
													<?=number_format($ceTotalGradeCountAdjWt,2);?>
													</b>
													<?
														} 
													?>
												</td>	
												<? if($edit==true){?>		
												<td>&nbsp;</td>
												<? }?>
											</tr>
											<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
											<? if($maxpage>1){?>
											<tr bgcolor="#f1f1f1">
 												<td colspan="12" style="padding-right:10px;">
													<div align="right">
													<?php 				 			  
													$nav  = '';
													for($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"DailyCatchEntry_New.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page&supplier=$selSupplierId&selFish=$selFish&selProcesscode=$selProcesscode&selRecord=$selRecord\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"DailyCatchEntry_New.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page&supplier=$selSupplierId&selFish=$selFish&selProcesscode=$selProcesscode&selRecord=$selRecord\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"DailyCatchEntry_New.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page&supplier=$selSupplierId&selFish=$selFish&selProcesscode=$selProcesscode&selRecord=$selRecord\"  class=\"link1\">>></a> ";
													} else {
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
												  ?>
												  </div>
													<input type="hidden" name="pageNo" value="<?=$pageNo?>">
											</td>
       									</tr>
										<? }?>
									</tbody>
									<?php
									} else {
									//No Records
									?>
									<tr bgcolor="white"> 
										<td colspan="12"  class="err1" height="10" align="center"> 
										<?=$msgNoRecords;?> 
										</td>
									</tr>
									<?
									}
									?>			
								</table>
							</td>
						</tr>
						<tr> 
							<td colspan="3" height="5" ></td>
						</tr>
						<tr> 
							<td colspan="4"> 
								<table cellpadding="0" cellspacing="0" align="center">
									<tr> 
										<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick=" return confirmDelete(this.form,'delId_',<?=$catchEntryRecSize?>);" ><? }?>
										  &nbsp;
											<? if($add==true){?> <input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>
										  &nbsp;<? if($print==true){?>
										  <input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyCatch.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplyTill=<?=$dateTill?>&supplier=<?=$selSupplierId?>&selFish=<?=$selFish?>&selProcesscode=<?=$selProcesscode?>&selRecord=<?=$selRecord?>',700,600);"><? }?>
										</td>
									</tr>		
								</table>
							</td>
						</tr>
						<tr> 
							<td colspan="3" height="5" ></td>
						</tr>
					</table>
					<?
						include "template/boxBR.php"
					?>
				</td>
			</tr>		
		</table>
        <!-- Form fields end   -->      
	</td>
</tr>
<?php 
	} // Display List Ends here
?>
    <tr> 
		<td height="10">
			<input type="hidden" name="addMode" id="addMode" value="<?=$addMode?>">
			<input type="hidden" name="editMode" id="editMode" value="<?=$editMode?>">
			<input type="hidden" name="enteredRMId" id="enteredRMId" value="<?=$editId;?>">
			<input type="hidden" name="dailyCatchentryId" id="dailyCatchentryId" value="<?=$dailyCatchentryId?>">
			<input type="hidden" name="editId" id="editId" value="">
			<input type="hidden" name="editChellan" id="editChellan" value="">
            <input type="hidden" name="editSelectionChange" id="editSelectionChange" value="0">
			<input type="hidden" name="entryId" id="entryId" value="<?=$lastId?>" readonly="true">		
			<input type="hidden" name="catchEntryNewId" id="catchEntryNewId" value="<?=$catchEntryNewId;?>" readonly="true">
			<input type="hidden" name="hidSelSupplierId" id="hidSelSupplierId" value="<?=$selSupplierId;?>">
			<input type="hidden" name="hidSelFish" id="hidSelFish" value="<?=$selFish;?>">
			<input type="hidden" name="hidSelProcesscode" id="hidSelProcesscode" value="<?=$selProcesscode;?>">
			<input type="hidden" name="hidSameEntryExist" id="hidSameEntryExist">
			<input type="hidden" name="hidSameCountAverage" id="hidSameCountAverage">
			<input type="hidden" name="validDate" id="validDate" >
			<input type="hidden" name="hidMode" id="hidMode" value="<?=$currentMode?>" >
			<input type="hidden" name="hidUserId" id="hidUserId" value="<?=$userId?>" readonly="true" >
			<input type="hidden" name="delArr" id="delArr" value="" readonly="true" />
			<input type="hidden" name="testF" id="testF" value="" readonly="true" />
			<input type="hidden" name="postItem" id="postItem" value="?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&pageNo=<?=$page?>&supplier=<?=$selSupplierId?>&selFish=<?=$selFish?>&selProcesscode=<?=$selProcesscode?>&selRecord=<?=$selRecord?>" readonly="true" />
			<?php
				# Reset search data
				if (!$displayListEnabled) {
			?>
			<input type="hidden" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" readonly>
			<input type="hidden" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" readonly>
			<input type="hidden" id="selRecord" name="selRecord" size="8"  value="<?=$selRecord?>" readonly>
			<input type="hidden" id="filterType" name="filterType" size="8"  value="<?=$filterType?>" readonly>
			<input type="hidden" id="supplier" name="supplier" size="8"  value="<?=$selSupplierId?>" readonly>
			<input type="hidden" id="billingCompanyFilter" name="billingCompanyFilter" size="8"  value="<?=$fBillingCompany?>" readonly>
			<input type="hidden" id="selFish" name="selFish" size="8"  value="<?=$selFish?>" readonly>
			<input type="hidden" id="selProcesscode" name="selProcesscode" size="8"  value="<?=$selProcesscode?>" readonly>
			<input type="hidden" name="pageNo" value="<?=$pageNo?>">
			<?php
				 }
			?>
			<input type="hidden" id='allQualityhide' value='<?echo $allQualityhide?>' name='allQualityhide'/>
		</td>
    </tr>
  </table>
</TD>
</tr>
</table>
  <?php
	// if($editMode==true){
  ?>
  <!--<script type="text/javascript" language="javascript">
	Hide on 23-12-09
	  actualWt();
	  effectiveWt();
  </script>-->
  <?php
	// } else if($addMode==true) {
  ?>
 <!-- <script type="text/javascript" language="javascript">
	  actualWt();
  </script>-->
  <?php
	// }
  ?>
  
  <?php
	 if($addMode==true||$editMode==true){
  ?>
  <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
<? }?> 
 <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
<?php 
	//if (!$addMode || !$editMode) {
?>
<!--
<script language="JavaScript" type="text/javascript">
window.onLoad = callOnLoad();
function callOnLoad()
{	
	xajax_getSupplier('supplier','<?=$dateFrom?>', '<?=$dateTill?>', '<?=$selSupplierId?>', '<?=$fBillingCompany?>', '<?=$filterType?>');
	xajax_getFish('selFish', '<?=$dateFrom?>', '<?=$dateTill?>', '<?=$selSupplierId?>', '<?=$selFish?>', '<?=$fBillingCompany?>');
	xajax_getProcessCode('selProcesscode', '<?=$dateFrom?>', '<?=$dateTill?>', '<?=$selSupplierId?>', '<?=$selFish?>', '<?=$selProcesscode?>', '<?=$fBillingCompany?>');
	xajax_getBillingCompany('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$fBillingCompany?>', '<?=$selSupplierId?>', '<?=$filterType?>');
}
</script>-->
<? //}?>
<?php 
	//Checking same Entries Exist
	if ($addMode) {
?>
	<script language="JavaScript" type="text/javascript">
		<?php 
			echo $onChangeCheck;
			echo $onChanageCountAverage;
		?>
	</script>

	<?php
		if ($receivedBy=='C' || $receivedBy=='B') {		
	?>
		<script language="JavaScript" type="text/javascript">
		//On Change Check
		document.getElementById("count").onchange = function() {
			<?php 
				echo $onChangeCheck;
				echo $onChanageCountAverage;
			?>
		};
		</script>
	<?php 
		}
	}
	?>
	<?php
		if ($addMode || $editMode) {
	?>
	<script language="Javascript" >
		<?=$gWtVal?>;
		<?=$bWtVal?>;
		<?=$idVal?>;		
		<?php if ($entryOption=='B') { ?>
			colWiseTot();
		<? }?>
	</script>
	<?php
		}
	?>
	
	
	<script>
function lotIdAvlCheck()
	{
		var lotId_aval = document.getElementById('lotIdAvailable');
		// alert(procure_aval.checked);
		if(lotId_aval.checked == true)
		{
			jQuery('#autoUpdate').hide();
			jQuery('#rm_lot_id').val('');
			jQuery('#lotUnit').val('');
			jQuery('#supplyLotChallanNo').val('');
			jQuery('#billingCompanyLot').val('');
			jQuery('#payment').val('');
			jQuery('#pondName').val('');
			jQuery('#count_code').val('');
			jQuery('#weighChallanNo').val('');
			jQuery('#entryOption').val("N");
			jQuery('#countRow').hide();
			//jQuery('#countRowDesp').hide();
			jQuery('#countAvg').hide();	
			
			jQuery('#autoUpdate2').show();
			xajax_getCountCode('', '');
			
			
		}
		else
		{
			jQuery('#autoUpdate2').hide();
			jQuery('#autoUpdate').show();
			jQuery('#unit').val('');
			jQuery('#landingCenter').val('');
			jQuery('#mainSupplier').val('');
			jQuery('#subSupplier').val('');
			jQuery('#vechicleNo').val('');
			jQuery('#supplyChallanNo').val('');
			jQuery('#weighChallanNo').val('');
			jQuery('#entryOption').val("N");
			jQuery('#countRow').hide();
			//jQuery('#countRowDesp').hide();
			jQuery('#countAvg').hide();	
			xajax_getCountCode('', '');
			
			
		}
		// alert(contentDis);
	}
	// jQuery(document).ready(function(){
	// alert("hii");
		// var lotIdAvailable = '<?php echo $lotIdAvailable;?>';
		
		// if(lotIdAvailable == '1')
		// {
			// document.getElementById('lotIdAvailable').checked = true;
			// jQuery('#autoUpdate').hide();
			// jQuery('#autoUpdate2').show();
			
		// }
		
	
	// });

	</script>
	
	
	<div id="example-popup" class="popup">
    <div class="popup-body"><span class="popup-exit"></span>
        <div class="popup-content" id="popupcontent"></div>
    </div>
</div>
<div class="popup-overlay"></div>
<script type='text/javascript'>//<![CDATA[ 
$(window).load(function(){
jQuery(document).ready(function ($) {

	$('#rm_lot_id').change(function(){
		getSuppierDetails($(this).val());
	});
	// $('.in_seal_class').keyup(function(){
		// checkSealNos();
	// });
    $('[data-popup-target]').click(function () {
        $('html').addClass('overlay');
        var activePopup = $(this).attr('data-popup-target');
        $(activePopup).addClass('visible');

    });

    $(document).keyup(function (e) {
        if (e.keyCode == 27 && $('html').hasClass('overlay')) {
            clearPopup();
        }
    });

    $('.popup-exit').click(function () {
        clearPopup();
    });

    $('.popup-overlay').click(function () {
        clearPopup();
    });

   
});
});//]]>


 


 function clearPopup() {
        $('.popup.visible').addClass('transitioning').removeClass('visible');
        $('html').removeClass('overlay');

        setTimeout(function () {
            $('.popup').removeClass('transitioning');
        }, 300);
    }
function getSuppierDetails(rm_lot_id)
{
	if(rm_lot_id != '')
	{
		xajax_checkProcurementStatus(rm_lot_id);
	}
}
function assignSupplierDetails(supplierId,pondId,unitId,companyId,supplierChallanNo,makePayment)
{
	
	// alert(makePayment);
	if(makePayment == 1) {
		document.getElementById('make_payment').checked = true;
	}
	else
	{
		document.getElementById('make_payment').checked = false;
	}
	var rm_lot_id = jQuery('#rm_lot_id').val();
	jQuery('#pondName').val(pondId);
	jQuery('#lotUnit').val(unitId);
	
	jQuery('#supplyLotChallanNo').val(supplierChallanNo);
	jQuery('#payment').val(supplierId);
	//var supplierOptions = "<option value='"+supplierId+"'>"+supplierName+"</option>";
	//jQuery('#payment').html(supplierOptions);
	jQuery('#billingCompany').val(companyId);
	
	//xajax_pondNames(supplierId,rm_lot_id,'');
	
	 xajax_getCountCode(pondId,rm_lot_id,supplierId,'');
	 xajax_filterGrade(document.getElementById('processCode').value, ''); 
	 xajax_getBasketwt(document.getElementById('processCode').value); 
	 //jQuery('#lotUnit').attr("disabled", true); 
	clearPopup();
}
function assignSupplierDetailsWeightment(weightmentId,supplierId,pondId,unitId,companyId,supplierChallanNo,makePayment,speciesid,processcodeid,countcode)
{
	
	 //alert(processcodeid);
	if(makePayment == 1) {
		document.getElementById('make_payment').checked = true;
	}
	else
	{
		document.getElementById('make_payment').checked = false;
	}
	var rm_lot_id = jQuery('#rm_lot_id').val();
	
	//xajax_pondNames(supplierId,rm_lot_id,'');
	
	//var supplierName = jQuery('.supplier_name_'+supplierId).html();
	//alert(supplierId);
	// alert(supplierId+'----'+supplierName+'-----'+unitId+'------'+supplierChallanNo);
	//alert(pondId);
	
	
	jQuery('#weightmentId').val(weightmentId);
	jQuery('#pondName').val(pondId);
	jQuery('#lotUnit').val(unitId);
	
	jQuery('#supplyLotChallanNo').val(supplierChallanNo);
	jQuery('#payment').val(supplierId);
	//var supplierOptions = "<option value='"+supplierId+"'>"+supplierName+"</option>";
	//jQuery('#payment').html(supplierOptions);
	//alert(companyId);
	//jQuery('#billingCompanyLot').val(companyId);
	jQuery('#processCode').val(processcodeid);
	jQuery('#fish').val(speciesid);
	jQuery('#count_code').val(countcode);
	//xajax_pondNames(supplierId,rm_lot_id,'');
	
	xajax_getCountCodeweightment(weightmentId,pondId,rm_lot_id,supplierId,'');
	xajax_filterGrade(processcodeid, ''); 
	xajax_getBasketwt(processcodeid); 
	
	 //jQuery('#hidReceived').val('B');
	//$("#countRow").css("display","block");
	 //jQuery('#lotUnit').attr("disabled", true); 
	clearPopup();
}
function assignSupplierDetails_vel(supplierId,unitId,supplierChallanNo,makePayment)
{
	// alert(makePayment);
	if(makePayment == 1) {
		document.getElementById('make_payment').checked = true;
	}
	else
	{
		document.getElementById('make_payment').checked = false;
	}
	var rm_lot_id = jQuery('#rm_lot_id').val();
	var supplierName = jQuery('.supplier_name_'+supplierId).html();
	//alert(supplierId);
	// alert(supplierId+'----'+supplierName+'-----'+unitId+'------'+supplierChallanNo);
	jQuery('#lotUnit').val(unitId);
	//jQuery('#lotUnit').prop("disabled", true); 
	jQuery('#supplyLotChallanNo').val(supplierChallanNo);
	var supplierOptions = "<option value='"+supplierId+"'>"+supplierName+"</option>";
	jQuery('#payment').html(supplierOptions);
	xajax_pondNames(supplierId,rm_lot_id,'');
	// xajax_getCountCode(document.getElementById('pondName').value,document.getElementById('rm_lot_id').value,'');
	clearPopup();
}


</script>
</form>

<script>
function repeatRmLotId()
//function repeatRmLotId(rm_lot_id)
{
var rm_lot_id=document.getElementById('rm_lot_id').value;

		if(rm_lot_id != '')
		{	
		//alert(rm_lot_id);
		jQuery('#countAvg').hide();
		xajax_checkProcurementStatus(rm_lot_id);
			//xajax_getSuppierDetails(rm_lot_id);
			// $('html').addClass('overlay');
			 ////var activePopup = $(this).attr('data-popup-target');
			 // $('#example-popup').addClass('visible');
			 //alert("bye");
		}
}		
	</script>
<SCRIPT LANGUAGE="JavaScript">
function addAllQuality()
{

		addQuality('tblQuality','','','','','','','');
}		
			</SCRIPT>

<?php
if($editMode	==	true)
	{
	?>
		<script type='text/javascript'>
		effectiveWt();
		var lotIdAvailable = '<?php echo $lotIdAvailable;?>';
		
		if(lotIdAvailable == '1')
		{
			document.getElementById('lotIdAvailable').checked = true;
			jQuery('#autoUpdate').hide();
			jQuery('#autoUpdate2').show();
			//jQuery('#countAvg').hide();
			
		}
		jQuery('#lotUnit').attr("disabled", false); 
		jQuery('#billingCompanyLot').attr("disabled", false);
		jQuery('#payment').attr("disabled", false); 
		jQuery('#pondName').attr("disabled", false);
	
		</script>
	<?php
	}
?>



			
<?php
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
$outputContents = ob_get_contents(); 
ob_end_clean();
echo $outputContents;
?>