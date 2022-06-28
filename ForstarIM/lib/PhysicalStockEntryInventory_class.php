<?php
class PhysicalStockInventory
{
	/****************************************************************
	This class deals with all the operations relating to Employee Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PhysicalStockInventory(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function getStock($company,$unit)
	{
		$qry="select b.id,b.name from m_stock_plantunit a join m_stock b on a.stock_id=b.id where a.company_id='$company' and  a.plant_unit='$unit' and activeconfirm='1' group by b.name order by b.name asc";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getSupplier($company,$unit,$item)
	{
		$qry="select c.id,c.name from supplier_stock_company_unit a join supplier_stock b on a.supplierstock_id=b.id left join supplier c on  a.supplier_id=c.id where a.company_id='$company' and  a.unit_id='$unit' and b.stock_id='$item' group by c.name  order by c.name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function checkDuplicate($companyName,$unit,$stockDate)
	{
		$qry	= "select id  from  physical_stock_inventory  where company_id='$companyName' and unit_id='$unit' and stock_date='$stockDate'";
		//$qry	= "select sum(stock_quantity) from  supplier_stock_quantity  where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$stockId' ";
		//echo $qry; die();
		$result	= $this->databaseConnect->getRecord($qry);
		//return 	$result[0];
		return  (sizeof($result)>0)?false:true;
	}	


	#Add a Physical Stock Inventory
	function addPhysicalStock($companyName,$unit,$stockDate,$userId)
	{
		$qry="insert into physical_stock_inventory (company_id,unit_id,stock_date,createdon, createdby) values('".$companyName."', '".$unit."', '".$stockDate."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getSupplierQty($supplierStockId,$supplierId,$itemId,$companyUnitId)
	{
		$qry	= "select sum(stock_quantity) from  supplier_stock_quantity  where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$itemId' and companyunitId='$companyUnitId'";
		//$qry	= "select sum(stock_quantity) from  supplier_stock_quantity  where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$stockId' ";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->getRecord($qry);
		//return 	$result[0];
		return  (sizeof($result)>0)?$result[0]:"";
	}	
	
	
	function addPhysicalStockQty($lastId,$supplierStockId,$itemId,$supplierId,$stockQty)
	{
		$qry="insert into physical_stock_quantity_inventory (physical_stockId,supplier_stock_id,stock_id,supplier_id,quantity) values('".$lastId."','".$supplierStockId."','".$itemId."','".$supplierId."','".$stockQty."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getCompanyUnitId($supplierId,$stockId,$supplierStockId,$companyId,$unitId)
	{
		$qry	= "select id from  supplier_stock_company_unit  where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$stockId' and company_id='$companyId' and unit_id='$unitId'";
		//$qry	= "select sum(stock_quantity) from  supplier_stock_quantity  where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$stockId' ";
		//echo $qry; die();
		$result	= $this->databaseConnect->getRecord($qry);
		//return 	$result[0];
		return  (sizeof($result)>0)?$result[0]:"";
	}	
	
	
	
	#Add a Physical Stock Inventory
	function addPhysicalStockQuantity($supplierStockId, $supplier, $item,$quantity,$stockDate,$userId)
	{
		$qry="insert into physical_stock_inventory (supplierstock_id,supplier_id,stock_id,stock_quantity,stock_date, createdon, createdby) values('".$supplierStockId."', '".$supplier."', '".$item."','".$quantity."','".$stockDate."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Add a Supplier stock Qty
	function addSupplierStock($supplierStockId,$supplier,$item,$quantity,$stockDate,$physicalId,$companyUnitId)
	{
		$qry="insert into supplier_stock_quantity (supplierstock_id,supplier_id,stock_id,stock_quantity,stock_date,physical_stock_id,companyunitId) values('".$supplierStockId."', '".$supplier."', '".$item."','".$quantity."','".$stockDate."','".$physicalId."','".$companyUnitId."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Get Physical Stock Inventory based on id 
	function find($physicalStockId)
	{
		$qry	= "select id, company_id, unit_id, stock_date from physical_stock_inventory where id=$physicalStockId";
		//$qry	= "select id, supplierstock_id, supplier_id, stock_id,stock_quantity,stock_date from physical_stock_inventory where id=$physicalStockId";
		return $this->databaseConnect->getRecord($qry);
	}


	function getSupplierStockDetail($physicalId)
	{
		$qry	= "select id, supplier_stock_id, stock_id, supplier_id, quantity from physical_stock_quantity_inventory where physical_stockId=$physicalId";
		//$qry	= "select id, supplierstock_id, supplier_id, stock_id,stock_quantity,stock_date from physical_stock_inventory where id=$physicalStockId";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Update  a  Physical Stock Inventory
	function updatePhysicalStock($physicalStockId,$companyId,$unitId,$stockDate)
	{
		$qry	= " update physical_stock_inventory set company_id='$companyId', unit_id='$unitId',stock_date='$stockDate' where id=$physicalStockId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updatePhysicalStockQty($physicalStockEntry,$supplierStockId,$itemId,$supplierId,$stockQty)
	{
		$qry	= " update physical_stock_quantity_inventory set supplier_stock_id='$supplierStockId', stock_id='$itemId',supplier_id='$supplierId',quantity='$stockQty' where id=$physicalStockEntry";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function deletePhysicalStockEntryId($physicalStockEntry)
	{
		$qry	= " delete from physical_stock_quantity_inventory where id='$physicalStockEntry'";
		//echo $qry; die(); 
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry="select a.id, a.company_id, a.unit_id, a.stock_date,b.display_name,c.name,a.active FROM physical_stock_inventory a left join m_billing_company b on a.company_id=b.id  left join m_plant c on a.unit_id=c.id order by a.stock_date limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	function fetchAllRecords()
	{
		$qry= "select a.id, a.company_id, a.unit_id, a.stock_date,b.display_name,c.name,a.active FROM physical_stock_inventory a left join m_billing_company b on a.company_id=b.id  left join m_plant c on a.unit_id=c.id order by a.stock_date";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function stockQtyDetail($physicalStockId)
	{
		$qry= "select a.id, a.supplier_stock_id,a.stock_id,a.supplier_id, a.quantity,b.name,c.name FROM physical_stock_quantity_inventory a left join m_stock b on a.stock_id=b.id  left join supplier c on a.supplier_id=c.id  where a.physical_stockId='$physicalStockId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function deletePhysicalStockInventoryEntry($physicalStockId)
	{
		$qry	= " delete from physical_stock_quantity_inventory where physical_stockId='$physicalStockId'";
		//echo $qry; die(); 
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}







/*	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	=	"select a.id, a.supplierstock_id, a.supplier_id, a.stock_id,a.stock_quantity,a.stock_date,b.name,c.name,a.active FROM physical_stock_inventory a left join supplier b on a.supplier_id=b.id  left join m_stock c on a.stock_id=c.id order by a.stock_date limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Physical Stock Inventory
	function fetchAllRecords()
	{
		$qry	= "select a.id, a.supplierstock_id, a.supplier_id, a.stock_id,a.stock_quantity,a.stock_date,b.name,c.name,a.active FROM physical_stock_inventory a left join supplier b on a.supplier_id=b.id  left join m_stock c on a.stock_id=c.id order by a.stock_date";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/
	
	# Update  a  Physical Stock Inventory
	function updatePhysicalStockQuantity($physicalStockId, $supplierStockId, $supplier,$item, $quantity,$stockDate)
	{
		$qry	= " update physical_stock_inventory set supplierstock_id='$supplierStockId', supplier_id='$supplier', stock_id='$item', stock_quantity='$quantity', stock_date='$stockDate' where id=$physicalStockId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	# Update  a  Physical Stock Inventory Confirm
	function updatePhysicalStockConfirm($physicalStockId)
	{
		$qry	= " update physical_stock_inventory set active='1'  where id=$physicalStockId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update  a  Physical Stock Inventory Confirm
	function updatePhysicalStockReConfirm($physicalStockId)
	{
		$qry	= " update physical_stock_inventory set active='0'  where id=$physicalStockId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	
	# Delete a Physical Stock Inventory
	function deletePhysicalStockInventory($physicalStockId)
	{
		$qry	= " delete from physical_stock_inventory where id=$physicalStockId";
		//echo $qry; die(); 
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Physical Stock Inventory
	function deleteStockInventory($physicalStockId)
	{
		$qry	= " delete from supplier_stock_quantity where physical_stock_id=$physicalStockId";
		//echo $qry; die(); 
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	

	
	###get all suppliers ion supplier stock
	function getSupplierInStock()
	{
		$qry	= "SELECT b.id,b.name FROM `supplier_stock` a  left join supplier b on a.supplier_id=b.id group by b.id order by b.name";
		return $this->databaseConnect->getRecords($qry);
	}

	###get all suppliers ion supplier stock
	function getSupplierStock($supplierId)
	{
		$qry	= "SELECT b.id,b.name FROM `supplier_stock` a  left join m_stock b on a.stock_id=b.id where a.supplier_id='$supplierId' group by b.id order by b.name";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	###get  supplier stock id using supplierid and stockId
	function getSupplierStockId($supplierId,$stockId)
	{
		$cDate = date("Y-m-d");
		$qry	= "SELECT id FROM `supplier_stock` where supplier_id='$supplierId' and stock_id='$stockId'  and  (('$cDate'>=start_date && (end_date is null || end_date=0)) or ('$cDate'>=start_date and '$cDate'<=end_date)) ";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		//echo $result[0];
		return $result[0];
		//return  (sizeof($result)>0)?$result[0]:"";
		
	}
	function getsupplierStockLst($supplierStockId)
	{
		$qry	= "select id from  physical_stock_inventory ";
		if($supplierStockId) $qry.= " where supplierstock_id='$supplierStockId'"; 
		$qry.= " order by id desc limit 1"; 
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->getRecord($qry);
		return  (sizeof($result)>0)?$result[0]:"";
	}

	function checkRecordExist($supplierStockId,$supplierId,$stockId)
	{
		$qry	= "select id,stock_quantity from physical_stock_inventory where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$stockId' order by id desc limit 1 ";
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry; die();
		return 	$result;
	}

/*	function getSupplierQty($supplierStockId,$supplierId,$stockId)
	{
		$qry	= "select sum(stock_quantity) from  supplier_stock_quantity where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$stockId' ";
		//echo $qry; die();
		$result	= $this->databaseConnect->getRecord($qry);
		//return 	$result[0];
		return  (sizeof($result)>0)?$result[0]:"";
	}


	




	
		# Filter company List
	function getcompany($supplier,$stockId,$supplierStockId)
	{
		$qry="select b.id,b.display_name from supplier_stock_company_unit a join m_billing_company b on a.company_id=b.id where a.stock_id='$stockId' and a.supplier_id='$supplier' and a.supplierstock_id='$supplierStockId' group by b.display_name  order by b.display_name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getUnit($supplier,$stockId,$supplierStockId,$company)
	{
		$qry="select b.id,b.name from supplier_stock_company_unit a join m_plant b on a.unit_id=b.id where a.supplierstock_id='$supplierStockId' and  a.stock_id='$stockId' and a.company_id='$company' and a.supplier_id='$supplier' group by b.name  order by b.name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}*/

	 
}

?>