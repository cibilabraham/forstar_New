<?php
class FishCategory
{
	/****************************************************************
	This class deals with all the operations relating to fish Category
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function FishCategory(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addCategory($category)
	{
		$qry	= "insert into m_fishcategory (category) values('".$category."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function fetchAllRecordscategoryActive()
	{
		$qry	= "select id,category,active from m_fishcategory where active=1 order by category asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	



	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		if ($confirm){
		$qry	= "select id,category,active from m_fishcategory order by category asc limit $offset, $limit";
		}
		else{
		$qry	= "select id,category,active from m_fishcategory where active=1 order by category asc limit $offset, $limit";
		}
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Category
	function fetchAllRecords($confirm)
	{
		if ($confirm){
		$qry	= "select id,category,active from m_fishcategory order by category asc";
		} else {
		$qry	= "select id,category,active from m_fishcategory where active=1 order by category asc";
		}

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Category based on id 
	function find($categoryId)
	{
		$qry	=	"select id, category from m_fishcategory where id=$categoryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a category
	function updateCategory($category, $categoryId)
	{
		$qry	= " update m_fishcategory set category='$category' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	function updateCategoryconfirm($categoryId)
	{
		$qry	= " update m_fishcategory set active='1' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateCategoryReleaseconfirm($categoryId)
	{
		$qry	= " update m_fishcategory set active='0' where id=$categoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
	# Delete a Category
	function deleteCategory($categoryId)
	{
		$qry	= " delete from m_fishcategory where id=$categoryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Checking category is exist in any other table
	function moreEntriesExist($categoryId)
	{
		$qry = "select id from m_fish where category_id='$categoryId'";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

}
?>