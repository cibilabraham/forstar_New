<?php
class DisplayRecord
{  
	/****************************************************************
	This class deals with all the operations relating to Display Settings 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DisplayRecord(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addDisplayRecord($noRec)
	{
		$qry	= "insert into s_displayrecord (no_records) values('".$noRec."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function addDisplayAgreementRec($displayRecordId,$certification,$termsConditions,$policyAgreement)
	{
		$qry	= "update  s_displayrecord set certified_agreement='".$certification."',terms_conditions='".$termsConditions."',law_policy_agreement='".$policyAgreement."' where id=$displayRecordId";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Get Record
	function find()
	{
		$qry = "select id,no_records,certified_agreement,terms_conditions,law_policy_agreement from s_displayrecord where id is not null ";
		//echo $qry;
		//die();
		return $this->databaseConnect->getRecord($qry);
	}
	
	function findInvPurchaseOrder()
	{
		$qry = "select id,termsandconditions,paymentterms from inventory_purchase_order";
		//echo $qry;
		//die();
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateDisplayRecord($displayRecordId,$noRec)
	{
		$qry	= " update s_displayrecord set no_records='$noRec' where id=$displayRecordId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	function findDisplayRecord()
	{
		$rec = $this->find();
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	# Update Default Tolerance level
	function updateDefaultYieldTolerance($defaultYieldTol)
	{
		$qry	= " update c_system set default_yield_tolerance='$defaultYieldTol'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function getDefaultYieldTolerance()
	{
		$qry = "select default_yield_tolerance from c_system";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	function getStockEntrystnum()
	{
		$qry = "select stock_start_num from c_system";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	function getSupplierstnum()
	{
		$qry = "select supplier_start_num from c_system";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}
	function updateDailyFrozenPackingSetDate($dfpSelDate)
	{
	$qry	= " update c_system set daily_frozenpacking_start_date='$dfpSelDate'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateStockstartNum($stNum)
	{
	$qry	= " update c_system set stock_start_num='$stNum'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateSupplierstartNum($stNum)
	{
	$qry	= " update c_system set supplier_start_num='$stNum'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	function getDefaultDFPDate()
	{
		$qry = "select daily_frozenpacking_start_date from c_system";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//echo $rec[0];
		return $rec[0];
	}
	
	function updateInventoryPurchaseOrder($termsConditionsinvpo,$paymenttermsinvpo){
		$qry	= " update inventory_purchase_order set termsandconditions='".$termsConditionsinvpo."',paymentterms='$paymenttermsinvpo'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
		
	}
	
	function addInventoryPurchaseOrder($termsConditionsinvpo,$paymenttermsinvpo){
		$qry	= "insert into inventory_purchase_order(termsandconditions,paymentterms) values('".$termsConditionsinvpo."','".$paymenttermsinvpo."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
		
	}
}