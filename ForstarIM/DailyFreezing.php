<?php

	require("include/include.php");
	require_once("lib/DailyFreezing_ajax.php");
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
	
	//$rm_lot_id = $p['rm_lot_id'];
	
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
	if ($p["cmdAddSoaking"]!="" || $p["cmdSaveAdd"]!="") {
				
		$selectDate		=	mysqlDateFormat($p["selectDate"]);
		$rmlotid			=	$p["rm_lot_id"];
		$fishId			=	$p["selFish"];	
		$processCode       = $p["processCode"];		
		$processRowCount	=	$p["hidProcessRowCount"];
		//$processorCount		=	$p["hidColumnCount"];
		
		if ($selectDate!="")
		{
			$prevSelFish	= 0;
			$lastInsertedId = "";
			for ($i=0; $i<$processRowCount; $i++) {
				$processFrom		=	$p["processFrom_".$i];
				$lotidAvailable		=	$p["lotidAvailable_".$i];
				$selFishId          =	$p["selFishId_".$i];
				$gradeName		=	$p["gradeName_".$i];
				if($gradeName==1)
				{
					$soak_inCount		=	$p["soak-inCount_".$i];
					$soak_outCount		=	$p["soak-outCount_".$i];
					$soak_inGrade="";
					$soak_outGrade="";
				}
				else
				{
					$soak_inGrade	=	$p["soak-inGrade_".$i];
					$soak_outGrade		=	$p["soak-outGrade_".$i];
					$soak_inCount="";
					$soak_outCount="";
				}
				$soak_inQty		=	$p["soak-inQty_".$i];
				$soak_inTime		=	$p["soak-inTime_".$i];
				$soak_outQty		=	$p["soak-outQty_".$i];
				$soak_outTime		=	$p["soak-outTime_".$i];
				$temperature		=	$p["temperature_".$i];
				$gain		=	$p["gain_".$i];
				$chemicalName		=	$p["chemicalName_".$i];
				$chemicalQty		=	$p["chemicalQty_".$i];
				
				
				
				
				# Reverse calc ends here 
				if($prevSelFish!=$selFishId && $fishId!="" && $selectDate!=""	) {
				//echo "hii";
					$soakingIns	=	$dailyFreezingObj ->addSoaking($rmlotid,$selFishId, $selectDate,$processCode);
					$lastInsertedValue=$dailyFreezingObj ->lastIdInSoaking();
					$lastInsertedId=$lastInsertedValue[0];
					}
			
				//if (($todayArrivalQty!=0 || $totalPreProcessQty!=0) && $lastInsertedId!=0) {
				if ($lastInsertedId!=0 &&  $gradeName!="0" && $soak_inQty!="") {
					$soakingEntryRecIns = $dailyFreezingObj ->addSoakingEntry($lastInsertedId,$processFrom, $gradeName, $soak_inCount, $soak_inGrade,$soak_outCount, $soak_outGrade, $soak_inQty, $soak_inTime ,$soak_outQty, $soak_outTime, $temperature, $gain, $chemicalName, $chemicalQty,$lotidAvailable);
				} // Entry Condition ends here
	
				$prevSelFish = $selFishId;
			} // Process Loop Ends here

			if ($soakingEntryRecIns) {				
				$sessObj->createSession("displayMsg", $msg_succAddSoaking);		
				if ($p["cmdSaveAdd"]!="") {					
					$addMode 	= true;
					$addAnother	= true;
					$fishId		= "";
				} else if ($p["cmdAddSoaking"]!="") {
					$sessObj->createSession("nextPage",$url_afterAddSoaking.$selection);
					$addMode 	= false;
					$addAnother	= false;
					$fishId		= "";
				} else {
					$addMode=false;
				}
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSoaking;
			}
			$soakingEntryRecIns	=	false;
		} 
		else
		{
			$addMode	=	true;
			$err		=	$msg_failAddSoaking;
		}
	}

	# update
	if ($p["cmdSaveChange"]!="" ) {
		$selectDate		=	mysqlDateFormat($p["selectDate"]);
		$rmlotid			=	$p["rm_lot_id"];
		$fishId			=	$p["selFish"];	
		$processCode       = $p["processCode"];		
		$processRowCount	=	$p["hidProcessRowCount"];
		
		$soakingId= $p["hidsoakingId"];
		$prevSelFish	= "";
			for ($i=0; $i<$processRowCount; $i++) {
				$processFrom		=	$p["processFrom_".$i];
				$lotidAvailable		=	$p["lotidAvailable_".$i];
				$selFishId          =	$p["selFishId_".$i];
				$gradeName		=	$p["gradeName_".$i];
				if($gradeName==1)
				{
					$soak_inCount		=	$p["soak-inCount_".$i];
					$soak_outCount		=	$p["soak-outCount_".$i];
					$soak_inGrade="";
					$soak_outGrade="";
				}
				else
				{
					$soak_inGrade	=	$p["soak-inGrade_".$i];
					$soak_outGrade		=	$p["soak-outGrade_".$i];
					$soak_inCount="";
					$soak_outCount="";
				}
				$soak_inQty		=	$p["soak-inQty_".$i];
				$soak_inTime		=	$p["soak-inTime_".$i];
				$soak_outQty		=	$p["soak-outQty_".$i];
				$soak_outTime		=	$p["soak-outTime_".$i];
				$temperature		=	$p["temperature_".$i];
				$gain		=	$p["gain_".$i];
				$chemicalName		=	$p["chemicalName_".$i];
				$chemicalQty		=	$p["chemicalQty_".$i];
				$soakingEntryId		= $p["soakingEntry_".$i];
			
			# Reverse calc ends here 
				if($prevSelFish!=$selFishId && $fishId!="" && $selectDate!="" && $soakingId==""		) {
					$soakingIns	=	$dailyFreezingObj ->addSoaking($rmlotid,$selFishId, $selectDate,$processCode);
					}
						$soakId = $dailyFreezingObj ->getSoakingMainId($selectDate, $selFishId);
					
				
				if ($soakingEntryId=="" &&  $gradeName!="0" && $soak_inQty!="" ) {
					$soakingEntryRecInsUptd = $dailyFreezingObj ->addSoakingEntry($soakId,$processFrom, $gradeName, $soak_inCount, $soak_inGrade,$soak_outCount, $soak_outGrade, $soak_inQty, $soak_inTime ,$soak_outQty, $soak_outTime, $temperature, $gain, $chemicalName, $chemicalQty,$lotidAvailable);
				}
				else
				{
					$soakingEntryRecInsUptd = $dailyFreezingObj ->updateSoakingEntry($processFrom, $gradeName, $soak_inCount, $soak_inGrade,$soak_outCount, $soak_outGrade, $soak_inQty, $soak_inTime ,$soak_outQty, $soak_outTime, $temperature, $gain, $chemicalName, $chemicalQty,$lotidAvailable,$soakingEntryId);
				}				

			
			$prevSelFish = $selFishId;
			$soakingEntryRecInsUptd = true;
		} # Process Loop Ends 
				
		if ($soakingEntryRecInsUptd) {
			$sessObj->createSession("displayMsg", $msg_succSoakingUpdate);
			$sessObj->createSession("nextPage", $url_afterUpdateSoaking.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSoakingUpdate;
		}

		$soakingEntryRecInsUptd = false;
	}


	# Edit Daily Pre Proces
	//if ($p["editId"]!="" ) {
	if ($p["cmdEdit"]!="" || $p["editId"]!="") {
		$addMode		=	false;
		$editMode		=	true;
		$editId			=	$p["editId"];	
		if (!$validDPPEnabled) {			
			$soakingRec	=	$dailyFreezingObj ->find($editId);
			$soakingMainId	=	$soakingRec[0];				
			$enteredDate		=	dateFormat($soakingRec[1]);	
			$rm_lot_id   =  $soakingRec[2];
			if ($p["editSelectionChange"]=='1'|| $p["selFish"]=="") {
			$editFishId			=	$soakingRec[3];
			} else $editFishId			=	$p["selFish"];
			
		}
	}	
	
	
			
	# Delete Daily Pre Process	
	if ($p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$soakingMainId	= $p["delId_".$i];
			$soakingEntryId	= $p["soakingEntryId_".$i];

			if ($soakingMainId!="") {

				# Checking Pre-process entry Confirmed
				//$processAcConfirmed = $dailyFreezingObj ->chkPreProcessAcConfirmed($dailyPreProcessEntryId);		
				//if (!$processAcConfirmed) {					
					# Delete Pre-Processor qty rec
					//$dailyPreProcessRecDel	=	$dailyFreezingObj ->delDailyPreProcessorQty($dailyPreProcessEntryId);
			
					# Deleting Process Entry Rec
					$soakingRecDel	=	$dailyFreezingObj ->delSoakingEntryQty($soakingEntryId);
			
					# Checking Records Existing for the selected Main Id
					$exisitingRecords = $dailyFreezingObj ->checkRecordsExist($soakingMainId);

					# Delete Main Rec		
					if (sizeof($exisitingRecords)==0) {					
						$soakingRecDel =	$dailyFreezingObj ->deleteSoaking($soakingMainId);
					}					
				//} // Confirm Check Ends Here
			}
		}
		//DIE();
		if ($soakingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSoaking);
			$sessObj->createSession("nextPage",$url_afterDelSoaking.$selection);
		} else {
			//if ($processAcConfirmed) $errDel = $msg_failAddSoaking."<br>Please make sure the Pre-Process entry you have selected is not settled/paid.";
			//else
			$errDel		=	$msg_failDelSoaking;
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
		$soakingRecords	= $dailyFreezingObj ->soakingRecPagingFilter($recordsFilterId, $recordsDate, $offset, $limit);
		$soakingRecordsSize	= sizeof($soakingRecords);
		// echo '<pre>';
			// print_r($soakingRecords);
		// echo '</pre>';
		$numrows	=  sizeof($dailyFreezingObj ->soakingRecFilter($recordsFilterId, $recordsDate));
		
		# Checking Entry Confirmed
		//$entryConfirmed	= $dailyFreezingObj ->chkEntryConfirmed($recordsDate);
						//18/10/08
		$prevDate = date("Y-m-d",mktime(0, 0, 0,$Date[1],$Date[0]-1,$Date[2]));
		
		# Check Prev date Entry
		//$prevDateEntryConfirmed = $dailyFreezingObj ->chkPrevDateEntryConfirmed($prevDate);
		$displayPrevdate = date("jS M Y", mktime(0, 0, 0, $Date[1], $Date[0]-1, $Date[2]));
		//$dppConfirmEnabled = $manageconfirmObj->isDPPConfirmEnabled();

		# Check RM CB Exist
		$ppEntryExist = $dailyFreezingObj ->chkRecExist('', $recordsDate);
	}
	
	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Returns all fish master records 
	//$fishMasterRecords =	$fishmasterObj->fetchAllRecords();
	$fishMasterRecords =	$fishmasterObj->fetchAllRecordsFishactive();
		
	
	#Select all Pre Processing Master Records based on fishId
	if ($addMode || $editMode) {
	
		if($addAnother==true) 
		{
			$fishId = ""; $rmLotID="";
		}
		elseif($editFishId!="" && $editRmLotId!="")
		{	
			$fishId = $editFishId;
			$rmLotID= $editRmLotId;	
			
			if($editProcessId!="")
			{
				$processCodeId=$editProcessId;
			}
		}
		elseif($editFishId)
		{	
			$fishId = $editFishId;
			if($editProcessId!="")
			{
			$processCodeId=$editProcessId;
			}	
		}
		elseif($editRmLotId)
		{	
			$rmLotID=$editRmLotId;
		}
		else 
		{
			$fishId	=	$p["selFish"];
			$rmLotID=	$p["rm_lot_id"];
			$processCode=	$p["processCode"];
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
	
		if ($recordSelectDate) {
			$entryDate = mysqlDateFormat($recordSelectDate);

			if ($editMode) $selRateListId = $processratelistObj->getPPRateList($entryDate);			
			if ($validDPPEnabled) {
				# get Valid Fish Recs
				$getFishRecords = $dailyFreezingObj ->getFishRecords($entryDate,'');
				$rmLotIds  = $dailyFreezingObj ->getLotRecords($entryDate,'');
				# Days Active PC Recs
				$daysActivePCRecs = $dailyFreezingObj ->getDaysActivePreProcessCodes($entryDate, $fishId, $selRateListId);
				//$daysActiveLotPCRecs = $dailyFreezingObj ->getDaysActivePreProcessLotCodes($entryDate, $lotId, $selRateListId);
			} 
			else 
			{
				# get Valid Fish Recs
				$getFishRecords = $dailyFreezingObj ->getAllFishAddedOnDate($entryDate,$rmLotID);
				
				#get Valid rmlotid for the date
				if($entryDate!="")
				{
				 
					$rmLotIds  = $dailyFreezingObj ->getLotIdAfterGradingLoad($entryDate);
				}
				
				#get Valid process code if lotid not available get all process code of a fish else only display process code added in rmlotid 
				$process_codes= $dailyFreezingObj ->getAllProcessCodeForFishAddedOnDate($entryDate,$fishId,$rmLotID);
				
				if ($addMode || $editMode)
				{
					if(($selRateListId!="" && $rmLotID=="" && $fishId!="" && $processCode=="")  || ($selRateListId!="" && $rmLotID=="" && $fishId!="" && $processCode!="") || ($selRateListId!="" && $rmLotID!="" && $fishId!="" && $processCode=="") || ($selRateListId!="" && $rmLotID!="" && $fishId!="" && $processCode!="")  ) 
					{
						$daysActivePCRecs = $dailyFreezingObj ->soakingRecs($entryDate,$selRateListId,$rmLotID,$fishId,$processCode);
					}
					
				}
				
			}
		}
	}

	
	$harvestingchemicalNameRecs = $dailyFreezingObj ->WeightmentHarvestingChemical();
	
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 0;	
	else $mode = "";

	# Display heading
	if ($editMode) $heading	=	$label_editSoaking;
	else $heading	=	$label_addSoaking;
	
	$ON_LOAD_SAJAX = "Y"; // SAJAX, settings for TopLeftNav	

	//$help_lnk="help/hlp_soaking.html";

	$ON_LOAD_PRINT_JS	= "libjs/DailyFreezing.js";
	//echo "hii";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
 <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
 <script type="text/javascript" src="libjs/jquery.timepicker.js"></script>
 <link href="libjs/jquery.timepicker.css" rel="stylesheet" type="text/css">
 
<form name="frmSoaking" id="frmSoaking" action="Soaking.php" method="Post">
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
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSoaking(document.frmSoaking);">
										</td>
									<?} else{?>
									<td align="center" colspan="3">
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddSoaking" id="cmdAddSoaking" class="button" value=" Save " onClick="return validateSoaking(document.frmSoaking);">
											<? if (!$validDPPEnabled) {?>
											&nbsp;&nbsp;<input name="cmdSaveAdd" type="submit" class="button" id="cmdSaveAdd" onClick="return validateSoaking(document.frmSoaking);" value=" Save &amp; Add ">
											<? }?>
									</td>
									<input type="hidden" name="cmdAddNew" value="1">
									<?}?>
											</tr>
											<input type="hidden" name="hidsoakingId" value="<?=$soakingMainId;?>">
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
					<tr><TD class="fieldName" colspan="4" align="center" style="text-align:center;"><span id="divEntryExistTxt" style='line-height:normal; font-size:14px; color:red;'></span></TD></tr>
											<tr>
											  <td colspan="4" nowrap >
											
				
				<tr >	
