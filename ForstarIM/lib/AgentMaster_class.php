<?php
class AgentMaster
{
	/****************************************************************
	This class deals with all the operations relating to Agent Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function AgentMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	# Add a Record
	function addShippingCompany($companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos, $userId, $email, $website)
	{
		$qry = "insert into m_agent (name, address, city_id, state_id, phone_no, fax_no, phone_nos, fax_nos, created, created_by, email, website) values('$companyName', '$officeAddress', '$selCity', '$state', '$telephoneNo', '$faxNo', '$telephoneNos', '$faxNos', NOW(), '$userId', '$email', '$website')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		
		return $insertStatus;
	}

	
	# Insert (Entry) Records
	function addCompanyContact($agentId, $personName, $designation, $role, $contactNo)
	{
		$qry = "insert into m_agent_contact (main_id, person_name, designation, role, contact_no) values('$agentId', '$personName', '$designation', '$role', '$contactNo')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{		
		$whr = "";

		$orderBy = " a.name asc ";

		$limit	 = " $offset,$limit ";

		//$qry = " select a.id, a.name, b.name as city, c.name as state,a.active from m_agent a left join m_city b on a.city_id=b.id left join m_state c on a.state_id=c.id "; 
		$qry = " select a.id, a.name, b.name as city, c.name as state,a.active,(select count(a1.id) from m_agent_contact a1 where a1.main_id=a.id) as tot from m_agent a left join m_city b on a.city_id=b.id left join m_state c on a.state_id=c.id "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$whr = "";

		$orderBy = " a.name asc ";
		
		$qry = " select a.id, a.name, b.name as city, c.name as state,a.active from m_agent a left join m_city b on a.city_id=b.id left join m_state c on a.state_id=c.id "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($agentId)
	{
		$qry = "select id, name, address, city_id, state_id, phone_no, fax_no, phone_nos, fax_nos, email, website from m_agent where id=$agentId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getCompanyContactRecs($agentId)
	{
		$qry = " select a.id, a.person_name, a.designation, a.role, a.contact_no from m_agent_contact a where a.main_id='$agentId' order by a.person_name asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Update  a  Record
	function updateShippingCompany($agentId, $companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos, $email, $website)
	{		
		$qry = "update m_agent set name='$companyName', address='$officeAddress', city_id='$selCity', state_id='$state', phone_no='$telephoneNo', fax_no='$faxNo', phone_nos='$telephoneNos', fax_nos='$faxNos', email='$email', website='$website' where id=$agentId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update Entry
	function updateShipCompanyContact($shipCompanyContactId, $personName, $designation, $role, $contactNo)
	{
		$qry = " update m_agent_contact set person_name='$personName', designation='$designation', role='$role', contact_no='$contactNo' where id='$shipCompanyContactId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Entry Rec
	function delShipCompanyContactRec($shipCompanyContactId)
	{
		$qry = " delete from m_agent_contact where id=$shipCompanyContactId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Entry Table Rec
	function deleteShippingCompanyContactRec($agentId)
	{
		$qry 	= " delete from m_agent_contact where main_id=$agentId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Main Rec
	function deleteShippingCompanyRec($agentId)
	{
		$qry 	= " delete from m_agent where id=$agentId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	# Duplicate entry check
	function chkDuplicateEntry($name, $agentId)
	{
		$qry	= "select id from m_agent where name='$name'";
		if ($agentId) $qry .= " and id!='$agentId'";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Filter State List
	function filterStateList($cityId)
	{
		$qry = " select  b.id, b.name from m_city a left join m_state b on a.state_id=b.id where a.id='$cityId' order by b.name asc ";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get  All Customer Recs
	function fetchAllCustomerRecs()
	{	
		$qry	= "select id, code, customer_name from m_customer where active=1 order by customer_name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSelCustomerRecs($editId)
	{		
		$qry 	= "select a.id, a.code, a.customer_name from m_customer a left join m_agent_customer b on a.id=b.customer_id where b.agent_id='$editId' order by a.customer_name asc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;

	}

	#Add Agent 2 Customer
	function addAgent2Customer($agentId, $customers)
	{
 	 	if($customers){
			foreach ($customers as $gId){
				$customerId	=	"$gId";
				$qry	=	"insert into m_agent_customer (agent_id,customer_id) values('".$agentId."', '".$customerId."')";
				//echo $qry;
				$insertGrade	=	$this->databaseConnect->insertRecord($qry);
				if ($insertGrade) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();				
			}
		} 
 	}

	#Delete Customer
	function deleteAgent2Customer($agentId)
	{	
		# Get PC Grade Recs
		/*
		$getPC2GradeRecs = $this->getPCWiseGradeRecs($processCodeId);

		foreach ($getPC2GradeRecs as $pcg) {
			$gradeEntryId = $pcg[0];
			$pcGradeId    = $pcg[1];
			
			if (!$this->pcGradeRecInUse($processCodeId, $pcGradeId)) {
				$delPCGradeEntry = $this->delPCWiseGradeEntry($gradeEntryId);
			}
		}
		*/
		
		$qry	= "delete from m_agent_customer where agent_id='$agentId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}	

	function updateAgentconfirm($agentId)
	{
		$qry	= "update m_agent set active='1' where id=$agentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateAgentReleaseconfirm($agentId)
	{
		$qry	= "update m_agent set active='0' where id=$agentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}	
?>