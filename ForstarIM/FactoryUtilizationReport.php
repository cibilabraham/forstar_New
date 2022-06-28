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
	


	if ($p["selCompany"]) $selCompany = $p["selCompany"];	
	if ($p["searchType"]) $searchType = $p["searchType"];	
	if($searchType!="")
	{
		($searchType=="totalsummary")?$seltotalsummary="selected":$seltotalsummary="";
		($searchType=="unitwisesummary")?$selunitwisesummary="selected":$selunitwisesummary="";
		($searchType=="unitwise")?$selunitwise="selected":$selunitwise="";
	}
	if ($p["unit"]) $unit = $p["unit"];
	
	// Change Status PO
	$purchaseOrderId	= $p["csPurchaseOrder"];
	if ($purchaseOrderId>0) {
		$enableSearch = true;
		// Get All Invoices
		$invoiceRecs = $factoryUtilizationReportObj->getAllInvoices($purchaseOrderId);
	}
	
	if ($selDate || ($dateFrom && $dateTill)) {
		$fDate = ($selDate)?mysqlDateFormat($selDate):mysqlDateFormat($dateFrom);
		$tDate = ($selDate)?mysqlDateFormat($selDate):mysqlDateFormat($dateTill);
		# Get Processors
		$companyRecs = $factoryUtilizationReportObj->getCompany($fDate,$tDate);
		$units = $factoryUtilizationReportObj->getAllUnit($fDate,$tDate);
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
				$relAllocationRec=$factoryUtilizationReportObj->releaseAllocation($csPurchaseOrderId, $csInvoiceId, $userId);
				if ($relAllocationRec) $releaseAllocation = true;				
			}
		}
		else if ($changeStatus=="INVR"){
				if ($csPurchaseOrderId>0) {
					$relInvoiceRec = $factoryUtilizationReportObj->releaseInvoice($csPurchaseOrderId, $csInvoiceId, $userId);				
					if ($relInvoiceRec) $releaseInvoice = true;
				}
		}
	}
		
	if (($p["cmdSearch"]!="" && ($details!="" || $summary!="")) || (($statusChangeMode || $enableSearch) && ($details!="" || $summary!="")) || ($dateFrom!="" && $dateTill!="")) {

		
		//if ($selDate) $selectDate = mysqlDateFormat($selDate);
		if ($dateFrom) $fromDate = mysqlDateFormat($dateFrom);
		if ($dateTill) $toDate	 = mysqlDateFormat($dateTill);
		if($searchType=="unitwise")
		{
			$dailyFrozenPackingRecords = $factoryUtilizationReportObj->getDFPForADate($fromDate, $toDate, $selCompany,$unit);
		}
		else if($searchType=="totalsummary")
		{
			$dailyFrozenPackingRecords = $factoryUtilizationReportObj->getDFPForTotal($fromDate, $toDate, $selCompany);
		}
		
		$searchMode = true;
	}

	#Confirm the Records
	if ($p["cmdConfirm"]!="") {
		$selDate	= $p["selDate"];
 		if ($selDate)  {
			$selectDate = mysqlDateFormat($selDate);
			$updateDailyFrozenPackingRecords = $factoryUtilizationReportObj->updateDailyFrozenPackingRecords($selectDate);
  		}
	}
	

	# Display heading
	$heading = $label_FactoryUtilizationReport;

	$ON_LOAD_PRINT_JS	= "libjs/factoryutilizationreport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");


	###FIND DATE AND YEAR BETWEEN TWO DATES
	

