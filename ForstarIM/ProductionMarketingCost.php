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

	#----------------Rate list--------------------	
	$productionMatrixMaster = "PMM";		

	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList = $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($productionMatrixMaster);			
	#--------------------------------------------

	# CST RATE
	$cstRate = $taxMasterObj->getBaseCst();

		
	# Update / Insert a Record
	if ($p["cmdSaveChange"]!="") 
	{
		$hidFixedCount=$p["hidFixedCount"];
		$hidVariableCount=$p["hidVariableCount"];
		if($hidFixedCount>0)
		{
			//$fixedDel=$productionMarketingCostObj->deleteProductionFixedMarketingRec();
			for($i=0; $i<$hidFixedCount; $i++)
			{
				$fixedProductionId=$p["fixedProductionId_".$i];
				$newFixedCost=$p["newFixedCost_".$i];
				$fixedCost=$p["fixedCost_".$i];
				$fixedPrdtIns=$productionMarketingCostObj->updateFixedMktngCost($fixedProductionId,$newFixedCost,$fixedCost);	
			}
		}

		if($hidVariableCount>0)
		{
			//$varDel=$productionMarketingCostObj->deleteProductionVariableMarketing();
			for($j=0; $j<$hidVariableCount; $j++)
			{
				$varProductionId=$p["varProductionId_".$j];
				$newVarCost=$p["newVarCost_".$j];
				$varCost=$p["varCost_".$j];
				$varPrdtIns=$productionMarketingCostObj->updateVariableMktngCost($varProductionId,$newVarCost,$varCost);	
			}
		}
		if($varPrdtIns ||$fixedPrdtIns)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdateProductionMarketing);
			$sessObj->createSession("nextPage",$url_afterUpdateProductionMarketing.$selection);
		}
		else {
				$addMode	=	true;
				$err		=	$msg_failUpdateProductionMarketing;
			}
		$varPrdtIns		=	false;
	}
	
	$getFixedProductionRecord=$productionMarketingCostObj->fetchAllFixedProduction();
	$getVariableProductionRecord=$productionMarketingCostObj->fetchAllVariableProduction();
	

	
	$ON_LOAD_PRINT_JS = "libjs/ProductionMarketingCost.js";

	
	require("template/btopLeftNav.php");
