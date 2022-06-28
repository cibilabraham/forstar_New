<?php
class PurchaseOrder
{  
	/****************************************************************
	This class deals with all the operations relating to Shipment > Purchase Order
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PurchaseOrder(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# --------- Proforma invoice Number starts ----
	# Get Next Proforma Invoice Number	
	function getNextProformaInvoiceNo()
	{	
		$selDate = date("Y-m-d");
		$soYear	 = date("Y", strtotime($selDate));
		list($soNum, $invoiceDate) = $this->getMaxProformaNum($soYear, $selDate);
		$validSONum = $this->getValidProformaNum($soNum, $invoiceDate);		
		if ($validSONum) return $soNum+1;
		else return $this->getCurrentProformaNum($selDate);
	}

	function getMaxProformaNum($soYear, $selDate)
	{
		//$qry = " select max(proforma_no), entry_date from t_invoice_main where proforma_no!=0 and EXTRACT(YEAR FROM entry_date)='$soYear' group by id order by id desc, entry_date desc ";
		$qry = "select max(proforma_no) , max(entry_date) from t_invoice_main where proforma_no!=0 and date_format(entry_date,'%Y-%m-%d')>=(select date_format(start_date,'%Y-%m-%d') from number_gen where type='SPO' and so_invoice_type='PF' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate') and date_format(entry_date,'%Y-%m-%d')<=(select date_format(end_date,'%Y-%m-%d') from number_gen where type='SPO' and so_invoice_type='PF' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate') group by null";
		//echo "Max=<br>$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

	function getValidProformaNum($soNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SPO' and so_invoice_type='PF' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and start_no<='$soNum' and end_no>='$soNum' ";
		//echo "Valid check==><br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getCurrentProformaNum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where type='SPO' and so_invoice_type='PF' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' ";
		//echo $qry;
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
		$qry = " select max(sample_invoice_no), sample_invoice_date from t_purchaseorder_main where sample_invoice_no!=0 and po_year='$soYear' group by id order by id desc, sample_invoice_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

	function getValidSampleNum($soNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SPO' and so_invoice_type='SA' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and start_no<='$soNum' and end_no>='$soNum' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getCurrentSampleNum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where type='SPO' and so_invoice_type='SA' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate'";
		//echo $qry;
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
		$qry	= "select start_no, end_no from number_gen where type='SPO' and so_invoice_type='PF' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum'";	
		echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	

	function checkProformaNumExist($soId, $cSOId)
	{
		if ($cSOId!="") $uptdQry = " and id!=$cSOId";
		else $uptdQry = "";
		//$qry = " select id from t_purchaseorder_main where proforma_no='$soId' $uptdQry";
		$qry = " select id from t_invoice_main where proforma_no='$soId' $uptdQry";
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
		$qry	= "select start_no, end_no from number_gen where type='SPO' and so_invoice_type='SA' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum'";	
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	

	function checkSampleNumExist($soId, $cSOId)
	{
		if ($cSOId!="") $uptdQry = " and id!=$cSOId";
		else $uptdQry = "";
		$qry = " select id from t_purchaseorder_main where sample_invoice_no='$soId' $uptdQry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	// Ends Here


	# Returns Brand Based on CustomerId 
	function getBrandRecords($customerId)
	{
		//$qry	=	"select id, brand from m_brand where customer_id='$customerId' order by brand asc";
		# Common Brand
		$qry1    = "select id, brand from m_brand where active=1 order by brand asc";		
		$result1	 = $this->databaseConnect->getRecords($qry1);
		# Customer Based Brand
		$qry2    = "select id, brand from m_customer_brand where customer_id='$customerId' and active=1 order by brand asc";		
		$result2  = $this->databaseConnect->getRecords($qry2);

		$resultArr = array(''=>'--Select--');
		while (list(,$v) = each($result1)) {
			$resultArr[$v[0].'_F'] = $v[1];
		}
		
		while (list(,$v1) = each($result2)) {
			$resultArr[$v1[0].'_C'] = $v1[1];
		}
		//ksort($resultArr);	
		return $resultArr;
	}


	#Grade selection for Raw Grades
 	function fetchSelectedGrade($processCodeId)
	{
 		$qry	= "select b.id, b.code from m_processcode2grade a, m_grade b where a.grade_id = b.id and a.processcode_id='$processCodeId' and a.unit_select='r' order by b.code asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
 	}


	#Grade selection for Frozen Grades
 	function getFrozenGradeRecs($codeId)
	{
 		$qry	= "select a.grade_id, b.code from m_processcode2grade a, m_grade b where a.grade_id = b.id and a.processcode_id='$codeId' and a.unit_select='f' order by b.code asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	#Grade selection for Secondary Frozen Grades
 	function getSecondaryFrozenGradeRecs($codeId)
	{
 		$qry	= "select a.secondary_grade, b.code from m_secondary_processcode a, m_grade b where a.secondary_grade = b.id and a.id='$codeId'   order by b.code asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}


	# Get Filled Wt of Frozen Packing
	function getFrznPkgFilledWt($frozenPackingId)
	{
		$qry	= "select filled_wt, decl_wt, unit from m_frozenpacking where id='$frozenPackingId'";
		$rec 	= $this->databaseConnect->getRecords($qry);
		return array($rec[0][0], $rec[0][1], $rec[0][2]);
	}

	# Get Num of MC Pack
	function numOfPacks($mcpackingId)
	{
		$qry	= "select number_packs from m_mcpacking where id='$mcpackingId'";
		$rec 	= $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}

	# Add Purchase Order
	function addPurchaseOrder($selCustomer, $dischargePort, $paymentTerms, $lastDate, $selectDate, $totalNumMC, $totalValUSD, $totalValINR, $selCountry, $selPort, $selAgent, $poNo, $poDate, $shipmentInstrs, $documentInstrs, $surveyInstrs, $commnPaymentInstrs, $varients, $selCarriageMode, $otherBuyer, $userId, $currencyId, $currencyRateListId, $selUnit,$company,$unit)
	{		
		$qry = "insert into t_purchaseorder_main (customer_id, discharge_port, payment_term, lastdate, select_date, total_num_mc, total_usd_amt, total_inr_amt, createdby, created_on, country_id, port_id, agent_id, po_no, po_date, shipment_instrs, document_instrs, survey_instrs, payment_instrs, varients, carriage_mode_id, other_buyer, currency_id, currency_ratelist_id, unit_id,company_id,unit) values('$selCustomer', '$dischargePort', '$paymentTerms', '$lastDate', '$selectDate', '$totalNumMC', '$totalValUSD', '$totalValINR', '$userId', NOW(), '$selCountry', '$selPort', '$selAgent', '$poNo', '$poDate', '$shipmentInstrs', '$documentInstrs', '$surveyInstrs', '$commnPaymentInstrs', '$varients', '$selCarriageMode', '$otherBuyer', '$currencyId', '$currencyRateListId', '$selUnit','$company','$unit')";

		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Add Purchase Order Entry Recs
	function addPurchaseOrderRawEntries($lastInsId, $selFish, $selProcessCode, $selEuCode, $selBrand, $selGrade, $selFreezingStage, $selFrozenCode, $selMCPacking, $numMC, $pricePerKg, $valueInUSD, $valueInINR, $brandFrom, $wtType,$processType)
	{
		/*$qry = "insert into t_purchaseorder_rm_entry (main_id, fish_id, processcode_id, eucode_id, brand_id, grade_id, freezingstage_id, frozencode_id, mcpacking_id, number_mc, priceperkg, value_usd, value_inr, brand_from, wt_type,created_on) values('$lastInsId', '$selFish', '$selProcessCode', '$selEuCode', '$selBrand', '$selGrade', '$selFreezingStage', '$selFrozenCode', '$selMCPacking', '$numMC', '$pricePerKg', '$valueInUSD', '$valueInINR', '$brandFrom', '$wtType',now())";*/
		$qry = "insert into t_purchaseorder_rm_entry (main_id,fish_id, processcode_id,brand_id, grade_id, freezingstage_id, frozencode_id, mcpacking_id, number_mc, priceperkg, value_usd, value_inr, brand_from, wt_type,created_on,process_type) values('$lastInsId', '$selFish', '$selProcessCode','$selBrand', '$selGrade', '$selFreezingStage', '$selFrozenCode', '$selMCPacking', '$numMC', '$pricePerKg', '$valueInUSD', '$valueInINR', '$brandFrom', '$wtType',now(),'$processType')";
		//echo $qry."<br>";	
		//
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Returns all Paging Records  
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{

		$whr = "a.select_date>='$fromDate' and a.select_date<='$tillDate'";
		
		$limit		= " $offset, $limit ";
	
		$orderBy	= " a.select_date desc ";
		
		$qry = " select  a.id, a.total_num_mc, a.total_usd_amt, a.total_inr_amt, a.lastdate, a.select_date, b.customer_name, a.qel_gen, a.qel_confirmed, a.complete, a.po_no, a.po_date, (select count(*) from t_invoice_main where po_id=a.id and confirmed!='Y') as notConfirmedCount, (select count(*) from t_invoice_main where po_id=a.id) as invoiceCount from t_purchaseorder_main a join m_customer b on  a.customer_id=b.id";

		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
		if ($limit!="")		$qry 	.= " limit ".$limit;	
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}


