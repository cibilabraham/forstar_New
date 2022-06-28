<?php
class SubSupplier
{
	/****************************************************************
	This class deals with all the operations relating to Sub Supplier 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function SubSupplier(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

		
	function addSubSupplier($Code, $Name, $supplierId, $Address, $Place, $Pincode, $TelNo, $FaxNo, $Email, $LstNo, $CstNo, $PanNo)
	{
		$qry	= "insert into m_subsupplier (code, name, supplier, address, place, pin, tel, fax, email, lstno, cstno, pan) values('".$Code."', '".$Name."', ".$supplierId.", '".$Address."', '".$Place."', '".$Pincode."', '".$TelNo."', '".$FaxNo."', '".$Email."', '".$LstNo."','".$CstNo."','".$PanNo."')";
		
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Sub Suppliers	
	function fetchAllRecords()
	{
		$qry	= "select id, name, code,supplier from m_subsupplier order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	function filterRecords($mainSupplier)
	{
		$qry	= "select id, name, code,supplier,place from m_subsupplier where supplier='$mainSupplier'";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Filter records based on filter id --> used in subsupplier listing 
	function fetchAllFilterRecords($landingCenterId,$supplierId)
	{
		$qry	= "select id, name, code,supplier,place from m_subsupplier where place='$landingCenterId' and supplier='$supplierId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Sub Supplier based on id 
	function find($subsupplierId)
	{
		$qry	= "select id,code,name,supplier,address,place,pin,tel,fax,email,lstno,cstno,pan from m_subsupplier where id=$subsupplierId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Sub Supplier
	function deleteSubSupplier($subsupplierId)
	{
		$qry	= " delete from m_subsupplier where id=$subsupplierId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update Sub Supplier

	function updateSubSupplier($subsupplierId, $Code, $Name, $supplierId, $Address, $Place, $Pincode, $TelNo, $FaxNo, $Email, $LstNo, $CstNo, $PanNo)
	{
		$qry	= " update m_subsupplier set code='$Code', name='$Name', supplier=$supplierId, address='$Address', place='$Place', pin='$Pincode', tel='$TelNo', fax='$FaxNo', email='$Email', lstno='$LstNo', cstno='$CstNo', pan ='$PanNo' where id=$subsupplierId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#fetch all Landing Center records based on supplier
	function filterLandingCenterRecords($supplierId)
	{
		$qry 	= "select a.id,a.name,b.id,b.supplier_id,b.center_id from m_landingcenter a, m_supplier2center b where a.id=b.center_id and b.supplier_id='$supplierId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Filter records based main supplier and landing center ---> it is used in DailyCatchEntry
	function filterSubSupplierRecords($mainSupplier,$landingCenter)
	{
		$qry	= "select id, name, code,supplier from m_subsupplier where supplier='$mainSupplier' and place='$landingCenter' order by name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Check Record is Linked with any other Process
	function checkMoreEntriesExist($subSupplierId)
	{
		$qry = " select id from (
				select a.id from m_subsupplier a, t_dailycatch_main b where a.id = b.sub_supplier and a.id='$subSupplierId'
			union
				select a.id from m_subsupplier a, t_dailycatch_declared b where a.id = b.sub_supplier and a.id='$subSupplierId'
			) as X group by id ";
		$result	=	$this->databaseConnect->getRecords($qry);
		return  ( sizeof($result) > 0 )?true:false;
	}

}
?>