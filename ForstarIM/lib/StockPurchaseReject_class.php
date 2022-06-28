<?php
class StockPurchaseReject
{
	/****************************************************************
	This class deals with all the operations relating to Stock Purchase Reject
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function StockPurchaseReject(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function fetchStkPurchaseRejectRecords($fromDate, $tillDate, $selSupplierId)
	{

		$qry = "select a.id, d.id, d.name, sum(b.qty_received), sum(b.qty_rejected), sum(b.quantity) from goods_receipt a, goods_receipt_entries b, m_purchaseorder c, m_stock d where a.id=b.goods_receipt_id and a.po_id=c.id and d.id=b.stock_id and a.created>='$fromDate' and a.created<='$tillDate' and c.supplier_id='$selSupplierId' group by b.stock_id order by d.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}
		
	
}