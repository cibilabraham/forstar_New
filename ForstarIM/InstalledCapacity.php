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
		$machinery	= addSlash(trim($p["machinery"]));
		$description	= addSlash(trim($p["description"]));
		$operationType	= $p["operationType"];
		$capacity	= trim($p["capacity"]);
		$unitId		= $p["unitId"];
		$perVal		= $p["perVal"];
		$monitor	= $p["monitor"];
		//$monitoringParameter = $p["monitoringParameter"];
		$monitoringParameter ="";
		$hidTableRowCount	= $p["hidTableRowCount"];
		
		if ($machinery!="")
		{
			$installedCapacityRecIns = $installedCapacityObj->addInstalledCapacity($machinery, $description, $operationType, $capacity, $unitId, $perVal, $monitor, $monitoringParameter, $userId);
			$lastId = $databaseConnect->getLastInsertedId();
			if($hidTableRowCount!='')
			{
				for($i=0; $i<$hidTableRowCount; $i++)
				{
					$status=$p["status_".$i];
					if($status!='N')
					{
						$headName=$p["headName_".$i];
						$monitoringParamId=$p["monitoringParamId_".$i];
						$smpStart=$p["smpStart_".$i];
						$smpStop=$p["smpStop_".$i];
						($smpStop)?$smpStop:"N";
						$monitoringInterval=$p["monitoringInterval_".$i];
						$seqFlag=$p["seqFlag_".$i];
						($seqFlag)?$seqFlag:"N";
						//$seqMParamId=$p["seqMParamId_".$i];
						$insMonitoringparam = $installedCapacityObj->insertMonitoringParam($lastId,$headName,$monitoringParamId,$smpStart,$smpStop,$monitoringInterval,$seqFlag,$userId);
					}
					
				}
			}
			if ($installedCapacityRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddInstalledCapacity);
				$sessObj->createSession("nextPage",$url_afterAddInstalledCapacity.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddInstalledCapacity;
			}
			$installedCapacityRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$installedCapacityRec	= $installedCapacityObj->find($editId);
		
		$editInstalledCapacityId	=	$installedCapacityRec[0];
		$machinery	= stripSlash($installedCapacityRec[1]);
		$description	= stripSlash($installedCapacityRec[2]);
		$operationType	= $installedCapacityRec[3];
		$capacity	= $installedCapacityRec[4];
		$unitId		= $installedCapacityRec[5];
		$perVal		= $installedCapacityRec[6];
		$monitor	= $installedCapacityRec[7];
		if($monitor=="S")
		{
			$Sels="selected";
		}
		else if($monitor=="M")
		{
			$Selm="selected";
		}

		//$monitoringParameter = $installedCapacityRec[8];
		$monitoringParamRec	= $installedCapacityObj->getMonitoringParam($editId);
		$monitorParamRecSize=sizeof($monitoringParamRec);

	}

	# Update
	if ($p["cmdSaveChange"]!="") {

		$installedCapacityId	= $p["hidInstalledCapacityId"];

		$machinery	= addSlash(trim($p["machinery"]));
		$description	= addSlash(trim($p["description"]));
		$operationType	= $p["operationType"];
		$capacity	= trim($p["capacity"]);
		$unitId		= $p["unitId"];
		$perVal		= $p["perVal"];
		$monitor	= $p["monitor"];
		//$monitoringParameter = $p["monitoringParameter"];
		$monitoringParameter ="";
		$hidTableRowCount	= $p["hidTableRowCount"];
		
		if ($installedCapacityId!="" && $machinery!="") 
		{
			$installedCapacityRecUptd	= $installedCapacityObj->updateInstalledCapacity($installedCapacityId, $machinery, $description, $operationType, $capacity, $unitId, $perVal, $monitor, $monitoringParameter);
			if($hidTableRowCount!='')
			{
				for($i=0; $i<$hidTableRowCount; $i++)
				{
					$status=$p["status_".$i];
					$mParamId=$p["mParamId_".$i];
					if($status!='N')
					{	
						$headName=$p["headName_".$i];
						$monitoringParamId=$p["monitoringParamId_".$i];
						$smpStart=$p["smpStart_".$i];
						$smpStop=$p["smpStop_".$i];
						($smpStop)?$smpStop:"N";
						$monitoringInterval=$p["monitoringInterval_".$i];
						$seqFlag=$p["seqFlag_".$i];
						($seqFlag)?$seqFlag:"N";
						if($mParamId!='')
						{
							//$seqMParamId=$p["seqMParamId_".$i];
							$upMonitoringparam = $installedCapacityObj->updateMonitoringparam($headName,$monitoringParamId,$smpStart,$smpStop,$monitoringInterval,$seqFlag,$mParamId);
						}
						else
						{
							$insMonitoringparam = $installedCapacityObj->insertMonitoringParam($installedCapacityId,$headName,$monitoringParamId,$smpStart,$smpStop,$monitoringInterval,$seqFlag,$userId);
					
						}
					}
					
					if ($status=='N' && $mParamId!="")
					{
						$delMonitoringRec = $installedCapacityObj->delMonitoringParamRec($mParamId);
					}
				}
			}
		}
	
		if ($installedCapacityRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateInstalledCapacity);
			$sessObj->createSession("nextPage",$url_afterUpdateInstalledCapacity.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateInstalledCapacity;
		}
		$installedCapacityRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++)	{
			$installedCapacityId	=	$p["delId_".$i];

			if ( $installedCapacityId!="" ) {
				$installedCapacityRecDel = $installedCapacityObj->deleteTypeOfOperation($installedCapacityId);
				$monitoringParamRecDel = $installedCapacityObj->deleteMonitoringParam($installedCapacityId);
			}
		}
		if ($installedCapacityRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelInstalledCapacity);
			$sessObj->createSession("nextPage",$url_afterDelInstalledCapacity.$selection);
		} else {
			$errDel	=	$msg_failDelInstalledCapacity;
		}
		$installedCapacityRecDel	=	false;
	}
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		//print_r($rowCount);
		for ($i=1; $i<=$rowCount; $i++) {
			$installedCapacityId	=	$p["confirmId"];
			if ($installedCapacityId!="") 
			{
				// Checking the selected port of loading is link with any other process
				$installedCapacityRecConfirm = $installedCapacityObj->updateconfirmInstalledCapacity($installedCapacityId);
			}
		}
		if ($installedCapacityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmInstalledCapacity);
			$sessObj->createSession("nextPage",$url_afterConfirmInstalledCapacity.$selection);
		} else {
			$errConfirm	=	$  $msg_failConfirmExporterMaster;
		}
	}
	
	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$installedCapacityId	=	$p["rlconfirmId"];
			if ($installedCapacityId!="")
			{
				#Check any entries exist
				$installedCapacityRecConfirm = $installedCapacityObj->updaterlconfirmInstalledCapacity($installedCapacityId);
			}
		}
		if ($installedCapacityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmInstalledCapacity);
			$sessObj->createSession("nextPage", $url_afterReleaseConfirmInstalledCapacity.$selection);
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
	$installedCapacityRecords	=	$installedCapacityObj->fetchPagingRecords($offset, $limit);
	$installedCapacityRecordSize	=	sizeof($installedCapacityRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($installedCapacityObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {
		# Operation Type Recs
		$operationTypeRecords = $operationTypeObj->fetchAllRecords();

		# Unit Records
		$stockUnitRecs = $stockItemUnitObj->fetchAllRecords();

		# Monitoring Paramter Recs
		$monitoringParameterRecords = $monitoringParametersObj->fetchAllRecords();
	}

	if ($editMode)	$heading = $label_editInstalledCapacity;
	else $heading = $label_addInstalledCapacity;
	
	//$ON_LOAD_SAJAX = "Y";	
	$ON_LOAD_PRINT_JS	= "libjs/InstalledCapacity.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>
<form name="frmInstalledCapacity" action="InstalledCapacity.php" method="post">
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
								$bxHeader="INSTALLED CAPACITY";
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
																				<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
																				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">												</td>
																				
																				<?} else{?>

																				
																				<td align="center">
																				<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
																				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">												</td>

																				<?}?>
																			</tr>
																			<input type="hidden" name="hidInstalledCapacityId" value="<?=$editInstalledCapacityId;?>">
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
																												<td class="fieldName" nowrap="nowrap">*Machinery</td>
																												<td class="listing-item">
																													<input name="machinery" type="text" id="machinery" size="28" value="<?=$machinery?>">
																												</td>
																											</tr>
																											<tr>
																												<td class="fieldName" nowrap="nowrap">Description</td>
																												<td class="listing-item"><textarea name="description" id="description"><?=$description?></textarea></td>
																											</tr>
																											<tr>
																												<td class="fieldName" nowrap="nowrap">*Type of Operation</td>
																												<td class="listing-item">
																														<select name="operationType" id="operationType">
																														<option value="">--Select--</option>
																														<?php
																														foreach ($operationTypeRecords as $otr) {
																															$operationTypeId = $otr[0];
																															$otName		= $otr[1];
																															$selected = ($operationTypeId==$operationType)?"selected":"";
																														?>
																														<option value="<?=$operationTypeId?>" <?=$selected?>><?=$otName?></option>
																														<?php
																															}
																														?>
																														</select>
																												</td>
																											</tr>
							
																										</table>
																									</TD>
																									<td>&nbsp;</td>
																									<td valign="top">
																										<table>
																											<tr>
																												<td colspan='2'>
																													<table>
																														<tr><td class="fieldNameLeft" nowrap="nowrap">*Capacity</td><td class="fieldNameLeft" nowrap="nowrap">*Unit</td><td class="fieldNameLeft" nowrap="nowrap">*Per</td></tr>
																														<tr>
																															<td class="listing-item">
																																<input name="capacity" type="text" id="capacity" size="5" value="<?=$capacity?>" style="text-align:right;" >
																															</td>
																															<td class="listing-item">
																																<select name="unitId" id="unitId">
																																<option value="">--Select--</option>
																																<?php
																																foreach ($stockUnitRecs as $sur) {
																																	$stkUnitId 	= $sur[0];
																																	$unitName	= $sur[1];
																																	$selected = ($stkUnitId==$unitId)?"selected":"";
																																?>
																																<option value="<?=$stkUnitId?>" <?=$selected?>><?=$unitName?></option>
																																<?php
																																	}
																																?>
																																</select>
																															</td>
																															<td class="listing-item" nowrap>
																																<select name="perVal" id="perVal">
																																<option value="">--Select--</option>
																																<?php
																																for ($pv=1; $pv<=24; $pv++) {
																																	$selected = ($pv==$unitId)?"selected":"";
																																?>
																																<option value="<?=$pv?>" <?=$selected?>><?=$pv?></option>
																																<?php
																																	}
																																?>
																																</select>&nbsp;HR
																															</td>
																														</tr>
																													</table>
																												</td>
																											</tr>
																											<tr>
																												<td colspan='2'>
																													<table align='center'>
																													<tr>
																														<td class="fieldNameLeft" nowrap="nowrap">*Monitor</td>
																														<td class="listing-item">
																															<select name="monitor" id="monitor">
																																<option value="">--Select--</option>
																																<option value="S" <?=$Sels?>>SINGLE</option>
																																<option value="M" <?=$Selm?>>MULTIPLE</option>
																															</select>
																														</td>
																													</tr>
																													<!--<tr>
																													<td class="fieldName" nowrap="nowrap">*Monitoring Parameters</td>
																													<td class="listing-item">
																														<select name="monitoringParameter" id="monitoringParameter">
																															<option value="">--Select--</option>
																															<?php
																																foreach ($monitoringParameterRecords as $mpr) {
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
																													</tr>	-->
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
																					<TD style="padding-left:10px;padding-right:10px; color: Maroon;" colspan="2" align="center" class="listing-item" nowrap>
																						Please confirm that the entries are in sequence.
																					</TD>
																				</tr>
																				<tr>
																					<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
																						<table>
																							<TR>
																								<TD>
																									<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblMonitorParam" class="newspaperType">
																										<tr align="center">
																											<th nowrap style="text-align:center;">*Head</th>
																											<th nowrap style="text-align:center; line-height:normal;">*Monitoring<br> Factor</th>	
																											<th nowrap style="text-align:center;">*Start</th>
																											<th nowrap style="text-align:center;">Stop</th>
																											<th nowrap style="line-height:normal; text-align:center;">Monitoring <br> Interval<br>(HR)</th>
																											<th nowrap style="line-height:normal; text-align:center;" id="seqFlagHCol">Sequential<br>To</th>
																											<th>&nbsp;</th>
																										</tr>
																										<? 
																										if($monitorParamRecSize>0)
																										{
																											$l=0;	
																											foreach($monitoringParamRec as $mpRec)
																											{	$smpStopY=''; $smpStopN=''; $smpStartY=''; $smpStartN='';
																												$mParamId=$mpRec[0];
																												$headName=$mpRec[1];
																												$monitoringParameter=$mpRec[2];
																												$smpStart=$mpRec[3];
																												($smpStart=='Y')? $smpStartY="selected":$smpStartN="selected";
																												$smpStop=$mpRec[4];
																												if($smpStop!='')
																												{ 
																													if($smpStop=='Y')
																													{ 
																														$smpStopY="selected";
																													} 
																													else 
																													{
																														$smpStopN="selected"; 
																													}
																												}
																												$monitoringInterval=$mpRec[5];
																												if($monitoringInterval>0)
																												{
																													$interval="display:display";
																												}
																												else
																												{
																													$interval="display:none";
																												}
																												$seqFlag=$mpRec[6];

																												
																											?>
																												<tr id="row_<?=$l?>" class="whiteRow" align="center">
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
																												</tr>
																											<?
																											$l++;
																											}
																										}
																										?>
																									</table>
																								</td>
																							</tr>
																							<tr>
																								<td><input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$monitorParamRecSize?>" readonly="true">
																								</td>
																							</tr>
																							<tr>
																								<TD>
																									<a href="###" id='addRow' onclick="javascript:addNewMonitorParamItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New </a>
																								</TD>
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
																			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
																			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">	
																		</td>
																	<? } else{?>
																		<td align="center">
																			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
																			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">
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
										<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$installedCapacityRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintInstalledCapacity.php',700,600);"><? }?></td>
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
								if( sizeof($installedCapacityRecords) > 0 )
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
																$nav.= " <a href=\"InstalledCapacity.php?pageNo=$page\" class=\"link1\">$page</a> ";
															}
													}
												if ($pageNo > 1)
													{
														$page  = $pageNo - 1;
														$prev  = " <a href=\"InstalledCapacity.php?pageNo=$page\"  class=\"link1\"><<</a> ";
													}
													else
													{
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

												if ($pageNo < $maxpage)
													{
														$page = $pageNo + 1;
														$next = " <a href=\"InstalledCapacity.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Machinery</th>
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description</th>
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Type of Operation</th>
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Capacity</th>
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Unit</th>
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Per</th>
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Monitor</th>
																				
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
										foreach($installedCapacityRecords as $mpr) {
											$i++;
											$installedCapacityId	=	$mpr[0];
											$machinery		=	stripSlash($mpr[1]);
											$description		=	stripSlash($mpr[2]);
											$typeOperation		=	stripSlash($mpr[3]);
											$capacity		=	stripSlash($mpr[4]);
											$unit		=	stripSlash($mpr[5]);
											$per			=	stripSlash($mpr[6]);
											$Monitor		=	stripSlash($mpr[7]);
											$active		=	$mpr[11];
											($Monitor=='S')? $MonitorNm="Single" : $MonitorNm="Multiple";
										?>
										<tr>
											<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$installedCapacityId;?>" class="chkBox"></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$machinery;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$typeOperation;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$capacity;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$per;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$MonitorNm;?></td>
											<? if($edit==true){?>
											  <td class="listing-item" width="45" align="center"><? if($active!=1) { ?>	<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$installedCapacityId;?>,'editId');"><? } ?></td>
											  <? }?>

											<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
											<?php 
											if ($confirm==true){	
												if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$installedCapacityId;?>,'confirmId');" >
												<?php } else if ($active==1){ ?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$installedCapacityId;?>,'rlconfirmId');" >
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
																	$nav.= " <a href=\"InstalledCapacity.php?pageNo=$page\" class=\"link1\">$page</a> ";
																}
														}
													if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"InstalledCapacity.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

													if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"InstalledCapacity.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$installedCapacityRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintInstalledCapacity.php',700,600);"><? }?></td>
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
	</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<?php if(sizeof($monitoringParamRec)>0)
{
?>
<script>
fieldId='<? echo sizeof($monitoringParamRec);?>';
</script>
<?php
}
?>
<script>
	function addNewMonitorParamItem()
	{
		//alert("hii");
		addNewMonitorParam('tblMonitorParam','','');
		validParam();		
	}
</script>
<?
if($addMode)
{
?>
<script>
window.load=addNewMonitorParamItem();
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