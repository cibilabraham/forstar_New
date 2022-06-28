<?php
class MCPacking
{  
	/****************************************************************
	This class deals with all the operations relating to MCPacking
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function MCPacking(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	#Add
	function addMCPacking($code,$numPacks,$description)
	{
		$qry	=	"insert into m_mcpacking (code, number_packs, descr) values('".$code."','".$numPacks."','".$description."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	=	"select id, code, number_packs, descr,active,((select COUNT(a1.id) from m_mc_pkg_wt a1 where a1.mc_pkg_id = a.id)+(select COUNT(a2.id) from t_fznpakng_quick_entry a2 where a2.mcpacking_id=a.id)+(select count(a3.id) from t_dailyfrozenpacking_entry a3 where a3.mcpacking_id=a.id)) as tot from m_mcpacking a order by number_packs asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivemcpacking()
	{
		$qry	=	"select id, code, number_packs, descr,active from m_mcpacking where active=1 order by number_packs asc";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Records(PAGING)
	function fetchPagingRecords($offset,$limit)
	{
		$qry	=	"select id, code, number_packs, descr,active,((select COUNT(a1.id) from m_mc_pkg_wt a1 where a1.mc_pkg_id = a.id)+(select COUNT(a2.id) from t_fznpakng_quick_entry a2 where a2.mcpacking_id=a.id)+(select count(a3.id) from t_dailyfrozenpacking_entry a3 where a3.mcpacking_id=a.id)) as tot from m_mcpacking a order by number_packs asc limit $offset,$limit";		
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}


	# Get Record  based on id 
	function find($mcpackingId)
	{
		$qry	=	"select id, code, number_packs, descr from m_mcpacking where id=$mcpackingId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update
	function updateMCPacking($mcpackingId,$code,$numPacks, $description)
	{
		$qry	=	" update m_mcpacking set code='$code', number_packs='$numPacks', descr='$description' where id=$mcpackingId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete
	function deleteMCPacking($mcpackingId)
	{
		$qry	= " delete from m_mcpacking where id=$mcpackingId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
	function findMCPackingCode($mcpackingId)
	{
		$rec = $this->find($mcpackingId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	# Get Num of MC Pack
	function numMCPacks($mcpackingId)
	{
		$qry	= "select number_packs from m_mcpacking where id='$mcpackingId'";
		$rec 	= $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}

	function findMCPackingValue($numPck)
	{
		$qry	=	"select code from m_mcpacking where number_packs=$numPck";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function findValue($numPck)
	{
		
	}


	function updateMCPackingconfirm($mcpackingId)
	{
	$qry	= "update m_mcpacking set active='1' where id=$mcpackingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateMCPackingReleaseconfirm($mcpackingId)
	{
		$qry	= "update m_mcpacking set active='0' where id=$mcpackingId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	
}
?>