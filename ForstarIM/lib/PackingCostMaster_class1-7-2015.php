<?php
class PackingCostMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Packing Cost Master
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PackingCostMaster(&$databaseConnect)
    	{	
        	$this->databaseConnect =&$databaseConnect;
	}


	#Add
	function addPackingCostMaster($vatRateForPackingMaterial, $innerCartonWstage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc, $plcRateList, $pscRateList, $pmcRateList, $selRateList)
	{
		$qry = "insert into m_packing_cost (vat_rate, inner_carton_wstage, cost_of_gum, num_of_mc_per_tape_roll, cost_of_tape_roll, tape_cost_per_mc, plc_rate_list_id, psc_rate_list_id, pmc_rate_list_id, rate_list_id) values('$vatRateForPackingMaterial', '$innerCartonWstage', '$costOfGum', '$noOfMcsPerTapeRoll', '$costOfTapeRoll', '$tapeCostPerMc', $plcRateList, $pscRateList, $pmcRateList, $selRateList)";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get Record
	function find($selRateList)
	{
		$qry = "select id, vat_rate, inner_carton_wstage, cost_of_gum, num_of_mc_per_tape_roll, cost_of_tape_roll, tape_cost_per_mc, plc_rate_list_id, psc_rate_list_id, pmc_rate_list_id, rate_list_id from m_packing_cost where rate_list_id='$selRateList' and  id is not null ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updatePackingCostMaster($packingCostMasterId, $vatRateForPackingMaterial, $innerCartonWstage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc, $plcRateList, $pscRateList, $pmcRateList, $selRateList)
	{
		$qry =	" update m_packing_cost set vat_rate='$vatRateForPackingMaterial', inner_carton_wstage='$innerCartonWstage', cost_of_gum='$costOfGum', num_of_mc_per_tape_roll='$noOfMcsPerTapeRoll', cost_of_tape_roll='$costOfTapeRoll', tape_cost_per_mc='$tapeCostPerMc', plc_rate_list_id='$plcRateList', psc_rate_list_id='$pscRateList', pmc_rate_list_id='$pmcRateList', rate_list_id='$selRateList' where id=$packingCostMasterId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Update labour Cost
	function updatePackingLabourCostRec($packingLabourCostRecId, $costPerItem)
	{
		$qry = "update m_packing_labour_cost set cost='$costPerItem' where id=$packingLabourCostRecId ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	# Update  Sealing Cost
	function updatePackingSealingCostRec($packingSealingCostRecId, $costPerItem)
	{
		$qry = "update m_packing_sealing_cost set cost='$costPerItem' where id=$packingSealingCostRecId ";
		
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;	
	}

	# Update Packing Material Cost
	function updatePackingMaterialCostRec($packingMaterialCostRecId, $costPerItem, $totMaterialCost)
	{
		$qry = "update m_packing_material_cost set pu_cost='$costPerItem', tot_cost='$totMaterialCost' where id=$packingMaterialCostRecId ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	
	#Get all master Setting Value (using in Other Screen)
	function getPackingCostMasterValue($selRateList)
	{
		$rec = $this->find($selRateList);
		return 	(sizeof($rec)>0)?array($rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6]):0;		
	}

	# Delete Product Matrix Master Rec
	function deletePackingCostMasterRec($packingCostMasterId)
	{
		$qry = " delete from m_packing_cost where id=$packingCostMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# get packing Rate
	function getInnerPackingRate($pmcRateList)
	{
		$qry = "select id, pu_cost, tot_cost from m_packing_material_cost where rate_list_id='$pmcRateList'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[2]):0;
	}

	# get sealing cost
	function getInnerSealingCost($pscRateList)
	{
		$qry = "select id, cost from m_packing_sealing_cost where rate_list_id='$pscRateList'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):0;
	}

	# get labour cost
	function getPackingLabourCost($plcRateList)
	{
		$qry = "select id, cost from m_packing_labour_cost where rate_list_id='$plcRateList'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):0;
	}

	# Update  a  Record
	function updatePackingMatrix($pkgMatrixRecId, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPackingCost, $innerContainerQty, $innerPackingQty, $innerSampleQty, $innerLabelingQty, $innerLeafletQty, $innerSealingQty, $pkgLabourRateQty, $userId)
	{
		$qry = " update t_packing_matrix set inner_container_id='$innerContainerId', inner_packing_id='$innerPackingId', inner_sample_id='$innerSampleId', inner_labeling_id='$innerLabelingId', inner_leaflet_id='$innerLeafletId', inner_sealing_id='$innerSealingId', pkg_labour_Rate_id='$pkgLabourRateId', master_pkg_id='$masterPackingId', iner_continer_rate='$innerContainerRate', inner_pkg_rate='$innerPackingRate', inner_sample_rate='$innerSampleRate', inner_labeling_rate='$innerLabelingRate', inner_leaflet_rate='$innerLeafletRate', inner_sealing_rate='$innerSealingRate', pkg_labour_rate='$pkgLabourRate', inner_pkg_cost='$innerPkgCost', master_pkg_rate='$masterPackingRate', master_sealing_rate='$masterSealingRate', outer_pkg_cost='$outerPackingCost', inner_container_qty='$innerContainerQty', inner_packing_qty='$innerPackingQty', inner_sample_qty='$innerSampleQty', inner_labeling_qty='$innerLabelingQty', inner_leaflet_qty='$innerLeafletQty', inner_sealing_qty='$innerSealingQty', pkg_labour_qty='$pkgLabourRateQty', modified=NOW(), modifiedby='$userId' where id=$pkgMatrixRecId ";		
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

}
?>