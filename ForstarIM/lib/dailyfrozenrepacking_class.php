<?php

class DailyFrozenRePacking
{
	/****************************************************************
	This class deals with all the operations relating to Daily Frozen Re-Packing
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DailyFrozenRePacking(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Check Blank Record Exist
	function checkBlankRecord()
	{
		$qry = "select id from t_dailyfrozenrepacking where flag=0 order by id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?($result[0]):false;
	}

	#Insert blank record in main table
	function addTempDataMainTable()
	{
		$qry	=	"insert into t_dailyfrozenrepacking (select_date) values(Now())";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	#Updating the Frozen Repacking table
	function updateFrozenRePackingRec($mainId, $fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $repackReasonId, $numNewInnerPack, $numLabelCard, $numNewMC, $rePackEUCode, $rePackBrand, $rePackFrozenCode, $rePackMCPacking, $selectDate)
 	{
		$qry	=	"update t_dailyfrozenrepacking  set fish_id='$fishId', processcode_id='$processCode', freezing_stage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', frozencode_id='$frozenCode', mcpacking_id='$mCPacking', repacking_reason_id='$repackReasonId', num_inner_packs_used='$numNewInnerPack', num_labels_used='$numLabelCard', num_mc_used='$numNewMC', repack_eucode_id='$rePackEUCode', repack_brand_id='$rePackBrand', repack_frozencode_id='$rePackFrozenCode', repack_mcpacking_id='$rePackMCPacking', select_date='$selectDate', flag=1 where id='$mainId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}


#Get Records For Selected Date Range
	
	function getDFRePackingRecords($fromDate,$tillDate)
	{
	
		$whr		=	"select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=1" ;
						
		$orderBy	=	"select_date asc";
		
		$qry		=	"select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, repacking_reason_id, num_inner_packs_used, num_labels_used, num_mc_used, repack_eucode_id, repack_brand_id, repack_frozencode_id, repack_mcpacking_id, select_date from t_dailyfrozenrepacking";
		
		if ($whr!="")
			$qry   .=" where ".$whr;
		if ($orderBy!="")
		 	$qry   .=" order by ".$orderBy;
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
		//return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}
	
	
	# Get RePacking Record  based on id 

	function find($dailyFrozenPackingMainId)
	{
		$qry	=	"select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, repacking_reason_id, num_inner_packs_used, num_labels_used, num_mc_used, repack_eucode_id, repack_brand_id, repack_frozencode_id, repack_mcpacking_id, select_date from t_dailyfrozenrepacking where id='$dailyFrozenPackingMainId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

//------------------------ Delete From Main Table------------------------------
# Delete a Daily Frozen RePacking Grade Rec

	function deleteFrozenRePackingGradeRec($dailyFrozenRePackingMainId)
	{
		$qry	=	" delete from t_dailyfrozenrepacking_grade where main_id=$dailyFrozenRePackingMainId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
	function deleteFrozenRePackingGradeRecRMLotID($dailyFrozenRePackingMainId)
	{
		$qry	=	" delete from t_dailyfrozenrepacking_grade_rmlotid where main_id=$dailyFrozenRePackingMainId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
	function deleteFrozenRePackingEntryRecMain($entryId)
	{

	$qry	= " delete from t_dailyfrozenpacking_entry where id=$entryId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteFrozenRePackingEntryRecMainRMLotID($entryId)
	{

	$qry	= " delete from t_dailyfrozenpacking_entry_rmlotid where id=$entryId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteFrozenRePackingGradeRecMain($gradeEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_grade where id=$gradeEntryId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;

	}
	function deleteFrozenRePackingGradeRecMainRMLotID($gradeEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_grade_rmlotid where id=$gradeEntryId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;

	}
	function deleteDailyFrozenRePackingMainRecMain($dailyFrozenRePackingMainId)
	{
		$qry	= " delete from t_dailyfrozenpacking_main where id=$dailyFrozenRePackingMainId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;

	}
	function deleteDailyFrozenRePackingMainRecMainRMLotID($dailyFrozenRePackingMainId)
	{
		$qry	= " delete from t_dailyfrozenpacking_main_rmlotid where id=$dailyFrozenRePackingMainId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;

	}
	function getProduct($prevId)
	{
		$qry="select id,fish_id, processcode_id, freezing_stage_id,frozencode_id, mcpacking_id from t_dailyfrozenpacking_entry where main_id='$prevId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1],$result[0][2],$result[0][3],$result[0][4],$result[0][5],$result[0][6],$result[0][7]):array();

	}
	function getGradeProduct($pentryId)
	{
	$qry="select id,grade_id from t_dailyfrozenpacking_grade where entry_id='$pentryId'";
	//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1]):array();
	}
	function getPrevId($mainId)
	{
		$qry="select daily_frozen_id,select_date from t_dailyfrozenpacking_main where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1]):array();
	}
	function getEntryId_old($mainId)
	{
		$qry="select id from t_dailyfrozenpacking_entry where main_id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();
	}
	function getEntryId($mainId)
	{
		$qry="select id,mcpacking_id from t_dailyfrozenpacking_entry where main_id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getOldMainId($mainId)
	{
		$qry="select repack_frozen_id from t_dailyfrozenpacking_main where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();
	}
	function getOldMainIdRMLotID($mainId)
	{
		$qry="select repack_frozen_id from t_dailyfrozenpacking_main_rmlotid where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();
	}
	
	function getEntryIdRMLotID($mainId)
	{
		$qry="select id,mcpacking_id from t_dailyfrozenpacking_entry_rmlotid where main_id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getGradeEntryId_old($entryId)
	{
		$qry="select id from t_dailyfrozenpacking_grade where entry_id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();
	}
	function getGradeEntryId($entryId)
	{
		$qry="select id,grade_id,number_mc,number_loose_slab,repkdQty from t_dailyfrozenpacking_grade where entry_id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getGradeEntryIdRMLotID($entryId)
	{
		$qry="select id,grade_id,number_mc,number_loose_slab,repkdQty from t_dailyfrozenpacking_grade_rmlotid where entry_id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
# Delete a Daily Frozen Packing Entry Rec
/*
	function deletePackingEntryRec($dailyFrozenPackingEntryId)
	{
		$qry	=	" delete from t_dailyfrozenpacking_entry where id=$dailyFrozenPackingEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;
	}*/

/*function checkRecordsExist($dailyFrozenPackingMainId){

		$qry	=	"select b.main_id from t_dailyfrozenpacking_main a, t_dailyfrozenpacking_entry b  where  a.id=b.main_id and b.main_id='$dailyFrozenPackingMainId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
}*/


