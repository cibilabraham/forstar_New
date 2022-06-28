<?php
class BillingCompanyMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Billing Company Master ($billingCompanyObj)
	*****************************************************************/
	var $databaseConnect;	

	//Constructor, which will create a db instance for this class
	function BillingCompanyMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert A Rec
	function addBillingCompany($companyName, $address, $place, $pinCode, $country,$userId, $alphaCode, $displayName,$vatTin,$cstTin,$range,$division,$commissionerate,$exciseNo,$notificationDetails,$panNo,$eicApprovalNo)
	{		
		$qry = "insert into m_billing_company (name, address, place, pin, country, created, createdby, alpha_code, display_name,vat_tin,cst_tin,notification_details,ti_range,ti_division,commissionerate,excise_no,pan_no,eic_approval_no,active,dr_status) values('$companyName', '$address', '$place', '$pinCode', '$country',  NOW(), '$userId', '$alphaCode', '$displayName','$vatTin','$cstTin','$notificationDetails','$range','$division','$commissionerate','$exciseNo','$panNo','$eicApprovalNo','1','Y') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	

	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,active,(select count(a1.id) from t_dailycatch_main a1 where billing_company_id=a.id) as tot,dr_status from m_billing_company a order by name asc limit $offset, $limit";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	= "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,active,(select count(a1.id) from t_dailycatch_main a1 where billing_company_id=a.id) as tot,dr_status from m_billing_company a order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsActivebillingCompany()
	{
		$qry	= "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,active from m_billing_company where active=1 order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get a Record based on Id
	function find($billingCompanyId)
	{
		$qry = "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,vat_tin,cst_tin,notification_details,ti_range,ti_division,commissionerate,excise_no,pan_no,eic_approval_no,active,dr_status from m_billing_company where id=$billingCompanyId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
		
	# Update
	function updateBillingCompany($billingCompanyId, $companyName, $address, $place, $pinCode, $country, $telNo, $faxNo, $alphaCode, $displayName,$vatTin,$cstTin,$range,$division,$commissionerate,$exciseNo,$notificationDetails,$panNo,$eicApprovalNo)
	{		
		$qry = "update m_billing_company set name='$companyName', address='$address', place='$place', pin='$pinCode', country='$country', telno='$telNo', faxno='$faxNo', alpha_code='$alphaCode', display_name='$displayName',vat_tin='$vatTin',cst_tin='$cstTin',notification_details='$notificationDetails',ti_range='$range',ti_division='$division',commissionerate='$commissionerate',excise_no='$exciseNo',pan_no='$panNo',eic_approval_no='$eicApprovalNo' where id='$billingCompanyId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete 
	function deleteBillingCompany($billingCompanyId)
	{
		$qry	=	" delete from m_billing_company where id='$billingCompanyId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	
	# Checking Billing Company Used
	function chkBillCompanyUsed($billingCompanyId)
	{
		$qry = " select id from t_dailycatch_main where billing_company_id='$billingCompanyId' union select id from m_exporter where name='$billingCompanyId'";
		//echo $qry."<br>";
		//$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	

	# Get Billing Company Rec
	# $companyName, $address, $place,$pinCode, $country, $telNo, $faxNo
	function getBillingCompanyRec($billingCompanyId)
	{
		$qry = "select id, name, address, place, pin, country, telno from m_billing_company where id=$billingCompanyId";
		//$rec = $this->find($billingCompanyId);
		$rec	= $this->databaseConnect->getRecord($qry);	
		return (sizeof($rec)>0)?array($rec[1],$rec[2],$rec[3],$rec[4],$rec[5],$rec[6],$rec[7]):"";
	}

	# Update default value for the selected rec
	function updateDefaultChk($billingCompanyId)
	{
		$qry = "update m_billing_company set default_row='Y' where id='$billingCompanyId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}
	# Update  All Rec
	function updateAllDefaultChk()
	{
		$qry = "update m_billing_company set default_row='N'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	# -------------------------------
	# Get Alpha Code (Using In daily Catch Entry)
	function getBillingCompanyAlphaCode($billingCompanyId)
	{
		$qry = "select alpha_code from m_billing_company where id='$billingCompanyId' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";		
	}

	# Get Default Billing Company
	function getDefaultBillingCompany()
	{
		$qry = "select alpha_code from m_billing_company where default_row='Y' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";	
	}
	# -------------------------------

	# Update Daily catch Entry Rec
	function updateDailyCatchEntryRec($billingCompanyId, $alphaCode)
	{
		$qry = "update t_dailycatch_main set alpha_code='$alphaCode' where billing_company_id='$billingCompanyId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Add Billing Compnay Bank AC
	function addBillingCmpnyBankAC($billingCompanyMainId, $bankName, $accountNo, $defaultAC)
	{
		$qry = "insert into m_billing_company_bank_ac (main_id, bank_name, account_no, default_ac) values ('$billingCompanyMainId', '$bankName', '$accountNo', '$defaultAC') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function addBillingCmpnyContactDetails($billingCompanyMainId,$telephoneNo, $mobileNo, $fax,$email, $defaultCD)
	{
		$qry = "insert into  m_billing_company_contact_detail (main_id,telephone_no, mobile_no,fax,email ,default_CD) values ('$billingCompanyMainId', '$telephoneNo', '$mobileNo','$fax','$email', '$defaultCD') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Get company bank ac
	function getCompanyBankACRecs($billingCompanyId)
	{
		$qry = "select id, bank_name, account_no, default_ac from m_billing_company_bank_ac where main_id='$billingCompanyId' order by id asc";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get company bank ac
	function displayContactDtls($billingCompanyId)
	{
		$qry = "select id, telephone_no, mobile_no,fax,email,default_CD from m_billing_company_contact_detail where main_id='$billingCompanyId' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Update bank AC rec
	function updateBillingCmpnyBankAC($bankACEntryId, $bankName, $accountNo, $defaultAC)
	{
		$qry = "update m_billing_company_bank_ac set bank_name='$bankName', account_no='$accountNo', default_ac='$defaultAC' where id='$bankACEntryId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Update bank AC rec
	function updateBillingCmpnyContactDetail($telephoneNo, $mobileNo, $fax,$email, $defaultCD,$contactEntryId)
	{
		$qry = "update m_billing_company_contact_detail set telephone_no='$telephoneNo', mobile_no='$mobileNo',fax='$fax',email='$email', default_CD='$defaultCD' where id='$contactEntryId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Bank AC
	function delBankACRec($bankACEntryId)
	{
		$qry	=	" delete from m_billing_company_bank_ac where id='$bankACEntryId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function delContactRec($contactID)
	{
		$qry	=	" delete from m_billing_company_contact_detail where id='$contactID'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function displayBankACDtls($billingCompanyId)
	{
		$bankACRecs = $this->getCompanyBankACRecs($billingCompanyId);
		$displayHtml = "";
		if (sizeof($bankACRecs)>0) {
			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";	
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
			$displayHtml .= "<td class=listing-head>Company Name</td>";
			$displayHtml .= "<td class=listing-head>Account No</td>";
			$displayHtml .= "<td class=listing-head>Default</td>";			
			$displayHtml .= "</tr>";
			foreach ($bankACRecs as $bcb) {
				$bankName 	= $bcb[1];
				$accountNo	= $bcb[2];
				$defaultAC	= $bcb[3];

				$displayHtml .= "<tr bgcolor=#fffbcc>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $bankName;
				$displayHtml .= "</td>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $accountNo;
				$displayHtml .=	"</td>";
				$displayHtml .= "<td class=listing-item nowrap align=center>";				
				$displayHtml .= ($defaultAC=='Y')?"<img src=\'images/y.png\' />":"";
				$displayHtml .=	"</td>";
				$displayHtml .= "</tr>";
			}
			$displayHtml  .= "</table>";
		}

		return $displayHtml;
	}

	# Delete
	function deleteBankACRecs($billingCompanyId)
	{
		$qry	= " delete from m_billing_company_bank_ac where main_id='$billingCompanyId'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	# Delete
	function deleteContactDetailRecs($billingCompanyId)
	{
		$qry	= " delete from m_billing_company_contact_detail where main_id='$billingCompanyId'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Fetch All Bank AC Recs
	function fetchAllCompanyBankACs($billingCompanyId=null)
	{
		if ($billingCompanyId) $whr = "main_id='$billingCompanyId'";

		$orderBy	= "bank_name asc";
		
		$qry = "select id, bank_name, account_no, default_ac, CONCAT(SUBSTRING_INDEX(bank_name,' ',1),' ',account_no) as displayName from m_billing_company_bank_ac ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "<br>$qry<br>";
		return $result;
	}

	
	function chkCpnyBankAcInUse($bankACEntryId)
	{
		$qry = "select id from t_distributor_ac where deposited_bank_ac_id='$bankACEntryId'";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	
	function chkBillingCpnyBankAcInUse($billingCompanyId)
	{
		$bankACRecs = $this->getCompanyBankACRecs($billingCompanyId);
		$recInUse = false;
		foreach ($bankACRecs as $bar) {
			$bankACEntryId = $bar[0];
			$bankAcInUse   = $this->chkCpnyBankAcInUse($bankACEntryId);
			if ($bankAcInUse) $recInUse = true;
		}

		return $recInUse;
	}


	function updateBillingCompanyconfirm($billingCompanyId)
	{
		$qry	= "update m_billing_company set active='1' where id=$billingCompanyId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

function updateBillingCompanyReleaseconfirm($billingCompanyId){
	$qry	= "update m_billing_company set active='0' where id=$billingCompanyId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function getDefaultAlIdBillingCompany()
	{
		$qry = "select alpha_code,id from m_billing_company where default_row='Y'";
		//echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);	
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";	
	}
	function getCompanyCurrentStatus($companyId)
	{
		$rec = $this->find($companyId);
		//printr($rec[21]);
		return $rec[21];
	}
	# Updating status Starts here
	function updateCompanyStatus($companyId, $status)
	{
//old query dated on 13 june 2018
		if($status=='Y'){
			
			$comp_st=1; 
		}else{
			$comp_st=0;
		}
		
		
		//$qry	=	" update m_billing_company set dr_status='$status' where id=$companyId";
	
		
		$qry	=	" update m_billing_company set dr_status='$status', active ='$comp_st' where id=$companyId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	
	function getCompanyDrActive()
	{
		$qry="select id,display_name from m_billing_company where dr_status='Y' order by name asc";
		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on Id
	function findContactdetail($billingCompanyId)
	{
		$qry = "select id,telephone_no,mobile_no,fax,email,default_CD from m_billing_company_contact_detail where main_id=$billingCompanyId and default_CD='Y' ";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Get an id of default billing company
	function getDefaultBillingCompanyID()
	{
		$qry = "select id from m_billing_company where default_row='Y' ";
		//echo $qry;
		 $result=$this->databaseConnect->getRecord($qry);
		 return $result[0];
	}
	
}
?>