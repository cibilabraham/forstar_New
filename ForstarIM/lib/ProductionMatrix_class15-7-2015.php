<?php
class ProductionMatrix
{
	/****************************************************************
	This class deals with all the operations relating to Production Matrix
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductionMatrix(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addProductionMatrix($prodCode, $prodName, $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $boilerRequired, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch, $userId)
	{
		$qry = "insert into t_production_matrix (code, name, filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day, boiler_required, diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch, mktg_travel_cost, ad_cost_per_pouch, created, createdby) values('$prodCode', '$prodName', $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, '$boilerRequired', $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch, NOW(), '$userId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, code, name, filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day, boiler_required, diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch, mktg_travel_cost, ad_cost_per_pouch from t_production_matrix order by code asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	=	"select id, code, name, filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day, boiler_required, diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch, mktg_travel_cost, ad_cost_per_pouch from t_production_matrix order by code asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records With out Iterator (Using in Combo MX)
	function fetchAllProductionMatrixRecords()
	{
		$qry = "select id, code, name, filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day, boiler_required, diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch, mktg_travel_cost, ad_cost_per_pouch from t_production_matrix order by code asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get a Record based on id
	function find($productionMatrixRecId)
	{
		$qry = "select id, code, name, filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day, boiler_required, diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch, mktg_travel_cost, ad_cost_per_pouch from t_production_matrix where id=$productionMatrixRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateProductionMatrix($productionMatrixRecId, $prodCode, $prodName, $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $boilerRequired, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch, $userId)
	{
		$qry = "update t_production_matrix set code='$prodCode', name='$prodName', filling_wt_per_pouch=$fillingWtPerPouch, qty_per_btch=$prodQtyPerBtch, no_of_pouch=$noOfPouch, processed_wt_per_btch=$processedWtPerBtch, no_of_hrs_prep=$noOfHrsPrep, no_of_hrs_cook=$noOfHrsCook, no_of_hrs_filling=$noOfHrsFill, no_of_hrs_retort=$noOfHrsRetort, no_of_hrs_first_btch=$noOfHrsFirstBtch, no_of_hrs_other_btch=$noOfHrsOtherBtch, no_of_btchs_per_day=$noOfBtchsPerDay, boiler_required='$boilerRequired', diesel_cost_per_btch=$dieselCostPerBtch, electric_cost_per_btch=$electricityCostPerBtch, water_cost_per_btch=$waterCostPerBtch, gas_cost_per_btch=$gasCostPerBtch, tot_fuel_cost_per_btch=$totFuelCostPerBtch, maint_cost_per_btch=$maintCostPerBtch, vari_manpower_cost_per_btch=$variManPwerCostPerBtch, mktg_cost_per_btch=$mktgTeamCostPerPouch, mktg_travel_cost=$mktgTravelCost, ad_cost_per_pouch=$adCostPerPouch, modified=NOW(), modifiedby='$userId' where id=$productionMatrixRecId ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deleteProductionMatrixRec($productionMatrixRecId)
	{
		$qry	= " delete from t_production_matrix where id=$productionMatrixRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Get Production matrix Values
	/************************************
	$prodCode, $prodName, $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, 	$noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $boilerRequired, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, 	$gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch
	*************************************/
	function getProductionMatrixRec($productionMatrixRecId)
	{
		$rec = $this->find($productionMatrixRecId);
		return (sizeof($rec)>0)?array($rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7], $rec[8], $rec[9], $rec[10], $rec[11], $rec[12], $rec[13], $rec[14], $rec[15], $rec[16], $rec[17], $rec[18], $rec[19], $rec[20], $rec[21], $rec[22], $rec[23], $rec[24]):0;
	}
	
}
?>