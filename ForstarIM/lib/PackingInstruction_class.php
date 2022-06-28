<?php
class PackingInstruction
{
	/****************************************************************
	This class deals with all the operations relating to Product Identifier Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PackingInstruction(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Find Record based on SO id 
	function getSORecord($sOId)
	{
		$qry = "select a.id, a.so, a.distributor_id, a.invoice_date, a.createdby, a.status, b.name, a.status_id, a.payment_status, a.dispatch_date, a.gross_wt, a.transporter_id, a.docket_no, a.transporter_rate_list_id, a.complete_status, a.tax_applied, a.round_value, a.grand_total_amt, a.invoice_type, a.adnl_item_total_wt, a.last_date, a.extended, a.trans_oc_rate_list_id, a.discount, a.discount_remark, a.discount_percent, a.discount_amt, a.octroi_exempted, a.oec_no, a.oec_date, a.oec_issued_date, a.proforma_no, a.proforma_date, a.sample_invoice_no, a.sample_invoice_date, a.net_wt, a.num_box, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.state_id from t_salesorder a, m_distributor b where a.distributor_id=b.id and a.id='$sOId' ";
		return $this->databaseConnect->getRecord($qry);
	}

	#Filter sales Order Recs
	function salesOrderEntryRecs($selSOId)
	{			
		$qry = "select a.id, a.salesorder_id, a.product_id, a.rate, a.quantity, a.total_amount, a.mc_pkg_id, a.mc_pack, a.loose_pack, a.dist_mgn_state_id, a.tax_percent, a.p_gross_wt, a.p_mc_wt, a.free_pkts, a.basic_rate, c.id, c.remarks, a.mc_pkg_wt_id from t_salesorder_entry a left join t_pkng_inst b on b.so_id=a.salesorder_id left join t_pkng_inst_prd c on b.id=c.pkng_inst_id and a.product_id=c.product_id  where a.salesorder_id='$selSOId' ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Sales Order recs
	function getSORecords()
	{
		$qry = "select a.id, a.so, b.name, a.proforma_no, a.sample_invoice_no, a.invoice_type from t_salesorder a, m_distributor b where a.distributor_id=b.id order by a.invoice_date desc, a.so desc";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	
	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $fromDate, $tillDate)
	{
		$whr = "  a.so_id=b.id and b.distributor_id=c.id and b.last_date>='$fromDate' and b.last_date<='$tillDate' ";
	
		$orderBy  = "  b.last_date desc ";

		$limit 	  = " $offset,$limit";

		$qry = "select a.id, a.so_id, b.invoice_type, b.so, b.proforma_no,  b.sample_invoice_no, c.name, a.total_gross_wt, a.confirm_status, b.last_date, b.complete_status from t_pkng_inst a, t_salesorder b, m_distributor c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;		

		return $this->databaseConnect->getRecords($qry);
	}

	# Returns all Records
	function fetchAllRecords($fromDate, $tillDate)
	{
		$whr = "  a.so_id=b.id and b.distributor_id=c.id and b.last_date>='$fromDate' and b.last_date<='$tillDate'";
	
		$orderBy  = "  b.last_date desc ";

		$qry = "select a.id, a.so_id, b.invoice_type, b.so, b.proforma_no, b.sample_invoice_no, c.name, a.total_gross_wt, a.confirm_status, b.last_date, b.complete_status from t_pkng_inst a, t_salesorder b, m_distributor c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;		
		
		return $this->databaseConnect->getRecords($qry);
	}
	

	# Get a Record based on id
	function find($pkngInstructionId)
	{		
		$qry = "select a.id, a.so_id, b.invoice_type, b.so, b.proforma_no, b.sample_invoice_no, c.name, a.tot_mc_actual_wt, a.tot_additional_item_wt, a.total_gross_wt, a.mc_done_by, a.verified_by, a.confirm_status, a.tot_prd_mc_actual_wt from t_pkng_inst a, t_salesorder b, m_distributor c where a.so_id=b.id and b.distributor_id=c.id and a.id=$pkngInstructionId";

		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updatePackingInstruction($pkngInstructionId, $mcTotalActualWt, $additionalItemTotalWt, $totalGrossWt, $mcDoneBy, $verifiedBy, $packingConfirm, $prdMCTotalActualWt)
	{
		$qry = "update t_pkng_inst set tot_mc_actual_wt='$mcTotalActualWt', tot_additional_item_wt='$additionalItemTotalWt', total_gross_wt='$totalGrossWt', mc_done_by='$mcDoneBy', verified_by='$verifiedBy', confirm_status='$packingConfirm', tot_prd_mc_actual_wt='$prdMCTotalActualWt' where id=$pkngInstructionId ";
				
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function addPkngInstProduct($pkngInstructionId, $selProduct, $remarks)
	{
		$qry = "insert into t_pkng_inst_prd (pkng_inst_id, product_id, remarks) values('$pkngInstructionId', '$selProduct', '$remarks')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}

	function addProductBatchNo($pkgInstPrdEId, $productBatchNo)
	{
		$qry = "insert into t_pkng_inst_prd_btch (inst_prd_eid, batch_no) values('$pkgInstPrdEId', '$productBatchNo')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}

	function addPrdPkngDtls($pkgInstPrdEId, $pkngMaterialBatchNo, $pkngMaterialName, $pkngQtyUsed)
	{
		$qry = "insert into t_pkng_inst_prd_pkng (inst_prd_eid, batch_no, material_name, qty) values('$pkgInstPrdEId', '$pkngMaterialBatchNo', '$pkngMaterialName', '$pkngQtyUsed')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}	


	function addMCActualGrossWt($pkngInstructionId, $mcPackId, $mcActualGrossWt, $mcProductId, $rowId)
	{
		$qry = "insert into t_pkng_inst_mc_wt (pkng_inst_id, pkng_id, pkng_wt, product_id, row_id) values('$pkngInstructionId', '$mcPackId', '$mcActualGrossWt', '$mcProductId', '$rowId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;		
	}


	# Add Aditional Item
	function addAdnlItem($pkngInstructionId, $itemName, $itemWt)
	{
		$qry =	"insert into t_pkng_inst_adnl_item (pkng_inst_id, item_name, item_wt) values('$pkngInstructionId', '$itemName', '$itemWt')";

		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) 	$this->databaseConnect->commit();
		else 			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get ProductBtch Recs
	function getProductBtchRecs($pkgInstPrdEId)
	{
		$qry = "select a.id, a.batch_no from t_pkng_inst_prd_btch a where inst_prd_eid='$pkgInstPrdEId' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getProductPkngDtlsRecs($pkgInstPrdEId)
	{
		$qry = "select a.id, a.batch_no, a.material_name, a.qty from t_pkng_inst_prd_pkng a where inst_prd_eid='$pkgInstPrdEId' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAdnlItemRecs($pkngInstructionId, $selSOId)
	{
		$resultArr = array();
		$qry1 = " select id, item_name, item_wt from t_salesorder_other where salesorder_id='$selSOId'";
		$soResult = $this->databaseConnect->getRecords($qry1);
		
		$i = 0;
		if (sizeof($soResult)>0) {
			foreach ($soResult as $sr) {
				$id 		= $sr[0];
				$itemName	= $sr[1];
				$itemWt		= $sr[2];
				//$resultArr[$i] = array($id, $itemName, $itemWt);
				$resultArr[$itemName] = $itemWt;
				$i++;
			}
		} 		
		$qry = "select id, item_name, item_wt from t_pkng_inst_adnl_item where pkng_inst_id='$pkngInstructionId' ";
		$pkngInstAdnlRecs = $this->databaseConnect->getRecords($qry);
		if (sizeof($pkngInstAdnlRecs)>0) {
			foreach ($pkngInstAdnlRecs as $sr) {
				$id 		= $sr[0];
				$itemName	= $sr[1];
				$itemWt		= $sr[2];
				//$resultArr[$i] = array($id, $itemName, $itemWt);
				$resultArr[$itemName] = $itemWt;
				$i++;
			}
		}
		return $resultArr;
	}

	

	/*
	# Delete From Product Table
	function delPkngInstPrdRec($pkngInstructionId)
	{
		$qry	= " delete from t_pkng_inst_prd where pkng_inst_id='$pkngInstructionId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function delPkngInstMCWtRec($pkngInstructionId)
	{

	}
	*/

