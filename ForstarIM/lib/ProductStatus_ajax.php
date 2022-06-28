<?php
//require_once("ProductStatus_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

	$xajax = new xajax();	
//$xajax->configure("debug", true);

	class NxajaxResponse extends xajaxResponse
	{		
		function addDropDownOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}

		function addOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}	

					
	}

	# Get Product wise Distributor Recs
	function getDistributorRecs($productId, $rowId, $cDistId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$productStatusObj	= new ProductStatus($databaseConnect);
		$distributorRecords	= $productStatusObj->getDistributorMarginRecs($productId);
		$objResponse->addOptions("selDistributor_".$rowId, $distributorRecords, $cDistId);
		return $objResponse;
	}

	# Get Dist wise State List
	function getDistStateList($distributorId, $rowId, $cStateId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$productStatusObj	= new ProductStatus($databaseConnect);
		$stateRecords		= $productStatusObj->filterStateList($distributorId);
		$objResponse->addOptions("selState_".$rowId, $stateRecords, $cStateId);
		return $objResponse;
	}

	# Get Distributor Recs
	function getDistributors($selStateId, $cDistId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$productStatusObj	= new ProductStatus($databaseConnect);
		$distributorRecs 	= $productStatusObj->getDistributorRecs($selStateId);
		//$objResponse->alert($distributorRecs);
		/*foreach ($distributorRecs as $key=>$val) {
			$objResponse->alert($val[1]);
		}
		*/
		/*
		$resultArr = array(''=>'-- Select All --');
		$arrstr="";
		foreach ($distributorRecs as $key=>$val) {
		//$objResponse->alert($val[1]);
			if($arrstr!=""){
			$arrstr = $arrstr.",'".$val[0]."'=>"."'".$val[1]."'" ;
			}
			else{
				$arrstr = "'".$val[0]."'=>"."'".$val[1]."'";
				
			}
		}
		
		$resultArr = array_merge($resultArr, array($arrstr));
		
		$objResponse->alert($resultArr);
		*/

		$objResponse->addOptions("selDistributor", $distributorRecs, $cDistId);
		if ($cDistId=="") $objResponse->script("xajax_distributorWiseMgnRL('');");
		return $objResponse;
	}

	function updateProductMgmt($selStateId, $distributorId, $productId, $userId, $rowId, $cityId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$productStatusObj	= new ProductStatus($databaseConnect);
		
		if ($distributorId=="") { 
			$distributorRecs 	= $productStatusObj->getDistributorRecs($selStateId);
			foreach ($distributorRecs as $seldistId=>$selDistName) {				
				if ($seldistId!="") {					
					$productStatusIns = $productStatusObj->addProductStatusRecs($productId, $seldistId, $selStateId, 'Y', $userId, $cityId);			
				}
			}
		} else {
			$invoiceRecs = $productStatusObj->chkProductInUse($selStateId, $distributorId, $productId, $cityId);
			if (!$invoiceRecs) {
				$productStatusIns = $productStatusObj->addProductStatusRecs($productId, $distributorId, $selStateId, 'Y', $userId, $cityId);
			}			
		}
		if ($productStatusIns) {
			$disVar = "<img src='images/x.png' onclick=\"xajax_removeProductMgmt('$selStateId','$distributorId','$productId','$userId','$rowId', '$cityId');\" />";
			$objResponse->assign("statusRow_".$rowId, "innerHTML", $disVar);
		} else if ($invoiceRecs) {
			$objResponse->alert("Failed to inactive.\nProduct is linked with the respective invoices,\n $invoiceRecs.");
		}
		return $objResponse;			
	}

	function removeProductMgmt($selStateId, $distributorId, $productId, $userId, $rowId, $cityId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$productStatusObj	= new ProductStatus($databaseConnect);
		
		if ($distributorId=="") { 
			$distributorRecs 	= $productStatusObj->getDistributorRecs($selStateId);
			foreach ($distributorRecs as $seldistId=>$selDistName) {			
				if ($seldistId!="") {					
					$delProductStatus = $productStatusObj->deleteProductStatus($productId, $seldistId, $selStateId);
				}
			}
		} else {
			//$objResponse->alert("$selStateId, $distributorId, $productId, $userId, $rowId, $cityId");
			$delProductStatus = $productStatusObj->deleteProductStatus($productId, $distributorId, $selStateId, $cityId);
		}
		if ($delProductStatus) {
			$disVar = "<img src='images/y.png' onclick=\"xajax_removeProductMgmt('$selStateId','$distributorId','$productId','$userId','$rowId', '$cityId');\" />";
			$objResponse->assign("statusRow_".$rowId, "innerHTML", $disVar);
		}
		return $objResponse;
	}


	function removeDistMargin($distMarginId, $distMarginStateEntryId, $rowId, $selDistributorId, $mproductId, $selStateId, $selRateListId, $xjxRedirectUrl)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$productStatusObj	= new ProductStatus($databaseConnect);
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		
		# Get Dist Margin State Recs
		/*
		$getDMStateRecs = $productStatusObj->getDistMarginStateRecs($mproductId, $selDistributorId, $selStateId, $selRateListId);
		*/	
			# Checking Margin Used
			$marginUseChk = $distMarginStructureObj->chkDistMgnUsed($distMarginStateEntryId);
			//if ($marginUseChk) $distMgnUsed = true;

			if ($distMarginStateEntryId && !$marginUseChk) {
				$delDistMarginEntry = $distMarginStructureObj->delDistMarginEntryRec($distMarginStateEntryId);
				//StateRec
				$delDistMarginStateEntryRec = $distMarginStructureObj->delDistMarginStateEntryRec($distMarginStateEntryId);
				
				$chkDistStateRecExist = $distMarginStructureObj->chkDistStateRecSize($distMarginId);
				if (!$chkDistStateRecExist) {
					$distMarginRecDel = $distMarginStructureObj->deleteDistMarginStructure($distMarginId);
				}
			}		
		
		if ($marginUseChk) {
			$objResponse->alert("Failed to Remove Margin.\nThe selected Product Margin is already in use.");
		} else {			
			$disAssignMsg = "<a href='$xjxRedirectUrl' class='link1' title='Click here to assign a product'>Not Assigned  </a>";
			$objResponse->assign("assignRow_".$rowId, "innerHTML", $disAssignMsg);	
			$objResponse->assign("statusRow_".$rowId, "innerHTML", "");
			$objResponse->assign("productAssign_".$rowId, "value", "");		
		}
		return $objResponse;
	}


	/**
	* Fetch All Product Group
	* (Using in Prouct MRP Master)
	*/
	function getProductGroupExist($productStateId, $selId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$manageProductObj	= new ManageProduct($databaseConnect); 
		if ($productStateId) {
			# Checking Prouct Group Exist
			$productGroupExist = $manageProductObj->checkProductGroupExist($productStateId);
			# Product Group Records
			$productGroupRecords = $manageProductObj->filterProductGroupList($productGroupExist);
			
			$objResponse->addOptions("selProductGroup", $productGroupRecords, $selId);		
		}
		return $objResponse;			
	}

	# Distributor Rate List recs
	function distributorWiseMgnRL($distributorId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$productStatusObj	= new ProductStatus($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);

		$distRLRecs = $productStatusObj->filterDistMgnRLRecs($distributorId);
		$selRateListId 	= $distMarginRateListObj->latestRateList($distributorId);
		$objResponse->addOptions("distributorMgnRateList", $distRLRecs, $selRateListId);

		sleep(0.4);
		return $objResponse;
	}




$xajax->register(XAJAX_FUNCTION, 'getDistributorRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getDistStateList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getDistributors', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateProductMgmt', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'removeProductMgmt', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'removeDistMargin', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProductGroupExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'distributorWiseMgnRL', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>