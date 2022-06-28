<?
class OrderProcessing
{  
	/****************************************************************
	This class deals with all the operations relating to PurchaseOrder
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function OrderProcessing(&$databaseConnect)
    	{
      	  $this->databaseConnect =&$databaseConnect;
	}


	function filterPurchaseOrderRecs($selPOId)
	{
		//$qry = "select a.id, a.po_id, a.customer_id, a.payment_term, a.lastdate, a.select_date, b.id, b.fish_id, b.processcode_id, b.eucode_id, b.brand_id, c.id, c.grade_id, c.freezingstage_id, c.frozencode_id, c.mcpacking_id, c.number_mc, c.priceperkg, c.value_usd, c.value_inr, a.extended, a.logstatus, a.logstatusdescr from t_purchaseorder_main a, t_purchaseorder_rm_entry b, t_purchaseorder_grade_entry c where a.id=b.main_id and b.id=c.rmentry_id and a.po_id!=0 and a.id='$selPOId' order by a.po_id asc";
		//$qry = "select a.id, a.po_id, a.customer_id, a.payment_term, a.lastdate, a.select_date, b.id, b.fish_id, b.processcode_id, b.eucode_id, b.brand_id, b.id, b.grade_id, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, b.number_mc, b.priceperkg, b.value_usd, b.value_inr, a.extended, a.logstatus, a.logstatusdescr from t_purchaseorder_main a, t_purchaseorder_rm_entry b where a.id=b.main_id and a.id='$selPOId' order by a.po_id asc";
		$qry = "select a.id, a.po_no, a.customer_id, a.payment_term, a.lastdate, a.select_date, b.id, b.fish_id, b.processcode_id, b.eucode_id_removed, b.brand_id, b.id, b.grade_id, b.freezingstage_id, b.frozencode_id, b.mcpacking_id, b.number_mc, b.priceperkg, b.value_usd, b.value_inr, a.extended, a.logstatus, a.logstatusdescr from t_purchaseorder_main a, t_purchaseorder_rm_entry b where a.id=b.main_id and a.id='$selPOId' order by a.po_no asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	

	
	# Update Purchase Order

function updateOrder($selPOId, $labelling, $paymentStatus, $invoiceNo, $shipmentDate, $selStatus, $isComplete)
	{
		if($isComplete=='')
		{
			$qry	=	" update t_purchaseorder_main set labelling='$labelling', payment_status= '$paymentStatus', invoiceno='$invoiceNo' , shipment_date='$shipmentDate', status='$selStatus' where id=$selPOId";	
		}
		else
		{
			$qry	=	" update t_purchaseorder_main set labelling='$labelling', payment_status= '$paymentStatus', invoiceno='$invoiceNo' , shipment_date='$shipmentDate', status='$selStatus', complete='$isComplete' where id=$selPOId";
		}
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	

	
	# Find Record based on PO id 

	function findPORecord($pOId)
	{
		//$qry	=	"select id, po_id, customer_id, payment_term from t_purchaseorder_main where id=$pOId";
		$qry	=	"select id, po_no, customer_id, payment_term from t_purchaseorder_main where id=$pOId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function findShipmentDetails($pOId)
	{
		$qry	=	"select labelling,payment_status,invoiceno,shipment_date,status,complete  from t_purchaseorder_main where id=$pOId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Check Packing Is Ready -- Used in Order Processing
	function checkFrozenPackingReady($poEntyId)
	{
		$qry	=	"select id, number_mc, number_loose_slab from t_dailyfrozenpacking_allocate where po_rm_id='$poEntyId' union all select id, number_mc, number_loose_slab from t_dailyfrozenpacking_allocate_rmlotid where po_rm_id='$poEntyId'";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach($result as $rec)
			{
				$numMc	= $rec[1];
				$numLooseSlab = $rec[2];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}
		}	
		return  $totalNumMC;
	}
	


/*#Check Packing Is Ready -- Used in Order Processing
	function checkFrozenPackingReady_old($poId, $fishId, $processCodeId, $gradeId)
	{
		$qry	=	"select a.id, b.number_mc, b.number_loose_slab from t_dailyfrozenpacking_entry a, t_dailyfrozenpacking_grade b where a.id=b.entry_id and a.fish_id='$fishId' and  a.processcode_id='$processCodeId' and a.export_lot_id=$poId and b.grade_id='$gradeId'";
		echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach($result as $rec)
			{
				$numMc	= $rec[1];
				$numLooseSlab = $rec[2];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}
		}	
		return  $totalNumMC;
	}
	*/
	/*function checkFrozenPackingReady($poId)
	{
		$qry	=	"select id from t_dailyfrozenpacking where export_lot_id=$poId";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return  ( sizeof($result) > 0 ) ? true : false ;
	}*/
	
	
	
