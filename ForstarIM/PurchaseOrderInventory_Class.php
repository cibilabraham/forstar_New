<?php
class PurchaseOrderInventory
{  
	/****************************************************************
	This class deals with all the operations relating to Supplier Stock
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function PurchaseOrderInventory(&$databaseConnect)
    	{
       	 $this->databaseConnect =&$databaseConnect;
	}

	#For updating the status
	function updateStatus($supplierId, $selStatus)
	{
		$qry	=	"update m_purchaseorder set status='$selStatus' where id='$supplierId'";		
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Find the Max value of PO
	function maxValuePO()
	{
		$qry	=	"select max(po) from m_purchaseorder";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Insert PO Rec
	function addPurchaseOrder($purchaseOrderNo, $poNumber, $selSupplierId, $userId, $hidSupplierRateListId,$compId,$remarks,$totalQuantity)
	{
		$qry = "insert into m_purchaseorder (po , po_number, supplier_id, created, createdby, status, used_rate_list_id,billing_company_id,remarks) values('$purchaseOrderNo','$poNumber','$selSupplierId', Now(),'$userId','P', '$hidSupplierRateListId','$compId','$remarks')";
		//echo $qry."<br>";
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#For adding Purchae Items
	function addPurchaseEntries($lastId, $stockId, $unitPrice, $quantity, $totalQty,$proddesc,$printoutdesc,$notover,$plant_unit_id)
	{
		$qry	=	"insert into purchaseorder_entry (po_id, stock_id, unit_price, quantity, total_amount,notover,proddescription,printoutdescrip,plant_unit_id) values('$lastId', '$stockId', '$unitPrice', '$quantity', '$totalQty','$notover','$proddesc','$printoutdesc','$plant_unit_id')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}


	# Returns all Paging Records 
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry = "select a.id, a.po, a.po_number, a.supplier_id, a.created, a.createdby, a.status, b.name, a.base_po_id,a.remarks from m_purchaseorder a, supplier b where a.supplier_id=b.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.po desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Purchase Order
	function fetchAllRecords($fromDate, $tillDate)
	{		
		$qry = "select a.id, a.po, a.po_number, a.supplier_id, a.created, a.createdby, a.status, b.name, a.base_po_id from m_purchaseorder a, supplier b where a.supplier_id=b.id and a.created>='$fromDate' and a.created<='$tillDate' order by a.po desc";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Printing Purpose
	function getPORecords()
	{
		$qry	=	"select id, po, po_number, supplier_id, created, createdby, status from m_purchaseorder where status='P' order by po desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}
	
	#For Getting Total Amount Of Each Supplier
	function fetchPurchaseOrderAmount($purchaseOrderId)
	{
		$qry = " select sum(total_amount) from purchaseorder_entry where po_id='$purchaseOrderId' group by po_id";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;		
	}
	
	# Get Supplier stock based on Supplier id 
	function find($orderId)
	{
		$qry = "select id, po, po_number, supplier_id, created, createdby, status, used_rate_list_id,remarks,nettotal from m_purchaseorder where id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on PO Id from purchaseorder_entry TABLE	
	function fetchAllStockItem($editPurchaseOrderId, $poItem)
	{
		if ($poItem) {
			$qry	=	"select code, name, id from m_stock where id in ($poItem)";
		} else {
			$qry	=	"select id, po_id, stock_id, unit_price, quantity, total_amount,proddescription,printoutdescrip,notover,plant_unit_id from purchaseorder_entry where po_id='$editPurchaseOrderId' ";
		}		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	


	#Delete  Purchase Order Item  Recs
	function deletePurchaseOrderItemRecs($purchaseOrderId)
	{
		$qry	=	" delete from purchaseorder_entry where po_id=$purchaseOrderId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}


	# Delete a Purchase Order
	function deletePurchaseOrder($purchaseOrderId)
	{
		$qry	=	" delete from m_purchaseorder where id=$purchaseOrderId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	# Update  a  Purchase Order
	function updatePurchaseOrder($purchaseOrderId, $poNumber, $selSupplierId,$remarks,$totalQuantity)
	{
		$qry	=	"update m_purchaseorder set supplier_id='$selSupplierId', po_number='$poNumber',remarks='$remarks',nettotal='$totalQuantity' where id='$purchaseOrderId'";		
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Supplier Recs of selected stock id and Not the Selected supplier  => , $supplierRateListId
	function getSupplierRec($stockId, $supplierId, $poItem)
	{		
		$cDate = date("Y-m-d");

		$fieldSelection = "";
		if ($poItem) $fieldSelection = "";
		else $fieldSelection = " and a.supplier_id !='$supplierId'";

		$qry = " select a.id, a.supplier_id, a.stock_id, a.nego_price, b.name, a.rate_list_id from supplier_stock a, supplier b, m_supplier_ratelist c where a.supplier_id=b.id and a.stock_id=$stockId and a.rate_list_id=c.id and (('$cDate'>=c.start_date && (c.end_date is null || c.end_date=0)) or ('$cDate'>=c.start_date and '$cDate'<=c.end_date)) $fieldSelection order by b.name asc";		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getBalanceQty($stockId,$plantUnitId)
  	{
		//$qry = "select actual_quantity from m_stock where id='$stockId' and plant_unit='$plantUnitId'";
		$qry = "select actual_quantity from m_stock_plantunit where stock_id='$stockId' and plant_unit='$plantUnitId'";
		//echo $qry;
		$rec =  $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function fetchSupplierStocks($supplierId, $poItem, $supplierRateListId)
	{
		if ($poItem) {
			$qry = "select code, name, id from m_stock order by name asc";
		} else {
			$qry = "select id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type from supplier_stock where supplier_id='$supplierId' and rate_list_id='$supplierRateListId' order by stock_id asc";
		}		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Stock based on id 
	function getStockName($stockId)
	{
		$qry = "select id, code, name from m_stock where id=$stockId";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[2]:"";
	}	

	// --------------------------------
	// Get Supplier Stock  Records
	// Using in AJAX Section
	// ---------------------------------
	function fetchSupplierStockRecords($supplierId, $poItem, $supplierRateListId)
	{
		
		if ($poItem) {
			$qry = "select id, code, name from m_stock order by name asc";
		} else {
			$qry = "select a.stock_id, b.code, b.name from supplier_stock a, m_stock b where b.id=a.stock_id and a.supplier_id='$supplierId' and a.rate_list_id='$supplierRateListId' order by b.name asc";
		}		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
	}


	function getitemSupplierStockRecords($supplierId, $poItem, $supplierRateListId)
	{
		if (($supplierId=="") && ($supplierRateListId==""))
		{
		$qry = "select id, code, name from m_stock order by name asc";
		} else if ($poItem) {
			$qry = "select id, code, name from m_stock order by name asc";
		} else {
			$qry = "select a.stock_id, b.code, b.name from supplier_stock a, m_stock b where b.id=a.stock_id and a.supplier_id='$supplierId' and a.rate_list_id='$supplierRateListId' order by b.name asc";
		}		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
	}

	# Get Stock Item Rate - Find Record Based on SupplierID and Stock ID
	function getStockItemRate($supplierId, $stockId, $supplierRateListId)
	{
		$qry	= "select nego_price from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId' and rate_list_id='$supplierRateListId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;		
	}

	# Get Stock Item Rate - Find Record Based on SupplierID and Stock ID
	function getStockItemMaxRate($supplierId, $stockId)
	{
		$curdate=Date("Y-m-d");
		$qry	= "select nego_price from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId' and '$curdate' >startdate order by startdate desc limit 1";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;		
	}


	# Get Minimum Stock Qty
	function getStockMinOrderQty($stockId)
	{
		$qry = " select min_order_unit, min_order_qty_per_unit from m_stock where id='$stockId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?($rec[0]*$rec[1]):0;		
	}

	# Get Stock Item Records
	function getStockItemRec($stockId)
	{
		$qry = " select min_order_unit, min_order_qty_per_unit from m_stock where id='$stockId'";
		return $this->databaseConnect->getRecord($qry);		
	}

	# Check PO Number Exist
	function checkPONumberExist($poId)
	{
		$qry = " select id from m_purchaseorder where po='$poId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	function getmaxPurchaseOrderInventory($billingCompany)
	{
		$qry = "select po from m_purchaseorder where billing_company_id='$billingCompany' order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function checkPONumberDisplayExist($billingCompany)
	{
		$qry = "select (count(*)) from m_purchaseorder where billing_company_id='$billingCompany'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getValidPurchaseOrderInventory($selDate,$billingCompany)
	{
		//$billingCompany=0;
		$selDate=Date('Y-m-d');
		$qry	= "select start_no from number_gen where billing_company_id='$billingCompany' and type='PO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getValidendnoPurchaseOrderInventory($selDate,$billingCompany)
	{
		
		$selDate=Date('Y-m-d');
		$qry	= "select end_no from number_gen where billing_company_id='$billingCompany' and type='PO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	function chkValidPurchaseOrderInventory($selDate,$billingCompany)
	{
		$selDate=Date('Y-m-d');
		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		$qry	="select id,type,start_no, end_no from number_gen where billing_company_id='$billingCompany' and type='PO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	function getFssaiRegNo($supplierId)
	{
		$qry	= "select fssairegnno from supplier where id='$supplierId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	function getServiceTaxNo($supplierId)
	{
		$qry	= "select servicetaxno from supplier where id='$supplierId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;

	}
	function getVatNo($supplierId)
	{
		$qry	= "select vat_no from supplier where id='$supplierId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;

	}
	function getCstNo($supplierId)
	{
		$qry	= "select cst_no from supplier where id='$supplierId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;

	}
	function getPanNo($supplierId)
	{
		$qry	= "select pan from supplier where id='$supplierId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;

	}

	function getLastPurchasePrice($stockId)
	{
		//$qry	= "select a.id,b.stock_id,b.quantity,a.po_id,a.id,c.id,c.supplier_id,d.unit_price,e.name from goods_receipt a left join goods_receipt_entries b on a.id=b.goods_receipt_id left join m_purchaseorder c on c.id=a.po_id left join purchaseorder_entry d on d.po_id=c.id left join supplier e on c.supplier_id=e.id where b.stock_id='$stockId' order by a.id desc limit 1";
		//$qry="select a.id,b.stock_id,b.qty_received,a.po_id,a.id,c.id,c.supplier_id,d.unit_price,e.name from goods_receipt a left join goods_receipt_entries b on a.id=b.goods_receipt_id left join m_purchaseorder c on c.id=a.po_id left join purchaseorder_entry d on d.po_id=c.id left join supplier e on c.supplier_id=e.id where b.stock_id='$stockId' order by d.id desc limit 1";
		$qry="select a.id,a.po_id,d.stock_id,b.qty_received,d.unit_price,b.id,c.id,d.id,e.name from goods_receipt a left join goods_receipt_entries b on a.id=b.goods_receipt_id left join m_purchaseorder c on c.id=a.po_id left join purchaseorder_entry d on d.po_id=c.id left join supplier e on c.supplier_id=e.id where d.stock_id='$stockId'  order by a.id desc limit 1";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?array($result[3], $result[4],$result[8]):false;
	}

	function getPurchaseOrderDescrption($catId,$subcatId,$stock_main_id)
	{
		//$qry="select a.id,category_id,sub_category_id,b.id,field_value,c.id  from stock_group a join stock_group_entry b on a.id=b.main_id join m_stock_stkg_entry c on b.id=c.stk_group_entry_id  where category_id='$catId' and sub_category_id='$subcatId' and b.stk_field_id=69 and stock_main_id='$stock_main_id'";
		$qry="select field_value from stock_group a  left join stock_group_entry b on a.id=b.main_id left join m_stock_stkg_entry c on b.id=c.stk_group_entry_id where a.category_id=$catId and a.sub_category_id=$subcatId and c.stk_group_entry_id=54 and stock_main_id='$stock_main_id'";

		//$qry="select a.id,category_id,sub_category_id,b.id,field_value,c.id  from stock_group a join stock_group_entry b on a.id=b.main_id join m_stock_stkg_entry c on b.id=c.stk_group_entry_id  where category_id=$catId and sub_category_id=$subcatId and b.stk_field_id=15 and stock_main_id=$stock_main_id";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;

	}

	function getCategoryDetails($stockid)
	{
		$qry="select category_id,subcategory_id from m_stock where id='$stockid'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?array($result[0],$result[1]):false;

	}


	function getItems()
	{
		
		$qry = "select code, name, id from m_stock where activeconfirm=1 order by name asc";			
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllSuppliers($supplierFilterId, $supplierRateListFilterId,$stock_id)
	{
		$cDate = date("Y-m-d");
		$tableUpdate = "";

		$whr = " b.id=a.supplier_id and c.id=a.stock_id and c.id='$stock_id'";
			
		if ($supplierFilterId!="") 		$whr .= " and a.supplier_id=".$supplierFilterId;
		if ($supplierRateListFilterId!="") 	$whr .= " and a.rate_list_id=".$supplierRateListFilterId;

		if ($supplierRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_supplier_ratelist f";
		}

		//$orderBy 	= " b.name asc, c.name asc";
		//$limit 		= " $offset,$limit";

		$qry = "select  a.id, a.supplier_id, a.stock_id, a.quote_price, a.nego_price, a.excise_rate, a.cst, a.schedule, a.packing_rate, a.packing_conv_rate, a.remark, a.stock_type, b.name, c.name, a.rate_list_id,a.activeconfirm,(select count(b.id) from purchaseorder_entry b where b.stock_id=a.id) as tot from supplier_stock a, supplier b, m_stock c $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function fetchSelectedSupplierStockRecords($selectSupplierId, $poItem, $supplierRateListId)
	{

		if ($poItem) {
			$qry = "select id, code, name from m_stock order by name asc";
		} else {
			$qry = "select a.stock_id, b.code, b.name from supplier_stock a, m_stock b where b.id=a.stock_id and a.supplier_id='$selectSupplierId' and a.rate_list_id='$supplierRateListId' order by b.name asc";
		}		
		//echo $qry;


		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
	}


	function getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitid)
	{


		$qry="select stock_id,name from supplier_stock a left join m_stock b on a.stock_id=b.id where supplier_id='$selectSupplierId'";		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;


	}

	function fetchAllRecordsPlantsStockActive()
	{
		$qry	=	"select id, no, name,active from m_plant where active=1 order by name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
		}
	
	}
?>