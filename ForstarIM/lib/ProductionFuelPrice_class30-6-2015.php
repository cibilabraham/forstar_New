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
		//Other Cost
		$dieselConsumptionOfBoilerUnit = $p["dieselConsumptionOfBoilerUnit"];
		$dieselCostPerLitre	=	$p["dieselCostPerLitre"];
		$electricConsumptionPerDayUnit = $p["electricConsumptionPerDayUnit"];
		$electricCostPerUnit	=	$p["electricCostPerUnit"];
		$waterConsumptionPerRetortBatchUnit = $p["waterConsumptionPerRetortBatchUnit"];
		$generalWaterConsumptionPerDayUnit = $p["generalWaterConsumptionPerDayUnit"];
		$costPerLitreOfWater	=	$p["costPerLitreOfWater"];		
		$gasPerCylinderPerDay	=	$p["gasPerCylinderPerDay"];
		$costOfCylinder		=	$p["costOfCylinder"];
		//echo "hii".$generalWaterConsumptionPerDayUnit.'--'.$gasPerCylinderPerDay.'--'.$costOfCylinder;
		//die();
		if($dieselConsumptionOfBoilerUnit|| $dieselCostPerLitre || $electricConsumptionPerDayUnit || $electricCostPerUnit || $waterConsumptionPerRetortBatchUnit || $generalWaterConsumptionPerDayUnit || $costPerLitreOfWater || $gasPerCylinderPerDay ||$costOfCylinder)
		{
			$upFuelPrice=$productionFuelPriceObj->updateProductionFuelPrice($dieselConsumptionOfBoilerUnit,$dieselCostPerLitre,$electricConsumptionPerDayUnit,$electricCostPerUnit, $waterConsumptionPerRetortBatchUnit,$generalWaterConsumptionPerDayUnit,$costPerLitreOfWater,$gasPerCylinderPerDay,$costOfCylinder,$userId);

			if($upFuelPrice)
			{
				$sessObj->createSession("displayMsg",$msg_succUpdateProductionFuelPrice);
				$sessObj->createSession("nextPage",$url_afterUpdateProductionFuelPrice.$selection);
			}
			else 
			{
				$addMode	=	true;
				$err		=	$msg_failUpdateProductionFuelPrice;
			}
			$upFuelPrice		=	false;
		}
	}	
		
	#List all Production Working Hours
	$upProductionCost=$productionFuelPriceObj->getProductionFuelPrice();
	$dieselConsumptionOfBoilerUnit=$upProductionCost[1];
	$dieselCostPerLitre=$upProductionCost[2];
	$electricConsumptionPerDayUnit=$upProductionCost[3];
	$electricCostPerUnit=$upProductionCost[4];
	$waterConsumptionPerRetortBatchUnit=$upProductionCost[5];
	$generalWaterConsumptionPerDayUnit=$upProductionCost[6];
	$costPerLitreOfWater=$upProductionCost[7];
	$gasPerCylinderPerDay=$upProductionCost[8];
	$costOfCylinder=$upProductionCost[9];
	//echo $maintenanceCost;
	$ON_LOAD_PRINT_JS = "libjs/productionfuelprice.js";
	require("template/btopLeftNav.php");
?>
<form name="frmProductionFuelPrice" action="ProductionFuelPrice.php" method="post">
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
							$bxHeader = "Production Fuel Price";
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
																				<input type="hidden" name="hidProductionMatrixMasterId" id="hidProductionMatrixMasterId" value="<?=$editProdnMxMasterRecId;?>">
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
																											<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">FUEL PRICE</Th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Diesel Consumption of Boiler/Hour in Litre</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="dieselConsumptionOfBoilerUnit" id="dieselConsumptionOfBoilerUnit" size="5" style="text-align:right" value="<?=($dieselConsumptionOfBoilerUnit!="")?number_format($dieselConsumptionOfBoilerUnit,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Diesel Cost per Litre (Escalation 5%)</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="dieselCostPerLitre" id="dieselCostPerLitre" size="5" style="text-align:right" value="<?=($dieselCostPerLitre!="")?number_format($dieselCostPerLitre,2,'.',''):""?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Electricity Consumption per Day in Units</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="electricConsumptionPerDayUnit" id="electricConsumptionPerDayUnit" size="5" style="text-align:right" value="<?=($electricConsumptionPerDayUnit!="")?number_format($electricConsumptionPerDayUnit,2,'.',''):""?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Electricity Cost per Unit</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="electricCostPerUnit" id="electricCostPerUnit" size="5" style="text-align:right" value="<?=($electricCostPerUnit!="")?number_format($electricCostPerUnit,2,".",""):"";?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Water consumption per Retort Batch</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="waterConsumptionPerRetortBatchUnit" id="waterConsumptionPerRetortBatchUnit" size="5" style="text-align:right" value="<?=($waterConsumptionPerRetortBatchUnit!="")?number_format($waterConsumptionPerRetortBatchUnit,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">General Water consumption per Day</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="generalWaterConsumptionPerDayUnit" id="generalWaterConsumptionPerDayUnit" size="5" style="text-align:right" value="<?=($generalWaterConsumptionPerDayUnit!="")?number_format($generalWaterConsumptionPerDayUnit,2,'.',''):""?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Cost per litre of Water</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;">
																											<input type="text" name="costPerLitreOfWater" id="costPerLitreOfWater" size="5" style="text-align:right" value="<?=($costPerLitreOfWater!="")?number_format($costPerLitreOfWater,2,".",""):""?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Gas per 19Kg Cylinder per Day</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="gasPerCylinderPerDay" id="gasPerCylinderPerDay" size="5" style="text-align:right" value="<?=($gasPerCylinderPerDay!="")?number_format($gasPerCylinderPerDay,2,".",""):""?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Cost of Cylinder</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="costOfCylinder" id="costOfCylinder" size="5" style="text-align:right" value="<?=($costOfCylinder!="")?number_format($costOfCylinder,2,".",""):""?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<input type="hidden" name="workingHoursSize" id="workingHoursSize" size="5" value="<?=$i?>">
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