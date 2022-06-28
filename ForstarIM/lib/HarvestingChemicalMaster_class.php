<?php
class HarvestingChemicalMaster
{
	/****************************************************************
	This class deals with all the operations relating to Chemical Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function HarvestingChemicalMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Chemical Master
	function addHarvestingChemicalMasters($chemicalName, $chemicalDescription, $userId)
	{
		$qry	=	"insert into m_harvesting_chemical_master (chemical_name, description, created_on, created_by) values('".$chemicalName."', '$chemicalDescription',  Now(), '$userId')";

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
		 $qry	=	"select id, chemical_name, description,active FROM m_harvesting_chemical_master order by chemical_name limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Registration Type 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, chemical_name, description,active FROM m_harvesting_chemical_master order by chemical_name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllChemicalRecordsActive()
	{
		$qry = $qry	= "select id,chemical_name,active from m_harvesting_chemical_master where active=1 order by chemical_name asc"; 
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Registration Type based on id 
	function find($harvestingChemicalId)
	{
		$qry	= "select id, chemical_name, description  from m_harvesting_chemical_master where id=$harvestingChemicalId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Harvesting Chemical Master 
	function deleteHarvestingChemicalMaster($harvestingChemicalId)
	{
		$qry	= " delete from m_harvesting_chemical_master where id=$harvestingChemicalId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Harvesting Chemical Master
	function updateHarvestingChemicalMaster($harvestingChemicalId, $chemicalName, $chemicalDescription)
	{
		$qry	= " update m_harvesting_chemical_master set chemical_name='$chemicalName', description='$chemicalDescription' where id=$harvestingChemicalId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateHarvestingChemicalMasterconfirm($harvestingChemicalId){
		$qry	= "update m_harvesting_chemical_master set active='1' where id=$harvestingChemicalId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateHarvestingChemicalMasterReleaseconfirm($harvestingChemicalId){
	$qry	= "update m_harvesting_chemical_master set active='0' where id=$harvestingChemicalId";
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