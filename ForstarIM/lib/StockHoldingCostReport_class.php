<?php
class StockHoldingCostReport
{  
	/****************************************************************
	This class deals with all the operations relating to Stock Report
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function StockHoldingCostReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function filterStockRecord($stockId, $supplierId)
	{
		$qry	=	"select nego_price, schedule from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

/*
	function fetchStockRecords($averagePeriodType)
	{	
		$toDate = date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
		//$beforeOneYear = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")-1));
		if ($averagePeriodType=='Q') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-4, date("d"), date("Y")));
		} else if ($averagePeriodType=='H') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-6, date("d"), date("Y")));
		} else if ($averagePeriodType=='Y') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-12, date("d"), date("Y")));
		}	

		$qry = "select id, name, qty, sum(grnSum) as gSum, sum(siSum) as sSum, reOrderQty as reOrderPoint, actualQty as currentQty from ( select a.id as id, a.code as code, a.name as name, a.quantity as qty, sum(b.qty_received) as grnSum,0 as siSum, a.reorder as reOrderQty, a.actual_quantity as actualQty from m_stock a left join goods_receipt_entries b on a.id=b.stock_id right join goods_receipt c on (c.id=b.goods_receipt_id) and c.created>='$fromDate' and c.created<='$toDate' group by a.id
		union
		select a1.id as id, a1.code as code, a1.name as name, a1.quantity as qty, 0 as grnSum, sum(b1.quantity) as siSum, a1.reorder as reOrderQty, a1.actual_quantity as actualQty from m_stock a1 left join stockissuance_entries b1 on a1.id=b1.stock_id right join m_stockissuance c1 on c1.id=b1.issuance_id and c1.created>='$fromDate' and c1.created<='$toDate' group by a1.id
		) as X $where group by id order by name ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;

	}
	*/

