<?php
class FrozenPackingRate
{  
	/****************************************************************
	This class deals with all the operations relating to loading port
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FrozenPackingRate(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
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
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
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
		$result	= $this->databaseConnect->getRecord($qry);
		
		return $result;
	}

	function chkGradeWiseRateExist($frznPkgGradeRateEntryId)
	{
		$qry = " select id from m_frzn_pkg_rate_grade where pkg_rate_entry_id='$frznPkgGradeRateEntryId' ";
		//echo "<br>Frozen Rate=<br>$qry<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}

	# Find Default rate
	function getDefaultRate($frznPkgRateEntryId)
	{
		$qry = "select fprg.rate from m_frzn_pkg_rate_grade fprg where fprg.pkg_rate_entry_id='$frznPkgRateEntryId' and  fprg.grade_id=0 and fprg.pre_processor_id=0 ";
		//echo "<br>Frozen Pkg G=<br>$qry<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	function displayFPRExpt($frznPkgRateEntryId)
	{
		$exptRecs = $this->getExpFPGRate($frznPkgRateEntryId);
		$disFPRE = "";
		if (sizeof($exptRecs)>0) {		
			$disFPRE	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>Processor</td><td>Grade</td><td>Rate</td></tr>";	
			$totGrossWt = 0;	
			foreach ($exptRecs as $fgr) {	
				$gcomb = ($fgr[3])?$fgr[3]:"ALL";
				$gRate = $fgr[2];
				$selProcessor = ($fgr[6])?$fgr[6]:"ALL";

				$disFPRE .= "<tr bgcolor=#fffbcc><td class=listing-item>$selProcessor</td><td class=listing-item align=right>$gcomb</td><td class=listing-item align=right>$gRate</td></tr>";		
			}					
			$disFPRE	.= "</table>"; 
		}

		return array(sizeof($exptRecs), $disFPRE);
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
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

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

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSelGrade($frznPkgRateEntryId)
	{
		$qry = "select fprg.id, fprg.grade_id from m_frzn_pkg_rate_grade fprg where fprg.pkg_rate_entry_id='$frznPkgRateEntryId'";
		//echo "<br>Frozen Pkg G=<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		$gArr = array();
		if (sizeof($result)>0) {
			$i =0 ;
			foreach ($result as $r) {
				$gArr[$i] = $r[1];
				$i++;
			}
		}

		return $gArr;
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
		return $result	= $this->databaseConnect->getRecords($qry);
	}



	# Comma seperated grade entry id
	function deleteFrznPkgRateGrade($selGradeEntryId)
	{
		$qry = "delete from m_frzn_pkg_rate_grade where id in ($selGradeEntryId)";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}

	function getQELWiseFishRecs($fishCategoryId)
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
		$result=$this->databaseConnect->getRecords($qry);
		return $result;
		
	}

	function getQELWisePCRecs($fishCategoryId, $fishId)
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
		$result=$this->databaseConnect->getRecords($qry);
		return $result;
	}
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
		$result=$this->databaseConnect->getRecords($qry);
		return $result;
		//return $qry;
	}

	function updateFrozenPackRateId($frznPkgRateId,$fishId,$processcodeId,$freezingStageId,$qualityId,$frozencodeId,$rateListId)
	{
		$qry	= "update m_frzn_pkg_rate set fish_id='$fishId',process_code_id='$processcodeId',freezing_stage_id='$freezingStageId',quality_id='$qualityId',frozen_code_id='$frozencodeId',rate_list_id='$rateListId' where id=$frznPkgRateId";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function addFrozenPackRate($fishId,$processcodeId,$freezingStageId,$qualityId,$frozencodeId,$rateListId)
	{
		$qry	= "insert into m_frzn_pkg_rate(fish_id,process_code_id,freezing_stage_id,quality_id,frozen_code_id,rate_list_id) values('".$fishId."','".$processcodeId."','".$freezingStageId."','".$qualityId."','".$frozencodeId."','".$rateListId."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;

	}

	# Default Frzn Pkg rate
	function defaultFrznPkgRate($processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId)
	{
		$qry = " select mfprg.rate from m_frzn_pkg_rate mfpr join m_frzn_pkg_rate_grade mfprg on mfpr.id=mfprg.pkg_rate_entry_id where mfpr.process_code_id='$processCodeId' and mfpr.freezing_stage_id='$freezingStageId' and mfpr.quality_id='$qualityId' and mfpr.frozen_code_id='$frozenCodeId' and mfpr.rate_list_id='$rateListId' and  mfprg.grade_id=0 and mfprg.pre_processor_id=0 ";
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}

	/*function addExporter($name,$displayName,$iecCode,$userId)
	{
		$qry	= "insert into m_exporter(name,created,created_by,display_name,iec_code) values('".$name."',NOW(),'".$userId."','".$displayName."','".$iecCode."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function addExporterUnit($exporter_id,$monitoringParamId,$headName)
	{
		$qry	= "insert into m_exporter_unit(unitno,unitcode,exporterid) values('".$monitoringParamId."','".$headName."','".$exporter_id."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

		# Returns all port of loading (Pagination)
	function fetchAllPagingRecords($offset, $limit,$confirm)
	{
		
		$qry	= "select id,name,address,place,pin,country,telno,faxno,alpha_code,default_row,display_name,active from m_exporter order by name asc limit $offset, $limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all port of loading
	function fetchAllRecords()
	{
		
		$qry	= "select id,name,address,place,pin,country,telno,faxno,alpha_code,default_row,display_name from m_exporter order by name asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	# Get port of loading based on id 
	function find($ExporterMasterId)
	{
		$qry	= "select id,name,address,place,pin,country,telno,faxno,alpha_code,default_row,display_name,	iec_code from  m_exporter where id=$ExporterMasterId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function findExporterUnit($ExporterMasterId)
	{
		$qry	= "select id,unitno,unitcode from  m_exporter_unit where exporterid=$ExporterMasterId";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	
	function chkEntryExist($name, $selICId)
	{
		$qry = "select id from m_exporter where name='$name' ";
		if ($selICId) $qry .= " and id!=$selICId";
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return (sizeof($result)>0)?true:false;
	}
	
	
	function updateExporterMaster($ExporterMasterId,$name,$displayName,$iecCode)
	{
		//$qry	= "update m_exporter set name='$name' where id=$ExporterMasterId";
		$qry	= "update m_exporter set name='$name',display_name='$displayName',iec_code='$iecCode' where id=$ExporterMasterId";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateExporterUnit($monitoringParamEntryId,$monitoringParamId,$headName)
	{
		$qry	= "update m_exporter_unit set unitno='$monitoringParamId',unitcode='$headName' where id=$monitoringParamEntryId";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	function updateconfirmExporterMaster($ExporterMasterId)
	{
	$qry="update m_exporter set active=1 where id=$ExporterMasterId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;

	}
	
	function updaterlconfirmExporterMaster($ExporterMasterId)
	{
	$qry="update m_exporter set active=0 where id=$ExporterMasterId";
	//echo $qry;
	$result	= $this->databaseConnect->updateRecord($qry);
	if ($result) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();		
	return $result;
	}
	
	# Delete port of loading
	function deleteExporterMaster($ExporterMasterId)
	{
		$qry	= " delete from  m_exporter where id=$ExporterMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	function delExporterExporterUnit($ExporterMasterId)
	{
		$qry	= " delete from  m_exporter_unit where exporterid=$ExporterMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	function delExporterUnit($exporterunitId)
	{
		$qry	= " delete from  m_exporter_unit where id=$exporterunitId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else	$this->databaseConnect->rollback();
		return $result;
	}
	# -----------------------------------------------------
	# Checking loading port Id is in use (Process Code, Process, Daily catch Entry, Daily Pre Process);
	# -----------------------------------------------------
	function exporterMasterRecInUse($ExporterMasterId)
	{	
		$qry = "select id from t_invoice_main where exporter_id='$ExporterMasterId'";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}
	function fetchAllUnitCodesdis($exporterid)
	{
		//$qry = "select smp.*, mp.* from m_set_monitoring_param smp left join m_monitoring_parameters mp on smp.monitoring_parameter_id=mp.id where smp.installed_capacity_id='$installedCapacityId' order by smp.id asc ";	

		$qry = "select meu.*, me.*,mp.*,mbc.* from m_plant mp left join m_exporter_unit meu on mp.id=meu.unitno left join m_exporter me on meu.exporterid=me.id left join m_billing_company mbc on mbc.id=me.name   where meu.exporterid='$exporterid' and mp.active=1 order by meu.id asc ";
		//ECHO $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		//printr($result);
		return $result;
		
		//return (sizeof($result)>0)?true:false;;
	}

	function updateExporterMasterDefaultRow($exporterId)
	{
		$qry	= "update m_exporter set default_row='Y' where id='$exporterId'";	
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	function getUnitAlphaCode($unitId,$exporterId)
	{
		

		//$qry = "select unitcode from m_exporter_unit where id='$exporterId'";
		$qry = "select unitcode from m_exporter_unit where exporterid='$exporterId' and unitno='$unitId'";
		//echo $qry;
			$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getUnitExporterAlphaCode($unitId,$exporterId)
	{
		$qry = "select unitcode from m_exporter_unit where exporterid='$exporterId' and unitno='$unitId'";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	 function getExporterDetails($exporterId)
	{
		
		$qry = "select * from  m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.id='$exporterId'";
		
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		
		$displayAddress = "";
		if (sizeof($result)>0) {

			$companyName	= $result[1];
			$address		= $result[2];
			$place			= $result[3];
			$pinCode		= $result[4];
			$country		= $result[5];
			$telNo			= $result[6];
			$faxNo			= $result[7];

			$displayAddress = strtoupper($companyName)."<br/>".strtoupper($address)."<br>".strtoupper($place)." - ".$pinCode." (".strtoupper($country).") ";
			
			$displayTelNo = "";	
			if ($incContactNo=="") {
				if ($telNo)		$displayTelNo	= "<br>Tel:&nbsp;".$telNo;
				if ($faxNo)		$displayTelNo	.= ", Fax No:&nbsp;".$faxNo;
				$displayAddress .= $displayTelNo;
			}

			$displayAddress = nl2br($displayAddress);
		}
		return $displayAddress;
	
	}
	function getExporterAlphaCode($exporterId)
	{
		$qry = "select alpha_code from m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.id='$exporterId'";
		if ($whr!="") $qry .= " where ".$whr;
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getDefaultExporter()
	{
		$qry = "select id from m_exporter where default_row='Y'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:'';		
	}
	function getExporterName($exporterId)
	{
		$qry = "select name from  m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.id='$exporterId'";
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:'';
	}
	function getExporterNameActive()
	{
		$qry = "select me.id,mbc.name,me.display_name,me.default_row from  m_billing_company mbc left join m_exporter me on mbc.id=me.name where me.active='1' order by mbc.name asc";
		$result=$this->databaseConnect->getRecords($qry);
		return $result;
	}
*/
	
}

