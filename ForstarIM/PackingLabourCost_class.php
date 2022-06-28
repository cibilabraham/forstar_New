<?php
class PackingLabourCost
{
	/****************************************************************
	This class deals with all the operations relating to Packing Labour Cost
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PackingLabourCost(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addPackingLabourCost($itemName, $itemCode, $costPerItem, $plcRateListId)
	{
		$qry = "insert into m_packing_labour_cost (name, code, cost, rate_list_id) values('$itemName', '$itemCode', '$costPerItem', '$plcRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selRateList)
	{
		$qry = "select id, name, code, cost,active from m_packing_labour_cost where rate_list_id='$selRateList' order by code asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));// 		
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry	= "select id, name, code, cost,active from m_packing_labour_cost where rate_list_id='$selRateList' order by code asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($packingLabourCostRecId)
	{
		$qry = "select id, name, code, cost, rate_list_id from m_packing_labour_cost where id=$packingLabourCostRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updatePackingLabourCostRec($packingLabourCostRecId, $itemName, $itemCode, $costPerItem, $plcRateListId)
	{
		$qry = "update m_packing_labour_cost set name='$itemName', code='$itemCode', cost='$costPerItem', rate_list_id='$plcRateListId' where id=$packingLabourCostRecId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deletePackingLabourCostRec($packingLabourCostRecId)
	{
		$qry	=	" delete from m_packing_labour_cost where id=$packingLabourCostRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Check Rec Exist
	function chkRecExist($itemCode, $plcRateListId, $cRecId)
	{
		$appQry = "";
		if ($cRecId!="") $appQry = " and id!=$cRecId";
		$qry = "select id from m_packing_labour_cost where code='$itemCode' and rate_list_id='$plcRateListId' $appQry";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updatePackingLabourCostconfirm($packingLabourCostRecId)
	{
		$qry	= " update m_packing_labour_cost set active='1' where id=$packingLabourCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updatePackingLabourCostReleaseconfirm($packingLabourCostRecId)
	{
		$qry	= " update m_packing_labour_cost set active='0' where id=$packingLabourCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}

?>