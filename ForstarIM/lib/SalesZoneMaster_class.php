<?php
class SalesZoneMaster
{
	/****************************************************************
	This class deals with all the operations relating to Sales Zone Master
	*****************************************************************/
	var $databaseConnect;
	
	// Constructor, which will create a db instance for this class
	function SalesZoneMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addZone($code, $name, $cUserId)
	{
		$qry = "insert into m_sales_zone (code, name, created, createdby) values('$code', '$name', NOW(), '$cUserId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$whr = " a.id is not null ";
		$orderBy	= " a.name asc ";
		$limit		= " $offset,$limit";
		$qry = " select a.id, a.code, a.name,a.active,(select count(a1.id) from m_state a1 where a1.sales_zone_id=a.id) as tot from m_sales_zone a ";
		if ($whr!="")		$qry .= " where".$whr;
		if ($orderBy!="")	$qry .= " order by".$orderBy;
		if ($limit!="")		$qry .= " limit".$limit;		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		//$whr 		= " a.id is not null ";	
		$orderBy	= " a.name asc ";
		$qry = " select a.id, a.code, a.name,a.active,(select count(a1.id) from m_state a1 where a1.sales_zone_id=a.id) as tot from m_sales_zone a ";
		if ($whr!="") 		$qry .= " where".$whr;
		if ($orderBy!="") 	$qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveZone()
	{
		//$whr 		= " a.id is not null ";	
		$orderBy	= " a.name asc ";
		$qry = " select a.id, a.code, a.name,a.active from m_sales_zone a where a.active=1";
		if ($whr!="") 		$qry .= " where".$whr;
		if ($orderBy!="") 	$qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Rec based on id 
	function find($zoneId)
	{
		$qry = "select id, code, name from m_sales_zone where id=$zoneId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  record
	function updateZone($zoneId, $name)
	{
		$qry = "update m_sales_zone set name='$name' where id='$zoneId' ";		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();		
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a record
	function deleteZone($zoneId)
	{
		$qry = "delete from m_sales_zone where id=$zoneId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	# -----------------------------------------------------
	# Checking functions using in another screen
	# -----------------------------------------------------
	function zoneRecInUse($zoneId)
	{	
		/*
		$qry = " select id from (
				select a.id as id from m_area_demarcation a where a.zone_id='$zoneId'
			union
				select a1.id as id from m_transporter_rate a1 where a1.zone_id='$zoneId'	
			) as X group by id ";
		*/
		$qry = "select a1.id as id from m_state a1 where a1.sales_zone_id='$zoneId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Check for duplicate entry
	function chkDuplicateEntry($name, $zoneId)
	{
		if ($zoneId!="") $updateQry = " and id!=$zoneId";
		else $updateQry = "";
		$qry = " select id from m_sales_zone where name = '$name' $updateQry";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}



function updateZoneconfirm($zoneId){
		$qry	= "update m_sales_zone set active='1' where id=$zoneId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updateZoneReleaseconfirm($zoneId){
		$qry	= "update m_sales_zone set active='0' where id=$zoneId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}



		
}
?>