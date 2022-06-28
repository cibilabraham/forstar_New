<?php
class PlantMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Plants Master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PlantMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert
	function addPlant($no, $name)
	{
		$qry	=	"insert into m_plant (no,name) values('".$no."','".$name."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Plant 
	function fetchAllRecords()
	{
		$qry	=	"select id, no, name,active from m_plant order by name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRecordsPlantsActive()
	{
		$qry	=	"select id, no, name,active from m_plant where active='1'  order by name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Plant (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	=	"select id, no, name,active,((select COUNT(a.id) from m_preprocessor2plant a where a.plant_id = mp.id)+(select COUNT(a1.id) from t_dailycatch_main a1 where a1.unit=mp.id)) as tot from m_plant mp order by name asc limit $offset, $limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#filter plant based on Id
	function filterAllPlantRecords($plantId)
	{
		$qry	=	"select id, no, name from m_plant where id=$plantId";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Plant based on id 
	function find($plantId)
	{
		$qry	=	"select id, no, name from m_plant where id=$plantId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Plant
	function deletePlant($plantId)
	{
		$qry	=	" delete from m_plant where id=$plantId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update Plant
	function updatePlant($plantId, $no, $name)
	{
		$qry	=	" update m_plant set no='$no', name='$name' where id=$plantId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# -----------------------------------------------------
	# Checking Plant Id is in use ( Pre Process Maste, Daily Catch Entry);
	# -----------------------------------------------------
	function plantNUnitRecInUse($plantId)
	{		
		$qry = " select id from (
				select a.id as id from m_preprocessor2plant a where a.plant_id='$plantId'
			union
				select a1.id as id from t_dailycatch_main a1 where a1.unit='$plantId'		
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	function updatePlantconfirm($plantId)
	{
	$qry	= "update m_plant set active='1' where id=$plantId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	

	}

	function updatePlantReleaseconfirm($plantId)
	{
		$qry	= "update m_plant set active='0' where id=$plantId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
}
?>