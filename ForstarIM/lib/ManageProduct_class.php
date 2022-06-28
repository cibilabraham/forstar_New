<?php
class ManageProduct
{  
	/****************************************************************
	This class deals with all the operations relating to Manage Product 
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ManageProduct(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	#Insert A Rec
	function addProduct($productCode, $productName, $productCategory, $productState, $productGroup, $netWt, $openingQty, $userId, $identifiedNo)
	{		
		$qry = "insert into m_product_manage (code, name, category_id, product_state_id, product_group_id, net_wt, opening_qty, actual_qty, created, createdby, identified_no) values('$productCode', '$productName', '$productCategory', '$productState', '$productGroup', '$netWt', '$openingQty', '$openingQty', NOW(), '$userId', '$identifiedNo')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	

	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit, $selProductCategoryId, $selProductStateId, $selProductGroupId, $srchName)
	{		
		$whr = " id is not null ";
			
		if ($selProductCategoryId!="") $whr .= " and category_id=".$selProductCategoryId;
		if ($selProductStateId!="") $whr .= " and product_state_id=".$selProductStateId;
		if ($selProductGroupId!="" && $selProductStateId!="") {
			$whr .= " and product_group_id=".$selProductGroupId;
		}

			if ($srchName!=''){
			if($whr=="")
			//$whr .= " name like '% ".$srchName. "%'";
			$whr .= " (name like '%".$srchName."%' or name like '".$srchName."%'" ." or name like '%".$srchName."')" ;

			else	
			//$whr .= " and name like '% ".$srchName. "%'";
			 $whr .= " and (name like '%".$srchName."%' or name like '".$srchName."%'" ." or name like '%".$srchName."')" ;
		} 
		
		$orderBy 	= " name asc ";
		$limit 		= " $offset,$limit";

		$qry = "select id, code, name, net_wt, opening_qty, actual_qty, identified_no, category_id, product_state_id, product_group_id,active  from m_product_manage";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords($selProductCategoryId, $selProductStateId, $selProductGroupId, $srchName)
	{
		$whr = " a.id is not null ";
			
		if ($selProductCategoryId!="") $whr .= " and a.category_id=".$selProductCategoryId;
		if ($selProductStateId!="") $whr .= " and a.product_state_id=".$selProductStateId;
		if ($selProductGroupId!="" && $selProductStateId!="") {
			$whr .= " and a.product_group_id=".$selProductGroupId;
		}
		
			if ($srchName!=''){
			if($whr=="")
			//$whr .= " name like '% ".$srchName. "%'";
			$whr .= " (a.name like '%".$srchName."%' or a.name like '".$srchName."%'" ." or a.name like '%".$srchName."')" ;

			else	
			//$whr .= " and name like '% ".$srchName. "%'";
			 $whr .= " and (a.name like '%".$srchName."%' or a.name like '".$srchName."%'" ." or a.name like '%".$srchName."')" ;
		} 
		
		
		
		$orderBy 	= " a.name asc ";
		
		$qry = " select a.id, a.code, a.name, a.net_wt, a.opening_qty, a.actual_qty, b.id, b.name, a.identified_no, a.category_id, a.product_state_id, a.product_group_id from m_product_manage a left join m_product_category b on a.category_id=b.id ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function getAllProductRecs()
	{
		$whr = " a.id is not null ";
		/*	
		if ($selProductCategoryId!="") $whr .= " and a.category_id=".$selProductCategoryId;
		if ($selProductStateId!="") $whr .= " and a.product_state_id=".$selProductStateId;
		if ($selProductGroupId!="" && $selProductStateId!="") {
			$whr .= " and a.product_group_id=".$selProductGroupId;
		}
		*/
		$orderBy 	= " b.name asc, a.name asc ";
		
