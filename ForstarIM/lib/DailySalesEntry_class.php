<?php
class DailySalesEntry
{  
	/****************************************************************
	This class deals with all the operations relating to Daily Sales Entry
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DailySalesEntry(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert Daily Sales Main Entry Rec
	function addDailySalesMainEntry($entryDate, $selSalesStaffId, $userId)
	{
		$qry = "insert into t_dailysales_main (entry_date, sales_staff_id, created, createdby) values('$entryDate', '$selSalesStaffId', Now(), '$userId')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#For adding Daily sales Entry (Retail Counter Entry)
	function addDailySalesRtCounterEntry($salesEntryMainId, $selRtCounter, $visitDate, $visitTime, $selSchemeId, $poNum, $orderValue)
	{
		$qry =	"insert into t_dailysales_rtcounter (main_id, rt_counter_id, visit_date, visit_time, scheme_id, po_number, order_value) values('$salesEntryMainId', '$selRtCounter', '$visitDate', '$visitTime', '$selSchemeId', '$poNum', '$orderValue')";
		//echo $qry;			
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#For adding Daily sales Entry (Product Entry)
	function addDailySalesProductEntry($dailySalesRtctEntryId, $selProduct, $numStock, $numOrder, $balStk)
	{
		$qry =	"insert into t_dailysales_product (rtcounter_entry_id, product_id, stock_num, order_num, balance_stk) values('$dailySalesRtctEntryId', '$selProduct', '$numStock', '$numOrder', '$balStk')";
		//echo $qry;			
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}



	#Update sales Order Items
	function updateSalesOrderentries($salesOrderEntryId, $selProductId, $unitPrice, $quantity, $totalAmt)
	{
		$qry = "update t_dailysales_main_entry set product_id='$selProductId', rate='$unitPrice', quantity='$quantity', total_amount='$totalAmt' where id='$salesOrderEntryId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.entry_date, a.sales_staff_id, b.name from t_dailysales_main a, m_sales_staff b where a.sales_staff_id=b.id and a.entry_date>='$fromDate' and a.entry_date<='$tillDate' order by a.entry_date asc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Returns all Sales Order
	function fetchAllDateRangeRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, a.entry_date, a.sales_staff_id, b.name from t_dailysales_main a, m_sales_staff b where a.sales_staff_id=b.id and a.entry_date>='$fromDate' and a.entry_date<='$tillDate' order by a.entry_date asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Sales Order
	function fetchAllRecords()
	{
		$qry = "select a.id, a.entry_date, a.sales_staff_id, b.name from t_dailysales_main a, m_sales_staff b where a.sales_staff_id=b.id order by a.entry_date asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	
	
	# Get Sales Order based on Sales Order id 
	function find($dailySalesEntryId)
	{
		$qry = "select id, entry_date, sales_staff_id from t_dailysales_main where id=$dailySalesEntryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on $dailySalesEntryId Id from t_dailysales_rtcounter TABLE	
	function fetchAllDailySalesRtCtEntryRecs($dailySalesEntryId)
	{
		$qry = "select id, rt_counter_id, visit_date, visit_time, scheme_id, po_number, order_value from t_dailysales_rtcounter where main_id='$dailySalesEntryId' ";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Product Entry Records (t_dailysales_product)
	function fetchAllDailySalesProductEntryRecs($rtCounterEntryId)
	{
		$qry = "select id, product_id, stock_num, order_num, balance_stk from t_dailysales_product where rtcounter_entry_id='$rtCounterEntryId' ";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	# Update  Daily Sales Entry Main Rec
	function updateDailySalesMainEntrRec($salesEntryMainId, $entryDate, $selSalesStaffId)
	{
		$qry = "update t_dailysales_main set entry_date='$entryDate', sales_staff_id='$selSalesStaffId' where id='$salesEntryMainId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete all Entries
	function deleteDailySalesEntries($salesEntryMainId)
	{
		# Fetch all Daily sales Entry records
		$fetchAllSalesRtCtEntryRecs = $this->fetchAllDailySalesRtCtEntryRecs($salesEntryMainId);
		if (sizeof($fetchAllSalesRtCtEntryRecs)>0) {
			foreach ($fetchAllSalesRtCtEntryRecs as $rec) {
				$salesOrderRtCtEntryId = $rec[0];
				$deleteProductSalesEntryRec = $this->delProductEntryRec($salesOrderRtCtEntryId);
			}
		}
		# Delete Retailer Entries
		$delRtCounterEntryRecs = $this->delSalesRtCounterEntryRec($salesEntryMainId);		
		return true;
	}

	# Delete a Product For a Retailer
	function delProductEntryRec($salesOrderRtCtEntryId)
	{
		$qry = " delete from t_dailysales_product where rtcounter_entry_id=$salesOrderRtCtEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# delete Retailer Counter Entry For a Sales Staff
	function delSalesRtCounterEntryRec($salesEntryMainId)
	{
		$qry = " delete from t_dailysales_rtcounter where main_id=$salesEntryMainId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Daily sales Entry Main Rec
	function deleteDailySalesEntryMainRec($dailySalesEntryId)
	{
		$qry = " delete from t_dailysales_main where id=$dailySalesEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Get Prev Stock
	function getPrevStock($rtCounterId, $productId)
	{
		$qry = "select c.stock_num, c.order_num from t_dailysales_main a, t_dailysales_rtcounter b, t_dailysales_product c where a.id=b.main_id and b.id=c.rtcounter_entry_id and b.rt_counter_id='$rtCounterId' and c.product_id='$productId' order by a.entry_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):0;
	}
	
	# Get Price defined Product
	function fetchMrpProductRecs($productPriceRateListId)
	{
		$qry = "select a.product_id, b.code, b.name from m_product_price a, t_combo_matrix b where a.product_id=b.id and a.rate_list_id='$productPriceRateListId' order by b.name asc ";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchMRPSelectedProductRecs($rtCounterEntryId, $productPriceRateListId)
	{
		$qry = "select a.product_id, b.code, b.name, c.id, c.stock_num, c.order_num, c.balance_stk from m_product_price a join t_combo_matrix b on a.product_id=b.id left join t_dailysales_product c on a.product_id=c.product_id and rtcounter_entry_id='$rtCounterEntryId' where a.rate_list_id='$productPriceRateListId' order by b.name asc ";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getProductValue($rtCounterId, $productId, $selRateList)
	{
		$qry = " select mrp from m_product_price where product_id='$productId'";
		$rec = $this->databaseConnect->getRecord($qry);	
		
		$qry1 = " select margin from m_rtcounter_margin where retail_counter_id='$rtCounterId' and product_id='$productId' and rate_list_id='$selRateList'";	
		$mRec = $this->databaseConnect->getRecord($qry1);

		$calProductValue = $rec[0]-(($rec[0]*$mRec[0])/100);	
		return ($calProductValue>0)?$calProductValue:0;
	}

	function getEligibleSchemes($rtCounterId)
	{
		$cDate = date("Y-m-d");

		$qry = "select a.scheme_id, b.name, a.scheme_from, a.scheme_to from m_scheme_assign a, m_scheme b where a.scheme_id=b.id and (a.retailer_id='$rtCounterId' || a.retailer_id='0') and ('$cDate'>=date_format(a.scheme_from,'%Y-%m-%d') and '$cDate'<=date_format(a.scheme_to,'%Y-%m-%d') or '$cDate'>=date_format(a.scheme_from,'%Y-%m-%d') and '$cDate'<=date_format(a.scheme_to,'%Y-%m-%d')) order by b.name asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
		/*
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		*/
	}

	# Get Visited Rt Counters
	function getVisitedRtCounter($dailySalesEntryId)
	{
		$qry = "select a.rt_counter_id, b.name from t_dailysales_rtcounter a, m_retail_counter b where main_id='$dailySalesEntryId' and a.rt_counter_id=b.id order by b.name asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check same entry exist
	function chkEntryExist($entryDate, $selSalesStaffId, $salesEntryMainId)
	{
		if ($salesEntryMainId!="") $uptdQry = " and id!=$salesEntryMainId";
		else $uptdQry	= "";

		$qry = " select id from t_dailysales_main where entry_date='$entryDate' and sales_staff_id='$selSalesStaffId' $uptdQry";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Display Charge Of Retail Counter
	function getEligibleDisplayCharge($rtCounterId)
	{
		$cDate = date("Y-m-d");

		$qryD = "select a.charge from m_rtct_assign_dis_charge a, m_retail_counter b where a.retail_counter_id=b.id and a.retail_counter_id='$rtCounterId' and ('$cDate'>=date_format(a.from_date,'%Y-%m-%d') and '$cDate'<=date_format(a.till_date,'%Y-%m-%d') or '$cDate'>=date_format(a.from_date,'%Y-%m-%d') and '$cDate'<=date_format(a.till_date,'%Y-%m-%d')) and charge_type='D' order by b.name asc";

		$qryM	= "select a.charge from m_rtct_assign_dis_charge a, m_retail_counter b where a.retail_counter_id=b.id and a.retail_counter_id='$rtCounterId' and charge_type='M' order by b.name asc";
		//echo $qryD;		
		$recD = $this->databaseConnect->getRecord($qryD);
		if ($recD[0]!="") {
			return $recD[0];
		} else {
			$recM = $this->databaseConnect->getRecord($qryM);
			return $recM[0];
		}		
	}
	
}
?>