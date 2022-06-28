<?php
class Designation
{
	/****************************************************************
	This class deals with all the operations relating to Registration Type
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Designation(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Designation
	function addDesignation($designation, $userId)
	{
		$qry	=	"insert into m_designation (designation, created_on, created_by) values('".$designation."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select id, designation,active FROM m_designation order by designation limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Designation 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, designation,active FROM m_designation order by designation";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveDesignation()
	{
		$qry	= "select id, designation,active from m_designation where active=1 order by designation";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get designation based on id 
	function find($designationId)
	{
		$qry	= "select id, designation  from m_designation where id=$designationId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Designation 
	function deleteDesignation($designationId)
	{
		$qry	= " delete from m_designation where id=$designationId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a Designation
	function updateDesignation($designationId, $designation)
	{
		$qry	= " update m_designation set designation='$designation' where id=$designationId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateDesignationconfirm($designationId){
		$qry	= "update m_designation set active='1' where id=$designationId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateDesignationReleaseconfirm($designationId){
	$qry	= "update m_designation set active='0' where id=$designationId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	function fetchAllRecordsunitActive()
	{
		$qry	= "select id,registration_type,active from m_registration_type where active=1 order by registration_type asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchRegistartionType($registrationTypecode)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select registration_type FROM m_registration_type WHERE id=$registrationTypecode";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
}

?>