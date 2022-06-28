<?php
	require("include/include.php");
	require("lib/subcategory_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 		=	"?pageNo=".$p["pageNo"]."&categoryFilter=".$p["categoryFilter"];

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
	
	# Variable Resetting
	$cpyFrmCategoryId	= $p["cpyFrmCategory"];	
	$selSubCategoryId	= $p["selSubCategory"];

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") {
		$addMode 	= false;
		$editMode 	= false;
		$cpyFrmCategoryId = "";
		$selSubCategoryId = "";		
	}
		

	# Add 
	if ($p["cmdAdd"]!="") {
		
		$categoryId	=	$p["category"];
		$name		=	addSlash(trim($p["subCategoryName"]));
		$descr		=	addSlash(trim($p["subCategoryDescr"]));
		$unitGroup	=	$p["unitGroup"];

		$rowCount 	= $p["hidTableRowCount"];
		$checkPoint	= ($p["checkPoint"]=="")?N:$p["checkPoint"];
		$carton		= ($p["carton"]=="")?N:$p["carton"];
		
		if ($categoryId!="" && $name!="") {
			$subCategoryRecIns = $subcategoryObj->addSubCategory($categoryId, $name, $descr, $unitGroup, $checkPoint, $userId, $carton);
			if ($checkPoint=='Y') {
				#Find the Last inserted Id From Main Table
				$lastId = $databaseConnect->getLastInsertedId();
							
				for ($i=0; $i<$rowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selCheckPoint	= $p["selCheckPoint_".$i];
						if ($lastId!="" && $selCheckPoint!="") {
							$checkPointRecIns = $subcategoryObj->addCheckPoint($lastId, $selCheckPoint);
						}
					}
				}
			}
			if ($subCategoryRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddSubCategory);
				$sessObj->createSession("nextPage",$url_afterAddSubCategory.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSubCategory;
			}
			$subCategoryRecIns		=	false;
		}
	}

	# Edit Sub Category 
	if ($p["editId"]!="" || $selSubCategoryId!="") {

		if ($selSubCategoryId) $editId = $selSubCategoryId;
		else $editId	= $p["editId"];
		if (!$selSubCategoryId) $editMode  = true;
	
		$subcategoryRec		=	$subcategoryObj->find($editId);
		$subcategoryId		=	$subcategoryRec[0];
		$editCategoryId		=	$subcategoryRec[1];
		$subcategoryName	=	stripSlash($subcategoryRec[2]);
		$subcategoryDescr	=	stripSlash($subcategoryRec[3]);
		$selUnitGroupId		=	$subcategoryRec[4];
		$selChkPoint		= 	$subcategoryRec[5];
		$cartonChk		= 	($subcategoryRec[6]=='Y')?"checked":"";

		$chkPointSel = "";
		if ($selChkPoint=='Y') {
			$chkPointSel = "checked";
			$getSelChkPointRecs = $subcategoryObj->getChkPointRecs($subcategoryId);
		}
	}

	# update  
	if ($p["cmdSaveChange"]!="") {
		
		$subcategoryId	=	$p["hidSubCategoryId"];
		$categoryId	=	$p["category"];
		$name		=	addSlash(trim($p["subCategoryName"]));
		$descr		=	addSlash(trim($p["subCategoryDescr"]));
		$unitGroup	=	$p["unitGroup"];

		$rowCount 	= $p["hidTableRowCount"];
		$checkPoint	= ($p["checkPoint"]=="")?N:$p["checkPoint"];
		$carton		= ($p["carton"]=="")?N:$p["carton"];
		
		if ($categoryId!="" && $name!="" && $subcategoryId!="") {
			$subCategoryRecUptd = $subcategoryObj->updateSubCategory($subcategoryId, $categoryId, $name, $descr, $unitGroup, $checkPoint, $carton);

			if ($checkPoint=='Y') {
				for ($i=0; $i<$rowCount; $i++) {
					$status = $p["status_".$i];
					$chkPointEntryId = $p["chkPointEntryId_".$i];
					if ($status!='N') {
						$selCheckPoint	 = $p["selCheckPoint_".$i];		
						
						if ($subcategoryId!="" && $selCheckPoint!="" && $chkPointEntryId=="") {
							# Insert New Rec
							$checkPointRecIns = $subcategoryObj->addCheckPoint($subcategoryId, $selCheckPoint);
						} else if ($selCheckPoint!="" && $chkPointEntryId!="") {
							# Upate Existing Rec
							$updateCheckPointRec = $subcategoryObj->updateCheckPoint($chkPointEntryId, $selCheckPoint);
						} 
					} else if ($status=='N') {
						# Delete Status=N Rec
						$deleteChkPointEntryRec = $subcategoryObj->deleteChkPointEntryRec($chkPointEntryId);	
					}
				}
			} else {
				# Delete All Subcategory Wise check Points
				$delCheckPointRecs = $subcategoryObj->deleteCheckPointRecs($subcategoryId);
			}
		}
	
		if ($subCategoryRecUptd) {
			$sessObj->createSession("displayMsg", $msg_succSubCategoryUpdate);
			$sessObj->createSession("nextPage", $url_afterUpdateSubCategory.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSubCategoryUpdate;
		}
		$subCategoryRecUptd	=	false;
	}


	# Delete Sub Category
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$subCategoryId =  $p["delId_".$i];
			if ($subCategoryId!="") {
				// Need to check the selected Category is link with any other process
				$subCategoryRecInUse = $subcategoryObj->subCategoryRecInUse($subCategoryId);
				
				if (!$subCategoryRecInUse) {
					# Delete All Subcategory Wise check Points
					$delCheckPointRecs = $subcategoryObj->deleteCheckPointRecs($subCategoryId);
					# Delete Sub Category
					$subCategoryRecDel =  $subcategoryObj->deleteSubCategory($subCategoryId);
				}
			}
		}
		if ($subCategoryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSubCategory);
			$sessObj->createSession("nextPage",$url_afterDelSubCategory.$selection);
		} else {
			$errDel	=	$msg_failDelSubCategory;
		}
		$subCategoryRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$subCategoryId	=	$p["confirmId"];
			if ($subCategoryId!="") {
				// Checking the selected fish is link with any other process
				$subCategoryRecConfirm = $subcategoryObj->updateSubCategoryconfirm($subCategoryId);
			}

		}
		if ($subCategoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmsubCategory);
			$sessObj->createSession("nextPage",$url_afterDelSubCategory.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$subCategoryId = $p["confirmId"];
			if ($subCategoryId!="") {
				#Check any entries exist
				
					$subCategoryRecConfirm = $subcategoryObj->updateSubCategoryReleaseconfirm($subCategoryId);
				
			}
		}
		if ($subCategoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmsubCategory);
			$sessObj->createSession("nextPage",$url_afterDelSubCategory.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	if ($g["categoryFilter"]!="") $categoryFilterId = $g["categoryFilter"];
	else $categoryFilterId = $p["categoryFilter"];	

	# Resettting offset values
	if ($p["hidCategoryFilterId"]!=$p["categoryFilter"]) {		
		$offset = 0;
		$pageNo = 1;
	}

	
	# List all Sub Category
	$subCategoryRecords	= $subcategoryObj->fetchAllPagingRecords($offset, $limit, $categoryFilterId);
	$subCategorySize	= sizeof($subCategoryRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($subcategoryObj->fetchAllRecords($categoryFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# List all Category ;
	$categoryRecords	=	$categoryObj->fetchAllRecordsActivecategory();
	
	if ($addMode || $editMode) {
		# List all Unit Group 
		//$unitGroupRecords = $unitGroupObj->fetchAllRecords();
		$unitGroupRecords = $unitGroupObj->fetchAllActiveRecords();
	
		# List all Check Point Records
		//$checkPointRecords = $checkPointObj->fetchAllRecords();
		$checkPointRecords = $checkPointObj->fetchAllActiveRecords();
	}

	if ($addMode) {
		# Get All Sub-Category Recs
		//$getSubCategoryRecs = $subcategoryObj->fetchAllRecords();	
		if ($cpyFrmCategoryId) $getSubCategoryRecs = $subcategoryObj->filterRecords($cpyFrmCategoryId);
	}

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	if ($editMode) $heading = $label_editSubCategory;
	else $heading = $label_addSubCategory;
$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/subcategory.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSubCategory" action="SubCategory.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="5"></TD></tr>
		<tr><td height="10" align="center"><a href="UnitGroup.php" class="link1"> Unit Group</a></td></tr>
		<tr><TD height="5"></TD></tr>
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
					$bxHeader = "Manage Sub-Category";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="50%">
		<?php
			if ($editMode || $addMode) {
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSubCategory(document.frmSubCategory);">												</td>
												
												<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSubCategory(document.frmSubCategory);">												</td>

												<?}?>
											</tr>
	<input type="hidden" name="hidSubCategoryId" value="<?=$subcategoryId;?>">
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
		<?php
			 if ($addMode) { 
		?>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
			<!--<fieldset>
			<legend class="listing-item" onMouseover="ShowTip('Copy from existing sub-category and save after editing.');" onMouseout="UnTip();">Copy From</legend>-->
			<?php
				$entryHead = "Copy From";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>
			<table>
				<TR>
					<td class="fieldName" nowrap onMouseover="ShowTip('Copy from existing sub-category and save after editing.');" onMouseout="UnTip();">
						*Category
					</td>
					<td nowrap="true">
						<!--<select name="cpyFrmCategory" onchange="this.form.submit();" style="width:150px;">-->
						<select name="cpyFrmCategory" onchange="getSubcategory(this);" style="width:150px;"
						<option value="">--Select--</option>
						<?php
						foreach ($categoryRecords as $cr) {
							$cpyCategoryId	= $cr[0];
							$cpyCategoryName	= stripSlash($cr[1]);
							$selected =  ($cpyFrmCategoryId==$cpyCategoryId)?"Selected":"";	
						?>
						<option value="<?=$cpyCategoryId?>" <?=$selected;?>><?=$cpyCategoryName;?></option>
						<? }?>
					</select>
					</td>
					<TD class="fieldName" onMouseover="ShowTip('Copy from existing sub-category and save after editing.');" onMouseout="UnTip();" nowrap="true">*Sub-Category:</TD>
					<td nowrap="true">
						<!--<select name="selSubCategory" id="selSubCategory" onchange="this.form.submit();" style="width:150px;">-->
						<select name="selSubCategory" id="selSubCategory" onchange="getSubcategory(this);" style="width:150px;">
							<option value="">--Select--</option>
							<?php
								foreach ($getSubCategoryRecs as $gscr) {
									$sSubCategoryId	= $gscr[0];
									$sSubCategoryName= stripSlash($gscr[2]);
									$selected =  ($selSubCategoryId==$sSubCategoryId)?"selected":"";
							?>
							<option value="<?=$sSubCategoryId?>" <?=$selected?>><?=$sSubCategoryName?></option>
							<? }?>
						</select>
					</td>
				</TR>
			</table>
			<!--</fieldset>-->
			<?php
				require("template/rbBottom.php");
			?>
		  </td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<?php
			} // Cpy Frm add mode ends here
		?>
		<tr><TD colspan="2" align="center" style="padding-left:10px; padding-right:10px;">
			<table>
			<tr>
				<td class="fieldName" nowrap >*Category</td>
				<td nowrap="true">
				<select name="category">
					<option value="">--Select--</option>
					<?
					foreach ($categoryRecords as $cr) {
						$categoryId	=	$cr[0];
						$categoryName	=	stripSlash($cr[1]);
						$selected =  ($editCategoryId==$categoryId)?"Selected":"";	
					?>
					<option value="<?=$categoryId?>" <?=$selected;?>><?=$categoryName;?></option>
					<? }?>
				</select>
				</td>
			</tr>
			<tr>
			  <td class="fieldName" nowrap >*Name </td>
			<td nowrap="true">
				<INPUT TYPE="text" NAME="subCategoryName" size="30" value="<?=$subcategoryName;?>">
				<input type="hidden" name="hidSubCategoryName" id="hidSubCategoryName" value="<?=$subcategoryName?>" size="14">
			</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap >*Unit Group </td>
				<td nowrap="true">	
				<select name="unitGroup" id="unitGroup">
				<option value="">--Select--</option>
				<?
				foreach ($unitGroupRecords as $cr) {			
					$unitGroupId	= $cr[0];
					$groupName	= stripSlash($cr[1]);	
					$selected =  ($selUnitGroupId==$unitGroupId)?"Selected":"";		
				?>
				<option value="<?=$unitGroupId?>" <?=$selected?>><?=$groupName?></option>
				<? }?>
					</select>
					</td>
				</tr>
	<tr>
		<td class="fieldName" nowrap >Description</td>
		<td nowrap="true">
			<textarea name="subCategoryDescr"><?=$subcategoryDescr;?></textarea>
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap>Carton</td>
		<td nowrap="true">
			<INPUT type="checkbox" name="carton" id="carton" class="chkBox" value="Y" <?=$cartonChk?> /> &nbsp;&nbsp;<span class="fieldName" style="vertical-align:middle; line-height:normal"><font size="1">(If Yes, please give tick mark)</font></span>
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Check Point</td>
		<td nowrap="true">
			<INPUT type="checkbox" name="checkPoint" id="checkPoint" class="chkBox" value="Y" <?=$chkPointSel?> onclick="showChkPoint();"> &nbsp;&nbsp;<span class="fieldName" style="vertical-align:middle; line-height:normal"><font size="1">(If Yes, please give tick mark)</font></span>
		</td>
	</tr>
	<!--  Dynamic Row Starting Here-->
	<tr id="chkPointRow">
		<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
			<table>
				<TR>
					<TD>
							<table  cellspacing="1" cellpadding="3" id="tblCheckPoint" class="newspaperType">
								<tr align="center">
									<th class="listing-head" style="text-align:center;">Check Point</th>
									<th>&nbsp;</th>
								</tr>
							</table>
					<!--  Hidden Fields-->
				<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="">
					</TD>
				</TR>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD>
						<a href="###" id='addRow' onclick="javascript:addNewCheckPointItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
	</table>
	</TD>
	</tr>	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSubCategory(document.frmSubCategory);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SubCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSubCategory(document.frmSubCategory);">
<input type="hidden" name="cmdAddNew" value="1">												</td>

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
			<tr>
				<td height="10" align="center" ></td>
			</tr>
<tr>
				<td colspan="3" align="center">
						<table width="25%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="4">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table cellpadding="0" cellspacing="0">
                	<tr>
			  	<td class="listing-item">Category&nbsp;</td>
                      		<td nowrap="nowrap"> 		
               <!-- <select name="categoryFilter" onchange="this.form.submit();" style="width:160px;">-->
			   <select name="categoryFilter" onchange="getSubcategory(this)" style="width:160px;">
                <option value="">--Select All--</option>
                <?php
		foreach ($categoryRecords as $cr) {
			$fCategoryId	=	$cr[0];
			$fCategoryName	=	stripSlash($cr[1]);
			$selected =  ($categoryFilterId==$fCategoryId)?"Selected":"";		
		?>
               <option value="<?=$fCategoryId?>" <?=$selected;?>><?=$fCategoryName;?></option>
                <? }?>
                </select>
		</td>
                       </tr>
                 </table>
		</td></tr>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Manage SubCategory </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$subCategorySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSubCategory.php?categoryFilter=<?=$categoryFilterId?>',700,600);"><? }?></td>
											</tr>
										</table>			</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if ($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
		<tr>
			<td width="1" ></td>
			<td colspan="2" style="padding-left:10px;padding-right:10px;" >
			<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
		if (sizeof($subCategoryRecords)>0) {
			$i	=	0;
		?>
		<thead>
		<? if($maxpage>1){?>
		<tr>
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Name</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Description </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Category</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Unit Group</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Check Point</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Carton</th>
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($subCategoryRecords as $scr) {
		$i++;
		$subCategoryId		= $scr[0];
		$subCategoryName	= stripSlash($scr[2]);
		$subCategoryDescr	= stripSlash($scr[3]);
		$category		= $scr[4];
		
		$unitGroupName 		= $unitGroupObj->getUnitGroupName($scr[5]); //Find the name
		$chkPoint		= ($scr[6]=='Y')?"YES":"NO";
		$chkCarton		= ($scr[7]=='Y')?"YES":"NO";
		$active=$scr[8];
		$existingcount=$scr[9];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
		<?php 
		if($existingcount==0){
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$subCategoryId;?>" class="chkBox"></td>
		<?php 
		}
		?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$subCategoryName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$subCategoryDescr?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$category;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$unitGroupName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center" nowrap><?=$chkPoint;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center" nowrap><?=$chkCarton;?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="60" align="center" style="padding-left:5px; padding-right:5px;">
			<? if ($active==0){?>	<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$subCategoryId;?>,'editId'); this.form.action='SubCategory.php';">
			<? } ?>
			</td>
		<? }?>

<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$subCategoryId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$subCategoryId;?>,'confirmId');"  >
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
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SubCategory.php?pageNo=$page&categoryFilter=$categoryFilterId\"  class=\"link1\">>></a> ";
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
		<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$subCategorySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSubCategory.php?categoryFilter=<?=$categoryFilterId?>',700,600);"><? }?></td>
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
		
		<tr>
			<td height="10">
				<input type="hidden" name="hidCategoryFilterId" value="<?=$categoryFilterId?>">	
				<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
			</td>
		</tr>
		<tr><td height="10" align="center"><a href="UnitGroup.php" class="link1"> Unit Group</a></td></tr>
	</table>
	<?php
		if ($addMode || $editMode) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			function addNewCheckPointItem()
			{
				addNewCheckPoint('tblCheckPoint','','');		
			}
		</SCRIPT>
	<? }?>
<?php 
	if ($addMode && !sizeof($getSelChkPointRecs)) {
?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewCheckPointItem();
	</SCRIPT>
<?php 
	}
?>
	<?php if ($addMode || $editMode) {?>
	<script language="JavaScript" type="text/javascript">
	showChkPoint();
	</script>
	<? }?>
	<script language="JavaScript" type="text/javascript">
	<?php
		if (sizeof($getSelChkPointRecs)>0) {
			foreach ($getSelChkPointRecs as $gcp) {
				$subCategoryChkPointId = $gcp[0];
				$checkPointId	       = $gcp[1];
	?>
		addNewCheckPoint('tblCheckPoint','<?=$checkPointId?>', '<?=$subCategoryChkPointId?>');	
	<?php
			}
		}
	?>
	</script>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>