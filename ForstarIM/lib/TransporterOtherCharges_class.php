<?php
class TransporterOtherCharges
{
	/****************************************************************
	This class deals with all the operations relating to Transporter Other Charges
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function TransporterOtherCharges(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addTransporterOtherCharge($selTransporter, $transporterRateListId, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId, $odaCharge, $surcharge)
	{
		$qry = "insert into m_transporter_other_charge (transporter_id, rate_list_id, fov_charge, docket_charge, service_tax, octroi_service_charge, created, createdby, oda_charge, surcharge) values('$selTransporter', '$transporterRateListId', '$fovCharge', '$docketCharge', '$serviceTax', '$octroiServiceCharge', Now(), '$userId', '$odaCharge', '$surcharge')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $transporterFilterId, $transporterRateListFilterId)
	{		
		$cDate = date("Y-m-d");

		$whr = " a.transporter_id=b.id ";
			
		if ($transporterFilterId!="") $whr .= " and a.transporter_id=".$transporterFilterId;
		if ($transporterRateListFilterId!="") $whr .= " and a.rate_list_id=".$transporterRateListFilterId;

		if ($transporterRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_transporter_ratelist f";
		} else {
			$whr .= "";
			$tableUpdate = "";
		}
		
		$orderBy 	= " b.name asc";
		$limit 		= " $offset,$limit";


		$qry = "select a.id, a.transporter_id, a.rate_list_id, b.name, a.fov_charge, a.docket_charge, a.service_tax, a.octroi_service_charge, a.oda_charge, a.surcharge,a.active from m_transporter_other_charge a, m_transporter b $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;			
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords($transporterFilterId, $transporterRateListFilterId)
	{
		$cDate = date("Y-m-d");

		$whr = " a.transporter_id=b.id ";
			
		if ($transporterFilterId!="") $whr .= " and a.transporter_id=".$transporterFilterId;
		if ($transporterRateListFilterId!="") $whr .= " and a.rate_list_id=".$transporterRateListFilterId;

		if ($transporterRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_transporter_ratelist f";
		} else {
			$whr .= "";
			$tableUpdate = "";
		}
		
		$orderBy 	= " b.name asc";
		
		$qry = "select a.id, a.transporter_id, a.rate_list_id, b.name, a.fov_charge, a.docket_charge, a.service_tax, a.octroi_service_charge, a.oda_charge from m_transporter_other_charge a, m_transporter b $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;			
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	# Get a Record based on id
	function find($transporterRateId)
	{
		$qry = "select id, transporter_id, rate_list_id, fov_charge, docket_charge, service_tax, octroi_service_charge, oda_charge, surcharge from m_transporter_other_charge where id='$transporterRateId'";
		return $this->databaseConnect->getRecord($qry);
	}


	# Update  a  Record
	function updateTransporterRate($transporterRateId, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surcharge)
	{
		$qry = " update m_transporter_other_charge set fov_charge='$fovCharge', docket_charge='$docketCharge', service_tax='$serviceTax', octroi_service_charge='$octroiServiceCharge', oda_charge='$odaCharge', surcharge='$surcharge' where id=$transporterRateId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	# Delete a Record
	function deleteTransporterRate($transporterRateId)
	{
		$qry =	" delete from m_transporter_other_charge where id=$transporterRateId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Checking Entry Exist
	function checkEntryExist($transporterId, $transporterRateList, $currentId)
	{
		if ($currentId) $updateQry = " and id!=$currentId";
		else $updateQry = "";

		$qry = " select id from m_transporter_other_charge where transporter_id='$transporterId' and rate_list_id='$transporterRateList' $updateQry ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	/**	
	* Checking functions using in another screen	
	*/
	function trptrOCRecInUse($transporterOtherChargeId)
	{	
		/*	
		$qry = " select id from (
				select a.id as id from t_salesorder a where a.trans_oc_rate_list_id='$transporterOtherChargeId'				
			) as X group by id ";
		*/
		$rec = $this->find($transporterOtherChargeId);
		$rateListId = $rec[2];
		if ($rateListId) {
			$qry = "select a.id as id from t_salesorder a where a.trans_oc_rate_list_id='$rateListId'";
			//echo $qry."<br>";
			$result	= $this->databaseConnect->getRecords($qry);
		}
		return (sizeof($result)>0)?true:false;		
	}

	function updatetransporterOtherChargesconfirm($transporterOtherChargeId)
	{
	$qry	= "update m_transporter_other_charge set active='1' where id=$transporterOtherChargeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatetransporterOtherChargesReleaseconfirm($transporterOtherChargeId)
	{
		$qry	= "update m_transporter_other_charge set active='0' where id=$transporterOtherChargeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>