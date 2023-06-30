<?php
	require("include/include.php");
	require_once("lib/dailyfrozenpacking_ajax.php");
	ob_start();

	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= false;
	$allocateMode	= false;
	$isSearched		= false;
	$entrySection 		= true;
	

	$selection = "?frozenPackingFrom=".$p["frozenPackingFrom"]."&frozenPackingTill=".$p["frozenPackingTill"]."&pageNo=".$p["pageNo"];
	
	
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false;

	$filterProcessCode = "";
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	if ($accesscontrolObj->canReEdit()) $reEdit=true;
	
	# Checking day's valid pc enabled/disabled
	$validPCEnabled = $manageconfirmObj->pkgValidPCEnabled();
	# MC Packing Conversion type, AC - Auto convert/ MC - Manually Convert
	$LSToMCConversionType = $manageconfirmObj->getLS2MCConversionType();
	//----------------------------------------------------------	
	
	

	# Reset Data
	if ($p["selQuickEntryList"]!="") $selQuickEntryList = $p["selQuickEntryList"];
	if($p["company"]!="") 		 $company 		= $p["company"];
	if ($p["unit"]!="") 		 $unit 		= $p["unit"];
	if ($p["selectDate"]!="") $selDate	=	$p["selectDate"];
	if( $selDate!="" && $company!="" && $unit!="" ) 
	$rmLotIdsVal  = $dailyfrozenpackingObj->getLotIdOnDate(mysqlDateFormat($selDate),$company,$unit);
	if ($p["rm_lot_id"]!="") $rmlot= $p["rm_lot_id"];
	
		
	
	$entrySel = ($p["entrySel"]=="")?$p["hidEntrySel"]:$p["entrySel"];
	if ($entrySel) $addMode = true;

	$displayQE	= $p["displayQE"]; // DMCLS-Both MC&LS, DMC = MC, DLS = LS;
	
	# Add New
	if ($p["cmdAddNew"]!="" ) {
		$addMode	=	true;
		
		
	}

	
	#Cancel 	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;
		$editMode	=	false;
		$mainId 	=	$p["mainId"];
		$entryId	=	$p["entryId"];
		
		if ($p['editMode']=="") {
			$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRec($entryId);
					
			$frozenPackingEntryRecDel = $dailyfrozenpackingObj->deletePackingEntryRec($entryId);
			#Check Record Exists
			$exisitingRecords = $dailyfrozenpackingObj->checkRecordsExist($mainId);
			if (sizeof($exisitingRecords)==0) {
				$dailyFrozenPackingRecDel = $dailyfrozenpackingObj->deleteDailyFrozenPackingMainRec($mainId);
			}
		}
		$mainId 	= "";
		$p["mainId"]	= "";
		$entryId	= "";
		$p["entryId"] 	= "";
		$entrySel 	= "";
		$p["hidEntrySel"] = "";
		$p["entrySel"]	= "";
		$p["editId"] = "";
		$editId = "";
		$editCriteria	= "";
		$p["editCriteria"] = "";
	}

	#New Entry
	if ($p["cmdAdd"]!="" || $p["cmdAddSameLotEntry"]!="" || $p["cmdSaveAndAddNew"]!="" || $p["cmdAddFishInSameICAndMC"]!="" || $p["cmdAddFishInSameMC"]!="" || $p["cmdAddProcessCodeInSameICAndMC"]!="" || $p["cmdSaveAndQE"]!="") {
		 
		$selectDate		= mysqlDateFormat($p["selectDate"]);
		$rm_lot_id = $p['rm_lot_id'];
		$company			=	$p["company"];
		$unit			=	$p["unit"];
		$processorId		=	$p["processor"];		
		
		$fishId			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$freezingStage		=	$p["freezingStage"];
		$eUCode			=	$p["eUCode"];
		//$brand			=	$p["brand"];
		$sBrand		= explode("_",$p["brand"]);
		$brand		= $sBrand[0];
		$brandFrom	= $sBrand[1];
		$frznCode			=$p["frznCode"];
		$frozenCode		=	$p["frozenCode"];
		$mCPacking		=	$p["mCPacking"];
		$lotId			= ($p["lotId"]=="")?0:$p["lotId"];
		$exportLotId		= $p["exportLotId"];
		$selQuality 		= $p["selQuality"];
		$customer		= $p["customer"];
		if ($frznCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frznCode);
		$glazeId=$frozenpackingObj->frznPkgglaze($frznCode);
		$glaze=$glazeObj->findGlazePercentage($glazeId);
		$Wt=$filledWt-($filledWt*$glaze/100);
		//echo "hii".$frznCode."sss".$filledWt;
		//die();

		# QEL Starts Here
		$selQuickEntryList 	= $p["selQuickEntryList"];
		$displayQE		= $p["displayQE"]; // DMCLS-Both MC&LS, DMC = MC, DLS = LS;
		 
		if($rm_lot_id=="")
		{
		if ($p["mainId"]=="" && $p["entryId"]=="" && list($mId,$eId) = $dailyfrozenpackingObj->checkBlankRecord($userId)) {		
			list($mId,$eId)	=	$dailyfrozenpackingObj->checkBlankRecord($userId);
			$mainId 	=	$mId; 
			$entryId 	= 	$eId;
		} else {
			if ($p["mainId"]=="" && $p["entryId"]=="") {
				
				//$available_qty = $p['available_qty'];
				//$tempMainTableRecIns = $dailyfrozenpackingObj->addTempDataMainTable($userId,$rm_lot_id,$available_qty);
				$tempMainTableRecIns = $dailyfrozenpackingObj->addTempDataMainTable($userId);
				if ($tempMainTableRecIns!="") {
					$mainId	= $databaseConnect->getLastInsertedId();
				}
								
				$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTable($mainId);
				if ($tempEntryTableRecIns!="") {
					$entryId = $databaseConnect->getLastInsertedId();
				}				
				
			} else {
				$mainId 	=	$p["mainId"];
				$entryId	=	$p["entryId"];
			}
		}

		if (list($mId,$eId) = $dailyfrozenpackingObj->checkBlankRecord($userId) && $p["mainId"]=="" && $p["entryId"]=="") {
			# Delete grade Rec
			$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRec($entryId);
		}		
		
		

		if ($selQuickEntryList) {
			# Check Entry Blank Rec Exist
			list($dailyFrozenPackingEntryId) = $dailyfrozenpackingObj->getBalnkDFPERec($mainId);
			if ($dailyFrozenPackingEntryId) {
				# Delete Entry Rec
				$frozenPackingEntryRecDel = $dailyfrozenpackingObj->deletePackingEntryRec($dailyFrozenPackingEntryId);
				# Delete grade Rec
				$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRec($dailyFrozenPackingEntryId);
			}

			# Get QE REC
			list($qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom) = $dailyfrozenpackingObj->getQERec($selQuickEntryList);

			# If not selected then update the Selected MC Packing			
			$qeMCPackingId = ($qeMCPackingId!=0)?$qeMCPackingId:$p["qeMCPacking"];
			
			$gradeRowCount	= $p["hidGradeRowCount"];
			$pcRowCount    = $p["hidPCRowCount"];
			if ($company && $unit && $processorId && $mainId) {
				
				$updatePackingMainRec =	$dailyfrozenpackingObj->updatePackingMainRec($selectDate,$company,$unit, $processorId, $mainId);
				//$available_qty = $p['available_qty'];
				//$updatePackingMainRec =	$dailyfrozenpackingObj->updatePackingMainRec($selectDate, $unit, $processorId, $mainId,$rm_lot_id,$available_qty);
			}
			
			if ($qeMCPackingId!=0) {
				$mcpackingRec	= $mcpackingObj->find($qeMCPackingId);
				$numPacks	= $mcpackingRec[2];
			}
			for ($i=1; $i<=$pcRowCount; $i++) {				
				$hidFishId 	 	= $p["hidFishId_".$i];
				$hidProcesscodeId 	= $p["hidProcesscodeId_".$i];
				$packEntered		= $p["packEntered_".$i];
				$processType     = $p["processType_".$i];
				# Insert into t_dailyfrozenpacking_entry
				$lEntryId = "";
				if ($hidFishId!="" && $hidProcesscodeId!="" && $packEntered!="") {
					$dfpeRecIns = $dailyfrozenpackingObj->addDailyFrozenPackingEntry($mainId, $hidFishId, $hidProcesscodeId, $qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom, $selQuickEntryList,$processType); 
					# Last Entry Id
					if ($dfpeRecIns) $lEntryId = $databaseConnect->getLastInsertedId();
				}

				for ($j=1; $j<=$gradeRowCount; $j++) {
					$gradeId = $p["gradeId_".$j];

					$numMC	= ($p["numMC_".$i."_".$j]=="")?0:$p["numMC_".$i."_".$j];
					$numLooseSlab 	= ($p["numLooseSlab_".$i."_".$j]=="")?0:$p["numLooseSlab_".$i."_".$j]; 

					if ($displayQE!='DMCLS') {
						if (($numMC==0 || $numMC!=0) && $numLooseSlab!=0 && $LSToMCConversionType=="AC") {
							$totalMcPacks = floor($numLooseSlab/$numPacks);
							$numMC	+= $totalMcPacks;
							$numLSlab = $numLooseSlab%$numPacks;
						} else {
							$numMC	= ($p["numMC_".$i."_".$j]=="")?0:$p["numMC_".$i."_".$j];
							$numLSlab 	= ($p["numLooseSlab_".$i."_".$j]=="")?0:$p["numLooseSlab_".$i."_".$j]; 
						}
					} else $numLSlab = $numLooseSlab;

					if( ($dailyfrozenpackingObj->processCodeHasGrade($hidProcesscodeId, $gradeId) && $lEntryId ) || ($dailyfrozenpackingObj->secondaryProcessCodeHasGrade($hidProcesscodeId, $gradeId) && $lEntryId ))
					{
							if (($gradeId>0) && ($numMC>0 )) 
							{
								$netWt=$numMC*$Wt;	
								$gradeRecIns = $dailyfrozenpackingObj->addFrozenPackingGrade($lEntryId, $gradeId, $numMC, $numLSlab, $LSToMCConversionType,$netWt);
							}
					}
				} // Grade Row count loop ends here
			} // Process Code loop ends here	
			if ($dfpeRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddDailyFrozenPacking);
				if ($p["cmdAdd"]!="") {
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$addMode = false;
					$selQuickEntryList = "";
					$p["selQuickEntryList"] = "";
					$sessObj->createSession("nextPage",$url_afterAddDailyFrozenPacking.$selection);
				} else if ($p["cmdSaveAndAddNew"]!="") {
					$addMode=true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					
					$p["selectDate"] = "";
					$selDate	= "";
					$p["unit"]	= "";
					$unit		= "";
					$processor	= "";
					$p["processor"] = ""; 
					$selQuickEntryList = "";
					$p["selQuickEntryList"] = "";
				} else if ($p["cmdSaveAndQE"]!="") {
					$addMode=true;
					$selQuickEntryList = "";
					$p["selQuickEntryList"] = "";
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["mCPacking"]		=	"";
					$mCPacking 		= 	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				}
			} else {
				$addMode	= true;
				$err		= $msg_failAddDailyFrozenPacking;
			}
			$dfpeRecIns = false;
		}
		# Sel QE List Ends Here
		
		if ($unit!="" && $processorId!="" && $fishId!="" &&  $processCode!="" && $frozenCode!="" && $selQuickEntryList=="") {
			$updateFrozenPackingEntryRec =	$dailyfrozenpackingObj->updateDailyFrozenPackingEntry($fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $exportLotId, $entryId, $lotId, $selQuality, null, $brandFrom, $customer);
			$rm_lot_id = $p['rm_lot_id'];
			$available_qty = $p['available_qty'];
			$updatePackingMainRec =	$dailyfrozenpackingObj->updatePackingMainRec($selectDate, $unit, $processorId, $mainId,$rm_lot_id,$available_qty);
			
			if ($updatePackingMainRec && $updateFrozenPackingEntryRec) {
				if ($p['editMode']=="1") $sessObj->createSession("displayMsg", $msg_succUpdateDailyFrozenPacking);
				else $sessObj->createSession("displayMsg", $msg_succAddDailyFrozenPacking);
				
				if ($p["cmdAddSameLotEntry"]!="") {
					
					$mainId = $p["mainId"];
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTable($mainId);
					if ($tempEntryTableRecIns!="") {		
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode = true;
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["mCPacking"]		=	"";
					$mCPacking 		= 	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				} else if ($p["cmdAdd"]!="") {
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$addMode = false;
				} else if ($p["cmdSaveAndAddNew"]!="") {
					$addMode=true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					
					$p["selectDate"]	=	"";
					$selDate		= 	"";
					$p["unit"]		=	"";
					$unit			= 	"";
					$p["lotId"]		=	"";
					$lotId			= 	"";
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["mCPacking"]		=	"";
					$mCPacking 		= 	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";

				} else if ($p["cmdAddFishInSameICAndMC"]!="") {
				//Save & Add New Fish in same IC & MC Pkg (Same Date – Unit – EUCode – Brand – FrozenCode - MC Pkg)
				
					$mainId = $p["mainId"];
				
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTable($mainId);
					if ($tempEntryTableRecIns!="") {
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode = true;

					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				} else if ($p["cmdAddFishInSameMC"]!="") {
				//Save & Add New Fish in same MC Pkg (Same Date – Unit – EUCode - Brand)
				
					$mainId = $p["mainId"];
				
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTable($mainId);
					if ($tempEntryTableRecIns!="") {		
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode = true;
						
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				} else if ($p["cmdAddProcessCodeInSameICAndMC"]!="") {
					//Save & Add New Process Code of Fish in same IC(Frozen Code) & MC Pkg (Same Fish, FrozenCode, MC Pkg)
		
					$mainId = $p["mainId"];
				
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTable($mainId);
					if ($tempEntryTableRecIns!="") {		
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode=true;						
						
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				}
				
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDailyFrozenPacking;
			}
			$dailyFrozenPackingRecIns	=	false;
		}
		
	}
	elseif($rm_lot_id!="")
	{
		if ($p["mainId"]=="" && $p["entryId"]=="" && list($mId,$eId) = $dailyfrozenpackingObj->checkBlankRecordRmlotid($userId)) {		
			list($mId,$eId)	=	$dailyfrozenpackingObj->checkBlankRecordRmlotid($userId);
			$mainId 	=	$mId; 
			$entryId 	= 	$eId;
		} else {
			if ($p["mainId"]=="" && $p["entryId"]=="") 
			{
				//$available_qty = $p['available_qty'];
				//$tempMainTableRecIns = $dailyfrozenpackingObj->addTempDataMainTable($userId,$rm_lot_id,$available_qty);
				$tempMainTableRecIns = $dailyfrozenpackingObj->addTempDataMainTableRmlotid($userId,$rm_lot_id);
				if ($tempMainTableRecIns!="") {
					$mainId	= $databaseConnect->getLastInsertedId();
				}
								
				$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTableRmlotid($mainId);
				if ($tempEntryTableRecIns!="") {
					$entryId = $databaseConnect->getLastInsertedId();
				}				
				
			} else {
				$mainId 	=	$p["mainId"];
				$entryId	=	$p["entryId"];
			}
		}

		if (list($mId,$eId) = $dailyfrozenpackingObj->checkBlankRecordRmlotid($userId) && $p["mainId"]=="" && $p["entryId"]=="") {
			# Delete grade Rec
			$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRecRmlotid($entryId);
		}		
		
		

		if ($selQuickEntryList) {
			//echo "hii";
			//die();
			# Check Entry Blank Rec Exist
			list($dailyFrozenPackingEntryId) = $dailyfrozenpackingObj->getBalnkDFPERecRmlotid($mainId);
			if ($dailyFrozenPackingEntryId) {
				# Delete Entry Rec
				$frozenPackingEntryRecDel = $dailyfrozenpackingObj->deletePackingEntryRecRmlotid($dailyFrozenPackingEntryId);
				# Delete grade Rec
				$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRecRmlotid($dailyFrozenPackingEntryId);
			}

			# Get QE REC
			list($qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom) = $dailyfrozenpackingObj->getQERec($selQuickEntryList);

			# If not selected then update the Selected MC Packing			
			$qeMCPackingId = ($qeMCPackingId!=0)?$qeMCPackingId:$p["qeMCPacking"];
			
			$gradeRowCount	= $p["hidGradeRowCount"];
			$pcRowCount    = $p["hidPCRowCount"];
			if ($company && $unit && $processorId && $mainId) {
				$rm_lot_id = $p['rm_lot_id'];
				$updatePackingMainRec =	$dailyfrozenpackingObj->updatePackingMainRecRmlotid($selectDate,$company,$unit, $processorId, $mainId,$rm_lot_id);
				//$available_qty = $p['available_qty'];
				//$updatePackingMainRec =	$dailyfrozenpackingObj->updatePackingMainRec($selectDate, $unit, $processorId, $mainId,$rm_lot_id,$available_qty);
			}
			
			if ($qeMCPackingId!=0) {
				$mcpackingRec	= $mcpackingObj->find($qeMCPackingId);
				$numPacks	= $mcpackingRec[2];
			}
			for ($i=1; $i<=$pcRowCount; $i++) {				
				$hidFishId 	 	= $p["hidFishId_".$i];
				$hidProcesscodeId 	= $p["hidProcesscodeId_".$i];
				$packEntered		= $p["packEntered_".$i];
				$processType      =$p["processType_".$i];
				# Insert into t_dailyfrozenpacking_entry
				$lEntryId = "";
				if ($hidFishId!="" && $hidProcesscodeId!="" && $packEntered!="") {
					$dfpeRecIns = $dailyfrozenpackingObj->addDailyFrozenPackingEntryRmlotid($mainId, $hidFishId, $hidProcesscodeId, $qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom, $selQuickEntryList,$processType); 
					# Last Entry Id
					if ($dfpeRecIns) $lEntryId = $databaseConnect->getLastInsertedId();
				}

				for ($j=1; $j<=$gradeRowCount; $j++) {
					$gradeId = $p["gradeId_".$j];

					$numMC	= ($p["numMC_".$i."_".$j]=="")?0:$p["numMC_".$i."_".$j];
					$numLooseSlab 	= ($p["numLooseSlab_".$i."_".$j]=="")?0:$p["numLooseSlab_".$i."_".$j]; 

					if ($displayQE!='DMCLS') {
						if (($numMC==0 || $numMC!=0) && $numLooseSlab!=0 && $LSToMCConversionType=="AC") {
							$totalMcPacks = floor($numLooseSlab/$numPacks);
							$numMC	+= $totalMcPacks;
							$numLSlab = $numLooseSlab%$numPacks;
						} else {
							$numMC	= ($p["numMC_".$i."_".$j]=="")?0:$p["numMC_".$i."_".$j];
							$numLSlab 	= ($p["numLooseSlab_".$i."_".$j]=="")?0:$p["numLooseSlab_".$i."_".$j]; 
						}
					} else $numLSlab = $numLooseSlab;

					if(($dailyfrozenpackingObj->processCodeHasGrade($hidProcesscodeId, $gradeId) && $lEntryId) || ($dailyfrozenpackingObj->secondaryProcessCodeHasGrade($hidProcesscodeId, $gradeId) && $lEntryId )) {
							if (($gradeId>0) && ($numMC>0 )) {
								$netWt=$numMC*$Wt+$numLSlab*$Wt;	
								$gradeRecIns = $dailyfrozenpackingObj->addFrozenPackingGradeRmlotid($lEntryId, $gradeId, $numMC, $numLSlab, $LSToMCConversionType,$netWt);
							}
					}
				} // Grade Row count loop ends here
			} // Process Code loop ends here	
			if ($dfpeRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddDailyFrozenPacking);
				if ($p["cmdAdd"]!="") {
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$addMode = false;
					$selQuickEntryList = "";
					$p["selQuickEntryList"] = "";
					$sessObj->createSession("nextPage",$url_afterAddDailyFrozenPacking.$selection);
				} else if ($p["cmdSaveAndAddNew"]!="") {
					$addMode=true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					
					$p["selectDate"] = "";
					$selDate	= "";
					$p["unit"]	= "";
					$unit		= "";
					$processor	= "";
					$p["processor"] = ""; 
					$selQuickEntryList = "";
					$p["selQuickEntryList"] = "";
				} else if ($p["cmdSaveAndQE"]!="") {
					$addMode=true;
					$selQuickEntryList = "";
					$p["selQuickEntryList"] = "";
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["mCPacking"]		=	"";
					$mCPacking 		= 	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				}
			} else {
				$addMode	= true;
				$err		= $msg_failAddDailyFrozenPacking;
			}
			$dfpeRecIns = false;
		}
		//die();
		# Sel QE List Ends Here
		
		if ($unit!="" && $processorId!="" && $fishId!="" &&  $processCode!="" && $frozenCode!="" && $selQuickEntryList=="") {
			$updateFrozenPackingEntryRec =	$dailyfrozenpackingObj->updateDailyFrozenPackingEntryRmlotid($fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $exportLotId, $entryId, $lotId, $selQuality, null, $brandFrom, $customer);
			$rm_lot_id = $p['rm_lot_id'];
			//$available_qty = $p['available_qty'];
			$updatePackingMainRec =	$dailyfrozenpackingObj->updatePackingMainRecRmlotid($selectDate, $unit, $processorId, $mainId,$rm_lot_id);
			
			if ($updatePackingMainRec && $updateFrozenPackingEntryRec) {
				if ($p['editMode']=="1") $sessObj->createSession("displayMsg", $msg_succUpdateDailyFrozenPacking);
				else $sessObj->createSession("displayMsg", $msg_succAddDailyFrozenPacking);
				
				if ($p["cmdAddSameLotEntry"]!="") {
					
					$mainId = $p["mainId"];
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTableRmlotid($mainId);
					if ($tempEntryTableRecIns!="") {		
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode = true;
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["mCPacking"]		=	"";
					$mCPacking 		= 	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				} else if ($p["cmdAdd"]!="") {
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$addMode = false;
				} else if ($p["cmdSaveAndAddNew"]!="") {
					$addMode=true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					
					$p["selectDate"]	=	"";
					$selDate		= 	"";
					$p["unit"]		=	"";
					$unit			= 	"";
					$p["lotId"]		=	"";
					$lotId			= 	"";
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["mCPacking"]		=	"";
					$mCPacking 		= 	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";

				} else if ($p["cmdAddFishInSameICAndMC"]!="") {
				//Save & Add New Fish in same IC & MC Pkg (Same Date – Unit – EUCode – Brand – FrozenCode - MC Pkg)
				
					$mainId = $p["mainId"];
				
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTableRmlotid($mainId);
					if ($tempEntryTableRecIns!="") {
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode = true;

					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				} else if ($p["cmdAddFishInSameMC"]!="") {
				//Save & Add New Fish in same MC Pkg (Same Date – Unit – EUCode - Brand)
				
					$mainId = $p["mainId"];
				
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTableRmlotid($mainId);
					if ($tempEntryTableRecIns!="") {		
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode = true;
						
					$fishId			= 	"";
					$p["fish"] 		= 	"";
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["frozenCode"]	=	"";
					$frozenCode		=	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				} else if ($p["cmdAddProcessCodeInSameICAndMC"]!="") {
					//Save & Add New Process Code of Fish in same IC(Frozen Code) & MC Pkg (Same Fish, FrozenCode, MC Pkg)
		
					$mainId = $p["mainId"];
				
					$tempEntryTableRecIns = $dailyfrozenpackingObj->addTempDataEntryTableRmlotid($mainId);
					if ($tempEntryTableRecIns!="") {		
						$entryId = $databaseConnect->getLastInsertedId();
					}
					$addMode=true;						
						
					$processId		= 	"";
					$p["processCode"] 	= 	"";
					$p["freezingStage"]	=	"";
					$freezingStage		=	"";
					$p["eUCode"]		=	"";
					$eUCode			=	"";
					$p["brand"]		=	"";
					$brand			=	"";
					$p["exportLotId"]	=	"";
					$exportLotId 		=	"";
				}
				
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDailyFrozenPacking;
			}
			$dailyFrozenPackingRecIns	=	false;
		}
	}	
	}
	
	# Edit and Delete section
	if ($p["cmdEntryDelete"]!="") {
		$prodnRowCount 	= $p["hidProdnRowCount"];	
		$completeEntriesDeleted = false;
		$cnt = 0;
		for ($i=1; $i<=$prodnRowCount; $i++) {
			$dailyFrozenPackingEntryId  = $p["delId_".$i];
			$dailyFrozenPackingMainId = $p["dFrznPkgMainId_".$i];
			if ($dailyFrozenPackingEntryId!="") {
				# Delete grade Rec
				$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRec($dailyFrozenPackingEntryId);
				
				# Delete Entry Rec
				$frozenPackingEntryRecDel = $dailyfrozenpackingObj->deletePackingEntryRec($dailyFrozenPackingEntryId);
				#Check Record Exists
				$exisitingRecords = $dailyfrozenpackingObj->checkRecordsExist($dailyFrozenPackingMainId);
				
				if (sizeof($exisitingRecords)==0) {
					# delete Main Rec
					$dailyFrozenPackingRecDel = $dailyfrozenpackingObj->deleteDailyFrozenPackingMainRec($dailyFrozenPackingMainId);	
				}
			}
		} // Loop Ends here

		if ($dailyFrozenPackingRecDel || $frozenPackingEntryRecDel) {
			$cnt++;
			$sessObj->createSession("displayMsg", $msg_succDelDailyFrozenPacking);				
			} else $err	= $msg_failDelDailyFrozenPacking;

		if ($prodnRowCount==$cnt) $completeEntriesDeleted = true;
		
		if (!$completeEntriesDeleted) {
			$entrySel = "DE";
			$entrySection = false;
			$editMode = true;
		} else {
			$editMode = false;
			$addMode = false;
			$sessObj->createSession("nextPage", $url_afterDelDailyFrozenPacking.$selection);
		}
	}


	# Edit 
	if ($p["editId"]!="") {
	
		$allocateId		=	$p["allocateId"];
		if ($allocateId && $p["cmdEdit"]=="") $allocateMode	=	true;
		$editId			=	$p["editId"];
		$frozenPackingEntryId	=	$p["editFrozenPackingEntryId"];
		
		$editMode		= true;
		if ($allocateMode) {
			$readOnly = "readOnly";
			$disabled  = "disabled";
		}
		$rmLotStatus	= $p["editRMLotStatus"];
		$editCriteria		= $p["editCriteria"];
		$eCriteria		= explode(",",$editCriteria);		
		$processId		= trim($eCriteria[0]);
		$freezingStage		= trim($eCriteria[1]);
		$frznCode			= trim($eCriteria[2]);
		$frozenCode			= trim($eCriteria[2]);
		//ECHO $frozenCode;
		$displayEditMsg		= strtoupper(trim($eCriteria[3]));
		$selMCPkgId			= trim($eCriteria[4]);
		if ($frznCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frznCode);
		$entrySel = "DE";
		$entrySection = false;	
			
			
		$numPacks = "";
		if ($allocateMode) {
			$fpMCPkgRecs	= $dailyfrozenpackingObj->getFPMCPkg($processId, $freezingStage, $frozenCode);
			if (sizeof($fpMCPkgRecs)>1 || sizeof($fpMCPkgRecs)<0) {
				$allocateMode = false;
				$editMode = false;
				$err		=	"Please check MC Pkg settings";
			} else {
				$MCPkgId   = $fpMCPkgRecs[0][0];
				$MCPkgCode = $fpMCPkgRecs[0][1];
				$numPacks = $fpMCPkgRecs[0][2];
				$displayEditMsg .= " - MC PKG ".$MCPkgCode;
			}
			// Get Pending POs
			$purchaseOrders = $purchaseorderObj->getPendingOrders($processId, $freezingStage, $frozenCode, $MCPkgId);
		}
		
	}

	# update a rec
	if ($p["cmdSaveChange"]!="" || $p["cmdConvertLS2MC"]!="") {
		$addMode=false;
		$LS2MCConversionEnabled = false;
		if ($p["cmdConvertLS2MC"]!="") $LS2MCConversionEnabled = true;

		$dailyFrozenPackingId	=	$p["hidDailyFrozenPackingId"];
		
		$mainId 	=	$p["mainId"];
		$entryId	=	$p["entryId"];
				
		$selectDate		=	mysqlDateFormat($p["selectDate"]);
		
		$unit			=	$p["unit"];
		$processorId		=	$p["processor"];		
		$fishId			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$freezingStage		=	$p["freezingStage"];
		$eUCode			=	$p["eUCode"];
		$sBrand		= explode("_",$p["brand"]);
		$brand		= $sBrand[0];
		$brandFrom	= $sBrand[1];

		$frozenCode		=	$p["frozenCode"];
		$frznCode		=	$p["frznCode"];
		if ($frznCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frznCode);
		$glazeId=$frozenpackingObj->frznPkgglaze($frznCode);
		$glaze=$glazeObj->findGlazePercentage($glazeId);
		$Wt=$filledWt-($filledWt*$glaze/100);
		//echo "hii".$frozenCode;
		//die();
		$mCPacking		=	$p["mCPacking"];
		$exportLotId		=	$p["exportLotId"];
		$lotId			=	$p["lotId"];
		$selQuality 		= 	$p["selQuality"];
		$customer		= $p["customer"];

		$allocateMode		=	$p["allocateMode"];

		$prodnRowCount 	= $p["hidProdnRowCount"];
		$gradeRowCount	= $p["hidGradeRowCount"];
		$rmLotStatus	= $p["rmLotStatus"];
		
		if ($prodnRowCount>0) {
			$currentDate	= mysqlDateFormat(date("d/m/Y")); // Get current date for conversion settings

			for ($i=1; $i<=$prodnRowCount; $i++) {
				$dFrznPkgEntryId = $p["dFrznPkgEntryId_".$i];  
				$frozenLotId	 = $p["frozenLotId_".$i];
				$mcPackingId	 = $p["mcPackingId_".$i];
				$physStockMainId=$p["physStockMainId_".$i];
				if ($dFrznPkgEntryId && $rmLotStatus=="0") {
					$updateDFPEntryRec = $dailyfrozenpackingObj->updateDFPEntry($dFrznPkgEntryId, $frozenLotId, $mcPackingId);
					$updatephysstatus = $dailyfrozenpackingObj->updatePhyMain($physStockMainId);
				}
				elseif ($dFrznPkgEntryId && $rmLotStatus!="0") {
					$updateDFPEntryRec = $dailyfrozenpackingObj->updateDFPEntryRmlotId($dFrznPkgEntryId, $frozenLotId, $mcPackingId);
					$updatephysstatus = $dailyfrozenpackingObj->updatePhyMain($physStockMainId);
				}
				for ($j=1; $j<=$gradeRowCount; $j++) {
					$gradeEntryId = $p["gradeEntryId_".$j."_".$i];
					$numMC = $p["numMC_".$j."_".$i];
					$numLS = $p["numLS_".$j."_".$i];

					$oldMC = "";
					$oldLS = "";
					$conversionDate = "";
					if ($LS2MCConversionEnabled) {
						$oldMC = $p["hidNumMC_".$j."_".$i];
						$oldLS = $p["hidNumLS_".$j."_".$i];
						$conversionDate = $currentDate;
					}

					if ($gradeEntryId && $rmLotStatus=="0") 
					{
						$netWt=$numMC*$Wt+$numLS*$Wt;	
						$uptdDFPGradeEntry = $dailyfrozenpackingObj->updateDFPGradeEntry($gradeEntryId, $numMC, $numLS, $oldMC, $oldLS, $conversionDate,$netWt);
						$updatephysstatus = $dailyfrozenpackingObj->updatePhyMain($physStockMainId);
					}
					elseif ($gradeEntryId && $rmLotStatus!="0") 
					{
						$netWt=$numMC*$Wt+$numLS*$Wt;
						$uptdDFPGradeEntry = $dailyfrozenpackingObj->updateDFPGradeEntryRMlotId($gradeEntryId, $numMC, $numLS, $oldMC, $oldLS, $conversionDate,$netWt);
						$updatephysstatus = $dailyfrozenpackingObj->updatePhyMain($physStockMainId);
					}
				} // Grade Ends here
			} // Product Row Ends here
		}
	
		if ($updateFrozenPackingEntryRec || $updateDFPEntryRec) {
		$editMode	=	false;
			$sessObj->createSession("displayMsg", $msg_succUpdateDailyFrozenPacking);
			$sessObj->createSession("nextPage", $url_afterUpdateDailyFrozenPacking.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyFrozenPacking;
		}
		$dailyFrozenPackingRecUptd	=	false;
		
		
		//$dailyFrozenPackingRecIns=	false;
		//$hidEditId 	=  "";
	}

	// Allocation
	if ($p["cmdAllocation"]!="") {
		
		$allocateMode		=	$p["allocateMode"];

		$prodnRowCount 	= $p["hidAllocateProdnRowCount"];
		$gradeRowCount	= $p["hidAllocateGradeRowCount"];
		if ($prodnRowCount>0) {
			for ($i=1; $i<=$prodnRowCount; $i++) {
				$status = $p["status_".$i];	
			   if ($status!='N')
			   {
						$dFrznPkgEntryId = $p["dFrznPkgEntryId_".$i];
						$POId			 = $p["POId_".$i];
						$totalSlabs		 = $p["totalSlabs_".$i];
						$totalQty		 = $p["totalQty_".$i];
						$POEntryId		 = $p["POEntryId_".$i];

						$dfpPOEntryId = 0;		
						if ($dFrznPkgEntryId>0 && $POId>0) {

							if ($POEntryId>0) {
								// Update record
								$updatePORec = $dailyfrozenpackingObj->updateDFPPORecs($dFrznPkgEntryId, $POId, $totalSlabs, $totalQty, $POEntryId);
								$dfpPOEntryId = $POEntryId;
							} else {
								$insertDFPPORecs =  $dailyfrozenpackingObj->insertDFPPORecs($dFrznPkgEntryId, $POId, $totalSlabs, $totalQty);
								// Last Entry Id
								if ($insertDFPPORecs) $dfpPOEntryId = $databaseConnect->getLastInsertedId();
							}
							
							if ($dfpPOEntryId>0) {
								for ($j=1; $j<=$gradeRowCount; $j++) {
									$gradeId = $p["sGradeId_".$j."_".$i];
									$allocateGradeEntryId = $p["allocateGradeEntryId_".$j."_".$i];

									$numMC = $p["numMC_".$j."_".$i];
									$numLS = $p["numLS_".$j."_".$i];

									if ($allocateGradeEntryId>0) {
										// Update
										if ($gradeId>0) {
											$updatePOGradeRec = $dailyfrozenpackingObj->updateDFPPOGradeForAllocation($dfpPOEntryId, $gradeId, $numMC, $numLS, $allocateGradeEntryId);
										}
									} else {
										// Insert
										if ($gradeId>0 && ($numMC>0 || $numLS>0) ) {
											$insertPOGradeRec = $dailyfrozenpackingObj->insertDFPPOGradeForAllocation($dfpPOEntryId, $gradeId, $numMC, $numLS);									
										}
									}
								} // Grade Ends here
							}
						}	
					}			
			} // Product Row Ends here

			# Delete 
			if ( $p["hidDelAllocationArr"] != "" ) {						
				$delArr = $p["hidDelAllocationArr"];		
				$delAllocationArr = explode(",",$delArr); 				
				if (sizeof($delAllocationArr)>0) {
					for ($i=0;$i<sizeof($delAllocationArr);$i++) {
						$allocationPOEntryId	= $delAllocationArr[$i];
						if ($allocationPOEntryId>0) $delAllocation = $dailyfrozenpackingObj->deleteAllocationEntry($allocationPOEntryId);	
					}
				}
			}	
		}
	
		if ($insertPOGradeRec || $updatePOGradeRec) {
			$sessObj->createSession("displayMsg", $msg_succAllocateDailyFrozenPacking);
			$sessObj->createSession("nextPage", $url_afterUpdateDailyFrozenPacking.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failAllocateDailyFrozenPacking;
		}
		$dailyFrozenPackingAllocationRecUptd	=	false;
		
	}

	# Delete 
	if ($p["cmdDelete"]!="") {
		
		$dateFrom = $p["frozenPackingFrom"];
		$dateTill = $p["frozenPackingTill"];
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dfpGroupRec  = $p["delGId_".$i];
			
			if ($dfpGroupRec!="") {
				$dfpMainId = $p["dfpMainId_".$i];
				$rmLotID=$p["rmLotID_".$i];
				$eCriteria	= explode(",",$dfpGroupRec);					
				$processCodeId	= trim($eCriteria[0]);
				$freezingStageId = trim($eCriteria[1]);
				$frozenCodeId	= trim($eCriteria[2]);
				$mcPkgId		= trim($eCriteria[4]);
				$recInUse = false;
				$allocatedRecs = $dailyfrozenpackingObj->getPOAllocatedRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId);
				if (sizeof($allocatedRecs)<=0) {
										
					# get Grouped recs
					if($rmLotID=="0")
					{	
						$groupedFPRecs = $dailyfrozenpackingObj->getProductionRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId);
					}
					else
					{
						$groupedFPRecs = $dailyfrozenpackingObj->getProductionRecsRmLotId($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId,$rmLotID);
					}
					$dfPkgEntryId = "";
					$dfPkgMainId = "";
					foreach ($groupedFPRecs as $gfpr) {
						$dfPkgEntryId 	= $gfpr[0];
						$dfPkgMainId = $gfpr[4];	

						if ($dfPkgEntryId!="" && $rmLotID=="0") {					
							# Delete grade Rec
							$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRec($dfPkgEntryId);
							
							# Delete Entry Rec
							$frozenPackingEntryRecDel = $dailyfrozenpackingObj->deletePackingEntryRec($dfPkgEntryId);
							#Check Record Exists
							$exisitingRecords = $dailyfrozenpackingObj->checkRecordsExist($dfPkgMainId);
							
							if (sizeof($exisitingRecords)==0) {
								# delete Main Rec
								$dailyFrozenPackingRecDel = $dailyfrozenpackingObj->deleteDailyFrozenPackingMainRec($dfPkgMainId);	
							}
						} 
						elseif ($dfPkgEntryId!="" && $rmLotID!="0") {		
						
							
							# Delete grade Rec
							$dailyFrozenPackingGradeRecDel = $dailyfrozenpackingObj->deleteFrozenPackingGradeRecRmlotid($dfPkgEntryId);
							
							# Delete Entry Rec
							$frozenPackingEntryRecDel = $dailyfrozenpackingObj->deletePackingEntryRecRmlotid($dfPkgEntryId);
							#Check Record Exists
							$exisitingRecords = $dailyfrozenpackingObj->checkRecordsExistRmlotId($dfPkgMainId);
							
							if (sizeof($exisitingRecords)==0) {
								# delete Main Rec
								$dailyFrozenPackingRecDel = $dailyfrozenpackingObj->deleteDailyFrozenPackingMainRecRmLotId($dfPkgMainId);	
							}
						
						}
					} // Loop Ends here

					// Delete Main rec
					if (empty($processCodeId) && empty($freezingStageId) && empty($frozenCodeId) && $dfpMainId>0) {
						# delete Main Rec
						$dailyFrozenPackingRecDel = $dailyfrozenpackingObj->deleteDailyFrozenPackingMainRecRmLotId($dfpMainId);
					}
				} // Allocated Rec size check ends here
				else {
					$recInUse = true;
				}
			}
		}
		if ($dailyFrozenPackingRecDel || $frozenPackingEntryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelDailyFrozenPacking);
			$sessObj->createSession("nextPage", $url_afterDelDailyFrozenPacking.$selection);
		} else {
			$errDel	=	$msg_failDelDailyFrozenPacking;
			if ($recInUse) $errDel	.= " The selected record is already in use.";
		}

		$dailyFrozenPackingRecDel	=	false;
	}

	if($p["convertLS"]!="")
	{
		$convertLS=$p["convertLS"];
		//echo "hii";
		$frozenEntryId=""; $rmlotIds="";
		$hidRowCount=$p['hidRowCount'];
		for($i=1; $i<=$hidRowCount; $i++)
		{
			$delGId=$p["delGId_".$i]; 
			if($delGId!="")
			{
				if($rmlotIds=="")
				{
					$rmlotIds=$p["rmLotID_".$i]; 
				}
				else
				{
					$rmlotIds.=','.$p["rmLotID_".$i]; 
				}
				
			$values=explode(',', $delGId);
			$processId=$values[0];
			$freezingId=$values[1];	
			$frozenCode=$values[2];	
			$MCPkgCode=$values[3];			
			}
		
		}
		//echo $processId;
		$hidMcDisplay="display:none;";
		$editMode=true;
		$entrySection = false;	
		//echo $frozenEntryId;
		$displayEditMsg .= " - MC PKG ".$MCPkgCode;	
		$filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
	}

	/*if($p["cmdConvertLSSave"])
	{
		$prodnRowCount 	= $p["hidProdnRowCountCvt"];
		$GradeRowCount=$p["hidGradeRowCountCvt"];
		
		for($i=1; $i<=$prodnRowCount; $i++)
		{
			$dFrznPkgEntryIdCvt=$p["dFrznPkgEntryIdCvt_".$i];
			$rm_lot_id=$p["rmLotStatusCvt_".$i];
			$companyId=$p["companyId_".$i];
			$unitId=$p["unitId_".$i];
			$fishId=$p["fishIdCvt_".$i];
			$processId=$p["processIdCvt_".$i];
			$freezingId=$p["freezingIdCvt_".$i];
			$frozenCodeId=$p["frozenCodeIdCvt_".$i];
			$mcPackingId=$p["mcPackingIdCvt_".$i];
			//echo $dFrznPkgEntryIdCvt.'-->'.$i.'<br/>';
			if($i==1)
			{
				$tempMainTableRecIns = $dailyfrozenpackingObj->addTempDataMainTableRmlotid($userId,$rm_lot_id,$companyId,$unitId);
				if ($tempMainTableRecIns!="") 
				{
					$mainId	= $databaseConnect->getLastInsertedId();
				}
			
				$tempEntryTableRecIns = $dailyfrozenpackingObj->addDataToEntryRmlotId($mainId,$fishId,$processId,$freezingId,$frozenCodeId,$mcPackingId);
				if ($tempEntryTableRecIns!="") 
				{
					$entryId	= $databaseConnect->getLastInsertedId();
				}	
				//update
				for($j=1; $j<=$GradeRowCount; $j++)
				{
					$gradeId   = 	$p["gId_".$j];
					$numLSCvt=	$p["numLSCvt_".$j."_".$i];
					$numMCCvt=	$p["numMCCvt_".$j];
					//echo $j.'-->'.$gId.'-->'.$numLSCvt.'<br/>';
					$numMCCvtLs='0';
					$tempGradeTableRecIns = $dailyfrozenpackingObj->addDataToGradeRmlotId($entryId,$gradeId,$numMCCvtLs);
					$updateLS=$dailyfrozenpackingObj->updateLSOfGrade($gradeId,$dFrznPkgEntryIdCvt,$numLSCvt);
				}
			}
			else
			{
				$removeLS=$dailyfrozenpackingObj->updateEntryRMlotLS($dFrznPkgEntryIdCvt);
			}
		}
		
		if ($updateLS || $removeLS  ) {
					//$editMode	=	false;
						$sessObj->createSession("displayMsg", $msg_succNewLotDailyFrozenPacking);
						$sessObj->createSession("nextPage", $url_afterNewLotDailyFrozenPacking.$selection);
					} else {
						$editMode	=	true;
						$err		=	$msg_failDailyFrozenPackingNewLot;
					}
		
	}*/
	if($p["cmdConvertLSSave"])
	{
		//echo "hii";
		
		$newRMAlpha=$p["newRMAlpha"];
		$newRMNumber=$p["newRMNumber"];
		$newRMNumberGenId=$p["newRMNumberGenId"];
		$newRMmcPackingId=$p["newRMmcPackingId"];
		$newRMfishId=$p["newRMfishId"];
		$newRMProcessId=$p["newRMProcessId"];
		$newRMFreezingId=$p["newRMFreezingId"];
		$newRMFrozenCodeId=$p["newRMFrozenCodeId"];
		$newRMCompanyId=$p["newRMCompanyId"];
		$newRMUnitId=$p["newRMUnitId"];
		$entryidOld=$p["entryidOld"];
		$hidGradeRowCount=$p["hidGradeRowCountCvt"];
		
			$rm_lot_id="";
			$tempMainTableRecIns = $dailyfrozenpackingObj->addTempDataMainTableRmlotid($userId,$rm_lot_id,$newRMCompanyId,$newRMUnitId);
				if ($tempMainTableRecIns!="") 
				{
					$mainId	= $databaseConnect->getLastInsertedId();
				}
			//die();	
			$tempEntryTableRecIns = $dailyfrozenpackingObj->addDataToEntryRmlotId($mainId,$newRMfishId,$newRMProcessId,$newRMFreezingId,$newRMFrozenCodeId,$newRMmcPackingId);
				if ($tempEntryTableRecIns!="") 
				{
					$entryId	= $databaseConnect->getLastInsertedId();
				}	
							
				for($i=0; $i<$hidGradeRowCount; $i++)
				{
					$gradeId=$p["grd_".$i];
					$numMCNewRM=$p["numMCNewRM_".$i];
					$numLSNewRM='';
					if($numMCNewRM!='0')
					{
					//$numLSNewRM=$p["numLSNewRM_".$i];
					$tempGradeTableRecIns = $dailyfrozenpackingObj->addDataToGradeRmlotId($entryId,$gradeId,$numMCNewRM,$numLSNewRM);
					}
				
				}
				###need to edit the data not removal of ls
				$entry=explode(',',$entryidOld);
				$entrySize=sizeof($entry);
				$m=1;
				for($j=0; $j<$entrySize; $j++)
				{
					$entryId=$entry[$j];
					for($l=1; $l<=$hidGradeRowCount; $l++)
					{	
						$gradeId   = 	$p["gId_".$l];
					 	$numLSCvt=	$p["numLSCvt_".$l."_".$m];
						//echo "numLSCvt_".$l."_".$m;
						 $entryId.'-->'.$gradeId.'-->'.$numLSCvt.'<br/>';
						
						$updateLS=$dailyfrozenpackingObj->updateLSOfGrade($gradeId,$entryId,$numLSCvt);
					}
					
					$m++;
					
					
				}
				//die();
				
				if($tempGradeTableRecIns!="")
				{
					$lot=$objManageRMLOTID->addLotIdGeneratedInFrozen($newRMCompanyId,$newRMUnitId,$newRMNumber,$newRMAlpha,$newRMNumberGenId,$userId);
					$rmlotid	= $databaseConnect->getLastInsertedId();
					$updateFrozen=$dailyfrozenpackingObj->updatelotidInPackingMain($newRMCompanyId,$newRMUnitId,$rmlotid, $mainId);
				}
					$delRmLOtId = $objManageRMLOTID->deleteTemporary($newRMNumber,$newRMNumberGenId);	
				
				if ($updateFrozen ) {
					//$editMode	=	false;
						$sessObj->createSession("displayMsg", $msg_succNewLotDailyFrozenPacking);
						$sessObj->createSession("nextPage", $url_afterNewLotDailyFrozenPacking.$selection);
					} else {
						$editMode	=	true;
						$err		=	$msg_failDailyFrozenPackingNewLot;
					}
					
		
		//$dailyFrozenPackingRecDel	=	false;
				
				
				
				
	}
	
	
	
	
	
	//if ($addMode || $editMode ) {
	if ($addMode || $editMode || $convertLS) {
			#List All Plants
		//$plantRecords		= $plantandunitObj->fetchAllRecordsPlantsActive();
		
		//$companyRecords		= $billingCompanyObj->fetchAllRecordsActivebillingCompany();
		#List All Fishes
		$fishMasterRecords	= $fishmasterObj->fetchAllRecordsFishactive();
	
		#List All Freezing Stage Record
		$freezingStageRecords	= $freezingstageObj->fetchAllRecordsActivefreezingstage();
		
		#List All EU Code Records
		$euCodeRecords		= $eucodeObj->fetchAllRecordsActiveEucode();
	
		#List All Brand Records
		//$brandRecords		= $brandObj->fetchAllRecords();
		if ($customer) $brandRecords		= $brandObj->getBrandRecords($customer);
			
		#List All Frozen Code Records
		$frozenPackingRecords	= $frozenpackingObj->fetchAllRecordsActiveFrozen();
		
		#List All MC Packing Records
		$mcpackingRecords	= $mcpackingObj->fetchAllRecordsActivemcpacking();
	
		#List All Purchase Order Records
		$purchaseOrderRecords	= $purchaseorderObj->fetchNotCompleteRecords();
	
		#List All Quality Records
		$qualityMasterRecords	= $qualitymasterObj->fetchAllRecordsActiveQuality();

		#List All Customer Records
		$customerRecords		=	$customerObj->fetchAllRecordsActiveCustomer();
	}

	#List all Lot Id for a selected date
	if ($p["selectDate"])	$packingDate	= mysqlDateFormat($p["selectDate"]);
	else 			$packingDate	= mysqlDateFormat(date("d/m/Y"));
	if ($addMode) $frozenLotIdRecords = $dailyfrozenpackingObj->fetchLotIdRecords($packingDate);

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------


	$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
	# select records between selected date
	if ($g["frozenPackingFrom"]!="" && $g["frozenPackingTill"]!="") {
		$dateFrom = $g["frozenPackingFrom"];
		$dateTill = $g["frozenPackingTill"];		
	} else if ($p["frozenPackingFrom"]!="" && $p["frozenPackingTill"]!="") {
		$dateFrom = $p["frozenPackingFrom"];
		$dateTill = $p["frozenPackingTill"];		
	} else {
		/* As on Physical stock Entry last date*/
		$maxdate= $dailyfrozenpackingObj->getMaxDate();
		//$currYear=Date("Y");
		//$currFinanYear="01/04/$currYear";
		//$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
		//$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):date("d/m/Y");
		$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):$defaultDFPDate;
		$dateFrom=$maximumdt;
		$dateTill = date("d/m/Y");
		$supplierFilterId = "";
	}

	
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$filterProcessCode = $p["processCode"];

		$dailyFrozenPackingRecs = $dailyfrozenpackingObj->getPagingDFPRecs($fromDate, $tillDate, $offset, $limit,$filterProcessCode);		
		$numrows	=  sizeof($dailyfrozenpackingObj->getDFPForDateRange($fromDate, $tillDate,$filterProcessCode));
		$dailyFrozenPackingRecordSize=sizeof($dailyFrozenPackingRecs);
	}
	
	
	
	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
	# Get Quick Entry List Records
	$fznPkngQuickEntryListRecords = array();
	if ($addMode && $validPCEnabled) {
		if ($p["selectDate"]!="") $selectedDate = mysqlDateFormat($p["selectDate"]);
		else $selectedDate = date("Y-m-d");
		# PC TEMP Recs
		$processCodeRecs = $productionAnalysisReportObj->processCodeRecs($selectedDate, $selectedDate, '');
			
		
		# get PC Grouping recs
		$gTempFishRecs = $productionAnalysisReportObj->getTempFishRecs();
		
		$fznPkngQuickEntryListRecords = array();
		foreach ($gTempFishRecs as $tProcesCodeId=>$tFishId) {
			# Get Fish recs		
			$qelFishRecs = $dailyfrozenpackingObj->getQELFishRecs($tProcesCodeId);
			$qelMainId="";
			$qelName = "";
			foreach ($qelFishRecs as $qfr) {
				$qelMainId 	= $qfr[0];
				$qelName	= $qfr[2];
				$fznPkngQuickEntryListRecords[$qelMainId] = $qelName;
			}
		}
		asort($fznPkngQuickEntryListRecords);

		# If Quick Entry List	
		if ($selQuickEntryList) {		
			# Grade Records
			$qeGradeRecords = $dailyfrozenpackingObj->qeGradeRecords($selQuickEntryList);
			
	
			# Today's Selected Process Code Recs
			if ($selectedDate) {			
				$todaysPCArr = array();
				foreach ($processCodeRecs as $pcr) {
					$tPCId		= $pcr[0];
					$tFishId 	= $pcr[1];
					$tProcessCode	= $pcr[2];
					if ($tProcessCode!="") {
						$todaysPCArr[$tPCId] = array($tPCId, $tFishId, $tProcessCode);
					}
				}
	
				# Get QEL Selected Process Code Records
				$qelProcessCodeRecs = $dailyfrozenpackingObj->getSelQELProcessCodeRecs($selQuickEntryList);
				$qelPCArr	= array();
				foreach ($qelProcessCodeRecs as $qelpcr) {
					$qelPCId		= $qelpcr[2];
					$qelFishId 		= $qelpcr[1];
					$qelProcessCode		= $qelpcr[3];		
					if (array_key_exists($qelPCId,$todaysPCArr)) {		
						$qelPCArr[$qelPCId] = array($qelPCId, $qelFishId, $qelProcessCode);
					}
				}
				$selProcessCodeRecs = $qelPCArr;
			}	
			
		} // Check ends here
	} else if ($addMode && !$validPCEnabled) {
		# get all recs
		$fznPkngQuickEntryListRecords = $dailyfrozenpackingObj->frznPkgAllRecords();
		if ($selQuickEntryList) {
 			# Grade Records
			$qeGradeRecords = $dailyfrozenpackingObj->qeGradeRecords($selQuickEntryList);
			# Get QEL Selected Process Code Records
			$selProcessCodeRecs = $dailyfrozenpackingObj->getQELWiseProcessCodeRecs($selQuickEntryList);
		}
	}

	# Coomon Setting
	if ($selQuickEntryList) {
		list($qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom) = $dailyfrozenpackingObj->getQERec($selQuickEntryList);
			$numPacks = "";
			if ($qeMCPackingId!=0) {
				$mcpackingRec	= $mcpackingObj->find($qeMCPackingId);
				$numPacks	= $mcpackingRec[2];
				$qelMCPackingCode = $mcpackingRec[1];
			}
	}
	

	if ($addMode || $editMode || $convertLS) {
		# Get All Active Processors
		if ($unit) {
			
			$activeProcessorRecords = $preprocessorObj->getActiveProcessorRecs($currentUrl, $unit);
			$selFPProcessors        = $dailyfrozenpackingObj->getSelDFPProcessor(mysqlDateFormat($selDate));	
			$processorRecords	= multi_unique(array_merge($activeProcessorRecords, $selFPProcessors));
			# sort by name asc
			usort($processorRecords, 'cmp_name');	
			# Processor section ends here
			
		}
	}
	
	# Edit Mode
	if ($editMode ) {
		
		if ($allocateMode) {
			# Get products
			$productRecs = $dailyfrozenpackingObj->getAllocateProductionRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode);			
			$productRecSize = sizeof($productRecs);
		}
		elseif ($convertLS) 
		{
			//$productRecs = $dailyfrozenpackingObj->getProductionRecsRmLotIdSelected($frozenEntryId);
			$productRecs = $dailyfrozenpackingObj->getProductionRecsRmLotIdSelected($rmlotIds,$processId,$freezingId,$frozenCode);
			$productRecSize = sizeof($productRecs);
			$rmlotRecs = $dailyfrozenpackingObj->getlotId($rmlotIds,$processId,$freezingId,$frozenCode);
			$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecordsRmLotId($processId, $entryId);
		}
		else {
			# Get products
			if($rmLotStatus=='0')
			{
				$productRecs = $dailyfrozenpackingObj->getProductionRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $selMCPkgId);
				$productRecSize = sizeof($productRecs);
				$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecords($processId, $entryId);
			}
			else
			{
				$productRecs = $dailyfrozenpackingObj->getProductionRecsRmLotId($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $selMCPkgId,$rmLotStatus);
				$productRecSize = sizeof($productRecs);
				$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecordsRmLotId($processId, $entryId);
			}
		}
		# grade
		//$gradeRecs = $dailyfrozenpackingObj->getProductionGradeRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $selMCPkgId);
		//echo $entryId;
		
		// if($rmLotStatus=='0')
		// {
			// $gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecords($processId, $entryId);
		// }
		// else
		// {	
			// $gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecordsRmLotId($processId, $entryId);
		// }
	}

	if( ($editMode && !$allocateMode ) && ($editMode && !$convertLS ) )$heading = $label_editDailyFrozenPacking;
	else if ($allocateMode) $heading = $label_allocateDailyFrozenPacking; 
	else if ($convertLS) $heading =$label_convertLSDailyFrozenPacking;
	else $heading = $label_addDailyFrozenPacking;

	$processCodeRecords	= $processcodeObj->processCodeRecFilter11();
	//print_r($processCodeRecords);
	
	# $help_lnk="help/hlp_Packing.html";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	

	list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
	$frznCode=$dailyfrozenpackingObj->getFrozenCode($selQuickEntryList);
	//echo "hiii".$frozenCode;
	# Setting the mode
	$mode = "";
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/dailyfrozenpacking.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmDailyFrozenPacking" action="DailyFrozenPacking.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?php if (($defaultDFPDate=="" ) && ($maximumdt=="")){?>
		<tr>
			<td height="10" align="center" class="listing-item" style="color:Maroon;">Please set the Frozen stock start date</td>
		</tr>
		<?php
		}?>
		<?
		if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<?php
								if ($addMode && $entrySel=="" ) {
								?>
								<tr> 
									<td width="1" ></td>
									<td colspan="2" > 
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr> 
												<td height="10"></td>
											</tr>
											<tr> 
												<td align="center">
													<table>
														<TR>
															<TD class="listing-item">
																<!--<INPUT type="radio" name="entrySel" id="entrySel1"  class="chkBox" value="QE" onclick="this.form.submit();">-->
																<INPUT type="radio" name="entrySel" id="entrySel1"  class="chkBox" value="QE" onclick="quickEntryOption(this);">
																&nbsp;Quick Entry
															</TD>
															<TD class="listing-item">
																<!--<INPUT type="radio" name="entrySel" id="entrySel2" class="chkBox" value="DE" onclick="this.form.submit();">&nbsp;Detailed Entry-->
															</TD>
														</TR>
													</table>
												</td>
											</tr>
											<tr> 
												<td height="10"></td>
											</tr>
										</table>
									</td>
								</tr>
								<?php
								}
								?>
								<?php
								//if ($entrySel || $editMode ) {
								if ($entrySel || $editMode ) {
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td align="center">
													<? if(($editMode && !$allocateMode) && ($editMode && !$convertLS)){?>
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);">	
													<? if($del==true){?>
														&nbsp;&nbsp;
														<input type="submit" value=" Delete " class="button"  name="cmdEntryDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productRecSize;?>);">
													<? }?>
													<?php } else if ($allocateMode) {?>
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
														<input type="submit" name="cmdAllocation" class="button" value=" Allocate " onClick="return validateDFPAllocation(document.frmDailyFrozenPacking);">	
													<?php } 
													else if ($convertLS) {?>
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
														
													<?php }?>
													&nbsp;&nbsp;
													<?php if (!$convertLS)
													{ ?>
														<input type="submit" value=" Convert LS to MC & Save " class="button"  name="cmdConvertLS2MC" id="cmdConvertLS2MC" onClick="return convertLS2MC();">
													<?php
													}
													else
													{
													?>
														<!--<input type="button" value=" Convert LS to MC" class="button"  name="cmdConvert2MC" onClick="return calcProdnQtyLS();">-->
													<input type="button" value=" Convert LS to MC" class="button"  name="cmdConvert2MC" id="cmdConvert2MC1" onClick="return convert2MC();">
													<?php
													}
													?>
													<input type="submit" value="Save" class="button"  name="cmdConvertLSSave" id="cmdConvertLSSave1"  style="display:none;">
												</td>
												<? } else{?>
												<td align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" style="width:100px;">&nbsp;&nbsp;
													<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:130px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New">
													<? if ($selQuickEntryList!="") {?>
													&nbsp;&nbsp;<input name="cmdSaveAndQE" type="submit" class="button" id="cmdSaveAndQE" style="width:200px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New Quick Entry">	
													<? }?>
												</td>
											<? }?>
											</tr>
											<input type="hidden" name="hidDailyFrozenPackingId" value="<?=$dailyFrozenPackingId;?>">
											<tr>
												<td nowrap></td>
											 </tr>
											 <tr>
												<td colspan="2" height="10"></td>
											 </tr>
											<?php
											if ($entrySel=="DE" && $entrySection) {
											?>
											<tr>
												<td colspan="2" style="padding-left:60px; padding-right:60px;" align="center" nowrap><? if(!$allocateMode){?>
													&nbsp; &nbsp;
													<input name="cmdAddFishInSameICAndMC" type="submit" class="button" id="cmdAddFishInSameICAndMC" tabindex="32" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="Save &amp; Add New Fish in Same IC & MC Pkg" style="width:290px;">&nbsp; &nbsp;
													<input name="cmdAddFishInSameMC" type="submit" class="button" id="cmdAddFishInSameMC" tabindex="32" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="Save &amp; Add New Fish in Same MC Pkg" style="width:260px;" />&nbsp; &nbsp;
												<?}?>
												</td>
											</tr>
											<tr><td height="10">&nbsp;</td></tr>
											<tr>
												<td align="center">
													<?if(!$allocateMode){?>
														<input name="cmdAddProcessCodeInSameICAndMC" type="submit" class="button" id="cmdAddProcessCodeInSameICAndMC" tabindex="32" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="Save &amp; Add New Process Code in same IC&MC Pkg" style="width:340px;" />
														<?}?>
												</td>
											</tr>
											<tr>
												<td colspan="2" style="padding-left:60px;">&nbsp;</td>
											</tr>
											<?php
												}
											?>
											<tr>
												<td colspan="2" align="center">
													<table width="75%" align="center" cellpadding="0" cellspacing="0">
														<tr>
															<td valign="top">
															<?php
															if ($entrySection) {
															?>
																<table width="100%" >
																	<tr>
																		<td valign="top" width="30%"   >
																		<?php
																		$entryHead = "";
																		$rbTopWidth = "";
																		require("template/rbTop.php");
																		?>
																			<table border="0" cellspacing="2" cellpadding="2" >
																				<tr>
																					<td class="fieldName" nowrap="nowrap">*Date</td>
																					<td nowrap>
																					<?							
																						if($selDate==""){
																								$selDate	=	date("d/m/Y");
																						}						
																						?>
																				  <input type="text" id="selectDate" name="selectDate" size="8" value="<?=$selDate?>" <?=$readOnly?> onChange="this.form.submit();">
																				 </td>
																			</tr>
																			<tr>
																				<td class="fieldName" nowrap="nowrap">*Company</td>
																				<td nowrap>
																					<select name="company" id="company" <?php if ($addMode==true) { ?> onchange="dailyFrozenpackingLoad(this);"
																					<?php } else { ?>
																					onchange="this.form.editId.value=<?=$editId?>; dailyFrozenpackingLoad(this);" <?=$disabled?> <?php }
																					?>
																					>
																						<option value="">-- Select --</option>
																						<? foreach($companyRecords as $cr=>$crName)
																							{
																								$companyId	=	$cr;
																								$companyNm	=	stripSlash($crName);
																								$sel="";
																								if(($companyId== $company) || ($company=="" && $companyId==$defaultCompany)) $sel	=	"selected";
																						?>
																						<option value="<?=$companyId?>" <?=$sel?>><?=$companyNm?></option>
																						<? }?>
																					</select>		
																				</td>
																			</tr>
																			<tr>
																				<td class="fieldName" nowrap="nowrap">*Unit</td>
																				<td nowrap>
																				<? ($company!="")?$plantRecords=$unitRecords[$company]:$plantRecords=$unitRecords[$defaultCompany];?>
																					<?php  
																					if ($addMode==true) {
																					?>
																					<!--<select name="unit" onchange="this.form.submit();">-->
																					<select name="unit" onchange="dailyFrozenpackingLoad(this);">
																					<?
																					 } else {
																					?>
																					<!--<select name="unit" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" <?//=$disabled?>>-->
																					<select name="unit" onchange="this.form.editId.value=<?=$editId?>; dailyFrozenpackingLoad(this)" <?=$disabled?>>
																					<? }?>		
																						<option value="">-- Select --</option>
																						<? foreach($plantRecords as $pr=>$prName)
																							{
																								$plantId	=	$pr;
																								$plantName	=	stripSlash($prName);
																								$selected="";
																								if($plantId== $unit) $selected	=	"selected";
																						?>
																						<option value="<?=$plantId?>" <?=$selected?>><?=$plantName?></option>
																						<? }?>
																					</select>		
																				</td>
																			</tr>
																			<tr>
																				<td class="fieldName">*Processor</td>
																				<td nowrap>
																					<?  
																					if($p["processor"]!="") $processor	=	$p["processor"]; 
																					?>
																					<select name="processor" id="processor" <?=$disabled?>>
																						<option value="">-- Select --</option>
																						<? 
																						foreach($processorRecords as $ppr) {
																								$processorId		=	$ppr[0];
																								$processorName		=	stripSlash($ppr[1]);
																								$selected="";
																								if($processorId== $processor || $editProcessorId==$processorId) $selected	=	"selected";
																						?>
																						<option value="<?=$processorId?>" <?=$selected?>><?=$processorName?></option>
																						<? }?>
																					</select>			
																				</td>
																			</tr>
																		</table>
																		<?php
																		require("template/rbBottom.php");
																		?>
																	</td>
																	<td valign="top" width="30%"  >
																	<?php
																		$entryHead = "";
																		$rbTopWidth = "";
																		require("template/rbTop.php");
																	?>
																		<table cellspacing="2" cellpadding="2">
																			<tr>
																				<td  nowrap class="fieldName"> Rm Lot ID :</td>
																				<td class="listing-item">
																					<select  id="rm_lot_id" name="rm_lot_id"  <?php if ($addMode==true) { ?> onchange="dailyFrozenpackingLoad(this);"
																					<?php } else { ?>
																					onchange="this.form.editId.value=<?=$editId?>; dailyFrozenpackingLoad(this)" <?=$disabled?> <?php }
																					?>>
																						<option value=""> -- Select Lot ID --</option>
																						<?php
																							if(sizeof($rmLotIdsVal) > 0)
																							{
																								foreach($rmLotIdsVal as $lotID)
																								{	
																								$sel = '';
																								if($rmlot == $lotID[0]) $sel = 'selected="selected"';
																								echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																								}
																							}
																						?>
																					</select>
																				</td>	
																			</tr>
																			<?php if ($addMode && $entrySel=='QE') {?>	
																			<tr>
																				<TD class="fieldName" nowrap="true">Quick Entry List</TD>
																				<td>
																				<!--<select name="selQuickEntryList" id="selQuickEntryList" onchange="this.form.submit();">-->
																					<select name="selQuickEntryList" id="selQuickEntryList" onchange="changeQuickEntryList(this);">
																						<option value="">-- Select --</option>
																						<?php
																						foreach ($fznPkngQuickEntryListRecords as $fznPkngQuickEntryListId=>$qEntryName) {
																							$selected = "";
																							if ($selQuickEntryList==$fznPkngQuickEntryListId) $selected = "selected";	
																						?>
																						<option value="<?=$fznPkngQuickEntryListId?>" <?=$selected?>><?=$qEntryName?></option>
																						<?php
																							}
																						?>
																					</select>
																				</td>
																			</tr>
																			<tr>
																				<TD class="fieldName" nowrap="true">Display</TD>
																				<td>
																					<select name="displayQE" id="displayQE" onchange="changeDisplayMCLS(this);">
																						<option value="DMCLS" <? if ($displayQE=='DMCLS') echo "selected";?>>Both MC & LS</option>
																						<option value="DMC" <? if ($displayQE=='DMC') echo "selected";?>>Only MC</option>
																						<option value="DLS" <? if ($displayQE=='DLS') echo "selected";?>>Only LS</option>
																					</select>
																				</td>
																			</tr>
																			<? }?>
																		</table>
																		<?php
																		require("template/rbBottom.php");
																		?>
																	</td>
																</tr>
																<tr id="fishRow">
																	<td class="fieldName" nowrap="nowrap">*Fish</td>
																	<td>
																		 <? 
																		if($addMode==true){
																		$fishId		=	$p["fish"];
																		if ( $fishId != "" ){	
																		$processCodeRecords	=	$processcodeObj->processCodeRecFilter($fishId);	
																		}	
																		?>
																		<select name="fish" onchange="this.form.submit();">
																		<? } else {?>
																		<select name="fish" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" <?=$disabled?>>
																		<? }?>
																			<option value="">--Select--</option>
																			<?
																			foreach($fishMasterRecords as $fr)
																			{
																				$Id		=	$fr[0];
																				$fishName	=	stripSlash($fr[1]);
																				$fishCode	=	stripSlash($fr[2]);
																				$selected	=	"";
																				if( $fishId==$Id){
																					$selected	=	"selected";
																				}
																			?>
																			<option value="<?=$Id?>" <?=$selected?>><?=$fishName?></option>
																			<? }?>
																		</select>
																	</td>
																</tr>
																<tr id="pcRow">
																	<td class="fieldName" nowrap="nowrap">*Process Code</td>
																	<td>
																		<?
																		if($addMode==true){ 
																		$processId	=	$p["processCode"];
																		?>
																		<select name="processCode" id="processCode" onchange="this.form.submit();">
																		 <? 
																		} else
																		{
																		?>
																		<select name="processCode" >
																		<? }?>
																			<option value="">-- Select --</option>
																			<?
																			foreach ($processCodeRecords as $fl)
																			{
																				$processCodeId		=	$fl[0];
																				$processCode		=	$fl[2];
																				$selected	=	"";
																				if( $processId==$processCodeId){
																				$selected	=	"selected";
																			}
																			?>
																			<option value="<?=$processCodeId;?>" <?=$selected;?> >
																			<?=$processCode;?>
																			</option>
																			<?
																			}
																			?>
																		</select>
																	</td>
																</tr>
																<tr id="fsRow">
																	<td class="fieldName" nowrap="nowrap">*Freezing Stage</td>
																	<td>
																		<? if($p["freezingStage"]!="") $freezingStage = $p["freezingStage"];?>
																		<select name="freezingStage" id="freezingStage">
																			<option value="0">--Select--</option>
																			<?
																			foreach($freezingStageRecords as $fsr)
																			{
																				$freezingStageId		=	$fsr[0];
																				$freezingStageCode		=	stripSlash($fsr[1]);
																				$selected		=	"";
																				if($freezingStage==$freezingStageId)  $selected	=	" selected ";
																													  
																			 ?>
																			<option value="<?=$freezingStageId?>" <?=$selected?>>
																				  <?=$freezingStageCode?>
																			</option>
																				<? }?>
																		</select>
																	</td>
																</tr>
																	<tr id="qltyRow">
																		<TD class="fieldName">Quality</TD>
																		<td>
																			<? if($p["selQuality"]!="") $selQuality = $p["selQuality"];?>
																			<select name="selQuality" id="selQuality">
																				<option value="0">-- Select --</option>
																				<?
																				foreach ($qualityMasterRecords as $fr) {
																					$qualityId	=	$fr[0];
																					$qualityName	=	stripSlash($fr[1]);
																					$qualityCode	=	stripSlash($fr[2]);
																					$selected = "";
																					if($selQuality==$qualityId)  $selected = " selected ";
																				?>
																				<option value="<?=$qualityId?>" <?=$selected?>><?=$qualityName?></option>
																				<? }?>
																			</select>
																		</td>
																	</tr>
																	<tr id="eucRow">
																		<td class="fieldName" nowrap="nowrap">EU Code</td>
																		<td>
																			<? if($p["eUCode"]!="") $eUCode = $p["eUCode"];?>
																			<select name="eUCode" id="eUCode">
																				<option value="0">-- Select--</option>
																				 <?php
																				foreach ($euCodeRecords as $eucr) {
																					$euCodeId		=	$eucr[0];
																					$euCode			=	stripSlash($eucr[1]);
																					$selected		=	"";
																					if ($eUCode==$euCodeId)  $selected = " selected ";
																				?>
																				<option value="<?=$euCodeId?>" <?=$selected?>>
																				<?=$euCode?>
																				</option>
																			<? }?>
																		</select>
																	</td>
																</tr>
																<tr id="buyerRow">
																	<td class="fieldName" nowrap="nowrap">Customer</td>
																	<td nowrap>	
																		<? if($p["customer"]!="") $customer =$p["customer"];?>  
																		<select name="customer" id="customer" onchange="xajax_getBrandRecs(document.getElementById('customer').value, '');">
																			<option value="">-- select --</option>
																			<?php
																				foreach ($customerRecords as $cr) {
																					$customerId	=	$cr[0];
																					$customerName	=	stripSlash($cr[2]);
																					$selected 	=	($customerId==$customer)?"Selected":"";
																			?>
																			<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
																			<? }?>
																		</select>
																	</td>
																</tr>
																<tr id="brndRow">
																	<td class="fieldName" nowrap="nowrap">Brand</td>
																	<td>
																	<? if($p["brand"]!="") $brand =$p["brand"];?>
																		<select name="brand" id="brand">
																			<? if (!sizeof($brandRecords)) {?> <option value="0">-- Select --</option><? }?>
																			<?php
																			foreach($brandRecords as $brandId=>$brandName) {
																				$selected = ($brand==$brandId)?"selected":"";
																			?>
																			<option value="<?=$brandId?>" <?=$selected?>><?=$brandName?></option>
																			<? }?>
																		</select>
																	</td>
																</tr>
																<tr id="fcRow">
																	<td class="fieldName" nowrap="nowrap">*Frozen Code</td>
																		<td nowrap="nowrap">
																			<? if($p["frozenCode"]!="") $frozenCode=$p["frozenCode"];?>
																			<select name="frozenCode" id="frozenCode" <?=$disabled?>>
																				<option value="">-- Select --</option>
																					<?
																					foreach($frozenPackingRecords as $fpr) {
																						$frozenPackingId	= $fpr[0];
																						$frozenPackingCode	= stripSlash($fpr[1]);
																						$selected		=	"";
																						if ($frozenCode==$frozenPackingId)  $selected = " selected ";
																					  ?>
																				<option value="<?=$frozenPackingId?>" <?=$selected?>>
																					 <?=$frozenPackingCode?>
																				</option>
																			<? }?>
																		</select>
																	</td>
																</tr>
																<tr id="mcpRow">
																	<td class="fieldName" nowrap="nowrap">MC Pkg</td>
																	<td nowrap="nowrap">
																		<? if($p["mCPacking"]!="") $mCPacking = $p["mCPacking"];?>
																		<select name="mCPacking" id="mCPacking" onchange="passMCPkgValue();">
																			<option value="0">-- Select --</option>
																			<?
																			foreach($mcpackingRecords as $mcp) {
																				$mcpackingId		=	$mcp[0];
																				$mcpackingCode		=	stripSlash($mcp[1]);
																				$selected		=	"";
																			if($mCPacking==$mcpackingId)  $selected	= " selected ";
																			?>
																			<option value="<?=$mcpackingId?>" <?=$selected?>>
																			 <?=$mcpackingCode?>
																			</option>
																			<? }?>
																		</select>
																	</td>
																</tr>
																<tr id="fliRow">
																	<td class="fieldName" nowrap="nowrap">Frozen Lot Id </td>
																	<td nowrap>
																	 <? if($p["lotId"]!="") $lotId	=	$p["lotId"]; ?>
																		<select name="lotId">
																			<option value=""> -- Select --</option>
																			<?php
																			$k=0;
																			foreach($frozenLotIdRecords as $flr)
																			{
																				$k++;
																				$dailyActivityChartEntryId = $flr[1];
																				$freezer	=	$flr[2];
																				$displayLotId	=	$k."-".$freezer;
																				$selected	= "";
																				if ($dailyActivityChartEntryId==$lotId) $selected = "Selected";
																			?>
																			<option value="<?=$dailyActivityChartEntryId?>" <?=$selected?>><?=$displayLotId?></option>
																			<? }?>
																		</select>
																	</td>
																</tr>
																<tr id="eliRow">
																	<td class="fieldName" nowrap="nowrap">Export &nbsp;Lot ID</td>
																	<td nowrap="nowrap" class="listing-item">
																		<? if($p["exportLotId"]!="") $exportLotId = $p["exportLotId"];?>
																		<select name="exportLotId" id="exportLotId">
																			<option value="0">-- Select --</option>
																			<? 
																			foreach($purchaseOrderRecords as $por)
																			{
																				$purchaseOrderId	=	$por[0];
																				$pOId			=	$por[1];
																				$selected		=	"";
																				if($exportLotId==$purchaseOrderId) $selected = "Selected";
																			?>
																			<option value="<?=$purchaseOrderId?>" <?=$selected;?>><?=$pOId?></option>
																			<? }?>
																		</select>                                                      
																	</td>
																</tr>
															</table>
															<?php
																} // Enable section ends here
															?>
														</td>
														<td valign="top" align="center" id="gradeRow">
															<table width="200">
																<? 
																if($fishId!="" && $processId!="")
																{
																?>
																<tr>
																	<TD align="center">
																		<table width="100%" border="0" cellpadding="0" cellspacing="0">
																			<tr>
																				<td>
																					<table align="center" cellpadding="0" cellspacing="0">
																						<tr>
																							<td>
																								<fieldset>
																									<legend class="listing-item">Grade</legend>
																									<iframe name="iFrame1" id="iFrame1" src="DailyFrozenPackingGrade.php?entryId=<?=$entryId?>&fishId=<?=$fishId?>&process=<?=$processId?>&mCPacking=<?=$mCPacking?>&allocateMode=<?=$allocateMode?>" width="280" frameborder="0" height="370"></iframe>
																								</fieldset>
																							</td>
																						</tr>
																					</table>				
																				</td>
																			</tr>
																		</table>
																	</TD>
																</tr>
																<?
																	}
																?>
															</table>
														</td>
													</tr>
													<?php
													if ($selQuickEntryList) {
													//echo $selQuickEntryList;
													?>
													<tr>
														<TD colspan="2">
														<input type="hidden" name="frznCode" id="frznCode" value="<?=$frznCode?>"/>
															<table>
																<tr bgcolor="White">
																	<TD class="listing-head" colspan="2">MC Packing = &nbsp;
																	<? if ($qelMCPackingCode!="") { ?>
																		<span class="listing-item"><b><?=$qelMCPackingCode?></b></span>
																	<? } else {?>
																			<select name="qeMCPacking" id="qeMCPacking" onchange="xajax_getMCNumPack(document.getElementById('qeMCPacking').value); calcMCPack('<?=$displayQE?>');">
																				<option value="">-- Select --</option>
																				<?php
																				foreach($mcpackingRecords as $mcp) {
																					$mcpackingId	= $mcp[0];
																					$mcpackingCode	= stripSlash($mcp[1]);
																					$selected	= ($mCPacking==$mcpackingId)?"selected":"";
																				?>
																				<option value="<?=$mcpackingId?>" <?=$selected?>><?=$mcpackingCode?></option>
																			<? }?>
																		</select>
																		<?php
																		}		
																		?>
																	</TD>
																</tr>
																<TR>
																	<TD>
																		<table width="200" border="0" cellpadding="1" cellspacing="1" align="center" bgcolor="#999999">
																		<?php
																			if (sizeof($qeGradeRecords) && sizeof($selProcessCodeRecs)>0) {
																			//echo "hai";
																			$i = 0;
																		?>
																			<tr bgcolor="#f2f2f2"  align="center">		
																				<td nowrap style="padding-left:2px;padding-right:2px;" class="listing-head">Grade</td>
																				<?php
																					$spc = 0;
																					foreach ($selProcessCodeRecs as $pcr) {
																						$spc++;
																						$pCode = $pcr[2];
																						$qelSFishId	= $pcr[1];
																						$qelSPCId 	= $pcr[0];
																						$processType=$pcr[4];
																				?>
																				<input type="hidden" name="processType_<?=$spc?>" id="processType_<?=$spc?>"  value="<?=$processType?>"/>
																				<td nowrap style="padding-left:2px;padding-right:2px;">				
																					<table cellpadding="1" cellspacing="0" width="100%">
																						<TR bgcolor="#f2f2f2">
																							<TD class="listing-head" colspan="3" align="center" id="processCodeCol_<?=$spc?>">
																								<?=$pCode;?>
																								<input type="hidden" name="packEntered_<?=$spc?>" id="packEntered_<?=$spc?>" value=""/>
																								<input type="hidden" name="hProcesscodeId_<?=$spc?>" id="hProcesscodeId_<?=$spc?>" value="<?=$qelSPCId?>"/>
																								<input type="hidden" name="hFishId_<?=$spc?>" id="hFishId_<?=$spc?>" value="<?=$qelSFishId?>"/>	
																								<input type="hidden" name="recExist_<?=$spc?>" id="recExist_<?=$spc?>" value=""/>
																								<span id="qelPCErr_<?=$spc?>" class="listing-item" style="font-size:9px; color:black;"></span>
																							</TD>
																						</TR>
																						<tr>
																							<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																						</tr>
																						<tr>
																							<?php
																								if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																							?>
																							<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																							<?php }?>
																							<?php
																								if ($displayQE=='DMCLS') {
																							?>
																							<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																							<? }?>	
																							<?php
																								if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																							?>
																							<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																							<? }?>
																						</tr>
																					</table>
																				</td>
																			<?php
																				}
																			?>
																			</tr>
																			<?php	
																				$g = 0;
																				//print_r($qeGradeRecords);
																				foreach ($qeGradeRecords as $gr) {
																					$g++;
																					$gradeId 	= $gr[0];
																					$displayGrade	= $gr[1];
																			?>
																			<tr bgcolor="WHITE">
																			<td class="listing-item" nowrap style="padding-left:2px;padding-right:2px;" id="gradeRow_<?=$g?>">
																				<input type="hidden" name="gradeId_<?=$g;?>" value="<?=$gradeId?>">
																					<?=$displayGrade?>
																			</td>
																			<?php
																			$p=0;
																			//print_r($selProcessCodeRecs);
																			foreach ($selProcessCodeRecs as $pcr)
																			{
																				//echo "First Value=$p";
																				$p++;
																				$sFishId	= $pcr[1];
																				$sProcessCodeId = $pcr[0];
																				if($processType=="primary")
																				{
																				# Check The selected PC has Grade Exist
																					$pcHasGrade = $dailyfrozenpackingObj->processCodeHasGrade($sProcessCodeId, $gradeId);
																				}
																				else
																				{
																					$pcHasGrade = $dailyfrozenpackingObj->secondaryProcessCodeHasGrade($sProcessCodeId, $gradeId);
																				}
																				$cellReadonly = "";
																				$styleDisplay = "";
																				if (!$pcHasGrade) 
																				{ 
																					$cellReadonly = "readonly";
																					$styleDisplay = "border:none;";
																				}
																			?>
																			<td nowrap align="right" height="25" style="padding-left:2px;padding-right:2px;">
																				<input type="hidden" name="hidProcesscodeId_<?=$p?>" id="hidProcesscodeId_<?=$p?>" value="<?=$sProcessCodeId?>"/>
																				<input type="hidden" name="hidFishId_<?=$p?>" id="hidFishId_<?=$p?>" value="<?=$sFishId?>"/>	
																				<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																					<TR>
																						<?php
																							if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																						?>
																						<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																							<input name="numMC_<?=$p?>_<?=$g?>" type="text" id="numMC_<?=$p?>_<?=$g?>" size="4" value="<?=$numMC?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyFrozenPacking','numMC_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" autocomplete="off" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" <?=$cellReadonly?> onkeyup="chkQePcExist();" />
																						</TD>
																						<? } ?>
																						<?php
																							if ($displayQE=='DMCLS') {
																						?>
																						<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																						<? }?>
																						<?php
																							if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																						?>
																						<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																							<input name="numLooseSlab_<?=$p?>_<?=$g?>" type="text" id="numLooseSlab_<?=$p?>_<?=$g?>" size="4" value="<?=$numLooseSlab?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyFrozenPacking','numLooseSlab_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" autocomplete="off" <?=$cellReadonly?> onkeyup="chkQePcExist();" />
																						</TD>
																						<? }?>
																					</TR>
																				</table>
																			</td>
																			<?php
																				} // Process code Loop ends here
																			?>
																		</tr>
																		<? 
																			} // Grade Loop Ends Here
																		?>					
																		<tr bgcolor="White">
																			<TD class="listing-head" align="right" style="padding-left:2px;padding-right:2px;">Total:</TD>
																			<?php
																			$p=0;
																			foreach ($selProcessCodeRecs as $pcr) {
																				$p++;
																			?>
																			<TD align="right" style="padding-left:2px;padding-right:2px;">
																				<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																					<TR>
																					<?php
																					if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																					?>
																						<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																							<input name="totalMCPack_<?=$p?>" type="text" id="totalMCPack_<?=$p?>" size="4" value="<?=$totMcPack?>" style="text-align:right;font-weight:bold;border:none;" readonly />				
																						</td>
																					<? } ?>
																					<?php
																					if ($displayQE=='DMCLS') {
																					?>
																					<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																					<? }?>
																					<?php
																					if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																					?>
																					<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																						<input name="totLooseSlab_<?=$p?>" type="text" id="totLooseSlab_<?=$p?>" size="4" value="<?=$totLooseSlab?>" style="text-align:right;font-weight:bold;border:none;" readonly />
																						<?php ?>
																						<span id="totSlabs_<?=$p?>" onMouseover="ShowTip('Total Slabs');" onMouseout="UnTip();"></span>
																						<? ?>
																					</td>
																					<? }?>
																					</TR>
																				</table>
																			</TD>
																			<?php
																				}
																			?>
																		</tr>	
																		<?php
																			 // Size of Grade Check
																			 } else if (sizeof($qeGradeRecords)>0) {
																		?>
																		<tr bgcolor="White"><TD class="err1" nowrap="true" style="padding:10 10 10 10px;">No process codes are valid for the selected day.</TD></tr>
																		<? }?>
																		<input type="hidden" name="hidPCRowCount" id="hidPCRowCount" value="<?=$p?>"/>
																		<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=$g;?>" >
																	</TD>
																</tr>
															</table>
														</TD>
													</TR>
												</table>
											</TD>
										</tr>
										<?php
										// QE List Ends Here
										}  
										?>
										<!-- 	Quick List Ends Here	 -->
										<!-- EditMode Prod packing  -->
										<?php
										if (($editMode && !$allocateMode) && ($editMode && !$convertLS)) {
										?>
										<tr><TD height="5"></TD></tr>
										<tr><TD colspan="2">
											<table>		
											<tr>
												<TD class="listing-head" nowrap="true">
												PRODUCTION DETAILS OF <?=$displayEditMsg?>
												<?php
													$leftBdr 	= "border-left: 1px solid #999999;";
													$rightBdr 	= "border-right: 1px solid #999999;";
													$topBdr 	= "border-top: 1px solid #999999;";
													$bottomBdr 	= "border-bottom: 1px solid #999999;";
													$fullBdr	= "border: 1px solid #999999;";
												?>
												</TD>
											</tr>
											<TR>
												<TD align="center">
													<table width="200" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnDtlsTble">
														<tr bgcolor="#f2f2f2"  align="center">		
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="3">&nbsp;</td>	
															<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>-->
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$fullBdr?> " class="listing-head" colspan="<?=sizeof($gradeRecs)?>">
															SLABS OF EACH GRADE/COUNT
															</td>
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
														</tr>
														<tr bgcolor="#f2f2f2"  align="center">
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO</td>
															<!--<td nowrap style="display:none;padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head"></td>-->				
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SET MC PKG</td>
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
															<?php
															$g = 1;
															foreach ($gradeRecs as $gR) {
																$gId = $gR[0];
																$gradeCode = $gR[1];
															?>
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																<?=$gradeCode?>
																<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
															</td>
															<?php
																$g++;
															}
															?>
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
															<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
														</tr>			
														<?php
														$i = 0;
														$pkgGroupArr = array();
														$lsPkgGroupArr = array();
														$mcPkgGroupArr = array();
														foreach ($productRecs as $pr) {
															$i++;
															$dFrznPkgEntryId = $pr[0];
															$frozenLotId 	= $pr[1];
															$mcPackingId	= $pr[2];
															$mcPkgCode	= $pr[3];
															$dFrznPkgMainId = $pr[4];
															$phyStkMainId=$pr[5];
															$rmLotStatus=$pr[6];
															
															$disabled="";
															$readonly="";
															if ($phyStkMainId!='')
																{
																$disabled="disabled";
																
															}
															echo $readonly;
															# Get Num of Packs
															$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);	
															?>	
															<tr bgcolor="White">				
																<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-item" align="center">
																	<table cellpadding="0" cellspacing="0">
																		<TR>
																			<TD>
																			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dFrznPkgEntryId;?>" class="chkBox" <?=$disabled?> >
																			</TD>
																			<TD class="listing-item"><?=$i?></TD>
																		</TR>
																	</table>
																	<?php if ($phyStkMainId!=''){?>
																	<div style="font-size:8px;color:red">Physical Stock </div>
																	<?}?>
																		<input type="hidden" name="physStockMainId_<?=$i?>" id="physStockMainId_<?=$i?>" value="<?=$phyStkMainId;?>" readonly="true" />
																		<input type="hidden" name="numMcPack_<?=$i?>" id="numMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />					
																		<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																		<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																		<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																</td>
																<td nowrap style="display:none;padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																	<select name="frozenLotId_<?=$i?>" id="frozenLotId_<?=$i?>" >
																		<option value="">--Select--</option>
																		<?php
																			$k=0;
																			foreach($frozenLotIdRecords as $flr) {
																				$k++;
																				$dActivityChartEntryId = $flr[1];
																				$freezer	= $flr[2];
																				$displayLotId	= $k."-".$freezer;
																				$selected	= ($dActivityChartEntryId==$lotId)?"Selected":"";
																		?>
																		<option value="<?=$dActivityChartEntryId?>" <?=$selected?>><?=$displayLotId?></option>
																		<?php
																			 }
																		?>
																	</select>
																</td>
																<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item" align="center">
																	<?php if ($phyStkMainId!='')
																	{?>
																		<input type=text name="mcPackingIdC_<?=$i?>" value="<?=$mcPkgCode?>" id="mcPackingId_<?=$i?>" readonly size="9" style="border:none;text-align:center"  />
																		<input type=hidden name="mcPackingId_<?=$i?>" value="<?=$mcPackingId?>"  readonly />
																		<input type=hidden name="hidflag_<?=$i?>" value="t"  readonly  id="hidflag_<?=$i?>" />
																	<?php } else {?>
																	<select name="mcPackingId_<?=$i?>" id="mcPackingId_<?=$i?>" onchange="xajax_assignMCPack(document.getElementById('mcPackingId_<?=$i?>').value, '<?=$i?>');"  >
																		<option value="0">--Select--</option>
																		<?php
																		  foreach($mcpackingRecords as $mcp) {
																			$mcPkgId		= $mcp[0];
																			$mcpackingCode		= stripSlash($mcp[1]);
																			$selected		= ($mcPackingId==$mcPkgId)?"selected":"";
																		?>
																		<option value="<?=$mcPkgId?>" <?=$selected?>><?=$mcpackingCode?></option>
																		<? }?>
																	</select>
																	<?php }?>
																	<input type=hidden name="hidflag_<?=$i?>" value="c" id="hidflag_<?=$i?>" readonly />
																</td>
																<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																	<table cellpadding="1" cellspacing="0" width="100%">
																		<tr>
																			<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																		</tr>
																		<tr>
																			<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																		</tr>
																		<tr>
																			<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																		</tr>
																	</table>
																</td>
																<?php
																# MC Section
																$j=0;
																$totNumMC = 0;
																$totNumLS = 0;
																foreach ($gradeRecs as $gR) {
																	$j++;
																	$sGradeId   = $gR[0];
																	$gradeCode = $gR[1];
																	# Find MC
																	if($rmLotStatus=='0')
																	{
																		list($gradeEntryId, $numMC, $numLS) = $dailyfrozenpackingObj->getSlab($dFrznPkgEntryId, $sGradeId);
																	}
																	else
																	{
																		list($gradeEntryId, $numMC, $numLS) = $dailyfrozenpackingObj->getSlabRmLotId($dFrznPkgEntryId, $sGradeId,$rmLotStatus);
																	}
																	$totNumMC += $numMC;
																	$totNumLS += $numLS;
																	$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
																	$lsPkgGroupArr[$sGradeId] += $numLS;
																	$mcPkgGroupArr[$sGradeId] += $numMC;
																	?>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																		<table cellpadding="1" cellspacing="0" width="100%">
																			<tr>
																				<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																					<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																					<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																					<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcProdnQty();" onkeydown="return nTxtBox(event,'document.frmDailyFrozenPacking','numMC_<?=$j?>_<?=$i?>');" <?php if ($phyStkMainId!='')
																					{?>
																					readonly
																					<?php }?> />
																					<input type="hidden" name="hidNumMC_<?=$j?>_<?=$i?>"  id="hidNumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" readonly />
																				</td>
																			</tr>
																			<tr>
																				<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																			</tr>
																			<tr>
																				<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																					<input type="text" name="numLS_<?=$j?>_<?=$i?>" id="numLS_<?=$j?>_<?=$i?>" size="4" value="<?=($numLS!=0)?$numLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcProdnQty();" onkeydown="return nTxtBox(event,'document.frmDailyFrozenPacking','numLS_<?=$j?>_<?=$i?>');" <?php if ($phyStkMainId!='')
																				{?>
																				readonly
																					<?php }?> />
																					<input type="hidden" name="hidNumLS_<?=$j?>_<?=$i?>" id="hidNumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($numLS!=0)?$numLS:""?>" readonly />
																				</td>
																			</tr>
																		</table>
																	</td>
																	<?php
																		} // Grade Loop Ends here

																		

																		# Total Slabs
																		$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;

																		#total Qty	
																		$totalQty	= $totalSlabs*$filledWt;
																		//echo $filledWt;
																		//echo $numPacks;
																	?>	
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																		<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																	</td>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																		<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																	</td>
																</tr>
																<?php
																	} // Loop Ends here
																?>
																<tr bgcolor="White" >
																	<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$productRecSize?>" readonly="true" />
																	<td>&nbsp;</td>
																	<td style="<?=$bottomBdr?>">&nbsp;</td>
																	<td style="<?=$bottomBdr?>">&nbsp;</td>
																	<?php					
																		foreach ($gradeRecs as $gR) {
																	?>
																	<td style="<?=$bottomBdr?>">&nbsp;</td>
																	<?php
																		}
																	?>
																	<td>&nbsp;</td>
																	<td>&nbsp;</td>
																</tr>
																<?php				
																	$fieldRowSize = $productRecSize+1;
																	$p = 1;
																	foreach ($pkgGroupArr as $pga=>$gradeArr) {					
																		$selMcPkgCode = $pga;
																?>
																<tr bgcolor="White" id="tRow_<?=$fieldRowSize+$p?>">
																	<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff;">&nbsp;</TD>
																	<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">MC PKG</TD>
																	<TD class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" align="center"><?=$selMcPkgCode?></TD>
																	<?php					
																	foreach ($gradeRecs as $gR) {
																		$gradeId = $gR[0];
																		$mcQty  = $gradeArr[$gradeId];
																	?>
																	<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>"><strong><?=($mcQty!=0)?$mcQty:"&nbsp;";?></strong></TD>
																	<?php
																		}
																	?>
																	<TD colspan="2">&nbsp;</TD>			
																</tr>
																<?php
																		$p++;
																	}
																?>
																<input type="hidden" name="hidSummaryTblRowCount" id="hidSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																<tr bgcolor="White">
																	<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff;">&nbsp;</TD>
																	<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">LS SLABS</TD>
																	<TD style="<?=$rightBdr.$bottomBdr?>">&nbsp;</TD>
																	<?php					
																		foreach ($gradeRecs as $gR) {
																			$gradeId = $gR[0];
																			$lsQty  = $lsPkgGroupArr[$gradeId];
																	?>
																	<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" id="LS_<?=$gradeId?>"><strong><?=($lsQty!=0)?$lsQty:"&nbsp;";?></strong></TD>
																	<?php
																		}
																	?>
																	<TD colspan="2">&nbsp;</TD>				
																</tr>	
																<!-- LS conversion -->
																<tr><td height="10">&nbsp;</td></tr>
																<tr bgcolor="White">
																	<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff;">&nbsp;</TD>
																	<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr.$topBdr?>">Total LS</TD>
																	<TD style="<?=$rightBdr.$bottomBdr.$topBdr?>">&nbsp;</TD>
																	<?php					
																		$lc = 0;
																		foreach ($gradeRecs as $gR) {
																			$lc++;
																			$gradeId = $gR[0];
																			$lsQty  = $lsPkgGroupArr[$gradeId];
																			$mcQty	= $mcPkgGroupArr[$gradeId];
																			$setFieldAccess = "";
																			if ($lsQty==0 && $mcQty==0) $setFieldAccess = "readonly";
																	?>
																	<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr.$topBdr?>" id="LSC_<?=$gradeId?>">
																			<input name="cGradeId_<?=$lc?>" id="cGradeId_<?=$lc?>" type="hidden" size="4" value="<?=$gradeId?>" readonly />
																			<input name="numLSC_<?=$lc?>" id="numLSC_<?=$lc?>" type="text"  size="4" value="<?=($lsQty!=0)?$lsQty:""?>" style="text-align:right;" autocomplete="off" <?=$setFieldAccess?> />
																	</TD>
																	<?php
																		}
																	?>
																	<TD colspan="2">&nbsp;</TD>				
																</tr>
															</table>
														</TD>
													</TR>			
												</table>
												<input type="hidden" name="hidProdnRowCount" id="hidProdnRowCount" value="<?=$i?>" readonly="true" />
												<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=sizeof($gradeRecs);?>" 
												readonly="true" />
												<input type="hidden" name="rmLotStatus" id="rmLotStatus" value="<?=$rmLotStatus?>" readonly="true" />	
											</TD>
										</tr>
										<?php
											# EditMode section Ends here
											}
										?>
										<!-- EditMode Prod Packing  Loose ends here -->
										<?php
										/*
										* convert LS starts here
										*/
										if ($convertLS!="") {
										
										?>
										<tr><TD height="5"></TD></tr>
										<tr>
											<TD colspan="2">
												<table>		
													<tr>
														<TD class="listing-head" nowrap="true">
															PRODUCTION DETAILS OF <?=$displayEditMsg?>
															<?php
																$leftBdr 	= "border-left: 1px solid #999999;";
																$rightBdr 	= "border-right: 1px solid #999999;";
																$topBdr 	= "border-top: 1px solid #999999;";
																$bottomBdr 	= "border-bottom: 1px solid #999999;";
																$fullBdr	= "border: 1px solid #999999;";
															?>
														</TD>
													</tr>
													<TR>
														<TD align="center">
															<table width="200" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnDtlsTble">
																<tr bgcolor="#f2f2f2"  align="center">		
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="4">&nbsp;</td>	
																	<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>-->
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$fullBdr?> " class="listing-head" colspan="<?=sizeof($gradeRecs)?>">
																		SLABS OF EACH GRADE/COUNT
																	</td>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
																</tr>
																<tr bgcolor="#f2f2f2"  align="center">
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;SL No.</td>
																	<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO</td>-->
																	<!--<td nowrap style="display:none;padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head"></td>-->	
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">RM Lot Id</td>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SET MC PKG</td>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																	<?php
																		$g = 1;
																		foreach ($gradeRecs as $gR) {
																			$gId = $gR[0];
																			$gradeCode = $gR[1];
																	?>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																	<?=$gradeCode?>
																	<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
																	</td>
																	<?php
																			$g++;
																		}
																	?>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
																	<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
																</tr>			
																<?php
																//echo "hii";
																$i = 0;
																$pkgGroupArr = array();
																$lsPkgGroupArr = array();
																$mcPkgGroupArr = array(); $lsPkgRMlotArr=array(); //$rmlotidArr=array();
																//echo	sizeof($productRecs);
																//printr($productRecs);
																foreach ($productRecs as $pr) {
																	$i++;
																	$dFrznPkgEntryId = $pr[0];
																	$frozenLotId 	= $pr[1];
																	$mcPackingId	= $pr[2];
																	$mcPkgCode	= $pr[3];
																	$dFrznPkgMainId = $pr[4];
																	$phyStkMainId=$pr[5];
																	$rmLotStatus=$pr[6];
																	$rmLotName=$pr[11];
																	$fish_id=$pr[7];
																	$process_id=$pr[8];
																	$freezing_id=$pr[9];
																	$frozenCode_id=$pr[10];
																	$companyId=$pr[13];
																	$unitId=$pr[14];
																	//echo $frozenCode_id;
																	$disabled="";
																	$readonly="";
																	if ($phyStkMainId!='')
																		{
																		$disabled="disabled";
																		
																	}
																	if($rmlotidArr=="")
																	{
																		$rmlotidArr=$rmLotStatus;
																	}
																	else
																	{
																		if($rmLotPrev!=$rmLotStatus)
																		{
																			$rmlotidArr.=','.$rmLotStatus;
																		}
																	}
																	echo $readonly;
																	# Get Num of Packs
																	$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);	
																	
																	?>	
																	<tr bgcolor="White">				
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-item" align="center">
																			<table cellpadding="0" cellspacing="0">
																				<TR>
																					<!--		<TD>
																						<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dFrznPkgEntryId;?>" class="chkBox" <?=$disabled?> >
																					</TD>-->
																					<input name="fishIdCvt_<?=$i?>" id="fishIdCvt_<?=$i?>" type="hidden" value='<?=$fish_id?>'/>
																					<input name="processIdCvt_<?=$i?>" id="processIdCvt_<?=$i?>" type="hidden" value='<?=$process_id?>'/>
																					<input name="freezingIdCvt_<?=$i?>" id="freezingIdCvt_<?=$i?>" type="hidden" value='<?=$freezing_id?>'/>
																					<input name="frozenCodeIdCvt_<?=$i?>" id="frozenCodeIdCvt_<?=$i?>" type="hidden" value='<?=$frozenCode?>'/>
																					<input name="companyIdCvt_<?=$i?>" id="companyIdCvt_<?=$i?>" type="hidden" value='<?=$companyId?>'/>
																					<input name="unitIdCvt_<?=$i?>" id="unitIdCvt_<?=$i?>" type="hidden" value='<?=$unitId?>'/>
																					<TD class="listing-item"><?=$i?></TD>
																				</TR>
																			</table>
																			<?php if ($phyStkMainId!=''){?>
																			<div style="font-size:8px;color:red">Physical Stock </div>
																			<?}?>
																			<input type="hidden" name="physStockMainIdCvt_<?=$i?>" id="physStockMainIdCvt_<?=$i?>" value="<?=$phyStkMainId;?>" readonly="true" />
																			<input type="hidden" name="numMcPackCvt_<?=$i?>" id="numMcPackCvt_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />					
																			<input type="hidden" name="hidNumMcPackCvt_<?=$i?>" id="hidNumMcPackCvt_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																			<input type="hidden" name="dFrznPkgEntryIdCvt_<?=$i?>" id="dFrznPkgEntryIdCvt_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																			<input type="hidden" name="dFrznPkgMainIdCvt_<?=$i?>" id="dFrznPkgMainIdCvt_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																		<!--</td>-->
																		<td nowrap style="display:none;padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																			<select name="frozenLotId_<?=$i?>" id="frozenLotId_<?=$i?>"  >
																			<option value="">--Select--</option>
																			<?php
																				$k=0;
																				foreach($frozenLotIdRecords as $flr) {
																					$k++;
																					$dActivityChartEntryId = $flr[1];
																					$freezer	= $flr[2];
																					$displayLotId	= $k."-".$freezer;
																					$selected	= ($dActivityChartEntryId==$lotId)?"Selected":"";
																			?>
																			<option value="<?=$dActivityChartEntryId?>" <?=$selected?>><?=$displayLotId?></option>
																			<?php
																				 }
																			?>
																			</select>
																		</td>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item"><?=$rmLotName?>
																			<input type="hidden" name="rmLotStatusCvt_<?=$i?>" id="rmLotStatusCvt_<?=$i?>" value="<?=$rmLotStatus?>" readonly="true" />	
																		</td>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item" align="center">
																			<?php if ($phyStkMainId!='')
																			{?>
																				<input type=text name="mcPackingIdCvt_<?=$i?>" value="<?=$mcPkgCode?>" id="mcPackingIdCvt_<?=$i?>" readonly size="9" style="border:none;text-align:center"  />
																				<input type=hidden name="mcPackingIdCvt_<?=$i?>" value="<?=$mcPackingId?>"  readonly />
																				<input type=hidden name="hidflagCvt_<?=$i?>" value="t"  readonly  id="hidflagCvt_<?=$i?>" />
																			<?php } else {?>
																				<select name="mcPackingIdCvt_<?=$i?>" id="mcPackingIdCvt_<?=$i?>" onchange="xajax_assignMCPack(document.getElementById('mcPackingIdCvt_<?=$i?>').value, '<?=$i?>');" disabled >
																					<option value="0">--Select--</option>
																					 <?php
																					  foreach($mcpackingRecords as $mcp) 
																					  {
																							$mcPkgId		= $mcp[0];
																							$mcpackingCode		= stripSlash($mcp[1]);
																							$selected		= ($mcPackingId==$mcPkgId)?"selected":"";
																						?>
																						<option value="<?=$mcPkgId?>" <?=$selected?>><?=$mcpackingCode?></option>
																						<? }?>
																				</select>
																				<?php }?>
																				<input type=hidden name="hidflag_<?=$i?>" value="c" id="hidflag_<?=$i?>" readonly />
																			</td>
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																				<table cellpadding="1" cellspacing="0" width="100%">
																					<?php /*<? if($i==1) { ?>
																					<tr>
																						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																					</tr>
																					<tr>
																						<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																					</tr>
																					<?php
																					}
																					?>
																					*/?>
																					<tr>
																						<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																					</tr>
																				</table>
																			</td>
																			<?php
																			# MC Section
																			$j=0;
																			$totNumMC = 0;
																			$totNumLS = 0; 
																			foreach ($gradeRecs as $gR) {
																				$j++;
																				$sGradeId   = $gR[0];
																				$gradeCode = $gR[1];
																				# Find MC
																				if($rmLotStatus=='0')
																				{
																					list($gradeEntryId, $numMC, $numLS) = $dailyfrozenpackingObj->getSlab($dFrznPkgEntryId, $sGradeId);
																				}
																				else
																				{
																					list($gradeEntryId, $numMC, $numLS) = $dailyfrozenpackingObj->getSlabRmLotId($dFrznPkgEntryId, $sGradeId,$rmLotStatus);
																				}
																				if($i==1)
																				{
																					if($GradeIdArr=="")
																					{
																						$GradeIdArr=$sGradeId;
																					}
																					else
																					{
																						$GradeIdArr.=','.$sGradeId;
																					}
																				}
																				$totNumMC += $numMC;
																				$totNumLS += $numLS;
																				$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
																				$lsPkgGroupArr[$sGradeId] += $numLS;
																				//$mcPkgGroupArr[$sGradeId] += $numMC;
																				
																				if($rmLotPrev=="")
																				{
																					$lsPkgRMlotArr[$rmLotStatus][$sGradeId]=$numLS;
																					
																				}
																				else
																				{
																					if($rmLotPrev!=$rmLotStatus)
																					{
																						$lsPkgRMlotArr[$rmLotStatus][$sGradeId].=$numLS;
																						
																					}
																					else
																					{
																						$lsPkgRMlotArr[$rmLotStatus][$sGradeId] += $numLS;
																						//$lsPkgRMlotArr[$sGradeId][$rmLotName].= $numLS;
																														
																					}
																					//$lsPkgRMlotArr[$sGradeId][$rmLotName].=$numLS;
																				}
																				// echo $lsPkgRMlotArr[$sGradeId];
																				?>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																					<table cellpadding="1" cellspacing="0" width="100%">
																					<?php/*			<? if($i==1) { ?>
																						<tr>
																							<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																								<input name="numMCCvt_<?=$j?>" type="text" id="numMCCvt_<?=$j?>" size="4" value="" style="text-align:right;" autocomplete="off" onkeyup="calcProdnQty();" onkeydown="return nTxtBox(event,'document.frmDailyFrozenPacking','numMCCvt_<?=$j?>');"  readonly />
																								<input type="hidden" name="hidNumMC_<?=$j?>_<?=$i?>"  id="hidNumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" readonly />
																								<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																								<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																								<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcProdnQty();" onkeydown="return nTxtBox(event,'document.frmDailyFrozenPacking','numMC_<?=$j?>_<?=$i?>');"  readonly />
																								<input type="hidden" name="hidNumMC_<?=$j?>_<?=$i?>"  id="hidNumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" readonly />-->
																							</td>
																						</tr>
																						<tr>
																							<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																						</tr>
																						<? } ?>
																						*/?>
																						<tr>
																							<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																								<input type="hidden" name="sGradeIdCvt_<?=$j?>_<?=$i?>" id="sGradeIdCvt_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																								<input type="text" name="numLSCvt_<?=$j?>_<?=$i?>" id="numLSCvt_<?=$j?>_<?=$i?>" size="4" value="<?=($numLS!=0)?$numLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcProdnQty();" onkeydown="return nTxtBox(event,'document.frmDailyFrozenPacking','numLSCvt_<?=$j?>_<?=$i?>');" readonly="true" />
																								<input type="hidden" name="hidNumLSCvt_<?=$j?>_<?=$i?>" id="hidNumLSCvt_<?=$j?>_<?=$i?>" size="4" value="<?=($numLS!=0)?$numLS:""?>" readonly />
																							</td>
																						</tr>
																					</table>
																				</td>
																				<?php
																					} // Grade Loop Ends here
																					# Total Slabs
																					//$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;
																					$totalSlabs 	=$totNumLS;	
																					#total Qty	
																					$totalQty	= $totalSlabs*$filledWt;
																					//echo $filledWt;
																					//echo $numPacks;
																				?>	
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																					<input name="totalSlabsCvt_<?=$i?>" type="text" id="totalSlabsCvt_<?=$i?>" size="4" value="<?=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																				</td>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																					<input type="text" name="totalQtyCvt_<?=$i?>" id="totalQtyCvt_<?=$i?>" size="6" value="<?=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																				</td>
																			</tr>
																			<?php
																			$rmLotPrev=$rmLotStatus;
																		
																			} // Loop Ends here
																			?>
																			<tr bgcolor="White" >
																				<input type="hidden" name="hidTableRowCountCvt" id="hidTableRowCountCvt" value="<?=$productRecSize?>" readonly="true" />
																				<td>&nbsp;</td>
																				<td style="<?=$bottomBdr?>">&nbsp;</td>
																				<td style="<?=$bottomBdr?>">&nbsp;</td>
																				<?php					
																				foreach ($gradeRecs as $gR) {
																				?>
																				<td style="<?=$bottomBdr?>">&nbsp;</td>
																				<?php
																					}
																				?>
																				<td>&nbsp;</td>
																				<td>&nbsp;</td>
																			</tr>
																			<?php				
																			$fieldRowSize = $productRecSize+1;
																			$p = 1;
																			foreach ($pkgGroupArr as $pga=>$gradeArr) {					
																				$selMcPkgCode = $pga;
																			?>
																			<!--<tr bgcolor="White" id="tRow_<?=$fieldRowSize+$p?>">
																				<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff;">&nbsp;</TD>
																				<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">MC PKG</TD>
																				<TD class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" align="center"><?=$selMcPkgCode?></TD>
																				<?php					
																					foreach ($gradeRecs as $gR) {
																						$gradeId = $gR[0];
																						//$mcQty  = $gradeArr[$gradeId];
																				?>
																				<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>"><strong><?=($mcQty!=0)?$mcQty:"&nbsp;";?></strong></TD>
																				<?php
																					}
																				?>
																				<TD colspan="2">&nbsp;</TD>			
																			</tr>-->
																			<?php
																					$p++;
																				}
																			?>
																			<input type="hidden" name="hidSummaryTblRowCountCvt" id="hidSummaryTblRowCountCvt" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																			<tr bgcolor="White">
																				<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff;">&nbsp;</TD>
																				<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">LS SLABS</TD>
																				<TD class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" align="center"><?=$selMcPkgCode?></TD>
																				<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">&nbsp;</TD>
																				<?php					
																					foreach ($gradeRecs as $gR) {
																						$gradeId = $gR[0];
																						$lsQty  = $lsPkgGroupArr[$gradeId];
																				?>
																				<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" id="LSCvt_<?=$gradeId?>"><strong><?=($lsQty!=0)?$lsQty:"&nbsp;";?></strong></TD>
																				<?php
																					}
																				?>
																				<TD colspan="2">&nbsp;</TD>				
																			</tr>	
																			<!-- LS conversion -->
																			<tr><td height="10">&nbsp;</td></tr>
																			<tr bgcolor="White">
																				<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff;">&nbsp;</TD>
																				<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr.$topBdr?>">Total LS</TD>
																				<TD style="<?=$rightBdr.$bottomBdr.$topBdr?>">&nbsp;</TD>
																				<TD style="<?=$rightBdr.$bottomBdr.$topBdr?>">&nbsp;</TD>
																				<?php					
																					$lc = 0; //$gradeAll=array();
																					foreach ($gradeRecs as $gR) {
																						$lc++;
																						$gradeId = $gR[0];
																						$lsQty  = $lsPkgGroupArr[$gradeId];
																						$mcQty	= $mcPkgGroupArr[$gradeId];
																						$setFieldAccess = "";
																						if ($lsQty==0 && $mcQty==0) $setFieldAccess = "readonly";
																				?>
																				<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px;<?=$rightBdr.$bottomBdr.$topBdr?>" id="LSCCvt_<?=$gradeId?>" readonly>
																						<input name="cGradeIdCvt_<?=$lc?>" id="cGradeIdCvt_<?=$lc?>" type="hidden" size="4" value="<?=$gradeId?>" readonly />
																						<input name="numLSCCvt_<?=$lc?>" id="numLSCCvt_<?=$lc?>" type="text"  size="4" value="<?=($lsQty!=0)?$lsQty:""?>" style="text-align:right;" autocomplete="off" <?=$setFieldAccess?> readonly />
																				</TD>
																				<?php
																					}
																				?>
																				<TD colspan="2">&nbsp;</TD>				
																			</tr>
																		</table>
																	</TD>
																</TR>
																<tr>
																	<td>
																		<table>
																		<?php //printr($lsPkgRMlotArr).'/';
																			//printr($rmlotidArr).'<br/>';
																			//printr($GradeIdArr);
																		//echo	sizeof($rmlotidArr).'<br/>';
																		sizeof($lsPkgRMlotArr);
																		$rm=explode(',',$rmlotidArr);
																		$grd=explode(',',$GradeIdArr);
																		$rmlt=sizeof($rm);
																		for($i=0; $i<=sizeof($rmlt); $i++)
																		{
																		?>
																			<tr>
																				<td colspan="3" width="172px">&nbsp;</td>
																					<?php
																					 $rmlotid=$rm[$i];
																					//printr($lsPkgRMlotArr[$rmlotid]);
																					$lspkg= sizeof($lsPkgRMlotArr[$rmlotid]);
																					?>
																					<td><input type="hidden"  name="totalRM_<?=$i?>" id="totalRM_<?=$i?>" value="<?=$rmlotid?>" size="7"></td>
																					<?php
																					for($j=0; $j<$lspkg; $j++)
																					{
																						$grid=$grd[$j];
																						$lspkgrmLot=$lsPkgRMlotArr[$rmlotid][$grid];
																						//filter by grade;
																						//printr($lsPkgRMlotArr[$rmlotid][$grid]);
																						if($i==0)
																						{
																						?>
																						<input type="hidden"  name="grdRM_<?=$j?>_<?=$i?>" id="grdRM_<?=$j?>_<?=$i?>" value="<?=$grid?>" size="7">
																						<?php
																						}
																						
																					?>
																					<td> <input type="hidden"  name="lstotalRM_<?=$j?>_<?=$i?>" id="lstotalRM_<?=$j?>_<?=$i?>" value="<?=$lspkgrmLot?>" size="7"></td>
																					<?php
																					}
																					?>
																				</tr>	
																				<?php
																				}
																				?>	
																				<tr><td><input type="hidden" name="rmsz" id="rmsz" value="<?=$rmlt?>"></td></tr>	
																			</table>	
																		</td>
																	</tr>
																	<tr>
																		<td  class="listing-head" nowrap="true"><U>CREATE MC</U></td>
																	</tr>
																	<tr>
																		<td  class="listing-head" nowrap="true">
																			<table cellspacing="1" bgcolor="#999999" cellpadding="3"  align="left" id="tblConvertLS">
																				<tr bgcolor="#f2f2f2"  align="center">
																					<td nowrap style="padding-left:2px;padding-right:2px; " class="listing-head">&nbsp;SL No.</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; " class="listing-head">RM Lot Id</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; " class="listing-head">SET MC PKG</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; " class="listing-head">LS</td>
																					<?php
																					$g = 0;
																					foreach ($gradeRecs as $gR) {
																						$gId = $gR[0];
																						$gradeCode = $gR[1];
																						if($gradeIdAll=="")
																						{
																							$gradeIdAll=$gId;
																							$gradeAll=$gradeCode;
																						}
																						else
																						{
																							$gradeIdAll.='/'.$gId;
																							$gradeAll.='/'.$gradeCode;
																						}
																					?>
																					<td nowrap style="padding-left:2px;padding-right:2px; " class="listing-head">
																						<?=$gradeCode?>
																						<input type="hidden" name="gIdrm_<?=$g?>" id="gIdrm_<?=$g?>" value="<?=$gId?>" readonly="true" />
																					</td>
																					<?php
																					$g++;
																					}
																				?>
																			<input type="hidden" name="gIdRMSz" id="gIdRMSz" value="<?=$g?>" readonly="true" />
																			<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>-->
																			</tr>
																		</table>
																	<input type='hidden' name="hidLSRowCount" id="hidLSRowCount" value="<?=$l?>">		
																	</td>
																</tr>
																<tr><td colspan='4' height="10"></td></tr>
																<tr >
																	<TD style="padding-left:5px;padding-right:5px;" colspan="4">
																		<a href="###" id='addRow' onclick="javascript:addNewMC();"  class="link1" title="Click here to duplicate value."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New<!--(Copy)--></a>
																	</TD>
																</tr>
																<tr>
																	<td width="40" colspan="4"><span class="fieldName" style="color:red; line-height:normal" id="message" name="message"></span></td>
																<tr>
																<tr>
																	<td id="newRMlot">
																	</td>
																</tr>
															</table>
															<input type="hidden" name="hidProdnRowCountCvt" id="hidProdnRowCountCvt" value="<?=$i?>" readonly="true" />
															<input type="hidden" name="hidGradeRowCountCvt" id="hidGradeRowCountCvt" value="<?=sizeof($gradeRecs);?>" 
															readonly="true" />
														</TD>
													</tr>
													<?php
														#convertLS section Ends here
														} 
													?>
													<!-- Allocation Starts here -->
													<?php
														/*
														* Allocation starts here
														*/
														if ($allocateMode) {
													?>
													<tr><TD height="5"></TD></tr>
													<tr>
														<TD colspan="2">
															<table>		
																<tr>
																	<TD class="listing-head" nowrap="true">
																		PRODUCTION DETAILS OF <?=$displayEditMsg?>
																		<?php
																			$leftBdr 	= "border-left: 1px solid #999999;";
																			$rightBdr 	= "border-right: 1px solid #999999;";
																			$topBdr 	= "border-top: 1px solid #999999;";
																			$bottomBdr 	= "border-bottom: 1px solid #999999;";
																			$fullBdr	= "border: 1px solid #999999;";
																		?>
																	</TD>
																</tr>
																<TR>
																	<TD align="center">
																		<table width="200" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnAllocateTble">
																			<tr bgcolor="#f2f2f2"  align="center">		
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="3">&nbsp;</td>	
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$fullBdr?> " class="listing-head" colspan="<?=sizeof($gradeRecs)?>">
																					SLABS OF EACH GRADE/COUNT
																				</td>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
																			</tr>
																			<tr bgcolor="#f2f2f2"  align="center">
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head" title="Set Purchase order">SET PO</td>				
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																				<?php
																					$g = 1;
																					foreach ($gradeRecs as $gR) {
																						$gId = $gR[0];
																						$gradeCode = $gR[1];
																				?>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																						<?=$gradeCode?>
																						<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
																					</td>
																				<?php
																						$g++;
																					}
																				?>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
																			</tr>
																			<tr bgcolor="#f2f2f2"  align="center">
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="2">RM Used</td>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																					<table cellpadding="1" cellspacing="0" width="100%">
																						<tr>
																							<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																						</tr>
																						<tr>
																							<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																						</tr>
																						<tr>
																							<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																						</tr>
																					</table>
																				</td>
																				<?php
																					foreach ($gradeRecs as $gR) {
																						$j++;
																						$sGradeId   = $gR[0];
																						list($availableMC, $availableLS) = $dailyfrozenpackingObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId);
																				?>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																					<table cellpadding="1" cellspacing="0" width="100%">
																						<tr>
																							<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																								<b><?=($availableMC!=0)?$availableMC:"&nbsp;"?></b>
																							</td>
																						</tr>
																						<tr>
																							<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																						</tr>
																						<tr>
																							<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																								<b><?=($availableLS!=0)?$availableLS:"&nbsp;"?></b>
																							</td>
																						</tr>
																					</table>
																				</td>
																				<?php
																					} // Grade Loop Ends here
																				?>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																			</tr>
																			<?php
																			$i = 0;
																			$pkgGroupArr = array();
																			$lsPkgGroupArr = array();
																			$mcPkgGroupArr = array();
																			foreach ($productRecs as $pr) {
																				$i++;
																				$dFrznPkgEntryId = $pr[0];
																				$frozenLotId 	= $pr[1];
																				$mcPackingId	= $pr[2];
																				$mcPkgCode	= $pr[3];
																				$dFrznPkgMainId = $pr[4];
																				$POEntryId = $pr[5]; 
																				$allocatePOId = $pr[6];
																				# Get Num of Packs
																				
																				?>	
																				<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">				
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-item" align="center">
																						<table cellpadding="0" cellspacing="0">
																							<TR>
																								<TD style="padding-left:5px;padding-right:5px;">
																									<a onclick="setAllocateRowStatus(this);" href="javascript:void(0);" id="allocateRemoveLink_<?=$i?>"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
																								</TD>
																							</TR>
																						</table>										
																						<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																						<input type="text" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																						<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																						<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />
																					</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">					
																						<select name="POId_<?=$i?>" id="POId_<?=$i?>" style="width:120px;" onchange="changePO(this);">
																							<option value="">--Select--</option>
																							<?php
																								$k=0;
																								foreach($purchaseOrders as $por) {
																									$poId	= $por[0];
																									$poDate = $por[3];
																									$selected = ($allocatePOId==$poId)?"selected":"";
																							?>
																							<option value="<?=$poId?>" <?=$selected?>><?=$poDate?></option>
																							<?php
																								 }
																							?>
																						</select>
																						<?php
																						if ($allocatePOId>0) {
																						?>
																						<script>
																						getPOItems('<?=$allocatePOId?>', '<?=$i?>');
																						</script>
																						<?php }?>
																						<div id="ViewPOItems" style="padding:2px;"><a href="javascript:void(0);" id="viewPOForAllocation_<?=$i?>" onclick="viewPO('<?=$allocatePOId?>')" class="link1">View</a></div>
																					</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																						<table cellpadding="1" cellspacing="0" width="100%">
																							<tr>
																								<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																							</tr>
																							<tr>
																								<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																							</tr>
																							<tr>
																								<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																							</tr>
																						</table>
																					</td>
																					<?php
																						# MC Section
																						$j=0;
																						$totNumMC = 0;
																						$totNumLS = 0;
																						foreach ($gradeRecs as $gR) {
																							$j++;
																							$sGradeId   = $gR[0];
																							$gradeCode = $gR[1];
																							# Find MC
																							$allocateGradeEntryId = "";
																							if ($POEntryId>0) {
																								list($allocateGradeEntryId, $numMC, $numLS) = $dailyfrozenpackingObj->getAllocatedSlab($POEntryId, $sGradeId);
																							} else {
																								list($gradeEntryId, $numMC, $numLS) = $dailyfrozenpackingObj->getSlab($dFrznPkgEntryId, $sGradeId);
																							}
																							$totNumMC += $numMC;
																							$totNumLS += $numLS;
																							$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
																							$lsPkgGroupArr[$sGradeId] += $numLS;
																							$mcPkgGroupArr[$sGradeId] += $numMC;
																						?>
																						<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																							<table cellpadding="1" cellspacing="0" width="100%">
																								<tr>
																									<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																										<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																										<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																										<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																										<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />			
																									</td>
																								</tr>
																								<tr>
																									<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																								</tr>
																								<tr>
																									<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																										<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" value="<?=($numLS!=0)?$numLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																									</td>
																								</tr>
																							</table>
																						</td>
																						<?php
																						} // Grade Loop Ends here

																						# Total Slabs
																						$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;

																						#total Qty	
																						$totalQty	= $totalSlabs*$filledWt;
																						?>	
																						<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																							<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																						</td>
																						<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																							<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																						</td>
																					</tr>
																					<?php
																						} // Loop Ends here
																					?>
																					<tr><td height="10"></td></tr>
																					<tr>
																						<td style="padding-left:10px;padding-right:10px;" nowrap colspan="2">
																							<a href="javascript:void(0);" id='addRow' onclick="javascript:addAllocateRow();" class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New</a>		
																						</td>
																					</tr>
																					<tr bgcolor="White" >
																						<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																						<td>&nbsp;</td>
																						<td style="<?=$bottomBdr?>">&nbsp;</td>
																						<td style="<?=$bottomBdr?>">&nbsp;</td>
																						<?php					
																							foreach ($gradeRecs as $gR) {
																						?>
																							<td style="<?=$bottomBdr?>">&nbsp;</td>
																						<?php
																							}
																						?>
																						<td>&nbsp;</td>
																						<td>&nbsp;</td>
																					</tr>
																					<?php				
																						$fieldRowSize = $productRecSize+1;
																						$p = 1;
																						foreach ($pkgGroupArr as $pga=>$gradeArr) {					
																							$selMcPkgCode = $pga;
																					?>
																					<tr bgcolor="White" id="tRow_<?=$fieldRowSize+$p?>">
																						<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff;">&nbsp;</TD>
																						<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">MC PKG</TD>
																						<TD style="<?=$rightBdr.$bottomBdr?>">&nbsp;</TD>
																						<?php					
																							foreach ($gradeRecs as $gR) {
																								$gradeId = $gR[0];
																								$mcQty  = $gradeArr[$gradeId];
																						?>
																						<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>"><strong><?=($mcQty!=0)?$mcQty:"&nbsp;";?></strong></TD>
																						<?php
																							}
																						?>
																						<TD colspan="2">&nbsp;</TD>			
																					</tr>
																					<?php
																							$p++;
																						}
																					?>
																					<input type="hidden" name="hidAllocateSummaryTblRowCount" id="hidAllocateSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																					<tr bgcolor="White">
																						<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff;">&nbsp;</TD>
																						<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">LS SLABS</TD>
																						<TD style="<?=$rightBdr.$bottomBdr?>">&nbsp;</TD>
																						<?php					
																							foreach ($gradeRecs as $gR) {
																								$gradeId = $gR[0];
																								$lsQty  = $lsPkgGroupArr[$gradeId];
																						?>
																						<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" id="LS_<?=$gradeId?>"><strong><?=($lsQty!=0)?$lsQty:"&nbsp;";?></strong></TD>
																						<?php
																							}
																						?>
																						<TD colspan="2">&nbsp;</TD>				
																					</tr>
																				</table>
																			</TD>
																		</TR>		
																	</table>
																	<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
																	<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																</TD>
															</tr>
															<?php
															# Allocation section Ends here
															}
															?>
															<!-- Allocation ends here-->
														</table>
													</td>
												</tr>
												<tr>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
												</tr>
												<tr>
													<? if($editMode){?>
													<td align="center">
														<? if(($editMode && !$allocateMode) && ($editMode && !$convertLS)){?>
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
														<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);">	
														<? if($del==true){?>
														&nbsp;&nbsp;
														<input type="submit" value=" Delete " class="button"  name="cmdEntryDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productRecSize;?>);">
														<? }?>
														<?php } else if ($allocateMode) {?>
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
														<input type="submit" name="cmdAllocation" class="button" value=" Allocate " onClick="return validateDFPAllocation(document.frmDailyFrozenPacking);">	
														<?php } 
														else if ($convertLS) {?>
															<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
															<?php }?>
														&nbsp;&nbsp;
														<?php if (!$convertLS)
														{ ?>
															<input type="submit" value=" Convert LS to MC & Save " class="button"  name="cmdConvertLS2MC" id="cmdConvertLS2MC" onClick="return convertLS2MC();">
														<?php
														}
														else
														{
														?>
															<!--<input type="button" value=" Convert LS to MC" class="button"  name="cmdConvert2MC" onClick="return calcProdnQtyLS();">-->
															<input type="button" value=" Convert LS to MC" class="button"  name="cmdConvert2MC" id="cmdConvert2MC" onClick="return convert2MC();">
														<?php
														}
														?>
														<input type="submit" value="Save" class="button"  name="cmdConvertLSSave" id="cmdConvertLSSave"  style="display:none;">
													</td>
													<? } else{?>
													<td align="center">
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenPacking.php<?=$selection?>');">&nbsp;&nbsp;
														<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" style="width:100px;">&nbsp;&nbsp;
														<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:130px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New">
														<? if ($selQuickEntryList!="") {?>
																&nbsp;&nbsp;<input name="cmdSaveAndQE" type="submit" class="button" id="cmdSaveAndQE" style="width:200px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New Quick Entry">	
														<? }?>
													</td>
													<? }?>
												</tr>
												<tr>
													<td  height="10" ></td>
												</tr>
											</table>
										</td>
									</tr>
									<?php
									} // Entry Selection
									?>
								</table>
							</td>
						</tr>
					</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<?
		}
		# Listing Grade Starts
		?>
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Frozen Packing </td>
								    <td background="images/heading_bg.gif" class="pageName" align="right" >
										<table cellpadding="0" cellspacing="0" width="200">
                     						<tr> 
			  									<td class="listing-item"> As&nbsp;On:</td>
												<td nowrap="nowrap"> 
												<input type="text" id="frozenPackingFrom" name="frozenPackingFrom" size="8" value="<?=$dateFrom?>"></td>
												<td class="listing-item">&nbsp;</td>
												<td class="listing-item">Till:</td>
												<td> 
												 <? 
												 if($dateTill=="") $dateTill=date("d/m/Y");
												  ?>
												  <input type="text" id="frozenPackingTill" name="frozenPackingTill" size="8"  value="<?=$dateTill?>"></td>
												<td class="listing-item">&nbsp;</td>

												<td class="listing-item" nowrap="nowrap">Process Code:</td>

																			<td>
																			
																				<select name="processCode" id="processCode" >
																				
																					<option value="">-- Select --</option>
																					<?
																					foreach ($processCodeRecords as $fl)
																					{
																						$processCodeId		=	$fl[0];
																						$processCode		=	$fl[2];
																						$selected	=	"";
																						if( $filterProcessCode==$processCodeId){
																						$selected	=	"selected";
																						}
																					?>
																					<option value="<?=$processCodeId;?>" <?=$selected;?> >
																					<?=$processCode;?>
																					</option>
																				<?
																				}
																				?>
																				</select>
																			</td>


												<td class="listing-item">&nbsp;</td>

												<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search " onclick="return validateDailyFrozenPackingSearch(document.frmDailyFrozenPacking);"></td>
												<td class="listing-item" nowrap >&nbsp;</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
												<? if($del==true){?>
													<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return cfmDelete(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);">
												<? }?>
												&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFrozenPacking.php?frozenPackingFrom=<?=$dateFrom?>&frozenPackingTill=<?=$dateTill?>',700,600);"><? }?>
												
												<input type="submit" value=" Convert LS to MCs " class="button"  name="convertLS" onClick="return cfmConvertLS(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);">
												</td>
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
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" style="padding-left:5px; padding-right:5px;" >
										<table cellpadding="1"  width="85%" cellspacing="1" border="0" align="center" bgcolor="#999999">
										<?
										if (sizeof($dailyFrozenPackingRecs)>0) {
											$i	=	0;
										?>
										 <? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF">
												<td colspan="17" style="padding-right:10px">
													<div align="right">
													<?php 				 			  
													$nav  = '';
													for($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"DailyFrozenPacking.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"DailyFrozenPacking.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
													}
													if ($pageNo < $maxpage)	{
														$page = $pageNo + 1;
														$next = " <a href=\"DailyFrozenPacking.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
													} else {
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
													?>	
													<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
												</div>
											</td>
										</tr>
										<? }?>
										<tr  bgcolor="#f2f2f2" align="center">
											<td width="20">
											<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delGId_');" class="chkBox">	</td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">RM LOT ID</td>
											<!--<td class="listing-head" style="padding-left:5px;padding-right:5px;">Available Qty</td>-->
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Process Code</td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Stage</td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Code</td>
											<!--<td class="listing-head" style="padding-left:5px;padding-right:5px;">For Pkg</td>-->
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">No.of<br> MCs</td>	
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">MC Pkg</td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;"><p>No.of<br />
											LS</p>
											 </td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Qty</td>	
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Pkd Qty</td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">RM Used</td>		
											<!--<td class="listing-head" style="padding-left:5px;padding-right:5px;">RePkdQty</td>-->
											<!--<td class="listing-head" style="padding-left:5px;padding-right:5px;">Allocated</td>-->
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">View</td>
											<?php 
											if ($edit==true) {
											?>
											<td class="listing-head" width="45">&nbsp;</td>
											<?php
												}
											?>
										</tr>
										<?php
										
										$totalPkdQty = 0;
										$totalFrozenQty = 0;
										$totalActualQty = 0;
										$totNumMCs = 0;
										$totNumLSs = 0;
										
										$c=0;
										$repkdQty=0;
										foreach ($dailyFrozenPackingRecs as $dfpr) 
										{
											$i++;
											$c++;
											$dailyFrozenPackingMainId	= $dfpr[0];
											$selProcessCodeId	= $dfpr[3];
											$selProcessCode		= $dfpr[6];	
											$selFreezingStageId	= $dfpr[4];
											$selFreezingStage	= $dfpr[7];
											$selFrozenCodeId	= $dfpr[5];
											$selFrozenCode		= $dfpr[8];
											$reportConfirmed	= $dfpr[9];
											$allocatedCount		= $dfpr[10];
											$selMCPackingId		= $dfpr[11];
											$selMCPkgCode	    = $dfpr[12];
											$repackId			= $dfpr[19];
											$numLS				= $dfpr[20];
											if($dfpr[21]!='0')
											{
												$rmLotIDNm			= $dfpr[21];
											}
											else
											{
												$rmLotIDNm			='';
											}
											$rmLotID			= $dfpr[22];
											$frozen_entry_id=$dfpr[23];
											$unit_id=$dfpr[24];
											$company_id=$dfpr[25];
											//echo $repackId;
											if ($repackId==""){
											//$pkdQty=$dfpr[13];
											$dpkdQty=$dfpr[13];
											$repkdQty=0;
											}
											else{
											$repkdQty=$dfpr[13];
											$dpkdQty=0;
											}
											
											$numMCs=$dfpr[14];
											$frozenQty=$dfpr[15];
											 $actualQty=$dfpr[16];
											 $physstockid=$dfpr[17];
											 $sumphysstockid=$dfpr[18];
											$disabled = "";
											$disableddel="";
											$allocateDisabled = "";
											
											if ($sumphysstockid!="")
											{
												
												$disableddel="disabled";
											}
											else if ($physstockid!="")
											{
												$disabled = "disabled";
												
											}
											if ($reportConfirmed=='Y' && $reEdit==false) {
												$disabled = "disabled";
											}
											if ($allocatedCount!="" && $allocatedCount>0) {
												$disabled = "disabled";
												$allocateDisabled = "disabled";

											}

											#Find Number of packing Details
											###rmlotid concept pending
											if($rmLotID=="0")
											{
												list($pkdQty, $numMCs, $frozenQty, $actualQty) = $dailyfrozenpackingObj->getPkdQty($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selMCPackingId);
											}
											else
											{
												list($pkdQty, $numMCs, $frozenQty, $actualQty) = $dailyfrozenpackingObj->getPkdQtyRmlotId($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selMCPackingId,$rmLotID);
											}
											//$totalPkdQty += $pkdQty;
											$totalPkdQty += $dpkdQty;
											$retotalPkdQty += $repkdQty;
											$totalFrozenQty += $frozenQty;
											$totalActualQty += $actualQty;
											$totNumMCs += $numMCs;
											$totNumLSs+=$numLS;

											# Edit criteria
											$selEditCriteria = "$selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selProcessCode - $selFreezingStage - $selFrozenCode - $selMCPkgCode, $selMCPackingId";
										?>
										<tr <?=$listRowMouseOverStyle?>>
											<td width="20">
												<input type="checkbox" name="delGId_<?=$i;?>" id="delGId_<?=$i;?>" <?php if (!$disableddel) { ?> value="<?=$selEditCriteria;?>" <?php } else{?>  value="" <?php }?> class="chkBox" <?=$disableddel?>>
												<input type="hidden" name="dfpMainId_<?=$i;?>" id="dfpMainId_<?=$i;?>" value="<?=$dailyFrozenPackingMainId?>" readonly>	
												<input type="hidden" name="rmLotID_<?=$i;?>" id="rmLotID_<?=$i;?>" value="<?=$rmLotID?>" readonly>
												<input type="hidden" name="frozenEntryId_<?=$i;?>" id="frozenEntryId_<?=$i;?>" value="<?=$frozen_entry_id?>" readonly>
												<input type="hidden" name="numLS_<?=$i;?>" id="numLS_<?=$i;?>" value="<?=$numLS?>" readonly>
												<input type="hidden" name="companyId_<?=$i;?>" id="companyId_<?=$i;?>" value="<?=$company_id?>" readonly>
												<input type="hidden" name="unitId_<?=$i;?>" id="unitId_<?=$i;?>" value="<?=$unit_id?>" readonly>
												</td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?php echo $rmLotIDNm;?></td>
												<?php /*<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?php echo $avlQty;?></td>*/?>
												<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?//=$selProcessCode?></td>-->
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><input type=hidden name="hidphystockstatus" value=<?php echo $physstockid;?> /><?=$selProcessCode?></td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selFreezingStage?></td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selFrozenCode?></td>
												<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?//=$forPkg?></td>-->
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($numMCs!=0)?$numMCs:"";?></td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selMCPkgCode?>	</td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><span class="listing-item" style="padding-left:5px;padding-right:5px;">
												  <?=($numLS!=0)?$numLS:"";?>
												</span></td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($frozenQty!=0)?$frozenQty:"";?></td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($dpkdQty!=0)?$dpkdQty:"";?></td>
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($actualQty!=0)?$actualQty:"";?></td>	
												<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=($repkdQty!=0)?$repkdQty:"";?>&nbsp;</td>-->
												<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;">&nbsp;	</td>-->	
												<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;">&nbsp;</td>	
												<?php 
													if ($edit==true) {
												?>
												<td class="listing-item" align="center" style="padding-left:2px;padding-right:2px;" nowrap>
													<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyFrozenPackingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$selEditCriteria?>','editCriteria');
													assignValue(this.form,'<?=$rmLotID?>','editRMLotStatus');		this.form.action='DailyFrozenPacking.php';" <?=$disabled?>>	</td>
												<?php
													 }
												?>
											</tr>	
											<?php
											} 
											// Main loopEnds here 
											?>
											<tr bgcolor="White">
												<TD colspan="5" class="listing-head" align="right" style="padding-left:5px;padding-right:5px;">Total:</TD>
												<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
													<strong><?=$totNumMCs?></strong>		</td>
												<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">&nbsp;		</td>
												<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right"><strong>
												  <?=$totNumLSs?>
												</strong></td>
												<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
													<strong><?=number_format($totalFrozenQty,2,'.','');?></strong>		</td>
												<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right"><strong><?=number_format($totalPkdQty,2,'.','');?></strong>		</td>
													<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right"><strong>
													  <?=number_format($totalActualQty,2,'.','');?>
													</strong></td>
												
												<!--<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right"><strong>
												  <?=number_format($retotalPkdQty,2,'.','');?>
												</strong></td>-->
												<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">&nbsp;</td>
												<TD colspan="5" class="listing-head" align="right" style="padding-left:5px;padding-right:5px;"></TD>
											</tr>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" readonly="true">
											<input type="hidden" name="editId" value="<?=$editId?>" readonly="true">
											<input type="hidden" name="editFrozenPackingEntryId" value="<?=$frozenPackingEntryId;?>" readonly="true">
											<input type="hidden" name="editSelectionChange" value="0" readonly="true">
											<input type="hidden" name="editMode" value="<?=$editMode?>" readonly="true">
											<input type="hidden" name="allocateId" value="<?=$allocateId?>" readonly="true">
											<input type="hidden" name="editCriteria" id="editCriteria" value="<?=$editCriteria?>" readonly="true">
											<input type="hidden" name="editRMLotStatus" id="editRMLotStatus" value="<?=$editRMLotStatus?>" readonly="true">
											<? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF">
												<td colspan="17" style="padding-right:10px">
													<div align="right">
													<?php 				 			  
													 $nav  = '';
													for($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
														} else {
															$nav.= " <a href=\"DailyFrozenPacking.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"DailyFrozenPacking.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"DailyFrozenPacking.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
													 } else {
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
													 ?>	
													 <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
													 </div>
												</td>
											</tr>
											<? }?>
											<?
											} 
											else 	
											{
											?>
											<tr bgcolor="white">
												<td colspan="16"  class="err1" height="11" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
											}
											?>
											<input type="hidden" name="allocateMode" value="<?=$allocateMode?>">
										</table>
										<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
										<input type="hidden" name="entryId" id="entryId" value="<?=$entryId?>">
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
												<? 
												if($del==true)
												{
												?>
													<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return cfmDelete(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);">
												<? 
												}
												?>
												&nbsp;
												<? 
												if($add==true)
												{
												?>
												<input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFrozenPacking.php?frozenPackingFrom=<?=$dateFrom?>&frozenPackingTill=<?=$dateTill?>',700,600);">
												<? 
												}
												?>
												<input type="submit" value=" Convert LS to MCs " class="button"  name="convertLS" onClick="return cfmConvertLS(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);">
												</td>
											</tr>
										</table>			
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<tr>
			<td height="10">
				<input type="hidden" name="hidNumPack" id="hidNumPack" value="<?=$numPacks?>"/>
				<input type="hidden" name="hidSelQuickEntryList" id="hidSelQuickEntryList" value="<?=$selQuickEntryList?>"/>
				<input type="hidden" name="hidEntrySel" id="hidEntrySel" value="<?=$entrySel?>"/>
				<!-- QEL Selection Hidden Starts here  -->
				<input type="hidden" name="qeFreezingStageId" id="qeFreezingStageId" value="<?=$qeFreezingStageId?>"/>
				<input type="hidden" name="qeEUCodeId" id="qeEUCodeId" value="<?=$qeEUCodeId?>"/>
				<input type="hidden" name="qeBrandId" id="qeBrandId" value="<?=$qeBrandId?>"/>
				<input type="hidden" name="qeFrozenCodeId" id="qeFrozenCodeId" value="<?=$qeFrozenCodeId?>"/>
				<input type="hidden" name="qeMCPackingId" id="qeMCPackingId" value="<?=$qeMCPackingId?>"/>
				<input type="hidden" name="qeFrozenLotId" id="qeFrozenLotId" value="<?=$qeFrozenLotId?>"/>
				<input type="hidden" name="qeExportLotId" id="qeExportLotId" value="<?=$qeExportLotId?>"/>
				<input type="hidden" name="qeQualityId" id="qeQualityId" value="<?=$qeQualityId?>"/>
				<input type="hidden" name="qeCustomerId" id="qeCustomerId" value="<?=$qeCustomerId?>"/>
				<!-- QEL Selection Hidden Ends here  -->
				<input type="hidden" name="filledWt" id="filledWt" value="<?=$filledWt?>" readonly/>
				<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" readonly />
				<input type="hidden" name="hidMCPkgCode" id="hidMCPkgCode" value="<?=$MCPkgCode?>" readonly />
				<input type="hidden" name="hidDelAllocationArr" id="hidDelAllocationArr" value="" readonly />
				<input type="hidden" name="hidLS2MCType" id="hidLS2MCType" value="<?=$LSToMCConversionType?>" readonly />
			</td>
		</tr>	
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
		fieldId = '<?=$productRecSize+2?>';
		fldId = '<?=sizeof($pkgGroupArr)?>';
		
	</script>
	<?php 
		
		if ($entrySel=="QE") {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		hidRows();
	</SCRIPT>
	<?php
		}
	?>
	<?php 
	if ($allocateMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		displayAllocateSummary();
	</SCRIPT>
	<?php
		}
	?>
		<?php if ($convertLS) {
		
		/*$gra=sizeof($gradeAll);
		$gde=explode(',',$gradeAll);
		$gr=sizeof($gde);
		for($i=0; $i<$gr; $i++)
		{
			echo $gde[$i].'<br/>';
		}*/
		?>
		<SCRIPT LANGUAGE="JavaScript">
		//var arrId=new Array(); var arr=new Array(); 
		//arrId='<?php echo $gradeIdAll;?>';
		//arr='<?php echo $gradeAll;?>';
		//alert(arr);
		//alert(arrId);
		window.onLoad = addNewMC();
		//addNewMC();
		/*function convertLs()
		{
			addNewMC();
			alert("hui");
		}*/
		
		function addNewMC()
		{
		//alert("hii");
			var rmsize =document.getElementById('rmsz').value;
			var hidLSRowCount=document.getElementById('hidLSRowCount').value;
			//alert(rmsize);
			if(hidLSRowCount < rmsize)
			{
				addMCForConvert('tblConvertLS','','<?=$mcPackingId?>','<?=$numPacks?>','<?=$gradeIdAll?>','<?=$gradeAll?>','addmode');
			}
			else
			{
				alert("Cannot add more rows");
			}
		}
		</script>
		<?php
		}
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
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "frozenPackingFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingFrom", 
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
			inputField  : "frozenPackingTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->

	</SCRIPT>
	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>