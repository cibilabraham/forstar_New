<?php
require_once("flib/AFModel.php");

class SetExporterUnit_model extends AFModel
{
	protected $name = "SetExporterUnit";
	protected $tableName = "m_exporter_unit";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array( "created" => "N" );	// N - numeric, S - string

	# Return fetch all records Query
	function fetchAllRecQry()
	{
		$qry = "select smp.id, smp.installed_capacity_id as installedcapacityid, smp.head_name as headname, smp.monitoring_parameter_id as monitorparamid, smp.start, smp.stop, smp.monitoring_interval as monitorinterval, smp.description, mic.name as machinery, mmp.name as parametername from m_set_monitoring_param smp left join m_installed_capacity mic on mic.id=smp.installed_capacity_id left join m_monitoring_parameters mmp on mmp.id=smp.monitoring_parameter_id order by mic.name asc";
		return $qry;
	}

	# get Monitoring params

	function filterMontoringParams($installedCapacityId)
	{
		$qry = "select smp.id, smp.installed_capacity_id as installedcapacityid, smp.head_name as headname, smp.monitoring_parameter_id as monitorparamid, smp.start, smp.stop, smp.monitoring_interval as monitorinterval, smp.description, mic.name as machinery, mmp.name as parametername, msu.name as stkunit, smp.seq_flag, smp.seq_mparam_id from m_set_monitoring_param smp left join m_installed_capacity mic on mic.id=smp.installed_capacity_id left join m_monitoring_parameters mmp on mmp.id=smp.monitoring_parameter_id left join m_stock_unit msu on msu.id=mmp.unit_id where smp.installed_capacity_id='$installedCapacityId' order by smp.id asc";

		return $this->queryAll($qry);
	}
}

?>