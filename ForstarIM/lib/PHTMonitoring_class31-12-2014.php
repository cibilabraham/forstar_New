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
		
		$qry="select id,concat(alpha_character,rm_lotid) from  t_manage_rm_lotid  where created_on='$date' and status='0' and id not in (select lot_id_origin from t_manage_rm_lotid) and id not in (select lot_id from t_phtmonitoring) or id='$id'";
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
	
	#Add a PHT Monitoring
	function addPHTMonitoringData($phtCertificateId,$date,$supplier, $supplierGroupName, $specious,$supplyQty, $userId)
	{
		$qry	=	"insert into t_phtmonitoring (date_on,  supplier,supplier_group,species,supply_qty,created_on, created_by,certificate_id) values('".$date."','".$supplier."','".$supplierGroupName."','".$specious."','".$supplyQty."', Now(), '$userId','$phtCertificateId')";

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
		$qry	= "select tphtm.id, tphtm.date_on, tphtm.lot_id, tphtm.supplier,tphtm.supplier_group,tphtm.species,tphtm.supply_qty,s.name,msg.supplier_group_name,mf.name,tphtc.PHTCertificateNo from t_phtmonitoring tphtm left join supplier s on s.id=tphtm.supplier left join m_supplier_group msg on msg.id=tphtm.supplier_group left join m_fish mf on mf.id=tphtm.species left join t_phtcertificate tphtc on tphtc.id=tphtm.certificate_id where tphtm.created_on>='$fromDate' and tphtm.created_on<='$tillDate' order by tphtm.created_on desc limit $offset,  $limit";


		//$qry	= "select id, date_on, lot_id, supplier,supplier_group,species,supply_qty from t_phtmonitoring where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc limit $offset,  $limit";
		
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

	function getPhtCerificate()
	{
		//$qry	=	"SELECT id,PHTCertificateNo FROM t_phtcertificate order by id desc";
		$qry	=	"SELECT tphtc.id,tphtc.PHTCertificateNo FROM t_phtmonitoring tphtm join t_phtcertificate tphtc on tphtc.id =tphtm.certificate_id order by tphtm.id desc";
		return $this->databaseConnect->getRecords($qry);
	}
	
	function getCerificateLotId()
	{	$result="";
		$qry	= "SELECT id,concat(alpha_character,rm_lotid) from t_manage_rm_lotid order by id desc";
		//echo $qry;
		$rmlot=$this->databaseConnect->getRecords($qry);
		
		if(sizeof($rmlot)>0)
		{
			$rmlotdropDown = '<select name="rmlotid_0" id="rmlotid_0" onchange="xajax_certificateNo(this.value,0);">';
			$rmlotdropDown.= '<option value=""> --Select-- </option>';
			foreach($rmlot as $rmlotDetail)
			{	
				$rmlotdropDown.= '<option value="'.$rmlotDetail[0].'">'.$rmlotDetail[1].'</option>';
			}

			$rmlotdropDown.= '</select>';

		}


		$result.="<table><tr><td><table cellpadding='4' cellspacing='1' bgcolor='#999999' align='center' id='tblAddCertificateDetail'><tr bgcolor='#f2f2f2' align='center'><td class='listing-head' nowrap>RM Lot id</td><td class='listing-head' nowrap>Certificate Qty</td><td class='listing-head' nowrap>Adjusted Qty</td><td>&nbsp;</td></tr>";
		$result.="<tr bgcolor='#e8edff' align='center'>";
		$result.="<td class='listing-item'>$rmlotdropDown</td>";
		$result.="<td class='listing-item'><input name='cerificateQty_0' id='cerificateQty_0'/></td>";
		$result.="<td class='listing-item'><input name='adjustedCertificateQty_0' id='adjustedCertificateQty_0'/></td><td>&nbsp;</td>";
		$result.="</tr></table></td></tr>";
		$result.="<tr id='addNew'><td colspan='4' align='left' bgcolor='#ffffff' class='listing-item'><a id='addRow' class='link1' title='Click here to add new item.' onclick='javascript:addNewCertificateTableRow(0);' href='javascript:void(0);'>Add New</a></td><input type='hidden' id='certificateSize' name='certificateSize' value='1'/></tr>";
		$result.="</table>";
		return $result;
	}


	function getLotIdValues()
	{	$result="";
		$qry	= "SELECT tphc.id,tphc.PHTCertificateNo from t_phtcertificate tphc left join t_phtmonitoring tphtm on tphc.id=tphtm.certificate_id order by tphc.id";
		//echo $qry;
		$cerificate=$this->databaseConnect->getRecords($qry);
		
		if(sizeof($cerificate)>0)
		{
			$certificatedropDown = '<select name="certificateNo_0" id="certificateNo_0" onchange="xajax_certificateNo(this.value,0,'.$i.');">';
			$certificatedropDown.= '<option value=""> --Select-- </option>';
			foreach($cerificate as $cerificateDetail)
			{	
				$certificatedropDown.= '<option value="'.$cerificateDetail[0].'">'.$cerificateDetail[1].'</option>';
			}

			$certificatedropDown.= '</select>';

		}


		$result.="<table><tr><td><table cellpadding='4' cellspacing='1' bgcolor='#999999' align='center' id='tblAddcerificateDetail'><tr bgcolor='#f2f2f2' align='center'><td class='listing-head' nowrap>Pht Cerificate</td><td class='listing-head' nowrap>Supply Qty</td><td class='listing-head' nowrap>Adjusted Qty</td><td>&nbsp;</td></tr>";
		$result.="<tr bgcolor='#e8edff' align='center'>";
		$result.="<td class='listing-item'>$certificatedropDown</td>";
		$result.="<td class='listing-item'><input name='supplyQty_0' id='supplyQty_0'/></td>";
		$result.="<td class='listing-item'><input name='adjustedQty_0' id='adjustedQty_0'/></td><td>&nbsp;</td>";
		$result.="</tr></table></td></tr>";
		$result.="<tr id='addNew'><td colspan='4' align='left' bgcolor='#ffffff' class='listing-item'><a id='addRow' class='link1' title='Click here to add new item.' onclick='javascript:addNewSupplierTableRow(0);' href='javascript:void(0);'>Add New</a></td><input type='hidden' id='supplierSize' name='supplierSize' value='1'/></tr>";
		$result.="</table>";
		return $result;
	}
	function getCerificateSupplierData($certificateId)
	{
		$qry	= "SELECT supplier,species,pond_Name,pht_Qty from t_phtcertificate where id='$certificateId'";
		//echo $qry;
		$res=$this->databaseConnect->getRecord($qry);
		$query	="SELECT tmgrm.id,concat( tmgrm.alpha_character,tmgrm.rm_lotid)  FROM `weighment_data_sheet` wds left join t_weightment_data_entries  twde on twde.weightment_data_sheet_id=wds.id left join t_manage_rm_lotid tmgrm on  wds.rm_lot_id=tmgrm.id where supplier_name='$res[0]' and  product_species='$res[1]' and pond_name='$res[2]' group by tmgrm.id";
		//echo  $query;
		$result = array();
		$result = $this->databaseConnect->getRecords($query);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function getCertificate($certificateNo)
	{
		$qry	= "SELECT balance_quantity FROM t_phtmonitoring_quantity WHERE  pht_certificate_number='$certificateNo' order by id desc limit 1";
		$result= $this->databaseConnect->getRecord($qry);
		if(sizeof($result)>0)
		{
			$resultArr=$result[0];
		}
		else
		{
			$qry	= "SELECT pht_Qty FROM t_phtcertificate WHERE  id='$certificateNo' order by id desc limit 1";
			$result= $this->databaseConnect->getRecord($qry);
			$resultArr=$result[0];
		}
		//echo $qry;
		//echo $resultArr;
		return $resultArr;
	}
	function getWeightmentData($rmlotid,$phtCerificate)
	{
		//echo $phtCerificate;
		$qry	= "SELECT twde.id,twde.supplier_name,twde.pond_name,twde.product_species,twde.weight,s.name,mpm.pond_name,mf.name from weighment_data_sheet wdt left join t_weightment_data_entries twde on wdt.id= twde.weightment_data_sheet_id left join supplier s on twde.supplier_name=s.id left join m_pond_master mpm on twde.pond_name=mpm.id left join m_fish mf on twde.product_species=mf.id where rm_lot_id='$rmlotid'";
		//echo $qry;
		$res=$this->databaseConnect->getRecords($qry);
		$certificateQnty 			= $this->getCertificate($phtCerificate);
		$result=array();
		if(sizeof($res)>0)
		{
			$result="<table><tr><td>";
			$result.="<table cellpadding='4' cellspacing='1' bgcolor='#999999' align='center' id='tblAddcerificateDetail'><tr align='center'><th valign='center' bgcolor='#ffffff' colspan='5' class='listing-head'><div style='height:100%; float: left; vertical-align:middle;'><img width='11' height='15' border='0' src='images/topLink.jpg'></div><div style='float: left; vertical-align:middle;'>Weightment Data sheet Details</div></th></tr>";
			$result.="<tr><td colspan='2' bgcolor='#ffffff' class='listing-head'>Cerificate Qty</td><td colspan='3' bgcolor='#ffffff' class='listing-item'><input type='text' name='certifyQnty' id='certifyQnty' value='".$certificateQnty."' readonly/></td></tr>";
			$result.="<tr bgcolor='#f2f2f2'><td><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick=\"checkAll(this.form,'rm_lot_'); \" class='chkBox'></td><td class='listing-head' nowrap>Supplier Name</td><td class='listing-head' nowrap>Pond Name</td><td class='listing-head' nowrap>Species</td><td class='listing-head' nowrap>Weight</td></tr>";
			
			$i=0; $j=0;
			foreach($res as $weighment)
			{
				$result.="<tr bgcolor='#e8edff'><td><input type='checkbox' class='chkBox' value='".$weighment[0]."' id='weightmentId_".$i."' name='weightmentId_".$i."' onclick='displayData(".$i.");'>
				</td><td class='listing-item' nowrap>$weighment[5]</td>
				<td class='listing-item' nowrap>$weighment[6]</td>
				<td class='listing-item' nowrap>$weighment[7]</td>
				<td class='listing-item' nowrap>$weighment[4]</td></tr>";
				$i++;
			}
			//foreach($res as $weighment)
			//{
				//$result.="<table id='selectedlotId_".$j."'></table>";
			//}
			//$result.="<tr><td bgcolor='#fff'></td></tr>";
			$result.="<input type='hidden' name='rowCnt' id='rowCnt' value='".$i."'/><input type='hidden' name='checkedSize' id='checkedSize' value='0'/></table></td></tr>";
			$result.="</table>";
		}
		return $result;
	}
	function getWeightmentDataSingle($weightmentId)
	{
		$qry	= "SELECT twde.id,twde.supplier_name,twde.pond_name,twde.product_species,twde.weight,s.name,mpm.pond_name,mf.name from weighment_data_sheet wdt left join t_weightment_data_entries twde on wdt.id= twde.weightment_data_sheet_id left join supplier s on twde.supplier_name=s.id left join m_pond_master mpm on twde.pond_name=mpm.id left join m_fish mf on twde.product_species=mf.id where twde.id='$weightmentId'";
		//echo $qry;
		$res=$this->databaseConnect->getRecord($qry);
		return $res;
	}
	function getWeightmentDataRMLotID($rmlotid)
	{
		$qry	= "SELECT twde.id,twde.supplier_name,twde.pond_name,twde.product_species,twde.weight,s.name,mpm.pond_name,mf.name from weighment_data_sheet wdt left join t_weightment_data_entries twde on wdt.id= twde.weightment_data_sheet_id left join supplier s on twde.supplier_name=s.id left join m_pond_master mpm on twde.pond_name=mpm.id left join m_fish mf on twde.product_species=mf.id where rm_lot_id='$rmlotid'";
		//echo $qry;
		$res=$this->databaseConnect->getRecords($qry);
		if(sizeof($res)>0)
		{
			$result="<table><tr><td>";
			$result.="<table cellpadding='4' cellspacing='1' bgcolor='#999999' align='center' id='tblAddcerificateDetail'><tr align='center'><th valign='center' bgcolor='#ffffff' colspan='8' class='listing-head'><div style='height:100%; float: left; vertical-align:middle;'><img width='11' height='15' border='0' src='images/topLink.jpg'></div><div style='float: left; vertical-align:middle;'>Weightment Data sheet Details</div></th></tr>";
			$result.="<tr bgcolor='#f2f2f2'><td class='listing-head' nowrap>Supplier Name</td><td class='listing-head' nowrap>Pond Name</td><td class='listing-head' nowrap>Species</td><td class='listing-head' nowrap>Weight</td><td class='listing-head' nowrap>Pht Cerificate</td><td class='listing-head' nowrap>Available Qty</td><td class='listing-head' nowrap>Adjusted Qty</td></tr>";
			
			$i=0; $j=0;
			foreach($res as $weighment)
			{
				$supplierId=$weighment[1];
				$pondId=$weighment[2];
				$speciesId=$weighment[3];
				$query	= "SELECT tphtc.id,tphtc.PHTCertificateNo from t_phtcertificate tphtc join t_phtmonitoring tphtm on tphtm.certificate_id=tphtc.id where tphtc.supplier='$supplierId' and tphtc.pond_Name='$pondId' and tphtc.species='$speciesId'";
				//echo $query;
				$rest = $this->databaseConnect->getRecords($query);
				if(sizeof($rest)>0)
				{
					$certificatedropDown = '<select name="phtcertificateNo_'.$i.'" id="certificateNo_'.$i.'" onchange="xajax_certificateAvailableQty(this.value,'.$i.');">';
					$certificatedropDown.= '<option value=""> --Select-- </option>';
					foreach($rest as $cerificateDetail)
					{	
						$certificatedropDown.= '<option value="'.$cerificateDetail[0].'">'.$cerificateDetail[1].'</option>';
					}

					$certificatedropDown.= '</select>';

				}
				
				//return $resultArr;

				$result.="<tr bgcolor='#e8edff'><td class='listing-item' nowrap>$weighment[5]</td>
				<td class='listing-item' nowrap>$weighment[6]</td>
				<td class='listing-item' nowrap>$weighment[7]</td>
				<td class='listing-item' nowrap>$weighment[4]</td><td class='listing-item' nowrap>$certificatedropDown</td><td class='listing-item' nowrap><input name='availableQtySupplier_".$i."' id='availableQtySupplier_".$i."' size='15' /></td><td class='listing-item' nowrap><input name='adjustedQtySupplier_".$i."' id='adjustedQtySupplier_".$i."' size='15' /></td></tr>";
				$i++;
			}
			//foreach($res as $weighment)
			//{
				//$result.="<table id='selectedlotId_".$j."'></table>";
			//}
			//$result.="<tr><td bgcolor='#fff'></td></tr>";
			$result.="</table></td></tr>";
			$result.="</table>";
		}
		return $result;
	}
	function getcerificateAvailableQnty($cerificateID)
	{
	}
}
?>