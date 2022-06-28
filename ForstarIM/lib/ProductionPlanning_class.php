<?php
class ProductionPlanning
{  
	/****************************************************************
	This class deals with all the operations relating to Production Planning
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductionPlanning(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert Record
	function addProductionPlan($plannedDate, $selProduct, $productGmsPerPouch, $pouchPerBatch, $userId, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty)
	{
		$qry = "insert into t_production_plan (planned_date, product_id, product_qty, num_pouch, created, createdby, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty) values ('$plannedDate', '$selProduct', '$productGmsPerPouch', '$pouchPerBatch',  Now(), '$userId', '$productRatePerPouch', '$fishRatePerPouch', '$gravyRatePerPouch', '$productGmsPerPouch', '$fishGmsPerPouch', '$gravyGmsPerPouch', '$productPercentagePerPouch', '$fishPercentagePerPouch', '$gravyPercentagePerPouch', '$productRatePerKgPerBatch', '$fishRatePerKgPerBatch', '$gravyRatePerKgPerBatch', '$pouchPerBatch', '$productRatePerBatch', '$fishRatePerBatch', '$gravyRatePerBatch', '$productKgPerBatch', '$fishKgPerBatch', '$gravyKgPerBatch', '$productRawPercentagePerPouch', '$fishRawPercentagePerPouch', '$gravyRawPercentagePerPouch', '$productKgInPouchPerBatch', '$fishKgInPouchPerBatch', '$gravyKgInPouchPerBatch', '$fishPercentageYield', '$gravyPercentageYield', '$totalFixedFishQty')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#For adding Ingredient  Items
	function addIngredientRec($lastId, $ingredientId, $quantity, $fixedQtyChk, $currentStock, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType)
	{
		$qry = "insert into t_production_plan_entry (production_plan_id, ingredient_id, quantity, fixed_qty_chk, current_stock, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type) values('$lastId', '$ingredientId', '$quantity', '$fixedQtyChk', '$currentStock', '$fixedQty', '$percentagePerBatch', '$ratePerBatch', '$ingGmsPerPouch', '$percentageWtPerPouch', '$ratePerPouch', '$percentageCostPerPouch', '$cleanedQty', '$ingType')";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Update Batch Fixed Qty 
	function updateBatchFixedQty($productBatchId, $qty)
	{
		$qry = "update t_production_plan set fixed_qty='$qty' where id='$productBatchId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	/***********************************************/
	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.planned_date, a.product_id, a.created, a.createdby, b.name, a.num_pouch from t_production_plan a, m_productmaster b where a.product_id=b.id and a.planned_date>='$fromDate' and a.planned_date<='$tillDate' order by a.planned_date desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Returns all Records
	function fetchDateRangeRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, a.planned_date, a.product_id, a.created, a.createdby, b.name, a.num_pouch from t_production_plan a, m_productmaster b where a.product_id=b.id and a.planned_date>='$fromDate' and a.planned_date<='$tillDate' order by a.planned_date desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	

	/*
	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.planned_date, a.product_id, a.created, a.createdby, b.name from t_production_plan a, m_productmaster b where a.product_id=b.id order by a.created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/
	/***********************************************/
	
