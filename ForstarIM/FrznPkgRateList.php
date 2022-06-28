<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
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
	/*-----------------------------------------------------------*/


	# Add Employee Master Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Employee Master
	if ($p["cmdAdd"]!="") 
	{

		$name		=	addSlash(trim($p["name"]));
		$startDate	=	mysqlDateFormat($p["startDate"]);
		$copyRateList		=$p["copyRateList"];
		
		if ($name!="") 
		{
			$vaildDateEntry	=$frozenPackingRateListObj->chkValidDateEntry($startDate); 
			//echo $vaildDateEntry;
			//die();
			if($vaildDateEntry)
			{
				$FrozenPackingRateListRecIns	=	$frozenPackingRateListObj->addFrozenPackingRateList($name, $startDate,$userId);
				if ($FrozenPackingRateListRecIns) 
				{
					# Update Prev Rate List Rec END DATE
					$sDate		= explode("-",$startDate);
					$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
					$lastRateListId =$frozenPackingRateListObj->getFrznPkgRateList($endDate);
					if ($lastRateListId!=0) 
					{
						$updateRateListEndDate = $frozenPackingRateListObj->updateRateListRec($lastRateListId, $endDate);
					}	
				}

				if($copyRateList!='')
				{
					$rateListId =$frozenPackingRateListObj->latestRateList();
					//die();
					$frznPkgMainRecs =$frozenPackingRateListObj->getRate($copyRateList);
					foreach($frznPkgMainRecs as $frznPkg)
					{	$frznPkgId=$frznPkg[0];
						$fishId=$frznPkg[1];
						$processCodeId=$frznPkg[2];
						$freezingStageId=$frznPkg[3];
						$qualityId=$frznPkg[4];
						$frozenCodeId=$frznPkg[5];
						$defaultRate=$frznPkg[6];
						$insFrznPkg=$frozenPackingRateListObj->insFrozenPkgRate($fishId,$processCodeId,$freezingStageId,$qualityId,$frozenCodeId,$defaultRate,$rateListId);
						$lastId = $databaseConnect->getLastInsertedId();
						$frznPkgRateGradeMainRecs =$frozenPackingRateListObj->getPkgRateGrade($frznPkgId);
						foreach($frznPkgRateGradeMainRecs as $frznPkgRateGrade)
						{	
							$pkgRateEntryId=$lastId;
							$gradeId=$frznPkgRateGrade[2];
							$rate=$frznPkgRateGrade[3];
							$preProcessorId=$frznPkgRateGrade[4];
							$insFrznPkgRate=$frozenPackingRateListObj->insFrozenPkgRateGrade($pkgRateEntryId,$gradeId,$rate,$preProcessorId);
						}
						//$rateListId=
					}
				}
			}

			if ($FrozenPackingRateListRecIns) 
			{
				$sessObj->createSession("displayMsg", $msg_succAddFrozenPackingRateList);
				$sessObj->createSession("nextPage", $url_afterAddFrozenPackingRateList.$selection);
			}
			else 
			{
				$addMode	=	true;
				$err		=	$msg_failAddFrozenPackingRateList;
			}
			$FrozenPackingRateListRecIns		=	false;
		}
	}
		
	# Edit Employee Master 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$FrozenPackingRateListRec		=	$frozenPackingRateListObj->find($editId);
		$FrozenPackingRateListId			=	$FrozenPackingRateListRec[0];
		$name			=	stripSlash($FrozenPackingRateListRec[1]);
		$startDate			=dateFormat($FrozenPackingRateListRec[2]);
		
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$FrozenPackingRateListId		=	$p["hidFrozenPackingRateListId"];
		$name		=	addSlash(trim($p["name"]));
		$startDate	=	mysqlDateFormat($p["startDate"]);
		
		if ($FrozenPackingRateListId!="" && $name!="") {
			$vaildDateEntry	=$frozenPackingRateListObj->chkValidDateEntry($startDate,$FrozenPackingRateListId); 
			if($vaildDateEntry)
			{
				$FrozenPackingRateListRecUptd = $frozenPackingRateListObj->updateFrozenPackingRateList($FrozenPackingRateListId, $name, $startDate);
			}
		}
	
		if ($FrozenPackingRateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succFrozenPackingRateListUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateFrozenPackingRateList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failFrozenPackingRateListUpdate;
		}
		$FrozenPackingRateListRecUptd	=	false;
	}


	# Delete Employee Master
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		$recInUse = false;
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$frozenPackingRateListId	=	$p["delId_".$i];

			if ($frozenPackingRateListId!="") 
			{
				$frozenRateExist =	$frozenPackingRateListObj->frozenPackingRateInUse($frozenPackingRateListId);
				if(!$frozenRateExist)
				{
					$frozenPackingRateListRecDel =	$frozenPackingRateListObj->deleteFrozenPackingRateList($frozenPackingRateListId);
				}
			}
		}
		if ($frozenPackingRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelFrozenPackingRateList);
			$sessObj->createSession("nextPage",$url_afterDelFrozenPackingRateList.$selection);
		} else {
			if ($recInUse) $errDel	=	$msg_failDelFrozenPackingRateListInUse;
			else $errDel	=	$msg_failDelFrozenPackingRateList;
		}
		$FrozenPackingRateListRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$FrozenPackingRateListId	=	$p["confirmId"];
			if ($FrozenPackingRateListId!="") {
				// Checking the selected fish is link with any other process
				$FrozenPackingRateListRecConfirm = $frozenPackingRateListObj->updateFrozenPackingRateListconfirm($FrozenPackingRateListId);
			}

		}
		if ($FrozenPackingRateListRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmFrozenPackingRateList);
			$sessObj->createSession("nextPage",$url_afterDelFrozenPackingRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


if ($p["btnRlConfirm"]!="")
{
	
	$rowCount	=	$p["hidRowCount"];
	for ($i=1; $i<=$rowCount; $i++) 
	{

		$FrozenPackingRateListId = $p["confirmId"];
		if ($FrozenPackingRateListId!="") 
			{
				#Check any entries exist
				$FrozenPackingRateListRecConfirm = $frozenPackingRateListObj->updateFrozenPackingRateListReleaseconfirm($FrozenPackingRateListId);
			}
		}
		if ($FrozenPackingRateListRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmFrozenPackingRateList);
			$sessObj->createSession("nextPage",$url_afterDelFrozenPackingRateList.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Employee Master ;
	$frozenPakingRateList	=	$frozenPackingRateListObj->fetchAllPagingRecords($offset, $limit);
	$frozenPakingRateListSize		=	sizeof($frozenPakingRateList);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($frozenPackingRateListObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editFrozenPackingRateList;
	else 		$heading = $label_addFrozenPackingRateList;
	
	$ON_LOAD_PRINT_JS	= "libjs/FrozenPackingRateList.js";
	
	
	# Get all department Recs
	$copyRateList = $frozenPackingRateListObj->RateList();
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmFrznPkgRateList" action="FrznPkgRateList.php" method="post" id='frmFrznPkgRateList'>
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
	<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "FROZEN PACKING RATE LIST";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
			if ( $editMode || $addMode) {
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrznPkgRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFrozenPackingRateList(document.frmFrznPkgRateList);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrznPkgRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFrozenPackingRateList(document.frmFrznPkgRateList);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidFrozenPackingRateListId" value="<?=$FrozenPackingRateListId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
								<td class="fieldName" nowrap="nowrap">*Name</td>
								<td class="listing-item">
									<input name="name" type="text" id="name" size="28" value="<?=$name?>" onblur="xajax_chkRecExist(document.getElementById('name').value, '<?=$editId?>');" autocomplete="off">
									<? if($editMode) {?>
									<input type="hidden" name="id" value="" readonly>
									<? } ?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >*Start Date </td>
								<td>
									<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate?>" size="8" autocomplete="off">
									<input type="hidden" name="hidStartDate" id="hidStartDate" value="" readonly>
								</td>
							</tr>
							<? if($addMode){?>
							<tr >
								<td class="fieldName" nowrap >*Copy From</td>
								<td>
									<select name="copyRateList" id="copyRateList">
										<option value=''>--Select--</option>
										<?php 
										foreach($copyRateList as $cpr)
										{
										?>
										<option value='<?=$cpr[0]?>'><?=$cpr[1]?></option>
										<?php
										}
										?>
									</select>
								</td>

							</tr>
							<? } ?>
											
							<tr>
								<? if($editMode){?>
									<td colspan="2" align="center">
										<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrznPkgRateList.php');">&nbsp;&nbsp;
										<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFrozenPackingRateList(document.frmFrznPkgRateList);">												</td>
												
								<?} else{?>

									<td  colspan="2" align="center">
										<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrznPkgRateList.php');">&nbsp;&nbsp;
										<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFrozenPackingRateList(document.frmFrznPkgRateList);">												</td>

								<?}?>
							</tr>
							<tr>
								<td colspan="2"  height="10" ></td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Employee master Starts
		?>
	</table>
	</td>
	</tr>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Department </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
	<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$frozenPakingRateListSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintFrznPkgRateList.php',700,600);"><? }?></td>
											</tr>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
			if ( sizeof($frozenPakingRateList) > 0 ) {
				$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"FrznPkgRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FrznPkgRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FrznPkgRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
		<th nowrap  class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th nowrap  class="listing-head" style="padding-left:10px; padding-right:10px;">Start Date</th>	
		
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach($frozenPakingRateList as $cr) {
		$i++;
		 $frozenPakingRateListId		=	$cr[0];
		 $name		=	stripSlash($cr[1]);
		 $startDate	=	dateformat($cr[2]);
		 $active=$cr[3];
		 
		
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
		<?php 
		if ($existingrecords==0) {
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$frozenPakingRateListId;?>" >
		<?php 
		}
		?>
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$startDate;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) {
			?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$frozenPakingRateListId;?>,'editId'); this.form.action='FrznPkgRateList.php';"  >
		<?php } ?></td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$frozenPakingRateListId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$frozenPakingRateListId;?>,'confirmId');" >
			<?php //}

			} }?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"FrznPkgRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FrznPkgRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FrznPkgRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<?
		} else {
	?>
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</tbody>
	</table>	
								</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete ** " style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$frozenPakingRateListSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintFrznPkgRateList.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
			<td height="10"></td>
		</tr>
	</table>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>