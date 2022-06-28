<?php
class HealthCertificate
{  
	/****************************************************************
	This class deals with all the operations relating to Health Certificate
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function HealthCertificate(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Find the Max value of PO
	function maxValueSO()
	{
		$qry = "select max(so) from t_health_certificate";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	# Insert Rec
	function addHealthCetificate($selLanguage, $consignorName, $consignorAddress, $consignorPostalCode, $consignorTelNo, $consigneeName, $consigneeAddress, $consigneePostalCode, $consigneeTelNo, $originCompanyName, $originCompanyAddress, $originCompanyPostalCode, $originCompanyTelNo, $isoCode, $regionOfOrigin, $originCode, $destinationCountry, $approvalNumber, $departureDate, $identification, $entryBPEU, $commodityDesciption, $commodityCode, $netWt, $grWt, $noOfPackage, $containerNo, $sealNo, $typeOfPackaging, $species, $natureOfCommodity, $destinationIsoCode, $transportType, $proTempType, $humanConsumption, $admissionEU, $userId)
	{		
		$qry = "insert into t_health_certificate (`language`, `consignor_name`, `consignor_addr`, `consignor_po`, `consignor_telno`, `consignee_name`, `consignee_addr`, `consignee_po`, `consignee_telno`, `origin_cname`, `origin_caddr`, `origin_cpo`, `origin_ctelno`, `iso_code`, `region_of_origin`, `origin_code`, `destination_country`, `approval_no`, `departure_date`, `identification`, `entry_bp_eu`, `commodity_descr`, `commodity_code`, `net_wt`, `gr_wt`, `no_of_package`, `container_no`, `seal_no`, `type_of_packaging`, `species`, `nature_of_commodity`, `dest_iso_code`, `transport_type`, `product_temp_type`, `human_consum`, `admission_eu`, `created`,  `createdby`) values('$selLanguage', '$consignorName', '$consignorAddress', '$consignorPostalCode', '$consignorTelNo', '$consigneeName', '$consigneeAddress', '$consigneePostalCode', '$consigneeTelNo', '$originCompanyName', '$originCompanyAddress', '$originCompanyPostalCode', '$originCompanyTelNo', '$isoCode', '$regionOfOrigin', '$originCode', '$destinationCountry', '$approvalNumber', '$departureDate', '$identification', '$entryBPEU', '$commodityDesciption', '$commodityCode', '$netWt', '$grWt', '$noOfPackage', '$containerNo', '$sealNo', '$typeOfPackaging', '$species', '$natureOfCommodity', '$destinationIsoCode', '$transportType', '$proTempType', '$humanConsumption', '$admissionEU', NOW(), '$userId')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select id, `language`, `consignee_name`, `created` from t_health_certificate where created>='$fromDate' and created<='$tillDate' order by consignee_name asc limit $offset, $limit";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Returns all Recs for a Date Range
	function fetchAllDateRangeRecords($fromDate, $tillDate)
	{
		$qry = " select id, `language`, `consignee_name`, `created` from t_health_certificate where created>='$fromDate' and created<='$tillDate' order by consignee_name asc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Sales Order
	function fetchAllRecords()
	{
		$qry = "select id, `language`, `consignee_name`, `created` from t_health_certificate order by consignee_name asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Sales Order based on Sales Order id 
	function find($selId)
	{
		$qry = "select `id`, `language`, `consignor_name`, `consignor_addr`, `consignor_po`, `consignor_telno`, `consignee_name`, `consignee_addr`, `consignee_po`, `consignee_telno`, `origin_cname`, `origin_caddr`, `origin_cpo`, `origin_ctelno`, `iso_code`, `region_of_origin`, `origin_code`, `destination_country`, `approval_no`, `departure_date`, `identification`, `entry_bp_eu`, `commodity_descr`, `commodity_code`, `net_wt`, `gr_wt`, `no_of_package`, `container_no`, `seal_no`, `type_of_packaging`, `species`, `nature_of_commodity`, `dest_iso_code`,   `transport_type`, `product_temp_type`, `human_consum`, `admission_eu`  from t_health_certificate where id=$selId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update  a Rec
	function updateHealthCertificate($healthCertificateRecId, $selLanguage, $consignorName, $consignorAddress, $consignorPostalCode, $consignorTelNo, $consigneeName, $consigneeAddress, $consigneePostalCode, $consigneeTelNo, $originCompanyName, $originCompanyAddress, $originCompanyPostalCode, $originCompanyTelNo, $isoCode, $regionOfOrigin, $originCode, $destinationCountry, $approvalNumber, $departureDate, $identification, $entryBPEU, $commodityDesciption, $commodityCode, $netWt, $grWt, $noOfPackage, $containerNo, $sealNo, $typeOfPackaging, $species, $natureOfCommodity, $destinationIsoCode, $transportType, $proTempType, $humanConsumption, $admissionEU)
	{
		$qry = "update t_health_certificate set `language`='$selLanguage', `consignor_name`='$consignorName', `consignor_addr`='$consignorAddress', `consignor_po`='$consignorPostalCode', `consignor_telno`='$consignorTelNo', `consignee_name`='$consigneeName', `consignee_addr`='$consigneeAddress', `consignee_po`='$consigneePostalCode', `consignee_telno`='$consigneeTelNo', `origin_cname`='$originCompanyName', `origin_caddr`='$originCompanyAddress', `origin_cpo`='$originCompanyPostalCode', `origin_ctelno`='$originCompanyTelNo', `iso_code`='$isoCode', `region_of_origin`='$regionOfOrigin', `origin_code`='$originCode', `destination_country`='$destinationCountry', `approval_no`='$approvalNumber', `departure_date`='$departureDate', `identification`='$identification', `entry_bp_eu`='$entryBPEU', `commodity_descr`='$commodityDesciption', `commodity_code`='$commodityCode', `net_wt`='$netWt', `gr_wt`='$grWt', `no_of_package`='$noOfPackage', `container_no`='$containerNo', `seal_no`='$sealNo', `type_of_packaging`='$typeOfPackaging', `species`='$species', `nature_of_commodity`='$natureOfCommodity', `dest_iso_code`='$destinationIsoCode', `transport_type`='$transportType', `product_temp_type`='$proTempType', `human_consum`='$humanConsumption', `admission_eu`='$admissionEU' where id='$healthCertificateRecId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	
	# Delete a Rec
	function deleteHealthCertificate($healthCertificateRecId)
	{
		$qry = " delete from t_health_certificate where id=$healthCertificateRecId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#For Printing Purpose
	function getHealthCertificateRecords()
	{
		$qry = "select id, `consignee_name`, created from t_health_certificate order by created desc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}






	# ----------------------------
	# Check SO Number Exist
	# ----------------------------
	function checkSONumberExist($soId)
	{
		$qry = " select id from t_health_certificate where so='$soId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	
	
	
}
?>