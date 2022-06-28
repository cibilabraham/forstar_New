<?php
class CommonReason
{  
	/****************************************************************
	This class deals with all the operations relating to common reason
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function CommonReason(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addCommonReason($accountType,$reason,$checkPoint,$createdBy)
	{
		$qry	= "insert into m_common_reason (cod,reason,check_point,created,created_by) values('".$accountType."','". $reason."','". $checkPoint."',NOW(),'".$createdBy."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function addChecklistRecord($commonReasonId, $chkListName, $required)
	{
		$qry = "insert into m_common_reason_chk (common_reason_id,name,required) values ('$commonReasonId', '$chkListName', '$required') ";
		//echo $qry;			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	
	function updateCommonReasonChkList($commonReasonId, $chkListName, $required)
	{
		$qry	= "update m_common_reason_chk  set name='$chkListName',required='$required' where id= '$commonReasonId' ";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function chkEntryExist($reason, $selICId)
	{
		$qry = "select id from m_common_reason where reason='$reason' ";
		if ($selICId) $qry .= " and id!=$selICId";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
		# Returns all common reason (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,cod,reason,check_point,default_entry,active from  m_common_reason order by reason asc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all common reason
	function fetchAllRecords()
	{
		
		$qry	= "select * from m_common_reason order by reason asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	
	function updateCommonReason($cmnReasonId,$accountType,$reason, $checkPoint)
	{
		$qry	= "update m_common_reason  set cod='$accountType',reason='$reason',check_point='$checkPoint' where id=$cmnReasonId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	function updateConfirmCommonReason($cmnReasonId)
	{
	$qry="update m_common_reason set active=1 where id=$cmnReasonId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;

	}
	
	function updaterlconfirmCommonReason($cmnReasonId)
	{
	$qry="update m_common_reason set active=0 where id=$cmnReasonId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;
	}
	
	# Check Common reason using any other section
	function commonReasonExist($commonReasonId)
	{
		$qry = "select id from t_distributor_ac where reason_id='$commonReasonId'";
		$recs = $this->databaseConnect->getRecords($qry);	
		return (sizeof($recs)>0)?true:false;
	}
	function chkCheckLisIntUse($commonReasonId)
	{
		$chkListRecs=$this->getChecklistRecords($commonReasonId);
		$recInUse=false;
		//echo $chkListRecs;
		foreach($chkListRecs as $rcd)
		{	
			$cmnReasonChekListId= $rcd[0];
			//echo $cmnReasonChekListId;
			$chkListRecInUse=$this->chkListRecordInUse($cmnReasonChekListId);
			if($chkListRecInUse) $recInUse=true;
			
		}
		return $recInUse;
	}
	
	function deleteChkListRcd($commonReasonId)
	{
		$qry = "delete from  m_common_reason_chk where common_reason_id = '$commonReasonId'";
		$result = $this -> databaseConnect->delRecord($qry);
		if($result) $this -> databaseConnect -> commit();
		else
		$this->databaseConnect->rollback();
		return $result;
	}
	
	function chkListRecordInUse($cmnReasonChekListId)
	{
		$qry = "select id from  t_distributor_ac_chk_list where chk_list_id = '$cmnReasonChekListId' ";
		//echo $qry;
		$result=$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Delete common reason
	function deleteCommonReason($commonReasonId)
	{
		$qry	= " delete from  m_common_reason where id = '$commonReasonId' ";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	
	function getChecklistRecords($commonReasonId)
	{
			
			$qry =  "select id, name,required  from m_common_reason_chk where common_reason_id = $commonReasonId order by id asc ";
			$result	= $this->databaseConnect->getRecords($qry);
			return $result;
	}
	

	function delChekListRec($cmnReasonChkId)
	{
		$qry	=	" delete from m_common_reason_chk where id='$cmnReasonChkId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function displayChkList($commonReasonId)
	{
		//$qry =  "select id, name,required  from m_common_reason_chk where common_reason_id = $commonReasonId order by id asc ";
		//echo $qry;
		$chkListRecs	= $this->getChecklistRecords($commonReasonId);
		
		//$chkListRecs = $this->crChkList_m->findAll(array("where"=>"common_reason_id='".$commonReasonId."'", "order"=>"id asc"));
		
		$displayHtml = "";
		if (sizeof($chkListRecs)>0) {
			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";		
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
			$displayHtml .= "<td class=listing-head>Check List</td>";
			$displayHtml .= "<td class=listing-head>Required</td>";
			$displayHtml .= "</tr>";
			foreach ($chkListRecs as $clr) {
				$displayHtml .= "<tr bgcolor=#fffbcc>";
				$displayHtml .= "<td class=listing-item nowrap>";
				//$displayHtml .= $clr->name;
				$displayHtml .= $clr[1];
				$displayHtml .= "</td>";
				$displayHtml .= "<td class=listing-item align=center>";
				//$displayHtml .= ($clr->required=='Y')?'YES':'NO';
				$displayHtml .=  ($clr[2]=='Y')?'YES':'NO';
				$displayHtml .=	"</td>";
				$displayHtml .= "</tr>";	
			}
			$displayHtml  .= "</table>";
		}
		//echo $displayHtml;
		return $displayHtml;
	}
	
	function chkList($chk)
	{
		return ($chk=='Y')?"YES":"NO";
	}
	
	
	# Get common reason based on id 
	function find($commonReasonId)
	{
		$qry	= "select id, cod, reason,check_point,active from m_common_reason where id=$commonReasonId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	
	function chkReasonEntryExist($accountType, $reason,$selICId)
	{	
		$qry = "select id from m_common_reason where cod = '$accountType' and reason = '$reason' ";
		if ($selICId) $qry .= " and id!=$selICId";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	function updateCommonReasonChkPoint($cmnReasonId, $checkPoint)
	{
		$qry	= "update m_common_reason  set check_point='$checkPoint' where id=$cmnReasonId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
		

}

