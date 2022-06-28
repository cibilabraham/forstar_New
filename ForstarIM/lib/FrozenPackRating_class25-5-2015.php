<?php
class FrozenPackRating
{
	/****************************************************************
	This class deals with all the operations relating to Frozen Packing Quick Entry List
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FrozenPackRating(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	# Insert Rec
	function addFznPckRate($qeName,$selProcessor,$effectiveDate,$rate,$ratelistid,$userId)
	{
		$qry	 = "insert into m_frznpackrating (name,processor_id,rate,ratelistid,effective_date,created_on, created_by) values('$qeName','$selProcessor','$rate','$ratelistid','$effectiveDate',NOW(),'$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	### rate list id for this current date
	function getRateListId($cDate)
	{
		//$cDate=date("Y-m-d");
		$qry = " select id from m_frzn_pkg_rate_list where  (('$cDate'>=start_date && (end_date is null || end_date=0)) or ('$cDate'>=start_date and '$cDate'<=end_date)) ";
		//$qry = " select id from m_frzn_pkg_rate_list where '$cDate'<=date_format(a.start_date,'%Y-%m-%d') ";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# Insert Rec
	function addFznPkgRateEntry($qelId, $selFish, $selProcessCode,$selFreezingStage,$selQuality)
	{
		$qry	 = "insert into m_frzn_pkg_rate (rating_id, fish_id, process_code_id,freezing_stage_id,quality_id) values('$qelId', '$selFish', '$selProcessCode','$selFreezingStage','$selQuality')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert Rec
	function addFznPkgRateFrozen($ratingId,$entryId,$frozenId)
	{
		$qry	 = "insert into m_frzn_pkg_frozen (rating_id, packing_entry_id, frozen_id) values('$ratingId', '$entryId', '$frozenId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	function addSelGradeRec($ratingId,$entryId,$frznId,$gradeId,$rate,$userId)
	{
		$qry	 = "insert into m_frzn_pkg_rate_grade (rating_id,pkg_rate_entry_id,frzn_pack_id,grade_id,rate,created_by) values('$ratingId','$entryId','$frznId','$gradeId','$rate','$userId')";
		//echo $qry.'<br/>';
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	/*function addGradeRec($ratingId,$qelEntryId, $gradeId, $g,$rate,$userId,$processor)
	{
		$qry	 = "insert into m_frzn_pkg_rate_grade(rating_id,pkg_rate_entry_id, grade_id, display_order,rate,created_by,pre_processor_id) values('$ratingId','$qelEntryId','$gradeId', '$g','$rate','$userId','$processor')";
		//$qry	 = "insert into t_frznpackrate_grade(frznrate_id, grade_id, display_order, created_by,rate) values('$qelEntryId','$gradeId', '$g', '$userId', '$rate')";
		//echo $qry;
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	*/


