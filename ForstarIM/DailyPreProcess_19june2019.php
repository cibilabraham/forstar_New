<?php

	require("include/include.php");
	require_once("lib/dailypreprocess_ajax.php");
	ob_start();
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$addAnother		=	false;
	
	$fishId			=	"";
	$recordsFilterId	=	0;
	$recordsDate		=	0;
	$editFishId		= 	"";
	$dailyPProcessMainId	= 	"";

	
	
	# Current Rate List name	
	$currentRateList = $processratelistObj->findRateList();	
	
	
	$selection = "?selFilter=".$p["selFilter"]."&selDate=".$p["selDate"]."&pageNo=".$p["pageNo"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	$reEdit  = false;
	
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();	
	}	

	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;		
	//----------------------------------------------------------
	# Valid Pre-Process Entry enabled/disabled
	$validDPPEnabled = $manageconfirmObj->dppValidPrePCEnabled();	
	#---------------Getting Rate List -------------------
	$selRateListId = $processratelistObj->latestRateList();
	#----------------------------------------------------

	# Add Daily Pre Processor
	
	if ($p["cmdAddNew"]!="" || $p["cmdSaveAdd"]!="") {
		$addMode	=	true;
	}
		
	if ($p["cmdCancel"]!="" ) {
		$p["selFish"]	= "";
		$addMode	= false;
	}
		

	/*
	foreach($p as $val =>$key) { echo "<br>$val = $key"; }
	*/

	# Add 
	/*
	# User should be able to enter Today's Arrival Qty and save without entering pre-processed qty or anype-processor. ON Sep 1 Mail
	*/

	if ($p["cmdAddDailyPreProcess"]!="" || $p["cmdSaveAdd"]!="") 
	{
		$selectDate		=	mysqlDateFormat($p["selectDate"]);
		$rmlotid			=	$p["rm_lot_id"];
		$fishId			=	$p["selFish"];	
		$processCode       = $p["processCode"];	
		$company       = $p["company"];	
		$unit       = $p["unit"];	
		$processRowCount	=	$p["hidProcessRowCount"];
		$processorCount		=	$p["hidColumnCount"];
		
		if ($selectDate!="")
		{
			$prevSelFish	= 0; 
			$lastInsertedId = "";
		
			for ($i=1; $i<=$processRowCount; $i++) {
				$preProcessId		=	$p["preProcessId_".$i];
				$openingBalQty		=	$p["openingBalQty_".$i];
				$todayArrivalQty	=	trim($p["todayArrivalQty_".$i]);
				$totalQty		=	$p["totalQty_".$i];
				
				$totalPreProcessQty	=	$p["totalPreProcessQty_".$i];
				$actualYield		=	$p["actualYield_".$i];
				$idealYield		=	$p["idealYield_".$i];
				$diffYield		=	$p["diffYield_".$i];
				$lanCenterId		=	($p["lanCenterId_".$i]=="")?0:$p["lanCenterId_".$i];
				$selFishId		= $p["selFishId_".$i];
				$fishHasVal		= $p["fishHasVal_".$selFishId]; // Checking Any value entered for a Fish
				$availableQty		= trim($p["availableQty_".$i]);
				//echo "hii";
				
				# Reversly Calculate Actual Used Qty ---
				$autoGenCalc = "N";
				if ($todayArrivalQty==0 && $totalPreProcessQty!=0) {
					$autoGenCalc = "Y";
					# Actual used Qty
					$todayArrivalQty = number_format(($totalPreProcessQty/($idealYield/100)),2,'.','');
					$totalQty = $todayArrivalQty;
					$actualYield	 = number_format((($totalPreProcessQty/$todayArrivalQty)*100),2,'.',''); 
				}
				# Reverse calc ends here 
			
			if($prevSelFish!=$selFishId && $fishHasVal!="" && $rmlotid=="") {
				//echo "hii";
					$dailyPreProcessRecIns	=	$dailypreprocessObj->addDailyPreProcess($selFishId, $selectDate,$processCode,$company,$unit);
					$lastInsertedValue=$dailypreprocessObj->lastIdInPreprocess();
					$lastInsertedId=$lastInsertedValue[0];
			}
			elseif($rmlotid!="" && $fishHasVal!="" && $prevSelFish!=$selFishId) {
				 //echo "hui";
				// die();
					$dailyPreProcessRecIns	=	$dailypreprocessObj->addDailyPreProcessRmLOtID($rmlotid,$selFishId, $selectDate,$processCode,$company,$unit);
					$lastInsertedValue=$dailypreprocessObj->lastIdInPreprocessRmLotID();
					$lastInsertedId=$lastInsertedValue[0];
			}				

				if ($lastInsertedId!=0 && ($totalQty!="" && $totalQty!=0) && ($todayArrivalQty!=0 || $totalPreProcessQty!=0)) {
					
					if($rmlotid!="")
					{
						$dailyPreProcessEntryRecIns = $dailypreprocessObj->addPreProcessRmLOtID($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $lastInsertedId, $lanCenterId, $availableQty, $autoGenCalc);
					}
					else
					{
						$dailyPreProcessEntryRecIns = $dailypreprocessObj->addPreProcess($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $lastInsertedId, $lanCenterId, $availableQty, $autoGenCalc);
					}
					
					###************edited by preethi **********
					// $dailyPreProcessEntryRecIns = $dailypreprocessObj->addPreProcess($lotID,$preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $lastInsertedId, $lanCenterId, $availableQty, $autoGenCalc);
					
					if ($dailyPreProcessEntryRecIns) {
						$preProcessEntryLastId	=	$databaseConnect->getLastInsertedId();
					}
	
					for ($j=1; $j<=$processorCount; $j++) {
						$preProcessorQty = $p["preProcessorQty_".$j."_".$i];
						$processorId	 = $p["processorId_".$j."_".$i];
						if (($preProcessorQty!="" && $preProcessorQty!=0) && $preProcessEntryLastId!=0 && $rmlotid=="" ) {
							$dailyPreProcessRecIns	=	$dailypreprocessObj->addPreProcesserQty($processorId, $preProcessorQty, $preProcessEntryLastId);
						}
						elseif (($preProcessorQty!="" && $preProcessorQty!=0) && $preProcessEntryLastId!=0 && $rmlotid!="" ) {
							$dailyPreProcessRecIns	=	$dailypreprocessObj->addPreProcesserQtyRmLOtID($processorId, $preProcessorQty, $preProcessEntryLastId);
						}
					} // Processor Loop ends here
				} // Entry Condition ends here
	
				$prevSelFish = $selFishId;
			} // Process Loop Ends here

			if ($dailyPreProcessRecIns) {				
				$sessObj->createSession("displayMsg", $msg_succAddDailyPreProcess);		
				if ($p["cmdSaveAdd"]!="") {					
					$addMode 	= true;
					$addAnother	= true;
					$fishId		= "";
				} else if ($p["cmdAddDailyPreProcess"]!="") {
					$sessObj->createSession("nextPage",$url_afterAddDailyPreProcess.$selection);
					$addMode 	= false;
					$addAnother	= false;
					$fishId		= "";
				} else {
					$addMode=false;
				}
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddDailyPreProcess;
			}
			$dailyPreProcessRecIns	=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddDailyPreProcess;
		}
	}

	# update
	if ($p["cmdSaveChange"]!="" ) {
		//$dailyPreProcessId	=	$p["hidDailyPreProcessId"];
		//$selFishId		=	$p["hidFishId"];		
		$processRowCount	=	$p["hidProcessRowCount"];
		$processorCount		=	$p["hidColumnCount"];
		$rmlotid			=	$p["rm_lot_id"];
		$selectDate	=	mysqlDateFormat($p["selectDate"]);
		$processCode       = $p["processCode"];
		$company       = $p["company"];	
		$unit       = $p["unit"];	
		# Checking Duplicate Entry		
		/*
		if ($selectDate!="") {						
			//$uptdDPProcessMainEntry = $dailypreprocessObj->updateDailyPPEntryConfirm($selPreProcessDate, 'N');
		}
		*/
		
		
		$prevSelFish	= "";
		$lastInsertedId = "";
		for ($i=1; $i<=$processRowCount; $i++) {
			$preProcessId		=	$p["preProcessId_".$i];
			$openingBalQty		=	$p["openingBalQty_".$i];
			$todayArrivalQty	=	trim($p["todayArrivalQty_".$i]);
			$totalQty		=	$p["totalQty_".$i];
				
			$totalPreProcessQty	=	$p["totalPreProcessQty_".$i];
			$actualYield		=	$p["actualYield_".$i];
			$idealYield		=	$p["idealYield_".$i];
			$diffYield		=	$p["diffYield_".$i];			
			$lanCenterId		=	($p["lanCenterId_".$i]=="")?0:$p["lanCenterId_".$i];
			$selFishId		= $p["selFishId_".$i];
			$fishHasVal		= $p["fishHasVal_".$selFishId]; // Checking Any value entered for a Fish 
			$dailyPreProcessId	= $p["fishEntryId_".$selFishId]; // Setting in JS
			$processEntryId		= $p["processEntryId_".$i];
			$availableQty		= trim($p["availableQty_".$i]);
			$hidTodayArrivalQty	= trim($p["hidTodayArrivalQty_".$i]);
			$lotAVailability	= $p["lotAVailability_".$i];
			
			$changeAutoGenCalc = "";
			if ($todayArrivalQty!=$hidTodayArrivalQty) {
				$changeAutoGenCalc = "N";
			}
			# Reversly Calculate Actual Used Qty ---
				$autoGenCalc = "N";
				if ($todayArrivalQty==0 && $totalPreProcessQty!=0) {
					$autoGenCalc = "Y";
					# Actual used Qty
					$todayArrivalQty = number_format(($totalPreProcessQty/($idealYield/100)),2,'.','');
					$totalQty = $todayArrivalQty;
					$actualYield	 = number_format((($totalPreProcessQty/$todayArrivalQty)*100),2,'.',''); 
				}
				
				//echo 'one:-'.$totalQty.'two:-'.$todayArrivalQty.'three:-'.$totalPreProcessQty.'<br/>';
				
			# Reverse calc ends here 
			
			if($lotAVailability=="" || $lotAVailability=="0")
			{
			
				$chk	=	$dailypreprocessObj->getDPProcessMainId($selectDate, $selFishId);
				if($chk)
				{	
					$dailyPreProcessId =$chk; 
				}
				else
				{
					$dailyPreProcessRecIns	=	$dailypreprocessObj->addDailyPreProcess($selFishId, $selectDate,$processCode,$company,$unit);
					$chk	=	$dailypreprocessObj->getDPProcessMainId($selectDate, $selFishId);
					$dailyPreProcessId =$chk; 
				//if ($dailyPreProcessRecIns) $dailyPreProcessId	= $databaseConnect->getLastInsertedId();
				}
				
			}
			else
			{
			
				$chk	=$dailypreprocessObj->getDPProcessMainIdRMlotid($selectDate, $selFishId, $lotAVailability);
				if($chk)
				{
					//echo "hii"; echo "<br/>";
					$dailyPreProcessId = $chk;
				}
				else
				{ //echo "hui";
					$dailyPreProcessRecIns	=	$dailypreprocessObj->addDailyPreProcessRmLOtID($lotAVailability,$selFishId, $selectDate,$processCode,$company,$unit);
					$chk	=$dailypreprocessObj->getDPProcessMainIdRMlotid($selectDate, $selFishId, $lotAVailability);
					$dailyPreProcessId = $chk;
				}
			
			}
			if($lotAVailability=="")
			{
				if ($processEntryId!="") {
					$dailyPreProcessRecUptd = $dailypreprocessObj->updatePreProcess($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $processEntryId, $availableQty, $changeAutoGenCalc);
				} 
				
				if ($processEntryId=="" && ($totalQty!="" && $totalQty!=0) && ($todayArrivalQty!=0 || $totalPreProcessQty!=0)) { 
				
					$dailyPreProcessEntryRecIns = $dailypreprocessObj->addPreProcess($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $dailyPreProcessId, $lanCenterId, $availableQty, $autoGenCalc);
					# Get Inserted Id
					$lastInsertedId = $databaseConnect->getLastInsertedId();
				} else {
					$lastInsertedId = $processEntryId;
				}				
			}
			else
			{
				if ($processEntryId!="") {
					$dailyPreProcessRecUptd = $dailypreprocessObj->updatePreProcessRMLotId($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $processEntryId, $availableQty, $changeAutoGenCalc);
				} 
				
				if ($processEntryId=="" && ($totalQty!="" && $totalQty!=0) && ($todayArrivalQty!=0 || $totalPreProcessQty!=0)) { 
					//echo "hii";
					$dailyPreProcessEntryRecIns = $dailypreprocessObj->addPreProcessRmLOtID($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $dailyPreProcessId, $lanCenterId, $availableQty, $autoGenCalc);
					# Get Inserted Id
					$lastInsertedId = $databaseConnect->getLastInsertedId();
				} else {
					$lastInsertedId = $processEntryId;
				}	
				//with rm lotid table
				
			}
			
			if($lotAVailability=="")
			{
				for ($j=1; $j<=$processorCount; $j++) {
					$preProcessorQty	=	$p["preProcessorQty_".$j."_".$i];
					$processorQtyEntryId	=	$p["processorQtyEntryId_".$j."_".$i];
					$processorId		=	$p["processorId_".$j."_".$i]; 			
					if ($processorQtyEntryId!="") {
						$dailyPreProcessRecUptd	=	$dailypreprocessObj->updatePreProcesserQty($processorQtyEntryId, $preProcessorQty);
					}
					if ($processorQtyEntryId=="" && ($preProcessorQty!="" && $preProcessorQty!=0) && ($totalPreProcessQty!="" && $totalPreProcessQty!=0)) {						
						$dailyPreProcessRecIns	=	$dailypreprocessObj->addPreProcesserQty($processorId, $preProcessorQty, $lastInsertedId);
					}
				} # Pre-Procesor Loop Ends
			}
			else
			{
				//with rm lotid table
				for ($j=1; $j<=$processorCount; $j++) {
					$preProcessorQty	=	$p["preProcessorQty_".$j."_".$i];
					$processorQtyEntryId	=	$p["processorQtyEntryId_".$j."_".$i];
					$processorId		=	$p["processorId_".$j."_".$i]; 			
					if ($processorQtyEntryId!="") {
						$dailyPreProcessRecUptd	=	$dailypreprocessObj->updatePreProcesserQtyRMlotid($processorQtyEntryId, $preProcessorQty);
					}
					if ($processorQtyEntryId=="" && ($preProcessorQty!="" && $preProcessorQty!=0) && ($totalPreProcessQty!="" && $totalPreProcessQty!=0)) {						
						$dailyPreProcessRecIns	=	$dailypreprocessObj->addPreProcesserQtyRmLOtID($processorId, $preProcessorQty, $lastInsertedId);
					}
				} # Pre-Procesor Loop Ends
				
			}
			$prevSelFish = $selFishId;
			$dailyPreProcessRecUptd = true;
		} # Process Loop Ends 
				
		if ($dailyPreProcessRecUptd) {
			$sessObj->createSession("displayMsg", $msg_succUpdateDailyPreProcess);
			$sessObj->createSession("nextPage", $url_afterUpdateDailyPreProcess.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyPreProcess;
		}

		$dailyPreProcessRecUptd = false;
	}


	# Edit Daily Pre Proces
	//if ($p["editId"]!="" ) {
	if ($p["cmdEdit"]!="" || $p["editId"]!="") {
		$addMode		=	false;
		$editMode		=	true;
		$editId			=	$p["editId"];	
		$lotidstatus	=	$p["lotidstatus"];	
		
		
		
		//echo $validDPPEnabled;
		/*
		Hide on 14-09-09	
		$preProcessEntryId	=	$p["preProcessEntryId"];		
		$dailypreProcessRec	=	$dailypreprocessObj->find($editId);
		$dailyPreProcessId	=	$dailypreProcessRec[0];				
		$enteredDate		=	dateFormat($dailypreProcessRec[2]);		
		if ($p["editSelectionChange"]=='1'||$p["selFish"]=="") {
			$fishId			=	$dailypreProcessRec[1];
		} else {
			$fishId			=	$p["selFish"];
		}
		# Find rate List from Process Entry Table		
		$rateListId =  $dailypreprocessObj->getRateList($preProcessEntryId);
		//getPPRateList($selDate)
		# Display Process Records 
		//$processesRecords  =	$dailypreprocessObj->findPreProcessEntryRec($editId, $rateListId);	
		*/

		if (!$validDPPEnabled) {
			
			if($lotidstatus=="notavailable")
			{
				$dailypreProcessRec	=	$dailypreprocessObj->find($editId);
				$dailyPProcessMainId	=	$dailypreProcessRec[0];				
				$enteredDate		=	dateFormat($dailypreProcessRec[3]);	
				$editCompanyId	=	$dailypreProcessRec[4];	
				$editUnitId	=	$dailypreProcessRec[5];	
				//$rm_lot_id   =  $dailypreProcessRec[3];
				if ($p["editSelectionChange"]=='1'|| $p["selFish"]=="") {
					$editFishId			=	$dailypreProcessRec[1];
					if($dailypreProcessRec[2]!="")
					{
					$editProcessId      =   $dailypreProcessRec[2];
					}
					else{
					$editProcessId="";
					}
				}
				else
					{	$editFishId			=	$p["selFish"];
						$editProcessId		=	$p["processCode"];
						$editCompanyId	=	$p["company"];	
						$editUnitId	=	$p["unit"];	
				}
			}
			else
			{
				$dailypreProcessRec	=	$dailypreprocessObj->findLotId($editId);
				//printr($dailypreProcessRec);
				$dailyPProcessMainId	=	$dailypreProcessRec[0];				
				$enteredDate		=	dateFormat($dailypreProcessRec[4]);	
				$editCompanyId	=	$dailypreProcessRec[5];	
				$editUnitId	=	$dailypreProcessRec[6];	
				$editRmLotId   =  $dailypreProcessRec[1];
				if ($p["editSelectionChange"]=='1'|| $p["selFish"]=="")
				{
						$editFishId			=	$dailypreProcessRec[2];
						$editRmLotId   =  $dailypreProcessRec[1];
						if($dailypreProcessRec[3]!="0")
						{
							$editProcessId      =   $dailypreProcessRec[3];
						}
						else
						{
							$editProcessId="";
						}
					} 
					else 
					{	
						$editFishId			=	$p["selFish"];
						$editRmLotId		=	$p["rm_lot_id"];
						$editProcessId		=	$p["processCode"];
						$editCompanyId	=	$p["company"];	
						$editUnitId	=	$p["unit"];	
					}
			}
			
		}
	}	
	

//	echo "jii".$editCompanyId.$editUnitId;		
	# Delete Daily Pre Process	
	if ($p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dailyPreProcessMainId	= $p["delId_".$i];
			$dailyPreProcessEntryId	= $p["dailyPreProcessEntryId_".$i];
			$lotIdStatus			= $p["lotIdStatus_".$i];
			if ($dailyPreProcessMainId!="") {
				if($lotIdStatus=="notavailable")
				{
					# Checking Pre-process entry Confirmed
					$processAcConfirmed = $dailypreprocessObj->chkPreProcessAcConfirmed($dailyPreProcessEntryId);		
					if (!$processAcConfirmed) {					
						# Delete Pre-Processor qty rec
						$dailyPreProcessRecDel	=	$dailypreprocessObj->delDailyPreProcessorQty($dailyPreProcessEntryId);
				
						# Deleting Process Entry Rec
						$dailyPreProcessRecDel	=	$dailypreprocessObj->delDailyPreProcessEntryQty($dailyPreProcessEntryId);
				
						# Checking Records Existing for the selected Main Id
						$exisitingRecords = $dailypreprocessObj->checkRecordsExist($dailyPreProcessMainId);

						# Delete Main Rec		
						if (sizeof($exisitingRecords)==0) {					
							$dailyPreProcessRecDel =	$dailypreprocessObj->deleteDailyPreProcess($dailyPreProcessMainId);
						}					
					} // Confirm Check Ends Here
				}
				else
				{
					# Checking Pre-process entry Confirmed
					$processAcConfirmed = $dailypreprocessObj->chkPreProcessAcConfirmedRMLotId($dailyPreProcessEntryId);		
					if (!$processAcConfirmed) {					
						# Delete Pre-Processor qty rec
						$dailyPreProcessRecDel	=	$dailypreprocessObj->delDailyPreProcessorQtyRMLotId($dailyPreProcessEntryId);
				
						# Deleting Process Entry Rec
						$dailyPreProcessRecDel	=	$dailypreprocessObj->delDailyPreProcessEntryQtyRMLotId($dailyPreProcessEntryId);
				
						# Checking Records Existing for the selected Main Id
						$exisitingRecords = $dailypreprocessObj->checkRecordsExistRMLotId($dailyPreProcessMainId);

						# Delete Main Rec		
						if (sizeof($exisitingRecords)==0) {					
							$dailyPreProcessRecDel =	$dailypreprocessObj->deleteDailyPreProcessRMLotId($dailyPreProcessMainId);
						}					
					} // Confirm Check Ends Here
				
				
				}
			}
		}
		//die();
		if ($dailyPreProcessRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyPreProcess);
			$sessObj->createSession("nextPage",$url_afterDelDailyPreProcess.$selection);
		} else {
			if ($processAcConfirmed) $errDel = $msg_failDelDailyPreProcess."<br>Please make sure the Pre-Process entry you have selected is not settled/paid.";
			else $errDel		=	$msg_failDelDailyPreProcess;
		}
	}


	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# List records based on filter 
	if ($g["selFilter"]!="" || $g["selDate"]!="") {
		$recordsFilterId	=	$g["selFilter"];
		$selDate		=	$g["selDate"];
	} else if($p["selDate"]=="") {
		$recordsFilterId	=	$p["selFilter"];
		$selDate		=	date("d/m/Y");
	} else {
		$recordsFilterId	=	$p["selFilter"];
		$selDate		=	$p["selDate"];
	}


	#Condition for Select a Fish 	
	if ($p["existRecordsFilterId"]==0 && $p["selFilter"]!=0) {
		$offset = 0;
		$pageNo = 1;
	}

	
	if ($recordsFilterId!="" || $selDate!="" || $p["cmdSearch"]!="") {	
		$Date		= explode("/",$selDate);
		$recordsDate	= $Date[2]."-".$Date[1]."-".$Date[0];
		$dailyPreProcessRecords	= $dailypreprocessObj->dailyPreProcessRecPagingFilter($recordsFilterId, $recordsDate, $offset, $limit);
		$dailyPreProcessRecordsSize	= sizeof($dailyPreProcessRecords);
		// echo '<pre>';
			// print_r($dailyPreProcessRecords);
		// echo '</pre>';
		$numrows	=  sizeof($dailypreprocessObj->dailyPreProcessRecFilter($recordsFilterId, $recordsDate));
		
		# Checking Entry Confirmed
		$entryConfirmed	= $dailypreprocessObj->chkEntryConfirmed($recordsDate);
						//18/10/08
		$prevDate = date("Y-m-d",mktime(0, 0, 0,$Date[1],$Date[0]-1,$Date[2]));
		
		# Check Prev date Entry
		$prevDateEntryConfirmed = $dailypreprocessObj->chkPrevDateEntryConfirmed($prevDate);
		$displayPrevdate = date("jS M Y", mktime(0, 0, 0, $Date[1], $Date[0]-1, $Date[2]));
		$dppConfirmEnabled = $manageconfirmObj->isDPPConfirmEnabled();

		# Check RM CB Exist
		$ppEntryExist = $dailypreprocessObj->chkRecExist('', $recordsDate);
	}
	
	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Returns all fish master records 
	//$fishMasterRecords =	$fishmasterObj->fetchAllRecords();
	$fishMasterRecords =	$fishmasterObj->fetchAllRecordsFishactive();
	//echo "addmode".$addMode;	
	//echo "editmode".$editMode;
	#Select all Pre Processing Master Records based on fishId
	if ($addMode || $editMode) {
		if($addAnother==true) 
		{
			$fishId = ""; $rmLotID="";
		}
		elseif($editFishId!="" && $editRmLotId!="")
		{	
			//echo $p["processCode"];
			//echo "hii".$editRmLotId;
			$fishId = $editFishId;
			$rmLotIDVal= $editRmLotId;	
			$rmLotsID=$rmLotIDVal;
			//echo $editProcessId;
			$companyId=$editCompanyId;
			$unitId=$editUnitId;
			if($editProcessId!="")
			{
				$processCodeId=$editProcessId;
				
			}
			if($p["processCode"]!=" ")
			{
				$processCode=	$p["processCode"];
				$processCodes=$processCode;
			}
			if($p["rm_lot_id"]!="")	
			{
				$rmLotID=	$p["rm_lot_id"];
				$rmLotsID=$rmLotID;
				$companyId=$p["company"];
				$unitId=$p["unit"];
				$fishId	=	$p["selFish"];
			}
		}
		elseif($editFishId)
		{	
			//echo "hii";
			$fishId = $editFishId;
			if($editProcessId!="")
			{
			$processCodeId=$editProcessId;
			
			}	
			$companyId=$editCompanyId;
			$unitId=$editUnitId;
			
		}
		elseif($editRmLotId)
		{	
			$rmLotIDVal=$editRmLotId;
			$rmLotsID=$rmLotIDVal;
			$companyId=$editCompanyId;
			$unitId=$editUnitId;
			if($p["rm_lot_id"]!="")	
			{
			 $rmLotID=	$p["rm_lot_id"];
			 $rmLotsID=$rmLotID;
			// $companyId=$p["company"];
			//$unitId=$p["unit"];
			}
				
		}
		// elseif($p["processCode"]!="" && $p["rm_lot_id"]=="" && $p["selFish"]=="")
		// {
			// $processCode=	$p["processCode"];
		// }
		else 
		{
			
			$fishId	=	$p["selFish"];
			$rmLotID=	$p["rm_lot_id"];
			$processCode=	$p["processCode"];
			$companyId=$p["company"];
			$unitId=$p["unit"];
			//echo "hiiii".$company."--".$unit;
			//$processCodes=$processCode;
			$rmLotsID=$rmLotID;
		}
		if ($fishId!="") {		
			// Hide on 14-08-09
			//$processesRecords  =	$processObj->processRecFilter($fishId, $selRateListId);
		}
	}

	if ($addMode || $editMode) {
		if ($enteredDate) $recordSelectDate = $enteredDate;
		else if ($p["selectDate"]!="") $recordSelectDate	= $p["selectDate"];
		else if ($selDate!="") $recordSelectDate	= $selDate;
		else $recordSelectDate	= date("d/m/Y");
	
		if ($recordSelectDate) 
		{
			$entryDate = mysqlDateFormat($recordSelectDate);

			if ($editMode) $selRateListId = $processratelistObj->getPPRateList($entryDate);			
			if ($validDPPEnabled) 
			{
				# get Valid Fish Recs
				//$getFishRecords = $dailypreprocessObj->getFishRecords($entryDate,'');
				//$rmLotIds  = $dailypreprocessObj->getLotRecords($entryDate,'');
				# Days Active PC Recs
				$daysActivePCRecs = $dailypreprocessObj->getDaysActivePreProcessCodes($entryDate, $fishId, $selRateListId);
				//$daysActiveLotPCRecs = $dailypreprocessObj->getDaysActivePreProcessLotCodes($entryDate, $lotId, $selRateListId);
			}
			else
			{
			    //echo "hii";
				# get Valid Fish Recs from master, if rmlotid not selected else select fish from rmlotid
				if($rmLotID=="")
				{
					
					list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
					if($companyId=="")
					{
						$units=$unitRecords[$defaultCompany];
					}
					else
					{
						$units=$unitRecords[$companyId];
					}
					$getFishRecords = $fishmasterObj->fetchAllRecordsFishactive();
				}
				else
				{
					//list($company,$unit)  = $dailypreprocessObj->getRMlotIdDetail($rmlotid);
					list($companyRecords,$unitRecords)  = $dailypreprocessObj->getRMlotIdDetail($rmLotID);
					if($companyId=="")
					{
						$units=$unitRecords[$defaultCompany];
					}
					else
					{
						$units=$unitRecords[$companyId];
					}

					$getFishRecords =$dailypreprocessObj->getFishOfRMLotId($rmLotID);
				}
				
				#get Valid rmlotid for the date
				if($entryDate!="")
				{
					$rmLotIds  = $dailypreprocessObj->getLotIdAfterGradingLoad($entryDate);
				}
				
				#get Valid process code if lotid not available get all process code of a fish else only display process code added in rmlotid 
				if($rmLotID=="" && $fishId!="")
				{
					$process_codes= $dailypreprocessObj->getProcessCodeFish($fishId);
				}
				else
				{
					$process_codes= $dailypreprocessObj->getProcessCodeRMLot($rmLotID,$fishId);
				}
				
			# Days Active PC Recs
			if ($addMode)
			{
				if(($selRateListId!="" && $rmLotID=="" && $fishId!="" && $processCode=="" && $companyId!="" && $unitId!="")  || ($selRateListId!="" && $rmLotID=="" && $fishId!="" && $processCode!=""  && $companyId!="" && $unitId!="") || ($selRateListId!="" && $rmLotID!="" && $fishId!="" && $processCode==""  && $companyId!="" && $unitId!="") || ($selRateListId!="" && $rmLotID!="" && $fishId!="" && $processCode!=""  && $companyId!="" && $unitId!="")  ) 
				{
					//echo "hii";
					$daysActivePCRecs = $dailypreprocessObj->preProcessRecs($selRateListId,$rmLotID,$fishId,$processCode,$companyId,$unitId);
				}
			}
			elseif($editMode)
			{
				//$daysActivePCRecs = $dailypreprocessObj->dailyPreProcessRecEdit($fishId, $selRateListId,$entryDate);
				//echo $rmLotID;
				if($fishId!="" && $rmLotID=="" && $processCode=="")
				{
					$daysActivePCRecs = $dailypreprocessObj->dailyPreProcessRecEdit($fishId, $selRateListId,$entryDate,$rmLotID,$processCode,$companyId,$unitId);
				}
				elseif($processCode!="")
				{
					$daysActivePCRecs = $dailypreprocessObj->dailyPreProcessRecEdit($fishId, $selRateListId,$entryDate,$rmLotID ,$processCode,$companyId,$unitId);
				}
				elseif($rmLotID!="")
				{	
					$daysActivePCRecs = $dailypreprocessObj->dailyPreProcessRecEdit($fishId, $selRateListId,$entryDate,$rmLotID,$processCode,$companyId,$unitId);
				}
				elseif($processCode!="" && $rmLotID!="")
				{	
					$daysActivePCRecs = $dailypreprocessObj->dailyPreProcessRecEdit($fishId, $selRateListId,$entryDate,$rmLotID,$processCode,$companyId,$unitId);
				}
			}
		
				// if ($fishId && $selRateListId) 
				// {
					// $daysActivePCRecs = $dailypreprocessObj->preProcessRecs($fishId,$selRateListId,$rmLotID);
				// }
				
			}
		}
	}

	// Line here date: 18june 2019
	//print_r($daysActivePCRecs);
	
	
	//exit;
	
	//echo("rekha");
	//exit;

	//echo $currentUrl;
	#List all pre-Processor
	//$currentUrl="DailyPreProcess.php"; 
	//$preProcessorRecords	=	$preprocessorObj->fetchAllPreProcessingRecords($currentUrl, '');
	$activeProcessorRecords	= $preprocessorObj->getActiveProcessorRecsForDailyPreProcess($currentUrl, '');

	
	
	//$activeProcessorRecords	= $preprocessorObj->getActiveProcessorRecs($currentUrl, '');
	$selProcessors		= $dailypreprocessObj->getSelProcessor($recordsDate);	
	//$preProcessorRecords	= ary_merge($activeProcessorRecords, $selProcessors);
	$preProcessorRecords	= multi_unique(array_merge($activeProcessorRecords, $selProcessors));	
	# sort by name asc
	usort($preProcessorRecords, 'cmp_name');	
	# Processor section ends here
	
	// echo '<pre>';
		// print_r($preProcessorRecords);
	// echo '</pre>';
	// # Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();
	

	
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 0;	
	else $mode = "";
	

	# Display heading
	if ($editMode)	$heading	= $label_editDailyPreProcess;
	else		$heading	= $label_addDailyPreProcess;
	
	$ON_LOAD_SAJAX = "Y"; // SAJAX, settings for TopLeftNav	

	//$help_lnk="help/hlp_DailyPreProcess.html";

	$ON_LOAD_PRINT_JS	= "libjs/dailypreprocess.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
