<?php
Class FactoryUtilizationReport
{

	/****************************************************************
	This class deals with all the operations relating to FrozenPackingReport.
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function FactoryUtilizationReport(&$databaseConnect)
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

		$qry1="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit from t_dailyfrozenpacking_main dfp left join t_dailyfrozenpacking_entry tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id";
		$qry1.=" where $whr";
		$qry1.=" group by selDate,unit";

		$qry2="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit from t_dailyfrozenpacking_main_rmlotid dfp left join t_dailyfrozenpacking_entry_rmlotid tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id ";
		$qry2.=" where $whr";
		$qry2.=" group by selDate,unit";

		$qry="select mainid,selDate,sum(availableQty),unit from ($qry1 union all $qry2) dum group by selDate,unit order by selDate ";
		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Records For a Selected Date 
	function getDFPForTotal($fromDate, $toDate, $selCompany)
	{

			if ($fromDate && $toDate) {
			$whr  .= "dfp.select_date>='$fromDate' and dfp.select_date<='$toDate'" ;
		} 
	
		$qry1="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit,mp.standard_production as stdprod from t_dailyfrozenpacking_main dfp left join t_dailyfrozenpacking_entry tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id left join m_plant mp on mp.id=dfp.unit ";
		$qry1.=" where $whr";
		$qry1.=" group by selDate,unit";

		$qry2="select dfp.id as mainid, dfp.select_date as selDate, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,dfp.unit as unit,mp.standard_production as stdprod from t_dailyfrozenpacking_main_rmlotid dfp left join t_dailyfrozenpacking_entry_rmlotid tdfpe on dfp.id=tdfpe.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id left join m_plant mp on mp.id=dfp.unit";
		$qry2.=" where $whr";
		$qry2.=" group by selDate,unit";

		$qry="select mainid,selDate,sum(prod) ,unit,sum(stdprod),sum(prod)/sum(stdprod) from (select mainid,selDate,sum(availableQty) as prod,unit,stdprod from  ($qry1 union all $qry2) dum group by selDate,unit order by selDate ) dum group by selDate order by selDate ";
		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getStandardProduction($unit)
	{
		$qry="select standard_production from m_plant where id='$unit' and based_on='month'";
		$result	= $this->databaseConnect->getRecord($qry);
		if(sizeof($result)>0)
		{
			return $result[0];
		}
		else
		{
			$qrys="select standard_production from m_plant where id='$unit'";
			$res	= $this->databaseConnect->getRecord($qrys);
			if(sizeof($res)>0)
			{
				return ($res[0]*31);
			}
		}
	}
	
	function getGroupedFPGradeRecsLot($gFPEntryId)
	{
 		$qry = "select mg.code, sum(dfpg.number_mc), sum(dfpg.number_loose_slab), ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry_rmlotid dfpe join t_dailyfrozenpacking_grade_rmlotid dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id in ($gFPEntryId) and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) group by dfpg.grade_id order by mg.code asc";
		//echo "<br>==>Grouped ($gFPEntryId)<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	function getDetailedFPGradeRecsLot($entryId)
	{		
		//$qry	= "select c.code, b.number_mc, b.number_loose_slab from t_dailyfrozenpacking_grade b join m_grade c on b.grade_id=c.id where b.entry_id='$entryId' and (b.number_mc!=0 or b.number_loose_slab!=0) order by c.code asc";

		$qry	= "select mg.code, dfpg.number_mc, dfpg.number_loose_slab, (((dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+((dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, (((dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + ((dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry_rmlotid dfpe join t_dailyfrozenpacking_grade_rmlotid dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id='$entryId' and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) order by mg.code asc";				
		//echo "<br>==>($entryId)<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Update the dailyfrozen packing record
	function updateDailyFrozenPackingRecords($selectDate) 
	{
		$qry	= "update t_dailyfrozenpacking_main set report_confirm='Y' where select_date='$selectDate'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	#check Records confirmed
	function isConfirmed($selectDate)
	{
		$qry = "select id, report_confirm from t_dailyfrozenpacking_main where select_date='$selectDate' and report_confirm='Y'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	# Grade selection for Frozen Grades
 	function fetchFrozenGradeRecords($codeId, $entryId)
	{
 		$qry	= "select a.grade_id, c.code, b.id, b.entry_id, b.number_mc, b.number_loose_slab from m_processcode2grade a, t_dailyfrozenpacking_grade b , m_grade c where a.grade_id=b.grade_id and b.entry_id='$entryId' and a.grade_id = c.id and a.processcode_id='$codeId' and a.unit_select='f' and (b.number_mc!=0 or b.number_loose_slab!=0) order by c.code asc";
		
		//echo "<br>==>($codeId, $entryId)<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	function getDetailedFPGradeRecs($entryId)
	{		
		$qry	= "select mg.code as code, dfpg.number_mc, dfpg.number_loose_slab, (((dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+((dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, (((dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + ((dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry dfpe join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id='$entryId' and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) union all select mg.code as code, dfpg.number_mc, dfpg.number_loose_slab, (((dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+((dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, (((dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + ((dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry_rmlotid dfpe join t_dailyfrozenpacking_grade_rmlotid dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id='$entryId' and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) order by code asc";

// order by mg.code asc

		/*$qry	= "select mg.code, dfpg.number_mc, dfpg.number_loose_slab, (((dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+((dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, (((dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + ((dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry dfpe join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id='$entryId' and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) order by mg.code asc";*/				
		//echo "<br>==>($entryId)<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function getDetailedFPGradeRecsold($entryId)
	{	
		$qry	= "select mg.code, dfpg.number_mc, dfpg.number_loose_slab, (((dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+((dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, (((dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + ((dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry dfpe join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id='$entryId' and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) order by mg.code asc";			
		//echo "<br>==>($entryId)<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getGroupedFPGradeRecs($gFPEntryId)
	{
		$qry = "select code,sum(nummc),sum(numlc),sum(frozenqty),sum(pkdqty) from ( select mg.code as code, sum(dfpg.number_mc) as nummc, sum(dfpg.number_loose_slab) as numlc, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty,dfpg.grade_id as gradeId from t_dailyfrozenpacking_entry dfpe join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id in ($gFPEntryId) and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) union all select mg.code as code, sum(dfpg.number_mc) as nummc, sum(dfpg.number_loose_slab) as numlc, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty,dfpg.grade_id as gradeId from t_dailyfrozenpacking_entry_rmlotid dfpe join t_dailyfrozenpacking_grade_rmlotid dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id in ($gFPEntryId) and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0)) dum group by gradeId order by code asc";

		//group by dfpg.grade_id order by mg.code asc

 		/*$qry = "select mg.code, sum(dfpg.number_mc), sum(dfpg.number_loose_slab), ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry dfpe join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id in ($gFPEntryId) and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) group by dfpg.grade_id order by mg.code asc";*/
		//echo "<br>==>Grouped ($gFPEntryId)<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getGroupedFPGradeRecsold($gFPEntryId)
	{
		//group by dfpg.grade_id order by mg.code asc

 		$qry = "select mg.code, sum(dfpg.number_mc), sum(dfpg.number_loose_slab), ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty from t_dailyfrozenpacking_entry dfpe join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id join m_grade mg on dfpg.grade_id=mg.id left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id where dfpg.entry_id in ($gFPEntryId) and (dfpg.number_mc!=0 or dfpg.number_loose_slab!=0) group by dfpg.grade_id order by mg.code asc";
		//echo "<br>==>Grouped ($gFPEntryId)<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	/*
	function GetFPPORecs($FPEntryId, $allocatePOId)
	{
		$qry = "select id from t_dailyfrozenpacking_po where entry_id='$FPEntryId' and po_id='$allocatePOId' ";
		//echo "<br>==>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

	function delAllocateRecs($allocateId)
	{
		$qry	=	" delete from t_dailyfrozenpacking_allocate where id=$allocateId";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function delAllocatePORecs($FPEntryId, $allocatePOId)
	{
		$qry	=	" delete from t_dailyfrozenpacking_po where entry_id='$FPEntryId' and po_id='$allocatePOId'";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	// Allocated PO recs
	function getAllocatedPurchaseOrders($selectDate, $fromDate, $toDate)
	{
		$whr		= " dfppo.po_id!=0 ";

		if ($selectDate) {
			$whr  .= " and dfppo.created_on='".$selectDate."' " ;
		} else if ($fromDate && $toDate) {
			$whr  .= " and dfppo.created_on>='$fromDate' and dfppo.created_on<='$toDate'  " ;
		} 

		$orderBy	= " pom.po_date asc ";
		$groupBy	= " dfppo.po_id ";

		$qry = "select 
				dfppo.po_id, CONCAT(pom.po_no,' (',DATE_FORMAT(pom.po_date,'%d/%m/%Y'),')') as PO
			from 
				t_dailyfrozenpacking_po dfppo join t_purchaseorder_main pom on pom.id=dfppo.po_id
			";
		
		if ($whr!="")		$qry   .= " where ".$whr;
		if ($groupBy!="")	$qry   .= " group by ".$groupBy;
		if ($orderBy!="")	$qry   .= " order by ".$orderBy;

		//echo "<br>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	// Get All confirmed Invoices
	function getAllInvoices($purchaseOrderId)
	{
		//$qry = "select id, exp_invoice_no from t_invoice_main where confirmed='Y' and po_id='$purchaseOrderId' ";
		$qry = "select id, exp_invoice_no from t_invoice_main where po_id='$purchaseOrderId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	// Release Allocation
	


	

	function releaseAllocation($purchaseOrderId, $invoiceId, $userId)
	{

		if ($purchaseOrderId>0 && ($invoiceId=="-1" || $invoiceId==0) ) {
			$type="P";
				$releaseInvEntry = $this->updateInvoiceEntry($purchaseOrderId);
				if ($releaseInvEntry) {
					$releasePOEntry = $this->updatePOEntry($purchaseOrderId);
					if ($releasePOEntry) {
						$releaseAllocation = $this->updateFSAllocationPO($purchaseOrderId, $userId);
						$dailyFrozenpackingEntries=$this->getAllDailyfrozenpackingEntries($purchaseOrderId);
						foreach ($dailyFrozenpackingEntries as $dfe) {
							$id=$dfe[0];
							//list($processcodeid,$freestid,$frozid,$mcpid)=$this->getDailyfrozenpackingpoAlloc($id);
							//list($gradeid,$numc)=$this->getDailyfrozenpackingAlloc($id);
							list($processcodeid,$freestid,$frozid,$mcpid,$gradeid,$numc)=$this->getDailyfrozenpackingpoalAllocation($id);
							$insLog=$this->insDailyfrozenpackingAlloc($processcodeid,$freestid,$frozid,$mcpid,$gradeid,$numc,$purchaseOrderId,$id,$type,$invoiceId,$userId);
							//echo $insLog;
							//if ($insLog)
							//{
								$allocDel=$this->deleteAllocationEntry($id);
								$allocUp=$this->updateRmEntrydeliveredStatus($purchaseOrderId);
								
							//}

						}
					}
				}
			}
		/*if ($purchaseOrderId>0 && $invoiceId>0) {
			$type="I";
			$releaseInvEntry = $this->updateInvoice($purchaseOrderId,$invoiceId);
			if ($releaseInvEntry) {
				$releasePOEntry = $this->updatePOEntry($purchaseOrderId);
			}if ($releasePOEntry) {
					$releaseAllocation = $this->updateFSAllocationPO($purchaseOrderId, $userId);
					$dailyFrozenpackingEntries=$this->getAllDailyfrozenpackingEntries($purchaseOrderId);
					foreach ($dailyFrozenpackingEntries as $dfe) {
						$id=$dfe[0];
						list($processcodeid,$freestid,$frozid,$mcpid,$gradeid,$numc)=$this->getDailyfrozenpackingpoalAllocation($id);
						$insLog=$this->insDailyfrozenpackingAlloc($processcodeid,$freestid,$frozid,$mcpid,$gradeid,$numc,$purchaseOrderId,$id,$type,$invoiceId);
						$allocDel=$this->deleteAllocationEntry($id);
						$allocUp=$this->updateRmEntrydeliveredStatus($purchaseOrderId);							
					}
			}

	}*/
return true;
	}
function updateRmEntrydeliveredStatus($purchaseOrderId)
	{
$qry="update t_purchaseorder_rm_entry set delivered_status=0 where main_id='$purchaseOrderId'";
//echo $qry;
$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	



	}
function getDailyfrozenpackingpoalAllocation($id)
{
$qry = " select processcode_id,freezing_stage_id,frozencode_id,mcpacking_id,grade_id,number_mc from t_dailyfrozenpacking_po dfpo join t_dailyfrozenpacking_allocate dfpal on dfpo.id=dfpal.po_entry_id where dfpo.id='$id'";
		//echo "<br>$qry</br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4],$rec[5]);

}
function getDailyfrozenpackingpoAlloc($id)
{
		$qry = " select processcode_id,freezing_stage_id,frozencode_id,mcpacking_id from t_dailyfrozenpacking_po where id='$id'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3]);
}

function insDailyfrozenpackingAlloc($processcodeid,$freestid,$frozid,$mcpid,$gradeid,$numc,$purchaseOrderId,$id,$type,$invoiceId,$userId)
{
	$date=Date(Y-m-d);
$qry="insert into t_frozenpacking_log(processcode_id,freezingstage_id,frozencode_id,mcpacking_id,purchase_order_id,grade_id,num_mc,type,invoice_id,date_allocated,modified_by) values ('$processcodeid','$freestid','$frozid','$mcpid','$purchaseOrderId','$gradeid','$numc','$type','$invoiceId',now(),'$userId')";
//echo $qry;
$insertStatus	= $this->databaseConnect->insertRecord($qry);
if ($insertStatus) $this->databaseConnect->commit();
else $this->databaseConnect->rollback();


}



function getDailyfrozenpackingAlloc($id)
	{
		$qry = " select grade_id,number_mc from t_dailyfrozenpacking_allocate where po_entry_id='$id'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}


	function deleteAllocationEntry($allocateEntryId)
	{

		$qry	=	" delete from t_dailyfrozenpacking_allocate where po_entry_id='$allocateEntryId'";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;


	}



	function getAllDailyfrozenpackingEntries($purchaseOrderId)
	{
		$qry="select id from t_dailyfrozenpacking_po where po_id='$purchaseOrderId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function deleteAllocation($purchaseOrderId)
	{
	$qry	=	" delete from t_dailyfrozenpacking_po where po_id='$purchaseOrderId'";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;

	}

	function releaseInvoice($purchaseOrderId, $invoiceId, $userId)
	{
		// Get All Confirmed Invoice against the selected PO
		//$getInvRecs = $this->getAllInvoices($purchaseOrderId); //sizeof($getInvRecs)>0 &&
		
		// Release All Invoice and revert back PO Allocation
		/*if ($purchaseOrderId>0 && ($invoiceId=="" || $invoiceId==0) ) {
			$releaseInvEntry = $this->updateInvoiceEntry($purchaseOrderId);
		if ($releaseInvEntry) {
				$releasePOEntry = $this->updatePOEntry($purchaseOrderId);
				if ($releasePOEntry) {
					$releaseAllocation = $this->updateFSAllocationPO($purchaseOrderId, $userId);
				}
			}
			}*/
			if ($purchaseOrderId>0 && $invoiceId>0) {
			$invoiceItemRecs = $this->updateInvoice($purchaseOrderId, $invoiceId);

			if ($invoiceItemRecs) {
					$releasePOEntry = $this->updatePOEntry($purchaseOrderId);
			}
			}
			return true;
	}

function updateInvoice($purchaseOrderId, $invoiceId)
	{
$qry	= "update t_invoice_main set confirmed='N' where id='$invoiceId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	// Frozen Stock Allocation
	function getFrznStkAllocationRecs($purchaseOrderId, $processCodeId, $freezingStageId, $mcPackingId, $gradeId)
	{
		$qry = "select *
			from 
				t_dailyfrozenpacking_po dfppo join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id 				
			where dfppo.processcode_id='', dfppo.freezing_stage_id` INT(5) NULL DEFAULT NULL,
	`frozencode_id` INT(5) NULL DEFAULT NULL,
	`mcpacking_id  ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	// Release all confirmed invoice against the PO
	function updateInvoiceEntry($purchaseOrderId) 
	{
		$qry	= "update t_invoice_main set confirmed='N' where po_id='$purchaseOrderId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	// Release  PO
	function updatePOEntry($purchaseOrderId) 
	{
		$qry	= "update t_purchaseorder_main set complete=null where id='$purchaseOrderId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	// Release FS Allocation table 
	function updateFSAllocationPO($purchaseOrderId, $userId)
	{
		$qry	= "update t_dailyfrozenpacking_po set deleted=1, deleted_on=NOW(), deleted_by='$userId' where po_id='$purchaseOrderId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	// Get all invoice - po related items
	function getAllInvoicePOItems($purchaseOrderId, $invoiceId)
	{
		$qry = "select tpore.processcode_id, tpore.freezingstage_id, tpore.frozencode_id, tpore.mcpacking_id, tpore.grade_id, tire.mc_in_invoice
			from 
				t_invoice_main tim join t_invoice_rm_entry tire on tim.id=tire.main_id 
				join t_purchaseorder_rm_entry tpore on tire.po_entry_id=tpore.id 
			where tim.id='$invoiceId' and tim.po_id='$purchaseOrderId'  ";

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getMonthYear($fromdate,$todate)
	{
		$start    = new DateTime($fromdate);
		$start->modify('first day of this month');
		$end      = new DateTime($todate);
		$end->modify('first day of next month');
		$interval =  DateInterval::createFromDateString('1 month');
		$period   = new DatePeriod($start, $interval, $end);
		foreach ($period as $dt) {
			$disp=$dt->format("Y-F");
			$sel=$dt->format("Y-m");
			$result[]=array($disp,$sel);
		}
		//printr($result);
		return $result;

	}

}	
?>