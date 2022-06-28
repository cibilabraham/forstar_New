<?php
class UnitGroup
{
	/****************************************************************
	This class deals with all the operations relating to Unit Group
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function UnitGroup(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add 
	function addUnitGroup($name, $descr)
	{
		$qry	=	"insert into m_unit_group (name, description) values('".$name."','".$descr."')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select  id, name, description,active,(select count(a1.id) from m_stock_unit a1 where unitgroup_id=a.id) as tot from m_unit_group a order by name asc limit $offset,$limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Categorys 
	function fetchAllRecords()
	{
		$qry	= "select  id, name, description,active,(select count(a1.id) from m_stock_unit a1 where unitgroup_id=a.id) as tot from m_unit_group a order by name asc";
		 
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllActiveRecords()
	{
		$qry	= "select  id, name, description,active,(select count(a1.id) from m_stock_unit a1 where unitgroup_id=a.id) as tot from m_unit_group a  where a.active=1 order by name asc";
		 
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Category based on id 
	function find($unitGroupId)
	{
		$qry	= "select id, name, description from m_unit_group where id=$unitGroupId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Category
	function updateUnitGroup($unitGroupId, $name, $descr)
	{
		$qry	= " update m_unit_group set name='$name', description='$descr' where id=$unitGroupId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete a Category 
	function deleteUnitGroup($unitGroupId)
	{
		$qry	= " delete from m_unit_group where id=$unitGroupId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Check whether the selected category link with any other screen
	function checkMoreEntriesExist($unitGroupId)
	{
		$qry = "select id from m_stock_unit where unitgroup_id='$unitGroupId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	//Find the Unit Group Name
	function getUnitGroupName($unitGroupId)
	{
		$rec = $this->find($unitGroupId);
		return (sizeof($rec)>0)?$rec[1]:"";
	}

	function updateUnitGroupconfirm($unitGroupId){
	$qry	= "update m_unit_group set active='1' where id=$unitGroupId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateUnitGroupReleaseconfirm($unitGroupId){
	$qry	= "update m_unit_group set active='0' where id=$unitGroupId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
}

?>