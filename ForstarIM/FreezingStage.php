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
	if ($p["cmdAddNew"]!="" ) $addMode = true;
		
	# Add
	if ($p["cmdAdd"]!="") {
	
		$stage		= addSlash(trim($p["stage"]));
		$description	= addSlash(trim($p["description"]));
		$yield		= trim($p["yield"]);
		
		if ($stage!="") {

			$freezingStageRecIns	=	$freezingstageObj->addFreezingStage($stage, $description, $yield);
			
			if ($freezingStageRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddFreezingStage);
				$sessObj->createSession("nextPage",$url_afterAddFreezingStage.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddFreezingStage;
			}
			$freezingStageRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$freezingStageRec	=	$freezingstageObj->find($editId);
		
		$editFreezingStageId	=	$freezingStageRec[0];
		$freezingStageName	=	stripSlash($freezingStageRec[1]);
		$freezingStageDescr	=	stripSlash($freezingStageRec[2]);
		$yield			= 	$freezingStageRec[3];
	}


	if ($p["cmdSaveChange"]!="") {
		
		$freezingStageId		=	$p["hidFreezingStageId"];
		$stage			=	addSlash(trim($p["stage"]));
		$description	=	addSlash(trim($p["description"]));
		$yield		= trim($p["yield"]);
		
		if ($freezingStageId!="" && $stage!="") {
			$freezingStageRecUptd	= $freezingstageObj->updateFreezingStage($freezingStageId,$stage,$description, $yield);
		}
	
		if ($freezingStageRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateFreezingStage);
			$sessObj->createSession("nextPage",$url_afterUpdateFreezingStage.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFreezingStage;
		}
		$freezingStageRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$freezingStageId	= $p["delId_".$i];

			if ($freezingStageId!="") {
				$freezingStageRecDel	= $freezingstageObj->deleteFreezingStage($freezingStageId);			
			}
		}
		if ($freezingStageRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelFreezingStage);
			$sessObj->createSession("nextPage",$url_afterDelFreezingStage.$selection);
		} else {
			$errDel	=	$msg_failDelFreezingStage;
		}
		$freezingStageRecDel	=	false;
	}

if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$freezingStageId	=	$p["confirmId"];


			if ($freezingStageId!="") {
				// Checking the selected fish is link with any other process
				$freezingStageRecConfirm = $freezingstageObj->updateFreezingStageconfirm($freezingStageId);
			}

		}
		if ($freezingStageRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmfreezingStage);
			$sessObj->createSession("nextPage",$url_afterDelFreezingStage.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$freezingStageId = $p["confirmId"];

			if ($freezingStageId!="") {
				#Check any entries exist
				
					$freezingStageRecConfirm = $freezingstageObj->updateFreezingStageReleaseconfirm($freezingStageId);
				
			}
		}
		if ($freezingStageRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmfreezingStage);
			$sessObj->createSession("nextPage",$url_afterDelFreezingStage.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	#List All Record
	$freezingStageRecords		=	$freezingstageObj->fetchPagingRecords($offset, $limit);
	$freezingStageRecordSize	=	sizeof($freezingStageRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($freezingstageObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode)	$heading = $label_editFreezingStage;
	else $heading =	$label_addFreezingStage;
		
	$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/freezingstage.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmFreezingStage" action="FreezingStage.php" method="post">
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
								$bxHeader="Freezing Stage";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Freezing Stage </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%" >
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FreezingStage.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFreezingStage(document.frmFreezingStage);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FreezingStage.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFreezingStage(document.frmFreezingStage);">												</td>

												<?}?>
											</tr>
						<input type="hidden" name="hidFreezingStageId" value="<?=$editFreezingStageId;?>">
										<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center">
						<table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*RM Stage:</td>
                                                  <td class="listing-item">
							<input name="stage" type="text" id="stage" value="<?=$freezingStageName?>" size="14">
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*Yield:</td>
                                                <td class="listing-item">
							<input name="yield" type="text" id="yield" value="<?=$yield?>" size="4" style="text-align:right;">&nbsp;&nbsp;%
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Description:</td>
                                                  <td class="listing-item"><textarea name="description" rows="2" id="description"><?=$freezingStageDescr?></textarea></td>
                                                </tr>
                                              </table></td>
					</tr>
					<tr>
						  <td colspan="2" height="5"></td>
					  </tr>
											<tr>
												<? if($editMode){?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FreezingStage.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFreezingStage(document.frmFreezingStage);">
												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FreezingStage.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddFreezingStage(document.frmFreezingStage);">												</td>

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
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$freezingStageRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFreezingStage.php',700,600);"><? }?></td>
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
							<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($freezingStageRecords) > 0 )
												{
													$i	=	0;
											?>
	<thead>
											<? if($maxpage>1){?>
<tr>
<td colspan="5" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"FreezingStage.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FreezingStage.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"FreezingStage.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<th nowrap style="padding-left:10px; padding-right:10px;"> RM Stage </th>
												<th nowrap style="padding-left:10px; padding-right:10px;">Yield<br>(%)</th>
												<th style="padding-left:10px; padding-right:10px;">Description</th>
												<? if($edit==true){?>
												<th width="50">&nbsp;</th>
												<? }?>
												<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
											</tr>
	</thead>
	<tbody>
											<?
											foreach($freezingStageRecords as $fsr)
											{
												$i++;
												$freezingStageId	= $fsr[0];
												$freezingStageName	= stripSlash($fsr[1]);
												$freezingStageDescr	= stripSlash($fsr[2]);
												$fsYield		= $fsr[3];
												$active=$fsr[4];
												$existingrecords=$fsr[5];
											?>
											<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20" align="center">
												<?php 
												if ($existingrecords==0) {?>
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$freezingStageId;?>" class="chkBox"></td>
												<?php 
												}
												?>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezingStageName;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=($fsYield!=0)?$fsYield:"";?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezingStageDescr;?></td>
												<? if($edit==true){?>
											  <td class="listing-item" width="50" align="center">
											   <?php if ($active!=1) {?>
											  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$freezingStageId;?>,'editId');">
											 <? } ?>
											  </td>
											  <? }?>


											  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$freezingStageId;?>,'confirmId');"   >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$freezingStageId;?>,'confirmId');"   >
			<?php
			//}
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
<td colspan="5" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"FreezingStage.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FreezingStage.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"FreezingStage.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$freezingStageRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFreezingStage.php',700,600);"><? }?></td>
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