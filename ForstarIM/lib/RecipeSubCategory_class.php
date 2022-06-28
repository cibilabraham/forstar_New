<?php
class RecipeSubCategory
{
	/****************************************************************
	This class deals with all the operations relating to Recipe Category
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RecipeSubCategory(&$databaseConnect)
    {
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add Category
	function addRecipeCategory($name, $descr, $recpMainCategory)
	{
		$qry = "insert into recipe_subcategory (name, description, main_category_id) values('".$name."', '".$descr."', '$recpMainCategory')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit, $categoryFilterId)
	{		
		if ($categoryFilterId!="") $whr = "  main_category_id=$categoryFilterId";
		else $whr = "";
		$orderBy = " name asc";
		$limit   = " $offset, $limit ";

		$qry	= "select  id, name, description, main_category_id,active from recipe_subcategory ";		
		if ($whr!="")		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Categorys 
	function fetchAllRecords($categoryFilterId)
	{
		//$qry	=	"select  id, name, description, main_category_id from recipe_subcategory order by name";
		if ($categoryFilterId!="") $whr = "  main_category_id=$categoryFilterId";
		else $whr = "";

		$orderBy = " name asc";
		$qry	= "select  id, name, description, main_category_id,active from recipe_subcategory ";		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveSubcategory($categoryFilterId)
	{
		//$qry	=	"select  id, name, description, main_category_id from recipe_subcategory order by name";
		if ($categoryFilterId!="") $whr = "  main_category_id=$categoryFilterId and active=1";
		else $whr = "active=1";

		$orderBy = " name asc";
		$qry	= "select  id, name, description, main_category_id,active from recipe_subcategory ";		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Category based on id 
	function find($categoryId)
	{
		$qry	= " select id, name, description, main_category_id  from recipe_subcategory where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteCategory($categoryId)
	{
		$qry	=	" delete from recipe_subcategory where id=$categoryId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Category
	function updateCategory($categoryId, $name, $descr, $recpMainCategory)
	{
		$qry	= " update recipe_subcategory set name='$name', description='$descr', main_category_id='$recpMainCategory' where id=$categoryId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Check whether the selected category link with any other screen
	function checkMoreEntriesExist($categoryId)
	{
		$qry = "select id from m_recipe where category_id='$categoryId'";
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateCategoryconfirm($categoryId)
	{
	$qry	= "update recipe_subcategory set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateCategoryReleaseconfirm($categoryId)
	{
		$qry	= "update recipe_subcategory set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>