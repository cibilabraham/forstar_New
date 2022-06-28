<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$userId		=	$sessObj->getValue("userId");	
	$dateSelection = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

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
	if ($p["cmdCancel"]!="") $addMode = false;

	#Re-Setting the Values
	if ($p["batchNo"]!="") $batchNo = $p["batchNo"];
	if ($p["selProduct"]!=$p["hidSelProduct"]) $p["productGmsPerPouch"]="";
	if ($p["selProduct"]!="") $selProduct = $p["selProduct"];
	if ($p["productGmsPerPouch"]!="") $productGmsPerPouch = $p["productGmsPerPouch"];
	//if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch = $p["fishGmsPerPouch"];
	if ($p["pouchPerBatch"]!="") $pouchPerBatch = $p["pouchPerBatch"];
	if ($p["phFactorValue"]!="") $phFactorValue = $p["phFactorValue"];
	if ($p["foFactorValue"]!="") $foFactorValue = $p["foFactorValue"];

	# Check Whether Product Group Exist
	if ($selProduct) $productGroupExist = $productBatchObj->checkProductGroupExist($selProduct);

	if ($p["productGmsPerPouch"]=="" && $addMode!="" && $selProduct!="") $productGmsPerPouch = $productBatchObj->getProductNetWt($selProduct);

	if ($selProduct!="") {
		list($productCode, $productName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty) = $productMasterObj->getProductRec($selProduct);
	}

	# Add
	if ($p["cmdAdd"]!="" ) {	
		$itemCount 	= $p["hidItemCount"];
		$batchNo 	= $p["batchNo"];
		$selProduct 	= $p["selProduct"];

		$productGmsPerPouch 	= $p["productGmsPerPouch"];		
	 	$pouchPerBatch 		= $p["pouchPerBatch"];

		$startTimeHour		= $p["startTimeHour"];
		$startTimeMints		= $p["startTimeMints"];
		$startTimeOption 	= $p["startTimeOption"];
		$startTime		= $p["startTimeHour"]."-".$p["startTimeMints"]."-".$p["startTimeOption"];

		$stopTimeHour		= $p["stopTimeHour"];
		$stopTimeMints		= $p["stopTimeMints"];
		$stopTimeOption 	= $p["stopTimeOption"];
		$stopTime		= $p["stopTimeHour"]."-".$p["stopTimeMints"]."-".$p["stopTimeOption"];

		$phFactorValue		= ($p["phFactorValue"]=="")?0:$p["phFactorValue"];
		$foFactorValue		= ($p["foFactorValue"]=="")?0:$p["foFactorValue"];


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
		

		if ($batchNo!="" && $selProduct!="") {
			$productBatchRecIns = $productBatchObj->addProductBatch($batchNo, $selProduct, $productGmsPerPouch, $pouchPerBatch, $userId, $startTime, $stopTime, $phFactorValue, $foFactorValue, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty);

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
				$ingType		= $p["ingType_".$i];
				$currentStock = "";
				if ($ingType=='ING') {
					# Update the Current Stock Qty
					$totalQty = $productBatchObj->getTotalStockQty($ingredientId);
					$currentStock = $totalQty - $quantity;
					# Update the Stock
					$updateStockQty = $productBatchObj->updateBalanceStockQty($ingredientId, $currentStock);
				} else if ($ingType=='SFP') {
					# Semi-Finished actual Qty
					$semiFinishStkQty  = $semiFinishProductObj->getSemiFinishedActualQty($ingredientId);
					$totalSemiFinishStk = $semiFinishStkQty-$quantity;
					/*
						All the product used for making semi product will decrease the qty
					*/
					if ($totalSemiFinishStk>0) {
						$updateSemiFinishMaster = $productBatchObj->updateSemiFinishProduct($ingredientId, $totalSemiFinishStk);	
					} else {	# Update in all Ings
						$updateSemiFinishStkQty = $productBatchObj->updateSemiFinishStkQty($ingredientId, $quantity);	
					}
				} else {
					$currentStock = 0;
				}

				if ($fixedQtyChk=='Y') {
					$fQty += $fixedQty; //Find the sum of fixed qty
				}

				if ($lastId!="" && $ingredientId!="" && $quantity!="") {
					$ingredientRecIns = $productBatchObj->addIngredientRec($lastId, $ingredientId, $quantity, $fixedQtyChk, $currentStock, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType);
				}				
			}
			#Update Fixed Qty
			if ($fQty) {
				$updateFixedQty = $productBatchObj->updateBatchFixedQty($lastId, $fQty);
			}
		}

		if ($productBatchRecIns) {
			$addMode = false;
			$sessObj->createSession("displayMsg",$msg_succAddProductBatch);
			$sessObj->createSession("nextPage",$url_afterAddProductBatch.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddProductBatch;
		}
		$productBatchRecIns		=	false;
	}
	
	
	# Edit a Record
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$productBatchRec	=	$productBatchObj->find($editId);
		
		$editProductBatchId	=	$productBatchRec[0];
		$batchNo 		=  $productBatchRec[1];

		if ($p["editSelectionChange"]=='1' || $p["selProduct"]=="") {
			$selProduct 	=  $productBatchRec[2];
		} else {
			$selProduct 	=  $p["selProduct"];
		}

		$productGmsPerPouch 	= $productBatchRec[5];	 	
		$pouchPerBatch 		= $productBatchRec[6];

		$startTime		=	explode("-", $productBatchRec[7]);
		$startTimeHour		=	$startTime[0];
		$startTimeMints		=	$startTime[1];
		$startTimeOption 	= 	$startTime[2];

		$stopTime		=	explode("-", $productBatchRec[8]);
		$stopTimeHour		=	$stopTime[0];
		$stopTimeMints		=	$stopTime[1];
		$stopTimeOption 	= 	$stopTime[2];

		$phFactorValue		= $productBatchRec[9];
		$foFactorValue		= $productBatchRec[10];

		////////
		$productRatePerPouch 	= $productBatchRec[11];
		$fishRatePerPouch	= $productBatchRec[12];
		$gravyRatePerPouch	= $productBatchRec[13];
		$productGmsPerPouch	= $productBatchRec[14];
		$fishGmsPerPouch	= $productBatchRec[15];
		$gravyGmsPerPouch	= $productBatchRec[16];
		$productPercentagePerPouch = $productBatchRec[17];
		$fishPercentagePerPouch	= $productBatchRec[18];
		$gravyPercentagePerPouch = $productBatchRec[19];
		$productRatePerKgPerBatch = $productBatchRec[20];
		$fishRatePerKgPerBatch 	= $productBatchRec[21];
		$gravyRatePerKgPerBatch = $productBatchRec[22];
		$pouchPerBatch		= $productBatchRec[23];
		$productRatePerBatch	= $productBatchRec[24];
		$fishRatePerBatch	= $productBatchRec[25];
		$gravyRatePerBatch	= $productBatchRec[26];
		$productKgPerBatch	= $productBatchRec[27];
		$fishKgPerBatch		= $productBatchRec[28];
		$gravyKgPerBatch	= $productBatchRec[29];
		$productRawPercentagePerPouch = $productBatchRec[30];
		$fishRawPercentagePerPouch = $productBatchRec[31];
		$gravyRawPercentagePerPouch = $productBatchRec[32];
		$productKgInPouchPerBatch = $productBatchRec[33];
		$fishKgInPouchPerBatch	= $productBatchRec[34];
		$gravyKgInPouchPerBatch	= $productBatchRec[35];
		$fishPercentageYield	= $productBatchRec[36];
		$gravyPercentageYield 	= $productBatchRec[37];
		$totalFixedFishQty	= $productBatchRec[38];
		/*****/

		$productBatchRecs = $productBatchObj->fetchAllStockItem($editProductBatchId);
		#Check Whether Product Group Exist
		if ($selProduct) $productGroupExist = $productBatchObj->checkProductGroupExist($selProduct);
	}


	#Update A Record
	if ($p["cmdSaveChange"]!="") {
		
		$productBatchId		= $p["hidProductBatchId"];
		$itemCount		= $p["hidItemCount"];
		$batchNo 		= $p["batchNo"];
		$selProduct 		= $p["selProduct"];
		$productGmsPerPouch 	= $p["productGmsPerPouch"];
	 	$pouchPerBatch 		= $p["pouchPerBatch"];
		$startTimeHour		= $p["startTimeHour"];
		$startTimeMints		= $p["startTimeMints"];
		$startTimeOption 	= $p["startTimeOption"];
		$startTime		= $p["startTimeHour"]."-".$p["startTimeMints"]."-".$p["startTimeOption"];

		$stopTimeHour		= $p["stopTimeHour"];
		$stopTimeMints		= $p["stopTimeMints"];
		$stopTimeOption 	= $p["stopTimeOption"];
		$stopTime		= $p["stopTimeHour"]."-".$p["stopTimeMints"]."-".$p["stopTimeOption"];

		$phFactorValue		= ($p["phFactorValue"]=="")?0:$p["phFactorValue"];
		$foFactorValue		= ($p["foFactorValue"]=="")?0:$p["foFactorValue"];

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

		if ($productBatchId!="" && $batchNo!="" && $selProduct!="") {
			$productBatchRecUptd = $productBatchObj->updateProductBatch($productBatchId, $batchNo, $selProduct, $productGmsPerPouch, $pouchPerBatch, $startTime, $stopTime, $phFactorValue, $foFactorValue, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty);
		
			#Delete First all records from Entry table
			$deleteIngredientItem = $productBatchObj->deleteIngredientRecs($productBatchId);

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
				$ingType		= $p["ingType_".$i];

				if ($fixedQtyChk=='Y') {
					$fQty += $fixedQty; //Find the sum of fixed qty
				}
				$currentStock = 0;
				if ($ingType=='ING') { 
					# Update the Current Stock Qty
					$totalQty = $productBatchObj->getTotalStockQty($ingredientId);
					$stockQty = $quantityAlreadyIssued-$quantity;
					$currentStock = $totalQty + $stockQty;
				} else if ($ingType=='SFP') {					
					$semiFinishStkQty  = $semiFinishProductObj->getSemiFinishedActualQty($ingredientId);
					$stockQty = $quantityAlreadyIssued-$quantity;
					$currentStock = $semiFinishStkQty + $stockQty;
				}

				if ($productBatchId!="" && $ingredientId!="" && $quantity!="") {
					$ingredientRecIns = $productBatchObj->addIngredientRec($productBatchId, $ingredientId, $quantity, $fixedQtyChk, $currentStock, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType);
					
					# Update the Stock
					$stockQty = $quantityAlreadyIssued-$quantity;
					if ($quantity!=$quantityAlreadyIssued && $ingType=='ING') {
						$updateStockQty = $productBatchObj->updateStockQty($ingredientId, $stockQty);
					} else if ($quantity!=$quantityAlreadyIssued && $ingType=='SFP') {
						$updateSemiProuctRec = $productBatchObj->updateSemiProductStockQty($ingredientId, $stockQty);
					}
				}
			}
			#Update Fixed Qty
			if ($fQty) {
				$updateFixedQty = $productBatchObj->updateBatchFixedQty($productBatchId, $fQty);
			}
		}
		if ($productBatchRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductBatchUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductBatch.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductBatchUpdate;
		}
		$productBatchRecUptd	=	false;
	}

	# Delete a Record
	/*
		When delete semi finished product ing will decrease
	*/
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$productBatchId = $p["delId_".$i];
			
			if ($productBatchId!="") {
				//Delete From main Table ( and update stk qty)
				$productBatchRecDel   = $productBatchObj->deleteProductBatch($productBatchId);
				//Delete From Entry Table
				$deleteIngredientItem = $productBatchObj->deleteIngredientRecs($productBatchId);
			}
		}
		if ($productBatchRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductBatch);
			$sessObj->createSession("nextPage",$url_afterDelProductBatch.$dateSelection);
		} else {
			$errDel	=	$msg_failDelProductBatch;
		}
		$productBatchRecDel	=	false;
	}

	$selRateList = $ingredientRateListObj->latestRateList();
	# Fetch all Item
	if ($selProduct) $productRecs = $productBatchObj->fetchAllIngredients($selProduct, $selRateList);

	// $productRecs = $productMasterObj->fetchAllIngredients($selProduct);
	if ($addMode || $editMode) {
		#List all Product Records (Not Base Product)
		//$productMasterRecords = $productBatchObj->fetchAllProductMatrixRecords(); 
		$productMasterRecords = $productBatchObj->fetchAllProductMatrixRecordsActiveProducts(); 
		// Base Product Records
		// $productMasterRecords = $productMasterObj->fetchAllRecords(); 
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
		$productBatchRecords = $productBatchObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$productBatchRecSize = sizeof($productBatchRecords);

		#Pagination
		$fetchAllProductBatchRecs = $productBatchObj->fetchDateRangeRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllProductBatchRecs);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Product Batch	
	//$proBatchRecords = $productBatchObj->fetchAllRecords();
	

	if ($editMode)	$heading = $label_editProductBatch;
	else 		$heading = $label_addProductBatch;
	
	$ON_LOAD_PRINT_JS	= "libjs/ProductBatch.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
		<form name="frmProductBatch" action="ProductBatch.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductBatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductBatch(document.frmProductBatch);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductBatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductBatch(document.frmProductBatch);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidProductBatchId" value="<?=$editProductBatchId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
					<table width="200">
						<tr>
                                                  <td class="fieldName" nowrap>*Batch No: </td>
                                                  <td class="listing-item">
													<input name="batchNo" type="text" id="batchNo" value="<?=$batchNo?>" size="6">
												  </td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Product:</td>
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
						<tr>
                                                	<TD nowrap colspan="2">
							<table cellpadding="0" cellspacing="0">
								<TR>
									<TD class="fieldName" nowrap>Start Time&nbsp;&nbsp;</TD>
									<TD><table cellpadding="0" cellspacing="0">
					<TR>
					<td nowrap="nowrap">
				  <?
				  if ($addMode==true) {
				  	if($p["startTimeHour"]!="") $startTimeHour		=	$p["startTimeHour"];
				}
				 
				 if ($startTimeHour=="") {
				  	$startTimeHour		=	date("g");
				  }
				  ?>
				  <input type="text" id="startTimeHour" name="startTimeHour" size="1" value="<?=$startTimeHour;?>" onchange="return productBatchTimeCheck('H','startTimeHour');" style="text-align:center;" maxlength="2">:
				<?
				if ($addMode==true) {
			  		if($p["startTimeMints"]!="") $startTimeMints	=	$p["startTimeMints"];
			  	}
			  	if($startTimeMints=="") {
				  	$startTimeMints		=	date("i");
				}
				 
				?>
				    <input type="text" id="startTimeMints" name="startTimeMints" size="1" value="<?=$startTimeMints;?>" onchange="return productBatchTimeCheck('M','startTimeMints');" style="text-align:center;" maxlength="2">
				  <?
					if ($addMode==true) {
						if($p["startTimeOption"]!="") $startTimeOption = $p["startTimeOption"];
				  	}
					if($startTimeOption=="") {
						$startTimeOption = date("A");
					}
				  ?>
                    	<select name="startTimeOption" id="startTimeOption">
				<option value="AM" <? if($startTimeOption=='AM') echo "selected"?>>AM</option>
				<option value="PM" <? if($startTimeOption=='PM') echo "selected"?>>PM</option>
                    	</select></td>
					<td></td>
					<td class="fieldName" nowrap>&nbsp;&nbsp;End Time&nbsp;&nbsp;</td>
					<td><table cellpadding="0" cellspacing="0">
					<TR>
					<td nowrap="nowrap">
				  <?
				  if ($addMode==true) {
				  	if($p["stopTimeHour"]!="") $stopTimeHour   =	$p["stopTimeHour"];
				}
				 
				 if ($stopTimeHour=="") {
				  	$stopTimeHour		=	date("g");
				  }
				  ?>
				  <input type="text" id="stopTimeHour" name="stopTimeHour" size="1" value="<?=$stopTimeHour;?>" onchange="return productBatchTimeCheck('H','stopTimeHour');" style="text-align:center;" maxlength="2">:
				<?
				if ($addMode==true) {
			  		if($p["stopTimeMints"]!="") $stopTimeMints = $p["selectTimeMints"];
			  	}
			  	if($stopTimeMints=="") {
				  	$stopTimeMints		=	date("i");
				}
				 
				?>
				    <input type="text" id="stopTimeMints" name="stopTimeMints" size="1" value="<?=$stopTimeMints;?>" onchange="return productBatchTimeCheck('M','stopTimeMints');" style="text-align:center;" maxlength="2">
				  <?
					if ($addMode==true) {
						if($p["stopTimeOption"]!="") $stopTimeOption = $p["stopTimeOption"];
				  	}
					if($stopTimeOption=="") {
						$stopTimeOption = date("A");
					}
				  ?>
                    	<select name="stopTimeOption" id="stopTimeOption">
				<option value="AM" <? if($stopTimeOption=='AM') echo "selected"?>>AM</option>
				<option value="PM" <? if($stopTimeOption=='PM') echo "selected"?>>PM</option>
                    	</select></td>
					</TR>
				</table></td>
					</TR>
				</table></TD>
								</TR>
							</table>
							</TD>
						</tr>
						<? if (!$productGroupExist) {?>
						<tr>
                                                	<TD class="fieldName" nowrap>PH Factor Value:</TD>
                                                        <TD>
							<input name="phFactorValue" type="text" id="phFactorValue" value="<?=$phFactorValue?>" size="6" style="text-align:right;">
							</TD>
						</tr>
						<? } else {?>
						<tr>
                                                	<TD class="fieldName" nowrap>F0 Factor Value:</TD>
                                                        <TD>
							<input name="foFactorValue" type="text" id="foFactorValue" value="<?=$foFactorValue?>" size="6" style="text-align:right;">
							</TD>
						</tr>
						<? }?>
                                                          </table></td>
				  </tr>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD>
						<table>
							<TR><TD>
							<table>
							<TR><TD height="25"></TD></TR>
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
						<input type="text" size="6" style="text-align:right; border:none;background-color:lightblue;" name="productGmsPerPouch" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" onkeyup="getProductRatePerBatch();" autoComplete="off" readonly></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? //if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch=$p["fishGmsPerPouch"];?>
						<input type="text" size="4" style="text-align:right;background-color:lightblue; border:none;" name="fishGmsPerPouch" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" onkeyup="getProductRatePerBatch();" autoComplete="off" readonly></TD>
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
						<input type="text" size="6" style="text-align:right;border:none;background-color:lightblue;" name="pouchPerBatch" id="pouchPerBatch" value="<?=$pouchPerBatch?>" onkeyup="getProductRatePerBatch();" autoComplete="off" readonly>
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
						<table>
						<tr>
							<TD></TD>
							<TD class="listing-head" align="center">For pouches</TD>
							<TD></TD>
							<TD nowrap class="listing-head" align="center">For Fixed</TD>
						</tr>
						<TR>
							<td width="100"></td>
							<td valign="top">
					<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2" align="center">
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Fixed</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Gravy</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="orange"><strong><div id="pouchesProductRatePerPouch"></div></strong></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishRatePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyRatePerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="6" style="text-align:right;" name="pouchesProductGmsPerPouch" id="pouchesProductGmsPerPouch" onkeyup="calcProductBatchForPouch();" value="<?=$productGmsPerPouch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<input type="text" size="6" style="text-align:right;" bgcolor="lightblue" name="pouchesFishGmsPerPouch" id="pouchesFishGmsPerPouch" onkeyup="calcProductBatchForPouch();" value="<?=$fishGmsPerPouch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyGmsPerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap><div id="pouchesFishPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyPercentagePerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductRatePerKgPerbatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishRatePerKgPerbatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyRatePerKgPerbatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="6" style="text-align:right;" name="pouchesPerBatch" id="pouchesPerBatch" onkeyup="calcProductBatchForPouch();" value="<?=$pouchPerBatch?>"> </TD>
						<TD class="listing-item" colspan="2"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductRatePerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishRatePerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyRatePerBatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductKgPerbatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishKgPerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyKgPerBatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<div id="pouchesProductRawPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<div id="pouchesFishRawPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<div id="pouchesGravyRawPercentagePerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductKgInPouchPerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishKgInPouchPerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyKgInPouchPerBatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishPercentageYield"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyPercentageYield"></div></TD>
					</TR>
					</table>
					</td>
					<td></td>
					<!-- FOR FISH STARTS HERE -->
					<td valign="top">
					<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2" align="center">
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Fixed</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Gravy</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="orange"><strong><div id="productRatePerPouchForFish"></div></strong></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRatePerPouchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyRatePerPouchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<input type="text" size="6" style="text-align:right;" name="productGmsPerPouchForFish" id="productGmsPerPouchForFish" onkeyup="calcProductBatchForFish();" value="<?=$productGmsPerPouch?>">
						</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<input type="text" size="6" style="text-align:right;" bgcolor="lightblue" name="fishGmsPerPouchForFish" id="fishGmsPerPouchForFish" onkeyup="calcProductBatchForFish();" value="<?=$fishGmsPerPouch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyGmsPerPouchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productPercentagePerPouchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishPercentagePerPouchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyPercentagePerPouchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productRatePerKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRatePerKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyRatePerKgPerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchPerBatchForFish"></div></TD>
						<TD class="listing-item" colspan="2"></TD>
						<!--<TD class="listing-item"></TD>-->
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productRatePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRatePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"> <div id="gravyRatePerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyKgPerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productRawPercentagePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRawPercentagePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyRawPercentagePerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productKgInPouchPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="6" style="text-align:right;" name="fishKgInPouchPerBatchForFish" id="fishKgInPouchPerBatchForFish" onkeyup="calcProductBatchForFish();" value="<?=$fishKgInPouchPerBatch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyKgInPouchPerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishPercentageYieldForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyPercentageYieldForFish"></div></TD>
					</TR>
				</table></td>
						</TR>
					</table>
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
				<td></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Kg/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs./<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Wt/<br>Pouch</td>
				<td></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Kg/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs./<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Wt/<br>Pouch</td>
                              </tr>
				<?php
			 	foreach ($productRecs as $pr)	{
					$j++;
					$ingredientId	= $pr[2];
					/*$ingredientRec	= $ingredientMasterObj->find($ingredientId);
					$ingredientName	= stripSlash($ingredientRec[2]);			
					*/
					//$ingredientName	= $pr[12];
					//$lastPrice  	= $pr[13];
					$selIngType = $pr[13];
					$ingredientName = "";
					$lastPrice 	= 0;
					$existingQty	= 0;
					$ingredientName = "";
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

					
					
					$checked = "";
					if ($fixedQtyChk=='Y') $checked= "Checked";
					
				?>
                                <tr bgcolor="#FFFFFF" align="center">
                                	<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="left"><?=$ingredientName?>
						<input type="hidden" value="<?=$ingredientId?>" name="ingredientId_<?=$j?>" id="ingredientId_<?=$j?>">
						<input type="hidden" name="ingType_<?=$j?>" id="ingType_<?=$j?>" value="<?=$selIngType?>">
					</td>						
                                        <td style="padding-left:2px; padding-right:2px;">
						<input name="quantity_<?=$j?>" type="text" id="quantity_<?=$j?>" size="4" style="text-align:right;" value="<?=$quantity?>" onkeyup="getProductRatePerBatch();calcProductBatchForPouch();calcProductBatchForFish();">
						<input name="hidQuantity_<?=$j?>" type="hidden" id="hidQuantity_<?=$j?>" size="4" style="text-align:right;" value="<?=$quantity?>">
					</td>
					<td style="padding-left:2px; padding-right:2px;">
						<input name="cleanedQty_<?=$j?>" type="text" id="cleanedQty_<?=$j?>" value="<?=$cleanedQty;?>" size="4" style="text-align:right;" autoComplete="off">
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
						<input name="fixedQty_<?=$j?>" type="text" id="fixedQty_<?=$j?>" value="<?=$fixedQty;?>" size="4" style="text-align:right" onkeyup="getProductRatePerBatch();">
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
				<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="right">
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
				<td></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesKgPerBatch_<?=$j?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesRatePerBatch_<?=$j?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap><div id="pouchesWtPerBatch_<?=$j?>"></div></td>
					<td></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="kgPerBatchForFish_<?=$j?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="ratePerBatchForFish_<?=$j?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap><div id="wtPerBatchForFish_<?=$j?>"></div></td>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductBatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductBatch(document.frmProductBatch);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductBatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductBatch(document.frmProductBatch);">&nbsp;&nbsp;												</td>
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Product Batch   </td>
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
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productBatchRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductBatch.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
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
	if (sizeof($productBatchRecords)>0) {
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
      				$nav.= " <a href=\"ProductBatch.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductBatch.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductBatch.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Batch No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
		<?
		foreach ($productBatchRecords as $pbr) {
			$i++;
			$productBatchId	= $pbr[0];
			$batchNo	= $pbr[1];
			$productName	= $pbr[5];
																	$dateS		= explode("-",$pbr[3]);
			$createdDate	= $dateS[2]."/".$dateS[1]."/".$dateS[0];
		?>
		<tr  bgcolor="WHITE">
			<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productBatchId;?>" class="chkBox"></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$batchNo;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
			<td class="listing-item" width="60" style="padding-left:10px; padding-right:10px;"><?=$createdDate?></td>
			<? if($edit==true){?>
			<td class="listing-item" width="60" align="center">			
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productBatchId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ProductBatch.php';"></td>
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
         	<td colspan="5" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductBatch.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductBatch.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductBatch.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
	<input type="hidden" name="productGroupExist" value="<?=$productGroupExist?>">
	<input type="hidden" name="hidProductGmsPerPouch" id="hidProductGmsPerPouch" value="<?=$productGmsPerPouch?>">
	<input type="hidden" name="totalFixedFishQty" id="totalFixedFishQty" value="<?=$totalFixedFishQty?>">	
	</td>
	</tr>
	<tr>	
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
			<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productBatchRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductBatch.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
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

	<? if ($addMode || $editMode) {?>
	<script>
	// calc rate
	getProductRatePerBatch();
	// For Pouches
	calcProductBatchForPouch();
	//For Fish
	calcProductBatchForFish();
	</script>	
	<? }?>
	</form>
	
	</td>
	</tr>	
	</table>

<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