	# Get Product batch Rec
	function find($productBatchId)
	{
		$qry = "select id, planned_date, product_id, created, createdby, product_qty, num_pouch, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty from t_production_plan where id=$productBatchId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	#Fetch All Records based on Product batch Id from t_production_plan_entries TABLE
	function fetchAllStockItem($productBatchId)
	{
		$qry = "select id, production_plan_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty from t_production_plan_entry where production_plan_id='$productBatchId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Delete  Ingredient  Recs
	function deleteIngredientRecs($productBatchId)
	{
		$qry	= " delete from t_production_plan_entry where production_plan_id=$productBatchId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Rec
	function deleteProductBatch($productBatchId)
	{
		$qry	= " delete from t_production_plan where id=$productBatchId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}	

	# Update  a  Record
	function updateProductionPlan($productBatchId, $plannedDate, $selProduct, $productGmsPerPouch, $pouchPerBatch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty)
	{
		$qry = "update t_production_plan set planned_date='$plannedDate', product_id='$selProduct', product_qty='$productGmsPerPouch', num_pouch='$pouchPerBatch', product_rate_per_pouch='$productRatePerPouch', fish_rate_per_pouch='$fishRatePerPouch', gravy_rate_per_pouch='$gravyRatePerPouch', product_gms_per_pouch='$productGmsPerPouch', fish_gms_per_pouch='$fishGmsPerPouch', gravy_gms_per_pouch='$gravyGmsPerPouch', product_percent_per_pouch='$productPercentagePerPouch', fish_percent_per_pouch='$fishPercentagePerPouch', gravy_percent_per_pouch='$gravyPercentagePerPouch', product_rate_per_kg_per_btch='$productRatePerKgPerBatch', fish_rate_per_kg_per_btch='$fishRatePerKgPerBatch', gravy_rate_per_kg_per_btch='$gravyRatePerKgPerBatch', pouch_per_btch='$pouchPerBatch', product_rate_per_btch='$productRatePerBatch', fish_rate_per_btch='$fishRatePerBatch', gravy_rate_per_btch='$gravyRatePerBatch', product_kg_per_btch='$productKgPerBatch', fish_kg_per_btch='$fishKgPerBatch', gravy_kg_per_btch='$gravyKgPerBatch', pduct_raw_pcent_per_pouch='$productRawPercentagePerPouch', fish_raw_pcent_per_pouch='$fishRawPercentagePerPouch', gravy_raw_pcent_per_pouch='$gravyRawPercentagePerPouch', pduct_kg_pouch_per_btch='$productKgInPouchPerBatch', fish_kg_pouch_per_btch='$fishKgInPouchPerBatch', gravy_kg_pouch_per_btch='$gravyKgInPouchPerBatch', fish_percent_yield='$fishPercentageYield', gravy_percent_yield='$gravyPercentageYield', total_fixed_fish_qty='$totalFixedFishQty' where id='$productBatchId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	

	#Check Whether Product Group Exist
	function getProductNetWt($productId)
	{
		$qry = "select net_wt from m_productmaster where id=$productId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}


	#Get Total Qty of a Stock Item (usng in GRN)
	function  getTotalStockQty($ingredientId)
	{
		$qry = "select actual_quantity from m_ingredient where id='$ingredientId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	# Returns all Records
	function fetchAllProductMatrixRecords()
	{
		$qry	= " select id, code, name from m_productmaster where base_product='N' order by name asc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch All Records based on Master Id from m_productmaster_entry TABLE	
	function fetchAllIngredients($productMasterId, $selRateListId)
	{
		$qry = " select id, product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type from m_productmaster_entry where product_id='$productMasterId' ";

		/* Edit 16-10-08
		$qry = " select a.id, a.product_id, a.ingredient_id, a.quantity, a.fixed_qty_chk, a.fixed_qty, a.percent_per_btch, a.rate_per_btch, a.ing_gms_per_pouch, a.percent_wt_per_pouch, a.rate_per_pouch, a.percent_cost_per_pouch, b.name, c.rate_per_kg, a.cleaned_qty, c.yield from m_productmaster_entry a, m_ingredient b, m_ingredient_rate c where a.ingredient_id=b.id and b.id=c.ingredient_id and a.product_id='$productMasterId' and c.rate_list_id=$selRateListId  order by a.id asc";
		*/
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check for Same Production Plan Entry
	function productionPlanEntryExist($selProduct, $plannedDate, $productionPlanId)
	{
		if ($productionPlanId!="") $updteQry = " and id!=$productionPlanId";
		else $updteQry = "";

		$qry = " select id from t_production_plan where product_id='$selProduct' and planned_date='$plannedDate' $updteQry";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;
	}
}