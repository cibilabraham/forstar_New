<?php
class EmployeeCostCalculation
{  
	/****************************************************************
	This class deals with all the operations relating to fish master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function EmployeeCostCalculation(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function fetchAllRecords()
	{
		
		$qry	= "select id,name,percentage_value  from m_employee_cost_calculation order by id";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateEmployeeCost($empCostId,$costPercent,$userId)
	{
		$currentDate=date("Y-m-d");
		$qry	= "update m_employee_cost_calculation set percentage_value='$costPercent',createdon='$currentDate',createdby='$userId' where id=$empCostId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	function getProposedCost()
	{
		$qry	= "select percentage_value  from m_employee_cost_calculation where name='PROPOSED COST'";
			//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}
	

	function addStaff($name,$functions,$cost,$type,$department,$userId)
	{
		$qry	= "insert into m_staff_master (name,function,cost,type,department,createdby,createdon) values('".$name."','".$functions."','".$cost."','".$type."','".$department."','".$userId."',Now())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);

		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all fishs 
	

	function fetchAllRecordsFishactive()
	{
		
		$qry	= "select id,name,function,cost,active,type,department  from m_staff_master where actve='1' order by name";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all fishs (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,name,function,cost,active,type,department  from m_staff_master order by name limit $offset, $limit";
		
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
		$qry	= "select id,name,function,cost,type,department  from m_staff_master where id=$staffId";
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
	function updateStaff($staffId,$name, $function,$cost,$type,$department)
	{
		$qry	= " update m_staff_master set name='$name',function='$function', cost='$cost',type='$type',department='$department' where id=$staffId";
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
}