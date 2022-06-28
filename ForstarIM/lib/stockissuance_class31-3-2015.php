<?php
class StockIssuance
{  
	/****************************************************************
	This class deals with all the operations relating to Stock Issuance
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function StockIssuance(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Stock
	function addStockIssuance($requestNo, $selDepartment, $userId)
	{
		$qry	= "insert into m_stockissuance(requestno, department_id, created, createdby) values('$requestNo','$selDepartment', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	#For adding Issuance Items
	function addIssuanceEntries($lastId, $stockId, $exisitingQty, $quantity, $balanceQty, $currentStock)
	{
		$qry	=	"insert into stockissuance_entries (issuance_id, stock_id, existing_qty, quantity, balance_qty, current_stock) values('$lastId', '$stockId', '$exisitingQty', '$quantity', '$balanceQty', '$currentStock')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Stock Issuance
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id, requestno, department_id, created from m_stockissuance where created>='$fromDate' and created<='$tillDate' order by created desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, requestno, department_id, created from m_stockissuance where created>='$fromDate' and created<='$tillDate' order by created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Stock Issuance
	function fetchAllRecords()
	{
		$qry	= "select id, requestno, department_id, created from m_stockissuance order by created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
/*
#For Getting Total Amount Of Each Supplier
function fetchPurchaseOrderAmount($purchaseOrderId)
{
		$qry	=	"select stock_id,unit_price,quantity,total_amount,sum(total_amount) from purchaseorder_entry where po_id='$purchaseOrderId' group by po_id";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
*/


	# Get Supplier stock based on Supplier id 
	function find($orderId)
	{
		$qry	=	"select * from m_stockissuance where id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on PO Id from purchaseorder_entry TABLE	
	function fetchAllStockItem($editStockIssuanceId)
	{
		$qry	= "select id, issuance_id, stock_id, existing_qty, quantity, balance_qty from stockissuance_entries where issuance_id='$editStockIssuanceId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Delete  Stock Issuance Item  Recs
	function deleteIssuanceItemRecs($stockIssuanceId)
	{
		# find the received Qty 
		$this->getStockIssuanceQty($stockIssuanceId);

		$qry	= " delete from stockissuance_entries where issuance_id=$stockIssuanceId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}


	# Delete a Stock Issuance
	function deleteStockIssuance($stockIssuanceId)
	{
		$qry	=	" delete from m_stockissuance where id=$stockIssuanceId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
	

	# Update  a  Stock Issuance
	function updateStockIssuance($stockIssuanceId,$requestNo,$selDepartment)
	{
		$qry	= "update m_stockissuance set requestno='$requestNo', department_id='$selDepartment' where id='$stockIssuanceId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Get Total Qty of a Stock Item (usng in GRN)
	function  getTotalStockQty($stockId)
	{
		$qry = "select actual_quantity from m_stock where id='$stockId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	#Update the Balance Stock Qty
	function updateBalanceStockQty($stockId, $balanceQty)
	{
		$qry = "update m_stock set actual_quantity='$balanceQty' where id='$stockId'";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	/*************************************************/
	# get Stock Issued Qty
	function getStockIssuanceQty($stockIssuanceId)
	{
		$qry = " select stock_id, quantity from stockissuance_entries where issuance_id='$stockIssuanceId'";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);		
		foreach ($result as $rec) {
			$stockId 	= $rec[0];
			$qtyReceived 	= $rec[1];
			$updateStock = $this->updateMasterStockQty($stockId, $qtyReceived);
		}		
	}

	#Update the Master stock Qty
	function updateMasterStockQty($stockId, $qtyReceived)
	{
		$updateField = "";		
		if ($qtyReceived>0) $updateField = "actual_quantity=actual_quantity+$qtyReceived";
		$qry = "update m_stock set $updateField where id=$stockId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	/*************************************************/

	# Checking Unique Numbering
	function checkUnique($reqNumber, $hidReqNumber)
	{
		$addWhr = ( $hidReqNumber !="" ) ? " and requestno!='$hidReqNumber' " : "";
		$sqry = "select id from m_stockissuance where requestno='$reqNumber' $addWhr ";
		//echo $sqry;
		$srec = $this->databaseConnect->getRecord($sqry);
		return ( sizeof($srec)>0)?true:false;
	}

	function getTotalUnitStockQty($stockIdFrom,$unitTo)
	{

$qry="select actual_quantity from m_stock_plantunit where stock_id='$stockIdFrom' and plant_unit='$unitTo'";
//echo $qry;

$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

}
?>