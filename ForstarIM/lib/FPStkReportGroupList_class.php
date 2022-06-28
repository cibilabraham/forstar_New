<?php
class FPStkReportGroupList
{
	/****************************************************************
	This class deals with all the operations relating to Frozen Packing Stock Report Group List
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FPStkReportGroupList(&$databaseConnect)
    	{
        	$this->databaseConnect = &$databaseConnect;
	}

	# Insert Rec
	function addFPStkReportGroupList($groupName, $sortOrder, $freezingStyle, $freezingStage, $userId)
	{
		$qry	 = "insert into t_fpstk_report_group (name, sort_order, freezing_style_id, freezing_stage_id, created, createdby) values('$groupName', '$sortOrder', '$freezingStyle', '$freezingStage', NOW(), '$userId')";
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
		$orderBy = "tfrg.sort_order asc";

		$limit  = " $offset, $limit ";

		$qry	= " select tfrg.id, tfrg.name, tfrg.sort_order, tfrg.freezing_style_id, tfrg.freezing_stage_id, mfz.code as freezingStyle, rm_stage as freezingStage  from t_fpstk_report_group tfrg left join m_freezing mfz on mfz.id=tfrg.freezing_style_id left join m_freezingstage mfs on mfs.id=tfrg.freezing_stage_id";
		
		if ($whr!="") 		$qry   	.= " where ".$whr;
		if ($orderBy!="") 	$qry   	.= " order by ".$orderBy;
		if ($limit!="")   	$qry	.= " limit ".$limit;
		//echo $qry;		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecords()
	{
		$orderBy = "tfrg.sort_order asc";

		$qry	= " select tfrg.id, tfrg.name, tfrg.sort_order, tfrg.freezing_style_id, tfrg.freezing_stage_id, mfz.code as freezingStyle, rm_stage as freezingStage from t_fpstk_report_group tfrg left join m_freezing mfz on mfz.id=tfrg.freezing_style_id left join m_freezingstage mfs on mfs.id=tfrg.freezing_stage_id ";
		
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

		//$qry = " select a.processcode_id, b.code from t_fpstk_report_group_entry a, m_processcode b where a.processcode_id=b.id and a.main_id='$fPStkReportGroupMainId' order by a.id asc ";

		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Packing  based on id 
	function find($fPStkReportGroupMainId)
	{
		$qry	= "select id, name, sort_order, freezing_style_id, freezing_stage_id from t_fpstk_report_group where id='$fPStkReportGroupMainId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update
	function updateFPStkReportGroupRec($fPStkReportGroupMainId, $groupName, $sortOrder, $freezingStyle, $freezingStage)
	{	
		$qry = "update t_fpstk_report_group set  name='$groupName', sort_order='$sortOrder', freezing_style_id='$freezingStyle', freezing_stage_id='$freezingStage' where id='$fPStkReportGroupMainId' ";

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

	# Checking Sort Order Exist
	function chkSortOrderExist($sortOrder, $groupListMainId)
	{
		$qry = "select id from t_fpstk_report_group where sort_order='$sortOrder'";
		if ($groupListMainId!="") $qry .= " and id!=$groupListMainId";

		$result	= $this->databaseConnect->getRecords($qry);	
		return (sizeof($result)>0)?true:false;
	}

	# Checking Group Name Exist
	function chkGroupNameExist($groupName, $groupListMainId)
	{
		$qry = "select id from t_fpstk_report_group where name='$groupName'";
		if ($groupListMainId!="") $qry .= " and id!=$groupListMainId";

		$result	= $this->databaseConnect->getRecords($qry);	
		return (sizeof($result)>0)?true:false;
	}


	# Filter QEL Recs
	function fetchAllQELRecords($freezingStyleId, $freezingStageId)
	{
		$whr	= "mfp.freezing_id = '$freezingStyleId' and tfqe.freezing_stage_id='$freezingStageId' ";
						
		$orderBy = "tfqe.name asc";

		$qry	= "select tfqe.id, tfqe.name from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on mfp.id=tfqe.frozencode_id ";
		
		if ($whr!="") 	  $qry   .= " where ".$whr;
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}


	/************************ Display Order Starts Here ******************************/
	/*
		$recId = RecId:MenuOrderId; RecId:MenuOrderId;
	*/
	function changeDisplayOrder($recId)
	{
		$splitRec = explode(";",$recId);
		$changeDisOrderF = $splitRec[0];
		$changeDisOrderS = $splitRec[1];
		list($recIdF, $disOrderIdF) = explode("-",$changeDisOrderF);
		list($recIdS, $disOrderIdS) = explode("-",$changeDisOrderS);

		if ($recIdF!="") $updateDisOrderRecF = $this->updateMaginStructDisOrder($recIdF, $disOrderIdF);
		if ($recIdS!="") $updateDisOrderRecS = $this->updateMaginStructDisOrder($recIdS, $disOrderIdS);
		return ($updateDisOrderRecF || $updateDisOrderRecS)?true:false;		
	}	

	# update Menu Order
	function updateMaginStructDisOrder($marginStructId, $displayOrder)
	{
		$qry = "update t_fpstk_report_group set sort_order='$displayOrder' where id='$marginStructId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/
 
}
?>