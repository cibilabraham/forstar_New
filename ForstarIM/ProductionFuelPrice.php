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
		$productionFuelId = $p["hidproductionFuelId"];
		$dieselRatePerUnit = $p["dieselRatePerUnit"];
		$dieselConsumptionBatch	=	$p["dieselConsumptionBatch"];
		$electricityRatePerUnit = $p["electricityRatePerUnit"];
		$electricityConsumptionPerDay	=	$p["electricityConsumptionPerDay"];
		$waterProcessingRatePerUnit = $p["waterProcessingRatePerUnit"];
		$waterProcessingConsumptionBatch = $p["waterProcessingConsumptionBatch"];
		$waterGeneralRatePerUnit	=	$p["waterGeneralRatePerUnit"];		
		$waterGeneralConsumptionPerDay	=	$p["waterGeneralConsumptionPerDay"];
		$gasRatePerUnit		=	$p["gasRatePerUnit"];
		$gasPerDay		=	$p["gasPerDay"];
		//echo "hii".$generalWaterConsumptionPerDayUnit.'--'.$gasPerCylinderPerDay.'--'.$costOfCylinder;
		
		if($dieselRatePerUnit|| $dieselConsumptionBatch || $electricityRatePerUnit || $electricityConsumptionPerDay || $waterProcessingRatePerUnit || $waterProcessingConsumptionBatch || $waterGeneralRatePerUnit || $waterGeneralConsumptionPerDay ||$gasRatePerUnit || $gasPerDay)
		{
			if($productionFuelId!="")
			{
				$upFuelPrice=$productionFuelPriceObj->updateProductionFuelPrice($productionFuelId,$dieselRatePerUnit,$dieselConsumptionBatch,$electricityRatePerUnit,$electricityConsumptionPerDay, $waterProcessingRatePerUnit,$waterProcessingConsumptionBatch,$waterGeneralRatePerUnit,$waterGeneralConsumptionPerDay,$gasRatePerUnit,$gasPerDay,$userId);
				
			}
			else
			{
				$upFuelPrice=$productionFuelPriceObj->addProductionFuelPrice($dieselRatePerUnit,$dieselConsumptionBatch,$electricityRatePerUnit,$electricityConsumptionPerDay, $waterProcessingRatePerUnit,$waterProcessingConsumptionBatch,$waterGeneralRatePerUnit,$waterGeneralConsumptionPerDay,$gasRatePerUnit,$gasPerDay,$userId);	
			}

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
	$productionFuelId=$upProductionCost[0];
	$dieselRatePerUnit=$upProductionCost[1];
	$dieselConsumptionBatch=$upProductionCost[2];
	$electricityRatePerUnit=$upProductionCost[3];
	$electricityConsumptionPerDay=$upProductionCost[4];
	$waterProcessingRatePerUnit=$upProductionCost[5];
	$waterProcessingConsumptionBatch=$upProductionCost[6];
	$waterGeneralRatePerUnit=$upProductionCost[7];
	$waterGeneralConsumptionPerDay=$upProductionCost[8];
	$gasRatePerUnit=$upProductionCost[9];
	$gasPerDay=$upProductionCost[10];
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
																				<input type="hidden" name="hidproductionFuelId" id="hidproductionFuelId" value="<?=$productionFuelId;?>">
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
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">RATE/UNIT</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CONSUMPTION/BATCH</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CONSUMPTION/DAY</th>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Diesel</TD>
																											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">litre</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="dieselRatePerUnit" id="dieselRatePerUnit" size="5" style="text-align:right" value="<?=($dieselRatePerUnit!=0)?number_format($dieselRatePerUnit,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																											</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="dieselConsumptionBatch" id="dieselConsumptionBatch" size="5" style="text-align:right" value="<?=($dieselConsumptionBatch!=0)?number_format($dieselConsumptionBatch,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Electricity</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="electricityRatePerUnit" id="electricityRatePerUnit" size="5" style="text-align:right" value="<?=($electricityRatePerUnit!=0)?number_format($electricityRatePerUnit,2,'.',''):""?>"  onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="electricityConsumptionPerDay" id="electricityConsumptionPerDay" size="5" style="text-align:right" value="<?=($electricityConsumptionPerDay!=0)?number_format($electricityConsumptionPerDay,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																											
																											
																										</TR>
																										
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Water-Processing</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="waterProcessingRatePerUnit" id="waterProcessingRatePerUnit" size="5" style="text-align:right" value="<?=($waterProcessingRatePerUnit!=0)?number_format($waterProcessingRatePerUnit,3,'.',''):""?>"  onkeypress="return isNumber (event);" onblur="copyData();"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="waterProcessingConsumptionBatch" id="waterProcessingConsumptionBatch" size="5" style="text-align:right" value="<?=($waterProcessingConsumptionBatch!=0)?number_format($waterProcessingConsumptionBatch,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																											</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Water-General</TD>
																											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">litre</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="waterGeneralRatePerUnit" id="waterGeneralRatePerUnit" size="5" style="text-align:right" value="<?=($waterGeneralRatePerUnit!=0)?number_format($waterGeneralRatePerUnit,3,'.',''):""?>"  onkeypress="return isNumber (event);"></td>
																											</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="waterGeneralConsumptionPerDay" id="waterGeneralConsumptionPerDay" size="5" style="text-align:right" value="<?=($waterGeneralConsumptionPerDay!=0)?number_format($waterGeneralConsumptionPerDay,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Gas</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="gasRatePerUnit" id="gasRatePerUnit" size="5" style="text-align:right" value="<?=($gasRatePerUnit!=0)?number_format($gasRatePerUnit,2,'.',''):""?>"  onkeypress="return isNumber (event);"></td>
																											</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="gasPerDay" id="gasPerDay" size="5" style="text-align:right" value="<?=($gasPerDay!=0)?number_format($gasPerDay,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																										</TR>
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