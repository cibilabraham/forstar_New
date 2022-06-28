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

		$invoiceTypeName	= addSlash(trim($p["invoiceTypeName"]));	
		$taxApplicable		= $p["taxApplicable"];

		# Check Entry Exist
		$entryExist = $invoiceTypeMasterObj->chkInvTypeExist($invoiceTypeName, $invTypeId);
		
		if ($invoiceTypeName!="" && !$entryExist) {
									
			$invoiceTypeRecIns = $invoiceTypeMasterObj->addInvoiceType($invoiceTypeName, $taxApplicable, $userId);
					
			if ($invoiceTypeRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddInvoiceTypeMaster);
				$sessObj->createSession("nextPage",$url_afterAddInvoiceTypeMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddInvoiceTypeMaster;
			}
			$invoiceTypeRecIns = false;
		} else {
			$addMode = true;
			if ($entryExist) $err = $msg_failAddInvoiceTypeMaster."<br>".$msgFailAddInvoiceTypeExistRec;
			else $err = $msg_failAddInvoiceTypeMaster;
		}
	}

	# Update a Record
	if ($p["cmdSaveChange"]!="") {
		$invoiceTypeId		= $p["hidInvoiceTypeId"];		
		$invoiceTypeName	= $p["invoiceTypeName"];	
		$taxApplicable		= $p["taxApplicable"];

		# Check Entry Exist
		$entryExist = $invoiceTypeMasterObj->chkInvTypeExist($invoiceTypeName, $invoiceTypeId);
			
		if ($invoiceTypeId!="" && $invoiceTypeName!="" && !$entryExist) {
			# Update Main Table			
			$invoiceTypeRecUptd = $invoiceTypeMasterObj->updateInvoiceType($invoiceTypeId, $invoiceTypeName, $taxApplicable);		
		}
	
		if ($invoiceTypeRecUptd || $invoiceTypeRecIns) {
			$sessObj->createSession("displayMsg",$msg_succInvoiceTypeMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateInvoiceTypeMaster.$selection);
		} else {
			$editMode	=	true;
			//$err		=	$msg_failInvoiceTypeMasterUpdate;
			if ($entryExist) $err = $msg_failInvoiceTypeMasterUpdate."<br>".$msgFailAddInvoiceTypeExistRec;
			else $err = $msg_failInvoiceTypeMasterUpdate;
		}
		$invoiceTypeRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$invoiceTypeRec	= $invoiceTypeMasterObj->find($editId);
		$editInvTypeId 	= $invoiceTypeRec[0];
		$invoiceTypeName = $invoiceTypeRec[1];
		$taxApplicable = ($invoiceTypeRec[2]=='Y')?"checked":"";
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$invoiceTypeId	= $p["delId_".$i];
			
			//$stateEntryExist = $invoiceTypeMasterObj->stateEntryExist($invoiceTypeId); && !$stateEntryExist
			if ($invoiceTypeId!="") {
				// Need to check any connection
				# Delete From Main Table
				$invoiceTypeRecDel = $invoiceTypeMasterObj->deleteInvoiceTypeRec($invoiceTypeId);
			}
		}
		if ($invoiceTypeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelInvoiceTypeMaster);
			$sessObj->createSession("nextPage",$url_afterDelInvoiceTypeMaster.$selection);
		} else {
			$errDel	=	$msg_failDelInvoiceTypeMaster;
		}
		$invoiceTypeRecDel	=	false;
	}	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	
		
	
	# List all Recs
	$invoiceTypeMasterRecs = $invoiceTypeMasterObj->fetchAllPagingRecords($offset, $limit);
	$invoiceTypeRecordSize = sizeof($invoiceTypeMasterRecs);

	## -------------- Pagination Settings II -------------------
	$fetchAllRecs = $invoiceTypeMasterObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editInvoiceTypeMaster;
	else	       $heading	=	$label_addInvoiceTypeMaster;

	
	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/InvoiceTypeMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmInvoiceTypeMaster" action="InvoiceTypeMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	<tr><TD height="10"></TD></tr>	
	<tr>
		<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		<td></tr>
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('InvoiceTypeMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateInvoiceTypeMaster(document.frmInvoiceTypeMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InvoiceTypeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateInvoiceTypeMaster(document.frmInvoiceTypeMaster);">												</td>

												<?}?>
											</tr>
					<input type="hidden" name="hidInvoiceTypeId" value="<?=$editInvTypeId;?>">
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr><TD colspan="2" align="center">
	<table>
	
	</table>
	</TD></tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
		<table width="200">								
		<tr>
	  		<td class="fieldName" nowrap >*Name</td>
			<td>
				<input type="text" name="invoiceTypeName" id="invoiceTypeName" value="<?=$invoiceTypeName?>" />	
			</td>
		</tr>		
		<tr>
	  		<td class="fieldName" nowrap onMouseover="ShowTip('If Yes, please give tick mark.');" onMouseout="UnTip();">Tax Applicable</td>
			<td>
				 <input name="taxApplicable" type="checkbox" id="taxApplicable" value="Y" <?=$taxApplicable?> class="chkBox" />		
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InvoiceTypeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateInvoiceTypeMaster(document.frmInvoiceTypeMaster);">												</td>
											<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InvoiceTypeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateInvoiceTypeMaster(document.frmInvoiceTypeMaster);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Invoice Type Master</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
	<table cellpadding="0" cellspacing="0">
        <tr>
		<td nowrap="nowrap">
		</td></tr></table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$invoiceTypeRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintInvoiceTypeMaster.php',700,600);"><? }?></td>
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
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?php
		if ($invoiceTypeRecordSize) {
			$i	=	0;
		?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="4" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"InvoiceTypeMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"InvoiceTypeMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"InvoiceTypeMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Tax Applicable</td>			
		<?php if($edit==true){?>
			<td class="listing-head"></td>
		<?php
		 }
		?>
			</tr>
			<?php			
			foreach ($invoiceTypeMasterRecs as $itr) {
				$i++;
				$invoiceTypeId 	= $itr[0];
				$cntryName	= $itr[1];
				$taxApp		= ($itr[2]=='Y')?"YES":"NO";
			?>
	<tr  bgcolor="WHITE">
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$invoiceTypeId;?>" class="chkBox">			
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$cntryName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">
			<?=$taxApp?>
		</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$invoiceTypeId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='InvoiceTypeMaster.php';" ></td>
<? }?>
		</tr>
		<?			
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="4" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"InvoiceTypeMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"InvoiceTypeMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"InvoiceTypeMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<tr bgcolor="white">
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$invoiceTypeRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintInvoiceTypeMaster.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
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
