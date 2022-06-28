<?php
class Home
{  
	/****************************************************************
	This class deals with all the operations relating to Dash Board
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function Home(&$databaseConnect)
        {
        	$this->databaseConnect =&$databaseConnect;
	}


	#GetRecords based on date (Return $rmQty, $adjustQty, $localQty, $wastageQty, $softQty, $gradeCountAdj)
	function getRMQty($selDate)
	{
		$qry	= "select a.id, a.select_date, sum(b.effective_wt), sum(b.adjust+b.local_quantity+b.wastage+b.soft), sum(b.adjust), sum(b.local_quantity), sum(b.wastage), sum(b.soft), sum(b.grade_count_adj) from t_dailycatch_main a, t_dailycatchentry b where  a.id=b.main_id and a.select_date='$selDate' and a.flag=1 group by a.select_date order by a.select_date desc";
		//echo "<br>$qry<br>";
		$rec	 = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[2],$rec[4],$rec[5],$rec[6],$rec[7],$rec[8]):array();		
	}

	# ------------- Missing Challan Numbers Starts Here --------------------
	# Getting Prev Date Last Challan No and the current date Last challan Number : From this find the Missing Numbers
	# Get last Challan Number
	function getLastChallanNumber($selDate, $billingCompanyId)
	{
		$qry	= " select weighment_challan_no from t_dailycatch_main where select_date='$selDate' and flag=1 and billing_company_id='$billingCompanyId' order by weighment_challan_no desc ";
		//echo "<br>".$qry."=$selDate"."<br>";
		$rec	 = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get last Challan Number
	function getPrevLastChallanNumber($selDate, $billingCompanyId, $currentDate)
	{
		$qry	= " select weighment_challan_no, select_date from t_dailycatch_main where select_date='$selDate' and flag=1 and billing_company_id='$billingCompanyId' order by weighment_challan_no desc ";
		//echo $qry."<br>";
		$qry1	= " select weighment_challan_no, select_date from t_dailycatch_main where select_date<='$selDate' and flag=1 and billing_company_id='$billingCompanyId' order by select_date desc, weighment_challan_no desc ";
	

		$rec	 = $this->databaseConnect->getRecord($qry);
		$selRec = "";	
		$selectedDate = "";
		if (sizeof($rec)>0) {
			$selRec 	= $rec[0];
			$selectedDate 	= $rec[1];
		} else { # If No Rec for Prev Date
			$pRec	 = $this->databaseConnect->getRecord($qry1);
			$selRec = $pRec[0];
			$selectedDate 	= $pRec[1];
		}
		# Check Challan Num Valid
		if ($selRec) {
			list($startingNum, $endingNum) = $this->getValidChallanNum($currentDate, $billingCompanyId, $selRec);
		
			if ($startingNum=="") $selRec = "";
		}
		//echo "$startingNum, $endingNum, $selectedDate, $selDate, Rec=$selRec";
		return ($selRec!="" && $selectedDate!="")?array($selRec,$selectedDate):array('',$selDate);
	}

	function getContinuousChallanNo($minChallanNo, $maxChallanNo)
	{
		$challanNo = array();
		$k=0;
		for ($i=$minChallanNo; $i<=$maxChallanNo; $i++) {
			$challanNo[$k] = $minChallanNo++;
			$k++;
		}		
		return $challanNo;
	}

	function getExistingChallanNo($selDate, $billingCompanyId, $startNum, $endNum)
	{
		//select_date<='$selDate' and
		$qry	= "select weighment_challan_no from t_dailycatch_main where flag=1 and billing_company_id='$billingCompanyId' and weighment_challan_no>='$startNum' and weighment_challan_no<='$endNum'  order by select_date asc";
		//echo $qry."<br>";		
		$result		= $this->databaseConnect->getRecords($qry);		
		$challan 	= array();
		$i = 0;
		foreach ($result as $r) {
			$challan[$i] = $r[0];
			$i++;
		}
		return $challan;
	}

	#GetRecords based on date and supplier
	function getMissingRecords($minChallanNo, $maxChallanNo, $selDate, $billingCompanyId)
	{		
		// Get challan number range from manage challan
		list($startNum, $endNum) = $this->getChallanRange($selDate, $billingCompanyId);
		$continuousChallanNos = $this->getContinuousChallanNo($minChallanNo, $maxChallanNo);
		$existingChallanNos   = $this->getExistingChallanNo($selDate, $billingCompanyId, $startNum, $endNum);		
		$cancelledChallanNos  = $this->cancelledChallanNos($billingCompanyId);
		return $arr = array_diff($continuousChallanNos, $existingChallanNos, $cancelledChallanNos);	 
	}

	function getChallanRange($selDate, $billingCompanyId)
	{
		$qry	= "select start_no, end_no from number_gen where (('$selDate'>=start_date && (end_date is null || end_date=0)) or ('$selDate'>=start_date and '$selDate'<=end_date) or (start_date is null and end_date is null) ) and type='RM' and billing_company_id='$billingCompanyId' order by start_date asc";
		//echo "<br>$qry<br>";		
		$result		= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?array($result[0],$result[1]):array();
	}

	#Get cancelled challan
	function cancelledChallanNos($billingCompanyId)
	{
		$qry	= "select challan_no from s_cancelled_challan where billing_company_id='$billingCompanyId'";
		//echo $qry."<br>";		
		$result		= $this->databaseConnect->getRecords($qry);		
		$challan 	= array();
		$i = 0;
		foreach ($result as $r) {
			$challan[$i] = $r[0];
			$i++;
		}
		return $challan;
	}
	
	# Get Billing Company Wise Recs
	function getBillingCompanyWiseRecs($selDate)
	{
		$qry	= "select id, weighment_challan_no, billing_company_id, alpha_code from t_dailycatch_main where select_date='$selDate' and flag=1 group by billing_company_id order by weighment_challan_no desc";
		//echo "<br>$qry<br>";		

		return $this->databaseConnect->getRecords($qry);	
	}

	# Get Rate List based on Date
	function getValidChallanNum($selDate, $billingCompany, $challanNum)
	{	
		$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$challanNum' and end_no>='$challanNum'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array();
	}

	# ------------- Missing Challan Numbers Ends Here --------------------
	
	# Processed Qty Display ----
	function getTotalPreProcessedQty($selDate)
	{
		###commented on 09-10-2014
		/*$whr		= " a.id = b.dailypreprocess_main_id and a.date='".$selDate."' ";		
		$groupBy	= " a.date ";
		$qry		= " select sum(b.total_preprocess_qty) from t_dailypreprocess a, t_dailypreprocess_entries b ";		
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($groupBy!="")	$qry 	.= " group by ".$groupBy; */					
		//echo "<br>$qry<br>";		
		$qry="select sum(totalpreprocess) from (select b.total_preprocess_qty as totalpreprocess,a.date as date from t_dailypreprocess a left join  t_dailypreprocess_entries b on  a.id = b.dailypreprocess_main_id where a.date='".$selDate."'  union  select b.total_preprocess_qty as totalpreprocess ,a.date as date from t_dailypreprocess_rmlotid a left join  t_dailypreprocess_entries_rmlotid b on  a.id = b.dailypreprocess_main_id where a.date='".$selDate."') dum  group by date
		";
		
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Sales Order Records
	# ---------------------Starts Here---------
	
