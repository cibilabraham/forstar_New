<?php
class PHTMonitoring
{  
	/****************************************************************
	This class deals with all the operations relating to PHT Monitoring 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PHTMonitoring(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	# Filter lots List
	function getLots($date,$id)
	{ 
 		//$qry="select a.id,a.vehicle_No,b.vehicle_number from t_rmprocurmentorderentries a join m_vehicle_master b on a.vehicle_No=b.id where rmProcurmentOrderId='$procurmentGatePassId' order by vehicle_number asc";
		//$qry="select id,new_lot_Id from t_unittransfer where active='1' and created_on='$date'";
		//$qry="select a.id,a.new_lot_Id from t_unittransfer a join t_rmreceiptgatepass b on a.new_lot_Id=b.lot_Id where a.active='1' and b.date_Of_Entry='$date'";
		//echo $qry;
		//$qry="select id,lot_Id from t_rmreceiptgatepass where date_Of_Entry='$date'";
		// $qry="select a.id,a.lot_Id from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id where a.date_Of_Entry='$date'";
		
		$qry="select id,concat(alpha_character,rm_lotid) from  t_manage_rm_lotid  where created_on='$date' and status='0' and id not in (select lot_id from t_phtmonitoring) or id='$id'";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	# Filter supplier group List
	function getSupplierGroup($supplier)
	{ 
 		//$qry="select a.id,a.vehicle_No,b.vehicle_number from t_rmprocurmentorderentries a join m_vehicle_master b on a.vehicle_No=b.id where rmProcurmentOrderId='$procurmentGatePassId' order by vehicle_number asc";
		$qry="select a.id,a.supplier_group_name from m_supplier_group a join m_supplier_group_details b on b.supplier_group_name_id=a.id where supplier_name='$supplier'";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	# get specious
	function getSpecious($lotId)
	{ 
 		
		//$qry="select a.id,a.supplier_group_name from m_supplier_group a join m_supplier_group_details b on b.supplier_group_name_id=a.id where supplier_name='$supplier'";
		//$qry="select product_species from weighment_data_sheet where rm_lot_id='$lotId'";
		$qry="select c.name from weighment_data_sheet a left join t_weightment_data_entries b on b.weightment_data_sheet_id=a.id 
		left join m_fish c on c.id=b.product_species
		where a.rm_lot_id='$lotId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
		//return (sizeof($rec)>0)?$rec[0]:0;
		
	}
	function getSupplier($lotId)
	{ 
 		
		//$qry="select a.id,a.supplier_group_name from m_supplier_group a join m_supplier_group_details b on b.supplier_group_name_id=a.id where supplier_name='$supplier'";
		//$qry="select product_species from weighment_data_sheet where rm_lot_id='$lotId'";
		$qry="select c.id,c.name from weighment_data_sheet a left join t_weightment_data_entries b on b.weightment_data_sheet_id=a.id 
		left join supplier c on c.id=b.supplier_name
		where a.rm_lot_id='$lotId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
		//return (sizeof($rec)>0)?$rec[0]:0;
		
	}
	function getSupplierGroupNm($supplier)
	{
	//echo $supplier;
		//$qry="select b.id,b.supplier_group_name from weighment_data_sheet a left join m_supplier_group b on b.id=a.supplier_group where a.rm_lot_id='$rmLotId' ";
		$qry	= "select a.id,a.supplier_group_name from m_supplier_group a join m_supplier_group_details b on a.id=b.supplier_group_name_id where b.supplier_name='$supplier'";
		//$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty from t_phtMonitoring where id=$phtMonitoringId";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		//return $this->databaseConnect->getRecord($qry);
	}
	
	function getPhtCertificate($rmLotId)
	{
	//echo $supplier;
		//$qry="select b.id,b.supplier_group_name from weighment_data_sheet a left join m_supplier_group b on b.id=a.supplier_group where a.rm_lot_id='$rmLotId' ";
		$qry	= "select c.id,c.PHTCertificateNo from weighment_data_sheet a left join t_weightment_data_entries b on b.weightment_data_sheet_id=a.id left join t_phtcertificate c on c.species=b.product_species where a.rm_lot_id='$rmLotId'";
		//$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty from t_phtMonitoring where id=$phtMonitoringId";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		//return $this->databaseConnect->getRecord($qry);
	
		
	}
	
	
	
	function fetchAllRecordsRMLotIdVal()
	{
		//$qry	=	"select id, new_lot_Id from t_unittransfer where active='1' order by new_lot_Id asc";
		$qry="select id,new_lot_Id from t_unittransfer where  active='1' order by new_lot_Id asc ";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# get supply qty
	function getSupplyQty($rmLotId)
	{ 
 		
		//$qry="select a.id,a.supplier_group_name from m_supplier_group a join m_supplier_group_details b on b.supplier_group_name_id=a.id where supplier_name='$supplier'";
		$qry="select total_quantity from weighment_data_sheet where rm_lot_id='$rmLotId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
		
	}
	
	# get Qty id
	function getQuantity($phtCetificateId)
	{ 
 		
		//$qry="select a.id,a.supplier_group_name from m_supplier_group a join m_supplier_group_details b on b.supplier_group_name_id=a.id where supplier_name='$supplier'";
		$qry="select pht_Qty from t_phtcertificate where id='$phtCetificateId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
		
	
	}
	
	# get pht Quantity
	function getPhtQty($qtyIdRecs)
	{ 
 		
		//$qry="select a.id,a.supplier_group_name from m_supplier_group a join m_supplier_group_details b on b.supplier_group_name_id=a.id where supplier_name='$supplier'";
		$qry="select pond_qty from m_pond_master where id='$qtyIdRecs'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
		
	
	}
	
	#Add a PHT Monitoring
	function addPHTMonitoring($date, $rmLotId, $supplier, $supplierGroupName, $specious,$supplyQty, $userId)
	{
		$qry	=	"insert into t_phtmonitoring (date_on, lot_id, supplier,supplier_group,species,supply_qty,created_on, created_by) values('".$date."','".$rmLotId."','".$supplier."','".$supplierGroupName."','".$specious."','".$supplyQty."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	
	
	// function addPHTMonitoring($date, $rmLotId, $supplier, $supplierGroupName, $specious,$supplyQty, $phtCertificateNo, $phtQty, $setOfQty, $balance, $userId)
	// {
		// $qry	=	"insert into t_phtMonitoring (date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty, created_on, created_by) values('".$date."','".$rmLotId."','".$supplier."','".$supplierGroupName."','".$specious."','".$supplyQty."','".$phtCertificateNo."','".$phtQty."','".$setOfQty."','".$balance."', Now(), '$userId')";

		////echo $qry;
		// $insertStatus	= $this->databaseConnect->insertRecord($qry);		
		// if ($insertStatus) $this->databaseConnect->commit();
		// else $this->databaseConnect->rollback();
		// return $insertStatus;
	// }
	
	#Add a PHT Monitoring Cetificate Quantity
	function addPhtCertificateQuantity($lastId,$phtCertificateNo,$phtQuantity,$setoffQuantity,$balanceQuantity)
	{
		$qry	=	"insert into t_phtmonitoring_quantity (pht_monitoring_id,pht_certificate_number, pht_quantity,setoff_quantity,balance_quantity) values('".$lastId."','".$phtCertificateNo."','".$phtQuantity."','".$setoffQuantity."','".$balanceQuantity."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	
	# Returns all PHT Monitoring
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id, date_on, lot_id, supplier,supplier_group,species,supply_qty from t_phtmonitoring where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc limit $offset,  $limit";
		
		//$qry	= "select id, date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty from t_phtMonitoring where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc limit $offset,  $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getCertificateQuantity($monitoringId)
	{
	$qry 	= "select a . * , b.PHTCertificateNo,c.lot_id FROM t_phtmonitoring_quantity a JOIN t_phtcertificate b ON a.pht_certificate_number = b.id left join t_phtmonitoring c on c.id=a.pht_monitoring_id where pht_monitoring_id='$monitoringId'";
		// $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getCertificateNo($certificateId)
	{		
		$qry 	= "select PHTCertificateNo from t_phtcertificate where id='$certificateId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getPhtNumber($certificateId)
	{		
		$qry 	= "select b.id,b.PHTCertificateNo from t_phtmonitoring_quantity  a left join t_phtcertificate b on b.id=a.pht_certificate_number where b.pht_monitoring_id='$certificateId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function find($phtMonitoringId)
	{	
		$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty from t_phtmonitoring where id=$phtMonitoringId";
		//$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty from t_phtMonitoring where id=$phtMonitoringId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	function updatePhtMonitoring($phtMonitoringId, $date,$rmLotId, $supplier,$supplierGroupName,$specious,$supplyQty)
	{
		$qry	= " update t_phtmonitoring set date_on='$date', lot_id='$rmLotId', supplier='$supplier', supplier_group='$supplierGroupName', species='$specious',supply_qty='$supplyQty' where id=$phtMonitoringId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function updatePhtCertificate($phtCertificateNo,$balanceQuantity)
	{
		$qry	= " update t_phtcertificate set pht_Qty='$balanceQuantity' where id=$phtCertificateNo";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function updatePhtCertificateQuantity($phtMonitoringId, $phtCertificateNo, $phtQuantity,$setoffQuantity,$balanceQuantity)
	{
		$qry	= " update t_phtmonitoring_quantity set pht_certificate_number='$phtCertificateNo', pht_quantity='$phtQuantity', setoff_quantity='$setoffQuantity', balance_quantity='$balanceQuantity' where id=$phtMonitoringId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	// function updatePhtMonitoring($phtMonitoringId, $date,$rmLotId, $supplier,$supplierGroupName,$specious,$supplyQty,$phtCertificateNo,$phtQty,$setOfQty,$balance)
	// {
		 // $qry	= " update t_phtMonitoring set date_on='$date', lot_id='$rmLotId', supplier='$supplier', supplier_group='$supplierGroupName', species='$specious',supply_qty='$supplyQty',pht_No='$phtCertificateNo',pht_Qty='$phtQty',set_off_Qty='$setOfQty',balance_Qty='$balance' where id=$phtMonitoringId";

		// $result	= $this->databaseConnect->updateRecord($qry);
		// if ($result) $this->databaseConnect->commit();
		// else $this->databaseConnect->rollback();
		// return $result;	
	// }
	function delPhtMonitorngQuantityRec($rmId)
	{
		 $qry = " delete from t_phtmonitoring_quantity where id=$rmId";
		// echo $qry = " delete from t_rmprocurmentequipment where rmProcurmentOrderId=$rmId";
		// die;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deletePhtMonitorng($phtMonitoringId)
	{
		$qry	= " delete from t_phtmonitoring where id=$phtMonitoringId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deletePhtMonitorngQuantity($phtMonitoringId)
	{
		$qry	= " delete from t_phtmonitoring_quantity where pht_monitoring_id=$phtMonitoringId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
	
	
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty from t_phtMonitoring where created_on>='$fromDate' and created_on<='$tillDate' order by date_Of_Entry desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecords()
	{
		$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty from t_phtmonitoring";
		
		//$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty from t_phtMonitoring order by created_on desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllPhtMonitoringItem($phtMonitoringDataId)
	{
		
		$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty from t_phtmonitoring where id='$phtMonitoringDataId' ";
		
		//$qry	= "select id,date_on, lot_id, supplier,supplier_group,species,supply_qty,pht_No,pht_Qty,set_off_Qty,balance_Qty from t_phtMonitoring where id='$phtMonitoringDataId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
		function fetchAllSupplierGroupName()
	{
		$qry	= "select b.id, b.supplier_group_name from m_supplier_group b where active='1' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get lot from t_rmreceiptgatepass 
	function findPHTLot($lotId)
	{
		//$qry="select a.id,a.lot_Id from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id where a.date_Of_Entry='$date'";
		$qry	=	"select id, concat(alpha_character,rm_lotid) from t_manage_rm_lotid where id=$lotId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	
}
?>