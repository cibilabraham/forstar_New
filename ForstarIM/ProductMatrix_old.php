<?php
	require("include/include.php");
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
	
	
	#Add 
	if ($p["cmdAdd"]!="") {
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

		
		if ($productCode!="" && $productName!="") {
			$productMatrixRecIns = $productMatrixObj->addProductMatrix($productCode, $productName, $netWt, $fishWt, $gravyWt, $percentSeafood, $forExport, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch);

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
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
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

	#heading Section
	if ($editMode) $heading	= $label_editProductMatrix;
	else	       $heading	= $label_addProductMatrix;

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
	<tr><TD height="10"></TD></tr>
	<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
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
					$bxHeader="Product Matrix";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
	<?
		if ( $editMode || $addMode) {
	?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
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
			<input type="hidden" name="hidProductMatrixId" value="<?=$editProductMatrixId;?>">
			<tr>
				  <td colspan="2" nowrap class="fieldName" >
					<table width="200">
					<tr>
<!-- 	Ist Column -->
					<TD valign="top">
					<table>
					 <tr>
					  <td class="fieldName" nowrap >*Product Code</td>
					  <td>
					<? if ($p["productCode"]!="") $productCode = $p["productCode"]; ?>
					  <input type="text" name="productCode" size="20" value="<?=$productCode?>" /></td>
				  	</tr>	
					<tr>
					  <td class="fieldName" nowrap >*Product Name</td>
					  <td>
					<? if ($p["productName"]!="") $productName = $p["productName"]; ?>
					<input type="text" name="productName" size="20" value="<?=$productName?>"></td>
				  	</tr>	
					<tr>
					  <td class="fieldName" nowrap >*Net Wt</td>
					  <td>
						<? if ($p["netWt"]!="") $netWt = $p["netWt"]; ?>
						<input type="text" name="netWt" id="netWt" size="6" value="<?=$netWt?>" style="text-align:right" onkeyup="findGravyWt();calcProductRMCost();">
					  </td>
					  </tr>
					<tr>
					  <td class="fieldName" nowrap >*Fish Wt</td>
					  <td class="listing-item">
						<? if ($p["fishWt"]!="") $fishWt = $p["fishWt"]; ?>
						<input type="text" name="fishWt" id="fishWt" size="6" value="<?=$fishWt?>" style="text-align:right" onkeyup="findGravyWt();findPerPouchCost();calcProductRMCost();"></td>
					</tr>					
					<tr>
					  <td class="fieldName" nowrap >Gravy Wt</td>
					<td class="listing-item">
					<? if ($p["gravyWt"]!="") $gravyWt = $p["gravyWt"]; ?>
					<input type="text" name="gravyWt" id="gravyWt" size="6" value="<?=$gravyWt?>" style="text-align:right" readonly>
					</td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >% of Seafood</td>
					  <td class="listing-item" nowrap>
						<? if ($p["percentSeafood"]!="") $percentSeafood = $p["percentSeafood"]; ?>
						<input type="text" name="percentSeafood" id="percentSeafood" size="6" value="<?=$percentSeafood?>" style="text-align:right" readonly>&nbsp;%
					  </td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >*For Export </td>
					  <td class="listing-item">
						<? if ($p["forExport"]!="") $forExport = $p["forExport"]; ?>
						<select name="forExport" id="forExport" onchange="findProductMktgCost();findAdvertCost();">
						<option value="">--Select--</option>				
						<option value="Y" <? if ($forExport=='Y') echo "Selected";?>>Y</option>	
						<option value="N" <? if ($forExport=='N') echo "Selected";?>>N</option>	
						<option value="R" <? if ($forExport=='R') echo "Selected";?>>R</option>	
						</select>
					  </td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >*RM Code</td>
					  <td class="listing-item">
						<? if ($p["rMCodeId"]!="") $rMCodeId = $p["rMCodeId"]; ?>
						<? if ($addMode) {?>
						<select name="rMCodeId" id="rMCodeId" onchange="this.form.submit(); calcProductRMCost()">
						<? } else {?>
						<select name="rMCodeId" id="rMCodeId" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();calcProductRMCost();">	
						<? }?>
						<option value="">--Select--</option>
						<?
						foreach ($productMasterRecords as $pmr) {
							$productId	=	$pmr[0];
							$productCode	=	$pmr[1];
							$productName	=	$pmr[2];
							$selected = "";
							if ($rMCodeId==$productId) $selected = "Selected";
						?>	
						<option value="<?=$productId?>" <?=$selected?>><?=$productCode?></option>
						<? }?>
						</select>						
					  </td>
					</tr>
					<tr>
					  		<td class="fieldName" nowrap >*No of Batches</td>
					  		<td class="listing-item">
							<? if ($p["noOfBatches"]!="") $noOfBatches = $p["noOfBatches"]; ?>
							<input type="text" name="noOfBatches" id="noOfBatches" size="6" value="<?=$noOfBatches?>" onkeyup="findPerPouchCost();">
							</td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >*Batch Size</td>
					  		<td class="listing-item">
							<? if ($p["batchSize"]!="") $batchSize = $p["batchSize"]; ?>
							<input type="text" name="batchSize" size="5" id="batchSize" value="<?=$batchSize?>" onkeyup="findPerPouchCost();"></td>
							</tr>
					</table>
					</TD>
<!-- IInd Column -->
					<td valign="top">
						<table>							
							
							<tr>
					  		<td class="fieldName" nowrap >*Fish</td>
					 		<td class="listing-item">
						<? if ($addMode) {?>
						<select name="selFish" id="selFish" onchange="this.form.submit();findPerPouchCost();">
						<? } else {?>
						<select name="selFish" id="selFish" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();findPerPouchCost();">
						<? }?>
						<option value="">-- Select --</option>	
						<?
						while(($fcr=$fishCuttingResultSetObj->getRow())) {
							$fishCuttingRecId 	= $fcr[0];
							$fName			= stripSlash($fcr[1]);
							$fCode			= $fcr[2];	
							$fishCuttingCost	= $fcr[3];
							$selected = "";
							if ($selFishId==$fishCuttingRecId) $selected = "Selected";
						?>		
						<option value="<?=$fishCuttingRecId?>" <?=$selected?>> <?=$fCode?></option>
						<? }?>	
						</select>
							</td>
							</tr>
							<tr>
							<td class="fieldName" nowrap >*Production Code</td>
					 		<td class="listing-item">
							<? if ($addMode) {?>
							<select name="productionCode" onchange="this.form.submit();findPerPouchCost();">
							<? } else {?>
							<select name="productionCode" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();findPerPouchCost();">
							<? }?>
							<option value="">-- Select --</option>
							<?
							while(($pmr=$productionMatrixResultSetObj->getRow())) {
								$productionMatrixRecId 	= $pmr[0];
								$pmCode			= $pmr[1];
								$pmName			= $pmr[2];
								$selected = "";
								if ($selProductionCodeId==$productionMatrixRecId) $selected = "Selected";	
							?>
							<option value="<?=$productionMatrixRecId?>" <?=$selected?>><?=$pmCode?></option>
							<? }?>
							</select>
							</td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >*Packing Code</td>
					  		<td class="listing-item">
							<? if ($addMode) {?>
							<select name="packingCode" onchange="this.form.submit();">
							<? } else {?>
							<select name="packingCode" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();">
							<? }?>
							<option value="">-- Select --</option>
							<?
							while(($pmr=$pkgMatrixResultSetObj->getRow())) {
								$pkgMatrixRecId 	= $pmr[0];
								$pmCode			= $pmr[1];
								$pmName			= $pmr[2];
								$numOfPacksMC		= $pmr[10];
								$selected = "";
								if ($selPkgCodeId==$pkgMatrixRecId) $selected = "Selected";	
							?>
							<option value="<?=$pkgMatrixRecId?>" <?=$selected?>><?=$pmCode?></option>
							<? }?>
							</select>
							</td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >*Freight Charges per Pack</td>
					  		<td class="listing-item">
							<? if ($p["freightChargePerPack"]!="") $freightChargePerPack = $p["freightChargePerPack"]; ?>
							<input type="text" name="freightChargePerPack" size="5" id="freightChargePerPack" value="<?=$freightChargePerPack?>" style="text-align:right;">							
							</td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >*Excise Rate</td>
					  		<td class="listing-item">
							<? if ($p["productExciseRate"]!="") $productExciseRate = $p["productExciseRate"]; ?>
							<input type="text" name="productExciseRate" size="5" id="productExciseRate" value="<?=$productExciseRate?>" style="text-align:right;">&nbsp;%
							</td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >PM in % of FC</td>
					  		<td class="listing-item"><input type="text" name="pmInPercentOfFc" size="5" id="pmInPercentOfFc" value="<?=$pmInPercentOfFc?>" style="text-align:right;" readonly>&nbsp;%</td>
							</tr>
							<tr>
						<td class="fieldName" nowrap >*IDEAL FACTORY COST</td>
					 	<td class="listing-item">
						<? if ($p["idealFactoryCost"]!="") $idealFactoryCost = $p["idealFactoryCost"]; ?>
						<input type="text" name="idealFactoryCost" size="5" id="idealFactoryCost" value="<?=$idealFactoryCost?>" style="text-align:right;" onkeyup="findContingency();">
						</td>
						</tr>
						<tr>
					  	<td class="fieldName" nowrap >Contingency</td>
					  	<td class="listing-item"><input type="text" name="contingency" size="5" id="contingency" value="<?=$contingency?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
					  	<td class="fieldName" nowrap >Actual Fact Cost</td>
					  <td class="listing-item"><input type="text" name="actualFactCost" size="7" id="actualFactCost" value="<?=$actualFactCost?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Profit Margin</td>
					  <td class="listing-item"><input type="text" name="productProfitMargin" size="6" id="productProfitMargin" value="<?=$productProfitMargin?>" style="text-align:right;" readonly></td>
					</tr>
						</table>	
					</td>
					<td valign="top">
					<table>							
					<tr>
					  <td class="fieldName" nowrap >Total Cost</td>
					  <td class="listing-item"><input type="text" name="totalCost" size="6" id="totalCost" value="<?=$totalCost?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Admin Overhead @5%</td>
					  <td class="listing-item"><input type="text" name="adminOverhead" size="6" id="adminOverhead" value="<?=$adminOverhead?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Holding Cost</td>
					  <td class="listing-item"><input type="text" name="proHoldingCost" size="5" id="proHoldingCost" value="<?=$proHoldingCost?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Advertisement Cost</td>
					  <td class="listing-item"><input type="text" name="proAdvertCost" size="5" id="proAdvertCost" value="<?=$proAdvertCost?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Marketing Cost</td>
					  <td class="listing-item"><input type="text" name="mktgCost" size="5" id="mktgCost" value="<?=$mktgCost?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Basic Manufacturing Cost</td>
					  <td class="listing-item"><input type="text" name="basicManufactCost" size="5" id="basicManufactCost" value="<?=$basicManufactCost?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Outer packing Cost</td>
					  <td class="listing-item"><input type="text" name="productOuterPkgCost" size="5" id="productOuterPkgCost" value="<?=$outerPkgCost?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Inner Packing Cost</td>
					  <td class="listing-item"><input type="text" name="productInnerPkgCost" size="5" id="productInnerPkgCost" value="<?=$innerPkgCost?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Testing Cost</td>
					  <td class="listing-item"><input type="text" name="testingCost" size="5" id="testingCost" value="<?=$testingCost?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Processing cost</td>
					  <td class="listing-item"><input type="text" name="processingCost" size="5" id="processingCost" value="<?=$processingCost?>" style="text-align:right;" readonly></td>
					</tr>									
					</table>
					</td>
<!--  Third Column End Here-->
<!--  IVth Column Starts Here-->
					<td valign="top">
					<table>						
					<tr>
					  <td class="fieldName" nowrap>Raw Material Cost</td>
					  <td class="listing-item"><input type="text" name="rMCost" size="5" id="rMCost" value="<?=$rMCost?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap>Seafood Cost</td>
					  <td class="listing-item"><input type="text" name="seaFoodCost" size="5" id="seaFoodCost" value="<?=$seaFoodCost?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Gravy Cost</td>
					  <td class="listing-item"><input type="text" name="gravyCost" size="5" id="gravyCost" value="<?=$gravyCost?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Water cost/pouch</td>
					  <td class="listing-item"><input type="text" name="waterCostPerPouch" size="5" id="waterCostPerPouch" value="<?=$waterCostPerPouch?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Diesel Cost per pouch</td>
					  <td class="listing-item"><input type="text" name="dieselCostPerPouch" size="5" id="dieselCostPerPouch" value="<?=$dieselCostPerPouch?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Elect cost per pouch</td>
					  <td class="listing-item"><input type="text" name="electricCostPerPouch" size="5" id="electricCostPerPouch" value="<?=$electricCostPerPouch?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Gas cost per Pouch</td>
					  <td class="listing-item"><input type="text" name="gasCostPerPouch" size="5" id="gasCostPerPouch" value="<?=$gasCostPerPouch?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Consumables per pouch</td>
					  <td class="listing-item"><input type="text" name="consumableCostPerPouch" size="5" id="consumableCostPerPouch" value="<?=$consumableCostPerPouch?>" style="text-align:right;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Manpower Cost/Pouch</td>
					  <td class="listing-item"><input type="text" name="manPowerCostPerPouch" size="5" id="manPowerCostPerPouch" value="<?=$manPowerCostPerPouch?>" style="text-align:right;" readonly></td>
					</tr>	
					<tr>
					  <td class="fieldName" nowrap >Fish prep cost/Pouch</td>
					  <td class="listing-item"><input type="text" name="fishPrepCostPerPouch" size="5" id="fishPrepCostPerPouch" value="<?=$fishPrepCostPerPouch?>" style="text-align:right;" readonly></td>
					</tr>					
					</table>
					</td>
<!--  IVth Column End Here-->
					</tr>						
					</table></td>
					</tr>
					<tr>
						<td colspan="2"  height="10" ></td>
					</tr>
					<tr>
	<? if($editMode){?>
	<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMatrix.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductMatrix(document.frmProductMatrix);">
	</td>
	<?} else{?>
	<td  colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMatrix.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductMatrix(document.frmProductMatrix);">		
	</td>
	<input type="hidden" name="cmdAddNew" value="1">
	<?}?>
	</tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	</table></td>
	</tr>
	</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		</table>
		</td>
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Matrix  </td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductMatrix.php',700,600);"><? }?></td>
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
<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($productMatrixRecordSize) {
			$i	=	0;
		?>
		<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Code</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" width="100">Batch Size</td>	
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
		while (($pmr=$productMatrixResultSetObj->getRow())) {
			$i++;
			$productMatrixRecId 	= $pmr[0];
			$productCode			= $pmr[1];
			$productName			= $pmr[2];
			$batchSize		= $pmr[10];					
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productMatrixRecId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$productCode?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$productName?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$batchSize?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productMatrixRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange'); this.form.action='ProductMatrix.php';" ></td>
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
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</table></td>
	</tr>
	<tr>
	<td colspan="3" height="5">
	<input type="hidden" name="waterCostPerBtch" id="waterCostPerBtch" value="<?=$waterCostPerBtch?>">
	<input type="hidden" name="dieselCostPerBtch" id="dieselCostPerBtch" value="<?=$dieselCostPerBtch?>">	
	<input type="hidden" name="electricityCostPerBtch" id="electricityCostPerBtch" value="<?=$electricityCostPerBtch?>">
	<input type="hidden" name="gasCostPerBtch" id="gasCostPerBtch" value="<?=$gasCostPerBtch?>">	
	<input type="hidden" name="maintCostPerBtch" id="maintCostPerBtch" value="<?=$maintCostPerBtch?>">
	<input type="hidden" name="variManPwerCostPerBtch" id="variManPwerCostPerBtch" value="<?=$variManPwerCostPerBtch?>">
	<input type="hidden" name="fixedManPowerCostPerDay" id="fixedManPowerCostPerDay" value="<?=$fixedManPowerCostPerDay?>">
	<input type="hidden" name="selFishCost" id="selFishCost" value="<?=$selFishCost?>"> 
	<input type="hidden" name="mktgTeamCostPerPouch" id="mktgTeamCostPerPouch" value="<?=$mktgTeamCostPerPouch?>"> 
	<input type="hidden" name="mktgTravelCost" id="mktgTravelCost" value="<?=$mktgTravelCost?>"> 
	<input type="hidden" name="adCostPerPouch" id="adCostPerPouch" value="<?=$adCostPerPouch?>"> 
	<input type="hidden" name="fishRatePerKgPerBatch" id="fishRatePerKgPerBatch" value="<?=$fishRatePerKgPerBatch?>"> 
	<input type="hidden" name="gravyRatePerKgPerBatch" id="gravyRatePerKgPerBatch" value="<?=$gravyRatePerKgPerBatch?>">
	<input type="hidden" name="pouchesTestPerBatchUnit" id="pouchesTestPerBatchUnit" value="<?=$pouchesTestPerBatchUnit?>">
	<input type="hidden" name="holdingCost" id="holdingCost" value="<?=$holdingCost?>">
	<input type="hidden" name="holdingDuration" id="holdingDuration" value="<?=$holdingDuration?>">
	<input type="hidden" name="noOfDaysInYear" id="noOfDaysInYear" value="<?=$noOfDaysInYear?>">
	<input type="hidden" name="adminOverheadChargesCost" id="adminOverheadChargesCost" value="<?=$adminOverheadChargesCost?>">
	<input type="hidden" name="profitMargin" id="profitMargin" value="<?=$profitMargin?>">	
	
	</td>
	</tr>
	<tr>	
		<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
		<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductMatrix.php',700,600);"><? }?></td>
	</tr>
	</table>
	</td>
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
		<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	</table>
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
	require("template/bottomRightNav.php");
?>