		$qry = " select a.id, a.code, a.name, b.id, b.name from m_product_manage a left join m_product_category b on a.category_id=b.id ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array();
		$prevCategoryId 	= "";
		$i = 0;
		foreach ($result as $r) {
			$productId 	= $r[0];
			$code		= $r[1];
			$name		= $r[2];
			$categoryId 	= $r[3];
			$categoryName	= $r[4];
			if ($prevCategoryId!=$categoryId) {
				$resultArr [$i]      = array('','',"----- $categoryName -------");	
				$i++;
			}	
		
			$resultArr[$i] 		= array($productId,$code,$name);
			$prevCategoryId 	= $categoryId;
			$i++;
		}
		return $resultArr;
	}
		
	# Get a Record based on Id
	function find($productId)
	{
		$qry = "select id, code, name, category_id, product_state_id, product_group_id, net_wt, opening_qty, identified_no from m_product_manage where id=$productId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on Master Id from m_product_manage_entry TABLE	
	function fetchAllIngredients($editProductId)
	{
		$qry = "select id, product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty from m_product_manage_entry where product_id='$editProductId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Update
	function updateProduct($productId, $productCode, $productName, $productCategory, $productState, $productGroup, $netWt, $openingQty, $hidOpeningQty, $identifiedNo, $userId)
	{
		//Update the actual Qty
		$updateField = "";
		if ($openingQty!=$hidOpeningQty) {
			$actualQty = $openingQty-$hidOpeningQty;
			if ($actualQty>0) $updateField = ", actual_qty=actual_qty+$actualQty";
			else $updateField = ", actual_qty=actual_qty-'".abs($actualQty)."'";
		}
		$qry = "update m_product_manage set code='$productCode', name='$productName', category_id='$productCategory', product_state_id='$productState', product_group_id='$productGroup', net_wt='$netWt', opening_qty='$openingQty', identified_no='$identifiedNo', modified=NOW(), modifiedby='$userId' $updateField where id='$productId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete 
	function deleteProduct($productId)
	{
		$qry	=	" delete from m_product_manage where id=$productId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check Whether Product Group Exist
	function checkProductGroupExist($productStateId)
	{
		$qry = "select product_group from m_product_state where id=$productStateId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]=='Y')?true:false;
	}

	# Checking Product using in any where
	# (Product MRP Master, Distributor Margin Structure, Product Identifier, Sales Order/ Product Management)
	function chkProductUsed($productId)
	{		
		$qry = " select id from (
				select id from m_product_mrp where product_id='$productId'
			union
				select a1.id from m_distributor_margin a1 where a1.product_id='$productId'	
			union
				select a2.id from m_product_identifier a2 where a2.product_id='$productId'
			union
				select a3.id from t_salesorder_entry a3 where a3.product_id='$productId'
			union
				select a4.id from m_product_status a4 where a4.product_id='$productId'
			union
				select a5.id from m_excise_duty a5 where a5.product_id='$productId'
					
			) as X group by id ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}	

	
	# Filter State List
	function filterProductGroupList($productGroupExist)
	{		
		$qry	=	"select  id, name from m_product_group order by name";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (!$productGroupExist) $resultArr = array('0'=>'-- No Group --');		
		else if ($productGroupExist) {			
			$resultArr = array(''=>'-- Select All --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

	/*
	* Returns all Distinct Net Wt
	*/
	function getAllProductNetWt()
	{
		$qry = " select distinct net_wt from m_product_manage order by net_wt asc ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function chkProductCodeExist($pCode, $cId)
	{
		$qry = " select id from m_product_manage where code='$pCode'";
		if ($cId!="") $qry .= " and id!='$cId' ";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function chkProductIdentifiedExist($identifiedNo, $cId)
	{
		$qry = " select id from m_product_manage where identified_no='$identifiedNo'";
		if ($cId!="") $qry .= " and id!='$cId' ";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function chkPExciseCodeExist($exciseCode, $cId)
	{
		$qry = " select id from m_product_manage where excise_code='$exciseCode'";
		if ($cId!="") $qry .= " and id!='$cId' ";

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getExciseDutyMasterRec($pCategoryId, $pStateId, $pGroupId, $exDutyRateListId)
	{
		$qry = "select id, excise_duty, excise_rate_list_id, product_category_id, product_state_id, product_group_id, base_excise_id, chapter_subheading, ex_goods_id from m_excise_duty where product_category_id='$pCategoryId' and product_state_id='$pStateId' and product_group_id='$pGroupId' and excise_rate_list_id='$exDutyRateListId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	// Excemption rec
	function getExDutyByProductId($productId, $exDutyRateListId)
	{
		$qry = "select id, chapter_subheading from m_excise_duty where product_id='$productId' and excise_rate_list_id='$exDutyRateListId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function insertExCodeExemption($productId, $exDutyMasterId, $exciseCode)
	{
		$qry = "insert into m_excise_duty (product_category_id, product_state_id, product_group_id, excise_duty, excise_rate_list_id, base_excise_id, chapter_subheading, ex_goods_id, product_id) select ed.product_category_id, ed.product_state_id, ed.product_group_id, '0', ed.excise_rate_list_id, ed.base_excise_id, '$exciseCode', ed.ex_goods_id, '$productId'  from m_excise_duty ed where ed.id='$exDutyMasterId'";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function updateExCodeExemption($exDtyExemptionMasterId, $exciseCode)
	{
		$qry = "update m_excise_duty set chapter_subheading='$exciseCode' where id='$exDtyExemptionMasterId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function deleteExCodeExemption($exDtyExemptionMasterId)
	{
		$qry	= " delete from m_excise_duty where id=$exDtyExemptionMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateProductconfirm($productId)
	{
	$qry	= "update m_product_manage set active='1' where id=$productId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateProductReleaseconfirm($productId)
	{
		$qry	= "update m_product_manage set active='0' where id=$productId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
?>