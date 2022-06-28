<?php
	require("include/include.php");
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
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------	
	
	# Add New	
	if ($p["cmdAddNew"]!="") $addMode = true;	

	# Add
	if ($p["cmdAdd"]!="") {	
		$headName	= addSlash(trim($p["headName"]));
		$totalHead	= addSlash(trim($p["totalHead"]));
				
		if ($headName!="")
		{
			$damSettingRecIns =$damSettingObj->addDAMSetting($headName, $totalHead, $userId);
			$lastId = $databaseConnect->getLastInsertedId();
			if($totalHead!='')
			{
				for($i=0; $i<$totalHead; $i++)
				{
					$subheadName=$p["subheadName_".$i];
					if($subheadName!='')
					{
						$produced=$p["produced_".$i];
						$stocked=$p["stocked_".$i];
						$osSupply=$p["osSupply_".$i];
						$osSale=$p["osSale_".$i];
						$openingBalance=$p["openingBalance_".$i];
						$selUnit=$p["selUnit_".$i];
						$startDate=mysqlDateFormat($p["startDate_".$i]);
						$insSettingEntry=$damSettingObj->addDAMSettingEntry($lastId,$subheadName,$produced,$stocked,$osSupply,$osSale,$openingBalance,$selUnit,$startDate);
					}
				}
			}
			if ($damSettingRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddDAMSetting);
				$sessObj->createSession("nextPage",$url_afterAddDAMSetting.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDAMSetting;
			}
			$damSettingRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$damSettingRec	=$damSettingObj->find($editId);
		
		$editDAMSettingId	=	$damSettingRec[0];
		$head	= stripSlash($damSettingRec[1]);
		$nos	= stripSlash($damSettingRec[2]);
		
		//$monitoringParameter = $damSettingRec[8];
		$damSettingEntryRec	=$damSettingObj->getDAMSettingEntryEdit($editId);
		$damSettingEntryRecSize=sizeof($damSettingEntryRec);

	}

	# Update
	if ($p["cmdSaveChange"]!="") {

		$damSettingId	= $p["hidDAMSettingId"];
		$headName	= addSlash(trim($p["headName"]));
		$totalHead	= addSlash(trim($p["totalHead"]));
		if ($damSettingId!="" && $headName!="") 
		{
			$damSettingRecUptd	=$damSettingObj->updateDAMSetting($damSettingId, $headName, $totalHead);
			$delDAMSetEntry	=$damSettingObj->deleteDAMSettingEntry($damSettingId);
			if($totalHead!='')
			{
				for($i=0; $i<$totalHead; $i++)
				{
					$subheadName=$p["subheadName_".$i];
					if($subheadName!='')
					{
						$produced=$p["produced_".$i];
						$stocked=$p["stocked_".$i];
						$osSupply=$p["osSupply_".$i];
						$osSale=$p["osSale_".$i];
						$openingBalance=$p["openingBalance_".$i];
						$selUnit=$p["selUnit_".$i];
						$startDate=mysqlDateFormat($p["startDate_".$i]);
						$insSettingEntry=$damSettingObj->addDAMSettingEntry($damSettingId,$subheadName,$produced,$stocked,$osSupply,$osSale,$openingBalance,$selUnit,$startDate);
					}
				}
			}
		}
	
		if ($damSettingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateDAMSetting);
			$sessObj->createSession("nextPage",$url_afterUpdateDAMSetting.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDAMSetting;
		}
		$damSettingRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++)	{
			$damSettingId	=	$p["delId_".$i];

			if ( $damSettingId!="" ) {
				$damSettingRecDel =$damSettingObj->deleteDAMSetting($damSettingId);
				$damSettingEntryRecDel =$damSettingObj->deleteDAMSettingEntry($damSettingId);
			}
		}
		if ($damSettingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDAMSetting);
			$sessObj->createSession("nextPage",$url_afterDelDAMSetting.$selection);
		} else {
			$errDel	=	$msg_failDelDAMSetting;
		}
		$damSettingRecDel	=	false;
	}
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		//print_r($rowCount);
		for ($i=1; $i<=$rowCount; $i++) {
			$damSettingId	=	$p["confirmId"];
			if ($damSettingId!="") 
			{
				// Checking the selected port of loading is link with any other process
				$damSettingRecConfirm =$damSettingObj->updateconfirmDAMSetting($damSettingId);
			}
		}
		if ($damSettingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmDAMSetting);
			$sessObj->createSession("nextPage",$url_afterConfirmDAMSetting.$selection);
		} else {
			$errConfirm	=	$  $msg_failConfirmExporterMaster;
		}
	}
	
	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$damSettingId	=	$p["rlconfirmId"];
			if ($damSettingId!="")
			{
				#Check any entries exist
				$damSettingRecConfirm =$damSettingObj->updaterlconfirmDAMSetting($damSettingId);
			}
		}
		if ($damSettingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmDAMSetting);
			$sessObj->createSession("nextPage", $url_afterReleaseConfirmDAMSetting.$selection);
		} else {
			$errReleaseConfirm	= $msg_failRlConfirmExporterMaster;
		}
	}





	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "" ) $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	#List All Record	
	$damSettingRecords	=	$damSettingObj->fetchPagingRecords($offset, $limit);
	$damSettingRecordSize	=	sizeof($damSettingRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($damSettingObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {
		# Operation Type Recs
		$operationTypeRecords = $operationTypeObj->fetchAllRecords();

		# Unit Records
		$stockUnitRecs = $stockItemUnitObj->fetchAllRecords();
		//printr($stockUnitRecs);
		# Monitoring Paramter Recs
		$monitoringParameterRecords = $monitoringParametersObj->fetchAllRecords();
	}

	if ($editMode)	$heading = $label_editDAMSetting;
	else $heading = $label_addDAMSetting;
	
	//$ON_LOAD_SAJAX = "Y";	
	$ON_LOAD_PRINT_JS	= "libjs/DAMSetting.js";
	
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
								$bxHeader=" Daily Activity Monitoring Setting";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;EU Code </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
										<table width="70%" align="center">
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
																												$producedN=""; $producedY="";
																												$stockedY=""; $stockedN="";
																												$osSupplyY=""; $osSupplyN="";
																												$osSaleY="";   $osSaleN="";

																																																					$damId=$dse[0];
																												$subheadName=$dse[1];
																												$produced=$dse[2];
																												if($produced!='')
																												{ 
																													if($produced=='Y')
																													{ 
																														$producedY="selected";
																													} 
																													else if($produced=='N') 
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
																													else if($stocked=='N') 
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
																													else if($osSupply=='N')
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
																													else if($osSale=='N')
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








																												<!--<tr id="row_<?=$l?>" class="whiteRow" align="center">
																													<td class="listing-item" align="center">
																														<input id="headName_<?=$l?>" type="text" autocomplete="off" size="24" value="<?=$headName?>" name="headName_<?=$l?>">
																													</td>
																													<td class="listing-item" align="center">
																														<select id="monitoringParamId_<?=$l?>" name="monitoringParamId_<?=$l?>">
																															<option value="">--Select--</option>
																															<?php
																															foreach ($monitoringParameterRecords as $mpr) 
																															{
																																$monitoringParameterId = $mpr[0];
																																$parameter	= $mpr[1];
																																$selected = ($monitoringParameterId==$monitoringParameter)?"selected":"";
																															?>
																																<option value="<?=$monitoringParameterId?>" <?=$selected?>><?=$parameter?></option>
																															<?php
																															}
																															?>
																														</select>
																													</td>
																													<td class="listing-item" align="center">
																													<select id="smpStart_<?=$l?>" onchange="validParam();" name="smpStart_<?=$l?>">
																														<option value="">--Select--</option>
																														<option value="Y" <?=$smpStartY?>>YES</option>
																														<option value="N" <?=$smpStartN?>>NO</option>
																													</select>
																													</td>
																													<td class="listing-item" align="center">
																													<select id="smpStop_<?=$l?>" onchange="validParam();" name="smpStop_<?=$l?>">
																														<option value="">--Select--</option>
																														<option value="Y" <?=$smpStopY?>>YES</option>
																														<option value="N" <?=$smpStopN?>>NO</option>
																													</select>
																													</td>
																													<td class="listing-item" align="center">
																													 <input id="monitoringInterval_<?=$l?>" type="text"  style="text-align: right; <?=$interval?>" autocomplete="off" size="5" value="<?=$monitoringInterval?>" name="monitoringInterval_<?=$l?>">
																													</td>
																													<td id="seqFlagRCol_<?=$l?>" class="listing-item" align="center">
																														<input id="seqFlag_<?=$l?>" class="chkBox" type="checkbox" style="display:none;" value="<?=$seqFlag?>" name="seqFlag_<?=$l?>">
																													</td>
																													<td class="listing-item" align="center">
																														<a onclick="setMParamItemStatus('<?=$l?>');" href="###">
																														<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
																														</a>
																														<input id="status_<?=$l?>" type="hidden" value="" name="status_<?=$l?>">
																														<input id="IsFromDB_<?=$l?>" type="hidden" value="N" name="IsFromDB_<?=$l?>">
																														<input id="chkListEntryId_<?=$l?>" type="hidden" value="" name="chkListEntryId_<?=$l?>">
																														<input id="mParamSeqFlag_<?=$l?>" type="hidden" readonly="" name="mParamSeqFlag_<?=$l?>" value="">
																														<input id="mParamId_<?=$l?>" type="hidden" readonly="" name="mParamId_<?=$l?>" value="<?=$mParamId?>">


																													</td>
																												</tr>-->
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
																							<!--<tr>
																								<TD>
																									<a href="###" id='addRow' onclick="javascript:addNewMonitorParamItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New </a>
																								</TD>
																							</tr>-->
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
						<?php 
							if ($addMode || $editMode) {
						?>
						<tr>
							<td colspan="3" height="10" ></td>
						</tr>
						<?php
							}
						?>
						<tr>
							<td colspan="3" height="10" ></td>
						</tr>
						<tr>	
							<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$damSettingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDAMSetting.php',700,600);"><? }?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5" ></td>
						</tr>
						<?
						if($errDel!="")
						{
						?>
						<tr>
							<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
						</tr>
						<?
							}
						?>		
						<tr>
							<td width="1" ></td>
							<td colspan="2" >
								<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
								<?
								if( sizeof($damSettingRecords) > 0 )
								{
									$i	=	0;
								?>
									<thead>
									<? if($maxpage>1){?>
									<tr>
										<td colspan="4" style="padding-right:10px" class="navRow">
											<div align="right">
											<?php
												$nav  = '';
												for($page=1; $page<=$maxpage; $page++)
													{
														if ($page==$pageNo)
															{
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
															}
															else
															{
																$nav.= " <a href=\"DAMSetting.php?pageNo=$page\" class=\"link1\">$page</a> ";
															}
													}
												if ($pageNo > 1)
													{
														$page  = $pageNo - 1;
														$prev  = " <a href=\"DAMSetting.php?pageNo=$page\"  class=\"link1\"><<</a> ";
													}
													else
													{
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

												if ($pageNo < $maxpage)
													{
														$page = $pageNo + 1;
														$next = " <a href=\"DAMSetting.php?pageNo=$page\"  class=\"link1\">>></a> ";
													}
													else
													{
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
												  ?>	
													<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
												 </div>
											</td>
										  </tr>
									 <? }?>
										<tr align="center">
											<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
											<th nowrap style="padding-left:10px; padding-right:10px;">Head</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">NOS</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">Sub Head</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">Produced</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">Stocked</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">O/S Supply</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">O/S Sale</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">O/B</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">Unit</th>
											<th nowrap style="padding-left:10px; padding-right:10px;">As On</th>									
											<? if($edit==true){?>
											<th width="45">&nbsp;</th>
											<? }?>
											<? if($confirm==true){?>
												<th class="listing-head">&nbsp;</th>
											<? }?>
										</tr>
									</thead>
									<tbody>
									<?php
										foreach($damSettingRecords as $dpr) {
											$i++;
											$damSettingId	=	$dpr[0];
											$head		=	stripSlash($dpr[1]);
											$nos		=	stripSlash($dpr[2]);
											$active		=	$dpr[3];
											$damEntry=$damSettingObj->getDamSettingEntry($damSettingId);
										?>
										<tr>
											<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$damSettingId;?>" class="chkBox"></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$head;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$nos;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<? foreach($damEntry as $de)
													{
												     echo $de[1].'<br/>';
													}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<? foreach($damEntry as $de)
													{
														if($de[2]=='Y')
														{
															echo "YES".'<br/>';
														}
														elseif($de[2]=='N')
														{
															echo "NO".'<br/>';
														}
													}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<? foreach($damEntry as $de)
													{
														if($de[3]=='Y')
														{
															echo "YES".'<br/>';
														}
														elseif($de[3]=='N')
														{
															echo "NO".'<br/>';
														}
													}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<? foreach($damEntry as $de)
												{
													if($de[4]=='Y')
													{
														echo "YES".'<br/>';
													}
													elseif($de[4]=='N')
													{
														echo "NO".'<br/>';
													}
												}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<? foreach($damEntry as $de)
												{
													if($de[5]=='Y')
													{
														echo "YES".'<br/>';
													}
													elseif($de[5]=='N')
													{
														echo "NO".'<br/>';
													}
												}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<? foreach($damEntry as $de)
													{
												     echo $de[6].'<br/>';
													}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<? foreach($damEntry as $de)
													{
													$unit=$damSettingObj->getStockUnit($de[7]);
												     echo $unit.'<br/>';
													}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<? foreach($damEntry as $de)
													{
												     echo dateFormat($de[8]).'<br/>';
													}
												?>
											</td>
											<? if($edit==true){?>
											  <td class="listing-item" width="45" align="center"><? if($active!=1) { ?>	<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$damSettingId;?>,'editId');"><? } ?></td>
											  <? }?>

											<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
											<?php 
											if ($confirm==true){	
												if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$damSettingId;?>,'confirmId');" >
												<?php } else if ($active==1){ ?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$damSettingId;?>,'rlconfirmId');" >
												<?php  } }?>
												</td>
											<? }?>
										</tr>
										<?php
											}
										?>


											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="confirmId"	id="confirmId" value="" >
											<input type="hidden" name="rlconfirmId"	id="rlconfirmId" value="" >
											<input type="hidden" name="editId" value="">
											<? if($maxpage>1){?>
										<tr>
											<td colspan="4" style="padding-right:10px" class="navRow"> 
												<div align="right">
												<?php
													$nav  = '';
													for($page=1; $page<=$maxpage; $page++)
														{
															if ($page==$pageNo)
																{
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
																}
																else
																{
																	$nav.= " <a href=\"DAMSetting.php?pageNo=$page\" class=\"link1\">$page</a> ";
																}
														}
													if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"DAMSetting.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

													if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"DAMSetting.php?pageNo=$page\"  class=\"link1\">>></a> ";
														}
														else
														{
															$next = '&nbsp;'; // we're on the last page, don't print next link
															$last = '&nbsp;'; // nor the last page link
														}
														// print the navigation link
														$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
														echo $first . $prev . $nav . $next . $last . $summary; 
													  ?>	
													<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
													</div>
												 </td>
											  </tr>
											  <? }?>
	
											<?
											}
											else
											{
											?>
											<tr>
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$damSettingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDAMSetting.php',700,600);"><? }?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
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
				
				<input type="hidden" name="addMode" id="addMode" value="<?=$addMode?>" readonly /></td>
		</tr>
	</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<?php if(sizeof($damSettingRec)>0)
{
?>
<script>
//alert("hii");
	displayCalender();
</script>
<?php
}
?>
<script>
	function addNewItem()
	{
		addNewItemRow('tblSubhead','','');		
	}
</script>
<?
if($addMode)
{
?>
<script>
window.load=addNewItem();
</script>
<?
}
?>

<script>
$(document).ready(function() {
    $("#capacity").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});



</script>