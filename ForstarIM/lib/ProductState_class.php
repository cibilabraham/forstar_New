<?php
class ProductState
{
	/****************************************************************
	This class deals with all the operations relating to Product Category
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function ProductState(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Category
	function addProductState($name, $descr, $group)
	{
		$qry	=	"insert into m_product_state (name, description, product_group) values('".$name."','".$descr."', '$group')";

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
		$qry	=	"select  id, name, description,active from m_product_state order by name limit $offset,$limit";

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Categorys 
	function fetchAllRecords()
	{
		$qry	=	"select  id, name, description,active from m_product_state order by name";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveProduct()
	{
		$qry	=	"select  id, name, description,active from m_product_state where active=1 order by name";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;

	}

	# Get Category based on id 
	function find($categoryId)
	{
		$qry	=	"select id, name, description, product_group  from m_product_state where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteCategory($categoryId)
	{
		$qry	=	" delete from m_product_state where id=$categoryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	# Update  a  Category
	function updateCategory($categoryId, $name, $descr, $group)
	{
		$qry	=	" update m_product_state set name='$name', description='$descr', product_group='$group' where id=$categoryId";
		//echo $qry;
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

function updateproductStateconfirm($categoryId)
	{
	$qry	= "update m_product_state set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateproductStateReleaseconfirm($categoryId)
	{
		$qry	= "update m_product_state set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}






}