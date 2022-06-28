<?php
class FrznPkgAccounts 
{  
	/****************************************************************
	This class deals with all the operations relating to Frozen Packing
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function FrznPkgAccounts(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function getDFPRecQry($fromDate, $tillDate, $selProcessorId,$offset, $limit)
	{		
		$whr = " dfpm.select_date>='$fromDate' and dfpm.select_date<='$tillDate' and dfpm.processor_id='$selProcessorId' and dfpm.processor_id!=0 and dfpe.freezing_stage_id!=0 ";	
				
		$groupBy	= " dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id ";

				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc, pc.code asc, fs.rm_stage asc, mq.name asc, fpc.code asc, mg.code asc";

		$qry = " select 
				mfprg.grade_id as rategradeid, dfpm.id, dfpe.id as entryid, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, dfpe.processcode_id as processcodeid, dfpe.freezing_stage_id as freezingstageid, dfpe.quality_id as qualityid, dfpe.frozencode_id as frozencodeid, dfpe.fish_id as fishid, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*if(mmcp.number_packs,mmcp.number_packs,0))+sum(dfpg.number_loose_slab)) as slab, mgz.glaze, fpc.decl_wt as declwt, fpc.filled_wt as filledwt, mfprg.rate as fprate, if (mg.code,mg.code,'ALL') as grade, if (dfpg.pkg_rate!=0,dfpg.pkg_rate,mfprg.rate) as pkgrate, dfpm.select_date as seldate, group_concat(dfpg.id) as gentryid, (if(mmcp.number_packs,mmcp.number_packs,0)) as numpack, group_concat(dfpg.number_mc) as gnummc, group_concat(dfpg.number_loose_slab) as gnumls, dfpg.settled, date_format(dfpg.settled_date,'%d/%m/%Y') as setlddate, sum(dfpg.pkg_amount) as pkgamt, mmcp.code as mcpkgcode
			 from 
				t_dailyfrozenpacking_main dfpm join t_dailyfrozenpacking_entry dfpe on dfpm.id=dfpe.main_id
				join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id
				left join m_fish mf on dfpe.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=dfpe.processcode_id 
				left join m_freezingstage fs on fs.id=dfpe.freezing_stage_id 
				left join m_quality mq on mq.id=dfpe.quality_id 
				left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id
				left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id
				left join m_glaze mgz on fpc.glaze_id=mgz.id
				left join m_frzn_pkg_rate mfpr on mfpr.fish_id=dfpe.fish_id and mfpr.process_code_id=dfpe.processcode_id and mfpr.freezing_stage_id=dfpe.freezing_stage_id and mfpr.quality_id=dfpe.quality_id and mfpr.frozen_code_id=dfpe.frozencode_id
				left join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id and (mfprg.grade_id=dfpg.grade_id )
				left join m_grade mg on mg.id=mfprg.grade_id
			";
//or mfprg.grade_id=0
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		if($limit) $qry .= " limit $offset, $limit";
		echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllRecords($fromDate, $tillDate, $selProcessorId)
	{		
		$whr = " dfpm.select_date>='$fromDate' and dfpm.select_date<='$tillDate' and dfpm.processor_id='$selProcessorId' and dfpm.processor_id!=0 and dfpe.freezing_stage_id!=0 ";	
				
		$groupBy	= " dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id ";

				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc, pc.code asc, fs.rm_stage asc, mq.name asc, fpc.code asc, mg.code asc";

		$qry = " select 
				mfprg.grade_id as rategradeid, dfpm.id, dfpe.id as entryid, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, dfpe.processcode_id as processcodeid, dfpe.freezing_stage_id as freezingstageid, dfpe.quality_id as qualityid, dfpe.frozencode_id as frozencodeid, dfpe.fish_id as fishid, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*if(mmcp.number_packs,mmcp.number_packs,0))+sum(dfpg.number_loose_slab)) as slab, mgz.glaze, fpc.decl_wt as declwt, fpc.filled_wt as filledwt, mfprg.rate as fprate, if (mg.code,mg.code,'ALL') as grade, if (dfpg.pkg_rate!=0,dfpg.pkg_rate,mfprg.rate) as pkgrate, dfpm.select_date as seldate, group_concat(dfpg.id) as gentryid, (if(mmcp.number_packs,mmcp.number_packs,0)) as numpack, group_concat(dfpg.number_mc) as gnummc, group_concat(dfpg.number_loose_slab) as gnumls, dfpg.settled, date_format(dfpg.settled_date,'%d/%m/%Y') as setlddate, sum(dfpg.pkg_amount) as pkgamt, mmcp.code as mcpkgcode
			 from 
				t_dailyfrozenpacking_main dfpm join t_dailyfrozenpacking_entry dfpe on dfpm.id=dfpe.main_id
				join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id
				left join m_fish mf on dfpe.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=dfpe.processcode_id 
				left join m_freezingstage fs on fs.id=dfpe.freezing_stage_id 
				left join m_quality mq on mq.id=dfpe.quality_id 
				left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id
				left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id
				left join m_glaze mgz on fpc.glaze_id=mgz.id
				left join m_frzn_pkg_rate mfpr on mfpr.fish_id=dfpe.fish_id and mfpr.process_code_id=dfpe.processcode_id and mfpr.freezing_stage_id=dfpe.freezing_stage_id and mfpr.quality_id=dfpe.quality_id and mfpr.frozen_code_id=dfpe.frozencode_id
				left join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id and (mfprg.grade_id=dfpg.grade_id )
				left join m_grade mg on mg.id=mfprg.grade_id
			";
//or mfprg.grade_id=0
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function getDFPRecQryNew($fromDate, $tillDate, $selProcessorId,$offset, $limit)
	{		
			
		//$groupBy	= " dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id ";

				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc, pc.code asc, fs.rm_stage asc, mq.name asc, fpc.code asc, mg.code asc";

		$qry1 = " select 
				mfprg.grade_id as rategradeid, dfpm.id, dfpe.id as entryid, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, dfpe.processcode_id as processcodeid, dfpe.freezing_stage_id as freezingstageid, dfpe.quality_id as qualityid, dfpe.frozencode_id as frozencodeid, dfpe.fish_id as fishid, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*if(mmcp.number_packs,mmcp.number_packs,0))+sum(dfpg.number_loose_slab)) as slab, mgz.glaze, fpc.decl_wt as declwt, fpc.filled_wt as filledwt, mfprg.rate as fprate, if (mg.code,mg.code,'ALL') as grade, if (dfpg.pkg_rate!=0,dfpg.pkg_rate,mfprg.rate) as pkgrate, dfpm.select_date as seldate, group_concat(dfpg.id) as gentryid, (if(mmcp.number_packs,mmcp.number_packs,0)) as numpack, group_concat(dfpg.number_mc) as gnummc, group_concat(dfpg.number_loose_slab) as gnumls, dfpg.settled, date_format(dfpg.settled_date,'%d/%m/%Y') as setlddate, sum(dfpg.pkg_amount) as pkgamt, mmcp.code as mcpkgcode
			 from 
				t_dailyfrozenpacking_main dfpm join t_dailyfrozenpacking_entry dfpe on dfpm.id=dfpe.main_id
				join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id
				left join m_fish mf on dfpe.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=dfpe.processcode_id 
				left join m_freezingstage fs on fs.id=dfpe.freezing_stage_id 
				left join m_quality mq on mq.id=dfpe.quality_id 
				left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id
				left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id
				left join m_glaze mgz on fpc.glaze_id=mgz.id
				left join m_frzn_pkg_rate mfpr on mfpr.fish_id=dfpe.fish_id and mfpr.process_code_id=dfpe.processcode_id and mfpr.freezing_stage_id=dfpe.freezing_stage_id and mfpr.quality_id=dfpe.quality_id and mfpr.frozen_code_id=dfpe.frozencode_id
				left join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id and (mfprg.grade_id=dfpg.grade_id )
				left join m_grade mg on mg.id=mfprg.grade_id where  dfpm.select_date>='$fromDate' and dfpm.select_date<='$tillDate' and dfpm.processor_id='$selProcessorId' and dfpm.processor_id!=0 and dfpe.freezing_stage_id!=0  group by  dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id";
			

	    $qry2= " select 
				mfprg.grade_id as rategradeid, dfpm.id, dfpe.id as entryid, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, dfpe.processcode_id as processcodeid, dfpe.freezing_stage_id as freezingstageid, dfpe.quality_id as qualityid, dfpe.frozencode_id as frozencodeid, dfpe.fish_id as fishid, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*if(mmcp.number_packs,mmcp.number_packs,0))+sum(dfpg.number_loose_slab)) as slab, mgz.glaze, fpc.decl_wt as declwt, fpc.filled_wt as filledwt, mfprg.rate as fprate, if (mg.code,mg.code,'ALL') as grade, if (dfpg.pkg_rate!=0,dfpg.pkg_rate,mfprg.rate) as pkgrate, dfpm.select_date as seldate, group_concat(dfpg.id) as gentryid, (if(mmcp.number_packs,mmcp.number_packs,0)) as numpack, group_concat(dfpg.number_mc) as gnummc, group_concat(dfpg.number_loose_slab) as gnumls, dfpg.settled, date_format(dfpg.settled_date,'%d/%m/%Y') as setlddate, sum(dfpg.pkg_amount) as pkgamt, mmcp.code as mcpkgcode
			 from 
				t_dailyfrozenpacking_main_rmlotid dfpm join t_dailyfrozenpacking_entry_rmlotid dfpe on dfpm.id=dfpe.main_id
				join t_dailyfrozenpacking_grade_rmlotid dfpg on dfpe.id=dfpg.entry_id
				left join m_fish mf on dfpe.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=dfpe.processcode_id 
				left join m_freezingstage fs on fs.id=dfpe.freezing_stage_id 
				left join m_quality mq on mq.id=dfpe.quality_id 
				left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id
				left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id
				left join m_glaze mgz on fpc.glaze_id=mgz.id
				left join m_frzn_pkg_rate mfpr on mfpr.fish_id=dfpe.fish_id and mfpr.process_code_id=dfpe.processcode_id and mfpr.freezing_stage_id=dfpe.freezing_stage_id and mfpr.quality_id=dfpe.quality_id and mfpr.frozen_code_id=dfpe.frozencode_id
				left join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id and (mfprg.grade_id=dfpg.grade_id )
				left join m_grade mg on mg.id=mfprg.grade_id where  dfpm.select_date>='$fromDate' and dfpm.select_date<='$tillDate' and dfpm.processor_id='$selProcessorId' and dfpm.processor_id!=0 and dfpe.freezing_stage_id!=0  group by  dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id
			";
//or mfprg.grade_id=0
		//if ($whr) 	$qry .= " where ".$whr;
	//	if ($groupBy)	$qry .= " group by ".$groupBy;
		$qry=$qry1." union ".$qry2." limit $offset, $limit";
		//if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllRecordsNew($fromDate, $tillDate, $selProcessorId)
	{		
			
		//$groupBy	= " dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id ";

				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc, pc.code asc, fs.rm_stage asc, mq.name asc, fpc.code asc, mg.code asc";

		$qry1 = " select 
				mfprg.grade_id as rategradeid, dfpm.id, dfpe.id as entryid, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, dfpe.processcode_id as processcodeid, dfpe.freezing_stage_id as freezingstageid, dfpe.quality_id as qualityid, dfpe.frozencode_id as frozencodeid, dfpe.fish_id as fishid, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*if(mmcp.number_packs,mmcp.number_packs,0))+sum(dfpg.number_loose_slab)) as slab, mgz.glaze, fpc.decl_wt as declwt, fpc.filled_wt as filledwt, mfprg.rate as fprate, if (mg.code,mg.code,'ALL') as grade, if (dfpg.pkg_rate!=0,dfpg.pkg_rate,mfprg.rate) as pkgrate, dfpm.select_date as seldate, group_concat(dfpg.id) as gentryid, (if(mmcp.number_packs,mmcp.number_packs,0)) as numpack, group_concat(dfpg.number_mc) as gnummc, group_concat(dfpg.number_loose_slab) as gnumls, dfpg.settled, date_format(dfpg.settled_date,'%d/%m/%Y') as setlddate, sum(dfpg.pkg_amount) as pkgamt, mmcp.code as mcpkgcode
			 from 
				t_dailyfrozenpacking_main dfpm join t_dailyfrozenpacking_entry dfpe on dfpm.id=dfpe.main_id
				join t_dailyfrozenpacking_grade dfpg on dfpe.id=dfpg.entry_id
				left join m_fish mf on dfpe.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=dfpe.processcode_id 
				left join m_freezingstage fs on fs.id=dfpe.freezing_stage_id 
				left join m_quality mq on mq.id=dfpe.quality_id 
				left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id
				left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id
				left join m_glaze mgz on fpc.glaze_id=mgz.id
				left join m_frzn_pkg_rate mfpr on mfpr.fish_id=dfpe.fish_id and mfpr.process_code_id=dfpe.processcode_id and mfpr.freezing_stage_id=dfpe.freezing_stage_id and mfpr.quality_id=dfpe.quality_id and mfpr.frozen_code_id=dfpe.frozencode_id
				left join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id and (mfprg.grade_id=dfpg.grade_id )
				left join m_grade mg on mg.id=mfprg.grade_id where  dfpm.select_date>='$fromDate' and dfpm.select_date<='$tillDate' and dfpm.processor_id='$selProcessorId' and dfpm.processor_id!=0 and dfpe.freezing_stage_id!=0  group by  dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id";
			

	    $qry2= " select 
				mfprg.grade_id as rategradeid, dfpm.id, dfpe.id as entryid, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, dfpe.processcode_id as processcodeid, dfpe.freezing_stage_id as freezingstageid, dfpe.quality_id as qualityid, dfpe.frozencode_id as frozencodeid, dfpe.fish_id as fishid, ((sum(dfpg.number_mc)*fpc.filled_wt* if(mmcp.number_packs,mmcp.number_packs,0)) + (sum(dfpg.number_loose_slab)*fpc.filled_wt)) as pkdqty, ((sum(dfpg.number_mc)*fpc.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(dfpg.number_loose_slab)*fpc.decl_wt)) as frozenqty, ((sum(dfpg.number_mc)*if(mmcp.number_packs,mmcp.number_packs,0))+sum(dfpg.number_loose_slab)) as slab, mgz.glaze, fpc.decl_wt as declwt, fpc.filled_wt as filledwt, mfprg.rate as fprate, if (mg.code,mg.code,'ALL') as grade, if (dfpg.pkg_rate!=0,dfpg.pkg_rate,mfprg.rate) as pkgrate, dfpm.select_date as seldate, group_concat(dfpg.id) as gentryid, (if(mmcp.number_packs,mmcp.number_packs,0)) as numpack, group_concat(dfpg.number_mc) as gnummc, group_concat(dfpg.number_loose_slab) as gnumls, dfpg.settled, date_format(dfpg.settled_date,'%d/%m/%Y') as setlddate, sum(dfpg.pkg_amount) as pkgamt, mmcp.code as mcpkgcode
			 from 
				t_dailyfrozenpacking_main_rmlotid dfpm join t_dailyfrozenpacking_entry_rmlotid dfpe on dfpm.id=dfpe.main_id
				join t_dailyfrozenpacking_grade_rmlotid dfpg on dfpe.id=dfpg.entry_id
				left join m_fish mf on dfpe.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=dfpe.processcode_id 
				left join m_freezingstage fs on fs.id=dfpe.freezing_stage_id 
				left join m_quality mq on mq.id=dfpe.quality_id 
				left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id
				left join m_mcpacking mmcp on mmcp.id=dfpe.mcpacking_id
				left join m_glaze mgz on fpc.glaze_id=mgz.id
				left join m_frzn_pkg_rate mfpr on mfpr.fish_id=dfpe.fish_id and mfpr.process_code_id=dfpe.processcode_id and mfpr.freezing_stage_id=dfpe.freezing_stage_id and mfpr.quality_id=dfpe.quality_id and mfpr.frozen_code_id=dfpe.frozencode_id
				left join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id and (mfprg.grade_id=dfpg.grade_id )
				left join m_grade mg on mg.id=mfprg.grade_id where  dfpm.select_date>='$fromDate' and dfpm.select_date<='$tillDate' and dfpm.processor_id='$selProcessorId' and dfpm.processor_id!=0 and dfpe.freezing_stage_id!=0  group by  dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id, mfprg.grade_id
			";
//or mfprg.grade_id=0
		//if ($whr) 	$qry .= " where ".$whr;
	//	if ($groupBy)	$qry .= " group by ".$groupBy;
		$qry=$qry1." union ".$qry2;
		//if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# get Common rate
	function getRate($selDate, $preProcessorId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId)
	{
		$rateListId =$this->validFPRateList($selDate);

		return $defaultRate =$this->defaultFrznPkgRate($processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId,$preProcessorId);
		//return $rate;
	}

	# Get Rate List based on Date
	function validFPRateList($selDate)
	{	
		$qry	= " select id from m_frzn_pkg_rate_list where date_format(start_date,'%Y-%m-%d')<='$selDate' and  (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date='0000-00-00')) order by start_date desc ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Default Frzn Pkg rate
	function defaultFrznPkgRate($processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId,$preProcessorId)
	{
		$qry = " select mfprg.rate from m_frzn_pkg_rate mfpr join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id where mfpr.process_code_id='$processCodeId' and mfpr.freezing_stage_id='$freezingStageId' and mfpr.quality_id='$qualityId' and mfpr.frozen_code_id='$frozenCodeId' and mfpr.rate_list_id='$rateListId' and  mfprg.grade_id=0 and (mfprg.pre_processor_id=0 or mfprg.pre_processor_id='$preProcessorId')";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	function updateDFPGradeRec($gradeEntryId, $settled, $rate, $totalAmt,$rateListId)
	{
		$qry = "update t_dailyfrozenpacking_grade set pkg_rate='$rate', pkg_amount='$totalAmt',rate_list_id='$rateListId'";
		
		if ($settled=='Y') $qry .= " , settled='$settled', settled_date=Now()";		
		else if ($settled=='N') $qry .= " , settled='$settled', settled_date=null";		
		else $qry .="";
		$qry .= "  where id='$gradeEntryId' ";
		//echo "<br>Update=<br>$qry";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
}

