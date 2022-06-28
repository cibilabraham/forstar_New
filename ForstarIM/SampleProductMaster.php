<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;

	$selection 	= "?pageNo=".$p["pageNo"];
	$cUserId	= $sessObj->getValue("userId");

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
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

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
		
	if ($p["cmdCancel"]!="") $addMode = false;

	// Value setting
	if ($p["code"]!="") $code = $p["code"];
	if ($p["sampleProductName"]!="") $sampleProductName = $p["sampleProductName"];
			
	# Add a Record
	if ($p["cmdAdd"]!="") {
		//$code		= addSlash(trim($p["code"]));
		$code			= "SP_".autoGenNum();	// SP - sample Product
		$sampleProductName	= addSlash(trim($p["sampleProductName"]));		
		$description		= addSlash($p["description"]);
				
		if ($code!="" && $sampleProductName!="") {
			$sampleProductRecIns = $sampleProductMasterObj->addSampleProduct($code, $sampleProductName, $description, $cUserId);

			if ($sampleProductRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddSampleProduct);
				$sessObj->createSession("nextPage",$url_afterAddSampleProduct.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSampleProduct;
			}
			$sampleProductRecIns		=	false;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$sampleProductRec	= $sampleProductMasterObj->find($editId);
		$editSampleProductId	= $sampleProductRec[0];
		$code			= stripSlash($sampleProductRec[1]);
		$sampleProductName	= stripSlash($sampleProductRec[2]);		
		$description		= stripSlash($sampleProductRec[3]);
		
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {
		$sampleProductId	= $p["hidSampleProductId"];
		$sampleProductName	= addSlash(trim($p["sampleProductName"]));		
		$description		= addSlash($p["description"]);
				
		if ($sampleProductId!="" && $sampleProductName!="") {
			$sampleProductRecUptd = $sampleProductMasterObj->updateSampleProduct($sampleProductId, $sampleProductName, $description);
		}
	
		if ($sampleProductRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSampleProductUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSampleProduct.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSampleProductUpdate;
		}
		$sampleProductRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$sampleProductId	=	$p["delId_".$i];

			if ($sampleProductId!="") {
				// Need to check the selected Category is link with any other process		
				$sampleProductRecDel = $sampleProductMasterObj->deleteSampleProduct($sampleProductId);
			}
		}
		if ($sampleProductRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSampleProduct);
			$sessObj->createSession("nextPage",$url_afterDelSampleProduct.$selection);
		} else {
			$errDel	=	$msg_failDelSampleProduct;
		}
		$sampleProductRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$sampleProductId	=	$p["confirmId"];
			if ($sampleProductId!="") {
				// Checking the selected fish is link with any other process
				$sampleRecConfirm = $sampleProductMasterObj->updateSampleProductconfirm($sampleProductId);
			}

		}
		if ($sampleRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmsample);
			$sessObj->createSession("nextPage",$url_afterDelSampleProduct.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$sampleProductId = $p["confirmId"];
			if ($sampleProductId!="") {
				#Check any entries exist				
					$sampleRecConfirm = $sampleProductMasterObj->updateSampleProductReleaseconfirm($sampleProductId);				
			}
		}
		if ($sampleRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmsample);
			$sessObj->createSession("nextPage",$url_afterDelSampleProduct.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Sample Product
	$sampleProductResultSetObj = $sampleProductMasterObj->fetchAllPagingRecords($offset, $limit);
	$sampleProductRecordSize	 = $sampleProductResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$fetchAllsalesStaffResultSetObj = $sampleProductMasterObj->fetchAllRecords();
	$numrows	=  $fetchAllsalesStaffResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode)	$heading = $label_editSampleProduct;
	else 		$heading = $label_addSampleProduct;
	
	$ON_LOAD_PRINT_JS	= "libjs/SampleProductMaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSampleProduct" action="SampleProductMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		<td>		</tr>
		<?
			if( $editMode || $addMode)
			{
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SampleProductMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSampleProductMaster(document.frmSampleProduct);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SampleProductMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSampleProductMaster(document.frmSampleProduct);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidSampleProductId" value="<?=$editSampleProductId;?>">
	<!--<tr>
		<td class="fieldName" nowrap >*Code</td>
		<td><input type="text" name="code" size="10" value="<?=$code;?>" /></td>
	</tr>-->
	<tr>
		<td class="fieldName" nowrap >*Name </td>
		<td><input type="text" name="sampleProductName" size="25" value="<?=$sampleProductName;?>"></td>
	</tr>	
	<tr>
		<td class="fieldName" nowrap >Description</td>
		<td><textarea name="description"><?=$description;?></textarea></td>
	</tr>						
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SampleProductMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSampleProductMaster(document.frmSampleProduct);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SampleProductMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSampleProductMaster(document.frmSampleProduct);">	
		</td>
		<input type="hidden" name="cmdAddNew" value="1">
		<?}?>
	</tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	</table>
	</td>
	</tr>
		</table>
			</td>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Sample Product Master</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$sampleProductRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSampleProductMaster.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($sampleProductRecordSize) {
			$i = 0;
	?>

	<? if($maxpage>1){ ?>
		<tr bgcolor="#FFFFFF">
		<td colspan="4" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SampleProductMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SampleProductMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SampleProductMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Code</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>		
		<? if($edit==true){?>
			<td class="listing-head"></td>
		<? }?>
		<? if($confirm==true){?>
			<td class="listing-head"></td>
		<? }?>
	</tr>
	<?	
	while ($spr=$sampleProductResultSetObj->getRow()) {
		$i++;
		$sampleProductId   = $spr[0];
		$sampleProductCode = stripSlash($spr[1]);
		$sampleProductName = stripSlash($spr[2]);
		$active=$spr[4];
	?>
	<tr  <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } else {?> bgcolor="white" <?php }?> >
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$sampleProductId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sampleProductCode;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sampleProductName;?></td>		
		<? if($edit==true){?>
			<td class="listing-item" width="80" align="center">
				<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$sampleProductId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SampleProductMaster.php';">
			</td>
		<? }?>


		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$sampleProductId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$sampleProductId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?
	
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"SampleProductMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SampleProductMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SampleProductMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
			} else {
		?>
	<tr bgcolor="white">
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$sampleProductRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSampleProductMaster.php',700,600);"><? }?></td>
											</tr>
										</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
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