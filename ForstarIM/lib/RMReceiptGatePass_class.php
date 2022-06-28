<?php
class RMReceiptGatePass
{  
	
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RMReceiptGatePass(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function fetchAllRecordsRMProcurment()
	{
		 $qry	=	"select id, gatePass from t_rmprocurmentorder order by gatePass asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsInSeal()
	{
		 $qry	=	"select id, seal_number from m_seal_master where purpose='IN' order by seal_number asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllProcessType()
	{
		  $qry	=	"select id, process_type from m_lotid_process_type  order by process_type asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsOutSeal()
	{
		 $qry	=	"select id, seal_number from m_seal_master where purpose='OUT' order by seal_number asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Filter driver List
	function filterDriver($procurmentGatePassId)
	{ 
 		$qry="select a.id,a.driver_Name,b.name_of_person from t_rmprocurmentorderentries a join m_driver_master b on a.driver_Name=b.id where rmProcurmentOrderId='$procurmentGatePassId' order by name_of_person asc";
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
	
	# Filter vehicle List
	function filterVehicleNumber($procurmentGatePassId)
	{ 
 		$qry="select a.id,a.vehicle_No,b.vehicle_number from t_rmprocurmentorderentries a join m_vehicle_master b on a.vehicle_No=b.id where rmProcurmentOrderId='$procurmentGatePassId' order by vehicle_number asc";
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
	
	# Filter labours
	function filterLaboursList($superVisorNameId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select labours from procurement_gate_pass where supervisor='$superVisorNameId'";
		//echo $qry;
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	#Add rmReceipt GatePass
	function addRmReceiptGatePass($processType,$lotId, $procurmentGatePassId, $vehicleNumbers, $driver, $inSeal,$result,$sealNo ,$outSeal,$verified,$labours,$selCompanyName,$unit,$supplierChallanNo,$supplierChallanDate,$dateOfEntry, $userId)
	{

	//$supplierChallanDateval=mysqlDateFormat($supplierChallanDate);

		$qry	= "insert into t_rmReceiptGatePass(process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry, created_on, created_by) values('$processType','$lotId', '$procurmentGatePassId', '$vehicleNumbers', '$driver', '$inSeal','$result','$sealNo' ,'$outSeal','$verified','$labours','$selCompanyName','$unit','$supplierChallanNo','$supplierChallanDate','$dateOfEntry', Now(),'$userId')";
		// $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	#Add a Seal Master
	function addSealMaster($sealNo, $userId)
	{
		$qry	=	"insert into m_seal_master (seal_number, status, purpose,change_status, created_on, created_by) values('".$sealNo."', 'Free', 'IN','Blocked', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function addReceiptGatepassSupplier($lastId,$supplier_id,$pond_id,$challan_no,$challan_date,$Company_Name,$unit,$landing_center)
	{
		$qry	=	"insert into t_rm_receipt_gatepass_supplier (receipt_gatepass_id, supplier_id, pond_id,challan_no, challan_date, company_id,unit_id,landing_center_id) values('".$lastId."','".$supplier_id."','".$pond_id."','".$challan_no."','".$challan_date."','".$Company_Name."','".$unit."','".$landing_center."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Add a unit
	function addUnit($supplierChallanNo,$unit,$processType,$lotId, $userId)
	{
		$qry	=	"insert into t_unitTransfer (supplier_Details,current_Unit,current_Stage,unit_Name,process_Type, new_lot_Id ,created_on, created_by) values('".$supplierChallanNo."','".$unit."', '".$processType."','".$unit."', '".$processType."', '".$lotId."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	# Update  a seal
	function updateSealMaster($sealNo,$oldSeal)
	{
		$qry	= " update m_seal_master set seal_number='$sealNo' where seal_number=$oldSeal";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function findSeal($rmReceiptGatePassId)
	{
		 $qry	= "select seal_No from t_rmreceiptgatepass where id=$rmReceiptGatePassId";

		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	# Returns all RM Receipt Gate Pass
	function fetchAllRecords()
	{
		$qry	= "select id, process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmreceiptgatepass order by date_Of_Entry desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function chkValidGatePassId($selDate)
	{
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 and  type='RG'";

		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}
	
	function chkValidGatePassIdSeal($selDate)
	{
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 and type='SL'";

		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	function getAlphaCodeGatePass()
	{
		$qry = "select alpha_code from number_gen where type='RG'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}

	function checkGatePassDisplayExist()
	{
	
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		$qry = "select (count(*)) from t_rmreceiptgatepass";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getmaxGatePassId()
	{
		$qry = "select 	receipt_gatepass_number from  t_rmreceiptgatepass order by id desc limit 1";
		//$qry = "select gate_pass_id from m_rm_gate_pass order by id desc limit 1";
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		//$qry = "select gatePass from t_rmprocurmentorder order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getValidendnoGatePassId($selDate)
	{
		
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='RG'";
		
		//$qry	= "select number_to from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getValidGatePassId($selDate)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		 $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='RG'";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function chkValidLotId($selDate,$processType)
	{
		$selDate=Date('Y-m-d');
		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		$qry	="select id,process_type,number_from, number_to from m_lotid_generate where process_type='$processType' and  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	
	function getAlphaCode($processType)
	{
		$qry = "select alpha_code_prefix from m_lotid_generate where  process_type='$processType'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function checkLotIdDisplayExist($processType)
	{
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		$qry = "select (count(*)) from t_unitTransfer where process_type='$processType'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getmaxLotId($processType)
	{
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		$qry = "select new_lot_Id from t_unitTransfer where  process_type='$processType' order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoLotId($selDate,$processType)
	{
		
		$selDate=Date('Y-m-d');
		$qry	= "select number_to from m_lotid_generate where process_type='$processType' and date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getValidLotId($selDate,$processType)
	{
		//$billingCompany=0;
		$selDate=Date('Y-m-d');
		$qry	= "select number_from from m_lotid_generate where  process_type='$processType' and date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	# Returns all RM Receipt Gate Pass
	/*function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id, process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmReceiptGatePass where date_Of_Entry>='$fromDate' and date_Of_Entry<='$tillDate' order by date_Of_Entry desc limit $offset,  $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}*/

	# Get process type based on id 
	function findProcessType($processId)
	{
		$qry	=	"select id, process_type from m_lotid_process_type where id=$processId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Get RMReceipt Gatepass based on id 
	function find($RMReceiptGatePassId)
	{
		$qry	= "select id,process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmreceiptgatepass where id=$RMReceiptGatePassId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	// for pagination
	/*function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmReceiptGatePass where date_Of_Entry>='$fromDate' and date_Of_Entry<='$tillDate' order by date_Of_Entry desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}*/
	
	function fetchAllRmReceiptGatePassItem($rmReceiptGatePassId)
	{
		$qry	= "select id, process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmReceiptGatePass where id='$rmReceiptGatePassId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Update  a  rm Receipt Gate pass
	function updateRMReceiptGatePass($rmReceiptGatePassId,$processType,$lotId, $procurmentGatePassId, $vehicleNumbers, $driver, $inSeal,$result,$sealNo ,$outSeal,$verified,$labours,$selCompanyName,$unit,$supplierChallanNo,$supplierChallanDate,$dateOfEntry)
	{
		$qry	= " update t_rmReceiptGatePass set process_type='$processType', lot_Id='$lotId', procurment_Gate_PassId='$procurmentGatePassId', vehicle_Number='$vehicleNumbers', driver='$driver',in_Seal='$inSeal',result='$result',seal_No='$sealNo',out_Seal='$outSeal',verified='$verified',labours='$labours',Company_Name='$selCompanyName',unit='$unit',supplier_Challan_No='$supplierChallanNo' ,supplier_Challan_Date='$supplierChallanDate' ,date_Of_Entry='$dateOfEntry'  where id=$rmReceiptGatePassId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
		# Delete a RM Receipt Gate Pass
	function DeleteRmReceiptGatePass($rmReceiptGatePassId)
	{
		$qry	= " delete from t_rmReceiptGatePass where id=$rmReceiptGatePassId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	# Get all the receipt gate pass 
	function getAllReceiptGatePass()
	{
	 	$qry = "SELECT a.*,b.gate_pass_id,c.name as company_name,d.name as unit_name,e.name as Supervisor 
				FROM t_rmreceiptgatepass a 
		        LEFT JOIN m_rm_gate_pass b ON b.procurment_id = a.procurment_Gate_PassId 
				LEFT JOIN m_billing_company c ON c.id = a.Company_Name 
				LEFT JOIN m_plant d ON d.id = a.unit 
				left join m_employee_master e on e.id=a.verified";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}

	function getAllReceiptGatePassSupplier($receiptid)
	{
		$qry = "SELECT a.*,b.name as company_name,c.name as unit_name from t_rm_receipt_gatepass_supplier a
		LEFT JOIN m_billing_company b ON b.id = a.company_id 
				LEFT JOIN m_plant c ON c.id = a.unit_id 
		where receipt_gatepass_id='$receiptid'";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}

	# Get all the procurement id for drop down
	function getAllProcurement($gate_pass_id)
	{
	// $qry = "SELECT procurment_id,gate_pass_id FROM m_rm_gate_pass where (gate_pass_id!='' and gate_pass_id is not null) 	";
		$qry = "SELECT a.procurment_id,a.gate_pass_id FROM m_rm_gate_pass a left join t_rmreceiptgatepass b on a.procurment_id=b.procurment_Gate_PassId where b.procurment_Gate_PassId IS NULL and (a.gate_pass_id!='' and a.gate_pass_id is not null) or b.procurment_Gate_PassId='$gate_pass_id'";
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	
	# Get all the company for drop down
	function getAllCompany()
	{
		$qry = "SELECT id,name FROM m_billing_company WHERE default_row = 'Y' ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}

	function getCompanyName($companyID)
	{
	 $qry = "SELECT id,name FROM m_billing_company WHERE id = '$companyID' ";
		
		$result	=	array();
			$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	# Get all the unit for drop down
	function getAllUnit()
	{
		$qry = "SELECT id,name FROM m_plant where active='1'";
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}

	function getUnitName($untid)
	{
		$qry = "SELECT id,name FROM m_plant where id='$untid' ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	# Get all the supervisor for drop down
	function getAllSupervisor()
	{
		$qry = "SELECT id,name FROM m_employee_master ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	function getAllMaterialType()
	{
		$qry = "SELECT id,name FROM t_rm_type";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllSupplier()
	{
		$qry = "SELECT id,name FROM supplier where active='Y' AND activeconfirm='1'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	
	}
	function getSupplierName($supplier)
	{
	 $qry = "SELECT name FROM supplier where id='$supplier'";
		
		$result	=	array();
			$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getPondName($pondId)
	{
		$qry = "SELECT pond_name FROM m_pond_master where id='$pondId'";
		//echo $qry;	
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	# Get details for fill the form 
	function getFormFillData($procurement_id)
	{
	
		$qry = "SELECT a.id,a.company,a.date_of_entry,(SELECT GROUP_CONCAT(vehicle_id) from t_rm_procurement_vehicle_details WHERE rmProcurmentOrderId=a.id) as vehicle_id,(SELECT GROUP_CONCAT(vehicle_number) FROM m_vehicle_master l left join t_rm_procurement_vehicle_details m on m.vehicle_id =l.id WHERE m.rmProcurmentOrderId=a.id) as vehicle_number,(SELECT GROUP_CONCAT(driver_id) from t_rm_procurement_vehicle_details WHERE rmProcurmentOrderId=a.id) as driver_id,(SELECT GROUP_CONCAT(name_of_person) FROM m_driver_master g left join t_rm_procurement_vehicle_details h on h.driver_id =g.id WHERE h.rmProcurmentOrderId=a.id) as driver_name, (SELECT GROUP_CONCAT(labour) FROM m_gate_pass_labour WHERE rm_gate_pass_id=d.id) as labours, d.out_seal as seal_number,d.seal_out,d.id as gatepass_id,alpha_code FROM t_rmprocurmentorder a LEFT JOIN m_rm_gate_pass d ON d.procurment_id = a.id WHERE a.id = '".$procurement_id."' ";
		//echo $qry;
		/*$qry = "SELECT a.id,a.company,a.date_of_entry,e.vehicle_id as vehicle_id,b.vehicle_number,
				e.driver_id as driver_id,c.name_of_person,
				(SELECT GROUP_CONCAT(labour) FROM m_gate_pass_labour WHERE rm_gate_pass_id=d.id) as labours,
				
				d.out_seal as seal_number,d.seal_out,d.id as gatepass_id 
				FROM t_rmprocurmentorder a 
                left join t_rm_procurement_vehicle_details e on e.rmProcurmentOrderId=a.id 
				LEFT JOIN m_vehicle_master b on b.id = e.vehicle_id 
				LEFT JOIN m_driver_master c ON c.id = e.driver_id 
				LEFT JOIN m_rm_gate_pass d ON d.procurment_id = a.id 
				WHERE a.id = '".$procurement_id."' ";*/
	 	/*$qry = "SELECT a.id,a.company,a.date_of_entry,a.vehicle_number as vehicle_id,b.vehicle_number,
				a.driver_name as driver_id,c.name_of_person,
				(SELECT GROUP_CONCAT(labour) FROM m_gate_pass_labour WHERE rm_gate_pass_id=d.id) as labours,
				
				d.out_seal as seal_number,d.seal_out,d.id as gatepass_id 
				FROM t_rmprocurmentorder a 
				LEFT JOIN m_vehicle_master b on b.id = a.vehicle_number 
				LEFT JOIN m_driver_master c ON c.id = a.driver_name 
				LEFT JOIN m_rm_gate_pass d ON d.procurment_id = a.id 
				WHERE a.id = '".$procurement_id."' ";*/
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	
	function getProcurementSupplierDetails($procurmentId)
	{
	 $qry	= "select id,supplier_id,pond_id,procurement_center from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchLocationType($centerId)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select name FROM m_landingcenter WHERE id=$centerId";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}
	
	#Get a in seal drop down datas
	function getInsealData($procurement_id)
	{
	 $qry="select id,in_seal,number_gen_id,alpha_code,accepted_status FROM m_gate_pass_seal WHERE ( in_seal!='' and in_seal is not null ) and rm_gate_pass_id='$procurement_id'";
	//$qry="select id,in_seal FROM m_gate_pass_seal WHERE  rm_gate_pass_id='$procurement_id' ";
	/*$qry="select a.id,a.seal_number FROM m_seal_master a left join m_gate_pass_seal b on b.seal=a.id 
					left join m_rm_gate_pass c on c.id=b.rm_gate_pass_id where c.procurment_id ='$procurement_id'
					and (a.change_status='Free' OR a.change_status='Blocked'  
				OR a.id = (SELECT in_Seal FROM t_rmreceiptgatepass WHERE 
				procurment_Gate_PassId = '".$procurement_id."')) " ;*/
				
	//protect//	/*$qry="select a.id,a.seal_number FROM m_seal_master a left join m_gate_pass_seal b on b.seal=a.id 
					//left join m_rm_gate_pass c on c.id=b.rm_gate_pass_id where c.procurment_id ='$procurement_id'" ;*/
			
					
		// $qry = "SELECT id,seal_number FROM m_seal_master WHERE id IN (SELECT seal FROM 
				// m_gate_pass_seal WHERE rm_gate_pass_id = '".$procurement_id."') ";
			
	/*echo	$qry = "SELECT a.id,a.seal_number FROM m_seal_master a 
		        INNER JOIN m_gate_pass_seal b ON a.id = b.rm_gate_pass_id 
				WHERE a.id = '".$procurement_id."' AND (a.change_status='Free' OR a.change_status='Blocked'  
				OR a.id = (SELECT in_Seal FROM t_rmreceiptgatepass WHERE 
				procurment_Gate_PassId = '".$procurement_id."'))  ";*/
				//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	function getalphachar($alphacharacter)
	{
	 $qry="select alpha_code FROM number_gen WHERE id='".$alphacharacter."'";
	 $result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	# Get receipt gate pass details for edit
	function getReceiptGatePassForEdit($editId)
	{
		$qry = "SELECT a.*,b.vehicle_number,c.name_of_person as driver_name FROM t_rmreceiptgatepass a 
				LEFT JOIN m_vehicle_master b on b.id = a.vehicle_number 
				LEFT JOIN m_driver_master c ON c.id = a.driver   
				WHERE a.id = '".$editId."' ";
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	function getSealNo()
	{
		$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='SL' AND end_date >= '".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1 ";
		$result = $this->databaseConnect->fetch_array($qry);
		
		return $result;
	}
	function getReceiptGatePassInSeal($procurment_Gate_PassId)
	{
	 $qry = "SELECT in_Seal from t_rmreceiptgatepass where procurment_Gate_PassId=
				 '".$procurment_Gate_PassId."' ";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getReceiptSupplierDetails($receipt_id)
	{
	  $qry = "SELECT id,supplier_id,pond_id,landing_center_id,challan_no,challan_date,company_id,unit_id from t_rm_receipt_gatepass_supplier where receipt_gatepass_id = '".$receipt_id."' ";
	// $qry = "SELECT a.*,b.vehicle_number,c.name_of_person as driver_name FROM t_rmreceiptgatepass a 
				// LEFT JOIN m_vehicle_master b on b.id = a.vehicle_number 
				// LEFT JOIN m_driver_master c ON c.id = a.driver   
				// WHERE a.id = '".$editId."' ";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getReceiptValid($procurementID)
	{
		$qry	= "select id from t_rmreceiptgatepass where procurment_Gate_PassId='$procurementID' ";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	#Add a rm receipt gate pass details
	function addRmReceiptGatePassDetails($data,$userId)
	{	
		$date_Of_EntryDateval=mysqlDateFormat($data['date_Of_Entry']);
		$supplierChallanDateval=mysqlDateFormat($data['supplier_Challan_Date']);
		
		$qry	=	"insert into t_rmreceiptgatepass(receipt_gatepass_number,procurment_Gate_PassId,vehicle_Number,driver,
					 vehicle_number_other,driver_name_other,in_Seal,result,
					 seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_id,material_type_id,supplier_Challan_No,
					 supplier_Challan_Date,date_Of_Entry,pond_id,landingcenter_id,created_on,created_by,active,from_company,from_unit) values
					 ('".$data['receiptGatePass']."','".$data['procurment_Gate_PassId']."','".$data['vehicle_id']."','".$data['driver_id']."',
					 '".$data['vehicle_Number']."','".$data['driver']."',
					 '".$data['in_Seal']."','".$data['result']."','".$data['seal_No']."', '".$data['out_seal_id']."',
					 '".$data['verified']."','".$data['labours']."','".$data['Company_Name']."','".$data['unit']."','".$data['supplier']."','".$data['material']."',
					 '".$data['supplier_Challan_No']."', '$supplierChallanDateval', '$date_Of_EntryDateval','".$data['pond']."','".$data['landingCenter']."',
					 '".date('Y-m-d')."','".$userId."','1','".$data['selCompanyName']."','".$data['unitId']."')";
		
		
		/*$qry	=	"insert into t_rmreceiptgatepass (receipt_gatepass_number,procurment_Gate_PassId,vehicle_Number,driver,
					 vehicle_number_other,driver_name_other,in_Seal,result,
					 seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_id,material_type_id,supplier_Challan_No,
					 supplier_Challan_Date,date_Of_Entry,created_on,created_by,active) values
					 ('".$data['receiptGatePass']."','".$data['procurment_Gate_PassId']."','".$data['vehicle_id']."','".$data['driver_id']."',
					 '".$data['vehicle_Number']."','".$data['driver']."',
					 '".$data['in_Seal']."','".$data['result']."','".$data['seal_No']."', '".$data['out_seal_id']."',
					 '".$data['verified']."','".$data['labours']."','".$data['Company_Name']."','".$data['unit']."','".$data['supplier']."','".$data['material']."',
					 '".$data['supplier_Challan_No']."', '$supplierChallanDateval', '$date_Of_EntryDateval',
					 '".date('Y-m-d')."','".$userId."','1')";*/
		
		
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	function updatesealstatus($data,$userId)
	{
	if(isset($data['in_Seal']) && $data['in_Seal'] != '')
		{
		 $qry	=	"UPDATE m_gate_pass_seal SET accepted_status = '1' WHERE id='".$data['in_Seal']."'";
		
			//$qry	=	"UPDATE m_seal_master SET change_status = 'Used' WHERE id IN('".$data['in_Seal']."') ";
		
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
			return $updateStatus;
		}
		}
		
		function updateInsealstatusValue($inseal)
		{
		//echo $inseal;
			$qry	=	"UPDATE m_gate_pass_seal SET accepted_status = '0' WHERE id='".$inseal."'";
		
			//$qry	=	"UPDATE m_seal_master SET change_status = 'Used' WHERE id IN('".$data['in_Seal']."') ";
		
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		return $updateStatus;
		}
	#Update a rm receipt gate pass details
	function updateRmReceiptGatePassDetails($data,$userId)
	{
		$date_Of_EntryDateval=mysqlDateFormat($data['date_Of_Entry']);
		$supplierChallanDateval=mysqlDateFormat($data['supplier_Challan_Date']);	
		($data['material']=='2')? $data['pond']="":$data['landingCenter']="";
		$qry	="UPDATE t_rmreceiptgatepass SET receipt_gatepass_number='".$data['receiptGatePass']."',procurment_Gate_PassId = '".$data['procurment_Gate_PassId']."',vehicle_Number = '".$data['vehicle_id']."',driver = '".$data['driver_id']."',vehicle_number_other = '".$data['vehicle_Number']."',driver_name_other = '".$data['driver']."',in_Seal = '".$data['in_Seal']."',result = '".$data['result']."',seal_No = '".$data['seal_No']."', out_Seal = '".$data['out_seal_id']."',verified = '".$data['verified']."',labours = '".$data['labours']."',Company_Name = '".$data['Company_Name']."',unit= '".$data['unit']."',supplier_id ='".$data['supplier']."',material_type_id = '".$data['material']."',supplier_Challan_No = '".$data['supplier_Challan_No']."',supplier_Challan_Date = '$supplierChallanDateval',date_Of_Entry = '$date_Of_EntryDateval',pond_id='".$data['pond']."',landingcenter_id='".$data['landingCenter']."', created_by = '".$userId."' WHERE id = '".$data['id']."' ";
		//echo $qry;

		/*$qry	="UPDATE t_rmreceiptgatepass SET receipt_gatepass_number='".$data['receiptGatePass']."', 
					procurment_Gate_PassId = '".$data['procurment_Gate_PassId']."',
					 vehicle_Number = '".$data['vehicle_id']."',driver = '".$data['driver_id']."',
					 vehicle_number_other = '".$data['vehicle_Number']."',driver_name_other = '".$data['driver']."',
					 in_Seal = '".$data['in_Seal']."',result = '".$data['result']."',
					 seal_No = '".$data['seal_No']."',out_Seal = '".$data['out_seal_id']."',
					 verified = '".$data['verified']."',labours = '".$data['labours']."',
					 Company_Name = '".$data['Company_Name']."',unit = '".$data['unit']."',supplier_id = '".$data['supplier']."',material_type_id = '".$data['material']."'
					 ,supplier_Challan_No = '".$data['supplier_Challan_No']."',supplier_Challan_Date = '$supplierChallanDateval',
					 date_Of_Entry = '$date_Of_EntryDateval',created_by = '".$userId."' WHERE id = '".$data['id']."' ";*/
		
		//echo $qry;
		$updateStatus	= $this->databaseConnect->updateRecord($qry);		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
	function updateReceiptGatepassSupplier($receipt_id,$supplier_id,$pond_id,$challan_no,$challan_date,$Company_Name,$unit,$landing_center)
	{
		$qry="update  t_rm_receipt_gatepass_supplier set supplier_id='$supplier_id',pond_id='$pond_id',challan_no='$challan_no',challan_date='$challan_date',company_id='$Company_Name',unit_id='$unit',landing_center_id='$landing_center' where id='$receipt_id'";
		$updateStatus	= $this->databaseConnect->updateRecord($qry);		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
	
	# Check lot id availability
	function checkLotIdAvailable()
	{
		$lastLotIdNumber = 0;
		 $sql = "SELECT lot_Id FROM t_rmreceiptgatepass where lot_Id is not null ORDER BY lot_Id DESC LIMIT 0,1";
		$resultNo	=	$this->databaseConnect->fetch_array($sql);
		if(sizeof($resultNo) > 0)
		{
		//print_r($resultNo);
			//$lastLotIdNumber = explode('F',$resultNo[0]['lot_Id']);
			$lastLotIdNumber = explode('FR',$resultNo[0]['lot_Id']);
			//sizeof($lastLotIdNumber)
			if(sizeof($lastLotIdNumber) == 2)
			{
			//echo "hii";
				$lastLotIdNumber = $lastLotIdNumber[1];
			}
		}
		 $lastLotIdNumber;
		/*
		 $qry = "SELECT count(*) as total FROM number_gen 
                WHERE type = 'LF' AND alpha_code = 'FR' AND (end_date <= ".date('Y-m-d')." 
			     OR end_no <= '".$lastLotIdNumber."')  ";*/
		$selDate = date('Y-m-d');
		$qry	= "select count(*) as total from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='LF' AND  alpha_code = 'FR' AND end_no <= '".$lastLotIdNumber."') ";

		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		if($result[0]['total'] == 0)
		{
			return $lastLotIdNumber;
		}
		else
		{
			return $result[0]['total'];
		}
	}
	
	# Generate lot ID 
	function generateLotID($generateLotID,$lastNumber)
	{
		$lastNumber = $lastNumber+1;
		$lotId = 'FR'.$lastNumber;
		//$lotId = 'F'.$lastNumber;
		
		$qry	=	"UPDATE t_rmreceiptgatepass SET lot_Id = '".$lotId."' WHERE id = '".$generateLotID."' ";
		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
	
	# Free blocked seals to free
	function freeSeals($blockIds)
	{
		$qry	=	"UPDATE m_gate_pass_seal SET accepted_status = '2' WHERE id IN(".$blockIds.") ";
		//$qry	=	"UPDATE m_seal_master SET status = 'Free' WHERE id IN('".$blockIds."') ";
		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
	
	function fetchAllPagingRecords($fromDate, $tillDate,$offset, $limit)
	{
		$qry = "SELECT a.*,b.gate_pass_id,c.name as company_name,d.name as unit_name,e.name as Supervisor 
				FROM t_rmreceiptgatepass a 
		        LEFT JOIN m_rm_gate_pass b ON b.procurment_id = a.procurment_Gate_PassId 
				LEFT JOIN m_billing_company c ON c.id = a.Company_Name 
				LEFT JOIN m_plant d ON d.id = a.unit
				left join m_employee_master e on e.id=a.verified
				where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		/*$qry	= "select a.*,b.supplier_group_name,c.vehicle_number,d.name_of_person from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";*/
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	function fetchAllDateRangeRecords($fromDate, $tillDate)
	{
		$qry = "SELECT a.*,b.gate_pass_id,c.name as company_name,d.name as unit_name,e.name as Supervisor 
				FROM t_rmreceiptgatepass a 
		        LEFT JOIN m_rm_gate_pass b ON b.procurment_id = a.procurment_Gate_PassId 
				LEFT JOIN m_billing_company c ON c.id = a.Company_Name 
				LEFT JOIN m_plant d ON d.id = a.unit
				left join m_employee_master e on e.id=a.verified
				where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc";
		/*$qry	= "select a.*,b.supplier_group_name,c.vehicle_number,d.name_of_person from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";*/
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	function getValidGatePassIdSeal($selDate)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		   $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='SL'";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getValidendnoGatePassIdSeal($selDate)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		   $qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='SL'";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getEquipmentValid($procurementID)
	{
		$qry	= "select trmpe.id,trmpe.equipment_id,trmpe.issued_quantity,mhem.name_of_equipment,trmpe.returned_quantity,trmpe.difference_inreturn,trmpe.remarks from t_rmprocurmentequipment trmpe left join m_harvesting_equipment_master mhem on mhem.id=trmpe.equipment_id  where rmProcurmentOrderId='$procurementID' ";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getChemicalValid($procurementID)
	{
		$qry	= "select trmpe.id,trmpe.chemical_id,trmpe.issued_quantity,mhem.chemical_name,trmpe.returned_quantity,trmpe.used_quantity,trmpe.remarks from t_rmprocurmentchemical trmpe left join m_harvesting_chemical_master mhem on mhem.id=trmpe.chemical_id  where rmProcurmentOrderId='$procurementID' ";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateReceiptEquipment($procurementEquipmentId,$equipmentId,$equipmentReturnedQuantity,$equipmentDifferenceQuantity,$equipmentRemarks)
	{
		$qry	= "update t_rmprocurmentequipment set returned_quantity = '$equipmentReturnedQuantity',difference_inreturn = '$equipmentDifferenceQuantity' ,remarks='$equipmentRemarks' where id = '$procurementEquipmentId' and equipment_id='$equipmentId'";	
		$insertStatus	=	$this->databaseConnect->updateRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function updateReceiptChemical($procurementChemicalId,$chemicalId,$chemicalReturnedQuantity,$chemicalUsedQuantity,$chemicalRemarks)
	{
		$qry	= "update t_rmprocurmentchemical set returned_quantity = '$chemicalReturnedQuantity',used_quantity = '$chemicalUsedQuantity' ,remarks='$chemicalRemarks' where id = '$procurementChemicalId' and chemical_id='$chemicalId'";	
		$insertStatus	=	$this->databaseConnect->updateRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	function updateVehiclestatus($vehicleId)
	{
		$qry	= "update m_vehicle_master set allocated='0' ,procurement_number='' where id='$vehicleId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateDriverstatus($driverId)
	{
		$qry	= "update m_driver_master set allocated='0',procurement_number='' where id='$driverId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}

	# Filter supplier Address
	function getfilterPondList($supplier)
	{
		$qry="SELECT a.supplier,a.id, a.pond_name FROM m_pond_master a JOIN supplier b ON a.supplier = b.id WHERE a.supplier = '$supplier' and a.active='1'";
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

	function getLandingCenterSupplier($supplier)
	{
		$qry	= "SELECT lc.id,lc.name FROM `m_landingcenter` lc left join m_supplier2center s2c on lc.id=s2c.center_id where s2c.supplier_id='$supplier'";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
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

	function checkProcurementStatus($numbers,$letters)
	{	
		$currentdate=date("Y-m-d");
		$qry="select id,billing_company_id,unitid from number_gen where alpha_code='$letters' and '$currentdate' between start_date and end_date  and '$numbers' between start_no and end_no and type='RG'";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function chkDuplicate($gatePass)
	{
		$qry="select id from t_rmreceiptgatepass where receipt_gatepass_number='$gatePass'";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?true:false;
	}

	
}
?>