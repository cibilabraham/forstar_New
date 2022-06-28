<?php
class ManageChallan
{  
	/****************************************************************
	This class deals with all the operations relating to Manage Challan
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function ManageChallan(&$databaseConnect)
    	{
       		 $this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addIdGenRec($functionType, $billingCompany, $idDateFrom, $idDateTo, $startNo, $endNo, $dEntryLimitDays, $soInvoiceType, $exporter,$unitid,$alpha_code_prefix,$auto_Generate)
	{
		$qry = "insert into number_gen(type, billing_company_id, start_date, end_date, start_no, end_no, generate, active, created_on, dentry_limit_days, so_invoice_type, exporter_id,unitid,alpha_code,auto_generate) values('$functionType', '$billingCompany', '$idDateFrom', '$idDateTo', '$startNo', '$endNo', 'N', 'Y', NOW(), '$dEntryLimitDays', '$soInvoiceType', '$exporter','$unitid','$alpha_code_prefix','$auto_Generate')";
		//echo $qry;
		//die;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit, $filterFunctionType,$filterUnitName,$filterCompanyName)
	{
		$cDate = date("Y-m-d");
		
		$whr = "";
		if ($filterFunctionType!="") $whr .= " type='$filterFunctionType' ";
		else if ($filterUnitName!="") $whr .= " unitid='$filterUnitName' ";
		else if ($filterCompanyName!="") $whr .= " billing_company_id='$filterCompanyName' ";
		else $whr .= " (('$cDate'>=start_date && (end_date is null || end_date=0)) or ('$cDate'>=start_date and '$cDate'<=end_date) or (start_date is null and end_date is null) ) ";
		
		//$orderBy = " type asc";
		$orderBy = " type asc,start_date desc";
		$limit	 = " $offset,$limit";
	

		$qry	= " select ng.id, type, start_date, end_date, start_no, end_no, current_no, generate,ng.active, billing_company_id, dentry_limit_days, so_invoice_type, exporter_id,name,alpha_code,auto_generate from number_gen ng left join m_plant mp on ng.unitid=mp.id";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit)		$qry .= " limit ".$limit ;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	/**
	* Check Alpha code Exist
	*/
	function checkAlphaCodeExist($alphaNo, $editMainId)
	{

		$qry = " select id from number_gen where alpha_code='$alphaNo'";
		if ($editMainId!="") $qry .= " and id!=$editMainId";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	
	function checkUnitExist($function,$company,$unitId)
	{

		$qry = "SELECT id FROM `number_gen` WHERE `type`='$function' and billing_company_id='$company' and challan_status='0'";
		if ($unitId!="") $qry .= " and unitid=$unitId";
		//echo  $qry;
		 //and `unitid`='$unitId'
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}
	function CheckExistance($functionType,$billingCompany,$startdt,$enddt,$unitid,$soInvoiceType)
	{
		//$qry	= "select id from number_gen where ('$startdt'<=date_format(start_date,'%Y-%m-%d') or '$enddt'<=date_format(end_date,'%Y-%m-%d')) and type='$functionType' and billing_company_id='$billingCompany' AND challan_status!='1'";
		$qry	= "select id from number_gen where '$startdt' between start_date and end_date and type='$functionType' and billing_company_id='$billingCompany' AND challan_status!='1'";
		if ($unitid!="") $qry .= " and unitid=$unitid";
		if($soInvoiceType!="") $qry .= " and so_invoice_type='$soInvoiceType'";
		$qry.=" or '$enddt' between start_date and end_date and type='$functionType' and billing_company_id='$billingCompany' AND challan_status!='1'";
		if ($unitid!="") $qry .= " and unitid=$unitid";
		if($soInvoiceType!="") $qry .= " and so_invoice_type='$soInvoiceType'";
		//echo $qry;
		//DIE();
		return $this->databaseConnect->getRecord($qry);
	 
	}
	# Returns all Records
	function fetchAllRecords($filterFunctionType,$filterUnitName,$filterCompanyName)
	{
		$cDate = date("Y-m-d");

		$whr = "";
		if ($filterFunctionType!="") $whr .= " type='$filterFunctionType' ";
		else if ($filterUnitName!="") $whr .= " unitid='$filterUnitName' ";
		else if ($filterCompanyName!="") $whr .= " billing_company_id='$filterCompanyName' ";
		else $whr .= " (('$cDate'>=start_date && (end_date is null || end_date=0)) or ('$cDate'>=start_date and '$cDate'<=end_date) or (start_date is null and end_date is null) ) ";
		
		//$orderBy = " type asc";
		$orderBy = " type asc,start_date desc";

		$qry	= " select id, type, start_date, end_date, start_no, end_no, current_no, generate, active, billing_company_id, dentry_limit_days, so_invoice_type,alpha_code from number_gen";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record
	function find($editId)
	{
		$qry	= " select ng.id, type, billing_company_id, start_date, end_date, start_no, end_no, dentry_limit_days, so_invoice_type, exporter_id,name,unitid,alpha_code,challan_status,auto_generate from number_gen ng left join m_plant mp on ng.unitid=mp.id where ng.id='$editId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}	

	# Update  a  Record
	function updateIdGenRec($recordId, $functionType, $billingCompany, $idDateFrom, $idDateTo, $startNo, $endNo, $dEntryLimitDays, $soInvoiceType, $exporter,$unitid,$alpha_code_prefix,$disable,$auto_Generate)
	{	
		$qry = "update number_gen set type='$functionType', billing_company_id='$billingCompany', start_date='$idDateFrom', end_date='$idDateTo', start_no='$startNo', end_no='$endNo', dentry_limit_days='$dEntryLimitDays', so_invoice_type='$soInvoiceType', exporter_id='$exporter',unitid='$unitid',alpha_code='$alpha_code_prefix',challan_status='$disable',auto_generate='$auto_Generate' where id='$recordId' ";

		//$qry = "update number_gen set type='$functionType', billing_company_id='$billingCompany', start_date='$idDateFrom', end_date='$idDateTo', start_no='$startNo', end_no='$endNo', dentry_limit_days='$dEntryLimitDays', so_invoice_type='$soInvoiceType', exporter_id='$exporter',unitid='$unitid',alpha_code='$alpha_code_prefix' where id='$recordId' ";

		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	# Delete a Record
	function deleteIdGenRec($recordId)
	{
		$qry	= " delete from number_gen where id=$recordId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Get Record
	function getManangeChallanRec()
	{
		$qry	= " select id, challan_starting_no, challan_ending_no, challan_starting_date, so_dentry_limit_days, rm_challan_dentry_limit_days from c_system where id is not null ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateChallanRecord($soDEntryLimitDays, $challanDEntryLimitDays)
	{
		$qry	= " update c_system set so_dentry_limit_days='$soDEntryLimitDays', rm_challan_dentry_limit_days='$challanDEntryLimitDays' ";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}

	/*
		function updateChallanRecord($recordId, $startingNumber, $endingNumber, $selectDate, $soDEntryLimitDays, $challanDEntryLimitDays)
	{
		$qry	= " update c_system set challan_starting_no='$startingNumber', challan_ending_no='$endingNumber', challan_starting_date='$selectDate', so_dentry_limit_days='$soDEntryLimitDays', rm_challan_dentry_limit_days='$challanDEntryLimitDays' where id=$recordId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;	
	}
	*/


	#-------------------------------------------
	# here we check the seleted challan No is between starting number and Ending Number,date and Cancelled which is we set in this screen

	#Checking the entry is allowed or not
	function checkAllowedEntry($challanNo, $challanDate, $billingCompany)
	{
		/*
		$rec = $this->getManangeChallanRec();		
		$challanDEntryLimitDays = $rec[5];
		*/
		list($startingNumber, $endingNumber, $challanDEntryLimitDays) = $this->getChallanRec($challanDate, $billingCompany);
		# Get no. of Days
		$dateDiff = $this->getDateDiff($challanDate);
		$calcDiff = $dateDiff-$challanDEntryLimitDays; 
		return ( ($challanNo>=$startingNumber) && ($challanNo<=$endingNumber) && ($calcDiff<=0) && (!$this->checkCancelled($challanNo, $billingCompany)))?true:false;
	}

	#Checking the Selected Challan is cancelled
	function checkCancelled($challanNo, $billingCompany)
	{
		$qry	=	"select challan_no from s_cancelled_challan where challan_no='$challanNo' and billing_company_id='$billingCompany' ";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;
	}

	# Get Rate List based on Date
	function getChallanRec($selDate, $billingCompany)
	{	
		$qry	= "select start_no, end_no, dentry_limit_days from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}
	#-------------------------------------------	

	/*
	* Check Allowed Sales Order Entry
	*/
	function chkAllowedSOEntry($selLastDate)
	{
		/*
			$rec = $this->getManangeChallanRec();
			$soDEntryLimitDays = $rec[4];
		*/
		list($startingNumber, $endingNumber, $soDEntryLimitDays) = $this->getSOChallanRestrictionRec($selLastDate);
		
		# Get no. of Days
		$dateDiff = $this->getDateDiff($selLastDate);
		$calcDiff = $dateDiff-$soDEntryLimitDays; 
		//echo $calcDiff;
		# If Calc Diff Less than or equal to then it is a valid
		return ($calcDiff<=0)?true:false;
	}
	
	# Get Rate List based on Date
	function getSOChallanRestrictionRec($selDate)
	{	
		$qry	= "select start_no, end_no, dentry_limit_days from number_gen where type='SO' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}
	/*--------------------------------------------------------------*/

	# Get Date Diff based on Current  date
	function getDateDiff($selLastDate)
	{
		$currentDate	= date("Y-m-d");
		$cDate		= explode("-",$currentDate);
		$d2 = mktime(0,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$eDate		= explode("-", $selLastDate);		
		$d1 = mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		return $dateDiff; 
	}

	# Check the last date valid ----------------------------------------------------------
	# $ftype = Function TYPE, Date selected
	function chkValidDate($fType, $selLastDate)
	{		
		list($startingNumber, $endingNumber, $dEntryLimitDays) = $this->getChallanRestriction($fType, $selLastDate);
		# Get no. of Days
		$dateDiff = $this->getDateDiff($selLastDate);
		$calcDiff = $dateDiff-$dEntryLimitDays; 
		# If Calc Diff Less than or equal to then it is a valid
		return ($calcDiff<=0)?true:false;
	}
	
	# Get Rate List based on Date
	function getChallanRestriction($fType, $selDate)
	{	
		$qry	= "select start_no, end_no, dentry_limit_days from number_gen where type='$fType' and date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}
	# --------------------------------------------------------------------------------------


function fetchAllRecordsUnitsActiveExpId($exporter)
	{
		
	if ($exporter=="")
		{
			$qry="select unitno,mp.name,default_row from m_exporter me left join m_exporter_unit mu on me.id=mu.exporterid left join m_plant mp on mu.unitno=mp.no where default_row='Y' order by unitno asc";
		}
		else {

		$qry="select unitno,name from m_exporter_unit mu left join m_plant mp on mu.unitno=mp.id where exporterid='$exporter' order by unitno asc";
		}
		
		//$qry	=	"select *from m_exporter_unit where exporterid=$exporter order by unitno asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getUnitExporter($exporterId)
	{

		$objResponse 		= new NxajaxResponse();		
		$exporter_m		= new ExporterMaster_model();		
		$unitAlphaCode	= $exporter_m->getUnitAlphaCode($unitId,$exporterId);
		$objResponse->assign("unitalphacode", "value", $unitAlphaCode);
		return $objResponse;

	}
	
	# --------------------------------------------------------------------------------------
	#update last generated procurement Id
	function lastGeneratedProcurementId($currentNo,$id)
	{
		$qry	= "update number_gen set current_no='$currentNo'  where id='$id'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	


	
	
}