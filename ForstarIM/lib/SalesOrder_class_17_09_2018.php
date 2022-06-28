<?php
class SalesOrder
{  
	/****************************************************************
	This class deals with all the operations relating to Sales Order
	*****************************************************************/
	var $databaseConnect;
	var $productActiveRecArr = array();

	
	//Constructor, which will create a db instance for this class
	function SalesOrder(&$databaseConnect, &$stateVatRateListObj)
    {
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert SO Rec
	function addSalesOrder($salesOrderNo, $selDistributorId, $selState, $selCity,  $lastDate, $totalAmount, $taxType, $billingForm, $selTax, $totalTaxAmt, $grandTotalAmt, $productPriceRateListId, $distMgnRateListId, $userId, $soRemark, $netWt, $grossWt, $numBox, $roundVal, $invoiceType, $additionalItemTotalWt, $poNo, $selArea, $invoiceDate, $poDate, $challanNo, $challanDate, $discount, $discountRemark, $discountPercent, $discountAmt, $octroiExempted, $oecNo, $oecValidDate, $oecIssuedDate, $proformaInvoiceNo, $proformaInvoiceDate, $sampleInvoiceNo, $sampleInvoiceDate, $entryDate, $soYear, $exDutyActive, $eduCessPercent, $eduCessRLId, $secEduCessPercent, $secEduCessRLId, $totExDuty, $totEduCess, $totSecEduCess, $totCentralExDuty, $transChargeActive, $transporterCharge, $billingType,$company,$unit,$number_gen_id)
	{
		$qry = "insert into t_salesorder (so , distributor_id, invoice_date, createdby, status, last_date, rate_list_id, state_id, total_amt, tax_type, tax_amt, grand_total_amt, dist_mgn_ratelist_id, city_id, billing_frm, tax_applied, remark, net_wt, gross_wt, num_box, round_value, invoice_type, adnl_item_total_wt, po_no, area_id, created_on, po_date, challan_no, challan_date, discount, discount_remark, discount_percent, discount_amt, octroi_exempted, oec_no, oec_date, oec_issued_date, proforma_no, proforma_date, sample_invoice_no, sample_invoice_date, entry_date, so_year, ex_duty_active, edu_cess_percent, edu_cess_rl_id, sec_edu_cess_percent, sec_edu_cess_rl_id, tot_ex_duty_amt, tot_edu_cess_amt, tot_sec_edu_cess_amt, grand_tot_central_excise_amt, transport_charge_active, transport_charge, billing_type,company_id,unit_id,number_gen_id) values('$salesOrderNo', '$selDistributorId', '$invoiceDate', '$userId', 'P', '$lastDate', '$productPriceRateListId', '$selState', '$totalAmount', '$taxType', '$totalTaxAmt', '$grandTotalAmt', '$distMgnRateListId', '$selCity', '$billingForm', '$selTax', '$soRemark', '$netWt', '$grossWt', '$numBox', '$roundVal', '$invoiceType', '$additionalItemTotalWt', '$poNo', '$selArea', NOW(), '$poDate', '$challanNo', '$challanDate', '$discount', '$discountRemark', '$discountPercent', '$discountAmt', '$octroiExempted', '$oecNo', '$oecValidDate', '$oecIssuedDate', '$proformaInvoiceNo', '$proformaInvoiceDate', '$sampleInvoiceNo', '$sampleInvoiceDate', '$entryDate', '$soYear', '$exDutyActive', '$eduCessPercent', '$eduCessRLId', '$secEduCessPercent', '$secEduCessRLId', '$totExDuty', '$totEduCess', '$totSecEduCess', '$totCentralExDuty', '$transChargeActive', '$transporterCharge', '$billingType','$company','$unit','$number_gen_id')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#For adding Sales order Items
	function addSalesOrderEntries($lastId, $selProductId, $unitPrice, $quantity, $totalAmt, $selMcPkgId, $mcPack, $loosePack, $distMgnStateEntryId, $taxPercent, $pGrossWt, $pMCPkgGrossWt, $freePkts, $basicRate, $exDutyPercent, $exDutyAmt, $exDutyMasterId, $eduCessAmt, $secEduCessAmt, $taxAmt, $mcPkgWtId)
	{
		$qry =	"insert into t_salesorder_entry (salesorder_id, product_id, rate, quantity, total_amount, mc_pkg_id, mc_pack, loose_pack, dist_mgn_state_id, tax_percent, p_gross_wt, p_mc_wt, free_pkts, basic_rate, ex_duty_percent, ex_duty_amt, ex_duty_id,  edu_cess_amt, sec_edu_cess_amt, tax_amt, mc_pkg_wt_id) values('$lastId', '$selProductId', '$unitPrice', '$quantity', '$totalAmt', '$selMcPkgId', '$mcPack', '$loosePack','$distMgnStateEntryId', '$taxPercent', '$pGrossWt', '$pMCPkgGrossWt', '$freePkts', '$basicRate', '$exDutyPercent', '$exDutyAmt', '$exDutyMasterId', '$eduCessAmt', '$secEduCessAmt', '$taxAmt', '$mcPkgWtId')";

		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();
		
		return $insertStatus;
	}
	# Add Aditional Item
	function addSOAnlItemEntries($lastId, $itemName, $itemWt)
	{
		$qry =	"insert into t_salesorder_other (salesorder_id, item_name, item_wt) values('$lastId', '$itemName', '$itemWt')";

		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();
		
		return $insertStatus;
	}

	#Update sales Order Items
	function updateSalesOrderentries($salesOrderEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $selMcPkgId, $mcPack, $loosePack, $distMgnStateEntryId, $taxPercent, $pGrossWt, $pMCPkgGrossWt, $freePkts, $basicRate, $exDutyPercent, $exDutyAmt, $exDutyMasterId, $eduCessAmt, $secEduCessAmt, $taxAmt, $mcPkgWtId)
	{	
		$qry = "update t_salesorder_entry set product_id='$selProductId', rate='$unitPrice', quantity='$quantity', total_amount='$totalAmt', mc_pkg_id='$selMcPkgId', mc_pack='$mcPack', loose_pack='$loosePack', dist_mgn_state_id='$distMgnStateEntryId', tax_percent='$taxPercent', p_gross_wt='$pGrossWt', p_mc_wt='$pMCPkgGrossWt', free_pkts='$freePkts', basic_rate='$basicRate', ex_duty_percent='$exDutyPercent', ex_duty_amt='$exDutyAmt', ex_duty_id='$exDutyMasterId', edu_cess_amt='$eduCessAmt', sec_edu_cess_amt='$secEduCessAmt', tax_amt='$taxAmt', mc_pkg_wt_id='$mcPkgWtId' where id='$salesOrderEntryId'";		

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		
		return $result;	
	}

	# Returns all Paging Records  // TI, PI, SI
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $invoiceTypeFilter, $distributorFilter)
	{

		$whr = "a.distributor_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate'";

		if ($invoiceTypeFilter=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceTypeFilter=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceTypeFilter=='SI') $whr .= " and a.invoice_type='S' ";

		if ($distributorFilter) $whr .= " and a.distributor_id='$distributorFilter' "; 

		$limit		= " $offset, $limit ";
	
		$orderBy	= " a.so desc ";

		//a.invoice_date
		$qry = " select a.id, a.so, a.distributor_id, if (a.so!=0 and a.invoice_type='T', a.invoice_date, entry_date), a.createdby, b.name, a.last_date, a.extended, a.logstatus, a.logstatus_descr, a.payment_status, a.dispatch_date, a.status_id, a.complete_status, a.grand_total_amt, a.invoice_type, a.settled, a.paid, a.area_id, a.city_id, ROUND((a.grand_total_amt+a.round_value),2) as tSOAmt, a.modified_by, a.modified_time, TIME_TO_SEC(TIMEDIFF(NOW(), a.modified_time)) as diffTinS, a.proforma_no, a.sample_invoice_no, a.pkng_gen, a.pkng_confirm, a.gpass_gen, a.gpass_confirm,a.company_id,a.unit_id,a.number_gen_id from t_salesorder a, m_distributor b ";

		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
		if ($limit!="")		$qry 	.= " limit ".$limit;	

		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Returns all Sales Order
	function fetchAllDateRangeRecords($fromDate, $tillDate, $invoiceTypeFilter, $distributorFilter)
	{
		$whr = "a.distributor_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate'";

		if ($invoiceTypeFilter=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceTypeFilter=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceTypeFilter=='SI') $whr .= " and a.invoice_type='S' ";
		
		if ($distributorFilter) $whr .= " and a.distributor_id='$distributorFilter' "; 	

		$orderBy	= " a.so desc ";

		$qry = " select a.id, a.so, a.distributor_id, if (a.so!=0 and a.invoice_type='T',a.invoice_date, entry_date), a.createdby, b.name, a.last_date, a.extended, a.logstatus, a.logstatus_descr, a.payment_status, a.dispatch_date, a.status_id, a.complete_status, a.grand_total_amt, a.invoice_type, a.settled, a.paid, a.area_id, a.city_id, ROUND((a.grand_total_amt+a.round_value),2) as tSOAmt, a.modified_by, a.modified_time, TIME_TO_SEC(TIMEDIFF(NOW(), a.modified_time)) as diffTinS, a.proforma_no, a.sample_invoice_no, a.pkng_gen, a.pkng_confirm, a.gpass_gen, a.gpass_confirm,a.company_id,a.unit_id,a.number_gen_id from t_salesorder a, m_distributor b ";

		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Sales Order
	function fetchAllRecords()
	{
		$qry = "select a.id, a.so, a.distributor_id, a.invoice_date, a.createdby, b.name from t_salesorder a, m_distributor b where a.distributor_id=b.id order by a.so desc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Printing Purpose
	function getSORecords()
	{
		$qry = "select id, so, distributor_id, invoice_date, createdby, status from t_salesorder where status='P' order by so desc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}
	
	#For Getting Total Amount Of Each Sales Order
	function getSalesOrderAmount($salesOrderId)
	{
		$qry = "select sum(total_amount) from t_salesorder_entry where salesorder_id='$salesOrderId' group by salesorder_id";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	# Get Sales Order based on Sales Order id  # USing in Edit Section SAME (findSORec())
	function find($orderId)
	{
		$qry = "select id, so, distributor_id, invoice_date, createdby, status, last_date, extended, rate_list_id, state_id, dist_mgn_ratelist_id, city_id, tax_type, billing_frm, tax_amt, remark, tax_applied, net_wt, gross_wt, num_box, invoice_type, transporter_id, adnl_item_total_wt, po_no, area_id, po_date, challan_no, challan_date, discount, discount_remark, discount_percent, discount_amt, grand_total_amt, round_value, octroi_exempted, oec_no, oec_date, oec_issued_date, complete_status, proforma_no, proforma_date, sample_invoice_no, sample_invoice_date, entry_date, docket_no, ex_duty_active, edu_cess_percent, edu_cess_rl_id, sec_edu_cess_percent, sec_edu_cess_rl_id, transport_charge_active, transport_charge, billing_type, inv_seq_num, to_pay,company_id,unit_id from t_salesorder where id='$orderId' FOR UPDATE ";		
		
		return $this->databaseConnect->getRecord($qry);
	}

	# Find Record For Invoice Display
	function findSORec($orderId)
	{
		$qry = "select id, so, distributor_id, invoice_date, createdby, status, last_date, extended, rate_list_id, state_id, dist_mgn_ratelist_id, city_id, tax_type, billing_frm, tax_amt, remark, tax_applied, net_wt, gross_wt, num_box, invoice_type, transporter_id, adnl_item_total_wt, po_no, area_id, po_date, challan_no, challan_date, discount, discount_remark, discount_percent, discount_amt, grand_total_amt, round_value, octroi_exempted, oec_no, oec_date, oec_issued_date, complete_status, proforma_no, proforma_date, sample_invoice_no, sample_invoice_date, entry_date, docket_no, gpass_gen, gpass_confirm, pkng_confirm, ex_duty_active, edu_cess_percent, edu_cess_rl_id, sec_edu_cess_percent, sec_edu_cess_rl_id, tot_ex_duty_amt, tot_edu_cess_amt, tot_sec_edu_cess_amt, grand_tot_central_excise_amt, transport_charge_active, transport_charge, billing_type, inv_seq_num from t_salesorder where id='$orderId' ";
		//echo $qry;
		
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on SO Id from t_salesorder_entry TABLE	
	function fetchAllSalesOrderItem($editSalesOrderId)
	{
		$qry = "select a.id, a.salesorder_id, a.product_id, a.rate, a.quantity, a.total_amount, a.mc_pkg_id, a.mc_pack, a.loose_pack, a.dist_mgn_state_id, a.tax_percent, a.p_gross_wt, a.p_mc_wt, a.free_pkts, a.basic_rate, a.ex_duty_percent, a.ex_duty_amt, a.ex_duty_id, a.edu_cess_amt, a.sec_edu_cess_amt, (a.ex_duty_amt+a.edu_cess_amt+a.sec_edu_cess_amt) as totalCentTax, a.tax_amt, a.mc_pkg_wt_id from t_salesorder_entry a where a.salesorder_id='$editSalesOrderId' ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  Sales Order
	function updateSalesOrder($salesOrderId, $selDistributorId, $lastDate, $dateExtended, $productPriceRateListId, $selState, $totalAmount, $taxType, $calcTaxAmt, $grandTotalAmt, $distMgnRateListId, $selCity, $billingForm, $selTax, $soRemark, $netWt, $grossWt, $numBox, $roundVal, $invoiceType, $additionalItemTotalWt, $poNo, $selArea, $invoiceDate, $poDate, $challanNo, $challanDate, $discount, $discountRemark, $discountPercent, $discountAmt, $octroiExempted, $oecNo, $oecValidDate, $oecIssuedDate, $proformaInvoiceNo, $proformaInvoiceDate, $sampleInvoiceNo, $sampleInvoiceDate, $entryDate, $soYear, $exDutyActive, $eduCessPercent, $eduCessRLId, $secEduCessPercent, $secEduCessRLId, $totExDuty, $totEduCess, $totSecEduCess, $totCentralExDuty, $transChargeActive, $transportCharge, $billingType, $toPay,$company,$unit)
	{
		$qry = "update t_salesorder set distributor_id='$selDistributorId', last_date='$lastDate', extended='$dateExtended', rate_list_id='$productPriceRateListId', state_id='$selState', total_amt='$totalAmount', tax_type='$taxType', tax_amt='$calcTaxAmt', grand_total_amt='$grandTotalAmt', dist_mgn_ratelist_id='$distMgnRateListId', city_id='$selCity', billing_frm='$billingForm', tax_applied='$selTax', remark='$soRemark', net_wt='$netWt', gross_wt='$grossWt', num_box='$numBox', round_value='$roundVal', invoice_type='$invoiceType', adnl_item_total_wt='$additionalItemTotalWt', po_no='$poNo', area_id='$selArea', invoice_date='$invoiceDate', po_date='$poDate', challan_no='$challanNo', challan_date='$challanDate', discount='$discount', discount_remark='$discountRemark', discount_percent='$discountPercent', discount_amt='$discountAmt', octroi_exempted='$octroiExempted', oec_no='$oecNo', oec_date='$oecValidDate', oec_issued_date='$oecIssuedDate', proforma_no='$proformaInvoiceNo', proforma_date='$proformaInvoiceDate', sample_invoice_no='$sampleInvoiceNo', sample_invoice_date='$sampleInvoiceDate', entry_date='$entryDate', so_year='$soYear', ex_duty_active='$exDutyActive', edu_cess_percent='$eduCessPercent', edu_cess_rl_id='$eduCessRLId', sec_edu_cess_percent='$secEduCessPercent', sec_edu_cess_rl_id='$secEduCessRLId', tot_ex_duty_amt='$totExDuty', tot_edu_cess_amt='$totEduCess', tot_sec_edu_cess_amt='$totSecEduCess', grand_tot_central_excise_amt='$totCentralExDuty', transport_charge_active='$transChargeActive', transport_charge='$transportCharge', billing_type='$billingType',to_pay='$toPay',company_id='$company', unit_id='$unit' where id='$salesOrderId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Delete  Sales Order Item  Recs
	function deleteSalesOrderItemRecs($salesOrderId)
	{
		$qry = " delete from t_salesorder_entry where salesorder_id=$salesOrderId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Sales Order
	function deleteSalesOrder($salesOrderId)
	{
		$qry = " delete from t_salesorder where id=$salesOrderId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Delete  Sales Order Entry  Rec
	function deleteSalesOrderEntryRec($salesOrderEntryId)
	{
		$qry = " delete from t_salesorder_entry where id=$salesOrderEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	
	#Setting the Log status	
	function updateSalesOrderLogStatus($salesOrderId, $statusFlag, $dispatchLastDate)
	{
		$qry = " update t_salesorder set logstatus='$statusFlag', logstatus_descr='$dispatchLastDate' where id=$salesOrderId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Filter Product Recs
	function filterProductRecs($selDistributorId, $productPriceRateListId)
	{
		$qry = "select b.id, b.code, b.name from m_dist_product_price a, t_combo_matrix b where a.product_id=b.id and a.distributor_id='$selDistributorId' and a.rate_list_id='$productPriceRateListId' ";

		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the Product Price
	function findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId)
	{
		//$qry = " select mrp from m_product_mrp where product_id='$productId' and rate_list_id='$productPriceRateListId'"; Removed on JAn 11 10
		$qry = " select pme.mrp from m_product_mrp pm join m_product_mrp_expt pme on pme.product_mrp_id=pm.id where pm.product_id='$productId' and pm.rate_list_id='$productPriceRateListId' and (pme.distributor_id='$distributorId' or pme.distributor_id=0) and (pme.state_id='$stateId' or pme.state_id=0) order by pme.state_id desc, pme.distributor_id desc ";
		$rec =  $this->databaseConnect->getRecord($qry);

		return (sizeof($rec)>0)?$rec[0]:0;
	}

	/*
	# Filter Product Recs
	function fetchProductRecs($selDistributorId, $productPriceRateListId)
	{
		$qry = "select b.id, b.code, b.name from m_dist_product_price a, t_combo_matrix b where a.product_id=b.id and a.distributor_id='$selDistributorId' and a.rate_list_id='$productPriceRateListId' ";

		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
	}
	*/
	# Filter State List
	function filterStateList($selDistributorId, $exportEnabled)
	{
		$qry = "select  distinct c.id, c.name from m_distributor a, m_distributor_state b, m_state c where a.id=b.distributor_id and b.state_id=c.id and a.id='$selDistributorId' and b.export_active='$exportEnabled' order by c.name asc";

		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	# Dist City Recs
	function getDistributorCityRecs($distributorId, $stateId, $exportEnabled='N')
	{
		$qry = " select distinct b.city_id, c.name from m_distributor_state a, m_distributor_city b, m_city c where a.id=b.dist_state_entry_id and b.city_id=c.id and a.distributor_id='$distributorId' and a.state_id='$stateId' and a.export_active='$exportEnabled'";

		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}


	function getStateRec($selDistributorId, $exportEnabled='N')
	{
		$qry = " select  distinct c.id, c.name from m_distributor a, m_distributor_state b, m_state c where a.id=b.distributor_id and b.state_id=c.id and a.id='$selDistributorId' and b.export_active='$exportEnabled' order by c.name asc ";

		$result = $this->databaseConnect->getRecords($qry);		
		return $result;
	}
	# ----------------------------
	# Check SO Number Exist
	# ----------------------------
	# Check valid SO num
	function chkValidSONum($selDate, $invoiceNum, $invoiceType)
	{		

		$whr = " type='SO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum' ";

		if ($invoiceType=='S') $whr .= " and so_invoice_type='SA'";
		else $whr .= " and so_invoice_type='TA'";

		$qry	= "select start_no, end_no from number_gen ";
		if ($whr!="") $qry .= " where ".$whr;
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	

	# Check SO Number Exist
	function checkSONumberExist($soId, $cSOId, $invoiceType, $selDate)
	{		
		$soYear	 = date("Y", strtotime($selDate));

		//$whr = " id is not null and so_year='$soYear' ";
		$whr = "so_year='$soYear' ";

		if ($cSOId!="") $whr .= " and id!=$cSOId";		
		if ($invoiceType=='S') $whr .= " and sample_invoice_no='$soId' ";
		else $whr .= " and (so='$soId' or inv_seq_num like '%$soId%') ";
		//else $whr .= " and so='$soId' ";

		$qry = " select id from t_salesorder";
		if ($whr!="") $qry .= " where ".$whr;

		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	// Ends Here

	# Get Distributor Rec
	function getDistributorRec($distributorId, $stateId, $cityId, $areaId, $exportEnabled='N')
	{
		$whr = "a.id=b.distributor_id and b.id=c.dist_state_entry_id and c.id=d.dist_city_entry_id and  if(d.area_id=0, c.city_id=e.city_id,d.area_id=e.id) and a.id='$distributorId' and b.state_id='$stateId' and c.city_id='$cityId' and b.export_active='$exportEnabled' ";

		if ($areaId!=0 && $areaId!="") $whr .= " and e.id='$areaId' ";

		$qry = " select  a.id, a.code, a.name, a.contact_person, a.address, a.contact_no, a.opening_bal, a.credit_limit, b.vat_no, b.tin_no, b.cst_no, b.tel_no, b.billing_address, b.delivery_address, b.same_billing_adr, b.pin_code, b.ecc_no from m_distributor a, m_distributor_state b, m_distributor_city c, m_distributor_area d, m_area e ";

		if ($whr!="")  $qry .= " where ".$whr;
		if ($orderBy!="")  $qry .= " order by ".$orderBy;

		return $this->databaseConnect->getRecord($qry);
	}

	# Return Avg Margin an State EntryId
	function getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $cityId, $exportEnabled='N')
	{
		$qry = " select distinct b.avg_margin, b.id, b.basic_margin from m_distributor_margin a, m_distributor_margin_state b left join m_distributor_state mds on b.dist_state_entry_id=mds.id, m_product_manage c where a.product_id=c.id and a.id=b.distributor_margin_id and a.distributor_id='$distributorId' and a.product_id='$productId' and a.rate_list_id='$distMarginRateListId' and b.state_id='$stateId' and b.city_id='$cityId' and mds.export_active='$exportEnabled'";
		$rec =  $this->databaseConnect->getRecord($qry);

		return (sizeof($rec)>0)?array($rec[0],$rec[1], $rec[2]):0;
	}

	# Get distributor Wise Tax Invoice Calc
	function  distWiseTaxInvoiceCalc($distributorId, $stateId)
	{
		$qry = " select b.tax_type, b.billing_form from m_distributor a, m_distributor_state b where a.id=b.distributor_id and a.id='$distributorId' and b.state_id='$stateId' ";

		$result = $this->databaseConnect->getRecord($qry);
		if (sizeof($result)>0) {
			$taxType 	= $result[0];
			$billingForm	= $result[1];

			$qryVat  = " select distinct b.vat from m_state_vat a, m_state_vat_entry b where a.id=b.main_id and a.state_id='$stateId' ";
			$vatResult = $this->databaseConnect->getRecord($qryVat);
			if (sizeof($vatResult)>0)	$vatRate =  $vatResult[0];
			else				$vatRate =  0;

			$qCstTax = " select base_cst from m_tax where active='Y' ";	
			$cstResult = $this->databaseConnect->getRecord($qCstTax);
			if (sizeof($cstResult)>0)	$cstRate =  $cstResult[0];
			else				$cstRate =  0;

			if ($billingForm=='FN')		$cstRate = $vatRate;
			else if ($billingForm=='FC')	$cstRate = $cstResult[0];
			else if ($billingForm=='FF') 	$cstRate = 0;	

			if ($taxType=='VAT')	return array($taxType,$vatRate);
			if ($taxType=='CST')	return array($taxType,$cstRate);			
		}
	}

	/*****************************************************/
	/*
		From Master get CreditLimit, CreditPeriod, Outstanding Amt
	*/
	function getDistMasterRec($distributorId)
	{
		$qry = " select credit_limit, credit_period, sum(opening_bal+amount), cr_period_from from m_distributor where id='$distributorId' group by id";
		//echo $qry ;
		//die();
		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3]):array(0,0,0,0);
	}

	/*
		Dist Account
	*/
	function getDistributorAccountRec($salesOrderId)
	{
		$qry = " select distributor_id, amount, cod from t_distributor_ac where so_id='$salesOrderId' ";

		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}
	
	/*
		Del dist Account
	*/
	function delDistributorAccount($salesOrderId)
	{
		$qry = " delete from t_distributor_ac where so_id=$salesOrderId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	/**
		calculation of Due Date

		Outstanding Amt Credit Period
		Distributor Amt - Orderes total Amt = before credit period Amt is > no Processing
		// If Credit Period is set from Delivery Date, and if Delivery Date is not entered, then use Dispatch Date. If Dispatch Date is not there, then Invoice Date should be the base for calculation of Due Date.
		// In Sales order t_salesorder table , last_date and dispatch_date is same but dispatch is update when invoice is complete
	**/
	function getCreditPeriodOutStandAmount($distributorId, $creditPeriod)
	{
		/* Before May 30, 2011
		$qry = " select sum(a.grand_total_amt+a.round_value) as totalSOAmt, c.amount, (c.amount-sum((a.grand_total_amt+a.round_value))) as outStandAmt
			from 
				t_salesorder a, m_distributor c 
			where 
				a.distributor_id=c.id and 
				a.invoice_date <= (SELECT DATE_SUB(CURDATE(), INTERVAL $creditPeriod DAY)) 
				and a.distributor_id=$distributorId 
				and a.complete_status='C' 
			group by 
				a.distributor_id ";
		*/
		$qry = "select sum(a.grand_total_amt+a.round_value) as totalSOAmt, c.amount, (c.amount-sum((a.grand_total_amt+a.round_value))) as outStandAmt 
			from 
				t_salesorder a, m_distributor c 
			where 
				a.distributor_id=c.id and 
				if (c.cr_period_from='DELID', if (a.delivery_date!='0000-00-00',a.delivery_date, if (a.delivery_date='0000-00-00' and a.dispatch_date!='0000-00-00',a.dispatch_date,a.invoice_date)), if (c.cr_period_from='DESPD', if (a.dispatch_date!='0000-00-00',a.dispatch_date,a.invoice_date), a.invoice_date)) <= (SELECT DATE_SUB(CURDATE(), INTERVAL $creditPeriod DAY)) 
				and a.distributor_id=$distributorId 
				and a.complete_status='C' 
			group by 
				a.distributor_id";

		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[2]:0;		
	}

	/*****************************************************/

	# Returns all MRP Products
	function getMRPProducts($distMgnRateListId, $distributorId, $stateId, $productPriceRateListId, $cityId, $exportEnabled='N')
	{	
		/* Original
		$qry = " select distinct a.product_id, b.name from m_distributor_margin a, m_product_manage b, m_distributor_margin_state mgnState where mgnState.distributor_margin_id=a.id and a.product_id=b.id and a.rate_list_id='$distMgnRateListId' and a.distributor_id='$distributorId' and mgnState.state_id='$stateId' and a.product_id not in (select product_id from m_product_status where (distributor_id='$distributorId' or distributor_id=0)  and (state_id='$stateId' or state_id=0)) and a.product_id in (select pmrp.product_id from m_product_mrp pmrp where pmrp.rate_list_id='$productPriceRateListId')order by b.name asc ";
		*/
		$qry = " select distinct a.product_id, b.name from 
				m_distributor_margin a, m_product_manage b, m_distributor_margin_state mgnState left join m_distributor_state mds on mgnState.dist_state_entry_id=mds.id 
			where 
				mgnState.distributor_margin_id=a.id and a.product_id=b.id 
				and a.rate_list_id='$distMgnRateListId' and a.distributor_id='$distributorId' and mgnState.state_id='$stateId' and mgnState.city_id='$cityId'
				and a.product_id not in (select product_id from m_product_status where (distributor_id='$distributorId' or distributor_id=0)  
				and (state_id='$stateId' or state_id=0) and city_id='$cityId') 
				and a.product_id in (select pmrp.product_id from m_product_mrp pmrp where pmrp.rate_list_id='$productPriceRateListId')
				and mds.export_active='$exportEnabled'
			order by b.name asc ";

		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;		
	}

	# Returns all MRP Products
	function getActiveProducts($distMgnRateListId, $distributorId, $stateId, $productPriceRateListId, $cityId, $exportEnabled='N')
	{
		$qry = " select distinct a.product_id, b.name from 
				m_distributor_margin a, m_product_manage b, m_distributor_margin_state mgnState left join m_distributor_state mds on mgnState.dist_state_entry_id=mds.id
			 where 
					mgnState.distributor_margin_id=a.id and a.product_id=b.id and a.rate_list_id='$distMgnRateListId' and a.distributor_id='$distributorId' and mgnState.state_id='$stateId' and mgnState.city_id='$cityId'
					and a.product_id not in (select product_id from m_product_status where (distributor_id='$distributorId' or distributor_id=0)  and (state_id='$stateId' or state_id=0) and city_id='$cityId') and a.product_id in (select pmrp.product_id from m_product_mrp pmrp where pmrp.rate_list_id='$productPriceRateListId') 
					and mds.export_active='$exportEnabled'
			order by b.name asc ";

		$result	= $this->databaseConnect->getRecords($qry);
		$this->productActiveRecArr = $result;
	}

	# Returns all MRP Products
	function getSelProducts()
	{		
		return $this->productActiveRecArr;
	}

	/*State Wise Vat Percent Return Vat Percent*/
	function getStateWiseVatPercent($productId, $stateId, $selDate)
	{
		# Find the Product Category
		list($categoryId, $pStateId, $pGroupId) = $this->findProductRec($productId);
		# State Vat Rate List Id
		$stateVatRateListId = $this->getValidStateVatRateList($stateId, $selDate);
		# Get Vat Percent
		$vatPercent	= $this->getVatPercent($categoryId, $pStateId, $pGroupId, $stateId, $stateVatRateListId);
		return ($vatPercent!="")?$vatPercent:0;
	}

	# Get Category, Product State Id, Product Group Id
	function findProductRec($productId)
	{
		$qry = " select category_id, product_state_id, product_group_id from m_product_manage where id='$productId'";

		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2]);		
	}

	# Get Vat Percent
	function getVatPercent($categoryId, $pStateId, $pGroupId, $stateId, $stateVatRateListId)
	{
		$qry = " select b.vat from m_state_vat a, m_state_vat_entry b where a.id=b.main_id and a.state_id='$stateId' and b.product_category_id='$categoryId' and b.product_state_id='$pStateId' and b.product_group_id='$pGroupId' and a.rate_list_id='$stateVatRateListId' ";

		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}
	# Vat Percent Ends Here ---------------------

	function getDistTaxType($distributorId, $stateId, $exportEnabled='N')
	{
		$qry = " select b.tax_type, b.billing_form, b.billing_state_id, b.ex_billing_form from m_distributor a, m_distributor_state b where a.id=b.distributor_id and a.id='$distributorId' and b.state_id='$stateId' and b.export_active='$exportEnabled' ";

		$result = $this->databaseConnect->getRecord($qry);
		return array($result[0],$result[1], $result[2], $result[3]);	
	}

	# Get distributor Wise Tax Invoice Calc
	function  getDistributorWiseTax($distributorId, $stateId, $productId, $selDate)
	{
		list($taxType, $billingForm, $billingStateId) 	= $this->getDistTaxType($distributorId, $stateId);		
		
		$vatRate = $this->getStateWiseVatPercent($productId, $stateId, $selDate);

		//$qCstTax = " select base_cst from m_tax where active='Y' ";	
		$qCstTax = "select mt.base_cst from m_tax mt join m_tax_ratelist mtrl on mtrl.id=mt.rate_list_id where mt.active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mtrl.end_date,'%Y-%m-%d')>='$selDate' or (mtrl.end_date is null || mtrl.end_date=0)) order by mtrl.start_date desc";
		$cstResult = $this->databaseConnect->getRecord($qCstTax);

		if (sizeof($cstResult)>0)	$cstRate =  $cstResult[0];
		else				$cstRate =  0;

		if ($billingForm=='FN')		$cstRate = $this->getStateWiseVatPercent($productId, $billingStateId, $selDate);
		else if ($billingForm=='FC')	$cstRate = $cstResult[0];
		else if ($billingForm=='FF') 	$cstRate = 0;	

		if ($taxType=='VAT')	return $vatRate;
		if ($taxType=='CST')	return $cstRate;
	}

	/* 
	* Get MC Packing Wt Records
	*/	
	function getMCPkgRecs($productNetWt)
	{	
		/*
		$qry = "select a.id, a.code, a.number_packs, a.descr from m_mcpacking a where a.id in (select mc_pkg_id from m_mc_pkg_wt where net_wt='$productNetWt') order by a.number_packs asc";
		*/
		$qry = "select mcpw.mc_pkg_id as mcPkgId, mcp.code, mcpw.id as pkgWtId, mcpw.name as mcPkgWtName, (select count(*) from m_mc_pkg_wt where net_wt=mcpw.net_wt and mc_pkg_id=mcpw.mc_pkg_id) as duplicatePkg
				from m_mc_pkg_wt mcpw left join m_mcpacking mcp on mcp.id=mcpw.mc_pkg_id
				where mcpw.net_wt='$productNetWt' order by mcp.number_packs asc";

		$result = $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$pkgName = $v[1];
			$duplicatePkg = $v[4];
			$pkgSelectionId = $v[0]."_".$v[2];			
			if ($duplicatePkg>1) $pkgName = $pkgName." (".$v[3].")";
			$resultArr[$pkgSelectionId] = $pkgName;
		}
		return $resultArr;

		/* Original Before 18 JUNE 12
		$qry = "select a.id, a.code, a.number_packs, a.descr from m_mcpacking a where a.id in (select mc_pkg_id from m_mc_pkg_wt where net_wt='$productNetWt') order by a.number_packs asc";

		$result = $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		*/
	}

	
	# Round off Calculation	
	function getRoundoffVal($grandTotalAmt)
	{
		$grandTotalAmt=number_format($grandTotalAmt,2);
		$roundFig = FloatVal(substr($grandTotalAmt,-3));
		$roundVal = ($roundFig>=0.50)?(1-$roundFig):(-$roundFig);
		//echo $roundVal;
		return $roundVal;
		//return $roundFig;
	}

	function  getRoundoffValRecord($selSOId)
	{
		$qry="select a.round_value from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.id='$selSOId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Return Product Net Wt
	function getProductNetWt($productId)
	{
		$qry = " select net_wt from m_product_manage where id='$productId' ";
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}	
	
	#Fetch All Records based on SO Id from t_salesorder_entry TABLE	
	function fetchAllAdnlItem($editSalesOrderId)
	{
		$qry = "select id, salesorder_id, item_name, item_wt from t_salesorder_other where salesorder_id='$editSalesOrderId' ";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Del Additional Item
	function delAdnlItem($salesOrderId)
	{
		$qry = " delete from t_salesorder_other where salesorder_id=$salesOrderId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# ----------------------------
	# Check PO Number Exist
	# ----------------------------
	function checkPONumberExist($poNo, $cSOId)
	{
		if ($cSOId!="") $uptdQry = " and id!=$cSOId";
		else $uptdQry = "";

		$qry = " select id from t_salesorder where po_no='$poNo' $uptdQry";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	# Get Pkg Group
	function getPkgGroup($selComb)
	{
		$qry = " select p_left, p_right from m_pkg_group where (p_left='$selComb' or p_right='$selComb')";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array();
	}

	/*
	function getPackingGroupRec($selPackingComb)
	{
		$gRec = "";
		$qry1 = " select p_left from m_pkg_group where p_right='$selComb' ";
		$rec1 = $this->databaseConnect->getRecord($qry1);
		$gRec = "L:".$rec1[0];
		$qry2 = " select p_right from m_pkg_group where p_left='$selComb' ";
		$rec2 = $this->databaseConnect->getRecord($qry2);
		$gRec = "R:".$rec2[0];
		return $gRec;	
	}
	*/

	function getRightPkgRule($selPkgComb)
	{
		$qry = " select p_right from m_pkg_group where p_left='$selPkgComb'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	/*
	function getLeftPkgRule($selPkgComb)
	{
		$qry = " select p_left from m_pkg_group where p_right='$selPkgComb'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}	
	*/

	# Get City Recs
	function getCityRec($selDistributorId, $stateId, $exportEnabled='N')
	{
		$qry = " select distinct c.city_id, d.name from m_distributor a, m_distributor_state b, m_distributor_city c, m_city d where a.id=b.distributor_id and b.id=c.dist_state_entry_id and c.city_id=d.id and a.id='$selDistributorId' and b.state_id='$stateId' and b.export_active='$exportEnabled' order by d.name asc ";

		$result = $this->databaseConnect->getRecords($qry);		
		return $result;
	}

	# Dist City Recs
	function getDistributorAreaRecs($distributorId, $stateId, $cityId, $exportEnabled='N')
	{
		$qry = " select distinct d.id, d.name 
			from m_distributor_state a, m_distributor_city b, m_distributor_area c, m_area d 
			where a.id=b.dist_state_entry_id and b.id=c.dist_city_entry_id 
			and if(c.area_id=0, b.city_id=d.city_id,c.area_id=d.id) and
			a.distributor_id='$distributorId' and a.state_id='$stateId' and d.city_id='$cityId' and a.export_active='$exportEnabled'
			order by d.name asc ";

		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select All--');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select All--');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get Fomated Address (If second comma then put a Break)
	function getAddressFormat($address)
	{
		$toAddr = explode(",",$address);	
		$k=0;
		$displayAddressVal = "";
		foreach ($toAddr as $kv=>$val) {
			$k++;				
			if ($k==3) $dVal .= "<br/>";
			$dVal .= $val;
			if ($k!=sizeof($toAddr)) $dVal .= ",";
		}	
		return $dVal;
	}

	// ---------------  invoice Number check ----------
	# Get Next Invoice Number	
	function getNextInvoiceNo()
	{	
		$selDate = date("Y-m-d");
		list($soNum, $invoiceDate, $invSeqNum) = $this->getMaxSONum();

		$invSeqArr = explode(",", $invSeqNum);
		$soNum = (sizeof($invSeqArr)>0)?max($invSeqArr):$soNum;
		
		$validSONum = $this->getValidSONum($soNum, $invoiceDate);		
		if ($validSONum) return $soNum+1;
		else return $this->getCurrentSONum($selDate);
	}

	function getValidSONum($soNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO' and start_no<='$soNum' and end_no>='$soNum' ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getMaxSONum()
	{
		//$qry = " select max(so), invoice_date, inv_seq_num from t_salesorder where so!=0 group by id order by id desc, invoice_date desc";
		$qry = "select max(so), invoice_date, inv_seq_num from t_salesorder where so!=0 group by id order by max(so) desc";

		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1], $rec[2]);
	}

	function getCurrentSONum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO'";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
	// ------------------------- invoice number check ends here ----------

	# Check Octroi Exempted
	function chkOctroiExempted($distributorId, $stateId, $cityId)
	{
		$qry = " select a.id from m_distributor_state a, m_distributor_city b where a.id=b.dist_state_entry_id and a.distributor_id='$distributorId' and a.state_id='$stateId' and b.city_id='$cityId' and a.octroi_exempted='Y' ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateModifiedRec($salesOrderId, $userId, $mode)
	{
		if ($mode=='E') $uptdQry = "modified_time=NOW()";
		else $uptdQry = "modified_time=0";
		
		$qry = " update t_salesorder set modified_by='$userId', $uptdQry where id=$salesOrderId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function chkRecModified($salesOrderId)
	{
		$qry = " select a.modified_by, b.username from t_salesorder a, user b  where a.modified_by=b.id and a.id='$salesOrderId' and a.modified_by!=0 ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][1]:false;
	}

	# --------- Proforma invoice Number starts ----
	# Get Next Proforma Invoice Number	
	function getNextProformaInvoiceNo()
	{	
		$selDate = date("Y-m-d");
		$soYear	 = date("Y", strtotime($selDate));
		list($soNum, $invoiceDate) = $this->getMaxProformaNum($soYear);
		$validSONum = $this->getValidProformaNum($soNum, $invoiceDate);		
		if ($validSONum) return $soNum+1;
		else return $this->getCurrentProformaNum($selDate);
	}

	function getMaxProformaNum($soYear)
	{
		$qry = " select max(proforma_no), proforma_date from t_salesorder where proforma_no!=0 and so_year='$soYear' group by id order by id desc, proforma_date desc";

		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

	function getValidProformaNum($soNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='PF' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO' and start_no<='$soNum' and end_no>='$soNum' ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getCurrentProformaNum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='PF' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO'";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# ----- Proforma Ends Here -----------------------------

	# --------- Sample invoice Number starts ----
	# Get Next Proforma Invoice Number	
	function getNextSampleInvoiceNo()
	{	
		$selDate = date("Y-m-d");
		$soYear	 = date("Y", strtotime($selDate));
		list($soNum, $invoiceDate) = $this->getMaxSampleNum($soYear);
		$validSONum = $this->getValidSampleNum($soNum, $invoiceDate);		
		if ($validSONum) return $soNum+1;
		else return $this->getCurrentSampleNum($selDate);
	}

	function getMaxSampleNum($soYear)
	{
		$qry = " select max(sample_invoice_no), sample_invoice_date from t_salesorder where sample_invoice_no!=0 and so_year='$soYear' group by id order by id desc, sample_invoice_date desc";

		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

	function getValidSampleNum($soNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='SA' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO' and start_no<='$soNum' and end_no>='$soNum' ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getCurrentSampleNum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='SA' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO'";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# ----- Sample Invoice Ends Here -----------------------------

	# ----------------------------
	# Check Proforma Number Exist
	# ----------------------------
	# Check valid PF num
	function chkValidProformaNum($selDate, $invoiceNum)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='PF' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum'";	

		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	

	function checkProformaNumExist($soId, $cSOId)
	{
		if ($cSOId!="") $uptdQry = " and id!=$cSOId";
		else $uptdQry = "";
		$qry = " select id from t_salesorder where proforma_no='$soId' $uptdQry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	// Ends Here

	# ----------------------------
	# Check Sample Number Exist
	# ----------------------------
	# Check valid PF num
	function chkValidSampleNum($selDate, $invoiceNum)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='SA' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum'";	

		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	

	function checkSampleNumExist($soId, $cSOId)
	{
		if ($cSOId!="") $uptdQry = " and id!=$cSOId";
		else $uptdQry = "";
		$qry = " select id from t_salesorder where sample_invoice_no='$soId' $uptdQry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	// Ends Here

	# Get Rate List based on Date
	function getValidStateVatRateList($stateId, $selDate)
	{	
		$qry	= " select id from m_statevat_ratelist where state_id='$stateId' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) order by start_date desc ";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# ~~~~~~~~~~~~~~~~~~~~~~ PKNG INST STARTS HERE ~~~~~~~~~~~~~~~~~~~~~
	# Ins Packing Instruction
	function addPackingInstruction($selSOId, $userId)
	{
		$qry = "insert into t_pkng_inst (so_id, created, created_by) values('$selSOId', NOW(), '$userId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Pkng Gen Flag
			$this->updateSOPkng($selSOId);
		}
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Update Pkng
	function updateSOPkng($selSOId)
	{		
		$qry = "update t_salesorder set pkng_gen='Y' where id='$selSOId' ";

		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}
	# ~~~~~~~~~~~~~~~~~~~~~~ PKNG INST ENDS HERE ~~~~~~~~~~~~~~~~~~~~~
	

	# Update Sales Order Despatch Details
	function updateSODespatchDetails($selSOId, $dispatchDate, $isComplete, $selTransporter,  $transporterRateListId, $dateExtended, $transOtherChargeRateListId, $invoiceNo, $invoiceDate, $invoiceType)
	{	
		$uptdQry = "";
		if ($invoiceType=='T' && $isComplete=='C') $uptdQry = " , so='$invoiceNo'";
		else if ($invoiceType=='S' && $isComplete=='C') $uptdQry = " , sample_invoice_no='$invoiceNo', sample_invoice_date='$invoiceDate' ";

		$qry = "update t_salesorder set dispatch_date='$dispatchDate', complete_status='$isComplete', status='$isComplete', transporter_id='$selTransporter', transporter_rate_list_id='$transporterRateListId', extended='$dateExtended', trans_oc_rate_list_id='$transOtherChargeRateListId', invoice_date='$invoiceDate' $uptdQry where id=$selSOId";

		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}
	
	#Checking the Selected Invoice is cancelled
	function checkCancelledInvoice($invNo, $selDate, $invoiceType)
	{
		$soYear	 = date("Y", strtotime($selDate));

		$qry	= "select invoice_no from s_cancelled_invoice where invoice_no='$invNo' and inv_year='$soYear' and inv_type='$invoiceType' ";

		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	
	
	# ~~~~~~~~~~~~~~~~~~~~~~ Gate Pass STARTS HERE ~~~~~~~~~~~~~~~~~~~~~
	# Ins Gate Pass
	function addGatePass($selSOId, $userId,$company,$unit,$number_gen)
	{
		$qry = "insert into t_gate_pass (so_id, created, createdby, gpass_date,company_id,unit_id,number_gen_id) values('$selSOId', NOW(), '$userId', NOW(),'$company','$unit','$number_gen')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Pkng Gen Flag
			$this->updateGatePassStatus($selSOId);
		}
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Update Gate Pass
	function updateGatePassStatus($selSOId)
	{		
		$qry = "update t_salesorder set gpass_gen='Y' where id='$selSOId' ";

		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}
	# ~~~~~~~~~~~~~~~~~~~~~~ PKNG INST ENDS HERE ~~~~~~~~~~~~~~~~~~~~~

	# get Actual Packing Gross Wt from Packing details
	function getActualPkgGrossWt($salesOrderId)
	{
		$qry = " select total_gross_wt from t_pkng_inst where so_id='$salesOrderId'";

		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?$rec[0][0]:"";
	}

	# Returns all Paging Records  // TI, PI, SI
	function getDistributorList($fromDate, $tillDate, $invoiceTypeFilter)
	{
		$whr = "a.distributor_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate'";

		if ($invoiceTypeFilter=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceTypeFilter=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceTypeFilter=='SI') $whr .= " and a.invoice_type='S' ";
		
		$groupBy	= " a.distributor_id";
		$orderBy	= " b.name asc ";
		
		$qry = " select a.distributor_id, b.name from t_salesorder a, m_distributor b ";

		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($groupBy!="")	$qry	.= " group by ".$groupBy;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
				

		$result	= $this->databaseConnect->getRecords($qry);

		$resultArr = array(''=>'--Select All--');
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
			}
		} 
		/*
		else {
			$resultArr = array();
		}
		*/
		return $resultArr;	
	}

	# Get Distributor Account Rec
	function distributorAccountRec($selSOId)
	{
		$qry = " select id from t_distributor_ac where so_id='$selSOId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";	
	}

	# Upate Dist Account
	function updateDistributorAccount($distAccountId, $salesOrderTotalAmt, $selDistributorId, $selCity)
	{
		$qry = "update t_distributor_ac set amount='$salesOrderTotalAmt', distributor_id='$selDistributorId', city_id='$selCity' where id='$distAccountId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function getDistAC($salesOrderId)
	{
		$qry = " select distributor_id, amount, cod, id, confirmed from t_distributor_ac where so_id='$salesOrderId' ";

		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2], $rec[3], $rec[4]):array();
	}

	function delDistACRefInvoice($salesOrderId)
	{
		$qry = " delete from t_distributor_ac_invoice where invoice_id='$salesOrderId'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getSubDistACEntry($distAccountId)
	{
		$qry 	= "select id, cod, amount from t_distributor_ac where parent_ac_id='$distAccountId' order by parent_ac_id asc";
		return $this->databaseConnect->getRecords($qry);
	}

	# Get Grand total amount
	function getSOGrandTotalAmount($fromDate, $tillDate, $invoiceTypeFilter, $distributorFilter)
	{
		$whr = "a.distributor_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate'";

		if ($invoiceTypeFilter=='TI') $whr .= " and a.invoice_type='T' and a.so!=0";
		else if ($invoiceTypeFilter=='PI') $whr .= " and a.invoice_type='T' and a.so=0";
		else if ($invoiceTypeFilter=='SI') $whr .= " and a.invoice_type='S' ";
		
		if ($distributorFilter) $whr .= " and a.distributor_id='$distributorFilter' "; 
		
		$qry = " select sum(ROUND((a.grand_total_amt+a.round_value),2)) as tSOAmt from t_salesorder a, m_distributor b ";

		if ($whr!="") $qry .= " where ".$whr;	
		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Get Distributor Status
	# Return inactive Dist
	function chkDistributorInactive($distributorId, $stateId, $cityId)
	{
		$qry = "select mds.id from m_distributor_state mds join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id where distributor_id='$distributorId' and mds.state_id='$stateId' and mdc.city_id='$cityId' and mds.active='N' ";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get credit Balance
	function getCreditBalance($distributorId, $cityId)
	{
		$fromDate = date("Y-m-d", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
		$tillDate = date("Y-m-d");
		//a.select_date>='$fromDate' and a.select_date<='$tillDate'
		//a1.select_date>='$fromDate' and a1.select_date<='$tillDate'

		$qry = "select sum(dAmt) as debitAmt, sum(cAmt) as creditAmt, sum(ob) as openingBal, sum(crLimit) as creditLimit, (sum(cAmt)+sum(crLimit)-(sum(dAmt)+sum(ob))) as creditBalance from
		( 
			select a.distributor_id as distributorId, sum(a.amount) as dAmt, 0 as cAmt, 0 as ob, 0 as crLimit from t_distributor_ac a join m_distributor b on a.distributor_id=b.id where (a.value_date is not null and a.value_date!='0000-00-00') and a.pmt_type!='M' and a.cod='D' and a.distributor_id='$distributorId' and a.city_id='$cityId'  group by a.distributor_id
		union
			select a1.distributor_id as distributorId, 0 as dAmt, sum(a1.amount) as cAmt, 0 as ob, 0 as crLimit from t_distributor_ac a1 join m_distributor b1 on a1.distributor_id=b1.id where (a1.value_date is not null and a1.value_date!='0000-00-00') and a1.pmt_type!='M' and a1.cod='C' and a1.distributor_id='$distributorId' and a1.city_id='$cityId' group by a1.distributor_id
		union 
			select md.id as distId, 0 as dAmt, 0 as cAmt, sum(mds.opening_balance) as ob, sum(mds.credit_limit) as crLimit  from m_distributor md join m_distributor_state mds on md.id=mds.distributor_id join m_distributor_city mdc on mdc.dist_state_entry_id=mds.id where md.id='$distributorId' and mdc.city_id='$cityId' group by md.id
		) 
	 	as X group by distributorId";
		
		$result = $this->databaseConnect->getRecords($qry);

		return (sizeof($result)>0)?$result[0][4]:"";
	}

	#---------------------------------- Excises Duties-----------------------------------------
	function getExciseDuty($selDate, $pCategoryId, $pStateId, $pGroupId)	
	{
		$qry = "select mt.id as edId, mtrl.id as rlId, mt.excise_duty, mt.chapter_subheading, mt.ex_goods_id from m_excise_duty mt join m_excise_duty_ratelist mtrl on mtrl.id=mt.excise_rate_list_id where mt.active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mtrl.end_date,'%Y-%m-%d')>='$selDate' or (mtrl.end_date is null || mtrl.end_date=0)) and product_category_id='$pCategoryId' and product_state_id='$pStateId' and product_group_id='$pGroupId' and mt.product_id=0 order by mtrl.start_date desc";

		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2], $rec[3], $rec[4]);
	}

	function getEduCessDuty($selDate)	
	{
		$qECess = "select mec.base_cst, mecrl.id as eduCessRLId from m_edu_cess mec join m_edu_cess_ratelist mecrl on mecrl.id=mec.rate_list_id where mec.active='Y' and date_format(mecrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mecrl.end_date,'%Y-%m-%d')>='$selDate' or (mecrl.end_date is null || mecrl.end_date=0)) order by mecrl.start_date desc";

		$rec = $this->databaseConnect->getRecord($qECess);
		return array($rec[0],$rec[1]);
	}

	function getSecEduCessDuty($selDate)	
	{
		$qSecEdu = "select sec.base_cst, secrl.id from m_sec_edu_cess sec join m_sec_edu_cess_ratelist secrl on secrl.id=sec.rate_list_id where sec.active='Y' and date_format(secrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(secrl.end_date,'%Y-%m-%d')>='$selDate' or (secrl.end_date is null || secrl.end_date=0)) order by secrl.start_date desc";
		$rec = $this->databaseConnect->getRecord($qSecEdu);
		return array($rec[0],$rec[1]);
	}

	function chkExciseDutyActive($selDate)
	{		
		$qry = "select mtrl.id from m_excise_duty_ratelist mtrl where date_format(mtrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mtrl.end_date,'%Y-%m-%d')>='$selDate' or (mtrl.end_date is null || mtrl.end_date=0)) and mtrl.ex_duty_active='Y' order by mtrl.start_date desc";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getExciseDutyRec($exDutyMasterId)
	{	
		$qry = "select chapter_subheading from m_excise_duty where id='$exDutyMasterId' ";
		return $this->databaseConnect->getRecord($qry);
	}

	function getInvoicePolicies()
	{
		$qry = "select certified_agreement, terms_conditions, law_policy_agreement from s_displayrecord";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2]);
	}

	function getExcisableCommodity($salesOrderId)
	{
		//GROUP_CONCAT(exg.name) AS exCommodity
		$qry = "select exg.name AS exCommodity from t_salesorder_entry soe 
			join m_excise_duty ed on soe.ex_duty_id=ed.id
			join m_excisable_goods exg on ed.ex_goods_id=exg.id 
			where soe.salesorder_id='$salesOrderId' and ed.product_id=0 group by exg.id";

		$recs = $this->databaseConnect->getRecords($qry);
		$exCommodity = array();
		foreach ($recs as $r) {
			$exCommodity[] = $r[0];
		}

		return (sizeof($exCommodity)>0)?implode(",",$exCommodity):"";
	}

	// Get Exemption chapter/subhead
	function getExCodeByProductId($productId)
	{
		$qry = "select chapter_subheading from m_excise_duty where product_id='$productId' and product_id!=0 ";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Distributor Location wise Billing Type
	function chkExportBilling($selDistributorId)
	{
		$qry = "select id from m_distributor_state where distributor_id='$selDistributorId' and export_active='Y'";

		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateSOInvSeqNum($salesOrderId, $invNum)
	{
		$qry = "update t_salesorder set inv_seq_num='$invNum' where id='$salesOrderId'";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	###---------------------------------- CODE FOR GENERATE	PO ID STARTS----------------------------------------------------------
	function chkValidGatePassId($selDate,$compId,$invUnit)
	{
		$qry	="select id,start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='SO' and billing_company_id='$compId' and unitid='$invUnit' and so_invoice_type='TA' or date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='SO' and billing_company_id='$compId' and unitid='0' and so_invoice_type='TA'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}

	function getAlphaCode($id)
	{
		$qry = "select alpha_code from number_gen where type='SO' and id='$id'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}

	function checkGatePassDisplayExist($numbergen)
	{
		$qry = "select (count(*)) from t_salesorder where number_gen_id='$numbergen'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getmaxGatePassId($numbergen)
	{
		$qry = "select po_no from  t_salesorder where number_gen_id='$numbergen'  order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getValidendnoGatePassId($selDate,$companyId,$unitId)
	{
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='SO' and billing_company_id='$companyId' and unitid='$unitId' and so_invoice_type='TA' OR date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='SO' and billing_company_id='$companyId' and unitid='0' and so_invoice_type='TA' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getValidGatePassId($selDate,$companyId,$unitId)
	{
		$qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='SO' and company_id='$companyId' and unit_id='$unitId' and so_invoice_type='TA' OR date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='PO' and company_id='$companyId' and unit_id='0' and so_invoice_type='TA'";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
###----------------------------------CODE FOR GENERATE	PO ID ENDS----------------------------------------------------------
	
	function checkIdExistInGatepass($salesOrderId)
	{
		$qry	= "SELECT id,gate_pass_no FROM `t_gate_pass` where so_id='$salesOrderId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";
	}
	
	function displayGatepass($company,$unit,$salesOrderId)
	{
		$qry	= "SELECT id,gate_pass_no FROM `t_gate_pass` where company_id='$company' and unit_id='$unit' and so_id is null";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		$result='<p>
			<form action="#" id="inputVal" name="inputVal">
			<table cellpadding="6" cellspacing="1" bgcolor="#999999" align="center">
				<tr bgcolor="#ffffff">
					<td colspan="2" bgcolor="#e8edff" class="listing-head" height="30">Link to gatepass</td>
				</tr>
				<tr bgcolor="#ffffff" height="40">
					<td class="listing-item"><b>Gate Pass</b></td>
					<td class="fieldName"><b><select name="linkGatePass" id="linkGatePass" ><option value="">--Select--</option>';
					foreach($rec as $rc)
					{
						$result.='<option value="'.$rc[0].'">'.$rc[1].'</option>';		
					}
						$result.='</select></td>
					<input name="salesOrderId" id="salesOrderId" value="'.$salesOrderId.'" type="hidden"/>
				</tr>
				<tr bgcolor="#ffffff" height="40" align="center"><td colspan="2"><input class="button" type="submit" style="height:18px; font-size:11px; font-align:center" value="Submit" name="gatePass" id="gatePass" ></td></tr>
			</table>
			</form>
		</p>';
		return $result;
	}
	
	function updateGatePass($salesOrderId,$userId,$gatePass)
	{
		$qry = "update t_gate_pass set so_id='$salesOrderId',modified=NOW(),modifiedby='$userId' where id='$gatePass'";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
}
?>