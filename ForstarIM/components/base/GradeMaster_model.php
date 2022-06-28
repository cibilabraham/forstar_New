<?php
require_once("flib/AFModel.php");
class GradeMaster_model extends AFModel
{	
	public $name="GradeMaster";
	protected $tableName = "m_grade";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array();	// N - numeric, S - string

	
}

?>