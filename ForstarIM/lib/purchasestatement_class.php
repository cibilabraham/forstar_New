<?php
Class PurchaseStatement
{
	/****************************************************************
	This class deals with all the operations relating to Purchase Statement
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PurchaseStatement(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	

	#Select distinct Supplier (Ac Confirm is related to Purchase Report Confirm)
	function fetchSupplierRecords($fromDate, $tillDate, $acConfirmed)
	{
		if ($acConfirmed) $confirmEnable = "and a.report_confirm='Y'";
		else 		 $confirmEnable = "";

		$qry	=	"select distinct a.main_supplier, c.id, c.name from t_dailycatch_main a, t_dailycatchentry b, supplier c where a.id=b.main_id and a.main_supplier=c.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.payment_confirm='Y' and a.print_status='N' $confirmEnable order by c.name asc";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Get Records based on date and supplier
	function filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $acConfirmed, $billingCompanyId, $unit)
	{
		$whr = " a.id=b.main_id and a.main_supplier='$selectSupplier' and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.flag=1 and b.actual_amount!=0 and a.payment_confirm<>'N' and a.print_status='N' ";
		//$whr = " a.id=b.main_id and a.unit=m.id and a.main_supplier='$selectSupplier' and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.flag=1 and b.actual_amount!=0 and a.payment_confirm<>'N' and a.print_status='N' ";

		if ($acConfirmed!="") $whr .= " and a.report_confirm='Y' ";
		else $whr .= "";

		if ($billingCompanyId!=0) $whr .= " and a.billing_company_id='$billingCompanyId'";
		else $whr .= "";
		/*rekha added code */
		if ($unit!=0) $whr .= " and a.unit='$unit'";
		else $whr .= "";
		/* end code */
		$groupBy	= " a.weighment_challan_no ";

		$orderBy	= " a.select_date asc, a.weighment_challan_no asc ";

		//Rekha update code 
		$qry	=	"select a.id, a.weighment_challan_no, a.select_date,sum(b.effective_wt),b.ice_wt, sum(b.actual_amount), CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b ";
		//$qry	=	"select a.id, a.weighment_challan_no, a.select_date,sum(b.effective_wt),b.ice_wt, sum(b.actual_amount), CONCAT(a.alpha_code,'',a.weighment_challan_no),m.name from t_dailycatch_main a, t_dailycatchentry b,m_plant m";

		
		if ($whr!="")		$qry .= " where ".$whr;
		if ($groupBy!="")	$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		//echo $qry;	
		//exit;
		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the Supply Cost of the selected Challan Number (included in Settlement summary)
	function getSupplyCost($weighNumber, $selBillCompanyId)
	{
		$qry = " select a.id, b.weighment_challan_no, a.ice_total_block, a.ice_cost_per_block, a.ice_total_cost, a.ice_fixed_cost, a.tran_km, a.tran_cost_per_km, a.tran_total_amt, a.tran_fixed_amt, a.comm_total_qty, a.comm_per_kg, a.comm_total_rate, a.comm_fixed_rate, b.payment_confirm, a.handl_total_qty, a.handl_rate_per_kg, a.handl_total_amt, a.handl_fixed_amt, a.commission_total_amt, a.handling_total_amt, b.id from t_rmsupplycost a, t_dailycatch_main b where a.challan_id=b.id and b.weighment_challan_no='$weighNumber' and b.billing_company_id='$selBillCompanyId'";		
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
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

	#Update Print Confirm Y/N
	function updateRMChallanPrintStatus($challanId)
	{
		$qry = "update t_dailycatch_main set print_status='Y', print_date=Now() where id='$challanId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	# Get Other Billing Companies
	function getOtherBillingCompany($selectSupplier, $fromDate, $tillDate, $acConfirmed)
	{
		if ($acConfirmed) $confirmEnable = "and a.report_confirm='Y'";
		else 		 $confirmEnable = "";

		$qry	= "select distinct a.billing_company_id, c.display_name, c.default_row from t_dailycatch_main a, t_dailycatchentry b, m_billing_company c where a.billing_company_id=c.id and a.id=b.main_id and a.main_supplier='$selectSupplier' and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.flag=1 and b.actual_amount!=0 and a.payment_confirm<>'N' and a.print_status='N' $confirmEnable group by a.weighment_challan_no order by a.select_date asc, a.weighment_challan_no asc";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
// rekha added dated on 10 july 2018
		# Get Other Billing Companies
	function getOtherunit($selectSupplier, $fromDate, $tillDate, $acConfirmed, $billingCompany)
	{
		if ($acConfirmed) $confirmEnable = "and a.report_confirm='Y'";
		else 		 $confirmEnable = "";

		$qry	= "select distinct a.unit, m.name from t_dailycatch_main a, t_dailycatchentry b, m_plant m where a.id=b.main_id and a.unit = m.id and a.main_supplier='$selectSupplier' and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.billing_company_id ='$billingCompany' and a.flag=1 and b.actual_amount!=0 and a.payment_confirm<>'N' and a.print_status='N' $confirmEnable group by a.weighment_challan_no order by a.select_date asc, a.weighment_challan_no asc";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
// end code 	
	
	# Get Other Billing Companies
	function getBillingCompanyId($selectSupplier, $fromDate, $tillDate, $acConfirmed)
	{
		if ($acConfirmed) $confirmEnable = "and a.report_confirm='Y'";
		else 		 $confirmEnable = "";

		$qry	= "select distinct a.billing_company_id, c.name from t_dailycatch_main a, t_dailycatchentry b, m_billing_company c where a.billing_company_id=c.id and a.id=b.main_id and a.main_supplier='$selectSupplier' and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.flag=1 and b.actual_amount!=0 and a.payment_confirm<>'N' and a.print_status='N' $confirmEnable group by a.weighment_challan_no order by a.select_date asc, a.weighment_challan_no asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Get default Company
	function getDefaultCompany()
	{
		$qry = "select id from m_billing_company where default_row='Y'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
}	
?>