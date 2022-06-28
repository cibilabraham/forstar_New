<?php
require_once("flib/AFModel.php");
class operation_type_model extends AFModel
{
	protected $tableName = "m_operation_type";
	protected $pk = 'id';	// Primary key field
	
	#Add
	function addTypeOfOperation($operationTypeName, $operationTypeDescr, $userId)
	{
		$qry	= "insert into m_operation_type (name, description, created, created_by) values('".$operationTypeName."','".$operationTypeDescr."', NOW(), $userId)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, name, description from m_operation_type order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, name, description from m_operation_type order by name asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($operationTypeId)
	{
		$qry	= "select id, name, description from m_operation_type where id=$operationTypeId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateTypeOfOperation($operationTypeId, $operationTypeName, $operationTypeDescr)
	{
		$qry	= " update m_operation_type set name='$operationTypeName', description='$operationTypeDescr' where id=$operationTypeId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	
	
	# Delete 
	function deleteTypeOfOperation($operationTypeId)
	{
		$qry	= " delete from m_operation_type where id=$operationTypeId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function getTypeOfOperation($operationTypeId)
	{
		$rec = $this->find($operationTypeId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}
	

}
