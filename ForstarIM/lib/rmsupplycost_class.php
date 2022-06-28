<?php
class RMSupplyCost
{  
	/****************************************************************
	This class deals with all the operations relating to RM Supply Cost
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function RMSupplyCost(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addSupplyCost($selWtChallanId, $numIceBlocks, $costPerBlock, $totalIceCost, $fixedIceCost, $km, $costPerKm, $totalTransAmt, $fixedTransCost, $totalQuanty, $commissionPerKg, $totalCommiRate, $fixedCommiRate, $totalRMQuanty, $handlingRatePerKg, $totalHandlingAmt, $fixedHandlingAmt,  $selOption, $selCommission, $grandTotalCommiAmt, $selHandling, $grandTotalHadlngAmt, $icePaid, $tptPaid, $commiPaid,  $hadlngPaid)
 	{
		$qry = "insert into t_rmsupplycost (challan_id, ice_total_block, ice_cost_per_block, ice_total_cost, ice_fixed_cost, tran_km, tran_cost_per_km, tran_total_amt, tran_fixed_amt, comm_total_qty, comm_per_kg, comm_total_rate, comm_fixed_rate, supplycost_date, handl_total_qty, handl_rate_per_kg, handl_total_amt, handl_fixed_amt , settled_option, commission_option, commission_total_amt, handling_option, handling_total_amt, ice_paid, transportation_paid, commission_paid, handling_paid) values('$selWtChallanId', '$numIceBlocks', '$costPerBlock', '$totalIceCost', '$fixedIceCost', '$km', '$costPerKm', '$totalTransAmt', '$fixedTransCost', '$totalQuanty', '$commissionPerKg', '$totalCommiRate', '$fixedCommiRate', Now(), '$totalRMQuanty', '$handlingRatePerKg', '$totalHandlingAmt', '$fixedHandlingAmt', '$selOption', '$selCommission', '$grandTotalCommiAmt', '$selHandling', '$grandTotalHadlngAmt', '$icePaid', '$tptPaid', '$commiPaid', '$hadlngPaid')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	

	# Returns all Records	
	function fetchAllRecords($fromDate, $tillDate)
	{
		$qry = "select a.id, b.weighment_challan_no, a.ice_total_block, a.ice_cost_per_block, a.ice_total_cost, a.ice_fixed_cost, a.tran_km, a.tran_cost_per_km, a.tran_total_amt, a.tran_fixed_amt, a.comm_total_qty, a.comm_per_kg, a.comm_total_rate, a.comm_fixed_rate, b.payment_confirm, a.handl_total_qty, a.handl_rate_per_kg, a.handl_total_amt, a.handl_fixed_amt, a.commission_total_amt, a.handling_total_amt, b.id, CONCAT(b.alpha_code,'',b.weighment_challan_no) from t_rmsupplycost a, t_dailycatch_main b where a.challan_id=b.id and b.select_date>='$fromDate' and b.select_date<='$tillDate' order by b.weighment_challan_no asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Pagination
	function filterAllSupplyCostRecords($fromDate, $tillDate, $offset, $limit) 
	{
		$qry = "select a.id, b.weighment_challan_no, a.ice_total_block, a.ice_cost_per_block, a.ice_total_cost, a.ice_fixed_cost, a.tran_km, a.tran_cost_per_km, a.tran_total_amt, a.tran_fixed_amt, a.comm_total_qty, a.comm_per_kg, a.comm_total_rate, a.comm_fixed_rate, b.payment_confirm, a.handl_total_qty, a.handl_rate_per_kg, a.handl_total_amt, a.handl_fixed_amt, a.commission_total_amt, a.handling_total_amt, b.id, CONCAT(b.alpha_code,'',b.weighment_challan_no) from t_rmsupplycost a, t_dailycatch_main b where a.challan_id=b.id and b.select_date>='$fromDate' and b.select_date<='$tillDate' order by b.weighment_challan_no asc limit $offset,$limit ";
		/*	Edited 19-07-08 this is range of supply cost created
			$qry = "select a.id, b.weighment_challan_no, a.ice_total_block, a.ice_cost_per_block, a.ice_total_cost, a.ice_fixed_cost, a.tran_km, a.tran_cost_per_km, a.tran_total_amt, a.tran_fixed_amt, a.comm_total_qty, a.comm_per_kg, a.comm_total_rate, a.comm_fixed_rate, b.payment_confirm, a.handl_total_qty, a.handl_rate_per_kg, a.handl_total_amt, a.handl_fixed_amt, a.commission_total_amt, a.handling_total_amt, b.id from t_rmsupplycost a, t_dailycatch_main b where a.challan_id=b.id and a.supplycost_date>='$fromDate' and a.supplycost_date<='$tillDate' order by b.weighment_challan_no asc limit $offset,$limit ";
		*/
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record based on id 	
	function find($supplyCostId)
	{
		$qry = "select a.id, a.challan_id, a.ice_total_block, a.ice_cost_per_block, a.ice_total_cost, a.ice_fixed_cost, a.tran_km, a.tran_cost_per_km, a.tran_total_amt, a.tran_fixed_amt, a.comm_total_qty, a.comm_per_kg, a.comm_total_rate, a.comm_fixed_rate, b.select_date, a.handl_total_qty, a.handl_rate_per_kg, a.handl_total_amt, a.handl_fixed_amt, a.settled_option, a.commission_option, a.commission_total_amt, a.handling_option, a.handling_total_amt, a.ice_paid, a.transportation_paid, a.commission_paid, a.handling_paid from t_rmsupplycost a, t_dailycatch_main b where a.challan_id=b.id and a.id=$supplyCostId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 	
	function updateSupplyCost($supplyCostId, $numIceBlocks, $costPerBlock, $totalIceCost, $fixedIceCost, $km, $costPerKm, $totalTransAmt, $fixedTransCost, $totalQuanty, $commissionPerKg, $totalCommiRate, $fixedCommiRate, $totalRMQuanty, $handlingRatePerKg, $totalHandlingAmt, $fixedHandlingAmt, $selOption, $selCommission, $grandTotalCommiAmt, $selHandling, $grandTotalHadlngAmt, $icePaid, $tptPaid, $commiPaid,  $hadlngPaid)
	{
		$qry	=	" update t_rmsupplycost set ice_total_block='$numIceBlocks', ice_cost_per_block='$costPerBlock', ice_total_cost='$totalIceCost', ice_fixed_cost='$fixedIceCost', tran_km='$km', tran_cost_per_km='$costPerKm', tran_total_amt='$totalTransAmt', tran_fixed_amt='$fixedTransCost', comm_total_qty='$totalQuanty', comm_per_kg='$commissionPerKg', comm_total_rate='$totalCommiRate', comm_fixed_rate='$fixedCommiRate', handl_total_qty='$totalRMQuanty', handl_rate_per_kg='$handlingRatePerKg', handl_total_amt='$totalHandlingAmt', handl_fixed_amt='$fixedHandlingAmt', settled_option='$selOption', commission_option='$selCommission', commission_total_amt='$grandTotalCommiAmt', handling_option='$selHandling', handling_total_amt='$grandTotalHadlngAmt', ice_paid='$icePaid', transportation_paid='$tptPaid', commission_paid='$commiPaid', handling_paid='$hadlngPaid' where id=$supplyCostId";
 		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	
	# Delete 
	function deleteSupplyCost($supplyCostId)
	{
		$qry	=	" delete from t_rmsupplycost where id=$supplyCostId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
////////////////////////////////////////////	
#get catch entry records based on Date
	function  getCatchEntryRecords($selectDate)
	{
		$qry	= "select id, weighment_challan_no, CONCAT(alpha_code,'',weighment_challan_no) from t_dailycatch_main where select_date='$selectDate' and weighment_challan_no is not null order by weighment_challan_no desc";
		//and payment_confirm!='Y'
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

#Find Distance of Selected Landing Center
	function findLandingcenterKm($selWtChallanId){
		$qry= " select a.landing_center, b.distance from t_dailycatch_main a, m_landingcenter b where a.id='$selWtChallanId' and a.landing_center=b.id";
		//echo $qry;
		$rec =	$this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[1]:""; 
	}
#Find total Qty
	function getTotalWtChallanQty($selWtChallanId){
	
		$qry	=	"select b.effective_wt from t_dailycatch_main a, t_dailycatchentry b where a.id=b.main_id and a.id='$selWtChallanId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$totalQty = 0;
		foreach ($result as $R)
		{
			$qty 	  = $R[0];
			$totalQty += $qty;
		}
		return $totalQty;
	}

	function getRMRecord($selWtChallanId) 
	{
		$qry	= " select id, unit, select_date, select_time, vechile_no, weighment_challan_no, landing_center, main_supplier, CONCAT(alpha_code,'',weighment_challan_no), payment_by from t_dailycatch_main where id='$selWtChallanId' ";		
		//echo $qry;		
		return $this->databaseConnect->getRecord($qry);
	}

	#Filter Fish Process Summary
	function filterFishProcessSummaryRecords($selWtChallanId)
	{
		$whr = "a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.id=$selWtChallanId" ;	
		
		//$groupBy = "b.fish_code";
		$groupBy = " b.id ";
		
		$orderBy = "c.name asc, d.code asc, b.effective_wt desc ";
		
		$qry = "select a.id, b.fish, b.fish_code, b.effective_wt, sum(b.effective_wt), d.code, b.commission_rate, sum(b.commission_amt), b.handling_rate, sum(b.handling_amt), b.count_values,  b.grade_id, a.payment_by, b.id from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d ";

		if ($whr!="") 	  $qry   .= " where ".$whr;
		if ($groupBy!="") $qry   .= " group by ".$groupBy;
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Select distinct Supplier
	function fetchSupplierRecords($fromDate, $tillDate, $dateSelectFrom)
	{
		/* Original upto 17-4-09
		$whr = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.main_supplier=b.id ";		
		$orderBy = "b.name asc";		
		$qry = "select distinct a.main_supplier, b.id, b.name from t_dailycatch_main a, supplier b $tableName";		
		if($whr!="") $qry .= " where ".$whr;
		if($orderBy!="") $qry .= " order by ".$orderBy;
		*/
		if($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	
		} else {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";		
			$tableJoin = " and a.id=c.main_id and c.id=d.entry_id";
			$tableName = " , t_dailycatchentry c, t_dailycatch_declared d ";			
		}
		
		$whr	=	"a.main_supplier=b.id and $dateSelection $tableJoin";
		$orderBy	=	"b.name asc";
		$qry	= "select distinct a.main_supplier, b.id, b.name from t_dailycatch_main a, supplier b $tableName";
		
		if ($whr!="") $qry	.= " where ".$whr;
		if ($orderBy!="") $qry	.= " order by ".$orderBy;
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Filter Fish Process Summary
	function getProcessSummaryRecords($fromDate, $tillDate, $selectSupplier, $dateSelectFrom, $searchType)
	{
		/* Original upto 17-4-09
		$whr		=	"a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.main_supplier='".$selectSupplier."'" ;		
		$groupBy	=	"b.fish_code desc";		
		$orderBy	=	"c.name asc, d.code asc, b.effective_wt desc ";		
		$qry		=	"select a.id, b.fish, b.fish_code, b.effective_wt, sum(b.effective_wt), d.code from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d ";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($groupBy!="") $qry   .=" group by ".$groupBy;			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		*/

		$totalQty = "";
		$tableJoin = "";
		$tableName = "";
		if($dateSelectFrom=='WCD') {			
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	
			$totalQty = " sum(b.effective_wt) ";
		} else {
			$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			$tableJoin = " and b.id=e.entry_id";
			$tableName = " , t_dailycatch_declared e ";

			$totalQty = " sum(e.decl_wt) ";
		}


		$whr = " $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.main_supplier='$selectSupplier' $tableJoin " ;

		$groupBy = " b.fish_code ";
		$selQry  = "";
		/*
		if ($dateSelectFrom=='SCD' && $searchType=='CS') {
			$groupBy .= " , e.decl_count";
			$selQry  = " , e.decl_count";
		}
		*/
	
		$orderBy = "c.name asc, d.code asc, b.effective_wt desc ";
		
		$qry = "select a.id, b.fish, b.fish_code, b.effective_wt, $totalQty, d.code $selQry from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d $tableName";
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($groupBy!="")	$qry   .= " group by ".$groupBy;			
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
			
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	#Filter Daily Catch Entry Recs
	function filterDailyCatchEntryRecords($selWtChallanId)
	{
		$whr		=	"a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.id=$selWtChallanId" ;	
		
		$orderBy	=	"c.name asc, d.code asc, b.effective_wt desc ";
		
		$qry		=	"select a.id, b.id, b.fish, b.fish_code, b.effective_wt from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d ";

		if ($whr!="") $qry   .= " where ".$whr;	
			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# update catch Entry Commission Update
	function  updateCatchEntryCommissionRec($dailyCatchEntryId, $rate, $totalAmt)
	{
		$qry	=	" update t_dailycatchentry set commission_rate='$rate', commission_amt='$totalAmt' where id=$dailyCatchEntryId";
 		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	# update catch Entry Handling Cost Update
	function  updateCatchEntryHandlingRec($dailyCatchEntryId, $rate, $totalAmt)
	{
		$qry = " update t_dailycatchentry set handling_rate='$rate', handling_amt='$totalAmt' where id=$dailyCatchEntryId ";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Remove catch Entry Commission Rec
	function  removeCatchEntryCommissionRec($dailyCatchEntryId)
	{
		$qry = " update t_dailycatchentry set commission_rate=0, commission_amt=0 where id=$dailyCatchEntryId";
 		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Remove catch Entry Handling Cost 
	function  removeCatchEntryHandlingRec($dailyCatchEntryId)
	{
		$qry = " update t_dailycatchentry set handling_rate=0, handling_amt=0 where id=$dailyCatchEntryId ";
 		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	#Filter Challan Numbers
	function getDailyCatchEntryRecs($fromDate, $tillDate, $selectSupplier)
	{
		$whr = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.main_supplier='".$selectSupplier."'" ;
					
		//$orderBy	=	"c.name asc, d.code asc, b.effective_wt desc ";
		
		$qry = "select a.id, b.id, b.fish, b.fish_code, b.effective_wt, d.code from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d ";

		if ($whr!="") $qry   .=" where ".$whr;		
			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
			
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Check RM Individually Set
	function checkRMIndividuallySet($fromDate, $tillDate, $selectSupplier, $processCodeId)
	{
		$whr = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and a.main_supplier='".$selectSupplier."' and b.fish_code='".$processCodeId."' and a.id=c.challan_id" ;
		
		$qry = "select a.id from t_dailycatch_main a, t_dailycatchentry b, t_rmsupplycost c ";

		if ($whr!="") 	  $qry   .= " where ".$whr;
		if ($groupBy!="") $qry   .= " group by ".$groupBy;
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
			
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return	(sizeof($result)>0)?true:false;  
		// Size >0 this processcode Settled Individually else not settled	
	}

	#After Click group Option
	function addGroupedSupplyCost($selWtChallanId, $selectedOption, $selCommission, $grandTotalCommiAmt, $selHandling, $grandTotalHadlngAmt)
 	{
		$qry = "insert into t_rmsupplycost (challan_id, settled_option, commission_option, commission_total_amt, handling_option, handling_total_amt, supplycost_date) values('$selWtChallanId', '$selectedOption', '$selCommission', '$grandTotalCommiAmt', '$selHandling', '$grandTotalHadlngAmt', Now())";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $insertStatus;
	}

	# Decl Wt Records
	function declWtRecords($dcEntryId)
	{
		$qry = " select id, supplier_challan_no, supplier_challan_date, decl_wt, decl_count from t_dailycatch_declared where entry_id='$dcEntryId' order by decl_count asc";
		return $this->databaseConnect->getRecords($qry);
	}
		
}