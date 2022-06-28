<?php
Class ProcessorsAccounts
{
	/****************************************************************
	This class deals with all the operations relating to Processor Accounts
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProcessorsAccounts(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}


	#Update t_dailypreprocess table 
	function updatePreProcessPaidAmount($preProcessorQtyId, $selectRate, $actualAmount, $paid, $commission)
	{
		$qry	=	"update t_dailypreprocess_processor_qty set select_commission='$commission', select_rate='$selectRate', actual_amount='$actualAmount'";
		
		if ($paid=='Y') {
			$qry .= " , paid='$paid',settlement_date=Now()";		
		} else if ($paid=='N') {
			$qry .= " , paid='$paid', settlement_date='0000-00-00'";		
		} else {
			$qry .="";
		}
		
		$qry .= "  where id='$preProcessorQtyId' ";
		
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Update daily preprocess entry
	function updatePreProcessEntryRec($preProcessorEntryId, $idealYield)
	{
		$qry 	=	"update t_dailypreprocess_entries set ideal_yield='$idealYield' where id='$preProcessorEntryId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}


	#Update t_dailypreprocess rmlotid table 
	function updatePreProcessPaidAmountRMLot($preProcessorQtyId, $selectRate, $actualAmount, $paid, $commission)
	{
		$qry	=	"update t_dailypreprocess_processor_qty_rmlotid set select_commission='$commission', select_rate='$selectRate', actual_amount='$actualAmount'";
		
		if ($paid=='Y') {
			$qry .= " , paid='$paid',settlement_date=Now()";		
		} else if ($paid=='N') {
			$qry .= " , paid='$paid', settlement_date='0000-00-00'";		
		} else {
			$qry .="";
		}
		
		$qry .= "  where id='$preProcessorQtyId' ";
		
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Update daily preprocess entry
	function updatePreProcessEntryRecRMLot($preProcessorEntryId, $idealYield)
	{
		$qry 	=	"update t_dailypreprocess_entries_rmlotid set ideal_yield='$idealYield' where id='$preProcessorEntryId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}


	# Fetch all record based on Processor
	function fetchAllRecords($fromDate, $tillDate, $processor, $offset, $limit, $selProcessCode)
	{
		$whr = " a.date>='$fromDate' and a.date<='$tillDate' and c.preprocess_qty";

		if ($processor!="") $whr .= " and c.preprocessor_id='".$processor."'";
		if ($selProcessCode!="") $whr .= " and b.process=$selProcessCode";

		$orderBy  = " a.date asc, d.name asc, e.code asc ";

		$limit = " $offset, $limit ";

		$qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, c.preprocessor_id, c.payment_confirm, e.code as preProcessCode from t_dailypreprocess a join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id join t_dailypreprocess_processor_qty c on b.id = c.dailypreprocess_entry_id left join m_fish d on d.id=a.fish_id left join m_process e on e.id=b.process ";		
		
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
		if ($limit!="") 	$qry   .= " limit ".$limit;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}	

	#All records with date range
	function getAllPreProcessorRecords($fromDate, $tillDate, $processor, $selProcessCode)
	{
		$whr = "a.id = b.dailypreprocess_main_id and b.id = c.dailypreprocess_entry_id and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocess_qty";

		if ($processor=="") $whr .= "";
		else $whr .= " and c.preprocessor_id='".$processor."'";

		if ($selProcessCode=="") $whr .= "";
		else $whr .= " and b.process=$selProcessCode";


		$qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, c.preprocessor_id, c.payment_confirm from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c";
		
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;		
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Filter m_process table of fish and process
	function filterProcessRec($preProcessId)
	{
		$qry	=	"select id,day,rate,commi, criteria from m_process where id = '$preProcessId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Select Disitinct preProcessor
	function fetchDistinctPreProcessorRecords($fromDate, $tillDate, $selProcessCode)
	{
		$whr = "";

		$orderBy	=	"name asc";
		$groupBy	=	"name ";
		$qry1 = "select  preprocessor_id, d.name as name,b.process as process  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d where a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";

		$qry2 = "select  preprocessor_id, d.name as name,b.process as process  from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, t_dailypreprocess_processor_qty_rmlotid c, m_preprocessor d where a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";
	
		$qry="select * from ($qry1 union all $qry2) dum";
		if ($selProcessCode!="") $qry.= " where  process='$selProcessCode'";
		if ($groupBy!="") 	$qry   .=" group by ".$groupBy;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;

		/*$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";

		if ($selProcessCode!="") $whr .= " and  b.process=$selProcessCode";
			
		$orderBy	=	"d.name asc";
		
		$qry = "select distinct preprocessor_id, d.name  from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor d ";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;	*/		
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	function fetchDistinctRMLotRecords($fromDate, $tillDate, $selProcessCode,$processor)
	{
		$whr = "a.id = b.dailypreprocess_main_id and  b.id = c.dailypreprocess_entry_id and d.id=c.preprocessor_id and c.preprocess_qty and a.rm_lot_id=e.id and  a.date>='".$fromDate."' and a.date<='".$tillDate."'";

		if ($selProcessCode!="") $whr .= " and  b.process=$selProcessCode";
		if ($processor!="") $whr .= " and  c.preprocessor_id=$processor";
			
		$orderBy	=	"d.name asc";
		
		$qry = "select distinct a.rm_lot_id, concat(e.alpha_character,e.rm_lotid)  from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, t_dailypreprocess_processor_qty_rmlotid c, m_preprocessor d, t_manage_rm_lotid e ";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;			
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Select distinct process code
	function getDistinctProcesscode($fromDate, $tillDate)
	{	
		$orderBy	=	"code asc";		
		//changes done dated 27june 2019
		//$qry1 = "select  b.process as process, c.code  as code from t_dailypreprocess a, t_dailypreprocess_entries b, m_process c where  a.id = b.dailypreprocess_main_id and b.process=c.id and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.rate_list_id in (select id from m_processratelist where (date_format(start_date,'%Y-%m-%d') between '$fromDate' and '$tillDate') or date_format(end_date,'%Y-%m-%d') between '$fromDate' and '$tillDate' or (end_date is null or end_date=0) order by start_date desc)";	
		$qry1 = "select  b.process as process, c.code  as code from t_dailypreprocess a, t_dailypreprocess_entries b, m_process c where  a.id = b.dailypreprocess_main_id and b.process=c.id and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.rate_list_id in (select id from m_processratelist where ('$fromDate' between date_format(start_date,'%Y-%m-%d') and date_format(end_date,'%Y-%m-%d')) or '$tillDate' between date_format(start_date,'%Y-%m-%d') and date_format(end_date,'%Y-%m-%d') or (end_date is null or end_date=0) order by start_date desc)";	

		//changes done dated 27june 2019
		//$qry2= "select  b.process as process, c.code  as code from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, m_process c where a.id = b.dailypreprocess_main_id and b.process=c.id and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.rate_list_id in (select id from m_processratelist where (date_format(start_date,'%Y-%m-%d') between '$fromDate' and '$tillDate') or date_format(end_date,'%Y-%m-%d') between '$fromDate' and '$tillDate' or (end_date is null or end_date=0) order by start_date desc)";	
		$qry2= "select  b.process as process, c.code  as code from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, m_process c where a.id = b.dailypreprocess_main_id and b.process=c.id and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and c.rate_list_id in (select id from m_processratelist where ('$fromDate' between date_format(start_date,'%Y-%m-%d') and date_format(end_date,'%Y-%m-%d')) or '$tillDate' between date_format(start_date,'%Y-%m-%d') and date_format(end_date,'%Y-%m-%d') or (end_date is null or end_date=0) order by start_date desc)";	
		
		$qry="select * from ($qry1 union all $qry2) dum group by process";	
		
		if ($orderBy!="") 	$qry   .= "  order by ".$orderBy;
		
		/*	$whr = "a.id = b.dailypreprocess_main_id and b.process=c.id and  a.date>='".$fromDate."' and a.date<='".$tillDate."' and 
			c.rate_list_id in (select id from m_processratelist where (date_format(start_date,'%Y-%m-%d') between '$fromDate' and '$tillDate') or date_format(end_date,'%Y-%m-%d') between '$fromDate' and '$tillDate' or (end_date is null or end_date=0)
 			order by start_date desc)";			
		$orderBy	=	"c.code asc";		
		$qry = "select distinct b.process, c.code  from t_dailypreprocess a, t_dailypreprocess_entries b, m_process c ";		
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;*/

		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;		
	}	

	# Fetch all record based on Processor
	function fetchAllRecordsNew($fromDate, $tillDate, $processor, $offset, $limit, $selProcessCode,$selRmlotId)
	{
		$orderBy  = " date asc, name asc, preProcessCode asc ";
		$limit = " $offset, $limit ";
		
		$qry1= "select a.id as processid, a.fish_id, a.date as date, b.id as processEntryId, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, c.preprocessor_id, c.payment_confirm, e.code as preProcessCode ,d.name as name,'0' as rmlotid,'0' as rmlotnm from t_dailypreprocess a join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id join t_dailypreprocess_processor_qty c on b.id = c.dailypreprocess_entry_id left join m_fish d on d.id=a.fish_id left join m_process e on e.id=b.process ";
		$whr1= " a.date>='$fromDate' and a.date<='$tillDate' and c.preprocess_qty";
		if ($processor!="") $whr1.= " and c.preprocessor_id='".$processor."'";
		if ($selProcessCode!="") $whr1.= " and b.process=$selProcessCode";
		if ($whr1!="")		$qry1   .= " where ".$whr1;
		
		//echo($qry1);
		//echo("<br><br>");

		
		$qry2= "select a.id as processid, a.fish_id, a.date as date, b.id as processEntryId, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, c.preprocessor_id, c.payment_confirm, e.code as preProcessCode ,d.name as name,tmngl.id as rmlotid ,concat(tmngl.alpha_character,tmngl.rm_lotid) as rmlotnm from t_dailypreprocess_rmlotid a join t_dailypreprocess_entries_rmlotid b on a.id = b.dailypreprocess_main_id join t_dailypreprocess_processor_qty_rmlotid c on b.id = c.dailypreprocess_entry_id left join m_fish d on d.id=a.fish_id left join m_process e on e.id=b.process 
		 left join t_manage_rm_lotid tmngl on tmngl.id=a.rm_lot_id";		
		$whr2= " a.date>='$fromDate' and a.date<='$tillDate' and c.preprocess_qty";
		if ($processor!="") $whr2.= " and c.preprocessor_id='".$processor."'";
		if ($selProcessCode!="") $whr2.= " and b.process=$selProcessCode";
		if($selRmlotId!="") $whr2.= " and a.rm_lot_id=$selRmlotId";
		if ($whr2!="")		$qry2   .= " where ".$whr2;

		if($selRmlotId=="")
		{
			
			
			$qry="select * from (".$qry1." union ".$qry2.") dum ";
		}
		else
		{
			$qry=$qry2;
		}
		
		//rekha modified here dated 18 june 2019
		//$qry   .= " and e.active=1"; 
		
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
		if ($limit!="") 	$qry   .= " limit ".$limit;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}
	
	#All records with date range
	function getAllPreProcessorRecordsNew($fromDate, $tillDate, $processor, $selProcessCode, $selRmlotId)
	{
		$orderBy  = " date asc, name asc, preProcessCode asc ";
		//$limit = " $offset, $limit ";
		
		$qry1= "select a.id as processid, a.fish_id, a.date as date, b.id as processEntryId, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, c.preprocessor_id, c.payment_confirm, e.code as preProcessCode ,d.name as name,'0' as rmlotid,'0' as rmlotnm from t_dailypreprocess a join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id join t_dailypreprocess_processor_qty c on b.id = c.dailypreprocess_entry_id left join m_fish d on d.id=a.fish_id left join m_process e on e.id=b.process ";
		$whr1= " e.active='1' and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocess_qty";
		if ($processor!="") $whr1.= " and c.preprocessor_id='".$processor."'";
		if ($selProcessCode!="") $whr1.= " and b.process=$selProcessCode";
		if ($whr1!="")		$qry1   .= " where ".$whr1;
		

		
		$qry2= "select a.id as processid, a.fish_id, a.date as date, b.id as processEntryId, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, c.preprocessor_id, c.payment_confirm, e.code as preProcessCode ,d.name as name,tmngl.id as rmlotid ,concat(tmngl.alpha_character,tmngl.rm_lotid) as rmlotnm from t_dailypreprocess_rmlotid a join t_dailypreprocess_entries_rmlotid b on a.id = b.dailypreprocess_main_id join t_dailypreprocess_processor_qty_rmlotid c on b.id = c.dailypreprocess_entry_id left join m_fish d on d.id=a.fish_id left join m_process e on e.id=b.process 
		 left join t_manage_rm_lotid tmngl on tmngl.id=a.rm_lot_id";		
		$whr2= " e.active='1' and a.date>='$fromDate' and a.date<='$tillDate' and c.preprocess_qty";
		if ($processor!="") $whr2.= " and c.preprocessor_id='".$processor."'";
		if ($selProcessCode!="") $whr2.= " and b.process=$selProcessCode";
		if($selRmlotId!="") $whr2.= " and a.rm_lot_id=$selRmlotId";
		if ($whr2!="")		$qry2   .= " where ".$whr2;

		if($selRmlotId=="")
		{
			$qry="select * from (".$qry1." union ".$qry2.") dum ";
		}
		else
		{
			$qry=$qry2;
		}

		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}
	
	// check active code dated: 19 june 2019
	function chkActiveCode($fishId,$preProcessCode,$preProcessId)
	{
		$qry= "select active from m_process where fish_id='".$fishId."' and code='".$preProcessCode."' and id='".$preProcessId."'";
		//$fishId,$preProcessCode,$preProcessId
		//$qry= "select active from m_process where fish_id='".$fishId."'";
		//$qry= "select active from m_process where fish_id='".$fishId."' and code='" $preProcessCode "'";
		//echo $qry;
		//echo "<br>";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];		
	}
		
		// end code 
	}	
?>