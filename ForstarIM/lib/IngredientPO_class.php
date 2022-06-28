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
	function addPurchaseOrder($purchaseOrderNo, $selSupplierId,$company,$unit, $userId, $ingredientRateListId)
	{
		$qry = "insert into ing_purchaseorder (po , supplier_id,company_id,unit_id, created, createdby, status) values('$purchaseOrderNo', '$selSupplierId','$company','$unit', Now(), '$userId', 'P')";
		//echo $qry."<br>";		
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#For adding Purchae Items
	function addPurchaseEntries($lastId, $ingredientId, $unitPrice, $quantity, $totalQty,$hidSupplierIng)
	{
		$qry	= "insert into ing_purchaseorder_entry (po_id, ingredient_id, rate, quantity, total_amount,	supplier_ing_id) values('$lastId', '$ingredientId', '$unitPrice', '$quantity', '$totalQty','$hidSupplierIng')";
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
		$qry = "select id, po, supplier_id, created, createdby, status, ing_rate_list_id,company_id,unit_id from ing_purchaseorder where id=$orderId";
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

	###get other suppliers and their rates
	function OtherSuppliers($supplierId, $ingredientId)
	{
		$currentDate=date("Y-m-d");
		$result="";
		$qry = "select a.supplier_id,b.name,a.rate from m_supplier_ing a left join supplier b on a.supplier_id=b.id where a.supplier_id!='$supplierId' and a.ingredient_id='$ingredientId' and ('$currentDate' between a.start_date and a.end_date or  '$currentDate' >=a.start_date and a.end_date='0000-00-00' )";
		//echo $qry;
		$rec =  $this->databaseConnect->getRecords($qry);
		if(sizeof($rec)>0)
		{
			$result.="<table class='print'>";
			$result.="<tr><td class='fieldName'  style='padding:0px 5px 0px 5px'>Supplier</td><td class='fieldName'  style='padding:0px 5px 0px 5px'>Rate</td></tr>";
			foreach($rec as $ret)
			{
				$supplierName=$ret[1];
				$rate=$ret[2];
				$result.="<tr><td  class='listing-item'  style='padding:0px 5px 0px 5px'>".$supplierName."</td><td class='listing-item'  style='padding:0px 5px 0px 5px'>".$rate."</td></tr>";
			}
			$result.="</table>";
		}
			return $result;
		//return $rec;
		//return (sizeof($rec)>0)?$rec[0][0]:"";
	}

	###get last purchase order
	function getLastPurchaseOrder($ingredientId)
	{
		$result="";
		$qry="select d.name,c.qty_received,c.new_rate from ing_purchaseorder a left join ing_receipt b on a.id=b.po_id left join ing_receipt_entry c on b.id=c.ing_receipt_id	 left join  supplier d on a.supplier_id=d.id where c.ingredient_id='$ingredientId' order by a.id desc limit 1";
		//$qry = "select a.supplier_id,b.name,a.rate from m_supplier_ing a left join supplier b on a.supplier_id=b.id where a.supplier_id!='$supplierId' and a.ingredient_id='$ingredientId'";
		//echo $qry;
		$rec =  $this->databaseConnect->getRecord($qry);
		if(sizeof($rec)>0)
		{
		$result.="<table class='print'>";
			$result.="<tr><td class='fieldName'  style='padding:0px 5px 0px 5px'>Supplier</td><td class='fieldName'  style='padding:0px 5px 0px 5px'>Qty</td><td class='fieldName'  style='padding:0px 5px 0px 5px'>Rate</td></tr>";
				$supplierName=$rec[0];
				$qty=$rec[1];
				$rate=$rec[2];
				$result.="<tr><td  class='listing-item'  style='padding:0px 5px 0px 5px'>".$supplierName."</td><td  class='listing-item'  style='padding:0px 5px 0px 5px'>".$qty."</td><td class='listing-item'  style='padding:0px 5px 0px 5px'>".$rate."</td></tr>";
	
			$result.="</table>";
		}
		/*if(sizeof($rec)>0)
		{
			$result.="<table class='print'>";
			$result.="<tr><td class='fieldName'  style='padding:0px 5px 0px 5px'>Supplier</td><td class='fieldName'  style='padding:0px 5px 0px 5px'>Qty</td><td class='fieldName'  style='padding:0px 5px 0px 5px'>Rate</td></tr>";
			foreach($rec as $ret)
			{
				$supplierName=$ret[0];
				$qty=$ret[1];
				$rate=$ret[2];
				$result.="<tr><td  class='listing-item'  style='padding:0px 5px 0px 5px'>".$supplierName."</td><td  class='listing-item'  style='padding:0px 5px 0px 5px'>".$qty."</td><td class='listing-item'  style='padding:0px 5px 0px 5px'>".$rate."</td></tr>";
			}
			$result.="</table>";
		}*/
			return $result;
	}


	#Find the balance Qty of an Ingredient
	function getBalanceQty($supplierId,$ingredientId)
  	{
		$currentDate=date("Y-m-d");
		$qry = "select sum(quantity) as available_quantity from m_supplier_ing_qty a  left join  m_supplier_ing b on a.supplier_ing_id=b.id where b.supplier_id='$supplierId' and b.ingredient_id='$ingredientId' and  ('$currentDate' between start_date and end_date or  '$currentDate' >=start_date and end_date='0000-00-00'  or start_date<'$currentDate')";
		//$qry = "select sum(quantity) as available_quantity from m_supplier_ing_qty a  left join  m_supplier_ing b on a.supplier_ing_id=b.id where b.supplier_id='$supplierId' and b.ingredient_id='$ingredientId' and  ('$currentDate' between start_date and end_date or  '$currentDate' >=start_date and end_date='0000-00-00' )";
		 //echo $qry;
		$rec =  $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?$rec[0][0]:"";
	}

	#Find the Rate of ingredient
	function findIngredientRate($supplierId,$ingredientId)
	{
		//last_price
		$result="";
		$currentDate=date("Y-m-d");
		$qry="select id,rate from m_supplier_ing where ingredient_id='$ingredientId'  and supplier_id='$supplierId'  order by id desc limit 1";
		//$qry="select id,rate from m_supplier_ing where ingredient_id='$ingredientId'  and supplier_id='$supplierId' and  ('$currentDate' between start_date and end_date or  '$currentDate'>=start_date  and end_date='0000-00-00' )";
		//$qry = "select rate_per_kg from m_ingredient_rate where ingredient_id=$ingredientId and rate_list_id=$selRateListId";		
		$rec =  $this->databaseConnect->getRecord($qry);
		//echo $qry;
		return (sizeof($rec)>0)? $result=array($rec[0],$rec[1]):$result="";
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
	// Get Supplier Ing  Records
	// Get Supplier Ing  Records
	// Using in AJAX Section
	// ---------------------------------
	function fetchSupplierIngredientRecords($supplierId)
	{		
		$qry = " select a.ingredient_id, b.name from m_supplier_ing a, m_ingredient b where b.id=a.ingredient_id and a.supplier_id='$supplierId'  and a.active='1' order by b.name asc ";
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

	###chkvalid po id exist in db
	function chkValidPOId($selDate,$company,$unit)
	{
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 and type='IPO' and 	billing_company_id='$company' and (unitid='0' or unitid='$unit')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}

	function getAlphaCode($selDate,$company,$unit)
	{
		$qry = "select alpha_code from number_gen where type='IPO' and   date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1  and type='IPO' and 	billing_company_id='$company' and (unitid='0' or unitid='$unit')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec;
	}

	function checkPOIDDisplayExist($company,$unit)
	{
		$qry = "select (count(*)) from ing_purchaseorder where company_id='$company'"; 
		if($unit!=0) $qry.= " and unit_id='$unit'";
		//echo $qry;
		//die();
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getmaxPOId($company,$unit)
	{
		$qry = "select 	po from  ing_purchaseorder where company_id='$company'";
		if($unit!=0) $qry.= " and unit_id='$unit'";
		 $qry.= " order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	function getValidendnoPOId($selDate,$company,$unit)
	{
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate')  and auto_generate=1  and type='IPO' and 	billing_company_id='$company' and (unitid='0' or unitid='$unit')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getValidPOId($selDate,$company,$unit)
	{
		 $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and auto_generate=1  and type='IPO' and 	billing_company_id='$company' and (unitid='0' or unitid='$unit')'";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
/* rekha modify the code*/ 
	function getMaxIng_PONum($ing_POYear,$company,$unitID)
	{
		$qry = " select max(po), created from ing_purchaseorder where po!=0 and year(created)='$ing_POYear' and company_id = '$company' and unit_id='$unitID' group by id order by id desc, created desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

    function getCurrent_ingPONum($selDate,$company,$unitID)
	{
		//old
		//$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='PF' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO'";
		$qry	= "select start_no, end_no from number_gen where type='IPO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='IPO' and billing_company_id='$company' and unitid='$unitID' ";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
	
	function getValid_IngPONum($ing_PONum, $ing_PODate,$company,$unitID)
	{		
		//$qry	= "select start_no, end_no from number_gen where type='SO' and so_invoice_type='PF' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and type='SO' and start_no<='$soNum' and end_no>='$soNum' and billing_company_id='$compId' and unitid='$comp_unit'";
		$qry	= "select start_no, end_no from number_gen where type='IPO' and  date_format(start_date,'%Y-%m-%d')<='$ing_PODate' and date_format(end_date,'%Y-%m-%d')>='$ing_PODate' and type='IPO' and start_no<='$ing_PONum' and end_no>='$ing_PONum' and billing_company_id='$company' and unitid='$unitID'";

		//echo "<br><br>";
		//echo $qry; 
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	
/*end code */	

}
?>