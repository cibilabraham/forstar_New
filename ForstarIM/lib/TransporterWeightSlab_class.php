<?php
class TransporterWeightSlab
{
	/****************************************************************
	This class deals with all the operations relating to Transporter Weight Slab
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function TransporterWeightSlab(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addTransporterWtSlab($selTransporter, $userId)
	{
		$qry = "insert into m_trptr_wt_slab (transporter_id, created, createdby) values('$selTransporter', Now(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# Insert Entry Recs
	function addTrptrWiseWtSlab($trptrWtSlabLastId, $wtSlabId)
	{
		$qry = "insert into m_trptr_wt_slab_entry (main_id, wt_slab_id) values('$trptrWtSlabLastId', '$wtSlabId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{			
		$whr = " a.transporter_id=b.id";
		
		$orderBy 	= " b.name asc";
		$limit 		= " $offset,$limit";

		$qry = "select a.id, a.transporter_id, b.name,a.active from m_trptr_wt_slab a, m_transporter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;			
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$whr = " a.transporter_id=b.id";
		
		$orderBy 	= " b.name asc";	

		$qry = "select a.id, a.transporter_id, b.name,a.active from m_trptr_wt_slab a, m_transporter b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Selected Wt Slab Recs
	function getSelWtSlabRecs($transporterWeightSlabId)
	{
		$qry = " select a.id, a.wt_slab_id, b.name from m_trptr_wt_slab_entry a, m_weight_slab b where a.wt_slab_id=b.id and a.main_id='$transporterWeightSlabId' order by b.name asc ";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($transporterWeightSlabId)
	{
		$qry = "select id, transporter_id from m_trptr_wt_slab where id='$transporterWeightSlabId'";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update  a  Record 
	function updateTransporterWeightSlab($transporterWeightSlabId)
	{		
		//$qry = " update m_trptr_wt_slab set fov_charge='$fovCharge', docket_charge='$docketCharge', service_tax='$serviceTax', octroi_service_charge='$octroiServiceCharge' where id=$transporterWeightSlabId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	

	# update Entry Rec
	function updateTransporterWtSlabEntryRec($trptrWtSlabEntryId, $weightSlabId)
	{
		$qry = "update m_trptr_wt_slab_entry set wt_slab_id='$weightSlabId' where id='$trptrWtSlabEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delelte Removed rec
	function delRemovedWtSlabRec($trptrWtSlabEntryId)
	{
		$qry =	" delete from m_trptr_wt_slab_entry where id=$trptrWtSlabEntryId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	


	# Delete Entry Rec
	function deleteTransporterWtSlabEntryRec($transporterWeightSlabId)
	{
		$qry =	" delete from m_trptr_wt_slab_entry where main_id=$transporterWeightSlabId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	# Delete a Record
	function deleteTransporterWtSlab($transporterWeightSlabId)
	{
		$qry =	" delete from m_trptr_wt_slab where id=$transporterWeightSlabId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	
	# Checking Entry Exist
	function checkEntryExist($transporterId, $currentId)
	{
		$updateQry = "";
		if ($currentId) $updateQry = " and id!=$currentId";
		
		$qry = " select id from m_trptr_wt_slab where transporter_id='$transporterId' $updateQry ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Chk Slab Exist in Transporter rate master
	function chkWtSlabExistInTrptrRate($trptrWtSlabEntryId)
	{
		$qry = " select id from m_transporter_rate_entry where trptr_wt_slab_entry_id='$trptrWtSlabEntryId' ";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	/**
	* Checking functions using in another screen
	*/
	function trptrWtSlabRecInUse($transporterWeightSlabId)
	{	
		$qry = " select id from  m_trptr_wt_slab_entry where main_id='$transporterWeightSlabId'";			
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		$existCount= 0;
		foreach ($result as $rec) {
			$trptrWtSlabEntryId = $rec[0];
			$recInUse = $this->chkWtSlabExistInTrptrRate($trptrWtSlabEntryId);
			if ($recInUse) $existCount++;
		}
		return ($existCount>0)?true:false;		
	}

	function updateTransporterWtSlabconfirm($trptrWtSlabEntryId)
	{
	$qry	= "update m_trptr_wt_slab set active='1' where id=$trptrWtSlabEntryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateTransporterWtSlabReleaseconfirm($trptrWtSlabEntryId)
	{
		$qry	= "update m_trptr_wt_slab set active='0' where id=$trptrWtSlabEntryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>