<?php
class ExportMaster
{
	/****************************************************************
	This class deals with all the operations relating to Product Matrix
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ExportMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	#Fetch Export Master using limit
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, name, description, created_on, created_by, active from m_export_master order by name limit $offset, $limit";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result>0)?$result:"");
	}
	
	#Fetch All Export Master Records
	function fetchAllRecords()
	{
		$qry = "select id, name, description, created_on, created_by, active from m_export_master order by name";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result>0)?$result:"");
	}
	
	#Check for Duplicate Entry
	function checkExportExist($name)
	{
		$qry = "select id from m_export_master where name='$name'";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result>0)?$result:"");
	}
	
	#Add New Export Master
	function addExportMaster($name,$description,$userId,$currentDate)
	{
		$qry = "insert into m_export_master(name, description, created_on, created_by, active) values('$name','$description','$currentDate','$userId','0')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function updateExportConfirm($exportId)
	{
		$qry = "update m_export_master set active=1 where id='$exportId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	function updateExportReleaseConfirm($exportId)
	{
		$qry = "update m_export_master set active=0 where id='$exportId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	function find($exportId)
	{
		$qry = "select id, name, description from m_export_master where id='$exportId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result>0)?$result:"");
	}
	
	function deleteExportMaster($exportId)
	{
		$qry = "delete from m_export_master where id='$exportId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}
	
	#Get All Active Export Master
	function getAllActiveExports()
	{
		$qry = "select id, name, description from m_export_master where active=1";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result>0)?$result:"");
	}
}
?>