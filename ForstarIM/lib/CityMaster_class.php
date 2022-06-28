<?php
class CityMaster
{
	/****************************************************************
	This class deals with all the operations relating to City Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function CityMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addCity($cityCode, $cityName, $state, $octroi, $octroiPercent)
	{
		$qry = "insert into m_city (code, name, state_id, octroi, octroi_percent) values('$cityCode', '$cityName', '$state', '$octroi', '$octroiPercent')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selStateFilter)
	{
		$whr = "  a.state_id=b.id";
		
		if ($selStateFilter!="") $whr .= " and a.state_id='$selStateFilter'";
		else $whr .= "";
		
		$orderBy  = "  a.name asc";
		$limit 	  = " $offset,$limit";

		$qry = "select a.id, a.code, a.name, a.state_id, b.name, a.octroi, a.octroi_percent,a.active from m_city a, m_state b ";
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($selStateFilter)
	{
		$whr = "  a.state_id=b.id";
		
		if ($selStateFilter!="") $whr .= " and a.state_id='$selStateFilter'";
		else $whr .= "";
		$orderBy  = "  a.name asc";
		
		$qry = "select a.id, a.code, a.name, a.state_id, b.name, a.octroi, a.octroi_percent,a.active from m_city a, m_state b ";
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	function fetchAllRecordsCityActive($selStateFilter)
	{
		$whr = "  a.state_id=b.id and a.active=1";
		
		if ($selStateFilter!="") $whr .= " and a.state_id='$selStateFilter' and a.active=1";
		else $whr .= "";
		$orderBy  = "  a.name asc";
		
		$qry = "select a.id, a.code, a.name, a.state_id, b.name, a.octroi, a.octroi_percent,a.active from m_city a, m_state b ";
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id (using in Dist Mgn Struct Ajax)
	function find($cityId)
	{
		$qry = "select id, code, name, state_id, octroi, octroi_percent from m_city where id=$cityId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Record
	function deleteCity($cityId)
	{
		$qry	=	" delete from m_city where id=$cityId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}

	# Update  a  Record
	function updateCity($cityId, $cityName, $state, $octroi, $octroiPercent)
	{
		$qry = "update m_city set name='$cityName', state_id='$state', octroi='$octroi', octroi_percent='$octroiPercent' where id=$cityId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Filter City Based on State (Using in Distributor Screen)
	function filterCityRecs($stateId)
	{
		$qry = "select id, code, name from m_city where state_id='$stateId' order by name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Check City Exist (In Area Master)
	function cityEntryExist($cityId)
	{		
		$qry = " select id from (
				select id from m_area where city_id='$cityId'
			union
				select a1.id from m_distributor_city a1 where a1.city_id='$cityId'
			union
				select a2.id from m_distributor_margin_state a2 where a2.city_id='$cityId'
			union
				select a3.id from t_salesorder a3 where a3.city_id='$cityId'	
			) as X group by id ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	# Get Octroi Percent
	function getOctroiPercent($cityId)
	{
		$qry = "select octroi_percent from m_city where id=$cityId";
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	function updatecityconfirm($cityId)
	{
	$qry	= "update m_city set active='1' where id=$cityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatecityReleaseconfirm($cityId)
	{
		$qry	= "update m_city set active='0' where id=$cityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>