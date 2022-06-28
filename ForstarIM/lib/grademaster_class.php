<?php
class GradeMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Grade master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function GradeMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert
	function addGrade($gradeCode, $min, $max,$secondaryProcessCode)
	{
		$qry	= "insert into m_grade (code,max,min,include_secondary) values('".$gradeCode."',".$max.",".$min.",'".$secondaryProcessCode."')";
		//echo $qry;
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Grades
	function fetchAllRecords()
	{
		$qry	= "select id, code, max, min,active from m_grade order by code asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsGradeActive()
	{
		$qry	= "select id, code, max, min,active from m_grade where active=1 order by code asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Paging Records
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, code,max,min,active,(select COUNT(a.id) from m_processcode2grade a where a.grade_id = mg.id)+(select count(a1.id) as id from t_dailycatchentry a1 where a1.grade_id=mg.id) as tot from m_grade mg order by code asc limit $offset, $limit";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Getting Unique Records
	function fetchAllUniqueRecords($gradeCode)
	{
		$qry	=	"select * from m_grade where code='$gradeCode'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get fish based on id 
	function find($gradeId)
	{
		$qry	= "select id, code,max,min,include_secondary from m_grade where id=$gradeId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function findGradeCode($gradeId)
	{
		$rec = $this->find($gradeId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	# Delete a fish 
	function deleteGrade($gradeId)
	{
		$qry	=	" delete from m_grade where id=$gradeId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Fish
	function updateGrade($gradeCode, $min, $max, $gradeId,$secondaryProcessCode)
	{
		$qry	=	" update m_grade set code='$gradeCode', min='$min' , max='$max',include_secondary='$secondaryProcessCode' where id=$gradeId"; 
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	


	function updateGradeReleaseconfirm($gradeId)
	{
		$qry	=	" update m_grade set active=0 where id=$gradeId";  
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateGradeconfirm($gradeId)
	{
	$qry	=	" update m_grade set active=1 where id=$gradeId"; 
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Took Grade based on Average -- used in DAILY CATCH ENTRY
	function fetchGradeRecords($countAverage)
	{
		$qry	= "select id, code,min,max from m_grade where min>='$countAverage' and max>='$countAverage' order by min asc";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Checking Grade Id in use
	function gradeRecInUse($gradeId)
	{		
		$qry = " select id from (
				select a.id as id from m_processcode2grade a where a.grade_id='$gradeId'
			union
				select a1.id as id from t_dailycatchentry a1 where a1.grade_id='$gradeId'	
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Check Grade Rec Exist
	function chkGradeRecExist($gradeCode, $gradeId)
	{
		$qry = " select id from m_grade where code='$gradeCode'";
		if ($gradeId) $qry .= " and id!=$gradeId ";

		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

}