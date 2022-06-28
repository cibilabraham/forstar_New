<?php
class Customer
{  
	/****************************************************************
	This class deals with all the operations relating to Customer
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function Customer(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	#Add
	function addCustomer($customerCode, $customerName, $officeAddress, $selCountry, $cuContactNo, $faxNo, $telephoneNos, $faxNos, $email, $website, $description, $userId)
	{
		$qry	=	"insert into m_customer (code, customer_name, address, country_id, cu_contactno, fax_no, phone_nos, fax_nos, email, website, descr, created, created_by) values('$customerCode', '$customerName', '$officeAddress', '$selCountry', '$cuContactNo', '$faxNo', '$telephoneNos', '$faxNos', '$email', '$website', '$description', NOW(), '$userId')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

function updateCustomerconfirm($customerId)
{
$qry	= "update m_customer set active='1' where id=$customerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}

function updateCustomerReleaseconfirm($customerId){
	$qry	= "update m_customer set active='0' where id=$customerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

}
	# Insert (Entry) Records
	function addCustomerContact($customerId, $personName, $designation, $role, $contactNo)
	{
		$qry = "insert into m_customer_contact (customer_id, person_name, designation, role, contact_no) values('$customerId', '$personName', '$designation', '$role', '$contactNo')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Insert (Entry) Records
	function addCustomerBrand($customerId, $brand)
	{
		$qry = "insert into m_customer_brand (customer_id, brand) values('$customerId', '$brand')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Add Customer 2 Brand
	function addCust2Brand($customerId, $brands)
	{
 	 	if($brands){
			foreach ($brands as $gId){
				$brandId	=	"$gId";
				$qry	=	"insert into m_customer_brand (customer_id,brand_id) values('".$customerId."', '".$brandId."')";
				//echo $qry;
				$insertGrade	=	$this->databaseConnect->insertRecord($qry);
				if ($insertGrade) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();				
			}
		} 
 	}

	#Add Customer 2 Shipping
	function addCust2Shipping($customerId, $shippingLines)
	{
 	 	if($shippingLines){
			foreach ($shippingLines as $gId){
				$shippingCompanyId	=	"$gId";
				$qry	=	"insert into m_customer_shipping (customer_id, shipping_company_id) values('".$customerId."', '".$shippingCompanyId."')";
				//echo $qry;
				$insert	=	$this->databaseConnect->insertRecord($qry);
				if ($insert) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();				
			}
		} 
 	}

	# Returns all Records(PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	=	"select a.id, a.code, a.customer_name, b.name as countyName, a.cu_contactno,a.active,((select COUNT(a1.id) from m_agent_customer a1 where a1.customer_id=a.id)+(select COUNT(a2.id) from t_fznpakng_quick_entry a2 where a2.customer_id=a.id)+(select count(a3.id) from t_purchaseorder_main a3 where a3.customer_id=a.id)+
				(select count(a4.id) from t_invoice_main a4 where a4.customer_id=a.id)) as tot from m_customer a left join m_country b on a.country_id=b.id order by a.customer_name asc limit $offset, $limit";
				//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	=	"select a.id, a.code, a.customer_name, b.name as countyName, a.cu_contactno,a.active from m_customer a left join m_country b on a.country_id=b.id order by a.customer_name asc";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsActiveCustomer()
	{
		$qry	=	"select a.id, a.code, a.customer_name, b.name as countyName, a.cu_contactno,a.active from m_customer a left join m_country b on a.country_id=b.id where a.active=1 order by a.customer_name asc";	
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	function getSelBrandRecs($customerId)
	{		
		$qry 	= "select id, brand from m_customer_brand where customer_id='$customerId' order by brand asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSelShippingRecs($customerId)
	{		
		$qry 	= "select a.id, a.name from m_shipping_company a left join m_customer_shipping b on a.id=b.shipping_company_id where b.customer_id='$customerId' order by a.name asc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Record  based on id
	function find($customerId)
	{
		$qry	= "select id, code, customer_name, address, country_id, cu_contactno, fax_no, phone_nos, fax_nos, email, website, descr from m_customer where id=$customerId";
		return $this->databaseConnect->getRecord($qry);
	}

	
	function getCustomerContactRecs($customerId)
	{
		$qry = " select a.id, a.person_name, a.designation, a.role, a.contact_no from m_customer_contact a where a.customer_id='$customerId' order by a.person_name asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update

	function updateCustomer($customerId, $customerName, $officeAddress, $selCountry, $cuContactNo, $faxNo, $telephoneNos, $faxNos, $email, $website, $description)
	{
		//$qry	=	" update m_customer set  code='$customerCode', customer_name='$customerName', country_id='$cuCountry', cu_contact_person='$cuContactPerson', cu_contactno='$cuContactNo', agent_name='$agName', ag_contact_person='$agContactPerson', ag_contactno='$agContactNo', descr='$description' where id=$customerId";
		//, , , , , , , , , 
		$qry	=	" update m_customer set customer_name='$customerName', address='$officeAddress', country_id='$selCountry', cu_contactno='$cuContactNo', fax_no='$faxNo', phone_nos='$telephoneNos', fax_nos='$faxNos', email='$email', website='$website', descr='$description' where id=$customerId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete 
	function deleteCustomer($customerId)
	{
		$qry	= " delete from m_customer where id=$customerId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	/*function findBrandCode($brandId)
	{
		$rec = $this->find($brandId);
		return sizeof($rec) > 0 ? $rec[2] : "";
	}*/
	
	
	function findCustomer($customerId)
	{
		$rec = $this->find($customerId);
		return sizeof($rec) > 0 ? $rec[2] : "";
	}

