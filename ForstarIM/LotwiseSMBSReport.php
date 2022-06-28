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
	if ($p["selDate"]) $selDate = $p["selDate"];

	if ($p["details"]!="") $details	= $p["details"];
	if ($p["summary"]!="") $summary	= $p["summary"];

	#Select a day or date range SD-> Single Date/ DR-> Date Range
	
			
	//if (($p["cmdSearch"]!="" && ($details!="" || $summary!="")) || (($statusChangeMode || $enableSearch) && ($details!="" || $summary!="")) ) {
	if (($p["cmdSearch"]!="") || ($dateFrom!='' &&  $dateTill!='')) {

		if ($dateFrom) $fromDate  = mysqlDateFormat($dateFrom);
		if ($dateTill) $toDate  = mysqlDateFormat($dateTill);
			$rmLotRecords = $lotwiseSMBSReportObj->getLotReport($fromDate,$toDate);
			$rmLotRecordsSize=sizeof($rmLotRecords);
			$searchMode = true;
	}

	#Confirm the Records
	if ($p["cmdConfirm"]!="") {
		$selDate	= $p["selDate"];
 		if ($selDate)  {
			$selectDate = mysqlDateFormat($selDate);
			$updateDailyFrozenPackingRecords = $rmVarianceReportObj->updateDailyFrozenPackingRecords($selectDate);
  		}
	}
	
	# Display heading
	$heading = $label_LotwiseSMBSReport;

	$ON_LOAD_PRINT_JS	= "libjs/lotwisereport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmLotwiseSMBSReport" action="LotwiseSMBSReport.php" method="Post">
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
																							<legend class="listing-item">Date Selection</legend>
																								<table width="200" border="0">
																									<tr>
																										<td>
																											<table width="100" border="0">
																												<tr> 
																													<td class="fieldName">From:</td>
																													<td nowrap="true">
																							<!--   -->					<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off"   onchange="submitForm('supplyFrom','supplyTill', frmLotwiseSMBSReport);"/>
																													</td>
																													<td class="fieldName">Till:</td>
																													<td nowrap="true">
																														<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off"  onchange="submitForm('supplyFrom','supplyTill', frmLotwiseSMBSReport);"/>
																													</td>
																													<td>&nbsp;</td>
																													<td class="listing-item">
																													<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateSMBSReportSearch();"/>
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
																		if( sizeof($rmLotRecords) > 0 )
																		{
																		?>
																			<tr  bgcolor="#f2f2f2" align="center" >
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Date</td>
																				<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px"  nowrap>RM Lot Id</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Species</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>SMPS Consumption Qty</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>SMPS Consumption %</td>
																			</tr>
																			<?php
																			$totalCount	= 0;
																			
																			foreach($rmLotRecords as $rmlot)
																			{	
																				$totalQty	= 0; $prevFish="";
																				$unitName=$rmlot[5];
																				$rmlotnm=$rmlot[3];
																				$chemical=$rmlot[2];
																				$date=dateFormat($rmlot[4]);
																				$weighmentDetails= $lotwiseSMBSReportObj->getWeighmentDetail($rmlotnm);
																			?>
																			<tr>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$date?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$rmlotnm?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap >
																				<? foreach($weighmentDetails as $wghtDet)
																				{
																				
																					$fish=$wghtDet[3];
																					if($fish!=$prevFish)
																					{
																						echo $fish.'<br/>';
																					}
																					$weight=$wghtDet[4];
																					$totalQty+=$weight;
																					$prevFish=$fish;
																				}
																				?>
																				
																				</td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$chemical?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=number_format(($chemical/$totalQty), 2, '.', '');?></td>
																			</tr>
																			<? 
																			}
																			?>


																	<?php
																		if ($dateRange && $details) $colspan = 10;
																		else $colspan = 10;
																	?>
																	<?php
																		} 
																		/*else if($searchMode!="") {
																	?>
																		<tr bgcolor="white">
																			<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																		</tr>	
																	<?
																		}*/
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
												<td colspan="4" align="center"><? if($print==true){?><input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('PrintPendingRMLotReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTill?>&unit=<?=$unit?>',700,600);" <? if ($rmLotRecordsSize==0 && $unit=="") echo "disabled";?> ><? }?></td>
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