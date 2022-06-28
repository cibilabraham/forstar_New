<?php
Class DailyProcessingReport
{
	/****************************************************************
	This class deals with all the operations relating to Daily Rate 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DailyProcessingReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	# Fetch Plant/unit from dailyprocessing table based on date
	function fetchDailyProcessingPlantRecords($selectedDate)
	{
		$qry	=	"select distinct a.unit from t_dailyprocessing a, t_dailyprocessing_grade b where a.id=b.lot_id and a.date='$selectedDate' and a.flag=1";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch Lot No from dailyprocessing table based on date
	function fetchDailyProcessingLotNoRecords($selectedDate)
	{
		$qry	=	"select distinct a.id,a.lot_no from t_dailyprocessing a, t_dailyprocessing_grade b where a.id=b.lot_id and a.date='$selectedDate' and a.flag=1";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Fetch fish from dailyprocessing table based on date
	function fetchDailyProcessingFishRecords($selectedDate)
	{
		$qry	=	"select distinct b.fish_id,c.name from t_dailyprocessing a, t_dailyprocessing_grade b,m_fish c where a.id=b.lot_id and b.fish_id=c.id and a.date='$selectedDate' and a.flag=1";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Fetch Process Code from dailyprocessing table based on date
	function fetchDailyProcessingProcessCodeRecords($selectedDate)
	{
		$qry	=	"select distinct b.processcode_id,c.code from t_dailyprocessing a, t_dailyprocessing_grade b,m_processcode c where a.id=b.lot_id and b.processcode_id=c.id and a.date='$selectedDate' and a.flag=1";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Filter all records based on the search criteria
	function filterDailyCatchReportRecords($selectUnit, $selLotNoId, $selectFish, $selectProcessCode, $selectedDate)
	{
		$qry	=	"select a.id,b.fish_id,b.processcode_id,b.packing_id,b.grade_id,b.quantity from t_dailyprocessing a, t_dailyprocessing_grade b where a.id=b.lot_id and a.unit='$selectUnit' and b.lot_id='$selLotNoId' and b.fish_id='$selectFish' and processcode_id='$selectProcessCode' and a.date='$selectedDate' and b.quantity!=0 and a.flag=1";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
}	
?>