	# Duplicate entry check
	function chkDuplicateEntry($name, $customerId)
	{
		$qry	= "select id from m_customer where customer_name='$name'";
		if ($customerId) $qry .= " and id!='$customerId'";

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

	# Get Shipping Comapny Recs from Master
	function getShippingComapnyRecords()
	{
		$whr = "";
		$orderBy = " a.name asc ";		
		$qry = " select a.id, a.name from m_shipping_company a where active=1"; 
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Delete Customer
	function deleteCustomer2Shipping($customerId)
	{
		$qry	= " delete from m_customer_shipping where customer_id='$customerId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update Entry
	function updateCustomerContact($shipCompanyContactId, $personName, $designation, $role, $contactNo)
	{
		$qry = " update m_customer_contact set person_name='$personName', designation='$designation', role='$role', contact_no='$contactNo' where id='$shipCompanyContactId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Entry Rec
	function delCustomerContactRec($shipCompanyContactId)
	{
		$qry = " delete from m_customer_contact where id=$shipCompanyContactId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	# Update Entry
	function updateBrand($brandEntryId, $brand)
	{
		$qry = " update m_customer_brand set brand='$brand' where id='$brandEntryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Entry Rec
	function delBrandRec($brandEntryId)
	{
		$qry = " delete from m_customer_brand where id=$brandEntryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Entry Table Rec
	function deleteCustomerContactRecs($customerId)
	{
		$qry 	= " delete from m_customer_contact where customer_id=$customerId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function deleteCustomerBrandRecs($customerId)
	{
		$qry 	= " delete from m_customer_brand where customer_id=$customerId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Get Agent List
	function getAgentList($customerId)
	{
		$qry = " select ma.id, ma.name from m_agent_customer mac, m_agent ma where mac.agent_id=ma.id and mac.customer_id='$customerId' order by ma.name asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get  All Customer Recs
	function fetchAllAgentRecs()
	{	
		$qry	= "select id, name from m_agent where active=1 order by name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Add Agent 2 Customer
	function addCustomer2Agent($customerId, $agents)
	{
 	 	if($agents){
			foreach ($agents as $gId){
				$agentId	=	"$gId";				
				if (!$this->chkAgentExist($customerId, $agentId)) {
					$qry	= "insert into m_agent_customer (agent_id,customer_id) values('".$agentId."', '".$customerId."')";
					//echo $qry;
					$insertGrade	= $this->databaseConnect->insertRecord($qry);
					if ($insertGrade) $this->databaseConnect->commit();
					else $this->databaseConnect->rollback();				
				} // Entry Check Ends here 
			}
		} 
 	}

	function chkAgentExist($customerId, $agentId)
	{
		$qry	= "select id from m_agent_customer where agent_id='$agentId' and customer_id ='$customerId' ";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# -----------------------------------------------------
	# Checking Customer Id is in use (Agent Master, t_fznpakng_quick_entry, t_dailyfrozenpacking_entry, t_purchaseorder_main, t_invoice_main); t_dailythawing (Need later)
	# -----------------------------------------------------
	function customerRecInUse($customerId)
	{	
		$qry = " select id from (
				select a.id as id from m_agent_customer a where a.customer_id='$customerId'
			 union
				select a1.id as id from t_fznpakng_quick_entry a1 where a1.customer_id='$customerId'
			 union
				select a2.id as id from t_dailyfrozenpacking_entry a2 where a2.customer_id='$customerId'
			 union
				select a3.id as id from t_purchaseorder_main a3 where a3.customer_id='$customerId'
			union
				select a4.id as id from t_invoice_main a4 where a4.customer_id='$customerId'	
			) as X group by id ";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# -----------------------------------------------------
	# Checking Customer Brand Id in (t_fznpakng_quick_entry, t_dailyfrozenpacking_entry, t_purchaseorder_rm_entry) # t_dailyfrozenrepacking
	# -----------------------------------------------------
	function customerBrandRecInUse($brandEntryId)
	{	
		$qry = " select id from (
				select a.id as id from t_fznpakng_quick_entry a where a.brand_from='C' and a.brand_id='$brandEntryId'
			 union
				select a1.id as id from t_dailyfrozenpacking_entry a1 where a1.brand_from='C' and a1.brand_id='$brandEntryId'
			 union
				select a2.id as id from t_purchaseorder_rm_entry a2 where a2.brand_from='C' and a2.brand_id='$brandEntryId'
			
			) as X group by id ";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}


	# Returns all Records
	function fetchAllPaymentTermsRecs()
	{
		$qry	=	"select id, mode from m_paymentterms where active=1 order by mode asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Add Agent 2 Customer
	function addCustomer2PaymentTerm($customerId, $paymentTerms)
	{
 	 	if($paymentTerms){
			foreach ($paymentTerms as $gId){
				$paymentTermId	=	"$gId";	
	
// 				list($recExist, $custPTermEntryId, $selPTermId) = $this->chkPaymentTermExist($customerId, $paymentTermId);	
// 				echo "$recExist, $custPTermEntryId, $selPTermId<br>";
				if (!$this->chkPaymentTermExist($customerId, $paymentTermId)) {
					$qry	= "insert into m_customer_payment_terms (payment_term_id, customer_id) values('".$paymentTermId."', '".$customerId."')";
					//echo $qry;
					$insertGrade	= $this->databaseConnect->insertRecord($qry);
					if ($insertGrade) $this->databaseConnect->commit();
					else $this->databaseConnect->rollback();				
				} // Entry Check Ends here 
				/*
				else if ($recExist && !in_array($selPTermId,$paymentTerms)) {
					# Delete entry
					$delCust2PaymentTerm = $this->delCustomer2paymentTermEntry($custPTermEntryId);
				}
				*/
			}
		} 
 	}

	function chkPaymentTermExist($customerId, $paymentTermId)
	{
		$qry	= "select id, payment_term_id from m_customer_payment_terms where payment_term_id='$paymentTermId' and customer_id ='$customerId' ";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Agent List
	function getPaymentTermList($customerId)
	{
		$qry = " select ma.id, ma.mode from m_customer_payment_terms mac, m_paymentterms ma where mac.payment_term_id=ma.id and mac.customer_id='$customerId' order by ma.mode asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Delete customer payment term entry
	function delCustomer2paymentTermEntry($pTermEntryId)
	{
		$qry	= " delete from m_customer_payment_terms where id='$pTermEntryId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	} 

	#Delete Customer 2 payment Terms
	function deleteCustomer2paymentTerms($customerId)
	{
		$pTermRecs = $this->getCustPaymentTermRecs($customerId);

		foreach ($pTermRecs as $ptr) {
			$pTermEntryId 	= $ptr[0];
			$paymentTermId   = $ptr[1];
			
			if (!$this->pTermRecInUse($customerId, $paymentTermId)) {
				$delCust2PaymentTerm = $this->delCustomer2paymentTermEntry($pTermEntryId);
			}
		}

		/*
		$qry	= " delete from m_customer_payment_terms where customer_id='$customerId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
		*/
	}

	function pTermRecInUse($customerId, $paymentTermId)
	{
		$qry	= "select id from t_purchaseorder_main where payment_term='$paymentTermId' and customer_id ='$customerId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;	
	}

	# Get Cust Payment terms
	function getCustPaymentTermRecs($customerId)
	{
		$qry = " select mac.id, mac.payment_term_id from m_customer_payment_terms mac, m_paymentterms ma where mac.payment_term_id=ma.id and mac.customer_id='$customerId' order by ma.mode asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getCustomerRecord()
	{
		$qry = "select id, customer_name from m_customer where active=1";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}

}