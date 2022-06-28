<?php
class StockReport
{  
	/****************************************************************
	This class deals with all the operations relating to Stock Report
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function StockReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function filterStockRecord($stockId, $supplierId)
	{
		$qry	=	"select nego_price, schedule from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	function fetchStockRecords($selectDate, $stockReportType)
	{
		/* 
		SO	- Show out of stock
		SR	- Show reorder stock
		SA	- Show all
		*/
		if ($stockReportType=='SO') {
			$whr = " actualQty < 0";
		} else if ($stockReportType=='SR') {
			$whr = " actualQty < reOrderQty";
		} else {
			$whr = "";
		}

		if ($whr!="") $where = " where ".$whr;

		$qry = "select id, name, qty, sum(grnSum) as gSum, sum(siSum) as sSum, reOrderQty as reOrderPoint, actualQty as currentQty from ( select a.id as id, a.code as code, a.name as name, a.quantity as qty, sum(b.qty_received) as grnSum,0 as siSum, a.reorder as reOrderQty, a.actual_quantity as actualQty from m_stock a left join goods_receipt_entries b on a.id=b.stock_id right join goods_receipt c on (c.id=b.goods_receipt_id) and c.created='$selectDate' group by a.id
		union
		select a1.id as id, a1.code as code, a1.name as name, a1.quantity as qty, 0 as grnSum, sum(b1.quantity) as siSum, a1.reorder as reOrderQty, a1.actual_quantity as actualQty from m_stock a1 left join stockissuance_entries b1 on a1.id=b1.stock_id right join m_stockissuance c1 on c1.id=b1.issuance_id and c1.created='$selectDate' group by a1.id
		) as X $where group by id order by name ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;

		/*
		If no record in table
		$qry = "select id, name, qty, sum(grnSum) as gSum, sum(siSum) as sSum from ( select a.id as id, a.code as code, a.name as name, a.quantity as qty, sum(b.qty_received) as grnSum,0 as siSum from m_stock a left join goods_receipt_entries b on a.id=b.stock_id left join goods_receipt c on (c.id=b.goods_receipt_id) and c.created='$selectDate' group by a.id
		union
		select a1.id as id, a1.code as code, a1.name as name, a1.quantity as qty, 0 as grnSum,sum(b1.quantity) as siSum from m_stock a1 left join stockissuance_entries b1 on a1.id=b1.stock_id left join m_stockissuance c1 on c1.id=b1.issuance_id and c1.created='$selectDate' group by a1.id
		) as X group by id order by name ";

		//Correct Qry edited on 23-2-08 (if record)
		$qry = "select id, name, qty, sum(grnSum) as gSum, sum(siSum) as sSum from ( select a.id as id, a.code as code, a.name as name, a.quantity as qty, sum(b.qty_received) as grnSum,0 as siSum from m_stock a left join goods_receipt_entries b on a.id=b.stock_id right join goods_receipt c on (c.id=b.goods_receipt_id) and c.created='$selectDate' group by a.id
		union
		select a1.id as id, a1.code as code, a1.name as name, a1.quantity as qty, 0 as grnSum,sum(b1.quantity) as siSum from m_stock a1 left join stockissuance_entries b1 on a1.id=b1.stock_id right join m_stockissuance c1 on c1.id=b1.issuance_id and c1.created='$selectDate' group by a1.id
		) as X group by id order by name ";*/
	}

	#Find the opening Qty (using in another Screens)
	function  getOpeningQty($stockId, $lastDate)
	{
		$qry = "select stockId, sum(qtyRe) as qReSum, sum(qtyRej) as qRejSum, sum(isuQty) as isuQtySum, openQty as openingQty, (openQty+sum(qtyRe))-sum(isuQty) from
		( select a.stock_id as stockId, sum(a.qty_received) as qtyRe, sum(a.qty_rejected) as qtyRej, 0 as isuQty, c.quantity as openQty from goods_receipt_entries a, goods_receipt b, m_stock c where c.id=a.stock_id and  b.id=a.goods_receipt_id and b.created<='$lastDate' and a.stock_id=$stockId group by a.stock_id
		union
		select a1.stock_id as stockId, 0 as qtyRe, 0 as qtyRej, sum(a1.quantity) as isuQty, c1.quantity as openQty  from stockissuance_entries a1, m_stockissuance b1, m_stock c1 where c1.id=a1.stock_id and b1.id=a1.issuance_id and b1.created<='$lastDate' and a1.stock_id=$stockId group by a1.stock_id
		) as X group by stockId ";
		# Find opening Qty
		$openingQry = "select a.quantity from m_stock a where a.id='$stockId' ";
		//echo $qry."<br>";
		$issuanceRec = $this->databaseConnect->getRecord($qry);
		if (sizeof($issuanceRec)>0) {			
			return $issuanceRec[5];
		} else {
			$stockRec = $this->databaseConnect->getRecord($openingQry);
			return $stockRec[0];
		}
		/* Edited on 21-06-08 Shobu
		$qry1 = "select a.quantity from m_stock a where a.id='$stockId' ";
		$qry2 = "select a.current_stock from goods_receipt_entries a, goods_receipt b where a.goods_receipt_id=b.id and a.stock_id='$stockId' and b.created<'$lastDate' order by b.id desc ";
		$qry3 = "select a.current_stock from stockissuance_entries a, m_stockissuance b where a.issuance_id=b.id and a.stock_id='$stockId' and b.created='$lastDate' order by b.id desc ";
		//echo $qry3."<br>";

		$issuanceRec = $this->databaseConnect->getRecord($qry3);
		if (sizeof($issuanceRec)>0) {
			//echo "Here1";
			return $issuanceRec[0];
		} else {
			$grnRec = $this->databaseConnect->getRecord($qry2);	
			//echo $qry2;
			if (sizeof($grnRec)>0) {
				//echo "Here2";
				return $grnRec[0];
			} else {
				//echo "Here3";
				$stockRec = $this->databaseConnect->getRecord($qry1);
				return $stockRec[0];
			}
		}

		*/
	}

	#Find the ReorderPoint and current Stock
	function  findReOrderPoint($stockId)
	{	
		$qry = "select reorder, actual_quantity from m_stock where id=$stockId";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1]):"";
	}

	function checkSupplierExistForStock($stockId)
	{
		$qry = "select id, supplier_id, stock_id from supplier_stock where stock_id=$stockId and supplier_id!=0";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);				
	}
}
?>