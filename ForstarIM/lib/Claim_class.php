<?php
class Claim
{  
	/****************************************************************
	This class deals with all the operations relating to Claim
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function Claim(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	#Find the Max value of Claim Order
	function maxValueCO()
	{
		$qry = "select max(claim_number) from t_claim";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	#Insert Claim Main Rec
	function addClaim($claimOrderNo, $userId, $lastDate, $claimType, $debit, $distributorId, $toalClaimAmt, $grandTotalReturnAmt, $fixedAmtReason)
	{
		$qry = "insert into t_claim (claim_number, last_date, created, createdby, claim_type, cod, distributor_id, fixed_amount, mr_amount, fixed_amt_reason) values('$claimOrderNo', '$lastDate', Now(), '$userId', '$claimType', '$debit', '$distributorId', '$toalClaimAmt', '$grandTotalReturnAmt', '$fixedAmtReason')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#Insert SO Rec
	function addClaimSORec($claimMainEntryId, $selSalesOrderId)
	{
		$qry = "insert into t_claim_so_entry (claim_main_id, salesorder_id) values('$claimMainEntryId', '$selSalesOrderId')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#For adding Claim Entries Items
	function addClaimEntries($lastId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType, $salesOrderEntryId)
	{
		$qry =	"insert into t_claim_entry (claim_so_id, product_id, rate, quantity, total_amount, defect_qty, defect_type, sales_order_entry_id) values('$lastId', '$selProductId', '$unitPrice', '$quantity', '$totalAmt', '$defectQty', '$defectType', '$salesOrderEntryId')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $insertStatus;
	}

	#For Update Claim Entries Items
	function updateClaimEntries($claimEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType, $salesOrderEntryId)
	{
		//claim_so_id,
		$qry =	"update t_claim_entry set  product_id='$selProductId', rate='$unitPrice', quantity='$quantity', total_amount='$totalAmt', defect_qty='$defectQty', defect_type='$defectType', sales_order_entry_id='$salesOrderEntryId' where id='$claimEntryId'";
		//echo $qry;			
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.claim_number, a.created, a.createdby, a.last_date, c.name, a.extended, a.logstatus, a.logstatus_descr, a.settled_date, a.status_id, a.complete_status from t_claim a, m_distributor c where c.id=a.distributor_id and a.created>='$fromDate' and a.created<='$tillDate' order by a.claim_number desc limit $offset, $limit";		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Returns all Purchase Order
	function fetchAllRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, a.claim_number, a.created, a.createdby, a.last_date, c.name, a.extended, a.logstatus, a.logstatus_descr, a.settled_date, a.status_id, a.complete_status from t_claim a, m_distributor c where c.id=a.distributor_id and a.created>='$fromDate' and a.created<='$tillDate' order by a.claim_number desc";		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Printing Purpose
	function getSORecords()
	{
		$qry = "select id, so, distributor_id, created, createdby, status from t_claim where status='P' order by so desc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}
	
	#For Getting Total Amount Of Each Sales Order
	/*function getSalesOrderAmount($salesOrderId)
	{
		$qry = "select sum(total_amount) from t_claim_entry where salesorder_id='$salesOrderId' group by salesorder_id";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	*/
	
