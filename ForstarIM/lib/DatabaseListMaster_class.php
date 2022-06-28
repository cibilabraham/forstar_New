<?php
class DatabaseListMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Billing Company Master ($billingCompanyObj)
	*****************************************************************/
	var $databaseConnect;	

	//Constructor, which will create a db instance for this class
	function DatabaseListMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry = "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,active,(select count(a1.id) from t_dailycatch_main a1 where billing_company_id=a.id) as tot,dr_status from m_billing_company a order by name asc limit $offset, $limit";
		$qry = "select Id, Db_filename, size, date_on, created_by FROM m_dbbackup_history order by ID desc limit $offset, $limit";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		//$qry	= "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,active,(select count(a1.id) from t_dailycatch_main a1 where billing_company_id=a.id) as tot,dr_status from m_billing_company a order by name asc";
		$qry = "select Id, Db_filename, size, date_on, created_by FROM m_dbbackup_history order by ID desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete 
	function deleteDBDetails($Id)
	{
		$qry	=	" delete from m_dbbackup_history where id='$Id'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

# Select 
	function findDbFile($Id)
	{
		$qry	=	"Select Db_filename from m_dbbackup_history where id='$Id'";
		//$qry = "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,vat_tin,cst_tin,notification_details,ti_range,ti_division,commissionerate,excise_no,pan_no,eic_approval_no,active,dr_status from m_billing_company where id=$billingCompanyId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	

	# Insert A Rec
	function adddbbackup($Db_filename, $size, $date_on, $created_by)
	{		
		$qry = "insert into m_dbbackup_history (Db_filename, size, date_on, created_by) values('$Db_filename', '$size', '$date_on', '$created_by') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	


}
?>