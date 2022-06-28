<?php
class DistributorAccount
{  
	/****************************************************************
	This class deals with all the operations relating to Distributor Account
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DistributorAccount(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add Payment Received recs
	function addDistAccountRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $userId, $debit, $amount, $description, $selCity, $commonReason, $otherReason, $pmtType, $advPmtStatus, $distBankAccount)
	{
		$qry = "insert into t_distributor_ac (entry_type, select_date, distributor_id, payment_mode, chq_rtgs_no, chq_date, bank_name, account_no, branch_location, deposited_ac_no, value_date, bank_charge, bank_charge_descr, created, createdby, cod, amount, description, city_id, reason_id, other_reason, pmt_type, adv_pmt, deposited_bank_ac_id, dist_bank_ac_id) values ('$entryType', '$selDate', '$selDistributor', '$paymentMode', '$chqRtgsNo', '$chqDate', '$bankName', '$accountNo', '$branchLocation', '$depositedBankAccount', '$valueDate', '$bankCharges', '$bankChargeDescription', NOW(), '$userId', '$debit', '$amount', '$description', '$selCity', '$commonReason', '$otherReason', '$pmtType', '$advPmtStatus', '$depositedBankAccount', '$distBankAccount')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Upate Distributor Account 
			if ($pmtType!='M') $this->manageDistributorAccount($selDistributor, $debit, $amount);
		}
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	# Insert Dist Bank charges
	function addDistACBankCharge($distAccountId, $selDate, $selDistributor, $codType, $bankCharges, $bankChargeDescription, $userId, $selCity, $commonReason, $chargesPostType, $valueDate, $verified)
	{
		$qry = "insert into t_distributor_ac (parent_ac_id, select_date, distributor_id, cod, amount, description, created, createdby, city_id, reason_id, post_type, value_date, confirmed) values ('$distAccountId', '$selDate', '$selDistributor', '$codType', '$bankCharges', '$bankChargeDescription', NOW(), '$userId', '$selCity', '$commonReason', '$chargesPostType', '$valueDate', '$verified')";
		//echo "Dist Bank charge entry===><br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();

			# Upate Distributor Account 
			$this->manageDistributorAccount($selDistributor, $codType, $bankCharges);
		}
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert Reference invoice (Also using Sales Order)
	function insertDistAccountInvoice($distAccountId, $invoiceId)
	{
		$qry = "insert into t_distributor_ac_invoice (dist_ac_id, invoice_id) values ('$distAccountId', '$invoiceId')";
		//echo "<br>$qry<br>";

		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
		
	# Add other payment modes
	function addCODDistAccount($selDate, $selDistributor, $amount, $debit, $description, $userId, $soId, $claimId, $commonReason=null, $otherReason=null, $entryType=null, $selCity, $chqReturnBankCharge, $penaltyCharge, $pendingChequeId, $valueDate, $pmtType)
	{
		$qry = "insert into t_distributor_ac (select_date, distributor_id, amount, cod, description, created, createdby, so_id, claim_id, reason_id, other_reason, entry_type, city_id, chq_return_bank_charge, penalty_charge, chq_dist_ac_id, value_date, pmt_type) values ('$selDate', '$selDistributor', '$amount', '$debit', '$description', NOW(), '$userId', '$soId', '$claimId', '$commonReason', '$otherReason', '$entryType', '$selCity', '$chqReturnBankCharge', '$penaltyCharge', '$pendingChequeId', '$valueDate', '$pmtType')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Upate Distributor Account 
			$this->manageDistributorAccount($selDistributor, $debit, $amount);
		}
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Insert Chk List Recs
	function insertDistChkList($distAccountId, $chkListId)
	{
		$qry = "insert into t_distributor_ac_chk_list (dist_ac_id, chk_list_id) values ('$distAccountId', '$chkListId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Insert Record
	function addDistributorAccount($selDate, $selDistributor, $amount, $debit, $description, $userId, $soId, $claimId, $selCity, $entryType, $commonReasonId, $verified, $valueDate, $debitAmt)
	{
		$qry = "insert into t_distributor_ac (select_date, distributor_id, amount, cod, description, created, createdby, so_id, claim_id, city_id, entry_type, reason_id, confirmed, value_date, debit_amt) values ('$selDate', '$selDistributor', '$amount', '$debit', '$description', NOW(), '$userId', '$soId', '$claimId', '$selCity', '$entryType', '$commonReasonId' , '$verified', '$valueDate', '$debitAmt')";

		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Upate Distributor Account 
			$this->manageDistributorAccount($selDistributor, $debit, $amount);
		}
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# Returns all Paging Records 
	# $filterType = VE: Valid Entry, PE: Pending entry
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $distributorFilterId, $cityFilterId, $invoiceFilterId, $reasonFilterIds, $filterType)
	{		
		$whr = " a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.adv_entry_parent_id is null ";

		if ($distributorFilterId) 	$whr .= " and a.distributor_id='$distributorFilterId' ";
		if ($cityFilterId)		$whr .= " and a.city_id='$cityFilterId' ";
		if ($reasonFilterIds!="") 	$whr .= " and a.reason_id in ($reasonFilterIds)";
		
		$uptdMainQry = "";
		if ($invoiceFilterId) {
			$whr .= " and dai.invoice_id='$invoiceFilterId' ";
			$uptdMainQry = " left join t_distributor_ac_invoice dai on a.id=dai.dist_ac_id";
			$whr .= " and a.pmt_type!='M'";
		} else {
			$whr .= " and a.pmt_type!='A'";
		}

		
		if (!$filterType || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00')";
		else if ($filterType=="PE") $whr .= " and (a.value_date='0000-00-00' or a.value_date is null)";
		else if ($filterType=="CHQR" || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00') and mcr.de_code='PR'";
		/*		
		if ($filterType=="PE")  $orderBy = "a.select_date asc, a.id asc";
		else $orderBy = "a.value_date asc, a.id asc";
		*/
		if ($filterType=="PE")  $orderBy = "a.select_date asc, mcr.de_code asc, a.id asc";
		else $orderBy = "a.value_date asc, mcr.de_code asc, a.id asc";
		

