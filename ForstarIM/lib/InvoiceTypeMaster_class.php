<?php
class InvoiceTypeMaster
{
	/****************************************************************
	This class deals with all the operations relating to Invoice Type Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function InvoiceTypeMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Chek Entry Exist
	function chkInvTypeExist($invTypeName, $invTypeId)
	{
		$qry = "select id from m_invoice_type where name='$invTypeName'";
		if ($invTypeId) $qry .= " and id!=$invTypeId";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Add 
	function addInvoiceType($invoiceTypeName, $taxApplicable, $sampleInvoice, $userId)
	{
		$qry = "insert into m_invoice_type (name, tax, sample, created, createdby) values('$invoiceTypeName', '$taxApplicable', '$sampleInvoice', NOW(), '$userId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{		
		$whr = "";

		$orderBy = " a.name asc ";
		$limit	 = " $offset,$limit ";

		$qry = " select a.id, a.name, a.tax, a.sample,active,(select count(id) from t_invoice_main where invoice_type_id=a.id)as tot from m_invoice_type a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;

		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$whr = "";
		$orderBy = " a.name asc ";

		$qry = " select a.id, a.name, a.tax, a.sample,active from m_invoice_type a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveinvoice()
	{
		$whr = "active=1";
		$orderBy = " a.name asc ";

		$qry = " select a.id, a.name, a.tax, a.sample,active from m_invoice_type a "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}


	
	# Get a Record based on id
	function find($invTypeId)
	{
		$qry = "select id, name, tax, sample from m_invoice_type where id=$invTypeId";

		return $this->databaseConnect->getRecord($qry);
	}


	# Update  a  Record
	function updateInvoiceType($invTypeId, $invoiceTypeName, $taxApplicable, $sampleInvoice)
	{
		$qry = "update m_invoice_type set name ='$invoiceTypeName', tax='$taxApplicable', sample='$sampleInvoice' where id=$invTypeId ";
	
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Selected State Rec
	function deleteInvoiceTypeRec($invTypeId)
	{
		$qry 	= " delete from m_invoice_type where id=$invTypeId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function updateShipmentInvoice($invoiceTypeId)
	{
		$invoiceType = $this->getInvoiceType($invoiceTypeId);			
		if ($invoiceType!="") {
			$this->updateInvoiceRecs($invoiceType, $invoiceTypeId);
		}
	}

	function updateInvoiceRecs($invoiceType, $invoiceTypeId)
	{
		$qry = "update t_invoice_main set invoice_type ='$invoiceType' where invoice_type_id=$invoiceTypeId";
	
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	function getInvoiceType($invoiceTypeId)
	{
		$qry = " select id, tax, sample from m_invoice_type where id='$invoiceTypeId' ";
		$rec	= $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$tax 	= $rec[1];
			$sample = $rec[2];
			if ($sample=='Y') return 'S';
			else if ($tax=='Y') return 'T';
			else return 'E'; // Export
		}
	}

	function chkRecInUse($invoiceTypeId)
	{
		$qry = " select id from t_invoice_main where invoice_type_id='$invoiceTypeId' ";

		$recs	= $this->databaseConnect->getRecords($qry);
		return (sizeof($recs))?true:false;
	}
	
function updateInvoiceconfirm($invoiceTypeId)
	{
	$qry	= "update m_invoice_type set active='1' where id=$invoiceTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateInvoiceReleaseconfirm($invoiceTypeId)
	{
		$qry	= "update m_invoice_type set active='0' where id=$invoiceTypeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>