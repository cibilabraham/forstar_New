<?
class ProcessingRestriction
{  
	/****************************************************************
	This class deals with all the operations relating to Processing Activity 
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function ProcessingRestriction(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addProcessingRestriction($selpage, $selActivity)
	{
		$qry	=	"insert into m_processingrestriction (function_id,activity_id) values('".$selpage."','".$selActivity."')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Processing Activity 
	function fetchAllRecords()
	{
		$qry	=	"select a.id, c.name, b.name from m_processingrestriction a, m_processingactivities b, function c where a.function_id=c.id and a.activity_id=b.id order by c.name asc";

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Processing Activity (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	=	"select a.id, c.name, b.name from m_processingrestriction a, m_processingactivities b, function c where a.function_id=c.id and a.activity_id=b.id order by c.name asc limit $offset, $limit";

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Processing Restriction record based on id 
	function find($processingRestrictionId)
	{
		$qry	=	"select id, function_id, activity_id from m_processingrestriction where id=$processingRestrictionId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Processing Restriction
	function deleteProcessingRestriction($processingRestrictionId)
	{
		$qry	=	" delete from m_processingrestriction where id=$processingRestrictionId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	# Update Processing Restriction

	function updateProcessingRestriction($processingRestrictionId, $selpage, $selActivity)
	{
		$qry	=	" update m_processingrestriction set function_id='$selpage', activity_id='$selActivity' where id=$processingRestrictionId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Get processing Activity  URL From function 
	function getUrlForProcessingRestriction()
	{
		$qry	=	"select id, name, url from function where processing_activity='Y' order by name asc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	//Not Correct this function
	function checkUsageOfActivity($processingRestrictionId)
	{
		$qry	=	"select a.id from m_processingrestriction a, m_preprocessor2activity b, m_preprocessor c, t_dailypreprocess_processor_qty d, t_processorspayments e where a.activity_id=b.activity_id and b.processor_id=c.id and c.id=d.preprocessor_id and e.processor_id=c.id and a.id=$processingRestrictionId";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}

}