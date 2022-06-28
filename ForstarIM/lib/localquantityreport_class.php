<?php
Class LocalQuantityReport
{

	/****************************************************************
	This class deals with all the operations relating to Local Quantity Report
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function LocalQuantityReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		

	# Filter all Records based on the unit, supplier and landing center
	function filterDailyCatchEntryRecords($selectUnit,$landingCenterId,$selectSupplier,$fromDate,$tillDate, $fishId, $processId, $weighNumber, $billingCompany)
	{
		$whr1=""; $whr2="";
		$criteriaSelection = "";
		if ($weighNumber!="") $criteriaSelection = "a.weighment_challan_no='".$weighNumber."'"; //Single Date
		else $criteriaSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		
		//$whr	= "" ;
		
		if ($selectUnit==0) $whr .= "";
		else $whr .= " and a.unit=".$selectUnit;
				
		if ($landingCenterId==0) $whr .= "";
		else $whr .= " and a.landing_center=".$landingCenterId;
				
		/*if ($selectSupplier==0) $whr .= "";
		else $whr .= " and a.main_supplier=".$selectSupplier;*/
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		$whr1.=$whr; $whr2.=$whr;
		if ($selectSupplier==0) 
		{ 
			$whr1.= "";
			$whr2.= "";
		}
		else
		{
			$whr1.= " and a.main_supplier=".$selectSupplier;
			$whr2.= " and a.payment=".$selectSupplier;
		}
		
		//$orderBy = "a.weighment_challan_no asc, c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc";
		$orderBy = "wtChallanNo asc, fishName asc, pCode asc, cntVal asc,grade asc, eftWt asc";
		
		$qry1= "select a.id as mainId, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no as wtChallanNo, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values as cntVal, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt  as eftWt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, a.payment_by, a.confirm, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, a.payment_confirm, a.payment_date, a.report_confirm, CONCAT(a.alpha_code,'',a.weighment_challan_no), c.name as fishName, d.code as pCode, e.code as grade, supp.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, m_grade e, supplier supp where $criteriaSelection and a.id=main_id and b.fish=c.id and b.fish_code=d.id and b.grade_id=e.id and a.weighment_challan_no is not null and supp.id=a.main_supplier";

		$qry2= "select a.id  as mainId, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no  as wtChallanNo, a.landing_center, a.payment, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values as cntVal, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt as eftWt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, a.payment_by, a.confirm, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, a.payment_confirm, a.payment_date, a.report_confirm, CONCAT(a.alpha_code,'',a.weighment_challan_no), c.name as fishName, d.code as pCode, e.code as grade, supp.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, m_grade e, supplier supp where $criteriaSelection and a.id=main_id and b.fish=c.id and b.fish_code=d.id and b.grade_id=e.id and a.weighment_challan_no is not null and supp.id=a.payment";
		if ($whr1!="") 		$qry1   .= $whr1; $qry2   .=$whr2;
		$qry="select * from ($qry1 union all $qry2 ) dum";
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;

		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));

	}
}	
?>