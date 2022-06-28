<?php
Class ProcessorsPayments
{
	/****************************************************************
	This class deals with all the operations relating to Processors  Payments
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProcessorsPayments(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	# Add Processors Payments
	function addProcessorsPayments($processor, $chequeNo, $amount, $paymentMode, $paymentDate, $bankName, $userId)
	{
		$qry	= " insert into t_processorspayments (processor_id, cheque_no, amount, payment_date, payment_mode, bank_name, created, createdby) values('$processor', '$chequeNo', '$amount', '$paymentDate', '$paymentMode', '$bankName', NOW(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	
	# Filter Records (PAGING)
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $preProcessorId)
	{			
		$whr = "a.processor_id=b.id and a.payment_date>='$fromDate' and a.payment_date<='$tillDate'";

		if ($preProcessorId) $whr .= " and a.processor_id='$preProcessorId' ";

		$orderBy = "a.payment_date asc";

		$limit = " $offset, $limit ";

		$qry = "select  a.id, a.processor_id, a.cheque_no, a.amount, a.payment_date, b.id, b.name, a.bank_name  from t_processorspayments a, m_preprocessor b  ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Fetch All Records
	function fetchAllRecords($fromDate, $tillDate, $preProcessorId)
	{
		$whr = "a.processor_id=b.id and a.payment_date>='$fromDate' and a.payment_date<='$tillDate'";

		if ($preProcessorId) $whr .= " and a.processor_id='$preProcessorId' ";

		$orderBy = "a.payment_date asc";

		$qry = "select  a.id, a.processor_id, a.cheque_no, a.amount, a.payment_date, b.id, b.name, a.bank_name from t_processorspayments a, m_preprocessor b  ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Filter table using id
	function find($paymentId)
	{
		$qry = "select  a.id, a.processor_id, a.cheque_no, a.amount, a.payment_date, a.bank_name  from t_processorspayments a where a.id=$paymentId";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	# update Supplier Payment Record 
	function updateProcessorPayments($processorPaymentId, $processor, $chequeNo, $amount, $paymentDate, $bankName)
	{
		$qry	= " update t_processorspayments set processor_id=$processor, cheque_no='$chequeNo', amount='$amount', payment_date='$paymentDate', bank_name='$bankName' where id=$processorPaymentId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Supplier Payment
	function deleteProcessorPayments($processorPaymentsId)
	{
		$qry = " delete from t_processorspayments where id=$processorPaymentsId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;
	}

	#For checking advance balance
	function getPaidRecords($paidProcessorId)
	{
		$qry	= "select id, cheque_no, amount, payment_mode from t_processorspayments where processor_id='$paidProcessorId'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
}	
?>