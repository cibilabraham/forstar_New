<?php
class testing
{
	/****************************************************************
	This class deals with all the operations relating to Vehicle Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function testing(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Vehicle Master
	//function addVehicleMaster($vehicle_number, $vehicle_type, $userId)
	function insertName($name)
	{		
		 $qry	=	"insert into `anishiyatest` (name) values('".$name."')";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function fetchAllPagingRecords($offset, $limit)
	{
		 $qry	=	"select id,name FROM anishiyatest";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	function find($id)
	{
		$qry	=	"select id, name from anishiyatest where id=$id";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	function deleteName($id)
	{
		$qry	= " DELETE FROM `anishiyatest` WHERE id=$id";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updatename($name, $id)
	{
		$qry	= " update anishiyatest set name='$name' where id= '$id'";
 	//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	function moreEntriesExist($id)
	{
		$qry = "select id from `anishiyatest` where id=$id";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

}

?>