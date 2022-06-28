<?php
class IngredientCriticalParameters
{
	/****************************************************************
	This class deals with all the operations relating to Ingredient Main Category
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function IngredientCriticalParameters(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Category
	function addIngredientCategory($name, $descr)
	{
		$qry	=	"insert into ing_critical_parameters (name, description) values('".$name."','".$descr."')";

		echo $qry; die();
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	#Add Category
	function addIngredientCriticalParameters($name, $descr,$entryType)
	{
		$qry	=	"insert into ing_critical_parameters (name, description,entry_type) values('".$name."','".$descr."','".$entryType."' )";

		echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select  id, name, description,active from ing_main_category order by name asc limit $offset,$limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
		
	}

	# Returns all Categorys 
	function fetchAllRecords()
	{
		$qry	= "select  id, name, description,active from ing_main_category order by name asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRecordsActiveCategory()
	{
		$qry	= "select  id, name, description,active from ing_main_category where active=1 order by name asc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Category based on id 
	function find($categoryId)
	{
		$qry	= "select id, name, description  from ing_main_category where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteCategory($categoryId)
	{
		$qry	=	" delete from ing_main_category where id=$categoryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Category
	function updateCategory($categoryId, $name, $descr)
	{
		$qry	= " update ing_main_category set name='$name', description='$descr' where id=$categoryId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Check whether the selected category link with any other screen
	function checkMoreEntriesExist($categoryId)
	{
		$qry = "select id from ing_category where main_category_id='$categoryId'";
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateCategoryconfirm($categoryId)
	{
	$qry	= "update ing_main_category set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateCategoryReleaseconfirm($categoryId)
	{
		$qry	= "update ing_main_category set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>