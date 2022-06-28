<?php
class ProcessRateList
{
	/****************************************************************
	This class deals with all the operations relating to Process Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProcessRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addProcessRateList($rateListName, $startDate, $copyRateList)
	{
		$qry	=	"insert into m_processratelist (name,start_date) values('".$rateListName."','".$startDate."')";
		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();

			# Update Prev Rate List Rec END DATE
			$sDate		= explode("-",$startDate);
			$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
			$lastRateListId = $this->getPPRateList($endDate);
			if ($lastRateListId!="") {
				$updateRateListEndDate = $this->updateRateListRec($lastRateListId, $endDate);
			}

	#-----------------------------Copy Functions-------------------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="") {
				$preProcessRecords = $this->fetchAllPreProcessRecords($copyRateList);
				foreach ($preProcessRecords as $ppr) {
					$cProcessId		=	$ppr[0];
					$fishId			=	$ppr[1];
					$processes		=	$ppr[2];
					$day			=	$ppr[3];
					$rate			=	$ppr[4];
					$commission		=	$ppr[5];
					$criteria		=	$ppr[6];
					$code			=	$ppr[7];
							
					$preProcessInsertStatus = $this->addPreProcess($fishId, $processes, $day, $rate, $commission, $criteria, $code, $insertedRateListId, $cProcessId);
							
					if ($preProcessInsertStatus) {
						$newPreProcesId = $this->databaseConnect->getLastInsertedId();
					}
							
					$allYieldRecords = $this->fetchAllYieldRecords($cProcessId);
					while (list(,$v) = each($allYieldRecords)) {
						$centerId	=	$v[1];
						$yieldJan	=	$v[3];
						$yieldFeb	=	$v[4];
						$yieldMar	=	$v[5];
						$yieldApr	=	$v[6];
						$yieldMay	=	$v[7];
						$yieldJun	=	$v[8];
						$yieldJul	=	$v[9];
						$yieldAug	=	$v[10];
						$yieldSep	=	$v[11];
						$yieldOct	=	$v[12];
						$yieldNov	=	$v[13];
						$yieldDec	=	$v[14];
						$this->addYieldItem($centerId, $yieldJan, $yieldFeb, $yieldMar, $yieldApr, $yieldMay, $yieldJun, $yieldJul, $yieldAug, $yieldSep, $yieldOct, $yieldNov, $yieldDec, $newPreProcesId);
					}
					# Get All Processors Exception recs
					$processorsExptRecs = $this->fetchAllExceptionProcessor($cProcessId);
					foreach ($processorsExptRecs as $r) {
						$ppeRate = $r[3];
						$ppeCommi = $r[4];
						$ppeCriteria = $r[5];
						$selPreProcessor = $r[6];
						$this->addProcessorExmpt($selPreProcessor, $newPreProcesId, $ppeRate, $ppeCommi, $ppeCriteria);
					} // Expt Loop Ends here
				}
			}
	#----------------------------Copy Functions End   --------------------------------------------		
	
	# ------------------ Update the new rate List Starts Here ----------
	$updateRecords	= $this->getPreProcessRecords($startDate, $hStartDate);	
			
	# ------------------ Update the new rate List Ends Here ----------

		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select id, name, start_date,active,(select count(a1.id) from m_process a1 where rate_list_id=a.id) as tot from m_processratelist a order by start_date desc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllPagingRecordsRatelistActive($offset, $limit)
	{
		$qry	= "select id, name, start_date,active from m_processratelist where active=1 order by start_date desc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Recs
	function fetchAllRecords()
	{
		$qry	=	"select id, name, start_date,active,(select count(a1.id) from m_process a1 where rate_list_id=a.id) as tot from m_processratelist a from m_processratelist order by start_date desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsRateactive()
	{
		$qry	=	"select id, name, start_date,active from m_processratelist where active=1 order by start_date desc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get a Rec based on id 
	
	function find($categoryId)
	{
		$qry	=	"select id, name, start_date from m_processratelist where id=$categoryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateProcessRateList($rateListName, $startDate, $processRateListId, $hidStartDate)
	{
		$qry = " update m_processratelist set name='$rateListName', start_date='$startDate' where id=$processRateListId";
 		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			if ($startDate!=$hidStartDate) {
				# Update the records
				$updateRecords	= $this->getPreProcessRecords($startDate, $hidStartDate);
			}
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteProcessRateList($processRateListId)
	{
		$qry	=	" delete from m_processratelist where id=$processRateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			
			$lastRateListId = $this->latestRateList();
			if ($lastRateListId!="") {
				# Update Prev Rate List End Date
				$endDate = "0000-00-00";
				$updateRateListEndDate = $this->updateRateListRec($lastRateListId, $endDate);
			}
		}
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Checking Rate List Id used
	function checkRateListUse($processRateListId)
	{
		$qry	=	"select id from m_process where rate_list_id='$processRateListId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Latest Rate List  Id (using in Other screen )
	function latestRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry = "select a.id from m_processratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in DailyPreProcess
	function findRateList()
	{
		$cDate = date("Y-m-d");
		$qry	=	"select a.id,name,start_date from m_processratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$array			=	explode("-", $rec[2]);
			$startDate		=	$array[2]."/".$array[1]."/".$array[0];
			$displayRateList =  $rec[1]."&nbsp;(".$startDate.")";
		}
		return (sizeof($rec)>0)?$displayRateList:"";
	}

#---------------------------------Copy Functions---------------------------------------------
	#Fetch All Pre-Process Records
	function fetchAllPreProcessRecords($selRateList)
	{
		$qry	=	"select a.id, a.fish_id, a.processes, a.day, a.rate,a.commi,a.criteria, a.code from m_process a where rate_list_id='$selRateList'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Insert Record PreProcess Record
	function addPreProcess($fishId, $processes, $day, $rate, $commission, $criteria, $code, $insertedRateListId, $baseProcessId)
	{
		$qry	=	"insert into m_process (fish_id, processes, day, rate, commi, criteria, code, rate_list_id, base_process_id, flag) values ('$fishId', '$processes', '$day', '$rate', '$commission', '$criteria', '$code', '$insertedRateListId', '$baseProcessId', 1)";
		//echo $qry."<br>";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#Fech all Yield records
	function fetchAllYieldRecords($copyProcessId)
	{
		$qry	=	"select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where process_id='$copyProcessId'";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Add Month Wise yield for all or Each
	function addYieldItem($selLandingCenter, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec, $processMainId)
	{
		$qry="insert into m_process_yield_months (center_id, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dece,process_id) values('".$selLandingCenter."', '".$jan."','".$feb."','".$mar."','".$apr."','".$may."','".$jun."','".$jul."','".$aug."','".$sep."','".$oct."','".$nov."','".$dec."','$processMainId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	function fetchAllExceptionProcessor($processMainId)
	{
		$qry	= "select a.id, b.id, b.name, a.rate, a.commission, a.criteria, a.pre_processor_id from m_process_pre_processor a left join m_preprocessor b on a.pre_processor_id=b.id where process_id='$processMainId' order by b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Add Processor Exception
	function addProcessorExmpt($selPreProcessor, $processMainId, $rate, $commission, $criteria)
	{
		$qry="insert into m_process_pre_processor (pre_processor_id, process_id, rate, commission, criteria) values('$selPreProcessor', '$processMainId', '$rate', '$commission', '$criteria')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
#------------------------------Copy Functions End------------------------------------------------

	# ----------------------------------------------------
	# Update Daily Pre Process Entry Starts Here
	# ----------------------------------------------------
		
	# Get Pre Processed Records
	function  getPreProcessRecords($selDate, $prevDate)
	{
		if ($selDate) $dailyPreProcessRecords = $this->filterDailyPreProcessRecords($selDate, $prevDate);
		
		foreach ($dailyPreProcessRecords as $dpr) {
			$createdDate	   = $dpr[2];	
			$preProcessEntryId = $dpr[3];
			$processId	   = $dpr[4];	
			# Get Process
			list($processCode, $rateListId)	   = $this->getProcess($processId);
			$selProcessId = "";
			$cRateListId = "";			
			if ($selDate!="" && $prevDate=="") {	// While Inserting				
				$cRateListId	= $this->latestRateList();				
				$selProcessId	= $this->getCurrentProcessId($processCode,$cRateListId);
				$updateDailyPreProcessRec = $this->updateDailyPreProcessEntryRec($preProcessEntryId, $selProcessId);	
			} else if ($selDate!="" && $prevDate!="") { // While Upating
				 $cRateListId   = $this->getRateList($createdDate);
				 $selProcessId	= $this->getCurrentProcessId($processCode,$cRateListId);
				 $updateDailyPreProcessRec = $this->updateDailyPreProcessEntryRec($preProcessEntryId, $selProcessId);
			}
			//echo $selProcessId."<br>";					
		} // Loop Ends here
		# Pre-Process Account Updation		
		$dailyPreProcessAccountRecords = $this->fetchPreProcessAccountRecords($selDate, $prevDate);
			if (sizeof($dailyPreProcessAccountRecords)>0) {
				# Default Yield Tolerance
				$defaultYieldTolerance  = $this->getDefaultYieldTolerance();

				$settledAmount = "";
				$duesAmount    = "";
				$totalProcessRate = "";
				$grandTotalPreProcessesQty = 0;
				foreach ($dailyPreProcessAccountRecords as $dpr) {
					$paidStatus		=	$dpr[17];
					if ($paidStatus=='Y') { 
						$dailyPreProcessEntryId =  $dpr[3];
						$selectDate = $dpr[2];					
						$pDate		=	explode("-",$dpr[2]);
						$setldDate	=	$dpr[18];			
						$fishId			=	$dpr[1];		
						$preProcessId		=	$dpr[4];
						$preProcessorQtyId	=	$dpr[12];
						$totalArrivalQty	=	$dpr[7];
						$totalPreProcessedQty	=	$dpr[8];
						$preProcessedQty	=	$dpr[13];		
						$grandTotalPreProcessesQty += $preProcessedQty; 	
						#To Take the Rate & Commi
						/*
						$processRateRec = $this->filterProcessRec($preProcessId);
						$rate			=	$processRateRec[2];
						$commission		=	$processRateRec[3];
						$criteria		=	$processRateRec[4];	
						*/
						
