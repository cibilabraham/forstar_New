<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err			= "";
	$errDel			= "";	
	$editProdnMxMasterRecId	= "";
	$productionMatrixMasterId = "";
	$noRec			= "";	
	$editMode	= true;
	$addMode	= false;
	
	$recUptd   = false;
	
	
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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	
	
	
	# Update / Insert a Record
	if ($p["cmdSaveChange"]!="") {		
		$hidProductionWorkingHoursId=$p["hidProductionWorkingHoursId"];
		$noOfHoursShift=$p["noOfHoursShift"];
		$noOfShift=$p["noOfShift"];
		$noOfGravyCookers=$p["noOfGravyCookers"];
		$noOfRetorts=$p["noOfRetorts"];
		$noOfSealingMachines=$p["noOfSealingMachines"];
		$noOfPouchesSealedPerminute=$p["noOfPouchesSealedPerminute"];
		$noOfMinutesForSealing=$p["noOfMinutesForSealing"];
		$noOfDaysInYear=$p["noOfDaysInYear"];
		$noOfWorkingDaysInMonth=$p["noOfWorkingDaysInMonth"];
		$noOfHoursPerDay=$p["noOfHoursPerDay"];
		$noOfMinutesPerHour=$p["noOfMinutesPerHour"];
		
		if($hidProductionWorkingHoursId!="")
		{
			$upWorkHours=$productionWorkingHoursObj->updateWorkingHours($hidProductionWorkingHoursId,$noOfHoursShift,$noOfShift,$noOfGravyCookers,$noOfRetorts,$noOfSealingMachines,$noOfPouchesSealedPerminute,$noOfMinutesForSealing,$noOfDaysInYear,$noOfWorkingDaysInMonth,$noOfHoursPerDay,$noOfMinutesPerHour,$userId);
		}
		else
		{
			$upWorkHours=$productionWorkingHoursObj->addWorkingHours($noOfHoursShift,$noOfShift,$noOfGravyCookers,$noOfRetorts,$noOfSealingMachines,$noOfPouchesSealedPerminute,$noOfMinutesForSealing,$noOfDaysInYear,$noOfWorkingDaysInMonth,$noOfHoursPerDay,$noOfMinutesPerHour,$userId);
		}
		if($upWorkHours)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdateProductionWorkingHours);
			$sessObj->createSession("nextPage",$url_afterUpdateProductionWorkingHours.$selection);
		}
		else {
				$addMode	=	true;
				$err		=	$msg_failUpdateProductionWorkingHours;
			}
		$upWorkHours		=	false;
	}	
		
	#List all Production Working Hours
	
	$prdnWorkingHoursRec = $productionWorkingHoursObj->getProductionWorkingHours();
	$productionWorkingHoursId=$prdnWorkingHoursRec[0];
	$noOfHoursShift=$prdnWorkingHoursRec[1];
	$noOfShift=$prdnWorkingHoursRec[2];
	$noOfGravyCookers=$prdnWorkingHoursRec[3];
	$noOfRetorts=$prdnWorkingHoursRec[4];
	$noOfSealingMachines=$prdnWorkingHoursRec[5];
	$noOfPouchesSealedPerminute=$prdnWorkingHoursRec[6];
	$noOfMinutesForSealing=$prdnWorkingHoursRec[7];
	$noOfDaysInYear=$prdnWorkingHoursRec[8];
	$noOfWorkingDaysInMonth=$prdnWorkingHoursRec[9];
	$noOfHoursPerDay=$prdnWorkingHoursRec[10];
	$noOfMinutesPerHour=$prdnWorkingHoursRec[11];

	$ON_LOAD_PRINT_JS = "libjs/productionworkinghours.js";
	require("template/btopLeftNav.php");
