<?php
	//require("include/include.php");
	//require_once('lib/AreaDemarcationMaster_ajax.php');	

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$cUserId	= $sessObj->getValue("userId");
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
	if ($p["cmdCancel"]!="") $addMode = false;

	# Value Re-setting	
	if ($p["name"]!="") $name = $p["name"];
			
	# Add a Record
	if ($p["cmdAdd"]!="") {
		$selZone	= $p["selZone"];
		$rowCount 	= $p["hidTableRowCount"];
				
		# Check Duplicate Entry
		//$duplicateEntry = $areaDemarcationMasterObj->chkDuplicateEntry($wtFrom, $wtTo, $cRtCtId);	&& !$duplicateEntry

		if ($selZone!="" && $rowCount>0) {
			$areaDemarcationRecIns = $areaDemarcationMasterObj->addAreaDemarcation($selZone, $cUserId);

			#Find the Last inserted Id From Main Table
			$lastId = $databaseConnect->getLastInsertedId();
						
			for ($i=0; $i<$rowCount; $i++) {
			    $status = $p["status_".$i];
			    if ($status!='N') {
				$selStateId	= $p["state_".$i];
				$selCity	= $p["city_".$i];
					if ($lastId!="" && $selStateId!="") {
						$areaDemarcationStateRecIns = $areaDemarcationMasterObj->addAreaDemarcationState($lastId, $selStateId, $selCity);
					}
				}
			}	

			if ($areaDemarcationRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddAreaDemarcationMaster);
				$sessObj->createSession("nextPage",$url_afterAddAreaDemarcationMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddAreaDemarcationMaster;
			}
			$areaDemarcationRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddAreaDemarcationMaster;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$areaDemarcationRec	= $areaDemarcationMasterObj->find($editId);
		$editAreaDemarcationId	= $areaDemarcationRec[0];
		$selZone		= $areaDemarcationRec[1];

		# State Records
		$areaDemarcationStateRecs  = $areaDemarcationMasterObj->getAreaDemarcationStateRecords($editAreaDemarcationId);	
	}

	# Update a record
	if ($p["cmdSaveChange"]!="") {
		$areaDemarcationId 	= $p["hidAreaDemarcationId"];

		$selZone	= $p["selZone"];
		$rowCount 	= $p["hidTableRowCount"];
		
		if ($areaDemarcationId!="" && $selZone!="") {
			$areaDemarcationRecUptd = $areaDemarcationMasterObj->updateAreaDemarcation($areaDemarcationId, $selZone);
			for ($i=0; $i<$rowCount; $i++) {
			    $status 		= $p["status_".$i];
			    $stateEntryId	= $p["hidStateEntryId_".$i];
			    if ($status!='N') {
				$selStateId	= $p["state_".$i];
				$selCity	= $p["city_".$i];
					if ($areaDemarcationId!="" && $selStateId!="" && $stateEntryId=="") {
						$areaDemarcationStateRecIns = $areaDemarcationMasterObj->addAreaDemarcationState($areaDemarcationId, $selStateId, $selCity);
					} else if ($areaDemarcationId!="" && $selStateId!="" && $stateEntryId!="") {
						$updateDemarcationStateRec = $areaDemarcationMasterObj->updateDemarcationState($stateEntryId, $selStateId, $selCity);
					}
				} // Status=Y Loop ends here
				  # Delete the state IF Status=N	
			        if ($status=='N' && $stateEntryId!="") {
					# Delete Removed Rec
					$delRemovedRec = $areaDemarcationMasterObj->delRemovedDistRec($stateEntryId);
				}
			   }	
	
			} // For Loop ends Here	
		//}
	
		if ($areaDemarcationRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succAreaDemarcationMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateAreaDemarcationMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failAreaDemarcationMasterUpdate;
		}
		$areaDemarcationRecUptd	=	false;
	}

	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount = $p["hidRowCount"];
		$existCount = 0;
		for ($i=1; $i<=$rowCount; $i++) {
			$areaDemarcationId = $p["delId_".$i];

			if ($areaDemarcationId!="") {
				# Check Area Demarcation in use
				$areaDemarcationRecInUse = $areaDemarcationMasterObj->areaDemarcationRecInUse($areaDemarcationId);
				if (!$areaDemarcationRecInUse) {
					# Need to check the selected Category is link with any other process
					$delAreaDemacationEntryRecs = $areaDemarcationMasterObj->delAreaDemarcationEntryRecs($areaDemarcationId);	
					# Delete rec
					$areaDemarcationRecDel = $areaDemarcationMasterObj->deleteAreaDemarcation($areaDemarcationId);
				} // Checking ends here
				if ($areaDemarcationRecInUse) $existCount++;
			}
		}
		if ($areaDemarcationRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelAreaDemarcationMaster);
			$sessObj->createSession("nextPage",$url_afterDelAreaDemarcationMaster.$selection);
		} else {
			if ($existCount>0) $errDel	= $msg_failDelAreaDemarcationMaster."<br>The selected Area Demarcation is in use. <br><span style='font-size:9px;'>Please make sure the zone does not exist in Transporter Rate Master </span>";
			else $errDel	=	$msg_failDelAreaDemarcationMaster;
		}
		$areaDemarcationRecDel	=	false;
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") 		$pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="") 	$pageNo = $g["pageNo"];
	else 				$pageNo = 1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$areaDemarcationRecords = $areaDemarcationMasterObj->fetchAllPagingRecords($offset, $limit);
	$areaDemarcationRecordSize  = sizeof($areaDemarcationRecords);

	## -------------- Pagination Settings II -------------------
	//$fetchAllAreaDemarcationRecords = $areaDemarcationMasterObj->fetchAllRecords();
	$numrows	=  sizeof($areaDemarcationMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode ) {
		# Get All Zone Records
		$zoneRecords = $zoneMasterObj->fetchAllRecords();	
		
		#List all State
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
	}

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else $mode = "";


	if ($editMode)	$heading =	$label_editAreaDemarcationMaster;
	else 		$heading =	$label_addAreaDemarcationMaster;
	

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/AreaDemarcationMaster.js";  // Topleft Nav Settings

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmAreaDemarcationMaster" action="AreaDemarcationMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="55%" >
	<tr><TD height="10"></TD></tr>
	<!--tr>
		<td height="10" align="center">
			<a href="StateMaster.php" class="link1">State</a>&nbsp;&nbsp;<a href="CityMaster.php" class="link1">City</a>&nbsp;&nbsp;<a href="AreaMaster.php" class="link1">Area</a>
		</td>
	</tr-->
		<tr>
			<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		<td>		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AreaDemarcationMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAreaDemarcationMaster(document.frmAreaDemarcationMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AreaDemarcationMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " id="cmdAdd" onClick="return validateAreaDemarcationMaster(document.frmAreaDemarcationMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidAreaDemarcationId" value="<?=$editAreaDemarcationId;?>">		
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr>
		<td class="fieldName" nowrap >*Zone </td>
		<td>
			<select name="selZone" id="selZone" onchange="xajax_chkZoneExist(document.getElementById('selZone').value, '<?=$mode?>', '<?=$editAreaDemarcationId?>')">
				<option value="">-- Select --</option>
				<?php
					foreach ($zoneRecords as $zr) {
						$zoneId		= $zr[0];		
						$zoneName 	= stripSlash($zr[2]);
						$selected = "";
						if ($selZone==$zoneId) $selected = "selected";
				?>
				<option value="<?=$zoneId?>" <?=$selected?>><?=$zoneName?></option>
				<?php
					}
				?>			
			</select>
		</td>
	</tr>	
	<tr>
		<td class="fieldName" nowrap colspan="2">			
		</td>
	</tr>
	<!--  Dynamic Row adding starts here-->
<tr>
	<td colspan="2" style="padding-left:5px; padding-right:5px;">
		<table cellspacing="1" bgcolor="#999999" cellpadding="2" id="tblAddAreaDemarcation">
		<TR bgcolor="#f2f2f2" align="center">
			<td class="listing-head" style="padding-left:5px; padding-right:5px;">*State</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">City</td>	
			<td></td>
		</TR>	
		</table>
		<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
		</td>
	</tr>
	<tr><TD height="10"></TD></tr>
<tr>
	<TD nowrap style="padding-left:5px; padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewStateRow();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
<!--  Dynamic Row adding ends here-->
	<tr>

		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AreaDemarcationMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateAreaDemarcationMaster(document.frmAreaDemarcationMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AreaDemarcationMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateAreaDemarcationMaster(document.frmAreaDemarcationMaster);">	
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
		</td>
			</tr>
		</table>
		<!-- Form fields end   -->
		</td>
	</tr>	
		<?php
			}
			# Listing Category Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						<tr>
							<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
			<td background="images/heading_bg.gif" class="pageName" nowrap="true" style="background-repeat:repeat-x">&nbsp;Manage Area Demarcation</td>
			<td background="images/heading_bg.gif" class="pageName" align="right" nowrap="true" style="background-repeat:repeat-x">
			</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$areaDemarcationRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAreaDemarcationMaster.php',700,600);"><? }?></td>
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
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($areaDemarcationRecordSize) {
			$i = 0;
	?>

	<? if($maxpage>1){ ?>
		<tr bgcolor="#FFFFFF">
		<td colspan="4" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AreaDemarcationMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AreaDemarcationMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AreaDemarcationMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Zone</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Area Demarcation</td>
		<? if($edit==true){?>
		<td class="listing-head" ></td>
		<? }?>
	</tr>
	<?php
	foreach ($areaDemarcationRecords as $wsr) {
		$i++;
		$areaDemarcationId	= $wsr[0];		
		$zoneName 	= stripSlash($wsr[2]);
		# Get selected Area's
		$getselAreaRecs = $areaDemarcationMasterObj->getAreaDemarcationRecs($areaDemarcationId);
		
		/*
		echo "<pre>";
		print_r($getselAreaRecs);
		echo "</pre>";
		*/
	?>
<tr  bgcolor="WHITE">
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$areaDemarcationId;?>" class="chkBox">
	</td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?=$zoneName;?>
	</td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getselAreaRecs)>0) {
						$nextRec	=	0;
						$k=0;
						$cityName = "";
						foreach ($getselAreaRecs as $cR) {
							$j++;
							$selName = $cR[0];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$selName?></td>
						<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php
							}	
					 	}
					}
				?>
				</tr>
		</table>
	</td>	
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$areaDemarcationId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='AreaDemarcationMaster.php';"></td>
	<? }?>
	</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="4" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AreaDemarcationMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AreaDemarcationMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AreaDemarcationMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$areaDemarcationRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAreaDemarcationMaster.php',700,600);"><? }?></td>
											</tr>
										</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
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
	ensureInFrameset(document.frmAreaDemarcationMaster);
	//-->
	</script>
<?php 
	}
?>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>