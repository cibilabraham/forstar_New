<?php
require_once("flib/AFModel.php");
class frzn_pkg_rate_list_model extends AFModel
{	
	public $name="FrznPkgRateList";
	protected $tableName = "m_frzn_pkg_rate_list";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array( "created" => "N" );	// N - numeric, S - string

	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from ".$this->tableName." where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";

		$result = $this->queryAll($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Check Valid Date Entry
	function chkValidDateEntry($seldate, $cId)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		$qry	= "select a.id, a.name, a.start_date from ".$this->tableName." a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";
		//echo $qry."<br>";
		$result = $this->queryAll($qry);
		return (sizeof($result)>0)?false:true;
	}

	#Find the Latest Rate List  Id (using in Other screen )
	function latestRateList()
	{
		$cDate = date("Y-m-d");	
		$qry = "select a.id as ratelistid from ".$this->tableName." a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$result = $this->query($qry);
		return $result->ratelistid;
	}

	# Date Wise Rate list
	function getFrznPkgRateList($selDate)
	{	
		$qry	= "select id as ratelistid from ".$this->tableName." where date_format(start_date,'%Y-%m-%d')<='$selDate' order by id desc";
		//echo $qry;
		$result = $this->query($qry);
		return $result->ratelistid;
	}

	# update Rec
	function updateRateListRec($pageCurrentRateListId, $endDate)
	{
		$qry = " update ".$this->tableName." set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry;
		$result	= $this->exec($qry);
		return $result;	
	}

	#Checking Rate List Id used
	function chkRateListUse($rateListId)
	{
		/*
		$qry	= "select id from m_process1 where rate_list_id='$rateListId'";
		//echo $qry;
		$result = $this->queryAll($qry);		
		return (sizeof($result)>0)?true:false;
		*/
		$qry = "select id from m_frzn_pkg_rate where rate_list_id='$rateListId'";
		//echo $qry;
		$result = $this->queryAll($qry);		
		return (sizeof($result)>0)?true:false;
	}

	# Get Rate List based on Date
	function validFPRateList($selDate)
	{	
		$qry	= " select id from ".$this->tableName." where date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date='0000-00-00')) order by start_date desc ";
		//echo $qry;
		$result = $this->query($qry);
		return $result->id;
	}
	
}

?>