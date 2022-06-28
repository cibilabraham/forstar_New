<?php
class CheckPointMaster
{
	/****************************************************************
	This class deals with all the operations relating to Check Point Master
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function CheckPointMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addCheckPoint($name, $description, $selSubModule)
	{
		$qry	= "insert into m_check_point (name, description) values('".$name."','".$description."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus)	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Processing Activity 
	function fetchAllRecords()
	{
		$qry	= "select id, name, description from m_check_point order by name asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Processing Activity (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, name, description from m_check_point order by name asc limit $offset, $limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Processing Activity based on id 
	function find($checkPointId)
	{
		$qry	=	"select id, name, description from m_check_point where id=$checkPointId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Processing Activity
	function deleteCheckPoint($checkPointId)
	{
		$qry	=	" delete from m_check_point where id=$checkPointId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 	 	$this->databaseConnect->rollback();		
		return $result;
	}

	# Update 
	function updateCheckPoint($checkPointId, $name, $description)
	{
		$qry	= " update m_check_point set name='$name', description='$description' where id=$checkPointId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 	 	$this->databaseConnect->rollback();		
		return $result;	
	}
	
	#Check whether the selected entries exist
	function moreEntriesExist($checkPointId)
	{
		$qry = " select id from stk_subcategory_chkpoint where check_point_id = '$checkPointId'";	
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

}
?>