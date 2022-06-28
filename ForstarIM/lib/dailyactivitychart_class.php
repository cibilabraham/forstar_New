<?php
class DailyActivityChart
{
	/****************************************************************
	This class deals with all the operations relating to Daily Activity Chart
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function DailyActivityChart(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	/**
	* Add Main rec
	*/	
	function addMainRec($selectDate, $selectTime, $userId)
	{
		$qry = "insert into t_dailyactivitychart_main (entry_date, entry_time, created, created_by) values ('$selectDate', '$selectTime', NOW(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	/**
	* Add Daily Activity chart entry 
	*/	
	function addDACEntryRec($dacMainId, $damEntryId, $closingBal, $openingBal, $diffBal, $produced, $purchased, $used, $osSupply, $osSale)
	{
		$qry = "insert into t_dailyactivitychart_entry (main_id, dam_set_entry_id , closing_bal, opening_bal, diff_val, produced, purchased, used, os_supply, os_sales) values ('$dacMainId', '$damEntryId ', '$closingBal', '$openingBal', '$diffBal', '$produced', '$purchased', '$used', '$osSupply', '$osSale')";
		//echo "<br>$qry";

		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	/**
	* Checking duplicate entry exist
	*/

	function checkDupEntry($selDate, $selEntryId)
	{
		$qry	= "select id from t_dailyactivitychart_main where entry_date='$selDate'";		
		if ($selEntryId) $qry .= " and id!=$selEntryId";
		//echo $qry;

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	/**
	* Returns all Records (PAGING)
	*/	
	function fetchPagingActivityChartRecords($searchDate, $offset, $limit)
	{
		$qry	= "select a.id, a.entry_date, a.entry_time from t_dailyactivitychart_main a where a.entry_date='$searchDate' order by a.id asc limit $offset, $limit";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	/**
	* Returns all Records
	*/
	function fetchAllRecords()
	{
		$qry	= "select a.id from t_dailyactivitychart_main a order by a.id asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	/**
	* Get Daily activity chart entry recs
	*/
	function getDAChartEntryRec($mainId, $damEntryId)
	{
		$qry	= "select id, closing_bal, opening_bal, diff_val, produced, purchased, used, os_supply, os_sales from t_dailyactivitychart_entry where main_id='$mainId' and dam_set_entry_id='$damEntryId'";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}


	/**
	* Update Main rec
	*/	
	function updateMainRec($mainId, $selectDate, $selectTime)
	{		

		$qry	= " update t_dailyactivitychart_main set entry_date='$selectDate', entry_time='$selectTime' where id=$mainId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	/**
	* Update Daily activity entry rec
	*/	
	function updateDACEntryRec($dacEntryId, $damEntryId, $closingBal, $openingBal, $diffBal, $produced, $purchased, $used, $osSupply, $osSale)
	{
		$qry	= " update t_dailyactivitychart_entry set dam_set_entry_id='$damEntryId', closing_bal='$closingBal', opening_bal='$openingBal', diff_val='$diffBal', produced='$produced', purchased='$purchased', used='$used', os_supply='$osSupply', os_sales='$osSale' where id=$dacEntryId";
		//echo $qry;

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	/**
	* Delete daily activity chart rec
	*/
	function deleteDAChartRec($dailyActivityChartMainId)
	{		
		$qry1 = " delete from t_dailyactivitychart_entry where main_id=$dailyActivityChartMainId";
		$this->databaseConnect->delRecord($qry1);		

		$qry  = " delete from t_dailyactivitychart_main where id=$dailyActivityChartMainId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	/**
	* Get Record  based on Main id  
	*/
	function find($mainId)
	{
		$qry	= "select a.id, a.entry_date, a.entry_time from t_dailyactivitychart_main a where a.id=$mainId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

# ===================================================================================================================================
	 
	#Find the Closing Balace for a day
	function  getClosingActivityDetails($closingDate)
	{
		$qry = "select id, diesel_cb, ice_cb, first_generator_balance, second_generator_balance,  third_generator_balance, first_electricity_balance, second_electricity_balance, third_electricity_balance, water_balance from t_dailyactivitychart_main where entry_date='$closingDate' and flag=1 ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array($result[1], $result[2], $result[3], $result[4], $result[5], $result[6], $result[7], $result[8], $result[9]):0;
	}


	#Get main Id
	function getMainTableId($selDate)
	{
		$qry = "select id from t_dailyactivitychart_main where entry_date='$selDate' ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";		
	}
}

?>