<?php
class ManageGatePass
{
	/****************************************************************
	This class deals with all the operations relating to Manage Gate Pass
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ManageGatePass(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add New gate pass
	function addGatePass($gatePassNo, $partyAddress, $consignmentDetails, $vehicleNo, $userId,$company,$unit,$numbergenId)
	{
		$selDate = date("Y-m-d");
		$gpYear	 = date("Y", strtotime($selDate));

		$qry = "insert into t_gate_pass (gate_pass_no, gp_year, gpass_date, to_address, consignment_details, vehicle_no, created, createdby,company_id,unit_id,number_gen_id) values('$gatePassNo', '$gpYear', '$selDate', '$partyAddress', '$consignmentDetails', '$vehicleNo', NOW(), '$userId','$company','$unit','$numbergenId')";
		echo $qry;
		exit;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}	

	# --------- Gate Pass Number starts ----
	# Get Next Proforma Invoice Number	
	function getNextGatePassNo()
	{			
		$selDate = date("Y-m-d");
		$soYear	 = date("Y", strtotime($selDate));
		$gatePassNum = $this->getMaxGatePassNum($soYear);
		$validGatePassNum = $this->getValidGatePassNum($gatePassNum, $selDate);		
		if ($validGatePassNum) return $gatePassNum+1;
		else return $this->getCurrentGatePassNum($selDate);
	}

	function getMaxGatePassNum($soYear)
	{
		$qry = " select max(gate_pass_no) from t_gate_pass where gate_pass_no!=0 and gp_year='$soYear' ";
		//echo "<br>$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	function getValidGatePassNum($gatePassNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='GP' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO' and start_no<='$gatePassNum' and end_no>='$gatePassNum' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getCurrentGatePassNum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='GP' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO'";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
	# ----- Gate Pass Ends Here -----------------------------


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $fromDate, $tillDate)
	{
		$whr = " a.gpass_date>='$fromDate' and a.gpass_date<='$tillDate' ";
	
		$orderBy  = "  a.gate_pass_no desc, b.so desc ";

		$limit 	  = " $offset,$limit";

		$qry = "select a.id, a.so_id, b.invoice_type, b.so, b.proforma_no, b.sample_invoice_no, c.name, a.confirm_status, a.gate_pass_no, a.user_id, a.editing_time, TIME_TO_SEC(TIMEDIFF(NOW(), a.editing_time)) as diffTinS, b.complete_status from t_gate_pass a left join t_salesorder b on a.so_id=b.id left join m_distributor c on b.distributor_id=c.id";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;	
	
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	# Returns all Records
	function fetchAllRecords($fromDate, $tillDate)
	{
		$whr = " a.gpass_date>='$fromDate' and a.gpass_date<='$tillDate' ";
		
		$orderBy  = "  a.gate_pass_no desc, b.so desc ";
	
		$qry = "select a.id, a.so_id, b.invoice_type, b.so, b.proforma_no, b.sample_invoice_no, c.name, a.confirm_status, a.gate_pass_no, a.user_id, a.editing_time, TIME_TO_SEC(TIMEDIFF(NOW(), a.editing_time)), b.complete_status from t_gate_pass a left join t_salesorder b on a.so_id=b.id left join m_distributor c on b.distributor_id=c.id";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;				
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}


	# Get a Record based on id
	function find($gatePassId)
	{		
		$qry = "select a.id, a.so_id, b.invoice_type, b.so, b.proforma_no, b.sample_invoice_no, c.name, a.confirm_status, a.gate_pass_no, a.to_address, a.consignment_details, a.vehicle_no,a.company_id,a.unit_id,a.number_gen_id from t_gate_pass a left join t_salesorder b on a.so_id=b.id left join m_distributor c on b.distributor_id=c.id where a.id='$gatePassId' ";
		//echo "Edit==>$qry";
		return $this->databaseConnect->getRecord($qry);
	}


	# Update  a  Record
	function updateGatePass($gatePassId, $partyAddress, $consignmentDetails, $vehicleNo, $userId, $gPassConfirm, $gatePassNo,$company,$unit,$numbergenId)
	{
		$selDate = date("Y-m-d");
		$gpYear	 = date("Y", strtotime($selDate));

		//if ($gPassConfirm=='C') {
		//	$uptdQry = " , gate_pass_no='$gatePassNo' ";
		//}
		
		$qry = "update t_gate_pass set to_address='$partyAddress', consignment_details='$consignmentDetails', vehicle_no='$vehicleNo', confirm_status='$gPassConfirm', modified=NOW(), modifiedby='$userId', gp_year='$gpYear', gpass_date='$selDate',company_id='$company',unit_id='$unit',number_gen_id='$numbergenId', gate_pass_no='$gatePassNo'  $uptdQry where id=$gatePassId ";

		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	function updateSOGatePass($selSOId, $uptdValue)
	{
		$qry = "update t_salesorder set gpass_confirm='$uptdValue' where id='$selSOId' ";
		//echo $qry;
		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}



	# Delete Packing instruction
	function deleteGatePass($gatePassId)
	{
		$qry	= " delete from t_gate_pass where id='$gatePassId'";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Change SO Gate Pass Status
	function changeSOStatusRec($soId)
	{
		$qry = "update t_salesorder set gpass_gen='N', gpass_confirm='N' where id='$soId' ";
		//echo $qry;
		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}


	# Returns all Sales Order
	function getSOMainRec($selSOId)
	{
		$qry = "select a.so, a.distributor_id, a.state_id, a.city_id, a.area_id, a.invoice_type, a.proforma_no, a.sample_invoice_no from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.id='$selSOId' order by a.so desc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return array($result[0][0], $result[0][1], $result[0][2], $result[0][3], $result[0][4], $result[0][5], $result[0][6], $result[0][7]);
	}


	function getGPEditId($uSOId)
	{
		$qry = " select id from t_gate_pass where so_id='$uSOId'";
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}

	# Gate Pass Records (Return=>Gate Pass No, To, C Details, Vechile No, Status, gpass date)
	function getGatePassRec($selSOId, $gatePassId)
	{
		$qry = " select gate_pass_no, to_address, consignment_details, vehicle_no, confirm_status, gpass_date from t_gate_pass where (id='$gatePassId' or so_id='$selSOId')";
		$result	= $this->databaseConnect->getRecords($qry);
		return array($result[0][0], $result[0][1], $result[0][2], $result[0][3], $result[0][4], $result[0][5]);
	}


	# Modified Time Updating
	function chkMGPRecModified($gatePassId)
	{
		$qry = " select a.user_id, b.username from t_gate_pass a, user b  where a.user_id=b.id and a.id='$gatePassId' and a.user_id!=0 ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][1]:false;
	}

	function updateMGPModifiedRec($gatePassId, $userId, $mode)
	{
		if ($mode=='E') $uptdQry = "editing_time=NOW()";
		else $uptdQry = "editing_time=0";
		
		$qry = " update t_gate_pass set user_id='$userId', $uptdQry where id=$gatePassId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	# ------------------------------- Time Update Ends here

	# Find Gate Pass Main ID
	function gatePassMainId($selSOId)
	{
		$qry = "select a.id from t_gate_pass a where a.so_id='$selSOId' ";
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}
	
	
	###---------------------------------- CODE FOR GENERATE	PO ID STARTS----------------------------------------------------------
	function chkValidGatePassId($selDate,$compId,$invUnit)
	{
		$qry	="select id,start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='MGP' and billing_company_id='$compId' and unitid='$invUnit'  or date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='MGP' and billing_company_id='$compId' and unitid='0'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}

	function getAlphaCode($id)
	{
		$qry = "select alpha_code from number_gen where type='MGP' and id='$id'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}

	function checkGatePassDisplayExist($numbergen)
	{
		$qry = "select (count(*)) from  t_gate_pass where number_gen_id='$numbergen'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getmaxGatePassId($numbergen)
	{
		$qry = "select gate_pass_no from t_gate_pass where number_gen_id='$numbergen' order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getValidendnoGatePassId($selDate,$companyId,$unitId)
	{
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MGP' and billing_company_id='$companyId' and unitid='$unitId' OR date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MGP' and billing_company_id='$companyId' and unitid='0' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getValidGatePassId($selDate,$companyId,$unitId)
	{
		$qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MGP' and company_id='$companyId' and unit_id='$unitId' OR date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MGP' and company_id='$companyId' and unit_id='0'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
###----------------------------------CODE FOR GENERATE	PO ID ENDS----------------------------------------------------------
	

}