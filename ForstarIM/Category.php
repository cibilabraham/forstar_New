<?php
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
		header("Location: ErrorPage.php");
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

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	
	#Add a Category
	if ($p["cmdAddCategory"]!="" ) {

		$name		=	addSlash(trim($p["categoryName"]));
		$descr		=	addSlash(trim($p["categoryDescription"]));
		
		if ($name!="") {

			$categoryRecIns	=	$categoryObj->addCategory($name,$descr);

			if ($categoryRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddCategory);
				$sessObj->createSession("nextPage",$url_afterAddCategory.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddCategory;
			}
			$categoryRecIns		=	false;
		}
	}
		
	# Edit Category 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$categoryRec		=	$categoryObj->find($editId);
		$categoryId		=	$categoryRec[0];
		$categoryName		=	stripSlash($categoryRec[1]);
		$categoryDescr		=	stripSlash($categoryRec[2]);
	}

	#Update a Category
	if ($p["cmdSaveChange"]!="" ) {
		
		$categoryId	=	$p["hidCategoryId"];
		$name		=	addSlash(trim($p["categoryName"]));
		$descr		=	addSlash(trim($p["categoryDescription"]));
		
		if ($categoryId!="" && $name!="") {
			$categoryRecUptd = $categoryObj->updateCategory($categoryId,$name,$descr);
		}
	
		if ($categoryRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succCategoryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateCategory.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failCategoryUpdate;
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
				$moreEntriesExist = $categoryObj->checkMoreEntriesExist($categoryId);
				
				if (!$moreEntriesExist) {
					$categoryRecDel = $categoryObj->deleteCategory($categoryId);
				}
			}
		}
		if ($categoryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelCategory);
			$sessObj->createSession("nextPage", $url_afterDelCategory.$selection);
		} else {
			$errDel	=	$msg_failDelInventoryCategory;
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
				$categoryRecConfirm = $categoryObj->updateCategoryconfirm($categoryId);
			}

		}
		if ($categoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmcategory);
			$sessObj->createSession("nextPage",$url_afterDelCategory.$selection);
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
				
					$categoryRecConfirm = $categoryObj->updateCategoryReleaseconfirm($categoryId);
				
			}
		}
		if ($categoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmcategory);
			$sessObj->createSession("nextPage",$url_afterDelCategory.$selection);
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

	# List all Category 	
	$categoryRecords	=	$categoryObj->fetchAllPagingRecords($offset, $limit);
	$categorySize		=	sizeof($categoryRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($categoryObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) $heading	= $label_editCategory;
	else $heading	= $label_addCategory;
		
	$ON_LOAD_PRINT_JS	= "libjs/category.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");	
?>
	<form name="frmCategory" action="Category.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
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
				<?php	
					$bxHeader = "Manage Category";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
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
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Category.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddCategory(document.frmCategory);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Category.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateAddCategory(document.frmCategory);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidCategoryId" value="<?=$categoryId;?>">
<tr>
	<td colspan="2"  height="10" ></td>
</tr>
											<tr>
												<td class="fieldName" nowrap >*Name</td>
												<td nowrap><INPUT TYPE="text" NAME="categoryName" size="15" value="<?=$categoryName;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >Description</td>
												<td nowrap><textarea name="categoryDescription"><?=$categoryDescr;?></textarea></td>
											</tr>
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Category.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddCategory(document.frmCategory);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Category.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateAddCategory(document.frmCategory);">												</td>

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
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Category </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
								<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
								<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintCategory.php',700,600);"><? }?></td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
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
      				$nav.= " <a href=\"Category.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Category.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Category.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description </th>
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
		<?php
			foreach($categoryRecords as $cr) {
				$i++;
				$categoryId	= $cr[0];
				$categoryName	= stripSlash($cr[1]);
				$categoryDescr	= stripSlash($cr[2]);
				$active=$cr[3];
				$existingcount=$cr[4];

		?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
		<?php 
		if($existingcount==0){
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$categoryId;?>" class="chkBox"></td>
		<?php 
		}
		?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$categoryName;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$categoryDescr?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><? if ($active==0){?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$categoryId;?>,'editId'); this.form.action='Category.php';">
		<? } ?>
		</td>
		<? }?>




												<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');" >
			<?php 
			//} 
			} }?>
			
			
			
			
			</td>
												
<? }?>




		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"Category.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Category.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Category.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintCategory.php',700,600);"><? }?></td>
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