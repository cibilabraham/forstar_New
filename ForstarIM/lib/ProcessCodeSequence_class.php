<?php
class ProcessCodeSequence
{
	/****************************************************************
	This class deals with all the operations relating to Frozen Packing Stock Report Group List
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProcessCodeSequence(&$databaseConnect)
    	{
        	$this->databaseConnect = &$databaseConnect;
	}

	# Get Process Code sequence recs
	function getPCSeqRecs()
	{		
		$qry = "select  pps.fish_id, mf.name as fishName, group_concat(pps.processcode_id) as pcId, group_concat(mp.code) as pc from pre_process_sequence pps left join m_processcode mp on mp.id=pps.processcode_id left join m_fish mf on mf.id=mp.fish_id group by pps.fish_id order by mf.name asc, pps.process_criteria desc, pps.sort_id asc, mp.code asc";

		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	# Get Max Fish wise Process Code count
	function getMaxPCCount()
	{
		$qry = "select count(*) as counted from pre_process_sequence pps group by pps.fish_id order by count(*) desc";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecord($qry); 
		return $result[0];
	}



	/*
	# Insert Rec
	function addFPStkReportGroupList($qeName, $userId)
	{
		$qry	 = "insert into t_fpstk_report_group (name, created, createdby) values('$qeName', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert Rec
	function addFPStkRawEntry($mainId, $selQEL)
	{
		$qry	 = "insert into t_fpstk_report_group_entry (main_id, qel_id) values('$mainId', '$selQEL')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# get All Records
	function fetchAllPagingRecords($offset, $limit)
	{		
		$orderBy = "tfrg.name asc";

		$limit  = " $offset, $limit ";

		$qry	= " select tfrg.id, tfrg.name from t_fpstk_report_group tfrg ";
		
		if ($whr!="") 		$qry   	.= " where ".$whr;
		if ($orderBy!="") 	$qry   	.= " order by ".$orderBy;
		if ($limit!="")   	$qry	.= " limit ".$limit;
		//echo $qry;		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecords()
	{
		$orderBy = "tfrg.name asc";

		$qry	= " select tfrg.id, tfrg.name from t_fpstk_report_group tfrg ";
		
		if ($whr!="") 		$qry   	.= " where ".$whr;
		if ($orderBy!="") 	$qry   	.= " order by ".$orderBy;		
		//echo $qry;		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Processcode Records
	function getQELGroupRecs($fPStkReportGroupMainId)
	{
		$qry = " select a.qel_id, b.name from t_fpstk_report_group_entry a, t_fznpakng_quick_entry b where a.qel_id=b.id and a.main_id='$fPStkReportGroupMainId' order by a.id asc ";

		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Packing  based on id 
	function find($fPStkReportGroupMainId)
	{
		$qry	= "select a.id, a.name from t_fpstk_report_group a where a.id='$fPStkReportGroupMainId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update
	function updateFPStkReportGroupRec($fPStkReportGroupMainId, $qeName)
	{	
		$qry = "update t_fpstk_report_group set  name='$qeName' where id='$fPStkReportGroupMainId' ";

		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Del Raw Entry Rec
	function delSRGroupRawData($fPStkReportGroupMainId)
	{
		$qry = " delete from t_fpstk_report_group_entry where main_id='$fPStkReportGroupMainId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Delete 
	function deleteFPStkReportGroupEntryRec($fPStkReportGroupMainId)
	{
		$qry	=	" delete from t_fpstk_report_group where id='$fPStkReportGroupMainId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}


	function getSRGroupRawRecs($fPStkReportGroupMainId)
	{
		$qry = " select id, qel_id from t_fpstk_report_group_entry where main_id='$fPStkReportGroupMainId' order by id asc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

 
}
?>