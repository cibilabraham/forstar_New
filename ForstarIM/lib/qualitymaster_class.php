<?php
class QualityMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Quality Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function QualityMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	// function addQuality($qualityCode,$qualityName)
	// {
		// $qry	= "insert into m_quality (code,name) values('".$qualityCode."','".$qualityName."')";
		////echo $qry;
		// $insertStatus	= $this->databaseConnect->insertRecord($qry);		
		// if ($insertStatus) $this->databaseConnect->commit();
		// else $this->databaseConnect->rollback();
		// return $insertStatus;
	// }

	function addQuality($includeBilling,$qualityCode,$qualityName,$description)
	{
		$qry	= "insert into m_quality (billing_include,code,name,description) values('".$includeBilling."','".$qualityCode."','".$qualityName."','".$description."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all quality
	function fetchAllRecords()
	{
		$qry	=	"select id, name, code,active,(select count(tqe.id) from t_qualityentry tqe where entry_id=a.id) as tot,billing_include,	change_requirement,description from m_quality a order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveQuality()
	{
		$qry	=	"select id, name, code,active,description from m_quality where active=1 order  by name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Paging quality
	function fetchPagingRecords($offset, $limit)
	{
		$qry	=	"select id, name, code,active,(select count(tqe.id) from t_qualityentry tqe where entry_id=a.id) as tot,billing_include,	change_requirement,description	 from m_quality a order by name asc limit $offset, $limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get fish based on id 
	function find($qualityId)
	{
		$qry	=	"select id, name, code,billing_include,description from m_quality where id=$qualityId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a fish 
	function deleteQuality($qualityId)
	{
		$qry	=	" delete from m_quality where id=$qualityId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Fish
	function updateQuality($includeBilling,$qualityId,$qualityName, $qualityCode,$description)
	{
		$qry	= " update m_quality set billing_include='$includeBilling',code='$qualityCode' , name='$qualityName' ,description='$description'  where id=$qualityId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	
	
	
	// function updateQuality($qualityId,$qualityName, $qualityCode)
	// {
		// $qry	= " update m_quality set code='$qualityCode' , name='$qualityName'  where id=$qualityId";

		// $result	= $this->databaseConnect->updateRecord($qry);
		// if ($result) $this->databaseConnect->commit();
		// else $this->databaseConnect->rollback();
		// return $result;	
	// }	


	function updateQualityconfirm($qualityId)
{
$qry	= "update m_quality set active='1' where id=$qualityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function updateQualityReleaseconfirm($qualityId){
	$qry	= "update m_quality set active='0' where id=$qualityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}


function checkDuplicate($qualityCode,$qualityName)
{
	$qry	=	"select id from m_quality where code='$qualityCode',name='qualityName'";
	return $this->databaseConnect->getRecord($qry);
}
}
?>