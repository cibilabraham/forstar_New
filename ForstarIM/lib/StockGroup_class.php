<?php
class StockGroup
{
	/****************************************************************
	This class deals with all the operations relating to Stock Group
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function StockGroup(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Filter Unit from Sub Category
	function filterUnitRecs($selSubCategoryId)
	{
		//$qry = " select b.id, b.name from stock_subcategory a, m_stock_unit b where a.unitgroup_id=b.unitgroup_id and a.id=$selSubCategoryId order by b.name asc";

		$qry = " select b.id, b.name from stock_subcategory a, m_stock_unit b where a.unitgroup_id=b.unitgroup_id and a.id=$selSubCategoryId and b.active=1 order by b.name asc";
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Add a Stock Group
	function addStockGroup($selCategory, $selSubCategory, $basicStkUnit, $userId)
	{
		$qry	= "insert into stock_group (category_id, sub_category_id, basic_unit_id, created, createdby) values('$selCategory', '$selSubCategory', '$basicStkUnit', NOW(), $userId)";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	# Add a Stock Group Entry
	function addStockGroupEntry($stkGroupId, $stkFieldId, $stkFieldValidation)
	{
		$qry	= "insert into stock_group_entry (main_id, stk_field_id, field_validation) values('$stkGroupId', '$stkFieldId', '$stkFieldValidation')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Stock
	function fetchAllPagingRecords($offset, $limit, $categoryFilterId, $subCategoryFilterId)
	{		
		//$whr = " a.category_id=b.id and a.sub_category_id=c.id";
		$whr = " a.category_id=b.id";

		if ($categoryFilterId!="") $whr .= " and a.category_id=".$categoryFilterId;
		if ($subCategoryFilterId!="") $whr .= " and a.sub_category_id=".$subCategoryFilterId;

		$orderBy 	= " b.name asc, c.name asc";
		$limit 		= " $offset,$limit";

	
		$qry = "select a.id, a.category_id, a.sub_category_id, a.basic_unit_id, b.name, c.name, d.name,a.active from (stock_group a, stock_category b) left join stock_subcategory c on a.sub_category_id=c.id left join m_stock_unit d on a.basic_unit_id=d.id";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		//echo $qry;		
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords($categoryFilterId, $subCategoryFilterId)
	{
		//$whr = " a.category_id=b.id and a.sub_category_id=c.id";
		$whr = " a.category_id=b.id";

		if ($categoryFilterId!="") $whr .= " and a.category_id=".$categoryFilterId;
		if ($subCategoryFilterId!="") $whr .= " and a.sub_category_id=".$subCategoryFilterId;

		$orderBy 	= " b.name asc, c.name asc";
			
		$qry = "select a.id, a.category_id, a.sub_category_id, a.basic_unit_id, b.name, c.name, d.name,a.active from (stock_group a, stock_category b) left join stock_subcategory c on a.sub_category_id=c.id left join m_stock_unit d on a.basic_unit_id=d.id";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Stock based on id  
	# ---------------------- NEED TO MODIFY BELOW QRY(findCatORSubCatWiseRec) WHEN MODIFY
	function find($stockGroupId)
	{
		$qry = "select id, category_id, sub_category_id, basic_unit_id from stock_group where id=$stockGroupId";
		return $this->databaseConnect->getRecord($qry);
	}

	function findCatORSubCatWiseRec($cpyFrmCategoryId, $cpyFrmSubCategoryId)
	{
		$qry = "select id, category_id, sub_category_id, basic_unit_id from stock_group where category_id='$cpyFrmCategoryId' and sub_category_id='$cpyFrmSubCategoryId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	# ------------------------------- <> -------------------


	# Get AllStock group Recs
	function getAllStockGroupRecs($stockGroupId)
	{
		$qry	= " select id, stk_field_id, field_validation from stock_group_entry where main_id='$stockGroupId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  a  Stock
	function updateStockGroup($stockGroupId, $selCategory, $selSubCategory, $basicStkUnit)
	{
		$qry = " update stock_group set category_id='$selCategory', sub_category_id='$selSubCategory', basic_unit_id='$basicStkUnit' where id=$stockGroupId ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# delete Entry Rec
	function delStockGroupEntryRecs($stockGroupId)
	{
		$qry	=	" delete from stock_group_entry where main_id='$stockGroupId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Stock
	function deleteStockGroup($stockGroupId)
	{
		$qry	= " delete from stock_group where id=$stockGroupId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update Stock Group Entry
	function updateStockGroupEntry($stkGroupEntryId, $stkFieldId, $stkFieldValidation)
	{
		$qry = " update stock_group_entry set stk_field_id='$stkFieldId', field_validation='$stkFieldValidation' where id=$stkGroupEntryId ";	
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#  Chk Group Id in Use (M_STOCK)
	function checkStkGroupIdInUse($stkGroupEntryId)
	{
		$qry	= " select id from m_stock_stkg_entry where stk_group_entry_id='$stkGroupEntryId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Delete Stk Group Inividual rec
	function delStkGroupIndividualRec($stkGroupEntryId)
	{
		$qry	= " delete from stock_group_entry where id=$stkGroupEntryId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Main Rec
	function chkStockGroupRecExist($stockGroupId)
	{
		# Get Entry Recs
		$stkgEntryRecs = $this->getAllStockGroupRecs($stockGroupId);
		$recInUse = false;
		foreach ($stkgEntryRecs as $sr) {
			$stkgEId = $sr[0];
			$usedRec = $this->checkStkGroupIdInUse($stkgEId);
			if ($usedRec) $recInUse = true;	
		}		
		return $recInUse;
	}

	# Get Stock Group Recs
	function getStockGroupRecs($categoryFilterId, $subCategoryFilterId)
	{		
		/*
		$whr = " b.stk_field_id=c.id and a.id=b.main_id and a.category_id='$categoryFilterId' and (a.sub_category_id='$subCategoryFilterId' or a.sub_category_id is not null) ";
		$qry = " select a.id, c.id, a.category_id, a.sub_category_id, a.basic_unit_id, b.id, b.main_id, c.label_name, c.field_type, c.field_name, c.field_default_value, c.field_size, b.field_validation, c.field_data_type from stock_group a, stock_group_entry b, stock_field c ";		
		if ($whr) $qry .= " where ".$whr;
		*/
		$qry = " select a.id, c.id, a.category_id, a.sub_category_id, a.basic_unit_id, b.id, b.main_id, c.label_name, c.field_type, c.field_name, c.field_default_value, c.field_size, b.field_validation, c.field_data_type, c.unit_group_id from stock_group a, stock_group_entry b, stock_field c where b.stk_field_id=c.id and a.id=b.main_id and a.category_id='$categoryFilterId' and (a.sub_category_id='$subCategoryFilterId' or a.sub_category_id=0)";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Already selected Field Recs
	function getSelFieldRecs($categoryId, $subCategoryId)
	{
		$whr = " a.id=b.main_id and a.category_id='$categoryId' and a.sub_category_id=0 ";
		//if ($subCategoryId!=0) $whr .= " and a.sub_category_id='$subCategoryId' ";

		$qry = " select b.id, b.stk_field_id, b.field_validation from stock_group a, stock_group_entry b ";

		if ($whr) $qry .= " where ".$whr;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check Group Exist
	function chkGroupExist($categoryId, $subCategoryId, $editStockGroupId)
	{
		$whr = " category_id='$categoryId' ";
		if ($subCategoryId!=0) $whr .= " and sub_category_id='$subCategoryId' ";
		else $whr .= " and sub_category_id='$subCategoryId' ";

		if ($editStockGroupId!="") $whr .= " and id!='$editStockGroupId' ";
		
		$qry = " select id from stock_group ";
		if ($whr) $qry .= " where ".$whr;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Already selected Field Recs
	function getSelEditFieldRecs($categoryId, $subCategoryId)
	{
		$whr = " a.id=b.main_id and a.category_id='$categoryId' and a.sub_category_id=0 ";
		
		$qry = " select b.id, b.stk_field_id, b.field_validation from stock_group a, stock_group_entry b ";

		if ($whr) $qry .= " where ".$whr;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Sub Category used in Stock group
	function getLabelUsageRecs($categoryId, $subCategoryId, $fieldLabelId)
	{
		$qry = " select c.id, c.name from (stock_group a, stock_group_entry b) left join stock_subcategory c on a.sub_category_id=c.id where a.id=b.main_id and a.category_id='$categoryId' and b.stk_field_id='$fieldLabelId' and a.sub_category_id!=0 ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Stock Group Entry Id
	function getStkGroupId($categoryId, $subCategoryId, $fieldLabelId)
	{
		$qry = " select b.id from stock_group a, stock_group_entry b where a.id=b.main_id and a.category_id='$categoryId' and a.sub_category_id='$subCategoryId' and b.stk_field_id='$fieldLabelId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Check Field Group Exist in Stock Master
	function chkFieldGroupExist($categoryId, $subCatId, $stkGroupEntryId)
	{
		$qry = " select a.id from m_stock a, m_stock_stkg_entry b where a.id=b.stock_main_id and a.category_id='$categoryId' and a.subcategory_id='$subCatId' and b.stk_group_entry_id='$stkGroupEntryId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}	

	# Get Stock Group Main Id
	function getStkGroupMainId($stkGroupEntryId)
	{
		$qry = " select main_id from stock_group_entry where id = '$stkGroupEntryId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Check More Entry Exist in Stock Group Entry	
	function chkMoreEntryExistInStkGEntry($stkGroupMainId)
	{
		$qry = " select id from stock_group_entry where main_id = '$stkGroupMainId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}


	#-----------------------

	function updateStockGroupconfirm($stkGroupEntryId){
		$qry	= "update stock_group set active='1' where id=$stkGroupEntryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	

	

	function updateStockGroupReleaseconfirm($stkGroupEntryId){
	$qry	= "update stock_group set active='0' where id=$stkGroupEntryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

		
	
}
?>