?>
<form name="frmFactoryUtilizationReport" action="FactoryUtilizationReport.php" method="Post">
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
																										<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" onchange="submitForm('supplyFrom','supplyTill',document.frmFactoryUtilizationReport);" />
																									</td>
																									<td class="fieldName">Till:</td>
																									<td nowrap="true">
																										<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off" onchange="submitForm('supplyFrom','supplyTill',document.frmFactoryUtilizationReport);" />
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
																								<TD class="fieldName">Company</TD>
																								<td nowrap="true">
																									<select name="selCompany" id="selCompany">
																										<option value="">--Select All--</option>
																										<?php
																										foreach ($companyRecs as $cr) {
																											$companyId 	= $cr[0];
																											$companyName	= $cr[1];
																											$selected = ($selCompany==$companyId)?"selected":"";
																										?>
																										<option value="<?=$companyId?>" <?=$selected?>><?=$companyName?></option>
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
																							<legend class="fieldName">Search Options </legend>
																							<table width="200" border="0">
																								<tr> 
																									<td class="fieldName">Type:</td>
																									<td nowrap="true">
																										<select name="searchType" id="searchType" onchange="getField();">
																											<option  value="">Select</option>
																											<option  value="totalsummary" <?=$seltotalsummary?>>Total Summary</option>
																											<option  value="unitwisesummary" <?=$selunitwisesummary?>>Unit Wise Summary</option>
																											<option  value="unitwise" <?=$selunitwise?>>Unit</option>
																										</select>
																									</td>
																								</tr>
																								<tr class="rowtype" id="unitwise" style="display:none;">
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
																													if($unit == $unitval[0]) $sel = 'selected';
																																
																													echo '<option '.$sel.' value="'.$unitval[0].'">'.$unitval[1].'</option>';
																												}
																											}
																										?>			
																										</select>	
																									</td>
																								</tr>
																								<tr >
																									<td colspan="2">
																										<input id="cmdSearch" class="button" type="submit" onclick="return validateFactoryUtilizationReportSearch();" value="Search" name="cmdSearch">
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</TD>
																				</TR>
																			</table>
																		</td>
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
										
