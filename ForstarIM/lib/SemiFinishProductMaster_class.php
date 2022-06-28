<?php
class SemiFinishedProduct
{  
	/****************************************************************
	This class deals with all the operations relating to Semi-Finished Product Master
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function SemiFinishedProduct(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert A Rec
	function addSemiFinishProduct($productCode, $productName, $ingCategory, $subCategory, $productRatePerKgPerBatch, $kgPerBatch, $productRatePerBatch, $productKgRawPerBatch, $productYieldPercent, $processHrs, $processMints, $gasHrs, $gasMints, $steamHrs, $steamMints, $fixedStaffHrs, $fixedStaffMints, $noOfFixedStaff, $ingCostPerKg, $productionCostPerKg, $totProdCostPerKg, $openingQty, $userId, $selIngRateList, $selManPowerRateList)
	{		
		$qry = " insert into m_sf_product (`code`, `name`, `category_id`, `subcategory_id`, `rate_per_kg_per_btch`, `kg_per_batch`,  `rate_per_batch`, `kg_raw_per_batch`, `yield_percent`, `process_hrs`, `process_mints`, `gas_hrs`, `gas_mints`, `steam_hrs`, `steam_mints`, `labour_hrs`, `labour_mints`, `fixed_staff`, `ing_cost`, `production_cost`, `product_cost`,opening_qty, actual_qty, created, createdby,   `ing_ratelist_id`, `mp_ratelist_id`) values ('$productCode', '$productName', '$ingCategory', '$subCategory', '$productRatePerKgPerBatch', '$kgPerBatch', '$productRatePerBatch', '$productKgRawPerBatch', '$productYieldPercent', '$processHrs', '$processMints', '$gasHrs', '$gasMints', '$steamHrs', '$steamMints', '$fixedStaffHrs', '$fixedStaffMints', '$noOfFixedStaff', '$ingCostPerKg', '$productionCostPerKg', '$totProdCostPerKg' ,'$openingQty', '$openingQty',  NOW(), '$userId', '$selIngRateList', '$selManPowerRateList')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	/*
		Add Semi- Finished Product Ing Recs
	*/
	function addSfProductIngRecs($lastId, $ingredientId, $quantity, $percentagePerBatch, $ratePerBatch, $selIngType)
	{
		$qry = " insert into m_sf_product_entry (sf_product_id, ingredient_id, raw_qty, percent_per_btch, rate_per_btch, ing_type) values ('$lastId', '$ingredientId', '$quantity', '$percentagePerBatch', '$ratePerBatch', '$selIngType')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	/*
		Var Staff Rec Ins
	*/
	function addSfProductVarStaffRecs($lastId, $manPowerId, $manPowerUnit)
	{
		$qry = " insert into m_sf_product_varstaff (sf_product_id, man_power_id, var_staff) values ('$lastId', '$manPowerId', '$manPowerUnit')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit)
	{
		//$whr 		= " b.id = a.subcategory_id";
		$orderBy 	= " a.name asc";
		$limit 		= " $offset,$limit";

		$qry = "select a.id, a.code, a.name, a.opening_qty, a.actual_qty, b.name, c.name,a.active  from m_sf_product a join ing_category b on b.id = a.subcategory_id left join ing_main_category c on a.category_id=c.id ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		//$whr 		= " b.id = a.subcategory_id";
		$orderBy 	= " a.name asc";
		
		$qry = "select a.id, a.code, a.name, a.opening_qty, a.actual_qty, b.name, c.name,a.active  from m_sf_product a join ing_category b on b.id = a.subcategory_id left join ing_main_category c on a.category_id=c.id ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	
	
	# Get a Record based on Id
	function find($semiProductMasterId)
	{
		$qry = "select id, `code`, `name`, `category_id`, `subcategory_id`, `rate_per_kg_per_btch`, `kg_per_batch`,  `rate_per_batch`, `kg_raw_per_batch`, `yield_percent`, `process_hrs`, `process_mints`, `gas_hrs`, `gas_mints`, `steam_hrs`, `steam_mints`, `labour_hrs`, `labour_mints`, `fixed_staff`, `ing_cost`, `production_cost`, `product_cost`, opening_qty, actual_qty, `ing_ratelist_id`, `mp_ratelist_id` from m_sf_product where id=$semiProductMasterId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateSemiProductMaster($semiProductMasterId, $productCode, $productName, $ingCategory, $subCategory, $productRatePerKgPerBatch, $kgPerBatch, $productRatePerBatch, $productKgRawPerBatch, $productYieldPercent, $processHrs, $processMints, $gasHrs, $gasMints, $steamHrs, $steamMints, $fixedStaffHrs, $fixedStaffMints, $noOfFixedStaff, $ingCostPerKg, $productionCostPerKg, $totProdCostPerKg, $openingQty, $hidExistingQty)
	{
		//Update the actual Qty
		$updateField = "";
		if ($openingQty!=$hidExistingQty) {
			$actualQty = $openingQty-$hidExistingQty;
			if ($actualQty>0) $updateField = ", actual_qty = actual_qty+$actualQty";
			else $updateField = ", actual_qty = actual_qty-'".abs($actualQty)."'";
		}

		//$qry = "update m_sf_product set product_id='$semiFinishProduct', opening_qty='$openingQty' $updateField where id='$semiProductMasterId'";		

		$qry = "update m_sf_product set `code`='$productCode', `name`='$productName', `category_id`='$ingCategory', `subcategory_id`='$subCategory', `rate_per_kg_per_btch`='$productRatePerKgPerBatch', `kg_per_batch`='$kgPerBatch',  `rate_per_batch`='$productRatePerBatch', `kg_raw_per_batch`='$productKgRawPerBatch', `yield_percent`='$productYieldPercent', `process_hrs`='$processHrs', `process_mints`='$processMints', `gas_hrs`='$gasHrs', `gas_mints`='$gasMints', `steam_hrs`='$steamHrs', `steam_mints`='$steamMints', `labour_hrs`='$fixedStaffHrs', `labour_mints`='$fixedStaffMints', `fixed_staff`='$noOfFixedStaff', `ing_cost`='$ingCostPerKg', `production_cost`='$productionCostPerKg', `product_cost`='$totProdCostPerKg', opening_qty='$openingQty' $updateField where id='$semiProductMasterId'";		

		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete SF Ing Recs
	function delSFIngRecs($semiProductMasterId)
	{
		$qry	= " delete from m_sf_product_entry where sf_product_id=$semiProductMasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete SF VARIABLE STAFF Recs
	function delSFVarStaffRecs($semiProductMasterId)
	{
		$qry	= " delete from m_sf_product_varstaff where sf_product_id=$semiProductMasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Purchase Order
	function deleteSemiFinishProductMaster($semiProductMasterId)
	{
		$qry	= " delete from m_sf_product where id=$semiProductMasterId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check Rec Exist
	function chkRecExist($selProduct, $semiProductMasterId)
	{
		$uptdQry = "";
		if ($semiProductMasterId) $uptdQry = " and id!=$semiProductMasterId";
		else $uptdQry	= "";

		$qry = " select id from m_sf_product where product_id='$selProduct' $uptdQry";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Semi Finished Prouct Records
	/*
	function getSemiFinishProductRecs()
	{
		$qry	= " select id, code, name from m_productmaster where base_product='Y' and semi_finished='Y' order by name asc ";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

	# Get semi Finished Product
	function getSemiFinishedActualQty($productId)
	{
		$qry = " select id, opening_qty, actual_qty from m_sf_product where id='$productId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[2]:0;
	}

	# Returns all Ing Records  (Ingredients and Semi Finished Products)
	function fetchAllIngredientRecords($selRateList, $semiFinishProductId)
	{
		$uptdQry = "";
		if ($semiFinishProductId) $uptdQry = " and a.id!=$semiFinishProductId";
		else $uptdQry	= "";

		//$qry = "select a.ingredient_id, b.name, d.id, d.name, c.id, c.name from m_ingredient_rate a, m_ingredient b, ing_category c left join ing_main_category d on b.main_category_id=d.id where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by d.name asc, c.name asc, b.name asc";

		$qry = "select a.ingredient_id, b.name, d.id, d.name, c.id, c.name from m_ingredient_rate a join m_ingredient b on b.id=a.ingredient_id join ing_category c on c.id=b.category_id left join ing_main_category d on b.main_category_id=d.id where a.rate_list_id='$selRateList' order by d.name asc, c.name asc, b.name asc";
		//echo $qry;
	
		//$qrySFinish = " select id, name from m_sf_product $uptdQry order by name asc ";
		$qrySFinish = " select a.id, a.name, b.id, b.name, c.id, c.name from m_sf_product a, ing_main_category b, ing_category c where a.category_id=b.id and a.subcategory_id=c.id $uptdQry order by b.name asc, c.name asc, a.name asc ";
		//echo $qrySFinish;
		
		$result	=	$this->databaseConnect->getRecords($qry);		
		$resultArr = array();
		$i = 0;
		$prevCategoryId 	= "";
		$preSubCategoryId	= "";
		while (list(,$v) = each($result)) {
			$ingredientId	= $v[0];
			$ingName	= $v[1];
			$categoryId 	= $v[2]; 
			$categoryName	= $v[3];
			$subCategoryId 	= $v[4];
			$subCategoryName = $v[5];
			//$resultArr["ING_".$v[0]] = $v[1];
			if ($prevCategoryId!=$categoryId) {
				$resultArr [$i]      = array('',"----- $categoryName -------");	
				$i++;
			}
			$resultArr[$i] 		= array("ING_$ingredientId",$ingName);
			$prevCategoryId 	= $categoryId;
			$preSubCategoryId 	= $subCategoryId;
			$i++;
		}

		# Semi Finished Product Records
		$resultSemi	= $this->databaseConnect->getRecords($qrySFinish);

		if (sizeof($resultSemi)>0) {
			$resultArr [$i]      = array('',"---- Semi-Finished Products ----");		
			$prevCategoryId 	= "";
			$preSubCategoryId	= "";	
			while (list(,$v) = each($resultSemi)) {
				$i++;
				$ingredientId	= $v[0];
				$ingName	= $v[1];
				$categoryId 	= $v[2]; 
				$categoryName	= $v[3];
				$subCategoryId 	= $v[4];
				$subCategoryName = $v[5];
				//$resultArr["SFP_".$v[0]] = $v[1];
				if ($prevCategoryId!=$categoryId) {
					$resultArr [$i]      = array('',"----- $categoryName -------");	
					$i++;
				}
				$resultArr[$i] 		= array("SFP_$ingredientId",$ingName);
				$prevCategoryId 	= $categoryId;
				$preSubCategoryId 	= $subCategoryId;
			}
		}		
		return $resultArr;
	}
	
	/* ORIGINAL
	function fetchAllIngredientRecords($selRateList, $semiFinishProductId)
	{
		$uptdQry = "";
		if ($semiFinishProductId) $uptdQry = " where id!=$semiFinishProductId";
		else $uptdQry	= "";

		$qry = "select a.ingredient_id, b.name from m_ingredient_rate a, m_ingredient b, ing_category c where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by c.name asc, b.name asc";

		$qrySFinish = " select id, name from m_sf_product $uptdQry order by name asc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);		

		while (list(,$v) = each($result)) {
			$resultArr["ING_".$v[0]] = $v[1];
		}

		# Semi Finished Product Records
		$resultSemi	= $this->databaseConnect->getRecords($qrySFinish);

		if (sizeof($resultSemi)>0) {
			$resultArr ["-1"]      = "-- Semi-Finished Products--";		
			while (list(,$v) = each($resultSemi)) {
				$resultArr["SFP_".$v[0]] = $v[1];
			}
		}		
		return $resultArr;
	}
	*/


	# Find the Ingredient Rate ( clean rate/kg - last_price) (Taking - Raw Rate Per Kg)
	function getIngredientRate($ingredientId, $selRateListId)
	{		
		$qry = "select rate_per_kg, yield from m_ingredient_rate where ingredient_id=$ingredientId and rate_list_id=$selRateListId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array(0,0);
		/* Return rate and declared yield*/
	}

	# Get Finished Product Rate
	function getSemiFinishRate($semiFinishProductId)
	{		
		$qry = "select product_cost, yield_percent from m_sf_product where id='$semiFinishProductId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array(0,0);
		/* Return rate and declared yield*/
	}

	#Fetch All Records based on Master Id from m_sf_product_entry TABLE	
	function fetchAllSelIngRecs($semiFinishProductId)
	{
		$qry = " select id, sf_product_id, ingredient_id, raw_qty, percent_per_btch, rate_per_btch, ing_type from m_sf_product_entry where sf_product_id='$semiFinishProductId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Variable Man Power Records
	function getSelVarManPowerRecords($selRateList, $semiFinishProductId)
	{
		$qry	= "select a.id, a.name, a.type, a.unit, a.pu_cost, a.tot_cost, b.var_staff from m_prodn_matrix_manpower a left join m_sf_product_varstaff b on b.man_power_id=a.id and b.sf_product_id='$semiFinishProductId' where a.rate_list_id='$selRateList' and a.type='V' order by a.name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find Semi-Finish Product Name (using in Combo Matrix etc)
	function getSemiFinishProductName($productId)
	{
		$rec = $this->find($productId);
		return (sizeof($rec)>0)?$rec[2]:"";
	}


	function updatesemiProductMasterconfirm($productId)
	{
	$qry	= "update m_sf_product set active='1' where id=$productId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatesemiProductMasterReleaseconfirm($productId)
	{
		$qry	= "update m_sf_product set active='0' where id=$productId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>