<?php
class StaffRoleMaster
{  
	/****************************************************************
	This class deals with all the operations relating to fish master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function StaffRoleMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addStaffRole($name,$description,$userId)
	{
		$qry	= "insert into m_role_rte (name,description,createdby,createdon) values('".$name."','".$description."','".$userId."',Now())";
		//echo $qry;
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);

		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all fishs 
	function fetchAllRecords()
	{
		
		//$qry	= "select mf.id, name, code,category_id,mf.active from m_role_rte mf join m_role_rtecategory  mc on mf.category_id=mc.id order by name";	
		$qry	= "select id,name,description,active  from m_role_rte order by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	function fetchAllRecordsRoleactive()
	{
		
			$qry	= "select id,name,description,active  from m_role_rte where active='1' order by name";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all fishs (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,name,description,active  from m_role_rte order by name limit $offset, $limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateRoleconfirm($roleId)
	{
		$qry	= "update m_role_rte set active='1' where id=$roleId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateRoleReleaseconfirm($roleId)
	{
		$qry	= "update m_role_rte set active='0' where id=$roleId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	
	# Get fish based on id 
	function find($roleId)
	{
		$qry	= "select id,name,description from m_role_rte where id=$roleId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	
	# Delete a deleteDepartment 
	function deleteStaffRole($roleId)
	{
		$qry	= " delete from m_role_rte where id=$roleId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}

	# Delete Department
	function updateStaffRole($roleId,$name,$description)
	{
		$qry	= " update m_role_rte set name='$name',description='$description' where id=$roleId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# -----------------------------------------------------
	# Checking Fish Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function staffRecInUse($roleId)
	{		
		$qry = " select id from m_product_manage where id='$roleId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	
	# Get all fish source 
	function fetchAllSourceRecords()
	{
		$qry	= "select id,name from m_role_rte_source";
		return $this->databaseConnect->getRecords($qry);
	}
}