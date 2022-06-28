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
		$qry	= "select id, process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmReceiptGatePass order by date_Of_Entry desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
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
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id, process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmReceiptGatePass where date_Of_Entry>='$fromDate' and date_Of_Entry<='$tillDate' order by date_Of_Entry desc limit $offset,  $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get process type based on id 
	function findProcessType($processId)
	{
		$qry	=	"select id, process_type from m_lotid_process_type where id=$processId";
		return $this->databaseConnect->getRecord($qry);
	}
	# Get RMReceipt Gatepass based on id 
	function find($RMReceiptGatePassId)
	{
		$qry	= "select id,process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmReceiptGatePass where id=$RMReceiptGatePassId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, process_type,lot_Id, procurment_Gate_PassId,vehicle_Number,driver,in_Seal,result,seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,supplier_Challan_Date,date_Of_Entry from t_rmReceiptGatePass where date_Of_Entry>='$fromDate' and date_Of_Entry<='$tillDate' order by date_Of_Entry desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
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
		$qry = "SELECT a.*,b.gate_pass_id,c.name as company_name,d.name as unit_name 
				FROM t_rmreceiptgatepass a 
		        LEFT JOIN m_rm_gate_pass b ON b.procurment_id = a.procurment_Gate_PassId 
				LEFT JOIN m_billing_company c ON c.id = a.Company_Name 
				LEFT JOIN m_plant d ON d.id = a.unit ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	
	# Get all the procurement id for drop down
	function getAllProcurement()
	{
		$qry = "SELECT procurment_id,gate_pass_id FROM m_rm_gate_pass ";
		
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
	
	# Get all the unit for drop down
	function getAllUnit()
	{
		$qry = "SELECT id,name FROM m_plant ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
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
	
	# Get details for fill the form 
	function getFormFillData($procurement_id)
	{
		$qry = "SELECT a.id,a.company,a.date_of_entry,a.vehicle_number as vehicle_id,b.vehicle_number,
				a.driver_name as driver_id,c.name_of_person,
				(SELECT GROUP_CONCAT(labour) FROM m_gate_pass_labour WHERE rm_gate_pass_id=d.id) as labours,
				(SELECT GROUP_CONCAT(seal) FROM m_gate_pass_seal WHERE rm_gate_pass_id=d.id) as seals,
				e.seal_number,d.seal_out 
				FROM t_rmprocurmentorder a 
				LEFT JOIN m_vehicle_master b on b.id = a.vehicle_number 
				LEFT JOIN m_driver_master c ON c.id = a.driver_name 
				LEFT JOIN m_rm_gate_pass d ON d.procurment_id = a.id 
				LEFT JOIN m_seal_master e ON e.id = d.seal_out 
				WHERE a.id = '".$procurement_id."' ";
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
	
	#Get a in seal drop down datas
	function getInsealData($procurement_id)
	{
	$qry="select a.id,a.seal_number FROM m_seal_master a left join m_gate_pass_seal b on b.seal=a.id 
					left join m_rm_gate_pass c on c.id=b.rm_gate_pass_id where c.procurment_id ='$procurement_id'
					and (a.change_status='Free' OR a.change_status='Blocked'  
				OR a.id = (SELECT in_Seal FROM t_rmreceiptgatepass WHERE 
				procurment_Gate_PassId = '".$procurement_id."')) " ;
				
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
	
	#Add a rm receipt gate pass details
	function addRmReceiptGatePassDetails($data,$userId)
	{
		$qry	=	"insert into t_rmreceiptgatepass (procurment_Gate_PassId,vehicle_Number,driver,
					 vehicle_number_other,driver_name_other,in_Seal,result,
					 seal_No,out_Seal,verified,labours,Company_Name,unit,supplier_Challan_No,
					 supplier_Challan_Date,date_Of_Entry,created_on,created_by,active) values
					 ('".$data['procurment_Gate_PassId']."','".$data['vehicle_id']."','".$data['driver_id']."',
					 '".$data['vehicle_Number']."','".$data['driver']."',
					 '".$data['in_Seal']."','".$data['result']."','".$data['seal_No']."', '".$data['out_seal_id']."',
					 '".$data['verified']."','".$data['labours']."','".$data['Company_Name']."','".$data['unit']."',
					 '".$data['supplier_Challan_No']."', '".$data['supplier_Challan_Date']."', '".$data['date_Of_Entry']."',
					 '".date('Y-m-d')."','".$userId."','1')";

		//echo $qry;seal_No
		if(isset($data['in_Seal']) && $data['in_Seal'] != '')
		{
			$qry	=	"UPDATE m_seal_master SET change_status = 'Used' WHERE id IN('".$data['in_Seal']."') ";
		
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	#Update a rm receipt gate pass details
	function updateRmReceiptGatePassDetails($data,$userId)
	{
		$qry	=	"UPDATE t_rmreceiptgatepass SET procurment_Gate_PassId = '".$data['procurment_Gate_PassId']."',
					 vehicle_Number = '".$data['vehicle_id']."',driver = '".$data['driver_id']."',
					 vehicle_number_other = '".$data['vehicle_Number']."',driver_name_other = '".$data['driver']."',
					 in_Seal = '".$data['in_Seal']."',result = '".$data['result']."',
					 seal_No = '".$data['seal_No']."',out_Seal = '".$data['out_seal_id']."',
					 verified = '".$data['verified']."',labours = '".$data['labours']."',
					 Company_Name = '".$data['Company_Name']."',unit = '".$data['unit']."',
					 supplier_Challan_No = '".$data['supplier_Challan_No']."',supplier_Challan_Date = '".$data['supplier_Challan_Date']."',
					 date_Of_Entry = '".$data['date_Of_Entry']."',created_by = '".$userId."' WHERE id = '".$data['id']."' ";

		//echo $qry;
		$updateStatus	= $this->databaseConnect->updateRecord($qry);		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
	
	# Check lot id availability
	function checkLotIdAvailable()
	{
		$lastLotIdNumber = 0;
		$sql = "SELECT lot_Id FROM t_rmreceiptgatepass ORDER BY lot_Id DESC LIMIT 0,1";
		$resultNo	=	$this->databaseConnect->fetch_array($sql);
		if(sizeof($resultNo) > 0)
		{
			$lastLotIdNumber = explode('F',$resultNo[0]['lot_Id']);
			if(sizeof($lastLotIdNumber) == 2)
			{
				$lastLotIdNumber = $lastLotIdNumber[1];
			}
		}
		// echo $lastLotIdNumber;
		$qry = "SELECT count(*) as total FROM number_gen 
                WHERE type = 'LF' AND alpha_code = 'F' AND (end_date <= ".date('Y-m-d')." 
			     OR end_no <= '".$lastLotIdNumber."')  ";
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
		$lotId = 'F'.$lastNumber;
		
		$qry	=	"UPDATE t_rmreceiptgatepass SET lot_Id = '".$lotId."' WHERE id = '".$generateLotID."' ";
		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
	
	# Free blocked seals to free
	function freeSeals($blockIds)
	{
		$qry	=	"UPDATE m_seal_master SET status = 'Free' WHERE id IN('".$blockIds."') ";
		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
}
?>