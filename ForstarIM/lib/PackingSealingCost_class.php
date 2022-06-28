<?php
class PackingSealingCost
{
	/****************************************************************
	This class deals with all the operations relating to Packing Sealing Cost
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PackingSealingCost(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addPackingSealingCost($itemName, $itemCode, $costPerItem, $pscRateListId)
	{
		$qry = "insert into m_packing_sealing_cost (name, code, cost, rate_list_id) values('$itemName', '$itemCode', '$costPerItem', '$pscRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selRateList)
	{
		$qry = "select id, name, code, cost,active from m_packing_sealing_cost where rate_list_id='$selRateList' order by code asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));// 		
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry	=	"select id, name, code, cost,active from m_packing_sealing_cost where rate_list_id='$selRateList' order by code asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($packingSealingCostRecId)
	{
		$qry = "select id, name, code, cost, rate_list_id from m_packing_sealing_cost where id=$packingSealingCostRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updatePackingSealingCostRec($packingSealingCostRecId, $itemName, $itemCode, $costPerItem, $pscRateListId)
	{
		$qry = "update m_packing_sealing_cost set name='$itemName', code='$itemCode', cost='$costPerItem', rate_list_id='$pscRateListId' where id=$packingSealingCostRecId ";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deletePackingSealingCostRec($packingSealingCostRecId)
	{
		$qry	= " delete from m_packing_sealing_cost where id=$packingSealingCostRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function chkRecExist($itemCode, $fcRateListId, $cRecId)
	{
		$appQry = "";
		if ($cRecId!="") $appQry = " and id!=$cRecId";

		$qry = "select id from m_packing_sealing_cost where code='$itemCode' and rate_list_id='$fcRateListId' $appQry";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}	

	function updatePackingSealingconfirm($categoryId)
	{
		$qry	= " update m_packing_sealing_cost set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updatePackingSealingReleaseconfirm($categoryId)
	{
		$qry	= " update m_packing_sealing_cost set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>