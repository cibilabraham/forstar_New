<?php
class IngredientRateMaster
{
	/****************************************************************
	This class deals with all the operations relating to Ingredient Rate Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function IngredientRateMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addIngredientRate($selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $rawIngredient, $ingLastPrice, $cleanedCost, $materialType,  $effectiveDate, $userId, $cleaningYield)
	{
		$qry	= "insert into m_ingredient_rate (ingredient_id, rate_per_kg, yield, highest_price, lowest_price, raw_ingredient, last_price, cleaned_cost, material_type, start_date, created, createdby, cleaning_yield, active) values('$selIngredient', '$ingRatePerKg', '$ingYield', '$ingHighPrice', '$ingLowPrice', '$rawIngredient', '$ingLastPrice', '$cleanedCost','$materialType', '$effectiveDate', Now(), '$userId', '$cleaningYield', '0')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $categoryFilterId, $mainCategoryFilterId, $ingredientFilterId)
	{
		$whr = " c.id=b.category_id and b.id=a.ingredient_id and b.active='1'";
		
		//echo "CategoryFilterId = ".$categoryFilterId;
		if ($categoryFilterId!="") $whr .= " and b.category_id=".$categoryFilterId;
		if ($mainCategoryFilterId!="") $whr .= " and c.main_category_id=".$mainCategoryFilterId;
		if ($ingredientFilterId!="") $whr .= " and a.ingredient_id=".$ingredientFilterId;

		$orderBy 	= " start_date desc";
		$limit 		= " $offset,$limit";
		if ($ingredientFilterId!="") 
			$groupBy    = "";
		else
			$groupBy    = "ingredient";

		$qry = " select * from(select a.id as id, a.ingredient_id as ingredient, a.rate_per_kg as rateperkg, a.yield as yield, a.highest_price as highest_price, a.lowest_price as lowest_price, a.last_price as last_price, a.start_date as start_date, b.name as name, a.active as active, a.cleaned_cost as cleancost, a.material_type as material_type from m_ingredient_rate a left join m_ingredient b on b.id=a.ingredient_id left join ing_category c on c.id=b.category_id left join ing_main_category d on c.main_category_id=d.id ";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy.") dum";
		if($groupBy!="") $qry .= " group by ".$groupBy." order by name asc";
		if ($limit!="") $qry .= " limit ".$limit;
		 
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function ingredientRateRecFilter($categoryFilterId, $mainCategoryFilterId, $ingredientFilterId)
	{
		$whr = " c.id=b.category_id and b.id=a.ingredient_id and b.active='1'";

		if ($categoryFilterId!="") $whr .= " and b.category_id=".$categoryFilterId;
		if ($mainCategoryFilterId!="") $whr .= " and c.main_category_id=".$mainCategoryFilterId;
		if ($ingredientFilterId!="") $whr .= " and a.ingredient_id=".$ingredientFilterId;

		$orderBy 	= " start_date desc";
		if ($ingredientFilterId!="") 
			$groupBy    = "";
		else
			$groupBy    = "ingredient";
		
		//$qry = "select a.id, a.ingredient_id, a.rate_per_kg, a.yield, a.highest_price, a.lowest_price, a.last_price, a.rate_list_id, b.name,a.cleaned_cost,a.material_type from (m_ingredient_rate a, m_ingredient b, ing_category c) left join ing_main_category d on c.main_category_id=d.id";

		$qry = " select * from(select a.id as id, a.ingredient_id as ingredient, a.rate_per_kg as rateperkg, a.yield as yield, a.highest_price as highest_price, a.lowest_price as lowest_price, a.last_price as last_price, a.start_date as start_date, b.name as name, a.active as active, a.cleaned_cost as cleancost, a.material_type as material_type from m_ingredient_rate a left join m_ingredient b on b.id=a.ingredient_id left join ing_category c on c.id=b.category_id left join ing_main_category d on c.main_category_id=d.id ";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy.") dum";
		if($groupBy!="") $qry .= " group by ".$groupBy." order by name asc";		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry = "select a.id, a.ingredient_id, a.rate_per_kg, a.yield, a.highest_price, a.lowest_price, a.last_price, a.rate_list_id, b.name,a.cleaned_cost,a.material_type from m_ingredient_rate a, m_ingredient b, ing_category c where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by c.name asc, b.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Ing Records (using in Ingredient PO)
	function fetchAllIngredientRecords($selRateList)
	{
		$qry = "select a.id, a.ingredient_id, a.rate_per_kg, a.yield, a.highest_price, a.lowest_price, a.last_price, a.rate_list_id, b.name,a.cleaned_cost,a.material_type from m_ingredient_rate a, m_ingredient b, ing_category c where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by c.name asc, b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($ingredientRateId)
	{
		$qry = "select id, ingredient_id, rate_per_kg, yield, highest_price, lowest_price, last_price, rate_list_id, cleaned_cost, material_type, raw_ingredient, start_date, end_date, cleaning_yield from m_ingredient_rate where id=$ingredientRateId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Record
	function deleteIngredientRate($ingredientRateId)
	{
		$qry	= " delete from m_ingredient_rate where id=$ingredientRateId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Record
	function updateIngredientRate($ingredientRateId, $selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $rawIngredient, $ingLastPrice, $cleanedCost,$materialType, $effectDate, $updNewEffectiveDate, $cleaningYield)
	{
		$qry	= " update m_ingredient_rate set ingredient_id='$selIngredient', rate_per_kg='$ingRatePerKg', yield='$ingYield', highest_price='$ingHighPrice', lowest_price='$ingLowPrice', raw_ingredient='$rawIngredient', last_price='$ingLastPrice', cleaned_cost='$cleanedCost', material_type='$materialType', start_date='$effectDate', end_date='$updNewEffectiveDate' cleaning_yield='$cleaningYield' where id=$ingredientRateId ";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	#Checking same entry exist
	function checkEntryExist($selIngredient,$effectiveDate)
	{
		$qry = "select id from m_ingredient_rate where ingredient_id='$selIngredient' and (('$effectiveDate' between start_date and end_date) or (start_date='$effectiveDate' and end_date='0000-00-00'))";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Checking for Start Date Greater than Effective Date
	function checkGreaterStartDate($selIngredient,$effectiveDate)
	{
		$qry = "select id, start_date, end_date from m_ingredient_rate where ingredient_id='$selIngredient' and (start_date>'$effectiveDate' and end_date='0000-00-00')";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Checking for Start Date Smaller than Effective Date
	function checkSmallerStartDate($selIngredient,$effectiveDate)
	{
		$qry = "select id, start_date from m_ingredient_rate where ingredient_id='$selIngredient' and (start_date<'$effectiveDate' and end_date='0000-00-00')";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Update the Existing End Date when Start Date is Smaller than Effective Date 
	function updateExistEntry($updateId,$endDate)
	{
		$qry = "update m_ingredient_rate set end_date='$endDate' where id='$updateId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	#Add new record while Start Date is Smaller than Effective Date
	function addSmallerStartDateRate($selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $rawIngredient, $ingLastPrice, $cleanedCost, $materialType, $effectiveDate, $updateEndDate, $userId, $cleaningYield)
	{
		$qry = "insert into m_ingredient_rate (ingredient_id, rate_per_kg, yield, highest_price, lowest_price, raw_ingredient, last_price, cleaned_cost, material_type, start_date, end_date, created, createdby, cleaning_yield, active) values('$selIngredient', '$ingRatePerKg', '$ingYield', '$ingHighPrice', '$ingLowPrice', '$rawIngredient', '$ingLastPrice', '$cleanedCost','$materialType', '$effectiveDate', '$updateEndDate', Now(), '$userId', '$cleaningYield', '0')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	/*
	#Get Total Qty of a Ingredient (usng in Other Screen)
	function  getTotalStockQty($ingredientId)
	{
		$qry = "select actual_quantity from m_ingredient where id='$ingredientId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	*/

