<?php
class FishMaster
{  
	/****************************************************************
	This class deals with all the operations relating to fish master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FishMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addFish($fishCode, $fishName, $categoryId,$sourceId)
	{
		$qry	= "insert into m_fish (name,code,category_id,source_id) values('".$fishName."','".$fishCode."','".$categoryId."','".$sourceId."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);

		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all fishs 
	function fetchAllRecords()
	{
		
		//$qry	= "select mf.id, name, code,category_id,mf.active from m_fish mf join m_fishcategory  mc on mf.category_id=mc.id order by name";	
	$qry	= "select mf.id, name, code,category_id,mf.active,((select COUNT(a.id) from m_processcode a where a.fish_id = mf.id)+(select COUNT(a1.id) from m_process a1 where a1.fish_id=mf.id)+(select count(a2.id) from t_dailycatchentry a2 where a2.fish=mf.id)+
				(select count(a3.id) from t_dailypreprocess a3 where a3.fish_id=mf.id)) as tot from m_fish mf join m_fishcategory mc on mf.category_id=mc.id order by name";


		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

function fetchAllRecordsFishactive()
	{
		
		$qry	= "select mf.id, name, code,category_id,mf.active from m_fish mf join m_fishcategory  mc on mf.category_id=mc.id where mc.active=1 and mf.active=1 order by name";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all fishs (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select mf.id, name, code,category_id,mf.active,((select COUNT(a.id) from m_processcode a where a.fish_id = mf.id)+(select COUNT(a1.id) from m_process a1 where a1.fish_id=mf.id)+(select count(a2.id) from t_dailycatchentry a2 where a2.fish=mf.id)+
				(select count(a3.id) from t_dailypreprocess a3 where a3.fish_id=mf.id)) as tot from m_fish mf join m_fishcategory mc on mf.category_id=mc.id order by name limit $offset, $limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateFishconfirm($fishId)
	{
	$qry	= "update m_fish set active='1' where id=$fishId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateFishReleaseconfirm($fishId)
	{
		$qry	= "update m_fish set active='0' where id=$fishId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	
	# Get fish based on id 
	function find($fishId)
	{
		$qry	= "select id, name, code,category_id,source_id  from m_fish where id=$fishId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function findFishName($fishId)
	{
		$rec = $this->find($fishId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	# Delete a fish 
	function deleteFish($fishId)
	{
		$qry	= " delete from m_fish where id=$fishId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}

	# Delete Fish
	function updateFish($fishId,$fishName, $fishCode,$categoryId,$sourceId)
	{
		$qry	= " update m_fish set name='$fishName', code='$fishCode', category_id='$categoryId',source_id = '$sourceId' where id=$fishId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# -----------------------------------------------------
	# Checking Fish Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function fishRecInUse($fishId)
	{		
		$qry = " select id from (
				select a.id as id from m_processcode a where a.fish_id='$fishId'
			union
				select a1.id as id from m_process a1 where a1.fish_id='$fishId'
			union 
				select a2.id as id from t_dailycatchentry a2 where a2.fish='$fishId'
			union 
				select a3.id as id from t_dailypreprocess a3 where a3.fish_id='$fishId'
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Get All Fish Recs
	function getFishRecs()
	{
		$fishArr = array();
		//$fishRecs = $this->fetchAllRecords();
		$fishRecs=$this->fetchAllRecordsFishactive();
		if (sizeof($fishRecs)>0) {
			foreach ($fishRecs as $fr) {
				$fishId = $fr[0];
				$fName  = $fr[1];
				$fishArr[$fishId] = $fName;
			}	
		}
		return $fishArr;
	}
	
	# Get all fish source 
	function fetchAllSourceRecords()
	{
		$qry	= "select id,name from m_fish_source";
		return $this->databaseConnect->getRecords($qry);
	}
}