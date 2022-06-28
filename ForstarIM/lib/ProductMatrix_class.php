<?php
class ProductMatrix
{
	/****************************************************************
	This class deals with all the operations relating to Product Matrix
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductMatrix(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addProductMatrix($productCode, $productName, $netWt, $fishWt, $gravyWt, $percentSeafood, $forExport, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch)
	{
		$qry = "insert into t_product_matrix (code, name, net_wt, fish_wt, gravy_wt, percent_seafood, export, rm_code_id, no_of_btchs, batch_size, sel_fish_id, production_code_id, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch) values('$productCode', '$productName', '$netWt', '$fishWt', '$gravyWt', '$percentSeafood', '$forExport', '$rMCodeId', '$noOfBatches', '$batchSize', '$selFish', '$productionCode', '$packingCode', '$freightChargePerPack', '$productExciseRate', '$pmInPercentOfFc', '$idealFactoryCost', '$contingency', '$actualFactCost', '$productProfitMargin', '$totalCost', '$adminOverhead', '$proHoldingCost', '$proAdvertCost', '$mktgCost', '$basicManufactCost', '$productOuterPkgCost', '$productInnerPkgCost', '$testingCost', '$processingCost', '$rMCost', '$seaFoodCost', '$gravyCost', '$waterCostPerPouch', '$dieselCostPerPouch', '$electricCostPerPouch', '$gasCostPerPouch', '$consumableCostPerPouch', '$manPowerCostPerPouch', '$fishPrepCostPerPouch')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, code, name, net_wt, fish_wt, gravy_wt, percent_seafood, export, rm_code_id, no_of_btchs, batch_size, sel_fish_id, production_code_id, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch from t_product_matrix order by code asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	=	"select id, code, name, net_wt, fish_wt, gravy_wt, percent_seafood, export, rm_code_id, no_of_btchs, batch_size, sel_fish_id, production_code_id, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch from t_product_matrix order by code asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($productMatrixRecId)
	{
		$qry = "select id, code, name, net_wt, fish_wt, gravy_wt, percent_seafood, export, rm_code_id, no_of_btchs, batch_size, sel_fish_id, production_code_id, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch from t_product_matrix where id=$productMatrixRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateProductMatrix($productMatrixRecId, $productCode, $productName, $netWt, $fishWt, $gravyWt, $percentSeafood, $forExport, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch)
	{
		$qry = "update t_product_matrix set code='$productCode', name='$productName', net_wt='$netWt', fish_wt='$fishWt', gravy_wt='$gravyWt', percent_seafood='$percentSeafood', export='$forExport', rm_code_id='$rMCodeId', no_of_btchs='$noOfBatches', batch_size='$batchSize', sel_fish_id='$selFish', production_code_id='$productionCode', packing_code_id='$packingCode', freight_charge_per_pack='$freightChargePerPack', excise_rate='$productExciseRate', pm_in_percent_of_fc='$pmInPercentOfFc', ideal_factory_cost='$idealFactoryCost', contingency='$contingency', actual_fact_cost='$actualFactCost', profit_margin='$productProfitMargin', total_cost='$totalCost', admin_over_head='$adminOverhead', holding_cost='$proHoldingCost', advert_cost='$proAdvertCost', mktg_cost='$mktgCost', basic_manufact_cost='$basicManufactCost', outer_pkg_cost='$productOuterPkgCost', inner_pkg_cost='$productInnerPkgCost', testing_cost='$testingCost', processing_cost='$processingCost', rm_cost='$rMCost', sea_food_cost='$seaFoodCost', gravy_cost='$gravyCost', water_cost_per_pouch='$waterCostPerPouch', diesel_cost_per_pouch='$dieselCostPerPouch', electric_cost_per_pouch='$electricCostPerPouch', gas_cost_per_pouch='$gasCostPerPouch', consumable_cost_per_pouch='$consumableCostPerPouch', man_power_cost_per_pouch='$manPowerCostPerPouch', fish_prep_cost_per_pouch='$fishPrepCostPerPouch' where id=$productMatrixRecId ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}

	# Delete a Record
	function deleteProductMatrixRec($productMatrixRecId)
	{
		$qry	= " delete from t_product_matrix where id=$productMatrixRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}
	
	#Get all Active Products
	function getActiveProducts()
	{
		$qry = "select id, product_name from m_product_matrix where active=1";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}

	#Find Packing material Rate (TABLE: m_packing_material_cost)
	/*function findPackingMaterialRate($masterPackingId)
	{
		$qry = " select pu_cost, tot_cost from m_packing_material_cost where id='$masterPackingId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[1]:0;
	}*/
	///////////////////////
	
}