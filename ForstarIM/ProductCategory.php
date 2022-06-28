<?php	
	$insideIFrame = "Y";
	require("include/include.php");	
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;

	$selection 	=	"?pageNo=".$p["pageNo"];

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
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add Category Start
	if ($p["cmdAddNew"]!="") $addMode = true;


	#Add a Category
	if ($p["cmdAddCategory"]!="" ) {

		$name		=	addSlash(trim($p["categoryName"]));
		$descr		=	addSlash(trim($p["categoryDescription"]));
		
		if ($name!="") {

			$categoryRecIns = $productCategoryObj->addProductCategory($name, $descr);

			if ($categoryRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddProductCategory);
				$sessObj->createSession("nextPage",$url_afterAddProductCategory.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddProductCategory;
			}
			$categoryRecIns		=	false;
		}
	}


	# Edit Category 
	if ($p["editId"]!="" ) {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$categoryRec	=	$productCategoryObj->find($editId);
		$categoryId	=	$categoryRec[0];
		$categoryName	=	stripSlash($categoryRec[1]);
		$categoryDescr	=	stripSlash($categoryRec[2]);
	}


	#Update a Category
	if ($p["cmdSaveChange"]!="" ) {
		
		$categoryId	=	$p["hidCategoryId"];
		$name		=	addSlash(trim($p["categoryName"]));
		$descr		=	addSlash(trim($p["categoryDescription"]));
		
		if ($categoryId!="" && $name!="") {
			$categoryRecUptd = $productCategoryObj->updateCategory($categoryId, $name, $descr);
		}
	
		if ($categoryRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductCategoryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductCategory.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductCategoryUpdate;
		}
		$categoryRecUptd	=	false;
	}


	# Delete Category
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$categoryId	=	$p["delId_".$i];

			if ($categoryId!="") {
				// Check the selected Category is linked with any other process
				//$moreEntriesExist = $productCategoryObj->checkMoreEntriesExist($categoryId);
				//if (!$moreEntriesExist)
					$categoryRecDel = $productCategoryObj->deleteCategory($categoryId);
			}
		}
		if ($categoryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelProductCategory);
			$sessObj->createSession("nextPage", $url_afterDelProductCategory.$selection);
		} else {
			$errDel	=	$msg_failDelProductCategory;
		}
		$categoryRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$categoryId	=	$p["confirmId"];
			if ($categoryId!="") {
				// Checking the selected fish is link with any other process
				$categoryRecConfirm = $productCategoryObj->updateCategoryconfirm($categoryId);
			}

		}
		if ($categoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmcategory);
			$sessObj->createSession("nextPage",$url_afterDelCity.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$categoryId = $p["confirmId"];
			if ($categoryId!="") {
				#Check any entries exist				
					$categoryRecConfirm = $productCategoryObj->updateCategoryReleaseconfirm($categoryId);
				
			}
		}
		if ($categoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmcategory);
			$sessObj->createSession("nextPage",$url_afterDelCity.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="")		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "")	$pageNo=$g["pageNo"];
	else				$pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Category ;	
	$categoryRecords	=	$productCategoryObj->fetchAllPagingRecords($offset, $limit);
	$categorySize		=	sizeof($categoryRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($productCategoryObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($editMode)	$heading = $label_editProductCategory;
	else 		$heading = $label_addProductCategory;
	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ProductCategory.js";

	# Include Template [topLeftNav.php]
	//$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	/*
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else
	*/
	 require("template/btopLeftNav.php");
?>
	<form name="frmProductCategory" action="ProductCategory.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" border="0" >
		<tr><TD height="10"></TD></tr>
		<!--<tr><td height="10" align="center"><a href="ProductMaster.php" class="link1"> Product Master</a></td></tr>-->
		<tr><td height="10" align="center"><a href="###" class="link1" onclick="parent.openTab('ProductMaster.php')">Product Master</a></td></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>
		</tr>
		<?}?>		
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			
			<tr>
			<td align="center">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td bgcolor="white">
						<?	
							$bxHeader="Product Category";
							include "template/boxTL.php";
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						<tr>
									<td colspan="3" align="center">
	<Table width="30%">
		<?php
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
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
									<td colspan="2" align="center" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ProductCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductCategory(document.frmProductCategory);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ProductCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateProductCategory(document.frmProductCategory);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidCategoryId" value="<?=$categoryId;?>">
<tr>
	<td colspan="2"  height="10" ></td>
</tr>
	<tr><TD colspan="2">
		<table align="center" width="50%">
		<TR><TD>
		
		<table align="center">
			<tr>
					<td class="fieldName1" nowrap >*Name</td>
												<td><INPUT TYPE="text" NAME="categoryName" size="15" value="<?=$categoryName;?>"></td>
											</tr>
											<tr>
												<td class="fieldName1" nowrap >Description</td>
												<td ><textarea name="categoryDescription"><?=$categoryDescr;?></textarea></td>
											</tr>
		</table>
		
		</TD></TR>
		</table>
	</TD></tr>
											
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductCategory(document.frmProductCategory);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateProductCategory(document.frmProductCategory);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						
					</td>
					</tr>
				</table>
			<?php
			require("template/rbBottom.php");
		?>
				<!-- Form fields end   -->			
			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
</table>
									</td>
						</tr>
							<!-- Form fields start -->
							<!--<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Category </td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>-->
								<tr>	
								<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
								<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductCategory.php',700,600);"><? }?></td>
		</tr>
		</table></td>
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
			if ( sizeof($categoryRecords) > 0) {
				$i	=	0;
		?>
		<thead>
		<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductCategory.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductCategory.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductCategory.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<tr align="center" >
			<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
			<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description </th>
			<? if($edit==true){?><th class="listing-head"></th><? }?>
			<? if($confirm==true){?><th class="listing-head"></th><? }?>
		</tr>
		</thead>
		<tbody>
		<?
		foreach ($categoryRecords as $cr) {
			$i++;
			$categoryId	= $cr[0];
			$categoryName	= stripSlash($cr[1]);
			$categoryDescr	= stripSlash($cr[2]);
			$active=$cr[3];
		?>
		<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
			<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$categoryId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$categoryName;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$categoryDescr?></td>
		<? if($edit==true){?><td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$categoryId;?>,'editId'); this.form.action='ProductCategory.php';"><? } ?></td>
		<? }?>

		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		</tr>	
		<?
			}
		?>
		
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
		<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductCategory.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductCategory.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductCategory.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	</tbody>
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
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del){?><input type="submit" value=" Delete **" name="cmdDelete" style="background-color:#ff0000;color: white;" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProductCategory.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>	
					<?
						include "template/boxBR.php"
					?>
					</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
		<tr><td height="10" align="center"><a href="###" class="link1" onclick="parent.openTab('ProductMaster.php')">Product Master</a></td></tr>
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
<?php 
	if ($iFrameVal=="") { 
?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	//ensureInFrameset(document.frmProductCategory);
	//-->
	</script>
<?php 
	}
?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>