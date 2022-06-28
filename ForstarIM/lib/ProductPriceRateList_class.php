<?php
class ProductPriceRateList
{
	/****************************************************************
	This class deals with all the operations relating to Product Price Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProductPriceRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#add a Record
	function addProductPriceRateList($rateListName, $startDate, $copyRateList, $productPriceCRateListId, $userId)
	{
		$qry = "insert into m_productprice_ratelist (name, start_date) values('".$rateListName."', '".$startDate."')";
		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Prev Rate List Rec END DATE
			if ($productPriceCRateListId!="") {
				$updateRateListEndDate = $this->updateProductPriceRateListRec($productPriceCRateListId, $startDate);
			}
		#--------------- Copy Functions ------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="") {
				$productPriceRecords = $this->fetchAllProductPriceRecords($copyRateList);
				foreach ($productPriceRecords as $ppr) {			
					$productPriceRecId 	= $ppr[0];
					$selProduct		= $ppr[1];
					$basicManufCost 	= $ppr[2];
					$selBuffer 		= $ppr[3];
					$inclBuffer 		= $ppr[4];
					$profitMargin 		= $ppr[5];
					$factoryCost 		= $ppr[6];
					$avgDistMgn 		= $ppr[7];
					$mgnForScheme 		= $ppr[8];
					$noOfPacksFree 		= $ppr[9];
					$mrp 			= $ppr[10];
					$actualProfitMargin	= $ppr[11];
					$onMRP 			= $ppr[12];
					$onFactoryCost 		= $ppr[13];				

					// Insert New Product Price rec	
					$productPriceInsertStatus = $this->addProductPriceRec($selProduct, $basicManufCost, $selBuffer, $inclBuffer, $profitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $mrp, $actualProfitMargin, $onMRP, $onFactoryCost, $insertedRateListId, $userId);	

					if ($productPriceInsertStatus) $productPriceInsLastId = $this->databaseConnect->getLastInsertedId();	
					# Get Dist wise Prod wise main Rec
					$distProdPriceMainRecs = $this->fetchDistProPriceMainRecs($productPriceRecId);
					foreach ($distProdPriceMainRecs as $dpm) {
						$distProdPriceMainRecId = $dpm[0];
						$selDistributor		= $dpm[1];
						$selProduct		= $dpm[2];
						$mrp			= $dpm[3];
						
						# Insert Main Rec
						$distProductPriceMainRecIns = $this->addDistProductPriceMainRec($selDistributor, $selProduct, $mrp, $userId, $insertedRateListId, $productPriceInsLastId);
						
						if ($distProductPriceMainRecIns) $disProdPriceMainInsLastId = $this->databaseConnect->getLastInsertedId();

						#fetch all records of Dist product price recs
						$distProductPriceStateWiseRecs = $this->fetchDistproductStateWisePriceRecs($distProdPriceMainRecId);

						foreach ($distProductPriceStateWiseRecs as $dpp) {
							$distProdStateWiseEntryId	= $dpp[0];
							$stateId		= $dpp[1];	
							$costToDistOrStkist	= $dpp[2];
							$actualDistnCost	= $dpp[3];
							$octroi			= $dpp[4];
							$freight		= $dpp[5];
							$insurance		= $dpp[6];
							$vatOrCst		= $dpp[7];
							$excise			= $dpp[8];
							$eduCess		= $dpp[9];
							$basicCost		= $dpp[10];
							$costMargin		= $dpp[11];
							$actualProfitMgn 	= $dpp[12];
							$onMrp			= $dpp[13];
							$onFactoryCost		= $dpp[14];
							# Insert Dist Product wise state Wise rec
							$distProdPriceStateWiseRecIns = $this->addDistProductPriceStateWiseRec($disProdPriceMainInsLastId, $stateId, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost);
			
							if ($distProdPriceStateWiseRecIns) $distProdPriceStateInsLastId = $this->databaseConnect->getLastInsertedId();
							#dis product Margin price Entry recs
							$distProductMarginpriceEntryRecs = $this->filterDistProdMarginPriceEntryRecs($distProdStateWiseEntryId);
							foreach ($distProductMarginpriceEntryRecs as $dpe) {
								$marginStructureId = $dpe[0];
								$distMarginEntryId = $dpe[1];
								$distProfitMargin  = $dpe[2];

								$distProductPriceEntryRecIns= $this->addDistProductMarginPriceEntry($distProdPriceStateInsLastId, $marginStructureId, $distMarginEntryId, $distProfitMargin);
							
							}					
						}						
					}					
				} // Main Loop Ends Here
			}
	#-------------------- Copy Functions End -------------------------------------		
		
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	function fetchAllPagingRecords($offset, $limit)
	{
		$orderBy = "start_date desc,name asc";
		$limit = "$offset, $limit";
		$qry = "select id, name, start_date,active,(select count(a1.id) from m_product_price a1 where rate_list_id=a.id) as tot from m_productprice_ratelist a";	
		if ($orderBy)	$qry .= " order by ".$orderBy;
		if ($limit)	$qry .= " limit ".$limit;	
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Recs
	function fetchAllRecords()
	{
		$qry = "select id, name, start_date,active,(select count(a1.id) from m_product_price a1 where rate_list_id=a.id)as tot from m_productprice_ratelist a order by start_date desc,name asc";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Rec based on id 	
	function find($rateListId)
	{
		$qry = "select id, name, start_date from m_productprice_ratelist where id=$rateListId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateProductPriceRateList($rateListName, $startDate, $rateListId)
	{
		$qry = " update m_productprice_ratelist set name='$rateListName', start_date='$startDate' where id=$rateListId";
 		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteProductPriceRateList($rateListId)
	{
		$qry = " delete from m_productprice_ratelist where id=$rateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			$latestRateListId = $this->latestRateList();
			# Update Prev Rate List Date
			$sDate = "0000-00-00";			
			$this->updatePrevRateListRec($latestRateListId, $sDate);
		}
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Checking Rate List Id used
	function checkRateListUse($rateListId)
	{
		$qry	= "select id from m_product_price where rate_list_id='$rateListId'";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Current Rate List
	function latestRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	=	"select a.id from m_productprice_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in other Screen
	function findRateList()
	{
		$cDate = date("Y-m-d");
		$qry	=	"select a.id,name,start_date from m_productprice_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$array			=	explode("-", $rec[2]);
			$startDate		=	$array[2]."/".$array[1]."/".$array[0];
			$displayRateList =  $rec[1]."&nbsp;(".$startDate.")";
		}
		return (sizeof($rec)>0)?$displayRateList:"";
	}

	#-------------------- Copy Functions Starts -------------------------

	#Fetch All Product Price Records
	function fetchAllProductPriceRecords($selRateList)
	{
		$qry = "select id, product_id, baisc_manuf_cost, buffer, incl_buffer, profit_margin, factory_cost, avg_distributor_margin, mgn_for_scheme, num_packs_one_free, mrp, actual_profit_margin, on_mrp, on_factory_cost, createdby from m_product_price where rate_list_id='$selRateList'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Insert Product Price
	function addProductPriceRec($selProduct, $basicManufCost, $selBuffer, $inclBuffer, $profitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $mrp, $actualProfitMargin, $onMRP, $onFactoryCost, $insertedRateListId, $userId)
	{
		$qry = "insert into m_product_price (product_id, baisc_manuf_cost, buffer, incl_buffer, profit_margin, factory_cost, avg_distributor_margin, mgn_for_scheme, num_packs_one_free, mrp, actual_profit_margin, on_mrp, on_factory_cost, rate_list_id, created, createdby) values('$selProduct', '$basicManufCost', '$selBuffer', '$inclBuffer', '$profitMargin', '$factoryCost', '$avgDistMgn', '$mgnForScheme', '$noOfPacksFree', '$mrp', '$actualProfitMargin', '$onMRP', '$onFactoryCost', '$insertedRateListId', Now() , '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# fetch all dist Product price main rec
	function fetchDistProPriceMainRecs($productPriceRecId)
	{
		$qry = " select id, distributor_id, product_id, product_mrp, created, createdby, rate_list_id from m_dist_product_price where product_price_entry_id='$productPriceRecId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# fetch all dist Product price main rec
	function fetchDistproductStateWisePriceRecs($productPriceRecId)
	{
		$qry = " select id, state_id, cost_to_dist_or_stkist, actual_distn_cost, octroi, freight, insurance, vat_cst, excise, edu_cess, basic_cost, cost_margin, actual_profit_mgn, on_mrp, on_factory_cost from m_dist_product_price_state where dist_price_main_id='$productPriceRecId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# filter dist product price entry Recs
	function filterDistProdMarginPriceEntryRecs($distProdStateWiseEntryId)
	{
		$qry = " select margin_structure_id, dist_margin_entry_id, dist_profit_mgn from m_dist_product_price_entry where dprice_state_entry_id='$distProdStateWiseEntryId'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;				
	}

	# Add Distributor Product Price Entry
	function addDistProductMarginPriceEntry($lastId, $marginStructureId, $distMarginEntryId, $distProfitMargin)
	{
		$qry = "insert into m_dist_product_price_entry (dprice_state_entry_id, margin_structure_id, dist_margin_entry_id, dist_profit_mgn) values('$lastId', '$marginStructureId', '$distMarginEntryId', '$distProfitMargin')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

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
	#------------------------ Copy Functions End ----------------------------------------

	# update Dist Rate List Rec
	function updateProductPriceRateListRec($productPriceCRateListId, $startDate)
	{
		$sDate		= explode("-",$startDate);
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_productprice_ratelist set end_date='$endDate' where id=$productPriceCRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# update Prev Rate List of current dist Rec
	function updatePrevRateListRec($productPriceCRateListId, $sDate)
	{		
		$qry = " update m_productprice_ratelist set end_date='$endDate' where id=$productPriceCRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	function updateProductPriceRateListconfirm($productPriceRecId)
	{
	$qry	= "update m_productprice_ratelist set active='1' where id=$productPriceRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateProductPriceRateListReleaseconfirm($productPriceRecId)
	{
		$qry	= "update m_productprice_ratelist set active='0' where id=$productPriceRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>