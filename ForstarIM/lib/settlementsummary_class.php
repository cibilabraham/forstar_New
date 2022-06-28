<?php
Class SettlementSummary
{

	/****************************************************************
	This class deals with all the operations relating to Settlement Summary
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function SettlementSummary(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select distinct Supplier
	function fetchSupplierRecords($fromDate, $tillDate)
	{
		$qry	= "select distinct a.main_supplier, c.id, c.name from t_dailycatch_main a, t_dailycatchentry b, supplier c where a.id=b.main_id and a.main_supplier=c.id and b.select_date>='$fromDate' and b.select_date<='$tillDate' and b.paid='Y' order by c.name asc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#GetRecords based on date and supplier
	function filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $billingCompany)
	{		

		$whr	= " a.id=b.main_id and a.main_supplier='$selectSupplier' and (a.select_date>='$fromDate' and a.select_date<='$tillDate') and a.flag=1 and b.actual_amount!=0 and b.paid!='N' ";

		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		$groupBy	= " a.weighment_challan_no, a.billing_company_id";

		$orderBy	= " a.billing_company_id asc, a.weighment_challan_no asc, a.select_date asc ";
		
		$qry	= " select a.id, a.weighment_challan_no, a.select_date, b.ice_wt, sum(b.actual_amount), a.payment_confirm, a.payment_date, a.billing_company_id, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b ";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($groupBy!="") $qry   .=" group by ".$groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Update challan Paid status
	function updateChallanPayment($challanEntryId, $paid)
	{
		$qry	=	"update t_dailycatch_main set";
		if ($paid=='Y') {
			$qry .= " payment_confirm='$paid', payment_date=Now(), payment_time=Now()";		
		} else if ($paid=='N') {
			$qry .= " payment_confirm='$paid', payment_date='0000-00-00', payment_time='0000-00-00 00:00:00'";		
		} else {
			$qry .="";
		}
		
		$qry .= "  where id='$challanEntryId' ";		

		$result	=	$this->databaseConnect->updateRecord($qry);
		
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
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

	#Fetch Challan billing company Records
	function fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier)
	{	

		$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and a.billing_company_id=bc.id and b.paid='Y' and a.main_supplier='".$selectSupplier."'";
		
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