//echo "hii".$companyId;
?>
<form name="frmDailyPreProcess" id="frmDailyPreProcess" action="DailyPreProcess.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="95%">
		<? if($err!="" ){?>
    	<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?
		if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('DailyPreProcess.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyPreProcess(document.frmDailyPreProcess);">			
												</td>
												<?} else{?>
												<td align="center" colspan="3">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyPreProcess.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAddDailyPreProcess" id="cmdAddDailyPreProcess" class="button" value=" Save " onClick="return validateAddDailyPreProcess(document.frmDailyPreProcess);">
													<? if (!$validDPPEnabled) {?>
													&nbsp;&nbsp;<input name="cmdSaveAdd" type="submit" class="button" id="cmdSaveAdd" onClick="return validateAddDailyPreProcess(document.frmDailyPreProcess);" value=" Save &amp; Add ">
													<? }?>
												</td>
												<?} ?>
											</tr>
											<input type="hidden" name="hidDailyPreProcessId" value="<?=$dailyPreProcessId;?>">
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
										<tr>
											<TD class="fieldName" colspan="4" align="center" style="text-align:center;">
											<span id="divEntryExistTxt" style='line-height:normal; font-size:14px; color:red;'></span>
											</TD>
										</tr>
										<tr>
											<td colspan="4" nowrap >
												<tr id="autoUpdate" class="autoUpdate">	
													<td>
														<table width="215" align="center">
															<tr>
																<td  nowrap class="fieldName">*Date:</td>
																<td  nowrap>
																	<input type="text" id="selectDate" name="selectDate" size="8" value="<?=$recordSelectDate;?>"   onchange="<? if ($addMode && $validDPPEnabled) {?>this.form.submit();<?} else if($editMode && $validDPPEnabled) { ?>this.form.editId.value=1;this.form.submit();<? } else {?> xajax_chkEntryExistInTable(document.getElementById('rm_lot_id').value,document.getElementById('selFish').value,'',document.getElementById('selectDate').value, '<?=$dailyPProcessMainId?>', '<?=$mode?>', document.getElementById('selDate').value); <?}?>  xajax_getRMLotId(document.getElementById('selectDate').value); " autocomplete="off" <? if ($editMode && $validDPPEnabled) {?> readonly <?}?> />
																&nbsp;	</td>
																<td class="fieldName" nowrap>RM Lot ID:</td>
																<td>
																<!--<select onchange="functionLoad(this);" id="rm_lot_id" name="rm_lot_id">-->
																<select name="rm_lot_id" id="rm_lot_id" <?					 
																if($addMode==true) {			
																?> onchange=" functionLoad(this);" <?php }
																elseif($editMode==true) { ?> disabled <?php }?>>
																	<option value=""> -- Select Lot ID --</option>
																	<?php
																	if(sizeof($rmLotIds) > 0)
																	{
																		foreach($rmLotIds as $lotID)
																		{	
																			$sel = '';
																			if($rmLotsID == $lotID[0]) $sel = 'selected="selected"';
																				echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																		}
																	}
																	?>							
																</select>								
															</td>
															<td class="fieldName" nowrap>*Company:</td>
															<td nowrap>
																<!--<select name="company" id="company" <?php if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?> onchange="assignValue(this.form,'<?php echo $editId;?>','editId');this.form.submit();" <?php }?> >-->
																<select name="company" id="company" <?php  if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?> disabled <?php }?> >
																	<option value="" >---Select--- </option>
																	<?php
																	if (sizeof($companyRecords)>0)
																	{	
																		foreach ($companyRecords as $cr=>$crs) 
																		{
																			$companyIds		= $cr;
																			$companyName	= stripSlash($crs);
																			if($companyId!="")
																			{
																				$sel  = ($companyId==$companyIds)?"Selected":"";
																			}
																			else
																			{
																				$sel  = ($defaultCompany==$companyIds)?"Selected":"";
																			}
																				//$sel  = (($company==$companyIds) || ($defaultCompany==$companyIds))?"Selected":"";
																		?>	
																			<option value="<?=$companyIds?>" <?=$sel?>><?=$companyName?></option>
																		<?
																		}
																	}
																	?>
																</select>
															</td>	
															<td class="fieldName" nowrap>*Unit:</td>
															<td nowrap>
																<!--<select name="unit" id="unit" <?php if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?> onchange="assignValue(this.form,'<?php echo $editId;?>','editId');this.form.submit();" <?php }?> >-->
																<select name="unit" id="unit" <?php if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?> disabled <?php }?> >
																	<option value="" >---Select--- </option>
																	<?php 
																	if($units>0)
																	{
																		foreach($units as $unt=>$ut)
																		{	
																			$sel = '';
																			$unitIds=$unt;
																			$unitName=$ut;
																			if($unitId == $unitIds) $sel = 'selected';
																			?>
																			<option value="<?=$unitIds;?>" <?=$sel?> ><?=$unitName;?></option>
																			<?php
																		}
																	}
																	?>
																</select>
															</td>	
															<td class="fieldName" nowrap>*Fish:</td>
															<td nowrap>
																 <?						 
																 if($addMode==true) {				
																?>
																 <!-- <select name="selFish" id="selFish" onchange="this.form.submit();">-->
																	<select name="selFish" id="selFish" onchange="functionLoad(this);">
																	<? } else {?>
																	<select name="selFish" id="selFish" <? if ($validDPPEnabled) {?> onchange="this.form.editId.value=1;this.form.submit();" <? } else {?> disabled <?}?> >
																	<? }?>					
																		<option value="" >---Select--- </option>
																		<?					
																		foreach ($getFishRecords as $fId)
																		{
																			$selected 	= "";			
																			if ($fishId==$fId[0]) {
																				$selected = " selected ";
																			}
																			?>
																		<option value="<?=$fId[0];?>" <?=$selected?> ><?=$fId[1];?></option>
																		<?
																		}
																		?>
																	</select>
																</td>
																<td class="fieldName" nowrap>Process code:</td>
																<td nowrap>
																	<select name="processCode" id="processCode" <?php if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?> onchange="assignValue(this.form,'<?php echo $editId;?>','editId');this.form.submit();" <?php }?> >
																		<option value="" >---Select--- </option>
																		<?php 
																		if($process_codes>0)
																		{
																			foreach($process_codes as $process)
																			{	
																				$sel = '';
																				$processid=$process[0];
																				$processName=$process[1];
																				if($process[0] == $processCode) $sel = 'selected';
																				?>
																				 <option value="<?=$processid;?>" <?=$sel?> ><?=$processName;?></option>
																				<?php
																			}
																		}
																		?>
																	</select>
																</td>
																
															</tr>
														</table>
													</td>						  
												</tr>
											</td>						  
										 </tr>
											<!-- Day's valid PreProcess-->
											<?php
											if (sizeof($daysActivePCRecs)>0) {
											?>
										<tr>
											<td colspan="4" nowrap style="padding-left:5px; padding-right:5px;">
												<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999">
													<tr bgcolor="#f2f2f2" align="center">
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Pre-process<br />Code <br></td>
														<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Today's <br />RM Arrival </td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Opening <br>Balance Qty </td>-->
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Available Qty<!--Total Qty--> </td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Actual Used Qty<!--Today's <br>Arrival Qty--> </td>	
														<?php
														
														foreach ($preProcessorRecords as $pr) {
															$processorName	=	stripSlash($pr[1]);
														?>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="50px" align="center"><?=$processorName?></td>
														<? }?>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total <br>PreProcessed <br>Qty </td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Actual <br>Yield (%) </td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Ideal <br>Yield (%) </td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Diff (%) </td>
													</tr>
													<?php
													$processExist="";
													$Date1		= explode("/",$recordSelectDate);
													$selectDate	= $Date1[2]."-".$Date1[1]."-".$Date1[0];
													$j=0;	
													
													$colSpan = 9+sizeof($preProcessorRecords);
													$prevFishId="";
													$selFish = "";
													$pcCountArr = array();
													$avQtyArr	= array();
													foreach ($daysActivePCRecs as $processIdVal=>$dapr) {	
													$processIdarr=explode("_", $processIdVal);
													$processId=$processIdarr[0];
													$selFish 	= $dapr[0];
														$selFishName 	= $dapr[1];
														//-----------------------------
														$exceptionLandingCenterRecords	= $processObj->fetchAllExceptionCenterRecords($processId);
															$noOfCenters	=	"";
															if (sizeof($exceptionLandingCenterRecords)>0) {
																$noOfCenters = sizeof($exceptionLandingCenterRecords);	
															}
														//------------------------------		
													$preProcessCode		= $dapr[4];	
													//echo "hii".$rmLotID;
													if($editMode)
													{	
														if($dapr[6]!="notrm")
														{
															$rmLotIDVal=$dapr[6];
															$LotIdName = $dailypreprocessObj ->getLotIdName($rmLotIDVal);
															$rmLotIDNm=$LotIdName[0];
														}
														else
														{
															$rmLotIDVal="";
														}
													}
													elseif($addMode)
													{
														$rmLotIDVal=$rmLotID;
													}
						
													if ($preProcessCode!="") {
														$processFrom = $dapr[2];	
											//echo $processFrom." row=".$j;		
														$ppmOBQty		= $dailypreprocessObj->getPPMOpeningBalance($processFrom, $selectDate);
														$dailyCatchEntryRecords = $dailypreprocessObj ->dailyCatchEntryArrivalWeight($selFish, $processFrom, $selectDate);
														$entryProcessId 	= $dailyCatchEntryRecords[2];
														$catchEntryLandingCenterId = $dailyCatchEntryRecords[5];
														$idealYield		= "";			
														for ($l=0;$l<=$noOfCenters;$l++) {
															$j++;
															$rec = $exceptionLandingCenterRecords[$l-1];
															$lanCenterId	= $rec[2];				
															$landingCenter	= $rec[3];
															$catchEntryWeight = 0;
															if ($catchEntryLandingCenterId==$lanCenterId || $entryProcessId==$processFrom) {
																 $catchEntryWeight	= $dailyCatchEntryRecords[4];
															}
														$processYieldRec = $dailypreprocessObj->findYieldRec($processId, $lanCenterId);
														$monthArray	=	array($processYieldRec[3],$processYieldRec[4],$processYieldRec[5],$processYieldRec[6],$processYieldRec[7],$processYieldRec[8],$processYieldRec[9],$processYieldRec[10],$processYieldRec[11],$processYieldRec[12],$processYieldRec[13],$processYieldRec[14]);
														$day	=	"";
														if ($Date1[1]<10) $day = $Date1[1]%10;
														else $day = $Date1[1];
														$idealYield = $monthArray[$day-1];

														// Fish Head Display
														$disMHead = "";	
														$fishRowStyle = "";
														if ($fishId) $fishRowStyle = "display:none";
														else $fishRowStyle = "display:''";
														if ($prevFishId!=$selFish) {
															$disMHead = '<tr bgcolor="white" style="'.$fishRowStyle.'"><td class="fieldname" colspan="'.$colSpan.'" style="padding-left:10px; padding-right:10px; line-height:normal;" nowrap height="15"><b>'.$selFishName.'</b><input type="hidden" name="fishHasVal_'.$selFish.'" id="fishHasVal_'.$selFish.'" value="" readonly><input type="hidden" name="fishEntryId_'.$selFish.'" id="fishEntryId_'.$selFish.'" value=""></td></tr>';
															echo $disMHead;
															//echo "hii";
														}
														
														// MainId, processEntryId, obQty, ArrivalQty, totalQty, totPPQty, actualYield, idealY, diffYield, centerId, Avaialble Qty
														if($rmLotIDVal=="")
														{
															list($dppMainId, $processEntryId, $openingBalQty, $arrivalQty, $totalQty, $totalPreProcessedQty, $actualYield,$idealYieldVal,$diffYield, $selLandgCenter, $selAvailableQty) = $dailypreprocessObj->getDPPExistRec($selectDate, $selFish, $processId, $lanCenterId,$companyId,$unitId);
														}
														else
														{
															list($dppMainId, $processEntryId, $openingBalQty, $arrivalQty, $totalQty, $totalPreProcessedQty, $actualYield,$idealYieldVal,$diffYield, $selLandgCenter, $selAvailableQty) = $dailypreprocessObj->getDPPExistRecLotID($selectDate, $selFish, $processId, $lanCenterId,$rmLotIDVal,$processFrom,$companyId,$unitId);
														}
														//echo "<br>From TBLE=$dppMainId, $processEntryId, $openingBalQty, $arrivalQty, $totalQty, $totalPreProcessedQty, $actualYield, $diffYield, $selLandgCenter, $selAvailableQty<br>";
														if (!$dppMainId)
														{
															if($lotidstatus=="notavailable" || $lotidstatus=="")
															{
																$dppMainId = $dailypreprocessObj->getDPProcessMainId($selectDate, $selFish,$companyId,$unitId);
															}
															else
															{
																$dppMainId = $dailypreprocessObj->getDPProcessMainIdRMlotid($selectDate, $selFish, $rmLotIDVal,$companyId,$unitId);
															}
														}
														/*
														if ($arrivalQty==0 || $arrivalQty=="") {
															# Calc Today's Arrival Qty
															# [ Todays arrival qty => ob + Todays RM Arrival-Todays production - Pre-process Qty - CS ] 
															$todaysProductionQty 	= $dailypreprocessObj->getPkgQty($processFrom, $selectDate);
															$todaysPPQty		= $dailypreprocessObj->getTodaysPPQty($processFrom, $selectDate);
															$todaysRPMQty		= $dailypreprocessObj->getRPMQty($processFrom, $selectDate);
															$totalCSQty 		= $dailypreprocessObj->getTotalCSQty($processFrom, $selectDate);
															//echo "<br>$ppmOBQty+$catchEntryWeight-$todaysProductionQty-$todaysPPQty-$csQty";
															$todaysArrivalQty	= ($ppmOBQty+$catchEntryWeight-$todaysProductionQty-$todaysPPQty-$csQty);
															$todaysArrivalQty 	= ($todaysArrivalQty>0)?number_format($todaysArrivalQty,2,'.',''):0;
															//echo $todaysArrivalQty;
															$totalQty = $ppmOBQty+$todaysArrivalQty;
															$totalQty = number_format($totalQty,2,'.','');
														}	
														*/		
														# Today's Arrival Qty -- Ends here			
														# Calc Total Qty
														# (Opening Balance + Arrival + PPM + RPM)-(Prodn + Total CS)
														# If (Available Qty=0) Use Actual used qty  else Available Qty
														if($rmLotIDVal=="")
														{
															if ($avQtyArr[$processFrom]=="" ) {				
																 $totalPPMOBQty		= $dailypreprocessObj->getTotalPPMOBQty($processFrom, $selectDate,$companyId,$unitId);
																 $todaysProductionQty 	= $dailypreprocessObj->getPkgQty($processFrom, $selectDate,$companyId,$unitId);
																$todaysPPMQty		= $dailypreprocessObj->getTodaysPPQty($processFrom, $selectDate,$companyId,$unitId);
																$todaysRPMQty		= $dailypreprocessObj->getRPMQty($processFrom, $selectDate,$companyId,$unitId);
																$totalCSQty 		= $dailypreprocessObj->getTotalCSQty($processFrom, $selectDate,$companyId,$unitId);			
																$todaysAvailableQty 	= ($totalPPMOBQty+$catchEntryWeight+$todaysPPMQty+$todaysRPMQty)-($todaysProductionQty+$totalCSQty);
																$avQtyArr[$processFrom] = $todaysAvailableQty;
																//echo $totalPPMOBQty."--".$catchEntryWeight."--".$todaysPPMQty."--".$todaysRPMQty."--sub--".$todaysProductionQty."--".$totalCSQty.'<br/>';
																//echo "PROCESS NOT".'-'.$todaysAvailableQty.'=>'.$todaysPPMQty.','.$todaysRPMQty.','.$totalCSQty;
																
																/*
																process combination(pre_process_cs+closing_balance+re_process_cs) from t_daily_rm_cb 
																*/
															}
															else	
															{
																$todaysAvailableQty = $avQtyArr[$processFrom];
																//echo "PROCESS".$todaysAvailableQty;
															}
														}
														else
														{
															$todaysAvailableQty = $selAvailableQty;
														}
														//echo $todaysAvailableQty ;
														//die();
														$hiddenFields = "";
														###commented on 19-09-2014
														/*$pcCountArr[$processFrom] = $j;*/
														###commented on 20-09-2014
														//$processFrm=$processFrom."_".$j."_".$rmLotID;
														//$pcCountArr[$processFrm]=$j;
														//echo $rmLotIDVal;
														if($rmLotIDVal!="")
														{
															$processFrm=$processFrom."_".$rmLotIDVal;
														}
														else
														{
															$processFrm=$processFrom;
														}
														$pcCountArr[]=$processFrm;
														//echo $processFrom;
														if($processExist=="")
														{
															$processExist=$processFrm;
															$processCnt=1;
														}
														elseif($processExist!="")
														{
															if($processExist==$processFrm)
															{
																$processCnt=$processCnt;
															}
															else
															{
																$processCnt=$processCnt+1;
															}
															$processExist=$processFrm;
														}
														?>
														<tr bgcolor="#FFFFFF">
															<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="25">
																<?=$preProcessCode?><input type="hidden" name="processSame_<?=$j?>"  id="processSame_<?=$j?>" value="<?=$processCnt?>" >
																<? if ($landingCenter) {?>
																	<br />
																	<span class="fieldname" style="line-height:normal"><?=$landingCenter?></span>
																<? }?>
																<? if ($rmLotIDVal) {?>
																	<br />
																	<span class="fieldname" style="line-height:normal"><?=$rmLotIDNm?></span>
																<? }?>
																<input type="hidden" name="preProcessId_<?=$j?>"  id="preProcessId_<?=$j?>" value="<?=$processId?>" readonly="true">
																<input type="hidden" name="lanCenterId_<?=$j?>" id="lanCenterId_<?=$j?>" value="<?=$lanCenterId?>">
																<input type="hidden" name="selFishId_<?=$j?>" id="selFishId_<?=$j?>" value="<?=$selFish?>">
																<input type="hidden" name="processEntryId_<?=$j?>" id="processEntryId_<?=$j?>" value="<?=$processEntryId?>">
																<input type="hidden" name="dppMainId_<?=$j?>" id="dppMainId_<?=$j?>" value="<?=$dppMainId?>">
																<input type="hidden" name="openingBalQty_<?=$j?>" id="openingBalQty_<?=$j?>" size="8"  onkeyup="totalArrivalQty();" style="text-align:right;" autocomplete="off" value="<?=0//=($openingBalQty!=0)?$openingBalQty:$ppmOBQty?>" readonly="true">
																<input type="hidden" name="processFrom_<?=$j?>" id="processFrom_<?=$j?>" value="<?=$processFrom?>" readonly>
																<!--<input type="text" name="rowNum_<?=$j?>" id="rowNum_<?=$j?>" value="1" readonly>-->
																<?=$hiddenFields?>
															</td>
															<!--<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$catchEntryWeight?></td>-->
															<!--<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
																<input name="openingBalQty_<?//=$j?>" type="text" id="openingBalQty_<?//=$j?>" size="8"  onkeyup="totalArrivalQty();" style="text-align:right;" autocomplete="off" value="<?//=($openingBalQty!=0)?$openingBalQty:$ppmOBQty?>">
															</td>-->
															<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
																<input type="hidden" name="totalQty_<?=$j?>" id="totalQty_<?=$j?>" size="8" readonly style="text-align:right; border:none;" autocomplete="off" value="<?=$totalQty?>" title="Total Qty used in other section" readonly="true">
																<!--<input type="text" name="availableQty_<?=$j?>" id="availableQty_<?=$j?>" size="8"  readonly style="text-align:right; border:none;" autocomplete="off" value="<?=($selAvailableQty=="" ? "0.00" :$selAvailableQty);  ?>" title="Available Qty">-->
																<?php /*<?=($selAvailableQty > 0 ? $selAvailableQty : "0.00") */?>
																<input type="text" name="availableQty_<?=$j?>" id="availableQty_<?=$j?>" size="8" readonly style="text-align:right; border:none;" autocomplete="off" value="<?=number_format($todaysAvailableQty,2,'.','');?>" title="Available Qty">
															</td>
																	<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
																<input type="text" name="todayArrivalQty_<?=$j?>" id="todayArrivalQty_<?=$j?>" size="8" onkeyup="totalArrivalQty(); changeAvailableQty('<?=$j?>', '<?=$processFrom?>');" style="text-align:right;" autocomplete="off" value="<?=($arrivalQty!=0)?$arrivalQty:(($todaysArrivalQty>0)?$todaysArrivalQty:"");?>" title="Actual Used Qty">
																<input type="hidden" name="hidTodayArrivalQty_<?=$j?>" id="hidTodayArrivalQty_<?=$j?>" size="8" value="<?=($arrivalQty!=0)?$arrivalQty:(($todaysArrivalQty>0)?$todaysArrivalQty:"");?>" readonly />
																<input type="hidden" id="lotAVailability_<?=$j?>" name="lotAVailability_<?=$j?>" value="<?=$rmLotIDVal?>">
															</td>
															<?php
															$k=0;
															$processorQtyId = "";
															foreach ($preProcessorRecords as $pr) {
																$k++;
																$masterPreProcessorId	=	$pr[0];
																$processorQtyId = "";
																$preProcessorQty = "";
																$settled	= "";
																$paid		= "";
																if ($processEntryId!="") {
																	if($rmLotIDVal=="")
																	{
																		$preProcessorRec	=	$dailypreprocessObj->findPreProcessorRec($processEntryId, $masterPreProcessorId);
																		$processorQtyId		= $preProcessorRec[0];
																		$preProcessorQty	= $preProcessorRec[3];
																		$settled		= $preProcessorRec[4];
																		$paid			= $preProcessorRec[5];
																		$readOnlyField = "";	
																		if ($settled=='Y' || $paid=='Y') {
																			$readOnlyField = "readonly";
																		}
																	}
																	else
																	{
																		$preProcessorRec	=	$dailypreprocessObj->findPreProcessorRecRmLotId($processEntryId, $masterPreProcessorId);
																		$processorQtyId		= $preProcessorRec[0];
																		$preProcessorQty	= $preProcessorRec[3];
																		$settled		= $preProcessorRec[4];
																		$paid			= $preProcessorRec[5];
																		$readOnlyField = "";	
																		if ($settled=='Y' || $paid=='Y') {
																			$readOnlyField = "readonly";
																		}
																	}
																}
															?>
															<td class="listing-item" align="center" height="25" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
																<input name="preProcessorQty_<?=$k?>_<?=$j?>" type="text" id="preProcessorQty_<?=$k?>_<?=$j?>" size="8" style="text-align:right;" onkeyup="totalPreProcessingQty();" value="<?=$preProcessorQty?>" onkeydown="return nextProcess(event,'document.frmDailyPreProcess','preProcessorQty_<?=$k?>_<?=$j+1?>');" <?=$readOnlyField?> autocomplete="off"/>
																<input type="hidden" name="processorQtyEntryId_<?=$k?>_<?=$j?>" id="processorQtyEntryId_<?=$k?>_<?=$j?>" value="<?=$processorQtyId?>">
																<input type="hidden" name="processorId_<?=$k?>_<?=$j?>" id="processorId_<?=$k?>_<?=$j?>" value="<?=$masterPreProcessorId?>">
															</td>
															<?php 
																} // Processor Loop Ends here
															?>
															<input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$k?>" />
															 <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
																<input name="totalPreProcessQty_<?=$j?>" type="text" id="totalPreProcessQty_<?=$j?>" size="8"  style="text-align:right; border:none;" readonly value="<?=$totalPreProcessedQty?>">
															</td>
															 <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
																<input name="actualYield_<?=$j?>" type="text" id="actualYield_<?=$j?>" size="5"  style="text-align:right; border:none;" readonly value="<?=$actualYield?>">
															</td>
															 <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
																<input name="idealYield_<?=$j?>" type="text" id="idealYield_<?=$j?>" size="5" style="text-align:right; border:none;" readonly value="<?=$idealYield?>">
															</td>
															 <td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
																<input name="diffYield_<?=$j?>" type="text" id="diffYield_<?=$j?>" size="5" style="text-align:right; border:none;" readonly value="<?=$diffYield?>">
															</td>
														</tr>
														<?php 					
																$prevFishId=$selFish;
																} // Landing center loop ends here			
																$prevFishId=$selFish;
															} // Loop Ends here
														}
														?>	
														<?php	
														//
														###commented on 20-09-2014
														/*foreach($pcCountArr as $pc=>$cc)
														{
															$valexp=explode("_",$pc);
															$val1= $valexp[0];
															$val2=$valexp[2];
															if($val2!="")
															{
															 $pcValue=$val1.'_'.$val2;	
															}
															else
															{
																$pcValue=$val1;
															}
														$pcarray[]=$pcValue;
														}
														//printr($pcarray);
														$prearrayexp	=	$dailypreprocessObj->countDuplicate($pcarray);
														printr($prearrayexp);
														
															foreach ($prearrayexp as $pre=>$count) {
															$valueexp=explode("_",$pre);
															$pcId= $valueexp[0];
															//$countVal=$count;
															//echo $count;
															###commented on 20-09-2014
															if($countVal=="")
															{
																$countVal=$count;
															}
															else{
																$countVal=$countVal+$count;
															}*/
															
															//printr($pcCountArr);
															$prearrayexp	=	$dailypreprocessObj->countDuplicate($pcCountArr);
															//printr($prearrayexp);
															$pcarray=array();$pcCnt=array(); $i=1;
															foreach ($prearrayexp as $pre=>$count) {
																$valueexp=explode("_",$pre);
																$countVal=end($valueexp);
																$pcId= $valueexp[0];
																
															
															?> 
															<input type="hidden" name="pcCount_<?=$pcId?>_<?=$i?>" id="pcCount_<?=$pcId?>_<?=$i?>" value="<?=$countVal?>" readonly>
															<?php
															$i++;
															}
															###commented on 19-09-2014
															/*
																foreach ($pcCountArr as $pcId=>$count) {
															?>
																<input type="hidden" name="pcCount_<?=$pcId?>" id="pcCount_<?=$pcId?>" value="<?=$count?>" readonly>
															<?php
																}
																*/
															?>	
														</table>
													</td>
												</tr>
												<?
												 } 
												else
												{?>
												<tr bgcolor="white"> 
													<td colspan="13"  class="err1" height="10" align="center"> 
													<? 
														if ($validDPPEnabled) {
															echo $msg_DaysValidPCNotExist;
														} else if (!$validDPPEnabled && $fishId) {
															echo $msg_PreProcessCodeExist;
														}
													?>
													</td>
												</tr>
											<? }?>
											<input type="hidden" name="hidProcessRowCount"	id="hidProcessRowCount" value="<?=$j?>" >
												<tr>
													<td colspan="2"  height="10" ></td></tr>
												<tr>
													<? if($editMode){?>
														<td colspan="2" align="center">
															<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyPreProcess.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateAddDailyPreProcess(document.frmDailyPreProcess);">
														</td>
													<?} else{?>
													<td align="center" colspan="3">
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyPreProcess.php');">&nbsp;&nbsp;
														<input type="submit" name="cmdAddDailyPreProcess" id="cmdAddDailyPreProcess1" class="button" value=" Save " onClick="return validateAddDailyPreProcess(document.frmDailyPreProcess);">
														<? if (!$validDPPEnabled) {?>
														&nbsp;&nbsp;<input name="cmdSaveAdd" type="submit" class="button" id="cmdSaveAdd1" onClick="return validateAddDailyPreProcess(document.frmDailyPreProcess);" value=" Save &amp; Add ">
														<? }?>
													</td>
													<input type="hidden" name="cmdAddNew" value="1">
													<?}?>
												</tr>
												<tr>
													<td colspan="2"  height="10" ></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<!-- Form fields end   -->
				</td>
			</tr>	
			<tr>
				<td height="10" ></td>
			</tr>
			<?
			}			
			# Listing Fish-Grade Starts
			?>
			<tr>
			<td>
			  <table width="200" border="0" align="center">
				   <tr>
				  <td nowrap="nowrap" class="fieldName">Current Rate List: </td>
				  <td class="listing-item" nowrap><b><?=$currentRateList?></b></td>
				</tr>
			  </table></td>
			</tr>
			<? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?>
				<tr>
					<TD height="10" align="center" class="err1"> <?=$displayPrevdate?> Pre-Process Entries are not Confirmed</TD>
				</tr>
			<?
				}
			?>
			<? if ($entryConfirmed) {?>
			<tr>
				<td nowrap class="successMsg" align="center" height="30"><strong>The day's entry is confirmed.</strong></td>
			</tr>		
			<? }?>	
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Daily Pre-Process </td>
									<td background="images/heading_bg.gif"  >
										<table cellpadding="0" cellspacing="0" align="right">	
											<tr> 
												<td class="listing-item" nowrap >Fish: &nbsp;</td>
											   <td>
													<select name="selFilter">
														<option value="0">--Select All--</option>
														<? 
														foreach ($fishMasterRecords as $fl) {
															$rFishId	= $fl[0];
															$fishName	= $fl[1];
															$selected	= "";
															if ($rFishId==$recordsFilterId) {
																$selected	= "selected";
															}
														?>
															<option value="<?=$rFishId;?>" <?=$selected;?> ><?=$fishName;?> </option>
														<?
														}
														?>
													</select>								
												</td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;Date:&nbsp;</td>
												<td nowrap>
											   <? 
												if($selDate=="") $selDate=date("d/m/Y");
												?>
												   <input type="text" id="selDate" name="selDate" size="8" value="<?=$selDate?>">&nbsp;
												</td>
												<td>&nbsp;<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search ">&nbsp;</td>
											</tr>
										</table>
									</td>
								</tr>
								<?php
									if ($isAdmin || $reEdit) {
								?>
								<tr>
									<td colspan="3" height="10" >&nbsp;</td>
								</tr>
								<tr>
									<TD style="padding-left:10px;padding-right:10px;" align="right" colspan="3">
										<input type="button" name="refreshAvailableQty" value="Refresh RM Qty" class="button" onclick="return updateAvailableRMQty(document.getElementById('selDate').value);" title="Click here to update the days available RM Qty. " />
									</TD>
								</tr>
								<?php
									}
								?>
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td>
												<? if($del==true){?>
												<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyPreProcessRecordsSize;?>);"><? }?>&nbsp;
												<? if($add==true && (!$ppEntryExist || !$validDPPEnabled ) ){?>
													<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>
												<? } else if ($edit && $ppEntryExist) {?>
													<input type="submit" value=" Edit " name="cmdEdit" class="button" <? if ($entryConfirmed || sizeof($dailyPreProcessRecords)<=0) {?> disabled="true" <? }?>>
												<? }?>
												<? if($add==true){?>
												<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>-->
												<? }?>&nbsp;
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyPreProcess.php?selFilter=<?=$recordsFilterId?>&selDate=<?=$recordsDate?>',700,600);"><? }?>&nbsp;
												<? if ($confirm==true && $dppConfirmEnabled && !$entryConfirmed) {?>
												<input type="button" name="cmdConfirm" id="cmdConfirm" value="Confirm" class="button" onclick="return confirmDPPEntry('<?=$selDate?>');" <? if ($entryConfirmed || sizeof($dailyPreProcessRecords)<=0) {?> disabled="true" <? }?> />
												<? }?>
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
								<td colspan="2" >
									<table cellpadding="1"  width="98%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if (sizeof($dailyPreProcessRecords)>0) {
										$i	= 0;
									?>
									<? if($maxpage>1){?>
										<tr bgcolor="#FFFFFF">
											<td colspan="<?=12+sizeof($preProcessorRecords);?>" style="padding-right:10px">
												<div align="right">
												<?php 				 			  
												$nav  = '';
												for ($page=1; $page<=$maxpage; $page++) {
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"DailyPreProcess.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"DailyPreProcess.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"DailyPreProcess.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
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
										<tr  bgcolor="#f2f2f2" align="center" > 
											<td width="20"> <input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
											<td class="listing-head" nowrap style="padding-left:2px; padding-right:2px;">RM Lot ID</td>
											<td class="listing-head" nowrap style="padding-left:2px; padding-right:2px;">Fish</td>
											<td class="listing-head" style="padding-left:2px; padding-right:2px;">Pre-Process Code </td>
											<!--<td class="listing-head" style="padding-left:2px; padding-right:2px;" nowrap>O/B Qty<br>(Kg)</td>-->
											<td class="listing-head" style="padding-left:2px; padding-right:2px;">Available<br> Qty<!--Total Qty--></td>
											<td class="listing-head" style="padding-left:2px; padding-right:2px;">Actual<br/>Used Qty<!--Today's<br> Arrival Qty-->(Kg)</td>
											<?php
											foreach ($preProcessorRecords as $pr)
											{
												$processorName	=	stripSlash($pr[1]);
											?>
											<td class="listing-head" style="padding-left:2px; padding-right:2px;" width="50px"><?=$processorName?></td>
											<? }?>
											<td class="listing-head" style="padding-left:2px; padding-right:2px;">Total<br> PreProcessed Qty</td>
											<td class="listing-head" style="padding-left:2px; padding-right:2px;">Actual Yield(%)</td>
											<td class="listing-head" style="padding-left:2px; padding-right:2px;">Ideal Yield (%)</td>
											<td class="listing-head" nowrap style="padding-left:2px; padding-right:2px;">Diff (%) </td>
											<td class="listing-head" style="padding-left:2px; padding-right:2px;">Total<br> PreProcess Amt</td>
											<? if($edit==true && !$validDPPEnabled){?>
											<td class="listing-head" nowrap></td>
											<? }?>
										</tr>
										<?php 
										$dailyPreProcessAmount	=	"";
										$pFromArr = array();
										$totalDppAvailableQty = 0;
										$totArrivalQty = 0;
										$dProcessorArr = array();
										$grandTotPPQty = 0;
										foreach ($dailyPreProcessRecords as $pr) {
											$i++;
											$dailyPreProcessId	=	$pr[0];
											$sfishId		=	$pr[1];	
											$fishName		= stripSlash($fishmasterObj->findFishName($sfishId));	
											$preProcessId		=	$pr[4];
											$processRec		=	$processObj->find($preProcessId);
											//$preProcessCommission	=	$processRec[5];
											//$preProcessCriteria	=	$processRec[6];
											$preProcessCode		=	$processRec[7];
											//rekha modified here dated 18 june 2019
											
											$is_activepreProcessCode= $dailypreprocessObj->chkActiveCode($sfishId,$preProcessCode,$preProcessId);
											
											//echo($is_activepreProcessCode);
											//echo("<br>");		
											//end code 
											
											
											
											//$processRate		=	$dailypreprocessObj->findProcessRate($preProcessId);		
											$openingBalQty		=	$pr[5];
											$arrivalQty		=	$pr[6];
											$totArrivalQty		+= 	$arrivalQty;
								
											$totalArrivalQty	=	$pr[7]; //from qry
											$dailyPreProcessEntryId	= 	$pr[3];
											$totalPreProcessedQty	=	$pr[8];
											$grandTotPPQty		+= $totalPreProcessedQty;
											$actualYield		=	$pr[9];
											$idealYield		=	$pr[10];
											$diffYield		=	number_format(($actualYield-$idealYield),2,'.','');
											# Criteria Calculation 1=> From/ 0=>To
											# HO-HL if  From HOXRate+HL*commi
											# HO-HL if  To   HL Xrate + HL * commi
											$IYield	  = ($idealYield/100);
											$aYield	  = ($actualYield/100);
											$lotIdStatus=$pr[17];
											/*
											if ($preProcessCriteria==1) {
												//if (From) and actual yield> ideal yield  then yield=actual yield
												if ($actualYield>$idealYield) {
													$totalPreProcessAmt 	=	($totalPreProcessedQty/$aYield) * $processRate + $totalPreProcessedQty * $preProcessCommission;
												} else {
													$totalPreProcessAmt 	=	($totalPreProcessedQty/$IYield) * $processRate + $totalPreProcessedQty * $preProcessCommission;
												}					
											} else {
												$totalPreProcessAmt	=	$totalPreProcessedQty*$processRate + $totalPreProcessedQty * $preProcessCommission;
											}
											$dailyPreProcessAmount += $totalPreProcessAmt;
											*/
											# New calculation
											$totalPreProcessAmt = 0;
											$diffCalcUsed = false;
											foreach ($preProcessorRecords as $ppr) {
												$mPrePId = $ppr[0];
												if($lotIdStatus!="available")
												{
													$ppRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId,$mPrePId);
												}
												else
												{
												
													$ppRec = $dailypreprocessObj->findPreProcessorRMlotidRec($dailyPreProcessEntryId,$mPrePId);
												}
												$ppQty = $ppRec[3];
												$preProcessorAmt = 0;
												if ($ppQty!=0) {
													list($ppeRate, $ppeCommission, $ppeCriteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $mPrePId);
													/*
													$selPPRate = ($ppeRate!=0)?$ppeRate:$processRate;
													$selPPCommi = ($ppeCommission!=0)?$ppeCommission:$preProcessCommission;
													$selPPCriteria = ($ppeRate!=0)?$ppeCriteria:$preProcessCriteria;
													*/
													$selPPRate = $ppeRate;
													$selPPCommi = $ppeCommission;
													$selPPCriteria = ($ppeRate!=0)?$ppeCriteria:$preProcessCriteria;
													$selYieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
													//echo $selPPCriteria;
													if ($selPPCriteria==1) {
												//if (From) and actual yield> ideal yield  then yield=actual yield
														//echo "<br>$diffYield<$selYieldTolerance::$selPPCriteria";
														if ($actualYield>$idealYield && $diffYield<$selYieldTolerance) {
															//echo "echo $actualYield>$idealYield && $diffYield<$selYieldTolerance";
															$preProcessorAmt = ($ppQty/$aYield)*$selPPRate+$ppQty*$selPPCommi;
														} else {
															$preProcessorAmt = ($ppQty/$IYield)*$selPPRate+ $ppQty*$selPPCommi;
														}	
															
														if ($actualYield>$idealYield && $diffYield>$selYieldTolerance) $diffCalcUsed = true;
													} else {
														$preProcessorAmt = $ppQty*$selPPRate+$ppQty*$selPPCommi;
													}
													//echo "<br>$selPPRate, $selPPCommi, $selPPCriteria=>$preProcessorAmt<br>";
													$totalPreProcessAmt += $preProcessorAmt;
												} // Qty check ends here
											} // PProcessor Loop Ends here
											# New Calculation ends here
											$dailyPreProcessAmount += $totalPreProcessAmt;
											$selLandingCenter ="";		
											if ($pr[12]!=0) $selLandingCenter = $landingcenterObj->findLandingCenter($pr[12]);	

											$confirmStatus	= $pr[13];
											$editDisabled = "";	
											//if ($confirmStatus=='Y' && $reEdit==false) {
											if ($confirmStatus=='Y') {
												$editDisabled = "disabled";
											}
											
											
											$dppAvailableQty = number_format($pr[14],2,'.','');
											
											if($is_activepreProcessCode){
											$totalDppAvailableQty += $dppAvailableQty;	
											}
											$autoGeneratedCalc = $pr[15];

											/*
											$rowColor = "";
											if ($diffCalcUsed) $rowColor = "#FFFFCC";
											else $rowColor = "#e6fff8";
											*/

											/*
											$displayRow = "";				
											$displayMsg = "";			
											if ($diffCalcUsed) {
												$displayRow = "style=\"background-color: #FFFFCC;\" onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Diff yield is greater than tolerance yield.');\" onMouseout=\"this.style.backgroundColor='#FFFFCC';UnTip();\" ";
												//$displayMsg = "";
											} else if ($autoGeneratedCalc=='Y') {
												$displayRow = "style=\"background-color: #e6fff8;\" onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Automatic calculation is applied in Actual used qty.');\" onMouseout=\"this.style.backgroundColor='#e6fff8';UnTip();\" ";
											} else {
												$displayRow = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
											}*/
														
											$diffCalcStyle = "";
											$diffCalcMsg	= "";								
											if ($diffCalcUsed) {
												$diffCalcStyle = "background-color: #FFFFCC;";
												 $diffCalcMsg = "onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Diff yield is greater than tolerance yield.');\" onMouseout=\"this.style.backgroundColor='#FFFFCC';UnTip();\" ";
											} 
											$auQtyStyle 	= "";
											$auQtyMsg 	= "";
											if ($autoGeneratedCalc=='Y') {
												$auQtyStyle = "background-color: #e6fff8;";
												$auQtyMsg = "onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Automatic calculation is applied in Actual used qty.');\" onMouseout=\"this.style.backgroundColor='#e6fff8';UnTip();\" ";
											}

											/* else {
												$displayRow = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
											}*/

											$selProcessFromId = $pr[16];
											$showAvailableCalc = "";
											if (!isset($pFromArr[$selProcessFromId])) {
												# Display Calc
												$displayAvailableCalc = $dailypreprocessObj->disAvailableQtyCalc($selProcessFromId, $recordsDate);
												if ($dppAvailableQty!=0) {
													$showAvailableCalc = "onMouseover=\"ShowTip('$displayAvailableCalc');\" onMouseout=\"UnTip();\" ";
												}
												$pFromArr[$selProcessFromId] = $dppAvailableQty;
											}
										if($is_activepreProcessCode){
										?>
										<tr <?=$listRowMouseOverStyle?>> 
											<td width="20" height="25"> 
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyPreProcessId;?>" class="chkBox">
												<input type="hidden" name="dailyPreProcessEntryId_<?=$i?>" value="<?=$dailyPreProcessEntryId?>" />
												<input type="hidden" name="lotIdStatus_<?=$i?>" value="<?=$lotIdStatus?>" />
											</td>
											<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;">
												<? if($lotIdStatus=="available") { echo $pr[18]; } ?>
											</td>
											<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$fishName;?></td>
											<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$preProcessCode?>
											<? if ($selLandingCenter) { ?>
											<br /><span class="fieldname" style="line-height:normal"><?=$selLandingCenter?></span>
											<? }?>
											</td>
											<!--<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?//=$openingBalQty?></td>-->
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												<?=($dppAvailableQty!=0)?(($showAvailableCalc!="")?"<a href='###' class='link5' $showAvailableCalc>$dppAvailableQty</a>":$dppAvailableQty):""?>
											</td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; <?=$auQtyStyle?>" <?=$auQtyMsg?>><?=$arrivalQty?></td>
											<?php
											foreach ($preProcessorRecords as $ppr) {
												$masterPreProcessorId	=	$ppr[0];
												if($lotIdStatus!="available")
													{
														$preProcessorRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId,$masterPreProcessorId);
													}
													else
													{
													
														$preProcessorRec = $dailypreprocessObj->findPreProcessorRMlotidRec($dailyPreProcessEntryId,$masterPreProcessorId);
													}
												
												
												//$preProcessorRec	=	$dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId,$masterPreProcessorId);
												//$preProcessorQty	=	$preProcessorRec[3]; edited 05-01-07
												if ($preProcessorRec[3]!=0) $preProcessorQty = $preProcessorRec[3];
												else $preProcessorQty	= "";
												if ($preProcessorQty!="") $dProcessorArr[$masterPreProcessorId] += $preProcessorQty;
											?>
											<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;" nowrap><?=$preProcessorQty;?></td>
											<?
												}
											?>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$totalPreProcessedQty?></td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$actualYield?> </td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$idealYield?></td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$diffYield?></td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; <?=$diffCalcStyle?>" <?=$diffCalcMsg?>><?=number_format($totalPreProcessAmt,2);?></td>
											<? if($edit==true && !$validDPPEnabled){?>
														<td class="listing-item" width="40" align="center">
												<input type="submit" value=" Edit " name="cmdEdit_<?=$i?>" id="cmdEdit_<?=$i?>" onClick="assignValue(this.form,<?=$dailyPreProcessId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,<?=$dailyPreProcessEntryId?>,'preProcessEntryId'); assignValue(this.form,'<?=$lotIdStatus?>','lotidstatus');" <?=$editDisabled?>>

											</td>
											<? }?>
										  </tr>
										<?php
										} // end if
										}
										?>
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="">
										<input type="hidden" name="lotidstatus" value="">
										<input type="hidden" name="preProcessEntryId" value="">
										<input type="hidden" name="editSelectionChange" value="0">
										<tr bgcolor="white">
											<td height="10" colspan="4" align="right" class="listing-head">Total:</td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												<strong><?=number_format($totalDppAvailableQty,2);?></strong>
											</td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												<strong><?=number_format($totArrivalQty,2);?></strong>
											</td>
											<?php
											$processorQty = "";
											foreach ($preProcessorRecords as $ppr) {
												$mPreProcessorId =	$ppr[0];
												$processorQty = $dProcessorArr[$mPreProcessorId];
											?>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												<strong><?=($processorQty!=0)?number_format($processorQty,2):"";?></strong>
											</td>
											<? }?>			
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												<strong><?=number_format($grandTotPPQty,2);?></strong>
											</td>
											 <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												&nbsp;
											</td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												&nbsp;
											</td>
											<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
												&nbsp;
											</td>
											<td height="10" class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><strong><? echo number_format($dailyPreProcessAmount,2);?></strong></td>
											<? if($edit==true && !$validDPPEnabled){?>
											 <td height="10" align="center">&nbsp;</td>
											<? }?>
										</tr>
										<? if($maxpage>1){?>
										<tr bgcolor="#FFFFFF">
											<td colspan="<?=12+sizeof($preProcessorRecords);?>" style="padding-right:10px">
												<div align="right">
												<?php 				 			  
												 $nav  = '';
												for ($page=1; $page<=$maxpage; $page++) {
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"DailyPreProcess.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"DailyPreProcess.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"DailyPreProcess.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
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
										<td colspan="13"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									  </tr>
										<?
										}
										?>
									</table>
									<input type="hidden" name="existRecordsFilterId" value="<?=$recordsFilterId?>">
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyPreProcessRecordsSize;?>);"><? }?>&nbsp;
												<? if($add==true && (!$ppEntryExist || !$validDPPEnabled ) ){?>
													<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>
												<? } else if ($edit && $ppEntryExist) {?>
													<input type="submit" value=" Edit " name="cmdEdit" class="button" <? if ($entryConfirmed || sizeof($dailyPreProcessRecords)<=0) {?> disabled="true" <? }?>>
												<? }?>
												<? if($add==true){?>
													<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>-->
												<? }?>&nbsp;
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyPreProcess.php?selFilter=<?=$recordsFilterId?>&selDate=<?=$recordsDate?>',700,600);"><? }?>
															&nbsp;
												<? if ($confirm==true && $dppConfirmEnabled && !$entryConfirmed) {?>
													<input type="button" name="cmdConfirm" id="cmdConfirm1"  value="Confirm" class="button" onclick="return confirmDPPEntry('<?=$selDate?>');" <? if ($entryConfirmed || sizeof($dailyPreProcessRecords)<=0) {?> disabled="true" <? }?>>
												<? }?>
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
		<input type="hidden" name="hidFishId" value="<?=$fishId?>">
		<input type="hidden" name="validDPPEnabled" id="validDPPEnabled" value="<?=$validDPPEnabled?>">
		<tr>
			<td height="10"></td>
		</tr>	
	</table>

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
  <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>

	<?php
	if ($addMode!="" && $fishId!=""   && $recordSelectDate!="" && !$validDPPEnabled) {
		//if ($addMode!="" && $fishId!=""   && $recordSelectDate!="" && !$validDPPEnabled) {;
		//echo $rmLotID;
	?>	
		<script language="JavaScript">
		//alert("hii");
			xajax_chkEntryExistInTable('<?=$rmLotID?>','<?=$fishId?>','<?=$processCodeLotid?>','<?=$recordSelectDate?>', '<?=$dailyPProcessMainId?>','<?=$selRateListId?>', '<?=$mode?>','', '<?=$companyId?>','<?=$unitId?>');
		</script>
	<?php
	}
	/*elseif ($addMode!="" && $fishId!="" && $recordSelectDate!="" && !$validDPPEnabled) {
		?>	
		<script language="JavaScript">
		//alert("hii");
	
			xajax_chkEntryExistInTable('','<?=$fishId?>','<?=$processCode?>','<?=$recordSelectDate?>', '<?=$dailyPProcessMainId?>','<?=$selRateListId?>', '<?=$mode?>', '');
			// xajax_chkEntryExist('<?=$fishId?>','<?=$recordSelectDate?>', '<?=$dailyPProcessMainId?>', '<?=$mode?>', '');
		</script>
	<?php
	}*/
	?>
	<?php
	if (($addMode!="" || $editMode!="") && $recordSelectDate!="" && $validDPPEnabled) {
?>	
	<script language="JavaScript">
	//alert("hui");
		xajax_chkDPPEntryExist('<?=$recordSelectDate?>', '<?=$mode?>');
	</script>
<?php
	}
