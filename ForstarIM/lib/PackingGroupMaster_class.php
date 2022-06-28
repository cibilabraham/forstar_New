<?php
class PackingGroupMaster
{
	/****************************************************************
	This class deals with all the operations relating to Packing Group Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PackingGroupMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addPackingGroup($pSelLeft, $pSelRight)
	{
		$qry = "insert into m_pkg_group (p_left, p_right) values('$pSelLeft', '$pSelRight')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus)	$this->databaseConnect->commit();			
		else  			$this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = " select a.id, a.p_left, a.p_right,a.active from m_pkg_group a order by a.id asc limit $offset, $limit ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= " select a.id, a.p_left, a.p_right,a.active from m_pkg_group a order by a.id asc ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($packingGroupId)
	{
		$qry = "select id, p_left, p_right from m_pkg_group where id=$packingGroupId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update  a  Record
	function updatePackingGroup($packingGroupId, $pSelLeft, $pSelRight)
	{
		$qry = "update m_pkg_group set p_left='$pSelLeft', p_right='$pSelRight' where id=$packingGroupId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	

	# Delete 
	function deletePackingGroupRec($packingGroupId)
	{
		$qry 	= " delete from m_pkg_group where id=$packingGroupId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
	/**	
	* Check Rec Exist
	*/
	function checkPackingGroupRecExist($pSelLeft, $pSelRight, $packingGroupId)
	{
		if ($packingGroupId) $uptdQry = " and id!=$packingGroupId";
		else $uptdQry = "";

		$qry = " select id from m_pkg_group where (p_left='$pSelLeft' and p_right='$pSelRight') or (p_left='$pSelRight' and p_right='$pSelLeft') $uptdQry";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	

	# Check MC Packaging Wt Using Sales Order
	function checkMCPkgSOExist($mcPackingId)
	{
		$qry = " select id from t_salesorder_entry where mc_pkg_id='$mcPackingId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# new
	# Check Whether Product Group Exist
	function checkProductGroupExist($productStateId)
	{
		$qry = "select product_group from m_product_state where id=$productStateId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]=='Y')?true:false;
	}

	# Filter State List
	function filterProductGroupList($productGroupExist)
	{		
		$qry	=	"select  id, name from m_product_group order by name asc";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (!$productGroupExist) $resultArr = array('0'=>'-- No Group --');		
		else if ($productGroupExist) {			
			$resultArr = array(''=>'-- Select --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

	/**
	* Returns all Distinct Net Wt
	*/
	function fetchProductNetWtRecs($pstateId, $pGroupId)
	{
		$qry = " select distinct net_wt from m_product_manage where product_state_id='$pstateId' and product_group_id='$pGroupId' order by net_wt asc ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		//return $result;
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[0];
		}
		return $resultArr;
	}

	function updatePackingGroupconfirm($packingGroupId)
	{
		$qry	= "update m_pkg_group set active='1' where id=$packingGroupId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updatePackingGroupReleaseconfirm($packingGroupId)
	{
		$qry	= "update m_pkg_group set active='0' where id=$packingGroupId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}

?>