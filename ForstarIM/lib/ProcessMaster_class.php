<?php 
class ProcessMaster
{	
	/****************************************************************
	This class deals with all the operations relating to Process Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProcessMaster(&$databaseConnect)
	{
		$this->databaseConnect = &$databaseConnect;
	}
	
	# Returns all Paging Records
	function fetchProcess($offset,$limit)
	{
		$qry = "SELECT * FROM(SELECT id, name, description, water, diesel, electricity, gas, active FROM m_process_master ORDER BY id desc) dum GROUP BY name ORDER BY name LIMIT $offset,$limit";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Process Master Details
	function fetchAllProcess()
	{
		$qry = "SELECT id, name, description, active FROM m_process_master ORDER BY name";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Add a new Process Master
	function addNewProcessDetails($name, $description, $water, $diesel, $electricity, $gas, $userId, $currentDate)
	{
		$qry = "INSERT INTO m_process_master (name, description, water, diesel, electricity, gas, created_by, created_on, active) VALUES ('$name', '$description', '$water', '$diesel', '$electricity', '$gas', '$userId', '$currentDate', 0)";
		$addResult = $this->databaseConnect->insertRecord($qry);
		if($addResult)
		$this->databaseConnect->commit();
		else 
		$this->databaseConnect->rollback();
		return $addResult;
	}
	
	#Get Process Master based on id 
	function getProcessDetails($edtId)
	{
		$qry = "SELECT id, name, description, water, diesel, electricity, gas, active FROM m_process_master WHERE id='$edtId'";
		$editResult = $this->databaseConnect->getRecord($qry);
		return $editResult;
	}
	
	#Get older Process Master Rate.
	function getOldProcessRate($id,$name)
	{
		$qry = "select id, name, water, diesel, electricity, gas, created_on FROM m_process_master where name='$name' and id!='$id' order by id desc limit 5";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Update a Process Master 
	function updateProcessDetails($updateId, $updateName, $updateDesc, $updateWater, $updateDiesel, $updateElectricity, $updateGas)
	{
		$qry = "UPDATE m_process_master SET name='$updateName', description='$updateDesc' water='$updateWater', diesel='$updateDiesel', electricity='$updateElectricity', gas='$updateGas' WHERE id='$updateId'";
		$updateResult = $this->databaseConnect->updateRecord($qry);
		if ($updateResult) 
		$this->databaseConnect->commit();
		else 
		$this->databaseConnect->rollback();	
		return $updateResult;
	}
	
	#Delete a Process Master
	function deleteProcessDetails($processDelId)
	{
		$qry = "DELETE FROM m_process_master WHERE id='$processDelId'";
		//echo $qry;
		$deleteResult = $this->databaseConnect->delRecord($qry);
		if ($deleteResult) 
		$this->databaseConnect->commit();
		else 
		$this->databaseConnect->rollback();
		return $deleteResult;
	}
	
	#Confirm a Process Master
	function processConfirmation($confirmId)
	{
		$qry = "UPDATE m_process_master SET active=1 WHERE id='$confirmId'";
		$confirmResult = $this->databaseConnect->updateRecord($qry);
		if ($confirmResult) 
		$this->databaseConnect->commit();
		else 
		$this->databaseConnect->rollback();	
		return $confirmResult;
	}
	
	#Release Confirmation of a Process Master
	function processReleaseConfirmation($releaseId)
	{
		$qry = "UPDATE m_process_master SET active=0 WHERE id='$releaseId'";
		$releaseResult = $this->databaseConnect->updateRecord($qry);
		if ($releaseResult) 
		$this->databaseConnect->commit();
		else 
		$this->databaseConnect->rollback();	
		return $releaseResult;
	}
	
	#Find Any Duplicate Entry Exist
	function findProcessDetails($name)
	{
		$qry = "select id from m_process_master where name='$name'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Get Water, Electricity, Diesel, Gas values of a Process Type
	function getProcessValues($processType)
	{
	    $qry = "select water, diesel, electricity, gas from m_process_master where id='$processType'";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	
}