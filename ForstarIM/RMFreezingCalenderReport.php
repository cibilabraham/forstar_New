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
	//echo $dateFrom.'--'.$dateTill;
	# Select a Date
	//if ($p["selDate"]) $selDate = $p["selDate"];

	if ($p["details"]!="") $details	= $p["details"];
	if ($p["summary"]!="") $summary	= $p["summary"];

/*	#Select a day or date range SD-> Single Date/ DR-> Date Range
	if ($g["dateSelection"]!="") $dateSelection = $g["dateSelection"];
	else $dateSelection = $p["dateSelection"];

	if ($dateSelection=='SD' || $dateSelection=="") {
		$singleDay = "Checked";
		$dateFrom = "";
		$dateTill = "";
	} else if ($dateSelection=='DR') {
		$dateRange = "Checked";
		$selDate = "";
	} else $dateSelection = "";
*/

	if ($p["selProcessor"]) $selProcessor = $p["selProcessor"];	
	if ($p["searchType"]) $searchType = $p["searchType"];	
	if($searchType!="")
	{
		($searchType=="monthwise")?$mnthSelect="selected":$mnthSelect="";
		($searchType=="datewise")?$dtSelect="selected":$dtSelect="";
	}
	// Change Status PO
	$purchaseOrderId	= $p["csPurchaseOrder"];
	if ($purchaseOrderId>0) {
		$enableSearch = true;
		// Get All Invoices
		$invoiceRecs = $rmFreezingCalendarReportObj->getAllInvoices($purchaseOrderId);
	}
	
	if ($selDate || ($dateFrom && $dateTill)) {
		$fDate = ($selDate)?mysqlDateFormat($selDate):mysqlDateFormat($dateFrom);
		$tDate = ($selDate)?mysqlDateFormat($selDate):mysqlDateFormat($dateTill);
		# Get Processors
		$processorsRecs = $rmFreezingCalendarReportObj->getProcessors($fDate, $tDate);
	}

	// Status change
	$allocationReleaseChk = "";
	if ($p["cmdStatusUpdate"]!="") {

		$statusChangeMode = true;
		$allocationReleaseChk = "checked";

		$changeStatus = $p["changeStatus"];
		$csPurchaseOrderId	= $p["csPurchaseOrder"];
		$csInvoiceId			= $p["csInvoice"];

		if ($changeStatus=="CRA") {
			if ($csPurchaseOrderId>0) {
				$relAllocationRec=$rmFreezingCalendarReportObj->releaseAllocation($csPurchaseOrderId, $csInvoiceId, $userId);
				if ($relAllocationRec) $releaseAllocation = true;				
			}
		}
		else if ($changeStatus=="INVR"){
				if ($csPurchaseOrderId>0) {
					$relInvoiceRec = $rmFreezingCalendarReportObj->releaseInvoice($csPurchaseOrderId, $csInvoiceId, $userId);				
					if ($relInvoiceRec) $releaseInvoice = true;
				}
		}
	}
		
	if (($p["cmdSearch"]!="" && ($details!="" || $summary!="")) || (($statusChangeMode || $enableSearch) && ($details!="" || $summary!="")) || ($dateFrom!="" && $dateTill!="")) {

		
		//if ($selDate) $selectDate = mysqlDateFormat($selDate);
		if ($dateFrom) $fromDate = mysqlDateFormat($dateFrom);
		if ($dateTill) $toDate	 = mysqlDateFormat($dateTill);
		if($searchType=="datewise")
		{
			$dailyFrozenPackingRecords = $rmFreezingCalendarReportObj->getDFPForADate($fromDate, $toDate, $details, $summary, $selProcessor);
		}
		else if($searchType=="monthwise")
		{	$displayMnth = $rmFreezingCalendarReportObj->getMonthYear($fromDate,$toDate);
			//$dailyFrozenPackingRecordsSearch = $rmFreezingCalendarReportObj->getDFPForADate($fromDate, $toDate, $details, $summary, $selProcessor);
		}
		$searchMode = true;
	}

	#Confirm the Records
	if ($p["cmdConfirm"]!="") {
		$selDate	= $p["selDate"];
 		if ($selDate)  {
			$selectDate = mysqlDateFormat($selDate);
			$updateDailyFrozenPackingRecords = $rmFreezingCalendarReportObj->updateDailyFrozenPackingRecords($selectDate);
  		}
	}
	

	# Display heading
	$heading = $label_RMFreezingCalendarReport;

	$ON_LOAD_PRINT_JS	= "libjs/rmfreezingcalendarreport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");


	###FIND DATE AND YEAR BETWEEN TWO DATES
	

