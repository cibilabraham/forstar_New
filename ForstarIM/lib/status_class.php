<?php
class Status
{  
	/****************************************************************
	This class deals with all the operations relating to Status
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Status(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addStatus($status,$description)
	{
		$qry	= "insert into m_status (status,descr) values('".$status."','".$description."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, status, descr,active from m_status order by status";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsActiveStatus()
	{
		$qry	= "select id, status, descr,active from m_status where active=1 order by status";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, status, descr,active from m_status order by status limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($statusId)
	{
		$qry	= "select id, status, descr from m_status where id=$statusId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateStatus($statusId,$status,$description)
	{
		$qry	= " update m_status set status='$status', descr='$description' where id=$statusId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
		
	# Delete
	function deleteStatus($statusId)
	{
		$qry	= " delete from m_status where id=$statusId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	#Find Status by using Id
	function findStatus($statusId)
	{
		$rec = $this->find($statusId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function updateStatusModeconfirm($statusId)
	{
	$qry	= "update m_status set active='1' where id=$statusId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateStatusModeReleaseconfirm($statusId)
	{
		$qry	= "update m_status set active='0' where id=$statusId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>