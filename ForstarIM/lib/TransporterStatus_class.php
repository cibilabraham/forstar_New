<?php
class TransporterStatus
{
	/****************************************************************
	This class deals with all the operations relating to Transporter Status
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function TransporterStatus(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Record
	function addTransporterStatus($selTransporter, $selectFrom, $selectTill, $cUserId)
	{
		$qry = "insert into m_transporter_status (transporter_id, valid_from, valid_to, created, createdby) values('$selTransporter', '$selectFrom', '$selectTill', Now(), '$cUserId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Checking for entry exist
	function chkEntryExist($selTransporter, $selectFrom, $selectTill, $transporterStatusId)
	{
		if ($transporterStatusId!="") $uptdQry = " and id!=$transporterStatusId";
		else $uptdQry	= "";

		$qry = "select id from m_transporter_status where transporter_id='$selTransporter' and ('$selectFrom'>=date_format(valid_from,'%Y-%m-%d') and '$selectFrom'<=date_format(valid_to,'%Y-%m-%d') or '$selectTill'>=date_format(valid_from,'%Y-%m-%d') and '$selectTill'<=date_format(valid_to,'%Y-%m-%d')) $uptdQry";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]!="")?true:false;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select a.id, a.transporter_id, a.valid_from, a.valid_to, b.name,a.active from m_transporter_status a, m_transporter b where a.transporter_id=b.id order by b.name asc limit $offset,$limit";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.transporter_id, a.valid_from, a.valid_to, b.name,a.active from m_transporter_status a, m_transporter b where a.transporter_id=b.id order by b.name asc";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Get rec based on id 
	function find($transporterStatusId)
	{
		$qry = "select id, transporter_id, valid_from, valid_to from m_transporter_status where id=$transporterStatusId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Rec
	function updateTransporterStatus($transporterStatusId, $selTransporter, $selectFrom, $selectTill)
	{
		$qry = "update m_transporter_status set transporter_id='$selTransporter', valid_from='$selectFrom', valid_to='$selectTill' where id='$transporterStatusId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Assign Scheme
	function deleteTransporterStatus($transporterStatusId)
	{
		$qry = "delete from m_transporter_status where id=$transporterStatusId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updatetransporterStatusconfirm($transporterStatusId)
	{
	$qry	= "update m_transporter_status set active='1' where id=$transporterStatusId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatetransporterStatusReleaseconfirm($transporterStatusId)
	{
		$qry	= "update m_transporter_status set active='0' where id=$transporterStatusId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>