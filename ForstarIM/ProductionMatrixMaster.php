<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err			= "";
	$errDel			= "";	
	$editProdnMxMasterRecId	= "";
	$productionMatrixMasterId = "";
	$noRec			= "";	
	$editMode	= true;
	$addMode	= false;
	
	$recUptd   = false;
	
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	#----------------Rate list--------------------	
	$productionMatrixMaster = "PMM";		

	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList = $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($productionMatrixMaster);			
	#--------------------------------------------

	# CST RATE
	$cstRate = $taxMasterObj->getBaseCst();

		
	# Update / Insert a Record
	if ($p["cmdSaveChange"]!="" && !$recUptd) {		
		$productionMatrixMasterId = $p["hidProductionMatrixMasterId"];

		# Creating a New Rate List IF no Rate List Exist
		if ($selRateList=="") {			
			$selName  = substr("PRODMTRIX", 0,9);	
			$rateListName = $selName."-".date("dMy");
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $cpyRateList, $userId, $productionMatrixMaster, $pageCurrentRateListId);
			if ($rateListRecIns) $selRateList = $manageRateListObj->latestRateList($productionMatrixMaster);	
		}

		# New Rate IF Rate List Exist
		$newRateList	= $p["newRateList"];
		if ($newRateList=='Y' && $selRateList!="") {
			$selName  = substr("PRODMTRIX", 0,9);	
			$rateListName = $selName."-".date("dMy");
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $selRateList, $userId, $productionMatrixMaster, $selRateList);
			if ($rateListRecIns) $selRateList = $manageRateListObj->latestRateList($productionMatrixMaster);	
		}
		
		//Working Duration
		$noOfHoursPerShiftUnit 	=	$p["noOfHoursPerShiftUnit"];
		$noOfShiftsUnit		=	$p["noOfShiftsUnit"];
		$noOfRetortsUnit	=	$p["noOfRetortsUnit"];
		$noOfSealingMachinesUnit = 	$p["noOfSealingMachinesUnit"];	
		$noOfPouchesSealedUnit 	=	$p["noOfPouchesSealedUnit"];
		$noOfMinutesUnit	=	$p["noOfMinutesUnit"];
		$noOfDaysInYearUnit	=	$p["noOfDaysInYearUnit"];
		$noOfWorkingDaysInMonthUnit = 	$p["noOfWorkingDaysInMonthUnit"];	
		$noOfHoursPerDayUnit	=	$p["noOfHoursPerDayUnit"];
		$noOfMinutesPerHourUnit	=	$p["noOfMinutesPerHourUnit"];
		
		//Fuel Price
		$dieselConsumptionOfBoilerUnit = $p["dieselConsumptionOfBoilerUnit"];
		$dieselCostPerLitre	=	$p["dieselCostPerLitre"];
		$electricConsumptionPerShift = $p["electricConsumptionPerShift"];
		$electricConsumptionPerDayUnit = $p["electricConsumptionPerDayUnit"];
		$electricCostPerUnit	=	$p["electricCostPerUnit"];
		$waterConsumptionPerRetortBatchUnit = $p["waterConsumptionPerRetortBatchUnit"];
		$generalWaterConsumptionPerDayUnit = $p["generalWaterConsumptionPerDayUnit"];
		$costPerLitreOfWater	=	$p["costPerLitreOfWater"];		
		$noOfCylindersPerShiftPerRetort = $p["noOfCylindersPerShiftPerRetort"];
		$gasPerCylinderPerDay	=	$p["gasPerCylinderPerDay"];
		$costOfCylinder		=	$p["costOfCylinder"];
		
		//Other Cost
		$maintenanceCostPerRetortPerShift = $p["maintenanceCostPerRetortPerShift"];
		$maintenanceCost	=	$p["maintenanceCost"];
		$consumableCostPerShiftPerMonth = $p["consumableCostPerShiftPerMonth"];
		$consumablesCost	=	$p["consumablesCost"];
		$labCostPerRetort	=	$p["labCostPerRetort"];
		$labCost		=	$p["labCost"];
		$pouchesTestPerBatchUnit = 	$p["pouchesTestPerBatchUnit"];
		$pouchesTestPerBatchTCost = $p["pouchesTestPerBatchTCost"];
		//Holding Cost
		$holdingCost		=	$p["holdingCost"];
		$holdingDuration	=	$p["holdingDuration"];
		$adminOverheadChargesCode = $p["adminOverheadChargesCode"];
		$adminOverheadChargesCost = $p["adminOverheadChargesCost"];
		$profitMargin		=	$p["profitMargin"];
		$insuranceCost		=	$p["insuranceCost"];
		//$cstRate		=	$p["cstRate"];
		$educationCess		=	$p["educationCess"];
		$exciseRate		=	$p["exciseRate"];
		$pickle			=	$p["pickle"];

		//Man power		
		$variableManPowerCostPerDay = 	$p["variableManPowerCostPerDay"];
		$fixedManPowerCostPerDay =	$p["fixedManPowerCostPerDay"];	

		//Fish Cutting
		
		//Maketing	
		$totalMktgCostActual		=	$p["totalMktgCostActual"];
		$totalMktgCostIdeal		=	$p["totalMktgCostIdeal"];
		$totalMktgCostTCost		=	$p["totalMktgCostTCost"];
		$totalMktgCostACost		=	$p["totalMktgCostACost"];
		//Travel		

		$totalTravelCost		=	$p["totalTravelCost"];
		$totalTravelACost		=	$p["totalTravelACost"];
		//Advertisement
		$advtCostPerMonthIdeal		=	$p["advtCostPerMonthIdeal"];

		# Man Power Updation		
		$hidManPowerCount		=	$p["hidManPowerCount"];
		for ($i=1; $i<=$hidManPowerCount; $i++) {
			$manPowerId   = $p["manPowerId_".$i];					
			$manPowerUnit = $p["manPowerUnit_".$i];
			$manPowerPuCost = $p["manPowerPuCost_".$i];
			$manPowerTCost = $p["manPowerTCost_".$i];
			$updateManPowerRec = $productionMatrixMasterObj->updateManPowerRec($manPowerId, $manPowerUnit, $manPowerPuCost, $manPowerTCost);
		}

		# Fish Cutting Cost updation
		/*
		$hidFishCuttingCount = $p["hidFishCuttingCount"];
		for ($j=1; $j<=$hidFishCuttingCount; $j++) {
			$fishCuttingRecId   = $p["fishCuttingRecId_".$j];					
			$costPerKg = $p["costPerKg_".$j];			
			$updateFishCuttingRec = $productionMatrixMasterObj->updateFishCuttingRec($fishCuttingRecId, $costPerKg);
		}
		*/

		# Marketing Cost
		$hidMktgCostCount = $p["hidMktgCostCount"];
		for ($k=1; $k<=$hidMktgCostCount; $k++) {
			$marketingCostRecId   = $p["marketingCostRecId_".$k];					
			$mktgActual = $p["mktgActual_".$k];
			$mktgIdeal = $p["mktgIdeal_".$k];
			$mktgPuCost = $p["mktgPuCost_".$k];
			$mktgTotCost = $p["mktgTotCost_".$k];
			$mktgAvgCost = $p["mktgAvgCost_".$k];

			$updateMktgCostRec = $productionMatrixMasterObj->updateMarketingCost($marketingCostRecId, $mktgActual, $mktgIdeal, $mktgPuCost, $mktgTotCost, $mktgAvgCost);
		}
		# Travel cost
		$hidTravelCostCount	= $p["hidTravelCostCount"];
		for ($l=1; $l<=$hidTravelCostCount; $l++) {
			$travelCostRecId   = $p["travelCostRecId_".$l];					
			$travelActual = $p["travelActual_".$l];
			$travelIdeal = $p["travelIdeal_".$l];
			$travelPuCost = $p["travelPuCost_".$l];
			$travelTotCost = $p["travelTotCost_".$l];
			$travelAvgCost = $p["travelAvgCost_".$l];

			$updateTravelCostRec = $productionMatrixMasterObj->updateTravelCost($travelCostRecId, $travelActual, $travelIdeal, $travelPuCost, $travelTotCost, $travelAvgCost);
		}

		$mpcRateList	= $p["hidMPCRateList"];
		$fccRateList	= $p["hidFCCRateList"];
		$mcRateList	= $p["hidMCRateList"];
		$tcRateList	= $p["hidTCRateList"];
		
		if ($productionMatrixMasterId!="" && $newRateList!='Y') {
			$productionMatrixMasterRecUptd		=	$productionMatrixMasterObj->updateProductionMatrixMaster($productionMatrixMasterId, $noOfHoursPerShiftUnit, $noOfShiftsUnit, $noOfRetortsUnit, $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateList, $fccRateList, $mcRateList, $tcRateList, $selRateList);
		} else if ($productionMatrixMasterId=="" || $newRateList=='Y') {			
			$productionMatrixMasterRecUptd	=	$productionMatrixMasterObj->addProductionMatrixMaster($noOfHoursPerShiftUnit, $noOfShiftsUnit, $noOfRetortsUnit, $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateList, $fccRateList, $mcRateList, $tcRateList, $selRateList);		
		}
		
		# Production Matrix Master Current Rate List
		$cRateListId = $manageRateListObj->latestRateList($productionMatrixMaster);
		// -----------------------------------------
		// Updating production Matrix Recs
		// -----------------------------------------
		
		if ($productionMatrixMasterId!="" && $selRateList==$cRateListId) {
			# Get Prouction Matrix Records	
			$productionMatrixResultSetObj = $productionMatrixObj->fetchAllRecords();
			$calcNumOfHrs = "";
			$totalHrsFirstBtch = "";
			$totalHrsOtherBtch = "";	
			$totalNumOfBtchPerDay = "";
			$calcElectricCostPerBtch = "";
			$calcWaterCostPerBtch = "";
			$calcGasCostPerBtch = "";
			$totalFuelCost = "";
			$calcMaintCostPerBtch = "";
			$variManPowerCost = "";
			$mktgTeamCostPerBtch = "";
			$calcMktgTravelCost = "";
			$advCostPerPouch = "";
			while ($pmr=$productionMatrixResultSetObj->getRow()) {				
				$productionMatrixRecId 	= $pmr[0];
				$fillingWtPerPouch 	= $pmr[3];
				$prodQtyPerBtch		= $pmr[4];
				$noOfPouch		= $pmr[5];
				$processedWtPerBtch 	= $pmr[6];
				$noOfHrsPrep		= $pmr[7];
				$noOfHrsCook		= $pmr[8];
				
				# No of Hours for Filling & Sealing
				$calcNumOfHrs = ($noOfMinutesUnit/$noOfMinutesPerHourUnit)+(((($noOfPouchesSealedUnit/$noOfMinutesPerHourUnit)/$noOfMinutesPerHourUnit)*$noOfPouch)/$noOfSealingMachinesUnit);
				$noOfHrsFill		= number_format($calcNumOfHrs,2,'.','');
				$noOfHrsRetort		= $pmr[10];
				# Calc First Btch Hrs
				$totalHrsFirstBtch 	= $noOfHrsPrep+$noOfHrsCook+$noOfHrsFill+$noOfHrsRetort;

				$noOfHrsFirstBtch	= number_format($totalHrsFirstBtch,2,'.','');
				
				# Other Btch Hrs
				$totalHrsOtherBtch = ($noOfHrsFirstBtch-($noOfHrsFill+$noOfHrsRetort));

				$noOfHrsOtherBtch	= number_format($totalHrsOtherBtch,2,'.','');
				# Calc Num Batchs Per Day
				$totalNumOfBtchPerDay = ((($noOfHoursPerShiftUnit-$noOfHrsFirstBtch)/$noOfHrsOtherBtch)+1)*$noOfRetortsUnit*$noOfShiftsUnit;
				$noOfBtchsPerDay	= number_format(abs($totalNumOfBtchPerDay),2,'.','');

				$boilerRequired		= $pmr[14];
				# Calc
				$calcDieselCostPerBtch = "";
				if ($boilerRequired=='Y') {
					$calcDieselCostPerBtch = ($dieselConsumptionOfBoilerUnit*$dieselCostPerLitre*($noOfHrsFirstBtch+($noOfHrsOtherBtch*($noOfBtchsPerDay-1))))/$noOfBtchsPerDay;
				} else {
					$calcDieselCostPerBtch = 0;
				}
				
				$dieselCostPerBtch	= number_format(abs($calcDieselCostPerBtch),2,'.','');

				# Calc Electric Cost
				$calcElectricCostPerBtch = ($electricConsumptionPerDayUnit*$electricCostPerUnit)/$noOfBtchsPerDay;

				$electricityCostPerBtch = number_format($calcElectricCostPerBtch,2,'.','');

				//Calc Water Cost
				$waterConsumptionPerRetort = "";
				if ($boilerRequired=='Y') $waterConsumptionPerRetort = $waterConsumptionPerRetortBatchUnit;
				else $waterConsumptionPerRetort = 0;
				#					
				$calcWaterCostPerBtch = (($waterConsumptionPerRetort)+(($generalWaterConsumptionPerDayUnit * $noOfShiftsUnit)/$noOfWorkingDaysInMonthUnit/$noOfBtchsPerDay)) * $costPerLitreOfWater;
				$waterCostPerBtch	= number_format($calcWaterCostPerBtch,2,'.','');
				# Calc Gas  Cost
				$calcGasCostPerBtch = ($costOfCylinder*$gasPerCylinderPerDay)/$noOfBtchsPerDay;
				$gasCostPerBtch		= number_format($calcGasCostPerBtch,2,'.','');

				# Calc Total Fuel Cost
				$totalFuelCost = $dieselCostPerBtch+$electricityCostPerBtch+$waterCostPerBtch+$gasCostPerBtch;
				$totFuelCostPerBtch	= number_format($totalFuelCost,2,'.','');
				
				# Calc Maintenance Cost
				$calcMaintCostPerBtch = ($maintenanceCost+$consumablesCost+$labCost)/$noOfWorkingDaysInMonthUnit/$noOfBtchsPerDay;
				$maintCostPerBtch	= number_format($calcMaintCostPerBtch,2,'.','');
				# Variable Man Power Cost
				$variManPowerCost = ($variableManPowerCostPerDay/$noOfBtchsPerDay);
				$variManPwerCostPerBtch = number_format($variManPowerCost,2,'.','');
				# Mkg Team Cost
				$mktgTeamCostPerBtch = $totalMktgCostTCost/($noOfWorkingDaysInMonthUnit*$noOfPouch*$noOfBtchsPerDay);
				$mktgTeamCostPerPouch	= number_format($mktgTeamCostPerBtch,2,'.','');

				# Calc Mktg Travel Cost
				$calcMktgTravelCost = $totalTravelCost/($noOfWorkingDaysInMonthUnit*$noOfPouch*$noOfBtchsPerDay);
				$mktgTravelCost		= number_format($calcMktgTravelCost,2,'.','');

				# Calc Advert Cost
				$advCostPerPouch = $advtCostPerMonthIdeal/($noOfWorkingDaysInMonthUnit*$noOfPouch*$noOfBtchsPerDay);
				$adCostPerPouch		= number_format($advCostPerPouch,2,'.','');
				if ($productionMatrixRecId!="") {
					# Update Production MAtrix Table
					$productionMatrixRecUptd = $productionMatrixMasterObj->updateProductionMatrix($productionMatrixRecId, $noOfHrsFill, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch, $userId);
				}
			}			
		}
	
		// -----------------------------------------
		// Updating Dist Margin Recs
		// -----------------------------------------
		$hidCstRate = $p["hidCstRate"]; // Edited on 16-09-08  Not working here
		
		if ($productionMatrixMasterId!="" && $selRateList==$cRateListId && $cstRate==$hidCstRate) {
			# Get Dist State Records
			//Just hide on 4-3-09 $getDistStateRecords  = $productionMatrixMasterObj->filterDistStateRecords();
			# Magin Struct Records
			$marginStructureRecords = $marginStructureObj->fetchAllRecords();

			foreach ($getDistStateRecords as $dsr) {
				//$billingForm		= $dsr[4];
				$sBillingForm		= $dsr[4];
				$distributorMgnStateEntryId = $dsr[5];	
				$billingForm	= "";
				//Billing Form VN: VAT NO, CFF: Form F, FC:Form C, FN:Form None
				if ($sBillingForm=='FF' || $sBillingForm=='FC' || $sBillingForm=='FN') {
					$billingForm = 'Y';
				} else if ($sBillingForm=='VN') {
					$billingForm = 'N';
				}

				$actualValue = 0;
				$calcDistMargin = 0;	
				$calcMarkUpValue=0;
				$totalMarkUpValue=1;
				$totalMarkDownValue = 1;	
				$distMarginEntryId = "";
				$distMarginPercent = "";
				$avgMargin = "";
				foreach ($marginStructureRecords as $msr) {
					$marginStructureId = $msr[0];
					$marginStructureName	= stripSlash($msr[1]);
					$mgnStructureDescr	= stripSlash($msr[2]);
					$priceCalcType		= $msr[3];
					$useAvgDistMagn		= $msr[4];
					$mgnStructBillingOnFormF = $msr[7];

					list($distMarginEntryId, $distMarginPercent) = $distMarginStructureObj->getMarginEntryRec($distributorMgnStateEntryId,$marginStructureId);
					if ($mgnStructBillingOnFormF=='Y' && $billingForm=='Y') {
						$distMarginPercent = $cstRate;
					} else if($mgnStructBillingOnFormF=='Y' && $billingForm=='N') {
						$distMarginPercent = 0;
					}
					$actualValue =  $distMarginPercent/100;

					if ($useAvgDistMagn=='Y') {				
						if ($priceCalcType=='MU') {
							$calcMarkUpValue = 1+$actualValue;
							$totalMarkUpValue /= $calcMarkUpValue;			
						}		
						if ($priceCalcType=='MD') {
							$calcMarkDownValue = 1-$actualValue;			
							$totalMarkDownValue *= $calcMarkDownValue;
						}
					}
					//echo "$distMarginEntryId, $marginStructureName = $distMarginPercent"."<br>";
					# Update Dist Margin Structure
					$updateDistMagnStructureRec = $distMarginStructureObj->updateDistMarginStructureEntry($distMarginEntryId, $distMarginPercent);
				}  // Structure Loops Ends Here
				$calcDistMargin = (1-($totalMarkUpValue*$totalMarkDownValue))*100;
				if ($calcDistMargin!="") {
					$avgMargin = number_format($calcDistMargin,2,'.','');	
					//echo "=====>$distributorMgnStateEntryId,Average=$avgMargin"."<br>";
					# Update Dist Margin State Average Margin
					$updateDistMarginStateWiseRec = $productionMatrixMasterObj->updateDistMarginStateRec($distributorMgnStateEntryId, $avgMargin);
				}
			}
		} // Dist Margin Up End

		// -----------------------------------------
		// Updating Combo Matrix Rec
		// -----------------------------------------				
		if ($productionMatrixMasterId!="" && $selRateList==$cRateListId) {
			# List all Combo matrix
			$comboMatrixResultSetObj = $productionMatrixMasterObj->fetchAllComboMatrixRecords();
			$calcProcessingCost 	= 0;
			$calcRMCost 		= 0;
			$calcMftingCost		= 0;
			$calcHoldingCost	= 0;
			$calcAdminOverheadCharge = 0;	
			$calcTotalCost		= 0;
			$calcProfitMargin	= 0;
			$calcContingency	= 0;
			$calcPMInPercentOfFC 	= 0;
			while ($cmr=$comboMatrixResultSetObj->getRow()) {
				$comboMatrixRecId 	= $cmr[0];				
				//$productCode	= stripSlash($cmr[1]);
				//$productName	= stripSlash($cmr[2]);
				$forExport	= $cmr[3];

				$selPkgCodeId	= $cmr[4];
				list($packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPkgCost) = $packingMatrixObj->getPackingMatrixRec($selPkgCodeId);
				$idealFactoryCost	= $cmr[8];

				# Listing Mix product combination
				$mixProductRecs = $comboMatrixObj->fetchMixProductRecs($comboMatrixRecId);
				$totalWaterCostPouch 	= 0;
				$totalDieselCostPerPouch = 0;
				$totalElectricCostPerPouch = 0;
				$totalGasCostPerPouch	= 0;
				$totalConsumCostPerPouch = 0;
				$totalManPowerCostPerPouch = 0;
				$fishCode = "";
				$totalFishPrepCostPerPouch = 0;
				$totalSeaFoodCost = 0;
				$totalGravyCost = 0;
				$totalMktgCostPerPouch = 0;
				$totalAdCostPerPouch = 0;
				/*
				$dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch
				*/
				foreach ($mixProductRecs as $mpr) {
					$productEntryId = $mpr[0];
					$netWt		= $mpr[1];
					$fishWt		= $mpr[2];
					$gravyWt	= $mpr[3];
					$percentSeafood	= $mpr[4];
					$rMCodeId	= $mpr[5];
					$noOfBatches	= $mpr[6];
					$batchSize	= $mpr[7];
					$selFishId	= $mpr[8];
					$selProductionCodeId	= $mpr[9];
					list($prodCode, $prodName, $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $boilerRequired, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch) = $productionMatrixObj->getProductionMatrixRec($selProductionCodeId);
					
					# 1. Calculate Water Cost Per Pouch Each Product
					$calcWaterCostPerPouch = 0;					
					$calcWaterCostPerPouch = $waterCostPerBtch/$batchSize;			
					if ($calcWaterCostPerPouch) {
						$totalWaterCostPouch += $calcWaterCostPerPouch;			
					}	

					# 2. Calc Diesel Cost Per Pouch
					$calcDieselCostPerPouch = 0;					
					$calcDieselCostPerPouch = $dieselCostPerBtch/$batchSize;
					//echo "";
					if ($calcDieselCostPerPouch) {
						$totalDieselCostPerPouch += $calcDieselCostPerPouch;
					}

					# 3. Calc Electric Cost Per Pouch
					$calcElectricCostPerPouch = 0;					
					$calcElectricCostPerPouch = $electricityCostPerBtch/$batchSize;
					if ($calcElectricCostPerPouch) {
						$totalElectricCostPerPouch += $calcElectricCostPerPouch;	
					}

					# 4. Calc Gas Cost Per Pouch
					$calcGasCostPerPouch = 0;					
					$calcGasCostPerPouch = $gasCostPerBtch/$batchSize;
					if ($calcGasCostPerPouch) {
						$totalGasCostPerPouch += $calcGasCostPerPouch;			
					}

					# 5. Consumables per pouch
					$calcConsumCostPerPouch = 0;				
					$calcConsumCostPerPouch = $maintCostPerBtch/$batchSize;
					if ($calcConsumCostPerPouch) {
						$totalConsumCostPerPouch += $calcConsumCostPerPouch;
					}

					// 6. Manpower Cost/Pouch
					$calcVariManPowerCostPerPouch = 0;
					$calcFixedManPowerCostPerPouch = 0;
					$calcManPowerCostPerPouch = 0;
					$calcVariManPowerCostPerPouch = $variManPwerCostPerBtch/$batchSize;	
					$calcFixedManPowerCostPerPouch = $fixedManPowerCostPerDay/($noOfBatches*$batchSize);
					$calcManPowerCostPerPouch = $calcVariManPowerCostPerPouch+$calcFixedManPowerCostPerPouch;
					if ($calcManPowerCostPerPouch) {
						$totalManPowerCostPerPouch += $calcManPowerCostPerPouch;
					}
					
					# 7. Fish prep cost/Pouch
					$calcFishPrepCostPerPouch = 0;
					 // Find the selected Fish Code
					//$fishCode = $productionMatrixMasterObj->fishCuttingCode($selFishId);
					 // Find the fish Id and cost from the selected Fish Code
					list($fishId, $selFishCost) = $productionMatrixMasterObj->getFishCuttingCost($selFishId, $fccRateList);						
				
					$calcFishPrepCostPerPouch = $fishWt * $selFishCost;
					if ($calcFishPrepCostPerPouch) {
						$totalFishPrepCostPerPouch += $calcFishPrepCostPerPouch;
					}

					list($prCode, $prName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty) = $productMasterObj->getProductRec($rMCodeId);

					# RM Cost, sea Food cost and Gravy cost
					$calcSeaFoodCost = 0;
					$calcGravyCost = 0;			
					$calcSeaFoodCost = $fishWt*$fishRatePerKgPerBatch;
					if ($calcSeaFoodCost) {
						$totalSeaFoodCost += $calcSeaFoodCost;			
					}					
					$calcGravyCost = $gravyWt*$gravyRatePerKgPerBatch;
					if ($calcGravyCost) {
						$totalGravyCost += $calcGravyCost;
					}	

					// Find the Product Marketing Cost
					$calcMktgCostPerPouch = 0;
					$calcMktgCostPerPouch  = $mktgTeamCostPerPouch + $mktgTravelCost;
					if ($calcMktgCostPerPouch && $forExport=='N') {
						$totalMktgCostPerPouch += $calcMktgCostPerPouch;		
					} else {
						$totalMktgCostPerPouch += 0;			
					}

					# Find the Advert Cost Calculation					
					if ($forExport=='N') {
						$totalAdCostPerPouch += $adCostPerPouch;			
					} else {
						$totalAdCostPerPouch += 0;			
					}

					//echo "Entry Rec=>$productEntryId, $fishId"."<br>";
					# Update Combo Matrix Entry Rec
					$productEntryRecUpdted = $productionMatrixMasterObj->updateComboProductEntryRec($productEntryId, $fishId);

				} // Mix Product Loop End

				#  1. Set the total Mix Product Water Cost	
				if ($totalWaterCostPouch) {
					$waterCostPerPouch = number_format($totalWaterCostPouch,2,'.','');
				}
				# 2. Set the total Mix Product Diesel Cost	
				if ($totalDieselCostPerPouch) {
					$dieselCostPerPouch = number_format($totalDieselCostPerPouch,2,'.','');
				}
				# 3. Set the total Mix Product Electric Cost	
				if ($totalElectricCostPerPouch) {
					$electricCostPerPouch = number_format($totalElectricCostPerPouch,2,'.','');
				}
				# 4. Set the total Mix Product gas Cost	
				if ($totalGasCostPerPouch) {
					$gasCostPerPouch = number_format($totalGasCostPerPouch,2,'.','');
				}
				# 5. Set the total Mix Product Consum Cost	
				if ($totalConsumCostPerPouch) {
					$consumableCostPerPouch = number_format($totalConsumCostPerPouch,2,'.','');
				}
				# 6. Set the total Mix Product Manpower Cost	
				if ($totalManPowerCostPerPouch) {
					$manPowerCostPerPouch = number_format($totalManPowerCostPerPouch,2,'.','');
				}
				# 7. Set the total Mix Product Manpower Cost	
				if ($totalFishPrepCostPerPouch) {
					$fishPrepCostPerPouch = number_format($totalFishPrepCostPerPouch,2,'.','');
				}

				# Processing cost	
				$calcProcessingCost = $totalWaterCostPouch + $totalDieselCostPerPouch + $totalElectricCostPerPouch + $totalGasCostPerPouch + $totalConsumCostPerPouch + $totalManPowerCostPerPouch + $totalFishPrepCostPerPouch;
				if ($calcProcessingCost) {
					$processingCost = number_format($calcProcessingCost,2,'.','');
				}	

				# Set the total sea Food Cost
				if ($totalSeaFoodCost) {
					$seaFoodCost = number_format($totalSeaFoodCost,2,'.','');
				}

				# Set the total sea Food Cost
				if ($totalGravyCost) {
					$gravyCost = number_format($totalGravyCost,2,'.','');
				}

				# Find RM Cost
				$calcRMCost = $totalSeaFoodCost + $totalGravyCost;
				if ($calcRMCost) {
					$rMCost = number_format($calcRMCost,2,'.','');
				}

				# Testing Cost
				$calcTestingCost = 0;					
				$calcTestingCost = ($calcProcessingCost+$calcRMCost) * $pouchesTestPerBatchUnit;
				if ($calcTestingCost) {
					$testingCost = number_format(($calcTestingCost/100),2,'.','');
				}

				# Set total Mktg Cost
				if ($totalMktgCostPerPouch && $forExport=='N') {		
					$mktgCost = number_format($totalMktgCostPerPouch,2,'.','');
				} else {
					$mktgCost = 0;
				}	

				# Set total ad Cost
				if ($totalAdCostPerPouch && $forExport=='N') {					
					$proAdvertCost = number_format($totalAdCostPerPouch,2,'.','');
				} else {
					$proAdvertCost == 0;			
				}

				# Basic Manufacturing Cost
				$calcMftingCost =  $outerPkgCost+$innerPkgCost+$testingCost+$processingCost+$rMCost;

				if ($calcMftingCost) {
					$basicManufactCost = number_format($calcMftingCost,2,'.','');
				}	

				# Find Holding Cost
				$calcHoldingCost = ($basicManufactCost*(($holdingCost/100)/$noOfDaysInYearUnit)*$holdingDuration);
				if ($calcHoldingCost) {
					$proHoldingCost = number_format($calcHoldingCost,2,'.','');
				}
				
				# Find Admin Overhead Charge					
				$calcAdminOverheadCharge = ($proHoldingCost+$proAdvertCost+$mktgCost+$basicManufactCost)*$adminOverheadChargesCost;
				if ($calcAdminOverheadCharge) {
					$adminOverhead = number_format(($calcAdminOverheadCharge/100),2,'.','');
				}
				# Total Cost
				$calcTotalCost	= $proHoldingCost+$proAdvertCost+$mktgCost+$basicManufactCost+$adminOverhead;
				if ($calcTotalCost) {
					$totalCost = number_format($calcTotalCost,2,'.','');
				}

				# Find Profit Margin
				$calcProfitMargin = $totalCost*$profitMargin; 
				if ($calcProfitMargin) {
					$productProfitMargin = number_format(($calcProfitMargin/100),2,'.','');
				}

				# Actual Fact Cost
				$calcActualCost = $totalCost + $productProfitMargin;
				if ($calcActualCost) {
					$actualFactCost = number_format($calcActualCost,2,'.','');
				}
				# Find contingency
				$calcContingency = $idealFactoryCost-$actualFactCost;
				if ($calcContingency) {
					$contingency = number_format($calcContingency,2,'.','');
				}

				# PM in % of FC	
				$calcPMInPercentOfFC = ($productProfitMargin+$contingency)/$idealFactoryCost;
				if ($calcPMInPercentOfFC) {
					$pmInPercentOfFc = number_format(abs($calcPMInPercentOfFC*100),2,'.','');
				} else {
					$pmInPercentOfFc = 0;
				}

				//echo "Main Rec===>$comboMatrixRecId, Percernt=$pmInPercentOfFc, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, Basic=$basicManufactCost, $outerPkgCost, $innerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, Diesel=$dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch, $userId"."<br>";
				# Update Combo matrix Main Rec
				$comboMatrixRecUptd = $productionMatrixMasterObj->updateComboMatrixMainRec($comboMatrixRecId, $pmInPercentOfFc, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $outerPkgCost, $innerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch, $userId);				
			}
		}  //Update Combo Matrix Rec

		// -----------------------------------------
		// Updating Product Pricing
		// -----------------------------------------

		if ($productionMatrixMasterId!="" && $selRateList==$cRateListId) {
			$productPriceResultSetObj = $productionMatrixMasterObj->fetchAllProductPricingRecords();
			# Find the average dist margin
			$avgTotalDistMargin = $productPricingObj->getAvgDistMargin(); 
			$factoryProfitMargin = 0;
			while ($ppr=$productPriceResultSetObj->getRow()) {
				$productPriceMasterId = $ppr[0];	
				$selProduct 		= $ppr[1];
				//$basicManufCost 	= $ppr[2];
				$selBuffer 		= $ppr[3];
				$inclBuffer 		= $ppr[4];
				//$profitMargin 		= $ppr[5];
				//$factoryCost 		= $ppr[6];
				//$avgDistMgn 		= $ppr[7];
				//$mgnForScheme 		= $ppr[8];
				//$noOfPacksFree 		= $ppr[9];
				$mrp 			= $ppr[10];
				//$actualProfitMargin	= $ppr[11];
				//$onMRP 			= $ppr[12];
				//$onFactoryCost 		= $ppr[13];

				// (basic_manufact_cost, contingency, profit_margin, ideal_factory_cost)
				list($basicManufCost, $contigency, $profitMargin, $factoryCost) = $productPricingObj->getProductMatrixRec($selProduct);
		
				# for getting Distributor wise product wise records
				//$distributorWiseRecords = $productPricingObj->fetchAllDistributorRecs($selProduct);
				$distributorCost	= 0;
				$margin 		= 0;
				$totalDistMargin 	= 0;
				$grandTotalDistMargin 	= 0;
				$calcActualProfitMargin = 0;
				$calcOnMRP		= 0;
				$calcOnFactoryCost 	= 0;
				#Find product rec
				list($productExciseRatePercent) = $productPricingObj->getProductExciseRate($selProduct);
				//echo "$productPriceMasterId=$productExciseRatePercent<br>";
				$disWiseProdWiseRecords = $productionMatrixMasterObj->getDistProductPriceRecords($productPriceMasterId);

				// Include Buffer
				$calcProfitMargin = 0;	
				$inclBuffer = "";
				if ($selBuffer=='Y') $inclBuffer = $contigency;
				else $inclBuffer = 0;				
				$calcProfitMargin = $profitMargin + $inclBuffer;
				//echo "$productPriceMasterId==>$profitMargin + $inclBuffer<br>";
				if ($calcProfitMargin) {
					$factoryProfitMargin = number_format($calcProfitMargin,2,'.','');
				}
				$onMRP = "";
				$onFactoryCost = "";
				$excise = "";
				$vatOrCst = "";
				$insurance = "";
				$eduCess = "";
				foreach ($disWiseProdWiseRecords as $dwp) {
					$dpriceStateEntryId	= $dwp[2];
					$distributorId		= $dwp[1];
					$selStateId		= $dwp[3];	
					$costToDistOrStkist	= $dwp[4];
					$actualDistnCost	= $dwp[5];
					$octroi			= $dwp[6];
					$freight		= $dwp[7];
					//echo "F=$productPriceMasterId=".
					# Fin te Dist Latest rate List Id
					$distMRateListId = $distMarginRateListObj->latestRateList($distributorId);
					# Find The valuies from Master
					list ($avgDistributorMargin, $distriTransportCost, $octroiPercent, $vatPercent, $freight) = $productionMatrixMasterObj->getDistStateWiseRec($distributorId, $selStateId, $distMRateListId, $selProduct);

					# (vat/CST, billing form F
					list($taxType, $billingFormF) = $productPricingObj->getDistributorRec($distributorId, $selStateId);
				
					// Insurance
					$calcInsurance = 0;					
					$calcInsurance = $costToDistOrStkist * ($insuranceCost/100);
					if ($calcInsurance) {
						$insurance = number_format($calcInsurance,2,'.','');
					}

					// VAT / CST
					$calcVatOrCST = 0;
					/* FF: 0%, FC:2%, FN:4% (vat rate) not complete info*/
					if ($taxType=='VAT') {
						$calcVatOrCST = $costToDistOrStkist-$costToDistOrStkist/(1+($vatPercent/100));
					} else if ($billingFormF=='FF') {
						$calcVatOrCST = $costToDistOrStkist-$costToDistOrStkist/(1+($cstRate/100));
					} else {
						$calcVatOrCST = 0;	
					}
					if ($calcVatOrCST) {
						$vatOrCst = number_format($calcVatOrCST,2,'.','');
					}	

					// Excise 
					$calcExcise = 0;							
					if ($productExciseRatePercent>0) {
						$calcExcise = $costToDistOrStkist-$vatOrCst-($costToDistOrStkist-$vatOrCst)/(1+($productExciseRatePercent/100));
					}
					if ($calcExcise) {
						$excise = number_format($calcExcise,2,'.','');
					}
			
					// EducationCess
					$calcEduactionCess = 0;					
					$calcEducationCess = $excise * $educationCess;
					if ($calcEduactionCess) {
						$eduCess = number_format($calcEducationCess,2,'.','');
					}				
				
					// Basic Cost
					$calcBasicCost = 0;					
					$calcBasicCost = $actualDistnCost-($octroi+$freight+$insurance+$vatOrCst+$excise+$eduCess);
					//echo "$productPriceMasterId>>>>>>>>>>>>>>>>$actualDistnCost-($octroi+$freight+$insurance+$vatOrCst+$excise+$eduCess)<br>";
					if ($calcBasicCost) {
						$basicCost = number_format($calcBasicCost,2,'.','');
					}

					//Cost Margin
					$calcCostMargin = 0;				
					$calcCostMargin = $basicCost-$factoryCost;
					if ($calcCostMargin) {
						$costMargin = number_format($calcCostMargin,2,'.','');
					}

					//Actual Profit Margin
					$calcActualProfitMgn = 0;
					$calcActualProfitMgn = $costMargin+$factoryProfitMargin;
					//echo "$basicCost-$factoryCost,$productPriceMasterId=>Actual PMargin= $costMargin+$factoryProfitMargin<br>";
					if ($calcActualProfitMgn) {
						$actualProfitMgn = number_format($calcActualProfitMgn,2,'.','');			
						// display the actual Profit Margin
						$distriActualProfitMargin = number_format($calcActualProfitMgn,2,'.','');

						$grandTotalDistMargin += $calcActualProfitMgn;
					}

					// On MRP
					$calcOnMrpPercent = 0;					
					$calcOnMrpPercent = $actualProfitMgn/$mrp;
					if ($calcOnMrpPercent) {
						$onMrp = number_format($calcOnMrpPercent,2,'.','');
					}

					// On Factory Cost
					$calcOnFactoryCost = 0;
					$calcOnFactoryCost = $actualProfitMgn/$factoryCost;
					if ($calcOnFactoryCost) {
						$onFactoryCost = number_format($calcOnFactoryCost,2,'.','');
					}
					// -----------
					# Update Dist Product Price State Wise Rec
					// -----------
					//echo "Dist Product Price State Wis ==>$dpriceStateEntryId, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost<br>";
					$updateDistStateWiseProPriceRec = $productionMatrixMasterObj->updateDistProdPriceStateWiseRec($dpriceStateEntryId, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost);

				} // Dist wise Product Wise loop Ends

				// Product Actual Profit Margin
				//echo "total DistM=$grandTotalDistMargin, Size=".sizeof($disWiseProdWiseRecords)."<br>";
				$calcActualProfitMargin = $grandTotalDistMargin/sizeof($disWiseProdWiseRecords);
				$actualProfitMargin = "";
				if ($calcActualProfitMargin) {
					$actualProfitMargin = number_format($calcActualProfitMargin,2,'.','');
				}
				//echo "MasterId=$productPriceMasterId, Actual Margin=$actualProfitMargin<br>";
				// On MRP (actualProfitMargin/mrp)
				$calcOnMRP = $actualProfitMargin/$mrp;
				if ($calcOnMRP) {
					$onMRP = number_format($calcOnMRP,2,'.','');
				}
				// On Factory Cost (actualProfitMargin/factoryCost)
				$calcOnFactoryCost = $actualProfitMargin/$factoryCost;
				if ($calcOnFactoryCost) {
					$onFactoryCost = number_format($calcOnFactoryCost,2,'.','');
				}

				// calculate average dist margin for Each product
				$calcProductMagn=0 ;				
				$calcProductMagn = $factoryCost/(1-($avgTotalDistMargin/100));		
				if ($calcProductMagn) {
					$avgDistMgn = number_format($calcProductMagn,2,'.','');
				}
	
				// Find Margin Sscheme (MRP- Avg distMargin)
				$calcMarginForScheme = 0;
				$calcMarginForScheme = $mrp-$avgDistMgn;
				if ($calcMarginForScheme) {
					$mgnForScheme = number_format($calcMarginForScheme,2,'.',''); 
				}

				// No of Packs for One Free (=Basic Manuf Cost/Mgn For Scheme)
				$calcNoOfPacksFree = 0;
				$calcNoOfPacksFree = ceil($basicManufCost/$mgnForScheme);
				if ($calcNoOfPacksFree) {
					$noOfPacksFree = number_format($calcNoOfPacksFree,0,'','');
				}
				
				// -----------	
				# Update Product Price Rec
				// ----------
				//echo "<br>";
				//echo "Product Price Main===>$productPriceMasterId, $basicManufCost, $inclBuffer, $factoryProfitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, Actual==$actualProfitMargin, $onMRP, $onFactoryCost<br>";
				$productPriceRecUptd = $productionMatrixMasterObj->updateProductPrice($productPriceMasterId, $basicManufCost, $inclBuffer, $factoryProfitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $actualProfitMargin, $onMRP, $onFactoryCost);
				
			}
		} // Product Pricing Ends Here
		

		if ($productionMatrixMasterRecUptd) {
			$recUptd = true;
			$sessObj->createSession("displayMsg",$msg_succProductionMatrixMasterUpdate);
			$p["cmdSaveChange"] = "";
			//$sessObj->createSession("nextPage",$url_afterUpdateProductionMatrixMaster);		
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductionMatrixMasterUpdate;
		}
		$productionMatrixMasterRecUptd		=	false;
	}
	
	
	# Edit Section
		$newRateList	= "N";
		$prodnMxMasterRec	=	$productionMatrixMasterObj->find($selRateList);
		$editProdnMxMasterRecId	=	$prodnMxMasterRec[0];
		$noOfHoursPerShiftUnit	=	$prodnMxMasterRec[1];
		$noOfShiftsUnit		=	$prodnMxMasterRec[2];
		$noOfRetortsUnit	=	$prodnMxMasterRec[3];
		$noOfSealingMachinesUnit=	$prodnMxMasterRec[4];
		$noOfPouchesSealedUnit	=	$prodnMxMasterRec[5];
		$noOfMinutesUnit	=	$prodnMxMasterRec[6]; 
		$noOfDaysInYearUnit	=	$prodnMxMasterRec[7];
		$noOfWorkingDaysInMonthUnit =	$prodnMxMasterRec[8]; 
		$noOfHoursPerDayUnit	=	$prodnMxMasterRec[9];
		$noOfMinutesPerHourUnit	=	$prodnMxMasterRec[10];
		$dieselConsumptionOfBoilerUnit = $prodnMxMasterRec[11];
		$dieselCostPerLitre	=	$prodnMxMasterRec[12];
		$electricConsumptionPerShift = $prodnMxMasterRec[13];
 		$electricConsumptionPerDayUnit = $prodnMxMasterRec[14];
		$electricCostPerUnit	=	$prodnMxMasterRec[15];
		$waterConsumptionPerRetortBatchUnit = $prodnMxMasterRec[16];
		$generalWaterConsumptionPerDayUnit = $prodnMxMasterRec[17];
		$costPerLitreOfWater	=	$prodnMxMasterRec[18];
		$noOfCylindersPerShiftPerRetort = $prodnMxMasterRec[19];
		$gasPerCylinderPerDay	=	$prodnMxMasterRec[20];
		$costOfCylinder		=	$prodnMxMasterRec[21];
		$maintenanceCostPerRetortPerShift = 	$prodnMxMasterRec[22];
		$maintenanceCost	=	$prodnMxMasterRec[23];
		$consumableCostPerShiftPerMonth =	$prodnMxMasterRec[24];
		$consumablesCost	=	$prodnMxMasterRec[25];
		$labCostPerRetort	=	$prodnMxMasterRec[26];
		$labCost		=	$prodnMxMasterRec[27];
		$pouchesTestPerBatchUnit = $prodnMxMasterRec[28]; 
		$pouchesTestPerBatchTCost = $prodnMxMasterRec[29];
		$holdingCost		=	$prodnMxMasterRec[30];
		$holdingDuration	=	$prodnMxMasterRec[31];
		$adminOverheadChargesCode =	$prodnMxMasterRec[32];
		$adminOverheadChargesCost = $prodnMxMasterRec[33];
		$profitMargin		=	$prodnMxMasterRec[34];
		$insuranceCost		=	$prodnMxMasterRec[35];
		//$cstRate		=	$prodnMxMasterRec[36];
		$educationCess		=	$prodnMxMasterRec[36];
		$exciseRate		=	$prodnMxMasterRec[37];
		$pickle			=	$prodnMxMasterRec[38];

		$variableManPowerCostPerDay =	$prodnMxMasterRec[39];
		$fixedManPowerCostPerDay = 	$prodnMxMasterRec[40];
			
		$totalMktgCostActual	=	$prodnMxMasterRec[41];
		$totalMktgCostIdeal	=	$prodnMxMasterRec[42];
		$totalMktgCostTCost	=	$prodnMxMasterRec[43];
		$totalMktgCostACost	=	$prodnMxMasterRec[44];	
		
		$totalTravelCost	=	$prodnMxMasterRec[45];
		$totalTravelACost	=	$prodnMxMasterRec[46];
		$advtCostPerMonthIdeal	=	$prodnMxMasterRec[47];
		$mpcRateListId		= 	$prodnMxMasterRec[48];
		$fccRateList		= 	$prodnMxMasterRec[49];
		$mcRateListId		= 	$prodnMxMasterRec[50];
		$tcRateListId		=	$prodnMxMasterRec[51];
		
	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$productionMatrixMasterId = $p["hidProductionMatrixMasterId"];
		if ($productionMatrixMasterId!="") {
			// Need to check the selected Category is link with any other process
			$prodMatrixRecDel = $productionMatrixMasterObj->deleteProductMatrixMasterRec($productionMatrixMasterId);
		}
		
		if ($prodMatrixRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductionMatrixMaster);
			$sessObj->createSession("nextPage",$url_afterDelProductionMatrixMaster);
		} else {
			$errDel	=	$msg_failDelProductionMatrixMaster;
		}
		$prodMatrixRecDel	=	false;
	}
