<?php
Class PendingRMLotReport
{
	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PendingRMLotReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	function getRmLOT($fromDate,$toDate,$unit)
	{
		$qry="SELECT tmngrml.id,concat(tmngrml.alpha_character,tmngrml.rm_lotid),tmngrml.lot_id_origin,
		tmngrml.created_on,tmngrml.unit_id,mp.name,tmngrml.active FROM `t_manage_rm_lotid` tmngrml left join m_plant mp on mp.id=tmngrml.unit_id where tmngrml.created_on>='$fromDate' and tmngrml.created_on<='$toDate' and tmngrml.id not in (select lot_id_origin from t_manage_rm_lotid) ";
		//$qry="SELECT unit_id,id,concat(alpha_character,rm_lotid),lot_id_origin,created_on FROM `t_manage_rm_lotid` where created_on>='$fromDate' and created_on<='$toDate' and id not in (select lot_id_origin from t_manage_rm_lotid)  and active='1'";
		
		
		if($unit!='') $qry.=" and tmngrml.unit_id='$unit'";
		$qry.=" order by tmngrml.id";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllUnit($fromDate,$toDate)
	{
		$qry = "SELECT mp.id,mp.name FROM `t_manage_rm_lotid` tmngrml left join m_plant mp on mp.id=tmngrml.unit_id where tmngrml.created_on>='$fromDate' and tmngrml.created_on<='$toDate' and tmngrml.id not in (select lot_id_origin from t_manage_rm_lotid) group by mp.id";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}
}	
?>