<?php
class RevisePurchaseOrder
{
	/****************************************************************
	This class deals with all the operations relating to Supplier Stock
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RevisePurchaseOrder(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

/*
	# Returns all Supplier Paging Stock
	function fetchAllPagingRecords($offset, $limit, $supplierFilterId, $supplierRateListFilterId)
	{
		
		$whr = " b.id=a.supplier_id and c.id=a.stock_id ";

		if ($supplierFilterId=="") $whr .= "";
		else $whr .= " and a.supplier_id=".$supplierFilterId;

		if ($supplierRateListFilterId=="") $whr .= "";
		else $whr .= " and a.rate_list_id=".$supplierRateListFilterId;

		$orderBy 	= " b.name asc, c.name asc";
		$limit 		= " $offset,$limit";

		$qry = "select  a.id, a.supplier_id, a.stock_id, a.quote_price, a.nego_price, a.excise_rate, a.cst, a.schedule, a.packing_rate, a.packing_conv_rate, a.remark, a.stock_type, b.name, c.name from supplier_stock a, supplier b, m_stock c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Returns all Supplier Paging Stock
	function fetchAllFilterRecords($supplierFilterId, $supplierRateListFilterId)
	{
		
		$whr = " b.id=a.supplier_id and c.id=a.stock_id";

		if ($supplierFilterId=="") $whr .= "";
		else $whr .= " and a.supplier_id=".$supplierFilterId;

		if ($supplierRateListFilterId=="") $whr .= "";
		else $whr .= " and a.rate_list_id=".$supplierRateListFilterId;

		$orderBy 	= " b.name asc, c.name asc";		

		$qry = "select  a.id, a.supplier_id, a.stock_id, a.quote_price, a.nego_price, a.excise_rate, a.cst, a.schedule, a.packing_rate, a.packing_conv_rate, a.remark, a.stock_type, b.name, c.name from supplier_stock a, supplier b, m_stock c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
				
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
*/

	/*------------------------------------------------------------------------------------*/
	# Get Not Received PO records
	function getNotRececivedPORecords($supplierId)
	{
		$qry = " select id, po, status, supplier_id, used_rate_list_id from m_purchaseorder where status!='R' and supplier_id='$supplierId' order by po asc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}
	# Get Rate List
	function getRateList($rateListId)
	{
		$qry	= "select id, name, start_date from m_supplier_ratelist where id=$rateListId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$array			=	explode("-", $rec[2]);
			$startDate		=	$array[2]."/".$array[1]."/".$array[0];
			$displayRateList =  $rec[1]."&nbsp;(".$startDate.")";
		}
		return (sizeof($rec)>0)?$displayRateList:"";
	}

	#---------- Update PO Recs Starts Here ----------------------------
	function updatePORecs($poMainId, $currentRateListId, $supplierId)
	{
		$updatePOMainRec = $this->updatePOMainRec($poMainId, $currentRateListId);	
		
		$getPOStockRecs  = $this->fetchAllStockItem($poMainId);
		
		if (sizeof($getPOStockRecs)>0) {
			foreach ($getPOStockRecs as $por) {
				$poEntryId = $por[0];
				$stockId   = $por[2];
				$qty 	   = $por[4];
				$unitPrice = $this->getUnitPrice($supplierId, $stockId, $currentRateListId);
				$totalAmt = $qty * $unitPrice;
				$updatePOEntryRec = $this->updatePOEntryRec($unitPrice, $totalAmt, $poEntryId);	
			}
		}		
		if ($updatePOMainRec && $updatePOEntryRec) return true;		
		else return false;
	}
	
	# Update m_purchaseorder table
	function updatePOMainRec($poMainId, $currentRateListId)
	{
		$qry = " update m_purchaseorder set used_rate_list_id='$currentRateListId',revise_need='N' where id='$poMainId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();		
		}
		return $result;	
	}


	#Fetch All Records based on PO Id from purchaseorder_entry TABLE	
	function fetchAllStockItem($purchaseOrderId)
	{
		$qry	= "select id, po_id, stock_id, unit_price, quantity, total_amount from purchaseorder_entry where po_id='$purchaseOrderId' ";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getUnitPrice($supplierId, $stockId, $currentRateListId)
	{
		$qry = " select nego_price from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId' and rate_list_id='$currentRateListId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec>0)?$rec[0]:0;
	}

	function updatePOEntryRec($unitPrice, $totalAmt, $poEntryId)
	{
		$qry = " update purchaseorder_entry set unit_price='$unitPrice', total_amount='$totalAmt' where id='$poEntryId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	#---------- Update PO Recs Ends Here ----------------------------

	function getPurchaseOrderRecords($poMainId)
	{
		$qry = " select id, po_id, stock_id, unit_price, quantity, total_amount from purchaseorder_entry where po_id='$poMainId' ";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	function getReceivedQtyOfStock($stockId, $poId)
	{
		$qry = "select b.qty_received from goods_receipt a join goods_receipt_entries b on b.goods_receipt_id = a.id  and a.po_id=$poId and b.stock_id=$stockId";
		$grnRecs = $this->databaseConnect->getRecords($qry);	
		
		if( sizeof($grnRecs) > 0 )
		{
			$totalRecdQty = 0;
			while( list(,$rec) = each ($grnRecs) )
			{
				$totalRecdQty = $totalRecdQty + $rec[0];
			}
			if( $totalRecdQty!="" ) return $totalRecdQty;
		}
		return 0;
	}

	#Insert PO Rec
	function addPurchaseOrder($purchaseOrderNo, $selSupplierId, $userId, $supplierRateListId, $poMainId)
	{
		$qry = "insert into m_purchaseorder (po, supplier_id, created, createdby, status, used_rate_list_id, base_po_id, revise_need) values('$purchaseOrderNo', '$selSupplierId', Now(),'$userId','P', '$supplierRateListId', '$poMainId', 'N')";
		//echo $qry."<br>";			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			// Update PO Status
			$this->updatePOStatus($poMainId, 'R');
			$this->updatePOReviseNeed($poMainId, 'N');
		} else {
			$this->databaseConnect->rollback();		
		}
		return $insertStatus;
	}

	#For adding Purchae Items
	function addPurchaseEntries($lastId, $stockId, $unitPrice, $quantity, $totalAmt)
	{
		$qry	= "insert into purchaseorder_entry (po_id, stock_id, unit_price, quantity, total_amount) values('$lastId', '$stockId', '$unitPrice', '$quantity', '$totalAmt')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Update the PO Status
	function updatePOStatus($poId, $selStatus)
	{
		$qry	= "update m_purchaseorder set status='$selStatus' where id='$poId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update PO Revise
	function updatePOReviseNeed($purchaseOrderId, $flag)
	{
		$qry	= "update m_purchaseorder set revise_need='$flag' where id='$purchaseOrderId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

}
?>
