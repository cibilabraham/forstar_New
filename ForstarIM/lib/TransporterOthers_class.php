<?php
class TransporterOthers
{  
	/****************************************************************
	This class deals with all the operations relating to Transporter Others
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function TransporterOthers(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add
	function addTransporterOthersRec($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId)
	{	
		$qry	= "insert into m_transporter_others (fov_charge, docket_charge, service_tax, octroi_service_charge, created, createdby) values('$fovCharge', '$docketCharge', '$serviceTax', '$octroiServiceCharge', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get Record
	function find()
	{
		$qry	= " select id, fov_charge, docket_charge, service_tax, octroi_service_charge from m_transporter_others where id is not null ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateTransporterOthersRec($transporterOthersRecId, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId)
	{
		$qry	= " update m_transporter_others set fov_charge='$fovCharge', docket_charge='$docketCharge', service_tax='$serviceTax', octroi_service_charge='$octroiServiceCharge', modified=NOW(), modifiedby='$userId' where id=$transporterOthersRecId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
}