						$lanCenterId 		= $dpr[19];
						$preProcessorId 	= $dpr[20];

						#To Take the Rate & Commi
						list($rate, $commission, $criteria, $ppYieldTolerance) = $this->getPProcessorExpt($preProcessId, $preProcessorId);

						######################
						$processYieldRec = $this ->findYieldRec($preProcessId, $lanCenterId);					
						$monthArray	=	array($processYieldRec[3], $processYieldRec[4], $processYieldRec[5], $processYieldRec[6], $processYieldRec[7], $processYieldRec[8], $processYieldRec[9], $processYieldRec[10], $processYieldRec[11], $processYieldRec[12], $processYieldRec[13], $processYieldRec[14]);
						$day	=	"";
						if ($pDate[1]<10) $day = $pDate[1]%10;
						else $day = $pDate[1];		
						$idealYield = $monthArray[$day-1];
						#################
						$actualYield		=	$dpr[9];
						$diffYield	= number_format(($actualYield-$idealYield),2,'.','');

						$selectCommission	=	$dpr[14];
						$selectRate		=	$dpr[15];
						$actualRate		=	$dpr[16];		
						$paidStatus		=	$dpr[17];		
						$displayCommission = "";	
						if ($selectCommission!="" && $selectCommission!=0) {
							$displayCommission = $selectCommission;
						} else {
							$displayCommission = $commission;
						}							
						$changedRate = "";	
						if ($selectRate!="" && $selectRate!=0) {
							$changedRate	=	$selectRate;
						} else {
							$changedRate		=	$rate;
						}
						
