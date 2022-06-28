<?php
class RmTestMaster
{
	/****************************************************************
	This class deals with all the operations relating to Registration Type
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RmTestMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Registration Type
	function addRMTestMaster($testName,$testMethod, $description, $userId)
	{
		 $qry	=	"insert into m_rmtest_master (test_name,test_method, description, created_on, created_by) values('".$testName."','".$testMethod."','".$description."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	function addTestMethod($testId, $testMethod)
	{
		 $qry	=	"insert into m_rmtest_method (test_method, test_name_id) values('".$testMethod."','".$testId."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	function getTestMethod($testNameId)
	{		
		$qry 	= "select id, test_method from m_rmtest_method where test_name_id='$testNameId' order by test_method asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select id, test_name,test_method, description,active FROM m_rmtest_master order by test_name limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Registration Type 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 //$qry	=	"select id, test_name, test_method, description,active FROM m_rmtest_master order by test_name";
		 $qry	=	"select id, test_name,test_method, description, active FROM m_rmtest_master order by test_name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsActive()
	{
		
		 $qry	=	"select id, test_name,test_method, description, active FROM m_rmtest_master where active='1' order by test_name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
		function deleteTestMethodRecs($rmTestMasterId)
	{
		$qry 	= " delete from m_rmtest_method where test_name_id=$rmTestMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	

	function fetchAllRecordsActivedept()
	{
		$qry	= "select id, name, description, incharge,active from m_department where active=1 order by name";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Registration Type based on id 
	function find($testId)
	{
		$qry	= "select id, test_name,test_method, description  from m_rmtest_master where id=$testId";
		return $this->databaseConnect->getRecord($qry);
	}
	

	

	# Delete a Registration Type 
	function deleteRmTestMaster($rmTestMasterId)
	{
		$qry	= " delete from m_rmtest_master where id=$rmTestMasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Registration Type
	function updateRmTestMaster($rmTestMasterId, $testName,$testmethod, $description)
	{
		 $qry	= " update m_rmtest_master set test_name='$testName',test_method='$testmethod', description='$description' where id=$rmTestMasterId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updatermTestMasterconfirm($rmTestMasterId){
		$qry	= "update m_rmtest_master set active='1' where id=$rmTestMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}
	
	# Update Entry
	function updateTestMethod($testMethodId, $testMethod)
	{
		$qry = " update m_rmtest_method set test_method='$testMethod' where id='$testMethodId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete Entry Rec
	function delTestMethodRec($testMethodId)
	{
		$qry = " delete from m_rmtest_method where id=$testMethodId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function updateRmTestMasterReleaseconfirm($rmTestMasterId){
	$qry	= "update m_rmtest_master set active='0' where id=$rmTestMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	function fetchAllRecordsunitActive()
	{
		$qry	= "select id,name,active from m_stock_unit where active=1 order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchPondSizeUnit($pondSizeUnitcode)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select name from m_stock_unit where id=$pondSizeUnitcode";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
}

?>