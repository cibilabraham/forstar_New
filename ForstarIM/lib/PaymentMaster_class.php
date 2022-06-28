<?php
class PaymentMaster
{  
	/****************************************************************
	This class deals with all the operations relating to fish master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PaymentMaster(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Check for Duplicate Entry
	function checkPaymentExist($duration)
	{
		$qry = "select id from m_payment_master where payment_duration='$duration'";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false; 
	}
	
	#Add New Payment Master
	function addPaymentMaster($duration,$description,$userId,$currentDate)
	{
		$qry = "insert into m_payment_master (payment_duration, description, created_by, created_on, active) values('$duration','$description','$userId','$currentDate','0')";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		
		if($insertStatus)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	#Fetch All Payment Master using limit
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, payment_duration, description, active from m_payment_master order by payment_duration limit $offset, $limit";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result>0)?$result:"");
	}
	
	#Fetch All Payment Master Records
	function fetchAllRecords()
	{
		$qry = "select id, payment_duration, description, active from m_payment_master order by payment_duration";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result>0)?$result:"");
	}
	
	#Confirm Payment Master
	function updatePaymentConfirm($paymentId)
	{
		$qry = "update m_payment_master set active=1 where id='$paymentId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	#Release Confirmation of Payment Master
	function updatePaymentReleaseConfirm($paymentId)
	{
		$qry = "update m_payment_master set active=0 where id='$paymentId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	#Fetch Payment Master Details based on id
	function find($editId)
	{
		$qry = "select id, payment_duration, description from m_payment_master where id='$editId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Update Payment Master Details
	function updatePaymentMaster($paymentId,$duration,$description)
	{
		$qry = "update m_payment_master set payment_duration='$duration', description='$description' where id='$paymentId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	#Delete Payment Master Records
	function deletePaymentMaster($paymentId)
	{
		$qry = "delete from m_payment_master where id='$paymentId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}
	
	#Get All Ative Payment Masters
	function getAllActivePayments()
	{
		$qry = "select id, payment_duration, description from m_payment_master where active=1";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Get Payment Duration
	function getPaymntDurtn($paymntId)
	{
		$qry = "select id, payment_duration from m_payment_master where id='$paymntId'";
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
}