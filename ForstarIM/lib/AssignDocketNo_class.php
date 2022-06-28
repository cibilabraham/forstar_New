<?php
Class AssignDocketNo
{

	/****************************************************************
	This class deals with all the operations relating to Assign Docket No
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function AssignDocketNo(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select distinct Supplier (a.dispatch_date)
	function fetchTransporterRecords($fromDate, $tillDate)
	{
		$qry	= "select distinct a.transporter_id, b.name from t_salesorder a, m_transporter b where a.transporter_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.complete_status='C' order by b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Get Records based on date and Transporter
	function filterSalesOrderRecords($fromDate, $tillDate, $selTransporter)
	{
		$whr = " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.complete_status='C' ";

		if ($selTransporter) $whr .= " and a.transporter_id='$selTransporter'";
			
		$orderBy	= " a.invoice_date asc, a.so asc, a.grand_total_amt asc, b.name asc ";

		$qry	= "select a.id, a.so, a.invoice_date, ROUND((a.grand_total_amt+a.round_value),2) as tSOAmt, a.gross_wt, a.invoice_type, a.sample_invoice_no, a.docket_no, a.settled as odSetld, a.paid as transporterPaid, a.num_box, b.name as transporter, a.dispatch_date, a.delivery_date, a.delivery_remarks, a.oda_applicable, a.oc_settled as ocSetld, md.name as dName  from t_salesorder a left join m_transporter b on a.transporter_id=b.id left join m_distributor md on a.distributor_id=md.id";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update SO REC
	function updateSalesOrderRec($soId, $docketNo, $deliveryDate, $deliveryRemark, $odaApplicable)
	{
		$qry .= " update t_salesorder set docket_no='$docketNo', delivery_date='$deliveryDate', delivery_remarks='$deliveryRemark', oda_applicable='$odaApplicable'  where id='$soId' ";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);		
		if ($result) 	$this->databaseConnect->commit();
		else	 	$this->databaseConnect->rollback();
		return $result;	
	}
	
}	
?>