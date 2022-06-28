<?php
class HarvestingEquipment
{
	/****************************************************************
	This class deals with all the operations relating to Registration Type
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function HarvestingEquipment(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Registration Type
	function addHarvestingEquipmentType($equipmentType, $equipmentDescription, $userId)
	{
		$qry	=	"insert into m_harvesting_equipment_type (type_name, description, created_on, created_by) values('".$equipmentType."', '".$equipmentDescription."', Now(), '$userId')";

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
		 $qry	=	"select id, type_name, description,active FROM m_harvesting_equipment_type order by type_name limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Registration Type 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, type_name, description,active FROM m_harvesting_equipment_type order by type_name";
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
	function find($equipmentTypeId)
	{
		$qry	= "select id, type_name, description  from m_harvesting_equipment_type where id=$equipmentTypeId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Registration Type 
	function deleteHarvestingEquipmentType($harvestingEquipmentTypeId)
	{
		$qry	= " delete from m_harvesting_equipment_type where id=$harvestingEquipmentTypeId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Registration Type
	function updateHarvestingEquipmentType($harvestingEquipmentTypeId, $equipmentType, $equipmentDescription)
	{
		$qry	= " update m_harvesting_equipment_type set type_name='$equipmentType', description='$equipmentDescription' where id=$harvestingEquipmentTypeId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateHarvestingEquipmentTypeconfirm($harvestingEquipmentTypeId){
		$qry	= "update m_harvesting_equipment_type set active='1' where id=$harvestingEquipmentTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateHarvestingEquipmentTypeReleaseconfirm($harvestingEquipmentTypeId){
	$qry	= "update m_harvesting_equipment_type set active='0' where id=$harvestingEquipmentTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	function fetchAllRecordsActiveequipmentType()
	{
		
		
		 $qry	= "select id,type_name,active from m_harvesting_equipment_type where active=1 order by type_name asc"; 

		
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
}

?>