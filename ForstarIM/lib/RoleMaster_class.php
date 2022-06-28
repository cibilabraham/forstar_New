<?php
class StateMaster
{
	/****************************************************************
	This class deals with all the operations relating to State Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function StateMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addState($stateCode, $stateName, $billingState, $entryTax, $salesZoneId)
	{
		$qry	= "insert into m_role (code, name, billing_state, entry_tax, sales_zone_id) values('$stateCode', '$stateName', '$billingState', '$entryTax', '$salesZoneId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $salesZoneFilter)
	{
		if ($salesZoneFilter) $whr = " a.sales_zone_id='$salesZoneFilter' ";

		$orderBy	= "a.name asc, b.name asc";

		$limit		= "$offset,$limit";

		$qry = "select a.id, a.code, a.name, a.billing_state, a.entry_tax, a.sales_zone_id, b.name from m_role a left join m_sales_zone b on a.sales_zone_id=b.id ";

		if ($whr)  	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;
		//echo "<br>$qry";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($salesZoneFilter=null)
	{			
		if ($salesZoneFilter) $whr = " a.sales_zone_id='$salesZoneFilter' ";

		$orderBy	= "a.name asc, b.name asc";

		$qry = "select a.id, a.code, a.name, a.billing_state, a.entry_tax, a.sales_zone_id, b.name from m_role a left join m_sales_zone b on a.sales_zone_id=b.id ";

		if ($whr)  	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($stateId)
	{
		$qry = "select id, code, name, billing_state, entry_tax, sales_zone_id from m_role where id=$stateId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateState($stateId, $stateName, $billingState, $entryTax, $salesZoneId)
	{
		$qry = "update m_role set name='$stateName', billing_state='$billingState', entry_tax='$entryTax', sales_zone_id='$salesZoneId' where id=$stateId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete a Record
	function deleteState($stateId)
	{
		$qry = " delete from m_role where id=$stateId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		return $result;
	}

	# Check State Exist [Ciuty Master, State Wise Vat Master, Distributor Master, Dist Margin, Sales Order]
	function stateEntryExist($stateId)
	{
		//$qry = " select id from m_city where state_id='$stateId'";
		$qry = " select id from (
				select a.id from m_city a where a.state_id='$stateId'
			union
				select a1.id from m_role_vat a1 where a1.state_id='$stateId'
			union
				select a2.id from m_distributor_state a2 where a2.state_id='$stateId'
			union
				select a3.id from m_distributor_margin_state a3 where a3.state_id='$stateId'
			union
				select a4.id from t_salesorder a4 where a4.state_id='$stateId'	
			) as X group by id ";
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	# Fetch All State Records (Using other Sections)
	function fetchAllStateRecords($salesZoneFilter=null)
	{			
		if ($salesZoneFilter) $whr = " a.sales_zone_id='$salesZoneFilter' ";

		$orderBy	= "a.name asc, b.name asc";

		$qry = "select a.id, a.code, a.name, a.billing_state, a.entry_tax, a.sales_zone_id, b.name from m_role a left join m_sales_zone b on a.sales_zone_id=b.id ";

		if ($whr)  	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;		
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	
}
?>