<?php
class Invoice
{  
	/****************************************************************
	This class deals with all the operations relating to Invoice 
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function Invoice(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Returns all Paging Records  // 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $invoiceTypeFilter=null)
	{
		$whr = "im.entry_date>='$fromDate' and im.entry_date<='$tillDate'";

		$limit		= " $offset, $limit ";

		$orderBy	= " im.invoice_no desc";

		$qry = " select im.id, im.invoice_no, im.invoice_date, im.customer_id, im.proforma_no, im.entry_date, im.po_id, im.invoice_type_id, mit.name, mc.customer_name, im.confirmed, if (im.total_usd_amt!=0,im.total_usd_amt,(select sum(tirm.value_usd) from t_invoice_rm_entry tirm where im.id=tirm.main_id group by tirm.main_id)) as usdAmt, im.ship_bill_no, im.bill_ladding_no, me.alpha_code,im.unitid,im.alphacode from 
			t_invoice_main im left join m_invoice_type mit on mit.id=im.invoice_type_id left join  m_customer mc on mc.id=im.customer_id left join m_exporter me on me.id=im.exporter_id";
		
		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
		if ($limit!="")		$qry 	.= " limit ".$limit;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}


	#Get Records For Selected Date Range
	function fetchAllRecords($fromDate, $tillDate, $invoiceTypeFilter=null)
	{
		$whr = "im.entry_date>='$fromDate' and im.entry_date<='$tillDate'";

		$orderBy	= " im.invoice_no desc";

		$qry = " select im.id, im.invoice_no, im.invoice_date, im.customer_id, im.proforma_no, im.entry_date, im.po_id, im.invoice_type_id, mit.name, mc.customer_name, im.confirmed, if (im.total_usd_amt!=0,im.total_usd_amt,(select sum(tirm.value_usd) from t_invoice_rm_entry tirm where im.id=tirm.main_id group by tirm.main_id)) as usdAmt, im.ship_bill_no, im.bill_ladding_no, me.alpha_code,im.unitid,im.alphacode from 
			t_invoice_main im left join m_invoice_type mit on mit.id=im.invoice_type_id left join  m_customer mc on mc.id=im.customer_id left join m_exporter me on me.id=im.exporter_id";
		
		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
				
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;		
	}

	function fetchAllRecordsInvoice()
	{
		$qry = " select im.id, im.invoice_no, im.invoice_date, im.customer_id, im.proforma_no, im.entry_date, im.po_id, im.invoice_type_id, mit.name, mc.customer_name, im.confirmed, if (im.total_usd_amt!=0,im.total_usd_amt,(select sum(tirm.value_usd) from t_invoice_rm_entry tirm where im.id=tirm.main_id group by tirm.main_id)) as usdAmt, im.ship_bill_no, im.bill_ladding_no, me.alpha_code,im.unitid,im.alphacode from 
			t_invoice_main im left join m_invoice_type mit on mit.id=im.invoice_type_id left join  m_customer mc on mc.id=im.customer_id left join m_exporter me on me.id=im.exporter_id";
			$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}


	// Find record
	/*function find($invoiceMainId)
	{
		$qry	= "select im.id, im.invoice_no, im.invoice_date, im.customer_id, im.proforma_no, im.entry_date, im.po_id, im.invoice_type_id, im.invoice_type, im.pre_carrier_place, im.final_destination, im.container_marks, im.goods_description, im.discount, im.discount_remark, im.discount_amt, im.total_net_wt, im.total_gross_wt, im.total_usd_amt, im.confirmed, im.ship_bill_no, im.ship_bill_date, im.bill_ladding_no, im.bill_ladding_date, im.loading_port, im.exporter_id, im.remarks, im.edit_log, im.terms_delivery_payment, im.pkg_remarks, im.total_value_rs,im.unitid,im.alphacode,me.code from t_invoice_main im left join m_eucode me on im.eucode=me.id where im.id='$invoiceMainId'";
		//echo $qry;

		return $this->databaseConnect->getRecord($qry);
	}*/

