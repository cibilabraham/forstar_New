<?php
	require("include/include.php");
	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
		
	$selection 	= "?pageNo=".$p["pageNo"];	

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
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

	# Add new
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;

	# Add 
	if ($p["cmdAdd"]!="") {

		$carriageMode	= addSlash(trim($p["carriageMode"]));	
		
		# Check Entry Exist
		$entryExist = $carriageModeObj->chkRecExist($carriageMode, $invTypeId);
		
		if ($carriageMode!="" && !$entryExist) {
									
			$carriageModeRecIns = $carriageModeObj->addCarriageMode($carriageMode, $userId);
					
			if ($carriageModeRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddCarriageModeMaster);
				$sessObj->createSession("nextPage",$url_afterAddCarriageModeMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddCarriageModeMaster;
			}
			$carriageModeRecIns = false;
		} else {
			$addMode = true;
			if ($entryExist) $err = $msg_failAddCarriageModeMaster."<br>".$msgFailAddCarriageModeExistRec;
			else $err = $msg_failAddCarriageModeMaster;
		}
	}

	# Update a Record
	if ($p["cmdSaveChange"]!="") {

		$carriageModeId		= $p["hidCarriageModeId"];		
		$carriageMode		= $p["carriageMode"];	
		
		# Check Entry Exist
		$entryExist = $carriageModeObj->chkRecExist($carriageMode, $carriageModeId);
			
		if ($carriageModeId!="" && $carriageMode!="" && !$entryExist) {
			# Update Main Table			
			$carriageModeRecUptd = $carriageModeObj->updateCarriageMode($carriageModeId, $carriageMode);		
		}
	
		if ($carriageModeRecUptd || $carriageModeRecIns) {
			$sessObj->createSession("displayMsg",$msg_succCarriageModeMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateCarriageModeMaster.$selection);
		} else {
			$editMode	=	true;
			//$err		=	$msg_failCarriageModeMasterUpdate;
			if ($entryExist) $err = $msg_failCarriageModeMasterUpdate."<br>".$msgFailAddCarriageModeExistRec;
			else $err = $msg_failCarriageModeMasterUpdate;
		}
		$carriageModeRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$invoiceTypeRec	= $carriageModeObj->find($editId);
		$editInvTypeId 	= $invoiceTypeRec[0];
		$carriageMode = $invoiceTypeRec[1];
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$carriageModeId	= $p["delId_".$i];
			
			//$stateEntryExist = $carriageModeObj->stateEntryExist($carriageModeId); && !$stateEntryExist
			if ($carriageModeId!="") {
				// Need to check any connection
				# Delete From Main Table
				$carriageModeRecDel = $carriageModeObj->deleteCarriageModeRec($carriageModeId);
			}
		}
		if ($carriageModeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCarriageModeMaster);
			$sessObj->createSession("nextPage",$url_afterDelCarriageModeMaster.$selection);
		} else {
			$errDel	=	$msg_failDelCarriageModeMaster;
		}
		$carriageModeRecDel	=	false;
	}	

	# Make efault
	if ($p["cmdDefault"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$carriageModeId	= $p["delId_".$i];
			if ($carriageModeId) {
				# Update N For All Rec
				$updateDCForAllRec = $carriageModeObj->updateAllDefaultChk();
				# Update  Y For selected Rec
				$updateBillingCompanyRec = $carriageModeObj->updateDefaultChk($carriageModeId);
			}
		}
	}



