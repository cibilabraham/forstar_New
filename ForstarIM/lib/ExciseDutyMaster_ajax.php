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

	function changeRLDate($rateListId, $startDate)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$exciseDutyMasterObj	= new ExciseDutyMaster($databaseConnect); 

		 $startDate = mysqlDateFormat($startDate);

		$rateListName	= "Ex.Duty"."-".date("dMy",strtotime($startDate));
		
		#Check valid rate list
            	$recExist = $exciseDutyMasterObj->chkValidEDRLDate($startDate, $rateListId);
            	if (!$recExist) {
			$updateRL = $exciseDutyMasterObj->updateEDRLRec($rateListId, $startDate, $rateListName);
			if ($updateRL) {
				$objResponse->alert("Successfully updated the start date.");
				$objResponse->script("location.reload( true );");
			}
		} else $objResponse->alert("Failed to update start date.\nA valid rate list is existing for the selected date");
		return $objResponse;
	}

	function updateActiveFlag($edActive)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$exciseDutyMasterObj	= new ExciseDutyMaster($databaseConnect); 
		
		$updateEDFlag = $exciseDutyMasterObj->updateEDFlag($edActive);
		if ($updateEDFlag && $edActive=='N') $objResponse->assign("edActiveFlag", "innerHTML", "<img onclick=\"uptdActiveFlag('Y');\" src=\"images/x.png\" style=\"cursor:pointer;\"/>");
		else  $objResponse->assign("edActiveFlag", "innerHTML", "<img onclick=\"uptdActiveFlag('N');\" src=\"images/y.png\" style=\"cursor:pointer;\"/>");
		
		return $objResponse;
	}

	function deleteEDRateList($rateListId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$exciseDutyMasterObj	= new ExciseDutyMaster($databaseConnect); 

		$chkRateListInUse	= $exciseDutyMasterObj->chkEDRLInUse($rateListId);
		if (!$chkRateListInUse) {
			$delRateList = $exciseDutyMasterObj->deleteEDRLRec($rateListId);
			if ($delRateList) {
				$objResponse->alert("Rate list deleted successfully");
				$objResponse->script("location.reload( true );");
			}
		} else {
			$objResponse->alert("Failed to delete rate list.\nRate list is already in use.");
		}

		return $objResponse;
	}


$xajax->registerFunction("getProductGroupExist");
$xajax->registerFunction("changeRLDate");
$xajax->registerFunction("updateActiveFlag");
$xajax->registerFunction("deleteEDRateList");


$xajax->ProcessRequest();
?>