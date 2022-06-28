<?php
	require("include/include.php");
	require_once('lib/FrozenPackRating_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	$printMode=false;
	
	/*list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}*/
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";



	/*require_once 'include/include.php';
	require_once 'components/base/FrznPkgRate_model.php';
	require_once 'components/FrznPkgRate/FrznPkgRate_ajax.php';
	$frznPkgRateObj	= new FrznPkgRate_model();

	# Active Processor Records
	
	
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;	
	$recUpdated		= false;
	$selGroupEntry = "";
	//if (!isset($_SESSION["selComb"])) $_SESSION["selComb"] = array();*/
	$selGroupEntry = "";
	$processorRecords = $preprocessorObj->getActiveProcessorRecs("DailyFrozenPacking.php", '');
	if ($g["iframe"]!="") {
		$_SESSION["selComb"] = array();
		$_SESSION["selGrade"] = array();
	}
//echo "hii".$g["rowId"];
	//printr($_SESSION);
		
	if ($g["processCodeId"]) 		$processCodeId = $g["processCodeId"];
	else if ($p["hidProcessCodeId"]) 	$processCodeId = $p["hidProcessCodeId"];
	
	if ($g["freezingStageId"]) 		$freezingStageId = $g["freezingStageId"];
	else if ($p["hidFreezingStageId"]) 	$freezingStageId = $p["hidFreezingStageId"];

	if ($g["qualityId"]) 		$qualityId	= $g["qualityId"];
	else if ($p["hidQualityId"]) 	$qualityId = $p["hidQualityId"];

	if ($g["frozenCodeId"]) 		$frozenCodeId	= $g["frozenCodeId"];
	else if ($p["hidFrozenCodeId"]) 	$frozenCodeId	= $p["hidFrozenCodeId"];

	if ($g["rowId"]!="") $rowId	= $g["rowId"];
	else if ($p["hidRowId"]) $rowId	= $p["hidRowId"];
	
	//echo "hui".$p["hidRowId"].$rowId;
	
//echo "hii".$g["rowId"].$rowId;
	if ($g["selRowId"]) 		$selRowId	= $g["selRowId"];
	else if ($p["hidSelRowId"]) 	$selRowId	= $p["hidSelRowId"];

	if ($g["rateListId"]) 		$rateListId	= $g["rateListId"];
	else if ($p["hidRateListId"]) 	$rateListId	= $p["hidRateListId"];

	if ($g["fishId"]) 		$fishId	= $g["fishId"];
	else if ($p["hidFishId"]) 	$fishId	= $p["hidFishId"];

	if ($g["fishCategoryId"]) 		$fishCategoryId	= $g["fishCategoryId"];
	else if ($p["hidFishCategoryId"]) 	$fishCategoryId	= $p["hidFishCategoryId"];

	if ($g["rateTag"]) $rateTag	= $g["rateTag"];
	else if ($p["hidRateTag"]) $rateTag	= $p["hidRateTag"];
	
	$rowR = $processCodeId."_".$freezingStageId."_".$qualityId."_".$frozenCodeId;
	
	# Check rate entry exist in main table
	$rateExist = $frznPkgRatingObj->chkRateExist($fishId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId);
	$frznPkgRateId =$rateExist[0];
	//echo "REId=".$frznPkgRateId;

	$gradeArr = array();
	if ($frznPkgRateId) {
		# Get grade in combination
		$frznGradeRecs = $frznPkgRatingObj->getFrznPkgGrade($frznPkgRateId);

		# Sel Grade recs
		$gradeArr = $frznPkgRatingObj->getSelGrade($frznPkgRateId);	
		$gradeAllChk = "";
		//if (in_array(0, $gradeArr)) $gradeAllChk = "disabled";
	}
	
	# Grade Recs
	$gradeRecs = $frznPkgRatingObj->getGrades($processCodeId,$fishId,$freezingStageId, $frozenCodeId,$qualityId);
	
	//print_r($gradeRecs);
	//echo "PC=$processCodeId, FS=$freezingStageId, Q=$qualityId, FC=$frozenCodeId, R=$rowId, SRow=$selRowId, RATELIST=$rateListId, Fish=$fishId, FishCategory=$fishCategoryId";

	
	# Add Exception Rate	
	if ($p["cmdAddExptRate"]!="" && !$recUpdated) {
		$rowCount 	= $p["rowCount"];
		$exptRate	= trim($p["exptRate"]);	
		//echo "hii".$exptRate; die();
		$gradeCodeArr 	= array();

		$usetArr = $_SESSION["selComb"];
		if (sizeof($usetArr)>0 ) {
			$searchItem 	= $p["editId"];
			$cKey = array_search($searchItem, $usetArr);			
			unset($_SESSION['selComb'][$cKey]);	
		}
		
		if ($exptRate!=0) {	

			$j = 0;
			for ($i=0; $i<$rowCount; $i++) {
				$gradeId 	= $p["gradeId_".$i];
				$gradeCode	= $p["gradeCode_".$i];
				$fromDB		= $p["fromDB_".$i];

				if ($gradeId!="") {
					$gradeCodeArr[$j] = $gradeCode;		
					if (!in_array($gradeCode,$_SESSION["selGrade"])) {
						array_push($_SESSION["selGrade"], $gradeCode);
					}
					$j++;
				}
				# Unset 
				if ($fromDB!="" && $gradeId=="") {
					$unsetArr = $_SESSION["selGrade"];
					$key = array_search($gradeCode, $unsetArr);
					unset($_SESSION['selGrade'][$key]);
				}
			}
			
			if (sizeof($gradeCodeArr)>0) {
				$gcArr = implode(",",$gradeCodeArr);
				 $exRateArr = $gcArr.":".$exptRate;
				if (!in_array($exRateArr,$_SESSION["selComb"])) {			
					array_push($_SESSION["selComb"], $exRateArr);
				}
			}
		}	
		$recUpdated = true;	
		$exptRate = "";	
	}
	
	if ($p["editId"] && !$recUpdated) {
		$editId 	= $p["editId"];
		$editArr 	= explode(":",$editId);
		$exptGrade	= explode(",",$editArr[0]);
		$exptRate	= $editArr[1];
		$selProcessorId = $editArr[2];
		$selGroupEntry  = $p["hidGroupEntryId"];
		//print_r($exptGrade);
	}

	if ($p["cmdDelete"]!="") {
		$tblRowCount	= $p["tblRowCount"];
		$unsetArr = $_SESSION["selGrade"];
		$usetArr = $_SESSION["selComb"];

		for ($i=1; $i<=$tblRowCount; $i++) {
			$delId 		= $p["delId_".$i];
			$groupEntryId  = $p["groupEntryId_".$i]; 
			$rateListId 		= $p["rateList_".$i];
			if ($delId!="") {
				$recExistFrozen =$frozenPackingRateGradeObj->chckRateListExistInFrozenGrade($rateListId);
				if($recExistFrozen)
				{
					$delSelGradeCombination = $frznPkgRatingObj->deleteFrznPkgRateGrade($groupEntryId);	
				}			
				
				$frznGradeRecs = $frznPkgRatingObj->getFrznPkgGrade($frznPkgRateId);
				$gradeArr = $frznPkgRatingObj->getSelGrade($frznPkgRateId);
				$gradeAllChk = "";
				if (in_array(0, $gradeArr)) $gradeAllChk = "disabled";
				$gradeAllChk = "disabled";
				$gradeAllChkStatus='';
				$checked='';
				/*
				$dItem = explode(":",$delId);
				$gStg = explode(",",$dItem[0]);
				for ($j=0;$j<sizeof($gStg);$j++) {
					$gCode = $gStg[$j];
					$key = array_search($gCode, $unsetArr);
					unset($_SESSION['selGrade'][$key]);
				}				
				$cKey = array_search($delId, $usetArr);		
				unset($_SESSION['selComb'][$cKey]);
				*/
			}
		}
	}

	$exptRateArr	= $_SESSION["selComb"];
	$selGrade 	= $_SESSION["selGrade"];

	//$preProcessorRecords	= multi_unique(array_merge($activeProcessorRecords, $selProcessors));	
	# sort by name asc
	//usort($preProcessorRecords, 'cmp_name');	
	/*
	echo "<pre>";
	//print_r($selGrade);
	//print_r($exptRateArr);
	echo "</pre>";
	*/

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav		

	//$PRINT_JS	= "components/FrznPkgRate/FrozenPackingRate.js";
	$ON_LOAD_PRINT_JS	= "libjs/FrozenPackRating.js";
	//$ON_LOAD_PRINT_JS	= "libjs/FrozenPackRate.js";
	# Include Template [topLeftNav.php]
	require("template/bTLNav.php");
?>
<form name="frmFrznPkgGradeException" action="FrznPkgGradeException.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td nowrap="true">
							<!-- Form fields start -->
						<?php	
							$bxHeader="Grade Exception Rate";
							include "template/boxTL.php";
						?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3">
										<table>
											<TR>
												<TD valign="top">
												<?php			
													$entryHead = "";
													require("template/rbTop.php");
												?>
													<table>
														<tr>
															<td class="listing-item">
																<?php
																	$gradeAllChkStatus = "";
																	if (in_array(0, $exptGrade)) $gradeAllChkStatus = "checked";
																	
																?>
																<input type="checkbox" name="gradeAll" id="gradeAll" value="0" class="chkBox" onclick="chkGradeExist();" <?=$gradeAllChk?> <?=$gradeAllChkStatus?>>&nbsp;ALL
															</td>
														</tr>
														<TR>				
														<?php
														if (sizeof($gradeRecs)>0) {
															$i	= 0;
															$numLine = 2;
															$nextRec	=	0;
															$gName = "";
															foreach ($gradeRecs as $cR) {	
																$gId  = $cR[0];
																$gName = $cR[1];
																$nextRec++;
																$disabledChk = "";
																$checked = "";
																$selgpId="";
																//if (in_array($gName, $selGrade)) $disabledChk = "disabled";
																//if (in_array($gId, $gradeArr) || $gradeAllChk) $disabledChk = "disabled";
																
																$fromDb  = false; 
																if (in_array($gId, $exptGrade)) {
																	//$disabledChk = "";
																	$checked = "checked";
																	$fromDb  = true;
																	$val=array_search($gId,$exptGrade);
																	$selgp=explode(",",$selGroupEntry);
																	$selgpId=$selgp[$val];
																}
																else if (in_array(0, $exptGrade)) $disabledChk = "disabled";

																
																//echo $selGroupEntry;
															?>
															<td class="listing-item" nowrap>
																<input type="hidden" name="gradeCode_<?=$i?>" value="<?=$gName?>">
																<input type="hidden" name="fromDB_<?=$i?>" value="<?=$fromDb?>">
																<input type="hidden" name="selgpId_<?=$i?>" value="<?=$selgpId?>" id="selgpId_<?=$i?>">
																<input type="checkbox" name="gradeId_<?=$i?>" id="gradeId_<?=$i?>" value="<?=$gId?>" <?=$disabledChk?> <?=$checked?> class="chkBox">
																<?=$gName?>
															</td>
															<? if($nextRec%$numLine == 0) { ?>
														</tr>
														<tr>
														<?php 
																}
																$i++;	
															 }
															}
														?>
														</TR>
													</table>
													<?php
														require("template/rbBottom.php");
													?>
													<input type="hidden" name="rowCount" id="rowCount" value="<?=$i?>" readonly />
												</TD>
												<TD valign="top">
													<table>
														<TR>
															<TD valign="top">
																<table cellpadding="0" cellspacing="0">
																	<tr>
																		<!--<td class="fieldName">Processor</td>-->
																		<TD class="fieldName">*Rate (Rs.)</TD>						
																	</tr>
																	<TR>
																		<!--<td nowrap align="center">
																			<select name="processorId" id="processorId" <?=$disabled?>>
																			<option value="0">--ALL--</option>
																			<? 
																			foreach($processorRecords as $ppr) {
																				$processorId	= $ppr[0];
																				$processorName	= stripSlash($ppr[1]);
																				$selected = ($selProcessorId==$processorId)?"selected":"";
																			?>
																			<option value="<?=$processorId?>" <?=$selected?>><?=$processorName?></option>
																			<? }?>
																			</select>
																		</td>
																		-->
																		<TD align="center">
																			<input type="text" name="exptRate" id="exptRate" size="3" value="<?=$exptRate?>" style="text-align:right;" autocomplete="off" />
																		</TD>
																		<td nowrap style="padding-left:10px;">
																			<!--<input type="submit" name="cmdAddExptRate" id="cmdAddExptRate" value="Save" />-->
																			<input type="button" name="cmdAddExptRate" id="cmdAddExptRate" value="Save" onclick="addGrade('<?=$rowId?>','<?=$selGroupEntry?>','<?=$rateTag?>','<?=$processCodeId?>', '<?=$freezingStageId?>', '<?=$qualityId?>', '<?=$frozenCodeId?>',  '<?=$rateListId?>', '<?=$selRowId?>', '<?=$frznPkgRateId?>', '<?=$fishId?>',  '<?=$fishCategoryId?>');" />
																		</td>
																	</TR>
																</table>
															</TD>
														</TR>
														<?php
															if (sizeof($frznGradeRecs)>0) {
														?>
														<tr>
															<TD valign="top">
																<table>
																	<tr>
																		<TD align="right">
																		<input type="submit" name="cmdDelete" value="Delete" onClick="return confirmDelete(this.form,'delId_',<?=sizeof($frznGradeRecs);?>);"/>
																		</TD>
																	</tr>
																	<TR>
																		<TD>
																		<!-- New Listing ends here -->
																			<table id="newspaper-b1">
																				<tr align="center">
																					<th width="20">
																						<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
																					</th>
																					<Th class="listing-head">Processor</Th>
																					<Th class="listing-head">Grade</Th>
																					<Th class="listing-head">Rate</Th>
																					<th>&nbsp;</th>
																				</tr>
																			<tbody>
																				<?php		
																				$j = 0;	
																				$eraArr = array();
																				foreach ($frznGradeRecs as $fgr) {
																					$j++;
																					//echo $fgr[0].'<br/>';
																					$gcomb = ($fgr[3])?$fgr[3]:"ALL";
																					$gRate = $fgr[2];

																					$groupedGradeId = $fgr[4];
																					$editVal = $groupedGradeId.":".$gRate.":".$fgr[7];
																					$groupEntryId = $fgr[5];
																					//echo $groupEntryId.'<br/>';
																					$selProcessor = ($fgr[6])?$fgr[6]:"ALL";

																					$eraArr[] = $era; 
																				?>
																				<TR>
																					<td width="20" align="center">
																						<input type="checkbox" name="delId_<?=$j;?>" id="delId_<?=$j;?>" value="<?=$editVal;?>" class="chkBox">
																						<input type="hidden" name="groupEntryId_<?=$j;?>" id="groupEntryId_<?=$j;?>" value="<?=$groupEntryId;?>" readonly>
																						<input type="hidden" name="ratelist_<?=$j;?>" id="rateList_<?=$j;?>" value="<?=$rateListId;?>" readonly>
																					</td>	
																					<TD class="listing-item"><?=$selProcessor?></TD>
																					<TD class="listing-item"><?=$gcomb?></TD>
																					<TD class="listing-item" align="right"><?=$gRate?></TD>	
																					<td>
																						<input type="submit" name="cmdEdit" value="Edit" onClick=" assignValue(this.form,'<?=$groupEntryId;?>','hidGroupEntryId'); assignValue(this.form,'<?=$editVal;?>','editId'); this.form.action='FrznPkgGradeException.php';" />
																					</td>
																				</TR>
																				<?php
																					 }
																				?>
																			</tbody>
																			<input type="hidden" name="tblRowCount" id="tblRowCount" value="<?=$j?>" />
																		</table>
																		<!-- Ned Ends here -->
																		<?php
																		if (sizeof($exptRateArr)>0) {
																		?>
																		<!--<table id="newspaper-b1">
																		<tr align="center">
																			<th width="20">
																				<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
																			</th>
																			<Th class="listing-head">Grade</Th>
																			<Th class="listing-head">Rate</Th>
																			<th>&nbsp;</th>
																		</tr>
																		<tbody>
																		<?php		
																		/*
																		$j = 0;	
																		$eraArr = array();
																		foreach ($exptRateArr as $k=>$era) {
																			$j++;
																			$eraR = explode(":",$era);
																			$gcomb = $eraR[0];
																			$gRate = $eraR[1];
																			$editVal = $gcomb.":".$gRate;
																			$eraArr[] = $era; 
																		*/
																		?>
																		<TR>
																			<td width="20" align="center"><input type="checkbox" name="delId_<?=$j;?>" id="delId_<?=$j;?>" value="<?=$editVal;?>" class="chkBox"></td>	
																			<TD class="listing-item"><?=$gcomb?></TD>
																			<TD class="listing-item" align="right"><?=$gRate?></TD>	
																			<td>
																				<input type="submit" name="cmdEdit" value="Edit" onClick="assignValue(this.form,'<?=$editVal;?>','editId');this.form.action='FrznPkgGradeException.php';" />
																			</td>
																		</TR>
																	<?php
																		// }
																	?>
																	</tbody>
																	<input type="hidden" name="tblRowCount" id="tblRowCount" value="<?=$j?>" />
																	</table>-->
																	<?php
																		} // Size chk ends here
																	?>
																	</TD>
																</TR>
															</table>
														</TD>
													</tr>
													<?php
														} // Size chk ends here
													?>
												</table>
											</TD>
										</TR>
									</table>
								</td>
							</tr>						
						</table>	
						<?php
							include "template/boxBR.php";
						?>		
					</td>
				</tr>
			</table>
				<input type="hidden" name="editId" value="<?=$editId?>" readonly title="Edit Id">
				
				<!--<input type="hidden" name="hidGroupEntryId" value="<?=$groupEntryId?>" readonly>-->
				<input type="hidden" name="hidGroupEntryId" value="<?=$selGroupEntry?>" readonly>
				<input type="hidden" name="hidProcessCodeId" id="hidProcessCodeId" value="<?=$processCodeId?>" />
				<input type="hidden" name="hidFreezingStageId" id="hidFreezingStageId" value="<?=$freezingStageId?>" />
				<input type="hidden" name="hidQualityId" id="hidQualityId" value="<?=$qualityId?>" />
				<input type="hidden" name="hidFrozenCodeId" id="hidFrozenCodeId" value="<?=$frozenCodeId?>" />
				<input type="hidden" name="hidRowId" id="hidRowId" value="<?=$rowId?>" />
				<input type="hidden" name="hidSelRowId" id="hidSelRowId" value="<?=$selRowId?>" />
				<input type="hidden" name="hidRateListId" id="hidRateListId" value="<?=$rateListId?>" />
				<input type="hidden" name="hidFishId" id="hidFishId" value="<?=$fishId?>" />
				<input type="hidden" name="hidFishCategoryId" id="hidFishCategoryId" value="<?=$fishCategoryId?>" />
		</td>
	</tr>	
	<tr>
		<TD height="20"></TD>
	</tr>
	<tr>
		<TD align="center">
		<input type="button" name="cancel" value="Close" onClick="closeSelLightBox('<?=$fishCategoryId?>', '<?=$fishId?>', '<?=$processCodeId?>', '<?=$selRowId?>', '<?=$rateListId?>')" title="Click here to close this box">
		</TD>
	</tr>		
</table>	
	<script language="javascript" type="text/javascript">		
		//alert('<?//=implode("||",$eraArr)?>');
		//parent.document.getElementById("frznPkgExceptionRate_<?//=$rowR?>").value='<?//=implode("||",$eraArr)?>';
	</script>
</form>
<?php
	# Include Template [bottomRightNav.php]
	//require("template/bbottomRightNav.php");
?>