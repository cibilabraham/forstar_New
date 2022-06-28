<?php
class DailyIceUsage
{
	/****************************************************************
	This class deals with all the operations relating to Daily Ice Usage
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function DailyIceUsage(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add Daily Ice usage
	function addDailyIceUsage($selectDate, $supplier, $issuedTo, $qty, $unitId, $sold, $soldQty, $adjQty, $rate, $amount, $pymntRecd, $userId, $customerType, $plantId, $partyName, $partyLocation)
	{
		$qry = "insert into t_ice_usage (select_date, supplier_id, issued_to, qty, unit_id, sold, sold_qty, rate, amount, adjust_qty, paid_amt, created, created_by, customer_type, plant_id, party_name, party_location) values('$selectDate', '$supplier', '$issuedTo', '$qty', '$unitId', '$sold', '$soldQty', '$rate', '$amount', '$adjQty', '$pymntRecd', NOW() ,'$userId', '$customerType', '$plantId', '$partyName', '$partyLocation')";
 		//echo $qry;
 		$insertStatus = $this->databaseConnect->insertRecord($qry); 		
 		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
 		return $insertStatus;
	}

	# Fetch All Recs
	function fetchAllPagingRecords($searchDate, $offset, $limit)
	{		
		$whr = " diu.select_date='$searchDate'";

		$orderBy = " diu.select_date asc";

		$limit = "$offset, $limit";

		$qry	= "select diu.id, diu.select_date, diu.supplier_id, diu.issued_to, diu.qty, diu.unit_id, diu.sold, diu.sold_qty, diu.rate, diu.amount, diu.adjust_qty, diu.paid_amt, sm.name as suppName, mp.name as plantName, msu.name as stkUnit, diu.customer_type, diu.plant_id, diu.party_name, diu.party_location 
			   from t_ice_usage diu left join supplier sm on sm.id=diu.supplier_id 
				left join m_plant mp on mp.id=diu.plant_id
				left join m_stock_unit msu on msu.id=diu.unit_id
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
		$whr = " diu.select_date='$searchDate'";

		$orderBy = " diu.select_date asc";

		$qry	= "select diu.id, diu.select_date, diu.supplier_id, diu.issued_to, diu.qty, diu.unit_id, diu.sold, diu.sold_qty, diu.rate, diu.amount, diu.adjust_qty, diu.paid_amt, sm.name as suppName, mp.name as plantName, msu.name as stkUnit, diu.customer_type, diu.plant_id, diu.party_name, diu.party_location 
			   from t_ice_usage diu left join supplier sm on sm.id=diu.supplier_id 
				left join m_plant mp on mp.id=diu.plant_id
				left join m_stock_unit msu on msu.id=diu.unit_id
			";

		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
	
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Record  based on Main id
	function find($mainId)
	{
		$qry = "select id, select_date, supplier_id, issued_to, qty, unit_id, sold, sold_qty, rate, amount, adjust_qty, paid_amt, customer_type, plant_id, party_name, party_location from t_ice_usage where id=$mainId";
		//echo $qry;

		return $this->databaseConnect->getRecord($qry);
	}

	# Update daily activity chart Main table
	function updateDailyIceUsageRec($mainId, $selectDate, $supplier, $issuedTo, $qty, $unitId, $sold, $soldQty, $adjQty, $rate, $amount, $pymntRecd, $customerType, $plantId, $partyName, $partyLocation)
	{
		$qry	= " update t_ice_usage set select_date='$selectDate', supplier_id='$supplier', issued_to='$issuedTo', qty='$qty', unit_id='$unitId', sold='$sold', sold_qty='$soldQty', rate='$rate', amount='$amount', adjust_qty='$adjQty', paid_amt='$pymntRecd', customer_type='$customerType', plant_id='$plantId', party_name='$partyName', party_location='$partyLocation' where id=$mainId ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Main Rec Entry
	function deleteDailyIceUsageRec($dailyIceUsageId)
	{
		$qry	= " delete from t_ice_usage where id=$dailyIceUsageId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	function addIceUsageDaysTotal($selectDate, $daysTotalQty, $daysIssuedQty, $daysSoldQty, $userId)
	{
		$qry = "insert into t_ice_usage_track (entry_date, days_qty, issued_qty, sold_qty, created, created_by) values('$selectDate', '$daysTotalQty', '$daysIssuedQty', '$daysSoldQty', NOW() ,'$userId')";
 		//echo $qry;
 		$insertStatus = $this->databaseConnect->insertRecord($qry); 		
 		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
 		return $insertStatus;
	}

	function getIceUsageDaysTotal($selectDate)
	{
		$qry	= "select id, days_qty, issued_qty, sold_qty from t_ice_usage_track where entry_date='$selectDate'";

		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2], $rec[3]);
	}

	function updateIceUsageDaysTotal($daysQtyTrackId, $selectDate, $daysTotalQty, $daysIssuedQty, $daysSoldQty)
	{
		$qry = " update t_ice_usage_track set entry_date='$selectDate', days_qty='$daysTotalQty', issued_qty='$daysIssuedQty', sold_qty='$daysSoldQty' where id='$daysQtyTrackId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


}
?>