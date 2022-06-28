<?php
class PaymentTerms
{  
	/****************************************************************
	This class deals with all the operations relating to Payment Terms
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PaymentTerms(&$databaseConnect)
 	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addPaymentTerm($paymentMode, $description, $paymentRealization)
	{
		$qry	= "insert into m_paymentterms(mode, descr, realization_days) values('".$paymentMode."', '".$description."', '$paymentRealization')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, mode, descr, realization_days,active,(select count(mac.id) from m_customer_payment_terms mac where mac.payment_term_id=mp.id) as tot from m_paymentterms order by mode asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivePayment()
	{
		$qry	= "select id, mode, descr, realization_days,active from m_paymentterms where active=1 order by mode asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, mode, descr, realization_days,active,(select count(mac.id) from m_customer_payment_terms mac where mac.payment_term_id=mp.id) as tot from m_paymentterms mp order by mode asc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Record  based on id 
	function find($paymentTermId)
	{
		$qry = "select id, mode, descr, realization_days from m_paymentterms where id=$paymentTermId";
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update
	function updatePaymentTerm($paymentTermId, $paymentMode, $description, $paymentRealization)
	{
		$qry	= " update m_paymentterms set  mode='$paymentMode', descr='$description', realization_days='$paymentRealization' where id=$paymentTermId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
		
	# Delete 
	function deletePaymentTerm($paymentTermId)
	{
		$qry	= " delete from m_paymentterms where id=$paymentTermId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();
		return $result;
	}
		
	function findPaymentTerm($paymentTermId)
	{
		$rec = $this->find($paymentTermId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function updatePaymentTermconfirm($paymentTermId)
	{
	$qry	= "update m_paymentterms set active='1' where id=$paymentTermId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatePaymentTermReleaseconfirm($paymentTermId)
	{
		$qry	= "update m_paymentterms set active='0' where id=$paymentTermId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>