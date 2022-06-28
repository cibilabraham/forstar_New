<?php
class DocumentationInstructions
{  
	/****************************************************************
	This class deals with all the operations relating to documentation instructions
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DocumentationInstructions(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addDocumentationInstructions($name, $required, $createdBy)
	{
		$qry	= "insert into m_doc_instructions_chk (name,required,created,created_by) values('".$name."','". $required."',NOW(),'".$createdBy."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from m_doc_instructions_chk where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";
	//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
		# Returns all documentation instructions (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,name,required,active from m_doc_instructions_chk order by name asc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all documentation instructions
	function fetchAllRecords()
	{
		
		$qry	= "select * from m_doc_instructions_chk order by name asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get documentation instructions based on id 
	function find($docinstructionId)
	{
		$qry	= "select id, name,required  from m_doc_instructions_chk where id=$docinstructionId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function updateDocInstructions($docInstructionId,$name, $required)
	{
		$qry	= "update m_doc_instructions_chk  set name='$name',required='$required' where id=$docInstructionId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	function updateconfirmDocumentationInstructions($docInstructionId)
	{
	$qry="update m_doc_instructions_chk set active=1 where id=$docInstructionId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;

	}
	
	function updaterlconfirmDocumentationInstructions($docInstructionId)
	{
	$qry="update m_doc_instructions_chk set active=0 where id=$docInstructionId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;
	}
	
	# Delete documentation instructions 
	function deleteDocInstructions($docInstructionId)
	{
		$qry	= " delete from  m_doc_instructions_chk where id=$docInstructionId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	
	# -----------------------------------------------------
	# Checking Documentation instructions Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function docInstructionRecInUse($docInstructionId)
	{		
		$qry = "select id from (
				select a.chk_list_id as id from t_purchaseorder_doc_chklist a where a.chk_list_id='$docInstructionId'
			
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Get documentation instructions based on id 
	function findAll()
	{
		$qry	= "select id, name,required  from m_doc_instructions_chk where active='1'";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	

}

