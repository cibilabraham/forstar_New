<?php
Class PreProcessingPaymentStatus
{

	/****************************************************************
	This class deals with all the operations relating to Payment Status
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PreProcessingPaymentStatus(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select Disitinct preProcessor
	function fetchDistinctPreProcessorRecords($fromDate, $tillDate)
	{
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."' ";
					
		$orderBy = "d.name asc";
		
		$qry = "select distinct c.preprocessor_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d ";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
			
		//echo $qry."<br>";		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# select distinct settlement dates
	function fetchAllDateRecords($fromDate, $tillDate, $selProcessor)
	{		
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.preprocessor_id='$selProcessor' and c.paid='Y'";
					
		$orderBy = " c.settlement_date asc";
		
		$qry = "select distinct c.settlement_date  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c ";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;				
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Took Records based on date and supplier
	function fetchProcessorSettlementRecords($fromDate, $tillDate, $processor, $selSettlementDate)
	{
		$whr = " a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and c.preprocess_qty ";

		if ($selSettlementDate=="") $whr .="";
		else $whr .=" and c.settlement_date= '".$selSettlementDate."'";

		/*$groupBy	=	" a.date ";
		$orderBy	=	" a.date asc ";*/

		$groupBy	=	"date ";
		$orderBy	=	"date asc ";

		$qry1= " select a.id, a.fish_id, a.date as date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, sum(c.actual_amount), c.paid, c.settlement_date, c.payment_confirm, c.payment_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c ";

		$qry2= " select a.id, a.fish_id, a.date  as date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, sum(c.actual_amount), c.paid, c.settlement_date, c.payment_confirm, c.payment_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c ";

		if ($whr!="") 		$qry1.= " where ".$whr;		$qry2.= " where ".$whr; 
		//if ($whr!="") 		$qry .= " where ".$whr;	
		$qry="$qry1 union $qry2";
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;	
		//echo "<br>$qry<br>";
				
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	#Checking All Record Settled
	function checkAllRecordsSettled($fromDate, $tillDate, $processor, $entryDate)
	{
		$notSettled = 0;
		$notPaid    = 0;
		$notConfirmed = 0;
		
		$qry = "select a.confirmed, c.paid, c.payment_confirm from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date = '$entryDate' and c.preprocess_qty order by a.date asc";				
		//echo "<br>".$qry."<br>";		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		foreach ($result as $cr) { 
			$confirmed 	= $cr[0];
			$settled 	= $cr[1];
			$paid	 	= $cr[2];
			if ($confirmed=='N')	$notConfirmed++;
			if ($settled=='N') 	$notSettled++;
			if ($paid=='N')		$notPaid++;			
		}
		if ($notSettled>0) $notSettledStatus = true;
		if ($notPaid>0) $notPaidStatus = true;
		if ($notConfirmed>0) $notConfirmedStatus = true;
		return array($notSettledStatus, $notPaidStatus, $notConfirmedStatus);
	}

	# Filter Pre-Processing records from t_dailypreprocessing based on Date (using below function)
	function filterPreProcessingRecords($fromDate, $tillDate, $selProcessorId, $qtySearchType)
	{
		$whr = " b.dailypreprocess_main_id=a.id and c.dailypreprocess_entry_id = b.id and c.preprocessor_id=d.id and e.id=a.fish_id and b.process=f.id and a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.preprocess_qty and c.preprocessor_id='$selProcessorId' ";			
		
		
		if ($qtySearchType=='DT') {
			$groupBy	= "";
		} else {
			//$groupBy	= " a.fish_id, b.process ";
			$groupBy	= " date ";
		}
		
		if ($qtySearchType=='DT') {
			$orderBy 	= " date asc, name asc, code asc";
		} else {
			$orderBy	= " date asc, name asc, code asc";
		}
		
		if ($qtySearchType=='DT') {
			$qry1 = "select a.id, a.fish_id, a.date as date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.select_commission, c.select_rate, c.actual_amount, e.name as name, f.code as code, d.name as processName, c.preprocess_qty, c.paid, c.payment_confirm, a.confirmed, c.id from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d, m_fish e, m_process f ";
			$qry2 = "select a.id, a.fish_id, a.date as date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.select_commission, c.select_rate, c.actual_amount, e.name  as name, f.code as code, d.name  as processName, c.preprocess_qty, c.paid, c.payment_confirm, a.confirmed, c.id from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, t_dailypreprocess_processor_qty_rmlotid c, m_preprocessor d, m_fish e, m_process f ";
		} else {
			$qry1 = "select a.id, a.fish_id, a.date as date, b.id, b.process, sum(b.opening_bal_qty), sum(b.arrival_qty), sum(b.total_qty), sum(b.total_preprocess_qty), sum(b.actual_yield), sum(b.ideal_yield), b.diff_yield, sum(c.select_commission), sum(c.select_rate), sum(c.actual_amount), e.name as name, f.code as code, d.name  as processName, sum(c.preprocess_qty), c.paid, c.payment_confirm, a.confirmed, c.id from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d, m_fish e, m_process f ";
			$qry2 = "select a.id, a.fish_id, a.date as date, b.id, b.process, sum(b.opening_bal_qty), sum(b.arrival_qty), sum(b.total_qty), sum(b.total_preprocess_qty), sum(b.actual_yield), sum(b.ideal_yield), b.diff_yield, sum(c.select_commission), sum(c.select_rate), sum(c.actual_amount), e.name as name, f.code as code, d.name  as processName, sum(c.preprocess_qty), c.paid, c.payment_confirm, a.confirmed, c.id from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid  b, t_dailypreprocess_processor_qty_rmlotid  c, m_preprocessor d, m_fish e, m_process f ";
		}
				
		//if ($whr!="")		$qry .= " where ".$whr;	
		if ($whr!="")		$qry1.= " where ".$whr;	$qry2.= " where ".$whr;
		$qry="$qry1 union $qry2";
		if ($groupBy!="")	$qry .= " group by ". $groupBy;			
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo $qry."<br>";		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	/*
	Status Release
		Change Pre-Process Quantity - paid, settled, confirm  Release
		Change Rate  - paid and settled
		Change Payment Details: Paid realse
	*/
	#Change Payment Status
	#*************************
	# Changestatus : CRMQ->Change RM Qty, CR->Change Rate, CPD->Change Payment Details
	function updatePreProcessPaymentStatus($fromDate, $tillDate, $selProcessor, $changeStatus)
	{
		# Get All records for date range
		$preProcessedRecords = $this->filterPreProcessingRecords($fromDate, $tillDate, $selProcessor, 'DT');
		if (sizeof($preProcessedRecords)>0) {			
			foreach ($preProcessedRecords as $ppr) {
				$dailyPreProcessMainId	= $ppr[0];
				$processorEntryId	= $ppr[22];				
				if ($processorEntryId!="") {
					$updatePaymentDetails = $this->updatePreProcessStatus($processorEntryId, $changeStatus);
					if ($changeStatus=='CRMQ') {
						$updateDailyPreProcessMainRec = $this->updateDailyPreProcessMainRec($dailyPreProcessMainId);
					}
				}
			}
		}
		return true;	
	}
	# Upate Status
	function updatePreProcessStatus($processorEntryId, $changeStatus)
	{
		$uptdQry = "";
		//, actual_amount='0'
		if ($changeStatus=='CR' || $changeStatus=='CRMQ') $uptdQry = " , paid='N', settlement_date='0' ";

		$qry = " update t_dailypreprocess_processor_qty set payment_confirm='N', payment_date='0' $uptdQry where id='$processorEntryId' ";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}
	# Update Main Rec
	function updateDailyPreProcessMainRec($dailyPreProcessMainId)
	{
		$qry = " update t_dailypreprocess set confirmed='N' where id='$dailyPreProcessMainId' ";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}
}	
?>