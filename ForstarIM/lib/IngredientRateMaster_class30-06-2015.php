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
	function addIngredientRate($selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $ingLastPrice, $ingRateList, $userId)
	{
		$qry	= "insert into m_ingredient_rate (ingredient_id, rate_per_kg, yield, highest_price, lowest_price, last_price, rate_list_id, created, createdby) values('$selIngredient', '$ingRatePerKg', '$ingYield', '$ingHighPrice', '$ingLowPrice', '$ingLastPrice', '$ingRateList', Now(), '$userId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($selRateList, $offset, $limit, $categoryFilterId, $mainCategoryFilterId)
	{
		$whr = " c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList'";

		if ($categoryFilterId!="") $whr .= " and b.category_id=".$categoryFilterId;
		if ($mainCategoryFilterId!="") $whr .= " and c.main_category_id=".$mainCategoryFilterId;

		$orderBy 	= " c.name asc, b.name asc";
		$limit 		= " $offset,$limit";

		$qry = " select a.id, a.ingredient_id, a.rate_per_kg, a.yield, a.highest_price, a.lowest_price, a.last_price, a.rate_list_id, b.name,a.active from (m_ingredient_rate a, m_ingredient b, ing_category c) left join ing_main_category d on c.main_category_id=d.id ";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function ingredientRateRecFilter($selRateList, $categoryFilterId, $mainCategoryFilterId)
	{
		$whr = " c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList'";

		if ($categoryFilterId!="") $whr .= " and b.category_id=".$categoryFilterId;
		if ($mainCategoryFilterId!="") $whr .= " and c.main_category_id=".$mainCategoryFilterId;

		$orderBy 	= " c.name asc, b.name asc";
		
		$qry = "select a.id, a.ingredient_id, a.rate_per_kg, a.yield, a.highest_price, a.lowest_price, a.last_price, a.rate_list_id, b.name from (m_ingredient_rate a, m_ingredient b, ing_category c) left join ing_main_category d on c.main_category_id=d.id";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry = "select a.id, a.ingredient_id, a.rate_per_kg, a.yield, a.highest_price, a.lowest_price, a.last_price, a.rate_list_id, b.name from m_ingredient_rate a, m_ingredient b, ing_category c where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by c.name asc, b.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Ing Records (using in Ingredient PO)
	function fetchAllIngredientRecords($selRateList)
	{
		$qry = "select a.id, a.ingredient_id, a.rate_per_kg, a.yield, a.highest_price, a.lowest_price, a.last_price, a.rate_list_id, b.name from m_ingredient_rate a, m_ingredient b, ing_category c where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by c.name asc, b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($ingredientRateId)
	{
		$qry = "select id, ingredient_id, rate_per_kg, yield, highest_price, lowest_price, last_price, rate_list_id from m_ingredient_rate where id=$ingredientRateId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Record
	function deleteIngredientRate($ingredientRateId)
	{
		$qry	= " delete from m_ingredient_rate where id=$ingredientRateId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Record
	function updateIngredientRate($ingredientRateId, $selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $ingLastPrice, $ingRateList)
	{
		$qry	= " update m_ingredient_rate set ingredient_id='$selIngredient', rate_per_kg='$ingRatePerKg', yield='$ingYield', highest_price='$ingHighPrice', lowest_price='$ingLowPrice', last_price='$ingLastPrice', rate_list_id='$ingRateList' where id=$ingredientRateId ";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	#Checking same entry exist
	function checkEntryExist($selIngredient, $ingRateList)
	{
		$qry = "select id from m_ingredient_rate where ingredient_id='$selIngredient' and rate_list_id='$ingRateList'";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
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
	
	/**
	* Get Last Three Revised Rate of the selected Ingredient 	
	*/
	function getRevisedRateListRecs($selIngredient, $ingRateList)
	{
		$qry = " select a.rate_per_kg, b.start_date from m_ingredient_rate a, m_ingredient_ratelist b where a.rate_list_id=b.id and a.ingredient_id='$selIngredient' and a.rate_list_id!='$ingRateList' order by b.start_date desc limit 0,3";
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

	function updateRateListconfirm($ingredientId)
	{
	$qry	= "update m_ingredient_rate set active='1' where id=$ingredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateRateListReleaseconfirm($ingredientId)
	{
		$qry	= "update m_ingredient_rate set active='0' where id=$ingredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>