	# Delete All Main Entry Rec
	function delPkngInstAllMainEntryRec($pkngInstructionId)
	{
		$qry1	= " delete from t_pkng_inst_prd where pkng_inst_id='$pkngInstructionId'";
		$result1	= $this->databaseConnect->delRecord($qry1);
		if ($result1) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		$qry2	= " delete from t_pkng_inst_mc_wt where pkng_inst_id='$pkngInstructionId'";
		$result2	= $this->databaseConnect->delRecord($qry2);
		if ($result2) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		$qry3	= " delete from t_pkng_inst_adnl_item where pkng_inst_id='$pkngInstructionId'";
		$result3	= $this->databaseConnect->delRecord($qry3);
		if ($result3) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		return true;
	}
	
	# Delete Product wise Entry Rcs
	function delAllPkngInstPrdEntryRecs($pkgInstPrdEId)
	{
		$qry1	= " delete from t_pkng_inst_prd_btch where inst_prd_eid='$pkgInstPrdEId'";
		$result1	= $this->databaseConnect->delRecord($qry1);
		if ($result1) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		$qry2	= " delete from t_pkng_inst_prd_pkng where inst_prd_eid='$pkgInstPrdEId'";
		$result2	= $this->databaseConnect->delRecord($qry2);
		if ($result2) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		$qry3	= " delete from t_pkng_inst_prd_mc_wt where inst_prd_eid='$pkgInstPrdEId'";
		$result3	= $this->databaseConnect->delRecord($qry3);
		if ($result2) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		return true;
	}