#List all Man Power
	$productionManPower = "MPC";	
	if ($p["selRateList"]=="") $mpcRateList = $manageRateListObj->latestRateList($productionManPower);
	else $mpcRateList = $mpcRateListId;
	$manPowerResultSetObj = $productionManPowerObj->fetchAllRecords($mpcRateList);
#List all Fish Cutting Cost
	$prodFishCutting = "FCC";
	if ($p["selRateList"]=="") $fccRateList = $manageRateListObj->latestRateList($prodFishCutting);
	else $fccRateList = $fccRateList;
	//$fishCuttingResultSetObj = $productionFishCuttingObj->fetchAllRecords($fccRateList);
#List all Mktg Cost 
	$prodMarketing = "MC";
	if ($p["selRateList"]=="") $mcRateList = $manageRateListObj->latestRateList($prodMarketing);
	else $mcRateList = $mcRateListId;
	$marketingCostResultSetObj = $productionMarketingObj->fetchAllRecords($mcRateList);
#List all Travel Cost
	$prodTravel 	= "TC";
	if ($p["selRateList"]=="") $tcRateList = $manageRateListObj->latestRateList($prodTravel);
	else $tcRateList = $tcRateListId;
	$travelCostResultSetObj = $productionTravelObj->fetchAllRecords($tcRateList);

	# Rate List
	$pmmRateListRecords = $manageRateListObj->fetchAllRecords($productionMatrixMaster);


	$ON_LOAD_PRINT_JS = "libjs/ProductionMatrixMaster.js";

	# Include Template [topLeftNav.php]
	/*
	//$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	//if ($iFrameVal=='N') require("template/topLeftNav.php");
	//else require("template/btopLeftNav.php");
	*/
	require("template/btopLeftNav.php");
