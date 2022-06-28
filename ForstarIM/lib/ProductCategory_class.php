<?php
class ProductCategory
{
	/****************************************************************
	This class deals with all the operations relating to Product Category
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductCategory(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Category
	function addProductCategory($name, $descr)
	{
		$qry	=	"insert into m_product_category (name, description) values('".$name."','".$descr."')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	=	"select  id, name, description,active from m_product_category order by name limit $offset,$limit";

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Categorys 
	function fetchAllRecords()
	{
		$qry	=	"select  id, name, description,active from m_product_category order by name";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Categorys 
	function fetchAllRecordsActiveCategory()
	{
		$qry	=	"select  id, name, description,active from m_product_category where active=1 order by name";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Category based on id 
	function find($categoryId)
	{
		$qry	=	"select id, name, description  from m_product_category where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteCategory($categoryId)
	{
		$qry	=	" delete from m_product_category where id=$categoryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	# Update  a  Category
	function updateCategory($categoryId, $name, $descr)
	{
		$qry	=	" update m_product_category set name='$name', description='$descr' where id=$categoryId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Check whether the selected category link with any other screen
	/*function checkMoreEntriesExist($categoryId)
	{
		$qry = "select id from m_ingredient where category_id='$categoryId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}*/


	function updateCategoryconfirm($categoryId)
	{
	$qry	= "update m_product_category set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateCategoryReleaseconfirm($categoryId)
	{
		$qry	= "update m_product_category set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}