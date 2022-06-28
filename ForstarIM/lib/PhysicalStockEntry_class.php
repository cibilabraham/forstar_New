<?php
class PhysicalStockEntry
{  
	/****************************************************************
	This class deals with all the operations relating to Physical Stock Entry
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function PhysicalStockEntry(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert A Rec
	# Add to Main Table
	function addPhysicalStock($selDate, $selStkType, $userId)
	{		
		$qry = "insert into m_physical_stock (date, stk_type, created, createdby) values ('$selDate', '$selStkType', NOW(), '$userId')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	# Add to entry table
	function addPhysicalStockEntries($physicalStkEntryId, $stockId, $physicalStkQty, $stkQty, $diffStkQty)
	{		
		$qry = "insert into m_physical_stock_entry (main_id, stock_id, physical_stk_qty, stk_qty, diff_stk_qty) values ('$physicalStkEntryId', '$stockId', '$physicalStkQty', '$stkQty', '$diffStkQty')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit, $selFilterStkType)
	{
		if ($selFilterStkType!="") $whr = " a.stk_type = '".$selFilterStkType."'";			
		
		$orderBy 	= " a.date asc ";
		$limit 		= " $offset,$limit";

		$qry = " select a.id, a.date, a.stk_type,a.active from m_physical_stock a ";
		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords($selFilterStkType)
	{
		if ($selFilterStkType!="") $whr = " a.stk_type = '".$selFilterStkType."'";			
	
		$orderBy 	= " a.date asc ";

		$qry = " select a.id, a.date, a.stk_type,a.active from m_physical_stock a ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on Id
	function find($physicalStockRecId)
	{
		$qry = "select id, date, stk_type from m_physical_stock where id=$physicalStockRecId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getPhysicalStkRec($physicalStockRecId, $stockId)
	{
		$qry = " select id, physical_stk_qty, stk_qty, diff_stk_qty from m_physical_stock_entry where main_id='$physicalStockRecId' and stock_id='$stockId' ";
		
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[2], $rec[1], $rec[3]);
	}

	# Update
	function updatePhysicalStock($physicalStockRecId, $selDate, $selStkType)
	{
		$qry = "update m_physical_stock set date='$selDate', stk_type='$selStkType' where id='$physicalStockRecId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function delPhysicalStockEntries($physicalStockRecId)
	{
		$qry	= " delete from m_physical_stock_entry where main_id=$physicalStockRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Main Rec
	function deletePhysicalStock($physicalStockRecId)
	{
		$qry	= " delete from m_physical_stock where id=$physicalStockRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check Rec Exist
	function chkRecExist($selDate, $selStkType, $physicalStkId)
	{
		$uptdQry = "";
		if ($physicalStkId) $uptdQry = " and id!=$physicalStkId";
		else $uptdQry	= "";
		$qry = " select id from m_physical_stock where date='$selDate' and stk_type='$selStkType' $uptdQry";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Ingredient Records
	function getIngredientRecords()
	{
		$qry = "select a.id, a.code, a.name, a.actual_quantity from (m_ingredient a, ing_category b) left join ing_main_category c on a.main_category_id=c.id where b.id=a.category_id order by b.name asc, a.name asc";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Product Records
	function getProductRecords()
	{
		$qry = " select id, code, name, actual_qty from m_product_manage order by name asc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Semi-finished Product
	function getSemiFinishedProductRecs()
	{
		$qry = " select a.id, a.code, a.name, a.actual_qty from (m_sf_product a, ing_category b) left join ing_main_category c on a.category_id=c.id where b.id = a.subcategory_id order by a.name asc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updatephysicalStockconfirm($physicalStkId)
	{
		$qry	= "update m_physical_stock set active='1' where id=$physicalStkId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updatephysicalStockReleaseconfirm($physicalStkId)
	{
		$qry	= "update m_physical_stock set active='0' where id=$physicalStkId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	

}
?>