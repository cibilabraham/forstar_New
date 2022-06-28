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

	/*
		$num = 0.255;
		echo "Number=".$string = ltrim($num,"0.");
	*/	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;	
	
	# Add 
	if ($p["cmdAdd"]!="") {
		$productCode	= addSlash(trim($p["productCode"]));
		$productName	= addSlash(trim($p["productName"]));
		$forExport	= $p["forExport"];		
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

		$productCombination	= $p["productCombination"]; // Num of Product Combination
		
		if ($productCode!="" && $productName!="") {
			$comboMatrixRecIns = $comboMatrixObj->addComboMatrix($productCode, $productName, $forExport, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch, $productCombination, $userId);

			#Find the Last inserted Id 
			$lastId = $databaseConnect->getLastInsertedId();

			$hidColumnCount = $p["hidColumnCount"];
			for ($i=1; $i<=$hidColumnCount;$i++) {
				$netWt		= $p["netWt_".$i];
				$fishWt		= $p["fishWt_".$i];
				$gravyWt	= $p["gravyWt_".$i];
				$percentSeafood	= $p["percentSeafood_".$i];
				$rMCodeId	= $p["rMCodeId_".$i];
				$noOfBatches	= $p["noOfBatches_".$i];
				$batchSize	= $p["batchSize_".$i];
				$selFish	= $p["selFish_".$i];
				$productionCode	= $p["productionCode_".$i];
				if ($netWt!="" && $fishWt!="" && $lastId!="") {
					$productRecIns = $comboMatrixObj->addMixProductRec($lastId, $netWt, $fishWt, $gravyWt, $percentSeafood, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode);
					// Find the Combo Matrix Entry Last Id
					$entryLastId = $databaseConnect->getLastInsertedId();
				}

				/********************************/
				$itemCount	 = $p["hidItemCount_".$i];	
				$productCategory = $p["productCategory_".$i];
				$productState 	 = $p["productState_".$i];
				$productGroup = ($p["productGroup_".$i]=="")?0:$p["productGroup_".$i];
				$selProduct 	= $p["rMCodeId_".$i]; // Base product Id
				$gmsPerPouch	= $p["productGmsPerPouch_".$i];
	
				// Insert a New Product Code and Name
				$newProductCode = $p["hidProductCode_".$i]."-".ltrim($gmsPerPouch,"0.");
				$newProductName = $p["hidProductName_".$i]."-".ltrim($gmsPerPouch,"0.");
		
				// Reference fields
				$productRatePerPouch 	= $p["productRatePerPouch_".$i];
				$fishRatePerPouch	= $p["fishRatePerPouch_".$i];
				$gravyRatePerPouch	= $p["gravyRatePerPouch_".$i];
				$productGmsPerPouch	= $p["productGmsPerPouch_".$i];
				$fishGmsPerPouch	= $p["fishGmsPerPouch_".$i];
				$gravyGmsPerPouch	= $p["gravyGmsPerPouch_".$i];
				$productPercentagePerPouch = $p["productPercentagePerPouch_".$i];
				$fishPercentagePerPouch	= $p["fishPercentagePerPouch_".$i];
				$gravyPercentagePerPouch = $p["gravyPercentagePerPouch_".$i];
				$productRatePerKgPerBatch = $p["productRatePerKgPerBatch_".$i];
				$fishRatePerKgPerBatch 	= $p["fishRatePerKgPerBatch_".$i];
				$gravyRatePerKgPerBatch = $p["gravyRatePerKgPerBatch_".$i];
				$pouchPerBatch		= $p["pouchPerBatch_".$i];
				$productRatePerBatch	= $p["productRatePerBatch_".$i];
				$fishRatePerBatch	= $p["fishRatePerBatch_".$i];
				$gravyRatePerBatch	= $p["gravyRatePerBatch_".$i];
				$productKgPerBatch	= $p["productKgPerBatch_".$i];
				$fishKgPerBatch		= $p["fishKgPerBatch_".$i];
				$gravyKgPerBatch	= $p["gravyKgPerBatch_".$i];
				$productRawPercentagePerPouch = $p["productRawPercentagePerPouch_".$i];
				$fishRawPercentagePerPouch = $p["fishRawPercentagePerPouch_".$i];
				$gravyRawPercentagePerPouch = $p["gravyRawPercentagePerPouch_".$i];
				$productKgInPouchPerBatch = $p["productKgInPouchPerBatch_".$i];
				$fishKgInPouchPerBatch	= $p["fishKgInPouchPerBatch_".$i];
				$gravyKgInPouchPerBatch	= $p["gravyKgInPouchPerBatch_".$i];
				$fishPercentageYield	= $p["fishPercentageYield_".$i];
				$gravyPercentageYield 	= $p["gravyPercentageYield_".$i];
				$totalFixedFishQty	= $p["totalFixedFishQty_".$i];
				$ingRateList		= $p["hidIngRateList"]; # Ing Rate List	

		if ($entryLastId!=0) {
			$productRecIns = $comboMatrixObj->addProduct($newProductCode, $newProductName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $selProduct, $userId, $entryLastId, $ingRateList);

			#Find the Last inserted Id From m_productmaster Table
			if ($productRecIns) $productEntryLastId = $databaseConnect->getLastInsertedId();
			
			for ($j=1; $j<=$itemCount; $j++) {

				$ingredientId	= $p["selIngredient_".$j."_".$i];
				$quantity	= trim($p["quantity_".$j."_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$j."_".$i]=="")?N:$p["fixedQtyChk_".$j."_".$i];
				$fixedQty	= ($p["fixedQty_".$j."_".$i]=="")?0:$p["fixedQty_".$j."_".$i];
	
				$percentagePerBatch 	= $p["percentagePerBatch_".$j."_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$j."_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$j."_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$j."_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$j."_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$j."_".$i];
				$cleanedQty		= trim($p["cleanedQty_".$j."_".$i]);
				$ingType		= $p["ingType_".$j."_".$i];	

				if ($productEntryLastId!="" && $ingredientId!="" && $quantity!="") {
					$productItemsIns = $comboMatrixObj->addIngredientEntries($productEntryLastId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType);
				}
			}
		}
		/***********************************************/
	}

			if ($comboMatrixRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddComboMatrix);
				$sessObj->createSession("nextPage",$url_afterAddComboMatrix.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddComboMatrix;
			}
			$comboMatrixRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$comboMatrixRecId	= $p["hidComboMatrixId"];

		$productCode	= addSlash(trim($p["productCode"]));
		$productName	= addSlash(trim($p["productName"]));		
		$forExport	= $p["forExport"];
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

		$productCombination	= $p["productCombination"]; // Num of Product Combination
		
		if ($comboMatrixRecId!="" && $productCode!="" && $productName!="") {
			$comboMatrixRecUptd = $comboMatrixObj->updateComboMatrix($comboMatrixRecId, $productCode, $productName, $forExport, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch, $productCombination, $userId);

			$hidColumnCount = $p["hidColumnCount"];
			for ($i=1; $i<=$hidColumnCount;$i++) {
				$productEntryId = $p["productEntryId_".$i];
				$netWt		= $p["netWt_".$i];
				$fishWt		= $p["fishWt_".$i];
				$gravyWt	= $p["gravyWt_".$i];
				$percentSeafood	= $p["percentSeafood_".$i];
				$rMCodeId	= $p["rMCodeId_".$i];
				$noOfBatches	= $p["noOfBatches_".$i];
				$batchSize	= $p["batchSize_".$i];
				$selFish	= $p["selFish_".$i];
				$productionCode	= $p["productionCode_".$i];
				if ($netWt!="" && $fishWt!="" && $comboMatrixRecId!="" && $productEntryId!="") {
					$productRecUpdted = $comboMatrixObj->updateMixProductRec($productEntryId, $netWt, $fishWt, $gravyWt, $percentSeafood, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode);
					
				} else if ($netWt!="" && $fishWt!="" && $comboMatrixRecId!="" && $productEntryId=="") {
					$productRecIns = $comboMatrixObj->addMixProductRec($comboMatrixRecId, $netWt, $fishWt, $gravyWt, $percentSeafood, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode);
					// Find the Combo Matrix Entry Last Id
					$comboEntryLastId = $databaseConnect->getLastInsertedId();
				}

				if ($productEntryId=="") $entryLastId = $comboEntryLastId;
				else $entryLastId = $productEntryId;

				$hidProductMasterId = $p["hidProductMasterId_".$i];
				# Delete all record from product Master
				if ($hidProductMasterId!="") {
					$deleteIngredientItemRecs =	$comboMatrixObj->deleteIngredientItemRecs($hidProductMasterId);
					$productMasterRecDel = $comboMatrixObj->deleteProductMaster($hidProductMasterId);
				}

			/********************************/
				$itemCount	=	$p["hidItemCount_".$i];	
				$productCategory = $p["productCategory_".$i];
				$productState 	 = $p["productState_".$i];
				$productGroup = ($p["productGroup_".$i]=="")?0:$p["productGroup_".$i];
				$selProduct 	= $p["rMCodeId_".$i]; // Base product Id
				$gmsPerPouch	= $p["productGmsPerPouch_".$i];
				
				// Insert a New Product Code and Name
				$newProductCode = $p["hidProductCode_".$i]."-".ltrim($gmsPerPouch,"0.");
				$newProductName = $p["hidProductName_".$i]."-".ltrim($gmsPerPouch,"0.");

				// Reference fields
				$productRatePerPouch 	= $p["productRatePerPouch_".$i];
				$fishRatePerPouch	= $p["fishRatePerPouch_".$i];
				$gravyRatePerPouch	= $p["gravyRatePerPouch_".$i];
				$productGmsPerPouch	= $p["productGmsPerPouch_".$i];
				$fishGmsPerPouch	= $p["fishGmsPerPouch_".$i];
				$gravyGmsPerPouch	= $p["gravyGmsPerPouch_".$i];
				$productPercentagePerPouch = $p["productPercentagePerPouch_".$i];
				$fishPercentagePerPouch	= $p["fishPercentagePerPouch_".$i];
				$gravyPercentagePerPouch = $p["gravyPercentagePerPouch_".$i];
				$productRatePerKgPerBatch = $p["productRatePerKgPerBatch_".$i];
				$fishRatePerKgPerBatch 	= $p["fishRatePerKgPerBatch_".$i];
				$gravyRatePerKgPerBatch = $p["gravyRatePerKgPerBatch_".$i];
				$pouchPerBatch		= $p["pouchPerBatch_".$i];
				$productRatePerBatch	= $p["productRatePerBatch_".$i];
				$fishRatePerBatch	= $p["fishRatePerBatch_".$i];
				$gravyRatePerBatch	= $p["gravyRatePerBatch_".$i];
				$productKgPerBatch	= $p["productKgPerBatch_".$i];
				$fishKgPerBatch		= $p["fishKgPerBatch_".$i];
				$gravyKgPerBatch	= $p["gravyKgPerBatch_".$i];
				$productRawPercentagePerPouch = $p["productRawPercentagePerPouch_".$i];
				$fishRawPercentagePerPouch = $p["fishRawPercentagePerPouch_".$i];
				$gravyRawPercentagePerPouch = $p["gravyRawPercentagePerPouch_".$i];
				$productKgInPouchPerBatch = $p["productKgInPouchPerBatch_".$i];
				$fishKgInPouchPerBatch	= $p["fishKgInPouchPerBatch_".$i];
				$gravyKgInPouchPerBatch	= $p["gravyKgInPouchPerBatch_".$i];
				$fishPercentageYield	= $p["fishPercentageYield_".$i];
				$gravyPercentageYield 	= $p["gravyPercentageYield_".$i];
				$totalFixedFishQty	= $p["totalFixedFishQty_".$i];
				$ingRateList		= $p["hidIngRateList"]; # Ing Rate List	

		if ($entryLastId!=0) {
			$productRecIns = $comboMatrixObj->addProduct($newProductCode, $newProductName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $selProduct, $userId, $entryLastId, $ingRateList);

			#Find the Last inserted Id From m_productmaster Table
			if ($productRecIns) $productEntryLastId = $databaseConnect->getLastInsertedId();
			
			for ($j=1; $j<=$itemCount; $j++) {
				$ingredientId	= $p["selIngredient_".$j."_".$i];
				$quantity	= trim($p["quantity_".$j."_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$j."_".$i]=="")?N:$p["fixedQtyChk_".$j."_".$i];
				$fixedQty	= ($p["fixedQty_".$j."_".$i]=="")?0:$p["fixedQty_".$j."_".$i];
	
				$percentagePerBatch 	= $p["percentagePerBatch_".$j."_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$j."_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$j."_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$j."_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$j."_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$j."_".$i];
				$cleanedQty		= trim($p["cleanedQty_".$j."_".$i]);
				$ingType		= $p["ingType_".$j."_".$i];	

				if ($productEntryLastId!="" && $ingredientId!="" && $quantity!="") {
					$productItemsIns = $comboMatrixObj->addIngredientEntries($productEntryLastId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType);
				}
			}
		}
		/***********************************************/
		}			
	}	
		if ($comboMatrixRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succComboMatrixUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateComboMatrix.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failComboMatrixUpdate;
		}
		$comboMatrixRecUptd	=	false;
	}


	# Edit a Record
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$comboMatrixRec =	$comboMatrixObj->find($editId);
		$editComboMatrixId =	$comboMatrixRec[0];
		$productCode	= stripSlash($comboMatrixRec[1]);
		$productName	= stripSlash($comboMatrixRec[2]);
		$forExport	= $comboMatrixRec[3];
		//$slPkgCodeId	= $comboMatrixRec[4];
		$freightChargePerPack	= $comboMatrixRec[5];
		$productExciseRate	= $comboMatrixRec[6];
		$pmInPercentOfFc	= $comboMatrixRec[7];
		$idealFactoryCost	= $comboMatrixRec[8];
		$contingency		= $comboMatrixRec[9];
		$actualFactCost		= $comboMatrixRec[10];
		$productProfitMargin	= $comboMatrixRec[11];
		$totalCost		= $comboMatrixRec[12];
		$adminOverhead		= $comboMatrixRec[13];
		$proHoldingCost		= $comboMatrixRec[14];
		$proAdvertCost		= $comboMatrixRec[15];
		$mktgCost		= $comboMatrixRec[16];
		$basicManufactCost	= $comboMatrixRec[17];
		$productOuterPkgCost	= $comboMatrixRec[18];
		$productInnerPkgCost	= $comboMatrixRec[19];
		$testingCost		= $comboMatrixRec[20];
		$processingCost		= $comboMatrixRec[21];
		$rMCost			= $comboMatrixRec[22];
		$seaFoodCost		= $comboMatrixRec[23];
		$gravyCost		= $comboMatrixRec[24];
		$waterCostPerPouch	= $comboMatrixRec[25];
		$dieselCostPerPouch	= $comboMatrixRec[26];
		$electricCostPerPouch	= $comboMatrixRec[27];
		$gasCostPerPouch	= $comboMatrixRec[28];
		$consumableCostPerPouch	= $comboMatrixRec[29];
		$manPowerCostPerPouch	= $comboMatrixRec[30];
		$fishPrepCostPerPouch	= $comboMatrixRec[31];	
		$productCombination	= $comboMatrixRec[32];	

		# Listing Mix product combination
		$mixProductRecs = $comboMatrixObj->fetchMixProductRecs($editComboMatrixId);
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$comboMatrixRecId	= $p["delId_".$i];
			if ($comboMatrixRecId!="") {
				// Need to check the selected combo is link with any other process
				# Listing Mix product combination
				$mixProductRecs = $comboMatrixObj->fetchMixProductRecs($comboMatrixRecId);
				foreach ($mixProductRecs as $mpr) {
					$productEntryId = $mpr[0];
					# get Product from product master
					list($productMasterId, $productMasterRefId) = $comboMatrixObj->getProductMasterRecs($productEntryId);	
					# Delete from product Master Entry table
					$deleteIngredientItemRecs =	$comboMatrixObj->deleteIngredientItemRecs($productMasterId);
					# Delete from product Master Main table
					$productMasterRecDel = $comboMatrixObj->deleteProductMaster($productMasterId);
				}	
				# Del record from entry table
				$mixedProductRecDel = $comboMatrixObj->deleteMixProductRec($comboMatrixRecId);
				
				# Del record from main table
				$comboMatrixRecDel = $comboMatrixObj->deleteComboMatrixRec($comboMatrixRecId);
			}
		}
		if ($comboMatrixRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelComboMatrix);
			$sessObj->createSession("nextPage",$url_afterDelComboMatrix.$selection);
		} else {
			$errDel	=	$msg_failDelComboMatrix;
		}
		$comboMatrixRecDel	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Product matrix
	$comboMatrixResultSetObj = $comboMatrixObj->fetchAllPagingRecords($offset, $limit);
	$comboMatrixRecordSize   = $comboMatrixResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allComboMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();
	$numrows	=  $allComboMatrixResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#Get all Packing Cost master Value
	/*
	Removed on 04-09-08
	if ($addMode || $editMode) {
		$packingCostMaster = "PCM";
		$pcmRateList = $manageRateListObj->latestRateList($packingCostMaster);
		list($vatRateForPackingMaterial, $innerCartonWstage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc) = $packingCostMasterObj->getPackingCostMasterValue($pcmRateList);
	}
	*/

	# List all Packing Recs
	$pkgMatrixResultSetObj = $packingMatrixObj->fetchAllRecords();

	# List all Product Records
	$productMasterRecords = $productMasterObj->filterBaseProductRecords();
	
	# List all Fish Cutting Records
	$prodFishCutting = "FCC";
	$fccRateList = $manageRateListObj->latestRateList($prodFishCutting);
	$fishCuttingRecords = $productionFishCuttingObj->fetchAllFishCuttingRecs($fccRateList);

	# List all Production Records
	$productionMatrixRecords = $productionMatrixObj->fetchAllProductionMatrixRecords();

	# Ing Rate List
	$selRateList = $ingredientRateListObj->latestRateList();	

	# Find the packing Code values
	if ($p["editSelectionChange"]=='1'|| $p["packingCode"]=="") { 
		$selPkgCodeId	= $comboMatrixRec[4];
	} else {
		$selPkgCodeId = $p["packingCode"];		
	}
	if ($selPkgCodeId!="") {
		list($packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPkgCost) = $packingMatrixObj->getPackingMatrixRec($selPkgCodeId);
	}
	
	#heading Section
	if ($editMode) $heading	= $label_editComboMatrix;
	else	       $heading	= $label_addComboMatrix;

	$ON_LOAD_PRINT_JS = "libjs/ComboMatrix.js"; # For Printing JS in Head section

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmComboMatrix" action="ComboMatrix.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%" >
	<tr><TD height="10"></TD></tr>
	<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	<tr>
		<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		<td></tr>
		<?
			if ( $editMode || $addMode) {
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ComboMatrix.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateComboMatrix(document.frmComboMatrix);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ComboMatrix.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateComboMatrix(document.frmComboMatrix);">												</td>
				<?}?>
			</tr>
			<input type="hidden" name="hidComboMatrixId" value="<?=$editComboMatrixId;?>">
			<tr>
				  <td colspan="2" nowrap class="fieldName" >
					<table width="200">
					<!--<tr>-->
<!-- 	Ist Column -->
					<tr valign="top">
					<td>
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
					  <td class="fieldName" nowrap >*For Export </td>
					  <td class="listing-item">
						<? if ($p["forExport"]!="") $forExport = $p["forExport"]; ?>
						<select name="forExport" id="forExport" onchange="getComboMatrixMixProduct();">
						<option value="">--Select--</option>
						<option value="Y" <? if ($forExport=='Y') echo "Selected";?>>Y</option>	
						<option value="N" <? if ($forExport=='N') echo "Selected";?>>N</option>	
						<option value="R" <? if ($forExport=='R') echo "Selected";?>>R</option>	
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
						while($pmr=$pkgMatrixResultSetObj->getRow()) {
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
			<td class="fieldName" nowrap >*No.of Product Combination</td>
			<td>
				<? if ($p["productCombination"]!="") $productCombination = $p["productCombination"]; ?>
				<input type="text" name="productCombination" id="productCombination" size="4" value="<?=$productCombination?>" style="text-align:right" onkeyup="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
					  </td>
					  </tr>					
					</table>
					</TD>
					</tr>
<!--  N. of Mix Product -->
<tr>
	<TD colspan="3">
		<table cellpadding="0" cellspacing="0" border="0" align="center">
	<tr>
	<? 
		$col=$productCombination;			
		$productEntryId = "";	
		$rMCodeId = "";	
		for ($i=1;$i<=$col;$i++) {
			if ($editMode) {
				$netWt		= "";
				$fishWt		= "";
				$gravyWt	= "";
				$percentSeafood	= "";
				$rMCodeId	= "";
				$noOfBatches	= "";
				$batchSize	= "";
				$selFishId	= "";
				$selProductionCodeId	= "";
				$productMasterId = "";	
			}
			
			if ($i<=sizeof($mixProductRecs)) {				
				$rec = $mixProductRecs[$i-1];
				$productEntryId = $rec[0];

				$netWt		= $rec[1];
				$fishWt		= $rec[2];
				$gravyWt	= $rec[3];
				$percentSeafood	= $rec[4];
				$rMCodeId	= $rec[5];
				$noOfBatches	= $rec[6];
				$batchSize	= $rec[7];
				$selFishId	= $rec[8];
				$selProductionCodeId	= $rec[9];
				# get new Product from product master
				list($productMasterId, $productMasterRefId) = $comboMatrixObj->getProductMasterRecs($productEntryId);		
			}
	?>
			
	<td width="9%" valign="top">
    	<table cellpadding="0" cellspacing="0">
	<input type="hidden" name="productEntryId_<?=$i?>" value="<?=$productEntryId?>">
	<tr bgcolor="#f2f2f2" class="listing-head">
		<td colspan="2" align="center">PRODUCT <?=$i?></td>
		
	</tr>
	<tr>
		<td class="fieldName" nowrap >*RM Code</td>
		<td class="listing-item">
		<? 
		if ($addMode!="") $rMCodeId = "";
		$fishRatePerKgPerBatch = "";
		$gravyRatePerKgPerBatch = "";
		$fishRatePerPouch = "";
		$ingRecSize = ""; 
		if ($p["rMCodeId_".$i]!="") $rMCodeId = $p["rMCodeId_".$i]; 
				
		if ($p["editSelectionChange"]=='1' || $p["rMCodeId_".$i]=="" || ( $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i] && $editMode!="")) {
			$selProductMasterId =	$productMasterId;		
		} else if ($p["rMCodeId_".$i]!="") {
			$selProductMasterId =	$p["rMCodeId_".$i];
		}	

		if ($selProductMasterId!="") {
			list($productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch) =$productMasterObj->getProductMasterRec($selProductMasterId);
			
			# get ing records	
			$baseProductRecs = $comboMatrixObj->fetchAllIngredients($selProductMasterId, $selRateList);
			$ingRecSize =sizeof($baseProductRecs);	
		}	
		?>
		<select name="rMCodeId_<?=$i?>" id="rMCodeId_<?=$i?>" onchange="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>" >		
		<option value="">--Select--</option>
		<?
			foreach ($productMasterRecords as $pmr) {
				$productId	=	$pmr[0];
				$productCode	=	$pmr[1];
				$productName	=	$pmr[2];
				$selected = "";
				//|| $selProductMasterId==$productId
				if ($rMCodeId==$productId) $selected = "Selected";
		?>	
		<option value="<?=$productId?>" <?=$selected?>><?=$productCode?></option>
		<? }?>
		</select>
<!--  Hide the RM Code Selected id-->
		<input type="hidden" name="hidRMCodeId_<?=$i?>" value="<?=$rMCodeId?>">
		<input type="hidden" name="hidProductMasterId_<?=$i?>" value="<?=$productMasterId?>">
		
		</td>
	</tr>
<!--  Ing List Start-->
<? if ($ingRecSize>0) {?>
<tr>
	<TD colspan="2">	
		<table>
		<?
		if ($selProductMasterId!="") {
			$proCode = "";
			$proName = "";
			list($proCode, $proName) = $productMasterObj->getProductRec($rMCodeId);
			
			list($prCode, $prName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty) = $productMasterObj->getProductRec($selProductMasterId);
			$selProductGmsPerPouch = $productGmsPerPouch;
		}
		?>
	<TR><TD>
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
				<? 					
					if ($p["productRatePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productRatePerPouch=$p["productRatePerPouch_".$i];
				?>
					<input type="text" name="productRatePerPouch_<?=$i?>" id="productRatePerPouch_<?=$i?>" style="text-align:right;border:none; background-color:orange;font-weight:bold" readonly value="<?=$productRatePerPouch?>" size="5"></TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["fishRatePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishRatePerPouch=$p["fishRatePerPouch_".$i];
					?>
					<input type="text" name="fishRatePerPouch_<?=$i?>" id="fishRatePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishRatePerPouch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyRatePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyRatePerPouch=$p["gravyRatePerPouch_".$i];
					?>
					<input type="text" name="gravyRatePerPouch_<?=$i?>" id="gravyRatePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyRatePerPouch?>" size="5">
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow" nowrap>Gms per Pouch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
				<? 					
					if ($p["productGmsPerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productGmsPerPouch=$p["productGmsPerPouch_".$i];
				?>
				<input type="text" size="5" style="text-align:right;" name="productGmsPerPouch_<?=$i?>" id="productGmsPerPouch_<?=$i?>" value="<?=$productGmsPerPouch?>" onkeyup="comboProductConversionIngProportion();calcCmbMtxProductConversionRatePerBatch();" onchange="callFunCalc();" autoComplete="off"></TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
				<? 					
					if ($p["fishGmsPerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishGmsPerPouch=$p["fishGmsPerPouch_".$i];
				?>
					<input type="text" size="4" style="text-align:right;background-color:lightblue; border:none;" name="fishGmsPerPouch_<?=$i?>" id="fishGmsPerPouch_<?=$i?>" value="<?=$fishGmsPerPouch?>" autoComplete="off" readonly>
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyGmsPerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyGmsPerPouch=$p["gravyGmsPerPouch_".$i];
					?>
					<input type="text" name="gravyGmsPerPouch_<?=$i?>" id="gravyGmsPerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyGmsPerPouch?>" size="5">
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow" >% per Pouch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["productPercentagePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productPercentagePerPouch=$p["productPercentagePerPouch_".$i];
					?>
					<input type="text" name="productPercentagePerPouch_<?=$i?>" id="productPercentagePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$productPercentagePerPouch?>" size="5">%
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<? 					
					if ($p["fishPercentagePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishPercentagePerPouch=$p["fishPercentagePerPouch_".$i];
					?>
					<input type="text" name="fishPercentagePerPouch_<?=$i?>" id="fishPercentagePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishPercentagePerPouch?>" size="5">%
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyPercentagePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyPercentagePerPouch=$p["gravyPercentagePerPouch_".$i];
					?>
					<input type="text" name="gravyPercentagePerPouch_<?=$i?>" id="gravyPercentagePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyPercentagePerPouch?>" size="5">%
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["productRatePerKgPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productRatePerKgPerBatch=$p["productRatePerKgPerBatch_".$i];
					?>
					<input type="text" name="productRatePerKgPerBatch_<?=$i?>" id="productRatePerKgPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$productRatePerKgPerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["fishRatePerKgPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishRatePerKgPerBatch=$p["fishRatePerKgPerBatch_".$i];
					?>
					<input type="text" name="fishRatePerKgPerBatch_<?=$i?>" id="fishRatePerKgPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishRatePerKgPerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyRatePerKgPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyRatePerKgPerBatch=$p["gravyRatePerKgPerBatch_".$i];
					?>
					<input type="text" name="gravyRatePerKgPerBatch_<?=$i?>" id="gravyRatePerKgPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyRatePerKgPerBatch?>" size="5">
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Pouches per Batch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
					<? 					
					if ($p["pouchPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $pouchPerBatch=$p["pouchPerBatch_".$i];
					?>
					<input type="text" size="5" style="text-align:right; border:none;background-color:lightblue;" name="pouchPerBatch_<?=$i?>" id="pouchPerBatch_<?=$i?>" value="<?=$pouchPerBatch?>" autoComplete="off" readonly>
				</TD>
				<TD class="listing-item" align="center" colspan="2"></TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Batch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["productRatePerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productRatePerBatch=$p["productRatePerBatch_".$i];
					?>
					<input type="text" name="productRatePerBatch_<?=$i?>" id="productRatePerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$productRatePerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["fishRatePerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishRatePerBatch=$p["fishRatePerBatch_".$i];
					?>
					<input type="text" name="fishRatePerBatch_<?=$i?>" id="fishRatePerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishRatePerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyRatePerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyRatePerBatch=$p["gravyRatePerBatch_".$i];
					?>
					<input type="text" name="gravyRatePerBatch_<?=$i?>" id="gravyRatePerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyRatePerBatch?>" size="5">
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["productKgPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productKgPerBatch=$p["productKgPerBatch_".$i];
					?>
					<input type="text" name="productKgPerBatch_<?=$i?>" id="productKgPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$productKgPerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["fishKgPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishKgPerBatch=$p["fishKgPerBatch_".$i];
					?>
					<input type="text" name="fishKgPerBatch_<?=$i?>" id="fishKgPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishKgPerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyKgPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyKgPerBatch=$p["gravyKgPerBatch_".$i];
					?>
					<input type="text" name="gravyKgPerBatch_<?=$i?>" id="gravyKgPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyKgPerBatch?>" size="5">
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% (Raw) per Batch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<? 					
					if ($p["productRawPercentagePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productRawPercentagePerPouch=$p["productRawPercentagePerPouch_".$i];
					?>
					<input type="text" name="productRawPercentagePerPouch_<?=$i?>" id="productRawPercentagePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$productRawPercentagePerPouch?>" size="5">%
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<? 					
					if ($p["fishRawPercentagePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishRawPercentagePerPouch=$p["fishRawPercentagePerPouch_".$i];
					?>
					<input type="text" name="fishRawPercentagePerPouch_<?=$i?>" id="fishRawPercentagePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishRawPercentagePerPouch?>" size="5">%
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<? 					
					if ($p["gravyRawPercentagePerPouch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyRawPercentagePerPouch=$p["gravyRawPercentagePerPouch_".$i];
					?>
					<input type="text" name="gravyRawPercentagePerPouch_<?=$i?>" id="gravyRawPercentagePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyRawPercentagePerPouch?>" size="5">%
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Kg (in Pouch) per Batch</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["productKgInPouchPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $productKgInPouchPerBatch=$p["productKgInPouchPerBatch_".$i];
					?>
					<input type="text" name="productKgInPouchPerBatch_<?=$i?>" id="productKgInPouchPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$productKgInPouchPerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["fishKgInPouchPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishKgInPouchPerBatch=$p["fishKgInPouchPerBatch_".$i];
					?>
					<input type="text" name="fishKgInPouchPerBatch_<?=$i?>" id="fishKgInPouchPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishKgInPouchPerBatch?>" size="5">
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyKgInPouchPerBatch_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyKgInPouchPerBatch=$p["gravyKgInPouchPerBatch_".$i];
					?>
					<input type="text" name="gravyKgInPouchPerBatch_<?=$i?>" id="gravyKgInPouchPerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyKgInPouchPerBatch?>" size="5">
				</TD>
			</TR>
			<TR bgcolor="#FFFFFF">
				<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% Yield</TD>
				<TD class="listing-item"></TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["fishPercentageYield_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fishPercentageYield=$p["fishPercentageYield_".$i];
					?>
					<input type="text" name="fishPercentageYield_<?=$i?>" id="fishPercentageYield_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$fishPercentageYield?>" size="5">%
				</TD>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<? 					
					if ($p["gravyPercentageYield_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $gravyPercentageYield=$p["gravyPercentageYield_".$i];
					?>
					<input type="text" name="gravyPercentageYield_<?=$i?>" id="gravyPercentageYield_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gravyPercentageYield?>" size="5">%
				</TD>
			</TR>
		</table>
		</TD></TR>
		<tr><TD>
		<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
                        	<tr bgcolor="#f2f2f2" align="center">
                                	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Ingredient</td>
					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Raw <br>Kg</td>
					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Cleaned<br> Kg</td>
					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Fixed Qty</td>		  
					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Qty</td>		 	
					<td class="listing-head" style="padding-left:5px; padding-right:5px; display:none;">%/<br>Batch</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px; display:none;">Rs/<br>Batch</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px; display:none;">Gms/<br>Pouch</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px; display:none;">%Wt/<br>Pouch</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px; display:none;">Rs/<br>Pouch</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px; display:none;">%Cost/<br>Pouch</td>
                                </tr>
		<?php
		$lastPrice = 0;
		$m=0;
		foreach ($baseProductRecs as $pr)	{
			$m++;
			$ingredientId 	= $pr[2];
			//$ingredientName = $pr[12];				
			//$lastPrice  	= $pr[13];
			$editQuantity 	= $pr[3];
			$rawQty		= $pr[3];
			$fixedQtyChk 	= $pr[4];
			
			$fixedQty	= $pr[5];
			$percentagePerBatch = $pr[6];
			$ratePerBatch	= $pr[7];
			$ingGmsPerPouch	= $pr[8];
			$percentageWtPerPouch = $pr[9];
			$ratePerPouch	= $pr[10];
			$percentageCostPerPouch = $pr[11];

			$checked = "";
			$styleDisplay = "";			
			if ($fixedQtyChk=='Y') {
				$checked= "Checked";				
				$styleDisplay = "display:''";
			} else {
				$styleDisplay = "display:'none'"; //none				
			}
			
			$cleanedQty 	  = $pr[12];
			$editCleanedQty   = $pr[12];
			$selIngType	  = $pr[13]; 
			$lastPrice = 0;
			
			$ingredientName = "";
			if ($selIngType=='ING') {	# If ING
				$ingredientName	= $ingredientMasterObj->getIngName($ingredientId);
				list($lastPrice,$declYield) = $productMasterObj->getIngredientRate($ingredientId, $selRateList);
			} else if ($selIngType=='SFP') { # If Semi Finished
				/*$ingredientName = $productMasterObj->getProductName($ingredientId);*/	
				$ingredientName = $semiFinishProductObj->getSemiFinishProductName($ingredientId);	
				list($lastPrice,$declYield) = $productMasterObj->getSemiFinishRate($ingredientId);
			} else {
				$lastPrice	= 0;
				$declYield	= 0;
			}			
		?>
                <tr bgcolor="#FFFFFF" align="center" style="<?=$styleDisplay;?>">
                        <td style="padding-left:5px; padding-right:5px;" class="listing-item" align="left"><?=$ingredientName;?>
				<input type="hidden" name="selIngredient_<?=$m?>_<?=$i?>" id="selIngredient_<?=$m?>_<?=$i?>" value="<?=$ingredientId?>">
				<input type="hidden" name="ingType_<?=$m?>_<?=$i?>" id="ingType_<?=$m?>_<?=$i?>" value="<?=$selIngType?>">
			</td>
                        <td style="padding-left:5px; padding-right:5px;">
				<? 
					if ($p["quantity_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $editQuantity=$p["quantity_".$m."_".$i];
				?>
				<input name="quantity_<?=$m?>_<?=$i?>" type="text" id="quantity_<?=$m?>_<?=$i?>" value="<?=$editQuantity;?>" size="4" style="text-align:right; border:none;" autoComplete="off" readonly>
				<input name="hidQuantity_<?=$m?>_<?=$i?>" type="hidden" id="hidQuantity_<?=$m?>_<?=$i?>" size="4" style="text-align:right;" value="<?=$rawQty?>">
			</td>
			<td style="padding-left:5px; padding-right:5px;">
				<? 
					if ($p["cleanedQty_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $cleanedQty=$p["cleanedQty_".$m."_".$i];
				?>
				<input name="cleanedQty_<?=$m?>_<?=$i?>" type="text" id="cleanedQty_<?=$m?>_<?=$i?>" value="<?=$cleanedQty;?>" size="4" style="text-align:right; border:none;" autoComplete="off" readonly>
				<input name="hidCleanedQty_<?=$m?>_<?=$i?>" type="hidden" id="hidCleanedQty_<?=$m?>_<?=$i?>" size="4" style="text-align:right;" value="<?=$editCleanedQty?>">
			</td>
			<td style="padding-left:5px; padding-right:5px;">
			<? if ($checked!="" ) {?> 
				<img src="images/y.gif">
			<? }?>
				<input name="fixedQtyChk_<?=$m?>_<?=$i?>" type="hidden" id="fixedQtyChk_<?=$m?>_<?=$i?>" value="<?=$fixedQtyChk?>" size="4">
			</td>
			<?if ($fixedQtyChk!="") {?>
			<td style="padding-left:5px; padding-right:5px;">
			<?if ($fixedQtyChk!='N' ) {?> 
				<? 
					if ($p["fixedQty_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $fixedQty=$p["fixedQty_".$m."_".$i];
				?>
				<input name="fixedQty_<?=$m?>_<?=$i?>" type="text" id="fixedQty_<?=$m?>_<?=$i?>" value="<?=$fixedQty;?>" size="4" style="text-align:right;" onkeyup="comboProductConversionIngProportion();calcCmbMtxProductConversionRatePerBatch();" onchange="callFunCalc();">
			<?}?>
			</td>
			<? }?>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;display:none;" align="right" nowrap>
				<? 
					if ($p["percentagePerBatch_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $percentagePerBatch=$p["percentagePerBatch_".$m."_".$i];
				?>
				<input type="text" name="percentagePerBatch_<?=$m?>_<?=$i?>" id="percentagePerBatch_<?=$m?>_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;display:none;" align="right">
				<input type="hidden" name="lastPrice_<?=$m?>_<?=$i?>" id="lastPrice_<?=$m?>_<?=$i?>" value="<?=$lastPrice?>">
				<? 
					if ($p["ratePerBatch_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $ratePerBatch=$p["ratePerBatch_".$m."_".$i];
				?>
				<input type="text" name="ratePerBatch_<?=$m?>_<?=$i?>" id="ratePerBatch_<?=$m?>_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$ratePerBatch?>" size="5">
			</td>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;display:none;" align="right">
				<? 
					if ($p["ingGmsPerPouch_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $ingGmsPerPouch=$p["ingGmsPerPouch_".$m."_".$i];
				?>
				<input type="text" name="ingGmsPerPouch_<?=$m?>_<?=$i?>" id="ingGmsPerPouch_<?=$m?>_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$ingGmsPerPouch?>" size="5">
			</td>
                        <td class="listing-item" style="padding-left:5px; padding-right:5px;display:none;" align="right" nowrap>
				<? 
					if ($p["percentageWtPerPouch_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $percentageWtPerPouch=$p["percentageWtPerPouch_".$m."_".$i];
				?>
				<input type="text" name="percentageWtPerPouch_<?=$m?>_<?=$i?>" id="percentageWtPerPouch_<?=$m?>_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$percentageWtPerPouch?>" size="5">%
			</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;display:none;" align="right">
					<? 
						if ($p["ratePerPouch_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $ratePerPouch=$p["ratePerPouch_".$m."_".$i];
					?>
					<input type="text" name="ratePerPouch_<?=$m?>_<?=$i?>" id="ratePerPouch_<?=$m?>_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$ratePerPouch?>" size="5">
				</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;display:none;" align="right" nowrap>
					<? 
						if ($p["percentageCostPerPouch_".$m."_".$i]!="" && $p["hidRMCodeId_".$i]==$p["rMCodeId_".$i]) $percentageCostPerPouch=$p["percentageCostPerPouch_".$m."_".$i];
					?>
					<input type="text" name="percentageCostPerPouch_<?=$m?>_<?=$i?>" id="percentageCostPerPouch_<?=$m?>_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$percentageCostPerPouch?>" size="5">%
				</td>	
                                 </tr>
				<?
					}
				?>
       </table>
	<input type='hidden' name='hidItemCount_<?=$i?>' id='hidItemCount_<?=$i?>' value="<?=$m;?>">
	<input type="hidden" name="totalFixedFishQty_<?=$i?>" id="totalFixedFishQty_<?=$i?>" value="<?=$totalFixedFishQty?>">
	<input type="hidden" name="hidProductGmsPerPouch_<?=$i?>" id="hidProductGmsPerPouch_<?=$i?>" value="<?=$selProductGmsPerPouch?>">
	<input type="hidden" name="productCategory_<?=$i?>" id="productCategory_<?=$i?>" value="<?=$productCategory?>">
	<input type="hidden" name="productState_<?=$i?>" id="productState_<?=$i?>" value="<?=$productState?>">
	<input type="hidden" name="productGroup_<?=$i?>" id="productGroup_<?=$i?>" value="<?=$productGroup?>">	
	<input type="hidden" name="hidProductCode_<?=$i?>" id="hidProductCode_<?=$i?>" value="<?=$proCode?>">	
	<input type="hidden" name="hidProductName_<?=$i?>" id="hidProductName_<?=$i?>" value="<?=$proName?>">	
	</TD></tr>
		</table>
	</TD>
</tr>
<? }?>
<!--  Ing List End-->
	<tr style="display:none;">
		<td class="fieldName" nowrap >*Net Wt</td>
		<td>
		<? 
			if ($addMode!="") $netWt = "";	
			if ($p["netWt_".$i]!="") $netWt = $p["netWt_".$i]; 
		?>
		<input type="text" name="netWt_<?=$i?>" id="netWt_<?=$i?>" size="6" value="<?=$netWt?>" style="text-align:right" onkeyup="getComboMatrixMixProduct();">
		</td>
	</tr>
	<tr style="display:none;">
		<td class="fieldName" nowrap >*Fixed Wt</td>
		<td class="listing-item">
		<? 
			if ($addMode!="") $fishWt = "";
			if ($p["fishWt_".$i]!="") $fishWt = $p["fishWt_".$i]; 
		?>
		<input type="text" name="fishWt_<?=$i?>" id="fishWt_<?=$i?>" size="6" value="<?=$fishWt?>" style="text-align:right" onkeyup="getComboMatrixMixProduct();">
		</td>
	</tr>					
	<tr style="display:none;">
		<td class="fieldName" nowrap >Gravy Wt</td>
		<td class="listing-item">
		<? 
			if ($addMode!="") $gravyWt = "";
			if ($p["gravyWt_".$i]!="") $gravyWt = $p["gravyWt_".$i]; 
		?>
		<input type="text" name="gravyWt_<?=$i?>" id="gravyWt_<?=$i?>" size="6" value="<?=$gravyWt?>" style="text-align:right" readonly>
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >% of Seafood</td>
		<td class="listing-item" nowrap>
		<? 
			if ($addMode!="") $percentSeafood = "";
			if ($p["percentSeafood_".$i]!="") $percentSeafood = $p["percentSeafood_".$i]; 
		?>
		<input type="text" name="percentSeafood_<?=$i?>" id="percentSeafood_<?=$i?>" size="6" value="<?=$percentSeafood?>" style="text-align:right" readonly>&nbsp;%
		</td>
	</tr>	
	<tr>
		<td class="fieldName" nowrap >*No of Batches</td>
		<td class="listing-item">
		<? 
			if ($addMode!="") $noOfBatches = "";
			if ($p["noOfBatches_".$i]!="") $noOfBatches = $p["noOfBatches_".$i]; 
		?>
		<input type="text" name="noOfBatches_<?=$i?>" id="noOfBatches_<?=$i?>" size="6" value="<?=$noOfBatches?>" onkeyup="getComboMatrixMixProduct();">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Batch Size</td>
		<td class="listing-item">
		<? 
			if ($addMode!="") $batchSize = "";
			if ($p["batchSize_".$i]!="") $batchSize = $p["batchSize_".$i]; 
		?>
		<input type="text" name="batchSize_<?=$i?>" size="5" id="batchSize_<?=$i?>" value="<?=$batchSize?>" onkeyup="getComboMatrixMixProduct();"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Fish</td>
		<td class="listing-item">
		<? 
			if ($addMode!="") $selFishId = "";
			$selFishCost = "";
			if ($p["selFish_".$i]!="") $selFishId = $p["selFish_".$i]; 
		if ($selFishId!="") {
			$selFishCost = $productionFishCuttingObj->getFishCuttingCost($selFishId, $fccRateList);
		}
		?>
		<select name="selFish_<?=$i?>" id="selFish_<?=$i?>" onchange="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
		<option value="">-- Select --</option>	
		<?
		foreach ($fishCuttingRecords as $fcr) {
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
		<? 
			if ($addMode!="") $selProductionCodeId	= "";
			$waterCostPerBtch	= "";
			$dieselCostPerBtch	= "";
			$electricityCostPerBtch = "";
			$gasCostPerBtch 	= "";
			$maintCostPerBtch	= "";
			$variManPwerCostPerBtch	= "";
			$fixedManPowerCostPerDay = "";
			$mktgTeamCostPerPouch	= "";
			$adCostPerPouch		= "";
			$holdingCost		= "";
			$holdingDuration	= "";
			$noOfDaysInYear		= "";
			$adminOverheadChargesCost = "";
			if ($p["productionCode_".$i]!="") $selProductionCodeId = $p["productionCode_".$i]; 

	if ($selProductionCodeId!="") {		
		
		list($prodCode, $prodName, $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $boilerRequired, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch) = $productionMatrixObj->getProductionMatrixRec($selProductionCodeId);	


		$productionMatrixMaster = "PMM";	
		$pmmRateList = $manageRateListObj->latestRateList($productionMatrixMaster);
		#Producion matrix Master
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($pmmRateList);
	}
		?>
		<select name="productionCode_<?=$i?>" id="productionCode_<?=$i?>" onchange="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
		<option value="">-- Select --</option>
		<?
		foreach ($productionMatrixRecords as $pmr) {
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
<!-- Setting hidden value	 -->
<input type="hidden" name="waterCostPerBtch_<?=$i?>" id="waterCostPerBtch_<?=$i?>" value="<?=$waterCostPerBtch?>">
<input type="hidden" name="dieselCostPerBtch_<?=$i?>" id="dieselCostPerBtch_<?=$i?>" value="<?=$dieselCostPerBtch?>">
<input type="hidden" name="electricityCostPerBtch_<?=$i?>" id="electricityCostPerBtch_<?=$i?>" value="<?=$electricityCostPerBtch?>">
<input type="hidden" name="gasCostPerBtch_<?=$i?>" id="gasCostPerBtch_<?=$i?>" value="<?=$gasCostPerBtch?>">
<input type="hidden" name="maintCostPerBtch_<?=$i?>" id="maintCostPerBtch_<?=$i?>" value="<?=$maintCostPerBtch?>">
<input type="hidden" name="variManPwerCostPerBtch_<?=$i?>" id="variManPwerCostPerBtch_<?=$i?>" value="<?=$variManPwerCostPerBtch?>">
<input type="hidden" name="fixedManPowerCostPerDay_<?=$i?>" id="fixedManPowerCostPerDay_<?=$i?>" value="<?=$fixedManPowerCostPerDay?>">
<input type="hidden" name="selFishCost_<?=$i?>" id="selFishCost_<?=$i?>" value="<?=$selFishCost?>"> 
<!--input type="hidden" name="fishRatePerKgPerBatch_<?=$i?>" id="fishRatePerKgPerBatch_<?=$i?>" value="<?=$fishRatePerKgPerBatch?>"> 
<input type="hidden" name="gravyRatePerKgPerBatch_<?=$i?>" id="gravyRatePerKgPerBatch_<?=$i?>" value="<?=$gravyRatePerKgPerBatch?>"-->
<input type="hidden" name="pouchesTestPerBatchUnit" id="pouchesTestPerBatchUnit" value="<?=$pouchesTestPerBatchUnit?>">
<input type="hidden" name="mktgTeamCostPerPouch_<?=$i?>" id="mktgTeamCostPerPouch_<?=$i?>" value="<?=$mktgTeamCostPerPouch?>"> 
<input type="hidden" name="mktgTravelCost_<?=$i?>" id="mktgTravelCost_<?=$i?>" value="<?=$mktgTravelCost?>"> 
<input type="hidden" name="adCostPerPouch_<?=$i?>" id="adCostPerPouch_<?=$i?>" value="<?=$adCostPerPouch?>">
<input type="hidden" name="holdingCost" id="holdingCost" value="<?=$holdingCost?>">
<input type="hidden" name="holdingDuration" id="holdingDuration" value="<?=$holdingDuration?>">
<input type="hidden" name="noOfDaysInYear" id="noOfDaysInYear" value="<?=$noOfDaysInYear?>">
<input type="hidden" name="adminOverheadChargesCost" id="adminOverheadChargesCost" value="<?=$adminOverheadChargesCost?>">
<input type="hidden" name="profitMargin" id="profitMargin" value="<?=$profitMargin?>">
	</tr>					
	</table>
	</td>
	<!--td width="1">&nbsp;</td-->
	<!--td width="1" bgcolor="#CCCCCC">&nbsp;</td-->
	<? }?>	
	<input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$col?>">	
	</tr>
<tr><td align="center"></td></tr>
</table>
	</TD>
</tr>
<!-- No. of Product End -->
<tr>
	<TD valign="top">
	<table>
		<tr>
			<td class="fieldName" nowrap >*Freight Charges per Pack</td>
			<td class="listing-item">
			<? if ($p["freightChargePerPack"]!="") $freightChargePerPack = $p["freightChargePerPack"]; ?>
			<input type="text" name="freightChargePerPack" size="5" id="freightChargePerPack" value="<?=$freightChargePerPack?>" style="text-align:right;">					
			</td>
		</tr>
		<tr>
	  		<td class="fieldName" nowrap >*Excise Rate</td>
			<td class="listing-item" nowrap>
							<? if ($p["productExciseRate"]!="") $productExciseRate = $p["productExciseRate"]; ?>
							<input type="text" name="productExciseRate" size="5" id="productExciseRate" value="<?=$productExciseRate?>" style="text-align:right;">&nbsp;%
							</td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >PM in % of FC</td>
					  		<td class="listing-item" nowrap><input type="text" name="pmInPercentOfFc" size="5" id="pmInPercentOfFc" value="<?=$pmInPercentOfFc?>" style="text-align:right;" readonly>&nbsp;%</td>
							</tr>
							<tr>
						<td class="fieldName" nowrap >*IDEAL FACTORY COST</td>
					 	<td class="listing-item">
						<? if ($p["idealFactoryCost"]!="") $idealFactoryCost = $p["idealFactoryCost"]; ?>
						<input type="text" name="idealFactoryCost" size="5" id="idealFactoryCost" value="<?=$idealFactoryCost?>" style="text-align:right;" onkeyup="findComboProdContingency();">
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
	</TD>
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
</tr>
	<tr>
		<TD colspan="3">
			<table align="center">
				<TR>
					<TD>
					<!--fieldset>
<iframe 
src ="ComboMatrix_Product.php?" width="600" frameborder="0" height="300" marginwidth="2"></iframe>
					</fieldset-->
					</TD>
				</TR>
			</table>
		</TD>
	</tr>						
	</table></td>
					</tr>
					<tr>
						<td colspan="2"  height="10" ></td>
					</tr>
					<tr>
	<? if($editMode){?>
	<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ComboMatrix.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateComboMatrix(document.frmComboMatrix);">
	</td>
	<?} else{?>
	<td  colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ComboMatrix.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateComboMatrix(document.frmComboMatrix);">		
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Combo Matrix  </td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$comboMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintComboMatrix.php',700,600);"><? }?></td>
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
		if ($comboMatrixRecordSize) {
			$i	=	0;
		?>
		<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="7" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ComboMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ComboMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ComboMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Basic Man Fact Cost</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Profit Margin</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Ideal Fact Cost</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
		$basicManufactCost ="";
		while ($pmr=$comboMatrixResultSetObj->getRow()) {
			$i++;
			$comboMatrixRecId 	= $pmr[0];
			$productCode		= $pmr[1];
			$productName		= $pmr[2];
			$basicManufactCost	= $pmr[17];
			$comboProdProfitMargin  = $pmr[11];
			$comboIdealFactCost	= $pmr[8];
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$comboMatrixRecId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$productCode?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$productName?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$basicManufactCost?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$comboProdProfitMargin?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$comboIdealFactCost?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$comboMatrixRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange'); this.form.action='ComboMatrix.php';" ></td>
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
		<td colspan="7" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ComboMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ComboMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ComboMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</table></td>
	</tr>
	<tr>
	<td colspan="3" height="5">	
</td>
	</tr>
	<tr>	
		<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
		<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$comboMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintComboMatrix.php',700,600);"><? }?></td>
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
				<!-- Form fields end   -->
			</td>
		</tr>	
<input type="hidden" name="hidIngRateList" id="hidIngRateList" value="<?=$selRateList?>">
		<tr>
			<td height="10"></td>
		</tr>		
	</table>
	<? if ($addMode || $editMode) {?>
	<script>
	// Combo Matrix Calculation
getComboMatrixMixProduct();
calcCmbMtxProductConversionRatePerBatch();
	//callFunCalc();
	</script>
	<? }?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