	# Check Any entry exist in another table
	function checkMoreEntriesExist($ingredientId)
	{
		$qry = "select id from m_ingredient_rate where ingredient_id='$ingredientId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	#Check any entry exist in Recipe Master table
	function checkRecipeIngredient($ingredientRateId)
	{
		$qry = "select id from m_recipemaster_entry where ingredient_rate_id='$ingredientRateId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	/**
	* Get Last Three Revised Rate of the selected Ingredient 	
	*/
	function getRevisedRateListRecs($selIngredient)
	{
		$qry = " select a.created, b.rate from ing_purchaseorder a left join ing_purchaseorder_entry b on a.id=b.po_id where b.ingredient_id='$selIngredient' order by b.id desc limit 0,3";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	/**
	* Find the Ingredient Rate 
	* Return Rate Per Kg, Yield, Clean Rate
	* Format list($rawRatePerKg, $yield, $cleanRatePerKg) = $ingredientRateMasterObj->getIngRate($ingredientId, $selRateListId);
	*/
	function getIngRate($ingredientId, $selRateListId)
	{		
		$qry = "select rate_per_kg, yield, last_price from m_ingredient_rate where ingredient_id='$ingredientId' and rate_list_id='$selRateListId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2]):array();
	}

	#confirm RateList
	function updateRateListconfirm($ingredientId)
	{
	$qry	= "update m_ingredient_rate set active='1' where id=$ingredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}

	#Release Confirm of RateList
	function updateRateListReleaseconfirm($ingredientId)
	{
		$qry	= "update m_ingredient_rate set active='0' where id=$ingredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
	//Get Material Type of an Ingredient
	function displayMaterialType($ingredientId)
	{
		$qry = "select material_type from m_ingredient where id=$ingredientId";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}
	
	//Get the Rate and Yield for particular Ingredient
	function displayRateYield($rawIngredientId)
	{
		$qry = "select rate_per_kg, yield from m_ingredient_rate where ingredient_id='$rawIngredientId' order by id desc limit 1";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	//Update only rate if end date exist for a record
	function updateOnlyRate($ingredientRateId, $ingRatePerKg, $ingYield, $ingLastPrice, $cleanedCost, $cleaningYield)
	{
		$qry = "update m_ingredient_rate set rate_per_kg='$ingRatePerKg', yield='$ingYield', last_price='$ingLastPrice', cleaned_cost='$cleanedCost', cleaning_yield='$cleaningYield' where id='$ingredientRateId'";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	//Check before deleting whether the Raw Ingredient is used for any Cleaned Ingredient
	function findRawIngredient($ingredientId)
	{
		$qry = "select id from m_ingredient_rate where raw_ingredient='$ingredientId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	//Get Highest and Lowest Rate for an Ingredient
	function displayHighLowRate($ingredientId)
	{
		$qry = "select MAX(rate) as highest_price, MIN(rate) as lowest_price from ing_purchaseorder_entry where ingredient_id='$ingredientId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#check for ingredient in Recipe Entry Table
	function checkRecipeEntry($selIngredient)
	{
		$qry = "select id, recipe_id, ingredient_id, quantity, fixed_qty_chk from m_recipemaster_entry where ingredient_id='$selIngredient'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Returns Ingredients for a particular Recipe
	function getRecipeIngredient($recipeId)
	{
		$qry = "select id, recipe_id, ingredient_id, quantity, fixed_qty_chk from m_recipemaster_entry where recipe_id='$recipeId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Returns latest rate for the ingredient
	function getLatestIngredientRate($recipeIngredientId)
	{
		$qry = "select id, last_price from m_ingredient_rate where ingredient_id='$recipeIngredientId' order by id desc limit 1";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Returns Recipe Records
	function getRecipeRecord($recipeId)
	{
		$qry = "select product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, pouch_per_btch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch from m_recipemaster where id='$recipeId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Updating Recipe Master Entry Table while editing ingredients rate
	function updateRecipeIngredients($ingRate,$ingredientRsperBatch,$ingredientRsperPouch,$ingRateId,$recipeIngredientId,$recipeId)
	{
		$qry = "update m_recipemaster_entry set rs_per_kg='$ingRate', rate_per_kg='$ingredientRsperBatch', rate_per_pouch='$ingredientRsperPouch', ingredient_rate_id='$ingRateId' where ingredient_id='$recipeIngredientId' and recipe_id='$recipeId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}
	
	#return Rs per Pouch for an ingredient
	function getIngredientRsperPouch($recipeIngredientId,$recipeId)
	{
		$qry = "select rate_per_pouch from m_recipemaster_entry where ingredient_id='$recipeIngredientId' and recipe_id='$recipeId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Update Percent Cost per pouch for an ingredient in Recipe Master Entry table
	function updateIngredientCostperPouch($percentCostperPouch,$recipeIngredientId,$recipeId)
	{
		$qry = "update m_recipemaster_entry set percent_cost_per_pouch='$percentCostperPouch' where ingredient_id='$recipeIngredientId' and recipe_id='$recipeId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}
	
	#Update Recipe table
	function updateRecipeRecord($fishRsperBatch,$gravyRsperBatch,$productRsperBatch,$fishRsperKgperBatch,$gravyRsperKgperBatch,$productRsperKgperBatch,$fishRsPerPouch,$gravyRsPerPouch,$productRsPerPouch,$recipeId)
	{
		$qry = "update m_recipemaster set fish_rate_per_btch='$fishRsperBatch', gravy_rate_per_btch='$gravyRsperBatch', product_rate_per_btch='$productRsperBatch', product_rate_per_kg_per_btch='$productRsperKgperBatch', fish_rate_per_kg_per_btch='$fishRsperKgperBatch', gravy_rate_per_kg_per_btch='$gravyRsperKgperBatch', product_rate_per_pouch='$productRsPerPouch', fish_rate_per_pouch='$fishRsPerPouch', gravy_rate_per_pouch='$gravyRsPerPouch' where id='$recipeId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//echo $qry;
		return $result;	
	}
}
?>