<?php
class ExporterMaster
{  
	/****************************************************************
	This class deals with all the operations relating to loading port
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ExporterMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	
	/*function addExporter($name,$address,$place,$pinCode,$country,$telNo,$faxNo,$alphaCode,$displayName,$userId)
	{
		$qry	= "insert into m_exporter(name,address,place,pin,country,telno,	faxno,created,created_by,alpha_code,display_name) values('".$name."','".$address."','".$place."','".$pinCode."','".$country."','".$telNo."','".$faxNo."',NOW(),'".$userId."','".$alphaCode."','".$displayName."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}*/
	function addExporter($name,$displayName,$iecCode,$userId)
	{
		$qry	= "insert into m_exporter(name,created,created_by,display_name,iec_code) values('".$name."',NOW(),'".$userId."','".$displayName."','".$iecCode."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function addExporterUnit($exporter_id,$monitoringParamId,$headName)
	{
		$qry	= "insert into m_exporter_unit(unitno,unitcode,exporterid) values('".$monitoringParamId."','".$headName."','".$exporter_id."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

		# Returns all port of loading (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,name,address,place,pin,country,telno,faxno,alpha_code,default_row,display_name,active from m_exporter order by name asc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all port of loading
	function fetchAllRecords()
	{
		
		$qry	= "select id,name,address,place,pin,country,telno,faxno,alpha_code,default_row,display_name from m_exporter order by name asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	# Get port of loading based on id 
	function find($ExporterMasterId)
	{
		$qry	= "select id,name,address,place,pin,country,telno,faxno,alpha_code,default_row,display_name,	iec_code from  m_exporter where id=$ExporterMasterId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get port of loading based on id 
	function findAll()
	{
		$qry	= "select id,name,address,place,pin,country,telno,faxno,alpha_code,default_row,display_name,iec_code from  m_exporter where active='1'";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	
	function findExporterUnit($ExporterMasterId)
	{
		$qry	= "select id,unitno,unitcode from  m_exporter_unit where exporterid=$ExporterMasterId";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	
	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from m_exporter where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return (sizeof($result)>0)?true:false;
	}
	
	
	/*function updateExporterMaster($ExporterMasterId,$name,$address,$place,$pinCode,$country,$telNo,$faxNo,$alphaCode,$displayName)
	{
		//$qry	= "update m_exporter set name='$name' where id=$ExporterMasterId";
		$qry	= "update m_exporter set name='$name',address='$address',place='$place',pin='$pinCode',country='$country',telno='$telNo',faxno='$faxNo',alpha_code='$alphaCode',display_name='$displayName' where id=$ExporterMasterId";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}*/

	function updateExporterMaster($ExporterMasterId,$name,$displayName,$iecCode)
	{
		//$qry	= "update m_exporter set name='$name' where id=$ExporterMasterId";
		$qry	= "update m_exporter set name='$name',display_name='$displayName',iec_code='$iecCode' where id=$ExporterMasterId";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateExporterUnit($monitoringParamEntryId,$monitoringParamId,$headName)
	{
		$qry	= "update m_exporter_unit set unitno='$monitoringParamId',unitcode='$headName' where id=$monitoringParamEntryId";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	function updateconfirmExporterMaster($ExporterMasterId)
	{
	$qry="update m_exporter set active=1 where id=$ExporterMasterId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;

	}
	
	function updaterlconfirmExporterMaster($ExporterMasterId)
	{
	$qry="update m_exporter set active=0 where id=$ExporterMasterId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;
	}
	
	# Delete port of loading
	function deleteExporterMaster($ExporterMasterId)
	{
		$qry	= " delete from  m_exporter where id=$ExporterMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	function delExporterExporterUnit($ExporterMasterId)
	{
		$qry	= " delete from  m_exporter_unit where exporterid=$ExporterMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	function delExporterUnit($exporterunitId)
	{
		$qry	= " delete from  m_exporter_unit where id=$exporterunitId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	# -----------------------------------------------------
	# Checking loading port Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function exporterMasterRecInUse($ExporterMasterId)
	{	
		$qry = "select id from t_invoice_main where exporter_id='$ExporterMasterId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}
	function fetchAllUnitCodesdis($exporterid)
	{
		//$qry = "select smp.*, mp.* from m_set_monitoring_param smp left join m_monitoring_parameters mp on smp.monitoring_parameter_id=mp.id where smp.installed_capacity_id='$installedCapacityId' order by smp.id asc ";	

		$qry = "select meu.*, me.*,mp.*,mbc.* from m_plant mp left join m_exporter_unit meu on mp.id=meu.unitno left join m_exporter me on meu.exporterid=me.id left join m_billing_company mbc on mbc.id=me.name   where meu.exporterid='$exporterid' and mp.active=1 order by meu.id asc ";
		//ECHO $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		//printr($result);
		return $result;
		
		//return (sizeof($result)>0)?true:false;;
	}

	function updateExporterMasterDefaultRow($exporterId)
	{
		$qry	= "update m_exporter set default_row='Y' where id='$exporterId'";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	function getUnitAlphaCode($unitId,$exporterId)
	{
		

		//$qry = "select unitcode from m_exporter_unit where id='$exporterId'";
		$qry = "select unitcode from m_exporter_unit where exporterid='$exporterId' and unitno='$unitId'";
		//echo $qry;
			$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getUnitExporterAlphaCode($unitId,$exporterId)
	{
		$qry = "select unitcode from m_exporter_unit where exporterid='$exporterId' and unitno='$unitId'";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	 function getExporterDetails($exporterId)
	{
		
		$qry = "select * from  m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.id='$exporterId'";
		
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		
		$displayAddress = "";
		if (sizeof($result)>0) {

			$companyName	= $result[1];
			$address		= $result[2];
			$place			= $result[3];
			$pinCode		= $result[4];
			$country		= $result[5];
			$telNo			= $result[6];
			$faxNo			= $result[7];

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
		$qry = "select alpha_code from m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.id='$exporterId'";
		if ($whr!="") $qry .= " where ".$whr;
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getDefaultExporter()
	{
		$qry = "select id from m_exporter where default_row='Y'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:'';		
	}
	function getExporterName($exporterId)
	{
		$qry = "select name from  m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.id='$exporterId'";
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:'';
	}
	function getExporterNameActive()
	{
		$qry = "select me.id,mbc.name,me.display_name,me.default_row from  m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.active='1' order by mbc.name asc";
		$result=$this->databaseConnect->getRecords($qry);
		return $result;
	}
}

