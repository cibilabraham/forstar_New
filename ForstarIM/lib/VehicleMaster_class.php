<?php
class VehicleMaster
{
	/****************************************************************
	This class deals with all the operations relating to Vehicle Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function VehicleMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Vehicle Master
	//function addVehicleMaster($vehicle_number, $vehicle_type, $userId)
	function addVehicleMaster($vehicle_number, $vehicle_type,$description,$userId)
	{		
		$qry	=	"insert into m_vehicle_master (vehicle_number,vehicle_type,description,created_on, created_by) values('".$vehicle_number."','".$vehicle_type."','".$description."', Now(), '$userId')";

		 //$qry	=	"insert into m_vehicle_master (vehicle_number, vehicle_type, created_on, created_by) values('".$vehicle_number."', '".$vehicle_type."', Now(), '$userId')";

		//echo $qry;
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
		
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	=	"select id, vehicle_number, vehicle_type,registration_number,description,active,allocated,procurement_number FROM m_vehicle_master order by vehicle_number limit $offset,$limit";
		
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		// $qry	=	"select id, vehicle_number, vehicle_type,active FROM m_vehicle_master order by vehicle_number limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	function getharvestingEquipment($vehicleMasterId)
	{		
		 $qry 	= "select id, harvesting_equipment,equipment_quantity from m_vehicle_harvesting_equipment where vehicle_master_id='$vehicleMasterId' order by harvesting_equipment asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getEquipmentName($equipment)
	{		
		$qry 	= "select name_of_equipment from m_harvesting_equipment_master where id='$equipment'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function checkDuplicate($number)
	{		
		$qry 	= "select id from m_vehicle_master where vehicle_number='$number'";
		//echo $qry;
		$srec	= $this->databaseConnect->getRecord($qry);
		return ( sizeof($srec)>0)?true:false;
	}
	function getharvestingChemical($vehicleMasterId)
	{		
		 $qry 	= "select id, harvesting_chemical,chemical_quantity from m_vehicle_harvesting_chemical where vehicle_master_id='$vehicleMasterId' order by harvesting_chemical asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getChemicalName($chemical)
	{		
		$qry 	= "select chemical_name from m_harvesting_chemical_master where id='$chemical'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	# Returns all Vehicle Master 
	function fetchAllRecords()
	{
		$qry	=	"select id,vehicle_number, vehicle_type,registration_number,description,active,allocated FROM m_vehicle_master order by vehicle_number";
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 //$qry	=	"select id,vehicle_number, vehicle_type,active FROM m_vehicle_master order by vehicle_number";
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
	# Delete Entry Rec
	function delHarvestingEquipmentRec($equipmentId)
	{
		$qry = " delete from m_vehicle_harvesting_equipment where id=$equipmentId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function delHarvestingChemicalRec($chemicalId)
	{
		$qry = " delete from m_vehicle_harvesting_chemical where id=$chemicalId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Get Vehicle Master based on id 
	function find($vehicleMasterId)
	{
		$qry	= "select id, vehicle_number, vehicle_type,registration_number,description from m_vehicle_master where id=$vehicleMasterId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	
	
	
	# Update Entry

	function updateEquipmentQuantity($equipmentId, $equipmentName,$equipmentQuantity)
	{
		 $qry = " update m_vehicle_harvesting_equipment set harvesting_equipment='$equipmentName',equipment_quantity='$equipmentQuantity' where id='$equipmentId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function updateChemicalQuantity($chemicalId, $chemicalName,$chemicalQuantity)
	{
		 $qry = " update m_vehicle_harvesting_chemical set harvesting_chemical='$chemicalName',chemical_quantity='$chemicalQuantity' where id='$chemicalId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	/*check vehicle exist in procurement*/
	function checkVehicleNumber($vehicleMasterId)
	{
	$qry 	= "select id from t_rmprocurmentorder where vehicle_number='$vehicleMasterId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	

	# Delete a Vehicle Master 
	function deleteVehicleMaster($vehicleMasterId)
	{
		$qry	= " delete from m_vehicle_master where id=$vehicleMasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function deleteEquimentRecs($vehicleMasterId)
	{
		$qry 	= " delete from m_vehicle_harvesting_equipment where vehicle_master_id=$vehicleMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function deleteChemicalRecs($vehicleMasterId)
	{
		$qry 	= " delete from m_vehicle_harvesting_chemical where vehicle_master_id=$vehicleMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Vehicle Master
	//function updateVehicleMaster($vehicleMasterId, $vehicleNumber, $vehicleType)
	function updateVehicleMaster($vehicleMasterId, $vehicleNumber, $vehicleType,$description)
	{ 
		$qry	= "update m_vehicle_master set  vehicle_number='$vehicleNumber', vehicle_type='$vehicleType',description='$description' where id=$vehicleMasterId";

		 //$qry	= " update m_vehicle_master set  vehicle_number='$vehicleNumber', vehicle_type='$vehicleType' where id=$vehicleMasterId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateVehicleMasterconfirm($vehicleMasterId){
		$qry	= "update m_vehicle_master set active='1' where id=$vehicleMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateVehicleMasterReleaseconfirm($vehicleMasterId){
	$qry	= "update m_vehicle_master set active='0' where id=$vehicleMasterId";
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
	
	function fetchAlldeclarVehicleType(){
	$qry = " select id,vehicle_type from m_vehicle_type WHERE active=1 order by vehicle_type asc";
	//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;


	}
	
	function addVehicleEquipment($vehicleMasterId,  $harvestingEquipment,$harvestingQty)
	{
		 $qry	=	"insert into m_vehicle_harvesting_equipment (vehicle_master_id, harvesting_equipment,equipment_quantity) values('".$vehicleMasterId."','".$harvestingEquipment."','".$harvestingQty."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function addVehicleChemical($vehicleMasterId,$HarvestingChemical,$harvestingQty)
	{
		 $qry	=	"insert into m_vehicle_harvesting_chemical (vehicle_master_id, harvesting_chemical,chemical_quantity) values('".$vehicleMasterId."','".$HarvestingChemical."','".$harvestingQty."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function fetchVehicleType($vehicleType)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		  $qry	=	"select id, vehicle_type FROM m_vehicle_type WHERE id=$vehicleType";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}
	function getschedulehistory($vehicle_id)
	{	
		$currentdate=date("Y-m-d");
		$qry	=	"SELECT b.procurement_number,a.schedule_date FROM t_rm_procurement_vehicle_details a left join t_rmprocurmentorder b on a.rmProcurmentOrderId=b.id WHERE a.vehicle_id=$vehicle_id  and a.schedule_date >= '$currentdate' group by b.procurement_number";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
}

?>