<?php
require_once("flib/AFModel.php");

class DailyFrozenPacking_model extends AFModel
{	
	public $name="DailyFrznPkg";
	protected $tableName = "t_dailyfrozenpacking_main";
	protected $pk = 'id';	// Primary key field
	protected $fieldType = array( "created" => "N" );	// N - numeric, S - string

	# get Processors
	function getProcessors($fromDate, $tillDate, $defaultStr=null)
	{
		$qry = "select mpp.id, mpp.name from t_dailyfrozenpacking_main dfm join m_preprocessor mpp on mpp.id=dfm.processor_id where dfm.select_date>='$fromDate' and dfm.select_date<='$tillDate' group by dfm.processor_id order by mpp.name asc";
		//echo "<br>$qry<br>";
		$result = $this->queryAll($qry);
		$assoc = array();
		if ($defaultStr!=null) $assoc[""] = $defaultStr;
		foreach ($result as $rec) {
			$assoc [$rec->id] = $rec->name; 
		}
		return $assoc;
	}

	# Daily frozen packing
	# ======================== New
	function getDFPRecQry($fromDate, $tillDate, $selProcessorId)
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
	
		//echo "<br>$qry<br>";
		return $qry; 	
		//return $this->queryAll($qry);
	}

	function getDFPRecs($fromDate, $tillDate, $selProcessorId)
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
	
		//echo "<br>$qry<br>";
		
		return $this->queryAll($qry);
	}

	# get Pkd Qty based on entry id
	# Return Packed Qty, Num of MCs, Frozen qty (based on decl.wt)
	function getFrznPkgQty($fromDate, $tillDate, $preProcessorId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId)
	{
		//var calcTotSlab = (parseInt(numMC)*parseInt(numPacks))+parseInt(numLSlab); 	totSlab += parseInt(calcTotSlab);
		$qry = "select ((sum(tdfpg.number_mc)*mfp.filled_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdqty, sum(tdfpg.number_mc) as nummc, ((sum(tdfpg.number_mc)*mfp.decl_wt*if(mmcp.number_packs,mmcp.number_packs,0))+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenqty, ((sum(tdfpg.number_mc)*if(mmcp.number_packs,mmcp.number_packs,0))+sum(tdfpg.number_loose_slab)) as slab, mg.glaze, mfp.decl_wt as declwt, mfp.filled_wt as filledwt 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
				left join m_glaze mg on mfp.glaze_id=mg.id
			where 
				tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpm.processor_id='$preProcessorId' and tdfpe.quality_id='$qualityId'  and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' 
			group by tdfpg.entry_id 
			 ";	
		//echo "PkdQty===><br/>$qry<br>";		
		$result	= $this->queryAll($qry);

		/*
		echo "<pre>";
		print_r($result);
		echo "</pre>";
		*/
	
		$totPkdQty = 0;
		$totNumMc = 0;
		$totalPkdQty= 0;
		$totFrozenQty = 0;
		$totSlab = 0;	
		
		if (sizeof($result)>0) {
			//echo "===================>".$filledWt	= $result[0]->filledwt;
			foreach ($result as $r) {
				$pkdQty 	= $r->pkdqty;
				$numMc		= $r->nummc;
				$frozenQty 	= $r->frozenqty;
				$slab		= $r->slab; // LS
				$glaze		= $r->glaze;
				$netWt		= $r->declwt;
				$filledWt	= $r->filledwt;				

				$totNumMc += $numMc;
				$totPkdQty += $pkdQty;
				$totFrozenQty += $frozenQty;
				$totSlab += $slab;
			}
		}
		$totalPkdQty = number_format($totPkdQty,2,'.','');
		$totFrozenQty = number_format($totFrozenQty,2,'.','');

		return (sizeof($totPkdQty)>0)?array($totalPkdQty, $totNumMc, $totFrozenQty, $totSlab, $glaze, $netWt, $filledWt):array();
	}

	/*
	function getDFPRecs($fromDate, $tillDate, $selProcessorId)
	{		
		$whr = " dfpm.select_date>='$fromDate' and dfpm.select_date<='$tillDate' and dfpm.processor_id='$selProcessorId' and dfpm.processor_id!=0 and dfpe.freezing_stage_id!=0 ";	
				
		$groupBy	= " dfpe.processcode_id, dfpe.freezing_stage_id, dfpe.quality_id, dfpe.frozencode_id ";
				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc, pc.code asc, fs.rm_stage asc, mq.name asc, fpc.code asc";

		$qry = " select 
				dfpm.id, dfpe.id as entryid, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, dfpe.processcode_id as processcodeid, dfpe.freezing_stage_id as freezingstageid, dfpe.quality_id as qualityid, dfpe.frozencode_id as frozencodeid, dfpe.fish_id as fishid
			 from 
				t_dailyfrozenpacking_main dfpm join t_dailyfrozenpacking_entry dfpe on dfpm.id=dfpe.main_id
				left join m_fish mf on dfpe.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=dfpe.processcode_id 
				left join m_freezingstage fs on fs.id=dfpe.freezing_stage_id 
				left join m_quality mq on mq.id=dfpe.quality_id 
				left join m_frozenpacking fpc on fpc.id=dfpe.frozencode_id
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
	
		//echo "<br>$qry<br>";

		return $this->queryAll($qry);
	}
	*/

	
}

?>