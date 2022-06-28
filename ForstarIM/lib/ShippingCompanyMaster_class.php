<?php
class ShippingCompanyMaster
{
	/****************************************************************
	This class deals with all the operations relating to Shipping company Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ShippingCompanyMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	# Add a Record
	function addShippingCompany($companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos, $userId)
	{
		$qry = "insert into m_shipping_company (name, address, city_id, state_id, phone_no, fax_no, phone_nos, fax_nos, created, created_by) values('$companyName', '$officeAddress', '$selCity', '$state', '$telephoneNo', '$faxNo', '$telephoneNos', '$faxNos', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		
		return $insertStatus;
	}

	
	# Insert (Entry) Records
	function addCompanyContact($shippingCompanyId, $personName, $designation, $role, $contactNo)
	{
		$qry = "insert into m_shipping_company_contact (main_id, person_name, designation, role, contact_no) values('$shippingCompanyId', '$personName', '$designation', '$role', '$contactNo')";
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

		$qry = " select a.id, a.name, b.name as city, c.name as state,a.active,(select count(a1.id) from m_shipping_company_contact a1 where a1.main_id=a.id) as tot from m_shipping_company a left join m_city b on a.city_id=b.id left join m_state c on a.state_id=c.id "; 

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
		
		$qry = " select a.id, a.name, b.name as city, c.name as state,a.active,(select count(a1.id) from m_shipping_company_contact a1 where a1.main_id=a.id) as tot from m_shipping_company a left join m_city b on a.city_id=b.id left join m_state c on a.state_id=c.id "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActiveShippingCompany()
	{
		$whr = "a.active=1";

		$orderBy = " a.name asc ";
		
		$qry = " select a.id, a.name, b.name as city, c.name as state,a.active from m_shipping_company a left join m_city b on a.city_id=b.id left join m_state c on a.state_id=c.id "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on id
	function find($shippingCompanyId)
	{
		$qry = "select id, name, address, city_id, state_id, phone_no, fax_no, phone_nos, fax_nos from m_shipping_company where id=$shippingCompanyId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getCompanyContactRecs($shippingCompanyId)
	{
		$qry = " select a.id, a.person_name, a.designation, a.role, a.contact_no from m_shipping_company_contact a where a.main_id='$shippingCompanyId' order by a.person_name asc"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Update  a  Record
	function updateShippingCompany($shippingCompanyId, $companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos)
	{		
		$qry = "update m_shipping_company set name='$companyName', address='$officeAddress', city_id='$selCity', state_id='$state', phone_no='$telephoneNo', fax_no='$faxNo', phone_nos='$telephoneNos', fax_nos='$faxNos' where id=$shippingCompanyId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update Entry
	function updateShipCompanyContact($shipCompanyContactId, $personName, $designation, $role, $contactNo)
	{
		$qry = " update m_shipping_company_contact set person_name='$personName', designation='$designation', role='$role', contact_no='$contactNo' where id='$shipCompanyContactId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Entry Rec
	function delShipCompanyContactRec($shipCompanyContactId)
	{
		$qry = " delete from m_shipping_company_contact where id=$shipCompanyContactId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Entry Table Rec
	function deleteShippingCompanyContactRec($shippingCompanyId)
	{
		$qry 	= " delete from m_shipping_company_contact where main_id=$shippingCompanyId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Main Rec
	function deleteShippingCompanyRec($shippingCompanyId)
	{
		$qry 	= " delete from m_shipping_company where id=$shippingCompanyId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	# Duplicate entry check
	function chkDuplicateEntry($name, $shippingCompanyId)
	{
		$qry	= "select id from m_shipping_company where name='$name'";
		if ($shippingCompanyId) $qry .= " and id!='$shippingCompanyId'";

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

	function getShippingCompanyName($shippingCompanyId)
	{
		$rec = $this->find($shippingCompanyId);
		return (sizeof($rec)>0)?$rec[1]:"";
	}


	function updateShippingCompanyconfirm($shippingCompanyId)
	{
	$qry	= "update m_shipping_company set active='1' where id=$shippingCompanyId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateShippingCompanyReleaseconfirm($shippingCompanyId)
	{
		$qry	= "update m_shipping_company set active='0' where id=$shippingCompanyId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

	/**
	# Xajax Starts Here ----------------------------------------------------
	**/
	/*
	function addDropDownOptions($sSelectId, $options, $cId, &$objResponse)
	{		
		$objResponse->script("document.getElementById('".$sSelectId."').length=0");
		if (sizeof($options) >0) {
			foreach ($options as $option=>$val) {
				$objResponse->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
			}
		}
	}
	*/


	function chkShipNameExist($name, $shippingCompanyId, $mode)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$shippingCompanyMasterObj 	= new ShippingCompanyMaster($databaseConnect);
		$chkNameExist = $shippingCompanyMasterObj->chkDuplicateEntry(trim($name), $shippingCompanyId);		
		if ($chkNameExist) {
			$objResponse->assign("divNameExistMsg", "innerHTML", "Name is already in database.<br>Please choose another one.");
			$objResponse->script("disableCmdButton($mode);");
		} else  {
			$objResponse->assign("divNameExistMsg", "innerHTML", "");
			$objResponse->script("enableCmdButton($mode);");
		}		
		return $objResponse;
	}

	function shipCompanyState($cityId, $selCityId)
	{
		$objResponse 			= &new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$shippingCompanyMasterObj 	= new ShippingCompanyMaster($databaseConnect);
		$stateRecs 			= $shippingCompanyMasterObj->filterStateList($cityId);
		
		if (sizeof($stateRecs)>0) addDropDownOptions("state", $stateRecs, $selCityId, $objResponse);
		
		return $objResponse;
	}

	
	/*# Xajax Ends here --------------------------------------------------*/
?>