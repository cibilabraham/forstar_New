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
	function addSupplierStock($supplierStockId,$supplier,$item,$quantity,$stockDate,$physicalId)
	{
		$qry="insert into supplier_stock_quantity (supplierstock_id,supplier_id,stock_id,stock_quantity,stock_date,physical_stock_id) values('".$supplierStockId."', '".$supplier."', '".$item."','".$quantity."','".$stockDate."','".$physicalId."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
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
	
	# Get Physical Stock Inventory based on id 
	function find($physicalStockId)
	{
		$qry	= "select id, supplierstock_id, supplier_id, stock_id,stock_quantity,stock_date from physical_stock_inventory where id=$physicalStockId";
		return $this->databaseConnect->getRecord($qry);
	}

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

	function getSupplierQty($supplierStockId,$supplierId,$stockId)
	{
		$qry	= "select sum(stock_quantity) from  supplier_stock_quantity where supplierstock_id='$supplierStockId' and supplier_id='$supplierId' and stock_id='$stockId' ";
		//echo $qry; die();
		$result	= $this->databaseConnect->getRecord($qry);
		//return 	$result[0];
		return  (sizeof($result)>0)?$result[0]:"";
	}



/*function fetchAllRecordsActiveEmployee()
	{
		$qry	= "select id, name, designation, department,address,telephone_no,active FROM m_employee_master where active='1'  order by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivedept()
	{
		$qry	= "select id, name, description, incharge,active from m_department where active=1 order by name";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
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
*/


	 
}

?>