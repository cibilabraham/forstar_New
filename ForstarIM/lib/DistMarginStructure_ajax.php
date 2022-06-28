<?php
//require_once("DistMarginStructure_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

	$xajax = new xajax();	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   				if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}				
	}
	
	function getProductGroupExist($productStateId, $rowId, $selId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		# Checking Prouct Group Exist
		$productGroupExist = $distMarginStructureObj->checkProductGroupExist($productStateId);
		# Product Group Records
		$productGroupRecords = $distMarginStructureObj->filterProductGroupList($productGroupExist);
		$objResponse->addCreateOptions("selProductGroup", $productGroupRecords,$selId);
		$objResponse->assign("productStateGroup", "value", $productGroupExist);
		$objResponse->script("getStateVatPercent();");
		return $objResponse;			
	}

	# State Exist
	function chkEntryExist($selDistributorId, $selProductId, $distMarginRateList, $mode, $currentId, $pendingStateId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		$chkRecExist = false;
		if ($pendingStateId=="") $chkRecExist = $distMarginStructureObj->checkEntryExist($selDistributorId, $distMarginRateList, $selProductId, $currentId);
		if ($chkRecExist) {
			$objResponse->assign("divRecExistTxt", "innerHTML", "Please make sure the selected record is not existing.");
			$objResponse->script("disableDistMgnStructButton($mode);");
		} else if ($pendingStateId=="")  {
			$objResponse->assign("divRecExistTxt", "innerHTML", "");
			$objResponse->script("enableDistMgnStructButton($mode);");
		}
		return $objResponse;
	}

	function chkProductRecsExist($selPCategory, $selPState, $selPGroup, $mode, $distributorId, $pendingStateId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		if ($pendingStateId=="")
			$chkRecExist  = $distMarginStructureObj->getProductRecords($selPCategory, $selPState, $selPGroup);		
		if (!sizeof($chkRecExist) && $distributorId!="" && $selPCategory!="") {
			$objResponse->assign("divProdRecExistTxt", "innerHTML", "Products not existing for the selected criteria.");
			$objResponse->script("disableDistMgnStructButton($mode);");
		} else  {
			$objResponse->assign("divProdRecExistTxt", "innerHTML", "");
			$objResponse->script("enableDistMgnStructButton($mode);");
		}
		return $objResponse;
	}

	// $distributorMgnRateList -> from product management
	function getDistWiseAvgMargin($selDistributor, $selId, $distributorMgnRateList)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		
		if ($distributorMgnRateList) $selRateList = $distributorMgnRateList;
		else $selRateList 	= $distMarginRateListObj->latestRateList($selDistributor);

		$data		= $distMarginStructureObj->getDistwiseMarginRecs($selDistributor, $selRateList);
		
		$objResponse->addCreateOptions("selDistMargin", $data, $selId);
		return $objResponse;
	}

	function getStateWiseVat($productId, $stateId, $columnCount, $rowId, $selDistributor, $distMarginRateList)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		# Get Tax Percent
		$taxPercent = $distMarginStructureObj->getDistWiseTaxPercent($productId, $stateId, $selDistributor, $distMarginRateList);
		if ($taxPercent!=0) $objResponse->assign("vat_$rowId", "value", $taxPercent);		
		else $objResponse->assign("vat_$rowId", "value", 0);
		return $objResponse;
	}

	// When Add to Category Wise 
	function getStateVatPercent($distributorId, $stateId, $rowId, $categoryId, $pStateId, $pGroupId, $distMarginRateList)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);		
		# Get Tax Percent
		$taxPercent = $distMarginStructureObj->getDistWiseTPercent($distributorId, $stateId, $categoryId, $pStateId, $pGroupId, $distMarginRateList);			
		if ($taxPercent!=0) $objResponse->assign("vat_$rowId", "value", $taxPercent);		
		else $objResponse->assign("vat_$rowId", "value", 0);
		return $objResponse;
	}

	# Update Pending SO Rec
	function updateRevisedMgnStruct($distributorId=null,$rateListId=null)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$salesOrderObj		= new SalesOrder($databaseConnect);
		$taxMasterObj		= new TaxMaster($databaseConnect); 
		$marginStructureObj	= new MarginStructure($databaseConnect);
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$manageRateListObj	= new ManageRateList($databaseConnect);
		$changesUpdateMasterObj	= new ChangesUpdateMaster($databaseConnect, $salesOrderObj, $taxMasterObj, $marginStructureObj, $distMarginStructureObj, $distMarginRateListObj, $manageRateListObj);
		//$distributorId=null
		$updateDistMgn = $changesUpdateMasterObj->updateDistributorMgnStructRecs($distributorId, $rateListId);
		if ($updateDistMgn) {
			$objResponse->alert("Successfully updated all distributor Margin Structure.");
			$objResponse->script("document.getElementById('frmDistMarginStructure').submit();");
		}
		return $objResponse;
	}

	# Get City Wise Octroi Percent
	function getOctroiPercent($rowId, $cityId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		$cityMasterObj		= new CityMaster($databaseConnect);

		# Get Octroi Percent		
		$octroiPercent = $cityMasterObj->getOctroiPercent($cityId);
		if ($octroiPercent!=0) $objResponse->assign("octroi_$rowId", "value", $octroiPercent);		
		else $objResponse->assign("octroi_$rowId", "value", 0);
		return $objResponse;
	}



$xajax->register(XAJAX_FUNCTION, 'getProductGroupExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkEntryExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkProductRecsExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getDistWiseAvgMargin', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getStateWiseVat', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getStateVatPercent', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateRevisedMgnStruct', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getOctroiPercent', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>