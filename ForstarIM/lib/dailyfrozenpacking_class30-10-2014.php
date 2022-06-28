<?php
class DailyFrozenPacking
{
	/****************************************************************
	This class deals with all the operations relating to Daily Frozen Packing
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DailyFrozenPacking(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Check Blank Record Exist
	function checkBlankRecord($userId)
	{
		$qry = "select a.id, b.id from t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id where a.user_id='$userId' and a.unit is null and a.processor_id is null and physical_stock_main_id is null order by a.id desc";
		//echo "Chk Blank=<br>$qry";
		$result	= $this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array( $result[0], $result[1]):false;
	}
	
	function checkBlankRecordRmlotid($userId)
	{
		$qry = "select a.id, b.id from t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry b on a.id=b.main_id where a.user_id='$userId' and a.unit is null and a.processor_id is null and physical_stock_main_id is null order by a.id desc";
		//echo "Chk Blank=<br>$qry";
		$result	= $this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array( $result[0], $result[1]):false;
	}
	
	
	
	/*#Indert blank record
	function addTempDataMainTable($userId,$rm_lot_id,$available_qty)
	{
		$qry	= "insert into t_dailyfrozenpacking_main (select_date, user_id,rm_lot_id,available_qty) 
				   values(Now(), $userId,$rm_lot_id,$available_qty)";
		//echo $qry;die;
		
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}*/
	
