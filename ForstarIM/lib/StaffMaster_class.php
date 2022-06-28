<?php
class StaffMaster
{  
	/****************************************************************
	This class deals with all the operations relating to fish master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function StaffMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addStaff($name,$functions,$cost,$allowance,$multiplyFactor,$effectiveDate,$actualCost,$type,$department,$userId)
	{
		$qry	= "insert into m_staff_master (name,function,cost,allowance,multiply_factor,start_date,actual_cost,type,department,createdby,createdon) values('".$name."','".$functions."','".$cost."','".$allowance."','".$multiplyFactor."','".$effectiveDate."','".$actualCost."','".$type."','".$department."','".$userId."',Now())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);

		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all fishs 
	function fetchAllRecords()
	{
		
		//$qry	= "select mf.id, name, code,category_id,mf.active from m_staff_master mf join m_staff_mastercategory  mc on mf.category_id=mc.id order by name";	
		$qry	= "select a.id,a.name,a.function,a.cost,a.active,a.type,a.department,a.start_date,a.actual_cost,a.allowance,b.name,a.multiply_factor from m_staff_master a left join m_role_rte b on a.function=b.id where end_date='0000-00-00' order by a.name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	function fetchAllRecordsStaffactive()
	{
		
		$qry	= "select a.id,a.name,a.function,a.cost,a.active,a.type,a.department,a.start_date,a.actual_cost,b.name  from m_staff_master a left join m_role_rte b on a.function=b.id    where a.active='1' and end_date='0000-00-00'  group by a.name  order by a.name desc";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all fishs (Pagination)
	function  fetchAllPagingRecords($offset, $limit,$confirm)
	{
		$qry	= "select a.id,a.name,a.function,a.cost,a.active,a.type,a.department,a.start_date,a.actual_cost,a.allowance,b.name,a.multiply_factor from m_staff_master a left join m_role_rte b on a.function=b.id where end_date='0000-00-00' order by a.name asc limit $offset, $limit";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateStaffconfirm($staffId)
	{
	$qry	= "update m_staff_master set active='1' where id=$staffId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateStaffReleaseconfirm($staffId)
	{
		$qry	= "update m_staff_master set active='0' where id=$staffId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	
	# Get fish based on id 
	function find($staffId)
	{
		$qry	= "select id,name,function,cost,allowance,type,department,start_date,actual_cost,active,multiply_factor from m_staff_master where id=$staffId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function findFishName($staffId)
	{
		$rec = $this->find($staffId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	# Delete a deleteStaff 
	function deleteStaff($staffId)
	{
		$qry	= " delete from m_staff_master where id=$staffId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}

	# Delete Staff
	function updateStaff($staffId,$name,$function,$cost,$allowance,$multiplyFactor,$effectiveDate,$actualCost,$type,$department)
	{
		$qry	= " update m_staff_master set name='$name',function='$function',cost='$cost',allowance='$allowance',multiply_factor='$multiplyFactor',start_date='$effectiveDate',actual_cost='$actualCost',type='$type',department='$department' where id=$staffId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# -----------------------------------------------------
	# Checking Fish Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function staffRecInUse($staffId)
	{		
		$qry = " select id from m_product_manage where id='$staffId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Get All Fish Recs
	function getFishRecs()
	{
		$fishArr = array();
		//$fishRecs = $this->fetchAllRecords();
		$fishRecs=$this->fetchAllRecordsFishactive();
		if (sizeof($fishRecs)>0) {
			foreach ($fishRecs as $fr) {
				$staffId = $fr[0];
				$fName  = $fr[1];
				$fishArr[$staffId] = $fName;
			}	
		}
		return $fishArr;
	}
	
	# Get all fish source 
	function fetchAllSourceRecords()
	{
		$qry	= "select id,name from m_staff_master_source";
		return $this->databaseConnect->getRecords($qry);
	}

	function checkDuplicate($name,$functions,$department)
	{
		$qry = " select id from m_staff_master where name='$name' && function='$functions' && department='$department'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	function getStartDate($staffId)
	{
		$qry = " select start_date from m_staff_master where id='$staffId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	function updateStaffEndDate($endDate,$staffId)
	{
		$qry	= " update m_staff_master set end_date='$endDate' where id='$staffId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	#Fetching Details by joining Staff Master and Department
	function fetchStaffDetail($staffId)
	{
		$qry = "select a.department, a.type, a.actual_cost, b.type, a.active from m_staff_master a left join m_rte_department b on a.department=b.id where a.id='$staffId'";
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	
}