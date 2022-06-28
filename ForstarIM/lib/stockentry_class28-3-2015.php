<?php
class Stock
{
	/****************************************************************
	This class deals with all the operations relating to Stock
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Stock(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Stock
	function addStock($code, $name, $selCategory, $selSubCategory, $reOrder, $descr, $stockType, $brand, $unit, $quantity, $size, $dimension, $weight, $color, $made, $layer, $carton, $packingBrand, $packingColor, $packingWeight, $packing, $numColors, $packingDimension, $cartonWeight, $active, $userId, $selFrozenCode, $packingKg, $reorderRequired, $basicUnitQty, $minOrderUnit, $minOrderQtyPerUnit, $brandType, $modelNo, $dimensionLength,  $dimensionBreadth, $dimensionHeight, $dimensionDiameter, $dimensionRadius, $particularsDescription, $additionalHoldingPercent, $stockingPeriod,$tolerancelevel,$plantunit)
	{
		//echo "$stockType";
		# Packing section
		if ($stockType=='P') {
			/*$qry	= "insert into m_stock (code, name, category_id, subcategory_id, reorder, description, quantity, stock_type, layer, carton, packing_brand, packing_color, packing_weight, packing, num_colors, packing_dimension, carton_weight, active, created, createdby, actual_quantity,  packing_kg, additional_holding_percent, stocking_period, reorder_required) values('$code', '$name', '$selCategory', '$selSubCategory', '$reOrder', '$descr', '$quantity', '$stockType', '$layer', '$carton', '$packingBrand', '$packingColor', '$packingWeight', '$packing','$numColors', '$packingDimension', '$cartonWeight', '$active', Now(), '$userId', '$quantity', '$packingKg', '$additionalHoldingPercent', '$stockingPeriod', '$reorderRequired')";*/
			//$additionalHoldingPercent=0;
			$qry="insert into m_stock (code, name, category_id, subcategory_id, reorder, description, quantity, stock_type, layer, carton, packing_brand, packing_color, packing,packing_dimension, carton_weight, active, created, createdby, actual_quantity, packing_kg, additional_holding_percent, stocking_period, reorder_required,tolerance_level,plant_unit) values('$code', '$name', '$selCategory', '$selSubCategory', '$reOrder', '$descr', '$quantity', '$stockType', '$layer', '$carton', '$packingBrand', '$packingColor', '$packing','$packingDimension', '$cartonWeight', '$active', Now(), '$userId', '$quantity', '$packingKg', '$additionalHoldingPercent', '$stockingPeriod', '$reorderRequired','$tolerancelevel','$plantunit')";
		} else { # Ordinary section	
			$qry	= "insert into m_stock (code, name, category_id, subcategory_id, reorder, description, stock_type, brand, unit, quantity, size, dimension, weight, color, made, active, created, createdby, actual_quantity, reorder_required, basic_unit_qty, min_order_unit, min_order_qty_per_unit, brand_type, model_no, dim_length, dim_breadth, dime_height, dim_diameter, dim_radius, particulars_description, additional_holding_percent, stocking_period,plant_unit) values('$code', '$name', '$selCategory', '$selSubCategory', '$reOrder', '$descr', '$stockType', '$brand', '$unit', '$quantity', '$size', '$dimension', '$weight', '$color', '$made', '$active', Now(), '$userId', '$quantity', '$reorderRequired', '$basicUnitQty', '$minOrderUnit', '$minOrderQtyPerUnit', '$brandType', '$modelNo', '$dimensionLength',  '$dimensionBreadth', '$dimensionHeight', '$dimensionDiameter', '$dimensionRadius', '$particularsDescription', '$additionalHoldingPercent', '$stockingPeriod','$plantunit')";
		}
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		//$stklastId = $this->databaseConnect->getLastInsertedId();
		$lastId = $this->databaseConnect->getLastInsertedId();
		//$this->getStkLastIdVal($stklastId);
		
		if ($insertStatus) {
			//echo "hai";
			$this->databaseConnect->commit();
			//$this->addStockQuantity($lastId, $quantity,$quantity,$plantunit);
			if ($selFrozenCode!="") {
				//echo "hello";
				//$lastId = $this->databaseConnect->getLastInsertedId();
				$this->addFrozenCode2Stock($lastId, $selFrozenCode);
				
			}
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
		//return $stklastId;
	}


