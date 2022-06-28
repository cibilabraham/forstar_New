<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$confirmed		=	"";

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit  = false;
	
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
	if ($accesscontrolObj->canReEdit()) $reEdit=true;
	//----------------------------------------------------------
	
	# Reset variables
	$selectDate 	= "";
	$fromDate 	= "";
	$toDate 	= "";
	$searchMode = false;
	$selDate	= "";	
	$processorsRecs = array();
	$details = "";
	$summary = "";
	$selProcessor = "";
	$statusChangeMode = false;
	$releaseAllocation = false;
	$releaseInvoice = false;

	$enableSearch = false;

	if ($p["supplyFrom"]) $dateFrom = $p["supplyFrom"];
	if ($p["supplyTill"]) $dateTill = $p["supplyTill"];

	# Select a Date
	
	#Select a day or date range SD-> Single Date/ DR-> Date Range
	
			
	//if (($p["cmdSearch"]!="" && ($details!="" || $summary!="")) || (($statusChangeMode || $enableSearch) && ($details!="" || $summary!="")) ) {
	if (($p["cmdSearch"]!="") || ($dateFrom!='' &&  $dateTill!='')) {

		if ($dateFrom) $fromDate  = mysqlDateFormat($dateFrom);
		if ($dateTill) $toDate  = mysqlDateFormat($dateTill);
			
			
			//if($p['unit']!="")$unitId=$p['unit'];
			//echo "hii".$unitId;
			//$units = $yearwisePurchaseReportObj->getAllUnit($fromDate,$toDate);
			$fishRec = $yearwisePurchaseReportObj->getAllFish($fromDate,$toDate);
			$displayMnth = $rmFreezingCalendarReportObj->getMonthYear($fromDate,$toDate);
			$searchMode = true;
	}

	

	# Display heading
	$heading = $label_YearwisePurchaseReport;

	$ON_LOAD_PRINT_JS	= "libjs/yearwisepurchasereport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmYearwisePurchaseReport" action="YearwisePurchaseReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<? if($err!="" ){?>
		<tr>
			<td height="30" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											 <tr> 
												<td colspan="4" align="center">
												 <? if($print==true){?>
													<!--input type="button" name="Submit" value=" View / Print" class="button" onClick="return printWindow('PrintDailyCatchSumary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>',700,600);"-->&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; 
												</td>
												 <? } ?>
											</tr>
											<tr> 
												<td colspan="3" nowrap>
													<table  border="0" align="center" cellpadding="0" cellspacing="0">
														<tr>
															<TD>
																<table>
																	<TR>
																		<TD valign="top">
																			<table>
																				<TR>
																					<TD>
																						<fieldset>
																							<legend class="fieldName">Date Selection</legend>
																								<table width="200" border="0">
																									<tr>
																										<td>
																											<table width="100" border="0">
																												<tr> 
																													<td class="fieldName">From:</td>
																													<td nowrap="true">
																														<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off"   onchange="submitForm('supplyFrom','supplyTill', frmYearwisePurchaseReport);"/>
																													</td>
																													<td class="fieldName">Till:</td>
																													<td nowrap="true">
																														<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off"  onchange="submitForm('supplyFrom','supplyTill', frmYearwisePurchaseReport);"/>
																													</td>
																													<td>&nbsp;</td>
																													<td class="listing-item">
																														<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateYearWisePurchaseReportSearch();"/>
																													</td>
																												</tr>	
																											</table>
																										</td>
																									</tr>
																								</table>
																							</fieldset>
																						</TD>
																					</TR>
																					<tr>
																						<TD height="5"></TD>
																					</tr>
																					<tr>
																						<TD valign="top"></TD>
																					</tr>
																				</table>
																			</TD>
																	<!--	<TD valign="top">
																				<table width="200" border="0" cellpadding="0" cellspacing="0">
																					<tr>
																						<td valign="top">
																							<fieldset>
																								<legend class="fieldName">Search Options </legend>
																									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																										<tr>
																											<td  class="fieldName">Unit</td>
																											<td>
																												<select id="unit" name="unit" >
																													<option value="">--Select All--</option>
																													<?php
																													
																														if(sizeof($units) > 0)
																														{
																															foreach($units as $unitval)
																															{
																																$sel = '';
																																if($unitId == $unitval[0]) $sel = 'selected';
																																
																																echo '<option '.$sel.' value="'.$unitval[0].'">'.$unitval[1].'</option>';
																															}
																														}
																													?>			
																												</select>	
																											</td>
																										</tr>

																										<tr>
																											<td>&nbsp;</td>
																											<td class="listing-item">
																											<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateYearWiseProductionReportSearch();"/>
																											</td>
																										</tr>
																										<tr>
																											<TD height="5"></TD>
																										</tr>
																									</table>
																								</fieldset>
																							</td>
																						</tr>
																					</table>
																				</TD> -->
																			</TR>
																		</table>				
																	</TD>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td colspan="3"  height="10" class="listing-item">&nbsp;</td>
													</tr>
													<tr> 
														<td colspan="3" class="listing-item" style="padding-left:10px; padding-right:10px;">
															<table border="0" cellpadding="2" cellspacing="0" align='center'>
																<tr bgcolor="#FFFFFF">
																	<td style="padding-left:10px; padding-right:10px;" >
																		<table cellpadding="1"   cellspacing="1" border="0" align="center" bgcolor="#999999">
																		<?
																		if( sizeof($fishRec) > 0 )
																		{
																		?>
																			<tr  bgcolor="#f2f2f2" align="center" >
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Species</td>
																				<? if(sizeof($displayMnth)>0)   
																				{
																					foreach($displayMnth as $disp) {
																				?>
																				<td class="listing-head" style="padding-left:5px; padding-right:5px;"><?=$disp[0]?></td>
																				<?
																					}	
																				}
																				?>
																			</tr>
																			<?php
																				
																			$purchaseDetailTotal	= 0;
																			foreach($fishRec as $fish)
																			{	
																				$fishId=$fish[0];
																				$fishName=$fish[1];
																			?>
																			<tr>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$fishName?></td>
																				<? if(sizeof($displayMnth)>0)   
																				{
																					foreach($displayMnth as $disp) 
																					{
																						$fromDate=$disp[1].'-'.'01';
																						$toDate=$disp[1].'-'.'31';
																						$purchase = $yearwisePurchaseReportObj->getPOForAFish($fromDate, $toDate,$fishId);
																						$purchaseDetail=$purchase[0];
																						$purchaseDetailTotal+=$purchaseDetail;
																				?>
																				<td bgcolor="#fff" class="listing-head" style="padding-left:5px; padding-right:5px;" align="right"><?=$purchaseDetail?></td>
																				<?
																					}	
																				}
																				?>
																			</tr>
																			<? 
																			}
																			?>
																			<tr>
																				<td bgcolor="#fff" class="listing-head" style="padding-left:5px; padding-right:5px; ">Total</td>
																				<? if(sizeof($displayMnth)>0)   
																				{
																					foreach($displayMnth as $disp) 
																					{
																						$fromDate=$disp[1].'-'.'01';
																						$toDate=$disp[1].'-'.'31';
																						$purchaseTotal = $yearwisePurchaseReportObj->getPOForADate($fromDate, $toDate);
																						$purchaseDetailTot=$purchaseTotal[0];
																						
																				?>
																				<td bgcolor="#fff" class="listing-head" style="padding-left:5px; padding-right:5px;" align="right"><?=$purchaseDetailTot?></td>
																				<?
																					}	
																				}
																				?>
																			</tr>


																			<tr><td bgcolor="#fff" class="listing-head" style="padding-left:5px; padding-right:5px; ">Grand Total</td><td bgcolor="#fff" colspan="<?=sizeof($displayMnth)?>" class="listing-head" style="padding-left:5px; padding-right:5px; " align="right"><?=number_format($purchaseDetailTotal, 2, '.', '');?></td></tr>
																	<?php
																		} 
																	
																	?>
																</table>
															</td>
														</tr>		
													</table>
												</td>
											</tr>
											<tr> 
												<td colspan="3" align="center" height="5"></td>
											</tr>
											<!--<tr> 
												<? if($editMode){?>
												<?} else{?>
												<td colspan="4" align="center"><? if($print==true){?><input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('YearwiseProductionReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTill?>&unit=<?=$unit?>',700,600);" <? if ($rmLotRecordsSize==0 && $unit=="") echo "disabled";?> ><? }?></td>
												<?} ?>
											</tr>-->
											<tr> 
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							
						<!-- Form fields end   -->
						</td>
					</tr>		
				</table>
			</td>	
		</tr>
	</table>
</form>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>

<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>