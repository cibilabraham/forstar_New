<?php
	require("include/include.php");
	require_once("lib/FrozenPackingQuickEntryList_ajax.php");
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


	//$frznPkngQuickEntryListObj->insertTempPCRecs("10,11", '');
	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
		# Delete Blank Recs
		if (!$selQuickEntryList) $delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, '');
	}

	#Cancel 	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;
		$editMode	=	false;
		# Delete Blank Recs
		$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, '');
		$selQuickEntryList ="";
		$fznPkgQEListId = "";
		$editId = "";
		$p["editId"] = "";
		$p["selQuickEntryList"] = "";
	}
	

	# Add
	if ($p["cmdAdd"]!="" || $p["cmdSaveAndAddNew"]!="") {	
		$qeName		= addSlash(trim($p["qeName"]));
		$freezingStage	= $p["freezingStage"];
		$selQuality 	= $p["selQuality"];
		$eUCode		= $p["eUCode"];
		//$brand		= $p["brand"];
		$sBrand		= explode("_",$p["brand"]);
		$brand		= $sBrand[0];
		$brandFrom	= $sBrand[1];
		
		$selCustomerId  = $p["customer"];
		$frozenCode	= $p["frozenCode"];
		$mCPacking	= $p["mCPacking"];
		$frozenLotId	= ($p["frozenLotId"]=="")?0:$p["frozenLotId"];
		$exportLotId	= $p["exportLotId"];
		$codeType=$p["codeType"];
		
		$tableRowCount		= $p["hidTableRowCount"];
		$tableRowCount2		= $p["hidTableRowCount2"];
		$gradeRowCount		= $p["hidGradeRowCount"];
		
		if ($frozenCode!="" && $qeName!="") {
			$fznPkngQuickEntryListRecIns =	$frznPkngQuickEntryListObj->addFznPkngQuickEntryList($qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $userId, $brandFrom,$codeType);
			#Find the Last inserted Id 
			if ($fznPkngQuickEntryListRecIns) $qelId = $databaseConnect->getLastInsertedId();
			if($codeType=="S")
			{
				if ($tableRowCount>0) {
					for ($i=0; $i<$tableRowCount; $i++) {
						$status = $p["status_".$i];
						if ($status!='N') {
							$selFish 	= $p["selFish_".$i];
							$selProcessCode = $p["selProcessCode_".$i];
							if ($selFish!="" && $selProcessCode!="" && $qelId) {
								$frznPkngQELEntryRecIns = $frznPkngQuickEntryListObj->addFznPkgRawEntry($qelId, $selFish, $selProcessCode,$codeType);
							}
						}
					}
				} // Row Count Loop Ends Here
			}
			else if($codeType=="C")
			{
				$l=0;
				if ($tableRowCount2>0) {
					for ($i=0; $i<$tableRowCount2; $i++) {
						$sstatus = $p["sstatus_".$i];
						if ($sstatus!='N') {
							$l++;
							$selFish 	= $frznPkngQuickEntryListObj->getFishId();
							$selSecondaryProcessCode = $p["selSecondaryProcessCode_".$i];
							$selGrade=$p["selGrade_".$i];
							if ($selSecondaryProcessCode!="" && $qelId) {
								$frznPkngQELEntryRecIns = $frznPkngQuickEntryListObj->addFznPkgRawEntry($qelId, $selFish, $selSecondaryProcessCode,$codeType);
								if ($frznPkngQELEntryRecIns) $sublId = $databaseConnect->getLastInsertedId();
								$insGradeRec = $frznPkngQuickEntryListObj->addSelGradeSubRec($qelId, $selGrade,$l, $userId, "Y",$codeType,$sublId);
							}
						}
					}
				} // Row Count Loop Ends Here
			}

			if($codeType=="S")
			{
				if ($gradeRowCount>0) {
					# Del Temp Rec
					$delTempGradeRec 	= $frznPkngQuickEntryListObj->delTempGradeRec($userId);
					for ($g=1;$g<$gradeRowCount;$g++) {
						$gradeId = $p["gradeId_".$g];
						$displayOrderId = $p["displayOrderId_".$g];
						$gradeStatus   = ($p["gradeStatus_".$g]!="")?$p["gradeStatus_".$g]:'N';
						if ($qelId) {
							$insGradeRec = $frznPkngQuickEntryListObj->addSelGradeRec($qelId, $gradeId, $displayOrderId, $userId, $gradeStatus,$codeType);
						}
					}
				}
			}
						
			if ($fznPkngQuickEntryListRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddFrznPkngQuickEntryList);
				
				if ($p["cmdAdd"]!="") {				
					$addMode = false;
					$sessObj->createSession("nextPage",$url_afterAddFrznPkngQuickEntryList.$selection);
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
				$err			=	$msg_failAddFrznPkngQuickEntryList;
			}
			$fznPkngQuickEntryListRecIns	=	false;
		}
	}
	
	# Edit Packing
	if ($p["editId"]!="" || $selQuickEntryList!="") {		
		# Delete Blank Recs
		if (!$selQuickEntryList) $delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, '');

		if ($selQuickEntryList) $editId = $selQuickEntryList;
		else $editId	= $p["editId"];		

		if (!$selQuickEntryList) $editMode = true;
		# Find Selected Rec
		$fznPkQuickEntryListRec	= $frznPkngQuickEntryListObj->find($editId);
		
		if (!$selQuickEntryList) $fznPkgQEListId 	= $fznPkQuickEntryListRec[0];
		$qeName			= $fznPkQuickEntryListRec[1];			
		$freezingStage		= $fznPkQuickEntryListRec[2];
		$eUCode			= $fznPkQuickEntryListRec[3];
		//$brand			= $fznPkQuickEntryListRec[4];
		$selBrd		  = $fznPkQuickEntryListRec[4];
		$selBrdFrom	  = $fznPkQuickEntryListRec[11]; // Company
		$brand	  = $selBrd."_".$selBrdFrom;
		$selProcesscodeType	  = $fznPkQuickEntryListRec[12]; // Company
		//echo $selProcesscodeType;
		if($selProcesscodeType=="S")
		{
			$selProcesscodeTypeS="Selected";
			$selProcesscodeTypeC="";
		}
		if($selProcesscodeType=="C")
		{
			$selProcesscodeTypeC="Selected";
			$selProcesscodeTypeS="";
		}
		$frozenCode		= $fznPkQuickEntryListRec[5];
		$mCPacking		= $fznPkQuickEntryListRec[6];
		if ($fznPkQuickEntryListRec[9]!=0) $frozenLotId = $fznPkQuickEntryListRec[7];
		$exportLotId		= $fznPkQuickEntryListRec[8];
		$selQuality		= $fznPkQuickEntryListRec[9];
		$selCustomerId		= $fznPkQuickEntryListRec[10];		
		
		#FrozenLot Id records for the selected date
		$sDate = date("Y-m-d");
		$frozenLotIdRecords = $frznPkngQuickEntryListObj->fetchLotIdRecords($sDate);

		# Get Entry Recs
		if (!$selQuickEntryList) $getRawDataRecs = $frznPkngQuickEntryListObj->getQELRawRecords($fznPkgQEListId);
		else { // Copy from section
			# Delete Blank Recs
			$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, '');
			# Get Selected Grade Recs
			$getRawDataRecs = $frznPkngQuickEntryListObj->getQELRawRecords($selQuickEntryList);
			$fznPkgQEListId = 0;
			# Get Grade Recs from DB	
			$getGdRecords	= $frznPkngQuickEntryListObj->getSelGradeRecords('', $selQuickEntryList);
			foreach ($getGdRecords as $gdr) {
				$iGradeId = $gdr[0];
				$iDisOrderId = $gdr[3];
				$iGradeStatus = $gdr[5];
				# Add Temp Rec
				$frznPkngQuickEntryListObj->addGradeRec('0', $iGradeId, $iDisOrderId, $userId, $iGradeStatus);
			}
		}		
	}

	

	# update a rec
	if ($p["cmdSaveChange"]!="") {
		
		$fznPkngQuickEntryListId = $p["hidQuickEntryListId"];
		
		$qeName		= addSlash(trim($p["qeName"]));
		$freezingStage	= $p["freezingStage"];
		$selQuality 	= $p["selQuality"];
		$eUCode		= $p["eUCode"];
		//$brand		= $p["brand"];
		$sBrand		= explode("_",$p["brand"]);
		$brand		= $sBrand[0];
		$brandFrom	= $sBrand[1];

		$selCustomerId  = $p["customer"];
		$frozenCode	= $p["frozenCode"];
		$mCPacking	= $p["mCPacking"];
		$frozenLotId	= ($p["frozenLotId"]=="")?0:$p["frozenLotId"];
		$exportLotId	= $p["exportLotId"];
		$codeType=$p["codeType"];
		
		$tableRowCount		= $p["hidTableRowCount"];
		$tableRowCount2		= $p["hidTableRowCount2"];
		$gradeRowCount		= $p["hidGradeRowCount"];
		
		if ($fznPkngQuickEntryListId!="" && $frozenCode!="" && $qeName!="") {
			$updateFznPkngWuickEntryRec =	$frznPkngQuickEntryListObj->updateFznPkngEntryRec($fznPkngQuickEntryListId, $qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $brandFrom,$codeType);

			# Del Entry Recs
			$delRawDataEntryRecs = $frznPkngQuickEntryListObj->delQELRawData($fznPkngQuickEntryListId);
			if($codeType=="S")
			{
				if ($tableRowCount>0) {
					for ($i=0; $i<$tableRowCount; $i++) {
						$status = $p["status_".$i];
						if ($status!='N') {
							$selFish 	= $p["selFish_".$i];
							$selProcessCode = $p["selProcessCode_".$i];
							if ($selFish!="" && $selProcessCode!="" && $fznPkngQuickEntryListId!="") {
								$frznPkngQELEntryRecIns = $frznPkngQuickEntryListObj->addFznPkgRawEntry($fznPkngQuickEntryListId, $selFish, $selProcessCode,$codeType);
							}
						}
					}
				} // Row Count Loop Ends Here	
			}
			else if($codeType=="C")
			{
				$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, $fznPkngQuickEntryListId);
				$l=0;
				if ($tableRowCount2>0) {
					for ($i=0; $i<$tableRowCount2; $i++) {
						$sstatus = $p["sstatus_".$i];
						if ($sstatus!='N') {
							$l++;
							$selFish 	= $frznPkngQuickEntryListObj->getFishId();
							$selSecondaryProcessCode = $p["selSecondaryProcessCode_".$i];
							$selGrade=$p["selGrade_".$i];
							//echo $selFish ."---".$selSecondaryProcessCode.'--'.$selGrade;
							//die();
							if ($selSecondaryProcessCode!="") {
								$frznPkngQELEntryRecIns = $frznPkngQuickEntryListObj->addFznPkgRawEntry($fznPkngQuickEntryListId, $selFish, $selSecondaryProcessCode,$codeType);
								//die();
								if ($frznPkngQELEntryRecIns) $sublId = $databaseConnect->getLastInsertedId();
								$insGradeRec = $frznPkngQuickEntryListObj->addSelGradeSubRec($fznPkngQuickEntryListId, $selGrade,$l, $userId, "Y",$codeType,$sublId);
							}
						}
					}
				} // Row Count Loop Ends Here
			}
	//die();
	
			if($codeType=="S")
			{
				if ($gradeRowCount>0) {
					# Del Temp Rec
					$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, $fznPkngQuickEntryListId);
					for ($g=1;$g<$gradeRowCount;$g++) {
						$gradeId = $p["gradeId_".$g];
						$displayOrderId = $p["displayOrderId_".$g];
						$gradeStatus   = ($p["gradeStatus_".$g]!="")?$p["gradeStatus_".$g]:'N';
						if ($fznPkngQuickEntryListId) {
							$insGradeRec = $frznPkngQuickEntryListObj->addSelGradeRec($fznPkngQuickEntryListId, $gradeId, $displayOrderId, $userId, $gradeStatus,$codeType);
						}
					}
				} // Grade ceck ends Here		
			}
		}
	
		if ($updateFznPkngWuickEntryRec) {
			$sessObj->createSession("displayMsg", $msg_succUpdateFrznPkngQuickEntryList);
			$sessObj->createSession("nextPage", $url_afterUpdateFrznPkngQuickEntryList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFrznPkngQuickEntryList;
		}
		$dailyFrozenPackingRecUptd	=	false;
	}

	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fznPkngQuickEntryListId  = $p["delId_".$i];

			if ($fznPkngQuickEntryListId!="") {
				# Del QEL Grade recs
				$delQELGradeRecs = $frznPkngQuickEntryListObj->deleteQELGradeRec($fznPkngQuickEntryListId);
				# Del Entry Recs
				$delRawDataEntryRecs = $frznPkngQuickEntryListObj->delQELRawData($fznPkngQuickEntryListId);
				# delete Main Rec
				$frznPkngQuickEntryRecDel =	$frznPkngQuickEntryListObj->deleteFznPkngQuickEntryRec($fznPkngQuickEntryListId);
			}
		}
		if ($frznPkngQuickEntryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelFrznPkngQuickEntryList);
			$sessObj->createSession("nextPage", $url_afterDelFrznPkngQuickEntryList.$selection);
		} else {
			$errDel	=	$msg_failDelFrznPkngQuickEntryList;
		}

		$frznPkngQuickEntryRecDel	=	false;
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

		$secondaryProcessCode=$secondaryProcessCodeObj->getSecondaryProcessCodeActive();
	}

	#List all Lot Id for a selected date	
	$packingDate	= date("Y-m-d");
	if ($addMode) $frozenLotIdRecords = $frznPkngQuickEntryListObj->fetchLotIdRecords($packingDate);
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$fznPkngQuickEntryListRecords = $frznPkngQuickEntryListObj->fetchAllPagingRecords($offset, $limit);
	$fznPkngQuickEntryListRecSize	= sizeof($fznPkngQuickEntryListRecords);

	$fetchAllQEfrznPkgRecs = $frznPkngQuickEntryListObj->fetchAllRecords();		

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($fetchAllQEfrznPkgRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($editMode) $heading	= $label_editFrznPkngQuickEntryList;
	else $heading	= $label_addFrznPkngQuickEntryList;	
	
	# Setting the mode
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	$sortBtnEnabled = false;
	if ($editMode || $selQuickEntryList) {
		# Check any Grade Inserted entry
		$gGradeRecords	= $frznPkngQuickEntryListObj->getSelGradeRecords($userId, $fznPkgQEListId);
		# Default Process Code recs
		$defaultPCWiseGradeRecs = $frznPkngQuickEntryListObj->getDefaultGradeRecs($fznPkgQEListId);
		$grDiffSize = $frznPkngQuickEntryListObj->getGradeRecDiffSize('', $fznPkgQEListId);	
		if (sizeof($defaultPCWiseGradeRecs)!=sizeof($gGradeRecords) || $grDiffSize!=0) $sortBtnEnabled = true;
		//echo "D=".sizeof($defaultPCWiseGradeRecs)."=>C-G".sizeof($gGradeRecords);
	}
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/FrozenPackingQuickEntryList.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmFrozenPackingQuickEntryList" id="frmFrozenPackingQuickEntryList" action="FrozenPackingQuickEntryList.php" method="post">
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
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingQuickEntryList.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFznPkngQuickEntryList(document.frmFrozenPackingQuickEntryList);">												
												</td>
												<?} else{?>
												<td align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingQuickEntryList.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateFznPkngQuickEntryList(document.frmFrozenPackingQuickEntryList);">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFznPkngQuickEntryList(document.frmFrozenPackingQuickEntryList);" value="save &amp; Add New">										
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
											if ($addMode) 
											{ 
											?>
											<tr>
												<td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top" align="center">
													<table width="75%" align="center" cellpadding="0" cellspacing="0">
														<TR>
															<TD>
																<fieldset>
																	<legend class="listing-item" onMouseover="ShowTip('Copy from existing Quick Entry List and save after editing.');" onMouseout="UnTip();">Copy From</legend>
																	<table>
																		<TR>
																			<TD class="fieldName" onMouseover="ShowTip('Copy from existing Quick Entry List and save after editing.');" onMouseout="UnTip();">Quick Entry List</TD>
																			<td nowrap="true">
																				<!--<select name="selQuickEntryList" id="selQuickEntryList" onchange="this.form.submit();">-->
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
																</fieldset>
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
															<TD nowrap>
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
																						<td class="fieldName" nowrap="nowrap">Freezing Stage</td>
																						<td nowrap>
																							<select name="freezingStage" id="freezingStage">
																								<option value="0">--Select--</option>
																								<?
																								foreach($freezingStageRecords as $fsr)
																								{
																									$freezingStageId	= $fsr[0];
																									$freezingStageCode	= stripSlash($fsr[1]);
																									$selected		= "";
																									if ($freezingStage==$freezingStageId)  $selected = " selected ";
																								?>
																								<option value="<?=$freezingStageId?>" <?=$selected?>><?=$freezingStageCode?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<TD class="fieldName" nowrap>Quality</TD>
																						<td nowrap>		
																							<select name="selQuality" id="selQuality">
																								<option value="0">-- Select --</option>
																								<?
																								foreach ($qualityMasterRecords as $fr) {
																									$qualityId	= $fr[0];
																									$qualityName	= stripSlash($fr[1]);
																									$selected = "";
																									if($selQuality==$qualityId)  $selected = " selected ";
																								?>
																								<option value="<?=$qualityId?>" <?=$selected?>><?=$qualityName?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">EU Code</td>
																						<td nowrap>
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
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Buyer</td>
																						<td nowrap>	  
																							<select name="customer" id="customer" onchange="xajax_getBrandRecs(document.getElementById('customer').value, '');">
																								<option value="">-- select --</option>
																								<?php
																									foreach ($customerRecords as $cr) {
																										$customerId	=	$cr[0];
																										$customerName	=	stripSlash($cr[2]);
																										$selected 	=	"";
																										if($customerId==$customer || $selCustomerId==$customerId) $selected = "Selected";
																								?>
																								<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																				</table>
																			</fieldset>	
																		</TD>
																		<!-- 	First Column Ends Here -->
																		<TD nowrap>&nbsp;</TD>
																		<TD valign="top" nowrap>
																			<fieldset>
																				<table>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Brand</td>
																						<td nowrap>						
																						<select name="brand" id="brand">
																							<?php
																							if (sizeof($brandRecs)<=0) {
																							?>
																							 <option value="0">-- Select --</option>
																							<?php
																							}
																							?>
																							<?php
																							foreach($brandRecs as $brandId=>$brandName) {
																								$selected	= ($brand==$brandId)?"selected":"";
																							?>
																							<option value="<?=$brandId?>" <?=$selected?>><?=$brandName?></option>
																						   <? }?>
																						</select>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap="nowrap">*Frozen Code</td>
																					<td nowrap="nowrap">	  
																						<select name="frozenCode" id="frozenCode" <?=$disabled?>>
																							<option value="">-- Select --</option>
																							<?php
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
																				<tr>
																					<td class="fieldName" nowrap="nowrap">MC Pkg</td>
																					<td nowrap="nowrap">
																						<select name="mCPacking" id="mCPacking">
																							<option value="0">-- Select --</option>
																							 <?
																							  foreach($mcpackingRecords as $mcp) {
																								$mcpackingId		=	$mcp[0];
																								$mcpackingCode	=	stripSlash($mcp[1]);
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
																				<tr>	
																					<td class="fieldName" nowrap="nowrap">Frozen Lot Id </td>
																					<td nowrap>
																						<select name="frozenLotId" id="frozenLotId">
																							<option value=""> -- Select --</option>
																							<?
																							$k=0;
																							foreach($frozenLotIdRecords as $flr)
																							{
																								$k++;
																								$dailyActivityChartEntryId = $flr[1];
																								$freezer	=	$flr[2];
																								$displayLotId	=	$k."-".$freezer;
																								$selected	= "";
																								if ($dailyActivityChartEntryId==$frozenLotId) $selected = "Selected";
																							?>
																							<option value="<?=$dailyActivityChartEntryId?>" <?=$selected?>><?=$displayLotId?></option>
																							<? }?>
																						</select>
																					</td>
																				</tr>		
																				<tr>
																					<td class="fieldName" nowrap="nowrap">Export &nbsp;Lot ID</td>
																					<td nowrap="nowrap" class="listing-item">
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
																				<tr>
																					<td class="fieldName" nowrap="nowrap">*Process Code Type</td>
																					<td  nowrap="nowrap" class="listing-item">
																						<select name="codeType" id="codeType" onchange="displaytbl();">
																							<option value="">--Select--</option>
																							<option value="S" <?=$selProcesscodeTypeS?>>Single</option>
																							<option value="C" <?=$selProcesscodeTypeC?>>Combo</option>
																						</select>
																					</td >
																				</tr>
																			</table>
																		</fieldset>	
																	</TD>
																</TR>
															</table>
														</TD>
													</tr>
												</table>
											</td>
										</tr>
										<tr id="secondary" style="display:none;" align="center">
											<td  align="center"  colspan="2" style="padding-left:5px;padding-right:5px;">
												<table>
													<tr>
														<td>
															<fieldset>
																<table>
																	<tr>
																		<td>
																			<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddRawData2">
																				<tr bgcolor="#f2f2f2" align="center">
																					<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Secondary Process Code</td>
																					<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Grade</td>
																					<td>&nbsp;</td>			
																				</tr>
																				<?php
																				$i=0;
																				$spCodeArr= array();
																				foreach ($getRawDataRecs as $rdr) {			
																					$qelEntryId = $rdr[0];
																					$sFishId    = $rdr[1];	
																					$sProcessCodeId = $rdr[2];
																					$spCodeArr[$j] = $sProcessCodeId;
																					# PC Recs
																					$secondaryGrade	= $frznPkngQuickEntryListObj->getSecondaryGrade($sProcessCodeId);
																					$sGradeId= $frznPkngQuickEntryListObj->getGradeRec($qelEntryId);
																					//echo $sGradeId;
																					//printr($secondaryGrade);
																				?>
																				<tr id="srow_<?=$i?>" class="whiteRow" align="center">
																					<td class="listing-item" align="center">
																						<select id="selSecondaryProcessCode_<?=$i?>" onchange="xajax_getSecondaryGrade(this.value,<?=$i?>);" name="selSecondaryProcessCode_<?=$i?>">
																							<option value="">-- Select --</option>
																							<?php
																							if (sizeof($secondaryProcessCode)>0)
																							{	
																								foreach ($secondaryProcessCode as $sPC) 
																								{
																									$secondaryPCId		= $sPC[0];
																									$secondaryName	= stripSlash($sPC[1]);
																									($sProcessCodeId== $secondaryPCId)?$sel = "Selected":$sel = "";
																								?>	
																								<option value="<?=$secondaryPCId?>" <?=$sel?>><?=$secondaryName?></option>
																								<?
																								}
																							}
																							?>
																						</select>
																					</td>
																					<td class="listing-item" align="center">
																						<select id="selGrade_<?=$i?>" name="selGrade_<?=$i?>">
																							<option value="">-- Select --</option>
																							<? if(sizeof($secondaryGrade)>0)
																							{
																								foreach($secondaryGrade as $sg=>$sGrade)
																								{
																									$secondaryGradeId		=$sg;
																									$secondaryGradeName	= stripSlash($sGrade);
																									($sGradeId== $sg)?$sels = "Selected":$sels = "";
																								?>	
																								<option value="<?=$secondaryGradeId?>" <?=$sels?>><?=$secondaryGradeName?></option>
																								<?
																								}
																							}
																							?>
																						</select>
																					</td>
																					<td class="listing-item" align="center">
																						<a onclick="setRowItemStatus2('<?=$i?>', '1', '1', '');" href="###">
																						<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
																						</a>
																						<input id="sstatus_<?=$i?>" type="hidden" value="" name="sstatus_<?=$i?>">
																						<input id="IsFromDB_<?=$i?>" type="hidden" value="N" name="IsFromDB_<?=$i?>">
																					</td>
																				</tr>
																				<?
																				$i++;
																				}
																				?>
																			</table>
																		</td>
																	</tr>
																	
																	<input type="hidden" name="hidTableRowCount2" id="hidTableRowCount2" value="<?=$i?>" readonly />
																	<!--  Dynamic Row Ends Here-->
																	<tr id="catRow2"><TD height="5"></TD></tr>
																	<tr id="catRow3">
																		<TD style="padding-left:5px;padding-right:5px;">
																			<a href="###" id='addRow' onclick="javascript:addNewRawData2();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																		</TD>
																	</tr>
																</table>
															</fieldset>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr style="display:none;" id="primary" align="center">
											<TD align="center" colspan="2" style="padding-left:5px;padding-right:5px;" >
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
																					<td>&nbsp;</td>			
																				</tr>	
																				<?php
																				$j=0;
																				$spCodeArr= array();
																				foreach ($getRawDataRecs as $rdr) {			
																					$qelEntryId = $rdr[0];
																					$sFishId    = $rdr[1];	
																					$sProcessCodeId = $rdr[2];
																					$spCodeArr[$j] = $sProcessCodeId;
																					# PC Recs
																					$pcRecords = $processcodeObj->getProcessCodeRecs($sFishId);
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
																					<td align="center" class="listing-item">
																						<a onclick="setRowItemStatus('<?=$j?>', '<?=$mode?>', '<?=$userId?>', '<?=$fznPkgQEListId?>');" href="###">
																						<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
																						<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
																						<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>"/>
																						<input type="hidden" value="<?=$qelEntryId?>" id="qelEntryId_<?=$j?>" name="qelEntryId_<?=$j?>"/>
																						<input type="hidden" value="Y" id="pcFromDB_<?=$j?>" name="pcFromDB_<?=$j?>"/>
																						<input type="hidden" value="<?=$sFishId?>" id="hidFishId_<?=$j?>" name="hidFishId_<?=$j?>"/>
																						<input type="hidden" value="<?=$sProcessCodeId?>" id="hidProcessCodeId_<?=$j?>" name="hidProcessCodeId_<?=$j?>"/>
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
																	<tr id="arrangeBtnRow">
																		<TD>
																			<table>
																				<TR>
																					<TD>
																						<input type="button" value=" Sort & Arrange Grade " name="btnArrangeGrade" class="button" onclick="arrangeGradeRecords('<?=$userId?>', '<?=$fznPkgQEListId?>','<?=$mode?>');" style="width:150px;">
																					</TD>
																				</TR>
																			</table>
																		</TD>
																	</tr>
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
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingQuickEntryList.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFznPkngQuickEntryList(document.frmFrozenPackingQuickEntryList);">												
												</td>
												<? } else{?>
												<td align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingQuickEntryList.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateFznPkngQuickEntryList(document.frmFrozenPackingQuickEntryList);">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFznPkngQuickEntryList(document.frmFrozenPackingQuickEntryList);" value="save &amp; Add New">												
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;QUICK ENTRY LIST </td>
								    <td background="images/heading_bg.gif" class="pageName" align="right" ></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<?php
									if ($isAdmin || $reEdit) {
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
								?>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fznPkngQuickEntryListRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFrozenPackingQuickEntryList.php',700,600);"><? }?></td>
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
										if (sizeof($fznPkngQuickEntryListRecords)>0) {
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
																$nav.= " <a href=\"FrozenPackingQuickEntryList.php?pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"FrozenPackingQuickEntryList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"FrozenPackingQuickEntryList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap>MC Pkg</td>	
											<? if($edit==true){?>
											<td class="listing-head" width="45">&nbsp;</td>	
											<? }?>
										</tr>
										<?
										foreach ($fznPkngQuickEntryListRecords as $fpqel) {
											$i++;
											$fznPkngQuickEntryListId = $fpqel[0];
											$qEntryName	 = $fpqel[1];
											//echo $qEntryName;
											$sFrozenCode = $frozenpackingObj->findFrozenPackingCode($fpqel[5]);
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
											$mCPackingCode = $mcpackingObj->findMCPackingCode($fpqel[6]);
											# Get Selected Process Coes
											$getProcessCodeRecs = $frznPkngQuickEntryListObj->getProcessCodeRecs($fznPkngQuickEntryListId);

											$rowColor = "WHITE";
											$displayToolTip = "";
											$displayRowStyle = "";
											# ------- checkng grade list correct/not ---------------------
											# Check any Grade Inserted entry
											$selGradeRecords = $frznPkngQuickEntryListObj->getSelGradeRecords('', $fznPkngQuickEntryListId);
											# Default Process Code recs
											$selDefaultPCWiseGradeRecs = $frznPkngQuickEntryListObj->getDefaultGradeRecs($fznPkngQuickEntryListId);
											$gradeDiffSize = $frznPkngQuickEntryListObj->getGradeRecDiffSize('', $fznPkngQuickEntryListId);				
											if (sizeof($selGradeRecords)!=sizeof($selDefaultPCWiseGradeRecs) || $gradeDiffSize!=0) {
												$rowColor = "#FFFFCC";
												$displayToolTip = "onMouseover=\"ShowTip('Please sort and arrange the grade.');\" onMouseout=\"UnTip();\"";

												$displayRowStyle = "style=\"background-color: #FFFFCC;\" onMouseover=\"this.style.backgroundColor='#fde89f';ShowTip('Please sort and arrange the grade.');\" onMouseout=\"this.style.backgroundColor='#FFFFCC';UnTip();\" ";
											} else {
												$displayRowStyle = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
											}
											# ----------------------
										?>
										<!-- bgcolor="<?//=$rowColor?>" <?//=$displayToolTip?> -->
										<tr <?=$displayRowStyle?>>
											<td width="20">
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$fznPkngQuickEntryListId;?>" class="chkBox">
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
											<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=$mCPackingCode?></td>	
											<? if($edit==true){?>
											<td class="listing-item" width="45" align="center" style="padding-left:3px;padding-right:3px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$fznPkngQuickEntryListId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='FrozenPackingQuickEntryList.php';" <?=$disabled?>></td>	
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
															$nav.= " <a href=\"FrozenPackingQuickEntryList.php?pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"FrozenPackingQuickEntryList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"FrozenPackingQuickEntryList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fznPkngQuickEntryListRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFrozenPackingQuickEntryList.php',700,600);"><? }?></td>
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
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewRawData()
		{
			addNewRawDataRow('tblAddRawData', '', '', '<?=$mode?>', '', 'N', '<?=$userId?>', '<?=$fznPkgQEListId?>', '');	
		}

		function addNewRawData2()
		{
			addNewRawDataRow2('tblAddRawData2', '', '', '<?=$mode?>', '', 'N', '<?=$userId?>', '<?=$fznPkgQEListId?>', '');	
		}
	</SCRIPT>
	<?php 
		} 
	?>
	<?php 
		if ($editMode) {
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

		function addNewRawData2()
		{
			addNewRawDataRow2('tblAddRawData2', '', '', '<?=$mode?>', '', 'N', '<?=$userId?>', '<?=$fznPkgQEListId?>', '');	
		}
	</SCRIPT>
	<?php 
		} 
	?>

	<?php
		if ($addMode && !sizeof($getRawDataRecs)) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewRawData();
		window.load = addNewRawData2();
	</SCRIPT>
	<?php 
		}
	?>	
	<!-- Edit Record -->
	<script type="text/javascript" language="JavaScript">
	<?php
		if (sizeof($getRawDataRecs)>0) {
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
<!--$selProcesscodeType=="S"-->
	<?
	if($selProcesscodeType!="")
	{
		?>
		<script>
		displaytbl();
		</script>
	<?
	}
	if($selProcesscodeType=="C")
	{
		//echo sizeof($getRawDataRecs);
		?>
		<script>
		fdId="<?=sizeof(getRawDataRecs)?>";
		</script>
	<?
	}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>