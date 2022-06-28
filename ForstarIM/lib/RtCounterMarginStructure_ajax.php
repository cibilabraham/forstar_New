<?php
require_once("lib/databaseConnect.php");
require_once("RtCounterMarginStructure_class.php");
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
	
	/*
	
	*/
	
	function chkProductRecsExist($selPCategory, $selPState, $selPGroup, $mode)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		
		$chkRecExist  = $distMarginStructureObj->getProductRecords($selPCategory, $selPState, $selPGroup);		
		if (!sizeof($chkRecExist) && $selPCategory!="") {
			$objResponse->assign("divProdRecExistTxt", "innerHTML", "Products not existing for the selected criteria.");
			$objResponse->script("disableRtCounterMgnStructBtn($mode);");
		} else  {
			$objResponse->assign("divProdRecExistTxt", "innerHTML", "");
			$objResponse->script("enableRtCounterMgnStructBtn($mode);");
		}
		return $objResponse;
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
		//$objResponse->assign("productStateGroup", "value", $productGroupExist);
		return $objResponse;
	}
	

	function getRtCounterMgnRateList($rtCounterId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$rtCounterMarginStructureObj	= new RetailCounterMarginStructure($databaseConnect);
		$rtCountMarginRateListObj	= new RetailCounterMarginRateList($databaseConnect);
		$currentRateListId = $rtCountMarginRateListObj->latestRateList($rtCounterId);
		$objResponse->assign("retCtMarginRateList", "value", $currentRateListId);
		return $objResponse;
	}

	# Entry Exist
	function chkEntryExist($rtCounterId, $selProductId, $currentRateListId, $mode, $currentId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$rtCounterMarginStructureObj	= new RetailCounterMarginStructure($databaseConnect);
		//$objResponse->alert("$rtCounterId, $selProductId, $currentRateListId, $mode, $currentId");
		if ($mode==1) {
			$chkRecExist = $rtCounterMarginStructureObj->checkEntryExist($rtCounterId, $currentRateListId, $selProductId, $currentId);
			if ($chkRecExist) {
				$objResponse->assign("divRecExistTxt", "innerHTML", "Please make sure the selected record is not existing.");
				$objResponse->script("disableRtCounterMgnStructBtn($mode);");
			} else  {
				$objResponse->assign("divRecExistTxt", "innerHTML", "");
				$objResponse->script("enableRtCounterMgnStructBtn($mode);");
			}
		} else if ($mode==2) {
			$rtCounterMarginId = $rtCounterMarginStructureObj->getRtCtMarginRec($rtCounterId, $selProductId, $currentRateListId);
			$objResponse->assign("hidDistMarginStructureId", "value", $rtCounterMarginId);
		} else {
			$objResponse->assign("hidDistMarginStructureId", "value", "");
		}
		return $objResponse;
	}

	
	
	/*
	function getDistWiseAvgMargin($selDistributor, $selId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);		
		$selRateList 	= $distMarginRateListObj->latestRateList($selDistributor);
		$data		= $distMarginStructureObj->getDistwiseMarginRecs($selDistributor, $selRateList);
		
		$objResponse->addCreateOptions("selDistMargin", $data, $selId);
		return $objResponse;
	}

	function getStateWiseVat($productId, $stateId, $columnCount, $rowId, $selDistributor)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();		
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
				
		$taxType 	= $distMarginStructureObj->getDistTaxType($selDistributor, $stateId);
		if ($taxType=='CST') {
			$taxPercent = $distMarginStructureObj->getCSTPercent();
		} else if ($taxType=='VAT') {
			# State Vat
			$taxPercent = $distMarginStructureObj->getStateWiseVatPercent($productId,$stateId);
		}		
		$objResponse->assign("vat_$rowId", "value", $taxPercent);		
		return $objResponse;
	}

$xajax->registerFunction("getDistWiseAvgMargin");
$xajax->registerFunction("getStateWiseVat");
*/

$xajax->registerFunction("getProductGroupExist");
$xajax->registerFunction("chkProductRecsExist");
//$xajax->registerFunction("getRtCounterMgnRateList");
$xajax->register(XAJAX_FUNCTION,'getRtCounterMgnRateList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("chkEntryExist");
$xajax->register(XAJAX_FUNCTION,'chkEntryExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>