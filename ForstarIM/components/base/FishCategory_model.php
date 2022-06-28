<?php
require_once("flib/AFModel.php");
class FishCategory_model extends AFModel
{	
	public $name="FishCategory";
	protected $tableName = "m_fishcategory";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array();	// N - numeric, S - string

	
}

?>