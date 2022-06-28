<?php
class TransporterReport
{
	/****************************************************************
	This class deals with all the operations relating to Transporter Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function TransporterReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	
	# Get Records based on selection
	# $statusType => SD-Settled, NS -not settled, PD - paid
	function fetchTransporterInvoiceRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityIds, $billType, $statusType)
	{
		$whr		= " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id and mds.state_id=a.state_id and mdc.city_id=a.city_id ";
		//and a.paid='Y' and (a.settled='Y' or a.oc_settled='Y' )

		if ($billType=='OC') $whr .= " and mds.octroi_applicable='Y'";
		/*
		if ($billType=='OD') $whr .= " and mds.octroi_applicable='N'";
		else if ($billType=='OC') $whr .= " and mds.octroi_applicable='Y'";
		else $whr .= "";
		*/

		//if ($invoiceType=='S') $whr .= " and invoice_type='$invoiceType'";
		if ($invoiceType) 	$whr .= " and invoice_type='$invoiceType'";

		if ($selTransporter!="")	$whr .= " and a.transporter_id='$selTransporter' ";		
		if ($selDistributorId!="")	$whr .= " and a.distributor_id='$selDistributorId' ";		
		if ($selState!="")	$whr .= " and a.state_id='$selState' ";		
		if ($selCityIds!="")	$whr .= " and a.city_id in ($selCityIds) ";
		
		if ($billType=='OD' && $statusType=="SD") $whr .= " and a.settled='Y'"; 
		else if ($billType=='OD' && $statusType=="NS") $whr .= " and a.settled='N'"; 
		else if ($billType=='OC' && $statusType=="SD") $whr .= " and a.oc_settled='Y'"; 
		else if ($billType=='OC' && $statusType=="NS") $whr .= " and a.oc_settled='N'"; 
		else if ($billType=="" && $statusType!="") {
			if ($statusType=="SD") 		$whr .= " and (a.settled='Y' or a.oc_settled='Y')";
			else if ($statusType=="NS")	$whr .= " and (a.settled='N' and a.oc_settled='N')";
		}

		if ($statusType=='PD') $whr .= " and a.paid='Y' ";
			
		$orderBy	= " a.dispatch_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";
		$groupBy = " a.id ";

		$qry	= "select distinct 
				a.id, a.so, a.distributor_id, a.invoice_date, a.dispatch_date, a.state_id, a.total_amt, a.tax_type, a.tax_amt, (a.grand_total_amt+a.round_value), a.city_id, a.billing_frm, a.tax_applied, a.net_wt, a.gross_wt, a.num_box, a.round_value, a.transporter_id, a.docket_no, b.name, c.name, d.name, a.transporter_rate_list_id, a.bill_no, a.adjust_wt,  a.total_wt, a.rate_per_kg, a.freight_cost, a.fov_rate, a.docket_rate, a.octroi_rate, a.transporter_total_amt, a.service_tax_rate, a.transporter_grand_total_amt, a.settled, a.settled_date, mds.octroi_applicable, d.octroi_percent, a.transporter_actual_amt, a.invoice_type, a.proforma_no, a.sample_invoice_no, a.oc_service_tax_rate, a.oc_t_grand_total, a.oc_t_actual_cost, a.oc_bill_no, a.oc_settled, a.oc_settled_date, a.oda_applicable, a.oda_rate, a.trptr_surcharge
			from
				t_salesorder a join m_distributor b on a.distributor_id=b.id 
				join m_distributor_state mds on b.id = mds.distributor_id 
				join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id 
				join m_distributor_area mda on mda.dist_city_entry_id=mdc.id and (mda.area_id=a.area_id or a.area_id=0) 
				join m_state c on a.state_id=c.id 
				join m_city d on a.city_id=d.id
				";
				/*				
				t_salesorder a, m_distributor b join m_distributor_state mds on b.id = mds.distributor_id  join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id, m_state c, m_city d ";
				*/

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Dist City Recs
	function getDistributorCityRecs($distributorId)
	{
		$qry = " select distinct b.city_id, c.name from m_distributor_state a, m_distributor_city b, m_city c where a.id=b.dist_state_entry_id and b.city_id=c.id and a.distributor_id='$distributorId' order by c.name asc ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getSelDistCityRecords($distributorId, $selCityId)
	{
		$qry = " select distinct b.city_id, c.name from m_distributor_state a, m_distributor_city b, m_city c where a.id=b.dist_state_entry_id and b.city_id=c.id and a.distributor_id='$distributorId' order by c.name asc ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);

		$resultArr = array();
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		$qry1 = " select distinct b.city_id, c.name from m_distributor_state a, m_distributor_city b, m_city c where a.id=b.dist_state_entry_id and b.city_id=c.id and a.distributor_id='$distributorId' and b.city_id in ($selCityId) order by c.name asc ";
		//echo "<br/>$qry1<br/>";
		$result2 = $this->databaseConnect->getRecords($qry1);
		$resultArr1 = array();
		while (list(,$v) = each($result2)) {
			$resultArr1[$v[0]] = $v[1];
		}
		$i = 0;
		//$newArr = array();
		if (sizeof($resultArr)>0) {
			$newArr[$i] = array('','-- Select --','');
			foreach ($resultArr as $cityId=>$cityName) {
				$i++;
				$selCId= "";
				if (array_key_exists($cityId,$resultArr1)) $selCId = $cityId;
				$newArr[$i] = array($cityId,$cityName, $selCId);
			}
		}
		return $newArr;
	}

	# Get State List
	function getStateRecords($fromDate, $tillDate, $invoiceType, $statusType)
	{
		$whr = " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.state_id=b.id ";

		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";	

		if ($statusType=="SD") 		$whr .= " and (a.settled='Y' or a.oc_settled='Y')";
		else if ($statusType=="NS")	$whr .= " and (a.settled='N' and a.oc_settled='N')";
		else if ($statusType=='PD') 	$whr .= " and a.paid='Y' ";

		$orderBy	= " b.name asc ";

		$qry	= "select distinct a.state_id, b.name from t_salesorder a, m_state b";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

	# Get dist List
	function getDistributorList($fromDate, $tillDate, $invoiceType, $statusType)
	{
		$whr = " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.distributor_id=b.id ";

		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";

		if ($statusType=="SD") 		$whr .= " and (a.settled='Y' or a.oc_settled='Y')";
		else if ($statusType=="NS")	$whr .= " and (a.settled='N' and a.oc_settled='N')";
		else if ($statusType=='PD') 	$whr .= " and a.paid='Y' ";
	
		$orderBy	= " b.name asc ";

		$qry	= "select distinct a.distributor_id, b.name from t_salesorder a, m_distributor b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;	
	}

	# Get Transporter List
	function getTransporterList($fromDate, $tillDate, $invoiceType, $statusType)
	{
		$whr	= " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.transporter_id=b.id";
		

		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";

		if ($statusType=="SD") 		$whr .= " and (a.settled='Y' or a.oc_settled='Y')";
		else if ($statusType=="NS")	$whr .= " and (a.settled='N' and a.oc_settled='N')";
		else if ($statusType=='PD') 	$whr .= " and a.paid='Y' ";

		$orderBy = " b.name asc ";
		$qry	 = "select a.transporter_id, b.name from t_salesorder a, m_transporter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;					

		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

}
?>
