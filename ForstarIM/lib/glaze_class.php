<?php
class Glaze
{  
	/****************************************************************
	This class deals with all the operations relating to Glaze
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Glaze(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addGlaze($glazePercent,$glazeDescr)
	{
		$qry	= "insert into m_glaze (glaze,descr) values('".$glazePercent."','".$glazeDescr."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		
		$qry	= "select id, glaze, descr,active from m_glaze order by glaze asc";	
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsGlazeActive()
	{
		
		$qry	= "select id, glaze, descr,active from m_glaze where active=1 order by glaze asc";	
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		
		$qry	= "select id, glaze, descr,active,(select COUNT(a.id) from m_frozenpacking a where a.glaze_id = mg.id) as tot from m_glaze mg order by glaze asc limit $offset, $limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($glazeId)
	{
		$qry	= "select id, glaze, descr from m_glaze where id=$glazeId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update
	function updateGlaze($glazeId,$glazePercent,$glazeDescr)
	{
		$qry	= " update m_glaze set glaze='$glazePercent', descr='$glazeDescr' where id=$glazeId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete 
	function deleteGlaze($glazeId)
	{
		$qry	= " delete from m_glaze where id=$glazeId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	#Find glazze by using Id
	function findGlazePercentage($glazeId)
	{
		$rec = $this->find($glazeId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function updateGlazeconfirm($glazeId)
	{
	$qry	= "update m_glaze set active='1' where id=$glazeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateGlazeReleaseconfirm($glazeId)
	{
		$qry	= "update m_glaze set active='0' where id=$glazeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>