?>
<form name="frmProductionMatrixMaster" action="ProductionMatrixMaster.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>	
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Production Matrix Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
	<?
		if ( $editMode || $addMode) {
	?>
	<tr>
				<td colspan="3" align="center">
						<table width="35%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="4">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td nowrap>
		<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                <option value="">-- Select --</option>
                <?
		foreach ($pmmRateListRecords as $prl) {
			$mRateListId	= $prl[0];
			$rateListName	= stripSlash($prl[1]);
			$startDate	= dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected =  ($selRateList==$mRateListId)?"Selected":"";
		?>
                <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td>
				<input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value="Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$productionMatrixMaster?>'">
			</td>		
		<? }?>
                  </tr>
                </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
				</td>
			</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
				<tr>
					<td>
						<!-- Form fields start -->
						<?php							
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<!--<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Production Matrix Master  </td>
							</tr>-->
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
		
		<td colspan="2" align="center">
			<? if($edit==true){?>&nbsp;&nbsp;
			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductionMatrixMaster(document.frmProductionMatrixMaster)">
		</td>				
											<?} else{?>
										  <td align="center">&nbsp;&nbsp;</td>
											<?}?>
										</tr>
		<input type="hidden" name="hidProductionMatrixMasterId" id="hidProductionMatrixMasterId" value="<?=$editProdnMxMasterRecId;?>">
		<input type="hidden" name="newRateList" id="newRateList" value="">
										<tr>
										  <td colspan="2" nowrap class="fieldName" height="5"></td>
								  </tr>
	<tr>
  	<td colspan="2" nowrap style="padding-left:10px;padding-right:10px;">
	<table align="center">
          <tr>
              	<td nowrap >
		<table cellpadding="1" cellspacing="1" id="newspaper-b1">
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">WORKING DURATION</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No.of Hours/Shift</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfHoursPerShiftUnit" id="noOfHoursPerShiftUnit" size="5" value="<?=$noOfHoursPerShiftUnit?>" style="text-align:right">
				</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No.of Shifts</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfShiftsUnit" id="noOfShiftsUnit" size="5" style="text-align:right" value="<?=$noOfShiftsUnit?>" onkeyup="calcElectricConsumptionPerDayUnit(); calcGasPerCylinderPerDay(); calcMaintenanceCost(); calcConsumablesCost(); calcLabCost();">
				</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No.of Retorts</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfRetortsUnit" id="noOfRetortsUnit" size="5" style="text-align:right" value="<?=$noOfRetortsUnit?>" onkeyup="calcGasPerCylinderPerDay(); calcMaintenanceCost(); calcLabCost();">
				</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Sealing Machines</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfSealingMachinesUnit" id="noOfSealingMachinesUnit" size="5" style="text-align:right" value="<?=$noOfSealingMachinesUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of pouches sealed/minute/Sealing Machine</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfPouchesSealedUnit" id="noOfPouchesSealedUnit" size="5" style="text-align:right" value="<?=$noOfPouchesSealedUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Minutes for Sealing to start after Filling</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfMinutesUnit" id="noOfMinutesUnit" size="5" style="text-align:right" value="<?=$noOfMinutesUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Days in Year</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfDaysInYearUnit" id="noOfDaysInYearUnit" size="5" style="text-align:right" value="<?=$noOfDaysInYearUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Working Days in Month</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfWorkingDaysInMonthUnit" id="noOfWorkingDaysInMonthUnit" size="5" style="text-align:right" value="<?=$noOfWorkingDaysInMonthUnit?>" onkeyup="calcMaintenanceCost(); calcConsumablesCost(); calcLabCost();"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Hours per Day</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfHoursPerDayUnit" id="noOfHoursPerDayUnit" size="5" style="text-align:right" value="<?=$noOfHoursPerDayUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Minutes per Hour</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfMinutesPerHourUnit" id="noOfMinutesPerHourUnit" size="5" style="text-align:right" value="<?=$noOfMinutesPerHourUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
<!-- 	FUEL PRICE	 -->
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">FUEL PRICE</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Diesel Consumption of Boiler/Hour in Litre</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="dieselConsumptionOfBoilerUnit" id="dieselConsumptionOfBoilerUnit" size="5" style="text-align:right" value="<?=$dieselConsumptionOfBoilerUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Diesel Cost per Litre-38</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="dieselCostPerLitre" id="dieselCostPerLitre" size="5" style="text-align:right" value="<?=$dieselCostPerLitre?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Electricity Consumption per shift</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="electricConsumptionPerShift" id="electricConsumptionPerShift" size="5" style="text-align:right" value="<?=$electricConsumptionPerShift?>" onkeyup="calcElectricConsumptionPerDayUnit();"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Electricity Consumption per Day in Units</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="electricConsumptionPerDayUnit" id="electricConsumptionPerDayUnit" size="5" style="text-align:right" value="<?=$electricConsumptionPerDayUnit?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Electricity Cost per Unit</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="electricCostPerUnit" id="electricCostPerUnit" size="5" style="text-align:right" value="<?=$electricCostPerUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Water consumption per Retort Batch</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="waterConsumptionPerRetortBatchUnit" id="waterConsumptionPerRetortBatchUnit" size="5" style="text-align:right" value="<?=$waterConsumptionPerRetortBatchUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">General Water consumption per Day</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="generalWaterConsumptionPerDayUnit" id="generalWaterConsumptionPerDayUnit" size="5" style="text-align:right" value="<?=$generalWaterConsumptionPerDayUnit?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Cost per litre of Water</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;">
				<input type="text" name="costPerLitreOfWater" id="costPerLitreOfWater" size="5" style="text-align:right" value="<?=$costPerLitreOfWater?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No.of cylinders per shift per Retort</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;">
                                    </td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="noOfCylindersPerShiftPerRetort" id="noOfCylindersPerShiftPerRetort" size="5" style="text-align:right" value="<?=$noOfCylindersPerShiftPerRetort?>" onkeyup="calcGasPerCylinderPerDay();"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Gas per 19Kg Cylinder per Day</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="gasPerCylinderPerDay" id="gasPerCylinderPerDay" size="5" style="text-align:right" value="<?=$gasPerCylinderPerDay?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Cost of Cylinder</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="costOfCylinder" id="costOfCylinder" size="5" style="text-align:right" value="<?=$costOfCylinder?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
<!-- OTHER COST -->
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">OTHER COST</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Maintenance cost per Retort per shift</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="maintenanceCostPerRetortPerShift" id="maintenanceCostPerRetortPerShift" size="5" style="text-align:right" value="<?=$maintenanceCostPerRetortPerShift?>" onkeyup="calcMaintenanceCost();"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Maintenance Cost</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="maintenanceCost" id="maintenanceCost" size="5" style="text-align:right" value="<?=$maintenanceCost?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Consumable cost per shift per month</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="consumableCostPerShiftPerMonth" id="consumableCostPerShiftPerMonth" size="5" style="text-align:right" value="<?=$consumableCostPerShiftPerMonth?>" onkeyup="calcConsumablesCost();"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Consumables Cost</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="consumablesCost" id="consumablesCost" size="5" style="text-align:right" value="<?=$consumablesCost?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Lab cost per Retort</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="labCostPerRetort" id="labCostPerRetort" size="5" style="text-align:right" value="<?=$labCostPerRetort?>" onkeyup="calcLabCost();"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Lab Cost</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="labCost" id="labCost" size="5" style="text-align:right" value="<?=$labCost?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Pouches for testing per batch</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
				<input type="text" name="pouchesTestPerBatchUnit" id="pouchesTestPerBatchUnit" size="5" style="text-align:right" value="<?=$pouchesTestPerBatchUnit?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item">
				<input type="text" name="pouchesTestPerBatchTCost" id="pouchesTestPerBatchTCost" size="5" style="text-align:right" value="<?=$pouchesTestPerBatchTCost?>">&nbsp;%</td>
			</TR>
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Holding COST</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Holding Cost</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
				<input type="text" name="holdingCost" id="holdingCost" size="5" style="text-align:right" value="<?=$holdingCost?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Holding Duration</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="holdingDuration" id="holdingDuration" size="5" style="text-align:right" value="<?=$holdingDuration?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Administrative overhead Charges</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item" align="center">
				<input type="text" name="adminOverheadChargesCode" id="adminOverheadChargesCode" size="5" style="text-align:right" value="<?=$adminOverheadChargesCode?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item" align="center">
				<input type="text" name="adminOverheadChargesCost" id="adminOverheadChargesCost" size="5" style="text-align:right" value="<?=$adminOverheadChargesCost?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Profit Margin</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
				<input type="text" name="profitMargin" id="profitMargin" size="5" style="text-align:right" value="<?=$profitMargin?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Insurance Cost</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="insuranceCost" id="insuranceCost" size="5" style="text-align:right" value="<?=$insuranceCost?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
		<!--	<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">CST Rate</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
				<input type="text" name="cstRate" id="cstRate" size="5" style="text-align:right" value="<?=$cstRate?>">&nbsp;%
				<input type="hidden" name="hidCstRate" id="hidCstRate" size="5" style="text-align:right" value="<?=$cstRate?>">
				</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>-->
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Education Cess</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
				<input type="text" name="educationCess" id="educationCess" size="5" style="text-align:right" value="<?=$educationCess?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Excise Rate</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item">RTE</td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
				<input type="text" name="exciseRate" id="exciseRate" size="5" style="text-align:right" value="<?=$exciseRate?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Pickle</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item">PK</td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
				<input type="text" name="pickle" id="pickle" size="5" style="text-align:right" value="<?=$pickle?>">&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
<!--  MAN POWER -->
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">MAN POWER</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
			</TR>			
			<?php
			$i=0;
			while ($mpr=$manPowerResultSetObj->getRow()) {
				$i++;
				$manPowerId 	= $mpr[0];
				$mPName		= stripSlash($mpr[1]);
				$mPType		= $mpr[2];	
				$mPUnit		= $mpr[3];
				$mPPuCost	= $mpr[4];
				$mpTotCost	= $mpr[5];
			?>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><?=$mPName;?>
				<input type="hidden" name="manPowerId_<?=$i?>" id="manPowerId_<?=$i?>" value="<?=$manPowerId?>">
				<input type="hidden" name="manPowerName_<?=$i?>" id="manPowerName_<?=$i?>" size="15"></TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"><?=$mPType?>
				<input type="hidden" name="manPowerType_<?=$i?>" id="manPowerType_<?=$i?>" value="<?=$mPType?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="manPowerUnit_<?=$i?>" id="manPowerUnit_<?=$i?>" size="5" style="text-align:right" value="<?=$mPUnit?>" onkeyup="calcManPowerCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="manPowerPuCost_<?=$i?>" id="manPowerPuCost_<?=$i?>" size="5" style="text-align:right" value="<?=$mPPuCost?>" onkeyup="calcManPowerCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="manPowerTCost_<?=$i?>" id="manPowerTCost_<?=$i?>" size="5" style="text-align:right" value="<?=$mpTotCost?>" readonly></td>
			</TR>
		<? }?>			
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Variable Manpower Cost per Day</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="variableManPowerCostPerDay" id="variableManPowerCostPerDay" size="5" style="text-align:right" value="<?=$variableManPowerCostPerDay?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Fixed Manpower Cost per Day</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="fixedManPowerCostPerDay" id="fixedManPowerCostPerDay" size="5" style="text-align:right" value="<?=$fixedManPowerCostPerDay?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
<!-- 	Man Power Total Count -->
			<input type="hidden" name="hidManPowerCount" id="hidManPowerCount" value="<?=$i?>">	
<!--  FISH CUTTING per Kg -->
			<!--<TR>
				<TD class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">FISH CUTTING per Kg</TD>
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</td>
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</td>
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</td>
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</td>
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</td>
			</TR>-->
			<?php
			/*
			$j=0;
			while(($fcr=$fishCuttingResultSetObj->getRow())) {
				$j++;
				$fishCuttingRecId 	= $fcr[0];
				$fName			= stripSlash($fcr[1]);
				$fCode			= $fcr[2];	
				$fishCuttingCost	= $fcr[3];
			*/
			?>
			<!--<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><?=$fName?>
				<input type="hidden" name="fishCuttingRecId_<?=$j?>" id="fishCuttingRecId_<?=$j?>" value="<?=$fishCuttingRecId?>">
				</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"><?=$fCode?></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="costPerKg_<?=$j?>" id="costPerKg_<?=$j?>" size="5" style="text-align:right" value="<?=$fishCuttingCost?>" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>-->			
			<? //}?>
			<!--<input type="hidden" name="hidFishCuttingCount" id="hidFishCuttingCount" value="<?//$j?>">-->
<!-- 	Fish Cutting Rec Count -->
<!--  MARKETING -->
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">MARKETING</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">ACTUAL</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">IDEAL</th>				
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">A/COST</th>
			</TR>
			<?
			$k = 0;
			while(($mcr=$marketingCostResultSetObj->getRow())) {
				$k++;
				$marketingCostRecId 	= $mcr[0];
				$headName	= stripSlash($mcr[1]);
				$mcActualUnit	= $mcr[2];
				$mcIdealUnit	= $mcr[3];		
				$mcPuCost	= $mcr[4];
				$mcTotCost	= $mcr[5];
				$mcAvgCost	= $mcr[6]; 	
			?>
		
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><?=$headName?>
				<input type="hidden" name="marketingCostRecId_<?=$k?>" id="marketingCostRecId_<?=$k?>" value="<?=$marketingCostRecId?>">
				</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="mktgActual_<?=$k?>" id="mktgActual_<?=$k?>" size="5" style="text-align:right" value="<?=$mcActualUnit?>" onkeyup="calcMktgCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="mktgIdeal_<?=$k?>" id="mktgIdeal_<?=$k?>" size="5" style="text-align:right" value="<?=$mcIdealUnit?>" onkeyup="calcMktgCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="mktgPuCost_<?=$k?>" id="mktgPuCost_<?=$k?>" size="5" style="text-align:right" value="<?=$mcPuCost?>" onkeyup="calcMktgCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="mktgTotCost_<?=$k?>" id="mktgTotCost_<?=$k?>" size="5" style="text-align:right" value="<?=$mcTotCost?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="mktgAvgCost_<?=$k?>" id="mktgAvgCost_<?=$k?>" size="5" style="text-align:right" value="<?=$mcAvgCost?>" readonly></td>
			</TR>
			<? }?>
			<!-- 	Marketing Cost Count -->
			<input type="hidden" name="hidMktgCostCount" id="hidMktgCostCount" value="<?=$k?>">
	
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Total Mktg Team cost per Month</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="totalMktgCostActual" id="totalMktgCostActual" size="5" style="text-align:right" value="<?=$totalMktgCostActual?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="totalMktgCostIdeal" id="totalMktgCostIdeal" size="5" style="text-align:right" value="<?=$totalMktgCostIdeal?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="totalMktgCostTCost" id="totalMktgCostTCost" size="5" style="text-align:right" value="<?=$totalMktgCostTCost?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="totalMktgCostACost" id="totalMktgCostACost" size="5" style="text-align:right" value="<?=$totalMktgCostACost?>"></td>
			</TR>
<!--   Travel Cost -->
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">TRAVEL</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
			</TR>
			<?
			$l = 0;			
			while ($tcr=$travelCostResultSetObj->getRow()) {
				$l++;
				$travelCostRecId 	= $tcr[0];
				$headName	= stripSlash($tcr[1]);
				$mcActualUnit	= $tcr[2];
				$mcIdealUnit	= $tcr[3];		
				$mcPuCost	= $tcr[4];
				$mcTotCost	= $tcr[5];
				$mcAvgCost	= $tcr[6]; 	
			?>		
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;" height="25"><?=$headName?>
				<input type="hidden" name="travelCostRecId_<?=$l?>" id="travelCostRecId_<?=$l?>" value="<?=$travelCostRecId?>">
				</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="travelActual_<?=$l?>" id="travelActual_<?=$l?>" size="5" style="text-align:right" value="<?=$mcActualUnit?>" onkeyup="calcTravelCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="travelIdeal_<?=$l?>" id="travelIdeal_<?=$l?>" size="5" style="text-align:right" value="<?=$mcIdealUnit?>" onkeyup="calcTravelCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="travelPuCost_<?=$l?>" id="travelPuCost_<?=$l?>" size="5" style="text-align:right" value="<?=$mcPuCost?>" onkeyup="calcTravelCost();" readonly="true"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="travelTotCost_<?=$l?>" id="travelTotCost_<?=$l?>" size="5" style="text-align:right" value="<?=$mcTotCost?>" readonly></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="travelAvgCost_<?=$l?>" id="travelAvgCost_<?=$l?>" size="5" style="text-align:right" value="<?=$mcAvgCost?>" readonly></td>
			</TR>
			<? }?>
			<!-- 	Travel Cost Count -->
			<input type="hidden" name="hidTravelCostCount" id="hidTravelCostCount" value="<?=$l?>">
	
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Total Travel cost per Month</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="totalTravelCost" id="totalTravelCost" size="5" style="text-align:right" value="<?=$totalTravelCost?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="totalTravelACost" id="totalTravelACost" size="5" style="text-align:right" value="<?=$totalTravelACost?>"></td>
			</TR>
<!--  Ad Cost -->
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">ADVERTISEMENT</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"></th>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Advt cost per Month</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
				<input type="text" name="advtCostPerMonthIdeal" id="advtCostPerMonthIdeal" size="5" style="text-align:right" value="<?=$advtCostPerMonthIdeal?>"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
			</TR>
		</table>
		</td>
          </tr>
          </table></td>
	  </tr>
	<tr>
		<td colspan="4"  height="10" ></td>
	</tr>
	<tr>
  	<td colspan="2" align="center">
		<? if($edit==true){?>&nbsp;&nbsp;
		<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validateProductionMatrixMaster(document.frmProductionMatrixMaster);">
		<? }?>
	</td>
	</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
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
		
		# Listing LandingCenter Starts
	?>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10" align="center" ></td>
	</tr>
<input type='hidden' name="hidMPCRateList" value="<?=$mpcRateList?>">
<input type='hidden' name="hidFCCRateList" value="<?=$fccRateList?>">
<input type='hidden' name="hidMCRateList" value="<?=$mcRateList?>">
<input type='hidden' name="hidTCRateList" value="<?=$tcRateList?>">
		<tr>
			<td height="10"></td>
		</tr>	
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
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
		<tr>
			<td height="10"></td>
		</tr>	
	</table>
<? 
	if ($iFrameVal=="") { 
?>
	<!--script language="javascript">	
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
	ensureInFrameset(document.frmProductionMatrixMaster);	
	</script-->
<? 
	}
?>
</form>
	<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
	?>