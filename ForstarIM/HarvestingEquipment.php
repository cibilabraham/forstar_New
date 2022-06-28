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

		$equipmentType		=	addSlash(trim($p["equipmentType"]));
		$equipmentDescription	=	addSlash(trim($p["equipmentDescription"]));
	
		
		
		if ($equipmentType!="") {
			$harvestingEquipmentTypeRecIns	=	$harvestingEquipmentObj->addHarvestingEquipmentType($equipmentType, $equipmentDescription, $userId);

			if ($harvestingEquipmentTypeRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddHarvestingEquipmentType);
				$sessObj->createSession("nextPage", $url_afterAddHarvestingEquipmentType.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddHarvestingEquipmentType;
			}
			$harvestingEquipmentTypeRecIns		=	false;
		}
	}
		
	# Edit Registration Type 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$harvestingEquipmentTypeRec		=	$harvestingEquipmentObj->find($editId);
		$harvestingEquipmentTypeId			=	$harvestingEquipmentTypeRec[0];
		$equipmentType			=	stripSlash($harvestingEquipmentTypeRec[1]);
		$equipmentDescription				=	stripSlash($harvestingEquipmentTypeRec[2]);
		
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$harvestingEquipmentTypeId		=	$p["hidHarvestingEquipmentTypeId"];
		$equipmentType		=	addSlash(trim($p["equipmentType"]));
		$equipmentDescription	=	addSlash(trim($p["equipmentDescription"]));
		
		
		if ($harvestingEquipmentTypeId!="" && $equipmentType!="") {
			$harvestingEquipmentTypeRecUptd = $harvestingEquipmentObj->updateHarvestingEquipmentType($harvestingEquipmentTypeId, $equipmentType, $equipmentDescription);
		}
	
		if ($harvestingEquipmentTypeRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succHarvestingEquipmentTypeUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateHarvestingEquipmentType.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failHarvestingEquipmentTypeUpdate;
		}
		$harvestingEquipmentTypeRecUptd	=	false;
	}


	# Delete Registration Type
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$harvestingEquipmentTypeId	=	$p["delId_".$i];

			if ($harvestingEquipmentTypeId!="") {
				// Need to check the selected Department is link with any other process
				$harvestingEquipmentTypeRecDel	=	$harvestingEquipmentObj->deleteHarvestingEquipmentType($harvestingEquipmentTypeId);
			}
		}
		if ($harvestingEquipmentTypeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelHarvestingEquipmentType);
			$sessObj->createSession("nextPage",$url_afterDelHarvestingEquipmentType.$selection);
		} else {
			$errDel	=	$msg_failDelHarvestingEquipmentType;
		}
		$harvestingEquipmentTypeRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$harvestingEquipmentTypeId	=	$p["confirmId"];
			if ($harvestingEquipmentTypeId!="") {
				// Checking the selected fish is link with any other process
				$harvestingEquipmentTypeRecConfirm = $harvestingEquipmentObj->updateHarvestingEquipmentTypeconfirm($harvestingEquipmentTypeId);
			}

		}
		if ($harvestingEquipmentTypeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmHarvestingEquipmentType);
			$sessObj->createSession("nextPage",$url_afterDelHarvestingEquipmentType.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$harvestingEquipmentTypeId = $p["confirmId"];
			if ($harvestingEquipmentTypeId!="") {
				#Check any entries exist
				
					$HarvestingEquipmentTypeRecConfirm = $harvestingEquipmentObj->updateHarvestingEquipmentTypeReleaseconfirm($harvestingEquipmentTypeId);
				
			}
		}
		if ($HarvestingEquipmentTypeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmHarvestingEquipmentType);
			$sessObj->createSession("nextPage",$url_afterDelHarvestingEquipmentType.$selection);
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
	$harvestingEquipmentTypeRecords	=	$harvestingEquipmentObj->fetchAllPagingRecords($offset, $limit);
	$harvestingEquipmentTypeSize		=	sizeof($harvestingEquipmentTypeRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($harvestingEquipmentObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editHarvestingEquipmentType;
	else 		$heading = $label_addHarvestingEquipmentType;
	
	$ON_LOAD_PRINT_JS	= "libjs/HarvestingEquipmentType.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmHarvestingEquipmentType" action="HarvestingEquipment.php" method="post">
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
					$bxHeader = "Manage Harvesting Equipment";
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipment.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddHarvestingEquipmentType(document.frmHarvestingEquipmentType);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipment.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddHarvestingEquipmentType(document.frmHarvestingEquipmentType);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidHarvestingEquipmentTypeId" value="<?=$harvestingEquipmentTypeId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<td class="fieldName" nowrap >*Harvesting equipment Type</td>
												<td><INPUT TYPE="text" NAME="equipmentType" size="15" value="<?=$equipmentType;?>"></td>
											</tr>
	
											<tr>
												<td class="fieldName" nowrap >Description</td>
												<td ><textarea name="equipmentDescription"><?=$equipmentDescription;?></textarea></td>
											</tr>
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipment.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddHarvestingEquipmentType(document.frmHarvestingEquipmentType);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HarvestingEquipment.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddHarvestingEquipmentType(document.frmHarvestingEquipmentType);">												</td>

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
			
			# Listing Registration Type Starts
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
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$harvestingEquipmentTypeSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintHarvestingEquipment.php',700,600);"><? }?></td>
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
			if ( sizeof($harvestingEquipmentTypeRecords) > 0 ) {
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
      				$nav.= " <a href=\"HarvestingEquipment.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"HarvestingEquipment.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"HarvestingEquipment.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Harvesting Equipment Type</th>
		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Description </th>
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
	foreach($harvestingEquipmentTypeRecords as $cr) {
		$i++;
		 $equipmentTypeId		=	$cr[0];
		 $equipmentType		=	stripSlash($cr[1]);
		 $equipmentDescription	=	stripSlash($cr[2]);
		
		 $active=$cr[3];
		$existingrecords=$cr[4];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$equipmentTypeId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$equipmentType;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$equipmentDescription;?></td>
		
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$equipmentTypeId;?>,'editId'); this.form.action='HarvestingEquipment.php';"  ><?php } ?>
			
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$equipmentTypeId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$equipmentTypeId;?>,'confirmId');" >
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
      				$nav.= " <a href=\"HarvestingEquipment.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"HarvestingEquipment.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"HarvestingEquipment.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$harvestingEquipmentTypeSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintHarvestingEquipment.php',700,600);"><? }?></td>
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