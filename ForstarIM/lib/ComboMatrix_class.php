<?php
class ComboMatrix
{
	/****************************************************************
	This class deals with all the operations relating to Combo Matrix
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ComboMatrix(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addComboMatrix($productCode, $productName, $forExport, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch, $productCombination, $userId)
	{
		$qry = "insert into t_combo_matrix (code, name, export, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch, num_of_product, createdby, created) values('$productCode', '$productName', '$forExport', '$packingCode', '$freightChargePerPack', '$productExciseRate', '$pmInPercentOfFc', '$idealFactoryCost', '$contingency', '$actualFactCost', '$productProfitMargin', '$totalCost', '$adminOverhead', '$proHoldingCost', '$proAdvertCost', '$mktgCost', '$basicManufactCost', '$productOuterPkgCost', '$productInnerPkgCost', '$testingCost', '$processingCost', '$rMCost', '$seaFoodCost', '$gravyCost', '$waterCostPerPouch', '$dieselCostPerPouch', '$electricCostPerPouch', '$gasCostPerPouch', '$consumableCostPerPouch', '$manPowerCostPerPouch', '$fishPrepCostPerPouch', '$productCombination', $userId, NOW())";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	// Insert Mix product Recs
	function addMixProductRec($lastId, $netWt, $fishWt, $gravyWt, $percentSeafood, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode)
	{
		$qry = "insert into t_combo_matrix_entry (combo_main_id, net_wt, fish_wt, gravy_wt, percent_seafood, rm_code_id, no_of_btchs, batch_size, sel_fish_id, production_code_id) values('$lastId', '$netWt', '$fishWt', '$gravyWt', '$percentSeafood', '$rMCodeId', '$noOfBatches', '$batchSize', '$selFish', '$productionCode')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	/*************************************************/
	# Insert New Product
	function addProduct($newProductCode, $newProductName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $selProduct, $userId, $entryLastId, $ingRateList)
	{
		$qry = "insert into m_productmaster (code , name, category_id, state_id, group_id, net_wt, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty, reference_product_id, created, createdby, combo_matrix_entry_id, ing_rate_list_id) values('$newProductCode', '$newProductName', '$productCategory', '$productState', '$productGroup', '$gmsPerPouch', '$productRatePerPouch', '$fishRatePerPouch', '$gravyRatePerPouch', '$productGmsPerPouch', '$fishGmsPerPouch', '$gravyGmsPerPouch', '$productPercentagePerPouch', '$fishPercentagePerPouch', '$gravyPercentagePerPouch', '$productRatePerKgPerBatch', '$fishRatePerKgPerBatch', '$gravyRatePerKgPerBatch', '$pouchPerBatch', '$productRatePerBatch', '$fishRatePerBatch', '$gravyRatePerBatch', '$productKgPerBatch', '$fishKgPerBatch', '$gravyKgPerBatch', '$productRawPercentagePerPouch', '$fishRawPercentagePerPouch', '$gravyRawPercentagePerPouch', '$productKgInPouchPerBatch', '$fishKgInPouchPerBatch', '$gravyKgInPouchPerBatch', '$fishPercentageYield', '$gravyPercentageYield', '$totalFixedFishQty','$selProduct', Now(), '$userId', '$entryLastId', '$ingRateList')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	#For adding Ingredient Items
	function addIngredientEntries($lastId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType)
	{
		$qry = "insert into m_productmaster_entry (product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type) values('$lastId', '$ingredientId', '$quantity', '$fixedQtyChk', '$fixedQty', '$percentagePerBatch', '$ratePerBatch', '$ingGmsPerPouch', '$percentageWtPerPouch', '$ratePerPouch', '$percentageCostPerPouch', '$cleanedQty', '$ingType')";
		//echo $qry;
			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	/**************************************************/

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, code, name, export, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch from t_combo_matrix order by code asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select id, code, name, export, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch from t_combo_matrix order by code asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}


	# Get a Record based on id
	function find($comboMatrixRecId)
	{
		$qry = "select id, code, name, export, packing_code_id, freight_charge_per_pack, excise_rate, pm_in_percent_of_fc, ideal_factory_cost, contingency, actual_fact_cost, profit_margin, total_cost, admin_over_head, holding_cost, advert_cost, mktg_cost, basic_manufact_cost, outer_pkg_cost, inner_pkg_cost, testing_cost, processing_cost, rm_cost, sea_food_cost, gravy_cost, water_cost_per_pouch, diesel_cost_per_pouch, electric_cost_per_pouch, gas_cost_per_pouch, consumable_cost_per_pouch, man_power_cost_per_pouch, fish_prep_cost_per_pouch, num_of_product from t_combo_matrix where id=$comboMatrixRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	function fetchMixProductRecs($editComboMatrixId)
	{
		$qry = " select id, net_wt, fish_wt, gravy_wt, percent_seafood, rm_code_id, no_of_btchs, batch_size, sel_fish_id, production_code_id from  t_combo_matrix_entry where combo_main_id='$editComboMatrixId'";
		//echo $qry."<br>";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  a  Record
	function updateComboMatrix($comboMatrixRecId, $productCode, $productName, $forExport, $packingCode, $freightChargePerPack, $productExciseRate, $pmInPercentOfFc, $idealFactoryCost, $contingency, $actualFactCost, $productProfitMargin, $totalCost, $adminOverhead, $proHoldingCost, $proAdvertCost, $mktgCost, $basicManufactCost, $productOuterPkgCost, $productInnerPkgCost, $testingCost, $processingCost, $rMCost, $seaFoodCost, $gravyCost, $waterCostPerPouch, $dieselCostPerPouch, $electricCostPerPouch, $gasCostPerPouch, $consumableCostPerPouch, $manPowerCostPerPouch, $fishPrepCostPerPouch, $productCombination, $userId)
	{
		$qry = "update t_combo_matrix set code='$productCode', name='$productName', export='$forExport', packing_code_id='$packingCode', freight_charge_per_pack='$freightChargePerPack', excise_rate='$productExciseRate', pm_in_percent_of_fc='$pmInPercentOfFc', ideal_factory_cost='$idealFactoryCost', contingency='$contingency', actual_fact_cost='$actualFactCost', profit_margin='$productProfitMargin', total_cost='$totalCost', admin_over_head='$adminOverhead', holding_cost='$proHoldingCost', advert_cost='$proAdvertCost', mktg_cost='$mktgCost', basic_manufact_cost='$basicManufactCost', outer_pkg_cost='$productOuterPkgCost', inner_pkg_cost='$productInnerPkgCost', testing_cost='$testingCost', processing_cost='$processingCost', rm_cost='$rMCost', sea_food_cost='$seaFoodCost', gravy_cost='$gravyCost', water_cost_per_pouch='$waterCostPerPouch', diesel_cost_per_pouch='$dieselCostPerPouch', electric_cost_per_pouch='$electricCostPerPouch', gas_cost_per_pouch='$gasCostPerPouch', consumable_cost_per_pouch='$consumableCostPerPouch', man_power_cost_per_pouch='$manPowerCostPerPouch', fish_prep_cost_per_pouch='$fishPrepCostPerPouch', num_of_product='$productCombination', modified=NOW(), modifiedby='$userId' where id=$comboMatrixRecId ";
		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	// Update Mix product Recs
	function updateMixProductRec($productEntryId, $netWt, $fishWt, $gravyWt, $percentSeafood, $rMCodeId, $noOfBatches, $batchSize, $selFish, $productionCode)
	{
		$qry = "update t_combo_matrix_entry set net_wt='$netWt', fish_wt='$fishWt', gravy_wt='$gravyWt', percent_seafood='$percentSeafood', rm_code_id='$rMCodeId', no_of_btchs='$noOfBatches', batch_size='$batchSize', sel_fish_id='$selFish', production_code_id='$productionCode' where id='$productEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete Mix Product Rec
	function deleteMixProductRec($comboMatrixRecId)
	{
		$qry	= " delete from t_combo_matrix_entry where combo_main_id=$comboMatrixRecId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Record (Combo Matrix Rec)
	function deleteComboMatrixRec($comboMatrixRecId)
	{
		$qry	= " delete from t_combo_matrix where id=$comboMatrixRecId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}	
	
	#Fetch All Records based on Master Id from m_productmaster_entry TABLE	
	function fetchAllIngredients($editProductId, $selRateList)
	{
		/* b.name, c.rate_per_kg */
		$qry = " select id, product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type from m_productmaster_entry where product_id='$editProductId' ";

		//echo $qry."<br>";

		/* Edited on 15-10-08
		$qry = "select a.id, a.product_id, a.ingredient_id, a.quantity, a.fixed_qty_chk, a.fixed_qty, a.percent_per_btch, a.rate_per_btch, a.ing_gms_per_pouch, a.percent_wt_per_pouch, a.rate_per_pouch, a.percent_cost_per_pouch, b.name, c.rate_per_kg, a.cleaned_qty from m_productmaster_entry a, m_ingredient b, m_ingredient_rate c where a.ingredient_id=b.id and b.id=c.ingredient_id and a.product_id='$editProductId' and c.rate_list_id=$selRateList ";
		*/
		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# get record from Product Master
	function getProductMasterRecs($comboProductEntryId)
	{
		$qry = " select id, reference_product_id from m_productmaster where combo_matrix_entry_id=$comboProductEntryId";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";
	}


	#Delete  Product Master ing Item  Recs
	function deleteIngredientItemRecs($productId)
	{
		$qry = " delete from m_productmaster_entry where product_id=$productId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	# Delete from Product Master
	function deleteProductMaster($productId)
	{
		$qry	= " delete from m_productmaster where id=$productId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}	
}