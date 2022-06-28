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
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	//----------------------------------------------------------
	
	# Add New	
	if ($p["cmdAddNew"]!="" ) $addMode	= true;
	
	#Insert one record
	if ($p["cmdAdd"]!="") {
	
		$freezerName		=	addSlash(trim($p["freezerName"]));
		$capacity		=	$p["capacity"];
		$freezingTime		=	$p["freezingTime"];
		$freezerDescr		=	addslash($p["freezerDescr"]);
		
		if ($freezerName!="") {
			$freezerRecIns	=	$freezercapacityObj->addFreezerCapacity($freezerName, $capacity, $freezingTime, $freezerDescr);
			
			if ($freezerRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddFreezerCapacity);
				$sessObj->createSession("nextPage",$url_afterAddFreezerCapacity.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddFreezerCapacity;
			}
			$freezerRecIns	=	false;
		}
	}


	# Edit
	
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$freezerRec		=	$freezercapacityObj->find($editId);
		
		$editFreezerCapacityId	=	$freezerRec[0];
		$freezerName		=	stripSlash($freezerRec[1]);
		$capacity		=	$freezerRec[2];
		$freezingTime		=	$freezerRec[3];
		$freezerDescr		=	$freezerRec[4];
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$freezerId		=	$p["hidFreezerCapacityId"];
		$freezerName		=	addSlash(trim($p["freezerName"]));
		$capacity		=	$p["capacity"];
		$freezingTime		=	$p["freezingTime"];
		$freezerDescr		=	addslash($p["freezerDescr"]);

		if ($freezerId!="" && $freezerName!="") {
			$freezerRecUptd	=	$freezercapacityObj->updateFreezerCapacity($freezerId, $freezerName, $capacity, $freezingTime, $freezerDescr);
		}
	
		if ($freezerRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateFreezerCapacity);
			$sessObj->createSession("nextPage",$url_afterUpdateFreezerCapacity.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFreezerCapacity;
		}
		$freezerRecUptd	=	false;
	}
	
	
	# Delete
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$freezerId	=	$p["delId_".$i];
			if ($freezerId!="") {
				$freezerRecDel	= $freezercapacityObj->deleteFreezerCapacity($freezerId);
			}

		}
		if ($freezerRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelFreezerCapacity);
			$sessObj->createSession("nextPage",$url_afterDelFreezerCapacity.$selection);
		} else {
			$errDel	=	$msg_failDelFreezerCapacity;
		}
		$freezerRecDel	=	false;
	}


	if ($p["btnConfirm"]!="")	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$freezerId	=	$p["confirmId"];
			if ($freezerId!="") {
				// Checking the selected fish is link with any other process
				$freezerRecConfirm = $freezercapacityObj->updateFreezerCapacityconfirm($freezerId);
			}

		}
		if ($freezerRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmFreezerCapacity);
			$sessObj->createSession("nextPage",$url_afterDelFreezerCapacity.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$freezerId= $p["confirmId"];

			if ($freezerId!="") {
				#Check any entries exist
				
					$freezerRecConfirm = $freezercapacityObj->updateFreezerCapacityReleaseconfirm($freezerId);
				
			}
		}
		if ($freezerRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmFreezerCapacity);
			$sessObj->createSession("nextPage",$url_afterDelFreezerCapacity.$selection);
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
	$freezerRecords		=	$freezercapacityObj->fetchPagingRecords($offset, $limit);
	$freezerRecordSize	=	sizeof($freezerRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  	sizeof($freezercapacityObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) $heading = $label_editFreezerCapacity;
	else $heading	=	$label_addFreezerCapacity;
	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/freezercapacity.js";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmFreezerCapacity" action="FreezerCapacity.php" method="post">
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
								$bxHeader="Freezer Capacity";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Freezer Capacity </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FreezerCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFreezerCapacity(document.frmFreezerCapacity);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FreezerCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFreezerCapacity(document.frmFreezerCapacity);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidFreezerCapacityId" value="<?=$editFreezerCapacityId;?>">
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center"> <table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap> * Freezer Name</td>
                                                  <td class="listing-item"><input name="freezerName" type="text" id="freezerName" size="8" value="<?=$freezerName?>"></td>
                                                </tr>
                                                <tr>
                                                   <TD class="fieldName" nowrap>* Capacity</TD>
                                                   <TD><input name="capacity" type="text" id="capacity" size="5" value="<?=$capacity?>" style="text-align:right"></TD>
                                                                      </tr>
                                                                      <tr>
                                                                           <TD class="fieldName" nowrap>* Freezing Time(Hrs)</TD>
                                                                           <TD><input name="freezingTime" type="text" id="freezingTime" size="3" value="<?=$freezingTime?>" style="text-align:right"></TD>
                                                                      </tr>
                                                                      <tr>
                                                                           <TD class="fieldName" nowrap>Description</TD>
                                                                           <TD><textarea name="freezerDescr" rows="2" id="freezerDescr"><?=$freezerDescr?></textarea></TD>
                                                                      </tr>
                                                                 </table></td>
							</tr>
										<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FreezerCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFreezerCapacity(document.frmFreezerCapacity);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FreezerCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFreezerCapacity(document.frmFreezerCapacity);">												</td>

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
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$freezerRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFreezerCapacity.php',700,600);"><? }?></td>
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
				<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
				<?
				if (sizeof($freezerRecords) > 0) {
					$i	=	0;
				?>
	<thead>
	<? if($maxpage>1){?>
<tr>
<td colspan="6" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"FreezerCapacity.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FreezerCapacity.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"FreezerCapacity.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr align="center">
	<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
<th nowrap style="padding-left:10px; padding-right:10px;">Name </th>
<th nowrap style="padding-left:10px; padding-right:10px;">Capacity</th>
<th nowrap style="padding-left:10px; padding-right:10px;">Freezing Time<br>(Hrs) </th>
<th style="padding-left:10px; padding-right:10px;">Description</th>
<? if($edit==true){?>
<th width="45">&nbsp;</th>
<? }?>
<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
</tr>
</thead>
<tbody>
			<?
			foreach($freezerRecords as $fr)
			{
				$i++;
				$freezerId	=	$fr[0];
				$freezerName	=	stripSlash($fr[1]);
				$capacity	=	$fr[2];
				$freezingTime	=	$fr[3];
				$freezerDescr	=	stripSlash($fr[4]);
				$active=$fr[5];
			?>
<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
<td width="20"  align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$freezerId;?>" class="chkBox"></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezerName;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$capacity;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$freezingTime;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezerDescr;?></td>
<? if($edit==true){?>
  <td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$freezerId;?>,'editId');"></td>
											  <? }?>

											  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$freezerId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$freezerId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
											</tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="confirmId" value="">
											
	<? if($maxpage>1){?>
<tr>
<td colspan="6" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"FreezerCapacity.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FreezerCapacity.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"FreezerCapacity.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$freezerRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFreezerCapacity.php',700,600);"><? }?></td>
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
