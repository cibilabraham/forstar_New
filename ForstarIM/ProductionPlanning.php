<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$userId		=	$sessObj->getValue("userId");	
	$dateSelection = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

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

	#Re-Setting the Values
	if ($p["pDate"]!="") $pDate = $p["pDate"];
	if ($p["selProduct"]!=$p["hidSelProduct"]) $p["productGmsPerPouch"]="";
	if ($p["selProduct"]!="") $selProduct = $p["selProduct"];
	if ($p["productGmsPerPouch"]!="") $productGmsPerPouch = $p["productGmsPerPouch"];	
	if ($p["pouchPerBatch"]!="") $pouchPerBatch = $p["pouchPerBatch"];
	
	if ($p["productGmsPerPouch"]=="" && $addMode!="" && $selProduct!="") $productGmsPerPouch = $productionPlanningObj->getProductNetWt($selProduct);

	if ($selProduct!="") {
		list($productCode, $productName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty) = $productMasterObj->getProductRec($selProduct);
	}

	#Add
	if ($p["cmdAdd"]!="" ) {	
		$itemCount = $p["hidItemCount"];
		$plannedDate	= mysqlDateFormat($p["pDate"]); 	// Planning Date
		$selProduct	= $p["selProduct"];

		$productGmsPerPouch 	= $p["productGmsPerPouch"];		
	 	$pouchPerBatch 		= $p["pouchPerBatch"];

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
		
		# Checking the Production Plan Entry Exist
		$entryExist = $productionPlanningObj->productionPlanEntryExist($selProduct,$plannedDate,$currentId);

		if ($pDate!="" && $selProduct!="" && !$entryExist) {
			$productionPlanRecIns = $productionPlanningObj->addProductionPlan($plannedDate, $selProduct, $productGmsPerPouch, $pouchPerBatch, $userId, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty);

			$lastId = $databaseConnect->getLastInsertedId(); // Find the Inserted Id

			$fQty = 0;
			for ($i=1; $i<=$itemCount; $i++) {
				$ingredientId	= $p["ingredientId_".$i];
				$quantity	= trim($p["quantity_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];
				$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];

				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];
				$cleanedQty		= $p["cleanedQty_".$i];
				$currentStock		= $p["existingQty_".$i];
				$ingType		= $p["ingType_".$i];	

				if ($fixedQtyChk=='Y') {
					$fQty += $fixedQty; //Find the sum of fixed qty
				}

				if ($lastId!="" && $ingredientId!="" && $quantity!="") {
					$ingredientRecIns = $productionPlanningObj->addIngredientRec($lastId, $ingredientId, $quantity, $fixedQtyChk, $currentStock, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType);
				}
			}
			#Update Fixed Qty
			if ($fQty) {
				$updateFixedQty = $productionPlanningObj->updateBatchFixedQty($lastId, $fQty);
			}
		}

		if ($productionPlanRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddProductionPlan);
			$sessObj->createSession("nextPage",$url_afterAddProductionPlan.$dateSelection);
		} else {
			$addMode	=	true;
			//$err		=	$msg_failAddProductionPlan;
			if ($entryExist) $err	= $msg_failAddProductionPlanEntryExist;
			else $err	= $msg_failAddProductionPlan; 
		}
		$productionPlanRecIns		=	false;
	}
	
	
	# Edit a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$productBatchRec	= $productionPlanningObj->find($editId);
		
		$editProductBatchId	= $productBatchRec[0];
		$pDate 			= dateFormat($productBatchRec[1]);

		if ($p["editSelectionChange"]=='1' || $p["selProduct"]=="") {
			$selProduct 	=  $productBatchRec[2];
		} else {
			$selProduct 	=  $p["selProduct"];
		}

		$productGmsPerPouch 	= $productBatchRec[5];	 	
		$pouchPerBatch 		= $productBatchRec[6];

		////////
		$productRatePerPouch 	= $productBatchRec[7];
		$fishRatePerPouch	= $productBatchRec[8];
		$gravyRatePerPouch	= $productBatchRec[9];
		$productGmsPerPouch	= $productBatchRec[10];
		$fishGmsPerPouch	= $productBatchRec[11];
		$gravyGmsPerPouch	= $productBatchRec[12];
		$productPercentagePerPouch = $productBatchRec[13];
		$fishPercentagePerPouch	= $productBatchRec[14];
		$gravyPercentagePerPouch = $productBatchRec[15];
		$productRatePerKgPerBatch = $productBatchRec[16];
		$fishRatePerKgPerBatch 	= $productBatchRec[17];
		$gravyRatePerKgPerBatch = $productBatchRec[18];
		$pouchPerBatch		= $productBatchRec[19];
		$productRatePerBatch	= $productBatchRec[20];
		$fishRatePerBatch	= $productBatchRec[21];
		$gravyRatePerBatch	= $productBatchRec[22];
		$productKgPerBatch	= $productBatchRec[23];
		$fishKgPerBatch		= $productBatchRec[24];
		$gravyKgPerBatch	= $productBatchRec[25];
		$productRawPercentagePerPouch = $productBatchRec[26];
		$fishRawPercentagePerPouch = $productBatchRec[27];
		$gravyRawPercentagePerPouch = $productBatchRec[28];
		$productKgInPouchPerBatch = $productBatchRec[29];
		$fishKgInPouchPerBatch	= $productBatchRec[30];
		$gravyKgInPouchPerBatch	= $productBatchRec[31];
		$fishPercentageYield	= $productBatchRec[32];
		$gravyPercentageYield 	= $productBatchRec[33];
		$totalFixedFishQty	= $productBatchRec[34];
		/*****/
		# Production Planned Recs
		$productBatchRecs = $productionPlanningObj->fetchAllStockItem($editProductBatchId);
		$optionDisabled	= "disabled";		
	}


	#Update A Record
	if ($p["cmdSaveChange"]!="") {	
	
		$productionPlanId	= $p["hidProductBatchId"];
		$itemCount		= $p["hidItemCount"];

		$plannedDate	= mysqlDateFormat($p["pDate"]); 	// Planning Date		
		$selProduct 	= $p["hidSelProduct"];

		$productGmsPerPouch 	= $p["productGmsPerPouch"];		
	 	$pouchPerBatch 		= $p["pouchPerBatch"];

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

		# Checking the Production Plan Entry Exist
		$entryExist = $productionPlanningObj->productionPlanEntryExist($selProduct, $plannedDate, $productionPlanId);

		if ($productionPlanId!="" && $plannedDate!="" && $selProduct!="" && !$entryExist) {
			$productionPlanRecUptd = $productionPlanningObj->updateProductionPlan($productionPlanId, $plannedDate, $selProduct, $productGmsPerPouch, $pouchPerBatch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty);
		
			# Delete First all records from Entry table
			$deleteIngredientItem = $productionPlanningObj->deleteIngredientRecs($productionPlanId);

			$fQty = 0;
			for ($i=1; $i<=$itemCount; $i++) {
				$ingredientId	=	$p["ingredientId_".$i];
				$quantity	=	trim($p["quantity_".$i]);
				$quantityAlreadyIssued = trim($p["hidQuantity_".$i]); 
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];

				$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];

				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];
				$cleanedQty		= $p["cleanedQty_".$i];
				$currentStock		= $p["existingQty_".$i];
				$ingType		= $p["ingType_".$i];		

				if ($fixedQtyChk=='Y') {
					$fQty += $fixedQty; //Find the sum of fixed qty
				}

				if ($productionPlanId!="" && $ingredientId!="" && $quantity!="") {
					$ingredientRecIns = $productionPlanningObj->addIngredientRec($productionPlanId, $ingredientId, $quantity, $fixedQtyChk, $currentStock, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType);					
				}
			}
			#Update Fixed Qty
			if ($fQty) {
				$updateFixedQty = $productionPlanningObj->updateBatchFixedQty($productionPlanId, $fQty);
			}
		}
	
		if ($productionPlanRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductionPlanUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductionPlan.$dateSelection);
		} else {
			$editMode	=	true;
			//$err		=	$msg_failProductionPlanUpdate;
			if ($entryExist) $err	= $msg_failUpdateProductionPlanEntryExist;
			else $err	= $msg_failProductionPlanUpdate; 
		}
		$productionPlanRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$productionPlanId = $p["delId_".$i];
			
			if ($productionPlanId!="") {
				//Delete From Entry Table
				$deleteIngredientItem = $productionPlanningObj->deleteIngredientRecs($productionPlanId);
				//Delete From main Table
				$productionPlanRecDel   = $productionPlanningObj->deleteProductBatch($productionPlanId);
				
			}
		}
		if ($productionPlanRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductionPlan);
			$sessObj->createSession("nextPage",$url_afterDelProductionPlan.$dateSelection);
		} else {
			$errDel	=	$msg_failDelProductionPlan;
		}
		$productionPlanRecDel	=	false;
	}

	$selRateList = $ingredientRateListObj->latestRateList();
	#Fetch all Item
	if ($selProduct) $productRecs = $productionPlanningObj->fetchAllIngredients($selProduct, $selRateList);
	
	if ($addMode || $editMode) {
		#List all Product Records (Not Base Product)
		$productMasterRecords = $productionPlanningObj->fetchAllProductMatrixRecords(); 	
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
		
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------		

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	#List all Purchase Order
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		#List all Product Batch	
		$productionPlanningRecords = $productionPlanningObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$productionPlanningRecSize = sizeof($productionPlanningRecords);

		# Pagination
		$fetchAllPlannedProductionRecs = $productionPlanningObj->fetchDateRangeRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllPlannedProductionRecs);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($editMode)	$heading = $label_editProductionPlan;
	else 		$heading = $label_addProductionPlan;

	# On Load Print JS	
	$ON_LOAD_PRINT_JS	= "libjs/ProductionPlanning.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProductionPlanning" action="ProductionPlanning.php" method="post">
 <!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
			<table cellspacing="0"  align="center" cellpadding="0" width="100%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionPlanning.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionPlanning(document.frmProductionPlanning);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionPlanning.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionPlanning(document.frmProductionPlanning);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidProductBatchId" value="<?=$editProductBatchId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
	<tr>
		  <td colspan="2" nowrap>
					<table width="200">
						<tr>
                                                  <td class="fieldName" nowrap>*Date: </td>
                                                  <td class="listing-item">
							<input name="pDate" type="text" id="pDate" value="<?=$pDate?>" size="9" autoComplete="off">
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Product:</td>
                                                  <td class="listing-item">
						 <select name="selProduct" id="selProduct" onchange="<? if ($addMode) {?>this.form.submit();<? }  else {?>this.form.editId.value=<?=$editId?>; this.form.submit();<?}?>" <?=$optionDisabled?>>
						<option value="">-- Select --</option>
						<?
						foreach ($productMasterRecords as $pmr) {
							$productId	=	$pmr[0];
							$productCode	=	$pmr[1];
							$productName	=	$pmr[2];
							$selected = "";
							if ($selProduct==$productId) $selected = "Selected";
						?>
						<option value="<?=$productId?>" <?=$selected?>><?=$productName?></option>
						  <? }?>
                                                  </select></td>
                                                </tr>
                                                <!--tr>
                                                	<TD class="fieldName" nowrap>Gms per Pouch (Product):</TD>
                                                        <TD><input name="productGmsPerPouch" type="text" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" size="6" style="text-align:right;" onkeyup="calcIngProportion();"></TD>
						</tr-->
 						<!--tr>
                                                	<TD class="fieldName" nowrap>Gms per Pouch (Fish):</TD>
                                                        <TD><input name="fishGmsPerPouch" type="text" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" size="6" style="text-align:right;"></TD>
						</tr-->
						<!--tr>
                                                	<TD class="fieldName" nowrap>Pouches Per Batch:</TD>
                                                        <TD><input name="pouchPerBatch" type="text" id="pouchPerBatch" value="<?=$pouchPerBatch?>" size="6" style="text-align:right;"></TD>
						</tr-->						
                                                          </table></td>
				  </tr>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD>
						<table>
							<TR><TD>
							<table>
							<!--<TR><TD height="5"></TD></TR>-->
							<tr><TD>	
							<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2" align="center">
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
						<input type="text" size="6" style="text-align:right; border:none;background-color:lightblue;" name="productGmsPerPouch" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" onkeyup="getProductionPlanRatePerBatch();" autoComplete="off" readonly></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? //if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch=$p["fishGmsPerPouch"];?>
						<input type="text" size="4" style="text-align:right;background-color:lightblue; border:none;" name="fishGmsPerPouch" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" onkeyup="getProductionPlanRatePerBatch();" autoComplete="off" readonly></TD>
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
						<input type="text" size="6" style="text-align:right;" name="pouchPerBatch" id="pouchPerBatch" value="<?=$pouchPerBatch?>" onkeyup="ingQtyProportion();" onchange="ingQtyProportion();" autoComplete="off">
						<input type="hidden" size="6" style="text-align:right;" name="hidPouchPerBatch" id="hidPouchPerBatch" value="<?=$pouchPerBatch?>" readonly="true">
						</TD>
						<TD class="listing-item" align="center" colspan="2"></TD>
						<!--TD class="listing-item" align="center"><input type="submit" name="cmdReview" class="button" value="Review"-->
						</TD-->
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
				</table>
				</TD></tr></table>
