<?php
class OrderDispatched
{
	/****************************************************************
	This class deals with all the operations relating to Order Dispatched
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function OrderDispatched(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Filter sales Order Recs
	function filterSalesOrderRecs($selSOId)
	{		
		//$qry = "select a.id, a.salesorder_id, a.product_id, a.rate, a.quantity, a.total_amount, b.name from t_salesorder_entry a, t_combo_matrix b where b.id=a.product_id and a.salesorder_id='$selSOId' ";
		//$qry = "select a.id, a.salesorder_id, a.product_id, a.rate, a.quantity, a.total_amount, a.mc_pkg_id, a.mc_pack, a.loose_pack, a.dist_mgn_state_id, a.tax_percent, a.p_gross_wt, a.p_mc_wt, a.free_pkts, a.basic_rate from t_salesorder_entry a where a.salesorder_id='$editSalesOrderId' ";
		$qry = "select a.id, a.salesorder_id, a.product_id, a.rate, a.quantity, a.total_amount, a.mc_pkg_id, a.mc_pack, a.loose_pack, a.dist_mgn_state_id, a.tax_percent, a.p_gross_wt, a.p_mc_wt, a.free_pkts, a.basic_rate from t_salesorder_entry a where a.salesorder_id='$selSOId' ";
		//echo $qry;
		//$result = array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Update Sales Order
	function updateSalesOrder($selSOId, $paymentStatus, $dispatchDate, $selStatus, $isComplete, $selTransporter, $docketNo, $transporterRateListId, $dateExtended, $transOtherChargeRateListId, $invoiceNo, $invoiceDate, $invoiceType)
	{		
		//$qry = "update t_salesorder set payment_status='$paymentStatus', dispatch_date='$dispatchDate', status_id='$selStatus', complete_status='$isComplete', status='$isComplete', transporter_id='$selTransporter', docket_no='$docketNo', transporter_rate_list_id='$transporterRateListId', extended='$dateExtended', trans_oc_rate_list_id='$transOtherChargeRateListId', so='$invoiceNo', invoice_date='$invoiceDate' where id=$selSOId";

		$uptdQry = "";
		//if ($invoiceType=='T' && $isComplete=='C') $uptdQry = " , so='$invoiceNo', proforma_no='0', proforma_date='0000-00-00'";
		if ($invoiceType=='T' && $isComplete=='C') $uptdQry = " , so='$invoiceNo'";
		else if ($invoiceType=='S' && $isComplete=='C') $uptdQry = " , sample_invoice_no='$invoiceNo', sample_invoice_date='$invoiceDate' ";

		$qry = "update t_salesorder set payment_status='$paymentStatus', dispatch_date='$dispatchDate', status_id='$selStatus', complete_status='$isComplete', status='$isComplete', transporter_id='$selTransporter', docket_no='$docketNo', transporter_rate_list_id='$transporterRateListId', extended='$dateExtended', trans_oc_rate_list_id='$transOtherChargeRateListId', invoice_date='$invoiceDate' $uptdQry where id=$selSOId";
		//echo $qry;
		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}
		
	# Find Record based on SO id 
	function findSORecord($sOId)
	{
		$qry = "select a.id, a.so, a.distributor_id, a.invoice_date, a.createdby, a.status, b.name, a.status_id, a.payment_status, a.dispatch_date, a.gross_wt, a.transporter_id, a.docket_no, a.transporter_rate_list_id, a.complete_status, a.tax_applied, a.round_value, a.grand_total_amt, a.invoice_type, a.adnl_item_total_wt, a.last_date, a.extended, a.trans_oc_rate_list_id, a.discount, a.discount_remark, a.discount_percent, a.discount_amt, a.octroi_exempted, a.oec_no, a.oec_date, a.oec_issued_date, a.proforma_no, a.proforma_date, a.sample_invoice_no, a.sample_invoice_date, a.net_wt, a.num_box, a.pkng_gen, a.pkng_confirm from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.id='$sOId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Returns all Not Completed Sales Order Records
	function fetchNotCompleteRecords()
	{
		//$qry = "select id, so, distributor_id, invoice_date, createdby from t_salesorder where complete_status<>'C' or complete_status is null order by so desc";		
		$qry = "select id, so, distributor_id, invoice_date, createdby from t_salesorder order by so desc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on Id
	function getProductExistingQty($productId)
	{
		$qry = "select actual_qty from m_product_manage where id=$productId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	#Update the Balance Stock Qty
	function updateBalanceStockQty($productId, $balanceQty)
	{
		$qry = "update m_product_manage set actual_qty='$balanceQty' where id='$productId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Check Valid Transporter
	function checkValidTransporter($transporterId)
	{
		$cDate = date("Y-m-d");

		$qry = "select id from m_transporter_status where transporter_id='$transporterId' and ('$cDate'>=date_format(valid_from,'%Y-%m-%d') and '$cDate'<=date_format(valid_to,'%Y-%m-%d') or '$cDate'>=date_format(valid_from,'%Y-%m-%d') and '$cDate'<=date_format(valid_to,'%Y-%m-%d'))";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Get istributor Account Rec
	function getDistAccountRec($selSOId)
	{
		$qry = " select id from t_distributor_ac where so_id='$selSOId'";
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";	
	}
	# Upate Dist Account
	function updateDistAccount($distAccountId, $salesOrderTotalAmt)
	{
		$qry = "update t_distributor_ac set amount='$salesOrderTotalAmt' where id='$distAccountId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Get SOrders Based on selection
	function getSalesOrders($fromDate, $tillDate, $invoiceType)
	{
		$invType = ($invoiceType=="")?'T':$invoiceType;

		$qry = "select a.id, a.so, b.name, a.proforma_no, a.sample_invoice_no from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.invoice_type='$invType' order by a.invoice_date desc, a.so desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$soNo 	= $v[1];
				$pfNo 	= $v[3];
				$saNo	= $v[4];
				$invoiceNo = "";
				if ($soNo!=0) $invoiceNo=$soNo;
				else if ($invType=='T') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "$saNo";
				$distName = $v[2];

				$displayTxt = $invoiceNo." (".$distName.")";
				$resultArr[$v[0]] = $displayTxt;
			}
		}
		return $resultArr;
	}

	# Report efinition rec
	function getDistributorReportDefinition($distributorId)
	{
		$qry = " select rate_margin_id, grouped_mgn_ids from m_dist_report_definition where distributor_id='$distributorId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?array($result[0], $result[1]):array();
	}

	# Dist Mgn Percent
	/*
	function getDistMarginPercent($distributorId, $distMgnRateListId, $productId, $stateId, $cityId, $marginHeadId)
	{
		$whr = " c.dist_state_entry_id=b.id and b.distributor_margin_id=a.id and a.distributor_id='$distributorId' and a.rate_list_id='$distMgnRateListId' and b.state_id='$stateId' and c.margin_structure_id='$marginHeadId'";

		if ($productId) $whr .= " and a.product_id='$productId'";

		$qry = " select distinct c.percentage from m_distributor_margin a, m_distributor_margin_state b, m_distributor_margin_entry c ";
		if ($whr!="") $qry .= " where ".$whr;
	
		//echo "<br/>$qry<br/>";
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}
	*/