	# Del Temp Grade Rec
	function delTempGradeRec($userId, $qelEntryId)
	{
		$selGradeRecs = "";
		if ($qelEntryId) $selGradeRecs	= $this->getGradeRecords($qelEntryId);
		if (sizeof($selGradeRecs)>0) {
			$qry = " delete from m_frzn_pkg_rate_grade where rating_id	='$qelEntryId' ";
		} else {
			$qry = " delete from m_frzn_pkg_rate_grade where (rating_id	 is null || rating_id=0) and created_by='$userId' ";
		}
	/*	
	if (sizeof($selGradeRecs)>0) {
			$qry = " delete from t_frznpackrate_grade where frznrate_id='$qelEntryId' ";
		} else {
			$qry = " delete from t_frznpackrate_grade where (frznrate_id is null || frznrate_id=0) and created_by='$userId' ";
		}*/
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	function getGradeRecords($entryId)
	{
		$qry = " select a.grade_id, c.code, a.id, a.display_order from m_frzn_pkg_rate_grade a, m_grade c where a.grade_id = c.id and a.rating_id='$entryId' order by a.display_order asc";
		//$qry = " select a.grade_id, c.code, a.id, a.display_order from t_frznpackrate_grade a, m_grade c where a.grade_id = c.id and a.frznrate_id='$entryId' order by a.display_order asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	function getAllGradeForProcessCode($processCode)
	{
		$qry = " select grade_id from m_processcode2grade where processcode_id='$processCode'";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		foreach($result as $res)
		{
			$resultval[]=$res[0];
		}
		return $resultval;	
	}

	function getSelGradeRecords($userId, $ratingId,$pkgRateEntryId,$packfrozenId)
	{
		$whr = " (a.grade_id = c.id  or a.grade_id=0) ";

		if ($ratingId=="") $whr .= " and a.created_by='$userId' and (a.rating_id is null or a.rating_id=0) ";
		else $whr .= " and a.rating_id='$ratingId' and a.pkg_rate_entry_id='$pkgRateEntryId'";
		$whr.=" and frzn_pack_id='$packfrozenId'";
		//$orderBy	= " a.display_order asc ";	
		$orderBy	= " a.id,a.rate asc ";
		$qry = " select a.grade_id, c.code, a.id,a.rate from m_frzn_pkg_rate_grade a, m_grade c";
		//$qry = " select a.grade_id, c.code, a.id, a.display_order, a.frznrate_id,a.rate from t_frznpackrate_grade a, m_grade c";

		if ($whr!="") $qry .= " where ".$whr;
		$qry .= " group by grade_id";
		if ($orderBy) $qry .= " order by ".$orderBy;
		//echo "$qry";
		//die();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	function getSelGradeRecords_old($userId, $qelEntryId)
	{
		$whr = " a.grade_id = c.id ";

		if ($qelEntryId=="") $whr .= " and a.created_by='$userId' and (a.rating_id is null or a.rating_id=0) ";
		else $whr .= " and a.rating_id='$qelEntryId' ";

		$orderBy	= " a.display_order asc ";		
		$qry = " select a.grade_id, c.code, a.id, a.display_order, a.pkg_rate_entry_id,a.rate from m_frzn_pkg_rate_grade a, m_grade c";
		//$qry = " select a.grade_id, c.code, a.id, a.display_order, a.frznrate_id,a.rate from t_frznpackrate_grade a, m_grade c";

		if ($whr!="") $qry .= " where ".$whr;
		$qry .= " group by grade_id";
		if ($orderBy) $qry .= " order by ".$orderBy;
		//echo "$qry";
		//die();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

		# insert recs
	function insertTempPCRecs($selProcesscodes, $userId)
	{
		$this->deletePCTempTable($userId);
		$processCodes = explode(",",$selProcesscodes);
		for ($k=0; $k<sizeof($processCodes);$k++) {	
			$processCodeId = $processCodes[$k];
			$this->insertTempPCRec($processCodeId, $userId);
		}
		return true;
	}

	
	# Create temp table
	function deletePCTempTable($userId)
	{
		$qry	= " delete from temp_pc_rate where user_id=$userId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	
	# Insert Temp recs
	function insertTempPCRec($processCodeId, $userId)
	{
		$qry = "insert into temp_pc_rate (`processcode_id`, `user_id`) values('$processCodeId', '$userId')";
		//echo "<br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getTempPCRecs($userId) 
	{
		$qry = "select id from temp_pc_rate ";
		if ($userId) $qry .= " where user_id='$userId'";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	function getGradeNotSelPC($gradeId, $userId)
	{
		$qry = "select a.id, b.id, mpc.code, a.processcode_id from temp_pc_rate a left join m_processcode2grade b on a.processcode_id=b.processcode_id and b.grade_id='$gradeId' and b.unit_select='f' left join m_processcode mpc on a.processcode_id=mpc.id where a.user_id='$userId' and b.id is null order by mpc.code asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	# Process Code Grade Records
	function getPCGradeRecords($processCodes)
	{
		$qry = " select distinct a.grade_id, c.code from m_processcode2grade a, m_grade c where a.grade_id = c.id and a.processcode_id  in ($processCodes) and a.unit_select='f' order by c.code asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	

	# get All Records
	function fetchAllPagingRecords($offset, $limit)
	{
		//$whr	= " a.name is not null" ;
						
		$orderBy = "a.name asc";
		$limit  = " $offset, $limit ";

		$qry	= " select a.id, a.name, a.freezing_id, a.frozen_id, a.quality_id from m_frznpackrating a ";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		if ($limit!="")   $qry .= " limit ".$limit;
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecords()
	{
		//$whr	= " a.name is not null" ;
						
		$orderBy = "a.name asc";
		
		$qry	= "select a.id, a.name, a.freezing_id, a.frozen_id, a.quality_id from m_frznpackrating a";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Processcode Records
	function getProcessCodeRecs($fznPkngRateId)
	{
		$qry = " select a.process_code_id, b.code from m_frzn_pkg_rate a, m_processcode b where a.process_code_id=b.id and a.rating_id='$fznPkngRateId' order by a.id asc ";
		//$qry = " select a.processcode_id, b.code from t_frznpackrate_entry a, m_processcode b where a.processcode_id=b.id and a.frznrate_id='$fznPkngRateId' order by a.id asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Packing  based on id 
	function find($fznPkngRateId)
	{
		$qry	= "select a.id,a.name,a.processor_id,a.rate,a.ratelistid,a.effective_date from m_frznpackrating a where a.id='$fznPkngRateId' ";
		//$qry	= "select a.id, a.name, a.freezing_id, a.frozen_id,a.quality_id,a.processor_id from m_frznpackrating a where a.id='$fznPkngRateId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Raw data Records
	function getQELRawRecords($fznPkngRateId)
	{
		$qry = " select id, fish_id, process_code_id,freezing_stage_id,quality_id from m_frzn_pkg_rate where rating_id='$fznPkngRateId' order by id asc ";
		//$qry = " select id, fish, processcode_id from t_frznpackrate_entry where frznrate_id='$fznPkngRateId' order by id asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getFrozenCodeRecs($rating,$entyId)
	{
		$qry = " select id, frozen_id from m_frzn_pkg_frozen where rating_id='$rating' and packing_entry_id='$entyId' order by id asc ";
		//$qry = " select id, fish, processcode_id from t_frznpackrate_entry where frznrate_id='$fznPkngRateId' order by id asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update
	function updateFznPkngRateRec($fznPkngRateId,$qeName,$selProcessor,$effectiveDate,$rate,$ratelistid)
	{	
		$qry = "update m_frznpackrating set  name='$qeName',processor_id='$selProcessor',ratelistid='$ratelistid' ,effective_date='$effectiveDate',rate='$rate' where id='$fznPkngRateId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# del rate Entry Rec
	function delRateRawData($fznPkngRateId)
	{
		$qry = " delete from m_frzn_pkg_rate where rating_id='$fznPkngRateId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Del Temp Grade Rec
	function deleteRateGradeRec($qelEntryId)
	{
		$qry = " delete from m_frzn_pkg_rate_grade where rating_id='$qelEntryId' ";
		//$qry = " delete from t_frznpackrate_grade where frznrate_id='$qelEntryId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}
	
	# Del Temp Grade Rec
	function deleteFrozenPackRec($qelEntryId)
	{
		$qry = " delete from m_frzn_pkg_frozen where rating_id='$qelEntryId' ";
		//$qry = " delete from t_frznpackrate_grade where frznrate_id='$qelEntryId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}
	

	# Delete 
	function deleteFznPkngRateRec($fznPkngRateId)
	{
		$qry	=	" delete from m_frznpackrating where id='$fznPkngRateId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}


	/************************ Display Order Starts Here ******************************/
	/*
		$recId = FunctionId:MenuOrderId; FunctionId:MenuOrderId;
	*/
	function changeDisplayOrder($recId)
	{
		$splitRec = explode(";",$recId);
		$changeDisOrderF = $splitRec[0];
		$changeDisOrderS = $splitRec[1];
		list($entryIdF, $disOrderIdF) = $this->getSplittedRec($changeDisOrderF);
		list($entryIdS, $disOrderIdS) = $this->getSplittedRec($changeDisOrderS);
		if ($entryIdF!="") {
			$updateDisOrderRecF = $this->updateDisplayOrder($entryIdF, $disOrderIdF);
		}

		if ($entryIdS!="") {
			$updateDisOrderRecS = $this->updateDisplayOrder($entryIdS, $disOrderIdS);
		}
		return ($updateDisOrderRecF || $updateDisOrderRecS)?true:false;		
	}
	# Split Function Rec and Return Function Id and Menu Order
	function getSplittedRec($rec)
	{
		$splitRec = explode("-",$rec);
		return (sizeof($splitRec)>0)?array($splitRec[0], $splitRec[1]):array();
	}

	# update Menu Order
	function updateDisplayOrder($entryId, $displayOrder)
	{
		$qry = "update t_frznpackrate_grade set display_order='$displayOrder' where id='$entryId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/

	#Filter Lot Id Records Based on Date
	function fetchLotIdRecords($selDate)
	{
		$qry	=	"select a.id, b.id, c.freezer_name from t_dailyactivitychart_main a, t_dailyactivitychart_entry b, m_freezercapacity c where a.id=b.main_id and c.id=b.freezer_no and a.entry_date='$selDate' and a.flag=1 order by a.id asc, b.id asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# get Grade Recs
	function getGradeRate($processCodeId,$fishId,$rowId,$fieldId,$rateTag,$editGradeValue)
	{
		$table="";
		/*$whr = " qee.processcode_id='$processCodeId' and qem.freezing_stage_id='$freezingStageId' and qem.quality_id='$qualityId' and qem.frozencode_id='$frozenCodeId' and qem.freezing_stage_id!=0 ";	
			
		$groupBy	= " qeg.grade_id ";
		$orderBy 	= " mg.code asc";

		$qry = " select qeg.grade_id, mg.code from 
				t_fznpakng_quick_entry qem join t_fznpakng_qel_entry qee on qem.id=qee.qe_entry_id 
				join t_fznpakng_qel_grade qeg on qem.id=qeg.qe_entry_id 
				left join m_grade mg on qeg.grade_id=mg.id
			";
		
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;*/

		$qry="SELECT mg.id,mg.code FROM m_processcode2grade mpc left join m_grade mg on mpc.grade_id=mg.id left join m_processcode mp on mp.id=mpc.processcode_id where mpc.processcode_id='$processCodeId' and mp.fish_id='$fishId'";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{

			if($rateTag!="")
			{
				//$rateTag='[{"grade":["48","21"],"rate":45},{"grade":["72","24"],"rate":56},{"grade":["24","25"],"rate":78}]';
				//echo $rateTag;
				$rates=json_decode($rateTag);
				$rs=count($rates);
				//echo $rs;
				for($i=0; $i<$rs; $i++)
				{
					$gradeTot=$rates[$i];
					//printr($gradeTot);
					foreach($gradeTot->GradeId as $grades )
					{
						$gradeIds[]=$grades;
						//printr($gradeIds);
					}
					
					if($gradeTot->GradeId==$editGradeValue || $editGradeValue=='0')
					{
						$exptRate=$gradeTot->Rate;
						$arrayCnt=$i;
						//echo $rateVal;
					}
					//printr($gradeIds); 
					//echo $gradeIds;
				}
			}
			//printr($editGrade);
			//printr (explode(".",$editGrade));
			$table.='<table id="rounded-corner-es" cellpadding="0"  width="100%" cellspacing="0" border="0" align="center" bgcolor="#D0DAFD">
								<tr>
									<td colspan="3">
										<table>
											<TR>
												<TD valign="top" >
													<table  bgcolor="#e8edff">
														<tr >
															<td class="listing-item" colspan="2">';
															$gradeAllChkStatus = "";
																//if (in_array(0, $grdAll ,true)) $gradeAllChkStatus = "checked";
																if($gradeIds[0]=='A') $gradeAllChkStatus = "checked";
																else if($rateTag!="")
																{	
																	$gradeAllChkStatus = "disabled";
																}
																else
																{
																	$gradeAllChkStatus = "";
																}
																$table.='<input type="checkbox" name="gradeAll" id="gradeAll" value="0" class="chkBox" onclick="chkGradeExist();" '.$gradeAllChk.' '.$gradeAllChkStatus.'>&nbsp;ALL
															</td>
														</tr>
														<TR >';	
														
														if (sizeof($result)>0) {
															$i	= 0;
															$numLine = 2;
															$nextRec	=	0;
															$gName = "";
															foreach ($result as $cR) {	
																$gId  = $cR[0];
																$gName = $cR[1];
																$nextRec++;
																$disabledChk = "";
																$checked = "";
																$selgpId="";
																//if (in_array($gName, $selGrade)) $disabledChk = "disabled";
																//if (in_array($gId, $gradeArr) || $gradeAllChk) $disabledChk = "disabled";
																$fromDb  = false; 
																if (in_array($gId, $gradeIds, true)) {
																	//$disabledChk = "";
																	//addmode=disable, edit mode check
																	$disabledChk = "disabled";
																	//$checked = "checked";
																	$fromDb  = true;
																	$val=array_search($gId,$gradeIds);
																	$selgp=explode(",",$selGroupEntry);
																	$selgpId=$selgp[$val];
																}
																else if($gradeIds[0]=='A')
																{
																	$disabledChk = "disabled";
																	$grdName[]=$gName;
																}
																else if (in_array(0, $grdAll)) $disabledChk = "disabled";
																//printr($editGrade);
																if($editGradeValue)
																{
																	if(in_array($gId,$editGradeValue)) 
																	{
																		//echo "hii";
																		$disabledChk = "";
																		$checked = "checked";
																	}
																	$editArr=join(",",$editGradeValue);
																}
																if(!$editGradeValue && $gradeIds[0]=='A')
																{
																	$rateField="readonly";
																}
																
																//echo $selGroupEntry;
															$table.='<td class="listing-item" nowrap>
																<input type="hidden" name="gradeCode_'.$i.'" value="'.$gName.'">
																<input type="hidden" name="fromDB_'.$i.'" value="'.$fromDb.'">
																<input type="hidden" name="selgpId_'.$i.'" value="'.$selgpId.'" id="selgpId_'.$i.'">
																<input type="checkbox" name="gradeId_'.$i.'" id="gradeId_'.$i.'" value="'.$gId.'" '.$disabledChk.' '.$checked.' class="chkBox">
																'.$gName.'
															</td>';
															 if($nextRec%$numLine == 0) { 
														$table.='</tr>
														<tr>';
														
																}
																$i++;	
															 }
															}
													
														$table.='</TR>
													</table>
													
													<input type="hidden" name="rowCount" id="rowCount" value="'.$i.'" readonly />
												</TD>
												<TD valign="top">
													<table>
														<TR>
															<TD valign="top">
																<table cellpadding="0" cellspacing="0">
																	<tr>
																		
																		<td nowrap style="padding-left:10px;">
																			<input type="button" name="cmdAddExptRate" id="cmdAddExptRate" value="Save" onclick="addGrade('.$rowId.','.$fieldId.','.$processCodeId.','.$fishId.');" style=" font-size:11px;"/>
																			<input type="hidden" name="totalRowCnt" id="totalRowCnt"/>
																			<input type="hidden" name="editArrayCnt" id="editArrayCnt" value="'.$arrayCnt.'"/>
																		</td>
																	</TR>
																</table>
															</TD>
														</TR>';
														if ($rateTag!="") 
														{
														$table.='<tr>
															<TD valign="top">
																<table>
																	<tr>
																		<TD align="right">
																		<input type="button" name="cmdDelete" value="Delete" style="text-align:right; font-size:11px;" onclick="getGrades('.$processCodeId.','.$fishId.','.$rowId.','.$fieldId.');" />
																		</TD>
																	</tr>
																	<TR>
																		<TD>
																		<table id="newspaper-b1">
																		<tr align="center">
																			<th width="20">
																				<INPUT type="checkbox" name="selectall" id="selectall"  class="chkBox" onclick="chkAllData();">
																			</th>
																			<Th class="listing-head">Grade</Th>
																			<th>&nbsp;</th>
																		</tr>
																		<tbody>';
																		$rates=json_decode($rateTag);
																		$rs=count($rates);
																		//echo $rs;
																		for($j=0; $j<$rs; $j++)
																		{
																			//$allgrade=[];
																			$allgrade=""; 
																			$gradeTot=$rates[$j];
																			//printr($gradeTot);
																			//$editVal=$gradeTot->GradeId;
																			foreach($gradeTot->GradeId as $grades )
																			{
																				$allgrade[]=$grades;
																				$gRate='';
																				//printr($allgrade);
																			}
																			if((sizeof($allgrade)>0) && ($allgrade[0]!="A"))
																			{
																				$grades=join(",",$allgrade);
																				$et=join(",",$allgrade);
																				$editgrades="[".$et."]";
																				$gradeName=$this->getGradeName($grades);
																			}
																			else if((sizeof($allgrade)>0) && ($allgrade[0]=="A"))
																			{
																				$gradeName=join(",",$grdName);
																				$editgrades="[0]";
																				//$gradeName=$grdName;
																			}
																			//$grdName
																			
																			//$rateValue=$gradeTot->Rate;
																		$table.='<TR>
																				<td width="20" align="center"><input type="checkbox" name="remove_'.$j.'" id="remove_'.$j.'" value="'.$j.'" class="chkBox1" ></td>	
																				<TD class="listing-item">'.$gradeName.'</TD>
																					
																				<td>
																					<input type="submit" name="cmdEdit" value="Edit" onClick="editGrade('.$processCodeId.','.$fishId.','.$rowId.','.$fieldId.','.$editgrades.');" style="text-align:right; font-size:11px;"/>
																				</td>
																			</TR>';
																		}
																	$table.='</tbody>
																	<input type="hidden" name="tblRowCount" id="tblRowCount" value="'.$j.'" />
																	</table>';
																
																	$table.='</TD>
																</TR>
															</table>
														</TD>
													</tr>';
													} // Size chk ends here
												$table.='</table>
											</TD>
										</TR>
									</table>
								</td>
							</tr>						
						</table>';
		}
		return $table;
	}


	function getGradeName($allgrade)
	{
		$qry="select code FROM `m_grade` where id in ($allgrade) ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{
			foreach($result as $res)
			{
				$resultArr[]=$res[0];
			}
		}
		if(sizeof($resultArr)>0)
		{
			$returnArr=join(",",$resultArr);
		}
		return $returnArr;
	}

	function getGrades_old($processCodeId,$fishId, $freezingStageId, $frozenCodeId, $qualityId)
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

















/*

	function addFznPkngQuickEntryList($qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $userId, $brandFrom)
	{
		$qry	 = "insert into t_fznpakng_quick_entry (name, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, created, createdby, brand_from) values('$qeName', '$freezingStage', '$eUCode', '$brand', '$frozenCode', '$mCPacking', '$frozenLotId', '$exportLotId', '$selQuality', '$selCustomerId', NOW(), '$userId', '$brandFrom')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Update
	function updateFznPkngEntryRec($fznPkngQuickEntryListId, $qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $brandFrom)
	{	
		$qry = "update t_fznpakng_quick_entry set  name='$qeName', freezing_stage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', frozencode_id='$frozenCode', mcpacking_id='$mCPacking', frozen_lot_id='$frozenLotId', export_lot_id='$exportLotId', quality_id='$selQuality', customer_id='$selCustomerId', brand_from='$brandFrom' where id='$fznPkngQuickEntryListId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete 
	function deleteFznPkngQuickEntryRec($fznPkngQuickEntryListId)
	{
		$qry	=	" delete from t_fznpakng_quick_entry where id='$fznPkngQuickEntryListId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}
*/
	
/*	# Get Raw data Records
	function getQELRawRecords($fznPkngQuickEntryListId)
	{
		$qry = " select id, fish_id, processcode_id from t_fznpakng_qel_entry where frznrate_id='$fznPkngQuickEntryListId' order by id asc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	# el QEL Entry Rec
	function delQELRawData($fznPkngQuickEntryListId)
	{
		$qry = " delete from t_fznpakng_qel_entry where frznrate_id='$fznPkngQuickEntryListId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

*/

	
	/************************ Display Order Starts Here ******************************/
	/*
		$recId = FunctionId:MenuOrderId; FunctionId:MenuOrderId;
	*/
/*
	function changeDisplayOrder($recId)
	{
		$splitRec = explode(";",$recId);
		$changeDisOrderF = $splitRec[0];
		$changeDisOrderS = $splitRec[1];
		list($entryIdF, $disOrderIdF) = $this->getSplittedRec($changeDisOrderF);
		list($entryIdS, $disOrderIdS) = $this->getSplittedRec($changeDisOrderS);
		if ($entryIdF!="") {
			$updateDisOrderRecF = $this->updateQELDisplayOrder($entryIdF, $disOrderIdF);
		}

		if ($entryIdS!="") {
			$updateDisOrderRecS = $this->updateQELDisplayOrder($entryIdS, $disOrderIdS);
		}
		return ($updateDisOrderRecF || $updateDisOrderRecS)?true:false;		
	}
	# Split Function Rec and Return Function Id and Menu Order
	function getSplittedRec($rec)
	{
		$splitRec = explode("-",$rec);
		return (sizeof($splitRec)>0)?array($splitRec[0], $splitRec[1]):array();
	}

	# update Menu Order
	function updateQELDisplayOrder($entryId, $displayOrder)
	{
		$qry = "update t_fznpakng_qel_grade set display_order='$displayOrder' where id='$entryId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
*/
	/********************* Display Order End Here****************************/

/*	function addSelGradeRec($entryId, $gradeId, $displayOrder, $userId, $gradeStatus)
	{
		$qry	 = "insert into t_fznpakng_qel_grade (frznrate_id, grade_id, display_order, created_by, active) values('$entryId', '$gradeId', '$displayOrder', '$userId', '$gradeStatus')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

	function getGradeRecords($entryId)
	{
		$qry = " select a.grade_id, c.code, a.id, a.display_order from t_fznpakng_qel_grade a, m_grade c where a.grade_id = c.id and a.frznrate_id='$entryId' order by a.display_order asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}*/

/*
	# Del Temp Grade Rec
	function deleteQELGradeRec($qelEntryId)
	{
		$qry = " delete from t_fznpakng_qel_grade where frznrate_id='$qelEntryId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Get Max Dis Order Id
	function getMaxDisplayOrderId($qelEntryId)
	{
		$qry = " select max(display_order) from t_fznpakng_qel_grade where frznrate_id='$qelEntryId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];	
	}
*/

	# get Imploded Process Code
	function getDefaultGradeRecs($fznPkngQuickEntryListId)
	{
		$result = $this->getQELRawRecords($fznPkngQuickEntryListId);
		$nResultArr = array();
		$i = 0;
		foreach ($result as  $r) {
			$rPc = $r[2];
			$nResultArr[$i] = $rPc;		
			$i++;
		}
		$selPCodes = implode(",",$nResultArr);
		if (sizeof($selPCodes)>0) $defaultPCodeResult = $this->getPCGradeRecords($selPCodes);
		return $defaultPCodeResult;
	}

	

	function getGradeRecDiffSize($userId, $gradeQELId)
	{
		$getGradeRecords = $this->getDefaultGradeRecs($gradeQELId);
		# If QEL ID Exist
		$searchArr = "";	
		if (sizeof($getGradeRecords)>0 && $gradeQELId!="") {
			$selGradeArr = array();
			# Get Sel Grade Recs
			$getSelGradeRecords	= $this->getSelGradeRecords($userId, $gradeQELId);
			$nSelGradeArr = array();
			$k = 0;
			foreach ($getGradeRecords as $gr) {
				$nsGradeId = $gr[0];				
				$nSelGradeArr[$k] = $nsGradeId;
				$k++;
			}
			$sGradeArr = array();
			$m = 0;
			foreach ($getSelGradeRecords as $cRec) {
				$sGradeId = $cRec[0];
				$sGradeArr[$m] = $sGradeId;
				$m++;
			}
			$searchArr = array_diff($nSelGradeArr,$sGradeArr);
			
		} # Chk $gradeQELId Ends Here

		return sizeof($searchArr);
	}

	function addGradeToPC($processCodeId, $gradeId)
	{
		$qry	= "insert into m_processcode2grade (processcode_id, grade_id, unit_select) values('".$processCodeId."','".$gradeId."','f')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getLastRateList($selDate)
	{
		$qry	= "select id from m_frzn_pkg_rate_list where date_format(start_date,'%Y-%m-%d')>='$selDate' and (end_date is null || end_date=0) order by id desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}
	
}
?>