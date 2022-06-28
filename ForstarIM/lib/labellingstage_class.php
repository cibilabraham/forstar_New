<?php
class LabellingStage
{  
	/****************************************************************
	This class deals with all the operations relating to Labelling Stage
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function LabellingStage(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addLabellingStage($label,$description)
	{
	
		$qry	= "insert into m_labellingstage (label, descr) values('".$label."','".$description."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, label, descr,active from m_labellingstage order by label asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	="select id, label, descr,active from m_labellingstage order by label asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id
	function find($labellingStageId)
	{
		$qry	= "select id, label, descr from m_labellingstage where id=$labellingStageId";
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update
	function updateLabellingStage($labellingStageId,$label,$description)
	{
		$qry	= " update m_labellingstage set label='$label', descr='$description' where id=$labellingStageId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete 
	function deleteLabellingStage($labellingStageId)
	{
		$qry	= " delete from m_labellingstage where id=$labellingStageId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function findLabellingStage($labellingStageId)
	{
		$rec = $this->find($labellingStageId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function updateLabellingStageconfirm($statusId)
	{
	$qry	= "update m_labellingstage set active='1' where id=$statusId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateLabellingStageReleaseconfirm($statusId)
	{
		$qry	= "update m_labellingstage set active='0' where id=$statusId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>