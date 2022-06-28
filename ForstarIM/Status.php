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
	
	# Add New	
	if ($p["cmdAddNew"]!="") $addMode	=	true;
	
	# Add
	if ($p["cmdAdd"]!="") {
	
		$status		=	addSlash(trim($p["status"]));
		$description	=	addslash($p["description"]);
		
		if ($status!="") {
			$statusRecIns	=	$statusObj->addStatus($status,$description);
			
			if ($statusRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddStatus);
				$sessObj->createSession("nextPage",$url_afterAddStatus.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddStatus;
			}
			$statusRecIns	=	false;
		}
	}
	
	# Edit 	
	if( $p["editId"]!="" ){
		$editId			=	$p["editId"];
		$editMode		=	true;
		$statusRec		=	$statusObj->find($editId);
		
		$editStatusId		=	$statusRec[0];
		$status				=	stripSlash($statusRec[1]);
		$description		=	stripSlash($statusRec[2]);
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$statusId	=	$p["hidStatusId"];
		$status		=	addSlash(trim($p["status"]));
		$description	=	addslash($p["description"]);
		
		if ($statusId!="" && $status!="" ) {
			$statusRecUptd	=	$statusObj->updateStatus($statusId,$status,$description);
		}
	
		if ($statusRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateStatus);
			$sessObj->createSession("nextPage",$url_afterUpdateStatus.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateStatus;
		}
		$statusRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$statusId	= $p["delId_".$i];

			if ($statusId!="") {
				$statusRecDel		=	$statusObj->deleteStatus($statusId);				
			}
		}
		if ($statusRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStatus);
			$sessObj->createSession("nextPage",$url_afterDelStatus.$selection);
		} else {
			$errDel	=	$msg_failDelStatus;
		}
		$statusRecDel	=	false;
	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$statusId	=	$p["confirmId"];
			if ($statusId!="") {
				// Checking the selected fish is link with any other process
				$statusRecConfirm = $statusObj->updateStatusModeconfirm($statusId);
			}

		}
		if ($statusRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmStatus);
			$sessObj->createSession("nextPage",$url_afterDelStatus.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$statusId = $p["confirmId"];
			if ($statusId!="") {
				#Check any entries exist
				
					$statusRecConfirm = $statusObj->updateStatusModeReleaseconfirm($statusId);
				
			}
		}
		if ($statusRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmStatus);
			$sessObj->createSession("nextPage",$url_afterDelStatus.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit;  
	## ----------------- Pagination Settings I End ------------	
	
	#List All Record	
	$statusRecords		=	$statusObj->fetchPagingRecords($offset, $limit);
	$statusRecordsize	=	sizeof($statusRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($statusObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) $heading = $label_editStatus;
	else $heading = $label_addStatus;

	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/status.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmStatus" action="Status.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
			<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		
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
								$bxHeader="STAGES-ORDER PROCESSING";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;STAGES-ORDER PROCESSING </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('Status.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStatus(document.frmStatus);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('Status.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddStatus(document.frmStatus);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidStatusId" value="<?=$editStatusId;?>">
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center"> 
						<table width="50%">
                                                <tr>
                                                 <td class="fieldName" nowrap="nowrap">*Status</td>
                                                  <td class="listing-item"><input name="status" type="text" id="status" size="8" value="<?=$status?>"></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Description</td>
                                                  <td class="listing-item"><textarea name="description" rows="2" id="description"><?=$description?></textarea></td>
                                                </tr>
                                              </table></td>
											</tr>
											<tr>
											  <td colspan="2" align="center" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Status.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStatus(document.frmStatus);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Status.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddStatus(document.frmStatus);">												</td>

												<? }?>
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
		<?
			}			
			# Listing Grade Starts
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
									<td colspan="3"></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete ** " style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$statusRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintStatus.php',700,600);"><? }?></td>
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
						<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($statusRecords) > 0 )
												{
													$i	=	0;
											?>
	<thead>
		<? if($maxpage>1){?>
<tr>
<td colspan="4" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"Status.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Status.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"Status.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  <? }?>
											<tr>
												<th width="20">
												<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th nowrap>Stage </th>
												<th>Description</th>
												<? if($edit==true){?>
												<th width="45">&nbsp;</th>
												<? }?>
												<? if($confirm==true){?>
												<th width="45">&nbsp;</th>
												<? }?>
											</tr>
	</thead>	
	<tbody>
											<?php
												foreach($statusRecords as $sr) {
													$i++;
													$statusId		=	$sr[0];
													$status			=	stripSlash($sr[1]);
													$description	=	stripSlash($sr[2]);
													$active=$sr[3];
											?>
											<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20" align="center">
												<!--<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$statusId;?>" class="chkBox"></td>-->
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$statusId;?>" class="chkBox">
												
												</td>
												<td class="listing-item" nowrap ><?=$status;?></td>
												<td class="listing-item" nowrap="nowrap" ><?=$description;?></td>
												<? if($edit==true){?>
											  		<td class="listing-item" width="45" align="center">
													 <?php if ($active!=1) {?>
													<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$statusId;?>,'editId');">
													<? } ?>
													</td>
											  <? }?>
											  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm"  onClick="assignValue(this.form,<?=$statusId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm"  onClick="assignValue(this.form,<?=$statusId;?>,'confirmId');"  >
			<?php } }?>
			
			
			
			
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
<td colspan="4" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"Status.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Status.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"Status.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  <? }?>
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete ** " style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$statusRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintStatus.php',700,600);"><? }?></td>
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
				<!-- Form fields end   -->
			</td>
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