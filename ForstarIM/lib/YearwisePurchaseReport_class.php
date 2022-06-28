<?php
Class YearwisePurchaseReport
{
	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function YearwisePurchaseReport(&$databaseConnect)
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

	function getAllFish($fromDate,$toDate)
	{
		$qry="SELECT tdore.fish_id as fishId,mf.name as name FROM t_purchaseorder_main tpo left join t_purchaseorder_rm_entry tdore on  tpo.id=tdore.main_id left join m_fish mf on mf.id=tdore.fish_id where tpo.select_date>='$fromDate' and tpo.select_date<='$toDate' group by name";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	# Get Records For a Selected Date 
	function getPOForAFish($fromDate, $toDate, $fishId)
	{

			if ($fromDate && $toDate) {
			$whr  .= "tpom.select_date>='$fromDate' and tpom.select_date<='$toDate'" ;
			
			} 
	
		$qry="SELECT SUM(tporme.priceperkg) FROM `t_purchaseorder_rm_entry` tporme left join t_purchaseorder_main tpom on tporme.main_id=tpom.id WHERE tporme.fish_id='$fishId' ";
		$qry.=" and $whr";
		$qry.=" group by fish_id";
		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}

	function getPOForADate($fromDate, $toDate)
	{

			if ($fromDate && $toDate) {
			$whr  .= "tpom.select_date>='$fromDate' and tpom.select_date<='$toDate'" ;
			
			} 
	
		$qry="SELECT SUM(tporme.priceperkg) FROM `t_purchaseorder_rm_entry` tporme left join t_purchaseorder_main tpom on tporme.main_id=tpom.id where";
		$qry.="  $whr";
		
		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
}	
?>