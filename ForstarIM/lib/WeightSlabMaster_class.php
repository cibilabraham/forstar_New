<?php
class WeightSlabMaster
{
	/****************************************************************
	This class deals with all the operations relating to Weight Slab Master
	*****************************************************************/
	var $databaseConnect;
	
	// Constructor, which will create a db instance for this class
	function WeightSlabMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addWeightSlab($code, $name, $wtFrom, $wtTo, $wtAbove, $cUserId)
	{
		$qry = "insert into m_weight_slab (code, name, wt_from, wt_to, above, created, createdby) values('$code', '$name', '$wtFrom', '$wtTo', '$wtAbove', NOW(), '$cUserId')";
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
		$orderBy	= " a.wt_from asc ";
		$limit		= " $offset,$limit";
		$qry = " select a.id, a.code, a.name,a.active from m_weight_slab a ";
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
		$orderBy	= " a.wt_from asc ";
		$qry = " select a.id, a.code, a.name,a.active from m_weight_slab a ";
		if ($whr!="") 		$qry .= " where".$whr;
		if ($orderBy!="") 	$qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Rec based on id 
	function find($weightSlabId)
	{
		$qry = "select id, code, name, wt_from, wt_to, above from m_weight_slab where id=$weightSlabId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  record
	function updateWeightSlab($weightSlabId, $name, $wtFrom, $wtTo, $wtAbove)
	{
		$qry = "update m_weight_slab set name='$name', wt_from='$wtFrom', wt_to='$wtTo', above='$wtAbove' where id='$weightSlabId' ";		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();		
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a record
	function deleteWeightSlab($weightSlabId)
	{
		$qry = "delete from m_weight_slab where id=$weightSlabId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	# -----------------------------------------------------
	# Checking functions using in another screen
	# -----------------------------------------------------
	function wtSlabRecInUse($weightSlabId)
	{		
		$qry = " select id from (
				select a.id as id from m_trptr_wt_slab_entry a where a.wt_slab_id='$weightSlabId'
			union
				select a1.id as id from m_transporter_rate_entry a1 where a1.weight_slab_id='$weightSlabId'				
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Check for duplicate entry
	function chkDuplicateEntry($wtFrom, $wtTo, $weightSlabId)
	{
		/* Removed on 11-07-09
		if ($weightSlabId!="") $updateQry = " id!=$weightSlabId and ";
		else $updateQry = "";
		$qry = " select id from m_weight_slab where $updateQry (('$wtFrom' between wt_from and wt_to) or ('$wtTo' between wt_from and wt_to)) ";
		*/

		if ($weightSlabId!="") $updateQry = " and id!=$weightSlabId ";
		
		$qry = " select id from m_weight_slab where wt_from='$wtFrom' and wt_to='$wtTo' $updateQry";

		//echo "<br>Validate=<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Checking Above Wt
	function chkAboveWt($wtFrom, $weightSlabId)
	{
		$aboveWtChk = false;
		if ($weightSlabId!="") $updateQry = " and id!=$weightSlabId";
		else $updateQry = "";
		$qry = " select id, wt_from from m_weight_slab where above = 'Y' $updateQry";
		//echo "<br>above Qry=<br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		if (sizeof($result)>0) {
			if ($wtFrom>$result[1]) {
				$aboveWtChk = true;
			}
		}
		return $aboveWtChk;		
	}


	function updateWeightSlabconfirm($weightSlabId)
	{
	$qry	= "update m_weight_slab set active='1' where id=$weightSlabId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateWeightSlabReleaseconfirm($weightSlabId)
	{
		$qry	= "update m_weight_slab set active='0' where id=$weightSlabId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>