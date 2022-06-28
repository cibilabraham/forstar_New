<?php
class RePacking
{  
	/****************************************************************
	This class deals with all the operations relating to Re-Packing
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function RePacking(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addRePacking($rePackingCode,$rePackingReason)
	{
		$qry	= "insert into m_repacking (code, reason) values('".$rePackingCode."','".$rePackingReason."')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	function addRePackingTypes($lastInsertedId,$packagingStructureId, $selRepackType)
	{	
		$qry	= "insert into m_repacking_entries (main_id, packagingstructure_id,repack_type) values('".$lastInsertedId."','".$packagingStructureId."', '".$selRepackType."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# paging recs
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select id, code, reason,active from m_repacking order by code asc limit $offset, $limit";		 
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, code, reason,active from m_repacking order by code asc";		 
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($repackingId)
	{
		$qry	= "select id, code, reason from m_repacking where id=$repackingId";
		return $this->databaseConnect->getRecord($qry);
	}

	function getPackagingStructure($rePackingId)
	{
	
		$qry = "select a.id, a.name, a.descr, b.id, b.packagingstructure_id, b.repack_type from m_packagingstructure a left join m_repacking_entries b on a.id=b.packagingstructure_id and b.main_id='$rePackingId' order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update
	function updateRePacking($rePackingId,$rePackingCode,$rePackingReason)
	{
		$qry	= " update m_repacking set code='$rePackingCode', reason='$rePackingReason' where id=$rePackingId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# Delete
	#RePacking Entries Rec 
	function deleteRePackingEntries($rePackingId)
	{
		$qry	= " delete from m_repacking_entries where main_id=$rePackingId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Delete Main Rec
	function deleteRePackingRec($rePackingId)
	{
		$qry	= " delete from m_repacking where id=$rePackingId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	#Find the Re-Packing Reason
	function findRePackingReason($reasonId)
	{
		$rec = $this->find($reasonId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}
	function updateRePackingconfirm($rePackingId)
{
$qry	= "update m_repacking set active='1' where id=$rePackingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function updateRePackingReleaseconfirm($rePackingId){
	$qry	= "update m_repacking set active='0' where id=$rePackingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}
}
?>