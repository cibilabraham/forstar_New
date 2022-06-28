<?php
class SalesOrderReport
{
	/****************************************************************
	This class deals with all the operations relating to Sales Order Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SalesOrderReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Get Records based on selection
	function fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityIds, $selStatus, $dateSelFrom)
	{
		//$whr	= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id  ";

		if ($dateSelFrom=='DSD') $whr = " (a.last_date>='$fromDate' and a.last_date<='$tillDate') ";
		else if ($dateSelFrom=='DED') $whr = " (a.delivery_date>='$fromDate' and a.delivery_date<='$tillDate') ";
		else $whr = " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') ";

		$whr	.= "  and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id  ";

		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";
		if ($selTransporter!="")	$whr .= " and a.transporter_id='$selTransporter' ";
		if ($selDistributorId!="")	$whr .= " and a.distributor_id='$selDistributorId' ";
		if ($selState!="")	$whr .= " and a.state_id='$selState' ";		
		if ($selCityIds!="")	$whr .= " and a.city_id in ($selCityIds) ";
		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";
	
		if ($reportType=='TRAN' && $selTransporter=="") $whr .= " and a.transporter_id is not null ";
	
		$orderBy	= " a.invoice_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";

		//if (a.so!=0 and a.invoice_type='T',a.invoice_date, entry_date)

		$qry	= "select a.id, a.so, a.distributor_id, a.invoice_date, a.dispatch_date, a.state_id, a.grand_total_amt, a.city_id, a.net_wt, a.gross_wt, a.num_box, a.transporter_id, a.docket_no, b.name, c.name, d.name, a.complete_status, ROUND((a.grand_total_amt+a.round_value),2), a.last_date, a.extended, a.invoice_type, a.proforma_no, a.sample_invoice_no, a.pkng_confirm, a.gpass_confirm, a.delivery_date, a.delivery_remarks, a.tax_type from t_salesorder a, m_distributor b, m_state c, m_city d ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Records based on selection
	function getPeriodTypeRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityIds, $selStatus, $periodType)
	{
		$whr		= " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id  ";

		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";
		if ($selTransporter!="")	$whr .= " and a.transporter_id='$selTransporter' ";		
		if ($selDistributorId!="")	$whr .= " and a.distributor_id='$selDistributorId' ";
		if ($selState!="")	$whr .= " and a.state_id='$selState' ";		
		if ($selCityIds!="")	$whr .= " and a.city_id in ($selCityIds) ";
		if ($selStatus=='C')		$whr .= " and a.complete_status='C'";	
		else if ($selStatus=='P')	$whr .= " and (a.complete_status<>'C' or a.complete_status is null) ";	
		if ($reportType=='TRAN' && $selTransporter=="") $whr .= " and a.transporter_id is not null ";

		$groupBy	= " EXTRACT(YEAR_MONTH FROM a.invoice_date)";
		
		$orderBy	= " a.invoice_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";
				

		$qry	= "select MONTHNAME(a.invoice_date), sum(ROUND((a.grand_total_amt+a.round_value),2))  from t_salesorder a, m_distributor b, m_state c, m_city d ";

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

	# Update SO Status
	function updateSalesOrderStatus($soNum, $invType, $soYear)
	{
		$whr = " invoice_type='$invType' and so_year='$soYear'";
		$uptdQry = "";
		if ($invType=='T') $whr .= " and so = '$soNum' ";
		else if ($invType=='S') {
			$whr .= " and sample_invoice_no='$soNum' ";
			$uptdQry = " , so=0 ";
		}		
		
		$qry = " update t_salesorder set status='P', complete_status='P', settled='N', settled_date='0', paid='N', paid_date='0', paid_time='0', pkng_confirm='N', gpass_confirm='N' ";

		if ($uptdQry!="") $qry .= $uptdQry;
		if ($whr)	  $qry .= " where ".$whr;
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Get SO Id
	function getSOId($soNum, $soYear, $invType)
	{
		//$qry = " select id from t_salesorder where so='$soNum' and so_year='$soYear' ";
		
		$whr = " invoice_type='$invType' and so_year='$soYear'";

		if ($invType=='T') $whr .= " and so = '$soNum' ";
		else if ($invType=='S') $whr .= " and sample_invoice_no='$soNum' ";

		$qry = " select id from t_salesorder ";
		if ($whr) $qry .= " where ".$whr;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Get State List
	function getSOStateRecords($fromDate, $tillDate, $invoiceType, $selStatus)
	{
		$whr = " (a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate') and a.state_id=b.id ";
		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";
	
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

		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";

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

		if ($invoiceType!="") $whr .= " and invoice_type='$invoiceType'";

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

	# Check valid/ Existing challan
	function chkValidInvoiceNo($invoiceNo)
	{
		$qry = " select id from t_salesorder where so='$invoiceNo'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	# Update New Invoice no
	function updateNewInvoiceNo($existingInvoiceId, $newInvoiceNo)
	{
		$qry = " update t_salesorder set so='$newInvoiceNo' where id='$existingInvoiceId' ";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}
	# Check Valid Number (with in the sequence)
	function validSONum($soNum)
	{	
		$selDate = date('Y-m-d');
		$qry	= "select start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO' and start_no<='$soNum' and end_no>='$soNum' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# get SO Year Recs
	function getSOYearList()
	{
		$qry = "select YEAR(invoice_date) from t_salesorder group by EXTRACT(YEAR from invoice_date) order by EXTRACT(YEAR from invoice_date) desc";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update in Packing Details/ gate Pass
	function updateSOInvoiceOtherRec($soId)
	{
		$qryPkgDetails = " update t_pkng_inst set confirm_status='P' where so_id='$soId'";
		//echo $qryPkgDetails;
		$pkgDetailsResult = $this->databaseConnect->updateRecord($qryPkgDetails);

		$qryGatePass = " update t_gate_pass set confirm_status='P' where so_id='$soId'";
		//echo $qryPkgDetails;
		$gatePassResult = $this->databaseConnect->updateRecord($qryGatePass);

		if ($pkgDetailsResult && $gatePassResult)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return ($pkgDetailsResult && $gatePassResult)?true:false;	
	}
}
?>