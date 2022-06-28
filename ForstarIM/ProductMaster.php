<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once('lib/ProductMaster_ajax.php');
	
	$_SESSION['rownum'] = '';

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
	if ($p["selProduct"]!="") $selProductId	= $p["selProduct"];
	if ($p["productCode"]!="") $productCode = $p["productCode"];
	if ($p["productName"]!="") $productName	= $p["productName"];
	if ($p["productCategory"]!="") $productCategory	= $p["productCategory"];
	if ($p["productState"]!="") $productState = $p["productState"];
	if ($p["productGroup"]!="") $productGroup = $p["productGroup"];
	if ($productState) $productGroupExist = $productMasterObj->checkProductGroupExist($productState);
	if ($p["gmsPerPouch"]!="") $gmsPerPouch = $p["gmsPerPouch"]; // Net Wt of the product	
	if ($p["fixedQtyCheked"]!="") $fixedQtyCheked = $p["fixedQtyCheked"];
	
	
	# Add a Product
	if ($p["cmdAdd"]!="") {

		$itemCount	= $p["hidTableRowCount"];
		$productCode	= $p["productCode"];
		$productName	= $p["productName"];

		$productCategory = $p["productCategory"];
		$productState 	 = $p["productState"];
		$productGroup 	 = ($p["productGroup"]=="")?0:$p["productGroup"];
		//$gmsPerPouch 	 = $p["gmsPerPouch"];
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

		$semiFinished		= ($p["semiFinished"]!="")?$p["semiFinished"]:N;
		$hidIngRateListId	= $p["hidIngRateListId"];

		$productRatePerKg	= $p["productRatePerKg"];
		$fishRatePerKg		= $p["fishRatePerKg"];
		$gravyRatePerKg		= $p["gravyRatePerKg"];
		
		if ($productCode!="" && $productName!="") {

			$productRecIns = $productMasterObj->addProduct($productCode, $productName, $userId, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $semiFinished, $hidIngRateListId, $productRatePerKg, $fishRatePerKg, $gravyRatePerKg);

			#Find the Last inserted Id From m_productmaster Table
			$lastId = $databaseConnect->getLastInsertedId();
			
			for ($i=0; $i<$itemCount; $i++) {
			    $status = $p["status_".$i];
			    if ($status!='N') {
				# ING - Ingredient  SFP = Semi Finished Product
				$selIngRec	= explode("_",$p["selIngredient_".$i]);
				//$ingredientId	= $p["selIngredient_".$i];
				$selIngType	= $selIngRec[0];	
				$ingredientId   = $selIngRec[1];
				$quantity	= trim($p["quantity_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];
				$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];
	
				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];
				$idealQty		= trim($p["cleanedQty_".$i]);
				$ratePerKg		= $p["ratePerKg_".$i];

				if ($lastId!="" && $ingredientId!="" && $quantity!="") {
					$productItemsIns = $productMasterObj->addIngredientEntries($lastId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $idealQty, $selIngType, $ratePerKg);
				}
			  }
		     }	
		}

		if ($productRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddProductMaster);
			$sessObj->createSession("nextPage",$url_afterAddProductMaster.$selection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddProductMaster;
		}
		$productRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" || $selProductId!="") {
		if ($selProductId) $editId = $selProductId;
		else $editId		=	$p["editId"];
		if (!$selProductId) $editMode = true;
		$productMasterRec	=	$productMasterObj->find($editId);
		$editProductId		=	$productMasterRec[0];
		$productCode		=	$productMasterRec[1];
		$productName		=	$productMasterRec[2];
		if ($p["editSelectionChange"]=='1' || $p["productCategory"]=="") {
			$productCategory	= 	$productMasterRec[3];
		} else {
			$productCategory	=	$p["productCategory"];
		}
		
		if ($p["editSelectionChange"]=='1' || $p["productState"]=="") {
			$productState	= 	$productMasterRec[4];
		} else {
			$productState	=	$p["productState"];
		}		
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
		$semiFinishedChk	= $productMasterRec[35];
		$sFinishedChk = "";
		if ($semiFinishedChk=='Y') $sFinishedChk = "checked";
		$selIngRateListId	= $productMasterRec[36];

		$productRatePerKg	= $productMasterRec[37];
		$fishRatePerKg		= $productMasterRec[38];
		$gravyRatePerKg		= $productMasterRec[39];
		/*****/
		
		//Find whether the state has a Group
		if ($productState) $productGroupExist = $productMasterObj->checkProductGroupExist($productState);

		/* Removed on 6-08-08
		$productRecs = $productMasterObj->fetchAllIngredients($editProductId);
		$rowSize = sizeof($productRecs);
		*/
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$productId = $p["hidProductId"];

		$itemCount	=	$p["hidTableRowCount"];
		$productCode	=	$p["productCode"];
		$productName	=	$p["productName"];
		$productCategory = $p["productCategory"];
		$productState 	 = $p["productState"];
		$productGroup 	 = ($p["productGroup"]=="")?0:$p["productGroup"];
		//$gmsPerPouch 	 = $p["gmsPerPouch"];
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

		$semiFinished		= ($p["semiFinished"]!="")?$p["semiFinished"]:N;
		$hidIngRateListId	= $p["hidIngRateListId"];

		$productRatePerKg	= $p["productRatePerKg"];
		$fishRatePerKg		= $p["fishRatePerKg"];
		$gravyRatePerKg		= $p["gravyRatePerKg"];
		
		if ($productId!="" && $productCode!="" && $productName!="") {
			$productMasterRecUptd = $productMasterObj->updateProductMaster($productId, $productCode, $productName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $semiFinished, $hidIngRateListId, $productRatePerKg, $fishRatePerKg, $gravyRatePerKg);
		
			#Delete First all records from Product master entry table
			$deleteIngredientItemRecs = $productMasterObj->deleteIngredientItemRecs($productId);
			
			for ($i=0; $i<$itemCount; $i++) {
			   $status = $p["status_".$i];
			    if ($status!='N') {
				//$ingredientId	=	$p["selIngredient_".$i];
				# ING - Ingredient  SFP = Semi Finished Product
				$selIngRec	= explode("_",$p["selIngredient_".$i]);				
				$selIngType	= $selIngRec[0];	
				$ingredientId   = $selIngRec[1];
				$quantity	=	trim($p["quantity_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];
				$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];

				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];
				$idealQty		= trim($p["cleanedQty_".$i]);

				$ratePerKg		= $p["ratePerKg_".$i];
					
				if ($productId!="" && $ingredientId!="" && $quantity!="") {
					$productItemsIns = $productMasterObj->addIngredientEntries($productId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $idealQty, $selIngType, $ratePerKg);
				}
			  }
			}
		}
	
		if ($productMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductMasterUpdate;
		}
		$productMasterRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productId	=	$p["delId_".$i];

			if ($productId!="" ) {
				# Checking Product used as an Ing
				$productUsedAsIng =  $productMasterObj->chkProductUsedAsIng($productId);
				
				if (!$productUsedAsIng) {
					$deleteIngredientItemRecs =	$productMasterObj->deleteIngredientItemRecs($productId);
					$productMasterRecDel = $productMasterObj->deleteProductMaster($productId);
				}				
			}
		}
		if ($productMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductMaster);
			$sessObj->createSession("nextPage",$url_afterDelProductMaster.$selection);
		} else {
			$errDel	=	$msg_failDelProductMaster;
		}
		$productMasterRecDel	=	false;
	}

