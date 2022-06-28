<?php
class RetailCounterStock
{
	/****************************************************************
	This class deals with all the operations relating to Retail Counter
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RetailCounterStock(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	#Insert a Rec
	function addRetailCounterStock($selDate, $selDistributorId, $selRetailCounter, $userId)
	{
		$qry = "insert into t_retail_counter_stock (select_date, distributor_id, retail_counter_id, created, createdby) values('$selDate', '$selDistributorId', '$selRetailCounter',Now(), '$userId')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#For adding Stock Items
	function addRetailCounterStockEntries($lastId, $selProductId, $availableQty, $usedQty, $balanceQty)
	{
		$qry =	"insert into t_retail_counter_stock_entry (retail_stock_main_id, product_id, available_qty, used_qty, balance_qty) values('$lastId', '$selProductId', '$availableQty', '$usedQty', '$balanceQty')";
		//echo $qry;			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.select_date, a.distributor_id, a.retail_counter_id, b.name, c.name from t_retail_counter_stock a, m_distributor b, m_retail_counter c where c.id=a.retail_counter_id and a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' order by a.select_date desc limit $offset, $limit";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}


	# Returns all Sales Order
	function fetchAllDateRangeRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, a.select_date, a.distributor_id, a.retail_counter_id, b.name, c.name from t_retail_counter_stock a, m_distributor b, m_retail_counter c where c.id=a.retail_counter_id and a.distributor_id=b.id and a.select_date>='$fromDate' and a.select_date<='$tillDate' order by a.select_date desc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Sales Order
	function fetchAllRecords()
	{
		$qry = "select a.id, a.select_date, a.distributor_id, a.retail_counter_id, b.name, c.name from t_retail_counter_stock a, m_distributor b, m_retail_counter c where c.id=a.retail_counter_id and a.distributor_id=b.id order by a.select_date desc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Rec based on id 
	function find($retailCounterStockId)
	{
		$qry = "select id, select_date, distributor_id, retail_counter_id from t_retail_counter_stock where id=$retailCounterStockId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Fetch All Records based on main Id from t_retail_counter_stock_entry TABLE	
	function fetchAllRCStockItem($editRetailCounterStkId)
	{
		$qry = "select id, retail_stock_main_id, product_id, available_qty, used_qty, balance_qty from t_retail_counter_stock_entry where retail_stock_main_id='$editRetailCounterStkId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  A Rec
	function updateRetailCounterStockRec($retailCounterStockId, $selDate, $selDistributorId, $selRetailCounter)
	{
		$qry = " update t_retail_counter_stock set select_date='$selDate', distributor_id='$selDistributorId', retail_counter_id='$selRetailCounter' where id='$retailCounterStockId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	# Update Entry table
	function updateRetailCounterStockEntries($retailCounterStkEntryId, $selProductId, $availableQty, $usedQty, $balanceQty)
	{
		$qry = "update t_retail_counter_stock_entry set product_id='$selProductId', available_qty='$availableQty', used_qty='$usedQty', balance_qty='$balanceQty' where id='$retailCounterStkEntryId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	# Delete  Entry  Recs
	function deleteRetailCounterStockEntryRecs($retailCounterStockId)
	{
		$qry = " delete from t_retail_counter_stock_entry where retail_stock_main_id=$retailCounterStockId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Main Rec
	function deleteRetailCounterStockRec($retailCounterStockId)
	{
		$qry = " delete from t_retail_counter_stock where id=$retailCounterStockId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Filter Retail Counter recs based Distributor
	function filterRetailCounterRecs($selDistributorId)
	{
		$qry = " select id, code, name from m_retail_counter where distributor_id='$selDistributorId' ";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter Product Recs
	function filterProductRecs($selDistributorId)
	{
		//$qry = "select b.id, b.code, b.name from m_dist_product_price a, t_product_matrix b where a.product_id=b.id and a.distributor_id='$selDistributorId' and a.rate_list_id='$productPriceRateListId' ";
		
		$qry = "select mpm.id, mpm.code, mpm.name from m_product_manage mpm join m_product_mrp pmrp on mpm.id=pmrp.product_id group by pmrp.product_id";
		//echo "<br>$qry";		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete RC Stk Item
	function deleteRCStkItem($retailCounterStkEntryId)
	{
		$qry = " delete from t_retail_counter_stock_entry where id=$retailCounterStkEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
}
?>