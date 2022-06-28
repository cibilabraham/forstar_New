<?php
class FrozenStockAllocation
{
	/****************************************************************
	This class deals with all the operations relating to Daily Frozen Packing
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FrozenStockAllocation(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Check Blank Record Exist
	function checkBlankRecord($userId)
	{
		$qry = "select a.id, b.id from t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id where a.user_id='$userId' and a.unit is null and a.processor_id is null order by a.id desc";
		//echo "Chk Blank=<br>$qry";
		$result	= $this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array( $result[0], $result[1]):false;
	}

	#Indert blank record
	function addTempDataMainTable($userId)
	{
		$qry	= "insert into t_dailyfrozenpacking_main (select_date, user_id) values(Now(), $userId)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Insert blank record
	function addTempDataEntryTable($mainId)
	{
		$qry	= "insert into t_dailyfrozenpacking_entry (main_id) values('$mainId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Update daily Frozen Packing Entry Table Rec
	function updateDailyFrozenPackingEntry($fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $exportLotId,$entryId, $lotId, $selQuality, $allocateMode, $brandFrom, $customer)
	{
		if (!$allocateMode) {
			$updateEntry = ",fish_id='".$fishId."', processcode_id='".$processCode."', frozencode_id='".$frozenCode."'";
		}
		
		$qry = "update t_dailyfrozenpacking_entry  set  freezing_stage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', mcpacking_id='$mCPacking', export_lot_id='$exportLotId', frozen_lot_id='$lotId', quality_id='$selQuality', brand_from='$brandFrom', customer_id='$customer' $updateEntry where id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#update Packing main Table
	function updatePackingMainRec($selectDate, $unit, $processorId, $mainId)
	{
		$qry	= "update t_dailyfrozenpacking_main  set select_date='$selectDate', unit='$unit', processor_id='$processorId' where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function getMaxDate()
	{
		
		$qry = "select max(date) maxdate from m_physical_stk_packing";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	} 
	
	
#Get Records For Selected Date Range
	function getPagingDFPRecs($fromDate, $tillDate, $offset, $limit, $filterProcessCode)
	{
		
		$whr		= " a.select_date>='".$fromDate."'  and a.select_date<='".$tillDate."'" ;
		if ($filterProcessCode>0) $whr .= " and tdfpe.processcode_id='$filterProcessCode' ";

		$groupBy	= " tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id,new_lot_Id";
		
		$orderBy	= " processCode asc, freezingStage asc,frznPkgCode asc, mcPkgCode asc";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";
		$rsLimit 		= " $offset, $limit";

				$qry1 	= "select a.id as id, a.select_date as selDate, mpc.fish_id as fishId, tdfpe.processcode_id as processcodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirmed, dfppo.po_id as poId, tdfpe.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack,
						((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as actualQty, ((sum(tdfpg.number_mc)*mcp.number_packs)+sum(tdfpg.number_loose_slab)) as totSlabs, 0 as allocatedMC, sum(tdfpg.number_loose_slab) as numLSPack, 0 as allocatedLS,mfp.filled_wt as filledwt,a.repack_frozen_id as repackfrozenid,sum(flag) as flag,repacked_from as repackedfrom,sum(repkdQty) as repkdQty,sum(rgzQty) as rgzQty,reglazed_from as reglazedfrom,'0' as new_lot_IdNm,'0' as new_lot_Id,tdfpe.id as entryid,'0' as unit, '0' as company
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry tdfpe on a.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id
				left join t_dailyfrozenpacking_po dfppo on dfppo.entry_id";
		

				$qry2 	= "select a.id as id, a.select_date as selDate, mpc.fish_id as fishId, tdfpe.processcode_id as processcodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirmed, dfppo.po_id as poId, tdfpe.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack,
						((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as actualQty, ((sum(tdfpg.number_mc)*mcp.number_packs)+sum(tdfpg.number_loose_slab)) as totSlabs, 0 as allocatedMC, sum(tdfpg.number_loose_slab) as numLSPack, 0 as allocatedLS,mfp.filled_wt as filledwt,a.repack_frozen_id as repackfrozenid,sum(flag) as flag,repacked_from as repackedfrom,sum(repkdQty) as repkdQty,sum(rgzQty) as rgzQty,reglazed_from as reglazedfrom,concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,a.rm_lot_id as new_lot_Id,tdfpe.id as entryid,mg.unit_id as unit,mg.company_id as company 
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid tdfpe on a.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id
				left join t_dailyfrozenpacking_po dfppo on dfppo.entry_id
				left join t_manage_rm_lotid mg on mg.id = a.rm_lot_id ";

		

		/*if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;*/
		
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		if ($groupBy) $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy;
		//
		//if ($limit) $qry .= " limit ".$limit;
		$qry=$qry1." union all ".$qry2; 
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		
		
		
		
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		//if ($rsLimit) $qry .= " limit ".$rsLimit;
		//echo $qry;

		// Frozen Stock Allocation
		//new comment
		//$fsWhr		= " dfppo.created_on<='".$tillDate."'  " ;
		//$fsWhr		= " dfppo.created_on>='".$fromDate."' and  dfppo.created_on<='".$tillDate."'";

		$fsWhr1="dfpa.created_on>='".$fromDate."' and  dfpa.created_on<='".$tillDate."'";
		if ($filterProcessCode>0) $fsWhr1 .= " and dfppo.processcode_id='$filterProcessCode' ";

		$fsGroupBy1	= " dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id";
		$fsOrderBy1	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";

		$fsWhr2="dfpa.created_on>='".$fromDate."' and  dfpa.created_on<='".$tillDate."'";
		if ($filterProcessCode>0) $fsWhr2 .= " and dfppo.processcode_id='$filterProcessCode' ";

		$fsGroupBy2	= " dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id,new_lot_Id";
		$fsOrderBy2= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";

		$fsLimit 		= " $offset, $limit";

	
			$fsQry1 	= " select 
						0 as id,0 as selDate,mpc.fish_id as fishId, dfppo.processcode_id as processcodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, 0 as confirmed, dfppo.po_id as poId, dfppo.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack, 0 as pkdQty, 0 as numMcs, 0 as frozenQty, 0 as actualQty, 0 as totSlabs, sum(dfpa.number_mc) as allocatedMC, 0 as numLSPack, sum(dfpa.number_loose_slab) as allocatedLS,mfp.filled_wt as filledwt,0 as repackfrozenid,0 as flag,0 as repackedfrom,0 as repkdQty,0 as rgzQty,0 as reglazedfrom,'0' as new_lot_IdNm,'0' as new_lot_Id,'0' as entryid,'0' as unit, '0' as company
			    from  t_dailyfrozenpacking_po dfppo   join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id
				left join m_processcode mpc on mpc.id=dfppo.processcode_id 
				left join m_freezingstage mfs on mfs.id=dfppo.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=dfppo.frozencode_id
				left join m_mcpacking mcp on dfppo.mcpacking_id=mcp.id 
			";
		
			$fsQry2 	= " select 
						0 as id,0 as selDate,mpc.fish_id as fishId, dfppo.processcode_id as processcodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, 0 as confirmed, dfppo.po_id as poId, dfppo.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack, 0 as pkdQty, 0 as numMcs, 0 as frozenQty, 0 as actualQty, 0 as totSlabs, sum(dfpa.number_mc) as allocatedMC, 0 as numLSPack, sum(dfpa.number_loose_slab) as allocatedLS,mfp.filled_wt as filledwt,0 as repackfrozenid,0 as flag,0 as repackedfrom,0 as repkdQty,0 as rgzQty,0 as reglazedfrom,concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,dfppo.rm_lot_id as new_lot_Id,dfppo.id as entryid,mg.unit_id as unit,mg.company_id as company 
			    from  t_dailyfrozenpacking_po_rmlotid dfppo join t_dailyfrozenpacking_allocate_rmlotid dfpa on dfppo.id=dfpa.po_entry_id
				left join m_processcode mpc on mpc.id=dfppo.processcode_id 
				left join m_freezingstage mfs on mfs.id=dfppo.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=dfppo.frozencode_id
				left join m_mcpacking mcp on dfppo.mcpacking_id=mcp.id 
				left join t_manage_rm_lotid mg on mg.id = dfppo.rm_lot_id 
			";




		if ($fsWhr1!="") $fsQry1 .= " where ".$fsWhr1;			if ($fsWhr2!="") $fsQry2.= " where ".$fsWhr2;
		if ($fsGroupBy1) $fsQry1 .= " group by ".$fsGroupBy1;		if ($fsGroupBy2) $fsQry2.= " group by ".$fsGroupBy2;
		
		$fsQry=$fsQry1." union all ".$fsQry2; 
	//echo $fsQry;
		$nQry = "select id, selDate, fishId, processcodeId, freezingStageId, frozenCodeId, processCode, freezingStage, frznPkgCode, confirmed, poId, mcPkgId, mcPkgCode, numPack, pkdQty, numMcs, frozenQty, actualQty, totSlabs, sum(allocatedMC), (sum(numMcs)-sum(allocatedMC)) as balMC, sum(numLSPack), sum(allocatedLS), (sum(numLSPack)-sum(allocatedLS)) as balLS,'0' as invConfirm,filledwt,repackfrozenid,flag,repackedfrom,repkdQty,rgzQty,reglazedfrom,new_lot_IdNm,new_lot_Id,entryid,unit,company from (
				$qry
				union all
				$fsQry
				) as x group by processcodeId, freezingStageId, frozenCodeId, mcPkgId ,new_lot_Id order by new_lot_IdNm desc,processCode asc, freezingStage asc, frznPkgCode asc, mcPkgCode asc  limit $fsLimit
				";
//echo "--------bbb<br>-------$nQry";

	//echo "<br>$fsQry2<br>";		
		$result	= $this->databaseConnect->getRecords($nQry);
		return $result;
	}
	
	
