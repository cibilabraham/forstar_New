<?php
class FreezingStage
{  
	/****************************************************************
	This class deals with all the operations relating to Freezing Stage
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function FreezingStage(&$databaseConnect)
   	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addFreezingStage($stage, $description, $yield)
	{
		$qry	= "insert into m_freezingstage (rm_stage, descr, yield) values('".$stage."','".$description."', '$yield')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, rm_stage, descr, yield,active,((select count(a1.id) from t_purchaseorder_rm_entry a1 where a1.freezingstage_id=a.id)+(select count(a2.id) from t_dailyfrozenpacking_entry a2 where a2.freezing_stage_id=a.id)) as tot from m_freezingstage a order by rm_stage asc";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRecordsActivefreezingstage()
	{
		$qry	= "select id, rm_stage, descr, yield,active from m_freezingstage where active=1 order by rm_stage asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records(PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, rm_stage, descr, yield,active,((select count(a1.id) from t_purchaseorder_rm_entry a1 where a1.freezingstage_id=a.id)+(select count(a2.id) from t_dailyfrozenpacking_entry a2 where a2.freezing_stage_id=a.id)) as tot from m_freezingstage a order by rm_stage asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($freezingStageId)
	{
		$qry	= "select id, rm_stage, descr, yield from m_freezingstage where id=$freezingStageId";
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update
	function updateFreezingStage($freezingStageId, $stage, $description, $yield)
	{
		$qry	= " update m_freezingstage set rm_stage='$stage', descr='$description', yield='$yield' where id=$freezingStageId";

		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete
	function deleteFreezingStage($freezingStageId)
	{
		$qry	= " delete from m_freezingstage where id=$freezingStageId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function findFreezingStageCode($freezingStageId)
	{
		$rec = $this->find($freezingStageId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}


		function updateFreezingStageconfirm($freezingStageId)
	{
	$qry	= "update m_freezingstage set active='1' where id=$freezingStageId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateFreezingStageReleaseconfirm($freezingStageId)
	{
		$qry	= "update m_freezingstage set active='0' where id=$freezingStageId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>