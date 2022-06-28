<?php
class ProductBatch
{  
	/****************************************************************
	This class deals with all the operations relating to Product Batch
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductBatch(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert Record
	function addProductBatch($batchNo, $selProduct, $productGmsPerPouch, $pouchPerBatch, $userId, $startTime, $stopTime, $phFactorValue, $foFactorValue, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty)
	{
		$qry = "insert into t_productbatch (batch_no, product_id, product_qty, num_pouch, created, createdby, start_time, end_time, ph_factor, fo_factor, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty) values ('$batchNo', '$selProduct', '$productGmsPerPouch', '$pouchPerBatch',  Now(), '$userId', '$startTime', '$stopTime', '$phFactorValue', '$foFactorValue', '$productRatePerPouch', '$fishRatePerPouch', '$gravyRatePerPouch', '$productGmsPerPouch', '$fishGmsPerPouch', '$gravyGmsPerPouch', '$productPercentagePerPouch', '$fishPercentagePerPouch', '$gravyPercentagePerPouch', '$productRatePerKgPerBatch', '$fishRatePerKgPerBatch', '$gravyRatePerKgPerBatch', '$pouchPerBatch', '$productRatePerBatch', '$fishRatePerBatch', '$gravyRatePerBatch', '$productKgPerBatch', '$fishKgPerBatch', '$gravyKgPerBatch', '$productRawPercentagePerPouch', '$fishRawPercentagePerPouch', '$gravyRawPercentagePerPouch', '$productKgInPouchPerBatch', '$fishKgInPouchPerBatch', '$gravyKgInPouchPerBatch', '$fishPercentageYield', '$gravyPercentageYield', '$totalFixedFishQty')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	#For adding Ingredient  Items
	function addIngredientRec($lastId, $ingredientId, $quantity, $fixedQtyChk, $currentStock, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $cleanedQty, $ingType)
	{
		$qry = "insert into t_productbatch_entry (productbatch_id, ingredient_id, quantity, fixed_qty_chk, current_stock, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type) values('$lastId', '$ingredientId', '$quantity', '$fixedQtyChk', '$currentStock', '$fixedQty', '$percentagePerBatch', '$ratePerBatch', '$ingGmsPerPouch', '$percentageWtPerPouch', '$ratePerPouch', '$percentageCostPerPouch', '$cleanedQty', '$ingType')";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Update Batch Fixed Qty 
	function updateBatchFixedQty($productBatchId, $qty)
	{
		$qry = "update t_productbatch set fixed_qty='$qty' where id='$productBatchId'";
		
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
		$qry = "select a.id, a.batch_no, a.product_id, a.created, a.createdby, b.name from t_productbatch a, m_productmaster b where a.product_id=b.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.created desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Returns all Records
	function fetchDateRangeRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, a.batch_no, a.product_id, a.created, a.createdby, b.name from t_productbatch a, m_productmaster b where a.product_id=b.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	


	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.batch_no, a.product_id, a.created, a.createdby, b.name from t_productbatch a, m_productmaster b where a.product_id=b.id order by a.created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	/***********************************************/
	
