<?php
class DistributorMarginRateList
{
	/****************************************************************
	This class deals with all the operations relating to Distributor Margin Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DistributorMarginRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#add a Record
	function addDistMarginRateList($rateListName, $startDate, $copyRateList, $userId, $selDistributor, $distCurrentRateListId)
	{
		$qry = "insert into m_distmargin_ratelist (name, start_date, distributor_id) values('".$rateListName."', '".$startDate."', '$selDistributor')";		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Prev Rate List Rec END DATE
			if ($distCurrentRateListId!="") {
				$updateRateListEndDate = $this->updateDistributorRateListRec($distCurrentRateListId, $startDate);
			}

	#----------------------- Copy Functions ---------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="" && $selDistributor!="") {
				$distMarginRecords = $this->fetchAllDistMarginRecords($copyRateList, $selDistributor);
				foreach ($distMarginRecords as $dmr) {
					$distMarginId	= $dmr[0];
					$selDistributor = $dmr[1];		
					$selProduct	= $dmr[2];
					
					// Insert New Dist Margin rec	
					$distMarginStructInsertStatus = $this->addDistMarginStructure($selDistributor, $selProduct, $insertedRateListId, $userId);
					if ($distMarginStructInsertStatus) {
						$newDistMarginId = $this->databaseConnect->getLastInsertedId();
					}
					# Get Dist Margin State Wise Recs
					$distMarginStateEntryRecs = $this->getDistMarginStructStateRecs($distMarginId);
					$cityId = "";
					foreach ($distMarginStateEntryRecs as $dms) {
						$distMarginStateEntryId = $dms[0];
						$selStateId		= $dms[1];
						$avgMargin		= $dms[2];
						$octroi			= $dms[3];
						$vat			= $dms[4];
						$freight		= $dms[5];		
						$transportCost		= $dms[6];
						$distStateEntryId	= $dms[7];
						$actualMgn		= $dms[8];
						$finalMargin		= $dms[9];
						$cityId			= $dms[10];
						$areaIds		= $dms[11];
						$vatCSTInc		= $dms[12];
						
						# Insert State Wise Rec
						if ($newDistMarginId!="") {
							$distMgnStateWiseRecIns = $this->addDistMarginStateWiseRec($newDistMarginId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMgn, $finalMargin, $cityId, $areaIds, $vatCSTInc);
							#Find the Last inserted Id From m_distributor_margin_state
							$distMarginStateWiseLastId =$this->databaseConnect->getLastInsertedId();
						}
						#fetch all margin entry Records
						$distMarginEntryRecs = $this->fetchAllDistMarginEntryRecs($distMarginStateEntryId);
						foreach ($distMarginEntryRecs as $mer) {
							$marginStructureId = $mer[0];
							$percentage	   = $mer[1];
							$distMagnStructEntry = $this->addDistMagnStructEntryRec($distMarginStateWiseLastId, $marginStructureId, $percentage);
						}
					}
				}
			}
	#-------------------- Copy Functions End -------------------------------------		
		
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}


	# Returns all Recs
	function fetchAllPagingRecords($offset, $limit, $distributorId)
	{
		$whr 	= " a.distributor_id=b.id";

		if ($distributorId!="") $whr .= " and a.distributor_id=".$distributorId;
		else {
			$whr .= " and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";
		}		

		$orderBy  = " b.name asc, a.start_date desc";
		$limit = " $offset, $limit ";
				
		$qry	= "select a.id, a.name, a.start_date, b.id, b.name,a.active from m_distmargin_ratelist a, m_distributor b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "<br>$qry";

		return $result;
	}

	# Returns all Recs
	function fetchAllRecords($distributorId)
	{
		$whr = " a.distributor_id=b.id";

		if ($distributorId!="") $whr .= " and a.distributor_id=".$distributorId;
		else $whr .= " and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";
		
		$orderBy  = " b.name asc, a.start_date desc";
						
		$qry	= "select a.id, a.name, a.start_date, b.id, b.name,a.active from m_distmargin_ratelist a, m_distributor b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "<br>$qry";
		return $result;
	}

	# Get a Rec based on id 	
	function find($rateListId)
	{
		$qry = "select id, name, start_date, distributor_id, end_date from m_distmargin_ratelist where id=$rateListId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateDistMarginRateList($rateListName, $startDate, $distMarginRateListId)
	{
		$qry = " update m_distmargin_ratelist set name='$rateListName', start_date='$startDate' where id=$distMarginRateListId";
 		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteDistMarginRateList($distMarginRateListId, $distributorId)
	{
		$qry = " delete from m_distmargin_ratelist where id=$distMarginRateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			$latestRateListId = $this->latestRateList($distributorId);
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
	function checkRateListUse($distMarginRateListId)
	{
		$qry	=	"select id from m_distributor_margin where rate_list_id='$distMarginRateListId'";
		//echo "<br>$qry<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Current Rate List
	function latestRateList($distributorId)
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select a.id from m_distmargin_ratelist a where a.distributor_id='$distributorId' and '$cDate'>=date_format(a.start_date,'%Y-%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in other Screen
	function findRateList()
	{
		$cDate = date("Y-m-d");
		$qry	=	"select a.id,name,start_date from m_distmargin_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
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
	function fetchAllDistMarginRecords($selRateList, $selDistributor)
	{
		$qry = "select id, distributor_id, product_id from m_distributor_margin where rate_list_id='$selRateList' and distributor_id='$selDistributor'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Add a Record
	function addDistMarginStructure($selDistributor, $selProduct, $distMarginRateListId, $userId)
	{
		$qry = "insert into m_distributor_margin (distributor_id, product_id, rate_list_id, created, createdby) values('$selDistributor', '$selProduct', '$distMarginRateListId', Now(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	/*
	#Insert Dist Margin Structure 
	function addDistMarginStructure($selDistributor, $selProduct, $cstDisc, $avgMargin, $insertedRateListId, $userId, $transportCost)
	{
		$qry = "insert into m_distributor_margin (distributor_id, product_id, cst_discount, avg_margin, rate_list_id, created, createdby, transport_cost) values('$selDistributor', '$selProduct', '$cstDisc', '$avgMargin', '$insertedRateListId', Now(), '$userId', '$transportCost')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	*/

