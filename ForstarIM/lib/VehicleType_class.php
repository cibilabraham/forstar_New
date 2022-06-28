<?php
class VehicleType
{
	/****************************************************************
	This class deals with all the operations relating to Vehicle Type
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function VehicleType(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Vehicle Type
	function addVehicleType($vehicleType, $descr, $userId)
	{
		$qry	=	"insert into m_vehicle_type (vehicle_type, description, created_on, created_by) values('".$vehicleType."', '$descr', Now(), '$userId')";

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
		 $qry	=	"select id, vehicle_type, description,active FROM m_vehicle_type order by vehicle_type limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Vehicle Type 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, vehicle_type, description, active FROM m_vehicle_type order by vehicle_type";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllActiveVehicleType(){
	$qry = " select id,vehicle_type from m_vehicle_type WHERE active=1 order by vehicle_type asc";
	//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;


	}

	
	# Get Vehicle Type based on id 
	function find($vehicleTypeId)
	{
		$qry	= "select id, vehicle_type, description from m_vehicle_type where id=$vehicleTypeId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Vehicle Type 
	function deleteVehicleType($vehicleTypeId)
	{
		$qry	= " delete from m_vehicle_type where id=$vehicleTypeId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Vehicle Type
	function updateVehicleType($vehicleTypeId, $vehicleType,$descr)
	{
		 $qry	= " update m_vehicle_type set vehicle_type='$vehicleType', description='$descr' where id=$vehicleTypeId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateVehicleTypeconfirm($vehicleTypeId){
		$qry	= "update m_vehicle_type set active='1' where id=$vehicleTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateVehicleTypeReleaseconfirm($vehicleTypeId){
	$qry	= "update m_vehicle_type set active='0' where id=$vehicleTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	function fetchAllRecordsVehicleActive()
	{
		$qry	= "select id,name,active from m_stock_unit where active=1 order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getVehicleType($vehicleTypeId)
	{
		$qry	= "select vehicle_type from m_vehicle_type WHERE id=$vehicleTypeId";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
}

?>