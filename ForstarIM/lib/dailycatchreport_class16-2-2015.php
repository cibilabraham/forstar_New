<?php
Class DailyCatchReport
{

	/****************************************************************
	This class deals with all the operations relating to Daily Catch Report
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function DailyCatchReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}		
		
	#Select distinct Supplier
	function fetchSupplierRecords($fromDate,$tillDate)
	{
		$whr	= "a.main_supplier=b.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null";
				
		$orderBy = "b.name asc";
		
		$qry	= "select distinct a.main_supplier, b.id, b.name from t_dailycatch_main a, supplier b";
		
		if ($whr!="")	 	$qry	.= " where ".$whr;
		if($orderBy!="")	$qry	.= " order by ".$orderBy;
		//echo $qry;			
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch Weighment Challan Records
	function fetchWeighmentRecords($selectSupplier, $fromDate, $tillDate, $billingCompanyId)
	{
		$whr	= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and weighment_challan_no is not null";
		
		if ($selectSupplier) $whr .= " and main_supplier='".$selectSupplier."'";
		if ($billingCompanyId) $whr .= " and billing_company_id='".$billingCompanyId."'";

		$orderBy	= " weighment_challan_no asc";
			
		$qry = " select id, weighment_challan_no, alpha_code from t_dailycatch_main ";
			
		if ($whr!="")		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAlldailyCatchReportRecords($selWeighmentNo, $fromDate, $tillDate)
	{
		$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id" ;
		
		$whr	.= " and a.id='".$selWeighmentNo."'";
				
		$orderBy = "c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc";	

		$qry = "select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time, a.payment_by,a.confirm, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, a.alpha_code, a.rmp_memo_print_count from (t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d) left join m_grade e on b.grade_id=e.id";

		if ($whr!="")		$qry   .=" where ".$whr;
		if ($orderBy!="")	$qry   .=" order by ".$orderBy;
		//echo "<br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Filter Records Using Weighment No only
	function fetchAllCatchReportRecords($challanMainId)
	{	
		$qry	= "select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time,a.payment_by,a.confirm, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, a.alpha_code, a.rmp_memo_print_count from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join m_fish c on b.fish=c.id join m_processcode d on b.fish_code=d.id left join m_grade e on b.grade_id=e.id where a.id='$challanMainId' order by c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc";			
		//echo "<br>$qry<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}		

	#Update DailyCatch_main Records after verified
	function updateDailyCatchMainConfirmRecords($challanMainId)
	{		
		$qry	= " update t_dailycatch_main set confirm='1' where id='$challanMainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Filter all Records based on the unit, supplier and landing center
	function filterDailyCatchEntryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $weighNumber, $challanMainId)
	{
		$criteriaSelection = "";

		if ($challanMainId!="") {
			$criteriaSelection = "a.id='".$challanMainId."'"; //Single Date
		} else {
			$criteriaSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}

		$whr = "$criteriaSelection and a.id=main_id and b.fish=c.id and b.fish_code=d.id and b.grade_id=e.id and a.weighment_challan_no is not null" ;
		
		if ($selectUnit!=0) 	 $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0)  $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="")	 $whr .= " and b.fish=".$fishId;		
		if ($processId!="")	 $whr .= " and b.fish_code=".$processId;
		
		$orderBy	= "a.weighment_challan_no asc, c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc";
		
		$qry		= "select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, a.payment_by, a.confirm, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, a.payment_confirm, a.payment_date, a.report_confirm, a.alpha_code from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, m_grade e";
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get Amount Symbol (Correct Smbol & Reverse Symbol)
	function convertAmountToSymbol($amount, $textLength)
	{
		$numLength = strlen($amount);
		
		$symbol = "";
		$totalDigit = 0;
		for ($i=0;$i<$numLength; $i++) {			
			$digit = substr($amount,$i,1);
			if ($i>0) $symbol .= "&nbsp;";
			//&sub;
			if ($digit==0) $symbol .= "<span style=\"font-size:7px;\"><></span>";
			for ($j=0;$j<$digit;$j++) {
				$symbol .= "-";
			}
			$totalDigit += $digit;
		}
		// Reverse Digit Symbol
		$revSymbol = "";
		for ($i=0;$i<$numLength; $i++) {			
			$revDigit = substr($amount,-($i+1),1);
			if ($i>0) $revSymbol .= "&nbsp;";
			if ($revDigit==0) $revSymbol .= "<span style=\"font-size:7px;letter-spacing:1px;\"><></span>";
			for ($j=0;$j<$revDigit;$j++) {
				$revSymbol .= "-";
			}			
		}
	
		$symbolLength = $totalDigit+$numLength;
		$divSpace = floor(($textLength-$symbolLength)/2);
		$extraSymobl = "";
		for ($i=0;$i<$divSpace;$i++) {
			$extraSymobl .= "-";			
		}		
		$sepSymbolLeft = 	"&nbsp;<span style=\"font-size:7px;\">></span>&nbsp;";
		$sepSymbolRight = 	"&nbsp;<span style=\"font-size:7px;\"><</span>&nbsp;";
		$cSymbol = $extraSymobl.$sepSymbolLeft.$symbol.$sepSymbolRight.$extraSymobl;
		$rSymbol = $extraSymobl.$sepSymbolLeft.$revSymbol.$sepSymbolRight.$extraSymobl;
		return array($cSymbol,$rSymbol);
	}

	# Check Challan Confirmed
	function chkChallanConfirmed($challanMainId)
	{		
		$qry = " select confirm from t_dailycatch_main where id='".$challanMainId."'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	# Check Zero Entry Exist
	function chkZeroEntryExist($challanMainId)
	{		
		$whr	= "a.id='".$challanMainId."' and b.fish is null and b.fish_code is null" ;
		$qry	= "select a.id, b.id from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id";		
		if ($whr!="") $qry .= " where ".$whr;
		//echo $qry."<br>";				
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Get PaymentBy  (payment By)
	function getPaymentType($challanMainId)
	{		
		$qry = " select payment_by from t_dailycatch_main where id='$challanMainId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get Supplier Challan Records
	function filterSupplierChallanRecords($challanMainId, $paymentType)
	{		

		if ($paymentType=='D') {
			$qry = " select distinct c.supplier_challan_no from  t_dailycatch_main a, t_dailycatchentry b, t_dailycatch_declared c where a.id=b.main_id and b.id=c.entry_id and a.id='$challanMainId'";
		} else {
			$qry = " select distinct a.supplier_challan_no from  t_dailycatch_main a, t_dailycatchentry b where a.id=b.main_id and a.id='$challanMainId' and supplier_challan_no!='' ";
		}

		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Supplier Memo 
	function getSupplierDeclaredWtRecords($challanMainId, $selSuppChallanNo)
	{	
		$whr	= "a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.id='$challanMainId' and e.supplier_challan_no='$selSuppChallanNo'" ;	
				
		$groupBy	= "b.fish, b.fish_code , e.decl_count";
		$orderBy	= "c.name asc, d.code asc, e.decl_count asc";				
		$qry		= "select a.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, sum(e.decl_wt), b.remarks from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry	.= " group by ". $groupBy;			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Supplier Challan Date (Using in Print Section)
	function getSupplierChallanDate($challanMainId, $selSuppChallanNo)
	{		
		$qry = "  select distinct c.supplier_challan_date, c.sub_supplier from  t_dailycatch_main a, t_dailycatchentry b, t_dailycatch_declared c where a.id=b.main_id and b.id=c.entry_id and a.id='$challanMainId' and c.supplier_challan_no='$selSuppChallanNo'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";
	}

	# Update Billing Company Rec
	function  updateBillingCompanyRec($weighNumber, $billingCompanyId)
	{
		$qry	= " update t_dailycatch_main set billing_company_id='$billingCompanyId' where weighment_challan_no='$weighNumber'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function chkBillingCompanySelected($weighNumber)
	{
		$qry = "select billing_company_id from t_dailycatch_main where weighment_challan_no='$weighNumber'";
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]>0)?$rec[0]:"";
	}	
	# Get Effective Wt Supplier Challan Dt $selSuppChallanNo
	function getEffectiveWtSupChallanDt($challanMainId)
	{			
		$qry = "  select distinct a.sub_supplier from t_dailycatch_main a where a.id='$challanMainId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Find Number of Supplier
	function numOfSubSupplier($challanMainId)
	{		
		$qry = "select  distinct d.sub_supplier, b.name from t_dailycatch_main a, m_subsupplier b, t_dailycatchentry c, t_dailycatch_declared d where a.id=c.main_id and d.sub_supplier=b.id and a.weighment_challan_no is not null and c.id=d.entry_id and a.id='$challanMainId' order by b.name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>1)?true:false; // Size >1 Don't allow to print
	}

	# Get Selected Billing Company Recs
	function getSelBillingCompanyRecs($weighNumber)
	{
		$qry = "select b.id, b.display_name from t_dailycatch_main a, m_billing_company b where a.billing_company_id=b.id and a.weighment_challan_no='$weighNumber' order by b.name asc";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get Challan Main Id
	function getChallanMainId($weighNumber, $billingCompanyId)	
	{
		$qry = "select id from t_dailycatch_main where weighment_challan_no='$weighNumber' and billing_company_id='$billingCompanyId' ";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Get Billing Company Id
	function getBillingCompanyId($weighNumber)	
	{
		$qry = "select billing_company_id from t_dailycatch_main where weighment_challan_no='$weighNumber'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	#Fetch Weighment Challan Records
	function fetchBillingCompanyRecords($selectSupplier, $fromDate, $tillDate)
	{
		$whr	= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and a.billing_company_id=b.id";
		
		if ($selectSupplier) $whr .= " and a.main_supplier='".$selectSupplier."'";
		$orderBy	= " a.weighment_challan_no asc";
			
		$qry = " select distinct b.id, b.display_name from t_dailycatch_main a, m_billing_company b  ";
			
		if ($whr!="")		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getBillCompanyRecId($challanMainId)	
	{
		$qry = "select billing_company_id, weighment_challan_no, alpha_code from t_dailycatch_main where id='$challanMainId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1],$result[0][2]):array();
	}

	function chkBillingCmpnyRecs($weighNumber)
	{
		$qry = "select b.id, b.display_name from t_dailycatch_main a, m_billing_company b where a.billing_company_id=b.id and a.weighment_challan_no='$weighNumber' order by b.name asc";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);		
		return $result;
	}

	# Update Raw Material Purchase Memo print Count
	function updateRMPMemoPrintCount($challanMainId)
	{
		$qry	= " update t_dailycatch_main set rmp_memo_print_count=rmp_memo_print_count+1 where id='$challanMainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

}	
?>