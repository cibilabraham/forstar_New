<?php
Class ChallanVerification
{

	/****************************************************************
	This class deals with all the operations relating to Challan Verification
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ChallanVerification(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	#************#
	#GetRecords based on date and supplier
	function getMissingRecords($fromDate, $tillDate, $billingCompany)
	{
		$continuousChallanNos = $this->getContinuousChallanNo($fromDate, $tillDate, $billingCompany);
		$existingChallanNos   = $this->getExistingChallanNo($fromDate, $tillDate, $billingCompany);
		$challanNosBforFromdate   = $this->getBforFromDateChallanNo($fromDate, $billingCompany);
		$challanNosAftrTilldate   = $this->getAftrTillDateChallanNo($tillDate, $billingCompany);	
		return $arr = array_diff($continuousChallanNos, $existingChallanNos, $challanNosBforFromdate, $challanNosAftrTilldate);		 
	}

	// Using Pagination
	function getPaginatedMissingRecords($fromDate, $tillDate, $offset, $limit, $billingCompany) 
	{
		$fetchAllMissingRecords = $this->getMissingRecords($fromDate, $tillDate, $billingCompany);
		return $sliceArray = array_slice($fetchAllMissingRecords,$offset,$limit);	
	}

	function getContinuousChallanNo ($fromDate, $tillDate, $billingCompany)
	{
		$qry = "select min(weighment_challan_no), max(weighment_challan_no) from t_dailycatch_main where (select_date>='$fromDate' and select_date<='$tillDate') and flag=1 and billing_company_id='$billingCompany' order by select_date asc";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		$minChallanNo = $rec[0];
		$maxChallanNo = $rec[1];
		
		if ($minChallanNo==$maxChallanNo) {
			list($minChallanNo,$endNum) = $this->getValidChallanNum($tillDate,$billingCompany);
		}

		if (!$this->chkValidCNum($tillDate, $billingCompany, $minChallanNo)) {
			list($minChallanNo,$endNum) = $this->getValidChallanNum($tillDate,$billingCompany);
		}

		$challanNo = array();
		$k=0;
		for ($i=$minChallanNo; $i<=$maxChallanNo; $i++) {
			$challanNo[$k] = $minChallanNo++;
			$k++;
		}		
		return $challanNo;
	}

	function getExistingChallanNo($fromDate, $tillDate, $billingCompany)
	{
		$qry	=	"select weighment_challan_no from t_dailycatch_main where (select_date>='$fromDate' and select_date<='$tillDate') and flag=1 and billing_company_id='$billingCompany' order by select_date asc";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		//return $result;
		$challan = array();
		$i=0;
		foreach ($result as $r) {
			$challan[$i] = $r[0];
			$i++;
		}
		return $challan;
	}

	// Before from date Challan Nos
	function getBforFromDateChallanNo($fromDate, $billingCompany)
	{
		$qry = "select weighment_challan_no from t_dailycatch_main where select_date<='$fromDate' and flag=1 and billing_company_id='$billingCompany' order by select_date asc";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		//return $result;
		$challan = array();
		$i=0;
		foreach ($result as $r) {
			$challan[$i] = $r[0];
			$i++;
		}
		return $challan;
	}

	function getAftrTillDateChallanNo($tillDate, $billingCompany)
	{
		$qry = "select weighment_challan_no from t_dailycatch_main where (select_date>='$tillDate' and select_date<=NOW()) and flag=1 and billing_company_id='$billingCompany' order by select_date asc";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		//return $result;
		$challan = array();
		$i=0;
		foreach ($result as $r) {
			$challan[$i] = $r[0];
			$i++;
		}
		return $challan;
	}
	#************#

	#insert a cancelled challan
	function cancelChallan($challanNo, $billingCompany)
	{
		$qry	= "insert into s_cancelled_challan (challan_no, billing_company_id) values ('".$challanNo."', '$billingCompany')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function checkCancelled($missingChallan, $billingCompany)
	{
		$qry	= " select challan_no from s_cancelled_challan where challan_no='$missingChallan' and billing_company_id='$billingCompany' ";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	# Get Rate List based on Date
	function getValidChallanNum($selDate, $billingCompany)
	{	
		$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array();
	}

	# Check Valid Challan Number
	function chkValidCNum($selDate, $billingCompany, $challanNum)
	{	
		$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$challanNum' and end_no>='$challanNum'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
#This screen table is linked with Mnage Challan

	# Check Challan Cancelled
	function chkChallanCancelled($missingChallan, $billingCompany)
	{
		$qry = " select id, challan_no from s_cancelled_challan where challan_no='$missingChallan' and billing_company_id='$billingCompany' ";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array(true,$result[0][0]):array();
	}

	# Change Challan Status
	function changeChallanStatus($cancelledChallanId)
	{
		$qry = " delete from s_cancelled_challan where id=$cancelledChallanId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
}	
?>