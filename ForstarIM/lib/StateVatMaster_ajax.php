<?php
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
		$stateVatMasterObj	= new StateVatMaster($databaseConnect);
		# Checking Prouct Group Exist
		$productGroupExist = $stateVatMasterObj->checkProductGroupExist($productStateId);
		# Product Group Records
		$productGroupRecords = $stateVatMasterObj->filterProductGroupList($productGroupExist);
		$objResponse->addCreateOptions("selProductGroup_".$rowId, $productGroupRecords,$selId);
		$objResponse->assign("productStateGroup_".$rowId, "value", $productGroupExist);
		return $objResponse;			
	}

	# State Exist
	function chkSelStateExist($stateId, $mode, $stateVatId, $stateVatRateListId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$stateVatMasterObj	= new StateVatMaster($databaseConnect);
		$stateVatRateListObj	= new StateVatRateList($databaseConnect);

		if ($stateVatRateListId!="") $selRateList = $stateVatRateListId;
		else 	$selRateList = $stateVatRateListObj->latestRateList($stateId);

		$chkStateExist = $stateVatMasterObj->checkStateExist($stateId, $stateVatId, $selRateList);

		if ($chkStateExist) {
			$objResponse->assign("divStateIdExistTxt", "innerHTML", "Please make sure the selected state is not existing.");
			$objResponse->script("disableStateVatButton($mode);");
		} else  {
			$objResponse->assign("divStateIdExistTxt", "innerHTML", "");
			$objResponse->script("enableStateVatButton($mode);");
		}
		return $objResponse;
	}

	# Get State Vat Rate List Records
	function getStateVatRateList($selStateId, $mode, $cRateListId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$stateVatMasterObj	= new StateVatMaster($databaseConnect);
		$stateVatRateListObj	= new StateVatRateList($databaseConnect);
		if ($mode==1)	$selRateList = $stateVatRateListObj->latestRateList($selStateId);
		else 		$selRateList = $cRateListId;

		$stateVatRecs	= $stateVatRateListObj->getStateWiseVatFilterRateListRecs($selStateId);	
		$objResponse->addCreateOptions("stateVatRateList", $stateVatRecs, $selRateList);
		return $objResponse;
	}

	function getCopyFromStateVatRateList($selStateId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$stateVatMasterObj	= new StateVatMaster($databaseConnect);
		$stateVatRateListObj	= new StateVatRateList($databaseConnect);
		//$selRateList = $stateVatRateListObj->latestRateList($selStateId);		

		$stateVatRecs	= $stateVatRateListObj->getStateWiseVatFilterRateListRecs($selStateId);	
		$objResponse->addCreateOptions("copyFromStateVatRateList", $stateVatRecs, $selRateList);
		return $objResponse;
	}


$xajax->registerFunction("getProductGroupExist");
$xajax->registerFunction("chkSelStateExist");
$xajax->registerFunction("getStateVatRateList");
$xajax->registerFunction("getCopyFromStateVatRateList");


$xajax->ProcessRequest();
?>