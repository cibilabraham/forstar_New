<?php
class CountryMaster
{
	/****************************************************************
	This class deals with all the operations relating to Country Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function CountryMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addCountry($country, $userId)
	{
		$qry = "insert into m_country (name, created, createdby) values('$country', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		
		return $insertStatus;
	}

	# Insert (Entry) Records
	function addPortEntries($countryId, $portName, $portCategory)
	{
		$qry = "insert into m_country_port (country_id, port_name, port_category) values('$countryId', '$portName', '$portCategory')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{		
		$whr = "";

		$orderBy = " a.name asc ";
		$limit	 = " $offset,$limit ";

		$qry = " select a.id, a.name,a.active,(select count(id) from m_customer where country_id=a.id) as tot from m_country a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$whr = "";

		$orderBy = " a.name asc ";
		
		$qry = " select a.id, a.name,a.active from m_country a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivecountry()
	{
		$whr = "active=1";

		$orderBy = " a.name asc ";
		
		$qry = " select a.id, a.name,a.active from m_country a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getPortRecs($countryId)
	{
		$qry = " select a.id, a.port_name, a.port_category from m_country_port a where a.country_id='$countryId' order by a.port_name asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($countryId)
	{
		$qry = "select id, name from m_country where id=$countryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateCountry($countryId, $countryName)
	{
		$qry = "update m_country set name ='$countryName' where id=$countryId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	

	# Update Entry
	function updatePortEntries($countryPortEntryId, $portName, $portCategory)
	{
		$qry = " update m_country_port set port_name='$portName', port_category='$portCategory' where id='$countryPortEntryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Entry Rec
	function delPortEntryRec($countryPortEntryId)
	{
		$qry = " delete from m_country_port where id=$countryPortEntryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	# Delete Entry Table Rec
	function deletePortEntryRec($countryId)
	{
		$qry 	= " delete from m_country_port where country_id=$countryId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Selected State Rec
	function deleteCountryRec($countryId)
	{
		$qry 	= " delete from m_country where id=$countryId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Check Country Exist
	function chkCountryExist($countryName, $countryId)
	{
		$qry = "select id from m_country where name='$countryName'";
		if ($countryId) $qry .= " and id!='$countryId'";		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Check country port exist
	function chkCountryPortExist($portName, $portEntryId)
	{
		$qry = "select id from m_country_port where port_name='$portName'";
		if ($portEntryId) $qry .= " and id!='$portEntryId'";	
	
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	/**
	* checking country Id is using anywhee
	*/
	function countryRecInUse($countryId)
	{	
		/*	
		$qry = " select id from (
				select a.id as id from m_processcode a where a.fish_id='$fishId'
			union
				select a1.id as id from m_process a1 where a1.fish_id='$fishId'
			) as X group by id ";
		*/
		$qry = "select id from m_customer where country_id='$countryId'";
		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	function updateCountryconfirm($countryId)
	{
	$qry	= "update m_country set active='1' where id=$countryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateCountryReleaseconfirm($countryId)
	{
		$qry	= "update m_country set active='0' where id=$countryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>