	function find($invoiceMainId)
	{
		$qry	= "select im.id, im.invoice_no, im.invoice_date, im.customer_id, im.proforma_no, im.entry_date, im.po_id, im.invoice_type_id, im.invoice_type, im.pre_carrier_place, im.final_destination, im.container_marks, im.goods_description, im.discount, im.discount_remark, im.discount_amt, im.total_net_wt, im.total_gross_wt, im.total_usd_amt, im.confirmed, im.ship_bill_no, im.ship_bill_date, im.bill_ladding_no, im.bill_ladding_date, im.loading_port, im.exporter_id, im.remarks, im.edit_log, im.terms_delivery_payment, im.pkg_remarks, im.total_value_rs,im.unitid,im.alphacode,me.code,invoice_alpha,invoice_numgen from t_invoice_main im left join m_eucode me on im.eucode=me.id where im.id='$invoiceMainId'";
		//echo $qry;

		return $this->databaseConnect->getRecord($qry);
	}

	# Get PO Recs	
	function getInvoiceItemRecs($mainId)
	{		
		/*$qry = "select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tire.id, tire.mc_in_po, tire.mc_in_invoice, tire.price_per_kg, tire.value_usd, tire.value_inr, mfp.filled_wt, mmcp.number_packs, tire.product_description, tire.net_wt, tire.gross_wt, tpore.wt_type, mfp.decl_wt, me.id as euCodeId, me.address as euCodeAddr,tm.unitid,tm.alphacode,tm.eucode
			from 
				t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id 
				left join m_processcode mpc on tpore.processcode_id=mpc.id
				left join m_eucode me on tpore.eucode_id=me.id
				left join m_brand mb on tpore.brand_id=mb.id
				left join m_grade mg on tpore.grade_id=mg.id 
				left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id
				left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id 
				left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id
				left join t_invoice_rm_entry tire on tire.po_entry_id=tpore.id left join t_invoice_main tm on tm.id=tire.main_id
			where tire.main_id='$mainId' order by tpore.id asc";*/

			/*$qry = "select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tire.id, tire.mc_in_po, tire.mc_in_invoice, tire.price_per_kg, tire.value_usd, tire.value_inr, mfp.filled_wt, mmcp.number_packs, tire.product_description, tire.net_wt, tire.gross_wt, tpore.wt_type, mfp.decl_wt, me.id as euCodeId, me.address as euCodeAddr,tm.unitid,tm.alphacode,tm.eucode
			from 
				t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id 
				left join m_processcode mpc on tpore.processcode_id=mpc.id
				left join m_eucode me on tm.eucode=me.id
				left join m_brand mb on tpore.brand_id=mb.id
				left join m_grade mg on tpore.grade_id=mg.id 
				left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id
				left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id 
				left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id
				left join t_invoice_rm_entry tire on tire.po_entry_id=tpore.id left join t_invoice_main tm on tm.id=tire.main_id
			where tire.main_id='$mainId' order by tpore.id asc";*/

			$qry = "select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tire.id, tire.mc_in_po, tire.mc_in_invoice, tire.price_per_kg, tire.value_usd, tire.value_inr, mfp.filled_wt, mmcp.number_packs, tire.product_description, tire.net_wt, tire.gross_wt, tpore.wt_type, mfp.decl_wt, me.id as euCodeId, me.address as euCodeAddr,tm.unitid,tm.alphacode,tm.eucode
			from 
				t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id 
				left join m_processcode mpc on tpore.processcode_id=mpc.id				
				left join m_brand mb on tpore.brand_id=mb.id
				left join m_grade mg on tpore.grade_id=mg.id 
				left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id
				left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id 
				left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id
				left join t_invoice_rm_entry tire on tire.po_entry_id=tpore.id left join t_invoice_main tm on tm.id=tire.main_id left join m_eucode me on tm.eucode=me.id where tire.main_id='$mainId' order by tpore.id asc";


		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}		
	
