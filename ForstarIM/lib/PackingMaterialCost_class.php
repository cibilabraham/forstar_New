<?php
class PackingMaterialCost
{
	/****************************************************************
	This class deals with all the operations relating to Packing Material Cost
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PackingMaterialCost(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addPackingMaterialCost($selStockId, $selSupplierId, $costPerItem, $pmcRateListId, $supplierRateListId)
	{
		$qry = "insert into m_packing_material_cost (stock_id, supplier_id, pu_cost, rate_list_id, supplier_rate_list_id) values('$selStockId', '$selSupplierId', '$costPerItem', '$pmcRateListId', '$supplierRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selRateList)
	{
		$qry = "select a.id, a.stock_id, a.supplier_id, a.pu_cost, b.name, b.code, c.name, a.tot_cost,a.active from m_packing_material_cost a, m_stock b, supplier c where a.stock_id=b.id and a.supplier_id=c.id and a.rate_list_id='$selRateList' order by b.name asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));// 		
	}

	# Returns all Records (using in packing Matrix)
	function fetchAllRecords($selRateList)
	{
		$qry	=	"select a.id, a.stock_id, a.supplier_id, a.pu_cost, b.name, b.code, a.tot_cost,a.active from m_packing_material_cost a, m_stock b where a.stock_id=b.id and a.rate_list_id='$selRateList' order by b.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($packingMaterialCostRecId)
	{
		$qry = "select a.id, a.stock_id, a.supplier_id, a.pu_cost, b.category_id, b.subcategory_id, a.rate_list_id, a.supplier_rate_list_id from m_packing_material_cost a, m_stock b where a.stock_id=b.id and a.id=$packingMaterialCostRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updatePackingMaterialCostRec($packingMaterialCostRecId, $selStockId, $selSupplierId, $costPerItem, $pmcRateListId, $supplierRateListId)
	{
		$qry = "update m_packing_material_cost set stock_id='$selStockId', supplier_id='$selSupplierId', pu_cost='$costPerItem', rate_list_id='$pmcRateListId', supplier_rate_list_id='$supplierRateListId' where id=$packingMaterialCostRecId ";	
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deletePackingMaterialCostRec($packingMaterialCostRecId)
	{
		$qry	= " delete from m_packing_material_cost where id=$packingMaterialCostRecId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Stock Records
	function filterStockRecords($selCategoryId, $selSubCategoryId)
	{
		$qry = " select id, code, name, category_id, subcategory_id from m_stock where category_id='$selCategoryId' and subcategory_id='$selSubCategoryId' order by name asc";		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Supplier Records
	function filterSupplierRecords($selStockId) 
	{
		$cDate = date("Y-m-d");
		$qry = " select a.id, a.supplier_id, b.name, a.rate_list_id from supplier_stock a, supplier b, m_supplier_ratelist c where a.supplier_id=b.id and stock_id='$selStockId' and a.rate_list_id=c.id and (('$cDate'>=c.start_date && (c.end_date is null || c.end_date=0)) or ('$cDate'>=c.start_date and '$cDate'<=c.end_date)) order by b.name asc";		
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Get Stock Cost
	function getStockCost($selStockId, $selSupplierId, $supplierRateListId) 
	{
		$qry = " select nego_price from supplier_stock where stock_id='$selStockId' and supplier_id='$selSupplierId' and rate_list_id='$supplierRateListId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function chkRecExist($selStockId, $pmcRateListId, $cRecId)
	{
		$appQry = "";
		if ($cRecId!="") $appQry = " and id!=$cRecId";

		$qry = "select id from m_packing_material_cost where stock_id='$selStockId' and rate_list_id='$pmcRateListId' $appQry";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
function updatePackingMaterialconfirm($packingMaterialCostRecId)
	{
		$qry	= " update m_packing_material_cost set active='1' where id=$packingMaterialCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updatePackingMaterialReleaseconfirm($packingMaterialCostRecId)
	{
		$qry	= " update m_packing_material_cost set active='0' where id=$packingMaterialCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>