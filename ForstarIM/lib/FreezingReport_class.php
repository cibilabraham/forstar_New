<?php
Class FreezingReport
{
	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function FreezingReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	function getFreezingDetail($fromDate,$toDate,$rmlotid)
	{
		if($rmlotid=="")
		{
			$qry="select * FROM (SELECT tdfpm.id as freezId,tdfpm.select_date as selectDate,'' as rmlotid,'' as rmlotname ,tdfpe.fish_id as fishid,mf.name as fish,tdfpe.brand_id as brandid ,mb.brand as brand,tdfpe.mcpacking_id as mcpackId,mmcp.code as mcpcode,tdfpg.grade_id as gradeid,mg.code as gradeCode,tdfpg.number_mc as mc ,tdfpg.number_loose_slab as lc,mfp.decl_wt,mgl.glaze from t_dailyfrozenpacking_main tdfpm left join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_fish mf on mf.id=tdfpe.fish_id left join m_brand mb on mb.id=tdfpe.brand_id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id left join m_grade mg on mg.id=tdfpg.grade_id left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id left join m_glaze mgl on mgl.id=mfp.glaze_id 
			union all
			SELECT tdfpm.id as freezId,tdfpm.select_date  as selectDate,tmngrml.id as rmlotid,concat(tmngrml.alpha_character,tmngrml.rm_lotid) as rmlotname ,tdfpe.fish_id as fishid,mf.name as fish,tdfpe.brand_id as brandid ,mb.brand as brand,tdfpe.mcpacking_id as mcpackId,mmcp.code as mcpcode,tdfpg.grade_id as gradeid,mg.code as gradeCode,tdfpg.number_mc as mc ,tdfpg.number_loose_slab as lc,mfp.decl_wt,mgl.glaze from t_dailyfrozenpacking_main_rmlotid tdfpm left join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_fish mf on mf.id=tdfpe.fish_id left join m_brand mb on mb.id=tdfpe.brand_id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id left join m_grade mg on mg.id=tdfpg.grade_id left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id left join m_glaze mgl on mgl.id=mfp.glaze_id left join t_manage_rm_lotid  tmngrml on tmngrml.id=tdfpm.rm_lot_id)  dum where  selectDate>='$fromDate' and selectDate<='$toDate' order by selectDate asc";
			//$qry="SELECT unit_id,id,concat(alpha_character,rm_lotid),lot_id_origin,created_on FROM `t_manage_rm_lotid` where created_on>='$fromDate' and created_on<='$toDate' and id not in (select lot_id_origin from t_manage_rm_lotid)  and active='1'";
		}
		else
		{
			$qry="SELECT tdfpm.id as freezId,tdfpm.select_date  as selectDate,tmngrml.id as rmlotid,concat(tmngrml.alpha_character,tmngrml.rm_lotid) as rmlotname ,tdfpe.fish_id as fishid,mf.name as fish,tdfpe.brand_id as brandid ,mb.brand as brand,tdfpe.mcpacking_id as mcpackId,mmcp.code as mcpcode,tdfpg.grade_id as gradeid,mg.code as gradeCode,tdfpg.number_mc as mc ,tdfpg.number_loose_slab as lc,mfp.decl_wt,mgl.glaze  from t_dailyfrozenpacking_main_rmlotid tdfpm left join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_fish mf on mf.id=tdfpe.fish_id left join m_brand mb on mb.id=tdfpe.brand_id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id left join m_grade mg on mg.id=tdfpg.grade_id left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id left join m_glaze mgl on mgl.id=mfp.glaze_id  left join t_manage_rm_lotid  tmngrml on tmngrml.id=tdfpm.rm_lot_id where  tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$toDate' and tdfpm.rm_lot_id='$rmlotid' order by selectDate asc";
		}
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	/*function getRmLOT($fromDate,$toDate,$unit)
	{
		$qry="SELECT tmngrml.id,concat(tmngrml.alpha_character,tmngrml.rm_lotid),tmngrml.lot_id_origin,
		tmngrml.created_on,tmngrml.unit_id,mp.name,tmngrml.active FROM `t_manage_rm_lotid` tmngrml left join m_plant mp on mp.id=tmngrml.unit_id where tmngrml.created_on>='$fromDate' and tmngrml.created_on<='$toDate' and tmngrml.id not in (select lot_id_origin from t_manage_rm_lotid) ";
		//$qry="SELECT unit_id,id,concat(alpha_character,rm_lotid),lot_id_origin,created_on FROM `t_manage_rm_lotid` where created_on>='$fromDate' and created_on<='$toDate' and id not in (select lot_id_origin from t_manage_rm_lotid)  and active='1'";
		
		
		if($unit!='') $qry.=" and tmngrml.unit_id='$unit'";
		$qry.=" order by tmngrml.id";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}*/

	function getAllRMLot($fromDate,$toDate)
	{
		$qry = "SELECT tmngrml.id as rmlotid,concat(tmngrml.alpha_character,tmngrml.rm_lotid) as rmlotname FROM  t_dailyfrozenpacking_main_rmlotid tdfpm left join t_manage_rm_lotid  tmngrml on tmngrml.id=tdfpm.rm_lot_id where tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$toDate' group by rmlotid";
		//echo $qry;
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
}	
?>