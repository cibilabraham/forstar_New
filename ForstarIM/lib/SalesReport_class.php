<?php
class SalesReport
{
	/****************************************************************
	This class deals with all the operations relating to Sales Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SalesReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Get Records based on selection
	function fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityIds, $selStatus)
	{
		$whr		= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id  ";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";			

		if ($selTransporter!="")	$whr .= " and a.transporter_id='$selTransporter' ";
		
		if ($selDistributorId!="")	$whr .= " and a.distributor_id='$selDistributorId' ";		

		if ($selState!="")	$whr .= " and a.state_id='$selState' ";
		
		if ($selCityIds!="")	$whr .= " and a.city_id in ($selCityIds) ";		

		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";
	
		if ($reportType=='TRAN' && $selTransporter=="") $whr .= " and a.transporter_id is not null ";
	
		$orderBy	= " a.invoice_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";

		$qry	= "select a.id, a.so, a.distributor_id, a.invoice_date, a.dispatch_date, a.state_id, a.grand_total_amt, a.city_id, a.net_wt, a.gross_wt, a.num_box, a.transporter_id, a.docket_no, b.name, c.name, d.name, a.complete_status, ROUND((a.grand_total_amt+a.round_value),2), a.last_date, a.extended, a.invoice_type, a.proforma_no, a.sample_invoice_no from t_salesorder a, m_distributor b, m_state c, m_city d ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Records based on selection
	function getPeriodTypeRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityIds, $selStatus, $periodType, $selZoneId, $selSOCityId)
	{
		$whr		= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id  ";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";
		if ($selTransporter!="")	$whr .= " and a.transporter_id='$selTransporter' ";		
		if ($selDistributorId!="")	$whr .= " and a.distributor_id='$selDistributorId' ";
		if ($selState!="")	$whr .= " and a.state_id='$selState' ";		
		if ($selCityIds!="")	$whr .= " and a.city_id in ($selCityIds) ";
		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";	
		if ($reportType=='TRAN' && $selTransporter=="") $whr .= " and a.transporter_id is not null ";
		$cityIds = "";
		if ($selZoneId) {
			$cityIds = $this->getZoneWiseCityList($selZoneId);
			$whr .= " and a.city_id in ($cityIds) ";
		}

		if ($selSOCityId!="") $whr .= " and a.city_id='$selSOCityId' ";

		if ($periodType=='M') $groupBy	= " EXTRACT(YEAR_MONTH FROM a.invoice_date)";
		else if ($periodType=='Y') $groupBy	= " EXTRACT(YEAR FROM a.invoice_date)";
		
		$orderBy	= " a.invoice_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";
				

		$qry	= "select MONTHNAME(a.invoice_date), YEAR(a.invoice_date), sum(ROUND((a.grand_total_amt+a.round_value),2))  from t_salesorder a, m_distributor b, m_state c, m_city d ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
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
		$resultArr = array(''=>'-- Select All --');
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
			$newArr[$i] = array('','-- Select All --','');
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
	function getSOStateRecords($fromDate, $tillDate, $invoiceType, $selStatus)
	{
		$whr = " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.state_id=b.id ";
		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";
	
		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";

		$orderBy	= " b.name asc ";

		$qry	= "select distinct a.state_id, b.name from t_salesorder a, m_state b";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select All --');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

	# Get dist List
	function getDistributorList($fromDate, $tillDate, $invoiceType, $selStatus)
	{
		$whr = " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.distributor_id=b.id ";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";

		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";
	
		$orderBy	= " b.name asc ";

		$qry	= "select distinct a.distributor_id, b.name from t_salesorder a, m_distributor b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select All --');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;	
	}

	# Get Transporter List
	function getTransporterList($fromDate, $tillDate, $invoiceType, $selStatus)
	{
		$whr	= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.transporter_id=b.id";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";

		if ($selStatus=='C')	  $whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P') $whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";

		$orderBy = " b.name asc ";
		$qry	 = "select a.transporter_id, b.name from t_salesorder a, m_transporter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;					
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select All --');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}


	# Get Transporter List
	function getZoneList($fromDate, $tillDate, $invoiceType, $selStatus)
	{
		/*
		$whr	= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.transporter_id=b.transporter_id and a.transporter_rate_list_id=b.rate_list_id and b.zone_id=c.id";
		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";
		if ($selStatus=='C')	  $whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P') $whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";
		$orderBy = " c.name asc ";
		$qry	 = "select b.zone_id, c.name from t_salesorder a, m_transporter_rate b, m_zone c";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		*/
		
		$whr	= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.transporter_id is not null ";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";
		if ($selStatus=='C')	  $whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P') $whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";
		
		$groupBy	= " a.transporter_id, a.transporter_rate_list_id, a.state_id, a.city_id ";
		
