<?php
class RegistrationType
{
	/****************************************************************
	This class deals with all the operations relating to Registration Type
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RegistrationType(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Registration Type
	function addRegistrationType($registrationType, $displayCode, $descr, $userId)
	{
		$qry	=	"insert into m_registration_type (registration_type, display_code, description, created_on, created_by) values('".$registrationType."', '$displayCode', '".$descr."', Now(), '$userId')";

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
		 $qry	=	"select id, registration_type, display_code, description,active FROM m_registration_type order by registration_type limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Registration Type 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, registration_type, display_code, description,active FROM m_registration_type order by registration_type";
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

	# Get Registration Type based on id 
	function find($registrationTypeId)
	{
		$qry	= "select id, registration_type, display_code, description  from m_registration_type where id=$registrationTypeId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Registration Type 
	function deleteRegistrationType($registrationTypeId)
	{
		$qry	= " delete from m_registration_type where id=$registrationTypeId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Registration Type
	function updateRegistrationType($RegistrationTypeId, $registrationType, $displayCode, $descr)
	{
		$qry	= " update m_registration_type set registration_type='$registrationType', display_code='$displayCode', description='$descr' where id=$RegistrationTypeId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateregistrationTypeconfirm($registrationTypeId){
		$qry	= "update m_registration_type set active='1' where id=$registrationTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateRegistrationTypeReleaseconfirm($registrationTypeId){
	$qry	= "update m_registration_type set active='0' where id=$registrationTypeId";
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