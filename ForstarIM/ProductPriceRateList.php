<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$mode		= $g["mode"];
	$userId		= $sessObj->getValue("userId");
	$selection 	= "?pageNo=".$p["pageNo"];

	#------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	#----------------------------------------------------------


	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") $addMode = true;

	#Insert a Record
	if ($p["cmdAdd"]!="") {
	
		$rateListName	=	addSlash(trim($p["rateListName"]));		
		$startDate	=	mysqlDateFormat($p["startDate"]);
		$copyRateList	=	$p["copyRateList"];
		$productPriceCRateListId	= $p["hidCurrentRateListId"]; 
		
		if ($rateListName!="" && $p["startDate"]!="") {
	
				$distMarginRateListRecIns = $productPriceRateListObj->addProductPriceRateList($rateListName, $startDate, $copyRateList, $productPriceCRateListId, $userId);
				
				if ($distMarginRateListRecIns) {
					$sessObj->createSession("displayMsg",$msg_succAddProductPriceRateList);
					$sessObj->createSession("nextPage",$url_afterAddProductPriceRateList.$selection);
				} else {
					$addMode		=	true;
					$err			=	$msg_failAddProductPriceRateList;
				}
				$distMarginRateListRecIns	=	false;
		}
	}


	# Edit 
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$rateListRec		=	$productPriceRateListObj->find($editId);
		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);
		$array			=	explode("-",$rateListRec[2]);
		$startDate		=	$array[2]."/".$array[1]."/".$array[0];		
	}
	

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$rateListId	=	$p["hidRateListId"];
		
		$rateListName		=	addSlash(trim($p["rateListName"]));
		$dateS			=	explode("/",$p["startDate"]);
		$startDate		=	$dateS[2]."-".$dateS[1]."-".$dateS[0];
		
		if ($rateListId!="" && $rateListName!="") {
			$distMarginRateListRecUptd = $productPriceRateListObj->updateProductPriceRateList($rateListName, $startDate, $rateListId);
		}
	
		if ($distMarginRateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateProductPriceRateList);
			$sessObj->createSession("nextPage",$url_afterUpdateProductPriceRateList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateProductPriceRateList;
		}
		$distMarginRateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rateListId	=	$p["delId_".$i];
			
			$isRateListUsed = $productPriceRateListObj->checkRateListUse($rateListId);
			
			if ($rateListId!="" && !$isRateListUsed) {
				$productPriceRateListRecDel = $productPriceRateListObj->deleteProductPriceRateList($rateListId);
			}
		}
		if ($productPriceRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductPriceRateList);
			$sessObj->createSession("nextPage",$url_afterDelProductPriceRateList.$selection);
		} else {
			$errDel	=	$msg_failDelProductPriceRateList;
		}
		$productPriceRateListRecDel	=	false;
	}
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rateListId	=	$p["confirmId"];
			if ($rateListId!="") {
				// Checking the selected fish is link with any other process
				$productRecConfirm = $productPriceRateListObj->updateProductPriceRateListconfirm($rateListId);
			}

		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmproductPriceRateList);
			$sessObj->createSession("nextPage",$url_afterDelProductPriceRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$rateListId = $p["confirmId"];
			if ($rateListId!="") {
				#Check any entries exist
				
					$productRecConfirm =$productPriceRateListObj->updateProductPriceRateListReleaseconfirm($rateListId);
				
			}
		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmproductPriceRateList);
			$sessObj->createSession("nextPage",$url_afterDelProductPriceRateList.$selection);
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

	#List All Records
	$productPriceRateListRecords	= $productPriceRateListObj->fetchAllPagingRecords($offset, $limit);
	$productPriceRateListRecordSize	= sizeof($productPriceRateListRecords);

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($productPriceRateListObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {
		# Find the current rate List Id
		$currentRateListId	= $productPriceRateListObj->latestRateList();
	}

	if ($editMode)	$heading = $label_editProductPriceRateList;
	else 		$heading = $label_addProductPriceRateList;
		
	$ON_LOAD_PRINT_JS = "libjs/ProductPriceRateList.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	/*$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else*/ 
	require("template/btopLeftNav.php");
?>
	<form name="frmProductPriceRateList" action="ProductPriceRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<tr>
	  <td height="10" align="center">&nbsp;</td>
	  </tr>
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
											<tr>
												<td class="fieldName" nowrap >*Name </td>
												<td><INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Start Date </td>
												<td><INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8"></td>
											</tr>
											<? if($addMode==true){?>
											<tr>
												<td class="fieldName" nowrap>Copy From  </td>
			<td>
		      <select name="copyRateList" id="copyRateList" title="Click here if you want to copy all data from the Existing Rate list">
                      <option value="">-- Select --</option>
                      <?
			foreach($productPriceRateListRecords as $pprl) {
				$rateListId	=	$pprl[0];
				$rateListName		=	stripSlash($pprl[1]);
				$array			=	explode("-",$pprl[2]);
				$startDate		=	$array[2]."/".$array[1]."/".$array[0];
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if($selRateList==$rateListId) $selected = "Selected";
				?>
                      <option value="<?=$rateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                    </select></td></tr>
									<? }?>		
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>

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
				<!-- Form fields end   -->
			</td>
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
								$bxHeader="Product Price Rate List";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Price Rate List  </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
<table width="50%" align="center">
	<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>

												<?}?>
											</tr>
						<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
						<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Name </td>
												<td><INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Start Date </td>
												<td><INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8"></td>
											</tr>
											<? if($addMode==true){?>
											<tr>
												<td class="fieldName" nowrap>Copy From  </td>
			<td>
		      <select name="copyRateList" id="copyRateList" title="Click here if you want to copy all data from the Existing Rate list">
                      <option value="">-- Select --</option>
                      <?
			foreach($productPriceRateListRecords as $pprl) {
				$rateListId	=	$pprl[0];
				$rateListName		=	stripSlash($pprl[1]);
				$array			=	explode("-",$pprl[2]);
				$startDate		=	$array[2]."/".$array[1]."/".$array[0];
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if($selRateList==$rateListId) $selected = "Selected";
				?>
                      <option value="<?=$rateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                    </select></td></tr>
									<? }?>		
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPriceRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPriceRateListMaster(document.frmProductPriceRateList);">												</td>

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
			
			# Listing Grade Starts
		?>
</table>
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productPriceRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProductPriceRateList.php',700,600);"><? }?></td>
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
	if (sizeof($productPriceRateListRecords)>0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductPriceRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductPriceRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductPriceRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
                        <th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
                        <th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
                        <th nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>
			<? if($edit==true){?>
                        <th class="listing-head" width="45">&nbsp;</th>
			<? }?>
			<? if($confirm==true){?>
                        <th class="listing-head" width="45">&nbsp;</th>
			<? }?>
                      </tr>
	</thead>
	<tbody>
                      <?
			foreach ($productPriceRateListRecords as $pprl) {
				$i++;
				$rateListId	=	$pprl[0];
				$rateListName	=	stripSlash($pprl[1]);
				$startDate	=	dateFormat($pprl[2]);
				$active=$pprl[3];
				$existingrecords=$pprl[4];
			?>
                      <tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>> 
                        <td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$rateListId;?>" class="chkBox"></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$startDate?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$rateListId;?>,'editId'); this.form.action='ProductPriceRateList.php';"><? } ?></td>
			<? }?>


			 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$rateListId;?>,'confirmId');"  >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$rateListId;?>,'confirmId');"  >
			<?php } }?>
			<? }?>
			
			
			
			</td>
                      </tr>
                      <?
			}
			?>
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
      				$nav.= " <a href=\"ProductPriceRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductPriceRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductPriceRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
                      <?
												}
												else
												{
											?>
                      <tr bgcolor="white"> 
                        <td colspan="4"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?></td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productPriceRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProductPriceRateList.php',700,600);"><? }?></td></tr></table></td></tr>
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
<input type="hidden" name="hidCurrentRateListId" value="<?=$currentRateListId?>">	
	<tr>
	      <td height="10"></td>
      </tr>
	    <!--<tr><td height="10" align="center"><a href="Processes.php" class="link1"> Back to Pre-Process Rate Master</a></td></tr>-->
<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
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
	//ensureInFrameset(document.frmProductPriceRateList);
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