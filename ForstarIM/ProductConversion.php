<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$userId		=	$sessObj->getValue("userId");

	$selection 	= "?pageNo=".$p["pageNo"];
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

	#Cancel
	if ($p["cmdCancel"]!="") $addMode = false;


	#Variable Setting
	if ($p["productCode"]!="") $productCode = $p["productCode"];
	if ($p["productName"]!="") $productName	= $p["productName"];
	if ($p["selProduct"]!="") $selProduct = $p["selProduct"];

/*	if ($p["productCategory"]!="") $productCategory	= $p["productCategory"];
	if ($p["productState"]!="") $productState = $p["productState"];
	if ($p["productGroup"]!="") $productGroup = $p["productGroup"];	
	if ($p["gmsPerPouch"]!="") $gmsPerPouch = $p["gmsPerPouch"]; // Net Wt of the product	
	if ($p["fixedQtyCheked"]!="") $fixedQtyCheked = $p["fixedQtyCheked"];
*/
	
	#Add a Product
	if ($p["cmdAdd"]!="") {

		$itemCount	=	$p["hidItemCount"];
		$productCode	=	$p["productCode"];
		$productName	=	$p["productName"];

		$productCategory = $p["productCategory"];
		$productState 	 = $p["productState"];
		$productGroup 	 = ($p["productGroup"]=="")?0:$p["productGroup"];
		
		$gmsPerPouch 	 = $p["productGmsPerPouch"];		

		$selProduct = $p["selProduct"];		// Base product Id

		// Reference fields
		$productRatePerPouch 	= $p["productRatePerPouch"];
		$fishRatePerPouch	= $p["fishRatePerPouch"];
		$gravyRatePerPouch	= $p["gravyRatePerPouch"];
		$productGmsPerPouch	= $p["productGmsPerPouch"];
		$fishGmsPerPouch	= $p["fishGmsPerPouch"];
		$gravyGmsPerPouch	= $p["gravyGmsPerPouch"];
		$productPercentagePerPouch = $p["productPercentagePerPouch"];
		$fishPercentagePerPouch	= $p["fishPercentagePerPouch"];
		$gravyPercentagePerPouch = $p["gravyPercentagePerPouch"];
		$productRatePerKgPerBatch = $p["productRatePerKgPerBatch"];
		$fishRatePerKgPerBatch 	= $p["fishRatePerKgPerBatch"];
		$gravyRatePerKgPerBatch = $p["gravyRatePerKgPerBatch"];
		$pouchPerBatch		= $p["pouchPerBatch"];
		$productRatePerBatch	= $p["productRatePerBatch"];
		$fishRatePerBatch	= $p["fishRatePerBatch"];
		$gravyRatePerBatch	= $p["gravyRatePerBatch"];
		$productKgPerBatch	= $p["productKgPerBatch"];
		$fishKgPerBatch		= $p["fishKgPerBatch"];
		$gravyKgPerBatch	= $p["gravyKgPerBatch"];
		$productRawPercentagePerPouch = $p["productRawPercentagePerPouch"];
		$fishRawPercentagePerPouch = $p["fishRawPercentagePerPouch"];
		$gravyRawPercentagePerPouch = $p["gravyRawPercentagePerPouch"];
		$productKgInPouchPerBatch = $p["productKgInPouchPerBatch"];
		$fishKgInPouchPerBatch	= $p["fishKgInPouchPerBatch"];
		$gravyKgInPouchPerBatch	= $p["gravyKgInPouchPerBatch"];
		$fishPercentageYield	= $p["fishPercentageYield"];
		$gravyPercentageYield 	= $p["gravyPercentageYield"];
		$totalFixedFishQty	= $p["totalFixedFishQty"];

		if ($productCode!="" && $productName!="") {

			$productRecIns = $productConversionObj->addProduct($productCode, $productName, $userId, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $selProduct);

			#Find the Last inserted Id From m_productmaster Table
			$lastId = $databaseConnect->getLastInsertedId();
			
			for ($i=1; $i<=$itemCount; $i++) {

				$ingredientId	= $p["selIngredient_".$i];
				$quantity	= trim($p["quantity_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];
				$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];
	
				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];

				if ($lastId!="" && $ingredientId!="" && $quantity!="") {
					$productItemsIns = $productConversionObj->addIngredientEntries($lastId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch);
				}
			}
		}

		if ($productRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddProductConversion);
			$sessObj->createSession("nextPage",$url_afterAddProductConversion.$selection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddProductConversion;
		}
		$productRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$productMasterRec	=	$productConversionObj->find($editId);
		
		$editProductId		=	$productMasterRec[0];
		$productCode		=	$productMasterRec[1];
		$productName		=	$productMasterRec[2];

		$productCategory	= 	$productMasterRec[3];
		$productState		= 	$productMasterRec[4];
		if ($p["editSelectionChange"]=='1' || $p["selProduct"]=="") {
			$selProduct =	$editProductId;
			//$selRefProductId =	$productMasterRec[36];
		} else {
			$selProduct =	$p["selProduct"];
		}
		
		$selRefProductId =	$productMasterRec[36];

		//echo "$selProduct";				
		$productGroup 		= $productMasterRec[5];
		$gmsPerPouch 	 	= $productMasterRec[6];

		////////
		$productRatePerPouch 	= $productMasterRec[7];
		$fishRatePerPouch	= $productMasterRec[8];
		$gravyRatePerPouch	= $productMasterRec[9];
		$productGmsPerPouch	= $productMasterRec[10];
		$fishGmsPerPouch	= $productMasterRec[11];
		$gravyGmsPerPouch	= $productMasterRec[12];
		$productPercentagePerPouch = $productMasterRec[13];
		$fishPercentagePerPouch	= $productMasterRec[14];
		$gravyPercentagePerPouch = $productMasterRec[15];
		$productRatePerKgPerBatch = $productMasterRec[16];
		$fishRatePerKgPerBatch 	= $productMasterRec[17];
		$gravyRatePerKgPerBatch = $productMasterRec[18];
		$pouchPerBatch		= $productMasterRec[19];
		$productRatePerBatch	= $productMasterRec[20];
		$fishRatePerBatch	= $productMasterRec[21];
		$gravyRatePerBatch	= $productMasterRec[22];
		$productKgPerBatch	= $productMasterRec[23];
		$fishKgPerBatch		= $productMasterRec[24];
		$gravyKgPerBatch	= $productMasterRec[25];
		$productRawPercentagePerPouch = $productMasterRec[26];
		$fishRawPercentagePerPouch = $productMasterRec[27];
		$gravyRawPercentagePerPouch = $productMasterRec[28];
		$productKgInPouchPerBatch = $productMasterRec[29];
		$fishKgInPouchPerBatch	= $productMasterRec[30];
		$gravyKgInPouchPerBatch	= $productMasterRec[31];
		$fishPercentageYield	= $productMasterRec[32];
		$gravyPercentageYield 	= $productMasterRec[33];
		$totalFixedFishQty	= $productMasterRec[34];		
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$productId = $p["hidProductId"];

		$itemCount	=	$p["hidItemCount"];
		$productCode	=	$p["productCode"];
		$productName	=	$p["productName"];
		$productCategory = $p["productCategory"];
		$productState 	 = $p["productState"];
		$productGroup 	 = ($p["productGroup"]=="")?0:$p["productGroup"];
		
		$selProduct = $p["selProduct"];

		$gmsPerPouch 	 = $p["productGmsPerPouch"];

		// Reference fields
		$productRatePerPouch 	= $p["productRatePerPouch"];
		$fishRatePerPouch	= $p["fishRatePerPouch"];
		$gravyRatePerPouch	= $p["gravyRatePerPouch"];
		$productGmsPerPouch	= $p["productGmsPerPouch"];
		$fishGmsPerPouch	= $p["fishGmsPerPouch"];
		$gravyGmsPerPouch	= $p["gravyGmsPerPouch"];
		$productPercentagePerPouch = $p["productPercentagePerPouch"];
		$fishPercentagePerPouch	= $p["fishPercentagePerPouch"];
		$gravyPercentagePerPouch = $p["gravyPercentagePerPouch"];
		$productRatePerKgPerBatch = $p["productRatePerKgPerBatch"];
		$fishRatePerKgPerBatch 	= $p["fishRatePerKgPerBatch"];
		$gravyRatePerKgPerBatch = $p["gravyRatePerKgPerBatch"];
		$pouchPerBatch		= $p["pouchPerBatch"];
		$productRatePerBatch	= $p["productRatePerBatch"];
		$fishRatePerBatch	= $p["fishRatePerBatch"];
		$gravyRatePerBatch	= $p["gravyRatePerBatch"];
		$productKgPerBatch	= $p["productKgPerBatch"];
		$fishKgPerBatch		= $p["fishKgPerBatch"];
		$gravyKgPerBatch	= $p["gravyKgPerBatch"];
		$productRawPercentagePerPouch = $p["productRawPercentagePerPouch"];
		$fishRawPercentagePerPouch = $p["fishRawPercentagePerPouch"];
		$gravyRawPercentagePerPouch = $p["gravyRawPercentagePerPouch"];
		$productKgInPouchPerBatch = $p["productKgInPouchPerBatch"];
		$fishKgInPouchPerBatch	= $p["fishKgInPouchPerBatch"];
		$gravyKgInPouchPerBatch	= $p["gravyKgInPouchPerBatch"];
		$fishPercentageYield	= $p["fishPercentageYield"];
		$gravyPercentageYield 	= $p["gravyPercentageYield"];
		$totalFixedFishQty	= $p["totalFixedFishQty"];
		
		if ($productId!="" && $productCode!="" && $productName!="") {
			$productMasterRecUptd = $productConversionObj->updateProductMaster($productId, $productCode, $productName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $selProduct);
		
			#Delete First all records from Product master entry table
			$deleteIngredientItemRecs = $productConversionObj->deleteIngredientItemRecs($productId);
			
			for ($i=1; $i<=$itemCount; $i++) {
				$ingredientId	=	$p["selIngredient_".$i];
				$quantity	=	trim($p["quantity_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];
				$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];

				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];
					
				if ($productId!="" && $ingredientId!="" && $quantity!="") {
					$productItemsIns = $productConversionObj->addIngredientEntries($productId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch);
				}
			}
		}
	
		if ($productMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductConversionUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductConversion.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductConversionUpdate;
		}
		$productMasterRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productId	=	$p["delId_".$i];

			if ($productId!="" ) {
				$deleteIngredientItemRecs =	$productConversionObj->deleteIngredientItemRecs($productId);
				$productMasterRecDel = $productConversionObj->deleteProductMaster($productId);
			}
		}
		if ($productMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductConversion);
			$sessObj->createSession("nextPage",$url_afterDelProductConversion.$selection);
		} else {
			$errDel	=	$msg_failDelProductConversion;
		}
		$productMasterRecDel	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List all Records
	$productConversionRecords = $productConversionObj->fetchAllPagingRecords($offset, $limit);
	$productConversionRecordsize    = sizeof($productConversionRecords);

	## -------------- Pagination Settings II -------------------
	$fetchAllProductConversionRecords = $productConversionObj->fetchAllRecords();	// fetch All Records
	$numrows	=  sizeof($fetchAllProductConversionRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

/*
	#List all Ingredient
	if ($addMode || $editMode) {
		$selRateList = $ingredientRateListObj->latestRateList();
		$ingredientRecords = $ingredientRateMasterObj->fetchAllIngredientRecords($selRateList);
	}	
*/

	if ($addMode || $editMode) {
		#List all Product Records
		$productMasterRecords = $productMasterObj->filterBaseProductRecords();
	}


	if ($p["editSelectionChange"]=='1') {
		$productRefId = $editProductId;
	} else {
		$productRefId = $editProductId;
	}	
	if ($selProduct!="") {
		list($proCode, $proName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty) = $productMasterObj->getProductRec($selProduct);	
	}

	if ($editMode) $heading	= $label_editProductConversion;
	else	       $heading	= $label_addProductConversion;
	
	$ON_LOAD_PRINT_JS	= "libjs/ProductConversion.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProductConversion" action="ProductConversion.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%" >
		<tr><TD height="10"></TD></tr>
	<!--tr><td height="10" align="center"><a href="ProductCategory.php" class="link1" title="Click to Manage Product Category">Product Category</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ProductState.php" class="link1">Product State</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ProductGroup.php" class="link1">Product Group</a></td>	
	</tr-->
	<tr>
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="4" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductConversion.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductConversionMaster(document.frmProductConversion);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductConversion.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductConversionMaster(document.frmProductConversion);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidProductId" value="<?=$editProductId;?>">
											
											<tr>
											  <td nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
						<tr>
						  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
					<table width="200">
                                                <tr>
                                                  <td class="fieldName">*Code : </td>
                                                  <td class="listing-item"><input type="text" name="productCode" value="<?=$productCode?>" size="10"></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Name:</td>
                                                  <td class="listing-item"><input type="text" name="productName" value="<?=$productName?>" size="30"></td>
                                                </tr>
						<tr>
                                                  <td class="fieldName" nowrap>*Reference Product:</td>
                                                <td class="listing-item">
						<? if ($addMode) {?>
						 <select name="selProduct" id="selProduct" onchange="this.form.submit();">
						<? } else {?>
						<select name="selProduct" id="selProduct" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();">
						<? }?>
						<option value="">-- Select --</option>
						<?
						foreach ($productMasterRecords as $pmr) {
							$productId	=	$pmr[0];
							$productCode	=	$pmr[1];
							$productName	=	$pmr[2];
							$selected = "";
							if ($selProduct==$productId || $selRefProductId==$productId) $selected = "Selected";
						?>
						<option value="<?=$productId?>" <?=$selected?>><?=$productName?></option>
						  <? }?>
                                                  </select>
						</td>
                                                </tr>
						<!--tr>
                                                 <td class="fieldName">Category:</td>
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
                                                </tr-->
						
						<!--tr>
                                                 <td class="fieldName">State:</td>
                                                 <td class="listing-item">
						<? if ($addMode) {?>
						 <select name="productState" onchange="this.form.submit();">
						<? } else {?>
						<select name="productState" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();">
						<? }?>
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
                                                </tr-->
					<? if ($productGroupExist) {?>
						<!--tr>
                                                 <td class="fieldName">Group:</td>
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
                                                </tr-->
						<? }?>
						<!--tr>
                                                  <td class="fieldName" nowrap>Gms per Pouch:</td>
                                                  <td class="listing-item"><input type="text" name="gmsPerPouch" value="<?=$gmsPerPouch?>" size="5"></td>
                                                </tr-->
                                              </table>
					</td>
					<td></td>
					<td style="padding-left:5px; padding-right:5px;" valign="top">
					
					</td>
					</tr>
					<tr>
					  <td colspan="2" height="5"></td>
					</tr>
				<?if ($selProduct!="") {?>
					<tr>
					  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;">
					<table bgcolor="#999999" cellspacing="1" border="0">	
					<TR bgcolor="#f2f2f2">
						<TD class="listing-head"></TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Fixed</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Gravy</TD>
					</TR>					
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"  bgcolor="orange">
						<input type="text" name="productRatePerPouch" id="productRatePerPouch" style="text-align:right;border:none; background-color:orange;font-weight:bold" readonly value="<?=$productRatePerPouch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerPouch" id="fishRatePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRatePerPouch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerPouch" id="gravyRatePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerPouch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow" nowrap>Gms per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? //if ($p["productGmsPerPouch"]!="") $productGmsPerPouch=$p["productGmsPerPouch"];?>
						<input type="text" size="5" style="text-align:right;" name="productGmsPerPouch" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" onkeyup="productConversionIngProportion();calcProductConversionRatePerBatch();" autoComplete="off"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? 						
							//if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch=$p["fishGmsPerPouch"];		
						?>
						<input type="text" size="4" style="text-align:right;background-color:lightblue; border:none;" name="fishGmsPerPouch" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" autoComplete="off" readonly></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyGmsPerPouch" id="gravyGmsPerPouch" style="text-align:right;border:none" readonly value="<?=$gravyGmsPerPouch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow" >% per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productPercentagePerPouch" id="productPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="fishPercentagePerPouch" id="fishPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyPercentagePerPouch" id="gravyPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyPercentagePerPouch?>" size="5">%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerKgPerBatch" id="productRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerKgPerBatch" id="fishRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerKgPerBatch" id="gravyRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerKgPerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Pouches per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? //if ($p["pouchPerBatch"]!="") $pouchPerBatch=$p["pouchPerBatch"];?>
						<input type="text" size="5" style="text-align:right; border:none;background-color:lightblue;" name="pouchPerBatch" id="pouchPerBatch" value="<?=$pouchPerBatch?>" autoComplete="off" readonly>
						</TD>
						<TD class="listing-item" align="center" colspan="2"></TD>				
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerBatch" id="productRatePerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerBatch" id="fishRatePerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerBatch" id="gravyRatePerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgPerBatch" id="productKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishKgPerBatch" id="fishKgPerBatch" style="text-align:right;border:none" readonly value="<?=$fishKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><input type="text" name="gravyKgPerBatch" id="gravyKgPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyKgPerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="productRawPercentagePerPouch" id="productRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productRawPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="fishRawPercentagePerPouch" id="fishRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRawPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="gravyRawPercentagePerPouch" id="gravyRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRawPercentagePerPouch?>" size="5">%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Kg (in Pouch) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgInPouchPerBatch" id="productKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgInPouchPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishKgInPouchPerBatch" id="fishKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$fishKgInPouchPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyKgInPouchPerBatch" id="gravyKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyKgInPouchPerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% Yield</TD>
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishPercentageYield" id="fishPercentageYield" style="text-align:right;border:none" readonly value="<?=$fishPercentageYield?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyPercentageYield" id="gravyPercentageYield" style="text-align:right;border:none" readonly value="<?=$gravyPercentageYield?>" size="5">%</TD>
					</TR>
				</table></td>
					</tr>
					<tr>
					  <td colspan="2" height="5"></td>
					</tr>
					<tr>
					  <td colspan="4" nowrap style="padding-left:5px; padding-right:5px;">
					<table >
					<TR><TD>
					<div id="hideblock" style="display:block">
					  <table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
                                            <tr bgcolor="#f2f2f2" align="center">
                                                  <td class="listing-head" style="padding-left:5px; padding-right:5px;">Ingredient</td>
						  <td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Kg</td>
						  <td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Fixed Qty</td>		  
						  <td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Qty</td>		 	
						<td class="listing-head" style="padding-left:5px; padding-right:5px;">%/<br>Batch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rs/<br>Batch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Gms/<br>Pouch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">%Wt/<br>Pouch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rs/<br>Pouch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">%Cost/<br>Pouch</td>
                                                </tr>
		<?
		if ($selProduct) { 
			$productRecs = $productConversionObj->fetchAllIngredients($selProduct);
			$numRows =sizeof($productRecs);
		}		
		
		$lastPrice = 0;
		$m=0;
		foreach ($productRecs as $pr)	{
			$m++;
			$ingredientId = $pr[2];
			$ingredientRec	= $ingredientMasterObj->find($ingredientId);
			$ingredientName	= stripSlash($ingredientRec[2]);	
			$lastPrice = $productConversionObj->getIngredientRate($ingredientId);	
			$editQuantity = $pr[3];
			$fixedQtyChk = $pr[4];
			$checked = "";
			if ($fixedQtyChk=='Y') $checked= "Checked";
			$fixedQty	=	$pr[5];
			$percentagePerBatch = $pr[6];
			$ratePerBatch	= $pr[7];
			$ingGmsPerPouch	= $pr[8];
			$percentageWtPerPouch = $pr[9];
			$ratePerPouch	= $pr[10];
			$percentageCostPerPouch = $pr[11];		
			?>
                        <tr bgcolor="#FFFFFF" align="center">
                               <td style="padding-left:5px; padding-right:5px;" class="listing-item">
				<?=$ingredientName;?>
				<input type="hidden" name="selIngredient_<?=$m?>" id="selIngredient_<?=$m?>" value="<?=$ingredientId?>">
			        </td>
                                 <td style="padding-left:5px; padding-right:5px;">
					<input name="quantity_<?=$m?>" type="text" id="quantity_<?=$m?>" value="<?=$editQuantity;?>" size="4" style="text-align:right; border:none;" autoComplete="off" readonly>
					<input name="hidQuantity_<?=$m?>" type="hidden" id="hidQuantity_<?=$m?>" size="4" style="text-align:right;" value="<?=$editQuantity?>">
				</td>
				<td style="padding-left:5px; padding-right:5px;">
					<? if ($checked!="" ) {?> 
					<img src="images/y.gif">
					<? }?>
					<input name="fixedQtyChk_<?=$m?>" type="hidden" id="fixedQtyChk_<?=$m?>" value="<?=$fixedQtyChk?>" size="4">
				</td>
				<? if ($fixedQtyChk!="") {?>
				<td style="padding-left:5px; padding-right:5px;">
					<? if ($fixedQtyChk!="" ) {?> 
					<input name="fixedQty_<?=$m?>" type="text" id="fixedQty_<?=$m?>" value="<?=$fixedQty;?>" size="4" style="text-align:right; border:none;" readonly>
					<?}?>
				</td>
				<? }?>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
					<input type="text" name="percentagePerBatch_<?=$m?>" id="percentagePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
				</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
					<input type="hidden" name="lastPrice_<?=$m?>" id="lastPrice_<?=$m?>" value="<?=$lastPrice?>">
					<input type="text" name="ratePerBatch_<?=$m?>" id="ratePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$ratePerBatch?>" size="5">
				</td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<input type="text" name="ingGmsPerPouch_<?=$m?>" id="ingGmsPerPouch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$ingGmsPerPouch?>" size="5">
				</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
					<input type="text" name="percentageWtPerPouch_<?=$m?>" id="percentageWtPerPouch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$percentageWtPerPouch?>" size="5">%
				</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
					<input type="text" name="ratePerPouch_<?=$m?>" id="ratePerPouch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$ratePerPouch?>" size="5">
				</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
					<input type="text" name="percentageCostPerPouch_<?=$m?>" id="percentageCostPerPouch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$percentageCostPerPouch?>" size="5">%
				</td>	
                                 </tr>
				<?
					}
				?>
                                </table>
</div>
					<!---  table 2 end Here-->
						</TD>
						</TR>
						</table>
					<!-- 		End Here 1		 -->
						</td>
						   </tr>
				<? }?>	
				
					<input type='hidden' name='hidItemCount' id='hidItemCount' value="<?=$m;?>">
					<input type="hidden" name="newline" value="">
					<input type="hidden" name="new" value="<?=$m?>">	
					<!--tr>
					 <td colspan="2" nowrap class="fieldName" style="padding-left:5px; padding-right:5px;">
					  <? if($addMode==true){?>
						  <a href="javascript:productMasterNewLine()">Add Another Item</a><? } else {?><a href="javascript:productMasterNewLine()" onclick="document.frmProductConversion.editId.value=<?=$editId?>;">Add Another Item</a><? }?>
					 </td>
					 </tr-->
					<tr>
						<td colspan="2" nowrap>&nbsp;</td>
					</tr>
<!--  Chart Listing Starts here -->
					<tr>
						<td colspan="2" nowrap>
						<table>							
				<TR><TD>				
				</TD></TR>
						</table>
						</td>
					</tr>
<!--  Chart Listing End here -->
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
				<td colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductConversion.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductConversionMaster(document.frmProductConversion);">	
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductConversion.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductConversionMaster(document.frmProductConversion);">&nbsp;&nbsp;			</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
				<!--input type="hidden" name="stockType" value="<?=$stockType?>"-->
				</tr>
		<tr>
			<td colspan="2"  height="10" >
				<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyChk?>">
			</td>
		</tr>
		</table></td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
	<!-- Form fields end   --></td>
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
									<td background="images/heading_bg.gif" class="pageName">&nbsp;Product Conversion</td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
						<tr>
								  <td colspan="3" align="right" style="padding-right:10px;">
</td> </tr>
			<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productConversionRecordsize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductConversion.php',700,600);"><?}?></td>
											</tr>
										</table>	</td>
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
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if ( sizeof($productConversionRecords) > 0) {
		$i	=	0;
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
      				$nav.= " <a href=\"ProductConversion.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductConversion.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductConversion.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Code</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($productConversionRecords as $pmr) {
		$i++;
		$productId	=	$pmr[0];
		$productCode	=	$pmr[1];
		$productName	=	$pmr[2];
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ProductConversion.php';">
		</td>
		<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
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
      				$nav.= " <a href=\"ProductConversion.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductConversion.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductConversion.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	</table>
<!--  Hiddwen fields-->
<input type="hidden" name="totalFixedFishQty" id="totalFixedFishQty" value="<?=$totalFixedFishQty?>">
<input type="hidden" name="hidProductGmsPerPouch" id="hidProductGmsPerPouch" value="<?=$productGmsPerPouch?>">
<input type="hidden" name="productCategory" id="productCategory" value="<?=$productCategory?>">
<input type="hidden" name="productState" id="productState" value="<?=$productState?>">
<input type="hidden" name="productGroup" id="productGroup" value="<?=$productGroup?>">	
<!-- in Js using -->
<input type="hidden" name="hidSelProductId" id="hidSelProductId" value="<?=$selProduct?>">	

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
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productConversionRecordsize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductConversion.php',700,600);"><?}?></td>
											</tr>
										</table>			</td>
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
		<!--tr>
			<td height="10" align="center"><a href="ProductCategory.php" class="link1" title="Click to Manage Product Category">Product Category</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ProductState.php" class="link1">Product State</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="ProductGroup.php" class="link1">Product Group</a></td>	
		</tr-->
	</table> 
	
	<? if ($addMode ) { //|| $editMode?>
		<script>
		// Calc Chart value
		
		</script>
	<? }?>

	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>