	# Delete a Daily Frozen RePacking Main Rec

	function deleteDailyFrozenRePackingMainRec($dailyFrozenRePackingMainId)
	{
		$qry	=	" delete from t_dailyfrozenrepacking where id=$dailyFrozenRePackingMainId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
	function deleteDailyFrozenRePackingMainRecRMLotID($dailyFrozenRePackingMainId)
	{
		$qry	=	" delete from t_dailyfrozenrepacking_rmlotid where id=$dailyFrozenRePackingMainId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
//------------------------ Delete End------------------------------

#IFRAME SECTION
###############################################################################	
	function addFrozenRePackingGrade($mainId, $gradeId, $numMCRePack, $numLooseSlabRePack, $totalNumMC, $totalNumLooseSlab)
	{
		$qry	=	" insert into t_dailyfrozenrepacking_grade (main_id, grade_id, number_mc_repack, number_loose_slab_repack, number_mc_stock,  number_loose_slab_stock) values($mainId, $gradeId, $numMCRePack, $numLooseSlabRePack, $totalNumMC, $totalNumLooseSlab)";
	
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}	

	#update Frozen Re Packing Grades
	function updateFrozenRePackingGrade($gradeEntryId, $gradeId, $numMCRePack, $numLooseSlabRePack, $totalNumMC, $totalNumLooseSlab)
	{
		$qry	=	" update t_dailyfrozenrepacking_grade set grade_id='$gradeId', number_mc_repack='$numMCRePack', number_loose_slab_repack='$numLooseSlabRePack', number_mc_stock='$totalNumMC',  number_loose_slab_stock='$totalNumLooseSlab' where id='$gradeEntryId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}	

#Delete a Grade Rec
	function deleteRePackingGradeRec($gradeEntryId)
	{
		$qry	=	" delete from t_dailyfrozenrepacking_grade where id=$gradeEntryId";
	
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;
	}
	#Grade selection for Frozen Grades
	function fetchFrozenGradeRecords($codeId, $mainId)
 	{
		$qry	=	"select a.grade_id, c.code, b.id, b.main_id, b.number_mc_repack, b.number_loose_slab_repack, b.number_mc_stock, b.number_loose_slab_stock from m_processcode2grade a left join t_dailyfrozenrepacking_grade b on a.grade_id=b.grade_id and b.main_id='$mainId', m_grade c where a.grade_id = c.id and a.processcode_id='$codeId' and a.unit_select='f' order by c.code asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Get total Stock of Num of MC and Num of Loose Slab
	function getTotalStock($processcodeId, $gradeId)
	{
		$qry = "select b.number_mc, b.number_loose_slab from t_dailyfrozenpacking_entry a, t_dailyfrozenpacking_grade b where a.id=b.entry_id and b.grade_id='$gradeId' and a.processcode_id='$processcodeId'";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		
		if (sizeof($result)>0) {
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach ($result as $rec) {
				$numMc	= $rec[0];
				$numLooseSlab = $rec[1];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}		
		}	
		return array($totalNumMC, $totalNumLooseSlab);
	}

	
	function getDFPForDateRange($fromDate, $tillDate, $offset, $limit)
	{
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is not null and tdfpg.number_mc>0";
		//$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,a.flag";
		$groupBy2	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,a.flag, a.rm_lot_id ";


		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$limit 		= " $offset, $limit";		
		$qry1 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),flag,a.processor_id,a.unit,sum(tdfpg.number_loose_slab) as numLSs,repack_main_id as repackmainid,'0' as rmlotid, '0' as rmlotName  
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";

		$qry2 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),flag,a.processor_id,a.unit,sum(tdfpg.number_loose_slab) as numLSs,repack_main_id as repackmainid ,a.rm_lot_id as rmlotid ,concat(tmg.alpha_character,tmg.rm_lotid) as rmlotName  
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
				left join t_manage_rm_lotid tmg on tmg.id=a.rm_lot_id 
			";

		if ($whr!="") $qry1 .= " where ".$whr;  $qry2 .= " where ".$whr;
		if ($groupBy) $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy2;
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		$qry= $qry1." union all ".$qry2;
		if ($limit) $qry .= " limit ".$limit;
		//echo "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
	
		return $result;

	}












	
	/*function getDFPForDateRange($fromDate, $tillDate, $offset, $limit)
	{
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is not null and tdfpg.number_mc>0";
		//$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,a.flag";
		$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$limit 		= " $offset, $limit";		
		$qry 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),flag,a.processor_id,a.unit,sum(tdfpg.number_loose_slab) as numLSs,repack_main_id as repackmainid  
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit) $qry .= " limit ".$limit;

		echo "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
	
		return $result;

	}*/

	function getDFPReForDateRange($fromDate, $tillDate)
	{
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is not null and tdfpg.number_mc>0";
		//$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,a.flag";
		$groupBy2	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,a.flag, a.rm_lot_id ";


		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		//$limit 		= " $offset, $limit";		
		$qry1 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),flag,a.processor_id,a.unit,sum(tdfpg.number_loose_slab) as numLSs,repack_main_id as repackmainid,'0' as rmlotid, '0' as rmlotName  
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";

		$qry2 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),flag,a.processor_id,a.unit,sum(tdfpg.number_loose_slab) as numLSs,repack_main_id as repackmainid ,a.rm_lot_id as rmlotid ,concat(tmg.alpha_character,tmg.rm_lotid) as rmlotName  
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
				left join t_manage_rm_lotid tmg on tmg.id=a.rm_lot_id 
			";

		if ($whr!="") $qry1 .= " where ".$whr;  $qry2 .= " where ".$whr;
		if ($groupBy) $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy2;
		//if ($orderBy!="") $qry .= " order by ".$orderBy;
		
		$qry= $qry1." union all ".$qry2;
		//if ($limit) $qry .= " limit ".$limit;

		//echo "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;

	}



/*function getDFPReForDateRange($fromDate, $tillDate)
	{
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is not null and tdfpg.number_mc>0";
		//$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,a.flag";
		$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		//$limit 		= " $offset, $limit";		
		$qry 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),flag,a.processor_id,a.unit,sum(tdfpg.number_loose_slab) as numLSs    
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//if ($limit) $qry .= " limit ".$limit;

		//echo "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;

	}*/




