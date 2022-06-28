<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 	= "?pageNo=".$p["pageNo"]."&categoryFilter=".$p["categoryFilter"]."&mainCategoryFilter=".$p["mainCategoryFilter"];

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

	# Add Category Start 
	if ($p["cmdAddNew"]!="") {
		$addMode  =   true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode   =  false;
	}

	#Add a stock
	if ($p["cmdAdd"]!="") {
		$selCategory	= $p["category"];
		$code		= addSlash(trim($p["ingredientCode"]));
		$name		= addSlash(trim($p["ingredientName"]));
		$surName	= addSlash(trim($p["ingredientSurname"]));
		$descr		= addSlash(trim($p["description"]));
		$openingQty	= trim($p["openingQty"]);
		$mainCategoryId = $p["ingMainCategory"];		

		if ($code!="" && $name!="" && $selCategory!="" && $mainCategoryId!="") {

			$ingredientRecIns = $ingredientMasterObj->addIngredient($selCategory, $code, $name, $surName, $descr, $userId, $openingQty, $mainCategoryId);

			if ($ingredientRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddIngredients);
				$sessObj->createSession("nextPage",$url_afterAddIngredients.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddIngredients;
			}
			$ingredientRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$ingredientId	=	$p["hidIngredientId"];
		$selCategory	=	$p["category"];
		$code		=	addSlash(trim($p["ingredientCode"]));
		$name		=	addSlash(trim($p["ingredientName"]));
		$surName	=	addSlash(trim($p["ingredientSurname"]));
		$descr		=	addSlash(trim($p["description"]));
		$openingQty	= 	trim($p["openingQty"]);
		$hidExistingQty = 	trim($p["hidExistingQty"]);
		$mainCategoryId = $p["ingMainCategory"];	
		
		if ($ingredientId!="" && $name!="" && $code!="" && $selCategory!="") {
			$ingredientRecUptd = $ingredientMasterObj->updateIngredient($ingredientId, $selCategory, $code, $name, $surName, $descr, $openingQty, $hidExistingQty, $mainCategoryId);
		}
	
		if ($ingredientRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succIngredientsUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateIngredients.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failIngredientsUpdate;
		}
		$ingredientRecUptd	=	false;
	}


	# Edit  an ingredient
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$ingredientRec	=	$ingredientMasterObj->find($editId);
		$editIngredientId =	$ingredientRec[0];
		$code		=	stripSlash($ingredientRec[1]);
		$name		=	stripSlash($ingredientRec[2]);
		$surname	=	stripSlash($ingredientRec[3]);
		$selCategory	=	$ingredientRec[4];
		$descr		=	stripSlash($ingredientRec[5]);
		$openingQty	= 	$ingredientRec[6];
		$selMainCategoyId = 	$ingredientRec[7];
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientId	=	$p["delId_".$i];
			$moreEntriesExist = $ingredientMasterObj->checkMoreEntriesExist($ingredientId);
			if ($ingredientId!="" && !$moreEntriesExist) {				
				// Need to check the selected ing is link with any other process
				$ingredientRecDel = $ingredientMasterObj->deleteIngredient($ingredientId);
			}
		}
		if ($ingredientRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelIngredients);
			$sessObj->createSession("nextPage",$url_afterDelIngredients.$selection);
		} else {
			$errDel	=	$msg_failDelIngredients;
		}
		$ingredientRecDel	=	false;
	}
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientId	=	$p["confirmId"];


			if ($ingredientId!="") {
				// Checking the selected fish is link with any other process
				$ingredientRecConfirm =$ingredientMasterObj->updateingredientsconfirm($ingredientId);
			}

		}
		if ($ingredientRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmingredient);
			$sessObj->createSession("nextPage",$url_afterDelIngredients.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$ingredientId= $p["confirmId"];

			if ($ingredientId!="") {
				#Check any entries exist
				
					$ingredientRecConfirm = $ingredientMasterObj->updateingredientReleaseconfirm($ingredientId);
				
			}
		}
		if ($ingredientRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmingredient);
			$sessObj->createSession("nextPage",$url_afterDelIngredients.$selection);
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

	
	if ($g["mainCategoryFilter"]!="") $mainCategoryFilterId = $g["mainCategoryFilter"];
	else $mainCategoryFilterId = $p["mainCategoryFilter"];

	if ($g["categoryFilter"]!="") $categoryFilterId = $g["categoryFilter"];
	else $categoryFilterId = $p["categoryFilter"];

	# Resettting offset values
	if ($p["hidCategoryFilterId"]!=$p["categoryFilter"] || $p["hidMainCategoryFilterId"]!=$p["mainCategoryFilter"]){
		$offset = 0;
		$pageNo = 1;
	}

	if ($p["hidMainCategoryFilterId"]!=$p["mainCategoryFilter"]) $categoryFilterId = ""; 
	
	# List all Ingredients
	$ingredientResultSetObj = $ingredientMasterObj->fetchAllPagingRecords($offset, $limit, $categoryFilterId, $mainCategoryFilterId);
	$ingredientRecordSize	= $ingredientResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allIngredientResultSetObj = $ingredientMasterObj->ingredientRecFilter($categoryFilterId, $mainCategoryFilterId);
	$numrows	=  $allIngredientResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	# Get Main Category Records
	//$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecords();
	$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecordsActiveCategory();
	if ($addMode || $editMode) {
		if ($p["ingMainCategory"]!="") $mainCategoryId = $p["ingMainCategory"];
		else $mainCategoryId = $selMainCategoyId; // Edit Mode
		
		# List all Ingredient Sub-Category	
		if ($mainCategoryId)$ingredientCategoryRecords = $ingredientCategoryObj->fetchAllRecordsActiveSubcategory($mainCategoryId); //$ingredientCategoryRecords = $ingredientCategoryObj->fetchAllRecords($mainCategoryId);
	}	
	# List all Ing Category Recs
	//$filterIngCategoryRecords = $ingredientCategoryObj->fetchAllRecords($mainCategoryFilterId);
	$filterIngCategoryRecords = $ingredientCategoryObj->fetchAllRecordsActiveSubcategory($mainCategoryFilterId);
		
	# Find Ing Rate List
	$latestIngRateListId = $ingredientRateListObj->latestRateList();

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/IngredientsMaster.js"; 

	#heading Section
	if ($editMode) $heading	=	$label_editIngredients;
	else	       $heading	=	$label_addIngredients;

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmIngredientsMaster" action="IngredientsMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<!--<tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Sub-Category">Sub-Category</a></td></tr>-->
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
				<?	
					$bxHeader="Ingredient Sub-Category";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="50%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('IngredientsMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateIngredientMaster(document.frmIngredientsMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientsMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientMaster(document.frmIngredientsMaster);">												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidIngredientId" value="<?=$editIngredientId;?>">
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD colspan="2" nowrap align="center">
		<table>
		<TR>
			<TD valign="top">
			<table>
				<tr>
						<td class="fieldName" nowrap >*Category</td>
						<td align="left">
							<select name="ingMainCategory" onchange="<?if ($addMode) {?> this.form.submit();<? } else {?> this.form.editId.value=<?=$editId?>;this.form.submit(); <? }?>">
								<option value="">-- Select --</option>
								<?
								foreach ($ingMainCategoryRecords as $cr) {
									$ingMainCategoryId	= $cr[0];
									$ingCategoryName	= stripSlash($cr[1]);
									$selected = "";
									if ($mainCategoryId==$ingMainCategoryId) $selected = "selected";
								?>	
								<option value="<?=$ingMainCategoryId?>" <?=$selected?>><?=$ingCategoryName?></option>
								<?
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
					<td nowrap class="fieldName" >*Sub-Category</td>
					<td nowrap align="left">
                                        <select name="category">
                                        <option value="">-- Select --</option>
					<?
					foreach ($ingredientCategoryRecords as $cr) {
						$categoryId	= $cr[0];
						$categoryName	= stripSlash($cr[1]);
						$selected = "";
						if ($selCategory==$categoryId) $selected = "Selected";
					?>
                                        <option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
					<? }?>
                                        </select>
				</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >*Code</td>
					<td align="left">
					<INPUT TYPE="text" NAME="ingredientCode" size="15" value="<?=$code;?>">
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >*Name</td>
					<td align="left">
					<input type="text" name="ingredientName" size="20" value="<?=$name;?>" />
					</td>
				</tr>
			</table>
			</TD>
			<td>&nbsp;</td>
			<TD valign="top">
			<table>
				<tr>
					  <td class="fieldName" nowrap >Local Name</td>
					  <td align="left">
						<textarea name="ingredientSurname"><?=$surname?></textarea>
					   </td>
				</tr>
				<tr>
					<TD></TD>
					<td class="listing-item" style="text-align:left;">
						<span style="vertical-align:middle; line-height:normal"><font size="1">(Please put a comma for every local name)</font></span>
					</td>
				</tr>
                                                                      <tr>
									<td class="fieldName" nowrap >Description</td>
									<td>
				<textarea name="description"><?=$descr?></textarea></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >Quantity in Stock</td>
					<td>
						<input type="text" size="4" name="openingQty" style="text-align:right" value="<?=$openingQty?>">
						<input type="hidden" name="hidExistingQty" size="5" value="<?=$openingQty;?>" style="text-align:right;">
					</td>
				</tr>
			</table>
			</TD>
		</TR>
		</table>
		</TD>
	</tr>				
						<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientsMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientMaster(document.frmIngredientsMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientsMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientMaster(document.frmIngredientsMaster);">												</td>
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
	<td align="center" colspan="3">
		<table width="40%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">
			<tr>
				<td nowrap="nowrap">
				<table cellpadding="0" cellspacing="0">
                      			<tr>		
					 <td class="listing-item" nowrap>Category:</td>
					 <td class="listing-item">&nbsp;</td>
					<td style="padding-left:5px; padding-right:10px;">
					<select name="mainCategoryFilter" onchange="this.form.submit();" style="width:120px;">
                                        <option value="">-- Select All --</option>
					<?
					foreach ($ingMainCategoryRecords as $cr) {
						$mCategoryId	= $cr[0];
						$mCategoryName	= stripSlash($cr[1]);
						$selected = "";
						if ($mainCategoryFilterId==$mCategoryId) $selected = "Selected";
					?>
                                        <option value="<?=$mCategoryId?>" <?=$selected?>><?=$mCategoryName?></option>
					<? }?>
                                        </select></td>			
                                    <td class="listing-item" nowrap>Sub-Category:</td>
					   <td class="listing-item">&nbsp;</td>
					<td style="padding-left:5px; padding-right:10px;">
					<select name="categoryFilter" onchange="this.form.submit();" style="width:120px;">
                                        <option value="">-- Select All --</option>
					<?
					foreach ($filterIngCategoryRecords as $cr) {
						$categoryId	= $cr[0];
						$categoryName	= stripSlash($cr[1]);
						$selected = "";
						if ($categoryFilterId==$categoryId) $selected = "Selected";
					?>
                                        <option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
					<? }?>
                                        </select></td>
                          </tr>
                    </table></td></tr>
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
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Ingredients Master  </td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">	</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientsMaster.php?categoryFilter=<?=$categoryFilterId?>&mainCategoryFilter=<?=$mainCategoryFilterId?>',700,600);"><? }?></td>

												<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm"  >
			<?php }?>
			<? }?>
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
			<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($ingredientRecordSize) {
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
      				$nav.= " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\">>></a> ";
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
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
		</th>		
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Name</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Local Name</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Category</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Sub-Category</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Stock</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Rate</th>
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
			<?php
			while ($ir=$ingredientResultSetObj->getRow()) {
				$i++;
				$ingredientId = $ir[0];
				$ingredientCode	= stripSlash($ir[1]);
				$ingredientName	= stripSlash($ir[2]);
				$surname	= stripSlash($ir[3]);
				$qtyInStock	= ($ir[4]==0)?"":$ir[4];
				$stockInHand	= ($ir[5]==0)?"":$ir[5];
				$categoryName	= $ir[6];
				$mainCategoryName = $ir[7];
				$active=$ir[8];
				# Find Ing Rate
				$ingRate = $ingredientMasterObj->getIngCurrentRate($ingredientId,$latestIngRateListId);
			?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ingredientId;?>" class="chkBox">
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$ingredientName;?></td>
		<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" ><?=$surname?></td>
		<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="left"><?=$mainCategoryName?></td>
		<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="left"><?=$categoryName?></td>
		<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right"><?=$stockInHand?></td>
		<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right"><?=($ingRate!="")?$ingRate:"";?></td>
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ingredientId;?>,'editId');this.form.action='IngredientsMaster.php';" ><? } ?></td>
	<? }?>
	 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$brandId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$brandId;?>,'confirmId');"  >
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
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\">>></a> ";
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
		} else {
	?>
	<tr>
		<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientsMaster.php?categoryFilter=<?=$categoryFilterId?>&mainCategoryFilter=<?=$mainCategoryFilterId?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
<input type="hidden" name="hidCategoryFilterId" value="<?=$categoryFilterId?>">
<input type="hidden" name="hidMainCategoryFilterId" value="<?=$mainCategoryFilterId?>">
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
		
		<tr>
			<td height="10"></td>
		</tr>
		<!--<tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Sub-Category">Sub-Category</a></td></tr>-->
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
	//ensureInFrameset(document.frmIngredientsMaster);
	//-->
	</script>
<?php 
	}
?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
	//require("template/bottomRightNav.php");
?>
