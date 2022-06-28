<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	

	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	$printMode=false;
	
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
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------
	
	//if ($g["print"]=="y")
	//{
	//	$printMode=true;
	//}
	
	# Add Documentation instructions 
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAdd"]!="") {

		$accountType	=	addSlash(trim($p["cod"]));
		$reason = addSlash(trim($p["reason"]));
		$checkPoint=(isset($p["check_point"]))?$p["check_point"]:"N";
		$createdBY=$userId;
		$chkEntryExist=false;
		$tblRowCount=$p[hidTableRowCount];
		
		if ($accountType!="" && $reason!="" && $checkPoint!="" && $createdBY!="") {
			$chkEntryExist = $commonReasonObj->chkReasonEntryExist($accountType, $reason,$createdBY);
			if(!$chkEntryExist)
				{
					$commonReasonRecIns	= $commonReasonObj->addCommonReason($accountType,$reason,$checkPoint,$userId);
				}
				
			if ($commonReasonRecIns && $checkPoint=="Y") {
			$checkListId = $databaseConnect->getLastInsertedId();
			if($tblRowCount>0)
				{
					for($i=0;$i < $tblRowCount; $i++)
						{
					
						$chkListName	= trim($p["chkListName_".$i]);		
						$required	= ($p["required_".$i]!="")?$p["required_".$i]:'N';
						if ($chkListName!="") 
							{
								$ChkListRecIns = $commonReasonObj->addChecklistRecord($checkListId, $chkListName, $required);				
							}
				
						}
				}
		
			}	
		}
		if ($commonReasonRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddCommonReason);
				$sessObj->createSession("nextPage",$url_afterAddCommonReason.$selection);
			} else {
				$addMode	=	true;
				if($chkEntryExist)
						{
							$err=$msg_AddCommonReasontExists;
						}
						else
						{
							$err		=	$msg_failCommonReasonUpdate;
						}
			}
			$commonReasonRecIns		=	false;
	}
	
	
	# Edit Common Reason
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$commonReasonRec = $commonReasonObj->find($editId);
		//print_r($commonReasonRec);
		$cmnReasonId =	$commonReasonRec[0];
		
		$accountType = $codArr[$commonReasonRec[1]];
		$reason	=	stripSlash($commonReasonRec[2]);
		$checklistVal	=	stripSlash($commonReasonRec[3]);
		$checklistStatus = ($checklistVal=='Y')?"checked":"";
		$active = stripSlash($commonReasonRec[4]);
					
		$chkListRecs = array();
		if ($checklistVal=='Y') {
			//echo $cmnReasonId;
			$chkListRecs = $commonReasonObj->getChecklistRecords($cmnReasonId);
		}
	}
	
	if ($p["cmdSaveChange"]!="") {
	
		$cmnReasonId		=	$p["hidCommonReasonId"];
		$tblRowCount=$p["hidTableRowCount"];
		//echo $cmnReasonId;
		$chkListOldValue = $p["chkListValue"];
		$accountType	=	addSlash(trim($p["cod"]));
		$reason = addSlash(trim($p["reason"]));
		$checkPoint=(isset($p["check_point"]))?$p["check_point"]:"N";
		
	
		$chkEntryExist=false;
		$chkListRecInUse=false;
		if ($cmnReasonId!="" && $accountType!="" && $reason!="") {
			$chkEntryExist = $commonReasonObj->chkEntryExist($reason, $cmnReasonId);
			if(!$chkEntryExist)
			{
				$commonReasonRecUptd		=	$commonReasonObj->updateCommonReason($cmnReasonId,$accountType,$reason, $checkPoint);
			}
			
			if ($tblRowCount > 0) {		
				for ($i=0; $i < $tblRowCount; $i++) {
				$cmnReasonChkId	= $p["chkListEntryId_".$i];
						$status 	= $p["status_".$i];


						if ($status!='N') {
						$chkListName	= trim($p["chkListName_".$i]);		
						$required	= ($p["required_".$i]!="")?$p["required_".$i]:'N';
						if($chkListName!="" && $cmnReasonChkId=="")
						{
							$ChkListRecIns = $commonReasonObj->addChecklistRecord($cmnReasonId, $chkListName, $required);
							//print_r($ChkListRecIns);
						}
						 if($chkListName!="" && $cmnReasonChkId!="")
						{
							$commonReasonChkListRecUptd = $commonReasonObj->updateCommonReasonChkList($cmnReasonChkId, $chkListName, $required);
							//print_r($commonReasonChkListRecUptd);
						}
					}
					if (($status=='N' || ($chkListOldValue != $checkPoint)) && $cmnReasonChkId > 0) {
						$chkListRecInUse = $commonReasonObj->chkListRecordInUse($cmnReasonChkId);
						if(!$chkListRecInUse) $delChekListRec = $commonReasonObj->delChekListRec($cmnReasonChkId);
					} 
				}
				//reverse checkpoint 
				if($chkListOldValue != $checkPoint && $chkListRecInUse){
					$commonReasonUpdateCheckpoint		=	$commonReasonObj->updateCommonReasonChkPoint($cmnReasonId,$chkListOldValue);
				}
			}
		}
		if ($commonReasonRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succCommonReasonUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateCommonReason.$selection);
		} else {
			$editMode	=	true;
			$editId=$cmnReasonId;
			if($chkEntryExist)
			{
				$err=$msg_CommonReasonUpdate;
			}
			else
			{
				$err		=	$msg_failCommonReasonUpdate;
			}
		}
		$commonReasonRecUptd	=	false;
	}
	
	
	if ($p["cmdConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$cmnReasonId	=	$p["confirmId"];
			if ($cmnReasonId!="") {
				// Checking the selected common reason is link with any other process
				$commonReasonRecConfirm = $commonReasonObj->updateConfirmCommonReason($cmnReasonId);
			}
		}
		if ($commonReasonRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmCommonReason);
			$sessObj->createSession("nextPage",$url_afterConfirmCommonReason.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmCommonReason;
		}
	}
	
	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$cmnReasonId	=	$p["rlconfirmId"];

			if ($cmnReasonId!="") {
				#Check any entries exist
				
					$commonReasonRecConfirm = $commonReasonObj->updaterlconfirmCommonReason($cmnReasonId);
					}
		}
		if ($commonReasonRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmCommonReason);
			$sessObj->createSession("nextPage", $url_afterRlConfirmCommonReason.$selection);
		} else {
			$errReleaseConfirm	= $msg_failRlConfirmCommonReason;
		}
	}

