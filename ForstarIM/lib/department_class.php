<?php
class Department
{
	/****************************************************************
	This class deals with all the operations relating to Department
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Department(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Department
	function addDepartment($name, $incharge, $descr, $userId)
	{
		$qry	=	"insert into m_department (name, incharge, description, created, createdby) values('".$name."', '$incharge', '".$descr."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	=	"select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Departments 
	function fetchAllRecords()
	{
		$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
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

	# Get Department based on id 
	function find($departmentId)
	{
		$qry	= "select id, name, description, incharge  from m_department where id=$departmentId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Department 
	function deleteDepartment($departmentId)
	{
		$qry	= " delete from m_department where id=$departmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Department
	function updateDepartment($departmentId, $name, $incharge, $descr)
	{
		$qry	= " update m_department set name='$name', incharge='$incharge', description='$descr' where id=$departmentId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateDepartmentconfirm($departmentId){
		$qry	= "update m_department set active='1' where id=$departmentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateDepartmentReleaseconfirm($departmentId){
	$qry	= "update m_department set active='0' where id=$departmentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
}

?>