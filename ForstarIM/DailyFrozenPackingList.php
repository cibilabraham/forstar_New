<?
	
	require_once("include/include.php");
	require_once("lib/dailyfrozenpackinglist_ajax.php");
	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$isSearched		=	false;
	
	$selection = "?frozenPackingFrom=".$p["frozenPackingFrom"]."&frozenPackingTill=".$p["frozenPackingTill"];
	
//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
//----------------------------------------------------------	

# MC Packing Conversion type, AC - Auto convert/ MC - Manually Convert
	$LSToMCConversionType = $manageconfirmObj->getLS2MCConversionType();
	//----------------------------------------------------------	
	
		
	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;		
		if ($p["mainId"]=="" && $dailyfrozenpackinglistObj->checkBlankRecord()) {
			
			$mainId		=	$dailyfrozenpackinglistObj->checkBlankRecord();
					
		} else {
			if ($p["mainId"]=="") {
				$tempMainTableRecIns=$dailyfrozenpackinglistObj->addTempDataMainTable();
				if ($tempMainTableRecIns!="") {				
					$mainId	=	$databaseConnect->getLastInsertedId();			
				}
				
			} else {
				$mainId 	=	$p["mainId"];
			}
		}
		#delete Old Grade records of Main id
		if ($dailyfrozenpackinglistObj->checkBlankRecord() && $p["mainId"]=="") {
			$dailyFrozenPackingGradeRecDel = $dailyfrozenpackinglistObj-> deleteFrozenRePackingGradeRec($mainId);
		}		
	}
	


	

	


if( $p["cmdSaveChange"]!="" ){?>
	

		<?php $dailyFrozenRePackingMainId		=	$p["editId"];
			if ($dailyFrozenRePackingMainId!="") {
				$i=1;				
				$mCPacking=$p["mcPackingId_".$i];
				$repselFrozenCode=$p["repselFrozenCode_".$i];
				$gradeRowCount	= $p["hidAllocateGradeRowCount"];
				$dailyFrozenrePackId=$p["delIdRep"];
				//echo "---$dailyFrozenrePackId";
				list($entryId)=$dailyfrozenpackinglistObj->getEntryId($dailyFrozenRePackingMainId);						
				list($gradeEntryId)=$dailyfrozenpackinglistObj->getGradeEntryId($entryId);
				$dailyfrozenpackinglistObj->updateDailyFrozenPackingEntry($mCPacking,$repselFrozenCode,$entryId);
				
				for ($j=1; $j<=$gradeRowCount; $j++) {
									$gradeId = $p["sGradeId_".$j."_".$i];
									$numMC = $p["numMC_".$j."_".$i];
									$numLS = $p["numLS_".$j."_".$i];
									if (($gradeId>0) && ($numMC>0 )) {
									$dailyfrozenpackinglistObj->updateDailyFrozenPackingGradeEdit($numMC,$numLS,$entryId,$gradeId);
									$dailyfrozenpackinglistObj->updateDailyFrozenRepackingGrade($numMC,$numLS,$dailyFrozenrePackId,$gradeId);
									}
				}
				
				//$dailyFrozenPackingGradeRecDelMain = $dailyfrozenpackinglistObj->deleteFrozenRePackingGradeRecMain($gradeEntryId);
				//$dailyFrozenPackingEntryRecDelMain = $dailyfrozenpackinglistObj->deleteFrozenRePackingEntryRecMain($entryId);
				//$dailyFrozenRePackingRecDelMain	=$dailyfrozenpackinglistObj->deleteDailyFrozenRePackingMainRecMain($dailyFrozenRePackingMainId);
				//list($glzentryId)=$dailyfrozenpackinglistObj->getRepEntryId($dailyFrozenRePackingMainId);
				$dailyfrozenpackinglistObj->updateDailyFrozenRepackingEntry($mCPacking,$repselFrozenCode,$dailyFrozenrePackId);
					
				 $editMode=false;

 }
 
 
 }



# Delete 

	if( $p["cmdDelete"]!=""){
		//$dailyFrozenrePackId=$p["delIdRep"];
		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)		{
			$dailyFrozenRePackingMainId		=	$p["delId_".$i];
			$dailyFrozenrePackId=$p["reglazeId_".$i];
			$rmlotId=$p["rmlotId_".$i];
			if ($dailyFrozenRePackingMainId!="") {
				
				if($rmlotId=='0')
				{
					list($entryId)=$dailyfrozenpackinglistObj->getEntryId($dailyFrozenRePackingMainId);		
					$dailyFrozenPackingEntryRecDelMain = $dailyfrozenpackinglistObj->deleteFrozenRePackingGradeWithEntryId($entryId);
					$dailyfrozenpackinglistObj->deleteFrozenRePackingEntryRecMain($entryId);
					$dailyFrozenPackingGradeRecDelMain = 
					$dailyFrozenRePackingRecDelMain	=$dailyfrozenpackinglistObj->deleteDailyFrozenRePackingMainRecMain($dailyFrozenRePackingMainId);
					$dailyFrozenPackingGradeRecDel = $dailyfrozenpackinglistObj->deleteFrozenRePackingGradeRec($dailyFrozenrePackId);
					$dailyFrozenRePackingRecDel	=	$dailyfrozenpackinglistObj->deleteDailyFrozenRePackingMainRec($dailyFrozenrePackId);
				}
				else
				{
					list($entryId)=$dailyfrozenpackinglistObj->getEntryIdRMLotID($dailyFrozenRePackingMainId);		$dailyFrozenPackingEntryRecDelMain = $dailyfrozenpackinglistObj->deleteFrozenRePackingGradeRMLotIDWithEntryId($entryId);	
					$dailyfrozenpackinglistObj->deleteFrozenRePackingEntryRecMainRMLotID($entryId);
					$dailyFrozenRePackingRecDelMain	=$dailyfrozenpackinglistObj->deleteDailyFrozenRePackingMainRecMainRMLotID($dailyFrozenRePackingMainId);
					$dailyFrozenPackingGradeRecDel = $dailyfrozenpackinglistObj->deleteFrozenRePackingGradeRecRMLotID($dailyFrozenrePackId);
					$dailyFrozenRePackingRecDel	=	$dailyfrozenpackinglistObj->deleteDailyFrozenRePackingMainRecRMLotID($dailyFrozenrePackId);


				}
				
				
				
				
					
			}

		}
		//if($dailyFrozenRePackingRecDelMain)
		//{
			$sessObj->createSession("displayMsg",$msg_succDelDailyFrozenRePacking);
			$sessObj->createSession("nextPage",$url_afterDelDailyFrozenRePacking.$selection);
		/*}
		else
		{
			$errDel	=	$msg_failDelDailyFrozenRePacking;
		}*/

		$dailyFrozenRePackingRecDelMain	=	false;
		 $editMode=false;

	}



