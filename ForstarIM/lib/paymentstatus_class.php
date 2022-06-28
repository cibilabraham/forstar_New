<?php
Class PaymentStatus
{

	/****************************************************************
	This class deals with all the operations relating to Payment Status
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PaymentStatus(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select distinct Supplier
	function fetchSupplierRecords($fromDate, $tillDate)
	{
		$qry	= "select distinct a.main_supplier, c.id, c.name from t_dailycatch_main a, t_dailycatchentry b, supplier c where a.id=b.main_id and a.main_supplier=c.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' order by c.name asc";		
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#GetRecords based on date and supplier
	function filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $selSettlementDate, $billingCompany)
	{
		$whr = "a.id=b.main_id and a.main_supplier='$selectSupplier' and (a.select_date>='$fromDate' and a.select_date<='$tillDate') and a.flag=1 ";
	
		if ($selSettlementDate!="") $whr .= " and b.settlement_date= '".$selSettlementDate."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	= " a.weighment_challan_no, a.billing_company_id";
		$orderBy	= " a.billing_company_id asc, a.select_date asc, a.weighment_challan_no asc";
	
		$qry = "select a.id, a.weighment_challan_no, a.select_date, b.ice_wt, sum(b.actual_amount), a.payment_confirm, a.payment_date, a.billing_company_id, a.confirm, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;	
		
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Check All RM Settled
	function challanRecords($fromDate, $tillDate, $challanEntryId, $selectSupplier)
	{
		$notSettled = 0;
		
		$qry	=	"select a.id, a.weighment_challan_no, a.select_date, b.ice_wt, b.actual_amount, a.payment_confirm, a.payment_date, b.paid from t_dailycatch_main a, t_dailycatchentry b where  a.id=b.main_id and a.main_supplier='$selectSupplier' and ((a.select_date>='$fromDate' and a.select_date<='$tillDate') or (b.settlement_date>='$fromDate' or b.settlement_date=0) and (b.settlement_date<='$tillDate' or b.settlement_date=0)) and a.flag=1 and a.id='$challanEntryId' order by a.select_date asc";
		
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		
		foreach ($result as $cr) {
			$settled = $cr[7];
			if ($settled=='N') {
				$notSettled++;
			}
		}
		return ($notSettled!="")?true:false;
	}

	#select distinct settlement dates
	function fetchAllDateRecords($fromDate, $tillDate, $selectSupplier, $billingCompany)
	{			
		$whr = " a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.main_supplier='$selectSupplier' and a.id=b.main_id and b.paid='Y' ";
	
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";		

		$orderBy	= 	" b.settlement_date asc ";
		
		$qry	= "select distinct b.settlement_date from t_dailycatch_main a, t_dailycatchentry b ";

		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#GetRecords based on date and supplier
	function filterDailyCatchEntryRecords($selectSupplier, $fromDate, $tillDate, $selSettlementDate, $qtySearchType, $billingCompany)
	{
		$whr = "a.id=b.main_id and a.main_supplier='$selectSupplier' and (a.select_date>='$fromDate' and a.select_date<='$tillDate') and a.flag=1 ";
	
		if ($selSettlementDate!="") $whr .=" and b.settlement_date= '".$selSettlementDate."'";

		$upQry = "";		
		if ($qtySearchType=='SU') {
			$groupBy = " a.weighment_challan_no, a.billing_company_id";	
			$upQry = " sum(b.effective_wt), sum(b.actual_amount) ";	
		} else if ($qtySearchType=='DT') {
			$groupBy = "";	
			$upQry = " b.effective_wt, b.actual_amount ";		
		}

		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";	

		$orderBy	=	" a.billing_company_id asc, a.select_date asc, a.weighment_challan_no asc";
	
		$qry = "select a.id, a.weighment_challan_no, a.select_date, a.payment_confirm, a.confirm, $upQry , b.fish, b.fish_code, b.count_values, b.grade_id, b.received_by, a.billing_company_id, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b ";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;		
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# From & To Date Format Like Y-m-d
	function getDateDiff($fromDate, $toDate)
	{		
		$cDate		= explode("-",$toDate);
		$d2 = mktime(0,0,0,$cDate[1],$cDate[2],$cDate[0]);
		$eDate		= explode("-",$fromDate);		
		$d1 = mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);		
		return $dateDiff = floor(($d2-$d1)/86400);
	}

	# GetRecords based on date and supplier
	function getPurchasedAmount($fromDate, $tillDate, $selectSupplier, $selSettlementDate)
	{
		$whr = "a.id=b.main_id and a.main_supplier ='$selectSupplier' and (a.select_date>='$fromDate' and a.select_date<='$tillDate') and a.flag=1 ";
	
		if ($selSettlementDate!="") $whr .= " and b.settlement_date = '".$selSettlementDate."'";
		
		$groupBy	= " b.paid ";
		$orderBy	= " a.billing_company_id asc, a.select_date asc";
	
		$qry = "select sum(b.actual_amount) from t_dailycatch_main a, t_dailycatchentry b";
	
		if ($whr!="") 	  $qry .= " where ".$whr;		
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;	
		
		//echo "<br>$qry<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}

	# Get Supplier Payment Records
	function getSupplierPaymentRecords($fromDate, $tillDate, $selectSupplier)
	{
		$qry = " select payment_date, sum(amount) from t_supplierpayments where payment_date>='$fromDate' and payment_date<='$tillDate' and supplier_id='$selectSupplier' group by payment_date order by payment_date asc ";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch Challan billing company Records
	function fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier)
	{	

		$whr = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.billing_company_id=bc.id and a.id=b.main_id and a.weighment_challan_no is not null ";
	
		if ($selectSupplier!="") $whr .=" and a.main_supplier=".$selectSupplier;			

		$orderBy	= 	"bc.display_name";
		
		$qry	= "select distinct bc.id, bc.display_name from t_dailycatch_main a, t_dailycatchentry b, m_billing_company bc ";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
}	
?>