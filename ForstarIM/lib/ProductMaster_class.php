<?php
class ProductMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Product Master
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	# Insert A Rec
	function addProduct($productCode, $productName, $userId, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $semiFinished, $hidIngRateListId, $productRatePerKg, $fishRatePerKg, $gravyRatePerKg)
	{
		$baseProduct = 'Y'; // Setting the base Product
		$qry = "insert into m_productmaster (code , name, created, createdby, category_id, state_id, group_id, net_wt, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty, base_product, semi_finished, ing_rate_list_id, product_rate_per_kg, fish_rate_per_kg, gravy_rate_per_kg) values('$productCode', '$productName', Now(), '$userId', '$productCategory', '$productState', '$productGroup', '$gmsPerPouch', '$productRatePerPouch', '$fishRatePerPouch', '$gravyRatePerPouch', '$productGmsPerPouch', '$fishGmsPerPouch', '$gravyGmsPerPouch', '$productPercentagePerPouch', '$fishPercentagePerPouch', '$gravyPercentagePerPouch', '$productRatePerKgPerBatch', '$fishRatePerKgPerBatch', '$gravyRatePerKgPerBatch', '$pouchPerBatch', '$productRatePerBatch', '$fishRatePerBatch', '$gravyRatePerBatch', '$productKgPerBatch', '$fishKgPerBatch', '$gravyKgPerBatch', '$productRawPercentagePerPouch', '$fishRawPercentagePerPouch', '$gravyRawPercentagePerPouch', '$productKgInPouchPerBatch', '$fishKgInPouchPerBatch', '$gravyKgInPouchPerBatch', '$fishPercentageYield', '$gravyPercentageYield', '$totalFixedFishQty', '$baseProduct', '$semiFinished', '$hidIngRateListId', '$productRatePerKg', '$fishRatePerKg', '$gravyRatePerKg')";
		//echo $qry."<br>";
			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# For adding Ingredient Items
	function addIngredientEntries($lastId, $ingredientId, $quantity, $fixedQtyChk, $fixedQty, $percentagePerBatch, $ratePerBatch, $ingGmsPerPouch, $percentageWtPerPouch, $ratePerPouch, $percentageCostPerPouch, $idealQty, $selIngType, $ratePerKg)
	{
		$qry = "insert into m_productmaster_entry (product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type, rate_per_kg) values('$lastId', '$ingredientId', '$quantity', '$fixedQtyChk', '$fixedQty', '$percentagePerBatch', '$ratePerBatch', '$ingGmsPerPouch', '$percentageWtPerPouch', '$ratePerPouch', '$percentageCostPerPouch', '$idealQty', '$selIngType', '$ratePerKg')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit, $selProductCategoryId, $selProductStateId, $selProductGroupId)
	{
		//$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch from m_productmaster where base_product='Y' order by name asc limit $offset, $limit";

		$whr = " base_product='Y' ";
			
		if ($selProductCategoryId!="") $whr .= " and category_id=".$selProductCategoryId;
		if ($selProductStateId!="") $whr .= " and state_id=".$selProductStateId;
		if ($selProductGroupId!="" && $selProductStateId!="") $whr .= " and group_id=".$selProductGroupId;

		$orderBy 	= " name asc ";
		$limit 		= " $offset,$limit";

		$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch,active from m_productmaster";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllPagingRecordsActiveProductMaster($offset, $limit, $selProductCategoryId, $selProductStateId, $selProductGroupId)
	{
		//$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch from m_productmaster where base_product='Y' order by name asc limit $offset, $limit";

		$whr = " base_product='Y' and active=1";
			
		if ($selProductCategoryId!="") $whr .= " and category_id=".$selProductCategoryId;
		if ($selProductStateId!="") $whr .= " and state_id=".$selProductStateId;
		if ($selProductGroupId!="" && $selProductStateId!="") $whr .= " and group_id=".$selProductGroupId;

		$orderBy 	= " name asc ";
		$limit 		= " $offset,$limit";

		$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch,active from m_productmaster";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Returns all Records
	function fetchAllRecords($selProductCategoryId, $selProductStateId, $selProductGroupId)
	{
		//$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch from m_productmaster where base_product='Y' order by name asc";

		$whr = " base_product='Y' ";
			
		if ($selProductCategoryId!="") $whr .= " and category_id=".$selProductCategoryId;
		if ($selProductStateId!="") $whr .= " and state_id=".$selProductStateId;
		if ($selProductGroupId!="" && $selProductStateId!="") $whr .= " and group_id=".$selProductGroupId;

		$orderBy 	= " name asc ";

		$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch from m_productmaster";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		return $result;
	}

	function fetchAllRecordsActiveProductMaster($selProductCategoryId, $selProductStateId, $selProductGroupId)
	{
		//$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch from m_productmaster where base_product='Y' order by name asc";

		$whr = " base_product='Y' and active=1";
			
		if ($selProductCategoryId!="") $whr .= " and category_id=".$selProductCategoryId;
		if ($selProductStateId!="") $whr .= " and state_id=".$selProductStateId;
		if ($selProductGroupId!="" && $selProductStateId!="") $whr .= " and group_id=".$selProductGroupId;

		$orderBy 	= " name asc ";

		$qry = "select id, code, name, net_wt, fish_gms_per_pouch, gravy_gms_per_pouch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, product_rate_per_pouch, product_gms_per_pouch from m_productmaster";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		return $result;
	}

	# Get all Records
	function getAllPMRecs()
	{
		$qry = "select id, code, name from m_productmaster where base_product='Y' order by name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		return $result;
	}
	
	
	# Get a Record based on Id
	function find($productId)
	{
		$qry = "select id, code, name, category_id, state_id, group_id, net_wt, product_rate_per_pouch, fish_rate_per_pouch, gravy_rate_per_pouch, product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_percent_per_pouch, fish_percent_per_pouch, gravy_percent_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty, semi_finished, ing_rate_list_id, product_rate_per_kg, fish_rate_per_kg, gravy_rate_per_kg from m_productmaster where id=$productId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	#Fetch All Records based on Master Id from m_productmaster_entry TABLE	
	function fetchAllIngredients($editProductId)
	{
		$qry = " select id, product_id, ingredient_id, quantity, fixed_qty_chk, fixed_qty, percent_per_btch, rate_per_btch, ing_gms_per_pouch, percent_wt_per_pouch, rate_per_pouch, percent_cost_per_pouch, cleaned_qty, sel_ing_type from m_productmaster_entry where product_id='$editProductId' order by id asc ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update
	function updateProductMaster($productId, $productCode, $productName, $productCategory, $productState, $productGroup, $gmsPerPouch, $productRatePerPouch, $fishRatePerPouch, $gravyRatePerPouch, $productGmsPerPouch, $fishGmsPerPouch, $gravyGmsPerPouch, $productPercentagePerPouch, $fishPercentagePerPouch, $gravyPercentagePerPouch, $productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch, $pouchPerBatch, $productRatePerBatch, $fishRatePerBatch, $gravyRatePerBatch, $productKgPerBatch, $fishKgPerBatch, $gravyKgPerBatch, $productRawPercentagePerPouch, $fishRawPercentagePerPouch, $gravyRawPercentagePerPouch, $productKgInPouchPerBatch, $fishKgInPouchPerBatch, $gravyKgInPouchPerBatch, $fishPercentageYield, $gravyPercentageYield, $totalFixedFishQty, $semiFinished, $hidIngRateListId, $productRatePerKg, $fishRatePerKg, $gravyRatePerKg)
	{
		$qry = "update m_productmaster set code='$productCode', name='$productName', category_id='$productCategory', state_id='$productState', group_id='$productGroup', net_wt='$gmsPerPouch', product_rate_per_pouch='$productRatePerPouch', fish_rate_per_pouch='$fishRatePerPouch', gravy_rate_per_pouch='$gravyRatePerPouch', product_gms_per_pouch='$productGmsPerPouch', fish_gms_per_pouch='$fishGmsPerPouch', gravy_gms_per_pouch='$gravyGmsPerPouch', product_percent_per_pouch='$productPercentagePerPouch', fish_percent_per_pouch='$fishPercentagePerPouch', gravy_percent_per_pouch='$gravyPercentagePerPouch', product_rate_per_kg_per_btch='$productRatePerKgPerBatch', fish_rate_per_kg_per_btch='$fishRatePerKgPerBatch', gravy_rate_per_kg_per_btch='$gravyRatePerKgPerBatch', pouch_per_btch='$pouchPerBatch', product_rate_per_btch='$productRatePerBatch', fish_rate_per_btch='$fishRatePerBatch', gravy_rate_per_btch='$gravyRatePerBatch', product_kg_per_btch='$productKgPerBatch', fish_kg_per_btch='$fishKgPerBatch', gravy_kg_per_btch='$gravyKgPerBatch', pduct_raw_pcent_per_pouch='$productRawPercentagePerPouch', fish_raw_pcent_per_pouch='$fishRawPercentagePerPouch', gravy_raw_pcent_per_pouch='$gravyRawPercentagePerPouch', pduct_kg_pouch_per_btch='$productKgInPouchPerBatch', fish_kg_pouch_per_btch='$fishKgInPouchPerBatch', gravy_kg_pouch_per_btch='$gravyKgInPouchPerBatch', fish_percent_yield='$fishPercentageYield', gravy_percent_yield='$gravyPercentageYield', total_fixed_fish_qty='$totalFixedFishQty', semi_finished='$semiFinished', ing_rate_list_id='$hidIngRateListId', product_rate_per_kg='$productRatePerKg', fish_rate_per_kg='$fishRatePerKg', gravy_rate_per_kg='$gravyRatePerKg' where id='$productId'";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	#Delete  Purchase Order Item  Recs
	function deleteIngredientItemRecs($productId)
	{
		$qry = " delete from m_productmaster_entry where product_id=$productId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	# Delete a Purchase Order
	function deleteProductMaster($productId)
	{
		$qry	= " delete from m_productmaster where id=$productId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Check Whether Product Group Exist
	function checkProductGroupExist($productStateId)
	{
		$qry = "select product_group from m_product_state where id=$productStateId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]=='Y')?true:false;
	}

	#Find Product Name (using in another Screen)
	function getProductName($productId)
	{
		$rec = $this->find($productId);
		return (sizeof($rec)>0)?$rec[2]:"";
	}
	
	#Find the Ingredient Rate ( clean rate/kg - last_price) (Taking - Raw Rate Per Kg)
	function getIngredientRate($ingredientId, $selRateListId)
	{		
		$qry = "select rate_per_kg, yield from m_ingredient_rate where ingredient_id=$ingredientId and rate_list_id=$selRateListId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array(0,0);
		/* Return rate and declared yield*/
	}

	#Get Product Rate and other rec  (using in Other Screen - Product Matrix)
	/******
	product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch
	******/
	function getProductMasterRec($productId)
	{
		$rec = $this->find($productId);		
		return (sizeof($rec)>0)?array($rec[16],$rec[17],$rec[18]):0;
	}

	#Get Product Master Rec Based on Id
	function getProductRec($productId)
	{
		$rec = $this->find($productId);
		return (sizeof($rec)>0)?array($rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8], $rec[9], $rec[10], $rec[11], $rec[12], $rec[13], $rec[14], $rec[15], $rec[16], $rec[17], $rec[18], $rec[19], $rec[20], $rec[21], $rec[22], $rec[23], $rec[24], $rec[25], $rec[26], $rec[27], $rec[28], $rec[29], $rec[30], $rec[31], $rec[32], $rec[33], $rec[34]):0;		
	}

	# Filter base product Records (using in product Conversion) 
	function filterBaseProductRecords()
	{
		$qry = " select id, code, name from m_productmaster where base_product='Y' and semi_finished!='Y' order by name asc ";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Ing Records  (Ingredients and Semi Finished Products)
	function fetchAllIngredientRecords($selRateList)
	{
		//$qry = "select a.ingredient_id, b.name, d.id, d.name, c.id, c.name from m_ingredient_rate a, m_ingredient b, ing_category c left join ing_main_category d on b.main_category_id=d.id where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by d.name, c.name asc, b.name asc";
		$qry = "select a.ingredient_id, b.name, d.id, d.name, c.id, c.name from m_ingredient_rate a join m_ingredient b on a.ingredient_id=b.id join ing_category c on b.category_id=c.id left join ing_main_category d on b.main_category_id=d.id where a.rate_list_id='$selRateList' order by d.name, c.name asc, b.name asc";
		//echo $qry;
		//$qrySFinish = " select a.id, a.name from m_sf_product a order by a.name asc ";
		$qrySFinish = " select a.id, a.name, b.id, b.name, c.id, c.name from m_sf_product a, ing_main_category b, ing_category c where a.category_id=b.id and a.subcategory_id=c.id  order by b.name asc, c.name asc, a.name asc ";
		//echo $qrySFinish;		
		$result		= $this->databaseConnect->getRecords($qry);		
		$resultArr	= array();
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
			$resultArr [$i]      = array('',"----Semi-Finished Products----");		
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
					$resultArr [$i]      = array('',"-----$categoryName-------");	
					$i++;
				}
				$resultArr[$i] 		= array("SFP_$ingredientId",$ingName);
				$prevCategoryId 	= $categoryId;
				$preSubCategoryId 	= $subCategoryId;
			}
		}		
		return $resultArr;
	}

	/* (Original Ing recs)
	function fetchAllIngredientRecords($selRateList)
	{
		$qry = "select a.ingredient_id, b.name from m_ingredient_rate a, m_ingredient b, ing_category c where c.id=b.category_id and b.id=a.ingredient_id and a.rate_list_id='$selRateList' order by c.name asc, b.name asc";

		$qrySFinish = " select id, name from m_sf_product order by name asc ";

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

		

	# Get Finished Product (Return rate and declared yield)
	function getSemiFinishRate($semiFinishProductId)
	{		
		$qry = "select product_cost, yield_percent from m_sf_product where id='$semiFinishProductId' ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array(0,0);
	}

	# Checking the selected product is using as ingredient
	function chkProductUsedAsIng($productId)
	{
		$qry = " select id, product_id, ingredient_id from m_productmaster_entry where sel_ing_type='SFP' and ingredient_id in ($productId) group by sel_ing_type";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;			
	}

	function chkProductCodeExist($pCode, $cId)
	{
		$qry = " select id from m_productmaster where code='$pCode'";
		if ($cId!="") $qry .= " and id!='$cId' ";

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

function updateProductconfirm($productId)
	{
	$qry	= "update m_productmaster set active='1' where id=$productId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateProductReleaseconfirm($productId)
	{
		$qry	= "update m_productmaster set active='0' where id=$productId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}






}

?>