	function getphysicalstkentrymccount()
	{

		$qry="select null,psp.date,pspe.fish_id,pspe.processcode_id,pspe.freezing_stage,pspe.frozencode_id,mpc.code as processCode,mfs.rm_stage as freezingStage, mfp.code as frznPkgCode,null,sum(pspe.num_mc) sum,pspe.mcpacking_id,mcp.code as mcPkgCode,((sum(pspe.num_mc)*mfp.filled_wt*mcp.number_packs)+(sum(pspe.num_ls)*mfp.filled_wt)) as pkdQty,sum(pspe.num_mc) sumnummc,((sum(pspe.num_mc)*mfp.decl_wt*mcp.number_packs)+(sum(pspe.num_ls)*mfp.decl_wt)) as frozenQty,((sum(pspe.num_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(pspe.num_ls)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty 
		from m_physical_stk_packing psp join m_physical_stk_packing_entry pspe on psp.id=pspe.main_id 
		left join m_processcode mpc on mpc.id=pspe.processcode_id 
		left join m_freezingstage mfs on mfs.id=pspe.freezing_stage 
		left join m_frozenpacking mfp on mfp.id=pspe.frozencode_id 
		left join m_mcpacking mcp on pspe.mcpacking_id=mcp.id 
		where psp.date=(select max(date) maxdate from m_physical_stk_packing) 
		group by pspe.processcode_id,pspe.freezing_stage,pspe.frozencode_id,pspe.mcpacking_id"; 
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}



