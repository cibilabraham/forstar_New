<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selection 	=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
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
//----------------------------------------------------------

	
	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdAddCategory"]!="" ){
	
		$category	=	$p["fishType"];
		
		if ($category!="")  {
			$categoryRecIns	=	$fishcategoryObj->addCategory($category);
				
			if ($categoryRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddFishCategory);
				$sessObj->createSession("nextPage",$url_afterAddFishCategory.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddFishCategory;
			}
			$categoryRecIns	=	false;
		}
	}
	
	# Edit Category 
	if( $p["editId"]!="" ){
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$categoryRec		=	$fishcategoryObj->find($editId);
		
		$editCategoryId		=	$categoryRec[0];
		$fishType		=	stripSlash($categoryRec[1]);
	}
	
	
	if ($p["cmdSaveChange"]!="") {
		
		$categoryId		=	$p["hidCategoryId"];		
		$category	=	$p["fishType"];		
		if ($categoryId!="" && $category!="") {
			$categoryRecUptd	=	$fishcategoryObj->updateCategory($category,$categoryId);
		}
	
		if ($categoryRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateFishCategory);
			$sessObj->createSession("nextPage",$url_afterUpdateFishCategory.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFishCategory;
		}
		$categoryRecUptd	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$categoryId = $p["confirmId"];

			if ($categoryId!="") {
				#Check any entries exist
				
					$categoryRecDel = $fishcategoryObj->updateCategoryconfirm($categoryId);
				
			}
		}
		if ($categoryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succConfirmFishCategory);
			$sessObj->createSession("nextPage",$url_afterDelFishCategory.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmFishCategory;
		}






	}

if ($p["btnRlConfirm"]!="")
	{
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$categoryId = $p["confirmId"];

			if ($categoryId!="") {
				#Check any entries exist
				
					$categoryRecDel = $fishcategoryObj->updateCategoryReleaseconfirm($categoryId);
				
			}
		}
		if ($categoryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmFishCategory);
			$sessObj->createSession("nextPage",$url_afterDelFishCategory.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmFishCategory;
		}






	}


	
	# Delete Category
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$categoryId = $p["delId_".$i];

			if ($categoryId!="") {
				#Check any entries exist
				if (!($fishcategoryObj->moreEntriesExist($categoryId))) {
					$categoryRecDel = $fishcategoryObj->deleteCategory($categoryId);
				}
			}
		}
		if ($categoryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelFishCategory);
			$sessObj->createSession("nextPage",$url_afterDelFishCategory.$selection);
		} else {
			$errDel	=	$msg_failDelFishCategory;
		}
		$categoryRecDel	=	false;
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="")		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "")	$pageNo=$g["pageNo"];
	else				$pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# List All Category
	$categoryRecords	=	$fishcategoryObj->fetchAllPagingRecords($offset, $limit,$confirm);
	$categoryRecordsSize	=	sizeof($categoryRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fishcategoryObj->fetchAllRecords($confirm));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------	

	if ($editMode)	$heading = $label_editFishCategory;
	else $heading	=	$label_addFishCategory;
	
	$help_lnk="help/hlp_Category.html";
	
	$ON_LOAD_PRINT_JS	= "libjs/fishcategory.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmFishCategory" action="FishCategory.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<tr>
	  <td height="10" align="center">&nbsp;</td>
	  </tr>
	<tr><td height="10" align="center"><a href="FishMaster.php" class="link1"> Fish Master</a></td></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidCategoryId" value="<?=$editCategoryId;?>">
											<tr>
												<td class="fieldName" nowrap >*Fish Type </td>
												<td <?php if ($active==0){?> bgcolor="#6699ff" <?php }?> ><INPUT NAME="fishType" TYPE="text" id="fishType" value="<?=$fishType;?>" size="20"></td>
											</tr>
											
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>

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
			
			# Listing Grade Starts
		?>
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
								$bxHeader="Fish Category";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Category </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%">
	<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidCategoryId" value="<?=$editCategoryId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Fish Type </td>
												<td><INPUT NAME="fishType" TYPE="text" id="fishType" value="<?=$fishType;?>" size="20"></td>
											</tr>							
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCategory" class="button" value=" Add " onClick="return validateAddFishCategory(document.frmFishCategory);">												</td>

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
			
			# Listing Grade Starts
		?>
	</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$categoryRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFishCategory.php',700,600);"><? }?>
												
												
												
												</td>
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
									<td colspan="2" >
							<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
                      									<?
												if( sizeof($categoryRecords) > 0 )
												{
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
      				$nav.= " <a href=\"FishCategory.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FishCategory.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FishCategory.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
                        <th nowrap   >Fish Type</th>
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

													foreach($categoryRecords as $cr) {		
														$i++;
														$categoryId	=	$cr[0];
														$categoryName	=	stripSlash($cr[1]);
														$active=$cr[2];
														$existingcount=$cr[3];

														//echo "-----$active";
														
											?>
                      <tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>  > 
                        <td width="20" align="center">
						<?php 
						if ($existingcount==0) {?>						
						<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$categoryId;?>" class="chkBox"></td>
                        <?php 
						}
						?>
					<td class="listing-item" nowrap><?=$categoryName;?></td>
			<? if($edit==true){?>
                     <td class="listing-item" width="45" align="center">   <?php if ($active!=1){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$categoryId;?>,'editId'); this.form.action='FishCategory.php';"><?php }
					 ?></td>

			<? }?>

			<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');" >
			<?php } else 
			if ($active==1){ 
			//if ($existingcount==0) {?>
				<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$categoryId;?>,'confirmId');" >
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
      				$nav.= " <a href=\"FishCategory.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FishCategory.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FishCategory.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
                        <td colspan="3"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?>
                        </td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$categoryRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFishCategory.php',700,600);"><? }?></td>
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
		      <td height="10"></td>
      </tr>
	    <tr><td height="10" align="center"><a href="FishMaster.php" class="link1"> Fish Master</a></td></tr>
	</table>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>