if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$carriageModeId	=	$p["confirmId"];
			if ($carriageModeId!="") {
				// Checking the selected fish is link with any other process
				$carriageRecConfirm = $carriageModeObj->updateCarriageModeconfirm($carriageModeId);
			}

		}
		if ($carriageRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmcarriage);
			$sessObj->createSession("nextPage",$url_afterDelCarriageModeMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$carriageModeId = $p["confirmId"];
			if ($carriageModeId!="") {
				#Check any entries exist
				
					$carriageRecConfirm = $carriageModeObj->updateCarriageModeReleaseconfirm($carriageModeId);
				
			}
		}
		if ($carriageRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmcarriage);
			$sessObj->createSession("nextPage",$url_afterDelCarriageModeMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	
		
	
	# List all Recs
	$carriageModeRecs = $carriageModeObj->fetchAllPagingRecords($offset, $limit);
	$carriageModeRecSize = sizeof($carriageModeRecs);

	## -------------- Pagination Settings II -------------------
	$fetchAllRecs = $carriageModeObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editCarriageModeMaster;
	else	       $heading	=	$label_addCarriageModeMaster;

	
	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/CarriageMode.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmCarriageMode" action="CarriageMode.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >	
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
								$bxHeader="Carriage Type Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Carriage Type Master</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">	
	</td>
	</tr>-->
	<tr>
		<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="55%">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('CarriageMode.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateCarriageModeMaster(document.frmCarriageMode);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CarriageMode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateCarriageModeMaster(document.frmCarriageMode);">												</td>

												<?}?>
											</tr>
					<input type="hidden" name="hidCarriageModeId" value="<?=$editInvTypeId;?>">
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr><TD colspan="2" align="center">
	<table>
	
	</table>
	</TD></tr>
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;" align="center">
		<table width="200">								
		<tr>
	  		<td class="fieldName" nowrap >*Name</td>
			<td>
				<input type="text" name="carriageMode" id="carriageMode" value="<?=$carriageMode?>" />	
			</td>
		</tr>
               </table>
		</td>
	</tr>		
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CarriageMode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateCarriageModeMaster(document.frmCarriageMode);">												</td>
											<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CarriageMode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateCarriageModeMaster(document.frmCarriageMode);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>	
					<?php
						require("template/rbBottom.php");
					?>
					</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
		?>
	</table>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="10" ></td>
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
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$carriageModeRecSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintCarriageMode.php',700,600);"><? }?>
				<?php
					if ($add || $edit) {
				?>
				&nbsp;
				<input type="submit" value=" Make Default " class="button"  name="cmdDefault" onClick="return confirmMakeDefault('delId_', '<?=$carriageModeRecSize;?>');" >
				<?php
					}
				?>
				</td>
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
	<td colspan="2" style="padding-left:10px;pading-right:10px;">
	<table cellpadding="2"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
		if ($carriageModeRecSize) {
			$i	=	0;
		?>
	<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"CarriageMode.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"CarriageMode.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"CarriageMode.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr align="center">
		<th width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</th>
		<th style="padding-left:10px; padding-right:10px;">Name</th>		
		<th style="padding-left:10px; padding-right:10px;">Default</th>		
		<?php if($edit==true){?>
			<th>&nbsp;</th>
		<?php
		 }
		?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
			<?php			
			foreach ($carriageModeRecs as $itr) {
				$i++;
				$carriageModeId 	= $itr[0];
				$carriageModeName	= $itr[1];
				$defaultRowChk		= $itr[2];
				$active=$itr[3];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$carriageModeId;?>" class="chkBox">			
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$carriageModeName;?></td>		
		<td align="center" style="padding-left:10px; padding-right:10px;">
			<? if($defaultRowChk=='Y'){?><img src="images/y.png" /><? } ?>
		</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		 <?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$carriageModeId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='CarriageMode.php';" >
		<? } ?>
		</td>
<? }?>


<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$carriageModeId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$carriageModeId;?>,'confirmId');" >
			<?php } }?>
			
			
			
			
			</td>
												
<? }?>
		</tr>
		<?			
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"CarriageMode.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"CarriageMode.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"CarriageMode.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<td>
												<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$carriageModeRecSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintCarriageMode.php',700,600);"><? }?>
												<?php
													if ($add || $edit) {
												?>
												&nbsp;
												<input type="submit" value=" Make Default " class="button"  name="cmdDefault" onClick="return confirmMakeDefault('delId_', '<?=$carriageModeRecSize;?>');" >
												<?php
													}
												?>	
											</td>
											</tr>
										</table>									</td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">		
		<tr>
			<td height="10"></td>
		</tr>			
	</table>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