?>
<form name="frmProductionWorkingHours" action="ProductionWorkingHours.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<tr>
			<TD height="10"></TD>
		</tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>	
		<tr>
			<td align="center">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
						<?php	
							$bxHeader = "Production Matrix Master";
							include "template/boxTL.php";
						?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<Table width="60%">
										<?
											if ( $editMode || $addMode) {
										?>
											<tr>
												<td colspan="3" align="center">
												</td>
											</tr>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
														<tr>
															<td>
																<!-- Form fields start -->
																<?php							
																	$entryHead = "";
																	require("template/rbTop.php");
																?>
																<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																	<tr>
																		<td width="1" ></td>
																		<td colspan="2" >
																			<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																				<tr>
																					<td colspan="2" align="center">
																						<? if($edit==true){?>&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " >
																					</td>				
																					<?} else{?>
																					<td align="center">&nbsp;&nbsp;</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidProductionWorkingHoursId" id="hidProductionWorkingHoursId" value="<?=$productionWorkingHoursId;?>">
																				<input type="hidden" name="newRateList" id="newRateList" value="">
																				<tr>
																					<td colspan="2" nowrap class="fieldName" height="5"></td>
																				</tr>
																				<tr>
																					<td colspan="2" nowrap style="padding-left:10px;padding-right:10px;">
																						<table align="center">
																							<tr>
																								<td nowrap >
																									<table cellpadding="1" cellspacing="1" id="newspaper-b1">
																										<TR>
																											<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">WORKING DURATION</Th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
																										</TR>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Hours/Shift</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfHoursShift" id="noOfHoursShift" value="<?=$noOfHoursShift?>" size="10"/></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Shifts</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfShift" id="noOfShift" value="<?=$noOfShift?>" size="10"/></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Gravy Cookers</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfGravyCookers" id="noOfGravyCookers" value="<?=$noOfGravyCookers?>" size="10"/></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Retorts</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfRetorts" id="noOfRetorts" value="<?=$noOfRetorts?>" size="10" /></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Sealing Machines</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfSealingMachines" id="noOfSealingMachines" value="<?=$noOfSealingMachines?>" size="10" /></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of pouches sealed/minute/Sealing Machine</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfPouchesSealedPerminute" id="noOfPouchesSealedPerminute" value="<?=$noOfPouchesSealedPerminute?>" size="10" /></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Minutes for Sealing to start after Filling 	</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfMinutesForSealing" id="noOfMinutesForSealing" value="<?=$noOfMinutesForSealing?>" size="10" /></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Days in Year</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfDaysInYear" id="noOfDaysInYear" value="<?=$noOfDaysInYear?>" size="10"/></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Working Days in Month</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfWorkingDaysInMonth" id="noOfWorkingDaysInMonth" value="<?=$noOfWorkingDaysInMonth?>" size="10"/></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Hours per Day</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfHoursPerDay" id="noOfHoursPerDay" value="<?=$noOfHoursPerDay?>" size="10"/></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of Minutes per Hour</td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfMinutesPerHour" id="noOfMinutesPerHour" value="<?=$noOfMinutesPerHour?>" size="10"/></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										
																										
																									</table>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="4"  height="10" ></td>
																				</tr>
																				<tr>
																					<td colspan="2" align="center">
																					<? if($edit==true){?>&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " >
																					<? }?>
																					</td>
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
												<!-- Form fields end   -->
												</td>
											</tr>	
											<?
											}
											
											# Listing LandingCenter Starts
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td height="10" align="center" ></td>
								</tr>
								<input type='hidden' name="hidMPCRateList" value="<?=$mpcRateList?>">
								<input type='hidden' name="hidFCCRateList" value="<?=$fccRateList?>">
								<input type='hidden' name="hidMCRateList" value="<?=$mcRateList?>">
								<input type='hidden' name="hidTCRateList" value="<?=$tcRateList?>">
								<tr>
									<td height="10"></td>
								</tr>	
								<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
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
<? 
	if ($iFrameVal=="") { 
?>
	<!--script language="javascript">	
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
	ensureInFrameset(document.frmProductionMatrixMaster);	
	</script-->
<? 
	}
?>
</form>
	<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
	?>