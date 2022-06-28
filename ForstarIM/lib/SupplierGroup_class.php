<?php
class SupplierGroup
{  
	/****************************************************************
	This class deals with all the operations relating to Master > supplier group
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SupplierGroup(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	
	
	# Filter location List
	function filterLocationList($supplierId)
	{
		
		//$qry = " select  b.id, b.name from m_city a left join m_state b on a.state_id=b.id where a.id='$cityId' order by b.name asc ";
		$qry="select a.id,a.location,b.name from m_pond_master a join m_landingcenter b on a.location=b.id where a.supplier='$supplierId' order by location asc";
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
	
	# Filter pond List
	function filterPondList($locationId)
	{
		
		//$qry1="select location from m_pond_master where id='$locationId'";
		//$result	= $this->databaseConnect->getRecords($qry1);
		//$loca=$result[0];
		
		//$qry = " select  b.id, b.name from m_city a left join m_state b on a.state_id=b.id where a.id='$cityId' order by b.name asc ";
		$qry="select id,pond_name from m_pond_master where location='$locationId' and active='1' order by pond_name asc";
		//echo $qry;
		

		
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
	
	#Add a supplier group
	function addSupplierGroup($supplierGroupName,$userId)
	{
		$qry	=	"insert into m_supplier_group (supplier_group_name, created_on, created_by) values('".$supplierGroupName."',Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	#Add a supplier group
	function addSupplierGroupDetails($supplierGroupId, $supplierName, $supplierLocation,$pondName )
	{
		$qry	=	"insert into m_supplier_group_details (supplier_group_name_id,supplier_name,supplier_location,pond) values('".$supplierGroupId."','".$supplierName."','".$supplierLocation."','".$pondName."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		  $qry	=	"select id, supplier_group_name,active FROM m_supplier_group order by supplier_group_name limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	function getSupplierData($supplierGroupNameId)
	{		
		$qry 	= "select id, supplier_name,supplier_location,pond from m_supplier_group_details where supplier_group_name_id='$supplierGroupNameId' order by supplier_name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getSupplierName($supplier)
	{		
		$qry 	= "select name,address from supplier where id='$supplier'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getSupplierLocation($location)
	{		
		$qry 	= "select name from m_landingcenter where id='$location'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getSupplierPond($pond)
	{		
		 $qry 	= "select pond_name,allotee_name,registration_no,registration_date,registration_expiry_date from m_pond_master where id='$pond'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	# Delete a supplier group 
	function deleteSupplierGroup($supplierGroupId)
	{
		$qry	= " delete from m_supplier_group where id=$supplierGroupId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	# Delete a supplier detail group 
	function deleteSupplierGroupDetail($supplierGroupId)
	{
		$qry	= " delete from m_supplier_group_details where supplier_group_name_id=$supplierGroupId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function updateSupplierGroupConfirm($supplierGroupId){
		 $qry	= "update m_supplier_group set active='1' where id=$supplierGroupId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}
	function updateSupplierGroupReleaseconfirm($supplierGroupId){
	$qry	= "update m_supplier_group set active='0' where id=$supplierGroupId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	# Get supplier group based on id 
	function find($supplierGroupId)
	{
		$qry	= "select id, supplier_group_name  from m_supplier_group where id=$supplierGroupId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update  a  supplier group
	function updateSupplierGroup($supplierGroupId, $supplierGroupName)
	{
		 $qry	= " update m_supplier_group set supplier_group_name='$supplierGroupName' where id=$supplierGroupId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Update Entry
	function updateSupplierGroupDetails($supplierGrpId, $name,$suppLocation,$suppPond)
	{
		$qry = " update m_supplier_group_details set supplier_name='$name',supplier_location='$suppLocation',pond='$suppPond' where id='$supplierGrpId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	# Delete Entry Rec
	function delSupGroupRec($supplierGrpId)
	{
		$qry = " delete from m_supplier_group_details where id=$supplierGrpId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Returns all Supplier group 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 //$qry	=	"select id, test_name, test_method, description,active FROM m_rmtest_master order by test_name";
		 $qry	=	"select id, supplier_group_name,active FROM m_supplier_group order by supplier_group_name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function filterLocationName($centerId)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select name FROM m_landingcenter WHERE id=$centerId";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}


	function chkPondEntryExist($supplierId, $locationId,$pondId, $supplierGroupId)
	{
		$qry = "select id from m_supplier_group_details where supplier_name='$supplierId' and supplier_location='$locationId' and pond='$pondId' ";
		if ($supplierGroupId!="" && $supplierGroupId>0) $qry .= " and supplier_group_name_id!=$supplierGroupId";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# -----------------------------------------------------
	# Checking Supplier group Id is in use (Procurement order);
	# -----------------------------------------------------
	function supplierGroupRecInUse($supplierGroupId)
	{		
		$qry = " select id from (
				select sgd.id from m_supplier_group_details sgd join t_rmprocurmentsupplier eps on sgd.supplier_name = eps.supplier_id where sgd.supplier_group_name_id='$supplierGroupId'
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	} 
	
	

	
}
?>