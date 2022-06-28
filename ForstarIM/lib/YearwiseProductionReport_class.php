<?php
Class YearwiseProductionReport
{
	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function YearwiseProductionReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	function getAllUnit($fromDate,$toDate)
	{
		$qry1="SELECT tdfm.unit as fishId,mp.name as name FROM t_dailyfrozenpacking_main tdfm left join t_dailyfrozenpacking_entry tdfpe on  tdfm.id=tdfpe.main_id left join m_plant mp on mp.id=tdfm.unit where tdfm.select_date>='$fromDate' and tdfm.select_date<='$toDate'";
		
		$qry2="SELECT tdfm.unit as fishId,mp.name as name FROM t_dailyfrozenpacking_main_rmlotid tdfm left join t_dailyfrozenpacking_entry_rmlotid tdfpe on  tdfm.id=tdfpe.main_id left join m_plant mp on mp.id=tdfm.unit where tdfm.select_date>='$fromDate' and tdfm.select_date<='$toDate'";
		
		$qry="select * from($qry1 union all $qry2)  dum group by name";
		//echo $qry;
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllFish($fromDate,$toDate,$unit)
	{
		$qry1="SELECT tdfpe.fish_id as fishId,mf.name as name FROM t_dailyfrozenpacking_main tdfm left join t_dailyfrozenpacking_entry tdfpe on  tdfm.id=tdfpe.main_id left join m_fish mf on mf.id=tdfpe.fish_id where tdfm.select_date>='$fromDate' and tdfm.select_date<='$toDate'";
		
		$qry2="SELECT tdfpe.fish_id  as fishId,mf.name as name FROM t_dailyfrozenpacking_main_rmlotid tdfm left join t_dailyfrozenpacking_entry_rmlotid tdfpe on  tdfm.id=tdfpe.main_id left join m_fish mf on mf.id=tdfpe.fish_id where tdfm.select_date>='$fromDate' and tdfm.select_date<='$toDate'";
		if($unit!='')
		{
			$qry1.=" and tdfm.unit='$unit'";	
			$qry2.=" and tdfm.unit='$unit'";
		}
		
		$qry="select * from($qry1 union all $qry2)  dum group by name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	# Get Records For a Selected Date 
	function getDFPForAFish($fromDate, $toDate, $fishId,$unitId)
	{

			if ($fromDate && $toDate) {
			$whr  .= "dfp.select_date>='$fromDate' and dfp.select_date<='$toDate'" ;
			
			} 
	
		if ($unitId) $whr .= " and dfp.unit='$unitId' ";

		$qry1="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit,tdfpe.fish_id as fish from t_dailyfrozenpacking_main dfp left join t_dailyfrozenpacking_entry tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id where tdfpe.fish_id='$fishId'";
		$qry1.=" and $whr";
		$qry1.=" group by selDate,fish";

		$qry2="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit,tdfpe.fish_id as fish  from t_dailyfrozenpacking_main_rmlotid dfp left join t_dailyfrozenpacking_entry_rmlotid tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id where tdfpe.fish_id='$fishId'";
		$qry2.=" and $whr";
		$qry2.=" group by selDate,fish";

		$qry="select mainid,selDate,sum(availableQty),unit,fish from ($qry1 union all $qry2) dum group by fish order by selDate ";
		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}

	# Get Records For a Selected Date 
	function getDFPForADate($fromDate, $toDate,$unitId)
	{

			if ($fromDate && $toDate) {
			$whr  .= "dfp.select_date>='$fromDate' and dfp.select_date<='$toDate'" ;
			
			} 
	
		if ($unitId) $whr .= " and dfp.unit='$unitId' ";

		$qry1="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit,tdfpe.fish_id as fish from t_dailyfrozenpacking_main dfp left join t_dailyfrozenpacking_entry tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id where ";
		$qry1.="  $whr";
		$qry1.=" group by selDate";

		$qry2="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit,tdfpe.fish_id as fish  from t_dailyfrozenpacking_main_rmlotid dfp left join t_dailyfrozenpacking_entry_rmlotid tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id where ";
		$qry2.="  $whr";
		$qry2.=" group by selDate";

		$qry="select mainid,selDate,sum(availableQty),unit,fish from ($qry1 union all $qry2) dum order by selDate ";
		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
}	
?>