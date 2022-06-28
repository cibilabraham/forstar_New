<?php
Class DailyCatchSummary
{

	/****************************************************************
	This class deals with all the operations relating to Daily Catch Summary 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, whdich will create a db instance for this class
	function DailyCatchSummary(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#Select disinct Unit/Plants
	function fetchPlantWiseRecords($fromDate, $tillDate, $selectADate, $dateSelectFrom)
	{
		/*$dateSelection = "";
		if($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}*/

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and a.id=c.main_id and c.id=d.entry_id";
			$tableName = " , t_dailycatchentry c, t_dailycatch_declared d ";
		}

		$qry	= "select distinct a.unit,b.id,b.name from t_dailycatch_main a, m_plant b $tableName where a.unit=b.id and $dateSelection $tableJoin order by b.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	#Select Distinct Landing Center
	function fetchLandingCenterRecords($fromDate, $tillDate, $selectUnit, $selectADate, $dateSelectFrom)
	{
		/*$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}*/		

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and a.id=c.main_id and c.id=d.entry_id";
			$tableName = " , t_dailycatchentry c, t_dailycatch_declared d ";
		}

		$whr	=	"a.landing_center=b.id and $dateSelection $tableJoin";
		
		if ($selectUnit!=0)  $whr .=" and a.unit=".$selectUnit;
		
		$orderBy = " b.name asc";
		$qry	 = "select distinct a.landing_center, b.id, b.name from t_dailycatch_main a, m_landingcenter b $tableName";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;	
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Select distinct Supplier
	function fetchSupplierRecords($fromDate, $tillDate, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $billingCompany)
	{
		/*$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}*/

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and a.id=c.main_id and c.id=d.entry_id";
			$tableName = " , t_dailycatchentry c, t_dailycatch_declared d ";
			//$distinct = " distinct ";
		}
		
		$whr = "a.main_supplier=b.id and $dateSelection $tableJoin";
		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$orderBy	=	"b.name asc";
		
		$qry	= "select distinct a.main_supplier, b.id, b.name from t_dailycatch_main a, supplier b $tableName";
		
		if ($whr!="") $qry	.= " where ".$whr;
		if ($orderBy!="") $qry	.= " order by ".$orderBy;
		//echo "<br/>Supp=$qry<br/>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fish Records for a date range	
	function fetchFishRecords($fromDate, $tillDate, $selectSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $billingCompany)
	{

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";
		}	
		

		$whr = "b.fish=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
	
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;	
		if ($landingCenterId!=0)$whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$orderBy	=	"c.name asc";
	
		$qry = "select distinct b.fish, c.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c $tableName";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}	

	#Process Code Records
	function getProcessCodeRecords($fromDate, $tillDate, $fishId, $selectSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $billingCompany)
	{
		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";
		}
		
		$whr = "b.fish_code=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."' $tableJoin";
	
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$orderBy	=	"c.code asc";
	
		$qry = "select distinct b.fish_code,c.code from t_dailycatch_main a, t_dailycatchentry b, m_processcode c $tableName";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}	
	
	# Filter all Records based on the unit, supplier and landing center (Using in Advance and Quick Search)
	function filterDailyCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany)
	{
		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "c.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "c.supplier_challan_date>='".$fromDate."' and c.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and b.id=c.entry_id";
			$tableName = " , t_dailycatch_declared c ";
			$distinct = " distinct ";
		}

		//$whr	=	"$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin" ;
		$whr	=	"$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin and b.select_rate!=0" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$orderBy	=	"a.weighment_challan_no asc";
		
		//$qry		=	"select $distinct a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b $tableName";

		$qry		=	"select $distinct a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, CONCAT(a.alpha_code,'',a.weighment_challan_no),b.select_rate,b.actual_amount from t_dailycatch_main a, t_dailycatchentry b $tableName";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
			
		//echo $qry."<br>";				
		$result	= $this->databaseConnect->getRecords($qry);


		$whr1	=	"$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin and b.select_rate=0" ;
		
		if ($selectUnit!=0) $whr1 .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr1 .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr1 .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr1 .= " and b.fish=".$fishId;		
		if ($processId!="") $whr1 .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr1 .= " and a.billing_company_id='".$billingCompany."'";
		
		$orderBy1	=	"a.weighment_challan_no asc";
		
		//$qry		=	"select $distinct a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b $tableName";

		$qry1		=	"select $distinct a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, CONCAT(a.alpha_code,'',a.weighment_challan_no),b.select_rate,b.actual_amount from t_dailycatch_main a, t_dailycatchentry b $tableName";
		if ($whr1!="") $qry1   .=" where ".$whr1;
		if ($orderBy1!="") $qry1   .=" order by ".$orderBy1;
			
		//echo $qry1."<br>";				
		$result1	= $this->databaseConnect->getRecords($qry1);



		$result2=array_merge($result, $result1);


		return $result2;
	}

	#Process Count Summary
	function filterProcessCountSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}

		/*if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "e.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and b.id=e.entry_id";
			$tableName = " , t_dailycatch_declared e ";
			$distinct = " distinct ";
		}*/

		//$whr	=	"$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id $tableJoin" ;
		$whr	=	"$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id $tableJoin" ;
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	=	"b.fish_code desc, b.grade_id, b.count_values";		
		
		$orderBy 	= "c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc";
		
		$qry		= "select a.id, b.fish, b.fish_code, b.count_values, b.decl_count, b.decl_wt, b.effective_wt, b.grade_id, b.received_by, sum(b.effective_wt), sum(b.net_wt), sum(b.adjust), sum(b.local_quantity), sum(b.wastage), sum(b.soft),sum(b.actual_amount),sum(b.select_rate) from (t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d) left join m_grade e on b.grade_id=e.id $tableName";

		if ($whr!="") 	$qry   	.= " where ".$whr;
		if ($groupBy!="") $qry	.= " group by ". $groupBy;
		if ($orderBy!="") $qry  .= " order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Filter Fish Process Summary
	function filterFishProcessSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany)
	{
		/*$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}*/	
		$totalQty = "";
		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}

			$totalQty = " sum(b.effective_wt) ";

		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "e.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and b.id=e.entry_id";
			$tableName = " , t_dailycatch_declared e ";
			$distinct = " distinct ";
			$totalQty = " sum(e.decl_wt) ";
		}


		$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id $tableJoin" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	=	"b.fish_code desc";		
		$orderBy	=	"c.name asc, d.code asc, b.effective_wt desc ";
		
		//$qry		=	"select a.id, b.fish, b.fish_code, b.effective_wt, $totalQty, sum(b.net_wt), sum(b.adjust), sum(b.local_quantity), sum(b.wastage), sum(b.soft), sum(b.grade_count_adj) from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d $tableName";
		$qry		=	"select a.id, b.fish, b.fish_code, b.effective_wt, $totalQty, sum(b.net_wt), sum(b.adjust), sum(b.local_quantity), sum(b.wastage), sum(b.soft), sum(b.grade_count_adj),sum(b.actual_amount) from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d $tableName";
		if ($whr!="")	$qry   .=" where ".$whr;
		if ($groupBy!="")	$qry   .=" group by ".$groupBy;			
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Get Data Wise Records
	function fetchDateWiseRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany)
	{		
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}

		$whr	= "$dateSelection and a.id=b.main_id and weighment_challan_no is not null" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .=	" and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$orderBy	= "b.fish desc";
		
		$qry		= "select distinct a.weighment_challan_no, a.select_date, a.main_supplier, a.landing_center, a.unit from t_dailycatch_main a, t_dailycatchentry b";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#In Print Window Filter Summary Records based on Challn No
	function filterSummaryChallanWiseRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $challanNo, $selectADate)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}	

		$whr = "$dateSelection and a.id=b.main_id and weighment_challan_no is not null" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($challanNo!=0) $whr .= " and a.weighment_challan_no=".$challanNo;

		$orderBy	=	"b.fish desc";
		
		$qry = "select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time from t_dailycatch_main a, t_dailycatchentry b";
		if ($whr!="") $qry   .= " where ".$whr;
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#fish Catch Summary Records
	function filterFishWiseCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}	

		//$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null" ;
		//$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null" ;
		$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.select_rate!=0" ;
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	=	"b.fish desc";
		
		//$qry		=	"select a.id, b.fish, b.fish_code, b.effective_wt, sum(b.effective_wt) from t_dailycatch_main a, t_dailycatchentry b ";
		//$qry		=	"select a.id, b.fish, b.fish_code, b.effective_wt, sum(b.effective_wt),sum(b.select_rate)/count(b.fish),sum(b.actual_amount)/ sum(b.effective_wt) from t_dailycatch_main a, t_dailycatchentry b ";
		$qry		=	"select a.id, b.fish, b.fish_code, b.effective_wt, sum(b.effective_wt),sum(b.select_rate)/count(b.fish),sum(b.actual_amount) from t_dailycatch_main a, t_dailycatchentry b ";
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);



		$whr1 = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.select_rate=0" ;
		if ($selectUnit!=0) $whr1 .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr1 .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr1 .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr1 .= " and b.fish=".$fishId;		
		if ($processId!="") $whr1 .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr1 .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy1	=	"b.fish desc";
		
		//$qry		=	"select a.id, b.fish, b.fish_code, b.effective_wt, sum(b.effective_wt) from t_dailycatch_main a, t_dailycatchentry b ";
		$qry1		=	"select a.id, b.fish, b.fish_code, b.effective_wt, sum(b.effective_wt),sum(b.select_rate)/count(b.fish),sum(b.actual_amount)/ sum(b.effective_wt) from t_dailycatch_main a, t_dailycatchentry b ";
		if ($whr1!="") $qry1 .= " where ".$whr1;
		if ($groupBy1!="") $qry1 .= " group by ".$groupBy1;
		//echo $qry1;		
		$result1	= $this->databaseConnect->getRecords($qry1);
		$result2=array_merge($result,$result1);



		return $result2;
	}
	
	#Weighment Challan No wise summary
	function filterWtChallanRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}	

		$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and a.main_supplier=c.id and b.fish=d.id and b.fish_code=e.id and b.grade_id=f.id" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";		

		$orderBy	= "a.weighment_challan_no asc, c.name asc, d.name asc, e.code asc, b.count_values asc, f.code asc ";
		
		//$qry		= "select a.id, a.select_date, a.main_supplier, a.weighment_challan_no, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, c.name, d.name, e.code, f.code, b.adjust, b.local_quantity, b.wastage, b.soft, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b, supplier c, m_fish d, m_processcode e, m_grade f ";
		$qry		= "select a.id, a.select_date, a.main_supplier, a.weighment_challan_no, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, c.name, d.name, e.code, f.code, b.adjust, b.local_quantity, b.wastage, b.soft, CONCAT(a.alpha_code,'',a.weighment_challan_no),b.select_rate,b.actual_amount from t_dailycatch_main a, t_dailycatchentry b, supplier c, m_fish d, m_processcode e, m_grade f ";
		
		if ($whr!="") $qry   .= " where ".$whr;
		if ($groupBy!="") $qry   .= " group by ".$groupBy;		
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Supplier Memo
	function getSupplierDeclaredWtRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany)
	{
		/*$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}*/	

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "e.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			}
			//$tableJoin = " and b.id=e.entry_id";
			//$tableName = " , t_dailycatch_declared e ";
			//$distinct = " distinct ";
		}
		
		$whr = "$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr	.= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;

		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		$groupBy	=	"b.fish, b.fish_code , e.decl_count";

		$orderBy	=	"c.name asc, d.code asc, e.decl_count asc";
		
		$qry		=	"select a.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, sum(e.decl_wt) from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry 	.= " group by ". $groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Decl wt Summary Sheet
	//-------------------------------------------
	function getSupplierWiseDeclaredRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany)
	{

		/*$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}*/	

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "e.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			}
			//$tableJoin = " and b.id=e.entry_id";
			//$tableName = " , t_dailycatch_declared e ";
			//$distinct = " distinct ";
		}

		$whr		=	"$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	=	" e.supplier_challan_no, e.supplier_challan_date ";		
		$orderBy	=	" e.supplier_challan_no asc, e.supplier_challan_date asc";
		
		$qry		=	"select a.id, b.fish, b.fish_code, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry 	.= " group by ". $groupBy;			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the Grouped Process-Count Wise Declared Records
	function getProcessCountWiseDeclaredRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany)
	{
		/*$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}*/	

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "e.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			}
			//$tableJoin = " and b.id=e.entry_id";
			//$tableName = " , t_dailycatch_declared e ";
			//$distinct = " distinct ";
		}

		$whr = "$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish='".$fishId."'";		
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	=	"b.fish_code, e.decl_count ";		
		$orderBy	=	" b.fish_code asc, e.decl_count asc";
		
		$qry		=	"select a.id, b.fish, b.fish_code, e.supplier_challan_no, e.supplier_challan_date, sum(e.decl_wt), e.decl_count, c.name, d.code from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry 	.= " group by ". $groupBy;			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDeclaredWt($sChallanDate, $supplierChallanNo, $declCount, $processCodeId)
	{
		$qry		=	"select a.decl_wt from t_dailycatch_declared a, t_dailycatchentry b where a.entry_id=b.id and a.supplier_challan_date='$sChallanDate' and a.supplier_challan_no='$supplierChallanNo' and a.decl_count='$declCount' and b.fish_code='$processCodeId'";
		//$qry		=	"select decl_wt from t_dailycatch_declared where supplier_challan_date='$sChallanDate' and supplier_challan_no='$supplierChallanNo' and decl_count='$declCount'"; // edited on 30-1-08
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		$totalDeclWt = 0;
		foreach ($result as $dr) {
			$declWt = $dr[0];
			$totalDeclWt += $declWt;
		}
		return $totalDeclWt;
	}
	
