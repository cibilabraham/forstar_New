<?php
require_once("flib/AFModel.php");

class FrznPkgRate_model extends AFModel
{
	protected $name = "FrznPkgRate";
	protected $tableName = "m_frzn_pkg_rate";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array();

	function getPCRecs($fishCategoryId, $fishId)
	{
		//and qem.freezing_stage_id!=0 and qem.quality_id!=0

		$whr = " fc.id='$fishCategoryId' and qem.freezing_stage_id!=0 ";	
		
		if ($fishId) $whr .= " and qee.fish_id='$fishId' ";
	
		$groupBy	= " qee.processcode_id, qem.freezing_stage_id, qem.quality_id, qem.frozencode_id ";
				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc, pc.code asc, fs.rm_stage asc, mq.name asc, fpc.code asc";

		$qry = " select 
				qem.id, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, qee.processcode_id, qem.freezing_stage_id, qem.quality_id, qem.frozencode_id, qee.fish_id
			 from 
				t_fznpakng_quick_entry qem join t_fznpakng_qel_entry qee on qem.id=qee.qe_entry_id 
				join t_fznpakng_qel_grade qeg on qem.id=qeg.qe_entry_id 
				left join m_fish mf on qee.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=qee.processcode_id 
				left join m_freezingstage fs on fs.id=qem.freezing_stage_id 
				left join m_quality mq on mq.id=qem.quality_id 
				left join m_frozenpacking fpc on fpc.id=qem.frozencode_id
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo $qry;
		//return $this->queryAll($qry);
		return $qry;
	}

	# get Grade Recs
	function getGrades($processCodeId, $freezingStageId, $qualityId, $frozenCodeId)
	{
		$whr = " qee.processcode_id='$processCodeId' and qem.freezing_stage_id='$freezingStageId' and qem.quality_id='$qualityId' and qem.frozencode_id='$frozenCodeId' and qem.freezing_stage_id!=0 ";	
			
		$groupBy	= " qeg.grade_id ";
		$orderBy 	= " mg.code asc";

		$qry = " select 
				qeg.grade_id, mg.code
			 from 
				t_fznpakng_quick_entry qem join t_fznpakng_qel_entry qee on qem.id=qee.qe_entry_id 
				join t_fznpakng_qel_grade qeg on qem.id=qeg.qe_entry_id 
				left join m_grade mg on qeg.grade_id=mg.id
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "<br>$qry";
		return $this->queryAll($qry);
	}

	#QEL Wise Fish Recs
	function getQELFishRecs($fishCategoryId)
	{
		$whr = " fc.id='$fishCategoryId' and qem.freezing_stage_id!=0 ";	
			
		$groupBy	= " qee.fish_id ";
				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc";

		$qry = " select 
				qee.fish_id as fishid, mf.name as fishname
			 from 
				t_fznpakng_quick_entry qem join t_fznpakng_qel_entry qee on qem.id=qee.qe_entry_id 
				join t_fznpakng_qel_grade qeg on qem.id=qeg.qe_entry_id 
				left join m_fish mf on qee.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=qee.processcode_id 				
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "Fish=<br>$qry";
		//return $this->queryAll($qry);
		return $qry;
	}

	# Process code recs
	function processCodeRecs($fishCategoryId, $fishId)
	{
		$whr = " fc.id='$fishCategoryId' and qem.freezing_stage_id!=0 and qee.fish_id='$fishId'";	
			
		$groupBy	= " qee.processcode_id";
		$orderBy 	= "pc.code asc";

		$qry = " select 
				pc.id as pcid, pc.code as processcode, fc.id as fcid, qee.fish_id as fishid
			 from 
				t_fznpakng_quick_entry qem join t_fznpakng_qel_entry qee on qem.id=qee.qe_entry_id 
				join t_fznpakng_qel_grade qeg on qem.id=qeg.qe_entry_id 
				left join m_fish mf on qee.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=qee.processcode_id 
				left join m_freezingstage fs on fs.id=qem.freezing_stage_id 
				left join m_quality mq on mq.id=qem.quality_id 
				left join m_frozenpacking fpc on fpc.id=qem.frozencode_id
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "<br>PC=$qry";
		//return $this->queryAll($qry);
		return $qry;
	}

