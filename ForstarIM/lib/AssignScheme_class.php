<?php
class AssignSchemeMaster
{
	/****************************************************************
	This class deals with all the operations relating to Assign Scheme Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function AssignSchemeMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Record
	function addSchemeAssign($selScheme, $schemeCategory, $selectFrom, $selectTill, $selState, $selCity, $selArea, $distributor, $selRetailCounter, $cUserId)
	{
		$qry = "insert into m_scheme_assign (scheme_id, scheme_category, scheme_from, scheme_to, state_id, city_id, area_id, distributor_id, retailer_id, created, createdby) values('$selScheme', '$schemeCategory', '$selectFrom', '$selectTill', '$selState', '$selCity', '$selArea', '$distributor', '$selRetailCounter', Now(), '$cUserId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Checking for entry exist
	function chkEntryExist($selScheme, $selectFrom, $selectTill, $assignSchemeId)
	{
		if ($assignSchemeId!="") $uptdQry = " and id!=$assignSchemeId";
		else $uptdQry	= "";
		$qry = "select id from m_scheme_assign where scheme_id='$selScheme' and ('$selectFrom'>=date_format(scheme_from,'%Y-%m-%d') and '$selectFrom'<=date_format(scheme_to,'%Y-%m-%d') or '$selectTill'>=date_format(scheme_from,'%Y-%m-%d') and '$selectTill'<=date_format(scheme_to,'%Y-%m-%d')) $uptdQry";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]!="")?true:false;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select a.id, a.scheme_id, a.scheme_category, a.scheme_from, a.scheme_to, a.state_id, a.city_id, a.area_id, a.distributor_id, a.retailer_id, b.name,a.active from m_scheme_assign a, m_scheme b where a.scheme_id=b.id order by b.name asc limit $offset,$limit";
		//echo $qry;		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.scheme_id, a.scheme_category, a.scheme_from, a.scheme_to, a.state_id, a.city_id, a.area_id, a.distributor_id, a.retailer_id, b.name,
		a.active from m_scheme_assign a, m_scheme b where a.scheme_id=b.id order by b.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	
	# Get Assign Scheme based on id 
	function find($assignSchemeId)
	{
		$qry = "select id, scheme_id, scheme_category, scheme_from, scheme_to, state_id, city_id, area_id, distributor_id, retailer_id from m_scheme_assign where id=$assignSchemeId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Assign Scheme
	function updateAssignScheme($assignSchemeId, $selScheme, $schemeCategory, $selectFrom, $selectTill, $selState, $selCity, $selArea, $distributor, $selRetailCounter)
	{
		$qry = "update m_scheme_assign set scheme_id='$selScheme', scheme_category='$schemeCategory', scheme_from='$selectFrom', scheme_to='$selectTill', state_id='$selState', city_id='$selCity', area_id='$selArea', distributor_id='$distributor', retailer_id='$selRetailCounter' where id='$assignSchemeId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Assign Scheme
	function deleteAssignScheme($assignSchemeId)
	{
		$qry = "delete from m_scheme_assign where id=$assignSchemeId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateassignSchemeconfirm($assignSchemeId)
	{
		$qry	= " update m_scheme_assign set active='1' where id=$assignSchemeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateassignSchemeReleaseconfirm($assignSchemeId)
	{
		$qry	= " update m_scheme_assign set active='0' where id=$assignSchemeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}