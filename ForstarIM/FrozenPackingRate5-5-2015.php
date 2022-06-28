<?php
	require("include/include.php");
	require_once('lib/FrozenPackingRate_ajax.php');
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
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------
		
	$fishCategory= $fishcategoryObj->fetchAllRecords($confirm);
	$ON_LOAD_SAJAX = "Y";	
	$ON_LOAD_PRINT_JS	= "libjs/FrozenPakingRate.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>
<form name="frmDAMSetting" action="DAMSetting.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>		
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader=" Frozen Packing Rate";
								include "template/boxTL.php";
							?>

							<div id="filter"></div>
							<div id="box">
								<span id="boxtitle"></span>
								  <iframe width="100%" height="300" id="gradeExptIFrame" src="" style="border:none;" frameborder="0"></iframe>	
									<!--<p align="center"> 
									  <input type="button" name="cancel" value="Cancel" onClick="closeLightBox()">
									</p>-->
							</div>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;EU Code </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
										<table width="70%" align="center">
											<tr>
												<td>
													<div id="container-1">
														<ul>
														<? foreach($fishCategory as $fishCat)
														{
														?>
															<li><a href="#<?=$fishCat[1]?>"><span><?=$fishCat[1]?></span></a></li>
														<? } ?>
														</ul>

														<? 
														foreach($fishCategory as $fcR)
														{
															$fishData=$frozenPackingRateObj->getQELWiseFishRecs($fcR[0]);
														?>	
														<div id="<?=$fcR[1]?>">
														 <? if(sizeof($fishData)>0) 
														 {?>
															<div id="container<?=$fcR[0]?>">
																<ul>
																<? foreach($fishData as $fr) { ?>	
																<li><a href="#<?=$fcR[0].$fr[0]?>"><span><?=$fr[1]?></span></a></li>
																<? 
																}
																?>
																</ul>
																<? $i=1;?>
																<? foreach($fishData as $fr) 
																{ 
																	$qeFprRecs=$frozenPackingRateObj->getQELWisePCRecs($fcR[0],$fr[0]);
																	$qeFprPCRecs=$frozenPackingRateObj->processCodeRecs($fcR[0],$fr[0]);
																?>
																<div id="<?=$fcR[0].$fr[0]?>" class="ui-content-region"  >
																	<table cellpadding="0"  width="80%" cellspacing="0" border="0" align="center" class="tbl-pcl">
																		<tr align="center">
																			<th nowrap style="padding-left:10px; padding-right:10px;" width="10%">Process Code</th>
																			<th nowrap style="padding-left:10px; padding-right:10px;" width="16%">Freezing Stage</th>
																			<th nowrap style="padding-left:10px; padding-right:10px;" width="16%">Quality</th>
																			<th nowrap style="padding-left:10px; padding-right:10px;" width="26%">Frozen Code</th>
																			<th nowrap style="padding-left:10px; padding-right:10px;" width="2%">Default<br>Rate</th>
																			<th nowrap style="padding-left:10px; padding-right:10px;" width="2%">No.of <br>Expt. Rate</th>
																			<th nowrap style="padding-left:10px; padding-right:10px;" width="10%">Rate</th>
																			<!--<th nowrap style="padding-left:10px; padding-right:10px;">Rate/Kg</th>-->	
																			<!--<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>-->
																		</tr>
																		<tr>
																			<td valign="top">
													<!-- List All Process codes -->
																				<table cellpadding="0" cellspacing="0" class="tbl-nb" width="100%" >
																				<? foreach($qeFprPCRecs as $fprPCR) {
																				$rateListId=$frozenPackingRateListObj->latestRateList();
																				?>

																					<TR>
																						<TD>
																							<a href="###" onclick="xajax_getQEL('<?=$fprPCR[2]?>', '<?=$fprPCR[3]?>', '<?=$fprPCR[0]?>', '<?=$fcR[0].'_'.$fr[0].'_'.$i?>', '<?=$rateListId?>', 'PW'); changeFZN(this.id, '<?=$fcR[0].'_'.$fr[0].'_'.$i?>');" class="tbl-pc" id="<?=$fprPCR[0]?>"><?=$fprPCR[1]?></a>
																							<input type="hidden" name="rateModified_<?=$fcR[0].'_'.$fr[0].'_'.$i?>" id="rateModified_<?=$fcR[0].'_'.$fr[0].'_'.$fprPCR[0]?>" value="" readonly />
																						</TD>
																					</TR>
																				<? } ?>
																				</table>
																			</td>
																			<td colspan="6" id="<?=$fcR[0].'_'.$fr[0].'_'.$i?>" valign="top">
																			<!--<table cellpadding="0" cellspacing="0">
																				<TR><TD>4543534</TD></TR>
																			</table>-->	
																			
																			</td>
																		</tr>
																	</table>
																	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1" style='display:none;'>
																	<? 
																	if(sizeof($qeFprRecs)>0)
																	{
																	?>
																		<thead>			
																			<tr align="center">
																				<th nowrap style="padding-left:10px; padding-right:10px;">Fish</th>
																				<th nowrap style="padding-left:10px; padding-right:10px;">Process Code</th>
																				<th nowrap style="padding-left:10px; padding-right:10px;">Freezing Stage</th>
																				<th nowrap style="padding-left:10px; padding-right:10px;">Quality</th>
																				<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Code</th>
																				<th nowrap style="padding-left:10px; padding-right:10px;">Rate/Kg</th>	
																				<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>
																			</tr>
																		</thead>
																		<tbody>
																		<? foreach($qeFprRecs as $fprRec) { ?>
																			<tr>		
																				<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
																					<?=$fprRec[1]?>
																					<input type="hidden" name="fish_id_<?=$i?>" id="fishId_<?=$i?>" value="<?=$fprRec[11]?>" readonly />
																					<input type="hidden" name="process_code_id_<?=$i?>" id="processCodeId_<?=$i?>" value="<?=$fprRec[7]?>" readonly />
																					<input type="hidden" name="freezing_stage_id_<?=$i?>" id="freezingStageId_<?=$i?>" value="<?=$fprRec[8]?>" readonly />
																					<input type="hidden" name="quality_id_<?=$i?>" id="qualityId_<?=$i?>" value="<?$fprRec[9]?>" readonly />
																					<input type="hidden" name="frozen_code_id_<?=$i?>" id="frozenCodeId_<?=$i?>" value="<?=$fprRec[10]?>" readonly />
																				</td>
																				<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$fprRec[3]?></td>
																				<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fprRec[4]?></td>
																				<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fprRec[5]?></td>
																				<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fprRec[6]?></td>			
																				<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
																					<input type="text" name="default_rate_<?=$i?>" id="defaultRate_<?=$i?>" value="" size="3" style="text-align:right;" autocomplete="off">
																				</td>
																				<? $gCRRec=$fprRec[7]."_".$fprRec[8]."_".$fprRec[9]."_".$fprRec[10]; ?>
																				<td class="listing-item" nowrap style="vertical-align: center;padding-left:5px; padding-right:5px;">
																					<input type="hidden" name="frznPkgExceptionRate_<?=$gCRRec?>" id="frznPkgExceptionRate_<?=$gCRRec?>" value="" readonly />
																					<a href="###" onclick="getGrade(<?=$fprRec[7]?>, <?=$fprRec[8]?>,<?$fprRec[9]?>,<?=$fprRec[10]?>,<?=$i?>)">Exception Rate</a>
																				</td>		
																			</tr>
																			<?
																			$i++;
																			}
																			?>
																		</tbody>
																		<?
																		}
																		else
																		{
																		?>
																		<tr>
																			<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																		</tr>
																		<?
																		}
																		?>

																		<input type="hidden" name="hidRowCount" id="hidRowCount" value="<? sizeof($qeFprPCRecs);?>" />
																	</table>
																</div>
																<? 
																} ?>
															</div>
														<? } ?>
														</div>
														<?
														}
														?>
														<? $j=1;
														foreach($fishCategory as $fcR)
														{
															$qeFprRecs=$frozenPackingRateObj->getQELWisePCRecs($fcR[0]); 
														?>
														<div id="<?=$fcR[1]?>" style='display:none;'>
															<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
																<? if(sizeOf($qeFprRecs)>0) 
																{ ?>
																<thead>			
																	<tr align="center">
																		<th nowrap style="padding-left:10px; padding-right:10px;">Fish</th>
																		<th nowrap style="padding-left:10px; padding-right:10px;">Process Code</th>
																		<th nowrap style="padding-left:10px; padding-right:10px;">Freezing Stage</th>
																		<th nowrap style="padding-left:10px; padding-right:10px;">Quality</th>
																		<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Code</th>
																		<th nowrap style="padding-left:10px; padding-right:10px;">Rate/Kg</th>	
																		<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>
																	</tr>
																</thead>
																<tbody>
																<? 
																foreach($qeFprRecs as $fprRec) 
																{ 
																?>
																	<tr>		
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
																			<?=$fprRec[1]?>
																			<input type="hidden" name="fish_id_<?=$i?>" id="fishId_<?=$i?>" value="<?=$fprRec[11]?>" readonly />
																			<input type="hidden" name="process_code_id_<?=$i?>" id="processCodeId_<?=$i?>" value="<?=$fprRec[7]?>" readonly />
																			<input type="hidden" name="freezing_stage_id_<?=$i?>" id="freezingStageId_<?=$i?>" value="<?=$fprRec[8]?>" readonly />
																			<input type="hidden" name="quality_id_<?=$i?>" id="qualityId_<?=$i?>" value="<?$fprRec[9]?>" readonly />
																			<input type="hidden" name="frozen_code_id_<?=$i?>" id="frozenCodeId_<?=$i?>" value="<?=$fprRec[10]?>" readonly />
																		</td>
																		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$fprRec[3]?></td>
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fprRec[4]?></td>
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fprRec[5]?></td>
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fprRec[6]?></td>			
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
																			<input type="text" name="default_rate_<?=$i?>" id="defaultRate_<?=$i?>" value="" size="3" style="text-align:right;" autocomplete="off">
																		</td>
																		<? $gCRRec=$fprRec[7]."_".$fprRec[8]."_".$fprRec[9]."_".$fprRec[10];?>
																		<td class="listing-item" nowrap style="vertical-align: center;padding-left:5px; padding-right:5px;">
																			<input type="hidden" name="frznPkgExceptionRate_<?=$gCRRec?>" id="frznPkgExceptionRate_<?=$gCRRec?>" value="" readonly />
																			<a href="###" onclick="getGrade(<?=$fprRec[7]?>, <?=$fprRec[8]?>,<?$fprRec[9]?>,<?=$fprRec[10]?>,<?=$i?>)">Exception Rate</a>
																		</td>		
																	</tr>
																	<? 
																	$j++;
																	}
																	?>
																</tbody>
																<? 
																}
																else
																{
																?>
																<tr>
																	<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																</tr>
																<?
																}
																?>
																<input type="hidden" name="hidRowCount" id="hidRowCount" value="<? sizeof($qeFprPCRecs);?>" />
															</table>
														</div>
														<?
														}
														?>
													</div>
												</td>
												</tr>
												<?
													if ($editMode || $addMode) {
												?>
												<tr>
													<td>
														<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
															<tr>
																<td>
																	<!-- Form fields start -->
																<?php			
																	$entryHead = $heading;
																	require("template/rbTop.php");
																?>
																	<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																	<!--<tr>
																		<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
																		<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
																	</tr>-->
																	<tr>
																		<td width="1" ></td>
																		<td colspan="2" >
																			<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>

																					<td align="center">
																					<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDAMSetting(document.frmDAMSetting);">												</td>
																					
																					<?} else{?>

																					
																					<td align="center">
																					<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDAMSetting(document.frmDAMSetting);">												</td>

																					<?}?>
																				</tr>
																				<input type="hidden" name="hidDAMSettingId" value="<?=$editDAMSettingId;?>">
																				<tr>
																					<td nowrap class="fieldName">											  </td>
																				</tr>
																				 <tr>
																					  <td colspan="2" height="5"></td>
																				 </tr>
																				<tr>
																					<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;"> 
																						<table width="70%" border="0">
																							<tr>
																								<TD>
																									<table>
																										<TR>
																											<TD valign="top">
																												<table>
																													<tr>
																														<td class="fieldName" nowrap="nowrap">*Head</td>
																														<td class="listing-item">
																														<input name="headName" type="text" id="headName" size="28" value="<?=$head?>" onkeyup="singleHead();" >
																														<? if($editMode){?>
																															<input type="hidden" name="id" value="" readonly>
																														<? } ?>
																														</td>
																													</tr>
																												</table>
																											</TD>
																											<td>&nbsp;</td>
																											<td valign="top">
																												<table>
																													<tr>
																														<td colspan='2'>
																															<table align='center'>
																																<tr>
																																	<td class="fieldNameLeft" nowrap="nowrap">*NOS</td>
																																	<td class="listing-item">
																																		<input name="totalHead" type="text" id="totalHead" size="3" value="<?=$nos?>" style="text-align:right;" autocomplete="off" onkeyup="displaySubhead(document.getElementById('totalHead').value); singleHead();">
																																		<input type="hidden" name="hidTotalHead" id="hidTotalHead" size="3" value="<?=$nos?>" style="text-align:right;" autocomplete="off" readonly="true">
																																	</td>
																																</tr>
																															</table>
																														</td>
																													</tr>
																												</table>
																											</td>
																										</TR>
																									</table>
																								</TD>
																							</tr>
																							<tr>
																								<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
																									<table>
																										<TR>
																											<TD>
																												<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblSubhead" class="newspaperType">
																													<tr align="center">
																														<th nowrap style="text-align:center;">Sub Head</th>
																													<th nowrap style="text-align:center;">Produced</th>
																													<th nowrap style="text-align:center;">Stocked</th>
																													<th nowrap style="text-align:center;">O/S Supply</th>
																													<th nowrap style="text-align:center;">O/S Sale</th>
																													<th nowrap style="text-align:center;">O/B</th>
																													<th nowrap style="text-align:center;">Unit</th>
																													<th nowrap style="text-align:center;">As On</th>
																													</tr>
																													<? 
																													if($damSettingEntryRecSize>0)
																													{
																														$l=0;	
																														foreach($damSettingEntryRec as $dse)
																														{	$smpStopY=''; $smpStopN=''; $smpStartY=''; $smpStartN='';
																															$damId=$dse[0];
																															$subheadName=$dse[1];
																															$produced=$dse[2];
																															if($produced!='')
																															{ 
																																if($produced=='Y')
																																{ 
																																	$producedY="selected";
																																} 
																																else 
																																{
																																	$producedN="selected"; 
																																}
																															}
																															$stocked=$dse[3];
																															if($stocked!='')
																															{ 
																																if($stocked=='Y')
																																{ 
																																	$stockedY="selected";
																																} 
																																else 
																																{
																																	$stockedN="selected"; 
																																}
																															}
																															$osSupply=$dse[4];
																															if($osSupply!='')
																															{ 
																																if($osSupply=='Y')
																																{ 
																																	$osSupplyY="selected";
																																} 
																																else 
																																{
																																	$osSupplyN="selected"; 
																																}
																															}
																															$osSale=$dse[5];
																															if($osSale!='')
																															{ 
																																if($osSale=='Y')
																																{ 
																																	$osSaleY="selected";
																																} 
																																else 
																																{
																																	$osSaleN="selected"; 
																																}
																															}
																															$ob=$dse[6];
																															$stockUnit=$dse[7];
																															$asOn=dateFormat($dse[8]);
																															?>
																														<tr id="row_<?=$l?>" class="whiteRow" align="center">
																															<td class="listing-item" align="center">
																															<input id="subheadName_<?=$l?>" type="text" autocomplete="off" size="38" value="<?=$subheadName?>" name="subheadName_<?=$l?>">
																															</td>
																															<td class="listing-item" align="center">
																															<select id="produced_<?=$l?>" name="produced_<?=$l?>">
																															<option value="">--Select--</option>
																															<option value="Y" <?=$producedY?>>YES</option>
																															<option value="N" <?=$producedN?>>NO</option>
																															</select>
																															</td>
																															<td class="listing-item" align="center">
																															<select id="stocked_<?=$l?>" name="stocked_<?=$l?>">
																															<option value="">--Select--</option>
																															<option value="Y" <?=$stockedY?>>YES</option>
																															<option value="N" <?=$stockedN?>>NO</option>
																															</select>
																															</td>
																															<td class="listing-item" align="center">
																															<select id="osSupply_<?=$l?>" name="osSupply_<?=$l?>">
																															<option value="">--Select--</option>
																															<option value="Y" <?=$osSupplyY?>>YES</option>
																															<option value="N" <?=$osSupplyN?>>NO</option>
																															</select>
																															</td>
																															<td class="listing-item" align="center">
																															<select id="osSale_<?=$l?>" name="osSale_<?=$l?>">
																															<option value="">--Select--</option>
																															<option value="Y" <?=$osSaleY?>>YES</option>
																															<option value="N" <?=$osSaleN?>>NO</option>
																															</select>
																															</td>
																															<td class="listing-item" align="center">
																															<input id="openingBalance_<?=$l?>" type="text" style="text-align:right;" autocomplete="off" size="6" value="<?=$ob?>" name="openingBalance_<?=$l?>">
																															</td>
																															<td class="listing-item" align="center">
																															<select id="selUnit_<?=$l?>" name="selUnit_<?=$l?>">
																																<option>--Select--</option>
																																<? foreach($stockUnitRecs as $stkUnit)
																																{ ($stkUnit[0]==$stockUnit)? $sel="selected":$sel="";
																																?>
																																<option value='<?=$stkUnit[0]?>' <?=$sel?>><?=$stkUnit[1]?></option>
																																<?
																																}
																																?>
																															</select>
																															</td>
																															<td class="listing-item" align="center">
																															<input id="startDate_<?=$l?>" type="text" style="text-align:right;" autocomplete="off" size="8" value="<?=$asOn?>" name="startDate_<?=$l?>">
																															<input id="status_<?=$l?>" type="hidden" value="" name="status_<?=$l?>">
																															<input id="IsFromDB_<?=$l?>" type="hidden" value="N" name="IsFromDB_<?=$l?>">
																															<input id="damEntryId_<?=$l?>" type="hidden" value="" name="damEntryId_<?=$l?>">
																															</td>
																														</tr>
																														<input id="monitoringInterval_<?=$l?>" type="text"  style="text-align: right; <?=$interval?>" autocomplete="off" size="5" value="<?=$monitoringInterval?>" name="monitoringInterval_<?=$l?>">
																														<?
																														$l++;
																														}
																													}
																													?>
																												</table>
																											</td>
																										</tr>
																										<tr>
																											<td><input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$damSettingEntryRecSize?>" readonly="true">
																											</td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2" height="5"></td>
																				</tr>
																				<tr>
																				<? if($editMode){?>
																					<td align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDAMSetting(document.frmDAMSetting);">	
																					</td>
																				<? } else{?>
																					<td align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDAMSetting(document.frmDAMSetting);">
																					</td>
																				<? }?>
																				</tr>
																				<tr>
																					<td  height="10" ></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
																<?php
																	require("template/rbBottom.php");
																?>						
															</td>
														</tr>
													</table>
													<!-- Form fields end   -->
												</td>
											</tr>	
											<?
												}			
												# Listing Starts
											?>
										</table>
									</td>
								</tr>
							</table>
							<?php
								include "template/boxBR.php"
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>			
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td height="10"><input type="hidden" name="entryExist" id="entryExist" value="" readonly />
				<input type="hidden" name="addMode" id="addMode" value="<?=$addMode?>" readonly />
			</td>
		</tr>
	</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<script language="JavaScript" type="text/javascript">
	function chk()
	{
		return true;
	}

	 $(function() {
		 //alert("hii");
                $("#container-1 ul").tabs();
		$('#container-1 ul').bind('tabsselect', function(event, ui) {
			return chk();
			// Objects available in the function context:				
		});
            });

	<?php
	if (sizeof($exporterunit)>0) 
	{
	?>
		fieldId = <?=sizeof($exporterunit)?>;
	<?php
	}
	?>
	function addNewMonitorParamItem()
	{	
		addNewMonitorParam('tblMonitorParam','','');		
	}
</script>	
			

<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">

window.onLoad = addNewMonitorParamItem();

</SCRIPT>
<? }?>
