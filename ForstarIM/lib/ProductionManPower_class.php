<?php
class ProductionManPower
{
	/****************************************************************
	This class deals with all the operations relating to Production Man Power
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductionManPower(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addManPower($name, $manPowerType, $manPowerUnit, $puCost, $totCost, $manPowerRateListId)
	{
		$qry	= "insert into m_prodn_matrix_manpower (name, type, unit, pu_cost, tot_cost, rate_list_id) values('$name', '$manPowerType', '$manPowerUnit', '$puCost', '$totCost', '$manPowerRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selRateList)
	{
		$qry = "select id, name, type, unit, pu_cost, tot_cost,active from m_prodn_matrix_manpower where rate_list_id='$selRateList' order by type asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry	= "select id, name, type, unit, pu_cost, tot_cost,active from m_prodn_matrix_manpower where rate_list_id='$selRateList' order by type asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($manPowerId)
	{
		$qry = "select id, name, type, unit, pu_cost, tot_cost, rate_list_id from m_prodn_matrix_manpower where id=$manPowerId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateManPower($manPowerId, $name, $manPowerType, $manPowerUnit, $puCost, $totCost, $manPowerRateListId)
	{
		$qry = "update m_prodn_matrix_manpower set name='$name', type='$manPowerType', unit='$manPowerUnit', pu_cost='$puCost', tot_cost='$totCost', rate_list_id='$manPowerRateListId' where id=$manPowerId ";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deleteManPower($manPowerId)
	{
		$qry	= " delete from m_prodn_matrix_manpower where id=$manPowerId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Returns all Variable Man Power Records
	function getVariableManPowerRecords($selRateList)
	{
		$qry	= "select id, name, type, unit, pu_cost, tot_cost from m_prodn_matrix_manpower where rate_list_id='$selRateList' and type='V' order by name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Fixed Man Power Records
	function getFixedManPowerRecords($selRateList)
	{
		$qry	= "select id, name, type, unit, pu_cost, tot_cost from m_prodn_matrix_manpower where rate_list_id='$selRateList' and type='F' order by name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateManPowerconfirm($manPowerId)
	{
		$qry	= "update m_prodn_matrix_manpower set active='1' where id=$manPowerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateManPowerReleaseconfirm($manPowerId)
	{
		$qry	= "update m_prodn_matrix_manpower set active='0' where id=$manPowerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>