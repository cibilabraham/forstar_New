<?php
class SalesStaffMaster
{
	/****************************************************************
	This class deals with all the operations relating to Sales Staff Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SalesStaffMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Record
	function addSalesStaff($code, $salesStaffName, $address, $selStateId, $selCityId, $selArea, $pinCode, $telNo, $mobNo, $cUserId, $opState, $opCity, $opArea, $designation)
	{
		$qry = "insert into m_sales_staff (code, name, address, state_id, city_id, pin_code, tel_no, mob_no, created, createdby, area_id, op_state_id, op_city_id, designation) values('$code', '$salesStaffName', '$address', '$selStateId', '$selCityId', '$pinCode', '$telNo', '$mobNo', Now(), '$cUserId', '$selArea', '$opState', '$opCity', '$designation')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			$lastId = $this->databaseConnect->getLastInsertedId();
			# Add Sales Staff Working Area
			$this->addSalesStaffWorkingArea($lastId, $opArea);	
		} else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add Sales Staff Working Area
 	function addSalesStaffWorkingArea($lastId, $area)
	{
 	 	if ($area) {
			foreach ($area as $aId) {
				$areaId	= $aId;	// "$aId"
				$qry	= "insert into m_sales_staff_area (sales_staff_id, area_id) values('".$lastId."','".$areaId."')";
				//echo $qry;
				$insertGrade	=	$this->databaseConnect->insertRecord($qry);
				if ($insertGrade) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		}
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select a.id, a.code, a.name, a.address, a.state_id, a.city_id, a.pin_code, a.tel_no, a.mob_no, b.name, c.name, a.area_id, a.op_state_id, a.op_city_id, a.designation,a.active,(select COUNT(a1.id) from t_dailysales_main a1 where a1.sales_staff_id=a.id) as tot from m_sales_staff a, m_state b, m_city c where a.state_id=b.id and a.city_id=c.id order by a.name asc limit $offset,$limit";
		//echo $qry;		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select a.id, a.code, a.name, a.address, a.state_id, a.city_id, a.pin_code, a.tel_no, a.mob_no, b.name, c.name, a.area_id, a.op_state_id, a.op_city_id, a.designation,a.active,(select count(a1.id) from t_dailysales_main a1 where a1.sales_staff_id=a.id) as tot from m_sales_staff a, m_state b, m_city c where a.state_id=b.id and a.city_id=c.id order by a.name asc ";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	function fetchAllRecordsActiveStaff()
	{
		$qry = "select a.id, a.code, a.name, a.address, a.state_id, a.city_id, a.pin_code, a.tel_no, a.mob_no, b.name, c.name, a.area_id, a.op_state_id, a.op_city_id, a.designation,a.active from m_sales_staff a, m_state b, m_city c where a.state_id=b.id and a.city_id=c.id and a.active=1 order by a.name asc ";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}


	# Get sales Staff based Working Area Records
	function getWorkingAreaRecords($salesStaffId)
	{
		//$qry = " select a.area_id, b.name from m_sales_staff_area a, m_area b where a.area_id=b.id and a.sales_staff_id='$salesStaffId'";
		$qry = " select a.area_id, b.name from m_sales_staff_area a left join m_area b on a.area_id=b.id where a.sales_staff_id='$salesStaffId'";
		//echo "<br>$qry";		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Sales Staff based on id 
	function find($salesStaffId)
	{
		$qry = "select id, code, name, address, state_id, city_id, pin_code, tel_no, mob_no, designation, area_id, op_state_id, op_city_id  from m_sales_staff where id=$salesStaffId";
		return $this->databaseConnect->getRecord($qry);
	}

	#fOR SELECTING THE SELECTED Working Area
	function fetchSelectedAreaRecords($editId, $selCityId)
	{
		$qry 	= "select a.id, a.code, a. name, b.id, b.area_id from m_area a left join m_sales_staff_area b on a.id=b.area_id and b.sales_staff_id='$editId' where a.city_id='$selCityId' order by b.id desc, a.code asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  a  Sales Staff
	function updateSalesStaff($salesStaffId, $salesStaffName, $address, $selStateId, $selCityId, $selArea, $pinCode, $telNo, $mobNo, $opState, $opCity, $opArea, $designation)
	{
		$qry = "update m_sales_staff set name='$salesStaffName', address='$address', state_id='$selStateId', city_id='$selCityId', pin_code='$pinCode', tel_no='$telNo', mob_no='$mobNo', area_id='$selArea', op_state_id='$opState', op_city_id='$opCity', designation='$designation' where id='$salesStaffId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			# Delete sales staff working area
			$this->deleteWorkingArea($salesStaffId);
			# Add Sales Staff Working Area
			$this->addSalesStaffWorkingArea($salesStaffId, $opArea);
		} else $this->databaseConnect->rollback();
		
		return $result;	
	}

	# Delete Working Area of selected sales staff
	function deleteWorkingArea($salesStaffId)
	{
		$qry = "delete from m_sales_staff_area where sales_staff_id=$salesStaffId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;		
	}

	# Delete a Sales Staff
	function deleteSalesStaff($salesStaffId)
	{
		$qry = "delete from m_sales_staff where id=$salesStaffId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# -----------------------------------------------------
	# Checking Sales Staff is using in another Process
	# -----------------------------------------------------
	function salesStaffInUse($salesStaffId)
	{	
		$qry = " select id from t_dailysales_main where sales_staff_id='$salesStaffId'";	
		/*	
		$qry = " select id from (
				select a.id as id from m_rtcounter_margin a where a.retail_counter_id='$retailCounterId'
			union
				select a1.id as id from m_rtct_assign_dis_charge a1 where a1.retail_counter_id='$retailCounterId'
			) as X group by id ";
		*/
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Check for duplicate entry
	function chkDuplicateEntry($salesStaffName, $salesStaffId)
	{
		if ($salesStaffId!="") $updateQry = " and id!=$salesStaffId";
		else $updateQry = "";
		$qry = " select id from m_sales_staff where name = '$salesStaffName' $updateQry";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	function updateSalesStaffconfirm($salesStaffId){
		$qry	= "update m_sales_staff set active='1' where id=$salesStaffId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateSalesStaffReleaseconfirm($salesStaffId){
	$qry	= "update m_sales_staff set active='0' where id=$salesStaffId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
}