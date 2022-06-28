<?php
class ManageRateList
{
	/****************************************************************
	This class deals with all the operations relating to Manage Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ManageRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addRateList($rateListName, $startDate, $copyRateList, $userId, $selPage, $pageCurrentRateListId)
	{
		$qry = "insert into m_rate_list (name, start_date, page_type) values('".$rateListName."', '".$startDate."', '$selPage')";		
		//echo $qry;
		
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Prev Rate List Rec END DATE
			if ($pageCurrentRateListId!="") {
				$updateRateListEndDate = $this->updateRateListRec($pageCurrentRateListId, $startDate);
			}

	#----------------------- Copy Functions ---------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();			
			if ($copyRateList!="") {
				# Man Power Master
				if ($selPage=='MPC') {
					# Get all Man Power Records
					$manPowerRecords = $this->productionManPowerRecs($copyRateList);
					foreach ($manPowerRecords as $mpr) {
						$manPowerId 	= $mpr[0];
						$mPName		= $mpr[1];
						$mPType		= $mpr[2];	
						$mPUnit		= $mpr[3];
						$mPPuCost	= $mpr[4];
						$mpTotCost	= $mpr[5];
						if ($insertedRateListId) {
							# Insert Man Power Recs
							$manPowerRecIns = $this->addManPower($mPName, $mPType, $mPUnit, $mPPuCost, $mpTotCost, $insertedRateListId);	
						}
					}	
				}

				# Fish Cutting Cost Master
				if ($selPage=='FCC') {
					
					# Get all Fish Cutting Cost Records
					$fishCuttingCostRecords = $this->fishCuttingCostRecords($copyRateList);	
					foreach ($fishCuttingCostRecords as $fcr) {
						$fishCuttingRecId 	= $fcr[0];
						$ingMainCategory	= $fcr[1];
						$selFish		= $fcr[2];
						$costPerKg		= $fcr[3];				
						if ($insertedRateListId) {
							$fishCuttingRecIns = $this->addFishCutting($ingMainCategory, $selFish, $costPerKg, $insertedRateListId);
						}
					}
				}

				# Marketting Cost Master
				if ($selPage=='MC') {
					# Get all Marketting Cost Records
					$marketingCostRecords = $this->marketingCostRecords($copyRateList);
					foreach ($marketingCostRecords as $mcr) {
						$marketingCostRecId 	= $mcr[0];
						$mktgPositionName	= $mcr[1];
						$mktgActual	= $mcr[2];
						$mktgIdeal	= $mcr[3];		
						$puCost		= $mcr[4];
						$totCost	= $mcr[5];
						$avgCost	= $mcr[6]; 
						if ($insertedRateListId) {
							$marketingCostRecIns = $this->addMarketingCost($mktgPositionName, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $insertedRateListId);
						}
					}
				}
				# Travel Cost Master
				if ($selPage=='TC') {
					$travelCostRecords = $this->travelCostRecords($copyRateList);	
					foreach ($travelCostRecords as $tcr) {
						$travelCostRecId 	= $tcr[0];
						$marketingPerson	= $tcr[1];
						$mktgActual	= $tcr[2];
						$mktgIdeal	= $tcr[3];		
						$puCost		= $tcr[4];
						$totCost	= $tcr[5];
						$avgCost	= $tcr[6];
						if ($insertedRateListId) {
							$travelCostRecIns = $this->addTravelCost($marketingPerson, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $insertedRateListId);					
						}
					}	
				}

				# Packing Labour Cost
				if ($selPage=='PLC') {
					$packingLabourCostRecords = $this->packingLabourCostRecords($copyRateList);
					foreach ($packingLabourCostRecords as $pcr) {
						$packingLabourCostRecId = $pcr[0];
						$itemName		= $pcr[1];
						$itemCode		= $pcr[2];	
						$costPerItem		= $pcr[3];
						if ($insertedRateListId) {
							$packingLabourCostRecIns = $this->addPackingLabourCost($itemName, $itemCode, $costPerItem, $insertedRateListId);
						}
					}
				}
				# Packing Sealing Cost
				if ($selPage=='PSC') {
					$packingSealingCostRecords = $this->packingSealingCostRecords($copyRateList);
					foreach ($packingSealingCostRecords as $pscr) {
						$packingSealingCostRecId = $pscr[0];
						$itemName	= $pscr[1];
						$itemCode	= $pscr[2];	
						$costPerItem	= $pscr[3];
						if ($insertedRateListId) {
							$packingSealingCostRecIns = $this->addPackingSealingCost($itemName, $itemCode, $costPerItem, $insertedRateListId);
						}
					}
				}
				# Packing Material Cost
				if ($selPage=='PMC') {
					$packingMaterialCostRecords = $this->packingMaterialCostRecords($copyRateList);
					foreach ($packingMaterialCostRecords as $pmcr) {
						$packingMaterialCostRecId = $pmcr[0];				
						$stockId	= $pmcr[1];
						$supplierId	= $pmcr[2];
						$costPerItem	= $pmcr[3];
						$totCost	= $pmcr[4];
						$supplierRateListId = $pmcr[5];
						
						if ($insertedRateListId) {
							$packingMaterialCostRecIns = $this->addPackingMaterialCost($stockId, $supplierId, $costPerItem, $totCost, $insertedRateListId, $supplierRateListId);
						}
					}					
				}

				# Production Matrix Master Master
				if ($selPage=='PMM') {
					$productionMatrixMasterRecords = $this->productionMatrixMasteRecs($copyRateList);
					foreach ($productionMatrixMasterRecords as $pmmr) {
						$editProdnMxMasterRecId	=	$pmmr[0];
						$noOfHoursPerShiftUnit	=	$pmmr[1];
						$noOfShiftsUnit		=	$pmmr[2];
						$noOfRetortsUnit	=	$pmmr[3];
						$noOfSealingMachinesUnit=	$pmmr[4];
						$noOfPouchesSealedUnit	=	$pmmr[5];
						$noOfMinutesUnit	=	$pmmr[6]; 
						$noOfDaysInYearUnit	=	$pmmr[7];
						$noOfWorkingDaysInMonthUnit =	$pmmr[8]; 
						$noOfHoursPerDayUnit	=	$pmmr[9];
						$noOfMinutesPerHourUnit	=	$pmmr[10];
						$dieselConsumptionOfBoilerUnit = $pmmr[11];
						$dieselCostPerLitre	=	$pmmr[12];
						$electricConsumptionPerShift = $pmmr[13];
						$electricConsumptionPerDayUnit = $pmmr[14];
						$electricCostPerUnit	=	$pmmr[15];
						$waterConsumptionPerRetortBatchUnit = $pmmr[16];
						$generalWaterConsumptionPerDayUnit = $pmmr[17];
						$costPerLitreOfWater	=	$pmmr[18];
						$noOfCylindersPerShiftPerRetort = $pmmr[19];
						$gasPerCylinderPerDay	=	$pmmr[20];
						$costOfCylinder		=	$pmmr[21];
						$maintenanceCostPerRetortPerShift = 	$pmmr[22];
						$maintenanceCost	=	$pmmr[23];
						$consumableCostPerShiftPerMonth =	$pmmr[24];
						$consumablesCost	=	$pmmr[25];
						$labCostPerRetort	=	$pmmr[26];
						$labCost		=	$pmmr[27];
						$pouchesTestPerBatchUnit = $pmmr[28]; 
						$pouchesTestPerBatchTCost = $pmmr[29];
						$holdingCost		=	$pmmr[30];
						$holdingDuration	=	$pmmr[31];
						$adminOverheadChargesCode =	$pmmr[32];
						$adminOverheadChargesCost = $pmmr[33];
						$profitMargin		=	$pmmr[34];
						$insuranceCost		=	$pmmr[35];
						//$cstRate		=	$pmmr[36];
						$educationCess		=	$pmmr[36];
						$exciseRate		=	$pmmr[37];
						$pickle			=	$pmmr[38];
				
						$variableManPowerCostPerDay =	$pmmr[39];
						$fixedManPowerCostPerDay = 	$pmmr[40];
							
						$totalMktgCostActual	=	$pmmr[41];
						$totalMktgCostIdeal	=	$pmmr[42];
						$totalMktgCostTCost	=	$pmmr[43];
						$totalMktgCostACost	=	$pmmr[44];	
						
						$totalTravelCost	=	$pmmr[45];
						$totalTravelACost	=	$pmmr[46];
						$advtCostPerMonthIdeal	=	$pmmr[47];
						$mpcRateListId		= 	$pmmr[48];
						$fccRateList		= 	$pmmr[49];
						$mcRateListId		= 	$pmmr[50];
						$tcRateListId		=	$pmmr[51];
						if ($insertedRateListId) {
							$productionMatrixMasterRecIns	=	$this->addProductionMatrixMaster($noOfHoursPerShiftUnit, $noOfShiftsUnit, $noOfRetortsUnit, $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateListId, $fccRateList, $mcRateListId, $tcRateListId, $insertedRateListId);
						}
					}
				}

				# Packing Cost Master
				if ($selPage=='PCM') {
					$packingCostMasterRecords = $this->packingCostMasterRecs($copyRateList);
					foreach ($packingCostMasterRecords as $pcmr) {
						$editPackingCostMasterRecId =	$pcmr[0];
						$vatRateForPackingMaterial  = $pcmr[1];
						$innerCartonWstage	    = $pcmr[2];
						$costOfGum		   = $pcmr[3];
						$noOfMcsPerTapeRoll	   = $pcmr[4];
						$costOfTapeRoll		   = $pcmr[5];
						$tapeCostPerMc		   = $pcmr[6];	
						$plcRateListId		   = $pcmr[7];	
						$pscRateListId		   = $pcmr[8];		
						$pmcRateListId		   = $pcmr[9];
						if ($insertedRateListId) {
							$packingCostMasterRecIns = $this->addPackingCostMaster($vatRateForPackingMaterial, $innerCartonWstage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc, $plcRateListId, $pscRateListId, $pmcRateListId, $insertedRateListId);
						}
					}
				}

				# Product MRP Master
				if ($selPage=='PMRP') {
					$prouctMRPMasterRecords = $this->prouctMRPMasterRecs($copyRateList);
					foreach ($prouctMRPMasterRecords as $pmr) {
						$prouctMRPId = $pmr[0];
						$productId   = $pmr[1];
						//$mrp	     = $pmr[2];

						# MRP Expt Recs
						$pMRPExptRecs = $this->productMRPExptRecs($prouctMRPId);

						if ($insertedRateListId) {
							# Add
							$productMRPRecIns = $this->addProductMRP($productId, $mrp, $insertedRateListId, $userId);
							if ($productMRPRecIns) {
								# MRP Entry Id
								$productMRPEntryId = $this->databaseConnect->getLastInsertedId();

								foreach ($pMRPExptRecs as $per) {
									$peStateId		= $per[1];
									$peDistributorId	= $per[2];
									$peMRP			= $per[3];
									# Product Expt Entry
									$this->addProductMRPExpt($productMRPEntryId, $peStateId, $peDistributorId, $peMRP);
								} // Loop Ends here
							} // Rec ins ends here	
						}
					}
		
				} // Product MRP Master End
				
					
			}
	#-------------------- Copy Functions End -------------------------------------		
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# ----------------- PMRP --------------------------
	# Returns all Records
	function prouctMRPMasterRecs($selRateList)
	{
		$qry = " select id, product_id, mrp from m_product_mrp where rate_list_id='$selRateList'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function productMRPExptRecs($productMRPId)
	{
		$qry = " select id, state_id, distributor_id, mrp from m_product_mrp_expt where product_mrp_id='$productMRPId'";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);		
	}	
	
	# Add Product MRP Exception
	function addProductMRPExpt($productMRPEntryId, $selState, $selDistributor, $mrp)
	{
		$qry = "insert into m_product_mrp_expt (product_mrp_id, state_id, distributor_id, mrp) values ('$productMRPEntryId', '$selState', '$selDistributor', '$mrp')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Insert A Rec
	function addProductMRP($selProduct, $mrp, $productMRPRateList, $userId)
	{		
		$qry = "insert into m_product_mrp (product_id, mrp, rate_list_id, created, createdby) values ('$selProduct', '$mrp', '$productMRPRateList', NOW(), '$userId')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# ----------------- PMRP ENDS HERE --------------------------

	# ----------------- PCM --------------------------
	function  packingCostMasterRecs($copyRateList)
	{
		$qry = "select id, vat_rate, inner_carton_wstage, cost_of_gum, num_of_mc_per_tape_roll, cost_of_tape_roll, tape_cost_per_mc, plc_rate_list_id, psc_rate_list_id, pmc_rate_list_id from m_packing_cost where rate_list_id='$copyRateList' and id is not null ";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);		
		return $result;
	}

	#Add
	function addPackingCostMaster($vatRateForPackingMaterial, $innerCartonWstage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc, $plcRateListId, $pscRateListId, $pmcRateListId, $insertedRateListId)
	{
		$qry = "insert into m_packing_cost (vat_rate, inner_carton_wstage, cost_of_gum, num_of_mc_per_tape_roll, cost_of_tape_roll, tape_cost_per_mc, plc_rate_list_id, psc_rate_list_id, pmc_rate_list_id, rate_list_id) values('$vatRateForPackingMaterial', '$innerCartonWstage', '$costOfGum', '$noOfMcsPerTapeRoll', '$costOfTapeRoll', '$tapeCostPerMc', $plcRateListId, $pscRateListId, $pmcRateListId, $insertedRateListId)";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# ----------------- PCM Ends--------------------------

	# ----------------- PMM --------------------------
	function productionMatrixMasteRecs($copyRateList)
	{
		$qry = "select id, no_hours_per_shift, no_of_shifts, no_of_retorts, no_sealing_machines, no_pouches_sealed, no_minutes, no_days_in_year, no_working_days, no_hours_day, no_minutes_hour, diesel_consumption_boiler, diesel_cost_litre, electric_consumption_shift, electric_consumption_day, electric_cost_unit, water_consumption_retort_batch, general_water_consumption_day, cost_per_litre_water, no_of_cyldrs_per_sft_rt, gas_per_cyldr_per_day, cost_of_cyldr, maitance_cost_per_rt_per_sft, maitance_cost, consum_cost_per_sft_per_mth, consum_cost, lab_cost_per_rt, lab_cost, pouch_test_per_btch_unit, pouch_test_per_btch_tot_cost, holding_cost, holding_duration, adn_overhd_charge_code, adn_overhd_charge_cost, profit_margin, insurance_cost, education_cess, excise_rate, pickle, varble_manpower_cost_per_day, fixed_manpower_cost_per_day, tot_mktg_cost_actual, tot_mktg_cost_ideal, tot_mktg_cost_tot_cost, tot_mktg_cost_avg_cost, tot_travel_cost, tot_travel_avg_cost, advt_cost_per_mnth_ideal, mpc_rate_list_id, fcc_rate_list_id, mc_rate_list_id, tc_rate_list_id from m_production_matrix where rate_list_id='$copyRateList' ";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);		
		return $result;
	}

	#Add Production Matrix Master Rec
	function addProductionMatrixMaster($noOfHoursPerShiftUnit, $noOfShiftsUnit, $noOfRetortsUnit, $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateListId, $fccRateList, $mcRateListId, $tcRateListId, $insertedRateListId)
	{
		$qry = "insert into m_production_matrix (no_hours_per_shift, no_of_shifts, no_of_retorts, no_sealing_machines, no_pouches_sealed, no_minutes, no_days_in_year, no_working_days, no_hours_day, no_minutes_hour, diesel_consumption_boiler, diesel_cost_litre, electric_consumption_shift, electric_consumption_day, electric_cost_unit, water_consumption_retort_batch, general_water_consumption_day, cost_per_litre_water, no_of_cyldrs_per_sft_rt, gas_per_cyldr_per_day, cost_of_cyldr, maitance_cost_per_rt_per_sft, maitance_cost, consum_cost_per_sft_per_mth, consum_cost, lab_cost_per_rt, lab_cost, pouch_test_per_btch_unit, pouch_test_per_btch_tot_cost, holding_cost, holding_duration, adn_overhd_charge_code, adn_overhd_charge_cost, profit_margin, insurance_cost, education_cess, excise_rate, pickle, varble_manpower_cost_per_day, fixed_manpower_cost_per_day, tot_mktg_cost_actual, tot_mktg_cost_ideal, tot_mktg_cost_tot_cost, tot_mktg_cost_avg_cost, tot_travel_cost, tot_travel_avg_cost, advt_cost_per_mnth_ideal, mpc_rate_list_id, fcc_rate_list_id, mc_rate_list_id, tc_rate_list_id, rate_list_id) values('$noOfHoursPerShiftUnit', '$noOfShiftsUnit', '$noOfRetortsUnit', $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateListId, $fccRateList, $mcRateListId, $tcRateListId, $insertedRateListId)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# ----------------- PMM  Ends --------------------------


	# ----------------- PMC --------------------------
	function packingMaterialCostRecords($copyRateList)
	{
		$qry = "select id, stock_id, supplier_id, pu_cost, tot_cost, supplier_rate_list_id from m_packing_material_cost where rate_list_id='$copyRateList' order by stock_id asc";
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}	
	function addPackingMaterialCost($stockId, $supplierId, $costPerItem, $totCost, $insertedRateListId, $supplierRateListId)
	{
		$qry = "insert into m_packing_material_cost (stock_id, supplier_id, pu_cost, tot_cost, rate_list_id, supplier_rate_list_id) values('$stockId', '$supplierId', '$costPerItem', '$totCost', '$insertedRateListId', '$supplierRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	# ----------------- PMC  Ends --------------------------

	# ----------------- PSC --------------------------
	function packingSealingCostRecords($copyRateList)
	{
		$qry	= "select id, name, code, cost from m_packing_sealing_cost where rate_list_id='$copyRateList' order by code asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}
	function addPackingSealingCost($itemName, $itemCode, $costPerItem, $insertedRateListId)
	{
		$qry = "insert into m_packing_sealing_cost (name, code, cost, rate_list_id) values('$itemName', '$itemCode', '$costPerItem', '$insertedRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	# ----------------- PSC Ends --------------------------

	# ----------------- PLC --------------------------		
	function packingLabourCostRecords($copyRateList)
	{
		$qry	= "select id, name, code, cost from m_packing_labour_cost where rate_list_id='$copyRateList' order by code asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	function addPackingLabourCost($itemName, $itemCode, $costPerItem, $insertedRateListId)
	{
		$qry = "insert into m_packing_labour_cost (name, code, cost, rate_list_id) values('$itemName', '$itemCode', '$costPerItem', '$insertedRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	# ----------------- PLC Ends --------------------------		

	# ----------------- TC --------------------------		
	function travelCostRecords($copyRateList)
	{
		$qry = "select id, marketing_person_id, actual, ideal, pu_cost, tot_cost, avg_cost from m_prodn_travel where rate_list_id='$copyRateList' ";		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;		
	}

	# Add 
	function addTravelCost($marketingPerson, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $tcRateListId)
	{
		$qry	= "insert into m_prodn_travel (marketing_person_id, actual, ideal, pu_cost, tot_cost, avg_cost, rate_list_id) values('$marketingPerson', '$mktgActual', '$mktgIdeal', '$puCost', '$totCost', '$avgCost', '$tcRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	# ----------------- TC Ends --------------------------		

	# ----------------- MC --------------------------		
	function marketingCostRecords($copyRateList)
	{
		$qry	= "select id, name, actual, ideal, pu_cost, tot_cost, avg_cost from m_prodn_marketing where rate_list_id='$copyRateList' order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}
	# Add 
	function addMarketingCost($mktgPositionName, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $insertedRateListId)
	{
		$qry	=	"insert into m_prodn_marketing (name, actual, ideal, pu_cost, tot_cost, avg_cost, rate_list_id) values('$mktgPositionName', '$mktgActual', '$mktgIdeal', '$puCost', '$totCost', '$avgCost', '$insertedRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	# ------------------ MC END--------------------------

	# ------------------ FCC --------------------------
	function fishCuttingCostRecords($copyRateList)
	{
		$qry	= " select id, ing_category_id, ingredient_id, cost from m_prodn_fish_cutting where rate_list_id='$copyRateList' ";				
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Add 
	function addFishCutting($ingMainCategory, $selFish, $costPerKg, $fcRateListId)
	{
		$qry = "insert into m_prodn_fish_cutting (ing_category_id, ingredient_id, cost, rate_list_id) values('$ingMainCategory', '$selFish', '$costPerKg', '$fcRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# ------------------ FCC END --------------------------

	# ------------------ Man Power Cost --------------------------
	# Get All Man Power Cost Recs
	function productionManPowerRecs($copyRateList)
	{
		$qry	= "select id, name, type, unit, pu_cost, tot_cost from m_prodn_matrix_manpower where rate_list_id='$copyRateList' order by type asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Add 
	function addManPower($name, $manPowerType, $manPowerUnit, $puCost, $totCost, $insertedRateListId)
	{
		$qry	= "insert into m_prodn_matrix_manpower (name, type, unit, pu_cost, tot_cost, rate_list_id) values('$name', '$manPowerType', '$manPowerUnit', '$puCost', '$totCost', '$insertedRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# --------------------------------------------


	# Returns all Recs
	function fetchAllPagingRecords($offset, $limit, $functionFilterId)
	{		
		if ($functionFilterId!="") $whr .= " a.page_type = '".$functionFilterId."'";
		else $whr .= " ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";

		$orderBy	= " a.start_date desc";
		$limit		= " $offset, $limit ";
				
		$qry	= "select a.id, a.name, a.start_date, a.page_type,a.active from m_rate_list a";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		
		$result	= $this->databaseConnect->getRecords($qry);

		//echo "<br>$qry";
		return $result;
	}

	# Returns all Recs
	function fetchAllRecords($functionFilterId)
	{
		if ($functionFilterId!="") $whr .= " a.page_type = '".$functionFilterId."'";
		else $whr .= " ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";

		$orderBy	= " a.start_date desc";					
		$qry		= "select a.id, a.name, a.start_date, a.page_type,a.active from m_rate_list a";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Get a Rec based on id 	
	function find($rateListId)
	{
		$qry = "select id, name, start_date, page_type from m_rate_list where id=$rateListId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateRateList($rateListName, $startDate, $rateListId)
	{
		$qry = " update m_rate_list set name='$rateListName', start_date='$startDate' where id=$rateListId";
 		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteDistMarginRateList($rateListId, $selPage)
	{
		$qry = " delete from m_rate_list where id=$rateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			$latestRateListId = $this->latestRateList($selPage);
			if ($latestRateListId!="") {
				# Update Prev Rate List Date
				$sDate = "0000-00-00";
				$this->updatePrevRateListRec($latestRateListId, $sDate);
			}
		}
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Checking Rate List Id used
	function checkRateListUse($rateListId, $selPage)
	{
		if ($selPage=='MPC') {
			$qry	= "select id from m_prodn_matrix_manpower where rate_list_id='$rateListId'";
		}
		if ($selPage=='FCC') {
			$qry	= "select id from m_prodn_fish_cutting where rate_list_id='$rateListId'";
		}	
		if ($selPage=='MC') {
			$qry	= "select id from m_prodn_marketing where rate_list_id='$rateListId'";
		}	
		if ($selPage=='TC') {
			$qry	= "select id from m_prodn_travel where rate_list_id='$rateListId'";
		}
		if ($selPage=='PLC') {
			$qry	= "select id from m_packing_labour_cost where rate_list_id='$rateListId'";
		}
		if ($selPage=='PSC') {
			$qry	= "select id from m_packing_sealing_cost where rate_list_id='$rateListId'";
		}
		if ($selPage=='PMC') {
			$qry	= "select id from m_packing_material_cost where rate_list_id='$rateListId'";
		}

		if ($selPage=='PMM') {
			$qry	= "select id from m_production_matrix where rate_list_id='$rateListId'";
		}

		if ($selPage=='PCM') {
			$qry	= "select id from m_packing_cost where rate_list_id='$rateListId'";
		}
		if ($selPage=='PMRP') {
			$qry	= "select id from m_product_mrp where rate_list_id='$rateListId'";
		}

		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Current Rate List
	function latestRateList($pageType)
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select a.id from m_rate_list a where a.page_type='$pageType' and '$cDate'>=date_format(a.start_date,'%Y-%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in other Screen
	function findRateList()
	{
		$cDate = date("Y-m-d");
		$qry	=	"select a.id,name,start_date from m_rate_list a where '$cDate'>=date_format(a.start_date,'%Y-%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$array			=	explode("-", $rec[2]);
			$startDate		=	$array[2]."/".$array[1]."/".$array[0];
			$displayRateList =  $rec[1]."&nbsp;(".$startDate.")";
		}
		return (sizeof($rec)>0)?$displayRateList:"";
	}
	#---------------------------------Copy Functions---------------------------------------------

	
	
	#------------------------ Copy Functions End ----------------------------------------

	# Returns all Distibutor based Recs
	function filterRateListRecords($selPageType)
	{
		$qry = "select id, name, start_date from m_rate_list where page_type='$selPageType' order by start_date desc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# update Dist Rate List Rec
	function updateRateListRec($pageCurrentRateListId, $startDate)
	{
		$sDate		= explode("-",$startDate);
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_rate_list set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# update Prev Rate List of current dist Rec
	function updatePrevRateListRec($pageCurrentRateListId, $sDate)
	{		
		$qry = " update m_rate_list set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Checking Record Exist 	
	function checkRecExist($startDate, $selPage)
	{
		$qry = "select id from m_rate_list where start_date='$startDate' and page_type='$selPage'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Check Valid Date Entry
	function chkValidDateEntry($selDate, $selPage, $cId)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		else $uptdQry ="";

		$qry	= "select id from m_rate_list where '$selDate'<=date_format(start_date,'%Y-%m-%d') and page_type='$selPage' $uptdQry order by start_date desc";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?false:true;
	}

	# Get Rate List based on Date
	function getRateList($pageType, $selDate)
	{	
		$qry	= "select id from m_rate_list where page_type='$pageType' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) order by start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get Last rate list from start date
	function getLastRateList($pageType, $rateListStartDate)
	{	
		$qry	= "select a.id from m_rate_list a where a.page_type='$pageType' and date_format(a.start_date,'%Y-%m-%d')<'$rateListStartDate' order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function updateRateListconfirm($rateListId){
		$qry	= "update m_rate_list set active='1' where id=$rateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updateRateListReleaseconfirm($rateListId){
		$qry	= "update m_rate_list set active='0' where id=$rateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
}

?>