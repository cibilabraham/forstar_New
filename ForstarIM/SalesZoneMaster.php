<?php
	require("include/include.php");
	require_once('lib/SalesZoneMaster_ajax.php');	

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
		header("Location: ErrorPage.php");
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
		$addMode  = false;
		$editMode = false;
	}

	# Value Re-setting	
	if ($p["name"]!="") $name = $p["name"];
			
	# Add a Record
	if ($p["cmdAdd"]!="") {
		$code		= "SZM_".autoGenNum();  // Sales Zone Master		
		$name		= addSlash(trim($p["name"]));
						
		# Check Duplicate Entry
		$duplicateEntry = $salesZoneObj->chkDuplicateEntry($name, $cRtCtId);
			
		if ($name!="" && !$duplicateEntry) {
			$zoneRecIns = $salesZoneObj->addZone($code, $name, $userId);		
			if ($zoneRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddSalesZoneMaster);
				$sessObj->createSession("nextPage",$url_afterAddSalesZoneMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSalesZoneMaster;
			}
			$zoneRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddSalesZoneMaster;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$zoneRec	= $salesZoneObj->find($editId);
		$editZoneId	= $zoneRec[0];
		$code		= stripSlash($zoneRec[1]);
		$name		= stripSlash($zoneRec[2]);		
	}


	# Update a record
	if ($p["cmdSaveChange"]!="") {
		$zoneId 	= $p["hidZoneId"];
		$name		= addSlash(trim($p["name"]));	
		$rowCount 	= $p["hidTableRowCount"];	
				
		# Check Duplicate Entry
		$duplicateEntry = $salesZoneObj->chkDuplicateEntry($name, $zoneId);	

		if ($zoneId!="" && $name!="" && !$duplicateEntry) {
			$zoneRecUptd = $salesZoneObj->updateZone($zoneId, $name);	
		}
	
		if ($zoneRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSalesZoneMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSalesZoneMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSalesZoneMasterUpdate;
		}
		$zoneRecUptd	=	false;
	}

	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount = $p["hidRowCount"];
		$existCount = 0;
		for ($i=1; $i<=$rowCount; $i++) {
			$zoneId = $p["delId_".$i];

			if ($zoneId!="") {
				# Check Retail Counter in use
				$zoneRecInUse = $salesZoneObj->zoneRecInUse($zoneId);
				if (!$zoneRecInUse) {
					# Delete rec
					$zoneRecDel = $salesZoneObj->deleteZone($zoneId);
				} // Checking ends here
				if ($zoneRecInUse) $existCount++;
			}
		}
		if ($zoneRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSalesZoneMaster);
			$sessObj->createSession("nextPage",$url_afterDelSalesZoneMaster.$selection);
		} else {
			if ($existCount>0) $errDel	= $msg_failDelSalesZoneMaster."<br><span style='font-size:9px;'>Please make sure the zone does not exist in State Master </span>";
			else $errDel	=	$msg_failDelSalesZoneMaster;
		}
		$zoneRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$zoneId	=	$p["confirmId"];


			if ($zoneId!="") {
				// Checking the selected fish is link with any other process
				$zoneRecConfirm = $salesZoneObj->updatezoneconfirm($zoneId);
			}

		}
		if ($zoneRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmzone);
			$sessObj->createSession("nextPage",$url_afterDelSalesZoneMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$zoneId = $p["confirmId"];

			if ($zoneId!="") {
				#Check any entries exist
				
					$zoneRecConfirm = $salesZoneObj->updatezoneReleaseconfirm($zoneId);
				
			}
		}
		if ($zoneRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmzone);
			$sessObj->createSession("nextPage",$url_afterDelSalesZoneMaster.$selection);
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
	$zoneRecords = $salesZoneObj->fetchAllPagingRecords($offset, $limit);
	$zoneRecordSize  = sizeof($zoneRecords);

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($salesZoneObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else $mode = "";

	if ($editMode)	$heading =	$label_editSalesZoneMaster;
	else 		$heading =	$label_addSalesZoneMaster;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/SalesZoneMaster.js";  // Topleft Nav Settings

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");	
?>
	<form name="frmSalesZoneMaster" action="SalesZoneMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="5"></TD></tr>
	<tr>
		<td align="center">
			<a href="StateMaster.php" class="link1">State</a>
		</td>
	</tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>
		</tr>
		<?}?>
	<tr><TD height="5"></TD></tr>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Sales Zone Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="40%">
		<?php
			if ( $editMode || $addMode) {
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesZoneMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSalesZoneMaster(document.frmSalesZoneMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesZoneMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateSalesZoneMaster(document.frmSalesZoneMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidZoneId" value="<?=$editZoneId;?>">		
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divZoneExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>	
	<tr>
		<td class="fieldName" nowrap >*Name </td>
		<td><input type="text" name="name" id="name" size="20" value="<?=$name;?>" autocomplete="off" onkeyup="xajax_chkZoneExist(document.getElementById('name').value, '<?=$mode?>', '<?=$editZoneId?>')"></td>
	</tr>	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesZoneMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateSalesZoneMaster(document.frmSalesZoneMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesZoneMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateSalesZoneMaster(document.frmSalesZoneMaster);">	
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
				<!-- Form fields end   -->			</td>
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
			<td background="images/heading_bg.gif" class="pageName" nowrap="true" style="background-repeat:repeat-x">&nbsp;Sales Zone Master</td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$zoneRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesZoneMaster.php',700,600);"><? }?></td>
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
	<table cellpadding="1"  width="25%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php
		if ($zoneRecordSize) {
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
      				$nav.= " <a href=\"SalesZoneMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SalesZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SalesZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
			<th class="listing-head" >&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($zoneRecords as $zr) {
		$i++;
		$zoneId	= $zr[0];		
		$name 	= stripSlash($zr[2]);
		$active=$zr[3];
		$existingrecords=$zr[4];
	?>
<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
	<td width="20">
		<?php 
		
		if($existingrecords==0){?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$zoneId;?>" class="chkBox">
		<?php }?>
	</td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>			
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center" style="padding-left:3px; padding-right:3px;">
			 <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$zoneId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SalesZoneMaster.php';">
			 <? } ?>
		</td>
	<? }?>



	 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$zoneId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$zoneId;?>,'confirmId');" >
			<?php
//			}
 }?>
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
      				$nav.= " <a href=\"SalesZoneMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SalesZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SalesZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$zoneRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesZoneMaster.php',700,600);"><? }?></td>
											</tr>
										</table></td>
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
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>