		$limit = " $offset, $limit ";
		//a.select_date
		$qry = "select 
			a.id, (if (a.value_date!='0000-00-00', a.value_date, if (a.chq_date!='0000-00-00', a.chq_date, a.select_date) )) , a.distributor_id, a.amount, a.cod, a.description, b.name, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason, a.chq_return, a.chq_dist_ac_id, a.post_type, mcr.de_code, a.deposited_bank_ac_id, a.dist_bank_ac_id, CONCAT(SUBSTRING_INDEX(mbcb.bank_name,' ',1),' ',mbcb.account_no) as displayBillCompanyName, mdba.bank_name as distbankName, mdba.account_no as distAcNo, mdba.branch_location as distBrLoc, a.edited_by, a.edited_time, TIME_TO_SEC(TIMEDIFF(NOW(), a.edited_time)) as diffTS, a.adv_entry_parent_id as partialAdvanceEntry 
			from (t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id 
				left join m_common_reason mcr on mcr.id=a.reason_id 
				left join m_distributor_bank_ac mdba on a.dist_bank_ac_id=mdba.id 
				left join m_billing_company_bank_ac mbcb on mbcb.id=a.deposited_bank_ac_id $uptdMainQry";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="")	  $qry .= " limit ".$limit;

		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Returns all Records
	function fetchDateRangeRecords($fromDate, $tillDate, $distributorFilterId, $cityFilterId, $invoiceFilterId, $reasonFilterIds, $filterType)
	{		
		$whr = " a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.adv_entry_parent_id is null";

		if ($distributorFilterId) $whr .= " and a.distributor_id='$distributorFilterId' ";
		if ($cityFilterId)	$whr .= " and a.city_id='$cityFilterId' ";
		if ($reasonFilterIds!="") $whr .= " and a.reason_id in ($reasonFilterIds)";
		
		$uptdMainQry = "";
		if ($invoiceFilterId) {
			$whr .= " and dai.invoice_id='$invoiceFilterId' ";
			$uptdMainQry = "left join t_distributor_ac_invoice dai on a.id=dai.dist_ac_id";
			$whr .= " and a.pmt_type!='M'";
		} else $whr .= " and a.pmt_type!='A'";

		if (!$filterType || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00')";
		else if ($filterType=="PE") $whr .= " and (a.value_date='0000-00-00' or a.value_date is null)";
		else if ($filterType=="CHQR" || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00') and mcr.de_code='PR'";

		//$orderBy = "a.select_date asc, a.id asc";		
		/*
		if ($filterType=="PE")  $orderBy = "a.select_date asc, a.id asc";
		else $orderBy = "a.value_date asc, a.id asc";
		*/
		if ($filterType=="PE")  $orderBy = "a.select_date asc, mcr.de_code asc, a.id asc";
		else $orderBy = "a.value_date asc, mcr.de_code asc, a.id asc";
		
		$qry = "select a.id, a.select_date, a.distributor_id, a.amount, a.cod, a.description, b.name, a.confirmed, a.parent_ac_id, a.entry_type, a.payment_mode, a.chq_rtgs_no, a.chq_date, a.bank_name, a.account_no, a.branch_location, a.deposited_ac_no, a.value_date, a.bank_charge, a.bank_charge_descr, mc.name as cityName, mcr.reason as reasonName, a.reason_id as commonReasonId, a.other_reason, a.chq_return, a.chq_dist_ac_id, a.post_type, mcr.de_code, a.deposited_bank_ac_id, a.dist_bank_ac_id, CONCAT(SUBSTRING_INDEX(mbcb.bank_name,' ',1),' ',mbcb.account_no) as displayBillCompanyName, mdba.bank_name as distbankName, mdba.account_no as distAcNo, mdba.branch_location as distBrLoc, a.edited_by, a.edited_time, TIME_TO_SEC(TIMEDIFF(NOW(), a.edited_time)) as diffTinS 
		from (t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id 
			left join m_common_reason mcr on mcr.id=a.reason_id
			left join m_distributor_bank_ac mdba on a.dist_bank_ac_id=mdba.id 
			left join m_billing_company_bank_ac mbcb on mbcb.id=a.deposited_bank_ac_id $uptdMainQry";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;		

		//echo "Full Result Set==<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	# Get COD Grand Total
	function getCODGrandTotalAmt($fromDate, $tillDate, $distributorFilterId, $cityFilterId, $invoiceFilterId, $reasonFilterIds, $filterType)
	{
		$whr = " a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.adv_entry_parent_id is null";

		if ($distributorFilterId) $whr .= " and a.distributor_id='$distributorFilterId' ";
		if ($cityFilterId)	$whr .= " and a.city_id='$cityFilterId' ";
		if ($reasonFilterIds!="") $whr .= " and a.reason_id in ($reasonFilterIds)";
		
		$uptdMainQry = "";
		if ($invoiceFilterId) {
			$whr .= " and dai.invoice_id='$invoiceFilterId' ";
			$uptdMainQry = "left join t_distributor_ac_invoice dai on a.id=dai.dist_ac_id";
			$whr .= " and a.pmt_type!='M'";
		} else $whr .= " and a.pmt_type!='A'";

		if (!$filterType || $filterType=="VE") $whr .= " and (a.value_date!='0000-00-00')";
		else if ($filterType=="PE") $whr .= " and (a.value_date='0000-00-00' or a.value_date is null)";
								
		if ($filterType=="PE")  $orderBy = "a.select_date asc, a.id asc";
		else $orderBy = "a.value_date asc, a.id asc";

		$groupBy	= "a.cod";
		//$orderBy 	= "a.select_date asc, a.id asc";		
		
		$qry = "select a.cod, sum(a.amount) from (t_distributor_ac a, m_distributor b) left join m_city mc on a.city_id=mc.id left join m_common_reason mcr on mcr.id=a.reason_id $uptdMainQry";

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;		

		//echo "Grand total Amt==<br>$qry";

		$result	= $this->databaseConnect->getRecords($qry);
		$codArr = array();
		foreach ($result as $r) {
			$cod = $r[0];
			$amt = $r[1];
			$codArr[$cod] = $amt;
		}
		return (sizeof($codArr)>0)?array($codArr['D'], $codArr['C']):array();
	}
	
	# find rec
	function find($distributorAccountId)
	{		
		$qry = "select id, select_date, distributor_id, amount, cod, description, entry_type, payment_mode, chq_rtgs_no, chq_date, bank_name, account_no, branch_location, deposited_ac_no, value_date, bank_charge, bank_charge_descr, reason_id, other_reason, city_id, chq_return_bank_charge, penalty_charge, chq_return, chq_dist_ac_id, pmt_type, parent_ac_id, so_id, debit_amt, credit_amt, deposited_bank_ac_id, dist_bank_ac_id from t_distributor_ac where id = '$distributorAccountId'";

		//echo "<br>".$qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Delete a Rec (USING SALES ORDER)
	function deleteDistributorAccount($distributorAccountId)
	{
		$qry	= " delete from t_distributor_ac where id='$distributorAccountId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}	

	# Update  a  Record
	function updateDistributorAccount($distributorAccountId, $selDate, $selDistributor, $amount, $debit, $amtDescription)
	{
		$qry = "update t_distributor_ac set select_date='$selDate', distributor_id='$selDistributor', amount='$amount', cod='$debit', description='$amtDescription' where id='$distributorAccountId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	/****
		Update amount in Master 
		According to Ledger a/c 
			Debit -> Sales
			credit -> Receipt
		If Credit the Out amount will decrease
		If Debit the Out amount will Increase
			Account         Type Debit          Credit
			Assets           Increases          Decreases
			Liabilities      Decreases          Increases
			Income           Decreases          Increases
			Expenses         Increases          Decreases
	****/
	function manageDistributorAccount($selDistributor, $cod, $amount)
	{
		if ($cod=='C' && $amount>0) $updateField = "amount = amount-$amount";
		else if ($cod=='D') $updateField = "amount = amount+$amount";

		$qry = " update m_distributor set $updateField where id='$selDistributor'";

		//echo "<br>manageDistributorAccount==$qry";
		$result	= 	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;		
	}

	# When edit/ delete a transaction the amount posting will reverse (USING in Sales Order)	
	function updateDistributorAmt($selDistributor, $cod, $amount)
	{
		if ($cod=='C' && $amount>0) $updateField = "amount = amount+$amount";
		else if ($cod=='D') $updateField = "amount = amount-$amount";

		$qry = " update m_distributor set $updateField where id='$selDistributor'";

		//echo "<br>updateDistributorAmt = $qry";
		$result	= 	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;			
	}

	# When delete
	function getDistributorAccountRec($distributorAccountId)
	{
		$qry = " select distributor_id, amount, cod, so_id, claim_id from t_distributor_ac where id='$distributorAccountId' ";
		//echo $qry;
		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4]):array();
	}

	# Update Sales Order Rec
	function updateSOPaymentStatus($salesOrderId)
	{
		$qry = " update t_salesorder set status_id='', complete_status='' where id=$salesOrderId";
		//echo "<br>SO==>$qry<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateClaimPaymentStatus($claimId)
	{
		$qry = "update t_claim set status_id='', complete_status='' where id='$claimId'";		
		//echo "<br>Claim==>$qry<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	

	# Get Distributor Recs (From Sales Order Recs)	
	function getDistributorList($fromDate, $tillDate)
	{
		/*
		$whr = " a.select_date>='$fromDate' and a.select_date<='$tillDate'";		
		$groupBy	= " a.distributor_id";
		$orderBy	= " b.name asc ";		
		$qry = " select a.distributor_id, b.name from t_distributor_ac a join m_distributor b on a.distributor_id=b.id";
		*/

		$whr = " a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.so!=0";
		
		$groupBy	= " a.distributor_id";
		$orderBy	= " b.name asc ";
		
		$qry = " select a.distributor_id, b.name from t_salesorder a join m_distributor b on a.distributor_id=b.id";
		
		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($groupBy!="")	$qry	.= " group by ".$groupBy;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
				
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
			}
		}
		return $resultArr;	
	}

	function getInvoiceRecs($distributorId=null, $cityId=null, $filter=null, $reasonType=null, $mode=null)
	{
		
		/*
		$whr = "so!=0 ";
		if ($distributorId) $whr .= " and distributor_id='$distributorId' ";
		if ($cityId) $whr .= " and city_id='$cityId' ";		
		$orderBy = " invoice_date desc";
		$qry = " select id, so from t_salesorder";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		*/


		$whr = "tso.so!=0";

		if ($distributorId) $whr .= " and tso.distributor_id='$distributorId' ";
		//if ($cityId) $whr .= " and tso.city_id='$cityId' ";		
		if ($mode) $whr .= " and (tdac.debit_amt-tdac.credit_amt)>0 ";
		
		$orderBy = " GetFY(tso.invoice_date) desc, tso.invoice_date desc";

		$qry = " select tso.id, tso.so as invNum, GetFY(tso.invoice_date) as fy from t_distributor_ac tdac left join t_salesorder tso on tdac.so_id=tso.id";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		if ($filter!="") $resultArr = array(''=>'--Select All--');
		else $resultArr = array(''=>'--Select--');
		if (sizeof($result)>0) {
			$prevFY = "";
			$i=0;
			foreach ($result as $rec) {
				$fy = $rec[2];
				if ($prevFY != $fy) {
					$resultArr["FY_".$i] = "---$fy---"; 	
					$i++;
				}
				$resultArr[$rec[0]] = $rec[1];

				$prevFY = $fy;
			}
			$resultArr['ADV'] = "ADVANCE"; // Advance Allocation
		}
		return $resultArr;
	}


	# get Selected Ref invoice
	function getSelReferenceInvoice($distributorAccountId)
	{
		$qry = "select id, invoice_id from t_distributor_ac_invoice where dist_ac_id='$distributorAccountId' ";
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array();
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
			}
		}

		$advPmtRecs = $this->splitupAdvPmtRecs($distributorAccountId);	
		if (sizeof($advPmtRecs)>0) {
			foreach ($advPmtRecs as $apr) {
				$resultArr[$apr[1]] = "ADV";
			}
		}
		return $resultArr;
	}

	# get sel Chk list
	function getSelChkListRecs($distributorAccountId)
	{
		$qry = "select id, chk_list_id from t_distributor_ac_chk_list where dist_ac_id='$distributorAccountId' ";
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

	# Update Payment Received recs
	function updateDistAccountRecs($distributorAccountId, $entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $commonReason, $otherReason, $verified, $debit, $amount, $description, $selCity, $commonReason, $otherReason, $pmtType, $distBankAccount)
	{		
		$qry = "update t_distributor_ac set entry_type='$entryType', select_date='$selDate', distributor_id='$selDistributor', payment_mode='$paymentMode', chq_rtgs_no='$chqRtgsNo', chq_date='$chqDate', bank_name='$bankName', account_no='$accountNo', branch_location='$branchLocation', deposited_ac_no='$depositedBankAccount', value_date='$valueDate', bank_charge='$bankCharges', bank_charge_descr='$bankChargeDescription', reason_id='$commonReason', other_reason='$otherReason', confirmed='$verified', cod='$debit', amount='$amount', description='$description', city_id='$selCity', reason_id='$commonReason', other_reason='$otherReason', pmt_type='$pmtType', deposited_bank_ac_id='$depositedBankAccount', dist_bank_ac_id='$distBankAccount', edited_by=0, edited_time=0 where id='$distributorAccountId'";
		//echo $qry;

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Del Ref Invoice (USING Sales Order and report)
	function delRefInvoiceRecs($distributorAccountId)
	{
		$qry	= " delete from t_distributor_ac_invoice where dist_ac_id='$distributorAccountId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update other payment modes
	function updateCODDistAccount($distributorAccountId, $selDate, $selDistributor, $amount, $debit, $description, $commonReason, $otherReason, $entryType, $verified, $selCity, $chqReturnBankCharge, $penaltyCharge, $valueDate, $pmtType)
	{
		$qry = "update t_distributor_ac set select_date='$selDate', distributor_id='$selDistributor', amount='$amount', cod='$debit', description='$description', reason_id='$commonReason', other_reason='$otherReason', entry_type='$entryType', confirmed='$verified', city_id='$selCity', chq_return_bank_charge='$chqReturnBankCharge', penalty_charge='$penaltyCharge', value_date='$valueDate', pmt_type='$pmtType', edited_by=0, edited_time=0 where id='$distributorAccountId'";
		//echo $qry;

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# delete Chk list (USING SALES ORDER)
	function delChkList($distributorAccountId)
	{
		$qry	= " delete from t_distributor_ac_chk_list where dist_ac_id='$distributorAccountId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getBankChargeRec($distAccountId)
	{
		$qry 	= "select id, cod, amount from t_distributor_ac where parent_ac_id='$distAccountId' and post_type='PRBC' order by parent_ac_id asc";
		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):"";
	}

	# Update Dist AC Bank Charge 
	function updateDistACBankCharge($bankChargeRecId, $bankCharges, $bankChargeDescription, $chargesPostType, $valueDate, $verified)
	{
		$qry = "update t_distributor_ac set amount='$bankCharges', description='$bankChargeDescription', post_type='$chargesPostType', value_date='$valueDate', confirmed='$verified' where id='$bankChargeRecId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function delBankCharge($bankChargeRecId)
	{
		$qry	= " delete from t_distributor_ac where id='$bankChargeRecId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	function uptdBankChargeConfirm($bankChargeRecId, $verified)
	{
		$qry = "update t_distributor_ac set confirmed='$verified' where id='$bankChargeRecId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function getParentId($distributorAccountId)
	{
		$qry = "select parent_ac_id from t_distributor_ac where id='$distributorAccountId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	function unsetBankCharge($parentId)
	{
		$qry = "update t_distributor_ac set bank_charge='0', bank_charge_descr='' where id='$parentId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Dist City Recs
	function distributorCityRecs($distributorId, $filter)
	{
		$qry = " select distinct b.city_id, c.name from m_distributor_state a, m_distributor_city b, m_city c where a.id=b.dist_state_entry_id and b.city_id=c.id and a.distributor_id='$distributorId' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>1) {
			if ($filter!="") $resultArr = array(''=>'-- Select All--');
			else $resultArr = array(''=>'-- Select --');
		}
		else if (sizeof($result)==1) {
			if ($filter!="") $resultArr = array(''=>'-- Select All--');
			else $resultArr = array();
		} else {
			if ($filter!="") $resultArr = array(''=>'-- Select All--');
			else $resultArr = array(''=>'-- Select --');
		}

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get City Recs
	function getCityRec($selDistributorId)
	{
		$qry = " select distinct c.city_id, d.name from m_distributor a, m_distributor_state b, m_distributor_city c, m_city d where a.id=b.distributor_id and b.id=c.dist_state_entry_id and c.city_id=d.id and a.id='$selDistributorId' order by d.name asc ";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);		
		return $result;
	}

	function DefaultReasonEntry($commonReasonId)
	{
		$qry = "select id from m_common_reason where id='$commonReasonId' and default_entry='Y' and de_code='PR'";

		$result = $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?"PR":"";
	}

	function getRefInvoices($distributorAccountId)
	{
		$qry = "select daci.id, tso.so, daci.invoice_id from t_distributor_ac_invoice daci join t_salesorder tso on daci.invoice_id=tso.id where daci.dist_ac_id='$distributorAccountId'";
		return $this->databaseConnect->getRecords($qry);
	}

	# Get Outstanding amount
	function getOutStandingAmt($fromDate, $tillDate, $selDistributorId)
	{
		/*
		and a.select_date>= '$fromDate' and a.select_date<= '$tillDate'
		and a1.select_date>= '$fromDate' and a1.select_date<= '$tillDate'
		*/
		$qry = "select sum(dAmt) as debitAmt, sum(cAmt) as creditAmt, openAmt, outAmt, (sum(dAmt)-sum(cAmt))+openAmt as outStandAmount from
		( 
			select a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, b.opening_bal as openAmt from t_distributor_ac a, m_distributor b where a.distributor_id=b.id  and a.cod='D' and a.distributor_id='$selDistributorId' and (a.value_date is not null and a.value_date!='0000-00-00') and a.pmt_type!='M' group by a.distributor_id
		union
			select a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, b1.opening_bal as openAmt from t_distributor_ac a1, m_distributor b1 where a1.distributor_id=b1.id and a1.cod='C' and a1.distributor_id='$selDistributorId' and (a1.value_date is not null and a1.value_date!='0000-00-00') and a1.pmt_type!='M' group by a1.distributor_id
		) 
	 	as X group by distributor_id";

		$rec = $this->databaseConnect->getRecord($qry);
		/* If Amt +ve Debit / Credit*/
		//echo "<br>$qry<br>";	
		return (sizeof($rec)>0)?$rec[4]:0;
	}

	# Check List
	function distChkList($distributorAccountId)
	{
		$qry = "select tacl.id, mrc.name, mrc.required from t_distributor_ac_chk_list tacl join m_common_reason_chk mrc on mrc.id=tacl.chk_list_id where tacl.dist_ac_id='$distributorAccountId'";
		//echo "<br>$qry<br>";

		$result = $this->databaseConnect->getRecords($qry);
		
		$displayHtml = "";
		if (sizeof($result)>0) {
			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";		
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
			$displayHtml .= "<td class=listing-head>Check List</td>";
			$displayHtml .= "<td class=listing-head>Required</td>";
			$displayHtml .= "</tr>";
			foreach ($result as $clr) {
				$chkListName = $clr[1];
				$required = $clr[2];

				$displayHtml .= "<tr bgcolor=#fffbcc>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $chkListName;
				$displayHtml .= "</td>";
				$displayHtml .= "<td class=listing-item align=center>";
				$displayHtml .= ($required=='Y')?'YES':'NO';
				$displayHtml .=	"</td>";
				$displayHtml .= "</tr>";	
			}
			$displayHtml  .= "</table>";
		}

		// ------------------------ Invoice Split up section ----------------
		# Get Invoice Split up
		$dar = $this->find($distributorAccountId);
		$pmtType = $dar[24];
		$commonReasonId = $dar[17];
		$reasonType = $this->defaultReasonType($commonReasonId);

		if ($pmtType=='M') {
			# Split up invoice recs
			$invoiceRecs = $this->splitupInvRecs($distributorAccountId);
			$advPmtRecs = $this->splitupAdvPmtRecs($distributorAccountId);
			if (sizeof($invoiceRecs)>0) {
				
				$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";	
				$displayHtml .= "<tr bgcolor=#fffbcc align=center><td colspan=\'2\' class=listing-head>Amt Allocation</td></tr>";	
				$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
				$displayHtml .= "<td class=listing-head>Inv No</td>";
				$displayHtml .= "<td class=listing-head>Amt</td>";
				$displayHtml .= "</tr>";
				foreach ($invoiceRecs as $invr) {
					$invNum 	= $invr[0];
					$splitupAmt 	= $invr[1];

					$displayHtml .= "<tr bgcolor=#fffbcc>";
					$displayHtml .= "<td class=listing-item nowrap>";
					$displayHtml .= $invNum;
					$displayHtml .= "</td>";
					$displayHtml .= "<td class=listing-item align=right>";
					$displayHtml .= $splitupAmt;
					$displayHtml .=	"</td>";
					$displayHtml .= "</tr>";
				}
				
				foreach ($advPmtRecs as $invr) {					
					$splitupAdvAmt 	= $invr[0];

					$displayHtml .= "<tr bgcolor=#fffbcc>";
					$displayHtml .= "<td class=listing-item nowrap>Adv. Amt";					
					$displayHtml .= "</td>";
					$displayHtml .= "<td class=listing-item align=right>";
					$displayHtml .= $splitupAdvAmt;
					$displayHtml .=	"</td>";
					$displayHtml .= "</tr>";
				}

				$displayHtml  .= "</table>";
			}
		}

		# If Allocation show the total value and break up
		if ($pmtType=='A') {
			$parentACId = $dar[25];	
			# Split up invoice recs
			$invoiceRecs = $this->splitupInvRecs($parentACId);
			if (sizeof($invoiceRecs)>0) {
				$pacr = $this->find($parentACId);
				$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";	
				$displayHtml .= "<tr bgcolor=#fffbcc align=center><td colspan=\'2\' class=listing-head>Received Total Amt:&nbsp;".$pacr[3]."</td></tr>";	
				$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
				$displayHtml .= "<td class=listing-head>Inv No</td>";
				$displayHtml .= "<td class=listing-head>Amt</td>";
				$displayHtml .= "</tr>";
				foreach ($invoiceRecs as $invr) {
					$invNum 	= $invr[0];
					$splitupAmt 	= $invr[1];

					$displayHtml .= "<tr bgcolor=#fffbcc>";
					$displayHtml .= "<td class=listing-item nowrap>";
					$displayHtml .= $invNum;
					$displayHtml .= "</td>";
					$displayHtml .= "<td class=listing-item align=right>";
					$displayHtml .= $splitupAmt;
					$displayHtml .=	"</td>";
					$displayHtml .= "</tr>";
				}
				$displayHtml  .= "</table>";
			}
		}
		// ------------------------ Invoice Split up section Ends here----------------	
		if ($reasonType=='SI') {
			$soDebitAmt 	= $dar[27];
			$soCreditAmt	= $dar[28];
			//echo "<br>$soDebitAmt-$soCreditAmt";
			$balDueAmt 	= $soDebitAmt-$soCreditAmt;

			$soInvId = $dar[26];
			$distACRecs = $this->getDistACInvRecs($soInvId);
			if (sizeof($distACRecs)>0) {				
				$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";
				$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
					$displayHtml .= "<td class=listing-head>DATE</td>";
					$displayHtml .= "<td class=listing-head>REASON</td>";
					$displayHtml .= "<td class=listing-head>DEBIT AMT</td>";
					$displayHtml .= "<td class=listing-head>CREDIT AMT</td>";
				$displayHtml .= "</tr>";
				$totalCreditAmt = 0;
				$totalDebitAmt = 0;
				foreach ($distACRecs as $dir) {
					$selEntryDate 		= $dir[1];
					$trValueDate	= ($dir[7]!="0000-00-00")?dateFormat($dir[7]):"";

					$amount			= $dir[3];
					$cod			= $dir[4];					
					$creditAmt = 0;
					$debitAmt  = 0;	
					if ($cod=="C")  {
						$creditAmt = number_format(abs($amount),2,'.','');
						$totalCreditAmt += abs($creditAmt);				
					} else if ($cod=="D") {
		 				$debitAmt = number_format(abs($amount),2,'.','');
						$totalDebitAmt += abs($debitAmt);			
					}
					$chequeReturnStatus 	= $dir[8];							
					$dacChargeType  = $dir[9];
					$deReasonType    = $dir[12];
					$selCommonReasonId 	= $dir[10];
					$otherReasonDetails 	= $dir[11];

					$selReasonName = "";
					if ($dacChargeType=="PRBC" || $dacChargeType=="CRBC") 	$selReasonName = "BANK CHARGES"; 			
					else if ($dacChargeType=="CRPC") $selReasonName = "PENALTY CHARGES"; 
					else if ($trValueDate!="" && $chequeReturnStatus=='N' && $deReasonType=='PR') $selReasonName = "PAYMENT RECEIVED"; 
					else if ($selCommonReasonId==0 && $otherReasonDetails!="") $selReasonName = $otherReasonDetails;
					else $selReasonName	= $dir[13];


					$displayHtml .= "<tr bgcolor=#fffbcc>";
						$displayHtml .= "<td class=listing-item nowrap align=center style=\'padding-left:5px;padding-right:5px;\'>";
						$displayHtml .= dateFormat($selEntryDate);
						$displayHtml .= "</td>";
						$displayHtml .= "<td class=listing-item align=left style=\'padding-left:5px;padding-right:5px;\'>";
						$displayHtml .= $selReasonName;
						$displayHtml .=	"</td>";
						$displayHtml .= "<td class=listing-item align=right style=\'padding-left:5px;padding-right:5px;\'>";
						$displayHtml .= ($debitAmt!=0)?$debitAmt:"";
						$displayHtml .=	"</td>";
						$displayHtml .= "<td class=listing-item align=right style=\'padding-left:5px;padding-right:5px;\'>";
						$displayHtml .= ($creditAmt!=0 )?$creditAmt:"";
						$displayHtml .=	"</td>";
					$displayHtml .= "</tr>";
				} 
				// Loop ends here
				$displayHtml .= "<tr bgcolor=#fffbcc>";
					$displayHtml .= "<td class=listing-head colspan=2 align=right style=\'padding-left:5px;padding-right:5px;\'>Total:</td>";				
					$displayHtml .= "<td class=listing-item align=right style=\'padding-left:5px;padding-right:5px;\'><strong>";
					$displayHtml .= ($totalDebitAmt!=0 )?number_format($totalDebitAmt,2,'.',','):"";
					$displayHtml .=	"</strong></td>";
					$displayHtml .= "<td class=listing-item align=right style=\'padding-left:5px;padding-right:5px;\'><strong>";	
					$displayHtml .= ($totalCreditAmt!=0)?number_format($totalCreditAmt,2,'.',','):"";
					$displayHtml .=	"</strong></td>";
				$displayHtml .= "</tr>";

				if ($balDueAmt!=0) {
					$displayHtml .= "<tr bgcolor=#fffbcc>";
						$displayHtml .= "<td class=listing-head colspan=2 align=right style=\'padding-left:5px;padding-right:5px;\'>Balance Due Amt:</td>";				
						$displayHtml .= "<td class=listing-item align=left style=\'padding-left:5px;padding-right:5px;\' colspan=2><strong>";
						$displayHtml .= ($balDueAmt!=0)?number_format($balDueAmt,2,'.',','):"";
						$displayHtml .=	"</strong></td>";					
					$displayHtml .= "</tr>";
				} 
				// Bal Due Amt

				$displayHtml  .= "</table>";
			}
			
		}	

		return array($result, $displayHtml);
	}

	# Get Split up invoice
	function splitupInvRecs($distributorAccountId)
	{
		$qry = "select tso.so, da.amount from t_distributor_ac da join t_distributor_ac_invoice daci on da.id=daci.dist_ac_id join t_salesorder tso on daci.invoice_id=tso.id where da.post_type is null and da.parent_ac_id='$distributorAccountId' order by tso.so asc";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function splitupAdvPmtRecs($distributorAccountId)
	{
		$qry = "select da.amount, da.id from t_distributor_ac da where da.post_type is null and da.adv_entry_parent_id='$distributorAccountId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	# Distributor ac invoice recs
	# Query Modified on 25 Oct 10: and (mcr.de_code!='SI' or mcr.de_code is null)
	function getDistACInvRecs($invoiceId)
	{
		$qry = "select 
				tdac.id, (if (tdac.value_date!='0000-00-00', tdac.value_date, if (tdac.chq_date!='0000-00-00', tdac.chq_date, tdac.select_date))) as selDate, tdac.distributor_id, tdac.amount, tdac.cod, tdac.so_id, tdaci.invoice_id, tdac.value_date, tdac.chq_return, tdac.post_type, tdac.reason_id, tdac.other_reason, mcr.de_code as reasonCode, mcr.reason as reasonName
			from 
				t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id join m_common_reason mcr on mcr.id=tdac.reason_id 
			where 
				tdac.value_date!='0000-00-00' and tdaci.invoice_id='$invoiceId' and tdac.pmt_type!='M'
			order by tdac.value_date asc, mcr.de_code asc, tdac.id asc ";

		//echo "Dist AC Inv:$invoiceId=<br>$qry<br>";

		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# PAYMENT RECEIVED - PR
	# SALES INVOICE - SI
	# Return coomon reason id, Credit/Debit, reason	
	function defaultCommonReason($code)
	{
		$qry = "select id, cod, reason from m_common_reason where de_code='$code' ";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?array($rec[0][0], $rec[0][1], $rec[0][2]):array();
	}


	# get City List from Sales Order
	function getCityFilterList($fromDate, $tillDate, $distributorFilterId)
	{
		/*
		$whr = " a.select_date>='$fromDate' and a.select_date<='$tillDate'";		
		if ($distributorFilterId!="") $whr .= " and a.distributor_id='$distributorFilterId'";
		$groupBy	= " a.city_id";
		$orderBy	= " b.name asc ";
		$qry = " select a.city_id, b.name from t_distributor_ac a join m_city b on a.city_id=b.id";
		*/
		
		$whr = " a.invoice_date>='$fromDate' and a.invoice_date<='$tillDate' and a.so!=0";
		if ($distributorFilterId!="") $whr .= " and a.distributor_id='$distributorFilterId'";
		$groupBy	= " a.city_id";
		$orderBy	= " b.name asc ";
		$qry = " select a.city_id, b.name from t_salesorder a join m_city b on a.city_id=b.id";

		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($groupBy!="")	$qry	.= " group by ".$groupBy;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
				
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		
		if (sizeof($result)>1) $resultArr = array(''=>'--Select All--');
		else $resultArr = array();		
		//$resultArr = array(''=>'--Select All--');
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
			}
		}
		return $resultArr;	
	}

	# Check Return
	function chequeReturnEntry($commonReasonId)
	{
		$qry = "select id from m_common_reason where id='$commonReasonId' and default_entry='Y' and de_code='CR'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?"CR":"";
	}

	# (using in Sales Order)
	function getSubEntryRecs($distAccountId)
	{
		$qry 	= "select id, cod, amount from t_distributor_ac where parent_ac_id='$distAccountId' order by parent_ac_id asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	# Get Pending cheque
	function getPendingCheques($distributorId, $cityId, $ajax=null)
	{
		$qry = "select id, chq_rtgs_no from t_distributor_ac where distributor_id='$distributorId' and city_id='$cityId' and (value_date is null or value_date='0000-00-00') and chq_rtgs_no is not null";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		if ($ajax) $resultArr = array(''=>'--Select--');
		else $resultArr = array();
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
			}
		}
		return $resultArr;
	}

	# Update Pending cheque
	function updatePendingCheque($distributorAccountId, $selDate)
	{
		$qry = "update t_distributor_ac set chq_return='Y', value_date='$selDate', confirmed='Y' where id='$distributorAccountId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update Cheque return status
	function updateParentDistACEntry($distributorAccountId)
	{
		$qry = "update t_distributor_ac set chq_return='N', value_date='0000-00-00' where id='$distributorAccountId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Invoice Filtered List (From Sales  Order)
	function getInvoiceFilteredList($fromDate, $tillDate, $distributorId, $cityId)
	{
		/*
		$whr = " tda.select_date>='$fromDate' and tda.select_date<='$tillDate' and tda.distributor_id='$distributorId' ";		
		if ($cityId) $whr .= " and tda.city_id='$cityId' ";
		$groupBy	= " daci.invoice_id";
		$orderBy	= " tso.so asc ";		
		$qry = "select tso.id, tso.so as invNum from t_distributor_ac tda join t_distributor_ac_invoice daci on tda.id=daci.dist_ac_id join t_salesorder tso on daci.invoice_id=tso.id ";
		*/
		
		$whr = " tso.invoice_date>='$fromDate' and tso.invoice_date<='$tillDate' and tso.distributor_id='$distributorId' and tso.so!=0 ";

		if ($cityId) $whr .= " and tso.city_id='$cityId' ";
		//$groupBy	= " daci.invoice_id";
		$orderBy	= " GetFY(tso.invoice_date) desc, tso.invoice_date desc, tso.so asc ";
		
		$qry = "select tso.id, tso.so as invNum, GetFY(tso.invoice_date) as fy from t_salesorder tso ";

		if ($whr!="") 		$qry 	.= " where ".$whr;
		if ($groupBy!="")	$qry	.= " group by ".$groupBy;
		if ($orderBy!="") 	$qry 	.= " order by ".$orderBy;
		//echo "<br>$qry";		

		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');		
		if (sizeof($result)>0) {
			$prevFY = "";
			$i = 0;
			foreach ($result as $rec) {
				$fy = $rec[2];

				if ($prevFY != $fy) {
					$resultArr["FY_".$i] = "---$fy---"; 	
					$i++;
				}
				$resultArr[$rec[0]] = $rec[1];

				$prevFY = $fy;
			}
		}
		return $resultArr;
	}

	# Chk ref inv assign
	function chkRefInvAssignStatus($fromDate, $tillDate, $distributorFilterId, $cityFilterId)
	{
		$whr = " tdac.select_date>='$fromDate' and tdac.select_date<='$tillDate' and tdaci.invoice_id is null ";

		if ($distributorFilterId) 	$whr .= " and tdac.distributor_id='$distributorFilterId' ";
		if ($cityFilterId)		$whr .= " and tdac.city_id='$cityFilterId' ";

		$qry = "select tdac.id from t_distributor_ac tdac left join t_distributor_ac_invoice tdaci on tdaci.dist_ac_id=tdac.id ";
		if ($whr) $qry .= " where ".$whr;
		//echo "<br>$qry<br>";	

		$result	= $this->databaseConnect->getRecords($qry); 
		return (sizeof($result)>0)?true:false;
	}
	

	# Get D Ac Invoice Main entry
	function getDACInvoiceMainEntry($distributorId, $invoiceId)
	{
		$qry = "select tdac.id from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id join m_common_reason mcr on mcr.id=tdac.reason_id where tdac.distributor_id='$distributorId' and tdaci.invoice_id='$invoiceId' and mcr.de_code='SI' ";
		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Update Credit Amt / Debit Amt in distributor acc
	function updateInvDNCAmt($distAccountId, $cod, $amount, $rollback=null)
	{
		if ($rollback) {
			# Rollback AC
			if ($cod=='C' && $amount>0) $updateField = "credit_amt = credit_amt-$amount";
			else if ($cod=='D') $updateField = "debit_amt = debit_amt-$amount";
		} else {
			if ($cod=='C' && $amount>0) $updateField = "credit_amt = credit_amt+$amount";
			else if ($cod=='D') $updateField = "debit_amt = debit_amt+$amount";
		}

		$qry = " update t_distributor_ac set $updateField where id='$distAccountId'";

		//echo "<br>Distributor Account Tra==<br>$qry";
		$result	= 	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Default Reason type
	function defaultReasonType($commonReasonId)
	{
		$qry = "select de_code from m_common_reason where id='$commonReasonId' and default_entry='Y'";
		//echo "<br>$qry";

		$result = $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Get SO Value
	function getInvValue($invoiceId)
	{
		$qry = "select sum(tso.grand_total_amt+tso.round_value) as totalSOAmt, dispatch_date from t_salesorder tso where tso.id='$invoiceId' group by tso.id ";
		$result = $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?array($result[0][0], $result[0][1]):array();
	}

	function addDistACRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $userId, $debit, $amount, $description, $selCity, $commonReason, $otherReason, $pmtType, $parentACId, $verified, $distBankAccount)
	{
		$qry = "insert into t_distributor_ac (entry_type, select_date, distributor_id, payment_mode, chq_rtgs_no, chq_date, bank_name, account_no, branch_location, deposited_ac_no, value_date, bank_charge, bank_charge_descr, created, createdby, cod, amount, description, city_id, reason_id, other_reason, pmt_type, parent_ac_id, confirmed, deposited_bank_ac_id, dist_bank_ac_id) values ('$entryType', '$selDate', '$selDistributor', '$paymentMode', '$chqRtgsNo', '$chqDate', '$bankName', '$accountNo', '$branchLocation', '$depositedBankAccount', '$valueDate', '$bankCharges', '$bankChargeDescription', NOW(), '$userId', '$debit', '$amount', '$description', '$selCity', '$commonReason', '$otherReason', '$pmtType', '$parentACId', '$verified', '$depositedBankAccount', '$distBankAccount')";
		//echo "<br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Upate Distributor Account 
			$this->manageDistributorAccount($selDistributor, $debit, $amount);
		}
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Multiple Ref Inv amt
	function multipleRefInvAmt($distAccountId, $invoiceId, $pmtType)
	{
		/*
		$qry = "select tdac.id, tdac.amount from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id where tdac.parent_ac_id='$distAccountId' and tdaci.invoice_id='$invoiceId' and (tdac.post_type!='PRBC' or tdac.post_type is null) ";
		*/

		$whr = " (tdac.post_type!='PRBC' or tdac.post_type is null) and tdaci.invoice_id='$invoiceId' ";

		if ($pmtType=='M') $whr .= " and tdac.parent_ac_id='$distAccountId'";
		else $whr .= " and tdaci.dist_ac_id='$distAccountId'";

		$qry = "select tdac.id, tdac.amount from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id";

		if ($whr!="") $qry .= " where ".$whr;

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?array($result[0][0],$result[0][1]):array();
	}

	# Get invoice Pending Amt
	function pendingAmt($invoiceId)
	{
		$qry = "select (debit_amt-credit_amt) as pendingAmt from t_distributor_ac where so_id='$invoiceId'";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);	
		return (sizeof($result)>0)?$result[0][0]:"";
	}
	
	function distACSingleRefInv($distAccountId)
	{
		$qry = "select invoice_id from t_distributor_ac_invoice where dist_ac_id='$distAccountId'";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);	
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# get Extra charge applied invoice
	function getExtraChargeAppliedInv($distAccountId, $invoiceId)
	{
		$whr = " tdac.post_type is not null and tdaci.invoice_id='$invoiceId' and tdac.parent_ac_id='$distAccountId'";

		$qry = "select tdac.post_type from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id";

		if ($whr!="") $qry .= " where ".$whr;

		//echo "<br>$qry<br>";

		$result = $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Modified Time Updating
	function chkDistACRecModified($distAccountId)
	{
		$qry = " select a.edited_by, b.username from t_distributor_ac a, user b  where a.edited_by=b.id and a.id='$distAccountId' and a.edited_by!=0 ";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][1]:false;
	}

	function updateDistACPModifiedRec($distAccountId, $userId, $mode)
	{
		if ($mode=='E') $uptdQry = "edited_time=NOW()";
		else $uptdQry = "edited_time=0";
		
		$qry = " update t_distributor_ac set edited_by='$userId', $uptdQry where id=$distAccountId";
		//echo "<br>$qry<br>";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateAllDistAC()
	{
		$qry = " update t_distributor_ac set edited_by=0, edited_time=0 ";
		//echo "<br>$qry<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function fetchAllInvoiceRecs($distributorId=null, $cityId=null, $filter=null, $reasonType=null, $mode=null)
	{
		$whr = "tso.so!=0";

		if ($distributorId) $whr .= " and tso.distributor_id='$distributorId' ";
		//if ($cityId) $whr .= " and tso.city_id='$cityId' ";		
		if ($mode) $whr .= " and (tdac.debit_amt-tdac.credit_amt)>0 ";
		
		$orderBy = " GetFY(tso.invoice_date) desc, tso.invoice_date desc";

		$qry = " select tso.id, tso.so as invNum, GetFY(tso.invoice_date) as fy from t_distributor_ac tdac left join t_salesorder tso on tdac.so_id=tso.id";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "Fetch ALL=<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return sizeof($result);
	}

	function addDistAcBalAdvPmtRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $userId, $debit, $amount, $description, $selCity, $commonReason, $otherReason, $pmtType, $advPmtStatus, $distBankAccount, $parentPaymentEntryId)
	{
		$qry = "insert into t_distributor_ac (entry_type, select_date, distributor_id, payment_mode, chq_rtgs_no, chq_date, bank_name, account_no, branch_location, deposited_ac_no, value_date, bank_charge, bank_charge_descr, created, createdby, cod, amount, description, city_id, reason_id, other_reason, pmt_type, adv_pmt, deposited_bank_ac_id, dist_bank_ac_id, adv_entry_parent_id) values ('$entryType', '$selDate', '$selDistributor', '$paymentMode', '$chqRtgsNo', '$chqDate', '$bankName', '$accountNo', '$branchLocation', '$depositedBankAccount', '$valueDate', '$bankCharges', '$bankChargeDescription', NOW(), '$userId', '$debit', '$amount', '$description', '$selCity', '$commonReason', '$otherReason', '$pmtType', '$advPmtStatus', '$depositedBankAccount', '$distBankAccount', '$parentPaymentEntryId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Check Bal Adv Entry exist
	function chkBalAdvPmtEntryExist($distAccountId)
	{
		$qry = "select id from t_distributor_ac where adv_entry_parent_id='$distAccountId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function chkBalAdvPmtEntryConfirmed($distAccountId)
	{
		$qry = "select id from t_distributor_ac where confirmed='Y' and adv_entry_parent_id='$distAccountId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function delRefInvAdvAmt($distributorAccountId)
	{
		$qry	= " delete from t_distributor_ac where adv_entry_parent_id='$distributorAccountId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	/**
	* Distributor Wise Overdue amt
	*/
	function overdueAmt($fromDate, $tillDate, $distributorId)
	{
		$qry = "select if (sum(a.debit_amt-a.credit_amt),sum(a.debit_amt-a.credit_amt),'') as overdueAmt from t_distributor_ac a join m_distributor b on a.distributor_id=b.id left join m_common_reason mcr on mcr.id=a.reason_id where a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.distributor_id='$distributorId' and (a.select_date<=(SELECT DATE_SUB(CURDATE(), INTERVAL b.credit_period DAY)) or mcr.de_code='PR' or mcr.de_code='CR') and a.value_date!='0000-00-00' and (a.debit_amt-a.credit_amt)>0 ";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}
	
}
?>