?>
<form name="frmProductionMarketingCost" action="ProductionMarketingCost.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<tr><TD height="10"></TD></tr>
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
							$bxHeader = "Production Marketing";
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
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes ">
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
																											<Th class="listing-head" colspan="6" nowrap style="padding-left:5px; padding-right:5px;">Production Marketing Cost</Th>
																										</TR>
																										<TR>
																											<Td class="listing-head" colspan="6" nowrap style="padding-left:5px; padding-right:5px;" bgcolor="#f1f1f1">Fixed Cost</Td>
																										</TR>
																										<?
																										$i=0;	$sumTotalFixed=0; $sumNewTotalFixed=0;
																										if(sizeof($getFixedProductionRecord)>0)
																										{
																									
																										foreach($getFixedProductionRecord as $pr)
																										{
																											/*$fixedPrdnId=$pr[0];
																											$fixedPrdName=$pr[1];
																											$newFixedCost=$productionMarketingCostObj->getFixedNew($fixedPrdnId);
																											$sumNewTotalFixed+=$newFixedCost;
																											$totalFixed=$productionMarketingCostObj->getFixedTot($fixedPrdnId);
																											$sumTotalFixed+=$totalFixed;
																											//echo "hii".$newFixedCost; */
																											
																											$fixedMrktngId = $pr[0];
																											$newTotalCost = $pr[1];
																											$totalCost = $pr[2];
																											$deptName = $pr[3];
																											$sumNewTotalFixed+=$newTotalCost;
																											$sumTotalFixed+=$totalCost;
																											
																										if($totalCost!=0)
																										{
																										?>
																										<tr>
																											<td class="listing-item"><?=$deptName?><input type="hidden" name="fixedProductionId_<?=$i?>" id="fixedProductionId_<?=$i?>" value="<?=$fixedMrktngId?>" ></td>
																											<td class="listing-item"><input type="text" name="newFixedCost_<?=$i?>" id="newFixedCost_<?=$i?>" value="<?=number_format((float)$newTotalCost,2,'.','');?>" style="text-align:right" size="12" class="newFixedCost" onkeyUp="getTotalFixed();" onkeypress="return isNumber (event);" autocomplete="off"></td>
																											<td class="listing-item"><input type="text" readonly name="fixedCost_<?=$i?>" id="fixedCost_<?=$i?>" value="<?=number_format((float)$totalCost, 2, '.', '');?>" size="12" style="text-align:right"></td>
																										</tr>
																										<? 
																										}
																											$i++;
																										} 
																										?>
																										<tr>
																											<td class="listing-item">Total</td>
																											<td class="listing-item"><input type="text" readonly name="newTotalFix" id="newTotalFix" value="<?=($sumNewTotalFixed!="0")?number_format((float)$sumNewTotalFixed,2,'.',''):""?>" style="text-align:right" size="12" /></td>
																											<td class="listing-item" ><input type="text" readonly name="totalFix" id="totalFix" value="<?=number_format((float)$sumTotalFixed, 2, '.', '');?>" size="12" style="text-align:right"/></td>
																										</tr>
																										<input type="hidden" name="hidFixedCount" id="hidFixedCount" value="<?=$i?>">
																										<? 
																										
																										} 
																										?>
																										<TR>
																											<Td class="listing-head" colspan="6" nowrap style="padding-left:5px; padding-right:5px;" bgcolor="#f1f1f1">Variable Cost</Td>
																										</TR>
																										<?
																										if(sizeof($getVariableProductionRecord)>0)
																										{
																										$j=0;	
																										$sumTotalVar=0; $sumNewTotalVar=0;
																										foreach($getVariableProductionRecord as $vpr)
																										{
																											/*$varPrdnId=$vpr[0];
																											$varPrdnName=$vpr[1];
																											$newVarCost=$productionMarketingCostObj->getVariableNew($varPrdnId);
																											$sumNewTotalVar+=$newVarCost;
																											$totalVar=$productionMarketingCostObj->getVariableTot($varPrdnId);
																											$sumTotalVar+=$totalVar;*/
																											
																											$varMrktngId = $vpr[0];
																											$newVarCost = $vpr[1];
																											$totalVar = $vpr[2];
																											$deptName = $vpr[3];
																											$sumNewTotalVar+=$newVarCost;
																											$sumTotalVar+=$totalVar;
																											
																										if($totalVar!=0)
																										{
																										?>
																										<tr>
																											<td class="listing-item"><?=$deptName?><input type="hidden" name="varProductionId_<?=$j?>" id="varProductionId_<?=$j?>" value="<?=$varMrktngId?>"></td>
																											<td class="listing-item"><input type="text" name="newVarCost_<?=$j?>" id="newVarCost_<?=$j?>" value="<?=number_format((float)$newVarCost, 2, '.', '');?>" size="12" style="text-align:right" class="newVariableCost" onkeyUp="getTotalVariable();" onkeypress="return isNumber (event);" autocomplete="off"></td>
																											<td class="listing-item"><input type="text" readonly name="varCost_<?=$j?>" id="varCost_<?=$j?>" value="<?=number_format((float)$totalVar, 2, '.', '');?>" style="text-align:right" size="12"></td>
																										</tr>
																										<? 
																										}
																											$j++;
																										} 
																										?>
																										<tr>
																											<td class="listing-item">Total</td>
																											<td class="listing-item"><input type="text" readonly name="newTotalVar" id="newTotalVar" value="<?=($sumNewTotalVar!="0")?number_format((float)$sumNewTotalVar,2,'.',''):"";?>" style="text-align:right" size="12" /></td>
																											<td class="listing-item" ><input type="text" readonly name="totalVar" id="totalVar" value="<?=number_format((float)$sumTotalVar, 2, '.', '');?>" size="12" style="text-align:right"/></td>
																										</tr>
																										<input type="hidden" name="hidVariableCount" id="hidVariableCount" value="<?=$j?>">
																										<? 
																										} 
																										?>
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