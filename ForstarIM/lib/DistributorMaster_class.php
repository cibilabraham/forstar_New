<?php
class DistributorMaster
{
	/****************************************************************
	This class deals with all the operations relating to Distributor Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DistributorMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Record
	function addDistributor($code, $distriName, $contactPerson, $address, $userId, $contactNo, $openingBal, $creditLimit, $creditPeriod, $crPeriodFrom, $distStartDate)
	{
		$qry = "insert into m_distributor (code, name, contact_person, address, created, createdby, contact_no, opening_bal, credit_limit, credit_period, cr_period_from, start_date) values('$code', '$distriName', '$contactPerson', '$address', Now(), '$userId', '$contactNo', '$openingBal', '$creditLimit', '$creditPeriod', '$crPeriodFrom', '$distStartDate')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Distributor wise State
	function addDistributorState($lastId, $selStateId, $selCity, $billingAddress, $deliveryAddress, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo,$selTaxType, $billingForm, $billingState, $sameBillingAdr, $selArea, $octroiApplicable, $octroiPercent, $octroiExempted, $entryTaxApplicable, $entryTaxPercent, $entryTaxExempted, $cityContactPerson, $openingBalance, $crLimit, $lwStatus,$eccNo, $exBillingForm, $exportEnabled, $lastDistStateEntryId, $locationStartDate, $locationId, $gistinNo)
	{
		$qry = "insert into m_distributor_state (distributor_id, state_id, billing_address, delivery_address, pin_code, tel_no, fax_no, mob_no, vat_no, tin_no, cst_no,tax_type, billing_form, billing_state_id, same_billing_adr, octroi_applicable, octroi_percent, octroi_exempted, entry_tax_applicable, entry_tax_percent, entry_tax_exempted, contact_person, opening_balance, credit_limit, active, ecc_no, ex_billing_form, export_active, dist_state_entry_id, start_date, location_id, gstint_no) values('$lastId', '$selStateId', '$billingAddress', '$deliveryAddress', '$pinCode', '$telNo', '$faxNo', '$mobNo', '$vatNo', '$tinNo', '$cstNo','$selTaxType', '$billingForm', '$billingState', '$sameBillingAdr', '$octroiApplicable', '$octroiPercent', '$octroiExempted', '$entryTaxApplicable', '$entryTaxPercent', '$entryTaxExempted', '$cityContactPerson', '$openingBalance', '$crLimit', '$lwStatus', '$eccNo', '$exBillingForm', '$exportEnabled', '$lastDistStateEntryId', '$locationStartDate', '$locationId', '$gistinNo')";

		//echo "<br>$lastDistStateEntryId=".$qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);	
		$stateEntryId = "";	
		if ($insertStatus) {
			$this->databaseConnect->commit();
			$stateEntryId = $this->databaseConnect->getLastInsertedId();
			# Add City
			$cityRecIns = $this->addDistributorCity($stateEntryId, $selCity);
			if ($cityRecIns) {
				$cityEntryId = $this->databaseConnect->getLastInsertedId();
				# Add Area	
				$this->addDistributorArea($cityEntryId, $selArea);
			}
		} else $this->databaseConnect->rollback();
		return $stateEntryId;	
	}

	# Add City
	function addDistributorCity($stateEntryId, $selCity)
	{
		$qry	= "insert into m_distributor_city (dist_state_entry_id, city_id) values('".$stateEntryId."','".$selCity."')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Add Area
	function addDistributorArea($cityEntryId, $selArea)
	{
		if ($selArea) {
			foreach ($selArea as $cId) {
				$areaId	= $cId;	
				$qry	= "insert into m_distributor_area (dist_city_entry_id, area_id) values('".$cityEntryId."','".$areaId."')";
				//echo $qry;
				$insertStatus	= $this->databaseConnect->insertRecord($qry);
				if ($insertStatus) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		}
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit, $selStateFilterId, $selCityFilterId)
	{
		$whr = "";
		$tableJoin = "";
		if ($selStateFilterId!="" || $selCityFilterId!="") {
			$whr .= " a.id = b.distributor_id and b.id = c.dist_state_entry_id";
			if ($selStateFilterId!="" && $selCityFilterId=="") $whr .= " and b.state_id=$selStateFilterId";
			else if ($selStateFilterId=="" && $selCityFilterId!="") $whr .= " and c.city_id=$selCityFilterId";
			else if ($selStateFilterId!="" && $selCityFilterId!="") $whr .= " and b.state_id=$selStateFilterId and c.city_id=$selCityFilterId ";
			
			$tableJoin .= " , m_distributor_state b, m_distributor_city c" ;
			$distinct = " distinct ";			
		} 
		
		$orderBy  = "  a.name asc";
		$limit 	  = " $offset, $limit";

		$qry = "select $distinct a.id, a.code, a.name, a.contact_person, a.address, a.contact_no,a.active from m_distributor a $tableJoin";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		
		//echo "<br>$qry<br>";		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Filter all Distributor Records 
	function filterDistributorMasterRecords($selStateFilterId, $selCityFilterId)
	{
		$whr = "";
		$tableJoin = "";
		if ($selStateFilterId!="" || $selCityFilterId!="") {
			$whr .= " a.id = b.distributor_id and b.id = c.dist_state_entry_id";
			if ($selStateFilterId!="" && $selCityFilterId=="") $whr .= " and b.state_id=$selStateFilterId";
			else if ($selStateFilterId=="" && $selCityFilterId!="") $whr .= " and c.city_id=$selCityFilterId";
			else if ($selStateFilterId!="" && $selCityFilterId!="") $whr .= " and b.state_id=$selStateFilterId and c.city_id=$selCityFilterId ";
			
			$tableJoin .= " , m_distributor_state b, m_distributor_city c" ;
			$distinct = " distinct ";			
		}
		$orderBy  = "  a.name asc";		

		$qry = "select $distinct a.id, a.code, a.name, a.contact_person, a.address, a.contact_no from m_distributor a $tableJoin";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		//echo "<br>$qry<br>";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.code, a.name, a.contact_person, a.address, a.contact_no,a.active from m_distributor a order by a.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	function fetchAllRecordsActiveDistributor()
	{
		$qry = "select a.id, a.code, a.name, a.contact_person, a.address, a.contact_no,a.active from m_distributor a where active=1 order by a.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}
	

	# Get Distributor based on id 
	function find($distributorId)
	{
		$qry = "select id, code, name, contact_person, address, contact_no, opening_bal, credit_limit, credit_period, cr_period_from, start_date from m_distributor where id=$distributorId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get  Dist State And City Recs
	function getDistributorStateRecords($distributorId)
	{
		$qry = " select id, distributor_id, state_id, billing_address, delivery_address, pin_code, tel_no, fax_no, mob_no, vat_no, tin_no, cst_no, tax_type, billing_form, billing_state_id, same_billing_adr, octroi_applicable, octroi_percent, octroi_exempted, entry_tax_applicable, entry_tax_percent, entry_tax_exempted, contact_person, opening_balance, credit_limit, active, ecc_no, ex_billing_form, start_date, export_active,gstint_no from m_distributor_state where distributor_id='$distributorId'";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  a  Distributor
	function updateDistributor($distributorId, $distriName, $contactPerson, $address, $contactNo, $openingBal, $creditLimit, $creditPeriod, $crPeriodFrom, $distStartDate)
	{
		$qry = "update m_distributor set name='$distriName', contact_person='$contactPerson', address='$address', contact_no='$contactNo', opening_bal='$openingBal', credit_limit='$creditLimit', credit_period='$creditPeriod', cr_period_from='$crPeriodFrom', start_date='$distStartDate' where id='$distributorId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete all Entries reg. distibutor
	function delDistributorEntryRecs($distributorId)
	{
		# Get State Entry Records
		$getDistStateRecords  = $this->getDistributorStateRecords($distributorId);
		if (sizeof($getDistStateRecords)>0) {
			foreach ($getDistStateRecords as $dsr) {
				$distStateEntryId	= $dsr[0];
				list($selCityEntryId, $selCityId) = $this->getSelCityId($distStateEntryId);
				# Del City Rec
				$delDistCityEntyRec  = $this->delDistributorCityRec($distStateEntryId);
				# Del Area Rec
				$delDistAreaEntyRec  = $this->delDistributorAreaRec($selCityEntryId);
			}
		}
		# Delete Dist State Rec
		$delDistStateEntyRec = $this->delDistributorStateRec($distributorId);
		return true;
	}

	# Delete City Rec
	function delDistributorCityRec($distStateEntryId)
	{
		$qry = "delete from m_distributor_city where dist_state_entry_id=$distStateEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Area Rec
	function delDistributorAreaRec($distCityEntryId)
	{
		$qry = "delete from m_distributor_area where dist_city_entry_id=$distCityEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete State Rec
	function delDistributorStateRec($distributorId)
	{
		$qry = "delete from m_distributor_state where distributor_id=$distributorId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Distributor
	function deleteDistributor($distributorId)
	{
		$qry = "delete from m_distributor where id=$distributorId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Filter City Based on State (Using in Distributor Screen)
	function filterCityRecs($stateId)
	{
		$qry = "select id, code, name from m_city where state_id='$stateId' order by name asc";
		//echo $qry;
		$result = array();
		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$resultArr = array(''=>'-- Select --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[2];
			}
		} else {
			$resultArr = array(''=>'No City');
		}
		return $resultArr;				
	}
	# Edit Mode select City list
	function getSelectedCityList($stateId, $stateEntryId)
	{
		$qry = "select a.id, a.code, a.name, b.city_id from m_city a left join m_distributor_city b on a.id=b.city_id and b.dist_state_entry_id='$stateEntryId'  where  a.state_id='$stateId' order by a.name asc";
		//echo $qry;
		$result = array();
		$resultArr = array();
		$result	= $this->databaseConnect->getRecords($qry);
		$i=0;
		if (sizeof($result)>0) {			
			$resultArr[$i] = array('','-- Select --','');
			while (list(,$v) = each($result)) {		
				$i++;
				$resultArr[$i] = array($v[0],$v[2],$v[3]);
			}
		} else {
			$resultArr[$i] = array('','-- Select --','');
		}
		return $resultArr;
	}
	//---------------------------------------------
	# Selected State Records
	//---------------------------------------------
	function getSelectedStateRecords($distributorId)
	{
		$qry = " select distinct a.state_id, b.name from m_distributor_state a, m_state b where a.state_id=b.id and a.distributor_id='$distributorId' order by b.name asc";
		//echo "<br>$qry<br>";				

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	//---------------------------------------------
	# Selected City Records
	//---------------------------------------------
	function getSelCityRecords($distributorId)
	{
		$qry = " select distinct b.city_id, c.name from m_distributor_state a, m_distributor_city b, m_city c where a.id=b.dist_state_entry_id and b.city_id=c.id and a.distributor_id='$distributorId' order by c.name asc";
		//echo $qry."<br>";				
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Update State Rec
	function updateDistributorState($distStateEntryId, $selStateId, $selCity, $billingAddress, $deliveryAddress, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo, $selTaxType, $billingForm, $billingState, $sameBillingAdr, $selArea, $distCityEntryId, $octroiApplicable, $octroiPercent, $octroiExempted, $entryTaxApplicable, $entryTaxPercent, $entryTaxExempted, $cityContactPerson, $openingBalance, $crLimit, $lwStatus, $eccNo, $exBillingForm, $exportEnabled, $locationStartDate, $locationId, $gistinNo)
	{
		$qry = "update m_distributor_state set state_id='$selStateId', billing_address='$billingAddress', delivery_address='$deliveryAddress', pin_code='$pinCode', tel_no='$telNo', fax_no='$faxNo', mob_no='$mobNo', vat_no='$vatNo', tin_no='$tinNo', cst_no='$cstNo', tax_type='$selTaxType', billing_form='$billingForm', billing_state_id='$billingState', same_billing_adr='$sameBillingAdr', octroi_applicable='$octroiApplicable', octroi_percent='$octroiPercent', octroi_exempted='$octroiExempted', entry_tax_applicable='$entryTaxApplicable', entry_tax_percent='$entryTaxPercent', entry_tax_exempted='$entryTaxExempted', contact_person='$cityContactPerson', opening_balance='$openingBalance', credit_limit='$crLimit', active='$lwStatus', ecc_no='$eccNo', ex_billing_form='$exBillingForm', export_active='$exportEnabled', start_date='$locationStartDate', location_id='$locationId',gstint_no='$gistinNo' where id='$distStateEntryId'";
		//echo $qry;
		//exit;
		$result = $this->databaseConnect->updateRecord($qry);	
		if ($result) {
			$this->databaseConnect->commit();
			# Del City Rec
			$delDistCityEntyRec  = $this->delDistributorCityRec($distStateEntryId);
			# Del Area Rec
			$delDistAreaEntyRec  = $this->delDistributorAreaRec($distCityEntryId);

			# Add City
			$cityRecIns = $this->addDistributorCity($distStateEntryId, $selCity);			
			if ($cityRecIns) {
				$cityEntryId = $this->databaseConnect->getLastInsertedId();
				# Add Area
				$this->addDistributorArea($cityEntryId, $selArea);	
			}
			
		}
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}

	# Delete state entry
	function delRemovedDistRec($distStateEntryId, $distCityEntryId)
	{
		# Del City Rec
		$delDistCityEntyRec  = $this->delDistributorCityRec($distStateEntryId);
		# Del Area Rec
		$delDistAreaEntyRec  = $this->delDistributorAreaRec($distCityEntryId);

		$qry = "delete from m_distributor_state where id=$distStateEntryId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Get Billing State Records
	function getBillingStateRecords()
	{
		$qry = " select id, name from m_state where billing_state='Y'";
		//echo $qry."<br>";			
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Checking Dist Exist
	function chkDistributorExist($distributorId)
	{		
		$qry = " select id from (
				select a.id as id from m_distmargin_ratelist a where a.distributor_id='$distributorId'
			union
				select a1.id as id from m_distributor_margin a1 where a1.distributor_id='$distributorId'
			union
				select a2.id as id from t_distributor_ac a2 where a2.distributor_id='$distributorId'
			union
				select a3.id as id from m_product_identifier a3 where a3.distributor_id='$distributorId'
			union
				select a4.id as id from m_product_status a4 where a4.distributor_id='$distributorId'
			union
				select a5.id as id from t_salesorder a5 where a5.distributor_id='$distributorId'
		) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Checking the Dist - State is linked with any other process
	function distStateRecExist($distributorId, $stateId)
	{
		$qry = " select a.id from m_distributor_margin a, m_distributor_margin_state b where a.id=b.distributor_margin_id and a.distributor_id='$distributorId' and b.state_id='$stateId' ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Filter Area Based on City (Using in Distributor Screen)
	function filterAreaRecs($cityId)
	{
		$qry = "select id, code, name from m_area where city_id='$cityId' order by name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			$resultArr = array('0'=>'-- Select All --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[2];
			}
		} else {
			$resultArr = array(''=>'No Area');
		}
		return $resultArr;				
	}
	# Edit Mode select Area list
	function getSelectedAreaList($cityId, $distCityEntryId)
	{
		$qry = "select a.id, a.code, a.name, b.area_id from m_area a left join m_distributor_area b on a.id=b.area_id and b.dist_city_entry_id='$distCityEntryId'  where  a.city_id='$cityId' order by a.name asc";
		//echo $qry;
		$gQry = "select b.area_id from m_distributor_area b where b.dist_city_entry_id='$distCityEntryId' and b.area_id=0 ";
		$gResult	= $this->databaseConnect->getRecords($gQry);
		$selectAll = '';
		if (sizeof($gResult)>0) $selectAll = 0;
		$result = array();
		$resultArr = array();
		$result	= $this->databaseConnect->getRecords($qry);
		$i=0;
		if (sizeof($result)>0) {			
			$resultArr[$i] = array('0','-- Select All--',$selectAll);
			while (list(,$v) = each($result)) {		
				$i++;
				$resultArr[$i] = array($v[0],$v[2],$v[3]);
			}
		} else {
			$resultArr[$i] = array('0','-- Select --','');
		}
		return $resultArr;
	}
	# Area filter Ends Here
	# ----------------------	

	function getSelCityId($distStateEntryId)
	{
		$qry = " select id, city_id from m_distributor_city where dist_state_entry_id='$distStateEntryId'";
		$result	= $this->databaseConnect->getRecord($qry);
		return array($result[0],$result[1]);
	}

	//---------------------------------------------
	# Selected Area Records
	//---------------------------------------------
	function getSelAreaRecords($distributorId)
	{		
		$qry = " select b.area_id, c.name, e.name, a.tax_type, a.billing_form, a.octroi_applicable, a.octroi_percent, a.octroi_exempted, a.entry_tax_applicable, a.entry_tax_percent, a.entry_tax_exempted, a.state_id, d.city_id, a.export_active,a.active
			from 
				m_distributor_state a join m_distributor_city d on a.id=d.dist_state_entry_id 
				left join m_city e on d.city_id=e.id 
				join m_distributor_area b on d.id=b.dist_city_entry_id 
				left join m_area c on b.area_id=c.id 
			where a.distributor_id='$distributorId' order by c.name asc";

		//echo "<br>$qry<br>";			
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check Octroi Applicable
	function chkOctroi($stateId, $cityId)
	{
		$qry = " select id from m_city where state_id='$stateId' and id='$cityId' and octroi='Y'";
		//echo $qry."<br>";			
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Check entry Tax
	function chkEntryTax($stateId)
	{
		$qry = " select id from m_state where id='$stateId' and entry_tax='Y'";
		//echo $qry."<br>";			
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get All Distributor Master Recs
	function fetchAllDistributorRecords()
	{
		$qry = "select a.id, a.code, a.name, a.contact_person, a.address, a.contact_no from m_distributor a order by a.name asc";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Update Processor Status
	function updateDistStatus($distributorId, $distStatus)
	{
		$qry	=	" update m_distributor set active='$distStatus' where id=$distributorId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Get current dist status
	function getDistCurrentStatus($distributorId)
	{
		$rec = $this->find($distributorId);
		return $rec[9];
	}

	# Get city wise Dist margin 
	function getDistMarginRecs($distributorId, $rateListId, $stateId, $cityId)
	{
		$qry = "select mdms.final_margin from m_distributor_margin mdm join m_distributor_margin_state mdms on mdm.id=mdms.distributor_margin_id where mdm.distributor_id='$distributorId' and mdm.rate_list_id='$rateListId' and mdms.state_id='$stateId' and mdms.city_id='$cityId' group by mdms.final_margin order by mdms.final_margin asc";
		
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	# Get Dist Rec	
	function getDistRec($distributorId, $stateId, $cityId)
	{
		$qry = "select mds.opening_balance, mds.credit_limit, mds.active from m_distributor_state mds join m_distributor_city mdc on mds.id=mdc.dist_state_entry_id where mds.distributor_id='$distributorId' and mds.state_id='$stateId' and mdc.city_id='$cityId' ";
		//echo "<br>$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2]);
	}

	# Get Credit Balance
	function getCreditBalance($fromDate, $tillDate, $distributorId, $cityId=null, $selAreaId=null, $multipleCityExist)
	{	
		//a.select_date>= '$fromDate' and a.select_date<= '$tillDate'
		//a1.select_date>= '$fromDate' and a1.select_date<= '$tillDate'
		# Common
		if (!$cityId) {
			$qry = "select (sum(cAmt)+IFNULL(creditLimit,0)-(sum(dAmt)+IFNULL(openAmt,0))) as creditBalance from
			( 
				select a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, b.opening_bal as openAmt, b.credit_limit as creditLimit, b.name as distName from t_distributor_ac a join m_distributor b on a.distributor_id=b.id where (a.value_date is not null and a.value_date!='0000-00-00') and a.pmt_type!='M' and a.distributor_id='$distributorId' and a.cod='D' group by a.distributor_id
			union
				select a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, b1.opening_bal as openAmt, b1.credit_limit as creditLimit, b1.name as distName from t_distributor_ac a1 join m_distributor b1 on a1.distributor_id=b1.id where (a1.value_date is not null and a1.value_date!='0000-00-00') and a1.pmt_type!='M' and a1.distributor_id='$distributorId' and a1.cod='C' group by a1.distributor_id
			) 
			as X group by distributor_id order by distName";
		} else if ($cityId!="") {
		# City Base Credit balance
			$qry = "select (sum(cAmt)+IFNULL(creditLimit,0)-(sum(dAmt)+IFNULL(openAmt,0))) as creditBalance, sum(cAmt) , creditLimit, sum(dAmt), openAmt from
			( 
				select 
					a.distributor_id, sum(a.amount) as dAmt, 0 as cAmt, b.amount as outAmt, mds.opening_balance as openAmt, (mds.credit_limit) as creditLimit, b.name as distName 
				from 
					t_distributor_ac a ";
			if ($selAreaId!=0 && $multipleCityExist) $qry .= " join t_distributor_ac_invoice tdai on tdai.dist_ac_id=a.id join t_salesorder tso on tso.id=tdai.invoice_id";
				$qry .= " join m_distributor b on a.distributor_id=b.id  
					left join m_distributor_state mds on mds.distributor_id=b.id 
					left join m_distributor_city mdc on mdc.dist_state_entry_id=mds.id and mdc.city_id=a.city_id ";
			if ($selAreaId!=0 && $multipleCityExist) $qry .= " join m_distributor_area mda on mda.dist_city_entry_id=mdc.id ";
				$qry .= " where 
					(a.value_date is not null and a.value_date!='0000-00-00') and a.pmt_type!='M' and a.distributor_id='$distributorId' and a.cod='D' and mdc.city_id='$cityId'";
			if ($selAreaId!=0 && $multipleCityExist) $qry .= " and (mda.area_id in ($selAreaId) or mda.area_id=0) and (tso.area_id in ($selAreaId) or tso.area_id=0)";
			$qry .= "
				group by a.distributor_id
			union
				select 
					a1.distributor_id, 0 as dAmt, sum(a1.amount) as cAmt, b1.amount as outAmt, mds1.opening_balance as openAmt, (mds1.credit_limit) as creditLimit, b1.name as distName 
				from 
					t_distributor_ac a1 ";
			if ($selAreaId!=0 && $multipleCityExist) $qry .= " join t_distributor_ac_invoice tdai1 on tdai1.dist_ac_id=a1.id join t_salesorder tso1 on tso1.id=tdai1.invoice_id";
			$qry .= " join m_distributor b1 on a1.distributor_id=b1.id 
					left join m_distributor_state mds1 on mds1.distributor_id=b1.id 
					left join m_distributor_city mdc1 on mdc1.dist_state_entry_id=mds1.id and mdc1.city_id=a1.city_id";
			if ($selAreaId!=0 && $multipleCityExist) $qry .= " join m_distributor_area mda1 on mda1.dist_city_entry_id=mdc1.id";
			$qry .= " where 
					(a1.value_date is not null and a1.value_date!='0000-00-00') and a1.pmt_type!='M' and a1.distributor_id='$distributorId' and a1.cod='C' and mdc1.city_id='$cityId'";
			if ($selAreaId!=0 && $multipleCityExist) $qry .= " and (mda1.area_id in ($selAreaId) or mda1.area_id=0) and (tso1.area_id in ($selAreaId) or tso1.area_id=0)";
			$qry .= " 
				group by a1.distributor_id
			) 
			as X group by distributor_id order by distName";
		}
		//echo "$fromDate, $tillDate, $distributorId, $cityId, A=$selAreaId==><br>$qry<br>";

		$result = $this->databaseConnect->getRecords($qry);
		$crResult = array();

		if (!sizeof($result) && $cityId!="") {
			$crQry = "select mds.credit_limit from m_distributor md join m_distributor_state mds on mds.distributor_id=md.id join m_distributor_city mdc on mdc.dist_state_entry_id=mds.id where md.id='$distributorId' and mdc.city_id='$cityId'";
			$crResult = $this->databaseConnect->getRecords($crQry);
		}

		return (sizeof($result)>0)?$result[0][0]:$crResult[0][0];
	}

	function getAreaList($distCityEntryId)
	{
		$qry = "select group_concat(area_id) from m_distributor_area where dist_city_entry_id='$distCityEntryId' group by dist_city_entry_id ";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	function chkDuplicateCity($distributorId)
	{
		$qry = "select count(*) from m_distributor md join m_distributor_state mds on mds.distributor_id=md.id join m_distributor_city mdc on mdc.dist_state_entry_id=mds.id where md.id='$distributorId' group by mdc.city_id";
		$result = $this->databaseConnect->getRecords($qry);
		$multipleCityExist = false;
		foreach ($result as $r) {
			$cityCount = $r[0];
			if ($cityCount>1) $multipleCityExist = true;
		}
		return $multipleCityExist;
	}

	# Bank AC section starts here ------------------------------------------

	# Add Billing Compnay Bank AC
	function addDistBankAC($distributorId, $bankName, $accountNo, $branchLocation, $defaultAC, $selLocIds)
	{
		$qry = "insert into m_distributor_bank_ac (distributor_id, bank_name, account_no, branch_location, default_ac, tagged_location_id) values ('$distributorId', '$bankName', '$accountNo', '$branchLocation', '$defaultAC', '$selLocIds') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Get company bank ac
	function getDistBankACRecs($distributorId)
	{
		$qry = "select id, bank_name, account_no, branch_location, default_ac, tagged_location_id from m_distributor_bank_ac where distributor_id='$distributorId' order by id asc";

		
		//echo("<br><br>".$qry);
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update bank AC rec
	function updateDistBankAC($bankACEntryId, $bankName, $accountNo, $branchLocation, $defaultAC, $selLocIds)
	{
		$qry = "update m_distributor_bank_ac set bank_name='$bankName', account_no='$accountNo', branch_location='$branchLocation', default_ac='$defaultAC', tagged_location_id='$selLocIds' where id='$bankACEntryId'";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Bank AC
	function delDistBankACRec($bankACEntryId)
	{
		$qry	=	" delete from m_distributor_bank_ac where id='$bankACEntryId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function displayDistBankACDtls($distributorId)
	{
		$bankACRecs = $this->getDistBankACRecs($distributorId);
		$displayHtml = "";
		if (sizeof($bankACRecs)>0) {
			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";	
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
			$displayHtml .= "<td class=listing-head>Company Name</td>";
			$displayHtml .= "<td class=listing-head>Account No</td>";
			$displayHtml .= "<td class=listing-head>Branch Location</td>";
			$displayHtml .= "<td class=listing-head>Default</td>";
			$displayHtml .= "</tr>";
			foreach ($bankACRecs as $bcb) {
				$bankName 	= $bcb[1];
				$accountNo	= $bcb[2];
				$branchLocation = $bcb[3];
				$defaultAC	= $bcb[4];

				$displayHtml .= "<tr bgcolor=#fffbcc>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $bankName;
				$displayHtml .= "</td>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $accountNo;
				$displayHtml .=	"</td>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $branchLocation;
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

	# Delete Dist Bank AC
	function deleteDistBankACRecs($distributorId)
	{
		$qry	= " delete from m_distributor_bank_ac where distributor_id='$distributorId'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Fetch All Distributor Bank AC Recs
	function fetchAllDistBankACs($distributorId)
	{
		$whr = "distributor_id='$distributorId'";

		$orderBy	= "bank_name asc";
		
		$qry = "select id, bank_name, account_no, branch_location, default_ac, CONCAT(SUBSTRING_INDEX(bank_name,' ',1),' ',account_no) as displayName from m_distributor_bank_ac ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "<br>$qry<br>";
		return $result;
	}

	function chkDistBankAcInUse($bankACEntryId)
	{
		$qry = "select id from t_distributor_ac where dist_bank_ac_id='$bankACEntryId'";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	
	function chkDistributorBankAcInUse($distributorId)
	{
		$bankACRecs = $this->getDistBankACRecs($distributorId);
		$recInUse = false;
		foreach ($bankACRecs as $bar) {
			$bankACEntryId = $bar[0];
			$bankAcInUse   = $this->chkDistBankAcInUse($bankACEntryId);
			if ($bankAcInUse) $recInUse = true;
		}

		return $recInUse;
	}

	# Bank AC section ends here ----------------------------
	

	function updateDistributorconfirm($distributorId)
	{
	$qry	= "update m_distributor set active='1' where id=$distributorId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateDistributorReleaseconfirm($distributorId)
	{
		$qry	= "update m_distributor set active='0' where id=$distributorId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
	#Get All Active Distributors Name:
	function getCustomerRecord()
	{
		$qry = "select id,name from m_distributor where active=1 order by name asc";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0?$result:"");
	}
	
	#Get the Credit Period of selected Customer
	function getCreditPeriod($customerId)
	{
		$qry = "select id, credit_period from m_distributor where id='$customerId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0?$result:"");
	}
	
}
?>