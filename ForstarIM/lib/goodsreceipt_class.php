<?php
class GoodsReceipt
{  
	/****************************************************************
	This class deals with all the operations relating to Goods Receipt Note
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function GoodsReceipt(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Update the PO Status
	function updateStatus($poId, $selStatus)
	{
		$qry	=	"update m_purchaseorder set status='$selStatus' where id='$poId'";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Insert Goods Receipt and Status Update after Insertion
	function addGoodsReceipt($selPoId, $selDepartment, $challanNo, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry, $userId, $grnRemarks,$companyId,$plantId)
	{
		$qry	=	"insert into goods_receipt (po_id,department_id, challanno, billno, gate_entry, store_entry,rejected_gate_entry,created, createdby, remarks,company_id,unit_id) values('$selPoId','$selDepartment','$challanNo','$billNo','$gateEntryNo','$storeEntry', '$rejectedEntry', Now(),'$userId', '$grnRemarks','$companyId','$plantId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			//#Update Order Status 6/30/2008
			//$this->updateStatus($selPoId,'R');
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	#For adding Received  Items
	function addReceivedEntries($lastId, $stockId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $checkPoint,$confirmation,$notover,$extraquantity,$quantityafterextra)
	{
		$qry	= "insert into goods_receipt_entries (goods_receipt_id, stock_id, quantity, qty_received, qty_rejected, remarks, current_stock, chk_point,confirmation,notover,extraquantity,quantityafterextra) values('$lastId', '$stockId', '$quantity', '$qtyReceived', '$qtyRejected', '$remarks', '$currentStock', '$checkPoint','$confirmation','$notover','$extraquantity','$quantityafterextra')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		//echo $qry;
		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 		 	$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add GR Check Point Recs
	function addGRCheckPoint($goodsReceiptEntryId, $chkPointId, $chkPointAnswer, $chkPointRemarks)
	{
		$qry	=	"insert into gr_entry_chkpoint (gr_entry_id, check_point_id, answer, remark) values('$goodsReceiptEntryId', '$chkPointId', '$chkPointAnswer', '$chkPointRemarks')";

		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select id, po_id, department_id, challanno, billno, gate_entry, store_entry, rejected_gate_entry, created, createdby from goods_receipt where created>='$fromDate' and created<='$tillDate' order by created desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	// For Count total Number of Records
	function fetchAllDateRangeRecords($fromDate, $tillDate)
	{
		$qry = "select id, po_id, department_id, challanno, billno, gate_entry, store_entry, rejected_gate_entry, created, createdby from goods_receipt where created>='$fromDate' and created<='$tillDate' order by created desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Goods Receipt Note Records
	function fetchAllRecords()
	{
		$qry = "select id, po_id, department_id, challanno, billno, gate_entry, store_entry, rejected_gate_entry, created, createdby from goods_receipt order by created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Supplier stock based on Supplier id
	function find($goodsReceiptId)
	{
		$qry = "select id, po_id, department_id, challanno, billno, gate_entry, store_entry, rejected_gate_entry, created, createdby, remarks from goods_receipt where id=$goodsReceiptId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Fetch All Records based on Goods Receipt Id Id from goods_receipt entries TABLE	
	function fetchAllStockItem($editGoodsReceiptId)
	{
		$qry = "select id, goods_receipt_id, stock_id, quantity, qty_received, qty_rejected, remarks, current_stock,confirmation,notover,extraquantity,quantityafterextra from goods_receipt_entries where goods_receipt_id='$editGoodsReceiptId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Delete  Goods received Item  Recs
	function deleteGoodsReceivedRecs($goodsReceiptId)
	{
		# find the received Qty 
		$this->getStockReceivedQty($goodsReceiptId);

		$deleteCheckPointRecs = $this->deleteCheckPointRecs($goodsReceiptId);

		$qry = " delete from goods_receipt_entries where goods_receipt_id=$goodsReceiptId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}


	# Delete a Goods Receipt Rec
	function deleteGoodsReceipt($goodsReceiptId)
	{
		$qry	=	" delete from goods_receipt where id=$goodsReceiptId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Check Point Recs
	function deleteCheckPointRecs($goodsReceiptId)
	{
		$goodsReceiptRecs = $this->fetchAllStockItem($goodsReceiptId);
		foreach ($goodsReceiptRecs as $grr) {
			$goodsReceiptEntryId =	$grr[0];
			$delCheckPointEntry = $this->delCheckPointEntryRec($goodsReceiptEntryId);
		}
	}

	# Del Each Record
	function delCheckPointEntryRec($goodsReceiptEntryId)
	{
		$qry	=	" delete from gr_entry_chkpoint where gr_entry_id=$goodsReceiptEntryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}

	# Del supplier stock
	function deleteSupplierStock($goodsReceiptId)
	{
		$qry	=	" delete from supplier_stock_quantity where goods_receipt_id=$goodsReceiptId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Goods Receipt Note
	function updateGoodsReceipt($goodsReceiptId, $selDepartment, $challanNo, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry, $grnRemarks)
	{
		$qry = "update goods_receipt set department_id='$selDepartment', challanno='$challanNo', billno='$billNo', gate_entry='$gateEntryNo', store_entry='$storeEntry', rejected_gate_entry='$rejectedEntry', remarks='$grnRemarks' where id='$goodsReceiptId'";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}

	#Update the stock Qty [Qty>0 add stock else Less stock ] - Linked with Stock Issuance
	function updateStockQty($stockId,$qtyReceived,$plantId)
	{
		/*$updateField = "";
		
		if ($qtyReceived>0) $updateField = "actual_quantity=actual_quantity+$qtyReceived";
		else $updateField = "actual_quantity=actual_quantity-'".abs($qtyReceived)."'";

		$qry = "update m_stock set $updateField where id=$stockId";*/

