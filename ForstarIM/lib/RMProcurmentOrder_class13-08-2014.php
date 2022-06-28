<?php
class ProcurementOrder
{  
	/****************************************************************
	This class deals with all the operations relating to Procurement Order
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProcurementOrder(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	
	
	# Checking Unique Numbering
	function checkUnique($reqNumber, $hidReqNumber)
	{
		$addWhr = ( $hidReqNumber !="" ) ? " and gatePass!='$hidReqNumber' " : "";
		$sqry = "select id from t_rmProcurmentOrder where gatePass='$reqNumber' $addWhr ";
		//echo $sqry;
		$srec = $this->databaseConnect->getRecord($sqry);
		return ( sizeof($srec)>0)?true:false;
	}
	function fetchAllProcurementValue()
	{
	$qry	= "select id from t_rmprocurmentorder order by id desc  limit 1";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	#Add procurment
	//function addProcumentOrder($selCompanyName,$entryDate,$driverName,$vehicleNo,$procurmentNo,$userId)
	//function addProcumentOrder($selCompanyName,$selRMSupplierGroup,$entryDate,$driverName,$vehicleNo, $userId)
	function addProcumentOrder($selCompanyName,$entryDate,$procurmentNo,$schedule_date,$userId)
	{
		$qry	= "insert into t_rmprocurmentorder(company,procurement_number,date_of_entry,schedule_date,created_on, created_by) values('$selCompanyName','$procurmentNo','$entryDate','$schedule_date', Now(),'$userId')";
		// $qry	= "insert into t_rmprocurmentorder(company,driver_name,vehicle_number,procurement_number,date_of_entry, created_on, created_by) values('$selCompanyName','$driverName','$vehicleNo','$procurmentNo','$entryDate', Now(),'$userId')";
		
		//$qry	= "insert into t_rmprocurmentorder(company,suppler_group_name,driver_name,vehicle_number,date_of_entry, created_on, created_by) values('$selCompanyName','$selRMSupplierGroup','$driverName','$vehicleNo','$entryDate', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	#Add procurment entries
	function addProcurmentEquipment($lastId, $harvestingEquipment,$harvestingQty)
	//function addProcurmentEquipment($lastId, $equipmentNameId, $equipmentQty,$equipmentIssued,$balanceQty)
	{	
		$qry	= "insert into t_rmprocurmentequipment(rmProcurmentOrderId,equipment_id,required_quantity) values('$lastId','$harvestingEquipment', '$harvestingQty')";
		 //$qry	= "insert into t_rmprocurmentequipment(rmProcurmentOrderId,equipment_Name,max_equipment,equipment_issued,difference) values('$lastId','$equipmentNameId', '$equipmentQty','$equipmentIssued','$balanceQty')";
		//echo $qry;
		//die;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addProcurmentChemical($lastId, $HarvestingChemical,$harvestingQty)
	//function addProcurmentChemical($lastId, $chemicalNameId,$chemicalQty,$chemicalIssued)
	{
		$qry	= "insert into t_rmprocurmentchemical(rmProcurmentOrderId,chemical_id,required_quantity) values('$lastId','$HarvestingChemical', '$harvestingQty')";
		
		 //$qry	= "insert into t_rmprocurmentchemical(rmProcurmentOrderId,chemical_Name,chemical_required,chemical_issued) values('$lastId','$chemicalNameId', '$chemicalQty','$chemicalIssued')";
		//echo $qry;
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addProcurmentVehicleAndDriver($lastId,$vehicleNumber,$driverName,$schedule_date)
	{
		$qry	= "insert into  t_rm_procurement_vehicle_details(rmProcurmentOrderId,vehicle_id,driver_id,schedule_date) values('$lastId','$vehicleNumber','$driverName','$schedule_date')";
		
		// $qry	= "insert into t_rmprocurmentsupplier(rmProcurmentOrderId,supplier_name,supplier_address,pond_name,pond_address) values('$lastId','$supplierName', '$supplierAddress','$pondName','$pondAddress')";
		//echo $qry;
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	/*function addProcurmentDriver($lastId, $driverName,$schedule_date)
	{
	 $qry	= "insert into t_rm_procurement_driver(rmProcurmentOrderId,driver_id,schedule_date) values('$lastId','$driverName','$schedule_date')";
		
		// $qry	= "insert into t_rmprocurmentsupplier(rmProcurmentOrderId,supplier_name,supplier_address,pond_name,pond_address) values('$lastId','$supplierName', '$supplierAddress','$pondName','$pondAddress')";
		//echo $qry;
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addProcurmentVehicle($lastId,$vehicleNumber,$schedule_date)
	{
		$qry	= "insert into t_rm_procurement_vehicle(rmProcurmentOrderId,vehicle_id,schedule_date) values('$lastId','$vehicleNumber','$schedule_date')";
		
		// $qry	= "insert into t_rmprocurmentsupplier(rmProcurmentOrderId,supplier_name,supplier_address,pond_name,pond_address) values('$lastId','$supplierName', '$supplierAddress','$pondName','$pondAddress')";
		//echo $qry;
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}*/
	//function addProcurmentSupplier($lastId,$supplierName,$supplierAddress,$pondName,$pondAddress)
	function addProcurmentSupplier($lastId, $supplierName,$pondName)
	{
		 $qry	= "insert into t_rmprocurmentsupplier(rmProcurmentOrderId,supplier_id,pond_id) values('$lastId','$supplierName','$pondName')";
		
		// $qry	= "insert into t_rmprocurmentsupplier(rmProcurmentOrderId,supplier_name,supplier_address,pond_name,pond_address) values('$lastId','$supplierName', '$supplierAddress','$pondName','$pondAddress')";
		//echo $qry;
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function blockDriver($driverId)
	{
		$qry	= "update m_driver_master set active='0' where id='$driverId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function blockVehicle($vehicleId)
	{
		$qry	= "update m_vehicle_master set active='0' where id='$vehicleId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllCompanyName()
	{
		$qry	= "select id, name from m_billing_company where default_row='Y'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
		function fetchAllSupplierGroupName()
	{
		$qry	= "select id, supplier_group_name from m_supplier_group where active='1' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllPondName()
	{
		$qry	= "select id, pond_name from m_pond_master where active='1' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function fetchAllDriverNameEdit($vehicleNo)
	{
		$qry	= "select id, name_of_person from m_driver_master where active='1' and allocated='0' or id='$vehicleNo'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllDriver()
	{
		$qry	= "select id, name_of_person from m_driver_master  ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllVehicleNumber()
	{
		$qry	= "select id, vehicle_number from m_vehicle_master where active='1' and allocated='0' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllVehicleNumberEdit($vehicleId)
	{
		$qry	= "select id, vehicle_number from m_vehicle_master where active='1' and allocated='0' or id='$vehicleId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllVehicle()
	{
		$qry	= "select id, vehicle_number from m_vehicle_master  ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getfilterSupplierList($supplierGroupId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' group by a.supplier_name";
		//echo $qry;
		
		$result = array();
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Filter supplier List
	function filterSupplierList($supplierGroupId)
	{
		$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	
	# Filter supplier Address
	function getfilterPondList($supplierNames)
	{
		$qry="SELECT a.supplier,a.id, a.pond_name FROM m_pond_master a JOIN supplier b ON a.supplier = b.id WHERE a.supplier = '$supplierNames'";
		//echo $qry;
		
		$result = array();
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	/*function getSupplierData($supplierGroupNameId,$procurementId)
	{	
		$qry= "SELECT a.id, a.supplier_name, a.supplier_location, b.pond_name, b.rmProcurmentOrderId
		FROM m_supplier_group_details a
		INNER JOIN t_rmprocurmentsupplier b ON a.supplier_name = b.supplier_name
		WHERE a.supplier_group_name_id =  '$supplierGroupNameId'
		AND b.rmProcurmentOrderId =  '$procurementId'
		group by a.supplier_name,b.pond_name, b.rmProcurmentOrderId";
	
		/*$qry= "SELECT a.id, a.supplier_name, a.supplier_location, b.pond_name, b.rmProcurmentOrderId
		FROM m_supplier_group_details a
		INNER JOIN t_rmprocurmentsupplier b ON a.supplier_name = b.supplier_name
		WHERE a.supplier_group_name_id =  '$supplierGroupNameId'
		AND b.rmProcurmentOrderId =  '$procurementId'
		group by a.supplier_name,b.pond_name, b.rmProcurmentOrderId";*/
	
		/*echo	$qry= "SELECT a.id, a.supplier_name, a.supplier_location, b.pond_name, b.rmProcurmentOrderId FROM t_rmprocurmentsupplier b left JOIN m_supplier_group_details a ON a.supplier_name = b.supplier_name left join m_pond_master c on c.id=b.pond_name WHERE a.supplier_group_name_id = '$supplierGroupNameId' AND b.rmProcurmentOrderId = '$procurementId' ORDER BY a.supplier_name ASC";*/
		
		//$qry;
	/*	$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}*/
	# Filter supplier Address
	function filterSupplierAddressList($supplierNameId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select address from supplier where id='$supplierNameId'";
		
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		/*if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;*/
		return (sizeof($result)>0)?$result[0]:0;
	}
	function filterPondList($supplierNameId)
	{	$currentdate=date("Y-m-d");
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		 //$qry="select id,pond_name from m_pond_master where supplier='$supplierNameId' and 	registration_expiry_date >'$currentdate'";
		$qry="select id,pond_name from m_pond_master where supplier='$supplierNameId'";
		
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		//return (sizeof($result)>0)?$result[0]:0;
	}
	
	# Filter pond Address
	function filterPondAddressList($pondNameId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select address from m_pond_master where id='$pondNameId'";
		//echo $qry;
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		/*if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;*/
		return (sizeof($result)>0)?$result[0]:0;
	}
	function ProcurmentNumberFetch($procurementId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select driver_name,vehicle_number	 from t_rmprocurmentorder where id='$procurementId'";
		//echo $qry;
		
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
		//return (sizeof($result)>0)?$result[0]:0;
	}
	# Filter equipment List
	function filterEquipmentList($vehicleNumId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select a.id,a.harvesting_equipment,b.name_of_equipment from  m_vehicle_harvesting_equipment a join m_harvesting_equipment_master b on a.harvesting_equipment=b.id where vehicle_master_id='$vehicleNumId' order by harvesting_equipment asc"; 
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	
	# Filter cheical List
	function filterChemicalList($vehicleNumId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select a.id,a.harvesting_chemical,b.chemical_name from  m_vehicle_harvesting_chemical a join m_harvesting_chemical_master b on a.harvesting_chemical=b.id where vehicle_master_id='$vehicleNumId' order by harvesting_chemical asc"; 
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	# equipment quantity
	function filterEquipmentQty($equipmentNameId,$vehicleNumId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select equipment_quantity from m_vehicle_harvesting_equipment where harvesting_equipment='$equipmentNameId' and vehicle_master_id='$vehicleNumId'";
		//echo $qry;
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	# equipment quantity
	function filterChemicalQty($chemicalNameId,$vehicleNumId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select chemical_quantity from m_vehicle_harvesting_chemical where harvesting_chemical='$chemicalNameId' and vehicle_master_id='$vehicleNumId'";
		//echo $qry;
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	# Returns all procurement records
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{	
		$qryGenCount = "(select count(*) from t_rmprocurmentchemical where rmProcurmentOrderId=a.id and (issued_quantity != 0 || difference_quantity != 0)) as total_generated  ";
		$qry	= "select a.id,a.suppler_group_name,a.procurement_number,a.date_of_entry,a.active,a.generated,b.supplier_group_name,c.vehicle_number,d.name_of_person,$qryGenCount from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";
		
		//$qry	= "select a.*,b.supplier_group_name,c.vehicle_number,d.name_of_person from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function fetchAllDateRangeRecords($fromDate, $tillDate)
	{
		$qry	= "select a.*,b.supplier_group_name,c.vehicle_number,d.name_of_person from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getSupplierGroupDetails($supplierVal)
	{
		$qry	= "select id,supplier_group_name from m_supplier_group where id in(select supplier_group_name_id FROM m_supplier_group_details where supplier_name='$supplierVal' )";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function getSupplierDetails($procurementId)
	{
		$qry	= "select id,name,address from supplier where id in(select supplier_id FROM t_rmprocurmentsupplier WHERE rmProcurmentOrderId='$procurementId')";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getPondDetails($procurementId)
	{
		$qry	= "select id,pond_name,allotee_name,location from m_pond_master where id in(select pond_id FROM t_rmprocurmentsupplier WHERE rmProcurmentOrderId='$procurementId')";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get procurment entries based on procurement id 
	function find($orderId)
	{
		$qry	= "select id,company,procurement_number,date_of_entry,schedule_date from t_rmprocurmentorder   where id=$orderId";
		
		// $qry	= "select a.id,a.company,a.driver_name,a.vehicle_number,a.procurement_number,a.date_of_entry,b.supplier_group_name,c.vehicle_number from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id where a.id=$orderId";
		
		//$qry	=	"select * from t_rmprocurmentorder where id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function fetchAllProcurmentSupplier($procurmentId)
	{
	 $qry	= "select id,supplier_id,pond_id from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	#Fetch All Records based on  procurment equipment TABLE	
	function fetchAllProcurmentEquipment($procurmentId)
	{
		$qry	= "select id,equipment_id,required_quantity from t_rmprocurmentequipment where rmProcurmentOrderId='$procurmentId' ";
		 //$qry	= "select * from t_rmprocurmentequipment where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllProcurmentChemical($procurmentId)
	{	$qry	= "select id,chemical_id,required_quantity from t_rmprocurmentchemical where rmProcurmentOrderId='$procurmentId' ";
		// $qry	= "select * from t_rmprocurmentchemical where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	/*function fetchAllProcurmentDriver($procurmentId)
	{	
	 $qry	= "select a.id,a.driver_id,b.name_of_person from t_rm_procurement_driver a left join  m_driver_master b on b.id=a.driver_id where a.rmProcurmentOrderId='$procurmentId'  ";
		// $qry	= "select * from t_rmprocurmentchemical where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllProcurmentVehicle($procurmentId)
	{	$qry	= "select id,vehicle_id from  t_rm_procurement_vehicle where rmProcurmentOrderId='$procurmentId' ";
		// $qry	= "select * from t_rmprocurmentchemical where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}*/
	
	function fetchAllProcurmentVehicleAndDriver($procurmentId)
	{
	$qry	= "select id,vehicle_id,driver_id from  t_rm_procurement_vehicle_details where rmProcurmentOrderId='$procurmentId' ";
		// $qry	= "select * from t_rmprocurmentchemical where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Update  a  Procurment Order
	
	function updateProurmentOrder($procurementId, $selCompanyName,$entryDate,$schedule_date,$procurmentNo)
	{	
			$qry	= "update t_rmprocurmentorder set company='$selCompanyName',procurement_number='$procurmentNo',date_of_entry='$entryDate',schedule_date='$schedule_date' where id='$procurementId'";
			
			//echo $qry;
			$result	= $this->databaseConnect->updateRecord($qry);
			if ($result) {
				$this->databaseConnect->commit();
			} else {
				 $this->databaseConnect->rollback();
			}
			return $result;	
	}
	
	
	
	
	
	
	function updateProurmentOrder_old($procurementId, $selCompanyName,$driverName,$vehicleNo,$entryDate,$procurmentNo)
	//function updateProurmentOrder($procurementId, $selCompanyName,$selRMSupplierGroup,$driverName,$vehicleNo,$entryDate)
	{	$qry	= "update t_rmprocurmentorder set company='$selCompanyName',driver_name='$driverName',vehicle_number='$vehicleNo',procurement_number='$procurmentNo',date_of_entry='$entryDate' where id='$procurementId'";
			
		//$qry	= "update t_rmprocurmentorder set company='$selCompanyName', suppler_group_name='$selRMSupplierGroup',driver_name='$driverName',vehicle_number='$vehicleNo',date_of_entry='$entryDate' where id='$procurementId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	// function updateProurmentOrder($procurementId, $procurmentNo, $selCompanyName,$selRMSupplierGroup,$supplierName,$supplierAddress,$pondName,$pondAddress,$driverName,$vehicleNo,$entryDate)
	// {		
		// $qry	= "update t_rmprocurmentorder set gatePass='$procurmentNo', company='$selCompanyName', suppler_group_name='$selRMSupplierGroup',supplier_name='$supplierName',supplier_address='$supplierAddress',pond_name='$pondName',pond_address='$pondAddress',driver_name='$driverName',vehicle_number='$vehicleNo',date_of_entry='$entryDate' where id='$procurementId'";
		
		////echo $qry;
		// $result	= $this->databaseConnect->updateRecord($qry);
		// if ($result) {
			// $this->databaseConnect->commit();
		// } else {
			 // $this->databaseConnect->rollback();
		// }
		// return $result;	
	// }
	
	
	# Update Entry
	/*function updateProcurmentDriver($driverId, $driverName,$schedule_date)
		{	
		$qry = " update t_rm_procurement_driver set driver_id='$driverName' , schedule_date='$schedule_date' where id='$driverId'";
	
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Delete Entry Rec
	function delRMProcurmentDriverRec($driverId)
	{
		 $qry = " delete from t_rm_procurement_driver where id=$driverId";
		
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	# Update Entry
	function updateProcurmentVehicle($vehicleId, $vehicleNumber,$schedule_date)
		{	
		$qry = " update t_rm_procurement_vehicle set vehicle_id='$vehicleNumber' , schedule_date='$schedule_date' where id='$vehicleId'";
	
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Delete Entry Rec
	function delRMProcurmentVehicleRec($vehicle_id)
	{
		 $qry = " delete from t_rm_procurement_vehicle where id=$vehicle_id";
		
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	*/
	# Update Entry
	function updateProcurmentVehicleAndDriver($detail_id,$driverName, $vehicleNumber,$schedule_date)
		{	
		$qry = " update t_rm_procurement_vehicle_details set vehicle_id='$vehicleNumber' ,driver_id='$driverName', schedule_date='$schedule_date' where id='$detail_id'";
	
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Delete Entry Rec
	function delRMProcurmentVehicleAndDriverRec($detail_id)
	{
		 $qry = " delete from t_rm_procurement_vehicle_details where id=$detail_id";
		
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
	# Update Entry
	function updateProcurmentEquipment($equipmentId, $equipmentName,$equipmentQuantity)
	//function updateProcurmentEquipment($rmId,$equipmentName,$equipmentQty,$equipmentIssued,$balanceQty)
	{	
		$qry = " update t_rmprocurmentequipment set equipment_id='$equipmentName',required_quantity='$equipmentQuantity' where id='$equipmentId'";
		
		//$qry = " update t_rmprocurmentequipment set equipment_Name='$equipmentName',max_equipment='$equipmentQty',equipment_issued='$equipmentIssued',difference='$balanceQty' where id='$rmId'";
		//$qry = " update t_rmprocurmentequipment set equipment_Name='$equipmentName',max_equipment='$equipmentQty',equipment_issued='$equipmentIssued',difference='$balanceQty' where rmProcurmentOrderId='$rmId'";
		 //echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Delete Entry Rec
	function delRMProcurmentEquipmentRec($rmId)
	{
		 $qry = " delete from t_rmprocurmentequipment where id=$rmId";
		// echo $qry = " delete from t_rmprocurmentequipment where rmProcurmentOrderId=$rmId";
		// die;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updateProcurmentChemical($chemicalId, $chemicalName,$chemicalQuantity)
	//function updateProcurmentChemical($rmId,$chemicalNameId,$chemicalQty,$chemicalIssued)
	{	$qry = " update t_rmprocurmentchemical set chemical_id='$chemicalName',required_quantity='$chemicalQuantity' where id='$chemicalId'";
		
		//$qry = " update t_rmprocurmentchemical set chemical_Name='$chemicalNameId',chemical_required='$chemicalQty',chemical_issued='$chemicalIssued' where id='$rmId'";
		 //$qry = " update t_rmprocurmentchemical set chemical_Name='$chemicalNameId',chemical_required='$chemicalQty',chemical_issued='$chemicalIssued' where rmProcurmentOrderId='$rmId'";
		 //echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function delRMProcurmentChemicalRec($rmId)
	{
		 $qry = " delete from t_rmprocurmentchemical where id=$rmId";
		 //echo $qry = " delete from t_rmprocurmentchemical where rmProcurmentOrderId=$rmId";
		// die;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updateProcurmentSupplier($rmId,$supplierName,$pondName)
	//function updateProcurmentSupplier($rmId,$supplierName,$supplierAddress,$pondName,$pondAddress)
	{	$qry = " update t_rmprocurmentsupplier set supplier_id='$supplierName',pond_id='$pondName' where rmProcurmentOrderId='$rmId'";
		
		//$qry = " update t_rmprocurmentsupplier set supplier_name='$supplierName',supplier_address='$supplierAddress',pond_name='$pondName',pond_address='$pondAddress' where rmProcurmentOrderId='$rmId'";
		//$qry = " update t_rmprocurmentsupplier set supplier_name='$supplierName',supplier_address='$supplierAddress',pond_name='$pondName',pond_address='$pondAddress' where id='$rmId'";
		// echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function delRMProcurmentSupplierRec($rmId)
	{
		  $qry = " delete from t_rmprocurmentsupplier where id=$rmId";
		// die;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Delete a rmProcurment group 
	function deleteProcurmentGroup($procurmentId)
	{
		$qry	= " delete from t_rmprocurmentorder where id=$procurmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteProcurmentVehicleDetail($procurmentId)
	{
		$qry	= "delete from t_rm_procurement_vehicle_details where rmProcurmentOrderId=$procurmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Delete a rmProcurment Supplier  
	function deleteProcurmentSupplier($procurmentId)
	{
		$qry	= " delete from t_rmprocurmentsupplier where rmProcurmentOrderId=$procurmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
		# Delete a rmProcurment detail  
	function deleteProcurmentChemical($procurmentId)
	{
		$qry	= " delete from t_rmprocurmentchemical where rmProcurmentOrderId=$procurmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteProcurmentEquipment($procurmentId)
	{
		$qry	= " delete from t_rmprocurmentequipment where rmProcurmentOrderId=$procurmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function getGatePassForEdit($gate_pass_id)
	{
		$qry	= "SELECT a.*,b.seal_number FROM procurement_gate_pass a 
				   LEFT JOIN m_seal_master b ON a.seal_no=b.id 
				   WHERE a.gate_pass_id=".$gate_pass_id;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getAllSealNos($seal_no)
	{
		$qry	= "SELECT id,seal_number FROM m_seal_master WHERE change_status='Blocked' ";
		if($seal_no != '')
		{
			$qry.= " OR id = '".$seal_no."' ";
		}
		//echo $query;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllEmployee()
	{
		$qry	= "SELECT id,name FROM m_employee_master";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function chkValidGatePassIdchkValidGatePassId($selDate)
	{
		//$selDate=Date('Y-m-d');
		$selDate=mysqlDateFormat($selDate);
		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	
	function chkValidGatePassId($selDate)
	{
		//$selDate=Date('Y-m-d');
		$selDate=mysqlDateFormat($selDate);
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 and type='MG'";

		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
		//return (sizeof($rec)>0)?true:false;
	}
	
	
	function getAlphaCode($processType)
	{
		$qry = "select alpha_code from number_gen where type='MG'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}
	
	function checkGatePassDisplayExist()
	{
	 // $qry = "select (count(*)) from m_rm_gate_pass";
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		$qry = "select (count(*)) from t_rmprocurmentorder";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getValidGatePassId($selDate)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		$selDate=mysqlDateFormat($selDate);
		 $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MG'";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getmaxGatePassId()
	{
		$qry = "select 	procurement_number from  t_rmprocurmentorder order by id desc limit 1";
		//$qry = "select gate_pass_id from m_rm_gate_pass order by id desc limit 1";
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		//$qry = "select gatePass from t_rmprocurmentorder order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoGatePassId($selDate)
	{
		
		//$selDate=Date('Y-m-d');
		$selDate=mysqlDateFormat($selDate);
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MG'";
		
		//$qry	= "select number_to from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	function generateGatePass($insertArray)
	{
		$insertStatus = '';
		if(sizeof($insertArray) > 0)
		{
			$qry = "INSERT INTO procurement_gate_pass SET ";
			$i = 0;
			foreach($insertArray as $field => $value)
			{
				if($i == 0)
				{
					$qry.= $field." = '".$value."' ";
				}
				else
				{
					$qry.= ",".$field." = '".$value."' ";
				}
				$i++;
			}	
			// echo $qry;die;
			$insertStatus	= $this->databaseConnect->insertRecord($qry);		
			// if ($insertStatus) $this->databaseConnect->commit();
			// else $this->databaseConnect->rollback();
			
			$query = "UPDATE m_seal_master SET status = 'Blocked',change_status='Used' 
					  WHERE id='".$_POST['seal_no']."' ";
					  
			$updateStatus	= $this->databaseConnect->updateRecord($query);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();		  
		}
		return $insertStatus;
	}
	function updateVehiclestatus($vehicleNo,$procurmentNo)
	{
	$qry	= "update m_vehicle_master set allocated='1' ,procurement_number	='$procurmentNo' where id='$vehicleNo'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}
	function updateDriverstatus($driverName,$procurmentNo)
	{
	$qry	= "update m_driver_master set allocated='1',procurement_number	='$procurmentNo' where id='$driverName'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}
	function updateVehicleRestatus($vehicle_number)
	{
	$qry	= "update m_vehicle_master set allocated='0',procurement_number	=''  where id='$vehicle_number'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function updateDriverRestatus($driver_name)
	{
	$qry	= "update m_driver_master set allocated='0',procurement_number	=''  where id='$driver_name'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function checkGatePassExsits($gate_pass_id)
	{
		$returnVal = '';
		$qry	= "SELECT id FROM procurement_gate_pass WHERE gate_pass_id='".$gate_pass_id."' ";
		$result	= $this->databaseConnect->getRecord($qry);
		if(sizeof($result) > 0)
		{
			$returnVal = $result[0];
		}
		return $returnVal;
	}
	function updateGenerateGatePass($updateArray,$id)
	{
		$updateStatus = '';
		if(sizeof($updateArray) > 0)
		{
			$qry = "UPDATE procurement_gate_pass SET ";
			$i = 0;
			foreach($updateArray as $field => $value)
			{
				if($i == 0)
				{
					$qry.= $field." = '".$value."' ";
				}
				else
				{
					$qry.= ",".$field." = '".$value."' ";
				}
				$i++;
			}
			$qry.= " WHERE id = ".$id;
			// echo $qry;die;
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
			
			if($_POST['editSealNo'] != '' && $_POST['seal_no'] != $_POST['editSealNo'])
			{
				$query = "UPDATE m_seal_master SET status = 'Blocked',change_status='Used' 
						  WHERE id='".$_POST['seal_no']."' ";
						  
				$statusChange	= $this->databaseConnect->updateRecord($query);		
				if ($statusChange) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();

				$query = "UPDATE m_seal_master SET status = 'Used',change_status='Blocked' 
						  WHERE id='".$_POST['editSealNo']."' ";
						  
				$statusChange	= $this->databaseConnect->updateRecord($query);		
				if ($statusChange) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		}
		return $updateStatus;
	}
	function checkGatePass($gate_pass_id)
	{
		$sql = "SELECT count(*) AS total FROM t_rmprocurmentorder 
				WHERE gatePass='".$gate_pass_id."' ";
		$result	= $this->databaseConnect->getRecord($sql);
		return $result[0];
	}
	function getGatePassDetails($gate_pass_id)
	{
		$sql = "SELECT * FROM procurement_gate_pass 
				WHERE gate_pass_id='".$gate_pass_id."' ";
		$result	= $this->databaseConnect->getRecord($sql);
		return $result;
	}
	
	function updateProcurmentconfirm($procurementId){
		$qry	= "update t_rmprocurmentorder set active='1' where id=$procurementId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateProcurmentReleaseconfirm($procurementId){
	$qry	= "update t_rmprocurmentorder set active='0' where id=$procurementId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	function getChemical($procurmentId)
	{	
		$qry 	= "select a.id,a.chemical_id,a.required_quantity,b.chemical_name from t_rmprocurmentchemical a left join m_harvesting_chemical_master b on a.chemical_id=b.id where rmProcurmentOrderId='$procurmentId'";
	
	
		//$qry 	= "select id,chemical_id,required_quantity from t_rmprocurmentchemical where rmProcurmentOrderId='$procurmentId' order by chemical_Name asc";
				 // $qry 	= "select id,chemical_Name,chemical_required,chemical_issued from t_rmprocurmentchemical where rmProcurmentOrderId='$procurmentId' order by chemical_Name asc";
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getEquipment($procurmentId)
	{	
		$qry 	= "select a.id,a.equipment_id,a.required_quantity,b.name_of_equipment from t_rmprocurmentequipment a left join m_harvesting_equipment_master b on a.equipment_id=b.id where a.rmProcurmentOrderId='$procurmentId'";
		//$qry 	= "select id,equipment_id,required_quantity from t_rmprocurmentequipment where rmProcurmentOrderId='$procurmentId' order by equipment_Name asc";
		
		// $qry 	= "select id,equipment_Name,max_equipment,equipment_issued,difference from t_rmprocurmentequipment where rmProcurmentOrderId='$procurmentId' order by equipment_Name asc";
	//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getharvestingChemical($chemicalId)
	{		
		 $qry 	= "select chemical_name from m_harvesting_chemical_master where id='$chemicalId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getharvestingEquipment($equipmentId)
	{		
		 $qry 	= "select name_of_equipment from m_harvesting_equipment_master where id='$equipmentId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getVehicleId($ProcurmentId)
	{		
		 $qry 	= "select vehicle_number from t_rmprocurmentorder where id='$ProcurmentId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getSupplierGroupId($ProcurmentId)
	{		
		 $qry 	= "select suppler_group_name from t_rmprocurmentorder where id='$ProcurmentId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function fetchAllRecordsActiveSupplierName()
	{
	 $qry	= "select id,name from supplier where active='Y' order by name asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getGatePass($procurementId)
	{
		$qry	= "select gate_pass_id from m_rm_gate_pass where procurment_id='$procurementId'"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function filterSupplierGroupList($supplierID)
	{
		$qry	= "select a.id,a.supplier_group_name from m_supplier_group a inner join m_supplier_group_details b	on a.id=supplier_group_name_id where b.supplier_name='$supplierID'"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function filterPondLocationList($pondNameId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select a.id,a.name,b.pond_size,b.pond_qty,b.registration_no from m_landingcenter a inner join  m_pond_master b on a.id=b.location  where b.id='$pondNameId'";
		//echo $qry;
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
		
	}
	function filterPondQtyList($pondNameId)
	{   $qry	= "select id,pht_Qty from  t_phtcertificate where pond_Name='$pondNameId'"; 
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
		
	}
	function fetchAllDriverNameList()
	{
			$qry	= "select id, name_of_person from m_driver_master where active='1' and allocated='0'";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function fetchAllVehicleNameList()
	{
		$qry	= "select id, vehicle_number from m_vehicle_master where active='1' and allocated='0' ";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function checkExistInProcurement($driverid)
	{
	 $qry	= "SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='$driverid' and a.active=1 "; 
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllDriverName($schedule_date,$procurementId)
	{
	//echo $schedule_date;
		$qry	= "select id, name_of_person from m_driver_master where active='1' and allocated='0'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$arrayVal=array();
		if(sizeof($result)>0)
		{
			$arrayVal[]='--select--';
			foreach($result as $driver)
			{ 
			$driverid=$driver[0];
			$driverName=$driver[1];
		//echo $driverid.$driverName.',';
				$query	= "SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_vehicle_details b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where a.id='$driverid' and b.rmProcurmentOrderId!='$procurementId' and a.active=1 "; 
				//
				//echo $query;
				// $query	= "SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.driver_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='$driverid' and a.active=1 "; 
				$results	=	array();
				$results	=	$this->databaseConnect->getRecords($query);
				
					if(sizeof($results) >0)
					{
					
						if(sizeof($results)==1)
						{
							$allotDt=$results[0][2];
							$returnDay=$results[0][4]+1;
							if(strtotime($allotDt)==strtotime($schedule_date))
							{
								continue;	
							}
							else
							{
								//echo "hii";
								$date = date('Y-m-d',strtotime($allotDt) + (24*3600*$returnDay));
							//	echo $schedule_date;
						//echo $allotDt.','.$returnDay.','.$date.','.$schedule_date.','.$driverName;
								//echo $date.','.$schedule_day.','.$driverName;
								if(strtotime($date) >= strtotime($schedule_date))
								{
									$newdate=date('Y-m-d',strtotime($date) - (24*3600*$returnDay));
									//echo '<br/>'.$newdate.','.$schedule_date.'<br/>';
										if(strtotime($newdate) <= strtotime($schedule_date))
										{
											continue;	
										}										
								}
									
									
							}
							//continue;	
						}
						elseif(sizeof($results)>1)
						{
							$setvalue=1;
							foreach($results as $driverValues)
							{
								$allotdts=$driverValues[2];
								$add_days=$driverValues[4]+1;
								$dates = date('Y-m-d',strtotime($allotdts) + (24*3600*$add_days));
									if(strtotime($allotdts)==strtotime($schedule_date))
									{
										$setvalue=0;	
									}
									else
									{
									
									
									
									
									
										if($dates >= $schedule_day)
										{
											$newdate=date('Y-m-d',strtotime($dates) - (24*3600*$add_days));
											echo $dates.','.$schedule_date.'  /  ';
												//echo $dates.','.$allotdts.','.$add_days.','.$newdate.','.$schedule_date.','.$driverName;
												if($newdate >= $schedule_date)
												{
													$setvalue=0;	
												}
												elseif($newdate<=$allotdts)
												{
													$setvalue=0;	
												}
												else
												{
													$setvalue=1;	
												}
												// if($newdate <= $schedule_date)
												// {
												// echo	$setvalue=0;	
												// }
												// else
												// {
													// $setvalue=1;	
												// }
												
										}
										else
										{
											$setvalue=0;
										}
											
									}
			
								}
								
								if($setvalue==0)
								{
									continue;
								}
								
							}
						
					}
					//print_r($arrayVal[$driverid]=$driverName);
					$arrayVal[$driverid]=$driverName;
			}
			return $arrayVal;
				//print_r($arrayVal);
		}
		//return $result;
	}
	
	function fetchAllVehicleName($schedule_date,$procurementId)
	{
		$qry	= "select id,vehicle_number from  m_vehicle_master where active='1' and allocated='0'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$arrayVal=array();
		if(sizeof($result)>0)
		{
			$arrayVal[]='--select--';
			foreach($result as $vehicle)
			{ 
			$vehicleid=$vehicle[0];
			$vehicleName=$vehicle[1];
		//echo $driverid.$driverName.',';
				$query	= "SELECT a.id,a.vehicle_number,b.schedule_date,d.pond_id,e.return_days from  m_vehicle_master a left join t_rm_procurement_vehicle_details b on a.id=b.vehicle_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where a.id='$vehicleid' and b.rmProcurmentOrderId!='$procurementId' and a.active=1 "; 
				// $query	= "SELECT a.id,a.name_of_person,b.schedule_date,d.pond_id,e.return_days from m_driver_master a left join t_rm_procurement_driver b on a.id=b.vehicle_id left join t_rmprocurmentorder c on c.id=b.rmProcurmentOrderId left join t_rmprocurmentsupplier d on d.rmProcurmentOrderId=c.id left join m_pond_master e on e.id=d.pond_id where c.generated='0' and a.id='$driverid' and a.active=1 "; 
				$results	=	array();
				$results	=	$this->databaseConnect->getRecords($query);
				
					if(sizeof($results) >0)
					{
					
						if(sizeof($results)==1)
						{
							$allotDt=$results[0][2];
							$returnDay=$results[0][4]+1;
							if(strtotime($allotDt)==strtotime($schedule_date))
							{
								continue;	
							}
							else
							{
								//echo "hii";
								$date = date('Y-m-d',strtotime($allotDt) + (24*3600*$returnDay));
								//echo $allotdt.','.$add_day.','.$date.','.$schedule_day.','.$driverName;
								//echo $date.','.$schedule_day.','.$driverName;
								if(strtotime($date) >= strtotime($schedule_date))
								{
									$newdate=date('Y-m-d',strtotime($date) - (24*3600*$returnDay));
										if(strtotime($newdate) <= strtotime($schedule_date))
										{
											continue;	
										}										
								}
									
									
							}
							//continue;	
						}
						elseif(sizeof($results)>1)
						{
							$setvalue=1;
							foreach($results as $driverValues)
							{
								$allotdts=$driverValues[2];
								$add_days=$driverValues[4]+1;
								$dates = date('Y-m-d',strtotime($allotdts) + (24*3600*$add_days));
									if(strtotime($allotdts)==strtotime($schedule_date))
									{
										$setvalue=0;	
									}
									else
									{
										if($dates >= $schedule_day)
										{
											$newdate=date('Y-m-d',strtotime($dates) - (24*3600*$add_days));
												//echo $allotdts.','.$add_days.','.$newdate.','.$schedule_day.','.$driverName;
												if($newdate >= $schedule_date)
												{
													$setvalue=0;	
												}
												else
												{
													$setvalue=1;	
												}
												// if($newdate <= $schedule_date)
												// {
													// $setvalue=0;	
												// }
												// else
												// {
													// $setvalue=1;	
												// }
												
										}
										else
										{
											$setvalue=0;
										}
											
									}
			
								}
								
								if($setvalue==0)
								{
									continue;
								}
								
							}
						
					}
					//print_r($arrayVal[$driverid]=$driverName);
					$arrayVal[$vehicleid]=$vehicleName;
			}
			return $arrayVal;
				//print_r($arrayVal);
		}
		//return $result;
	}
	function getVehicleAndDriverDetails($procurementId)
	{
		$qry	= "select a.id,a.vehicle_id,a.driver_id,b.vehicle_number,c.name_of_person from  t_rm_procurement_vehicle_details a left join m_vehicle_master b on a.vehicle_id=b.id left join   m_driver_master c on a.driver_id=c.id where rmProcurmentOrderId='$procurementId'"; 
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	
	}
}
?>