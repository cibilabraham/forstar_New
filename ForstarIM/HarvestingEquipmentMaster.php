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


	# Add Registration Type Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Registration Type
	if ($p["cmdAdd"]!="") {

		$equipmentName		=	addSlash(trim($p["equipmentName"]));
		$tarWt	=	addSlash(trim($p["tarWt"]));
		$equipmentType		=	addSlash(trim($p["equipmentType"]));
		
		
		if ($equipmentName!="") {
			$harvestingEquipmentMasterRecIns	=	$harvestingEquipmentMasterObj->addHarvestingEquipmentMaster($equipmentName, $tarWt, $equipmentType, $userId);

			if ($harvestingEquipmentMasterRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddHarvestingEquipmentMaster);
				$sessObj->createSession("nextPage", $url_afterAddHarvestingEquipmentMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddHarvestingEquipmentMaster;
			}
			$harvestingEquipmentMasterRecIns		=	false;
		}
	}
		
	# Edit Registration Type 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$HarvestingEquipmentMasterRec		=	$harvestingEquipmentMasterObj->find($editId);
		$harvestingEquipmentMasterId			=	$HarvestingEquipmentMasterRec[0];
		$equipmentName			=	stripSlash($HarvestingEquipmentMasterRec[1]);
		$tarWt				=	stripSlash($HarvestingEquipmentMasterRec[2]);
		$equipmentType	=	stripSlash($HarvestingEquipmentMasterRec[3]);
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$harvestingEquipmentMasterId		=	$p["hidHarvestingEquipmentMasterId"];
		$equipmentName		=	addSlash(trim($p["equipmentName"]));
		$tarWt	=	addSlash(trim($p["tarWt"]));
		$equipmentType		=	addSlash(trim($p["equipmentType"]));
		
		if ($harvestingEquipmentMasterId!="" && $equipmentName!="") {
			$harvestingEquipmentMasterRecUptd = $harvestingEquipmentMasterObj->updateHarvestingEquipmentMaster($harvestingEquipmentMasterId, $equipmentName, $tarWt, $equipmentType);
		}
	
		if ($harvestingEquipmentMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succHarvestingEquipmentMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateHarvestingEquipmentMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failHarvestingEquipmentMasterUpdate;
		}
		$harvestingEquipmentMasterRecUptd	=	false;
	}


	# Delete Harvesting Equipment Master
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$harvestingEquipmentMasterId	=	$p["delId_".$i];

			if ($harvestingEquipmentMasterId!="") {
				// Need to check the selected Department is link with any other process
				$harvestingEquipmentMasterRecDel	=	$harvestingEquipmentMasterObj->deleteHarvestingEquipmentMaster($harvestingEquipmentMasterId);
			}
		}
		if ($harvestingEquipmentMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelHarvestingEquipmentMaster);
			$sessObj->createSession("nextPage",$url_afterDelHarvestingEquipmentMaster.$selection);
		} else {
			$errDel	=	$msg_failDelHarvestingEquipmentMaster;
		}
		$harvestingEquipmentMasterRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$harvestingEquipmentMasterId	=	$p["confirmId"];
			if ($harvestingEquipmentMasterId!="") {
				// Checking the selected fish is link with any other process
				$harvestingEquipmentMasterRecConfirm = $harvestingEquipmentMasterObj->updateHarvestingEquipmentMasterObjconfirm($harvestingEquipmentMasterId);
			}

		}
		if ($harvestingEquipmentMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmHarvestingEquipmentMaster);
			$sessObj->createSession("nextPage",$url_afterDelharvestingEquipmentMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$harvestingEquipmentMasterId = $p["confirmId"];
			if ($harvestingEquipmentMasterId!="") {
				#Check any entries exist
				
					$harvestingEquipmentMasterRecConfirm = $harvestingEquipmentMasterObj->updateHarvestingEquipmentMasterReleaseconfirm($harvestingEquipmentMasterId);
				
			}
		}
		if ($harvestingEquipmentMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmHarvestingEquipmentMaster);
			$sessObj->createSession("nextPage",$url_afterDelHarvestingEquipmentMaster.$selection);
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

	# List all Registration type ;
	$harvestingEquipmentMasterRecords	=	$harvestingEquipmentMasterObj->fetchAllPagingRecords($offset, $limit);
	$harvestingEquipmentMasterSize		=	sizeof($harvestingEquipmentMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($harvestingEquipmentMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editHarvestingEquipmentMaster;
	else 		$heading = $label_addHarvestingEquipmentMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/HarvestingEquipmentMaster.js";
	
	# Get all Equipment Recs
		$equipmentTypeRecs = $harvestingEquipmentObj->fetchAllRecordsActiveequipmentType();
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmHarvestingEquipmentMaster" action="HarvestingEquipmentMaster.php" method="post">
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
					$bxHeader = "Manage Harvesting Equipment Master";
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipmentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddHarvestingEquipmentMaster(document.frmHarvestingEquipmentMaster);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipmentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddHarvestingEquipmentMaster(document.frmHarvestingEquipmentMaster);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidHarvestingEquipmentMasterId" value="<?=$harvestingEquipmentMasterId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<td class="fieldName" nowrap >*Name of Equipment</td>
												<td><INPUT TYPE="text" NAME="equipmentName" size="15" value="<?=$equipmentName;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*Tare Wt</td>
												<td><INPUT TYPE="text" NAME="tarWt" size="15" value="<?=$tarWt;?>"></td>
											</tr>
											
											<tr>
												<td nowrap class="fieldName" >*Equipment Type</td>
												<td nowrap>
													<select name="equipmentType" id="equipmentType">
													<option value="">-- Select --</option>
													<?php
													foreach ($equipmentTypeRecs as $cmr) {
														$equipmentTypeId 	= $cmr[0];	
														$equipmentType1	= $cmr[1];
														$selected = ($equipmentType==$equipmentTypeId)?"selected":""
													?>
													<option value="<?=$equipmentTypeId?>" <?=$selected?>><?=$equipmentType1?></option>
													<?  }?>
													</select>
												</td>
											</tr>
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipmentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddHarvestingEquipmentMaster(document.frmHarvestingEquipmentMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipmentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddHarvestingEquipmentMaster(document.frmHarvestingEquipmentMaster);">												</td>

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
			
			# Listing Harvesting Equipment Master Starts
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
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$harvestingEquipmentMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintHarvestingEquipmentMaster.php',700,600);"><? }?></td>
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
			if ( sizeof($harvestingEquipmentMasterRecords) > 0 ) {
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
      				$nav.= " <a href=\"HarvestingEquipmentMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"HarvestingEquipmentMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"HarvestingEquipmentMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name of Equipment</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Tare Wt</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Equipment Type </th>
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
	foreach($harvestingEquipmentMasterRecords as $cr) {
		$i++;
		 $harvestingEquipmentMasterId		=	$cr[0];
		 $equipmentName		=	stripSlash($cr[1]);
		 $tarWt	=	stripSlash($cr[2]);
		 $equipment		=	stripSlash($cr[3]);
		 
		 $equipmentTypeRec=$harvestingEquipmentMasterObj->fetchEquipmentType($equipment);
		 $equipmentType=$equipmentTypeRec[1];
		 $active=$cr[4];
		$existingrecords=$cr[5];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$harvestingEquipmentMasterId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$equipmentName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$tarWt;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$equipmentType;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$harvestingEquipmentMasterId;?>,'editId'); this.form.action='HarvestingEquipmentMaster.php';"  > <?php } ?>
			
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$harvestingEquipmentMasterId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$harvestingEquipmentMasterId;?>,'confirmId');" >
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
      				$nav.= " <a href=\"HarvestingEquipmentMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"HarvestingEquipmentMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"HarvestingEquipmentMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$harvestingEquipmentMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintHarvestingEquipmentMaster.php',700,600);"><? }?></td>
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