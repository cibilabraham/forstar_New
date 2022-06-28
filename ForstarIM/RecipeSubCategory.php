<?php
	$insideIFrame = "Y";
	# Ing Sub Category Sections
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;

	$selection 	=	"?pageNo=".$p["pageNo"]."&categoryFilter=".$p["categoryFilter"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		//header("Location: ErrorPage.php");
		//header("Location: ErrorPageIFrame.php");
		//die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add Category Start
	if ($p["cmdAddNew"]!="") $addMode = true;


	#Add a Category
	if ($p["cmdAddCategory"]!="" ) {

		$name			= addSlash(trim($p["categoryName"]));
		$descr			= addSlash(trim($p["categoryDescription"]));
		$recpMainCategory	= $p["recpMainCategory"];
		
		if ($name!="") {

			$categoryRecIns = $recpSubCategoryObj->addRecipeCategory($name, $descr, $recpMainCategory);

			if ($categoryRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddRecipeCategory);
				$sessObj->createSession("nextPage",$url_afterAddRecipeCategory.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddRecipeCategory;
			}
			$categoryRecIns		=	false;
		}
	}


	# Edit Category 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$categoryRec		=	$recpSubCategoryObj->find($editId);
		$categoryId		=	$categoryRec[0];
		$categoryName		=	stripSlash($categoryRec[1]);
		$categoryDescr		=	stripSlash($categoryRec[2]);
		$mainCategoryId		= 	$categoryRec[3];
	}

	#Update a Category
	if ($p["cmdSaveChange"]!="" ) {
		
		$categoryId	=	$p["hidCategoryId"];
		$name		=	addSlash(trim($p["categoryName"]));
		$descr		=	addSlash(trim($p["categoryDescription"]));
		$recpMainCategory	= $p["recpMainCategory"];
		
		if ($categoryId!="" && $name!="" && $recpMainCategory!="") {
			$categoryRecUptd = $recpSubCategoryObj->updateCategory($categoryId, $name, $descr, $recpMainCategory);
		}
	
		if ($categoryRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRecipeCategoryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateRecipeCategory.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failRecipeCategoryUpdate;
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
				$moreEntriesExist = $recpSubCategoryObj->checkMoreEntriesExist($categoryId);
				
				if (!$moreEntriesExist) $categoryRecDel = $recpSubCategoryObj->deleteCategory($categoryId);
			}
		}
		if ($categoryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelRecipeCategory);
			$sessObj->createSession("nextPage", $url_afterDelRecipeCategory.$selection);
		} else {
			$errDel	=	$msg_failDelRecipeCategory;
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
				$categoryRecConfirm = $recpSubCategoryObj->updateCategoryconfirm($categoryId);
			}

		}
		if ($categoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmcategory);
			$sessObj->createSession("nextPage",$url_afterDelRecipeCategory.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$categoryId= $p["confirmId"];

			if ($categoryId!="") {
				#Check any entries exist
				
					$categoryRecConfirm = $recpSubCategoryObj->updateCategoryReleaseconfirm($categoryId);
				
				}
			}
			if ($categoryRecConfirm) {
				$sessObj->createSession("displayMsg",$msg_succRelConfirmcategory);
				$sessObj->createSession("nextPage",$url_afterDelRecipeCategory.$selection);
			} else {
				$errReleaseConfirm	=	$msg_failRlConfirm;
			}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1) * $limit; 
	## ----------------- Pagination Settings I End ------------	


	if ($g["categoryFilter"]!="") $categoryFilterId = $g["categoryFilter"];
	else $categoryFilterId = $p["categoryFilter"];

	# Resettting offset values
	//if ($p["hidCategoryFilterId"]=="" && $p["categoryFilter"]!=""){
	if ($p["hidCategoryFilterId"]!=$p["categoryFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}

	# List all Category ;	
	$categoryRecords	= $recpSubCategoryObj->fetchAllPagingRecords($offset, $limit, $categoryFilterId);
	$categorySize		= sizeof($categoryRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($recpSubCategoryObj->fetchAllRecords($categoryFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Get Main Category Records
	$recpMainCategoryRecords = $recpMainCategoryObj->fetchAllRecordsActiveCategory();


	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/RecipeCategory.js";

	if ($editMode)	$heading = $label_editRecipeCategory;
	else 		$heading = $label_addRecipeCategory;
	
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmRecipeCategory" action="RecipeSubCategory.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
		<!--<tr><td height="10" align="center"><a href="RecipesMaster.php" class="link1"> Recipe Master</a></td></tr>-->
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?	
					$bxHeader="Recipe Sub-Category";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('RecipeSubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRecipeCategory(document.frmRecipeCategory);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('RecipeSubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateRecipeCategory(document.frmRecipeCategory);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidCategoryId" value="<?=$categoryId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Category</td>
												<td>
													<select name="recpMainCategory">
														<option value="">-- Select --</option>
														<?
														foreach ($recpMainCategoryRecords as $cr) {
															$recpMainCategoryId	= $cr[0];
															$recpCategoryName	= stripSlash($cr[1]);
															$selected = "";
															if ($mainCategoryId==$recpMainCategoryId) $selected = "selected";
														?>	
														<option value="<?=$recpMainCategoryId?>" <?=$selected?>><?=$recpCategoryName?></option>
														<?
														}
														?>
													</select>
												</td>
											</tr>

											<tr>
												<td class="fieldName" nowrap >*Name</td>
												<td><INPUT TYPE="text" NAME="categoryName" size="15" value="<?=$categoryName;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >Description</td>
												<td ><textarea name="categoryDescription"><?=$categoryDescr;?></textarea></td>
											</tr>
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RecipeSubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRecipeCategory(document.frmRecipeCategory);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RecipeSubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateRecipeCategory(document.frmRecipeCategory);">												</td>

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
	<tr>
	<td align="center" colspan="3">
		<table width="20%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">
			<tr>					
				<td class="listing-item">Category:</td>
				<td class="listing-item">&nbsp;</td>
				<td style="padding-left:5px; padding-right:10px;">
					<select name="categoryFilter" onchange="this.form.submit();">
					<option value="">-- Select All --</option>
					<?
					foreach ($recpMainCategoryRecords as $cr) {
						$categoryId	= $cr[0];
						$categoryName	= stripSlash($cr[1]);
						$selected = "";
						if ($categoryFilterId==$categoryId) $selected = "Selected";
					?>
					<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
					<? }?>
					</select>
				</td>
                          </tr>
                    </table>
			<?php
				require("template/rbBottom.php");
			?>
		</td>
		</tr>
		</table>	
	</td>
</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Recipe Sub-Category </td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									</td>
								</tr>-->
		<tr>
			<td colspan="3" height="10" ></td>
		</tr>
		<tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRecipeSubCategory.php?categoryFilter=<?=$categoryFilterId?>',700,600);"><? }?></td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">		
		<?
		if ( sizeof($categoryRecords) > 0) {
			$i	=	0;
		?>
		<thead>
		<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RecipeSubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RecipeSubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RecipeSubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Category</th>	
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description </th>
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
		</thead>
		<tbody>
		<?
		foreach ($categoryRecords as $cr) {
			$i++;
			$categoryId	= $cr[0];
			$categoryName	= stripSlash($cr[1]);
			$categoryDescr	= stripSlash($cr[2]);
			$ingredientMainCatId = $cr[3];
			$ingMainCategoryRec	=	$recpMainCategoryObj->find($ingredientMainCatId);
			$ingCategoryName	=	stripSlash($ingMainCategoryRec[1]);
			$active=$cr[4];
		?>
		<tr <?php if ($active==0) { ?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> >
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$categoryId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$categoryName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$ingCategoryName;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$categoryDescr?></td>
		<? if($edit==true){?><td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$categoryId;?>,'editId'); this.form.action='RecipeSubCategory.php';"><? } ?></td>
		<? }?>
<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');"  >
			<?php }?>
			<? }?>
		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
		<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RecipeSubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RecipeSubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RecipeSubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\">>></a> ";
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
											<tr>
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRecipeSubCategory.php?categoryFilter=<?=$categoryFilterId?>',700,600);"><? }?></td>
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
				<!-- Form fields end   -->
			</td>
		</tr>	
<input type="hidden" name="hidCategoryFilterId" value="<?=$categoryFilterId?>">
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
	//ensureInFrameset(document.frmRecipeCategory);
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