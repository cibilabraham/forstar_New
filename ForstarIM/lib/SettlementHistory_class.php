<?php
Class SetlementHistory
{
	/****************************************************************
	This class deals with all the operations relating to Setlement History
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function SetlementHistory(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	

	#Select distinct Supplier (Ac Confirm is related to Purchase Report Confirm)
	function fetchSupplierRecords($fromDate, $tillDate, $acConfirmed)
	{
		if ($acConfirmed) $confirmEnable = "and a.report_confirm='Y'";
		else $confirmEnable = "";

		$qry	= " select distinct a.main_supplier, c.id, c.name from t_dailycatch_main a, t_dailycatchentry b, supplier c where a.id=b.main_id and a.main_supplier=c.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.payment_confirm='Y' $confirmEnable order by c.name asc ";
	//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Get Records based on date and supplier
	function filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $acConfirmed, $selSettlementDate, $billingCompany)
	{
		if ($acConfirmed) $confirmEnable = "and a.report_confirm='Y'";
		else $confirmEnable = "";
		
		$whr 	=  "a.id=b.main_id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.flag=1 and b.actual_amount!=0 and a.payment_confirm='Y' $confirmEnable";	
	
		if ($selectSupplier!="") $whr .= "and a.main_supplier='$selectSupplier'";
		else $whr .=  "";
		
		if ($selSettlementDate!="") $whr .= " and a.payment_time=date_format('$selSettlementDate','%Y-%m-%d %H:%i:%s')";
		else $whr .=  "";
		
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		$groupBy =  " a.weighment_challan_no, a.billing_company_id ";
		$orderBy = " a.billing_company_id asc, a.select_date asc, a.weighment_challan_no asc ";

		$qry = " select a.id, a.weighment_challan_no, a.select_date, b.ice_wt, sum(b.actual_amount), a.billing_company_id, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b ";

		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the Supply Cost of the selected Challan Number 
	function getSupplyCost($weighNumber, $selBillCompanyId)
	{
		$qry = " select a.id, b.weighment_challan_no, a.ice_total_block, a.ice_cost_per_block, a.ice_total_cost, a.ice_fixed_cost, a.tran_km, a.tran_cost_per_km, a.tran_total_amt, a.tran_fixed_amt, a.comm_total_qty, a.comm_per_kg, a.comm_total_rate, a.comm_fixed_rate, b.payment_confirm, a.handl_total_qty, a.handl_rate_per_kg, a.handl_total_amt, a.handl_fixed_amt, a.commission_total_amt, a.handling_total_amt, b.id from t_rmsupplycost a, t_dailycatch_main b where a.challan_id=b.id and b.weighment_challan_no='$weighNumber' and b.billing_company_id='$selBillCompanyId' ";
		//echo "$qry<br/>";

		$rec = $this->databaseConnect->getRecord($qry);
		//$totalSupplyCostAmt = 0;
		$totalIceCost	=	$rec[4];
		$fixedIceCost	=	$rec[5];
		$displyIceCost = "";
		if ($fixedIceCost!=0) $displyIceCost  = $fixedIceCost;
		else $displyIceCost  = $totalIceCost;

		$totalTransCost 	= $rec[8];
		$fixedTransCost		= $rec[9];
		$displyTransCost = "";
		if ($fixedTransCost!=0) $displyTransCost  = $fixedTransCost;
		else $displyTransCost  = $totalTransCost;
		
		// Detailed Sum	Section
		$commissionTotalAmt = $rec[19];		
		$handlingTotalAmt   = $rec[20];

		$totalCommiRate		= $rec[12];
		$fixedCommiRate		= $rec[13];
		$displyCommiCost = "";
		if ($fixedCommiRate!=0) $displyCommiCost  = $fixedCommiRate;
		else if ($totalCommiRate!=0) $displyCommiCost  = $totalCommiRate;
		else if ($commissionTotalAmt!=0) $displyCommiCost = $commissionTotalAmt;	

		$totalHandlingAmt = $rec[17];
		$fixedHandlingAmt = $rec[18];				
		$displayHandlingCost = "";
		if ($fixedHandlingAmt!=0) $displayHandlingCost = $fixedHandlingAmt;
		else if ($totalHandlingAmt!=0) $displayHandlingCost = $totalHandlingAmt;
		else if ($handlingTotalAmt!=0) $displayHandlingCost = $handlingTotalAmt;
		$totalSupplyCostAmt = $displyIceCost + $displyTransCost + $displyCommiCost + $displayHandlingCost;		
		return (sizeof($rec)>0)?$totalSupplyCostAmt:0;	
	}

	#select distinct settlement dates
	function getAllPaymentDate($fromDate, $tillDate, $selectSupplier)
	{	
		//$qry	= "select distinct payment_time, date_format(payment_time,'%d/%m/%Y %h:%i:%s %p') from t_dailycatch_main where select_date>='$fromDate' and select_date<='$tillDate' and main_supplier='$selectSupplier'  and payment_confirm='Y' order by payment_time asc";	
	
		$whr = "select_date>='$fromDate' and select_date<='$tillDate' and main_supplier='$selectSupplier'  and payment_confirm='Y'";	

		$orderBy = "payment_time asc";
		
		$qry	= "select distinct payment_time, date_format(payment_time,'%d/%m/%Y %h:%i:%s %p') from t_dailycatch_main";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch Challan billing company Records
	function fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier)
	{	

		$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and a.billing_company_id=bc.id and b.paid='Y' and a.payment_confirm='Y'";

		if ($selectSupplier!="") $whr .= "and a.main_supplier='".$selectSupplier."'";

		$orderBy	=	"bc.display_name";
		
		$qry	= "select distinct bc.id, bc.display_name from t_dailycatch_main a, t_dailycatchentry b, m_billing_company bc $tableName";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
}	
?>