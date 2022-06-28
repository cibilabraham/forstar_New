<?php
class StockSummary
{
	/****************************************************************
	This class deals with all the operations relating to Stock Purchase Reject
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function StockSummary(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function fetchStkSummaryRecords($fromDate, $tillDate, $selStockId)
	{
		$qry = "select stockId, sum(qtyRe) as qReSum, sum(qtyRej) as qRejSum, sum(isuQty) as isuQtySum from
		( select a.stock_id as stockId, sum(a.qty_received) as qtyRe, sum(a.qty_rejected) as qtyRej, 0 as isuQty  from goods_receipt_entries a, goods_receipt b where b.id=a.goods_receipt_id and b.created>='$fromDate' and b.created<='$tillDate' and a.stock_id=$selStockId group by a.stock_id
		union
		select a1.stock_id as stockId, 0 as qtyRe, 0 as qtyRej, sum(a1.quantity) as isuQty  from stockissuance_entries a1, m_stockissuance b1 where b1.id=a1.issuance_id and b1.created>='$fromDate' and b1.created<='$tillDate' and a1.stock_id=$selStockId group by a1.stock_id
		) as X group by stockId ";
				
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));

		//$issuanceQty = $this->getStockIssuanceQty($fromDate, $tillDate, $selStockId);
	}

	function getStockIssuanceQty($fromDate, $tillDate, $selStockId)
	{
		$qry = "select sum(b.quantity) from m_stockissuance a, stockissuance_entries b where a.id=b.issuance_id and  a.created>='$fromDate' and a.created<='$tillDate' and b.stock_id='$selStockId' group by b.stock_id";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
}
?>