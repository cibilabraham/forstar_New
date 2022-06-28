<?php
Class TransporterPaymentStatus
{

	/****************************************************************
	This class deals with all the operations relating to Payment Status
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function TransporterPaymentStatus(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select distinct Supplier
	function fetchTransporterRecords($fromDate, $tillDate)
	{
		$qry	= "select distinct a.transporter_id, b.name from t_salesorder a, m_transporter b where a.transporter_id=b.id and a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate' order by b.name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# select distinct settlement dates
	function fetchAllSettledDateRecords($fromDate, $tillDate, $selTransporter)
	{		
		$whr = "a.dispatch_date>='".$fromDate."' and a.dispatch_date<='".$tillDate."' and a.transporter_id='$selTransporter' and a.settled='Y'";
					
		$orderBy = " a.settled_date asc";		

		$qry = "select distinct a.settled_date  from t_salesorder a";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;	
		//echo $qry;				
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get All Transporter Records
	function fetchAllTransporterRecords($fromDate, $tillDate, $selTransporter, $selSettlementDate)
	{
		$whr = " a.dispatch_date>='".$fromDate."' and a.dispatch_date<='".$tillDate."' and a.transporter_id='$selTransporter' and a.docket_no!='' ";

		if ($selSettlementDate=="") 	$whr .= "";
		else 				$whr .= " and a.settled_date= '".$selSettlementDate."'";

		$groupBy 	= " a.docket_no ";	
						
		$orderBy = " a.settled_date asc, a.docket_no asc";		

		$qry = "select a.id, a.dispatch_date, sum(a.gross_wt), a.docket_no, a.settled, a.settled_date, a.paid, a.paid_date, a.paid_time, sum(a.transporter_grand_total_amt), a.bill_no from t_salesorder a";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="")	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
	
		//echo "$qry<br>";				
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	/*
	Status Release
		Change All - paid, settled, confirm  Release
		Change Rate  - paid and settled
		Change Payment Details: Paid realse
	*/
	#Change Payment Status
	#*************************
	# Changestatus : CA->Change All, CR->Change Rate, CPD->Change Payment Details
	function updateTransporterPaymentStatus($fromDate, $tillDate, $selTransporter, $changeStatus, $selSettlementDate)
	{
		# Get All records for date range
		$salesOrderRecs = $this->filterSalesOrderRecs($fromDate, $tillDate, $selTransporter, $selSettlementDate);
		if (sizeof($salesOrderRecs)>0) {			
			foreach ($salesOrderRecs as $sor) {
				$salesOrderId	= $sor[0];
				# Update Payment
				$updatePaymentDetails = $this->updateTransporterPayment($salesOrderId, $changeStatus);				
			}
		}
		return true;	
	}	
	
	# Filter Sales Order records 
	function filterSalesOrderRecs($fromDate, $tillDate, $selTransporter, $selSettlementDate)
	{
		$whr = " a.dispatch_date>='".$fromDate."' and a.dispatch_date<='".$tillDate."' and a.transporter_id='$selTransporter' and a.docket_no!='' ";

		if ($selSettlementDate=="") 	$whr .= "";
		else 				$whr .= " and a.settled_date= '".$selSettlementDate."'";
						
		$orderBy = " a.settled_date asc, a.docket_no asc";		

		$qry = "select a.id from t_salesorder a";
		
		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
	
		//echo "$qry<br>";				
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update Status
	function updateTransporterPayment($salesOrderId, $changeStatus)
	{
		$uptdQry = "";		
		if ($changeStatus=='CR' || $changeStatus=='CA') $uptdQry = " , settled='N', settled_date='0' ";

		$qry = " update t_salesorder set paid='N', paid_date='0', paid_time='0' $uptdQry where id='$salesOrderId' ";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}
	
}	
?>