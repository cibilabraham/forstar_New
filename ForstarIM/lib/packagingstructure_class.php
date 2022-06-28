<?php
class PackagingStructure
{
	/****************************************************************
	This class deals with all the operations relating to Packaging Structure
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PackagingStructure(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addPackagingStructure($structureName, $description)
	{
		$qry = "insert into m_packagingstructure (name, descr) values('".$structureName."','".$description."')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);

		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, name, descr,active,(select count(a1.id) from m_repacking_entries a1 where a1.packagingstructure_id=a.id) as tot from m_packagingstructure a order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function fetchAllRecordsPackingActive()
	{
		$qry	= "select id, name, descr,active,(m_repacking_entries b on a.id=b.packagingstructure_id) as tot from m_packagingstructure where active=1 order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry = "select id, name, descr,active,(select count(a1.id) from m_repacking_entries a1 where a1.packagingstructure_id=a.id) as tot from m_packagingstructure a order by name asc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($packagingStructureId)
	{
		$qry = "select id, name, descr from m_packagingstructure where id=$packagingStructureId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updatePackagingStructure($packagingStructureId, $structureName, $description)
	{
		$qry = " update m_packagingstructure set name='$structureName', descr='$description' where id=$packagingStructureId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		return $result;	
	}
	
	
	# Delete 
	function deletePackagingStructure($packagingStructureId)
	{
		$qry = " delete from m_packagingstructure where id=$packagingStructureId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}

	function findPackagingStructure($packagingStructureId)
	{
		$rec = $this->find($packagingStructureId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}


function updatePackagingStructureconfirm($rePackingId)
{
$qry	= "update  m_packagingstructure set active='1' where id=$rePackingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function updatePackagingStructureReleaseconfirm($rePackingId){
	$qry	= "update  m_packagingstructure set active='0' where id=$rePackingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}







}
?>