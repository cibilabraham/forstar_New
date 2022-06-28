<?php
	require("include/include.php");
	require_once("lib/sealNumber_ajax.php");
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


	# Add Seal Master Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
		$mainId		= "";
	}
	

	#Add a Seal Master
	if ($p["cmdAdd"]!="") {

		$sealNo		=	addSlash(trim($p["sealNo"]));
		$status	=	addSlash(trim($p["status"]));
		$purpose		=	addSlash(trim($p["purpose"]));
		$changeStatus		=	addSlash(trim($p["changeStatus"]));
		
		
		
		if ($sealNo!="") {
		//echo "hai";
			$checkSeal=$sealNumberObj->check($sealNo);
//echo $id			=	$checkSeal[0];
			if($checkSeal)
			{
			echo $id			=	$checkSeal[0];
			$sealIns	=	$sealNumberObj->updateSealMaster($id, $sealNo, $status, $purpose,$changeStatus);
			}
			else
			{
			$sealMasterRecIns	=	$sealNumberObj->addSealMaster($sealNo, $status, $purpose,$changeStatus, $userId);

			if ($sealMasterRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddSealNumber);
				$sessObj->createSession("nextPage", $url_afterAddSealNumber.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSealNumber;
			}
			$sealMasterRecIns		=	false;
			}
		}
	}
		
	# Edit Seal Master 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$sealMasterRec		=	$sealNumberObj->find($editId);
		$sealNumberId			=	$sealMasterRec[0];
		$sealNo			=	stripSlash($sealMasterRec[1]);
		$status	=	stripSlash($sealMasterRec[2]);
		$purpose		=	stripSlash($sealMasterRec[3]);
		$changeStatus		=	stripSlash($sealMasterRec[4]);
		
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$sealNumberId		=	$p["hidSealNumberId"];
		$sealNo		=	addSlash(trim($p["sealNo"]));
		$status	=	addSlash(trim($p["status"]));
		$purpose		=	addSlash(trim($p["purpose"]));
		$changeStatus		=	addSlash(trim($p["changeStatus"]));
		
		
		if ($sealNumberId!="" ) {
			$sealMasterRecUptd=$sealNumberObj->updateSealMaster($sealNumberId, $sealNo, $status, $purpose,$changeStatus);
		}
	
		if ($sealMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSealNumberUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSealNumber.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSealNumberUpdate;
		}
		$sealMasterRecUptd	=	false;
	}


	# Delete Seal Master
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$sealNumberId	=	$p["delId_".$i];

			if ($sealNumberId!="") {
				// Need to check the selected employee is link with any other process
				$sealMasterRecDel	=	$sealNumberObj->deleteSealMaster($sealNumberId);
			}
		}
		if ($sealMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSealNumber);
			$sessObj->createSession("nextPage",$url_afterDelSealNumber.$selection);
		} else {
			$errDel	=	$msg_failDelSealNumber;
		}
		$sealMasterRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$sealNumberId	=	$p["confirmId"];
			if ($sealNumberId!="") {
				// Checking the selected fish is link with any other process
				$sealMasterRecConfirm = $sealNumberObj->updateSealMasterObjconfirm($sealNumberId);
			}

		}
		if ($sealMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmSealNumber);
			$sessObj->createSession("nextPage",$url_afterDelSealNumber.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$sealNumberId = $p["confirmId"];
			if ($sealNumberId!="") {
				#Check any entries exist
				
					$sealMasterRecConfirm = $sealNumberObj->updateSealMasterReleaseconfirm($sealNumberId);
				
			}
		}
		if ($sealMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmSealNumber);
			$sessObj->createSession("nextPage",$url_afterDelSealNumber.$selection);
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

	# List all Seal Number ;
	$sealNumberRecords	=	$sealNumberObj->fetchAllPagingRecords($offset, $limit);
	$sealNumberSize		=	sizeof($sealNumberRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($sealNumberObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editSealNumber;
	else 		$heading = $label_addSealNumber;
	
	# Setting the mode
	$mode = "";
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;
	
	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/SealNumber.js";
	
	
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSealNumber" action="SealNumber.php" method="post">
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
					$bxHeader = "Manage Seal Number";
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SealNumber.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSealNumber(document.frmSealNumber);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SealNumber.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSealNumber(document.frmSealNumber);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidSealNumberId" value="<?=$sealNumberId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
											<td class="fieldName" nowrap >*Seal Number </td>
											<td>
											<input name="sealNo" id="sealNo" type="text" size="12" value="<?=$sealNo?>" onKeyUp="xajax_chksealNumberExist(document.getElementById('sealNo').value,'<?=$mode?>','<?=$mainId?>');" autocomplete="off" onchange="displayView();" />
											</td> 
												
											</tr>
											<tr>
												<td class="fieldName" nowrap>Status</td>
												<td><INPUT TYPE="text" NAME="status" size="15" id="status" value="<?=$status?>" ></td>
											</tr>
											
											<tr id="purposeField">
												<td nowrap class="fieldName" >*Purpose</td>
												<td nowrap>
													<select name="purpose" id="purpose">
													
													<?php if($purpose) { ?>
													<option value="<?=$purpose?>" selected><?=$purpose?></option>
													<?php }
													else
													{?>
													<option value="">-- Select --</option>
													<?php } ?>
													<?php if($purpose!='IN') { ?>
													<option value="IN">IN</option>
													<?php } 
													 if($purpose!='OUT') {?>
													<option value="OUT">OUT</option>
													<?php } ?>
													
													
													</select>
												</td>
											</tr>
											
											<tr id="changeField">
												<td class="fieldName" nowrap>Change Status</td>
												
												<td nowrap>
													<select name="changeStatus" id="changeStatus">
													<?php if($changeStatus) { ?>
													<option value="<?=$changeStatus?>" selected><?=$changeStatus?></option>
													<?php }
													else
													{?>
													<option value="">-- Select --</option>
													<?php } ?>
													<?php if($changeStatus!='Used') {?>
													<option value="Used">Used</option>
													<?php } 
													 if($changeStatus!='Blocked') {?>
													<option value="Blocked">Blocked</option>
													<?php } 
													 if($changeStatus!='Free') {?>
													<option value="Free">Free</option>
													<?php }
													 ?>
													
													
													</select>
												</td>
											</tr>
											
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SealNumber.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSealNumber(document.frmSealNumber);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SealNumber.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSealNumber(document.frmSealNumber);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
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
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$sealNumberSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSealNumber.php',700,600);"><? }?></td>
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
			if ( sizeof($sealNumberRecords) > 0 ) {
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
      				$nav.= " <a href=\"SealNumber.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SealNumber.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SealNumber.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Seal Number</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Status</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Purpose </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Change Status </th>
		
		
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
	foreach($sealNumberRecords as $cr) {
		$i++;
		 $sealNumberId		=	$cr[0];
		 $sealNo		=	stripSlash($cr[1]);
		 $status	=	stripSlash($cr[2]);
		 $purpose		=	stripSlash($cr[3]);
		 $changeStatus		=	stripSlash($cr[4]);
		 $active=$cr[5];
		 $existingrecords=$cr[6];
			
		
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$sealNumberId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sealNo;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$status;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$purpose;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$changeStatus;?></td>
		
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$sealNumberId;?>,'editId'); this.form.action='SealNumber.php';"  >
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$sealNumberId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$sealNumberId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
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
      				$nav.= " <a href=\"SealNumber.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SealNumber.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SealNumber.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$sealNumberSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSealNumber.php',700,600);"><? }?></td>
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
	
	<?php
		if ($addMode || $editMode) {
	?>
		<script language="JavaScript" type="text/javascript">
			displayView();
		</script>
	<?php
		}
	?>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>