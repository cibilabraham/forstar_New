<?php
Class DailyRMClosingBalance
{
	/****************************************************************
	This class deals with all the operations relating to Daily RM closingBalance 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DailyRMClosingBalance(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	# Add Daily Rate
	function addDailyRMCB($currentDate, $fishId, $processCodeId, $closingBalance, $userId, $preProcessCS, $totalCS, $reProcessedCS, $exptEntry='N',$company,$unit)
	{
		$qry 	= " insert into t_daily_rm_cb (select_date, fish_id, processcode_id, closing_balance, created, createdby, pre_process_cs, total_cs, re_process_cs, expt_entry,company_id,unit_id) values('$currentDate', '$fishId', '$processCodeId', '$closingBalance', NOW(), '$userId', '$preProcessCS', '$totalCS', '$reProcessedCS', '$exptEntry','$company','$unit')";
//		echo "<br>".$qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else 		 	$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	
	# Filter records(PAGING)
	function fetchAllPagingRecords($filterId, $recordsDate, $offset, $limit)
	{
		$whr  = " a.select_date='".$recordsDate."'" ;
		//$whr  = " a.select_date>='".$recordsDate."'" ;

		if ($filterId!=0) $whr .= " and a.fish_id = '".$filterId."'" ;
		
		//$orderBy = "a.select_date asc, c.name asc, d.code asc";
		$orderBy = "a.select_date asc";

		$limit	 = "$offset,$limit";
		$groupBy=" a.company_id,a.unit_id,a.select_date";

		  $qry = " select  a.id,a.company_id,a.unit_id,b.display_name,c.name,a.select_date from t_daily_rm_cb a left join m_billing_company b on a.company_id=b.id left join m_plant c on a.unit_id=c.id";

	//   $qry = " select a.id, a.select_date, a.fish_id, a.processcode_id, a.closing_balance, c.name as fName, d.code as processCode, a.pre_process_cs, a.re_process_cs from t_daily_rm_cb a left join m_fish c on a.fish_Id = c.id left join m_processcode d on a.processcode_id=d.id ";

		if ($whr!="") 		$qry   .= " where ".$whr;
		
		if($groupBy!="")		$qry   .= " group by ".$groupBy;
		if ($orderBy!="")	$qry   .= " order by ".$orderBy;
		if ($limit!="")		$qry   .= " limit ".$limit;
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Fetch All Records
	function fetchAllRecords($filterId, $recordsDate)
	{		
		$whr = " a.select_date='".$recordsDate."'" ;
		//$whr = " a.select_date>='".$recordsDate."'" ;

		if ($filterId!=0) $whr	.= " and a.fish_id = '".$filterId."'" ;
		
		//$orderBy =	"a.select_date asc, c.name asc, d.code asc";
		$orderBy = "a.select_date asc";

		$groupBy=" a.company_id,a.unit_id,a.select_date";

		 $qry = " select a.id, a.company_id,a.unit_id,b.display_name,c.name,a.select_date from t_daily_rm_cb a left join m_billing_company b on a.company_id=b.id left join m_plant c on a.unit_id=c.id";

		if ($whr!="") 		$qry   .= " where ".$whr;
		if($groupBy!="")		$qry   .= " group by ".$groupBy;
		if ($orderBy!="")	$qry   .= " order by ".$orderBy;		
		//echo $qry;	
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getFishDetail($companyId,$unitId,$selectDate)
	{
		$qry = " select a.fish_id,c.name from t_daily_rm_cb a left join m_fish c on a.fish_Id = c.id where a.company_id='$companyId' and a.unit_id='$unitId' and a.select_date='$selectDate' group by  a.fish_id";
	//	echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getProcessCodes($companyId,$unitId,$selDate,$fishId)
	{
		$qry = " select a.processcode_id,b.code,a.pre_process_cs,a.total_cs,a.re_process_cs,a.expt_entry from t_daily_rm_cb a left join  m_processcode b on a.processcode_id=b.id where a.company_id='$companyId' and a.unit_id='$unitId' and a.select_date='$selDate' and  a.fish_id='$fishId' ";
	//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Filter table using fish id
	function find($dailyRMCBId)
	{
		$qry = "select  a.id, a.select_date, a.fish_id, a.processcode_id, a.closing_balance, a.pre_process_cs,a.total_cs,a.re_process_cs,a.expt_entry,a.company_id,a.unit_id  from t_daily_rm_cb a where a.id=$dailyRMCBId ";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	

	# update 
	function updateDailyRMCB($dailyRMCBId, $currentDate, $fishId, $processCodeId, $closingBalance, $preProcessCS, $totalCS, $reProcessedCS) 
	{
		$qry	= " update t_daily_rm_cb set select_date='$currentDate', fish_id='$fishId', processcode_id='$processCodeId', closing_balance='$closingBalance', pre_process_cs='$preProcessCS', total_cs='$totalCS', re_process_cs='$reProcessedCS' where id=$dailyRMCBId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;	
	}

	
	# Delete 
	function deleteDailyRMCB($dailyRMCBId)
	{
		$qry	= " delete from t_daily_rm_cb where id=$dailyRMCBId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check More Entry Exist
	function chkEntryExist($currentDate, $fishId, $processCodeId, $selRMCBId)
	{
		$qry = "select id from t_daily_rm_cb where select_date='$currentDate' and fish_id='$fishId' and processcode_id='$processCodeId' ";
		if ($selRMCBId) $qry .= " and id!=$selRMCBId ";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;	
	}


	# Filter m_processcode table using fish id (Copied from Processcode_class)
	function pcRecFilter($fishId, $pcId)
	{
		//$qry = "select a.id, a.code from m_processcode a, m_fish b where a.fish_id = b.id and b.id='$fishId' order by b.name asc, a.code asc";

		$filterPCId = "";
		if ($pcId!="") {
			$pcIdArr = explode(":",$pcId);
			$filterPCId = implode(",",$pcIdArr);
		}
		$qry = "select a.id, a.code from m_processcode a where a.fish_id='$fishId'";
		if ($filterPCId!="") $qry .= " and a.id not in ($filterPCId) ";
		$qry .= "order by a.code asc";

		//echo "$qry";
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}


	# Get Process codes used for one day
	function dailyRMProcessCodeRecs($fromDate, $fishId,$companyId,$unitId)
	{
		$processCodeArr = array();

		# Daily CB 
		$dailyCBRecs	= $this->getCBRecs($fromDate, $fishId,$companyId,$unitId);
		foreach ($dailyCBRecs as $drmr) {
			$cbProcessCodeId = $drmr[0];
			$cbFishId	= $drmr[1];
			$cbProcessCode	= $drmr[2];
			$cbFishName	= $drmr[3];
			$processCodeArr[$cbProcessCodeId] = array($cbProcessCodeId, $cbFishId, $cbProcessCode, $cbFishName);
		}

		# Daily RM
		$dailyRMRecords = $this->getDailyRMProcessCodeRecs($fromDate, $fishId,$companyId,$unitId);
		foreach ($dailyRMRecords as $drmr) {
			$fProcessCodeId = $drmr[0];
			$fFishId	= $drmr[1];
			$fProcessCode	= $drmr[2];
			$fFishName	= $drmr[3];
			$processCodeArr[$fProcessCodeId] = array($fProcessCodeId, $fFishId, $fProcessCode, $fFishName);
		}
		#Daily PreProcess
		$dailyPPRecs = $this->getDailyPPProcessCodes($fromDate, $fishId,$companyId,$unitId);
		foreach ($dailyPPRecs as $k=>$drmr) {
			$sProcessCodeId = $drmr[0];
			$sFishId	= $drmr[1];
			$sProcessCode	= $drmr[2];
			$sFishName	= $drmr[3];
			$processCodeArr[$sProcessCodeId] = array($sProcessCodeId, $sFishId, $sProcessCode, $sFishName);
		}
		# Daiy Production Recs
		$dailyProdRecs = $this->getDailyProductionRecs($fromDate, $fishId,$companyId,$unitId);
		foreach ($dailyProdRecs as $drmr) {
			$tProcessCodeId = $drmr[0];
			$tFishId	= $drmr[1];
			$tProcessCode	= $drmr[2];
			$tFishName	= $drmr[3];
			$processCodeArr[$tProcessCodeId] = array($tProcessCodeId, $tFishId, $tProcessCode, $tFishName);
		}
		
		# daily Re-Process (Thawed) Recs
		$dailyThawedRecs1 = $this->dailyReProcessedRecs($fromDate, $fishId,$companyId,$unitId);
		foreach ($dailyThawedRecs1 as $drmr) {
			$frProcessCodeId = $drmr[0];
			$frFishId	= $drmr[1];
			$frProcessCode	= $drmr[2];
			$frFishName	= $drmr[3];
			$processCodeArr[$frProcessCodeId] = array($frProcessCodeId, $frFishId, $frProcessCode, $frFishName);
		}	
		//print_r($processCodeArr);

		$dailyThawedRecs2 = $this->dailyReProcessedRecsTomm($fromDate, $fishId,$companyId,$unitId);
		foreach ($dailyThawedRecs2 as $drmr) {
			$frProcessCodeId = $drmr[0];
			$frFishId	= $drmr[1];
			$frProcessCode	= $drmr[2];
			$frFishName	= $drmr[3];
			$processCodeArr[$frProcessCodeId] = array($frProcessCodeId, $frFishId, $frProcessCode, $frFishName);
		}	
		//print_r($processCodeArr);
//$processCodeArr[$frProcessCodeId] =array_merge( $processCodeArr1,$processCodeArr2);

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
		return $result;
	}

	# Get Current CB Recs
	function getCBRecs($fromDate, $fishId,$companyId,$unitId)
	{
		//$whr = "tdrcb.select_date='$fromDate' and tdrcb.fish_id is not null";
		$whr = "tdrcb.select_date=(DATE_SUB('$fromDate', INTERVAL 1 DAY)) and tdrcb.fish_id is not null";
		if ($fishId) $whr .= " and tdrcb.fish_id=$fishId '";
		if ($companyId) $whr .= " and company_id='$companyId' ";
		if ($unitId) $whr .= "  and unit_id='$unitId'";
		//if ($fishId) $whr .= " and tdrcb.fish_id in ($fishId) ";

		$groupBy = "tdrcb.processcode_id";	
		
		$qry = "select tdrcb.processcode_id, tdrcb.fish_id, mpc.code, mf.name from t_daily_rm_cb tdrcb left join m_processcode mpc on mpc.id=tdrcb.processcode_id left join m_fish mf on mpc.fish_id = mf.id ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		//echo "<br>Prev Day OB==<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDailyRMProcessCodeRecs($fromDate, $fishId,$companyId,$unitId)
	{
		$whr = "a.select_date='$fromDate' and b.fish is not null";

		//$whr = "a.select_date>='$fromDate' and b.fish is not null";

		if ($fishId) $whr .= " and b.fish='$fishId'  ";
		if ($companyId) $whr .= " and a.billing_company_id='$companyId' ";
		if ($unitId) $whr .= "  and a.unit='$unitId'";
		
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

	function getDailyPPProcessCodes($fromDate, $fishId,$companyId,$unitId)
	{
		$whr = "a.date='$fromDate'";

		if ($fishId) $whr .= " and a.fish_id='$fishId' ";
		if ($companyId) $whr .= " and company_id='$companyId' ";
		if ($unitId) $whr .= "  and unit_id='$unitId'";
		
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
			} // For Loop Ends here
		}	
		return $resultArr;
	}
	
	function getDailyProductionRecs($fromDate, $fishId,$companyId,$unitId)
	{
		$whr = "a.select_date='$fromDate' and b.fish_id is not null and company='$companyId' and unit='$unitId'";

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

	function dailyReProcessedRecs($fromDate, $fishId,$companyId,$unitId)
	{
		$whr = "a.select_date='$fromDate' and a.fish_id is not null";
		//$whr = "a.select_date>='$fromDate' and a.fish_id is not null";

		if ($fishId) $whr .= " and a.fish_id='$fishId' ";
		if ($companyId) $whr .= " and company_id='$companyId' ";
		if ($unitId) $whr .= "  and unit_id='$unitId'";

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



	function dailyReProcessedRecsTomm($fromDate, $fishId,$companyId,$unitId)
	{
		$whrs = "a.select_date=(DATE_ADD('$fromDate', INTERVAL 1 DAY)) and a.fish_id is not null and mpc.code is not null";
		$whrss = "a.select_date=(DATE_ADD('$fromDate', INTERVAL 1 DAY)) and a.fish_id is not null and mpc.name is not null";
		//$whr = "a.select_date='$fromDate' and a.fish_id is not null";
		//$whr = "a.select_date>='$fromDate' and a.fish_id is not null";

		if ($fishId) $whr .= " and a.fish_id='$fishId' ";
		if ($companyId) $whr .= " and company_id='$companyId' ";
		if ($unitId) $whr .= "  and unit_id='$unitId'";

		$orderBy	= " name asc, code asc";
		//$groupBy	= " a.processcode_id";
		$groupBy	= " processcode_id";
		
		$qry1= " select a.processcode_id as processcode_id, a.fish_id as fish_id, mpc.code as code, mf.name as name from t_dailythawing a left join m_processcode mpc on mpc.id=a.processcode_id left join m_fish mf on mpc.fish_id = mf.id ";
		$qry11= " select  a.processcode_id as processcode_id, a.fish_id as fish_id, mpc.code as code, mf.name as name from t_dailythawing_rmlotid a left join m_processcode mpc on mpc.id=a.processcode_id left join m_fish mf on mpc.fish_id = mf.id";

		$qry2= "select a.processcode_id as processcode_id, a.fish_id as fish_id, mpc.name as code, mf.name as name from t_dailythawing a left join m_secondary_processcode mpc on mpc.id=a.processcode_id left join m_fish mf on mpc.fish_id = mf.id";
		$qry21= "select  a.processcode_id as processcode_id, a.fish_id as fish_id, mpc.name as code, mf.name as name from t_dailythawing_rmlotid a left join m_secondary_processcode mpc on mpc.id=a.processcode_id left join m_fish mf on mpc.fish_id = mf.id";

		/*
		if ($orderBy)	$qry1.= " order by ".$orderBy; $qry11.= " order by ".$orderBy;
		if ($orderBy)	$qry2.= " order by ".$orderBy; $qry21.= " order by ".$orderBy;
*/
		if ($whrs)	
		{
			$qry1.= " where ".$whrs;
			$qry11.= " where ".$whrs;
		}
		if ($whrss)	
		{
			$qry2.= " where ".$whrss; 
			$qry21.= " where ".$whrss; 
		}
		$qrys="$qry1 union	all $qry11";
		$qryss="$qry2 union all $qry21";
		//if ($whrs)	$qrys.= " where ".$whrs; 
		//if ($whrss)	$qryss.= " where ".$whrss;
		//if ($orderBy)	$qrys.= " order by ".$orderBy; $qryss.= " order by ".$orderBy;
		//echo $qryss;
		$qry="select * from ( $qrys union all $qryss) dum ";
		//if ($orderBy)	$qry.= " order by ".$orderBy; 
		if ($groupBy)	$qry .= " group by ".$groupBy;
		//echo "Daily Thawed Recs===<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Create temp table
	function createTempTable()
	{
		$qry = "create temporary table temp_RM_CB( 
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
		$qry = "insert into temp_RM_CB (`processcode_id`, `fish_id`, `processcode`, `fish_name`) values('$processCodeId', '$fishId', '$processCode', '$fishName')";
		//echo "<br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	# get All Temply inserted recs
	function getTempProdAnlysReport()
	{	
		$qry = "select par.`processcode_id`, par.`fish_id`, par.`processcode`, par.`fish_name` from temp_RM_CB par left join pre_process_sequence pps on par.processcode_id=pps.processcode_id  order by par.`fish_name` asc, pps.process_criteria desc, pps.sort_id asc, par.`processcode` asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}
	function getProcessCodeRec($processCodeId)
	{
		$qry = "select mpc.id, mpc.fish_id, mpc.code, mf.name from m_processcode mpc left join m_fish mf on mpc.fish_id = mf.id where mpc.id='$processCodeId'";
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2], $rec[3]);
	}
	# ------------------ Selection of process code ends here
	
	# Get All Fish records
	function getFishRecords($fromDate, $fishId=null)	
	{		
		$selFishArr = array();
		# Daily RM
		$dailyRMRecords = $this->getDailyRMProcessCodeRecs($fromDate, $fishId,$companyId,$unitId);
		//echo "Daily RM";
		//printr($dailyRMRecords);
		foreach ($dailyRMRecords as $drmr) {			
			$fFishId	= $drmr[1];			
			$fFishName	= $drmr[3];
			$selFishArr[$fFishId] = $fFishName;
		}
		#Daily PreProcess
		$dailyPPRecs = $this->getDailyPPProcessCodes($fromDate, $fishId,$companyId,$unitId);
		//echo "dailyPPRecs";
		//printr($dailyPPRecs);
		foreach ($dailyPPRecs as $k=>$drmr) {			
			$sFishId	= $drmr[1];
			$sFishName	= $drmr[3];
			$selFishArr[$sFishId] = $sFishName;
		}
		# Daiy Production Recs
		$dailyProdRecs = $this->getDailyProductionRecs($fromDate, $fishId,$companyId,$unitId);
		//echo "dailyProdRecs";
		//printr($dailyProdRecs);
		foreach ($dailyProdRecs as $drmr) {			
			$tFishId	= $drmr[1];			
			$tFishName	= $drmr[3];
			$selFishArr[$tFishId] = $tFishName;
		}
		# daily Re-Process (Thawed) Recs
		$dailyThawedRecs = $this->dailyReProcessedRecs($fromDate, $fishId,$companyId,$unitId);
		//echo "dailyThawedRecs";
		//printr($dailyThawedRecs);
		foreach ($dailyThawedRecs as $drmr) {			
			$frFishId	= $drmr[1];			
			$frFishName	= $drmr[3];
			$selFishArr[$frFishId] = $frFishName;
		}	
		
		$dailyThawedRecs = $this->dailyReProcessedRecsTomm($fromDate, $fishId,$companyId,$unitId);
		//echo "dailyThawedRecs";
		//printr($dailyThawedRecs);
		foreach ($dailyThawedRecs as $drmr) {			
			$frFishId	= $drmr[1];			
			$frFishName	= $drmr[3];
			$selFishArr[$frFishId] = $frFishName;
		}	


		asort($selFishArr);	
		//print_r($selFishArr);
		return $selFishArr;
	}
	
	# Get Daily CS Rec
	function getDailyCBRec($selDate, $fishId, $processCodeId,$companyId,$unitId)
	{
		$qry = "select  a.id, a.closing_balance as prodCB, a.pre_process_cs, a.total_cs, a.re_process_cs from t_daily_rm_cb a where a.select_date='$selDate' and a.fish_id='$fishId' and a.processcode_id='$processCodeId' and company_id='$companyId' and unit_id='$unitId'";
		//$qry = "select  a.id, a.closing_balance as prodCB, a.pre_process_cs, a.total_cs, a.re_process_cs from t_daily_rm_cb a where a.select_date>='$selDate' and a.fish_id='$fishId' and a.processcode_id='$processCodeId' ";
		//echo $qry;		
		$rec	= $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?array($rec[0][0], $rec[0][1], $rec[0][2], $rec[0][3], $rec[0][4]):array();
	}

	function chkRMCBEntryExist($selDate)
	{
		$qry = "select  a.id from t_daily_rm_cb a where a.select_date='$selDate' ";
		//echo $qry;		
		$rec	= $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	function dailyClosingBalance($selectDate,$recordsFilterId,$companyId,$unitId)
	{
		$qry="select id from t_daily_rm_cb where select_date='$selectDate' and company_id='$companyId' and unit_id='$unitId' and fish_id='$recordsFilterId'";
		//echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;
	}

	
}	
?>