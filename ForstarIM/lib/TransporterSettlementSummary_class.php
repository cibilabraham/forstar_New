<?php
Class TransporterSettlementSummary
{

	/****************************************************************
	This class deals with all the operations relating to Transporter Settlement Summary
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function TransporterSettlementSummary(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select distinct Supplier
	function fetchTransporterRecords($fromDate, $tillDate)
	{
		$qry	= "select distinct a.transporter_id, b.name from t_salesorder a, m_transporter b where a.transporter_id=b.id and a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate' and (a.settled='Y' or a.oc_settled='Y') order by b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# GetRecords based on date and Transporter
	function filterTransporterSettlementRecords($selTransporter, $fromDate, $tillDate)
	{
		/*
		$whr		= " a.transporter_id='$selTransporter' and (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.settled='Y' ";
		$groupBy	= " a.bill_no ";	
		$orderBy	= " a.bill_no asc ";
		$qry	= "select a.bill_no, a.settled_date, sum(a.transporter_grand_total_amt), a.paid from t_salesorder a ";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="")	$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
		*/
		$qry = " select billNo, setlDate, sum(tAmt), paid from (
				select a.id as id, a.bill_no as billNo, a.settled_date as setlDate, sum(a.transporter_actual_amt) as tAmt, a.paid as paid from t_salesorder a where a.transporter_id='$selTransporter' and (a.dispatch_date>='$fromDate' and  a.dispatch_date<='$tillDate') and a.settled='Y' group by a.bill_no 
				union
				select a1.id as id, a1.oc_bill_no as billNo, a1.oc_settled_date as setlDate, sum(a1.oc_t_actual_cost) as tAmt, a1.paid as paid from t_salesorder a1 where a1.transporter_id='$selTransporter' and (a1.dispatch_date>='$fromDate' and a1.dispatch_date<='$tillDate') and a1.oc_settled='Y' group by a1.oc_bill_no
			) as x group by billNo order by billNo";

		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Upate Selected Bill No Records
	function updateTransporterBillRecords($selTransporter, $billNo, $paid)
	{
		# Get Bill Num Records
		$getAllBillRecords = $this->getBillNoRecords($selTransporter, $billNo);
		
		if (sizeof($getAllBillRecords)>0) {
			foreach ($getAllBillRecords as $br) {
				$salesOrderId	= $br[0];
				# Update Transporter Payment
				$updateTransporterPayment = $this->updateTransporterPayment($salesOrderId, $paid);
			}
		}
		return true;
	}


	# Get Bill Number Records
	function getBillNoRecords($selTransporter, $billNo)
	{
		$qry = " select id from t_salesorder where transporter_id='$selTransporter' and bill_no='$billNo' ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Update Transporte Paid status
	function updateTransporterPayment($salesOrderId, $paid)
	{
		$qry	=	"update t_salesorder set";
		if ($paid=='Y') {
			$qry .= " paid='$paid', paid_date=Now(), paid_time=Now()";		
		} else if ($paid=='N') {
			$qry .= " paid='$paid', paid_date='0000-00-00', paid_time='0000-00-00 00:00:00'";		
		} else {
			$qry .="";
		}
		
		$qry .= "  where id='$salesOrderId' ";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);		
		if ($result) 	$this->databaseConnect->commit();
		else	 	$this->databaseConnect->rollback();
		return $result;	
	}

	
}	
?>