<?php 
class SulabhaTest
 {
    var $databaseConnect;

	function SulabhaTest(&$databaseConnect)
	  {
	    $this->databaseConnect = &$databaseConnect; 
	  }
	  
	//Fetch All Employee Records
	function fetchAllRecords()
	{
		$qry = "SELECT id, name, designation, department, active FROM m_sulabha_test ORDER BY name";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	 
	//Fetch All Employee Details 
    function fetchAllemployee($offset,$limit)
	 {
	    $qry = "SELECT id, name, designation, department, active FROM m_sulabha_test ORDER BY name LIMIT $offset,$limit";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	 }
	
	function fetchEmployee()
	 {
	    $qry = "SELECT id, name, designation, department, active FROM m_sulabha_test ORDER BY name";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	 }
	 
	function getEditData($id)
	{
	   $qry = "SELECT id, name, designation, department FROM m_sulabha_test WHERE id=$id";
	   //echo $qry;
	   $editResult = $this->databaseConnect->getRecord($qry);
	   return $editResult;
	}
	
	function updateEmployeeDetail($updtId, $updtName, $updtDesig, $updtDept)
	{
	   $qry = "UPDATE m_sulabha_test SET name='$updtName', designation='$updtDesig', department='$updtDept' WHERE id='$updtId'";
       $updateResult = $this->databaseConnect->updateRecord($qry);
       return $updateResult;	   
	} 
	
	function addNewEmployee($addName, $addDesignation, $addDept)
	{
	   $qry = "INSERT INTO m_sulabha_test (name, designation, department, active) VALUES('$addName', '$addDesignation', '$addDept', 0)";
	   $addEmpDetails = $this->databaseConnect->insertRecord($qry);
	   return $addEmpDetails;
	}
	
	function employeeConfirm($empId)
	{
	   $qry = "UPDATE m_sulabha_test SET active='1' WHERE id=$empId";
	   $empConfirm = $this->databaseConnect->updateRecord($qry);
	   return $empConfirm;
	}
	
	function employeeReleaseConfirm($empId)
	{
	   $qry = "UPDATE m_sulabha_test SET active='0' WHERE id=$empId";
	   $empPending = $this->databaseConnect->updateRecord($qry);
	   return $empPending;
	}
	
	function employeeDelete($delId)
	{
		$qry = "DELETE FROM m_sulabha_test WHERE id=$delId";
		$empDel = $this->databaseConnect->delRecord($qry);
		return $empDel;
	}
	
 }