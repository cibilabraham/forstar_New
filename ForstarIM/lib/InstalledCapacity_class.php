<?php
class InstalledCapacity
{  
	/****************************************************************
	This class deals with all the operations relating to Installed Capacity
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function InstalledCapacity(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addInstalledCapacity($machinery, $description, $operationType, $capacity, $unitId, $perVal, $monitor, $monitoringParameter, $userId)
	{
		$qry	= "insert into m_installed_capacity (name, description, operation_type_id, capacity, unit_id, per_val, monitor, monitoring_parameter_id, created, created_by) values ('$machinery', '$description', '$operationType', '$capacity', '$unitId', '$perVal', '$monitor', '$monitoringParameter', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function insertMonitoringParam($lastId,$headName,$monitoringParamId,$smpStart,$smpStop,$monitoringInterval,$seqFlag,$userId)
	{
		$qry	= "insert into m_set_monitoring_param (installed_capacity_id, head_name, monitoring_parameter_id, start, stop, monitoring_interval, description,created, created_by) values ('$lastId', '$headName', '$monitoringParamId', '$smpStart', '$smpStop', '$monitoringInterval', '$seqFlag', NOW(), '$userId')";
		//echo $qry; die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	# $qry	= "select a.id, a.name, a.unit_id, b.name as unitName from m_installed_capacity a join m_stock_unit b on b.id=a.unit_id  order by a.name asc ";
	function fetchAllRecords()
	{
		//$qry	= "select a.id, a.name, a.unit_id, b.name as unitName from m_installed_capacity a join m_stock_unit b on b.id=a.unit_id  order by a.name asc ";
		$qry	= "select mic.id, mic.name, mic.description,mot.name,mic.capacity,msu.name,mic.per_val, mic.monitor, mic.unit_id,mic.monitoring_parameter_id, msu.name as unitName,mic.active from m_installed_capacity mic join m_operation_type mot on mic.operation_type_id=mot.id join m_stock_unit msu on msu.id=mic.unit_id  order by mic.name asc";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select mic.id, mic.name, mic.description,mot.name,mic.capacity,msu.name,mic.per_val, mic.monitor, mic.unit_id,  mic.monitoring_parameter_id, msu.name as unitName,mic.active from m_installed_capacity mic join m_operation_type mot on mic.operation_type_id=mot.id join m_stock_unit msu on msu.id=mic.unit_id  order by mic.name asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($installedCapacityId)
	{
		$qry	= "select id, name, description, operation_type_id, capacity, unit_id, per_val, monitor, monitoring_parameter_id from m_installed_capacity where id=$installedCapacityId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateInstalledCapacity($installedCapacityId, $machinery, $description, $operationType, $capacity, $unitId, $perVal, $monitor, $monitoringParameter)
	{		
		$qry	= " update m_installed_capacity set name='$machinery', description='$description', operation_type_id='$operationType', capacity='$capacity', unit_id='$unitId', per_val='$perVal', monitor='$monitor', monitoring_parameter_id='$monitoringParameter' where id=$installedCapacityId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	
	
	function updateMonitoringparam($headName,$monitoringParamId,$smpStart,$smpStop,$monitoringInterval,$seqFlag,$mParamId)
	{		
		$qry	= " update m_set_monitoring_param set head_name='$headName', monitoring_parameter_id='$monitoringParamId', start='$smpStart', stop='$smpStop', monitoring_interval='$monitoringInterval', seq_flag='$seqFlag' where id=$mParamId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}		




	# Delete 
	function deleteTypeOfOperation($installedCapacityId)
	{
		$qry	= " delete from m_installed_capacity where id=$installedCapacityId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function getTypeOfOperation($installedCapacityId)
	{
		$rec = $this->find($installedCapacityId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function getMonitoringParam($installedCapacity)
	{
		$qry	= "select id,head_name,monitoring_parameter_id,start,stop,monitoring_interval,seq_flag,seq_mparam_id from m_set_monitoring_param where installed_capacity_id='$installedCapacity' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function delMonitoringParamRec($paramId)
	{
		$qry	= " delete from m_set_monitoring_param where id=$paramId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteMonitoringParam($installedCapacityId)
	{
		$qry	= " delete from m_set_monitoring_param where installed_capacity_id=$installedCapacityId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function updateconfirmInstalledCapacity($id)
	{
		$qry="update m_installed_capacity set active=1 where id=$id";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updaterlconfirmInstalledCapacity($id)
	{
		$qry="update m_installed_capacity set active=0 where id=$id";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
}

?>