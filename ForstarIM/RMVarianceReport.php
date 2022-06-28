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
	if (($p["cmdSearch"]!="") ||  ($dateFrom!='' &&  $dateTill!='')) 
	{

		if ($dateFrom) $fromDate  = mysqlDateFormat($dateFrom);
		if ($dateTill) $toDate  = mysqlDateFormat($dateTill);
			$rmlotids=$p['rmlotid'];
			$supplier=$p['supplier'];
			if($rmlotids=="")
			{
				$supplier='';
			}
			if($dateFrom!='' && $dateTill!='')
			{
				$rmLotRec = $rmVarianceReportObj->getRmLot($fromDate,$toDate);
				
				if($rmlotids!='')
				{
					$supplierRecords=$rmVarianceReportObj->getSupplierData($rmlotids);
				}
				$rmLotRecords = $rmVarianceReportObj->getRmLotRec($fromDate,$toDate,$rmlotids,$supplier);
				$rmLotRecordsSize=sizeof($rmLotRecords);
				// sizeof($rmLotRecords);
			}
		/*if($dateFrom!='' && $dateTill!='' && $rmlotid=='')
		{
			$rmLotRecords = $rmVarianceReportObj->getRmLOT($fromDate,$toDate);
			$rmLotRec = $rmVarianceReportObj->getRmLOT($fromDate,$toDate);
		}
		else if($dateFrom!='' && $dateTill!='' && $rmlotid!='')
		{
			$rmLotRecords = $rmVarianceReportObj->getRmLOT($fromDate,$toDate,$rmlotid);
			$supplierRecords=$rmVarianceReportObj->getSupplierData($rmlotid);
		}
		else if($dateFrom!='' && $dateTill!='' && $rmlotid!='' && $supplier!='')
		{
			$rmLotRecords = $rmVarianceReportObj->getRmLOT($fromDate,$toDate,$rmlotid,$supplier);
			$supplierRecords=$rmVarianceReportObj->getSupplierData($rmlotid);
		}*/
		//$rmVarianceRecords = $rmVarianceReportObj->getDFPForADate($fromDate, $toDate);
		//printr($dailyFrozenPackingRecords);

		
		$searchMode = true;
	}

	# Display heading
	$heading = $label_RMVarianceReport;

	$ON_LOAD_PRINT_JS	= "libjs/rmvariancereport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmRMVarianceReport" action="RMVarianceReport.php" method="Post">
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
												<!--input type="button" name="Submit" value=" View / Print" class="button" onClick="return printWindow('PrintDailyCatchSumary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>',700,600);"-->&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; </td>
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
																							<!--   -->				<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off"    onchange="submitForm('supplyFrom','supplyTill', frmRMVarianceReport);"/>
																												</td>
																												<td class="fieldName">Till:</td>
																												<td nowrap="true">
																													<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off"    onchange="submitForm('supplyFrom','supplyTill', frmRMVarianceReport);"/>
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
																					<TD valign="top">
																					</TD>
																				</tr>
																			</table>
																		</TD>
																		<TD valign="top">
																			<table width="200" border="0" cellpadding="0" cellspacing="0">
																				 <tr>
																					<td valign="top">
																						<fieldset>
																							<legend class="fieldName">Search Options </legend>
																								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																									<tr>
																										<td  class="fieldName" nowrap>Rm Lot Id</td>
																										<td><select name='rmlotid' id='rmlotid'>
																											<option value=''>--SelectAll--</option>
																												<? foreach($rmLotRec as $rmlotVal)
																													{ 
																														($rmlotids==$rmlotVal[0])? $selected="selected":$selected="";
																													?>
																														<option value='<?=$rmlotVal[0]?>' <?=$selected?>><?=$rmlotVal[1]?></option>
																													<?
																													}
																													?>
																											
																											</select>
																										</td>
																									</tr>
																									<tr>
																										<td  class="fieldName">Supplier</td>
																										<td>
																											<select name='supplier' id='supplier'>
																												<option value=''>--SelectAll--</option>
																											<?
																											foreach($supplierRecords as $supplierVal)
																											{
																												($supplier==$supplierVal[0])? $sel="selected":$sel="";
																											?>
																												<option value='<?=$supplierVal[0]?>' <?=$sel?>><?=$supplierVal[1]?></option>
																											<?
																											}
																											?>
																											</select>
																										</td>
																									</tr>
																									<tr>
																										<td>&nbsp;</td>
																										<td class="listing-item">
																											<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateRMVarianceReportSearch();"/>
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
																			</TD>
																		</TR>
																	</table>
																</TD>
															<!--  Change Status End here   -->
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
																		<tr  bgcolor="#f2f2f2" align="center">	
																			<td class="listing-head" style="padding-left:5px; padding-right:5px; height:40px" colspan="4" >Procurement</td>
																			<td class="listing-head" style="padding-left:5px; padding-right:5px; " colspan="7" >Receiving</td>
																		</tr>
																		<tr  bgcolor="#f2f2f2" align="center" >
																			<!--td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">PO</td-->
																			<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px"  nowrap>RM Lot Id</td>
																			
																			<td colspan="3" ><table width="332px"><tr >
																			<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999;"  nowrap>Supplier</td>
																			<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999;"  >Gate Supervisor</td>
																			<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px; "  nowrap>Date</td>
																			</tr></table></td>
																			
																			<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999;"  nowrap>Supervisor</td>
																			<td colspan="6" ><table width="600px"><tr >	
																			<td  class="listing-head"   style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999;" nowrap>Date</td>
																			<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999; height: 38px" nowrap>Variety </td>
																			<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:170px; border-right:1px solid #999999;" nowrap>Item Name</td>
																			<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999;" nowrap>Count</td>
																			<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999;"  nowrap>Qty</td>	
																			<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; " nowrap>Soft%</td>
																			</tr></table></td>
																		</tr>
																		<?php
																		$totalCount	= 0;
																		$totalQty	= 0;
																		foreach($rmLotRecords as $rmlot)
																		{
																			($rmlot[2]!=0)? $rmlotid=$rmlot[2]:$rmlotid=$rmlot[0];
																			$rmVarianceRecords = $rmVarianceReportObj->getDFPForADateRmLot($rmlotid,$supplier);
																		?>

																		<tr>
																			<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$rmlot[1];?></td>
																			
																			<td colspan="10" bgcolor="#fff">
																				<table  cellpadding='1' cellspacing='1' width="100%" border="0" bgcolor="#ccc">
																				<?php
																				$i = 0;
																				
																				foreach ($rmVarianceRecords as $dfpr) 
																				{
																					$supplierNm=$dfpr[0];
																					$gateSupervisor=$dfpr[1];
																					$date=dateformat($dfpr[2]);
																					$supervisor=$dfpr[3];
																					$supplierId=$dfpr[6];
																					$farmId=$dfpr[7];
																					$WeighmentRecords	= $rmVarianceReportObj->getGroupedWeighment($rmlot[0],$supplierId,$farmId);
																					$i++;
																					/*$dailyFrozenPackingMainId	=	$dfpr[0];
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
																					if ($summary) {
																						$gFPEntryId = $dfpr[22];
																						$gradeMasterRecords	= $rmVarianceReportObj->getGroupedFPGradeRecs($gFPEntryId);
																					} else {
																						$gradeMasterRecords	= $rmVarianceReportObj->getDetailedFPGradeRecs($dailyFrozenPackingEntryId);
																					}
																					
																					$allocatedPOId	= $dfpr[23];
																					$allocatedPO	= $dfpr[24];
																					*/

																				?>
																					<tr  bgcolor="#fff">	
																						<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$supplierNm?></td>	
																						<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$gateSupervisor?></td>
																						<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$date?></td>
																						<!--<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$eUCode;?></td>
																						<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$brand?></td>
																						<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$frozenCode;?></td>
																						<td class="listing-item" style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>
																						<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>
																						<td class="listing-item" style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>
																						<td class="listing-item" style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>-->
																						<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$supervisor?></td>
																							<!--<td>
																							<table border="0" cellpadding="2" cellspacing="0" width="100%">

																							</table>
																						</td>-->
																						<td colspan="6"  >&nbsp;
																							<table border="0" cellpadding="1" cellspacing="1"  width="100%"  bgcolor="#fff" >
																								<?php
																								if ( sizeof($WeighmentRecords)) 
																								{
																								?>
																								<?php
																									foreach ( $WeighmentRecords as $wr ) 
																									{
																										$weightmentDate		=	dateformat($wr[0]);
																										$variety			=	$wr[1];
																										$itemName			=	$wr[2];
																										$count			=	$wr[3];
																										$totalCount+=$count;
																										$weight		=$wr[4];	
																										$totalQty+=$weight;
																										$soft		=$wr[5];	
																									
																								?>
																									<tr>	
																										<td class="listing-item" nowrap style="width:100px;  padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >&nbsp;<?=$weightmentDate?></td>
																										<td class="listing-item" nowrap style="width:100px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >&nbsp;<?=$variety?></td>
																										<td class="listing-item" nowrap style="width:170px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >&nbsp;<?=$itemName?></td>
																										<td class="listing-item" nowrap style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">&nbsp;<?=$count;?></td>
																										<td class="listing-item" nowrap style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">&nbsp;<?=$weight;?></td>
																										<td class="listing-item" nowrap style="width:50px; padding-left:5px; padding-right:5px; " align="right">&nbsp;<?=$soft;?></td>
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
																					</table>
																				</td>
																			</tr>
																			<? 
																				}
																			?>
																			<?php
																				if ($dateRange && $details) $colspan = 10;
																				else $colspan = 10;
																			?>
																			<tr bgcolor="White">	
																				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="5" align="right">Total:</td>	
																				<td>
																					<table border="0" cellpadding="1" cellspacing="1" width="100%">
																						<tr>
																							<td colspan="3" class="listing-item" nowrap  style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																							&nbsp;</td>
																							<td class="listing-item" nowrap  style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																								<strong><?=$totalCount?></strong>
																							</td>
																							<td class="listing-item" nowrap  style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																								<strong><?=$totalQty?></strong>
																							</td>
																							<td style='width:59px;'>&nbsp;</td>
																						</tr>
																					</table>
																					<input type="hidden" name="hdnRowCount" id="hdnRowCount" value="<?=$i?>" readonly />
																				</td>	
																			</tr>
																			<?php
																				} else if($searchMode!="") {
																			?>
																			<!--<tr bgcolor="white">
																				<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																			</tr>-->	
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
														<td colspan="3" align="center" height="5"></td>
													</tr>
													<tr> 
														<? if($editMode){?>
														<?} else{?>
														<td colspan="4" align="center"><? if($print==true){?><input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('PrintRMVarianceReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTill?>&rmlotid=<?=$rmlotids?>&supplier=<?=$supplier?>',700,600);" <? if ($rmLotRecordsSize==0 && $rmlotid=="" &&	$supplier=="") echo "disabled";?> ><? }?></td>
														<?} ?>
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