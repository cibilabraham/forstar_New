<?php
class AreaMaster
{
	/****************************************************************
	This class deals with all the operations relating to Area Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function AreaMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addArea($areaCode, $areaName, $areaId)
	{
		$qry = "insert into m_area (code, name, city_id) values('$areaCode', '$areaName', '$areaId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selCityFilter)
	{
		$whr = "  a.city_id=b.id";
		
		if ($selCityFilter!="") $whr .= " and a.city_id='$selCityFilter'";
		else $whr .= "";
		
		$orderBy  = "  a.name asc";
		$limit 	  = " $offset,$limit";

		$qry = "select a.id, a.code, a.name, a.city_id, b.name,a.active from m_area a, m_city b ";
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($selCityFilter)
	{
		$whr = "  a.city_id=b.id";
		
		if ($selCityFilter!="") $whr .= " and a.city_id='$selCityFilter'";
		else $whr .= "";

		$orderBy  = "  a.name asc";
		
		$qry = "select a.id, a.code, a.name, a.city_id, b.name,a.active from m_area a, m_city b ";
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;				
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($areaId)
	{
		$qry = "select id, code, name, city_id from m_area where id=$areaId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Record
	function deleteArea($areaId)
	{
		$qry	=	" delete from m_area where id=$areaId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Record
	function updateArea($areaId, $areaName, $selCity)
	{
		$qry = "update m_area set name='$areaName', city_id='$selCity' where id=$areaId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	#Filter Area Based on State (Using Sales Staff Master)
	function filterAreaRecs($selCityId)
	{
		$qry = "select id, code, name from m_area where city_id='$selCityId' order by name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function updateAreaconfirm($areaId)
	{
	$qry	= "update m_area set active='1' where id=$areaId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateAreaReleaseconfirm($areaId)
	{
		$qry	= "update m_area set active='0' where id=$areaId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>