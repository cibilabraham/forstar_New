<?php
Class TranspoterPayments
{
	/****************************************************************
	This class deals with all the operations relating to Transpoter Payments
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function TranspoterPayments(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	# Add Payments
	function addTransporterPayments($transporter, $chequeNo, $amount, $paymentMode, $userId, $paymentDate, $bankName)
	{
		$qry	= " insert into t_transporter_payments (transporter_id, cheque_no, amount, payment_date, payment_mode, created, createdby, bank_name) values('$transporter', '$chequeNo', '$amount', '$paymentDate', '$paymentMode', NOW(), '$userId', '$bankName')";		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# Filter Records (PAGING)
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $transporterId)
	{			
		$whr = " a.transporter_id=b.id and a.payment_date>='$fromDate' and a.payment_date<='$tillDate'";

		if ($transporterId) $whr .= " and a.transporter_id='$transporterId' ";

		$orderBy = "a.payment_date asc";

		$limit = " $offset, $limit ";

		$qry = "select  a.id, a.transporter_id, a.cheque_no, a.amount, a.payment_date, b.id, b.name, a.bank_name  from t_transporter_payments a, m_transporter b  ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Fetch All Records
	function fetchAllRecords($fromDate, $tillDate, $transporterId)
	{
		$whr = " a.transporter_id=b.id and a.payment_date>='$fromDate' and a.payment_date<='$tillDate'";

		if ($transporterId) $whr .= " and a.transporter_id='$transporterId' ";

		$orderBy = "a.payment_date asc";		

		$qry = "select  a.id, a.transporter_id, a.cheque_no, a.amount, a.payment_date, b.id, b.name, a.bank_name  from t_transporter_payments a, m_transporter b  ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Filter table using id
	function find($paymentId)
	{
		$qry = "select  a.id, a.transporter_id, a.cheque_no, a.amount, a.payment_date, a.bank_name  from t_transporter_payments a where a.id=$paymentId";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	# update Supplier Payment Record
	function updateTansporterPayments($transporterPaymentsId, $transporter, $chequeNo, $amount, $paymentDate, $bankName)
	{
		$qry	= " update t_transporter_payments set transporter_id=$transporter, cheque_no='$chequeNo', amount='$amount', payment_date='$paymentDate', bank_name='$bankName' where id=$transporterPaymentsId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}
	
	# Delete Transporter Payment
	function deleteTransporterPayments($transporterPaymentsId)
	{
		$qry	=	" delete from t_transporter_payments where id=$transporterPaymentsId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;
	}

	#Get Paid Records
	function getPaidRecords($paidSupplierId)
	{
		$qry	= "select id, cheque_no, amount, payment_mode from t_transporter_payments where transporter_id='$paidSupplierId'";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
}	
?>