	function insertDFPPOGradeForRepacking($dfpPOEntryId, $gradeId, $numMC, $numMCStock)
	{
		$qry	= "insert into t_dailyfrozenrepacking_grade(main_id,grade_id,number_mc_repack,number_mc_stock) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numMCStock')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function insertDFPPOGradeForRepackingRMLot($dfpPOEntryId, $gradeId, $numMC, $numMCStock)
	{
		$qry	= "insert into t_dailyfrozenrepacking_grade_rmlotid(main_id,grade_id,number_mc_repack,number_mc_stock) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numMCStock')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function insertDailyRepacking($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId)
	{
		$qry	= "insert into t_dailyfrozenrepacking(select_date,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id) values ('$selectDate',$processId,$freezingStageId,$frozenCodeId,$MCPkgId)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function insertDailyRepackingRMLotID($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$rmlotid)
	{
		$qry	= "insert into t_dailyfrozenrepacking_rmlotid(select_date,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id,rm_lot_id) values ('$selectDate',$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$rmlotid)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function getRepackGradeQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate)
	{
		$qry="select sum(number_mc_repack) from t_dailyfrozenrepacking trep left join t_dailyfrozenrepacking_grade tdg on tdg.main_id=trep.id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
	
	}

	function addPhysicalStkdailyFrozenmain($selDate, $userId,$repackId,$unit,$processorId,$dfId,$repackedfrom)
	{

		$qry = "insert into t_dailyfrozenpacking_main(select_date,user_id,repack_main_id,flag,unit,processor_id,repack_frozen_id,repacked_from) values ('$selDate','$userId','$repackId',1,'$unit','$processorId','$dfId','$repackedfrom')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function addPhysicalStkdailyFrozenmainRMLotID($selDate, $userId,$repackId,$unit,$processorId,$dfId,$repackedfrom, $rmlotid)
	{

		$qry = "insert into t_dailyfrozenpacking_main_rmlotid(select_date,user_id,repack_main_id,flag,unit,processor_id,repack_frozen_id,repacked_from,rm_lot_id) values ('$selDate','$userId','$repackId',1,'$unit','$processorId','$dfId','$repackedfrom','$rmlotid')";
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
	function adddailyfrozenEntriesRMLotID($mainId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id)
	{		
		$qry = "insert into t_dailyfrozenpacking_entry_rmlotid(main_id,fish_id,processcode_id,freezing_stage_id, frozencode_id, mcpacking_id) values ('$mainId','$fish_id','$processcode_id', '$freezing_stage', ' $frozencode_id','$mcpacking_id')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

	function adddailyFrozenGradeEntries($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used)
	{

		$qry = "insert into t_dailyfrozenpacking_grade(entry_id,grade_id,number_mc,number_loose_slab) values ('$dailyentrymainId','$grade_id','$num_mc_used','$num_ls_used')";
	//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function adddailyFrozenGradeEntriesRMLot($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used)
	{

		$qry = "insert into t_dailyfrozenpacking_grade_rmlotid(entry_id,grade_id,number_mc,number_loose_slab) values ('$dailyentrymainId','$grade_id','$num_mc_used','$num_ls_used')";
	//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		//$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=1" ;
		$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=0" ;
		$groupBy	= "td.processcode_id, td.freezing_stage_id, td.frozencode_id, td.mcpacking_id,td.select_date,tdg.grade_id";

		$orderBy	= "select_date asc";
		$limit		= "$offset, $limit";
		
		//$qry		= "select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date from t_dailythawing";

		$qry		= "select td.id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date,sum(number_mc_thawing) as thawStock,tdg.grade_id,tdg.id from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;
		if ($limit!="") 	$qry   .=" limit ".$limit;

		//echo "<br>$qry<br>";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getThaGradeQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$tillDate,$sGradeId)
	{
	//$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";
	$qry="select sum(number_mc_thawing),sum(number_loose_slab_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and select_date<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";


	
		//echo "*************".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
	
	}
	function getThaGradeQtyLot($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$tillDate,$sGradeId,$rmlotid)
	{
	//$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";
	$qry="select sum(number_mc_thawing),sum(number_loose_slab_thawing) from t_dailythawing_rmlotid td left join t_dailythawing_grade_rmlotid tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and select_date<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId'  and rm_lot_id='$rmlotid'";


	
		//echo "*************".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
	
	}
	function getAllocGradeQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$tillDate,$sGradeId)
	{
	//$qry="select sum(number_mc) from t_dailyfrozenpacking_allocated_entry tdae left join t_dailyfrozenpacking_allocate tda on tdae.id=tda.po_entry_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and tdae.created_on>='$selectDate' and tdae.created_on>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";

	$qry="select sum(number_mc) from t_dailyfrozenpacking_allocated_entry tdae left join t_dailyfrozenpacking_allocate tda on tdae.id=tda.po_entry_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and tdae.created_on>='$selectDate' and tdae.created_on<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";
		//echo "&&&&&&&&&&&&&&&&&&".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1]):array();	
	
	}
	function checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$selectDate,$tillDate,$gradeId)
	{
		$qry = "select tdfpm.id,tdfpe.id,tdfpg.id from 
			t_dailyfrozenpacking_main tdfpm join
			t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.fish_id='$fish_id' and tdfpe.processcode_id='$processId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$MCPkgId' and tdfpg.grade_id='$gradeId' and tdfpm.select_date>='".$selectDate."' and tdfpm.select_date<='".$tillDate."'";	
		//echo "Grade===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;

	}
	function checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$selectDate,$tillDate,$gradeId,$hidrmLotID)
	{
		$qry = "select tdfpm.id,tdfpe.id,tdfpg.id from 
			t_dailyfrozenpacking_main_rmlotid tdfpm join
			t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.fish_id='$fish_id' and tdfpe.processcode_id='$processId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$MCPkgId' and tdfpg.grade_id='$gradeId' and tdfpm.select_date>='".$selectDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.rm_lot_id='$hidrmLotID'";	
	//echo "Grade===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;

	}
	
	function updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$repkdQty)
	{
		$qryrp = " select repkdQty from t_dailyfrozenpacking_grade where id=$gradeUpid";
		//echo $qry;
		$recrp	= $this->databaseConnect->getRecord($qryrp);
		list($exirepQty)=array($recrp[0]);
		$repkdQty=$repkdQty+$exirepQty;
		$qry	=	" update t_dailyfrozenpacking_grade set number_mc='$thawGrdTotal',repkdQty='$repkdQty' where id='$gradeUpid'";
		//echo $qry;
		//die();
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	function updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$repkdQty)
	{
		//echo $thawGrdTotal;
		$qryrp = " select repkdQty from t_dailyfrozenpacking_grade_rmlotid where id=$gradeUpid";
		//echo $qry;
		$recrp	= $this->databaseConnect->getRecord($qryrp);
		list($exirepQty)=array($recrp[0]);
		$repkdQty=$repkdQty+$exirepQty;
		
		$qry	=	" update t_dailyfrozenpacking_grade_rmlotid set number_mc='$thawGrdTotal',repkdQty='$repkdQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}

/*	function updateDailyFrozenPackingGrade_old($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$repkdQty)
	{
		$qryrp = " select repkdQty from t_dailyfrozenpacking_grade where id=$gradeUpid";
		//echo $qry;
		$recrp	= $this->databaseConnect->getRecord($qryrp);
		list($exirepQty)=array($recrp[0]);
		$repkdQty=$repkdQty+$exirepQty;
		$qry	=	" update t_dailyfrozenpacking_grade set number_mc='$thawGrdTotal',number_loose_slab='$thawGrdLsTotal',repkdQty='$repkdQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	function updateDailyFrozenPackingGradeRMLot_old($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$repkdQty)
	{
		//echo $thawGrdTotal;
		$qryrp = " select repkdQty from t_dailyfrozenpacking_grade_rmlotid where id=$gradeUpid";
		//echo $qry;
		$recrp	= $this->databaseConnect->getRecord($qryrp);
		list($exirepQty)=array($recrp[0]);
		$repkdQty=$repkdQty+$exirepQty;
		
		$qry	=	" update t_dailyfrozenpacking_grade_rmlotid set number_mc='$thawGrdTotal',number_loose_slab='$thawGrdLsTotal',repkdQty='$repkdQty' where id='$gradeUpid'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
*/
	function getThaQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$tillDate)
	{
		$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and select_date<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId' ";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	

	}

	function getAllocQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$tillDate)
	{
		/*$qry="select sum(number_mc) from t_dailyfrozenpacking_allocated_entry te left join t_dailyfrozenpacking_allocate tda on te.id=tda.allocate_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and te.created_on>='$selectDate' and te.created_on<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId'";*/

		$qry="select sum(tda.number_mc),sum(number_loose_slab) from t_purchaseorder_rm_entry te left join t_dailyfrozenpacking_allocate tda on te.id=tda.po_rm_id where processcode_id='$processId' and freezingstage_id='$freezingStage' and frozencode_id='$frozenCode' and tda.created_on>='$selectDate' and tda.created_on<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId'";
			//echo $qry;


		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	

	}

	function getGradeAllocQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$tillDate,$gradeId)
	{
		/*$qry="select sum(number_mc) from t_dailyfrozenpacking_allocated_entry te left join t_dailyfrozenpacking_allocate tda on te.id=tda.allocate_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and te.created_on>='$selectDate' and te.created_on<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId'";*/

		$qry="select sum(tda.number_mc),sum(number_loose_slab) from t_purchaseorder_rm_entry te left join t_dailyfrozenpacking_allocate tda on te.id=tda.po_rm_id where te.processcode_id='$processId' and te.freezingstage_id='$freezingStage' and te.frozencode_id='$frozenCode' and tda.created_on>='$selectDate' and tda.created_on<='$tillDate' and te.mcpacking_id='$stkAllocateMCPkgId' and tda.grade_id='$gradeId'";
			//echo $qry;


		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1]):array();	

	}
	function getGradeAllocQtyRmLot($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$tillDate,$gradeId,$rmlotid)
	{
		/*$qry="select sum(number_mc) from t_dailyfrozenpacking_allocated_entry te left join t_dailyfrozenpacking_allocate tda on te.id=tda.allocate_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and te.created_on>='$selectDate' and te.created_on<='$tillDate' and mcpacking_id='$stkAllocateMCPkgId'";*/

		$qry="select sum(tda.number_mc),sum(number_loose_slab) from t_purchaseorder_rm_entry te left join t_dailyfrozenpacking_allocate_rmlotid tda on te.id=tda.po_rm_id left join t_dailyfrozenpacking_po_rmlotid dfpor on dfpor.id= tda.po_entry_id where te.processcode_id='$processId' and te.freezingstage_id='$freezingStage' and te.frozencode_id='$frozenCode' and tda.created_on>='$selectDate' and tda.created_on<='$tillDate' and te.mcpacking_id='$stkAllocateMCPkgId' and tda.grade_id='$gradeId' and dfpor.rm_lot_id='$rmlotid'";
		//echo $qry;


		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0],$result[0][1]):array();	

	}
	function updateDailyFrozenPackingMain($mainId)
	{
		$qry	=	"update t_dailyfrozenpacking_main set flag=2 where id='$mainId'";
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
		$qry	=	"update t_dailyfrozenpacking_main_rmlotid set flag=2 where id='$mainId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;

	}
	function updateDailyFrozenPackingEntry($mCPacking,$repselFrozenCode,$entryId)
	{
		
		$qry = "update t_dailyfrozenpacking_entry  set  mcpacking_id='$mCPacking',frozencode_id='$repselFrozenCode' where id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function updateDailyFrozenPackingGradeEdit($numMC,$numLS,$gradeEntryId,$gradeId)
	{
	//$qry	= " update t_dailyfrozenpacking_grade set number_mc='$numMC',number_loose_slab='$numLS' where id='$gradeEntryId'";
	$qry="update t_dailyfrozenpacking_grade set number_mc='$numMC',number_loose_slab='$numLS' where entry_id='$gradeEntryId' and grade_id='$gradeId'";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();
	return $result;
	}

		function getRepEntryId($mainId)
		{
		$qry="select id from t_dailyfrozenrepacking where main_id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();
		}


function updateDailyFrozenRepackingEntry($mCPacking,$repselFrozenCode,$entryId)
{
		
		$qry = "update t_dailyfrozenrepacking  set  mcpacking_id='$mCPacking',frozencode_id='$repselFrozenCode' where id='$entryId'";
		//$qry = "update t_dailyfrozenreglazing  set frozencode_id='$frozencodeid' where id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
}

function updateDailyFrozenRepackingGrade($numMC,$numLS,$entryId,$gradeEntryId)
{
		
		$qry = "update t_dailyfrozenrepacking_grade  set number_mc_repack='$numMC',number_loose_slab_repack='$numLS' where main_id='$entryId' and grade_id='$gradeEntryId'";
		//$qry = "update t_dailyfrozenreglazing  set frozencode_id='$frozencodeid' where id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
}


####code created on 11-10-2014 for deleting data in daily frozen table on deleting reglazing data.
	function deleteFrozenRePackingGradeWithEntryId($entryId)
	{
		$qry	=	" delete from t_dailyfrozenpacking_grade where entry_id='$entryId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;
	}

	function deleteFrozenRePackingGradeRMLotIDWithEntryId($entryId)
	{
		$qry	=	" delete from t_dailyfrozenpacking_grade_rmlotid where entry_id='$entryId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;
	}
	#update Frozen Re Packing Grades
	function updateFrozenPackingGradeReEnter($id, $gradeId, $numMC, $numLS , $repckNew)
	{
		$qry	=	" update t_dailyfrozenpacking_grade set number_mc='$numMC', number_loose_slab='$numLS',repkdQty='$repckNew' where id='$id' and grade_id='$gradeId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	#update Frozen Re Packing Grades
	function updateFrozenPackingGradeReEnterRMLotID($id, $gradeId, $numMC, $numLS , $repckNew)
	{
		$qry	=	" update t_dailyfrozenpacking_grade_rmlotid set number_mc='$numMC', number_loose_slab='$numLS',repkdQty='$repckNew' where id='$id' and grade_id='$gradeId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			$this->databaseConnect->rollback();
		}
		return $result;	
	}
	function getMCNumPack($id)
	{
		$qry="select number_packs from m_mcpacking where id='$id'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();
	
	}

}

?>