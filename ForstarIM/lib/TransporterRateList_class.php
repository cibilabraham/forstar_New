<?php
class TransporterRateList
{
	/****************************************************************
	This class deals with all the operations relating to Transporter Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function TransporterRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# add a Record
	function addTransporterRateList($rateListName, $startDate, $copyRateList, $userId, $selTransporter, $currentRateListId, $selFunctionality)
	{
		$qry = "insert into m_transporter_ratelist (name, start_date, transporter_id, function_type) values('".$rateListName."', '".$startDate."', '$selTransporter', '$selFunctionality')";		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Prev Rate List Rec END DATE
			if ($currentRateListId!="") {
				$updateRateListEndDate = $this->updateTransporterRateListRec($currentRateListId, $startDate);
			}

	#----------------------- Copy Functions ---------------------------------------
			# Last Inserted Rate List Id
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			// "TRM"=>"Transporter Rate Master","TOC"=>"Transporter Other Charges"
			if ($copyRateList!="" && $selTransporter!="") {
				if ($selFunctionality=='TRM') {
					# Get All Transporter Rate Records
					$transporterRateRecords = $this->fetchAllTransporterRateRecords($copyRateList, $selTransporter);
					foreach ($transporterRateRecords as $trr) {
						$transporterRateId	= $trr[0];
						$selTransporter 	= $trr[1];		
						$selZone		= $trr[2];	

						// Insert New Transporter RateRec	
						$transporterRateRecIns = $this->addTransporterRate($selTransporter, $selZone, $insertedRateListId, $userId);
						if ($transporterRateRecIns) {
							$newTransporterRateId = $this->databaseConnect->getLastInsertedId();
						}
						# Get Transporter Rate Entry Recs
						$transporterRateEntryRecs = $this->getTransporterRateEntryRecs($transporterRateId);
						foreach ($transporterRateEntryRecs as $dms) {
							$transporteRateEntryId  = $dms[0];
							$weightSlabId		= $dms[1];
							$rate 			= $dms[2];
							$trptrWtSlabEntryId	= $dms[3];
							$trptrRateType		= $dms[4];
							# Insert State Wise Rec
							if ($newTransporterRateId!="") {
								$transporterRateEntryRecIns = $this->addTransporterRateEntryRec($newTransporterRateId, $weightSlabId, $rate, $trptrWtSlabEntryId, $trptrRateType);
							}
						} // Transporter Rate Entry Loop Ends
					} // Transporter Rate Recs Loop Ends
				} // TRM Loop Ends Here

				if ($selFunctionality=='TOC') {
					//$fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge

					# Get All Transporter Rate Records
					$transporterOCRecords = $this->fetchAllTransporterOtherChargeRecords($copyRateList, $selTransporter);
					foreach ($transporterOCRecords as $trr) {
						$transporterRateId	= $trr[0];
						$selTransporter 	= $trr[1];		
						$fovCharge		= $trr[2];
						$docketCharge		= $trr[3];
						$serviceTax		= $trr[4];
						$octroiServiceCharge 	= $trr[5];	
						$odaCharge		= $trr[6];	
						$surcharge		= $trr[7];

						// Insert New Transporter RateRec	
						$transporterOCRecIns = $this->addTransporterOtherCharge($selTransporter, $insertedRateListId, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId, $odaCharge, $surcharge);
					} // Transporter OC Recs Loop Ends
				} // TOC Ends Here
			}
	#-------------------- Copy Functions End -------------------------------------	
		# Update SO Transporter Rec~~~~~~~~~~~~~~~~~~~~~~
		$updateSOTransporterRec = $this->updateTransporterWiseSORecs($startDate, $prevDate, $selTransporter, $selFunctionality);	
		# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}


	# Returns all Recs
	function fetchAllPagingRecords($offset, $limit, $transporterId)
	{
		$cDate = date("Y-m-d");

		$whr 	= " a.transporter_id=b.id";

		if ($transporterId!="") $whr .= " and a.transporter_id=".$transporterId;

		if ($transporterId=="") {
			$whr .= " and (('$cDate'>=a.start_date && (a.end_date is null || a.end_date=0)) or ('$cDate'>=a.start_date and '$cDate'<=a.end_date)) "; 			
		} 
		
		$orderBy  = " b.name asc, a.start_date desc";		

		$limit = " $offset, $limit ";
				
		$qry	= "select a.id, a.name, a.start_date, b.id, b.name, a.function_type,a.active from m_transporter_ratelist a, m_transporter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Returns all Recs
	function fetchAllRecords($transporterId)
	{
		$cDate = date("Y-m-d");
		$whr 	= " a.transporter_id=b.id";

		if ($transporterId!="") $whr .= " and a.transporter_id=".$transporterId;

		if ($transporterId=="") {
			$whr .= " and (('$cDate'>=a.start_date && (a.end_date is null || a.end_date=0)) or ('$cDate'>=a.start_date and '$cDate'<=a.end_date)) "; 			
		} 
		
		$orderBy  = " b.name asc, a.start_date desc";
						
		$qry	= "select a.id, a.name, a.start_date, b.id, b.name, a.function_type from m_transporter_ratelist a, m_transporter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Get a Rec based on id 	
	function find($rateListId)
	{
		$qry = "select id, name, start_date, transporter_id, function_type from m_transporter_ratelist where id='$rateListId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateTransporterRateList($rateListName, $startDate, $transporterRateListId, $selTransporter, $selFunctionality, $prevDate)
	{
		$qry = " update m_transporter_ratelist set name='$rateListName', start_date='$startDate' where id=$transporterRateListId";
 		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			# Update SO recs
			$this->updateTransporterWiseSORecs($startDate, $prevDate, $selTransporter, $selFunctionality);
			/*
			if ($startDate!=$prevDate) {
				$this->updateTransporterWiseSORecs($startDate, $prevDate, $selTransporter, $selFunctionality);
			}*/
		}
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteTransporterRateList($transporterRateListId, $transporterId, $functionType)
	{
		$qry = " delete from m_transporter_ratelist where id=$transporterRateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			$latestRateListId = $this->latestRateList($transporterId, $functionType);
			if ($latestRateListId!="") {
				# Update Prev Rate List Date
				$sDate = "0000-00-00";
				$this->updatePrevRateListRec($latestRateListId, $sDate);
			}
		}
		else $this->databaseConnect->rollback();
		
		return $result;
	}

	#Checking Rate List Id used
	# "TRM"=>"Transporter Rate Master","TOC"=>"Transporter Other Charges"
	function checkRateListUse($transporterRateListId, $functionType)
	{
		if ($functionType=='TRM') $qry = "select id from m_transporter_rate where rate_list_id='$transporterRateListId'";
		else if ($functionType=='TOC') $qry = "select id from m_transporter_other_charge where rate_list_id='$transporterRateListId'";
		else $qry = "";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Current Rate List
	function latestRateList($transporterId, $selFunctionality)
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select a.id from m_transporter_ratelist a where a.transporter_id='$transporterId' and '$cDate'>=date_format(a.start_date,'%Y-%m-%d') and a.function_type='$selFunctionality' order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in other Screen
	function findRateList()
	{
		$cDate = date("Y-m-d");
		$qry	=	"select a.id,name,start_date from m_transporter_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
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

	#Fetch All Distributor Margin Records
	function fetchAllTransporterRateRecords($selRateList, $selTransporter)
	{
		$qry = "select id, transporter_id, zone_id from m_transporter_rate where rate_list_id='$selRateList' and transporter_id='$selTransporter'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Add a Record
	//, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge // 
	function addTransporterRate($selTransporter, $selZone, $transporterRateListId, $userId)
	{
		$qry = "insert into m_transporter_rate (transporter_id, zone_id, rate_list_id, created, createdby) values('$selTransporter', '$selZone', '$transporterRateListId', Now(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get Transporter Rate Entry Recs
	function getTransporterRateEntryRecs($transporterRateId)
	{
		$qry = " select id, weight_slab_id, rate, trptr_wt_slab_entry_id, rate_type from m_transporter_rate_entry where main_id='$transporterRateId'";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Insert Entry Recs
	function addTransporterRateEntryRec($transporterRateId, $weightSlabId, $rate, $trptrWtSlabEntryId, $trptrRateType)
	{
		$qry = "insert into m_transporter_rate_entry (main_id, weight_slab_id, rate, trptr_wt_slab_entry_id, rate_type) values('$transporterRateId', '$weightSlabId', '$rate', '$trptrWtSlabEntryId', '$trptrRateType')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# ------ TOC Records
	#Fetch All Distributor Margin Records
	function fetchAllTransporterOtherChargeRecords($selRateList, $selTransporter)
	{
		$qry = "select id, transporter_id, fov_charge, docket_charge, service_tax, octroi_service_charge, oda_charge, surcharge from m_transporter_other_charge where rate_list_id='$selRateList' and transporter_id='$selTransporter'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Add a Record
	function addTransporterOtherCharge($selTransporter, $transporterRateListId, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId, $odaCharge, $surcharge)
	{
		$qry = "insert into m_transporter_other_charge (transporter_id, rate_list_id, fov_charge, docket_charge, service_tax, octroi_service_charge, created, createdby, oda_charge, surcharge) values('$selTransporter', '$transporterRateListId', '$fovCharge', '$docketCharge', '$serviceTax', '$octroiServiceCharge', Now(), '$userId', '$odaCharge', '$surcharge')";

		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#------------------------ Copy Functions End ----------------------------------------

	# Returns all Transporter based Recs
	function filterTransporterWiseRecords($transporterId, $selFunctionality)
	{
		$qry = "select id, name, start_date from m_transporter_ratelist where transporter_id='$transporterId' and function_type='$selFunctionality' order by start_date desc";
		//$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# update Dist Rate List Rec
	function updateTransporterRateListRec($currentRateListId, $startDate)
	{
		$sDate		= explode("-",$startDate);
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_transporter_ratelist set end_date='$endDate' where id=$currentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# update Prev Rate List of current dist Rec
	function updatePrevRateListRec($currentRateListId, $endDate)
	{		
		$qry = " update m_transporter_ratelist set end_date='$endDate' where id=$currentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Checking Record Exist 	
	function checkRecExist($startDate, $selTransporter, $selFunctionality)
	{
		$qry = "select id from m_transporter_ratelist where start_date='$startDate' and transporter_id='$selTransporter' and function_type='$selFunctionality'";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Check Valid Date Entry
	function chkValidDateEntry($seldate, $selTransporter, $selFunctionality, $cId)
	{		
		if ($cId!="") $uptdQry = " and id!=$cId";
		else $uptdQry ="";	
	
		$qry	= "select id, name, start_date from m_transporter_ratelist where '$seldate'<=date_format(start_date,'%Y-%m-%d') and transporter_id='$selTransporter' and function_type='$selFunctionality' $uptdQry order by start_date desc";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?false:true;
	}

	#Find the Current Rate List
	function getTransporterValidRateListId($transporterId, $selFunctionality, $selDate)
	{
		$qry	= "select a.id from m_transporter_ratelist a where a.transporter_id='$transporterId' and a.function_type='$selFunctionality' and (('$selDate'>=date_format(a.start_date,'%Y-%m-%d') && (a.end_date is null || a.end_date=0)) or ('$selDate'>=date_format(a.start_date,'%Y-%m-%d') and '$selDate'<=date_format(a.end_date,'%Y-%m-%d'))) order by a.start_date desc";
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get Valid Rate List based on Date
	function getValidRateList($transporterId, $selFunctionality, $selDate)
	{	
		//$cDate = date("Y-m-d");
		$selDate = ($selDate!="")?$selDate: date("Y-m-d");		
		$qry = " select id from m_transporter_ratelist where transporter_id='$transporterId' and function_type='$selFunctionality' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) order by start_date desc ";
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# ~~~~~~~~~~~~~~~~~~~~  Update SO Rec When Rate List Date Changed STARTS HERE~~~~~~~~~~~~~~~~~~

	# When Rate List date Changed update all the SO rec
	function updateTransporterWiseSORecs($startDate, $prevStartDate, $selTransporter, $transType)
	{
		if ($startDate) $soRecs = $this->filterTransporterWiseSORecords($startDate, $prevStartDate, $selTransporter); 
		if (sizeof($soRecs)>0) {
			foreach ($soRecs as $sor) {
				$salesOrderId	= $sor[0];
				$dispatchDate	= $sor[1];
				$selTRMId 	= $sor[2];
				$selTOCId	= $sor[3];
				
				# Current Transporter Rate List Id
				$cRateListId = $this->getValidRateList($selTransporter, $transType, $dispatchDate);
				
				if ($transType=='TRM' && $selTRMId!=$cRateListId) {
					//echo "<br>SOID=$salesOrderId;Despatch=$dispatchDate; TRMID =$selTRMId; CTRMRLID=$cRateListId ";

					# Update TRM Rec
					$updateTRMRateListId = $this->updateSOTransporterRateList($salesOrderId, $transType, $cRateListId);	
				}
				if ($transType=='TOC' && $selTOCId!=$cRateListId) {
					//echo "<br>SOID=$salesOrderId;Despatch=$dispatchDate; TOCID =$selTOCId; CTOCRLID=$cRateListId ";

					# Update TOC Rec
					$updateTOCRateListId = $this->updateSOTransporterRateList($salesOrderId, $transType, $cRateListId);
				}

			} // SO Loop ends here
		}
		# Size chk ends here
	}

	# Filter Not Completed SO Recs
	function filterTransporterWiseSORecords($selDate, $prevDate, $selTransporter)
	{
		$uptdQry = "";
		if ($prevDate!="" && $selDate>$prevDate) $uptdQry = " and a.dispatch_date>='$prevDate' and a.dispatch_date<'$selDate' "; 
		else if ($selDate) $uptdQry = " and a.dispatch_date>='$selDate' ";
		//a.complete_status='C' and
		$qry = " select a.id, a.dispatch_date, a.transporter_rate_list_id, a.trans_oc_rate_list_id from t_salesorder a where a.transporter_id='$selTransporter' $uptdQry ";
		//echo $qry ;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update SO Transporter Rec
	function updateSOTransporterRateList($salesOrderId, $transType, $cRateListId)
	{
		$updateQry = "";
		if ($transType=='TRM') $updateQry = " transporter_rate_list_id='$cRateListId' ";
		if ($transType=='TOC') $updateQry = " trans_oc_rate_list_id='$cRateListId' ";

		$qry = " update t_salesorder set $updateQry where id='$salesOrderId' ";

 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# ~~~~~~~~~~~~~~~~~~~~  Update SO Rec When Rate List Date Changed End Here~~~~~~~~~~~~~~~~~~


	// Get Active rate list for selected date
	function getActiveRateListForSelDate($seldate, $selTransporter, $selFunctionality, $cId)
	{	
		$uptdQry ="";	
		if ($cId!="") $uptdQry = " and id!=$cId";
			
		$drArr = array();
		$qry1	= "select id, name, start_date from m_transporter_ratelist where date_format(start_date,'%Y-%m-%d')<='$seldate' and transporter_id='$selTransporter' and function_type='$selFunctionality' $uptdQry order by start_date desc";
		//echo "1=><br>$qry1<br>";
		$tRec = $this->databaseConnect->getRecord($qry1);

		$drArr[0] = array($tRec[0], $tRec[1], $tRec[2],1);
				
		$qry2	= "select id, name, start_date from m_transporter_ratelist where date_format(start_date,'%Y-%m-%d')>='$seldate' and transporter_id='$selTransporter' and function_type='$selFunctionality' $uptdQry order by start_date desc";
		//echo "<br>$qry2<br>";
		$bRec = $this->databaseConnect->getRecord($qry2);
		$drArr[1] = array($bRec[0], $bRec[1], $bRec[2],2);

		return $drArr;
	}
	
	function updateTransporterRateListconfirm($transporterId)
	{
	$qry	= "update m_transporter_ratelist set active='1' where id=$transporterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateTransporterRateListReleaseconfirm($transporterId)
	{
		$qry	= "update m_transporter_ratelist set active='0' where id=$transporterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>