	# Get Dist Margin State wise Recs
	function getDistMarginStructStateRecs($distMarginId)
	{
		$qry = " select id, state_id, avg_margin, octroi, vat, freight, transport_cost, dist_state_entry_id, actual_margin, final_margin, city_id, area_ids, vat_cst_include from m_distributor_margin_state where distributor_margin_id='$distMarginId'";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Add Dist Margin State Wise Rec
	function addDistMarginStateWiseRec($distMarginLastId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMgn, $finalMargin, $cityId, $areaIds, $vatCSTInc)
	{
		$qry = "insert into m_distributor_margin_state (distributor_margin_id, state_id, avg_margin, transport_cost, octroi, vat, freight, dist_state_entry_id, actual_margin, final_margin, city_id, area_ids, vat_cst_include) values('$distMarginLastId', '$selStateId', '$avgMargin', '$transportCost', '$octroi', '$vat', '$freight', '$distStateEntryId', '$actualMgn', '$finalMargin', '$cityId', '$areaIds', '$vatCSTInc')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#fetch All Entry Records
	function fetchAllDistMarginEntryRecs($distMarginId)
	{
		$qry = "select margin_structure_id, percentage from m_distributor_margin_entry where dist_state_entry_id='$distMarginId'";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# add dist Magn Struct entry Rec
	function addDistMagnStructEntryRec($newDistMarginId, $marginStructureId, $percentage)
	{
		$qry = "insert into m_distributor_margin_entry (dist_state_entry_id, margin_structure_id, percentage) values('$newDistMarginId', '$marginStructureId', '$percentage')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;		
	}
	#------------------------ Copy Functions End ----------------------------------------

	# Returns all Distibutor based Recs
	function filterDistributorWiseRecords($distributorId)
	{
		$qry = "select id, name, start_date from m_distmargin_ratelist where distributor_id='$distributorId' order by start_date desc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# update Dist Rate List Rec
	function updateDistributorRateListRec($distCurrentRateListId, $startDate)
	{
		$sDate		= explode("-",$startDate);
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_distmargin_ratelist set end_date='$endDate' where id=$distCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# update Prev Rate List of current dist Rec
	function updatePrevRateListRec($distCurrentRateListId, $sDate)
	{		
		$qry = " update m_distmargin_ratelist set end_date='$endDate' where id=$distCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Checking Record Exist 	
	function checkRecExist($startDate, $selDistributor)
	{
		$qry = "select id from m_distmargin_ratelist where start_date='$startDate' and distributor_id='$selDistributor'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Rate List based on Date
	function getRateList($selDistributor, $selDate)
	{	
		$qry	= "select id from m_distmargin_ratelist where distributor_id='$selDistributor' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Check Valid Date Entry
	function chkValidDateEntry($seldate, $distributorId, $cId)
	{		
		if ($cId!="") $uptdQry = " and id!=$cId";
		else $uptdQry ="";
		//$qry	= "select id, name, start_date, end_date from m_distmargin_ratelist where ('$seldate'<=date_format(start_date,'%Y-%m-%d') or '$seldate'<=date_format(end_date,'%Y-%m-%d')) and distributor_id='$distributorId' $uptdQry order by start_date desc";
		$qry	= "select id, name, start_date from m_distmargin_ratelist where '$seldate'<=date_format(start_date,'%Y-%m-%d') and distributor_id='$distributorId' $uptdQry order by start_date desc";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?false:true;
	}

	# Find the Prev Rate List Id
	function prevRateListId($distributorId, $distMarginRateListId)
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select a.id from m_distmargin_ratelist a where a.distributor_id='$distributorId' and '$cDate'>=date_format(a.start_date,'%Y-%m-%d') and id!='$distMarginRateListId' order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}


	// Dist Mgn recs based on date
	function getDistMgnStateWiseRecs($stateId, $startDate)
	{		
		$qry = " select 
				dm.id, dm.distributor_id, dm.product_id, dm.rate_list_id, md.name as distName 
			from 
				m_distributor_margin dm join m_distributor_margin_state dms on dm.id=dms.distributor_margin_id 
				join m_distmargin_ratelist mrl on dm.rate_list_id=mrl.id join m_distributor md on md.id=dm.distributor_id
			where 
				dms.state_id='$stateId' and date_format(mrl.start_date,'%Y-%m-%d')<'$startDate' 
				and (date_format(mrl.end_date,'%Y-%m-%d')>'$startDate' or (mrl.end_date is null || mrl.end_date=0)) 
			group by dm.rate_list_id, dm.distributor_id
			order by dm.distributor_id asc 
			";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	function updatedistributorconfirm($distributorId)
	{
	$qry	= "update m_distmargin_ratelist set active='1' where id=$distributorId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatedistributorReleaseconfirm($distributorId)
	{
		$qry	= "update m_distmargin_ratelist set active='0' where id=$distributorId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}

?>