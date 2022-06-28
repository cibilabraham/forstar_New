<?php
class TransporterMaster
{
	/****************************************************************
	This class deals with all the operations relating to Retail Counter Master
	*****************************************************************/
	var $databaseConnect;
	
	// Constructor, which will create a db instance for this class
	function TransporterMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addTransporter($code, $name, $address, $pinCode, $telNo, $faxNo, $mobNo, $serviceTaxNo, $cUserId, $billNoRequired)
	{
		$qry = "insert into m_transporter (code, name, address, pin_code, tel_no, mob_no, fax_no, service_tax_no, created, createdby, bill_required) values('$code', '$name', '$address', '$pinCode', '$telNo', '$faxNo', '$mobNo', '$serviceTaxNo', NOW(),'$cUserId', '$billNoRequired')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$whr = " a.id is not null ";
		$orderBy	= " a.name asc ";
		$limit		= " $offset,$limit";
		$qry = " select a.id, a.code, a.name, a.address, a.pin_code, a.tel_no, a.fax_no, a.mob_no, a.service_tax_no, a.bill_required,a.active from m_transporter a ";
		if ($whr!="")		$qry .= " where".$whr;
		if ($orderBy!="")	$qry .= " order by".$orderBy;
		if ($limit!="")		$qry .= " limit".$limit;		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		//$whr 		= " a.id is not null ";	
		$orderBy	= " a.name asc ";
		$qry = " select a.id, a.code, a.name, a.address, a.pin_code, a.tel_no, a.fax_no, a.mob_no,  a.service_tax_no, a.bill_required,a.active from m_transporter a ";
		if ($whr!="") 		$qry .= " where".$whr;
		if ($orderBy!="") 	$qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveTransporter()
	{
		//$whr 		= " a.id is not null ";	
		$whr=" a.active=1";
		$orderBy	= " a.name asc ";
		$qry = " select a.id, a.code, a.name, a.address, a.pin_code, a.tel_no, a.fax_no, a.mob_no,  a.service_tax_no, a.bill_required from m_transporter a ";
		if ($whr!="") 		$qry .= " where".$whr;
		if ($orderBy!="") 	$qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Rec based on id 
	function find($transporterId)
	{
		$qry = "select id, code, name, address, pin_code, tel_no, fax_no, mob_no, service_tax_no, bill_required from m_transporter where id=$transporterId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  record
	function updateTransporter($transporterId, $name, $address, $pinCode, $telNo, $faxNo, $mobNo, $serviceTaxNo, $billNoRequired)
	{
		$qry = "update m_transporter set name='$name', address='$address', pin_code='$pinCode', tel_no='$telNo', fax_no='$faxNo', mob_no='$mobNo', service_tax_no='$serviceTaxNo', bill_required='$billNoRequired' where id='$transporterId' ";		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();		
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a record
	function deleteTransporter($transporterId)
	{
		$qry = "delete from m_transporter where id=$transporterId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	#fOR SELECTING THE SELECTED Working Area
	function fetchSelectedAreaRecords($editId, $selCityId)
	{
		$qry 	= "select a.id, a.code, a. name, b.id, b.area_id from m_area a left join m_transporter_area b on a.id=b.area_id and b.retail_counter_id='$editId' where a.city_id='$selCityId' order by b.id desc, a.code asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	

	/**
	* Checking Transporter Id is using in another screen;
	*/
	function transporterInUse($transporterId)
	{		
		$qry = " select id from (
				select a.id from m_transporter_ratelist a where a.transporter_id='$transporterId'
			union
				select a1.id from m_transporter_other_charge a1 where a1.transporter_id='$transporterId'	
			union
				select a2.id from m_transporter_rate a2 where a2.transporter_id='$transporterId'
			union
				select a3.id from m_transporter_status a3 where a3.transporter_id='$transporterId'
			union
				select a4.id from t_salesorder a4 where a4.transporter_id='$transporterId'
			union
				select a5.id from t_salesorder a5 where a5.transporter_id='$transporterId'	
			) as X group by id ";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Check for duplicate entry
	function chkDuplicateEntry($retailCounterName, $transporterId)
	{
		if ($transporterId!="") $updateQry = " and id!=$transporterId";
		else $updateQry = "";
		$qry = " select id from m_transporter where name = '$retailCounterName' $updateQry";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	function updateTransporterconfirm($transporterId)
	{
	$qry	= "update m_transporter set active='1' where id=$transporterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateTransporterReleaseconfirm($transporterId)
	{
		$qry	= "update m_transporter set active='0' where id=$transporterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>