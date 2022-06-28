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
		
		$qry = "select * from (select distinct(preprocessor_id) as pid, d.name as name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d where a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.paid='Y' union all select distinct (preprocessor_id) as pid, d.name as name  from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, t_dailypreprocess_processor_qty_rmlotid c, m_preprocessor d where a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.paid='Y')dum group by pid  order by name asc";

		 
		//$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.paid='Y'";
		//$orderBy = "d.name asc";
		//$qry = "select distinct preprocessor_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d ";
		//if ($whr!="") $qry .= " where ".$whr;
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
			
		//echo $qry."<br>";		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Took Records based on date and supplier
	function fetchProcessorSettlementRecords($fromDate, $tillDate, $processor)
	{
		$qry = "select mainid,fishid,date,sub_id,process,ob,aq,tq,tpq,ay,iy,dy,qtyid,pq,sc,rate, sum(ac),paid, seldate,confirm,paydate from(select a.id as mainid, a.fish_id as fishid, a.date as date, b.id as sub_id, b.process as process, b.opening_bal_qty as ob, b.arrival_qty as aq, b.total_qty as tq, b.total_preprocess_qty as tpq, b.actual_yield as ay, b.ideal_yield as iy, b.diff_yield as dy, c.id as qtyid, c.preprocess_qty as pq, c.select_commission as sc, c.select_rate as rate, sum(c.actual_amount) as ac, c.paid as paid, c.settlement_date as seldate,c.payment_confirm as confirm,c.payment_date as paydate from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id=b.dailypreprocess_main_id and b.id=c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and c.paid='Y' and c.preprocess_qty group by a.date 
		union all
		select a.id as mainid, a.fish_id as fishid, a.date as date, b.id as sub_id, b.process as process, b.opening_bal_qty as ob, b.arrival_qty as aq, b.total_qty as tq, b.total_preprocess_qty as tpq, b.actual_yield as ay, b.ideal_yield as iy, b.diff_yield as dy, c.id as qtyid, c.preprocess_qty as pq, c.select_commission as sc, c.select_rate as rate, sum(c.actual_amount) as ac, c.paid as paid, c.settlement_date as seldate,c.payment_confirm as confirm,c.payment_date as paydate from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, t_dailypreprocess_processor_qty_rmlotid c where a.id=b.dailypreprocess_main_id and b.id=c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and c.paid='Y' and c.preprocess_qty group by a.date)dum group by date order by date";
		//$qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, sum(c.actual_amount), c.paid, c.settlement_date,c.payment_confirm,c.payment_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id=b.dailypreprocess_main_id and b.id=c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and c.paid='Y' and c.preprocess_qty group by a.date order by a.date asc";
		//echo $qry;		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Checking All Record Settled
	function checkAllRecordsSettled($fromDate, $tillDate, $processor, $entryDate)
	{
		$notSettled = 0;

		$qry = "select date,paid,settleDate from(select a.date as date , c.paid as paid, c.settlement_date as settleDate from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date='$entryDate' and c.preprocess_qty union all
		select a.date as date, c.paid as paid, c.settlement_date as settleDate from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, t_dailypreprocess_processor_qty_rmlotid c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date='$entryDate' and c.preprocess_qty) dum order by date asc";
		
		//$qry = "select a.date, c.paid, c.settlement_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date='$entryDate' and c.preprocess_qty order by a.date asc";	
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
		$qry = "select * from(select c.id as id, c.paid, c.settlement_date ,'0' as rmStatus from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date = '$entryDate' union all select c.id as id, c.paid, c.settlement_date ,'1' as rmStatus from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, t_dailypreprocess_processor_qty_rmlotid c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date = '$entryDate') dum order by id";
		//$qry = "select c.id, c.paid, c.settlement_date from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c where a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocessor_id='$processor' and a.date = '$entryDate' order by c.id asc";
		//order by c.id asc
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

	# Update Processor Payment
	function updateProcessorPaymentRmLot($processorEntryId, $paid)
	{
		$qry = "update t_dailypreprocess_processor_qty_rmlotid set";
		
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