	function fetchQELRecs($fishCategoryId, $fishId, $processCodeId)
	{
		//and qem.freezing_stage_id!=0 and qem.quality_id!=0
		$whr = " fc.id='$fishCategoryId' and qem.freezing_stage_id!=0 ";	
		
		if ($fishId) 		$whr .= " and qee.fish_id='$fishId' ";
		if ($processCodeId)	$whr .= " and qee.processcode_id='$processCodeId' ";
	
		$groupBy	= " qee.processcode_id, qem.freezing_stage_id, qem.quality_id, qem.frozencode_id ";
				# Fish, Process code, freezin stage, quality, frozen code
		$orderBy 	= " mf.name asc, pc.code asc, fs.rm_stage asc, mq.name asc, fpc.code asc";

		$qry = " select 
				qem.id, mf.name as fishname, fc.category as categoryname, pc.code as processcode, fs.rm_stage as freezingstage, mq.name as qualityname, fpc.code as frozencode, qee.processcode_id, qem.freezing_stage_id, qem.quality_id, qem.frozencode_id, qee.fish_id
			 from 
				t_fznpakng_quick_entry qem join t_fznpakng_qel_entry qee on qem.id=qee.qe_entry_id 
				join t_fznpakng_qel_grade qeg on qem.id=qeg.qe_entry_id 
				left join m_fish mf on qee.fish_id=mf.id 
				left join m_fishcategory fc on fc.id=mf.category_id 
				left join m_processcode pc on pc.fish_id=mf.id and pc.id=qee.processcode_id 
				left join m_freezingstage fs on fs.id=qem.freezing_stage_id 
				left join m_quality mq on mq.id=qem.quality_id 
				left join m_frozenpacking fpc on fpc.id=qem.frozencode_id
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "<br>$qry";
		//return $this->queryAll($qry);
		return $qry;
	}

	# Check rate Exist
	function chkRateExist($fishId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId)
	{
		$whr = " mfpr.process_code_id='$processCodeId' and mfpr.freezing_stage_id='$freezingStageId' and mfpr.quality_id='$qualityId' and mfpr.frozen_code_id='$frozenCodeId' ";	
	
		if ($fishId)	$whr .= " and mfpr.fish_id='$fishId' ";
		if ($rateListId) $whr .= " and mfpr.rate_list_id='$rateListId' ";
			
		//$groupBy	= " qee.processcode_id";
		//$orderBy 	= "pc.code asc";

		$qry = " select mfpr.id as frznpackrateid from  m_frzn_pkg_rate mfpr
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "<br>Frozen Rate=<br>$qry<br>";
		$result = $this->query($qry);
		
		return $result;
	}

	# Rate wise group
	function getFrznPkgGrade($frznPkgRateEntryId)
	{
		//$qry = "select fprg.id, fprg.grade_id, fprg.rate, group_concat(mg.code) as gcode, group_concat(fprg.grade_id) as gid, group_concat(fprg.id) as entryid from m_frzn_pkg_rate_grade fprg left join m_grade mg on mg.id=fprg.grade_id where fprg.pkg_rate_entry_id='$frznPkgRateEntryId' group by fprg.rate order by mg.code asc";

		$qry = "select 
				fprg.id, fprg.grade_id, fprg.rate, group_concat(mg.code) as gcode, group_concat(fprg.grade_id) as gid, group_concat(fprg.id) as entryid, mpp.name as ppname, fprg.pre_processor_id as processorid  
			from m_frzn_pkg_rate_grade fprg left join m_grade mg on mg.id=fprg.grade_id 
			left join m_preprocessor mpp on fprg.pre_processor_id=mpp.id
			where fprg.pkg_rate_entry_id='$frznPkgRateEntryId' 
			group by fprg.pre_processor_id, fprg.rate order by mg.code asc
			";

		//echo "<br>Frozen Pkg G=<br>$qry<br>";

		return $this->queryAll($qry);
	}

