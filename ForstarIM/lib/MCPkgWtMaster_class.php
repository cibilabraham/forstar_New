<?php
class MCPkgWtMaster
{
	/****************************************************************
	This class deals with all the operations relating to MC Pkg Wt Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function MCPkgWtMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	# Add a Record
	function addMCPkgWt($selMcPkg, $packingWt, $userId, $selNetWt, $pkgName, $pkgWtTolerance)
	{
		$qry = "insert into m_mc_pkg_wt (mc_pkg_id, pkg_wt, created, createdby, net_wt, name, pkg_wt_tolerance) values('$selMcPkg', '$packingWt', NOW(), '$userId', '$selNetWt', '$pkgName', '$pkgWtTolerance')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus)	$this->databaseConnect->commit();			
		else  			$this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = " select a.id, a.mc_pkg_id, a.pkg_wt, b.code, a.net_wt, a.name, a.pkg_wt_tolerance,a.active from m_mc_pkg_wt a, m_mcpacking b where a.mc_pkg_id = b.id order by a.net_wt asc, b.number_packs asc limit $offset, $limit ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= " select a.id, a.mc_pkg_id, a.pkg_wt, b.code, a.net_wt, a.name, a.pkg_wt_tolerance,a.active from m_mc_pkg_wt a, m_mcpacking b where a.mc_pkg_id = b.id order by a.net_wt asc, b.number_packs asc ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($mcPkgWtEntryId)
	{
		$qry = "select id, mc_pkg_id, pkg_wt, net_wt, name, pkg_wt_tolerance from m_mc_pkg_wt where id=$mcPkgWtEntryId";
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update  a  Record
	function updateMCPkgWt($mcPkgWtEntryId, $selMcPkg, $packingWt, $selNetWt, $pkgName, $pkgWtTolerance)
	{
		$qry = "update m_mc_pkg_wt set mc_pkg_id='$selMcPkg', pkg_wt='$packingWt', net_wt='$selNetWt', name='$pkgName', pkg_wt_tolerance='$pkgWtTolerance' where id=$mcPkgWtEntryId ";		

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	

	# Delete 
	function deleteMCPkgWtRec($mcPkgWtEntryId)
	{
		$qry 	= " delete from m_mc_pkg_wt where id=$mcPkgWtEntryId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
	# ----------------------------
	# Check Rec Exist
	# ----------------------------
	function checkMCPackingRecExist($mcPkgId, $mcPkgWtEntryId, $netWt)
	{
		if ($mcPkgWtEntryId) $uptdQry = " and id!=$mcPkgWtEntryId";
		else $uptdQry = "";

		$qry = " select id from m_mc_pkg_wt where mc_pkg_id='$mcPkgId' and net_wt='$netWt' $uptdQry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?array(true,$rec[0][0]):array(false,0);	
	}

	# Get Package Wt
	function getPackageWt($mcPackingId, $productNetWt, $mcPkgWtId=null)
	{
		$whr = " mc_pkg_id='$mcPackingId' and net_wt='$productNetWt' ";
		if ($mcPkgWtId>0) $whr = " id = '$mcPkgWtId' ";
		//$qry = " select pkg_wt from m_mc_pkg_wt where mc_pkg_id='$mcPackingId' and net_wt='$productNetWt' ";
		$qry = " select pkg_wt from m_mc_pkg_wt where $whr ";

		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?$rec[0][0]:0;
	}

	# Get Package Wt
	function getPkgWtId($mcPackingId, $productNetWt)
	{		
		$qry = " select id from m_mc_pkg_wt where mc_pkg_id='$mcPackingId' and net_wt='$productNetWt' ";

		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?$rec[0][0]:0;
	}

	# Check MC Packaging Wt Using Sales Order
	function checkMCPkgSOExist($mcPackingId)
	{
		$qry = " select id from t_salesorder_entry where mc_pkg_id='$mcPackingId'";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getMCPkgWtRec($mcPkgId, $netWt)
	{
		$qry = " select a.id, a.mc_pkg_id, a.pkg_wt, b.code, a.net_wt, a.name, a.pkg_wt_tolerance from m_mc_pkg_wt a, m_mcpacking b where a.mc_pkg_id = b.id and mc_pkg_id='$mcPkgId' and net_wt='$netWt'  order by a.net_wt asc, b.number_packs asc ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateMCPkgWtconfirm($mcPkgId)
	{
	$qry	= "update m_mc_pkg_wt set active='1' where id=$mcPkgId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateMCPkgWtReleaseconfirm($mcPkgId)
	{
		$qry	= "update m_mc_pkg_wt set active='0' where id=$mcPkgId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	

	
}
?>