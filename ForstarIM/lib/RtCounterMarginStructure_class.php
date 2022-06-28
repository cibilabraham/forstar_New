<?php
class RetailCounterMarginStructure
{
	/****************************************************************
	This class deals with all the operations relating to Retail Counter Margin Structure
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RetailCounterMarginStructure(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addRtCounterMarginStructure($selRetailCounter, $selProduct, $margin, $retCtMarginRateListId, $userId)
	{
		$qry = "insert into m_rtcounter_margin (retail_counter_id, product_id, margin, rate_list_id, created, createdby) values('$selRetailCounter', '$selProduct', '$margin', '$retCtMarginRateListId', Now(), '$userId')";
		//echo $qry;
		$insertStatus 	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	/*
	function fetchAllPagingRecords($selRateList, $offset, $limit)
	{
		$qry = "select a.id, a.retail_counter_id, a.product_id, a.rate_list_id, b.name, c.code, a.margin from m_rtcounter_margin a, m_retail_counter b, m_product_manage c where c.id=a.product_id and b.id=a.retail_counter_id and a.rate_list_id='$selRateList' order by b.name asc, c.code asc limit $offset, $limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}
	*/

	# Returns all Records
	function fetchAllRecords($rtCounterId, $rtCounterRateListFilterId)
	{
		$cDate = date("Y-m-d");

		$whr = " a.retail_counter_id = b.id and a.product_id = c.id and b.state_id = d.id and d.id = e.state_id ";		
			
		if ($rtCounterId=="") $whr .= "";
		else $whr .= " and a.retail_counter_id=".$rtCounterId;

		if ($rtCounterRateListFilterId=="") $whr .= "";
		else $whr .= " and a.rate_list_id=".$rtCounterRateListFilterId;

		if ($rtCounterRateListFilterId=="") {
			$whr .= " and a.rate_list_id = mrl.id and (('$cDate' >= mrl.start_date && (mrl.end_date is null || mrl.end_date = 0)) or ('$cDate' >= mrl.start_date and '$cDate' <= mrl.end_date)) "; 
			$tableUpdate = " , m_rtcounter_margin_ratelist mrl";
		} else {
			$whr .= "";
			$tableUpdate = "";
		}

		$groupBy        = " a.retail_counter_id, a.margin ";
		$orderBy 	= " b.name asc ";
		
		$qry = "select a.id, a.retail_counter_id, a.product_id, a.rate_list_id, b.name, a.margin ,a.active from m_rtcounter_margin a, m_retail_counter b, m_product_manage c, m_state d, m_city e $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo "<br>$qry<br>";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));	
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $rtCounterId, $rtCounterRateListFilterId)
	{		
		$cDate = date("Y-m-d");

		$whr = " a.retail_counter_id = b.id and a.product_id = c.id and b.state_id = d.id and d.id = e.state_id ";
		
		if ($rtCounterId=="") $whr .= "";
		else $whr .= " and a.retail_counter_id=".$rtCounterId;

		if ($rtCounterRateListFilterId=="") $whr .= "";
		else $whr .= " and a.rate_list_id=".$rtCounterRateListFilterId;

		if ($rtCounterRateListFilterId=="") {
			$whr .= " and a.rate_list_id = mrl.id and (('$cDate' >= mrl.start_date && (mrl.end_date is null || mrl.end_date = 0)) or ('$cDate' >= mrl.start_date and '$cDate' <= mrl.end_date)) "; 
			$tableUpdate = " , m_rtcounter_margin_ratelist mrl";
		} else {
			$whr .= "";
			$tableUpdate = "";
		}

		$groupBy        = " a.retail_counter_id, a.margin ";
		$orderBy 	= " b.name asc ";
		$limit 		= " $offset,$limit";


		$qry = "select a.id, a.retail_counter_id, a.product_id, a.rate_list_id, b.name, a.margin,a.active from m_rtcounter_margin a, m_retail_counter b, m_product_manage c, m_state d, m_city e $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;			
		//echo "<br>$qry<br>";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
		/*
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
		*/
	}
	
	# Get a Record based on id
	function find($rtCounterMarginId)
	{
		$qry = "select id, retail_counter_id, product_id, rate_list_id, margin from m_rtcounter_margin where id=$rtCounterMarginId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateRtCounterMarginStructure($rtCounterMarginId, $selRetailCounter, $selProduct, $retCtMarginRateListId, $margin)
	{
		$qry = "update m_rtcounter_margin set retail_counter_id='$selRetailCounter', product_id='$selProduct', rate_list_id='$retCtMarginRateListId', margin='$margin' where id=$rtCounterMarginId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	# Update Rec
	function updateRtCtMarginStructure($rtCounterMarginId, $margin)
	{
		$qry = "update m_rtcounter_margin set margin='$margin' where id=$rtCounterMarginId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# Delete a Record
	function deleteRtCounterMarginStructure($rtCounterMarginId)
	{
		$qry =	" delete from m_rtcounter_margin where id=$rtCounterMarginId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	#Checking same entry exist
	function checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProduct, $rtCounterMarginId)
	{
		if ($rtCounterMarginId!="") $updateQry = " and id!='$rtCounterMarginId'"; // While Updating
		else $updateQry = "";

		$qry = "select id from m_rtcounter_margin where retail_counter_id='$selRetailCounter' and rate_list_id='$retCtMarginRateListId' and product_id='$selProduct' $updateQry";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	/*
		@ Get Product based on category wise, state wise and group wise selection
	*/
	function getProductRecords($selPCategory, $selPState, $selPGroup)
	{
		$cSelected = 0;
		if ($selPCategory!="") {
			$whr .= " category_id='$selPCategory' ";
			$cSelected = 1;
		}
		else $whr .= "";

		if ($cSelected==1 && $selPState!=0)  $whr .= " and ";
		else $whr .= "";

		if ($selPState!=0) $whr .= " product_state_id='$selPState' ";
		else $whr .= "";

		if ($selPGroup!=0) $whr .= " and product_group_id='$selPGroup' ";
		else $whr .= "";
		
		$qry = " select id, name from m_product_manage";
		if ($whr!="") $qry .= " where ".$whr;
		
		//echo $qry;

		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	/* Get RT Counter wise assigned Records */
	function getRtCounterMarginProductRecs($retailCounterId, $marginPercent, $rtctRateListId)
	{
		$whr = " a.product_id = b.id and a.retail_counter_id='$retailCounterId' and FORMAT(a.margin,2)='$marginPercent' and a.rate_list_id='$rtctRateListId' ";			
				
		$orderBy 	= " b.name asc ";
		
		$qry = "select a.product_id, b.code, b.name, a.id  from m_rtcounter_margin a, m_product_manage b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		//echo "<br>".$qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Rt Ct Margin Rec
	function getRtCtMarginRec($rtCounterId, $selProductId, $currentRateListId)
	{
		$qry = " select id from m_rtcounter_margin where retail_counter_id='$rtCounterId' and product_id='$selProductId' and rate_list_id='$currentRateListId' ";
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}


	function updateRtMarginconfirm($rtCounterMarginId)
	{
		$qry	= "update m_rtcounter_margin set active='1' where id=$rtCounterMarginId";
 		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateRtMarginReleaseconfirm($rtCounterMarginId)
	{
		$qry	= "update m_rtcounter_margin set active='0' where id=$rtCounterMarginId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>