	#Get Records For Selected Date Range
	function fetchAllRecords($fromDate, $tillDate)
	{
		$whr = "a.select_date>='$fromDate' and a.select_date<='$tillDate'";
			
		$orderBy	= " a.select_date desc ";

		$qry = " select  a.id, a.total_num_mc, a.total_usd_amt, a.total_inr_amt, a.lastdate, a.select_date, b.customer_name, a.qel_gen, a.qel_confirmed, a.complete, a.po_no, a.po_date, (select count(*) from t_invoice_main where po_id=a.id and confirmed!='Y') as notConfirmedCount, (select count(*) from t_invoice_main where po_id=a.id) as invoiceCount from t_purchaseorder_main a join m_customer b on  a.customer_id=b.id";

		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}


	# Get Packing  based on id 
	function find($poMainId)
	{
		$qry	= "select id, customer_id, discharge_port, payment_term, lastdate, select_date, total_num_mc, total_usd_amt, total_inr_amt, country_id, port_id, agent_id, po_no, po_date, shipment_instrs, document_instrs, survey_instrs, payment_instrs, varients, carriage_mode_id, other_buyer, currency_id, currency_ratelist_id, unit_id,company_id,unit from t_purchaseorder_main where id=$poMainId";
	
	//echo "<br>$qry";
		return $this->databaseConnect->getRecord($qry);
	}

