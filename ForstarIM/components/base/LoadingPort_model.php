<?php
require_once("flib/AFModel.php");

class LoadingPort_model extends AFModel
{
	protected $name = "LoadingPort";
	protected $tableName = "m_loading_port";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array("created" => "N");


	# Check loading port using any other section
	function loadingPortExist($loadingPortId)
	{
		$qry = "select id from t_invoice_main where loading_port='$loadingPortId'";
		$recs = $this->queryAll($qry);	
		return (sizeof($recs)>0)?true:false;
	}

	function updateconfirmloadingPort($confirmid)
	{
	$qry="update ".$this->tableName." set active=1 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}

	function updaterlconfirmloadingPort($confirmid)
	{
	$qry="update ".$this->tableName." set active=0 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}
}

?>