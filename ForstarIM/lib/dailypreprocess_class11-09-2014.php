<?php
Class DailyPreProcess
{
	/****************************************************************
	This class deals with all the operations relating to Daily Pre-Process
	*****************************************************************/
	var $databaseConnect;
	//var $preProcessSequence;

	//Constructor, which will create a db instance for this class
	function DailyPreProcess(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	# Add Daily Rate (Main table)
	function addDailyPreProcess($fishId,$selectDate)
	{
		$qry	= " insert into t_dailypreprocess (fish_id,date) values('$fishId', '$selectDate')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	
	function getAllLotIds()
	{	
		//$qry	= "select id,lot_Id from `t_rmreceiptgatepass` where active='1'";
		$qry	= "select id,new_lot_Id from t_unittransfer where active='1'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	# Get All Fish Recs
	function getLotRecs()
	{
		$lotArr = array();
		//$fishRecs = $this->fetchAllRecords();
		$lotRecs=$this->getAllLotIds();
		if (sizeof($lotRecs)>0) {
			foreach ($lotRecs as $fr) {
				$lotId = $fr[0];
				$lName  = $fr[1];
				$lotArr[$lotId] = $lName;
			}	
		}
		return $lotArr;
	}
	
	

	#Add Pre-Process
	#Insert datas for t_dailypreprocess_entries tablw 
	
	function addPreProcess($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $lastInsertedId, $lanCenterId, $availableQty, $autoGenCalc)
	{
		$qry	= " insert into t_dailypreprocess_entries (dailypreprocess_main_id, process, opening_bal_qty, arrival_qty, total_qty, total_preprocess_qty, actual_yield, ideal_yield, diff_yield, center_id, available_qty, auto_gen_calc) values
				   ( '$lastInsertedId','$preProcessId', '$openingBalQty', '$todayArrivalQty', '$totalQty', '$totalPreProcessQty', '$actualYield', '$idealYield', '$diffYield','$lanCenterId', '$availableQty', '$autoGenCalc')";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	function addPreProcess_preethi($lotID,$preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $lastInsertedId, $lanCenterId, $availableQty, $autoGenCalc)
	{
		$qry	= " insert into t_dailypreprocess_entries (dailypreprocess_main_id,lot_id, process, opening_bal_qty, arrival_qty, total_qty, total_preprocess_qty, actual_yield, ideal_yield, diff_yield, center_id, available_qty, auto_gen_calc) values
				   ( '$lastInsertedId','$lotID', '$preProcessId', '$openingBalQty', '$todayArrivalQty', '$totalQty', '$totalPreProcessQty', '$actualYield', '$idealYield', '$diffYield','$lanCenterId', '$availableQty', '$autoGenCalc')";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Insert datas for t_dailypreprocess_processor_qty
	function addPreProcesserQty($processorId,$preProcessorQty,$preProcessEntryLastId)
	{
		$qry	= " insert into t_dailypreprocess_processor_qty(dailypreprocess_entry_id, preprocessor_id, preprocess_qty) values('$preProcessEntryLastId', '$processorId', '$preProcessorQty')";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getAllProcessCodeList($fishId)
	{
		$qry="SELECT id,code FROM m_processcode  WHERE fish_id = '".$fishId."' ";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getProcessCodeFish($fishId)
	{	
		$qry="SELECT id,code FROM m_processcode  WHERE fish_id ='".$fishId."' and active='1' ";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	
	
	
	
	
	
	function getProcessCode($fishId,$rmlotid)
	{	
		$qry="SELECT id,code FROM m_processcode  WHERE id in (SELECT a.fish_code FROM t_dailycatchentry a left join t_dailycatch_main b on b.id=a.main_id where b.rm_lot_id ='".$rmlotid."' and a.fish='".$fishId."') ";
		//$qry="SELECT id,code FROM m_processcode  WHERE fish_id = '".$fishId."' ";
		
		//$qry="SELECT b.id, b.lot_Id FROM weighment_data_sheet a INNER JOIN t_rmreceiptgatepass b ON a.rm_lot_id = b.id WHERE a.active =  '1'";
		//$result	= $this->databaseConnect->getRecords($qry);
		//return $result;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	
	}
	
	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select a.id, a.fish_id, a.date, b.id, b.process,b.opening_bal_qty,b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield from t_dailypreprocess a, t_dailypreprocess_entries b where a.id = b.dailypreprocess_main_id order by a.date desc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter table using fish id
	function dailyPreProcessRecFilter($filterId, $recordsDate)
	{
		$whr = " a.date='".$recordsDate."'" ;
		
		if ($filterId!=0)  $whr .= " and a.fish_id='".$filterId."'" ;

		$orderBy	= "fishname asc,processsequence desc,sortval asc, preprocess desc, tossort asc";

		//$limit		=  "$offset,$limit";

		$qry		= "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id, a.confirmed, b.available_qty, b.auto_gen_calc, SUBSTRING_INDEX(mp.processes,',',1) as processfrom ,'notavailable' as lotid_available,a.id as lot_id,mf.name as fishname,frs.process_criteria as processsequence,frs.sort_id as sortval,tos.process_criteria as preprocess,tos.sort_id as tossort from t_dailypreprocess a left join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id left join m_process mp on mp.id=b.process left join m_fish mf on a.fish_id=mf.id join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2) 
		union 
		select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id, a.confirmed, b.available_qty, b.auto_gen_calc, SUBSTRING_INDEX(mp.processes,',',1) as processfrom,'available' as lotid_available,concat(rm.alpha_character,rm.rm_lotid) as lot_id,mf.name as fishname,frs.process_criteria as processsequence,frs.sort_id as sortval,tos.process_criteria as preprocess,tos.sort_id as tossort from t_dailypreprocess_rmlotid a left join t_dailypreprocess_entries_rmlotid b on a.id = b.dailypreprocess_main_id left join m_process mp on mp.id=b.process left join m_fish mf on a.fish_id=mf.id
		left join t_manage_rm_lotid rm on a.rm_lot_id=rm.id
		join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2)
		";
		
		if ($whr!="") 		$qry   .=" where ".$whr;
		if ($orderBy!="")  	$qry   .=" order by ".$orderBy;
		//if ($limit!="")	 	$qry   .=" limit ".$limit;				
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}
	
	
	
	
	
	
	# Filter table using fish id
	function dailyPreProcessRecFilter_old($filterId, $recordsDate)
	{
		$whr = " a.date='".$recordsDate."'" ;		
		if ($filterId!=0)	$whr	.= " and a.fish_id='".$filterId."'" ;
								
		$orderBy	=	"mf.name asc, frs.process_criteria desc, frs.sort_id asc, tos.process_criteria desc, tos.sort_id asc";
		
		$qry		= "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id, a.confirmed, b.available_qty, b.auto_gen_calc, SUBSTRING_INDEX(mp.processes,',',1) as processfrom 
		from 
		t_dailypreprocess a left join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id 
		left join m_process mp on mp.id=b.process 
		left join m_fish mf on a.fish_id=mf.id	
		join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) 
		join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2)
		";
		
		if ($whr!="") 		$qry   .=" where ".$whr;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;				
		//echo "<br>$qry";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	#Filter Pagination Records
	function dailyPreProcessRecPagingFilter($filterId, $recordsDate, $offset, $limit)
	{				
		$whr = " a.date='".$recordsDate."'" ;
		
		if ($filterId!=0)  $whr .= " and a.fish_id='".$filterId."'" ;

		$orderBy	= "fishname asc,processsequence desc,sortval asc, preprocess desc, tossort asc";

		$limit		=  "$offset,$limit";

		$qry1		= "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id, a.confirmed, b.available_qty, b.auto_gen_calc, SUBSTRING_INDEX(mp.processes,',',1) as processfrom ,'notavailable' as lotid_available,a.id as lot_id,mf.name as fishname,frs.process_criteria as processsequence,frs.sort_id as sortval,tos.process_criteria as preprocess,tos.sort_id as tossort from t_dailypreprocess a left join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id left join m_process mp on mp.id=b.process left join m_fish mf on a.fish_id=mf.id join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2) ";
		 
		$qry2="select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id, a.confirmed, b.available_qty, b.auto_gen_calc, SUBSTRING_INDEX(mp.processes,',',1) as processfrom,'available' as lotid_available,concat(rm.alpha_character,rm.rm_lotid) as lot_id,mf.name as fishname,frs.process_criteria as processsequence,frs.sort_id as sortval,tos.process_criteria as preprocess,tos.sort_id as tossort from t_dailypreprocess_rmlotid a left join t_dailypreprocess_entries_rmlotid b on a.id = b.dailypreprocess_main_id left join m_process mp on mp.id=b.process left join m_fish mf on a.fish_id=mf.id
		left join t_manage_rm_lotid rm on a.rm_lot_id=rm.id
		join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2)
		";
		
		
		if ($whr!="") 		//$qry   .=" where ".$whr;
		{
			$qry =$qry1." where ".$whr." union ".$qry2." where ".$whr;
		}
		else{
			$qry =$qry1." union ".$qry2;
		}
		if ($orderBy!="")  	$qry   .=" order by ".$orderBy;
		if ($limit!="")	 	$qry   .=" limit ".$limit;				
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}	
		
		
		
	#Filter Pagination Records
	function dailyPreProcessRecPagingFilter_old($filterId, $recordsDate, $offset, $limit)
	{				
		$whr = " a.date='".$recordsDate."'" ;
		
		if ($filterId!=0)  $whr .= " and a.fish_id='".$filterId."'" ;

		$orderBy	= "mf.name asc, frs.process_criteria desc, frs.sort_id asc, tos.process_criteria desc, tos.sort_id asc";

		$limit		=  "$offset,$limit";

		$qry		= " select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id, a.confirmed, b.available_qty, b.auto_gen_calc, SUBSTRING_INDEX(mp.processes,',',1) as processfrom from 
		t_dailypreprocess a left join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id
		left join m_process mp on mp.id=b.process 
		left join m_fish mf on a.fish_id=mf.id	
		join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) 
		join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2)
		";
		
		if ($whr!="") 		$qry   .=" where ".$whr;
		if ($orderBy!="")  	$qry   .=" order by ".$orderBy;
		if ($limit!="")	 	$qry   .=" limit ".$limit;				
		echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}
		
	#Filter t_dailypreprocess_processor_qty // using in Daily Pre Processing report 
	function preProcessorRecFilter($dailyPreProcessEntryId) 
	{
		$qry	= "select id, dailypreprocess_entry_id, preprocessor_id, preprocess_qty  from t_dailypreprocess_processor_qty where  dailypreprocess_entry_id=$dailyPreProcessEntryId ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Finding Pre Processing Qty based on pre processor
	function findPreProcessorRec($dailyPreProcessEntryId, $masterPreProcessorId)
	{
		$qry	= "select id, dailypreprocess_entry_id, preprocessor_id, preprocess_qty, paid, payment_confirm  from t_dailypreprocess_processor_qty where  dailypreprocess_entry_id=$dailyPreProcessEntryId and preprocessor_id='$masterPreProcessorId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Finding Pre Processing Qty rm Lot Id based on pre processor
	function findPreProcessorRecRmLotId($dailyPreProcessEntryId, $masterPreProcessorId)
	{
		$qry	= "select id, dailypreprocess_entry_id, preprocessor_id, preprocess_qty, paid, payment_confirm  from t_dailypreprocess_processor_qty_rmlotid where  dailypreprocess_entry_id=$dailyPreProcessEntryId and preprocessor_id='$masterPreProcessorId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Filter table using fish id
	function find($dailyPreProcessId)
	{
		$qry	= "select a.id, a.fish_id, a.date from t_dailypreprocess a 
				   left join t_dailypreprocess_entries b on b.dailypreprocess_main_id=a.id 
				   where a.id=$dailyPreProcessId";
		// echo $qry;		
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	# Filter table using fish id
	function findLotId($dailyPreProcessId)
	{
		$qry	= "select a.id,a.rm_lot_id,a.fish_id, a.date from t_dailypreprocess_rmlotid a 
				   left join t_dailypreprocess_entries_rmlotid b on b.dailypreprocess_main_id=a.id 
				   where a.id=$dailyPreProcessId";
		// echo $qry;		
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	#Get all Pre-Process records
	function findPreProcessEntryRec($preProcessMainId, $selRateListId)
	{
		$qry = "select a.id, a.code, c.id, c.process, c.opening_bal_qty, c.arrival_qty, c.total_qty, c.total_preprocess_qty, c.actual_yield, c.ideal_yield, c.diff_yield, c.center_id, f.center_id from m_process a left join t_dailypreprocess b on a.fish_id=b.fish_id left join m_process_yield_months f on a.id=f.process_id left join t_dailypreprocess_entries c on b.id =c.dailypreprocess_main_id and c.process=a.id and f.center_id = c.center_id where b.id=$preProcessMainId and a.rate_list_id='$selRateListId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete Daily Pre Process		 
	function deleteDailyPreProcess($dailyPreProcessId)
	{
		$qry	=	" delete from t_dailypreprocess where id=$dailyPreProcessId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Del record from t_dailypreprocess_processor_qty		
	function delDailyPreProcessorQty($dailyPreProcessEntryId)
	{
		$qry	=	" delete from t_dailypreprocess_processor_qty where dailypreprocess_entry_id=$dailyPreProcessEntryId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Del record from t_dailypreprocess_entries
	function delDailyPreProcessEntryQty($dailyPreProcessEntryId)
	{
		$qry	=	" delete from t_dailypreprocess_entries where id=$dailyPreProcessEntryId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Daily Pre Process RMLotId		 
	function deleteDailyPreProcessRMLotId($dailyPreProcessId)
	{
		$qry	=	" delete from t_dailypreprocess_rmlotid where id=$dailyPreProcessId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Del record from t_dailypreprocess_processor_qty_rmlotid 
	function delDailyPreProcessorQtyRMLotId($dailyPreProcessEntryId)
	{
		$qry	=	" delete from t_dailypreprocess_processor_qty_rmlotid where dailypreprocess_entry_id=$dailyPreProcessEntryId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Del record from t_dailypreprocess_entries_rmlotid
	function delDailyPreProcessEntryQtyRMLotId($dailyPreProcessEntryId)
	{
		$qry	=	" delete from t_dailypreprocess_entries_rmlotid where id=$dailyPreProcessEntryId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	
	# Update Pre Process
	function updatePreProcess($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $processEntryId, $availableQty, $changeAutoGenCalc)
	{
		$uptdQry = "";
		if ($changeAutoGenCalc!="") $uptdQry = ", auto_gen_calc='$changeAutoGenCalc'";
		$qry	= " update t_dailypreprocess_entries set process='$preProcessId', opening_bal_qty='$openingBalQty', arrival_qty='$todayArrivalQty', total_qty='$totalQty', total_preprocess_qty='$totalPreProcessQty', actual_yield='$actualYield', ideal_yield='$idealYield', diff_yield='$diffYield', available_qty='$availableQty' $uptdQry where id='$processEntryId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update Pre Process rmlotid
	function updatePreProcessRMLotId($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $processEntryId, $availableQty, $changeAutoGenCalc)
	{
		$uptdQry = "";
		if ($changeAutoGenCalc!="") $uptdQry = ", auto_gen_calc='$changeAutoGenCalc'";
		$qry	= " update t_dailypreprocess_entries_rmlotid set process='$preProcessId', opening_bal_qty='$openingBalQty', arrival_qty='$todayArrivalQty', total_qty='$totalQty', total_preprocess_qty='$totalPreProcessQty', actual_yield='$actualYield', ideal_yield='$idealYield', diff_yield='$diffYield', available_qty='$availableQty' $uptdQry where id='$processEntryId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	# Function #update datas for t_dailypreprocess_processor_qty
	function updatePreProcesserQty($processorQtyEntryId,$preProcessorQty)
	{
		$qry	= " update t_dailypreprocess_processor_qty  set preprocess_qty='$preProcessorQty' where id='$processorQtyEntryId'";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# Function #update datas for t_dailypreprocess_processor_qty
	function updatePreProcesserQtyRMlotid($processorQtyEntryId,$preProcessorQty)
	{
		$qry	= " update t_dailypreprocess_processor_qty_rmlotid  set preprocess_qty='$preProcessorQty' where id='$processorQtyEntryId'";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	
	# Filter Arrival Weight for selected date from daily catch Entry Records
	function dailyCatchEntryArrivalWeight($fishId, $processFrom, $selectedDate)
	{
		//+a.wastage
		$qry	= "select a.id, a.fish, a.fish_code, b.select_date, sum(a.effective_wt+a.adjust+a.local_quantity+a.soft), b.landing_center from t_dailycatchentry a, t_dailycatch_main b where b.id=a.main_id and b.select_date='$selectedDate' and a.fish='$fishId' and a.fish_code='$processFrom' group by a.fish_code";	
		//echo $qry."<br>";		
		return $this->databaseConnect->getRecord($qry);
	}

	# Find Exception Landing Center Rec
	function findYieldRec($processId,$lanCenterId)
	{
		$qry	=	"select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where process_id=$processId and center_id='$lanCenterId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Find Each Process Rate
	function findProcessRate($preProcessId)
	{
		$qry	= "select id,fish_id,processes,day, rate,commi,criteria, code from m_process where id='$preProcessId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return ( sizeof($result) > 0 ) ? $result[4] : ""; 
	}

	#Checking Record already exist
	function checkRecordsExist($dailyPreProcessMainId)
	{
		$qry	= "select a.id, a.fish_id, a.date, b.id, b.process,b.opening_bal_qty,b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield from t_dailypreprocess a, t_dailypreprocess_entries b where a.id = b.dailypreprocess_main_id and b.dailypreprocess_main_id='$dailyPreProcessMainId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Checking Record already exist
	function checkRecordsExistRMLotId($dailyPreProcessMainId)
	{
		$qry	= "select a.id,a.rm_lot_id,a.fish_id, a.date, b.id, b.process,b.opening_bal_qty,b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b where a.id = b.dailypreprocess_main_id and b.dailypreprocess_main_id='$dailyPreProcessMainId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	
	
	

	# Check unique all Records
	function checkUniqueRecords($fishId, $selectDate)
	{
		$qry	= "select id, fish_id, date from t_dailypreprocess where fish_id = '$fishId'  and date='$selectDate' ";			
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the Rate List Id from PreProcess Entry Id
	function getRateList($preProcessEntryId)
	{		
		$qry	= "select b.rate_list_id from t_dailypreprocess_entries a, m_process b where a.process=b.id and a.id='$preProcessEntryId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Checking Account confirmed  (Using Entry Id)
	function chkPreProcessAcConfirmed($dailyPreProcessEntryId)
	{		
		$qry = " select id, paid from t_dailypreprocess_processor_qty where dailypreprocess_entry_id='$dailyPreProcessEntryId' order by paid desc ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		
		$processSettled = false;
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$paid = $rec[1];
				if ($paid=='Y') $processSettled = true;
			}
		}
		return ($processSettled!="")?true:false;
	}

	# Checking Account confirmed RMLotId (Using Entry Id)
	function chkPreProcessAcConfirmedRMLotId($dailyPreProcessEntryId)
	{		
		$qry = " select id, paid from t_dailypreprocess_processor_qty_rmlotid where dailypreprocess_entry_id='$dailyPreProcessEntryId' order by paid desc ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		
		$processSettled = false;
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$paid = $rec[1];
				if ($paid=='Y') $processSettled = true;
			}
		}
		return ($processSettled!="")?true:false;
	}
	
	
	# Update Daily Pre Process Main Table
	function updateDailyPreProcess($dailyPreProcessMainId, $selectDate)
	{	
		$qry	= " update t_dailypreprocess set date='$selectDate' where id='$dailyPreProcessMainId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Check Duplicate Entry Exist
	function chkDuplicateEntryExist($fishId, $selectDate, $dailyPreProcessId)
	{
		if ($dailyPreProcessId!="") $uptdQry = " and id!='$dailyPreProcessId' ";
		else $uptdQry = "";
		$qry	= "select id, fish_id, date, confirmed from t_dailypreprocess where fish_id='$fishId' and date='$selectDate' $uptdQry";			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		//return (sizeof($result)>0)?array(true, $result[0][3]):array(false,'N');
		return (sizeof($result)>0)?true:false;
	}
	
	
	
	function chkDuplicateEntryLotExist($lotId, $selectDate, $dailyPreProcessId)
	{
		if ($dailyPreProcessId!="") $uptdQry = " and id!='$dailyPreProcessId' ";
		else $uptdQry = "";
		$qry	= "select id, lot_id, date, confirmed from t_dailypreprocess where lot_id='$lotId' and date='$selectDate' $uptdQry";			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		//return (sizeof($result)>0)?array(true, $result[0][3]):array(false,'N');
		return (sizeof($result)>0)?true:false;
	}

	function updateDailyPPEntryConfirm($selPreProcessDate, $confirmed)
	{
		$qry	= " update t_dailypreprocess set confirmed='$confirmed' where date='$selPreProcessDate' ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateDailyPPEntryConfirmRMLotID($selPreProcessDate, $confirmed)
	{
		$qry	= " update t_dailypreprocess_rmlotid set confirmed='$confirmed' where date='$selPreProcessDate' ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	function chkEntryConfirmed($selectDate)
	{		
		$qry = " select id, confirmed from t_dailypreprocess where date='$selectDate' group by confirmed ";	
		$result	= $this->databaseConnect->getRecords($qry);
		$confirmed = true;
		foreach ($result as $rec) {
			$confirmStatus = $rec[1];
			if ($confirmStatus=='N') $confirmed = false; 
		}
		return (sizeof($result)>0 && $confirmed)?true:false;
	}
	
	# Check Prev Entry Confirmed
	function chkPrevDateEntryConfirmed($prevDate)
	{
		//$prevEntryRecords = $this->dailyPreProcessRecFilter($fId, $prevDate);
		$qry = " select confirmed from t_dailypreprocess where date='$prevDate' group by confirmed ";	
		//echo "<br>$qry";
		$prevEntryRecords = $this->databaseConnect->getRecords($qry);

		if (sizeof($prevEntryRecords)>0) {
			$confirmed = true;
			foreach ($prevEntryRecords as $pr) {
				//$confirmStatus = $pr[13];
				$confirmStatus = $pr[0];
				if ($confirmStatus=='N') $confirmed = false; 
			}
			return ($confirmed)?true:false;
		} else {
			return true;
		}
	}

	# get Exception Recs
	function getPProcessorExpt($preProcessId, $processorId)
	{
		$qry = " select rate, commission, criteria, yield_tolerance from m_process_pre_processor where process_id='$preProcessId' and (pre_processor_id='$processorId' or pre_processor_id=0) order by pre_processor_id desc";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2], $result[0][3]):array();	
	}

	# Get Active Pre-Process codes
	function getDaysActivePreProcessCodes($fromDate, $fishId, $selRateListId)
	{
		# Pre-Process Sequence
		//$this->preProcessSequence = $this->getPreProcessSequence();

		$preProcessCodeArr = array();
		$activeProcessCodeArr = array();
		# Previous Day OB
		$prevDayOBRecs = $this->getOBRecs($fromDate, $fishId);
		foreach ($prevDayOBRecs as $drmr) {
			$pdProcessCodeId = $drmr[0];
			$pdFishId	= $drmr[1];
			$pdProcessCode	= $drmr[2];
			$pdFishName	= $drmr[3];
			$activeProcessCodeArr[$pdProcessCodeId] = array($pdFishId, $pdFishName, $pdProcessCode);			
		}

		# Daily RM
		$dailyRMRecords = $this->getDailyRMProcessCodeRecs($fromDate, $fishId);
		foreach ($dailyRMRecords as $drmr) {
			$fProcessCodeId = $drmr[0];
			$fFishId	= $drmr[1];
			$fProcessCode	= $drmr[2];
			$fFishName	= $drmr[3];
			$activeProcessCodeArr[$fProcessCodeId] = array($fFishId, $fFishName, $fProcessCode);			
		}


		# Daiy Production Recs
		$dailyProdRecs = $this->getDailyProductionRecs($fromDate, $fishId);
		foreach ($dailyProdRecs as $drmr) {
			$tProcessCodeId = $drmr[0];
			$tFishId	= $drmr[1];
			$tProcessCode	= $drmr[2];
			$tFishName	= $drmr[3];
			$activeProcessCodeArr[$tProcessCodeId] = array($tFishId, $tFishName, $tProcessCode);
		}
		
		# daily Re-Process (Thawed) Recs
		$dailyThawedRecs = $this->dailyReProcessedRecs($fromDate, $fishId);
		foreach ($dailyThawedRecs as $drmr) {
			$frProcessCodeId = $drmr[0];
			$frFishId	= $drmr[1];
			$frProcessCode	= $drmr[2];
			$frFishName	= $drmr[3];
			$activeProcessCodeArr[$frProcessCodeId] = array($frFishId, $frFishName, $frProcessCode);
		}
		//$preProcessCodeArr = array();		
		# Arranging Pre-Process
		if (sizeof($activeProcessCodeArr)>0) {
			foreach ($activeProcessCodeArr as $pCodeId=>$pca) {
				$fishId		= $pca[0];
				$fishName	= $pca[1];
				$fPCode		= $pca[2];
				# Get Valid Sequence
				$validPPSeqRecs = $this->getValidProcessSequence($fishId, $pCodeId);				
				foreach ($validPPSeqRecs as $k=>$vPCId) {
					//echo $vPCId;
					$preProcessRecs = $this->getPreProcessRecs($vPCId, $selRateListId);
					if (sizeof($preProcessRecs)>0) {
						foreach ($preProcessRecs as $ppr) {
							$preProcessId 	= $ppr[0];
							$preProcessCode = $ppr[1];
							$preProcesses	= $ppr[2];
							$preProcessCodeArr[$preProcessId] = array($fishId, $fishName, $vPCId, $fPCode, $preProcessCode, $preProcesses);
						}
					}
				} // Valid PC Loop Ends here
			} // Active PC Loop Ends here
		} // Size of Active PC Ends here		
		return $preProcessCodeArr;		
	}
	
	
	
	
	function getDaysActivePreProcessLotCodes($fromDate, $lotId, $selRateListId)
	{
		# Pre-Process Sequence
		//$this->preProcessSequence = $this->getPreProcessSequence();

		$preProcessCodeArr = array();
		$activeProcessCodeArr = array();
		# Previous Day OB
		/*$prevDayOBRecs = $this->getOBRecs($fromDate, $fishId);
		foreach ($prevDayOBRecs as $drmr) {
			$pdProcessCodeId = $drmr[0];
			$pdFishId	= $drmr[1];
			$pdProcessCode	= $drmr[2];
			$pdFishName	= $drmr[3];
			$activeProcessCodeArr[$pdProcessCodeId] = array($pdFishId, $pdFishName, $pdProcessCode);			
		}*/

		# Daily RM
		$dailyRMRecords = $this->getDailyRMProcessCodeLotRecs($fromDate, $lotId);
		foreach ($dailyRMRecords as $drmr) {
			$fProcessCodeId = $drmr[0];
			$fFishId	= $drmr[1];
			$fProcessCode	= $drmr[2];
			$fFishName	= $drmr[3];
			$activeProcessCodeArr[$fProcessCodeId] = array($fFishId, $fFishName, $fProcessCode);			
		}


		# Daiy Production Recs
		/*$dailyProdRecs = $this->getDailyProductionRecs($fromDate, $fishId);
		foreach ($dailyProdRecs as $drmr) {
			$tProcessCodeId = $drmr[0];
			$tFishId	= $drmr[1];
			$tProcessCode	= $drmr[2];
			$tFishName	= $drmr[3];
			$activeProcessCodeArr[$tProcessCodeId] = array($tFishId, $tFishName, $tProcessCode);
		}
		
		# daily Re-Process (Thawed) Recs
		$dailyThawedRecs = $this->dailyReProcessedRecs($fromDate, $fishId);
		foreach ($dailyThawedRecs as $drmr) {
			$frProcessCodeId = $drmr[0];
			$frFishId	= $drmr[1];
			$frProcessCode	= $drmr[2];
			$frFishName	= $drmr[3];
			$activeProcessCodeArr[$frProcessCodeId] = array($frFishId, $frFishName, $frProcessCode);
		}*/
		//$preProcessCodeArr = array();		
		# Arranging Pre-Process
		if (sizeof($activeProcessCodeArr)>0) {
			foreach ($activeProcessCodeArr as $pCodeId=>$pca) {
				$fishId		= $pca[0];
				$fishName	= $pca[1];
				$fPCode		= $pca[2];
				# Get Valid Sequence
				$validPPSeqRecs = $this->getValidProcessSequence($fishId, $pCodeId);				
				foreach ($validPPSeqRecs as $k=>$vPCId) {
					//echo $vPCId;
					$preProcessRecs = $this->getPreProcessRecs($vPCId, $selRateListId);
					if (sizeof($preProcessRecs)>0) {
						foreach ($preProcessRecs as $ppr) {
							$preProcessId 	= $ppr[0];
							$preProcessCode = $ppr[1];
							$preProcesses	= $ppr[2];
							$preProcessCodeArr[$preProcessId] = array($fishId, $fishName, $vPCId, $fPCode, $preProcessCode, $preProcesses);
						}
					}
				} // Valid PC Loop Ends here
			} // Active PC Loop Ends here
		} // Size of Active PC Ends here		
		return $preProcessCodeArr;		
	}
	
	
	

	# Get Prev Days OB Recs
	function getOBRecs($fromDate, $fishId)
	{
		$whr = "tdrcb.select_date=(DATE_SUB('$fromDate', INTERVAL 1 DAY))";

		if ($fishId) $whr .= " and tdrcb.fish_id=$fishId";
		$groupBy = "tdrcb.processcode_id";	
		
		$qry = "select tdrcb.processcode_id, tdrcb.fish_id, mpc.code, mf.name from t_daily_rm_cb tdrcb left join m_processcode mpc on mpc.id=tdrcb.processcode_id left join m_fish mf on mpc.fish_id = mf.id ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		//echo "Prev Day OB==<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDailyRMProcessCodeRecs($fromDate, $fishId)
	{
		$whr = "a.select_date='$fromDate' and b.effective_wt!=0";

		if ($fishId) $whr .= " and b.fish='$fishId' ";
		
		$orderBy	= " mf.name asc, mpc.code asc";
		$groupBy	= "b.fish_code";
		
		$qry = " select b.fish_code, b.fish, mpc.code, mf.name from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id left join m_processcode mpc on mpc.id=b.fish_code left join m_fish mf on mpc.fish_id = mf.id";
		if ($whr)	$qry .= " where ".$whr;		
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "Daily RM Recs===$fishId<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getDailyRMProcessCodeLotRecs($fromDate, $lotId)
	{
		$whr = "a.select_date='$fromDate' and b.effective_wt!=0";

		if ($lotId) $whr .= " and a.rm_lot_id='$lotId' ";
		
		$orderBy	= " mf.name asc, mpc.code asc";
		$groupBy	= "b.fish_code";
		
		$qry = " select b.fish_code, b.fish, mpc.code, mf.name,a.rm_lot_id,rmg.lot_Id from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id left join m_processcode mpc on mpc.id=b.fish_code left join m_fish mf on mpc.fish_id = mf.id left join t_rmreceiptgatepass rmg on rmg.id=a.rm_lot_id";
		if ($whr)	$qry .= " where ".$whr;		
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "Daily RM Recs===$fishId<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function getDailyProductionRecs($fromDate, $fishId)
	{
		$whr = "a.select_date='$fromDate'";

		if ($fishId) $whr .= " and b.fish_id='$fishId' ";
		
		$orderBy	= " mf.name asc, mpc.code asc";
		$groupBy	= "b.processcode_id";
		
		$qry = " select b.processcode_id, b.fish_id, mpc.code, mf.name from t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join m_processcode mpc on mpc.id=b.processcode_id left join m_fish mf on mpc.fish_id = mf.id";
		if ($whr)	$qry .= " where ".$whr;		
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "Daily Production Recs===$fishId<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function dailyReProcessedRecs($fromDate, $fishId)
	{
		$whr = "a.select_date='$fromDate'";

		if ($fishId) $whr .= " and a.fish_id='$fishId' ";
		
		$orderBy	= " mf.name asc, mpc.code asc";
		$groupBy	= " a.processcode_id";
		
		$qry = " select a.processcode_id, a.fish_id, mpc.code, mf.name from t_dailythawing a left join m_processcode mpc on mpc.id=a.processcode_id left join m_fish mf on mpc.fish_id = mf.id";
		if ($whr)	$qry .= " where ".$whr;		
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "Daily Thawed Recs===<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getPreProcessRecs($processCodeId, $preProcessRateListId)
	{
		$qry = "select id, code, processes, criteria from m_process where rate_list_id='$preProcessRateListId' and (processes like '$processCodeId' or processes like '$processCodeId,%') order by code asc";
		//echo "Pre-Process=><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# get All Temply inserted recs
	function getPreProcessSequence($fishId)
	{		
		$qry = "select pps.processcode_id, mf.name, mpc.code from pre_process_sequence pps left join m_processcode mpc on pps.processcode_id=mpc.id left join m_fish mf on mpc.fish_id=mf.id where pps.fish_id='$fishId' order by mf.`name` asc, pps.process_criteria desc, pps.sort_id asc, mpc.`code` asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	function getValidProcessSequence($fishId, $processCodeId)
	{
		# Get All Pre-Process sequence
		$ppRecs = $this->getPreProcessSequence($fishId);
		$processCodeArr = array();
		$i = 0;
		//$ppSeqChked = false;
		foreach ($ppRecs as $ppr) {
			$sPCId = $ppr[0];
			/*
			if ($processCodeId==$sPCId) $ppSeqChked = true;
			if ($ppSeqChked) {
				$processCodeArr[$i] = $sPCId;
				$i++;	
			}
			*/
			$processCodeArr[$i] = $sPCId;
			$i++;	
		}
		return $processCodeArr;
	}

	# Daily Active Pre-Process Codes Ends here

	# get Opening Balance (Daily Raw material closing balance = selected date opening balance)
	function getPPMOpeningBalance($processCodeId, $fromDate)
	{
		$qry = "select  pre_process_cs from t_daily_rm_cb where processcode_id='$processCodeId' and select_date=(DATE_SUB('$fromDate', INTERVAL 1 DAY)) ";
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?number_format($result[0],2,'.',''):0;
	}

	# Get All Fish records
	function getFishRecords($fromDate, $fishId=null)	
	{		
		$selFishArr = array();
		# Previous Day OB
		$prevDayOBRecs = $this->getOBRecs($fromDate, $fishId);
		foreach ($prevDayOBRecs as $drmr) {
			$pdFishId	= $drmr[1];
			$pdFishName	= $drmr[3];
			$selFishArr[$pdFishId] = $pdFishName;		
		}

		# Daily RM
		$dailyRMRecords = $this->getDailyRMProcessCodeRecs($fromDate, $fishId);
		foreach ($dailyRMRecords as $drmr) {
			$fFishId	= $drmr[1];
			$fFishName	= $drmr[3];
			$selFishArr[$fFishId] = $fFishName;
		}


		# Daiy Production Recs
		$dailyProdRecs = $this->getDailyProductionRecs($fromDate, $fishId);
		foreach ($dailyProdRecs as $drmr) {
			$tFishId	= $drmr[1];
			$tFishName	= $drmr[3];
			$selFishArr[$tFishId] = $tFishName;
		}
		
		# daily Re-Process (Thawed) Recs
		$dailyThawedRecs = $this->dailyReProcessedRecs($fromDate, $fishId);
		foreach ($dailyThawedRecs as $drmr) {			
			$frFishId	= $drmr[1];			
			$frFishName	= $drmr[3];
			$selFishArr[$frFishId] = $frFishName;		
		}	
		asort($selFishArr);	
		return $selFishArr;
	}
	
	
	function getLotRecords($fromDate, $lotId=null)	
	{		
		$selLotArr = array();
		

		# Daily RM
		$dailyRMLotRecords = $this->getDailyRMProcessCodeLotRecs($fromDate, $lotId);
		foreach ($dailyRMLotRecords as $drmr) {
			$flotId	= $drmr[4];
			$fLotName	= $drmr[5];
			$selLotArr[$flotId] = $fLotName;
		}


		
		asort($selLotArr);	
		return $selLotArr;
	}
	
	
	
	

	# Check unique all Records
	function chkRecExist($fishId=null, $selectDate)
	{
		$whr = " date='$selectDate'";

		if ($fishId)  $whr .= " and fish_id = '$fishId'";		

		$qry	= "select id from t_dailypreprocess ";

		if ($whr) $qry .= " where".$whr;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get all Pre-Process rec
	function getDPPExistRec($selDate, $fishId, $processId, $lanCenterId)
	{
		$whr = "dpp.date='$selDate' and dpp.fish_id='$fishId' and dppe.process='$processId'";

		if ($lanCenterId!="" && $lanCenterId!=0) $whr .= " and dppe.center_id='$lanCenterId'";
		else $whr .= " and dppe.center_id=0";

		$qry = "select dpp.id, dppe.id, dppe.opening_bal_qty, dppe.arrival_qty, dppe.total_qty, dppe.total_preprocess_qty, dppe.actual_yield, dppe.ideal_yield, dppe.diff_yield, dppe.center_id, dppe.available_qty from 
			t_dailypreprocess dpp left join t_dailypreprocess_entries dppe on dpp.id =dppe.dailypreprocess_main_id";
		if ($whr) $qry .= " where ".$whr;
		//echo "<br>$qry<br>";		
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8], $rec[9], $rec[10]);
		
		// MainId, processEntryId, obQty, ArrivalQty, totalQty, totPPQty, actualYield, idealY, diffYield, centerId, Avaialble Qty
	}

	# Get all Pre-Process rec
	function getDPPExistRecLotID($selDate, $fishId, $processId, $lanCenterId,$rmlotid)
	{
		$whr = "dpp.date='$selDate' and dpp.fish_id='$fishId' and dpp.rm_lot_id='$rmlotid' and dppe.process='$processId'";

		if ($lanCenterId!="" && $lanCenterId!=0) $whr .= " and dppe.center_id='$lanCenterId'";
		else $whr .= " and dppe.center_id=0";

		$qry = "select dpp.id, dppe.id, dppe.opening_bal_qty, dppe.arrival_qty, dppe.total_qty, dppe.total_preprocess_qty, dppe.actual_yield, dppe.ideal_yield, dppe.diff_yield, dppe.center_id, dppe.available_qty from 
			t_dailypreprocess_rmlotid dpp left join t_dailypreprocess_entries_rmlotid dppe on dpp.id =dppe.dailypreprocess_main_id";
		if ($whr) $qry .= " where ".$whr;
		//echo "<br>$qry<br>";		
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8], $rec[9], $rec[10]);
		
		// MainId, processEntryId, obQty, ArrivalQty, totalQty, totPPQty, actualYield, idealY, diffYield, centerId, Avaialble Qty
	}
	
	
	
	
	
	
	function getDPProcessMainId($selDate, $fishId)
	{
		$qry = "select id from t_dailypreprocess where fish_id='$fishId' and date='$selDate'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}
	function getDPProcessMainIdRMlotid($selDate, $fishId, $rmLotID)
	{
		$qry = "select id from t_dailypreprocess_rmlotid where fish_id='$fishId' and date='$selDate' and rm_lot_id='$rmLotID'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}

	# Check any Processor entry need
	function chkDailyPPProcessorEntry($selDate)
	{
		
		$qry = "select b.id, d.id from t_dailypreprocess b left join t_dailypreprocess_entries c on b.id =c.dailypreprocess_main_id left join t_dailypreprocess_processor_qty d on c.id=d.dailypreprocess_entry_id where b.date='$selDate'";
		//$qry = "select b.id, d.id from t_dailypreprocess b left join t_dailypreprocess_entries c on b.id =c.dailypreprocess_main_id left join t_dailypreprocess_processor_qty d on c.id=d.dailypreprocess_entry_id where b.date='$selDate' and d.id is null";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check any Processor entry RMLotId need
	function chkDailyPPProcessorEntryRMLotId($selDate)
	{
		$qry = "select b.id, d.id from t_dailypreprocess_rmlotid b left join t_dailypreprocess_entries_rmlotid c on b.id =c.dailypreprocess_main_id left join t_dailypreprocess_processor_qty_rmlotid d on c.id=d.dailypreprocess_entry_id where b.date='$selDate'";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	
	# Check Duplicate Entry Exist
	function chkDupEntryExist($selectDate)
	{
		$qry	= "select id, confirmed from t_dailypreprocess where date='$selectDate'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array(true,$result[0][1]):false;
	}

	# Filter m_process table using fish id 	 
	function preProcessRecs($filterId,$selRateList,$rmLotId)
	{	
		$mPreProcessArr = array();

		//$qry	= " select a.id, a.code, SUBSTRING_INDEX(a.processes,',',1) as pfrom, a.processes, a.fish_id, b.name from m_process a left join m_fish b on a.fish_id=b.id where a.fish_id='$filterId' and a.rate_list_id='$selRateList' order by a.processes asc, a.criteria desc, b.name asc, a.code asc";
		$qry = "select a.id, a.code, SUBSTRING_INDEX(a.processes,',',1) as pfrom, a.processes, a.fish_id, b.name from m_process a left join m_fish b on a.fish_id=b.id join pre_process_sequence frs on frs.processcode_id = substring(a.processes,1,instr(a.processes,',')-1) join pre_process_sequence tos on tos.processcode_id = substring(a.processes,length(a.processes)-instr(reverse(a.processes),',')+2) where a.rate_list_id='$selRateList'"; 
		if($filterId!="")
		{
		$qry.= "and a.fish_id='$filterId'"; 
		}
		if($rmLotId!="")
		{
		$qry.= "and frs.processcode_id in (SELECT a.process_code_id FROM `t_rmweightaftergradingdetails` a left join t_rmweightaftergrading b on a.weightment_grading_id=b.id where b.rmLotId='$rmLotId')";
		}
		$qry.= "order by frs.process_criteria desc, frs.sort_id asc, tos.process_criteria desc, tos.sort_id asc";
	//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		foreach ($result as $ppr) {
			$preProcessId 	= $ppr[0];
			$preProcessCode = $ppr[1];
			$preProcessFrom = $ppr[2];
			$preProcesses	= $ppr[3];
			$fishId		= $ppr[4];
			$fishName	= $ppr[5];
			$mPreProcessArr[$preProcessId] = array($fishId, $fishName, $preProcessFrom, $fPCode, $preProcessCode, $preProcesses);	
		}
		return $mPreProcessArr;
	}

	
	function preProcessRecs_old($filterId, $selRateList)
	{	
		$mPreProcessArr = array();

		//$qry	= " select a.id, a.code, SUBSTRING_INDEX(a.processes,',',1) as pfrom, a.processes, a.fish_id, b.name from m_process a left join m_fish b on a.fish_id=b.id where a.fish_id='$filterId' and a.rate_list_id='$selRateList' order by a.processes asc, a.criteria desc, b.name asc, a.code asc";
		$qry = "select a.id, a.code, SUBSTRING_INDEX(a.processes,',',1) as pfrom, a.processes, a.fish_id, b.name from m_process a left join m_fish b on a.fish_id=b.id join pre_process_sequence frs on frs.processcode_id = substring(a.processes,1,instr(a.processes,',')-1) join pre_process_sequence tos on tos.processcode_id = substring(a.processes,length(a.processes)-instr(reverse(a.processes),',')+2) where a.fish_id='$filterId' and a.rate_list_id='$selRateList' order by frs.process_criteria desc, frs.sort_id asc, tos.process_criteria desc, tos.sort_id asc";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		foreach ($result as $ppr) {
			$preProcessId 	= $ppr[0];
			$preProcessCode = $ppr[1];
			$preProcessFrom = $ppr[2];
			$preProcesses	= $ppr[3];
			$fishId		= $ppr[4];
			$fishName	= $ppr[5];
			$mPreProcessArr[$preProcessId] = array($fishId, $fishName, $preProcessFrom, $fPCode, $preProcessCode, $preProcesses);	
		}
		return $mPreProcessArr;
	}
	
	
	
	
	
	
	
	# Check Duplicate Entry Exist
	function chkDaysEntryConfirmed($selectDate)
	{
		$qry	= "select id, confirmed from t_dailypreprocess where date='$selectDate' and confirmed='Y'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# get Date Wise Processor
	function getSelProcessor($selDate)
	{
		$qry	= "select c.preprocessor_id, mp.name from t_dailypreprocess a, t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_preprocessor mp where a.id = b.dailypreprocess_main_id and b.id=c.dailypreprocess_entry_id and c.preprocessor_id=mp.id and a.date='$selDate' and c.preprocess_qty!=0 group by c.preprocessor_id ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# packing Qty (PRODUCTION)  ---------------------- 
	function getPkgQty($processCodeId, $fromDate)
	{
		# Daily Frzn Packing records
		$qry = " select 
				a.id, a.select_date, a.unit, b.freezing_stage_id, b.eucode_id, b.brand_id, b.frozencode_id, b.mcpacking_id, sum(c.number_mc), sum(c.number_loose_slab), mf.filled_wt, mcp.number_packs, mf.code as frznCode, mf.actual_filled_wt
			from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade c on b.id=c.entry_id
				left join m_frozenpacking mf on b.frozencode_id=mf.id left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			where 
				a.select_date='$fromDate' and b.processcode_id='$processCodeId'
			group by b.frozencode_id
			";
		//echo "<br>$qry<br>"; 
		$dailyFrznPkgRecs = $this->databaseConnect->getRecords($qry);
		$totalGrossWt = 0;		
		foreach ($dailyFrznPkgRecs as $dfpr) {
			$numMc		= $dfpr[8];
			$numLoosePack 	= $dfpr[9];
			$declaredFilledWt = $dfpr[10]; // Filled Wt
			$actualFilledWt = $dfpr[13];
			$frznCodeFilledWt = ($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt;
			//$frznCodeFilledWt = $declaredFilledWt;
			$numMCPack	= $dfpr[11];
			
			$mcActualWt = ($frznCodeFilledWt*$numMCPack*$numMc);			
			$lcActualWt = $numLoosePack*$frznCodeFilledWt;
			$grossWt = $mcActualWt+$lcActualWt;
			$totalGrossWt += $grossWt;			
		}
		//echo "<b>$totalGrossWt</b><br>";
		return ($totalGrossWt!=0)?number_format($totalGrossWt,2,'.',''):0;
	}

	# Todays PP M Qty
	function getTodaysPPQty($processCodeId, $selectDate)
	{
		$uptdQry = "";
		//if ($processEntryId!=0) $uptdQry = " and b.id!=$processEntryId";

		$qry = "
			select c.id, c.processes , c.criteria, sum(b.total_preprocess_qty), sum(b.total_qty), b.actual_yield, b.ideal_yield, b.diff_yield from 
			t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
			where b.dailypreprocess_main_id=a.id 
				and a.date='$selectDate' and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '%,$processCodeId') $uptdQry group by b.process order by c.processes asc
			";
		//echo "<br><b>PPQTY Pre-Process=$processCodeId</b><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		$totPreProcessedQty = 0;
		if (sizeof($result)>0) {			
			foreach ($result as $r) {				
				$preProcessedQty 	= $r[3];
				$totPreProcessedQty	+= $preProcessedQty;
			}
		}		
		return $totPreProcessedQty;
	}

	# PPM Closing Stock Qty (
	function getTotalCSQty($processCodeId, $fromDate)
	{
		//+re_process_cs On jan 9 moni sir asked to removed
		$qry = "select (pre_process_cs+closing_balance) from t_daily_rm_cb where processcode_id='$processCodeId' and select_date='$fromDate'";
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?number_format($result[0],2,'.',''):0;
	}

	# Thawed Material Qty (RPM-ReProcessed Qty) -------------------------- STARTS HERE
	function getRPMQty($processCodeId, $fromDate)
	{
		# Daily Frzn Packing records
		$qry = " select 
				tdt.id, tdt.select_date, tdt.frozencode_id, tdt.mcpacking_id, sum(tdtg.number_mc_thawing), sum(tdtg.number_loose_slab_thawing), mf.filled_wt, mf.actual_filled_wt
			from 
				t_dailythawing tdt left join t_dailythawing_grade tdtg on tdt.id=tdtg.main_id
				left join m_frozenpacking mf on tdt.frozencode_id=mf.id
			where 
				tdt.select_date='$fromDate' and tdt.processcode_id='$processCodeId' and tdt.flag=1
			group by tdt.frozencode_id
			";
		//echo "<br>$qry<br>"; 
		$dailyThawedRecs = $this->databaseConnect->getRecords($qry);
		$totalGrossWt = 0;
		foreach ($dailyThawedRecs as $dtr) {
			$numMc		= $dtr[4];
			$numLoosePack 	= $dtr[5];
			$declaredFilledWt = $dtr[6]; // Filled Wt
			$actualFilledWt = $dtr[7];
			$frznCodeFilledWt = ($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt;
			//$frznCodeFilledWt = $declaredFilledWt;
			$mcActualWt = $frznCodeFilledWt*$numMc;
			$eachPackWt = $frznCodeFilledWt/$numMc;
			$lcActualWt = $numLoosePack*$eachPackWt;
			$grossWt = $mcActualWt+$lcActualWt;
			$totalGrossWt += $grossWt;
			//echo "Out=$numMc, $numLoosePack, $frznCodeFilledWt, Gross=$grossWt<br>";
		}
		return ($totalGrossWt!=0)?number_format($totalGrossWt,2,'.',''):0;
	}
	
	function getTotalPPMOBQty($processCodeId, $fromDate)
	{
		$qry = "select  (pre_process_cs+closing_balance+re_process_cs) from t_daily_rm_cb where processcode_id='$processCodeId' and select_date=(DATE_SUB('$fromDate', INTERVAL 1 DAY)) ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?number_format($result[0],2,'.',''):0;
	}

	# Available Qty check
	function chkZeroAvailableQtyExist($selDate)
	{
		$qry = "select c.id from t_dailypreprocess b left join t_dailypreprocess_entries c on b.id =c.dailypreprocess_main_id where b.date='$selDate' and total_preprocess_qty<=0";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Available Qty check
	function chkZeroAvailableQtyExistRMLotID($selDate)
	{
		$qry = "select c.id from t_dailypreprocess_rmlotid b left join t_dailypreprocess_entries_rmlotid c on b.id =c.dailypreprocess_main_id where b.date='$selDate' and total_preprocess_qty<=0";	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	function processCodeSequence()
	{
		# Get Valid Sequence
				$validPPSeqRecs = $this->getValidProcessSequence($fishId, $pCodeId);				
				foreach ($validPPSeqRecs as $k=>$vPCId) {
					//echo $vPCId;
					$preProcessRecs = $this->getPreProcessRecs($vPCId, $selRateListId);
					if (sizeof($preProcessRecs)>0) {
						foreach ($preProcessRecs as $ppr) {
							$preProcessId 	= $ppr[0];
							$preProcessCode = $ppr[1];
							$preProcesses	= $ppr[2];
							$preProcessCodeArr[$preProcessId] = array($fishId, $fishName, $vPCId, $fPCode, $preProcessCode, $preProcesses);
						}
					}
				} // Valid PC Loop Ends here
	}

	
	# Days DPP Entries
	function getDailyPreProcessRecs($recordsDate)
	{		
		$whr		= " a.date='".$recordsDate."'" ;
		$orderBy	= "mf.name asc, frs.process_criteria desc, frs.sort_id asc, tos.process_criteria desc, tos.sort_id asc";
		
		//$qry		= "select a.id, a.fish_id, b.id, b.process, b.available_qty, mp.processes, SUBSTRING_INDEX(mp.processes,',',1) as pfrom, b.arrival_qty as actualUQ, b.total_qty, b.total_preprocess_qty, mp.code from t_dailypreprocess a left join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id left join m_process mp on mp.id=b.process ";
		$qry		= "select a.id, a.fish_id, b.id, b.process, b.available_qty, mp.processes, SUBSTRING_INDEX(mp.processes,',',1) as pfrom, b.arrival_qty as actualUQ, b.total_qty, b.total_preprocess_qty, mp.code from t_dailypreprocess a left join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id left join m_process mp on mp.id=b.process 
			left join m_fish mf on a.fish_id=mf.id	
			join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2)";
		
		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;				
		//echo "<br>$qry";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# CEQTY Arrival Qty = (Effective Qty + Adjust Qty + Local qty+ Wastage Qty + Soft Qty
	//select a.id, b.fish, b.fish_code, b.effective_wt, $totalQty, sum(b.net_wt), sum(b.adjust), sum(b.local_quantity), sum(b.wastage), sum(b.soft), sum(b.grade_count_adj
	function dailyCatchRMArrivalQty($processFrom, $selectedDate)
	{
		//sum(a.effective_wt+b.adjust+b.local_quantity+b.wastage+b.soft removed +a.wastage)
		$qry	= "select sum(a.effective_wt+a.adjust+a.local_quantity+a.soft) as netWt from t_dailycatchentry a, t_dailycatch_main b where b.id=a.main_id and b.select_date='$selectedDate' and a.fish_code='$processFrom' group by a.fish_code";	
		//echo "<br>$qry";		
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?$rec[0][0]:"";
	}

	function updateDPPEntryAvailableQty($dppEntryId, $todaysAvailableQty)
	{
		$qry	= " update t_dailypreprocess_entries set available_qty='$todaysAvailableQty' where id='$dppEntryId'";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# (Opening Balance + Arrival + PPM + RPM)-(Prodn + Total CS)
	function disAvailableQtyCalc($processFrom, $sDate)
	{
		$dcEntryWeight		= $this->dailyCatchRMArrivalQty($processFrom, $sDate);
		$totalPPMOBQty		= $this->getTotalPPMOBQty($processFrom, $sDate);		
		$todaysPPMQty		= $this->getTodaysPPQty($processFrom, $sDate);
		$todaysRPMQty		= $this->getRPMQty($processFrom, $sDate);
		$todaysProductionQty 	= $this->getPkgQty($processFrom, $sDate);
		$totalCSQty 		= $this->getTotalCSQty($processFrom, $sDate);
		//$todaysAvailableQty = ($totalPPMOBQty+$dcEntryWeight+$todaysPPMQty+$todaysRPMQty)-($todaysProductionQty+$totalCSQty);
		
		$displayCalc	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";
		
		$displayCalc	.= "<tr bgcolor=#fffbcc align=center class=listing-head>";
		$displayCalc	.= "<td colspan=6>( OB + ARR + PPM + RPM - Prodn - Tot CS )</td>";
		$displayCalc	.= "</tr>";
		
		$displayCalc	.= "<tr bgcolor=#fffbcc align=center class=listing-head>";
		$displayCalc	.= "<td>OB</td><td>ARR</td><td>PPM</td><td>RPM</td><td>Prodn</td><td>Tot CS</td>";
		$displayCalc	.= "</tr>";
		$displayCalc	.= "<tr bgcolor=#fffbcc align=center class=listing-item>";
		$displayCalc	.= "<td>$totalPPMOBQty</td><td>$dcEntryWeight</td><td>$todaysPPMQty</td><td>$todaysRPMQty</td><td>$todaysProductionQty</td><td>$totalCSQty</td>";
		$displayCalc	.= "</tr>";
		$displayCalc	.= "</table>";
		return $displayCalc;
	}
	
	function getLotIdAfterGrading()
	{
		$qry	= "SELECT b.id,concat(b.alpha_character,b.rm_lotid) FROM `t_rmweightaftergrading` a left join t_manage_rm_lotid b on a.rmLotId = b.id";
			//echo $qry;		
			$result	= $this->databaseConnect->getRecords($qry);
			return $result;
	}
	# add daily preprocess with rmlotid
	function addDailyPreProcessRmLOtID($rmLotId,$fishId,$selectDate)
	{
		$qry	= "insert into t_dailypreprocess_rmlotid(rm_lot_id,fish_id,date) values('$rmLotId','$fishId', '$selectDate')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#Insert datas for t_dailypreprocess_entries_rmlotid tablw 
	
	function addPreProcessRmLOtID($preProcessId, $openingBalQty, $todayArrivalQty, $totalQty, $totalPreProcessQty, $actualYield, $idealYield, $diffYield, $lastInsertedId, $lanCenterId, $availableQty, $autoGenCalc)
	{
		$qry	= " insert into t_dailypreprocess_entries_rmlotid (dailypreprocess_main_id, process, opening_bal_qty, arrival_qty, total_qty, total_preprocess_qty, actual_yield, ideal_yield, diff_yield, center_id, available_qty, auto_gen_calc) values
				   ( '$lastInsertedId','$preProcessId', '$openingBalQty', '$todayArrivalQty', '$totalQty', '$totalPreProcessQty', '$actualYield', '$idealYield', '$diffYield','$lanCenterId', '$availableQty', '$autoGenCalc')";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#Insert datas for t_dailypreprocess_processor_qty_rmlotid
	function addPreProcesserQtyRmLOtID($processorId,$preProcessorQty,$preProcessEntryLastId)
	{
		$qry	= " insert into t_dailypreprocess_processor_qty_rmlotid(dailypreprocess_entry_id, preprocessor_id, preprocess_qty) values('$preProcessEntryLastId', '$processorId', '$preProcessorQty')";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	#Finding Pre Processing Qty based on pre processor
	function findPreProcessorRMlotidRec($dailyPreProcessEntryId, $masterPreProcessorId)
	{
		$qry	= "select id, dailypreprocess_entry_id, preprocessor_id, preprocess_qty, paid, payment_confirm  from t_dailypreprocess_processor_qty_rmlotid where  dailypreprocess_entry_id=$dailyPreProcessEntryId and preprocessor_id='$masterPreProcessorId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function lastIdInPreprocess()
	{
		$qry	= "select id from t_dailypreprocess order by id  desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function lastIdInPreprocessRmLotID()
	{
		$qry	= "select id from t_dailypreprocess_rmlotid order by id desc limit 1 ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
}	
?>