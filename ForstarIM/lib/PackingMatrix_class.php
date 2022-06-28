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
	function addPackingMatrix($packingType, $idealNetwt, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $shrinkGroup, $dispenserShrink, $noOfPacksInMC, $masterPackingId, $grossMC, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $dispenserPkg, $dispenserSeal, $masterPackingRate, $masterSealingRate, $masterLoading, $outerPackingCost, $labourCostOnly, $userId, $currentDate)
	{
		$qry = "insert into m_packing_matrix (packing_type, ideal_net_wt, inner_container, inner_packing, inner_sample, inner_label, inner_leaflet, inner_seal, pkg_labour_rate, shrink_group, dispenser_shrink_pkg, no_of_packs_mc, master_packing, gross_mc, inner_container_value, inner_carton_value, inner_sample_value, inner_label_value, inner_leaflet_value, inner_seal_value, labour_cost, inner_packing_cost, dispenser_pkg, dispenser_seal, master_packing_value, master_seal_value, master_load, outer_packing_cost, labour_cost_only, created_by, created_on, active) values('$packingType', '$idealNetwt', '$innerContainerId', '$innerPackingId', '$innerSampleId', '$innerLabelingId', '$innerLeafletId', '$innerSealingId', '$pkgLabourRateId', '$shrinkGroup', '$dispenserShrink', '$noOfPacksInMC', '$masterPackingId', '$grossMC', '$innerContainerRate', '$innerPackingRate', '$innerSampleRate', '$innerLabelingRate', '$innerLeafletRate', '$innerSealingRate', '$pkgLabourRate', '$innerPkgCost', '$dispenserPkg', '$dispenserSeal', '$masterPackingRate', '$masterSealingRate', '$masterLoading', '$outerPackingCost', '$labourCostOnly', '$userId', '$currentDate', 0)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, packing_type, inner_packing_cost, outer_packing_cost, labour_cost_only, active from m_packing_matrix order by packing_type asc limit $offset,$limit";
		//echo $qry;
		$fetchResult = $this->databaseConnect->getRecords($qry);
		return $fetchResult;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select * from m_packing_matrix order by packing_type asc";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($pkgMatrixRecId)
	{
		$qry = "select * from m_packing_matrix where id=$pkgMatrixRecId";
		$editResult = $this->databaseConnect->getRecord($qry);
		return $editResult;
	}

	# Update  a  Record
	function updatePackingMatrix($pkgMatrixRecId, $packingType, $idealNetwt, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $shrinkGroup, $dispenserShrink, $noOfPacksInMC, $masterPackingId, $grossMC, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $dispenserPkg, $dispenserSeal, $masterPackingRate, $masterSealingRate, $masterLoading, $outerPackingCost, $labourCostOnly, $userId)
	{
		$qry = "update m_packing_matrix set packing_type='$packingType', ideal_net_wt='$idealNetwt', inner_container='$innerContainerId', inner_packing='$innerPackingId', inner_sample='$innerSampleId', inner_label='$innerLabelingId', inner_leaflet='$innerLeafletId', inner_seal='$innerSealingId', pkg_labour_rate='$pkgLabourRateId', shrink_group='$shrinkGroup', dispenser_shrink_pkg='$dispenserShrink', no_of_packs_mc='$noOfPacksInMC', master_packing='$masterPackingId', gross_mc='$grossMC', inner_container_value='$innerContainerRate', inner_carton_value='$innerPackingRate', inner_sample_value='$innerSampleRate', inner_label_value='$innerLabelingRate', inner_leaflet_value='$innerLeafletRate', inner_seal_value='$innerSealingRate', labour_cost='$pkgLabourRate', inner_packing_cost='$innerPkgCost', dispenser_pkg='$dispenserPkg', dispenser_seal='$dispenserSeal', master_packing_value='$masterPackingRate', master_seal_value='$masterSealingRate', master_load='$masterLoading', outer_packing_cost='$outerPackingCost', labour_cost_only='$labourCostOnly', created_by='$userId' where id=$pkgMatrixRecId";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deletePackingMatrixRec($pkgMatrixRecId)
	{
		$qry	= " delete from m_packing_matrix where id=$pkgMatrixRecId";

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
	
	function findInnerContainerRate($stockId)
	{
		$qry = "select ROUND(rate,3) from m_packing_material where stock_id='$stockId'";
		$innerContainerRate = $this->databaseConnect->getRecord($qry);
		return $innerContainerRate;
	}
	
	function getPackingCost()
	{
		$qry = "select rate_per_glass_bottle, rate_per_bottle, rate_per_retort_pouch, sealing_cost_glass_bottles, sealing_cost, sealing_cost_pet_bottles, loading_charges from m_packing_cost";
		$packingCost = $this->databaseConnect->getRecord($qry);
		return $packingCost;
	}
	
	function getCostPerDispenser()
	{
		$qry = "select shrink_film from m_packing_cost";
		//echo $qry;
		$costperDispenser = $this->databaseConnect->getRecord($qry);
		return $costperDispenser;
	}
	
	function getTapeCost()
	{
		$qry = "select tape_cost_per_mc from m_packing_cost";
		$tapeCost = $this->databaseConnect->getRecord($qry);
		return $tapeCost;
	}
	
	function getLoadingCharge()
	{
		$qry = "select loading_charges from m_packing_cost";
		$loadingCharge = $this->databaseConnect->getRecord($qry);
		return $loadingCharge;
	}
	
	function getTempIncrease()
	{
		$qry = "select temp_increase_factor from m_packing_cost";
		$tempIncrease = $this->databaseConnect->getRecord($qry);
		return $tempIncrease;
	}
	
	//Confirm Packing Matrix
	function confirmPackingMatrix($confirmId)
	{
		$qry = "update m_packing_matrix set active=1 where id=$confirmId";
		$confirmResult	=	$this->databaseConnect->updateRecord($qry);
		if ($confirmResult) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $confirmResult;
	}

	//Release Confirmation of Packing Matrix
	function releaseConfirmation($relConfirmId)
	{
		$qry = "update m_packing_matrix set active=0 where id=$relConfirmId";
		$releaseResult	=	$this->databaseConnect->updateRecord($qry);
		if ($releaseResult) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $releaseResult;
	}
	
	//Get the Packing Name
	function getPackingName($netWgt)
	{
		$qry = "select id, packing_type from m_packing_matrix where ideal_net_wt like '$netWgt%'";
		//echo $qry;
		$result = array();
		$result=$this->databaseConnect->getRecords($qry);
		
		if (sizeof($result)>=1) $resultArr1 = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr1 = array();
		else $resultArr1 = array(''=>'-- Select --');
		
		while (list(,$v) = each($result)) 
		{
			$resultArr1[$v[0]] = $v[1];
		}
		
		$qry2 = "select id, packing_type from m_packing_matrix where ideal_net_wt not like '$netWgt%'";
		$result2 = array();
		$result2 = $this->databaseConnect->getRecords($qry2);
		$resultArr1[0]="-- Others --";
		while (list(,$v) = each($result2)) {
			$resultArr1[$v[0]] = $v[1];
		}
		
		//print_r($resultArr1);
		return $resultArr1;
	}
	
	#Get Inner and Outer Packing Cost of a particular Packing Matrix
	function getInnerOuterPackingCost($packingId)
	{
		$qry = "select id, inner_packing_cost, outer_packing_cost from m_packing_matrix where id='$packingId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
}
?>