#Cancel 	
	if ($p["cmdCancel"]!="")
	{
		//echo "hai";
		$addMode	=	false;
		$editMode	=	false;
		/*$mainId 	=	$p["mainId"];
		$entryId	=	$p["entryId"];
		
		if($p['editMode']==""){
			$dailyFrozenPackingGradeRecDel = $dailyfrozenpackinglistObj-> deleteFrozenPackingGradeRec($entryId);
					
			$frozenPackingEntryRecDel	=	$dailyfrozenpackinglistObj->deletePackingEntryRec($entryId);
			#Check Record Exists
			$exisitingRecords			=	$dailyfrozenpackinglistObj->checkRecordsExist($mainId);
			if(sizeof($exisitingRecords)==0)
				{
					$dailyFrozenRePackingRecDel	=	$dailyfrozenpackinglistObj->deleteDailyFrozenPackingMainRec($mainId);
				}
		}
		$mainId 		= "";
		$p["mainId"]	= "";
		$entryId		= "";
		$p["entryId"] 	= "";*/
	}

#List All Dailyfrozen Packing Records
	


#List All Fishes
$fishMasterRecords	=	$fishmasterObj->fetchAllRecords();

#List All Freezing Stage Record
	$freezingStageRecords		=	$freezingstageObj->fetchAllRecords();
	
#List All EU Code Records
	$euCodeRecords		=	$eucodeObj->fetchAllRecords();

#List All Brand Records
	$brandRecords		=	$brandObj->fetchAllRecords();

#List All Frozen Code Records
	$frozenPackingRecords		=	$frozenpackingObj->fetchAllRecords();
	
#List All MC Packing Records
	$mcpackingRecords		=	$mcpackingObj->fetchAllRecords();

#List All Re Packing Reason Records
	$rePackingRecords		=	$repackingObj->fetchAllRecords();

# select records between selected date
if($g["frozenPackingFrom"]!="" && $g["frozenPackingTill"]!=""){
	
	//$dateFrom = $g["frozenPackingFrom"];
	$fromdateSt=$g["frozenPackingFrom"];
	$fromdateStp=$g["frozenPackingFrom"];
	$fromdateSt=mysqldateformat($fromdateSt);
	$dateStFrom=$g["frozenPackingFrom"];
	//echo $fromdateSt;
	//$dateStFrom=dateFormat($fromdateSt);
	//echo "kkk";
	//echo $dateStFrom;
	//echo "kkk";
	$tillDate = $g["frozenPackingTill"];
	$tillDatep = $g["frozenPackingTill"];
	//echo "&&&&&&&&&&&&&&$tillDate";
	$tillDate=mysqldateformat($tillDate);
/*} else {
	$dateFrom = $p["frozenPackingFrom"];
	$dateTill = $p["frozenPackingTill"];
}*/
} else {
		/* As on Physical stock Entry last date*/		
		$maxdate= $dailyfrozenpackingObj->getMaxDate();
		//$currYear=Date("Y");
		//$currFinanYear="01/04/$currYear";
		$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());

		//$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):date("d/m/Y");
		$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):$defaultDFPDate;
		//$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):date("d/m/Y");
		$dateStFrom=$maximumdt;
		$dateTill = date("d/m/Y");
		$supplierFilterId = "";
	}




	if ($p["cmdSearch"]!="" || ($dateStFrom!="" && $dateTill!="")) {
		
	$dateF			=	explode("/", $dateFrom);
	$fromDate		=	$dateF[2]."-".$dateF[1]."-".$dateF[0];
	$fromdateSt=$dateF[2]."-".$dateF[1]."-".$dateF[0];
	$dateT			=	explode("/", $dateTill);
	$tillDate		=	$dateT[2]."-".$dateT[1]."-".$dateT[0];
	$isSearched	=	true;
} else {
	$CurrentDay = date("d/m/Y");
	$dateTill = date("d/m/Y");
	
	$dateF			=	explode("/", $dateFrom);
	$fromDate		=	$dateF[2]."-".$dateF[1]."-".$dateF[0];
	$dateT			=	explode("/", $dateTill);
	$tillDate		=	$dateT[2]."-".$dateT[1]."-".$dateT[0];
	//$Date			=	explode("/",$CurrentDay);
	//$fromDate		=	$Date[2]."-".$Date[1]."-".$Date[0];
	//$tillDate		=	$Date[2]."-".$Date[1]."-".$Date[0];
}

//$numPacks = $eCriteria[6];


## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------
	//echo $tillDate."---------";
	$fromdateSt=mysqldateformat($dateStFrom);
	$dailyFrozenRePackingRecords		=	$dailyfrozenpackinglistObj->getDFPForDateRange($fromdateSt, $tillDate,$offset, $limit);
	//$dailyFrozenRePackingRecordSize	=	sizeof($dailyFrozenRePackingRecords);
	$dailyFrozenRePackingRecordSize	=sizeof($dailyfrozenpackinglistObj->getDFPReForDateRange($fromdateSt, $tillDate));
	$numrows=sizeof($dailyfrozenpackinglistObj->getDFPReForDateRange($fromdateSt, $tillDate));
			
			## -------------- Pagination Settings II -------------------
			$maxpage	=	ceil($numrows/$limit);

	
	//$dailyFrozenRePackingRecordSize	=	sizeof($dailyFrozenRePackingRecords);

	

	if ($editMode) {
		$heading	=	$label_editDailyFrozenPackingList;
	} else {
		$heading	=	$label_addDailyFrozenPackingList;
	}
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/dailyfrozenpackinglist.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmDailyFrozenRePacking" action="DailyFrozenRePacking.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">
			<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?php
		//echo $editMode;
			if( $editMode || $addMode)
			{
				$dailyFrozenreRepackId=$p["delIdRep"];
				$editId=$p["editId"];
		$editCriteria		= $p["editCriteria"];
		//echo $editCriteria;
		$eCriteria		= explode(",",$editCriteria);		
		$processId		= trim($eCriteria[0]);
		$freezingStage		= trim($eCriteria[1]);
		$frozenCode			= trim($eCriteria[2]);
		$displayEditMsg		= strtoupper(trim($eCriteria[3]));
		$dis=explode("-",$displayEditMsg);
		$selMCPkgId			= trim($eCriteria[4]);
		$unit=$eCriteria[6];
		$processorId=$eCriteria[5];
		//echo "The unit is $unit $processorId";
		
		$mcPackingId=$selMCPkgId;
		//$numPacks = $eCriteria[6];

				
				$productRecs = $frozenStockAllocationObj->getAllocateProductionRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $selMCPkgId);			
				$productRecSize = sizeof($productRecs);
				
				//$gradeRecs = $frozenStockAllocationObj->getProductionGradeRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $selMCPkgId);
				$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecords($processId, $entryId);
				
				$leftBdr 	= "border-left: 1px solid #999999;";
				$rightBdr 	= "border-right: 1px solid #999999;";
				$topBdr 	= "border-top: 1px solid #999999;";
				$bottomBdr 	= "border-bottom: 1px solid #999999;";
				$fullBdr	= "border: 1px solid #999999;";
			
		
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  >
					<tr>
						<td   bgcolor="white" nowrap  style="<?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" >
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
												<?php if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenRePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDFPPacking(document.frmDailyFrozenRePacking);"></td>
												
												<?php } else{?>

												
											  <td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenRePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenRePacking(document.frmDailyFrozenRePacking);">&nbsp;&nbsp;												</td>
<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<input type="hidden" name="hidDailyFrozenPackingId" value="<?=$dailyFrozenPackingId;?>">
											<tr>
											  <td nowrap class="fieldName"></td>
										  </tr>
											<tr>
											  <td colspan="2" height="10"></td>
										  </tr>
											
											<tr>
											  <td colspan="2" style="padding-left:60px;">&nbsp;</td>
										  </tr>
											<tr>
											  <td colspan="2" align="center">
											  <table width="75%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
        <td valign="top">
		<table width="585">	
		<tr>
                                                 <td class="fieldName" colspan=3><div align="center">Date:<span class="listing-item">
                                                   <?php		
												 
							if($selDate=="") $selDate=date("d/m/Y");
											
							?>
                                                   <input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autocomplete="off" />
                                                 </span></div></td>
                                </tr>
		<tr><TD class="listing-head" nowrap="true">
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
			<!--<table width="533" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnAllocateTble">-->

			<table width="574" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnDtlsTble">
			<!--<tr bgcolor="#f2f2f2"  align="center">	
				
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="<?=sizeof($gradeRecs)+4?>">&nbsp;</td>	-->
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$fullBdr?> " class="listing-head" colspan="<?=sizeof($gradeRecs)?>">
					SLABS OF EACH GRADE/COUNT
				</td>-->
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">&nbsp;</td>			
			</tr>-->
			<tr bgcolor="#f2f2f2"  align="center">
			  <td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="4">&nbsp;</td>
			  <td width="182" colspan="<?=sizeof($gradeRecs)+1?>" nowrap class="listing-head" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$topBdr.$bottomBdr?>"><span class="listing-head" style="padding-left:2px;padding-right:2px; ">SLABS OF EACH GRADE/COUNT</span></td>
			  
			  <td width="52" nowrap class="listing-head" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$topBdr.$bottomBdr?>">TOTAL</td>
			  </tr>
			<tr bgcolor="#f2f2f2"  align="center">
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="4">&nbsp;<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO--></td>
				
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head" title="Set Purchase order">
				
					&nbsp;</td>			
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>-->
			
				<?php
					$g = 1;
					foreach ($gradeRecs as $gR) {
						$gId = $gR[0];
						$gradeCode = $gR[1];
				?>
					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
						<?=$gradeCode?>
						<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />					</td>
				<?php
						$g++;
					}
				?>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
				<td width="80" nowrap class="listing-head" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">QTY (KG)</td>
			</tr>
			<tr bgcolor="#f2f2f2"  align="center">
			
				
				
<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="2" ><span class="listing-head" style="padding-left:2px;padding-right:2px; ">SET MC PKG</span></td>
<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" ><span class="listing-head" style="padding-left:2px;padding-right:2px; ">SET Frozen Code</span></td>
				
				<td width="29" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">&nbsp;</td>
				
				<?php
					$totAvailableMC = 0;
					$totAvailableLS = 0;
					$lastVal=explode(" ",$dis[2]);
							//print_r($lastVal);
							$n=count($lastVal);
							//echo $n;
							$lastVal=$lastVal[$n-2];
					$glazeId=$frozenpackingObj->frznPkgglaze($frozenCode);
					$getF=$frozenpackingObj->find($frozenCode);
					//$selFrozenC=$frozenpackingObj->find($glfrozenCodeId);
					
					$selFrozenCode=$getF[0];
					 //echo "---".$selFrozenCode;
					//$selFrozenC=$frozenpackingObj->find($glfrozenCodeId);
					$selFrozenCode1=$getF[1];
					$frozenCodeValues=$frozenStockAllocationObj->getFrozQty($processId,$glazeId,$lastVal);
					$dateFrom=mysqlDateFormat($maximumdt);
					foreach ($gradeRecs as $gR) {
						$j++;
						
						$sGradeId   = $gR[0];
						list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $selMCPkgId,$dateFrom,$tillDate);
						$selectDate		=($p["selDate"]!="")?mysqlDateFormat($p["selDate"]):mysqlDateFormat(date("d/m/Y"));
						
						list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$selMCPkgId,$dateFrom,$sGradeId);
						$availableNetMC=$availableMC-$thawingGrdTotal;
						$totAvailableMC += $availableNetMC;
						$totAvailableLS += $availableLS;
				?>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
					<table cellpadding="1" cellspacing="0" width="100%">
					<tr>
						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
						<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
							<b><?=($availableNetMC!=0)?$availableNetMC:"&nbsp;"?></b>						</td>
					</tr>
					<!--<tr>
						<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
					</tr>-->
					<!--<tr>
						<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
							<b><?//=($availableLS!=0)?$availableLS:"&nbsp;"?></b>
						</td>
					</tr>-->
					</table>				</td>
				<?php
					} // Grade Loop Ends here

					# Total Available Slabs
					$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
				?>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
					<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
