<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$recUpdated 	= false;
	
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
	
	#For Refreshing the main Window when click PopUp window
	if ($g["popupWindow"]=="") $popupWindow = $p["popupWindow"];
	else $popupWindow = $g["popupWindow"];

	# Add New	
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdAdd"]!="") {
		$code		=	addSlash(trim($p["code"]));
		$description	=	addSlash(trim($p["description"]));
		$numPacks	=	$p["numPacks"];
		
		if ($code!="") {
			$mcpackingRecIns	= $mcpackingObj->addMCPacking($code,$numPacks,$description);
			
			if ($mcpackingRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddMCPacking);
				$sessObj->createSession("nextPage",$url_afterAddMCPacking.$selection);
				$recUpdated 	= true;
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddMCPacking;
			}
			$mcpackingRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$mcpackingRec		=	$mcpackingObj->find($editId);
		
		$editMCPackingId		=	$mcpackingRec[0];
		$mcpackingName		=	stripSlash($mcpackingRec[1]);
		$numPacks			=	$mcpackingRec[2];
		$mcpackingDescr		=	stripSlash($mcpackingRec[3]);
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$mcpackingId		=	$p["hidMCPackingId"];
		$code			=	addSlash(trim($p["code"]));
		$description	=	addSlash(trim($p["description"]));
		$numPacks		=	$p["numPacks"];
		
		if ($mcpackingId!="" && $code!="") {
			$mcpackingRecUptd	=	$mcpackingObj->updateMCPacking($mcpackingId,$code,$numPacks,$description);
		}
	
		if($mcpackingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateMCPacking);
			$sessObj->createSession("nextPage",$url_afterUpdateMCPacking.$selection);
			$recUpdated 	= true;
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateMCPacking;
		}
		$mcpackingRecUptd	=	false;
	}
	
	
	# Delete
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$mcpackingId	=	$p["delId_".$i];

			if ($mcpackingId!="") {
				$mcpackingRecDel = $mcpackingObj->deleteMCPacking($mcpackingId);
			}
		}
		if ($mcpackingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelMCPacking);
			$sessObj->createSession("nextPage",$url_afterDelMCPacking.$selection);
			$recUpdated = true;
		} else {
			$errDel	=	$msg_failDelMCPacking;
		}
		$mcpackingRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$mcpackingId	=	$p["confirmId"];


			if ($mcpackingId!="") {
				// Checking the selected fish is link with any other process
				$MCPackingRecConfirm = $mcpackingObj->updateMCPackingconfirm($mcpackingId);
			}

		}
		if ($MCPackingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmMCPacking);
			$sessObj->createSession("nextPage",$url_afterDelMCPacking.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmFishCategory;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$mcpackingId = $p["confirmId"];

			if ($mcpackingId!="") {
				#Check any entries exist
				
					$MCPackingRecConfirm = $mcpackingObj->updateMCPackingReleaseconfirm($mcpackingId);
				
			}
		}
		if ($MCPackingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmMCPacking);
			$sessObj->createSession("nextPage",$url_afterDelMCPacking.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmFishCategory;
		}
		}
	
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Record	
	$mcpackingRecords		=	$mcpackingObj->fetchPagingRecords($offset,$limit);
	$mcpackingRecordSize	=	sizeof($mcpackingRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($mcpackingObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
## ----------------- Pagination Settings II End ------------

	if ($editMode)	$heading = $label_editMCPacking;
	else $heading = $label_addMCPacking;
	
	$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/mcpacking.js";

	# Include Template [topLeftNav.php]
	//require("template/topLeftNav.php");
	if (!$popupWindow) require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmMCPacking" action="MCPacking.php" method="post">
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
								$bxHeader="MC Packing";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; MC Packing </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('MCPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddMCPacking(document.frmMCPacking);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('MCPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddMCPacking(document.frmMCPacking);">												</td>

												<?}?>
											</tr>
								<input type="hidden" name="hidMCPackingId" value="<?=$editMCPackingId;?>">	
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center">
					 <table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*Code</td>
                                                  <td class="listing-item"><input name="code" type="text" id="code" value="<?=$mcpackingName?>" size="8"></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*No. of Packs</td>
                                                  <td class="listing-item"><input name="numPacks" type="text" id="numPacks" size="1" style="text-align:right" value="<?=$numPacks?>"></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Description</td>
                                                  <td class="listing-item"><textarea name="description" rows="2" id="description"><?=$mcpackingDescr?></textarea></td>
                                                </tr>
                                              </table></td>
					</tr>
					<tr>
						  <td colspan="2" height="5"></td>
					  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MCPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddMCPacking(document.frmMCPacking);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MCPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddMCPacking(document.frmMCPacking);">												</td>

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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$mcpackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintMCPacking.php',700,600);"><? }?></td>
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
												if( sizeof($mcpackingRecords) > 0 )
												{
													$i	=	0;
											?>
	<thead>
<? if($maxpage>1){?>
<tr>
<td colspan="4" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"MCPacking.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MCPacking.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"MCPacking.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		
		<th nowrap style="padding-left:10px; padding-right:10px">Code </th>
		<th style="padding-left:10px; padding-right:10px">Description</th>
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
		foreach($mcpackingRecords as $mcp) {
			$i++;
			$mcpackingId		=	$mcp[0];
			$mcpackingName	=	stripSlash($mcp[1]);
			$mcpackingDescr	=	stripSlash($mcp[3]);
			$active=$mcp[4];
			$existingrecords=$mcp[5];
	?>
											<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20" align="center">
												<?php 
												if ($existingrecords==0) {?>
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$mcpackingId;?>" class="chkBox"></td>
												<?php 
												}
												?>
												
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px"><?=$mcpackingName;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px"><?=$mcpackingDescr;?></td>
												<? if($edit==true){?>
											  <td class="listing-item" width="50" align="center">
											   <?php if ($active!=1) {?>
											  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$mcpackingId;?>,'editId');">
											  <? }?>
											  </td>
											  <? }?>

											  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$mcpackingId;?>,'confirmId');"   >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm"  onClick="assignValue(this.form,<?=$mcpackingId;?>,'confirmId');"  >
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
<td colspan="4" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"MCPacking.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MCPacking.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"MCPacking.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$mcpackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintMCPacking.php',700,600);"><? }?></td>
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
			<td height="10"><input type="hidden" name="popupWindow" id="popupWindow" value="<?=$popupWindow?>"></td>
		</tr>	
	</table>
	<?php 
		if ($recUpdated && $popupWindow!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		// Shipment purchase order:: MCP - MC PACKING
		parent.reloadDropDownList('MCP');	
	</script>
	<?php
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if (!$popupWindow) require("template/bottomRightNav.php");
?>