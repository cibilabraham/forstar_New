<?php
class UnitTransfer
{  
	/****************************************************************
	This class deals with all the operations relating to Unit Transfer 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function UnitTransfer(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	# Filter Supplier Detail
	function getSupplierDetail($rmLotId)
	{
		//$qry="select supplier_Challan_No from t_rmreceiptgatepass where id='$rmLotId'";
		$qry="select supplier_Details from t_unittransfer where id='$rmLotId'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	# Filter unit List
	function getUnit($rmLotId)
	{
		//$qry="SELECT a.id, a.unit, b.name FROM t_rmreceiptgatepass a JOIN m_plant b ON a.unit = b.id WHERE a.id ='$rmLotId' ORDER BY name ASC";
		$qry="SELECT a.id, a.unit_Name, b.name FROM t_unittransfer a JOIN m_plant b ON a.unit_Name = b.id WHERE a.id ='$rmLotId' ORDER BY name ASC";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	
	# Filter processing type List
	function getProcessingStage($rmLotId)
	{
		//$qry="SELECT a.id, a.process_type, b.process_type FROM t_rmreceiptgatepass a JOIN m_lotid_process_type b ON a.process_type = b.id WHERE a.id ='$rmLotId' ORDER BY b.process_type ASC";
		$qry="SELECT a.id, a.process_Type, b.process_type FROM t_unittransfer a JOIN m_lotid_process_type b ON a.process_Type = b.id WHERE a.id ='$rmLotId' ORDER BY b.process_type ASC";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	
	
	function getFirstLot($rmLotId)
	{
		//$qry="select supplier_Challan_No from t_rmreceiptgatepass where id='$rmLotId'";
		$qry="select first_lot_id from t_unittransfer where id='$rmLotId'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	
	/*# Filter Unit
	function getUnit($rmLotId)
	{
		$qry="select unit from t_rmreceiptgatepass where id='$rmLotId'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	# Filter Unit Name
	function getUnitName($unit)
	{
		$qry="select name from m_plant where id='$unit'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	# Filter Processing Stage
	function getProcessingStage($rmLotId)
	{
		$qry="select process_type from t_rmreceiptgatepass where id='$rmLotId'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	# Filter Processing Name
	function getProcessingName($processingStage)
	{
		$qry="select process_type from m_lotid_process_type where id='$processingStage'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}*/
	
	#Add Unit Transfer
	function addUnitTransfer($rmlotId, $supplierDetails, $currentUnitName, $currentProcessingStage, $unitName,$processType,$lotId ,$firstLotId,$userId)
	{
		$qry	= "insert into t_unitTransfer(rm_lot_Id, supplier_Details,current_Unit,current_Stage,unit_Name,process_Type,new_lot_Id,first_lot_id, created_on, created_by) values('$rmlotId', '$supplierDetails','$currentUnitName','$currentProcessingStage','$unitName','$processType','$lotId','$firstLotId', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	# Returns all unit Transfer
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id, rm_lot_Id, supplier_Details,current_Unit,current_Stage,unit_Name,process_Type,new_lot_Id from t_unitTransfer where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get unit transfer data based on id 
	function find($unitTransferDataId)
	{
		$qry	= "select * from t_unitTransfer where id=$unitTransferDataId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update  a unit Transfer Data
	function updateUnitTransfer($unitTransferDataId, $rmlotId, $supplierDetails,$currentUnitName,$currentProcessingStage,$unitName,$processType,$lotId,$firstLotId)
	{
		$qry	= " update t_unitTransfer set rm_lot_Id='$rmlotId', supplier_Details='$supplierDetails', current_Unit='$currentUnitName', 	current_Stage='$currentProcessingStage', unit_Name='$unitName',process_Type='$processType',new_lot_Id='$lotId',first_lot_id='$firstLotId' where id=$unitTransferDataId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete a unit transfer Data
	function deleteUnitTransfer($unitTransferDataId)
	{
		$qry	= " delete from t_unitTransfer where id=$unitTransferDataId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Returns all unit Transfer Data
	function fetchAllRecords()
	{
		$qry	= "select id, rm_lot_Id, supplier_Details,current_Unit,current_Stage,unit_Name,process_Type,new_lot_Id from t_unitTransfer order by created_on desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllUnitTransfer($untTransferDataId)
	{
		$qry	= "select id, rm_lot_Id, supplier_Details,current_Unit,current_Stage,unit_Name,process_Type,new_lot_Id from t_unitTransfer where id='$untTransferDataId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, rm_lot_Id, supplier_Details,current_Unit,current_Stage,unit_Name,process_Type,new_lot_Id from t_unitTransfer where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get lot based on id 
	function findLot($lotId)
	{
		$qry	=	"select id, new_lot_Id from t_unittransfer where id=$lotId";
		return $this->databaseConnect->getRecord($qry);
	}

	function getProcessingStages($processType)
	{
		$qry	=	"select process_type from m_lotid_process_type where id=$processType";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function chkValidLotId($selDate,$processType)
	{
		$selDate=Date('Y-m-d');
		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//$qry	="select id,process_type,number_from, number_to from m_lotid_generate where process_type='$processType' and  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	
	function getAlphaCode($processType)
	{
		
		//$qry = "select alpha_code_prefix from m_lotid_generate where  process_type='$processType'";
		$qry = "select alpha_code from number_gen where type='$processType'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function checkLotIdDisplayExist($process)
	{
	  $qry = "select (count(*)) from t_unittransfer where  process_Type='$process'";
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		//$qry = "select (count(*)) from t_rmprocurmentorder";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getmaxLotId($processType)
	{
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		$qry = "select new_lot_Id from t_unitTransfer where  process_Type='$processType' order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoLotId($selDate,$processType)
	{
		
		$selDate=Date('Y-m-d');
		//$qry	= "select number_to from m_lotid_generate where process_type='$processType' and date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		$qry	= "select end_no from number_gen where type='$processType' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate')";
		
		
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	function getValidLotId($selDate,$processType)
	{
		//$billingCompany=0;
		$selDate=Date('Y-m-d');
		$qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='$processType'";
		//$qry	= "select number_from from m_lotid_generate where  process_type='$processType' and date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	
}
?>