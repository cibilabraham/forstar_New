<?php
class PurchaseOrderReport
{
	/****************************************************************
	This class deals with all the operations relating to Purchase Order Report
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function PurchaseOrderReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# filter PO records
	function fetchPurchaseOrderRecords($fromDate, $tillDate, $selSupplierId, $selStatus)
	{
		$whr = "a.id=b.po_id and a.created>='$fromDate' and a.created<='$tillDate' and c.id=a.supplier_id";

		if ($selSupplierId=="") $whr .= "";
		else $whr .= " and a.supplier_id=$selSupplierId ";

		if ($selStatus=="") $whr .= "";
		else $whr .= " and a.status= '".$selStatus."'";

		$groupBy = " a.po ";

		$orderBy = " a.po asc ";

		$qry = "select a.id, a.po, a.po_number, a.created, sum(b.total_amount), a.status, c.name from m_purchaseorder a, purchaseorder_entry b, supplier c";

		if ($whr!="")
			$qry .= " where ".$whr;
		if ($groupBy!="")
			$qry .= " group by ".$groupBy;
		if ($orderBy!="")
			$qry .= " order by ". $orderBy;

		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# get GRN record ID
	function  getGRNRec($selPOId)
	{
		$qry = " select id, store_entry from goods_receipt where po_id=$selPOId";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):""; 
	}
}
?>