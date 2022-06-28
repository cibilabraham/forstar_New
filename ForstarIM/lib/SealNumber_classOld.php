<?php
class SealNumber
{  
	/****************************************************************
	This class deals with all the operations relating to Shipment > Purchase Order
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SealNumber(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	/**
	* Check Seal Number Exist
	*/
	function checkSealNumberExist($sealNo, $editMainId)
	{

		 $qry = " select id from m_seal_master where seal_number='$sealNo' AND change_status != 'Free'";
		if ($editMainId!="") $qry .= " and id!=$editMainId";
		

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	
	/**
	* Check Seal Number Exist blocked
	*/
	function checkSealNumberExistSeal($sealNo, $editMainId)
	{

		 $qry = " select id from m_seal_master where seal_number='$sealNo' AND change_status = 'Used'";
		if ($editMainId!="") $qry .= " and id!=$editMainId";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	
	function check($sealNo)
	{
		 $qry = " select id from m_seal_master where seal_number='$sealNo'";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}
	
	#Add a Seal Master
	function addSealMaster($sealNo, $status, $purpose,$changeStatus, $userId)
	{
		$qry	=	"insert into m_seal_master (seal_number, status, purpose,change_status, created_on, created_by) values('".$sealNo."', '$status', '".$purpose."','".$changeStatus."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select id, seal_number, status, purpose,change_status,active FROM m_seal_master order by seal_number limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Get Seal number based on id 
	function find($sealNumberId)
	{
		$qry	= "select id, seal_number, status, purpose,change_status  from m_seal_master where id=$sealNumberId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update  a  Seal Number
	function updateSealMaster($sealNumberId, $sealNo, $status, $purpose,$changeStatus)
	{
		$qry	= " update m_seal_master set seal_number='$sealNo', status='$status', purpose='$purpose',change_status='$changeStatus' where id=$sealNumberId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
		# Delete a Seal Master 
	function deleteSealMaster($sealNumberId)
	{
		$qry	= " delete from m_seal_master where id=$sealNumberId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function updateSealMasterObjconfirm($sealNumberId){
		$qry	= "update m_seal_master set active='1' where id=$sealNumberId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateSealMasterReleaseconfirm($sealNumberId){
	$qry	= "update m_seal_master set active='0' where id=$sealNumberId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	# Returns all seal master 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, seal_number, status, purpose,change_status,active FROM m_seal_master order by seal_number asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

}
?>