<?php
class EmployeeMaster
{
	/****************************************************************
	This class deals with all the operations relating to Employee Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function EmployeeMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Employee Master
	function addEmployeeMaster($name, $designation, $department,$address,$telephone, $userId)
	{
		$qry	=	"insert into m_employee_master (name, designation, address,telephone_no,department, created_on, created_by) values('".$name."', '$designation', '".$address."','".$telephone."','".$department."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	=	"select id, name, designation, department,address,telephone_no,active FROM m_employee_master  order by name limit $offset,$limit";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Employee Master 
	function fetchAllRecords()
	{
		$qry	= "select id, name, designation, department,address,telephone_no,active FROM m_employee_master  order by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsActiveEmployee()
	{
		$qry	= "select id, name, designation, department,address,telephone_no,active FROM m_employee_master where active='1'  order by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivedept()
	{
		$qry	= "select id, name, description, incharge,active from m_department where active=1 order by name";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Employee Master based on id 
	function find($employeeId)
	{
		$qry	= "select id, name, designation, department,address,telephone_no from m_employee_master where id=$employeeId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Employee Master 
	function deleteEmployeeMaster($employeeMasterId)
	{
		$qry	= " delete from m_employee_master where id=$employeeMasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Employee Master
	function updateEmployeeMaster($employeeMasterId, $name, $designation, $department,$address,$telephone)
	{
		$qry	= " update m_employee_master set name='$name', designation='$designation', department='$department', address='$address', telephone_no='$telephone' where id=$employeeMasterId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateEmployeeMasterObjconfirm($employeeMasterId){
		$qry	= "update m_employee_master set active='1' where id=$employeeMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateEmployeeMasterReleaseconfirm($employeeMasterId){
	$qry	= "update m_employee_master set active='0' where id=$employeeMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	function fetchDesignation($designation)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		  $qry	=	"select id, designation FROM m_designation WHERE id=$designation";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}
	
	function fetchDepartment($department)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		  $qry	=	"select id, name FROM m_department WHERE id=$department";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}


	# -----------------------------------------------------
	# Checking Supplier group Id is in use (Procurement order);
	# -----------------------------------------------------
	function employeeRecInUse($employeeId)
	{		
		$qry = " select id from (
				select id from weighment_data_sheet where ( purchase_supervisor='$employeeId' OR receiving_supervisor='$employeeId' )
				UNION
				select id from t_rmreceiptgatepass where verified='$employeeId'
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	} 
}

?>