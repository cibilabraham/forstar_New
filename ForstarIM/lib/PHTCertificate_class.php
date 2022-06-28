<?php
class PHTCertificate
{  
	/****************************************************************
	This class deals with all the operations relating to Procurement Order
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PHTCertificate(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	# Filter supplier List
	function filterSupplierList($supplierGroupId)
	{
		$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	
	# Checking Unique Numbering
	function checkUnique($reqNumber, $hidReqNumber)
	{
		$addWhr = ( $hidReqNumber !="" ) ? " and PHTCertificateNo!='$hidReqNumber' " : "";
		$sqry = "select id from t_phtcertificate where PHTCertificateNo='$reqNumber' $addWhr ";
		//echo $sqry;
		$srec = $this->databaseConnect->getRecord($sqry);
		//return ( sizeof($srec)>0)?true:false;
		return $srec;
	}
	
	# Filter pond List
	function filterPondList($supplerId,$suplierGroupId)
	{
		$qry="SELECT a.supplier,a.id, a.pond_name FROM m_pond_master a JOIN supplier b ON a.supplier = b.id WHERE a.supplier = '$supplerId' ORDER BY name";
		//$qry="select a.id,a.pond,b.pond_name from m_supplier_group_details a join m_pond_master b on a.pond=b.id where supplier_name='$supplerId' and supplier_group_name_id='$suplierGroupId' order by pond asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	function getMonitoringBalance($phtCertificateId)
	{	
		$qry	=	"select sum(setoff_quantity) FROM t_phtmonitoring_quantity  where pht_certificate_number='$phtCertificateId'";
		//$qry	=	"select balance_quantity,setoff_quantity FROM t_phtmonitoring_quantity  where pht_certificate_number='$phtCertificateId'";
		//$qry	=	"select balance_Qty FROM t_phtmonitoring  where pht_No='$phtCertificateId'";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	
	}
	#Add PHT Certificate
	function addPHTCertificate($PHTCertificateNo,$species,$supplierGroup,$supplier,$pondName,$phtQuantity,$dateOfIssue,$dateOfExpiry,$receivedDate, $userId)
	{
		$qry	= "insert into t_phtcertificate(PHTCertificateNo, species,supplier_group,supplier,pond_Name,pht_Qty,date_of_issue,date_of_expiry,received_date, created_on, created_by) values('$PHTCertificateNo', '$species','$supplierGroup','$supplier','$pondName','$phtQuantity','$dateOfIssue','$dateOfExpiry','$receivedDate', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select * FROM t_phtcertificate order by PHTCertificateNo limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Get phtCertificate based on id 
	function find($phtCertificateId)
	{
		$qry	= "select * from t_phtcertificate where id=$phtCertificateId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function getSpeciousName($speciesId)
	{	$qry 	= "select name from m_fish where id='$speciesId'";	
		//$qry 	= "select category from m_fishcategory where id='$speciesId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function fetchQty($pondId)
	{		
		$qry 	= "select id,pond_qty from m_pond_master where id='$pondId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function filterPondQty($pondId)
	{		
		$qry 	= "select id,pond_qty from m_pond_master where id='$pondId'";
		//echo $qry;
		// $result	= $this->databaseConnect->getRecord($qry);
		// return $result;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	function getSupplierGroupName($supplierGroup)
	{		
		 $qry 	= "select supplier_group_name from m_supplier_group where id='$supplierGroup'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getSupplierName($supplier)
	{		
		 $qry 	= "select name from supplier where id='$supplier'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getPondName($pondId)
	{		
		 $qry 	= "select pond_name from m_pond_master where id='$pondId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getPondQty($pondQtyId)
	{		
		 $qry 	= "select pond_qty from m_pond_master where id='$pondQtyId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function fetchAllSupplieRecords($supplierGroup)
	{		
		 $qry="select DISTINCT(a.name),a.id from supplier a join m_supplier_group_details b on a.id=b.supplier_name where supplier_group_name_id='$supplierGroup'   order by name asc";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllPondRecords($supplier,$supplierGroup)
	{		
		// $qry="select DISTINCT(a.pond_name),a.id from m_pond_master a join m_supplier_group_details b on a.id=b.pond where supplier_name='$supplier' and supplier_group_name_id='$supplierGroup' order by pond asc";
		$qry="SELECT a.id, a.pond_name FROM m_pond_master a JOIN supplier b ON a.supplier = b.id WHERE a.supplier = '$supplier' ORDER BY name";
		
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Update  a  PHT Certificate
	function updatePHTCertificate($phtCertificateId, $PHTCertificateNo, $species, $supplierGroup, $supplier, $pondName,$phtQuantity, $dateOfIssue, $dateOfExpiry, $receivedDate)
	{
		$qry	= " update t_phtcertificate set PHTCertificateNo='$PHTCertificateNo', species='$species', supplier_group='$supplierGroup', 	supplier='$supplier', pond_Name='$pondName',pht_Qty='$phtQuantity',date_of_issue='$dateOfIssue',date_of_expiry='$dateOfExpiry',received_date='$receivedDate' where id=$phtCertificateId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete a PHT Certificate
	function deletePhtCertificate($phtCertificateId)
	{
		$qry	= " delete from t_phtcertificate where id=$phtCertificateId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function updatePHTCertificateconfirm($phtCertificateId){
		$qry	= "update t_phtcertificate set active='1' where id=$phtCertificateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updatePhtCerticicateReleaseconfirm($phtCertificateId){
	$qry	= "update t_phtcertificate set active='0' where id=$phtCertificateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	# Returns all  PHT Certificate
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select * from t_phtcertificate order by PHTCertificateNo";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function deletePhtMonitoring($phtCertificateId)
	{
		$qry	= " delete from t_phtmonitoring where certificate_id=$phtCertificateId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


}
?>