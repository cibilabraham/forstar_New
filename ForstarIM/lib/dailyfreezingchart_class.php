<?php
class DailyFreezingChart
{
	/****************************************************************
	This class deals with all the operations relating to Daily Activity Chart
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function DailyFreezingChart(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add Daily Processing Chart
	function addDailyProcessingChart($selectDate, $installedCapacityId, $userId)
	{
		$qry = "insert into t_dailyprocessingchart (select_date, installed_capacity_id, created, created_by) values('$selectDate', '$installedCapacityId', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;	
	}
	
	# Add DPC Monitoring Params
	function addDailyPCMonitoringParam($dpcLastId, $monitoringParamId, $startTime, $stopTime, $startedAtTime, $stoppedAtTime, $stopMonitoring, $monitoringLastInterval)
	{
		$qry = "insert into t_dailyprocessingchart_entry (main_id, monitoring_param_id, start_time, stop_time, started_time, stopped_time, stop_monitor, interval_last_time) values('$dpcLastId', '$monitoringParamId', '$startTime', '$stopTime', '$startedAtTime', '$stoppedAtTime', '$stopMonitoring', '$monitoringLastInterval')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	

	# Add Daily Processing chart monitoring interval parameters
	function addIntervalParam($dpcMonitorParamEntryLastId, $startTime, $startTemp, $stopTime, $stopTemp)
	{
		$qry = "insert into t_dailyprocessingchart_interval (dpc_entry_id, start_time, start_val, stop_time, stop_val) values('$dpcMonitorParamEntryLastId', '$startTime', '$startTemp', '$stopTime', '$stopTemp')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Fetch All Recs
	function fetchAllPagingRecords($searchDate, $offset, $limit)
	{		
		//$qry	= "select  from t_dailyprocessingchart dpc left join t_dailyprocessingchart_entry dpce on dpc.id=dpce.main_id where dpc.select_date='$searchDate' order by a.id asc, b.id asc limit $offset, $limit";
		
		$whr = " dpc.select_date='$searchDate'";

		$orderBy = " dpc.id asc, dpce.id asc";

		$limit = "$offset, $limit";

		$qry	= "select dpc.id as mainId, dpce.id as entryId, dpc.select_date, dpc.installed_capacity_id, dpce.monitoring_param_id, if (dpce.start_time,dpce.start_time,dpci.start_time) , if (dpce.stop_time,dpce.stop_time,dpci.stop_time), dpce.started_time, dpce.stopped_time, mic.name as machineryName, smp.head_name as paramHead, dpci.start_time, dpci.start_val, dpci.stop_time, dpci.stop_val, mic.per_val as machineryTime
			   from t_dailyprocessingchart dpc left join t_dailyprocessingchart_entry dpce on dpc.id=dpce.main_id 
				left join m_installed_capacity mic on mic.id=dpc.installed_capacity_id 
				left join m_set_monitoring_param smp on smp.id=dpce.monitoring_param_id 
				left join t_dailyprocessingchart_interval dpci on dpci.dpc_entry_id=dpce.id
			 ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;		

		//echo "<br>$qry";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	# Returns all Records
	function fetchAllRecords($searchDate)
	{
		$whr = " dpc.select_date='$searchDate'";

		$orderBy = " dpc.id asc, dpce.id asc";

		$qry	= "select dpc.id as mainId, dpce.id as entryId, dpc.select_date, dpc.installed_capacity_id, dpce.monitoring_param_id, if (dpce.start_time,dpce.start_time,dpci.start_time) , if (dpce.stop_time,dpce.stop_time,dpci.stop_time), dpce.started_time, dpce.stopped_time, mic.name as machineryName, smp.head_name as paramHead, dpci.start_time, dpci.start_val, dpci.stop_time, dpci.stop_val, mic.per_val as machineryTime
			   from t_dailyprocessingchart dpc left join t_dailyprocessingchart_entry dpce on dpc.id=dpce.main_id 
				left join m_installed_capacity mic on mic.id=dpc.installed_capacity_id 
				left join m_set_monitoring_param smp on smp.id=dpce.monitoring_param_id 
				left join t_dailyprocessingchart_interval dpci on dpci.dpc_entry_id=dpce.id
			 ";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
	
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function find($mainId)
	{
		$qry	= "select dpc.id, dpc.select_date, dpc.installed_capacity_id from t_dailyprocessingchart dpc where dpc.id=$mainId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# get monitoring Time
	function getMonitoringValue($mainId, $parameterId)
	{
		$qry = "select dpce.id, dpce.start_time, dpce.stop_time, dpce.started_time, dpce.stopped_time, dpce.stop_monitor from t_dailyprocessingchart_entry dpce where dpce.main_id='$mainId' and dpce.monitoring_param_id='$parameterId'";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecord($qry);
	}	

	# Get Monitor interval value
	function getMonitoringIntervalValue($parameterEntryId)
	{
		$qry = "select dpci.id, dpci.start_time, dpci.start_val, dpci.stop_time, dpci.stop_val from t_dailyprocessingchart_interval dpci where dpci.dpc_entry_id='$parameterEntryId'";

		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	# Update Processing chart
	function updateDailyProcessingChart($mainId, $selectDate)
	{
		$qry	= " update t_dailyprocessingchart set select_date='$selectDate' where id=$mainId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Add DPC Monitoring Params
	function updateDailyPCMonitoringParam($mpEntryId, $startTime, $stopTime, $startedAtTime, $stoppedAtTime, $stopMonitoring, $monitoringLastInterval)
	{		
		$qry	= " update t_dailyprocessingchart_entry set start_time='$startTime', stop_time='$stopTime', started_time='$startedAtTime', stopped_time='$stoppedAtTime', stop_monitor='$stopMonitoring', interval_last_time='$monitoringLastInterval' where id=$mpEntryId";
		//echo "<br>$qry";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	function updateIntervalParam($mpIntervalEntryId, $startTime, $startTemp, $stopTime, $stopTemp)
	{
		$qry	= " update t_dailyprocessingchart_interval set start_time='$startTime', start_val='$startTemp', stop_time='$stopTime', stop_val='$stopTemp' where id=$mpIntervalEntryId";

		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function delIntervalParamRec($mpIntervalEntryId)
	{
		$qry	=	" delete from t_dailyprocessingchart_interval where id=$mpIntervalEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else	 	$this->databaseConnect->rollback();
		return $result;
	}

	# Delete  Daily Freezing Chart Interval Entry Rec
	function deleteDFCIntervalEntryRecs($dailyFreezingChartEntryId)
	{
		$qry	=	" delete from t_dailyprocessingchart_interval where dpc_entry_id=$dailyFreezingChartEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else	 	$this->databaseConnect->rollback();
		return $result;
	}

	# Delete  Daily Freezing Chart Entry Rec
	function deleteDailyFreezingChartEntryRec($dailyFreezingChartEntryId)
	{
		$qry	=	" delete from t_dailyprocessingchart_entry where id=$dailyFreezingChartEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else	 	$this->databaseConnect->rollback();
		return $result;
	}


	function checkRecordsExist($dailyFreezingChartMainId) 
	{
		$qry	= "select b.main_id from (t_dailyprocessingchart a, t_dailyprocessingchart_entry b)  where  a.id=b.main_id and b.main_id='$dailyFreezingChartMainId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Delete Main Rec Entry
	function deleteDailyFreezingChartRec($dailyFreezingChartMainId)
	{
		$qry	= " delete from t_dailyprocessingchart where id=$dailyFreezingChartMainId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# check stop monitor rec exist
	function chkStopMParamExist($selectDate, $installedCapacityId)
	{
		$qry = "select dpce.id, dpce.start_time, dpce.stop_time, dpce.started_time, dpce.stopped_time, dpce.stop_monitor, dpc.select_date 
			from t_dailyprocessingchart dpc join t_dailyprocessingchart_entry dpce on dpce.main_id = dpc.id
			left join m_installed_capacity mic on mic.id=dpc.installed_capacity_id 
			where mic.monitor='M' and dpc.select_date=DATE_SUB('$selectDate', INTERVAL 1 DAY) and dpc.installed_capacity_id='$installedCapacityId' and  (dpce.stop_monitor='N' or  dpce.stop_monitor is null) and (dpce.started_time is not null and dpce.started_time!='')  group by dpc.installed_capacity_id";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		
		return (sizeof($result)>0)?array(true,$result[0][6]):false;
	}
	
	# Get previous Daily Processing chart
	function getPrevDPCRecs($selectDate, $installedCapacityId, $monitoringParamId)
	{
		$qry = "select dpce.id, dpc.select_date , dpce.start_time, dpce.started_time, dpce.stopped_time,  dpce.interval_last_time
			from t_dailyprocessingchart dpc join t_dailyprocessingchart_entry dpce on dpce.main_id = dpc.id
			left join m_installed_capacity mic on mic.id=dpc.installed_capacity_id 
			where mic.monitor='M' and dpc.select_date=DATE_SUB('$selectDate', INTERVAL 1 DAY) and dpc.installed_capacity_id='$installedCapacityId' and dpce.monitoring_param_id='$monitoringParamId' ";

		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[1], $rec[2], $rec[5]);
	}




	/* ============================================================================================================================*/
	//----------------------------------------------------------------------------------------------------------------------------------- #

