<?php
class RetailCounterMarginRateList
{
	/****************************************************************
	This class deals with all the operations relating to Retail Counter Margin Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function RetailCounterMarginRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#add a Record
	function addRtCounterMarginRateList($rateListName, $startDate, $copyRateList, $userId, $selRetailCounter)
	{
		$currentRateList = $this->latestRateList($selRetailCounter);
		if ($currentRateList!="") {
			$updateRateListEndDate = $this->updateRtCounterRateListRec($currentRateList, $startDate);
		}
		$qry = "insert into m_rtcounter_margin_ratelist (name, start_date, retail_counter_id) values('".$rateListName."', '".$startDate."', '$selRetailCounter')";
		
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
	#----------------------- Copy Functions ---------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="") {
				$rtCounterMarginRecords = $this->fetchAllRtCounterMarginRecords($copyRateList, $selRetailCounter);
			
				foreach ($rtCounterMarginRecords as $rcm) {
					$rtCounterMarginId	= $rcm[0];
					$selRetailCounter = $rcm[1];		
					$selProduct	= $rcm[2];					
					$margin	= $rcm[3];
					//$userId = $rcm[4];
					// Insert New Retail Counter Margin rec	
					$rtCounterMarginInsertStatus = $this->addRtCounterMarginStructure($selRetailCounter, $selProduct, $margin, $insertedRateListId, $userId);
				}
			}
	#-------------------- Copy Functions End -------------------------------------				
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# update Old  Rate List Rec
	function updateRtCounterRateListRec($currentRateList, $startDate)
	{
		$sDate		= explode("-",$startDate); 		
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_rtcounter_margin_ratelist set end_date='$endDate' where id='$currentRateList'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	function fetchAllPagingRecords($offset, $limit, $rtCounterFilterId)
	{	
		$whr 	= " a.retail_counter_id=b.id ";

		if ($rtCounterFilterId!="") $whr .= " and a.retail_counter_id= ".$rtCounterFilterId;
		else $whr .= "";

		$orderBy  = " a.start_date desc, a.name asc, b.name asc ";
		$limit = " $offset, $limit ";
	
		$qry	= "select a.id, a.name, a.start_date, a.retail_counter_id, b.name,a.active,(select count(a1.id) from m_rtcounter_margin a1 where a1.rate_list_id=a.id) as tot from m_rtcounter_margin_ratelist a, m_retail_counter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}

	# Returns all Recs
	function fetchAllRecords($rtCounterFilterId)
	{	
		//$qry = "select a.id, a.name, a.start_date, a.retail_counter_id, b.name from m_rtcounter_margin_ratelist a, m_retail_counter b where   a.retail_counter_id=b.id order by a.start_date desc, a.name asc, b.name asc";
		$whr 	= " a.retail_counter_id=b.id ";

		if ($rtCounterFilterId!="") $whr .= " and a.retail_counter_id= ".$rtCounterFilterId;
		else $whr .= "";

		$orderBy  = " a.start_date desc, a.name asc, b.name asc ";
		
		$qry	= "select a.id, a.name, a.start_date, a.retail_counter_id, b.name,a.active,(select count(a1.id) from m_rtcounter_margin a1 where a1.rate_list_id=a.id) as tot from m_rtcounter_margin_ratelist a, m_retail_counter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsRetailActive($rtCounterFilterId)
	{	
		//$qry = "select a.id, a.name, a.start_date, a.retail_counter_id, b.name from m_rtcounter_margin_ratelist a, m_retail_counter b where   a.retail_counter_id=b.id order by a.start_date desc, a.name asc, b.name asc";
		$whr 	= " a.retail_counter_id=b.id and a.active=1";

		if ($rtCounterFilterId!="") $whr .= " and a.retail_counter_id= ".$rtCounterFilterId;
		else $whr .= "";

		$orderBy  = " a.start_date desc, a.name asc, b.name asc ";
		
		$qry	= "select a.id, a.name, a.start_date, a.retail_counter_id, b.name,a.active from m_rtcounter_margin_ratelist a, m_retail_counter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get a Rec based on id 	
	function find($rateListId)
	{
		$qry = "select id, name, start_date, retail_counter_id from m_rtcounter_margin_ratelist where id=$rateListId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateRtCounterMarginRateList($rateListName, $startDate, $rtCounterMarginRateListId)
	{
		$qry = " update m_rtcounter_margin_ratelist set name='$rateListName', start_date='$startDate' where id='$rtCounterMarginRateListId'";
 		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteRtCounterMarginRateList($rtCounterMarginRateListId, $rtCounterId)
	{
		$qry = " delete from m_rtcounter_margin_ratelist where id='$rtCounterMarginRateListId' ";

		$result	=	$this->databaseConnect->delRecord($qry);

		if ($result) {
			$this->databaseConnect->commit();
			# Find the Prev Rate List Id
			$currentRateList = $this->latestRateList($rtCounterId);
			# Update the Prev Latestest Rate List
			$this->updatePrevRateList($currentRateList);
		}
		else $this->databaseConnect->rollback();
		
		return $result;
	}

	# Update the Prev rate List
	function updatePrevRateList($currentRateList)
	{
		$qry = " update m_rtcounter_margin_ratelist set end_date='0000-00-00' where id='$currentRateList'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Checking Rate List Id used
	function checkRateListUse($rtCounterMarginRateListId, $selRetailCounter)
	{
		$qry	= "select id from m_rtcounter_margin where rate_list_id='$rtCounterMarginRateListId' and retail_counter_id='$selRetailCounter' ";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Current Rate List
	function latestRateList($rtCounterId)
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select a.id from m_rtcounter_margin_ratelist a where a.retail_counter_id='$rtCounterId' and '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in other Screen
	function findRateList()
	{
		$cDate = date("Y-m-d");
		$qry	=	"select a.id,name,start_date from m_rtcounter_margin_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
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
	function fetchAllRtCounterMarginRecords($selRateList, $selRetailCounter)
	{
		$qry = "select id, retail_counter_id, product_id, margin, rate_list_id, createdby from m_rtcounter_margin where rate_list_id='$selRateList' and retail_counter_id='$selRetailCounter'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Insert Retail Counter Margin Structure 
	function addRtCounterMarginStructure($selRetailCounter, $selProduct, $margin, $insertedRateListId, $userId)
	{
		$qry = "insert into m_rtcounter_margin (retail_counter_id, product_id, margin, rate_list_id, created, createdby) values('$selRetailCounter', '$selProduct', '$margin', '$insertedRateListId', Now(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#------------------------ Copy Functions End ----------------------------------------

	# Returns all Distibutor based Recs
	function filterRtCounterWiseRecords($rtCounterId)
	{
		$qry = "select id, name, start_date from m_rtcounter_margin_ratelist where retail_counter_id='$rtCounterId' order by start_date desc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Checking Record Exist 	
	/*
	function checkRecExist($startDate, $selRetailCounter)
	{
		$qry = "select id from m_rtcounter_margin_ratelist where start_date='$startDate' and retail_counter_id='$selRetailCounter'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	*/
	# Checking Record Exist 	
	function checkRecExist($startDate, $selRetailCounter, $cId)
	{	
		$uptdQry ="";
		if ($cId!="")	$uptdQry = " and id!=$cId";
		else 		$uptdQry = "";

		$qry = "select id from m_rtcounter_margin_ratelist where '$startDate'<=date_format(start_date,'%Y-%m-%d') and retail_counter_id='$selRetailCounter' $uptdQry";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateRtCounterMarginRateListconfirm($rtCounterId){
		$qry	= "update m_rtcounter_margin_ratelist set active='1' where id=$rtCounterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateRtCounterMarginRateListReleaseconfirm($rtCounterId){
		$qry	= "update m_rtcounter_margin_ratelist set active='0' where id=$rtCounterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
}

?>