<?php
class DistributorAccountReport
{
	/****************************************************************
	This class deals with all the operations relating to Distributor Account Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DistributorAccountReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	
	//$chrtArr	= array("CHQN"=>"Cheque No", "RTGSN"=>"RTGS No");
	//$pmtDateArr	= array("CHQD"=>"Cheque Date", "VALD"=>"Value Date");
	function getDistPmtChqRtgsRecs($fromDate, $tillDate, $cbChqRt, $ddChqRt, $chqRtNo, $cbShowSimilar, $cbPmtDate, $ddPmtDate, $pmtDate, $cbPmtAmt, $txtPmtAmt, $ddPmtDateType, $pmtEndDate)
	{		
		$strLen = strlen($chqRtNo);
		// Remove right digit
		$digitLessChqNo = ($strLen>1)?substr($chqRtNo, 0, -1):$chqRtNo; 
		$lastFourDigit = bcmod("$chqRtNo","10000"); // Large value convert
		//echo "$chqRtNo=$lastFourDigit";


		$whr = " a.distributor_id=b.id and a.adv_entry_parent_id is null and a.select_date>='$fromDate' and a.select_date<='$tillDate' ";

		if ($cbChqRt!="") {
			if ($ddChqRt=="CHQN") $whr .= " and a.payment_mode = 'CHQ' ";
			else if ($ddChqRt=="RTGSN") $whr .= " and a.payment_mode = 'RT' ";
	
			if ($cbShowSimilar)	$whr .= " and (a.chq_rtgs_no='$chqRtNo' or TRIM(LEADING '0' FROM a.chq_rtgs_no)='$chqRtNo' or FLOOR(CAST(a.chq_rtgs_no AS UNSIGNED )/10)='$digitLessChqNo' or mod(a.chq_rtgs_no,10000)='$lastFourDigit' or Length(TRIM(LEADING '0' FROM a.chq_rtgs_no))='$strLen' or (CAST(mod(a.chq_rtgs_no,10000) as UNSIGNED)>=".($lastFourDigit-1000)." and CAST(mod(chq_rtgs_no,10000) as UNSIGNED)<=".($lastFourDigit+1000)." ) )";
			
			//if ($cbShowSimilar)	$whr .= " and ((right('$chqRtNo',2) = right(a.chq_rtgs_no,2)) or a.chq_rtgs_no like '%$chqRtNo%') ";
			else 			$whr .= " and a.chq_rtgs_no='$chqRtNo' ";
		}

		if ($cbPmtDate) {
			if ($ddPmtDate=="CHQD") {
				if ($ddPmtDateType=='DR') $whr .= " and a.chq_date>='$pmtDate' and a.chq_date<='$pmtEndDate' ";
				else $whr .= " and a.chq_date='$pmtDate' ";
			} else if ($ddPmtDate=="VALD") {
				if ($ddPmtDateType=='DR') $whr .= " and a.value_date>='$pmtDate' and a.value_date<='$pmtEndDate' ";
				else $whr .= " and a.value_date='$pmtDate' ";
			}
		}

		if ($cbPmtAmt) {
			//$whr .= " and a.payment_mode = 'CH' and a.amount='$txtPmtAmt' ";
			$whr .= " and (a.amount=cast('$txtPmtAmt' AS decimal(10,2)) or (a.amount>=cast('".($txtPmtAmt-1)."' AS decimal(10,2)) and a.amount<=cast('".($txtPmtAmt+1)."' AS decimal(10,2))) )";
		}

		if ($distributorFilterId) 	$whr .= " and a.distributor_id='$distributorFilterId' ";
		if ($cityFilterId)		$whr .= " and a.city_id='$cityFilterId' ";
		if ($reasonFilterIds!="") 	$whr .= " and a.reason_id in ($reasonFilterIds)";
		
		$uptdMainQry = "";
		if ($invoiceFilterId) {
			$whr .= " and dai.invoice_id='$invoiceFilterId' ";
			$uptdMainQry = "left join t_distributor_ac_invoice dai on a.id=dai.dist_ac_id";
			$whr .= " and a.pmt_type!='M'";
		} else {
			$whr .= " and a.pmt_type!='A'";
		}

		
		if (!$filterType || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00')";
		else if ($filterType=="PE") $whr .= " and (a.value_date='0000-00-00' or a.value_date is null)";
		else if ($filterType=="CHQR" || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00') and mcr.de_code='PR'";
		
		if ($filterType=="PE")  $orderBy = "a.select_date asc, mcr.de_code asc, a.id asc";
		else $orderBy = "a.value_date asc, mcr.de_code asc, a.id asc";
			
		
		$qry = "select 
				a.id, (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )) , a.distributor_id, a.amount, a.cod, a.description, b.name, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason, a.chq_return, a.chq_dist_ac_id, a.post_type, mcr.de_code, a.deposited_bank_ac_id, a.dist_bank_ac_id, CONCAT(SUBSTRING_INDEX(mbcb.bank_name,' ',1),' ',mbcb.account_no) as displayBillCompanyName, mdba.bank_name as distbankName, mdba.account_no as distAcNo, mdba.branch_location as distBrLoc 
			from 
				(t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id 
				left join m_common_reason mcr on mcr.id=a.reason_id 
				left join m_distributor_bank_ac mdba on a.dist_bank_ac_id=mdba.id 
				left join m_billing_company_bank_ac mbcb on mbcb.id=a.deposited_bank_ac_id $uptdMainQry";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}


	function getDistAdvSearchRecs($fromDate, $tillDate)
	{	
		
		$whr = " a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.adv_entry_parent_id is null and a.cod='C' ";
	
		
		$uptdMainQry = "";
		if ($invoiceFilterId) {
			$whr .= " and dai.invoice_id='$invoiceFilterId' ";
			$uptdMainQry = "left join t_distributor_ac_invoice dai on a.id=dai.dist_ac_id";
			$whr .= " and a.pmt_type!='M'";
		} else {
			$whr .= " and a.pmt_type!='A'";
		}

		
		if (!$filterType || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00')";
		else if ($filterType=="PE") $whr .= " and (a.value_date='0000-00-00' or a.value_date is null)";
		else if ($filterType=="CHQR" || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00') and mcr.de_code='PR'";
		
		if ($filterType=="PE")  $orderBy = "a.select_date asc, mcr.de_code asc, a.id asc";
		else $orderBy = "a.value_date asc, mcr.de_code asc, a.id asc";
			
		
		$qry = "select 
				a.id, (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )) , a.distributor_id, a.amount, a.cod, a.description, b.name, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason, a.chq_return, a.chq_dist_ac_id, a.post_type, mcr.de_code, a.deposited_bank_ac_id, a.dist_bank_ac_id, CONCAT(SUBSTRING_INDEX(mbcb.bank_name,' ',1),' ',mbcb.account_no) as displayBillCompanyName, mdba.bank_name as distbankName, mdba.account_no as distAcNo, mdba.branch_location as distBrLoc 
			from 
				(t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id 
				left join m_common_reason mcr on mcr.id=a.reason_id 
				left join m_distributor_bank_ac mdba on a.dist_bank_ac_id=mdba.id 
				left join m_billing_company_bank_ac mbcb on mbcb.id=a.deposited_bank_ac_id $uptdMainQry";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}


/*
	# Sales Order Records
	function getSalesOrderRecords($fromDate, $tillDate, $selDistributorId, $pendingOrder, $orderDispatched)
	{
		$whr  = "  a.id=b.salesorder_id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.distributor_id='$selDistributorId' ";
	
		if ($pendingOrder!="")		$whr	.= " and (a.complete_status<>'C' or a.complete_status is null) ";
		else if ($orderDispatched!="")	$whr	.= " and a.complete_status='C' ";

		$groupBy	= " b.salesorder_id ";
		$orderBy	= " a.so asc ";
		 

		$qry = "select a.id, a.so, a.distributor_id, a.invoice_date, a.grand_total_amt, a.last_date, a.extended, a.logstatus, a.logstatus_descr, a.payment_status, a.dispatch_date, a.status_id, a.complete_status, a.invoice_type, a.proforma_no, a.sample_invoice_no from (t_salesorder a, t_salesorder_entry b)";

		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($groupBy!="")	$qry	.= " group by ".$groupBy;
		if ($orderBy!="")	$qry	.= " order by ".$orderBy;
			
		//echo "SO===><br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Claim Records
	function getClaimOrderRecords($fromDate, $tillDate, $selDistributorId, $claimPending, $claimSettled)
	{
		$whr 	= " a.created>='$fromDate' and a.created<='$tillDate' and a.distributor_id='$selDistributorId' ";

		if ($claimPending!="")		$whr	.= " and a.complete_status<>'C' ";
		else if ($claimSettled!="")	$whr	.= " and a.complete_status='C' ";

		$orderBy	= " a.claim_number asc";

		$qry = "select a.id, a.claim_number, a.created, a.last_date, a.extended, a.logstatus, a.logstatus_descr, a.settled_date, a.status_id, a.complete_status, a.claim_type, a.cod, a.fixed_amount, a.mr_amount from t_claim a ";

		if ($whr!="")		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry	.= " order by ".$orderBy;
			
		//echo "CO===><br>".$qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Distributor Account Records
	function getDistributorAccountRecords($fromDate, $tillDate, $selDistributorId)
	{
		$resultArr = array();
	
		$whr	=  " a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.distributor_id='$selDistributorId' and a.pmt_type!='A' and a.adv_entry_parent_id is null ";
		$orderBy	= " a.select_date asc ";
		$qry = " select a.id, a.select_date, a.amount, a.cod, a.description from t_distributor_ac a ";

		if ($whr!="")		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		//echo "DA===><br>".$qry."<br>";
		$distAccountRecords	=	$this->databaseConnect->getRecords($qry);
		
		list($openingBalanceAmt, $postType) = $this->getOpeningBalanceAmt($fromDate, $tillDate, $selDistributorId);
		//echo "<br>$openingBalanceAmt,$postType";
		if ($openingBalanceAmt!=0) $resultArr[0] = array($fromDate,"Opening Balance",$openingBalanceAmt,$postType);
		$i=1;
		if (sizeof($distAccountRecords)>0) {
			foreach ($distAccountRecords as $dar) {
				$i++;
				$selectDate		= $dar[1];			
				$particulars		= $dar[4];
				$amount			= $dar[2];
				$cod			= $dar[3];
				$resultArr[$i] = array($selectDate,$particulars,$amount,$cod);
			}
		}	

		return $resultArr;		
	}

	# Get Opening Balance Amt
	function getOpeningBalanceAmt($fromDate, $tillDate, $selDistributorId, $selCityId=null)
	{
		//$selCityId = 19;
		if (!$selCityId) {
			$qry = "select distributor_id,  sum(dAmt) as debitAmt, sum(cAmt) as creditAmt, openAmt, outAmt, (sum(dAmt)-sum(cAmt))+openAmt from
			( 
				select a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, b.opening_bal as openAmt from t_distributor_ac a, m_distributor b where a.distributor_id=b.id and a.select_date<=  DATE_SUB('$fromDate', INTERVAL 1 DAY) and a.cod='D' and a.distributor_id='$selDistributorId' group by a.distributor_id
			union
				select a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, b1.opening_bal as openAmt from t_distributor_ac a1, m_distributor b1 where a1.distributor_id=b1.id and a1.select_date<=DATE_SUB('$fromDate', INTERVAL 1 DAY) and a1.cod='C' and a1.distributor_id='$selDistributorId' group by a1.distributor_id
			) 
			as X group by distributor_id";
		}
		
		# Join with m_distributor_state 
		if ($selCityId) {			
			$qry = "select distributor_id,  sum(dAmt) as debitAmt, sum(cAmt) as creditAmt, openAmt, outAmt, (sum(dAmt)-sum(cAmt))+ob, ob from
			( 
				select a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, b.opening_bal as openAmt, (mds.opening_balance) as ob from (t_distributor_ac a, m_distributor b) 
				left join m_distributor_state mds on mds.distributor_id=b.id 
				join m_distributor_city mdc on mdc.dist_state_entry_id=mds.id and a.city_id=mdc.city_id  and mdc.city_id='$selCityId' 
				where a.distributor_id=b.id and a.select_date<= DATE_SUB('$fromDate', INTERVAL 1 DAY) and a.cod='D' and a.distributor_id='$selDistributorId' group by a.distributor_id
			union
				select a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, b1.opening_bal as openAmt, (mds1.opening_balance) as ob from (t_distributor_ac a1, m_distributor b1) 
				left join m_distributor_state mds1 on mds1.distributor_id=b1.id 
				join m_distributor_city mdc1 on mdc1.dist_state_entry_id=mds1.id and a1.city_id=mdc1.city_id  and mdc1.city_id='$selCityId'
				where a1.distributor_id=b1.id and a1.select_date<=DATE_SUB('$fromDate', INTERVAL 1 DAY) and a1.cod='C' and a1.distributor_id='$selDistributorId' group by a1.distributor_id
			) 
			as X group by distributor_id";
		}
		
		$rec = $this->databaseConnect->getRecord($qry);
		
		//echo "<br>$qry<br>";	

		if (sizeof($rec)>0) {			
			# If Amt +ve Debit / Credit 
			return ($rec[5]>0)?array($rec[5],'D'):array($rec[5],'C');
		} else {
			$openingAmt = $this->getMasterOpeningAmt($selDistributorId, $selCityId);
			return ($openingAmt>0)?array($openingAmt,'D'):array($openingAmt,'C');
		}
	}

	function getMasterOpeningAmt($selDistributorId, $selCityId=null)
	{
		if (!$selCityId) {
			$qry = " select opening_bal from m_distributor where id='$selDistributorId' ";
		}
		else if ($selCityId) {
			$qry = " select mds.opening_balance as ob 
				from m_distributor b left join m_distributor_state mds on mds.distributor_id=b.id 
				join m_distributor_city mdc on mdc.dist_state_entry_id=mds.id and mdc.city_id='$selCityId' 
				where mds.distributor_id='$selDistributorId' group by mds.distributor_id";
		}
		//echo "<br>=>$qry<br>";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get Sales Order Sample Invoice Records
	function getSOSampleInvoiceRecords($fromDate, $tillDate, $selDistributorId, $qryType)
	{
		$whr  = "  a.id=b.salesorder_id and a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.distributor_id='$selDistributorId' and a.invoice_type='S' ";
	

		if ($qryType=='S') $groupBy = " b.salesorder_id ";
		
		$orderBy	= " a.invoice_date asc, a.so asc ";
				
		$qry = "select a.id, if (a.so!=0 and a.invoice_type='T', a.so, a.sample_invoice_no), a.distributor_id, a.invoice_date, a.grand_total_amt, a.last_date, a.extended, a.logstatus, a.logstatus_descr, a.payment_status, a.dispatch_date, a.status_id, a.complete_status, b.id, b.product_id, b.rate, b.quantity, b.total_amount from t_salesorder a, t_salesorder_entry b";

		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($groupBy!="")	$qry	.= " group by ".$groupBy;
		if ($orderBy!="")	$qry	.= " order by ".$orderBy;
			
		//echo "SO===><br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Invoice Recs
	function distACInvoiceRecs($fromDate, $tillDate, $selDistributorId)
	{	
		

		$qry = "select daci.invoice_id, tso.so, GetFY(tso.invoice_date) as fy from t_distributor_ac dac join t_distributor_ac_invoice daci on dac.id=daci.dist_ac_id join t_salesorder tso on tso.id=daci.invoice_id where dac.select_date>='$fromDate' and dac.select_date<='$tillDate' and dac.distributor_id='$selDistributorId' group by daci.invoice_id order by GetFY(tso.invoice_date) desc, tso.invoice_date desc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);

		$resultArr = array(''=>'--Select--');
		if (sizeof($result)>0) {
			$i=0;
			$prevFY = "";
			foreach ($result as $rec) {
				$fy = $rec[2];
				
				if ($prevFY != $fy) {
					$resultArr["FY".$i] = "--- FY $fy ---"; 	
					$i++;
				}
				$resultArr[$rec[0]] = $rec[1];
				$prevFY = $fy;
			}
		}
		return $resultArr;
	}

	function getDistACRecs($fromDate, $tillDate, $distributorFilterId, $invoiceFilterId)
	{
		$whr = " a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' ";

		if ($distributorFilterId) $whr .= " and a.distributor_id='$distributorFilterId' ";
		
		$uptdMainQry = "";
		if ($invoiceFilterId) {
			$whr .= " and dai.invoice_id='$invoiceFilterId' ";
			$uptdMainQry = "left join t_distributor_ac_invoice dai on a.id=dai.dist_ac_id";
		}

		$orderBy = "a.select_date asc";

		$qry = "select 
				a.id, a.select_date, a.distributor_id, a.amount, a.cod, a.description, b.name, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason, a.so_id, a.chq_return, a.chq_dist_ac_id, a.post_type, mcr.de_code, a.deposited_bank_ac_id, a.dist_bank_ac_id, CONCAT(SUBSTRING_INDEX(mbcb.bank_name,' ',1),' ',mbcb.account_no) as displayBillCompanyName, mdba.bank_name as distbankName, mdba.account_no as distAcNo, mdba.branch_location as distBrLoc 
			from 
				(t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id 
				left join m_common_reason mcr on mcr.id=a.reason_id 
				left join m_distributor_bank_ac mdba on a.dist_bank_ac_id=mdba.id 
				left join m_billing_company_bank_ac mbcb on mbcb.id=a.deposited_bank_ac_id $uptdMainQry";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;

		//echo "<br>$qry";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	function updateDistACStatusRec($distributorAccountId)
	{
		$qry = "update t_distributor_ac set confirmed='N', edited_by=0, edited_time=0 where id='$distributorAccountId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Get Distributor Overdue Invoice records 
	function distOverdueRecs($fromDate, $tillDate, $distributorId)
	{		
		$whr = " a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.distributor_id='$distributorId'  ";
		$whr .= " and (a.select_date<=(SELECT DATE_SUB(CURDATE(), INTERVAL b.credit_period DAY)) or mcr.de_code='PR' or mcr.de_code='CR')";
		$whr .= " and a.value_date!='0000-00-00'";
		$whr .= " and (a.debit_amt-a.credit_amt)>0"; // For getting only overdue recs
		
		$orderBy = "a.value_date asc, a.id asc";

		$qry = "select a.id, (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )) , a.distributor_id, a.amount, a.cod, a.description, b.name, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason, a.chq_return, a.chq_dist_ac_id, a.post_type, a.so_id, mcr.de_code, (a.debit_amt-a.credit_amt) as balDueAmt, a.deposited_bank_ac_id, a.dist_bank_ac_id, CONCAT(SUBSTRING_INDEX(mbcb.bank_name,' ',1),' ',mbcb.account_no) as displayBillCompanyName, mdba.bank_name as distbankName, mdba.account_no as distAcNo, mdba.branch_location as distBrLoc 
		from 
			t_distributor_ac a join  m_distributor b on a.distributor_id=b.id 
			left join m_city mc on a.city_id=mc.id 
			left join m_common_reason mcr on mcr.id=a.reason_id 
			left join t_distributor_ac_invoice tdaci on (a.so_id=tdaci.invoice_id and tdaci.dist_ac_id=a.id )
			left join m_distributor_bank_ac mdba on a.dist_bank_ac_id=mdba.id 
			left join m_billing_company_bank_ac mbcb on mbcb.id=a.deposited_bank_ac_id";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		//echo "<br>Main=<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# get Ref invoice dist ac records
	function refInvDistACRecs($invoiceId)
	{
		//(a.select_date<=(SELECT DATE_SUB(CURDATE(), INTERVAL b.credit_period DAY))) and
		$qry = "select 
			a.id, (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )) , a.distributor_id, a.amount, a.cod, a.description, b.name, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason, a.chq_return, a.chq_dist_ac_id, a.post_type, a.so_id, mcr.de_code, (a.debit_amt-a.credit_amt) as balDueAmt, a.deposited_bank_ac_id, a.dist_bank_ac_id, CONCAT(SUBSTRING_INDEX(mbcb.bank_name,' ',1),' ',mbcb.account_no) as displayBillCompanyName, mdba.bank_name as distbankName, mdba.account_no as distAcNo, mdba.branch_location as distBrLoc 
		from 
			t_distributor_ac a join m_distributor b on a.distributor_id=b.id 
			left join m_city mc on a.city_id=mc.id left join m_common_reason mcr on mcr.id=a.reason_id 
			left join t_distributor_ac_invoice tdaci on ( tdaci.dist_ac_id=a.id ) 
			left join m_distributor_bank_ac mdba on a.dist_bank_ac_id=mdba.id 
			left join m_billing_company_bank_ac mbcb on mbcb.id=a.deposited_bank_ac_id 
		where 
			a.value_date!='0000-00-00' and (mcr.de_code!='SI' or mcr.de_code is null) and tdaci.invoice_id ='$invoiceId' and a.pmt_type!='M' order by a.value_date asc, a.id asc";

		//echo "sub=<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# Get All Over Due Recs
	# Two array field sizemust be same size
	function getOverDueRecs($fromDate, $tillDate, $distributorId)
	{		
		$distOverDues = $this->distOverdueRecs($fromDate, $tillDate, $distributorId);
		
		$overdueArr = array();
		foreach ($distOverDues as $dod) {
			array_push($overdueArr,$dod);
			$soId = $dod[27];
			$refRecs = $this->refInvDistACRecs($soId);
			if (sizeof($refRecs)>0) {
				foreach ($refRecs as $rr) {
					array_push($overdueArr, $rr);
				}	
			}
		} // Loop Ends here
		return $overdueArr;
	}

	# DAILY ACCOUNT STATEMENT
	

	# Financial Year wise listing
	function getInvList()
	{
		$qry = "SELECT id, GetFY(invoice_date) as fy, invoice_date,  invoice_type, so as invNum, proforma_no, sample_invoice_no FROM t_salesorder where complete_status='C' order by GetFY(invoice_date) desc, invoice_date desc ";
		$result	= $this->databaseConnect->getRecords($qry);

		$prevFY = "";
		$invArr = array();
		$i=0;
		foreach ($result as $sor) {
			$invoiceId = $sor[0];
			$fy = $sor[1];
			$soInvoiceType = $sor[3];
			$soNo		= $sor[4];
			$proformaNo	= $sor[5];
			$sampleNo	= $sor[6];
			$invoiceNo = "";
			if ($soNo!=0) $invoiceNo=$soNo;
			else if ($soInvoiceType=='T') $invoiceNo = "P$proformaNo";
			else if ($soInvoiceType=='S') $invoiceNo = "S$sampleNo";
			if ($prevFY != $fy) {
				$invArr["FY".$i] = "---$fy---"; 	
				$i++;
			}
			$invArr[$invoiceId]= $invoiceNo;

			$prevFY = $fy;
		}

		//printr($invArr);
	}
	*/

}
?>