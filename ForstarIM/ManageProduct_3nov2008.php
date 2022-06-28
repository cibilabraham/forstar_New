<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once("lib/ManageProduct_ajax.php");
	
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;	

	$selection 	= "?pageNo=".$p["pageNo"]."&selProductCategory=".$p["selProductCategory"]."&selProductState =".$p["selProductState"]."&selProductGroup=".$p["selProductGroup"];
	
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
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
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

	#Cancel
	if ($p["cmdCancel"]!="") $addMode = false;

	#Variable Setting
	$selProductId	= $p["selProduct"];	

	if ($p["productCode"]!="") $productCode = $p["productCode"];
	if ($p["productName"]!="") $productName	= $p["productName"];
	if ($p["productCategory"]!="") $productCategory	= $p["productCategory"];
	if ($p["productState"]!="") $productState = $p["productState"];
	if ($p["productGroup"]!="") $productGroup = $p["productGroup"];
	if ($productState) $productGroupExist = $manageProductObj->checkProductGroupExist($productState);
	if ($p["identifiedNo"]!="") $identifiedNo = $p["identifiedNo"];
	//if ($p["newChapterSubhead"]!="") $newChapterSubhead = $p["newChapterSubhead"];
	
	#Add a Product
	if ($p["cmdAdd"]!="") {		
		$productCode	=	addSlash(trim($p["productCode"]));
		$productName	=	addSlash(trim($p["productName"]));

		$productCategory = $p["productCategory"];
		$productState 	 = $p["productState"];
		$productGroup 	 = ($p["productGroup"]=="")?0:$p["productGroup"];
		$netWt		= trim($p["netWt"]);
		$openingQty	= ($p["openingQty"]=="")?0:trim($p["openingQty"]);
		$identifiedNo	= trim($p["identifiedNo"]);		

		if ($productCode!="" && $productName!="" && $netWt!="") {
			$productRecIns = $manageProductObj->addProduct($productCode, $productName, $productCategory, $productState, $productGroup, $netWt, $openingQty, $userId, $identifiedNo);			
		}

		if ($productRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddProduct);
			$sessObj->createSession("nextPage",$url_afterAddProduct.$selection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddProduct;
		}
		$productRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" || $selProductId!="") {
		if ($selProductId) $editId = $selProductId;
		else $editId			=	$p["editId"];
		if (!$selProductId) $editMode		=	true;
		$productRec		=	$manageProductObj->find($editId);
		
		if (!$selProductId) $editProductId		=	$productRec[0];
		$productCode		=	stripSlash($productRec[1]);
		$productName		=	stripSlash($productRec[2]);
		if ($p["editSelectionChange"]=='1' || $p["productCategory"]=="") {
			$productCategory	= 	$productRec[3];
		} else {
			$productCategory	=	$p["productCategory"];
		}
		
		if ($p["editSelectionChange"]=='1' || $p["productState"]=="") {
			$productState	= 	$productRec[4];
		} else {
			$productState	=	$p["productState"];
		}		
		$productGroup 	= $productRec[5];
		$netWt		= $productRec[6];
		$openingQty	= $productRec[7];	
		$identifiedNo	= $productRec[8];		

		# Find whether the state has a Group
		if ($productState) $productGroupExist = $manageProductObj->checkProductGroupExist($productState);

		// Excise Duty Master Recs
		$curExciseDutyRateListId = $exciseDutyMasterObj->latestRateList();
		$exDutyRec = $manageProductObj->getExciseDutyMasterRec($productCategory, $productState, $productGroup, $curExciseDutyRateListId);
		$orgChapterSubhead = $exDutyRec[7];
		$exDutyMasterId = $exDutyRec[0];

		// Excise duty master record based on product id
		$exDutyProductRec = $manageProductObj->getExDutyByProductId($editProductId, $curExciseDutyRateListId);
		$exmptProductEntryId = $exDutyProductRec[0]; // Excise Duty Master Exemption code entry id
		$prdChapterSubhead = $exDutyProductRec[1];
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$productId = $p["hidProductId"];		
		$productCode	=	addSlash(trim($p["productCode"]));
		$productName	=	addSlash(trim($p["productName"]));
		$productCategory = $p["productCategory"];
		$productState 	 = $p["productState"];
		$productGroup 	 = ($p["productGroup"]=="")?0:$p["productGroup"];
		
		$netWt		= trim($p["netWt"]);
		$openingQty	= ($p["openingQty"]=="")?0:trim($p["openingQty"]);
		$hidOpeningQty	= trim($p["hidOpeningQty"]);
		$identifiedNo	= trim($p["identifiedNo"]);

		$newChapterSubhead 		= $p["newChapterSubhead"];
		$chaptSubExemptionActive	= $p["hidChShExemptionActive"];
		$exDutyMasterId			= $p["hidExDutyMasterId"];
		$exDtyExemptionMasterId		= $p["hidExDutyExemptionMasterId"];	
		
		if ($productId!="" && $productCode!="" && $productName!="") {
			$productRecUptd = $manageProductObj->updateProduct($productId, $productCode, $productName, $productCategory, $productState, $productGroup, $netWt, $openingQty, $hidOpeningQty, $identifiedNo, $userId);

			if ($chaptSubExemptionActive>0 && $exDtyExemptionMasterId=="" && $exDutyMasterId!="" && $newChapterSubhead!="") {		
				# Insert Exemption
				$insertExemption	= $manageProductObj->insertExCodeExemption($productId, $exDutyMasterId, $newChapterSubhead);
			} else if ($chaptSubExemptionActive>0 && $exDtyExemptionMasterId>0) {
				// Update
				$updateExemption	= $manageProductObj->updateExCodeExemption($exDtyExemptionMasterId, $newChapterSubhead);
			} else if ($chaptSubExemptionActive==0 && $exDtyExemptionMasterId>0) {
				// Delete Exemption
				$deleteExemption	= $manageProductObj->deleteExCodeExemption($exDtyExemptionMasterId);
			}
		}
	
		if ($productRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProduct.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductUpdate;
		}
		$productRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$productInUse	= false;
		for ($i=1; $i<=$rowCount; $i++) {
			$productId	=	$p["delId_".$i];
			if ($productId!="") {	
				# Checking Product Used any where
				$productUsed  = $manageProductObj->chkProductUsed($productId);
				if ($productId!="" && $productUsed) $productInUse = true;
				if ($productId!="" && !$productUsed) {			
					# Need to Chk 	
					$productRecDel = $manageProductObj->deleteProduct($productId);
				}				
			}
		}
		if ($productRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProduct);
			$sessObj->createSession("nextPage",$url_afterDelProduct.$selection);
		} else {
			if ($productInUse) {
				$disMsg = "<br>Please make sure the product does not exist in Product MRP Master/ Distributor Margin Structure/ Product Identifier/ Sales Order/ Product Management/ Excise Duty Master";
				$errDel = $msg_failDelProduct.$msgProductRecUsed.$disMsg;
			}
			else $errDel	= $msg_failDelProduct;			
		}
		$productRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productId	=	$p["confirmId"];
			if ($productId!="") {
				// Checking the selected fish is link with any other process
				$productRecConfirm = $manageProductObj->updateProductconfirm($productId);
			}

		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmProduct);
			$sessObj->createSession("nextPage",$url_afterDelProduct.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$productId = $p["confirmId"];
			if ($productId!="") {
				#Check any entries exist
				
					$productRecConfirm = $manageProductObj->updateProductReleaseconfirm($productId);
				
			}
		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmProduct);
			$sessObj->createSession("nextPage",$url_afterDelProduct.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") 		$pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="")	$pageNo = $g["pageNo"];
	else 				$pageNo = 1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["selProductCategory"]!="") $selProductCategoryId = $g["selProductCategory"];
	else $selProductCategoryId = $p["selProductCategory"];

	if ($g["selProductState"]!="") $selProductStateId = $g["selProductState"];
	else $selProductStateId = $p["selProductState"];

	if ($g["selProductGroup"]!="") $selProductGroupId = $g["selProductGroup"];
	else $selProductGroupId = $p["selProductGroup"];
		
	if ($p["cmdSearch"]) {
			$offset = 0;
			//$page 	= 0;
		}

	#List all Records
	$productRecords = $manageProductObj->fetchAllPagingRecords($offset, $limit, $selProductCategoryId, $selProductStateId, $selProductGroupId);
	$productRecordSize    = sizeof($productRecords);

	## -------------- Pagination Settings II -------------------
	$fetchProductRecords = $manageProductObj->fetchAllRecords($selProductCategoryId, $selProductStateId, $selProductGroupId);	// fetch All Records
	$numrows	=  sizeof($fetchProductRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	


		#List all Product Category Records
		//$productCategoryRecords = $productCategoryObj->fetchAllRecords();
		$productCategoryRecords = $productCategoryObj->fetchAllRecordsActiveCategory();
		#List all Product State Records
		//$productStateRecords = $productStateObj->fetchAllRecords();
		$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();

		if ($addMode || $editMode) {
			#List all Product Group Records
			//$productGroupRecords =$productGroupObj->fetchAllRecords();	
			$productGroupRecords =$productGroupObj->fetchAllRecordsActiveGroup();
			
			# Get product records
			$selProductRecs = $manageProductObj->getAllProductRecs();
		}		

	// Ex Duty Rate List Id
	$exDutyRateListId = $exciseDutyMasterObj->latestRateList();

	if ($editMode) $heading	= $label_editProduct;
	else	       $heading	= $label_addProduct;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/ManageProduct.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	/*$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else*/ 
	require("template/btopLeftNav.php");
?>
	<form name="frmManageProduct" action="ManageProduct.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
	<!--<tr><td height="10" align="center"><a href="ProductCategory.php" class="link1" title="Click to Manage Product Category">Product Category</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ProductState.php" class="link1">Product State</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ProductGroup.php" class="link1">Product Group</a></td>	
	</tr>-->
	<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product Category" onclick="parent.openTab('ProductCategory.php');">Product Category</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductState.php');">Product State</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductGroup.php');">Product Group</a></td>	
	</tr>
	<tr>
		<? if($err!="" ){?>
		<tr>
			<td height="20" align="center" class="err1" ><?=$err;?></td>			
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
								$bxHeader="Manage Product";
								include "template/boxTL.php";
							?>
			<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr><TD colspan="3" align="center">
	<table width="60%">
	<?
			if ( $editMode || $addMode) {
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>-->
	<tr>
		<td width="1" ></td>
		<td colspan="2" >
		<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
		<tr>
			<td colspan="4" height="10" ></td>
		</tr>
		<tr>
		<? if($editMode){?>
		<td colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageProduct.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageProduct(document.frmManageProduct);">	
		</td>
		<?} else{?>
		<td  colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageProduct.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageProduct(document.frmManageProduct);">&nbsp;&nbsp;		
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidProductId" value="<?=$editProductId;?>">
		<tr>
			<td nowrap >&nbsp;</td>
			  <td>&nbsp;</td>
		  </tr>
		<?php
			 if ($addMode) { 
		?>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
			<fieldset>
			<legend class="listing-item" onMouseover="ShowTip('Copy from existing Product and save after editing.');" onMouseout="UnTip();">Copy From</legend>
			<table>
				<TR>
					<TD class="fieldName" onMouseover="ShowTip('Copy from existing Product and save after editing.');" onMouseout="UnTip();">Product:</TD>
					<td>
						<select name="selProduct" id="selProduct" onchange="this.form.submit();">
							<option value="">--Select--</option>
							<?php
								foreach ($selProductRecs as $spr) {
									$sProductId 	= $spr[0];
									$sProuctName	= stripSlash($spr[2]);
									$selected = "";
									if ($selProductId==$sProductId) $selected = "selected";
							?>
							<option value="<?=$sProductId?>" <?=$selected?>><?=$sProuctName?></option>
							<? }?>
						</select>
					</td>
				</TR>
			</table>
			</fieldset>
		  </td>
		</tr>
		<?php
			}
		?>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
					<table width="200">
                                                <tr>
                                                  <td class="fieldName">*Code : </td>
                                                  <td class="listing-item">
							<input type="text" name="productCode" id="productCode" value="<?=$productCode?>" size="14" onblur="xajax_chkPCodeExist(document.getElementById('productCode').value, '<?=$editProductId?>');" autocomplete="off">
							<input type="hidden" name="hidProductCode" id="hidProductCode" value="<?=$productCode?>" size="14">
							<input type="hidden" name="hidPCodeExist" id="hidPCodeExist" value="">
							<span id="pcodeExist" class="err1" style="line-height:normal;" nowrap="true"></span>
						</td>						
                                                </tr>
						<tr>
                                                  <td class="fieldName" nowrap="true">Identification No:</td>
                                                  <td class="listing-item">
							<input type="text" name="identifiedNo" id="identifiedNo" value="<?=$identifiedNo?>" size="18" maxlength="16" autocomplete="off" onblur="xajax_chkIdentifiedNoExist(document.getElementById('identifiedNo').value, '<?=$editProductId?>');" />
							<input type="hidden" name="hidPIdentifiedNoExist" id="hidPIdentifiedNoExist" value="">
							<span id="pIdentifiedNoExist" class="err1" style="line-height:normal;" nowrap="true"></span>
						</td>
                                                </tr>						
                                                <tr>
                                                  <td class="fieldName">*Name:</td>
                                                  <td class="listing-item">
							<input type="text" name="productName" value="<?=$productName?>" size="45">
						</td>
                                                </tr>
						<tr>
                                                 <td class="fieldName">*Category:</td>
                                                 <td class="listing-item">
						 <select name="productCategory">
						 <option value="">-- Select --</option>
						 <?
						 foreach ($productCategoryRecords as $cr) {
							$categoryId	= $cr[0];
							$categoryName	= stripSlash($cr[1]);
							$selected = "";
							if ($productCategory==$categoryId) $selected = "Selected";
						 ?>
						 <option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
						 <? }?>
						 </select>
						 </td>
                                                </tr>
						<tr>
                                                 <td class="fieldName">*State:</td>
                                                 <td class="listing-item">
						 <select name="productState" onchange="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
						 <option value="">-- Select --</option>
						 <?
						 foreach ($productStateRecords as $cr) {
							$prodStateId	= $cr[0];
							$prodStateName	= stripSlash($cr[1]);
							$selected = "";
							if ($productState==$prodStateId) $selected = "Selected";
						 ?>
						 <option value="<?=$prodStateId?>" <?=$selected?>><?=$prodStateName?></option>
						 <? }?>
						 </select>
						 </td>
                                                </tr>
					<? if ($productGroupExist) {?>
						<tr>
                                                 <td class="fieldName">*Group:</td>
                                                 <td class="listing-item">
						 <select name="productGroup">
						 <option value="">-- Select --</option>
						 <?
						 foreach ($productGroupRecords as $gr) {
							$prodGroupId	= $gr[0];
							$prodGroupName	= stripSlash($gr[1]);
							$selected = "";
							if ($productGroup==$prodGroupId) $selected = "Selected";
						 ?>
						 <option value="<?=$prodGroupId?>" <?=$selected?>><?=$prodGroupName?></option>
						 <? }?>
						 </select>
						 </td>
                                                </tr>						
						<? }?>
						<?php
						if ($editMode) {
						?>	
						<tr>
                                                  <td class="fieldName" nowrap="true" title="Enter new chapter/subheading">Chapter/Subheading:</td>
                                                  <td class="listing-item">
							<table cellpadding="0" cellspacing="0">
								<TR>
									<TD class="listing-item" nowrap><?=$orgChapterSubhead?>&nbsp;&nbsp;<a href="###" onclick="changeChaptSubHead()" class="link1" id="changeHrf" style="display:<?=($prdChapterSubhead!="")?'none':''?>;">Change</a>
									</TD>
								</TR>
								<tr id="chaptSubheadTr" style="display:<?=($prdChapterSubhead!="")?"":"none"?>;">
									<TD>
										<input type="text" name="newChapterSubhead" id="newChapterSubhead" value="<?=($prdChapterSubhead!="")?$prdChapterSubhead:$orgChapterSubhead?>" size="18" maxlength="16" autocomplete="off" />						
										<input type="hidden" name="hidPExciseCodeExist" id="hidPExciseCodeExist" value="">
										<span id="pExciseCodeExist" class="err1" style="line-height:normal;" nowrap="true"></span>
									</TD>
									<td>
										<a href="###" border="0" title="Remove Chapter/Subheading Exemption" onclick="removeChaptSubHeadExmpt();">
											<img style="cursor: pointer;" src="images/x.png" border="0" />
										</a>								
									</td>
								</tr>
							</table>							
						<input type="hidden" name="hidOrgChaptSubhead" id="hidOrgChaptSubhead" value="<?=$orgChapterSubhead?>" readonly />
					<input type="hidden" name="hidChShExemptionActive" id="hidChShExemptionActive" value="<?=($prdChapterSubhead!="")?1:0?>" readonly />
					<input type="hidden" name="hidExDutyMasterId" id="hidExDutyMasterId" value="<?=$exDutyMasterId?>" readonly />
					<input type="hidden" name="hidExDutyExemptionMasterId" id="hidExDutyExemptionMasterId" value="<?=$exmptProductEntryId?>" readonly />		
						</td>
                                                </tr>		
						<?php
						}
						?>				
						<tr>
							<TD class="fieldName">*Net Wt:</TD>
							<td class="listing-item">
								<input type="text" name="netWt" value="<?=$netWt?>" size="6" style="text-align:right;">&nbsp;Gms
							</td>
						</tr>
						<!--tr>
							<TD class="fieldName" nowrap="true">Qty in Stock:</TD>
							<td>
								<input type="text" name="openingQty" value="<?=$openingQty?>" size="6" style="text-align:right;">
								<input type="hidden" name="hidOpeningQty" value="<?=$openingQty?>" size="6" style="text-align:right;">
							</td>
						</tr-->
                                              </table>
					</td>
					</tr>
					<tr>
					  <td colspan="2" height="5"></td>
					</tr>	
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
				<td colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageProduct.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageProduct(document.frmManageProduct);">	
				</td>
				<?} else{?>
				<td  colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageProduct.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageProduct(document.frmManageProduct);">&nbsp;&nbsp;			</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
				<!--input type="hidden" name="stockType" value="<?=$stockType?>"-->
				</tr>
		<tr>
			<td colspan="2"  height="10" >
				<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyCheked?>">
			</td>
		</tr>
		</table></td>
		</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
	<!-- Form fields end   --></td>
		</tr>
		<?
			}			
			# Listing Category Starts
		?>
	</table>
				</TD></tr>
	<?php
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<?php }?>
				<tr>
					<td colspan="3" align="center">
						<table width="50%" height="50%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table  cellspacing="4" cellpadding="0">
						<tr>
							<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Category</td>
							<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductCategory" id="selProductCategory">
		<option value=''>--Select All--</option>";
		<?php
		if (sizeof($productCategoryRecords)>0) {	
			 foreach ($productCategoryRecords as $cr) {
				$categoryId	= $cr[0];
				$categoryName	= stripSlash($cr[1]);
				$selected = "";
				if ($selProductCategoryId==$categoryId) $selected = "Selected";
		?>	
		<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>	
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">State</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductState" id="selProductState" onChange="xajax_getProductGroupExist(document.getElementById('selProductState').value, '');">
		<option value=''>--Select All--</option>";
		<?php
		if (sizeof($productStateRecords)>0) {	
			foreach ($productStateRecords as $cr) {
				$prodStateId	= $cr[0];
				$prodStateName	= stripSlash($cr[1]);
				$selected = "";
				if ($selProductStateId==$prodStateId) $selected = "Selected";
		?>	
		<option value="<?=$prodStateId?>" <?=$selected?>><?=$prodStateName?></option>
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Group</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductGroup" id="selProductGroup">
		<option value=''>--Select All--</option>
		</select>
	</td>
	<td style="padding-right:10px;">
		<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value=" Search ">
	</td>
	</tr>				
	</table>
						<?php
							require("template/rbBottom.php");
						?>
						</TD></TR></table>
					</td>
				</tr>
							<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Manage Product</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
		
	</td>
		</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>						
			<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintManageProduct.php?selProductCategory=<?=$selProductCategoryId?>&selProductState=<?=$selProductStateId?>&selProductGroup=<?=$selProductGroupId?>',700,600);"><?}?></td>
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
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if ( sizeof($productRecords) > 0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ManageProduct.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ManageProduct.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ManageProduct.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
		<th align="center" style="padding-left:10px; padding-right:10px;">Code</th>
		<th align="center" style="padding-left:10px; padding-right:10px;">Identification No</th>
		<th align="center" style="padding-left:10px; padding-right:10px;">Chapter/Subheading</th>
		<th style="padding-left:10px; padding-right:10px;">Name</th>
		<th style="padding-left:10px; padding-right:10px;">Net Wt</th>
		<!--<th style="padding-left:10px; padding-right:10px;">Opening Qty</th>-->
		<th style="padding-left:10px; padding-right:10px;">Actual Qty</th>
		<? if($edit==true){?>
		<th>&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th>&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach ($productRecords as $pr) {
		$i++;
		$productId	= $pr[0];
		$productCode	= $pr[1];
		$productName	= $pr[2];
		$pNetWt		= $pr[3];	
		$openingStock	= $pr[4];
		$actualStk	= $pr[5];
		$pIdentifiedNo	= $pr[6];

		$pCategoryId	= $pr[7];
		$pStateId	= $pr[8];
		$pGroupId	= $pr[9];
		
		$pExciseCode = "";
		$exDtyPrdRec = $manageProductObj->getExDutyByProductId($productId, $exDutyRateListId);	 
		$pExmptExciseCode = $exDtyPrdRec[1];
		$pExciseCode = $pExmptExciseCode;
		if ($pExmptExciseCode=="") {
			$exDutyRec = $manageProductObj->getExciseDutyMasterRec($pCategoryId, $pStateId, $pGroupId, $exDutyRateListId);
			$pExciseCode = $exDutyRec[7];
		}
		$active=$pr[10];
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$pIdentifiedNo;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$pExciseCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$pNetWt;?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$openingStock;?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=($actualStk!=0)?$actualStk:"";?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ManageProduct.php';"><? } ?>
		</td>
		<? }?>

		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$productId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$productId;?>,'confirmId');"  >
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
		<tr>
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ManageProduct.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ManageProduct.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ManageProduct.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
	<tr bgcolor="white">
		<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table>
<input type="hidden" name="productStateGroup" value="<?=$productGroupExist?>">
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
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintManageProduct.php?selProductCategory=<?=$selProductCategoryId?>&selProductState=<?=$selProductStateId?>&selProductGroup=<?=$selProductGroupId?>',700,600);"><?}?></td>
											</tr>
										</table>			</td>
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
	<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product Category" onclick="parent.openTab('ProductCategory.php');">Product Category</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductState.php');">Product State</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductGroup.php');">Product Group</a></td>	
	</tr>
<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">
	<input type="hidden" name="hidEditMode" id="hidEditMode" value="<?=$editMode?>">
	</table> 
	<script language="JavaScript" type="text/javascript">
		xajax_getProductGroupExist('<?=$selProductStateId?>', '<?=$selProductGroupId?>');
	</script>
	<?php 
		if ($selProductId!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_chkPCodeExist('<?=$productCode?>', '<?=$editProductId?>');
		xajax_chkIdentifiedNoExist('<?=$identifiedNo?>', '<?=$editProductId?>');
		//xajax_chkExciseCodeExist('<?=$newChapterSubhead?>', '<?=$editProductId?>');
	</script>
	<?php
		}
	?>
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
	//ensureInFrameset(document.frmManageProduct);
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
