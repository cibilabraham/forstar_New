<?php
require_once("flib/AFModel.php");

class DAMSetting_model extends AFModel
{
	protected $name = "DAMSettingEntry";
	protected $tableName = "m_dam_setting_entry";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array("created" => "N");
}

?>