//-----------------------------------------

#Advance Search

	# Filter all Records based on the unit, supplier and landing center

	function getAdvanceSearchGroupRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}

		$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish=c.id" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		$groupBy	=	"b.fish, b.fish_code, b.count_values";		
		$orderBy	=	"c.name asc";
		
		$qry		=	"select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, sum(b.local_quantity), sum(b.wastage), sum(b.soft), b.reason, sum(b.adjust), b.good, sum(b.peeling), b.remarks, b.gross, b.total_basket, sum(b.net_wt), b.actual_wt, sum(b.effective_wt), b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time, sum(b.grade_count_adj), b.grade_count_adj_reason, b.received_by from t_dailycatch_main a, t_dailycatchentry b, m_fish c";
		if ($whr!="")		$qry .= " where ".$whr;
		if ($groupBy!="")	$qry .= " group by ". $groupBy;			
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
			
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#RM Challan No wise Matrix summary
	function groupWtChallanRecords($fromDate, $tillDate, $selectUnit, $landingCenterId, $selectSupplier, $fishId, $processId, $selectADate, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}	

		$whr		=	"$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and a.main_supplier=c.id and b.fish=d.id and b.fish_code=e.id and b.grade_id=f.id" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	=	"a.weighment_challan_no, a.billing_company_id";
		
		$orderBy	=	"a.weighment_challan_no asc";

		$qry		=	"select a.id, a.select_date, a.main_supplier, a.weighment_challan_no, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, c.name, d.name, e.code, f.code, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b, supplier c, m_fish d, m_processcode e, m_grade f ";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($groupBy!="") $qry   .=" group by ".$groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Total Effective Weight Depend on RM Id , Fish Id and Process Code Id
	function getEffectiveWt($RMEntryId, $entryFishId, $processCodeId, $count, $rmGradeId)
	{
		$whr		=	"a.id=b.main_id and a.weighment_challan_no is not null and  a.id='$RMEntryId' and b.fish='$entryFishId' and b.fish_code='$processCodeId'" ;

		if ($count!="") $whr .= " and b.count_values='".$count."'";
		if ($rmGradeId!="") $whr .= " and b.grade_id='".$rmGradeId."'";	

		$qry 	= "select b.effective_wt from t_dailycatch_main a, t_dailycatchentry b ";
	
		if ($whr!="") $qry   .= " where ".$whr;
	
		//echo $qry."<br>";	
		$result	= $this->databaseConnect->getRecords($qry);
		$totalEffectiveWt = "";
		foreach ($result as $dr) {
			$effectiveWt 	 = $dr[0];
			$totalEffectiveWt 		+= $effectiveWt;
		}
		return $totalEffectiveWt;
	}

	#Find the totalEffective Wt of RM ID
	function findTotalEffectiveWt($RMEntryId, $selectUnit, $landingCenterId, $selectSupplier, $fishId, $processId) 
	{
		$whr =	" a.id=b.main_id and a.weighment_challan_no is not null and  a.id='$RMEntryId'" ;

		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;
		if ($fishId!="") $whr .= " and b.fish=".$fishId;
		if ($processId!="") $whr .= " and b.fish_code=".$processId;

		$qry = "select b.effective_wt from t_dailycatch_main a, t_dailycatchentry b ";
		
		if ($whr!="") $qry   .= " where ".$whr;				
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
	
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		$totalEffectiveWt = 0;
		foreach ($result as $dr) {
			$effectiveWt 	 = $dr[0];
			$totalEffectiveWt 		+= $effectiveWt;
		}
		return $totalEffectiveWt;
	}
	####################

	#RM Summary Matrix (like Process Count Summary but order by is different)
	function filterRMSummaryMatrixRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") $dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		else $dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		
		$whr	= "$dateSelection and a.weighment_challan_no is not null " ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;				
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;
		if ($fishId!="") $whr .= " and b.fish=".$fishId;
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy = "b.fish_code desc, b.grade_id, b.count_values";
		
		$orderBy = "c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc ";
		
		$qry	= "select a.id, b.fish, b.fish_code, b.count_values, b.decl_count, b.decl_wt, b.effective_wt, b.grade_id, b.received_by, sum(b.effective_wt), c.name as fishName, d.code as pCode, e.code as grade from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join m_fish c on b.fish=c.id join m_processcode d on b.fish_code=d.id left join m_grade e on b.grade_id=e.id";

		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy!="") $qry .= " group by ". $groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	# Filter all Records (Local Qty Report)
	function filterDailyCatchEntryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany)
	{

		$criteriaSelection = "";
		if     ($selectADate!="") $criteriaSelection = "a.select_date='".$selectADate."'"; //Single Date
		else   $criteriaSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";			

		$whr = "$criteriaSelection and a.id=main_id and b.fish=c.id and b.fish_code=d.id and b.grade_id=e.id and a.weighment_challan_no is not null" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";		
		
		$orderBy	= "a.weighment_challan_no asc, c.name asc, d.code asc, b.count_values asc, e.code asc, b.effective_wt asc";
		
		$qry		= "select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, a.payment_by, a.confirm, b.grade_count_adj, b.grade_count_adj_reason, b.received_by, a.payment_confirm, a.payment_date, a.report_confirm, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, m_grade e";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	#Get Daily Summary Records based on date and supplier
	function filterDailySummaryRecords($fromDate, $tillDate, $selectADate, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") $dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		else $dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		
		$whr  = "$dateSelection  and a.id=b.main_id and a.flag=1 ";

		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		$groupBy = " a.select_date ";
		$orderBy = " a.select_date desc ";

		//$qry  =   "select a.id, a.select_date, sum(b.effective_wt), sum(b.adjust+b.local_quantity+b.wastage+b.soft),  sum(b.adjust), sum(b.local_quantity), sum(b.wastage), sum(b.soft), sum(b.grade_count_adj) from t_dailycatch_main a, t_dailycatchentry b";
		$qry  =   "select a.id, a.select_date, sum(b.effective_wt), sum(b.adjust+b.local_quantity+b.wastage+b.soft),  sum(b.adjust), sum(b.local_quantity), sum(b.wastage), sum(b.soft), sum(b.grade_count_adj),sum(b.actual_amount) from t_dailycatch_main a, t_dailycatchentry b";

		if ($whr!="") $qry .= " where ". $whr;
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ". $orderBy;
		
		//echo $qry."<br>";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}


	#Sub Supplier Records
	function getSubSupplier($fromDate, $tillDate, $selectSupplier, $selectADate)
	{
		/*
		$dateSelection = "";
		if ($selectADate!="") $dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		else $dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		*/

		if ($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and c.id=d.entry_id ";
			$tableName = " , t_dailycatch_declared d ";		
		}
		
		$whr = " a.id=c.main_id and d.sub_supplier=b.id and a.weighment_challan_no is not null and a.main_supplier='$selectSupplier' and $dateSelection $tableJoin";

		$orderBy	=	"b.name asc";
	
		$qry = " select distinct d.sub_supplier, b.name from t_dailycatch_main a, m_subsupplier b, t_dailycatchentry c $tableName ";
	
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[1]:"";
	}
	/* Orignal Before 4-11-08
	function getSubSupplier($fromDate, $tillDate, $selectSupplier, $selectADate)
	{
		$dateSelection = "";
		if ($selectADate!="") $dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		else $dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	
		$whr = "a.sub_supplier=b.id and $dateSelection and a.weighment_challan_no is not null and a.main_supplier=$selectSupplier";
		$orderBy	=	"b.name asc";	
		$qry = "select distinct a.sub_supplier, b.name from t_dailycatch_main a, m_subsupplier b ";	
		if($whr!="") $qry .= " where ".$whr;
		if($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[1]:"";
	}	
	*/
	

	# Count Number of Sub Supplier 
	function getNumOfSubSupplier($fromDate, $tillDate, $selectSupplier, $selectADate, $dateSelectFrom)
	{
		/*
		$dateSelection = "";
		if ($selectADate!="") $dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		else $dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		*/
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and c.id=d.entry_id ";
			$tableName = " , t_dailycatch_declared d ";		
		}

		$whr = " a.id=c.main_id and d.sub_supplier=b.id and a.weighment_challan_no is not null and a.main_supplier='$selectSupplier' and $dateSelection $tableJoin";

		$orderBy = " b.name asc ";
	
		$qry = " select distinct d.sub_supplier, b.name from t_dailycatch_main a, m_subsupplier b, t_dailycatchentry c $tableName ";
	
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		

		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>1)?true:false; // Size >1 Don't allow to print
	}	
	/*
	function getNumOfSubSupplier($fromDate, $tillDate, $selectSupplier, $selectADate)
	{
		$dateSelection = "";
		if ($selectADate!="") $dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		else $dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	
		$whr = "a.sub_supplier=b.id and $dateSelection and a.weighment_challan_no is not null and a.main_supplier=$selectSupplier";
		$orderBy = "b.name asc";	
		$qry = "select distinct a.sub_supplier, b.name from t_dailycatch_main a, m_subsupplier b "	
		if($whr!="") $qry .= " where ".$whr;
		if($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>1)?true:false; // Size >1 Don't allow to print
	}
	*/


	/******************************************************
	** Decl Wt Settlement Summary Starts Here**
	******************************************************/
	#For Decl Wt Supplier Settlement Summary (If of Settled = Y )
	function getSupplierDeclWtSettlementSummary($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom)
	{
		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "e.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			}			
		}
		
		$whr = "$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and e.settled='Y'" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		
		$groupBy	=	"b.fish, b.fish_code , e.decl_count";
		$orderBy	=	"c.name asc, d.code asc, e.decl_count asc";		
		
		$qry		=	"select a.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, sum(e.decl_wt), e.rate from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry 	.= " group by ". $groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Decl Wt Supplier Settlement Summary (If of Settled = Y ) COMMISSION
	function getSupplierDeclWtCommissionSummary($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom)
	{
		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "e.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
			}			
		}
		
		$whr = "$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and e.settled='Y'" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;		
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;		
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;		
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		
		$groupBy	=	"b.fish, b.fish_code";
		$orderBy	=	"c.name asc, d.code asc";		
		
		$qry		=	"select a.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, sum(e.decl_wt), e.rate, sum(b.commission_rate), sum(b.handling_rate) from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";

		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry 	.= " group by ". $groupBy;			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;			
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	/*Decl Wt Settlement Summary Ends Here*/

	#Select distinct Supplier (using in RM Supplier Rate Matrix) 
	function fetchRMSupplierRecords($fromDate, $tillDate, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $selectSupplier, $fishId, $processId, $billingCompany)
	{
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and a.id=c.main_id and c.id=d.entry_id";
			$tableName = " , t_dailycatchentry c, t_dailycatch_declared d ";
			//$distinct = " distinct ";
		}
	
		$whr	= "a.id=b.main_id and a.main_supplier=c.id and $dateSelection $tableJoin";
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;				
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;				
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;				
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
				
		$orderBy	= "c.name asc";
		
		$qry	= "select distinct a.main_supplier, c.name from t_dailycatch_main a, t_dailycatchentry b, supplier c $tableName";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get RM Supplier Rates
	function getRMSupplierRates($fromDate, $tillDate, $selectADate, $dateSelectFrom, $rmFishId, $processCodeId, $count, $rmGradeId, $selectSupplier)
	{
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and a.id=c.main_id and c.id=d.entry_id";
			$tableName = " , t_dailycatchentry c, t_dailycatch_declared d ";
			//$distinct = " distinct ";
		}

		$whr = "a.id=b.main_id and a.weighment_challan_no is not null and b.fish='$rmFishId' and b.fish_code='$processCodeId' and a.main_supplier='$selectSupplier' and b.select_rate!=0 and $dateSelection $tableJoin" ;

		if ($count!="") $whr .= " and b.count_values='".$count."'";	
		if ($rmGradeId!="") $whr .= " and b.grade_id='".$rmGradeId."'";
		
		$orderBy	= "b.select_rate asc";

		$qry = "select distinct b.select_rate from t_dailycatch_main a, t_dailycatchentry b ";
	
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;

		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);		
		return $result;
	}	

	#Weighment Challan No wise summary
	function fetchWtChallanQtyRecs($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}	

		$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and a.flag=1" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;				
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;				
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;				
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		$groupBy = " a.weighment_challan_no, a.billing_company_id ";
		$orderBy = " a.select_date asc, a.weighment_challan_no asc ";
		
		$qry = "select a.id, a.select_date, a.weighment_challan_no, sum(b.effective_wt), CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a, t_dailycatchentry b ";
		
		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($groupBy!="") 	$qry   .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;			
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch Challan billing company Records
	function fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom)
	{	

		if($dateSelectFrom=='WCD') {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
			}	
		} else {
			$dateSelection = "";
			if ($selectADate!="") {
				$dateSelection = "d.supplier_challan_date='".$selectADate."'"; //Single Date
			} else {
				$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";
			}
			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";
		}	
		
		$whr = "a.billing_company_id=bc.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
	
		if ($selectSupplier!=0) $whr .=" and a.main_supplier=".$selectSupplier;	
		if ($landingCenterId!=0) $whr .=" and a.landing_center=".$landingCenterId;		
		if ($selectUnit!=0) $whr	.=" and a.unit=".$selectUnit;

		$orderBy	=	"bc.display_name";
		
		$qry	= "select distinct bc.id, bc.display_name from t_dailycatch_main a, t_dailycatchentry b, m_billing_company bc $tableName";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo "<br/>Billing=$qry<br/>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Challan No wise summary
	function fetchChallanWiseRecs($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany)
	{
		$dateSelection = "";
		if ($selectADate!="") {
			$dateSelection = "a.select_date='".$selectADate."'"; //Single Date
		} else {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		}	

		$whr = "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and a.flag=1" ;
		
		if ($selectUnit!=0) $whr .= " and a.unit=".$selectUnit;				
		if ($landingCenterId!=0) $whr .= " and a.landing_center=".$landingCenterId;				
		if ($selectSupplier!=0) $whr .= " and a.main_supplier=".$selectSupplier;				
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code=".$processId;
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		$groupBy = " a.weighment_challan_no, a.billing_company_id ";		
		$orderBy = " a.select_date asc, a.weighment_challan_no asc, c.name asc ";
		
		$qry = "select a.id, a.select_date, a.weighment_challan_no,sum(b.effective_wt),CONCAT(a.alpha_code,'',a.weighment_challan_no),sum(b.actual_amount), c.name,sum(b.select_rate) from (t_dailycatch_main a, t_dailycatchentry b) left join supplier c on a.main_supplier=c.id ";
		
		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($groupBy!="") 	$qry   .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;			
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

}	
?>