###commented on 17-12-2014
/*	#Get Records For Selected Date Range
	function getPagingDFPRecs($fromDate, $tillDate, $offset, $limit, $filterProcessCode)
	{
		
		$whr		= " a.select_date>='".$fromDate."'  and a.select_date<='".$tillDate."'" ;
		if ($filterProcessCode>0) $whr .= " and tdfpe.processcode_id='$filterProcessCode' ";

		$groupBy	= " tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id,new_lot_Id";
		
		$orderBy	= " processCode asc, freezingStage asc,frznPkgCode asc, mcPkgCode asc";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";
		$rsLimit 		= " $offset, $limit";

				$qry1 	= "select a.id as id, a.select_date as selDate, mpc.fish_id as fishId, tdfpe.processcode_id as processcodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirmed, dfppo.po_id as poId, tdfpe.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack,
						((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as actualQty, ((sum(tdfpg.number_mc)*mcp.number_packs)+sum(tdfpg.number_loose_slab)) as totSlabs, 0 as allocatedMC, sum(tdfpg.number_loose_slab) as numLSPack, 0 as allocatedLS,0 as invConfirm,mfp.filled_wt as filledwt,a.repack_frozen_id as repackfrozenid,sum(flag) as flag,repacked_from as repackedfrom,sum(repkdQty) as repkdQty,sum(rgzQty) as rgzQty,reglazed_from as reglazedfrom,'0' as new_lot_IdNm,'0' as new_lot_Id,tdfpe.id as entryid,'0' as unit, '0' as company
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry tdfpe on a.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id
				left join t_dailyfrozenpacking_po dfppo on dfppo.entry_id";
		

				$qry2 	= "select a.id as id, a.select_date as selDate, mpc.fish_id as fishId, tdfpe.processcode_id as processcodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirmed, dfppo.po_id as poId, tdfpe.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack,
						((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as actualQty, ((sum(tdfpg.number_mc)*mcp.number_packs)+sum(tdfpg.number_loose_slab)) as totSlabs, 0 as allocatedMC, sum(tdfpg.number_loose_slab) as numLSPack, 0 as allocatedLS,0 as invConfirm,mfp.filled_wt as filledwt,a.repack_frozen_id as repackfrozenid,sum(flag) as flag,repacked_from as repackedfrom,sum(repkdQty) as repkdQty,sum(rgzQty) as rgzQty,reglazed_from as reglazedfrom,concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,a.rm_lot_id as new_lot_Id,tdfpe.id as entryid,mg.unit_id as unit,mg.company_id as company 
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid tdfpe on a.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id
				left join t_dailyfrozenpacking_po dfppo on dfppo.entry_id
				left join t_manage_rm_lotid mg on mg.id = a.rm_lot_id ";

		

		//if ($whr!="") $qry .= " where ".$whr;
		//if ($groupBy) $qry .= " group by ".$groupBy;
		
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		if ($groupBy) $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy;
		//
		//if ($limit) $qry .= " limit ".$limit;
		$qry=$qry1." union all ".$qry2; 
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		
		
		
		
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		//if ($rsLimit) $qry .= " limit ".$rsLimit;
		//echo $qry;

		// Frozen Stock Allocation
		//new comment
		//$fsWhr		= " dfppo.created_on<='".$tillDate."'  " ;
		//$fsWhr		= " dfppo.created_on>='".$fromDate."' and  dfppo.created_on<='".$tillDate."'";

		$fsWhr1="dfpa.created_on>='".$fromDate."' and  dfpa.created_on<='".$tillDate."'";
		if ($filterProcessCode>0) $fsWhr1 .= " and dfppo.processcode_id='$filterProcessCode' ";

		$fsGroupBy1	= " dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id";
		$fsOrderBy1	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";

		$fsWhr2="dfpa.created_on>='".$fromDate."' and  dfpa.created_on<='".$tillDate."'";
		if ($filterProcessCode>0) $fsWhr2 .= " and dfppo.processcode_id='$filterProcessCode' ";

		$fsGroupBy2	= " dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id,new_lot_Id";
		$fsOrderBy2= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";

		$fsLimit 		= " $offset, $limit";

	
			$fsQry1 	= " select 
						0 as id,0 as selDate,mpc.fish_id as fishId, dfppo.processcode_id as processcodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, 0 as confirmed, dfppo.po_id as poId, dfppo.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack, 0 as pkdQty, 0 as numMcs, 0 as frozenQty, 0 as actualQty, 0 as totSlabs, sum(dfpa.number_mc) as allocatedMC, 0 as numLSPack, sum(dfpa.number_loose_slab) as allocatedLS,(if(tim.confirmed='Y',1,0)) as invConfirm,mfp.filled_wt as filledwt,0 as repackfrozenid,0 as flag,0 as repackedfrom,0 as repkdQty,0 as rgzQty,0 as reglazedfrom,'0' as new_lot_IdNm,'0' as new_lot_Id,'0' as entryid,'0' as unit, '0' as company
			    from t_invoice_main tim join t_dailyfrozenpacking_po dfppo on tim.po_id=dfppo.po_id  join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id
				left join m_processcode mpc on mpc.id=dfppo.processcode_id 
				left join m_freezingstage mfs on mfs.id=dfppo.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=dfppo.frozencode_id
				left join m_mcpacking mcp on dfppo.mcpacking_id=mcp.id 
			";
		
			$fsQry2 	= " select 
						0 as id,0 as selDate,mpc.fish_id as fishId, dfppo.processcode_id as processcodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, 0 as confirmed, dfppo.po_id as poId, dfppo.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack, 0 as pkdQty, 0 as numMcs, 0 as frozenQty, 0 as actualQty, 0 as totSlabs, sum(dfpa.number_mc) as allocatedMC, 0 as numLSPack, sum(dfpa.number_loose_slab) as allocatedLS,(if(tim.confirmed='Y',1,0)) as invConfirm,mfp.filled_wt as filledwt,0 as repackfrozenid,0 as flag,0 as repackedfrom,0 as repkdQty,0 as rgzQty,0 as reglazedfrom,concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,dfppo.rm_lot_id as new_lot_Id,dfppo.id as entryid,mg.unit_id as unit,mg.company_id as company 
			    from t_invoice_main tim join t_dailyfrozenpacking_po_rmlotid dfppo on tim.po_id=dfppo.po_id  join t_dailyfrozenpacking_allocate_rmlotid dfpa on dfppo.id=dfpa.po_entry_id
				left join m_processcode mpc on mpc.id=dfppo.processcode_id 
				left join m_freezingstage mfs on mfs.id=dfppo.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=dfppo.frozencode_id
				left join m_mcpacking mcp on dfppo.mcpacking_id=mcp.id 
				left join t_manage_rm_lotid mg on mg.id = dfppo.rm_lot_id 
			";




		if ($fsWhr1!="") $fsQry1 .= " where ".$fsWhr1;			if ($fsWhr2!="") $fsQry2.= " where ".$fsWhr2;
		if ($fsGroupBy1) $fsQry1 .= " group by ".$fsGroupBy1;		if ($fsGroupBy2) $fsQry2.= " group by ".$fsGroupBy2;
		
		$fsQry=$fsQry1." union all ".$fsQry2; 
	//echo $fsQry;
		$nQry = "select id, selDate, fishId, processcodeId, freezingStageId, frozenCodeId, processCode, freezingStage, frznPkgCode, confirmed, poId, mcPkgId, mcPkgCode, numPack, pkdQty, numMcs, frozenQty, actualQty, totSlabs, sum(allocatedMC), (sum(numMcs)-sum(allocatedMC)) as balMC, sum(numLSPack), sum(allocatedLS), (sum(numLSPack)-sum(allocatedLS)) as balLS,sum(invConfirm),filledwt,repackfrozenid,flag,repackedfrom,repkdQty,rgzQty,reglazedfrom,new_lot_IdNm,new_lot_Id,entryid,unit,company from (
				$qry
				union
				$fsQry
				) as x group by processcodeId, freezingStageId, frozenCodeId, mcPkgId ,new_lot_Id order by processCode asc, freezingStage asc, frznPkgCode asc, mcPkgCode asc  limit $fsLimit
				";
//echo "--------bbb<br>-------$nQry";

	echo "<br>$nQry<br>";		
		$result	= $this->databaseConnect->getRecords($nQry);
		return $result;
	}
	
	*/
	
	
	

