<?php
require_once("flib/AFModel.php");

class FrznPkgAccounts_model extends AFModel
{
	protected $name = "FrznPkgAccounts";
	protected $tableName = "FrznPkgAccounts";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array();

	function updateDFPGradeRec($gradeEntryId, $settled, $rate, $totalAmt)
	{
		$qry = "update t_dailyfrozenpacking_grade set pkg_rate='$rate', pkg_amount='$totalAmt'";
		
		if ($settled=='Y') $qry .= " , settled='$settled', settled_date=Now()";		
		else if ($settled=='N') $qry .= " , settled='$settled', settled_date=null";		
		else $qry .="";
		$qry .= "  where id='$gradeEntryId' ";
		//echo "<br>Update=<br>$qry";
		return $this->exec($qry);
	}
}

?>