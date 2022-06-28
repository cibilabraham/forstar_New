<?php
Class SupplierAccount
{
	/****************************************************************
	This class deals with all the operations relating to Supplier Account
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function SupplierAccount(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	#Select Distinct Landing Center
	function fetchLandingCenterRecords($fromDate, $tillDate, $settlementDate, $dateSelectFrom,$rmConfirmed)
	{	
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";
		
		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		} else {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";

			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";
		}

		$whr1 = "a.landing_center=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($settlementDate!="") $whr1.= " and b.settlement_date='".$settlementDate."'";		
		$orderBy1 = " landName asc";
		$qry1	= "select distinct c.id as landid, c.name as landName from t_dailycatch_main a, t_dailycatchentry b, m_landingcenter c $tableName";
		if ($whr1!="")		$qry1   .= " where $whr1 $confirmEnable";
		
		$qry2="SELECT ml.id as landid,ml.name as landName FROM `m_landingcenter` ml inner join m_supplier2center msc on ml.id=msc.center_id inner join supplier s on s.id=msc.supplier_id inner join t_dailycatch_main a on a.payment=msc.supplier_id ";
		if ($settlementDate!="") $qry2 .= " where  b.settlement_date='".$settlementDate."'";
		$qry2 .= " $confirmEnable group by ml.id order by landName";

		$qry="select * from ($qry1 union all $qry2) dum group by landid order by landName";

		//if ($orderBy!="") 	$qry   .= " order by ".$orderBy;

		/*$whr = "a.landing_center=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		$orderBy = " c.name asc";
		$qry	= "select distinct a.landing_center, c.id, c.name from t_dailycatch_main a, t_dailycatchentry b, m_landingcenter c $tableName";
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;*/
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Select distinct Supplier
	function fetchSupplierRecords($fromDate, $tillDate, $landingCenterId, $settlementDate, $dateSelectFrom,$rmConfirmed)
	{
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";
		
		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		} else {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";

			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";
		}
		
		$whr1	=	"a.main_supplier=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($landingCenterId) $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($settlementDate) $whr1 .= " and b.settlement_date='".$settlementDate."'";
		//$orderBy	=	"c.name asc";
		$qry1	=	"select a.main_supplier as supplier, c.id as supplierid, c.name as supplierName from t_dailycatch_main a, t_dailycatchentry b, supplier c $tableName";
		if ($whr1!="") $qry1 .= " where $whr1 $confirmEnable";
		
		$whr2	=	"a.payment=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($settlementDate) $whr2 .= " and b.settlement_date='".$settlementDate."'";
		//$orderBy	=	"c.name asc";
		$qry2	=	"select distinct a.payment as supplier, c.id as supplierid, c.name as supplierName from t_dailycatch_main a, t_dailycatchentry b, supplier c $tableName";
		if ($whr2!="") $qry2 .= " where $whr2 $confirmEnable";

		$qry="select * from ($qry1 union all $qry2)dum  group by supplier order by supplierName";

		/*$whr	=	"a.main_supplier=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($landingCenterId) $whr .= " and a.landing_center='".$landingCenterId."'";
		if ($settlementDate) $whr .= " and b.settlement_date='".$settlementDate."'";
		$orderBy	=	"c.name asc";
		$qry	=	"select distinct a.main_supplier, c.id, c.name from t_dailycatch_main a, t_dailycatchentry b, supplier c $tableName";
		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;*/
		//echo $qry;
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Select Distinct Challan Records
	function fetchChallanRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $settlementDate, $dateSelectFrom, $billingCompany,$rmConfirmed)
	{
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";
		
		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		} else {
			$dateSelection = "c.supplier_challan_date>='".$fromDate."' and c.supplier_challan_date<='".$tillDate."'";
			$tableJoin = " and b.id=c.entry_id";
			$tableName = " , t_dailycatch_declared c ";
		}

		$whr1	= " $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin ";
		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr1 .= " and a.main_supplier='".$selectSupplier."'";
		if ($settlementDate!="") $whr1 .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr1 .= " and a.billing_company_id='".$billingCompany."'";

		$whr2	= " $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin ";
		if ($selectSupplier!="") $whr2 .= " and a.payment='".$selectSupplier."'";
		if ($settlementDate!="") $whr2 .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr2 .= " and a.billing_company_id='".$billingCompany."'";

		$groupBy	= " a.weighment_challan_no, a.billing_company_id";		
		$orderBy	= "a.weighment_challan_no asc, a.alpha_code asc";
		
		$qry	= "select a.id, a.weighment_challan_no, a.alpha_code from t_dailycatch_main a, t_dailycatchentry b $tableName";
		if ($whr1!="") $qry   .=" where $whr1 $confirmEnable or $whr2 $confirmEnable";
		if ($groupBy!="") $qry   .=" group by ".$groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		
		/*$whr	= " $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin ";
		if ($landingCenterId!="") $whr .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr .= " and a.main_supplier='".$selectSupplier."'";
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		$groupBy	= " a.weighment_challan_no, a.billing_company_id";		
		$orderBy	= "a.weighment_challan_no asc, a.alpha_code asc";
		
		$qry	= "select a.id, a.weighment_challan_no, a.alpha_code from t_dailycatch_main a, t_dailycatchentry b $tableName";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($groupBy!="") $qry   .=" group by ".$groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;*/
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Find Fish Records for a date range
	function fetchFishRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $settlementDate, $dateSelectFrom, $billingCompany,$rmConfirmed)
	{
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		} else {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";

			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";
		}

		$whr1 = "b.fish=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";		
		if ($selectSupplier!="") $whr1 .= " and a.main_supplier='".$selectSupplier."'";		
		//if ($selChallanNo!="") $whr1 .= " and a.weighment_challan_no='".$selChallanNo."'";		
		if ($selChallanNo!="") $whr1 .= " and a.id='".$selChallanNo."'";		
		if ($settlementDate!="") $whr1 .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr1 .= " and a.billing_company_id='".$billingCompany."'";

		$whr2 = "b.fish=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($selectSupplier!="") $whr2 .= " and a.payment='".$selectSupplier."'";		
		if ($selChallanNo!="") $whr2 .= " and a.id='".$selChallanNo."'";		
		if ($settlementDate!="") $whr2 .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr2 .= " and a.billing_company_id='".$billingCompany."'";

		$orderBy	=	"c.name asc";
		$qry = "select distinct b.fish, c.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c $tableName";	
		if ($whr1!="") $qry .= " where $whr1 $confirmEnable or $whr2 $confirmEnable";		
		if ($orderBy!="") $qry .= " order by ".$orderBy;

		//echo $qry;		
		/*
		$whr = "b.fish=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null $tableJoin";
		if ($landingCenterId!="") $whr .= " and a.landing_center='".$landingCenterId."'";		
		if ($selectSupplier!="") $whr .= " and a.main_supplier='".$selectSupplier."'";		
		//if ($selChallanNo!="") $whr .= " and a.weighment_challan_no='".$selChallanNo."'";		
		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";		
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		$orderBy	=	"c.name asc";
		$qry = "select distinct b.fish, c.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c $tableName";	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;*/
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	


	#Process Code Records
	function getProcessCodeRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $fishId, $settlementDate, $dateSelectFrom, $billingCompany,$rmConfirmed)
	{
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		} else {
			$dateSelection = "d.supplier_challan_date>='".$fromDate."' and d.supplier_challan_date<='".$tillDate."'";

			$tableJoin = " and b.id=d.entry_id";
			$tableName = " , t_dailycatch_declared d ";
		}

		$whr1 = "b.fish_code=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."' $tableJoin";
		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr1 .= " and a.main_supplier='".$selectSupplier."'";
		//if ($selChallanNo!="") $whr1 .= " and a.weighment_challan_no='".$selChallanNo."'";
		if ($selChallanNo!="") $whr1 .= " and a.id='".$selChallanNo."'";
		if ($settlementDate!="") $whr1 .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr1 .= " and a.billing_company_id='".$billingCompany."'";

		$whr2 = "b.fish_code=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."' $tableJoin";
		if ($selectSupplier!="") $whr2 .= " and a.payment='".$selectSupplier."'";
		if ($selChallanNo!="") $whr2 .= " and a.id='".$selChallanNo."'";
		if ($settlementDate!="") $whr2 .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr2 .= " and a.billing_company_id='".$billingCompany."'";

		$orderBy	=	"c.code asc";
		$qry = "select distinct b.fish_code,c.code from t_dailycatch_main a, t_dailycatchentry b, m_processcode c $tableName";
		if ($whr1!="")		$qry .= " where $whr1 $confirmEnable or $whr2 $confirmEnable";		
		if ($orderBy!="")	$qry .= " order by ".$orderBy;	
		
		/*
		$whr = "b.fish_code=c.id and $dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."' $tableJoin";
		if ($landingCenterId!="") $whr .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr .= " and a.main_supplier='".$selectSupplier."'";
		//if ($selChallanNo!="") $whr .= " and a.weighment_challan_no='".$selChallanNo."'";
		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		$orderBy	=	"c.code asc";
		$qry = "select distinct b.fish_code,c.code from t_dailycatch_main a, t_dailycatchentry b, m_processcode c $tableName";
		if ($whr!="")		$qry .= " where ".$whr;		
		if ($orderBy!="")	$qry .= " order by ".$orderBy;			
		*/
		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	


	#select distinct settlement dates
	function fetchAllDateRecords($fromDate, $tillDate)
	{
		$qry	= "select distinct b.settlement_date from t_dailycatch_main a, t_dailycatchentry b where a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.id=b.main_id order by b.settlement_date asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Functions for supplierAccountSettlement.php
	function fetchAllCatchEntryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $offset, $limit, $rmConfirmed, $billingCompany)
	{
		$whr1=""; $whr2="";
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		if ($paidStatus=='Y') {				
			$whr	=	"a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid='Y' $confirmEnable";			
		} else if ($paidStatus=='N') {			
			$whr	=	"a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid<>'Y' $confirmEnable";
		} else {
			$whr	=	"a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null $confirmEnable";
		}

		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";				
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		if ($selPaid!="") $whr .= " and b.paid='".$selPaid."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") 
		{	
			$whr1 .= " and a.main_supplier='".$selectSupplier."'";
			$whr2 .= " and a.payment='".$selectSupplier."'";
		}
		//if ($selChallanNo!="") $whr .= " and a.weighment_challan_no='".$selChallanNo."'";
		
				
		//$orderBy = "a.select_date asc, a.weighment_challan_no asc, c.name asc, d.name asc, e.code asc, b.count_values asc, f.code asc, b.effective_wt asc";
		$orderBy = "selectDate asc, wtChellan asc, supplierName asc, Name asc,code asc, cntValue asc, fcode asc, eftwt asc";	
		$limit	= " ".$offset.", ".$limit."";
			
		$qry1	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as selectDate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no as wtChellan, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values as cntValue, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt  as eftwt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, a.confirm, a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no), c.name as supplierName,d.name as Name,e.code as code,f.code as fcode from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.main_supplier=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr1!="") $qry1   .=" where $whr $whr1 $confirmEnable";

		$qry2	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as selectDate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no as wtChellan, a.landing_center, a.payment, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values as cntValue, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt as eftwt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, a.confirm, a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no),c.name as supplierName,d.name as Name,e.code as code,f.code as fcode from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.payment=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		
		if ($whr!="") $qry2   .=" where $whr $whr2 $confirmEnable";

		$qry="select * from ($qry1 union all $qry2) dum";
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		if ($limit!="") $qry   .=" limit ".$limit;
		//echo $qry."<br>";


		/*
		if ($landingCenterId!="") $whr .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr .= " and a.main_supplier='".$selectSupplier."'";
		//if ($selChallanNo!="") $whr .= " and a.weighment_challan_no='".$selChallanNo."'";
		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";				
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		if ($selPaid!="") $whr .= " and b.paid='".$selPaid."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
				
		$orderBy = "a.select_date asc, a.weighment_challan_no asc, c.name asc, d.name asc, e.code asc, b.count_values asc, f.code asc, b.effective_wt asc";
			
		$limit	= " ".$offset.", ".$limit."";
			
		$qry	= "select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, a.confirm, a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.main_supplier=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		if ($limit!="") $qry   .=" limit ".$limit;
		//echo $qry."<br>";
		*/

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Getting All Detailed Catch Entry Records
	function getDetailedCatchEntryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $rmConfirmed, $billingCompany)
	{	
		$whr1=""; $whr2="";

		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		if ($paidStatus=='Y') {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid='Y' $confirmEnable";
		} else if ($paidStatus=='N') {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid<>'Y' $confirmEnable";
		} else {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null $confirmEnable";
		}

		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";				
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		if ($selPaid!="") $whr .= " and b.paid='".$selPaid."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") { 
			$whr1 .= " and a.main_supplier='".$selectSupplier."'";
			$whr2 .= " and a.payment='".$selectSupplier."'";
		}
		
		$orderBy = "selectDate asc, wtChellan asc, supplierName asc, Name asc,code asc, cntValue asc, fcode asc, eftwt asc";	
		$limit	= " ".$offset.", ".$limit."";
			
		$qry1	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as selectDate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no as wtChellan, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values as cntValue, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt  as eftwt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, a.confirm, a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no), c.name as supplierName,d.name as Name,e.code as code,f.code as fcode from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.main_supplier=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr1!="") $qry1   .=" where $whr $whr1 $confirmEnable";

		$qry2	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as selectDate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no as wtChellan, a.landing_center, a.payment, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values as cntValue, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt as eftwt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, a.confirm, a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no),c.name as supplierName,d.name as Name,e.code as code,f.code as fcode from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.payment=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		
		if ($whr2!="") $qry2   .=" where $whr $whr2 $confirmEnable";

		$qry="select * from ($qry1 union all $qry2) dum";
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		
		//echo "<br>$qry<br>";

		/*
		if ($landingCenterId!="") $whr .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr .= " and a.main_supplier='".$selectSupplier."'";
		//if ($selChallanNo!="") $whr .= " and a.weighment_challan_no='".$selChallanNo."'";
		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";				
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		if ($selPaid!="") $whr .= " and b.paid='".$selPaid."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		$orderBy = "a.select_date asc, a.weighment_challan_no asc, c.name asc, d.name asc, e.code asc, b.count_values asc, f.code asc, b.effective_wt asc";
		$qry	= "select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id, a.select_time, b.grade_count_adj, a.confirm, a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.main_supplier=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		//echo "<br>$qry<br>";	*/
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Group Dailycatch entry Items
	function getCatchEntrySummaryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $offset, $limit, $rmConfirmed, $billingCompany)
	{
		$whr1=""; $whr2="";

		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		if ($paidStatus=='Y') {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid='Y' $confirmEnable";
		} else if ($paidStatus=='N') {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid<>'Y' $confirmEnable";
		} else {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null $confirmEnable";
		}

		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		if ($selPaid!="") $whr .= " and b.paid='".$selPaid."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") 
		{	
			$whr1 .= " and a.main_supplier='".$selectSupplier."'";
			$whr2 .= " and a.payment='".$selectSupplier."'";
		}

					
		//$groupBy = " b.fish, b.fish_code, b.count_values, b.grade_id, (if (b.select_rate!='' or b.select_rate!=0, b.select_rate,''))";
		//$orderBy = " a.select_date asc, a.weighment_challan_no asc, c.name asc, d.name asc, e.code asc, b.count_values asc, f.code asc, b.effective_wt asc";

		$groupBy = "fish,fishCode,cntvalue,grade, (if (selRate!='' or selRate!=0, selRate,''))";
		
		$orderBy = " seldate asc, weightChallan asc, supplierName asc,fishName asc, processCode asc, cntvalue asc, gradeCode asc, eftWt asc";
			
		$limit	= " ".$offset.", ".$limit."";
			
		$qry1	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as seldate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no as weightChallan, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish as fish, b.fish_code as fishCode, b.count_values as cntvalue, b.average, b.basket_wt, sum(b.local_quantity), sum(b.wastage), sum(b.soft), b.reason, sum(b.adjust), b.good, b.peeling, b.remarks, b.gross, b.total_basket, sum(b.net_wt), b.actual_wt, sum(b.effective_wt), b.decl_wt, b.decl_count, a.flag, sum(b.select_weight), sum(b.select_rate) as selRate, sum(b.actual_amount), b.paid, b.settlement_date, b.grade_id as grade, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time, sum(b.grade_count_adj), count(*), a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no),c.name as supplierName,d.name as fishName,e.code as processCode,f.code as gradeCode,b.effective_wt as eftWt from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.main_supplier=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr1!="") $qry1   .=" where $whr $whr1 $confirmEnable";

		$qry2	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as seldate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no  as weightChallan, a.landing_center, a.payment, b.ice_wt, a.sub_supplier, b.fish as fish, b.fish_code as fishCode, b.count_values as cntvalue, b.average, b.basket_wt, sum(b.local_quantity), sum(b.wastage), sum(b.soft), b.reason, sum(b.adjust), b.good, b.peeling, b.remarks, b.gross, b.total_basket, sum(b.net_wt), b.actual_wt, sum(b.effective_wt), b.decl_wt, b.decl_count, a.flag, sum(b.select_weight), sum(b.select_rate) as selRate, sum(b.actual_amount), b.paid, b.settlement_date, b.grade_id as grade, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time, sum(b.grade_count_adj), count(*), a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no),c.name as supplierName,d.name as fishName,e.code as processCode,f.code as gradeCode,b.effective_wt as eftWt from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.payment=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr2!="") $qry2   .=" where $whr $whr2 $confirmEnable";
		
		
		$qry="select * from ($qry1 union all $qry2) dum";
		if($groupBy!="") $qry 	.= " group by ". $groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		if ($limit!="") $qry   .=" limit ".$limit;
		//echo "<br>Summary==><br>$qry <br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Get all Group Dailycatch entry Items
	function getAllCatchEntrySummaryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $rmConfirmed, $billingCompany)
	{
		$whr1=""; $whr2="";
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		if ($paidStatus=='Y') {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid='Y' $confirmEnable";
		} else if ($paidStatus=='N') {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null and b.paid<>'Y' $confirmEnable";
		} else {
			$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.weighment_challan_no is not null $confirmEnable";
		}

		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		if ($selPaid!="") $whr .= " and b.paid='".$selPaid."'";
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";

		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") 
		{	
			$whr1 .= " and a.main_supplier='".$selectSupplier."'";
			$whr2 .= " and a.payment='".$selectSupplier."'";
		}

		$groupBy = "fish,fishCode,cntvalue,grade, (if (selRate!='' or selRate!=0, selRate,''))";
		$orderBy = " seldate asc, weightChallan asc, supplierName asc,fishName asc, processCode asc, cntvalue asc, gradeCode asc, eftWt asc";
			
		$limit	= " ".$offset.", ".$limit."";
			
		$qry1	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as seldate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no as weightChallan, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish as fish, b.fish_code as fishCode, b.count_values as cntvalue, b.average, b.basket_wt, sum(b.local_quantity), sum(b.wastage), sum(b.soft), b.reason, sum(b.adjust), b.good, b.peeling, b.remarks, b.gross, b.total_basket, sum(b.net_wt), b.actual_wt, sum(b.effective_wt), b.decl_wt, b.decl_count, a.flag, sum(b.select_weight), sum(b.select_rate) as selRate, sum(b.actual_amount), b.paid, b.settlement_date, b.grade_id as grade, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time, sum(b.grade_count_adj), count(*), a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no),c.name as supplierName,d.name as fishName,e.code as processCode,f.code as gradeCode,b.effective_wt as eftWt from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.main_supplier=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr1!="") $qry1   .=" where $whr $whr1 $confirmEnable";

		$qry2	= "select a.id as mainid, a.unit, a.entry_date, a.select_date as seldate, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no  as weightChallan, a.landing_center, a.payment, b.ice_wt, a.sub_supplier, b.fish as fish, b.fish_code as fishCode, b.count_values as cntvalue, b.average, b.basket_wt, sum(b.local_quantity), sum(b.wastage), sum(b.soft), b.reason, sum(b.adjust), b.good, b.peeling, b.remarks, b.gross, b.total_basket, sum(b.net_wt), b.actual_wt, sum(b.effective_wt), b.decl_wt, b.decl_count, a.flag, sum(b.select_weight), sum(b.select_rate) as selRate, sum(b.actual_amount), b.paid, b.settlement_date, b.grade_id as grade, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time, sum(b.grade_count_adj), count(*), a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no),c.name as supplierName,d.name as fishName,e.code as processCode,f.code as gradeCode,b.effective_wt as eftWt from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.payment=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr2!="") $qry2   .=" where $whr $whr2 $confirmEnable";
		
		
		$qry="select * from ($qry1 union all $qry2) dum";
		if($groupBy!="") $qry 	.= " group by ". $groupBy;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		/*
		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";		
		if ($fishId!="") $whr .= " and b.fish=".$fishId;				
		if ($processId!="") $whr .= " and b.fish_code='".$processId."'";				
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		if ($selPaid!="") $whr .= " and b.paid='".$selPaid."'";		
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";	
		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") 
		{
			 $whr1 .= " and a.main_supplier='".$selectSupplier."'";
			 $whr2 .= " and a.payment='".$selectSupplier."'";
		}
		$groupBy = " b.fish, b.fish_code,b.count_values,b.grade_id, (if (b.select_rate!='' or b.select_rate!=0, b.select_rate,''))";
		$orderBy	=	" a.select_date asc, a.weighment_challan_no asc, c.name asc, d.name asc, e.code asc, b.count_values asc, f.code asc";
		$qry	=	"select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, sum(b.local_quantity), sum(b.wastage), sum(b.soft), b.reason, sum(b.adjust), b.good, b.peeling, b.remarks, b.gross, b.total_basket, sum(b.net_wt), b.actual_wt, sum(b.effective_wt), b.decl_wt, b.decl_count, a.flag, sum(b.select_weight), sum(b.select_rate), sum(b.actual_amount), b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time, sum(b.grade_count_adj), count(*), a.payment_confirm, b.re_settled_date, CONCAT(a.alpha_code,'',a.weighment_challan_no) from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id join supplier c on a.main_supplier=c.id join m_fish d on b.fish=d.id join m_processcode e on b.fish_code=e.id left join m_grade f on b.grade_id=f.id";
		if ($whr!="" || $whr1!="") 		$qry .= " where $whr $whr1 ";
		if ($whr!="" || $whr2!="")		$qry .= " or $whr $whr2";
		if ($groupBy!="") 	$qry .= " group by ". $groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		*/
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find Daily Rate
	function findDailyRate($fishId, $processCodeId, $gradeId, $countAverage, $selLandingCenterId, $supplier, $entryDate)
	{
		$whr = "a.id=b.main_id and a.fish_id='$fishId' and a.processcode_id='$processCodeId' ";
		
		if ($countAverage!=0) $whr .= " and (b.count_avg='$countAverage' or b.high_count is not null or b.low_count is not null)";
		else if ($gradeId!="")  $whr .= " and b.grade_id='$gradeId' ";
		else $whr .= "";

		if ($selLandingCenterId!="") $whr .= " and (a.center_id='$selLandingCenterId' || a.center_id=0)";
		if ($supplier!="") $whr .= " and (a.supplier_id='$supplier' || a.supplier_id=0)";
		if ($entryDate) $whr .= " and a.date='$entryDate' ";
		
		$orderBy	= " a.center_id desc, a.supplier_id desc";

		$qry = " select  a.id, a.fish_id, b.grade_id, a.center_id, a.date, a.supplier_id, b.market_rate, b.decl_rate, b.count_avg, b.high_count, b.low_count from t_dailyrates a, t_dailyrates_entry b ";
		if ($whr!="") 		$qry   .= " where ".$whr;		
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
		//echo $qry."<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	/* Modified on 26-09-08	
	function findDailyRate($fishId)
	{
		$qry = "select  a.id, a.fish_id, a.grade_id, a.center_id, a.date, a.supplier_id, a.marketrate, a.decrate, a.count,b.name  from t_dailyrates a left join m_fish b on a.fish_Id = b.id where a.fish_id=$fishId";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	*/

	#Update t_dailycatchentry table
	function updateCatchEntryActualAmount($catchEntryId, $selectWeight, $selectRate, $actualAmount, $paid, $suppSetldDate)
	{
		# If Already Settled then Checking the Old Entry
		$setldLog = "";
		$valueChanged = false;
		if ($paid=='Y') {
			$cDate = date("Y-m-d");
			list($isSetld, $selWt, $selRate, $sHistory, $sDate) = $this->chkCatchEntrySettled($catchEntryId);
			# On Resettlig Date Wt => Resettld Date-Old Wt:Old Rate
			$setldLog = $cDate.":".$selWt.":".$selRate;
			$logHistory = "";
			if ($sHistory!="") {
				$logHistory = $sHistory.",".$setldLog;
			} else {
				$logHistory = $setldLog;
			}
			if ($selectRate!=$selRate || $selectWeight!=$selWt) $valueChanged = true;		
		}

		$qry	= "update t_dailycatchentry set select_weight='$selectWeight', select_rate='$selectRate', actual_amount='$actualAmount'";

		if ($paid=='Y' && $isSetld=='Y' && $valueChanged) {
			$qry .= " , paid='$paid', re_settled_date=Now(), settled_history='$logHistory'";
		} else if ($paid=='Y' && $isSetld=='N') {
			//$qry .= " , paid='$paid', settlement_date=Now()";		
			$qry .= " , paid='$paid', settlement_date='$suppSetldDate' ";		
		} else if ($paid=='N') {
			$qry .= " , paid='$paid', settlement_date='0000-00-00'";		
		} else {
			$qry .="";
		}
		
		$qry .= "  where id='$catchEntryId' ";	
		
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);		
		if ($result) $this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();		
		return $result;	
	}

	# Fetch all record based on Challan No
	function filterCatchEntryChallanRecords($selChallanNo)
	{
		$qry	=	"select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time from t_dailycatch_main a, t_dailycatchentry b where weighment_challan_no='$selChallanNo' and a.id=b.main_id and weighment_challan_no is not null";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Filter all Records based on the settled date, supplier and landing center
	function filterCatchEntryRecords($paidDate, $selectSupplier, $landingCenterId)
	{
		$qry	=	"select a.id, a.unit, a.entry_date, a.select_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, b.ice_wt, a.sub_supplier, b.fish, b.fish_code, b.count_values, b.average, b.basket_wt, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.gross, b.total_basket, b.net_wt, b.actual_wt, b.effective_wt, b.decl_wt, b.decl_count, a.flag, b.select_weight, b.select_rate, b.actual_amount, b.paid, b.settlement_date, b.grade_id, b.reason_local, b.reason_wastage, b.reason_soft, b.entry_option, b.id,a.select_time from t_dailycatch_main a, t_dailycatchentry b where b.settlement_date='$paidDate' and a.main_supplier='$selectSupplier' and a.landing_center='$landingCenterId' and a.id=b.main_id";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Summary of Fish-Process Code - garde/Count Supplier Weighment Records
	function getSupplierDeclaredWtRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $fishId, $processId, $paidStatus, $rmConfirmed, $dateSelectFrom, $billingCompany)
	{
		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
				
		} else {
			$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
		}

		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		if ($paidStatus=='Y') {
			$whr		=	"$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and e.settled='Y' $confirmEnable" ;
		} else if ($paidStatus=='N') {
			$whr		=	"$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and e.settled<>'Y' $confirmEnable" ;
		} else {
			$whr		=	"$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id $confirmEnable" ;
		}	
		
		if ($landingCenterId==0) $whr .= "";
		else $whr .= " and a.landing_center=".$landingCenterId;
				
		if ($selectSupplier==0) $whr .= "";
		else $whr .= " and a.main_supplier=".$selectSupplier;
		
		//$whr .= " and a.weighment_challan_no='".$selChallanNo."'";		
		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";		
			
		if ($fishId=="") $whr .= "";
		else $whr .= " and b.fish=".$fishId;
				
		if ($processId=="") $whr .= "";
		else $whr .= " and b.fish_code=".$processId;

		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$groupBy	=	"b.fish, b.fish_code , e.decl_count , (if (e.rate!='' or e.rate!=0, e.rate,''))";		
		$orderBy	=	"c.name asc, d.code asc, e.decl_count asc ";		
		
		$qry		=	"select b.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, sum(e.decl_wt),e.id,e.rate, e.settled, e.settled_date, e.re_settled_date from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="")		$qry .= " where ".$whr;		
		if ($groupBy!="") 	$qry .= " group by ". $groupBy;			
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;	
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#For Detailed Supplier Weighment Records
	function getDetailedSupplierDeclaredWtRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $fishId, $processId, $paidStatus, $rmConfirmed, $dateSelectFrom, $billingCompany)
	{
		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		} else {
			$dateSelection = "e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."'";
		}
		
		if ($rmConfirmed) $confirmEnable = "and a.confirm=1";
		else 		 $confirmEnable = "";

		if ($paidStatus=='Y') {
			$whr	=	"$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and e.settled='Y' $confirmEnable" ;
		} else if ($paidStatus=='N') {
			$whr	=	"$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and e.settled<>'Y' $confirmEnable" ;
		} else {
			$whr	=	"$dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id $confirmEnable" ;
		}
		
		if ($landingCenterId==0) $whr .= "";
		else $whr .= " and a.landing_center=".$landingCenterId;
		
		if ($selectSupplier==0) $whr .= "";
		else $whr .= " and a.main_supplier=".$selectSupplier;
		
		//if ($selChallanNo!="") $whr .= " and a.weighment_challan_no='".$selChallanNo."'";
		if ($selChallanNo!="") $whr .= " and a.id='".$selChallanNo."'";
				
		if ($fishId=="") $whr .= "";
		else $whr .= " and b.fish=".$fishId;
		
		if ($processId=="") $whr .= "";
		else $whr .= " and b.fish_code=".$processId;

		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$orderBy	= "e.supplier_challan_no asc, c.name asc, d.code asc, e.decl_count asc ";
		
		$qry		= "select b.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, e.decl_wt, e.id, e.rate, e.settled, e.settled_date, e.re_settled_date from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") $qry   .=" where ".$whr;		
		if ($groupBy!="") $qry 	.= " group by ". $groupBy;			
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
			
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Declared Records
	function getDeclaredGroupRecs($fromDate, $tillDate, $selectSupplier, $sFishId, $processCodeId, $declCount, $dateSelectFrom, $rateSetld, $billingCompany)
	{
		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' ";	
		} else {
			$dateSelection = " e.supplier_challan_date>='".$fromDate."' and e.supplier_challan_date<='".$tillDate."' ";
		}

		$whr 	= " $dateSelection and a.id=b.main_id and b.id=e.entry_id and a.weighment_challan_no is not null and b.fish=c.id and b.fish_code=d.id and a.main_supplier='".$selectSupplier."' and b.fish='".$sFishId."' and b.fish_code='".$processCodeId."' and e.decl_count='".$declCount."' ";

		if ($rateSetld!=0) $whr .= " and e.rate='$rateSetld' ";
		else $whr .= " and (e.rate is null or e.rate=0)";
		//and (if (e.rate!='' or e.rate!=0, e.rate='$rateSetld', e.rate='' or e.rate=0))
		
		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
					
		$qry		= "select a.id, b.fish, b.fish_code, b.count_values, b.grade_id, b.effective_wt, b.received_by, e.supplier_challan_no, e.supplier_challan_date, e.decl_wt, e.decl_count, c.name, d.code, e.decl_wt, e.id, b.id from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d, t_dailycatch_declared e";
		
		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($groupBy!="") 	$qry .= " group by ". $groupBy;			
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;			
		//echo "<br>$declCount<=>$qry<br>";				
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Get Weighment Challan Records
	function getWeighmentChallanGroupRecs($fromDate, $tillDate, $fishId, $processCodeId, $gradeId, $count, $selectSupplier, $rateSetld, $billingCompany)
	{
		$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."' and b.fish_code='".$processCodeId."' and b.grade_id='".$gradeId."'";
			
		if ($count=="") $whr .= "";
		else $whr .= " and b.count_values='".$count."'";
		
		if ($selectSupplier=="") $whr .= "";
		else $whr .= " and a.main_supplier='".$selectSupplier."'";

		if ($rateSetld!=0) $whr .= " and b.select_rate='$rateSetld' ";
		else $whr .= " and (b.select_rate is null or b.select_rate=0)";

		if ($billingCompany!="") $whr .= " and a.billing_company_id='".$billingCompany."'";
		
		$qry		= "select b.id, b.effective_wt from t_dailycatch_main a, t_dailycatchentry b";
	
		if ($whr!="") $qry   .=" where ".$whr;					
		if ($orderBy!="") $qry   .=" order by ".$orderBy;			
		//echo "WC-Group==>".$qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Update Declared Rec
	function updateDeclaredRec($declaredId, $rate, $settled, $suppSetldDate)
	{
		# If Already Settled then Checking the Old Entry
		$setldLog = "";
		$rateChanged = false;
		if ($settled=='Y') {
			$cDate = date("Y-m-d");
			list($isSetld, $selRate, $sHistory, $sDate) = $this->chkDeclaredEntrySettled($declaredId);
			# On Resettlig Date Wt => Resettld Date-Old Wt:Old Rate
			$setldLog = $cDate.":".$selRate;
			$logHistory = "";
			if ($sHistory!="") {
				$logHistory = $sHistory.",".$setldLog;
			} else {
				$logHistory = $setldLog;
			}
			if ($rate!=$selRate) $rateChanged = true;
		}

		$qry	=	"update t_dailycatch_declared set rate='$rate'";
		if ($settled=='Y' && $isSetld=='Y' && $rateChanged) {
			$qry .= " , settled='$settled', re_settled_date=Now(), settled_history='$logHistory'";
		} else if ($settled=='Y' && $isSetld=='N') {
			//$qry .= " , settled='$settled', settled_date=Now()";		
			$qry .= " , settled='$settled', settled_date='$suppSetldDate'";
		} else if ($settled=='N') {
			$qry .= " , settled='$settled', settled_date='0000-00-00'";		
		} else {
			$qry .="";
		}
		
		$qry .= "  where id='$declaredId' ";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Check Supplier Declared Wt Settled 
	function challanRecords($challanEntryId)
	{
		$notSettled = 0;

		$qry	= "select settled from t_dailycatch_declared where entry_id='$challanEntryId'";
		//echo "$challanEntryId==>".$qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);		
		foreach ($result as $cr) {
			$settled = $cr[0];
			if ($settled=='N') {
				$notSettled++;
			}
		}
		return ($notSettled!="")?true:false;
	}


	#Calculate Supplier Actual amount if Declared Wt
	function calcSupplierActualAmount($challanEntryId)
	{
		$qry	=	"select decl_wt, rate from t_dailycatch_declared where entry_id='$challanEntryId'";
		
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$totalAmount = 0;
		foreach ($result as $amr) {
			$declWt = $amr[0];
			$rate   = $amr[1];			
			$actualRate = $declWt * $rate;
			$totalAmount +=$actualRate;
		}
		return ($totalAmount!="")?$totalAmount:0;
	}

	#update Daily Catch Entry Table 
	function updateDailyCatchEntry($challanEntryId, $paid, $supplierActualAmount, $suppSetldDate)
	{
		$isSettled = ($paid=="")?N:$paid;
		
		if ($isSettled=='Y') {
			//$settlementDate = "Now()";
			$settlementDate = $suppSetldDate;
		} else {
			$settlementDate = "0000-00-00";
			$supplierActualAmount = 0;
		}
		
		$qry	= "update t_dailycatchentry set paid='$isSettled', settlement_date='$settlementDate' , actual_amount = '$supplierActualAmount' where id='$challanEntryId' ";
		//echo "<br>$qry<br>";
		$result	=	$this->databaseConnect->updateRecord($qry);
		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	// ----------------------
	// Checking the Catch Entry Already Settled
	// Return Paid, Select Wt, Select Rate
	// ----------------------
	function chkCatchEntrySettled($catchEntryId)
	{
		$qry = "select paid, select_weight, select_rate, settled_history, settlement_date from t_dailycatchentry where id='$catchEntryId' ";	
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4]):"";
	}
	// ----------------------
	// Checking the Catch Declared Entry Already Settled
	// Return Paid, Select Rate, Settled date
	// ----------------------
	function chkDeclaredEntrySettled($declEntryId)
	{
		$qry = "select settled, rate, settled_history, settled_date from t_dailycatch_declared where id='$declEntryId' ";	
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3]):"";		
	}

	# Find the average of comma seperated numbers
	function calcCountAverage($declCount)
	{
		$cArray = explode(",",$declCount);
		$sum=0;
		$average=0;
		for ($i = 0; $i<=sizeof($cArray); $i++) {
			$sum += $cArray[$i];
		}
		$average = ceil($sum/sizeof($cArray));
		
		if ($average) {
			return $average;
		}	
	}


	# Get Last Paid Rate
	function getLastPaidRate($fromDate, $selLandingCenterId, $supplier, $fishId, $processCodeId, $count, $countAverage, $gradeId, $viewType)
	{
		//echo $fromDate; 02/01/2009 > 2009-01-02
		$dateC	   =	explode("-", $fromDate);
		$selDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[2]-1,$dateC[0]));

		$whr = " a.id=b.main_id and a.select_date<'$selDate' and b.paid='Y' and b.fish='$fishId' and b.fish_code='$processCodeId'";

		if ($viewType=='DT' && $selLandingCenterId!="") {
				$whr .= " and a.landing_center='$selLandingCenterId' ";
		} else $whr .= ""; 

		if ($viewType=='DT' && $supplier!="") {
				$whr .= " and a.main_supplier='$supplier' ";
		}

		if ($count!="") $whr .= " and b.average = '$countAverage' "; 
		else $whr .= "";	

		if ($count=="" && $gradeId!="") $whr .= " and b.grade_id='$gradeId' ";
		else $whr .= "";

		$limit = "1";		

		$qry = " select b.select_rate from t_dailycatch_main a, t_dailycatchentry b ";

		if ($whr!="") 	$qry .= " where ".$whr;
		if ($limit!="") $qry .= " limit ".$limit;

		//echo "<br>$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	#Fetch Challan billing company Records
	function fetchBillingCompanyRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $settlementDate, $dateSelectFrom)
	{	

		$dateSelection = "";
		if ($dateSelectFrom=='WCD') {
			$dateSelection = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		} else {
			$dateSelection = "c.supplier_challan_date>='".$fromDate."' and c.supplier_challan_date<='".$tillDate."'";

			$tableJoin = " and b.id=c.entry_id";
			$tableName = " , t_dailycatch_declared c ";
		}


		$whr1	= "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and a.billing_company_id=bc.id $tableJoin";
		if ($landingCenterId!="") $whr1 .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr1 .= " and a.main_supplier='".$selectSupplier."'";
		if ($settlementDate!="") $whr1 .= " and b.settlement_date='".$settlementDate."'";	
		
		$whr2	= "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and a.billing_company_id=bc.id $tableJoin";
		if ($selectSupplier!="") $whr2 .= " and a.payment='".$selectSupplier."'";
		if ($settlementDate!="") $whr2 .= " and b.settlement_date='".$settlementDate."'";

		$orderBy	=	"bc.display_name";
		
		$qry	= "select distinct bc.id, bc.display_name from t_dailycatch_main a, t_dailycatchentry b, m_billing_company bc $tableName";
		if ($whr1!="") $qry   .=" where $whr1 or $whr2";
		
		if ($orderBy!="") $qry   .=" order by ".$orderBy;

		/*$whr	= "$dateSelection and a.id=b.main_id and a.weighment_challan_no is not null and a.billing_company_id=bc.id $tableJoin";
		
		if ($landingCenterId!="") $whr .= " and a.landing_center='".$landingCenterId."'";
		if ($selectSupplier!="") $whr .= " and a.main_supplier='".$selectSupplier."'";
		if ($settlementDate!="") $whr .= " and b.settlement_date='".$settlementDate."'";		
		$orderBy	=	"bc.display_name";
		
		$qry	= "select distinct bc.id, bc.display_name from t_dailycatch_main a, t_dailycatchentry b, m_billing_company bc $tableName";
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;*/
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

}	
?>