	function getMcActualGrossWt($pkngInstId, $mcPackId, $mcProductId, $kId)
	{
		$qry = "select pkng_wt from t_pkng_inst_mc_wt where pkng_inst_id='$pkngInstId' and pkng_id='$mcPackId' and product_id='$mcProductId' and row_id='$kId' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";
	}

	function updateSOPkngInstRec($selSOId, $uptdValue)
	{
		$qry = "update t_salesorder set pkng_confirm='$uptdValue' where id='$selSOId' ";

		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}

	# get Pkng Products
	function getPkngPrdRecs($pkngInstructionId)
	{
		$qry = "select id from t_pkng_inst_prd where pkng_inst_id='$pkngInstructionId' ";
		return $this->databaseConnect->getRecords($qry);
	}

	# Delete Packing instruction
	function deletePackingInstruction($pkngInstructionId)
	{
		$getPkngPrdRecords = $this->getPkngPrdRecs($pkngInstructionId);
		if (sizeof($getPkngPrdRecords)>0) {
			foreach ($getPkngPrdRecords as $r) {
				$pkgInstPrdEId = $r[0];
				$this->delAllPkngInstPrdEntryRecs($pkgInstPrdEId);
			}	
		}
		$delPrdEntryRecs = $this->delPkngInstAllMainEntryRec($pkngInstructionId);

		$qry	= " delete from t_pkng_inst where id=$pkngInstructionId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function uptdSOPkngRec($soId)
	{
		$qry = "update t_salesorder set pkng_gen='N', pkng_confirm='N' where id='$soId' ";

		$result = 	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();		
		return $result;
	}	

	# 
	function addPrdMCActualGrossWt($pkgInstPrdEId, $mcGrossWt, $rowId)
	{
		$qry = "insert into t_pkng_inst_prd_mc_wt (inst_prd_eid, gross_wt, row_id) values('$pkgInstPrdEId', '$mcGrossWt', '$rowId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;	
	}

	function prdWiseActualGrossWt($pkgInstPrdEId, $b)
	{
		$qry = "select gross_wt from t_pkng_inst_prd_mc_wt where inst_prd_eid='$pkgInstPrdEId' and row_id='$b' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result))?$result[0][0]:"";
	}

	# Get MC Package Wt
	function getMCPkgWt($mcPackingId, $productNetWt, $mcPkgWtId=null)
	{
		$whr = " mc_pkg_id='$mcPackingId' and net_wt='$productNetWt' ";
		if ($mcPkgWtId>0) $whr = " id = '$mcPkgWtId' ";

		//$qry = " select pkg_wt, pkg_wt_tolerance from m_mc_pkg_wt where mc_pkg_id='$mcPackingId' and net_wt='$productNetWt' ";
		$qry = " select pkg_wt, pkg_wt_tolerance from m_mc_pkg_wt where $whr";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?array($rec[0][0], $rec[0][1]):array();
	}

	# --------------------------------------Edit Locking Starts here
	# Modified Time Updating
	function chkPkgInstModified($mcPackingDtlsMainId)
	{
		$qry = " select a.editing_by, b.username from t_pkng_inst a, user b  where a.editing_by=b.id and a.id='$mcPackingDtlsMainId' and a.editing_by!=0 ";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][1]:false;
	}
		# Update editing rec
	function updatePkgInstEditingRec($mcPackingDtlsMainId, $userId, $mode)
	{
		if ($mode=='E') $uptdQry = "editing_time=NOW()";
		else $uptdQry = "editing_time=0";
		
		$qry = " update t_pkng_inst set editing_by='$userId', $uptdQry where id=$mcPackingDtlsMainId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	

	function getMainRec($mcPkgMainId)
	{
		$qry = " select editing_by, TIMEDIFF(NOW(), editing_time) as tDiff from t_pkng_inst where id='$mcPkgMainId' ";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?array($rec[0][0], $rec[0][1]):array();
	}
	# Edit Locking ENDS HERE --------------------------------------------------<

	# Find Pkg Instruction Main ID
	function pkgInstrMainId($selSOId)
	{
		$qry = "select a.id from t_pkng_inst a where a.so_id='$selSOId' ";
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}
}