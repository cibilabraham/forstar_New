<?php
require_once("flib/AFModel.php");

class CommonReason_model extends AFModel
{
	protected $name = "CommonReason";
	protected $tableName = "m_common_reason";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array("created" => "N");

	# Check Common reason using anyother section
	function commonReasonExist($commonReasonId)
	{
		$qry = "select id from t_distributor_ac where reason_id='$commonReasonId'";
		$recs = $this->queryAll($qry);	
		return (sizeof($recs)>0)?true:false;
	}

	
	
	function updateconfirmcommonReason($confirmid)
	{
	$qry="update ".$this->tableName." set active=1 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}

	function updaterlconfirmcommonReason($confirmid)
	{
	$qry="update ".$this->tableName." set active=0 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}
}

?>