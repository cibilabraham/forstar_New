<?php
class DailySalesReport
{
	/****************************************************************
	This class deals with all the operations relating to Daily Sales Report
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function DailySalesReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function fetchDailySalesEntryRecords($fromDate, $tillDate, $selSalesStaffId)
	{

		$qry = "select a.id, a.entry_date, b.id, b.rt_counter_id, b.visit_date, b.visit_time, b.po_number, b.order_value from t_dailysales_main a, t_dailysales_rtcounter b where a.id=b.main_id and a.entry_date>='$fromDate' and a.entry_date<='$tillDate' and a.sales_staff_id='$selSalesStaffId' order by a.entry_date asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}

	function getCity($rtCounterId)
	{
		$qry = " select b.name from m_retail_counter a, m_city b where a.city_id=b.id and a.id='$rtCounterId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get the stock position of the Retail Counter
	function getStockPosition($rtCtEntryId, $comboMatrixRecId)
	{
		$qry = "select stock_num,order_num from t_dailysales_product where rtcounter_entry_id='$rtCtEntryId' and product_id='$comboMatrixRecId'";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";		
	}
	
}