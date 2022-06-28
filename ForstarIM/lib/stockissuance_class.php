<?php
class StockIssuance
{  
	/****************************************************************
	This class deals with all the operations relating to Stock Issuance
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function StockIssuance(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	# Returns all Stock Issuance
	function fetchAllPagingRecords($fromDate, $tillDate,$selDepartment,$selCompany,$selUnit,$selItem,$offset, $limit)
	{
		$qry	= "select a.id, a.department,b.name,a.item,c.name,a.company_id,e.display_name,a.unit,d.name,a.stock_quantity,a.quantity,a.created,a.created_by,a.active from t_stock_requisition a left join m_department b on a.department =b.id left join m_stock c on a.item=c.id left join m_plant d on a.unit=d.id left join m_billing_company e on a.company_id=e.id where ";
		$qry.=" a.created>='$fromDate' and a.created<='$tillDate' and a.active='1' ";
		if($selDepartment!="") $qry.=" and a.department='$selDepartment'";
		if($selCompany!="") $qry.=" and a.company_id='$selCompany'";
		if($selUnit!="") $qry.=" and a.unit='$selUnit'";
		if($selItem) $qry.=" and a.item='$selItem'";
		$qry.=" order by a.created desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select a.id, a.department,b.name,a.item,c.name,a.company_id,e.display_name,a.unit,d.name,a.stock_quantity,a.quantity,a.created,a.created_by,a.active from t_stock_requisition a left join m_department b on a.department =b.id left join m_stock c on a.item=c.id left join m_plant d on a.unit=d.id left join m_billing_company e on a.company_id=e.id where ";
		$qry.=" a.created>='$fromDate' and a.created<='$tillDate' and a.active='1' ";
		if($selDepartment!="") $qry.=" and a.department='$selDepartment'";
		if($selCompany!="") $qry.=" and a.company_id='$selCompany'";
		if($selUnit!="") $qry.=" and a.unit='$selUnit'";
		if($selItem) $qry.=" and a.item='$selItem'";
		$qry.=" order by a.created desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	### filling the department field in search
	function getDepartmentsInRequisition($fromDate, $tillDate)
	{
		$qry	= "select a.department,b.name from t_stock_requisition a left join m_department b on a.department =b.id  where a.created>='$fromDate' and a.created<='$tillDate' and a.active='1' group by a.department order by b.name asc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	### filling the company field in search
	function getCompanyInRequisition($fromDate, $tillDate,$selDepartment)
	{
		$qry	= "select a.company_id,b.display_name from t_stock_requisition a left join m_billing_company b on a.company_id =b.id  where a.created>='$fromDate' and a.created<='$tillDate' and a.active='1' and department='$selDepartment'  group by a.company_id order by b.display_name asc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	### filling the unit field in search
	function getUnitInRequisition($fromDate, $tillDate,$selDepartment,$selCompany)
	{
		$qry	= "select a.unit,b.name from t_stock_requisition a left join m_plant b on a.unit =b.id  where a.created>='$fromDate' and a.created<='$tillDate' and a.active='1' and a.department='$selDepartment' and a.company_id='$selCompany'  group by a.unit order by b.name asc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	### filling the item field in search
	function getItemInRequisition($fromDate, $tillDate,$selDepartment,$selCompany,$selUnit)
	{
		$qry	= "select a.item,b.name from t_stock_requisition a left join m_stock b on a.item =b.id  where a.created>='$fromDate' and a.created<='$tillDate' and a.active='1' and a.department='$selDepartment' and a.company_id='$selCompany' and a.unit='$selUnit' group by a.item order by b.name asc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	### displaying content in popup 
	function getSupplierItemDetail($item,$totalQty,$hidRowCount,$qty,$requisitionId,$row,$company,$unit)
	{
		//$qry	= "SELECT a.supplierstock_id,a.supplier_id,b.name,sum(stock_quantity) FROM `supplier_stock_quantity` a left join supplier b on a.supplier_id=b.id where stock_id='$item' group by a.supplier_id order by b.name ";
		$qry	= "SELECT a.supplierstock_id,a.supplier_id,b.name,sum(a.stock_quantity),c.id FROM `supplier_stock_quantity` a left join supplier b on a.supplier_id=b.id left join supplier_stock_company_unit c on a.companyunitId=c.id where a.stock_id='$item' and c.company_id='$company' and c.unit_id='$unit'  group by a.supplier_id order by b.name ";
		//echo $qry;
		$j=0;
		$res	=	array();
		$res	=	$this->databaseConnect->getRecords($qry);
		$supplierSize=sizeof($res);
		$result="";
		$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
		$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>Supplier Stock Quantity</td></tr>";
		$result.="<tr><td align='center' bgcolor='#ffffff' style='padding:20px 0px 20px 0px'>";
		$result.="<table cellpadding='4' cellspacing='1' width='85%' bgcolor='#999999'>";
		$result.="<tr  bgcolor='#f2f2f2'>";
		$result.="<td class='listing-head' >Supplier</td>";
		$result.="<td class='listing-head' >Quantity</td>";
		$result.="<td class='listing-head' >Allot Quantity</td>";
		$result.="</tr>";
		foreach($res as $val)
		{	
		
			$mean=($totalQty/$hidRowCount);
			//$allotQty=($val[2]/$mean*$qty);
			$result.="<tr  bgcolor='#fff'>";
			$result.="<td  class='listing-item'>".$val[2]." <input type='hidden' name='supplierId_".$j."' id='supplierId_".$j."' value='".$val[1]."' size='5'/></td>";
			$result.="<td  class='listing-item'>".$val[3]."<input type='hidden' name='supplierStockQty_".$j."' id='supplierStockQty_".$j."' value='".$val[3]."' size='5'/></td>";
			$result.="<td  class='listing-item'><input type='text' name='allotQuantity_".$j."' id='allotQuantity_".$j."' value='".$allotQty."' size='5' onkeyup='chkNumberStatus(".$j.");'/><input type='hidden' name='supplierStockId_".$j."' id='supplierStockId_".$j."' value='".$val[0]."' size='5'/> </td>";
			$result.="</tr>";
			$j++;
		}
		
		$result.="<tr height='40'><td colspan='4' align='center' bgcolor='#fff' class='listing-item'><input class='button' type='submit' onclick='allotQuantity(".$row.",".$requisitionId.",".$supplierSize.",".$item.",".$qty.",".$company.",".$unit.");'  value='Allot Quantity' name='allotQuantity' id='allotQuantity'  ></td></tr>";

		$result.="</table>";
		$result.="</td></tr>";
		$result.="</table></p>";

		return $result;
	}

	### insert data for stock issuance
	function saveData($supplierStockId,$item,$supplierId,$supplierStockQty,$allotQuantity,$requisitionId,$userId,$company,$unit)
	{
		$qry = "insert into t_stock_issuance (supplier_stock_id, stock_id,supplier_id,supplier_qty,allot_qty,requistion_id,company_id,unit_id,created_on,created_by) values('$supplierStockId', '$item', '$supplierId', '$supplierStockQty', '$allotQuantity','$requisitionId','$company','$unit',NOW(),'$userId') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	###get supplier stock quantity
	function getSupplierData($supplierStockId,$item,$supplierId,$company,$unit)
	{
		$qry	= "select a.id,a.stock_quantity from supplier_stock_quantity a left join supplier_stock_company_unit b on a.companyunitId=b.id where a.supplierstock_id='$supplierStockId' and a.stock_id='$item' and a.supplier_id='$supplierId' and b.company_id='$company' and b.unit_id='$unit'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	###update supplier stock quantity
	function updateSupplierStock($id,$qty)
	{
		$qry	=	"update supplier_stock_quantity set stock_quantity='$qty'  where id='$id'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	###check requisition id exist in stock issuance table
	function checkExistInIssuance($stockRequisitionId)
	{
		$qry	= "select id from t_stock_issuance where requistion_id='$stockRequisitionId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?true:false;
	}

	###get supplier issuance data 
	function getStockIssuance($stockRequisitionId,$stock)
	{
		$j=0;
		$qry	= "select a.id,a.supplier_stock_id,a.supplier_id,b.name,a.supplier_qty,a.allot_qty from t_stock_issuance a left join supplier b on  a.supplier_id=b.id where requistion_id='$stockRequisitionId' and stock_id='$stock'";
		//echo $qry;
		$res	=	$this->databaseConnect->getRecords($qry);
		$supplierSize=sizeof($res);
		$result="";
		$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
		$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>Supplier Stock Quantity</td></tr>";
		$result.="<tr><td align='center' bgcolor='#ffffff' style='padding:20px 0px 20px 0px'>";
		$result.="<table cellpadding='4' cellspacing='1' width='85%' bgcolor='#999999'>";
		$result.="<tr  bgcolor='#f2f2f2'>";
		$result.="<td class='listing-head' >Supplier</td>";
		$result.="<td class='listing-head' >Quantity</td>";
		$result.="<td class='listing-head' >Allot Quantity</td>";
		$result.="</tr>";
		foreach($res as $val)
		{	
			$mean=($totalQty/$hidRowCount);
			//$allotQty=($val[2]/$mean*$qty);
			$result.="<tr  bgcolor='#fff'>";
			$result.="<td  class='listing-item'>".$val[3]." <input type='hidden' name='supplierId_".$j."' id='supplierId_".$j."' value='".$val[2]."' size='5'/></td>";
			$result.="<td  class='listing-item'>".$val[4]."<input type='hidden' name='supplierStockQty_".$j."' id='supplierStockQty_".$j."' value='".$val[4]."' size='5'/></td>";
			$result.="<td  class='listing-item'><input type='text' name='allotQuantity_".$j."' id='allotQuantity_".$j."' value='".$val[5]."' size='5' readonly/><input type='hidden' name='supplierStockId_".$j."' id='supplierStockId_".$j."' value='".$val[1]."' size='5'/> </td>";
			$result.="</tr>";
			$j++;
		}
		
		//$result.="<tr height='40'><td colspan='4' align='center' bgcolor='#fff' class='listing-item'><input class='button' type='submit' onclick='allotQuantity(".$row.",".$requisitionId.",".$supplierSize.",".$item.");'  value='Allot Quantity' name='allotQuantity' id='allotQuantity'  ></td></tr>";

		$result.="</table>";
		$result.="</td></tr>";
		$result.="</table></p>";

		return $result;
		
	}

	###get all records for print
	# Returns all Stock Issuance
	function fetchAllRecords()
	{
		$qry	= "select a.id, a.department,b.name,a.item,c.name,a.company_id,e.display_name,a.unit,d.name,a.stock_quantity,a.quantity,a.created,a.created_by,a.active from t_stock_requisition a left join m_department b on a.department =b.id left join m_stock c on a.item=c.id left join m_plant d on a.unit=d.id left join m_billing_company e on a.company_id=e.id where a.active='1' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	###get stock allocation detail for print
	function getAllStockIssuance($stockRequisitionId,$stock)
	{
		$qry	= "select a.id,a.supplier_stock_id,a.supplier_id,b.name,a.supplier_qty,a.allot_qty from t_stock_issuance a left join supplier b on  a.supplier_id=b.id where requistion_id='$stockRequisitionId' and stock_id='$stock'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateStockIssuanceConfirm($stockRequisitionId)
	{
		$qry	=	"update t_stock_issuance set active='1'  where requistion_id='$stockRequisitionId'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function updateStockIssuanceReConfirm($stockRequisitionId)
	{
		$qry	=	"update t_stock_issuance set active='0'  where requistion_id='$stockRequisitionId'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getIssuanceStatus($stockRequisitionId)
	{
		$qry	= "select active from t_stock_issuance  where requistion_id='$stockRequisitionId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}














/*
	#Add Stock
	function addStockIssuance($requestNo, $selDepartment, $userId)
	{
		$qry	= "insert into m_stockissuance(requestno, department_id, created, createdby) values('$requestNo','$selDepartment', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	#For adding Issuance Items
	function addIssuanceEntries($lastId, $stockId, $exisitingQty, $quantity, $balanceQty, $currentStock)
	{
		$qry	=	"insert into stockissuance_entries (issuance_id, stock_id, existing_qty, quantity, balance_qty, current_stock) values('$lastId', '$stockId', '$exisitingQty', '$quantity', '$balanceQty', '$currentStock')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Get Supplier stock based on Supplier id 
	function find($orderId)
	{
		$qry	=	"select * from m_stockissuance where id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on PO Id from purchaseorder_entry TABLE	
	function fetchAllStockItem($editStockIssuanceId)
	{
		$qry	= "select id, issuance_id, stock_id, existing_qty, quantity, balance_qty from stockissuance_entries where issuance_id='$editStockIssuanceId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Delete  Stock Issuance Item  Recs
	function deleteIssuanceItemRecs($stockIssuanceId)
	{
		# find the received Qty 
		$this->getStockIssuanceQty($stockIssuanceId);

		$qry	= " delete from stockissuance_entries where issuance_id=$stockIssuanceId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}


	# Delete a Stock Issuance
	function deleteStockIssuance($stockIssuanceId)
	{
		$qry	=	" delete from m_stockissuance where id=$stockIssuanceId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
	

	# Update  a  Stock Issuance
	function updateStockIssuance($stockIssuanceId,$requestNo,$selDepartment)
	{
		$qry	= "update m_stockissuance set requestno='$requestNo', department_id='$selDepartment' where id='$stockIssuanceId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Get Total Qty of a Stock Item (usng in GRN)
	function  getTotalStockQty($stockId)
	{
		$qry = "select actual_quantity from m_stock where id='$stockId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	#Update the Balance Stock Qty
	function updateBalanceStockQty($stockId, $balanceQty)
	{
		$qry = "update m_stock set actual_quantity='$balanceQty' where id='$stockId'";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	/*************************************************/
	# get Stock Issued Qty
	function getStockIssuanceQty($stockIssuanceId)
	{
		$qry = " select stock_id, quantity from stockissuance_entries where issuance_id='$stockIssuanceId'";
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
		if ($qtyReceived>0) $updateField = "actual_quantity=actual_quantity+$qtyReceived";
		$qry = "update m_stock set $updateField where id=$stockId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	/*************************************************/

	# Checking Unique Numbering
	function checkUnique($reqNumber, $hidReqNumber)
	{
		$addWhr = ( $hidReqNumber !="" ) ? " and requestno!='$hidReqNumber' " : "";
		$sqry = "select id from m_stockissuance where requestno='$reqNumber' $addWhr ";
		//echo $sqry;
		$srec = $this->databaseConnect->getRecord($sqry);
		return ( sizeof($srec)>0)?true:false;
	}

	function getTotalUnitStockQty($stockIdFrom,$unitTo)
	{

$qry="select actual_quantity from m_stock_plantunit where stock_id='$stockIdFrom' and plant_unit='$unitTo'";
//echo $qry;

$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	



/*	# Returns all Stock Issuance
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select id, requestno, department_id, created from m_stockissuance where created>='$fromDate' and created<='$tillDate' order by created desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select id, requestno, department_id, created from m_stockissuance where created>='$fromDate' and created<='$tillDate' order by created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
*/
	
	
	/*
	#For Getting Total Amount Of Each Supplier
	function fetchPurchaseOrderAmount($purchaseOrderId)
	{
			$qry	=	"select stock_id,unit_price,quantity,total_amount,sum(total_amount) from purchaseorder_entry where po_id='$purchaseOrderId' group by po_id";
			//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}
	*/


	

}
?>