<?php
class FrozenPackingRateList
{
	/****************************************************************
	This class deals with all the operations relating to Employee Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FrozenPackingRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Employee Master
	function addFrozenPackingRateList($name, $start_date, $userId)
	{
		$qry	=	"insert into  m_frzn_pkg_rate_list (name, start_date,created, created_by) values('".$name."', '$start_date', Now(), '$userId')";

	//echo $qry; die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, name, designation, department,address,telephone_no,active FROM  m_frzn_pkg_rate_list  order by name limit $offset,$limit";
		$qry	=	"select fprl.id, fprl.name, fprl.start_date,fprl.active from m_frzn_pkg_rate_list fprl order by fprl.start_date desc limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Employee Master 
	function fetchAllRecords()
	{
		$qry	= "select fprl.id, fprl.name, fprl.start_date from m_frzn_pkg_rate_list fprl order by fprl.start_date desc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllRecordsActiveEmployee()
	{
		$qry	= "select id, name, designation, department,address,telephone_no,active FROM  m_frzn_pkg_rate_list where active='1'  order by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivedept()
	{
		$qry	= "select id, name, description, incharge,active from m_department where active=1 order by name";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Employee Master based on id 
	function find($FrozenPackingRateListId)
	{
		$qry	= "select id, name, start_date from  m_frzn_pkg_rate_list where id=$FrozenPackingRateListId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Employee Master 
	function deleteFrozenPackingRateList($FrozenPackingRateListId)
	{
		$qry	= " delete from  m_frzn_pkg_rate_list where id=$FrozenPackingRateListId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Employee Master
	function updateFrozenPackingRateList($FrozenPackingRateListId, $name, $startDate)
	{
		$qry	= " update  m_frzn_pkg_rate_list set name='$name', start_date='$startDate' where id=$FrozenPackingRateListId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateFrozenPackingRateListconfirm($frozenPackingRateListId){
		$qry	= "update m_frzn_pkg_rate_list set active='1' where id=$frozenPackingRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateFrozenPackingRateListReleaseconfirm($FrozenPackingRateListId)
	{
		$qry	= "update  m_frzn_pkg_rate_list set active='0' where id=$FrozenPackingRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	function fetchDesignation($designation)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		  $qry	=	"select id, designation FROM m_designation WHERE id=$designation";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}
	
	function fetchDepartment($department)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		  $qry	=	"select id, name FROM m_department WHERE id=$department";
		$result	=	$this->databaseConnect->getRecord($qry);
		//echo $qry;
		return $result;
	}

	function RateList()
	{ 
		$qry	=	"select id, name FROM m_frzn_pkg_rate_list";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}

	function chkValidDateEntry($seldate, $cId)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		$qry	= "select a.id, a.name, a.start_date from m_frzn_pkg_rate_list a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?false:true;
	}

	# Date Wise Rate list
	function getFrznPkgRateList($selDate)
	{	
		$qry	= "select id as ratelistid from m_frzn_pkg_rate_list where date_format(start_date,'%Y-%m-%d')<='$selDate' order by id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# update Rec
	function updateRateListRec($pageCurrentRateListId, $endDate)
	{
		$qry = " update m_frzn_pkg_rate_list set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	#Find the Latest Rate List  Id (using in Other screen )
	function latestRateList()
	{
		$cDate = date("Y-m-d");	
		$qry = "select a.id as ratelistid from m_frzn_pkg_rate_list a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Date Wise Rate list
	function getRate($copyRateList)
	{	
		$qry	= "select * from m_frzn_pkg_rate where rate_list_id='$copyRateList'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function insFrozenPkgRate($fishId,$processCodeId,$freezingStageId,$qualityId,$frozenCodeId,$defaultRate,$rateListId)
	{
		$qry="insert into  m_frzn_pkg_rate(fish_id,process_code_id,freezing_stage_id,quality_id,frozen_code_id,default_rate,rate_list_id) values('".$fishId."','".$processCodeId."','".$freezingStageId."','".$qualityId."','".$frozenCodeId."','".$defaultRate."','".$rateListId."')";
		//echo $qry; die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getPkgRateGrade($frozenPackingRateId)
	{	
		$qry	= "select * from  m_frzn_pkg_rate_grade where pkg_rate_entry_id	='$frozenPackingRateId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function insFrozenPkgRateGrade($pkgRateEntryId,$gradeId,$rate,$preProcessorId)
	{
		$qry="insert into  m_frzn_pkg_rate_grade (pkg_rate_entry_id,grade_id,rate,pre_processor_id) values('".$pkgRateEntryId."','".$gradeId."','".$rate."','".$preProcessorId."')";
		//echo $qry; die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	function frozenPackingRateInUse($frozenRateListId)
	{
		$qry	= "select * from  m_frzn_pkg_rate where rate_list_id='$frozenRateListId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Rate List based on Date
	function validFPRateList($selDate)
	{	
		$qry	= " select id from m_frzn_pkg_rate_list where date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date='0000-00-00')) order by start_date desc ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	
}

?>