	#Indert blank record
	function addTempDataMainTable($userId)
	{
		$qry	= "insert into t_dailyfrozenpacking_main (select_date, user_id) 
				   values(Now(), $userId)";
		//echo $qry;die;
		
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	#Indert blank record
	function addTempDataMainTableRmlotid($userId,$rm_lot_id)
	{
		$qry	= "insert into t_dailyfrozenpacking_main_rmlotid (select_date, user_id,rm_lot_id) 
				   values(Now(), '$userId', '$rm_lot_id')";
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

	#Insert blank record
	function addTempDataEntryTableRmlotid($mainId)
	{
		$qry	= "insert into t_dailyfrozenpacking_entry_rmlotid (main_id) values('$mainId')";
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

	function updateDailyFrozenPackingEntryRmlotid($fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $exportLotId,$entryId, $lotId, $selQuality, $allocateMode, $brandFrom, $customer)
	{
		if (!$allocateMode) {
			$updateEntry = ",fish_id='".$fishId."', processcode_id='".$processCode."', frozencode_id='".$frozenCode."'";
		}
		
		$qry = "update t_dailyfrozenpacking_entry_rmlotid  set  freezing_stage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', mcpacking_id='$mCPacking', export_lot_id='$exportLotId', frozen_lot_id='$lotId', quality_id='$selQuality', brand_from='$brandFrom', customer_id='$customer' $updateEntry where id='$entryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	/*#update Packing main Table
	function updatePackingMainRec($selectDate, $unit, $processorId, $mainId,$rm_lot_id,$available_qty)
	{
		$qry	= "update t_dailyfrozenpacking_main  set select_date='$selectDate', unit='$unit', processor_id='$processorId',
				   rm_lot_id='$rm_lot_id',available_qty='$available_qty' where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}*/
	#update Packing main Table
	function updatePackingMainRec($selectDate,$company,$unit, $processorId, $mainId)
	{
		$qry	= "update t_dailyfrozenpacking_main  set select_date='$selectDate',company='$company', unit='$unit', processor_id='$processorId' where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function updatePackingMainRecRmlotid($selectDate,$company,$unit, $processorId, $mainId)
	{
		$qry	= "update t_dailyfrozenpacking_main_rmlotid  set select_date='$selectDate',company='$company', unit='$unit', processor_id='$processorId' where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updatelotidInPackingMain($newRMCompanyId,$newRMUnitId,$rmlotid, $mainId)
	{
		$qry	= "update t_dailyfrozenpacking_main_rmlotid  set rm_lot_id='$rm_lot_id',company='$newRMCompanyId',unit='$newRMUnitId' where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
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
	
	#Get Records For Selected Date Range
	function getPagingDFPRecs($fromDate, $tillDate, $offset, $limit)
	{

		//$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is null and reglaze_main_id is null";

		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,new_lot_Id";
		//$groupBy=" b.processcode_id,b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$orderBy	= " processCode asc, freezingStage asc, frznPkgCode asc";
		$limit 		= " $offset, $limit";		
		$qry1 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, 
				mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, 
				(select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id 
				group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,
				((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty,
				sum(tdfpg.number_mc) as numMcs, 
				((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,
				((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),repack_main_id,sum(tdfpg.number_loose_slab) as numLS,  
			    '0' as new_lot_IdNm,'0' as new_lot_Id,b.id,'0' as unit, '0' as company from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id 
				";
		$qry2 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, 
				mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, 
				(select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id 
				group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,
				((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty,
				sum(tdfpg.number_mc) as numMcs, 
				((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,
				((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),repack_main_id,sum(tdfpg.number_loose_slab) as numLS,  
			    concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,a.rm_lot_id as new_lot_Id,b.id,mg.unit_id as unit,mg.company_id as company  from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id 
				left join t_manage_rm_lotid mg on mg.id = a.rm_lot_id 
			";	
		
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		if ($groupBy) $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy;
		//
		//if ($limit) $qry .= " limit ".$limit;
		$qry=$qry1." union all ".$qry2; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;		
		//echo  "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		$fa=$result;
		$getphysicalstkentrymc=$this->getphysicalstkentrymccount();
		$dailyfrozenphysiclastkentry= array_merge($fa,$getphysicalstkentrymc);
		$found = array();
		foreach ($dailyfrozenphysiclastkentry as $i=>$row) {
		$check = "$row[3],$row[4],$row[5],$row[11]";
   
		if (@$found[$check]++) {
        unset($dailyfrozenphysiclastkentry[$i]);
		}
}	  
		return $result;
	}
	
	/*function getPagingDFPRecs($fromDate, $tillDate, $offset, $limit)
	{

		//$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is null and reglaze_main_id is null";

		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$limit 		= " $offset, $limit";		
		$qry 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, 
				mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, 
				(select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id 
				group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,
				((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty,
				sum(tdfpg.number_mc) as numMcs, 
				((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,
				((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),repack_main_id,sum(tdfpg.number_loose_slab) as numLS,  
			    concat(mg.alpha_character,mg.rm_lotid) as new_lot_Id,a.available_qty from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id 
				left join t_manage_rm_lotid mg on mg.id = a.rm_lot_id 
			";
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit) $qry .= " limit ".$limit;
			 
		//echo  "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		$fa=$result;
		$getphysicalstkentrymc=$this->getphysicalstkentrymccount();
		$dailyfrozenphysiclastkentry= array_merge($fa,$getphysicalstkentrymc);
		$found = array();
		foreach ($dailyfrozenphysiclastkentry as $i=>$row) {
		$check = "$row[3],$row[4],$row[5],$row[11]";
   
		if (@$found[$check]++) {
        unset($dailyfrozenphysiclastkentry[$i]);
		}
}	  
		return $result;
	}*/

	function getMaxDate()
	{
		
	$qry = "select max(date) maxdate from m_physical_stk_packing;";
	//echo $qry;
	return $this->databaseConnect->getRecord($qry);
	}


	function getDFPForDateRange($fromDate, $tillDate)
	{	

		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is null and reglaze_main_id is null";

		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id,new_lot_Id";
		//$groupBy=" b.processcode_id,b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$orderBy	= " processCode asc, freezingStage asc, frznPkgCode asc";
		$limit 		= " $offset, $limit";		
		$qry1 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, 
				mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, 
				(select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id 
				group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,
				((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty,
				sum(tdfpg.number_mc) as numMcs, 
				((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,
				((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),repack_main_id,sum(tdfpg.number_loose_slab) as numLS,  
			    '0' as new_lot_IdNm,'0' as new_lot_Id,b.id from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id 
				";
		$qry2 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, 
				mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, 
				(select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id 
				group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,
				((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty,
				sum(tdfpg.number_mc) as numMcs, 
				((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,
				((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id,sum(a.physical_stock_main_id),repack_main_id,sum(tdfpg.number_loose_slab) as numLS,  
			    concat(mg.alpha_character,mg.rm_lotid) as new_lot_IdNm,a.rm_lot_id as new_lot_Id,b.id from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id 
				left join t_manage_rm_lotid mg on mg.id = a.rm_lot_id 
			";	
		
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		if ($groupBy) $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy;
		//
		//if ($limit) $qry .= " limit ".$limit;
		$qry=$qry1." union all ".$qry2; 
		if ($orderBy!="") $qry .= " order by ".$orderBy;
	//echo  $qry ;


	
		//$whr		= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'  and a.unit is not null" ;
		/*$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and repack_main_id is null";

		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$orderBy	= "mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		
		$qry 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm 
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
			";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;*/
		//echo $qry;
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
	
	# Delete a Daily Frozen Packing Grade Rec
	function deleteFrozenPackingGradeRecRmlotid($dailyFrozenPackingEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_grade_rmlotid where entry_id=$dailyFrozenPackingEntryId";

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

	# Delete a Daily Frozen Packing Entry Rec
	function deletePackingEntryRecRmlotid($dailyFrozenPackingEntryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_entry_rmlotid where id=$dailyFrozenPackingEntryId";

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

	#Checking Record Exisitng
	function checkRecordsExistRmlotId($dailyFrozenPackingMainId)
	{
		$qry	=	"select b.main_id from t_dailyfrozenpacking_main_rmlotid a, t_dailyfrozenpacking_entry_rmlotid b  where  a.id=b.main_id and b.main_id='$dailyFrozenPackingMainId' ";
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
	# Delete a Daily Frozen Packing Main Rec
	function deleteDailyFrozenPackingMainRecRmLotId($dailyFrozenPackingMainId)
	{
		$qry	= " delete from t_dailyfrozenpacking_main_rmlotid where id=$dailyFrozenPackingMainId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	//------------------------ Delete End------------------------------

//IFRAME SECTION
###############################################################################
	#Add Grade Records
	function addFrozenPackingGrade($entryId, $gradeId, $numMC, $numLooseSlab, $LSToMCConversionType)
	{
		$qry	= " insert into t_dailyfrozenpacking_grade (entry_id, grade_id, number_mc, number_loose_slab, convert_type) values($entryId, $gradeId, $numMC, $numLooseSlab, '$LSToMCConversionType')";				
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	

	function addFrozenPackingGradeRmlotid($entryId, $gradeId, $numMC, $numLooseSlab, $LSToMCConversionType)
	{
		$qry	= " insert into t_dailyfrozenpacking_grade_rmlotid (entry_id, grade_id, number_mc, number_loose_slab, convert_type) values($entryId, $gradeId, $numMC, $numLooseSlab, '$LSToMCConversionType')";				
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	
	
	#update Frozen Packing Grades
	function updateFrozenPackingGrade($gradeEntryId, $gradeId, $numMC, $numLooseSlab, $LSToMCConversionType)
	{
		$qry	= " update t_dailyfrozenpacking_grade set grade_id='$gradeId',number_mc='$numMC',number_loose_slab='$numLooseSlab', convert_type='$LSToMCConversionType' where id='$gradeEntryId'";

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
	
	#Grade selection for Frozen Grades
 	function fetchFrozenGradeRecordsRmLotId($codeId,$entryId)
	{
 		$qry	= "select a.grade_id, c.code, b.id, b.entry_id, b.number_mc, b.number_loose_slab from m_processcode2grade a left join t_dailyfrozenpacking_grade_rmlotid b on a.grade_id=b.grade_id and b.entry_id='$entryId', m_grade c where a.grade_id = c.id and a.processcode_id='$codeId' and a.unit_select='f' order by c.code asc";
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
		//echo $qry;
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4],$rec[5],$rec[6],$rec[7],$rec[8], $rec[9]);
	}
	
	
	
	# Check Process Code Has Grade
	function processCodeHasGrade($processCodeId, $gradeId)
	{
		$qry = " select a.grade_id from m_processcode2grade a where a.processcode_id='$processCodeId' and a.grade_id='$gradeId' and a.unit_select='f'";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Update daily Frozen Packing Entry Table Rec
	function addDailyFrozenPackingEntry($mainId, $hidFishId, $hidProcesscodeId, $qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom, $selQuickEntryList)
	{
		$qry	= "insert into t_dailyfrozenpacking_entry (main_id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, brand_from, quick_entry_list_id) values('$mainId', '$hidFishId', '$hidProcesscodeId', '$qeFreezingStageId', '$qeEUCodeId', '$qeBrandId', '$qeFrozenCodeId', '$qeMCPackingId', '$qeFrozenLotId', '$qeExportLotId', '$qeQualityId', '$qeCustomerId', '$qeBrandFrom', '$selQuickEntryList')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function addDailyFrozenPackingEntryRmlotid($mainId, $hidFishId, $hidProcesscodeId, $qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom, $selQuickEntryList)
	{
		$qry	= "insert into t_dailyfrozenpacking_entry_rmlotid (main_id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, brand_from, quick_entry_list_id) values('$mainId', '$hidFishId', '$hidProcesscodeId', '$qeFreezingStageId', '$qeEUCodeId', '$qeBrandId', '$qeFrozenCodeId', '$qeMCPackingId', '$qeFrozenLotId', '$qeExportLotId', '$qeQualityId', '$qeCustomerId', '$qeBrandFrom', '$selQuickEntryList')";
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
	
	function getBalnkDFPERecRmlotid($mainId)
	{
		$qry = "select b.id from t_dailyfrozenpacking_entry_rmlotid b  where b.fish_id is null and b.processcode_id is null and b.main_id='$mainId'";
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
	function getProductionRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id,tdfpm.physical_stock_main_id,'0' as rm_lot_id from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id  where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."'";
			
	//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Production Details
	function getProductionRecsRmLotId($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId,$rmLotStatus)
	{
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id,tdfpm.physical_stock_main_id,tdfpm.rm_lot_id as rm_lot_id from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id  where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.rm_lot_id='$rmLotStatus'";
			
	//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	# get Grade Recs
	function getProductionGradeRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		$qry = "select tdfpg.grade_id, mg.code  from 
			t_dailyfrozenpacking_main tdfpm join
			t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_grade mg on tdfpg.grade_id=mg.id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' group by tdfpg.grade_id order by mg.code asc ";	
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
	
	# Get Slab Details
	function getSlabRmLotId($dFrznPkgEntryId, $sGradeId,$rmLotStatus)
	{
		$qry = " select a.id, a.number_mc, a.number_loose_slab from t_dailyfrozenpacking_grade_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.entry_id=b.id left join t_dailyfrozenpacking_main_rmlotid c on c.id=b.main_id where a.entry_id='$dFrznPkgEntryId' and a.grade_id='$sGradeId' and c.rm_lot_id='$rmLotStatus' ";
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
	
	
	#Update daily Frozen Packing Entry Table Rec
	function updateDFPEntryRmlotId($dFrznPkgEntryId, $frozenLotId, $mcPackingId)
	{		
		$qry = "update t_dailyfrozenpacking_entry_rmlotid  set  mcpacking_id='$mcPackingId', frozen_lot_id='$frozenLotId' where id='$dFrznPkgEntryId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	# Update grade Rec
	function updateDFPGradeEntry($gradeEntryId, $numMC, $numLS, $oldMC, $oldLS, $conversionDate)
	{
		$uptdQry = "";
		if ($conversionDate!="") $uptdQry = " , converted_date='$conversionDate' ";
		$qry	= " update t_dailyfrozenpacking_grade set number_mc='$numMC', number_loose_slab='$numLS', mc_old='$oldMC', loose_slab_old = '$oldLS' $uptdQry where id='$gradeEntryId'";
		//echo "<br>$qry";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update grade Rec
	function updateDFPGradeEntryRMlotId($gradeEntryId, $numMC, $numLS, $oldMC, $oldLS, $conversionDate)
	{
		$uptdQry = "";
		if ($conversionDate!="") $uptdQry = " , converted_date='$conversionDate' ";
		$qry	= " update t_dailyfrozenpacking_grade_rmlotid set number_mc='$numMC', number_loose_slab='$numLS', mc_old='$oldMC', loose_slab_old = '$oldLS' $uptdQry where id='$gradeEntryId'";
		//echo "<br>$qry";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

function updatePhyMain($id)
	{
		
		$qry	= " update m_physical_stk_packing set daily_frozen_stk_used_status=1 where id='$id'";
		//echo "<br>$qry";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# get Pkd Qty based on entry id
	# Return Packed Qty, Num of MCs, Frozen qty (based on decl.wt)
	function getPkdQty($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPackingId)
	{
		
		$qry = "select ((sum(tdfpg.number_mc)*mfp.filled_wt*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPackingId'  and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' 
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

		# get Pkd Qty based on entry id
	# Return Packed Qty, Num of MCs, Frozen qty (based on decl.wt)
	function getPkdQtyRmlotId($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPackingId,$rmLotID)
	{
		
		$qry = "select ((sum(tdfpg.number_mc)*mfp.filled_wt*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty, ((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mmcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty 
			from 
				t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPackingId' and tdfpm.rm_lot_id='$rmLotID' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' 
			group by tdfpg.entry_id 
			";
			//and tdfpm.rm_lot_id='$rmLotID'";
			 	
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


	function insertDFPPORecs($dfpEntryID, $POId, $totalSlabs, $totalQty)
	{
		$qry	= "insert into t_dailyfrozenpacking_po (entry_id, po_id, total_slabs, total_qty, created_on) values('$dfpEntryID', '$POId', '$totalSlabs', '$totalQty', NOW())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function insertDFPPOGradeForAllocation($dfpPOEntryId, $gradeId, $numMC, $numLS)
	{
		$qry	= "insert into t_dailyfrozenpacking_allocate (po_entry_id, grade_id, number_mc, number_loose_slab, created_on) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numLS', NOW())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get Production Details
	function getAllocateProductionRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId)
	{
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id as MainId, dfppo.id as POEntryId, dfppo.po_id as POID,tdfpm.physical_stock_main_id from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id left join t_dailyfrozenpacking_po dfppo ON dfppo.entry_id=tdfpe.id  where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."'";
			
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


	function updateDFPPORecs($dfpEntryID, $POId, $totalSlabs, $totalQty, $POEntryId)
	{
		$qry	= "update t_dailyfrozenpacking_po SET entry_id='$dfpEntryID', po_id='$POId', total_slabs='$totalSlabs', total_qty='$totalQty' WHERE id='$POEntryId'";
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


	function deleteAllocationEntry($allocationPOEntryId)
	{
		$deleteFromAllocate = $this->deleteFromDFPAllocate($allocationPOEntryId);
		if ($deleteFromAllocate) {
			$this->deleteFromDFPPO($allocationPOEntryId);
			return true;
		} 
		return false;
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

	// Get Qty
	function getAvailablePacks($processCodeId, $freezingStageId, $frozenCodeId, $gradeId)
	{
		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po)
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
		
		return (sizeof($result)>0)?array($totNumMc,$totNumLS):array();
	}


	function getPOAllocatedRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		$qry = "select dfppo.id from t_dailyfrozenpacking_po dfppo  where dfppo.processcode_id='$processCodeId' and dfppo.freezing_stage_id='$freezingStageId' and dfppo.frozencode_id='$frozenCodeId' and dfppo.mcpacking_id='$mcPkgId' and dfppo.created_on>='".$fromDate."' and dfppo.created_on<='".$tillDate."'";
			
		//echo "getPOAllocatedRecs=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	function getLotIdOnDate($entryDate,$company,$unit)
	{
		$qry = "SELECT id,concat(alpha_character,rm_lotid) FROM t_manage_rm_lotid where created_on='$entryDate' and company_id='$company' and unit_id='$unit'";
		//	$qry = "SELECT b.id,concat(b.alpha_character,b.rm_lotid) FROM `t_rmweightaftergrading` a left join t_manage_rm_lotid b on a.rmLotId = b.id where a.created_on='$entryDate' and b.company_id='$company' and b.unit_id='$unit' union SELECT b.id,concat(b.alpha_character,b.rm_lotid) FROM `t_dailycatch_main` a left join t_manage_rm_lotid b on a.rm_lot_id= b.id where a.select_date='$entryDate' and b.company_id='$company'  and b.unit_id='$unit' and  a.rm_lot_id not in (select rmLotId from t_rmweightaftergrading) and  a.rm_lot_id !='0' ";
		//$qry = "SELECT b.id,concat(b.alpha_character,b.rm_lotid) FROM `t_rmweightaftergrading` a left join t_manage_rm_lotid b on a.rmLotId = b.id where a.created_on='$entryDate' group by b.id";
			//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function getProductionRecsRmLotIdSelected($rmlotIds,$processId,$freezingId,$frozenCode)
	{
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id,tdfpm.physical_stock_main_id,tdfpm.rm_lot_id as rm_lot_id,tdfpe.fish_id,tdfpe.processcode_id,tdfpe.freezing_stage_id,tdfpe.frozencode_id,concat(mg.alpha_character,mg.rm_lotid) as rmName from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id left join t_manage_rm_lotid mg on mg.id=tdfpm.rm_lot_id  where tdfpm.rm_lot_id in ($rmlotIds) and tdfpe.processcode_id='$processId' and tdfpe.freezing_stage_id='$freezingId' and tdfpe.frozencode_id='$frozenCode' order by tdfpm.rm_lot_id";
			
	//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getlotId($rmlotIds,$processId,$freezingId,$frozenCode)
	{
		$qry = "select tdfpm.rm_lot_id as rm_lot_id, concat(mg.alpha_character,mg.rm_lotid) as rmName from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id left join t_manage_rm_lotid mg on mg.id=tdfpm.rm_lot_id  where tdfpm.rm_lot_id in ($rmlotIds) and tdfpe.processcode_id='$processId' and tdfpe.freezing_stage_id='$freezingId' and tdfpe.frozencode_id='$frozenCode' group by tdfpm.rm_lot_id";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function getProductionRecsRmLotIdSelected_old($frozenEntryId)
	{
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id,tdfpm.physical_stock_main_id,tdfpm.rm_lot_id as rm_lot_id from t_dailyfrozenpacking_main_rmlotid tdfpm join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id  where tdfpe.id in ($frozenEntryId)";
			
	//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function chkValidGatePassId($selDate)
	{
	
		$qry	="select id,start_no, end_no ,alpha_code from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 AND TYPE = 'LF'";
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec;
		//return (sizeof($rec)>0)?true:false;
	}
	
	function getAvailableLotIdNos()
	{
	 	$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='LF' AND end_date >= '".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1 ";
		$result = $this->databaseConnect->fetch_array($qry);
		$seal_nos_array = array();
		if(sizeof($result) > 0)
		{
			$sql = "SELECT rm_lotid FROM t_manage_rm_lotid WHERE number_gen_id = '".$result[0]['id']."' 
					UNION 
					SELECT rmlotid FROM t_rmlotid_temporary WHERE number_gen_id = '".$result[0]['id']."'  
					";
			$existsSealNos = $this->databaseConnect->fetch_array($sql);
				// echo '<pre>';
		// print_r($existsSealNos);
		// echo '</pre>';
			$existsSealNos = array_map('current', $existsSealNos);
			$start_no = (int) $result[0]['start_no'];
			$end_no   = (int) $result[0]['end_no'];
			$k = 0;
			for($i=$start_no;$i<=$end_no;$i++)
			{
				if($k == 50)
				{
					break;
				}
				if(!in_array($i,$existsSealNos))
				{
					$seal_nos_array[] = $i;
					$k++;
				}
			}
		}
		
		return $seal_nos_array;
	}
	
	function getValidendnoGatePassId($selDate)
	{
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='LF'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getNewlot($mcPackingIdCvt,$fishIdCvt,$processIdCvt,$freezingIdCvt,$frozenCodeIdCvt,$companyId,$unitId,$lsPkgArr,$gradeId,$filledWt,$EntryId,$alphacode,$slnumber,$numbergen)
	{
		
		$slabTotal=""; $qtyTotal="";
			
		$qry = "select id, code from m_grade where id in ($gradeId)  order by code asc ";
		$grd= $this->databaseConnect->getRecords($qry);
		$qry2 = "SELECT id,code,number_packs FROM `m_mcpacking` where id='$mcPackingIdCvt'";
		$mcPac=$this->databaseConnect->getRecord($qry2);
				
		$gdcnt=explode(',',$gradeId);
		$sizegd=sizeof($gdcnt);
		$numberPacks=$mcPac[2];
		$packCode=$mcPac[1];
		$packCodeId=$mcPac[0];
		$colCount=3+$sizegd;
		//background-color: #D0DAFD;
		$result="<table  align='center' style='background-color:#202020;' cellpadding='2' cellspacing='1'  >";
		###header
		$result.="<tr  align='center' bgcolor='#F0F0F0'>
						<td colspan='$colCount' class='listing-head' >Details of New RM Lot id</td> 
						<td class='listing-head' colspan='2'>Total</td>
						
				</tr>";
		//$result.="<tr  align='center'><td colspan='$colCount' class='listing-head'>$sizegd</td></tr>";
		$result.="<tr bgcolor='#F0F0F0' >";
		$result.="<td class='listing-head' style='padding-left:2px;padding-right:2px; '>RMLotId</td>";
		$result.="<td class='listing-head' style='padding-left:2px;padding-right:2px; '>SET MC PKG</td>";
		$result.="<td class='listing-head' style='padding-left:2px;padding-right:2px; border-right: 1px solid #999999;border-bottom: 1px solid #999999;'>&nbsp;</td>";
		$j=0;
		foreach($grd as $grade)
		{
		
		$result.="<td class='listing-head' style='padding-left:2px;padding-right:2px;'>$grade[1]
		<input type='hidden' name='grd_$j' id='grd_$j' value='$grade[0]'/>
		</td>";
		$j++;
		}
		$result.="<td class='listing-head' style='padding-left:2px;padding-right:2px; '>SLABS</td>";
		$result.="<td class='listing-head' style='padding-left:2px;padding-right:2px; '>QTY (KG)</td>";
		$result.="</tr>";
		
		###item
		//$result.="<tr  align='center'><td colspan='$colCount' class='listing-head'>$sizegd</td></tr>";
		$result.="<tr bgcolor='#ffffff' >";
		$result.="<td class='listing-item' style='padding-left:2px;padding-right:2px; '>$alphacode$slnumber
			<input type='hidden' name='newRMAlpha' id='newRMAlpha' value='$alphacode'/>
			<input type='hidden' name='newRMNumber' id='newRMNumber' value='$slnumber'/>
		</td>";
		$result.="<td class='listing-item' style='padding-left:2px;padding-right:2px; '>$packCode
			<input type='hidden' name='newRMmcPackingId' id='newRMmcPackingId' value='$packCodeId'/>
			<input type='hidden' name='newRMfishId' id='newRMfishId' value='$fishIdCvt'/>
			<input type='hidden' name='newRMProcessId' id='newRMProcessId' value='$processIdCvt'/>
			<input type='hidden' name='newRMFreezingId' id='newRMFreezingId' value='$freezingIdCvt'/>
			<input type='hidden' name='newRMFrozenCodeId' id='newRMFrozenCodeId' value='$frozenCodeIdCvt'/>
			<input type='hidden' name='newRMNumberGenId' id='newRMNumberGenId' value='$numbergen'/>
			<input type='hidden' name='newRMCompanyId' id='newRMCompanyId' value='$companyId'/>
			<input type='hidden' name='newRMUnitId' id='newRMUnitId' value='$unitId'/>
			<input type='hidden' name='entryidOld' id='entryidOld' value='$EntryId'/>
		</td>";
		$result.="<td class='listing-item' style='padding-left:2px;padding-right:2px; '><table cellpadding='1' cellspacing='0' width='100%'>
						<tr>
							<td nowrap class='listing-item' title='Num of MC' align='center' width='50%' style='padding-left:2px;padding-right:2px;'>MC </td>
						</tr>
						<tr>
							<TD colspan='3' background='images/HL.png' style='background-repeat:repeat-x;color:#f2f2f2;line-height:normal;' width='100' height='1'></TD>
						</tr>
						<tr>
							<td nowrap class='listing-item' title='Num of Loose Pack' align='center' width='50%' style='padding-left:2px;padding-right:2px;'>LS</td>
						</tr>
					</table></td>";
			
		for($i=0; $i<$sizegd; $i++)
		{
			$grd=$gdcnt[$i];
			$gradeIDValue=$lsPkgArr[$grd];
			if($gradeIDValue!="undefined")
			{	
				
				$ls=$gradeIDValue % $numberPacks;
				$number=explode('.',($gradeIDValue / $numberPacks));
				$mc=$number[0];
				//echo $answer.' remainder '.$remainder.'<br/>';
				$slabTotal+=$gradeIDValue;
				$qtyTotal+=$gradeIDValue*$filledWt;
				$result.="<td class='listing-item' style='padding-left:2px;padding-right:2px; '><table cellpadding='1' cellspacing='0' width='100%'>
						<tr>
							<td nowrap class='listing-item' title='Num of MC' align='center' width='50%' style='padding-left:2px;padding-right:2px;'>$mc 
							<input type='hidden' name='numMCNewRM_$i' id='numMCNewRM_$i' value='$mc'/>
							</td>
						</tr>
						<tr>
							<TD colspan='3' background='images/HL.png' style='background-repeat:repeat-x;color:#f2f2f2;line-height:normal;' width='100' height='1'></TD>
						</tr>
						<tr>
							<td nowrap class='listing-item' title='Num of Loose Pack' align='center' width='50%' style='padding-left:2px;padding-right:2px;'>$ls
							<input type='hidden' name='numLSNewRM_$i' id='numLSNewRM_$i' value='$ls'/>
							</td>
						</tr>
					</table></td>";
				
				//$result.="<td class='listing-item' style='padding-left:2px;padding-right:2px;'>$gradeIDValue</td>";
		
			}
			//echo $gradeIDValue.'<br/>';
		}
		
		$result.="<td class='listing-item' style='padding-left:2px;padding-right:2px; '>$slabTotal</td>";
		$result.="<td class='listing-item' style='padding-left:2px;padding-right:2px; '>$qtyTotal</td>";
		$result.="</tr>";
		$result.="</table>";
		return $result;
	}
	
	function addDataToEntryRmlotId($mainId,$newRMfishId,$newRMProcessId,$newRMFreezingId,$newRMFrozenCodeId,$newRMmcPackingId)
	{		
		$qry	= "insert into t_dailyfrozenpacking_entry_rmlotid (main_id,fish_id,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id) values('$mainId','$newRMfishId','$newRMProcessId','$newRMFreezingId','$newRMFrozenCodeId','$newRMmcPackingId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	function addDataToGradeRmlotId($entryId,$gradeId,$numMCNewRM,$numLSNewRM)
	{		
		$qry	= "insert into t_dailyfrozenpacking_grade_rmlotid (entry_id,grade_id,number_mc,number_loose_slab) values('$entryId','$gradeId','$numMCNewRM','$numLSNewRM')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function updateEntryRMlotLS($entryId)	
	{
		$qry	= "update t_dailyfrozenpacking_grade_rmlotid SET number_loose_slab='0' WHERE entry_id='$entryId'";
		//echo $qry;		
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}	
	function updateLSOfGrade($gradeId,$dFrznPkgEntryIdCvt,$numLSCvt)
	{
		$qry	= "update t_dailyfrozenpacking_grade_rmlotid SET number_loose_slab='$numLSCvt' WHERE entry_id='$dFrznPkgEntryIdCvt' and grade_id='$gradeId'";
		//echo $qry;		
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
}
?>