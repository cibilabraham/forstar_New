<?php
class FreezerCapacity
{  
	/****************************************************************
	This class deals with all the operations relating to Freezer Capacity
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function FreezerCapacity(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addFreezerCapacity($freezerName, $capacity, $freezingTime, $freezerDescr)
	{
		$qry	= "insert into m_freezercapacity (freezer_name, capacity, freezing_time, description) values('$freezerName','$capacity', '$freezingTime', '$freezerDescr')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, freezer_name, capacity, freezing_time, description,active from m_freezercapacity order by freezer_name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, freezer_name, capacity, freezing_time, description,active from m_freezercapacity order by freezer_name asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($freezerId)
	{
		$qry	= "select id, freezer_name, capacity, freezing_time, description from m_freezercapacity where id=$freezerId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateFreezerCapacity($freezerId, $freezerName, $capacity, $freezingTime, $freezerDescr)
	{
		$qry	= " update m_freezercapacity set freezer_name='$freezerName', capacity='$capacity', freezing_time='$freezingTime', description='$freezerDescr' where id=$freezerId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
		
	# Delete 
	function deleteFreezerCapacity($freezerId)
	{
		$qry	= " delete from m_freezercapacity where id=$freezerId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	#Find glazze by using Id
	function findFreezer($freezerId)
	{
		$rec = $this->find($freezerId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	#Find Freezer Time
	function getFreezerTime($freezerId)
	{
		$rec = $this->find($freezerId);
		return sizeof($rec) > 0 ? $rec[3] : "";
	}

	#Find Freezer Name, Capacity, Freezing Time
	function getFreezerDetails($freezerId)
	{
		$rec = $this->find($freezerId);
		return sizeof($rec) > 0 ? array($rec[1],$rec[2],$rec[3]) : "";
	}

	function updateFreezerCapacityconfirm($freezerId)
{
	$qry	= "update m_freezercapacity set active='1' where id=$freezerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function updateFreezerCapacityReleaseconfirm($freezerId){
		$qry	= "update m_freezercapacity set active='0' where id=$freezerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}



}
?>