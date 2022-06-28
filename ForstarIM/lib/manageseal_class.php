<?php
class ManageSeal
{  
	/****************************************************************
	This class deals with all the operations relating to Quality Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ManageSeal(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	

	# Returns all quality
	function fetchAllRecords()
	{
		$qry	=	"select * from (SELECT id,in_seal as seal_number,accepted_status,number_gen_id,'in_seal' as seal,alpha_code FROM `m_gate_pass_seal` where in_seal!='' union SELECT id,out_seal as seal_number,gate_pass_id,number_gen_id,'out_seal' as seal,alpha_code from m_rm_gate_pass where out_seal!=''  order by id desc ) dum group by seal_number order by seal_number desc";
		//$qry	=	"SELECT id,in_Seal,accepted_status,number_gen_id FROM `m_gate_pass_seal` where in_seal!='' union SELECT id,out_seal,gate_pass_id,number_gen_id from m_rm_gate_pass where out_seal!='' order by in_Seal desc";

		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	//YES
	# Returns all Paging quality
	function fetchPagingRecords($offset, $limit)
	{
		
		$qry	=	"select * from (SELECT id,in_seal as seal_number,accepted_status,number_gen_id,'in_seal' as seal,alpha_code FROM `m_gate_pass_seal` where in_seal!='' union SELECT id,out_seal as seal_number,gate_pass_id,number_gen_id,'out_seal' as seal,alpha_code from m_rm_gate_pass where out_seal!=''  order by id desc ) dum group by seal_number order by seal_number desc limit $offset, $limit";				
	
	//$qry	=	"select * from (SELECT id,in_seal as seal_number,accepted_status,number_gen_id,'in_seal' as seal FROM `m_gate_pass_seal` where in_seal!='' union SELECT id,out_seal as seal_number,gate_pass_id,number_gen_id,'out_seal' as seal from m_rm_gate_pass where out_seal!=''  order by id desc ) dum group by seal_number order by seal_number desc limit $offset, $limit";				
 // $qry	=	"SELECT id,in_Seal,accepted_status,number_gen_id,'in_seal' as seal FROM `m_gate_pass_seal` where in_seal!='' union SELECT id,out_seal,gate_pass_id,number_gen_id,'out_seal' as seal from m_rm_gate_pass where out_seal!='' order by in_Seal desc limit $offset, $limit";

	//echo $qry;
	/*$qry	=	"SELECT id,rm_lotid FROM t_manage_rm_lotid 
					UNION 
					SELECT id,rmlotid FROM t_rmlotid_temporary ";*/
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function procurementNumberInseal($sealId)
	{
	$qry	=	"SELECT b.gate_pass_id,a.rm_gate_pass_id from m_gate_pass_seal a join m_rm_gate_pass b on a.rm_gate_pass_id=b.id where a.id	='$sealId'";
	//$qry	=	"SELECT b.gate_pass_id,a.rm_gate_pass_id from m_gate_pass_seal a join m_rm_gate_pass b on a.rm_gate_pass_id=b.procurment_id where a.id	='$sealId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	function releaseSeal($sealId)
	{
		$qry	=	"UPDATE m_gate_pass_seal SET accepted_status = '2' WHERE id='".$sealId."'";
		
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
			return $updateStatus;
	}
	function insertReleaseSeal($alphacode,$sealnumber,$seal_id,$rm_gate_pass_id,$seal_status,$user_id,$status)
	{
		$qry	=	"insert into t_seal_history (alpha_code,serial_number,seal_id,rm_gate_pass_id ,seal_status,created_on, created_by, status) values('".$alphacode."','".$sealnumber."','".$seal_id."', '".$rm_gate_pass_id."','".$seal_status."',Now(), '$user_id', '".$status."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function getAlphaPrefix($number_gen_id)
	{
		$qry	=	"SELECT alpha_code from number_gen where id='$number_gen_id'";
	//$qry	=	"SELECT b.gate_pass_id,a.rm_gate_pass_id from m_gate_pass_seal a join m_rm_gate_pass b on a.rm_gate_pass_id=b.procurment_id where a.id	='$sealId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getSealDetail($seal_id)
	{
	$qry	=	"select id,rm_gate_pass_id,in_Seal,number_gen_id FROM `m_gate_pass_seal` where id='$seal_id'";
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	function addInSeal($alphacode,$in_seals,$seal_id,$receipt_id,$seal_statusVal,$userId,$statusVal)
	{
			$qry = "insert into t_seal_history (alpha_code,serial_number,seal_id,rm_gate_pass_id ,seal_status,created_on, created_by, status) values('".$alphacode."','".$in_seals."','".$seal_id."', '".$receipt_id."','".$seal_statusVal."',Now(), '$userId', '".$statusVal."')";
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
			
		//echo $qry;
		// echo '<br/>';
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}


	####commented on 5-12-2014
	/*function addInSeal($alphacode,$in_seals,$seal_id,$receipt_id,$seal_statusVal,$userId,$statusVal)
	{
		// $qry = '';
		$insertStatus = '';
		//$rm_gate_pass_id = $this->getReceiptGatePassId($procurement_id);
		foreach($in_seals as $in_seal)
		{
			$qry = "insert into t_seal_history (alpha_code,serial_number,seal_id,rm_gate_pass_id ,seal_status,created_on, created_by, status) values('".$alphacode."','".$in_seal."','".$seal_id."', '".$receipt_id."','".$seal_statusVal."',Now(), '$userId', '".$statusVal."')";
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		}	
		
		//echo $qry;
		// echo '<br/>';
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}*/
	function getSealHistory($sealId)
	{
		$qry	=	"select id FROM `t_seal_history` where seal_id='$sealId' AND Seal_status !=  'Out seal'";
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getAllSealNumberData($alpha,$seal)
	{
		$qry	=	"select seal_status,status,rm_gate_pass_id FROM `t_seal_history` where alpha_code='$alpha' and serial_number='$seal' order by id DESC limit 1,25";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function procurementNumberOutseal($out_id)
	{
	$qry	=	"select gate_pass_id FROM `m_rm_gate_pass` where id='$out_id'";
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
}
?>