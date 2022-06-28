<?php
class SubCategory
{
	/****************************************************************
	This class deals with all the operations relating to Inventory Sub-Category
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function SubCategory(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add 
	function addSubCategory($categoryId, $name, $descr, $unitGroup, $checkPoint, $userId, $carton)
	{
		$qry	= "insert into stock_subcategory (category_id, name, description, unitgroup_id, check_point, created, createdby, carton) values('".$categoryId."', '".$name."', '".$descr."', '$unitGroup', '$checkPoint', NOW(), '$userId', '$carton')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();		
		return $insertStatus;
	}
	// Add Ceck Point
	function addCheckPoint($subCategoryId, $selCheckPoint)
	{
		$qry	= "insert into stk_subcategory_chkpoint (subcategory_id, check_point_id) values('".$subCategoryId."', '".$selCheckPoint."')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $categoryFilterId=null)
	{
		$whr = " a.category_id=b.id ";

		if ($categoryFilterId) $whr .= " and a.category_id='$categoryFilterId' ";

		$orderBy	= " a.name asc ";
		$limit 		= " $offset,$limit ";
				
		$qry	= "select a.id, a.category_id, a.name, a.description, b.name, a.unitgroup_id, a.check_point, a.carton,a.active,(select count(a1.id) from m_stock a1 where a1.subcategory_id=a.id) as tot from stock_subcategory a, stock_category b ";	
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all SubCategory
	function fetchAllRecords($categoryFilterId=null)
	{
		$whr = " a.category_id=b.id ";

		if ($categoryFilterId) $whr .= " and a.category_id='$categoryFilterId' ";

		$orderBy	= " a.name asc ";
				
		$qry	= "select a.id, a.category_id, a.name, a.description, b.name, a.unitgroup_id, a.check_point, a.carton,a.active,(select count(a1.id) from m_stock a1 where a1.subcategory_id=a.id) as tot from stock_subcategory a, stock_category b ";	
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get SubCategory based on id 
	function find($subcategoryId)
	{
		$qry	= "select id, category_id, name, description, unitgroup_id, check_point, carton from stock_subcategory where id=$subcategoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	// Get Check Point Recs
	function getChkPointRecs($subcategoryId)
	{
		$qry = " select id, check_point_id from stk_subcategory_chkpoint where subcategory_id='$subcategoryId' ";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete a Sub Category 
	function deleteSubCategory($subCategoryId)
	{
		$qry	=	" delete from stock_subcategory where id=$subCategoryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}

	# Update  
	function updateSubCategory($subcategoryId, $categoryId, $name, $descr, $unitGroup, $checkPoint, $carton)
	{
		$qry	= " update stock_subcategory set category_id='$categoryId', name='$name', description='$descr', unitgroup_id='$unitGroup', check_point='$checkPoint', carton='$carton' where id=$subcategoryId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Update 
	function updateCheckPoint($chkPointEntryId, $selCheckPoint)
	{
		$qry	= " update stk_subcategory_chkpoint set check_point_id='$selCheckPoint' where id=$chkPointEntryId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete Check Point Entry Rec
	function deleteChkPointEntryRec($chkPointEntryId)
	{
		$qry	=	" delete from stk_subcategory_chkpoint where id=$chkPointEntryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}

	# delete Chk Point Recs (All Based on Subcategory Id)
	function deleteCheckPointRecs($subcategoryId)
	{
		$qry	=	" delete from stk_subcategory_chkpoint where subcategory_id=$subcategoryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}
	
	# Returns list based on Category -- Used in Stock Entry
	function filterRecords($categoryId)
	{
		//$qry	= "select a.id, a.category_id, a.name, a.description, b.name from stock_subcategory a, stock_category b where a.category_id=b.id and a.category_id='$categoryId' order by a.name asc";

		$qry	= "select a.id, a.category_id, a.name, a.description, b.name from stock_subcategory a, stock_category b where a.category_id=b.id and a.category_id='$categoryId' and a.active=1 and b.active=1 order by a.name asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	// Using in Import Stock Xajax
	/* Get subcategories of selected category*/
	function getAssocSubCategories($categoryId)
	{
		$resultArr = array();
		//$qry	=	"select a.id, a.category_id, a.name, a.description, b.name from stock_subcategory a, stock_category b where a.category_id=b.id and a.category_id='$categoryId' order by a.name asc";

		$qry	=	"select a.id, a.category_id, a.name, a.description, b.name from stock_subcategory a, stock_category b where a.category_id=b.id and a.category_id='$categoryId' and a.active=1 and b.active=1 order by a.name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		if( sizeof( $result ) > 0 ) $resultArr = array(''=>'-- Select --');
		else $resultArr = array(''=>'-- No sub categories found. --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
	}

	# -----------------------------------------------------
	# Checking Sub Category in use ();
	# -----------------------------------------------------
	function subCategoryRecInUse($subCategoryId)
	{		
		$qry = " select a.id as id from m_stock a where a.subcategory_id='$subCategoryId'";
		/*
		$qry = " select id from (
				select a.id as id from table a where a.fish_id='$fishId'
			union
				select a1.id as id from table a1 where a1.fish_id='$fishId'
			union 
				select a2.id as id from table a2 where a2.fish='$fishId'
			union 
				select a3.id as id from table a3 where a3.fish_id='$fishId'
			) as X group by id ";
		*/
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Get Sub Categories (using in Ajax Section)
	function getSubCategories($categoryId)
	{
		$resultArr = array();
		//$qry	=	"select a.id, a.name from stock_subcategory a where a.category_id='$categoryId' order by a.name asc";
		$qry	=	"select a.id, a.name from stock_subcategory a where a.category_id='$categoryId' and a.active=1 order by a.name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		if( sizeof( $result ) > 0 ) $resultArr = array(''=>'-- Select --');
		else $resultArr = array(''=>'-- No Sub-Category --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;	
	}

	# get Sub category Stock Type
	function getSubCategoryStockType($subCategoryId)
	{
		$qry	= "select carton from stock_subcategory where id='$subCategoryId' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Returns list based on Category (Using other screen)
	function filterSubCategoryRecords($categoryId)
	{
		$qry	= "select a.id, a.name from stock_subcategory a where a.category_id='$categoryId' order by a.name asc ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function updateSubCategoryconfirm($subcategoryId){
	$qry	= "update stock_subcategory set active='1' where id=$subcategoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateSubCategoryReleaseconfirm($subcategoryId){
	$qry	= "update stock_subcategory set active='0' where id=$subcategoryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

}
?>