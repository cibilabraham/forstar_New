<?php
class HarvestingEquipmentMaster
{
	/****************************************************************
	This class deals with all the operations relating to Harvesting Equipment Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function HarvestingEquipmentMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Harvesting Equipmen tMaster
	function addHarvestingEquipmentMaster($equipmentName, $tarWt, $equipmentType, $userId)
	{
		$qry	=	"insert into m_harvesting_equipment_master (name_of_equipment, tare_wt, equipment_type, created_on, created_by) values('".$equipmentName."', '$tarWt', '".$equipmentType."', Now(), '$userId')";

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
		 $qry	=	"select id, name_of_equipment, tare_wt, equipment_type,active FROM m_harvesting_equipment_master order by name_of_equipment limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	function fetchEquipmentType($equipmentId)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		  $qry	=	"select id, type_name FROM m_harvesting_equipment_type WHERE id=$equipmentId";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all harvesting Equipment Master
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select  id, name_of_equipment, tare_wt, equipment_type,active FROM m_harvesting_equipment_master order by name_of_equipment";
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

	# Get Harvesting Equipment Master based on id 
	function find($harvestingEquipmentMasterId)
	{
		$qry	= "select id, name_of_equipment, tare_wt, equipment_type  from m_harvesting_equipment_master where id=$harvestingEquipmentMasterId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Harvesting Equipment Master 
	function deleteHarvestingEquipmentMaster($harvestingEquipmentMasterId)
	{
		$qry	= " delete from m_harvesting_equipment_master where id=$harvestingEquipmentMasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a Harvesting Equipmen Master
	function updateHarvestingEquipmentMaster($harvestingEquipmentMasterId, $equipmentName, $tarWt, $equipmentType)
	{
		 $qry	= " update m_harvesting_equipment_master set name_of_equipment='$equipmentName', tare_wt='$tarWt', equipment_type='$equipmentType' where id=$harvestingEquipmentMasterId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateHarvestingEquipmentMasterObjconfirm($harvestingEquipmentMasterId){
		$qry	= "update m_harvesting_equipment_master set active='1' where id=$harvestingEquipmentMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateHarvestingEquipmentMasterReleaseconfirm($harvestingEquipmentMasterId){
	$qry	= "update m_harvesting_equipment_master set active='0' where id=$harvestingEquipmentMasterId";
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
	function fetchAllRecordsActiveequipmentType()
	{
		
		
		$qry = $qry	= "select id,name_of_equipment,active from m_harvesting_equipment_master where active=1 order by name_of_equipment asc"; 

		
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
}

?>