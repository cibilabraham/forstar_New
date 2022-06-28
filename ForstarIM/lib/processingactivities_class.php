<?php
class ProcessingActivity
{
	/****************************************************************
	This class deals with all the operations relating to Processing Activity 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProcessingActivity(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addProcessingActivity($name, $description, $selSubModule)
	{
		$qry	=	"insert into m_processingactivities (name,description) values('".$name."','".$description."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			#Getting Last Id
			$lastId = $this->databaseConnect->getLastInsertedId();
			#Insert Selected Activities
			$this->addActivity2SubModule($lastId, $selSubModule);
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Processing Activity 
	function fetchAllRecords()
	{
		$qry	=	"select id, name, description,active from m_processingactivities order by name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllActiveRecords()
	{
		$qry	=	"select id, name, description,active from m_processingactivities where active=1 order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Processing Activity (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	=	"select id, name, description,active from m_processingactivities order by name asc limit $offset, $limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Processing Activity based on id 
	function find($processingActivityId)
	{
		$qry	=	"select id, name, description from m_processingactivities where id=$processingActivityId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Processing Activity
	function deleteProcessingActivity($processingActivityId)
	{
		$qry	= " delete from m_processingactivities where id=$processingActivityId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update Processing Activity
	function updateProcessingActivity($processingActivityId, $name, $description, $selSubModule)
	{
		$qry	=	" update m_processingactivities set name='$name', description='$description' where id=$processingActivityId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			#Delete all Entries from activity to subModule
			$this->deleteActivity2SubModule($processingActivityId);

			#Insert the selected Sub Module
			$this->addActivity2SubModule($processingActivityId, $selSubModule);
	
			$this->databaseConnect->commit();
		} else $this->databaseConnect->rollback();
		return $result;	
	}

	//------------------------- activity to sub module  Start Here -------------
	#Fetch all Sub Module Records
	function fetchAllSubModuleRecords()
	{
		$qry = "select id, name from submodule order by id asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	#Insert Sub module in Processing activitiy
	function  addActivity2SubModule($lastId, $selSubModule)
	{
		if ($selSubModule) {
			foreach ($selSubModule as $smId) {
				$subModuleId =	"$smId";
				$qry = "insert into m_activity2submodule (activity_id, submodule_id) values('".$lastId."','".$subModuleId."')";
				//echo $qry;
				$insertSubModule = $this->databaseConnect->insertRecord($qry);
				if ($insertSubModule) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		} 
	}

	#In Edit mode fetch Activity records
	function fetchSelectedSubModuleRecords($editId)
	{
		$qry = "select a.id, a.name, b.submodule_id from submodule a left join m_activity2submodule b on a.id=b.submodule_id and b.activity_id='$editId' order by b.id desc, a.id asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete a Processing Activity to sub module
	function deleteActivity2SubModule($processingActivityId)
	{
		$qry	= " delete from m_activity2submodule where  activity_id=$processingActivityId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Filter selected Sub Module Records
	function filterSelectedSubModule($processingActivityId)
	{
		$qry 	=	"select a.id, a.activity_id, a.submodule_id, b.name from  m_activity2submodule a, submodule b where b.id=a.submodule_id and a.activity_id='$processingActivityId' order by b.id asc";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	//------------------------- activity to sub module  End Here -------------

	#Check whether the selected entries exist
	function moreEntriesExist($processingActivityId)
	{
		$qry = " select id from m_preprocessor2activity where activity_id = '$processingActivityId'";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}


	function updateprocessingactivityconfirm($processingActivityId)
	{
	$qry	= "update m_processingactivities set active='1' where id=$processingActivityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateprocessingactivityReleaseconfirm($processingActivityId)
	{
		$qry	= "update m_processingactivities set active='0' where id=$processingActivityId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>