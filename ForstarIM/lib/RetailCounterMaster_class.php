<?php
class RetailCounterMaster
{
	/****************************************************************
	This class deals with all the operations relating to Retail Counter Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function RetailCounterMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Record
	function addRetailCounter($code, $retailCounterName, $contactPerson, $address, $selStateId, $selCityId, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo, $selDistributorId, $selSalesStaffId, $area, $cUserId, $selRtCtCateogry)
	{
		$qry = "insert into m_retail_counter (code, name, contact_person, address, state_id, city_id, pin_code, tel_no, fax_no, mob_no, vat_no, tin_no, cst_no, created, createdby, distributor_id, sales_staff_id, rt_ct_category_id) values('$code', '$retailCounterName', '$contactPerson', '$address', '$selStateId', '$selCityId', '$pinCode', '$telNo', '$faxNo', '$mobNo', '$vatNo', '$tinNo', '$cstNo', Now(), '$cUserId', '$selDistributorId', '$selSalesStaffId', '$selRtCtCateogry')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
			$lastId = $this->databaseConnect->getLastInsertedId();
			# Add Retail Counter operational Area
			$this->addRetailCounterArea($lastId, $area);
		} else $this->databaseConnect->rollback();
		
		return $insertStatus;
	}

	
	# Add Retail Counter operational Area
 	function addRetailCounterArea($lastId, $area)
	{
 	 	if ($area) {
			foreach ($area as $aId) {
				$areaId	= $aId;	// "$aId"
				$qry	= "insert into m_retail_counter_area (retail_counter_id, area_id) values('".$lastId."','".$areaId."')";
				//echo $qry;
				$insertGrade	= $this->databaseConnect->insertRecord($qry);
				if ($insertGrade) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		}
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit, $distFilterId)
	{
		$whr = " a.distributor_id=b.id and a.sales_staff_id=c.id and a.state_id=d.id and a.city_id=e.id";
	
		if ($distFilterId!=0) $whr .= " and a.distributor_id='".$distFilterId."'";
		else $whr .= "";
		$orderBy	= " a.name asc ";
		$limit		= " $offset,$limit";

		$qry = "select a.id, a.code, a.name, a.contact_person, a.address, a.state_id, a.city_id, a.pin_code, a.tel_no, a.fax_no, a.mob_no, a.vat_no, a.tin_no, a.cst_no, a.distributor_id, b.name as distName, a.sales_staff_id, c.name as sStaff, d.name as stateName, e.name as cityName,a.active,((select COUNT(a1.id) from m_rtcounter_margin a1 where a1.retail_counter_id = a.id)+(select COUNT(a2.id) from m_rtct_assign_dis_charge a2 where a2.retail_counter_id = a.id)+(select COUNT(a3.id) from t_dailysales_rtcounter a3 where a3.rt_counter_id = a.id)) as tot from m_retail_counter a, m_distributor b, m_sales_staff c, m_state d, m_city e ";

		if ($whr!="") $qry .= " where".$whr;
		if ($orderBy!="") $qry .= " order by".$orderBy;
		if ($limit!="")	$qry .= " limit".$limit;		
		//echo $qry;		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($distFilterId)
	{
		$whr = " a.distributor_id=b.id and a.sales_staff_id=c.id and a.state_id=d.id and a.city_id=e.id";
	
		if ($distFilterId!=0) $whr .= " and a.distributor_id='".$distFilterId."'";
		else $whr .= "";

		$orderBy	= " a.name asc ";		

		$qry = "select a.id, a.code, a.name, a.contact_person, a.address, a.state_id, a.city_id, a.pin_code, a.tel_no, a.fax_no, a.mob_no, a.vat_no, a.tin_no, a.cst_no, a.distributor_id, b.name as distName, a.sales_staff_id, c.name as sStaff, d.name as stateName, e.name as cityName,a.active,((select COUNT(a1.id) from m_rtcounter_margin a1 where a1.retail_counter_id = a.id)+(select COUNT(a2.id) from m_rtct_assign_dis_charge a2 where a2.retail_counter_id = a.id)+(select COUNT(a3.id) from t_dailysales_rtcounter a3 where a3.rt_counter_id = a.id)) as tot from m_retail_counter a, m_distributor b, m_sales_staff c, m_state d, m_city e ";

		if ($whr!="") $qry .= " where".$whr;
		if ($orderBy!="") $qry .= " order by".$orderBy;		
		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}


function fetchAllRecordsActiveRetailCounter($distFilterId)
	{
		$whr = " a.distributor_id=b.id and a.sales_staff_id=c.id and a.state_id=d.id and a.city_id=e.id and a.active=1";
	
		if ($distFilterId!=0) $whr .= " and a.distributor_id='".$distFilterId."'";
		else $whr .= "";

		$orderBy	= " a.name asc ";		

		$qry = "select a.id, a.code, a.name, a.contact_person, a.address, a.state_id, a.city_id, a.pin_code, a.tel_no, a.fax_no, a.mob_no, a.vat_no, a.tin_no, a.cst_no, a.distributor_id, b.name as distName, a.sales_staff_id, c.name as sStaff, d.name as stateName, e.name as cityName,a.active from m_retail_counter a, m_distributor b, m_sales_staff c, m_state d, m_city e ";

		if ($whr!="") $qry .= " where".$whr;
		if ($orderBy!="") $qry .= " order by".$orderBy;		
		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get Retail Counter based operational Area Records
	function getOperationalAreaRecords($retailCounterId)
	{
		$qry = " select a.area_id, b.name from m_retail_counter_area a, m_area b where a.area_id=b.id and a.retail_counter_id='$retailCounterId'";		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Distributor based on id 
	function find($retailCounterId)
	{
		$qry = "select id, code, name, contact_person, address, state_id, city_id, pin_code, tel_no, fax_no, mob_no, vat_no, tin_no, cst_no, distributor_id, sales_staff_id, rt_ct_category_id from m_retail_counter where id=$retailCounterId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  record
	function updateRetailCounter($retailCounterId, $retailCounterName, $contactPerson, $address, $selStateId, $selCityId, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo, $selDistributorId, $selSalesStaffId, $area, $selRtCtCateogry)
	{
		$qry = "update m_retail_counter set name='$retailCounterName', contact_person='$contactPerson', address='$address', state_id='$selStateId', city_id='$selCityId', pin_code='$pinCode', tel_no='$telNo', fax_no='$faxNo', mob_no='$mobNo', vat_no='$vatNo', tin_no='$tinNo', cst_no='$cstNo', distributor_id='$selDistributorId', sales_staff_id='$selSalesStaffId', rt_ct_category_id='$selRtCtCateogry' where id='$retailCounterId' ";
		//, dis_log_status
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			# Delete Retail Counter Operational Area
			$this->deleteOperationalArea($retailCounterId);
			# Add Retail Counter operational Area
			$this->addRetailCounterArea($retailCounterId, $area);	
		}
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete operational Area of selected Retail Counter
	function deleteOperationalArea($retailCounterId)
	{
		$qry = "delete from m_retail_counter_area where retail_counter_id=$retailCounterId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;		
	}

	# Delete a record
	function deleteRetailCounter($retailCounterId)
	{
		$qry = "delete from m_retail_counter where id=$retailCounterId";

		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	#fOR SELECTING THE SELECTED Working Area
	function fetchSelectedAreaRecords($editId, $selCityId)
	{
		$qry 	= "select a.id, a.code, a. name, b.id, b.area_id from m_area a left join m_retail_counter_area b on a.id=b.area_id and b.retail_counter_id='$editId' where a.city_id='$selCityId' order by b.id desc, a.code asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	

	# -----------------------------------------------------
	# Checking RetailCounterId is using in another screen;
	# -----------------------------------------------------
	function retailCounterInUse($retailCounterId)
	{		
		$qry = " select id from (
				select a.id as id from m_rtcounter_margin a where a.retail_counter_id='$retailCounterId'
			union
				select a1.id as id from m_rtct_assign_dis_charge a1 where a1.retail_counter_id='$retailCounterId'	
			union
				select a2.id as id from t_dailysales_rtcounter a2 where a2.rt_counter_id='$retailCounterId'		
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Check for duplicate entry
	function chkDuplicateEntry($retailCounterName, $retailCounterId)
	{
		if ($retailCounterId!="") $updateQry = " and id!=$retailCounterId";
		else $updateQry = "";
		$qry = " select id from m_retail_counter where name = '$retailCounterName' $updateQry";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}


	function updateretailCounterconfirm($retailCounterId){
		$qry	= "update m_retail_counter set active='1' where id=$retailCounterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateretailCounterReleaseconfirm($retailCounterId){
	$qry	= "update m_retail_counter set active='0' where id=$retailCounterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
}