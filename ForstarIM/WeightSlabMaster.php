<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
	$selection 	= "?pageNo=".$p["pageNo"]."&selFilter=".$p["selFilter"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;		
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	# Value Re-setting	
	if ($p["name"]!="") $name = $p["name"];
			
	# Add a Record
	if ($p["cmdAdd"]!="") {
		$code		= "WSM_".autoGenNum();  // Weight Slab Master		
		$name		= addSlash(trim($p["name"]));
		
		$wtFrom		= trim($p["wtFrom"]);
		$wtTo		= trim($p["wtTo"]);
		$wtAbove	= $p["wtAbove"];

				
		# Check Duplicate Entry
		$duplicateEntry = $weightSlabMasterObj->chkDuplicateEntry($wtFrom, $wtTo, $cRtCtId);
		
		# Checking Above Wt	
		//$aboveWt = $weightSlabMasterObj->chkAboveWt($wtFrom, $cRtCtId);&& !$aboveWt
				
		if ($name!="" && !$duplicateEntry ) {
			$weightSlabRecIns = $weightSlabMasterObj->addWeightSlab($code, $name, $wtFrom, $wtTo, $wtAbove, $userId);

			if ($weightSlabRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddWeightSlabMaster);
				$sessObj->createSession("nextPage",$url_afterAddWeightSlabMaster.$selection);
			} else {
				$addMode	=	true;
				if ($duplicateEntry) $err = $msg_failAddWeightSlabMaster."<br>Weight Slab already exist in database.";
				else $err = $msg_failAddWeightSlabMaster;
			}
			$weightSlabRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddWeightSlabMaster;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$weightSlabRec	= $weightSlabMasterObj->find($editId);
		$editWeightSlabId	= $weightSlabRec[0];
		$code		= stripSlash($weightSlabRec[1]);
		$name		= stripSlash($weightSlabRec[2]);
		$wtFrom		= $weightSlabRec[3];
		$wtTo		= $weightSlabRec[4];
		$wtAbove	= $weightSlabRec[5];
	
		$aboveChecked = "";
		if ($wtAbove=='Y') $aboveChecked = "checked";
	}

	# Update a record
	if ($p["cmdSaveChange"]!="") {
		$weightSlabId 	= $p["hidWeightSlabId"];
		$name		= addSlash(trim($p["name"]));

		$wtFrom		= trim($p["wtFrom"]);
		$wtTo		= trim($p["wtTo"]);
		$wtAbove	= $p["wtAbove"];
				
		# Check Duplicate Entry
		$duplicateEntry = $weightSlabMasterObj->chkDuplicateEntry($wtFrom, $wtTo, $weightSlabId);

		# Checking Above Wt	
		//$aboveWt = $weightSlabMasterObj->chkAboveWt($wtFrom, $cRtCtId);  && !$aboveWt	

		if ($weightSlabId!="" && $name!="" && !$duplicateEntry) {
			$weightSlabRecUptd = $weightSlabMasterObj->updateWeightSlab($weightSlabId, $name, $wtFrom, $wtTo, $wtAbove);
		}
	
		if ($weightSlabRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succWeightSlabMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightSlabMaster.$selection);
		} else {
			$editMode	=	true;
			if ($duplicateEntry) $err = $msg_failWeightSlabMasterUpdate."<br>Weight Slab already exist in database.";
			else $err = $msg_failWeightSlabMasterUpdate;
		}
		$weightSlabRecUptd	=	false;
	}

	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount = $p["hidRowCount"];
		$existCount = 0;
		for ($i=1; $i<=$rowCount; $i++) {
			$weightSlabId = $p["delId_".$i];

			if ($weightSlabId!="") {
				# Check Wt Slab in use in use
				$weightSlabRecInUse = $weightSlabMasterObj->wtSlabRecInUse($weightSlabId);
				if (!$weightSlabRecInUse) {
					# Need to check the selected Category is link with any other process	
					# Delete rec
					$weightSlabRecDel = $weightSlabMasterObj->deleteWeightSlab($weightSlabId);
				} // Checking ends here
				if ($weightSlabRecInUse) $existCount++;
			}
		}
		if ($weightSlabRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelWeightSlabMaster);
			$sessObj->createSession("nextPage",$url_afterDelWeightSlabMaster.$selection);
		} else {
			if ($existCount>0) $errDel	= $msg_failDelWeightSlabMaster."<br>The selected Wt slab is in use. <br><span style='font-size:9px;'>Please make sure the Wt slab does not exist in Transporter Weight Slab/ Rate Master </span>";
			else $errDel	=	$msg_failDelWeightSlabMaster;
		}
		$weightSlabRecDel	=	false;
	}