						#Criteria Calculation 1=> From / 0=>To
						$totalPreProcessAmt 	= "";
						$finalYield 		= "";
						$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;

						if ($criteria==1) {
							//if (From) and actual yield> ideal yield  then yield=actual yield
							if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
								$totalPreProcessAmt  = ($totalPreProcessedQty/($actualYield/100)) * $changedRate + $totalPreProcessedQty * $displayCommission;
								$finalYield	=	$actualYield;
							} else {
								$totalPreProcessAmt 	=	($totalPreProcessedQty/($idealYield/100)) * $changedRate + $totalPreProcessedQty * $displayCommission;
								$finalYield	=	$idealYield;
							}
						} else {
							$totalPreProcessAmt = $totalPreProcessedQty*$changedRate + $totalPreProcessedQty * $displayCommission;
							$finalYield = $idealYield;
						}
									
						$ratePerKg	=	 $totalPreProcessAmt/$totalPreProcessedQty;
						$amount		=	$preProcessedQty * $ratePerKg;		$totalRate = "";					
						if ($actualRate!="" && $actualRate!=0 && $paidStatus=='Y') {
							$totalRate	= $actualRate;	
						} else {
							$totalRate	=	number_format($amount,2,'.','');
						}
						# Column Total
						$totalProcessRate	+=$totalRate;
						if ($paidStatus=='Y') {
							$checked	=	"Checked";
							$settledAmount	= $settledAmount +	$totalRate;
						} else {
							$checked	=	"";
							$duesAmount	= $duesAmount +	$totalRate;
						}
						$disabled = "";
						$edited	  = "";
						if ($paidStatus=='Y' && $isAdmin==false && $reEdit==false) {
							$disabled = "readonly";
							$edited	  = 1;
						}		
										
						if ($paidStatus=='Y') {					
							$dailyPreProcessUpdateRec	=	$this->updatePreProcessPaidAmount($preProcessorQtyId,$displayCommission, $changedRate, $totalRate);
						}
				 	} // Paid Status Check	
				} // For Loop End
			} // Size Check
		return true;
	}
	
	function filterDailyPreProcessRecords($selDate, $prevDate)
	{
		//2009-01-09 -> 2009-01-23
		$uptdQry = "";
		if ($prevDate!="" && $selDate>$prevDate) $uptdQry = " and a.date>='$prevDate' and a.date<'$selDate' "; 
		else			$uptdQry = " and a.date>='$selDate'";
		$qry = " select a.id, a.fish_id, a.date, b.id, b.process,b.opening_bal_qty,b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id from t_dailypreprocess a, t_dailypreprocess_entries b where a.id = b.dailypreprocess_main_id $uptdQry order by a.date desc ";	
		//echo $qry ;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	// Find the Process Code
	function getProcess($processId)
	{
		$qry = " select processes, rate_list_id from m_process where id=$processId";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";
	}
		
	function getRateList($cDate)
	{
		$qry	=	"select a.id,name,start_date from m_processratelist a where '$cDate'>=date_format(a.start_date,'%Y-%m-%d') order by a.start_date desc";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}
	
	function getCurrentProcessId($processCode,$cRateListId)
	{
		$qry = " select id from m_process where processes='$processCode' and rate_list_id=$cRateListId order by id desc";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function updateDailyPreProcessEntryRec($preProcessEntryId, $selProcessId)
	{
		$qry	= " update t_dailypreprocess_entries set process='$selProcessId' where id='$preProcessEntryId'";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Fetch all record based on Processor
	function fetchPreProcessAccountRecords($selDate, $prevDate)
	{
		$uptdQry = "";
		if ($prevDate!="" && $selDate>$prevDate) $uptdQry = " and a.date>='$prevDate' and a.date<'$selDate' "; 
		else			$uptdQry = " and a.date>='$selDate' ";

		$whr = " c.preprocess_qty $uptdQry";

		if ($processor!="") $whr .= " and c.preprocessor_id='".$processor."'";
		if ($selProcessCode!="") $whr .= " and b.process=$selProcessCode";

		$qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, c.preprocessor_id from t_dailypreprocess a join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id join t_dailypreprocess_processor_qty c on b.id = c.dailypreprocess_entry_id";		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
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
	#Find Exception Landing Center Rec
	function findYieldRec($processId,$lanCenterId)
	{

		$qry	=	"select id,center_id,process_id,jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dece from m_process_yield_months where process_id=$processId and center_id='$lanCenterId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Update t_dailypreprocess table 
	function updatePreProcessPaidAmount($preProcessorQtyId,$displayCommission, $changedRate, $totalRate)
	{
		$qry	= "update t_dailypreprocess_processor_qty set select_commission='$displayCommission', select_rate='$changedRate', actual_amount='$totalRate'";	
		$qry .= "  where id='$preProcessorQtyId' ";		
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) $this->databaseConnect->commit();
		else	 $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update Daily Pre Process Entry Ends Here
	# ----------------------------------------------------

	# Check Valid Date Entry
	function chkValidDateEntry($seldate, $cId)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		else $uptdQry ="";
		$qry	= "select a.id, a.name, a.start_date from m_processratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?false:true;
	}

	function getPPRateList($selDate)
	{	
		$qry	= "select id from m_processratelist where date_format(start_date,'%Y-%m-%d')<='$selDate' order by id desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# update Rate List End date Rec
	function updateRateListRec($pageCurrentRateListId, $endDate)
	{
		$qry = " update m_processratelist set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
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



	function updateProcessRateListconfirm($preProcessId)
	{
	$qry	= "update m_processratelist set active='1' where id=$preProcessId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateProcessRateListReleaseconfirm($preProcessId)
	{
		$qry	= "update m_processratelist set active='0' where id=$preProcessId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}



}