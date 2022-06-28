<?php
require_once("flib/AFModel.php");

class DocumentationInstructions_model extends AFModel
{
	protected $name = "DocumentationInstructions";
	protected $tableName = "m_doc_instructions_chk"; // Documention Instruction check list
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array("created" => "N");



	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from ".$this->tableName." where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";

		$result = $this->queryAll($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateconfirmDocumentationInstructions($confirmid)
	{
	$qry="update ".$this->tableName." set active=1 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}

	function updaterlconfirmDocumentationInstructions($confirmid)
	{
	$qry="update ".$this->tableName." set active=0 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}
}

?>