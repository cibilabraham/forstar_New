<?php
Class TransporterAccount
{

	/****************************************************************
	This class deals with all the operations relating to Settlement Summary
	*****************************************************************/
	var $databaseConnect;
	var $searchResult;

	//Constructor, which will create a db instance for this class
	function TransporterAccount(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select distinct Supplier
	function fetchTransporterRecords($fromDate, $tillDate)
	{
		$qry	= "select distinct a.transporter_id, b.name from t_salesorder a, m_transporter b where a.transporter_id=b.id and a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate' and a.complete_status='C' order by b.name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# GetRecords based on date and Transporter
	function fetchTransporterInvoicePagingRecords($selTransporter, $fromDate, $tillDate, $offset, $limit, $billType)
	{
		$whr	= " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.complete_status='C' and mds.state_id=a.state_id and mdc.city_id=a.city_id";

		if ($selTransporter) $whr .= " and a.transporter_id='$selTransporter'";
		
		if ($billType=='OC') $whr .= " and mds.octroi_applicable='Y'";
			
		//$orderBy	= " a.dispatch_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";
		//date, distribut, docket no sort , city, invoice no
		$orderBy	= " a.dispatch_date asc, b.name asc, a.docket_no asc, d.name, a.so asc";

		$groupBy = " a.id ";

		$limit	= " $offset, $limit ";

		$qry	= "select 
				distinct a.id, a.so, a.distributor_id, a.invoice_date, a.dispatch_date, a.state_id, a.total_amt, a.tax_type, a.tax_amt, (a.grand_total_amt+a.round_value), a.city_id, a.billing_frm, a.tax_applied, a.net_wt, a.gross_wt, a.num_box, a.round_value, a.transporter_id, a.docket_no, b.name, c.name, d.name, a.transporter_rate_list_id, a.bill_no, a.settled, a.settled_date, mds.octroi_applicable, mds.octroi_percent, a.trans_oc_rate_list_id, tm.name, tm.bill_required, a.transporter_actual_amt, a.invoice_type, a.proforma_no, a.sample_invoice_no, a.oc_service_tax_rate, a.oc_t_grand_total, a.oc_t_actual_cost, a.oc_bill_no, a.oc_settled, a.oc_settled_date, mds.octroi_exempted, a.tptr_od_re_setld_date, a.tptr_oc_re_setld_date, a.delivery_date, a.oda_applicable, a.oda_rate 
				from 
				t_salesorder a join m_distributor b on a.distributor_id=b.id 
				join m_distributor_state mds on b.id = mds.distributor_id 
				join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id 
				join m_distributor_area mda on mda.dist_city_entry_id=mdc.id and (mda.area_id=a.area_id or a.area_id=0)
				join m_state c on a.state_id=c.id 
				join m_city d on a.city_id=d.id 
				left join m_transporter tm on a.transporter_id=tm.id ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="")	$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
		if ($limit!="")		$qry .= " limit ".$limit;
		
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);		
		$this->searchResult = $result;
		return $result;	
	}
	/*
	OLD
	function fetchTransporterInvoicePagingRecords($selTransporter, $fromDate, $tillDate, $offset, $limit, $billType)
	{
		$whr	= " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id and a.complete_status='C' and mds.state_id=a.state_id and mdc.city_id=a.city_id";

		if ($selTransporter) $whr .= " and a.transporter_id='$selTransporter'";

		
		#if ($billType=='OD') $whr .= " and mds.octroi_applicable='N'";
		#else $whr .= " and mds.octroi_applicable='Y'";
		
		if ($billType=='OC') $whr .= " and mds.octroi_applicable='Y'";
		
	
		$orderBy	= " a.dispatch_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";

		$limit	= " $offset, $limit ";

		$qry	= "select 
				distinct a.id, a.so, a.distributor_id, a.invoice_date, a.dispatch_date, a.state_id, a.total_amt, a.tax_type, a.tax_amt, (a.grand_total_amt+a.round_value), a.city_id, a.billing_frm, a.tax_applied, a.net_wt, a.gross_wt, a.num_box, a.round_value, a.transporter_id, a.docket_no, b.name, c.name, d.name, a.transporter_rate_list_id, a.bill_no, a.settled, a.settled_date, mds.octroi_applicable, mds.octroi_percent, a.trans_oc_rate_list_id, tm.name, tm.bill_required, a.transporter_actual_amt, a.invoice_type, a.proforma_no, a.sample_invoice_no, a.oc_service_tax_rate, a.oc_t_grand_total, a.oc_t_actual_cost, a.oc_bill_no, a.oc_settled, a.oc_settled_date, mds.octroi_exempted, a.tptr_od_re_setld_date, a.tptr_oc_re_setld_date, a.delivery_date, a.oda_applicable, a.oda_rate 
				from t_salesorder a, m_distributor b join m_distributor_state mds on b.id = mds.distributor_id  join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id, m_state c, m_city d left join m_transporter tm on a.transporter_id=tm.id ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
		if ($limit!="")		$qry .= " limit ".$limit;			
		
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);		
		$this->searchResult = $result;
		return $result;	
	}
	function filterTransporterInvoiceRecords($selTransporter, $fromDate, $tillDate, $billType)
	{
		$whr		= " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.distributor_id=b.id and a.state_id=c.id and a.city_id=d.id and a.complete_status='C' and mds.state_id=a.state_id and mdc.city_id=a.city_id";

		if ($selTransporter) $whr .= " and a.transporter_id='$selTransporter'";
		# if ($billType=='OD') $whr .= " and mds.octroi_applicable='N'";
		# else $whr .= " and mds.octroi_applicable='Y'";
		
		if ($billType=='OC') $whr .= " and mds.octroi_applicable='Y'";
	
		$orderBy	= " a.dispatch_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";

		$qry	= "select distinct a.id, a.so, a.distributor_id, a.invoice_date, a.dispatch_date, a.state_id, a.total_amt, a.tax_type, a.tax_amt, (a.grand_total_amt+a.round_value), a.city_id, a.billing_frm, a.tax_applied, a.net_wt, a.gross_wt, a.num_box, a.round_value, a.transporter_id, a.docket_no, b.name, c.name, d.name, a.transporter_rate_list_id, a.bill_no, a.settled, a.settled_date, mds.octroi_applicable, mds.octroi_percent, a.trans_oc_rate_list_id, tm.name, tm.bill_required, a.transporter_actual_amt, a.invoice_type, a.proforma_no, a.sample_invoice_no, a.oc_service_tax_rate, a.oc_t_grand_total, a.oc_t_actual_cost, a.oc_bill_no, a.oc_settled, a.oc_settled_date, mds.octroi_exempted, a.tptr_od_re_setld_date, a.tptr_oc_re_setld_date, a.delivery_date, a.oda_applicable, a.oda_rate from t_salesorder a, m_distributor b join m_distributor_state mds on b.id = mds.distributor_id  join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id, m_state c, m_city d left join m_transporter tm on a.transporter_id=tm.id";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/
	# GetRecords based on date and Transporter
	function filterTransporterInvoiceRecords($selTransporter, $fromDate, $tillDate, $billType)
	{
		$whr		= " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.complete_status='C' and mds.state_id=a.state_id and mdc.city_id=a.city_id";

		if ($selTransporter) $whr .= " and a.transporter_id='$selTransporter'";
		
		# if ($billType=='OD') $whr .= " and mds.octroi_applicable='N'";
		# else $whr .= " and mds.octroi_applicable='Y'";
		
		if ($billType=='OC') $whr .= " and mds.octroi_applicable='Y'";
	
		$orderBy	= " a.dispatch_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";
		
		$groupBy = " a.id ";

		$qry	= "select distinct a.id, a.so, a.distributor_id, a.invoice_date, a.dispatch_date, a.state_id, a.total_amt, a.tax_type, a.tax_amt, (a.grand_total_amt+a.round_value), a.city_id, a.billing_frm, a.tax_applied, a.net_wt, a.gross_wt, a.num_box, a.round_value, a.transporter_id, a.docket_no, b.name, c.name, d.name, a.transporter_rate_list_id, a.bill_no, a.settled, a.settled_date, mds.octroi_applicable, mds.octroi_percent, a.trans_oc_rate_list_id, tm.name, tm.bill_required, a.transporter_actual_amt, a.invoice_type, a.proforma_no, a.sample_invoice_no, a.oc_service_tax_rate, a.oc_t_grand_total, a.oc_t_actual_cost, a.oc_bill_no, a.oc_settled, a.oc_settled_date, mds.octroi_exempted, a.tptr_od_re_setld_date, a.tptr_oc_re_setld_date, a.delivery_date, a.oda_applicable, a.oda_rate 
		from t_salesorder a join m_distributor b on a.distributor_id=b.id 
		join m_distributor_state mds on b.id = mds.distributor_id 
		join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id 
		join m_distributor_area mda on mda.dist_city_entry_id=mdc.id and (mda.area_id=a.area_id or a.area_id=0)
		join m_state c on a.state_id=c.id 
		join m_city d on a.city_id=d.id 
		left join m_transporter tm on a.transporter_id=tm.id";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="")	$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Transporter Wt Slab Rate
	function getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $totalWt)
	{
		/* Hide on 13-07-09 After Transporter wise weight slab
		$qry = " 
			 select b.rate from m_transporter_rate a, m_transporter_rate_entry b, m_weight_slab c, m_area_demarcation d, m_area_demarcation_state e, m_area_demarcation_city f 
			 where 
				a.id=b.main_id and a.transporter_id='$transporterId' 
				and a.rate_list_id='$transporterRateListId' 
				and c.id=b.weight_slab_id 
				and ('$totalWt' between c.wt_from and c.wt_to)
				and a.zone_id=d.zone_id
				and d.id=e.main_id and e.state_id='$stateId'
				and e.id = f.demarcation_state_id and (f.city_id='$cityId' or f.city_id=0)
			";
		*/
		/* Hide on 16-07-09
		$qry = " 
			 select b.rate, b.id, a.rate_type from m_transporter_rate a, m_transporter_rate_entry b, m_trptr_wt_slab_entry twtSlab, m_weight_slab c, m_area_demarcation d, m_area_demarcation_state e, m_area_demarcation_city f 
			 where 
				a.id=b.main_id and a.transporter_id='$transporterId' 
				and a.rate_list_id='$transporterRateListId' 
				and twtSlab.id= b.trptr_wt_slab_entry_id
				and c.id=twtSlab.wt_slab_id 
				and ('$totalWt' between c.wt_from and c.wt_to)
				and a.zone_id=d.zone_id
				and d.id=e.main_id and e.state_id='$stateId'
				and e.id = f.demarcation_state_id and (f.city_id='$cityId' or f.city_id=0)
			";
		*/

		$qry = " 
			 select b.rate, b.id, b.rate_type from m_transporter_rate a, m_transporter_rate_entry b, m_trptr_wt_slab_entry twtSlab, m_weight_slab c, m_area_demarcation_state e, m_area_demarcation_city f 
			 where 
				a.id=b.main_id and a.transporter_id='$transporterId' 
				and a.rate_list_id='$transporterRateListId' 
				and twtSlab.id= b.trptr_wt_slab_entry_id
				and c.id=twtSlab.wt_slab_id 
				and (('$totalWt' between c.wt_from and c.wt_to) or (if(c.wt_to=0 and '$totalWt' > c.wt_from, true, false )))				
				and a.zone_id=e.main_id and e.state_id='$stateId'
				and e.id = f.demarcation_state_id and (f.city_id='$cityId' or f.city_id=0)
				order by f.city_id desc
			";

		//echo "<br>$qry<br>";
		$rec	= $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?array(number_format($rec[0][0],2,'.',''),$rec[0][1], $rec[0][2]):array();
	}

	# Get Transporter Other Charges
	function getTransporterOtherCharges($transporterId, $transporterOCRateListId)
	{
		$qry = " select fov_charge, docket_charge, service_tax, octroi_service_charge, oda_charge, surcharge from m_transporter_other_charge where transporter_id='$transporterId' and rate_list_id='$transporterOCRateListId' ";
		//echo "<br>$qry";
		$rec	= $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3], $rec[4], $rec[5]):array();
	}

	#Update challan Paid status
	function updateTransporterPayment($salesOrderId, $adjustWt, $totalWt, $ratePerKg, $freightCost, $fovRate, $docketRate, $octroiRate, $transTotalAmt, $serviceTaxRate, $transGrandTotalAmt, $billNo, $settled, $actualCost, $billType, $odaRate, $surcharge)
	{
		$isSettled = ($settled=="")?N:$settled;
		
		# If Already Settled then Checking the Old Entry
		$setldLog = "";
		$valueChanged = false;
		if ($isSettled=='Y') {
			$cDate = date("Y-m-d");			
			list($acSettled, $acSetldDate, $acResetledDate, $acSetldHistory, $acTrptrActualCost, $acTotalWt) = $this->getTransporterSettledRec($salesOrderId, $billType);
			# On Resettlig Date Wt => Resettld Date:Prev Actual Cost
			$setldLog = $cDate.":".$acTotalWt.":".$acTrptrActualCost;
			$logHistory = "";
			if ($acSetldHistory!="") $logHistory = $acSetldHistory.",".$setldLog;
			else $logHistory = $setldLog;
			
			if ($acTrptrActualCost!=$actualCost || $totalWt!=$acTotalWt) $valueChanged = true;
		}	
	
		/*
		if ($isSettled=='Y')	$settledDate = "Now()";
		else			$settledDate = "0000-00-00";
		*/

		if ($billType=='OD') {
			//settled_date=$settledDate,
			$updateQry = "rate_per_kg='$ratePerKg', freight_cost='$freightCost', fov_rate='$fovRate', docket_rate='$docketRate', transporter_total_amt='$transTotalAmt', service_tax_rate='$serviceTaxRate', transporter_grand_total_amt='$transGrandTotalAmt', bill_no='$billNo', settled='$isSettled', transporter_actual_amt='$actualCost', t_od_bill='$billType', oda_rate='$odaRate', trptr_surcharge='$surcharge' "; 
			# Update AC
			if ($isSettled=='Y' && $acSettled=='Y' && $valueChanged)  $updateQry .= " ,tptr_od_re_setld_date=Now(), tptr_od_setld_history='$logHistory'";
			else if ($isSettled=='Y' && $acSettled=='N') $updateQry .= ",settled_date=NOW()";
			else if ($isSettled=='N') $updateQry .= " ,settled_date='0000-00-00'";
		} else if ($billType=='OC') {
			//oc_settled_date=$settledDate
			$updateQry = " t_oc_bill='$billType', octroi_rate='$octroiRate', oc_service_tax_rate='$serviceTaxRate', oc_t_grand_total='$transGrandTotalAmt', oc_t_actual_cost='$actualCost', oc_bill_no='$billNo', oc_settled='$isSettled' ";
			# Update AC
			if ($isSettled=='Y' && $acSettled=='Y' && $valueChanged)  $updateQry .= " ,tptr_oc_re_setld_date=Now(), tptr_oc_setld_history='$logHistory'";
			else if ($isSettled=='Y' && $acSettled=='N') $updateQry .= ",oc_settled_date=NOW()";
			else if ($isSettled=='N') $updateQry .= " ,oc_settled_date='0000-00-00'";
		}	

		$qry	= " update t_salesorder set adjust_wt='$adjustWt', total_wt='$totalWt', $updateQry  ";
		$qry .= " where id='$salesOrderId' ";
		//echo "<br>Update==><br>$qry";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();
		return $result;	
	}

	// ----------------------
	// Checking Transporter Account Already Settled
	// Return Settled, settled Date, Resettled Date, ReSettled history, actual cost
	// ----------------------
	function getTransporterSettledRec($salesOrderId, $billType)
	{
		if ($billType=='OC') $selQryField = "oc_settled, oc_settled_date, tptr_oc_re_setld_date, tptr_oc_setld_history, oc_t_actual_cost";
		else if ($billType=='OD') $selQryField = "settled, settled_date, tptr_od_re_setld_date, tptr_od_setld_history, transporter_actual_amt";
		
		$qry = "select $selQryField, total_wt from t_salesorder where id='$salesOrderId' ";
		//echo "<br>$qry<br>";	
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4], $rec[5]):"";
	}	


	function getTransporterOCRateList()
	{
		//$qry = " select ";	
	}

	# Round off Calculation	
	function getRoundoffVal($grandTotalAmt)
	{		
		$roundFig = FloatVal(substr($grandTotalAmt,-3));
		//echo $roundFig;
		//$roundVal = ($roundFig>=0.50 || $roundFig<=0.50)?(1-$roundFig):"";
		//$roundVal = number_format((1-$roundFig),2,'.','');
		$roundVal = (($roundFig>=0.50 || $roundFig<=0.50) && $roundFig!=0.00)?(1-$roundFig):"";
		return $roundVal;
	}

	# Check Bill no Required
	function chkBillNoRequired()
	{
		$bNotRequired = false;
		$transporterAccountRecs = $this->searchResult;
		if (sizeof($transporterAccountRecs)>0) {
			foreach ($transporterAccountRecs as $tr) {
				$billRequired = $tr[30];
				if ($billRequired=='N') $bNotRequired = true;
			}
		}
		return $bNotRequired;
	}

	# ---------------------------------------------------------------
	# There will be only one Document Charges and one ODA Charges for a single Docket No. with one /more invoices. It will not be charged for each invoice if they have gone against a single docket no.
	# --------------------------------------------------------------
	function getTrptrRecs($offset, $limit, $selTransporter, $fromDate, $tillDate,  $billType, $distributorId, $cityId, $docketNum)
	{
		$whr	= " (a.dispatch_date>='$fromDate' and a.dispatch_date<='$tillDate') and a.complete_status='C' and mds.state_id=a.state_id and mdc.city_id=a.city_id and a.distributor_id='$distributorId' and (a.city_id='$cityId' or a.city_id=0) and a.docket_no='$docketNum'";

		if ($selTransporter) $whr .= " and a.transporter_id='$selTransporter'";
		
		if ($billType=='OC') $whr .= " and mds.octroi_applicable='Y'";
			
		$orderBy	= " a.dispatch_date asc, a.so asc, a.grand_total_amt asc, b.name asc, d.name asc ";
		$groupBy	= " a.docket_no";

		$limit	= " $offset, $limit ";

		$qry	= "select sum(ceil(a.gross_wt)), count(*)
				from 
			  t_salesorder a join m_distributor b on a.distributor_id=b.id 
			  join m_distributor_state mds on b.id = mds.distributor_id
			  join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id 
			  join m_distributor_area mda on mda.dist_city_entry_id=mdc.id and mda.area_id=a.area_id 
                          join m_state c on a.state_id=c.id 
                          join m_city d on a.city_id=d.id 
			  left join m_transporter tm on a.transporter_id=tm.id ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
		
		if ($limit!="")		$qry .= " limit ".$limit;			
		
		//echo "<br>$docketNum=====>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?array($result[0][0],$result[0][1]):array(1,1);	
	}
	
	
}	
?>
