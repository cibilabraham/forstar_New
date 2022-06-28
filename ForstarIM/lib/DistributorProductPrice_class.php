<?php
class DistributorProductPrice
{
	/****************************************************************
	This class deals with all the operations relating to Distributor Product Price
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DistributorProductPrice(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addDistProductPriceRec($selDistributor, $selProduct, $mrp, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost, $userId, $productPriceRateListId)
	{
		$qry = "insert into m_dist_product_price (distributor_id, product_id, product_mrp, cost_to_dist_or_stkist, actual_distn_cost, octroi, freight, insurance, vat_cst, excise, edu_cess, basic_cost, cost_margin, actual_profit_mgn, on_mrp, on_factory_cost, created, createdby, rate_list_id) values('$selDistributor', '$selProduct', '$mrp', '$costToDistOrStkist', '$actualDistnCost', '$octroi', '$freight', '$insurance', '$vatOrCst', '$excise', '$eduCess', '$basicCost', '$costMargin', '$actualProfitMgn', '$onMrp', '$onFactoryCost', Now(), '$userId', '$productPriceRateListId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add Distributor Product Price
	function addDistProductPriceEntry($lastId, $marginStructureId, $distMarginEntryId, $distProfitMargin)
	{
		$qry = "insert into m_dist_product_price_entry (dist_price_main_id, margin_structure_id, dist_margin_entry_id, dist_profit_mgn) values('$lastId', '$marginStructureId', '$distMarginEntryId', '$distProfitMargin')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select a.id, a.distributor_id, a.product_id, a.product_mrp, a.cost_to_dist_or_stkist, a.actual_profit_mgn, a.on_mrp, a.on_factory_cost, b.name, c.code from m_dist_product_price a, m_distributor b, t_combo_matrix c where c.id=a.product_id and b.id=a.distributor_id order by b.name asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.distributor_id, a.product_id, a.product_mrp, a.cost_to_dist_or_stkist, a.actual_profit_mgn, a.on_mrp, a.on_factory_cost, b.name, c.code from m_dist_product_price a, m_distributor b, t_combo_matrix c where c.id=a.product_id and b.id=a.distributor_id order by b.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}
	
	# Get a Record based on id
	function find($distProdPriceRecId)
	{
		$qry = "select id, distributor_id, product_id, product_mrp, cost_to_dist_or_stkist, actual_distn_cost, octroi, freight, insurance, vat_cst, excise, edu_cess, basic_cost, cost_margin, actual_profit_mgn, on_mrp, on_factory_cost from m_dist_product_price where id=$distProdPriceRecId";
		return $this->databaseConnect->getRecord($qry);
	}


	# Update  a  Record
	function updateDistProductPriceRec($distProdPriceRecId, $mrp, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost)
	{
		$qry = "update m_dist_product_price set product_mrp='$mrp', cost_to_dist_or_stkist='$costToDistOrStkist', actual_distn_cost='$actualDistnCost', octroi='$octroi', freight='$freight', insurance='$insurance', vat_cst='$vatOrCst', excise='$excise', edu_cess='$eduCess', basic_cost='$basicCost', cost_margin='$costMargin', actual_profit_mgn='$actualProfitMgn', on_mrp='$onMrp', on_factory_cost='$onFactoryCost' where id=$distProdPriceRecId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	
	#update dist product Price Entry Rec
	function  updateDistProdPriceEntryRec($distProductPriceEntryId, $distProfitMargin)
	{
		$qry = "update m_dist_product_price_entry set dist_profit_mgn='$distProfitMargin' where id=$distProductPriceEntryId ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	
	# Delete dist product price Entry Rec
	function delDistProdPriceEntryRec($distProdPriceRecId)
	{
		$qry = "delete from m_dist_product_price_entry where dist_price_main_id=$distProdPriceRecId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Record
	function deleteDistProductPriceRec($distProdPriceRecId)
	{
		$qry =	" delete from m_dist_product_price where id=$distProdPriceRecId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	#Checking same entry exist
	function checkEntryExist($selDistributor, $selProduct)
	{
		$qry = "select id from m_dist_product_price where distributor_id='$selDistributor' and product_id='$selProduct'";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}


	function filterProductRecords($distributorId, $selDistMarginRateList)
	{
		$qry = "select a.product_id, b.code, b.name from m_distributor_margin a, t_combo_matrix b where a.product_id=b.id and a.distributor_id='$distributorId' order by b.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# filter Structure entry Rec
	function filterStructureEntryRecs($selDistributor, $selProduct, $distMarginRateList)
	{
		$qry = "select b.id, b.margin_structure_id, b.percentage, c.name, c.price_calc, c.use_avg_dist, c.scheme_chk, c.scheme_struct_id from m_distributor_margin a, m_distributor_margin_entry b, m_margin_structure c where a.id=b.main_id and b.margin_structure_id=c.id and a.distributor_id='$selDistributor' and a.product_id='$selProduct' and a.rate_list_id='$distMarginRateList' order by c.id asc, c.use_avg_dist asc, c.name asc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Left Join With (EditMode=m_distributor_margin_entry,m_dist_product_price_entry d)
	function getDistMagnStructEntryRecs($selDistributor, $selProduct, $distMarginRateList)
	{
		$qry = "select b.id, b.margin_structure_id, b.percentage, c.name, c.price_calc, c.use_avg_dist, c.scheme_chk, c.scheme_struct_id, d.id, d.dist_profit_mgn from (m_distributor_margin a, m_distributor_margin_entry b) left join m_dist_product_price_entry d on d.dist_margin_entry_id=b.id, m_margin_structure c where a.id=b.main_id and b.margin_structure_id=c.id and a.distributor_id='$selDistributor' and a.product_id='$selProduct' and a.rate_list_id='$distMarginRateList' order by c.id asc, c.use_avg_dist asc, c.name asc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the dist Magn Struct Main Rec
	function getDistMgnStructRec($selDistributor, $selProduct, $selDistMarginRateList)
	{
		$qry = " select octroi, vat, freight from m_distributor_margin where distributor_id='$selDistributor' and product_id='$selProduct' and rate_list_id='$selDistMarginRateList'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2]):0;
	}

	# Find product Price Rate
	function getProductPriceRec($selProduct, $productPriceRateList)
	{
		$qry = "select baisc_manuf_cost, buffer, incl_buffer, profit_margin, factory_cost, avg_distributor_margin, mgn_for_scheme, num_packs_one_free, mrp, actual_profit_margin, on_mrp, on_factory_cost from m_product_price where product_id='$selProduct' and rate_list_id='$productPriceRateList'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[8], $rec[4], $rec[3]):0;
	}

	# Distributor rec
	function getDistributorRec($selDistributor)
	{
		$qry = " select  tax_type, billing_form_f from m_distributor where id='$selDistributor'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0], $rec[1]):"";
	}

	# find Product matrix Rec
	function getProductMatrixRec($selProduct)
	{
		$qry = " select excise_rate from t_combo_matrix where id='$selProduct'";	
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0]):"";
	}	
}
?>