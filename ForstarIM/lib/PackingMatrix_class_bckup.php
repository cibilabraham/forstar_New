<?php
class PackingMatrix
{
	/****************************************************************
	This class deals with all the operations relating to Packing Matrix
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PackingMatrix(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addPackingMatrix($packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPackingCost, $productType, $innerContainerQty, $innerPackingQty, $innerSampleQty, $innerLabelingQty, $innerLeafletQty, $innerSealingQty, $pkgLabourRateQty, $userId)
	{
		$qry = "insert into t_packing_matrix (code, name, inner_container_id, inner_packing_id, inner_sample_id, inner_labeling_id, inner_leaflet_id, inner_sealing_id, pkg_labour_Rate_id, num_of_pack_mc, master_pkg_id, iner_continer_rate, inner_pkg_rate, inner_sample_rate, inner_labeling_rate, inner_leaflet_rate, inner_sealing_rate, pkg_labour_rate, inner_pkg_cost, master_pkg_rate, master_sealing_rate, outer_pkg_cost, product_type, inner_container_qty, inner_packing_qty, inner_sample_qty, inner_labeling_qty, inner_leaflet_qty, inner_sealing_qty, pkg_labour_qty, created, createdby) values('$packingCode', '$packingName', '$innerContainerId', '$innerPackingId', '$innerSampleId', '$innerLabelingId', '$innerLeafletId', '$innerSealingId', '$pkgLabourRateId', '$noOfPacksInMC', '$masterPackingId', '$innerContainerRate', '$innerPackingRate', '$innerSampleRate', '$innerLabelingRate', '$innerLeafletRate', '$innerSealingRate', '$pkgLabourRate', '$innerPkgCost', '$masterPackingRate', '$masterSealingRate', '$outerPackingCost', '$productType', '$innerContainerQty', '$innerPackingQty', '$innerSampleQty', '$innerLabelingQty', '$innerLeafletQty', '$innerSealingQty', '$pkgLabourRateQty', NOW(), $userId)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, code, name, inner_container_id, inner_packing_id, inner_sample_id, inner_labeling_id, inner_leaflet_id, inner_sealing_id, pkg_labour_Rate_id, num_of_pack_mc, master_pkg_id, iner_continer_rate, inner_pkg_rate, inner_sample_rate, inner_labeling_rate, inner_leaflet_rate, inner_sealing_rate, pkg_labour_rate, inner_pkg_cost, master_pkg_rate, master_sealing_rate, outer_pkg_cost from t_packing_matrix order by code asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select id, code, name, inner_container_id, inner_packing_id, inner_sample_id, inner_labeling_id, inner_leaflet_id, inner_sealing_id, pkg_labour_Rate_id, num_of_pack_mc, master_pkg_id, iner_continer_rate, inner_pkg_rate, inner_sample_rate, inner_labeling_rate, inner_leaflet_rate, inner_sealing_rate, pkg_labour_rate, inner_pkg_cost, master_pkg_rate, master_sealing_rate, outer_pkg_cost, product_type, inner_container_qty, inner_packing_qty, inner_sample_qty, inner_labeling_qty, inner_leaflet_qty, inner_sealing_qty, pkg_labour_qty from t_packing_matrix order by code asc";
		//echo $qry."<br>";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($pkgMatrixRecId)
	{
		$qry = "select id, code, name, inner_container_id, inner_packing_id, inner_sample_id, inner_labeling_id, inner_leaflet_id, inner_sealing_id, pkg_labour_Rate_id, num_of_pack_mc, master_pkg_id, iner_continer_rate, inner_pkg_rate, inner_sample_rate, inner_labeling_rate, inner_leaflet_rate, inner_sealing_rate, pkg_labour_rate, inner_pkg_cost, master_pkg_rate, master_sealing_rate, outer_pkg_cost, product_type, inner_container_qty, inner_packing_qty, inner_sample_qty, inner_labeling_qty, inner_leaflet_qty, inner_sealing_qty, pkg_labour_qty from t_packing_matrix where id=$pkgMatrixRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updatePackingMatrix($pkgMatrixRecId, $packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPackingCost, $productType, $innerContainerQty, $innerPackingQty, $innerSampleQty, $innerLabelingQty, $innerLeafletQty, $innerSealingQty, $pkgLabourRateQty, $userId)
	{
		$qry = "update t_packing_matrix set code='$packingCode', name='$packingName', inner_container_id='$innerContainerId', inner_packing_id='$innerPackingId', inner_sample_id='$innerSampleId', inner_labeling_id='$innerLabelingId', inner_leaflet_id='$innerLeafletId', inner_sealing_id='$innerSealingId', pkg_labour_Rate_id='$pkgLabourRateId', num_of_pack_mc='$noOfPacksInMC', master_pkg_id='$masterPackingId', iner_continer_rate='$innerContainerRate', inner_pkg_rate='$innerPackingRate', inner_sample_rate='$innerSampleRate', inner_labeling_rate='$innerLabelingRate', inner_leaflet_rate='$innerLeafletRate', inner_sealing_rate='$innerSealingRate', pkg_labour_rate='$pkgLabourRate', inner_pkg_cost='$innerPkgCost', master_pkg_rate='$masterPackingRate', master_sealing_rate='$masterSealingRate', outer_pkg_cost='$outerPackingCost', product_type='$productType', inner_container_qty='$innerContainerQty', inner_packing_qty='$innerPackingQty', inner_sample_qty='$innerSampleQty', inner_labeling_qty='$innerLabelingQty', inner_leaflet_qty='$innerLeafletQty', inner_sealing_qty='$innerSealingQty', pkg_labour_qty='$pkgLabourRateQty', modified=NOW(),modifiedby='$userId' where id=$pkgMatrixRecId ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deletePackingMatrixRec($pkgMatrixRecId)
	{
		$qry	= " delete from t_packing_matrix where id=$pkgMatrixRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Find Packing material Rate (TABLE: m_packing_material_cost)
	function findPackingMaterialRate($masterPackingId, $pmcRateList)
	{
		$qry = " select pu_cost, tot_cost from m_packing_material_cost where id='$masterPackingId' and rate_list_id='$pmcRateList'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[1]:0;
	}

	#Get packing Matrix values based on Id
	/*************************************
	$packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, 	$innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPackingCost
	**************************************/
	function getPackingMatrixRec($pkgMatrixRecId)
	{
		$rec = $this->find($pkgMatrixRecId);
		return (sizeof($rec)>0)?array($rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8], $rec[9], $rec[10], $rec[11], $rec[12], $rec[13], $rec[14], $rec[15], $rec[16], $rec[17], $rec[18], $rec[19], $rec[20], $rec[21], $rec[22]):0;
	}

	/*
	function getProductMasterRec()
	{
		$qry = "select product_gms_per_pouch, fish_gms_per_pouch, gravy_gms_per_pouch, product_rate_per_kg_per_btch, fish_rate_per_kg_per_btch, gravy_rate_per_kg_per_btch, pouch_per_btch, product_rate_per_btch, fish_rate_per_btch, gravy_rate_per_btch, product_kg_per_btch, fish_kg_per_btch, gravy_kg_per_btch, pduct_raw_pcent_per_pouch, fish_raw_pcent_per_pouch, gravy_raw_pcent_per_pouch, pduct_kg_pouch_per_btch, fish_kg_pouch_per_btch, gravy_kg_pouch_per_btch, fish_percent_yield, gravy_percent_yield, total_fixed_fish_qty from m_productmaster where id=$productId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	*/

	# get packing Rate
	function getInnerPackingRate($packingId, $pmcRateList)
	{
		$qry = "select pu_cost, tot_cost from m_packing_material_cost where id='$packingId' and rate_list_id='$pmcRateList'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[1]:0;
	}

	# get sealing cost
	function getInnerSealingCost($innerSealingId, $pscRateList)
	{
		$qry = "select cost from m_packing_sealing_cost where id='$innerSealingId' and rate_list_id='$pscRateList'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	# get labour cost
	function getPackingLabourCost($packingLabourId, $plcRateList)
	{
		$qry = "select cost from m_packing_labour_cost where id='$packingLabourId' and rate_list_id='$plcRateList'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	
}
?>