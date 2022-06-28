<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once('lib/ZoneMaster_ajax.php');	
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
		//header("Location: ErrorPage.php");
		header("Location: ErrorPageIFrame.php");
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
		$code		= "ZM_".autoGenNum();  // Zone Master		
		$name		= addSlash(trim($p["name"]));
		$rowCount 	= $p["hidTableRowCount"];
				
		# Check Duplicate Entry
		$duplicateEntry = $zoneMasterObj->chkDuplicateEntry($name, $cRtCtId);
			
		if ($name!="" && !$duplicateEntry) {
			$zoneRecIns = $zoneMasterObj->addZone($code, $name, $userId);
			
			if ($zoneRecIns) {
				#Find the Last inserted Id From Main Table
				$lastZoneId = $databaseConnect->getLastInsertedId();
				for ($i=0; $i<$rowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selStateId	= $p["state_".$i];
						$selCity	= $p["city_".$i];
							if ($lastZoneId!="" && $selStateId!="") {
								$areaDemarcationStateRecIns = $zoneMasterObj->addAreaDemarcationState($lastZoneId, $selStateId, $selCity);
							}
					}
				}			
			} 

			if ($zoneRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddZoneMaster);
				$sessObj->createSession("nextPage",$url_afterAddZoneMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddZoneMaster;
			}
			$zoneRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddZoneMaster;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$zoneRec	= $zoneMasterObj->find($editId);
		$editZoneId	= $zoneRec[0];
		$code		= stripSlash($zoneRec[1]);
		$name		= stripSlash($zoneRec[2]);

		# State Records
		$areaDemarcationStateRecs  = $zoneMasterObj->getAreaDemarcationStateRecords($editZoneId);
	}


	# Update a record
	if ($p["cmdSaveChange"]!="") {
		$zoneId 	= $p["hidZoneId"];
		$name		= addSlash(trim($p["name"]));	
		$rowCount 	= $p["hidTableRowCount"];	
				
		# Check Duplicate Entry
		$duplicateEntry = $zoneMasterObj->chkDuplicateEntry($name, $zoneId);	

		if ($zoneId!="" && $name!="" && !$duplicateEntry) {
			$zoneRecUptd = $zoneMasterObj->updateZone($zoneId, $name);

			for ($i=0; $i<$rowCount; $i++) {
			    $status 		= $p["status_".$i];
			    $stateEntryId	= $p["hidStateEntryId_".$i];
			    if ($status!='N') {
				$selStateId	= $p["state_".$i];
				$selCity	= $p["city_".$i];
					if ($zoneId!="" && $selStateId!="" && $stateEntryId=="") {
						$areaDemarcationStateRecIns = $zoneMasterObj->addAreaDemarcationState($zoneId, $selStateId, $selCity);
					} else if ($zoneId!="" && $selStateId!="" && $stateEntryId!="") {
						$updateDemarcationStateRec = $zoneMasterObj->updateDemarcationState($stateEntryId, $selStateId, $selCity);
					}
				} // Status=Y Loop ends here
				  # Delete the state IF Status=N	
			        if ($status=='N' && $stateEntryId!="") {
					# Delete Removed Rec
					$delRemovedRec = $zoneMasterObj->delRemovedDistRec($stateEntryId);
				}
			   }	// For Loop ends Here		
		}
	
		if ($zoneRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succZoneMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateZoneMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failZoneMasterUpdate;
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
				$zoneRecInUse = $zoneMasterObj->zoneRecInUse($zoneId);
				if (!$zoneRecInUse) {
					# Need to check the selected Ad is link with any other process		
					$delAreaDemacationEntryRecs = $zoneMasterObj->delAreaDemarcationEntryRecs($zoneId);
					# Delete rec
					$zoneRecDel = $zoneMasterObj->deleteZone($zoneId);
				} // Checking ends here
				if ($zoneRecInUse) $existCount++;
			}
		}
		if ($zoneRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelZoneMaster);
			$sessObj->createSession("nextPage",$url_afterDelZoneMaster.$selection);
		} else {
			if ($existCount>0) $errDel	= $msg_failDelZoneMaster."<br>The selected zone is in use. <br><span style='font-size:9px;'>Please make sure the zone does not exist in Transporter Manage Area Demarcation/ Rate Master </span>";
			else $errDel	=	$msg_failDelZoneMaster;
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
				$zoneRecConfirm = $zoneMasterObj->updateZoneconfirm($zoneId);
			}

		}
		if ($zoneRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmzone);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
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
				
					$zoneRecConfirm = $zoneMasterObj->updateZoneReleaseconfirm($zoneId);
				
			}
		}
		if ($zoneRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmzone);
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
	$zoneRecords = $zoneMasterObj->fetchAllPagingRecords($offset, $limit);
	$zoneRecordSize  = sizeof($zoneRecords);

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($zoneMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($addMode || $editMode ) {
		#List all State
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
	}

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else $mode = "";

	if ($editMode)	$heading =	$label_editZoneMaster;
	else 		$heading =	$label_addZoneMaster;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/ZoneMaster.js";  // Topleft Nav Settings

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmZoneMaster" action="ZoneMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>	
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>
		</tr>
		<?}?>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Manage Zone";
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%">
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
										<table cellpadding="0"  width="70%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
		<? if($editMode){?>
			<td colspan="2" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ZoneMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateZoneMaster(document.frmZoneMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ZoneMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateZoneMaster(document.frmZoneMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidZoneId" value="<?=$editZoneId;?>">		
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>	
	<tr>
	<td colspan="2" nowrap>
		<table border="0" width="100%">
			<tr>
				<td class="fieldName" nowrap >*Name</td>
				<td align="left">
					<input type="text" name="name" id="name" size="20" value="<?=$name;?>" autocomplete="off" onkeyup="xajax_chkZoneExist(document.getElementById('name').value, '<?=$mode?>', '<?=$editZoneId?>')">
				</td>
			</tr>
			<tr>
				<TD colspan="2" align="center">
				<table width="100%">
				<TR>
					<TD>
					<!--<fieldset>
					<legend class="listing-item">Area Demarcation</legend>-->
					<?php
						$entryHead = "Area Demarcation";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table>
					<!--  Dynamic Row adding starts here-->
						<tr>
							<td colspan="2" style="padding:10px;">
								<table cellspacing="1" cellpadding="2" id="tblAddAreaDemarcation" class="newspaperType">
								<TR align="center">
									<th class="listing-head" style="padding-left:5px; padding-right:5px; text-align:center;">*State</th>
									<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; text-align:center;">City</th>	
									<th>&nbsp;</th>
								</TR>	
								</table>
								<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="">
								</td>
							</tr>
							<tr><TD height="10"></TD></tr>
						<tr>
							<TD nowrap style="padding-left:5px; padding-right:5px;">
								<a href="###" id='addRow' onclick="javascript:addNewStateRow();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
							</TD>
						</tr>
						<!--  Dynamic Row adding ends here-->
					</table>
					<!--</fieldset>-->	
					<?php
						require("template/rbBottom.php");
					?>
					</TD>
				</TR>
				</table>	
				</TD>
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
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ZoneMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateZoneMaster(document.frmZoneMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ZoneMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateZoneMaster(document.frmZoneMaster);">	
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
			<td background="images/heading_bg.gif" class="pageName" nowrap="true" style="background-repeat:repeat-x">&nbsp;Manage Zone</td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$zoneRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintZoneMaster.php',700,600);"><? }?></td>
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
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
		if ($zoneRecordSize) {
			$i = 0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ZoneMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Area Demarcation</td>
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
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
		
		# Get selected Area's
		$getselAreaRecs = $zoneMasterObj->getAreaDemarcationRecs($zoneId);
	?>
<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$zoneId;?>" class="chkBox">
	</td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?php
			$numLine = 4;
			if (sizeof($getselAreaRecs)>0) {
				$nextRec = 0;				
				$cityName = "";
				foreach ($getselAreaRecs as $cR) {
					$selName = $cR[0];
					$nextRec++;
					if ($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
					if ($nextRec%$numLine == 0) echo "<br/>";
				}
			}
		?> 
	</td>	
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center" style="padding-left:3px; padding-right:3px;">
			<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$zoneId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ZoneMaster.php';"><? } ?>
		</td>
	<? }?>
	 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$zoneId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$zoneId;?>,'confirmId');" >
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
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ZoneMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ZoneMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$zoneRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintZoneMaster.php',700,600);"><? }?></td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
		<!--<tr><TD height="10"></TD></tr>-->
	<!--tr>
		<td height="10" align="center">
			<a href="StateMaster.php" class="link1">State</a>&nbsp;&nbsp;<a href="CityMaster.php" class="link1">City</a>&nbsp;&nbsp;<a href="AreaMaster.php" class="link1">Area</a>
		</td>
	</tr-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
	<?php 
		if ($addMode || $editMode ) {
	?>
		<script language="JavaScript">
			function addNewStateRow()
			{
				addNewAreaDemarcationRow('tblAddAreaDemarcation','','<?=$mode?>','');
			}		
		</script>
	<?php 
		}
	?>
	<?php
		if ($addMode) {
	?>
	<script language="JavaScript">
		window.onLoad = addNewStateRow();
	</script>
	<?php
		 }
	?>
	<script language="JavaScript" type="text/javascript">	
		<?	
			if (sizeof($areaDemarcationStateRecs)>0) {		
				$j=0;
				foreach ($areaDemarcationStateRecs as $dsr) {
					$stateEntryId	= $dsr[0];
					$selStateId		= $dsr[2];
		?>	
			addNewAreaDemarcationRow('tblAddAreaDemarcation','<?=$selStateId?>','<?=$mode?>','<?=$stateEntryId?>');
			xajax_getCityList('<?=$selStateId?>', '<?=$j?>', '<?=$mode?>', '<?=$stateEntryId?>'); 
		<?
				$j++;
				}
			}
		?>
	</script>
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
	//ensureInFrameset(document.frmZoneMaster);
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