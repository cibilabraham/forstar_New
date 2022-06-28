<?php
class Process
{  
	/****************************************************************
	This class deals with all the operations relating to PROCESS 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function Process(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addBlankRec()
	{
		$qry		=	"insert into m_process (flag) values('0')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Check Blank Record Exist
	function checkBlankRecord()
	{
		$qry = " select a.id, a.fish_id, a.processes, a.day, a.rate, a.commi, a.flag, a.criteria from m_process a where a.fish_id is null or a.fish_id=0 and a.rate_list_id is null order by a.id desc ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?$result[0]:false;	
	}

	#Getting Processes Unique Records
	function checkProcessesUniqueRecords($fishId, $Processes, $rateListId)
	{
		$qry	= " select id from m_process where fish_id='$fishId' and processes='$Processes' and rate_list_id='$rateListId' ";
		//echo $qry."<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	#Check Further Process
	function checkFurtherProcess($fishId, $Processes)
	{
		$qry = "select id from m_process where fish_id='$fishId' and processes='$Processes' and noprocess='Y'";
		//echo $qry."<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	#Add New Process
	function addProcess($fishId, $Processes, $Day, $Rate, $Commission, $Criteria, $lastId, $copyFrom, $copyFishId, $copyPreProcessCode, $preProcessCode, $rateListId, $noProcess)
	{
		if ($copyFrom) // Insert records into  table using copyFromId 
		{
			# Fetch all records from table using copyFromId ( FishId)
			$selRecord = $this->processRecordsFilter($copyFishId, $copyPreProcessCode, $rateListId);

			if (sizeof($selRecord) > 0) {	
				$copyProcessId			=	$selRecord[0];
				$copyDay			=	$selRecord[3];
				$copyRate			=	$selRecord[4];
				$copyCommission			=	$selRecord[5];
				$copyCriteria			=	$selRecord[6];
				$copyRateListId			=	$selRecord[7];
				$cpyFromYieldTolerance		= 	$selRecord[8];
						
				$qry	= " update m_process set fish_id='$fishId' , processes='$Processes', day='$copyDay', rate='$copyRate', commi='$copyCommission', criteria='$copyCriteria', flag=1, code='$preProcessCode', rate_list_id='$copyRateListId', noprocess='$noProcess' where id=$lastId";
				//echo $qry;
				$allYieldRecords	=	$this->fetchAllYieldRecords($copyProcessId);
				while (list(,$v) = each($allYieldRecords)) {
					$centerId			=	$v[1];
					$yieldJan			=	$v[3];
					$yieldFeb			=	$v[4];
					$yieldMar			=	$v[5];
					$yieldApr			=	$v[6];
					$yieldMay			=	$v[7];
					$yieldJun			=	$v[8];
					$yieldJul			=	$v[9];
					$yieldAug			=	$v[10];
					$yieldSep			=	$v[11];
					$yieldOct			=	$v[12];
					$yieldNov			=	$v[13];
					$yieldDec			=	$v[14];
					$this->addYieldItem($centerId, $yieldJan, $yieldFeb,$yieldMar, $yieldApr, $yieldMay, $yieldJun, $yieldJul, $yieldAug, $yieldSep, $yieldOct, $yieldNov,$yieldDec, $lastId);
				}

				# Get All Processors Exception recs
				$processorsExptRecs = $this->fetchAllExceptionProcessor($copyProcessId);
				foreach ($processorsExptRecs as $r) {
					$ppeRate = $r[3];
					$ppeCommi = $r[4];
					$ppeCriteria = $r[5];
					$selPreProcessor = $r[6];
					$this->addProcessorExmpt($selPreProcessor, $lastId, $ppeRate, $ppeCommi, $ppeCriteria);
				} // Expt Loop Ends here
			}
		} 
		else // Direct Insert(ie. No Copy From) 
		{
			$qry	= " update m_process set fish_id='$fishId' , processes='$Processes', day='$Day', rate='$Rate', commi='$Commission', criteria='$Criteria', flag=1, code='$preProcessCode', rate_list_id='$rateListId', noprocess='$noProcess' where id=$lastId ";
		}
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# -------------------------------------------------------
	# Returns all Processes (PAGING)	
	function fetchAllPagingRecords($recordsFilterId, $selRateList, $offset, $limit)
	{	
		$whr = "a.fish_id = b.id and rate_list_id='$selRateList'";

		if ($recordsFilterId!=0) $whr .= " and a.fish_id='$recordsFilterId' ";

		$orderBy	= " b.name asc, a.code asc";

		$limit = "$offset, $limit";	
	
		$qry	= "select a.id, a.fish_id, a.processes, a.day, a.rate, a.commi, a.flag, a.criteria, b.id, b.name, a.code,a.active from m_process a, m_fish b";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Processes	
	function fetchAllRecords($selRateList, $recordsFilterId=null)
	{			
		$whr = "a.fish_id = b.id and rate_list_id='$selRateList'";

		if ($recordsFilterId!=0) $whr .= " and a.fish_id='$recordsFilterId' ";

		$orderBy	= " b.name asc, a.code asc";	
	
		$qry	= "select a.id, a.fish_id, a.processes, a.day, a.rate, a.commi, a.flag, a.criteria, b.id, b.name, a.code,active from m_process a, m_fish b";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy) 	$qry .= " order by ".$orderBy;		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# -------------------------------------------------------	

	# Returns all Processes (PAGING)	
	/*
	function fetchPagingRecords($selRateList, $offset, $limit)
	{		
		$qry	= "select a.id, a.fish_id, a.processes, a.day, a.rate,a.commi,a.flag,a.criteria, b.id, b.name, a.code from m_process a, m_fish b where a.fish_id = b.id and rate_list_id='$selRateList' order by b.name asc, a.code asc limit $offset, $limit";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

	# Filter m_process table using fish id // It is using in daiylPre-Process
	function processRecFilter($filterId, $selRateList)
	{	
		$qry	= " select a.id, a.fish_id, a.processes, a.day, a.rate, a.commi, a.flag, a.criteria, b.id, b.name, a.code from m_process a, m_fish b where a.fish_id=b.id and b.id='$filterId' and rate_list_id='$selRateList' order by b.name asc, a.code asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
		
	# Filter m_process table using fish id  (PAGING)
	/*
	function processRecPagingFilter($filterId,$selRateList, $offset, $limit)
	{
		$qry	=	"select a.id, a.fish_id, a.processes, a.day,a.rate,a.commi,a.flag,a.criteria, b.id, b.name, a.code from m_process a, m_fish b where a.fish_id = b.id and b.id=$filterId and rate_list_id=$selRateList order by b.name asc, a.code asc limit $offset, $limit";
		//echo $qry; 
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/	
	#Get Last Record in m_process table
	function fetchLastRecord()
	{
		$qry	=	"select a.id, a.fish_id, a.processes, a.day,a.rate,a.commi,a.flag,a.criteria from m_process a order by a.id desc";
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Processes based on id 
	function find($processId)
	{
		$qry = "select id, fish_id, processes, day, rate, commi, criteria, code, rate_list_id, noprocess from m_process where id=$processId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Process
	function deleteProcess($processId)
	{
		$qry	=	" delete from m_process where id=$processId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update a Process
	function updateProcess($processId, $fishId, $Processes, $Day, $Rate, $Commission, $Criteria, $preProcessCode, $rateListId)
	{
		$qry =  " update m_process set fish_id='$fishId', processes='$Processes', day='$Day', rate='$Rate', commi='$Commission', criteria='$Criteria', code='$preProcessCode', rate_list_id='$rateListId', flag=1 where id=$processId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			#For updating the t_dailypreprocess entries
			$this->getDailyPreProcessRecs($processId);
		} else $this->databaseConnect->rollback();	
		return $result;	
	}
	
	#Add Exception Landing center -- using in ProcessYield.php
	function addExceptionCenter($selExceptionLanding,$processMainId)	
	{
		$qry="insert into m_process_yield_months (center_id,process_id) values('".$selExceptionLanding."','$processMainId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}	

	function fetchAllExceptionCenterRecords($processMainId)
	{
		$qry	= "select a.id, a.center_id, b.id, b.name from m_process_yield_months a, m_landingcenter b where a.center_id = b.id and process_id='$processMainId' order by a.id desc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Add Month Wise yield for all or Each
	function addYieldItem($selLandingCenter, $Jan, $Feb, $Mar, $Apr, $May, $Jun, $Jul, $Aug, $Sep, $Oct, $Nov, $Dec, $processMainId)
	{
		$qry="insert into m_process_yield_months (center_id, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dece,process_id) values('".$selLandingCenter."', '".$Jan."','".$Feb."','".$Mar."','".$Apr."','".$May."','".$Jun."','".$Jul."','".$Aug."','".$Sep."','".$Oct."','".$Nov."','".$Dec."','$processMainId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Filter all Landing Center
	function filterAllExceptionLandingCenter($processMainId)
	{
		$qry	=	"select a.id, a.process_id, a.exception_landing_id, b.id, b.center_id, b.process_id  from  m_process_yield a right join  m_process_yield_months b on a.process_id=b.process_id and a.exception_landing_id=b.center_id and b.process_id='$processMainId' order by b.center_id";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find Exception Landing Center Rec
	function findExceptionCenterRec($processYieldId)
	{
		$qry	=	"select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where id=$processYieldId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Update Exception Landing Center Rec
	function updateProcessYieldMonths($processYieldId, $Jan, $Feb, $Mar, $Apr, $May, $Jun, $Jul, $Aug, $Sep, $Oct, $Nov, $Dec)
	{
	
		$qry	=	" update m_process_yield_months set  jan='$Jan', feb='$Feb', mar='$Mar', apr='$Apr', may='$May', jun='$Jun',jul='$Jul',aug='$Aug',sep='$Sep',oct='$Oct',nov='$Nov',dece='$Dec' where id=$processYieldId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	#Find All Landing Center Rec
	function findAllCenterRec($processYieldId,$processMainId)
	{
		$qry	=	"select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where center_id=$processYieldId and process_id='$processMainId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Delete Process Yield Record
	function deleteProcessYield($processYieldId)
	{
		$qry	= " delete from m_process_yield_months where id=$processYieldId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Delete Process Yield Record
	function deleteYieldProcessWiseRec($processId)
	{
		$qry	= " delete from m_process_yield_months where process_id= '$processId' ";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	#Getting Unique Records
	function fetchAllUniqueRecords($processMainId,$selLandingCenter)
	{
		$qry	=	"select * from m_process_yield_months where center_id='$selLandingCenter' and process_id='$processMainId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Process code COPY FROM 
	function processRecordsFilter($copyFishId, $copyPreProcessCode, $rateListId)
	{
		$qry	=	"select id, fish_id, processes, day, rate, commi, criteria, rate_list_id from m_process where fish_id=$copyFishId and code='$copyPreProcessCode' and rate_list_id='$rateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	#Fech all Yield records
	function fetchAllYieldRecords($copyProcessId)
	{
		$qry	= "select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where process_id='$copyProcessId'";
		//echo "<br>$qry<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter m_process table using fish id // It is using in daiylPre-Process
	function preProcessCodeRecFilter($filterId, $selRateList)
	{
		$qry	=	"select a.id, a.fish_id, a.processes, a.day,a.rate,a.commi,a.flag,a.criteria, b.id, b.name, a.code from m_process a, m_fish b where a.fish_id = b.id and b.id=$filterId and a.rate_list_id='$selRateList' and a.code is not null order by a.fish_id desc";
		//echo $qry; 		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find Exception Landing Center Rec
	function findYieldRec($processId)
	{
		$qry	= " select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where process_id=$processId ";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Process Yiel Average
	function getYieldAverage($processId)
	{
		$qry = " select sum(jan+feb+mar+apr+may+jun+jul+aug+sep+oct+nov+dece)/(12*count(*)) from m_process_yield_months where process_id='$processId' group by process_id";
		//echo "<br>$qry<br>"; 
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0]; 
	}

	###CHECK Selected PRE-PROCESS IS LINKED WOTH ANOTHER TABLE
	function checkPreProcessLinked($processId)
	{
		$qry	=	"select a.id from m_process a, t_dailypreprocess_entries b  where  a.id=b.process and a.id='$processId' ";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return sizeof($result)>0?true:false;
	}

	#Get daily Pre-Process Entry t_dailypreprocess a , t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c
	function  getDailyPreProcessRecs($processId) 
	{
		$qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, d.criteria, c.preprocessor_id from t_dailypreprocess a join t_dailypreprocess_entries b on a.id=b.dailypreprocess_main_id join t_dailypreprocess_processor_qty c on b.id=c.dailypreprocess_entry_id join m_process d on b.process=d.id where b.process='$processId'";
		//echo $qry;
		$dailyPreProcessRecords	=	array();
		$dailyPreProcessRecords	=	$this->databaseConnect->getRecords($qry);
		if (sizeof($dailyPreProcessRecords)>0) {
		# Default Yield Tolerance
		$defaultYieldTolerance  = $this->getDefaultYieldTolerance();

		$totalProcessRate = "";
		foreach ($dailyPreProcessRecords as $dpr) {
			
			$dailyPreProcessEntryId =  $dpr[3];
			$pDate			=	explode("-", $dpr[2]);			
			$preProcessId		=	$dpr[4];			
			$preProcessorQtyId	=	$dpr[12];
			$totalArrivalQty	=	$dpr[7];
			$totalPreProcessedQty	=	$dpr[8];
			$preProcessedQty	=	$dpr[13];					
			$criteria		=	$dpr[20];								
			$lanCenterId 		=	$dpr[19];
			$preProcessorId		= 	$dpr[21];
			#############
			$processYieldRec = $this->getYieldRec($preProcessId, $lanCenterId);
			$monthArray	=	array($processYieldRec[3], $processYieldRec[4], $processYieldRec[5], $processYieldRec[6], $processYieldRec[7], $processYieldRec[8], $processYieldRec[9], $processYieldRec[10], $processYieldRec[11], $processYieldRec[12], $processYieldRec[13], $processYieldRec[14]);
			$day	=	"";
			if($pDate[1]<10) $day =	$pDate[1]%10;
			else $day = $pDate[1];

			$idealYield = $monthArray[$day-1];
			#################					
			$enterdIdealYield 	= 	$dpr[10];
			#To Take the Rate & Commi
			list($rate, $commission, $criteria, $ppYieldTolerance) = $this->getPProcessorExpt($preProcessId, $preProcessorId);

			$selectCommission	=	$dpr[14];
			$selectRate		=	$dpr[15];
			$actualRate		=	$dpr[16];
			$actualYield		=	$dpr[9];
			$diffYield		=	abs($actualYield-$idealYield);			
			
			//echo "$idealYield & $actualYield<br>";
			#Criteria Calculation 1=> From / 0=>To
			$totalPreProcessAmt = "";
			$finalYield 	= "";
			$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;

			if ($criteria==1) {
					//if (From) and actual yield> ideal yield  then yield=actual yield
					if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
						$totalPreProcessAmt 	=	($totalPreProcessedQty/($actualYield/100)) * $selectRate + $totalPreProcessedQty * $selectCommission;		
					} else {
						$totalPreProcessAmt 	=	($totalPreProcessedQty/($idealYield/100)) * $selectRate + $totalPreProcessedQty * $selectCommission;
						}
					} else {
						$totalPreProcessAmt		=	$totalPreProcessedQty*$selectRate + $totalPreProcessedQty * $selectCommission;						
					}
							
					$ratePerKg	=	 $totalPreProcessAmt/$totalPreProcessedQty;

					$amount		=	$preProcessedQty * $ratePerKg;												
					$actualAmount	=	number_format($amount,2,'.','');
			
					#update Pre processor Qty Rec
					$dailyPreProcessQtyRecUpdate	= $this->updatePreProcessorQtyRec($preProcessorQtyId, $actualAmount);
					#update Pre Processor Entry Rec						
					$dailyPreProcessEntryRecUpdate =$this->updatePreProcessEntryRec($dailyPreProcessEntryId, $idealYield, $diffYield);
				}
		}						
	}

	#Find Exception Landing Center Rec
	function getYieldRec($processId,$lanCenterId)
	{

		$qry	=	"select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where process_id='$processId' and center_id='$lanCenterId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Update t_dailypreprocess_entries table 
	function updatePreProcessorQtyRec($preProcessorQtyId, $actualAmount)
	{
			$qry	= "update t_dailypreprocess_processor_qty set actual_amount='$actualAmount' where id='$preProcessorQtyId'";
							
			$result	=	$this->databaseConnect->updateRecord($qry);
			//echo $qry."<br>";
			if ($result) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
			return $result;	
	}

	# Update daily preprocess entry
	function updatePreProcessEntryRec($preProcessorEntryId, $idealYield, $diffYield) 
	{
		$qry 	=	"update t_dailypreprocess_entries set ideal_yield='$idealYield', diff_yield='$diffYield' where id='$preProcessorEntryId'";
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function chkProcessCodeExist($preProcessCode, $rateListId, $cId)
	{
		if ($cId!="") $uptdQry = " and id!=$cId ";

		$qry = " select id from m_process where code='$preProcessCode' and rate_list_id='$rateListId' $uptdQry ";
		$result = $this->databaseConnect->getRecords($qry);	
		return (sizeof($result)>0)?true:false;
	}

	# ----------- Pre-Processor Exception Starts here --------------
	function fetchAllExceptionProcessor($processMainId)
	{
		$qry	= "select a.id, b.id, b.name, a.rate, a.commission, a.criteria, a.pre_processor_id from m_process_pre_processor a left join m_preprocessor b on a.pre_processor_id=b.id where process_id='$processMainId' order by b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Find All Pre-Processor Rec
	function findProcessorException($preProcessorEId,$processMainId)
	{
		$qry	= " select id, pre_processor_id, rate, commission, criteria from m_process_pre_processor where pre_processor_id=$preProcessorEId and process_id='$processMainId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Find Exception Landing Center Rec
	function findExceptionProcessorRec($preProcessorEId)
	{
		$qry = "select id, pre_processor_id, rate, commission, criteria from m_process_pre_processor where id=$preProcessorEId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Add Exception Pre-Processors
	function addExceptionPreProcessor($selExceptionProcessor, $processMainId)	
	{
		$qry="insert into m_process_pre_processor (pre_processor_id, process_id) values('".$selExceptionProcessor."','$processMainId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	

	# Check Unique Records
	function chkProcessorExceptionExist($processMainId, $selExceptionProcessor, $exceptionEntryId)
	{
		$qry	= "select id from m_process_pre_processor where pre_processor_id='$selExceptionProcessor' and process_id='$processMainId'";
		if ($exceptionEntryId) $qry .= " and id!='$exceptionEntryId'"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Add Processor Exception
	function addProcessorExmpt($selPreProcessor, $processMainId, $rate, $commission, $criteria, $yieldTolerance)
	{
		$qry="insert into m_process_pre_processor (pre_processor_id, process_id, rate, commission, criteria, yield_tolerance) values('$selPreProcessor', '$processMainId', '$rate', '$commission', '$criteria', '$yieldTolerance')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Update Exception Processor Rec
	function updateProcessorException($processorExptId, $rate, $commission, $criteria)
	{	
		$qry = " update m_process_pre_processor set  rate='$rate', commission='$commission', criteria='$criteria' where id=$processorExptId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Delete Processor Record
	function deleteProcessorExptRec($processorExptId)
	{
		$qry	= " delete from m_process_pre_processor where id=$processorExptId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	#Delete Processor Exception
	function deleteAllProcessorExptRec($processId)
	{
		$qry	= " delete from m_process_pre_processor where process_id= '$processId' ";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getSingleProcessorsExpt($processMainId)
	{
		$qry	= " select id, pre_processor_id, rate, commission, criteria from m_process_pre_processor where process_id='$processMainId' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)==1)?$result[0][0]:"";
	}

	function chkRateExist($processMainId)
	{
		$result = $this->fetchAllExceptionProcessor($processMainId);
		$noRateExist = false;
		foreach ($result as $r) {
			$rate = $r[3];
			$commi = $r[4];
			if (($rate==0 || $rate=="") && ($commi==0 || $commi=="")) $noRateExist = true;
		}		
		return $noRateExist;
	}
	# ----------- Pre-Processor Exception Ends here --------------

	# Display Area
	function displayPPException($processMainId)
	{	
		$processorsExptRecs = $this->fetchExptedProcessor($processMainId);
		if (sizeof($processorsExptRecs)>0) {		
			$displayPPE	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>Processor</td><td>Rate</td><td>Commission</td><td>Criteria</td><td>Yield<br/>Tolerance(%)</td></tr>";	
			$totGrossWt = 0;	
			foreach ($processorsExptRecs as $r) {
				$ppName = $r[2];
				$ppeRate = $r[3];
				$ppeCommi = $r[4];
				$ppeCriteria = $r[5];
				$selPreProcessor = $r[6];	
				$criteria = ($ppeCriteria==0)?"To":"From";	
				$diplayPPName = ($ppName)?$ppName:"ALL";
				$selYieldTol = ($r[7]!=0)?$r[7]:"";
				
				$displayPPE	.= "<tr bgcolor=#fffbcc><td class=listing-item>$diplayPPName</td><td class=listing-item align=right>$ppeRate</td><td class=listing-item align=right>$ppeCommi</td><td class=listing-item align=center>$criteria</td><td class=listing-item align=center>$selYieldTol</td></tr>";		
			}					
			$displayPPE	.= "</table>";
		}
		return $displayPPE;
	}

	function displayExceptionLC($processMainId)
	{	
		$exptLCRecs = $this->fetchAllExceptionCenterRecords($processMainId);
		if (sizeof($exptLCRecs)>0) {		
			$displayPPE	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>Landing Center</td><td>Yield<br/>Average(%)</td></tr>";	
			$totGrossWt = 0;	
			foreach ($exptLCRecs as $r) {
				$lcName = $r[3];
				$landingCenterId	= $r[1];	
				$lcWiseYAvg = $this->getLCWiseYieldAverage($processMainId, $landingCenterId);
				$displayPPE	.= "<tr bgcolor=#fffbcc><td class=listing-item>$lcName</td><td class=listing-item align=right>$lcWiseYAvg</td></tr>";		
			}					
			$displayPPE	.= "</table>";
		}
		return $displayPPE;
	}

	function getLCWiseYieldAverage($processMainId, $landingCenterId)
	{
		$qry = " select sum(jan+feb+mar+apr+may+jun+jul+aug+sep+oct+nov+dece)/(12*count(*)) from m_process_yield_months where process_id='$processMainId' and center_id='$landingCenterId' group by process_id";
		//echo "<br>$qry<br>"; 
		$rec = $this->databaseConnect->getRecord($qry);
		return number_format($rec[0],2,'.',''); 
	}

	function getProcessorExptRecs($processMainId)
	{
		$qry	= "select a.id, a.pre_processor_id, a.rate, a.commission, a.criteria, a.yield_tolerance from m_process_pre_processor a where a.process_id='$processMainId' order by a.pre_processor_id asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	#Update Exception Processor Rec
	function updateProcessorExmpt($exceptionEntryId, $selPreProcessor, $processRate, $processCommission, $processCriteria, $yieldTolerance)
	{	
		$qry = " update m_process_pre_processor set  pre_processor_id='$selPreProcessor', rate='$processRate', commission='$processCommission', criteria='$processCriteria', yield_tolerance='$yieldTolerance' where id=$exceptionEntryId";

		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function defaultRateExist($processMainId)
	{
		$result = $this->fetchAllExceptionProcessor($processMainId);		
		return (sizeof($result)>0)?true:false;
	}

	function getDefaultPreProcessRate($processMainId)
	{
		$qry = "select id, rate, commission, criteria, yield_tolerance from m_process_pre_processor where process_id='$processMainId' and pre_processor_id=0";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[1], $rec[2], $rec[3], $rec[4]);
	}

	# Fetch only Excepted Recs
	function fetchExptedProcessor($processMainId)
	{
		$qry	= "select a.id, b.id, b.name, a.rate, a.commission, a.criteria, a.pre_processor_id, a.yield_tolerance from m_process_pre_processor a left join m_preprocessor b on a.pre_processor_id=b.id where process_id='$processMainId' and  a.pre_processor_id!=0 order by b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDefaultYieldTolerance()
	{
		$qry = "select default_yield_tolerance from c_system";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	# get Exception Recs
	function getPProcessorExpt($preProcessId, $processorId)
	{		
		$qry = " select rate, commission, criteria, yield_tolerance from m_process_pre_processor where process_id='$preProcessId' and (pre_processor_id='$processorId' or pre_processor_id=0) order by pre_processor_id desc";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2], $result[0][3]):array();	
	}


function updateProcessconfirm($processMainId)
	{
	$qry	= "update m_process set active='1' where id=$processMainId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateProcessReleaseconfirm($processMainId)
	{
		$qry	= "update m_process set active='0' where id=$processMainId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}


}