<td>
					<table width="auto" align="center">
                                                <tr>
                                                  <td  nowrap class="fieldName">Date:</td>
                                                  <td  nowrap>
												<input type="text" id="selectDate" name="selectDate" size="8" value="<?=$recordSelectDate;?>"   onchange="<? if ($addMode) {?>this.form.submit();<?} else if($editMode && $validDPPEnabled) { ?>this.form.editId.value=1;this.form.submit();<? } else {?> xajax_chkEntryExist(document.getElementById('selFish').value, document.getElementById('selectDate').value, '<?=$dailyPProcessMainId?>', '<?=$mode?>', document.getElementById('selDate').value); <?}?>  xajax_getRMLotId(document.getElementById('selectDate').value); " autocomplete="off" <? if ($editMode && $validDPPEnabled) {?> readonly <?}?> />
											&nbsp;	</td>
											<td class="fieldName" nowrap>RM Lot ID:</td>
											<td>
											<!--<select onchange="functionLoad(this);" id="rm_lot_id" name="rm_lot_id">-->
											<select name="rm_lot_id" id="rm_lot_id" <?					 
											if($addMode==true) {			
											?> onchange=" functionLoad(this);" <?php }
											elseif($editMode==true) { ?> onchange="assignValue(this.form,'<?php echo $editId;?>','editId');this.form.submit();" <?php }?>>
										
												<option value=""> -- Select Lot ID --</option>
														<?php
														if(sizeof($rmLotIds) > 0)
																		{
																			foreach($rmLotIds as $lotID)
																			{	
																				$sel = '';
																				if($rmLotID == $lotID[0]) $sel = 'selected="selected"';
																																	
																				echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																			}
																		}
																	?>							
													</select>								
											</td>
                                              
                                            <td class="fieldName" nowrap>Fish:</td>
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
					foreach ($getFishRecords as $fId) {
						$selected 	= "";
							$fID=$fId[0];
							$fName=$fId[1];
						if ($fishId==$fID) {
							$selected = " selected ";
						}

					?>
                                     <option value="<?=$fID;?>" <?=$selected?> ><?=$fName;?></option>
                                     <?
					}
					?>
                                                    </select></td>
													
									<td class="fieldName" nowrap>Process code:</td>
									<td>
                              		   <select name="processCode" id="processCode" <?php if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?>
											onchange="assignValue(this.form,'<?php echo $editId;?>','editId');this.form.submit();" <?php }?> >
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
<tr id='display1'>
	        <td colspan="4" nowrap style="padding-left:5px; padding-right:5px;">
	<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999">
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process<br />Code <br></td>
               				
				<td class="listing-head" style="padding-left:5px; padding-right:5px;"> Grade/Count</td>
				 <td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-IN<!--Total Qty--><br/>Type</td>
				<!--Today's <br>Arrival Qty--> </td>	
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">SOAK-IN <br /> Qty</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">SOAK-IN <br /> Time</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">SOAK-OUT <br/>Type </td>
				
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">SOAK-OUT <br /> Qty</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">SOAK-OUT <br /> Time</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Temperature </td>
	            <td class="listing-head" style="padding-left:5px; padding-right:5px;">Gain(%) </td>
                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Chemical <br /> Used</td>
                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Chemical <br />QtY</td>
        </tr>
		
			<?php
		$Date1		= explode("/",$recordSelectDate);
		$selectDate	= $Date1[2]."-".$Date1[1]."-".$Date1[0];
		$j=0;	
		$colSpan ==9;
		//$colSpan = 9+sizeof($preProcessorRecords);
		$prevFishId="";
		$selFish = "";
		$pcCountArr = array();
		$avQtyArr	= array();
		foreach ($daysActivePCRecs as $processId=>$dps) {			
			$selFish 	= $dps[0];
			$selFishName 	= $dps[1];
			
			//------------------------------		
		$ProcessCodes		= $dps[3];	
		$processFrom = $dps[2];
		$rmlotid= $dps[4];
		$dapr=$dailyFreezingObj ->soakingRecEditsingle($entryDate,$fishId,$rmLotID,$processFrom);
	
		if ($ProcessCodes!="") {
			
			$soakingEntryId	=$dapr[8];
			$soakingType=$dapr[9];
			$soakIn=$dapr[10];
			if($soakIn=="") $soakIn=$dapr[11];
			$soakInQnty=$dapr[12];
			$soakInTime=$dapr[13];
			$soakOut=$dapr[14];
			if($soakOut=="") $soakOut=$dapr[15];
			$soakOutQnty=$dapr[16];
			$soakOutTime=$dapr[17];
			$temperature=$dapr[18];
			$gain=$dapr[19];
			$chemicalUsed=$dapr[20];
			$chemicalQnty=$dapr[21];
			$pcCountArr[$processFrom] = $j;
			
				### Grade/Count selected
				$countSelected=$gradeSelected="";
				if($soakingType=="1") $countSelected="selected";
				elseif($soakingType=="2") $gradeSelected="selected";
				//echo $soakingType;
				
				### Grade/Count in soakin and soak out
				if($soakingType=="") 
				{ 
					$displayGrade=$displayCount="display:none";
				}
				elseif($soakingType=="1") 
				{ 	
					$displayCount="display:display";
					$displayGrade="display:none";
				}
				elseif($soakingType=="2") 
				{ 
					$displayGrade="display:display";
					$displayCount="display:none";	
				}					
									
				
		$grades = $dailyFreezingObj ->getProcessGradeEdit($processFrom);		
	?>
	
       <tr bgcolor="#FFFFFF">
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="25">
			<?=$ProcessCodes?>
			<input type="hidden" name="soakingEntry_<?=$j?>" id="soakingEntry_<?=$j?>" value="<?=$soakingEntryId?>">
			<input type="hidden" name="selFishId_<?=$j?>" id="selFishId_<?=$j?>" value="<?=$selFish?>">
			<input type="hidden" name="processFrom_<?=$j?>" id="processFrom_<?=$j?>" value="<?=$processFrom?>" readonly>
			<!--<input type="text" name="rowNum_<?=$j?>" id="rowNum_<?=$j?>" value="1" readonly>-->
			<?=$hiddenFields?>
			<input type="hidden" name="lotidAvailable_<?=$j?>" id="lotidAvailable_<?=$j?>" value="<?=$rmlotid?>" readonly>
			
			
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<select name="gradeName_<?=$j?>" id="gradeName_<?=$j?>" onchange="xajax_getGrade(document.getElementById('gradeName_<?=$j?>').value,<?=$processFrom?>,<?=$j?>);">
			<option value="0">--Select--</option>
				<option value="1" <?=$countSelected?>>Count</option>
				<option value="2" <?=$gradeSelected?>>Grade</option>
			</select>
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; ">
		
			<select name="soak-inGrade_<?=$j?>" id="soak-inGrade_<?=$j?>" style="<?=$displayGrade?>" >
				<?php foreach($grades as $grd)
				{
					$grdID=$grd[0];
					$grdNm=$grd[1];
					if($soakIn==$grdID) $sel="selected"; else $sel="";
				?>
				
				<option value="<?=$grdID?>" <?=$sel?>> <?=$grdNm?></option>
				<?php
				}
				?>
			</select>
			<input type="text" name="soak-inCount_<?=$j?>" id="soak-inCount_<?=$j?>" size="8"  style="text-align:right; border:none; <?=$displayCount?>" autocomplete="off" value="<?=$soakIn?>" title="Soak in count">

		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="soak-inQty_<?=$j?>" id="soak-inQty_<?=$j?>" size="8"  style="text-align:right; border:none;" autocomplete="off" value="<?=$soakInQnty?>" title="Soak in Qty" >
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="soak-inTime_<?=$j?>" id="soak-inTime_<?=$j?>" size="8"  style="text-align:right; border:none;" autocomplete="off" value="<?=$soakInTime?>" title="Soak in Time" onfocus="getSoakInTime(<?=$j?>);" <?/*onblur="return checkTimeFormat('<?=$j?>','in');"*/?> >
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<select name="soak-outGrade_<?=$j?>" id="soak-outGrade_<?=$j?>" style="<?=$displayGrade?>">
				<?php foreach($grades as $grd)
				{
					$grdID=$grd[0];
					$grdNm=$grd[1];
					if($soakOut==$grdID)  $selt="selected"; else $selt="";
				?>
				
				<option value="<?=$grdID?>" <?=$selt?>> <?=$grdNm?></option>
				<?php
				}
				?>	
			</select>
			
			<input type="text" name="soak-outCount_<?=$j?>" id="soak-outCount_<?=$j?>" size="8"  style="text-align:right; border:none; <?=$displayCount?>" autocomplete="off" value="<?=$soakOut?>" title="Soak out count">
		</td>
		
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="soak-outQty_<?=$j?>" id="soak-outQty_<?=$j?>" size="8" onKeyUp="gaincal(<?=$j?>);" style="text-align:right; border:none;" autocomplete="off" value="<?=$soakOutQnty?>" title="Soak out Qty">
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="soak-outTime_<?=$j?>" id="soak-outTime_<?=$j?>" size="8"  style="text-align:right; border:none;" autocomplete="off" value="<?=$soakOutTime?>" title="Soak out Time"  onfocus="getSoakOutTime(<?=$j?>);">
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="temperature_<?=$j?>" id="temperature_<?=$j?>" size="8"  style="text-align:right; border:none;" autocomplete="off" value="<?=$temperature?>" title="Temperature">
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="gain_<?=$j?>" id="gain_<?=$j?>" size="8" readonly style="text-align:right; border:none;" autocomplete="off" value="<?=$gain?>" title="Gain">
		</td>
		
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<select name='chemicalName_<?=$j?>'  id='chemicalName_<?=$j?>' tabindex=1   >";
					<option value=''>--select--</option>";
					<?php 
										foreach ($harvestingchemicalNameRecs as $dcw) {
						$chemicalNameId = $dcw[0];
						$chemicalName	= stripSlash($dcw[1]);
						$selected="";
						if($chemicalUsed==$chemicalNameId ) echo $selected="Selected";
					  ?>
                       <option value="<?=$chemicalNameId?>" <?=$selected?>><?=$chemicalName?></option>
                       <? }
						?>
						</select>
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="chemicalQty_<?=$j?>" id="chemicalQty_<?=$j?>" size="8"  style="text-align:right; border:none;" autocomplete="off" value="<?=$chemicalQnty?>" title="Chemical Quantity">
		</td>
		
					 
		
      </tr>
	<?php 					
		//	$prevFishId=$selFish;
		//	} // Landing center loop ends here			
			$prevFishId=$selFish;
		} // Loop Ends here
		$j++;
	}
	?>	
	
	</table>						
			
