<?php
Class ProcessorSettlementSummary
{
	/****************************************************************
	This class deals with all the operations relating to Processor Settlement Summary 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProcessorSettlementSummary(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	#Select Disitinct preProcessor
	function fetchDistinctPreProcessorRecords($fromDate, $tillDate)
	{
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.paid='Y'";
					
		$orderBy = "d.name asc";
		
		$qry = "select distinct preprocessor_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d ";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
			
		//echo $qry."<br>";		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Took Records based on date and supplier
	function fetchProcessorSettlementRecords($fromDate, $tillDate, $processor)
	{
		$qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, sum(c.actual_amount), c.paid, c.settlement_date,c.payment_confirm,c.payment_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id=b.dailypreprocess_main_id and b.id=c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and c.paid='Y' and c.preprocess_qty group by a.date order by a.date asc";
		echo $qry;		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Checking All Record Settled
	function checkAllRecordsSettled($fromDate, $tillDate, $processor, $entryDate)
	{
		$notSettled = 0;
		
		$qry = "select a.date, c.paid, c.settlement_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date='$entryDate' and c.preprocess_qty order by a.date asc";				
		//echo "<br>".$qry."<br>";		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		foreach ($result as $cr) { 
			$settled = $cr[1];
			if ($settled=='N') {
				$notSettled++;
			}
		}
		return ($notSettled!="")?true:false;
	}


	# Get all Settled Records
	function getAllSettledRecords($fromDate, $tillDate, $processor, $entryDate)
	{
		$qry = "select c.id, c.paid, c.settlement_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date = '$entryDate' order by c.id asc";				
		//echo $qry."<br>";		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update Processor Payment
	function updateProcessorPayment($processorEntryId, $paid)
	{
		$qry = "update t_dailypreprocess_processor_qty set";
		
		if ($paid=='Y') {
			$qry .= " payment_confirm='$paid',payment_date=Now()";		
		} else if ($paid=='N') {
			$qry .= " payment_confirm='$paid', payment_date='0000-00-00'";		
		} else {
			$qry .="";
		}
		
		$qry .= "  where id='$processorEntryId' ";	
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
}	
?>