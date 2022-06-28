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
	
	#Add procurment
	function addProcumentOrder($procurmentNo, $selCompanyName,$selRMSupplierGroup,$supplierName,$supplierAddress,$pondName,$pondAddress,$entryDate, $userId)
	{
		$qry	= "insert into t_rmprocurmentorder(gatePass, company,suppler_group_name,supplier_name,supplier_address,pond_name,pond_address,date_of_entry, created_on, created_by) values('$procurmentNo', '$selCompanyName','$selRMSupplierGroup','$supplierName','$supplierAddress','$pondName','$pondAddress','$entryDate', Now(),'$userId')";
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
	function addProcurmentEntries($lastId, $driverId, $vehicleId, $equipmentNameId, $equipmentQty,$equipmentIssued,$balanceQty, $chemicalNameId,$chemicalQty,$chemicalIssued)
	{
		 $qry	= "insert into t_rmprocurmentorderentries(rmProcurmentOrderId, driver_Name,vehicle_No,equipment_Name,max_equipment,equipment_issued,difference,chemical, chemical_required, chemical_issued) values('$lastId', '$driverId', '$vehicleId', '$equipmentNameId', '$equipmentQty','$equipmentIssued','$balanceQty', '$chemicalNameId','$chemicalQty','$chemicalIssued')";
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
		$qry	= "select id, name from m_companydetails ";
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
	
	function fetchAllDriverName()
	{
		$qry	= "select id, name_of_person from m_driver_master where active='1' ";
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
		$qry	= "select id, vehicle_number from m_vehicle_master where active='1' ";
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
		$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
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
	function filterSupplierAddressList($supplierNameId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select address from supplier where id='$supplierNameId'";
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
		$qry	= "select * from t_rmprocurmentorder where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get procurment entries based on procurement id 
	function find($orderId)
	{
		$qry	=	"select * from t_rmprocurmentorder where id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on  purchaseorder_entry TABLE	
	function fetchAllProcurmentEntries($procurmentId)
	{
		 $qry	= "select * from t_rmprocurmentorderentries where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Update  a  Procurment Order
	function updateProurmentOrder($procurementId, $procurmentNo, $selCompanyName,$selRMSupplierGroup,$supplierName,$supplierAddress,$pondName,$pondAddress,$entryDate)
	{		
		$qry	= "update t_rmprocurmentorder set gatePass='$procurmentNo', company='$selCompanyName', suppler_group_name='$selRMSupplierGroup',supplier_name='$supplierName',supplier_address='$supplierAddress',pond_name='$pondName',pond_address='$pondAddress',date_of_entry='$entryDate' where id='$procurementId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	# Update Entry			
	function updateProcurmentDetails($procurementId, $driverName,$vehicleNo,$equipmentName,$chemicalName,$equipmentQty,$equipmentIssued,$balanceQty,$chemicalQty,$chemicalIssued)
	{
		$qry = " update t_rmprocurmentorderentries set driver_Name='$driverName',vehicle_No='$vehicleNo',equipment_Name='$equipmentName',max_equipment='$equipmentQty',equipment_issued='$equipmentIssued',difference='$balanceQty',chemical='$chemicalQty',chemical_required='$chemicalQty',chemical_issued='$chemicalIssued' where rmProcurmentOrderId='$procurementId'";
		// echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Delete Entry Rec
	function delRMProcurmentEntriesRec($rmId)
	{
		// echo $qry = " delete from t_rmprocurmentorderentries where rmProcurmentOrderId=$rmId";
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
	
	# Delete a rmProcurment detail  
	function deleteProcurmentDetail($procurmentId)
	{
		$qry	= " delete from t_rmprocurmentorderentries where rmProcurmentOrderId=$procurmentId";

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
	
	function chkValidGatePassId($selDate)
	{
		//$selDate=Date('Y-m-d');
		$selDate=mysqlDateFormat($selDate);
		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	
	function getAlphaCode($processType)
	{
		$qry = "select alpha_code_prefix from manage_procrment_gate_pass";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}
	
	function checkGatePassDisplayExist()
	{
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
		$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getmaxGatePassId()
	{
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		$qry = "select gatePass from t_rmprocurmentorder order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoGatePassId($selDate)
	{
		
		//$selDate=Date('Y-m-d');
		$selDate=mysqlDateFormat($selDate);
		$qry	= "select number_to from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
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
}
?>