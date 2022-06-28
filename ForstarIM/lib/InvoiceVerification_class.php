<?php
Class InvoiceVerification
{

	/****************************************************************
	This class deals with all the operations relating to Challan Verification
	*****************************************************************/
	var $databaseConnect;
	var $invMissingRecs;

	//Constructor, which will create a db instance for this class
	function InvoiceVerification(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	function changeInvoiceStatus($invoiceId)
	{
		$qry = "update t_salesorder set complete_status='P', cn_cl_reason='' where id=$invoiceId";
		//echo $qry;
		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}
	
	function closeInvoice($invoiceId, $cnclReason)
	{
		$qry = "update t_salesorder set complete_status='CL', cn_cl_reason='$cnclReason' where id=$invoiceId";
		//echo $qry;
		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}

	function cancelInvoice($invoiceId, $cnclReason)
	{
		$qry = "update t_salesorder set complete_status='CN', cn_cl_reason='$cnclReason' where id=$invoiceId";
		//echo $qry;
		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Get Paginated Invoice Recs
	function getPaginatedInvoiceRecords($fromDate, $tillDate, $offset, $limit, $invoiceType)
	{
		/*
		$invType = ($invoiceType=="PI")?'T':'S';
		$qry = "select a.id, a.so, b.name, a.proforma_no, a.sample_invoice_no, a.complete_status, a.cn_cl_reason, a.invoice_type from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.invoice_type='$invType' and (a.complete_status!='C' or a.complete_status is null) order by a.invoice_date desc, a.so desc limit $offset, $limit";
		*/

		$whr = " a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and (a.complete_status!='C' or a.complete_status is null) ";

		if ($invoiceType=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceType=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceType=='SI') $whr .= " and a.invoice_type='S' ";
	
		$orderBy	= " a.invoice_date desc, a.so desc ";

		$limit		= "$offset, $limit";

		$qry = " select a.id, a.so, b.name, a.proforma_no, a.sample_invoice_no, a.complete_status, a.cn_cl_reason, a.invoice_type from t_salesorder a left join m_distributor b on a.distributor_id=b.id ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllInvoiceRecords($fromDate, $tillDate, $invoiceType)
	{
		$invType = ($invoiceType=="PI")?'T':'S';

		$qry = "select a.id, a.so, b.name, a.proforma_no, a.sample_invoice_no, a.complete_status, a.cn_cl_reason, a.invoice_type from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.invoice_type='$invType' and (a.complete_status!='C' or a.complete_status is null) order by a.invoice_date desc, a.so desc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# ------------------------------- Missing Invoice Records ------------
	// Using Pagination
	function getPaginatedMissingInvoiceRecords($fromDate, $tillDate, $offset, $limit, $invoiceType) 
	{
		$fetchAllMissingRecords = $this->getMissingRecords($fromDate, $tillDate, $invoiceType);
		$this->invMissingRecs  = $fetchAllMissingRecords;
		return $sliceArray = array_slice($fetchAllMissingRecords, $offset, $limit);	
	}

	function fetchAllMissingRecs()
	{
		return $this->invMissingRecs;
	}

	# GetRecords based on date and invoice type
	function getMissingRecords($fromDate, $tillDate, $invoiceType)
	{
		$continuousInvoiceNos = $this->getContinuousInvoiceNo($fromDate, $tillDate, $invoiceType);
		$existingInvoiceNos   = $this->getExistingInvoiceNo($fromDate, $tillDate, $invoiceType);
		$invoiceNosBforFromdate   = $this->getBforFromDateInvoiceNo($fromDate, $invoiceType);
		$invoiceNosAftrTilldate   = $this->getAftrTillDateInvoiceNo($tillDate, $invoiceType);	
	
			
		

		return $arr = array_diff($continuousInvoiceNos, $existingInvoiceNos, $invoiceNosBforFromdate, $invoiceNosAftrTilldate);
	}
	
	# Get Continuous invoice no
	function getContinuousInvoiceNo ($fromDate, $tillDate, $invoiceType)
	{		
		//$whr = " a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and (a.complete_status!='C' or a.complete_status is null) ";
		$whr = " a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' ";

		if ($invoiceType=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceType=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceType=='SI') $whr .= " and a.invoice_type='S' ";
	
		$orderBy	= " a.invoice_date desc, a.so desc ";

		$qry = " select min(a.so), max(a.so) from t_salesorder a  ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;

		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		$minInvoiceNo = $rec[0];
		$maxInvoiceNo = $rec[1];
		//echo "$minInvoiceNo-$maxInvoiceNo";
		if ($minInvoiceNo==$maxInvoiceNo) {
			list($minInvoiceNo, $endNum) = $this->getValidInvoiceNum($tillDate, $invoiceType);
		}

		if (!$this->chkValidInvNum($tillDate, $invoiceType, $minInvoiceNo)) {
			list($minInvoiceNo, $endNum) = $this->getValidInvoiceNum($tillDate, $invoiceType);
		}

		$invoiceNo = array();
		$k=0;
		for ($i=$minInvoiceNo; $i<=$maxInvoiceNo; $i++) {
			$invoiceNo[$k] = $minInvoiceNo++;
			$k++;
		}		
		return $invoiceNo;
	}
		

	# Get valid Invoice no
	function getValidInvoiceNum($selDate, $invoiceType)
	{			
		$whr = " date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='SO'";

		if ($invoiceType=='TI') $whr .= " and so_invoice_type='TA'";
		else if ($invoiceType=='PI') $whr .= "  and so_invoice_type='PF' ";
		else if ($invoiceType=='SI') $whr .= " and so_invoice_type='SA' ";
	
		$qry = " select start_no, end_no from number_gen ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;

		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array();
	}


	# Check Valid Challan Number
	function chkValidInvNum($selDate, $invoiceType, $invoiceNum)
	{	
		$whr = " date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='SO' and start_no<='$invoiceNum' and end_no>='$invoiceNum'";

		if ($invoiceType=='TI') $whr .= " and so_invoice_type='TA'";
		else if ($invoiceType=='PI') $whr .= "  and so_invoice_type='PF' ";
		else if ($invoiceType=='SI') $whr .= " and so_invoice_type='SA' ";
	
		$qry = " select start_no, end_no from number_gen ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	
	# Existing Invoice No
	function getExistingInvoiceNo($fromDate, $tillDate, $invoiceType)
	{
		//$whr = " a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and (a.complete_status!='C' or a.complete_status is null) ";
		$whr = " a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' ";

		if ($invoiceType=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceType=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceType=='SI') $whr .= " and a.invoice_type='S' ";
	
		$orderBy	= " a.invoice_date desc, a.so desc ";

		$qry = " select a.so from t_salesorder a  ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;

		//echo $qry."<br>";	
		$result	=	$this->databaseConnect->getRecords($qry);
	
		$invoiceNo = array();
		$i=0;
		foreach ($result as $r) {
			$invoiceNo[$i] = abs($r[0]);
			$i++;
		}
		return $invoiceNo;
	}


	// Before from date 
	function getBforFromDateInvoiceNo($fromDate, $invoiceType)
	{		
		//$whr = " a.invoice_date<='$fromDate' and (a.complete_status!='C' or a.complete_status is null) ";
		$whr = " a.invoice_date<='$fromDate' ";

		if ($invoiceType=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceType=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceType=='SI') $whr .= " and a.invoice_type='S' ";
	
		$orderBy	= " a.invoice_date desc, a.so desc ";

		$qry = " select a.so from t_salesorder a  ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;

		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		
		$invoiceNo = array();
		$i=0;
		foreach ($result as $r) {
			$invoiceNo[$i] = abs($r[0]);
			$i++;
		}
		return $invoiceNo;
	}
	
	# get After Date recs
	function getAftrTillDateInvoiceNo($tillDate, $invoiceType)
	{
		//$whr = " a.invoice_date>='$tillDate' and a.invoice_date<=NOW() and (a.complete_status!='C' or a.complete_status is null) ";
		$whr = " a.invoice_date>='$tillDate' and a.invoice_date<=NOW() ";

		if ($invoiceType=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceType=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceType=='SI') $whr .= " and a.invoice_type='S' ";
	
		$orderBy	= " a.invoice_date desc, a.so desc ";

		$qry = " select a.so from t_salesorder a  ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);		
		$invoiceNo = array();
		$i=0;
		foreach ($result as $r) {
			$invoiceNo[$i] = abs($r[0]);
			$i++;
		}
		return $invoiceNo;
	}

	# ------------------------------- Missing Invoice Records  ends here ------------

	# Get cancelled Invoice Rec
	function getCancelledInvoice($invNo, $invYear, $invType)
	{
		$qry = " select id from s_cancelled_invoice where invoice_no='$invNo' and inv_year='$invYear' and inv_type='$invType'";
		//echo $qry."<br>";	
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Insert Cancelled Tax Invoice 
	function InsInvoiceRec($invoiceId, $invYear, $invType, $userId)
	{
		$qry	= "insert into s_cancelled_invoice (invoice_no, inv_year, inv_type, created, createdby) values ('$invoiceId', '$invYear', '$invType', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}

	function delCancelledInvoice($cancelledInvoiceId)
	{
		$qry = " delete from s_cancelled_invoice where id=$cancelledInvoiceId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

}	
?>