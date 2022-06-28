<?php
class ZoneMaster
{
	/****************************************************************
	This class deals with all the operations relating to Zone Master
	*****************************************************************/
	var $databaseConnect;
	
	// Constructor, which will create a db instance for this class
	function ZoneMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addZone($code, $name, $cUserId)
	{
		$qry = "insert into m_zone (code, name, created, createdby) values('$code', '$name', NOW(), '$cUserId')";
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
		$qry = " select a.id, a.code, a.name,a.active from m_zone a ";
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
		$qry = " select a.id, a.code, a.name,a.active from m_zone a ";
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
		$qry = " select a.id, a.code, a.name,a.active from m_zone a where a.active=1 ";
		//if ($whr!="") 		$qry .= " where".$whr;
		if ($orderBy!="") 	$qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	} 
	# Get Rec based on id 
	function find($zoneId)
	{
		$qry = "select id, code, name from m_zone where id=$zoneId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  record
	function updateZone($zoneId, $name)
	{
		$qry = "update m_zone set name='$name' where id='$zoneId' ";		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();		
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a record
	function deleteZone($zoneId)
	{
		$qry = "delete from m_zone where id=$zoneId";

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
		$qry = " select a1.id as id from m_transporter_rate a1 where a1.zone_id='$zoneId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Check for duplicate entry
	function chkDuplicateEntry($name, $zoneId)
	{
		if ($zoneId!="") $updateQry = " and id!=$zoneId";
		else $updateQry = "";
		$qry = " select id from m_zone where name = '$name' $updateQry";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Area Demarcation Starts Here
	# ---------------------------------------------

	# Filter City Based on State ()
	function filterCityRecs($stateId)
	{
		$qry = "select id, code, name from m_city where state_id='$stateId' order by name asc";
		//echo $qry;
		$result = array();
		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$resultArr = array('0'=>'-- Select All --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[2];
			}
		} else {
			$resultArr = array(''=>'No City');
		}
		return $resultArr;				
	}

	# Edit Mode select City list
	function getSelectedCityList($stateId, $stateEntryId)
	{
		$qry = "select a.id, a.code, a.name, b.city_id from m_city a left join m_area_demarcation_city b on a.id=b.city_id and b.demarcation_state_id='$stateEntryId'  where  a.state_id='$stateId' order by a.name asc";
		//or b.city_id=0
		//echo "<br>".$qry."<br>";		
		$resultArr = array();
		$result	= $this->databaseConnect->getRecords($qry);

		# City Records
		$getCityRecords = $this->getAreaDemarcationCityRecs($stateEntryId);		
		$selectAll = "";
		if (sizeof($getCityRecords)==0)  $selectAll = 0;

		$i=0;
		if (sizeof($result)>0) {			
			$resultArr[$i] = array('0','-- Select All --',$selectAll);
			while (list(,$v) = each($result)) {		
				$i++;
				$resultArr[$i] = array($v[0],$v[2],$v[3]);
			}
		} else {
			$resultArr[$i] = array('','-- Select --','');
		}
		return $resultArr;
	}


	# Selected State Records	
	function getSelStateRecords($zoneId)
	{
		$qry = " select a.state_id, b.name, a.id from m_area_demarcation_state a, m_state b where a.state_id=b.id and a.main_id='$zoneId' order by b.name asc";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Selected City Records
	function getAreaDemarcationCityRecs($areaDemarcationStateId)
	{
		$qry = " select a.city_id, b.name from m_area_demarcation_city a, m_city b where a.city_id=b.id and a.demarcation_state_id='$areaDemarcationStateId' order by b.name asc";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAreaDemarcationRecs($zoneId)
	{
		$resultArr = array();
		$getStateRecs =	$this->getSelStateRecords($zoneId);
		$i= 0;
		foreach ($getStateRecs as $sr) {
			$stateName = $sr[1];
			$areaDemarcationStateId = $sr[2];
			$getCityRecords = $this->getAreaDemarcationCityRecs($areaDemarcationStateId);
			if (sizeof($getCityRecords)>0) {
				foreach ($getCityRecords as $cr) {
					$cityName = $cr[1];
					$resultArr[$i] = array($cityName);
					$i++;
				}
			} else  {
				$resultArr[$i] = array($stateName);
				$i++;
			}	
				
		}
		return $resultArr;
	}

	# Aea Demarcation State Records
	function getAreaDemarcationStateRecords($zoneId)
	{
		$qry = " select id, main_id, state_id from m_area_demarcation_state where main_id='$zoneId'";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Area Demarcation State Table Entry
	function addAreaDemarcationState($lastId, $selStateId, $selCity)
	{
		$qry = "insert into m_area_demarcation_state (main_id, state_id) values('$lastId', '$selStateId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	{
			$this->databaseConnect->commit();
			# Get Last inserrted Id
			$stateEntryId = $this->databaseConnect->getLastInsertedId();
			# Add City
			$this->addAreaDemarcationCity($stateEntryId, $selCity);
		}
		else	$this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Area Demarcation City Table Entry	
	function addAreaDemarcationCity($stateEntryId, $selCity)
	{
		if ($selCity) {
			foreach ($selCity as $cId) {
				$cityId	= $cId;	
				$qry	= "insert into m_area_demarcation_city (demarcation_state_id, city_id) values('".$stateEntryId."','".$cityId."')";
				//echo $qry;
				$insertStatus	= $this->databaseConnect->insertRecord($qry);
				if ($insertStatus) $this->databaseConnect->commit();
				else 		   $this->databaseConnect->rollback();
			}
		}
	}

	# Update State Rec
	function updateDemarcationState($stateEntryId, $selStateId, $selCity)
	{
		$qry = "update m_area_demarcation_state set state_id='$selStateId' where id='$stateEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);	
		if ($result) {
			$this->databaseConnect->commit();
			# Del City Rec
			$delDemarcationCityEntyRec  = $this->delDemarcationCityRec($stateEntryId);
			# Add City
			$this->addAreaDemarcationCity($stateEntryId, $selCity);
		}
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}

	# Delete City Rec
	function delDemarcationCityRec($stateEntryId)
	{
		$qry = "delete from m_area_demarcation_city where demarcation_state_id=$stateEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete state entry
	function delRemovedDistRec($stateEntryId)
	{
		# Del City Rec
		$delDemarcationCityEntyRec  = $this->delDemarcationCityRec($stateEntryId);

		$qry = "delete from m_area_demarcation_state where id=$stateEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete all Entries reg. distibutor
	function delAreaDemarcationEntryRecs($zoneId)
	{
		# Get State Entry Records
		$getSelStateRecords  = $this->getAreaDemarcationStateRecords($zoneId);
		if (sizeof($getSelStateRecords)>0) {
			foreach ($getSelStateRecords as $dsr) {
				$stateEntryId	= $dsr[0];
				# Del State & City Rec
				$delEntyRec  = $this->delRemovedDistRec($stateEntryId);
			}
		}
		return true;
	}

	function updateZoneconfirm($zoneId)
	{
	$qry	= "update m_zone set active='1' where id=$zoneId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateZoneReleaseconfirm($zoneId)
	{
		$qry	= "update m_zone set active='0' where id=$zoneId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>