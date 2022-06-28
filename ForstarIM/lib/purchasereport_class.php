<?php
Class PurchaseReport
{
	/****************************************************************
	This class deals with all the operations relating to Purchase Report
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PurchaseReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	#Filter Records Using Weighment No only
	function fetchAllCatchReportRecords($weighNumber, $billingCompany)
	{
		$qry	=	"select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, a.payment_by, a.confirm, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, a.payment_confirm, a.payment_date, a.report_confirm, CONCAT(a.alpha_code,'-',a.weighment_challan_no)
		from (t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d) left join m_grade e on b.grade_id=e.id where weighment_challan_no='$weighNumber' and a.id=main_id and b.fish=c.id and b.fish_code=d.id and a.billing_company_id='$billingCompany'
		order by c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Supplier Declared Wt Records
	function getSupplierDeclaredWtRecords($weighNumber, $billingCompany)
	{
		$whr		=	"a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no='$weighNumber' and b.fish=c.id and b.fish_code=d.id and a.billing_company_id='$billingCompany'" ;
		
		//$orderBy	=	"c.name asc, d.code asc, b.effective_wt desc ";
		$orderBy	=	"c.name asc, d.code asc, e.decl_count asc ";
		
	
		$qry		=	"select a.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, e.decl_wt, e.rate, e.settled, e.settled_date, a.payment_confirm, a.payment_date, e.id, CONCAT(a.alpha_code,'',a.weighment_challan_no)  from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="")		$qry   .= " where ".$whr;		
		if ($groupBy!="")	$qry   .= " group by ". $groupBy;			
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
			
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Check All RM Settled or Not
	function checkAllRMSettled($weighNumber, $billingCompany)
	{
		$notSettled = 0;
		$qry	=	"select b.paid, b.settlement_date, a.payment_by, a.payment_confirm, a.payment_date 
			from (t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d) left join m_grade e on b.grade_id=e.id
			where weighment_challan_no='$weighNumber' and a.id=main_id and b.fish=c.id and b.fish_code=d.id and a.billing_company_id='$billingCompany' ";
			//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
				foreach ($result as $rec) {
					$paid = $rec[0];
					if ($paid=='N') {
						$notSettled++;
					}
				}
		}
		return ($notSettled!="")?true:false;
	}		

	#Update DailyCatch_main Records after verified
	function updateDailyCatchMainConfirmRecords($weighNumber, $billingCompany)
	{
		$qry	=	" update t_dailycatch_main set report_confirm='Y' where weighment_challan_no='$weighNumber' and billing_company_id='$billingCompany'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Find Supply Cost 
	function findSupplyCostRec($weighNumber, $billingCompany)
	{
		$qry = "select a.id, a.challan_id, a.ice_total_block, a.ice_cost_per_block, a.ice_total_cost, a.ice_fixed_cost, a.tran_km, a.tran_cost_per_km, a.tran_total_amt, a.tran_fixed_amt, a.comm_total_qty, a.comm_per_kg, a.comm_total_rate, a.comm_fixed_rate, b.select_date, a.handl_total_qty, a.handl_rate_per_kg, a.handl_total_amt, a.handl_fixed_amt, a.settled_option, a.commission_option, a.commission_total_amt, a.handling_option, a.handling_total_amt, a.ice_paid, a.transportation_paid, a.commission_paid, a.handling_paid from t_rmsupplycost a, t_dailycatch_main b where a.challan_id=b.id and b.weighment_challan_no='$weighNumber' and b.billing_company_id='$billingCompany'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	/*
	Status Release
	Change RM Quantity - paid, settled, rm confirm
	Change Rate  - paid and settled
	Change Payment Details: Paid realse
	*/

	#Change Payment Status
	#*************************
	# Changestatus : CRMQ->Change RM Qty, CR->Change Rate, CPD->Change Payment Details
	function updateRMStatus($weighNumber, $supplierPaymentWt, $changeStatus, $billingCompany)
	{
		$rmConfirm = "";
		
		if ($changeStatus=='CR' || $changeStatus=='CRMQ') {

			$dailyCatchRMRecords = $this->fetchAllCatchReportRecords($weighNumber, $billingCompany);
			foreach ($dailyCatchRMRecords as $dcr) {
				$dailyCatchEntryId = $dcr[42];
				$updateDailyCatchEntry = $this->updateDailyCatchEntryRec($dailyCatchEntryId);
			}

			if ($supplierPaymentWt=='D' && ($changeStatus=='CR' || $changeStatus=='CRMQ')) {

				$declaredWtRMRecords = $this->getSupplierDeclaredWtRecords($weighNumber, $billingCompany);

				foreach ($declaredWtRMRecords as $dwr) {
					$declaredWtEntryId = $dwr[19];
					$updateDeclaredWtEntryRec = $this->updateDeclaredWtEntryRec($declaredWtEntryId);
				}
			}
		}
		

		if ($changeStatus=='CPD' || $changeStatus=='CR' || $changeStatus=='CRMQ') {

			if ($changeStatus=='CRMQ') $rmConfirm = " , confirm=0";

			$qry = " update t_dailycatch_main set payment_confirm='N', payment_date='0', report_confirm='N', print_status='N', setl_print_status='N' $rmConfirm where weighment_challan_no='$weighNumber' and billing_company_id='$billingCompany'";
		}
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	#Change Status in Daily CatchEntry Table
	function  updateDailyCatchEntryRec($dailyCatchEntryId)
	{
		$qry = "update t_dailycatchentry set paid='N', settlement_date='0' where id='$dailyCatchEntryId' ";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Change Settlement Status of Declared Wt entry
	function updateDeclaredWtEntryRec($declaredWtEntryId)
	{
		$qry = "update t_dailycatch_declared set settled='N', settled_date='0' where id = '$declaredWtEntryId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	#*************************  Status Updation End ********************

	# Get Account Entry No
	function getAccountEntryNo($challanNoEntryId)
	{
		$qry = " select account_entry_no from t_supplierpayments  where  challan_nos like '$challanNoEntryId' or challan_nos like '$challanNoEntryId,%' or challan_nos like '%,$challanNoEntryId,%' or challan_nos like '%,$challanNoEntryId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	#For Supplier Settlement Memo 	
	function getSuppDecWtRecords($challanMainId, $billingCompany)
	{	
		$whr	= "a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.id='$challanMainId' and a.billing_company_id='$billingCompany'" ;	
				
		//$groupBy	= "b.fish, b.fish_code , e.decl_count";
		//$orderBy	= "c.name asc, d.code asc, e.decl_count asc";
		$orderBy	= "c.name asc, d.code asc, e.decl_count asc";				
		$qry		= "select a.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, e.decl_wt, b.remarks, e.rate from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry	.= " group by ". $groupBy;			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;			
		//echo $qry;		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Supplier Challan Date (Using in Print Section)
	function getSuppChallanDate($challanMainId, $billingCompany)
	{		
		$qry = "  select distinct c.supplier_challan_date, c.sub_supplier, c.supplier_challan_no from  t_dailycatch_main a, t_dailycatchentry b, t_dailycatch_declared c where a.id=b.main_id and b.id=c.entry_id and a.id='$challanMainId' and a.billing_company_id='$billingCompany'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2]):"";
	}

	#Update Print Confirm Y/N
	function updateChallanSetlmntPrintStatus($challanId)
	{
		$qry = "update t_dailycatch_main set setl_print_status='Y', setl_print_date=Now() where id='$challanId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function billingCmpnyRecId($challanMainId)	
	{
		$qry = "select billing_company_id, weighment_challan_no, alpha_code, setl_print_status from t_dailycatch_main where id='$challanMainId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1],$result[0][2], $result[0][3]):array();
	}
}
?>