?>
<form name="frmRMFreezingCalenderReport" action="RMFreezingCalenderReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<? if($err!="" ){?>
		<tr>
			<td height="30" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?php if ($statusChangeMode) {
		?>
		<tr>
		<?php if ($changeStatus=="CRA")
		{?>
			<td height="30" align="center" class="err1" ><?=($releaseAllocation)?"<span style='color:green;'>Successfully released allocation</span>":"Failed to release allocation"?></td>
		<?php }?>
		<?php if ($changeStatus=="INVR")
		{
		?>
			<td height="30" align="center" class="err1" ><?=($releaseInvoice)?"<span style='color:green;'>Successfully released Invoices</span>":"Failed to release Invoices"?></td>
		<?php }?>
		</tr>
		<?php } ?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
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
																									<td class="fieldName">From:</td>
																									<td nowrap="true">
																										<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" onchange="submitForm('supplyFrom','supplyTill',document.frmRMFreezingCalenderReport);" />
																									</td>
																									<td class="fieldName">Till:</td>
																									<td nowrap="true">
																										<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off" onchange="submitForm('supplyFrom','supplyTill',document.frmRMFreezingCalenderReport);" />
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</TD>
																				</TR>
																				<tr><TD height="5"></TD></tr>
																			</table>
																		</TD>
																		<td valign="top">
																			<table>
																				<TR>
																					<TD>
																						<table>
																							<TR>
																								<TD class="fieldName">Processor</TD>
																								<td nowrap="true">
																									<select name="selProcessor" id="selProcessor">
																										<option value="">--Select All--</option>
																										<?php
																										foreach ($processorsRecs as $pr) {
																											$processorId 	= $pr[0];
																											$processorName	= $pr[1];
																											$selected = ($selProcessor==$processorId)?"selected":"";
																										?>
																										<option value="<?=$processorId?>" <?=$selected?>><?=$processorName?></option>
																										<?php
																											}
																										?>
																									</select>
																								</td>
																							</TR>
																						</table>
																					</TD>
																				</TR>
																			</table>
																		</td>
																		<td valign="top"> 
																			<table>
																				<TR>
																					<TD>
																						<fieldset>
																							<legend class="listing-item">Search Type</legend>
																							<table width="200" border="0">
																								<tr> 
																									<td class="fieldName">Type:</td>
																									<td nowrap="true">
																										<select name="searchType" id="searchType">
																											<option  value="">Select</option>
																											<option  value="monthwise" <?=$mnthSelect?>>Month Wise</option>
																											<option  value="datewise" <?=$dtSelect?>>Date Wise</option>
																										</select>
																									</td>
																									
																								</tr>
																							</table>
																						</fieldset>
																					</TD>
																				</TR>
																			</table>
																		</td>

																		<TD valign="top">
																			<table width="200" border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td valign="top">
																						<fieldset>
																							<legend class="fieldName">Search Options </legend>
																							<table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
																								<tr>
																									<td align="center">
																									<input type="checkbox" name="details" id="details" value="Y" <?=($details)?"checked":"";?> class="chkBox" onclick="remChk('details');">
																									</td>
																									<td class="listing-item" nowrap>Detailed</td>
																								</tr>
																								<tr>
																									<td align="center">
																										<input name="summary" type="checkbox" class="chkBox" id="summary" onclick="remChk('summary');" value="Y" <?=($summary)?"checked":"";?>>
																									</td>
																									<td class="listing-item" nowrap>Summary</td>
																								</tr>
																								<tr><TD height="5"></TD></tr>
																								<tr>
																									<td>&nbsp;</td>
																									<td class="listing-item">
																										<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateFrznPkgReportSearch();"/>
																									</td>
																								</tr>
																								<tr><TD height="5"></TD></tr>
																							</table>
																						</fieldset>
																					</td>
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
													<table width="100%" border="0" cellpadding="2" cellspacing="0">
														<?
														if( sizeof($dailyFrozenPackingRecords) > 0 )
														{
														?>
														<tr bgcolor="#FFFFFF">
															<td style="padding-left:10px; padding-right:10px;">
																<table cellpadding="1"  width="100%" cellspacing="1" border="0" align="center" bgcolor="#999999">
																
																	<tr  bgcolor="#f2f2f2" align="center">	
																		
																		<?php
																			if ($dtSelect && $searchType) {
																		?>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Date</td>
																		<?php
																			}
																		?>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2" nowrap>Rm Lot Id</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Processor</td>	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Fish</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Process Code</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Freezing Stage</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">EU Code</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Brand</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Frozen Code&nbsp; </td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">MC Pkg</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" >Other Details</td>	
																	</tr>
																	<tr bgcolor="#f2f2f2" align="center">
																		<TD>
																			<table cellpadding="1" cellspacing="1" width="100%">
																				<TR>
																					<td class="listing-head" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >Grade</td>
																					<td class="listing-head" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">MC </td>
																					<td class="listing-head" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">LS</td>
																					<td class="listing-head" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">Frozen<br> Qty</td>
																					<td class="listing-head" nowrap width="100" style="padding-left:5px; padding-right:5px;" align="right">Net<br>Qty</td>
																				</TR>
																			</table>
																		</TD>
																	</tr>
																	<?php
																		$i = 0;
																		$totalNumMc	= 0;
																		$totalLS	= 0;
																		$totalFrozenQty = 0;
																		$totalNetQty	= 0;
																		foreach ($dailyFrozenPackingRecords as $dfpr) {
																			$i++;
																			$dailyFrozenPackingMainId	=	$dfpr[0];
																			$dailyFrozenPackingEntryId	=	$dfpr[4];
																			$unitId			=	$dfpr[2];
																			if($dfpr[3]!=0) $lotId	=	$dfpr[3];
																			$fishId		= $dfpr[5];
																			$processCodeId 	= $dfpr[6];
																			$freezingStageId = $dfpr[7];
																			$euCodeId	 = $dfpr[8];
																			$brandId	 = $dfpr[9];
																			$frozenCodeId	 = $dfpr[10];
																			$mcPkgId	 = $dfpr[11];
																			$processorId 	 = $dfpr[13];

																			$exportLotId	= $dfpr[12];
																			$selectedDate = dateFormat($dfpr[1]);
																			
																			$preProcessor	= $dfpr[14];
																			$fish		= $dfpr[15];
																			$processCode 	= $dfpr[16];
																			$freezingStage 	= $dfpr[17];
																			$eUCode 	= $dfpr[18];
																			$brand 		= $dfpr[19];
																			$frozenCode 	= $dfpr[20];
																			$mCPackingCode  = $dfpr[21];
																			
																			$gFPEntryId = $dfpr[22];
																			$rmlot	= $dfpr[25];
																			$rmlotId=$dfpr[26];
																			if($rmlotId!='')
																			{
																				$rmlotName		= $objManageRMLOTID->getLotName($rmlotId);
																			}
																		/*	if ($summary!="") {
																				//$gFPEntryId = $dfpr[22];
																				$gradeMasterRecords	= $rmFreezingCalendarReportObj->getGroupedFPGradeRecs($gFPEntryId);
																			} 
																			else
																			{
																				$gradeMasterRecords	= $rmFreezingCalendarReportObj->getDetailedFPGradeRecs($dailyFrozenPackingEntryId);
																			}*/

																			if ($summary && $rmlot=='0') {
																				$gFPEntryId = $dfpr[22];
																				$gradeMasterRecords	= $rmFreezingCalendarReportObj->getGroupedFPGradeRecsold($gFPEntryId);
																			} 
																			else if($summary=="" && $rmlot=='0')
																			{
																				$gradeMasterRecords	= $rmFreezingCalendarReportObj->getDetailedFPGradeRecsold($dailyFrozenPackingEntryId);
																			}
																			else if($summary && $rmlot=='1') {
																				$gFPEntryId = $dfpr[22];
																				$gradeMasterRecords	= $rmFreezingCalendarReportObj->getGroupedFPGradeRecsLot($gFPEntryId);
																			} 
																			else if($summary=="" && $rmlot=='1')
																			{
																				$gradeMasterRecords	= $rmFreezingCalendarReportObj->getDetailedFPGradeRecsLot($dailyFrozenPackingEntryId);
																			}
																			
																			$allocatedPOId	= $dfpr[23];
																			$allocatedPO	= $dfpr[24];
																			
																	?>
																	<tr  <?=$listRowMouseOverStyle?>>	
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; display:none;">
																			<?=$allocatedPO;?>
																			<input type="hidden" name="allocatePOId_<?=$i?>" id="allocatePOId_<?=$i?>" value="<?=$allocatedPOId?>" readonly />
																			<input type="hidden" name="FPEntryId_<?=$i?>" id="FPEntryId_<?=$i?>" value="<?=$gFPEntryId?>" readonly />
																		</td>
																		
																			<?php
																				if ($dtSelect && $searchType) {
																			?>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selectedDate?></td>
																		<?php
																			}
																		?>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$rmlotName?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$preProcessor?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fish?></td>	
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$freezingStage?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$eUCode;?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$brand?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$frozenCode;?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$mCPackingCode?></td>
																		<td>
																			<table border="0" cellpadding="2" cellspacing="0" width="100%">
																				<?php
																					if ( sizeof($gradeMasterRecords)) {
																				?>
																				<?php
																				foreach ( $gradeMasterRecords as $gl ) {
																					$displayGrade		=	$gl[0];
																					$numMC			=	$gl[1];
																					$totalNumMc += $numMC;
																					$numLooseSlab		= 	$gl[2];	
																					$totalLS += $numLooseSlab;
																					$frozenQty		= 	$gl[3];
																					$totalFrozenQty += $frozenQty;
																					$netQty			= 	$gl[4];
																					$totalNetQty += $netQty;
																				?>
																				<tr>	
																					<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" ><?=$displayGrade?></td>
																					<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right"><?=($numMC!=0)?$numMC:"&nbsp;";?></td>
																					<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right"><?=($numLooseSlab!=0)?$numLooseSlab:"&nbsp;";?></td>
																					<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right"><?=($frozenQty!=0)?$frozenQty:"&nbsp;";?></td>
																					<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px;" align="right"><?=($netQty!=0)?$netQty:"&nbsp;";?></td>	
																			   </tr>
																				<?php
																					}
																				  } 
																				  ?>
																			</table>
																		</td>	
																	</tr>
																	<?
																		}
																	?>
																	<?php
																		//if ($dateRange && $details) $colspan = 9;
																		//else $colspan = 8;
																		if ($dtSelect && $searchType)  $colspan = 10;
																		else $colspan = 9;
																	?>
																	<tr bgcolor="White">	
																		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="<?=$colspan?>" align="right">Total:</td>	
																		<td>
																			<table border="0" cellpadding="1" cellspacing="1" width="100%">
																				<tr>	
																					<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;">&nbsp;</td>
																					<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																						<strong><?=($totalNumMc!=0)?$totalNumMc:"&nbsp;";?></strong>
																					</td>
																					<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																						<strong><?=($totalLS!=0)?$totalLS:"&nbsp;";?></strong>
																					</td>
																					<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																						<strong><?=($totalFrozenQty!=0)?number_format($totalFrozenQty,2,'.',','):"&nbsp;";?></strong>
																					</td>
																					<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; " align="right">
																						<strong><?=($totalNetQty!=0)?number_format($totalNetQty,2,'.',','):"&nbsp;";?></strong>
																					</td>	
																				</tr>
																			</table>
																			<input type="hidden" name="hdnRowCount" id="hdnRowCount" value="<?=$i?>" readonly />
																		</td>	
																	</tr>
																	
																</table>
															</td>
														</tr>
														<?php
															} else if($searchMode!="" && $displayMnth=="") {
														?>
														<tr bgcolor="white">
															<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
														</tr>	
														<?
														}
														?>


														<?
														if( sizeof($displayMnth) > 0 )
														{
														?>
														<tr bgcolor="#FFFFFF">
															<td style="padding-left:10px; padding-right:10px;">
																<table cellpadding="1"  width="100%" cellspacing="1" border="0" align="center" bgcolor="#999999">
																	<? if(sizeof($displayMnth)>0)   
																	{
																		foreach($displayMnth as $disp) {
																		?>
																		<tr  bgcolor="#f2f2f2" align="center">
																			<td class="listing-head" style="padding-left:5px; padding-right:5px;"><?=$disp[0]?></td>
																			<td class="listing-head" style="padding-left:5px; padding-right:5px;" bgcolor="#ffffff">
																			<? 
																			$fromDate=$disp[1].'-'.'01';
																			$toDate=$disp[1].'-'.'31';
																			$dailyFrozenPackingRecordsSearch = $rmFreezingCalendarReportObj->getDFPForADate($fromDate, $toDate, $details, $summary, $selProcessor);
																			
																			if(sizeof($dailyFrozenPackingRecordsSearch)>0) { ?>
																			<?//=$disp[1]?>
																				<table cellpadding="1" cellspacing="1" width="100%" align="center" bgcolor="#999999">
																					<tr  bgcolor="#f2f2f2" align="center">
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2" nowrap>Rm Lot Id</td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Processor</td>	
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Fish</td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Process Code</td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Freezing Stage</td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">EU Code</td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Brand</td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Frozen Code&nbsp; </td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">MC Pkg</td>
																						<td class="listing-head" style="padding-left:5px; padding-right:5px;" >Other Details</td>	
																					</tr>
																					<tr bgcolor="#f2f2f2" align="center">
																						<TD>
																							<table cellpadding="1" cellspacing="1" width="100%">
																								<TR>
																									<td class="listing-head" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >Grade</td>
																									<td class="listing-head" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">MC </td>
																									<td class="listing-head" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">LS</td>
																									<td class="listing-head" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">Frozen<br> Qty</td>
																									<td class="listing-head" nowrap width="100" style="padding-left:5px; padding-right:5px;" align="right">Net<br>Qty</td>
																								</TR>
																							</table>
																						</TD>
																					</tr>
																						<?php
																					$i = 0;
																					$totalNumMc	= 0;
																					$totalLS	= 0;
																					$totalFrozenQty = 0;
																					$totalNetQty	= 0;
																					foreach ($dailyFrozenPackingRecordsSearch as $dfpr) {
																						$i++;
																						$dailyFrozenPackingMainId	=	$dfpr[0];
																						$dailyFrozenPackingEntryId	=	$dfpr[4];
																						$unitId			=	$dfpr[2];
																						if($dfpr[3]!=0) $lotId	=	$dfpr[3];
																						$fishId		= $dfpr[5];
																						$processCodeId 	= $dfpr[6];
																						$freezingStageId = $dfpr[7];
																						$euCodeId	 = $dfpr[8];
																						$brandId	 = $dfpr[9];
																						$frozenCodeId	 = $dfpr[10];
																						$mcPkgId	 = $dfpr[11];
																						$processorId 	 = $dfpr[13];

																						$exportLotId	= $dfpr[12];
																						$selectedDate = dateFormat($dfpr[1]);
																						
																						$preProcessor	= $dfpr[14];
																						$fish		= $dfpr[15];
																						$processCode 	= $dfpr[16];
																						$freezingStage 	= $dfpr[17];
																						$eUCode 	= $dfpr[18];
																						$brand 		= $dfpr[19];
																						$frozenCode 	= $dfpr[20];
																						$mCPackingCode  = $dfpr[21];
																						
																						$gFPEntryId = $dfpr[22];
																						$rmlot	= $dfpr[25];
																						$rmlotId=$dfpr[26];
																						if($rmlotId!='')
																						{
																							$rmlotName		= $objManageRMLOTID->getLotName($rmlotId);
																						}
																					/*	if ($summary!="") {
																							//$gFPEntryId = $dfpr[22];
																							$gradeMasterRecords	= $rmFreezingCalendarReportObj->getGroupedFPGradeRecs($gFPEntryId);
																						} 
																						else
																						{
																							$gradeMasterRecords	= $rmFreezingCalendarReportObj->getDetailedFPGradeRecs($dailyFrozenPackingEntryId);
																						}*/

																						if ($summary && $rmlot=='0') {
																							$gFPEntryId = $dfpr[22];
																							$gradeMasterRecords	= $rmFreezingCalendarReportObj->getGroupedFPGradeRecsold($gFPEntryId);
																						} 
																						else if($summary=="" && $rmlot=='0')
																						{
																							$gradeMasterRecords	= $rmFreezingCalendarReportObj->getDetailedFPGradeRecsold($dailyFrozenPackingEntryId);
																						}
																						else if($summary && $rmlot=='1') {
																							$gFPEntryId = $dfpr[22];
																							$gradeMasterRecords	= $rmFreezingCalendarReportObj->getGroupedFPGradeRecsLot($gFPEntryId);
																						} 
																						else if($summary=="" && $rmlot=='1')
																						{
																							$gradeMasterRecords	= $rmFreezingCalendarReportObj->getDetailedFPGradeRecsLot($dailyFrozenPackingEntryId);
																						}
																						
																						$allocatedPOId	= $dfpr[23];
																						$allocatedPO	= $dfpr[24];
																						
																				?>
																				<tr  <?=$listRowMouseOverStyle?>>	
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; display:none;">
																						<?=$allocatedPO;?>
																						<input type="hidden" name="allocatePOId_<?=$i?>" id="allocatePOId_<?=$i?>" value="<?=$allocatedPOId?>" readonly />
																						<input type="hidden" name="FPEntryId_<?=$i?>" id="FPEntryId_<?=$i?>" value="<?=$gFPEntryId?>" readonly />
																					</td>
																					
																						<?php
																							if ($dtSelect && $searchType) {
																						?>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selectedDate?></td>
																					<?php
																						}
																					?>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$rmlotName?></td>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$preProcessor?></td>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fish?></td>	
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$freezingStage?></td>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$eUCode;?></td>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$brand?></td>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$frozenCode;?></td>
																					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$mCPackingCode?></td>
																					<td>
																						<table border="0" cellpadding="2" cellspacing="0" width="100%">
																							<?php
																								if ( sizeof($gradeMasterRecords)) {
																							?>
																							<?php
																							foreach ( $gradeMasterRecords as $gl ) {
																								$displayGrade		=	$gl[0];
																								$numMC			=	$gl[1];
																								$totalNumMc += $numMC;
																								$numLooseSlab		= 	$gl[2];	
																								$totalLS += $numLooseSlab;
																								$frozenQty		= 	$gl[3];
																								$totalFrozenQty += $frozenQty;
																								$netQty			= 	$gl[4];
																								$totalNetQty += $netQty;
																							?>
																							<tr>	
																								<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" ><?=$displayGrade?></td>
																								<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right"><?=($numMC!=0)?$numMC:"&nbsp;";?></td>
																								<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right"><?=($numLooseSlab!=0)?$numLooseSlab:"&nbsp;";?></td>
																								<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right"><?=($frozenQty!=0)?$frozenQty:"&nbsp;";?></td>
																								<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px;" align="right"><?=($netQty!=0)?$netQty:"&nbsp;";?></td>	
																						   </tr>
																							<?php
																								}
																							  } 
																							  ?>
																						</table>
																					</td>	
																				</tr>
																				<?
																					}
																				?>
																				<?php
																					//if ($dateRange && $details) $colspan = 9;
																					//else $colspan = 8;
																					if ($dtSelect && $searchType)  $colspan = 10;
																					else $colspan = 9;
																				?>
																				<tr bgcolor="White">	
																					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="<?=$colspan?>" align="right">Total:</td>	
																					<td>
																						<table border="0" cellpadding="1" cellspacing="1" width="100%">
																							<tr>	
																								<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;">&nbsp;</td>
																								<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																									<strong><?=($totalNumMc!=0)?$totalNumMc:"&nbsp;";?></strong>
																								</td>
																								<td class="listing-item" nowrap width="50" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																									<strong><?=($totalLS!=0)?$totalLS:"&nbsp;";?></strong>
																								</td>
																								<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																									<strong><?=($totalFrozenQty!=0)?number_format($totalFrozenQty,2,'.',','):"&nbsp;";?></strong>
																								</td>
																								<td class="listing-item" nowrap width="100" style="padding-left:5px; padding-right:5px; " align="right">
																									<strong><?=($totalNetQty!=0)?number_format($totalNetQty,2,'.',','):"&nbsp;";?></strong>
																								</td>	
																							</tr>
																						</table>
																						<input type="hidden" name="hdnRowCount" id="hdnRowCount" value="<?=$i?>" readonly />
																					</td>	
																				</tr>
																			</table>
																			<? }
																			else
																			{
																			?>
																			<table >
																				<tr>
																					<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																				</tr>
																			</table>
																			<?php
																			}
																			?>
																		</td>
																	</tr>
																	<?
																	}
																	?>
																	<?
																	}
																	?>
																</table>
															</td>
														</tr>	
														<? } ?>
														<tr> 
															<td colspan="3" align="center" height="5"></td>
														</tr>
														<tr>                        
															<td colspan="4" align="center">
															<?php 
															if($confirm==true && (sizeof($dailyFrozenPackingRecords)>0) && $singleDay!="" && $details) {
															?>
															<input name="cmdConfirm" type="submit" class="button" id="cmdConfirm" value=" Confirm " <? if($confirmed || (sizeof($dailyFrozenPackingRecords)<=0)) echo "disabled";?> onclick="return validateUpdateFrozenPackingReport(document.frmFrozenPackingReport);">
															<? }?>	&nbsp;&nbsp;&nbsp;&nbsp;<? if($print==true){?>
																			<!--input type="button" name="Submit2" value=" View / Print" class="button" onclick="return printWindow('PrintDailyCatchSumary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>',700,600);"-->
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
									<!-- Form fields end   -->
								</td>
							</tr>		
						</table>
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