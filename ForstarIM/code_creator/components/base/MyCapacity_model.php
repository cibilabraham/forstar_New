<?php
require_once("flib/AFModel.php");

class MyCapacity_model extends AFModel
{
	protected $name = "MyCapacity";
	protected $tableName = "m_capacity";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array();
}

?>