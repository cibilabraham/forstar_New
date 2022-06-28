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
		$advtCostId	=	$p["hidadvtCostId"];
		$advtCostPerMonth = $p["advtCostPerMonth"];
		$advtCostPerYear	=	$p["advtCostPerYear"];
		
		//echo "hii".$generalWaterConsumptionPerDayUnit.'--'.$gasPerCylinderPerDay.'--'.$costOfCylinder;
		//die();
		if($advtCostPerMonth|| $advtCostPerYear)
		{
			if($advtCostId!="")
			{
				$upAdvertisement=$productionAdvertisementObj->updateProductionAdvertisement($advtCostId,$advtCostPerMonth,$advtCostPerYear,$userId);
			}
			else
			{
				$upAdvertisement=$productionAdvertisementObj->addProductionAdvertisement($advtCostPerMonth,$advtCostPerYear,$userId);
			}

			if($upAdvertisement)
			{
				$sessObj->createSession("displayMsg",$msg_succUpdateProductionAdvertisement);
				$sessObj->createSession("nextPage",$url_afterUpdateProductionAdvertisement.$selection);
			}
			else 
			{
				$addMode	=	true;
				$err		=	$msg_failUpdateProductionAdvertisement;
			}
			$upAdvertisement		=	false;
		}
	}	
		
	#List all Production Working Hours
	$upProductionCost=$productionAdvertisementObj->getProductionAdvertisement();
	$advtCostId=$upProductionCost[0];
	$advtCostPerMonth=$upProductionCost[1];
	$advtCostPerYear=$upProductionCost[2];
	
	//echo $maintenanceCost;
	$ON_LOAD_PRINT_JS = "libjs/productionfuelprice.js";
	require("template/btopLeftNav.php");
?>
<form name="frmProductionAdvertisement" action="ProductionAdvertisement.php" method="post">
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
							$bxHeader = "Production Advertisement";
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
																				<input type="hidden" name="hidadvtCostId" id="hidadvtCostId" value="<?=$advtCostId;?>">
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
																											<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">ADVERTISEMENT</Th>
																											<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Advt cost per Month</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="advtCostPerMonth" id="advtCostPerMonth" size="8" style="text-align:right" value="<?=($advtCostPerMonth!=0)?number_format($advtCostPerMonth,2,'.',''):"";?>"  onkeypress="return isNumber (event);"></td>
																										</TR>
																										<TR>
																											<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Adv cost per year</TD>
																											<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
																											<input type="text" name="advtCostPerYear" id="advtCostPerYear" size="8" style="text-align:right" value="<?=($advtCostPerYear!=0)?number_format($advtCostPerYear,2,'.',''):""?>"  onkeypress="return isNumber (event);"></td>
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