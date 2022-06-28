<?php
Class SupplierPayments
{
	/****************************************************************
	This class deals with all the operations relating to Supplier  Payments
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function SupplierPayments(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	# Add Supplier Payments
	function addSupplierPayments($supplier, $chequeNo, $amount, $paymentMode, $paymentDate, $bankName, $userId, $payableAt, $paymentMethod, $paymentType, $paymentReason, $accountEntryNo, $dateType, $fromDate, $toDate, $selChallan, $billingCompany, $selSettlementDate)
	{		
		$qry	= " insert into t_supplierpayments (supplier_id, cheque_no, amount, payment_date, payment_mode, bank_name, created, createdby, payable_at, payment_method, payment_type, payment_reason, account_entry_no, date_type, from_date, to_date, challan_nos, billing_company_id, settlement_date) values('$supplier', '$chequeNo', '$amount', '$paymentDate', '$paymentMode', '$bankName', NOW(), '$userId', '$payableAt', '$paymentMethod', '$paymentType', '$paymentReason', '$accountEntryNo', '$dateType', '$fromDate', '$toDate', '$selChallan', '$billingCompany', '$selSettlementDate')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Recs
	function fetchAllRecords()
	{
		$qry	= "select  a.id, a.supplier_id, a.cheque_no, a.amount, a.payment_date, b.id, b.name  from t_supplierpayments a, supplier b where a.supplier_id=b.id";
			
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter table by date (PAGING)
	function supplierPaymentsRecPagingFilter($fromDate, $tillDate, $offset, $limit, $supplierFilterId)
	{
		$whr  = " a.supplier_id=b.id and a.payment_date>='$fromDate' and a.payment_date<='$tillDate' ";

		if ($supplierFilterId!="") $whr .= " and a.supplier_id='$supplierFilterId' ";

		$orderBy  = " a.payment_date asc, b.name asc ";

		$limit = " $offset, $limit ";

		$qry	= " select  a.id, a.supplier_id, a.cheque_no, a.amount, a.payment_date, b.name, a.bank_name, a.payable_at, a.payment_method, a.payment_type, a.account_entry_no from t_supplierpayments a, supplier b ";

		if ($whr!="") 		$qry .= " where ".$whr ; 
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit)		$qry .= " limit ".$limit;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter table by date
	function supplierPaymentsRecFilter($fromDate, $tillDate, $supplierFilterId)
	{
		$whr  = " a.supplier_id=b.id and a.payment_date>='$fromDate' and a.payment_date<='$tillDate' ";

		if ($supplierFilterId!="") $whr .= " and a.supplier_id='$supplierFilterId' ";

		$orderBy  = " a.payment_date asc, b.name asc ";	

		$qry	= " select  a.id, a.supplier_id, a.cheque_no, a.amount, a.payment_date, b.name, a.bank_name, a.payable_at, a.payment_method, a.payment_type, a.account_entry_no  from t_supplierpayments a, supplier b ";

		if ($whr!="") 		$qry .= " where ".$whr ; 
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter table using id
	function find($paymentId)
	{		
		$qry = "select id, supplier_id, cheque_no, amount, payment_date, bank_name, payable_at, payment_method, payment_type, payment_reason, account_entry_no, date_type, from_date, to_date, challan_nos, billing_company_id, settlement_date from t_supplierpayments where id=$paymentId";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	# update Supplier Payment Record
	function updateSupplierPayments($supplierPaymentsId, $supplier, $chequeNo, $amount, $paymentDate, $bankName, $payableAt, $paymentMethod, $paymentType, $paymentReason, $accountEntryNo, $dateType, $fromDate, $toDate, $selChallan, $billingCompany, $selSettlementDate)
	{
		$qry	= " update t_supplierpayments set supplier_id=$supplier, cheque_no='$chequeNo', amount='$amount', payment_date='$paymentDate', bank_name='$bankName', payable_at='$payableAt', payment_method='$paymentMethod', payment_type='$paymentType', payment_reason='$paymentReason', account_entry_no='$accountEntryNo', date_type='$dateType', from_date='$fromDate', to_date='$toDate', challan_nos='$selChallan', billing_company_id='$billingCompany', settlement_date='$selSettlementDate'  where id=$supplierPaymentsId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	# Delete Supplier Payment
	function deleteSupplierPayments($supplierPaymentsId)
	{
		$qry	= " delete from t_supplierpayments where id=$supplierPaymentsId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();		
		return $result;
	}

	#Get Paid Records
	function getPaidRecords($paidSupplierId)
	{
		$qry	= "select id, cheque_no, amount, payment_type from t_supplierpayments where supplier_id='$paidSupplierId'";	
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch Challan billing company Records
	function getBillingCompnyRecs($dateSelectFrom, $fromDate, $tillDate, $selectSupplier, $selSetldDate)
	{			
		$dateSelection = "";		
		if($dateSelectFrom=='SCD') {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";			
			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";			
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	
		}	
		
		$whr = "a.billing_company_id=bc.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.paid='Y' and a.payment_confirm='Y' and a.main_supplier='".$selectSupplier."' $tableJoin";
		
		if ($selSetldDate!="") $whr .= " and b.settlement_date='$selSetldDate' ";

		$orderBy	=	"bc.display_name";
		
		$qry	= "select distinct bc.id, bc.display_name from t_dailycatch_main a, t_dailycatchentry b, m_billing_company bc $tableName";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$resultArr = array(''=>'-- Select--');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		} 
		return $resultArr;
	}

	# get hallan Nos
	function getChallanNos($dateSelectFrom, $fromDate, $tillDate, $selectSupplier, $selSetldDate)
	{		
		$dateSelection = "";
		if ($dateSelectFrom=='SCD') {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";			
			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";			
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	
		}	

		$whr = " a.id=b.main_id and $dateSelection and a.weighment_challan_no is not null and b.paid='Y' and a.payment_confirm='Y' and a.main_supplier='".$selectSupplier."' $tableJoin";
		
		if ($selSetldDate!="") $whr .= " and b.settlement_date='$selSetldDate' ";

		$orderBy  = " a.billing_company_id asc, a.weighment_challan_no asc, a.select_date asc ";
		
		$qry	= "select distinct a.id, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b $tableName";

		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$resultArr = array(''=>'-- Select--');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		} 
		return $resultArr;
	}

	#select distinct settlement dates
	function fetchAllSetldDateRecs($dateSelectFrom, $fromDate, $tillDate, $selectSupplier)
	{	
		$dateSelection = "";
		if ($dateSelectFrom=='SCD') {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";			
			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";			
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	
		}	

		$whr = " a.id=b.main_id and $dateSelection and a.weighment_challan_no is not null and b.paid='Y' and a.payment_confirm='Y' and a.main_supplier='".$selectSupplier."' $tableJoin";
		

		$orderBy  = " b.settlement_date desc ";
		
		$qry	= "select DATE_FORMAT(b.settlement_date,'%d/%m/%Y') from t_dailycatch_main a, t_dailycatchentry b $tableName";

		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$resultArr = array(''=>'-- Select--');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[0];
			}
		} 
		return $resultArr;
	}

	# Sel Supplier other entry rec in the same payment date
	function supplierOtherEntryRecs($paymentDate, $supplierId, $cId)
	{
		$whr  = " a.payment_date='$paymentDate' and a.supplier_id='$supplierId' ";
		if ($cId!="") $whr .= " and id!=$cId ";
		$orderBy  = " a.payment_date desc";

		$qry	= " select  a.id, a.supplier_id, a.cheque_no, a.amount, a.payment_date, a.bank_name, a.payable_at, a.payment_method, a.payment_type, a.account_entry_no, a.payment_reason from t_supplierpayments a ";

		if ($whr!="") 		$qry .= " where ".$whr ; 
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# Get Recs
	function getWtChallanRecs($selectSupplier, $fromDate, $toDate, $selSetldDate)
	{
		$whr = " a.id=b.main_id and a.weighment_challan_no is not null and b.paid='Y' and a.payment_confirm='Y' and a.main_supplier='".$selectSupplier."' and a.select_date>='".$fromDate."' and a.select_date<='".$toDate."' and b.settlement_date='$selSetldDate'";
		
		$groupBy	= " a.payment_by ";
		//$orderBy  = " b.settlement_date desc ";		
		$qry	= "select a.payment_by from t_dailycatch_main a, t_dailycatchentry b ";

		if ($whr!="")	$qry   .=" where ".$whr;
		if ($groupBy)	$qry   .=" group by ".$groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getBillingCompanyRecs($selectSupplier, $fromDate, $toDate, $selSetldDate, $paymentBy)
	{
		$whr = " a.id=b.main_id and a.weighment_challan_no is not null and b.paid='Y' and a.payment_confirm='Y' and a.main_supplier='".$selectSupplier."' and a.select_date>='".$fromDate."' and a.select_date<='".$toDate."' and b.settlement_date='$selSetldDate' and a.payment_by='$paymentBy' ";
		
		$groupBy	= "a.billing_company_id";
		$orderBy  = " c.name asc ";		
		$qry	= "select a.billing_company_id, c.name from (t_dailycatch_main a, t_dailycatchentry b) left join m_billing_company c on a.billing_company_id=c.id ";

		if ($whr!="")	$qry   .=" where ".$whr;
		if ($groupBy)	$qry   .=" group by ".$groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getChllanNos($selectSupplier, $fromDate, $toDate, $selSetldDate, $paymentBy, $billingCompanyId)
	{
		$whr = " a.id=b.main_id and a.weighment_challan_no is not null and b.paid='Y' and a.payment_confirm='Y' and a.main_supplier='".$selectSupplier."' and a.select_date>='".$fromDate."' and a.select_date<='".$toDate."' and b.settlement_date='$selSetldDate' and a.payment_by='$paymentBy' and a.billing_company_id='$billingCompanyId' ";
		
		//$groupBy	= "";
		$orderBy  = " a.billing_company_id asc, a.weighment_challan_no asc, a.select_date asc ";		
		$qry	= "select distinct a.id, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b  ";

		if ($whr!="")	$qry   .=" where ".$whr;
		if ($groupBy)	$qry   .=" group by ".$groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#select distinct settlement dates
	function getSetldDateRecs($selectSupplier, $fromDate, $tillDate)
	{	
		$whr = " a.id=b.main_id and a.weighment_challan_no is not null and b.paid='Y' and a.payment_confirm='Y' and a.main_supplier='".$selectSupplier."' and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		

		$orderBy  = " b.settlement_date desc ";
		
		$qry	= "select DATE_FORMAT(b.settlement_date,'%d/%m/%Y') from t_dailycatch_main a, t_dailycatchentry b $tableName";

		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$resultArr = array(''=>'-- Select--');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[0];
			}
		} 
		return $resultArr;
	}

	# Sel Supplier Advance entry rec in the same payment date
	function supplierAdvanceEntryRecs($paymentDate, $supplierId, $cId)
	{
		//$whr  = " a.payment_date='$paymentDate' and a.supplier_id='$supplierId' ";
		$whr  = " a.supplier_id='$supplierId' and a.payment_type='A'";

		if ($cId!="") $whr .= " and id!=$cId ";
		$orderBy  = " a.payment_date desc";

		$qry	= " select  a.id, a.supplier_id, a.cheque_no, a.amount, a.payment_date, a.bank_name, a.payable_at, a.payment_method, a.payment_type, a.account_entry_no, a.payment_reason from t_supplierpayments a ";

		if ($whr!="") 		$qry .= " where ".$whr ; 
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# Filter table using id
	function getSPRec($paymentId)
	{		
		$qry = "select cheque_no, bank_name, payable_at, payment_method, payment_reason, account_entry_no, createdby from t_supplierpayments where id=$paymentId";
		//echo $qry;		
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6]);
	}

	function updateBalanceAdvanceAmt($advanceEntryId, $balanceAdvanceAmt)
	{
		$qry	= " update t_supplierpayments set amount='$balanceAdvanceAmt'  where id=$advanceEntryId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateAdvanceFullAmt($advanceEntryId, $paymentDate, $selSetldDate)
	{
		//payment_date='$paymentDate',
		$qry	= " update t_supplierpayments set  payment_type='S', settlement_date='$selSetldDate', full_setld_adv='A' where id='$advanceEntryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Add Supplier Payments
	function addPartSetledRec($supplier, $chequeNo, $amount, $paymentMode, $paymentDate, $bankName, $userId, $payableAt, $paymentMethod, $paymentType, $paymentReason, $accountEntryNo, $dateType, $fromDate, $toDate, $selChallan, $billingCompany, $selSettlementDate, $advanceEntryId)
	{		
		$qry	= " insert into t_supplierpayments (supplier_id, cheque_no, amount, payment_date, payment_mode, bank_name, created, createdby, payable_at, payment_method, payment_type, payment_reason, account_entry_no, date_type, from_date, to_date, challan_nos, billing_company_id, settlement_date, part_setld_adv_id) values('$supplier', '$chequeNo', '$amount', '$paymentDate', '$paymentMode', '$bankName', NOW(), '$userId', '$payableAt', '$paymentMethod', '$paymentType', '$paymentReason', '$accountEntryNo', '$dateType', '$fromDate', '$toDate', '$selChallan', '$billingCompany', '$selSettlementDate', '$advanceEntryId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
}	
?>