	# Get Supplier stock based on Supplier id 
	function find($claimId)
	{
		$qry = "select id, claim_number, created, createdby, last_date, extended, claim_type, cod, distributor_id, fixed_amount, fixed_amt_reason from t_claim where id=$claimId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	

	# Update  
	function updateClaim($claimOrderId, $lastDate, $dateExtended, $claimType, $debit, $toalClaimAmt, $distributorId, $grandTotalReturnAmt, $fixedAmtReason)
	{
		$qry = "update t_claim set last_date='$lastDate', extended='$dateExtended', claim_type='$claimType', cod='$debit', distributor_id='$distributorId', fixed_amount='$toalClaimAmt', mr_amount='$grandTotalReturnAmt', fixed_amt_reason='$fixedAmtReason' where id='$claimOrderId'";
	
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#delete claim Rec
	function delClaimEntryRec($claimEntryId)
	{
		$qry = " delete from t_claim_entry where id=$claimEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;		
	}

	#Delete  Claim Product  Recs
	function deleteClaimItemRecs($claimOrderId)
	{
		$qry = " delete from t_claim_entry where claim_so_id=$claimOrderId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete claim reg Main
	function deleteClaim($claimOrderId)
	{
		# Get all Records From Claim SO Entries
		$claimSoEntryRecs = $this->getAllClaimSORecords($claimOrderId);
		if (sizeof($claimSoEntryRecs)>0) {
			foreach ($claimSoEntryRecs as $rec) {
				$claimSOEntryId = $rec[0];
				# Delete from t_claim_ntry table
				$delClaimEntryRec = $this->deleteClaimEntryRec($claimSOEntryId);
			}
		}
		# Delete from  SO Entry Rec		
		$deleteClaimSOEntryRec = $this->delClaimSOEntryRec($claimOrderId);

		# Delete from Main Rec
		$qry = " delete from t_claim where id=$claimOrderId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getAllClaimSORecords($claimOrderId)
	{
		$qry = " select id from t_claim_so_entry where claim_main_id='$claimOrderId'";		
		//echo $qry;
		$result = array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function deleteClaimEntryRec($claimSOEntryId)
	{
		$qry = " delete from t_claim_entry where claim_so_id=$claimSOEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function delClaimSOEntryRec($claimOrderId)
	{
		$qry = " delete from t_claim_so_entry where claim_main_id=$claimOrderId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	#Filter sales Order Recs
	function filterSalesOrderRecs($selSOId)
	{		
		$qry = "select a.id, a.salesorder_id, a.product_id, a.rate, a.quantity, a.total_amount, b.name from t_salesorder_entry a, m_product_manage b where b.id=a.product_id and a.salesorder_id='$selSOId' ";
		//echo $qry;
		$result = array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	// Filter Claims Recs
	function filterClaimRecs($selSOId)
	{
		$qry = "select a.id, a.salesorder_id, a.product_id, a.rate, a.quantity, a.total_amount, b.name, c.id, c.defect_qty, c.defect_type from t_salesorder_entry a left join t_claim_entry c on a.id=c.sales_order_entry_id, m_product_manage b where b.id=a.product_id and a.salesorder_id='$selSOId'";
		//echo $qry;
		$result = array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Setting the Log status	
	function updateClaimLogStatus($claimId, $statusFlag, $dispatchLastDate)
	{
		$qry = " update t_claim set logstatus='$statusFlag', logstatus_descr='$dispatchLastDate' where id=$claimId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Check Claim Number Exist
	function checkClaimNumberExist($claimId)
	{
		$qry = " select id from t_claim where claim_number='$claimId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	# get Sales Order List
	function getSalesOrderList($distributorId, $fromDate, $tillDate)
	{
		$dateF		=	explode("/",$fromDate);
		$fDate		=	$dateF[2]."-".$dateF[1]."-".$dateF[0];

		$dateT		=	explode("/",$tillDate);
		$tDate		=	$dateT[2]."-".$dateT[1]."-".$dateT[0];

		if ($fromDate!="" && $tillDate!="") $addWhr = "and a.invoice_date>='$fDate' and a.invoice_date<='$tDate'";

		$qry = "select a.id, a.so, a.distributor_id, a.invoice_date, a.createdby, b.name from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.distributor_id='$distributorId' $addWhr order by a.so desc";

		//$qry = "select a.id, a.so, a.distributor_id, a.invoice_date, a.createdby, b.name from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.distributor_id='$distributorId' and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' order by a.so desc";
		// echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		//print_r($result);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Returns all Sales Order
	function fetchAllDistWiseSORecords($distributorId)
	{
		$qry = "select a.id, a.so, a.distributor_id, a.invoice_date, a.createdby, b.name from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.distributor_id='$distributorId' order by a.so desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Selected Sales Order items
	function getselectedSalesOrderRecs($claimId)
	{
		$qry = "select id, salesorder_id from t_claim_so_entry where claim_main_id='$claimId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check PO Number Exist
	// 	function checkClaimNumberExist($claimGenId)
	// 	{
	// 		$qry = " select id from t_claim where claim_number='$claimGenId'";
	// 		$rec = $this->databaseConnect->getRecord($qry);
	// 	}

	# Checking Unique Numbering
	function checkUnique($claimOrderNo, $hidClaimNumber)
	{
		$addWhr = ($hidClaimNumber!="")? " and claim_number!='$hidReqNumber' " : "";
		$sqry = "select id from t_claim where claim_number='$claimOrderNo' $addWhr ";
		//echo $sqry;
		$srec = $this->databaseConnect->getRecord($sqry);
		return ( sizeof($srec)>0)?true:false;
	}

	#Update SO Rec
	function updateClaimSORec($claimSOEntryId, $selSalesOrderId)
	{
		$qry = "update t_claim_so_entry set salesorder_id='$selSalesOrderId' where id='$claimSOEntryId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete SO Entry Rec
	function deleteSOEntryRec($claimSOEntryId)
	{
		$qry = " delete from t_claim_so_entry where id=$claimSOEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete SO Entry Recs From t_claim_entry
	function deleteClaimEntriesRec($claimSOEntryId)
	{
		$qry = " delete from t_claim_entry where claim_so_id=$claimSOEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function getClaimSORecords($claimOrderId)
	{
		$qry = "select a.id, a.salesorder_id, b.so from t_claim_so_entry a, t_salesorder b where b.id=a.salesorder_id and claim_main_id='$claimOrderId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	/******** Distributor Account Transaction Section*/
	/* Dist Account */
	function getDistributorAccountRec($claimOrderId)
	{
		$qry = " select distributor_id, amount, cod from t_distributor_ac where claim_id='$claimOrderId' ";
		//echo $qry;
		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}
	
	/* Del dist Account */
	function delDistributorAccount($claimOrderId)
	{
		$qry = " delete from t_distributor_ac where claim_id=$claimOrderId";
		//echo $qry;
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	/************ Distributor Account Transaction Section Ends here */
		
}
?>