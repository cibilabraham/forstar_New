<?php
class RMTestData
{  
	/****************************************************************
	This class deals with all the operations relating to RM Test Data 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RMTestData(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	
	function fetchAllRecordsRMLotId($unitId,$companyId)
	{	
		$qry="select id,concat(alpha_character,rm_lotid) from t_manage_rm_lotid where unit_id='$unitId' and company_id='$companyId' and status='0' ";
		
		//$qry	=	"select id, new_lot_Id from t_unittransfer where active='1' order by new_lot_Id asc";
		//$qry="select id,new_lot_Id from t_unittransfer where unit_Name='$unitId' and active='1' order by new_lot_Id asc ";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Filter Test Method
	function getTestMethod($rmTestNameId)
	{
		$qry="select test_method from m_rmtest_master where id='$rmTestNameId'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	# Filter lot name
	function getLotName($unitId,$companyId)
	{
		$qry="select id,concat(alpha_character,rm_lotid) from t_manage_rm_lotid where unit_id='$unitId' and company_id='$companyId' and status='0' ";
		
		//$qry="select id,new_lot_Id from t_unittransfer where unit_Name='$unitId' and active='1' ";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	
	
	
	# Returns all RM Test Data
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id,companyName, unit, lot,test_name,test_method,date_of_testing, result from t_rmtestdata where date_of_testing>='$fromDate' and date_of_testing<='$tillDate' order by date_of_testing desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all RM Test Data
	function fetchAllRecords()
	{
		$qry	= "select id,companyName, unit, lot,test_name,test_method,date_of_testing, result from t_rmtestdata order by date_of_testing desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id,companyName, unit, lot,test_name,test_method,date_of_testing, result from t_rmtestdata where date_of_testing>='$fromDate' and date_of_testing<='$tillDate' order by date_of_testings desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Add RM Test Data
	function addRmTestData($selCompanyName,$unit, $rmLotId, $rmTestName, $rmtestMethod, $dateOfTesting,$result, $userId)
	{
		$qry	= "insert into t_rmtestdata(companyName,unit, lot,test_name,test_method,date_of_testing,result, created_on, created_by) values('$selCompanyName','$unit', '$rmLotId','$rmTestName','$rmtestMethod','$dateOfTesting','$result', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	# Get lot based on id 
	function findLot($lotId)
	{
		$qry	=	"select id, lot_Id from t_rmreceiptgatepass where id=$lotId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function findLote($lotId)
	{
		 $qry="select id,concat(alpha_character,rm_lotid) from  t_manage_rm_lotid  where  id='$lotId'";
		//$qry	=	"select id,new_lot_Id from t_unittransfer where id=$lotId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function findCompany($companyId)
	{
		$qry	=	"select id,name from m_billing_company where id=$companyId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Get rm test data based on id 
	function find($rmTestDataId)
	{
		$qry	= "select * from t_rmtestdata where id=$rmTestDataId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function fetchAllRmTestItem($rmTestDataId)
	{
		$qry	= "select id,companyName, unit, lot, test_name, test_method, date_of_testing,result from t_rmtestdata where id='$rmTestDataId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Update  a  rm Test Data
	function updateStockIssuance($rmTestDataId,$companyName, $unit, $rmLotId,$rmTestName,$rmtestMethod,$dateOfTesting,$result)
	{
		 $qry	= " update t_rmtestdata set companyName='$companyName', unit='$unit', lot='$rmLotId', test_name='$rmTestName', 	test_method='$rmtestMethod', date_of_testing='$dateOfTesting',result='$result' where id=$rmTestDataId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete a RM Test Data
	function deleteRmTestData($rmTestDataId)
	{
		$qry	= " delete from t_rmtestdata where id=$rmTestDataId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
}