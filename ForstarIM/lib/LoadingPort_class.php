<?php
class LoadingPort
{  
	/****************************************************************
	This class deals with all the operations relating to loading port
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function LoadingPort(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addPortOfLoading($name,$createdBy)
	{
		$qry	= "insert into m_loading_port(name,created_by,created) values('".$name."','".$createdBy."',NOW())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
		# Returns all port of loading (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,name,active from m_loading_port order by name asc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all port of loading
	function fetchAllRecords()
	{
		
		$qry	= "select * from m_loading_port order by name asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	# Get port of loading based on id 
	function find($loadingPortId)
	{
		$qry	= "select id, name from  m_loading_port where id=$loadingPortId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	
	
	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from m_loading_port where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	
	function updateLoadingPort($loadingPortId,$name)
	{
		$qry	= "update m_loading_port set name='$name' where id=$loadingPortId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	function updateconfirmLoadingPort($loadingPortId)
	{
	$qry="update m_loading_port set active=1 where id=$loadingPortId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;

	}
	
	function updaterlconfirmLoadingPort($loadingPortId)
	{
	$qry="update m_loading_port set active=0 where id=$loadingPortId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;
	}
	
	# Delete port of loading
	function deleteLoadingPort($loadingPortId)
	{
		$qry	= " delete from  m_loading_port where id=$loadingPortId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	

	# get all data
	function findAll()
	{
		$qry = "select * from m_loading_port where active='1'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return 	$result;
	}
	# -----------------------------------------------------
	# Checking loading port Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function loadingPortRecInUse($loadingPortId)
	{	
		$qry = "select id from t_invoice_main where loading_port='$loadingPortId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}
	

}

