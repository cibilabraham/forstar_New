<?php
	require("include/include.php");
	require_once("lib/FrozenPackRating_ajax.php");
	ob_start();

	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= false;
	$allocateMode		= false;
	$isSearched		= false;
	$selection 	= "?pageNo=".$p["pageNo"];
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false;
	
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

	# Reset Data
	if ($p["qeName"]!="") $qeName = $p["qeName"];
	if ($p["freezingStage"]!="") $freezingStage = $p["freezingStage"];
	if ($p["selQuality"]!="") $selQuality = $p["selQuality"];
	if ($p["eUCode"]!="") $eUCode = $p["eUCode"];
	if ($p["brand"]!="") $brand = $p["brand"];
	if ($p["frozenCode"]!="") $frozenCode = $p["frozenCode"];
	if ($p["mCPacking"]!="") $mCPacking = $p["mCPacking"];
	if ($p["frozenLotId"]!="") $frozenLotId = $p["frozenLotId"];
	if ($p["exportLotId"]!="") $exportLotId = $p["exportLotId"];
	if ($p["customer"]!="") $selCustomerId = $p["customer"];
	if ($p["selQuickEntryList"]!="") $selQuickEntryList = $p["selQuickEntryList"];
	//echo $selQuickEntryList;

	//$frznPkgRatingObj->insertTempPCRecs("10,11", '');
	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
		# Delete Blank Recs
		if (!$selQuickEntryList) $delTempGradeRec = $frznPkgRatingObj->delTempGradeRec($userId, '');
	}

	#Cancel 	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;
		$editMode	=	false;
		# Delete Blank Recs
		$delTempGradeRec = $frznPkgRatingObj->delTempGradeRec($userId, '');
		$selQuickEntryList ="";
		$fznPkgQEListId = "";
		$editId = "";
		$p["editId"] = "";
		$p["selQuickEntryList"] = "";
	}
	//$result=$frznPkgRatingObj->getAllGradeForProcessCode(36);
	//printr($result);
	# Add
	if ($p["cmdAdd"]!="" || $p["cmdSaveAndAddNew"]!="") {
		//die();
		$qeName		= addSlash(trim($p["qeName"]));
		$freezingStage	= $p["freezingStage"];
		$frozenCode	= $p["frozenCode"];
		$selQuality 	= $p["selQuality"];
		$selProcessor 	= $p["selProcessor"];
				
		$tableRowCount		= $p["hidTableRowCount"];
		$gradeRowCount		= $p["hidGradeRowCount"];
		
		if ($frozenCode!="" && $qeName!="") {
			$ratelistid=$frznPkgRatingObj->getRateListId();
			$fznPkRateIns =	$frznPkgRatingObj->addFznPckRate($qeName,$freezingStage,$frozenCode,$selQuality,$selProcessor,$ratelistid,$userId);
			#Find the Last inserted Id 
			if ($fznPkRateIns) $qelId = $databaseConnect->getLastInsertedId();
			if ($tableRowCount>0) {
				//$delTempGradeRec 	= $frznPkgRatingObj->delTempGradeRec($userId);
				for ($i=0; $i<$tableRowCount; $i++) 
				{
					$status = $p["status_".$i];
					if ($status!='N') 
					{
						$selFish 	= $p["selFish_".$i];
						$selProcessCode = $p["selProcessCode_".$i];
						$rateTag = $p["rateTag_".$i];
						//$gradeArray=$frznPkgRatingObj->getAllGradeForProcessCode($selProcessCode);
						if ($selFish!="" && $selProcessCode!="" && $qelId)
						{
							$frznPkngQELEntryRecIns = $frznPkgRatingObj->addFznPkgRateEntry($qelId, $selFish, $selProcessCode);
							$frznPkgId = $databaseConnect->getLastInsertedId();
							//$processorId='';
							if ($rateTag!="") 
							{
								$rates=json_decode($rateTag);
								$rs=count($rates);
								//echo $rs;
								for($j=0; $j<$rs; $j++)
								{
									$gradeTot=$rates[$j];
									//printr($gradeTot);
									$rate=$gradeTot->Rate;
									foreach($gradeTot->GradeId as $grades)
									{
										//$gradeIds[]=$grades;
										$insGradeRec = $frznPkgRatingObj->addSelGradeRec($qelId,$frznPkgId,$grades,$rate,$userId);
									}
									//printr($gradeIds); 
									//echo $grade[$i];
								}
							}
						}
					}
				}
			} // Row Count Loop Ends Here
			
						
			if ($fznPkRateIns) {
				$sessObj->createSession("displayMsg", $msg_succAddFrznPkRating);
				
				if ($p["cmdAdd"]!="") {				
					$addMode = false;
					$sessObj->createSession("nextPage",$url_afterAddFrznPkngListing.$selection);
				} else if ($p["cmdSaveAndAddNew"]!="") {
					$editMode	= false;
					$addMode	= true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$qeName = "";
					$p["frozenLotId"]	=	"";
					$frozenLotId		= 	"";
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
					$selCustomerId 		=	 "";
					$p["customer"] 		=	 "";
					$selQuality 		=	 "";
					$p["selQuality"] 	=	 "";
					$selQuickEntryList 	= 	"";
					$p["selQuickEntryList"]	=	"";
				} 
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddFrznPkRating;
			}
			$fznPkRateIns	=	false;
		}
	}
	
	# Edit Packing
	if ($p["editId"]!="" || $selQuickEntryList!="") {		
		# Delete Blank Recs
		if (!$selQuickEntryList) $delTempGradeRec = $frznPkgRatingObj->delTempGradeRec($userId, '');

		if ($selQuickEntryList) $editId = $selQuickEntryList;
		else $editId	= $p["editId"];		

		if (!$selQuickEntryList) $editMode = true;
		# Find Selected Rec
		$fznPkQuickEntryListRec	= $frznPkgRatingObj->find($editId);
		//echo $selQuickEntryList;
		//if (!$selQuickEntryList) 
		$fznPkgQEListId 	= $fznPkQuickEntryListRec[0];
		$qeName			= $fznPkQuickEntryListRec[1];			
		$freezingStage		= $fznPkQuickEntryListRec[2];
		$frozenCode		= $fznPkQuickEntryListRec[3];
		$selQuality		= $fznPkQuickEntryListRec[4];
		$selProcessorId =$fznPkQuickEntryListRec[5];		
		
		#FrozenLot Id records for the selected date
		$sDate = date("Y-m-d");
		$frozenLotIdRecords = $frznPkgRatingObj->fetchLotIdRecords($sDate);

		# Get Entry Recs
		if (!$selQuickEntryList) 
		{
			$getRawDataRecs = $frznPkgRatingObj->getQELRawRecords($fznPkgQEListId);
		}
		else 
		{ // Copy from section
			# Delete Blank Recs
			//echo "hii";
			$delTempGradeRec = $frznPkgRatingObj->delTempGradeRec($userId, '');
			# Get Selected Grade Recs
			$getRawDataRecs = $frznPkgRatingObj->getQELRawRecords($selQuickEntryList);
			//$fznPkgQEListId = 0;
			# Get Grade Recs from DB	
			$getGdRecords	= $frznPkgRatingObj->getSelGradeRecords('', $selQuickEntryList);
			//echo "dfgfh";
			//die();
			foreach ($getGdRecords as $gdr) {
				$iGradeId = $gdr[0];
				$iDisOrderId = $gdr[3];
				$rate = $gdr[5];
				$processor="";
				# Add Temp Rec
				$frznPkgRatingObj->addGradeRec('0', $iGradeId, $iDisOrderId, $rate,$userId,$processor);
			}
		}		
	}

	

	# update a rec
	if ($p["cmdSaveChange"]!="") {
		
		$fznPkngRateId = $p["hidQuickEntryListId"];
		//echo $fznPkngRateId;
		//die();
		$qeName		= addSlash(trim($p["qeName"]));
		$freezingStage	= $p["freezingStage"];
		$frozenCode	= $p["frozenCode"];
		$selQuality 	= $p["selQuality"];
		$selProcessor 	= $p["selProcessor"];
		
		$tableRowCount		= $p["hidTableRowCount"];
		$gradeRowCount		= $p["hidGradeRowCount"];
	//echo $gradeRowCount;
	//die();
		$ratelistid=$frznPkgRatingObj->getRateListId();
		if ($fznPkngRateId!="" && $frozenCode!="" && $qeName!="") {
			$updateFznPknEntryRec =	$frznPkgRatingObj->updateFznPkngRateRec($fznPkngRateId,$qeName,$freezingStage,$frozenCode,$selQuality,$selProcessor,$ratelistid);
		
			# Del Entry Recs
			$delRawDataEntryRecs = $frznPkgRatingObj->delRateRawData($fznPkngRateId);
			$delTempGradeRec = $frznPkgRatingObj->delTempGradeRec($userId, $fznPkngRateId);
			if ($tableRowCount>0) {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selFish 	= $p["selFish_".$i];
						$selProcessCode = $p["selProcessCode_".$i];
						$rateTag = $p["rateTag_".$i];
						//$gradeArray=$frznPkgRatingObj->getAllGradeForProcessCode($selProcessCode);
						if ($selFish!="" && $selProcessCode!="" && $fznPkngRateId!="")
						{
							$frznPkngQELEntryRecIns = $frznPkgRatingObj->addFznPkgRateEntry($fznPkngRateId, $selFish, $selProcessCode);
							$frznPkgId = $databaseConnect->getLastInsertedId();
							//$processorId='';
							if ($rateTag!="") 
							{
								$rates=json_decode($rateTag);
								$rs=count($rates);
								//echo $rs;
								for($j=0; $j<$rs; $j++)
								{
									$gradeTot=$rates[$j];
									//printr($gradeTot);
									$rate=$gradeTot->Rate;
									foreach($gradeTot->GradeId as $grades)
									{
										//$gradeIds[]=$grades;
										$insGradeRec = $frznPkgRatingObj->addSelGradeRec($fznPkngRateId,$frznPkgId,$grades,$rate,$userId);
									}
									//printr($gradeIds); 
									//echo $grade[$i];
								}
							}
						}
					}
				}
			} // Row Count Loop Ends Here	
		}
		//die();
		if ($updateFznPknEntryRec) {
			$sessObj->createSession("displayMsg", $msg_succUpdateFrznPkRating);
			$sessObj->createSession("nextPage", $url_afterUpdateFrznPkngListing.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failFrznPkRatingUpdate;
		}
		$updateFznPknEntryRec =	false;
	}

	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fznPkngRateId  = $p["delId_".$i];

			if ($fznPkngRateId!="") {
				# Del QEL Grade recs
				$delGradeRecs = $frznPkgRatingObj->deleteRateGradeRec($fznPkngRateId);
				# Del Entry Recs
				$delRawDataEntryRecs = $frznPkgRatingObj->delRateRawData($fznPkngRateId);
				# delete Main Rec
				$frznPkngRateRecDel =	$frznPkgRatingObj->deleteFznPkngRateRec($fznPkngRateId);
			}
		}
		if ($frznPkngRateRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelFrznPkRating);
			$sessObj->createSession("nextPage", $url_afterDelFrznPkngListing.$selection);
		} else {
			$errDel	=	$msg_failDelFrznPkRating;
		}

		$frznPkngRateRecDel	=	false;
	}

	if ($addMode || $editMode) {
		#List All Fishes
		$fishMasterRecords	= $fishmasterObj->fetchAllRecordsFishactive();
	
		#List All Freezing Stage Record
		$freezingStageRecords	= $freezingstageObj->fetchAllRecordsActivefreezingstage();
		
		#List All EU Code Records
		$euCodeRecords		= $eucodeObj->fetchAllRecordsActiveEucode();
	
		#List All Brand Records
		//$brandRecords		= $brandObj->fetchAllRecords();

		# get Recs
		if ($selCustomerId) $brandRecs = $brandObj->getBrandRecords($selCustomerId);		
	
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
		
		$processorRecords = $preprocessorObj->getActiveProcessorRecs("FrozenPackRating.php", '');

	}

	#List all Lot Id for a selected date	
	$packingDate	= date("Y-m-d");
	if ($addMode) $frozenLotIdRecords = $frznPkgRatingObj->fetchLotIdRecords($packingDate);
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$fznPkngRateRecords = $frznPkgRatingObj->fetchAllPagingRecords($offset, $limit);
	$fznPkngRateListRecSize	= sizeof($fznPkngRateRecords);
	$fetchAllQEfrznPkgRecs = $frznPkgRatingObj->fetchAllRecords();		

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($fetchAllQEfrznPkgRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($editMode) $heading	= $label_editFrznPkngListing;
	else $heading	= $label_addFrznPkngListing;	
	
	# Setting the mode
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	$sortBtnEnabled = false;
	if ($editMode || $selQuickEntryList) {
		# Check any Grade Inserted entry
		$gGradeRecords	= $frznPkgRatingObj->getSelGradeRecords($userId, $fznPkgQEListId);
		# Default Process Code recs
		$defaultPCWiseGradeRecs = $frznPkgRatingObj->getDefaultGradeRecs($fznPkgQEListId);
		$grDiffSize = $frznPkgRatingObj->getGradeRecDiffSize('', $fznPkgQEListId);	
		if (sizeof($defaultPCWiseGradeRecs)!=sizeof($gGradeRecords) || $grDiffSize!=0) $sortBtnEnabled = true;
		//echo "D=".sizeof($defaultPCWiseGradeRecs)."=>C-G".sizeof($gGradeRecords);
	}
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/FrozenPackRating.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

/*	$rateTag='[{"grade":["48","21"],"rate":45},{"grade":["72","24"],"rate":56},{"grade":["24","25"],"rate":78}]';
	printr(json_decode($rateTag));
	$rates=json_decode($rateTag);
	$rs=count($rates);
	//echo $rs;
	for($i=0; $i<$rs; $i++)
	{
		$gradeTot=$rates[$i];
		//printr($gradeTot);
		foreach ($gradeTot->grade as $grades )
		{
			$gradeIds[]=$grades;
		}
		//printr($gradeIds); 
		//echo $grade[$i];
	}
	*/

	
/*	$grd=["72","24"];
	$rateTag='[{"GradeId":["72","24"],"Rate":34},{"GradeId":["48","21","26","28","27","73","57","30","32"],"Rate":45},{"GradeId":["49","58","50","59","31"],"Rate":45},{"GradeId":["29","84","79","47"],"Rate":78},{"GradeId":["40","22","23","25","20","14","33","18"],"Rate":65}]';
	$rates=json_decode($rateTag);
	$rs=count($rates);
	for($i=0; $i<$rs; $i++)
	 { 
		
		//printr($rates[$i]->GradeId);
		if($rates[$i]->GradeId!=$grd)
		{
          $destArr[$i]=$rates[$i];
		}
		
     }
	 //$destArr = json_encode($destArr);
	printr($destArr);
	
*/


//$dt="Mon Nov 17 2014 05:30:00";	
//echo $new_date = date('Y-m-d H:i:s',  strtotime($dt)); 
	
?>
<script>
function addFrozen()
{
var serializedData = $('#frmFrozenPackRating').serialize();
//alert(serializedData);
$.ajax({
        url: "FrozenPackRating_class.php",
        type: "post",
        data: serializedData
    });
}
</script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

<!--<link rel="stylesheet" href="libjs/jquery-ui.css">-->
<script src="libjs/jquery/jquery-1.10.2.js"></script>
<script src="libjs/jquery/jquery-ui.js"></script>

<form name="frmFrozenPackRating" id="frmFrozenPackRating" action="FrozenPackRating.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%">
	<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?php
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
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackRating.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFznPkngRating(document.frmFrozenPackRating);">												
												</td>
												<?} else{?>
												<td align="center">
													
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackRating.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateFznPkngRating(document.frmFrozenPackRating);">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFznPkngRating(document.frmFrozenPackRating);" value="save &amp; Add New">
													
												</td>
													<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<input type="hidden" name="hidQuickEntryListId" value="<?=$fznPkgQEListId;?>">
											<tr>
												<td nowrap class="fieldName"></td>
											 </tr>
											 <tr>
												<td colspan="2" height="10"></td>
											 </tr>
											 <tr>
												<td colspan="2" style="padding-left:60px;">&nbsp;</td>
											</tr>
											<?php
												 if ($addMode) { 
											?>
											<tr>
												<td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top" align="center">
													<table width="75%" align="center" cellpadding="0" cellspacing="0">
														<TR>
															<TD>
															<!--
																<fieldset>
																	<legend class="listing-item" onMouseover="ShowTip('Copy from existing Frozen Pack Rate and save after editing.');" onMouseout="UnTip();">Copy From</legend>
																		<table>
																			<TR>
																				<TD class="fieldName" onMouseover="ShowTip('Copy from existing Frozen Pack Rate and save after editing.');" onMouseout="UnTip();">Frozen Pack Rate</TD>
																				<td nowrap="true">
																					<select name="selQuickEntryList" id="selQuickEntryList" onchange="quickEntryLoad(this);">
																						<option value="">-- Select --</option>
																						<?php
																						foreach ($fetchAllQEfrznPkgRecs as $fpqel) {
																							$cpfQuickEntryListId 	= $fpqel[0];
																							$cpfFEntryName	 	= $fpqel[1];
																							$selected = ($selQuickEntryList==$cpfQuickEntryListId)?"selected":""	
																						?>
																						<option value="<?=$cpfQuickEntryListId?>" <?=$selected?>><?=$cpfFEntryName?></option>
																						<?php
																							}
																						?>
																					</select>
																				</td>
																			</TR>
																		</table>
																	</fieldset-->
																</TD>
															</TR>
														</table>
													</td>
												</tr>
												<?php
													} // Copy from Ends here
												?>
												<tr>
													<td colspan="2" align="center">
														<table width="75%" align="center" cellpadding="0" cellspacing="0">
															<tr>
																<TD nowrap >
																	<table>
																		<TR>
																			<TD valign="top" nowrap>
																				<fieldset>
																					<table>
																						<tr>
																							<td class="fieldName" nowrap="nowrap">*Name</td>
																							<td nowrap>				 		
																								<input type="text" id="qeName" name="qeName" size="26" value="<?=$qeName?>" autocomplete="off" />
																								<input type="hidden" id="hidQeName" name="hidQeName" size="18" value="<?=$qeName?>" />
																							</td>
																						</tr>
																						<tr>
																							<td class="fieldName" nowrap="nowrap">*Processor:</td>
																							<td>
																								<select name="selProcessor" id="selProcessor">
																									<option value="0">--Select All--</option>
																									<? 
																									foreach($processorRecords as $ppr) {
																										$processorId	= $ppr[0];
																										$processorName	= stripSlash($ppr[1]);
																										$selected = ($selProcessorId==$processorId)?"selected":"";
																									?>
																									<option value="<?=$processorId?>" <?=$selected?>><?=$processorName?></option>
																									<? }?>
																								</select>
																							</td>
																						</tr>
																					</table>
																				</fieldset>	
																			</TD>
																			<td>
																				<fieldset>	
																					<table>
																						<tr>
																							<td class="fieldName" nowrap="nowrap">*Date</td>
																							<td nowrap>				 		
																								<input type="text" id="effectiveDate" name="effectiveDate"  value="<?=$effectiveDate?>" autocomplete="off" />
																							</td>
																						</tr>
																						<tr>
																							<td class="fieldName" nowrap="nowrap">*Rate:</td>
																							<td>
																								<input type="text" id="rate" name="rate" size="15" value="<?=$rate?>" autocomplete="off" />
																							</td>
																						</tr>
																					</table>
																				</fieldset>	
																			</td>
																			<!-- 	First Column Ends Here -->
																		</TR>
																	</table>
																</TD>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<TD align="center" colspan="2" style="padding-left:5px;padding-right:5px;">
														<table width="75%">
															<TR>
																<TD>
																	<fieldset>
																		<table  align="center" cellpadding="0" cellspacing="0" border="0">
																		<!--  Dynamic Row Starts Here style="padding-left:5px;padding-right:5px;"-->
																			<tr id="catRow1">
																				<td style="padding:5 5 5 5px;">
																					<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddRawData">
																						<tr bgcolor="#f2f2f2" align="center">
																							<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Fish</td>
																							<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Process Code</td>
																							<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Freezing Stage</td>
																							<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Quality</td>
																							<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Frozen Code</td>
																							<td>&nbsp;</td>
																							<td>&nbsp;</td>			
																						</tr>	
																						<?php
																							$j=0;
																							$spCodeArr= array();
																							foreach ($getRawDataRecs as $rdr) {			
																								$pkgRateEntryId = $rdr[0];
																								$sFishId    = $rdr[1];	
																								$sProcessCodeId = $rdr[2];
																								$spCodeArr[$j] = $sProcessCodeId;
																								# PC Recs
																								$pcRecords = $processcodeObj->getProcessCodeRecs($sFishId);
																								$rateDetails=$frznPkgRatingObj->getSelGradeRecords($userId, $fznPkgQEListId,$pkgRateEntryId);
																								//printr($rateDetails); 
																								//$totalGrade=""; 
																								$tagRecords=""; $rateTag=""; $tagRec=""; $grade=""; $rateOld="";
																								if(sizeof($rateDetails)>0)
																								{
																									$gradeAll=$rateDetails[0][0];
																									//echo $gradeAll;
																									if($gradeAll=="0")
																									{
																										$rate=$rateDetails[0][3];
																										$rateTag='[{"GradeId":["A"]'.',"Rate":'.$rate.'}]';
																									}
																									else
																									{
																										foreach($rateDetails as $rd)
																										{
																											$rateValue=$rd[3];
																											//echo $rateOld.'=='.$rateValue.'<br/>';
																											if($rateOld=="")
																											{
																												$grade[]='"'.$rd[0].'"';
																												
																											}
																											else if($rateOld==$rateValue)
																											{
																												 $grade[]='"'.$rd[0].'"';
																											}
																											else if(($rateOld!="") && ($rateOld!=$rateValue))
																											{
																												$gradeId=join(",",$grade);
																												$gradeId='['.$gradeId.']';
																												//$totalGrade[]=$gradeId.'--rate'.$rateOld;
																												$totalGrade[]='{"GradeId":'.$gradeId.',"Rate":'.$rateOld.'}';
																												$grade="";
																												$grade[]=$rd[0];
																											}
																											$rateOld=$rateValue;
																										}
																										$gradeId=join(",",$grade);
																										$gradeId='['.$gradeId.']';
																										$totalGrade[]='{"GradeId":'.$gradeId.',"Rate":'.$rateOld.'}';
																									}
																								}
																								if(sizeof($totalGrade)>0)
																								{
																									$tagRec=join(",",$totalGrade);
																									$rateTag='['.$tagRec.']';
																								}
																								//die();
																								//echo $rateTag;
																						?>
																						<tr align="center" class="whiteRow" id="row_<?=$j?>">
																							<td align="center" class="listing-item">
																								<select onchange="xajax_getProcessCodeRecords(document.getElementById('selFish_<?=$j?>').value, '<?=$j?>', '');" id="selFish_<?=$j?>" name="selFish_<?=$j?>">
																								<option value="">-- Select --</option>
																								<?php
																								if (sizeof($fishMasterRecords)>0) {	
																									foreach ($fishMasterRecords as $fr) {
																										$fId		= $fr[0];
																										$fishName	= stripSlash($fr[1]);
																										$selected = ($sFishId==$fId)?"selected":"";
																								?>
																								<option value="<?=$fId?>" <?=$selected?>><?=$fishName?></option>
																								<?php
																										}
																									}
																								?>
																								</select>
																							</td>
																							<td align="center" class="listing-item">
																								<select onchange="chkSortBtnDisplay('<?=$userId?>', '<?=$fznPkgQEListId?>');" id="selProcessCode_<?=$j?>" name="selProcessCode_<?=$j?>">
																								<!--<option value="">-- Select --</option>-->
																								<?php
																								if (sizeof($pcRecords)>0) {	
																									foreach ($pcRecords as $pCodeId=>$pCode) {
																										$selected = ($sProcessCodeId==$pCodeId)?"selected":"";
																								?>
																								<option value="<?=$pCodeId?>" <?=$selected?>><?=$pCode?></option>
																								<?php
																										}
																									}
																								?>
																								</select>
																							</td>
																							<td  align="center" class="listing-item">
																							<a onclick="getGrade(document.getElementById('selProcessCode_<?=$j?>').value,document.getElementById('selFish_<?=$j?>').value,document.getElementById('freezingStage').value,document.getElementById('frozenCode').value,document.getElementById('selQuality').value,'<?=$j?>')" href="javascript:void(<?=$j?>)">Set</a>
																							</td>
																							<td align="center" class="listing-item">
																								<a onclick="setRowItemStatus('<?=$j?>', '<?=$mode?>', '<?=$userId?>', '<?=$fznPkgQEListId?>');" href="###">
																								<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
																								<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
																								<input id="IsFromDB_<?=$j?>" type="hidden" value="N" name="IsFromDB_<?=$j?>">
																								<input id="rateTag_<?=$j?>" type="hidden" value='<?=$rateTag?>' name="rateTag_<?=$j?>">
																								<input id="frozenPackId_<?=$j?>" type="hidden" value="" name="frozenPackId_<?=$j?>">
																							</td>
																						</tr>
																						<?php
																								$j++;
																							}
																						?>
																					</table>
																				</td>
																			</tr>
																			<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$j?>" readonly />
																			<input type="hidden" name="hidTRowCount" id="hidTRowCount" value="<?=sizeof($getRawDataRecs)?>" readonly />
																			<!--  Dynamic Row Ends Here-->
																			<tr id="catRow2"><TD height="5"></TD></tr>
																			<tr id="catRow3">
																				<TD style="padding-left:5px;padding-right:5px;">
																					<a href="###" id='addRow' onclick="javascript:addNewRawData();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																				</TD>
																			</tr>
																			<tr><td height="20"></td></tr>
																			<!--
																			<tr id="arrangeBtnRow">
																				<TD>
																					<table>
																						<TR><TD>
																							<input type="button" value=" Sort & Arrange Grade " name="btnArrangeGrade" class="button" onclick="arrangeGradeRecords('<?=$userId?>', '<?=$fznPkgQEListId?>','<?=$mode?>');" style="width:150px;">
																						</TD></TR>
																					</table>
																				</TD>
																			</tr>
																			-->
																			<tr>
																				<td style="padding:5 5 5 5px;" id="gradeRecs" align="center">
																				</td>
																			</tr>
																		</table>
																	</fieldset>
																</TD>
															</TR>
														</table>
													</TD>
												</tr>
												<tr>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
												</tr>
												<tr>
													<? if($editMode){?>
													<td align="center">
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackRating.php');">&nbsp;&nbsp;
														<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFznPkngRating(document.frmFrozenPackRating);">												
													</td>
													<? } else{?>
													<td align="center">
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackRating.php');">&nbsp;&nbsp;
													<input type="button" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="addFrozen();">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFznPkngRating(document.frmFrozenPackRating);" value="save &amp; Add New">											
													</td>
													<? }?>
												</tr>
												<tr>
													<td  height="10" ></td>
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
										<td background="images/heading_bg.gif" class="pageName" >&nbsp;Frozen Pack Rating </td>
										<td background="images/heading_bg.gif" class="pageName" align="right" ></td>
									</tr>
									<tr>
										<td colspan="3" height="10" ></td>
									</tr>
									<?php
									/*	if ($isAdmin || $reEdit) {
									?>
									<tr>
										<td colspan="3" align="right" style="padding-right:10px;">
											<table width="200" border="0">			
												<tr>
													<TD style="padding-left:10px;padding-right:10px;" align="right">
														<input type="button" name="refreshPCGrade" value="Refresh Grade" class="button" onclick="return updateFullSetGrade('<?=$userId?>');" title="Click here to update all Process code wise grades. " />
													</TD>
												</tr>			
											</table>
										</td> 
									</tr>
									<?php
										}
									*/
									?>
									<tr>	
										<td colspan="3">
											<table cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fznPkngRateListRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFrozenPackRating.php',700,600);"><? }?></td>
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
										<td colspan="2" style="padding-left:10px;padding-right:10px;" align="center">
											<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
											if (sizeof($fznPkngRateRecords)>0) {
												$i	=	0;
											?>
											<? if($maxpage>1){?>
												<tr bgcolor="#FFFFFF">
													<td colspan="7" align="right" style="padding-right:10px;">
														<div align="right">
														<?php
														 $nav  = '';
														for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"FrozenPackRating.php?pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"FrozenPackRating.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"FrozenPackRating.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
													<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
												</td>
												<td class="listing-head" style="padding-left:5px;padding-right:5px;">Name</td>	
												<td class="listing-head" style="padding-left:5px;padding-right:5px;">Process Code</td>
												<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Code</td>
												<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Stage</td>
												<? if($edit==true){?>
													<td class="listing-head" width="45">&nbsp;</td>	
												<? }?>
												</tr>
												<?
												foreach ($fznPkngRateRecords as $fpqel) {
														$i++;
														$frznCodeRate = $fpqel[0];
														$qEntryName	 = $fpqel[1];
														//echo $qEntryName;
														$sFrozenCode = $frozenpackingObj->findFrozenPackingCode($fpqel[3]);
														$sFreezingStage = $freezingstageObj->findFreezingStageCode($fpqel[2]);
														/*
														$sFishName	 = $fishmasterObj->findFishName($fpqel[2]);
														$sProcessCode = $processcodeObj->findProcessCode($fpqel[3]);
														$frozenLotId = "";
														if ($fpqel[3]!=0) $frozenLotId	=	$fpqel[3];
														$eUCode = $eucodeObj->findEUCode($fpqel[8]);
														$brand = $brandObj->findBrandCode($fpqel[9]);
														$mCPackingCode = $mcpackingObj->findMCPackingCode($fpqel[11]);
														$exportLotId	=	$fpqel[12];
														*/
														
														# Get Selected Process Coes
														$getProcessCodeRecs = $frznPkgRatingObj->getProcessCodeRecs($frznCodeRate);

														$rowColor = "WHITE";
														$displayToolTip = "";
														$displayRowStyle = "";
														# ------- checkng grade list correct/not ---------------------
														# Check any Grade Inserted entry
														$selGradeRecords = $frznPkgRatingObj->getSelGradeRecords('', $frznCodeRate);
														# Default Process Code recs
														$selDefaultPCWiseGradeRecs = $frznPkgRatingObj->getDefaultGradeRecs($frznCodeRate);
														$gradeDiffSize = $frznPkgRatingObj->getGradeRecDiffSize('', $frznCodeRate);				
														/*if (sizeof($selGradeRecords)!=sizeof($selDefaultPCWiseGradeRecs) || $gradeDiffSize!=0) {
															$rowColor = "#FFFFCC";
															$displayToolTip = "onMouseover=\"ShowTip('Please sort and arrange the grade.');\" onMouseout=\"UnTip();\"";

															$displayRowStyle = "style=\"background-color: #FFFFCC;\" onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Please sort and arrange the grade.');\" onMouseout=\"this.style.backgroundColor='#FFFFCC';UnTip();\" ";
														} else {
															$displayRowStyle = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
														}
														*/
														# ----------------------
												?>
												<!-- bgcolor="<?//=$rowColor?>" <?//=$displayToolTip?> -->
												<tr <?=$displayRowStyle?> bgcolor="white">
													<td width="20">
														<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$frznCodeRate;?>" class="chkBox">
													</td>
													<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$qEntryName?></td>
													<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFishName;?></td>-->
													<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
														<?php
															$numCol = 6;
															if (sizeof($getProcessCodeRecs)>0) {
																$nextRec = 0;
																$pcName = "";
																foreach ($getProcessCodeRecs as $cR) {
																	$pcName = $cR[1];
																	$nextRec++;
																	if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $pcName;
																	if($nextRec%$numCol == 0) echo "<br/>";
																}
															}						
														?>
													</td>
													<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFrozenCode?></td>
													<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFreezingStage?></td>
													<? if($edit==true){?>
													<td class="listing-item" width="45" align="center" style="padding-left:3px;padding-right:3px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$frznCodeRate;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='FrozenPackRating.php';" <?=$disabled?>></td>	
													<? }?>
												</tr>
												<?php
													}
												?>
												<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
												<input type="hidden" name="editId" value="">	
												<input type="hidden" name="editSelectionChange" value="0">
												<input type="hidden" name="editMode" value="<?=$editMode?>">
												<input type="hidden" name="allocateId" value="<?=$allocateId?>">
												<? if($maxpage>1){?>
												<tr bgcolor="#FFFFFF">
													<td colspan="7" align="right" style="padding-right:10px;">
														<div align="right">
														<?php
														 $nav  = '';
														for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"FrozenPackRating.php?pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"FrozenPackRating.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"FrozenPackRating.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
													} else 	{
												?>
												<tr bgcolor="white">
												<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
									<tr>	
										<td colspan="3">
											<table cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fznPkngRateListRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFrozenPackRating.php',700,600);"><? }?></td>
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
					<input type="hidden" name="hidArrangeGrade" id="hidArrangeGrade" value=""/>
					<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>"/>
					<input type="hidden" name="gradeRecSize" id="gradeRecSize" value="<?=sizeof($gGradeRecords)?>"/>
					<input type="hidden" name="selGradeRecSize" id="selGradeRecSize" value="<?=sizeof($gGradeRecords)?>"/>
					<input type="hidden" name="selGradeRecSizeDiff" id="selGradeRecSizeDiff" value=""/>
				</td>
			</tr>	
		</table>

	<?php 
		if ($addMode || $editMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewRawData()
		{
			addNewRawDataRow('tblAddRawData', '', '', '<?=$mode?>', '', 'N', '<?=$userId?>', '<?=$fznPkgQEListId?>', '');	
		}
	</SCRIPT>
	<?php 
		} 
	?>
	<?
	if(sizeof($getRawDataRecs)>0)
	{
	?>
	<script>
	fieldId = '<?=sizeof($getRawDataRecs)?>';
	</script>
	<?
	}
	?>

	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "effectiveDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "effectiveDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>





	<?php 
	/*	if ($editMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<?php
			if (!$sortBtnEnabled) {
		?>
			// Hide Arrange Btn
			document.getElementById("arrangeBtnRow").style.display="none";
		<?php
			} else {
		?>
			document.getElementById("arrangeBtnRow").style.display="";
		<?php
			}
		?>
		function addNewRawData()
		{
			addNewRawDataRow('tblAddRawData', '', '', '<?=$mode?>', '', 'N', '<?=$userId?>', '<?=$fznPkgQEListId?>', '');	
			displaySortBtn();
		}
	</SCRIPT>
	<?php 
		} 
		*/
	?>

	<?php

		if ($addMode && !sizeof($getRawDataRecs)) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewRawData();
	</SCRIPT>
	<?php 
		}
		
	?>	
	<!-- Edit Record -->
	<script type="text/javascript" language="JavaScript">
	<?php
	/*	if (sizeof($getRawDataRecs)>0) {
			//$j=0;
			//$spCodeArr= array();
			foreach ($getRawDataRecss as $rdr) {			
				$qelEntryId = $rdr[0];
				$sFishId    = $rdr[1];	
				$sProcessCodeId = $rdr[2];
				$spCodeArr[$j] = $sProcessCodeId;
	?>	
		//addNewRawDataRow('tblAddRawData','<?=$sFishId?>', '<?=$qelEntryId?>', '<?=$mode?>', '<?=$sProcessCodeId?>', 'Y', '<?=$userId?>', '<?=$fznPkgQEListId?>', '<?=$selQuickEntryList?>');
	<?php
			//$j++;
		} // Get Raw Data Recs
	?>
		// Get Grade Recs
	<?php
	$impldeSpc = implode(",",$spCodeArr);
	if (!$selQuickEntryList)  {
	?>
	 	xajax_getGradeRecsForArrange('<?=$userId?>','<?=$fznPkgQEListId?>', '<?=$impldeSpc?>');	// Edit Section
	<?php } else {?>
		xajax_getGradeRecsForArrange('<?=$userId?>','', '<?=$impldeSpc?>'); // Copy from and modify	
	<?php }?>
	// Set Item Row Size
	fieldId = '<?=sizeof($getRawDataRecs)?>';
	<?php
		} // Raw Data Size Check Ends
		*/
	?>
	</script>
	<?php
		if (($addMode || $editMode) && sizeof($spCodeArr)) {
			$impldeSpc = implode(",",$spCodeArr);
	?>
	<script language="JavaScript" type="text/javascript">
		chkSelPcsGradeSize('<?=$impldeSpc?>', '<?=$userId?>', '<?=$fznPkgQEListId?>');
	</script>
	<?php
		 }
	?>	
</form>
<div id="dialog" title="Frozen Pack Rate" style="display:none" >
		<!--<p>
		This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.
			
		</p>-->
	</div>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>
