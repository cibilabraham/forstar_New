<?php
class ManageUsers
{  
	/****************************************************************
	This class deals with all the operations relating to Manage Users
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ManageUsers(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addUser($addUserName,$PWord,$selRole)
	{
		$qry	= "insert into user (username,password,role_id) values('".$addUserName."','".$PWord."',  '".$selRole."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

	# Returns all Users
	function fetchAllRecords()
	{
		$qry	= "select * from user";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get USER based on id 
	function find($userId)
	{
		$qry	= "select * from user where id=$userId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update a User	
	function updateUser($upUserName,$PWord,$selRole,$userEditId)
	{
		$qry	= " update user set username='$upUserName',password='$PWord',role_id='$selRole' where id=$userEditId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	
	
	# Delete a User	
	function deleteUser($userId)
	{
		$qry	=	" delete from user where id=$userId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getUsername($userId)
	{
		$qry = " select username from user where id='$userId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# -----------------------------------------------------
	# Checking User Id is in use (Manage Dashboard, Daily catch entry);
	# -----------------------------------------------------
	function userRecInUse($userId)
	{		
		$qry = " select id from (
				select a.id as id from assign_dashboard a where a.user_id='$userId'
			union
				select a1.id as id from t_dailycatch_main a1 where a1.createdby='$userId'			
			) as X group by id ";

		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}
}