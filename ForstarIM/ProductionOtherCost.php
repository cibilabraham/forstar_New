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
		$productionOtherCostId	=	$p["hidproductionOtherCostId"];
		$maintenanceCost	=	$p["maintenanceCost"];
		$consumablesCost	=	$p["consumablesCost"];
		$labCost		=	$p["labCost"];
		$pouchesTestPerBatchUnit = 	$p["pouchesTestPerBatchUnit"];
		$pouchesTestPerBatchTCost = $p["pouchesTestPerBatchTCost"];
		$ingredientCost=$p["ingredientCost"];
		$holdingCost		=	$p["holdingCost"];
		$holdingDuration	=	$p["holdingDuration"];
		$adminOverheadChargesCode = $p["adminOverheadChargesCode"];
		$adminOverheadChargesCost = $p["adminOverheadChargesCost"];
		$profitMargin		=	$p["profitMargin"];
		$insuranceCost		=	$p["insuranceCost"];
		if($maintenanceCost|| $consumablesCost || $labCost || $pouchesTestPerBatchUnit || $pouchesTestPerBatchTCost || $ingredientCost || $holdingCost || $holdingDuration || $adminOverheadChargesCode || $adminOverheadChargesCost || $profitMargin || $insuranceCost)
		{
			if($productionOtherCostId!="")
			{
				$upOtherCost=$productionOtherCostObj->updateProductionOtherCost($productionOtherCostId,$maintenanceCost,$consumablesCost,$labCost,$pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost,$ingredientCost,$holdingCost,$holdingDuration,$adminOverheadChargesCode,$adminOverheadChargesCost,$profitMargin,$insuranceCost,$userId);
			}
			else
			{
				$upOtherCost=$productionOtherCostObj->addProductionOtherCost($maintenanceCost,$consumablesCost,$labCost,$pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost,$ingredientCost,$holdingCost,$holdingDuration,$adminOverheadChargesCode,$adminOverheadChargesCost,$profitMargin,$insuranceCost,$userId);
			}

			if($upOtherCost)
			{
				$sessObj->createSession("displayMsg",$msg_succUpdateProductionOtherCost);
				$sessObj->createSession("nextPage",$url_afterUpdateProductionOtherCost.$selection);
			}
			else 
			{
				$addMode	=	true;
				$err		=	$msg_failUpdateProductionOtherCost;
			}
			$upOtherCost		=	false;
		}
	}	
		
	#List all Production Working Hours
	$upProductionCost=$productionOtherCostObj->getProductionOthersCost();
	$productionOtherCostId=$upProductionCost[0];
	$maintenanceCost=$upProductionCost[1];
	$consumablesCost=$upProductionCost[2];
	$labCost=$upProductionCost[3];
	$pouchesTestPerBatchUnit=$upProductionCost[4];
	$pouchesTestPerBatchTCost=$upProductionCost[5];
	$ingredientCost=$upProductionCost[6];
	$holdingCost		=	$upProductionCost[7];
	$holdingDuration	=	$upProductionCost[8];
	$adminOverheadChargesCode = $upProductionCost[9];
	$adminOverheadChargesCost = $upProductionCost[10];
	$profitMargin		=	$upProductionCost[11];
	$insuranceCost		=	$upProductionCost[12];
	//echo $maintenanceCost;
	$ON_LOAD_PRINT_JS = "libjs/productionothercost.js";
	require("template/btopLeftNav.php");
?>
<form name="frmProductionOtherCost" action="ProductionOtherCost.php" method="post">
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
							$bxHeader = "Production Other Cost";
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
																				<input type="hidden" name="hidproductionOtherCostId" id="hidproductionOtherCostId" value="<?=$productionOtherCostId;?>">
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
																											<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">OTHER COST (PerMonth)</Th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Maintenance Cost</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="maintenanceCost" id="maintenanceCost" size="5" style="text-align:right" value="<?=($maintenanceCost!=0)?number_format($maintenanceCost,2,'.',''):"";?>" onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Consumables Cost</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="consumablesCost" id="consumablesCost" size="5" style="text-align:right" value="<?=($consumablesCost!=0)?number_format($consumablesCost,2,".",""):"";?>" onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Lab Cost</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="labCost" id="labCost" size="5" style="text-align:right" value="<?=($labCost!=0)?number_format($labCost,2,'.',''):""?>" onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Pouches for testing per batch</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="pouchesTestPerBatchUnit" id="pouchesTestPerBatchUnit" size="5" style="text-align:right" value="<?=($pouchesTestPerBatchUnit!=0)?number_format($pouchesTestPerBatchUnit,2,".",""):"";?>" onkeypress="return isNumber (event);">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item">
																											<input type="text" name="pouchesTestPerBatchTCost" id="pouchesTestPerBatchTCost" size="5" style="text-align:right" value="<?=($pouchesTestPerBatchTCost!=0)?number_format($pouchesTestPerBatchTCost,2,".",""):"";?>" onkeypress="return isNumber (event);">&nbsp;%</td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Ingredient Powdering Cost per Kg</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="ingredientCost" id="ingredientCost" size="5" style="text-align:right" value="<?=($ingredientCost!=0)?number_format($ingredientCost,2,".",""):"";?>" onkeypress="return isNumber (event);"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">HOLDING COST</Th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</th>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Holding Cost</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="holdingCost" id="holdingCost" size="5" style="text-align:right" onkeypress="return isNumber (event);" value="<?=($holdingCost!=0)?number_format($holdingCost,2,'.',''):"";?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<!--<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Holding Duration</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="holdingDuration" id="holdingDuration" size="5" style="text-align:right" onkeypress="return isNumber (event);" value="<?=($holdingDuration!=0)?number_format($holdingDuration,2,'.',''):""?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>-->
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Administrative overhead Charges</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item" align="center">
																											<input type="text" name="adminOverheadChargesCode" id="adminOverheadChargesCode" size="5" onkeypress="return isNumber (event);" style="text-align:right" value="<?=($adminOverheadChargesCode!=0)?number_format($adminOverheadChargesCode,2,'.',''):"";?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item" align="center">
																											<input type="text" name="adminOverheadChargesCost" id="adminOverheadChargesCost" size="5" onkeypress="return isNumber (event);" style="text-align:right" value="<?=($adminOverheadChargesCost!=0)?number_format($adminOverheadChargesCost,2,'.',''):"";?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Profit Margin</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="profitMargin" id="profitMargin" size="5" style="text-align:right" onkeypress="return isNumber (event);" value="<?=($profitMargin!=0)?number_format($profitMargin,2,'.',''):"";?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Insurance Cost</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="insuranceCost" id="insuranceCost" size="5" style="text-align:right" onkeypress="return isNumber (event);" value="<?=($insuranceCost!=0)?number_format($insuranceCost,2,'.',''):""?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
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