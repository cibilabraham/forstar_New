<?php
class FrozenPackingRateGrade
{  
	/****************************************************************
	This class deals with all the operations relating to loading port
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FrozenPackingRateGrade(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function processorGradeCombExist($fprEntryId, $gradeId, $processorId)
	{
		$qry = "select fprg.id from m_frzn_pkg_rate_grade fprg where fprg.pkg_rate_entry_id='$fprEntryId' and fprg.grade_id='$gradeId' and fprg.pre_processor_id='$processorId' ";
		//echo "<br>Frozen Pkg Processor grade=<br>$qry<br>";
		$result=$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function chckRateListExistInFrozenGrade($rateListId)
	{
		$qry = "select id from t_dailyfrozenpacking_grade where rate_list_id='$rateListId' union select id from t_dailyfrozenpacking_grade_rmlotid where rate_list_id='$rateListId'";
		//echo "<br>Frozen Pkg Processor grade=<br>$qry<br>";
		$result=$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function addFrozenPackRateGrade($lastInsertedId,$gradeId,$exptRate,$preProcessorId)
	{
		$qry	= "insert into m_frzn_pkg_rate_grade(pkg_rate_entry_id,grade_id,rate,pre_processor_id) values('".$lastInsertedId."','".$gradeId."','".$exptRate."','".$preProcessorId."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	function updateFrozenPackGrade($frzPkRtGd,$exptRate)
	{
		$qry	= "update m_frzn_pkg_rate_grade set rate='$exptRate' where id=$frzPkRtGd";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	# Comma seperated grade entry id
	function deleteFrznPkgRateGrade($selGradeEntryId)
	{
		$qry = "delete from m_frzn_pkg_rate_grade where id in ($selGradeEntryId)";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	

}

