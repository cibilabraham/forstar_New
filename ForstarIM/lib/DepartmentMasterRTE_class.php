<?php
class DepartmentMaster
{  
	/****************************************************************
	This class deals with all the operations relating to fish master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DepartmentMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addDepartment($name,$description,$type,$userId)
	{
		$qry	= "insert into m_rte_department (name,description,	type,createdby,createdon) values('".$name."','".$description."','".$type."','".$userId."',Now())";
		//echo $qry;
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);

		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all fishs 
	function fetchAllRecords()
	{
		
		//$qry	= "select mf.id, name, code,category_id,mf.active from m_rte_department mf join m_rte_departmentcategory  mc on mf.category_id=mc.id order by name";	
		$qry	= "select id,name,description,type,active  from m_rte_department order by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	function fetchAllRecordsDepartmentactive()
	{
		
			$qry	= "select id,name,description,type,active  from m_rte_department where active='1' order by name";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all fishs (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,name,description,type,active  from m_rte_department order by name limit $offset, $limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateDepartmentconfirm($departmentId)
	{
		$qry	= "update m_rte_department set active='1' where id=$departmentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateDepartmentReleaseconfirm($departmentId)
	{
		$qry	= "update m_rte_department set active='0' where id=$departmentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	
	# Get fish based on id 
	function find($departmentId)
	{
		$qry	= "select id,name,description,type  from m_rte_department where id=$departmentId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	
	# Delete a deleteDepartment 
	function deleteDepartment($departmentId)
	{
		$qry	= " delete from m_rte_department where id=$departmentId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}

	# Delete Department
	function updateDepartment($departmentId,$name,$description,$type)
	{
		$qry	= " update m_rte_department set name='$name',description='$description', type='$type' where id=$departmentId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# -----------------------------------------------------
	# Checking Fish Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function staffRecInUse($departmentId)
	{		
		$qry = "select id from m_staff_master where department='$departmentId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Get All Fish Recs
	function getFishRecs()
	{
		$fishArr = array();
		//$fishRecs = $this->fetchAllRecords();
		$fishRecs=$this->fetchAllRecordsFishactive();
		if (sizeof($fishRecs)>0) {
			foreach ($fishRecs as $fr) {
				$departmentId = $fr[0];
				$fName  = $fr[1];
				$fishArr[$departmentId] = $fName;
			}	
		}
		return $fishArr;
	}
	
	# Get all fish source 
	function fetchAllSourceRecords()
	{
		$qry	= "select id,name from m_rte_department_source";
		return $this->databaseConnect->getRecords($qry);
	}
	
	#Get Department Type
	function getDepartmentType($department)
	{
		$qry = "select type from m_rte_department where id='$department'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
}