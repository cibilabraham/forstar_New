<?php
class UpdateSupplierQry
{
	/****************************************************************
	This class deals with all the operations relating to Update Qry
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function UpdateSupplierQry(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	function updateSupplierTable()
	{
		$qry = " ALTER TABLE `supplier` ADD COLUMN `fax` varchar(15) default NULL, ADD COLUMN `email` varchar(50) default NULL, ADD COLUMN `pan` varchar(50) default NULL, ADD COLUMN `pincode` varchar(10) default NULL, ADD COLUMN `native_place` int(2) default NULL, ADD COLUMN `payment_by` enum('E','D') default 'E', ADD COLUMN `createdby` int(5) default NULL COMMENT 'User', ADD COLUMN `modified_history` varchar(150) default NULL COMMENT 'Record payment by change in the format of modified date:oldPaymentBy', ADD COLUMN `old_supplier_id` int(5) ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	

	function getDailyCatchSupplierRecords()
	{
		$qry = " select id, code, name, address, tel, fax, email, pan, pincode, native_place, payment_by, createdby, modified_history from m_supplier where name!=''";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	function insertNewSupplierRec($supplierOldId, $supCode, $supName, $supAddress, $supTel, $supFax, $supEmail, $supPan, $supPincode, $supNativePlace, $supPaymentBy, $supCreatedBy, $supModifiedHistory)
	{
		$qry	= "insert into supplier (code, name, address, phone, vat_no, cst_no, created, frozen, inventory, rte, fax, email, pan, pincode, native_place, payment_by, createdby, modified_history, old_supplier_id) values ('$supCode', '$supName', '$supAddress', '$supTel','','',NOW(),'Y','N','N', '$supFax', '$supEmail', '$supPan', '$supPincode', '$supNativePlace', '$supPaymentBy', '$supCreatedBy', '$supModifiedHistory', '$supplierOldId')";
		//echo "<br>$qry<br>";		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();		
		return $insertStatus;
	}


	function getSupplier2CentreRec()
	{
		$qry = " select id, supplier_id from m_supplier2center ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#
	function updateSupplier2CentreRec($supplier2CenterRecId, $supplierMainId)
	{
		//$qry = " update m_supplier2center set supplier_id='$supplierMainId' where supplier_id='$supplierOldId' ";
		$qry = " update m_supplier2center set supplier_id='$supplierMainId' where id='$supplier2CenterRecId' ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getSubSupplierRec()
	{
		$qry = " select id, supplier from m_subsupplier ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# 
	function updateSubSupplierRec($subSupplierEntryId, $supplierMainId)
	{
		//$qry = " update m_subsupplier set supplier='$supplierMainId' where supplier='$supplierOldId' ";
		$qry = " update m_subsupplier set supplier='$supplierMainId' where id='$subSupplierEntryId' ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getDailyCatchMainRec()
	{
		$qry = " select id , main_supplier from t_dailycatch_main ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#
	function upateDailyCatchMainRec($dailyEntryId, $supplierMainId)
	{
		//$qry = " update t_dailycatch_main set main_supplier='$supplierMainId' where main_supplier='$supplierOldId' ";
		$qry = " update t_dailycatch_main set main_supplier='$supplierMainId' where id='$dailyEntryId' ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;

	}


	function updateSupplierOldTable()
	{
		$qry = " ALTER TABLE `m_supplier` RENAME TO `m_supplier_R`";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function getNewSupplierId($cSupplierId)
	{
		$qry = " select id from supplier where old_supplier_id='$cSupplierId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	
}