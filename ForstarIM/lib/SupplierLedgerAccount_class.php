<?php
class SupplierLedgerAccount
{  
	/****************************************************************
	This class deals with all the operations relating to Supplier Ledger Account
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SupplierLedgerAccount(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	/*
	#Insert Record
	function addDistributorAccount($selDate, $selDistributor, $amount, $debit, $description, $userId, $soId, $claimId)
	{
		$qry = "insert into t_distributor_ac (select_date, distributor_id, amount, cod, description, created, createdby, so_id, claim_id) values ('$selDate', '$selDistributor', '$amount', '$debit', '$description', NOW(), '$userId', '$soId', '$claimId')";
		//echo $qry;
		$insertStatus	= 	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Upate Distributor Account 
			$this->manageDistributorAccount($selDistributor, $debit, $amount);
		}
		else 			$this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.select_date, a.distributor_id, a.amount, a.cod, a.description, b.name from t_distributor_ac a, m_distributor b where a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' order by a.select_date desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Returns all Records
	function fetchDateRangeRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, a.select_date, a.distributor_id, a.amount, a.cod, a.description, b.name from t_distributor_ac a, m_distributor b where a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' order by a.select_date desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	

	
	# Get a Rec
	function find($distributorAccountId)
	{
		$qry = "select  id, select_date, distributor_id, amount, cod, description from t_distributor_ac where id=$distributorAccountId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Delete a Rec
	function deleteDistributorAccount($distributorAccountId)
	{
		$qry	= " delete from t_distributor_ac where id=$distributorAccountId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}	

	# Update  a  Record
	function updateDistributorAccount($distributorAccountId, $selDate, $selDistributor, $amount, $debit, $amtDescription)
	{
		$qry = "update t_distributor_ac set select_date='$selDate', distributor_id='$selDistributor', amount='$amount', cod='$debit', description='$amtDescription' where id='$distributorAccountId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	*/
	/*
		Update amount in Master 
		According to Ledger a/c 
			Debit -> Sales
			credit -> Receipt
		If Credit the Out amount will decrease
		If Debit the Out amount will Increase
			Account         Type Debit          Credit
			Assets           Increases          Decreases
			Liabilities      Decreases          Increases
			Income           Decreases          Increases
			Expenses         Increases          Decreases
	*/
	/*
	function manageDistributorAccount($selDistributor, $cod, $amount)
	{
		if ($cod=='C' && $amount>0) $updateField = "amount = amount-$amount";
		else if ($cod=='D') $updateField = "amount = amount+$amount";

		$qry = " update m_distributor set $updateField where id='$selDistributor'";
		//echo $qry;
		$result	= 	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;		
	}

	# When edit/ delete a transaction the amount posting will reverse
	
	function updateDistributorAmt($selDistributor, $cod, $amount)
	{
		if ($cod=='C' && $amount>0) $updateField = "amount = amount+$amount";
		else if ($cod=='D') $updateField = "amount = amount-$amount";

		$qry = " update m_distributor set $updateField where id='$selDistributor'";
		//echo $qry;
		$result	= 	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;			
	}

	# When delete
	function getDistributorAccountRec($distributorAccountId)
	{
		$qry = " select distributor_id, amount, cod, so_id, claim_id from t_distributor_ac where id='$distributorAccountId' ";
		//echo $qry;
		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4]):array();
	}
	# Update Sales Order Rec 
	function updateSOPaymentStatus($salesOrderId)
	{
		$qry = " update t_salesorder set status_id='', complete_status='' where id=$salesOrderId";
		//echo "<br>SO==>$qry<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateClaimPaymentStatus($claimId)
	{
		$qry = "update t_claim set status_id='', complete_status='' where id='$claimId'";		
		//echo "<br>Claim==>$qry<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	
	*/
	
	//function 
	
	
}