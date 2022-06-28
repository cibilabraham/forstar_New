<?php
class CarriageMode
{
	/****************************************************************
	This class deals with all the operations relating to Carriage Mode Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function CarriageMode(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Chek Entry Exist
	function chkRecExist($carriageMode, $carriageModeId)
	{
		$qry = "select id from m_carriage_mode where name='$carriageMode'";
		if ($carriageModeId) $qry .= " and id!=$carriageModeId";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Add 
	function addCarriageMode($carriageMode, $userId)
	{
		$qry = "insert into m_carriage_mode (name, created, createdby) values('$carriageMode', NOW(), '$userId')";

		//echo "<br/>$qry";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{		
		$whr = "";

		$orderBy = " a.name asc ";

		$limit	 = " $offset,$limit ";

		$qry = " select a.id, a.name, a.default_mode,a.active from m_carriage_mode a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$whr = "";
		$orderBy = " a.name asc ";

		$qry = " select a.id, a.name, a.default_mode,a.active from m_carriage_mode a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivecarriagemode()
	{
		$whr = "active=1";
		$orderBy = " a.name asc ";

		$qry = " select a.id, a.name, a.default_mode,a.active from m_carriage_mode a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Get a Record based on id
	function find($carriageModeId)
	{
		$qry = "select id, name from m_carriage_mode where id=$carriageModeId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update  a  Record
	function updateCarriageMode($carriageModeId, $carriageMode)
	{
		$qry = "update m_carriage_mode set name ='$carriageMode' where id=$carriageModeId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Selected State Rec
	function deleteCarriageModeRec($carriageModeId)
	{
		$qry 	= " delete from m_carriage_mode where id=$carriageModeId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update default value for the selected rec
	function updateDefaultChk($billingCompanyId)
	{
		$qry = "update m_carriage_mode set default_mode='Y' where id='$billingCompanyId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}
	# Update  All Rec
	function updateAllDefaultChk()
	{
		$qry = "update m_carriage_mode set default_mode='N'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

function updateCarriageModeconfirm($carriageModeId)
	{
	$qry	= "update m_carriage_mode set active='1' where id=$carriageModeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateCarriageModeReleaseconfirm($carriageModeId)
	{
		$qry	= "update m_carriage_mode set active='0' where id=$carriageModeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}


	
}
?>