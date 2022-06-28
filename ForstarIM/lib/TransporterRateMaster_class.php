<?php
class TransporterRateMaster
{
	/****************************************************************
	This class deals with all the operations relating to Transporter Rate Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function TransporterRateMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record	
	function addTransporterRate($selTransporter, $selZone, $transporterRateListId, $userId)
	{
		$qry = "insert into m_transporter_rate (transporter_id, zone_id, rate_list_id, created, createdby) values('$selTransporter', '$selZone', '$transporterRateListId', Now(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# Insert Entry Recs
	function addTransporterRateEntryRec($transporterRateId, $weightSlabId, $rate, $trptrWtSlabEntryId, $trptrRateType)
	{
		$qry = "insert into m_transporter_rate_entry (main_id, weight_slab_id, rate, trptr_wt_slab_entry_id, rate_type) values('$transporterRateId', '$weightSlabId', '$rate', '$trptrWtSlabEntryId', '$trptrRateType')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $transporterFilterId, $transporterRateListFilterId)
	{		
		$cDate = date("Y-m-d");

		$whr = " a.transporter_id=b.id and a.zone_id=c.id ";
			
		if ($transporterFilterId!="") $whr .= " and a.transporter_id=".$transporterFilterId;
		if ($transporterRateListFilterId!="") $whr .= " and a.rate_list_id=".$transporterRateListFilterId;

		if ($transporterRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_transporter_ratelist f";
		} else {
			$whr .= "";
			$tableUpdate = "";
		}

		//$groupBy        = "  ";
		$orderBy 	= " b.name asc, c.name asc";
		$limit 		= " $offset,$limit";


		$qry = "select a.id, a.transporter_id, a.zone_id, a.rate_list_id, b.name, c.name,a.active from m_transporter_rate a, m_transporter b, m_zone c $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;			
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords($transporterFilterId, $transporterRateListFilterId)
	{
		$cDate = date("Y-m-d");

		$whr = " a.transporter_id=b.id and a.zone_id=c.id ";
			
		if ($transporterFilterId!="") $whr .= " and a.transporter_id=".$transporterFilterId;
		if ($transporterRateListFilterId!="") $whr .= " and a.rate_list_id=".$transporterRateListFilterId;

		if ($transporterRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_transporter_ratelist f";
		} else {
			$whr .= "";
			$tableUpdate = "";
		}

		//$groupBy        = "  ";
		$orderBy 	= " b.name asc, c.name asc";
		
		$qry = "select a.id, a.transporter_id, a.zone_id, a.rate_list_id, b.name, c.name from m_transporter_rate a, m_transporter b, m_zone c $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Selected Wt Slab Recs
	function getSelWtSlabRecs($transporterRateId)
	{
		$qry = " select a.id, a.code, a.name, b.id, b.rate from m_weight_slab a left join m_transporter_rate_entry b on a.id=b.weight_slab_id and b.main_id='$transporterRateId' order by a.wt_from asc ";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	# Get a Record based on id
	function find($transporterRateId)
	{
		$qry = "select id, transporter_id, zone_id, rate_list_id from m_transporter_rate where id='$transporterRateId'";
		return $this->databaseConnect->getRecord($qry);
	}


	# Update  a  Record 
	function updateTransporterRate($transporterRateId)
	{		
		$qry = " update m_transporter_rate set where id=$transporterRateId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# update Entry Rec
	function updateTransporterRateEntryRec($transporterRateEntryId, $weightSlabId, $rate, $trptrWtSlabEntryId, $trptrRateType)
	{
		$qry = "update m_transporter_rate_entry set weight_slab_id='$weightSlabId', rate='$rate', trptr_wt_slab_entry_id='$trptrWtSlabEntryId', rate_type='$trptrRateType' where id='$transporterRateEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete Entry Rec
	function deleteTransporterRateEntryRec($transporterRateId)
	{
		$qry =	" delete from m_transporter_rate_entry where main_id=$transporterRateId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	# Delete a Record
	function deleteTransporterRate($transporterRateId)
	{
		$qry =	" delete from m_transporter_rate where id=$transporterRateId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Checking Entry Exist
	function checkEntryExist($transporterId, $zoneId, $transporterRateList, $currentId)
	{
		if ($currentId) $updateQry = " and id!=$currentId";
		else $updateQry = "";

		$qry = " select id from m_transporter_rate where transporter_id='$transporterId' and zone_id='$zoneId' and rate_list_id='$transporterRateList' $updateQry ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Area Demarcation  STARTS HERE
        # AD - Area Demarcation, ZW - zone wise
        function getZWAreaDemarcationRecs($zoneId)
	{
		$resultArr = array();
		$getStateRecs =	$this->getADStateRecords($zoneId);
		$i= 0;
		foreach ($getStateRecs as $sr) {
			$stateName = $sr[1];
			$areaDemarcationStateId = $sr[2];
			$getCityRecords = $this->getADCityRecs($areaDemarcationStateId);
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

	# Sel State Recs
	function getADStateRecords($zoneId)
	{
		//$qry = " select a.state_id, b.name, a.id from m_area_demarcation_state a, m_state b, m_area_demarcation c where c.id=a.main_id and a.state_id=b.id and c.zone_id='$zoneId' order by b.name asc";
		$qry = " select a.state_id, b.name, a.id from m_area_demarcation_state a, m_state b where a.state_id=b.id and a.main_id='$zoneId' order by b.name asc";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getADCityRecs($areaDemarcationStateId)
	{
		$qry = " select a.city_id, b.name from m_area_demarcation_city a, m_city b where a.city_id=b.id and a.demarcation_state_id='$areaDemarcationStateId' order by b.name asc";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Area Demarcation Ends here -------------------------------

	# Display Area
	function displayArea($zoneId)
	{
		$zoneWiseAreaRecs = $this->getZWAreaDemarcationRecs($zoneId);

		$areaRec	= "<fieldset><legend class=fieldName style=line-height:normal;>Area Demarcation</legend><table><tr>";
				$numLine = 4;
				if (sizeof($zoneWiseAreaRecs)>0) {
					$nextRec	=	0;
					$k=0;
					$selName = "";
					foreach ($zoneWiseAreaRecs as $zr) {					
						$j++;
						$selName = $zr[0];
						$nextRec++;
		$areaRec	.= "<td class=listing-item>";
					 if ($nextRec>1) {
		$areaRec	.=  ",";	
					}
		$areaRec	.= "$selName</td>";
				   	 if($nextRec%$numLine == 0) { 
		$areaRec	.= "</tr><tr>";
					  }	
					}
				} else if ($zoneId) {
		$areaRec	.= "<tr><td class=err1>Please define area demarcation for the selected zone.</td><tr>";
				}
		$areaRec	.= "</tr></table></fieldset>";

		return $areaRec;
	}


	/*
	# Get Transporter wise Wt Slab Recs
	function getTransporterWiseWtSlab($transporterId)
	{
		$qry = " select a.id, b.id, b.wt_slab_id, c.name from m_trptr_wt_slab a, m_trptr_wt_slab_entry b, m_weight_slab c where a.id=b.main_id and b.wt_slab_id=c.id and transporter_id='$transporterId' order by c.name asc ";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/	

	# Wt Slab List recs
	function getWtSlabListRecs($transporterId)
	{
		$qry = " select a.id, b.id, b.wt_slab_id, c.name from m_trptr_wt_slab a, m_trptr_wt_slab_entry b, m_weight_slab c where a.id=b.main_id and b.wt_slab_id=c.id and a.transporter_id='$transporterId' order by c.name asc ";
		//echo "<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	# Get Wt slab Rate
	function getWtSlabRate($transporterRateId, $weightSlabId)
	{		
		$qry = " select b.id, b.rate, b.rate_type from m_transporter_rate_entry b where b.main_id='$transporterRateId' and b.weight_slab_id='$weightSlabId'";	
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):array();
	}

	# Get Selected Wt Slab Recs
	function selWtSlabRecs($transporterRateId, $transporterId)
	{
		//$qry = " select a.name, b.rate from m_weight_slab a, m_transporter_rate_entry b where a.id=b.weight_slab_id and b.main_id='$transporterRateId' order by a.wt_from asc ";
		$qry = " select c.name, d.rate from m_trptr_wt_slab a, m_trptr_wt_slab_entry b, m_weight_slab c, m_transporter_rate_entry d where c.id=d.weight_slab_id and a.id=b.main_id and b.wt_slab_id=c.id and transporter_id='$transporterId' and d.main_id='$transporterRateId' order by c.name asc ";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Display Transporter Rate
	function displayTransporterRate($transporterRateId, $transporterId)
	{
		$getWtSlabRecs = $this->selWtSlabRecs($transporterRateId, $transporterId);
		$wtSlabList ="";
		if (sizeof($getWtSlabRecs)>0) {
			//#999999 bgcolor=#999999  bgcolor=white
			$wtSlabList	= "<table cellspacing=1 cellpadding=1 id=newspaper-b1><!--tr bgcolor=#f2f2f2 align=center><td class=listing-head>Weight Slab</td><td class=listing-head nowrap>Rate</td></tr-->";
				$m= 0;
				foreach ($getWtSlabRecs as $wsr) {
					$m++;							
					$name 		= stripSlash($wsr[0]);	
					$slabRate	= $wsr[1];	
					//#fffbcc			
			$wtSlabList .= "<tr id=ROW_R><td class=listing-item nowrap>$name</td><td class=listing-item nowrap>$slabRate</td></tr>";
					}
			$wtSlabList	.=	"</table>";
		}
		return $wtSlabList;
	}


	# -----------------------------------------------------
	# Checking functions using in another screen
	# -----------------------------------------------------
	function trptrRateRecInUse($transporterRateId)
	{			
		$rec = $this->find($transporterRateId);
		$rateListId = $rec[3];
		if ($rateListId) {
			$qry = "select a.id as id from t_salesorder a where a.transporter_rate_list_id='$rateListId'";
			//echo $qry."<br>";
			$result	= $this->databaseConnect->getRecords($qry);
		}
		return (sizeof($result)>0)?true:false;		
	} 


	function updateTransporterRateconfirm($transporterRateId)
	{
	$qry	= "update m_transporter_rate set active='1' where id=$transporterRateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateTransporterRateReleaseconfirm($transporterRateId)
	{
		$qry	= "update m_transporter_rate set active='0' where id=$transporterRateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}


}
?>