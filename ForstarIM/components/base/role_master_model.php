<?php
require_once("flib/AFModel.php");
class role_master_model extends AFModel
{
	protected $tableName = "m_role";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array( "created" => "N" );	// N - numeric, S - string

	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from m_role where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";

		$result = $this->queryAll($qry);
		return (sizeof($result)>0)?true:false;
	}
}

?>