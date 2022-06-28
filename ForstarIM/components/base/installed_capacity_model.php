<?php
require_once("flib/AFModel.php");
class installed_capacity_model extends AFModel
{
	protected $name="InstalledCapacity";	
	protected $tableName = "m_installed_capacity";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array( "created" => "N" );	// N - numeric, S - string

	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from m_installed_capacity where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";

		$result = $this->queryAll($qry);
		return (sizeof($result)>0)?true:false;
	}

	function fetchAllMonitoringParams($installedCapacityId)
	{
		$qry = "select smp.*, mp.* from m_set_monitoring_param smp left join m_monitoring_parameters mp on smp.monitoring_parameter_id=mp.id where smp.installed_capacity_id='$installedCapacityId' order by smp.id asc ";	
		//echo $qry;
		return $this->queryAll($qry);
	}

	function updateconfirminstalledcapacity($confirmid)
	{
	$qry="update ".$this->tableName." set active=1 where id='$confirmid'";
	echo $qry;
	$result = $this->query($qry);

	}

	function updaterlconfirminstalledcapacity($confirmid)
	{
	$qry="update ".$this->tableName." set active=0 where id='$confirmid'";
	echo $qry;
	$result = $this->query($qry);

	}
}

?>