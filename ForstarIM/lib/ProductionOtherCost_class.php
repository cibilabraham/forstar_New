<?php
class ProductionOtherCost
{  
	/****************************************************************
	This class deals with all the operations relating to Production Matrix Master
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProductionOtherCost(&$databaseConnect)
    {	
        	$this->databaseConnect =&$databaseConnect;
	}

	function getProductionOthersCost()
	{
		$qry = "select id,maintenance_cost,consumables_cost,lab_cost,pouches_perbatch_unit,pouches_perbatch_tcost,ingredient_powdering_cosperkg,holding_cost,holding_duration,admin_overhead_charges_code,admin_overhead_charges_cost,profit_margin,insurance_cost from m_production_other_cost order by id";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	function addProductionOtherCost($maintenanceCost,$consumablesCost,$labCost,$pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost,$ingredientCost,$holdingCost,$holdingDuration,$adminOverheadChargesCode,$adminOverheadChargesCost,$profitMargin,$insuranceCost,$userId)
	{
		
		$qry = "insert into m_production_other_cost (maintenance_cost,consumables_cost,lab_cost,pouches_perbatch_unit,pouches_perbatch_tcost,ingredient_powdering_cosperkg,holding_cost,holding_duration,admin_overhead_charges_code,	admin_overhead_charges_cost,profit_margin,
		insurance_cost,createdon,createdby) values('$maintenanceCost','$consumablesCost','$labCost','$pouchesTestPerBatchUnit','$pouchesTestPerBatchTCost','$ingredientCost','$holdingCost','$holdingDuration','$adminOverheadChargesCode','$adminOverheadChargesCost','$profitMargin','$insuranceCost',Now(),'$userId') ";
		//echo $qry;
		//die();
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function updateProductionOtherCost($productionOtherCostId,$maintenanceCost,$consumablesCost,$labCost,$pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost,$ingredientCost,$holdingCost,$holdingDuration,$adminOverheadChargesCode,$adminOverheadChargesCost,$profitMargin,$insuranceCost,$userId)
	{
		$qry="update m_production_other_cost set maintenance_cost='$maintenanceCost',consumables_cost='$consumablesCost',lab_cost='$labCost',pouches_perbatch_unit='$pouchesTestPerBatchUnit',pouches_perbatch_tcost='$pouchesTestPerBatchTCost',ingredient_powdering_cosperkg='$ingredientCost',holding_cost='$holdingCost',holding_duration='$holdingDuration',admin_overhead_charges_code='$adminOverheadChargesCode',	admin_overhead_charges_cost='$adminOverheadChargesCost',profit_margin='$profitMargin',
		insurance_cost='$insuranceCost',createdon=Now(),createdby='$userId' where id='$productionOtherCostId' ";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}





























	#Add
	function addProductionMatrixMaster($noOfHoursPerShiftUnit, $noOfShiftsUnit, $noOfRetortsUnit, $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateList, $fccRateList, $mcRateList, $tcRateList, $selRateList)
	{
		$qry = "insert into m_production_matrix (no_hours_per_shift, no_of_shifts, no_of_retorts, no_sealing_machines, no_pouches_sealed, no_minutes, no_days_in_year, no_working_days, no_hours_day, no_minutes_hour, diesel_consumption_boiler, diesel_cost_litre, electric_consumption_shift, electric_consumption_day, electric_cost_unit, water_consumption_retort_batch, general_water_consumption_day, cost_per_litre_water, no_of_cyldrs_per_sft_rt, gas_per_cyldr_per_day, cost_of_cyldr, maitance_cost_per_rt_per_sft, maitance_cost, consum_cost_per_sft_per_mth, consum_cost, lab_cost_per_rt, lab_cost, pouch_test_per_btch_unit, pouch_test_per_btch_tot_cost, holding_cost, holding_duration, adn_overhd_charge_code, adn_overhd_charge_cost, profit_margin, insurance_cost, education_cess, excise_rate, pickle, varble_manpower_cost_per_day, fixed_manpower_cost_per_day, tot_mktg_cost_actual, tot_mktg_cost_ideal, tot_mktg_cost_tot_cost, tot_mktg_cost_avg_cost, tot_travel_cost, tot_travel_avg_cost, advt_cost_per_mnth_ideal, mpc_rate_list_id, fcc_rate_list_id, mc_rate_list_id, tc_rate_list_id, rate_list_id) values('$noOfHoursPerShiftUnit', '$noOfShiftsUnit', '$noOfRetortsUnit', $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateList, $fccRateList, $mcRateList, $tcRateList, $selRateList)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}



	# Get Record
	function find($selRateList)
	{
		$qry = "select id, no_hours_per_shift, no_of_shifts, no_of_retorts, no_sealing_machines, no_pouches_sealed, no_minutes, no_days_in_year, no_working_days, no_hours_day, no_minutes_hour, diesel_consumption_boiler, diesel_cost_litre, electric_consumption_shift, electric_consumption_day, electric_cost_unit, water_consumption_retort_batch, general_water_consumption_day, cost_per_litre_water, no_of_cyldrs_per_sft_rt, gas_per_cyldr_per_day, cost_of_cyldr, maitance_cost_per_rt_per_sft, maitance_cost, consum_cost_per_sft_per_mth, consum_cost, lab_cost_per_rt, lab_cost, pouch_test_per_btch_unit, pouch_test_per_btch_tot_cost, holding_cost, holding_duration, adn_overhd_charge_code, adn_overhd_charge_cost, profit_margin, insurance_cost, education_cess, excise_rate, pickle, varble_manpower_cost_per_day, fixed_manpower_cost_per_day, tot_mktg_cost_actual, tot_mktg_cost_ideal, tot_mktg_cost_tot_cost, tot_mktg_cost_avg_cost, tot_travel_cost, tot_travel_avg_cost, advt_cost_per_mnth_ideal, mpc_rate_list_id, fcc_rate_list_id, mc_rate_list_id, tc_rate_list_id, rate_list_id from m_production_matrix where rate_list_id='$selRateList' and id is not null ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}



	# Update 
	function updateProductionMatrixMaster($productionMatrixMasterId, $noOfHoursPerShiftUnit, $noOfShiftsUnit, $noOfRetortsUnit, $noOfSealingMachinesUnit, $noOfPouchesSealedUnit, $noOfMinutesUnit, $noOfDaysInYearUnit, $noOfWorkingDaysInMonthUnit, $noOfHoursPerDayUnit, $noOfMinutesPerHourUnit, $dieselConsumptionOfBoilerUnit, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonthIdeal, $mpcRateList, $fccRateList, $mcRateList, $tcRateList, $selRateList)
	{
		$qry =	" update m_production_matrix set no_hours_per_shift=$noOfHoursPerShiftUnit, no_of_shifts=$noOfShiftsUnit, no_of_retorts=$noOfRetortsUnit, no_sealing_machines=$noOfSealingMachinesUnit, no_pouches_sealed=$noOfPouchesSealedUnit, no_minutes=$noOfMinutesUnit, no_days_in_year=$noOfDaysInYearUnit, no_working_days=$noOfWorkingDaysInMonthUnit, no_hours_day=$noOfHoursPerDayUnit, no_minutes_hour=$noOfMinutesPerHourUnit, diesel_consumption_boiler=$dieselConsumptionOfBoilerUnit, diesel_cost_litre=$dieselCostPerLitre, electric_consumption_shift=$electricConsumptionPerShift, electric_consumption_day=$electricConsumptionPerDayUnit, electric_cost_unit=$electricCostPerUnit, water_consumption_retort_batch=$waterConsumptionPerRetortBatchUnit, general_water_consumption_day=$generalWaterConsumptionPerDayUnit, cost_per_litre_water=$costPerLitreOfWater, no_of_cyldrs_per_sft_rt=$noOfCylindersPerShiftPerRetort, gas_per_cyldr_per_day=$gasPerCylinderPerDay, cost_of_cyldr=$costOfCylinder, maitance_cost_per_rt_per_sft=$maintenanceCostPerRetortPerShift, maitance_cost=$maintenanceCost, consum_cost_per_sft_per_mth=$consumableCostPerShiftPerMonth, consum_cost=$consumablesCost, lab_cost_per_rt=$labCostPerRetort, lab_cost=$labCost, pouch_test_per_btch_unit=$pouchesTestPerBatchUnit, pouch_test_per_btch_tot_cost=$pouchesTestPerBatchTCost, holding_cost=$holdingCost, holding_duration=$holdingDuration, adn_overhd_charge_code=$adminOverheadChargesCode, adn_overhd_charge_cost=$adminOverheadChargesCost, profit_margin=$profitMargin, insurance_cost=$insuranceCost, education_cess=$educationCess, excise_rate=$exciseRate, pickle=$pickle, varble_manpower_cost_per_day=$variableManPowerCostPerDay, fixed_manpower_cost_per_day=$fixedManPowerCostPerDay, tot_mktg_cost_actual=$totalMktgCostActual, tot_mktg_cost_ideal=$totalMktgCostIdeal, tot_mktg_cost_tot_cost=$totalMktgCostTCost, tot_mktg_cost_avg_cost=$totalMktgCostACost, tot_travel_cost=$totalTravelCost, tot_travel_avg_cost=$totalTravelACost, advt_cost_per_mnth_ideal=$advtCostPerMonthIdeal, mpc_rate_list_id='$mpcRateList', fcc_rate_list_id='$fccRateList', mc_rate_list_id='$mcRateList', tc_rate_list_id='$tcRateList', rate_list_id='$selRateList' where id=$productionMatrixMasterId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	#man Power Rec
	function updateManPowerRec($manPowerId, $manPowerUnit, $manPowerPuCost, $manPowerTCost)
	{
		$qry =	" update m_prodn_matrix_manpower set unit='$manPowerUnit', pu_cost='$manPowerPuCost', tot_cost='$manPowerTCost' where id='$manPowerId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	#Updte Fish Cutting cost
	function updateFishCuttingRec($fishCuttingRecId, $costPerKg)
	{
		$qry = "update m_prodn_fish_cutting set cost='$costPerKg' where id=$fishCuttingRecId ";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update  Marketing Cost
	function updateMarketingCost($marketingCostRecId, $mktgActual, $mktgIdeal, $mktgPuCost, $mktgTotCost, $mktgAvgCost)
	{
		$qry = "update m_prodn_marketing set actual='$mktgActual', ideal='$mktgIdeal', pu_cost='$mktgPuCost', tot_cost='$mktgTotCost', avg_cost='$mktgAvgCost' where id=$marketingCostRecId ";				
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update  Travel Cost
	function updateTravelCost($travelCostRecId, $travelActual, $travelIdeal, $travelPuCost, $travelTotCost, $travelAvgCost)
	{
		$qry = "update m_prodn_travel set actual='$travelActual', ideal='$travelIdeal', pu_cost='$travelPuCost', tot_cost='$travelTotCost', avg_cost='$travelAvgCost' where id=$travelCostRecId ";				
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Get all master Setting Value (using in Other Screen)
	function getProductionMasterValue($selRateList)
	{
		$rec = $this->find($selRateList);
		return 	(sizeof($rec)>0)?array($rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8], $rec[9], $rec[10], $rec[11], $rec[12], $rec[13], $rec[14], $rec[15], $rec[16], $rec[17], $rec[18], $rec[19], $rec[20], $rec[21], $rec[22], $rec[23], $rec[24], $rec[25], $rec[26], $rec[27], $rec[28], $rec[29], $rec[30], $rec[31], $rec[32], $rec[33], $rec[34], $rec[35], $rec[36], $rec[37], $rec[38], $rec[39], $rec[40], $rec[41], $rec[42], $rec[43], $rec[44], $rec[45], $rec[46], $rec[47]):0;		
	}

	# Delete Product Matrix Master Rec
	function deleteProductMatrixMasterRec($productionMatrixMasterId)
	{
		$qry = " delete from m_production_matrix where id=$productionMatrixMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update Production Matrix Rec
	function updateProductionMatrix($productionMatrixRecId, $noOfHrsFill, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch, $userId)
	{
		$qry = "update t_production_matrix set no_of_hrs_filling=$noOfHrsFill, no_of_hrs_first_btch=$noOfHrsFirstBtch, no_of_hrs_other_btch=$noOfHrsOtherBtch, no_of_btchs_per_day=$noOfBtchsPerDay, diesel_cost_per_btch=$dieselCostPerBtch, electric_cost_per_btch=$electricityCostPerBtch, water_cost_per_btch=$waterCostPerBtch, gas_cost_per_btch=$gasCostPerBtch, tot_fuel_cost_per_btch=$totFuelCostPerBtch, maint_cost_per_btch=$maintCostPerBtch, vari_manpower_cost_per_btch=$variManPwerCostPerBtch, mktg_cost_per_btch=$mktgTeamCostPerPouch, mktg_travel_cost=$mktgTravelCost, ad_cost_per_pouch=$adCostPerPouch, modified=NOW(), modifiedby='$userId' where id=$productionMatrixRecId ";		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Get Dist State Records
	function filterDistStateRecords()
	{
		$qry = " select a.id, a.distributor_id, a.state_id, a.tax_type, a.billing_form, b.id, b.avg_margin, b.octroi, b.vat, b.freight, b.transport_cost from m_distributor_state a left join m_distributor_margin_state b on a.id=b.dist_state_entry_id join m_distributor_margin c on b.distributor_margin_id=c.id ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# update Dist Margin State Wise Rec
	function updateDistMarginStateRec($distMarginStateEntryId, $avgMargin)
	{
		$qry = "update m_distributor_margin_state set avg_margin='$avgMargin' where id='$distMarginStateEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	/* Combo Matrix Rec Updation */
	# Returns all Records
	function fetchAllComboMatrixRecords()
	{
		$qry = " select id, code, name, export, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch, num_of_product from t_combo_matrix order by code asc ";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Find the Fish Cost
	/*
	function fishCuttingCode($fishCuttingRecId)
	{
		$qry = "select id, name, code, cost, rate_list_id from m_prodn_fish_cutting where id='$fishCuttingRecId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//$rec = $this->find($fishCuttingRecId);
		return (sizeof($rec)>0)?$rec[2]:0;
	}
	*/

	#Find the Fish Cost
	function getFishCuttingCost($selFishId, $fcRateListId)
	{
		$qry = "select id, cost from m_prodn_fish_cutting where id='$selFishId' and rate_list_id='$fcRateListId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);		
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):0;
	}