	function getDistMgnPercent($distributorId, $distMgnRateListId, $productId, $stateId, $cityId, $marginHeadId)
	{
		$whr = " c.dist_state_entry_id=b.id and b.distributor_margin_id=a.id and a.distributor_id='$distributorId' and a.rate_list_id='$distMgnRateListId' and b.state_id='$stateId' and c.margin_structure_id='$marginHeadId'";

		if ($productId) $whr .= " and a.product_id='$productId'";

		$qry = " select distinct c.percentage from m_distributor_margin a, m_distributor_margin_state b, m_distributor_margin_entry c ";
		if ($whr!="") $qry .= " where ".$whr;
	
		//echo "<br/>$qry<br/>";
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	function getDistMarginPercent($distributorId, $distMgnRateListId, $productId, $stateId, $cityId, $marginHeadId)
	{
		$whr = " c.dist_state_entry_id=b.id and b.distributor_margin_id=a.id and a.distributor_id='$distributorId' and a.rate_list_id='$distMgnRateListId' and b.state_id='$stateId' and c.margin_structure_id in ($marginHeadId) and c.margin_structure_id=d.id";

		if ($productId) $whr .= " and a.product_id='$productId'";
		
		$orderBy	=  " d.use_avg_dist asc, d.display_order asc";
		//order by 

		$qry = " select c.percentage, d.price_calc, d.use_avg_dist, d.billing_form_f from m_distributor_margin a, m_distributor_margin_state b, m_distributor_margin_entry c, m_margin_structure d ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
	
		//echo "<br/>$qry<br/>";
		$mgnRecords	= $this->databaseConnect->getRecords($qry);
		$avgMargin = 0;
		if (sizeof($mgnRecords)>0) {
			$calcMarkUpValue 	= 0;
			$calcMarkDownValue 	= 0;
			$totalMarkUpValue	= 1;
			$totalMarkDownValue 	= 1;
			$actualValue = 0;
			foreach ($mgnRecords as $msr) {				
				$distMarginPercent	= $msr[0];
				$priceCalcType		= $msr[1];
				$useAvgDistMagn		= $msr[2];
				$mgnStructBillingOnFormF = $msr[3];

				$actualValue =  $distMarginPercent/100;

				if ($useAvgDistMagn=='Y') {				
					if ($priceCalcType=='MU') {
						$calcMarkUpValue = 1+$actualValue;
						$totalMarkUpValue /= $calcMarkUpValue;			
					}		
					if ($priceCalcType=='MD') {
						$calcMarkDownValue = 1-$actualValue;			
						$totalMarkDownValue *= $calcMarkDownValue;
					}
				}
				//echo "<br/>$actualValue<br/>";
			}
			$calcDistMargin = (1-($totalMarkUpValue*$totalMarkDownValue))*100;
			
			
		}
		if ($calcDistMargin) $avgMargin = number_format($calcDistMargin,4,'.','');
		//echo "<br>Margin==>$avgMargin<==<br>";
		//return (sizeof($result)>0)?$result[0]:"";
		return $avgMargin;
	}
	

	# Total MC Pack
	function getTotalMCPack($salesOrderId)
	{
		$qry = " select sum(mc_pack) from t_salesorder_entry where salesorder_id='$salesOrderId' group by salesorder_id ";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	# Discunt Splitup recs
	function getDiscountSplitupRecs($distributorId)
	{
		$qry = " select 
				b.margin_structure_id, c.name, c.price_calc, b.display_name
			from  
				m_dist_report_definition a, m_dist_report_definition_entry b, m_margin_structure c 
			where 
				b.margin_structure_id=c.id and a.id=b.main_id and a.distributor_id='$distributorId'
			";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Product identifier
	function getProductIdentifier($distributorId, $productId)
	{
		$qry = " select index_no from m_product_identifier where distributor_id='$distributorId' and product_id='$productId' ";
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	# Get Product MRP
	function getProductMRP($productId, $productMRPRateListId, $distributorId, $stateId)
	{		
		//$qry = " select mrp from m_product_mrp where product_id='$productId' and rate_list_id='$productMRPRateListId' ";
		$qry = " select pme.mrp from m_product_mrp pm join m_product_mrp_expt pme on pme.product_mrp_id=pm.id where pm.product_id='$productId' and pm.rate_list_id='$productMRPRateListId' and (pme.distributor_id='$distributorId' or pme.distributor_id=0) and (pme.state_id='$stateId' or pme.state_id=0) order by pme.state_id desc, pme.distributor_id desc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Ins Packing Instruction
	function addPackingInstruction($selSOId, $userId)
	{
		$qry = "insert into t_pkng_inst (so_id, created, created_by) values('$selSOId', NOW(), '$userId')";
		//echo $qry."<br>";			
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
		//echo $qry;
		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}

}
?>