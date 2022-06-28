<?php
Class PreProcessingReport
{
	/****************************************************************
	This class deals with all the operations relating to Pre Processing Report
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function PreProcessingReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	# Filter records from t_dailypreprocessing based on Date
	function filterDailyPreProcessingRecords($fromDate, $tillDate, $fishId, $processId, $selProcessorId, $details, $summary)
	{
		$whr .=" a.id = b.dailypreprocess_main_id and a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.id=a.fish_id and b.process=d.id";
									
		if ($fishId!="") $whr .= " and a.fish_id=".$fishId;		
		if ($processId!="") $whr .= " and b.process=".$processId;		

		if ($details!="") $groupBy = "";
		else $groupBy = " a.fish_id, b.process ";
		
		if ($details!="") $orderBy 	= " a.date asc, c.name asc, d.code asc";
		else $orderBy	= " c.name asc, d.code asc";
												
		if ($details!="") {
			$qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield from t_dailypreprocess a, t_dailypreprocess_entries b, m_fish c, m_process d";
		} else {
	   		$qry = " select a.id, a.fish_id, a.date, b.id, b.process, sum(b.opening_bal_qty), sum(b.arrival_qty), sum(b.total_qty), sum(b.total_preprocess_qty), sum(b.actual_yield), sum(b.ideal_yield), b.diff_yield, count(*) from t_dailypreprocess a, t_dailypreprocess_entries b, m_fish c, m_process d ";
		}
			
		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($groupBy!="")	$qry   .= " group by ". $groupBy;	
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
					
		//echo $qry."<br>";				
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter Pre-Processing records from t_dailypreprocessing based on Date
	function filterPreProcessingRecords($fromDate, $tillDate, $selFishId, $processId, $selProcessorId, $details, $summary)
	{
		$whr = " b.dailypreprocess_main_id=a.id and c.dailypreprocess_entry_id = b.id and c.preprocessor_id=d.id and e.id=a.fish_id and b.process=f.id and a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.preprocess_qty and c.actual_amount!=0 and c.paid='Y' ";
				
		if ($selFishId!="") $whr .= " and a.fish_id=".$selFishId;
		if ($processId!="") $whr .= " and b.process=".$processId;		
		if ($selProcessorId!="") $whr .= " and c.preprocessor_id=".$selProcessorId;
		
		if ($details!="") $groupBy = "";
		else $groupBy = "a.fish_id, b.process";	
		
		if ($details!="") $orderBy = " a.date asc, e.name asc, f.code asc";
		else $orderBy	= " e.name asc, f.code asc";
		
		if ($details!="") {
			$qry = "select a.id, a.fish_id, a.date, b.id, b.process,b.opening_bal_qty,b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield,c.select_commission, c.select_rate, c.actual_amount, e.name, f.code, d.name, c.preprocess_qty from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d, m_fish e, m_process f ";
		} else {
			$qry = "select a.id, a.fish_id, a.date, b.id, b.process, sum(b.opening_bal_qty), sum(b.arrival_qty), sum(b.total_qty), sum(b.total_preprocess_qty), sum(b.actual_yield), sum(b.ideal_yield), b.diff_yield, sum(c.select_commission), sum(c.select_rate), sum(c.actual_amount), e.name, f.code, d.name, sum(c.preprocess_qty), count(*) from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c,m_preprocessor d, m_fish e, m_process f ";
		}
				
		if ($whr!="")		$qry .= " where ".$whr;			
		if ($groupBy!="")	$qry .= " group by ". $groupBy;			
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Filter daily catch Entry Records
	function filterDailyCatchRecords($fishId, $processFrom, $selectedDate)
	{
		$qry	= "select id,fish,fish_code,select_date,effective_wt from t_dailycatchentry where select_date='$selectedDate' and fish='$fishId' and fish_code='$processFrom'";
		//echo $qry;		
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Filter dailypreprocess quantity based on the crieria
	function filterDailyPreProcessRecord($fishId, $processes, $selectedDate, $processorId)
	{
		$qry =	"select quantity from t_dailypreprocess where fish_id = '$fishId' and process = '$processes' and processor_id='$processorId' and date='$selectedDate'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Select Disitinct preProcessor
	function fetchDistinctPreProcessorRecords($fromDate, $tillDate, $fishId, $processId)
	{	
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";
				
		if ($fishId!="") $whr .= " and a.fish_id=".$fishId;		
		if ($processId!="") $whr .= " and b.process=".$processId;
				
		$orderBy = "d.name asc";
		
		$qry = "select distinct preprocessor_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d ";
		
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
			
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}


	# get one by one preProcessor Records
	function getPreProcessorRecords($fromDate, $tillDate, $selProcessorId)
	{	
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";

		if ($selProcessorId!="") $whr .=" and c.preprocessor_id=".$selProcessorId;
				
		$orderBy = "d.name asc";
		
		$qry = " select distinct preprocessor_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d ";
		
		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
			
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter t_dailypreprocess_processor_qty // using in Daily Pre Processing report 
	function preProcessorRecFilter($dailyPreProcessEntryId, $preProcessorId)
	{
		$qry	= "select id, dailypreprocess_entry_id, preprocessor_id, preprocess_qty  from t_dailypreprocess_processor_qty where preprocess_qty and dailypreprocess_entry_id='$dailyPreProcessEntryId' and preprocessor_id='$preProcessorId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Find Each Process Commission Rate
	function findCommissionRate($preProcessId)
	{
		$qry	= "select id,fish_id,processes,day, rate,commi,criteria, code from m_process where id='$preProcessId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return ( sizeof($result) > 0 ) ? $result[5] : ""; 
	}

	# Fish Records for a date range
	function fetchFishRecords($fromDate, $tillDate)
	{
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=a.fish_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";	
				
		$orderBy = "d.name asc";
		
		$qry = "select distinct a.fish_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_fish d ";
		
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}	


	# Process Code Records
	function getPreProcessCodeRecords($fromDate, $tillDate, $fishId)
	{
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=b.process and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and a.fish_id='".$fishId."'";
	
		$orderBy = "d.code asc";
	
		$qry = "select distinct b.process, d.code  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_process d ";
	
		if ($whr!="")		$qry .= " where ".$whr;		
		if ($orderBy!="")	$qry .= " order by ".$orderBy;			
			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}	


	# PRE-PROCESS SUMMARY FUNCTIONs START HERE
	##---------------------------------------
	# Finding Pre Processor HO Qty,  based on pre processor

	function findPreProcessorHOQty($fishId, $preProcessId, $fromDate, $tillDate, $masterPreProcessorId)
	{
		$qry	= "select sum((a.preprocess_qty*100)/b.actual_yield), sum(a.preprocess_qty)  from t_dailypreprocess_processor_qty a, t_dailypreprocess_entries b, t_dailypreprocess c where c.id=b.dailypreprocess_main_id and b.id= a.dailypreprocess_entry_id and c.date>='".$fromDate."' and c.date<='".$tillDate."' and b.process=$preProcessId and c.fish_id='$fishId' and a.preprocessor_id='$masterPreProcessorId' group by c.fish_id, b.process";
		//echo $qry."<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	# get one by one preProcessor Records (Summary)
	function getPreProcessorSummaryRecords($fromDate, $tillDate, $selFishId, $processId, $selProcessorId)
	{
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";

		if ($selFishId!="") $whr .= " and a.fish_id=".$selFishId;		
		if ($processId!="") $whr .= " and b.process=".$processId;		
		if ($selProcessorId!="") $whr .= " and c.preprocessor_id=".$selProcessorId;
				
		$orderBy = " d.name asc ";
		
		$qry = "select distinct c.preprocessor_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d ";
		
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
			
	//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# Filter records from t_dailypreprocessing based on Date
	function getPreProcessingSummaryRecords($fromDate, $tillDate, $fishId, $processId)
	{
		$whr .= " a.id = b.dailypreprocess_main_id and b.process = c.id and a.date>='".$fromDate."' and a.date<='".$tillDate."'";
							
		if ($fishId!="") $whr .= " and a.fish_id=".$fishId;		
		if ($processId!="") $whr .= " and b.process=".$processId;		
	
		$groupBy	= "a.fish_id, b.process";		
		$orderBy	= " c.code asc";

		$qry = "select a.id, a.fish_id, a.date, b.id, b.process,b.opening_bal_qty,b.arrival_qty, b.total_qty, sum(b.total_preprocess_qty), b.actual_yield, b.ideal_yield, b.diff_yield from t_dailypreprocess a, t_dailypreprocess_entries b, m_process c";
		
		
		if ($whr!="")		$qry   .= " where ".$whr;			
		if ($groupBy!="") 	$qry   .= " group by ".$groupBy;			
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;

		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# get total Quantity of a Pre-Processor
	function getPreProcessorProcessedQty($fromDate, $tillDate, $selProcessorId, $fishId, $processId)
	{
		$whr = " b.dailypreprocess_main_id=a.id and c.dailypreprocess_entry_id = b.id and c.preprocessor_id=d.id and e.id=a.fish_id and b.process=f.id and a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.preprocessor_id='".$selProcessorId."' and c.preprocess_qty ";
		
		if ($fishId!="") $whr .= " and a.fish_id=".$fishId;		
		if ($processId!="") $whr .= " and b.process=".$processId;

		$groupBy = "a.fish_id, b.process";		
		$qry = " select sum((c.preprocess_qty*100)/b.actual_yield), sum(c.preprocess_qty) from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c,m_preprocessor d, m_fish e, m_process f ";	
				
		if ($whr!="")		$qry	.= " where ".$whr;			
		if ($groupBy!="")	$qry 	.= " group by ". $groupBy;			
		if ($orderBy!="") 	$qry    .= " order by ".$orderBy;		
				
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# PRE-PROCESS SUMMARY FUNCTIONs END HERE
	
}	
?>