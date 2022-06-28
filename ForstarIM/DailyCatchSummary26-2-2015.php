<?php
	require("include/include.php");
	require_once("lib/DailyCatchSummary_ajax.php");
	$err			= 	"";
	$errDel			= 	"";	
	$checked		=	"";
	$selectUnit		=	"";
	$landingCenterId	=	"";
	$selectSupplier		=	"";
	$fishId			=	"";
	$advSearch		= 	false;

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
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	if ($g["filterType"]!="") $filterType = $g["filterType"];
	else $filterType = $p["filterType"];

	#Select a day or date range SD-> Single Date/ DR-> Date Range
	if ($g["dateSelection"]!="") $dateSelection = $g["dateSelection"];
	else $dateSelection = $p["dateSelection"];
	if ($dateSelection=='SD'&& $dateSelection!='DR' || $dateSelection=="") {
		$singleDay = "Checked";
	} else if ($dateSelection=='DR') {
		$dateRange = "Checked";
	} else {
		$dateSelection = "";
	}

	#Date From which challan SCD-> Supplier Challan Date, WCD-> WT challan Date
	if ($g["dateSelectFrom"]!="") $dateSelectFrom = $g["dateSelectFrom"];
	else $dateSelectFrom = $p["dateSelectFrom"];
	if ($dateSelectFrom=='SCD') {
		$supplierChallanDate = "Checked";
	} else if($dateSelectFrom=='WCD')  {
		$wtChallanDate = "Checked";
	} else {
		$wtChallanDate = "Checked";
	}

	#QS-> Quick Search / AS-> Advanced Search
	if ($g["searchMode"]!="")	$searchMode = $g["searchMode"];
	else $searchMode = $p["searchMode"];
	if ($searchMode=='QS') {
		$quickSearch = "Checked";
	} else if($searchMode=='AS') {
		$advancedSearch = "Checked";
	} else {
		$searchMode = "";
	}

	#Select a Date (Also Getting from Home page)
	if ($g["selDate"]!="") $selDate	= $g["selDate"];
	else $selDate	= $p["selDate"];
 	if ($selDate) {		
		$selectADate = mysqlDateFormat($selDate);
  	}

	# select record between selected date
	$dateFrom = $p["supplyFrom"];
	$dateTill = $p["supplyTill"];

	$fromDate		= mysqlDateFormat($dateFrom);
	$tillDate		= mysqlDateFormat($dateTill);

	$selectUnit		= $p["selUnit"];
	$landingCenterId	= $p["landingCenter"];
	$selectSupplier		= $p["supplier"];
	if ($p["billingCompany"]!="") $billingCompany = $p["billingCompany"];

	if (($dateFrom!="" && $dateTill!="") || $selDate!="") {
	
		$plantRecords = $dailycatchsummaryObj->fetchPlantWiseRecords($fromDate, $tillDate, $selectADate, $dateSelectFrom);
	
		$landingCenterRecords = $dailycatchsummaryObj->fetchLandingCenterRecords($fromDate, $tillDate, $selectUnit, $selectADate, $dateSelectFrom);
	
		if ($filterType=='SW') $selBillingCompany = "";
		else $selBillingCompany = $billingCompany;
	
		$supplierRecords = $dailycatchsummaryObj->fetchSupplierRecords($fromDate, $tillDate, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $selBillingCompany);
	
		if ($filterType=='SW') $selSupplier = $selectSupplier;
		else $selSupplier = "";
		# Get Billing Comapany  Records
		$billingCompanyRecords = $dailycatchsummaryObj->fetchBillingCompanyRecords($fromDate, $tillDate, $selSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom);
	
		#List All Fishes
		$fishMasterRecords = $dailycatchsummaryObj->fetchFishRecords($fromDate, $tillDate, $selectSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $billingCompany);
	
		#Search Criteria
		$fishId		= $p["fish"];
		if ($fishId!="") { 
			$processCodeRecords = $dailycatchsummaryObj->getProcessCodeRecords($fromDate, $tillDate, $fishId, $selectSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $billingCompany);	
		}

	} // Condition check ends here

	$details = $p["details"];
	if ($details)	$checked1 = "Checked";

	$wChallan = $p["wChallan"];
	if ($wChallan) $checked2 = "Checked";

	$proCount = $p["proCount"];
	if ($proCount) $checked3 = "Checked";

	# Also Setting From Home Page
	if ($g["proSummary"]!="") $proSummary = $g["proSummary"];
	else $proSummary = $p["proSummary"];
	if ($proSummary) $checked4 = "Checked";

	$fishCatchSummary = $p["fishCatchSummary"];
	if ($fishCatchSummary) $checked5 = "Checked";

	$wtChallanSummary = $p["wtChallanSummary"];
	if ($wtChallanSummary) $checked6 = "Checked";

	$supplierMemo = $p["supplierMemo"];
	if ($supplierMemo) $checked7 = "Checked";

	$declWtSummary	=	$p["declWtSummary"];
	if ($declWtSummary) $checked8 = "Checked";

	$rateNAmount = $p["rateNAmount"];
	if ($rateNAmount) $rateNAmountCheck = "Checked";

	$RMMatrix = $p["RMMatrix"];
	if ($RMMatrix) $RMMatrixCheck = "Checked";

	# Also Setting From Home Page
	if ($g["localQtyChk"]!="") $localQtyChk = $g["localQtyChk"];
	else $localQtyChk = $p["localQtyChk"];
	if ($localQtyChk) $localQtyChk = "Checked";

	$localQtyReportChk = $p["localQtyReportChk"];
	if ($localQtyReportChk) $localQtyReportChk = "Checked";

	$dailySummary	=	$p["dailySummary"];
	if ($dailySummary)	$dailySummary = "Checked";

	$subSupplierChk = $p["subSupplierChk"];
	if ($subSupplierChk)	$subSupplierChk = "Checked";

	$supSetlmentSummaryChk = $p["supSetlmentSummary"];
	if ($supSetlmentSummaryChk)	$supSetlmentSummaryChk = "Checked";

	$RMRateMatrix	= $p["RMRateMatrix"];	
	if ($RMRateMatrix) $RMRateMatrixChk = "checked";

	$qtySummary	= $p["qtySummary"];
	if ($qtySummary) $qtySummaryChk = "checked";

	$challanSummary	= $p["challanSummary"];
	if ($challanSummary) $challanSummaryChk = "checked";
	

	$searchEnabled = false;
	if ($g["dateSelection"]!="" && $g["dateSelectFrom"] && $g["searchMode"] && $g["proSummary"] && $g["localQtyChk"])  $searchEnabled = true;

	if ($p["cmdSearch"]!="" || $searchEnabled!="") {
		$selectUnit		=	$p["selUnit"];
		$landingCenterId	=	$p["landingCenter"];
		$selectSupplier		=	$p["supplier"];
		$fishId			=	$p["fish"];
		$processId		=	$p["processCode"];
	
	#Using in Advance and Quick Search
		$dailyCatchSummaryRecords = $dailycatchsummaryObj->filterDailyCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	
	#process-Count-Summary
		$processCountSummaryRecords = $dailycatchsummaryObj->filterProcessCountSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	
	#For Selecting Fish - Process - Summary
		$processSummaryRecords = $dailycatchsummaryObj->filterFishProcessSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	
	#For Selecting Fish - Catch - Summary
		$fishWiseCatchSummaryRecords =	$dailycatchsummaryObj->filterFishWiseCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	
	#Wt Challan Summary 
		$wtChallanWiseSummary = $dailycatchsummaryObj->filterWtChallanRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	
	# Get Rec
	//$dateWiseRecords = $dailycatchsummaryObj->fetchDateWiseRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	
	#supplier Declared Wt Records(Suplier Memo)
	$declaredWtRecords  = $dailycatchsummaryObj->getSupplierDeclaredWtRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);

