<?php
Class ProductionAnalysisReport
{
	/****************************************************************
	This class deals with all the operations relating to Production Analysis Report
	*****************************************************************/
	var $databaseConnect;
	var $tempPCodeRecs;


	//Constructor, which will create a db instance for this class
	function ProductionAnalysisReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}


	# Get All Pre-Process Code recs 
	/*
	Original Recs
	$qry = "select a.id, a.fish_id, a.code, a.b_weight, a.grade_unit_raw, a.count_unit_frozen, a.available_option, a.frozen_available, a.grade_unit_frozen, a.count_unit_raw, b.name from m_processcode a left join m_fish b on a.fish_id = b.id order by b.name asc, a.code asc ";
	*/
	function processCodeRecs($fromDate, $tillDate, $fishId)
	{
		$processCodeArr = array();

		# Previous Day OB
		$prevDayOBRecs = $this->getOBRecs($fromDate, $fishId);
		foreach ($prevDayOBRecs as $drmr) {
			$pdProcessCodeId = $drmr[0];
			$pdFishId	= $drmr[1];
			$pdProcessCode	= $drmr[2];
			$pdFishName	= $drmr[3];
			$processCodeArr[$pdProcessCodeId] = array($pdProcessCodeId, $pdFishId, $pdProcessCode, $pdFishName);
		}		
		# Daily RM
		$dailyRMRecords = $this->getDailyRMProcessCodeRecs($fromDate, $tillDate, $fishId);
		foreach ($dailyRMRecords as $drmr) {
			$fProcessCodeId = $drmr[0];
			$fFishId	= $drmr[1];
			$fProcessCode	= $drmr[2];
			$fFishName	= $drmr[3];
			$processCodeArr[$fProcessCodeId] = array($fProcessCodeId, $fFishId, $fProcessCode, $fFishName);
		}
		#Daily PreProcess
		$dailyPPRecs = $this->getDailyPPProcessCodes($fromDate, $tillDate, $fishId);
		foreach ($dailyPPRecs as $k=>$drmr) {
			$sProcessCodeId = $drmr[0];
			$sFishId	= $drmr[1];
			$sProcessCode	= $drmr[2];
			$sFishName	= $drmr[3];
			$processCodeArr[$sProcessCodeId] = array($sProcessCodeId, $sFishId, $sProcessCode, $sFishName);
		}
		# Daiy Production Recs
		$dailyProdRecs = $this->getDailyProductionRecs($fromDate, $tillDate, $fishId);
		foreach ($dailyProdRecs as $drmr) {
			$tProcessCodeId = $drmr[0];
			$tFishId	= $drmr[1];
			$tProcessCode	= $drmr[2];
			$tFishName	= $drmr[3];
			$processCodeArr[$tProcessCodeId] = array($tProcessCodeId, $tFishId, $tProcessCode, $tFishName);
		}
		
		# daily Re-Process (Thawed) Recs
		$dailyThawedRecs = $this->dailyReProcessedRecs($fromDate, $tillDate, $fishId);
		foreach ($dailyThawedRecs as $drmr) {
			$frProcessCodeId = $drmr[0];
			$frFishId	= $drmr[1];
			$frProcessCode	= $drmr[2];
			$frFishName	= $drmr[3];
			$processCodeArr[$frProcessCodeId] = array($frProcessCodeId, $frFishId, $frProcessCode, $frFishName);
		}	
		
		# Insert temp table recs
		if (sizeof($processCodeArr)>0) {
			$this->createTempTable();
			foreach ($processCodeArr as $pcr) {
				$processCodeId	= $pcr[0];
				$fishId		= $pcr[1];
				$processCode	= $pcr[2];
				$fishName	= $pcr[3];
				//echo "$processCodeId, $fishId, $processCode, $fishName";
				$insertTempRecs = $this->insertTempProdnAnlysReportRec($processCodeId, $fishId, $processCode, $fishName);
			}
		}
		$result =  $this->getTempProdAnlysReport();
		# Assign the Result to Member Variables
		$this->tempPCodeRecs = $result;	
		return $result;
	}
	
	# Get Prev Days OB Recs
	function getOBRecs($fromDate, $fishId)
	{
		$whr = "tdrcb.select_date=(DATE_SUB('$fromDate', INTERVAL 1 DAY))";

		//if ($fishId) $whr .= " and tdrcb.fish_id=$fishId";
		if ($fishId) $whr .= " and tdrcb.fish_id in ($fishId) ";

		$groupBy = "tdrcb.processcode_id";	
		
		$qry = "select tdrcb.processcode_id, tdrcb.fish_id, mpc.code, mf.name from t_daily_rm_cb tdrcb left join m_processcode mpc on mpc.id=tdrcb.processcode_id left join m_fish mf on mpc.fish_id = mf.id ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		//echo "Prev Day OB==<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Create temp table
	function createTempTable()
	{
		$qry = "create temporary table production_analysis_report( 
			`id` int(2) NOT NULL auto_increment,
			`processcode_id` int(2) default NULL,
			`fish_id` int(2) default NULL,
			`processcode` varchar(50) default NULL,
			`fish_name` varchar(50) default NULL,
			PRIMARY KEY  (`id`),
			UNIQUE KEY `pcodeId` (`processcode_id`)
			)";
		//echo $qry;
		$result =  $this->databaseConnect->createTable($qry);
		return $result;
	}

	# Insert Temp recs
	function insertTempProdnAnlysReportRec($processCodeId, $fishId, $processCode, $fishName)
	{
		$qry = "insert into production_analysis_report (`processcode_id`, `fish_id`, `processcode`, `fish_name`) values('$processCodeId', '$fishId', '$processCode', '$fishName')";
		//echo "<br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	# get All Temply inserted recs
	function getTempProdAnlysReport()
	{		
		$qry = "select par.`processcode_id`, par.`fish_id`, par.`processcode`, par.`fish_name` from production_analysis_report par left join pre_process_sequence pps on par.processcode_id=pps.processcode_id  order by par.`fish_name` asc, pps.process_criteria desc, pps.sort_id asc, par.`processcode` asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}
	
	# Get Selected Process Code Recs while listing
	function getSelProcessCodeRecs($fishId)
	{	
		$qry = "select par.`processcode_id`, par.`fish_id`, par.`processcode`, par.`fish_name` from production_analysis_report par left join pre_process_sequence pps on par.processcode_id=pps.processcode_id where par.`fish_id`='$fishId' order by par.`fish_name` asc, pps.process_criteria desc, pps.sort_id asc, par.`processcode` asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	function getMaxSizeOfPCRecs()
	{
		$qry = "select numCount from (select count(*) as numCount  from production_analysis_report par  group by par.`fish_id`) as x order by numCount desc  ";
		//echo "<br>$qry";
		$result =  $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	function getDailyRMProcessCodeRecs($fromDate, $tillDate, $fishId)
	{
		$whr = "a.select_date>='$fromDate' and a.select_date<='$tillDate' and b.fish is not null";

		//if ($fishId) $whr .= " and b.fish='$fishId' ";
		if ($fishId) $whr .= " and b.fish in ($fishId) ";
		
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

	function getDailyPPProcessCodes($fromDate, $tillDate, $fishId)
	{
		$whr = "a.date>='$fromDate' and a.date<='$tillDate' and mp.id is not null and b.process is not null ";

		//if ($fishId) $whr .= " and a.fish_id='$fishId' ";
		if ($fishId) $whr .= " and a.fish_id in ($fishId) ";
				
		$groupBy	= " b.process ";
		
		$qry = " select mp.processes from t_dailypreprocess a left join t_dailypreprocess_entries b on a.id=b.dailypreprocess_main_id left join m_process mp on mp.id=b.process";

		if ($whr)	$qry .= " where ".$whr;		
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "PreProcess==$fishId<br>$qry<br/>";
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array();
		if (sizeof($result)>0) {
			foreach ($result as $ppr) {
				$processSeq = $ppr[0];
				$process = explode(",",$processSeq);
				$firstProcessCodeId = $process[0];
				list($fProcessCodeId, $fSelFishId, $fProcessCode, $fFishName) = $this->getProcessCodeRec($firstProcessCodeId);
				$resultArr[$firstProcessCodeId] = array($fProcessCodeId, $fSelFishId, $fProcessCode, $fFishName);
				$secondProcessCodeId = $process[sizeof($process)-1];
				list($processCodeId, $selFishId, $processCode, $fishName) = $this->getProcessCodeRec($secondProcessCodeId);

				$resultArr[$secondProcessCodeId] = array($processCodeId, $selFishId, $processCode, $fishName);
				//echo "$firstProcessCodeId=$fProcessCodeId, $fSelFishId, $fProcessCode, $fFishName<br>";
				//echo "$secondProcessCodeId=$processCodeId, $selFishId, $processCode, $fishName<br>";
				
			} // For Loop Ends here
		}
		
		return $resultArr;
	}

	function getDailyProductionRecs($fromDate, $tillDate, $fishId)
	{
		$whr = "a.select_date>='$fromDate' and a.select_date<='$tillDate' and b.processcode_id is not null ";

		//if ($fishId) $whr .= " and b.fish_id='$fishId' ";
		if ($fishId) $whr .= " and b.fish_id in ($fishId) ";
		
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

	function dailyReProcessedRecs($fromDate, $tillDate, $fishId)
	{
		$whr = "a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.processcode_id is not null ";

		//if ($fishId) $whr .= " and a.fish_id='$fishId' ";
		if ($fishId) $whr .= " and a.fish_id in ($fishId) ";
		
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
	

	function getProcessCodeRec($processCodeId)
	{
		$qry = "select mpc.id, mpc.fish_id, mpc.code, mf.name from m_processcode mpc left join m_fish mf on mpc.fish_id = mf.id where mpc.id='$processCodeId'";
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2], $rec[3]);
	}


	# get Opening Balance (Daily Raw material closing balance = selected date opening balance)
	function getRMOpeningBalance($processCodeId, $fromDate,$companyId,$unitId)
	{		
		$qry = "select  sum(closing_balance+pre_process_cs+re_process_cs), sum(pre_process_cs) as ppCB, sum(closing_balance) as prdnCB, sum(re_process_cs) as rpmCB from t_daily_rm_cb where processcode_id='$processCodeId' and company_id='$companyId' and unit_id='$unitId' and select_date=(DATE_SUB('$fromDate', INTERVAL 1 DAY)) group by processcode_id";
		//$qry = "select  sum(closing_balance+pre_process_cs+re_process_cs), sum(pre_process_cs) as ppCB, sum(closing_balance) as prdnCB, sum(re_process_cs) as rpmCB from t_daily_rm_cb where processcode_id='$processCodeId' and select_date=(DATE_SUB('$fromDate', INTERVAL 1 DAY)) group by processcode_id";
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecord($qry);
		$displayOB = "";
		if (sizeof($result)>0) {
			$displayOB= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>CB/PPM</td><td>CB/PROD</td><td>CB/RPM</td></tr>";
			$displayOB .= "<tr bgcolor=#fffbcc><td class=listing-item align=right>$result[1]</td><td class=listing-item align=right>$result[2]</td><td class=listing-item align=right>$result[3]</td></tr>";
			$displayOB .= "</table>";
		}
		//echo $displayOB;
		return array(number_format($result[0],2,'.',''), $displayOB);
	}

	# get Opening Balance (Daily Raw material closing balance = selected date opening balance)
	function getRMClosingBalance($processCodeId, $fromDate)
	{
		$qry = "select  closing_balance from t_daily_rm_cb where processcode_id='$processCodeId' and select_date='$fromDate' ";
		//echo "<br>$qry";		
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# get Opening Balance (Daily Raw material closing balance = selected date opening balance)
	function getPPRMClosingBalance($processCodeId, $fromDate)
	{
		$qry = "select  pre_process_cs from t_daily_rm_cb where processcode_id='$processCodeId' and select_date='$fromDate' ";
		//echo "<br>$qry";		
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# get Re-Process Closing Balance
	function getRPRMClosingBalance($processCodeId, $fromDate)
	{
		$qry = "select re_process_cs from t_daily_rm_cb where processcode_id='$processCodeId' and select_date='$fromDate' ";
		//echo "<br>$qry";		
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Get Arival Qty from Daily Catch Entry // Total Qty = EffectiveQty + AdjustQty
	# Arrival Qty = (EQ+Adjust+local+soft)
	function getRMArrivalQty($processCodeId, $fromDate, $tillDate,$companyId,$unitId)
	{
		//+b.wastage
		$qry = " select sum(b.effective_wt+b.adjust+b.local_quantity+b.soft) as totalQty, sum(b.effective_wt), sum(b.adjust), sum(b.local_quantity), sum(b.wastage) , sum(b.soft) from t_dailycatch_main a, t_dailycatchentry b where a.id=b.main_id and b.fish_code='$processCodeId' and a.select_date>='$fromDate' and a.select_date<='$tillDate'  and a.billing_company_id='$companyId'  and a.unit='$unitId' group by b.fish_code";
		//echo "Arrival Qty===<br>$qry<br>";	
		$result	= $this->databaseConnect->getRecord($qry);
		$displayArrival = "";
		if (sizeof($result)>0) {
			$displayArrival .= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td rowspan=2>Effective Wt</td><td colspan=3>Local Qty</td><td rowspan=2>Adj Wt</td></tr>";
			$displayArrival .= "<tr bgcolor=#fffbcc align=center class=listing-head><td>Local</td><td>Wastage</td><td>Soft</td></tr>";
			$displayArrival .= "<tr bgcolor=#fffbcc><td class=listing-item align=right>$result[1]</td><td class=listing-item align=right>$result[3]</td><td class=listing-item align=right>$result[4]</td><td class=listing-item align=right>$result[5]</td><td class=listing-item align=right>$result[2]</td></tr>";
			$displayArrival .= "</table>";
		}
		
		return array(number_format($result[0],2,'.',''), $displayArrival);
	}

	# Get RM Pre-Processed Qty ---------------------------------------
	function getRMPreProcessedQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId,$companyId,$unitId)
	{
		$ppmArr = array();
		//#Criteria Calculation 1=> From/ 0=>To
		list($preProcessId, $preProcessSequence, $ppCriteria, $ppQty, $totProcessingQty, $actualYield, $idealYield, $diffYield, $ppmArr) = $this->getDailyPPQtyRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId);
		//echo "<br>$preProcessId, $preProcessSequence, $ppCriteria, $ppQty, $totProcessingQty, $actualYield, $idealYield, $diffYield";
		# Yield Avg
		//$ppYieldAvg = $this->preProcessYieldAverage($preProcessId);
		//$ppYieldQty = number_format(($ppQty/($ppYieldAvg/100)),2,'.','');		
		$process = explode(",",$preProcessSequence);		
		$selQty = "";	
		$displayPPMCalc = "";		
		if ($process[0]==$processCodeId && $ppQty!=0) {
			if ($process[0]==$process[sizeof($process)-1]) {				
				$selQty = $ppQty;
				$displayPPMCalc = $this->displayPPMCalc($ppmArr);
			} else $selQty = "";
		} else if ($process[sizeof($process)-1]==$processCodeId && $ppQty!=0) {
			if ($ppCriteria==1) {				
				list($selQty, $ppmArr) = $this->getPPQtyRecs($process[sizeof($process)-1], $fromDate, $tillDate, $fishId);
				$displayPPMCalc = $this->displayPPMCalc($ppmArr);
			} else {
				$selQty = $ppQty;
				$displayPPMCalc = $this->displayPPMCalc($ppmArr);
			}
		}
		//echo "<br>Out=$preProcessId, Sequence=$preProcessSequence, Criteria=$ppCriteria, PPQty=$ppQty, Avg=$ppYieldAvg, Qty=$selQty, ProcessCODEId=$processCodeId, FirstP=".$process[0].", SecondP=".$process[sizeof($process)-1]."<br>";
		return  array($selQty, $displayPPMCalc);
	}

	function getDailyPPQtyRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId)
	{			
		
		$qry1 = "select c.id as id, c.processes as processes, c.criteria as criteria, sum(b.total_preprocess_qty) as total_preprocess_qty, b.arrival_qty as actualUsedQty, b.actual_yield as actual_yield, b.ideal_yield as ideal_yield, b.diff_yield as diff_yield, c.code as preProcessCode, sum(b.total_qty) as totalQty,b.process as process from t_dailypreprocess a, t_dailypreprocess_entries b, m_process c where b.dailypreprocess_main_id=a.id and a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate') and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '%,$processCodeId') and a.company_id='$companyId' and a.unit_id='$unitId'"; 
		$qry2= "select c.id as id, c.processes as processes, c.criteria as criteria, sum(b.total_preprocess_qty) as total_preprocess_qty, b.arrival_qty as actualUsedQty, b.actual_yield as actual_yield, b.ideal_yield as ideal_yield, b.diff_yield as diff_yield, c.code as preProcessCode, sum(b.total_qty) as totalQty,b.process as process from t_dailypreprocess_rmlotid a, t_dailypreprocess_entries_rmlotid b, m_process c where b.dailypreprocess_main_id=a.id and a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate') and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '%,$processCodeId') and a.company_id='$companyId' and a.unit_id='$unitId'"; 
		
		$qry="select * from ($qry1 union all $qry2) dum ";
		$qry.="group by process order by processes asc";
		//group by b.process order by c.processes asc";
		
		//echo $qry.'<br/>';
		
		
		//$qry = "select c.id, c.processes , c.criteria, sum(b.total_preprocess_qty), b.arrival_qty as actualUsedQty, b.actual_yield, b.ideal_yield, b.diff_yield, c.code as preProcessCode, sum(b.total_qty) as totalQty from t_dailypreprocess a, t_dailypreprocess_entries b, m_process c where b.dailypreprocess_main_id=a.id and a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate') and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '%,$processCodeId') and a.company_id='$companyId' and a.unit_id='$unitId' group by b.process order by c.processes asc";
		//echo "<br>getDailyPPQtyRecs==><b>Pre-Process=$processCodeId</b><br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		$rSize	= sizeof($result);
		$ppmArr = array();
		if (sizeof($result)>0) {
			$totPreProcessedQty = 0;
			$totProcessingQty = 0;
			$totActualY	= 0;
			$totIdealY	= 0;
			foreach ($result as $r) {				
				$preProcessedQty 	= $r[3];
				$totPreProcessedQty	+= $preProcessedQty;
				$processingQty   = $r[4];
				$totProcessingQty += $processingQty;
				$actualY	 = $r[5];
				$totActualY 	+= $actualY;
				$idealY		 = $r[6];
				$totIdealY 	+= $idealY;	
				$preProcessCode = trim($r[8]);
				$ppmArr[$preProcessCode] = array($processingQty, $preProcessedQty);
			}
			$sActualYield = number_format(($totActualY/$rSize),2,'.','');
			$sIdealYield  = number_format(($totIdealY/$rSize),2,'.','');
			$diffYieldCalc = number_format(($sActualYield-$sIdealYield),2,'.','');
		}
		//print_r($ppmArr);
		return array($result[0][0], $result[0][1], $result[0][2], $totPreProcessedQty, $totProcessingQty, $sActualYield, $sIdealYield, $diffYieldCalc, $ppmArr);
	}

	function getPPQtyRecs($processCodeId, $fromDate, $tillDate, $fishId)
	{			
		$qry = "
			select c.id, c.processes , c.criteria, sum(b.total_preprocess_qty), b.arrival_qty as actualUsedQty, b.actual_yield, b.ideal_yield, b.diff_yield, c.code as preProcessCode, sum(b.total_qty) as totalQty from 
			t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
			where b.dailypreprocess_main_id=a.id and 
				a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate')
				and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '%,$processCodeId') group by b.process order by c.processes asc
			";
		//echo "<br><b>PPQTY Pre-Process=$processCodeId</b><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		$rSize	= sizeof($result);
		$ppmArr = array();
		if (sizeof($result)>0) {
			$totPreProcessedQty = 0;
			$totProcessingQty = 0;
			$totActualY	= 0;
			$totIdealY	= 0;
			foreach ($result as $r) {				
				$preProcessedQty 	= $r[3];
				$totPreProcessedQty	+= $preProcessedQty;
				$processingQty   = $r[4];
				$totProcessingQty += $processingQty;
				$actualY	 = $r[5];
				$totActualY 	+= $actualY;
				$idealY		 = $r[6];
				$totIdealY 	+= $idealY;
				//$diffY	= $r[7]; 
				$preProcessCode = trim($r[8]);
				$ppmArr[$preProcessCode] = array($processingQty, $preProcessedQty);
			}
			$sActualYield = number_format(($totActualY/$rSize),2,'.','');
			$sIdealYield  = number_format(($totIdealY/$rSize),2,'.','');
			$diffYieldCalc = number_format(($sActualYield-$sIdealYield),2,'.','');
		}
		//echo "<b>size=".sizeof($result)."h=".$result[0][0]."</b><br>";		
		//return array($result[0][0], $result[0][1], $result[0][2], $totPreProcessedQty, $totProcessingQty, $sActualYield, $sIdealYield, $diffYieldCalc);
		return array($totPreProcessedQty, $ppmArr);
	}

	# Display Area
	function displayPPMCalc($ppmArr)
	{		
		$displayPCalc	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>Pre-Process Code</td><td>From Qty<br/>(Kg)</td><td>To Qty<br/>(Kg)</td></tr>";
		$totFromQty = 0;
		$totToQty = 0;
		foreach ($ppmArr as $preProcessCode=>$pcr) {
			$fromQty = $pcr[0];
			$totFromQty += $fromQty;
			$toQty 	= $pcr[1];
			$totToQty += $toQty;
			$displayPCalc	.= "<tr bgcolor=#fffbcc><td class=listing-item>$preProcessCode</td><td class=listing-item align=right>$fromQty</td><td class=listing-item align=right>$toQty</td></tr>";		
		} // Loop Ends here
		$totFromQty = number_format($totFromQty,2,'.','');
		$totToQty = number_format($totToQty,2,'.','');

		$displayPCalc	.= "<tr bgcolor=#fffbcc><td class=listing-head align=right>Total Qty:</td><td class=listing-item align=right><b>$totFromQty</b></td><td class=listing-item align=right><b>$totToQty</b></td></tr>";
		$displayPCalc	.= "</table>";
		return $displayPCalc;
	}

	function findPreProcessId($processCodeId, $preProcessRateListId)
	{
		$qry = "select id from m_process where rate_list_id='$preProcessRateListId' and (processes like '$processCodeId' or processes like '$processCodeId,%' or processes like '%,$processCodeId,%' or processes like '%,$processCodeId')";
		//echo "$qry<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	function getDailyPreProcessRecs($processCodeId, $fromDate, $tillDate, $fishId)
	{	
		$qry = "
			select c.id, c.processes , c.criteria, sum(b.total_preprocess_qty), sum(b.arrival_qty) as actualUsedQty , b.actual_yield, b.ideal_yield, b.diff_yield, sum(b.total_qty) as totalQty from 
			t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
			where b.dailypreprocess_main_id=a.id and b.total_preprocess_qty and
				a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate')
				and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '$processCodeId,%' or c.processes like '%,$processCodeId') group by b.process order by c.processes asc
			";
		//echo "<br><b>Pre-Process=$processCodeId</b><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		$rSize	= sizeof($result);
		if (sizeof($result)>0) {
			$totPreProcessedQty = 0;
			$totProcessingQty = 0;
			$totActualY	= 0;
			$totIdealY	= 0;
			foreach ($result as $r) {
				$preProcessedQty 	= $r[3];
				$totPreProcessedQty	+= $preProcessedQty;
				$actualY	 = $r[5];
				$totActualY 	+= $actualY;
				$idealY		 = $r[6];
				$totIdealY 	+= $idealY;
				$processingQty   = $r[4];
				$peeledQty = 0;
				if ($processingQty==0) {
					$actYCalc = (100/$actualY);
					$peeledQty = $actYCalc*$preProcessedQty;
					$processingQty = ceil($peeledQty);
				}
				$totProcessingQty += $processingQty;

				
			}
			$sActualYield = number_format(($totActualY/$rSize),2,'.','');
			$sIdealYield  = number_format(($totIdealY/$rSize),2,'.','');
			$diffYieldCalc = number_format(($sActualYield-$sIdealYield),2,'.','');
		}
		//echo "<b>size=".sizeof($result)."h=".$result[0][0]."</b><br>";
		//return array($result[0], $result[1], $result[2], $result[3], $result[4], $result[5], $result[6], $result[7]);	
		return array($result[0][0], $result[0][1], $result[0][2], $totPreProcessedQty, $totProcessingQty, $sActualYield, $sIdealYield, $diffYieldCalc);
	}

	# Get Process Yield Average
	function preProcessYieldAverage($processId)
	{
		$qry = " select sum(jan+feb+mar+apr+may+jun+jul+aug+sep+oct+nov+dece)/(12*count(*)) from m_process_yield_months where process_id='$processId' group by process_id";
		//echo "<br>$qry<br>"; 
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0]; 
	}
	# Get RM Pre-Processed Qty ---------------------------- Ends here -----------

	# packing Qty ---------------------- STARTS HERE
	function getRMPackingQty($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId)
	{
		# Daily Frzn Packing records
		$dailyFrznPkgRecs = $this->dailyFrozenPackingRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId);
		$totalGrossWt = 0;
		$prodnArr = array();
		foreach ($dailyFrznPkgRecs as $dfpr) {
			$numMc		= $dfpr[8];
			$numLoosePack 	= $dfpr[9];
			$declaredFilledWt = $dfpr[10]; // Filled Wt
			$actualFilledWt = $dfpr[13];
			$frznCodeFilledWt = ($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt;			
			$numMCPack	= $dfpr[11];
			
			$mcActualWt = ($frznCodeFilledWt*$numMCPack*$numMc);			
			$lcActualWt = $numLoosePack*$frznCodeFilledWt;
			$grossWt = $mcActualWt+$lcActualWt;
			$totalGrossWt += $grossWt;

			$frozenCodeId	= $dfpr[6];
			$frozenCode	= $dfpr[12];	
			$totLSlab	= ($numMCPack*$numMc)+$numLoosePack;
			$prodnArr[$frozenCodeId] = array($frozenCode, $totLSlab, $frznCodeFilledWt, $grossWt);	
			//echo "Out=$frznCodeFilledWt*$numMCPack*$numMc=$mcActualWt::LP=>::$numLoosePack*$frznCodeFilledWt=$lcActualWt, Gross=$grossWt<br>";
		}
		
		# Display Production Clac
		$displayProdnCalc = $this->displayProdnCalc($prodnArr);

		//echo "<b>$totalGrossWt</b><br>";
		return ($totalGrossWt!=0)?array(number_format($totalGrossWt,2,'.',''),$displayProdnCalc):array();
	}	
		
	function dailyFrozenPackingRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId)
	{
		$qry1= " select 
						a.id as id, 
						a.select_date as select_date,
						a.unit as unit, 
						b.freezing_stage_id as freezing_stage_id, 
						b.eucode_id as eucode_id, 
						b.brand_id as brand_id, 
						b.frozencode_id as frozencode_id, 
						b.mcpacking_id as mcpacking_id, 
						sum(c.number_mc) as nummc, 
						sum(c.number_loose_slab) as numls, 
						mf.filled_wt as filledwt, 
						mcp.number_packs as numpack, 
						mf.code as frznCode, 
						mf.actual_filled_wt as actualfilledwt
				from 
						t_dailyfrozenpacking_main a 
						left join t_dailyfrozenpacking_entry b on a.id=b.main_id 
						left join t_dailyfrozenpacking_grade c on b.id=c.entry_id
						left join m_frozenpacking mf on b.frozencode_id=mf.id 
						left join m_mcpacking mcp on b.mcpacking_id=mcp.id
				where 
						(a.select_date>='$fromDate' and a.select_date<='$tillDate') and b.fish_id='$fishId' and b.processcode_id='$processCodeId'  and a.company='$companyId' and a.unit='$unitId'";
		$qry2= "select 
							a.id as id, 
							a.select_date as select_date, 
							a.unit as unit, 
							b.freezing_stage_id as freezing_stage_id, 
							b.eucode_id as eucode_id, 
							b.brand_id as brand_id, 
							b.frozencode_id as frozencode_id, 
							b.mcpacking_id as mcpacking_id, 
							sum(c.number_mc) as nummc, 
							sum(c.number_loose_slab) as numls, 
							mf.filled_wt as filledwt, 
							mcp.number_packs as numpack, 
							mf.code as frznCode, 
							mf.actual_filled_wt as actualfilledwt
					from 
							t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id
							left join t_dailyfrozenpacking_grade_rmlotid c on b.id=c.entry_id
							left join m_frozenpacking mf on b.frozencode_id=mf.id left join m_mcpacking mcp on b.mcpacking_id=mcp.id
					where 
							(a.select_date>='$fromDate' and a.select_date<='$tillDate') and b.fish_id='$fishId' and b.processcode_id='$processCodeId'  and a.company='$companyId' and a.unit='$unitId'";
		$qry="$qry1 union all $qry2";
		$qry.="group by frozencode_id";
		//echo "<br>$qry<br>"; 
		return $this->databaseConnect->getRecords($qry);
	}

	# Display Area
	function displayProdnCalc($prodnArr)
	{		
		$displayPCalc	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>Frozen Code</td><td>No of Slabs</td><td>Filled Wt<br/> (Kg)</td><td>Net Wt</td></tr>";	
		$totGrossWt = 0;	
		foreach ($prodnArr as $fznCodeId=>$prdn) {
			$frozenCode = $prdn[0];
			$totLSlab = $prdn[1];
			$frznCodeFilledWt = $prdn[2];
			$grossWt = number_format($prdn[3],2,'.','');
			$totGrossWt += $grossWt;

			$displayPCalc	.= "<tr bgcolor=#fffbcc><td class=listing-item>$frozenCode</td><td class=listing-item align=right>$totLSlab</td><td class=listing-item align=right>$frznCodeFilledWt</td><td class=listing-item align=right>$grossWt</td></tr>";		
		}
		$totGrossWt = number_format($totGrossWt,2,'.','');
		$displayPCalc	.= "<tr bgcolor=#fffbcc><td class=listing-head colspan=3 align=right>Total Wt:</td><td class=listing-item align=right>$totGrossWt</td></tr>";				
		$displayPCalc	.= "</table>";

		return $displayPCalc;
	}
		
	# packing Qty ---------------------- ENDS HERE

	# ----------------------------------------- Peeled Qty Starts here--------------
	# Get Peeled Qty
	# Pre-Process From (Qty	
	# Criteria Calculation 1=> From/ 0=>To
	function getPeeledQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId)
	{
		list($preProcessId, $preProcessSequence, $ppCriteria, $ppQty, $totProcessingQty, $actualYield, $idealYield, $diffYield) = $this->getDailyPreProcessRecs($processCodeId, $fromDate, $tillDate, $fishId);

		//echo "<br>$preProcessId, seq=$preProcessSequence, criteria=$ppCriteria, $ppQty, $totProcessingQty, $actualYield, $idealYield, $diffYield<br>";
	
		$process = explode(",",$preProcessSequence);
		$selQty = "";
		
		if ($process[0]==$processCodeId && $ppQty!=0) {
			$selQty = $totProcessingQty;			
		} else if ($process[sizeof($process)-1]==$processCodeId && $ppQty!=0) {
			$selQty = $this->getPeeldedQtyRecs($process[sizeof($process)-1], $fromDate, $tillDate, $fishId);
		}
		//echo "<br/>PEELED QTY=$selQty,seq=>$preProcessSequence:>FirstCond:".$process[0]."==".$processCodeId.":SecndCond=".$process[sizeof($process)-1]."==".$processCodeId."PPQty=$totProcessingQty:::Criteria=$ppCriteria:::PPQTY=$ppQty";
		return $selQty;
	}

	function getPeeldedQtyRecs($processCodeId, $fromDate, $tillDate, $fishId)
	{			
		$qry = "
			select c.id, c.processes , c.criteria, sum(b.total_preprocess_qty) as PPQty, sum(b.arrival_qty) as actualUsedQty, b.actual_yield, b.ideal_yield, b.diff_yield, sum(b.total_qty) as totalQty from 
			t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
			where b.dailypreprocess_main_id=a.id and b.total_preprocess_qty and
				a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate')
				and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '$processCodeId,%') group by b.process order by c.processes asc
			";
		//echo "<br><b>Peeled Pre-Process=$processCodeId</b><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		
		# If actual used qty=0 then reverse calc 
		# Actual used Qty = 100 * 1/Actual Yield *Total PPm
		if (sizeof($result)>0) {
			$totPreProcessedQty = 0;
			$totProcessingQty = 0;
			foreach ($result as $r) {				
				$preProcessedQty 	= $r[3];
				$actualY	 = $r[5];
				$idealY		 = $r[6];
				$processingQty   = $r[4];
				$peeledQty = 0;
				if ($processingQty==0) {
					$actYCalc = (100/$actualY);
					$peeledQty = $actYCalc*$preProcessedQty;
					$processingQty = ceil($peeledQty);
				}
				$totProcessingQty += $processingQty;
			} // loop ends here			
		}
		//echo "<br><b>size=".sizeof($result)."h=".$result[0][0]."</b><br>";
		return $totProcessingQty;
	}
	# ----------------------------------------- Peeled Qty Ends  here --

	# ----------------------------------------- Difference PreProcessed Qty Starts here
	# Get Diff Pre-Processed Qty
	# Pre-Process From (Qty	
	# Criteria Calculation 1=> From/ 0=>To
	function getDiffPreProcessQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId)
	{
		list($preProcessSequence, $ppCriteria, $ppQty, $totProcessingQty, $dppmQty) = $this->getDiffDailyPPRecs($processCodeId, $fromDate, $tillDate, $fishId);
	
		$process = explode(",",$preProcessSequence);
		$selQty = "";
		if ($process[0]==$processCodeId && $ppQty!=0) {			
			$selQty = $dppmQty;			
		} else if ($process[sizeof($process)-1]==$processCodeId && $ppQty!=0) {
			$selQty = $this->getDiffPeeldedQtyRecs($process[sizeof($process)-1], $fromDate, $tillDate, $fishId);
		}
		return $selQty;
	}

	# Get Diff qty
	function getDiffDailyPPRecs($processCodeId, $fromDate, $tillDate, $fishId)
	{	
		$qry1= "select 
							c.id as id, 
							c.processes as processes, 
							c.criteria as criteria, 
							sum(b.total_preprocess_qty) as total_preprocess_qty, 
							sum(b.arrival_qty) as actualUsedQty , 
							b.actual_yield as actual_yield, 
							b.ideal_yield as ideal_yield, 
							b.diff_yield as diff_yield, 
							sum(b.total_qty) as totalQty,
							b.process as process 
					from 
							t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
					where b.dailypreprocess_main_id=a.id and b.total_preprocess_qty and
							a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate')
							and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '$processCodeId,%' or c.processes like '%,$processCodeId')";
		$qry2= "select 
							c.id as id, 
							c.processes as processes, 
							c.criteria as criteria, 
							sum(b.total_preprocess_qty) as total_preprocess_qty, 
							sum(b.arrival_qty) as actualUsedQty , 
							b.actual_yield as actual_yield, 
							b.ideal_yield as ideal_yield, 
							b.diff_yield as diff_yield, 
							sum(b.total_qty) as totalQty,
							b.process as process
					from 
							t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
					where b.dailypreprocess_main_id=a.id and b.total_preprocess_qty and
							a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate')
							and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '$processCodeId,%' or c.processes like '%,$processCodeId')";
			$qry="select id, 
							processes, 
							criteria, 
							sum(total_preprocess_qty), 
							sum(actualUsedQty) , 
							actual_yield, 
							ideal_yield, 
							diff_yield, 
							sum(totalQty),
							process
							from ($qry1 union all $qry2) dum";		
		$qry.=" group by process order by processes asc";
			//group by b.process order by c.processes asc";
			
		//echo "<br><b>Diff Pre-Process=$processCodeId</b><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		$rSize	= sizeof($result);
		if (sizeof($result)>0) {
			$totPreProcessedQty = 0;
			$totProcessingQty = 0;
			$totActualY	= 0;
			$totIdealY	= 0;
			$totDiffQty	= 0;
			foreach ($result as $r) {
				$preProcessedQty 	= $r[3];
				$totPreProcessedQty	+= $preProcessedQty;
				$actualY	 = $r[5];
				$totActualY 	+= $actualY;
				$idealY		 = $r[6];
				$totIdealY 	+= $idealY;
				$diffY		= ($actualY-$idealY);
				$processingQty   = $r[4];
				$peeledQty = 0;
				if ($processingQty==0) {
					$actYCalc = (100/$actualY);
					$peeledQty = $actYCalc*$preProcessedQty;
					$processingQty = ceil($peeledQty);
				}
				$totProcessingQty += $processingQty;
				$diffQty = $processingQty*($diffY/100);
				$totDiffQty += $diffQty;
			}			
		}
		//echo "<b>size=".sizeof($result)."h=".$result[0][0]."</b><br>";
		return array($result[0][1], $result[0][2], $totPreProcessedQty, $totProcessingQty, $totDiffQty);
	}

	# Diff Peeled
	function getDiffPeeldedQtyRecs($processCodeId, $fromDate, $tillDate, $fishId)
	{			
		$qry1 = "select 
							c.id as id , 
							c.processes as processes, 
							c.criteria as criteria, 
							sum(b.total_preprocess_qty) as PPQty, 
							sum(b.arrival_qty) as actualUsedQty, 
							b.actual_yield as actual_yield, 
							b.ideal_yield as ideal_yield, 
							b.diff_yield as diff_yield, 
							sum(b.total_qty) as totalQty,
							b.process as process
					from 
							t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
					where b.dailypreprocess_main_id=a.id and b.total_preprocess_qty and
							a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate')
							and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '$processCodeId,%')";

		$qry2= "select 
							c.id as id , 
							c.processes as processes, 
							c.criteria as criteria, 
							sum(b.total_preprocess_qty) as PPQty, 
							sum(b.arrival_qty) as actualUsedQty, 
							b.actual_yield as actual_yield, 
							b.ideal_yield as ideal_yield, 
							b.diff_yield as diff_yield, 
							sum(b.total_qty) as totalQty, 
							b.process as process
					from 
							t_dailypreprocess a, t_dailypreprocess_entries b, m_process c 
					where b.dailypreprocess_main_id=a.id and b.total_preprocess_qty and
							a.fish_id='$fishId' and (a.date>='$fromDate' and a.date<='$tillDate')
							and b.process=c.id and (c.processes like '$processCodeId' or c.processes like '$processCodeId,%')";
			
		$qry = "select id , 
							processes, 
							criteria, 
							sum(PPQty), 
							sum(actualUsedQty), 
							actual_yield, 
							ideal_yield, 
							diff_yield, 
							totalQty,
							process
							from($qry1 union all $qry2) dum";
		$qry.=" group by process order by processes asc";			
				//group by b.process order by c.processes asc";
		//echo "<br><b>Diff Peeled Pre-Process=$processCodeId</b><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		
		# If actual used qty=0 then reverse calc 
		# Actual used Qty = 100 * 1/Actual Yield *Total PPm
		if (sizeof($result)>0) {
			
			$totDiffQty = 0;
			foreach ($result as $r) {				
				$preProcessedQty 	= $r[3];                    
				$actualY	 	= $r[5];
				$idealY		 	= $r[6];
				//$diffY			= $r[7];
				$diffY			= ($actualY-$idealY);
				$processingQty   	= $r[4];
				$peeledQty = 0;
				if ($processingQty==0) {
					$actYCalc = (100/$actualY);
					$peeledQty = $actYCalc*$preProcessedQty;
					$processingQty = ceil($peeledQty);
				}
				$diffQty = $processingQty*($diffY/100);
				$totDiffQty += $diffQty;
			} // loop ends here			
		}
		//echo "<br><b>size=".sizeof($result)."h=".$result[0][0]."</b><br>";
		return $totDiffQty;
	}
	# ----------------------------------------- Difference PreProcessed Qty Ends here


	# Thawed Material Qty (RPM-ReProcessed Qty) -------------------------- STARTS HERE
	function getRMThawedQty($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId)
	{
		# Daily Frzn Packing records
		$dailyThawedRecs = $this->dailyThawedRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId);
		$totalGrossWt = 0;
		foreach ($dailyThawedRecs as $dtr) {
			/*$numMc		= $dtr[4];
			$numLoosePack 	= $dtr[5];
			$declaredFilledWt = $dtr[6]; // Filled Wt
			$actualFilledWt = $dtr[7];
			$frznCodeFilledWt = ($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt;
			$mcActualWt = $frznCodeFilledWt*$numMc;
			$eachPackWt = $frznCodeFilledWt/$numMc;
			$lcActualWt = $numLoosePack*$eachPackWt;
			$grossWt = $mcActualWt+$lcActualWt;
			$totalGrossWt += $grossWt;*/

			$grossWt = $dtr[8];
			$totalGrossWt += $grossWt;

			//echo "Out=$numMc, $numLoosePack, $frznCodeFilledWt, Gross=$grossWt<br>";
		}
		return ($totalGrossWt!=0)?number_format($totalGrossWt,2,'.',''):"";
	}	
		


		function getNextDayRMThawedQty($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId)
	{
		# Daily Frzn Packing records
		$dailyThawedRecs = $this->dailyNextDayThawedRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId);
		$totalGrossWt = 0;
		foreach ($dailyThawedRecs as $dtr) {
			/*$numMc		= $dtr[4];
			$numLoosePack 	= $dtr[5];
			$declaredFilledWt = $dtr[6]; // Filled Wt
			$actualFilledWt = $dtr[7];
			$frznCodeFilledWt = ($actualFilledWt!=0)?$actualFilledWt:$declaredFilledWt;
			$mcActualWt = $frznCodeFilledWt*$numMc;
			$eachPackWt = $frznCodeFilledWt/$numMc;
			$lcActualWt = $numLoosePack*$eachPackWt;
			$grossWt = $mcActualWt+$lcActualWt;
			$totalGrossWt += $grossWt;*/
			//echo "Out=$numMc, $numLoosePack, $frznCodeFilledWt, Gross=$grossWt<br>";
			$grossWt = $dtr[8];
			$totalGrossWt += $grossWt;
		}
		return ($totalGrossWt!=0)?number_format($totalGrossWt,2,'.',''):"";
	}	
	# Daily Thawed Recs
	function dailyThawedRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId)
	{
		$qry1 = " select tdt.id as id , tdt.select_date as select_date, tdt.frozencode_id as frozencode_id, tdt.mcpacking_id as mcpacking_id, sum(tdtg.number_mc_thawing) as numbermc, sum(tdtg.number_loose_slab_thawing) as numberls, mf.filled_wt as filledwt, mf.actual_filled_wt as actualwt,sum(number_mc_stock) as nummc,sum(number_loose_slab_stock) as numlc from  t_dailythawing tdt left join t_dailythawing_grade tdtg on tdt.id=tdtg.main_id
		left join m_frozenpacking mf on tdt.frozencode_id=mf.id where (tdt.select_date>='$fromDate' and tdt.select_date<='$tillDate') and tdt.fish_id='$fishId' and tdt.processcode_id='$processCodeId' and tdt.flag=1 and tdt.company_id='$companyId' and tdt.unit_id='$unitId'";
		$qry2 = " select tdt.id as id , tdt.select_date as select_date, tdt.frozencode_id as frozencode_id, tdt.mcpacking_id as mcpacking_id, sum(tdtg.number_mc_thawing) as numbermc, sum(tdtg.number_loose_slab_thawing) as numberls, mf.filled_wt as filledwt, mf.actual_filled_wt as actualwt,sum(number_mc_stock) as nummc,sum(number_loose_slab_stock) as numlc from  t_dailythawing_rmlotid tdt left join t_dailythawing_grade_rmlotid tdtg on tdt.id=tdtg.main_id
		left join m_frozenpacking mf on tdt.frozencode_id=mf.id where (tdt.select_date>='$fromDate' and tdt.select_date<='$tillDate') and tdt.fish_id='$fishId' and tdt.processcode_id='$processCodeId' and tdt.flag=1 and tdt.company_id='$companyId' and tdt.unit_id='$unitId'";
		$qry="select  id,select_date,frozencode_id,mcpacking_id,sum(numbermc), sum(numberls),filledwt,actualwt,sum(nummc),sum(numlc) from ($qry1 union all $qry2) dum";
		$qry.=" group by frozencode_id";
		//echo "<br>$qry<br>"; 
		return $this->databaseConnect->getRecords($qry);
	}
	# Thawed Material Qty (RPM-ReProcessed Qty) ---------------------- ENDS HERE



	function dailyNextDayThawedRecs($processCodeId, $fromDate, $tillDate, $fishId,$companyId,$unitId)
	{
		$qry1 = " select tdt.id as id , tdt.select_date as select_date, tdt.frozencode_id as frozencode_id, tdt.mcpacking_id as mcpacking_id, sum(tdtg.number_mc_thawing) as nummc, sum(tdtg.number_loose_slab_thawing) as numls, mf.filled_wt as filled_wt, mf.actual_filled_wt as actual_filled_wt,sum(number_mc_stock) as nummcstock,sum(number_loose_slab_stock) as numlsstock from t_dailythawing tdt left join t_dailythawing_grade tdtg on tdt.id=tdtg.main_id
		left join m_frozenpacking mf on tdt.frozencode_id=mf.id where tdt.select_date=(DATE_ADD('$fromDate', INTERVAL 1 DAY)) and tdt.fish_id='$fishId' and tdt.processcode_id='$processCodeId' and tdt.flag=1 and tdt.company_id='$companyId' and tdt.unit_id='$unitId' ";

		$qry2 = " select tdt.id as id , tdt.select_date as select_date, tdt.frozencode_id as frozencode_id, tdt.mcpacking_id as mcpacking_id, sum(tdtg.number_mc_thawing) as nummc, sum(tdtg.number_loose_slab_thawing) as numls, mf.filled_wt as filled_wt, mf.actual_filled_wt as actual_filled_wt,sum(number_mc_stock) as nummcstock,sum(number_loose_slab_stock) as numlsstock from t_dailythawing_rmlotid tdt left join t_dailythawing_grade_rmlotid tdtg on tdt.id=tdtg.main_id
		left join m_frozenpacking mf on tdt.frozencode_id=mf.id where tdt.select_date=(DATE_ADD('$fromDate', INTERVAL 1 DAY)) and tdt.fish_id='$fishId' and tdt.processcode_id='$processCodeId' and tdt.flag=1 and tdt.company_id='$companyId' and tdt.unit_id='$unitId' ";
		
		$qry="select id,select_date,frozencode_id,mcpacking_id, sum(nummc), sum(numls),filled_wt,actual_filled_wt,sum(nummcstock),sum(numlsstock) from ($qry1 union $qry2) dum where id is not null";
		$qry.=" group by frozencode_id";
		//group by tdt.frozencode_id";
		//echo "<br>$qry<br>"; 
		return $this->databaseConnect->getRecords($qry);
	}
	# Thawed Material Qty (RPM-ReProcessed Qty) ---------------------- ENDS HERE


	function getPreProcessRec($processCodeId, $preProcessRateListId)
	{
		$qry = "select id, processes, criteria from m_process where rate_list_id='$preProcessRateListId' and (processes like '$processCodeId' or processes like '$processCodeId,%' or processes like '%,$processCodeId,%' or processes like '%,$processCodeId')";
		//echo "$qry<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		return array($result[0], $result[1], $result[2]);
	}

	# Get Selected Fish Recs
	function getFishRecords($fromDate, $tillDate)	
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
		$dailyRMRecords = $this->getDailyRMProcessCodeRecs($fromDate, $tillDate, $fishId);		
		foreach ($dailyRMRecords as $drmr) {			
			$fFishId	= $drmr[1];			
			$fFishName	= $drmr[3];
			$selFishArr[$fFishId] = $fFishName;
		}
		#Daily PreProcess
		$dailyPPRecs = $this->getDailyPPProcessCodes($fromDate, $tillDate, $fishId);
		foreach ($dailyPPRecs as $k=>$drmr) {			
			$sFishId	= $drmr[1];
			$sFishName	= $drmr[3];
			$selFishArr[$sFishId] = $sFishName;
		}
		# Daiy Production Recs
		$dailyProdRecs = $this->getDailyProductionRecs($fromDate, $tillDate, $fishId);
		foreach ($dailyProdRecs as $drmr) {			
			$tFishId	= $drmr[1];			
			$tFishName	= $drmr[3];
			$selFishArr[$tFishId] = $tFishName;
		}
		# daily Re-Process (Thawed) Recs
		$dailyThawedRecs = $this->dailyReProcessedRecs($fromDate, $tillDate, $fishId);
		foreach ($dailyThawedRecs as $drmr) {			
			$frFishId	= $drmr[1];			
			$frFishName	= $drmr[3];
			$selFishArr[$frFishId] = $frFishName;
		}	
		asort($selFishArr);	
			/*
				//echo "<pre>";
				//echo "------1------<br>";
				print_r($prevDayOBRecs);		
				//echo "------2------<br>";
				print_r($dailyRMRecords);
				//echo "------3------<br>";
				print_r($dailyPPRecs);
				//echo "------4------<br>";
				print_r($dailyProdRecs);
				//echo "------5------<br>";
				print_r($dailyThawedRecs);
				//echo "</pre>";
			*/
		return $selFishArr;
	}


	# Get Excess / Shortage Qty
	function getExShortQty($defaultPCId, $selPCId, $netQty, $joinProcessCode, $rowPosition, $colPosition)
	{
		$qty = "";
		$displayExShrtCalc = "";
		if ($defaultPCId==$selPCId)	$qty = $netQty;
		else if ($defaultPCId!=$selPCId) {
			$process	= explode(",",$joinProcessCode);
			$pCodeFId	= $process[0];
			$pCodeSId	= $process[1];	
			list($preProcessId, $processSequence, $ppCriteria) = $this->getPProcessRec($pCodeFId, $pCodeSId);

			$preProcessExist = $this->chkPProcessRec($pCodeFId, $pCodeSId);
			# Yield Avg
			$ppYieldAvg = $this->preProcessYieldAverage($preProcessId);
			$ppYieldAvg = number_format($ppYieldAvg,2,'.','');
			$ppYieldDecQty = number_format(($netQty/($ppYieldAvg/100)),2,'.','');	
			$ppIncQty   = number_format(($netQty*($ppYieldAvg/100)),2,'.','');
			//echo "=>$joinProcessCode::$ppYieldAvg,$processSequence";		
			//if ($selPCId==$pCodeSId ) echo "h==$defaultPCId==$pCodeSId";
			//$qty = ($preProcessExist)?$ppIncQty:$ppYieldDecQty; // original
			$qty = ($rowPosition>$colPosition)?$ppYieldDecQty:$ppIncQty;

			# For Display Calculation
			$netQty = number_format($netQty, 2, '.','');
			$displayExShrtCalc	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>Qty</td><td>&nbsp;</td><td>Yield Avg (%)</td><td>Net Qty</td></tr>";
			if ($rowPosition<$colPosition) {
				$displayExShrtCalc	.= "<tr bgcolor=#fffbcc align=center class=listing-item><td class=listing-item>$netQty</td><td class=listing-item>X</td><td class=listing-item>$ppYieldAvg</td><td class=listing-item>$ppIncQty</td></tr>";	
			} else {
				$displayExShrtCalc	.= "<tr bgcolor=#fffbcc align=center class=listing-item><td class=listing-item>$netQty</td><td class=listing-item>/</td><td class=listing-item>$ppYieldAvg</td><td class=listing-item>$ppYieldDecQty</td></tr>";
			}
			$displayExShrtCalc	.= "</table>";
		}
		return array($qty, $displayExShrtCalc);
	}


	
	function getPProcessRec($pCodeFId, $pCodeSId)
	{
		$qry = "select id, processes , criteria from m_process 
			   where (processes like '$pCodeFId' or processes like '$pCodeFId,%' or processes like '%,$pCodeFId') and (processes like '$pCodeSId' or processes like '$pCodeSId,%' or processes like '%,$pCodeSId') group by processes";
		//		echo "$pCodeFId, $pCodeSId=><br>$qry<br>";
		$result	= $this->databaseConnect->getRecord($qry);		
		return array($result[0], $result[1], $result[2]);
	}

	function chkPProcessRec($pCodeFId, $pCodeSId)
	{
		$qry = "select id, processes , criteria from m_process 
			   where (processes like '$pCodeFId,%' or processes like '%,$pCodeSId') group by processes";
		// echo "$pCodeFId, $pCodeSId=><br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?true:false;
	}	


	# Pre-Process map ------------------ starts here
	# Criteria 1=> From/ 0=>To
	function getPreProcessMap()
	{
		# Delete Process Sequence
		$this->deletePSequence();
		//where mp.fish_id='36'
		$qry1 = " select mp.fish_id, mp.processes, mf.name, mp.criteria  from m_process mp left join m_fish mf on mp.fish_id=mf.id  group by mp.fish_id, mp.processes order by mf.name asc, mp.code asc";
		$result1 = $this->databaseConnect->getRecords($qry1);		
		//echo $qry1;
		$prevFishId = 0;
		$i = 0;
		$prevSelFishId = 0;
		foreach ($result1 as $rp) {
			$fishId 	= $rp[0];
			if ($i==0) $prevSelFishId = $fishId;
			# For checking
			//if ($prevSelFishId!=$fishId) echo $this->selArrangedPC($prevSelFishId)."<br>";

			if ($prevFishId!=$fishId) {
				//$pArr = array();
				$fpArr = array();
				$gArr = array();
			}
			
			$processes	= $rp[1];
			$sProcess	= explode(",",$processes);
			# Arranging the Process Codes
			$this->getPP($fishId, $processes);	
			/* Testing variables
			$fishName	= $rp[2];
			$pCriteria 	= $rp[3];
			$displayPCriteria = ($pCriteria==1)?"From":"To";
			$displayProcess ="";			
			$jProcess = array();
			for ($k=0; $k<sizeof($sProcess);$k++){	
				$displayProcess	=$this->getSelProcessCode($sProcess[$k]);
				$jProcess[$k] = $displayProcess;
			}
			$jpCode = implode("->",$jProcess);
			*/
			$prevFishId= $fishId;	
			$prevSelFishId = $fishId;
			$i++;	
		}
	}
	
	function getSelProcessCode($processCodeId)
	{
		$qry	= "select code from m_processcode where id='$processCodeId' ";
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	#Chk Pre-Process exist
	function chkPreProcessRecExist($fishId, $Processes)
	{
		$qry	= " select id from m_process where fish_id='$fishId' and processes='$Processes' ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Criteria 1=> From/ 0=>To
	function getPP($fishId, $processCodeId)
	{
		$qry = "select mp.id, mp.processes , mp.criteria from m_process mp 
			where mp.fish_id='$fishId' and (mp.processes like '$processCodeId' or mp.processes like '$processCodeId,%' or mp.processes like '%,$processCodeId,%' or mp.processes like '%,$processCodeId') group by mp.processes
			";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		foreach ($result as $r) {
			$sProcess	= explode(",",$r[1]);
			$pCriteria 	= $r[2];
			for ($k=0; $k<sizeof($sProcess);$k++){	
				list($chkPSequenceExist, $sequenceId, $sProcessCriteria) = $this->chkProcessQeuence($fishId, $sProcess[$k]);
				
				if ($chkPSequenceExist) {
					$orderId = "";
					if ($pCriteria==1) $orderId = $k+1; 
					else $orderId = $this->maxSortId($fishId);
					if ($sProcessCriteria==1) $uspCiteria = $sProcessCriteria;
					else $uspCiteria = $pCriteria;
					$updatePSequence = $this->updatePSequence($sequenceId, $orderId, $uspCiteria);
				} else {
					$orderId = "";
					if ($pCriteria==1) $orderId = $k+1;
					else $orderId = $this->maxSortId($fishId);

					$insertPSequence = $this->insertPSequence($fishId,$sProcess[$k], $orderId, $pCriteria);
				}
			}
		}
		return $result;
	}

	function chkProcessQeuence($fishId, $processCodeId)
	{
		$qry = " select id, process_criteria from pre_process_sequence where fish_id='$fishId' and processcode_id='$processCodeId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array(true,$result[0][0], $result[0][1]):array();
	}

	function updatePSequence($sequenceId, $orderId, $pCriteria)
	{
		$qry = "update pre_process_sequence set sort_id='$orderId', process_criteria='$pCriteria' where id=$sequenceId ";		
		//echo "Update=$qry<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function insertPSequence($fishId, $processCodeId, $orderId, $pCriteria)
	{
		$qry = "insert into pre_process_sequence (fish_id, processcode_id, sort_id, process_criteria) values('$fishId', '$processCodeId', '$orderId', '$pCriteria')";
		//echo "Insert=$qry<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function deletePSequence()
	{
		$qry	=	" delete from pre_process_sequence";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function maxSortId($fishId)
	{
		$qry = " select max(sort_id) from pre_process_sequence where fish_id='$fishId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?($result[0][0]+1):1;	
	}

	function selArrangedPC($fishId)
	{
		$qry = "select id, fish_id, processcode_id, sort_id, process_criteria from pre_process_sequence where fish_id='$fishId' order by process_criteria desc, sort_id asc";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		$jProcess = array();
		$k = 0;
		foreach ($result as $rp) {
			$fishId 	= $rp[0];
			$spCodeId	= $rp[2];
			$displayProcess	=$this->getSelProcessCode($spCodeId);
			$jProcess[$k] = $displayProcess;
			$k++;
		}
		return implode("->",$jProcess);
	}

	function showMProcessTableStatus()
	{
		$qry = 'Show Table Status like "m_process"';
		$rec = $this->databaseConnect->getRecord($qry);
		
		return strtotime($rec[12]);
	}
	function showPPSequenceTableStatus()
	{
		$qry = 'Show Table Status like "pre_process_sequence"';
		$rec = $this->databaseConnect->getRecord($qry);
		
		return strtotime($rec[12]);
	}

	function chkProcessSequenceRecs()
	{
		$qry = "select id from pre_process_sequence group by fish_id";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	# Pre-Process map ------------------ Ends here

	# Get Max Count of Selected Process Code
	/*
	function getMaxTempProcessCode()
	{
		# Get Current Temporary recs
		$tempPCRecs = $this->tempPCodeRecs;		
		$countArr = array();
		$i = 0;
		foreach ($tempPCRecs as $pcr) {
			$processCodeId	= $pcr[0];
			$fishId		= $pcr[1];
			# Get Process Code recs
			$pcRecs = $this->getSelProcessCodeRecs($fishId);
			if (sizeof($pcRecs)>0) {
				$countArr[$i] = sizeof($pcRecs);
				$i++;
			}
		}
		return max($countArr);
	}
	*/

	# get All Temply inserted recs
	function getTempFishRecs()
	{			
		$tempPCRecs= $this->tempPCodeRecs;	
		$fishArr = array();		
		foreach ($tempPCRecs as $pcr) {			
			$processCodeId	= $pcr[0];
			$fishId		= $pcr[1];
			//echo "<br>$processCodeId,$fishId";
			//$fishArr[$fishId] = $processCodeId;
			$fishArr[$processCodeId] = $fishId;
		}		
		return $fishArr;
	}


	# Pre-Process code sequence
	# Criteria 1=> From/ 0=>To
	function getProcessSequenceMap()
	{
		# Delete Process Sequence
		//$this->deletePSequence();

		//where mp.fish_id='38'
		$qry1 = " select mp.id, mp.rate_list_id, mp.fish_id, mp.processes, mf.name, mp.criteria, mp.code from m_process mp left join m_fish mf on mp.fish_id=mf.id where mp.rate_list_id in (select a.id from m_processratelist a where end_date is null or end_date=0) group by mp.fish_id, mp.processes order by mp.processes asc, mp.criteria desc, mf.name asc, mp.code asc ";
		$result1 = $this->databaseConnect->getRecords($qry1);		
		//echo "<br>$qry1<br>";		
		$i = 0;		
		foreach ($result1 as $rp) {
			$fishId 	= $rp[0];			
			$processes	= $rp[1];			
			# Arranging the Process Codes
			//$this->getPSPCode($fishId, $processes);
			$i++;	
		}
	}

	# Criteria 1=> From/ 0=>To
	/*
	function getPSPCode($fishId, $processCodeId)
	{
		$qry = "
			select mp.id, mp.processes , mp.criteria from m_process mp 
			where mp.fish_id='$fishId' and (mp.processes like '$processCodeId' or mp.processes like '$processCodeId,%' or mp.processes like '%,$processCodeId,%' or mp.processes like '%,$processCodeId') group by mp.processes
			";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		foreach ($result as $r) {
			$sProcess	= explode(",",$r[1]);
			$pCriteria 	= $r[2];
			for ($k=0; $k<sizeof($sProcess);$k++){	
				//list($chkPSequenceExist, $sequenceId, $sProcessCriteria) = $this->chkProcessQeuence($fishId, $sProcess[$k]);
				
				if ($chkPSequenceExist) {
					$orderId = "";
					if ($pCriteria==1) $orderId = $k+1; 
					else $orderId = $this->maxSortId($fishId);
					if ($sProcessCriteria==1) $uspCiteria = $sProcessCriteria;
					else $uspCiteria = $pCriteria;
					//$updatePSequence = $this->updatePSequence($sequenceId, $orderId, $uspCiteria);
				} else {
					$orderId = "";
					if ($pCriteria==1) $orderId = $k+1;
					else $orderId = $this->maxSortId($fishId);

					//$insertPSequence = $this->insertPSequence($fishId,$sProcess[$k], $orderId, $pCriteria);
				}
			}
		}
		return $result;
	}
	*/
	
	# --------------- Process Sequence Ends here ----------------

	function getPCPostion($fishId, $processCodeId)
	{
		$qry = "select id, fish_id, processcode_id, sort_id, process_criteria from pre_process_sequence where fish_id='$fishId' order by process_criteria desc, sort_id asc";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		$positionArr = array();
		if (sizeof($result)>0) {
			$j=0;
			foreach ($result as $r) {
				$j++;
				$positionArr[$r[2]] = $j;
			}
		}
		return $positionArr;
	}

	# Check Process table updated
	function chkProcessTbleUptd()
	{
		$qry1 = "select update_time from meta_data_log where table_name='m_process'";
		$result1 = $this->databaseConnect->getRecords($qry1);
		$processTbleUptdT = strtotime($result1[0][0]);

		$qry2 = "select update_time from meta_data_log where table_name='pre_process_sequence'";
		$result2 = $this->databaseConnect->getRecords($qry2);
		$processSequenceTbleUptdT = strtotime($result2[0][0]);
		
		return ($processSequenceTbleUptdT<$processTbleUptdT)?true:false;
	}

}	
?>