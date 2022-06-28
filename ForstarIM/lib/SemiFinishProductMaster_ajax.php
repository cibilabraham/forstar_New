<?php
require_once("lib/databaseConnect.php");
require_once("SemiFinishProductMaster_class.php");
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
	
	# get Ing rate and Declared Yield
	function getIngRate($ingId, $rowId, $selIngRateListId)
	{
		$sR = explode("_",$ingId);
		$ingId = $sR[1];
		$selIngType = $sR[0];	# ING- Ingredient / SFP - Semi Finished Product
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$ingredientRateListObj   = new IngredientRateList($databaseConnect);
	        $ingredientRateMasterObj = new IngredientRateMaster($databaseConnect);		
		$semiFinishProductObj 	=	new SemiFinishedProduct($databaseConnect);
		//$objResponse->alert($ingId);
		$selRateList = "";
		if ($selIngType=='ING') {	# Ingredient
			if ($selIngRateListId!="") $selRateList = $selIngRateListId;
			else $selRateList = $ingredientRateListObj->latestRateList();
			list($lastPrice,$declYield) = $semiFinishProductObj->getIngredientRate($ingId, $selRateList);	
		} else if ($selIngType=='SFP') {  # Semi Finished Product
			list($lastPrice,$declYield) = $semiFinishProductObj->getSemiFinishRate($ingId);	
		}
		//$objResponse->alert($lastPrice,$declYield);
		$objResponse->assign("lastPrice_".$rowId, "value", $lastPrice);	
		//$objResponse->assign("declYield_".$rowId, "value", $declYield);	
		$objResponse->assign("ingType_".$rowId, "value", $selIngType);	
			
		$objResponse->script("calcProductRatePerBatch();");		
            	return $objResponse;
	}
	
	function productionCost($processHrs, $processMints, $gasHrs, $gasMints, $steamHrs, $steamMints)
	{	
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$semiFinishProductObj 	=	new SemiFinishedProduct($databaseConnect);
		$manageRateListObj	=	new ManageRateList($databaseConnect);
		$productionMatrixMasterObj	= new ProductionMatrixMaster($databaseConnect);		
		$pmmRateList = $manageRateListObj->latestRateList("PMM");
		#Producion matrix Master
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($pmmRateList);

		/* Electric Consumption */
		if ($processHrs!=0 || $processMints!=0) {
			$electricityConsumedHrs = convertHrs($processHrs, $processMints);
			//$objResponse->alert("$electricityConsumedHrs");	
			$costOfElectricConsumptionPerHr	= ($electricConsumptionPerShift/$noOfHoursPerShift);
			$totalElectricConsumption = $electricityConsumedHrs*$costOfElectricConsumptionPerHr;
			$objResponse->assign("electricityConsumptionCost", "value", number_format($totalElectricConsumption,2,'.',''));	
		} else if ($processHrs==0 && $processMints==0) $objResponse->assign("electricityConsumptionCost", "value", 0);
		
		/* Gas Consumption*/
		if ($gasHrs!=0 || $gasMints!=0) {
			$gasConsumedHrs = convertHrs($gasHrs, $gasMints);
			$gasPer19KgCylinderPerHr = $gasPerCylinderPerDay/$noOfHoursPerShift;			
			$costOfCylinderPerKg = $costOfCylinder/19;
			$gasCostPerHr	     = 	$gasPer19KgCylinderPerHr * $costOfCylinderPerKg;
			$totalConsumGasCost  = $gasConsumedHrs * $gasCostPerHr;
			//$objResponse->alert("$costOfCylinderPerKg,$gasCostPerHr,$totalConsumGasCost");	
			$objResponse->assign("gasConsumptionCost", "value", number_format($totalConsumGasCost,2,'.',''));	
		} else if ($gasHrs==0 && $gasMints==0) $objResponse->assign("gasConsumptionCost", "value", 0);
		
		/* Steam Consumption */
		if ($steamHrs!=0 || $steamMints!=0) {
			$steamConsumedHrs = convertHrs($steamHrs, $steamMints);
			$costOfDieselPerHour  = $dieselConsumptionOfBoiler * $dieselCostPerLitre;
			$totalConsumSteamCost = $steamConsumedHrs * $costOfDieselPerHour;
			//$objResponse->alert("$costOfDieselPerHour,$totalConsumSteamCost");
			$objResponse->assign("steamConsumptionCost", "value", number_format($totalConsumSteamCost,2,'.',''));	
		} else if ($steamHrs==0 && $steamMints==0) $objResponse->assign("steamConsumptionCost", "value", 0);
		$objResponse->script("totalProductionCost();");
		return $objResponse;		
	}	

	function convertHrs($hrs, $mints)
	{
		$gHrs 	= number_format(($hrs * 60),2,'.','');
		$gMints = number_format((($mints*60)/100),2,'.','');
		$totalHrs = number_format((($gHrs + $gMints)/60),2,'.','');
		return $totalHrs;
	}

	function getVariableStaffCost($workingHrs, $workingMints, $varStaffTotalCost)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$semiFinishProductObj 	=	new SemiFinishedProduct($databaseConnect);
		$manageRateListObj	=	new ManageRateList($databaseConnect);
		$productionMatrixMasterObj	= new ProductionMatrixMaster($databaseConnect);		
		$pmmRateList = $manageRateListObj->latestRateList("PMM");
		#Producion matrix Master
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($pmmRateList);	

		$varStaffConsumedHrs = convertHrs($workingHrs, $workingMints);

		$varStaffCostPerHr = $varStaffConsumedHrs * ((($varStaffTotalCost/$noOfWorkingDaysInMonth)*$noOfShifts)/$noOfHoursPerShift);
		$objResponse->assign("varStaffPerHrCost", "value", number_format($varStaffCostPerHr,2,'.',''));	
		$objResponse->script("totalProductionCost();");
		return $objResponse;
	}

	function getFixedStaffCost($workingHrs, $workingMints, $noOfStaff)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$semiFinishProductObj 	=	new SemiFinishedProduct($databaseConnect);
		$manageRateListObj	=	new ManageRateList($databaseConnect);
		$productionMatrixMasterObj	= new ProductionMatrixMaster($databaseConnect);
		$productionManPowerObj		= new ProductionManPower($databaseConnect);
		$pmmRateList = $manageRateListObj->latestRateList("PMM");
		#Producion matrix Master
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($pmmRateList);
		
		$mpcRateList = $manageRateListObj->latestRateList("MPC");
		$fixedManPowerRecords = $productionManPowerObj->getFixedManPowerRecords($mpcRateList);
		$totalFMPCost = 0;
		if (sizeof($fixedManPowerRecords)>0) {
			foreach ($fixedManPowerRecords as $fmpr) {
				$fmpTotCost	= $fmpr[5];
				$totalFMPCost += $fmpTotCost;
			}
		}	
		$fixedStaffConsumedHrs = convertHrs($workingHrs, $workingMints);

		$fixedStaffCostPerHr = $noOfStaff* $fixedStaffConsumedHrs * (($totalFMPCost/$noOfWorkingDaysInMonth)/$noOfHoursPerShift);
		$objResponse->assign("fixedStaffCostPerHr", "value", number_format($fixedStaffCostPerHr,2,'.',''));	
		$objResponse->script("totalProductionCost();");
		return $objResponse;
	}


$xajax->registerFunction("getIngRate");
$xajax->registerFunction("productionCost");
$xajax->registerFunction("getVariableStaffCost");
$xajax->registerFunction("getFixedStaffCost");


$xajax->ProcessRequest();
?>