	#Fetch All Records based on SO Id 
	function fetchAllPOItem($poMainId)
	{
		/*$qry = "select id, fish_id, processcode_id, eucode_id, brand_id, grade_id, freezingstage_id, frozencode_id, mcpacking_id, number_mc, priceperkg, value_usd, value_inr, brand_from, wt_type from t_purchaseorder_rm_entry where main_id='$poMainId' ";*/

		$qry = "select id, fish_id, processcode_id,brand_id, grade_id, freezingstage_id, frozencode_id, mcpacking_id, number_mc, priceperkg, value_usd, value_inr, brand_from, wt_type,process_type from t_purchaseorder_rm_entry where main_id='$poMainId' ";
		//echo "--$qry--";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Update Purchase Order main Table
	function updatePurchaseOrderMain($poMainId, $selCustomer, $dischargePort, $paymentTerms, $lastDate, $selectDate, $totalNumMC, $totalValUSD, $totalValINR, $selCountry, $selPort, $selAgent, $poNo, $poDate, $shipmentInstrs, $documentInstrs, $surveyInstrs, $commnPaymentInstrs, $varients, $userId, $completedStatus, $selCarriageMode, $otherBuyer, $currencyId, $currencyRateListId, $selUnit,$company,$unit)
	{	
		$qry	= "update t_purchaseorder_main set customer_id='$selCustomer', discharge_port='$dischargePort', payment_term='$paymentTerms', lastdate='$lastDate', select_date='$selectDate', total_num_mc='$totalNumMC', total_usd_amt='$totalValUSD', total_inr_amt='$totalValINR', country_id='$selCountry', port_id='$selPort', agent_id='$selAgent', po_no='$poNo', po_date='$poDate', shipment_instrs='$shipmentInstrs', document_instrs='$documentInstrs', survey_instrs='$surveyInstrs', payment_instrs='$commnPaymentInstrs', varients='$varients', complete='$completedStatus', carriage_mode_id='$selCarriageMode', other_buyer='$otherBuyer', currency_id='$currencyId', currency_ratelist_id='$currencyRateListId', unit_id='$selUnit',company_id='$company',unit='$unit' where id='$poMainId'";	
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update Raw Entries
	function updatePORawEntries($poEntryId, $selFish, $selProcessCode, $selEuCode, $selBrand, $selGrade, $selFreezingStage, $selFrozenCode, $selMCPacking, $numMC, $pricePerKg, $valueInUSD, $valueInINR, $brandFrom,$wtType,$processType)
	{		
		/*$qry	= "update t_purchaseorder_rm_entry set fish_id='$selFish', processcode_id='$selProcessCode', eucode_id='$selEuCode', brand_id='$selBrand', grade_id='$selGrade', freezingstage_id='$selFreezingStage', frozencode_id='$selFrozenCode', mcpacking_id='$selMCPacking', number_mc='$numMC', priceperkg='$pricePerKg', value_usd='$valueInUSD', value_inr='$valueInINR', brand_from='$brandFrom' where id='$poEntryId'";*/

		$qry	= "update t_purchaseorder_rm_entry set fish_id='$selFish', processcode_id='$selProcessCode',brand_id='$selBrand', grade_id='$selGrade', freezingstage_id='$selFreezingStage', frozencode_id='$selFrozenCode', mcpacking_id='$selMCPacking', number_mc='$numMC', priceperkg='$pricePerKg', value_usd='$valueInUSD', value_inr='$valueInINR', brand_from='$brandFrom' ,wt_type='$wtType',process_type='$processType' where id='$poEntryId'";

		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;		
	}


	function deletePORawEntry($poEntryId)
	{
		$qry	=	" delete from t_purchaseorder_rm_entry where id=$poEntryId";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	# Delete PO RM table Entry
	function deleteRMEntry($mainId)
	{
		$qry	=	" delete from t_purchaseorder_rm_entry where main_id=$mainId";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete PO Main table Entry
	function deletePurchaseOrderMainRec($POMainId)
	{
		$qry	= " delete from t_purchaseorder_main where id=$POMainId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	# ----------------------------
	# Check SO Number Exist
	# ----------------------------
	# Check valid SO num
	function chkValidSONum($selDate, $invoiceNum, $invoiceType)
	{		

		$whr = " type='SPO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum' ";

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
		// t_purchaseorder_main
		// t_invoice_main	
		$soYear	 = date("Y", strtotime($selDate));
		
		$whr = "po_year='$soYear' ";

		if ($cSOId!="") $whr .= " and id!=$cSOId";		
		if ($invoiceType=='S') $whr .= " and sample_invoice_no='$soId' ";
		else $whr .= " and invoice_no='$soId' ";

		$qry = " select id from t_purchaseorder_main";
		if ($whr!="") $qry .= " where ".$whr;

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	
	#Checking the Selected Invoice is cancelled
	function checkCancelledInvoice($invNo, $selDate, $invoiceType)
	{
		$soYear	 = date("Y", strtotime($selDate));

		//$qry	= "select invoice_no from s_cancelled_invoice where invoice_no='$invNo' and inv_year='$soYear' and inv_type='$invoiceType' ";
		$qry = "";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecords($qry);
		//return (sizeof($rec)>0)?true:false;
		return false;
	}
	
	// Ends Here


	/*
	#Check Blank Record Exist
	function checkBlankRecord()
	{
		$qry = "select a.id, b.id, c.id from t_purchaseorder_main a left join t_purchaseorder_rm_entry b on a.id=b.main_id left join t_purchaseorder_grade_entry c on b.id=c.rmentry_id where (a.po_id is null  or a.po_id=0 ) order by a.id desc";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array( $result[0], $result[1], $result[2]):false;	
	}

	#Indert blank record
	function addTempDataMainTable()
	{
		$qry	= "insert into t_purchaseorder_main (select_date) values(Now())";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Insert blank rm Entry table record
	function addTempDataRMEntryTable($mainId)
	{
		$qry	= "insert into t_purchaseorder_rm_entry (main_id) values('$mainId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#Insert blank rm Entry table record
	function addTempDataGradeEntryTable($rmEntryId)
	{
		$qry	= "insert into t_purchaseorder_grade_entry (rmentry_id) values('$rmEntryId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	//------------------------------------------------------------------------------------

	function maxValuePO()
	{
		$qry	= "select max(po_id) from t_purchaseorder_main";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	

	#Update Purchase Order RM Entry Table
	function updatePurchaseOrderRMEntry($fishId, $processCode, $eUCode, $brand, $rmEntryId)
	{
		$qry	= "update t_purchaseorder_rm_entry set fish_id='$fishId', processcode_id='$processCode', eucode_id='$eUCode', brand_id='$brand' where id='$rmEntryId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Update Purchase Order Grade Entry Table
	function updatePurchaseOrderGradeEntry($selGrade, $freezingStage, $frozenCode, $mCPacking, $numMC, $pricePerKg, $valueInUSD, $valueInINR, $gradeEntryId )
	{
		$qry	= "update t_purchaseorder_grade_entry set grade_id='$selGrade', freezingstage_id='$freezingStage', frozencode_id='$frozenCode', mcpacking_id='$mCPacking', number_mc='$numMC', priceperkg='$pricePerKg', value_usd='$valueInUSD', value_inr='$valueInINR' where id='$gradeEntryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	

	# ---------- Delete Section  start ----------------------------

	# Delete PO Grade table Entry
	function deleteGradeEntry($POGradeEntryId)
	{
		$qry	=	" delete from t_purchaseorder_grade_entry where id=$POGradeEntryId";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check RM Entry Exist in PO Grade table 
	function checkRMRecordExist($PORMEntryId)
	{
		$qry	=	"select b.rmentry_id from t_purchaseorder_rm_entry a, t_purchaseorder_grade_entry b  where  a.id=b.rmentry_id and b.rmentry_id='$PORMEntryId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	# Check Main Entry Exist in RM Entry table 
	function checkRecordsExist($POMainId)
	{
		$qry	= "select b.main_id from t_purchaseorder_main a, t_purchaseorder_rm_entry b  where  a.id=b.main_id and b.main_id='$POMainId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# ---------- Delete Section  End ----------------------------


	# Update Daily Frozen Packing

	function updatePurchaseOrder($purchaseOrderId, $selCustomer, $fishId, $processCode, $selGrade, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $numMC, $pricePerKg, $valueInUSD, $valueInINR, $paymentTerms, $lastDate, $dateExtended, $currentUserId)
	{
		$extendedDate	= "";		
		if ($dateExtended=='E') {		
			$dateExtended	=	", extended='$dateExtended'";
			$createdBy		=	", createdby='$currentUserId'";	
		}
		
		$qry	=	" update t_purchaseorder set customer_id='$selCustomer', fish_id='$fishId', processcode_id='$processCode', grade_id='$selGrade', freezingstage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', frozencode_id='$frozenCode', mcpacking_id='$mCPacking', number_mc='$numMC', priceperkg='$pricePerKg', value_usd='$valueInUSD', value_inr='$valueInINR', payment_term='$paymentTerms', lastdate='$lastDate' $dateExtended $createdBy where id=$purchaseOrderId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	
	#Setting the Log status	
	function updateLogStatus($purchaseOrderId,$statusFlag, $shipmentDate)
	{
		$qry	= " update t_purchaseorder_main set logstatus='$statusFlag', logstatusdescr='$shipmentDate' where id=$purchaseOrderId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	*/

	# Returns all Not Completed Records
	function fetchNotCompleteRecords()
	{
		$qry	=	"select id, po_id from t_purchaseorder_main where complete <>  'C'  or   complete is null and po_id!=0";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#find Purchase Order Id using in Container
	function findPurchaseOrderNo($poId)
	{
		$qry = "select po_id from t_purchaseorder_main where id='$poId'";
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?$result[0]:false;
	}

	function getPortRecs($countryId)
	{
		$qry = "select id, port_name from m_country_port where country_id='$countryId' and active=1 ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);

		if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get Agent List
	function getAgentRecs($customerId)
	{
		$qry = " select ma.id, ma.name from m_agent_customer mac, m_agent ma where mac.agent_id=ma.id and mac.customer_id='$customerId' and ma.active=1 order by ma.name asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getCustomerRecs()
	{
		$qry	= "select a.id, a.customer_name from m_customer a where activeconfirm=1 order by a.customer_name asc";		
		$result	=	$this->databaseConnect->getRecords($qry);

		if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get payment Term Recs
	function getPaymentTermRecs($customerId)
	{
		$qry = " select ma.id, ma.mode from m_customer_payment_terms mac, m_paymentterms ma where mac.payment_term_id=ma.id and mac.customer_id='$customerId' and ma.active=1 order by ma.mode asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Check Packing Is Ready
	###commented on 20-11-2014
	/*# Check Frozen Packing MC Exist
	function chkFrznPkngMCExist($poId, $fishId, $processCodeId, $gradeId)
	{
		$qry	=	"select a.id, b.number_mc, b.number_loose_slab from t_dailyfrozenpacking_entry a, t_dailyfrozenpacking_grade b where a.id=b.entry_id and a.fish_id='$fishId' and  a.processcode_id='$processCodeId' and a.export_lot_id='$poId' and b.grade_id='$gradeId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach($result as $rec) {
				$numMc	= $rec[1];
				$numLooseSlab = $rec[2];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}
		}	
		return  $totalNumMC;
	}*/

# Check Frozen Packing MC Exist
	function chkFrznPkngMCExist($poEntryId)
	{
		$qry	=	"select id, number_mc, number_loose_slab from t_dailyfrozenpacking_allocate where po_rm_id='$poEntryId' union all select id, number_mc, number_loose_slab from t_dailyfrozenpacking_allocate_rmlotid where po_rm_id='$poEntryId'";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach($result as $rec)
			{
				$numMc	= $rec[1];
				$numLooseSlab = $rec[2];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}
		}	
		return  $totalNumMC;
	}




	// ---------------  invoice Number check ----------
	# Get Next Invoice Number	
	function getNextInvoiceNo()
	{	
		$selDate = date("Y-m-d");
		list($soNum, $invoiceDate) = $this->getMaxSONum();
		$validSONum = $this->getValidSONum($soNum, $invoiceDate);		
		if ($validSONum) return $soNum+1;
		else return $this->getCurrentSONum($selDate);
	}

	function getValidSONum($soNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SPO' and so_invoice_type='TA' and start_no<='$soNum' and end_no>='$soNum' ";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getMaxSONum()
	{
		//$qry = " select max(so), invoice_date from t_invoice_main where so!=0 group by id order by id desc, invoice_date desc";
		// t_purchaseorder_main
		// t_invoice_main
		$qry = " select max(invoice_no), invoice_date from t_invoice_main where invoice_no!=0 group by id order by id desc, invoice_date desc";
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

	function getCurrentSONum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SPO' and so_invoice_type='TA' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
	// ------------------------- invoice number check ends here ----------

	# Insert in Invoice Main table
	function insertSplitInvoiceRec($poMainId, $selCustomer, $spoInvoiceType, $spoSampleInvoiceNo, $spoProformaInvoiceNo, $spoInvoiceNo, $spoInvoiceDate, $spoEntryDate, $spoInvoiceTypeId, $userId,$eucode)
	{
		$qry = "insert into t_invoice_main (invoice_no, customer_id, invoice_date, invoice_type, proforma_no, sample_invoice_no, entry_date, po_id, created, createdby, invoice_type_id,eucode) values('$spoInvoiceNo', '$selCustomer', '$spoInvoiceDate', '$spoInvoiceType', '$spoProformaInvoiceNo', '$spoSampleInvoiceNo', '$spoEntryDate', '$poMainId', NOW(), '$userId', '$spoInvoiceTypeId','$eucode')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert in Invoice Entry table
	function insertSplitInvEntries($invoiceMainId, $sPOEntryId, $MCInPO, $splitNumMC, $pricePerKg, $valueInUSD, $valueInINR)
	{
		$qry = "insert into t_invoice_rm_entry (main_id, po_entry_id, mc_in_po, mc_in_invoice, price_per_kg, value_usd, value_inr) values('$invoiceMainId', '$sPOEntryId', '$MCInPO', '$splitNumMC', '$pricePerKg', '$valueInUSD', '$valueInINR')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# get Splitted invoice recs
	function getSplitInvoiceMainRecs($poMainId)
	{
		$qry	= "select id, invoice_no, invoice_date, invoice_type, proforma_no, sample_invoice_no, entry_date, invoice_type_id,eucode from t_invoice_main where po_id='$poMainId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# Get Invoice Entry Rec Details
	function getInvoiceRec($invoiceId, $poEntryId)
	{
		$qry	= "select id, mc_in_po, mc_in_invoice, price_per_kg, value_usd, value_inr from t_invoice_rm_entry where main_id='$invoiceId' and po_entry_id='$poEntryId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2], $result[0][3], $result[0][4], $result[0][5]):array();	
	}

	# Update in Invoice Main table
	function updateSplitInvoiceRec($selInvoiceId, $poMainId, $selCustomer, $spoInvoiceType, $spoSampleInvoiceNo, $spoProformaInvoiceNo, $spoInvoiceNo, $spoInvoiceDate, $spoEntryDate, $spoInvoiceTypeId, $userId,$eucode)
	{		
		$qry	= "update t_invoice_main set invoice_no='$spoInvoiceNo', customer_id='$selCustomer', invoice_date='$spoInvoiceDate', invoice_type='$spoInvoiceType', proforma_no='$spoProformaInvoiceNo', sample_invoice_no='$spoSampleInvoiceNo', entry_date='$spoEntryDate', po_id='$poMainId', modified=NOW(), modifiedby='$userId', invoice_type_id='$spoInvoiceTypeId',eucode='$eucode' where id='$selInvoiceId'";

		//echo "<br>$qry";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# update in Invoice Entry table
	function updateSplitInvEntries($invoiceEntryId, $MCInPO, $splitNumMC, $pricePerKg, $valueInUSD, $valueInINR)
	{
		$qry	= "update t_invoice_rm_entry set mc_in_po='$MCInPO', mc_in_invoice='$splitNumMC', price_per_kg='$pricePerKg', value_usd='$valueInUSD', value_inr='$valueInINR' where id='$invoiceEntryId'";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete All Split Invoice Recs
	function deleteSplitInvoiceRec($selInvoiceId)
	{
		$qry1	=	" delete from t_invoice_main where id='$selInvoiceId'";
		$qry2	=	" delete from t_invoice_rm_entry where main_id='$selInvoiceId'";	
		//echo $qry."<br>";
		$result1	= $this->databaseConnect->delRecord($qry1);
		$result2	= $this->databaseConnect->delRecord($qry2);

		if ($result1 && $result2) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		return $result;
	}

	# ----------------------------------
	# QEL Starts here 
	# ----------------------------------
	function genQEL($poMainId, $userId, $lastDate)
	{
		//$qry = " select a.customer_id, b.fish_id, b.processcode_id, b.eucode_id, b.brand_id, b.grade_id, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, b.number_mc, b.brand_from from t_purchaseorder_main a, t_purchaseorder_rm_entry b where a.id=b.main_id and b.main_id='$poMainId' group by b.eucode_id, b.brand_id, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, b.brand_from";
		/*$qry = "select a.customer_id, b.eucode_id, b.brand_id, b.brand_from, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, mc.customer_name, mfp.code as frznCode, mfs.rm_stage as frznStage, mmcp.code as mcPkgCode 
			from 
				t_purchaseorder_main a join t_purchaseorder_rm_entry b on a.id=b.main_id 
				left join m_customer mc on mc.id=a.customer_id 
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id 
				left join m_freezingstage mfs on mfs.id=b.freezingstage_id
				left join m_mcpacking mmcp on mmcp.id=b.mcpacking_id
			where  
			b.main_id='$poMainId' group by b.eucode_id, b.brand_id, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, b.brand_from";*/

			$qry = "select a.customer_id, tm.eucode, b.brand_id, b.brand_from, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, mc.customer_name, mfp.code as frznCode, mfs.rm_stage as frznStage, mmcp.code as mcPkgCode,company_id as company,unit as unit 
			from 
				t_purchaseorder_main a join t_purchaseorder_rm_entry b on a.id=b.main_id 
				left join m_customer mc on mc.id=a.customer_id 
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id 
				left join m_freezingstage mfs on mfs.id=b.freezingstage_id
				left join m_mcpacking mmcp on mmcp.id=b.mcpacking_id left join t_invoice_main tm on tm.po_id=b.main_id
			where  
			b.main_id='$poMainId' group by tm.eucode, b.brand_id, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, b.brand_from";


		
		$purchaseOrderRecs = $this->databaseConnect->getRecords($qry);
//echo "<br>$qry<br>";
		//$recArr = array();		
		$i = 0;
		$qelUpdated = false;
		foreach ($purchaseOrderRecs as $por) {
			$i++;
			$customerId 	= $por[0];			
			$eucodeId	= $por[1];			
			$brandId	= $por[2];
			$brandFrom	= $por[3];
			//$selBrand	= $brandId."_".$brandFrom;			
			$freezingStageId = $por[4];
			$frozenCodeId	= $por[5];			
			$mcPackingId	= $por[6];
			$custName	= $por[7];
			$frznCode	= $por[8];
			$frznStage	= $por[9];
			$mcPkgCode	= $por[10];
			$company	= $por[11];
			$unit	= $por[12];

			$selCustName = substr(str_replace (" ",'',$custName), 0,9);	
			//$qelName	= $frznCode."-".$frznStage."-".$mcPkgCode."-".$selCustName.$i.$poMainId;
			$qelName	= $selCustName."-".$frznCode."-".$frznStage."-".$mcPkgCode."-".$i.$poMainId."-".date("dMy");
			
			# Insert in Main table
			//echo "<br>$qelName, $customerId, $eucodeId, $brandId, $brandFrom, $freezingStageId, $frozenCodeId, $mcPackingId";
			$qelMainId = "";
			
			$insQELMainRec = $this->addFznPkngQuickEntryList($qelName, $freezingStageId, $selQuality, $eucodeId, $brandId, $customerId, $frozenCodeId, $mcPackingId, $frozenLotId, $exportLotId, $userId, $brandFrom, $lastDate,$company,$unit);
			if ($insQELMainRec) {
				$qelUpdated = true;
				$qelMainId = $this->databaseConnect->getLastInsertedId();	
				
				# PC Recs
				$pcRecs	= $this->getPOPCRecs($poMainId, $eucodeId, $brandId, $brandFrom, $freezingStageId, $frozenCodeId, $mcPackingId);
				foreach ($pcRecs as $pcr) {
					$fishId		= $pcr[0];
					$processCodeId	= $pcr[1];	
					# Insert in PC
					//echo "<br>PC=>$qelMainId, $fishId, $processCodeId";	
					$this->addFznPkgRawEntry($qelMainId, $fishId, $processCodeId);
				}
	
				# Grade Recs
				$gradeRecs = $this->getPOGradeRecs($poMainId, $eucodeId, $brandId, $brandFrom, $freezingStageId, $frozenCodeId, $mcPackingId);
				$g = 0;
				foreach ($gradeRecs as $gr) {
					$g++;
					$gradeId	= $gr[0];				
					# Insert in PC
					//echo "<br>G=>$qelMainId, $gradeId";	
					$this->addGradeRec($qelMainId, $gradeId, $g, $userId);
				}
			} // Insert Main Cond ends here			
		} // PO Recs Loop Ends here

		if ($qelUpdated) {
			# Update PO
			$updatePOMainRec = $this->updatePOQEL($poMainId);
		}
	}

	function getPOPCRecs($poMainId, $eucodeId, $brandId, $brandFrom, $freezingStageId, $frozenCodeId, $mcPackingId)
	{
		/*$qry = " select b.fish_id, b.processcode_id from t_purchaseorder_rm_entry b where b.main_id='$poMainId' and b.eucode_id='$eucodeId' and b.brand_id='$brandId' and b.freezingstage_id='$freezingStageId' and b.frozencode_id='$frozenCodeId' and b.mcpacking_id='$mcPackingId' and b.brand_from='$brandFrom' group by b.processcode_id";	*/

		$qry = " select b.fish_id, b.processcode_id from t_purchaseorder_rm_entry b where b.main_id='$poMainId' and b.brand_id='$brandId' and b.freezingstage_id='$freezingStageId' and b.frozencode_id='$frozenCodeId' and b.mcpacking_id='$mcPackingId' and b.brand_from='$brandFrom' group by b.processcode_id";

		//echo "<br>PC===$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getPOGradeRecs($poMainId, $eucodeId, $brandId, $brandFrom, $freezingStageId, $frozenCodeId, $mcPackingId)
	{
		/*$qry = " select b.grade_id from t_purchaseorder_rm_entry b where b.main_id='$poMainId' and b.eucode_id='$eucodeId' and b.brand_id='$brandId' and b.freezingstage_id='$freezingStageId' and b.frozencode_id='$frozenCodeId' and b.mcpacking_id='$mcPackingId' and b.brand_from='$brandFrom' group by b.grade_id";*/
		//$qry = "select b.grade_id from t_purchaseorder_rm_entry b left join t_invoice_main tm on tm.po_id=b.main_id left join m_eucode me on tm.eucode=me.id where b.main_id='$poMainId' and tm.eucode='$eucodeId' and b.brand_id='$brandId' and b.freezingstage_id='$freezingStageId' and b.frozencode_id='$frozenCodeId' and b.mcpacking_id='$mcPackingId' and b.brand_from='$brandFrom' group by b.grade_id";
		$qry = " select b.grade_id from t_purchaseorder_rm_entry b where b.main_id='$poMainId' and b.brand_id='$brandId' and b.freezingstage_id='$freezingStageId' and b.frozencode_id='$frozenCodeId' and b.mcpacking_id='$mcPackingId' and b.brand_from='$brandFrom' group by b.grade_id";
		

		//echo "<br>Grade===$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Insert QEL MainRec
	function addFznPkngQuickEntryList($qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $userId, $brandFrom, $shipmentLastDate,$company,$unit)
	{
		$qry	 = "insert into t_fznpakng_quick_entry (name, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, created, createdby, brand_from, expiry_date) values('$qeName', '$freezingStage', '$eUCode', '$brand', '$frozenCode', '$mCPacking', '$frozenLotId', '$exportLotId', '$selQuality', '$selCustomerId', NOW(), '$userId', '$brandFrom', '$shipmentLastDate')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert QEL PC
	function addFznPkgRawEntry($qelId, $selFish, $selProcessCode)
	{
		$qry	 = "insert into t_fznpakng_qel_entry (qe_entry_id, fish_id, processcode_id) values('$qelId', '$selFish', '$selProcessCode')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert QEL Grade 
	function addGradeRec($qelId, $gradeId, $g, $userId)
	{
		$qry	 = "insert into t_fznpakng_qel_grade (qe_entry_id, grade_id, display_order, created_by,active) values('$qelId','$gradeId', '$g', '$userId','Y')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	/* All
	$recArr = array();		
		foreach ($purchaseOrderRecs as $por) {
			$customerId 	= $por[0];
			$fishId		= $por[1];
			$processCodeId	= $por[2];
			$eucodeId	= $por[3];			
			$brandId	= $por[4];
			$brandFrom	= $por[10];
			$selBrand	= $brandId."_".$brandFrom;
			$gradeId	= $por[5];
			$freezingStageId = $por[6];
			$frozenCodeId	= $por[7];			
			$mcPackingId	= $por[8];
			$numMC		= $por[9];
			$groupRec	= $eucodeId.",".$selBrand.",".$freezingStageId.",".$frozenCodeId.",".$mcPackingId;		
			$recArr[$groupRec]	= array($customerId, $fishId, $processCodeId, $gradeId, $numMC);
		}
	*/
	# QEL Ends here
	function updatePOQEL($poMainId)
	{
		$qry	= "update t_purchaseorder_main set qel_gen='Y' where id='$poMainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Get All Products in PO
	function getProductsInPO($poMainId)
	{
		// Brand Need to check
		$qry = "select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tpore.frozencode_id, tpore.mcpacking_id, tpore.wt_type, tpore.grade_id,mg.id,if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.id),mfs.id
			from 
				t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id 
				left join m_processcode mpc on tpore.processcode_id=mpc.id 
				left join m_brand mb on tpore.brand_id=mb.id
				left join m_grade mg on tpore.grade_id=mg.id 
				left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id
				left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id 
				left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id left join t_invoice_main tm on tm.po_id=tpore.main_id left join m_eucode me on tm.eucode=me.id
			where main_id='$poMainId' ";
			/*$qry = "select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tpore.frozencode_id, tpore.mcpacking_id, tpore.wt_type, tpore.grade_id,mg.id,if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.id),mfs.id
			from 
				t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id 
				left join m_processcode mpc on tpore.processcode_id=mpc.id 
				left join m_brand mb on tpore.brand_id=mb.id
				left join m_grade mg on tpore.grade_id=mg.id 
				left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id
				left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id 
				left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id left join t_invoice_main tm on tm.po_id=tpore.main_id left join m_eucode me on tm.eucode=me.id
			where main_id='$poMainId' ";*/


		
			
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function availableQntyForGrade($gradeId,$poId,$processId)
	{
		$qry="select numMC -sum(sumAll) from (select tpore.number_mc as numMC , SUM(COALESCE(tdpa.number_mc, 0))  as sumAll from t_purchaseorder_rm_entry tpore  left join t_dailyfrozenpacking_allocate tdpa on tpore.id=tdpa.po_rm_id where tpore.grade_id ='$gradeId' and tpore.processcode_id='$processId' and tpore.main_id in ($poId) group by tpore.grade_id union all select  tpore.number_mc as numMC , SUM(COALESCE(tdpa.number_mc, 0))  as sumAll from t_purchaseorder_rm_entry tpore  left join t_dailyfrozenpacking_allocate_rmlotid tdpa on tpore.id=tdpa.po_rm_id where tpore.grade_id ='$gradeId' and  tpore.processcode_id='$processId' and tpore.main_id in ($poId) group by tpore.grade_id) dum";
		//$qry="select numMC -sum(sumAll) from (select tpore.number_mc as numMC , SUM(COALESCE(tdpa.number_mc, 0))  as sumAll from t_purchaseorder_rm_entry tpore  left join t_dailyfrozenpacking_allocate tdpa on tpore.id=tdpa.po_rm_id where tpore.grade_id ='$gradeId' and tpore.main_id in ($poId) group by tpore.grade_id union all select  tpore.number_mc as numMC , SUM(COALESCE(tdpa.number_mc, 0))  as sumAll from t_purchaseorder_rm_entry tpore  left join t_dailyfrozenpacking_allocate_rmlotid tdpa on tpore.id=tdpa.po_rm_id where tpore.grade_id ='$gradeId' and tpore.main_id in ($poId) group by tpore.grade_id) dum";
		//$qry="select numMC -sum(sumAll) from (select tpore.number_mc as numMC , SUM(COALESCE(tdpa.number_mc, 0))  as sumAll from t_purchaseorder_rm_entry tpore  left join t_dailyfrozenpacking_allocate tdpa on tpore.id=tdpa.po_rm_id where tpore.grade_id ='$gradeId' and tpore.main_id in ($poId) group by tpore.grade_id union all select  tpore.number_mc as numMC , SUM(COALESCE(tdpa.number_mc, 0))  as sumAll from t_purchaseorder_rm_entry tpore  left join t_dailyfrozenpacking_allocate_rmlotid tdpa on tpore.id=tdpa.po_rm_id where tpore.grade_id ='$gradeId' and tpore.main_id in ($poId) group by tpore.grade_id) dum";

		//$qry="select sum(tpore.number_mc) from t_purchaseorder_rm_entry tpore where grade_id ='$gradeId' and main_id in ($poId) group by grade_id";
	/*	$qry="select sum(tpore.number_mc)-sum(tdpa.number_mc) from t_purchaseorder_rm_entry tpore  left join t_dailyfrozenpacking_allocate tdpa on tpore.id=tdpa.po_rm_id where tpore.grade_id ='$gradeId' and tpore.main_id in ($poId) group by tpore.grade_id";*/
	
	
	//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getInvoiceType($invoiceTypeId)
	{
		$qry = " select id, tax, sample from m_invoice_type where id='$invoiceTypeId' ";
		$rec	= $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$tax 	= $rec[1];
			$sample = $rec[2];
			if ($sample=='Y') return 'S';
			else if ($tax=='Y') return 'T';
			else return 'E'; // Export
		}
	}

	function getFrznPkgCodeRecs()
	{
		$qry	= "select id, code from m_frozenpacking order by code asc";

		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getMCPkgRecs()
	{
		$qry	= "select id, code from m_mcpacking order by number_packs asc";

		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Insert Chk List Recs
	function insertDocInstuctionsChkList($purchaseOrderId, $chkListId)
	{
		$qry = "insert into t_purchaseorder_doc_chklist (main_id, chk_list_id) values ('$purchaseOrderId', '$chkListId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# delete Chk list (USING SALES ORDER)
	function delChkList($purchaseOrderId)
	{
		$qry	= " delete from t_purchaseorder_doc_chklist where main_id='$purchaseOrderId'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# get sel Chk list
	function getSelChkListRecs($purchaseOrderId)
	{
		$qry = "select id, chk_list_id from t_purchaseorder_doc_chklist where main_id='$purchaseOrderId' ";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array();
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
			}
		}
		return $resultArr;
	}

	/**
	* Check PO Number Exist
	*/
	function checkPONumberExist($poNo, $editMainId)
	{

		$qry = " select id from t_purchaseorder_main where po_no='$poNo'";
		if ($editMainId!="") $qry .= " and id!=$editMainId";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	/**
	* Quick Entry Wise Frozencode
	*/
	function qelFrzncode($fishId, $processCodeId)
	{
		$qry = "select fqem.frozencode_id, mfp.code from t_fznpakng_quick_entry fqem join t_fznpakng_qel_entry fpqe on fqem.id=fpqe.qe_entry_id 
			left join m_frozenpacking mfp on mfp.id=fqem.frozencode_id where fpqe.fish_id='$fishId' and fpqe.processcode_id='$processCodeId'
			group by fqem.frozencode_id order by mfp.code asc
			";

		//echo "Qry1==><br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array();
		$validArr = array();
		$frznIds = "";
		$resultArr['FS1'] = "--Select--";
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
				$validArr[] = $rec[0];
			}

			$frznIds = implode(',',$validArr);
		}

		if ($frznIds!="") $resultArr['FS2'] = "--Other--";

		# Get All Recs
		$qryAll = "select id, code from m_frozenpacking";
		if ($frznIds!="") $qryAll .= " where id not in ($frznIds)";
		$qryAll .= " order by code asc";
		//echo "Qry2==><br>$qryAll<br>";
		$resultAll	= $this->databaseConnect->getRecords($qryAll);

		if (sizeof($resultAll)>0) {
			foreach ($resultAll as $ra) {
				$resultArr[$ra[0]] = $ra[1];
			}
		}
		
		//$qry
		return $resultArr;
	}


	/**
	* QEL base MC packing
	*/
	function qelMCPkg($frozencodeId)
	{
		$qry = "select fqem.mcpacking_id, mcp.code from t_fznpakng_quick_entry fqem left join m_mcpacking mcp on mcp.id=fqem.mcpacking_id where fqem.frozencode_id='$frozencodeId' and fqem.mcpacking_id!=0 group by fqem.mcpacking_id order by mcp.number_packs asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array();
		if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		if (!sizeof($result)) {
			# Get All Recs
			$qryAll = "select id, code from m_mcpacking order by number_packs asc";			
			//echo "Qry2==><br>$qryAll<br>";
			$resultAll = $this->databaseConnect->getRecords($qryAll);	
			if (sizeof($resultAll)>0) {
				foreach ($resultAll as $ra) {
					$resultArr[$ra[0]] = $ra[1];
				}
			}
		}

		return $resultArr;
	}


	# Using in Daily frozen packing
	# Get Purchase orders for packing section

	function getAailableQty()
	{
	$qry="select  sum(tdfpa.number_mc) rr1,tdfpa.grade_id,sum(distinct tpore.number_mc) rr,sum(tdfpa.number_mc)-sum(distinct tpore.number_mc) 
from t_purchaseorder_rm_entry tpore join  t_dailyfrozenpacking_po tdpo on tpore.main_id=tdpo.po_id join t_dailyfrozenpacking_allocate tdfpa on tdpo.id=tdfpa.po_entry_id and tpore.grade_id=tdfpa.grade_id where tpore.main_id=204";

	}

	function updateAllocatedStatus1($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId)
	{
		// Original
		
		$qry =	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) and pore.processcode_id='$processCodeId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' group by pom.id order by pom.po_date asc";		
		$result	=	$this->databaseConnect->getRecords($qry);
		foreach($result as $por) {
							$poId	= $por[0];
							/*$avQry="select  sum(tdfpa.number_mc) rr1,tdfpa.grade_id,sum(distinct tpore.number_mc) rr,sum(tdfpa.number_mc)-sum(distinct tpore.number_mc),tpore.id 
from t_purchaseorder_rm_entry tpore 
join  t_dailyfrozenpacking_po tdpo on tpore.main_id=tdpo.po_id
left join t_dailyfrozenpacking_allocate tdfpa on tdpo.id=tdfpa.po_entry_id and tpore.grade_id=tdfpa.grade_id
where tpore.main_id=$poId";*/
$avQry="select  sum(tdfpa.number_mc) rr1,tdfpa.grade_id,sum(distinct tpore.number_mc) rr,sum(tdfpa.number_mc)-sum(distinct tpore.number_mc),tpore.id 
from t_purchaseorder_rm_entry tpore 
join  t_dailyfrozenpacking_po tdpo on tpore.main_id=tdpo.po_id
left join t_dailyfrozenpacking_allocate tdfpa on tdpo.id=tdfpa.po_entry_id and tpore.grade_id=tdfpa.grade_id where tpore.main_id=$poId";
//echo $avQry;
					$rec	= $this->databaseConnect->getRecord($avQry);
					if (sizeof($rec)>0) {
					$netSum 	= $rec[3];
					if ($netSum!="")
					{
					if (($netSum==0) )
					{
					$qryUpdeliv	= "update t_purchaseorder_main set allocated_status=1 where id='$poId'";					
					$resultUpdeliv	= $this->databaseConnect->updateRecord($qryUpdeliv);
					if ($resultUpdeliv) $this->databaseConnect->commit();
					else $this->databaseConnect->rollback();		
					}
					}}
					}}


	function updateAllocatedStatus($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId)
	{
		// Original
		$qry =	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) and pore.processcode_id='$processCodeId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' and pore.delivered_status!=1 group by pom.id order by pom.po_date asc";	
		//echo "updateAllocatedStatus=====".$qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		//print_r($result);
		
		foreach($result as $por) {
			$poId	= $por[0];
			//echo $poId;
			$avQry="select  sum(tdfpa.number_mc) rr1,tpore.grade_id,sum(distinct tpore.number_mc) rr,sum(distinct tpore.number_mc)-sum(tdfpa.number_mc),tpore.id,tpore.main_id from t_purchaseorder_rm_entry tpore join  t_dailyfrozenpacking_po tdpo on tpore.main_id=tdpo.po_id left join t_dailyfrozenpacking_allocate tdfpa on tdpo.id=tdfpa.po_entry_id and tpore.grade_id=tdfpa.grade_id where tpore.main_id=$poId and tpore.delivered_status=0 group by tpore.id";
			
			$resultAv	=	$this->databaseConnect->getRecords($avQry);
			//print_r($resultAv);
			$netresultAv[]=$resultAv;
		}
		
		$j=0;
		if (sizeof($netresultAv)>0) {
			foreach($netresultAv as $row)
			{
			foreach($row as $av) {
				
				$poIddel=$av[5];
				$netSum =$av[3];
				$id=$av[4];
				
				$i=1;		
				$flag=0;
				
				if ($netSum!="")
				{
						
						if ($netSum==0)
						{				
								
								$qryUpdeliv	= "update t_purchaseorder_rm_entry set delivered_status=1 where (id='$id' and main_id='$poIddel')";
								
								$resultUpdeliv	= $this->databaseConnect->updateRecord($qryUpdeliv);
								
								
								$flag=1;
								
								$i++;
						}
				}
				
				$j++;

			}
			}
		}



	}





	function getPendingOrders1($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId)
	{
		// Original
		
		/*$qry =	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) and pore.processcode_id='$processCodeId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' group by pom.id order by pom.po_date asc";*/
		//new code

		/*$qry =	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) and pore.processcode_id='$processCodeId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' and pore.delivered_status!=1  group by pom.id order by pom.po_date asc";*/
		$qry =	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) and pore.processcode_id='$processCodeId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' and pom.allocated_status!=1  group by pom.id order by pom.po_date asc";
		//echo $qry;
		/*
		$qry	=	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) group by pom.id order by pom.po_date asc";
		*/
		
		$result	=	$this->databaseConnect->getRecords($qry);
		//print_r($result);
		return $result;
	}



	function getPendingOrders($processCodeId,$freezingStageId,$frozenCodeId,$MCPkgId,$companyIds,$unitIds)
	{
		// Original
		/*$qry =	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) and pore.processcode_id='$processCodeId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' group by pom.id order by pom.po_date asc";*/
		//new code
		
		$qry =	"select pom.id, pom.po_no, pom.po_date, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PODate from t_purchaseorder_main pom JOIN t_purchaseorder_rm_entry pore ON pom.id=pore.main_id where (pom.complete <>'C' or pom.complete is null) and pore.processcode_id='$processCodeId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' and company_id='$companyIds' and unit='$unitIds' and pore.delivered_status is null  group by pom.id order by pom.po_date asc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllocatedMcno($selPOId,$poEntryId,$selGrade)
	{
	 $qry="select sum(tdfpa.number_mc) rr1,sum(distinct tpore.number_mc)-sum(tdfpa.number_mc) 
	from t_purchaseorder_rm_entry tpore 
	left join  t_dailyfrozenpacking_allocate tdfpa on tpore.id=tdfpa.po_rm_id where tpore.main_id=$selPOId and tdfpa.po_rm_id=$poEntryId";

	/* $qry="select sum(tdfpa.number_mc) rr1,sum(distinct tpore.number_mc)-sum(tdfpa.number_mc) 
	from t_purchaseorder_rm_entry tpore 
	left join  t_dailyfrozenpacking_allocate tdfpa on tpore.id=tdfpa.po_rm_id where tpore.main_id=$selPOId and tdfpa.po_rm_id=$poEntryId";*/

	//echo $qry;
	$rec = $this->databaseConnect->getRecord($qry);
	return array($rec[0],$rec[1]);

	}
	#Check Packing Is Ready -- Used in Order Processing
	function checkFrozenPackingReady($poEntyId)
	{
		$qry	=	"select id, number_mc, number_loose_slab from t_dailyfrozenpacking_allocate where po_rm_id='$poEntyId' union all select id, number_mc, number_loose_slab from t_dailyfrozenpacking_allocate_rmlotid where po_rm_id='$poEntyId'";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach($result as $rec)
			{
				$numMc	= $rec[1];
				$numLooseSlab = $rec[2];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}
		}	
		return  $totalNumMC;
	}

	function updateDeliveredStatus($netSum,$poEntryId)
	{


					if ($netSum!="")
					{
					if (($netSum==0))
					{
					$qryUpdeliv	= "update t_purchaseorder_rm_entry set delivered_status=1 where id='$poEntryId'";		
					//echo $qryUpdeliv;
					$resultUpdeliv	= $this->databaseConnect->updateRecord($qryUpdeliv);
					if ($resultUpdeliv) $this->databaseConnect->commit();
					else $this->databaseConnect->rollback();		
					}
					}
		
	}

	function getAllCompany()
	{
		$qry = "SELECT id,name,address,place,pin,country FROM m_billing_company WHERE default_row = 'Y' ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	function getAllCompanyContact($id)
	{
		$qry = "SELECT id,telephone_no,mobile_no,fax,email FROM m_billing_company_contact_detail WHERE default_CD = 'Y' and main_id='$id' ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	###get purchase order entry exist in frozen allocation
	function checkPurchaseOrderAllocation($id)
	{
		$qry = "SELECT tdar.id,'1' as lotstatus from t_purchaseorder_rm_entry tpore left join t_dailyfrozenpacking_allocate_rmlotid  tdar on tpore.id=tdar.po_rm_id where tpore.main_id='$id' union all SELECT tdar.id,'0' as lotstatus from t_purchaseorder_rm_entry tpore left join t_dailyfrozenpacking_allocate  tdar on tpore.id=tdar.po_rm_id where tpore.main_id='$id'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
		//return (sizeof($result)>0)?false:true;
	}


	function deleteRecordAllocate($allocateId)
	{
		$qry	= " delete from t_dailyfrozenpacking_allocate where id='$allocateId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function deleteRecordAllocateRmLotId($allocateId)
	{
		 
		$qry	= " delete from t_dailyfrozenpacking_allocate_rmlotid where id='$allocateId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	function getPoEntryIdRMLot($id)
	{
		$qry = "SELECT id from t_dailyfrozenpacking_po_rmlotid where po_id='$id'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		if(sizeof($result)>0)
		{
			$poEntryId=$result[0];
			$qry1	= " delete from t_dailyfrozenpacking_allocated_entry_rmlotid where po_entry_id='$poEntryId'";
			$result1	= $this->databaseConnect->delRecord($qry1);
			$qry2	= " delete from t_dailyfrozenpacking_po_rmlotid where id='$poEntryId'";
			$result2	= $this->databaseConnect->delRecord($qry);
			return $result2;
		}
		else
		{
			return $result;
		}
		//return $result;
	}

	function getPoEntryId($id)
	{
		$qry = "SELECT id from t_dailyfrozenpacking_po where po_id='$id'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		if(sizeof($result)>0)
		{
			$poEntryId=$result[0];
			$qry1	= " delete from t_dailyfrozenpacking_allocated_entry where po_entry_id='$poEntryId'";
			$result1	= $this->databaseConnect->delRecord($qry1);
			$qry2	= " delete from t_dailyfrozenpacking_po where id='$poEntryId'";
			$result2	= $this->databaseConnect->delRecord($qry);
			return $result2;
		}
		else
		{
			return $result;
		}
		//return $result;
	}

	
###---------------------------------- CODE FOR GENERATE	PO ID STARTS----------------------------------------------------------
	function chkValidGatePassId($selDate,$compId,$invUnit)
	{
		$qry	="select id,start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='PR' and billing_company_id='$compId' and unitid='$invUnit' or date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='PR' and billing_company_id='$compId' and unitid='0'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}

	function getAlphaCode($id)
	{
		$qry = "select alpha_code from number_gen where type='PR' and id='$id'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}

	function checkGatePassDisplayExist($compId,$invUnit)
	{
		$qry = "select (count(*)) from t_purchaseorder_main where company_id='$compId' and unit='$invUnit'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getmaxGatePassId($compId,$invUnit)
	{
		$qry = "select po_no from  t_purchaseorder_main where company_id='$compId' and unit='$invUnit' order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getValidendnoGatePassId($selDate,$compId,$invUnit)
	{
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='PR' and billing_company_id='$compId' and unitid='$invUnit' OR date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='PR' and billing_company_id='$compId' and unitid='0' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getValidGatePassId($selDate,$compId,$invUnit)
	{
		$qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='PR' and billing_company_id='$compId' and unitid='$invUnit' OR date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='PR' and billing_company_id='$compId' and unitid='0'";
		//echo $qry;
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
###----------------------------------CODE FOR GENERATE	PO ID ENDS----------------------------------------------------------

	# Filter m_secondary_processcode table using fish id (Used for xajax function call)
	function getSecondaryProcessCodeRecs($fishId)
	{
		$qry = "select id,name from m_secondary_processcode where active='1' order by name";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry ;
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}	

	function getFishRecs()
	{
		$qry	= "select mf.id, name, code,category_id,mf.active from m_fish mf join m_fishcategory  mc on mf.category_id=mc.id where mc.active=1 and mf.active=1 order by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getAssorted()
	{
		$qry	= "select id,name from m_fish where name='Assorted'";
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

}
	

	
?>