	/*
	#Check Blank Record Exist
	function checkBlankRecord()
	{
		$qry = "select a.id from t_dailyfreezingchart_main a where a.flag=0 order by a.id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?$result[0]:false;
	}

	#Indert blank record
	function addTempDataMainTable()
	{			
		$qry	= "insert into t_dailyfreezingchart_main (flag) values(0)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	


	# Update daily activity chart Main table
	function updateDailyFreezingMainRec($mainId, $selectDate, $selectTime)
	{
		$qry	=	" update t_dailyfreezingchart_main set entry_date='$selectDate', entry_time='$selectTime', flag=1 where id=$mainId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	

	# Returns all Records (PAGING)
	function fetchPagingActivityChartRecords($searchDate, $offset, $limit)
	{
		$qry	=	"select a.id, b.id, a.entry_date, a.entry_time, b.freezer_no, b.start_time, b.start_temp, b.stop_time, b.stop_temp, b.core_temp, b.unload_time from t_dailyfreezingchart_main a left join t_dailyfreezingchart_entry b on a.id=b.main_id where a.flag=1 and a.entry_date='$searchDate' order by a.id asc, b.id asc limit $offset, $limit";
		//echo $qry;
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on Main id  and Entry Id
	
	function findR($mainId, $entryId)
	{
		$qry	=	"select a.id, b.id, a.entry_date, a.entry_time, b.freezer_no, b.start_time, b.start_temp, b.stop_time, b.stop_temp, b.core_temp, b.unload_time from t_dailyfreezingchart_main a left join t_dailyfreezingchart_entry b on a.id=b.main_id where a.id=$mainId and b.id=$entryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	// -------------- IFrame -------------------------

	# Update daily activity chart Entry table
	function addDailyFreezingEntryDetailsRec($mainId, $freezerId, $startTime, $startTemp, $stopTime, $stopTemp, $coreTemp, $unloadTime)
	{
		$qry	=	"insert into t_dailyfreezingchart_entry (main_id, freezer_no, start_time, start_temp, stop_time, stop_temp, core_temp, unload_time) values('$mainId', '$freezerId', '$startTime', '$startTemp', '$stopTime', '$stopTemp', '$coreTemp', '$unloadTime')";
 		//echo $qry;
 		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
 		
 		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

 		return $insertStatus;
	}

	#Filter Daily Catch Entry Records based Main Id
	function fetchDailyChartEntry($mainId)
	{
		$qry	=	"select b.id, b.freezer_no, b.start_time, b.start_temp, b.stop_time, b.stop_temp, b.core_temp, b.unload_time from t_dailyfreezingchart_entry b where b.main_id='$mainId'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on Main id  (Iframe
	function findDailyFreezingChartRec($entryId)
	{
		$qry	=	"select b.id, b.freezer_no, b.start_time, b.start_temp, b.stop_time, b.stop_temp, b.core_temp, b.unload_time from t_dailyfreezingchart_entry b where b.id=$entryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update daily activity chart Entry table
	function updateDailyFreezingEntryRec($entryId, $freezerId, $startTime, $startTemp, $stopTime, $stopTemp, $coreTemp, $unloadTime)
	{
		$qry	=	" update t_dailyfreezingchart_entry set freezer_no='$freezerId', start_time='$startTime', start_temp='$startTemp', stop_time='$stopTime', stop_temp='$stopTemp', core_temp='$coreTemp', unload_time='$unloadTime' where id=$entryId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	// -------------- IFrame  Ends Here-------------------------

	#Find the Closing Balace for a day
	function  getClosingActivityDetails($closingDate)
	{
		$qry = "select id, diesel_cb, ice_cb, first_generator_balance, second_generator_balance,  third_generator_balance, first_electricity_balance, second_electricity_balance, third_electricity_balance, water_balance from t_dailyfreezingchart_main where entry_date='$closingDate' and flag=1 ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array($result[1], $result[2], $result[3], $result[4], $result[5], $result[6], $result[7], $result[8], $result[9]):0;
	}
	*/

}

?>