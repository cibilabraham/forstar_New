<?php
class StateVatRateList
{
	/****************************************************************
	This class deals with all the operations relating to State Vat Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function StateVatRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# add a Record
	function addStateVatRateList($rateListName, $startDate, $copyRateList, $userId, $selState, $stateVatCurrentRateListId)
	{
		$qry = "insert into m_statevat_ratelist (name, start_date, state_id) values('".$rateListName."', '".$startDate."', '$selState')";		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Prev Rate List Rec END DATE
			if ($stateVatCurrentRateListId!="") {
				$updateRateListEndDate = $this->updateStateVatRateListRec($stateVatCurrentRateListId, $startDate);
			}

	#----------------------- Copy Functions ---------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="" && $selState!="") {
				$stateVatMainEntryRecs = $this->fetchAllStateVatMainEntryRecords($copyRateList, $selState);
				foreach ($stateVatMainEntryRecs as $dmr) {
					$stateVatId	= $dmr[0];
					$selState 	= $dmr[1];		
										
					// Insert New State Vat rec	
					$stateVatRecIns = $this->addStateVat($selState, $insertedRateListId, $userId);
					if ($stateVatRecIns) {
						$newStateVatEntryId = $this->databaseConnect->getLastInsertedId();
					}
					# Get State Vat Entry Recs
					$stateVatEntryRecs = $this->getStateVatEntryRecs($stateVatId);
					foreach ($stateVatEntryRecs as $ver) {
						//$stateVatEntryId 	= $ver[0];
						$selProdCategory 	= $ver[2]; 
						$selProdState		= $ver[3]; 	
						$selProdGroup		= $ver[4]; 	
						$vat			= $ver[5];
						# Insert State Wise Rec
						if ($newStateVatEntryId!="") {
							$stateVatEntryRecIns = $this->addVatEntries($newStateVatEntryId, $selProdCategory, $selProdState, $selProdGroup, $vat);
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
	function fetchAllPagingRecords($offset, $limit, $stateId)
	{
		$whr 	= " a.state_id=b.id";

		if ($stateId!="") $whr .= " and a.state_id=".$stateId;
		else {
			$whr .= " and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";
		}
		
		$orderBy  = " b.name asc, a.start_date desc";
		$limit = " $offset, $limit ";
				
		$qry	= "select a.id, a.name, a.start_date, b.id, b.name,a.active from m_statevat_ratelist a, m_state b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Returns all Recs
	function fetchAllRecords($stateId)
	{
		$whr 	= " a.state_id=b.id";

		if ($stateId!="") $whr .= " and a.state_id=".$stateId;
		else {
			$whr .= " and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";
		}
		
		$orderBy  = " b.name asc, a.start_date desc";
						
		$qry	= "select a.id, a.name, a.start_date, b.id, b.name,a.active from m_statevat_ratelist a, m_state b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Get a Rec based on id 	
	function find($rateListId)
	{
		$qry = "select id, name, start_date, state_id, end_date from m_statevat_ratelist where id=$rateListId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateStateVatRateList($rateListName, $startDate, $stateVatRateListId)
	{
		$qry = " update m_statevat_ratelist set name='$rateListName', start_date='$startDate' where id=$stateVatRateListId";
 		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteDistMarginRateList($stateVatRateListId, $stateId)
	{
		$qry = " delete from m_statevat_ratelist where id=$stateVatRateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			$latestRateListId = $this->latestRateList($stateId);
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
	function checkRateListUse($stateVatRateListId)
	{
		$qry	=	"select id from m_state_vat where rate_list_id='$stateVatRateListId'";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Current Rate List
	function latestRateList($stateId)
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select a.id from m_statevat_ratelist a where a.state_id='$stateId' and '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in other Screen
	function findRateList()
	{
		$cDate	= date("Y-m-d");
		$qry	= "select a.id,name,start_date from m_statevat_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
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
	function fetchAllStateVatMainEntryRecords($selRateList, $selState)
	{
		$qry = "select id, state_id from m_state_vat where rate_list_id='$selRateList' and state_id='$selState'";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Add a Record
	function addStateVat($selState, $insertedRateListId, $userId)
	{
		$qry = "insert into m_state_vat (state_id, created, createdby, rate_list_id) values('$selState', NOW(), '$userId', '$insertedRateListId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get State wise vat entry Recs
	function getStateVatEntryRecs($stateVatId)
	{
		$qry = " select id, main_id, product_category_id, product_state_id, product_group_id, vat from m_state_vat_entry where main_id='$stateVatId' ";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Add Dist Margin State Wise Rec
	function addVatEntries($lastId, $selPCategory, $prodStateId, $prodGroupId, $vatPercent)
	{
		$qry = "insert into m_state_vat_entry (main_id, product_category_id, product_state_id, product_group_id, vat) values('$lastId', '$selPCategory', '$prodStateId', '$prodGroupId', '$vatPercent')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	#------------------------ Copy Functions End ----------------------------------------

	# Returns all State based Recs
	function filterStateWiseVatRateListRecords($stateId)
	{
		$qry = "select id, name, start_date from m_statevat_ratelist where state_id='$stateId' order by start_date desc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# update Dist Rate List Rec
	function updateStateVatRateListRec($stateVatCurrentRateListId, $startDate)
	{
		$sDate		= explode("-",$startDate);
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_statevat_ratelist set end_date='$endDate' where id=$stateVatCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# update Prev Rate List of current dist Rec
	function updatePrevRateListRec($stateVatCurrentRateListId, $sDate)
	{		
		$qry = " update m_statevat_ratelist set end_date='$endDate' where id=$stateVatCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Checking Record Exist 	
	function checkRecExist($startDate, $selState, $cId)
	{
		//$qry = "select id from m_statevat_ratelist where start_date='$startDate' and state_id='$selState'";
		/*			
			$qry	= "select a.id, a.name, a.start_date from m_processratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";
		*/
		$uptdQry ="";
		if ($cId!="")	$uptdQry = " and id!=$cId";		

		$qry = "select id from m_statevat_ratelist where '$startDate'<=date_format(start_date,'%Y-%m-%d') and state_id='$selState' $uptdQry";

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	/**
	* Get State Wise vat filtered rate list recs (using in StateVatMaster)
	*/
	function getStateWiseVatFilterRateListRecs($stateId)
	{
		$qry = "select id, name, start_date from m_statevat_ratelist where state_id='$stateId' order by start_date desc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		$displayRateList = "";
		$startDate 	= "";
		while (list(,$v) = each($result)) {
			$rateListName = $v[1];
			$sDate		= explode("-",$v[2]);
			$startDate  	= date("d/m/Y",mktime(0, 0, 0,$sDate[1],$sDate[2],$sDate[0])); //End Date
			$displayRateList = nl2br($rateListName."(".$startDate.")");
			$resultArr[$v[0]] = $displayRateList;
		}
		return $resultArr;
	}

	# Get Rate List based on Date
	function getValidRateList($stateId, $selDate)
	{	
		$qry	= " select id from m_statevat_ratelist where state_id='$stateId' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) order by start_date desc ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}
	function updatestateVatRateconfirm($stateId)
	{
	$qry	= "update m_statevat_ratelist set active='1' where id=$stateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatestateVatRateReleaseconfirm($stateId)
	{
		$qry	= "update m_statevat_ratelist set active='0' where id=$stateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>