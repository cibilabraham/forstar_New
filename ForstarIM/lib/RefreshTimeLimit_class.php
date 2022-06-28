<?php
class RefreshTimeLimit
{
	/****************************************************************
	This class deals with all the operations relating to Refresh Time Limit
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function RefreshTimeLimit(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addRefreshTimeLimit($sSubModule, $refreshTime, $userId, $selFunction)
	{
		$qry	= "insert into refresh_time_limit (submodule_id, refresh_time, created, createdby, function_id) values('$sSubModule' ,  '$refreshTime' , NOW(), $userId, '$selFunction')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Recs(PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select rtl.id, rtl.submodule_id, rtl.refresh_time, if (rtl.function_id!=0,fn.name,'ALL') as functionName, sm.name as subModuleName from refresh_time_limit rtl left join submodule sm on sm.id=rtl.submodule_id left join function fn on rtl.function_id=fn.id order by sm.name asc, rtl.function_id desc limit $offset, $limit";

		//echo "<br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all recs
	function fetchAllRecords()
	{
		$qry	= "select rtl.id, rtl.submodule_id, rtl.refresh_time, if (rtl.function_id!=0,fn.name,'ALL') as functionName, sm.name as subModuleName from refresh_time_limit rtl left join submodule sm on sm.id=rtl.submodule_id left join function fn on rtl.function_id=fn.id order by sm.name asc, rtl.function_id desc ";	
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Fetch all Sub Module Records
	function fetchAllSubModuleRecords()
	{
		$qry = "select id, name from submodule order by module_id asc, id asc";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Filter selected Sub Module Records
	function filterSelectedSubModule($refreshTimeLimitId, $selSubModuleIds)
	{
		$qry = "select id, name from submodule where id in ($selSubModuleIds) order by module_id asc, id asc";	
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Rec
	function find($refreshTimeLimitId)
	{
		$qry	= "select id, submodule_id, refresh_time, function_id from refresh_time_limit where id=$refreshTimeLimitId";
		return $this->databaseConnect->getRecord($qry);
	}

	#In Edit mode select sub moule records
	function fetchSelectedSubModuleRecords($editId, $submoduleIds)
	{
		$qry = "select a.id, a.name, b.submodule_id from submodule a left join refresh_time_limit b on a.id in ($submoduleIds) and b.id='$editId' order by FIELD(a.id, $submoduleIds) desc, a.module_id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Update 
	function updateRefreshPageLimit($refreshTimeLimitId, $sSubModule, $refreshTime, $selFunction)
	{
		$qry = " update refresh_time_limit set submodule_id='$sSubModule', refresh_time='$refreshTime', function_id='$selFunction' where id=$refreshTimeLimitId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	# Delete 
	function deleteRefreshTimeLimit($refreshTimeLimitId)
	{
		$qry	= " delete from refresh_time_limit where id=$refreshTimeLimitId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	function chkRecExist($submoduleIds, $cId, $selFunction)
	{
		$whr = " id is not null and function_id='$selFunction' ";
		$repStr ="";
		if (sizeof($submoduleIds)>0) {
			$repStr = str_replace("," , "%' OR submodule_id LIKE '%", $submoduleIds );
		} else {
			$repStr = $submoduleIds;
		}

		$whr .= " and (submodule_id  LIKE '%".$repStr."%')"; 
		if ($cId!="") $whr .= " and id!='$cId'";

		$qry = "select id from refresh_time_limit ";	
		if ($whr!="") $qry .= " where ".$whr;

		//echo "<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;	
	}

	# Return Refresh Time
	function getRefreshTimeLimit($submoduleId, $functionId)
	{
		$qry	= "select id, submodule_id, refresh_time from refresh_time_limit where submodule_id  LIKE '%".$submoduleId."%' and (function_id='$functionId' or function_id=0) order by function_id desc";
		//echo "<br>$qry<br>";	
		$rec= $this->databaseConnect->getRecord($qry);
		return $rec[2];
	}

	# Get Function Records
	function getFunctionRecs($selSubModule)
	{
		$whr  = " group_main_id=0 and pmenu_id='$selSubModule'";
				
		$orderBy = " menu_order asc";

		$qry = "select id, name, menu_order from function ";

		if ($whr!="") 		$qry .= " where".$whr ;
		if ($orderBy!="") 	$qry .= " order by".$orderBy ;
		//echo "<br>$qry<br>";		

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	
}
?>