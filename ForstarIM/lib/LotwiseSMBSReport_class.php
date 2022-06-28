<?php
Class LotwiseSMBSReport
{
	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function LotwiseSMBSReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}

	function getLotReport($fromDate,$tillDate)
	{
		$qry="SELECT trmpc.id,trmpc.chemical_id,sum(trmpc.used_quantity),trpg.lot_Id,trpg.date_Of_Entry FROM `t_rmprocurmentchemical` trmpc left join t_rmreceiptgatepass trpg on trmpc.rmProcurmentOrderId=trpg.procurment_Gate_PassId WHERE trmpc.used_quantity!='0' and trpg.date_Of_Entry>='$fromDate' and trpg.date_Of_Entry<='$tillDate' group by trpg.lot_Id";
		//$qry="SELECT trmpc.id,trmpc.chemical_id,trmpc.used_quantity,trpg.lot_Id FROM `t_rmprocurmentchemical` trmpc left join t_rmreceiptgatepass trpg on trmpc.rmProcurmentOrderId=trpg.procurment_Gate_PassId WHERE trmpc.used_quantity!='0'";
		$qry.=" order by trmpc.id";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getWeighmentDetail($rmlotNm)
	{
		$qry="SELECT trpg.id as receiptid,tmngrml.id as mngid,twde.product_species,mf.name,twde.weight FROM t_rmreceiptgatepass trpg left join t_manage_rmlotid_details tmngrmd on tmngrmd.receipt_gatepass_id=trpg.id join t_manage_rm_lotid tmngrml on tmngrml.id=tmngrmd.rmlot_main_id left join weighment_data_sheet wdt on wdt.rm_lot_id=tmngrml.id left join t_weightment_data_entries twde on twde.weightment_data_sheet_id=wdt.id left join m_fish mf on mf.id=twde.product_species WHERE trpg.lot_Id='$rmlotNm' group by mngid,product_species,twde.weight, wdt.id order by mf.name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
}	
?>