if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$weightSlabId	=	$p["confirmId"];
			if ($weightSlabId!="") {
				// Checking the selected fish is link with any other process
				$weightSlabRecConfirm = $weightSlabMasterObj->updateWeightSlabconfirm($weightSlabId);
			}

		}
		if ($weightSlabRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmweightSlab);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$weightSlabId = $p["confirmId"];
			if ($weightSlabId!="") {
				#Check any entries exist
				
					$weightSlabRecConfirm = $weightSlabMasterObj->updateWeightSlabReleaseconfirm($weightSlabId);
				
			}
		}
		if ($weightSlabRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmweightSlab);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") 		$pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="") 	$pageNo = $g["pageNo"];
	else 				$pageNo = 1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$weightSlabRecords = $weightSlabMasterObj->fetchAllPagingRecords($offset, $limit);
	$weightSlabRecordSize  = sizeof($weightSlabRecords);

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($weightSlabMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($editMode)	$heading =	$label_editWeightSlabMaster;
	else 		$heading =	$label_addWeightSlabMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/WeightSlabMaster.js";  // Topleft Nav Settings

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmWeightSlabMaster" action="WeightSlabMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1"><?=$err;?></td>
		</tr>
		<?}?>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Manage Weight Slab";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="40%">
		<?
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightSlabMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateWeightSlabMaster(document.frmWeightSlabMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightSlabMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateWeightSlabMaster(document.frmWeightSlabMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidWeightSlabId" value="<?=$editWeightSlabId;?>">		
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<td colspan="2" nowrap>
			<table width="100%">
				<tr>
					<td class="fieldName" nowrap >*Name </td>
					<td><input type="text" name="name" size="20" value="<?=$name;?>" autocomplete="off"></td>
				</tr>
				<tr>
				<td nowrap colspan="2">
					<!--<fieldset>
					<legend class="listing-item">Weight</legend>-->
					<?php
						$entryHead = "Weight";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table width="200">
						<TR>
							<TD class="fieldName">*From</TD>
							<td class="listing-item" nowrap="true">
								<input type="text" name="wtFrom" id="wtFrom" size="5" value="<?=$wtFrom;?>" autocomplete="off" style="text-align:right;">&nbsp;Kg
							</td>
							<td>&nbsp;</td>
							<TD class="fieldName">*To</TD>
							<td class="listing-item" nowrap="true">
								<input type="text" name="wtTo" size="5" value="<?=$wtTo;?>" autocomplete="off" style="text-align:right;">&nbsp;Kg
							</td>
						</TR>
						<tr>
							<TD></TD>
							<TD></TD>
							<TD></TD>
							<TD></TD>
							<TD class="listing-item">(OR)</TD>	
						</tr>
						<tr>
							<TD></TD>
							<TD></TD>
							<TD></TD>
							<TD>
								<INPUT type="checkbox" class="chkBox" name="wtAbove" value="Y" <?=$aboveChecked?>>
							</TD>
							<TD class="listing-item">Above</TD>	
						</tr>
					</table>
					<!--</fieldset>-->
					<?php
						require("template/rbBottom.php");
					?>
				</td>
			</tr>
			</table>
		</td>
	</tr>
		
	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightSlabMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateWeightSlabMaster(document.frmWeightSlabMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightSlabMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateWeightSlabMaster(document.frmWeightSlabMaster);">	
		</td>
		<input type="hidden" name="cmdAddNew" value="1">
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
				<!-- Form fields end   -->	
			</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
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
			<td background="images/heading_bg.gif" class="pageName" nowrap="true" style="background-repeat:repeat-x">&nbsp;Manage Weight Slab</td>
			<td background="images/heading_bg.gif" class="pageName" align="right" nowrap="true" style="background-repeat:repeat-x">
			</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$weightSlabRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintWeightSlabMaster.php',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
		if ($weightSlabRecordSize) {
			$i = 0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="3" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"WeightSlabMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"WeightSlabMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"WeightSlabMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
		<th width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</th>		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>		
		<? if($edit==true){?>
		<th class="listing-head" >&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($weightSlabRecords as $wsr) {
		$i++;
		$weightSlabId	= $wsr[0];		
		$name 	= stripSlash($wsr[2]);
		$active=$wsr[3];
	?>
<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$weightSlabId;?>" class="chkBox">
	</td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>	
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$weightSlabId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='WeightSlabMaster.php';"><? } ?></td>
	<? }?>
	 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$weightSlabId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$weightSlabId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="3" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"WeightSlabMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"WeightSlabMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"WeightSlabMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
		}  else {
	?>
	<tr>
		<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$weightSlabRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintWeightSlabMaster.php',700,600);"><? }?></td>
											</tr>
										</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>	
					<?
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
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">	
	</table>
	<?php 
	if ($iFrameVal=="") { 
	?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	//ensureInFrameset(document.frmWeightSlabMaster);
	//-->
	</script>
<?php 
	}
?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>