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
		$holdingCost		=	$p["holdingCost"];
		$holdingDuration	=	$p["holdingDuration"];
		$adminOverheadChargesCode = $p["adminOverheadChargesCode"];
		$adminOverheadChargesCost = $p["adminOverheadChargesCost"];
		$profitMargin		=	$p["profitMargin"];
		$insuranceCost		=	$p["insuranceCost"];
		$vatCode1		=	$p["vatCode1"];
		$vatRate1		=	$p["vatRate1"];
		$vatCode2		=	$p["vatCode2"];
		$vatRate2		=	$p["vatRate2"];
		$vatCode3		=	$p["vatCode3"];
		$vatRate3		=	$p["vatRate3"];
		$vatCode4		=	$p["vatCode4"];
		$vatRate4		=	$p["vatRate4"];
		$vatCode5		=	$p["vatCode5"];
		$vatRate5		=	$p["vatRate5"];
		$cstRate		=	$p["cstRate"];
		//$cstRate		=	$p["cstRate"];
		$educationCess		=	$p["educationCess"];
		$exciseCode	=	$p["exciseCode"];
		$exciseRate		=	$p["exciseRate"];
		$pickleCode		=	$p["pickleCode"];
		$pickleRate		=	$p["pickleRate"];
		if($holdingCost|| $holdingDuration || $adminOverheadChargesCode || $adminOverheadChargesCost || $profitMargin || $insuranceCost || $vatCode1 || $vatRate1  || $vatCode2 || $vatRate2 || $vatCode3 || $vatRate3 || $vatCode4 || $vatRate4 || $vatCode5 || $vatRate5  || $cstRate || $educationCess || $exciseCode	 || $exciseRate || $pickleCode|| $pickle)
		{
			$upHoldingCost=$productionOtherCostObj->updateProductionOtherCost($holdingCost,$holdingDuration,$adminOverheadChargesCode,$adminOverheadChargesCost,$profitMargin,$insuranceCost,$vatCode1,$vatRate1,$vatCode2,$vatRate2,$vatCode3,$vatRate3,$vatCode4,$vatRate4, $vatCode5,$vatRate5,$cstRate,$educationCess,$exciseRate,$pickle,$userId);

			if($upHoldingCost)
			{
				$sessObj->createSession("displayMsg",$msg_succUpdateProductionHoldingCost);
				$sessObj->createSession("nextPage",$url_afterUpdateProductionHoldingCost.$selection);
			}
			else 
			{
				$addMode	=	true;
				$err		=	$msg_failUpdateProductionHoldingCost;
			}
			$upHoldingCost		=	false;
		}
	}	
		
	#List all Production Working Hours
	$upProductionCost=$productionOtherCostObj->getProductionOthersCost();
	$maintenanceCost=$upProductionCost[1];
	$consumablesCost=$upProductionCost[2];
	$labCost=$upProductionCost[3];
	$pouchesTestPerBatchUnit=$upProductionCost[4];
	$pouchesTestPerBatchTCost=$upProductionCost[5];
	$ingredientCost=$upProductionCost[6];
	//echo $maintenanceCost;
	$ON_LOAD_PRINT_JS = "libjs/productionothercost.js";
	require("template/btopLeftNav.php");
?>
<form name="frmProductionHoldingCost" action="ProductionHoldingCost.php" method="post">
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
							$bxHeader = "Production Holding Cost";
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
																											<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Holding COST</Th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">PU/COST</th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">T/COST</th>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Holding Cost</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="holdingCost" id="holdingCost" size="5" style="text-align:right" value="<?=$holdingCost?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Holding Duration</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="holdingDuration" id="holdingDuration" size="5" style="text-align:right" value="<?=$holdingDuration?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Administrative overhead Charges</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item" align="center">
																											<input type="text" name="adminOverheadChargesCode" id="adminOverheadChargesCode" size="5" style="text-align:right" value="<?=$adminOverheadChargesCode?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item" align="center">
																											<input type="text" name="adminOverheadChargesCost" id="adminOverheadChargesCost" size="5" style="text-align:right" value="<?=$adminOverheadChargesCost?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Profit Margin</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="profitMargin" id="profitMargin" size="5" style="text-align:right" value="<?=$profitMargin?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Insurance Cost</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="insuranceCost" id="insuranceCost" size="5" style="text-align:right" value="<?=$insuranceCost?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">VAT Rate</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="vatCode1" id="vatCode1" size="5" style="text-align:right" value="<?=$vatCode1?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="vatRate1" id="vatRate1" size="5" style="text-align:right" value="<?=$vatRate1?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">VAT Rate</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="vatCode2" id="vatCode2" size="5" style="text-align:right" value="<?=$vatCode2?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="vatRate2" id="vatRate2" size="5" style="text-align:right" value="<?=$vatRate2?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">VAT Rate</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="vatCode3" id="vatCode3" size="5" style="text-align:right" value="<?=$vatCode3?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="vatRate3" id="vatRate3" size="5" style="text-align:right" value="<?=$vatRate3?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">VAT Rate</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="vatCode4" id="vatCode4" size="5" style="text-align:right" value="<?=$vatCode4?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="vatRate4" id="vatRate4" size="5" style="text-align:right" value="<?=$vatRate4?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<tr>
																										<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">VAT Rate</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="vatCode5" id="vatCode5" size="5" style="text-align:right" value="<?=$vatCode5?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="vatRate5" id="vatRate5" size="5" style="text-align:right" value="<?=$vatRate5?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</tr>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">CST Rate</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="cstRate" id="cstRate" size="5" style="text-align:right" value="<?=$cstRate?>"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																																																		
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Education Cess</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="educationCess" id="educationCess" size="5" style="text-align:right" value="<?=$educationCess?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Excise Rate</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item">RTE</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="exciseRate" id="exciseRate" size="5" style="text-align:right" value="<?=$exciseRate?>">&nbsp;%</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																											<td nowrap style="padding-left:5px; padding-right:5px;"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Pickle</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item">PK</td>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center" class="listing-item">
																											<input type="text" name="pickle" id="pickle" size="5" style="text-align:right" value="<?=$pickle?>">&nbsp;%</td>
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