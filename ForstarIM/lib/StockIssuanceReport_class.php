<?php
class StockIssuanceReport
{
	/****************************************************************
	This class deals with all the operations relating to Stock Purchase Reject
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function StockIssuanceReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	# get Stock Wise Issuance Records
	function getStockIssunaceRecords($fromDate, $tillDate, $stockId, $departmentId) 
	{
		$whr = "a.stock_id=c.id and b.department_id=d.id and a.issuance_id=b.id and b.created>='$fromDate' and b.created<='$tillDate'";

		if ($stockId!="") $whr .= " and a.stock_id=".$stockId;
		else if ($departmentId!="") $whr .= " and b.department_id=".$departmentId;
		else $whr .= "";
		
		if ($stockId!="") $groupBy = " b.department_id";
		else if ($departmentId!="") $groupBy = " a.stock_id";
		else $groupBy = "";

		if ($stockId!="") $orderBy = " d.name asc";
		else if ($departmentId!="") $orderBy = " c.name asc";
		else $orderBy = "";		

		$qry = " select a.stock_id, b.department_id, sum(a.quantity), c.name, d.name from stockissuance_entries a, m_stockissuance b, m_stock c, m_department d";

		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;		
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Wastage Rec Details
	function getWastageRecDetials($fromDate, $tillDate, $stockId, $departmentId)
	{
		$whr = " b.return_main_id = a.id and a.created>='$fromDate' and a.created<='$tillDate' and b.stock_id='".$stockId."' and a.department_id='$departmentId'";
		/*
		if ($stockId!="") $whr .= " and b.stock_id='".$stockId."' and a.department_id='$selDepartmentId'";
		else if ($departmentId!="") $whr .= " and a.department_id= '".$departmentId."' and  b.stock_id='$selStockId'";
		else $whr .= "";
		*/		
		$groupBy = " b.reason_type";

		$qry = " SELECT sum(b.quantity ), sum(b.scrap_value) ,sum(b.total_amount), b.reason_type from stock_return a, stock_return_entry b";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry."<br>";		
		$result	= array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

}
?>