<?php
class OperationType
{  
	/****************************************************************
	This class deals with all the operations relating to Operation Type
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function OperationType(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

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
		$qry	= "select id, name,description,active,(select count(mic.id) from m_installed_capacity mic where mic.operation_type_id=mot.id) as tot from m_operation_type mot order by name asc";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, name,description,active,(select count(mic.id) from m_installed_capacity mic where mic.operation_type_id=mot.id) as tot from m_operation_type mot order by name asc limit $offset, $limit";
		//echo $qry;
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

	# Get Type of operation	
	function getTypeOfOperation($operationTypeId)
	{
		$rec = $this->find($operationTypeId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}


	function updateOperationTypeconfirm($operationTypeId)
	{
	$qry	= "update m_operation_type set active='1' where id=$operationTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateOperationTypeReleaseconfirm($operationTypeId)
	{
		$qry	= "update m_operation_type set active='0' where id=$operationTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>