	# Returns all Not Completed Records
	/*
	function fetchNotCompleteRecords()
	{
		$qry	=	"select id, po_id from t_purchaseorder_main where complete <>  'C'  or   complete is null and po_id!=0";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

	function fetchPONotComplete($fromDate=null, $tillDate=null, $invoiceType=null)
	{		
		
		$qry = "SELECT a.id, a.po_no, a.customer_id, b.customer_name FROM t_purchaseorder_main a JOIN m_customer b ON a.customer_id = b.id WHERE a.complete IS NULL OR a.complete!='C' ORDER BY a.id DESC ";
		//$qry = " select  a.id, a.po_no,a.customer_id,b.customer_name from t_purchaseorder_main a join m_customer b on  a.customer_id=b.id  where a.complete IS NULL order by a.id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		//$resultArr = array(''=>'-- Select --');
	/*	if (sizeof($result)>0) {
			$i = 0;
			foreach ($result as $v) {
				$id 	= $v[0];
				$poNo 	= $v[1];
				//$fznArr[$qelMainId] = $qelName;
				$resultArr[$id] =$poNo;
				//$i++;
			}
		}
		return $resultArr;*/
		
		return $result;
	}




	# Get SOrders Based on selection
	function fetchNotCompleteRecords($fromDate=null, $tillDate=null, $invoiceType=null)
	{		
		$qry = " select  a.id, a.invoice_no, a.proforma_no, a.sample_invoice_no, b.customer_name, a.invoice_type from t_purchaseorder_main a join m_customer b on  a.customer_id=b.id where a.proforma_no!=0 or a.sample_invoice_no!=0 order by a.invoice_date desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		//$resultArr = array(''=>'-- Select --');
		if (sizeof($result)>0) {
			$i = 0;
			foreach ($result as $v) {
				$soNo 	= $v[1];
				$pfNo 	= $v[2];
				$saNo	= $v[3];
				$invType = $v[5];
				$invoiceNo = "";
				if ($soNo!=0) $invoiceNo=$soNo;
				else if ($invType=='T') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "$saNo";
				$distName = $v[4];

				$displayTxt = $invoiceNo." (".$distName.")";
				//$resultArr[$v[0]] = $displayTxt;
				$resultArr[$i] = array($v[0],$displayTxt);
				$i++;
			}
		}
		return $resultArr;
	}
	
	# Get All Products in PO
	function getProductsInPO($poMainId,$entryID)
	{
		
/*echo		$qry = "select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tpore.frozencode_id, tpore.mcpacking_id, tpore.wt_type, tpore.grade_id,mg.id
			from 
				t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id 
				left join m_processcode mpc on tpore.processcode_id=mpc.id 
				left join m_brand mb on tpore.brand_id=mb.id
				left join m_grade mg on tpore.grade_id=mg.id 
				left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id
				left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id 
				left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id left join t_invoice_main tm on tm.po_id=tpore.main_id left join m_eucode me on tm.eucode=me.id
			where tpore.main_id='$poMainId' and tpore.id='$entryID' ";*/
			$qry = "select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tpore.frozencode_id, tpore.mcpacking_id, tpore.wt_type, tpore.grade_id,mg.id,tdfpa.id,tdfpa.number_mc,'0' as rmlotid,'0' as rmlotNm,tfprm.id from t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id left join m_processcode mpc on tpore.processcode_id=mpc.id left join m_brand mb on tpore.brand_id=mb.id left join m_grade mg on tpore.grade_id=mg.id left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id left join t_invoice_main tm on tm.po_id=tpore.main_id left join m_eucode me on tm.eucode=me.id left join t_dailyfrozenpacking_allocate tdfpa on tdfpa.po_rm_id=tpore.id left join t_dailyfrozenpacking_po tfprm on  tfprm.id=tdfpa.po_entry_id where tpore.main_id='$poMainId' and tpore.id='$entryID' and tdfpa.po_rm_id='$entryID' union all select tpore.id, mf.name as fishName, mpc.code as processCode, me.code as euCode, if (tpore.brand_from='C',( select brand from m_customer_brand where id=tpore.brand_id),mb.brand) as brand, mg.code as gradeCode, mfs.rm_stage as freezingStage, mfp.code as frznCode, mmcp.code as mcPkg, tpore.number_mc, tpore.priceperkg, tpore.value_usd, tpore.value_inr, tpore.brand_from, tpore.processcode_id, tpore.frozencode_id, tpore.mcpacking_id, tpore.wt_type, tpore.grade_id,mg.id,tdfpa.id,tdfpa.number_mc,tmgrm.id as  rmlotid,concat(alpha_character,rm_lotid) as rmlotNm,tfprm.id from t_purchaseorder_rm_entry tpore left join m_fish mf on tpore.fish_id = mf.id left join m_processcode mpc on tpore.processcode_id=mpc.id left join m_brand mb on tpore.brand_id=mb.id left join m_grade mg on tpore.grade_id=mg.id left join m_freezingstage mfs on mfs.id=tpore.freezingstage_id left join m_frozenpacking mfp on mfp.id=tpore.frozencode_id left join m_mcpacking mmcp on mmcp.id=tpore.mcpacking_id left join t_invoice_main tm on tm.po_id=tpore.main_id left join m_eucode me on tm.eucode=me.id left join t_dailyfrozenpacking_allocate_rmlotid tdfpa on tdfpa.po_rm_id=tpore.id left join t_dailyfrozenpacking_po_rmlotid tfprm on  tfprm.id=tdfpa.po_entry_id left join t_manage_rm_lotid tmgrm on  tmgrm.id=tfprm.rm_lot_id where tpore.main_id='$poMainId' and tpore.id='$entryID' and tdfpa.po_rm_id='$entryID'";

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

}