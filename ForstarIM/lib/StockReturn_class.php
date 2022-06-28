<?php
class StockReturn
{  
	var $databaseConnect;

	function StockReturn(&$databaseConnect)
    {
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add New Stock Return
	function addStockReturn($requestNo, $selDepartment, $userId)
	{
		$qry	= "insert into stock_return (return_no, department_id, created, createdby) values('$requestNo','$selDepartment', Now(),'$userId')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	
	#For adding return items
	function addReturnEntries($srId, $stockId, $qty, $resonSel, $remark, $scrapValue, $totalAmt, $incCosting)
	{
		$qry	=	" insert into stock_return_entry (return_main_id, stock_id, quantity, reason_type, remark, scrap_value, total_amount, include_in_costing) values('$srId', '$stockId', '$qty', '$resonSel', '$remark', '$scrapValue','$totalAmt', '$incCosting') ";	

		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
		
	}


	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= " select id, return_no, department_id, created from stock_return where created>='$fromDate' and created<='$tillDate' order by id desc, created desc limit $offset, $limit ";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, return_no, department_id, created from stock_return where created>='$fromDate' and created<='$tillDate' order by created desc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	function getTotalVal($stkRetId, $field)
	{
		$qry = "SELECT sum($field) from stock_return_entry where return_main_id = $stkRetId ";
		$rec	=	$this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	/*
	# Returns all Stock Issuance
	function fetchAllRecords()
	{
		$qry	= "select id, requestno, department_id, created from m_stockissuance order by created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

	function checkUnique($reqNumber, $hidReqNumber)
	{
		$addWhr = ( $hidReqNumber !="" ) ? " and return_no!='$hidReqNumber' " : "";
		$sqry = "select id from stock_return where return_no='$reqNumber' $addWhr ";
		$srec = $this->databaseConnect->getRecord($sqry);
		return ( sizeof($srec) > 0 ) ? "$reqNumber already exists in the database." : "";
	}


	
	function find($retId)
	{
		$qry	=	"select * from stock_return where id=$retId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Fetch all return entry records
	function fetchAllStockItem($stkRetId)
	{
		$qry	= "select id, return_main_id, stock_id, scrap_value, quantity, reason_type, remark, total_amount, include_in_costing from stock_return_entry where return_main_id='$stkRetId' ";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function deleteReturnItemRecs($stkRetId)
	{
		$qry	= " delete from stock_return_entry where return_main_id=$stkRetId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteStockReturn($stkRetId)
	{
		$qry = " delete from stock_return where id=$stkRetId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	

	function updateStockReturn($stkRetId,$retNumber,$depId)
	{
		$qry	= "update stock_return set return_no='$retNumber', department_id='$depId' where id=$stkRetId ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function getIssuedQty($departmentId, $stockId)
	{
		//$qry = " select sum(a.quantity) from stockissuance_entries a, m_stockissuance b where a.issuance_id=b.id and b.department_id='$departmentId' and a.stock_id='$stockId'";

		$qry = " select sum(a.allot_qty) as allotQty from t_stock_issuance a, t_stock_requisition b where a.requistion_id=b.id and b.department='$departmentId' and a.stock_id='$stockId'";
		//echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);
		return  (sizeof($rec)>0)?$rec[0]:"";
		//printr($rec);
		//return $rec[0];
	}

	function getStockReturnedQty($departmentId, $stockId)
	{
		$qry = " select sum(a.quantity) from stock_return_entry a, stock_return b where a.return_main_id=b.id and b.department_id='$departmentId' and a.stock_id='$stockId'";
		//echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);
		return  (sizeof($rec)>0)?$rec[0]:0;
		//return ($rec[0]>0)?$rec[0]:0;	
	}
}
?>