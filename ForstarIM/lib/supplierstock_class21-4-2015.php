<?php
class SupplierStock
{
	/****************************************************************
	This class deals with all the operations relating to Supplier Stock
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SupplierStock(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Getting Unique Records
	function fetchAllUniqueRecords($supplierId, $selStockId, $supplierRateList)
	{
		$qry	=	"select  id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type, created, createdby from supplier_stock where supplier_id='$supplierId' and stock_id='$selStockId'";
		//$qry	=	"select  id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type, created, createdby from supplier_stock where supplier_id='$supplierId' and stock_id='$selStockId' and rate_list_id='$supplierRateList'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	###CHECK ENTRY FOR THIS DATE FOR THE SAME SUPPLIER EXIST
	function chkValidDateEntry($seldate, $cId,$supplierId,$selStockId)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		$qry	= "select a.id, a.nego_price, a.start_date from supplier_stock a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry and supplier_id='$supplierId' and stock_id='$selStockId' order by a.start_date desc";
		//echo $qry."<br>";
		//die();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?false:true;
	}

	# Date Wise Rate list
	function getSupplierRateList($selDate,$supplierId,$selStockId)
	{	
		$qry	= "select id as ratelistid from supplier_stock where date_format(start_date,'%Y-%m-%d')<='$selDate' and supplier_id='$supplierId' and stock_id='$selStockId' order by id desc";
		//echo $qry; 
		//echo die();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}
	
	# update Rec
	function updateRateListRec($pageCurrentRateListId, $endDate)
	{
		$qry = " update supplier_stock set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry; die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	


	#Add Supplier Stock
	function addSupplierStock($selSupplierId, $selStockId, $quotePrice, $negoPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerKgRate, $layerConverRate, $userId, $supplierRateList, $unitPricePer, $unitPricePerOneItem,$plantUnitid,$startdate)
	{
		//echo $stockType;
		if ($stockType=='O') {
			$qry	=	"insert into supplier_stock (supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, remark, stock_type, created, createdby, rate_list_id, unit_price_per_qty, unit_price_per_each_item,plant_unit_id,start_date) values('".$selSupplierId."','".$selStockId."','".$quotePrice."','".$negoPrice."','$exciseRate','$cstRate','$schedule','$remarks','$stockType',Now(),'$userId', '$supplierRateList', '$unitPricePer', '$unitPricePerOneItem','$plantUnitid','$startdate')";
			//echo $qry;
		} else {
			$qry	=	"insert into supplier_stock (supplier_id, stock_id, quote_price, nego_price, excise_rate, cst,schedule, remark, packing_rate, packing_conv_rate, stock_type, created, createdby, rate_list_id, unit_price_per_qty, unit_price_per_each_item,plant_unit_id,start_date) values('".$selSupplierId."','".$selStockId."','".$quotePrice."','".$negoPrice."', '$exciseRate', '$cstRate', '$schedule', '$remarks', '$layerKgRate', '$layerConverRate', '$stockType',Now(),'$userId', '$supplierRateList', '$unitPricePer', '$unitPricePerOneItem','$plantUnitid','$startdate')";
		}
		//echo "---$qry"; die();
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function addStockQuantity()
	{
		$qry	=	"insert into supplier_stock_quantity (supplierstock_id, stock_id, brand, gsm, bf, cobb, layer_no) values('$lastId','$paperQuality','$layerBrand','$layerGsm', '$layerBf', '$layerCobb','$layerNo')";
		


		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

		#Add Lyaer
	function addLayer($lastId, $paperQuality, $layerBrand, $layerGsm, $layerBf, $layerCobb, $layerNo)
	{
		$qry	=	"insert into supplier_stock_layer (supplierstock_id, quality, brand, gsm, bf, cobb, layer_no) values('$lastId','$paperQuality','$layerBrand','$layerGsm', '$layerBf', '$layerCobb','$layerNo')";
		
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Supplier Paging Stock
	function fetchAllPagingRecords($offset, $limit, $supplierFilterId, $supplierRateListFilterId)
	{
		$cDate = date("Y-m-d");
		$tableUpdate = "";

		$whr = " b.id=a.supplier_id and c.id=a.stock_id ";
			
		if ($supplierFilterId!="") 	
		{
			$whr .= " and a.supplier_id=".$supplierFilterId;
		}
		else
		{
			$whr .= " and  (('$cDate'>=a.start_date && (a.end_date is null || a.end_date=0)) or ('$cDate'>=a.start_date and '$cDate'<=a.end_date)) "; 
		}
		//if ($supplierRateListFilterId!="") 	$whr .= " and a.rate_list_id=".$supplierRateListFilterId;

		
			
		/*if ($supplierRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id ";
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_supplier_ratelist f";
		}*/