	# Container 
	function fetchAllContainerRecs()
	{
		$qry = "select id, container_id, container_no from t_container_main where container_id!=0 and container_no is not null and container_no!='' order by container_id desc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# get Container
	function getContainerRecs($invoiceId)
	{		
		$qry = "select tcm.id, tcm.container_id, tcm.container_no, tce.id, tcm.seal_no, tcm.vessal_details from t_container_entry tce join t_container_main tcm on tce.main_id=tcm.id where tcm.container_id!=0 and tcm.container_no is not null and tce.invoice_id='$invoiceId' order by tcm.container_id desc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update Invoice Main Rec
	function updateInvoiceRec($mainId, $invoiceConfirmed, $preCarrierPlace, $finalDestination, $containerMarks, $goodsDescription, $discount, $discountRemark, $discountAmt, $totNetWt, $totGrossWt, $totalValueInUSD, $invoiceNo, $invoiceDate, $shipBillNo, $shipBillDate, $billLaddingNo, $billLaddingDate, $loadingPort, $exporter, $shipInvRemark, $pkgListRemark, $expInvNum,$unitid,$unitalphacode,$invoiceAlpha,$invoiceNumGen)
	{
		$qry	= "update t_invoice_main set confirmed='$invoiceConfirmed', pre_carrier_place='$preCarrierPlace', final_destination='$finalDestination', container_marks='$containerMarks', goods_description='$goodsDescription', discount='$discount', discount_remark='$discountRemark', discount_amt='$discountAmt', total_net_wt='$totNetWt', total_gross_wt='$totGrossWt', total_usd_amt='$totalValueInUSD', invoice_no='$invoiceNo', invoice_date='$invoiceDate', ship_bill_no='$shipBillNo', ship_bill_date='$shipBillDate', bill_ladding_no='$billLaddingNo', bill_ladding_date='$billLaddingDate', loading_port='$loadingPort', exporter_id='$exporter', remarks='$shipInvRemark', pkg_remarks='$pkgListRemark', exp_invoice_no='$expInvNum',unitid='$unitid',alphacode='$unitalphacode',invoice_alpha='$invoiceAlpha',invoice_numgen='$invoiceNumGen' where id='$mainId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateInvoiceRec_old($mainId, $invoiceConfirmed, $preCarrierPlace, $finalDestination, $containerMarks, $goodsDescription, $discount, $discountRemark, $discountAmt, $totNetWt, $totGrossWt, $totalValueInUSD, $invoiceNo, $invoiceDate, $shipBillNo, $shipBillDate, $billLaddingNo, $billLaddingDate, $loadingPort, $exporter, $shipInvRemark, $pkgListRemark, $expInvNum,$unitid,$unitalphacode)
	{
		$qry	= "update t_invoice_main set confirmed='$invoiceConfirmed', pre_carrier_place='$preCarrierPlace', final_destination='$finalDestination', container_marks='$containerMarks', goods_description='$goodsDescription', discount='$discount', discount_remark='$discountRemark', discount_amt='$discountAmt', total_net_wt='$totNetWt', total_gross_wt='$totGrossWt', total_usd_amt='$totalValueInUSD', invoice_no='$invoiceNo', invoice_date='$invoiceDate', ship_bill_no='$shipBillNo', ship_bill_date='$shipBillDate', bill_ladding_no='$billLaddingNo', bill_ladding_date='$billLaddingDate', loading_port='$loadingPort', exporter_id='$exporter', remarks='$shipInvRemark', pkg_remarks='$pkgListRemark', exp_invoice_no='$expInvNum',unitid='$unitid',alphacode='$unitalphacode' where id='$mainId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function setInvoiceStatus($mainId, $invoiceConfirmed)
	{
		$qry	= "update t_invoice_main set confirmed='$invoiceConfirmed' where id='$mainId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update Invoice Entry Recs
	function updateInvoiceEntryRec($invoiceEntryId, $productDescr, $netWt, $grossWt, $prodOriginType)
	{
		$qry = "update t_invoice_rm_entry set product_description='$productDescr', net_wt='$netWt', gross_wt='$grossWt', prd_origin_type='$prodOriginType' where id='$invoiceEntryId' ";
		
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# insert Invoice Entry Packing Recs
	function insertInvoiceEntryRec($mainId,$productDescr,$rowParentId, $prodOriginType)
	{
		$qry	= "insert into t_invoice_rm_entry(main_id,product_description,parent_id,prd_origin_type) values('$mainId','$productDescr','$rowParentId', '$prodOriginType') ";
		
		$result	= $this->databaseConnect->insertRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	# Link Invoice to container
	function insertInvoice2Container($invoiceId, $selContainerId)
	{
		$qry = "insert into t_container_entry (main_id, invoice_id) values('$selContainerId','$invoiceId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Delete invoice from Container Entry
	function deleteInvoiceFromContainer($containerEntryId)
	{
		$qry	= " delete from t_container_entry where id='$containerEntryId' ";
		
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
	# ----------------------------
	# Check Invoice Number Exist
	# ----------------------------
	# Check valid Invoice Num
	function chkValidInvoiceNum($selDate, $invoiceNum, $invoiceType, $exporterId)
	{
		$whr = " type='SPO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum' and (exporter_id='$exporterId' or exporter_id='0')";

		if ($invoiceType=='S') $whr .= " and so_invoice_type='SA'";
		else $whr .= " and so_invoice_type='TA'";

		$qry	= "select start_no, end_no from number_gen ";
		if ($whr!="") $qry .= " where ".$whr;
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	

	# Check SO Number Exist
	function checkInvoiceNumberExist($invoiceNo, $cSOId, $invoiceType, $selDate, $exporterId,$unitid)
	{
		$soYear	 = date("Y", strtotime($selDate));
		list($startYear,$endYear) = $this->getValidRecs($selDate, $invoiceNo, $invoiceType, $exporterId,$unitid);
		
		//$whr = " EXTRACT(YEAR FROM invoice_date) ='$soYear' and (exporter_id='$exporterId' or exporter_id='0' or exporter_id is null) ";
		$whr = " EXTRACT(YEAR FROM invoice_date) >='$startYear' and EXTRACT(YEAR FROM invoice_date) <='$endYear' and (exporter_id='$exporterId' or exporter_id='0' or exporter_id is null) and unitid='$unitid'";

		if ($cSOId!="") $whr .= " and id!=$cSOId";		
		if ($invoiceType=='S') $whr .= " and sample_invoice_no='$invoiceNo'";
		else $whr .= " and invoice_no='$invoiceNo'";

		$qry = " select id from t_invoice_main";
		if ($whr!="") $qry .= " where ".$whr;
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	function getValidRecs($selDate, $invoiceNum, $invoiceType, $exporterId,$unitid)
	{
		$whr = " type='SPO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum' and (exporter_id='$exporterId' or exporter_id='0') and unitid='$unitid'";

		if ($invoiceType=='S') $whr .= " and so_invoice_type='SA'";
		else $whr .= " and so_invoice_type='TA'";

		$qry	= "select start_no, end_no, EXTRACT(YEAR FROM start_date), EXTRACT(YEAR FROM end_date) from number_gen ";
		if ($whr!="") $qry .= " where ".$whr;
		
		$rec = $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return (sizeof($rec)>0)?array($rec[0][2],$rec[0][3]):array();
	}
	
	#Checking the Selected Invoice is cancelled
	function checkCancelledInvoice($invNo, $selDate, $invoiceType)
	{
		$soYear	 = date("Y", strtotime($selDate));

		//$qry	= "select invoice_no from s_cancelled_invoice where invoice_no='$invNo' and inv_year='$soYear' and inv_type='$invoiceType' ";
		$qry = "";

		$rec = $this->databaseConnect->getRecords($qry);
		//return (sizeof($rec)>0)?true:false;
		return false;
	}
	
	// Ends Here //	

	function chkValidInvoiceNumber($selDate, $invoiceNum, $invoiceType,$invoiceAlpha,$invoiceNumGen, $exporterId)
	{
		$whr = " type='SPO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum' and (exporter_id='$exporterId' or exporter_id='0')";

		if ($invoiceType=='S') $whr .= " and so_invoice_type='SA'";
		else $whr .= " and so_invoice_type='TA'";

		$qry	= "select start_no, end_no from number_gen ";
		if ($whr!="") $qry .= " where ".$whr;
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	
	
	// ---------------  invoice Number check ----------
	# Get Next Invoice Number
		
	function getNextInvoiceNo_old($invoiceType)
	{	
		$selDate = date("Y-m-d");
		list($invoiceNo, $invoiceDate) = $this->getMaxInvoiceNo($invoiceType);
		$validSONum = $this->chkValidInvoiceNum($invoiceDate, $invoiceNo, $invoiceType);
		if ($validSONum) return $invoiceNo+1;
		else return $this->getCurrentInvoiceNo($selDate, $invoiceType);
	}
	
	function getNextInvoiceNo($invoiceType)
	{	
		$selDate = date("Y-m-d");
		list($invoiceNo, $invoiceDate, $invoiceAlpha,$invoiceNumGen,$exporterId) = $this->getMaxInvoiceNo($invoiceType);
		$validSONum = $this->chkValidInvoiceNumber($invoiceDate, $invoiceNo, $invoiceType, $invoiceAlpha,$invoiceNumGen,$exporterId);
		if ($validSONum) return array($invoiceNo+1,$invoiceAlpha,$invoiceNumGen);
		else return $this->getCurrentInvoiceNo($selDate, $invoiceType);
	}
	
	function getMaxInvoiceNo($invoiceType)
	{
		if ($invoiceType=='S') $qry = " select max(sample_invoice_no), invoice_date ,invoice_alpha,	invoice_numgen ,exporter_id from t_invoice_main where sample_invoice_no!=0 group by id order by id desc, invoice_date desc";
		else $qry = " select max(invoice_no), invoice_date from t_invoice_main where invoice_no!=0 group by id order by id desc, invoice_date desc";

		$rec = $this->databaseConnect->getRecord($qry);
		//print_r($rec);
		return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4]);
	}
	
	function getCurrentInvoiceNo($selDate, $invoiceType)
	{		
		$whr = " date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SPO' ";

		if ($invoiceType=='S') $whr .= " and so_invoice_type='SA'";
		else $whr .= " and so_invoice_type='TA'";

		$qry	= "select start_no, end_no,alpha_code,id from number_gen ";
		if ($whr!="") $qry .= " where ".$whr;	

		$result = $this->databaseConnect->getRecord($qry);
		return array($result[0],$result[2],$result[3]);
	
		//return (sizeof($result)>0)?$result[0][0]:"";
	}

	function getCurrentInvoiceNo_old($selDate, $invoiceType)
	{		
		$whr = " date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SPO' ";

		if ($invoiceType=='S') $whr .= " and so_invoice_type='SA'";
		else $whr .= " and so_invoice_type='TA'";

		$qry	= "select start_no, end_no from number_gen ";
		if ($whr!="") $qry .= " where ".$whr;	

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
		
	// ------------------------- invoice number check ends here ----------	
	

	# Invoice Display Section starts here
	# Get Customer Rec	
	function getCustomerRec($customerId)
	{
		$qry	= "select mc.customer_name, mc.address, cntry.name from m_customer mc left join m_country cntry on mc.country_id=cntry.id where mc.id='$customerId' ";
		$rec = $this->databaseConnect->getRecord($qry);

		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2]):array();
	}
	
	# Get PO details
	function getPurchaseOrderRec($purchaseOrderId)
	{
		$qry = " select tpom.po_no, tpom.po_date, tpom.discharge_port, mptt.mode as paymentMode, mptt.descr as paymentTerms, mcp.port_name, mcm.name as carriageMode, mc.name as countryName, tpom.other_buyer
				from t_purchaseorder_main tpom left join m_paymentterms mptt on mptt.id=tpom.payment_term
				left join m_country_port mcp on mcp.id=tpom.port_id
				left join m_carriage_mode mcm on mcm.id=tpom.carriage_mode_id
				left join m_country mc on mc.id=tpom.country_id
			where tpom.id='$purchaseOrderId' ";
			
		$rec = $this->databaseConnect->getRecord($qry);

		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8]):array();
	}
	
	# Get PO Recs	
	function getInvoiceRec($mainId)
	{		
		$qry = " select sum(tire.mc_in_invoice) from t_invoice_rm_entry tire where tire.main_id='$mainId' group by tire.main_id";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
	
	# get Container Vessal
	function getContVessalRecs($invoiceId)
	{		
		$qry = "select tcm.vessal_details, tcm.container_type, tcm.sailing_on, msc.name as shippingLine, mc.name as city, msc.address as shippingAddress
			from 
			t_container_entry tce join t_container_main tcm on tce.main_id=tcm.id 
			left join m_shipping_company msc on tcm.shipping_line_id=msc.id
			left join m_city mc on msc.city_id=mc.id 
			where tcm.container_id!=0 and tcm.container_no is not null and tce.invoice_id='$invoiceId' group by tcm.vessal_details ";
			//order by sailing_on desc

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array(sizeof($result), $result[0][0], $result[0][1], $result[0][2], $result[0][3], $result[0][4], $result[0][5]):array();
	}
	# Invoice Display Section Ends here

	#Delete Invoice Entry Record
	function deleteInvoiceEntryRec($invoiceMainId)
	{
		$qry	=	" delete from t_invoice_rm_entry where main_id='$invoiceMainId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Delete Invoice main Record
	function deleteInvoiceMainRec($invoiceMainId)
	{
		$qry	=	" delete from t_invoice_main where id='$invoiceMainId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getPackingDescription($mainId, $parentId=null)	 
	{
		if (!empty($parentId) && $parentId>0 ) {
			$qry= " SELECT `id`,`main_id`,`product_description`,`parent_id`, prd_origin_type FROM `t_invoice_rm_entry` WHERE `main_id`=".$mainId." AND `parent_id`=".$parentId;
		} else {
			$qry= " SELECT `id`,`main_id`,`product_description`,`parent_id`, prd_origin_type FROM `t_invoice_rm_entry` WHERE `main_id`=".$mainId." AND `parent_id`!=0";
		}

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function updateBRCRec($mainId, $brcIECCodeNo, $brcDEPBEnrolNo, $brcExportBillTo, $brcGoodsDescription, $brcBillAmt, $brcFreightAmt, $brcInsuranceAmt, $brcCommissionDiscount, $brcFreeConvert, $brcFOBValue, $brcRealisationDate, $brcLicenceCategory, $brcRefNo, $brcRefNoDate, $brcFgnExDealerCodeNo, $brcExporterName, $brcExportDate, $brcCertifyAmtDescr, $brcFreightAmtUSD, $brcFreightAmtRsPerUSD, $brcFreightAmtRs, $brcInsuranceAmtUSD, $brcInsuranceAmtRsPerUSD, $brcInsuranceAmtRs, $brcCommissionDiscountUSD, $brcCommissionDiscountRsPerUSD, $brcCommissionDiscountRs, $brcFOBValueUSD, $brcFOBValueRs)
	{

		$qry	= "update t_invoice_main set brc_IEC_code_no='$brcIECCodeNo', brc_DEPB_enrol_no='$brcDEPBEnrolNo', brc_export_bill_to='$brcExportBillTo', brc_goods_description='$brcGoodsDescription', brc_bill_amt='$brcBillAmt', brc_freight_amt='$brcFreightAmt', brc_insurance_amt='$brcInsuranceAmt', brc_commission_discount='$brcCommissionDiscount', brc_free_convert='$brcFreeConvert', brc_FOB_value='$brcFOBValue', brc_realisationDate='$brcRealisationDate', brc_licence_category='$brcLicenceCategory', brc_ref_no='$brcRefNo', brc_ref_no_date='$brcRefNoDate', brc_FE_dealer_code_no='$brcFgnExDealerCodeNo', brc_exporter_name='$brcExporterName', brc_export_date='$brcExportDate', brc_certify_amt_descr='$brcCertifyAmtDescr',
		brc_freight_amt_usd='$brcFreightAmtUSD', brc_freight_amt_rs_usd='$brcFreightAmtRsPerUSD', brc_freight_amt_rs='$brcFreightAmtRs', brc_insurance_amt_usd='$brcInsuranceAmtUSD', brc_insurance_amt_rs_usd='$brcInsuranceAmtRsPerUSD', brc_insurance_amt_rs='$brcInsuranceAmtRs', brc_commission_discount_usd='$brcCommissionDiscountUSD', brc_commission_discount_rs_usd='$brcCommissionDiscountRsPerUSD', brc_commission_disocunt_rs='$brcCommissionDiscountRs', brc_FOB_value_usd='$brcFOBValueUSD', brc_FOB_value_rs='$brcFOBValueRs'
		where id='$mainId'";


		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function findBRCRec($invoiceMainId)
	{
		$qry	= "select id, brc_IEC_code_no, brc_DEPB_enrol_no, brc_export_bill_to, brc_goods_description, brc_bill_amt, brc_freight_amt, brc_insurance_amt, brc_commission_discount, brc_free_convert, brc_FOB_value, brc_realisationDate, brc_licence_category, brc_ref_no, brc_ref_no_date, brc_FE_dealer_code_no, brc_exporter_name, brc_export_date, brc_certify_amt_descr, brc_freight_amt_usd, brc_freight_amt_rs_usd, brc_freight_amt_rs, brc_insurance_amt_usd, brc_insurance_amt_rs_usd, brc_insurance_amt_rs, brc_commission_discount_usd, brc_commission_discount_rs_usd, brc_commission_disocunt_rs, brc_FOB_value_usd, brc_FOB_value_rs from t_invoice_main im where im.id='$invoiceMainId'";

		return $this->databaseConnect->getRecord($qry);
	}

	function getPORec($purchaseOrderId)
	{
		$qry = " select tpom.po_no, tpom.po_date, tpom.discharge_port, mptt.mode as paymentMode, mptt.descr as paymentTerms, mcp.port_name, mcm.name as carriageMode, mc.name as countryName, tpom.other_buyer, mcy.code as currencyCode, tpom.unit_id as POUnitId
				from t_purchaseorder_main tpom left join m_paymentterms mptt on mptt.id=tpom.payment_term
				left join m_country_port mcp on mcp.id=tpom.port_id
				left join m_carriage_mode mcm on mcm.id=tpom.carriage_mode_id
				left join m_country mc on mc.id=tpom.country_id
				left join m_currency_ratelist mcrl on mcrl.id=tpom.currency_ratelist_id
				left join m_currency mcy on mcy.id=mcrl.currency_id
			where tpom.id='$purchaseOrderId' ";
			
		$rec = $this->databaseConnect->getRecord($qry);

		return $rec;
	}

	function updateTDP($mainId, $tdpContent)
	{
		$qry	= "update t_invoice_main set terms_delivery_payment='$tdpContent' where id='$mainId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateEditLog($mainId, $editHistory)
	{
		$qry	= "update t_invoice_main set edit_log='$editHistory' where id='$mainId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	function updateDebitNoteRec($mainId, $dnFreight, $dnBkgFreight, $dnExRate, $dnTotalBkg, $dnGrossAmt, $dnTdsAmt, $dnNetAmt, $dnChqNo, $dnChqDate)
	{
		$qry	= "update t_invoice_main set dn_freight='$dnFreight', dn_bkg_freight='$dnBkgFreight', dn_ex_rate='$dnExRate', dn_total_bkg='$dnTotalBkg', dn_gross_amt='$dnGrossAmt', dn_tds_amt='$dnTdsAmt', dn_net_amt='$dnNetAmt', dn_chq_no='$dnChqNo', dn_chq_date='$dnChqDate' where id='$mainId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	// Debit Note
	function findDNRec($invoiceMainId)
	{
		$qry	= "select id, dn_freight, dn_bkg_freight, dn_ex_rate, dn_total_bkg, dn_gross_amt, dn_tds_amt, dn_net_amt, dn_chq_no, dn_chq_date from t_invoice_main im where im.id='$invoiceMainId'";

		return $this->databaseConnect->getRecord($qry);
	}


	function AddSplitup($invoiceId, $splitupStr)
	{
		$siaArr = explode(",",$splitupStr);
		$arrSize = sizeof($siaArr);
		
		$rowCount = 0;

		if ($arrSize>0) 
		{

			// Delete All Entries First
			$this->deleteSplitupAmt($invoiceId);
				
			foreach ($siaArr as $k=>$rowVal) 
			{
					$rowArr = explode(":",$rowVal);

					$splitCurrency	= $rowArr[0];
					$INRPerCurrency = $rowArr[1];
					$totRs			= $rowArr[2];
					$insertStatus =  $this->insertSplitAmt($invoiceId, $splitCurrency, $INRPerCurrency, $totRs);
					if ($insertStatus) {
						$rowCount++;
					}				
			}
		}

		return ($arrSize==$rowCount)?true:false;		
	}


	# Link Invoice to Split Amt
	function insertSplitAmt($invoiceId, $splitCurrency, $INRPerCurrency, $totRs)
	{
		$qry = "insert into t_invoice_amt_splitup (invoice_id, currency_amt, rs_per_currency,`total_rs`) values('$invoiceId', '$splitCurrency', '$INRPerCurrency', '$totRs')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function deleteSplitupAmt($invoiceId)
	{
		$qry	= " delete from t_invoice_amt_splitup where invoice_id='$invoiceId' ";
		
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function GetSplitup($invoiceId)
	{
		$results = $this->fetchAllSplitUpAmt($invoiceId);
		return json_encode($results);
	}

	function fetchAllSplitUpAmt($invoiceId)
	{
		$qry = "select currency_amt, rs_per_currency, total_rs
			from 
				t_invoice_amt_splitup
			where invoice_id='$invoiceId' order by id asc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateInvValue($invoiceId, $totValueInRs)
	{
		$qry	= "update t_invoice_main set total_value_rs='$totValueInRs' where id='$invoiceId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function fetchAllRecordsUnitsActive($exporter)
	{
		//$qry="select unitno,unitname from m_exporter_unit mu leftjoin m_plant mp on mu.unitno=mp.no where exporterid=$exporter order by unitno asc";
		if ($exporter=="")
		{
			$qry="select unitno,mp.name,default_row from m_exporter me left join m_exporter_unit mu on me.id=mu.exporterid left join m_plant mp on mu.unitno=mp.no where default_row='Y' order by unitno asc";
		}
		else {
		$qry="select unitno,name from m_exporter_unit mu left join m_plant mp on mu.unitno=mp.id where exporterid='$exporter' order by unitno asc";
		}
		//$qry	=	"select *from m_exporter_unit where exporterid=$exporter order by unitno asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	function fetchAllRecordsUnitsActiveExpId($exporter)
	{
		
		$qry="select unitno,name from m_exporter_unit mu left join m_plant mp on mu.unitno=mp.id where exporterid='$exporter' order by unitno asc";
		
		//$qry	=	"select *from m_exporter_unit where exporterid=$exporter order by unitno asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		//return $result;
	}
	function fetchAlldata($unitId,$exporterId)
	{
		$qry = "select unitcode from m_exporter_unit where exporterid='$exporterId' and unitno='$unitId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getPurchaseOrderId($invoiceId)
	{
		$qry = "select po_id from t_invoice_main where id='$invoiceId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}
	function updatePurchaseOrderStatus($poId)
	{
		$qry	= "update t_purchaseorder_main set complete='NULL' where id='$poId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	

	
}

?>