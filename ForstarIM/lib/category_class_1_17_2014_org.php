<?php
class Category
{
	/****************************************************************
	This class deals with all the operations relating to Inventory Category
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function Category(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Category
	function addCategory($name,$descr)
	{
		$qry	=	"insert into stock_category (name, description) values('".$name."','".$descr."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select  id, name, description from stock_category order by name limit $offset,$limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Categorys 
	function fetchAllRecords()
	{
		$qry	= "select  id, name, description from stock_category order by name";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Category based on id 
	function find($categoryId)
	{
		$qry	=	"select id, name, description  from stock_category where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteCategory($categoryId)
	{
		$qry	=	" delete from stock_category where id=$categoryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Category
	function updateCategory($categoryId,$name,$descr)
	{
		$qry	= " update stock_category set name='$name', description='$descr' where id=$categoryId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function checkMoreEntriesExist($categoryId)
	{
		$qry = "select id from stock_subcategory where category_id='$categoryId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
}

?>