		$orderBy 	= " b.name asc, c.name asc";
		$limit 		= " $offset,$limit";

		$qry = "select  a.id, a.supplier_id, a.stock_id, a.quote_price, a.nego_price, a.excise_rate, a.cst, a.schedule, a.packing_rate, a.packing_conv_rate, a.remark, a.stock_type, b.name, c.name, a.rate_list_id,a.activeconfirm,(select count(b.id) from purchaseorder_entry b where b.stock_id=a.id) as tot,a.plant_unit_id,start_date,end_date from supplier_stock a, supplier b, m_stock c $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		
		//echo "-------------$qry-------------";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Supplier Stock
	function fetchAllRecords($supplierFilterId, $supplierRateListFilterId)
	{
		$tableUpdate = "";
		$cDate = date("Y-m-d");

		$whr = " b.id=a.supplier_id and c.id=a.stock_id ";
			
		//if ($supplierRateListFilterId!="") 	$whr .= " and a.rate_list_id=".$supplierRateListFilterId;
		if ($supplierFilterId!="") 	
		{
			$whr .= " and a.supplier_id=".$supplierFilterId;
		}
		else
		{
			$whr .= " and  (('$cDate'>=a.start_date && (a.end_date is null || a.end_date=0)) or ('$cDate'>=a.start_date and '$cDate'<=a.end_date)) "; 
		}

		/*if ($supplierRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_supplier_ratelist f";
		} */

		$orderBy 	= " b.name asc, c.name asc";		

		$qry = "select  a.id, a.supplier_id, a.stock_id, a.quote_price, a.nego_price, a.excise_rate, a.cst, a.schedule, a.packing_rate, a.packing_conv_rate, a.remark, a.stock_type, b.name, c.name, a.rate_list_id,(select count(b.id) from purchaseorder_entry b where b.stock_id=a.id) as tot from supplier_stock a, supplier b, m_stock c $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Used in Purchase Order
	function fetchSupplierStocks($supplierId, $supplierRateListId)
	{
		$qry = "select id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type from supplier_stock where supplier_id='$supplierId' and rate_list_id='$supplierRateListId' order by stock_id asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Using IN Purchase Order -- Find Record Based on SupplierID and Stock ID
	function findRecord($supplierId, $stockId, $supplierRateListId)
	{
		$qry	= "select id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId' and rate_list_id='$supplierRateListId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Get Supplier stock based on Supplier id 
	function find($supplierId)
	{
		$qry	=	"select id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type, rate_list_id, unit_price_per_qty, unit_price_per_each_item,start_date,end_date from supplier_stock where id=$supplierId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);		
	}
	
	
	#find Data from Supplier_stock_layer
	function fetchLayerRecords($supplierStockId)
	{
		$qry	= "select id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type from supplier_stock_layer where supplierstock_id=$supplierStockId";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}



	#Delete  Layer Recs
	function deleteLayerRecs($supplierStockId)
	{
		$qry	= " delete from supplier_stock_layer where supplierstock_id=$supplierStockId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	# Delete a Supplier Stock
	function deleteSupplierStock($supplierStockId)
	{
		$qry	= " delete from supplier_stock where id=$supplierStockId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)  $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Supplier Stock
	function updateSupplierStock($supplierStockId, $selSupplierId, $selStockId, $quotePrice, $negoPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerKgRate, $layerConverRate, $supplierRateList, $unitPricePer, $unitPricePerOneItem,$startdate,$endDate)
	{
		
		if ($stockType=='O') {
			$qry	= "update supplier_stock set supplier_id='$selSupplierId', stock_id='$selStockId', quote_price='$quotePrice', nego_price='$negoPrice', excise_rate='$exciseRate', cst='$cstRate', schedule='$schedule', remark='$remarks', stock_type='$stockType', rate_list_id='$supplierRateList', unit_price_per_qty='$unitPricePer', unit_price_per_each_item='$unitPricePerOneItem',end_date='$endDate' where id='$supplierStockId'";
			//echo $qry;
		} else {
			$qry	= "update supplier_stock set supplier_id='$selSupplierId', stock_id='$selStockId', quote_price='$quotePrice', nego_price='$negoPrice', excise_rate='$exciseRate', cst='$cstRate', schedule='$schedule', remark='$remarks', packing_rate='$layerKgRate', packing_conv_rate='$layerConverRate', stock_type='$stockType', rate_list_id='$supplierRateList', unit_price_per_qty='$unitPricePer', unit_price_per_each_item='$unitPricePerOneItem',end_date='$endDate' where id='$supplierStockId'";
		}
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# get the PO's of selected Rate List
	function getPurchaseOrderRec($supplierRateListFilterId)
	{
		$qry = " select id, po, status, supplier_id from m_purchaseorder where used_rate_list_id='$supplierRateListFilterId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Update Rec after create new rate List
	function uptdSupplierStockRec($supplierId, $stockId, $currentRateListId, $negotiatedPrice, $supplySchedule)
	{
		$qry = " update supplier_stock set nego_price='$negotiatedPrice', schedule='$supplySchedule' where supplier_id='$supplierId' and stock_id='$stockId' and rate_list_id='$currentRateListId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Bulk Update section
	function bulkUpdateSupplierStockRec($supplierStockId, $negotiatedPrice, $supplySchedule, $priceModified)
	{
		if ($priceModified!="") $updatePrice = " ,  nego_price='$negotiatedPrice'";
		else $updatePrice = "";

		$qry = " update supplier_stock set schedule='$supplySchedule' $updatePrice where id='$supplierStockId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
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
	# Update PO Main Table
	function updatePOMainRec($poMainId, $currentRateListId)
	{
		$qry = " update m_purchaseorder set used_rate_list_id = '$currentRateListId', revise_need='N' where id='$poMainId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Fetch All Records based on PO Id from purchaseorder_entry TABLE	
	function fetchAllStockItem($purchaseOrderId)
	{
		$qry	= "select id, po_id, stock_id, unit_price, quantity, total_amount from purchaseorder_entry where po_id='$purchaseOrderId' ";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get stock Unit Price
	function getUnitPrice($supplierId, $stockId, $currentRateListId)
	{
		$qry = " select nego_price from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId' and rate_list_id='$currentRateListId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec>0)?$rec[0]:0;
	}

	# Update Purchase Order Entry Rec
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

	# Get PO Records
	function getPurchaseOrderRecords($poMainId)
	{
		$qry = " select id, po_id, stock_id, unit_price, quantity, total_amount from purchaseorder_entry where po_id='$poMainId' ";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}
	
	# Get Received Stock Qty
	function getReceivedQtyOfStock($stockId, $poId)
	{
		$qry = "select b.qty_received from goods_receipt a join goods_receipt_entries b on b.goods_receipt_id = a.id  and a.po_id=$poId and b.stock_id=$stockId";
		$grnRecs = $this->databaseConnect->getRecords($qry);	
		
		if ( sizeof($grnRecs) > 0 ) {
			$totalRecdQty = 0;
			while ( list(,$rec) = each ($grnRecs) ) {
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
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Checking supplier stock used in po
	function chkSupplierStockExist($supplierId, $rateListId, $stockId)
	{
		$qry = " select a.id from m_purchaseorder a, purchaseorder_entry b where a.id=b.po_id and a.supplier_id='$supplierId' and used_rate_list_id='$rateListId' and stock_id='$stockId' ";
		//echo $qry."<br>";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Checking Entry Exist
	function checkEntryExist($supplierId, $stockId, $rateListId, $currentId)
	{
		if ($currentId) $updateQry = " and id!=$currentId";
		else $updateQry = "";

		$qry = " select id from supplier_stock where supplier_id='$supplierId' and stock_id='$stockId' and rate_list_id='$rateListId' $updateQry ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateSupplierStockconfirm($supplierId){
		$qry	= "update supplier_stock set activeconfirm='1' where id=$supplierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateSupplierRateListconfirm($supplierid,$stockId){
		$qry	= "update m_supplier_ratelist set active='1' where supplier_id='$supplierid' and stock_id=$stockId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

function updateSupplierStockReleaseconfirm($supplierId){
	$qry	= "update supplier_stock set activeconfirm='0' where id=$supplierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function addSupplierRateList($rateListName, $startDate, $userId, $currencyValue, $description,$supplierid,$selStockId,$price)
	{
		//$qry	= "insert into m_currency_ratelist (name, start_date, created, created_by, currency_value, descr) values('$rateListName', '$startDate', NOW(), '$userId', '$currencyValue', '$description')";
		$qry	= "insert into m_supplier_ratelist (name, start_date, supplier_id,stock_id,rate) values('$rateListName', '$startDate','$supplierid','$selStockId','$price')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}	


	function insertedSUPPLatestRateList()
	{
		$cDate = date("Y-m-d");
	
		//$qry	= "select id from m_supplier_ratelist where '$cDate'>=date_format(start_date,'%Y--%m-%d') and (currency_id=0 or currency_id is null) order by start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

# update Rate List Rec
	function updatesupRateListRec($supplierRateList, $newstartDate,$rate,$stockid)
	{
		//if ($startDate) {
			//$sDate		= explode("-",$startDate);
			//$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		//} else $endDate="0000-00-00";

		$qry = "update m_supplier_ratelist set end_date=DATE_SUB('$newstartDate',INTERVAL 1 DAY) where id='$supplierRateList'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	function updateSupplier($currencyId, $currencyCode, $currencyValue, $description, $cyRateListId)
	{
		//$qry	= " update m_currency set code='$currencyCode', currency_value='$currencyValue', descr='$description', rate_list_id='$cyRateListId' where id=$currencyId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateSupplierRateList($cyRateListId, $cyLatestId, $currencyValue, $description)
	{
		//$qry	= " update m_currency_ratelist set currency_id='$cyLatestId', currency_value='$currencyValue', descr='$description' where id=$cyRateListId";
		$qry	= " update m_supplier_ratelist set currency_id='$cyLatestId', currency_value='$currencyValue', descr='$description' where id=$cyRateListId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

###get stock rate according to current date
	function latestRateListUnit($supplierId,$stock_id)
	{
		$cDate = date("Y-m-d");
	
		//$qry = "select a.id from supplier_stock a where supplier_id=$supplierId and stock_id='$stock_id' and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date)) order by a.start_date desc";
		$qry = "select a.id from supplier_stock a where supplier_id=$supplierId and stock_id='$stock_id' and (('$cDate'>=a.start_date && (a.end_date is null || a.end_date=0)) or ('$cDate'>=a.start_date and '$cDate'<=a.end_date)) order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	
}
?>