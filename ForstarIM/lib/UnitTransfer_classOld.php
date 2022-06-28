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
	function addUnitTransfer($rmlotId, $supplierDetails, $currentUnitName, $currentProcessingStage, $unitName,$processType,$lotId ,$userId)
	{
		$qry	= "insert into t_unitTransfer(rm_lot_Id, supplier_Details,current_Unit,current_Stage,unit_Name,process_Type,new_lot_Id, created_on, created_by) values('$rmlotId', '$supplierDetails','$currentUnitName','$currentProcessingStage','$unitName','$processType','$lotId', Now(),'$userId')";
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
	function updateUnitTransfer($unitTransferDataId, $rmlotId, $supplierDetails,$currentUnitName,$currentProcessingStage,$unitName,$processType,$lotId)
	{
		$qry	= " update t_unitTransfer set rm_lot_Id='$rmlotId', supplier_Details='$supplierDetails', current_Unit='$currentUnitName', 	current_Stage='$currentProcessingStage', unit_Name='$unitName',process_Type='$processType',new_lot_Id='$lotId' where id=$unitTransferDataId";

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
		$qry	= "select id, rm_lot_Id, supplier_Details,current_Unit,current_Stage,unit_Name,process_Type,new_lot_Id from t_unitTransfer where active='1' order by created_on desc ";
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
	
}
?>