<?php
class DAMSetting
{  
	/****************************************************************
	This class deals with all the operations relating to Installed Capacity
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DAMSetting(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addDAMSetting($headName, $totalHead, $userId)
	{
		$qry	= "insert into m_dam_setting (head_name, sub_head, created, created_by) values ('$headName', '$totalHead', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function addDAMSettingEntry($lastId,$subheadName,$produced,$stocked,$osSupply,$osSale,$openingBalance,$selUnit,$startDate)
	{
		$qry	= "insert into m_dam_setting_entry (entry_id,sub_head_name,produced,stocked, os_supply, os_sale, opening_balance,unit_id, start_date) values ('$lastId', '$subheadName', '$produced', '$stocked', '$osSupply', '$osSale', '$openingBalance', '$selUnit', '$startDate')";
		//echo $qry; die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	# $qry	= "select a.id, a.name, a.unit_id, b.name as unitName from m_dam_setting a join m_stock_unit b on b.id=a.unit_id  order by a.name asc ";
	function fetchAllRecords()
	{
		//$qry	= "select a.id, a.name, a.unit_id, b.name as unitName from m_dam_setting a join m_stock_unit b on b.id=a.unit_id  order by a.name asc ";
		$qry	= "select id,head_name,sub_head,active from m_dam_setting order by id asc";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id,head_name,sub_head,active from m_dam_setting order by id asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDamSettingEntry($id)
	{
		$qry	= "select id,sub_head_name,produced,stocked,os_supply,os_sale,opening_balance,unit_id,start_date from m_dam_setting_entry  where entry_id='$id' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($damSettingId)
	{
		$qry	= "select id,head_name,sub_head from m_dam_setting where id=$damSettingId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateDAMSetting($damSettingId, $headName, $totalHead)
	{		
		$qry	= " update m_dam_setting set head_name='$headName', sub_head='$totalHead' where id=$damSettingId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	

	function getTypeOfOperation($installedCapacityId)
	{
		$rec = $this->find($installedCapacityId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function getDAMSettingEntryEdit($damEntryId)
	{
		$qry	= "select id,sub_head_name,produced,stocked,os_supply,os_sale,opening_balance,unit_id,start_date from m_dam_setting_entry where entry_id='$damEntryId' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Delete 
	function deleteDAMSetting($damSettingId)
	{
		$qry	= " delete from m_dam_setting where id=$damSettingId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function deleteDAMSettingEntry($damSetId)
	{
		$qry	= " delete from m_dam_setting_entry where entry_id=$damSetId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function updateconfirmDAMSetting($id)
	{
		$qry="update m_dam_setting set active=1 where id=$id";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updaterlconfirmDAMSetting($id)
	{
		$qry="update m_dam_setting set active=0 where id=$id";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getStockUnit($id)
	{
		$qry	= "select name from m_stock_unit where id=$id";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}
}

?>