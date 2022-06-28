<?php
class UnitMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Unit Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function UnitMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addUnit($unit,$received_type)
	{
		$qry	= "insert into m_unitmaster (unit,received_type) values('".$unit."','".$received_type."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all unit	
	function fetchAllRecords()
	{
		$qry	= "select id, unit,received_type,active from m_unitmaster order by unit asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all unit (paging)	
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, unit,received_type,active from m_unitmaster order by unit asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	# Get unit based on id 
	function find($unitId)
	{
		$qry	=	"select id, unit,received_type from m_unitmaster where id=$unitId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update a unit	
	function updateUnit($unit,$received_type,$unitId)
	{
		$qry	= " update m_unitmaster set unit= '".$unit."',received_type='$received_type' where id='$unitId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
		
	# Delete a Unit	
	function deleteUnit($unitId)
	{
		$qry	= " delete from m_unitmaster where id=$unitId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	#filter records based on Grade or count -->Used in Process code
	function filterRecords($available)
	{
		$qry	= "select id, unit,received_type from m_unitmaster where (received_type='$available' or received_type='B') and active=1";
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}
	function updateUnitconfirm($unitId)
{
$qry	= "update m_unitmaster set active='1' where id=$unitId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function updateUnitReleaseconfirm($unitId){
	$qry	= "update m_unitmaster set active='0' where id=$unitId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function fetchAllRecordsunitActive()
	{
		$qry	= "select id,unit,active from m_unitmaster where active=1 order by unit asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

}
?>