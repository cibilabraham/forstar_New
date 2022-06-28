<?php
class StockField
{
	/****************************************************************
	This class deals with all the operations relating to Stock Field
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function StockField(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

		
	# Add a Stock Field
	function addStockField($labelName, $inputType, $stkFieldName, $stkFieldValue, $stkFieldSize, $fieldDataType, $userId, $unitGroup)
	{
		$qry	= "insert into stock_field (label_name, field_type, field_name, field_default_value, field_size, field_data_type, created, createdby, unit_group_id) values('$labelName', '$inputType', '$stkFieldName', '$stkFieldValue', '$stkFieldSize', '$fieldDataType', NOW(), $userId, '$unitGroup')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

	# Returns all Paging Stock
	function fetchAllPagingRecords($offset, $limit)
	{	
		$orderBy 	= " a.label_name asc";
		$limit 		= " $offset,$limit";
	
		$qry = "select a.id, a.label_name, a.field_type, a.field_name, a.field_default_value, a.field_size, a.field_data_type, a.unit_group_id, b.name,a.active,(select count(a1.id) from stock_group_entry a1 where stk_field_id=a.id) as tot from stock_field a left join m_unit_group b on b.id=a.unit_group_id ";
		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$orderBy 	= " a.label_name asc";
			
		$qry = "select a.id, a.label_name, a.field_type, a.field_name, a.field_default_value, a.field_size, a.field_data_type, a.unit_group_id, b.name,a.active,(select count(a1.id) from stock_group_entry a1 where stk_field_id=a.id) as tot from stock_field a left join m_unit_group b on b.id=a.unit_group_id ";
		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
				
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Get Rec based on id 
	function find($stockFieldId)
	{
		$qry = "select id, label_name, field_type, field_name, field_default_value, field_size, field_data_type, unit_group_id from stock_field where id='$stockFieldId' ";
		//echo "$qry";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateStockField($stockFieldId, $labelName, $inputType, $stkFieldName, $stkFieldValue, $stkFieldSize, $fieldDataType, $unitGroup)
	{
		$qry = " update stock_field set label_name='$labelName', field_type='$inputType', field_name='$stkFieldName', field_default_value='$stkFieldValue', field_size='$stkFieldSize', field_data_type='$fieldDataType', unit_group_id='$unitGroup' where id='$stockFieldId' ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Stock
	function deleteStockField($stockFieldId)
	{
		$qry	= " delete from stock_field where id=$stockFieldId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check Stock Field Rec Exist
	function chkStockFieldRecExist($stockFieldId)
	{
		$qry = " select id from stock_group_entry where stk_field_id='$stockFieldId' ";
		//echo $qry;
		$result =  $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateStockFieldconfirm($stockFieldId){
		$qry	= "update stock_field set active='1' where id=$stockFieldId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateStockFieldReleaseconfirm($stockFieldId){
	$qry	= "update stock_field set active='0' where id=$stockFieldId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
}
?>