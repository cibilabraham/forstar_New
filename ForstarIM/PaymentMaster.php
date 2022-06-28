<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$fishId			=	"";	
	$fishName		=	"";
	$fishCode		=	"";
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	
	/*----------  Current Date  -----------*/
	$currentDate = date("Y-m-d");
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------

	# Add Department Start 
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	
	#Cancel Action
	if($p['cmdCancel']!="")
	{
		$addMode		=	false;
		$editMode		=	false;
	}
	
	#Add New Department
	if ($p["cmdAdd"]!="") 
	{
		$duration	=	addSlash(trim($p["paymentDuration"]));
		$description	=	addSlash(trim($p["paymentDescription"]));
		
		if ($duration!="") 
		{
			//Check for duplicate Entry
			$existPayment = $paymentMasterObj->checkPaymentExist($duration);
			
			if(!$existPayment)
			{
				$paymentRecIns	=	$paymentMasterObj->addPaymentMaster($duration,$description,$userId,$currentDate);
			}
			if ($paymentRecIns) 
			{
				$sessObj->createSession("displayMsg",$msg_succAddPaymentMaster);
				$sessObj->createSession("nextPage",$url_afterAddPaymentMaster.$selection);
			} 
			else 
			{
				$addMode	=	true;
				$err		=	$msg_failAddPaymentMaster;
			}
			$paymentRecIns		=	false;
		}

	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$paymentId	=	$p["confirmId"];

			if ($paymentId!="") 
			{
				$paymentRecConfirm = $paymentMasterObj->updatePaymentConfirm($paymentId);
			}
		}
		if ($paymentRecConfirm) 
		{
			$sessObj->createSession("displayMsg",$msg_succConfirmPaymentMaster);
			$sessObj->createSession("nextPage",$url_afterConfirmPaymentMaster.$selection);
		} 
		else 
		{
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$paymentId	=	$p["confirmId"];
			if ($paymentId!="") {
			#Check any entries exist
				$payReleaseConfirm = $paymentMasterObj->updatePaymentReleaseConfirm($paymentId);
			}
		}
		if ($payReleaseConfirm) 
		{
			$sessObj->createSession("displayMsg",$msg_succRelConfirmPaymentMaster);
			$sessObj->createSession("nextPage",$url_afterRelConfirmPaymentMaster.$selection);
		} 
		else 
		{
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}

	
	# Edit staff 
	if ($p["editId"]!="") 
	{
		$editId			=	$p["editId"];
		$editMode		=	true;
		$paymentRec		=	$paymentMasterObj->find($editId);
		$paymentId		=	$paymentRec[0];
		$duration		=	stripSlash($paymentRec[1]);
		$description	=	stripSlash($paymentRec[2]);
	}

	if ($p["cmdSaveChange"]!="") {
		
		$paymentId		=	$p["hidPaymentId"];
		$duration		=	addSlash(trim($p["paymentDuration"]));
		$description	=	addSlash(trim($p["paymentDescription"]));
		
		if ($paymentId!="" && $duration!="") 
		{
			$paymentRecUptd		=	$paymentMasterObj->updatePaymentMaster($paymentId,$duration,$description);
		}
	
		if ($paymentRecUptd)
		{
			$sessObj->createSession("displayMsg",$msg_succPaymentMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePaymentMaster.$selection);
		} 
		else 
		{
			$editMode	=	true;
			$err		=	$msg_failPaymentMasterUpdate;
		}
		$paymentRecUptd	=	false;
	}


	# Delete staff
	if ($p["cmdDelete"]!="") {
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$paymentId	=	$p["delId_".$i];
			if ($paymentId!="") 
			{
				// Checking the selected fish is link with any other process
				$paymentRecDel = $paymentMasterObj->deletePaymentMaster($paymentId);	
			}
		}
		if ($paymentRecDel) 
		{
			$sessObj->createSession("displayMsg",$msg_succDelPayment);
			$sessObj->createSession("nextPage",$url_afterDeletePaymentMaster.$selection);
		} 
		else 
		{
			$errDel	=	$msg_failDelPayment;
		}
		$paymentRecDel	=	false;

	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Fishes		
	$paymentMasterRecords	=	$paymentMasterObj->fetchAllPagingRecords($offset, $limit);
	$paymentMasterSize		=	sizeof($paymentMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($paymentMasterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	if ($editMode) $heading = $label_editPaymentMaster;
	else $heading = $label_addPaymentMaster;

    $ON_LOAD_PRINT_JS	= "libjs/PaymentMaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>
<form name="frmPaymentMaster" action="PaymentMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<!--<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>-->
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
							<!-- Form fields start -->
							<?php	
								$bxHeader="Payment Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Master</td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%">
		<?
			if( $editMode || $addMode) {
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PaymentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPayment(document.frmPaymentMaster);"></td>
												
												<?} 
												else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PaymentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPayment(document.frmPaymentMaster);"></td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Payment Duration</td>
												<td><INPUT TYPE="text" name="paymentDuration" id="paymentDuration" size="15" value="<?=$duration;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >Description</td>
												<td ><textarea name="paymentDescription"><?=$description;?></textarea></td>
											</tr>
											<tr>
												<td colspan="2"  height="10" ><input type="hidden" name="hidPaymentId" value="<?=$paymentId;?>"></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PaymentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPayment(document.frmPaymentMaster);"></td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PaymentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPayment(document.frmPaymentMaster);"></td>

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
			# Listing Fish Starts
		?>
	</table>
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$paymentMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPaymentMaster.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="4" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
							<table  cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">							
											<?
												if( sizeof($paymentMasterRecords) > 0 )
												{
													$i	=	0;
											?>
										<thead>
											<? if($maxpage>1){?>
											<tr>
											  <td colspan="6" align="right" style="padding-right:10px;"><div align="right" class="navRow">
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
      	$nav.= " <a href=\"PaymentMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"PaymentMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"PaymentMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div> </td>
		  </tr>
										  <? }?>
											<tr >
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th>Payment Duration</td>
												<th nowrap>Description</th>
												

											<? if($edit==true){?>	<th class="listing-head"></th><? }?>
											<? if($confirmF==true){?>	<th class="listing-head"></th><? }?>
											</tr>
		</thead>
		<tbody>
											<?
														$displayStatus = "";
													foreach($paymentMasterRecords as $pay)
													{
														$i++;
														$paymentId		=	$pay[0];
														$duration			=	stripSlash($pay[1]);
														$description	=	stripSlash($pay[2]);
														$active			=$pay[3];
														
														//echo "existing count is $existingcount";
														//echo $confirmF;
														
											?>
											<tr   <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>   >
												<td width="20" align="center">
												<?php 
												if ($existingcount==0) {?>
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$paymentId;?>" class="chkBox"></td>
												<?php}
												?>
												<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$duration;?></td>
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$description;?>&nbsp;</td>
												<? if($edit==true){?>
												<td class="listing-item" width="45" align="center"><?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$paymentId;?>,'editId'); this.form.action='PaymentMaster.php';" ><?php }
												?></td> 
																	<? }?>

												<? if ($confirmF==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$paymentId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$paymentId;?>,'confirmId');" >
			<?php 
			//} ?>
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
				<td align="right" style="padding-right:10px" colspan="6" class="navRow">
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
      	$nav.= " <a href=\"PaymentMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"PaymentMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"PaymentMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr>
											<? }?>
	</tbody>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="4">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$paymentMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPaymentMaster.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
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
			<!--<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>-->
	</table>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

