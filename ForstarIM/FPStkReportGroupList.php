<?php
	require("include/include.php");
	require_once("lib/FPStkReportGroupList_ajax.php");

	ob_start();

	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= false;
	$allocateMode		= false;
	$isSearched		= false;
	
	$selection 	= "?pageNo=".$p["pageNo"];
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false;
	
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
	//----------------------------------------------------------	

	# Reset Data
	if ($p["groupName"]!="") $groupName = $p["groupName"];	
	$getRawDataRecs = array();
	if ($p["selGroupList"]!="") $selGroupList = $p["selGroupList"];


	# Changing Display Order
	if ($g["up"]!="")		$displayChangeId   = $g["up"];			
	else if ($g["down"]!="")	$displayChangeId	= $g["down"];
	else $displayChangeId	= "";
	if ($displayChangeId!="" && ereg("^[0-9]*\-[0-9]*\;[0-9]*\-[0-9]", $displayChangeId)) {
		$updateDisplayOrder = $fpStkReportGroupListObj->changeDisplayOrder($displayChangeId);		
	}

	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode = true;
	}

	# Cancel 	
	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode	= false;		
		$fpStkReportGroupListId = "";
		$editId = "";
		$p["editId"] = "";
		$selGroupList ="";
		$p["selGroupList"] = "";
	}
	

	# Add
	if ($p["cmdAdd"]!="" || $p["cmdSaveAndAddNew"]!="") {	

		$groupName		= addSlash(trim($p["groupName"]));
		$sortOrder		= addSlash(trim($p["sortOrder"]));
		$freezingStyle		= $p["freezingStyle"];
		$freezingStage		= $p["freezingStage"];
		
		$tableRowCount		= $p["hidTableRowCount"];
				
		if ($groupName!="") {
			$fpStkReportGroupListRecIns =	$fpStkReportGroupListObj->addFPStkReportGroupList($groupName, $sortOrder, $freezingStyle, $freezingStage, $userId);

			$stkRGroupMainId = "";
			#Find the Last inserted Id 
			if ($fpStkReportGroupListRecIns) $stkRGroupMainId = $databaseConnect->getLastInsertedId();
			if ($tableRowCount>0 && $stkRGroupMainId) {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						//$selFish 	= $p["selFish_".$i];
						//$selProcessCode = $p["selProcessCode_".$i];
			
						$selQEL	= $p["selQEL_".$i];

						if ($selQEL!="" && $stkRGroupMainId) {
							$frznPkngQELEntryRecIns = $fpStkReportGroupListObj->addFPStkRawEntry($stkRGroupMainId, $selQEL);
						}
					}
				}
			} // Row Count Loop Ends Here
			 
						
			if ($fpStkReportGroupListRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddFPStkReportGroupList);
				
				if ($p["cmdAdd"]!="") {				
					$addMode = false;
					$sessObj->createSession("nextPage",$url_afterAddFPStkReportGroupList.$selection);
				} else if ($p["cmdSaveAndAddNew"]!="") {
					$editMode	= false;
					$addMode	= true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$groupName = "";
					$sortOrder = "";
					$freezingStyle	= "";
					$freezingStage	= "";
					$selGroupList 	= 	"";
					$p["selGroupList"]	=	"";
				} 
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddFPStkReportGroupList;
			}
			$fpStkReportGroupListRecIns	=	false;
		}
	}
	
	# Edit Packing
	if ($p["editId"]!="" || $selGroupList!="") {		
			
		//$editId	= $p["editId"];		
		//$editMode = true;
		if ($selGroupList) $editId = $selGroupList;
		else $editId	= $p["editId"];	
		if (!$selGroupList) $editMode = true;

		# Find Selected Rec
		$fpSReportGroupListRec	= $fpStkReportGroupListObj->find($editId);
		
		if (!$selGroupList) $fpStkReportGroupListId = $fpSReportGroupListRec[0];
		$groupName		= $fpSReportGroupListRec[1];
		$sortOrder		= $fpSReportGroupListRec[2];
		$selFreezingStyle	= $fpSReportGroupListRec[3];
		$selFreezingStage	= $fpSReportGroupListRec[4];
		
		# Get Entry Recs
		$getRawDataRecs = $fpStkReportGroupListObj->getSRGroupRawRecs($editId);	
		$qeListRecs = $fpStkReportGroupListObj->fetchAllQELRecords($selFreezingStyle, $selFreezingStage);	
	}


	# update
	if ($p["cmdSaveChange"]!="") {
		
		$fPStkReportGroupMainId = $p["hidGroupListId"];
		$groupName		= addSlash(trim($p["groupName"]));
		$sortOrder		= addSlash(trim($p["sortOrder"]));
		$freezingStyle		= $p["freezingStyle"];
		$freezingStage		= $p["freezingStage"];
		
		$tableRowCount		= $p["hidTableRowCount"];
		
		if ($fPStkReportGroupMainId!=0  && $groupName!="") {
			$updateFPStkReportGroupEntryRec = $fpStkReportGroupListObj->updateFPStkReportGroupRec($fPStkReportGroupMainId, $groupName, $sortOrder, $freezingStyle, $freezingStage);

			# Del Entry Recs
			$delRawDataEntryRecs = $fpStkReportGroupListObj->delSRGroupRawData($fPStkReportGroupMainId);

			if ($tableRowCount>0) {

				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						//$selFish 	= $p["selFish_".$i];
						//$selProcessCode = $p["selProcessCode_".$i];	
						$selQEL	= $p["selQEL_".$i];

						if ($selQEL!="" && $fPStkReportGroupMainId) {
							$frznPkngQELEntryRecIns = $fpStkReportGroupListObj->addFPStkRawEntry($fPStkReportGroupMainId, $selQEL);
						}
					}
				}
			} // Row Count Loop Ends Here								
		}
	
		if ($updateFPStkReportGroupEntryRec) {
			$sessObj->createSession("displayMsg", $msg_succUpdateFPStkReportGroupList);
			$sessObj->createSession("nextPage", $url_afterUpdateFPStkReportGroupList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFPStkReportGroupList;
		}
		$dailyFrozenPackingRecUptd	=	false;
	}

	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fPStkReportGroupMainId  = $p["delId_".$i];

			if ($fPStkReportGroupMainId!="") {
				# Del Entry Recs
				$delRawDataEntryRecs = $fpStkReportGroupListObj->delSRGroupRawData($fPStkReportGroupMainId);

				# delete Main Rec
				$fpStkReportGroupEntryRecDel = $fpStkReportGroupListObj->deleteFPStkReportGroupEntryRec($fPStkReportGroupMainId);
			}
		}
		if ($fpStkReportGroupEntryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelFPStkReportGroupList);
			$sessObj->createSession("nextPage", $url_afterDelFPStkReportGroupList.$selection);
		} else {
			$errDel	=	$msg_failDelFPStkReportGroupList;
		}

		$fpStkReportGroupEntryRecDel	=	false;
	}

		
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$fpStkReportGroupListRecs = $fpStkReportGroupListObj->fetchAllPagingRecords($offset, $limit);
	$fPStkReportGrListRecSize	= sizeof($fpStkReportGroupListRecs);

	$fetchAllFPSRGroupRecs = $fpStkReportGroupListObj->fetchAllRecords();

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($fetchAllFPSRGroupRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	


	if ($addMode || $editMode) {
		# List All Quick Entry List
		//$qelRecs = $frznPkngQuickEntryListObj->fetchAllRecords();

		#List Freezing Records
		$freezingRecords	= $freezingObj->fetchAllRecords();

		#List All Freezing Stage Record
		$freezingStageRecords	= $freezingstageObj->fetchAllRecords();

		#List All Fishes
		//$fishMasterRecords	= $fishmasterObj->fetchAllRecords();
	}


	if ($editMode) $heading	= $label_editFPStkReportGroupList;
	else $heading	= $label_addFPStkReportGroupList;	
	
	# Setting the mode
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/FPStkReportGroupList.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmFPStkReportGroupList" id="frmFPStkReportGroupList" action="FPStkReportGroupList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%">
		<? if($err!="" ){?>
			<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?php
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FPStkReportGroupList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFPStkReportGroupList(document.frmFPStkReportGroupList);">												</td>
			<?} else{?>
			<td align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FPStkReportGroupList.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateFPStkReportGroupList(document.frmFPStkReportGroupList);">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFPStkReportGroupList(document.frmFPStkReportGroupList);" value="save &amp; Add New">
		</td>
		<input type="hidden" name="cmdAddNew" value="1">
	<?}?>
	</tr>
	<input type="hidden" name="hidGroupListId" value="<?=$fpStkReportGroupListId;?>">	
	 <tr>
		<td colspan="2" height="10"></td>
	 </tr>	
<?php
			 if ($addMode) { 
		?>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top" align="center">
			<table width="75%" align="center" cellpadding="0" cellspacing="0">
			<TR><TD>
			<fieldset>
			<legend class="listing-item" onMouseover="ShowTip('Copy from existing Group List and save after editing.');" onMouseout="UnTip();">Copy From</legend>
			<table>
				<TR>
					<TD class="fieldName" onMouseover="ShowTip('Copy from existing Group List and save after editing.');" onMouseout="UnTip();">Group List</TD>
					<td nowrap="true">
						<select name="selGroupList" id="selGroupList" onchange="this.form.submit();">
							<option value="">-- Select --</option>
							<?php
							foreach ($fetchAllFPSRGroupRecs as $fsgl) {
								$cpfGroupListId 	= $fsgl[0];
								$cpfGroupName	 	= $fsgl[1];
								$selected = ($selGroupList==$cpfGroupListId)?"selected":""	
							?>
							<option value="<?=$cpfGroupListId?>" <?=$selected?>><?=$cpfGroupName?></option>
							<?php
								}
							?>
						</select>
					</td>
				</TR>
			</table>
			</fieldset>
			</TD></TR>
			</table>
		  </td>
		</tr>
		<?php
			} // Copy from Ends here
		?>

	<tr>
	<td colspan="2" align="center">
		<table width="50%" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<TD nowrap>
			<table>
				<TR>
				<TD valign="top" nowrap>
					<fieldset>
					<table>
						<tr>
							<TD valign="top" nowrap="true">
								<table>
									<tr>
										<td class="fieldName" nowrap="nowrap">*Name</td>
										<td nowrap>				 		
											<input type="text" id="groupName" name="groupName" size="14" value="<?=$groupName?>" autocomplete="off" onblur="xajax_chkGroupName(document.getElementById('groupName').value,'<?=$fpStkReportGroupListId?>');" />
											<div id="groupNameExistMSg" class="errMsg"></div>
											<input type="hidden" id="hidGroupName" name="hidGroupName" size="18" value="<?=$groupName?>" />
										</td>
									</tr>
									<tr>
										<td class="fieldName" nowrap="nowrap">*Sort Order</td>
										<td nowrap>				 		
											<input type="text" id="sortOrder" name="sortOrder" size="2" value="<?=$sortOrder?>" autocomplete="off" style="text-align:right;" onkeyup="xajax_chkSortOrder(document.getElementById('sortOrder').value,'<?=$fpStkReportGroupListId?>');" />
											<input type="hidden" id="hideSortOrder" name="hideSortOrder" readonly="true" />
											<div id="sortOrderMSg" class="errMsg"></div>
										</td>
									</tr>
								</table>
							</TD>
							<TD valign="top">&nbsp;</TD>
							<TD valign="top" nowrap="true">
								<table>
									<tr>
										<td class="fieldName" nowrap="nowrap">*Freezing Style</td>
										<td nowrap>
											<select name="freezingStyle" id="freezingStyle" onchange="xajax_filterQEList(document.getElementById('hidTableRowCount').value, document.getElementById('freezingStyle').value, document.getElementById('freezingStage').value);">
												<option value="">-- Select --</option>
												<?php
													foreach($freezingRecords as $fr) {
														$freezingId	= $fr[0];
														$freezingName	= stripSlash($fr[1]);
														$selected = ($selFreezingStyle==$freezingId)?"Selected":"";
												?>
													<option value="<?=$freezingId?>" <?=$selected?>><?=$freezingName?></option>
												<? }?>
											</select> 		
										</td>
									</tr>	
									<tr>
										<td class="fieldName" nowrap="nowrap">*Freezing Stage</td>
										<td nowrap>
											<select name="freezingStage" id="freezingStage" onchange="xajax_filterQEList(document.getElementById('hidTableRowCount').value, document.getElementById('freezingStyle').value, document.getElementById('freezingStage').value);">
											<option value="">-- Select --</option>
											<?php
												foreach($freezingStageRecords as $fsr) {
													$freezingStageId	= $fsr[0];
													$freezingStageCode	= stripSlash($fsr[1]);
													$selected		= ($selFreezingStage==$freezingStageId)?"selected":"";
											?>
											<option value="<?=$freezingStageId?>" <?=$selected?>><?=$freezingStageCode?></option>
											<? }?>
											</select>
											</td>
									</tr>
								</table>
							</TD>
						</tr>
					</table>
					</fieldset>
				</TD>								
				</TR>
			</table>
		</TD>
		</tr>
              </table></td>
	</tr>
		<tr>
			<TD align="center" colspan="2" style="padding-left:5px;padding-right:5px;">
				<table width="50%">
					<TR><TD>
				<fieldset>
				<table  align="center" cellpadding="0" cellspacing="0" border="0">
					<!--  Dynamic Row Starts Here style="padding-left:5px;padding-right:5px;"-->
		<tr id="catRow1">
			<td style="padding:5 5 5 5px;">
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddRawData">
				<tr bgcolor="#f2f2f2" align="center">
					<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Quick Entry List</td>
					<!--<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Process Code</td>	-->
					<td>&nbsp;</td>
				</tr>	
				<?php
					$j=0;					
					foreach ($getRawDataRecs as $rdr) {			
						$srgListEntryId = $rdr[0];
						$srgQELId 	= $rdr[1];
						//$sFishId    = $rdr[1];	
						//$sProcessCodeId = $rdr[2];
						# PC Recs
						//$pcRecords = $processcodeObj->getProcessCodeRecs($sFishId);
				?>
				<tr align="center" class="whiteRow" id="row_<?=$j?>">
					<td align="center" class="listing-item">
						<select id="selQEL_<?=$j?>" name="selQEL_<?=$j?>" style='width:200px;' onchange="xajax_setQELId('<?=$j?>', document.getElementById('selQEL_<?=$j?>').value);">
						<? if (sizeof($qeListRecs)<=0) { ?><option value="">-- Select --</option><?}?>
						<?php		
							if (sizeof($qeListRecs)>0) {	
								foreach ($qeListRecs as $qelId=>$qelName) {
									//$qelId		= $qel[0];
									//$qelName	= stripSlash($qel[1]);
									$selected = ($srgQELId==$qelId)?"selected":"";
						?>
						<option value="<?=$qelId?>" <?=$selected?>><?=$qelName?></option>
						<?php
								}
							}
						?>
						</select>

						<!--<select onchange="xajax_getProcessCodeRecords(document.getElementById('selFish_<?=$j?>').value, '<?=$j?>', '');" id="selFish_<?=$j?>" name="selFish_<?=$j?>">
						<option value="">-- Select --</option>
						<?php
							/*
						if (sizeof($fishMasterRecords)>0) {	
							foreach ($fishMasterRecords as $fr) {
								$fId		= $fr[0];
								$fishName	= stripSlash($fr[1]);
								$selected = ($sFishId==$fId)?"selected":"";
							*/
						?>
						<option value="<?=$fId?>" <?=$selected?>><?=$fishName?></option>
						<?php
							/*
								}
							}
							*/
						?>
						</select>-->
					</td>
					<!--<td align="center" class="listing-item">
						<select id="selProcessCode_<?=$j?>" name="selProcessCode_<?=$j?>">
						<?php
						/*
						if (sizeof($pcRecords)>0) {	
							foreach ($pcRecords as $pCodeId=>$pCode) {
								$selected = ($sProcessCodeId==$pCodeId)?"selected":"";
						*/
						?>
						<option value="<?=$pCodeId?>" <?=$selected?>><?=$pCode?></option>
						<?php
						/*
								}
							}
						*/
						?>
						</select>
					</td>-->					
					<td align="center" class="listing-item">
						<a onclick="setRowItemStatus('<?=$j?>', '<?=$mode?>', '<?=$userId?>', '<?=$fpStkReportGroupListId?>');" href="###">
						<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
						<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
						<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>"/>
						<input type="hidden" value="<?=$srgListEntryId?>" id="qelEntryId_<?=$j?>" name="qelEntryId_<?=$j?>"/>
						<input type="hidden" value="Y" id="pcFromDB_<?=$j?>" name="pcFromDB_<?=$j?>"/>	
						<input type='hidden' name='hidSelQEL_<?=$j?>'  id='hidSelQEL_<?=$j?>' value='<?=$srgQELId?>'>
					</td>
				</tr>
				<?php
						$j++;
					}
				?>
				</table>
			</td>
		</tr>
		<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$j?>" readonly />
		<input type="hidden" name="hidTRowCount" id="hidTRowCount" value="<?=sizeof($getRawDataRecs)?>" readonly />
	<!--  Dynamic Row Ends Here-->
		<tr id="catRow2"><TD height="5"></TD></tr>
		<tr id="catRow3">
			<TD style="padding-left:5px;padding-right:5px;">
				<a href="###" id='addRow' onclick="javascript:addNewRawData();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
			</TD>
		</tr>
			</table>
				</fieldset>
			</TD></TR>
			</table>
			</TD>
		</tr>
				<tr>
											  <td align="center">&nbsp;</td>
											  <td align="center">&nbsp;</td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FPStkReportGroupList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFPStkReportGroupList(document.frmFPStkReportGroupList);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FPStkReportGroupList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateFPStkReportGroupList(document.frmFPStkReportGroupList);">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFPStkReportGroupList(document.frmFPStkReportGroupList);" value="save &amp; Add New">												</td>

												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
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
		<?
			}
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Report Group List</td>
								    <td background="images/heading_bg.gif" class="pageName" align="right" ></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>			
			<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fPStkReportGrListRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFPStkReportGroupList.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?php
									if ($errDel!="")  {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
	<tr>
	<td width="1" ></td>
	<td colspan="2" style="padding-left:10px;padding-right:10px;" align="center">
	<table cellpadding="1"  width="55%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($fpStkReportGroupListRecs)>0) {
		$i	=	0;
	?>
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="7" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"FPStkReportGroupList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FPStkReportGroupList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FPStkReportGroupList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<td width="20" rowspan="2">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
	</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;" rowspan="2">Current<br> Position</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;" colspan="2">Sort</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;" rowspan="2">Name</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;" rowspan="2">Freezing Style</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;" rowspan="2">Freezing Stage</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;" rowspan="2">Quick Entry List</td>
	<? if($edit==true){?>
		<td class="listing-head" width="45" rowspan="2">&nbsp;</td>	
	<? }?>
	</tr>
	<tr align="center" bgcolor="#f2f2f2">
		<th align="center" style="padding-left:5px; padding-right:5px;" class="listing-head" width="60px;">Move Up</th>
		<th style="padding-left:5px; padding-right:5px;" class="listing-head" width="60px;">Move Down</th>
	</tr>
	<?php
	/*
	foreach ($fpStkReportGroupListRecs as $srgl) {
			$i++;
	*/
	for ($i=1; $i<=$fPStkReportGrListRecSize; $i++) {
		$srgl = $fpStkReportGroupListRecs[$i-1]; // Get Current Record
		$fRec   = $fpStkReportGroupListRecs[$i]; //Forward Record
		$pRec   = $fpStkReportGroupListRecs[$i-2]; // Prev Rec

			$fPStkReportGroupMainId = $srgl[0];
			$qEntryName	 	= $srgl[1];
			$srgSortOrder		= $srgl[2];
			$srgFreezingStyle	= $srgl[5];
			$srgFreezingStage	= $srgl[6];

			# Get Selected Process Coes
			$getQELGroupRecs = $fpStkReportGroupListObj->getQELGroupRecs($fPStkReportGroupMainId);

			# Display Settings	
			$disOrderUp		= "$pRec[0]-$srgl[2];$srgl[0]-$pRec[2]";	// Pass URL value		
			$disOrderDown	= "$fRec[0]-$srgl[2];$srgl[0]-$fRec[2]";
			//echo $disOrderUp."<------------->".$disOrderDown."<br>";
	?>

	<tr title="<?="Sort Order: ".$srgSortOrder?>" <?=$listRowMouseOverStyle?>>
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$fPStkReportGroupMainId;?>" class="chkBox">
	</td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="center"><?=$srgSortOrder?></td>
	<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
		<? if ($i>1 && $i!=$fPStkReportGrListRecSize) {?>
			<a href="FPStkReportGroupList.php?up=<?=$disOrderUp?>" class="displayArrow"><img src="images/arrow_up.gif" border="0" title="Move Up"></a>
		<? } ?>
		<?if ($i==$fPStkReportGrListRecSize) {?>
			<a href="FPStkReportGroupList.php?up=<?=$disOrderUp?>" class="displayArrow"><img src="images/arrow_up.gif" border="0" title="Move Up"></a>
		<? }?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<? if ($i==1) {?>
				<a href="FPStkReportGroupList.php?down=<?=$disOrderDown?>" class="displayArrow"><img src="images/arrow_down.gif" border="0" title="Move Down"></a>
			<? } ?>
			<? if ($i>1 && $i!=$fPStkReportGrListRecSize) {?>
				<a href="FPStkReportGroupList.php?down=<?=$disOrderDown?>" class="displayArrow"><img src="images/arrow_down.gif" border="0" title="Move Down"></a>
			<? } ?>
		</td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$qEntryName?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$srgFreezingStyle?></td>	
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$srgFreezingStage?></td>		
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left" nowrap>
		<?php
			$numCol = 4;
			if (sizeof($getQELGroupRecs)>0) {
				$nextRec = 0;
				$pcName = "";
				foreach ($getQELGroupRecs as $cR) {
					$pcName = $cR[1];
					$nextRec++;
					if($nextRec>1) echo ",&nbsp;"; echo $pcName;
					if($nextRec%$numCol == 0) echo "<br/>";
				}
			}						
		?>
	</td>
	<? if($edit==true){?>
		<td class="listing-item" width="45" align="center" style="padding-left:3px;padding-right:3px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$fPStkReportGroupMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='FPStkReportGroupList.php';" <?=$disabled?>></td>	
	<? }?>
	</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">	
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="editMode" value="<?=$editMode?>">
	<input type="hidden" name="allocateId" value="<?=$allocateId?>">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="7" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"FPStkReportGroupList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FPStkReportGroupList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FPStkReportGroupList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		} else 	{
	?>
	<tr bgcolor="white">
	<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	<input type="hidden" name="allocateMode" value="<?=$allocateMode?>">
	</table>
	<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
	<input type="hidden" name="entryId" id="entryId" value="<?=$entryId?>">
	  </td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fPStkReportGrListRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFPStkReportGroupList.php',700,600);"><? }?></td>
											</tr>
										</table>			</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>			
		<tr>
			<td height="10">
				<input type="hidden" name="hidArrangeGrade" id="hidArrangeGrade" value=""/>
				<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>"/>
			</td>
		</tr>	
	</table>
	<?php 
		if ($addMode || $editMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewRawData()
		{
			addNewRawDataRow('tblAddRawData');	
			//xajax_filterQEList(document.getElementById('hidTableRowCount').value, document.getElementById('freezingStyle').value, document.getElementById('freezingStage').value);
		}
	</SCRIPT>
	<?php 
		} 
	?>

	<?php
		if ($addMode && !sizeof($getRawDataRecs)) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewRawData();
	</SCRIPT>
	<?php 
		}
	?>	
	<!-- Edit Record -->
	<script type="text/javascript" language="JavaScript">
	<?php
		if (sizeof($getRawDataRecs)>0) {
	?>			
	// Set Item Row Size
	fieldId = '<?=sizeof($getRawDataRecs)?>';
	<?php
		} // Raw Data Size Check Ends
	?>

	<?php
	if ($selGroupList) {
	?>
	xajax_chkGroupName(document.getElementById('groupName').value,'<?=$fpStkReportGroupListId?>');
	xajax_chkSortOrder(document.getElementById('sortOrder').value,'<?=$fpStkReportGroupListId?>');
	<?php
	}
	?>

	</script>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>