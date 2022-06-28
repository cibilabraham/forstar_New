<?php
//require_once("lib/databaseConnect.php");
//require_once("RMProcurmentOrder_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure('statusMessages', true);
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
			
		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	}
	
	function getRecipeCost($recipeId, $fixedWt, $gravyWt)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$recipeMasterObj			=	new RecipeMaster($databaseConnect);
		
		$recipeCost					=	$recipeMasterObj->getRecipeCost($recipeId);
		
		if($recipeCost!="")
		{
			$fixedCostperKg = $recipeCost[0];
			$gravyCostperKg = $recipeCost[1];
			
			$objResponse->alert($fixedCostperKg);
			$objResponse->alert($gravyCostperKg);
			$mainIngredientCost = number_format((float)$fixedCostperKg*$fixedWt, 2, '.', '');
			$gravyCost          = number_format((float)$gravyCostperKg*$gravyWt, 2, '.', '');
			
			$objResponse->assign("mainIngredntCost","value","$mainIngredientCost");
			$objResponse->assign("gravyCost","value","$gravyCost");
			
			$objResponse->script("calcRawMaterialCost();");
		}
		else 
		{
			$objResponse->assign("mainIngredntCost","value","");
			$objResponse->assign("gravyCost","value","");
		}
		
		return $objResponse;
	}
	
	function getProductionMatrix($netWgt)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$productionMatrixObj		=	new ProductionMatrix($databaseConnect); 
		
		$netWt = number_format((float)$netWgt,3,'.','');
		$objResponse->alert("$netWt");
		$productionName = $productionMatrixObj->getProductionName($netWt);
		
		if(sizeof($productionName)>0)
		{
			addDropDownOptions("productionName", $productionName, '', $objResponse);
		}
		
		return $objResponse;
	}
	
	function getPackingMatrix($netWgt)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$packingMatrixObj		=	new PackingMatrix($databaseConnect);
		
		$netWt = number_format((float)$netWgt,3,'.','');
		//$objResponse->alert("$netWt");
		$packingName = $packingMatrixObj->getPackingName($netWt);
		
		if(sizeof($packingName)>0)
		{
			addDropDownOptions("packingName", $packingName, '', $objResponse);
		}
		
		return $objResponse;
	}
	
	function calcPerPouchCost($productionId)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$productionMatrixObj		=	new ProductionMatrix($databaseConnect); 
		$processMasterObj           =   new ProcessMaster($databaseConnect);
		$productionPowerObj			=	new ProductionPower($databaseConnect);
		$productionWorkingHoursObj		=	new ProductionWorkingHours($databaseConnect);
		$productionOtherCostObj			=	new ProductionOtherCost($databaseConnect);
		
		$processType = $productionMatrixObj->getProcessType($productionId);
		$processValues = $processMasterObj->getProcessValues($processType[0]);
		$fixedManpowerCost = $productionPowerObj->getFixdManpowerCost();
		$fixedStaffCost = $productionPowerObj->getFixdStaffCost();
		$noOfWrkngDaysMnth = $productionWorkingHoursObj->getProductionWorkingHours();
		$testingPouchUnit = $productionOtherCostObj->getProductionOthersCost();
		
		//Fixed Manpower Cost Per Day
		if($fixedManpowerCost!="")
		{
			$objResponse->assign("fixedManpowerCost","value","$fixedManpowerCost[0]");
		}
		
		//Fixed Staff Cost Per Day
		if($fixedStaffCost!="")
		{
			$objResponse->assign("fixedStaffCost","value","$fixedStaffCost[0]");
		}
		
		//No of Working Days in Month
		if($noOfWrkngDaysMnth!="")
		{
			$objResponse->assign("noOfDaysMnth","value","$noOfWrkngDaysMnth[9]");
			$objResponse->assign("noOfDaysYear","value","$noOfWrkngDaysMnth[8]");
		}
		
		//Pouches for testing per batch
		if($testingPouchUnit!="")
		{
			$objResponse->assign("testingPouchUnit","value","$testingPouchUnit[4]");
			$objResponse->assign("pdtHoldingCost","value","$testingPouchUnit[7]");
		}
		
		//Fuel Values From Process Master
		if($processValues!="")
		{
			$objResponse->assign("waterCostValue","value","$processValues[0]");
			$objResponse->assign("dieselCostValue","value","$processValues[1]");
			$objResponse->assign("electCostValue","value","$processValues[2]");
			$objResponse->assign("gasCostValue","value","$processValues[3]");
		}
		else
		{
			$objResponse->assign("waterCostValue","value","");
			$objResponse->assign("dieselCostValue","value","");
			$objResponse->assign("electCostValue","value","");
			$objResponse->assign("gasCostValue","value","");
		}
		
		//Per Batch Values From Production Matrix
		if($processType!="")
		{
			$objResponse->assign("waterCostperBatch","value","$processType[1]");
			$objResponse->assign("dieselCostperBatch","value","$processType[2]");
			$objResponse->assign("electCostperBatch","value","$processType[3]");
			$objResponse->assign("gasCostperBatch","value","$processType[4]");
			$objResponse->assign("mainConsCost","value","$processType[5]");
			$objResponse->assign("variblManpowerCost","value","$processType[6]");
		}
		else
		{
			$objResponse->assign("waterCostperBatch","value","");
			$objResponse->assign("dieselCostperBatch","value","");
			$objResponse->assign("electCostperBatch","value","");
			$objResponse->assign("gasCostperBatch","value","");
		}
		
		$objResponse->script("calcPerPouchCost();");
		return $objResponse;
	}
	
	function getPaymentTerms($customerId)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$distributorMasterObj		=	new DistributorMaster($databaseConnect);
		
		$creditPeriod = $distributorMasterObj->getCreditPeriod($customerId);
		if($creditPeriod!="")
		{
			$objResponse->assign("customerCreditPeriod","value","$creditPeriod[1]");
		}
		else
		{
			$objResponse->assign("customerCreditPeriod","value","");
		}
		return $objResponse;
	}
	
	function getPackingCost($packingId)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$packingMatrixObj		=	new PackingMatrix($databaseConnect);
		
		$packingCost = $packingMatrixObj->getInnerOuterPackingCost($packingId);
		
		if($packingCost!="")
		{
			$objResponse->assign("packingInnerCost","value","$packingCost[1]");
			$objResponse->assign("packingOuterCost","value","$packingCost[2]");
		}
		else
		{
			$objResponse->assign("packingInnerCost","value","");
			$objResponse->assign("packingOuterCost","value","");
		}
		
		$objResponse->script("calcPackingCost();");
		
		return $objResponse;
	}
	
	function getPaymentDuration($paymentId)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$paymentMasterObj	=	new PaymentMaster($databaseConnect);

		$paymentDuration = $paymentMasterObj->getPaymntDurtn($paymentId);
		//$objResponse->alert($paymentDuration[1]);
		if($paymentDuration!="")
		{
			$objResponse->assign("paymntDuratn","value","$paymentDuration[1]");
		}
		else
		{
			$objResponse->assign("paymntDuratn","value","");
		}
		
		$objResponse->script("calculateHoldingCost();");
		return $objResponse;
	}
	
	function getExportName($exportId)
	{
		$objResponse 			    = 	new xajaxResponse();
		$databaseConnect 		    = 	new DatabaseConnect();
		$exportMasterObj = new ExportMaster($databaseConnect);
		
		$exportName = $exportMasterObj->getExportMaster($exportId);
	}
	
	$xajax->register(XAJAX_FUNCTION, 'getRecipeCost', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getProductionMatrix', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getPackingMatrix', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'calcPerPouchCost', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getPaymentTerms', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getPackingCost', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getExportName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getPaymentDuration', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	$xajax->ProcessRequest();
?>