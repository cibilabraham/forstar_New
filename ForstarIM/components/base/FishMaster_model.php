<?php
require_once("flib/AFModel.php");
class FishMaster_model extends AFModel
{	
	public $name="FishMaster";
	protected $tableName = "m_fish";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array();	// N - numeric, S - string

	
}

?>