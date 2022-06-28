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
	if ($p["cmdAddNew"]!="") $addMode = true;	

	# Add
	if ($p["cmdAdd"]!="") {	
		$euCode		= addSlash(trim($p["euCode"]));
		$euCodeDescr	= addslash($p["euCodeDescr"]);
		$euCodeAddr		= addslash($p["euCodeAddr"]);
		
		if ($euCode!="") {
			$euCodeRecIns	=	$eucodeObj->addEuCode($euCode, $euCodeDescr, $euCodeAddr);
			if ($euCodeRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddEuCode);
				$sessObj->createSession("nextPage",$url_afterAddEuCode.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddEuCode;
			}
			$euCodeRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$euCodeRec	=	$eucodeObj->find($editId);
		
		$editEuCodeId	=	$euCodeRec[0];
		$euCode		=	stripSlash($euCodeRec[1]);
		$description	=	stripSlash($euCodeRec[2]);
		$euCodeAddr		=	stripSlash($euCodeRec[3]);
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		$euCodeId	=	$p["hidEuCodeId"];
		$euCode		=	addSlash(trim($p["euCode"]));
		$euCodeDescr	=	addslash($p["euCodeDescr"]);
		$euCodeAddr		= addslash($p["euCodeAddr"]);

		if ($euCodeId!="" && $euCode!="") {
			$euCodeRecUptd	=	$eucodeObj->updateEuCode($euCodeId,$euCode,$euCodeDescr, $euCodeAddr);
		}
	
		if ($euCodeRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateEuCode);
			$sessObj->createSession("nextPage",$url_afterUpdateEuCode.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateEuCode;
		}
		$euCodeRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount  = $p["hidRowCount"];
		$recInUse  = false;

		for ($i=1; $i<=$rowCount; $i++)	{
			$euCodeId	=	$p["delId_".$i];
			//echo '$euCodeId';
			if ( $euCodeId!="" ) {

				/*# Check Brand In use
				$euCodeInUse = $eucodeObj->euCodeRecInUse($euCodeId);


				if (!$euCodeInUse)*/ $euCodeRecDel	=	$eucodeObj->deleteEuCode($euCodeId);
				//$recInUse  = true;
			}
		}
		if ($euCodeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelEuCode);
			$sessObj->createSession("nextPage",$url_afterDelEuCode.$selection);
			//echo '11';
		} else {
			if ($recInUse) $errDel = $msg_failDelEuCode." EU Code is already in use.<br>Please check in Shipment Purchase Order";
			else $errDel	=	$msg_failDelEuCode;
		}
		$euCodeRecDel	=	false;
	}
	

if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$euCodeId	=	$p["confirmId"];
			if ($euCodeId!="") {
				// Checking the selected fish is link with any other process
				$euCodeRecConfirm = $eucodeObj->updateEuCodeconfirm($euCodeId);
			}

		}
		if ($euCodeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmeuCode);
			$sessObj->createSession("nextPage",$url_afterAddEuCode.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		
}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$euCodeId = $p["confirmId"];

			if ($euCodeId!="") {
				#Check any entries exist
				
					$euCodeRecConfirm = $eucodeObj->updateEuCodeReleaseconfirm($euCodeId);
				
			}
		}
		if ($euCodeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmeuCode);
			$sessObj->createSession("nextPage",$url_afterAddEuCode.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}



	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "" ) $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	#List All Record	
	$euCodeRecords		=	$eucodeObj->fetchPagingRecords($offset, $limit);
	$euCodeRecordSize	=	sizeof($euCodeRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($eucodeObj->fetchAllRecords($confirm));
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode)	$heading = $label_editEuCode;
	else $heading = $label_addEuCode;
	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/eucode.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmEuCode" action="EuCode.php" method="post">
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
								$bxHeader="EU Code";
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">								
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('EuCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddEuCode(document.frmEuCode);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('EuCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddEuCode(document.frmEuCode);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidEuCodeId" value="<?=$editEuCodeId;?>">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center"> <table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap"> *Code</td>
                                                  <td class="listing-item"><input name="euCode" type="text" id="euCode" size="8" value="<?=$euCode?>"></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Description</td>
                                                  <td class="listing-item"><textarea name="euCodeDescr" id="euCodeDescr"><?=$description?></textarea></td>
                                                </tr>
												<tr>
													<td nowrap="" class="fieldName" title="MANUFACTURER/PROCESSOR/PACKER address in Shipment Invoice">Address</td>
													<td><textarea rows="4" cols="27" name="euCodeAddr"><?=$euCodeAddr?></textarea></td>
												</tr>
                                              </table></td>
											</tr>
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EuCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddEuCode(document.frmEuCode);">	
												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EuCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddEuCode(document.frmEuCode);">
												</td>

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
			# Listing Starts
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
												<td><? if($del==true){?>
												<input type="submit" style="background-color:#ff0000;color: white;" value=" Delete **" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$euCodeRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintEuCode.php',700,600);"><? }?></td>
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
												if( sizeof($euCodeRecords) > 0 )
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
					$nav.= " <a href=\"EuCode.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"EuCode.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"EuCode.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th style="padding-left:10px; padding-right:10px;">Description</th>
		<th style="padding-left:10px; padding-right:10px;">Address</th>
		<? if($edit==true){?>
		<th width="45">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
		foreach($euCodeRecords as $eucr) {
			$i++;
			$euCodeId		=	$eucr[0];
			$euCodeCode	=	stripSlash($eucr[1]);
			$description	=	stripSlash($eucr[2]);
			$address		=	stripSlash($eucr[3]);
			$active=$eucr[4];
			$existingcount=$eucr[5];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
<?php 
	//echo $existingcount ;
	//if ($existingcount==0) {?>
	<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$euCodeId;?>" class="chkBox">
<?php
	//}
?>
</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$euCodeCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=nl2br($address);?></td>
		<? if($edit==true){?>
		  <td class="listing-item" width="45" align="center">
		   <?php if ($active!=1) {?>
		  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$euCodeId;?>,'editId');">
		  <? } ?>
		  </td>
		  <? }?>

		  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$euCodeId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$euCodeId;?>,'confirmId');" >
			<?php 
			
			//}
			
			}?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="<?=$euCodeId;?>">
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
					$nav.= " <a href=\"EuCode.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"EuCode.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"EuCode.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete ** " style="background-color:#ff0000;color: white;" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$euCodeRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintEuCode.php',700,600);"><? }?></td>
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