&nbsp;				</td>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
			</tr>
			<?php
				$i = 0;
				$pkgGroupArr = array();
				$lsPkgGroupArr = array();
				$k=1;
				foreach ($productRecs as $pr) {
					$i++;
					
				$dFrznPkgEntryId = $pr[0];
					$frozenLotId 	= $pr[1];
					$mcPackingId	= $pr[2];
					$mcPkgCode	= $pr[3];
					
					$selProcessCodeId=$pr[7];
					$selFreezingStageId=$pr[8];
					$selFrozenCodeId=$pr[9];
					$frznStkMCPkgId=$pr[10];
					

								
			?>	
			<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">				
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?//=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-item" align="center">
					<table cellpadding="0" cellspacing="0">
						<TR>
						
							
							<?php 
				if ($thawingStatus==0)
					{
				?>
				<TD style="padding-left:5px;padding-right:5px;">
								<a onclick="setAllocateRowStatus(this);" href="javascript:void(0);" id="allocateRemoveLink_<?=$i?>"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
								</TD>
								<?php }?>
							
							
						</TR>
					</table>	-->									
			
					<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
					<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
					<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
					<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
				<!--</td>
				<td nowrap style="padding-left:2px;padding-right:2px; <?//=$rightBdr.$bottomBdr?>" class="listing-item">	
				

			
						
						



				&nbsp;
				</td>-->
				<td colspan="2" rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>"><span class="listing-head" style="padding-left:2px;padding-right:2px; ">
				  <select name="mcPackingId_<?=$i?>" id="mcPackingId_<?=$i?>" onchange="xajax_assignMCPackChgrprg(document.getElementById('mcPackingId_<?=$i?>').value, '<?=$i?>');"  >
                    <option value="0">--Select--</option>
                    <?php
						  foreach($mcpackingRecords as $mcp) {
							$mcPkgId		= $mcp[0];
							$mcpackingCode		= stripSlash($mcp[1]);
							$selected		= ($mcPackingId==$mcPkgId)?"selected":"";
						?>
                    <option value="<?=$mcPkgId?>" <?=$selected?>>
                      <?=$mcpackingCode?>
                      </option>
                    <? }?>
                  </select>
				</span>
				
				<input type=hidden name="hidflag_<?=$i?>" value="c" id="hidflag_<?=$i?>" readonly />
				<input type=hidden name="finfrozenCode" id="finfrozenCode" value="<?=$frozenCode;?>" />
				<input type=hidden name="hidffilledWt" id="hidffilledWt" value="" />
				</td>
				<td rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>"><?//=$k;?><!--<select id="repselFrozenCode_<?=$k?>" name="repselFrozenCode_<?=$k?>" onchange="xajax_assignFrzPackChg(this.value);" >-->

				<select id="repselFrozenCode_<?=$k?>" name="repselFrozenCode_<?=$k?>" onchange="xajax_assignFrzPackChg(this.value);xajax_getFilledWt(document.getElementById('repselFrozenCode_<?=$k?>').value, '<?=$k?>'); xajax_getMCPkgs('<?=$k?>', document.getElementById('repselFrozenCode_<?=$k?>').value, '')" >
												<?php		
												if (sizeof($frozenCodeValues)>0) {	
													 foreach($frozenCodeValues as $frozenPackingId=>$frozenPackingCode) {
														
														//if ($frozenPackingId==$frozenCode) continue;
												?>	
													<option value="<?=$frozenPackingId?>" <?php if ($selFrozenCode==$frozenPackingId){?> selected <?php }?>  ><?=stripslashes($frozenPackingCode)?></option>
												<?php
														}
												} else {
												?>
												<option value="<?=$frozenCode?>" ><?=stripslashes( $selFrozenCode1)?></option>
												<option value="">-- Select --</option>
												<?php
												}
												?>	
												</select>&nbsp;</td>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item"><span class="listing-item" style="padding-left:2px;padding-right:2px;">MC</span></td>
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
							//list($allocateGradeEntryId, $numMC, $numLS) = $frozenStockAllocationObj->getAllocatedSlab($POEntryId, $sGradeId);
						} else {
							//list($gradeEntryId, $numMC, $numLS) = $frozenStockAllocationObj->getSlab($dFrznPkgEntryId, $sGradeId);
						}



						
						
						$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
						$lsPkgGroupArr[$sGradeId] += $numLS;
							list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $selMCPkgId,$dateFrom,$tillDate);
							//new Code
							//$totNumMC += $availableMC;
							$totNumLS += $availableLS;
							list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$selMCPkgId,$dateFrom,$sGradeId);
							$availableNetMC=$availableMC-$thawingGrdTotal;
							$totNumMC += $availableNetMC;
							//echo $selMCPkgId;
							$mcpackingRec	= $mcpackingObj->find($selMCPkgId);
							$numPacks	= $mcpackingRec[2];
							$hidtotSlabs=$availableNetMC*$numPacks;
							if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
							





				?>
				<td rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">
					<table cellpadding="1" cellspacing="0" width="100%">
					<tr>
						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
						
							<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
							<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
							<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
							<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
							<!--<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />-->
							<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />
							<input name="inumMC_<?=$j?>_<?=$i?>" type="hidden" id="inumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
							<input name="numMCG_<?=$j?>_<?=$i?>" type="hidden" id="numMCG_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />
							<input name="hidnumMC_<?=$j?>_<?=$i?>" type="hidden" id="hidnumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($hidtotSlabs!=0)?$hidtotSlabs:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />						</td>
					</tr>
					<tr>
						<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
					</tr>
				<tr>
						<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
							<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>	
							<input name="inumLS_<?=$j?>_<?=$i?>" type="hidden" id="inumLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>
							<input name="numLSG_<?=$j?>_<?=$i?>" type="text" id="numLSG_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>
							<input name="hidnumLS_<?=$j?>_<?=$i?>" type="hidden" id="hidnumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
								
							
							</td>
					</tr>
					</table>				</td>
					
				<?php
					} // Grade Loop Ends here

					# Total Slabs
					$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;
					

					#total Qty	
					$totalQty	= $totalSlabs*$filledWt;
				?>	
				<td rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">
					<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />				</td>
				<td rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">
					<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />				</td>
			</tr>
			<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">
			  <td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item"><span class="listing-item" style="padding-left:2px;padding-right:2px;">LS</span></td>
			  </tr>
			<?php
				} // Loop Ends here
			?>
			<tr>
			  <td width="111"></td>
			  <td width="2" height="10"></td>
			  <td width="104"></td>
			</tr>
			<tr>
				<td style="padding-left:10px;padding-right:10px;" nowrap colspan="3">&nbsp;				</td>
			</tr>
			<tr bgcolor="White" >
			  <td>&nbsp;</td>
			  <input type="hidden" name="numMcPack_<?=$i?>" id="numMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
			<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
			<input type="hidden" name="hidNumMcPackPrev_<?=$i?>" id="hidNumMcPackPrev_<?=$i?>" value="<?=$selMCPkgId;?>" readonly="true" />
			
				<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td style="<?//=$bottomBdr?>">&nbsp;</td>
				<?php					
					foreach ($gradeRecs as $gR) {
				?>
					<td style="<?//=$bottomBdr?>">&nbsp;</td>
				<?php
					}
				?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php				
				$fieldRowSize = $productRecSize+1;
				$p = 1;
				$totAllocatedMC = 0;
				$totAllocatedLS = 0;
				foreach ($pkgGroupArr as $pga=>$gradeArr) {					
					$selMcPkgCode = $pga;
			?>
			<!--<tr bgcolor="White" id="tRow_<?=$fieldRowSize+$p?>">
				<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff;">&nbsp;</TD>
				<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">MC PKG1</TD>
				<TD style="<?=$rightBdr.$bottomBdr?>">&nbsp;</TD>
				<?php					
					foreach ($gradeRecs as $gR) {
						$gradeId = $gR[0];
						$mcQty  = $gradeArr[$gradeId];
						$totAllocatedMC += $mcQty;
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
			<input type="hidden" name="hidSummaryTblRowCount" id="hidSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
			<input type="hidden" name="hidProdnRowCount" id="hidProdnRowCount" value="<?=$i?>" readonly="true" />
		<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
			<input type="hidden" name="hidNumPack" id="hidNumPack" value="<?=$numPacks?>"/>
			<input type="hidden" name="hidLS2MCType" id="hidLS2MCType" value="<?=$LSToMCConversionType?>" readonly />
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
				<input type="hidden" name="filledWt" id="filledWt" value="<?=$filledWt?>" readonly/>
				<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" />
				<input type="hidden" name="hidMCPkgCode" id="hidMCPkgCode" value="<?=$stkAllocateMCPkgCode?>" />
				<input type="hidden" name="hidDelAllocationArr" id="hidDelAllocationArr" value="" />
			<input type="hidden" name="hidProcessId" id="hidProcessId" value="<?=$processId?>" readonly />
			<input type="hidden" name="hidProcessorId" id="hidProcessorId" value="<?=$processorId?>" readonly />
			<input type="hidden" name="hidunit" id="hidunit" value="<?=$unit?>" readonly />
				<input type="hidden" name="hidFreezingStage" id="hidFreezingStage" value="<?=$freezingStage?>" readonly />
				<input type="hidden" name="hidFrozenCode" id="hidFrozenCode" value="<?=$frozenCode?>" readonly />
				<input type="hidden" name="hidMCPkgId" id="hidMCPkgId" value="<?=$selMCPkgId?>" readonly />
				<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
		<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />

			<input type="hidden" name="hidAllocateSummaryTblRowCount" id="hidAllocateSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
			<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$productRecSize?>" readonly="true" />
			<!--<tr bgcolor="White">-->
				<!--<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff;">&nbsp;</TD>-->
				<!--<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?//=$leftBdr.$rightBdr.$bottomBdr?>">LS SLABS</TD>-->
				<!--<TD style="<?//=$rightBdr.$bottomBdr?>">&nbsp;</TD>-->
				<?php					
			/*foreach ($gradeRecs as $gR) {
						$gradeId = $gR[0];
						$lsQty  = $lsPkgGroupArr[$gradeId];
						$totAllocatedLS += $lsQty;*/
				?>
			<!--	<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" id="LS_<?=$gradeId?>"><strong><?=($lsQty!=0)?$lsQty:"&nbsp;";?></strong></TD>-->
					<?php //} 
								
				
				
				// Grade Loop ends here

					# Total Allocated Slabs
					//$totAllocatedSlabs 	= ($totAllocatedMC*$numPacks)+$totAllocatedLS;
				?>
				<!--<TD colspan="2"><input type="hidden" name="totAllocatedSlabs" id="totAllocatedSlabs" value="<?//=$totAllocatedSlabs?>" readonly="true" />&nbsp;</TD>-->				
			<!--</tr>-->
			</table>		</TD>
		</TR>		
		</table>
		
		
		
		</td>
				 	 </tr>
					<? if($fishId!="" && $processId!=""){?>
					<tr>
					  <td colspan="2" align="center"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                                         <tr>
                                             <td>
					<table align="center">
					<tr><td>
					 <fieldset>
					 <legend class="listing-item">Grade</legend>
			 		<iframe id="iFrame1" 
src ="DailyFrozenRePackingGrade.php?mainId=<?=$mainId?>&fishId=<?=$fishId?>&process=<?=$processId?>" width="500" frameborder="0" height="300" name="iFrame1"></iframe>
					  </fieldset>
												  </td></tr></table>												  </td>
                                                   </tr>
                                                
                                              </table></td>
										  </tr>
										  <? }?>
											<tr>
											  <td colspan="2" align="center"><table width="75%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td valign="top"><!--<table width="200">
                                                    
                                                    
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Reason for Repacking </td>
                                                      <td>
													  <? if($p["reasonRePack"]!="") $reasonRePack = $p["reasonRePack"];?>
													  <select name="reasonRePack" id="reasonRePack">                                                      <option value="">-- Select--</option>
                                                        <?
													 foreach($rePackingRecords as $rpr)
														{
														$rePackingId		=	$rpr[0];
														$rePackingCode		=	stripSlash($rpr[1]);
														$rePackingReason	=	stripSlash($rpr[2]);
														$selected		=	"";
														if($reasonRePack==$rePackingId || $repackReasonId==$rePackingId)  $selected	=	" selected ";
													  
													  ?>
													  <option value="<?=$rePackingId?>" <?=$selected?>><?=$rePackingCode?></option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">No of New Inner Packs used</td>
                                                      <td>
													  <? if($p["numNewInnerPack"]!="") $numNewInnerPack = $p["numNewInnerPack"];?>
													  <input name="numNewInnerPack" type="text" id="numNewInnerPack" size="4" value="<?=$numNewInnerPack?>"></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">No of Labels / Header Cards Used</td>
                                                      <td>
													  <? if($p["numLabelCard"]!="") $numLabelCard = $p["numLabelCard"];?>
													  <input name="numLabelCard" type="text" id="numLabelCard" size="4" value="<?=$numLabelCard?>"></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">No of New MC Used</td>
                                                      <td>
													  <? if($p["numNewMC"]!="") $numNewMC = $p["numNewMC"];?>
													  <input name="numNewMC" type="text" id="numNewMC" size="4" value="<?=$numNewMC?>"></td>
                                                    </tr>
                                                    
                                                  </table>--></td>
                                                  <td valign="top"><!--<table width="200">
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked EU Code</td>
                                                      <td>
													  <? if($p["rePackEUCode"]!="") $rePackEUCode = $p["rePackEUCode"];?>
													  <select name="rePackEUCode" id="rePackEUCode">
                                                        <option value="">-- Select--</option>
                                                        <?
													  foreach($euCodeRecords as $eucr)
														{
														$euCodeId		=	$eucr[0];
														$euCode			=	stripSlash($eucr[1]);
														
														$selected		=	"";
														if($rePackEUCode==$euCodeId)  $selected	=	" selected ";
													  
													  ?>
                                                        <option value="<?=$euCodeId?>" <?=$selected?>>
                                                          <?=$euCode?>
                                                        </option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked Brand</td>
                                                      <td>
													  <? if($p["rePackBrand"]!="") $rePackBrand =$p["rePackBrand"];?>
													  <select name="rePackBrand" id="rePackBrand">
                                                        <option value="">-- Select --</option>
                                                        <?
													  foreach($brandRecords as $br)
														{
														$brandId		=	$br[0];
														$brandName		=	stripSlash($br[2]);
														$customerName	=	stripSlash($br[3]);
														$displayBrand   = 	$brandName."&nbsp;(".$customerName.")";
														$selected		=	"";
														if($rePackBrand==$brandId)  $selected	=	" selected ";
													  ?>
														<option value="<?=$brandId?>" <?=$selected?>><?=$displayBrand?></option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked Frozen Code</td>
                                                      <td nowrap="nowrap">
													  <? if($p["rePackFrozenCode"]!="") $rePackFrozenCode=$p["rePackFrozenCode"];?>
													  <select name="rePackFrozenCode" id="rePackFrozenCode">
                                                        <option value="">-- Select --</option>
                                                        <?
													  foreach($frozenPackingRecords as $fpr)
														{
														$frozenPackingId		=	$fpr[0];
												
														$frozenPackingCode		=	stripSlash($fpr[1]);													  
														$selected		=	"";
														if($rePackFrozenCode==$frozenPackingId)  $selected	=	" selected ";
														
													  ?>
                                                        <option value="<?=$frozenPackingId?>" <?=$selected?>>
                                                          <?=$frozenPackingCode?>
                                                        </option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked MC Pkg</td>
                                                      <td nowrap="nowrap">
													  <? if($p["rePackMCPacking"]!="") $rePackMCPacking = $p["rePackMCPacking"];?>
													  <select name="rePackMCPacking" id="rePackMCPacking">
                                                        <option value="">-- Select --</option>
                                                        <?
													  foreach($mcpackingRecords as $mcp)
														{
														$mcpackingId		=	$mcp[0];
														$mcpackingCode	=	stripSlash($mcp[1]);
														$selected		=	"";
														if($rePackMCPacking==$mcpackingId)  $selected	=	" selected ";
													  ?>
                                                        <option value="<?=$mcpackingId?>" <?=$selected?>>
                                                          <?=$mcpackingCode?>
                                                        </option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    </table>--></td>
                                                </tr>
                                              </table></td>
										  </tr>
											<tr>
											  <td align="center">&nbsp;</td>
											  <td align="center">&nbsp;</td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenRePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDFPPacking(document.frmDailyFrozenRePacking);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenRePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenRePacking(document.frmDailyFrozenRePacking);">&nbsp;&nbsp;												</td>

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
			
				$leftBdr 	= "border-left: 1px solid #999999;";
				$rightBdr 	= "border-right: 1px solid #999999;";
				$topBdr 	= "border-top: 0.5px solid #999999;";
				$bottomBdr 	= "border-bottom: 1px solid #999999;";
				$fullBdr	= "border: 1px solid #999999;";
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td  >
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  >
					<tr>
						<td   bgcolor="white"  style="<?=$leftBdr.$rightBdr.$bottomBdr.$topBdr ?>" >
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Frozen Packing List</td>
								    <td background="images/heading_bg.gif" class="pageName" align="right" ><table cellpadding="0" cellspacing="0" width="50%">
                      <tr> 
					  	<td class="listing-item">As&nbsp;On:</td>
                                    <td nowrap="nowrap"> 
                            <? 
							
							if($dateStFrom=="") $dateStFrom=date("d/m/Y");
							
							?>
                            <input type="text" id="frozenPackingFrom" name="frozenPackingFrom" size="8" value="<?=$dateStFrom?>"></td>
						            <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					     if($dateTill=="") $dateTill=date("d/m/Y");
				  ?>
                                      <input type="text" id="frozenPackingTill" name="frozenPackingTill" size="8"  value="<?=$dateTill?>"></td>
							        <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search " onclick="return validateDailyFrozenPackingSearch(document.frmDailyFrozenRePacking);"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<!--<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyFrozenRePackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFrozenRePacking.php',700,600);"><? }?></td>
											</tr>-->
										</table>									</td>
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
		<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
<?
	if (sizeof($dailyFrozenRePackingRecordSize)>0) {
		$i	=	0;
	?>
	 <? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+8;?>" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			$nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
  				} else {
				      	$nav.= " <a href=\"DailyFrozenRePacking.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\" class=\"link1\">$page</a> ";
	   			}
			}
			if ($pageNo > 1) {
		   		$page  = $pageNo - 1;
   				$prev  = " <a href=\"DailyFrozenRePacking.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
		   		$page = $pageNo + 1;
   				$next = " <a href=\"DailyFrozenRePacking.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\">>></a> ";
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







		<?php
		
		if( sizeof($dailyFrozenRePackingRecords) > 0 )
			{
			$i	=	0;
		?>
		<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
		<!--<td class="listing-head" nowrap>&nbsp;&nbsp;Date</td>
		<td class="listing-head" >Fish</td>-->	<td class="listing-head" >RM Lot ID</td>
												<td class="listing-head" >Process Code</td>
												<td class="listing-head" >Freezing Stage</td>
												<!--<td class="listing-head" >EU Code</td>
												<td class="listing-head">Brand</td>-->
												<td class="listing-head">Frozen Code&nbsp; </td>
												<td class="listing-head">MC Pkg</td>
												<td class="listing-head" >No.of MCs</td>
												<td class="listing-head" >No.of LSs</td>
		<!--<td class="listing-head">Re-Packed Quantity</td>-->
												<? //if($edit==true){?>
												<!--<td class="listing-head" width="45"></td>-->
<? //}?>
	</tr>
	<?php
	foreach($dailyFrozenRePackingRecords as $dfrp)
	{
		$i++;
		$dailyFrozenPackingMainId	=	$dfrp[0];
		$entryDate 	=	$dfrp[16];
		$selectedDate	=	dateFormat($entryDate);
		$fish	=	$fishmasterObj->findFishName($dfrp[2]);
		$processCode = $processcodeObj->findProcessCode($dfrp[3]);
		$selProcessCode=$processCode;
		$selProcessCodeId=$dfrp[3];
		$freezingStage = $freezingstageObj->findFreezingStageCode($dfrp[4]);
		$selFreezingStageId=$dfrp[4];
		$selFreezingStage=$freezingStage;
		$eUCode = $eucodeObj->findEUCode($dfrp[4]);
		$brand = $brandObj->findBrandCode($dfrp[5]);
		$frozenCode = $frozenpackingObj->findFrozenPackingCode($dfrp[5]);
		$selFrozenCode=$frozenCode;
		$selFrozenCodeId=$dfrp[5];
		$mCPackingCode = $mcpackingObj->findMCPackingCode($dfrp[11]);
		$selMCPkgCode=$mCPackingCode;
		$selMCPackingId=$dfrp[11];
		$rePackingReason	=	$repackingObj->findRePackingReason($dfrp[8]);
		$flag=$dfrp[19];
		$unit=$dfrp[20];
		$processor_id=$dfrp[21];
		$numLS=$dfrp[22];
		$reglazemainid=$dfrp[23];
		$rmlotid=$dfrp[24];
		
		($rmlotid>0) ? $rmlotidNm=$dfrp[25]:$rmlotidNm='';
		# Edit criteria
		$selEditCriteria = "$selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selProcessCode - $selFreezingStage - $selFrozenCode - $selMCPkgCode, $selMCPackingId,$unit,$processor_id";

		//echo $selEditCriteria;
		
		//list($rePackedQty)=$dailyfrozenpackinglistObj->getRepackGradeQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromDate);
		list($thawingTotal)=$dailyfrozenpackinglistObj->getThaQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromdateSt,$tillDate);
		
		
		list($allocatedTotal)=$dailyfrozenpackinglistObj->getAllocQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromdateSt,$tillDate);
		//echo "Alloc$allocatedTotal";
		if (($flag==1) || ($flag==0))
		{
		$numMCs=$dfrp[14]-($thawingTotal+$allocatedTotal);
		if ($numMCs<0)
			{
				$numMCs="";
			}
		//$numMCs=$dfrp[14];
		}
		else
		{
			$numMCs=$dfrp[14];
		}
		$disabled="";
		if ($flag==2)
		{
			//$disabled="disabled";

		}
		$repackMcSum=$numMCs+$repackMcSum;
		$repackLSSum=$numLS+$repackLSSum;
		
		?>
	<tr  bgcolor="WHITE"  >
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyFrozenPackingMainId;?>" onClick="assignValue(this.form,<?=$reglazemainid;?>,'delIdRep');"><?//=$dailyFrozenPackingMainId;?>
		<input type='hidden' name='reglazeId_<?=$i?>' id='reglazeId_<?=$i?>' value="<?=$reglazemainid;?>"/>
		<input type='hidden' name='rmlotId_<?=$i?>' id='rmlotId_<?=$i?>' value="<?=$rmlotid;?>"/>
		
		</td>
		<!--<td class="listing-item" nowrap style="padding-left:10px;"><?=$selectedDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fish?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$rmlotidNm?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$processCode?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$freezingStage?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px;"><?//=$eUCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?//=$brand?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$frozenCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$mCPackingCode?></td>
		<td class="listing-item" nowrap style="padding-left:10px;" align="right"><?=($numMCs!=0)?$numMCs:"";?></td>
		<td class="listing-item" nowrap style="padding-left:10px;" align="right"><?=($numLS!=0)?$numLS:"";?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px;"><?//=$rePackedQty;?></td>-->
		<? //if($edit==true){?>
		<!--<td class="listing-item" width="45" align="center"><input type="submit" value="Edit" name="cmdEdit" onClick="assignValue(this.form,<?=$dailyFrozenPackingMainId;?>,'editId');assignValue(this.form,'<?=$selEditCriteria?>','editCriteria'); assignValue(this.form,'1','editSelectionChange'); this.form.action='DailyFrozenRePacking.php';" <?php echo $disabled;?>></td>-->
		<? //}?>
	</tr>
	
		<?
			}
		?>
		<tr  bgcolor="WHITE"  >
	  <td>&nbsp;</td>
	  <td colspan="4" nowrap class="listing-item" style="padding-left:10px;">&nbsp;</td>
	  <td class="listing-item" nowrap style="padding-left:10px;"><span class="listing-head" style="padding-left:10px; padding-right:10px;">Total:</span></td>
	  <td class="listing-item" nowrap style="padding-left:10px;" align="right">
	    <?=$repackMcSum;?>
	  </td>
	  <td class="listing-item" align="right"><?=$repackLSSum;?></td>
	  <!-- <td class="listing-item" align="right">&nbsp;</td>-->
	  </tr>
	  <tr  bgcolor="WHITE">
            <td colspan="8" align="center"><input type="submit" value=" Delete " class="button"  name="cmdDelete" onclick="return cfmDelete(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);" /></td>
            </tr>
	<input type="hidden" name="editCriteria" id="editCriteria" value="<?=$editCriteria?>" readonly="true">
	<input type="hidden" name="editId"  id="editId" value="<?=$editId?>">
	<input type="hidden" name="delIdRep" id="delIdRep" value="<?=$dailyFrozenreRepackId?>">
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	
	<!--input type="hidden" name="editFrozenPackingEntryId" value="<?=$frozenPackingEntryId;?>"-->
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="editMode" value="<?=$editMode?>">
		<?
			} else {
		?>
	<tr bgcolor="white">
		<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>



	 <? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+8;?>" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			$nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
  				} else {
				      	$nav.= " <a href=\"DailyFrozenRePacking.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\" class=\"link1\">$page</a> ";
	   			}
			}
			if ($pageNo > 1) {
		   		$page  = $pageNo - 1;
   				$prev  = " <a href=\"DailyFrozenRePacking.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
		   		$page = $pageNo + 1;
   				$next = " <a href=\"DailyFrozenRePacking.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\">>></a> ";
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

	<?php
			} // Main loopEnds here 				
	?>
	</table>
	
	         <input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
	      <!--input type="hidden" name="entryId" id="entryId" value="<?=$entryId?>"-->
	    
	    <table width="200" border="0" align="center">
         <!-- <tr>
            <td><input type="submit" value=" Delete " class="button"  name="cmdDelete" onclick="return cfmDelete(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);" /></td>
            </tr>-->
        </table>	    
	    <p>&nbsp; </p></td>
	</tr>
	<tr>
		<td colspan="3" height="5" ></td>
	</tr>
	<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<!--<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyFrozenRePackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFrozenRePacking.php',700,600);"><? }?></td>
											</tr>-->
										</table>									</td>
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
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>