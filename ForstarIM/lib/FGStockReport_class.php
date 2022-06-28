<?php
Class FGStockReport
{
	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function FGStockReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	# get company 
	function getCompany($fromDate,$toDate)
	{
		$qry = "select mbc.id, mbc.display_name from t_dailyfrozenpacking_main dfm join m_billing_company mbc on mbc.id=dfm.company where dfm.select_date>='$fromDate' and dfm.select_date<='$toDate' group by dfm.company order by mbc.display_name asc";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllUnit($fromDate,$toDate)
	{
		$qry = " select mp.id, mp.name from t_dailyfrozenpacking_main dfm join m_plant mp on mp.id=dfm.unit where dfm.select_date>='$fromDate' and dfm.select_date<='$toDate' group by dfm.unit order by mp.name asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Records For a Selected Date 
	function getDFPForADate($fromDate, $toDate, $selCompany,$unit)
	{

			if ($fromDate && $toDate) {
			$whr  .= "dfp.select_date>='$fromDate' and dfp.select_date<='$toDate'" ;
			
		} 
	
		if ($unit) $whr .= " and dfp.unit='$unit' ";

		$qry="select dfp.id as mainid, dfp.select_date as selDate,mpc.code as processCode,concat(mfp.decl_wt, mmcp.code, mfp.unit),mfs.rm_stage,mb.brand,mg.glaze  from t_dailyfrozenpacking_main dfp left join t_dailyfrozenpacking_entry tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id left join m_processcode mpc on mpc.id=tdfpe.processcode_id left join t_fznpakng_quick_entry tfqe on tfqe.id=tdfpe.quick_entry_list_id
		left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id left join m_brand mb on mb.id=tdfpe.brand_id left join  m_glaze mg on mg.id=mfp.glaze_id";

		
	/*	$qry1="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit from t_dailyfrozenpacking_main dfp left join t_dailyfrozenpacking_entry tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id";
		$qry1.=" where $whr";
		$qry1.=" group by selDate,unit";

		$qry2="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit from t_dailyfrozenpacking_main_rmlotid dfp left join t_dailyfrozenpacking_entry_rmlotid tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id ";
		$qry2.=" where $whr";
		$qry2.=" group by selDate,unit";

		$qry="select mainid,selDate,sum(availableQty),unit from ($qry1 union all $qry2) dum group by selDate,unit order by selDate ";
				
*/
	echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	
	
	


}	
?>