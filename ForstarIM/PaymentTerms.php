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
		$paymentMode	= addSlash(trim($p["paymentMode"]));
		$description	= addSlash(trim($p["modeDescription"]));
		$paymentRealization = trim($p["paymentRealization"]);
		
		if ($paymentMode!="") {
			$paymentTermRecIns	= $paymenttermsObj->addPaymentTerm($paymentMode, $description, $paymentRealization);
			
			if ($paymentTermRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddPaymentTerms);
				$sessObj->createSession("nextPage",$url_afterAddPaymentTerms.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddPaymentTerms;
			}
			$paymentTermRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$paymentTermRec	= $paymenttermsObj->find($editId);
		
		$editPaymentTermId	=	$paymentTermRec[0];
		$paymentMode		=	stripSlash($paymentTermRec[1]);
		$description		=	stripSlash($paymentTermRec[2]);
		$paymentRealization	= 	$paymentTermRec[3];

	}

	# Update	
	if ($p["cmdSaveChange"]!="") {
		
		$paymentTermId		=	$p["hidPaymentTermId"];
		$paymentMode		=	addSlash(trim($p["paymentMode"]));
		$description		=	addSlash(trim($p["modeDescription"]));
		$paymentRealization 	= 	trim($p["paymentRealization"]);
		
		if ($paymentTermId!="" && $paymentMode!="") {
			$paymentTermRecUptd	=	$paymenttermsObj->updatePaymentTerm($paymentTermId, $paymentMode, $description, $paymentRealization);
		}
	
		if ($paymentTermRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdatePaymentTerms);
			$sessObj->createSession("nextPage",$url_afterUpdatePaymentTerms.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdatePaymentTerms;
		}
		$paymentTermRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$paymentTermId	=	$p["delId_".$i];

			if ($paymentTermId!="") {
				$paymentTermRecDel	=	$paymenttermsObj->deletePaymentTerm($paymentTermId);
			}
		}
		if ($paymentTermRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPaymentTerms);
			$sessObj->createSession("nextPage",$url_afterDelPaymentTerms.$selection);
		} else {
			$errDel	=	$msg_failDelPaymentTerms;
		}
		$paymentTermRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$paymentTermId	=	$p["confirmId"];
			if ($paymentTermId!="") {
				// Checking the selected fish is link with any other process
				$paymentTermRecConfirm = $paymenttermsObj->updatePaymentTermconfirm($paymentTermId);
			}

		}
		if ($paymentTermRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmpaymentTerm);
			$sessObj->createSession("nextPage",$url_afterDelPaymentTerms.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$paymentTermId = $p["confirmId"];
			if ($paymentTermId!="") {
				#Check any entries exist
				
					$paymentTermRecConfirm = $paymenttermsObj->updatePaymentTermReleaseconfirm($paymentTermId);
				
			}
		}
		if ($paymentTermRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmpaymentTerm);
			$sessObj->createSession("nextPage",$url_afterDelPaymentTerms.$selection);
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
	$paymentTermRecords		= $paymenttermsObj->fetchPagingRecords($offset, $limit);
	$paymentTermRecordsize		= sizeof($paymentTermRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($paymenttermsObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($editMode) $heading	=	$label_editPaymentTerms;
	else $heading	=	$label_addPaymentTerms;
	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/paymentterms.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPaymentTerms" action="PaymentTerms.php" method="post">
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
								$bxHeader="Export Payment Terms";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Export Payment Terms </td>
								</tr>-->
								<tr>
									<td colspan="3"  align="center">
	<table align="center" width="50%">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('PaymentTerms.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPaymentTerm(document.frmPaymentTerms);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('PaymentTerms.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPaymentTerm(document.frmPaymentTerms);">												</td>

												<?}?>
											</tr>
									<input type="hidden" name="hidPaymentTermId" value="<?=$editPaymentTermId;?>">
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center"> 
					<table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*Mode</td>
                                                  <td class="listing-item"><input name="paymentMode" type="text" id="paymentMode" size="10" value="<?=$paymentMode?>"></td>
                                                </tr>
						<tr>
                                                  <td class="fieldName" nowrap="nowrap" title="No of days for payment realization">*Payment Realization</td>
                                                  <td class="listing-item" nowrap="true">
							<input name="paymentRealization" type="text" id="paymentRealization" size="3" value="<?=$paymentRealization?>" style="text-align:right;">&nbsp;Days
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Description</td>
                                                  <td class="listing-item"><textarea name="modeDescription" rows="3" id="modeDescription"><?=$description?></textarea></td>
                                                </tr>
                                              </table></td>
					</tr>
				<tr>
						  <td colspan="2" height="5"></td>
				  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PaymentTerms.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPaymentTerm(document.frmPaymentTerms);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PaymentTerms.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPaymentTerm(document.frmPaymentTerms);">												</td>

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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$paymentTermRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPaymentTerms.php',700,600);"><? }?></td>
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
										<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($paymentTermRecords) > 0 )
												{
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
					$nav.= " <a href=\"PaymentTerms.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PaymentTerms.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"PaymentTerms.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th nowrap style="padding-left:10px; padding-right:10px;">Mode </th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Payment Realization<br>(Days)</th>	
		<th style="padding-left:10px; padding-right:10px;">Description</th>
		<? if($edit==true){?>
			<th width="60">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
			<th width="60">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
		foreach($paymentTermRecords as $ptr){
			$i++;
			$paymentTermId		=	$ptr[0];
			$paymentMode		=	stripSlash($ptr[1]);
			$description		=	stripSlash($ptr[2]);
			$realizationDays 	= 	$ptr[3];
			$active=$ptr[4];
			$existingrecords=$ptr[5];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
			<?php
			if($existingrecords==0){
			?>
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$paymentTermId;?>" class="chkBox">
			<?php 
			}
			?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$paymentMode?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=($realizationDays)?$realizationDays:""?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
		<? if($edit==true){?>
		  <td class="listing-item" width="60" align="center">
		   <?php if ($active!=1) {?>
		  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$paymentTermId;?>,'editId');">
		  <? } ?>
		  </td>
		  <? }?>

		  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$paymentTermId;?>,'confirmId');"  >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0){ ?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$paymentTermId;?>,'confirmId');"  >
			<?php 
			//}
			}
			}?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?php
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
					$nav.= " <a href=\"PaymentTerms.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PaymentTerms.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"PaymentTerms.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$paymentTermRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPaymentTerms.php',700,600);"><? }?></td>
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