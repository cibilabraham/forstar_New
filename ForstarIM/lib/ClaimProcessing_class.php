<?php
class ClaimProcessing
{
	/****************************************************************
	This class deals with all the operations relating to Claim Processing
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ClaimProcessing(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Filter Claim Entry Recs
	function filterClaimRecs($selClaimId)
	{
		$qry = "select a.id, a.claim_so_id, a.product_id, a.rate, a.quantity, a.total_amount, b.name, a.defect_qty, a.defect_type, d.so from t_claim_entry a, m_product_manage b, t_claim_so_entry c, t_salesorder d where c.salesorder_id=d.id and a.claim_so_id=c.id and b.id=a.product_id and c.claim_main_id='$selClaimId' ";		
		//echo $qry;
		$result = array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Find Record based on SO id 
	function findDistributorName($selClaimId)
	{
		$qry = "select b.name from t_claim a, m_distributor b where a.distributor_id=b.id and a.id=$selClaimId";		
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function getClaimRec($selClaimId)
	{
		$qry = "select claim_number, claim_type, cod, fixed_amount, mr_amount, created, distributor_id, settled_date, status_id from t_claim where id=$selClaimId ";	
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4],$rec[5], $rec[6], $rec[7], $rec[8]):array();
	}


	# Update claim rec
	function updateClaim($selClaimId, $dispatchDate, $selStatus, $isComplete)
	{
		$qry = "update t_claim set settled_date='$dispatchDate', status_id='$selStatus', complete_status='$isComplete' where id=$selClaimId";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
		
	# Returns all Not Completed claim Records
	function fetchNotCompleteClaimRecords()
	{
		//claim_type='MR' and
		$qry = "select id, claim_number from t_claim where  (complete_status<>'C' or complete_status is null) order by claim_number desc";		
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	
}
?>