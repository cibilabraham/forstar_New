<?php 
	require 'include/include.php';
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"];
	
	/*----------  Current Date  -----------*/
	$currentDate = date("Y-m-d");

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
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
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	//Page Offset settings
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit;
    //echo $pageNo;	
	//echo $offset;
	
	//List All Descriptions
	$allProcess = $processMasterObj->fetchProcess($offset,$limit);
	$recordSize = sizeof($allProcess);
	//print_r($allProcess);
	
	
	//Pagination Settings
	$numRows = sizeof($processMasterObj->fetchAllProcess());
	$maxpage = ceil($numRows/$limit);
	
	//Add New Process Master
	if($p['btnAddNew']!="")
	{
		$addMode = true;
	}
	
	//Add Process Master Details to Database
	if($p['btnAdd']!="")
	{
		$name 				= $p['name'];
		$description 		= $p['description'];
		$water 				= $p['water'];
		$diesel 			= $p['diesel'];
		$electricity 		= $p['electricity'];
		$gas 				= $p['gas'];
		
		$processDetails = $processMasterObj->findProcessDetails($name);
		if($processDetails == "")
		{
			$addNewProcess = $processMasterObj->addNewProcessDetails($name, $description, $water, $diesel, $electricity, $gas, $userId, $currentDate);
		}
		
		if($addNewProcess)
		{
			$sessObj->createSession("displayMsg",$msg_addProcessMasterDetail);
			$sessObj->createSession("nextPage",$url_afterAddProcessMaster.$selection);
		}
		else
		{
			$addMode = true;
			$err = $msg_failAddProcessMasterDetail;
		}
	}
	
	//Edit Process Master
	if($p['editId']!="")
	{
		$editMode = true;
		$editId = $p['editId'];
		if($editId!="")
		{
			$getProcessMaster = $processMasterObj->getProcessDetails($editId);
		}
		$processMasterId = $getProcessMaster[0];
		$processMasterName = $getProcessMaster[1];
		$processMasterDesc = $getProcessMaster[2];
		$processMasterWater	= $getProcessMaster[3];
		$processMasterDiesel = $getProcessMaster[4];
		$processMasterElectrcty = $getProcessMaster[5];
		$processMasterGas = $getProcessMaster[6];
		
		$oldProcessRatelist = $processMasterObj->getOldProcessRate($processMasterId, $processMasterName);
		/*echo "<pre>";
		print_r($oldProcessRatelist);
		echo "</pre>";
		die(); */
		
	}
	
	//Update Process Master
	if($p['btnSaveChange']!="")
	{
		$updateId 				= $p['hiddenId'];
		if($updateId!="")
		{
			$compareProcessDetail   = $processMasterObj->getProcessDetails($updateId);
		}
		$id            = 	$compareProcessDetail[0];
		$name          = 	$compareProcessDetail[1];
		$desc          = 	$compareProcessDetail[2];
		$water         = 	$compareProcessDetail[3];
		$diesel        = 	$compareProcessDetail[4];
		$electricity   = 	$compareProcessDetail[5];
		$gas           = 	$compareProcessDetail[6];
		
		$updateName 			= $p['name'];
		$updateDesc 			= $p['description'];
		$updateWater 			= $p['water'];
		$updateDiesel 			= $p['diesel'];
		$updateElectricity 		= $p['electricity'];
		$updateGas 				= $p['gas'];
		
		//echo $updateName." ".$updateDesc." ".$updateWater." ".$updateDiesel." ".$updateElectricity." ".$updateGas;
		//echo $name." ".$water." ".$diesel." ".$electricity." ".$gas." ".$name;
		//die();
		
		if($name == $updateName && $water == $updateWater && $diesel == $updateDiesel && $electricity == $updateElectricity && $gas == $updateGas)
		{
			$editMode = true;
			$err = $msg_failProcessMasterUpdate;
		}
		else
		{
			$updateProcess = $processMasterObj->addNewProcessDetails($updateName, $updateDesc, $updateWater, $updateDiesel, $updateElectricity, $updateGas, $userId, $currentDate);
			if($updateProcess)
			{
				$sessObj->createSession("displayMsg",$msg_succProcessMasterUpdate);
				$sessObj->createSession("nextPage",$url_afterAddProcessMaster.$selection);
			}
			else
			{
				$editMode = true;
				$err = $msg_failProcessMasterUpdate;
			}
		}
	   
	}
	
	//Heading Section
	if($editMode)
	{
		$heading = $label_editProcessMaster;
	}
	else 
	{
		$heading = $label_addProcessMaster;
	}
	
	//Cancel Action
	if($p['btnCancel']!="")
	{
		$addMode = false;
		$editMode = false;
	}
	
	//Delete Process Master
	if ( $p["cmdDelete"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$processDelId	=	$p["delId_".$i];
			
			//Check if this Process Method is using in Production Matrix section.
			$checkProcessExist = $productionMatrixObj->getEntryExist($processDelId);
			
			if(sizeof($checkProcessExist) == 0)
			{
				if ($processDelId!="") 
				{
					// Need to check the selected Category is link with any other process
					$deleteProcess = $processMasterObj->deleteProcessDetails($processDelId);
				}
			}
		}
		if ($deleteProcess) {
			$sessObj->createSession("displayMsg",$msg_succDelProcessMaster);
			$sessObj->createSession("nextPage",$url_afterDelProcessMaster.$selection);
		} else {
			$errDel	=	$msg_failDelProcessMaster;
		}
		$deleteProcess	=	false;
	}
	
	//Confirm Process Master
	if($p['btnPending']!="")
	{
		$confirmId = $p['confirmId'];
		if($confirmId!="")
		{
			$processMasterConfirm = $processMasterObj->processConfirmation($confirmId);
		}
		if($processMasterConfirm)
		{
			$sessObj->createSession("displayMsg",$msg_succConfirmProcessMaster);
			$sessObj->createSession("nextPage",$url_afterConfirmProcessMaster.$selection);
		}
		else 
		{
			$errConfirm	=	$msg_failConfirm;
		}
	}
	
	//Release Confirmation
	if($p['btnConfirm']!="")
	{
		$releaseId = $p['confirmId'];
		if($releaseId!="")
		{
			$processMasterRelease = $processMasterObj->processReleaseConfirmation($releaseId);
		}
		if($processMasterRelease)
		{
			$sessObj->createSession("displayMsg",$msg_succRelConfirmProcessMaster);
			$sessObj->createSession("nextPage",$url_afterConfirmProcessMaster.$selection);
		}
		else 
		{
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		
	}
	
	//echo $userId;
	//echo $currentDate;
	
	$ON_LOAD_PRINT_JS	= "libjs/ProcessMaster.js";
	
	//require("template/topLeftNav.php");
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
	
	
?>
<form name="frmProcessMaster" action="ProcessMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<TD height="10"></TD>
		</tr>
		<?php 
		if($err!="" )
		{
		?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?php 
		}
		?>
		<tr>
			<td align="center">
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Process Master Details";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
					<tr>
						<td colspan="3" align="center">
							<table>
								<?php
								if($editMode || $addMode) 
								{  ?>
								<tr>
									<td>
										<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
											<tr>
												<td>
												<?php 
													$entryHead = $heading;
													require("template/rbTop.php");
												?>
												<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
													<tr>
														<td width="1" ></td>
														<td colspan="2" >
															<table cellpadding="0"  width="92%" cellspacing="0" border="0" align="center">
																<tr>
																	<td colspan="2" height="10" ></td>
																</tr>
																<tr>
																<?php 
																if($editMode)
																{
																?>
																	<td colspan="2" align="center">
																		 <input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('ProcessMaster.php');">&nbsp;&nbsp;
																		 <input type="submit" name="btnSaveChange" id="btnSaveChange" class="button" value="Save Changes" onClick="return validateProcessMaster(document.frmProcessMaster);">
																	</td>
																<?php				
																 }
																else
																{
																?>
																	<td colspan="2" align="center">
																		<input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('ProcessMaster.php');">&nbsp;&nbsp;
																		<input type="submit" name="btnAdd" id="btnAdd" class="button" value="Add" onClick="return validateProcessMaster(document.frmProcessMaster);">
																	</td>
																<?php	
																}
																?>
																<input type="hidden" name="hiddenId" id="hiddenId" value="<?=$processMasterId;?>">
																</tr>
																<tr>
																	<td colspan="2"  height="10" ></td>
																</tr>
																<tr>
																	<TD colspan="2" nowrap align="center">
																		<table>
																			<TR>
																				<TD valign="top">
																					<table>
																						<tr>
																							<td class="fieldName" nowrap >*Name</td>
																							<td><input type="text" name="name" id="name" size="15" value="<?=$processMasterName;?>" <? if($editMode) {?> readonly <?} ?>></td>
																						</tr>
																						<tr>
																							<td class="fieldName" nowrap >Description</td>
																							<td><textarea name="description" id="description" value=""><?=$processMasterDesc;?></textarea></td>
																						</tr>
																					</table>
																				</td>
																				<td>&nbsp;</td>
																				<TD valign="top">
																					<table>
																						<tr>
																							<td class="fieldName" nowrap >Water</td>
																							<td><input type="text" name="water" id="water" size="7" value="<?=$processMasterWater;?>" autocomplete="off" ></td>
																						</tr>
																						<tr>
																							<td class="fieldName" nowrap >Diesel</td>
																							<td><input type="text" name="diesel" id="diesel" size="7" value="<?=$processMasterDiesel;?>" autocomplete="off" ></td>
																						</tr>
																						<tr>
																							<td class="fieldName" nowrap >Electricity</td>
																							<td><input type="text" name="electricity" id="electricity" size="7" value="<?=$processMasterElectrcty;?>" autocomplete="off" ></td>
																						</tr>
																						<tr>
																							<td class="fieldName" nowrap >Gas</td>
																							<td><input type="text" name="gas" id="gas" size="7" value="<?=$processMasterGas;?>" autocomplete="off" ></td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<? if($editMode)
																			{
																			?>
																			<tr>
																				<td colspan="2" nowrap >		
																			<!-- 	Last Five Rate List Starts Here -->
																				<?php
																					if (sizeof($oldProcessRatelist)>0) {
																				?>	
																				<table width="50%" cellpadding="0" cellspacing="3">
																				<TR>
																				<TD>
																				<?php
																					$entryHead = "Last 5 Rate List";
																					$rbTopWidth = "";
																					require("template/rbTop.php");
																				?>
																				<table cellpadding="2"  cellspacing="1" border="0" align="center" id="newspaper-b1">
																					<thead>
																					<tr align="center">
																						<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;" nowrap>Id</th>
																						<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;" nowrap>Revised Date</th>
																						<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;">Water</th>
																						<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;">Diesel</th>
																						<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;">Electricity</th>
																						<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;">Gas</th>
																					</tr>
																					</thead>
																					<tbody>
																					<?php
																						foreach($oldProcessRatelist as $oldRate) 
																						{
																							$revId = $oldRate[0];
																							$revWater = $oldRate[2];
																							$revDiesel = $oldRate[3];
																							$revElect = $oldRate[4];
																							$revGas = $oldRate[5];
																							$revDate = dateFormat($oldRate[6]);
																					?>
																					<tr>
																						<TD class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$revId?></TD>
																						<TD class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$revDate?></TD>
																						<TD class="listing-item" align="right" style="padding-left:5px;padding-right:5px;"><?=$revWater?></TD>
																						<TD class="listing-item" align="right" style="padding-left:5px;padding-right:5px;"><?=$revDiesel?></TD>
																						<TD class="listing-item" align="right" style="padding-left:5px;padding-right:5px;"><?=$revElect?></TD>
																						<TD class="listing-item" align="right" style="padding-left:5px;padding-right:5px;"><?=$revGas?></TD>
																					</tr>
																					<?php
																						}
																					?>
																					</tbody>
																				</table>
																				<?php
																					require("template/rbBottom.php");
																				?>
																				</TD>
																				</TR>
																				</table>	
																				<?php
																					} 
																					else
																					{
																				?>
																					<table>
																					<TR>
																						<td class="err1" nowrap style="line-height:normal;font-size:11px;">No old rate list found.</td>
																					</TR>
																					</table>
																				<? }?>
																					</td>
																			</tr>
																			<?
																			}
																			?>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td colspan="2"  height="10" ></td>
																</tr>
																<tr>
																<?php 		  
																if($editMode)
																{
																?>
																	<td colspan="2" align="center">
																		<input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('ProcessMaster.php');">&nbsp;&nbsp;
																		<input type="submit" name="btnSaveChange" id="btnSaveChange" class="button" value="Save Changes" onClick="return validateProcessMaster(document.frmProcessMaster);">
																	</td>
																<?php				
																 }
																else
																{
																?>
																	<td colspan="2" align="center">
																		 <input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('ProcessMaster.php');">&nbsp;&nbsp;
																		 <input type="submit" name="btnAdd" id="btnAdd" class="button" value="Add" onClick="return validateProcessMaster(document.frmProcessMaster);">
																	</td>
																<?php	
																}
																?>
																</tr>
																<tr>
																	<td colspan="2"  height="10" ></td>
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
									</td>
								</tr>
								<?php   
								}
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td height="10" align="center" ></td>
					</tr>
					<tr>
						<td colspan="3" height="10" ></td>
					</tr>
										<tr>	
						<td colspan="3">
							<table cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td>
										<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$recordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" name="btnAddNew" value="AddNew" class="button"><? } ?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProcessMaster.php',700,600);"><? }?>
									</td>
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
						<td colspan="2" style="padding-left:10px; padding-right:10px;" >
							<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
							<?php
							if($recordSize > 0)
							{
								$i = 0;
								?>
								<thead>
								<? if($maxpage>1){?>
									<tr>
										<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
										<div align="right">
										<?php
										 $nav  = '';
										for ($page=1; $page<=$maxpage; $page++) {
											if ($page==$pageNo) {
													$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
											} else {
													$nav.= " <a href=\"ProcessMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
												//echo $nav;
											}
										}
										if ($pageNo > 1) {
											$page  = $pageNo - 1;
											$prev  = " <a href=\"ProcessMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
										} else {
											$prev  = '&nbsp;'; // we're on page one, don't print previous link
											$first = '&nbsp;'; // nor the first page link
										}

										if ($pageNo < $maxpage) {
											$page = $pageNo + 1;
											$next = " <a href=\"ProcessMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
										} else {
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
										<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Description</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Water</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Diesel</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Electricity</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Gas</th>
										<? if($edit==true){?>
										<th class="listing-head">&nbsp;</th>
										<? }?>
										<? if($confirm==true){?>
										<th class="listing-head">&nbsp;</th>
										<? }?>
									</tr>
								</thead>
								<tbody>
									<?php
									
									foreach($allProcess as $process)
									{
										$i++;
										$id = $process[0];
										$name = $process[1];
										$description = $process[2];
										$water = $process[3];
										$diesel = $process[4];
										$electricity = $process[5];
										$gas = $process[6];
										$active = $process[7];
									?>	
										<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
											<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$id;?>"></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$water;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$diesel;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$electricity;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$gas;?></td>
											<?php
											if($edit==true) 
											{
											?>
											<td class="listing-item" width="60" align="center">
											<?php
												if($active!=1)
												{
												?>
												<input type="submit" name="btnEditActive" value="Edit" onClick="assignValue(this.form,<?=$id;?>,'editId')">
												</td>
												 <?php 
												}
											}
											if ($confirm==true)
											{
											?>
											<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
											<?php
												if($confirm==true) 
												{ 
												if($active==0) 
												{
												?>
												<input type="submit" name="btnPending" value="Pending" onclick="assignValue(this.form,<?=$id;?>,'confirmId')">
												<?php 
												}
												else if($active==1) 
												{ 
												?>
												<input type="submit" name="btnConfirm" value="Confirmed" onClick="assignValue(this.form,<?=$id;?>,'confirmId')">
												<?php  
												} 
												} 
											?>
											</td>
											<?php
											}
											?>
										</tr>
									<?php 
									}
									?>
									<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i;?>">
									<input type="hidden" name="editId" id="editId" value="">
									<input type="hidden" name="confirmId" id="confirmId" value="">
									<?php
									if($maxpage>1){?>
									<tr>
										<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
										<div align="right">
										<?php
										 $nav  = '';
										for ($page=1; $page<=$maxpage; $page++) {
											if ($page==$pageNo) {
													$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
											} else {
													$nav.= " <a href=\"ProcessMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
												//echo $nav;
											}
										}
										if ($pageNo > 1) {
											$page  = $pageNo - 1;
											$prev  = " <a href=\"ProcessMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
										} else {
											$prev  = '&nbsp;'; // we're on page one, don't print previous link
											$first = '&nbsp;'; // nor the first page link
										}

										if ($pageNo < $maxpage) {
											$page = $pageNo + 1;
											$next = " <a href=\"ProcessMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
										} else {
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
									<? 
									}
									} 
									else
									{
									?>
									<tr>
										<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>		
									<?php
									}
									?>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>	
						<td colspan="3">
							<table cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td>
										<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$recordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" name="btnAddNew" value="AddNew" class="button"><? } ?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProcessMaster.php',700,600);"><? }?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5" ></td>
					</tr>
				</table>
				<?php
					include "template/boxBR.php";
				?>
				</td>
			</tr>
			</table>
			</td>
		</tr>	
		<tr>
			<td height="10"></td>
		</tr>
		<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
<? if ($iFrameVal=="") 
{
?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	ensureInFrameset(document.frmPackingMatrix);
	//-->
	</script>
<? 
}
?>
</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>
