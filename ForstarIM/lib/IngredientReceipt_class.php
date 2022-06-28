<?php
class IngredientReceipt
{  
	/****************************************************************
	This class deals with all the operations relating to Ingredient Receipt
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function IngredientReceipt(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Update the PO Status
	function updateStatus($poId, $selStatus)
	{
		$qry	= "update ing_purchaseorder set status='$selStatus' where id='$poId'";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Insert Ingredient Receipt and Status Update after Insertion
	function addIngredientReceipt($selPoId, $selDepartment, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry, $userId)
	{
		$qry	=	"insert into ing_receipt (po_id, department_id, billno, gate_entry, store_entry,rejected_gate_entry,created, createdby) values('$selPoId', '$selDepartment', '$billNo', '$gateEntryNo', '$storeEntry', '$rejectedEntry', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			#Update Order Status
			$this->updateStatus($selPoId,'R');
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	#For adding Received  Items
	function addReceivedEntries($lastId, $stockId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $unitPrice,$newUnitPrice, $totalAmt)
	{
		$qry	= "insert into ing_receipt_entry (ing_receipt_id, ingredient_id, quantity, qty_received, qty_rejected, remarks, current_stock, rate,new_rate, total_amt) values('$lastId', '$stockId', '$quantity', '$qtyReceived', '$qtyRejected', '$remarks', '$currentStock', '$unitPrice','$newUnitPrice', '$totalAmt')";
		//echo $qry;
		//die();	
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Updating Received  Items($ingReceiptEntryId, $ingredientId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $unitPrice)
	function updateReceivedEntry($ingReceiptEntryId, $ingredientId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $unitPrice,$newUnitPrice, $totalAmt )
	{
		//echo $newUnitPrice.','.$totalAmt;
		$qry = " update ing_receipt_entry set ingredient_id='$ingredientId', quantity='$quantity', qty_received='$qtyReceived', qty_rejected='$qtyRejected', remarks='$remarks', current_stock='$currentStock', rate='$unitPrice', total_amt='$totalAmt',new_rate='$newUnitPrice' where id = '$ingReceiptEntryId'";
		//echo $qry;
		//die();
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	# Returns all Ing Receipt Records (Pagination)
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.po_id, a.department_id, a.billno, a.gate_entry, a.store_entry, a.rejected_gate_entry, a.created, a.createdby, c.name, b.po from ing_receipt a, ing_purchaseorder b, supplier c where a.po_id=b.id and b.supplier_id=c.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.created desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Ing Receipt Records
	function fetchAllRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, a.po_id, a.department_id, a.billno, a.gate_entry, a.store_entry, a.rejected_gate_entry, a.created, a.createdby, c.name, b.po from ing_receipt a, ing_purchaseorder b, supplier c where a.po_id=b.id and b.supplier_id=c.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Supplier stock based on Supplier id
	function find($ingredientReceiptId)
	{
		$qry	= "select a.id, a.po_id, a.department_id, a.billno, a.gate_entry, a.store_entry, a.rejected_gate_entry, a.created, a.createdby from ing_receipt a where a.id=$ingredientReceiptId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	#Fetch All Records based on Ingredient Receipt Id from ing_receipt entries TABLE
	function fetchAllStockItem($ingredientReceiptId)
	{
		$qry	= "select id, ing_receipt_id, ingredient_id, quantity, qty_received, qty_rejected, remarks, current_stock, rate, total_amt,new_rate from ing_receipt_entry where ing_receipt_id='$ingredientReceiptId' ";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Delete  Goods received Item  Recs
	function deleteGoodsReceivedRecs($ingredientReceiptId)
	{
		#Update stock when delete Receipt entries
		/*$ingredientRecs = $this->fetchAllStockItem($ingredientReceiptId);
		if (sizeof($ingredientRecs)>0) {
			foreach ($ingredientRecs as $ir) {
				$ingredientId = $ir[2];
				$qtyReceived  = -$ir[4];
				$this->updateStockQty($ingredientId, $qtyReceived);
			}
		}*/
		$qry	=	" delete from ing_receipt_entry where ing_receipt_id=$ingredientReceiptId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}


