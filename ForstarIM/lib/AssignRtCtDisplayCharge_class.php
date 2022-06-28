<?php
class AssignRtCtDisplayChargeMaster
{
	/****************************************************************
	This class deals with all the operations relating to Assign RT Ct Dis Charge Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function AssignRtCtDisplayChargeMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Record
	function addRtCtDisChargeAssign($selRetailCounter, $disCharge, $disType, $selectFrom, $selectTill, $cUserId)
	{
		$qry = "insert into m_rtct_assign_dis_charge (retail_counter_id, charge, charge_type, from_date, till_date, created, createdby) values('$selRetailCounter', '$disCharge', '$disType', '$selectFrom', '$selectTill', Now(), '$cUserId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Checking for entry exist
	function chkEntryExist($selRetailCounter, $disType, $selectFrom, $selectTill, $assignRtCtDisChargeId)
	{
		if ($disType=='D') $uptdQry = "and ('$selectFrom'>=date_format(from_date,'%Y-%m-%d') and '$selectFrom'<=date_format(till_date,'%Y-%m-%d') or '$selectTill'>=date_format(from_date,'%Y-%m-%d') and '$selectTill'<=date_format(till_date,'%Y-%m-%d'))";
		else $uptdQry = "";

		if ($assignRtCtDisChargeId!="") $uptdQry .= " and id!=$assignRtCtDisChargeId";
		else $uptdQry .= "";

		$qry = "select id from m_rtct_assign_dis_charge where retail_counter_id='$selRetailCounter' and  charge_type ='$disType' $uptdQry ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]!="")?true:false;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select a.id, a.retail_counter_id, a.charge, a.charge_type, a.from_date, a.till_date, b.name,a.active from m_rtct_assign_dis_charge a, m_retail_counter b where a.retail_counter_id=b.id order by b.name asc limit $offset,$limit";
		//echo $qry;		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.retail_counter_id, a.charge, a.charge_type, a.from_date, a.till_date, b.name,a.active from m_rtct_assign_dis_charge a, m_retail_counter b where a.retail_counter_id=b.id order by b.name asc ";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	
	# Get Assign Scheme based on id 
	function find($assignRtCtDisChargeId)
	{
		$qry = "select id, retail_counter_id, charge, charge_type, from_date, till_date from m_rtct_assign_dis_charge where id=$assignRtCtDisChargeId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Assign Scheme
	function updateAssignRtCtDisCharge($assignRtCtDisChargeId, $selRetailCounter, $disCharge, $disType, $selectFrom, $selectTill)
	{
		$qry = "update m_rtct_assign_dis_charge set retail_counter_id='$selRetailCounter', charge='$disCharge', charge_type='$disType', from_date='$selectFrom', till_date='$selectTill' where id='$assignRtCtDisChargeId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Assign Scheme
	function deleteAssignRtCtDisplayCharge($assignRtCtDisChargeId)
	{
		$qry = "delete from m_rtct_assign_dis_charge where id=$assignRtCtDisChargeId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateAssignRtCtconfirm($assignRtCtDisChargeId)
	{
	$qry	= "update m_rtct_assign_dis_charge set active='1' where id=$assignRtCtDisChargeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateAssignRtCtReleaseconfirm($assignRtCtDisChargeId)
	{
		$qry	= "update m_rtct_assign_dis_charge set active='0' where id=$assignRtCtDisChargeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}