# Delete Common Reason
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$recExists= false;
		for ($i=1; $i<=$rowCount; $i++) {
		
				$cmnReasonId	=	$p["delId_".$i];
				// Checking the selected common reason is link with any other process
				$commonReasonExist = $commonReasonObj->commonReasonExist($cmnReasonId);
				if ($cmnReasonId!="" && $commonReasonExist) $recExists=true;
				if ($cmnReasonId!="" && !$commonReasonExist) {

					$CheckLisIntUse = $commonReasonObj->chkCheckLisIntUse($cmnReasonId);
					//print_r($CheckLisIntUse);
					
					if(!$CheckLisIntUse)
					{
						$chkListRcdDel = $commonReasonObj -> deleteChkListRcd($cmnReasonId);
						$commonReasonRecDel = $commonReasonObj->deleteCommonReason($cmnReasonId);	
					}
					
				}
		}
		if ($commonReasonRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCOmmonReason);
			$sessObj->createSession("nextPage",  $url_afterDelCommonReason.$selection);
			} 
		else {
				if ($recExists) $errDel = $msg_delCommonReasonExists;
				else if ($CheckLisIntUse) $errDel = $msg_delCommonReasonExists;
				else  $errDel	= $msg_failDelCommonReason;	
		
			}
		$commonReasonRecDel	=	false;

	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Common Reason	
	$comReasonRecs	=	$commonReasonObj->fetchAllPagingRecords($offset, $limit);
	$comReasonRecSize		=	sizeof($comReasonRecs);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($commonReasonObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	
	if ($editMode) $heading =   $label_editCommonReason;
	else $heading =   $label_addCommonReason;
	
	
	
	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/CommonReason.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>

<form name="frmCommonReason" action="CommonReason.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err?></td>
		</tr>
		<? } ?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>							
								
								
								<!-- Form fields start -->
							<?php	
								
								$bxHeader=$label_pageHeadingCommonReason;
								include "template/boxTL.php";
							?>				
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td colspan="3" align="center">
	<table width="70%" align="center">		
		<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
					<tr>
						<td>	
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCommonReason(document.frmCommonReason);">	
												</td>	
												<? } ?>
												<? if($addmode) { ?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateCommonReason(document.frmCommonReason);">
												</td>
												
												<? } ?>
											</tr>
											<input type="hidden" name="hidCommonReasonId" value="<?= $editId?>">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;" id="divEntryExistTxt" class="err1"></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;"> 
			<table width="70%" border="0">
				<tr>
				<TD>
					<table border="0">
						<TR>
						<TD valign="top">
						<table>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Account Type</td>
								<td class="listing-item" align="left">
									<select name="cod" id="cod">
									<?php
									 foreach($codArr as $actype => $value) 
											{?>
														<?
														$selected	=	"";
														if( $value== $accountType){
																$selected	=	"selected";
														}
														?>
											<option value="<?=$actype?>" <?=$selected?>><?=$value?> </option>

									<?php
											}?>
								</select>
								</td>
							</tr>
							<tr>
							<td class="fieldName" nowrap="nowrap">*Reason</td>
							<td class="listing-item">
								<input name="reason" type="text" id="reason" size="28" value="<?=$reason?>" autocomplete="off"/>
								<? if($editmode) {?>
								<input type="hidden" name="hdnid" value="" readonly>
								<? } ?>
							</td>
							</tr>	
							<tr>
								<td class="fieldName" nowrap >Check List</td>
								<td>
									<INPUT type="checkbox" name="check_point" id="checkPoint" class="chkBox" value="Y" <?=$checklistStatus?> onclick="showChkPoint();"> &nbsp;&nbsp;<span class="fieldName" style="vertical-align:middle; line-height:normal"><font size="1">(If Yes, please give tick mark)</font></span>
									<input type="hidden" name="chkListValue" id="chkListValue" value="<?= $checklistVal?>" >
								</td>
							</tr>						
						</table>
						</TD>						
						</TR>