	# Delete a Ingredient Receipt Rec
	function deleteIngredientReceipt($goodsReceiptId, $poId)
	{

		#Update stock when delete Receipt entries
		$ingredientRecs = $this->fetchAllStockItem($goodsReceiptId);
		if (sizeof($ingredientRecs)>0) {
			foreach ($ingredientRecs as $ir) {
				$ingredientId = $ir[2];
				$qtyReceived  = -$ir[4];
				$this->updateStockQty($ingredientId, $qtyReceived);
			}
		}

		$qry	=	" delete from ing_receipt where id=$goodsReceiptId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			#Update Order Status
			$this->updateStatus($poId,'P');
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	# Update  a  Ingredient Receipt Note
	function updateIngredientReceipt($ingredientReceiptId, $selPoId, $selDepartment, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry)
	{
		$qry	=	"update ing_receipt set po_id='$selPoId',department_id='$selDepartment',  billno='$billNo', gate_entry='$gateEntryNo', store_entry='$storeEntry', rejected_gate_entry='$rejectedEntry' where id='$ingredientReceiptId'";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Update the stock Qty [Qty>0 add stock else Less stock ] 
	function updateStockQty($ingredientId, $qtyReceived)
	{
		$updateField = "";
		
		if ($qtyReceived>0) $updateField = "actual_quantity=actual_quantity+$qtyReceived";
		else $updateField = "actual_quantity=actual_quantity-'".abs($qtyReceived)."'";

		$qry = "update m_ingredient set $updateField where id=$ingredientId";
		
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry;
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Returns all Pending Purchase Order
	function fetchAllPORecords($mode)
	{
		if ($mode=='A') $whr = "status='P'";
		else if ($mode=='E') $whr = "";

		$orderBy = " po desc";

		$qry	=	"select id, po, supplier_id, created, createdby, status from ing_purchaseorder";
		if ($whr!="") $qry .= " where ". $whr;
		if ($orderBy!="") $qry .= " order by". $orderBy;
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Received Ing Qty
	function getReceivedQtyOfIngredient($stockId , $poId)
	{
		$qry = "select b.qty_received from ing_receipt a join ing_receipt_entry b on a.id=b.ing_receipt_id and a.po_id=$poId and b.ingredient_id=$stockId";
		//echo $qry."<br>";
		$grnRecs = $this->databaseConnect->getRecords($qry);			
		if (sizeof($grnRecs)>0) {
			$totalRecdQty = 0;
			while (list(,$rec) = each ($grnRecs)) {
				$totalRecdQty = $totalRecdQty + $rec[0];
			}
			if ($totalRecdQty!="") return $totalRecdQty;
		}
		return 0;
	}

	// ----------------------------------
	# Ing Price Variation (Highest and Lowest Price)
	// ----------------------------------
	function getIngPriceVariation($ingredientId, $rateListId)
	{
		$maxQry = " select max(rate) from ing_receipt_entry where ingredient_id='$ingredientId'";
		$minQry = " select min(rate) from ing_receipt_entry where ingredient_id='$ingredientId'";
		$maxResult = $this->databaseConnect->getRecords($maxQry);
		if (sizeof($maxResult)>0) {
			$highestPrice = $maxResult[0][0];
			$updateIngRateMasterRec = $this->updateIngRateHighestPrice($ingredientId, $rateListId, $highestPrice);
		}
		$minResult = $this->databaseConnect->getRecords($minQry);
		if (sizeof($minResult)>0) {
			$lowestPrice = $minResult[0][0];
			$updateIngRateMasterRec = $this->updateIngRateLowestPrice($ingredientId, $rateListId, $lowestPrice);
		}
		return;		
	}

	# Update a Ingredient Rate Master highest Price
	function updateIngRateHighestPrice($ingredientId, $rateListId, $highestPrice)
	{
		$qry = "update m_ingredient_rate set highest_price='$highestPrice' where ingredient_id='$ingredientId' and rate_list_id='$rateListId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update a Ingredient Rate Master highest Price
	function updateIngRateLowestPrice($ingredientId, $rateListId, $lowestPrice)
	{
		$qry = "update m_ingredient_rate set lowest_price='$lowestPrice' where ingredient_id='$ingredientId' and rate_list_id='$rateListId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	// ----------------------- Price Variation Ends Here -----------------------

	#For adding stock qty
	function addStockQty($supplierIngId,$lastId,$entryId, $qtyReceived)
	{
		$qry	= "insert into m_supplier_ing_qty (supplier_ing_id, quantity,ing_recipe_id,ing_recipe_entryid,ingredient_date) values('$supplierIngId', '$qtyReceived', '$lastId','$entryId',Now())";
		//echo $qry;
		//die();
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	###get supplier ingredient id
	function getSupplierIngId($supplierId,$ingredientId)
	{
		$qry	= "select id from m_supplier_ing where ingredient_id='$ingredientId'  and supplier_id='$supplierId'";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	###update stock
	function updateStock($supplierIngId,$ingReceiptEntryId,$ingredientReceiptId,$qtyReceived)
	{
		$qry = "update m_supplier_ing_qty set quantity='$qtyReceived' where supplier_ing_id='$supplierIngId' and ing_recipe_entryid='$ingReceiptEntryId' and  ing_recipe_id='$ingredientReceiptId'";
		//echo $qry;
		//die();
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	###delete recod from suplier stock
	function deleteSuplierStockRecs($ingredientReceiptId)
	{
		$qry	=	" delete from m_supplier_ing_qty where ing_recipe_id=$ingredientReceiptId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			#Update Order Status
			$this->updateStatus($poId,'P');
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;

	}


}