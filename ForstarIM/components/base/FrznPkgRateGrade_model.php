<?php
require_once("flib/AFModel.php");
class FrznPkgRateGrade_model extends AFModel
{	
	public $name="FrznPkgRateGrade";
	protected $tableName = "m_frzn_pkg_rate_grade";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array();	// N - numeric, S - string

	# Chk Processor wise grade Combination exist
	function processorGradeCombExist($fprEntryId, $gradeId, $processorId)
	{
		$qry = "select fprg.id from m_frzn_pkg_rate_grade fprg where fprg.pkg_rate_entry_id='$fprEntryId' and fprg.grade_id='$gradeId' and fprg.pre_processor_id='$processorId' ";
		//echo "<br>Frozen Pkg Processor grade=<br>$qry<br>";
		$result = $this->queryAll($qry);
		return (sizeof($result)>0)?true:false;
	}

	
}

?>