	# Get Product batch Rec
	function find($productBatchId)
	{
		$qry = "select id, batch_no, product_id, created, createdby, product_qty, num_pouch, start_time, end_time, ph_factor, fo_factor, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty from t_productbatch where id=$productBatchId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	#Fetch All Records based on Product batch Id from t_productbatch_entries TABLE
	function fetchAllStockItem($productBatchId)
	{
		$qry = " select id, productbatch_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type from t_productbatch_entry where productbatch_id='$productBatchId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Delete  Ingredient  Recs
	function deleteIngredientRecs($productBatchId)
	{
		$qry	= " delete from t_productbatch_entry where productbatch_id=$productBatchId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	# Delete a Rec
	function deleteProductBatch($productBatchId)
	{
		#Update stock when delete Receipt entries
		$ingredientRecs = $this->fetchAllStockItem($productBatchId);
		if (sizeof($ingredientRecs)>0) {
			foreach ($ingredientRecs as $ir) {
				$ingredientId 	= $ir[2];
				$qtyUsed  	= $ir[3];
				$selIngType	= $ir[13];
				if ($selIngType=='ING') { 
					$this->updateStockQty($ingredientId, $qtyUsed);
				} else if ($selIngType=='SFP') {
					$updateSemiFinishIngItem = $this->updateSemiStkQty($ingredientId);
				}
			}
		}
		$qry	= " delete from t_productbatch where id=$productBatchId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Record
	function updateProductBatch($productBatchId, $batchNo, $selProduct, $productGmsPerPouch, $pouchPerBatch, $startTime, $stopTime, $phFactorValue, $foFactorValue, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty)
	{
		$qry = "update t_productbatch set batch_no='$batchNo', product_id='$selProduct', product_qty='$productGmsPerPouch', num_pouch='$pouchPerBatch', start_time='$startTime', end_time='$stopTime', ph_factor='$phFactorValue', fo_factor='$foFactorValue', product_rate_per_pouch='$productRatePerPouch', fish_rate_per_pouch='$fishRatePerPouch', gravy_rate_per_pouch='$gravyRatePerPouch', product_gms_per_pouch='$productGmsPerPouch', fish_gms_per_pouch='$fishGmsPerPouch', gravy_gms_per_pouch='$gravyGmsPerPouch', product_percent_per_pouch='$productPercentagePerPouch', fish_percent_per_pouch='$fishPercentagePerPouch', gravy_percent_per_pouch='$gravyPercentagePerPouch', product_rate_per_kg_per_btch='$productRatePerKgPerBatch', fish_rate_per_kg_per_btch='$fishRatePerKgPerBatch', gravy_rate_per_kg_per_btch='$gravyRatePerKgPerBatch', pouch_per_btch='$pouchPerBatch', product_rate_per_btch='$productRatePerBatch', fish_rate_per_btch='$fishRatePerBatch', gravy_rate_per_btch='$gravyRatePerBatch', product_kg_per_btch='$productKgPerBatch', fish_kg_per_btch='$fishKgPerBatch', gravy_kg_per_btch='$gravyKgPerBatch', pduct_raw_pcent_per_pouch='$productRawPercentagePerPouch', fish_raw_pcent_per_pouch='$fishRawPercentagePerPouch', gravy_raw_pcent_per_pouch='$gravyRawPercentagePerPouch', pduct_kg_pouch_per_btch='$productKgInPouchPerBatch', fish_kg_pouch_per_btch='$fishKgInPouchPerBatch', gravy_kg_pouch_per_btch='$gravyKgInPouchPerBatch', fish_percent_yield='$fishPercentageYield', gravy_percent_yield='$gravyPercentageYield', total_fixed_fish_qty='$totalFixedFishQty' where id='$productBatchId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Check Whether Product Group Exist
	function checkProductGroupExist($productId)
	{
		$qry = "select group_id from m_productmaster where id=$productId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]!=0)?true:false;
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

	#Update the Balance Stock Qty
	function updateBalanceStockQty($ingredientId, $balanceQty)
	{
		$qry = " update m_ingredient set actual_quantity='$balanceQty' where id='$ingredientId'";

		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Update the stock Qty [Qty>0 add stock else Less stock ] - Linked with Stock Issuance
	function updateStockQty($ingredientId, $qtyReceived)
	{
		$updateField = "";
		
		if ($qtyReceived>0) $updateField = "actual_quantity=actual_quantity+$qtyReceived";
		else $updateField = "actual_quantity=actual_quantity-'".abs($qtyReceived)."'";

		$qry = "update m_ingredient set $updateField where id=$ingredientId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	#Find Product Name
	function getBatchNo($productBatchId)
	{
		$rec = $this->find($productBatchId);
		return (sizeof($rec)>0)?$rec[1]:"";
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

	function fetchAllProductMatrixRecordsActiveProducts()
	{
		$qry	= " select id, code, name from m_productmaster where base_product='N' and active=1 order by name asc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Fetch All Records based on Master Id from m_productmaster_entry TABLE	
	function fetchAllIngredients($productMasterId, $selRateListId)
	{
		$qry = " select id, product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type from m_productmaster_entry where product_id='$productMasterId' ";
	

		/* Edited 16-10-08
			$qry = " select a.id, a.product_id, a.ingredient_id, a.quantity, a.fixed_qty_chk, a.fixed_qty, a.percent_per_btch, a.rate_per_btch, a.ing_gms_per_pouch, a.percent_wt_per_pouch, a.rate_per_pouch, a.percent_cost_per_pouch, b.name, c.rate_per_kg, a.cleaned_qty from m_productmaster_entry a, m_ingredient b, m_ingredient_rate c where a.ingredient_id=b.id and b.id=c.ingredient_id and a.product_id='$productMasterId' and c.rate_list_id='$selRateListId' order by a.id asc";
		*/
		//echo $qry."<br>";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Semi finished Product Stock Qty
	function getSemiFinishProductStkQty($productId)
	{
		//$qry = " select id, product_id, ingredient_id, quantity, sel_ing_type from m_productmaster_entry where product_id='$productId' ";
		$qry = " select id, sf_product_id, ingredient_id, raw_qty from m_sf_product_entry where sf_product_id='$productId' ";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$totalStkQty = 0;
			$stkNotExist = false;
			foreach ($result as $r) {
				$ingId 	= $r[2];	
				$ingQty = $r[3];
				$stkQty = $this->getTotalStockQty($ingId);
				//echo "<br>$stkQty<$ingQty<br>";
				$totalStkQty += $stkQty;				
				if ($stkQty==0 && $stkQty<$ingQty) $stkNotExist= true;
			}
		}
		return ($totalStkQty>0 && !$stkNotExist)?$totalStkQty:0;
	}

	# Update the semi Finished Product Stk Qty
	function updateSemiFinishStkQty($productId, $rawQty)
	{
		//$qry = " select id, product_id, ingredient_id, quantity, sel_ing_type, percent_per_btch from m_productmaster_entry where product_id='$productId' ";
		$qry = " select id, sf_product_id, ingredient_id, raw_qty, percent_per_btch from m_sf_product_entry where sf_product_id='$productId' ";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$totalQty 	= 0;
			$currentStock 	= 0;
			$calcQty	= 0;
			foreach ($result as $r) {
				$ingredientId 	 = $r[2];
				$percentPerBatch = $r[4];	
				$calcQty 	= ($rawQty*$percentPerBatch)/100;
				//$quantity 	= $r[3];
				$quantity 	= number_format($calcQty,2,'.','');
				$totalQty 	= $this->getTotalStockQty($ingredientId);
				$currentStock = $totalQty - $quantity;
				# Update the Stock
				$updateStockQty = $this->updateBalanceStockQty($ingredientId, $currentStock);
			}
		}				
	}

	# When Delete upate the stk	
	function updateSemiStkQty($productId)
	{
		$qry = " select id, product_id, ingredient_id, quantity, sel_ing_type, percent_per_btch from m_productmaster_entry where product_id='$productId' ";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$totalQty 	= 0;
			$currentStock 	= 0;
			foreach ($result as $r) {
				$ingredientId 	= $r[2];	
				//$quantity 	= $r[3];		
				$percentPerBatch = $r[5];	
				$calcQty 	= ($rawQty*$percentPerBatch)*100;
				$quantity 	= number_format($calcQty,2,'.','');
				$this->updateStockQty($ingredientId, $quantity);
			}
		}
	}
	
	# Upate semi Finish Master Rec		
	function updateSemiFinishProduct($productId, $ingQty)
	{
		$qry = " update m_sf_product set actual_qty='$ingQty' where id='$productId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# Update the Semi Prouct stock Qty [Qty>0 add stock else Less stock ] 
	function updateSemiProductStockQty($productId, $qtyReceived)
	{
		$updateField = "";		
		if ($qtyReceived>0) $updateField = "actual_qty=actual_qty+$qtyReceived";
		else $updateField = "actual_qty=actual_qty-'".abs($qtyReceived)."'";

		$qry = "update m_sf_product set $updateField where id='$productId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

}