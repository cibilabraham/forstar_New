<?php
class ProcurementGatePass
{  
	/****************************************************************
	This class deals with all the operations relating to Manage Challan
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function ProcurementGatePass(&$databaseConnect)
    	{
       		 $this->databaseConnect =&$databaseConnect;
	}
	
	
	function chkValidGatePassId($selDate)
	{
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";

		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
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
	  $qry = "select (count(*)) from m_rm_gate_pass";
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		//$qry = "select (count(*)) from t_rmprocurmentorder";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getValidGatePassId($selDate)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		 $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MG'";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getmaxGatePassId()
	{
	
		$qry = "select gate_pass_id from m_rm_gate_pass order by id desc limit 1";
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		//$qry = "select gatePass from t_rmprocurmentorder order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoGatePassId($selDate)
	{
		
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate')";
		
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
	function procurementGenerate($procurmentId)
	{
	$qry	= "update t_rmprocurmentorder set generated='1' where id='$procurmentId'";
		
		//echo $query;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	
	
	
	
	
	
	function getAllSealNos()
	{
		$qry	= "SELECT id,seal_number FROM m_seal_master WHERE change_status='Free' ";
		
		//echo $query;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Add procurment gatepass
	function addProcumentGatePassOrder($procurmentId,$procurmentGatePass,$outTime,$sealNoOut,$supervisor,$userId)
	{
	
		$qry	= "insert into m_rm_gate_pass(procurment_id,gate_pass_id,out_time,seal_out,supervisor, created_on, created_by) values('$procurmentId','$procurmentGatePass','$outTime','$sealNoOut','$supervisor', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function addLabour($lastId, $labour)
	{
		$qry	= "insert into m_gate_pass_labour(rm_gate_pass_id,labour, created_on) values('$lastId','$labour', Now())";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function addProcurmentSeal($lastId, $sealNumber)
	{
		$qry	= "insert into m_gate_pass_seal(rm_gate_pass_id,seal, created_on) values('$lastId','$sealNumber', Now())";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function updateSeal($sealNumber)
	{		
		$qry	= "update m_seal_master set purpose='OUT', change_status='Blocked' where id='$sealNumber'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	
		function updateSealOut($sealNoOut)
	{		
		$qry	= "update m_seal_master set purpose='OUT', change_status='Used' where id='$sealNoOut'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	function find($procurmentGatePassId)
	{
		 $qry	= "select id, gate_pass_id, out_time,out_seal,supervisor from m_rm_gate_pass where procurment_id=$procurmentGatePassId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getSeal($sealNoOutId)
	{
		 $qry	= "select id, seal_number from m_seal_master where id=$sealNoOutId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function fetchAllPagingRecords($fromDate, $tillDate,$offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		$qry	=	"select id, gate_pass_id, out_time,out_seal,supervisor,created_on,number_gen_id FROM m_rm_gate_pass 
					where created_on>='$fromDate' and created_on<='$tillDate' order by created_on limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	function getSealNumber($sealNoOutId)
	{		
		 $qry 	= "select id, seal_number from m_seal_master where id='$sealNoOutId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getSupervisor($supervisorId)
	{		
		 $qry 	= "select id, name from m_employee_master where id='$supervisorId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getSealNumbers($procurmentGatePassId)
	{		
		// $qry 	= "select id, seal from m_gate_pass_seal where rm_gate_pass_id='$procurmentGatePassId' order by seal asc";
		 $qry 	= "select a.id, a.in_seal from m_gate_pass_seal a where a.rm_gate_pass_id='$procurmentGatePassId' order by a.seal asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getLabours($procurmentGatePassId)
	{		
		 $qry 	= "select id, labour from m_gate_pass_labour where rm_gate_pass_id='$procurmentGatePassId' order by labour asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getProcurmentId($procurmentId)
	{		
		$qry 	= "select id,procurment_id from m_rm_gate_pass where id='$procurmentId'";
		 //$qry 	= "select id,procurment_id from m_rm_gate_pass where procurment_id='$procurmentId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function deleteProcurmentGatePass($procurementGatePassId)
	{
	 $qry	= " delete from m_rm_gate_pass where id=$procurementGatePassId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function deleteLabour($procurementGatePassId)
	{
		$qry	= " delete from m_gate_pass_labour where rm_gate_pass_id=$procurementGatePassId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function deleteSeal($procurementGatePassId)
	{
		$qry	= " delete from m_gate_pass_seal where rm_gate_pass_id=$procurementGatePassId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function findProGatePass($procurementIds)
	{
		$qry	= "select a.id,a.company,a.driver_name,a.vehicle_number,a.procurement_number,a.date_of_entry,b.supplier_group_name,
				   c.vehicle_number,
				   (select GROUP_CONCAT(id,'$$',chemical_id,'$$',required_quantity) from t_rmprocurmentchemical where rmProcurmentOrderId=a.id) as chemicals,
				   (select GROUP_CONCAT(id,'$$',equipment_id,'$$',required_quantity) from t_rmprocurmentequipment where rmProcurmentOrderId=a.id) as equipments,
					d.id as gate_pass_id 
				   from t_rmprocurmentorder a 
				   left join m_supplier_group b on a.suppler_group_name=b.id 
				   left join m_vehicle_master c on a.vehicle_number=c.id 
				   left join m_rm_gate_pass d on a.id = d.procurment_id 
				   where a.id in ($procurementIds)";
		return $this->databaseConnect->fetch_array($qry);
	}
	function getSealNo()
	{
		$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='SL'  AND '".date('Y-m-d')."' between  start_date and  end_date  and auto_generate='1' and challan_status!='1'  ORDER BY id ASC LIMIT 0,1 ";
		$result = $this->databaseConnect->fetch_array($qry);
		
		return $result;
	}
	function checkExists($tableName,$fieldName,$value)
	{
		$qry = "SELECT count(*) as total FROM ".$tableName." WHERE ".$fieldName."='".$value."'";
		$result = $this->databaseConnect->fetch_array($qry);
		
		return $result[0]['total'];
	}
	function addProcureMentOutSeal($procurement_id,$procurement_number,$out_time,$out_seal,$userId,$active,$number_gen_id,$supervisor,$labour)
	{
		$qry	= "insert into m_rm_gate_pass(procurment_id,gate_pass_id,out_time,out_seal,created_on,created_by,active,number_gen_id,supervisor) values
				  ('$procurement_id','$procurement_number','$out_time','$out_seal',Now(),'$userId','$active','$number_gen_id','$supervisor')";	
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			$this->updateCurrentNo($out_seal);
			$this->addLabourDetails($procurement_id,$labour);
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function updateProcureMentOutSeal($procurement_id,$procurement_number,$out_time,$out_seal,$userId,$active,$number_gen_id,$supervisor,$labour)
	{
		$qry	= "update m_rm_gate_pass set gate_pass_id = '$procurement_number',out_time = '$out_time',out_seal = '$out_seal',
				  created_on = Now(),created_by = '$userId',number_gen_id = '$number_gen_id',supervisor = '$supervisor'  
				  where procurment_id = '$procurement_id'";
		$insertStatus	=	$this->databaseConnect->updateRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			$this->updateCurrentNo($out_seal);
			$this->addLabourDetails($procurement_id,$labour);
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function getReceiptGatePassId($procurement_id)
	{
		$rm_gate_pass_id = '';
		$qry = "SELECT id FROM m_rm_gate_pass WHERE procurment_id = '$procurement_id'";
		$result	= $this->databaseConnect->getRecord($qry);
		
		if(sizeof($result) > 0)
		{
			$rm_gate_pass_id = $result[0];
		}
		return $rm_gate_pass_id;
	}
	function addProcureMentInSeal($procurement_id,$in_seals,$userId,$active,$number_gen_id)
	{
		// $qry = '';
		$insertStatus = '';
		$rm_gate_pass_id = $this->getReceiptGatePassId($procurement_id);
		foreach($in_seals as $in_seal)
		{
			$qry = "insert into m_gate_pass_seal(rm_gate_pass_id,in_seal,created_on,created_by,active,number_gen_id) values
					  ('$rm_gate_pass_id','$in_seal',Now(),'$userId','$active','$number_gen_id');";
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		}	
		if(isset($in_seal))
		{
			$this->updateCurrentNo($in_seal);
		}
		// echo $qry;
		// echo '<br/>';
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function updateProcurementEquipment($id,$issued_quantity,$difference_quantity)
	{
		$qry	= "update t_rmprocurmentequipment set issued_quantity = '$issued_quantity',	difference_quantity = '$difference_quantity' 
				   where id = '$id'";	
		$insertStatus	=	$this->databaseConnect->updateRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function updateProcurementChemical($id,$issued_quantity,$difference_quantity,$userId,$loginTime)
	{
		$qry	= "update t_rmprocurmentchemical set issued_quantity = '$issued_quantity',	difference_quantity = '$difference_quantity' 
				   where id = '$id'";	
		$insertStatus	=	$this->databaseConnect->updateRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		$this->deleteSealAssigned($userId,$loginTime);
		return $insertStatus;
	}
	function getAvailableSealNosAll()
	{
		$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='SL' AND end_date >= '".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1 ";
		$result = $this->databaseConnect->fetch_array($qry);
		
		
		$seal_nos_array = array();
		if(sizeof($result) > 0)
		{
			$sql = "SELECT out_seal FROM m_rm_gate_pass WHERE number_gen_id = '".$result[0]['id']."' 
					UNION 
					SELECT in_seal FROM m_gate_pass_seal WHERE number_gen_id = '".$result[0]['id']."'  AND accepted_status = 0 
					UNION 
					SELECT seal_no FROM seal_assigned";
			$existsSealNos = $this->databaseConnect->fetch_array($sql);
			$existsSealNos = array_map('current', $existsSealNos);
			$start_no = (int) $result[0]['start_no'];
			$end_no   = (int) $result[0]['end_no'];
			for($i=$start_no;$i<=$end_no;$i++)
			{
				if(!in_array($i,$existsSealNos))
				{
					$seal_nos_array[] = $i;
				}
			}
		}
		return $seal_nos_array;
	}
	function getAvailableSealNos($sealNoFrom)
	{
		$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='SL' AND end_date >= '".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1 ";
		$result = $this->databaseConnect->fetch_array($qry);
		
		
		$seal_nos_array = array();
		if(sizeof($result) > 0)
		{
			/*$sql = "SELECT out_seal FROM m_rm_gate_pass WHERE number_gen_id = '".$result[0]['id']."' 
					UNION 
					SELECT in_seal FROM m_gate_pass_seal WHERE number_gen_id = '".$result[0]['id']."' AND accepted_status = 0 
					UNION 
					SELECT seal_no FROM seal_assigned WHERE number_gen_id = '".$result[0]['id']."'";*/
			$sql = "SELECT out_seal FROM m_rm_gate_pass WHERE number_gen_id = '".$result[0]['id']."' 
					UNION 
					SELECT in_seal FROM m_gate_pass_seal WHERE number_gen_id = '".$result[0]['id']."' AND accepted_status != 2 
					UNION 
					SELECT seal_no FROM seal_assigned WHERE number_gen_id = '".$result[0]['id']."'";		
			$existsSealNos = $this->databaseConnect->fetch_array($sql);
			$existsSealNos = array_map('current', $existsSealNos);
			$start_no = (int) $result[0]['start_no'];
			$end_no   = (int) $result[0]['end_no'];
			$k = 0;
			for($i=$start_no;$i<=$end_no;$i++)
			{
				if($k == 50)
				{
					break;
				}
				if(!in_array($i,$existsSealNos))
				{
					$seal_nos_array[] = $i;
					$k++;
				}
			}
		}
		//print_r($seal_nos_array);
		return $seal_nos_array;
	}
	function addSealAssigned($number_gen_id,$seal_no,$user_id,$login_time)
	{
		$qry	= "insert into seal_assigned(number_gen_id,seal_no,user_id,login_time) values
				  ('$number_gen_id','$seal_no','$user_id','$login_time')";	
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function deleteSealAssigned($user_id,$login_time)
	{
		$qry	= " delete from seal_assigned where user_id='$user_id' AND login_time='$login_time'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updateCurrentNo($seal_no)
	{
		$qry	= "update number_gen set current_no = '$seal_no' WHERE type='SL' AND end_date >= '".date('Y-m-d')."'";	
		$insertStatus	=	$this->databaseConnect->updateRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	function checkSealUsed($checkSeals,$alpha,$id)
	{
		
		$qry = "SELECT id,start_no,end_no FROM number_gen WHERE type='SL' AND '".date('Y-m-d')."' between  start_date and  end_date  and auto_generate='1' and challan_status!='1' and alpha_code='$alpha' and id='$id'";
		
		echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		
		$seal_nos_array = array();
		$returnMessage = '';
		if(sizeof($result) > 0)
		{
			$start_no = $result[1];
			$end_no   = $result[2];
			$sql = "SELECT out_seal FROM m_rm_gate_pass WHERE number_gen_id = '".$result[0]."' 
					UNION 
					SELECT in_seal FROM m_gate_pass_seal WHERE number_gen_id = '".$result[0]."' AND accepted_status = 0 ";
			$existsSealNos = $this->databaseConnect->getRecords($sql);
			$existsSealNos = array_map('current', $existsSealNos);
			foreach($checkSeals as $sealNo)
			{
				if(in_array($sealNo,$existsSealNos))
				{
					$returnMessage = "Seal no (".$sealNo.") already used ";
					break;
				}
				else if($sealNo < $start_no || $sealNo > $end_no)
				{
					$returnMessage = "Seal no (".$sealNo.") not available. Seal number must in between ".$start_no." - ".$end_no;
					break;
				}
			}
			
		}
		
		return $returnMessage;
	}





	/*function checkSealUsed($checkSeals)
	{
		//$qry = "SELECT id,start_no,end_no FROM number_gen WHERE type='SL' AND end_date >= '".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1 ";
		$qry = "SELECT id,start_no,end_no FROM number_gen WHERE type='SL' AND end_date >= '".date('Y-m-d')."' ORDER BY id ASC";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		
		$seal_nos_array = array();
		$returnMessage = '';
		if(sizeof($result) > 0)
		{
			$start_no = $result[1];
			$end_no   = $result[2];
			$sql = "SELECT out_seal FROM m_rm_gate_pass WHERE number_gen_id = '".$result[0]."' 
					UNION 
					SELECT in_seal FROM m_gate_pass_seal WHERE number_gen_id = '".$result[0]."' AND accepted_status = 0 ";
			$existsSealNos = $this->databaseConnect->getRecords($sql);
			$existsSealNos = array_map('current', $existsSealNos);
			foreach($checkSeals as $sealNo)
			{
				if(in_array($sealNo,$existsSealNos))
				{
					$returnMessage = "Seal no (".$sealNo.") already used ";
					break;
				}
				else if($sealNo < $start_no || $sealNo > $end_no)
				{
					$returnMessage = "Seal no (".$sealNo.") not available. Seal number must in between ".$start_no." - ".$end_no;
					break;
				}
			}
			
		}
		
		return $returnMessage;
	}*/
	function addLabourDetails($procurement_id,$labours)
	{
		$qry = "SELECT id FROM m_rm_gate_pass WHERE procurment_id='$procurement_id'";
		$result = $this->databaseConnect->getRecord($qry);
		$rm_gate_pass_id = $result[0];
		if(sizeof($labours) > 0)
		{
			foreach($labours as $labour)
			{
				$qry	= "insert into m_gate_pass_labour(rm_gate_pass_id,labour, created_on) values('$rm_gate_pass_id','$labour', Now())";			
				$insertStatus	=	$this->databaseConnect->insertRecord($qry);
			}
		}
		return true;
	}
	
	function lastSeal()
	{
		$qry	=	"SELECT id,in_Seal,accepted_status,number_gen_id FROM `m_gate_pass_seal` where in_seal!='' union SELECT id,out_seal,gate_pass_id,number_gen_id from m_rm_gate_pass where out_seal!='' order by in_Seal desc limit 1";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	 function getalphachar($alphacharacter)
	{
		$qry="select alpha_code FROM number_gen WHERE id='".$alphacharacter."'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getAvailableAlphaprefixSealNosAll_old()
	{
		$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='SL' AND '".date('Y-m-d')."' between  start_date and  end_date  and auto_generate='1' and challan_status!='1'  ORDER BY id ";
		$result	=	array();
		$result = $this->databaseConnect->getRecords($qry);
		$returnVal = '';
		//printr($result);
		if(sizeof($result) > 0)
		{
			$i = 0;
			$returnVal.= '<table width="100%">';
			//$returnVal.='<tr><td class="listing-head" colspan="4"> Available alpha prefix of seal numbers </td></tr>';
			$returnVal.='<tr>';
			foreach($result as $res)
			{	
			
				if($i%4 == 0)
				{
					$returnVal.= '</tr><tr>';
				}
				$returnVal.= '<td class="listing-head"><a href="javascript:void(0);" onclick="assignSeals('.$res[0].','.$res[1].','.$res[2].','.$res[4].');"> '.$res[3].'</a></td>';
				$i++;
			}
			$returnVal.= '</tr></table>';
		}
		return $returnVal;	
		//printr($returnVal);
	}
	function getAvailableAlphaprefixSealNosAllold11122014()
	{
		$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='SL' AND '".date('Y-m-d')."' between  start_date and  end_date  and auto_generate='1' and challan_status!='1'  ORDER BY id ";
		$result	=	array();
		$result = $this->databaseConnect->getRecords($qry);
		$returnVal = '';
		//printr($result);
		$returnVal.='<p><div id="tabs">';

		if(sizeof($result) > 0)
		{
			$i = 1;
			$returnVal.='<ul>';
			foreach($result as $res)
			{
				$returnVal.='<li><a href="#tabs-'.$i.'" >'.$res[3].'</a></li>';
				$i++;
			}
			$returnVal.='</ul>';
			$j = 1;
			foreach($result as $rest)
			{

					$sql1 = "SELECT out_seal FROM m_rm_gate_pass WHERE number_gen_id = '".$rest[0]."' 
					UNION 
					SELECT in_seal FROM m_gate_pass_seal WHERE number_gen_id = '".$rest[0]."' AND accepted_status != 2 
					UNION 
					SELECT seal_no FROM seal_assigned WHERE number_gen_id = '".$rest[0]."'";		
					$existsSealNos = $this->databaseConnect->fetch_array($sql1);
					$existsSealNos = array_map('current', $existsSealNos);
					$start_no = (int) $rest[1];
					$end_no   = (int) $rest[2];
					$k = 0;
					for($m=$start_no;$m<=$end_no;$m++)
					{
						if($k == 50)
						{
							break;
						}
						if(!in_array($m,$existsSealNos))
						{
							$seal_nos_array[$rest[0]][] = $m;
							$k++;
						}
					}
					
					//printr($seal_nos_array[$res[0]]);
					
					
			}	
			
			
			

			
			printr($seal_nos_array);
			foreach($seal_nos_array as $slno)
					{
						echo $slno;
						//$returnVal.='<div id="tabs-'.$j.'" style=" height:auto;">'.$slno.'</div>';
					}
					
					/*$returnVal.='<div id="tabs-'.$j.'" style=" height:auto;">';
						$returnVal.= '<table width="100%">';
								//$returnVal.='<tr><td class="listing-head" colspan="4"> Available alpha prefix of seal numbers </td></tr>';
								$returnVal.='<tr>';
								$l=0;
								foreach ($seal_nos_array as $slno)
								{	
								
									if($l%4 == 0)
									{
										$returnVal.= '</tr><tr>';
									}
									$returnVal.= '<td class="listing-head"><a href="javascript:void(0);" > '.$slno.'</a></td>';
									$l++;
								}
								$returnVal.= '</tr></table>';


						$returnVal.= '</div>';*/

						

				//$returnVal.='<div id="tabs-'.$j.'" style=" height:auto;">Big content'.$j.'...</div>';
				$j++;

		/*$returnVal.='<p>
			<div id="tabs">
			<ul>
			  <li><a href="#tabs-1">tab-1</a></li>
			  <li><a href="#tabs-2">tab-2</a></li>
			</ul>
			<div id="tabs-1" style=" height:auto;">Big content1...</div>
			<div id="tabs-2" style=" height:auto;">Big content2...</div>
		  </div>
		</p>';	*/
			
		}
		$returnVal.='</div></p>';
		
		return $returnVal;	
		
	}

	function getAvailableAlphaprefixSealNosAll($inputStatus,$row)
	{
		$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='SL' AND '".date('Y-m-d')."' between  start_date and  end_date  and auto_generate='1' and challan_status!='1'  ORDER BY id ";
		$result	=	array();
		$result = $this->databaseConnect->getRecords($qry);
		$returnVal = ""; $resultval="";
		//printr($result);
		$returnVal.="<p><div id='tabs'>";

		if(sizeof($result) > 0)
		{
			$i = 1;
			$returnVal.="<ul style='height:40px; '>";
			foreach($result as $res)
			{
				$returnVal.="<li  ><a href='#tabs-".$i."'  style=' background:none; height:14px'>".$res[3]."</a></li>";
					
				$seal=$this->getAllSealNo($i,$res[0],$res[1],$res[2],$res[3],$inputStatus,$row,$res[1],'1');
				$resultval.=$seal;
				$i++;
			}
			$returnVal.="</ul>";
			

		/*$returnVal.='<p>
			<div id="tabs">
			<ul>
			  <li><a href="#tabs-1">tab-1</a></li>
			  <li><a href="#tabs-2">tab-2</a></li>
			</ul>
			<div id="tabs-1" style=" height:auto;">Big content1...</div>
			<div id="tabs-2" style=" height:auto;">Big content2...</div>
		  </div>
		</p>';	*/
		$returnVal=$returnVal.$resultval;	
		}
		else
		{
			$returnVal.="<table><tr><td style='color:red' class='listing-item'>Please add/reset settings for seal number in manage challan</td></tr></table>";
		}

		$returnVal.="</div>
		</p>";
		//printr($returnVal);
		return 	$returnVal;
		
	}
	function getAllSealNo($i,$id,$startno,$endno,$alphacode,$inputStatus,$row,$startOriginal,$pageNo)
	{
		
		$result="";
		$sql1 = "SELECT out_seal FROM m_rm_gate_pass WHERE number_gen_id = '".$id."' 
					UNION 
					SELECT in_seal FROM m_gate_pass_seal WHERE number_gen_id = '".$id."' AND accepted_status != 2 
					UNION 
					SELECT seal_no FROM seal_assigned WHERE number_gen_id = '".$id."'";		
		$existsSealNos = $this->databaseConnect->fetch_array($sql1);
		$existsSealNos = array_map('current', $existsSealNos);
		$start_no = (int) $startno;
		$end_no   = (int) $endno;
		$existNum=count($existsSealNos);
		$num=($end_no-$startOriginal)+$existNum+1;
		//echo $num.','.$existNum.'<br/>';
		$numpage=ceil($num/52);
		//echo $numpage.','.$existNum.'<br/>';
		$k = 0;
			for($m=$start_no;$m<=$end_no;$m++)
			{
				if($k == 52)
				{
					break;
				}
				if(!in_array($m,$existsSealNos))
				{
					$seal_nos_array[] = $m;
					$k++;
				}
			}
				//$seal_nos_array;
		$result.="<div id='tabs-".$i."' style=' height:auto;'>";
		$result.= "<table width='100%'>";
		//$returnVal.='<tr><td class="listing-head" colspan="4"> Available alpha prefix of seal numbers </td></tr>';
		$result.="<tr>";
			$l=0;
			foreach ($seal_nos_array as $slno)
			{	
				if($l%4 == 0)
				{
					$result.= "</tr><tr>";
				}
				$result.= "<td class='listing-head'><a href='javascript:void(0);' onclick='assignSeals(".$id.",".$startno.",".$endno.",".$slno.",".$inputStatus.",".$row.",\"$alphacode\");' > ".$slno."</a></td>";
				$l++;
			}
		$result.= "</tr></table>
		<table width='100%'><tr>";
		$result.= "<td colspan='4' style='text-align:left' class='listing-item'>";
		//for($pageNo=1; $pageNo<=$numpage; $pageNo++)
		//{
			//if($pageNo=="") { $pageNo=1;}
			//$result.=$r.'|';
			
			if ($pageNo > 1)
			{
				$result.= "<a href='javascript:void(0);' onclick='getPrevious(".$i.",".$id.",".$seal_nos_array[0].",".$endno.",\"$alphacode\",".$inputStatus.",".$row.",".$startOriginal.",".$pageNo.");' class=\"link1\">Previous<<</a> ";
			}
			else
			{
   				$result.= '&nbsp;'; // we're on page one, don't print previous link
   			}
		$result.="</td>";
		$result.= "<td colspan='4' style='text-align:right' class='listing-item'>";
			if ($pageNo < $numpage)
			{
			
			$result.= " <a href='javascript:void(0);' onclick='getNext(".$i.",".$id.",".$seal_nos_array[0].",".$endno.",\"$alphacode\",".$inputStatus.",".$row.",".$startOriginal.",".$pageNo.");' class=\"link1\">Next>></a> ";
			}
			else
			{
			$result.= '&nbsp;'; // we're on the last page, don't print next link
			
			}
		//}
		$result.= "</td></tr></table>";
		
		$result.="</div>";
		return $result;
	}
}
?>

		