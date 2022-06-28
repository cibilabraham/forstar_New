<?php
class DebitNoteReport
{
	/****************************************************************
	This class deals with all the operations relating to DebitNoteReport
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DebitNoteReport(&$databaseConnect)
    {
		$this->databaseConnect =&$databaseConnect;
	}

	
	function getShippingLineRecs($fromDate, $tillDate)
	{		
		$qry = "select msc.id, msc.name as shippingLine
				from t_container_entry tce join t_container_main tcm on tce.main_id=tcm.id 
				left join m_shipping_company msc on tcm.shipping_line_id=msc.id
				left join t_invoice_main tim on tim.id=tce.invoice_id
				where tcm.container_id!=0 and tcm.container_no is not null and tim.invoice_date>='$fromDate' and tim.invoice_date<='$tillDate' group by tcm.vessal_details ";

		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		if (sizeof($result)>0) {
			foreach ($result as $rec) {				
				$resultArr[$rec[0]] = $rec[1];
			}
		}
		return $resultArr;
	}

	function debitNoteRecs($fromDate, $tillDate, $selShippingLineId)
	{		
		$whr = " tim.invoice_date>='$fromDate' and tim.invoice_date<='$tillDate' and tcm.container_id!=0 and tcm.container_no is not null and tim.confirmed='Y' ";

		if ($selShippingLineId) 	$whr .= " and tcm.shipping_line_id='$selShippingLineId' ";
		
		$groupBy	= " tim.id, tcm.shipping_line_id ";
		$orderBy	= "tim.invoice_date asc";			
		
		$qry = "select tim.id, msc.name as shippingLine, tim.bill_ladding_no, tim.bill_ladding_date, tim.dn_total_bkg, tim.dn_gross_amt, tim.dn_tds_amt, tim.dn_net_amt, tim.dn_chq_no, tim.dn_chq_date, tim.dn_freight, tim.dn_bkg_freight, tim.dn_ex_rate, tim.exp_invoice_no
				
			from 
			t_invoice_main tim  left join t_container_entry tce on tim.id=tce.invoice_id
			left join t_container_main tcm on tce.main_id=tcm.id 
			left join m_shipping_company msc on tcm.shipping_line_id=msc.id";
			//echo $qry;

		if ($whr!="") 	  $qry .= " where ".$whr; 
		if ($groupBy!="") $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

}
?>