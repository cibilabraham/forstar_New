<?php
	require("include/include.php");
	require_once('lib/ProductMatrix_ajax.php');
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$userId		=	$sessObj->getValue("userId");

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
	
	#If Copied From Already Existing Products
	if($p['selProduct']!="")
	{
		$selProductId = $p['selProduct'];
	}
	
	#Add 
	if ($p["cmdAdd"]!="") 
	{
		//Common Fields
		$customerName			= $p['customerName'];	
		$productName   			= $p['productName'];	
		$forExport				= $p['forExport'];	
		$productStyle			= $p['productStyle'];

		//Combo
		$comboPdtNetwt			= $p['comboPdtNetwt'];

		//Single
		$productNetwt			= $p['productNetwt'];
		$fixedWgt				= $p['fixedWgt'];
		$gravyWgt				= $p['gravyWgt'];
		$noOfBatches			= $p['noOfBatches'];
		$batchSize				= $p['batchSize'];
		$recipeName				= $p['recipeName'];
		$productionName			= $p['productionName'];
		$packingName			= $p['packingName'];
		$rdCostIncluded			= $p['rdCostIncluded'];
		$sharedPrmryPckng		= $p['sharedPrmryPckng'];	
		$shareVal				= $p['shareVal'];
		$sharePack     			= $p['sharePack'];                                 //("Inner"/"Outer")
		$customerCreditPeriod	= $p['customerCreditPeriod'];
		$payment				= $p['payment'];
		$mainIngredntCost		= $p['mainIngredntCost'];
		$gravyCost				= $p['gravyCost'];

		//Common Fields
		$rawMaterialCost		= $p['rawMaterialCost'];
		$processCost			= $p['processCost'];
		$testingCost			= $p['testingCost'];
		$innerPackingCost		= $p['innerPackingCost'];
		$outerPackingCost		= $p['outerPackingCost'];
		$basicManuftrCost		= $p['basicManuftrCost'];
		$marktngCost			= $p['marktngCost'];
		$advtsCost				= $p['advtsCost'];
		$holdingCost			= $p['holdingCost'];
		$adminOverhead			= $p['adminOverhead'];
		$totalCost				= $p['totalCost'];
		$profitMargin			= $p['profitMargin'];
		$actualFactCost			= $p['actualFactCost'];
		$contingency			= $p['contingency'];
		$idealFactCost			= $p['idealFactCost'];
		$pmPercent				= $p['pmPercent'];

		//Combo
		$idealSellingPrice		= $p['idealSellingPrice'];
		$pkgPerMC				= $p['pkgPerMC'];
		$costOfMC				= $p['costOfMC'];

		if ($productName!="" && $productStyle!="") 
		{
			if($productStyle == 1)
			{
				$productMatrixRecIns = $productMatrixObj->addProductMatrix($productCode, $productName, $netWt, $fishWt, $gravyWt, $percentSeafood, $forExport, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch);
			}
			else
			{
				$productMatrixRecIns = $productMatrixObj->addProductMatrix($productCode, $productName, $netWt, $fishWt, $gravyWt, $percentSeafood, $forExport, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch);
			}

			if ($productMatrixRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddProductMatrix);
				$sessObj->createSession("nextPage",$url_afterAddProductMatrix.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddProductMatrix;
			}
			$productMatrixRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$productMatrixRecId	= $p["hidProductMatrixId"];

		$productCode	= addSlash(trim($p["productCode"]));
		$productName	= addSlash(trim($p["productName"]));
		$netWt		= $p["netWt"];
		$fishWt		= $p["fishWt"];
		$gravyWt	= $p["gravyWt"];
		$percentSeafood	= $p["percentSeafood"];
		$forExport	= $p["forExport"];
		$rMCodeId	= $p["rMCodeId"];
		$noOfBatches	= $p["noOfBatches"];
		$batchSize	= $p["batchSize"];
		$selFish	= $p["selFish"];
		$productionCode	= $p["productionCode"];
		$packingCode	= $p["packingCode"];
		$freightChargePerPack	= $p["freightChargePerPack"];
		$productExciseRate	= $p["productExciseRate"];
		$pmInPercentOfFc	= $p["pmInPercentOfFc"];
		$idealFactoryCost	= $p["idealFactoryCost"];
		$contingency		= $p["contingency"];
		$actualFactCost		= $p["actualFactCost"];
		$productProfitMargin	= $p["productProfitMargin"];
		$totalCost		= $p["totalCost"];
		$adminOverhead		= $p["adminOverhead"];
		$proHoldingCost		= $p["proHoldingCost"];
		$proAdvertCost		= $p["proAdvertCost"];
		$mktgCost		= $p["mktgCost"];
		$basicManufactCost	= $p["basicManufactCost"];
		$productOuterPkgCost	= $p["productOuterPkgCost"];
		$productInnerPkgCost	= $p["productInnerPkgCost"];
		$testingCost		= $p["testingCost"];
		$processingCost		= $p["processingCost"];
		$rMCost			= $p["rMCost"];
		$seaFoodCost		= $p["seaFoodCost"];
		$gravyCost		= $p["gravyCost"];
		$waterCostPerPouch	= $p["waterCostPerPouch"];
		$dieselCostPerPouch	= $p["dieselCostPerPouch"];
		$electricCostPerPouch	= $p["electricCostPerPouch"];
		$gasCostPerPouch	= $p["gasCostPerPouch"];
		$consumableCostPerPouch	= $p["consumableCostPerPouch"];
		$manPowerCostPerPouch	= $p["manPowerCostPerPouch"];
		$fishPrepCostPerPouch	= $p["fishPrepCostPerPouch"];		
		
		
		if ($productMatrixRecId!="" && $productCode!="" && $productName!="") {
			$productMatrixRecUptd = $productMatrixObj->updateProductMatrix($productMatrixRecId, $productCode, $productName, $netWt, $fishWt, $gravyWt, $percentSeafood, $forExport, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch);
		}
	
		if ($productMatrixRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductMatrixUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductMatrix.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductMatrixUpdate;
		}
		$productMatrixRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="" || $selProductId!="") 
	{
	    if($selProductId!="")
		{
			$editId		=	$selProductId;
		}
		else
		{
			$editId		=	$p["editId"];
		}
		$editMode	=	true;
		$productMatrixRec =	$productMatrixObj->find($editId);
		$editProductMatrixId =	$productMatrixRec[0];

		$productCode	= stripSlash($productMatrixRec[1]);
		$productName	= stripSlash($productMatrixRec[2]);
		$netWt		= $productMatrixRec[3];
		$fishWt		= $productMatrixRec[4];
		$gravyWt	= $productMatrixRec[5];
		$percentSeafood	= $productMatrixRec[6];
		$forExport	= $productMatrixRec[7];
		//$rMCodeId	= $productMatrixRec[8];
		$noOfBatches	= $productMatrixRec[9];
		$batchSize	= $productMatrixRec[10];
		//$selFishId	= $productMatrixRec[11];
		//$selProductionCodeId	= $productMatrixRec[12];
		//$selPkgCodeId	= $productMatrixRec[13];
		$freightChargePerPack	= $productMatrixRec[14];
		$productExciseRate	= $productMatrixRec[15];
		$pmInPercentOfFc	= $productMatrixRec[16];
		$idealFactoryCost	= $productMatrixRec[17];
		$contingency		= $productMatrixRec[18];
		$actualFactCost		= $productMatrixRec[19];
		$productProfitMargin	= $productMatrixRec[20];
		$totalCost		= $productMatrixRec[21];
		$adminOverhead		= $productMatrixRec[22];
		$proHoldingCost		= $productMatrixRec[23];
		$proAdvertCost		= $productMatrixRec[24];
		$mktgCost		= $productMatrixRec[25];
		$basicManufactCost	= $productMatrixRec[26];
		$productOuterPkgCost	= $productMatrixRec[27];
		$productInnerPkgCost	= $productMatrixRec[28];
		$testingCost		= $productMatrixRec[29];
		$processingCost		= $productMatrixRec[30];
		$rMCost			= $productMatrixRec[31];
		$seaFoodCost		= $productMatrixRec[32];
		$gravyCost		= $productMatrixRec[33];
		$waterCostPerPouch	= $productMatrixRec[34];
		$dieselCostPerPouch	= $productMatrixRec[35];
		$electricCostPerPouch	= $productMatrixRec[36];
		$gasCostPerPouch	= $productMatrixRec[37];
		$consumableCostPerPouch	= $productMatrixRec[38];
		$manPowerCostPerPouch	= $productMatrixRec[39];
		$fishPrepCostPerPouch	= $productMatrixRec[40];	
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productMatrixRecId	=	$p["delId_".$i];

			if ($productMatrixRecId!="") {
				// Need to check the selected Category is link with any other process
				$productMatrixRecDel = $productMatrixObj->deleteProductMatrixRec($productMatrixRecId);
			}
		}
		if ($productMatrixRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductMatrix);
			$sessObj->createSession("nextPage",$url_afterDelProductMatrix.$selection);
		} else {
			$errDel	=	$msg_failDelProductMatrix;
		}
		$productMatrixRecDel	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") {
		$pageNo=$p["pageNo"];
	} else if ($g["pageNo"] != "") {
		$pageNo=$g["pageNo"];
	} else {
		$pageNo=1;
	}
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Product matrix
	$productMatrixResultSetObj = $productMatrixObj->fetchAllPagingRecords($offset, $limit);
	$productMatrixRecordSize   = $productMatrixResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allProductMatrixResultSetObj = $productMatrixObj->fetchAllRecords();
	$numrows	=  $allProductMatrixResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	#Get all Packing Cost master Value
	if ($addMode || $editMode) {
		list($vatRateForPackingMaterial, $innerCartonWstage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc) = $packingCostMasterObj->getPackingCostMasterValue();
	}
	

	#List all Production Records
	$productionMatrixResultSetObj = $productionMatrixObj->fetchAllRecords();
	
	#List all Packing Recs
	$pkgMatrixResultSetObj = $packingMatrixObj->fetchAllRecords();

	#List all Product Records
	$productMasterRecords = $productMasterObj->fetchAllRecords();

	#List all Fish Cutting Records
	$fishCuttingResultSetObj = $productionFishCuttingObj->fetchAllRecords();

	#List all Recipes
	$recipeRecords = $recipeMasterObj->fetchAllRecipe();

	#Find the Production Code Values

	//echo "H=".$p["editSelectionChange"];

	if ($p["editSelectionChange"]=='1'|| $p["productionCode"]=="") { 
		$selProductionCodeId = $productMatrixRec[12];
	} else {
		$selProductionCodeId = $p["productionCode"];		
	}

	if ($selProductionCodeId!="") {
		//$selProductionCodeId = $p["productionCode"];
		list($prodCode, $prodName, $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $boilerRequired, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch) = $productionMatrixObj->getProductionMatrixRec($selProductionCodeId);	

		#Producion matrix Master
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue();
	}

	# Find the packing Code values
	if ($p["editSelectionChange"]=='1'|| $p["packingCode"]=="") { 
		$selPkgCodeId	= $productMatrixRec[13];
	} else {
		$selPkgCodeId = $p["packingCode"];		
	}
	if ($selPkgCodeId!="") {
		//$selPkgCodeId = $p["packingCode"];
		list($packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPkgCost) = $packingMatrixObj->getPackingMatrixRec($selPkgCodeId);
	}

	// Fish Cutting Cost
	if ($p["editSelectionChange"]=='1'|| $p["selFish"]=="") { 
		$selFishId	= $productMatrixRec[11];
	} else {
		$selFishId = $p["selFish"];		
	}

	if ($selFishId!="") {
		//$selFishId = $p["selFish"];
		$selFishCost = $productionFishCuttingObj->getFishCuttingCost($selFishId);
	}

	// Find RM Fish Rate and Gravy Rate
	if ($p["editSelectionChange"]=='1'|| $p["rMCodeId"]=="") { 
		$rMCodeId = $productMatrixRec[8];
	} else {
		$rMCodeId = $p["rMCodeId"];		
	}	
	if ($rMCodeId!="") {
		//$rMCodeId = $p["rMCodeId"];  // Product Id
		list($productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch) =$productMasterObj->getProductMasterRec($rMCodeId);		
	}
	
	if($addMode || $editMode)
	{
		$selProductRecs = $productMatrixObj->getActiveProducts();
	}
	
	$customerRecs = $distributorMasterObj->getCustomerRecord();
	$exportRecs = $exportMasterObj->getAllActiveExports();
	$paymentRecs = $paymentMasterObj->getAllActivePayments();
	
	#heading Section
	if ($editMode) $heading	= $label_editProductMatrix;
	else	       $heading	= $label_addProductMatrix;
	
	# Include XAJAX
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS	= "libjs/ProductMatrix.js";

	# Include Template [topLeftNav.php]
	/* require("template/topLeftNav.php"); */
	
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmProductMatrix" action="ProductMatrix.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<TD height="10"></TD>
		</tr>
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
							$bxHeader="Product Matrix";
							include "template/boxTL.php";
						?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="50%">
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
																					<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductMatrix.php');" />&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductMatrix(document.frmProductMatrix);" /></td>
																					
																					<?} else{?>

																					
																					<td  colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMatrix.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductMatrix(document.frmProductMatrix);">												</td>

																					<?}?>
																				</tr>
																				
																				<tr><TD height="10"></TD></tr>
																				<tr>
																					<TD colspan="2" nowrap align="center">
																						<table>
																							<tr>
																								<td>
																									<table align="center" style="border:1px solid #999999; width:100%; padding:5px;">
																										<?php
																										if ($addMode) 
																										{ 
																										?>
																										<tr>
																											<td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
																												<fieldset>
																												<legend class="listing-item" onMouseover="ShowTip('Copy from existing Product and save after editing.');" onMouseout="UnTip();">Copy From</legend>
																													<table>
																														<TR>
																															<TD class="fieldName" onMouseover="ShowTip('Copy from existing Product and save after editing.');" onMouseout="UnTip();">Product</TD>
																															<td>
																																<select name="selProduct" id="selProduct" onchange="this.form.submit();">
																																	<option value="">--Select--</option>
																																	<?php
																																		foreach ($selProductRecs as $spr) {
																																			$sProductId 	= $spr[0];
																																			$sProductName	= stripSlash($spr[2]);
																																			$selected = "";
																																			if ($selProductId==$sProductId) $selected = "selected";
																																	?>
																																	<option value="<?=$sProductId?>" <?=$selected?>><?=$sProductName?></option>
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
																											<td class="fieldName" nowrap >*Customer Name</td>
																											<td align="left">
																												<select name="customerName" id="customerName" onchange="xajax_getPaymentTerms(this.value)">
																												<option value="">-- Select Customer --</option>
																												<?php 
																												   foreach ($customerRecs as $custmer)
																												   {
																														$custId = $custmer[0];
																														$custName = $custmer[1];
																														$selected = "";
																														
																														?>
																														<option value="<?=$custId?>" <?=$selected?>><?=$custName?></option>
																												   <?} ?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Product Name</td>
																											<td align="left">
																												<input type="text" name="productName" id="productName" size="20"/>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*For Export</td>
																											<td align="left">
																												<select id="forExport" name="forExport" onchange="xajax_getExportName(this.value);">
																													<option value="">-- Select --</option>
																													<? 
																													foreach ($exportRecs as $exp)
																													{
																														$exportId = $exp[0];
																														$exportName = $exp[1];
																														$selected = "";
																														?>
																													<option value="<?=$exportId?>" <?=$selected?>><?=$exportName?></option>
																													<?
																													}
																													?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Product Style</td>
																											<td align="left">
																												<select id="productStyle" name="productStyle" onchange="displaySingleCombo();">
																													<option value="">-- Select --</option>
																													<option value="1">Single</option>
																													<option value="2">Combo</option>
																												</select>
																											</td>
																										</tr>
																									</table>
																									<table id="comboNetWgt" align="center" style="border:1px solid #999999; width:100%; padding:5px; margin-top:10px; display:none;">
																										<tr>
																											<td class="fieldName" nowrap >Product</td>
																											<td> 
																												<table>
																													<!--  Dynamic Row Starts Here-->
																													<tr id="catRow1">
																														<td colspan="2" style="padding-left:5px;padding-right:5px;">
																															<table  id="tblProduct">
																																<tr bgcolor="#f2f2f2" align="center">
																															
																													
																																</tr>				
																															</table>
																														</td>
																													</tr>
																													<input type='hidden' name="hidProductTableRowCount" id="hidProductTableRowCount" value="">
																													<!--  Dynamic Row Ends Here-->
																													<tr id="catRow2">
																														<TD height="5"></TD>
																													</tr>
																													<tr id="catRow3">
																														<TD style="padding-left:5px;padding-right:5px;">
																															<a href="###" id='addRow' onclick="javascript:addNewProduct();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																														</TD>
																													</tr>
																												</table>
																											</td>			
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Net Wt</td>
																											<td align="left">
																												<input type="text" name="comboPdtNetwt" id="comboPdtNetwt" size="6" value="" readonly /><span class="fieldName"> in Kg</span>
																											</td>
																										</tr>
																									</table>
																									<table id="singleNetWgt" style="border:1px solid #999999; width:100%; padding:5px; margin-top:10px; display:none;">
																										<tr>
																											<td valign="top">
																												<table>
																													<tr>
																														<td class="fieldName" nowrap >*Net Wt</td>
																														<td align="left">
																															<input type="text" name="productNetwt" id="productNetwt" size="6" value="" onkeyup="calculateGravyWt()"/><span class="fieldName"> in Kg</span>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >*Fixed Wt</td>
																														<td align="left">
																															<input type="text" name="fixedWgt" id="fixedWgt" size="6" value="" onkeyup="calculateGravyWt()"/><span class="fieldName"> in Kg</span>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >*Gravy Wt</td>
																														<td align="left">
																															<input type="text" name="gravyWgt" id="gravyWgt" size="6" value="" readOnly /><span class="fieldName"> in Kg</span>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >No of Batches</td>
																														<td align="left">
																															<input type="text" name="noOfBatches" id="noOfBatches" size="6" value=""/>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Batch Size</td>
																														<td align="left">
																															<input type="text" name="batchSize" id="batchSize" size="6" value="" onkeyup="calcPerPouchCost();"/>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >*Recipe Name</td>
																														<td nowrap align="left">
																															<select name="recipeName" id="recipeName" onchange="xajax_getRecipeCost(this.value, document.getElementById('fixedWgt').value, document.getElementById('gravyWgt').value)">
																																<option value="">-- Select --</option>
																																<?php
																																	if(sizeof($recipeRecords)>0)
																																	{
																																		foreach($recipeRecords as $recipe)
																																		{
																																			$recipeId = $recipe[0];
																																			$recipeName = $recipe[1];
																																			$selected = "";
																																			?>
																																			<option value="<?=$recipeId;?>" <?=$selected?>><?=$recipeName;?></option>
																																			<?php
																																		}
																																	}
																																?>
																															</select>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Production Name</td>
																														<td nowrap align="left">
																															<select name="productionName" id="productionName" onchange="xajax_calcPerPouchCost(this.value,document.getElementById('batchSize').value);">
																																<option value="">-- Select --</option>
																															</select>
																														</td>
																													</tr>
																												</table>
																											</td>
																											<td>&nbsp;&nbsp;&nbsp;</td>
																											<td valign="top">
																												<table>
																													<tr>
																														<td class="fieldName" nowrap >Packing Name</td>
																														<td nowrap align="left">
																															<select name="packingName" id="packingName" onchange="xajax_getPackingCost(this.value);">
																																<option value="">-- Select --</option>
																															</select>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >R&D Cost Included</td>
																														<td nowrap align="left">
																															<select name="rdCostIncluded" id="rdCostIncluded">
																																<option value="">-- Select --</option>
																																<option value="1">Yes</option>
																																<option value="2">Partial</option>
																																<option value="3">No</option>
																															</select>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Shared Primary Packing</td>
																														<td nowrap align="left">
																															<select name="sharedPrmryPckng" id="sharedPrmryPckng" onchange="innerOuterShareVal(this.value)">
																																<option value="">-- Select --</option>
																																<option value="1">Yes</option>
																																<option value="2">No</option>
																															</select>
																														</td>
																													</tr>
																													<tr>
																														<td id="shareDiv" style="display:none; float:left;" class="fieldName" nowrap >Shared Value</td>
																														<td nowrap align="left" id="shareInputDiv" style="display:none;"><input type="text" name="shareVal" id="shareVal" value="" size="6">&nbsp;<span class="fieldName">Inner</span><input type="checkbox" name="sharePack" id="sharePack" value="Inner">&nbsp;<span class="fieldName">Outer</span><input type="checkbox" name="sharePack" id="sharePack" value="Outer"></td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Customer Credit Period</td>
																														<td nowrap align="left">
																															<input type="text" name="customerCreditPeriod" id="customerCreditPeriod" value="" size="6" readonly>
																														</td>
																													</tr>
																													<tr>
																														<td>
																															<input type="hidden" id="hidPaymentTermId" name="hidPaymentTermId" value=""/>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Payment</td>
																														<td nowrap align="left">
																															<select name="payment" id="payment" onchange="xajax_getPaymentDuration(this.value);">
																																<option value="">Select</option>
																																<?
																																foreach($paymentRecs as $pay)
																																{
																																	$paymentId = $pay[0];
																																	$paymentName = $pay[1];
																																	$selected = "";
																																	?>
																																<option value="<?=$paymentId;?>" <?=$selected?>><?=$paymentName;?></option>
																																<?
																																}
																																?>
																															</select><span class="fieldName"> Days</span>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Main Ingredient Cost</td>
																														<td>
																															<input type="text" name="mainIngredntCost" id="mainIngredntCost" size="6" readOnly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Gravy Cost</td>
																														<td>
																														   <input type="text" name="gravyCost" id="gravyCost" size="6" readOnly />
																														</td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																									<table style="border:1px solid #999999; width:100%; padding:5px; margin-top:10px;">
																										<tr>
																											<td valign="top">
																												<table>
																													<tr>
																														<td class="fieldName" nowrap >Raw Material Cost</td>
																														<td>
																														   <input type="text" name="rawMaterialCost" id="rawMaterialCost" size="6" readOnly />
																														</td>
																													</tr>
																													
																													<tr>
																														<td class="fieldName" nowrap >Processing Cost</td>
																														<td>
																															<input type="text" name="processCost" id="processCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Testing Cost</td>
																														<td>
																															<input type="text" name="testingCost" id="testingCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Inner Packing Cost</td>
																														<td>
																															<input type="text" name="innerPackingCost" id="innerPackingCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Outer Packing Cost</td>
																														<td>
																															<input type="text" name="outerPackingCost" id="outerPackingCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Basic Manufacturing Cost</td>
																														<td>
																															<input type="text" name="basicManuftrCost" id="basicManuftrCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Marketing Cost</td>
																														<td>
																															<input type="text" name="marktngCost" id="marktngCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Advertisement Cost</td>
																														<td>
																															<input type="text" name="advtsCost" id="advtsCost" size="6" readonly />
																														</td>
																													</tr>
																												</table>
																											</td>
																											<td>&nbsp;&nbsp;&nbsp;</td>
																											<td valign="top">
																												<table>
																													<tr>
																														<td class="fieldName" nowrap >Holding Cost</td>
																														<td>
																															<input type="text" name="holdingCost" id="holdingCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Admin Overhead</td>
																														<td>
																															<input type="text" name="adminOverhead" id="adminOverhead" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Total Cost</td>
																														<td>
																															<input type="text" name="totalCost" id="totalCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Profit Margin</td>
																														<td>
																															<input type="text" name="profitMargin" id="profitMargin" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Actual Fact Cost</td>
																														<td>
																															<input type="text" name="actualFactCost" id="actualFactCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Contingency</td>
																														<td>
																															<input type="text" name="contingency" id="contingency" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Ideal Factory Cost</td>
																														<td>
																															<input type="text" name="idealFactCost" id="idealFactCost" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >PM in % of FC</td>
																														<td>
																															<input type="text" name="pmPercent" id="pmPercent" size="6" readonly />
																														</td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																									<table id="comboIdealPrice" style="border:1px solid #999999; width:100%; padding:5px; margin-top:10px; display:none;">
																										<tr>
																											<td valign="top" style="float:left; margin-left:44px; margin-right:48px;">
																												<table>
																													<tr>
																														<td class="fieldName" nowrap >Ideal Selling Price</td>
																														<td>
																															<input type="text" name="idealSellingPrice" id="idealSellingPrice" size="6" readonly />
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName" nowrap >Pkg per MC</td>
																														<td>
																															<input type="text" name="pkgPerMC" id="pkgPerMC" size="6" />
																														</td>
																													</tr>
																												</table>
																											</td>
																											<td>&nbsp;&nbsp;&nbsp;</td>
																											<td valign="top">
																												<table>
																													<tr>
																														<td class="fieldName" nowrap >Cost of MC</td>
																														<td>
																															<input type="text" name="costOfMC" id="costOfMC" size="6" readonly />
																														</td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																								</td>
																								</tr>
																						</table>
																					</TD>
																				</tr>
																				<!--Process Type Values-->
																				<input type="hidden" name="waterCostValue" id="waterCostValue" value=""/>
																				<input type="hidden" name="dieselCostValue" id="dieselCostValue" value=""/>
																				<input type="hidden" name="electCostValue" id="electCostValue" value=""/>
																				<input type="hidden" name="gasCostValue" id="gasCostValue" value=""/>
																				
																				<!--Per Batch Cost-->
																				<input type="hidden" name="waterCostperBatch" id="waterCostperBatch" value=""/>
																				<input type="hidden" name="dieselCostperBatch" id="dieselCostperBatch" value=""/>
																				<input type="hidden" name="electCostperBatch" id="electCostperBatch" value=""/>
																				<input type="hidden" name="gasCostperBatch" id="gasCostperBatch" value=""/>
																				<input type="hidden" name="fixedManpowerCost" id="fixedManpowerCost" value=""/>
																				<input type="hidden" name="fixedStaffCost" id="fixedStaffCost" value=""/>
																				<input type="hidden" name="mainConsCost" id="mainConsCost" value=""/>
																				<input type="hidden" name="variblManpowerCost" id="variblManpowerCost" value=""/>
																				<input type="hidden" name="noOfDaysMnth" id="noOfDaysMnth" value=""/>
																				<input type="hidden" name="noOfDaysYear" id="noOfDaysYear" value=""/>
																				<input type="hidden" name="testingPouchUnit" id="testingPouchUnit" value=""/>
																				<input type="hidden" name="pdtHoldingCost" id="pdtHoldingCost" value=""/>
																				<input type="hidden" name="paymntDuratn" id="paymntDuratn" value=""/>
																				
																				<!--Per Pouch Cost-->
																				<input type="hidden" name="waterCostperPouch" id="waterCostperPouch" value=""/>
																				<input type="hidden" name="dieselCostperPouch" id="dieselCostperPouch" value=""/>
																				<input type="hidden" name="electCostperPouch" id="electCostperPouch" value=""/>
																				<input type="hidden" name="gasCostperPouch" id="gasCostperPouch" value=""/>
																				<input type="hidden" name="consumblCostperPouch" id="consumblCostperPouch" value=""/>
																				<input type="hidden" name="manpowerCostperPouch" id="manpowerCostperPouch" value=""/>
																				<input type="hidden" name="rdCostperPouch" id="rdCostperPouch" value=""/>
																				
																				<!--Packing Cost-->
																				<input type="hidden" name="packingInnerCost" id="packingInnerCost" value=""/>
																				<input type="hidden" name="packingOuterCost" id="packingOuterCost" value=""/>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>

																					<td colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMatrix.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductMatrix(document.frmProductMatrix);">												</td>
																					
																					<?} else{?>

																					<td  colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMatrix.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductMatrix(document.frmProductMatrix);">												</td>
																					<?}?>
																				</tr>
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																			</table>									
																		</td>
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
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientsMaster.php?categoryFilter=<?=$categoryFilterId?>&mainCategoryFilter=<?=$mainCategoryFilterId?>',700,600);"><? }?></td>
											</tr>
										</table>									
									</td>
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
													</div> 
												</td>
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
												//$stockInHand	= ($ir[5]==0)?"":$ir[5];
												$categoryName	= $ir[6];
												$mainCategoryName = $ir[7];
												$active=$ir[8];
												$ingredientStock = $ingredientMasterObj->getIngredientStock($ingredientId);

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
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right"><?=$ingredientStock[0]?></td>
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right"><?=($ingRate!="")?$ingRate:"";?></td>
												<? if($edit==true){?>
												<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ingredientId;?>,'editId');this.form.action='IngredientsMaster.php';" ><? } ?></td>
												<? }?>
												<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
												<?php if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$ingredientId;?>,'confirmId');"  >
													<?php } else if ($active==1){?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$ingredientId;?>,'confirmId');"  >
													<?php }?>
												</td>
												<? }?>
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
													</div> 
												</td>
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
									</table>									
								</td>
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
//if ($addMode || $editMode) {
if ($addMode) 
{
?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	function addNewProduct()
	{
		addNewRow('tblProduct','','');
	}

	function addNewItems()
	{
		addNewProduct();
	}
</SCRIPT>
<?php 
} 
?>


<?php		
if ($addMode) 
{
?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	window.load = addNewItems();
</SCRIPT>
<?php 
}
?>


<!-- Edit Record -->
<script language="JavaScript" type="text/javascript">	
// Get state
<?php
if ($editMode) 
{
if (sizeof($selProductRecs)>0)
{
	$j=0;
	foreach($selProductRecs as $pdt) 
	{	
		$productId 		= $pdt[0];
		$productName	= $pdt[1];
		?>	
		addNewRow('tblProduct','<?=$productId?>', '<?=$productName?>');		
		<?
	$j++;
	}
} 
?>
function addNewProduct()
{
	addNewRow('tblProduct','','');
}
<?
 }
?>
</script>


<? if ($addMode || $editMode) {?>
<script>
//Per Pouch Cost
findPerPouchCost();
// Find the Product Marketing Cost
findProductMktgCost();
//Find the Advert Cost Calculation
findAdvertCost();
// Calculate RM Cost
calcProductRMCost();
//Contingency
findContingency();	
</script>
<? }?>
</form>
<?
	# Include Template [bottomRightNav.php]
	//require("template/bottomRightNav.php");
?>
