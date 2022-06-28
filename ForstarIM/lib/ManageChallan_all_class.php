<?php
class ManageChallanAll
{  
	/****************************************************************
	This class deals with all the operations relating to Manage Challan
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function ManageChallanAll(&$databaseConnect)
    	{
       		 $this->databaseConnect =&$databaseConnect;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit, $filterFunctionType,$filterUnitName,$filterCompanyName,$filterYear)
	{
		//$cDate = date("Y-m-d");
		
		$whr = "";
		if ($filterFunctionType!="") $whr .= " type='$filterFunctionType' ";
		else if ($filterUnitName!="") $whr .= " unitid='$filterUnitName' ";
		else if ($filterCompanyName!="") $whr .= " billing_company_id='$filterCompanyName' ";
		else if ($filterYear!="") $whr .= " year(start_date)='$filterYear' or year(end_date)='$filterYear'";
		//else 
			//$whr .= " (('$cDate'>=start_date && (end_date is null || end_date=0)) or ('$cDate'>=start_date and '$cDate'<=end_date) or (start_date is null and end_date is null) ) ";
		
		//$orderBy = " type asc";
		$orderBy = " type asc,start_date desc";
		$limit	 = " $offset,$limit";
	

		$qry	= " select ng.id, type, start_date, end_date, start_no, end_no, current_no, generate,ng.active, billing_company_id, dentry_limit_days, so_invoice_type, exporter_id,name,alpha_code,auto_generate from number_gen ng left join m_plant mp on ng.unitid=mp.id";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit)		$qry .= " limit ".$limit ;
		//echo $qry;
		//echo("<br><br>");
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords($filterFunctionType,$filterUnitName,$filterCompanyName,$filterYear)
	{
		$cDate = date("Y-m-d");

		$whr = "";
		if ($filterFunctionType!="") $whr .= " type='$filterFunctionType' ";
		else if ($filterUnitName!="") $whr .= " unitid='$filterUnitName' ";
		else if ($filterCompanyName!="") $whr .= " billing_company_id='$filterCompanyName' ";
		else if ($filterYear!="") $whr .= " year(start_date)='$filterYear' or year(end_date)='$filterYear'";
		//else $whr .= " (('$cDate'>=start_date && (end_date is null || end_date=0)) or ('$cDate'>=start_date and '$cDate'<=end_date) or (start_date is null and end_date is null) ) ";
		
		//$orderBy = " type asc";
		$orderBy = " type asc,start_date desc";
	
		
		$qry	= " select id, type, start_date, end_date, start_no, end_no, current_no, generate, active, billing_company_id, dentry_limit_days, so_invoice_type,alpha_code from number_gen";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Distinct Year
	function fetchdistinctYear(){
		$qry	= " select distinct year(start_date) from number_gen order by id desc;";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
		
	
	}
	
	#Checking the Selected Challan is cancelled
	function checkCancelled($challanNo, $billingCompany)
	{
		$qry	=	"select challan_no from s_cancelled_challan where challan_no='$challanNo' and billing_company_id='$billingCompany' ";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;
	}

}