<tr id="chkPointRow">
		<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
			<table>
				<TR>
					<TD>
							<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblCheckList" class="newspaperType">
								<tr align="center">
									<th nowrap style="text-align:center;">Check List</th>
									<th nowrap>Required</th>	
									<th>&nbsp;</th>
								</tr>

<?

$i=0;
	foreach($chkListRecs as $clr)
		{
			$cmnReasonChkId = $clr[0];
			$name = $clr[1];	
			$required = $clr[2];
			$requiredStatus = ($required=='Y')?"checked":"";
			
	?>	
<tr align="center" class="whiteRow" id="row_<?=$i;?>">
	<td align="center" class="listing-item">
		<input type="text" autocomplete="off" size="38" value="<?= $name?>" id="chkListName_<?=$i;?>" name="chkListName_<?=$i;?>"/>
	</td>
	<td align="center" class="listing-item">
		<input type="checkbox" class="chkBox" value="Y" <?= $requiredStatus?> id="required_<?=$i;?>" name="required_<?=$i;?>" />
	</td>
	<td align="center" class="listing-item">
		<a onclick="setChkListItemStatus('<?=$i?>');" href="###"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="ffff"/></a>
		<input type="hidden" value="" id="status_<?=$i;?>" name="status_<?=$i;?>"/>
		<input type="hidden" value="N" id="IsFromDB_<?=$i;?>" name="IsFromDB_<?=$i;?>"/>
		<input type="hidden" value="<?= $cmnReasonChkId?>" id="chkListEntryId_<?=$i;?>" name="chkListEntryId_<?=$i;?>" readonly />
	</td>
 </tr>
	<? $i++ ?>
	<? } ?>
	</table>
	<!--  Hidden Fields-->
	<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?= sizeof($chkListRecs)?>">
	</TD>
				</TR>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD>
						<a href="###" id='addRow' onclick="javascript:addNewCheckListItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Check List</a>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
					</table>
				</TD>
				</tr>
	
                                          </table>
					</td>
					</tr>
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>		
												<? if($editMode) { ?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCommonReason(document.frmCommonReason);">	
												</td>	
												<? } ?>												
												<? if($addMode) { ?>
												<td align="center" >
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateCommonReason(document.frmCommonReason);">
												</td>
												<? } ?>
											</tr>
											<tr>
												<td  height="10" ></td>
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
				<!-- Form fields end   -->
			</td>
		</tr>	
		<? } ?>
	</table>
		</td>
			</tr>	
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<? if(!$addMode||!$editMode){?>
	<tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<? if(!$printMode) {?>
			<tr>
				<td><? if($del==true) {?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?= $comReasonRecSize ?>);"><? } ?>&nbsp;<? if($add==true) { ?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } ?>&nbsp;<? if($print==true) {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintCommonReason.php?print=y',700,600);"> <? } ?> </td>
			</tr>
			<? } ?>
			</table>
		</td>
		</tr>
		<? } ?>
	<tr>
		<td colspan="3" height="5" ></td>
	</tr>
	<? if($errDel){ ?>
	<tr>
		<td colspan="3" height="15" align="center" class="err1"><?= $errDel ?></td>
	</tr>
	<? } ?>
	<? if(!$addMode||!$editMode) { ?>
	<tr>
				<td width="1" ></td>
				<td colspan="2" >
				<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
				<?
					if( sizeof($comReasonRecs) > 0 )
						{
							$i	=	0;
				?>
	<thead>
				<? if($maxpage>1) { ?>
				<tr>
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right" class="navRow">
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
      	$nav.= " <a href=\"CommonReason.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"CommonReason.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"CommonReason.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div>
					</td>
				</tr>
				<?
					}
				?>
				
				
				<thead>
			
	<tr align="center">
		<? if(!$printMode) { ?>
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="chkAll(this.form,'delId_'); " class="chkBox"></th> <? } ?>
		<th nowrap style="padding-left:10px; padding-right:10px;">Account Type</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Reason</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Check List</th>
		<? if($edit==true &&  !$printMode) { ?>
		<th width="45">&nbsp;</th>
		<? } ?>
		<? if($edit==true && !$printMode) { ?>
		<th width="45">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach($comReasonRecs as $icmR)
		{
			$i++;
			$cmnReasonId = $icmR[0];
			$accountType = $codArr[$icmR[1]];
			$reason	=	stripSlash($icmR[2]);
			$checklistVal	=	stripSlash($icmR[3]);
			$checklist=($checklistVal=='Y')?"YES":"NO";
			$default_entry	=	stripSlash($icmR[4]);
			$active= $icmR[5];
			
			
	?>
	<? if($default_entry== "Y") { ?>
	<!--<tr onMouseover="ShowTip('Default Entry');" onMouseout="UnTip();">-->
	<tr title="Default Entry">
	<?} else{?>
	<tr>
	<? } ?>
		<? if(!$printMode){ ?>
		<td width="20" align="center">
			<? if($default_entry == "Y") { ?>
				<input type="hidden" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="" readonly>
			<?} else{?>
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?= $cmnReasonId?>" class="chkBox">
			<? } ?>

		</td>
		<? } ?>
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> <?= $accountType ?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap> <?= $reason ?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<? if($commonReasonObj->displayChkList($cmnReasonId)){?>
			<a href="###" onMouseover="ShowTip('<?=$commonReasonObj->displayChkList($cmnReasonId)?>');" onMouseout="UnTip();"> <?= $checklist ?></a>
			<? } else {?>
				<?= $checklist ?>
			<? } ?>
			
		</td>
		<? if($edit) {?>
		<? if(!$printMode) { ?>
		  <td class="listing-item" width="45" align="center">
		  <?php if ($active!=1) {?>
		  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,
		  <?= $cmnReasonId?>,'editId');">
		 <? } ?>
		</td>
				<? } ?>	
		<? }?>
		
		<? if($active==false && !$printMode ) {?>
		 <td class="listing-item" width="45" align="center"><input type="submit" value="<?=$pending;?>" name="cmdConfirm" onClick="assignValue(this.form, 
		 <?= $cmnReasonId?>,'confirmId');">
			</td>
		<? } ?>	
		<? if($active==true && !$printMode ) {?>
		 <td class="listing-item" width="45" align="center"><input type="submit" value=" <?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form, <?= $cmnReasonId?>,'rlconfirmId');">
			</td>
		<? } ?>
	</tr>
		
	<? } ?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?= $comReasonRecSize?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
	<input type="hidden" name="rlconfirmId" value="">
	</tbody>
	<? if($maxpage>1){?>
	<tr>
		<td colspan="10" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"CommonReason.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"CommonReason.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"CommonReason.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div>
		</td>
	</tr>
	<? }?>
		<?
			}
			else
			{
		?>
	<tr><TD align="center"><?=$msgNoRecords;?></TD></tr>
		<?
			}
		?>
		</table>
		</td>
								</tr>
								<?
									}
								?>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<? if(!$addMode||!$editMode){?>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<? if(!$printMode) { ?>
											<tr>
												<td><? if($del==true) {?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?= $comReasonRecSize?>);"><? }?>&nbsp; <? if($add==true) {?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } ?>&nbsp;<? if($print==true) {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintCommonReason.php?print=y',700,600);"><? } ?></td>
											</tr>
											<? } ?>
										</table>
									</td>
								</tr>
								<? } ?>
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
			</td>
		</tr>	
		<tr>
			<td height="10">
			<input type="hidden" name="entryExist" id="entryExist" value="" readonly />
			<input type="hidden" name="pageNo" value="<?= $pageNo ?>" readonly /> 
			</td>
		</tr>	
</table>
		<?
			if( $editMode || $addMode) {
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			function addNewCheckListItem()
			{
				addNewCheckList('tblCheckList','','');		
			}
				showChkPoint();	
		</SCRIPT>
		<? } ?>
		
		<? if (($addMode || $editMode) && !sizeof($chkListRecs)) {?>		
		<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewCheckListItem();
		</SCRIPT>
		<? } ?>
		<? if(sizeof($chkListRecs)>0) {?>
		<SCRIPT LANGUAGE="JavaScript">
			fieldId = <?= sizeof($chkListRecs)?>
		</SCRIPT>
		<? } ?>
</form>

<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
