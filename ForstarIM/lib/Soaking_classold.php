<?php
class Soaking
{  
	
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Soaking(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	# Filter available quantity
	function getAvailableQuantity($rmLotId)
	{
		//$qry="select supplier_Challan_No from t_rmreceiptgatepass where id='$rmLotId'";
		$qry="select total_preprocess_qty from t_dailypreprocess_entries where lot_id='$rmLotId'";
		//echo $qry;
		
		
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	}
	
	#Add soaking
	function addSoaking($rmlotId, $currentProcessingStage, $supplierDetails, $availableQuantity, $soakInCount,$soakInQuantity,$soakInTime,$soakOutCount,$soakOutQunatity,$soakOutTime,$temperature,$gain,$chemcalUsed,$chemcalQty ,$userId)
	{
		$qry	= "insert into t_soaking(rm_lot_Id,processing_Stage, supplier_Details,available_Qty,soak_In_Count,soak_In_Qty,soak_In_Time,soak_Out_Count,soak_Out_Qty,soak_Out_Time,temperature,gain,chemcal_Used,chemcal_Qty, created_on, created_by) values('$rmlotId','$currentProcessingStage', '$supplierDetails', '$availableQuantity', '$soakInCount', '$soakInQuantity','$soakInTime','$soakOutCount' ,'$soakOutQunatity','$soakOutTime','$temperature','$gain','$chemcalUsed','$chemcalQty', Now(),'$userId')";
		// $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	# Returns all soaking data
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id, rm_lot_Id,processing_Stage, supplier_Details,available_Qty,soak_In_Count,soak_In_Qty,soak_In_Time,soak_Out_Count,soak_Out_Qty,soak_Out_Time,temperature,gain,chemcal_Used,chemcal_Qty from t_soaking where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get soaking data based on id 
	function find($soakingId)
	{
		$qry	= "select * from t_soaking where id=$soakingId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update  a  soaking
	function updateSoaking($soakingDataId, $rmlotId, $currentProcessingStage,$supplierDetails,$availableQuantity,$soakInCount,$soakInQuantity,$soakInTime, $soakOutCount,$soakOutQunatity,$soakOutTime,$temperature,$gain,$chemcalUsed,$chemcalQty)
	{
		$qry	= " update t_soaking set rm_lot_Id='$rmlotId', processing_Stage='$currentProcessingStage', supplier_Details='$supplierDetails', available_Qty='$availableQuantity', soak_In_Count='$soakInCount',soak_In_Qty='$soakInQuantity',soak_In_Time='$soakInTime',soak_Out_Count='$soakOutCount',soak_Out_Qty='$soakOutQunatity',soak_Out_Time='$soakOutTime',temperature='$temperature',gain='$gain',chemcal_Used='$chemcalUsed',chemcal_Qty='$chemcalQty' where id=$soakingDataId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete a soaking Data
	function deleteSoaking($soakingDataId)
	{
		$qry	= " delete from t_soaking where id=$soakingDataId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, rm_lot_Id,processing_Stage, supplier_Details,available_Qty,soak_In_Count,soak_In_Qty,soak_In_Time,soak_Out_Count,soak_Out_Qty,soak_Out_Time,temperature,gain,chemcal_Used,chemcal_Qty from t_soaking where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all soaking Data
	function fetchAllRecords()
	{
		$qry	= "select id, rm_lot_Id,processing_Stage, supplier_Details,available_Qty,soak_In_Count,soak_In_Qty,soak_In_Time,soak_Out_Count,soak_Out_Qty,soak_Out_Time,temperature,gain,chemcal_Used,chemcal_Qty from t_soaking order by created_on desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllSoaking($soakingDataId)
	{
		$qry	= "select id, rm_lot_Id,processing_Stage, supplier_Details,available_Qty,soak_In_Count,soak_In_Qty,soak_In_Time,soak_Out_Count,soak_Out_Qty,soak_Out_Time,temperature,gain,chemcal_Used,chemcal_Qty from t_soaking where id='$soakingDataId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
}
?>