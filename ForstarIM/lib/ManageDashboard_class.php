<?php
class ManageDashboard
{
	/****************************************************************
	This class deals with all the operations relating to Manage IP Address
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ManageDashboard(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addDashboardRec($selRole, $selDashBoard, $selUser, $userId)
	{		
		$qry = "insert into assign_dashboard (role_id, dashboard_type, user_id, created, createdby) values('$selRole', '$selDashBoard', '$selUser', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}	

	# Returns all Recs
	function fetchAllRecords()
	{
		$qry	= " select a.id, a.role_id, a.dashboard_type, b.name, a.user_id, user.username from assign_dashboard a join role b on a.role_id=b.id left join user user on user.id=a.user_id group by a.role_id, a.user_id order by b.name asc";		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Edit based on id 	
	function find($dashboardRecId)
	{
		$qry	= " select id, role_id, user_id from assign_dashboard where id=$dashboardRecId ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update 	
	function updateDashboardRec($selRole, $selDashBoard, $entryId, $selUser)
	{
		$qry = " update assign_dashboard set role_id='$selRole', dashboard_type='$selDashBoard', user_id='$selUser' where id='$entryId' ";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete Entry
	function deleteDashboardEntry($entryId)
	{
		$qry	=	" delete from assign_dashboard where id='$entryId' ";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}

	
	# Delete All Recs
	function deleteDashboardRec($selRoleId, $selUserId)
	{
		$qry	= " delete from assign_dashboard where role_id='$selRoleId' and user_id='$selUserId' ";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}
	
	# Get Access records
	function dashboardAccessRecords($selRoleId, $selUserId)
	{
		$qry = " select dashboard_type from assign_dashboard where role_id='$selRoleId'";
		if ($selUserId!="" && $selUserId!=0) $qry .= " and user_id='$selUserId'";
		else $qry .= " and user_id=0 or user_id is null";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDashboardRec($roleId, $type, $selUser)
	{
		$qry = " select id, dashboard_type from assign_dashboard where role_id='$roleId' and dashboard_type='$type' ";
		if ($selUser!=0) $qry .= " and user_id='$selUser'";
		else $qry .= " and user_id=0 or user_id is null";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);

		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array();
	}

	# Checking Dash Board Enabled
	function chkDashboardEnabled($roleId, $type, $selUserId)
	{
		$qry = " select id from assign_dashboard where role_id='$roleId' and dashboard_type='$type' and user_id='$selUserId' ";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function chkRMEnabled($roleId, $userId)
	{
		$q1 = "select id, user_id from assign_dashboard where role_id='$roleId' and user_id='$userId'";
		$qResult = $this->databaseConnect->getRecords($q1);

		$qry = " select id from assign_dashboard where role_id='$roleId' and dashboard_type in ('RMQ', 'PPQ', 'FPQ') ";
		if (sizeof($qResult)>0) {
			$qry .= " and user_id='$userId'";
			$selUserId = $userId;
		} else {
			$qry .= " and user_id=0";
			$selUserId = 0;
		}
		//echo $qry;

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array(true,sizeof($result), $selUserId):array(false,0, $selUserId);
	}

	function getUserList($selRole)
	{
		$qry = "select id, username from user where role_id='$selRole'";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# ------------------------------ Dashboard distributor account starts  here ------------------------------#
	# Update Pending cheque 
	function updatePendingChqDisplayDays($pChqDays, $crBalDisplayLimit, $overdueDisplayLimit)
	{
		$qry = " update c_system set pending_chq_days='$pChqDays', cr_bal_display_limit='$crBalDisplayLimit', overdue_display_limit='$overdueDisplayLimit' ";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;	
	}

	# get Pending cheque display days
	# Return Pending chq display days, cr bal amt display limit
	function getPendingChqDisplayDays()
	{
		$qry = "select pending_chq_days, cr_bal_display_limit, overdue_display_limit from c_system";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return array($result[0][0], $result[0][1], $result[0][2]);
	}
	# ------------------------------ Dashboard distributor account ends  here ------------------------------#


	function getReorderLevelStock()
	{
	//$qry = "select name,quantity,reorder from m_stock where quantity < reorder";

	//$qry = "select name,quantity,reorder from m_stock a left join m_stock_plantunit b on a.id=b.stock_id where b.actual_quantity < reorder";

	$qry="select a.name,b.actual_quantity,reorder,b.plant_unit,c.name from m_stock a left join m_stock_plantunit b on a.id=b.stock_id left join m_plant c on b.plant_unit=c.id where b.actual_quantity < reorder";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;

	}

}
?>