	function getSelGrade($frznPkgRateEntryId)
	{
		$qry = "select fprg.id, fprg.grade_id from m_frzn_pkg_rate_grade fprg where fprg.pkg_rate_entry_id='$frznPkgRateEntryId'";
		//echo "<br>Frozen Pkg G=<br>$qry<br>";
		$result = $this->queryAll($qry);
		$gArr = array();
		if (sizeof($result)>0) {
			$i =0 ;
			foreach ($result as $r) {
				$gArr[$i] = $r->grade_id;
				$i++;
			}
		}

		return $gArr;
	}

	# Comma seperated grade entry id
	function deleteFrznPkgRateGrade($selGradeEntryId)
	{
		$qry = "delete from m_frzn_pkg_rate_grade where id in ($selGradeEntryId)";
		//echo $qry;
		return $this->exec($qry);
	}

	# Grade wise rate exist
	function chkGradeWiseRateExist($frznPkgGradeRateEntryId)
	{
		$qry = " select id from m_frzn_pkg_rate_grade where pkg_rate_entry_id='$frznPkgGradeRateEntryId' ";
		//echo "<br>Frozen Rate=<br>$qry<br>";
		$result = $this->query($qry);
		return $result;
	}

	# Find Default rate
	function getDefaultRate($frznPkgRateEntryId)
	{
		$qry = "select fprg.rate from m_frzn_pkg_rate_grade fprg where fprg.pkg_rate_entry_id='$frznPkgRateEntryId' and  fprg.grade_id=0 and fprg.pre_processor_id=0 ";
		//echo "<br>Frozen Pkg G=<br>$qry<br>";
		$result = $this->query($qry);
		return (sizeof($result)>0)?$result->rate:"";
	}

	# getException frzn pkg rate
	function getExpFPGRate($frznPkgRateEntryId)
	{
		$qry = "select 
				fprg.id, fprg.grade_id, fprg.rate, group_concat(mg.code) as gcode, group_concat(fprg.grade_id) as gid, group_concat(fprg.id) as entryid, mpp.name as ppname, fprg.pre_processor_id as processorid  
			from m_frzn_pkg_rate_grade fprg left join m_grade mg on mg.id=fprg.grade_id 
			left join m_preprocessor mpp on fprg.pre_processor_id=mpp.id
			where fprg.pkg_rate_entry_id='$frznPkgRateEntryId' and  (fprg.grade_id!=0 or fprg.pre_processor_id!=0)
			group by fprg.pre_processor_id, fprg.rate order by mg.code asc
			";
		//echo "<br>Frozen Pkg G=<br>$qry<br>";
		return $this->queryAll($qry);
	}

	function displayFPRExpt($frznPkgRateEntryId)
	{
		$exptRecs = $this->getExpFPGRate($frznPkgRateEntryId);
		$disFPRE = "";
		if (sizeof($exptRecs)>0) {		
			$disFPRE	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>Processor</td><td>Grade</td><td>Rate</td></tr>";	
			$totGrossWt = 0;	
			foreach ($exptRecs as $fgr) {	
				$gcomb = ($fgr->gcode)?$fgr->gcode:"ALL";
				$gRate = $fgr->rate;
				$selProcessor = ($fgr->ppname)?$fgr->ppname:"ALL";

				$disFPRE .= "<tr bgcolor=#fffbcc><td class=listing-item>$selProcessor</td><td class=listing-item align=right>$gcomb</td><td class=listing-item align=right>$gRate</td></tr>";		
			}					
			$disFPRE	.= "</table>";
		}

		return array(sizeof($exptRecs), $disFPRE);
	}

	# Default Frzn Pkg rate
	function defaultFrznPkgRate($processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId)
	{
		$qry = " select mfprg.rate from m_frzn_pkg_rate mfpr join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id where mfpr.process_code_id='$processCodeId' and mfpr.freezing_stage_id='$freezingStageId' and mfpr.quality_id='$qualityId' and mfpr.frozen_code_id='$frozenCodeId' and mfpr.rate_list_id='$rateListId' and  mfprg.grade_id=0 and mfprg.pre_processor_id=0 ";
		$result = $this->query($qry);
		return (sizeof($result)>0)?$result->rate:"";
	}


}
?>