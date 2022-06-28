<?php
class StockRequisition
{  
	/****************************************************************
	This class deals with all the operations relating to Stock Requisition
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function StockRequisition(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Stock
	function addStockRequisition($department, $item,$company,$unit,$stockQty,$qty,$userId)
	{
		$qry	= "insert into t_stock_requisition(department,item,company_id,unit,stock_quantity,quantity, created, created_by) values('$department','$item','$company','$unit','$stockQty','$qty', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	
	# Returns all Stock Requisition
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select a.id, a.department,b.name,a.item,c.name,a.company_id,e.display_name,a.unit,d.name,a.stock_quantity,a.quantity,a.created,a.created_by,a.active from t_stock_requisition a left join m_department b on a.department =b.id left join m_stock c on a.item=c.id left join m_plant d on a.unit=d.id left join m_billing_company e on a.company_id=e.id where a.created>='$fromDate' and a.created<='$tillDate' order by a.created desc limit $offset, $limit";
		//$qry	= "select id, department, item,unit,stock_quantity,quantity,created,created_by from t_stock_requisition where created>='$fromDate' and created<='$tillDate' order by created desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		$qry	= "select a.id, a.department,b.name,a.item,c.name,a.company_id,e.display_name,a.unit,d.name,a.stock_quantity,a.quantity,a.created,a.created_by from t_stock_requisition a left join m_department b on a.department =b.id left join m_stock c on a.item=c.id left join m_plant d on a.unit=d.id left join m_billing_company e on a.company_id=e.id where created>='$fromDate' and created<='$tillDate' order by created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Stock Requisition
	function fetchAllRecords()
	{
		$qry	= "select select a.id, a.department,b.name,a.item,c.name,a.company_id,e.display_name,a.unit,d.name,a.stock_quantity,a.quantity,a.created,a.created_by from t_stock_requisition a left join m_department b on a.department =b.id left join m_stock c on a.item=c.id left join m_plant d on a.unit=d.id left join m_billing_company e on a.company_id=e.id order by created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
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


	# Get Supplier stock based on Supplier id 
	function find($orderId)
	{
		$qry	=	"select * from t_stock_requisition where id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Supplier stock based on Supplier id 
	function findData($orderId)
	{
		$qry	=	"select a.id, a.department,b.name,a.item,c.name,a.company_id,e.display_name,a.unit,d.name,a.stock_quantity,a.quantity,a.created,a.created_by from t_stock_requisition a left join m_department b on a.department =b.id left join m_stock c on a.item=c.id left join m_plant d on a.unit=d.id  left join m_billing_company e on a.company_id=e.id where a.id=$orderId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on PO Id from purchaseorder_entry TABLE	
	function fetchAllStockItem($editStockRequisitionId)
	{
		$qry	= "select id, Requisition_id, stock_id, existing_qty, quantity, balance_qty from t_stock_requisition where Requisition_id='$editStockRequisitionId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	/*#Delete  Stock Requisition Item  Recs
	function deleteRequisitionItemRecs($stockRequisitionId)
	{
		# find the received Qty 
		$qry	= " delete from stockRequisition_entries where Requisition_id=$stockRequisitionId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}*/


	# Delete a Stock Requisition
	function deleteStockRequisition($stockRequisitionId)
	{
		$qry	=	" delete from t_stock_requisition where id=$stockRequisitionId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
	

	# Update  a  Stock Requisition
	function updateStockRequisition($stockRequisitionId,$department, $item,$company,$unit,$stockQty,$qty)
	{
		$qry	= "update t_stock_requisition set department='$department', item='$item', unit='$unit',company_id='$company',stock_quantity='$stockQty',quantity='$qty' where id='$stockRequisitionId'";
		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	function updateStockRequisitionConfirm($stockRequisitionId)
	{
		$qry	= "update t_stock_requisition set active='1' where id='$stockRequisitionId'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	function updateStockRequisitionReConfirm($stockRequisitionId)
	{
		$qry	= "update t_stock_requisition set active='0' where id='$stockRequisitionId'";
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
	function getStockRequisitionQty($stockRequisitionId)
	{
		$qry = " select stock_id, quantity from stockRequisition_entries where Requisition_id='$stockRequisitionId'";
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
		$sqry = "select id from m_stockRequisition where requestno='$reqNumber' $addWhr ";
		//echo $sqry;
		$srec = $this->databaseConnect->getRecord($sqry);
		return ( sizeof($srec)>0)?true:false;
	}

	function getTotalUnitStockQty($stock_id,$company,$unit)
	{
		$seldate=date("Y-m-d");
		//	$qry="select sum(a.stock_quantity) from supplier_stock_quantity a left join supplier_stock_company_unit b on a.companyunitId=b.id left join supplier_stock c on b.supplierstock_id=c.id  where a.stock_id='$stock_id' and '$seldate'<=date_format(c.start_date,'%Y-%m-%d') and b.company_id='$company' and b.unit_id='$unit'";
		$qry	= "select sum(a.stock_quantity) from supplier_stock_quantity a left join supplier_stock_company_unit b on
 a.companyunitId=b.id left join supplier_stock c on b.supplierstock_id=c.id  where a.stock_id='$stock_id'  and b.company_id='$company' and b.unit_id='$unit'  and  (('$seldate'>=c.start_date && (c.end_date is null || c.end_date=0)) or ('$seldate'>=c.start_date and '$seldate'<=c.end_date))";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
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


	###get all unit assigned for user in manageuser
	function getCompanyUser($stockId)
	{	
		$arrayVal=array();
		$qry = "select company_id from m_stock_plantunit where stock_id='$stockId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		if(sizeof($result>0))
		{
			$arrayVal[]="--Select--";
			foreach($result as $res)
			{
				if($res[0]=='0')
				{
					$query = "select id,display_name  from m_billing_company where active='1'";
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
					$query = "select id,display_name  from m_billing_company where id='".$res[0]."'";
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

	###get all unit assigned for user in manageuser
	function getUnitUser($stockId,$companyId)
	{	
		$arrayVal=array();
		$qry = "select plant_unit from m_stock_plantunit where stock_id='$stockId' and company_id='$companyId' or stock_id='$stockId' and company_id='0'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		if(sizeof($result>0))
		{
			$arrayVal[]="--Select--";
			foreach($result as $res)
			{
				if($res[0]=='0')
				{
					$query = "select id,name  from m_plant where active='1'";
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
					$query = "select id,name  from m_plant where id='".$res[0]."'";
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


}
?>