	#Find the opening Qty (using in another Screens)
	function  getOpeningQty($stockId)
	{
		$qry = "select stockId, sum(qtyRe) as qReSum, sum(qtyRej) as qRejSum, sum(isuQty) as isuQtySum, openQty as openingQty, (openQty+sum(qtyRe))-sum(isuQty) from
		( select a.stock_id as stockId, sum(a.qty_received) as qtyRe, sum(a.qty_rejected) as qtyRej, 0 as isuQty, c.quantity as openQty from goods_receipt_entries a, goods_receipt b, m_stock c where c.id=a.stock_id and  b.id=a.goods_receipt_id and a.stock_id=$stockId group by a.stock_id
		union
		select a1.stock_id as stockId, 0 as qtyRe, 0 as qtyRej, sum(a1.quantity) as isuQty, c1.quantity as openQty  from stockissuance_entries a1, m_stockissuance b1, m_stock c1 where c1.id=a1.stock_id and b1.id=a1.issuance_id and a1.stock_id=$stockId group by a1.stock_id
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
		/* $qry = "select stockId, sum(qtyRe) as qReSum, sum(qtyRej) as qRejSum, sum(isuQty) as isuQtySum, openQty as openingQty, (openQty+sum(qtyRe))-sum(isuQty) from
		( select a.stock_id as stockId, sum(a.qty_received) as qtyRe, sum(a.qty_rejected) as qtyRej, 0 as isuQty, c.quantity as openQty from goods_receipt_entries a, goods_receipt b, m_stock c where c.id=a.stock_id and  b.id=a.goods_receipt_id and b.created<='$lastDate' and a.stock_id=$stockId group by a.stock_id
		union
		select a1.stock_id as stockId, 0 as qtyRe, 0 as qtyRej, sum(a1.quantity) as isuQty, c1.quantity as openQty  from stockissuance_entries a1, m_stockissuance b1, m_stock c1 where c1.id=a1.stock_id and b1.id=a1.issuance_id and b1.created<='$lastDate' and a1.stock_id=$stockId group by a1.stock_id
		) as X group by stockId ";

		*/
	}

	#Find the ReorderPoint and current Stock
	function  findReOrderPoint($stockId)
	{	
		$qry = "select reorder, actual_quantity from m_stock where id=$stockId";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1]):"";
	}

	/*
		C-System updation starts here
	*/
	function updateAveragePeriodType($averagePeriod, $excessStockTolerance)
	{
		$qry = " update c_system set average_period='$averagePeriod', stock_tolerance='$excessStockTolerance'";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	function getAveragePeriodType()
	{
		$qry = " select average_period, stock_tolerance from c_system";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";
	}
	/* C-System updation ends here */

	function getStockConsumedQty($stockId, $averagePeriodType)
	{
		$toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		if ($averagePeriodType=='Q') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-4, date("d"), date("Y")));
		} else if ($averagePeriodType=='H') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-6, date("d"), date("Y")));
		} else if ($averagePeriodType=='Y') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-12, date("d"), date("Y")));
		}
		$qry = "select sum(qty)/count(*) from (select sum(a.quantity) as qty, EXTRACT(YEAR_MONTH FROM b.created)  from stockissuance_entries a, m_stockissuance b where b.id=a.issuance_id and a.stock_id=$stockId and b.created>='$fromDate' and b.created<='$toDate' group by EXTRACT(YEAR_MONTH FROM b.created)) X";
		//echo $qry."<br>";
		 $rec = $this->databaseConnect->getRecord($qry);
		//echo "$rec[0]-".sizeof($rec)."<br>";
		return ($rec[0]>0)?$rec[0]:0;
	}

	# Get Latestest unit Price
	function getUnitPriceOfStock($stkId)
	{
		$unitPrice = 0;

		$qry1 = "select a.stock_id, d.stock_id, d.unit_price 
		from goods_receipt_entries a, goods_receipt b, m_purchaseorder c, purchaseorder_entry d  where a.goods_receipt_id=b.id and b.po_id=c.id and c.id=d.po_id and a.stock_id=d.stock_id and  a.stock_id=$stkId  order by b.created desc limit 1 ";
			
		$qry2 = "SELECT min(a.nego_price) FROM supplier_stock a, supplier b WHERE a.supplier_id = b.id AND stock_id=$stkId";

		$lsunitPriceRec = $this->databaseConnect->getRecords($qry1);
		if (sizeof($lsunitPriceRec) > 0 ) {
			$unitPrice = $lsunitPriceRec[0][2]; // find the last supplier price 
			//echo $qry1."<br>";
		} else  {
			$minUnitPriceRec = $this->databaseConnect->getRecords($qry2);
			if (sizeof($minUnitPriceRec) > 0) $unitPrice = $minUnitPriceRec[0][0]; // get the lowst price of this stock
			//echo $qry2."<br>";
		}
		return $unitPrice;
	}

	# Average return qty
	function  getAverageReturnQty($stockId, $stockingPeriod)
	{
		$toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-$stockingPeriod, date("d"), date("Y")));
		/*
		if ($averagePeriodType=='Q') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-4, date("d"), date("Y")));
		} else if ($averagePeriodType=='H') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-6, date("d"), date("Y")));
		} else if ($averagePeriodType=='Y') {
			$fromDate = date("Y-m-d", mktime(0, 0, 0, date("m")-12, date("d"), date("Y")));
		}
		*/
		$qry = "select sum(qty)/count(*) from (select sum(a.quantity) as qty, EXTRACT(YEAR_MONTH FROM b.created)  from stock_return_entry a, stock_return b where b.id=a.return_main_id and a.stock_id=$stockId and b.created>='$fromDate' and b.created<='$toDate' and a.include_in_costing='Y' group by EXTRACT(YEAR_MONTH FROM b.created)) X";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	# Returns all Stock
	function fetchAllActiveRecords($offset, $limit)
	{
		//$qry	= " select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.active='Y' order by a.name asc limit $offset,$limit";
		$qry	= " select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.active='Y' and a.activeconfirm=1 and b.active=1 and c.active=1 order by a.name asc limit $offset,$limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecords()
	{
		//$qry	= " select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.active='Y' order by a.name asc ";

		$qry	= " select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.active='Y' and b.active=1 and c.active=1 order by a.name asc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	/*
	# Stock Item Price Variation
	Price Variation = Yearly Average Price - Latest Stock Price
	*/
	function getStockItemPriceVariation($stockId)
	{
		$currentStockPrice 	= $this->getLastestPrice($stockId);
		$yearlyAveragePrice 	= $this->getYearlyAveragePrice($stockId);
		//echo "$currentStockPrice-$yearlyAveragePrice<br>";
		// modified $yearlyAveragePrice-$currentStockPrice
		return array($currentStockPrice, $yearlyAveragePrice);
	}

	# Get the Stock Latest Price
	function getLastestPrice($stockId)
	{
		$qry =  " select d.unit_price from goods_receipt_entries a, goods_receipt b, m_purchaseorder c, purchaseorder_entry d where a.goods_receipt_id=b.id and b.po_id=c.id and c.id=d.po_id and a.stock_id=d.stock_id and a.stock_id=$stockId order by b.created desc";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getYearlyAveragePrice($stockId)
	{
		$cDate = date("Y-m-d");
		$beforeOneYear = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")-1));
		$qry = " select sum(d.unit_price)/count(*) from goods_receipt_entries a, goods_receipt b, m_purchaseorder c, purchaseorder_entry d where a.goods_receipt_id=b.id and b.po_id=c.id and c.id=d.po_id and a.stock_id=d.stock_id and a.stock_id=$stockId and b.created>='$beforeOneYear' and b.created<='$cDate' group by a.stock_id";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;			
	}
	/* Ends here*/
}
?>