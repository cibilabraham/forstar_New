<?php
class DriverMaster
{
	/****************************************************************
	This class deals with all the operations relating to Driver Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DriverMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Driver Master
	function addDriverMaster($name, $permanentAddress, $presentAddress,$telephoneNo,$mobileNo,$drivingLicenceNo,$licenceExpiryDate,$vehicleType, $userId)
	{
		$qry	=	"insert into m_driver_master (name_of_person, permanent_address,present_address,telephone_no,mobile_no,driving_licence_no,licence_expiry_date, created_on, created_by) values('".$name."', '".$permanentAddress."','".$presentAddress."','".$telephoneNo."','".$mobileNo."','".$drivingLicenceNo."','".$licenceExpiryDate."', Now(), '$userId')";

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
		 $qry	=	"select id, name_of_person, permanent_address,present_address,telephone_no,mobile_no,driving_licence_no,licence_expiry_date,active,allocated,procurement_number FROM m_driver_master order by name_of_person limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	function getVehicleType($driverMasterId)
	{		
		 $qry 	= "select id, vehicle_type from m_driver_vehicletype where driver_master_id='$driverMasterId' order by vehicle_type asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getVehicleTypeName($type)
	{		
		$qry 	= "select vehicle_type from m_vehicle_type where id='$type'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	# Returns all Driver Master 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id,name_of_person, permanent_address,present_address,telephone_no,mobile_no,driving_licence_no,licence_expiry_date,active FROM m_driver_master order by name_of_person";
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
	function delVehicleTypeRec($vehicleTypeId)
	{
		$qry = " delete from m_driver_vehicletype where id=$vehicleTypeId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Get Driver Master based on id 
	function find($driverMasterId)
	{
		$qry	= "select id,name_of_person, permanent_address,present_address,telephone_no,mobile_no,driving_licence_no,licence_expiry_date from m_driver_master where id=$driverMasterId";
		return $this->databaseConnect->getRecord($qry);
	}
	# Update Entry
	function updateVehicleType($vehicleTypeId, $vehicleType)
	{
		 $qry = " update m_driver_vehicletype set vehicle_type='$vehicleType' where id='$vehicleTypeId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	/*check Driver exist in procurement*/
	function checkDriverName($drivermasterId)
	{
	$qry 	= "select id from t_rmprocurmentorder where driver_name='$drivermasterId'";
	$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	
	}

	# Delete a Driver Master 
	function deleteDriverMaster($drivermasterId)
	{
		$qry	= " delete from m_driver_master where id=$drivermasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function deleteVehicletypeRecs($drivermasterId)
	{
		$qry 	= " delete from m_driver_vehicletype where driver_master_id=$drivermasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Driver Master
	function updateDriverMaster($driverMasterId, $name, $permanentAddress, $presentAddress,$telephoneNo,$mobileNo,$drivingLicenceNo,$licenceExpiryDate)
	{
		 $qry	= " update m_driver_master set  name_of_person='$name', permanent_address='$permanentAddress', present_address='$presentAddress', telephone_no='$telephoneNo', mobile_no='$mobileNo', driving_licence_no='$drivingLicenceNo', licence_expiry_date='$licenceExpiryDate' where id=$driverMasterId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateDrivermasterconfirm($drivermasterId){
		$qry	= "update m_driver_master set active='1' where id=$drivermasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateDriverMasterReleaseconfirm($drivermasterId){
	$qry	= "update m_driver_master set active='0' where id=$drivermasterId";
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
	
	function addVehicleType($driverId, $vehicleType)
	{
		 $qry	=	"insert into m_driver_vehicletype (vehicle_type, driver_master_id) values('".$vehicleType."','".$driverId."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	function getschedulehistory($driver_id)
	{
		$currentdate=date("Y-m-d");
		$qry	=	"SELECT b.procurement_number,a.schedule_date FROM t_rm_procurement_vehicle_details a left join t_rmprocurmentorder b on a.rmProcurmentOrderId=b.id WHERE a.driver_id=$driver_id and a.schedule_date >= '$currentdate' group by b.procurement_number";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
}

?>