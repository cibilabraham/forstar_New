<?php
class EuCode
{  
	/****************************************************************
	This class deals with all the operations relating to EuCode
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function EuCode(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addEuCode($euCode, $euCodeDescr, $euCodeAddr)
	{
		$qry	= "insert into m_eucode (code, descr, address) values('".$euCode."','".$euCodeDescr."','".$euCodeAddr."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		
		$qry	= "select id, code, descr, address,active from m_eucode order by code asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsActiveEucode()
	{
		
		$qry	= "select id, code, descr, address,active from m_eucode where active=1 order by code asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		
		//$qry	= "select id, code, descr, address,me.active,((select COUNT(a.id) from t_purchaseorder_rm_entry a where a.eucode_id=me.id)+(select COUNT(a.id) from t_dailyfrozenpacking_entry a where a.eucode_id=me.id)+(select COUNT(a.id) from t_fznpakng_quick_entry a where a.eucode_id=me.id)) as tot from m_eucode me order by code asc limit $offset, $limit";	
		$qry	= "select id, code, descr, address,active from m_eucode order by code asc limit $offset, $limit";		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($euCodeId)
	{
		$qry	= "select id, code, descr, address from m_eucode where id=$euCodeId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateEuCode($euCodeId,$euCode,$euCodeDescr, $euCodeAddr)
	{
		$qry	= " update m_eucode set code='$euCode', descr='$euCodeDescr', address='$euCodeAddr' where id=$euCodeId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	
	
	# Delete 
	function deleteEuCode($euCodeId)
	{
		$qry	= " delete from m_eucode where id=$euCodeId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function findEUCode($euCodeId)
	{
		$rec = $this->find($euCodeId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function getEuCodeAddr($euCodeId)
	{
		$rec = $this->find($euCodeId);
		return sizeof($rec) > 0 ? $rec[3] : "";
	}

	# -----------------------------------------------------
	# Checking Eucode Id in (t_purchaseorder_rm_entry) 
	# -----------------------------------------------------
	/*
	union select a1.id as id from tble a1 where 			 
	*/
	function euCodeRecInUse($euCodeId)
	{	
		$qry = " select id from (
					select a.id as id from t_purchaseorder_rm_entry a where a.eucode_id='$euCodeId'			 
			) as X group by id ";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

function updateEuCodeReleaseconfirm($euCodeId)
	{
		$qry	=	" update m_eucode set active=0 where id=$euCodeId"; 
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateEuCodeconfirm($euCodeId)
	{
	$qry	=	" update m_eucode set active=1 where id=$euCodeId"; 
	//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

}
?>