function getMaxstkId()
	{
		
		$qry = "select max(id) maxid from m_stock";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Returns all Paging Stock
	function fetchAllPagingRecords($offset, $limit, $categoryFilterId, $subCategoryFilterId)
	{		
		$whr = " a.category_id=b.id and a.subcategory_id=c.id";

		if ($categoryFilterId!="") $whr .= " and a.category_id=".$categoryFilterId;
		if ($subCategoryFilterId!="") $whr .= " and a.subcategory_id=".$subCategoryFilterId;

		$orderBy 	= " a.name asc";
		$limit 		= " $offset,$limit";


		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period,a.activeconfirm,((select count(a1.id) from supplier_stock a1 where a1.stock_id=a.id)+(select count(a2.id) from stockissuance_entries a2 where a2.stock_id=a.id)+(select count(a3.id) from purchaseorder_entry a3 where a3.stock_id=a.id)+(select count(a4.id) from goods_receipt_entries a4 where a4.stock_id=a.id)+(select count(a5.id) from stock_return_entry a5 where a5.stock_id=a.id)) as tot,plant_unit from m_stock a, stock_category b, stock_subcategory c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


function fetchAllStockRecords($categoryFilterId, $subCategoryFilterId)
	{
$whr = " a.category_id=b.id and a.subcategory_id=c.id and a.activeconfirm=1";

		if ($categoryFilterId!="") $whr .= " and a.category_id=".$categoryFilterId;
		if ($subCategoryFilterId!="") $whr .= " and a.subcategory_id=".$subCategoryFilterId;

		$orderBy 	= " a.name asc";
		$limit 		= " $offset,$limit";

		//$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c";

		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period,a.activeconfirm,((select count(a1.id) from supplier_stock a1 where a1.stock_id=a.id)+(select count(a2.id) from stockissuance_entries a2 where a2.stock_id=a.id)+(select count(a3.id) from purchaseorder_entry a3 where a3.stock_id=a.id)+(select count(a4.id) from goods_receipt_entries a4 where a4.stock_id=a.id)+(select count(a5.id) from stock_return_entry a5 where a5.stock_id=a.id)) as tot from m_stock a, stock_category b, stock_subcategory c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//if ($limit!="") $qry .= " limit ".$limit;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;

	}
	function fetchAllPagingRecordsSearch($offset, $limit, $categoryFilterId, $subCategoryFilterId,$stockName)
	{		
		$whr = " a.category_id=b.id and a.subcategory_id=c.id and a.name='$stockName'";

		if ($categoryFilterId!="") $whr .= " and a.category_id=".$categoryFilterId;
		if ($subCategoryFilterId!="") $whr .= " and a.subcategory_id=".$subCategoryFilterId;

		$orderBy 	= " a.name asc";
		$limit 		= " $offset,$limit";

		//$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c";

		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period,a.activeconfirm,((select count(a1.id) from supplier_stock a1 where a1.stock_id=a.id)+(select count(a2.id) from stockissuance_entries a2 where a2.stock_id=a.id)+(select count(a3.id) from purchaseorder_entry a3 where a3.stock_id=a.id)+(select count(a4.id) from goods_receipt_entries a4 where a4.stock_id=a.id)+(select count(a5.id) from stock_return_entry a5 where a5.stock_id=a.id)) as tot,a.plant_unit from m_stock a, stock_category b, stock_subcategory c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Returns all filter Stock
	function fetchAllFilterRecords($categoryFilterId, $subCategoryFilterId)
	{
		$whr = " a.category_id=b.id and a.subcategory_id=c.id";

		if ($categoryFilterId!="") $whr .= " and a.category_id=".$categoryFilterId;
		if ($subCategoryFilterId!="") $whr .= " and a.subcategory_id=".$subCategoryFilterId;

		$orderBy 	= " a.name asc";
		
		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c";

		if ($whr!="")		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllFilterRecordsSearch($categoryFilterId, $subCategoryFilterId,$stockName)
	{
		$whr = " a.category_id=b.id and a.subcategory_id=c.id and a.name='$stockName'";

		if ($categoryFilterId!="") $whr .= " and a.category_id=".$categoryFilterId;
		if ($subCategoryFilterId!="") $whr .= " and a.subcategory_id=".$subCategoryFilterId;

		$orderBy 	= " a.name asc";
		
		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c";

		if ($whr!="")		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Returns all Stock
	function fetchAllRecords()
	{
		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRecordsConfirm()
	{
		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.activeconfirm=1 and b.active=1 and c.active=1 order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


function fetchAllActiveplantUnitRecords($plantUnit)
{
	//$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.activeconfirm=1 and b.active=1 and c.active=1 and a.plant_unit='$plantUnit' order by a.name asc";
//$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a where a.activeconfirm=1 and a.plant_unit='$plantUnit' order by a.name asc";

$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, a.actual_quantity, a.additional_holding_percent, a.stocking_period from m_stock a where a.activeconfirm=1 order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;

}
	# Returns all Active Stock
	function fetchAllActiveRecords()
	{
		$qry	= " select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.active='Y' order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

function fetchAllActiveRecordsConfirm()
	{
		$qry	= " select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.active='Y' and a.activeconfirm=1 and b.active=1 and c.active=1 order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Stock based on id 
	function find($stockId)
	{
		$qry = "select id, code, name, category_id, subcategory_id, quantity, unit, reorder, description, stock_type, brand, size, dimension, weight, color, made, layer, carton, packing_brand, packing_color, packing_weight, packing, num_colors, packing_dimension, carton_weight, active, packing_kg, reorder_required, basic_unit_qty, min_order_unit, min_order_qty_per_unit, brand_type, model_no, dim_length, dim_breadth, dime_height, dim_diameter, dim_radius, particulars_description, additional_holding_percent, stocking_period,tolerance_level,plant_unit from m_stock where id=$stockId";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Stock
	function deleteStock($stockId)
	{
		$qry	= " delete from m_stock where id=$stockId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Stock
	function updateStock($stockId, $code, $name, $selCategory, $selSubCategory, $reOrder, $descr, $stockType, $brand, $unit, $quantity, $size, $dimension, $weight, $color, $made, $layer, $carton, $packingBrand, $packingColor, $packingWeight, $packing, $numColors, $packingDimension, $cartonWeight, $active, $oldStockQuantity, $selFrozenCode, $packingKg, $reorderRequired, $basicUnitQty, $minOrderUnit, $minOrderQtyPerUnit, $brandType, $modelNo, $dimensionLength,  $dimensionBreadth, $dimensionHeight, $dimensionDiameter, $dimensionRadius, $particularsDescription, $additionalHoldingPercent, $stockingPeriod,$tolerancelevel)
	{
		//Update the actual Qty
		$updateField = "";
		if ($quantity!=$oldStockQuantity) {
			$actualQty = $quantity-$oldStockQuantity;
			if ($actualQty>0) $updateField = ", actual_quantity=actual_quantity+$actualQty";
			else $updateField = ", actual_quantity=actual_quantity-'".abs($actualQty)."'";
		}

		if ($stockType=='P') {
			//$qry = " update m_stock set code='$code', name='$name', category_id='$selCategory', subcategory_id='$selSubCategory', reorder='$reOrder', description='$descr', quantity='$quantity', stock_type='$stockType', layer='$layer', carton='$carton', packing_brand='$packingBrand', packing_color='$packingColor', packing_weight='$packingWeight', packing='$packing', num_colors='$numColors', packing_dimension='$packingDimension', carton_weight='$cartonWeight', active='$active', packing_kg='$packingKg', additional_holding_percent='$additionalHoldingPercent', stocking_period='$stockingPeriod', reorder_required='$reorderRequired',tolerance_level='$tolerancelevel' $updateField where id=$stockId ";

			$qry = " update m_stock set code='$code', name='$name', category_id='$selCategory', subcategory_id='$selSubCategory', reorder='$reOrder', description='$descr', quantity='$quantity', stock_type='$stockType', layer='$layer', carton='$carton', packing_brand='$packingBrand', packing_color='$packingColor',packing='$packing',carton_weight='$cartonWeight', active='$active',additional_holding_percent='$additionalHoldingPercent', stocking_period='$stockingPeriod', reorder_required='$reorderRequired',tolerance_level='$tolerancelevel' $updateField where id=$stockId ";

		} else {   // Ordinary Section
			$qry = " update m_stock set code='$code', name='$name', category_id='$selCategory', subcategory_id='$selSubCategory', reorder='$reOrder', description='$descr', stock_type='$stockType', brand='$brand',unit='$unit',quantity='$quantity', size='$size', dimension='$dimension', weight='$weight', color='$color', made='$made', active='$active', reorder_required='$reorderRequired', basic_unit_qty='$basicUnitQty', min_order_unit='$minOrderUnit', min_order_qty_per_unit='$minOrderQtyPerUnit', brand_type='$brandType', model_no='$modelNo', dim_length='$dimensionLength', dim_breadth='$dimensionBreadth', dime_height='$dimensionHeight', dim_diameter='$dimensionDiameter', dim_radius='$dimensionRadius', particulars_description='$particularsDescription', additional_holding_percent='$additionalHoldingPercent', stocking_period='$stockingPeriod' $updateField where id=$stockId ";
		}
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {

			$this->deleteStock2FrozenCode($stockId);
			if ($selFrozenCode!="") {
				$this->addFrozenCode2Stock($stockId, $selFrozenCode);
			}
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}

	#<!-- FrozenCode2Stock	section Start Here -->
	#Frozen Code adding in Stock (Suitable For)
	function  addFrozenCode2Stock($lastId, $selFrozenCode)
	{
		//echo "entered";
		if ($selFrozenCode) {
			$selFrozenCodeStr=explode(",",$selFrozenCode);
			

			foreach ($selFrozenCodeStr as $fId) {
				//$frozenCodeId = "$fId";
				$selFrozenCodeStrVal=explode("-",$fId);
				$frozenCodeId=$selFrozenCodeStrVal[0];
				$quickfrozenCodeId=$selFrozenCodeStrVal[1];
				$qry = "insert into m_stock2frozencode (stock_id, frozencode_id,quickid) values('".$lastId."','".$frozenCodeId."','".$quickfrozenCodeId."')";
				//echo $qry;
				$insertGrade	=	$this->databaseConnect->insertRecord($qry);
				if ($insertGrade) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();				
			}
		}
	}

	function addStockQuantity($lastId, $quantity,$quantity,$plantunit)
	{
		$qry = "insert into m_stock_plantunit(stock_id,plant_unit,actual_quantity,openingquantity) values('".$lastId."','".$plantunit."','".$quantity."','".$quantity."')";
				//echo $qry;
				$insertGrade	=	$this->databaseConnect->insertRecord($qry);
				if ($insertGrade) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();	

	}
	#fOR SELECTING THE SELECTED Frozen Code
	function fetchSelectedFrozenCodeRecords($editId)
	{
		$qry 	= "select a.id, a.code, b.id, b.stock_id, b.frozencode_id from m_frozenpacking a left join m_stock2frozencode b on a.id=b.frozencode_id and b.stock_id='$editId' order by b.id desc, a.code asc";
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Delete the exisiting Frozen Code at the time of Update
	function deleteStock2FrozenCode($stockId)
	{
		$qry	= " delete from m_stock2frozencode where stock_id='$stockId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

function deletepackingWeight($stockId)
	{
		$qry	= " delete from m_stock_packing_weight where stock_id='$stockId'";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deletepackingWeightrow($Id)
	{
		$qry	= " delete from m_stock_packing_weight where id='$Id'";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

function getPackingWeightRecs($stockId){
$qry = " select a.id, a.packing_weight,a.mcpacking_id,a.stock_id from m_stock_packing_weight a where a.stock_id=$stockId"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;

}

function getplantUnitRecs($stockId){
$qry = " select a.id, a.plant_unit,a.actual_quantity from m_stock_plantunit a where a.stock_id=$stockId"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;

}


function getCopyStock($stockId){
$qry = " select a.id, a.packing_weight,a.mcpacking_id,a.stock_id from m_stock_packing_weight a where a.stock_id=$stockId"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;

}

	#<!-- FrozenCode2Stock section end Here -->

	#Filter Unit from Sub Category
	function filterUnitRecs($selSubCategoryId)
	{
		$qry = " select b.id, b.name from stock_subcategory a, m_stock_unit b where a.unitgroup_id=b.unitgroup_id and a.id=$selSubCategoryId order by b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Checking Record Used in (supplier_stock, stockissuance_entries, purchaseorder_entry, goods_receipt_entries, stock_return_entry
	function checkRecordUsed($stockId)
	{
		//$qry	= "select id from supplier_stock where stock_id='$stockId'";
		$qry = " select id from (
				select a.id as id from supplier_stock a where a.stock_id='$stockId'
			union
				select a1.id as id from stockissuance_entries a1 where a1.stock_id='$stockId'
			union
				select a2.id as id from purchaseorder_entry a2 where a2.stock_id='$stockId'
			union
				select a3.id as id from goods_receipt_entries a3 where a3.stock_id='$stockId'
			union
				select a4.id as id from stock_return_entry a4 where a4.stock_id='$stockId'
		) as X group by id ";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	/*
	# Stock Item Price Variation
	Price Variation = Yearly Average Price - Latest Stock Price
	*/
	function getStockItemPriceVariation($stockId)
	{
		$currentStockPrice 	= $this->getLastestPrice($stockId);
		$yearlyAveragePrice 	= $this->getYearlyAveragePrice($stockId);
		//echo "$currentStockPrice-$yearlyAveragePrice<br>";
		return ($yearlyAveragePrice-$currentStockPrice);
	}

	# Get the Stock Latest Price
	function getLastestPrice($stockId)
	{
		$qry =  " select d.unit_price from goods_receipt_entries a, goods_receipt b, m_purchaseorder c, purchaseorder_entry d where a.goods_receipt_id=b.id and b.po_id=c.id and c.id=d.po_id and a.stock_id=d.stock_id and a.stock_id=$stockId order by b.created desc";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getYearlyAveragePrice($stockId)
	{
		$cDate = date("Y-m-d");
		$beforeOneYear = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")-1));
		$qry = " select sum(d.unit_price)/count(*) from goods_receipt_entries a, goods_receipt b, m_purchaseorder c, purchaseorder_entry d where a.goods_receipt_id=b.id and b.po_id=c.id and c.id=d.po_id and a.stock_id=d.stock_id and a.stock_id=$stockId and b.created>='$beforeOneYear' and b.created<='$cDate' group by a.stock_id";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;			
	}
	/**********************************/

	# Bulk Stock Rec Update
	function updateStockRec($stockId, $holdingPercent, $stockingPeriod)
	{
		$qry = " update m_stock set additional_holding_percent='$holdingPercent', stocking_period='$stockingPeriod' where id='$stockId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	/*
	# Get units based on Sub Category Id 
	# Using in import stock Xajax
	*/
	function getAssocUnitRecs($subcatId)
	{
		$resultArr = array();
		$qry = " select b.id, b.name from stock_subcategory a, m_stock_unit b where a.unitgroup_id=b.unitgroup_id and a.id=$subcatId order by b.name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		if( sizeof( $result ) > 0 ) $resultArr = array(''=>'-- Select --');
		else $resultArr = array(''=>'-- No Units Found. --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	// Import Ordinary Stock (bulk Entry)
	function importOrdinaryStock($sCode, $sName, $sDesc, $sCatry, $sSubCatry, $sRePoint, $reOrderRequired, $qtyInStock, $sAddHoldPernt, $sStkingPeriod, $sActive, $sUnit, $basicUnitQty, $minOrderUnit, $minOrderQtyPerUnit, $sBrand, $type, $modelNo, $sSize, $dimensionLength, $dimensionBreadth, $dimensionHeight, $dimensionDiameter, $dimensionRadius, $sWeight, $sColor, $sMadeOf, $particularsDescription, $sType, $userId)
	{
		$qry	= "insert into m_stock (code, name, description, category_id, subcategory_id, reorder, reorder_required, quantity, additional_holding_percent, stocking_period, active, unit, basic_unit_qty, min_order_unit, min_order_qty_per_unit, brand, brand_type, model_no, size, dim_length, dim_breadth, dime_height, dim_diameter, dim_radius, weight, color, made, particulars_description, stock_type, createdby, created, actual_quantity, import) values('$sCode', '$sName', '$sDesc', '$sCatry', '$sSubCatry', '$sRePoint', '$reOrderRequired', '$qtyInStock', '$sAddHoldPernt', '$sStkingPeriod', '$sActive', '$sUnit', '$basicUnitQty', '$minOrderUnit', '$minOrderQtyPerUnit', '$sBrand', '$type', '$modelNo', '$sSize', '$dimensionLength', '$dimensionBreadth', '$dimensionHeight', '$dimensionDiameter', '$dimensionRadius', '$sWeight', '$sColor', '$sMadeOf', '$particularsDescription', '$sType', '$userId', NOW(), '$qtyInStock', 'Y')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();
		return $insertStatus;
	}

	// Import Packing Stock (bulk Entry) with multiple frozen records
	function importPackingStock($sCode, $sName, $sDesc, $sCatry, $sSubCatry, $sRePoint, $reOrderRequired, $qtyInStock, $sAddHoldPernt, $sStkingPeriod, $sActive, $numLayer, $typeOfCarton, $packingBrand, $packingColor, $packingWeight, $packingKg, $packingNos, $selFrozenCode, $numColors, $packingDimension, $cartonWeight, $sType, $userId)
	{
		$qry	= "insert into m_stock (code, name, description, category_id, subcategory_id, reorder, reorder_required, quantity, additional_holding_percent, stocking_period, active, layer, carton, packing_brand, packing_color, packing_weight, packing_kg, packing, num_colors, packing_dimension, carton_weight, stock_type, createdby, created, actual_quantity, import) values('$sCode', '$sName', '$sDesc', '$sCatry', '$sSubCatry', '$sRePoint', '$reOrderRequired', '$qtyInStock', '$sAddHoldPernt', '$sStkingPeriod', '$sActive', '$numLayer', '$typeOfCarton', '$packingBrand', '$packingColor', '$packingWeight', '$packingKg', '$packingNos', '$numColors', '$packingDimension', '$cartonWeight', '$sType', '$userId', NOW(), '$qtyInStock', 'Y')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
				
		if ($insertStatus) {
			$this->databaseConnect->commit();
			if ($selFrozenCode!="") {
				$lastId = $this->databaseConnect->getLastInsertedId();
				$this->addFrozenCode2Stock($lastId, $selFrozenCode);
			}
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Stk Dynamic Rec Ins
	function addStkGroupField($stkId, $stkGroupEntryId, $stkFieldValue, $stkUnitId)
	{
		$qry	= "insert into m_stock_stkg_entry (stock_main_id, stk_group_entry_id, field_value, unit_id) values('$stkId', '$stkGroupEntryId', '$stkFieldValue', '$stkUnitId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get Stock Group Recs stock_stkg_entry
	function getStkGroupRecs($stockId, $stkGroupEntryId)
	{
		$qry = " select id, field_value, unit_id from m_stock_stkg_entry where stock_main_id='$stockId' and stk_group_entry_id='$stkGroupEntryId' ";
		//$qry = " select id, field_value, unit_id from m_stock_stkg_entry where id='$stockId' and stk_group_entry_id='$stkGroupEntryId' ";
	//echo "<br>$qry";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):"";
	}
	# Update Stk Group Field
	function updateStkGroupField($mStkGroupEntyId, $stkFieldValue, $stkUnitId)
	{
		$qry = " update m_stock_stkg_entry set field_value='$stkFieldValue', unit_id='$stkUnitId' where id='$mStkGroupEntyId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# delete Stock Rec Fiel
	function deleteStkGroupField($mStkGroupEntyId)
	{
		$qry	= " delete from m_stock_stkg_entry where id='$mStkGroupEntyId' ";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete All Stock GRoup Entry Recs Based on Stock Id
	function deleteStockGroupEntryRecs($stockId)
	{	
		$qry	= " delete from m_stock_stkg_entry where stock_main_id='$stockId' ";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	# Generate Stock code
	/*function getStockCode($string)
	{
		$exArr = explode(" ",$string);
		//printr($exArr);
		
		if (sizeof($exArr)>1) {
			$strArr = array();
			for($i=0; $i<sizeof($exArr); $i++) {		
				$strArr[$i] = substr(str_replace (array(" ","-","/","\"","\\"),'',$exArr[$i]),0,3);
			}
			$str = implode("-",$strArr);
		} else {
			$str = substr(str_replace (array(" ","-","/","\"","\\"),'',$exArr[0]),0,3)."-".generateRandomString(3,str_replace (array(" ","-","/"),'',strtoupper($exArr[0])));
		}

		return strtoupper($str);
	}*/


function getCountStockCode()
	{
$qry = "select count(*) no from m_stock";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";

	}
	
	function getStockCode()
	{
$qry = "select max(code) from m_stock";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";

	}
	


	// Category Name
	function getCategoryIdByName($categoryName)
	{
		$qry = " select id from stock_category where name='$categoryName' ";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";
	}


	function getSubCategoryIdByName($subcategoryName)
	{
		$qry = " select id from stock_subcategory where name='$subcategoryName' ";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";
	}

	function checkStockExist($stockName)
	{
		$qry = " select id from m_stock where name='$stockName' ";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result))?true:false;
	}


	function importStockFromCSV($categoryId, $subCategoryId, $staticFieldArr, $stockType, $userId)
	{
		$statement = implode(",",$staticFieldArr);
		$statement .=  ", category_id=$categoryId, subcategory_id=$subCategoryId, stock_type='$stockType', import='Y', createdby='$userId', created='".date("Y-m-d")."' ";

		$qry	= "insert into m_stock SET $statement";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function importStkDynamicField($stkId, $dynamicFieldArr)
	{
		//$statement = implode(",",$dynamicFieldArr);
		foreach ($dynamicFieldArr as $k=>$statement) {
			$statement .=  ", stock_main_id='$stkId' ";

			$qry	= "insert into m_stock_stkg_entry SET $statement ";
			//echo $qry;
			$insertStatus	= $this->databaseConnect->insertRecord($qry);		
			if ($insertStatus) $this->databaseConnect->commit();			
			else $this->databaseConnect->rollback();		
		}
		return $insertStatus;
	}

	function fetchAlldeclaredWt(){
	$qry = " select distinct decl_wt from m_frozenpacking";
	//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;


	}

function addPackingWeightStockEntries($packingweight,$mcpacking_id,$stockid){
$qry	= "insert into m_stock_packing_weight(packing_weight,mcpacking_id,stock_id) values ('$packingweight','$mcpacking_id','$stockid')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $insertStatus;

}


function updatePackingWeightStockEntries($packingweight,$mcpacking_id,$stockid,$packingId){
$qry	= "update m_stock_packing_weight set packing_weight='$packingweight',mcpacking_id='$mcpacking_id',stock_id='$stockid' where id='$packingId'";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $insertStatus;

}

function getFrozencode($stockid){

	$qry="select a.frozencode_id,code,quickid,tfqe.name from t_fznpakng_quick_entry tfqe left join m_stock2frozencode a on tfqe.id=a.quickid left join m_frozenpacking a1 on a.frozencode_id=a1.id where a.stock_id='$stockid'";

	//$qry="select mfp.id,tfqe.name from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on  tfqe.frozencode_id=mfp.id left join m_stock2frozencode ms on mfp.id=ms.frozencode_id where expiry_date is null and ms.stock_id='$stockid'";
	//echo $qry;
	$result	= $this->databaseConnect->getRecords($qry);
		return $result;

}

function getFrozenCodeWtquickEntry($packingWtArr,$mcpackingIdArr)
	{
	//$packingWt1=explode(":",$packingWt);
	//$mcpackingId1=explode(":",$mcpackingId);
	$i=0;
	//$mcpackingIdVal[]=explode("-",$mcpackingId);
	//echo "hai";
	//echo "hello";
	//print_r($mcpackingIdArr);
	//print_r($packingWtArr);
	
	$totcount=count($packingWtArr);
	//echo $totcount;
	//foreach($packingWt as $pw)
	$whr="";
	for($i=0;$i<$totcount;$i++)
		{
		$or="or";
		$last=$totcount-1;
		if ($i!=$last)
		{
		$or="or"." ";
		}
		else
		{
		$or="";
		}
		$whr.= "(tfqe.mcpacking_id='$mcpackingIdArr[$i]' and mfp.decl_wt='$packingWtArr[$i]')"." $or";
		//$i++;
}
//$qry="select mfp.id,tfqe.name,tfqe.id,mfp.code,mfp.decl_wt,tfqe.mcpacking_id from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on  tfqe.frozencode_id=mfp.id where ";
$qry="select mfp.id,tfqe.name,tfqe.id from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on  tfqe.frozencode_id=mfp.id where (";
if ($whr!="") 		$qry 	.= "".$whr.") and expiry_date is null";
//$qry=trim($qry,'||');
//echo $qry;
$result	= $this->databaseConnect->getRecords($qry);
if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]."-".$v[2]] = $v[1];
		}		
		return $resultArr;
		
	}


	function getFrozenCodeWtquickEntryEdit($packingWtArr)
	{
	//$packingWt1=explode(":",$packingWt);
	//$mcpackingId1=explode(":",$mcpackingId);
	$i=0;
	//$mcpackingIdVal[]=explode("-",$mcpackingId);
	//echo "hai";
	//echo "hello";
	//print_r($mcpackingIdArr);
	//print_r($packingWtArr);
	
	$totcount=count($packingWtArr);
	//echo $totcount;
	//foreach($packingWt as $pw)
	$whr="";
	//for($i=0;$i<$totcount;$i++)
	foreach($packingWtArr as $pw)
		{
		$or="or";
		$last=$totcount-1;
		if ($i!=$last)
		{
		$or="or"." ";
		}
		else
		{
		$or="";
		}
		$whr.= "(tfqe.mcpacking_id='$pw[2]' and mfp.decl_wt='$pw[1]')"." $or";
		$i++;
}
//$qry="select mfp.id,tfqe.name,tfqe.id,mfp.code,mfp.decl_wt,tfqe.mcpacking_id from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on  tfqe.frozencode_id=mfp.id where ";
$qry="select mfp.id,tfqe.name,tfqe.id from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on  tfqe.frozencode_id=mfp.id where (";
if ($whr!="") 		$qry 	.= "".$whr.") and expiry_date is null";
//$qry=trim($qry,'||');
//echo $qry;
$result	= $this->databaseConnect->getRecords($qry);
/*if (sizeof($result)>1 || !sizeof($result)) $resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}		
		return $resultArr;*/
		return $result;
		
	}
	
	function updateStockconfirm($stockId){
		$qry	= "update m_stock set activeconfirm='1' where id=$stockId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateStockReleaseconfirm($stockId){
	$qry	= "update m_stock set activeconfirm='0' where id=$stockId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function filterRecords($plantunitid)
	{
		//$qry	= "select a.id, a.category_id, a.name, a.description, b.name from stock_subcategory a, stock_category b where a.category_id=b.id and a.category_id='$categoryId' order by a.name asc";

		//$qry	= "select m.id,name from m_stock m where m.plant_unit='$plantunitid'";
		$qry	= "select m.id,name from m_stock m where activeconfirm=1";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	

function updateStockFromUnit($stockIdFrom,$qty,$unitFrom)
{
//$qry	= "update m_stock set actual_quantity=actual_quantity-$qty where id=$stockIdFrom";
$qry	= "update m_stock_plantunit set actual_quantity=actual_quantity-$qty where stock_id=$stockIdFrom and plant_unit='$unitFrom'";
//echo $qry;
$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

function findFromunit($stockId)
	{
		$qry = "select id,name from m_stock where id=$stockId";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecord($qry);
	}

function getStockId($stockNameFrom,$unitTo)
	{
$qry = "select id from m_stock where name='$stockNameFrom' and plant_unit='$unitTo'";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecord($qry);

	}

	function updateStockToUnit($stockIdFrom,$qty,$unitTo)
	{
//$qry	= "update m_stock set actual_quantity=actual_quantity+$qty where id=$stockIdFrom";
$qry	= "update m_stock_plantunit set actual_quantity=actual_quantity+$qty where stock_id=$stockIdFrom and plant_unit='$unitTo'";
//echo $qry;
$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

function checkStockExistUnit($stockIdFrom,$qty,$unitTo)
	{

$qry="select *from  m_stock_plantunit where stock_id='$stockIdFrom' and plant_unit='$unitTo'";
//echo $qry;

$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}


function getStockqty($stockIdFrom,$qty,$unitTo)
	{

$qry="select *from  m_stock_plantunit where stock_id='$stockIdFrom' and plant_unit='$unitTo'";
//echo $qry;

$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function fetchAllStockTransfer()
	{
$qry="select *from stock_transfer";

	//$qry="select mfp.id,tfqe.name from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on  tfqe.frozencode_id=mfp.id left join m_stock2frozencode ms on mfp.id=ms.frozencode_id where expiry_date is null and ms.stock_id='$stockid'";
	//echo $qry;
	$result	= $this->databaseConnect->getRecords($qry);
		return $result;

	}

	function addStockTransfer($unitFrom,$unitTo,$item,$quantity,$date)
	{
	$qry="insert into stock_transfer(fromunit,tounit,item,quantity,date) values ('$unitFrom','$unitTo','$item','$quantity','$date')";
	$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $insertStatus;

	}
function getPlantList($stockId)
	{
$qry="select *from m_stock_plantunit a left join m_plant b on a.plant_unit=b.id  where stock_id='$stockId'";

	//$qry="select mfp.id,tfqe.name from t_fznpakng_quick_entry tfqe left join m_frozenpacking mfp on  tfqe.frozencode_id=mfp.id left join m_stock2frozencode ms on mfp.id=ms.frozencode_id where expiry_date is null and ms.stock_id='$stockid'";
	//echo $qry;
	$result	= $this->databaseConnect->getRecords($qry);
		return $result;

	}

	function addUnitStock($unitId,$stockQty,$stkIdVal)
	{
	$qry="insert into m_stock_plantunit(stock_id,plant_unit,actual_quantity,openingquantity) values ('$stkIdVal','$unitId','$stockQty','$stockQty')";
	//echo $qry;
	$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $insertStatus;

	}
	function updateUnitStock($punitId,$stkQty,$stockqtyid){
	$qry	= "update m_stock_plantunit set actual_quantity='$stkQty',openingquantity='$stkQty',plant_unit='$punitId' where id='$stockqtyid'";
//echo $qry;
$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function deleteUnitStock($Id)
	{
		$qry	= " delete from m_stock_plantunit where id=$Id";
		//echo $qry;

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
function fetchAllRecordsStockActive()
	{
		$qry = "select a.id, a.code, a.name, a.quantity, a.unit, a.reorder, a.active, b.name, c.name, a.actual_quantity, a.additional_holding_percent, a.stocking_period,a.activeconfirm from m_stock a, stock_category b, stock_subcategory c where a.category_id=b.id and a.subcategory_id=c.id and a.activeconfirm=1 order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
}

?>