?>

	
	
	
<?php
	/*if (($addMode!="" || $editMode!="") && $recordSelectDate!="" && $validDPPEnabled) {
?>	
	<script language="JavaScript">
		xajax_chkDPPEntryExist('<?=$recordSelectDate?>', '<?=$mode?>');
	</script>
<?php
	}*/
?>

<script>
var previous='';
</script>

<?php

/*	if ($addMode || $editMode ) {
	if(sizeof($daysActivePCRecs)>0)
	{
		$i=1;
	foreach ($prearrayexp as $pre=>$count) {
			$valueexp=explode("_",$pre);
			$countVal=end($valueexp);
			$pcId= $valueexp[0];
			//echo $i;
?>
<script language="JavaScript" type="text/javascript">
jQuery(document).ready(function(){
//alert("hii");
var i='<?=$i?>';
var countVal='<?=$countVal?>';
	if(previous=="")
	{	
		//alert(i);
		for(j=i; j<=countVal; j++)
		{
				document.getElementById("processSame_"+j).value=i;
		}
		previous=countVal;
	}
	else
	{
		//var countValNw=parseInt(previous)+parseInt('<?=$countVal?>');
		//alert(countValNw);
		previousnxt=parseInt(previous)+1;
		for(j=previousnxt; j<=countVal; j++)
		{
			document.getElementById("processSame_"+j).value=i;
		}
		previous=countVal;
	}
});
</script>
<?php
	$i++;
	}
	//echo "hii";
	}
	}*/
?>
<?php
	if ($editMode || $addMode) {
		// Total Arrival Qty
?>
<script language="JavaScript" type="text/javascript">
	//var myVar=setInterval(function(){updateAvailableQty()},1000);//1sec
	totalArrivalQty();
	updateAvailableQty();
</script>
<?php
	}
?>




<script>


/*function lotIdAvlCheck()
	{
		var lotId_aval = document.getElementById('lotIdAvailable');
		// alert(procure_aval.checked);
		if(lotId_aval.checked == true)
		{
			jQuery('#autoUpdate').hide();
			jQuery('#autoUpdate2').show();
			
		}
		else
		{
			jQuery('#autoUpdate2').hide();
			jQuery('#autoUpdate').show();
			
		}
		// alert(contentDis);
	}
	jQuery(document).ready(function(){
		var lotIdAvailable = '<?php echo $lotIdAvailable;?>';
		//alert(procurementAvailable);
		if(lotIdAvailable == '1')
		{
			document.getElementById('lotIdAvailable').checked = true;
			jQuery('#autoUpdate').hide();
			jQuery('#autoUpdate2').show();
			
		}
		
	
	});
	*/
	
	

	</script>



</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>