<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once('lib/RecipeMaster_ajax.php');
	
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
	if ($p["selRecipe"]!="") $selRecipeId	= $p["selRecipe"];
	if ($p["recipeCode"]!="") $productCode = $p["recipeCode"];
	if ($p["recipeName"]!="") $recipeName	= $p["recipeName"];
	if ($p["productCategory"]!="") $productCategory	= $p["productCategory"];
	if ($p["recipeCategory"]!="") $recipeCategory = $p["recipeCategory"];
	if ($p["cusine"]!="") $productGroup = $p["cusine"];
	//if ($productState) $productGroupExist = $recipeMasterObj->checkProductGroupExist($productState);
	if ($p["gmsPerPouch"]!="") $gmsPerPouch = $p["gmsPerPouch"]; // Net Wt of the product	
	if ($p["fixedQtyCheked"]!="") $fixedQtyCheked = $p["fixedQtyCheked"];
	
	
	# Add a Product
	if ($p["cmdAdd"]!="") {

		$itemCount	= $p["hidTableRowCount"];
		$recipeCode	= $p["recipeCode"];
		$recipeName	= $p["recipeName"];
		$productCategory = $p["productCategory"];
		$recipeCategory 	 = $p["recipeCategory"];
		$cusine 	 = $p["cusine"];
		
		
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

	/*$productRatePerKg	= $p["productRatePerKg"];
		$fishRatePerKg		= $p["fishRatePerKg"];
		$gravyRatePerKg		= $p["gravyRatePerKg"];
		*/
		
		if ($recipeCode!="" && $recipeName!="") {

			$recipeRecIns = $recipeMasterObj->addRecipe($recipeCode, $recipeName, $userId, $productCategory, $recipeCategory, $cusine, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $semiFinished, $hidIngRateListId);

			#Find the Last inserted Id From m_productmaster Table
			$lastId = $databaseConnect->getLastInsertedId();
			
			for ($i=0; $i<$itemCount; $i++) {
			    $status = $p["status_".$i];
			    if ($status!='N') {
				# ING - Ingredient  SFP = Semi Finished Product
				/*$selIngRec	= explode("_",$p["selIngredient_".$i]);
				//$ingredientId	= $p["selIngredient_".$i];
				$selIngType	= $selIngRec[0];	
				$ingredientId   = $selIngRec[1];*/
				$ingredientId   =$p["selIngredient_".$i];
				$ingRateId  =$p["ingRateId_".$i];
				$rsperKg =$p["rsperKg_".$i];
				$quantity	= trim($p["quantity_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];
				//$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];
	
				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerKg		= $p["ratePerKg_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];
				
				if ($lastId!="" && $ingredientId!="" && $quantity!="") {
					$recipeItemsIns = $recipeMasterObj->addIngredientEntries($lastId, $ingredientId, $quantity, $fixedQtyChk,$ingRateId,$rsperKg,$percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $ratePerPouch, $percentageCostPerPouch, $ratePerKg);
				}
			  }
		     }	
		}

		if ($recipeRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddRecipeMaster);
			$sessObj->createSession("nextPage",$url_afterAddRecipeMaster.$selection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddRecipeMaster;
		}
		$recipeRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" || $selRecipeId!="") {
		if ($selRecipeId) $editId = $selRecipeId;
		else $editId		=	$p["editId"];
		if (!$selRecipeId) $editMode = true;
		$recipeMasterRec	=	$recipeMasterObj->find($editId);
		$editRecipeId		=	$recipeMasterRec[0];
		$recipeCode		=	$recipeMasterRec[1];
		$recipeName		=	$recipeMasterRec[2];
		if ($p["editSelectionChange"]=='1' || $p["productCategory"]=="") {
			$productCategory	= 	$recipeMasterRec[3];
		} else {
			$productCategory	=	$p["productCategory"];
		}
		
		if ($p["editSelectionChange"]=='1' || $p["recipeCategory"]=="") {
			$category	= 	$recipeMasterRec[4];
		} else {
			$category	=	$p["recipeCategory"];
		}		
		$cusine 		= $recipeMasterRec[5];
		$gmsPerPouch 	 	= $recipeMasterRec[6];

		////////
		$productRatePerPouch 	= $recipeMasterRec[7];
		$fishRatePerPouch	= $recipeMasterRec[8];
		$gravyRatePerPouch	= $recipeMasterRec[9];
		$productGmsPerPouch	= $recipeMasterRec[10];
		$fishGmsPerPouch	= $recipeMasterRec[11];
		$gravyGmsPerPouch	= $recipeMasterRec[12];
		$productPercentagePerPouch = $recipeMasterRec[13];
		$fishPercentagePerPouch	= $recipeMasterRec[14];
		$gravyPercentagePerPouch = $recipeMasterRec[15];
		$productRatePerKgPerBatch = $recipeMasterRec[16];
		$fishRatePerKgPerBatch 	= $recipeMasterRec[17];
		$gravyRatePerKgPerBatch = $recipeMasterRec[18];
		$pouchPerBatch		= $recipeMasterRec[19];
		$productRatePerBatch	= $recipeMasterRec[20];
		$fishRatePerBatch	= $recipeMasterRec[21];
		$gravyRatePerBatch	= $recipeMasterRec[22];
		$productKgPerBatch	= $recipeMasterRec[23];
		$fishKgPerBatch		= $recipeMasterRec[24];
		$gravyKgPerBatch	= $recipeMasterRec[25];
		$productRawPercentagePerPouch = $recipeMasterRec[26];
		$fishRawPercentagePerPouch = $recipeMasterRec[27];
		$gravyRawPercentagePerPouch = $recipeMasterRec[28];
		$productKgInPouchPerBatch = $recipeMasterRec[29];
		$fishKgInPouchPerBatch	= $recipeMasterRec[30];
		$gravyKgInPouchPerBatch	= $recipeMasterRec[31];
		$fishPercentageYield	= $recipeMasterRec[32];
		$gravyPercentageYield 	= $recipeMasterRec[33];
		$totalFixedFishQty	= $recipeMasterRec[34];
		$semiFinishedChk	= $recipeMasterRec[35];
		$sFinishedChk = "";
		if ($semiFinishedChk=='Y') $sFinishedChk = "checked";
		$selIngRateListId	= $recipeMasterRec[36];

		$productRatePerKg	= $recipeMasterRec[37];
		$fishRatePerKg		= $recipeMasterRec[38];
		$gravyRatePerKg		= $recipeMasterRec[39];
		/*****/
		
		//Find whether the state has a Group
		if ($productState) $productGroupExist = $recipeMasterObj->checkProductGroupExist($productState);

		/* Removed on 6-08-08
		$productRecs = $recipeMasterObj->fetchAllIngredients($editRecipeId);
		$rowSize = sizeof($productRecs);
		*/
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$recipeId = $p["hidRecipeId"];

		$itemCount	=	$p["hidTableRowCount"];
		//$gmsPerPouch 	 = $p["gmsPerPouch"];
		$recipeCode	= $p["recipeCode"];
		$recipeName	= $p["recipeName"];
		$productCategory = $p["productCategory"];
		$recipeCategory 	 = $p["recipeCategory"];
		$cusine 	 = $p["cusine"];
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

		/*$productRatePerKg	= $p["productRatePerKg"];
		$fishRatePerKg		= $p["fishRatePerKg"];
		$gravyRatePerKg		= $p["gravyRatePerKg"];*/
		
		if ($recipeId!="" && $recipeCode!="" && $recipeName!="") {
			$recipeMasterRecUptd = $recipeMasterObj->updateRecipeMaster($recipeId, $recipeCode, $recipeName, $productCategory, $recipeCategory, $cusine, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $semiFinished, $hidIngRateListId);
		
			#Delete First all records from Product master entry table
			$deleteIngredientItemRecs = $recipeMasterObj->deleteIngredientItemRecs($recipeId);
			
			for ($i=0; $i<$itemCount; $i++) {
			   $status = $p["status_".$i];
			    if ($status!='N') {
				//$ingredientId	=	$p["selIngredient_".$i];
				# ING - Ingredient  SFP = Semi Finished Product
				$ingredientId   =$p["selIngredient_".$i];
				$rsperKg =$p["rsperKg_".$i];
				$ingRateId  =$p["ingRateId_".$i];
				$quantity	= trim($p["quantity_".$i]);
				$fixedQtyChk	= ($p["fixedQtyChk_".$i]=="")?N:$p["fixedQtyChk_".$i];
				//$fixedQty	= ($p["fixedQty_".$i]=="")?0:$p["fixedQty_".$i];
	
				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerKg		= $p["ratePerKg_".$i];	
				$ingGmsPerPouch		= $p["ingGmsPerPouch_".$i];	
				$percentageWtPerPouch	= $p["percentageWtPerPouch_".$i];
				$ratePerPouch		= $p["ratePerPouch_".$i];
				$percentageCostPerPouch	= $p["percentageCostPerPouch_".$i];
					
				if ($recipeId!="" && $ingredientId!="" && $quantity!="") {
					$recipeItemsIns = $recipeMasterObj->addIngredientEntries($recipeId, $ingredientId, $quantity, $fixedQtyChk,$ingRateId,$rsperKg,$percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $ratePerPouch, $percentageCostPerPouch, $ratePerKg);
				}
			  }
			}
		}
	
		if ($recipeMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRecipeMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterAddRecipeMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductMasterUpdate;
		}
		$recipeMasterRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$recipeId	=	$p["delId_".$i];

			if ($recipeId!="" ) {
				# Checking Product used as an Ing
				$recipeUsedAsIng =  $recipeMasterObj->chkRecipeUsedAsIng($recipeId);
				
				if (!$recipeUsedAsIng) {
					$deleteIngredientItemRecs =	$recipeMasterObj->deleteIngredientItemRecs($recipeId);
					$recipeMasterRecDel = $recipeMasterObj->deleteRecipeMaster($recipeId);
				}				
			}
		}
		if ($recipeMasterRecDel) {
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
			$recipeId	=	$p["confirmId"];
			if ($recipeId!="") {
				// Checking the selected fish is link with any other process
				$recipeRecConfirm = $recipeMasterObj->updateRecipeconfirm($recipeId);
			}

		}
		if ($recipeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmRecipeMaster);
			$sessObj->createSession("nextPage",$url_afterDelRecipeMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$recipeId = $p["confirmId"];
			if ($recipeId!="") {
				#Check any entries exist
				
					$recipeRecConfirm = $recipeMasterObj->updateRecipeReleaseconfirm($recipeId);
				
			}
		}
		if ($recipeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmRecipeMaster);
			$sessObj->createSession("nextPage",$url_afterDelRecipeMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}

	if ($p["btnApproval"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$recipeId	=	$p["confirmId"];
			if ($recipeId!="") {
				// Checking the selected fish is link with any other process
				$recipeRecApproval = $recipeMasterObj->updateRecipeApproval($recipeId);
			}

		}
		if ($recipeRecApproval) {
			$sessObj->createSession("displayMsg",$msg_succApprovedRecipeMaster);
			$sessObj->createSession("nextPage",$url_afterDelRecipeMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlApproval"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$recipeId = $p["confirmId"];
			if ($recipeId!="") 
			{
				#Check any entries exist
				$recipeReleaseRecApproval = $recipeMasterObj->updateRecipeReleaseApproval($recipeId);
			}
		}
		if ($recipeReleaseRecApproval) {
			$sessObj->createSession("displayMsg",$msg_succReApprovalRecipeMaster);
			$sessObj->createSession("nextPage",$url_afterDelRecipeMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	if($p["cmdRefresh"])
	{
		//echo "hii";
		$recipyMasterRecords = $recipeMasterObj->fetchAllRecords();
		foreach($recipyMasterRecords as $rMR)
		{
			$recipeId=$rMR[0];
			$recipeCode		=	$rMR[1];
			$recipeName		=	$rMR[2];
			$productGmsPerPouch=$rMR[9];
			$fishGmsPerPouch=$rMR[5];
			$gravyGmsPerPouch=$rMR[6];
			$pouchPerBatch=$rMR[10];
			$cusine=$rMR[11];
			$productCategory=$rMR[12];
			$recipeCategory=$rMR[13];
			$fishRatePerBatch=$rMR[14];
			$gravyRatePerBatch=$rMR[15];
			$fishKgPerBatch=$rMR[16];
			$gravyKgPerBatch=$rMR[17];

			$gravyGmsPerPouch=$productGmsPerPouch-$fishGmsPerPouch;
			$fishPercentagePerPouch=($fishGmsPerPouch/$productGmsPerPouch)*100;
			$gravyPercentagePerPouch=($gravyGmsPerPouch/$productGmsPerPouch)*100;
			$productPercentagePerPouch=$gravyPercentagePerPouch+$fishPercentagePerPouch;
			$fishKgInPouchPerBatch=$pouchPerBatch*$fishGmsPerPouch;
			$gravyKgInPouchPerBatch=$pouchPerBatch*$gravyGmsPerPouch;
			$productKgInPouchPerBatch=$fishKgInPouchPerBatch+$gravyKgInPouchPerBatch;

			$ingRateRecords = $recipeMasterObj-> fetchAllIngredients($recipeId);
			$fishRatePerKg=0; $gravyRatePerKg=0;
			foreach($ingRateRecords as $ingRec)
			{
				$ingId=$ingRec[2];
					list($ingRateId,$ingRate)=$recipeMasterObj->getIngRate($ingId);
					$fixedQtyChk=$ingRec[4];
					$quantity=$ingRec[3];
					
					if ($fixedQtyChk=="Y" && $ingId!="" && $quantity!="")
					{
							$fishRatePerKg	+= $quantity;
								
					}
					else if($ingId!="" && $quantity!="" && $fixedQtyChk=="N") 
					{	
							$gravyRatePerKg	+= $quantity;
					}

				}
				$productKgPerBatch=$fishRatePerKg+$gravyRatePerKg;
				$fishRawPercentagePerPouch=($fishRatePerKg/$productKgPerBatch)*100;	
				$gravyRawPercentagePerPouch=($gravyRatePerKg/$productKgPerBatch)*100;
				$productRawPercentagePerPouch=$fishRawPercentagePerPouch+$gravyRawPercentagePerPouch;
				$fishPercentageYield=($fishKgInPouchPerBatch/$fishRatePerKg)*100;
				$gravyPercentageYield=($gravyKgInPouchPerBatch/$gravyRatePerKg)*100;


					$ingRateRecords = $recipeMasterObj->fetchAllIngredients($recipeId);
					$rsfishPerBatch=0; $rsGravyPerBatch=0;
					foreach($ingRateRecords as $ingRec)
					{
							$ingId=$ingRec[2];
							list($ingRateId,$ingRate)=$recipeMasterObj->getIngRate($ingId);
							$fixedQtyChk=$ingRec[4];
							$quantity=$ingRec[3];
							$percentagePerBatch=($quantity/$productKgPerBatch)*100;
							$rsperKg=$ingRate;	
							$ratePerKg=$rsperKg*$quantity;
							$ingGmsPerPouch=$quantity/$pouchPerBatch;
							$ratePerPouch=$ratePerKg/$pouchPerBatch;
							if ($fixedQtyChk=="Y" && $ingId!="" && $quantity!="")
							{
								$rsfishPerBatch+= $ratePerKg;
								
							}
							else if($ingId!="" && $quantity!="" && $fixedQtyChk=="N") 
							{	
								$rsGravyPerBatch+= $ratePerKg;
							}
											
						}
					
						$productRatePerBatch=$rsfishPerBatch+$rsGravyPerBatch;
						if($rsfishPerBatch!="" && $fishKgInPouchPerBatch!="")
						{
							$fishRatePerKgPerBatch=$rsfishPerBatch/$fishKgInPouchPerBatch;
						}
						if($rsGravyPerBatch!="" && $gravyKgInPouchPerBatch!="")
						{
							$gravyRatePerKgPerBatch=$rsGravyPerBatch/$gravyKgInPouchPerBatch;
						}
						//alert(fishRatePerKgPerBatch+"----"+gravyRatePerKgPerBatch);
						if($fishRatePerKgPerBatch!="" && $gravyRatePerKgPerBatch!="" )
						{
							$productRatePerKgPerBatch=$fishRatePerKgPerBatch+$gravyRatePerKgPerBatch;
						}
						$fishRatePerPouch=$fishRatePerKgPerBatch*$fishGmsPerPouch;
						
						$gravyRatePerPouch=$gravyRatePerKgPerBatch*$gravyGmsPerPouch;
						
						$productRatePerPouch=$productRatePerBatch/$pouchPerBatch;
				
					$ingRateRecords = $recipeMasterObj->fetchAllIngredients($recipyId);
					$fishRatePerKg=0; $gravyRatePerKg=0;
					foreach($ingRateRecords as $ingRec)
					{
							$ingId=$ingRec[2];
							list($ingRateId,$ingRate)=$recipeMasterObj->getIngRate($ingId);
							$fixedQtyChk=$ingRec[4];
							$quantity=$ingRec[3];
							$percentagePerBatch=($quantity/$productKgPerBatch)*100;
							$rsperKg=$ingRate;	
							$ratePerKg=$rsperKg*$quantity;
							$ingGmsPerPouch=$quantity/$pouchPerBatch;
							$ratePerPouch=$ratePerKg/$pouchPerBatch;
							$percentageCostPerPouch=($ratePerPouch/$productRatePerPouch)*100;
											
					}
					
			$totalFixedFishQty	= "";
			$semiFinished		="";
			$hidIngRateListId	= "";

				
			$recipeMasterRecUptd = $recipeMasterObj->updateRecipeMaster($recipeId, $recipeCode, $recipeName, $productCategory, $recipeCategory, $cusine, $productGmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $semiFinished, $hidIngRateListId);


			$ingRateRecords = $recipeMasterObj->fetchAllIngredients($recipeId);
			$rsfishPerBatch=0; $rsGravyPerBatch=0;
			foreach($ingRateRecords as $ingRec)
			{
				$receipeEntryId=$ingRec[0];
				$ingId=$ingRec[2];
				list($ingRateId,$ingRate)=$recipeMasterObj->getIngRate($ingId);
				$fixedQtyChk=$ingRec[4];
				$quantity=$ingRec[3];
				$percentagePerBatch=($quantity/$productKgPerBatch)*100;
				$rsperKg=$ingRate;	
				$ratePerKg=$rsperKg*$quantity;
				$ingGmsPerPouch=$quantity/$pouchPerBatch;
				$ratePerPouch=$ratePerKg/$pouchPerBatch;
				$percentageCostPerPouch=($ratePerPouch/$productRatePerPouch)*100;
				$recipeItemsIns = $recipeMasterObj->updateIngredientEntries($receipeEntryId,$recipeId, $ingId, $quantity, $fixedQtyChk,$ingRateId,$rsperKg,$percentagePerBatch, $ingGmsPerPouch, $ratePerPouch, $percentageCostPerPouch, $ratePerKg);
											
			}


		

		//die();


		



				/*	var productGmsPerPouch = document.getElementById("productGmsPerPouch").value;	
					var fishGmsPerPouch= document.getElementById("fishGmsPerPouch").value;
					if(fishGmsPerPouch!="" && productGmsPerPouch!="")
					{
						var gravyGmsPerPouch=parseFloat(productGmsPerPouch)-parseFloat(fishGmsPerPouch);
						document.getElementById("gravyGmsPerPouch").value=gravyGmsPerPouch;
						
						
						var fishPercentagePerPouch=(parseFloat(fishGmsPerPouch)/parseFloat(productGmsPerPouch))*100;
						document.getElementById("fishPercentagePerPouch").value=Math.round(fishPercentagePerPouch);
						//alert(fishGmsPerPouch+"--"+productGmsPerPouch);
						var gravyPercentagePerPouch=(parseFloat(gravyGmsPerPouch)/parseFloat(productGmsPerPouch))*100;
						document.getElementById("gravyPercentagePerPouch").value=Math.round(gravyPercentagePerPouch);
						
						var productPercentagePerPouch=parseFloat(gravyPercentagePerPouch)+parseFloat(fishPercentagePerPouch);
						document.getElementById("productPercentagePerPouch").value=Math.round(productPercentagePerPouch);
					}*/

					

				/*	var pouchPerBatch= document.getElementById("pouchPerBatch").value;	
					if(pouchPerBatch!="")
					{
						if(fishGmsPerPouch!="")
						{
							var fishKgInPouchPerBatch=pouchPerBatch*fishGmsPerPouch;
							document.getElementById("fishKgInPouchPerBatch").value=Math.round(fishKgInPouchPerBatch);
						}
						if(gravyKgInPouchPerBatch!="")
						{
							var gravyKgInPouchPerBatch=pouchPerBatch*gravyGmsPerPouch;
							document.getElementById("gravyKgInPouchPerBatch").value=Math.round(gravyKgInPouchPerBatch);
						}

						if(fishGmsPerPouch!="" && gravyKgInPouchPerBatch!="")
						{
							var productKgInPouchPerBatch=parseFloat(fishKgInPouchPerBatch)+parseFloat(gravyKgInPouchPerBatch);
							document.getElementById("productKgInPouchPerBatch").value=Math.round(productKgInPouchPerBatch);
						}
					}
					
					var itemCount 	      = document.getElementById("hidTableRowCount").value;	
					var fishRatePerKg=0; var gravyRatePerKg=0; var fishRawPercentagePerPouch=0; var gravyRawPercentagePerPouch=0; var productKgPerBatch=0;
					for (i=0; i<itemCount; i++) 
					{
						var status = document.getElementById("status_"+i).value;
						if (status!='N')
						{
							var selIngredient = document.getElementById("selIngredient_"+i).value;
							var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
							var quantity  = document.getElementById("quantity_"+i).value;
							if (fixedQtyChk!="" && selIngredient!="" && quantity!="")
							{
								fishRatePerKg	+= parseFloat(quantity);
								
							}
							else if(selIngredient!="" && quantity!="" && fixedQtyChk=="") 
							{	
								gravyRatePerKg	+= parseFloat(quantity);
							}
						}
					}
					*/



					/*
					//var fishKgPerBatch=document.getElementById("quantity_0").value;
					if(itemCount==i)
					{
						document.getElementById("fishKgPerBatch").value=Math.round(fishRatePerKg);
						document.getElementById("gravyKgPerBatch").value=Math.round(gravyRatePerKg);
						var productKgPerBatch=parseFloat(fishRatePerKg)+parseFloat(gravyRatePerKg);
						document.getElementById("productKgPerBatch").value=Math.round(productKgPerBatch);
						//alert(fishRatePerKg+"------"+productKgPerBatch);
						if(fishRatePerKg!="")
						{
							var fishRawPercentagePerPouch=(fishRatePerKg/productKgPerBatch)*100;
							document.getElementById("fishRawPercentagePerPouch").value=Math.round(fishRawPercentagePerPouch);
						}

						if(gravyRatePerKg!="")
						{
							var gravyRawPercentagePerPouch=(gravyRatePerKg/productKgPerBatch)*100;
							document.getElementById("gravyRawPercentagePerPouch").value=Math.round(gravyRawPercentagePerPouch);
						}

						if(fishRawPercentagePerPouch!="" &&  gravyRawPercentagePerPouch!="")
						{
							var productRawPercentagePerPouch=parseFloat(fishRawPercentagePerPouch)+parseFloat(gravyRawPercentagePerPouch);
							document.getElementById("productRawPercentagePerPouch").value=Math.round(productRawPercentagePerPouch);
						}

						//alert(fishKgInPouchPerBatch+"----"+gravyKgInPouchPerBatch);
						if(fishRatePerKg!=0)
						{
						var fishPercentageYield=(fishKgInPouchPerBatch/fishRatePerKg)*100;
						document.getElementById("fishPercentageYield").value=Math.round(fishPercentageYield);
						}
						if(gravyRatePerKg!=0)
						{
						var gravyPercentageYield=(gravyKgInPouchPerBatch/gravyRatePerKg)*100;
						document.getElementById("gravyPercentageYield").value=Math.round(gravyPercentageYield);
						}


						
					}*/


				/*
					var  rsfishPerBatch=0; var  rsGravyPerBatch=0;
					for (i=0; i<itemCount; i++) 
					{
						var status = document.getElementById("status_"+i).value;
						if (status!='N')
						{
							var selIngredient = document.getElementById("selIngredient_"+i).value;
							var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
							var quantity  = document.getElementById("quantity_"+i).value;
							var percentagePerBatch=(quantity/productKgPerBatch)*100;
							document.getElementById("percentagePerBatch_"+i).value=Math.round(percentagePerBatch);
							var rsperKg=document.getElementById("rsperKg_"+i).value;	
							var ratePerKg=rsperKg*quantity;
							document.getElementById("ratePerKg_"+i).value= Math.round(ratePerKg);

							var ingGmsPerPouch=quantity/pouchPerBatch;
							document.getElementById("ingGmsPerPouch_"+i).value= number_format(ingGmsPerPouch,3, ".", "");
						
							var ratePerPouch=ratePerKg/pouchPerBatch;
							document.getElementById("ratePerPouch_"+i).value=number_format(ratePerPouch,2, ".", ""); 
							
							var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
							if (fixedQtyChk!="" && selIngredient!="" && quantity!="")
							{
								rsfishPerBatch	+= parseFloat(ratePerKg);
								
							}
							else if(selIngredient!="" && quantity!="" && fixedQtyChk=="") 
							{	
								rsGravyPerBatch	+= parseFloat(ratePerKg);
							}
						}
					}
					var fishRatePerKgPerBatch=0; var gravyRatePerKgPerBatch=0; 
					if(itemCount==i)
					{
						document.getElementById("fishRatePerBatch").value=Math.round(rsfishPerBatch);
						document.getElementById("gravyRatePerBatch").value=Math.round(rsGravyPerBatch);
						var productRatePerBatch=parseFloat(rsfishPerBatch)+parseFloat(rsGravyPerBatch);
						document.getElementById("productRatePerBatch").value=Math.round(productRatePerBatch);
						if(rsfishPerBatch!="" && fishKgInPouchPerBatch!="")
						{
							var fishRatePerKgPerBatch=rsfishPerBatch/fishKgInPouchPerBatch;
							document.getElementById("fishRatePerKgPerBatch").value=Math.round(fishRatePerKgPerBatch);
						}
						if(rsGravyPerBatch!="" && gravyKgInPouchPerBatch!="")
						{
							var gravyRatePerKgPerBatch=rsGravyPerBatch/gravyKgInPouchPerBatch;
							document.getElementById("gravyRatePerKgPerBatch").value=Math.round(gravyRatePerKgPerBatch);
						}
						//alert(fishRatePerKgPerBatch+"----"+gravyRatePerKgPerBatch);
						if(fishRatePerKgPerBatch!="" && gravyRatePerKgPerBatch!="" )
						{
							var productRatePerKgPerBatch=parseFloat(fishRatePerKgPerBatch)+parseFloat(gravyRatePerKgPerBatch);
							document.getElementById("productRatePerKgPerBatch").value=Math.round(productRatePerKgPerBatch);
						}
						var fishRatePerPouch=fishRatePerKgPerBatch*fishGmsPerPouch;
						document.getElementById("fishRatePerPouch").value=number_format(fishRatePerPouch,2, ".", ""); 

						var gravyRatePerPouch=gravyRatePerKgPerBatch*gravyGmsPerPouch;
						document.getElementById("gravyRatePerPouch").value=number_format(gravyRatePerPouch,2, ".", ""); 
						
						var productRatePerPouch=productRatePerBatch/pouchPerBatch;
						document.getElementById("productRatePerPouch").value=number_format(productRatePerPouch,2, ".", ""); 
						

					}

					 for (i=0; i<itemCount; i++) 
					{
						var status = document.getElementById("status_"+i).value;
						if (status!='N')
						{
							var quantity  = document.getElementById("quantity_"+i).value;
							var rsperKg=document.getElementById("rsperKg_"+i).value;	
							var ratePerKg=rsperKg*quantity;

							var ratePerPouch=ratePerKg/pouchPerBatch;
							document.getElementById("ratePerPouch_"+i).value=number_format(ratePerPouch,2, ".", ""); 
							//alert(ratePerPouch+"---"+productRatePerPouch+"--"+percentageCostPerPouch);
							var percentageCostPerPouch=(ratePerPouch/productRatePerPouch)*100;
							document.getElementById("percentageCostPerPouch_"+i).value=Math.round(percentageCostPerPouch);
						}
					}

				*/
			//}
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

	if ($g["selCategory"]!="") $selCategoryId = $g["selCategory"];
	else $selCategoryId = $p["selCategory"];

	if ($g["selCusine"]!="") $selCusineId = $g["selCusine"];
	else $selCusineId = $p["selCusine"];

	if ($p["cmdSearch"]) $offset = 0;
	
	#List all Records
	$productMasterRecords = $recipeMasterObj->fetchAllPagingRecords($offset, $limit, $selProductCategoryId, $selCategoryId, $selCusineId);
	$productMasterRecordSize    = sizeof($productMasterRecords);
	
	## -------------- Pagination Settings II -------------------
	$fetchAllProductMasterRecords = $recipeMasterObj->fetchAllRecords($selProductCategoryId, $selCategoryId, $selCusineId);	// fetch All Records
	$numrows	=  sizeof($fetchAllProductMasterRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Ingredient
	if ($addMode || $editMode) {
		if ($selIngRateListId!="") $selRateList = $selIngRateListId; 
		else $selRateList = $ingredientRateListObj->latestRateList();		
		$ingredientRecords = $recipeMasterObj->fetchAllIngredientRecords($selRateList);		
	}

	#List all Product Category Records
	$productCategoryRecords = $productCategoryObj->fetchAllRecordsActiveCategory();
	$categoryRecords = $recpMainCategoryObj->fetchAllRecordsActiveCategory();

	#List all Product State Records
	//$productStateRecords = $productStateObj->fetchAllRecords();
//	$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();

	# Get Product Group;
	if ($selProductStateId!="") {
		# Checking Prouct Group Exist
		$prdGroupExist = $manageProductObj->checkProductGroupExist($selProductStateId);
		$prdGroupRecs =  $recipeMasterObj->filterProductGroupList($prdGroupExist);
	}

	if ($addMode || $editMode) {
		#List all Product Group Records
		//$productGroupRecords =$productGroupObj->fetchAllRecords();		
	//	$productGroupRecords =$productGroupObj->fetchAllRecordsActiveGroup();
	}

	if ($addMode) $selRecipeRecs = $recipeMasterObj->getAllRMRecs();

	//list($productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch) =$recipeMasterObj->getProductMasterRec($productId);

	if ($addMode) 		$mode = 1;
	else if ($editMode)	$mode = 2;
	else $mode = "";

	if ($editMode) $heading	= $label_editRecipeMaster;
	else	       $heading	= $label_addRecipeMaster;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/RecipeMaster.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	/*$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else*/ 
	require("template/btopLeftNav.php");
?>
<form name="frmRecipeMaster" action="RecipeMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<TD height="10"></TD>
		</tr>
		<tr>
			<td height="10" align="center"><a href="###" class="link1" title="Click to Manage Recipe Category" onclick="parent.openTab('RecipeMainCategory.php');">Recipe Category</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<!--<a href="###" class="link1" onclick="parent.openTab('RecipeSubCategory.php');">Recipe Sub Category</a>-->
			</td>	
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
								$bxHeader="Recipe Master";
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
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RecipeMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRecipeMaster(document.frmRecipeMaster);">	
																					</td>
																					<?} else{?>
																					<td  colspan="4" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RecipeMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRecipeMaster(document.frmRecipeMaster);">&nbsp;&nbsp;		
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidRecipeId" value="<?=$editRecipeId;?>">
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
																																		<TD class="fieldName" onMouseover="ShowTip('Copy from existing Product and save after editing.');" onMouseout="UnTip();">Recipe</TD>
																																		<td>
																																			<select name="selRecipe" id="selRecipe" onchange="this.form.submit();">
																																				<option value="">--Select--</option>
																																				<?php
																																					foreach ($selRecipeRecs as $spr) {
																																						$sRecipeId 	= $spr[0];
																																						$sRecipeName	= stripSlash($spr[2]);
																																						$selected = "";
																																						if ($selRecipeId==$sRecipeId) $selected = "selected";
																																				?>
																																				<option value="<?=$sRecipeId?>" <?=$selected?>><?=$sRecipeName?></option>
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
																															<input type="text" name="recipeCode" id="recipeCode" value="<?=$recipeCode?>" size="10" onblur="xajax_chkPCodeExist(document.getElementById('recipeCode').value, '<?=(!$selRecipeId)?$editRecipeId:""?>');" autocomplete="off">
																															<input type="hidden" name="hidRecipeCode" id="hidRecipeCode" value="<?=$recipeCode?>" size="14">
																															<input type="hidden" name="hidPCodeExist" id="hidPCodeExist" value="">
																															<span id="pcodeExist" class="err1" style="line-height:normal;" nowrap="true"></span>
																														</td>
																												   </tr>
																													<tr>
																														<td class="fieldName">*Name:</td>
																														<td class="listing-item"><input type="text" name="recipeName" value="<?=$recipeName?>" size="30"></td>
																													</tr>
																													<tr>
																														<td class="fieldName">Product:</td>
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
																														<td class="fieldName">Category:</td>
																														<td class="listing-item">
																															<select name="recipeCategory">
																																<option value="">--Select--</option>
																																<?
																																foreach ($categoryRecords as $cr) {
																																	$categoryId	= $cr[0];
																																	$categoryName	= stripSlash($cr[1]);
																																	$selected = "";
																																	if ($category==$categoryId) $selected = "Selected";
																																?>
																																<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
																																<? }?>
																															 </select>
																														</td>
																													</tr>
																													<tr>
																														<td class="fieldName">Cusine:</td>
																														<td class="listing-item">
																															<? if($cusine=="indian")
																															{	 
																																$selIndian="selected";
																																$selChinese="";
																															}
																															else{
																																$selIndian="";
																																$selChinese="selected";
																															}
																															?>
																															<select name="cusine">
																																<option value="">--Select--</option>
																																<option value="indian" <?=$selIndian?>>Indian</option>
																																<option value="chinese" <?=$selChinese?>>Chinese</option> 
																															</select>
																														</td>
																													</tr>
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
																														<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow" nowrap>Gms per pouch<!--Gms per Pouch--></TD>
																														<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
																														<? if ($p["productGmsPerPouch"]!="") $productGmsPerPouch=$p["productGmsPerPouch"];?>
																															<input type="text" size="5" style="text-align:right;" name="productGmsPerPouch" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" onkeyup="calcProductRatePerBatch();" autoComplete="off"></TD>
																														<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
																														<? if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch=$p["fishGmsPerPouch"];?>
																															<input type="text" size='6' style="text-align:right; " name="fishGmsPerPouch" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" autoComplete="off"  onkeyup="calcProductRatePerBatch();"> </TD>
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
																														<!--TD class="listing-item" align="center"><input type="submit" name="cmdReview" class="button" value="Review"
																														</TD-->
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
																									<!-- Dynamic row starts here -->
																									<tr>
																										<TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3">
																											<table cellspacing="1" cellpadding="3" id="tblAddIng" class="newspaperType">
																												<tr align="center" style="line-height:normal;">
																													<th style="padding-left:5px; padding-right:5px;">Ingredient</th>
																													<th style="padding-left:5px; padding-right:5px;">Rs/<br>Kg</th>
																													<th nowrap style="padding-left:5px; padding-right:5px;">Quantity<br></th>
																													<th style="padding-left:5px; padding-right:5px;">%/<br>Batch</th> 
																													<th nowrap style="padding-left:5px; padding-right:5px;">Rs/<br>Batch</th>	
																													<th nowrap style="padding-left:5px; padding-right:5px;"> Fixed<br/> Qty</th>
																													<th style="padding-left:5px; padding-right:5px;">Gms/<br>Pouch</th>
																													<th style="padding-left:5px; padding-right:5px;">Rs/<br>Pouch</th>
																													<th style="padding-left:5px; padding-right:5px;">%Cost/<br>Pouch</th>
																													<th>&nbsp;</th>
																												</tr>
																												<?php
																												$rowSize = 0;	
																												if ($editRecipeId)  {
																													$productRecs = $recipeMasterObj->fetchAllIngredients($editRecipeId);
																													$rowSize = sizeof($productRecs);		
																												$lastPrice = 0;
																												$m = 0;
																												$displayIngredient = "";	
																												foreach ($productRecs as $rec) {			
																														$editIngredientId = $rec[2];
																														//$selIngType	  = $rec[13]; 
																														//$displayIngredient    = trim($selIngType.'_'.$editIngredientId);	
																														
																														$editQuantity		= $rec[3];
																														$fixedQtyChk		= $rec[4];
																														$rsPerKg				= $rec[5];	
																														$checked = "";
																														$styleDisplay = "";
																														if ($fixedQtyChk=='Y') {
																															$checked= "Checked";
																															$styleDisplay = "display:block";
																														} else {
																															$styleDisplay = "display:none";
																														}
																														// Refer values
																														$percentagePerBatch	= $rec[6];
																														$ratePerBatch		= $rec[7];
																														$ingGmsPerPouch		= $rec[8];
																														$ratePerPouch		= $rec[9];
																														$ratePerKg	= $rec[11];	
																														$ingRateId	= $rec[12];	
																														$percentageCostPerPouch	= $rec[10];	
																														
																													?>
																													<tr bgcolor="#E8EDFF" align="center" id="row_<?=$m?>">
																													   <td style="padding-left:5px; padding-right:5px;">
																															<select name="selIngredient_<?=$m?>" id="selIngredient_<?=$m?>" onchange="xajax_getIngRate(document.getElementById('selIngredient_<?=$m?>').value,<?=$m?>);calcProductRatePerBatch();">			
																																<option value="">-- Select --</option>
																																 <?
																																$ingredientId = "";				
																																foreach ($ingredientRecords as $ir) {
																																	$ingredientId	= $ir[0]; 
																																	$ingredientName = $ir[1];
																																	$selected	=	"";
																																	if ($editIngredientId==$ingredientId) $selected = "selected";
																																 ?>
																																<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
																																<? }?>
																															</select>
																														</td>
																														
																														<td style="padding-left:5px; padding-right:5px;">
																															<input name="rsperKg_<?=$m?>" type="text" id="rsperKg_<?=$m?>" value="<?=$rsPerKg;?>" size='6' style="text-align:right" readonly autoComplete="off">
																															<input name='ingRateId_<?=$m?>' type='hidden' id='ingRateId_<?=$m?>'  value='<?=$ingRateId?>' size='6' style='text-align:right'  autoComplete='off'>
																														</td>
																														<td style="padding-left:5px; padding-right:5px;">
																															<input name="quantity_<?=$m?>" type="text" id="quantity_<?=$m?>" value="<?=$editQuantity;?>" size='6' style="text-align:right" onkeyup="calcProductRatePerBatch();" autoComplete="off">
																														</td>
																														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
																															<input type="text" name="percentagePerBatch_<?=$m?>" id="percentagePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
																														</td>
																														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
																															<input type="text" name="ratePerKg_<?=$m?>" id="ratePerKg_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$ratePerKg?>" size="5">
																														</td>
																														<td style="padding-left:5px; padding-right:5px;">
																															<input name="fixedQtyChk_<?=$m?>" type="checkbox" id="fixedQtyChk_<?=$m?>" value="Y" size='6' class="chkBox" <?=$checked?> onClick="hidFixedQtyDiv();calcProductRatePerBatch();">
																														</td>
																														<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
																															<input type="text" name="ingGmsPerPouch_<?=$m?>" id="ingGmsPerPouch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$ingGmsPerPouch?>" size="5">
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
																										<!--<tr><TD height="5"></TD></tr>-->
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
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RecipeMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRecipeMaster(document.frmRecipeMaster);">	
																					</td>
																				<?} else{?>
																					<td  colspan="4" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RecipeMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRecipeMaster(document.frmRecipeMaster);">&nbsp;&nbsp;			</td>
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
													<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Product</td>
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
													<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Category</td>
													<td style="padding-left:2px;padding-right:2px;">
														<select name="selCategory" id="selCategory">
															<option value=''>--Select All--</option>
															<?php
															if (sizeof($categoryRecords)>0) {	
																foreach ($categoryRecords as $cr) {
																	$catId	= $cr[0];
																	$catName	= stripSlash($cr[1]);
																	$selected = "";
																	if ($selCategoryId==$catId) $selected = "Selected";
															?>	
															<option value="<?=$catId?>" <?=$selected?>><?=$catName?></option>
															<?php
																}
															}
															?>
														</select>
													</td>
													<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Cusine</td>
													<td style="padding-left:2px;padding-right:2px;">
														<select name="selCusine" id="selCusine">
														<? if($selCusineId== "indian") 
														{
															$selIndian="selected";
															$selChinese="";
														}
														else if($selCusineId== "chinese") 
														{
															$selIndian="";
															$selChinese="selected";
														}
														?>
															<option value=''>--Select All--</option> 
															<option value='indian' <?=$selIndian?>>Indian</option>
															<option value='chinese' <?=$selChinese?>>Chinese</option>
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
										<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRecipeMaster.php?selProductCategory=<?=$selProductCategoryId?>&selCategoryId=<?=$selCategoryId?>&selCusineId=<?=$selCusineId?>',700,600);"><?}?>&nbsp;<input type="submit" value=" Refresh " class="button"  name="cmdRefresh" onClick="this.form.submit();" > </td>
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
																$nav.= " <a href=\"RecipeMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"RecipeMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"RecipeMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
										<th style="padding-left:10px; padding-right:10px;">Approval</th>
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
									$approval=$pmr[11];
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
										<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RecipeMaster.php';"><? } ?>
									</td>
									<? }?>

									<td width="45" align="center" >
										<?php if ($approval==0){ ?>
										<input type="submit" value="not approved" name="btnApproval" onClick="assignValue(this.form,<?=$productId;?>,'confirmId');"  >
										<?php } else if ($approval==1){?>
										<input type="submit" value="approved" name="btnRlApproval" onClick="assignValue(this.form,<?=$productId;?>,'confirmId');"  >
										<?php }?>
									</td>
									<? if ($confirm==true){?>
									<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
										<?php if ($approval==1){ ?>
										<?php if ($active==0){ ?>
										<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$productId;?>,'confirmId');"  >
										<?php } else if ($active==1){?>
										<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$productId;?>,'confirmId');"  >
										<?php }?>
										<? }?>
										<? } ?>
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
													$nav.= " <a href=\"RecipeMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
											}
										}
										if ($pageNo > 1) {
											$page  = $pageNo - 1;
											$prev  = " <a href=\"RecipeMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
										} else {
											$prev  = '&nbsp;'; // we're on page one, don't print previous link
											$first = '&nbsp;'; // nor the first page link
										}

										if ($pageNo < $maxpage) {
											$page = $pageNo + 1;
											$next = " <a href=\"RecipeMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
											<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRecipeMaster.php?selProductCategory=<?=$selProductCategoryId?>&selCategoryId=<?=$selCategoryId?>&selCusineId=<?=$selCusineId?>',700,600);"><?}?>&nbsp;<input type="submit" value=" Refresh " class="button"  name="cmdRefresh" onClick="this.form.submit();" >
									</td>
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
	<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Recipe Category" onclick="parent.openTab('RecipeMainCategory.php');">Recipe Category</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<!--<a href="###" class="link1" onclick="parent.openTab('RecipeSubCategory.php');">Recipe Sub Category</a>-->
	</td>		
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
<? if ($addMode && !$selRecipeId) {?>
<script language="JavaScript">
	window.onLoad = addNewIngItem();
</script>
<? }?>

	<?php 
		if ($selRecipeId!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_chkPCodeExist('<?=$productCode?>', '<?=(!selRecipeId)?$editRecipeId:""?>');
	</script>
	<?php
		}
	?>
<?
if($editMode)
{
	if($recipeCode!="")
	{
		//echo "hii";
		
		$i=0;
		//printr($productRecs);
		foreach($productRecs as $rs)
		{
			$ingredientId=$rs[2];
	
?>
<script language="javascript">
 xajax_getIngRate('<?=$ingredientId?>','<?=$i?>');
//calcProductRatePerBatch();
</script>
<?
$i++;
		}
	}
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
	//ensureInFrameset(document.frmRecipeMaster);
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