/*	#Get Records For Selected Date Range
	function getPagingDFPRecs($fromDate, $tillDate, $offset, $limit, $filterProcessCode)
	{
		
		$whr		= " a.select_date>='".$fromDate."'  and a.select_date<='".$tillDate."'" ;
		if ($filterProcessCode>0) $whr .= " and tdfpe.processcode_id='$filterProcessCode' ";

		$groupBy	= " tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id";
		$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";
		$rsLimit 		= " $offset, $limit";

				$qry 	= "select a.id as id, a.select_date as selDate, mpc.fish_id as fishId, tdfpe.processcode_id as processcodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirmed, dfppo.po_id as poId, tdfpe.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack,
						((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as actualQty, ((sum(tdfpg.number_mc)*mcp.number_packs)+sum(tdfpg.number_loose_slab)) as totSlabs, 0 as allocatedMC, sum(tdfpg.number_loose_slab) as numLSPack, 0 as allocatedLS,0 as invConfirm,mfp.filled_wt as filledwt,a.repack_frozen_id as repackfrozenid,sum(flag) as flag,repacked_from as repackedfrom,sum(repkdQty) as repkdQty,sum(rgzQty) as rgzQty,reglazed_from as reglazedfrom
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry tdfpe on a.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id
				left join t_dailyfrozenpacking_po dfppo on dfppo.entry_id";
				

		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		//if ($rsLimit) $qry .= " limit ".$rsLimit;
		//echo $qry;

		// Frozen Stock Allocation
		//new comment
		//$fsWhr		= " dfppo.created_on<='".$tillDate."'  " ;
		//$fsWhr		= " dfppo.created_on>='".$fromDate."' and  dfppo.created_on<='".$tillDate."'";
		$fsWhr="dfpa.created_on>='".$fromDate."' and  dfpa.created_on<='".$tillDate."'";
		if ($filterProcessCode>0) $fsWhr .= " and dfppo.processcode_id='$filterProcessCode' ";

		$fsGroupBy	= " dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id";
		$fsOrderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";
		$fsLimit 		= " $offset, $limit";

	
			$fsQry 	= " select 
						0 as id,0 as selDate,mpc.fish_id as fishId, dfppo.processcode_id as processcodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, 0 as confirmed, dfppo.po_id as poId, dfppo.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack, 0 as pkdQty, 0 as numMcs, 0 as frozenQty, 0 as actualQty, 0 as totSlabs, sum(dfpa.number_mc) as allocatedMC, 0 as numLSPack, sum(dfpa.number_loose_slab) as allocatedLS,(if(tim.confirmed='Y',1,0)) as invConfirm,mfp.filled_wt as filledwt,0 as repackfrozenid,0 as flag,0 as repackedfrom,0 as repkdQty,0 as rgzQty,0 as reglazedfrom
			    from t_invoice_main tim join t_dailyfrozenpacking_po dfppo on tim.po_id=dfppo.po_id  join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id
				left join m_processcode mpc on mpc.id=dfppo.processcode_id 
				left join m_freezingstage mfs on mfs.id=dfppo.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=dfppo.frozencode_id
				left join m_mcpacking mcp on dfppo.mcpacking_id=mcp.id 
			";
		

		if ($fsWhr!="") $fsQry .= " where ".$fsWhr;
		if ($fsGroupBy) $fsQry .= " group by ".$fsGroupBy;
		

		$nQry = "select id, selDate, fishId, processcodeId, freezingStageId, frozenCodeId, processCode, freezingStage, frznPkgCode, confirmed, poId, mcPkgId, mcPkgCode, numPack, pkdQty, numMcs, frozenQty, actualQty, totSlabs, sum(allocatedMC), (sum(numMcs)-sum(allocatedMC)) as balMC, sum(numLSPack), sum(allocatedLS), (sum(numLSPack)-sum(allocatedLS)) as balLS,sum(invConfirm),filledwt,repackfrozenid,flag,repackedfrom,repkdQty,rgzQty,reglazedfrom from (
				$qry
				union
				$fsQry
				) as x group by processcodeId, freezingStageId, frozenCodeId, mcPkgId  order by processCode asc, freezingStage asc, frznPkgCode asc, mcPkgCode asc  limit $fsLimit
				";
		//echo "--------bbb<br>-------$nQry";

	//echo "<br>$nQry<br>";		
		$result	= $this->databaseConnect->getRecords($nQry);
		return $result;
	}*/

	function getDFPForDateRange($fromDate, $tillDate, $filterProcessCode)
	{		
		$whr		= " a.select_date>='".$fromDate."'  and a.select_date<='".$tillDate."'" ;
		if ($filterProcessCode>0) $whr .= " and tdfpe.processcode_id='$filterProcessCode' ";

		$groupBy	= " tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id,new_lot_Id";
		
		$orderBy	= " processCode asc, freezingStage asc,frznPkgCode asc, mcPkgCode asc";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";
		//$rsLimit 		= " $offset, $limit";

				$qry1 	= "select a.id as id, a.select_date as selDate, mpc.fish_id as fishId, tdfpe.processcode_id as processcodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirmed, dfppo.po_id as poId, tdfpe.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack,
						((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as actualQty, ((sum(tdfpg.number_mc)*mcp.number_packs)+sum(tdfpg.number_loose_slab)) as totSlabs, 0 as allocatedMC, sum(tdfpg.number_loose_slab) as numLSPack, 0 as allocatedLS,0 as invConfirm,mfp.filled_wt as filledwt,a.repack_frozen_id as repackfrozenid,sum(flag) as flag,repacked_from as repackedfrom,sum(repkdQty) as repkdQty,sum(rgzQty) as rgzQty,reglazed_from as reglazedfrom,'0' as new_lot_IdNm,'0' as new_lot_Id,tdfpe.id as entryid,'0' as unit, '0' as company
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry tdfpe on a.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id
				left join t_dailyfrozenpacking_po dfppo on dfppo.entry_id";
		

				$qry2 	= "select a.id as id, a.select_date as selDate, mpc.fish_id as fishId, tdfpe.processcode_id as processcodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirmed, dfppo.po_id as poId, tdfpe.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack,
						((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as actualQty, ((sum(tdfpg.number_mc)*mcp.number_packs)+sum(tdfpg.number_loose_slab)) as totSlabs, 0 as allocatedMC, sum(tdfpg.number_loose_slab) as numLSPack, 0 as allocatedLS,0 as invConfirm,mfp.filled_wt as filledwt,a.repack_frozen_id as repackfrozenid,sum(flag) as flag,repacked_from as repackedfrom,sum(repkdQty) as repkdQty,sum(rgzQty) as rgzQty,reglazed_from as reglazedfrom,concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,a.rm_lot_id as new_lot_Id,tdfpe.id as entryid,mg.unit_id as unit,mg.company_id as company 
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid tdfpe on a.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tdfpe.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id
				left join t_dailyfrozenpacking_po dfppo on dfppo.entry_id
				left join t_manage_rm_lotid mg on mg.id = a.rm_lot_id ";

		

		/*if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;*/
		
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		if ($groupBy) $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy;
		//
		//if ($limit) $qry .= " limit ".$limit;
		$qry=$qry1." union all ".$qry2; 
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		
		
		
		
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		//if ($rsLimit) $qry .= " limit ".$rsLimit;
		//echo $qry;

		// Frozen Stock Allocation
		//new comment
		//$fsWhr		= " dfppo.created_on<='".$tillDate."'  " ;
		//$fsWhr		= " dfppo.created_on>='".$fromDate."' and  dfppo.created_on<='".$tillDate."'";

		$fsWhr1="dfpa.created_on>='".$fromDate."' and  dfpa.created_on<='".$tillDate."'";
		if ($filterProcessCode>0) $fsWhr1 .= " and dfppo.processcode_id='$filterProcessCode' ";

		$fsGroupBy1	= " dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id";
		$fsOrderBy1	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";

		$fsWhr2="dfpa.created_on>='".$fromDate."' and  dfpa.created_on<='".$tillDate."'";
		if ($filterProcessCode>0) $fsWhr2 .= " and dfppo.processcode_id='$filterProcessCode' ";

		$fsGroupBy2	= " dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id,new_lot_Id";
		$fsOrderBy2= " mpc.code asc, mfs.rm_stage asc, mfp.code asc, mcp.code asc";

	//	$fsLimit 		= " $offset, $limit";

	
			$fsQry1 	= " select 
						0 as id,0 as selDate,mpc.fish_id as fishId, dfppo.processcode_id as processcodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, 0 as confirmed, dfppo.po_id as poId, dfppo.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack, 0 as pkdQty, 0 as numMcs, 0 as frozenQty, 0 as actualQty, 0 as totSlabs, sum(dfpa.number_mc) as allocatedMC, 0 as numLSPack, sum(dfpa.number_loose_slab) as allocatedLS,(if(tim.confirmed='Y',1,0)) as invConfirm,mfp.filled_wt as filledwt,0 as repackfrozenid,0 as flag,0 as repackedfrom,0 as repkdQty,0 as rgzQty,0 as reglazedfrom,'0' as new_lot_IdNm,'0' as new_lot_Id,'0' as entryid,'0' as unit, '0' as company
			    from t_invoice_main tim join t_dailyfrozenpacking_po dfppo on tim.po_id=dfppo.po_id  join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id
				left join m_processcode mpc on mpc.id=dfppo.processcode_id 
				left join m_freezingstage mfs on mfs.id=dfppo.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=dfppo.frozencode_id
				left join m_mcpacking mcp on dfppo.mcpacking_id=mcp.id 
			";
		
			$fsQry2 	= " select 
						0 as id,0 as selDate,mpc.fish_id as fishId, dfppo.processcode_id as processcodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, 0 as confirmed, dfppo.po_id as poId, dfppo.mcpacking_id as mcPkgId, mcp.code as mcPkgCode, mcp.number_packs as numPack, 0 as pkdQty, 0 as numMcs, 0 as frozenQty, 0 as actualQty, 0 as totSlabs, sum(dfpa.number_mc) as allocatedMC, 0 as numLSPack, sum(dfpa.number_loose_slab) as allocatedLS,(if(tim.confirmed='Y',1,0)) as invConfirm,mfp.filled_wt as filledwt,0 as repackfrozenid,0 as flag,0 as repackedfrom,0 as repkdQty,0 as rgzQty,0 as reglazedfrom,concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,dfppo.rm_lot_id as new_lot_Id,dfppo.id as entryid,mg.unit_id as unit,mg.company_id as company 
			    from t_invoice_main tim join t_dailyfrozenpacking_po_rmlotid dfppo on tim.po_id=dfppo.po_id  join t_dailyfrozenpacking_allocate_rmlotid dfpa on dfppo.id=dfpa.po_entry_id
				left join m_processcode mpc on mpc.id=dfppo.processcode_id 
				left join m_freezingstage mfs on mfs.id=dfppo.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=dfppo.frozencode_id
				left join m_mcpacking mcp on dfppo.mcpacking_id=mcp.id 
				left join t_manage_rm_lotid mg on mg.id = dfppo.rm_lot_id 
			";




		if ($fsWhr1!="") $fsQry1 .= " where ".$fsWhr1;			if ($fsWhr2!="") $fsQry2.= " where ".$fsWhr2;
		if ($fsGroupBy1) $fsQry1 .= " group by ".$fsGroupBy1;		if ($fsGroupBy2) $fsQry2.= " group by ".$fsGroupBy2;
		
		$fsQry=$fsQry1." union all ".$fsQry2; 
	//echo $fsQry;
		$nQry = "select id, selDate, fishId, processcodeId, freezingStageId, frozenCodeId, processCode, freezingStage, frznPkgCode, confirmed, poId, mcPkgId, mcPkgCode, numPack, pkdQty, numMcs, frozenQty, actualQty, totSlabs, sum(allocatedMC), (sum(numMcs)-sum(allocatedMC)) as balMC, sum(numLSPack), sum(allocatedLS), (sum(numLSPack)-sum(allocatedLS)) as balLS,sum(invConfirm),filledwt,repackfrozenid,flag,repackedfrom,repkdQty,rgzQty,reglazedfrom,new_lot_IdNm,new_lot_Id,entryid,unit,company from (
				$qry
				union
				$fsQry
				) as x group by processcodeId, freezingStageId, frozenCodeId, mcPkgId ,new_lot_Id order by processCode asc, freezingStage asc, frznPkgCode asc, mcPkgCode asc 
				";
//echo "--------bbb<br>-------$nQry";

	//echo "<br>$nQry<br>";		
		$result	= $this->databaseConnect->getRecords($nQry);
		return $result;
	}


	function getFrozenStockProcessCodes($fromDate,$tillDate)
	{
		//$whr		= " a.select_date<='".$tillDate."'  and a.unit is not null and b.processcode_id is not null and b.id not in (select entry_id from t_dailyfrozenpacking_po) ";
		
		$whr		= " a.select_date>='".$fromDate."'  and a.select_date<='".$tillDate."' and b.processcode_id is not null and b.id not in (select entry_id from t_dailyfrozenpacking_po) ";

		$groupBy	= " b.processcode_id";
		$orderBy	= " mpc.code asc ";

		$qry 	= " select b.processcode_id, mpc.code as processCode from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;

		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}



	
	
	# Get Packing  based on id 
	function find($dailyFrozenPackingMainId, $frozenPackingEntryId)
	{
		$qry	=	"select a.id, a.select_date, a.unit, b.frozen_lot_id, b.id, b.fish_id, b.processcode_id, b.freezing_stage_id, b.eucode_id, b.brand_id, b.frozencode_id, b.mcpacking_id, b.export_lot_id, a.processor_id, b.quality_id, b.brand_from, b.customer_id from t_dailyfrozenpacking_main a, t_dailyfrozenpacking_entry b where a.id=b.main_id and a.id='$dailyFrozenPackingMainId' and b.id='$frozenPackingEntryId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	//------------------------ Delete From Main Table------------------------------

	# Delete a Daily Frozen Packing Grade Rec
	function deleteFrozenPackingGradeRec($dailyFrozenPackingEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_grade where entry_id=$dailyFrozenPackingEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Daily Frozen Packing Entry Rec
	function deletePackingEntryRec($dailyFrozenPackingEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_entry where id=$dailyFrozenPackingEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Checking Record Exisitng
	function checkRecordsExist($dailyFrozenPackingMainId)
	{
		$qry	=	"select b.main_id from t_dailyfrozenpacking_main a, t_dailyfrozenpacking_entry b  where  a.id=b.main_id and b.main_id='$dailyFrozenPackingMainId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Delete a Daily Frozen Packing Main Rec
	function deleteDailyFrozenPackingMainRec($dailyFrozenPackingMainId)
	{
		$qry	= " delete from t_dailyfrozenpacking_main where id=$dailyFrozenPackingMainId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	//------------------------ Delete End------------------------------

//IFRAME SECTION
###############################################################################
	#Add Grade Records
	function addFrozenPackingGrade($entryId, $gradeId, $numMC, $numLooseSlab)
	{
		$qry	= " insert into t_dailyfrozenpacking_grade (entry_id,grade_id,number_mc,number_loose_slab) values($entryId, $gradeId, $numMC, $numLooseSlab)";				
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	

	#update Frozen Packing Grades
	function updateFrozenPackingGrade($gradeEntryId, $gradeId, $numMC, $numLooseSlab)
	{
		$qry	= " update t_dailyfrozenpacking_grade set grade_id='$gradeId',number_mc='$numMC',number_loose_slab='$numLooseSlab' where id='$gradeEntryId'";

		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	#Delete a Grade Rec
	function deletePackingGradeRec($gradeEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_grade where id=$gradeEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Grade selection for Frozen Grades
 	function fetchFrozenGradeRecords($codeId,$entryId)
	{
 		$qry	= "select a.grade_id, c.code, b.id, b.entry_id, b.number_mc, b.number_loose_slab from m_processcode2grade a left join t_dailyfrozenpacking_grade b on a.grade_id=b.grade_id and b.entry_id='$entryId', m_grade c where a.grade_id = c.id and a.processcode_id='$codeId' and a.unit_select='f' order by c.code asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	#########################

	#Filter Unit based on Processor
	function getProcessorForUnits($unitId)
	{
		$qry	= "select a.id, a.name, a.code from m_preprocessor a, m_preprocessor2plant b where a.id=b.processor_id and b.plant_id='$unitId' order by a.name asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Filter Lot Id Records Based on Date
	function fetchLotIdRecords($selDate)
	{
		$qry	= "select a.id, b.id, c.freezer_name from t_dailyactivitychart_main a, t_dailyactivitychart_entry b, m_freezercapacity c where a.id=b.main_id and c.id=b.freezer_no and a.entry_date='$selDate' and a.flag=1 order by a.id asc, b.id asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find number of packing Details of each Entry Id
	function  getNumOfPacking($dailyFrozenPackingEntryId)
	{
		$qry = "select sum(number_mc), sum(number_loose_slab) from t_dailyfrozenpacking_grade where entry_id=$dailyFrozenPackingEntryId group by entry_id";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?array($result[0], $result[1]):false;
	}

	# Get QE Process Codes an return comma seperated PC
	function getQEProcessCodes($selQuickEntryList)
	{
		$qry = " select id, fish_id, processcode_id from t_fznpakng_qel_entry where qe_entry_id='$selQuickEntryList' ";
		$result	= $this->databaseConnect->getRecords($qry);

		$resultArr = array();
		$processCodes = "";
		if (sizeof($result)>0) {
			$i = 0;
			foreach ($result as $r) {
				$processCodeId = $r[2];
				$resultArr[$i] = $processCodeId;
				$i++;
			}
			$processCodes = implode(",",$resultArr);
		}
		return $processCodes;
	}

	# QE Grade Records
	function qeGradeRecords($selQuickEntryList)
	{
		$qry = "select a.grade_id, c.code from t_fznpakng_qel_grade a, m_grade c where a.grade_id = c.id and a.qe_entry_id='$selQuickEntryList' and a.active='Y' order by a.display_order asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Raw data Records
	function getSelQELProcessCodeRecs($selQuickEntryList)
	{	
		$qry = " select a.id, a.fish_id, a.processcode_id, b.code as pc, c.code as fc from t_fznpakng_qel_entry a, m_processcode b, m_fish c where a.fish_id=c.id and a.processcode_id=b.id and a.qe_entry_id='$selQuickEntryList'  order by a.id asc  ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get QE Rec
	function getQERec($selQuickEntryList)
	{
		$qry = " select freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, brand_from from t_fznpakng_quick_entry where id='$selQuickEntryList' ";
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4],$rec[5],$rec[6],$rec[7],$rec[8], $rec[9]);

		//$qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom
	}
	# Check Process Code Has Grade
	function processCodeHasGrade($processCodeId, $gradeId)
	{
		$qry = " select a.grade_id from m_processcode2grade a where a.processcode_id='$processCodeId' and a.grade_id='$gradeId' and a.unit_select='f'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Update daily Frozen Packing Entry Table Rec
	function addDailyFrozenPackingEntry($mainId, $hidFishId, $hidProcesscodeId, $qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom)
	{
		$qry	= "insert into t_dailyfrozenpacking_entry (main_id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, brand_from) values('$mainId', '$hidFishId', '$hidProcesscodeId', '$qeFreezingStageId', '$qeEUCodeId', '$qeBrandId', '$qeFrozenCodeId', '$qeMCPackingId', '$qeFrozenLotId', '$qeExportLotId', '$qeQualityId', '$qeCustomerId', '$qeBrandFrom')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function getBalnkDFPERec($mainId)
	{
		$qry = "select b.id from t_dailyfrozenpacking_entry b  where b.fish_id is null and b.processcode_id is null and b.main_id='$mainId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array($result[0]):array();	
	}

	# QEL Fish Recs
	function getQELFishRecs($processCodeId)
	{		
		$qry = "select a.qe_entry_id, a.fish_id, qelm.name from t_fznpakng_qel_entry a, t_fznpakng_quick_entry qelm where qelm.id=a.qe_entry_id and a.processcode_id='$processCodeId' group by qe_entry_id";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function chkQELRecExist($selDate, $processor, $fishId, $pCodeId, $qeFrozenCodeId, $qeMCPackingId, $qeQualityId)
	{
		$whr = "dfpm.id=dfpe.main_id and dfpm.select_date='$selDate' and dfpe.fish_id='$fishId' and dfpe.processcode_id='$pCodeId' ";

		if ($processor) $whr .= " and dfpm.processor_id='$processor'";

		if ($qeFrozenCodeId) $whr .= "and dfpe.frozencode_id='$qeFrozenCodeId'";

		if ($qeMCPackingId)  $whr .= "and dfpe.mcpacking_id='$qeMCPackingId'";
		else $whr .= "and dfpe.mcpacking_id=0";

		if ($qeQualityId) $whr .= "and dfpe.quality_id='$qeQualityId'";

		$qry = "select dfpm.id from t_dailyfrozenpacking_main dfpm, t_dailyfrozenpacking_entry dfpe ";
		if ($whr) $qry .= " where ".$whr;
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# get all recs
	function frznPkgAllRecords()
	{	
		$orderBy = "a.name asc";
		
		$qry	= "select a.id, a.name from t_fznpakng_quick_entry a ";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		$fznArr = array();
		foreach ($result as $qr) {
			$qelMainId 	= $qr[0];
			$qelName	= $qr[1];
			$fznArr[$qelMainId] = $qelName;
		}
		return $fznArr;
	}

	# Get Raw data Records
	function getQELWiseProcessCodeRecs($selQuickEntryList)
	{	
		$qry = " select a.processcode_id, a.fish_id, b.code as pc, c.code as fc from t_fznpakng_qel_entry a, m_processcode b, m_fish c where a.fish_id=c.id and a.processcode_id=b.id and a.qe_entry_id='$selQuickEntryList'  order by a.id asc  ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# get Date Wise Processor
	function getSelDFPProcessor($selDate)
	{
		$qry	= "select a.processor_id, mp.name from t_dailyfrozenpacking_main a, m_preprocessor mp where a.processor_id=mp.id and a.select_date='$selDate' group by a.processor_id ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Production Details
	function getProductionRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId)
	{
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id  where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."'";
			
		//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Production Details
	function getProductionRecsRMLot($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId,$rmLotID)
	{
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id  where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.rm_lot_id='$rmLotID' ";
			
		//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# get Grade Recs
	function getProductionGradeRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		//tdfpm.select_date>='".$fromDate."' and
		$qry = "select tdfpg.grade_id, mg.code  from 
			t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
			join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			left join m_grade mg on tdfpg.grade_id=mg.id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId'   
			and tdfpm.select_date<='".$tillDate."' group by tdfpg.grade_id order by mg.code asc ";	
		//echo "Grade===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# MC Pkg Recs
	function getMCPkgRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId)
	{	
		$qry = "select tdfpe.mcpacking_id, mcp.code as mcPkgCode from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id  where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."'  and tdfpm.unit is not null and mcp.code is not null group by  tdfpe.mcpacking_id order by mcp.code asc";

		//echo "MCs=><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Frozen Lot Id's
	function getFrznLotIds($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId)
	{
		// $qry	= "select a.id, b.id, c.freezer_name from t_dailyactivitychart_main a, t_dailyactivitychart_entry b, m_freezercapacity c where a.id=b.main_id and c.id=b.freezer_no and a.entry_date='$selDate' and a.flag=1 order by a.id asc, b.id asc";
		$qry = "select 
				tdfpe.frozen_lot_id, mfc.freezer_name 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join t_dailyfreezingchart_entry tdfc on tdfpe.frozen_lot_id=tdfc.id 
				left join m_freezercapacity mfc on mfc.id=tdfc.freezer_no 
			where 
				tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."'  and tdfpm.unit is not null and mfc.freezer_name is not null 
			group by tdfpe.frozen_lot_id order by mfc.freezer_name asc
			";

		//echo "MCs=><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Slab Details
	function getSlab($dFrznPkgEntryId, $sGradeId)
	{
		$qry = " select id, number_mc, number_loose_slab from t_dailyfrozenpacking_grade where entry_id='$dFrznPkgEntryId' and grade_id='$sGradeId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):array();	
	}

	#Update daily Frozen Packing Entry Table Rec
	function updateDFPEntry($dFrznPkgEntryId, $frozenLotId, $mcPackingId)
	{		
		$qry = "update t_dailyfrozenpacking_entry  set  mcpacking_id='$mcPackingId', frozen_lot_id='$frozenLotId' where id='$dFrznPkgEntryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	# Update grade Rec
	function updateDFPGradeEntry($gradeEntryId, $numMC, $numLS)
	{
		$qry	= " update t_dailyfrozenpacking_grade set number_mc='$numMC', number_loose_slab='$numLS' where id='$gradeEntryId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# get Pkd Qty based on entry id
	# Return Packed Qty, Num of MCs, Frozen qty (based on decl.wt)
	function getPkdQty($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		//and tdfpm.select_date>='".$fromDate."' 
		$qry = "select ((sum(tdfpg.number_mc)*mfp.filled_wt*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId'  and tdfpm.select_date<='".$tillDate."' and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po) 
			group by tdfpg.entry_id 
			 ";	
		//echo "PkdQty===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$totQty = 0;
		$totNumMc = 0;
		$totalPkdQty= 0;
		$totFrozenQty = 0;		
		$totActualQty = 0;
		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$qty 	= $r[0];
				$numMc	= $r[1];
				$frozenQty = $r[2];
				$actualQty = $r[3];
				$totActualQty += $actualQty;
				$totNumMc += $numMc;
				$totQty += $qty;
				$totFrozenQty += $frozenQty;
			}
		}
		$totalPkdQty = number_format($totQty,2,'.','');
		$totFrozenQty = number_format($totFrozenQty,2,'.','');
		$totActualQty = number_format($totActualQty,2,'.','');

		return (sizeof($totQty)>0)?array($totalPkdQty,$totNumMc,$totFrozenQty,$totActualQty):array();
	}

	// Get Qty
	function getPacks($processCodeId, $freezingStageId, $frozenCodeId)
	{
		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  
			group by tdfpg.entry_id 
			 ";	
		//echo "PkdQty===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		$totNumMc = 0;
		$totNumLS = 0;
		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$numMc	= $r[0];
				$numLS	= $r[1];

				$totNumMc += $numMc;
				$totNumLS += $numLS;
			}
		}

		return (sizeof($totQty)>0)?array($totNumMc,$totNumLS):array();
	}

	// Get MC Packing records
	function getFPMCPkg($processCodeId, $freezingStageId, $frozenCodeId)
	{	
		$qry = "select tdfpe.mcpacking_id, mcp.code as mcPkgCode, mcp.number_packs as numPacks from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id  where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpm.unit is not null and mcp.code is not null group by  tdfpe.mcpacking_id order by mcp.code asc";

		//echo "MCs=><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function insertDFPPORecs($dfpEntryID, $POId, $totalSlabs, $totalQty, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId)
	{
		$qry	= "insert into t_dailyfrozenpacking_po (entry_id, po_id, total_slabs, total_qty, created_on, processcode_id, freezing_stage_id, frozencode_id, mcpacking_id, created_by) values('$dfpEntryID', '$POId', '$totalSlabs', '$totalQty', NOW(), '$processId', '$freezingStageId', '$frozenCodeId', '$MCPkgId', '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//$this->updatePurordRmEntryDelivStatus($POId);
		return $insertStatus;
		
	}
	
	function insertDFPPORMLotRecs($dfpEntryID, $POId, $totalSlabs, $totalQty, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$rmLotID)
	{
		$qry	= "insert into t_dailyfrozenpacking_po_rmlotid (entry_id, po_id, rm_lot_id, total_slabs, total_qty, created_on, processcode_id, freezing_stage_id, frozencode_id, mcpacking_id, created_by) values('$dfpEntryID', '$POId','$rmLotID', '$totalSlabs', '$totalQty', NOW(), '$processId', '$freezingStageId', '$frozenCodeId', '$MCPkgId', '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//$this->updatePurordRmEntryDelivStatus($POId);
		return $insertStatus;
		
	}
	
	
	
function getRmentryid($POId,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$gradeId)
	{
	$qry="select id from t_purchaseorder_rm_entry where processcode_id='$processId' and freezingstage_id='$freezingStageId' and frozencode_id='$frozenCodeId' and mcpacking_id='$MCPkgId' and grade_id='$gradeId' and main_id='$POId'";
	//echo $qry;
	$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	

	}
	function updatePurordRmEntryDelivStatus($POId)
	{
    $qry="update t_purchaseorder_rm_entry set delivered_status=1 where main_id='$POId'";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function insertDFPPOGradeForAllocation($dfpPOEntryId, $gradeId, $numMC, $numLS,$POEntryId)
	{
		$qry	= "insert into t_dailyfrozenpacking_allocate (po_entry_id, grade_id, number_mc, number_loose_slab, created_on,po_rm_id) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numLS', NOW(),'$POEntryId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function insertDFPPOGradeForAllocationRMLot($dfpPOEntryId, $gradeId, $numMC, $numLS,$POEntryId)
	{
		$qry	= "insert into t_dailyfrozenpacking_allocate_rmlotid (po_entry_id, grade_id, number_mc, number_loose_slab, created_on,po_rm_id) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numLS', NOW(),'$POEntryId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function insertDFPPOGradeForThawing($dfpPOEntryId, $gradeId, $numMC,$numLS,$numMCStock)
	{
		$qry	= "insert into t_dailythawing_grade(main_id,grade_id,number_mc_thawing,number_mc_stock) values('$dfpPOEntryId', '$gradeId', '$numMC','$numMCStock')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function insertDFPPOGradeForThawingRMLot($dfpPOEntryId, $gradeId, $numMC,$numLS,$numMCStock)
	{
		$qry	= "insert into t_dailythawing_grade_rmlotid(main_id,grade_id,number_mc_thawing,number_mc_stock) values('$dfpPOEntryId', '$gradeId', '$numMC','$numMCStock')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function insertDailyThawing($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fishIdth)
	{
		$qry	= "insert into t_dailythawing(select_date,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id,fish_id) values ('$selectDate',$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fishIdth)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	function insertDailyThawingRMLot($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fishIdth,$rmlotid)
	{
		$qry	= "insert into t_dailythawing_rmlotid(select_date,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id,fish_id,rm_lot_id) values ('$selectDate','$processId','$freezingStageId','$frozenCodeId','$MCPkgId','$fishIdth','$rmlotid')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	
	

	# Get Production Details one row
	function getAllocateProductionRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		//tdfpm.select_date>='".$fromDate."' and  and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po)
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id as MainId, dfppo.id as POEntryId, dfppo.po_id as POID,tdfpe.processcode_id,tdfpe.freezing_stage_id,tdfpe.frozencode_id,tdfpe.mcpacking_id  
		from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
		left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id 
		left join t_dailyfrozenpacking_po dfppo ON dfppo.entry_id=tdfpe.id  
		where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId'
		and tdfpm.select_date<='".$tillDate."' limit 0,1";
			
		//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getAllocateProductionRMlotIdRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId, $rmLotID)
	{
		//tdfpm.select_date>='".$fromDate."' and  and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po)
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id as MainId, dfppo.id as POEntryId, dfppo.po_id as POID,tdfpe.processcode_id,tdfpe.freezing_stage_id,tdfpe.frozencode_id,tdfpe.mcpacking_id ,tdfpm.rm_lot_id 
		from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id 
		left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id 
		left join t_dailyfrozenpacking_po dfppo ON dfppo.entry_id=tdfpe.id  
		where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.rm_lot_id='$rmLotID'
		and tdfpm.select_date<='".$tillDate."' limit 0,1";
			
	//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	function getAllocatedSlab($poEntryId, $sGradeId)
	{
		$qry = " select id, number_mc, number_loose_slab from t_dailyfrozenpacking_allocate where po_entry_id='$poEntryId' and grade_id='$sGradeId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):array();	
	}

	function getThaQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate)
	{
		$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' ";
		//echo "666666666666".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	

	}
	function getThaQtyRMLotId($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$rmlotid)
	{
		$qry="select sum(number_mc_thawing) from t_dailythawing_rmlotid td left join t_dailythawing_grade_rmlotid tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and rm_lot_id='$rmlotid'";
		//echo "666666666666".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	

	}
	function getThaGradeQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId)
	{
	$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";
		//echo "%%".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
	
	}
	function getThaGradeQtyLot($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId,$rmLotID)
	{
		$qry="select sum(number_mc_thawing) from t_dailythawing_rmlotid td left join t_dailythawing_grade_rmlotid tdg on td.id=tdg.main_id where td.processcode_id='$processId' and td.freezing_stage_id='$freezingStage' and td.frozencode_id='$frozenCode' and td.select_date>='$selectDate' and td.mcpacking_id='$stkAllocateMCPkgId' and rm_lot_id='$rmLotID' and tdg.grade_id='$sGradeId' ";
		//echo "%%".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
	}
	function updateDFPPORecs($dfpEntryID, $POId, $totalSlabs, $totalQty, $POEntryId)
	{

		$qry	= "update t_dailyfrozenpacking_po SET entry_id='$dfpEntryID', po_id='$POId', total_slabs='$totalSlabs', total_qty='$totalQty' WHERE id='$POEntryId'";
		//echo $qry;		
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function updateDFPPORMLotRecs($dfpEntryID, $POId, $totalSlabs, $totalQty, $POEntryId,$rmLotID)
	{

		$qry	= "update t_dailyfrozenpacking_po_rmlotid SET entry_id='$dfpEntryID', po_id='$POId', rm_lot_id='$rmLotID', total_slabs='$totalSlabs', total_qty='$totalQty' WHERE id='$POEntryId'";
		//echo $qry;		
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function updateDFPPOGradeForAllocation($dfpPOEntryId, $gradeId, $numMC, $numLS, $allocateGradeEntryId)
	{
		$qry	= "update t_dailyfrozenpacking_allocate SET number_mc='$numMC', number_loose_slab='$numLS' WHERE po_entry_id='$dfpPOEntryId' and grade_id='$gradeId' and id='$allocateGradeEntryId'";
		//echo $qry;				
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updateDFPPOGradeForAllocationRMLot($dfpPOEntryId, $gradeId, $numMC, $numLS, $allocateGradeEntryId)
	{
		$qry	= "update t_dailyfrozenpacking_allocate_rmlotid SET number_mc='$numMC', number_loose_slab='$numLS' WHERE po_entry_id='$dfpPOEntryId' and grade_id='$gradeId' and id='$allocateGradeEntryId'";
		//echo $qry;				
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteAllocationEntry($allocationPOEntryId)
	{
		$deleteFromAllocate = $this->deleteFromDFPAllocate($allocationPOEntryId);
		if ($deleteFromAllocate) {
			$this->deleteFromDFPPO($allocationPOEntryId);
			return true;
		} 
		return false;
	}
	function deleteAllocationEntryRMlot($allocationPOEntryId)
	{
		$deleteFromAllocate = $this->deleteFromDFPAllocateRMLot($allocationPOEntryId);
		if ($deleteFromAllocate) {
			$this->deleteFromDFPPORMlot($allocationPOEntryId);
			return true;
		} 
		return false;
	}
	function deleteFromDFPPORMlot($poEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_po_rmlotid where id=$poEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteFromDFPPO($poEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_po where id=$poEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteFromDFPAllocate($poEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_allocate where po_entry_id=$poEntryId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteFromDFPAllocateRMLot($poEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_allocate_rmlotid where po_entry_id=$poEntryId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	// Get Qty
	function getAvailablePacks($processCodeId, $freezingStageId, $frozenCodeId, $gradeId, $mcPkgId,$fromDate,$tillDate)
	{
		

		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS, 0 as allocatedMC , 0 as allocatedLS, tdfpe.processcode_id as processCodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, tdfpe.mcpacking_id as mcPackingId   
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
			
			 $fsQry = "select 0 as numMc, 0 as numLS, sum(dfpa.number_mc) as allocatedMC , sum(dfpa.number_loose_slab) as allocatedLS, dfppo.processcode_id as processCodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, dfppo.mcpacking_id as mcPackingId 
			from 
				t_dailyfrozenpacking_po dfppo join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id 
			where dfppo.processcode_id='$processCodeId' and dfppo.freezing_stage_id='$freezingStageId' and dfppo.frozencode_id='$frozenCodeId'  and dfpa.grade_id='$gradeId' and dfppo.mcpacking_id='$mcPkgId' and dfpa.created_on>='$fromDate' and dfpa.created_on<='$tillDate'
			group by dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id
			 ";
	//echo "<br>$fsQry";
		$uQry = " select sum(numMc), sum(numLS), sum(allocatedMC), sum(allocatedLS), processCodeId, freezingStageId, frozenCodeId, mcPackingId, (sum(numMc)-sum(allocatedMC)) as balMC, (sum(numLS)-sum(allocatedLS)) as balLS from (
					$qry
					union
					$fsQry
				 ) as x	group by processCodeId, freezingStageId, frozenCodeId, mcPackingId";
	//echo "<br>pppp+++++++$uQry";

		//echo "getAvailablePacks===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($uQry);
	
		return (sizeof($result)>0)?array($result[0][8],$result[0][9]):array();
		
	}
	
	function getAvailablePacksRMLot($processCodeId, $freezingStageId, $frozenCodeId, $gradeId, $mcPkgId,$fromDate,$tillDate,$rmLotID)
	{
		
		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS, 0 as allocatedMC , 0 as allocatedLS, tdfpe.processcode_id as processCodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, tdfpe.mcpacking_id as mcPackingId   
			from 
				t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' and tdfpm.rm_lot_id='$rmLotID'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id 
			 ";	
			//echo $qry;
	
			 $fsQry = "select 0 as numMc, 0 as numLS, sum(dfpa.number_mc) as allocatedMC , sum(dfpa.number_loose_slab) as allocatedLS, dfppo.processcode_id as processCodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, dfppo.mcpacking_id as mcPackingId 
			from 
				t_dailyfrozenpacking_po_rmlotid dfppo join t_dailyfrozenpacking_allocate_rmlotid dfpa on dfppo.id=dfpa.po_entry_id 
			where dfppo.processcode_id='$processCodeId' and dfppo.freezing_stage_id='$freezingStageId' and dfppo.frozencode_id='$frozenCodeId'  and dfpa.grade_id='$gradeId' and dfppo.mcpacking_id='$mcPkgId' and dfpa.created_on>='$fromDate' and dfpa.created_on<='$tillDate'  and dfppo.rm_lot_id='$rmLotID'
			group by dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id,dfppo.rm_lot_id
			 ";
	//echo "<br>$fsQry";
		$uQry = " select sum(numMc), sum(numLS), sum(allocatedMC), sum(allocatedLS), processCodeId, freezingStageId, frozenCodeId, mcPackingId, (sum(numMc)-sum(allocatedMC)) as balMC, (sum(numLS)-sum(allocatedLS)) as balLS from (
					$qry
					union
					$fsQry
				 ) as x	group by processCodeId, freezingStageId, frozenCodeId, mcPackingId";
	//echo "<br>pppp+++++++$uQry";

		//echo "getAvailablePacks===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($uQry);
		
		return (sizeof($result)>0)?array($result[0][8],$result[0][9]):array();
		
	}
	
	
	
	function getAvailablePacksDis($processCodeId, $freezingStageId, $frozenCodeId, $gradeId, $mcPkgId, $tillDate,$poId)
	{
		// and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po)
		/*
		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
		*/

		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS, 0 as allocatedMC , 0 as allocatedLS, tdfpe.processcode_id as processCodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, tdfpe.mcpacking_id as mcPackingId   
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date<='$tillDate'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
		
		$fsQry = "select 0 as numMc, 0 as numLS, sum(dfpa.number_mc) as allocatedMC , sum(dfpa.number_loose_slab) as allocatedLS, dfppo.processcode_id as processCodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, dfppo.mcpacking_id as mcPackingId 
			from 
				t_dailyfrozenpacking_po dfppo join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id 
			where dfppo.processcode_id='$processCodeId' and dfppo.freezing_stage_id='$freezingStageId' and dfppo.frozencode_id='$frozenCodeId'  and dfpa.grade_id='$gradeId' and dfppo.mcpacking_id='$mcPkgId' and dfppo.created_on<='$tillDate' and dfppo.po_id=$poId
			group by dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id
			 ";	
		//echo "<br>$fsQry";
		$uQry = " select sum(numMc), sum(numLS), sum(allocatedMC), sum(allocatedLS), processCodeId, freezingStageId, frozenCodeId, mcPackingId, (sum(numMc)-sum(allocatedMC)) as balMC, (sum(numLS)-sum(allocatedLS)) as balLS from (
					$qry
					union
					$fsQry
				 ) as x	group by processCodeId, freezingStageId, frozenCodeId, mcPackingId";
		//echo "<br>------------------$uQry";

		//echo "getAvailablePacks===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($uQry);
		/*
		$totNumMc = 0;
		$totNumLS = 0;
		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$numMc	= $r[0];
				$numLS	= $r[1];
				$totNumMc += $numMc;
				$totNumLS += $numLS;
			}
		}
		*/
		return (sizeof($result)>0)?array($result[0][2],$result[0][3]):array();
		
	}

	/*function insertAllocatedEntry($tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId)
	{
		$qry = "select tdfpe.id from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$MCPkgId' and tdfpm.select_date<='$tillDate'";
			
		//echo "insertAllocatedEntry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$entryId = $r[0];
				$insertRec = $this->insertDFPAllocatedRec($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId,$allocateId);					
			}
		}

		return true;
	}
	function insertDFPAllocatedRec($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId)
	{
		$qry	= "insert into t_dailyfrozenpacking_allocated_entry (processcode_id, freezing_stage_id, frozencode_id, mcpacking_id, entry_id, created_on, created_by) values('$processCodeId','$freezingStageId','$frozenCodeId','$MCPkgId','$entryId',Now(), '$userId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function insertAllocatedEntryRMLot($tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$hidrmLotID)
	{
		$qry = "select tdfpe.id from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$MCPkgId' and tdfpm.select_date<='$tillDate' and 
		tdfpm.rm_lot_id='$hidrmLotID'";
			
		//echo "insertAllocatedEntry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$entryId = $r[0];
				$insertRec = $this->insertDFPAllocatedRecRMLot($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId,$allocateId);					
			}
		}

		return true;
	}
	
	function insertDFPAllocatedRecRMLot($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId)
	{
		$qry	= "insert into t_dailyfrozenpacking_allocated_entry_rmlotid (processcode_id, freezing_stage_id, frozencode_id, mcpacking_id, entry_id, created_on, created_by) values('$processCodeId','$freezingStageId','$frozenCodeId','$MCPkgId','$entryId',Now(), '$userId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	*/
	function insertAllocatedEntry($tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$dfpPOEntryId)
	{//echo "hhhj".$dfpPOEntryId;
		$qry = "select tdfpe.id from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$MCPkgId' and tdfpm.select_date<='$tillDate'";
			
		//echo "insertAllocatedEntry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$entryId = $r[0];
				$insertRec = $this->insertDFPAllocatedRec($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId,$allocateId,$dfpPOEntryId);					
			}
		}

		return true;
	}
	function insertDFPAllocatedRec($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId,$allocateId,$dfpPOEntryId)
	{
		
		$qry	= "insert into t_dailyfrozenpacking_allocated_entry (processcode_id, freezing_stage_id, frozencode_id, mcpacking_id, entry_id,po_entry_id, created_on, created_by) values('$processCodeId','$freezingStageId','$frozenCodeId','$MCPkgId','$entryId','$dfpPOEntryId',Now(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function insertAllocatedEntryRMLot($tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$hidrmLotID,$dfpPOEntryId)
	{
		$qry = "select tdfpe.id from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$MCPkgId' and tdfpm.select_date<='$tillDate' and 
		tdfpm.rm_lot_id='$hidrmLotID'";
			
		//echo "insertAllocatedEntry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$entryId = $r[0];
				$insertRec = $this->insertDFPAllocatedRecRMLot($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId,$allocateId,$dfpPOEntryId);					
			}
		}

		return true;
	}
	
	function insertDFPAllocatedRecRMLot($processCodeId, $freezingStageId, $frozenCodeId, $MCPkgId, $entryId, $userId,$allocateId,$dfpPOEntryId)
	{
		$qry	= "insert into t_dailyfrozenpacking_allocated_entry_rmlotid (processcode_id, freezing_stage_id, frozencode_id, mcpacking_id, entry_id,po_entry_id, created_on, created_by) values('$processCodeId','$freezingStageId','$frozenCodeId','$MCPkgId','$entryId','$dfpPOEntryId',Now(), '$userId')";

		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

function glazeFrzncode($fishId, $processCodeId,$code)
	{
		$qry = "select fqem.frozencode_id, mfp.code from t_fznpakng_quick_entry fqem join t_fznpakng_qel_entry fpqe on fqem.id=fpqe.qe_entry_id 
			left join m_frozenpacking mfp on mfp.id=fqem.frozencode_id where fpqe.fish_id='$fishId' and fpqe.processcode_id='$processCodeId'
			group by fqem.frozencode_id order by mfp.code asc
			";

		//echo "Qry1==><br>$qry<br>";

		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array();
		$validArr = array();
		$frznIds = "";
		$resultArr['FS1'] = "--Select--";
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$resultArr[$rec[0]] = $rec[1];
				$validArr[] = $rec[0];
			}

			$frznIds = implode(',',$validArr);
		}

		if ($frznIds!="") $resultArr['FS2'] = "--Other--";

		# Get All Recs
		$qryAll = "select id, code from m_frozenpacking";
		//if ($frznIds!="") $qryAll .= " where id not in ($frznIds) and code like '$code'";
		if ($frznIds!="") $qryAll .= " where id not in ($frznIds)";
		$qryAll .= " order by code asc";
		//echo "Qry2==><br>$qryAll<br>";
		$resultAll	= $this->databaseConnect->getRecords($qryAll);

		if (sizeof($resultAll)>0) {
			foreach ($resultAll as $ra) {
				$resultArr[$ra[0]] = $ra[1];
			}
		}
		
		//$qry
		return $resultArr;
	}


	function getfrozenCodeValue($frozenID)
	{
	//$qry = " select unit,freezing_id, glaze_id,decl_wt,id from m_frozenpacking where code=trim('$frozenCode')";
	$qry = " select unit,freezing_id,glaze_id,decl_wt,id from m_frozenpacking where id=$frozenID";
	//echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4]);

	}

	function getAllfrozenCodeValues($unit,$freeId,$glazeFreezId,$declWt,$lastVal)
	{
		//$qry="select id,code from  m_frozenpacking where unit='$unit' and freezing_id='$freeId' and decl_wt='$declWt' and code like '%$lastVal' order by code asc";
		//$qry="select id,code from  m_frozenpacking where unit='$unit' and freezing_id='$freeId' and decl_wt='$declWt'";
		//$qry="select id,code from  m_frozenpacking where unit='$unit' and freezing_id='$freeId' and decl_wt='$declWt' and code like '%$lastVal'";
		//$qry="select id,code from  m_frozenpacking where unit='$unit' and freezing_id='$freeId'";
		$qry="select distinct id,code from  m_frozenpacking where unit='$unit' and freezing_id='$freeId' and code like '%$lastVal'";
		//echo "%%".$qry;
		$resultAll	= $this->databaseConnect->getRecords($qry);

		if (sizeof($resultAll)>0) {
			foreach ($resultAll as $ra) {
				$resultArr[$ra[0]] = $ra[1];
			}
	

	}
	return $resultArr;
	}


	function insertDailyReglazing($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId)
	{
		$qry	= "insert into t_dailyfrozenreglazing(select_date,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id) values ('$selectDate',$processId,$freezingStageId,$frozenCodeId,'$MCPkgId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function insertDailyReglazingRMLot($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$rmlotid)
	{
		$qry	= "insert into t_dailyfrozenreglazing_rmlotid(select_date,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id,rm_lot_id) values ('$selectDate',$processId,$freezingStageId,$frozenCodeId,'$MCPkgId','$rmlotid')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	function addPhysicalStkdailyFrozenmainRglz($selDate, $userId,$reglazeId,$unit,$processorId,$dfId,$reglazedfrom)
	{

		$qry = "insert into t_dailyfrozenpacking_main(select_date,user_id,reglaze_main_id,flag,unit,processor_id,reglaze_frozen_id,reglazed_from) values ('$selDate','$userId','$reglazeId',4,'$unit','$processorId','$dfId','$reglazedfrom')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function addPhysicalStkdailyFrozenmainRglzRMLotId($selDate, $userId,$reglazeId,$unit,$processorId,$dfId,$reglazedfrom,$rmlotid)
	{

		$qry = "insert into t_dailyfrozenpacking_main_rmlotid(select_date,user_id,reglaze_main_id,flag,unit,processor_id,reglaze_frozen_id,reglazed_from,rm_lot_id) values ('$selDate','$userId','$reglazeId',4,'$unit','$processorId','$dfId','$reglazedfrom','$rmlotid')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function adddailyfrozenEntries($mainId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id)
	{		
	
		$qry = "insert into t_dailyfrozenpacking_entry(main_id,fish_id,processcode_id,freezing_stage_id, frozencode_id, mcpacking_id) values ('$mainId','$fish_id','$processcode_id', ' $freezing_stage', ' $frozencode_id','$mcpacking_id')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function adddailyfrozenEntriesRMLotId($mainId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id)
	{		
	
		$qry = "insert into t_dailyfrozenpacking_entry_rmlotid(main_id,fish_id,processcode_id,freezing_stage_id, frozencode_id, mcpacking_id) values ('$mainId','$fish_id','$processcode_id', ' $freezing_stage', ' $frozencode_id','$mcpacking_id')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$reglzQty)
	{

		$qryrg = " select rgzQty from t_dailyfrozenpacking_grade where id=$gradeUpid";
	//echo $qry;
		$recrg	= $this->databaseConnect->getRecord($qryrg);
		list($exireglzQty)=array($recrg[0]);
		$reglzQty=$reglzQty+$exireglzQty;

	$qry	=	"update t_dailyfrozenpacking_grade set number_mc='$thawGrdTotal',rgzQty='$reglzQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}

	function updateDailyFrozenPackingGradeRepack($gradeUpid,$thawGrdTotal,$repkdQty)
	{

		$qryrg = " select repkdQty from t_dailyfrozenpacking_grade where id=$gradeUpid";
	//echo $qry;
		$recrg	= $this->databaseConnect->getRecord($qryrg);
		list($exireglzQty)=array($recrg[0]);
		$repkdQty=$repkdQty+$exireglzQty;

	$qry	=	"update t_dailyfrozenpacking_grade set number_mc='$thawGrdTotal',repkdQty='$repkdQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}

	function updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$reglzQty)
	{

		$qryrg = " select rgzQty from t_dailyfrozenpacking_grade_rmlotid where id=$gradeUpid";
	//echo $qry;
		$recrg	= $this->databaseConnect->getRecord($qryrg);
		list($exireglzQty)=array($recrg[0]);
		$reglzQty=$reglzQty+$exireglzQty;

	$qry	=	"update t_dailyfrozenpacking_grade_rmlotid set number_mc='$thawGrdTotal',rgzQty='$reglzQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}

	function updateDailyFrozenPackingGradeRMLotRePack($gradeUpid,$thawGrdTotal,$repkdQty)
	{

		$qryrg = " select repkdQty from t_dailyfrozenpacking_grade_rmlotid where id=$gradeUpid";
	//echo $qry;
		$recrg	= $this->databaseConnect->getRecord($qryrg);
		list($exireglzQty)=array($recrg[0]);
		$repkdQty=$repkdQty+$exireglzQty;

	$qry	=	"update t_dailyfrozenpacking_grade_rmlotid set number_mc='$thawGrdTotal',repkdQty='$repkdQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}



	function updateDailyFrozenPackingGrade_old($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$reglzQty)
	{

		$qryrg = " select rgzQty from t_dailyfrozenpacking_grade where id=$gradeUpid";
	//echo $qry;
		$recrg	= $this->databaseConnect->getRecord($qryrg);
		list($exireglzQty)=array($recrg[0]);
		$reglzQty=$reglzQty+$exireglzQty;

	$qry	=	"update t_dailyfrozenpacking_grade set number_mc='$thawGrdTotal',number_loose_slab='$thawGrdLsTotal',rgzQty='$reglzQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	function updateDailyFrozenPackingGradeRMLot_old($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$reglzQty)
	{

		$qryrg = " select rgzQty from t_dailyfrozenpacking_grade_rmlotid where id=$gradeUpid";
	//echo $qry;
		$recrg	= $this->databaseConnect->getRecord($qryrg);
		list($exireglzQty)=array($recrg[0]);
		$reglzQty=$reglzQty+$exireglzQty;

	$qry	=	"update t_dailyfrozenpacking_grade_rmlotid set number_mc='$thawGrdTotal',number_loose_slab='$thawGrdLsTotal',rgzQty='$reglzQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	function updateDailyFrozenPackingMain($mainId)
	{
		$qry	=	"update t_dailyfrozenpacking_main set flag=3 where id='$mainId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;

	}
	function updateDailyFrozenPackingMainRMLot($mainId)
	{
		$qry	=	"update t_dailyfrozenpacking_main_rmlotid set flag=3 where id='$mainId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;

	}

	function insertDFPPOGradeForReglazing($dfpPOEntryId, $gradeId, $numMC, $numMCStock)
	{
		$qry	= "insert into t_dailyfrozenreglazing_grade(main_id,grade_id,number_mc_reglaze,number_mc_stock) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numMCStock')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function insertDFPPOGradeForReglazingRMLot($dfpPOEntryId, $gradeId, $numMC, $numMCStock)
	{
		$qry	= "insert into t_dailyfrozenreglazing_grade_rmlotid(main_id,grade_id,number_mc_reglaze,number_mc_stock) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numMCStock')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function getFrozQty($processId,$glazeId,$lastVal,$frozcode)
	{
	//$qry	="select distinct tqe.processcode_id,qe.frozencode_id,mf.code,mf.id from t_fznpakng_qel_entry tqe  join t_fznpakng_quick_entry qe on tqe.qe_entry_id=qe.id join m_frozenpacking mf on qe.frozencode_id=mf.id where tqe.processcode_id='$processId'";
	//$qry	="select mf.id,mf.code from t_fznpakng_qel_entry tqe  join t_fznpakng_quick_entry qe on tqe.qe_entry_id=qe.id join m_frozenpacking mf on qe.frozencode_id=mf.id where (tqe.processcode_id='$processId' and mf.glaze_id='$glazeId') and code like '%$lastVal'";
	$qry	="select distinct mf.id,mf.code from t_fznpakng_qel_entry tqe  join t_fznpakng_quick_entry qe on tqe.qe_entry_id=qe.id join m_frozenpacking mf on qe.frozencode_id=mf.id where (tqe.processcode_id='$processId' and mf.glaze_id='$glazeId')";
	$resultAll	= $this->databaseConnect->getRecords($qry);
	//echo $qry;
		if (sizeof($resultAll)>0) {
			foreach ($resultAll as $ra) {
				$resultArr[$ra[0]] = $ra[1];
			}
	

	}
	return $resultArr;
	}

	function getFrozQtygl($processId,$glazeId)
	{
	//$qry	="select distinct tqe.processcode_id,qe.frozencode_id,mf.code,mf.id from t_fznpakng_qel_entry tqe  join t_fznpakng_quick_entry qe on tqe.qe_entry_id=qe.id join m_frozenpacking mf on qe.frozencode_id=mf.id where tqe.processcode_id='$processId'";
	$qry	="select distinct mf.id,mf.code from t_fznpakng_qel_entry tqe  join t_fznpakng_quick_entry qe on tqe.qe_entry_id=qe.id join m_frozenpacking mf on qe.frozencode_id=mf.id where (tqe.processcode_id='$processId' and mf.glaze_id!='$glazeId')";
	//$qry	="select mf.id,mf.code from t_fznpakng_qel_entry tqe  join t_fznpakng_quick_entry qe on tqe.qe_entry_id=qe.id join m_frozenpacking mf on qe.frozencode_id=mf.id where (tqe.processcode_id='$processId' and mf.glaze_id='$glazeId')";
	$resultAll	= $this->databaseConnect->getRecords($qry);
	//echo $qry;
		if (sizeof($resultAll)>0) {
			foreach ($resultAll as $ra) {
				$resultArr[$ra[0]] = $ra[1];
			}
	

	}
	return $resultArr;
	}

	#Delete a Grade Rec
	function deleteFrozenAllocationGrade($id)
	{
		$qry	= " delete from t_dailyfrozenpacking_allocate where id=$id";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteFrozenAllocation($id)
	{
		$qry	= " delete from t_dailyfrozenpacking_po where id=$id";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteFrozenAllocationEntry($id)
	{
		$qry	= " delete from t_dailyfrozenpacking_allocated_entry where po_entry_id=$id";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteFrozenAllocationGradeRMLotID($id)
	{
		$qry	= " delete from t_dailyfrozenpacking_allocate_rmlotid where id=$id";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteFrozenAllocationRMLotID($id)
	{
		$qry	= " delete from t_dailyfrozenpacking_po_rmlotid where id=$id";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteFrozenAllocationEntryRMLotID($id)
	{
		$qry	= " delete from t_dailyfrozenpacking_allocated_entry_rmlotid where po_entry_id=$id";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getfrozenAllocationDetail($entryid)
	{
		//$qry = " select unit,freezing_id, glaze_id,decl_wt,id from m_frozenpacking where code=trim('$frozenCode')";
		$qry = " select id from t_dailyfrozenpacking_allocate where po_entry_id=$entryid";
		//echo $qry;
		$rec	= $this->databaseConnect->getRecords($qry);
		//return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4]);
		return(sizeof($rec));

	}
	function getfrozenAllocationDetailRMLotID($entryid)
	{
		//$qry = " select unit,freezing_id, glaze_id,decl_wt,id from m_frozenpacking where code=trim('$frozenCode')";
		$qry = " select id from t_dailyfrozenpacking_allocate_rmlotid where po_entry_id=$entryid";
		//echo $qry;
		$rec	= $this->databaseConnect->getRecords($qry);
		//return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4]);
		return(sizeof($rec));

	}
	function getfrozenDetailOfAllocation($entryid,$mainId)
	{
		
		$qry = "select tdfpa.id,tdfpa.number_mc,tdfpae.entry_id,tdfpa.grade_id,mg.code,tdfp.processcode_id,tdfp.freezing_stage_id,tdfp.frozencode_id,tdfp.mcpacking_id from t_dailyfrozenpacking_po tdfp left join t_dailyfrozenpacking_allocate tdfpa on tdfp.id=tdfpa.po_entry_id left join t_dailyfrozenpacking_allocated_entry tdfpae on tdfpae.po_entry_id=tdfp.id left join m_grade mg on mg.id=tdfpa.grade_id  where tdfpa.id='$entryid' and tdfpa.po_entry_id='$mainId'";
	//	echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);
		return $rec;

	}

	function getfrozenDetailOfAllocationRMLotID($entryid,$mainId,$rmLotID)
	{
		
		$qry = "select tdfpa.id,tdfpa.number_mc,tdfpae.entry_id,tdfpa.grade_id,mg.code,tdfp.processcode_id,tdfp.freezing_stage_id,tdfp.frozencode_id,tdfp.mcpacking_id from t_dailyfrozenpacking_po_rmlotid tdfp left join t_dailyfrozenpacking_allocate_rmlotid tdfpa on tdfp.id=tdfpa.po_entry_id left join t_dailyfrozenpacking_allocated_entry_rmlotid tdfpae on tdfpae.po_entry_id=tdfp.id left join m_grade mg on mg.id=tdfpa.grade_id  where tdfpa.id='$entryid' and tdfpa.po_entry_id='$mainId' and tdfp.rm_lot_id='$rmLotID'";
//	echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);
		return $rec;

	}
	function updateDailyFrozenAllocationGradeRMLotID($id,$numMC)
	{
		$qry	=	"update t_dailyfrozenpacking_allocate_rmlotid set number_mc='$numMC' where id='$id'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;

	}
	function updateDailyFrozenAllocationGrade($id,$numMC)
	{
		$qry	=	"update t_dailyfrozenpacking_allocate set number_mc='$numMC' where id='$id'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;

	}
}
?>