<?php
class ProductPricing
{
	/****************************************************************
	This class deals with all the operations relating to Product Pricing (MRP)
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductPricing(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addProductPrice($selProduct, $basicManufCost, $selBuffer, $inclBuffer, $profitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $mrp, $actualProfitMargin, $onMRP, $onFactoryCost, $productPriceRateList, $userId)
	{
		$qry = "insert into m_product_price (product_id, baisc_manuf_cost, buffer, incl_buffer, profit_margin, factory_cost, avg_distributor_margin, mgn_for_scheme, num_packs_one_free, mrp, actual_profit_margin, on_mrp, on_factory_cost, rate_list_id, created, createdby) values('$selProduct', '$basicManufCost', '$selBuffer', '$inclBuffer', '$profitMargin', '$factoryCost', '$avgDistMgn', '$mgnForScheme', '$noOfPacksFree', '$mrp', '$actualProfitMargin', '$onMRP', '$onFactoryCost', '$productPriceRateList', Now(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	
	# Returns all Paging Records
	function fetchAllPagingRecords($selRateList, $offset, $limit)
	{
		$qry = "select a.id, a.product_id, a.baisc_manuf_cost, a.buffer, a.incl_buffer, a.profit_margin, a.factory_cost, a.avg_distributor_margin, a.mgn_for_scheme, a.num_packs_one_free, a.mrp, a.actual_profit_margin, a.on_mrp, a.on_factory_cost, b.code from m_product_price a, t_combo_matrix b where b.id=a.product_id and a.rate_list_id='$selRateList' order by b.code asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry = "select a.id, a.product_id, a.baisc_manuf_cost, a.buffer, a.incl_buffer, a.profit_margin, a.factory_cost, a.avg_distributor_margin, a.mgn_for_scheme, a.num_packs_one_free, a.mrp, a.actual_profit_margin, a.on_mrp, a.on_factory_cost, b.code from m_product_price a, t_combo_matrix b where b.id=a.product_id and a.rate_list_id='$selRateList' order by b.code asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

		
	# Get a Record based on id
	function find($productPriceId)
	{
		$qry = "select id, product_id, baisc_manuf_cost, buffer, incl_buffer, profit_margin, factory_cost, avg_distributor_margin, mgn_for_scheme, num_packs_one_free, mrp, actual_profit_margin, on_mrp, on_factory_cost, rate_list_id from m_product_price where id=$productPriceId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateProductPrice($productPriceMasterId, $basicManufCost, $selBuffer, $inclBuffer, $profitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $mrp, $actualProfitMargin, $onMRP, $onFactoryCost, $productPriceRateList)
	{
		$qry = "update m_product_price set baisc_manuf_cost='$basicManufCost', buffer='$selBuffer', incl_buffer='$inclBuffer', profit_margin='$profitMargin', factory_cost='$factoryCost', avg_distributor_margin='$avgDistMgn', mgn_for_scheme='$mgnForScheme', num_packs_one_free='$noOfPacksFree', mrp='$mrp', actual_profit_margin='$actualProfitMargin', on_mrp='$onMRP', on_factory_cost='$onFactoryCost', rate_list_id='$productPriceRateList' where id=$productPriceMasterId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
		
	# Delete a Record
	function deleteProductPrice($productPriceId)
	{
		$qry =	" delete from m_product_price where id=$productPriceId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	#Checking same entry exist
	function checkEntryExist($selProduct, $productPriceRateList)
	{
		$qry = "select id from m_product_price where product_id='$selProduct' and rate_list_id='$productPriceRateList'";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}



	#get Product Matrix values
	function getProductMatrixRec($productMatrixId)
	{
		$qry = "select basic_manufact_cost, contingency, profit_margin, ideal_factory_cost from t_combo_matrix where id='$productMatrixId'";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2], $rec[3]):0;
	}

	#get Avg dist Margin
	function getAvgDistMargin()
	{
		$qry = " select (sum(avg_margin)/count(*)) from m_distributor_margin";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	# Distributor wise product wise records
	function  fetchAllDistributorRecs($selProduct)
	{
		$qry = " select a.id, a.distributor_id, b.name, c.id, c.state_id, c.avg_margin, c.transport_cost, c.octroi, c.vat, c.freight from m_distributor_margin a, m_distributor b, m_distributor_margin_state c, m_distmargin_ratelist d where a.rate_list_id=d.id and a.id=c.distributor_margin_id and a.distributor_id=b.id and a.product_id='$selProduct' and (d.end_date is null || d.end_date='0000-00-00') order by b.name asc";		
		
		/* Edited becuase of Distributor wise rate List
			$qry = " select a.id, a.distributor_id, b.name, c.id, c.state_id, c.avg_margin, c.transport_cost, c.octroi, c.vat, c.freight from m_distributor_margin a, m_distributor b, m_distributor_margin_state c where a.id=distributor_margin_id and a.distributor_id=b.id and a.product_id='$selProduct' and a.rate_list_id='$selRateListId' order by b.name asc";		
		*/
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter Structure entry Rec 
	function filterStructureEntryRecs($distMarginStateEntryId)
	{
		$qry = "select b.id, b.margin_structure_id, b.percentage, c.name, c.price_calc, c.use_avg_dist, c.scheme_chk, c.scheme_struct_id from m_distributor_margin_entry b, m_margin_structure c where b.margin_structure_id=c.id and b.dist_state_entry_id='$distMarginStateEntryId' order by c.id asc, c.use_avg_dist asc, c.name asc";
		//$qry = "select b.id, b.margin_structure_id, b.percentage, c.name, c.price_calc, c.use_avg_dist, c.scheme_chk, c.scheme_struct_id from m_distributor_margin a, m_distributor_margin_entry b, m_margin_structure c where a.id=b.main_id and b.margin_structure_id=c.id and a.distributor_id='$selDistributor' and a.product_id='$selProduct' and a.rate_list_id='$distMarginRateList' order by c.id asc, c.use_avg_dist asc, c.name asc";
		//echo $qry."<br>";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the dist Magn Struct Main Rec
	/*
	function getDistMgnStructRec($selDistributor, $selProduct, $selDistMarginRateList)
	{
		$qry = " select octroi, vat, freight from m_distributor_margin where distributor_id='$selDistributor' and product_id='$selProduct' and rate_list_id='$selDistMarginRateList'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2]):0;
	}
	*/

	# Distributor rec
	function getDistributorRec($selDistributor, $selStateId)
	{
		$qry = " select  tax_type, billing_form from m_distributor_state where distributor_id='$selDistributor' and state_id='$selStateId' ";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1]):"";
	}

	# find Product matrix Rec
	function getProductExciseRate($selProduct)
	{
		$qry = " select excise_rate from t_combo_matrix where id='$selProduct'";	
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0]):"";
	}

	/********************************************/
	function addDistProductPriceMainRec($selDistributor, $selProduct, $mrp, $userId, $productPriceRateListId, $productPriceInsLastId)
	{
		$qry = "insert into m_dist_product_price (distributor_id, product_id, product_mrp, created, createdby, rate_list_id, product_price_entry_id) values('$selDistributor', '$selProduct', '$mrp', Now(), '$userId', '$productPriceRateListId', '$productPriceInsLastId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add Dist State wise Product Price
	function addDistProductPriceStateWiseRec($proPriceInsLastId, $stateId, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost)
	{
		$qry = "insert into m_dist_product_price_state (dist_price_main_id, state_id, cost_to_dist_or_stkist, actual_distn_cost, octroi, freight, insurance, vat_cst, excise, edu_cess, basic_cost, cost_margin, actual_profit_mgn, on_mrp, on_factory_cost) values('$proPriceInsLastId', '$stateId', '$costToDistOrStkist', '$actualDistnCost', '$octroi', '$freight', '$insurance', '$vatOrCst', '$excise', '$eduCess', '$basicCost', '$costMargin', '$actualProfitMgn', '$onMrp', '$onFactoryCost')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add Distributor Product Price
	function addDistProductPriceEntry($lastId, $marginStructureId, $distMarginEntryId, $distProfitMargin)
	{
		$qry = "insert into m_dist_product_price_entry (dprice_state_entry_id, margin_structure_id, dist_margin_entry_id, dist_profit_mgn) values('$lastId', '$marginStructureId', '$distMarginEntryId', '$distProfitMargin')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Delete all records of corre Id from m_dist_product_price
	function deleteDistProductPriceEntryRec($productPriceMasterId)
	{
		# Get records from m_dist_product_price
		$getDistProPriceMainRecs = $this->fetchAllDistProductPriceMainRec($productPriceMasterId);
		foreach ($getDistProPriceMainRecs as $gpp) {
			$distProPriceEntryMainId	= $gpp[0];
			# Get state Entry Rec (m_dist_product_price_state)
			$getDistProStateWiseEntryRecs = $this->ftchAllDistStateWiseRec($distProPriceEntryMainId);
			foreach ($getDistProStateWiseEntryRecs as $dps) {
				$dPriceStateEntryId = $dps[0];
				# Delete Margin Entry Rec
				$this->deleteDistProdPriceMarginEntry($dPriceStateEntryId);
				# Delete State wise Entry Rec
				$this->deleteDistProdPriceStateWiseEntry($dPriceStateEntryId);
			}
			
		}
		# Delete Dist Pro Price Main Rec
		$this->deleteDistProductPriceMainRec($productPriceMasterId);
	
		return true;
	}

	# fetch all dist product price rec based on Product Price master Id
	function fetchAllDistProductPriceMainRec($productPriceMasterId)
	{
		$qry = " select id from m_dist_product_price where product_price_entry_id='$productPriceMasterId'";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get state Entry Rec (m_dist_product_price_state)
	function ftchAllDistStateWiseRec($distProPriceEntryMainId)
	{
		$qry = " select id from m_dist_product_price_state where dist_price_main_id='$distProPriceEntryMainId'";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete Dist Product Margin Entry
	function deleteDistProdPriceMarginEntry($dPriceStateEntryId)
	{
		$qry =	" delete from m_dist_product_price_entry where dprice_state_entry_id=$dPriceStateEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Dist Product State wise Entry
	function deleteDistProdPriceStateWiseEntry($dPriceStateEntryId)
	{
		$qry =	" delete from m_dist_product_price_state where id=$dPriceStateEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a DistProductPrice Record
	function deleteDistProductPriceMainRec($productPriceMasterId)
	{
		$qry =	" delete from m_dist_product_price where product_price_entry_id=$productPriceMasterId";
		//echo $qry;
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	/********************************************/
}
?>