<?php
require_once("flib/AFModel.php");

class ExporterMaster_model extends AFModel
{
	protected $name = "Exporter";
	protected $tableName = "m_exporter";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array("created" => "N");

	# Check Export using any other section
	function ExporterExist($exporterId)
	{
		$qry = "select id from t_invoice_main where exporter_id='$exporterId'";
		$recs = $this->queryAll($qry);	
		return (sizeof($recs)>0)?true:false;
	}

	function updateDefaultRec($exporterId)
	{
		$result1 = false;
		$qry = "update m_exporter set default_row='N'";
		$result = $this->updateRecord($qry);

		if ($result) {
			$qry1 = "update m_exporter set default_row='Y' where id='$exporterId'";
			$result1 = $this->updateRecord($qry1);
		}

		return $result1;
	}

	function getExporterDetails($exporterId=null, $incContactNo=null)
	{		
		if ($exporterId!="") $whr = " id='$exporterId' ";
		else $whr = " default_row='Y' "	;		

		$qry = "select * from ".$this->tableName." ";
		if ($whr!="") $qry .= " where ".$whr;

		//echo $qry;
		$result = $this->query($qry);
		
		$displayAddress = "";
		if (sizeof($result)>0) {

			$companyName	= $result->name;
			$address		= $result->address;
			$place			= $result->place;
			$pinCode		= $result->pin;
			$country		= $result->country;
			$telNo			= $result->telno;
			$faxNo			= $result->faxno;

			$displayAddress = strtoupper($companyName)."<br/>".strtoupper($address)."<br>".strtoupper($place)." - ".$pinCode." (".strtoupper($country).") ";
			
			$displayTelNo = "";	
			if ($incContactNo=="") {
				if ($telNo)		$displayTelNo	= "<br>Tel:&nbsp;".$telNo;
				if ($faxNo)		$displayTelNo	.= ", Fax No:&nbsp;".$faxNo;
				$displayAddress .= $displayTelNo;
			}

			$displayAddress = nl2br($displayAddress);
		}
		return $displayAddress;
	}

	function getExporterAlphaCode($exporterId)
	{
		if ($exporterId!="") $whr = " id='$exporterId' ";
		else $whr = " default_row='Y' "	;		

		$qry = "select alpha_code from ".$this->tableName." ";
		if ($whr!="") $qry .= " where ".$whr;

		//echo $qry;
		$result = $this->query($qry);

		return $result->alpha_code;
	}


	function getUnitAlphaCode($unitId,$exporterId)
	{
		

		//$qry = "select unitcode from m_exporter_unit where id='$exporterId'";
		$qry = "select unitcode from m_exporter_unit where exporterid='$exporterId' and unitno='$unitId'";
		
		//echo $qry;
		$result = $this->query($qry);

		return $result->unitcode;
	}


	function getExporterName($exporterId)
	{
		if ($exporterId!="") $whr = " id='$exporterId' ";
		else $whr = " default_row='Y' "	;		

		$qry = "select name from ".$this->tableName." ";
		if ($whr!="") $qry .= " where ".$whr;

		//echo $qry;
		$result = $this->query($qry);

		return $result->name;
	}

		function fetchAllUnitCodes($exporterid)
	{
		//$qry = "select smp.*, mp.* from m_set_monitoring_param smp left join m_monitoring_parameters mp on smp.monitoring_parameter_id=mp.id where smp.installed_capacity_id='$installedCapacityId' order by smp.id asc ";	

		$qry = "select meu.*, me.*,mp.* from m_plant mp left join m_exporter_unit meu on mp.no=meu.unitno left join m_exporter me on meu.exporterid=me.id where meu.exporterid='$exporterid' and mp.active=1 order by meu.id asc ";
		return $this->queryAll($qry);
	}

		function fetchAllUnitCodesdis($exporterid)
	{
		//$qry = "select smp.*, mp.* from m_set_monitoring_param smp left join m_monitoring_parameters mp on smp.monitoring_parameter_id=mp.id where smp.installed_capacity_id='$installedCapacityId' order by smp.id asc ";	

		$qry = "select meu.*, me.*,mp.* from m_plant mp left join m_exporter_unit meu on mp.id=meu.unitno left join m_exporter me on meu.exporterid=me.id where meu.exporterid='$exporterid' and mp.active=1 order by meu.id asc ";
		return $this->queryAll($qry);
	}

	function updateconfirmExporterDetails($confirmid)
	{
	$qry="update ".$this->tableName." set active=1 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}

	function updaterlconfirmExporterDetails($confirmid)
	{
	$qry="update ".$this->tableName." set active=0 where id='$confirmid'";
	//echo $qry;
	$result = $this->query($qry);

	}

}

?>