if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productId	=	$p["confirmId"];
			if ($productId!="") {
				// Checking the selected fish is link with any other process
				$productRecConfirm = $productMasterObj->updateProductconfirm($productId);
			}

		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmproductMaster);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
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
				
					$productRecConfirm = $productMasterObj->updateProductReleaseconfirm($productId);
				
			}
		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmproductMaster);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------

	if ($g["selProductCategory"]!="") $selProductCategoryId = $g["selProductCategory"];
	else $selProductCategoryId = $p["selProductCategory"];

	if ($g["selProductState"]!="") $selProductStateId = $g["selProductState"];
	else $selProductStateId = $p["selProductState"];

	if ($g["selProductGroup"]!="") $selProductGroupId = $g["selProductGroup"];
	else $selProductGroupId = $p["selProductGroup"];

	if ($p["cmdSearch"]) $offset = 0;
	
	#List all Records
	$productMasterRecords = $productMasterObj->fetchAllPagingRecords($offset, $limit, $selProductCategoryId, $selProductStateId, $selProductGroupId);
	$productMasterRecordSize    = sizeof($productMasterRecords);
	
	## -------------- Pagination Settings II -------------------
	$fetchAllProductMasterRecords = $productMasterObj->fetchAllRecords($selProductCategoryId, $selProductStateId, $selProductGroupId);	// fetch All Records
	$numrows	=  sizeof($fetchAllProductMasterRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Ingredient
	if ($addMode || $editMode) {
		if ($selIngRateListId!="") $selRateList = $selIngRateListId; 
		else $selRateList = $ingredientRateListObj->latestRateList();		
		$ingredientRecords = $productMasterObj->fetchAllIngredientRecords($selRateList);		
	}

	#List all Product Category Records
	//$productCategoryRecords = $productCategoryObj->fetchAllRecords();
	$productCategoryRecords = $productCategoryObj->fetchAllRecordsActiveCategory();

	#List all Product State Records
	//$productStateRecords = $productStateObj->fetchAllRecords();
	$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();

	# Get Product Group;
	if ($selProductStateId!="") {
		# Checking Prouct Group Exist
		$prdGroupExist = $manageProductObj->checkProductGroupExist($selProductStateId);
		$prdGroupRecs =  $productMasterObj->filterProductGroupList($prdGroupExist);
	}

	if ($addMode || $editMode) {
		#List all Product Group Records
		//$productGroupRecords =$productGroupObj->fetchAllRecords();		
		$productGroupRecords =$productGroupObj->fetchAllRecordsActiveGroup();
	}

	if ($addMode) $selProductRecs = $productMasterObj->getAllPMRecs();

	//list($productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch) =$productMasterObj->getProductMasterRec($productId);

	if ($addMode) 		$mode = 1;
	else if ($editMode)	$mode = 2;
	else $mode = "";

	if ($editMode) $heading	= $label_editProductMaster;
	else	       $heading	= $label_addProductMaster;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/ProductMaster.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	/*$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else*/ 
	require("template/btopLeftNav.php");
?>
<form name="frmProductMaster" action="ProductMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<TD height="10"></TD>
		</tr>
		<tr>
			<td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product Category" onclick="parent.openTab('ProductCategory.php');">Product Category</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductState.php');">Product State</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductGroup.php');">Product Group</a></td>	
		</tr>
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
						<td   bgcolor="white">
							<!-- Form fields start -->
							<?php	
								$bxHeader="Product Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="50%" align="center">
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
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductMaster(document.frmProductMaster);">	
																					</td>
																					<?} else{?>
																					<td  colspan="4" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductMaster(document.frmProductMaster);">&nbsp;&nbsp;		
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidProductId" value="<?=$editProductId;?>">
																				<tr>
																					<td colspan="2"  height="5" ></td>
																				</tr>
																				<tr>
																					<TD colspan="4" nowrap style="padding-left:5px; padding-right:5px;">
																						<table align="center" width="50%">
																							<TR>
																								<TD>
																									<table align="center">
																										<tr>
																											<td nowrap style="padding-left:5px; padding-right:5px;" valign="top">
																												<table width="200" border="0">
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
																															<td class="fieldName">*Code : </td>
																															<td class="listing-item">
																																<input type="text" name="productCode" id="productCode" value="<?=$productCode?>" size="10" onblur="xajax_chkPCodeExist(document.getElementById('productCode').value, '<?=(!$selProductId)?$editProductId:""?>');" autocomplete="off">
																																<input type="hidden" name="hidProductCode" id="hidProductCode" value="<?=$productCode?>" size="14">
																																<input type="hidden" name="hidPCodeExist" id="hidPCodeExist" value="">
																																<span id="pcodeExist" class="err1" style="line-height:normal;" nowrap="true"></span>
																															</td>
																														</tr>
																														<tr>
																															<td class="fieldName">*Name:</td>
																															<td class="listing-item"><input type="text" name="productName" value="<?=$productName?>" size="30"></td>
																														</tr>
																														<tr>
																															<td class="fieldName">Category:</td>
																															<td class="listing-item">
																																<select name="productCategory">
																																	<option value="">--Select--</option>
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
																														</tr>
																														<? if ($productGroupExist) {?>
																														<tr>
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
																														</tr>
																														<? }?>
																													</table>
																												</td>
																												<td></td>
																												<td style="padding-left:5px; padding-right:5px;" valign="top">
																													<table cellspacing="1" cellpadding="0" border="0" class="newspaperType">
																														<thead>
																															<TR align="center">
																																<th>&nbsp;</th>
																																<th style="padding-left:5px; padding-right:5px;">Product</th>
																																<th style="padding-left:5px; padding-right:5px;">Fixed</th>
																																<th style="padding-left:5px; padding-right:5px;">Gravy</th>
																															</TR>
																														</thead>
																														<tbody>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Pouch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"  bgcolor="orange">
																																<input type="text" name="productRatePerPouch" id="productRatePerPouch" style="text-align:right;border:none; background-color:orange;font-weight:bold" readonly value="<?=$productRatePerPouch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="fishRatePerPouch" id="fishRatePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRatePerPouch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="gravyRatePerPouch" id="gravyRatePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerPouch?>" size="5"></TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow" nowrap>Qty per pouch<!--Gms per Pouch--></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
																																	<? if ($p["productGmsPerPouch"]!="") $productGmsPerPouch=$p["productGmsPerPouch"];?>
																																	<input type="text" size="5" style="text-align:right;" name="productGmsPerPouch" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" onkeyup="calcProductRatePerBatch();" autoComplete="off"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
																																	<? if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch=$p["fishGmsPerPouch"];?>
																																	<input type="text" size='6' style="text-align:right;background-color:lightblue; border:none;" name="fishGmsPerPouch" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" autoComplete="off" readonly></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="gravyGmsPerPouch" id="gravyGmsPerPouch" style="text-align:right;border:none" readonly value="<?=$gravyGmsPerPouch?>" size="5"></TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow" >% per Pouch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="productPercentagePerPouch" id="productPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productPercentagePerPouch?>" size="5">%</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
																																	<input type="text" name="fishPercentagePerPouch" id="fishPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishPercentagePerPouch?>" size="5">%</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="gravyPercentagePerPouch" id="gravyPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyPercentagePerPouch?>" size="5">%</TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="productRatePerKgPerBatch" id="productRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerKgPerBatch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="fishRatePerKgPerBatch" id="fishRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerKgPerBatch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="gravyRatePerKgPerBatch" id="gravyRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerKgPerBatch?>" size="5"></TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Pouches per Batch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
																																	<? if ($p["pouchPerBatch"]!="") $pouchPerBatch=$p["pouchPerBatch"];?>
																																	<input type="text" size="5" style="text-align:right;" name="pouchPerBatch" id="pouchPerBatch" value="<?=$pouchPerBatch?>" onkeyup="calcProductRatePerBatch();" autoComplete="off">
																																</TD>
																																<TD class="listing-item" align="center" colspan="2"></TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Kg</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="productRatePerKg" id="productRatePerKg" style="text-align:right;border:none" readonly value="<?=$productRatePerKg?>" size="5">
																																</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="fishRatePerKg" id="fishRatePerKg" style="text-align:right;border:none" readonly value="<?=$fishRatePerKg?>" size="5">
																																</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																	<input type="text" name="gravyRatePerKg" id="gravyRatePerKg" style="text-align:right;border:none" readonly value="<?=$gravyRatePerKg?>" size="5">
																																</TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Batch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="productRatePerBatch" id="productRatePerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerBatch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="fishRatePerBatch" id="fishRatePerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerBatch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="gravyRatePerBatch" id="gravyRatePerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerBatch?>" size="5"></TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="productKgPerBatch" id="productKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgPerBatch?>" size="5" onchange="calcProductRatePerBatch();"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="fishKgPerBatch" id="fishKgPerBatch" style="text-align:right;border:none" readonly value="<?=$fishKgPerBatch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><input type="text" name="gravyKgPerBatch" id="gravyKgPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyKgPerBatch?>" size="5"></TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% (Raw) per Batch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
																																<input type="text" name="productRawPercentagePerPouch" id="productRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productRawPercentagePerPouch?>" size="5">%</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
																																<input type="text" name="fishRawPercentagePerPouch" id="fishRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRawPercentagePerPouch?>" size="5">%</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
																																<input type="text" name="gravyRawPercentagePerPouch" id="gravyRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRawPercentagePerPouch?>" size="5">%</TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Kg (in Pouch) per Batch</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="productKgInPouchPerBatch" id="productKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgInPouchPerBatch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="fishKgInPouchPerBatch" id="fishKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$fishKgInPouchPerBatch?>" size="5"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="gravyKgInPouchPerBatch" id="gravyKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyKgInPouchPerBatch?>" size="5"></TD>
																															</TR>
																															<TR>
																																<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% Yield</TD>
																																<TD class="listing-item"></TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="fishPercentageYield" id="fishPercentageYield" style="text-align:right;border:none" readonly value="<?=$fishPercentageYield?>" size="5">%</TD>
																																<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
																																<input type="text" name="gravyPercentageYield" id="gravyPercentageYield" style="text-align:right;border:none" readonly value="<?=$gravyPercentageYield?>" size="5">%</TD>
																															</TR>
																														</tbody>
																													</table>
																												</td>
																											</tr>
																											<tr>
																												<td colspan="2" height="5"></td>
																											</tr>						
																											<tr>
																												<TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3">
																													<table cellspacing="1" cellpadding="3" id="tblAddIng" class="newspaperType">
																														<tr align="center" style="line-height:normal;">
																															<th style="padding-left:5px; padding-right:5px;">Ingredient</th>
																															<th nowrap style="padding-left:5px; padding-right:5px;">Raw<br> Kg</th>
																															<th nowrap style="padding-left:5px; padding-right:5px;">Cleaned<br>Kg</th>
																															<th nowrap style="padding-left:5px; padding-right:5px;">Decl.<br>Yield</th>
																															<th nowrap style="padding-left:5px; padding-right:5px;">Fixed<br>Qty</th>	
																															<th nowrap style="padding-left:5px; padding-right:5px;">Enter <br/>Fixed Qty</th>
																															<th style="padding-left:5px; padding-right:5px;">%/<br>Batch</th>
																															<th style="padding-left:5px; padding-right:5px;">Rs/<br>Kg</th>
																															<th style="padding-left:5px; padding-right:5px;">Rs/<br>Batch</th>
																															<th style="padding-left:5px; padding-right:5px;">Gms/<br>Pouch</th>
																															<th style="padding-left:5px; padding-right:5px;">%Wt/<br>Pouch</th>
																															<th style="padding-left:5px; padding-right:5px;">Rs/<br>Pouch</th>
																															<th style="padding-left:5px; padding-right:5px;">%Cost/<br>Pouch</th>
																															<th>&nbsp;</th>
																														</tr>
																														<?php
																														$rowSize = 0;	
																														if ($editProductId)  {
																															$productRecs = $productMasterObj->fetchAllIngredients($editProductId);
																															$rowSize = sizeof($productRecs);		
																														$lastPrice = 0;
																														$m = 0;
																														$displayIngredient = "";	
																														foreach ($productRecs as $rec) {			
																																$editIngredientId = $rec[2];
																																$selIngType	  = $rec[13]; 
																																$displayIngredient    = trim($selIngType.'_'.$editIngredientId);	
																																if ($selIngType=='ING') {
																																	list($lastPrice,$declYield) = $productMasterObj->getIngredientRate($editIngredientId, $selRateList);
																																} else if ($selIngType=='SFP') {
																																	list($lastPrice,$declYield) = $productMasterObj->getSemiFinishRate($editIngredientId);
																																} else {
																																	$lastPrice	= 0;
																																	$declYield	= 0;
																																}
																																$editQuantity		= $rec[3];
																																$fixedQtyChk		= $rec[4];
																																$checked = "";
																																$styleDisplay = "";
																																if ($fixedQtyChk=='Y') {
																																	$checked= "Checked";
																																	$styleDisplay = "display:block";
																																} else {
																																	$styleDisplay = "display:none";
																																}
																																$fixedQty		= $rec[5];
																																// Refer values
																																$percentagePerBatch	= $rec[6];
																																$ratePerBatch		= $rec[7];
																																$ingGmsPerPouch		= $rec[8];
																																$percentageWtPerPouch	= $rec[9];
																																$ratePerPouch		= $rec[10];
																																$percentageCostPerPouch	= $rec[11];	
																																$cleanedQty		= $rec[12];

																														?>
																														<tr bgcolor="#E8EDFF" align="center" id="row_<?=$m?>">
																														   <td style="padding-left:5px; padding-right:5px;">
																																<select name="selIngredient_<?=$m?>" id="selIngredient_<?=$m?>" onchange="xajax_getIngRate(document.getElementById('selIngredient_<?=$m?>').value,<?=$m?>, <?=$selRateList?>);calcProductRatePerBatch();">			
																																	<option value="">-- Select --</option>
																																	<?
																																	$ingredientId = "";				
																																	foreach ($ingredientRecords as $vKey=>$ir) {
																																		$ingredientId	= $ir[0]; 
																																		$ingredientName = $ir[1];
																																		$selected	=	"";
																																		if ($displayIngredient==$ingredientId) $selected = "selected";
																																	?>
																																	<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
																																 <? }?>
																																</select>
																															</td>
																															<td style="padding-left:5px; padding-right:5px;">
																																<input name="quantity_<?=$m?>" type="text" id="quantity_<?=$m?>" value="<?=$editQuantity;?>" size='6' style="text-align:right" onkeyup="calcProductRatePerBatch();" autoComplete="off">
																															</td>
																															<td style="padding-left:5px; padding-right:5px;">
																																<input name="cleanedQty_<?=$m?>" type="text" id="cleanedQty_<?=$m?>" value="<?=$cleanedQty;?>" size='6' style="text-align:right;border:none;" autoComplete="off" readonly>
																															</td>
																															<td style="padding-left:5px; padding-right:5px;" class="listing-item" nowrap>
																																<input name="declYield_<?=$m?>" type="text" id="declYield_<?=$m?>" value="<?=number_format($declYield,2,'.','')?>" size='4' style="text-align:right;border:none;" autoComplete="off" readonly>%
																															</td>
																															<td style="padding-left:5px; padding-right:5px;">
																																<input name="fixedQtyChk_<?=$m?>" type="checkbox" id="fixedQtyChk_<?=$m?>" value="Y" size='6' class="chkBox" <?=$checked?> onClick="hidFixedQtyDiv();calcProductRatePerBatch();">
																															</td>
																															<td style="padding-left:5px; padding-right:5px;">				
																																<div id="fixedQtyDiv_<?=$m?>" style="<?=$styleDisplay?>">
																																	<input name="fixedQty_<?=$m?>" type="text" id="fixedQty_<?=$m?>" value="<?=$fixedQty;?>" size='6' style="text-align:right" onkeyup="calcProductRatePerBatch();">
																																</div>
																															</td>
																															<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
																																<input type="text" name="percentagePerBatch_<?=$m?>" id="percentagePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
																															</td>
																															<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
																																<input type="text" name="ratePerKg_<?=$m?>" id="ratePerKg_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$lastPrice?>" size="5">
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
																															<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center" nowrap>
																																<a href='###' onClick="setIngItemStatus(<?=$m?>);"><img title="Click here to remove this item" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>
																																<input name="status_<?=$m?>" type="hidden" id="status_<?=$m?>" value="">
																																<input name="IsFromDB_<?=$m?>" type="hidden" id="IsFromDB_<?=$m?>" value="N">
																																<input name='ingType_<?=$m?>' type='hidden' id='ingType_<?=$m?>' value="<?=$selIngType?>">
																															</td>
																														 </tr>
																														<?
																														$m++;
																														}
																													}
																													?>	
																													</table>
																												</TD>
																											</tr>
																											<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
																											<tr>
																												<TD nowrap style="padding-left:5px; padding-right:5px;">
																												<a href="###" id='addRow' onclick="javascript:addNewIngItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																												</TD>
																											</tr>
																											<!-- Dynamic row ends here -->
																											<SCRIPT LANGUAGE="JavaScript">
																											<!--
																												setfieldId(<?=$rowSize;?>)
																											//-->
																											</SCRIPT>
																										</table>
																									</td>
																								</tr>
																							</table>
																						</TD>
																					</tr>	
																					<tr>
																						<td colspan="2"  height="5" ></td>
																					</tr>
																					<tr>
																						<? if($editMode){?>
																						<td colspan="4" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductMaster(document.frmProductMaster);">	
																						</td>
																						<?} else{?>
																						<td  colspan="4" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductMaster(document.frmProductMaster);">&nbsp;&nbsp;			</td>
																						<input type="hidden" name="cmdAddNew" value="1">
																						<?}?>				
																					</tr>
																					<tr>
																						<td colspan="2"  height="10" >
																						<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyCheked?>">
																						</td>
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
												<TR>
													<TD>
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
																	<? if (sizeof($prdGroupRecs)<=0) {?> <option value=''>--Select All--</option> <? }?>
																	<?php
																	foreach ($prdGroupRecs as $pgrGroupId=>$pgrGroupName) {		
																		$selected = ($pgrGroupId==$selProductGroupId)?"selected":"";	
																	?>
																	<option value='<?=$pgrGroupId?>' <?=$selected?>><?=$pgrGroupName?></option>
																	<?php
																	}
																	?>
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
													</TD>
												</TR>
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
													<td>
													<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductMaster.php?selProductCategory=<?=$selProductCategoryId?>&selProductState=<?=$selProductStateId?>&selProductGroup=<?=$selProductGroupId?>',700,600);"><?}?></td>
												</tr>
											</table>									
										</td>
									</tr>
									<tr>
										<td colspan="3" height="5" ></td>
									</tr>
									<?
										if($errDel!="") {
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
										<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
										<?
										if ( sizeof($productMasterRecords) > 0) {
											$i	=	0;
										?>
											<thead>
											<? if($maxpage>1){ ?>
												<tr>
													<td colspan="12" align="right" style="padding-right:10px;" class="navRow">
														<div align="right">
														<?php
														 $nav  = '';
														for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"ProductMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"ProductMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"ProductMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th align="center" style="padding-left:10px; padding-right:10px;">Code</th>
												<th style="padding-left:10px; padding-right:10px;">Name</th>
												<th style="padding-left:10px; padding-right:10px;">Net Wt</th>
												<th style="padding-left:10px; padding-right:10px;">Fixed Wt</th>
												<th style="padding-left:10px; padding-right:10px;">Gravy Wt</th>
												<th style="padding-left:10px; padding-right:10px;">Fixed Cost/Kg</th>
												<th style="padding-left:10px; padding-right:10px;">Gravy Cost/Kg</th>
												<th style="padding-left:10px; padding-right:10px;">Product Cost/Kg</th>
												<th style="padding-left:10px; padding-right:10px;">Cost/Unit</th>
												<? if($edit==true){?>
												<th class="listing-head">&nbsp;</th>
												<? }?>
												<? if($confirm==true){?>
												<th class="listing-head">&nbsp;</th>
												<? }?>
											</tr>
										</thead>
										<?php
										foreach ($productMasterRecords as $pmr) {
											$i++;
											$productId		= $pmr[0];
											$productCode		= $pmr[1];
											$productName		= $pmr[2];
											$netWt			= $pmr[3];
											$fixedWt 		= $pmr[4]; 
											$gravyWt		= $pmr[5]; 
											$fixedCostPerKg		= $pmr[6]; 
											$gravyCostPerKg		= $pmr[7];
											$rsPerPouch		= $pmr[8];
											$qtyPerPouch		= $pmr[9];
											$costPerProduct		= number_format(($rsPerPouch/$qtyPerPouch),2,'.','');
											$active=$pmr[10];
										?>
										<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
											<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productId;?>" class="chkBox"></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productCode;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$netWt;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$fixedWt;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$gravyWt;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$fixedCostPerKg;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$gravyCostPerKg;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$costPerProduct;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$rsPerPouch;?></td>
											<? if($edit==true){?>
											<td class="listing-item" width="60" align="center">
												<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ProductMaster.php';"><? } ?>
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
											<td colspan="12" align="right" style="padding-right:10px;" class="navRow">
												<div align="right">
												<?php
												 $nav  = '';
												for ($page=1; $page<=$maxpage; $page++) {
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"ProductMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"ProductMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"ProductMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
									<?
										} else {
									?>
									<tr bgcolor="white">
										<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>
									<?
										}
									?>
								</table>
								<input type="hidden" name="productStateGroup" value="<?=$productGroupExist?>">
								<input type="hidden" name="totalFixedFishQty" id="totalFixedFishQty" value="<?=$totalFixedFishQty?>">
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
											<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductMaster.php?selProductCategory=<?=$selProductCategoryId?>&selProductState=<?=$selProductStateId?>&selProductGroup=<?=$selProductGroupId?>',700,600);"><?}?></td>
										</tr>
									</table>			
								</td>
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
				<!-- Form fields end   -->			
		</td>
	</tr>
	<input type="hidden" name="hidIngRateListId" id="hidIngRateListId" value="<?=$selRateList?>" readonly>
	<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" readonly>
	<tr>
		<td height="10"></td>
	</tr>
	<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product Category" onclick="parent.openTab('ProductCategory.php');">Product Category</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductState.php');">Product State</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="###" class="link1" onclick="parent.openTab('ProductGroup.php');">Product Group</a></td>	
	</tr>
		<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
</table> 	
	<? if ($addMode || $editMode ) {?>
	<script language="JavaScript">
		function addNewIngItem()
		{
			addNewIngItemRow('tblAddIng');
		}		
	</script>
	<? }?>
	<? if ($addMode && !$selProductId) {?>
	<script language="JavaScript">
		window.onLoad = addNewIngItem();
	</script>
	<? }?>

	<?php 
		if ($selProductId!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_chkPCodeExist('<?=$productCode?>', '<?=(!$selProductId)?$editProductId:""?>');
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
	//ensureInFrameset(document.frmProductMaster);
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