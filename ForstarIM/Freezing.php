<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$glazeOperator		=	1;

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
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------	
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	

	# Add
	if ($p["cmdAdd"]!="" ) {
	
		$name		=	addSlash(trim($p["name"]));
		$description	=	addSlash(trim($p["description"]));
		$glazeCalc	=	$p["glazeCalc"];
		
		if ($name!="") {
			$freezingRecIns	=	$freezingObj->addFreezing($name,$description,$glazeCalc);
			
			if ($freezingRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddFreezing);
				$sessObj->createSession("nextPage",$url_afterAddFreezing.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddFreezing;
			}
			$freezingRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$freezingRec		=	$freezingObj->find($editId);
		
		$editFreezingId		=	$freezingRec[0];
		$freezingName		=	stripSlash($freezingRec[1]);
		$freezingDescr		=	stripSlash($freezingRec[2]);
		$glazeOperator		=	$freezingRec[3];
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$freezingId		=	$p["hidFreezingId"];
		$name			=	addSlash(trim($p["name"]));
		$description		=	addSlash(trim($p["description"]));
		$glazeCalc		=	$p["glazeCalc"];
				
		if ( $freezingId!="" && $name!="" ) {
			$freezingRecUptd = $freezingObj->updateFreezing($freezingId,$name,$description,$glazeCalc);
		}
	
		if ($freezingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateFreezing);
			$sessObj->createSession("nextPage",$url_afterUpdateFreezing.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFreezing;
		}
		$freezingRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$freezingId	= $p["delId_".$i];

			if ($freezingId!="") {
				$freezingRecDel = $freezingObj->deleteFreezing($freezingId);
				
			}
		}
		if ($freezingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelFreezing);
			$sessObj->createSession("nextPage",$url_afterDelFreezing.$selection);
		} else {
			$errDel	=	$msg_failDelFreezing;
		}
		$freezingRecDel	=	false;
	}
	


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$freezingId	=	$p["confirmId"];
			if ($freezingId!="") {
				// Checking the selected fish is link with any other process
				$freezRecConfirm = $freezingObj->updatefreezingconfirm($freezingId);
			}

		}
		if ($freezRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmfreez);
			$sessObj->createSession("nextPage",$url_afterDelFreezing.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$freezingId = $p["confirmId"];
			if ($freezingId!="") {
				#Check any entries exist				
					$freezRecConfirm = $freezingObj->updatefreezingReleaseconfirm($freezingId);				
			}
		}
		if ($freezRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmfreez);
			$sessObj->createSession("nextPage",$url_afterDelFreezing.$selection);
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
	$freezingRecords		=	$freezingObj->fetchPagingRecords($offset, $limit);
	$freezingRecordSize	=	sizeof($freezingRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($freezingObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) $heading = $label_editFreezing;
	else $heading = $label_addFreezing;
	
	$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/freezing.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmFreezing" action="Freezing.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
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
								$bxHeader="Freezing Style";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('Freezing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFreezing(document.frmFreezing);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('Freezing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFreezing(document.frmFreezing);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidFreezingId" value="<?=$editFreezingId;?>">				
											

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center"> 
				<table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap"> * Name:</td>
                                                  <td class="listing-item"><input name="name" type="text" id="name" value="<?=$freezingName?>"></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Description:</td>
                                                  <td class="listing-item"><textarea name="description" rows="1" id="description"><?=$freezingDescr?></textarea></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Glaze % </td>
                                                  <td class="listing-item">
												  <select name="glazeCalc" id="glazeCalc">
												  <option value="1" <? if($glazeOperator==1) echo "Selected";?>>Add</option>
												  <option value="0" <? if($glazeOperator==0) echo "Selected";?>>Deduct</option>
												   <option value="2" <? if($glazeOperator==2) echo "Selected";?>>None</option>
                                                  </select>
                                                  </td>
                                                </tr>
                                              </table></td>
											</tr>
										<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Freezing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFreezing(document.frmFreezing);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Freezing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFreezing(document.frmFreezing);">												</td>

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
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Freezing Style</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$freezingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFreezing.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="") {
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
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
			if (sizeof($freezingRecords) > 0 )  {
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
					$nav.= " <a href=\"Freezing.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Freezing.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"Freezing.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Code</th>
		<th style="padding-left:10px; padding-right:10px;">Glaze%</th>
		<th style="padding-left:10px; padding-right:10px;">Description</th>
		<? if($edit==true){?>
		<th width="50">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th width="50">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($freezingRecords as $fr) {
		$i++;
		$freezingId	=	$fr[0];
		$freezingName	=	stripSlash($fr[1]);
		$freezingDescr	=	stripSlash($fr[2]);
		$glazeOperator	=	$fr[3];
		$displayOperator = "";
		if ($glazeOperator==1) {
			$displayOperator = "Add";
		} else if($glazeOperator==0) {
			$displayOperator = "Deduct";
		} else {
			$displayOperator = "None";
		}
		$active=$fr[4];
		$existingcount=$fr[5];
		
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
		<?php
		if($existingcount){?>
		 <input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$freezingId;?>" class="chkBox"></td>
		<?php
		}
		?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezingName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$displayOperator?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$freezingDescr;?></td>
		<? if($edit==true){?>
		  <td class="listing-item" width="50" align="center">
		   <?php if ($active!=1) {?>
		  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$freezingId;?>,'editId');">
		  <? } ?>
		  </td>
		  <? }?>
		  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$freezingId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$freezingId;?>,'confirmId');"  >
			<?php 
//			}
			}?>
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
					$nav.= " <a href=\"Freezing.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Freezing.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"Freezing.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$freezingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFreezing.php',700,600);"><? }?></td>
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