<?php
require_once("flib/AFModel.php");

class DAMSetting_model extends AFModel
{
	protected $name = "DAMSetting";
	protected $tableName = "m_dam_setting";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array("created" => "N");

	# Check Common reason using anyother section
	function damSettingRecExist($damMainId)
	{
		$qry = "select tdacc.id from t_dailyactivitychart_entry tdacc join m_dam_setting_entry mdse on tdacc.dam_set_entry_id=mdse.id where mdse.entry_id='$damMainId'";
		//echo "<br>$qry";
		$recs = $this->queryAll($qry);	
		return (sizeof($recs)>0)?true:false;
	}

	function updateconfirmDAMSetting($confirmid)
	{
	$qry="update ".$this->tableName." set active=1 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}

	function updaterlconfirmDAMSetting($confirmid)
	{
	$qry="update ".$this->tableName." set active=0 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}
}

?>