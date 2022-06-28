<?php
require_once("flib/AFModel.php");
class display_record_model extends AFModel
{
	protected $tableName = "s_displayrecord";
	protected $pk = 'id';	// Primary key field	

	# Get Record
	function getDisplayLimit()
	{
		$qry = "select id, no_records as display_limit from s_displayrecord where id is not null";
		//echo $qry;
		$result = $this->query($qry);
		//print_r($result);
		return $result->display_limit;
	}
	

}