<!--------------------------------------------$searchType=unitwise------------------------------------------------------------->
													<? if($searchType=="unitwise") { ?>
													<table width="100%" border="0" cellpadding="2" cellspacing="0">
														<?
														if( sizeof($dailyFrozenPackingRecords) > 0 )
														{
															$avgperformance=0;
															if($unit!="")
															{
																$stndProduction=$factoryUtilizationReportObj->getStandardProduction($unit);
															}
														?>
														
														<tr bgcolor="#FFFFFF">
															<td style="padding-left:10px; padding-right:10px;">
																<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >STD. Production</td>
																		
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="<?=sizeof($dailyFrozenPackingRecords)?>"><?=$stndProduction?></td>
																		
																	</tr>
																	<tr  bgcolor="#f2f2f2"  >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Date</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ ?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=dateFormat($dfp[1])?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" >Production</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ ?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=$dfp[2]?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Performance</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ 
																		$performance=$dfp[2]/$stndProduction;
																		$avgperformance+=$performance;
																		?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=number_format((float)$performance, 2, '.', '')?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Cum. Performance</td>
																		<? $cumperformance=($avgperformance/sizeof($dailyFrozenPackingRecords));?>
																		
																		<td bgcolor="#ffffff" class="listing-head" colspan="<?=(sizeof($dailyFrozenPackingRecords))?>" style="padding-left:5px; padding-right:5px;"  align="right"><?=number_format((float)$cumperformance, 2, '.', '')?></td>
																		
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
														<tr> 
															<td colspan="3" align="center" height="5"></td>
														</tr>
												</table>
												<?
												}
												?>
<!--------------------------------------------$searchType=unitwise ends ------------------------------------------------------------->
<!--------------------------------------------$searchType=unitwisesummary starts------------------------------------------------------------->

												<? if($searchType=="unitwisesummary")	{
												?>
												<table width="100%" border="0" cellpadding="2" cellspacing="0">
													<?
													foreach($units as $unit)
													{
														$dailyFrozenPackingRecords=$factoryUtilizationReportObj->getDFPForADate($fromDate, $toDate, $selCompany,$unit[0]);
														if( sizeof($dailyFrozenPackingRecords) > 0 )
														{
															$avgperformance=0;
															if($unit!="")
															{
																$stndProduction=$factoryUtilizationReportObj->getStandardProduction($unit[0]);
															}
														?>
														<tr bgcolor="#FFFFFF">
															<td style="padding-left:10px; padding-right:10px;">
																<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Unit</td>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  colspan="<?=sizeof($dailyFrozenPackingRecords)?>"><?=$unit[1]?></td>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >STD. Production</td>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  colspan="<?=sizeof($dailyFrozenPackingRecords)?>"><?=$stndProduction?></td>
																	</tr>
																	<tr  bgcolor="#f2f2f2"  >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Date</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ ?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=dateFormat($dfp[1])?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" >Production</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ ?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=$dfp[2]?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Performance</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ 
																		$performance=$dfp[2]/$stndProduction;
																		$avgperformance+=$performance;
																		?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=number_format((float)$performance, 2, '.', '')?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; ">Cum. Performance</td>
																		<? $cumperformance=($avgperformance/sizeof($dailyFrozenPackingRecords));?>
																		<td bgcolor="#ffffff" class="listing-head"  style="padding-left:5px; padding-right:5px;" colspan="<?=(sizeof($dailyFrozenPackingRecords))?>" align="right"><?=number_format((float)$cumperformance, 2, '.', '')?></td>
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
														<tr> 
															<td colspan="3" align="center" height="5"></td>
														</tr>
														<tr> 
															<td colspan="2"  height="10" ></td>
														</tr>
													<?
													}
													?>
												</table>
												<? }
												?>
<!--------------------------------------------$searchType=unitwisesummary ends------------------------------------------------------------->
<!--------------------------------------------$searchType=totalsummary starts------------------------------------------------------------->

												<? 
												if($searchType=="totalsummary")
												{
												?>
													<table width="100%" border="0" cellpadding="2" cellspacing="0">
													<?
														if( sizeof($dailyFrozenPackingRecords) > 0 )
														{
															$avgperformance=0;
														?>
														<tr bgcolor="#FFFFFF">
															<td style="padding-left:10px; padding-right:10px;">
																<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >STD.Production</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ ?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=$dfp[4]?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2"  >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Date</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ ?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=dateFormat($dfp[1])?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" >Production</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ ?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=$dfp[2]?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; " >Performance</td>
																		<? foreach($dailyFrozenPackingRecords as $dfp){ 
																		$performance=$dfp[5];
																		$avgperformance+=$performance;
																		?>
																		<td bgcolor="#ffffff" class="listing-head" style="padding-left:5px; padding-right:5px;"  align="center"><?=number_format((float)$performance, 2, '.', '')?></td>
																		<? } ?>
																	</tr>
																	<tr  bgcolor="#f2f2f2" >	
																		<td class="listing-head" style="padding-left:5px; padding-right:5px; ">Cum. Performance</td>
																		<? $cumperformance=($avgperformance/sizeof($dailyFrozenPackingRecords));?>
																		<td bgcolor="#ffffff" class="listing-head"  style="padding-left:5px; padding-right:5px;"  colspan="<?=(sizeof($dailyFrozenPackingRecords)-1)?>"></td>
																		<td bgcolor="#ffffff" class="listing-head"  style="padding-left:5px; padding-right:5px;"  align="right"><?=number_format((float)$cumperformance, 2, '.', '')?></td>
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
														<tr> 
															<td colspan="3" align="center" height="5"></td>
														</tr>
														<tr> 
															<td colspan="2"  height="10" ></td>
														</tr>
													</table>
												<?
												}
												?>
<!--------------------------------------------$searchType=totalsummary ends------------------------------------------------------------->
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

<?php
if($p["cmdSearch"]!="")
{
?>
<script>
	getField();
</script>
<?
}
?>
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