<?php
class IngredientCategory
{
	/****************************************************************
	This class deals with all the operations relating to Ingredient Category
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function IngredientCategory(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add Category
	function addIngredientCategory($name, $descr, $ingMainCategory)
	{
		$qry = "insert into ing_category (name, description, main_category_id) values('".$name."', '".$descr."', '$ingMainCategory')";
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

		$qry	= "select  id, name, description, main_category_id,active from ing_category ";		
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
		//$qry	=	"select  id, name, description, main_category_id from ing_category order by name";
		if ($categoryFilterId!="") $whr = "  main_category_id=$categoryFilterId";
		else $whr = "";

		$orderBy = " name asc";
		$qry	= "select  id, name, description, main_category_id,active from ing_category ";		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveSubcategory($categoryFilterId)
	{
	
		//$qry	=	"select  id, name, description, main_category_id from ing_category order by name";
		if ($categoryFilterId!="") $whr = "  main_category_id=$categoryFilterId and active=1";
		else $whr = "active=1";

		$orderBy = " name asc";
		$qry	= "select  id, name, description, main_category_id,active from ing_category ";		
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Category based on id 
	function find($categoryId)
	{
		$qry	= " select id, name, description, main_category_id  from ing_category where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteCategory($categoryId)
	{
		$qry	=	" delete from ing_category where id=$categoryId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Category
	function updateCategory($categoryId, $name, $descr, $ingMainCategory)
	{
		$qry	= " update ing_category set name='$name', description='$descr', main_category_id='$ingMainCategory' where id=$categoryId";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Check whether the selected category link with any other screen
	function checkMoreEntriesExist($categoryId)
	{
		$qry = "select id from m_ingredient where category_id='$categoryId'";
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateCategoryconfirm($categoryId)
	{
	$qry	= "update ing_category set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateCategoryReleaseconfirm($categoryId)
	{
		$qry	= "update ing_category set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>