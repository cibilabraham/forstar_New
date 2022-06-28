<?php
	require("include/include.php");
	//require_once("lib/ProcessCodeSequence_ajax.php");
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
	if ($p["qeName"]!="") $qeName = $p["qeName"];	
	$getRawDataRecs = array();

	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode = true;
		$editMode = false;
	}

	# Cancel 	
	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode	= false;		
		$fpStkReportGroupListId = "";
		$editId = "";
		$p["editId"] = "";
		$p["selQuickEntryList"] = "";
	}
	

	# Add
	if ($p["cmdAdd"]!="" || $p["cmdSaveAndAddNew"]!="") {	
		$qeName		= addSlash(trim($p["qeName"]));		
		
		$tableRowCount		= $p["hidTableRowCount"];
				
		if ($qeName!="") {
			$fpStkReportGroupListRecIns =	$processCodeSequenceObj->addFPStkReportGroupList($qeName, $userId);

			#Find the Last inserted Id 
			if ($fpStkReportGroupListRecIns) $stkRGroupMainId = $databaseConnect->getLastInsertedId();
			if ($tableRowCount>0) {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selQEL 	= $p["selQEL_".$i];
						if ($selQEL!="" && $stkRGroupMainId) {
							$frznPkngQELEntryRecIns = $processCodeSequenceObj->addFPStkRawEntry($stkRGroupMainId, $selQEL);
						}
					}
				}
			} // Row Count Loop Ends Here
			 
						
			if ($fpStkReportGroupListRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddProcessCodeSequence);
				
				if ($p["cmdAdd"]!="") {				
					$addMode = false;
					$sessObj->createSession("nextPage",$url_afterAddProcessCodeSequence.$selection);
				} else if ($p["cmdSaveAndAddNew"]!="") {
					$editMode	= false;
					$addMode	= true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$qeName = "";
				} 
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddProcessCodeSequence;
			}
			$fpStkReportGroupListRecIns	=	false;
		}
	}
	
	# Edit Packing
	if ($p["editId"]!="") {		
			
		$editId	= $p["editId"];		
		$editMode = true;

		# Find Selected Rec
		$fpSReportGroupListRec	= $processCodeSequenceObj->find($editId);
		
		$fpStkReportGroupListId = $fpSReportGroupListRec[0];
		$qeName			= $fpSReportGroupListRec[1];
		
		# Get Entry Recs
		$getRawDataRecs = $processCodeSequenceObj->getSRGroupRawRecs($fpStkReportGroupListId);		
	}


	# update
	if ($p["cmdSaveChange"]!="") {
		
		$fPStkReportGroupMainId = $p["hidGroupListId"];
		$qeName			= addSlash(trim($p["qeName"]));		
		$tableRowCount		= $p["hidTableRowCount"];
		
		if ($fPStkReportGroupMainId!=0  && $qeName!="") {
			$updateFPStkReportGroupEntryRec = $processCodeSequenceObj->updateFPStkReportGroupRec($fPStkReportGroupMainId, $qeName);

			# Del Entry Recs
			$delRawDataEntryRecs = $processCodeSequenceObj->delSRGroupRawData($fPStkReportGroupMainId);

			if ($tableRowCount>0) {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selQEL 	= $p["selQEL_".$i];						
						if ($selQEL!="" && $fPStkReportGroupMainId!="") {
							$frznPkngQELEntryRecIns = $processCodeSequenceObj->addFPStkRawEntry($fPStkReportGroupMainId, $selQEL);
						}
					}
				}
			} // Row Count Loop Ends Here								
		}
	
		if ($updateFPStkReportGroupEntryRec) {
			$sessObj->createSession("displayMsg", $msg_succUpdateProcessCodeSequence);
			$sessObj->createSession("nextPage", $url_afterUpdateProcessCodeSequence.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateProcessCodeSequence;
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
				$delRawDataEntryRecs = $processCodeSequenceObj->delSRGroupRawData($fPStkReportGroupMainId);

				# delete Main Rec
				$fpStkReportGroupEntryRecDel = $processCodeSequenceObj->deleteFPStkReportGroupEntryRec($fPStkReportGroupMainId);
			}
		}
		if ($fpStkReportGroupEntryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelProcessCodeSequence);
			$sessObj->createSession("nextPage", $url_afterDelProcessCodeSequence.$selection);
		} else {
			$errDel	=	$msg_failDelProcessCodeSequence;
		}

		$fpStkReportGroupEntryRecDel	=	false;
	}

	
	if ($addMode || $editMode) {
		# List All Quick Entry List
		$qelRecs = $frznPkngQuickEntryListObj->fetchAllRecords();
	}

	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	//$fpStkReportGroupListRecs = $processCodeSequenceObj->fetchAllPagingRecords($offset, $limit);
	$fPStkReportGrListRecSize	= sizeof($fpStkReportGroupListRecs);

	//$fetchAllFPSRGroupRecs = $processCodeSequenceObj->fetchAllRecords();

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($fetchAllFPSRGroupRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	# Get Process Code Sequence
	$processCodeSequenceRecs = $processCodeSequenceObj->getPCSeqRecs();
	$maxPCCount = $processCodeSequenceObj->getMaxPCCount();


	if ($editMode) $heading	= $label_editProcessCodeSequence;
	else $heading	= $label_addProcessCodeSequence;	
	
	# Setting the mode
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	
	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ProcessCodeSequence.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmProcessCodeSequence" id="frmProcessCodeSequence" action="ProcessCodeSequence.php" method="post">
<link href="libjs/drag/drag.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="libjs/drag/drag.js"></script>
<!--<script type="text/javascript" src="libjs/drag/jquery.tablednd05.js"></script>-->
<!--<link href="libjs/drag/jquery-ui.css" rel="stylesheet" type="text/css"/>-->
<!--<script src="libjs/drag/jquery-ui.min.js"></script>-->

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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCodeSequence.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFznPkngQuickEntryList(document.frmProcessCodeSequence);">												</td>
			<?} else{?>
			<td align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCodeSequence.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateFznPkngQuickEntryList(document.frmProcessCodeSequence);">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFznPkngQuickEntryList(document.frmProcessCodeSequence);" value="save &amp; Add New">
		</td>
		<input type="hidden" name="cmdAddNew" value="1">
	<?}?>
	</tr>
	<input type="hidden" name="hidGroupListId" value="<?=$fpStkReportGroupListId;?>">	
	 <tr>
		<td colspan="2" height="10"></td>
	 </tr>	
	<tr>
	<td colspan="2" align="center">
		<table width="50%" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<TD nowrap>
			<table>
				<TR>
				<TD valign="top" nowrap>
					<!--<fieldset>-->
					<table>
						<tr>
							<td class="fieldName" nowrap="nowrap">*Name</td>
							<td nowrap>				 		
								<input type="text" id="qeName" name="qeName" size="26" value="<?=$qeName?>" autocomplete="off" />
								<input type="hidden" id="hidQeName" name="hidQeName" size="18" value="<?=$qeName?>" />
							</td>
						</tr>
					</table>
					<!--</fieldset>-->	
				</TD>								
				</TR>
			</table>
		</TD>
		</tr>
              </table></td>
	</tr>
		<tr>
			<TD align="center" colspan="2" style="padding-left:5px;padding-right:5px;">
				<table width="50%"><TR><TD>
				<fieldset>
				<table  align="center" cellpadding="0" cellspacing="0" border="0">
					<!--  Dynamic Row Starts Here style="padding-left:5px;padding-right:5px;"-->
		<tr id="catRow1">
			<td style="padding:5 5 5 5px;">
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddRawData">
				<tr bgcolor="#f2f2f2" align="center">
					<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">*Quick Entry List</td>	
					<td>&nbsp;</td>
				</tr>	
				<?php
					$j=0;					
					foreach ($getRawDataRecs as $rdr) {			
						$srgListEntryId = $rdr[0];
						$sQELId    = $rdr[1];						
				?>
				<tr align="center" class="whiteRow" id="row_<?=$j?>">
					<td align="center" class="listing-item">
						<select id="selQEL_<?=$j?>" name="selQEL_<?=$j?>" style='width:200px;'>
						<option value="">-- Select --</option>
						<?php
						if (sizeof($qelRecs)>0) {	
							foreach ($qelRecs as $qel) {
								$qelId		= $qel[0];
								$qelName	= stripSlash($qel[1]);
								$selected = ($sQELId==$qelId)?"selected":"";
						?>
						<option value="<?=$qelId?>" <?=$selected?>><?=$qelName?></option>
						<?php
								}
							}
						?>
						</select>
					</td>					
					<td align="center" class="listing-item">
						<a onclick="setRowItemStatus('<?=$j?>', '<?=$mode?>', '<?=$userId?>', '<?=$fpStkReportGroupListId?>');" href="###">
						<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
						<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
						<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>"/>
						<input type="hidden" value="<?=$srgListEntryId?>" id="qelEntryId_<?=$j?>" name="qelEntryId_<?=$j?>"/>
						<input type="hidden" value="Y" id="pcFromDB_<?=$j?>" name="pcFromDB_<?=$j?>"/>				 
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCodeSequence.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFznPkngQuickEntryList(document.frmProcessCodeSequence);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessCodeSequence.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateFznPkngQuickEntryList(document.frmProcessCodeSequence);">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:150px;" onclick="return validateFznPkngQuickEntryList(document.frmProcessCodeSequence);" value="save &amp; Add New">												</td>

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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Process Code Hierarchy</td>
								    <td background="images/heading_bg.gif" class="pageName" align="right" ></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>			
			<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($edit==true){?>
	<input type="button" value=" Update Order " name="btnUpdateOrder" class="button" >
<? }?>
<? //if($del==true){?>
<!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?//=$fPStkReportGrListRecSize;?>);">-->
<? //}?><!--&nbsp;-->
<? //if($add==true){?>
<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button">-->
<? //}?><!--&nbsp;-->
<? //if($print==true){?>
<!--<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessCodeSequence.php',700,600);">-->
<? //}?>
												</td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
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
	<?php
	if (sizeof($processCodeSequenceRecs)>0) {
	?>
	<tr>
		<TD colspan="2" style="padding-left:10px;padding-right:10px;" align="center">
<div id="drag">
			<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999" id="table1">
			<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:5px;padding-right:5px;">Fish</td>	
				<?php
				for ($ct=1; $ct<=$maxPCCount; $ct++) {
				?>
				<td class="listing-head" style="padding-left:5px;padding-right:5px;"><?=$ct?></td>
				<?php
				}
				?>
			</tr>
			<?php
			foreach ($processCodeSequenceRecs as $pcs) {
				$fishId 	= $pcs[0];
				$fishName 	= $pcs[1];
				$pcIdArr   	= explode(",",$pcs[2]);
				$pcCodeArr	= explode(",",$pcs[3]);
			?>
			
			<tr bgcolor="White">
				<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$fishName?></td>	
				<?php
				//for ($pcc=0; $pcc<sizeof($pcIdArr); $pcc++) {
				for ($pcc=0; $pcc<$maxPCCount; $pcc++) {
				?>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center" nowrap>
					<div id="<?=$pcIdArr[$pcc]?>" class="<?=($pcIdArr[$pcc])?"drag":""?>"><?=$pcCodeArr[$pcc]?></div>
				</td>
				<?php
				}
				?>
			</tr>
		
			<?php
			}
			?>
			</table>
</div>
		</TD>
	</tr>
	<?php
		}
	?>
<!--Ends here  -->
	<tr style="display:none;">
	<td width="1" ></td>
	<td colspan="2" style="padding-left:10px;padding-right:10px;" align="center">
	<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
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
      				$nav.= " <a href=\"ProcessCodeSequence.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProcessCodeSequence.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProcessCodeSequence.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
	</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Name</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Quick Entry List</td>		
	<? if($edit==true){?>
		<td class="listing-head" width="45">&nbsp;</td>	
	<? }?>
	</tr>
	<?php
	foreach ($fpStkReportGroupListRecs as $srgl) {
			$i++;
			$fPStkReportGroupMainId = $srgl[0];
			$qEntryName	 = $srgl[1];

			# Get Selected Process Coes
			$getQELGroupRecs = $processCodeSequenceObj->getQELGroupRecs($fPStkReportGroupMainId);
	?>

	<tr <?=$listRowMouseOverStyle?>>
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$fPStkReportGroupMainId;?>" class="chkBox">
	</td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$qEntryName?></td>	
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left" nowrap>
		<?php
			$numCol = 3;
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
		<td class="listing-item" width="45" align="center" style="padding-left:3px;padding-right:3px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$fPStkReportGroupMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='ProcessCodeSequence.php';" <?=$disabled?>></td>	
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
      				$nav.= " <a href=\"ProcessCodeSequence.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProcessCodeSequence.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProcessCodeSequence.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
									<td colspan="3" height="10" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($edit==true){?>
	<input type="button" value=" Update Order " name="btnUpdateOrder" class="button" >
<? }?>
<? //if($del==true){?>
<!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?//=$fPStkReportGrListRecSize;?>);">-->
<? //}?><!--&nbsp;-->
<? //if($add==true){?>
<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button">-->
<? //}?><!--&nbsp;-->
<? //if($print==true){?>
<!--<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessCodeSequence.php',700,600);">-->
<? //}?>
												</td>
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
			<td height="10"></td>
		</tr>	
	</table>
<!--<script type="text/javascript">
$(document).ready(function() {
    // Initialise the table
    $("#table1").tableDnD();
});
</script>-->

<script type="text/javascript">
			//window.onload = function () {
				// initialization
				REDIPS.drag.initDrag();
				
				// only "smile" can be placed to the marked cell
				//REDIPS.drag.mark.exception.d8 = 'smile';
				// prepare handlers
				REDIPS.drag.myhandler_clicked    = function () {}
				REDIPS.drag.myhandler_moved      = function () {}
				REDIPS.drag.myhandler_notmoved   = function () {}
				REDIPS.drag.myhandler_dropped    = function () {chk();}
				REDIPS.drag.myhandler_switched   = function () {}
				REDIPS.drag.myhandler_clonedend1 = function () {}
				REDIPS.drag.myhandler_clonedend2 = function () {}
				REDIPS.drag.myhandler_notcloned  = function () {}
				REDIPS.drag.myhandler_deleted    = function () {}
				REDIPS.drag.myhandler_undeleted  = function () {}
				REDIPS.drag.myhandler_cloned     = function () {
					// display message
					//document.getElementById('message').innerHTML = 'Cloned'
					// append 'd' to the element text (Clone -> Cloned)
					REDIPS.drag.obj.innerHTML += 'd';
				}
				REDIPS.drag.drop_option = "switch";
			//}
			// toggles trash_ask parameter defined at the top
			function toggle_confirm(chk) {
				REDIPS.drag.trash_ask = chk.checked;
			}
			// toggles delete_cloned parameter defined at the top
			function toggle_delete_cloned(chk) {
				REDIPS.drag.delete_cloned = chk.checked;
			}
			// enables / disables dragging
			function toggle_dragging(chk) {
				REDIPS.drag.enable_drag(chk.checked);
			}
			// function sets drop_option parameter defined at the top
			function set_drop_option(radio_button) {
				REDIPS.drag.drop_option = radio_button.value;
			}
			// show prepared content for saving
			function save(){
				// scan first table
				var content = REDIPS.drag.save_content(0);
				// if content doesn't exist
				if (content === '') {
					alert('Table is empty!');
				}
				// display query string
				else {
					//window.open('/my/multiple-parameters.php?' + content, 'Mypop', 'width=350,height=160,scrollbars=yes');
					window.open('multiple-parameters.php?' + content, 'Mypop', 'width=350,height=160,scrollbars=yes');
				}
			}

			function chk()
			{
				alert("h");
			}
		</script>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>