</td></tr>

	
<? } 
else {?>
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
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateSoaking(document.frmSoaking);">
		</td>
	<?} else{?>
	<td align="center" colspan="3">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAddSoaking" id="cmdAddSoaking1" class="button" value=" Save " onClick="return validateSoaking(document.frmSoaking);">
			<? if (!$validDPPEnabled) {?>
			&nbsp;&nbsp;<input name="cmdSaveAdd" type="submit" class="button" id="cmdSaveAdd1" onClick="return validateSoaking(document.frmSoaking);" value=" Save &amp; Add ">
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
			<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Soaking </td>
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
                            <input type="text" id="selDate" name="selDate" size="8" value="<?=$selDate?>">&nbsp;</td>
											
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
					<!--<input type="button" name="refreshAvailableQty" value="Refresh RM Qty" class="button" onclick="return updateAvailableRMQty(document.getElementById('selDate').value);" title="Click here to update the days available RM Qty. " />-->
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
			<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$soakingRecordsSize;?>);"><? }?>&nbsp;
			<? if($add==true && (!$ppEntryExist || !$validDPPEnabled ) ){?>
				<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>
			<? } else if ($edit && $ppEntryExist) {?>
				<input type="submit" value=" Edit " name="cmdEdit" class="button" <? if ($entryConfirmed || sizeof($soakingRecords)<=0) {?> disabled="true" <? }?>>
			<? }?>
			<? if($add==true){?>
			<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>-->
			<? }?>&nbsp;
			<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSoaking.php?selFilter=<?=$recordsFilterId?>&selDate=<?=$recordsDate?>',700,600);"><? }?>&nbsp;
			<?/* if ($confirm==true && $dppConfirmEnabled && !$entryConfirmed) {?>
			<input type="button" name="cmdConfirm" id="cmdConfirm" value="Confirm" class="button" onclick="return confirmDPPEntry('<?=$selDate?>');" <? if ($entryConfirmed || sizeof($soakingRecords)<=0) {?> disabled="true" <? }?> />
			<? } */?>
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
	if (sizeof($soakingRecords)>0) {
		$i	= 0;
	?>
        <? if($maxpage>1){?>
	<tr bgcolor="#FFFFFF"><td colspan="<?=12+sizeof($preProcessorRecords);?>" style="padding-right:10px"><div align="right">
	<?php 				 			  
	$nav  = '';
	for ($page=1; $page<=$maxpage; $page++) {
		if ($page==$pageNo) {
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
		} else {
		      	$nav.= " <a href=\"soaking.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
			//echo $nav;
		}
	}
	if ($pageNo > 1) {
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"soaking.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
 	} else {
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
	}

	if ($pageNo < $maxpage) {
   		$page = $pageNo + 1;
   		$next = " <a href=\"soaking.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
	 } else {
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
	}
	// print the navigation link
	$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
	echo $first . $prev . $nav . $next . $last . $summary; 
	?>	
	<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
        <tr  bgcolor="#f2f2f2" align="center" > 
                 <td width="20"> <input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
                        <td class="listing-head" nowrap style="padding-left:2px; padding-right:2px;">RM Lot ID</td>
						<td class="listing-head" nowrap style="padding-left:2px; padding-right:2px;">Fish</td>
                        <td class="listing-head" style="padding-left:2px; padding-right:2px;">Process Code </td>
                        <!--<td class="listing-head" style="padding-left:2px; padding-right:2px;" nowrap>O/B Qty<br>(Kg)</td>-->
						<td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-IN<br>
						Type</td>
						<td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-IN<br>Count
						</td>
                        <td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-IN<br>Grade
						</td>
						 <td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-IN<br>
						Qty</td>
						 <td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-IN <br>
						Time</td> 
						<td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-OUT<br>
						Count</td>
						 <td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-OUT<br>Grade
						</td>
						 <td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-OUT<br>
						Qty</td>
						 <td class="listing-head" style="padding-left:2px; padding-right:2px;">SOAK-OUT<br>
						Time</td>
						<td class="listing-head" style="padding-left:2px; padding-right:2px;">Temperature<br></td>
						<td class="listing-head" style="padding-left:2px; padding-right:2px;">Gain(%)<br></td>
						<td class="listing-head" style="padding-left:2px; padding-right:2px;">Chemical
						<br>Used</td>
                        <td class="listing-head" style="padding-left:2px; padding-right:2px;">Chemical<br>
						QtY</td>
                        
                        <? if($edit==true && !$validDPPEnabled){?>
                        	<td class="listing-head" nowrap></td>
			<? }?>
                      </tr>
                      <?php 
			
			foreach ($soakingRecords as $pr) {
				$i++;
				$soakingId	=	$pr[0];
				$rmlotid	=$pr[21];
				$fishName	=$pr[22];
				$ProcessCode	=$pr[23];
				$soakingEntryId=$pr[5];
				/*$sfishId		=	$pr[1];	
				$fishName		= stripSlash($fishmasterObj->findFishName($sfishId));	
				$preProcessId		=	$pr[4];
				$processRec		=	$processObj->find($preProcessId);
				//$preProcessCommission	=	$processRec[5];
				//$preProcessCriteria	=	$processRec[6];
				$preProcessCode		=	$processRec[7];
				//$processRate		=	$dailyFreezingObj ->findProcessRate($preProcessId);		
				$openingBalQty		=	$pr[5];
				$arrivalQty		=	$pr[6];
				$totArrivalQty		+= 	$arrivalQty;
	
				$totalArrivalQty	=	$pr[7]; //from qry
				$soakingEntryId	= 	$pr[3];
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
				$soakingAmount += $totalPreProcessAmt;
				*/
				# New calculation
				/*$totalPreProcessAmt = 0;
				$diffCalcUsed = false;
				foreach ($preProcessorRecords as $ppr) {
					$mPrePId = $ppr[0];
					$ppRec = $dailyFreezingObj ->findPreProcessorRec($soakingEntryId,$mPrePId);
					$ppQty = $ppRec[3];
					$preProcessorAmt = 0;
					if ($ppQty!=0) {
						list($ppeRate, $ppeCommission, $ppeCriteria, $ppYieldTolerance) = $dailyFreezingObj ->getPProcessorExpt($preProcessId, $mPrePId);
						/*
						$selPPRate = ($ppeRate!=0)?$ppeRate:$processRate;
						$selPPCommi = ($ppeCommission!=0)?$ppeCommission:$preProcessCommission;
						$selPPCriteria = ($ppeRate!=0)?$ppeCriteria:$preProcessCriteria;
						*/
						/*$selPPRate = $ppeRate;
						$selPPCommi = $ppeCommission;
						$selPPCriteria = ($ppeRate!=0)?$ppeCriteria:$preProcessCriteria;
						$selYieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;

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
				} */// PProcessor Loop Ends here
				# New Calculation ends here
				/*$soakingAmount += $totalPreProcessAmt;
				$selLandingCenter ="";		
				if ($pr[13]!=0) $selLandingCenter = $landingcenterObj->findLandingCenter($pr[13]);	

				$confirmStatus	= $pr[14];
				$editDisabled = "";	
				//if ($confirmStatus=='Y' && $reEdit==false) {
				if ($confirmStatus=='Y') {
					$editDisabled = "disabled";
				}
				$dppAvailableQty = number_format($pr[15],2,'.','');
				$totalDppAvailableQty += $dppAvailableQty;	
				$autoGeneratedCalc = $pr[16];*/

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
							
				/*$diffCalcStyle = "";
				$diffCalcMsg	= "";								
				if ($diffCalcUsed) {
					$diffCalcStyle = "background-color: #FFFFCC;";
					 $diffCalcMsg = "onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Diff yield is greater than tolerance yield.');\" onMouseout=\"this.style.backgroundColor='#FFFFCC';UnTip();\" ";
				} */
				/*$auQtyStyle 	= "";
				$auQtyMsg 	= "";
				if ($autoGeneratedCalc=='Y') {
					$auQtyStyle = "background-color: #e6fff8;";
					$auQtyMsg = "onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Automatic calculation is applied in Actual used qty.');\" onMouseout=\"this.style.backgroundColor='#e6fff8';UnTip();\" ";
				}*/

				/* else {
					$displayRow = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
				}*/

				/*$selProcessFromId = $pr[17];
				$showAvailableCalc = "";
				if (!isset($pFromArr[$selProcessFromId])) {
					# Display Calc
					$displayAvailableCalc = $dailyFreezingObj ->disAvailableQtyCalc($selProcessFromId, $recordsDate);
					if ($dppAvailableQty!=0) {
						$showAvailableCalc = "onMouseover=\"ShowTip('$displayAvailableCalc');\" onMouseout=\"UnTip();\" ";
					}
					$pFromArr[$selProcessFromId] = $dppAvailableQty;
				}*/
			?>
                <tr <?=$listRowMouseOverStyle?>> 
                        <td width="20" height="25"> 
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$soakingId;?>" class="chkBox">
				<input type="hidden" name="soakingEntryId_<?=$i?>" value="<?=$soakingEntryId?>" />
			</td>
				<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$rmlotid?></td>
                        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$fishName;?></td>
                        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$ProcessCode?>
				
                        <!--<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?//=$openingBalQty?></td>-->
			<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
				<?php if($pr[7]=="1")
						{
							echo "Count";
							$grade_in=''; $grade_out='';
						}
						elseif($pr[7]=="2")
						{
							echo "Grade";
							$grade1=$dailyFreezingObj ->getGradeInOrOut($pr[9]);
							$grade_in=$grade1[0];
							$grade2=$dailyFreezingObj ->getGradeInOrOut($pr[13]);
							$grade_out=$grade2[0];
						}
						?>
			</td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;" ><?=$pr[8]?></td>
						<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$grade_in?></td>
						 <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[10]?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[11]?> </td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[12]?></td>
						 <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$grade_out?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[14]?> </td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[15]?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[16]?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; " ><?=$pr[17]?></td>
						<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; " ><?=$pr[24]?></td>
						<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; " ><?=$pr[19]?></td>
			<? if($edit==true && !$validDPPEnabled){?>
                        <td class="listing-item" width="40" align="center">
				<input type="submit" value=" Edit " name="cmdEdit_<?=$i?>" id="cmdEdit_<?=$i?>" onClick="assignValue(this.form,<?=$soakingId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,<?=$soakingEntryId?>,'soakingEntryId');" <?=$editDisabled?>>
			</td>
			<? }?>
                      </tr>
                      <?php
					}
			?>
                    <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                    <input type="hidden" name="editId" value="">
					<input type="hidden" name="soakingEntryId" value="">
                    <input type="hidden" name="editSelectionChange" value="0">
                      
			<?/*	  <tr bgcolor="white">
			<td height="10" colspan="10" align="right" class="listing-head">Total:</td>
			<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
				<strong><?=number_format($totalDppAvailableQty,2);?></strong>
			</td>
			<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
				<strong><?=number_format($totArrivalQty,2);?></strong>
			</td>
                       
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
                        <td height="10" class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><strong><? echo number_format($soakingAmount,2);?></strong></td>
			<? if($edit==true && !$validDPPEnabled){?>
                        <td height="10" align="center">&nbsp;</td>
			<? }?>
                        </tr>*/?>
						
						
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF"><td colspan="<?=12+sizeof($preProcessorRecords);?>" style="padding-right:10px"><div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
			} else {
			      	$nav.= " <a href=\"soaking.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"soaking.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"soaking.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
	  <?
	  	} else {
	  ?>
                      <tr bgcolor="white"> 
                        <td colspan="13"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
                      </tr>
                      <?
					  	}
						?>
                    </table><input type="hidden" name="existRecordsFilterId" value="<?=$recordsFilterId?>"></td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$soakingRecordsSize;?>);"><? }?>&nbsp;
							<? if($add==true && (!$ppEntryExist || !$validDPPEnabled ) ){?>
								<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>
							<? } else if ($edit && $ppEntryExist) {?>
								<input type="submit" value=" Edit " name="cmdEdit" class="button" <? if ($entryConfirmed || sizeof($soakingRecords)<=0) {?> disabled="true" <? }?>>
							<? }?>
							<? if($add==true){?>
								<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button" <? if (!$prevDateEntryConfirmed && $dppConfirmEnabled) {?> disabled="true" <? }?>>-->
							<? }?>&nbsp;
							<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSoaking.php?selFilter=<?=$recordsFilterId?>&selDate=<?=$recordsDate?>',700,600);"><? }?>
										&nbsp;
										<?/* if ($confirm==true && $dppConfirmEnabled && !$entryConfirmed) {?>
											<input type="button" name="cmdConfirm" id="cmdConfirm1"  value="Confirm" class="button" onclick="return confirmDPPEntry('<?=$selDate?>');" <? if ($entryConfirmed || sizeof($soakingRecords)<=0) {?> disabled="true" <? }?>>
										<? } */?>
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
	if ($addMode!=""  && $fishId!="" && $recordSelectDate!="" && !$validDPPEnabled) {
?>	
	<script language="JavaScript">
		xajax_chkEntryExist('<?=$fishId?>','<?=$recordSelectDate?>', '<?=$soakingMainId?>',  '<?=$mode?>', '','<?=$processCode?>');
	</script>
<?php
	}
?>
<?php
	if (($addMode!="" || $editMode!="") && $recordSelectDate!="" && $validDPPEnabled) {
?>	
	<script language="JavaScript">
		xajax_chkDPPEntryExist('<?=$recordSelectDate?>', '<?=$mode?>');
	</script>
<?php
	}
?>

<?php
	if ($editMode) {
	// Total Arrival Qty
	$cntsz=sizeof($daysActivePCRecs);
?>
<script>
	
	/*var cnt='<?php echo $cntsz;?>';
	//alert("hii");
	for(j=0; j<cnt; j++)
	{
		var processFrom ='<?php echo  $daysActivePCRecs[0][2];?>';
		xajax_getGrade(document.getElementById('gradeName_'+j).value,'processFrom',j);
		//alert(document.getElementById('gradeName_'+j).value);
	}*/
	//totalArrivalQty();
	//updateAvailableQty();
</script>
<?php
	}
?>


<SCRIPT>
          


	function gaincal(id)
	{
		var soakin=document.getElementById('soak-inQty_'+id).value;
		var soakout=document.getElementById('soak-outQty_'+id).value;
		
		//alert(document.getElementById('soakInQuantity').value);
		 //var diff=parseInt(soakout) - parseInt(soakin);
		//var total=parseInt(soakout) + parseInt(soakin);
	//alert(diff+'--'+total);
	var inn=parseInt(soakin) ;
	var out=parseInt(soakout) ;
		//var gainvalue=(diff/total)*100;
		var gainvalue=(out/inn);
		document.getElementById("gain_"+id).value=gainvalue;
		
	}
	</SCRIPT>




<script>
	jQuery(document).ready(function(){
	
		//var soakType = '<?php echo $soakingType;?>';
		//jQuery('#gradeName_0').val(soakType);
		//alert(procurementAvailable);
		
		/*if(lotIdAvailable == '1')
		{
			document.getElementById('lotIdAvailable').checked = true;
			jQuery('#autoUpdate').hide();
			jQuery('#autoUpdate2').show();
			
		}*/
		
	
	});

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
		
	
	});*/

	</script>
<script>
 /*$('#gradename_1').on('change',function(){
        if( $(this).val()==="Count"){
			$("#soak-inCount1").show()
			$("#soak-inGrade1").hide()
		}
        else{
			$("#soak-inCount1").hide()
			$("#soak-inGrade1").show()
        }
    });*/
</script>
</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>