#For declared wt sheet
	# For Listing
	$supplierWiseDeclaredRecords = $dailycatchsummaryObj->getSupplierWiseDeclaredRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	
	$processCountWiseDeclaredRecords = $dailycatchsummaryObj->getProcessCountWiseDeclaredRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);

	if ($RMMatrix) {
	#RM Summary Matrix
		$RMChallanWiseRecords 	= $dailycatchsummaryObj->groupWtChallanRecords($fromDate, $tillDate, $selectUnit, $landingCenterId, $selectSupplier, $fishId, $processId, $selectADate, $billingCompany);
	
		$rmSummaryMatrixRecords = $dailycatchsummaryObj->filterRMSummaryMatrixRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	}

	#For getting the local qty Report
	if ($localQtyReportChk) {
		$dailyCatchReportResultSetObj = $dailycatchsummaryObj->filterDailyCatchEntryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate,  $billingCompany);
		$dailyCatchReportRecords = $dailyCatchReportResultSetObj->getNumRows();
	}

	#For getting the Daily Summary View
	if ($dailySummary) {
		$dailySummaryResultSetObj = $dailycatchsummaryObj->filterDailySummaryRecords($fromDate, $tillDate, $selectADate, $billingCompany);
		$dailySummaryRecordSize = $dailySummaryResultSetObj->getNumRows();
	}

	if ($subSupplierChk) {
		$numOfSubSupplier = $dailycatchsummaryObj->getNumOfSubSupplier($fromDate, $tillDate, $selectSupplier, $selectADate, $dateSelectFrom);
	}

	if ($supSetlmentSummaryChk) {
		#supplier Declared Wt Settlement Summary
		$declaredWtSettlementSummaryRecords  = $dailycatchsummaryObj->getSupplierDeclWtSettlementSummary($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom);
		#Supplier Commission Summary
		$declWtSupplierCommissionSummary = $dailycatchsummaryObj->getSupplierDeclWtCommissionSummary($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom);
	}
	# RM Supplier Rate Summary Matrix
	if ($RMRateMatrix) {
		# Getting RM Supplied Suppliers
		$rmSupplierRecords = $dailycatchsummaryObj->fetchRMSupplierRecords($fromDate, $tillDate, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $selectSupplier, $fishId, $processId, $billingCompany);
		# Getting Process Code, Grade Count summary
		$rmSummaryMatrixRecords = $dailycatchsummaryObj->filterRMSummaryMatrixRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	}
	
	# Qty Wise Summary
	if ($qtySummaryChk) {
		$wtChallanQtySummaryRecs = $dailycatchsummaryObj->fetchWtChallanQtyRecs($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	}

	# Challan Wise Summary
	if ($challanSummaryChk) {
		$challanQtyWiseSummaryRecs = $dailycatchsummaryObj->fetchChallanWiseRecs($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	}

} // Search Condition ends here

//--------------------------------
if ($p["date"]!="")		$date 		= "Checked";
if ($p["wtChallanNo"]!="") 	$wtChallanNo 	= "Checked"; 
if ($p["hFish"]!="") 		$fish 		= "Checked";
if ($p["fishCode"]!="") 	$fishCode 	= "Checked";
if ($p["declCt"]!="") 		$declCt 	= "Checked";
if ($p["declWt"]!="") 		$declWt 	= "Checked";
if ($p["gc"]!="") 		$gc 		= "Checked";
if ($p["netWt"]!="") 		$netWt 		= "Checked";
if ($p["adjustWt"]!="") 	$adjustWt 	= "Checked";
if ($p["local"]!="") 		$local 		= "Checked";
if ($p["wstge"]!="") 		$wstge 		= "Checked";
if ($p["soft"]!="") 		$soft 		= "Checked";
if ($p["peeling"]!="") 		$peeling 	= "Checked";
if ($p["remarks"]!="") 		$remarks 	= "Checked";
if ($p["effectiveWt"]!="") 	$effectiveWt	= "Checked";
if ($p["reason"]!="") 		$reason 	= "Checked";

//-----------------

//session_unregister("selTableHeader");
//session_unregister("arraySize");
unset($_SESSION['selTableHeader']);
unset($_SESSION['arraySize']);
#Using in Advance Search
if ($p["cmdAdvSearch"]!="") {

	$selectedTableHeader	= array();
	
	$selectedTableHeader[0]		= 	$p["date"];
	$selectedTableHeader[1] 	= 	$p["wtChallanNo"];
	$selectedTableHeader[2]		=	$p["hFish"];
	$selectedTableHeader[3] 	= 	$p["fishCode"];
	$selectedTableHeader[4]		=	$p["declCt"];
	$selectedTableHeader[5]		=	$p["declWt"];
	if($p["gc"]!="") {
		$selectedTableHeader[6] = "Count";
		$selectedTableHeader[7] = "Grade";
	}
	$selectedTableHeader[8]		=	$p["netWt"];
	if($p["adjustWt"]!="") {
		$selectedTableHeader[9]		=	$p["adjustWt"];		
	}
	if($p["adjustWt"]!="" && $p["reason"]!="" && ($p["date"]!="" || $p["wtChallanNo"]!="")) {
		$selectedTableHeader[10] = "Adj Reason";
	}
	if($p["adjustWt"]!="") {
		$selectedTableHeader[11]	=	"Grade/ Count Adj";		
	}
	if($p["adjustWt"]!="" && $p["reason"]!="" && ($p["date"]!="" || $p["wtChallanNo"]!="")) {
		$selectedTableHeader[12]	=	"Grade/ Count Adj Reason";
	}

	$selectedTableHeader[13]	=	$p["local"];
	if($p["local"]!="" && $p["reason"]!="" && ($p["date"]!="" || $p["wtChallanNo"]!=""))
	{
		
		$selectedTableHeader[14] = "Local Reason";
	}
	$selectedTableHeader[15]	=	$p["wstge"];
	if($p["wstge"]!="" && $p["reason"]!="" && ($p["date"]!="" || $p["wtChallanNo"]!=""))
	{
		$selectedTableHeader[16] = "Wstge Reason";
	}
	$selectedTableHeader[17]	=	$p["soft"];
	if($p["soft"]!="" && $p["reason"]!="" && ($p["date"]!="" || $p["wtChallanNo"]!=""))
	{
		$selectedTableHeader[18] = "Soft Reason";
	}
	
	if($p["date"]!="" || $p["wtChallanNo"]!=""){
		$selectedTableHeader[19]	=	$p["peeling"];
		$selectedTableHeader[20]	=	$p["remarks"];
	}
	$selectedTableHeader[21]	=	$p["effectiveWt"];
	//$selectedTableHeader[22]	=	$p["reason"];
	if($p["date"]!="" || $p["wtChallanNo"]!=""){
		#Using in Advance and Quick Search
		$dailyCatchSummaryRecords	= $dailycatchsummaryObj->filterDailyCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	} 
	if($p["date"]=="" && $p["wtChallanNo"]=="")
	{
		$dailyCatchSummaryRecords = $dailycatchsummaryObj->getAdvanceSearchGroupRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	}
	
	$advSearch=true;
	$arraySize = 22;
	$sessObj->createSession("selTableHeader",$selectedTableHeader);
	$sessObj->createSession("arraySize",$arraySize);
	//$sessObj->getValue("selTableHeader")
}
	
# Display heading
	if ($editMode)	$heading	=	$label_editDailyCatchSummary;
	else 		$heading	=	$label_addDailyCatchSummary;	
$ON_LOAD_SAJAX = "Y"; # Loading Ajax
	$ON_LOAD_PRINT_JS	= "libjs/dailycatchsummary.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	
?>
<form name="frmDailyCatchSummary" action="DailyCatchSummary.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="30" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
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
												<td colspan="2" height="10" ></td>
											</tr>
											<tr> 
												<td colspan="4" align="center">
												<? if($print==true){?>				 
												<input type="button" name="Submit" value=" View / Print" class="button" onclick="if(checkNumSubSupplier(document.frmDailyCatchSummary)) printWindow('PrintDailyCatchSummary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&selUnit=<?=$selectUnit?>&landingCenter=<?=$landingCenterId?>&supplier=<?=$selectSupplier?>&details=<?=$details?>&proCount=<?=$proCount?>&proSummary=<?=$proSummary?>&wChallan=<?=$wChallan?>&fishCatchSummary=<?=$fishCatchSummary?>&fish=<?=$fishId?>&processCode=<?=$processId?>&wtChallanSummary=<?=$wtChallanSummary?>&supplierMemo=<?=$supplierMemo?>&declWtSummary=<?=$declWtSummary?>&rateNAmount=<?=$rateNAmount?>&RMMatrix=<?=$RMMatrix?>&selDate=<?=$selDate?>&dateSelectFrom=<?=$dateSelectFrom?>&localQtyReportChk=<?=$localQtyReportChk?>&subSupplierChk=<?=$subSupplierChk?>&advSearch=<?=$advSearch?>&RMRateMatrix=<?=$RMRateMatrix?>&qtySummary=<?=$qtySummary?>&billingCompany=<?=$billingCompany?>&challanSummary=<?=$challanSummary?>',700,600);" <? if(sizeof($dailyCatchSummaryRecords)==0 && sizeof($processSummaryRecords)==0 && sizeof($fishWiseCatchSummaryRecords)==0 || $localQtyChk || $dailySummary) echo $disabled="disabled";?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												</td>
												<? } ?>
											</tr>
											<input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
											<tr> 
												<td colspan="3" nowrap>&nbsp;</td>
											</tr>
											<tr> 
												<td colspan="3" nowrap>
													<table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
														<tr> 
															<td valign="top">
																<table>
																	<tr>
																		<td colspan="2">
																			<fieldset>
																			<legend class="listing-item">Date Selection</legend>
																				<table width="200" border="0">
																					<tr>
																						<td>
																							<table width="60" border="0">
																								<tr>
																									<td><input name="dateSelection" type="radio" value="SD" onclick="this.form.submit();" <?=$singleDay?> class="chkBox"></td>
																									<td nowrap class="listing-item"> A Date</td>
																								</tr>
																							</table>
																						</td>
																						<td>
																							<table width="100" border="0">
																								<tr>
																									<td><input name="dateSelection" type="radio" value="DR" onClick="this.form.submit();" <?=$dateRange?> class="chkBox"></td>
																									<td nowrap class="listing-item">Date Range</td>
																								</tr>
																							</table>
																						</td>
																					</tr>
																				</table>
																			</fieldset>
																		</td>
																	</tr>
																	<? if($singleDay!=""){?>
																	<tr> 
																		<td class="fieldName">Date </td>
																		<td><input name="selDate" type="text" id="selDate" size="8" value="<?=$selDate?>" onchange="this.form.submit();">
																		</td>
																	</tr>
																	<? }?>
																	<? if($dateRange!="") {?>
																	<tr> 
																		<td class="fieldName"> From:</td>
																		<td> 
																		  <? $dateFrom = $p["supplyFrom"];?>
																		  <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmDailyCatchSummary);">
																		</td>
																	</tr>
																	<tr> 
																		<td class="fieldName"> Till:</td>
																		<td> 
																			<? $dateTill = $p["supplyTill"];?>
																			<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onchange="submitForm('supplyFrom','supplyTill',document.frmDailyCatchSummary);"/>
																		</td>
																	</tr>
																	<? }?>
																	<tr>
																		<TD colspan="2">
																			<table>
																				<TR>
																					<TD>
																						<fieldset>
																						<legend class="listing-item">Date Select From </legend>
																							<table width="200" border="0">
																								<tr>
																									<td>
																										<table width="60" border="0">
																											<tr>
																												<td><input name="dateSelectFrom" type="radio" value="SCD" onclick="this.form.submit();" <?=$supplierChallanDate?> class="chkBox"></td>
																												<td nowrap class="listing-item">
																														Supplier Date
																												</td>
																											</tr>
																										</table>
																									</td>
																									<td>
																										<table width="100" border="0">
																											<tr>
																												<td><input name="dateSelectFrom" type="radio" value="WCD" onClick="this.form.submit();" <?=$wtChallanDate?> class="chkBox"></td>
																												<td nowrap class="listing-item">Wt Challan Date</td>
																											</tr>
																										</table>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</TD>
																				</TR>
																			</table>
																		</TD>
																	</tr>
																</table>
															</td>
															<td valign="top">
																<table width="200" cellpadding="0" cellspacing="0">
																	<tr> 
																		<td class="fieldName" align="left">Unit/Plant:</td>
																		<td> 
																			<? $selectUnit			=	$p["selUnit"]; ?>
																			<!-- <select name="selUnit" onchange="this.form.submit();">-->
																			<select name="selUnit" onchange="getselUnit(this)">
																				<option value="0">- Select All -</option>
																				<?php
																				foreach ($plantRecords as $pr) {
																					$i++;
																					$plantId		=	$pr[0];
																					$plantName		=	stripSlash($pr[2]);
																					$selected="";
																					if ($plantId	== $selectUnit ) {
																						$selected	=	"selected";
																					}
																				?>
																				<option value="<?=$plantId?>" <?=$selected?>> 
																				<?=$plantName?>
																				</option>
																				<? }?>
																			</select> 
																		</td>
																	</tr>
																	<tr> 
																		<td class="fieldName" nowrap>Landing Center:</td>
																		<td> 
																			<? $landingCenterId	=	$p["landingCenter"];?>
																			<!--<select name="landingCenter" id="landingCenter" onchange="this.form.submit();">-->
																			<select name="landingCenter" id="landingCenter" onchange="functionLoad(this);">
																				<option value="0">-- Select All --</option>
																				<?
																				foreach ($landingCenterRecords as $fr) {
																				$centerId	=	$fr[0];
																				$centerName	=	stripSlash($fr[2]);
																				$selected="";
																				if ($centerId==$landingCenterId) {
																					$selected	=	"selected";
																				}
																				?>
																				<option value="<?=$centerId?>" <?=$selected?>> 
																				<?=$centerName?>
																				</option>
																				<? } ?>
																			</select>
																		</td>
																	</tr>
																	<TR>
																		<TD class="fieldName">
																			Filter
																		</TD>
																		<td nowrap="true" style="padding-left:2px; padding-right:2px;">
																		<!--<select name="filterType" id="filterType" style="width:70px;" onchange="this.form.submit();">-->
																			<select name="filterType" id="filterType" style="width:70px;" onchange="functionLoad(this);">
																				<option value="BW" <? if ($filterType=='BW') echo "selected";?>>Billing Company Wise</option>
																				<option value="SW" <? if ($filterType=='SW') echo "selected";?>>Supplier Wise</option>
																			</select>
																		</td>
																	</TR>	
																	<?php
																		if ($filterType=='SW') {
																	?>
	 																<tr> 
																		<td class="fieldName">Supplier:</td>
																		<td> 
																			<? $selectSupplier = $p["supplier"];?>
																			<!--<select name="supplier" onchange="this.form.submit();">-->
																			<select name="supplier" onchange="functionLoad(this);">
																				<option value="0">-- Select All --</option>
																				<?
																				foreach($supplierRecords as $fr) {
																					$supplierId	=	$fr[0];
																					$supplierName	=	stripSlash($fr[2]);
																					$selected	=	"";
																					if ($supplierId == $selectSupplier) {
																						$selected	=	"selected";
																				}
																				?>
																				<option value="<?=$supplierId?>" <?=$selected?>> 
																				<?=$supplierName?>
																				</option>
																				<? } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<TD class="fieldName" nowrap="true">Billing Company:</TD>
																		<td>
																			<!--<select name="billingCompany" id="billingCompany" onchange="this.form.submit();">-->
																			<select name="billingCompany" id="billingCompany" onchange="functionLoad(this);">
																				<option value="">--Select All--</option>
																				<?
																				foreach ($billingCompanyRecords as $bcr) {
																					$billingCompanyId	= $bcr[0];
																					$displayCName		= $bcr[1];
																					$selected = "";
																					if ($billingCompanyId==$billingCompany) $selected = "selected";
																				?>
																				<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
																				<?	
																				}	
																				?>
																			</select>
																		</td>
																	</tr>
																	<?php
																		} else {
																	?>
																	<tr>
																		<TD class="fieldName" nowrap="true">Billing Company:</TD>
																		<td>
																			<!--<select name="billingCompany" id="billingCompany" onchange="this.form.submit();">-->
																			<select name="billingCompany" id="billingCompany" onchange="functionLoad(this);">
																				<option value="">--Select All--</option>
																				<?
																				foreach ($billingCompanyRecords as $bcr) {
																					$billingCompanyId	= $bcr[0];
																					$displayCName		= $bcr[1];
																					$selected = "";
																					if ($billingCompanyId==$billingCompany) $selected = "selected";
																				?>
																				<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
																				<?	
																				}	
																				?>
																			</select>
																		</td>
																	</tr>
																	<tr> 
																		<td class="fieldName">Supplier:</td>
																		<td> 
																		  <? $selectSupplier = $p["supplier"];?>
																		  <!--<select name="supplier" onchange="this.form.submit();">-->
																		  <select name="supplier" onchange="functionLoad(this);">

																			<option value="0">-- Select All --</option>
																			<?
																			foreach($supplierRecords as $fr) {
																				$supplierId	=	$fr[0];
																				$supplierName	=	stripSlash($fr[2]);
																				$selected	=	"";
																				if ($supplierId == $selectSupplier) {
																				$selected	=	"selected";
																				}
																			?>
																			<option value="<?=$supplierId?>" <?=$selected?>> 
																			<?=$supplierName?>
																			</option>
																			<? } ?>
																		  </select>
																		</td>
																	</tr>
																	<?php
																		}
																	?>
																	<tr> 
																		<td class="fieldName">Fish:</td>
																		<td>
																		<?
																			$fishId	= $p["fish"];			
																		?>
																			<!--<select name="fish" onchange="this.form.submit();">-->
																			<select name="fish" onchange="functionLoad(this);">
																				<option value="">--Select--</option>
																				<?
																				foreach ($fishMasterRecords as $fr) {
																					$Id		=	$fr[0];
																					$fishName	=	stripSlash($fr[1]);
																					$selected	=	"";
																					if ($fishId==$Id) $selected	="selected";
																				?>
																				<option value="<?=$Id?>" <?=$selected?>><?=$fishName?></option>
																				<? }?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="fieldName">Process Code: </td>
																		<td>
																		<? $processId	=	$p["processCode"];?>
																			<select name="processCode" id="processCode">
																				<option value="">-- Select --</option>
																								 <?
																				foreach ($processCodeRecords as $fl) {
																				$processCodeId		=	$fl[0];
																				$processCode		=	$fl[1];
																				$selected	=	"";
																				if ($processId==$processCodeId) {
																					$selected	=	"selected";
																				}
																			?>
																				<option value="<?=$processCodeId;?>" <?=$selected;?>><?=$processCode;?></option>
																			<?
																			}
																			?>
																		</select>
																	</td>
																</tr>
																<tr>
																	<td colspan="2">
																		<table width="200" border="0">
																			<tr>
																				<td>
																					<table width="100" border="0">
																						<tr>
																							<td><input name="searchMode" type="radio" value="QS" onclick="this.form.submit();" <?=$quickSearch?> class="chkBox"></td>
																							<td nowrap class="listing-item">Quick Search </td>
																						</tr>
																					</table>
																				</td>
																				<td>
																					<table width="100" border="0">
																						<tr>
																							<td><input name="searchMode" type="radio" value="AS" onClick="this.form.submit();" <?=$advancedSearch?> class="chkBox"></td>
																							<td nowrap class="listing-item">Advanced Search </td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
														<td valign="top">
															<? if($searchMode=='QS'){?>
															<table>
																<tr>
																	<td>
																		<fieldset>
																		<legend class="fieldName">Search Options</legend>
																			<table width="250" border="0" align="center" cellpadding="0" cellspacing="0">
																			<? if ($dateSelectFrom=='WCD') {?>
																				<tr> 
																					<td><input type="checkbox" name="details" id="details" value="Y" <?=$checked1?> class="chkBox" onclick="removeAllChk(this.form,'details');enabrtAmount();"></td>
																					<td class="listing-item" align="left">Detailed</td>
																				</tr>
																				<tr> 
																					<td><input type="checkbox" name="proCount" id="proCount" value="Y" <?=$checked3?> class="chkBox" onclick="removeAllChk(this.form,'proCount');enabrtAmount();"></td>
																					<td colspan="3" class="listing-item" align="left">Process-Count 
																					 Summary
																					</td>
																				</tr>
																				<? }?>
																				<tr> 
																					<td><input type="checkbox" name="proSummary" id="proSummary" value="Y" <?=$checked4?> class="chkBox" onclick="removeAllChk(this.form,'proSummary');enabrtAmount();"></td>
																					<td colspan="3" class="listing-item" align="left">Process 
																						Summary 
																					</td>
																				</tr>
																				<? if ($dateSelectFrom=='WCD') {?>
																				<tr>
																					<td><input type="checkbox" name="fishCatchSummary" id="fishCatchSummary" value="Y" <?=$checked5?> class="chkBox" onclick="removeAllChk(this.form,'fishCatchSummary');enabrtAmount();"></td>
																					<td colspan="3" class="listing-item" align="left">Fish-Catch Summary</td>
																				</tr>
																				<tr>
																					<td><input type="checkbox" name="wtChallanSummary" id="wtChallanSummary" value="Y" <?=$checked6?> class="chkBox" onclick="removeAllChk(this.form,'wtChallanSummary');enabrtAmount();"></td>
																					<td class="listing-item" nowrap> Wt Challan Wise Summary </td>
																					<td>
																						<table>
																							<TR>
																								<td class="listing-item" nowrap><input name="localQtyChk" type="checkbox" id="localQtyChk" value="LQC" <?=$localQtyChk?> class="chkBox" onclick="removeChkRate(document.frmDailyCatchSummary);enabrtAmount();"></td>
																								<td class="listing-item" nowrap>Local Quantity </td>
																							</TR>
																						</table>
																					</td>
																				</tr>
																				<?  }
																				if ($dateSelectFrom=='SCD') {?>
																				<tr>
																					<td><input name="supplierMemo" type="checkbox" id="supplierMemo" value="Y" class="chkBox" <?=$checked7?> onclick="removeAllChk(this.form,'supplierMemo');"></td>
																					<td class="listing-item" nowrap>Supplier Memo (Decl.Wt)</td>
																					<td>
																						<table>
																							<TR>
																								<td class="listing-item" nowrap><input name="subSupplierChk" type="checkbox" id="subSupplierChk" value="SSC" <?=$subSupplierChk?> class="chkBox" onclick=""></td>
																								<td class="listing-item" nowrap>Sub-Supplier </td>
																							</TR>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td><input name="declWtSummary" type="checkbox" id="declWtSummary" value="Y" <?=$checked8?> onclick="removeAllChk(this.form,'declWtSummary');" class="chkBox"></td>
																					<td colspan="3" class="listing-item" align="left">Declared Wt Settlement Summary </td>
																				</tr>
																				<? }?>
																				<? if ($dateSelectFrom=='WCD') {?>
																				<tr>
																					<td><input name="RMMatrix" type="checkbox" id="RMMatrix" value="RMM" <?=$RMMatrixCheck?> class="chkBox" onclick="removeAllChk(this.form,'RMMatrix');disabrtAmount();" ></td>
																					<td colspan="3" class="listing-item" align="left">RM Summary Matrix </td>
																				</tr>
																				<tr>
																					<td><input name="localQtyReportChk" type="checkbox" id="localQtyReportChk" value="LQRC" <?=$localQtyReportChk?> class="chkBox" onclick="removeAllChk(this.form,'localQtyReportChk');enabrtAmount();"></td>
																					<td colspan="3" class="listing-item" align="left">Local Quantity Report</td>
																				</tr>
																				<tr>
																					<td><input name="dailySummary" type="checkbox" id="dailySummary" value="DSV" <?=$dailySummary?> class="chkBox" onclick="removeAllChk(this.form,'dailySummary');enabrtAmount();"></td>
																					<td colspan="3" class="listing-item" align="left">Daily Summary View</td>
																				</tr>
																				<tr>
																					<td><input name="RMRateMatrix" type="checkbox" id="RMRateMatrix" value="RMRM" <?=$RMRateMatrixChk?> class="chkBox" onclick="removeAllChk(this.form,'RMRateMatrix');disabrtAmount();"></td>
																					<td colspan="3" class="listing-item" align="left">RM Supplier Rate Summary Matrix </td>
																				</tr>
																				<tr>
																					<td>
																						<input type="checkbox" name="qtySummary" id="qtySummary" value="QSU" <?=$qtySummaryChk?> class="chkBox" onclick="removeAllChk(this.form,'qtySummary');disabrtAmount();">
																					</td>
																					<td colspan="3" class="listing-item" align="left">Quantity Summary View</td>
																				</tr>
																				<tr>
																					<td>
																						<input type="checkbox" name="challanSummary" id="challanSummary" value="CSU" <?=$challanSummaryChk?> class="chkBox" onclick="removeAllChk(this.form,'challanSummary');enabrtAmount();">
																					</td>
																					<td colspan="3" class="listing-item" align="left">Challan Summary View<p></td>
																				</tr>
																				<? }?>

																			<td>&nbsp;</td>
																			<td colspan="3" class="listing-item" style="padding-top:5px; padding-bottom:5px;">
																				<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateSummarySearch(document.frmDailyCatchSummary);"/>
																			</td>
																		</tr>
																	</table>
																</fieldset>
															</td>
														</tr>
														<tr>
															<td>
																<fieldset align="left">
																<legend class="fieldName">Rate &amp; Amount</legend>
																	<table>
																		<tr height="30px">
																			<td ><input name="rateNAmount" type="checkbox" id="rateNAmount" value="RA" <?=$rateNAmountCheck?> class="chkBox" onclick="removeChkLocal(document.frmDailyCatchSummary);enabrtAmount();"  <?php if (($p["RMMatrix"]=="RMM") || ( $p["RMRateMatrix"]=="RMRM") || ($p["qtySummary"]=="QSU")){?> disabled="true" <?php }?>>
																			</td>
																			<td colspan="3" class="listing-item" align="left" >Rate &amp; Amount</td></tr>
																		<tr>
																	</table>
																</fieldset>
															</td>
														</tr>			  
													</table>
												<? } else {?>&nbsp;<? }?>						  
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr> 
									<td colspan="3" nowrap class="fieldName" > </td>
								</tr>
								<tr> 
									<td class="fieldName" nowrap > </td>
									<td></td>
									<td class="fieldName"></td>
								</tr>
								<tr> 
									<td class="fieldName" nowrap ></td>
									<td></td>
									<td class="fieldName"></td>
								</tr>
								<? if($searchMode=='AS'){?>
								<tr>
									<td colspan="3" style="padding-left:10PX;">
										<table width="200" border="0">
											<tr>
												<td>
													<fieldset>
													<legend class="fieldName">Advanced Search</legend>
														<table width="200" border="0">
															<tr>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="date" type="checkbox" id="date" value="Date" <?=$date?> class="chkBox"></td>
																			<td class="listing-item">Date</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="wtChallanNo" type="checkbox" id="wtChallanNo" value="Wt Challan No" <?=$wtChallanNo?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Wt Challan No</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="hFish" type="checkbox" id="hFish" value="Fish" <?=$fish?> class="chkBox"></td>
																			<td class="listing-item">Fish</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center">					
																				<input name="fishCode" type="checkbox" id="fishCode" value="Process Code" <?=$fishCode?> class="chkBox">
																			</td>
																			<td class="listing-item" nowrap>Process Code </td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="declCt" type="checkbox" id="declCt" value="Decl. Ct" <?=$declCt?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Decl. Ct </td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="declWt" type="checkbox" id="declWt" value="Decl. Wt" <?=$declWt?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Decl. Wt </td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="gc" type="checkbox" id="gc" value="GC" <?=$gc?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Grade&amp;Count</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="netWt" type="checkbox" id="netWt" value="Net Wt" <?=$netWt?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Net Wt</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="adjustWt" type="checkbox" id="adjustWt" value="Adjust Wt" <?=$adjustWt?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Adjust Wt </td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="local" type="checkbox" id="local" value="Local" <?=$local?> class="chkBox"></td>
																			<td class="listing-item">Local</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="wstge" type="checkbox" id="wstge" value="Wstge" <?=$wstge?> class="chkBox"></td>
																			<td class="listing-item">Wstge</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="soft" type="checkbox" id="soft" value="Soft" <?=$soft?> class="chkBox"></td>
																			<td class="listing-item">Soft</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="peeling" type="checkbox" id="peeling" value="Peeling (%)" <?=$peeling?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Peeling (%)</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="remarks" type="checkbox" id="remarks" value="Remarks" <?=$remarks?> class="chkBox"></td>
																			<td class="listing-item">Remarks</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="effectiveWt" type="checkbox" id="effectiveWt" value="Effective Wt" <?=$effectiveWt?> class="chkBox"></td>
																			<td class="listing-item" nowrap>Effective Wt </td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="reason" type="checkbox" id="reason" value="Reason" <?=$reason?> class="chkBox"></td>
																			<td class="listing-item">Reason</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="reason" type="checkbox" id="reason" value="Reason" <?=$reason?> class="chkBox"></td>
																			<td class="listing-item">Rate</td>
																		</tr>
																	</table>
																</td>
																<td>
																	<table width="50" border="0">
																		<tr>
																			<td align="center"><input name="reason" type="checkbox" id="reason" value="Reason" <?=$reason?> class="chkBox"></td>
																			<td class="listing-item">Amount</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr>
																<td colspan="8" align="center" height="5"></td>
															</tr>
															<tr>
																<td colspan="8" align="center"><input name="cmdAdvSearch" type="submit" class="button" id="cmdAdvSearch" value="Search" onClick="return validateAdvanceSearch(document.frmDailyCatchSummary);"></td>
															</tr>
														</table>
													</fieldset>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<? }?>
								<? if($advSearch==true && sizeof($dailyCatchSummaryRecords)>0){?>
								<tr>
									<td colspan="3"  height="10" class="listing-item" style="padding-left:10PX;">
										<table width="100%" border="0">
											<tr>
												<td>
													<table width="95%" cellpadding="0" cellspacing="1" bgcolor="#999999">
														<tr bgcolor="#F2f2f2" align="center">
														<? //echo sizeof($selectedTableHeader);
														if (sizeof($selectedTableHeader)>0)
															{
																for($k=0;$k<$arraySize;$k++){
																	 $tHeader	=	$selectedTableHeader[$k];
																	if($tHeader!=""){
																	
															?>
															<td class="listing-head" style="padding-left:3px; padding-right:3px;"><?=$tHeader?></td>
														<? }}?>
													</tr>
													<?
														$totalWt	=	"";
														$grandTotalNetWt	=	"";
														$grandtotalAdjustWt	=	"";
														$totalLocalQty	=	"";
														$totalWastageQty	=	"";
														$totalSoftQty		=	"";
														$totalAdjustWt	=	"";
																					  
														foreach($dailyCatchSummaryRecords as $cer){
														$i++;
														
														$catchEntryId	=	$cer[0];
														$array			=	explode("-",$cer[3]);
														$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
														
														//$weigmentChellanNo	=	$cer[6];
														$weigmentChellanNo	=	$cer[47];	
														$supplier		=	$cer[8];
														$supplierRec		=	$supplierMasterObj->find($supplier);
														$supplierName	=	$supplierRec[3];
														//echo "hii";
														$fishRec		=	$fishmasterObj->find($cer[11]);
														$fishName		=	$fishRec[1];
														

														$local			=	$cer[16];
														$totalLocalQty	+=	$local;
														
														$wastage		=	$cer[17];
														$totalWastageQty += 	$wastage;
															
														$soft		=	$cer[18];
														$totalSoftQty	+=	$soft;
														
														$processCodeId	=	$cer[12];
														$processCodeRec		=	$processcodeObj->find($processCodeId);
														$processCode	=	$processCodeRec[2];
														
														$adjustWt		=	$cer[20];
														$totalAdjustWt		+=	$adjustWt;

														$gradeCountAdj		=	$cer[44];
														$totalGradeCountAdj	+= 	$gradeCountAdj;

														//$totalAdjustment	=	$adjustWt + $gradeCountAdj;
														//$grandtotalAdjustWt 	+= 	$totalAdjustment;
														
														$reason			=	$cer[19];
														$peeling		=	$cer[22];
														$remarks		=	$cer[23];
														$netWt			=	$cer[26];
														$grandTotalNetWt	+=	$netWt;
														
														$declWt			=	$cer[29];
														$declCount		=	$cer[30];
														
														$dailyRateRec	=	$supplieraccountObj->findDailyRate($cer[11]);
														$declRate		=	$dailyRateRec[7];
														
														$paidStatus		=	$cer[35];
														
														
														
														$selectWeight		=	$cer[32];
														$selectRate			=	$cer[33];
														$actualRate			=	$cer[34];
														
														$payableWt			=	$cer[28];
														$totalWt	+=	$payableWt;	
														
														$payableRate	=	$dailyRateRec[6];
														$receivedBy		=	$cer[46];
														
														$count			=	$cer[13];
														$gradeCode		=	"";
														if($count=="" || $receivedBy=='B' || $receivedBy =='G'){
															$gradeRec			=	$grademasterObj->find($cer[37]);
															$gradeCode			=	stripSlash($gradeRec[1]);
														}
														$centerRec			=	$landingcenterObj->find($cer[7]);
														$landingCenterName	=	stripSlash($centerRec[1]);
														$localReason    = 	$cer[38];
														$wstgeReason 	=	$cer[39];
														$softReason	=	$cer[40];
														$gradeCountAdjReason = 	$cer[45];
														?>
														<tr bgcolor="#FFFFFF">
														<?	
														$tHeader = "";		
														for($k=0;$k<$arraySize;$k++){
															$tHeader	=	$selectedTableHeader[$k];
															if($tHeader!=""){
																$alignStyle = "";
																if ($tHeader=="Effective Wt" || $tHeader=="Decl. Wt" || $tHeader=="Net Wt" || $tHeader=="Adjust Wt" || $tHeader=="Grade/ Count Adj" || $tHeader=="Local" || $tHeader=="Wstge" || $tHeader=="Soft" || $tHeader=="Peeling (%)")  {
																	$alignStyle = "align='right'";
																}
															
															?>
															<td class="listing-item" style="padding-left:3px; padding-right:3px;" <?=$alignStyle?>>
																<? 
																if($tHeader=="Date") echo $enteredDate;
																if($tHeader=="Wt Challan No") echo $weigmentChellanNo;
																if($tHeader=="Fish") echo $fishName;
																if($tHeader=="Process Code") echo $processCode;
																if($tHeader=="Decl. Ct") echo $declCount;
																if($tHeader=="Decl. Wt") echo $declWt;
																if($tHeader=="Grade") echo $gradeCode;
																if($tHeader=="Count") echo $count;
																if($tHeader=="Net Wt") echo $netWt;
																if($tHeader=="Adjust Wt") echo $adjustWt;
																if($tHeader=="Grade/ Count Adj") echo $gradeCountAdj;
																if($tHeader=="Grade/ Count Adj Reason") echo $gradeCountAdjReason;
																if($tHeader=="Adj Reason") echo $reason;
																if($tHeader=="Local") echo $local;
																if($tHeader=="Local Reason") echo $localReason;
																if($tHeader=="Wstge") echo $wastage;
																if($tHeader=="Wstge Reason") echo $wstgeReason;
																if($tHeader=="Soft") echo $soft;
																if($tHeader=="Soft Reason") echo $softReason;
																if($tHeader=="Peeling (%)") echo $peeling;
																if($tHeader=="Remarks") echo $remarks;
																if($tHeader=="Effective Wt") echo $payableWt;
																
																?>							
															</td>
															<? 
															}
															}
															?>
														</tr>
														<? 						
															}
														}
														?>
														<tr>
														<?	
														$tHeader = "";						
														for($k=0;$k<$arraySize;$k++)
														{
															$tHeader	=	$selectedTableHeader[$k];
															if($tHeader!=""){
																$alignStyle = "";
																if ($tHeader=="Effective Wt" || $tHeader=="Decl. Wt" || $tHeader=="Net Wt" || $tHeader=="Adjust Wt" || $tHeader=="Grade/ Count Adj" || $tHeader=="Local" || $tHeader=="Wstge" || $tHeader=="Soft" || $tHeader=="Peeling (%)")  {
																	$alignStyle = "align='right'";
																}
														?>
															<td class="listing-item" bgcolor="#FFFFFF" <?=$alignStyle?>>
															<?
															if($tHeader=="Net Wt") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($grandTotalNetWt,2)."</strong></span>";
															if($tHeader=="Adjust Wt") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalAdjustWt,2)."</strong></span>";
															if($tHeader=="Grade/ Count Adj") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalGradeCountAdj,2)."</strong></span>";		
															if($tHeader=="Local") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalLocalQty,2)."</strong></span>";
															if($tHeader=="Wstge") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalWastageQty,2)."</strong></span>";
															if($tHeader=="Soft") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalSoftQty,2)."</strong></span>";
																				
															if($tHeader=="Effective Wt") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalWt,2)."</strong></span>";
															
															
															?></td>
															<? }}?>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<? }?>
								  <?
								  if( sizeof($dailyCatchSummaryRecords) || sizeof($processSummaryRecords)){
								  ?>
								<tr> 
									<td colspan="3"  height="10" class="listing-item" style="padding-left:10PX;">
									<table width="100%" border="0" cellpadding="2" cellspacing="0">
									<?
									if( sizeof($dailyCatchSummaryRecords) && $details!=""){
									$i	=	0;
									?>
										<tr bgcolor="#FFFFFF">
											<td colspan="17" height="10"></td>
										</tr>
										<tr bgcolor="#FFFFFF"> 
											<td colspan="17" align="center" class="listing-head">
												<table width="200" align="left">
													<tr>
														<td class="fieldName" nowrap="nowrap">Landing Center: </td>
														<td class="listing-item">
														<? 
															if($landingCenterId!=0)
															{ 
																$ctreRec = $landingcenterObj->find($landingCenterId);
																echo $landingCenterName = stripSlash($ctreRec[1]);		
															 } else {?> All <? }?>
														</td>
														<td>&nbsp;</td>
														<td class="fieldName">Supplier:</td>
														<td class="listing-item" nowrap="nowrap">					
														  <? if($selectSupplier!=0){
															$sRec	=	$supplierMasterObj->find($selectSupplier);
															echo $supplierName	=	$sRec[2];	
														} else {?>
														All	
														<? }?>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr bgcolor="#FFFFFF"> 
											<td colspan="17" align="left" class="listing-head"> 
												<table width="95%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
													<tr bgcolor="#f2f2f2" align="center">
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Date</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Wt Challan No</td> 
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Fish</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Process Code </td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Decl. Ct</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Decl. Wt</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">&nbsp;&nbsp;Grade</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Count</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">NetWt</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Adjust Wt</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">
														  Grade/ Count Adj
														</td>		
														<td width="15%" class="listing-head" align="center">Local</td>
														<td width="15%" class="listing-head" align="center">Wstge</td>
														<td width="15%" class="listing-head" align="center">Soft</td>
														<td width="15%" class="listing-head" align="center">Peeling (%)</td>
														<td width="15%" class="listing-head" align="center">Remarks</td>
														<td width="15%" class="listing-head" align="center">Effective Wt </td>
															<? if($rateNAmount) {?>
														<td width="15%" class="listing-head" align="center">Rate</td>
														<td width="15%" class="listing-head" align="center">Amount </td>
														<?php }?>
													</tr>
													<?
													$totalWt	=	"";
													$grandTotalNetWt	=	"";
													$grandtotalAdjustWt	=	"";
													$totalLocalQty	=	"";
													$totalWastageQty	=	"";
													$totalSoftQty		=	"";
													$totalAdjustWt		=	"";
													$totalGradeCountAdj	=	"";
													$netselrate="";
													$netselamt="";
								  
													foreach($dailyCatchSummaryRecords as $cer){
													$i++;
													
													$catchEntryId	=	$cer[0];
													$challanDate = $cer[3];
													$enteredDate = "";
													if($prevRecDate!=$challanDate){
														$array			=	explode("-",$challanDate);
														$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
													}
													$weigmentChellanNo	=	$cer[6];
													$wChellanNo	=	$cer[47];
													$weigmentChellanNo = "";
													if($prevRecWChallanNo!=$wChellanNo){
														$weigmentChellanNo	=	$cer[47];
													}
													
													$supplier		=	$cer[8];
													$supplierRec		=	$supplierMasterObj->find($supplier);
													$supplierName	=	$supplierRec[3];
													
													$fishRec		=	$fishmasterObj->find($cer[11]);
													$fishName		=	$fishRec[1];
													

													$local			=	$cer[16];
													$totalLocalQty	+=	$local;
													
													$wastage		=	$cer[17];
													$totalWastageQty += 	$wastage;
														
													$soft			=	$cer[18];
													$totalSoftQty	+=	$soft;
													
													$processCodeId	=	$cer[12];
													$processCodeRec		=	$processcodeObj->find($processCodeId);
													$processCode	=	$processCodeRec[2];
													
													$adjustWt		=	$cer[20];
													$totalAdjustWt		+= 	$adjustWt;

													$gradeCountAdj		=	$cer[44];
													$totalGradeCountAdj	+=	$gradeCountAdj;

													//$totalAdjustment	=	$adjustWt + $gradeCountAdj;
													//$grandtotalAdjustWt 	+= 	$totalAdjustment;
	
													$reason			=	$cer[19];
													$peeling		=	$cer[22];
													$remarks		=	$cer[23];
													$netWt			=	$cer[26];
													$grandTotalNetWt	+=	$netWt;
													
													$declWt			=	$cer[29];
													$declCount		=	$cer[30];
													
													$dailyRateRec		=	$supplieraccountObj->findDailyRate($cer[11]);
													$declRate		=	$dailyRateRec[7];
													
													$paidStatus		=	$cer[35];
													
													
													
													$selectWeight		=	$cer[32];
													$selectRate			=	$cer[33];
													$actualRate			=	$cer[34];
													
													$payableWt			=	$cer[28];
													
													//DeclWt>0 and not equal to effective wt
													$displayEffectiveWt = "";
													if($declWt>0 && $declWt!=$payableWt){
														$displayEffectiveWt = "<span style=\"color:#FF0000\">".$payableWt."</span>";
													} else {
														$displayEffectiveWt = $payableWt;
													}

													$totalWt	+=	$payableWt;	
													
													$payableRate	=	$dailyRateRec[6];
													$receivedBy		=	$cer[46];
													$selrate=$cer[48];
													$selamt=$cer[49];
													$netselrate+=$selrate;
													$netselamt+=$selamt;
													
													$count			=	$cer[13];
													$gradeCode		=	"";
													if($count=="" || $receivedBy=='B' || $receivedBy =='G'){
														$gradeRec			=	$grademasterObj->find($cer[37]);
														$gradeCode			=	stripSlash($gradeRec[1]);
													}
													$centerRec		=	$landingcenterObj->find($cer[7]);
													$landingCenterName	=	stripSlash($centerRec[1]);
													
													?>
												<tr  <? if($rateNAmount) { if ($selamt>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>>
													<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$enteredDate?></td>
													<td class="listing-item" nowrap style="padding-left:3px; padding-right:3px;"><?=$weigmentChellanNo;?></td> 
													<td class="listing-item" width="40" style="padding-left:3px; padding-right:3px;"><?=$fishName?></td>
													<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$processCode?></td>
													<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$declCount?></td>
													<td class="listing-item" nowrap align="right" style="padding-left:3px; padding-right:3px;"><?=$declWt?></td>
													<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$gradeCode?></td>
													<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$count?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$netWt?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$adjustWt?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$gradeCountAdj?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$local?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$wastage?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$soft?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$peeling?></td>
													<td class="listing-item" align="left" style="padding-left:3px; padding-right:3px;"><?=$remarks?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$displayEffectiveWt?></td>
													<? if($rateNAmount) {?>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$selrate?></td>
													<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$selamt?></td>
													<?php }?>
												</tr>
												<? 
													$prevRecDate=$challanDate;
													$prevRecWChallanNo=$wChellanNo;
												}
												?>
												<tr bgcolor="#FFFFFF">
													<td colspan="8" align="right" nowrap class="listing-item"><span class="listing-head">TOTAL:</span>&nbsp;&nbsp;</td>
													<td nowrap class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><strong><? echo number_format($grandTotalNetWt,2);?></strong></td>
													<td nowrap class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><strong><? echo number_format($totalAdjustWt,2);?></strong></td>
													<td nowrap class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><strong><? echo number_format($totalGradeCountAdj,2);?></strong></td>
													<td nowrap class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><strong><? echo number_format($totalLocalQty,2);?></strong></td>
													<td nowrap class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><strong><? echo number_format($totalWastageQty,2);?></strong></td>
													<td nowrap class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><strong><? echo number_format($totalSoftQty,2);?></strong></td>
													<td nowrap class="listing-item" align="right">&nbsp;</td>
													<td nowrap class="listing-item" align="right">&nbsp;</td>
													<td class="listing-item" align="right" nowrap style="padding-left:3px; padding-right:3px;"><strong> 
														<? echo number_format($totalWt,2);?>
														</strong>
													</td>
													<? if($rateNAmount) {?>
													<td>&nbsp;<? //echo number_format($netselrate,2);?></td>
													<td>&nbsp;<? echo number_format($netselamt,2);?></td>
													<?php }?>
												</tr>
											</table>
											<? }?>
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td colspan="17" height="10"></td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td colspan="16" align="center" class="listing-head">
											<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
											<?
											if( sizeof($dailyCatchSummaryRecords) && $wChallan!=""){
											?>
												<tr bgcolor="#FFFFFF">
													<td colspan="10" class="listing-head" align="center">Wt-Challan Wise </td>
												</tr>
												<tr bgcolor="#f2f2f2">
													<td width="6%" class="listing-head" align="center">Wt Challan No </td> 
													<td width="6%" class="listing-head">&nbsp;&nbsp;Fish</td>
													<td width="9%" class="listing-head" align="center">Process Code</td>
													<td class="listing-head" align="center">Decl. Ct</td>
													<td class="listing-head" align="center">Decl. Wt</td>
													<td width="9%" class="listing-head">&nbsp;&nbsp;Grade</td>
													<td width="9%" class="listing-head">&nbsp;&nbsp;Count</td>
													<td width="15%" class="listing-head" align="center">Remarks</td>
													<td width="15%" class="listing-head" align="center">Effective Wt </td>
												</tr>
												<?
												$gradeCode ="";
												$totalWt	=	"";
												foreach($dailyCatchSummaryRecords as $cer){
												$i++;
													
												$catchEntryId	=	$cer[0];
												$array			=	explode("-",$cer[3]);
												$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
												
												$weigmentChellanNo	=	$cer[6];
												$displayChallanNum	= 	$cer[47];
												//echo $cer[8];
												($cer[8]!="")? $supplier=$cer[8]: $supplier=$cer[50];
												

												$supplierRec		=	$supplierMasterObj->find($supplier);
												$supplierName	=	$supplierRec[3];
												
												
												$fishRec		=	$fishmasterObj->find($cer[11]);
												$fishName		=	$fishRec[1];
												
												$local			=	$cer[16];
												$wastage		=	$cer[17];	
												$soft			=	$cer[18];
												
												$declCount		=	$cer[30];
												
												$processCodeId	=	$cer[12];
												$processCodeRec		=	$processcodeObj->find($processCodeId);
												$processCode	=	$processCodeRec[2];
												
												$adjustWt		=	$cer[20];
												$reason			=	$cer[19];
												$peeling		=	$cer[22];
												$remarks		=	$cer[23];
												$netWt			=	$cer[26];
												
												$declWt			=	$cer[29];
												
												$dailyRateRec	=	$supplieraccountObj->findDailyRate($cer[11]);
												$declRate		=	$dailyRateRec[7];
												
												$paidStatus			=	$cer[35];
												
												
												
												$selectWeight		=	$cer[32];
												$selectRate			=	$cer[33];
												$actualRate			=	$cer[34];
												
												$payableWt			=	$cer[28];
												$totalWt	+=	$payableWt;
												
												$payableRate	=	$dailyRateRec[6];
												
												$count		=	$cer[13];
												if($count==""){
													$gradeRec			=	$grademasterObj->find($cer[37]);
													$gradeCode			=	stripSlash($gradeRec[1]);
												}
												$centerRec			=	$landingcenterObj->find($cer[7]);
												$landingCenterName	=	stripSlash($centerRec[1]);
												
												?>
												<tr bgcolor="#FFFFFF">
													<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$displayChallanNum;?></td> 
													<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$fishName?>                                    </td>
													<td class="listing-item">&nbsp;&nbsp; 
													<?=$processCode?>                                    </td>
													<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$declCount?></td>
													<td class="listing-item" nowrap align="right"><?=$declWt?>&nbsp;</td>
													<td class="listing-item" nowrap>&nbsp;&nbsp; 
													<?=$gradeCode?>                                    </td>
													<td class="listing-item" nowrap>&nbsp;&nbsp; 
													<?=$count?>                                    </td>
													<td class="listing-item" align="left">&nbsp;&nbsp;<?=$remarks?></td>
													<td class="listing-item" align="right"> 
													<?=$payableWt?>
													  &nbsp;&nbsp; </td>
												</tr>
												<?  }?>
												<tr bgcolor="#FFFFFF">
													<td colspan="8" nowrap class="listing-item" align="right"><span class="listing-head">TOTAL</span>&nbsp;&nbsp;</td>
													<td class="listing-item" align="right" nowrap="nowrap"><strong> 
													  <? echo number_format($totalWt,2);?>
													  </strong>&nbsp;&nbsp;
													</td>
												</tr>
												<? }?>
											</table>
										</td>
										<td align="center" class="listing-head"></td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td colspan="17" height="10"></td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td colspan="17" align="left" class="listing-head">
											<table width="70%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
											<?
											if( sizeof($processCountSummaryRecords) && $proCount!=""){
											?>
												<tr bgcolor="white"> 
													<td colspan="11" align="center" class="listing-head">Fish-Process-Count-Summary</td>
												</tr>
												<tr bgcolor="#f2f2f2" align="center"> 
													<td class="listing-head" style="padding-left:10px;padding-right:10px;">Fish</td>
													<td class="listing-head" style="padding-left:10px;padding-right:10px;">Process Code</td>
													<td class="listing-head" style="padding-left:10px;padding-right:10px;">Grade</td>
													<td class="listing-head" style="padding-left:10px;padding-right:10px;">Count</td>
													<td class="listing-head" style="padding-left:10px;padding-right:10px;">Effective Wt </td>
													<? if($rateNAmount){?>
													<td class="listing-head" style="padding-left:10px;padding-right:10px;" width="180">Rate</td>
													<td class="listing-head" style="padding-left:10px;padding-right:10px;" width="200">Amount</td>
													<? }?>
													<? if ($localQtyChk) {?>
													<td>&nbsp;</td>
													<td class="listing-head" style="padding-left:10px; padding-right:10px;">Net Wt</td>
													<td class="listing-head" style="padding-left:10px; padding-right:10px;">Adjust Wt</td>
													<td class="listing-head" style="padding-left:10px; padding-right:10px;">Local Quantity<br>
														<span style="font-size:10px;line-height:normal;">(Local+<br>Wastage+<br>Soft)</span>
													</td>
													<? }?>	
												</tr>
												<?
												$j=0;
												$gradeCode	= "";
												$totalWt	= "";
												$totalLocalQuantity = 0;
												$grandTotalNetWt = 0;
												$grandTotalLocalQty = 0;
												$totalAdjustWt = 0;
												$netPamount=0;
												$prate=0;
												$pamount=0;
												$netPrate=0;
												foreach ($processCountSummaryRecords as $pcsr) {
												$j++;
												$catchEntryId	=	$pcsr[0];
												//$fishId	=	$pcsr[1];
												$fishRec	=	$fishmasterObj->find($pcsr[1]);
												$fishName	=	$fishRec[1];
												$processCodeId	= $pcsr[2];
												$processCodeRec	= $processcodeObj->find($processCodeId);
												$processCode	=	$processCodeRec[2];
												$count			=	$pcsr[3];
												$declCount		=	$pcsr[4];
												$declWt			=	$pcsr[5];
												$effectiveWt	=	$pcsr[9];
												$totalWt	+=	$effectiveWt;
												$receivedBy	=	$pcsr[8];
												$gradeCode	=	"";
												if($count=="" || $receivedBy=='B' || $receivedBy=='G'){
													$gradeRec  = $grademasterObj->find($pcsr[7]);
													$gradeCode = stripSlash($gradeRec[1]);
												}
												 
												$adjustWt	= $pcsr[11];
												$totalAdjustWt 	+= $adjustWt;
												$localQty	= $pcsr[12];
												$wastageQty	= $pcsr[13];
												$softQty	= $pcsr[14];
												$totalLocalQuantity =  $localQty + $wastageQty + $softQty;
												$grandTotalLocalQty += $totalLocalQuantity;	

												//$netWt	 = $pcsr[10];
												$netWt		 = $effectiveWt+$adjustWt+$totalLocalQuantity;
												$grandTotalNetWt += $netWt;
												$pamount=$pcsr[15];
												$prate=$pcsr[16];
												$netPrate+=$prate;
												$netPamount+=$pamount;
												?>
												<tr <? if($rateNAmount) { if ($pamount>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>  > 
													<td class="listing-item" nowrap style="padding-left:10px;padding-right:10px;">
													<?=$fishName?>
													</td>
													<td class="listing-item" nowrap style="padding-left:10px;padding-right:10px;">
													<?=$processCode?> </td>
													<td class="listing-item" nowrap style="padding-left:10px;padding-right:10px;"> 
													<?=$gradeCode?></td>
													<td class="listing-item" nowrap style="padding-left:10px;padding-right:10px;"> 
													<?=$count?>                                    </td>
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;"> 
													<?=$effectiveWt?></td>
													<? if($rateNAmount) {?>
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">&nbsp;<?=$prate;?></td>
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;"><?=$pamount;?>&nbsp;</td>
													<? }?>
													<? if ($localQtyChk) {?>
													<td>&nbsp;</td>
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">
														<?=($netWt!=0)?number_format($netWt,2,'.',''):"";?>
													</td>		
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">
														<?=($adjustWt!=0)?number_format($adjustWt,2,'.',''):"";?>
													</td>
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">
														<?=($totalLocalQuantity!=0)?number_format($totalLocalQuantity,2,'.',''):"";?>
													</td>
													<? }?>
												</tr>
												<? }  ?>
												<tr bgcolor="#FFFFFF">
													<td colspan="4" nowrap class="listing-item" align="right"><span class="listing-head">TOTAL</span>&nbsp;&nbsp;</td>
													<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
														<strong><? echo number_format($totalWt,2);?></strong></td>
													<? if($rateNAmount) {?>
													<td class="listing-item" align="right" nowrap="nowrap"><strong><?//echo number_format($netPrate,2);?></strong>&nbsp;</td>
													<td class="listing-item" align="right" nowrap="nowrap"><strong><?echo number_format($netPamount,2);?></strong>&nbsp;</td>
													<? }?>
													<? if ($localQtyChk) {?>
													<td>&nbsp;</td>
													<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
														<strong><?=number_format($grandTotalNetWt,2);?></strong></td>
													<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
														<strong><?=number_format($totalAdjustWt,2);?></strong></td>
													<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
														<strong><?=number_format($grandTotalLocalQty,2);?></strong></td>
													<? }?>		
												</tr>
											<? }?>
										</table>
									</td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td colspan="17" height="10"></td>
								</tr>
								<tr bgcolor="#FFFFFF"> 
									<td colspan="16" align="left" class="listing-head">
										<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
										<?
										if( sizeof($processSummaryRecords) && $proSummary!=""){
										?>
											<tr bgcolor="white"> 
												<td colspan="8" align="center" class="listing-head">Fish-Process-Summary</td>
											</tr>
											<tr bgcolor="#f2f2f2" align="center"> 
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Process Code</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Effective Wt </td>
												<? if($rateNAmount) {?>
												<td width="15%" class="listing-head" align="center">Rate</td>
												<td width="15%" class="listing-head" align="center">Amount </td>
												<?php }?>
												<? if ($localQtyChk) {?>
												<td>&nbsp;</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Net Wt</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Adjust Wt</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" title="Grade-Count Adj">Gd-Ct Adj</td>
													<td class="listing-head" style="padding-left:10px; padding-right:10px;">Local Quantity<br>
												<span style="font-size:10px;line-height:normal;">(Local+<br>Wastage+<br>Soft)</span>
												</td>
												<? }?>
											</tr>
											<?php
											$k=0;
											$totalWt	=	"";
											$totalLocalQuantity = 0;
											$grandTotalNetWt = 0;
											$grandTotalLocalQty = 0;
											$totalAdjustWt = 0;	
											$totalGdCtAdjQty = 0;
											$pramount=0;
											$netPramount=0;
											foreach ($processSummaryRecords as $psr) {
											$k++;
											$fishRec	= $fishmasterObj->find($psr[1]);
											$fishName	= $fishRec[1];
											$processCodeId	=	$psr[2];
											$processCodeRec	=	$processcodeObj->find($processCodeId);
											$processCode	=	$processCodeRec[2];
											//$payableWt	=	$cer[28];
											$totalQty	=	$psr[4];
											$totalWt	+=	$totalQty;
											
											$adjustWt	= $psr[6];
											$totalAdjustWt 	+= $adjustWt;
											$localQty	= $psr[7];
											$wastageQty	= $psr[8];
											$softQty	= $psr[9];
											$totalLocalQuantity =  $localQty + $wastageQty + $softQty;
											$grandTotalLocalQty += $totalLocalQuantity;
											$gradeCountAdjQty = $psr[10];
											$totalGdCtAdjQty += $gradeCountAdjQty;
											$pramount=$psr[11];
											//$netWt		 = $psr[5];
											$netWt		 = $totalQty+$adjustWt+$totalLocalQuantity;
											$grandTotalNetWt += $netWt; 
											$netPramount+=$pramount;
											$ratePSF=$pramount/$totalQty;
											$netratePSF+=$ratePSF;
											?>
											<tr <? if($rateNAmount) { if ($pramount>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>> 
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
	                        					<?=$fishName?>
												</td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;">
												<?=$processCode?>
												</td>
												<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"> 
												<?=$totalQty?>
												</td>
												<? if($rateNAmount) {?>
												<td width="15%" class="listing-item" align="center"><strong><?=number_format($ratePSF,2,'.','');?></strong></td>
												<td width="15%" class="listing-item" align="center"><strong><?=number_format($pramount,2,'.','');?></strong> </td>
												<?php }?>
												<? 
												if ($localQtyChk) {
												?>
												<td>&nbsp;</td>
												<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">
													<?=($netWt!=0)?number_format($netWt,2,'.',''):"";?>
												</td>
												<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;"><?=($adjustWt!=0)?$adjustWt:"";?></td>
												<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">
													<?=($gradeCountAdjQty!=0)?$gradeCountAdjQty:"";?>
												</td>	
												<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">
													<?=($totalLocalQuantity!=0)?number_format($totalLocalQuantity,2,'.',''):"";?>
												</td>
												<? }?>
											</tr>
											<?php
											}
											?>
											<tr bgcolor="#FFFFFF">
												<td colspan="2" nowrap class="listing-head" align="right">TOTAL&nbsp;&nbsp;</td>
												<td class="listing-item" align="right" nowrap="nowrap"><strong> 
												  <? echo number_format($totalWt,2);?>
												  </strong>&nbsp;&nbsp;
												</td>
												<? if($rateNAmount) {?>
												<td width="15%" class="listing-item" align="center">&nbsp;<strong><?//=number_format($netratePSF,2,'.','');?></strong></td>
												<td width="15%" class="listing-item" align="center"><strong><?=number_format($netPramount,2,'.','');?></strong> </td>
												<?php }?>
												<? if ($localQtyChk) {?>
												<td>&nbsp;</td>
												<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
												<strong><?=number_format($grandTotalNetWt,2);?></strong></td>
												<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
												<strong><?=number_format($totalAdjustWt,2);?></strong></td>
												<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
												<strong><?=number_format($totalGdCtAdjQty,2);?></strong></td>	
												<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px;padding-right:10px;">
												<strong><?=number_format($grandTotalLocalQty,2);?></strong></td>
												<? }?>	
											</tr>
											<? }?>
										</table>
									</td>
									<td align="center" class="listing-head"></td>
								</tr>
								<tr bgcolor="#FFFFFF"> 
									<td colspan="16" align="center" class="listing-head">					</td>
									<td width="50%" align="center" class="listing-head" valign="top">					</td>
								</tr>
								<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
								<tr>
									<td colspan="17" height="5"></td>
								</tr>
								<tr><td colspan="17">
									<table width="30%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
										<?
										if( sizeof($fishWiseCatchSummaryRecords) && $fishCatchSummary!=""){
									   ?>
										<tr bgcolor="white"> 
											<td colspan="4" align="center" class="listing-head">Fish Wise Catch Summary</td>
										</tr>
										<tr bgcolor="#f2f2f2"> 
											<td class="listing-head">&nbsp;&nbsp;Fish</td>
											<td class="listing-head" align="center">Effective Wt </td>
											<? if($rateNAmount) {?>
											<!--<td class="listing-head" align="center">Rate </td>-->
											<td class="listing-head" align="center">Amount </td>
											<?php }?>
										</tr>
										<?
										$k=0;
										$totalWt	=	"";
										$nettotalAmtfs	=0;
										$netratefc=0;
										foreach($fishWiseCatchSummaryRecords as $fcr){
										$k++;

										$fishRec		=	$fishmasterObj->find($fcr[1]);
										$fishName		=	$fishRec[1];
										
											
										$processCodeId	=	$fcr[2];
										$processCodeRec		=	$processcodeObj->find($processCodeId);
										$processCode	=	$processCodeRec[2];
											
										//$payableWt	=	$cer[28];
										
										$totalQty		=	$fcr[4];
										$totalWt	+=	$totalQty;
										$ratefc=$fcr[5];
										$totalAmt		=	$fcr[6];
										$nettotalAmtfs	+=	$totalAmt;
										$netratefc+=$ratefc;
										
										?>
										<tr <? if($rateNAmount) { if ($totalAmt>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>> 
											<td class="listing-item" nowrap>&nbsp;&nbsp; 
												<?=$fishName?>                                    </td>
											<td class="listing-item" align="right"> 
												 <?=$totalQty?>
											&nbsp;&nbsp; </td>
											<? if($rateNAmount) {?>
											<!--<td class="listing-item" align="right"> 
											<?//=$ratefc;?>
											&nbsp;&nbsp; </td>-->
											<td class="listing-item" align="right"> 
											<?=number_format($totalAmt,2);?>
											&nbsp;&nbsp; </td>
									  <?php }?>
									</tr>
									<? }
									?>
									<tr bgcolor="#FFFFFF">
										<td colspan="1" nowrap class="listing-item" align="right"><span class="listing-head">TOTAL</span>&nbsp;&nbsp;</td>
										<td class="listing-item" align="right" nowrap="nowrap"><strong> 
											<? echo number_format($totalWt,2);?>
											</strong>&nbsp;&nbsp;</td>
										   <? if($rateNAmount) {?>
										 <!-- <td>&nbsp;</td>--><td><?=number_format($nettotalAmtfs,2);?></td>
										  <?php }?>
									</tr><? }?>
								</table>
							</td>
						</tr>	
						<tr>
							<td colspan="17" height="10"></td>
						</tr>
						<tr>
							<td colspan="17">
							<?
							if( sizeof($wtChallanWiseSummary) && $wtChallanSummary!=""){
							?>
								<table width="50%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
									<tr bgcolor="white"> 
										<td colspan="13" align="center" class="listing-head">Wt Challan No Wise Summary</td>
									</tr>
									<tr bgcolor="#f2f2f2" align="center">
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td> 
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Wt Challan No </td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Process Code </td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Count</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Grade</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Quantity </td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Adjust Qty </td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total Quantity </td>
										<? if ($localQtyChk) {?>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Local Quantity <br>
										<span style="font-size:10px;line-height:normal;">(Local+<br>Wastage+<br>Soft)</span>
										</td>
									   <? }?>
										<? if($rateNAmount) {?>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Rate</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</td>
										<? }?>
									</tr>
									<?
									$k=0;
									$totalWt	=	"";
									$totalLocalQuantity = "";
									$grandTotallocalQty = "";
									$totalQty = "";
									$grandTotalQty = "";
									$totalAdjustWt = "";
									$totalCrate="";
									$totalCamount="";
									foreach ($wtChallanWiseSummary as $wcs) {
										$k++;
										$entryDate		=	$wcs[1];
												
										/*
										$wtChallanNo	=	$wcs[3];
										$weighmentChallanNo	=	$wcs[3];
										*/
										$wtChallanNo	=	$wcs[17];
										
										$weighmentChallanNo = "";
										if ($wtChallanNo!=$prevWtChallanNo) {
											$weighmentChallanNo	=	$wcs[17];
										}
											
									$enteredDate = "";
									if ($entryDate!=$prevEntryDate || $wtChallanNo!=$prevWtChallanNo) {
										$array			=	explode("-",$wcs[1]);
										$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
									}
						
									$supplierId			=	$wcs[2];
									$supplierName = "";
									if ($supplierId!=$prevSupplierId || $wtChallanNo!=$prevWtChallanNo) {
										$supplierName		=	$wcs[9];
									}
									$fishName			=	$wcs[10];
									$processCode		=	$wcs[11];
											
									$countValues		=	$wcs[6];
									$grade	= "";
									if ($countValues=="") {
										$grade				=	$wcs[12];
									}
									$effectiveWt	=	$wcs[8];
									$totalWt	+=	$effectiveWt;

									$adjustWt	=	$wcs[13];
									$totalAdjustWt += $adjustWt;

									$localQty	=	$wcs[14];
									//$totalLocalQty += $localQty;

									$wastageQty	=	$wcs[15];
									//$totalWastageQty += $wastageQty;

									$softQty	=	$wcs[16];
									//$totalSoftQty   += $softQty;
									//$adjustWt + Edited on 23-02-08
									$totalLocalQuantity =  $localQty + $wastageQty + $softQty;
									$grandTotalLocalQty += $totalLocalQuantity;

									$totalQty = $effectiveWt + $adjustWt;
									$grandTotalQty += $totalQty;
									$crate=$wcs[18];
									$camount=$wcs[19];
									$totalCrate+=$crate;
									$totalCamount+=$camount;
									?>
									<tr <? if($rateNAmount) { if ($camount>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$enteredDate?></td> 
										<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$weighmentChallanNo?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$supplierName?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;" ><?=$fishName?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$processCode?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$countValues?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$grade?></td>
										<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$effectiveWt?></td>
										<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$adjustWt?></td>
										<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><? echo number_format($totalQty,2);?></td>
										<? if ($localQtyChk) {?>
										<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><? echo number_format($totalLocalQuantity,2);?></td>
										 
										<? }?>
										 <? if($rateNAmount) {?>
										<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">&nbsp;<?=$crate;?></td>
										<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">&nbsp;<?=$camount;?></td>
										<? }?>
									</tr>
									<? 
										$prevWtChallanNo 	= 	$wtChallanNo;
										$prevSupplierId	=	$supplierId;
										$prevEntryDate	=	$entryDate;
									}
									?>
									<tr bgcolor="#FFFFFF">
										<td colspan="7" align="right" nowrap class="listing-head">TOTAL :&nbsp;&nbsp;</td>
										<td class="listing-item" align="right" nowrap style="padding-left:10px; padding-right:10px;"><strong><? echo number_format($totalWt,2);?></strong></td>
										<td class="listing-item" align="right" nowrap style="padding-left:10px; padding-right:10px;"><strong><? echo number_format($totalAdjustWt,2);?></strong></td>
										<td class="listing-item" align="right" nowrap style="padding-left:10px; padding-right:10px;"><strong><? echo number_format($grandTotalQty,2);?></strong></td>
										<? if ($localQtyChk) {?>
										<td class="listing-item" align="right" nowrap style="padding-left:10px; padding-right:10px;"><strong><? echo number_format($grandTotalLocalQty,2);?></strong></td>
										<? }?>
										<? if($rateNAmount) {?>
										<td class="listing-item" align="right" nowrap="nowrap">&nbsp;<strong><? //echo number_format($totalCrate,2);?></strong></td>
										<td class="listing-item" align="right" nowrap="nowrap">&nbsp;<strong><? echo number_format($totalCamount,2);?></strong></td>
										<? }?>
									</tr>
                                </table>
								<? }?>
							</td>
						</tr>
						<?
						if( sizeof($declaredWtRecords) && $supplierMemo!=""){
						?>
						<tr>
							<td colspan="17">
							<table width="55%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
								<tr bgcolor="white"> 
									<td colspan="4" align="center" class="listing-head">Fish-Process Code-Grade/Count-Declared Wt -Summary</td>
                                </tr>
								<tr bgcolor="#f2f2f2"> 
                                    <td width="6%" class="listing-head">&nbsp;&nbsp;Fish</td>
                                    <td width="9%" class="listing-head" align="center">Process Code</td>
                                    <td width="9%" class="listing-head">&nbsp;&nbsp;Grade/Count</td>
                                    <td width="15%" class="listing-head" align="center">Decl.Qty</td>
                                </tr>
                                <?
								$j=0;
								$gradeCode="";
								$totalWt	=	"";
								$prevFishId = 0;
								$prevProcessCodeId = 0;
								foreach($declaredWtRecords as $sdr){
								$j++;
								
								$catchEntryId	=	$sdr[0];
								
								$sFishId			=	$sdr[1];
								$fishName = "";
								if($prevFishId!=$sFishId){
									$fishName		=	$sdr[11];
								}
								
								$processCodeId	=	$sdr[2];	
								$processCode	= "";
								if($prevProcessCodeId!=$processCodeId){
									$processCode	=	$sdr[12];
								}
									
								$declCount		=	$sdr[10];
												
								$declWt	=	$sdr[13];
								$totalWt	+=	$declWt;
										
								?>
								<tr bgcolor="#FFFFFF"> 
									<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$declCount?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$declWt?></td>
                                </tr>
                                  <? 
								  $prevFishId = $sFishId;
								  $prevProcessCodeId = $processCodeId;
								  } 
								  ?>
								<tr bgcolor="#FFFFFF">
									<td colspan="3" nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?></strong></td>
                                </tr>								  
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="17" height="5"></td>
					</tr>
					<? } else if($supplierMemo!="") {?>
					<tr bgcolor="white"> 
						<td colspan="17"  class="err1" height="5" align="center"> 
                                <?=$msgNoRecords;?>
						</td>
                   </tr>
				<? }?>
				<?
				 if( sizeof($supplierWiseDeclaredRecords) && $declWtSummary!=""){
				?>
					<tr>
						<td colspan="17">
							<table width="55%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
								<? $columnSize = sizeof($processCountWiseDeclaredRecords);?>
                                <tr bgcolor="white"> 
									<td colspan="<?=$columnSize+3;?>" align="center" class="listing-head">Declared Wt -Settlement Summary</td>
                                </tr>
								<tr bgcolor="#f2f2f2">
									<td colspan="2" class="listing-head">&nbsp;</td>
                                    <td class="listing-head" colspan="<?=$columnSize?>" align="center">Process Code &amp; Decl.Count </td>
                                    <td class="listing-head" align="center">&nbsp;</td>
                                </tr>
                                <tr bgcolor="#f2f2f2" align="center"> 
									<td class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier RM Challan Date </td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier RM Challan No </td>
									<?
									foreach($processCountWiseDeclaredRecords as $ssr){
										$processCode 	= $ssr[8];
										$declCount 		= $ssr[6];
									?>
                                    <td class="listing-head">
										<table width="50" border="0" align="center">
											<tr>
												<td class="listing-head" align="center"><?=$processCode?></td>
											</tr>
											<tr>
												<td class="listing-head" align="center"><?=$declCount?></td>
											</tr>
										</table>
									</td>
									<? }?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Total.Qty</td>
                                  </tr>
                                  <?
								  $j=0;
								  $gradeCode="";
								  $totalWt	=	"";
								  $prevRecDate = "";
								  foreach($supplierWiseDeclaredRecords as $ssr) {
									$j++;
												
									$catchEntryId	=	$sdr[0];
												
									$supplierChallanNo = $ssr[3];
												
									$sChallanDate = $ssr[4];
									$supplierChallanDate = "";
									if ($prevRecDate!=$sChallanDate) {
										$array			=	explode("-",$sChallanDate);
										$supplierChallanDate	=	$array[2]."/".$array[1]."/".$array[0];
									}
									?>
									<tr bgcolor="#FFFFFF"> 
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$supplierChallanDate?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$supplierChallanNo?></td>
										<?
										$declQty = "";
										$totalDeclQty = 0;
										$i=0;
										$qty = 0;
										foreach ($processCountWiseDeclaredRecords as $ssr)
										{
											 $i++;
											$processCodeId 	= $ssr[2];
											$declCount 		= $ssr[6];
											#finding Declared wt for each cell
											$declQty = $dailycatchsummaryObj->getDeclaredWt($sChallanDate, $supplierChallanNo, $declCount, $processCodeId);
											//$declQty = $declaredQtyRec[0];

											$totalDeclQty +=$declQty;
																
										?>
										<td class="listing-item" nowrap  align="right" style="padding-left:5px; padding-right:5px;"><? if ($declQty!=0) echo number_format($declQty,2,'.','');?></td>
										<? }?>
										<?
											$grandTotalWt+=$totalDeclQty;
										?>
										<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($totalDeclQty,2);?></td>
									</tr>
									  <? 
									  $prevRecDate	=	$sChallanDate;
									  }
									  ?>
									<tr bgcolor="#FFFFFF">
										<td colspan="2" align="right" nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">TOTAL DECL WT:</td>
										<?
										$grandTotalDeclWt = "";
										foreach($processCountWiseDeclaredRecords as $ssr)
										{
											$grandTotalDeclWt = $ssr[5];
											//$declCount 	= $ssr[6];
												
											//$grandTotalDeclWt = $dailycatchsummaryObj->getGrandTotalWt($declCount); edited on 26-1-08
										?>
										<td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=$grandTotalDeclWt?></strong></td>
										<? }?>
										<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalWt,2);?></strong></td>
									</tr>								  
                                </table>
							</td>
						</tr>
						<? } else if($declWtSummary!="") {?>
						<tr bgcolor="white"> 
							<td colspan="17"  class="err1" height="5" align="center"> 
                                <?=$msgNoRecords;?>
							</td>
                        </tr>
						<? }?>
						<? } else {	?>
                        <tr bgcolor="white"> 
							<td colspan="17"  class="err1" height="5" align="center"> 
                                <?=$msgNoRecords;?>                              
							</td>
						</tr>
					<? } ?>
					<!-- New Type RM Matrix Horizontal Row =Fish,Process Code Vertical=Date/Wt.CNo-->
					</table>
				</td>
             </tr>
             <? if(sizeof($RMChallanWiseRecords)>0 && $RMMatrix!=""){?>
			<tr bgcolor="white">
				<td colspan="17"  height="5" align="center" style="padding-left:10PX;">
					<table width="55%" border="0" align="left" cellpadding="0" cellspacing="1" bgcolor="#999999">
					<? $columnSize = sizeof($rmSummaryMatrixRecords);?>
						<tr bgcolor="white"> 
							<td colspan="<?=$columnSize+3;?>" align="center" class="listing-head">RM  Summary Matrix </td>
                        </tr>
						<tr bgcolor="#f2f2f2">
                            <td colspan="2" class="listing-head">&nbsp;</td>
                            <td class="listing-head" colspan="<?=$columnSize?>" align="center"> Process Code &amp; Grade/Count </td>
                            <td class="listing-head" align="center">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#f2f2f2" align="center"> 
							<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"> RM Challan Date </td>
							<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Wt. Challan No </td>
							<?
							foreach ($rmSummaryMatrixRecords as $pcsr) {
							$catchEntryId	=	$pcsr[0];
				
							$fishRec	=	$fishmasterObj->find($pcsr[1]);
							$fishName		=	$fishRec[1];
			
							$processCodeId	=	$pcsr[2];
							$processCodeRec	=	$processcodeObj->find($processCodeId);
							$processCode	=	$processCodeRec[2];
												
							$count		=	$pcsr[3];
													
							$effectiveWt	=	$pcsr[9];
							$totalWt	+=	$effectiveWt;
												
							$receivedBy	=	$pcsr[8];
							$gradeCode	=	"";
							$rmGradeId = "";
							if ($count=="" || $receivedBy=='B' || $receivedBy=='G') {
									$rmGradeId = $pcsr[7];
									$gradeRec			=	$grademasterObj->find($pcsr[7]);
									$gradeCode			=	stripSlash($gradeRec[1]);
							}
							?>
                            <td style="padding-left:2px; padding-right:2px">
								<table width="50" border="0" align="center">
									<tr>
										<td class="listing-head" align="center" style="line-height:normal; font-size:10px;"><?=$processCode?></td>
                                    </tr>
                                    <tr>
                                         <td class="listing-head" align="center" style="line-height:normal; font-size:10px;">
										  <? 
										  if($count=="") {
										  	 echo $gradeCode;
											} else {
												echo $count;
											} 										  
										  ?>
										  </td>
									</tr>
                                 </table>
							</td>
							<? }?>
                            <td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Total.Qty</td>
                          </tr>
                          <?
							$j=0;
							$gradeCode="";
							$totalWt	=	"";
							$prevRecDate = "";
							$grandTotalWt = "";
							foreach($RMChallanWiseRecords as $rmcr){
							$j++;
							$RMEntryId	=	$rmcr[0];
							$RMChallanNo = $rmcr[13];
							$sChallanDate = $rmcr[1];
							$RMChallanDate = "";
							if($prevRecDate!=$sChallanDate){	
								$RMChallanDate	= dateFormat($sChallanDate);	
							}				
							?>
                            <tr bgcolor="#FFFFFF"> 
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><?=$RMChallanDate?></td>
                                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><?=$RMChallanNo?></td>
									<? 
									$effectiveQty = "";
									$totalEffectiveQty = 0;
									$i=0;
									$qty = 0;
									foreach($rmSummaryMatrixRecords as $pcsr)
									{
										 $i++;										
										$entryFishId 	=	$pcsr[1];	
										$processCodeId	=	$pcsr[2];										
										$count			=	$pcsr[3];																			
										$receivedBy		=	$pcsr[8];
										$gradeCode	=	"";
										$rmGradeId = "";
										if($count=="" || $receivedBy=='B' || $receivedBy=='G'){
											$rmGradeId = $pcsr[7];											
										}														
										#finding Effective wt for each cell										
										$effectiveQty = $dailycatchsummaryObj->getEffectiveWt($RMEntryId,$entryFishId, $processCodeId, $count, $rmGradeId);
										
										$totalEffectiveQty +=$effectiveQty;
										
									?>
                                <td class="listing-item" nowrap  align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><?=$effectiveQty?></td>
								<? }?>
								<? 
									$grandTotalWt+=$totalEffectiveQty;
								?>
                               <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><? echo number_format($totalEffectiveQty,2);?></td>
                            </tr>
                             <? 
							 $prevRecDate	=	$sChallanDate;
							} 
							?>
							<tr bgcolor="#FFFFFF">
                               <td colspan="2" align="right" nowrap class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">TOTAL  WT:</td>
                               <?
								foreach($rmSummaryMatrixRecords as $pcsr)
								{
									$totalColumnEffectiveWt	=	$pcsr[9];
								?>
                                <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><strong><?=$totalColumnEffectiveWt?></strong></td>
								<? }?>
                                <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><strong><? echo number_format($grandTotalWt,2);?></strong></td>
                            </tr>								  
						</table>
					</td>
				</tr>
                <? }?>
<!-- Local Qty Report Start here-->
				<? if ($localQtyReportChk && $dailyCatchReportRecords>0) {?>
				<tr>
					<TD>
						<table width="99%" cellpadding="2" cellspacing="1" bgcolor="#999999" align="center">
							<tr bgcolor="#f2f2f2" align="center">
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Date</th>	
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">Wt Challan No</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">FISH</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">PROCESS</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">COUNT</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">GRADE</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">REMARKS</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">QUANTITY</th>	
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">RATE</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">AMOUNT</th>	
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Qty</th>
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Rate</th>  
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Qty</th> 
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Rate</th> 
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Qty</th> 
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Rate</th> 
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Qty</th> 
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Rate</th> 
								<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Total Rate</th>	
							</tr>
	  
							<?php
							$grandTotalEffectiveWt	=	"";			
							$grandTotalActualAmount = "";	
							$prevFishId	= "";	
							$prevPurchaseSettledDate = "";
							$grandTotalLocalQtyRate = "";
							$grandTotalWastageQtyRate	= "";
							$grandTotalAdjstWtRate = "";
							$totalLocalRate = "";
							$grandTotalLocalRate = "";
							$totalAdjustWt  = "";
							$totalLocalQty = "";
							$totalWastageQty = "";
							$totalSoftQty = "";
							$selPrevEntryDate = "";
							$enteredDate = "";
							$i=0;		
							while ($dcr=$dailyCatchReportResultSetObj->getRow()) {
								$i++;	
								$catchEntryId		=	$dcr[0];
								$selEntryDate		= 	$dcr[3];
								$enteredDate = "";
								if ($selPrevEntryDate!=$selEntryDate) {	
									$enteredDate	=	dateFormat($selEntryDate);
								}
								$challanNo		=	$dcr[52];			
								$WtChallanNumber 	=	"";
								if ($prevChallanNo != $challanNo) {
									$WtChallanNumber = $dcr[52];
								}
								$selFishId			=	$dcr[11];
								$fishName		=	"";
								if ($prevFishId	!= $selFishId) {
									$fishRec	=	$fishmasterObj->find($selFishId);
									$fishName	=	$fishRec[1];
								}
							
								$processCodeRec		=	$processcodeObj->find($dcr[12]);
								$processCode		=	$processCodeRec[2];				
								
								$selectRate		=	$dcr[33];
															
								$actualRate		=	$dcr[34];
							
								$paymentBy	=	$dcr[44];
								$receivedBy	=	$dcr[48];
								
								$count		=	$dcr[13];
								$countAverage	=	$dcr[14];
								$gradeCode = "";
								if ($count == "" || $receivedBy=='B') {
									$gradeRec		=	$grademasterObj->find($dcr[37]);
									$gradeCode		=	stripSlash($gradeRec[1]);
								}
						
							
								$localQty	=	$dcr[16];
								$totalLocalQty += $localQty;

								$wastageQty	=	$dcr[17];
								$totalWastageQty += $wastageQty;

								$softQty	=	$dcr[18];
								$totalSoftQty   += $softQty;

								#Find the Wastage Rate Percentage
								list($localRatePercent, $wastageRatePercent, $softRatePercent) = $wastageratepercentageObj->getWastageRatePercentage();
								
								$localQtyRate 	= (($selectRate*$localRatePercent/100));
								$wastageQtyRate = (($selectRate*$wastageRatePercent/100));
								$softQtyRate	= (($selectRate*$softRatePercent/100));	

								$totalLocalQtyRate 	= $localQty * $localQtyRate;
								$totalWastageQtyRate 	= $wastageQty * $wastageQtyRate;
								$totalSoftQtyRate	= $softQty * $softQtyRate;

								$grandTotalLocalQtyRate += $totalLocalQtyRate;		
								$grandTotalWastageQtyRate += $totalWastageQtyRate;
								$grandTotalSoftQtyRate	+= $totalSoftQtyRate;

								$gradeCountAdj	=	$dcr[46]; // Don't add $gradeCountAdj in Lcal Qty report (said on 18-01-07)
						
								$adjustWt	=	$dcr[20];
								$totalAdjustWt += $adjustWt;

								$adjustWtRate  	=	$adjustWt * $selectRate;
								$grandTotalAdjstWtRate += $adjustWtRate;

								//Find the Total Wastage Rate
								$totalLocalRate = $adjustWtRate + $totalLocalQtyRate + $totalWastageQtyRate + $totalSoftQtyRate;

								$grandTotalLocalRate += $totalLocalRate;
								
								$effectiveWt	=	$dcr[28];
						
							$remarks		=	$dcr[23];
							
							$grandTotalEffectiveWt	+=	$effectiveWt;
							$grandTotalActualAmount += 	$actualRate;
							$grandTotalselectAmount +=$selectRate	
							
							?>
							<tr <? if($rateNAmount) { if ($actualRate>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>>
								<td class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="center"><?=$enteredDate?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$WtChallanNumber?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$fishName?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$processCode?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" nowrap="nowrap"><?=$count?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$gradeCode?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$remarks?></td>	
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><? echo number_format($effectiveWt,2);?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$selectRate?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$actualRate?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$adjustWt?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><? echo number_format($adjustWtRate,2,'.','');?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$localQty?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalLocalQtyRate?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$wastageQty?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalWastageQtyRate?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$softQty?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalSoftQtyRate?></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalLocalRate,2,'.','');?></strong></td>		
							</tr>
							  <?
							  $prevChallanNo = $challanNo;
							  $prevFishId	= $selFishId;	
							  $selPrevEntryDate = $selEntryDate;	  
							  }	
							  ?>
							<tr bgcolor="#FFFFFF">
								<td height='20' class="listing-item">&nbsp;</td>
								<td height='20' class="listing-item">&nbsp;</td>
								<td height='20' class="listing-item">&nbsp;</td>
								<td height='20' class="listing-item">&nbsp;</td>
								<td height='20' class="listing-item">&nbsp;</td>
								<td height='20' class="listing-head" align="right" nowrap style="padding-left:2px; padding-right:2px; font-size:7pt;">Total:</td>	
								<td height='20' class="listing-item">&nbsp;</td>
								<td height='20' class="listing-item">&nbsp;<strong><? echo number_format($grandTotalEffectiveWt,2);?></strong>   </td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? //echo number_format($grandTotalselectAmount,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalActualAmount,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalAdjustWt,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalAdjstWtRate,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalLocalQty,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalLocalQtyRate,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalWastageQty,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalWastageQtyRate,2);?></strong></td>
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalSoftQty,2);?></strong></td>
								<td height='20' nowrap="nowrap" class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalSoftQtyRate,2);?></strong></td> 
								<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><strong><? echo number_format($grandTotalLocalRate,2);?></strong></td>	
							</tr>
						</table>
					</TD>
				</tr>
				<? }?>
				<!-- Daily Summary View Starts Here -->
				<? if ($dailySummary && $dailySummaryRecordSize>0) {?>
				<tr>
					<TD colspan="15" style="padding-left:10px;">
						<table cellpadding="1"  width="50%" cellspacing="1" border="0" bgcolor="#999999">
							<tr bgcolor="White">
								<TD class="listing-head" colspan="13" align="center">Daily Summary View</TD>
							</tr>
							<?
							if ($dailySummaryRecordSize > 0) {
							$i	=	0;
							?>
							<tr  bgcolor="#f2f2f2" align="center">
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Date</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Effective<br> Qty</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Adjust<br> Qty</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px" title="Grade-Count Adj">Gd-Ct<br/> Adj</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Total<br> Quantity</td>
								<td>&nbsp;</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Local<br> Qty</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Wastage<br> Qty</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Soft<br> Qty</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Last <br>Challan No</td>
								<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Missing<br> Challan No.s</td>
								<? if($rateNAmount) {?>
									<!--<td width="15%" class="listing-head"  nowrap style="padding-left:5px; padding-right:5px">Rate</td>-->
                                <td width="15%" class="listing-head"  nowrap style="padding-left:5px; padding-right:5px">Amount </td>
								<?php }?>
							</tr>
							<?php
							$totalQty = "";
							$totalEffectiveQty = "";
							$totalAdjustQty = "";
							$grandTotalQty = "";
							$totalLocalQty = "";
							$totalWastageQty = "";
							$totalSoftQty = "";
							$totalGdCtAdjQty = 0;
							$gradeCountAdjQty = "";
							$dsvTotal=0;
							$netdsvTotal=0;
							while ($dsr=$dailySummaryResultSetObj->getRow()) {
			
							$challanDate = dateFormat($dsr[1]);
						
							$effectiveQty	= $dsr[2];
							$totalEffectiveQty += $effectiveQty;

							$adjustQty	= $dsr[4];
							$totalAdjustQty += $adjustQty;

							$totalQty = $effectiveQty + $adjustQty; // Total Qty = EffectiveQty + AdjustQty
							$grandTotalQty += $totalQty;
				
							$localQty	= $dsr[5];
							$totalLocalQty += $localQty;

							$wastageQty	= $dsr[6];
							$totalWastageQty += $wastageQty;

							$softQty	= $dsr[7];
							$totalSoftQty += $softQty;
								
							$gradeCountAdjQty = $dsr[8];
							$totalGdCtAdjQty += $gradeCountAdjQty;
							$dsvTotal=$dsr[9];
							$netdsvTotal+=$dsvTotal;
							# Missing Challan Numbers -- Starts Here --				
							$dateC	   =	explode("/", $challanDate);
							$selChallanDate   = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0],$dateC[2]));
							$currentChallanDate = dateFormat($selChallanDate);
							$prevDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-1,$dateC[2]));
							$prevChallanDate = dateFormat($prevDate);

							# Get Billing Comapny Recs
							$getBillCompanyRecs = $homeObj->getBillingCompanyWiseRecs($selChallanDate);
							/* Commented on 10 FEB 2010
							if (sizeof($getBillCompanyRecs)>0) { 
							foreach ($getBillCompanyRecs as $gbcr) {
								//$lastRMChallanNumber 	= $gbcr[1];
								$billingCompanyId	= $gbcr[2];
								# Find the Last RM Challan Number (Max Challan Number)
								$lastRMChallanNumber = $homeObj->getLastChallanNumber($selChallanDate, $billingCompanyId);
								$alphaCode		= $gbcr[3];
								$displayRMChallanNo 	= $alphaCode.$lastRMChallanNumber;
								# Find the Prev Date Last Challan Number (Min Challan Number, Callan Date)
								list($prevLastRMChallanNumber, $selPrevDate)  =  $homeObj->getPrevLastChallanNumber($prevDate, $billingCompanyId, $selDate);
								
								# Find the Missing Challan Numbers
								$missingChallanRecords = array();
								if ($prevLastRMChallanNumber=="") {
									list($startingNumber, $endingNumber) = $manageChallanObj->getChallanRec($selChallanDate, $billingCompanyId);
									$prevLastRMChallanNumber = $startingNumber;
								}
								//echo "<br/>$prevLastRMChallanNumber, $lastRMChallanNumber, $selDate,$billingCompanyId<br/>";	
								if ($prevLastRMChallanNumber!="" && $lastRMChallanNumber!="") {
									$missingChallanRecords = $homeObj->getMissingRecords($prevLastRMChallanNumber, $lastRMChallanNumber, $selChallanDate, $billingCompanyId);
								}
								}
							}
							*/

							?>
							<tr  <? if($rateNAmount) { if ($dsvTotal>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$challanDate;?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($effectiveQty,2);?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($adjustQty,2);?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($gradeCountAdjQty,2);?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($totalQty,2);?></td>
								<td>&nbsp;</td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($localQty,2);?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($wastageQty,2);?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($softQty,2);?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
								<?//$lastRMChallanNumber?>
									<table>
									<?php
									if (sizeof($getBillCompanyRecs)>0) { 
									foreach ($getBillCompanyRecs as $gbcr) {
									//$lastRMChallanNumber 	= $gbcr[1];
									$billingCompanyId	= $gbcr[2];
									# Find the Last RM Challan Number (Max Challan Number)
									$lastRMChallanNumber = $homeObj->getLastChallanNumber($selChallanDate, $billingCompanyId);
									$alphaCode		= $gbcr[3];
									$displayRMChallanNo 	= $alphaCode.$lastRMChallanNumber;
									# Find the Prev Date Last Challan Number (Min Challan Number, Challan Date)
									list($prevLastRMChallanNumber, $selPrevDate)  =  $homeObj->getPrevLastChallanNumber($prevDate, $billingCompanyId, $selChallanDate);
									
									# Find the Missing Challan Numbers
									$missingChallanRecords = array();
									if ($prevLastRMChallanNumber=="") {
										list($startingNumber, $endingNumber) = $manageChallanObj->getChallanRec($selChallanDate, $billingCompanyId);
										$prevLastRMChallanNumber = $startingNumber;
									}
									//echo "<br/>$prevLastRMChallanNumber, $lastRMChallanNumber, $selDate,$billingCompanyId<br/>";	
									if ($prevLastRMChallanNumber!="" && $lastRMChallanNumber!="") {
										$missingChallanRecords = $homeObj->getMissingRecords($prevLastRMChallanNumber, $lastRMChallanNumber, $selChallanDate, $billingCompanyId);
									}
									?>
										<TR><TD class="listing-item"><?=$displayRMChallanNo?></TD></TR>
									<?php
										}
										}
									?>
								</table>
							</td>
							<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
								<table>
								<?php
								if (sizeof($getBillCompanyRecs)>0) { 
								foreach ($getBillCompanyRecs as $gbcr) {
								//$lastRMChallanNumber 	= $gbcr[1];
								$billingCompanyId	= $gbcr[2];
								# Find the Last RM Challan Number (Max Challan Number)
								$lastRMChallanNumber = $homeObj->getLastChallanNumber($selChallanDate, $billingCompanyId);
								$alphaCode		= $gbcr[3];
								$displayRMChallanNo 	= $alphaCode.$lastRMChallanNumber;
								# Find the Prev Date Last Challan Number (Min Challan Number, Challan Date)
								list($prevLastRMChallanNumber, $selPrevDate)  =  $homeObj->getPrevLastChallanNumber($prevDate, $billingCompanyId, $selChallanDate);
								# Find the Missing Challan Numbers
								$missingChallanRecords = array();
								if ($prevLastRMChallanNumber=="") {
									list($startingNumber, $endingNumber) = $manageChallanObj->getChallanRec($selChallanDate, $billingCompanyId);
									$prevLastRMChallanNumber = $startingNumber;
								}
								if ($prevLastRMChallanNumber!="" && $lastRMChallanNumber!="") {
									$missingChallanRecords = $homeObj->getMissingRecords($prevLastRMChallanNumber, $lastRMChallanNumber, $selChallanDate, $billingCompanyId);
								}
								?>
									<TR>
										<TD class="listing-item">
											<table>
												<tr>
												<?php
												$numLine = 3;
												if (sizeof($missingChallanRecords)>0) {
													if (sizeof($missingChallanRecords)<=10) {
												
													$nextRec	=	0;
													$k=0;
													$missingChallan = "";
														foreach ($missingChallanRecords as $key=>$value) {
														$j++;
														$missingChallan = $value;
														$nextRec++;
													?>
													<td class="home-listing-item">
														<? if($nextRec>1) echo ",";?><?=$alphaCode.$missingChallan?>
													</td>
												<?php 
													if($nextRec%$numLine == 0) { 
												?>
											</tr>
											<tr>
														<?php 
														} 	
														} # For Loop Ends Here
												   } else {  # If size greater than 10
													echo sizeof($missingChallanRecords)."&nbsp;Challan Missed";
												  }
												} else if ($lastRMChallanNumber!="") {
											?>
											NIL
											<? }?>
											</tr>
										</table>
									</TD>
								</TR>
							<?php
								}
								}
							?>
						</table>
					</td>	
					<? if($rateNAmount) {?>
					<!--<td width="15%" class="listing-head"  nowrap style="padding-left:5px; padding-right:5px">&nbsp;</td>-->
                    <td width="15%" class="listing-item"  nowrap style="padding-left:5px; padding-right:5px">&nbsp;<?=$dsvTotal;?> </td>
					<?php }?>
				</tr>
				<?
					}
				?>
				<tr bgcolor="WHITE">
					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" align="right">Total:</td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($totalEffectiveQty,2);?></strong></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($totalAdjustQty,2);?></strong></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($totalGdCtAdjQty,2);?></strong></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($grandTotalQty,2);?></strong></td>
					<td>&nbsp;</td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($totalLocalQty,2);?></strong></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($totalWastageQty,2);?></strong></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($totalSoftQty,2);?></strong></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">&nbsp;</td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">&nbsp;</td>
					<? if($rateNAmount) {?>
					<!--<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">&nbsp;</td>-->
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><strong><?=number_format($netdsvTotal,2);?></strong></td>
					<?php }?>
				</tr>
				<?
					}
					else
					{
				?>
				<tr bgcolor="white">
					<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
				</tr>
				<?
					}
				?>
			</table>
		</TD>
	</tr>
	<? }?>
	<!-- Daily Summary View Ends Here -->
<!-- 	Decl Wt Supplier Setlement Starts Here -->
	<?
	if (sizeof($declaredWtSettlementSummaryRecords) && $supSetlmentSummaryChk!="") {
	?>
	<tr>
		<td colspan="17" style="padding-left:10px;">
			<table>
				<TR>
					<TD>
						<table width="55%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
							<tr bgcolor="white"> 
                				<td colspan="6" align="center" class="listing-head">Declared Wt - Settlement Summary</td>
							</tr>								  
							<tr bgcolor="#f2f2f2" align="center"> 
                				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>
                				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>
                				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</td>
                				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.Qty</td>
		 						<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>
								<td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
							</tr>
							<?				
							$totalWt = "";
							$prevFishId = 0;
							$prevProcessCodeId = 0;
							$grandTotalAmount = 0;
							foreach ($declaredWtSettlementSummaryRecords as $sdr) {		
							$catchEntryId	=	$sdr[0];
							$sFishId	=	$sdr[1];
							$fishName = "";
							if ($prevFishId!=$sFishId) {
								$fishName		=	$sdr[11];
							}
									
							$processCodeId	= $sdr[2];	
							$processCode	= "";
							if ($prevProcessCodeId!=$processCodeId) {
								$processCode	=	$sdr[12];
							}
													
							$declCount	= $sdr[10];								
							$declWt		= $sdr[13];
							$ratePerKg 	= $sdr[14];
							$totalAmount	= $declWt * $ratePerKg;

							$grandTotalAmount += $totalAmount;
							$totalWt	+= $declWt;
							?>
							<tr bgcolor="#FFFFFF"> 
                				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$declCount?></td>
								<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$declWt?></td>
								<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$ratePerKg?></td>
								<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$totalAmount?></td>
							</tr>
							 <? 
							$prevFishId = $sFishId;
							$prevProcessCodeId = $processCodeId;
							} 
							?>
							<tr bgcolor="#FFFFFF">
                				<td colspan="3" nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
								<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?></strong></td>
								<td></td>
								<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalAmount,2);?></strong></td>
							</tr>								  
						</table>
					</TD>
				</TR>
				<tr>
					<TD height="5"></TD>
				</tr>
				<tr align="center">
					<TD class="listing-head">COMMISSION</TD>
				</tr>
				<? if (sizeof($declWtSupplierCommissionSummary)>0) {?>
				<tr>
					<TD>
						<table width="55%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">									  
							<tr bgcolor="#f2f2f2" align="center">                 	
                				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Item</td>
                				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Qty</td>
								<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>
								<td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
							</tr>
							 <?				
							$totalWt = "";
							$prevFishId = 0;
							$prevProcessCodeId = 0;
							$grandTotalAmount = 0;
							foreach ($declWtSupplierCommissionSummary as $sdr) {		
							$catchEntryId	=	$sdr[0];
							$sFishId	=	$sdr[1];
							$fishName = "";
							if ($prevFishId!=$sFishId) {
								$fishName		=	$sdr[11];
							}
									
							$processCodeId	= $sdr[2];	
							$processCode	= "";
							if ($prevProcessCodeId!=$processCodeId) {
								$processCode	=	$sdr[12];
							}
													
							$declCount	= $sdr[10];								
							$declWt		= $sdr[13];
							$ratePerKg 	= $sdr[14];	// code rate
							$commissionRate	= $sdr[15];
							$totalAmount	= $declWt * $commissionRate;

							$grandTotalAmount += $totalAmount;
							$totalWt	+= $declWt;
												
							?>
							<tr bgcolor="#FFFFFF">                 	
								<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
								<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$declWt?></td>
								<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$commissionRate?></td>
								<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$totalAmount?></td>
							</tr>
							<? 
							$prevFishId = $sFishId;
							$prevProcessCodeId = $processCodeId;
							} 
							?>
							<tr bgcolor="#FFFFFF">
                				<td nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
								<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?></strong></td>
								<td></td>
								<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalAmount,2);?></strong></td>
							</tr>								  
						</table>
					</TD>
				</tr>
				<? }?>
			</table>
		</td>
	</tr>
	<? }?>
	<!-- 	Decl Wt Supplier Setlement ends Here -->
	<!-- 	RM Supplier Rate Summary Matrix Starts Here -->
	<? 
	  if(sizeof($rmSummaryMatrixRecords)>0 && $RMRateMatrix!=""){
	?>
    <tr bgcolor="white">
		<td colspan="17"  height="5" align="center" style="padding-left:10PX;">
			<table width="55%" border="0" align="left" cellpadding="0" cellspacing="1" bgcolor="#999999">
			<? $columnSize = sizeof($rmSupplierRecords);?>
				<tr bgcolor="white"> 
					<td colspan="<?=$columnSize+4;?>" align="center" class="listing-head">RM Supplier Rate Summary Matrix </td>
                </tr>
				<tr bgcolor="#f2f2f2">
                    <td colspan="4" class="listing-head">&nbsp;</td>
                    <td class="listing-head" colspan="<?=$columnSize?>" align="center"> Supplier Rate(s)</td>
                </tr>
                <tr bgcolor="#f2f2f2" align="center"> 
				     <td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Fish</td>	
                     <td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Process</td>
                     <td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Grade</td>
				     <td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Count</td>	
					 <?
						foreach ($rmSupplierRecords as $rsr) {
						$rmSupplierId	= $rsr[0];
						$rmSupplierName = $rsr[1];
					?>
                    <td style="padding-left:2px; padding-right:2px">
                        <table width="50" border="0" align="center">
							<tr>
								<td class="listing-head" align="center" style="line-height:normal; font-size:10px;"><?=$rmSupplierName?></td>
                            </tr>
                        </table>
					</td>
				  <? }?>
				</tr>
                <?php
				foreach ($rmSummaryMatrixRecords as $pcsr) {
					$catchEntryId	=	$pcsr[0];
					$rmFishId	= $pcsr[1];
					$fishName	= $pcsr[10];
					$processCodeId	=	$pcsr[2];
					$processCode	=	$pcsr[11];
					$count		=	$pcsr[3];
					$effectiveWt	=	$pcsr[9];
					$totalWt	+=	$effectiveWt;										
					$receivedBy	=	$pcsr[8];
					$gradeCode	=	"";
					$rmGradeId = "";
					if ($count=="" || $receivedBy=='B' || $receivedBy=='G') {
						$rmGradeId = $pcsr[7];
						$gradeCode = stripSlash($pcsr[12]);						
					}
				?>
                <tr bgcolor="#FFFFFF"> 
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><?=$fishName?></td>
                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><?=$processCode?></td>	
                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><?=$gradeCode?></td>
                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;"><?=$count?></td>
					<?php
					foreach ($rmSupplierRecords as $rs) {
						$i++;		
						$rmSupId	= $rs[0];
						# Get Distinct Rates
						$distinctRMRates = $dailycatchsummaryObj->getRMSupplierRates($fromDate, $tillDate, $selectADate, $dateSelectFrom, $rmFishId, $processCodeId, $count, $rmGradeId, $rmSupId);
					?>
                    <td class="listing-item" nowrap  align="center">
						<table>
							<tr>
							<?
							$numLine = 3;
							if (sizeof($distinctRMRates)>0) {
								$nextRec	=	0;
								$k=0;
								$selRate = "";
								foreach ($distinctRMRates as $dR) {
									$j++;
									$selRate = $dR[0];
									$nextRec++;
							?>
								<td class="listing-item" style="line-height:normal; font-size:11px;">
								<? if($nextRec>1) echo ",";?><?=$selRate?></td>
								<? if($nextRec%$numLine == 0) { ?>
							</tr>
							<tr>
							<? 
								}	
							}
							}
							?>
							</tr>
						</table>
				    </td>
					<? 
					}
					?>				 
                 </tr>
                 <?php
				  } 
				 ?>
			</table>
		</td>
     </tr>
    <? 
	}
	?>
	<!--  RM Supplier Rate summary Matrix Ends Here-->
	<!-- Qty Summary View Starts Here -->
	<? if ($qtySummary && sizeof($wtChallanQtySummaryRecs)>0) {?>
	<tr>
		<TD colspan="16" style="padding-left:10px;" align="center">
			<table cellpadding="1"  width="30%" cellspacing="1" border="0" bgcolor="#999999">
				<tr bgcolor="White">
					<TD class="listing-head" colspan="10" align="center">Quantity Summary View</TD>
				</tr>
				<?
				if ($wtChallanQtySummaryRecs > 0) {
				$i	=	0;
				?>
				<tr  bgcolor="#f2f2f2" align="center">
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Date</td>
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Challan No</td>
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Effective Qty</td>				
				</tr>
				<?php
				$totalEffectiveQty = "";
				$wtChallanNo 	= "";
				$effectiveQty 	= "";
				
				foreach ($wtChallanQtySummaryRecs as $wcqr) {

					$challanDate = dateFormat($wcqr[1]);
					$wtChallanNo = $wcqr[4];
					$effectiveQty	= $wcqr[3];
					$totalEffectiveQty += $effectiveQty;
				?>
				<tr  bgcolor="WHITE">
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$challanDate;?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$wtChallanNo?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><? echo number_format($effectiveQty,2,'.',',');?></td>
				</tr>
				<?
					}
				?>
				<tr bgcolor="WHITE">
					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" align="right" colspan="2">Total:</td>				
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><strong><? echo number_format($totalEffectiveQty,2,'.',',');?></strong></td>	
				</tr>
				<?
				}
				else
				{
				?>
				<tr bgcolor="white">
					<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
				</tr>
				<?
				}
				?>
			</table>
		</TD>
	</tr>
		<? }?>
	<!-- Qty Summary View Ends Here -->
	<!-- Challan Summary View Starts Here -->
	<? if ($challanSummary && sizeof($challanQtyWiseSummaryRecs)>0) {?>
	<tr>
		<TD colspan="16" style="padding-left:10px;" align="center">
			<table cellpadding="1"  width="30%" cellspacing="1" border="0" bgcolor="#999999">
				<tr bgcolor="White">
					<TD class="listing-head" colspan="10" align="center">Challan Summary View</TD>
				</tr>
				<?php
				if ($challanQtyWiseSummaryRecs > 0) {
				$i	=	0;
				?>
				<tr  bgcolor="#f2f2f2" align="center">
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Date</td>
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Challan No</td>
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Supplier</td>	
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Effective Qty</td>
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Rate</td>
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px">Amt</td>
				</tr>
				<?php
				$totalEffectiveQty = "";
				$wtChallanNo 	= "";
				$effectiveQty 	= "";
				$totSupActualAmt = 0;
				$rateCSV=0;
				foreach ($challanQtyWiseSummaryRecs as $wcqr) {

					$challanDate = dateFormat($wcqr[1]);
					$wtChallanNo = $wcqr[4];
					$effectiveQty	= $wcqr[3];
					$totalEffectiveQty += $effectiveQty;
					$selSupplierName  = $wcqr[6];
					$supplierActualAmt = $wcqr[5];
					$totSupActualAmt += $supplierActualAmt;
					$rateCSV=$wcqr[7];
					$netrateCSV+=$rateCSV;
				?>
				<tr  <? if($rateNAmount) { if ($rateCSV>0){?> bgcolor="#FFFFFF" <?php } else {?> bgcolor="#e0f1fe" <?php }} else {?> bgcolor="#FFFFFF"  <?php }?>  <?//=$listRowMouseOverStyle?> >
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$challanDate;?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$wtChallanNo?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left"><?=$selSupplierName?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><? echo number_format($effectiveQty,2,'.',',');?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px" align="right">&nbsp;<? echo number_format($rateCSV,2,'.',',');?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><? echo number_format($supplierActualAmt,2,'.',',');?></td>
				</tr>
				<?
					}
				?>
				<tr bgcolor="WHITE">
					<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" align="right" colspan="3">Total:</td>				
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><strong><? echo number_format($totalEffectiveQty,2,'.',',');?></strong></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px" align="right"><strong>&nbsp;<? //echo number_format($netrateCSV,2,'.',',');?><strong></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><strong><? echo number_format($totSupActualAmt,2,'.',',');?></strong></td>	
				</tr>
				<?php
					} else {
				?>
				<tr bgcolor="white">
					<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
				</tr>
				<?php
					}
				?>
			</table>
		</TD>
	</tr>
	<? }?>
	<!-- Challan Summary View Ends Here -->			
    <tr> 
		<td colspan="3" align="center" height="5">
		<input type="hidden" name="hidNumOfSubSupplier" id="hidNumOfSubSupplier" value="<?=$numOfSubSupplier?>"></td>
    </tr>
    <tr>
        <td colspan="4" align="center">
		<? if($print==true){?>
		<input type="button" name="Submit2" value=" View / Print" class="button" onclick="if(checkNumSubSupplier(document.frmDailyCatchSummary)) printWindow('PrintDailyCatchSummary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&selUnit=<?=$selectUnit?>&landingCenter=<?=$landingCenterId?>&supplier=<?=$selectSupplier?>&details=<?=$details?>&proCount=<?=$proCount?>&proSummary=<?=$proSummary?>&wChallan=<?=$wChallan?>&fishCatchSummary=<?=$fishCatchSummary?>&fish=<?=$fishId?>&processCode=<?=$processId?>&wtChallanSummary=<?=$wtChallanSummary?>&supplierMemo=<?=$supplierMemo?>&declWtSummary=<?=$declWtSummary?>&rateNAmount=<?=$rateNAmount?>&RMMatrix=<?=$RMMatrix?>&selDate=<?=$selDate?>&dateSelectFrom=<?=$dateSelectFrom?>&localQtyReportChk=<?=$localQtyReportChk?>&subSupplierChk=<?=$subSupplierChk?>&advSearch=<?=$advSearch?>&RMRateMatrix=<?=$RMRateMatrix?>&qtySummary=<?=$qtySummary?>&billingCompany=<?=$billingCompany?>&challanSummary=<?=$challanSummary?>',700,600);" <? if(sizeof($dailyCatchSummaryRecords)==0 && sizeof($processSummaryRecords)==0 && sizeof($fishWiseCatchSummaryRecords)==0 || $localQtyChk || $dailySummary) echo $disabled="disabled";?>>
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
</td>
</tr>	
<br>		
</table>		
</form>
	<script language="javascript">
		function disabrtAmount()
		{
		document.getElementById("rateNAmount").disabled=true;
		document.getElementById("rateNAmount").checked=false;
		}
		function enabrtAmount()
		{
		document.getElementById("rateNAmount").disabled=false;
		}
	</script>
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
	<br>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
