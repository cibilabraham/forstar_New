<?php
Class RMVarianceReport
{
	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function RMVarianceReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	function getRmLot($fromDate,$toDate)
	{
		$qry="SELECT id,concat(alpha_character,rm_lotid),lot_id_origin FROM `t_manage_rm_lotid` where created_on>='$fromDate' and created_on<='$toDate' and id not in (select lot_id_origin from t_manage_rm_lotid)  and active='1'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getRmLotRec($fromDate,$toDate,$rmlotid)
	{
		$qry="SELECT id,concat(alpha_character,rm_lotid),lot_id_origin FROM `t_manage_rm_lotid` where created_on>='$fromDate' and created_on<='$toDate' and id not in (select lot_id_origin from t_manage_rm_lotid)  and active='1'";
		if($rmlotid!='') $qry.=" and id='$rmlotid'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSupplierData($rmlotid)
	{
		$qry="select s.id,s.name from t_manage_rmlotid_details tmngrm left join supplier s on tmngrm.supplier_id=s.id where tmngrm.rmlot_main_id='$rmlotid' group by s.id";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDFPForADateRmLot($rmlotid,$supplier) 
	{
		$qry="SELECT s.name,mem.name as gate_supervisor,trmrgp.date_Of_Entry,mem2.name,tmngrm.id,tmngrmd.receipt_id,tmngrmd.supplier_id,tmngrmd.farm_id FROM `t_manage_rm_lotid` tmngrm  left join t_manage_rmlotid_details tmngrmd on tmngrm.id=tmngrmd.rmlot_main_id left join t_rm_receipt_gatepass_supplier trmrgps on trmrgps.id=tmngrmd.receipt_id  left join t_rmreceiptgatepass trmrgp on trmrgp.id= tmngrmd.receipt_gatepass_id left join m_rm_gate_pass mrmgp on mrmgp.procurment_id=trmrgp.procurment_Gate_PassId left join supplier s on  s.id=tmngrmd.supplier_id left join m_employee_master mem on mem.id=mrmgp.supervisor left join m_employee_master mem2 on mem2.id=trmrgp.verified where tmngrm.id='$rmlotid' ";
		if($supplier!='') $qry.=" and tmngrmd.supplier_id='$supplier'";
		$qry.=" order by tmngrm.id";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getGroupedWeighment($rmlotId,$supplierId,$farmId)
	{
		$qry="select wds.data_sheet_date,mpc.code,mf.name,twde.count_code,twde.weight,twde.soft_per from weighment_data_sheet wds left join t_weightment_data_entries twde on wds.id=twde.weightment_data_sheet_id left join m_fish mf on mf.id=twde.product_species left join m_processcode mpc on mpc.id=twde.process_code_id where wds.rm_lot_id='$rmlotId' and twde.supplier_name='$supplierId'";
 		//$qry="select wds.data_sheet_date,twde.count_code,twde.weight, from weighment_data_sheet wds left join t_weightment_data_entries twde on wds.id=twde.weightment_data_sheet_id where rm_lot_id='$rmlotId' and twde.supplier_name='$supplierId'";
		if($farmId!='0') $qry.=" and twde.pond_name='$farmId'";
		//echo "<br>==>Grouped<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	


	# Get Records For a Selected Date 
	function getDFPForADate($fromDate, $toDate) 
	{
		$qry="SELECT tmngrm.id,concat(tmngrm.alpha_character,tmngrm.rm_lotid) as rmlot,s.name,mem.name as gate_supervisor,trmrgp.date_Of_Entry,mem2.name,tmngrm.id,tmngrmd.receipt_id,tmngrmd.supplier_id,tmngrmd.farm_id FROM `t_manage_rm_lotid` tmngrm  left join t_manage_rmlotid_details tmngrmd on tmngrm.id=tmngrmd.rmlot_main_id left join t_rm_receipt_gatepass_supplier trmrgps on trmrgps.id=tmngrmd.receipt_id  left join t_rmreceiptgatepass trmrgp on trmrgp.id= tmngrmd.receipt_gatepass_id left join m_rm_gate_pass mrmgp on mrmgp.procurment_id=trmrgp.procurment_Gate_PassId left join supplier s on  s.id=tmngrmd.supplier_id left join m_employee_master mem on mem.id=mrmgp.supervisor left join m_employee_master mem2 on mem2.id=trmrgp.verified where tmngrm.created_on>='$fromDate' and tmngrm.created_on<='$toDate' order by tmngrm.id";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

}	
?>