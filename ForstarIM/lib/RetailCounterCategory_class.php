<?php
class RetailCounterCategory
{
	/****************************************************************
	This class deals with all the operations relating to Retail Counter Category
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RetailCounterCategory(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Category
	function addCategory($name, $descr)
	{
		$qry	= "insert into m_retail_counter_category (name, description) values('".$name."','".$descr."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select  id, name, description,active,(select COUNT(a1.id) from m_retail_counter a1 where a1.rt_ct_category_id = mr.id) as tot from m_retail_counter_category mr order by name asc limit $offset,$limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Categorys 
	function fetchAllRecords()
	{
		$qry	= "select  id, name, description,active from m_retail_counter_category order by name asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all Active Categorys 
	function fetchAllActiveRecords()
	{
		$qry	= "select  id, name, description,active from m_retail_counter_category where active=1 order by name asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Category based on id 
	function find($categoryId)
	{
		$qry	= "select id, name, description  from m_retail_counter_category where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteCategory($categoryId)
	{
		$qry	= " delete from m_retail_counter_category where id=$categoryId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Category
	function updateCategory($categoryId, $name, $descr)
	{
		$qry	= " update m_retail_counter_category set name='$name', description='$descr' where id=$categoryId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Check whether the selected category link with any other screen
	function checkMoreEntriesExist($categoryId)
	{
		$qry = "select id from m_retail_counter where rt_ct_category_id='$categoryId'";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateRetailCounterconfirm($categoryId){
		$qry	= "update m_retail_counter_category set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateRetailCounterReleaseconfirm($categoryId){
	$qry	= "update m_retail_counter_category set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}


}
?>