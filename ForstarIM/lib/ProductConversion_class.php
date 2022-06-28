<?php
class ProductConversion
{  
	/****************************************************************
	This class deals with all the operations relating to Product Conversion
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductConversion(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	#Insert A Rec
	function addProduct($productCode, $productName, $userId, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $selProduct)
	{
		$qry = "insert into m_productmaster (code , name, created, createdby, category_id, state_id, group_id, net_wt, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty, reference_product_id) values('$productCode', '$productName', Now(), '$userId', '$productCategory', '$productState', '$productGroup', '$gmsPerPouch', '$productRatePerPouch', '$fishRatePerPouch', '$gravyRatePerPouch', '$productGmsPerPouch', '$fishGmsPerPouch', '$gravyGmsPerPouch', '$productPercentagePerPouch', '$fishPercentagePerPouch', '$gravyPercentagePerPouch', '$productRatePerKgPerBatch', '$fishRatePerKgPerBatch', '$gravyRatePerKgPerBatch', '$pouchPerBatch', '$productRatePerBatch', '$fishRatePerBatch', '$gravyRatePerBatch', '$productKgPerBatch', '$fishKgPerBatch', '$gravyKgPerBatch', '$productRawPercentagePerPouch', '$fishRawPercentagePerPouch', '$gravyRawPercentagePerPouch', '$productKgInPouchPerBatch', '$fishKgInPouchPerBatch', '$gravyKgInPouchPerBatch', '$fishPercentageYield', '$gravyPercentageYield', '$totalFixedFishQty','$selProduct')";
		//echo $qry."<br>";
			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#For adding Ingredient Items
	function addIngredientEntries($lastId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch)
	{
		$qry = "insert into m_productmaster_entry (product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch) values('$lastId', '$ingredientId', '$quantity', '$fixedQtyChk', '$fixedQty', '$percentagePerBatch', '$ratePerBatch', '$ingGmsPerPouch', '$percentageWtPerPouch', '$ratePerPouch', '$percentageCostPerPouch')";
		//echo $qry;
			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	/*
	percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch	
	*/

	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, code, name from m_productmaster  where base_product='N' order by name asc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select id, code, name from m_productmaster where base_product='N' order by name asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	# Get a Record based on Id
	function find($productId)
	{
		$qry = "select id, code, name, category_id, state_id, group_id, net_wt, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty, base_product, reference_product_id from m_productmaster where id=$productId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on Master Id from m_productmaster_entry TABLE	
	function fetchAllIngredients($editProductId)
	{
		$qry = "select id, product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch from m_productmaster_entry where product_id='$editProductId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update
	function updateProductMaster($productId, $productCode, $productName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $selProduct)
	{
		$qry = "update m_productmaster set code='$productCode', name='$productName', category_id='$productCategory', state_id='$productState', group_id='$productGroup', net_wt='$gmsPerPouch', product_rate_per_pouch='$productRatePerPouch', fish_rate_per_pouch='$fishRatePerPouch', gravy_rate_per_pouch='$gravyRatePerPouch', product_gms_per_pouch='$productGmsPerPouch', fish_gms_per_pouch='$fishGmsPerPouch', gravy_gms_per_pouch='$gravyGmsPerPouch', product_percent_per_pouch='$productPercentagePerPouch', fish_percent_per_pouch='$fishPercentagePerPouch', gravy_percent_per_pouch='$gravyPercentagePerPouch', product_rate_per_kg_per_btch='$productRatePerKgPerBatch', fish_rate_per_kg_per_btch='$fishRatePerKgPerBatch', gravy_rate_per_kg_per_btch='$gravyRatePerKgPerBatch', pouch_per_btch='$pouchPerBatch', product_rate_per_btch='$productRatePerBatch', fish_rate_per_btch='$fishRatePerBatch', gravy_rate_per_btch='$gravyRatePerBatch', product_kg_per_btch='$productKgPerBatch', fish_kg_per_btch='$fishKgPerBatch', gravy_kg_per_btch='$gravyKgPerBatch', pduct_raw_pcent_per_pouch='$productRawPercentagePerPouch', fish_raw_pcent_per_pouch='$fishRawPercentagePerPouch', gravy_raw_pcent_per_pouch='$gravyRawPercentagePerPouch', pduct_kg_pouch_per_btch='$productKgInPouchPerBatch', fish_kg_pouch_per_btch='$fishKgInPouchPerBatch', gravy_kg_pouch_per_btch='$gravyKgInPouchPerBatch', fish_percent_yield='$fishPercentageYield', gravy_percent_yield='$gravyPercentageYield', total_fixed_fish_qty='$totalFixedFishQty', reference_product_id='$selProduct' where id='$productId'";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	#Delete  Purchase Order Item  Recs
	function deleteIngredientItemRecs($productId)
	{
		$qry = " delete from m_productmaster_entry where product_id=$productId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}


	# Delete a Purchase Order
	function deleteProductMaster($productId)
	{
		$qry	=	" delete from m_productmaster where id=$productId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	#Check Whether Product Group Exist
	function checkProductGroupExist($productStateId)
	{
		$qry = "select product_group from m_product_state where id=$productStateId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]=='Y')?true:false;
	}

	#Find Product Name (using in another Screen)
	function getProductName($productId)
	{
		$rec = $this->find($productId);
		return (sizeof($rec)>0)?$rec[2]:"";
	}
	
	#Find the Ingredient Rate
	function getIngredientRate($ingredientId)
	{
		$qry = "select last_price from m_ingredient_rate where ingredient_id=$ingredientId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return(sizeof($rec)>0)?$rec[0]:0;
	}

	#Get Product Rate and other rec  (using in Other Screen - Product Matrix)
	/******
	product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch
	******/
	function getProductMasterRec($productId)
	{
		$rec = $this->find($productId);		
		return (sizeof($rec)>0)?array($rec[16],$rec[17],$rec[18]):0;
	}

	#Get Product Master Rec Based on Id
	function getProductRec($productId)
	{
		$rec = $this->find($productId);
		return (sizeof($rec)>0)?array($rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8], $rec[9], $rec[10], $rec[11], $rec[12], $rec[13], $rec[14], $rec[15], $rec[16], $rec[17], $rec[18], $rec[19], $rec[20], $rec[21], $rec[22], $rec[23], $rec[24], $rec[25], $rec[26], $rec[27], $rec[28], $rec[29], $rec[30], $rec[31], $rec[32], $rec[33], $rec[34]):0;		
	}
}

?>