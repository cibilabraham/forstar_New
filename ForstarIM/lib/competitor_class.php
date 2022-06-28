<?php
class Competitor
{  
	/****************************************************************
	This class deals with all the operations relating to Competitor Master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Competitor(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addCompetitor($code,$name)
	{
		$qry	= "insert into m_competitor (code,name) values('".$code."','".$name."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Competitor
	function fetchAllRecords()
	{
		$qry	= "select id, code,name,active,(select  count(a.id) from t_competitorscatch_list a where a.competitor_id = c.id) as tot from m_competitor c order by name asc";
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Competitor (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, code,name,active,(select  count(a.id) from t_competitorscatch_list a where a.competitor_id = c.id) as tot from m_competitor c order by name asc limit $offset, $limit";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Competitor based on id 
	function find($competitorId)
	{
		$qry	= "select id, code, name from m_competitor where id=$competitorId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Competitor
	function deleteCompetitor($competitorId)
	{
		$qry	= " delete from m_competitor where id=$competitorId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update Competitor
	function updateCompetitor($competitorId,$code,$name)
	{
		$qry	= " update m_competitor set code='$code', name='$name' where id=$competitorId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateCompetitorconfirm($cityId)
	{
	$qry	= "update m_competitor set active='1' where id=$cityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateCompetitorReleaseconfirm($cityId)
	{
		$qry	= "update m_competitor set active='0' where id=$cityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>