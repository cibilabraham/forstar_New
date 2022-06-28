<?php
class IngredientPurchaseOrder
{  
	/****************************************************************
	This class deals with all the operations relating to Ingredient Purchase Order
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function IngredientPurchaseOrder(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	#Find the Max value of PO
	function maxValuePO()
	{
		$qry	=	"select max(po) from ing_purchaseorder";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	#Insert PO Rec
	function addPurchaseOrder($purchaseOrderNo, $selSupplierId, $userId, $ingredientRateListId)
	{
		$qry = "insert into ing_purchaseorder (po , supplier_id, created, createdby, status, ing_rate_list_id) values('$purchaseOrderNo', '$selSupplierId', Now(), '$userId', 'P', '$ingredientRateListId')";
		//echo $qry."<br>";			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#For adding Purchae Items
	function addPurchaseEntries($lastId, $ingredientId, $unitPrice, $quantity, $totalQty)
	{
		$qry	= "insert into ing_purchaseorder_entry (po_id, ingredient_id, rate, quantity, total_amount) values('$lastId', '$ingredientId', '$unitPrice', '$quantity', '$totalQty')";
		//echo $qry;
			
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.po, a.supplier_id, a.created, a.createdby, a.status, b.name from ing_purchaseorder a, supplier b where a.supplier_id=b.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.po desc limit $offset, $limit";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Purchase Order
	function fetchAllRecords($fromDate, $tillDate)
	{
		$qry	=	"select a.id, a.po, a.supplier_id, a.created, a.createdby, a.status, b.name from ing_purchaseorder a, supplier b where a.supplier_id=b.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.po desc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Printing Purpose
	function getPORecords()
	{
		$qry	=	"select id, po, supplier_id, created, createdby, status from ing_purchaseorder where status='P' order by po desc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}
	
	#For Getting Total Amount Of Each Supplier
	function getPurchaseOrderAmount($purchaseOrderId)
	{
		$qry	=	"select sum(total_amount) from ing_purchaseorder_entry where po_id='$purchaseOrderId' group by po_id";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	# Get Supplier stock based on Supplier id 
	function find($orderId)
	{
		$qry = "select id, po, supplier_id, created, createdby, status, ing_rate_list_id from ing_purchaseorder where id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on PO Id from ing_purchaseorder_entry TABLE	
	function fetchAllStockItem($editPurchaseOrderId)
	{
		$qry	= "select id, po_id, ingredient_id, rate, quantity, total_amount from ing_purchaseorder_entry where po_id='$editPurchaseOrderId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}



	#Delete  Purchase Order Item  Recs
	function deletePurchaseOrderItemRecs($purchaseOrderId)
	{
		$qry	= " delete from ing_purchaseorder_entry where po_id=$purchaseOrderId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();
		return $result;
	}


	# Delete a Purchase Order
	function deletePurchaseOrder($purchaseOrderId)
	{
		$qry	= " delete from ing_purchaseorder where id=$purchaseOrderId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Purchase Order
	function updatePurchaseOrder($purchaseOrderId, $selSupplierId, $ingredientRateListId)
	{
		$qry	= "update ing_purchaseorder set supplier_id='$selSupplierId', ing_rate_list_id='$ingredientRateListId' where id='$purchaseOrderId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	#Find the balance Qty of an Ingredient
	function getBalanceQty($ingredientId)
  	{
		$qry = "select actual_quantity from m_ingredient where id='$ingredientId'";
		//echo $qry;
		$rec =  $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?$rec[0][0]:"";
	}

	#Find the Rate of ingredient
	function findIngredientRate($ingredientId, $selRateListId)
	{
		//last_price
		$qry = "select rate_per_kg from m_ingredient_rate where ingredient_id=$ingredientId and rate_list_id=$selRateListId";		
		$rec =  $this->databaseConnect->getRecord($qry);
		//echo $qry;
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Check PO Number Exist
	function checkIngPONumberExist($poId)
	{
		$qry = " select id from ing_purchaseorder where po='$poId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	// --------------------------------
	// Get Supplier Ing  Records
	// Using in AJAX Section
	// ---------------------------------
	function fetchSupplierIngredientRecords($supplierId)
	{		
		$qry = " select a.ingredient_id, b.name from m_supplier_ing a, m_ingredient b where b.id=a.ingredient_id and a.supplier_id='$supplierId' order by b.name asc ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	// --------------------------
	// Get Ing Records from Production Planning
	// --------------------------
	function fetchIngredientRecords($productionPlanId, $ingRateListId)
	{
		$qry = " select a.id, b.ingredient_id, c.name, sum(b.quantity), e.rate_per_kg from t_production_plan a, t_production_plan_entry b, m_ingredient c, ing_category d, m_ingredient_rate e where e.ingredient_id=b.ingredient_id and d.id=c.category_id and b.ingredient_id=c.id and a.id=b.production_plan_id and e.rate_list_id='$ingRateListId' and a.id in ($productionPlanId) group by b.ingredient_id order by d.name asc, c.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getProductionPlanRecords($productionPlanId)
	{
		$qry = " select a.id, b.ingredient_id, b.quantity,  b.sel_ing_type from t_production_plan a, t_production_plan_entry b where a.id=b.production_plan_id and a.id in ($productionPlanId)";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSemiFinishIngRecords($sfProductId)
	{
		$qry = " select id, sf_product_id, ingredient_id, raw_qty, percent_per_btch from m_sf_product_entry where sf_product_id='$sfProductId' ";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Supplier Recs of selected stock id and Not the Selected supplier  => , $supplierRateListId
	function getSupplierIngRecs($stockId, $supplierId, $poItem)
	{	
		$fieldSelection = "";
		if ($poItem) $fieldSelection = "";
		else $fieldSelection = " and a.supplier_id !='$supplierId'";

		$qry = "select a.id, a.supplier_id, a.ingredient_id, b.name from m_supplier_ing a, supplier b where a.supplier_id=b.id and a.ingredient_id='$stockId' $fieldSelection";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update Production Plan Status
	function updateProductionPlanRec($prodPlanId, $prodPlanstatus)
	{
		$qry	= "update t_production_plan set status='$prodPlanstatus' where id='$prodPlanId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update PO Entry
	function updatePurchaseEntries($ingPOEntryId, $ingredientId, $unitPrice, $quantity, $totalQty)
	{		
		$qry = "update ing_purchaseorder_entry set ingredient_id='$ingredientId', rate='$unitPrice', quantity='$quantity', total_amount='$totalQty' where id='$ingPOEntryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	

	#Delete  Purchase Order Item  Recs
	function delPurchaseEntries($ingPOEntryId)
	{
		$qry	= " delete from ing_purchaseorder_entry where id='$ingPOEntryId'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();
		return $result;
	}

}
?>