		$qry="update m_stock_plantunit set actual_quantity=actual_quantity+$qtyReceived where stock_id='$stockId' and plant_unit='$plantId'";
		//echo $qry;
		
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	# Returns all Purchase Order
	function fetchAllNotReceivedPORecords()
	{
		$qry = "select id, po, po_number, supplier_id, created, createdby, status from m_purchaseorder where status!='R' order by po desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Purchase Order
	function fetchAllPORecords($id)
	{
		$qry = "select id, po, po_number, supplier_id, created, createdby, status from m_purchaseorder where confirmed='1' ";
		if($id!="")
		{
			$qry.= "and id in (select po_id from goods_receipt)";
		}
		else
		{
			$qry.= " and id not in(select po_id from goods_receipt)";
		}
		$qry.= "order by po desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# get received Qty
	function getStockReceivedQty($goodsReceiptId)
	{
		$qry = " select stock_id, qty_received from goods_receipt_entries where goods_receipt_id='$goodsReceiptId'";
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
		if ($qtyReceived>0) $updateField = "actual_quantity=actual_quantity-$qtyReceived";
		$qry = "update m_stock set $updateField where id=$stockId";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update pucrchase order status
	function updatePOStatus($poId)
	{
		$qry = "update m_purchaseorder set status='P' where id=$poId";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Check Chellan Number Exist or not 
	function checkChallanNumberExist($newChellan, $existChellan)
	{
		$qry = " select id from goods_receipt where challanno='$newChellan' and challanno!='$existChellan' ";
		$result	= $this->databaseConnect->getRecords($qry);	
		if( sizeof( $result ) > 0 ) return $newChellan.' already exists in the database.';
		return '';
	}
	
	# Check Gate Entry Number Exist or not 
	function checkGateEntryNumberExist($newGateEntry, $existGateEntry)
	{
		$qry = " select id from goods_receipt where gate_entry='$newGateEntry' and gate_entry!='$existGateEntry' ";
		$result	= $this->databaseConnect->getRecords($qry);	
		if( sizeof( $result ) > 0 ) return $newGateEntry.' already exists in the database.';
		return '';

	}

	# Check Gate Store Number Exist or not 
	function checkStoreEntryNumberExist($newStoreEntry, $existStoreEntry)
	{
		$qry = " select id from goods_receipt where store_entry='$newStoreEntry' and store_entry!='$existStoreEntry' ";
		$result	= $this->databaseConnect->getRecords($qry);	
		if( sizeof( $result ) > 0 ) return $newStoreEntry.' already exists in the database.';
		return '';

	}

	# Check Rejected Material Gate pass Number Exist or not 
	function checkRMGatePassNumberExist($newRMGatePass, $existRMGatePass)
	{
		$qry = " select id from goods_receipt where rejected_gate_entry='$newRMGatePass' and rejected_gate_entry!='$existRMGatePass' ";
		$result	= $this->databaseConnect->getRecords($qry);	
		if( sizeof( $result ) > 0 ) return $newRMGatePass.' already exists in the database.';
		return '';

	}

	/*function getExpectedQtyOfStock($stockId,$quantity,$poId,$addMode)
	{
		//if( ! $addMode ) return $quantity;
		$expQty = $quantity;
		// check existing GRN for this PO id 
		$qry = "select id from goods_receipt where po_id=$poId ";
		$grnRecs	=	$this->databaseConnect->getRecords($qry);	

		if( sizeof($grnRecs)  > 0 ) // if so, fetch the stock details of that grn id & stock id 
		{
			$grnId = $grnRecs[0][0];
			$qry1 = "select id, goods_receipt_id, stock_id, quantity, qty_received, qty_rejected, remarks, current_stock from goods_receipt_entries where goods_receipt_id = $grnId and stock_id = $stockId";
			$grnEntryRecs	=	$this->databaseConnect->getRecords($qry1);
			if( sizeof($grnEntryRecs) > 0 )
			{
				$actQty = 0;
				while( list(,$grne )  = each ($grnEntryRecs) )
				{
					$actQty += $grne[3];
				}
				//echo "ActQty=$actQty";

				if( $actQty !="" ) $expQty = $expQty-$actQty;
				if( $expQty < 0 ) $expQty = 0;
			}
		}

		return $expQty;
	}*/

	function getReceivedQtyOfStock($stockId,$poId)
	{
		$qry = "select b.qty_received from goods_receipt a join goods_receipt_entries b on b.goods_receipt_id = a.id  and a.po_id=$poId and b.stock_id=$stockId";
		$grnRecs = $this->databaseConnect->getRecords($qry);	
		//echo $qry;
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

	function updateGRNStatus($poId,$poTotalQty)
	{
		
		$qry = "select a.id, b.quantity from goods_receipt a join goods_receipt_entries b on b.goods_receipt_id = a.id  and a.po_id=$poId ";
		$grnRecs = $this->databaseConnect->getRecords($qry);	
		
		if( sizeof($grnRecs) > 0 )
		{
			$totalGRNQty = 0;
			while( list(,$rec) = each ($grnRecs) )
			{
				$totalGRNQty = $totalGRNQty + $rec[1];
			}
			if( $totalGRNQty > $poTotalQty  || $poTotalQty==$totalGRNQty ) $this->updateStatus($poId, 'R');
			else $this->updateStatus($poId, 'PC');
		}
	}

	#Update Received  Items
	function updateReceivedItemRec($goodsReceiptId, $stockId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $grnEntryId, $checkPoint,$confirmation,$notover,$extraquantity,$quantityafterextra)
	{
		$qry = " update goods_receipt_entries set goods_receipt_id='$goodsReceiptId', stock_id='$stockId', quantity='$quantity', qty_received='$qtyReceived', qty_rejected='$qtyRejected', remarks='$remarks', current_stock='$currentStock', chk_point='$checkPoint',confirmation='$confirmation',notover='$notover',extraquantity='$extraquantity',quantityafterextra='$quantityafterextra' where id='$grnEntryId'";
		//echo $qry;
					
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Check the Stock Item has Check Point
	function checkPointExist($stockId)
	{
		$qry = " select b.id from m_stock a, stock_subcategory b where a.subcategory_id=b.id and b.check_point='Y' and a.id='$stockId' ";
		//echo "$qry<br>";
		$rec	= $this->databaseConnect->getRecord($qry);	
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get Check Point Recs
	function getCheckPointRecs($subCategoryId)
	{
		//$qry = " select a.id, a.check_point_id, b.name from stk_subcategory_chkpoint a, m_check_point b where a.check_point_id=b.id and subcategory_id='$subCategoryId' ";
		$qry = " select a.id, a.check_point_id, b.name from stk_subcategory_chkpoint a, m_check_point b where a.check_point_id=b.id and subcategory_id='$subCategoryId' and b.active=1 ";
		//echo "$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Selected Chk Points
	function getSelCheckPointRecs($goodsReceiptEntryId)
	{
		$qry = "select id, check_point_id, answer, remark from gr_entry_chkpoint where gr_entry_id='$goodsReceiptEntryId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update
	function updateGRCheckPoint($goodsReceiptEntryId, $chkPointId, $chkPointAnswer, $chkPointRemarks, $grCPEntryId)
	{		
		$qry = " update gr_entry_chkpoint set gr_entry_id='$goodsReceiptEntryId', check_point_id='$chkPointId', answer='$chkPointAnswer', remark='$chkPointRemarks' where id='$grCPEntryId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Check Point Exist
	function checkGRNCheckPointExist($goodsReceiptId)
	{
		$qry = " select id from goods_receipt_entries where goods_receipt_id='$goodsReceiptId' and chk_point='Y'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function addStockQty($stockId,$qtyReceived,$plantId)
	{
	$qry="insert into m_stock_plantunit(stock_id,plant_unit,actual_quantity,openingquantity) values ('$stockId','$plantId','$qtyReceived','$qtyReceived')";
	//echo $qry;
	$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $insertStatus;

	}

	
	###get all unit assigned for user in manageuser
	function getAllDepartmentUser($userId)
	{	$arrayVal=array();
		$qry = "select department_id from user_details where user_id='$userId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		if(sizeof($result>0))
		{
			foreach($result as $res)
			{
				if($res[0]=='0')
				{
					$query = "select id,name  from m_department where active='1'";
					$rest	= $this->databaseConnect->getRecords($query);
					foreach($rest as $rt)
					{
						$id=$rt[0];
						$name=$rt[1];
						$arrayVal[$id]=$name;
					}
					
				}
				else
				{
					$query = "select id,name  from m_department where id='".$res[0]."'";
					$rests	= $this->databaseConnect->getRecords($query);
					foreach($rests as $rts)
					{
						$id=$rts[0];
						$name=$rts[1];
						//echo $id.','.$name;
						$arrayVal[$id]=$name;
					}
					
				}
				
			}
			
		}
		return $arrayVal;
		//return $result;
	}

# Returns all Purchase Order
	function fetchPOList($editPurchaseOrderId)
	{
		$qry="select a.id, a.po, a.po_number, a.supplier_id, a.created, a.createdby, a.status, a.used_rate_list_id,a.remarks,a.nettotal,a.billing_company_id,b.display_name,a.unitInv,c.name from m_purchaseorder a left join m_billing_company b on a.billing_company_id=b.id left join m_plant c on a.unitInv=c.id where a.id='$editPurchaseOrderId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	function chkValidDateEntry($supplierId,$selStockId)
	{
		$seldate=date("Y-m-d");
		$qry	= "select a.id, a.nego_price, a.start_date from supplier_stock a where (('$seldate'>=a.start_date && (a.end_date is null || a.end_date=0)) or ('$seldate'>=a.start_date and '$seldate'<=a.end_date)) and supplier_id='$supplierId' and stock_id='$selStockId' order by a.start_date desc";
		//echo $qry."<br>";
		//die();
		$result	=	$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	#Add a Supplier stock Qty
	function addSupplierStock($supplierStockId,$supplier,$item,$quantity,$stockDate,$goodsReceiptId,$companyUnitId)
	{
		$qry="insert into supplier_stock_quantity (supplierstock_id,supplier_id,stock_id,stock_quantity,stock_date,goods_receipt_id,companyunitId) values('".$supplierStockId."', '".$supplier."', '".$item."','".$quantity."','".$stockDate."','".$goodsReceiptId."','".$companyUnitId."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	###get Stock Qty for the Goods Receipt 
	function getGoodsStockQty($goodsReceiptId)
	{
		$qry="select stock_quantity from supplier_stock_quantity where goods_receipt_id='$goodsReceiptId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	function getCompanyUnitId($supplierId,$selStockId,$companyId,$unitId)
	{
		$selDate=date("Y-m-d");
		$qry	= "select a.id  from supplier_stock_company_unit a left join supplier_stock b on a.supplierstock_id=b.id where date_format(b.start_date,'%Y-%m-%d')<='$selDate' and a.supplier_id='$supplierId' and a.stock_id='$selStockId' and a.company_id='$companyId' and a.unit_id='$unitId' order by a.id desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		//print_r($result[0]);
		return (sizeof($result)>0)?$result[0]:"";

	}

}
?>