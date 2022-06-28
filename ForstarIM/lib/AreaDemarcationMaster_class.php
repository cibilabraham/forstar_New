<?php
class AreaDemarcationMaster_ssssssssssss
{
	/****************************************************************
	This class deals with all the operations relating to Area Demarcation Master
	*****************************************************************/
	var $databaseConnect;
	
	// Constructor, which will create a db instance for this class
	function AreaDemarcationMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addAreaDemarcation($selZone, $cUserId)
	{
		$qry = "insert into m_area_demarcation (zone_id, created, createdby) values('$selZone', NOW(), '$cUserId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	# State Table Entry
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

	# City Table Entry	
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

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$whr = " a.zone_id=b.id ";

		$orderBy	= " b.name asc ";
		$limit		= " $offset,$limit";

		$qry = " select a.id, a.zone_id, b.name from m_area_demarcation a, m_zone b ";

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
		$whr = " a.zone_id=b.id ";

		$orderBy	= " b.name asc ";
		
		$qry = " select a.id, a.zone_id, b.name from m_area_demarcation a, m_zone b ";

		if ($whr!="")		$qry .= " where".$whr;
		if ($orderBy!="")	$qry .= " order by".$orderBy;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Rec based on id 
	function find($areaDemarcationId)
	{
		$qry = "select id, zone_id from m_area_demarcation where id=$areaDemarcationId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Aea Demarcation State Records
	function getAreaDemarcationStateRecords($areaDemarcationId)
	{
		$qry = " select id, main_id, state_id from m_area_demarcation_state where main_id='$areaDemarcationId'";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  a  record
	function updateAreaDemarcation($areaDemarcationId, $selZone)
	{
		$qry = "update m_area_demarcation set zone_id='$selZone' where id='$areaDemarcationId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();		
		else		$this->databaseConnect->rollback();		
		return $result;	
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

	# Delete a record
	function deleteAreaDemarcation($areaDemarcationId)
	{
		$qry = "delete from m_area_demarcation where id=$areaDemarcationId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	# -----------------------------------------------------
	# Checking functions using in another screen
	# -----------------------------------------------------
	function areaDemarcationRecInUse($areaDemarcationId)
	{		
		/*$qry = " select id from (
				select a.id as id from m_rtcounter_margin a where a.retail_counter_id='$areaDemarcationId'
			union
				select a1.id as id from m_rtct_assign_dis_charge a1 where a1.retail_counter_id='$areaDemarcationId'	
			union
				select a2.id as id from t_dailysales_rtcounter a2 where a2.rt_counter_id='$areaDemarcationId'		
			) as X group by id ";
		*/
		$rec	  = $this->find($areaDemarcationId);
		$zoneId	  = $rec[1];
		if ($zoneId) {
			$qry = " select id from m_transporter_rate where zone_id='$zoneId'";
			//echo $qry."<br>";
			$result	= $this->databaseConnect->getRecords($qry);
		}		
		return (sizeof($result)>0)?true:false;		
	}

	# Check for duplicate entry
	function chkDuplicateEntry($zoneId, $areaDemarcationId)
	{
		if ($areaDemarcationId!="") $updateQry = " and id!=$areaDemarcationId";
		else $updateQry = "";
		$qry = " select id from m_area_demarcation where zone_id = '$zoneId' $updateQry";
				
		//echo "<br>Validate=<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}


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
		//$result = array();
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

	# ---------------------------------------------
	# Selected State Records
	# ---------------------------------------------
	function getSelStateRecords($areaDemarcationId)
	{
		$qry = " select a.state_id, b.name, a.id from m_area_demarcation_state a, m_state b where a.state_id=b.id and a.main_id='$areaDemarcationId' order by b.name asc";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# ---------------------------------------------
	# Selected City Records
	# ---------------------------------------------
	/*
	function getSelCityRecords($areaDemarcationId)
	{
		$qry = " select b.city_id, c.name from m_area_demarcation_state a, m_area_demarcation_city b, m_city c where a.id=b.demarcation_state_id and b.city_id=c.id and a.main_id='$areaDemarcationId' order by c.name asc";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

	function getAreaDemarcationCityRecs($areaDemarcationStateId)
	{
		$qry = " select a.city_id, b.name from m_area_demarcation_city a, m_city b where a.city_id=b.id and a.demarcation_state_id='$areaDemarcationStateId' order by b.name asc";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAreaDemarcationRecs($areaDemarcationId)
	{
		$resultArr = array();
		$getStateRecs =	$this->getSelStateRecords($areaDemarcationId);
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

	# Delete all Entries reg. distibutor
	function delAreaDemarcationEntryRecs($areaDemarcationId)
	{
		# Get State Entry Records
		$getSelStateRecords  = $this->getAreaDemarcationStateRecords($areaDemarcationId);
		if (sizeof($getSelStateRecords)>0) {
			foreach ($getSelStateRecords as $dsr) {
				$stateEntryId	= $dsr[0];
				# Del City Rec
				//$delDistCityEntyRec  = $this->delDemarcationCityRec($stateEntryId);
				$delEntyRec  = $this->delRemovedDistRec($stateEntryId);
			}
		}
		return true;
	}

	
	
	
}