	# Get Confirmed Sales Order Recs
	function getConfirmedSalesOrderRecs($selDate)
	{
		$whr		= " a.invoice_date='".$selDate."' and a.complete_status='C' ";		
		$orderBy	= " a.so asc ";
		$qry		= " select so from t_salesorder a ";		
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry 	.= " order by ".$orderBy; 					
		//echo "<br>$qry";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Confirmed Sales Order Recs
	function getPendingSalesOrderRecs($selDate)
	{
		$whr		= " a.invoice_date='".$selDate."' and (a.complete_status<>'C' or a.complete_status is null) ";		
		$orderBy	= " a.so asc ";
		$qry		= " select so from t_salesorder a ";		
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry 	.= " order by ".$orderBy; 					
		//echo "<br>$qry";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Billed Amt
	function getSOBilledAmt($month, $statusType, $year)
	{
		//$qry = " select SUM(grand_total_amt+round_value) from t_salesorder where EXTRACT(MONTH FROM invoice_date)='$month' and complete_status='C' group by EXTRACT(YEAR_MONTH FROM invoice_date)";

		$whr = " EXTRACT(MONTH FROM invoice_date)='$month' and EXTRACT(YEAR FROM invoice_date)='$year' ";

		if ($statusType=='C') 		$whr .= " and complete_status='C'";
		else if ($statusType=='P')	$whr .= " and (complete_status<>'C' or complete_status is null)";

		$groupBy	=  " EXTRACT(YEAR_MONTH FROM invoice_date) ";

		$qry = " select SUM(grand_total_amt+round_value) from t_salesorder ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="")	$qry .= " group by ".$groupBy;
		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}
	
	# Get Confirmed Sales Order Recs
	function getSOBasedOnDespatchDate($selDate)
	{		
		$whr		= " a.last_date='".$selDate."'";				
		$orderBy	= " a.so asc ";
		$qry		= " select so, invoice_type, proforma_no, sample_invoice_no from t_salesorder a ";		
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry 	.= " order by ".$orderBy; 					
		//echo "<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSOBasedOnDeliveryDate($selDate)
	{
		//$whr		= " a.delivery_date='".$selDate."' and complete_status='C'";
		$whr		= " a.delivery_date='".$selDate."'";				
		$orderBy	= " a.so asc ";
		$qry		= " select so, invoice_type, proforma_no, sample_invoice_no from t_salesorder a ";		
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry 	.= " order by ".$orderBy; 					
		//echo "<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# ---------------------Ends Here---------

	# Frozen Packing Qty Starts here (Production Qty)
	# packing Qty ---------------------- STARTS HERE-----------------------------
	function getFrznPkgQty($selDate)
	{
		# Daily Frzn Packing records
		$dailyFrznPkgRecs = $this->dailyFrozenPackingRecs($selDate);
		$totalGrossWt = 0;		
		foreach ($dailyFrznPkgRecs as $dfpr) {
			$numMc		= $dfpr[8];
			$numLoosePack 	= $dfpr[9];
			$declaredFilledWt = $dfpr[10]; // Filled Wt
			$actualFilledWt = $dfpr[13];
			$frznCodeFilledWt = ($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt;
			//echo "<br>($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt";			
			$numMCPack	= $dfpr[11];
			
			$mcActualWt = ($frznCodeFilledWt*$numMCPack*$numMc);			
			$lcActualWt = $numLoosePack*$frznCodeFilledWt;
			$grossWt = $mcActualWt+$lcActualWt; // calculating pkg available qty
			$totalGrossWt += $grossWt;
			//echo "Out=$frznCodeFilledWt*$numMCPack*$numMc=$mcActualWt::LP=>::$numLoosePack*$frznCodeFilledWt=$lcActualWt, Gross=$grossWt<br>";
		}	
		//echo "<b>$totalGrossWt</b><br>";
		return ($totalGrossWt!=0)?(number_format($totalGrossWt,2,'.','')):"";
	}	
		
	function dailyFrozenPackingRecs($selDate)
	{
		$qry = " select 
				a.id, a.select_date, a.unit, b.freezing_stage_id, b.eucode_id, b.brand_id, b.frozencode_id, b.mcpacking_id, sum(c.number_mc), sum(c.number_loose_slab), mf.filled_wt, mcp.number_packs, mf.code as frznCode, mf.actual_filled_wt
			from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade c on b.id=c.entry_id
				left join m_frozenpacking mf on b.frozencode_id=mf.id left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			where 
				(a.select_date>='$selDate' and a.select_date<='$selDate') group by b.frozencode_id
			";
		//echo "<br>$qry<br><br>"; 
		return $this->databaseConnect->getRecords($qry);
	}
	# Frozen Packing Qty Ends here ---------------------------------------------------

	# ---------- Distributor Account ---------
	# Display which cheques are to be deposited in the month. On click, it should show the details of the cheque and distributor name. Once the cheque value date is entered, it should be removed from the list on dashboard.
	function getDepositedChqRecs($fYearMonth, $tYearMonth)
	{
		$whr = " a.distributor_id=b.id and (a.value_date is null or a.value_date='0000-00-00') and EXTRACT(YEAR_MONTH FROM a.select_date)>='$fYearMonth' and EXTRACT(YEAR_MONTH FROM a.select_date)<='$tYearMonth' and a.payment_mode='CHQ' and a.chq_rtgs_no is not null";

		$orderBy = "a.select_date asc, b.name asc";
		
		$qry = "select a.id, a.select_date, a.distributor_id, a.amount, a.cod, a.description, b.name as distName, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason from (t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id left join m_common_reason mcr on mcr.id=a.reason_id";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Low balance details
	function getNegativeCreditLimitRecs($fromDate, $tillDate)
	{
		//a.select_date>= '$fromDate' and a.select_date<= '$tillDate' and
		//a1.select_date>= '$fromDate' and a1.select_date<= '$tillDate'

		$qry = "select sum(dAmt) as debitAmt, sum(cAmt) as creditAmt, openAmt, outAmt, (sum(dAmt)-sum(cAmt))+IFNULL(openAmt,0) as outStandAmount, creditLimit, distributor_id, distName, (sum(cAmt)+creditLimit-(sum(dAmt)+openAmt)) as creditBalance from
		( 
			select a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, b.opening_bal as openAmt, b.credit_limit as creditLimit, b.name as distName from t_distributor_ac a join m_distributor b on a.distributor_id=b.id where (a.value_date is not null and a.value_date!='0000-00-00') and a.pmt_type!='M' and a.cod='D' group by a.distributor_id
		union
			select a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, b1.opening_bal as openAmt, b1.credit_limit as creditLimit, b1.name as distName from t_distributor_ac a1 join m_distributor b1 on a1.distributor_id=b1.id where (a1.value_date is not null and a1.value_date!='0000-00-00') and a1.pmt_type!='M' and a1.cod='C' group by a1.distributor_id
		) 
	 	as X group by distributor_id order by distName";
		$result = $this->databaseConnect->getRecords($qry);
		//echo "<br>$qry<br>";	
		$resultArr = array();
		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$outStandAmt = $r[4];
				$creditLimit = $r[5];
				$distributorId	= $r[6];		
				$distributorName = $r[7];
				$creditBalance = ($outStandAmt<0)?($creditLimit+abs($outStandAmt)):($creditLimit-abs($outStandAmt));
				if ($creditBalance<0) {
					$resultArr[$distributorId] = array($distributorName,$creditBalance, '', '', '', '');
				}
			}
		}
		
		//$resultArr[7] = array("AS", "-500"); 
		return $resultArr;
	}

	function distWiseDepositedChqRecs($pendingChqTillDate, $distributorId)
	{
		/*
		$whr = " a.distributor_id=b.id and (a.value_date is null or a.value_date='0000-00-00') and EXTRACT(YEAR_MONTH FROM (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )))>='$fYearMonth' and EXTRACT(YEAR_MONTH FROM (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )))<='$tYearMonth' and a.payment_mode='CHQ' and a.chq_rtgs_no is not null and a.distributor_id='$distributorId'";
		*/
		
		$whr = " a.distributor_id=b.id and (a.value_date is null or a.value_date='0000-00-00') and (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) ))<='$pendingChqTillDate' and a.payment_mode='CHQ' and a.chq_rtgs_no is not null and a.distributor_id='$distributorId' and a.parent_ac_id is null and a.adv_entry_parent_id is null";

		$orderBy = "a.select_date asc, b.name asc";
		
		$qry = "select a.id, a.select_date, a.distributor_id, a.amount, a.cod, a.description, b.name as distName, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason from (t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id left join m_common_reason mcr on mcr.id=a.reason_id";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;

		//echo "<br>Dist Wise<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	# Get Cheque Deposited Distributors
	function chqDepositedDistributor($pendingChqTillDate)
	{
		/*
		$whr = " a.distributor_id=b.id and (a.value_date is null or a.value_date='0000-00-00') and EXTRACT(YEAR_MONTH FROM (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )))>='$fYearMonth' and EXTRACT(YEAR_MONTH FROM (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )))<='$tYearMonth' and a.payment_mode='CHQ' and a.chq_rtgs_no is not null";
		*/

		$whr = " a.distributor_id=b.id and (a.value_date is null or a.value_date='0000-00-00') and (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) ))<='$pendingChqTillDate' and a.payment_mode='CHQ' and a.chq_rtgs_no is not null and a.parent_ac_id is null and a.adv_entry_parent_id is null";

		$groupBy = "a.distributor_id";
		$orderBy = "b.name asc";
		
		$qry = "select b.id, b.name as distName, sum(a.amount) as depoAmt from (t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id left join m_common_reason mcr on mcr.id=a.reason_id";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;

		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	# Dist Account 
	function getDistAccountRecs($fromDate, $tillDate, $pendingChqTillDate)
	{
		list($pChqDays, $crBalDisplayLimit, $overdueDisplayLimit) = $this->dacDashboardSettings();
		

		$creditBalanceRecs = array();
		# get over due recs (Check Variable hide for calc )		
		$overDueRecs = $this->getOverduePayments($fromDate, $tillDate);
		foreach ($overDueRecs as $distributorId=>$odr) {
			if ($odr[4]>(float)$overdueDisplayLimit) {
				if (!isset($creditBalanceRecs[$distributorId])) $creditBalanceRecs[$distributorId] = array();
				//$creditBalanceRecs[$distributorId] += array($odr[0], $cb, $depositedAmt, $showPmnt, $odr[4], $odr[5]);
				$creditBalanceRecs[$distributorId][0] = $odr[0];
				$creditBalanceRecs[$distributorId][4] = $odr[4];
				$creditBalanceRecs[$distributorId][5] = $odr[5];
			}
		}		
		
		# get Negative Credit Balance recs		
		$negativeCLRecs = $this->getNegativeCreditLimitRecs($fromDate, $tillDate);
		$negativeCrAmt = 0;
		foreach ($negativeCLRecs as $distributorId=>$ncl) {
			$negativeCrAmt = $ncl[1];							
			//if ($ncl[1]>(float)$crBalDisplayLimit) {
			if ($negativeCrAmt<(-$crBalDisplayLimit)) {
				if (!isset($creditBalanceRecs[$distributorId])) $creditBalanceRecs[$distributorId] = array();
				//$creditBalanceRecs[$distributorId] += array($ncl[0], $ncl[1], $depositedAmt=null, $showPmnt=null, $overdueAmt=null, $invs=null);
				$creditBalanceRecs[$distributorId][0] = $ncl[0];
				$creditBalanceRecs[$distributorId][1] = $ncl[1];
			}
		}
		
		/*
		$creditBalanceRecs = $this->getOverduePayments($fromDate, $tillDate);
		$creditBalanceRecs += $this->getNegativeCreditLimitRecs($fromDate, $tillDate);
		*/
				
		# get Deposited chqs
		$chqDepositedDist  = $this->chqDepositedDistributor($pendingChqTillDate);
		$distACArr = array();
		
		foreach ($chqDepositedDist as $cdr) {
			$distributorId 	= $cdr[0];
			$distName 	= $cdr[1];
			$depositedAmt	= $cdr[2];
			# Distributor wise Pending chqs details
			$distChqRecs = $this->distWiseDepositedChqRecs($pendingChqTillDate, $distributorId);
			$showPmnt = "";
			if (sizeof($distChqRecs)>0) {
				$showPmnt  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc><td class=listing-head nowrap>Cheque Date</td><td class=listing-head nowrap>Cheque/RTGS No.</td><td class=listing-head nowrap>Amount</td></tr>";
				$totPendingAmt = "";
				foreach ($distChqRecs as $dar) {
					$chqRTGSNo	= $dar[11];
					$chqRTGSDate	= ($dar[12]!="0000-00-00")?dateFormat($dar[12]):"";
					$pendingAmt 	= $dar[3];
					$totPendingAmt += $pendingAmt;
					$showPmnt .= "<tr bgcolor=#fffbcc>";
					$showPmnt .= "<td class=listing-item>$chqRTGSDate</td>";
					$showPmnt .= "<td class=listing-item>$chqRTGSNo</td>";
					$showPmnt .= "<td class=listing-item align=right>$pendingAmt</td>";
					$showPmnt .= "</tr>";				
				}
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head nowrap colspan=2 align=right>Total:</td><td class=listing-item nowrap align=right><strong>".number_format($totPendingAmt,2,'.','')."</strong></td></tr>";
				$showPmnt  .= "</table>";
			}

			$cb = "";			
			if (!isset($creditBalanceRecs[$distributorId])) $creditBalanceRecs[$distributorId] = array();

			if ($showPmnt!="") {
				$creditBalanceRecs[$distributorId][0] = $distName;
				$creditBalanceRecs[$distributorId][2] = $depositedAmt;
				$creditBalanceRecs[$distributorId][3] = $showPmnt;
			}
			/*
			if (sizeof($creditBalanceRecs)>0 && $showPmnt!="" ) {
				$creditBalanceRecs[$distributorId] += array($distName, $cb, $depositedAmt, $showPmnt);
			} else if ($showPmnt!="") {
				$creditBalanceRecs[$distributorId] = array($distName, $cb, $depositedAmt, $showPmnt);	
			}
			*/			
		}
		
		 //array($distName, $cb, $depositedAmt, $showPmnt, $overdueAmt, $selInvs);		
		uasort($creditBalanceRecs, 'cmp_finame');
		return $creditBalanceRecs;
	}


	# Dist Overdue section 
	function getOverduePayments($fromDate, $tillDate)
	{
		$qry = "select (sum(dAmt)-sum(cAmt))+openAmt as outStandAmount, creditLimit, distributor_id, distName, cPeriod, sum(cAmt) as creditAmt, crPeriodFrom from
		( 
			select a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, b.opening_bal as openAmt, b.credit_limit as creditLimit, b.name as distName, b.credit_period as cPeriod, b.cr_period_from as crPeriodFrom from t_distributor_ac a join m_distributor b on a.distributor_id=b.id where a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.cod='D' group by a.distributor_id
		union
			select a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, b1.opening_bal as openAmt, b1.credit_limit as creditLimit, b1.name as distName, b1.credit_period as cPeriod, b1.cr_period_from as crPeriodFrom from t_distributor_ac a1 join m_distributor b1 on a1.distributor_id=b1.id where a1.select_date>='$fromDate' and a1.select_date<='$tillDate' and a1.cod='C' group by a1.distributor_id
		) 
	 	as X group by distributor_id order by distName";

		//echo "<br>$qry";
		$result =  $this->databaseConnect->getRecords($qry);

		$resultArr = array();

		foreach ($result as $r) {
			$outStandAmt 	= $r[0];
			$distributorId 	= $r[2];
			$creditLimit	= $r[1];
			$creditPeriod	= $r[4];
			$distName	= $r[3];
			$creditAmt 	= $r[5];
			$crPeriodFrom	= $r[6];			
			//echo "<br>====>Dist=$distributorId, OutAmt=$outStandAmt, $creditLimit, $creditPeriod";
			$getInvRecs = $this->getInvRecs($fromDate, $tillDate, $distributorId);			
			$totalODAmt = 0;
			$invArr = array();
			$pendingInvs="";
			$i = 0;
			$j=0;
			foreach ($getInvRecs as $ir) {
				$selectDate 	= $ir[2];
				$debitAmt	= $ir[3];
				$creditAmt	= $ir[4];
				$invNum		= $ir[5];
				$deliveryDate	= $ir[6];
				$dispatchDate	= $ir[7];
				$missingMsg = "";
				if ($crPeriodFrom=='DELID' && ($deliveryDate=="0000-00-00" || $deliveryDate=="")) $missingMsg = "(Delivery Date Missing)";
				else if ($crPeriodFrom=='DESPD' && ($dispatchDate=="0000-00-00" || $dispatchDate=="")) $missingMsg = "(Despatch Date Missing)"; 

				# Diff Days + Credit Period < 1 ? days Exceed = Y
				$diffDays = findDateDiff($selectDate, $tillDate);
				$daysExceed = $diffDays+$creditPeriod;
				$odAmt = 0;
				
				if ($daysExceed<1) {
					$j++;
					$odAmt = $debitAmt-$creditAmt;
					if ($odAmt>0) {
						$totalODAmt += $odAmt;
						$invArr[$i] = $invNum.$missingMsg;
					}
				}
				
				$i++;
			}
			//echo "<br>$distributorId=".$j;
			$pendingInvs = implode(",",$invArr);
			//$creditBalanceRecs[$distributorId] = array($distName, $cb, $depositedAmt, $showPmnt, $overDueAmt, $selInvs);
			if ($totalODAmt!=0) $resultArr[$distributorId] = array($distName, "", "", "", $totalODAmt, $pendingInvs);
		} // Loop Ends here

		return $resultArr;
	}

	function getInvRecs($fromDate, $tillDate, $distributorId)
	{
		
		/* Modified on 31 MAY 11
		$qry = "select tdac.id, tdac.so_id, tdac.select_date, tdac.debit_amt as debitAmt, tdac.credit_amt as creditAmt, tso.so as invNum from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id join t_salesorder tso on tso.id=tdaci.invoice_id join m_distributor md on md.id=tso.distributor_id join m_common_reason mcr on mcr.id=tdac.reason_id where mcr.de_code='SI' and tdac.select_date>='$fromDate' and tdac.select_date<='$tillDate'  and tdac.distributor_id='$distributorId' and tdac.select_date<=(SELECT DATE_SUB(CURDATE(), INTERVAL md.credit_period DAY))";
		*/
		$qry = "select tdac.id, tdac.so_id, tdac.select_date, tdac.debit_amt as debitAmt, tdac.credit_amt as creditAmt, tso.so as invNum, tso.delivery_date, tso.last_date  from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id join t_salesorder tso on tso.id=tdaci.invoice_id join m_distributor md on md.id=tso.distributor_id join m_common_reason mcr on mcr.id=tdac.reason_id where mcr.de_code='SI' and tdac.select_date>='$fromDate' and tdac.select_date<='$tillDate'  and tdac.distributor_id='$distributorId' and if (md.cr_period_from='DELID', if (tso.delivery_date!='0000-00-00',tso.delivery_date, if (tso.delivery_date='0000-00-00' and tso.last_date!='0000-00-00',tso.last_date,tso.invoice_date)), if (md.cr_period_from='DESPD', if (tso.last_date!='0000-00-00',tso.last_date,tso.invoice_date), tso.invoice_date))<=(SELECT DATE_SUB(CURDATE(), INTERVAL md.credit_period DAY))";

		//echo "<br>DistId=$distributorId<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get debit Amount
	function getDistACTransaction($fromDate, $tillDate, $distributorId)
	{
		$qry = "select tdac.id, tdac.select_date, tdac.amount, tdac.cod, tdac.so_id from t_distributor_ac tdac  where tdac.select_date>='$fromDate' and tdac.select_date<='$tillDate'  and tdac.distributor_id='$distributorId' and tdac.cod='D' order by tdac.select_date desc";
		//echo "<br>$distributorId--><br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function getOverdueInvoice($fromDate, $invDate, $distributorId)
	{				
		$qry = "select tdac.select_date, tdac.amount, tdac.so_id, tso.so 
			from 
				t_distributor_ac tdac join m_distributor md on tdac.distributor_id=md.id left join t_salesorder tso on tso.id=tdac.so_id
			where tdac.select_date>='$fromDate' and tdac.select_date<='$invDate' 
				and tdac.distributor_id='$distributorId' 
				and if (tso.delivery_date!='0000-00-00',tso.delivery_date, if (tso.delivery_date='0000-00-00' and tso.last_date!='0000-00-00',tso.last_date,tso.invoice_date)), if (md.cr_period_from='DESPD', if (tso.last_date!='0000-00-00',tso.last_date,tso.invoice_date), tso.invoice_date))<=(SELECT DATE_SUB(CURDATE(), INTERVAL md.credit_period DAY)) 
				and tdac.cod='D' 
				and tdac.so_id not in (select invoice_id from t_distributor_ac a, t_distributor_ac_invoice b where a.id=b.dist_ac_id and a.cod='C')
			order by tdac.select_date desc";

		//echo "<br>OVER-$distributorId--><br>$qry<br>";				
		$result = $this->databaseConnect->getRecords($qry);	
		$totAmt = 0;
		$invArr = array();
		$i=0;
		foreach ($result as $r) {
			$invAmt = $r[1];
			$totAmt += $invAmt;
			$soId	= $r[2];
			$invNum	= $r[3];
			$invArr[$i] = $invNum;
			$i++;
		}
		$selInvs = implode(",",$invArr);
		return array($totAmt, $selInvs);
	}

	/*
	function getOverduePayments($fromDate, $tillDate)
	{
		$qry = "select (sum(dAmt)-sum(cAmt))+openAmt as outStandAmount, creditLimit, distributor_id, distName, cPeriod, sum(cAmt) as creditAmt from
		( 
			select a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, b.opening_bal as openAmt, b.credit_limit as creditLimit, b.name as distName, b.credit_period as cPeriod from t_distributor_ac a join m_distributor b on a.distributor_id=b.id where a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.cod='D' group by a.distributor_id
		union
			select a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, b1.opening_bal as openAmt, b1.credit_limit as creditLimit, b1.name as distName, b1.credit_period as cPeriod from t_distributor_ac a1 join m_distributor b1 on a1.distributor_id=b1.id where a1.select_date>='$fromDate' and a1.select_date<='$tillDate' and a1.cod='C' group by a1.distributor_id
		) 
	 	as X group by distributor_id order by distName";

		//echo "<br>$qry";
		$result =  $this->databaseConnect->getRecords($qry);

		$resultArr = array();

		foreach ($result as $r) {
			$outStandAmt 	= $r[0];
			$distributorId 	= $r[2];
			$creditLimit	= $r[1];
			$creditPeriod	= $r[4];
			$distName	= $r[3];
			$creditAmt 	= $r[5];

			//echo "<br>====>Dist=$distributorId, OutAmt=$outStandAmt, $creditLimit, $creditPeriod";

			# Dist Trns recs
			$distRecs = $this->getDistACTransaction($fromDate, $tillDate, $distributorId);
			$i = 0;
			foreach ($distRecs as $dr) {
				$distACId	= $dr[0];
				$invDate	= $dr[1];
				$invAmt		= $dr[2];
				$COD		= $dr[3];	
				$soId		= $dr[4];
				$outStandAmt -= $invAmt;
				//$diffAmt = $outStandAmt-$invAmt;
				if ($outStandAmt<=0 || $creditAmt==0) {
					$i++;
					//echo "<br>$i=>$soId:::BAmt=$outStandAmt:::INV=$invAmt::: $invDate";
					list($overdueAmt, $selInvs) = $this->getOverdueInvoice($fromDate, $invDate, $distributorId);
					break;
				}				
			}
			//echo "<br>$overdueAmt, $selInvs";
			//$creditBalanceRecs[$distributorId] = array($distName, $cb, $depositedAmt, $showPmnt);
			if ($overdueAmt!=0) $resultArr[$distributorId] = array($distName, "", "", "", $overdueAmt, $selInvs); 
			
		} // Loop Ends here

		return $resultArr;
	}
	*/

	# ----------------------- Daily RM CB starts here --------------
	function getDailyRMCBQty($selDate)
	{
		$qry = "select sum(pre_process_cs+closing_balance+re_process_cs) from t_daily_rm_cb where select_date='$selDate' group by select_date";

		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		//print_r($result);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
	# ----------------------- Daily RM CB ends here --------------

	# Qty 4 PKG Starts here ----------
	function qtyForPkg($selDate)
	{
		# OB Qty
		$rmOBQty 	= $this->rmOBQty($selDate);
		//echo "<br>$selDate:$rmOBQty";
		$rmArrivalQty 	= $this->rmArrivalQty($selDate);
		$preProcessedQty = $this->getTotalPreProcessedQty($selDate);
		$rePreProcessedQty = $this->rmThawedQty($selDate);
		$peeledQty = $this->dppPeeledQty($selDate);
		$netQty = $rmOBQty+$rmArrivalQty+$preProcessedQty+$rePreProcessedQty;
		$qtyForPkg = $netQty-$peeledQty;

		//echo "<br>==>$rmOBQty+$rmArrivalQty+$preProcessedQty+$rePreProcessedQty || $netQty-$peeledQty";
		return array(number_format($qtyForPkg,2,'.',''), $rmOBQty);
	}

	function rmOBQty($selDate)
	{		
		$qry = "select  sum(closing_balance+pre_process_cs+re_process_cs) from t_daily_rm_cb where select_date=(DATE_SUB('$selDate', INTERVAL 1 DAY)) group by select_date";
		//echo "OBQty=<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecord($qry);				
		return number_format($result[0],2,'.','');
	}

	function rmArrivalQty($selDate)
	{		
		$qry = " select sum(b.effective_wt+b.adjust+b.local_quantity+b.soft) as totalRMQty from (t_dailycatch_main a, t_dailycatchentry b) where a.id=b.main_id and a.select_date='$selDate' group by a.select_date";
		//echo "Arrival Qty===<br>$qry<br>";	

		$result	= $this->databaseConnect->getRecord($qry);		
		return number_format($result[0],2,'.','');
	}

	function rmThawedQty($selDate)
	{
		# Daily Frzn Packing records
		$dailyThawedRecs = $this->dailyThawedRecs($selDate);
		$totalGrossWt = 0;
		foreach ($dailyThawedRecs as $dtr) {
			$numMc		= $dtr[4];
			$numLoosePack 	= $dtr[5];
			$declaredFilledWt = $dtr[6]; // Filled Wt
			$actualFilledWt = $dtr[7];
			$frznCodeFilledWt = ($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt;
			$mcActualWt = $frznCodeFilledWt*$numMc;
			$eachPackWt = $frznCodeFilledWt/$numMc;
			$lcActualWt = $numLoosePack*$eachPackWt;
			$grossWt = $mcActualWt+$lcActualWt;
			$totalGrossWt += $grossWt;
			//echo "Out=$numMc, $numLoosePack, $frznCodeFilledWt, Gross=$grossWt<br>";
		}
		return number_format($totalGrossWt,2,'.','');
	}

	function dailyThawedRecs($selDate)
	{
		$qry = " select 
				tdt.id, tdt.select_date, tdt.frozencode_id, tdt.mcpacking_id, sum(tdtg.number_mc_thawing), sum(tdtg.number_loose_slab_thawing), mf.filled_wt, mf.actual_filled_wt
			from 
				t_dailythawing tdt left join t_dailythawing_grade tdtg on tdt.id=tdtg.main_id
				left join m_frozenpacking mf on tdt.frozencode_id=mf.id
			where 
				tdt.select_date='$selDate' and tdt.flag=1
			group by tdt.frozencode_id
			";
		//echo "Thawed=<br>$qry<br>"; 
		return $this->databaseConnect->getRecords($qry);
	}

	function dppPeeledQty($selDate)
	{	
		$qry = " select c.id, c.processes , c.criteria, sum(b.total_preprocess_qty), sum(b.arrival_qty) as actualUsedQty , b.actual_yield, b.ideal_yield, b.diff_yield, sum(b.total_qty) as totalQty from (t_dailypreprocess a, t_dailypreprocess_entries b, m_process c) where b.dailypreprocess_main_id=a.id and b.total_preprocess_qty and a.date='$selDate' and b.process=c.id group by b.process order by c.processes asc ";
		//echo "<br><b>Pre-Process=$processCodeId</b><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		$rSize	= sizeof($result);
		if (sizeof($result)>0) {			
			$totProcessingQty = 0;						
			foreach ($result as $r) {
				$preProcessedQty  = $r[3];				
				$actualY	 = $r[5];
				$idealY		 = $r[6];
				$processingQty   = $r[4];
				$peeledQty = 0;
				if ($processingQty==0) {
					$actYCalc = (100/$actualY);
					$peeledQty = $actYCalc*$preProcessedQty;
					$processingQty = ceil($peeledQty);
				}
				$totProcessingQty += $processingQty;				
			}			
		}
		return number_format($totProcessingQty,2,'.','');
	}

	# Qty 4 Pkg Ends here --------------------

	# Distributor account dashboard settings
	function dacDashboardSettings()
	{
		$qry = "select pending_chq_days, cr_bal_display_limit, overdue_display_limit from c_system";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return array($result[0][0], $result[0][1], $result[0][2]);
	}

}
?>