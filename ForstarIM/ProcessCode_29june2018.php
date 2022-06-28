<?php
	require("include/include.php");
	require('lib/processcode_ajax.php');
	ob_start();

	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$copyFishId	=	"";
	$recUpdated 	= false;
	
	$selCriteria = "?selFilter=".$p["selFilter"]."&pageNo=".$p["pageNo"];
	
	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	#For Refreshing the main Window when click PopUp window
	$selProcessCodeId = "";
	if ($g["popupWindow"]=="") $popupWindow = $p["popupWindow"];
	else {
		$popupWindow 		= $g["popupWindow"];
		$selProcessCodeId 	= $g["selProcessCodeId"];
	}

	# Add Fish Start 
	if ($p["cmdAddNew"]!="") {
		$addMode = true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}
	
	$copyFromId		=	$p["selCopyFrom"];
	# Insert
	if ($p["cmdAddProcessCode"]!="" ) {
		$fishId				=	$p["processCodeFish"];
		$Code				=	addSlash(trim($p["processCode"]));
		$Descr				=	addSlash($p["processCodeDescr"]);
		$Weight				=	$p["processBasketWt"];
		$arrivalOption		=	$p["available"];
		if ($arrivalOption =='G' || $arrivalOption == 'B' ) {			
			//$gradeId		=	$p["selGrade"]; 			
			$gradeId		=	explode(",",$p["selRawGrade"]);
		}
				
		
		$frozenAvailable	=	$p["frozenAvailable"];
		if($frozenAvailable =='G' || $frozenAvailable =='B'){
		
			//$gradeFrozenId	=	$p["selGradeFrozen"];
			$gradeFrozenId		=	explode(",",$p["selFrozenGrade"]);
			
		}		
	
		$rawGradeUnit		=	($p["gradeUnitRaw"]=="")?0:$p["gradeUnitRaw"];
		$rawCountUnit		=	($p["countUnitRaw"]=="")?0:$p["countUnitRaw"];
		$frozenGradeUnit	=	($p["gradeUnitFrozen"]=="")?0:$p["gradeUnitFrozen"];
		$frozenCountUnit	=	($p["countUnitFrozen"]=="")?0:$p["countUnitFrozen"];
		
		
		$copyFishId			=	$p["selCopyFrom"];
		$copyCodeId			=	$p["selProcessCode"];
				
		if ($fishId!="") {
			if($copyFishId!="") $copyFrom	=	true;
			else $copyFrom	=	false;
			
			if($copyCodeId!="") $copyCode	=	true;
			else $copyCode	=	false;
			
			#For Checking Unique Record based on Fish & Code & quality
			$uniqueRecords		=	$processcodeObj->fetchAllUniqueRecords($fishId,$Code);	
			
			if (sizeof($uniqueRecords)==0 ) {
				$processCodeRecIns	=	$processcodeObj->addProcessCode($fishId, $Code, $Descr,$Weight, $gradeId, $arrivalOption, $copyFrom, $copyCode, $copyFishId,$copyCodeId, $gradeFrozenId, $frozenAvailable,$rawGradeUnit,	$rawCountUnit, $frozenGradeUnit, $frozenCountUnit);

			if ($processCodeRecIns) {
				$addMode=false;
				$sessObj->createSession("displayMsg",$msg_succAddProcessCode);
				$sessObj->createSession("nextPage",$url_afterAddProcessCode.$selCriteria);
				$recUpdated = true;
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddProcessCode;
			}
			$processCodeRecIns		=	false;
		} else  {
				$addMode	=	true;
				$err		=	$msg_failAddProcessCode;
			}
		$uniqueRecords = false;
	}
}
	# Update
	if ($p["cmdSaveChange"]!="") {
		$processCodeId		=	$p["hidProcessCodeId"];
		$fishId			=	$p["processCodeFish"];
		$Code			=	addSlash(trim($p["processCode"]));
		$Descr			=	addSlash($p["processCodeDescr"]);
		$Weight			=	$p["processBasketWt"];
		$arrivalOption		=	$p["available"];
		if ($arrivalOption =='G' || $arrivalOption =='B' ) {
			//$gradeId		=	$p["selGrade"];
			$gradeId		=	explode(",",$p["selRawGrade"]);
		}
					
		$frozenAvailable	=	$p["frozenAvailable"];
		
		if ($frozenAvailable =='G' || $frozenAvailable =='B') {
			//$gradeFrozenId		=	$p["selGradeFrozen"];
			$gradeFrozenId		=	explode(",",$p["selFrozenGrade"]);
		}
			
		$rawGradeUnit		=	($p["gradeUnitRaw"]=="")?0:$p["gradeUnitRaw"];
		$rawCountUnit		=	($p["countUnitRaw"]=="")?0:$p["countUnitRaw"];
		$frozenGradeUnit	=	($p["gradeUnitFrozen"]=="")?0:$p["gradeUnitFrozen"];
		$frozenCountUnit	=	($p["countUnitFrozen"]=="")?0:$p["countUnitFrozen"];
		
		$hidStage			=	$p["hidStage"];
		$hidProcessCode		=	trim($p["hidProcessCode"]);
		
		if($hidProcessCode!=$Code){
			#For Checking Unique Record based on Fish & Code & quality
			$uniqueRecords		=	$processcodeObj->fetchAllUniqueRecords($fishId,$Code);	
		}
		
		if ($Code!="" && $arrivalOption!="" && sizeof($uniqueRecords)==0 ) {
			$processCodeRecUptd =	$processcodeObj->updateProcessCode($processCodeId, $fishId, $Code, $Descr, $Weight, $gradeId, $arrivalOption, $gradeFrozenId, $frozenAvailable, $rawGradeUnit, $rawCountUnit, $frozenGradeUnit, $frozenCountUnit, $selStage);
		}
	
		if ($processCodeRecUptd) {
			$editMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succProcessCodeUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProcessCode.$selCriteria);
			$recUpdated = true;
		} else {
			$editMode	=	true;
			$err		=	$msg_failProcessCodeUpdate;
		}
		$processCodeRecUptd	=	false;
	}
		
	# Edit a Process Code	
	if (($p["editId"]!="" || $selProcessCodeId!="") && $p["cmdCancel"]=="") {

		if ($selProcessCodeId=="") $editId = $p["editId"];
		else $editId = $selProcessCodeId;

		$editMode			=	true;
		$processCodeRec		=	$processcodeObj->find($editId);
		
		$processCodeId		=	$processCodeRec[0];
		$processFishId		=	$processCodeRec[1];
		$processCode		=	stripSlash($processCodeRec[2]);
		$processCodeDescr	=	stripSlash($processCodeRec[3]);
		$BasketWeight		=	$processCodeRec[4];
		$rawGradeUnit		=	$processCodeRec[5];
		$frozenGradeUnit	=	$processCodeRec[6];
		$available		=	$processCodeRec[7];
		$frozenAvailable	=	$processCodeRec[10];
		
		$rawCountUnit		=	$processCodeRec[11];
		$frozenCountUnit	=	$processCodeRec[12];		
	
		if ($available=='G') {
				$gradeUnitRawRecords	= $unitmasterObj->filterRecords($available);			
		}
		
		if($available=='C'){
				$countUnitRawRecords	= $unitmasterObj->filterRecords($available);			
		}
		if($available=='B'){
			$gradeUnitRawRecords	= $unitmasterObj->filterRecords('G');
			$countUnitRawRecords	= $unitmasterObj->filterRecords('C');
		}

		if ($frozenAvailable=='G') {
			$gradeUnitFrozenRecords = $unitmasterObj->filterRecords($frozenAvailable);
		}
		if ($frozenAvailable=='C') {
			$countUnitFrozenRecords		=	$unitmasterObj->filterRecords($frozenAvailable);
		}
		if ($frozenAvailable=='B') {
			$gradeUnitFrozenRecords		=	$unitmasterObj->filterRecords('G');
			$countUnitFrozenRecords		=	$unitmasterObj->filterRecords('C');
		}
 	}	

	# Delete a Process Code
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processCodeId	=	$p["delId_".$i];

			if ($processCodeId!="") {
				// checking the selected Proces Code Id is link with any other process
				$processCodeRecInUse = $processcodeObj->processCodeRecInUse($processCodeId);
				
				if (!$processCodeRecInUse) {
					$processCode2GradeDel = $processcodeObj->deleteProcessCode2Grade($processCodeId);
					# Checking any entry exist
					$chkMoreEntryExist = $processcodeObj->chkMoreGradeEntryExist($processCodeId);
					if (!$chkMoreEntryExist) $processcodeRecDel = $processcodeObj->deleteProcessCode($processCodeId);
				}
			}
		}
		if ($processcodeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProcessCode);
			$sessObj->createSession("nextPage",$url_afterDelProcessCode.$selCriteria);
			$recUpdated = true;
		} else {
			$errDel	=	$msg_failDelProcessCode;
		}
		$processcodeRecDel	=	false;
	}
	


	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processCodeId	=	$p["confirmId"];
			if ($processCodeId!="") {
				// Checking the selected fish is link with any other process
				$processCodeRecConfirm = $processcodeObj->updateProcessCodeconfirm($processCodeId);
			}

		}
		if ($processCodeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmprocessCode);
			$sessObj->createSession("nextPage",$url_afterDelProcessCode.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$processCodeId = $p["confirmId"];

			if ($processCodeId!="") {
				#Check any entries exist
				
					$processCodeRecConfirm = $processcodeObj->updateProcessCodeReleaseconfirm($processCodeId);
				
			}
		}
		if ($processCodeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmprocessCode);
			$sessObj->createSession("nextPage",$url_afterDelProcessCode.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# List all Process Code 

	if ($g["selFilter"]!="") $recordsFilterId = $g["selFilter"];
	else $recordsFilterId		=	$p["selFilter"];
	
	#Condition for Select a Fish 	
	if ($p["existRecordsFilterId"]==0 && $p["selFilter"]!=0) {
		$offset = 0;
		$pageNo = 1;
	}
		

	$processCodeRecords	= $processcodeObj->fetchAllPagingRecords($offset, $limit, $recordsFilterId);
	$numrows		= sizeof($processcodeObj->fetchAllRecords($recordsFilterId));
	$processCodeSize	= sizeof($processCodeRecords);

	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List All Fishes
	$fishMasterRecords	=	$fishmasterObj->fetchAllRecordsFishactive();
	
	#Return  all grade master records 
	/*
	if ($addMode==true) {
		$gradeMasterRecords	= $grademasterObj->fetchAllRecords();
		$frozenGradeRecords	= $grademasterObj->fetchAllRecords();
	} else {
		$gradeMasterRecords	= $processcodeObj->fetchGradeSelectedRecords($editId);
		$frozenGradeRecords	= $processcodeObj->fetchFrozenGradeSelectedRecords($editId);
	}
	*/

	if ($addMode || $editMode) {
		# For test
		$gradeFullRecs 	= $grademasterObj->fetchAllRecordsGradeActive();
		$selGradeRecs 	= array();
		$selFrozenGradeRecs =array();
		if ($editMode) {
			$selGradeRecs		= $processcodeObj->getSelGradeRecs($editId);
			$selFrozenGradeRecs	= $processcodeObj->getSelFrozenGradeRecs($editId);
		}
		$gradeMasterRecords = ary_diff($gradeFullRecs, $selGradeRecs);
		$frozenGradeRecords = ary_diff($gradeFullRecs, $selFrozenGradeRecs);
	}
	
	
	#For filter unit based on availble
	if ($p["available"]!="") {
		$available=$p["available"];
		if ($available=='G') {
			$gradeUnitRawRecords = $unitmasterObj->filterRecords($available);			
		}
		
		if ($available=='C'){
			$countUnitRawRecords = $unitmasterObj->filterRecords($available);			
		}
		if ($available=='B') {
			$gradeUnitRawRecords	=	$unitmasterObj->filterRecords('G');
			$countUnitRawRecords	=	$unitmasterObj->filterRecords('C');
		}
	}

	if ($p["frozenAvailable"]!="") {
		$frozenAvailable		=	$p["frozenAvailable"];
		if ($frozenAvailable=='G') { 
			$gradeUnitFrozenRecords		=	$unitmasterObj->filterRecords($frozenAvailable);
		}
		if ($frozenAvailable=='C') {
			$countUnitFrozenRecords		=	$unitmasterObj->filterRecords($frozenAvailable);
		}
		if ($frozenAvailable=='B') {
			$gradeUnitFrozenRecords		=	$unitmasterObj->filterRecords('G');
			$countUnitFrozenRecords		=	$unitmasterObj->filterRecords('C');
		}			
	} else {
		if ($addMode==true) {
			$frozenAvailable		=	'G';
			$gradeUnitFrozenRecords		=	$unitmasterObj->filterRecords($frozenAvailable);
		}
	}

	# Resetting values while posting
	if ($addMode==true || $editMode) {
		//$gradeId		=	$p["selGrade"];
		//$gradeFrozenId	=	$p["selGradeFrozen"];
		if ($p["selRawGrade"]!="") {
			$selRawGradeId	 =  trim($p["selRawGrade"]); // Comma seperated values
			$selGradeRecs = $processcodeObj->selGradeRecs($selRawGradeId);
			$gradeMasterRecords = ary_diff($gradeFullRecs, $selGradeRecs);
		
		}
		if ($p["selFrozenGrade"]!="") {
			$selFrznGradeId	 =  trim($p["selFrozenGrade"]); // Comma seperated values
			$selFrozenGradeRecs = $processcodeObj->selGradeRecs($selFrznGradeId);
			$frozenGradeRecords = ary_diff($gradeFullRecs, $selFrozenGradeRecs);
		}
	}
	
	if ($editMode)	$heading	=	$label_editProcessCode;
	else 		$heading	=	$label_addProcessCode;
	
	$help_lnk="help/hlp_ProcessCodeMaster.html";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	
	$ON_LOAD_PRINT_JS	= "libjs/processcode.js";

	# Include Template [topLeftNav.php]
	//require("template/topLeftNav.php");	
	if (!$popupWindow) require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmProcessCode" action="ProcessCode.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" style="padding-left:15px; padding-right:15px;">
	<tr>
		<td height="10" align="center"><a href="Processes.php" class="link1">Process Master</a></td>
	</tr>
		<? if($err!="" ){?>
		<tr>
			<td  align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		
			<tr>
				<td height="10" align="center"></td>
			</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Process Code Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
	<table width="60%" align="center">
	<?php
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>-->
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="96%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="3" height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td width="88%" colspan="3" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCode.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateEditProcessCode(document.frmProcessCode);"> 
                        </td>
                        <?} else{?>
                        <td width="12%"  colspan="3" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCode.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAddProcessCode" class="button" value=" Add " onClick="return validateAddProcessCode(document.frmProcessCode);"> 
                        </td>
                        <?}?>
                      </tr>
                      <input type="hidden" name="hidProcessCodeId" id="hidProcessCodeId" value="<?=$processCodeId;?>">
			  <input type="hidden" name="hidStage" value="<?=$editStage?>">
			  <input type="hidden" name="hidProcessCode" value="<?=$processCode?>" />
			<tr> 
                            <td colspan="3"  align="center" height="5">&nbsp;</td>
                          </tr>
                      <tr> 
                        <td colspan="3" nowrap class="fieldName" align="center" >
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
                            <td  valign="top" align="center" >
				<table align="center" cellpadding="0" cellspacing="0">
					<tr> 
                            <td nowrap class="fieldName" >
				<input type="hidden" name="hidAvailable" value="<?=$available?>">
				<input type="hidden" name="hidFrozenAvailable" value="<?=$frozenAvailable?>">*Fish </td>
                            <td>
				<? $fishSelId=$p["processCodeFish"];?>
				<select name="processCodeFish">
                                <option value="">--Select--</option>
                                <?php
					if (sizeof($fishMasterRecords)>0 ) {
						foreach ($fishMasterRecords as $fl) {
							$fishId		=	$fl[0];
							$fishName	=	$fl[1];
							$selected	=	"";
							if ($fishId == $processFishId || $fishSelId==$fishId) {
								$selected	=	"selected";
							}
				?>
                                <option value="<?=$fishId;?>" <?=$selected;?> ><?=$fishName;?></option>
                                <?php
						}
					}
				?>
                              </select></td>
                          </tr>
				<tr> 
                            <td class="fieldName" nowrap >*Process Code </td>
                            <td> 
				<table cellpadding="0" cellspacing="0" align="left" width="100%">
                                <tr> 
                                  <td>
					  <? if($addMode==true ) {$processCode = $p["processCode"]; }?>
					  <input type="text" name="processCode" size="10" value="<?=$processCode;?>">
				</td>
                                </tr>
                              </table></td>
                          </tr>
				<tr>
				<TD class="fieldName" nowrap>Process Description</TD>
				<td nowrap>
					<?  if($p["processCodeDescr"]!="") $processCodeDescr = $p["processCodeDescr"]; ?>
					 <textarea name="processCodeDescr" cols="15" rows="2"><?=$processCodeDescr;?></textarea>     
				</td>
			</tr>
			<? if($addMode==true){?>
                          <tr>
                            <td  height="10" class="fieldName">*Copy From</td>
                            <td  height="10" colspan="1" nowrap><table width="200" border="0">
                              <tr>
                                <td>
								<? 
									$copyFromId		=	$p["selCopyFrom"];
									$copyProcessCodeRecords		=	$processcodeObj->processCodeRecFilter($copyFromId);
								?>
                                 <!-- <select name="selCopyFrom" id="selCopyFrom" onchange=" this.form.submit(); disable(document.frmProcessCode);" style="width:150;">-->

								 <select name="selCopyFrom" id="selCopyFrom" onchange="processCodeLoad(this); disable(document.frmProcessCode);" style="width:150;">
                                    <option value="">Select Fish </option>
                                    <?php
					if (sizeof($fishMasterRecords)>0) {
						foreach ($fishMasterRecords as $fl) {
							$id		=	$fl[0];
							$name		=	$fl[1];
							$selected	= 	"";
							if ($copyFromId == $id) {
								$selected	=	" selected ";
							}
					?>
                                    <option value="<?=$id;?>" <?=$selected;?>><?=$name;?></option>
                               		<?php
						}
					}
					?>
                                  </select></td>
                                <td class="fieldName" nowrap="nowrap">Process Code</td>
                                <td><select name="selProcessCode">
                                  <option value="">--Select--</option>
                                 <?php
					if (sizeof($copyProcessCodeRecords)>0 ) {
						foreach ($copyProcessCodeRecords as $fl) {
							$processCodeId		=	$fl[0];
							$processCode		=	$fl[2];
							$selected	=	"";
							if ($recordProcessCode == $processCodeId || $processId==$processCodeId) {
								$selected	=	"selected";
							}
				?>
                                  <option value="<?=$processCode;?>" <?=$selected;?> ><?=$processCode;?></option>
				<?php
						}
					}
				?>
                                </select></td>
                              </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td  height="10" class="fieldName" >&nbsp;</td>
                            <td  height="10" nowrap class="listing-item">[OR]</td>
                          </tr>
			  <? }?>
			<tr> 
                            <td class="fieldName" >*Basket Wt</td>
                            <td nowrap>
				<span class="listing-item"> 
				<? if($addMode==true ) { $BasketWeight	=	$p["processBasketWt"];}?>
                              <input type="text" name="processBasketWt"  id="processBasketWt" size="3" value="<?=$BasketWeight;?>" style="text-align:right" />
                              Kg</span>
			   </td>
                          </tr>
				</table>
			    </td>
			</tr>
			<?php 
				if ($editMode) {
			?>
			<tr><TD align="right" class="listing-item" style="line-height:normal; font-size:9px;"><span style="color:red">Red Color</span> indicates grade already in use.</TD></tr>
			<?php 
				} 
			?>
			<tr>
                            <td valign="top" align="center" >
				<table align="center" cellpadding="4" cellspacing="0">
					<TR>
						<TD valign="top">
						<table width="200">
                              <tr>
                                <td>
					<!--<fieldset class="fieldName"><legend>Raw</legend>-->
					<?php			
						$entryHead = "Raw";
						require("template/rbTop.php");
					?>
					<table>
								<tr>
								  <td class="fieldName" nowrap="nowrap">*Received By</td>
								  <td><? if($addMode==true){?>
							<select name="available" onChange="this.form.submit();">
							<? } else {?>
							<select name="available" onChange="this.form.editId.value=<?=$editId?>;this.form.submit();">
							<? }?>
                                <option value="">--select--</option>
                                <option value="G" <? if($p["available"]=='G' || $available=='G') echo "selected";?>>Grade</option>
                                <option value="C" <? if($p["available"]=='C' || $available=='C') echo "selected";?>>Count</option>
								<option value="B" <? if($p["available"]=='B' || $available=='B') echo "selected";?>>Both</option>
                              </select></td>
								  </tr>
			  	<? if($p["available"]=='G'|| $available=='G' || $p["available"]=='B' || $available=='B') {?>
				<tr>
					<td class="fieldName">*Grade</td>
					<td>
					<table>
						<TR>
							<TD>
				<select name="selFullGrade[]" size="7" multiple id="selFullGrade">
                                <option value="" >Select Grade </option>
                                <?php
				if (sizeof($gradeMasterRecords)> 0) {
					foreach ($gradeMasterRecords as $gl) {
						$id		= $gl[0];
						$displayGrade	= $gl[1];
						$selected	= "";
						/*
						$recordGradeId	= $gl[4];	
						if ($gradeCodeId== $id || $recordGradeId == $id) {
							$selected	=	" selected ";
						}
						$grade ="";
						foreach ($gradeId as $gId) {
							$grade	=	"$gId";
							if ( strstr($grade,"$gl[0]") ) $selected	=	" selected ";
						}
						*/
				?>
                                <option value="<?=$id;?>" <?=$selected;?> ><?=$displayGrade;?></option>
                                <?php
				  	}
				}
			  	?>
                              </select>
							</TD>
							<TD>
							<table>
								<TR><TD>
									<input type="button" value="Add All" onclick="addAll(document.getElementById('selFullGrade'), document.getElementById('selGrade'), 'R');" title="Add All" style="width:70px;"/>
								</TD></TR>
								<TR><TD><input type="button" value="Add" onclick="addAttribute(document.getElementById('selFullGrade'), document.getElementById('selGrade'), 'R');" title="Add one by one" style="width:70px;"/></TD></TR>
								<TR><TD></TD></TR>
								<TR><TD><input type="button" value="Remove" onclick="delAttribute(document.getElementById('selFullGrade'), document.getElementById('selGrade'), 'R');" title="Delete one by one" style="width:70px;"/></TD></TR>
								<TR><TD><input type="button" value="Remove All" onclick="delAll(document.getElementById('selFullGrade'), document.getElementById('selGrade'), 'R');" title="Delete All" style="width:70px;"/></TD></TR>
							</table>
							</TD>
							<TD>
				<select name="selGrade[]" size="7" multiple id="selGrade">
                                	<option value="" >Active Grade </option>
					<?php
					$sRawGrade = array();
					$sr = 0;
					foreach ($selGradeRecs as $gl) {
						$selGrId = $gl[0];
						$selGradeDisplay = $gl[1];
						$sRawGrade[$sr] = $selGrId;
						
						if ($processCodeId) {
							$style = "";
							$chkRecExist = $processcodeObj->pcGradeRecInUse($processCodeId, $selGrId);
							if ($chkRecExist) $style = "style='color:red'";
		
						}

				?>
                                <option value="<?=$selGrId;?>" <?=$style?>><?=$selGradeDisplay;?></option>
				<?php 
					$sr++;
					}
				?>
                              	</select>
				<input type="hidden" name="selRawGrade" id="selRawGrade" value="<?=implode(",",$sRawGrade);?>" />
				</TD>
				</TR>
				</table>				
				</td>
				</tr>
				<!--<tr> Original
					<td class="fieldName">*Grade</td>
					<td>
				<select name="selGrade[]" size="7" multiple id="selGrade">
                                <option value="" > Select Grade </option>
                                <?php
				/*	
				if (sizeof($gradeMasterRecords)> 0) {
					foreach ($gradeMasterRecords as $gl) {
						$id		= $gl[0];
						$displayGrade	= $gl[1];
						$recordGradeId	= $gl[4];	
						$selected	= "";
						if ($gradeCodeId== $id || $recordGradeId == $id) {
							$selected	=	" selected ";
						}
						$grade ="";
						foreach ($gradeId as $gId) {
							$grade	=	"$gId";
							if ( strstr($grade,"$gl[0]") ) $selected	=	" selected ";
						}
				*/
				?>
                                <option value="<?=$id;?>" <?=$selected;?> > 
                                <?=$displayGrade;?>
                                </option>
                                <?php
				/*
				  	}
				}
				*/
			  	?>
                              </select>
				</td>
				</tr>-->
								<? }?>
								 <? if($p["available"]=='G'|| $available=='G' || $p["available"]=='B' || $available=='B') {?>
								<tr>
									<td class="fieldName">* Grade Unit </td>
									<td>
									<? if($addMode==true ) {$rawGradeUnit=$p["gradeUnitRaw"];}?>
									<select name="gradeUnitRaw" id="gradeUnitRaw">
                                <option value="">-- Select--</option>
                                <?
									foreach($gradeUnitRawRecords as $gurr)
													{
										$unitRawId			=	$gurr[0];
										$unitRaw			=	stripSlash($gurr[1]);
										$selected = "";
										if($unitRawId == $rawGradeUnit || $punitRawId==$unitRawId){
											$selected 	=	"Selected";
										}
										?>
                                <option value="<?=$unitRawId?>" <?=$selected?>> 
                                <?=$unitRaw?>
                                </option>
                                <? }?>
                              </select></td>
								</tr>
								<? }?>
								<? if($p["available"]=='C'|| $available=='C' || $p["available"]=='B' || $available=='B') {?>
								<tr>
								  <td class="fieldName">* Count Unit </td>
								  <td>
								  <? if($addMode==true ) {$rawCountUnit=$p["countUnitRaw"];}?>
								  <select name="countUnitRaw" id="countUnitRaw">
                                <option value="">-- Select--</option>
                                <?
										foreach($countUnitRawRecords as $curr)
													{
										$unitRawId			=	$curr[0];
										$unitRaw			=	stripSlash($curr[1]);
										$selected = "";
										if($unitRawId == $rawCountUnit || $punitRawId==$unitRawId){
											$selected 	=	"Selected";
										}
										?>
                                <option value="<?=$unitRawId?>" <?=$selected?>> 
                                <?=$unitRaw?>
                                </option>
                                <? }?>
                              </select></td>
								  </tr>
								  <? }?>
								</table>								
				<?php
					require("template/rbBottom.php");
				?>
				<!--</fieldset>-->
				</td>
                                </tr>
                            </table>
						</TD>
						<TD valign="top">
						<table width="200">
                              <tr>
                                <td>
					<!--<fieldset class="fieldName"><legend>Frozen</legend>-->
				<?php			
					$entryHead = "Frozen";
					require("template/rbTop.php");
				?>
								<table>
								<tr>
								  <td class="fieldName" nowrap>*Received By</td>
								  <td><? if($addMode==true){?>
							<select name="frozenAvailable" onChange="this.form.submit();">
							<? } else { ?>
							<select name="frozenAvailable" onChange="this.form.editId.value=<?=$editId?>;this.form.submit();">
							<? }?>
                                <option value="G" <? if($p["frozenAvailable"]=='G' || $frozenAvailable=='G') echo "selected";?>>Grade</option>
                                <option value="C" <? if($p["frozenAvailable"]=='C' || $frozenAvailable=='C') echo "selected";?>>Count</option>
								<option value="B" <? if($p["frozenAvailable"]=='B' || $frozenAvailable=='B') echo "selected";?>>Both</option>
                              </select></td>
								  </tr>
	<? if($p["frozenAvailable"]=='G'|| $frozenAvailable=='G' || $p["frozenAvailable"]=='B' || $frozenAvailable=='B') {?>
	<tr>
		<td class="fieldName">*Grade</td>
		<td>
		<table>			
						<TR>
							<TD>
				<select name="selFullGradeFrozen[]" size="7" multiple id="selFullGradeFrozen">
                <option value="">Select Grade</option>
                <?php
		if (sizeof($frozenGradeRecords)>0) {
			foreach ($frozenGradeRecords as $gl) {
				$id		=	$gl[0];
				$displayGrade	=	$gl[1];				
				$selected		=	"";			
		?>
                 <option value="<?=$id;?>" <?=$selected;?>><?=$displayGrade;?></option>
                  <?php
		  		} // Loop Ends here
			}
		  ?>
                  </select>
							</TD>
							<TD>
							<table>
								<TR><TD>
									<input type="button" value="Add All" onclick="addAll(document.getElementById('selFullGradeFrozen'), document.getElementById('selGradeFrozen'), 'F');" title="Add All" style="width:70px;" />
								</TD></TR>
								<TR><TD><input type="button" value="Add" onclick="addAttribute(document.getElementById('selFullGradeFrozen'), document.getElementById('selGradeFrozen'), 'F');" title="Add one by one" style="width:70px;"/></TD></TR>
								<TR><TD></TD></TR>
								<TR><TD><input type="button" value="Remove" onclick="delAttribute(document.getElementById('selFullGradeFrozen'), document.getElementById('selGradeFrozen'), 'F');" title="Delete one by one" style="width:70px;"/></TD></TR>
								<TR><TD><input type="button" value="Remove All" onclick="delAll(document.getElementById('selFullGradeFrozen'), document.getElementById('selGradeFrozen'), 'F');" title="Delete All" style="width:70px;"/></TD></TR>
							</table>
							</TD>
							<TD>
				<select name="selGradeFrozen[]" size="7" multiple id="selGradeFrozen">
                                	<option value="" >Active Grade</option>
					<?php
					$sFrznGrade = array();
					$sr = 0;
					foreach ($selFrozenGradeRecs as $gl) {
						$selFznGrId = $gl[0];
						$selFrznGrDisplay = $gl[1];
						$sFrznGrade[$sr] = $selFznGrId;
						if ($processCodeId) {
							$style = "";
							$chkRecExist = $processcodeObj->pcGradeRecInUse($processCodeId, $selFznGrId);
							if ($chkRecExist) $style = "style='color:red'";		
						}
				?>
                                <option value="<?=$selFznGrId;?>" <?=$style?>><?=$selFrznGrDisplay;?></option>
				<?php 
					$sr++;
					}
				?>
                              	</select>
				<input type="hidden" name="selFrozenGrade" id="selFrozenGrade" value="<?=implode(",",$sFrznGrade);?>" />
				</TD>
				</TR>
				</table>		
		</td>
		  </tr>
	<!--<tr>
		<td class="fieldName">* Grade</td>
		<td>
		<select name="selGradeFrozen[]" size="7" multiple id="selGradeFrozen">
                <option value="" > Select Grade </option>
                <?php
		/*
		if (sizeof($frozenGradeRecords)>0) {
			foreach ($frozenGradeRecords as $gl) {
				$id				=	$gl[0];
				$displayGrade	=	$gl[1];
				$recordGradeId	=	$gl[4];	
				$selected		=	"";
				if ($gradeCodeId== $id || $recordGradeId == $id) {
					$selected	=	" selected ";
				}
				$grade ="";
				foreach ($gradeFrozenId as $gFId) {
					$grade	=	"$gFId";
					if ( strstr($grade,"$gl[0]") ) $selected	=	" selected ";
			}
			*/
		?>
                 <option value="<?=$id;?>" <?=$selected;?>><?=$displayGrade;?></option>
                  <?php
			/*
		  		} // Loop Ends here
			}
			*/
		  ?>
                  </select>
		</td>
		  </tr>-->
		 <? }?>
		 <? if($p["frozenAvailable"]=='G'|| $frozenAvailable=='G' || $p["frozenAvailable"]=='B' || $frozenAvailable=='B') {?>
								<tr>
									<td class="fieldName">* Grade Unit </td>
									<td><? if($addMode==true ) {
									$frozenGradeUnit=$p["gradeUnitFrozen"];}?>
                                      <select name="gradeUnitFrozen" id="gradeUnitFrozen">
                                       <option value="">-- Select--</option>
                                <?
										foreach($gradeUnitFrozenRecords as $gufr)
												{
										$unitFrozenId		=	$gufr[0];
										$unitFrozen			=	stripSlash($gufr[1]);
										$selected = "";
										if($unitFrozenId == $frozenGradeUnit){
											$selected 	=	"Selected";
										}
										?>
                                <option value="<?=$unitFrozenId?>" <?=$selected?>> 
                                <?=$unitFrozen?>
                                </option>
                                <? }?>
                                      </select></td>
								</tr>
								<? }?>
								<? if($p["frozenAvailable"]=='C'|| $frozenAvailable=='C' || $p["frozenAvailable"]=='B' || $frozenAvailable=='B') {?>
								<tr>
								  <td class="fieldName">* Count Unit </td>
								  <td><? if($addMode==true ) {$frozenCountUnit=$p["countUnitFrozen"];}?>
                                      <select name="countUnitFrozen" id="countUnitFrozen">
                                       <option value="">-- Select--</option>
                                	<?
										foreach($countUnitFrozenRecords as $cufr)
												{
										$unitFrozenId		=	$cufr[0];
										$unitFrozen			=	stripSlash($cufr[1]);
										$selected = "";
										if($unitFrozenId == $frozenCountUnit){
											$selected 	=	"Selected";
										}
										?>
                                <option value="<?=$unitFrozenId?>" <?=$selected?>> 
                                <?=$unitFrozen?>
                                </option>
                                <? }?>
                                      </select></td>
								  </tr>
								  <? }?>
								</table>
				<?php
					require("template/rbBottom.php");
				?>
				<!--</fieldset>-->
				</td>
                                </tr>
                            </table>
						</TD>
					</TR>
				</table>
				</td>
			</tr>
                        </table></td>
                      </tr>
			<tr> 
                            <td colspan="3"  align="center" height="5">&nbsp;</td>
                          </tr>
                      <tr> 
                        <td  height="10" colspan="3" align="center" >
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr> 
                            <? if($editMode){?>
                            <td colspan="3" align="center" nowrap="nowrap"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCode.php');"> 
                              &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateEditProcessCode(document.frmProcessCode);">                            </td>
                            <?} else{?>
                            <td  colspan="3" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCode.php');"> 
                              &nbsp;&nbsp; <input type="submit" name="cmdAddProcessCode" class="button" value=" Add " onClick="return validateAddProcessCode(document.frmProcessCode);">                            </td>
                            <input type="hidden" name="cmdAddNew" value="1">
                            <?}?>
                          </tr>
                          <tr> 
                            <td colspan="3"  height="10" ></td>
                          </tr>
                        </table>									
                          </td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<?php
					require("template/rbBottom.php");
				?>
				<!-- Form fields end   -->	
		</td>
		</tr>	
		</table>
		</td>
		</tr>
		<?
			}
			
			# Listing Fish Starts
		?>
	</table>
									</td>
								</tr>
		<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<?php
		}
	?>
								<tr>
									<td colspan="3" align="center">
		<table width="20%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">	
				<tr>
					<td nowrap class="listing-item" align="right">Fish&nbsp;</td>
					<td nowrap align="center">
						<select name="selFilter" onChange="this.form.submit();">
						<option value="0">--Select All--</option>
						<?php
							if (sizeof($fishMasterRecords)>0) {
								foreach ($fishMasterRecords as $fl) {
									$fishId		=	$fl[0];
									$fishName	=	$fl[1];
									$selected	=	"";
									if ($fishId==$recordsFilterId) {
										$selected = "selected";
									}
						?>
						<option value="<?=$fishId;?>" <?=$selected;?> ><?=$fishName;?> </option>
						<?
								}
							}
						?>
						</select>
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
						<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="327" background="images/heading_bg.gif" class="pageName" >&nbsp;Process Code  Master</td>
								    <td background="images/heading_bg.gif" class="pageName" align="right">
										</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processCodeSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessCode.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="") {
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
										<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($processCodeRecords) > 0 )
												{
													$i	=	0;
											?>
		<thead>
											<? if($maxpage>1){?>
											<tr>
		<td colspan="13" style="padding-right:10px" class="navRow">
		<div align="right">
				  <?php 				 			  
				 $nav  = '';
		for($page=1; $page<=$maxpage; $page++)
			{
				if ($page==$pageNo)
   				{
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				}
   				else
   				{
      	$nav.= " <a href=\"ProcessCode.php?selFilter=$recordsFilterId&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"ProcessCode.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"ProcessCode.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
	<tr align="center">
		<th width="23" align="center"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th nowrap style="padding-left:5px; padding-right:5px;">Fish Name </th>
		<th nowrap style="padding-left:5px; padding-right:5px;"> Code</th>
		<th style="padding-left:5px; padding-right:5px;">Description</th>
		<th style="padding-left:5px; padding-right:5px;">Basket<br />Wt (Kg) </th>
		<th style="padding-left:5px; padding-right:5px;">Raw Received </th>
		<th style="padding-left:5px; padding-right:5px;">Unit of Raw </th>
		<th style="padding-left:5px; padding-right:5px;">Raw Grades</th>
		<th style="padding-left:5px; padding-right:5px;">Frozen Received </th>
		<th style="padding-left:5px; padding-right:5px;">Unit of Frozen </th>
		<th style="padding-left:5px; padding-right:5px;">Frozen Grades </th>
		<? if($edit==true){?>
		<th width="60">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th width="60">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($processCodeRecords as $fr) {
		$i++;
		$codeId		= $fr[0];
		$Code		= stripSlash($fr[2]);
		$displayCode	= $Code;
		$Descr		=	stripSlash($fr[3]);
		$fishName	=	stripSlash($fr[9]);
		$basketWt	=	$fr[4];
		$availableFor	=	$fr[7];
		$active=$fr[13];
		$existingrecords=$fr[14];
		if ($availableFor=='G') {
			$displayAvailable	=	"Grade";
			$unitRawRec		=	$unitmasterObj->find($fr[5]);
			$displayUnitRaw		=	stripSlash($unitRawRec[1]);
		} else if ($availableFor=='C') {
			$displayAvailable	=	"Count";
			$unitCountRec		=	$unitmasterObj->find($fr[12]);
			$displayUnitRaw		=	stripSlash($unitCountRec[1]);
		} else if ($availableFor=='B') {
			$displayAvailable	=	"Grade/Count";
			$unitRawRec		=	$unitmasterObj->find($fr[5]);								
			$unitCountRec		=	$unitmasterObj->find($fr[12]);
			$displayUnitRaw	=	$unitRawRec[1]."/".$unitCountRec[1];
		} else {
			$displayAvailable	=	"";
			$unitRaw		=	"";
			$displayUnitRaw		=	"";
		}
		$frozenAvailable	=	$fr[10];
		//$stage			=	$fr[11];
		if ($frozenAvailable=='G') {
			$displayFrozenAvailable	=	"Grade";
			$unitFrozenGradeRec	=	$unitmasterObj->find($fr[11]);
			$displayUnitFrozen		=	stripSlash($unitFrozenGradeRec[1]);
		} else if ($frozenAvailable=='C') { 
			$displayFrozenAvailable	=	"Count";
			$unitFrozenCountRec	=	$unitmasterObj->find($fr[6]);
			$displayUnitFrozen		=	stripSlash($unitFrozenCountRec[1]);
		} else if($frozenAvailable=='B') {
			$displayFrozenAvailable	=	"Grade/Count";
			$unitFrozenGradeRec	=	$unitmasterObj->find($fr[11]);
			$unitFrozenCountRec	=	$unitmasterObj->find($fr[6]);
			$displayUnitFrozen		=	$unitFrozenGradeRec[1]."/".$unitFrozenCountRec[1];
		} else {
			$displayFrozenAvailable	=	"";
			$displayUnitFrozen	=	"";
		}

		#Find the Grade from The procescode2grade TABLE
		 $gradeRecords	= $processcodeObj->fetchGradeRecords($codeId);
		#Find FROZEN Grade from The procescode2grade TABLE
		$frozenGradeRecords	= $processcodeObj->fetchFrozenGradeRecords($codeId);
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();"  <?php }?> >
		<td width="23" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$codeId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$displayCode;?>&nbsp;</td>
		<td class="listing-item" align="left" style="padding-left:5px; padding-right:5px; line-height:normal;"><?=$Descr;?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$basketWt?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$displayAvailable?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$displayUnitRaw?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
				<?php
					$GradeLine = 3;
					if (sizeof($gradeRecords)>0) {
						$nextRec	=	0;						
						$selName = "";
						foreach ($gradeRecords as $gradeR) {							
							$selName = $gradeR[4];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$GradeLine == 0) {
								echo "<br/>";
							}
						}
					}
				?>				
				</td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$displayFrozenAvailable;?></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$displayUnitFrozen?></td>
			  	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
				<?php
					$frozenLine = 3;
					if (sizeof($frozenGradeRecords)>0) {
						$nextRec	=	0;						
						$selName = "";
						foreach ($frozenGradeRecords as $gradeF) {							
							$selName = $gradeF[4];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$frozenLine == 0) {
								echo "<br/>";
							}
						}
					}
				?>
				</td>
				<? if($edit==true){?>
				<td class="listing-item" align="center" style="padding-left:5px;padding-right:5px;">
					<?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$codeId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='ProcessCode.php';"><? } ?>
				</td>
				<? }?>

				 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$codeId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==1) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$codeId;?>,'confirmId');" >
			<?php } }?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?php
			}
	?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="confirmId" value="">
											<input type="hidden" name="editSelectionChange" value="0">
											<? if($maxpage>1){?>
											<tr>
		<td colspan="13" style="padding-right:10px" class="navRow">
		<div align="right">
				  <?php 				 			  
				 $nav  = '';
		for($page=1; $page<=$maxpage; $page++)
			{
				if ($page==$pageNo)
   				{
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				}
   				else
   				{
      	$nav.= " <a href=\"ProcessCode.php?selFilter=$recordsFilterId&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"ProcessCode.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"ProcessCode.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
	</tbody>
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processCodeSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessCode.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>	
					<?php
						include "template/boxBR.php"
					?>	
					</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"><input type="hidden" name="popupWindow" id="popupWindow" value="<?=$popupWindow?>"></td>
		</tr>
		<tr>
		  <td height="10" align="center"><a href="Processes.php" class="link1">Process Master</a></td>
	  </tr>	
	</table>
	<? if ( $addMode == true && $copyFromId!="" ){?>
	<script language="javascript">
		disable(document.frmProcessCode);
	</script>
	<? }?>	
	<?php 
		if ($recUpdated && $popupWindow!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		// Shipment purchase order FG: Frozen Grade
		parent.reloadDropDownList('FG');	
	</script>
	<?php
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if (!$popupWindow) require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>