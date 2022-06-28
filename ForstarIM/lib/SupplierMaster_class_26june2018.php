<?php
class SupplierMaster
{
	/****************************************************************
	This class deals with all the operations relating to Supplier
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SupplierMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addSupplier($code, $name, $address, $phone, $vatNo, $cstNo, $frozen, $inventory, $rte, $pinCode, $faxNo, $email, $panNo, $place, $landingCenter, $paymentBy, $currentUserId, $bankAcNo, $bankName, $supplierStatus,$fssaiRegNo,$serviceTaxno,$bankIFSCCode)
	{
		$qry	= "insert into supplier (code, name, address, phone, vat_no, cst_no, created, frozen, inventory, rte, pincode, fax, email, pan, native_place, payment_by, createdby, bank_account_no, bank_name, active,fssairegnno,servicetaxno,bankifsccode) values('".$code."', '".$name."', '".$address."', '".$phone."', '$vatNo', '$cstNo', Now(), '$frozen', '$inventory', '$rte', '$pinCode', '$faxNo', '$email', '$panNo', '$place', '$paymentBy', '$currentUserId', '$bankAcNo', '$bankName', '$supplierStatus','$fssaiRegNo','$serviceTaxno','$bankIFSCCode')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Getting Inserted Id
			$supplierMainId = $this->databaseConnect->getLastInsertedId();
			if ($landingCenter!="" && $frozen=='Y' && $supplierMainId!="") {
				$insertLandingCenter = $this->addSupplier2Center($landingCenter, $supplierMainId);
			}
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	#Add Supplier 2 Center
	function addSupplier2Center($place, $lastId)
	{
 	 	if ($place) {
			foreach ($place as $laId) {
				$landingId	=	"$laId";
				$qry	=	"insert into m_supplier2center (supplier_id, center_id) values ('".$lastId."', '".$landingId."')";
				//echo $qry;
				//die;
				$insertCenter	=	$this->databaseConnect->insertRecord($qry);
				if ($insertCenter) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();				
			}
			return $insertCenter;
		}	
	}


	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit, $sectionFilter)
	{
		
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' " ;
		else $whr .= "";

		if ($sectionFilter=='INV') $whr .= " inventory='Y' " ;
		else $whr .= "";
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' " ;
		else $whr .= "";

		$orderBy	= " name asc ";
		$limit		= " $offset, $limit ";

		$qry	= " select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier ";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;	
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivesupplier($sectionFilter)
	{		
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' and activeconfirm=1" ;
		else $whr .= "";

		if ($sectionFilter=='INV') $whr .= " inventory='Y' and activeconfirm=1" ;
		else $whr .= "";
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' and activeconfirm=1" ;
		else $whr .= "";

		$orderBy	=  " name asc ";

		$qry	= "select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier ";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllPagingRecordsfrninv($offset, $limit, $sectionFilter)
	{
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' " ;
		else $whr .= "";

		if ($sectionFilter=='INV') $whr .= " inventory='Y' " ;
		else $whr .= "";
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' " ;
		else $whr .= "";

		$orderBy	= " name asc ";
		$limit		= " $offset, $limit ";

		$qry	= " select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier where frozen='Y' || inventory='Y'";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;	
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllPagingRecordsfrnrte($offset, $limit, $sectionFilter)
	{
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' " ;
		else $whr .= "";

		if ($sectionFilter=='INV') $whr .= " inventory='Y' " ;
		else $whr .= "";
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' " ;
		else $whr .= "";

		$orderBy	= " name asc ";
		$limit		= " $offset, $limit ";

		$qry	= " select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier where frozen='Y' || rte='Y'";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;	
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllPagingRecordsinvrte($offset, $limit, $sectionFilter)
	{
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' " ;
		else $whr .= "";

		if ($sectionFilter=='INV') $whr .= " inventory='Y' " ;
		else $whr .= "";
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' " ;
		else $whr .= "";

		$orderBy	= " name asc ";
		$limit		= " $offset, $limit ";

		$qry	= " select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier where inventory='Y' || rte='Y'";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;	
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Supplier
	function fetchAllRecords($sectionFilter)
	{		
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' " ;
		else $whr .= "";

		if ($sectionFilter=='INV') $whr .= " inventory='Y' " ;
		else $whr .= "";
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' " ;
		else $whr .= "";

		$orderBy	=  " name asc ";

		$qry	= "select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier ";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsPrint($sectionFilter)
	{		
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' " ;
		

		if ($sectionFilter=='INV') $whr .= " inventory='Y' " ;
		
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' " ;
		
		if ($sectionFilter=='-1') $whr .= "" ;

		$orderBy	=  " name asc ";

		$qry	= "select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier ";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


function fetchAllRecordsfil($sectionFilter)
	{		
		$whr  = "";
		if ($sectionFilter=='FRN') $whr .= " frozen='Y' " ;
		else $whr .= "";

		if ($sectionFilter=='INV') $whr .= " inventory='Y' " ;
		else $whr .= "";
		
		if ($sectionFilter=='RTE') $whr .= " rte='Y' " ;
		else $whr .= "";

		$orderBy	=  " name asc ";

		$qry	= "select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, active,activeconfirm from supplier ";
		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Supplier based on id 
	function find($supplierId)
	{
		$qry	= " select id, code, name, address, phone, vat_no, cst_no, frozen, inventory, rte, pincode, fax, email, pan, native_place, payment_by, bank_account_no, bank_name, active,fssairegnno,servicetaxno,bankifsccode from supplier where id=$supplierId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Supplier
	function deleteSupplier($supplierId)
	{
		$qry	=	" delete from supplier where id=$supplierId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Supplier
	function updateSupplier($supplierId, $code, $name, $address, $phone, $vatNo, $cstNo, $frozen, $inventory, $rte, $pinCode, $faxNo, $email, $panNo, $place, $landingCenter, $paymentBy, $bankAcNo, $bankName, $supplierStatus,$fssaiRegNo,$serviceTaxNo,$bankIFSCCode)
	{
		# Update the modified history
		$modifiedLog = "";		
		if ($paymentBy && $frozen=='Y') {
			$cDate = date("Y-m-d");
			list($selPaymentBy, $mHistory) = $this->getModifiedHistory($supplierId);
			# On Modified Log Format => modified Date: Prev Payment by
			$modifiedLog = $cDate.":".$selPaymentBy;
			$logHistory = "";
			if ($mHistory!="") $logHistory = $mHistory.",".$modifiedLog;
			else $logHistory = $modifiedLog;
			# Update the table
			$updateQry = "";
			if ($paymentBy!=$selPaymentBy) $updateQry = " , modified_history='$logHistory'";	
		}

		$qry	= " update supplier set code='$code', name='$name', address='$address', phone='$phone', vat_no='$vatNo', cst_no='$cstNo', frozen='$frozen', inventory='$inventory', rte='$rte', pincode='$pinCode', fax='$faxNo', email='$email', pan='$panNo', native_place='$place', payment_by='$paymentBy', bank_account_no='$bankAcNo', bank_name='$bankName', active='$supplierStatus',fssairegnno='$fssaiRegNo',servicetaxno='$serviceTaxNo',bankifsccode='$bankIFSCCode' $updateQry where id='$supplierId' ";
		//echo $qry;
		//die ;
		
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			# Delete Supplier 2 Center
			$this->deleteSupplier2Center($supplierId);
			if ($landingCenter!="" && $frozen=='Y') {
				$insertLandingCenter=$this->addSupplier2Center($landingCenter, $supplierId);
			}
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Delete the exisiting Center at the time of Update
	function deleteSupplier2Center($supplierId)
	{
		$qry	= " delete from m_supplier2center where supplier_id='$supplierId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Sub Supplier when delete Supplier
	function deleteSubSupplierRec($supplierId)
	{
		$qry	=	" delete from m_subsupplier where supplier='$supplierId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	// ----------------------
	// Get Old History
	// Return payment by, Modified History
	// ----------------------
	function getModifiedHistory($catchEntryId)
	{
		$qry = "select payment_by, modified_history from supplier where id='$catchEntryId' ";	
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";
	}
	
	# -----------------------------------------------------
	# Checking Supplier Id is in use ( Sub-Supplier, Daily Catch Entry);
	# -----------------------------------------------------
	function supplierRecInUse($supplierId)
	{		
		$qry = " select id from (
				select a.id as id from m_subsupplier a where a.supplier='$supplierId'
			union
				select a1.id as id from t_dailycatch_main a1 where a1.main_supplier='$supplierId'	
			union
				select a2.id from supplier_stock a2 where a2.supplier_id='$supplierId'
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	#fetch all center records at the time of edit
	function fetchCenterSelectedRecords($editId)
	{
		$qry 	=	"select a.id, a.name, b.id, b.supplier_id, b.center_id from m_landingcenter a left join m_supplier2center b on a.id=b.center_id and b.supplier_id='$editId' order by a.name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch supplier2center records based on suppplier ID
	function fetchCenterRecords($supplierId)
	{
 		$qry = "select a.id,a.supplier_id,a.center_id,b.id,b.code,b.name from m_supplier2center a, m_landingcenter b where a.center_id = b.id and a.supplier_id='$supplierId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
 	}

	#Find the Number of Sub-Suppliers
	function  getNumberOfSubSuppliers($supplierId)
	{
		$qry = " select id from m_subsupplier where supplier='$supplierId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?sizeof($result):"";
	}

	#Fetch supplier2center records based on Center ID --- For Daily Catch Entry
	function fetchSupplierRecords($centerId)
	{
 		$qry	= "select a.id,a.supplier_id,a.center_id,b.id,b.name,b.payment_by from m_supplier2center a, supplier b where a.supplier_id = b.id and a.center_id='$centerId' order by b.name asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
 	}

	/*
		Get Supplier Payment By
	*/
	function getSupplierPaymentBy($supplierId)
	{
		$qry	= " select payment_by from supplier where id=$supplierId ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Updating status Starts here
	function updateSupplierStatus($supplierId, $status)
	{
		$qry	=	" update supplier set active='$status' where id=$supplierId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	
	function getSupplierCurrentStatus($supplierId)
	{
		$rec = $this->find($supplierId);
		return $rec[18];
	}
	# Updating status Ends here

	#Fetch supplier2center records based on Center ID --- For Daily Catch Entry
	# Get Center Wise Active Recs
	function getCenterWiseActiveSuppliers($centerId)
	{
 		$qry	= "select a.supplier_id, b.name from m_supplier2center a, supplier b where a.supplier_id = b.id and a.center_id='$centerId' and b.active='Y' and b.activeconfirm='1' order by b.name asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
 	}

	# Supplier Name
	function getSupplierName($supplierId)
	{
		$qry	= " select name from supplier where id='$supplierId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function getCountSupplierCode()
	{
$qry = "select count(*) no from supplier";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";

	}
	
	function getSupplierCode()
	{
$qry = "select max(code) from supplier";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";

	}

	function updateSupplierconfirm($supplierId)
	{
	$qry	= "update supplier set activeconfirm='1' where id=$supplierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateSupplierReleaseconfirm($supplierId)
	{
		$qry	= "update supplier set activeconfirm='0' where id=$supplierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	function fetchAllRecordsunitActive()
	{
		$qry	= "select id,name,active from supplier where active=1 order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRMSupplierActive()
	{
		$qry	= "select id,name,active from supplier where frozen='Y' and active=1 order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchSupplier($suppliercode)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select name FROM supplier WHERE id=$suppliercode";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}

}
?>