		$qry	 = "select a.transporter_id , a.transporter_rate_list_id, a.state_id, a.city_id from t_salesorder a";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;					
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		
		//$zoneArr = array();
		$zoneArr = array(''=>'-- Select All --');
		foreach ($result as $sor) {
			$transporterId 		= $sor[0];
			$transporterRateListId	= $sor[1];
			$stateId		= $sor[2];
			$cityId			= $sor[3];
			$trptrZoneRecs = $this->getTransporterZone($transporterId, $transporterRateListId, $stateId, $cityId);
			$zoneId = "";			
			$zName = "";
			foreach ($trptrZoneRecs as $zr) {
				$zoneId = $zr[0];
				$zName	= $zr[1];
				$zoneArr[$zoneId] = $zName;
			}
		}
		asort($zoneArr);		
		return $zoneArr;
	}
	# Get Transporter Wt Slab Rate
	function getTransporterZone($transporterId, $transporterRateListId, $stateId, $cityId)
	{
		$qry = " 
			 select a.zone_id, mz.name from (m_transporter_rate a, m_transporter_rate_entry b, m_trptr_wt_slab_entry twtSlab, m_weight_slab c, m_area_demarcation_state e, m_area_demarcation_city f) left join m_zone mz on a.zone_id=mz.id  
			 where 
				a.id=b.main_id and a.transporter_id='$transporterId' 
				and a.rate_list_id='$transporterRateListId' 
				and twtSlab.id= b.trptr_wt_slab_entry_id
				and c.id=twtSlab.wt_slab_id 
				and a.zone_id=e.main_id and e.state_id='$stateId'
				and e.id = f.demarcation_state_id and (f.city_id='$cityId' or f.city_id=0)
			group by a.zone_id
			";

		//echo "<br>$qry<br>";
		$rec	= $this->databaseConnect->getRecords($qry);
		//return (sizeof($rec)>0)?array($rec[0][0]):array();
		return $rec;
	}

	# Zone Wise City List
	function getZoneWiseCityList($zoneId)
	{
		//$qry = " select mc.id, mc.name from m_zone mz, m_area_demarcation_state mads, m_area_demarcation_city madc, m_city mc where mz.id=mads.main_id and mads.id=madc.demarcation_state_id and (mc.id=madc.city_id or mads.state_id=mc.state_id) and mz.id='$zoneId' order by mc.name asc";
		$qry = " select mc.id, mc.name from m_zone mz, m_area_demarcation_state mads, m_area_demarcation_city madc, m_city mc where mz.id=mads.main_id and mads.id=madc.demarcation_state_id and IF(madc.city_id=0,(mads.state_id=mc.state_id),(mc.id=madc.city_id) ) and mz.id='$zoneId' order by mc.name asc";		
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array();
		$i = 0;
		foreach ($result as $r) {
			$cityId = $r[0];
			$resultArr[$i] = $cityId;
			$i++;
		}

		return implode(",", $resultArr);
	}

	# Dist City Recs
	function getSOCityRecs($fromDate, $tillDate, $invoiceType, $selStatus)
	{	

		$whr	= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.city_id=b.id ";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";
		if ($selStatus=='C')	  $whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P') $whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";
		
		$orderBy = " b.name asc";
		
		$qry	 = "select distinct a.city_id, b.name from t_salesorder a, m_city b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select All --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get Selected  products
	function getSelProducts($fromDate, $tillDate, $invoiceType, $selStatus)
	{
		$whr = " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.id=b.salesorder_id and b.product_id=c.id";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";
		if ($selStatus=='C')	  $whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P') $whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";
		
		$groupBy  = " b.product_id ";
		$orderBy = " c.name asc";
		
		$qry	 = "select b.product_id, c.name as product from t_salesorder a, t_salesorder_entry b, m_product_manage c";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Records based on selection
	function getPeriodWiseProductRecs($fromDate, $tillDate, $invoiceType, $selStatus, $periodType, $productId, $extractMonth, $totNetWt, $totNumPack)
	{
		$whr	= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.id=b.salesorder_id and b.product_id=c.id and b.product_id='$productId'";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";		

		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";	
		
		if ($periodType=='M') {
			$groupBy	= " EXTRACT(YEAR_MONTH FROM a.invoice_date) ";
			$whr		.= " and EXTRACT(YEAR_MONTH FROM a.invoice_date)='$extractMonth'";
		} else if ($periodType=='Y') {
			$groupBy	= " EXTRACT(YEAR FROM a.invoice_date)";
			$whr		.= " and EXTRACT(YEAR FROM a.invoice_date)='$extractMonth'";
		}
		
		$updateQry = "";
		if ($totNetWt) $updateQry = "ROUND((sum(b.quantity+b.free_pkts)*c.net_wt)/1000,2 )";
		else if ($totNumPack) $updateQry = " sum(b.quantity+b.free_pkts) ";
		
		//$orderBy	= "";
				
		$qry	= "select $updateQry from t_salesorder a, t_salesorder_entry b, m_product_manage c";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}


	# Get Records based on selection
	function getPeriod($fromDate, $tillDate, $invoiceType, $selStatus, $periodType)
	{
		$whr		= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate')";

		if ($invoiceType!="") $whr .= " and a.invoice_type='$invoiceType'";		

		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";	
		
		if ($periodType=='M') {
			$groupBy	= " EXTRACT(YEAR_MONTH FROM a.invoice_date) ";
			$updateQry 	= " concat(DATE_FORMAT(a.invoice_date, '%b'),'', DATE_FORMAT(a.invoice_date, '%y')), EXTRACT(YEAR_MONTH FROM a.invoice_date) ";
		} else if ($periodType=='Y') {
			$groupBy	= " EXTRACT(YEAR FROM a.invoice_date)";	
			$updateQry 	= " EXTRACT(YEAR FROM a.invoice_date), EXTRACT(YEAR FROM a.invoice_date) ";	
		}
		//$orderBy	= "";				
		$qry	= "select $updateQry from t_salesorder a";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
				
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	
}
?>