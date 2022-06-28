<?php
class CompanyDetails
{  
	/****************************************************************
	This class deals with all the operations relating to Company Details 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function CompanyDetails(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Get Company Details based on id 
	function find($companyId)
	{		
		$qry	= "select id, name, address, place, pin, country, telno, faxno, vat_tin, cst_tin, ti_range, ti_division, commissionerate, excise_no, notification_details, pan_no,emailid,state,phoneno2 from m_companydetails where id=1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update Company Details
	function updateCompany($companyId, $name, $address, $place, $pinCode, $country, $telNo, $faxNo, $vatTin, $cstTin, $range, $division, $commissionRate, $exciseNo, $notificationDetails, $panNo,$emailid,$state,$phoneno2)
	{
		$qry	= " update m_companydetails set name='$name', address='$address', place='$place', pin='$pinCode', country='$country', telno='$telNo', faxno='$faxNo', vat_tin='$vatTin', cst_tin='$cstTin', ti_range='$range', ti_division='$division', commissionerate='$commissionRate', notification_details='$notificationDetails', excise_no='$exciseNo', pan_no='$panNo',emailid='$emailid',state='$state',phoneno2='$phoneno2' where id=1";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();		
		return $result;	
	}

	# Get Company Rec
	function getForstarCompanyDetails()
	{
		$qry	= "select id, name, address, place, pin, country, telno, faxno from m_companydetails";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);		
		return (sizeof($rec)>0)?array($rec[1],$rec[2],$rec[3],$rec[4],$rec[5],$rec[6],$rec[7]):"";
	}
	
	# ----------------------------------------------------- Company AC Starts Here -----------------------------------------------------------------------------
	# Add Bank AC
	function addCompanyBankAC($companyId, $accountNo, $bankName, $bankAddr, $bankADCode)
	{
		$qry = "insert into m_companydetails_bank (company_details_id, account_no, bank_name, bank_address, bank_ad_code) values ('$companyId', '$accountNo', '$bankName', '$bankAddr', '$bankADCode') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Update bank AC
	function updateCompanyBankAC($bankACEntryId, $accountNo, $bankName, $bankAddr, $bankADCode)
	{
		$qry = "update m_companydetails_bank set account_no='$accountNo', bank_name='$bankName', bank_address='$bankAddr', bank_ad_code='$bankADCode' where id='$bankACEntryId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	function chkBankAcInUse($bankACEntryId)
	{
		$qry = "select id from t_invoice_main where brc_export_bill_to='$bankACEntryId'";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Delete Bank AC
	function delBankAC($bankACEntryId)
	{
		$qry	=	" delete from m_companydetails_bank where id='$bankACEntryId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Get company bank ac
	function getCompanyBankACRecs($companyId)
	{
		$qry = "select id, account_no, bank_name, bank_address, bank_ad_code from m_companydetails_bank where company_details_id='$companyId' order by id asc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllBankACRecs()
	{
		$qry = "select id, account_no, bank_name, bank_address, bank_ad_code from m_companydetails_bank order by bank_name asc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getBankACRec($bankACEntryId)
	{
		$qry	= "select id, account_no, bank_name, bank_address, bank_ad_code from m_companydetails_bank where id='$bankACEntryId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# ----------------------------------------------------- Company AC Ends Here -----------------------------------------------------------------------------

}
?>