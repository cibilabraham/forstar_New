<?php
class StockItemUnit
{
	/****************************************************************
	This class deals with all the operations relating to Stock Item Unit
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function StockItemUnit(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add 
	function addStockItemUnit($unitGroup, $unitName, $descr)
	{
		$qry	= "insert into m_stock_unit (unitgroup_id, name, description) values('$unitGroup','".$unitName."','".$descr."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit, $unitGroupFilterId)
	{		
		$whr = " a.unitgroup_id=b.id ";

		if ($unitGroupFilterId) $whr .= " and a.unitgroup_id='$unitGroupFilterId' ";

		$orderBy	= " a.name asc";		

		$limit = "$offset,$limit";

		$qry	=	"select  a.id, a.name, a.description, a.unitgroup_id, b.name  from m_stock_unit a, m_unit_group b ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;	
		if ($limit)	$qry .= " limit ".$limit;

		//echo "$qry";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records 
	function fetchAllRecords($unitGroupFilterId=null)
	{
		$whr = " a.unitgroup_id=b.id ";

		if ($unitGroupFilterId) $whr .= " and a.unitgroup_id='$unitGroupFilterId' ";

		$orderBy	= " a.name asc";

		$qry	=	"select  a.id, a.name, a.description, a.unitgroup_id, b.name  from m_stock_unit a, m_unit_group b ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;	

		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record based on id 
	function find($stockItemUnitId)
	{
		$qry	= "select id, name, description, unitgroup_id from m_stock_unit where id=$stockItemUnitId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateStockItemUnit($stockItemUnitId, $unitGroup, $unitName, $descr)
	{
		$qry	= " update m_stock_unit set name='$unitName', description='$descr', unitgroup_id='$unitGroup' where id=$stockItemUnitId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete a Record 
	function deleteStockItemUnit($stockItemUnitId)
	{
		$qry	= " delete from m_stock_unit where id=$stockItemUnitId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check whether the selected Unit is  link with any other screen
	function checkMoreEntriesExist($stockItemUnitId)
	{
		$qry = "select id from m_stock where unit='$stockItemUnitId'";
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
}
?>