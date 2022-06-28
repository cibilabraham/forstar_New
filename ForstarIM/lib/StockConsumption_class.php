<?php
class StockConsumption
{
	/****************************************************************
	This class deals with all the operations relating to Stock Consumption
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function StockConsumption(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function fetchStockConsumptionRecords($fromDate, $tillDate, $selStockId, $details, $summary)
	{
		if ($selStockId && ($details || $summary)) {
			$whr = " a.id=b.issuance_id and c.id=a.department_id and d.id=b.stock_id and a.created>='$fromDate' and a.created<='$tillDate' and b.stock_id='$selStockId'";
		} else {
			$whr = " a.id=b.issuance_id and c.id=a.department_id and d.id=b.stock_id and a.created>='$fromDate' and a.created<='$tillDate'";
		}

		if ($selStockId!="" && $summary!="") {
			$groupBy 	= "c.id, b.stock_id";
		} else if ($selStockId=="" && $summary!="") {
			$groupBy  = "b.stock_id";
		} else {
			$groupBy = "";
		}

		$orderBy = "d.name asc";

		if ($summary) $sumOption = "sum(b.quantity)";
		else $sumOption = "b.quantity";

		$qry = "select d.id, d.name, c.name, $sumOption, a.created, c.id from m_stockissuance a, stockissuance_entries b, m_department c, m_stock d ";

		if ($whr!="")
			$qry .= " where ".$whr;

		if ($groupBy!="")
			$qry .= " group by ".$groupBy;

		if ($orderBy!="")
			$qry .= " order by ".$orderBy;

		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}
	
}