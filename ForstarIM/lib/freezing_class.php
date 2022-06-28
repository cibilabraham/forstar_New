<?php
class Freezing
{  
	/****************************************************************
	This class deals with all the operations relating to Freezing
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Freezing(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addFreezing($name,$description,$glazeCalc)
	{	
		$qry	= "insert into m_freezing (code, descr, glaze_operator) values('".$name."','".$description."', '$glazeCalc')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, code, descr, glaze_operator,active from m_freezing order by code asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivefreezing()
	{
		$qry	= "select id, code, descr, glaze_operator,active from m_freezing where active=1 order by code asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Records(PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, code, descr, glaze_operator,active,(select COUNT(a.id) from m_frozenpacking a where a.freezing_id = mf.id) as tot from m_freezing mf order by code asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($freezingId)
	{
		$qry	= "select id, code, descr, glaze_operator from m_freezing where id=$freezingId";
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update
	function updateFreezing($freezingId,$name,$description,$glazeCalc)
	{
		$qry	= " update m_freezing set code='$name', descr='$description', glaze_operator = '$glazeCalc' where id=$freezingId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	# Delete
	function deleteFreezing($freezingId)
	{
		$qry	= " delete from m_freezing where id=$freezingId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function findFreezingCode($freezingId)
	{
		$rec = $this->find($freezingId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}


	
	function updateFreezingconfirm($freezingId)
	{
	$qry	= "update m_freezing set active='1' where id=$freezingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateFreezingReleaseconfirm($freezingId)
	{
		$qry	= "update m_freezing set active='0' where id=$freezingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>