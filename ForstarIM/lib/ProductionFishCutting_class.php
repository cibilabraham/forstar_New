<?php
class ProductionFishCutting
{
	/****************************************************************
	This class deals with all the operations relating to Production Fish Cutting
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductionFishCutting(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
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


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selRateList)
	{
		$qry = "select a.id, b.name, b.code, a.cost, a.ingredient_id,a.active from m_prodn_fish_cutting a, m_ingredient b where a.ingredient_id=b.id and a.rate_list_id='$selRateList' order by b.name asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{		
		$qry = " select a.id, b.name, b.code, a.cost, a.ingredient_id,a.active from m_prodn_fish_cutting a, m_ingredient b where a.ingredient_id=b.id and a.rate_list_id='$selRateList' order by b.name asc ";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records (array) using in Combo MX
	function fetchAllFishCuttingRecs($selRateList)
	{
		//$qry = "select id, name, code, cost from m_prodn_fish_cutting where rate_list_id='$selRateList' order by code asc";
		$qry = " select a.id, b.name, b.code, a.cost from m_prodn_fish_cutting a, m_ingredient b where a.ingredient_id=b.id and a.rate_list_id='$selRateList' order by b.name asc ";
		//echo "<br>$qry";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($fishCuttingRecId)
	{
		$qry = "select id, ing_category_id, ingredient_id, cost, rate_list_id from m_prodn_fish_cutting where id=$fishCuttingRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateFishCuttingRec($fishCuttingRecId, $ingMainCategory, $selFish, $costPerKg, $fcRateListId)
	{
		$qry = "update m_prodn_fish_cutting set ing_category_id='$ingMainCategory', ingredient_id='$selFish', cost='$costPerKg', rate_list_id='$fcRateListId' where id=$fishCuttingRecId ";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deleteFishCuttingRec($fishCuttingRecId)
	{
		$qry	=	" delete from m_prodn_fish_cutting where id=$fishCuttingRecId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Find the Fish Cost
	function getFishCuttingCost($fishCuttingRecId, $fcRateListId)
	{
		//$qry = "select id, name, code, cost, rate_list_id from m_prodn_fish_cutting where id='$fishCuttingRecId' and rate_list_id='$fcRateListId'";		
		$qry = "select id, cost from m_prodn_fish_cutting where id='$fishCuttingRecId' and rate_list_id='$fcRateListId'";
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);		
		return (sizeof($rec)>0)?$rec[1]:0;
	}

	function chkRecExist($selFish, $fcRateListId, $cRecId)
	{
		$appQry = "";
		if ($cRecId!="") $appQry = " and id!=$cRecId";

		$qry = "select id from m_prodn_fish_cutting where ingredient_id='$selFish' and rate_list_id='$fcRateListId' $appQry";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	

	function updateFishCuttingconfirm($fishCuttingRecId)
	{
		$qry	= " update m_prodn_fish_cutting set active='1' where id=$fishCuttingRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateFishCuttingReleaseconfirm($fishCuttingRecId)
	{
		$qry	= " update m_prodn_fish_cutting set active='0' where id=$fishCuttingRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>