	// Update Mix product Recs
	function updateComboProductEntryRec($productEntryId, $selFish)
	{
		$qry = "update t_combo_matrix_entry set sel_fish_id='$selFish' where id='$productEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update  a  Record
	function updateComboMatrixMainRec($comboMatrixRecId, $pmInPercentOfFc, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch, $userId)
	{
		$qry = "update t_combo_matrix set pm_in_percent_of_fc='$pmInPercentOfFc', contingency='$contingency', actual_fact_cost='$actualFactCost', profit_margin='$productProfitMargin', total_cost='$totalCost', admin_over_head='$adminOverhead', holding_cost='$proHoldingCost', advert_cost='$proAdvertCost', mktg_cost='$mktgCost', basic_manufact_cost='$basicManufactCost', outer_pkg_cost='$productOuterPkgCost', inner_pkg_cost='$productInnerPkgCost', testing_cost='$testingCost', processing_cost='$processingCost', rm_cost='$rMCost', sea_food_cost='$seaFoodCost', gravy_cost='$gravyCost', water_cost_per_pouch='$waterCostPerPouch', diesel_cost_per_pouch='$dieselCostPerPouch', electric_cost_per_pouch='$electricCostPerPouch', gas_cost_per_pouch='$gasCostPerPouch', consumable_cost_per_pouch='$consumableCostPerPouch', man_power_cost_per_pouch='$manPowerCostPerPouch', fish_prep_cost_per_pouch='$fishPrepCostPerPouch', modified=NOW(), modifiedby='$userId' where id=$comboMatrixRecId ";		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	// -----------------------------------
	//  Product Pricing
	// -----------------------------------
	# Returns all Product Pricing Records
	function fetchAllProductPricingRecords()
	{
		$qry = " select a.id, a.product_id, a.baisc_manuf_cost, a.buffer, a.incl_buffer, a.profit_margin, a.factory_cost, a.avg_distributor_margin, a.mgn_for_scheme, a.num_packs_one_free, a.mrp, a.actual_profit_margin, a.on_mrp, a.on_factory_cost, b.code from m_product_price a, t_combo_matrix b where b.id=a.product_id order by b.code asc ";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Distributor Wise Product Price
	function getDistProductPriceRecords($productPriceEntryId)
	{
		$qry = " select a.id, a.distributor_id, b.id, b.state_id, b.cost_to_dist_or_stkist, b.actual_distn_cost, b.octroi, b.freight, b.insurance, b.vat_cst, b.excise, b.edu_cess, b.basic_cost, b.cost_margin, b.actual_profit_mgn, b.on_mrp, on_factory_cost from m_dist_product_price a, m_dist_product_price_state b where a.id=b.dist_price_main_id and a.product_price_entry_id='$productPriceEntryId' ";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	function getDistStateWiseRec($distributorId, $stateId, $distMRateListId, $selProduct)
	{
		$qry = " select b.avg_margin, b.transport_cost, b.octroi, b.vat, b.freight from m_distributor_margin a, m_distributor_margin_state b where a.id=b.distributor_margin_id and a.distributor_id='$distributorId' and b.state_id='$stateId' and a.rate_list_id='$distMRateListId' and a.product_id='$selProduct' ";
		//echo "$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);		
		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2], $rec[3], $rec[4]):0;		
	}

	# Update  Product Price Master Rec
	function updateProductPrice($productPriceMasterId, $basicManufCost, $inclBuffer, $factoryProfitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $actualProfitMargin, $onMRP, $onFactoryCost)
	{
		$qry = "update m_product_price set baisc_manuf_cost='$basicManufCost', incl_buffer='$inclBuffer', profit_margin='$factoryProfitMargin', factory_cost='$factoryCost', avg_distributor_margin='$avgDistMgn', mgn_for_scheme='$mgnForScheme', num_packs_one_free='$noOfPacksFree', actual_profit_margin='$actualProfitMargin', on_mrp='$onMRP', on_factory_cost='$onFactoryCost' where id=$productPriceMasterId ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Upate Dist State wise Product Price
	function updateDistProdPriceStateWiseRec($dpriceStateEntryId, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost)
	{
		$qry = " update m_dist_product_price_state set insurance='$insurance', vat_cst='$vatOrCst', excise='$excise', edu_cess='$eduCess', basic_cost='$basicCost', cost_margin='$costMargin', actual_profit_mgn='$actualProfitMgn', on_mrp='$onMrp', on_factory_cost='$onFactoryCost' where id='$dpriceStateEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

}
?>