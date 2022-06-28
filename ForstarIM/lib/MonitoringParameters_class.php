<?php
class MonitoringParameters
{  
	/****************************************************************
	This class deals with all the operations relating to Monitoring Parameters
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function MonitoringParameters(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addMonitoringParameter($parameterName, $unitId, $userId)
	{
		$qry	= "insert into m_monitoring_parameters (name, unit_id, created, created_by) values ('$parameterName', '$unitId', NOW(), $userId)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select a.id, a.name, a.unit_id, b.name as unitName,a.active,(select count(a1.id) from m_installed_capacity a1 where a1.monitoring_parameter_id=a.id) as tot from m_monitoring_parameters a left join m_stock_unit b on b.id=a.unit_id  order by a.name asc ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select a.id, a.name, a.unit_id, b.name as unitName,a.active,(select count(a1.id) from m_installed_capacity a1 where a1.monitoring_parameter_id=a.id) as tot from m_monitoring_parameters a left join m_stock_unit b on b.id=a.unit_id  order by a.name asc limit $offset, $limit";
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($monitoringParameterId)
	{
		$qry	= "select id, name, unit_id from m_monitoring_parameters where id=$monitoringParameterId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateMonitoringParameter($monitoringParameterId, $parameterName, $unitId)
	{
		$qry	= " update m_monitoring_parameters set name='$parameterName', unit_id='$unitId' where id=$monitoringParameterId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	
	
	# Delete 
	function deleteTypeOfOperation($monitoringParameterId)
	{
		$qry	= " delete from m_monitoring_parameters where id=$monitoringParameterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function getTypeOfOperation($monitoringParameterId)
	{
		$rec = $this->find($monitoringParameterId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}


	function updateMonitoringParametersconfirm($monitoringParameterId)
	{
	$qry	= "update m_monitoring_parameters set active='1' where id=$monitoringParameterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateMonitoringParametersReleaseconfirm($monitoringParameterId)
	{
		$qry	= "update m_monitoring_parameters set active='0' where id=$monitoringParameterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>