<!-- First Column Ends Here -->
							</TD>
						<td></td>
						<td valign="top">
						
						</td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr>
				  <td colspan="2" nowrap style="padding-left:2px; padding-right:2px;">
			   <?
			  if (sizeof($productRecs) > 0) {
				$j=0;
			  ?>
			<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
			     <tr bgcolor="#f2f2f2" align="center">
                                   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Ingredient</td>
                  		   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Raw <br>Qty</td>
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Cleaned <br>Qty</td>	
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Fixed<br> Qty</td>
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Existing <br>Qty</td>				   
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Qty</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Gms/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Wt/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Cost/<br>Pouch</td>				
                              </tr>
				<?
			 	foreach ($productRecs as $pr)	{
					$j++;
					$ingredientId	= $pr[2];					
					//$ingredientName	= $pr[12];
					// Find Ingredient Rate
					//$lastPrice  	= $pr[13];
					//$declYield	= $pr[15];
					$selIngType = $pr[13];
					$ingredientName = "";
					$lastPrice 	= 0;
					$existingQty	= 0;
					if ($selIngType=='ING') {	# If ING
						$ingredientName	= $ingredientMasterObj->getIngName($ingredientId);
						list($lastPrice,$declYield) = $productMasterObj->getIngredientRate($ingredientId, $selRateList);
						// Find the Existing Stk
						$existingQty = $productBatchObj->getTotalStockQty($ingredientId);
					} else if ($selIngType=='SFP') { # If Semi Finished
						//$ingredientName = $productMasterObj->getProductName($ingredientId);	
						$ingredientName = $semiFinishProductObj->getSemiFinishProductName($ingredientId);	
						list($lastPrice,$declYield) = $productMasterObj->getSemiFinishRate($ingredientId);
						# Find Semi Finished Product Exist
						$existingQty = $productBatchObj->getSemiFinishProductStkQty($ingredientId);
					} else {
						$lastPrice	= 0;
						$declYield	= 0;
					}					

					// Edit Mode
					if ($editProductBatchId) $rec = $productBatchRecs[$j-1];
								
					if ($p["editSelectionChange"]=='1') {
						$quantity	=	$rec[3];
					} else {
						$quantity	=	$pr[3];
					}

					if ($p["editSelectionChange"]=='1') {
						$fixedQtyChk	=	$rec[4];
					} else {
						$fixedQtyChk	=	$pr[4];
					}

					$checked = "";
					if ($fixedQtyChk=='Y') $checked= "Checked";

					if ($p["editSelectionChange"]=='1') {
						$fixedQty	=	$rec[5];
					} else {
						$fixedQty	=	$pr[5];
					}

					// Refer values
					if ($p["editSelectionChange"]=='1') {
						$percentagePerBatch = $rec[6];
					} else {
						$percentagePerBatch = $pr[6];
					}

					if ($p["editSelectionChange"]=='1') {
						$ratePerBatch	=	$rec[7];
					} else {
						$ratePerBatch	=	$pr[7];
					}

					if ($p["editSelectionChange"]=='1') {
						$ingGmsPerPouch	=	$rec[8];
					} else {
						$ingGmsPerPouch	=	$pr[8];
					}

					if ($p["editSelectionChange"]=='1') {
						$percentageWtPerPouch	=	$rec[9];
					} else {
						$percentageWtPerPouch	=	$pr[9];
					}

					if ($p["editSelectionChange"]=='1') {
						$ratePerPouch	=	$rec[10];
					} else {
						$ratePerPouch	=	$pr[10];
					}

					if ($p["editSelectionChange"]=='1') {
						$percentageCostPerPouch	= $rec[11];
					} else {
						$percentageCostPerPouch	= $pr[11];
					}

					if ($p["editSelectionChange"]=='1') {
						$cleanedQty	= $rec[12];
					} else {
						$cleanedQty	= $pr[12];
					}

					//Find the Existing
					//$existingQty = $productionPlanningObj->getTotalStockQty($ingredientId);

					$checked = "";
					if ($fixedQtyChk=='Y') $checked= "Checked";
					
				?>
                                <tr bgcolor="#FFFFFF" align="center">
                                	<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="left"><?=$ingredientName?>
						<input type="hidden" value="<?=$ingredientId?>" name="ingredientId_<?=$j?>" id="ingredientId_<?=$j?>">
						<input type="hidden" name="ingType_<?=$j?>" id="ingType_<?=$j?>" value="<?=$selIngType?>">
					</td>
                                        <td style="padding-left:2px; padding-right:2px;">
						<input name="quantity_<?=$j?>" type="text" id="quantity_<?=$j?>" size="4" style="text-align:right;border:none;" value="<?=$quantity?>" readonly>
						<input name="hidQuantity_<?=$j?>" type="hidden" id="hidQuantity_<?=$j?>" size="4" style="text-align:right;" value="<?=$quantity?>">
						<input name="declYield_<?=$j?>" type="hidden" id="declYield_<?=$j?>" value="<?=$declYield;?>" size='4' style="text-align:right;border:none;" autoComplete="off" readonly>
					</td>
					<td style="padding-left:2px; padding-right:2px;">
						<input name="cleanedQty_<?=$j?>" type="text" id="cleanedQty_<?=$j?>" value="<?=$cleanedQty;?>" size="4" style="text-align:right;border:none;" autoComplete="off" readonly="true">
						<input name="hidCleanedQty_<?=$j?>_<?=$i?>" type="hidden" id="hidCleanedQty_<?=$m?>_<?=$i?>" size="4" style="text-align:right;" value="<?=$cleanedQty?>">
					</td>
					<td style="padding-left:2px; padding-right:2px;">
						<? if ($checked!="" ) {?> 
							<img src="images/y.gif">
						<? }?>
						<input name="fixedQtyChk_<?=$j?>" type="hidden" id="fixedQtyChk_<?=$j?>" value="<?=$fixedQtyChk?>" size="4" style="text-align:right">
						<!--<input name="fixedQtyChk_<?=$j?>" type="checkbox" id="fixedQtyChk_<?=$j?>" value="Y" size="4" style="text-align:right" class="chkBox" <?=$checked?>>-->
					</td>
					<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;">
						<input name="existingQty_<?=$j?>" type="text" id="existingQty_<?=$j?>" size="4" style="text-align:right; border:none;" value="<?=$existingQty?>">
					</td>
					<? if ($fixedQtyChk!="" || $editMode) {?>
				<td style="padding-left:2px; padding-right:2px;">
					<? if ($fixedQtyChk=='Y') {?> 
						<input name="fixedQty_<?=$j?>" type="text" id="fixedQty_<?=$j?>" value="<?=$fixedQty;?>" size="4" style="text-align:right;border:none;" onkeyup="getProductionPlanRatePerBatch();" readonly="true">
					<?}?>
				</td>
				<? }?>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentagePerBatch_<?=$j?>" id="percentagePerBatch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
				</td>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<input type="hidden" name="lastPrice_<?=$j?>" id="lastPrice_<?=$j?>" value="<?=$lastPrice?>">
					<input type="text" name="ratePerBatch_<?=$j?>" id="ratePerBatch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$ratePerBatch?>" size="5">
				</td>
				<td class="listing-item" nowrap style="padding-left:2px; padding-right:4px;" align="right">
					<input type="text" name="ingGmsPerPouch_<?=$j?>" id="ingGmsPerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$ingGmsPerPouch?>" size="5">
				</td>
                                <td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentageWtPerPouch_<?=$j?>" id="percentageWtPerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$percentageWtPerPouch?>" size="5">%
				</td>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<input type="text" name="ratePerPouch_<?=$j?>" id="ratePerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$ratePerPouch?>" size="5">
				</td>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentageCostPerPouch_<?=$j?>" id="percentageCostPerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$percentageCostPerPouch?>" size="5">%
				</td>					
                                 </tr>
				<?
					 }
				?>
                                </table>
			  <? }?>
			  </td>
			    </tr>
				<input type="hidden" name="hidItemCount" id="hidItemCount" value="<?=$j;?>">
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionPlanning.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionPlanning(document.frmProductionPlanning);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionPlanning.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionPlanning(document.frmProductionPlanning);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
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
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Production Planning   </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From:</td>
                                    		<td nowrap="nowrap"> 
                            		<? 
					if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionPlanningRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionPlanning.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
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
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($productionPlanningRecords)>0) {
		$i	=	0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="5" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductionPlanning.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionPlanning.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionPlanning.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div></td>
       </tr>
	   <? }?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Pouches per Batch</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
		<?
		foreach ($productionPlanningRecords as $ppr) {
			$i++;
			$productionPlanId	= $ppr[0];
			$plannedDate		= dateFormat($ppr[1]);
			$productName		= $ppr[5];
			$numPouch		= $ppr[6];			
		?>
		<tr  bgcolor="WHITE">
			<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productionPlanId;?>" class="chkBox"></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$plannedDate;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
				<?=$numPouch?>
			</td>
			<? if($edit==true){?>
			<td class="listing-item" width="60" align="center">			
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productionPlanId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ProductionPlanning.php';"></td>
			<? }?>
		</tr>
		<?
			}
		?>
			<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
			<input type="hidden" name="editId" value="<?=$editId?>">
			<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
         	<td colspan="5" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductionPlanning.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionPlanning.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionPlanning.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div><input type="hidden" name="pageNo" value="<?=$pageNo?>"></td>
       	 	        </tr>
			<? }?>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" >
	<input type="hidden" name="hidSelProduct" value="<?=$selProduct?>">	
	<input type="hidden" name="hidProductGmsPerPouch" id="hidProductGmsPerPouch" value="<?=$productGmsPerPouch?>">
	<input type="hidden" name="totalFixedFishQty" id="totalFixedFishQty" value="<?=$totalFixedFishQty?>">	
	</td>
	</tr>
	<tr>	
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
			<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionPlanningRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionPlanning.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
		</td>
		</tr>
		</table></td></tr>
		<tr>
			<td colspan="3" height="5" ></td>
		</tr>
		</table></td>
					</tr>
				</table>
				<!-- Form fields end   -->
		</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>

	</td>
</tr>	
	</table>
	<!-- end code -->

	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "pDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "pDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<? if ($addMode || $editMode) {?>
	<script>
	// calc rate
	//getProductionPlanRatePerBatch();
		
	</script>	
	<? }?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
