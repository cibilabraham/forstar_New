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
	function addProductionMatrix($prodName,$processType,$kettles,$hrsForCooking,$fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook,$noOfHrsFilling	, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch,$gasRequired,$boilerRequired, $electRequired,$boilerRequiredProcessing, $noOfBtchsPerDay,  $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch,$coordinationCostPerPouch, $mktgTravelCost, $adCostPerPouch,$facilityCostPerDay, $userId)
	{
		$qry = "insert into m_production_matrix ( name,process_type,kettles, hrs_cooking,filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling,no_of_hrs_fill, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day,gas_required,boiler_required,elect_required,boiler_required_processing, diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch, coordination_cost,mktg_travel_cost, ad_cost_per_pouch,facility_cost, createdon, createdby) values('$prodName','$processType','$kettles','$hrsForCooking','$fillingWtPerPouch','$prodQtyPerBtch','$noOfPouch', '$processedWtPerBtch','$noOfHrsPrep','$noOfHrsCook','$noOfHrsFilling','$noOfHrsFill','$noOfHrsRetort', '$noOfHrsFirstBtch','$noOfHrsOtherBtch','$noOfBtchsPerDay','$gasRequired','$boilerRequired', '$electRequired','$boilerRequiredProcessing','$dieselCostPerBtch','$electricityCostPerBtch', '$waterCostPerBtch','$gasCostPerBtch','$totFuelCostPerBtch','$maintCostPerBtch','$variManPwerCostPerBtch', '$mktgTeamCostPerPouch','$coordinationCostPerPouch','$mktgTravelCost', '$adCostPerPouch','$facilityCostPerDay', NOW(), '$userId')";
		//echo $qry;
		//die();
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select a.id,a.name,b.name as process_type,a.kettles, a.hrs_cooking,a.filling_wt_per_pouch,a.qty_per_btch,a.no_of_pouch, a.processed_wt_per_btch,a.no_of_hrs_prep,a.no_of_hrs_cook, a.no_of_hrs_filling,a.no_of_hrs_fill,a.no_of_hrs_retort,a.no_of_hrs_first_btch,a.no_of_hrs_other_btch, a.no_of_btchs_per_day,a.gas_required,a.boiler_required,a.elect_required,a.boiler_required_processing, a.diesel_cost_per_btch,a.electric_cost_per_btch,a.water_cost_per_btch,a.gas_cost_per_btch, a.tot_fuel_cost_per_btch,a.maint_cost_per_btch,a.vari_manpower_cost_per_btch, a.mktg_cost_per_btch,a.coordination_cost,a.mktg_travel_cost,a.ad_cost_per_pouch,a.facility_cost from m_production_matrix a left join m_process_master b on a.process_type=b.id order by name asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	="select a.id,a.name,b.name as process_type,a.kettles, a.hrs_cooking,a.filling_wt_per_pouch,a.qty_per_btch,a.no_of_pouch, a.processed_wt_per_btch,a.no_of_hrs_prep,a.no_of_hrs_cook, a.no_of_hrs_filling,a.no_of_hrs_fill,a.no_of_hrs_retort,a.no_of_hrs_first_btch,a.no_of_hrs_other_btch, a.no_of_btchs_per_day,a.gas_required,a.boiler_required,a.elect_required,a.boiler_required_processing, a.diesel_cost_per_btch,a.electric_cost_per_btch,a.water_cost_per_btch,a.gas_cost_per_btch, a.tot_fuel_cost_per_btch,a.maint_cost_per_btch,a.vari_manpower_cost_per_btch, a.mktg_cost_per_btch,a.coordination_cost,a.mktg_travel_cost,a.ad_cost_per_pouch,a.facility_cost from m_production_matrix a left join m_process_master b on a.process_type=b.id order by name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records With out Iterator (Using in Combo MX)
	function fetchAllProductionMatrixRecords()
	{
		$qry = "select id,name,process_type,kettles, hrs_cooking,filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling,no_of_hrs_fill, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day,gas_required,boiler_required,elect_required,	boiler_required_processing,diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch,	coordination_cost, mktg_travel_cost, ad_cost_per_pouch,facility_cost from m_production_matrix order by name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($productionMatrixRecId)
	{
		$qry = "select id,name,process_type,kettles, hrs_cooking,filling_wt_per_pouch, qty_per_btch, no_of_pouch, processed_wt_per_btch, no_of_hrs_prep, no_of_hrs_cook, no_of_hrs_filling,no_of_hrs_fill, no_of_hrs_retort, no_of_hrs_first_btch, no_of_hrs_other_btch, no_of_btchs_per_day,gas_required,boiler_required,elect_required,	boiler_required_processing,diesel_cost_per_btch, electric_cost_per_btch, water_cost_per_btch, gas_cost_per_btch, tot_fuel_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch, mktg_cost_per_btch,	coordination_cost, mktg_travel_cost, ad_cost_per_pouch,facility_cost from m_production_matrix where id=$productionMatrixRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateProductionMatrix($productionMatrixRecId, $prodName,$processType,$kettles,$hrsForCooking,$fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook,$noOfHrsFilling	,$noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch,$gasRequired,$boilerRequired, $electRequired,$boilerRequiredProcessing,$noOfBtchsPerDay,$dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch,$coordinationCostPerPouch,$mktgTravelCost, $adCostPerPouch,$facilityCostPerDay)
	{
		$qry = "update m_production_matrix set name='$prodName',process_type='$processType', kettles='$kettles', hrs_cooking='$hrsForCooking',filling_wt_per_pouch='$fillingWtPerPouch', qty_per_btch='$prodQtyPerBtch', no_of_pouch='$noOfPouch', processed_wt_per_btch='$processedWtPerBtch', no_of_hrs_prep='$noOfHrsPrep', no_of_hrs_cook='$noOfHrsCook', no_of_hrs_filling='$noOfHrsFilling',no_of_hrs_fill='$noOfHrsFill',no_of_hrs_retort='$noOfHrsRetort', no_of_hrs_first_btch='$noOfHrsFirstBtch', no_of_hrs_other_btch='$noOfHrsOtherBtch', gas_required='$gasRequired',boiler_required='$boilerRequired',elect_required='$electRequired',boiler_required_processing='$boilerRequiredProcessing', no_of_btchs_per_day='$noOfBtchsPerDay',  diesel_cost_per_btch='$dieselCostPerBtch', electric_cost_per_btch='$electricityCostPerBtch', water_cost_per_btch='$waterCostPerBtch', gas_cost_per_btch='$gasCostPerBtch', tot_fuel_cost_per_btch='$totFuelCostPerBtch', maint_cost_per_btch='$maintCostPerBtch', vari_manpower_cost_per_btch='$variManPwerCostPerBtch', mktg_cost_per_btch='$mktgTeamCostPerPouch', 
		coordination_cost='$coordinationCostPerPouch',mktg_travel_cost='$mktgTravelCost', ad_cost_per_pouch='$adCostPerPouch',facility_cost='$facilityCostPerDay' where id=$productionMatrixRecId ";
		
		//echo $qry;
		//die();
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deleteProductionMatrixRec($productionMatrixRecId)
	{
		$qry	= " delete from m_production_matrix where id=$productionMatrixRecId";

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



	function getProductionWorkingHours()
	{
		$qry = "select no_of_hours_shift,no_of_shifts,no_of_gravy_cookers,no_of_retorts,no_of_sealing_machines,no_of_pouches_sealed,no_of_minutes_for_sealing,no_of_days_in_year,no_of_working_days_in_month,no_of_hours,no_of_minutes_per_hour  from m_production_working_hours order by id desc";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return array($result[0],$result[1],$result[2],$result[3],$result[4],$result[5],$result[6],$result[7],$result[8],$result[9],$result[10]);
	}

	function getProductionFuelRate()
	{
		$qry = "select diesel_rate_per_unit,diesel_consumption_batch,electricity_rate_per_unit,electricity_consumption_per_day,waterprocessing_rate_per_unit,waterprocessing_consumption_batch,watergeneral_rate_per_unit,watergeneral_consumption_per_day,gas_rate_per_unit,gas_per_day  from m_production_fuel_price order by id desc";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return array($result[0],$result[1],$result[2],$result[3],$result[4],$result[5],$result[6],$result[7],$result[8],$result[9]);
	}

	function getProductionOtherCost()
	{
		$qry = "select maintenance_cost,consumables_cost,lab_cost,	pouches_perbatch_unit,pouches_perbatch_tcost,ingredient_powdering_cosperkg,holding_cost,holding_duration,admin_overhead_charges_code,admin_overhead_charges_cost,profit_margin,insurance_cost  from m_production_other_cost order by id desc";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return array($result[0],$result[1],$result[2],$result[3],$result[4],$result[5],$result[6],$result[7],$result[8],$result[9],$result[10],$result[11]);
		//printr($res);
		//return $res;
	}

	function getProductionPowerValue()
	{
		$qry1="select sum(new_total_cost) from m_production_variable_manpower";
		$result1=$this->databaseConnect->getRecord($qry1);
		$qry2="select sum(new_total_cost) from m_production_fixed_manpower";
		$result2=$this->databaseConnect->getRecord($qry2);
		$qry3="select sum(new_total_cost) from  m_production_variable_marketing";
		$result3=$this->databaseConnect->getRecord($qry3);
		$qry4="select sum(new_total_cost) from m_production_fixed_marketing";
		$result4=$this->databaseConnect->getRecord($qry4);
		$qry5="select advt_cost_per_month from m_production_advertisement";
		$result5=$this->databaseConnect->getRecord($qry5);
		$qry6="select sum(new_total_cost) from m_production_variable_operation";
		$result6=$this->databaseConnect->getRecord($qry6);
		$qry7="select sum(new_total_cost) from m_production_fixed_operation";
		$result7=$this->databaseConnect->getRecord($qry7);
		
		$prdVarManPower=$result1[0];
		$prdFixManPower=$result2[0];
		$prdVarMarketing=$result3[0];
		$prdFixMarketing=$result4[0];
		//$totalMarketing=$result3[0]+$result4[0];
		$advertisementCost=$result5[0];
		$prdVarOperation=$result6[0];
		$prdFixOperation=$result7[0];
		$operationCost=$prdVarOperation+$prdFixOperation;
		$res= array($prdVarManPower,$prdFixManPower,$prdVarMarketing,$prdFixMarketing,$advertisementCost,$operationCost);
		//printr($res);
		return $res;
	}

	function getProcessRecords()
	{
		$qry = "select * from(select id,name from m_process_master where active=1 order by id desc) dum group by name order by name asc";
		return $this->databaseConnect->getRecords($qry);
	}

	function chckDuplicate($name,$processType)
	{
		//$nm=preg_replace('/[^A-Za-z0-9\-\']/', '', $name); 
		//echo $nm;
		$qry="select id from m_production_matrix where name='$name' and process_type='$processType'";
		//echo $qry;
		//die();
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	//Get All Production Name
	function getProductionName($netWgt)
	{
		$qry = "select id, name from m_production_matrix where filling_wt_per_pouch like '$netWgt%'";
		//echo $qry;
		$result = array();
		$result=$this->databaseConnect->getRecords($qry);
		
		if (sizeof($result)>=1) $resultArr1 = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr1 = array();
		else $resultArr1 = array(''=>'-- Select --');
		
		while (list(,$v) = each($result)) {
			$resultArr1[$v[0]] = $v[1];
		}
		
		$qry2 = "select id, name from m_production_matrix where filling_wt_per_pouch not like '$netWgt%'";
		$result2 = array();
		$result2 = $this->databaseConnect->getRecords($qry2);
		$resultArr1[0]="-- Others --";
		while (list(,$v) = each($result2)) {
			$resultArr1[$v[0]] = $v[1];
		}
		
		return $resultArr1;
	}
	
	function getProcessType($productnId)
	{
		$qry = "select process_type, water_cost_per_btch, diesel_cost_per_btch, electric_cost_per_btch, gas_cost_per_btch, maint_cost_per_btch, vari_manpower_cost_per_btch from m_production_matrix where id='$productnId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result>0)?$result:"");
	}

	#Before deleting check whether the Process Method is using in Production Matrix
	function getEntryExist($processId)
	{
		$qry = "select id,name from m_production_matrix where process_type='$processId'";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result>0)?$result:"");
	}
	
}
?>