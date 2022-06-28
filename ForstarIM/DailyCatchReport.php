<?php
	require("include/include.php");
	require_once("lib/dailycatchreport_ajax.php");
	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= true;
	$searchMode		= false;
	$confirmed		= "";
	$zeroEntryExist		= false;
	$searchChanged		= false;
	$challanWiseSearch 	= false;

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
	$companySpecific = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canCompanySpecific()) $companySpecific=true;	
	//----------------------------------------------------------

	$localQtyReportChk = $p["localQtyReportChk"];
	if ($localQtyReportChk) $localQtyReportChk = "Checked";
	$selSuppChallanNo	= $p["selSuppChallanNo"];

	if ($p["weighNumber"]!="") $challanWiseSearch = true;
	if ($p["selBillingCompany"]!="") $selBillingCompany = $p["selBillingCompany"];
	
	if ($p["billingCompany"]!="") $billingCompany = $p["billingCompany"];
	
	# Select record between selected date
	$dateFrom = $p["supplyFrom"];
	$dateTill = $p["supplyTill"];

	if ($dateFrom || $dateTill) $searchChanged = true;
	# Search
	if ($p["cmdSearch"]!="" || $p["weighNumber"]!="" && !$searchChanged) {
		$p["supplyFrom"] = "";
		$p["supplyTill"] = "";		
		if ($p["selViewType"]!="") $selViewType = $p["selViewType"];
		
		if($selViewType!="" && $selViewType=='summary')
		{	//echo $selViewType;
			$summaryWiseSearch=true;
			$selsummary="selected";
			$sellot="";
		}
		elseif($selViewType!="" && $selViewType=='lot')
		{
			$lotBasedSearch=true;
			$sellot="selected";
			$selsummary="";
		}
		$weighNumber	=	trim($p["weighNumber"]);
		$billingCompanyRecs	= $dailycatchreportObj->chkBillingCmpnyRecs($weighNumber);
		$cBillingCompanyId = "";
		if (sizeof($billingCompanyRecs)==1) {
			$cBillingCompanyId= $dailycatchreportObj->getBillingCompanyId($weighNumber);
		}
		$p["selWeighment"] = "";
		# Get Main Id
		$challanMainId = "";
		if ($cBillingCompanyId!="") {
			$challanMainId = $dailycatchreportObj->getChallanMainId($weighNumber, $cBillingCompanyId);
		} else if (sizeof($billingCompanyRecs)>0) $challanMainId = $p["challanMainId"];
		
		# Get RM Records
		if ($challanMainId!="") {
			$dailyCatchReportRecords = $dailycatchreportObj ->fetchAllCatchReportRecords($challanMainId,$selViewType);
		}	
		$dailyCatchReport = $dailyCatchReportRecords;
		$searchMode	=	true;	
	} else if ($dateFrom!="" && $dateTill!="") {
		$challanWiseSearch = false;	
		$fromDate	=	mysqlDateFormat($dateFrom);
		$tillDate	=	mysqlDateFormat($dateTill);

		# Supplier Records query from dailycatchsummary_class.php
		$supplierRecords	= $dailycatchreportObj->fetchSupplierRecords($fromDate, $tillDate);

		$weighNumber	=	"";
		$dateFrom	=	"";
		$dateTill	=	"";
		$p["weighNumber"] 	= "";
		$selWeighmentNo		= $p["selWeighment"];	
	
		# For selecting the Weigment Challan No - Supplier wise
		$selectSupplier		= $p["selSupplier"];
		//echo "The supplier is $selectSupplier";
		$billingCompany		= $p["billingCompany"];
		if ($p["viewType"]!="") $viewType = $p["viewType"];
			
		if($viewType!="" && $viewType=='summary')
		{	//echo $selViewType;
			$summarySupplierWiseSearch=true;
			$summary="selected";
			$lot="";
		}
		elseif($viewType!="" && $viewType=='lot')
		{
			$lotSupplierBasedSearch=true;
			$lot="selected";
			$summary="";
		}
		# Get Billing Comapany  Records
		$billingCompanyRecords = $dailycatchreportObj->fetchBillingCompanyRecords($selectSupplier, $fromDate, $tillDate);
		//$rmLotRecords = $dailycatchreportObj->fetchLotRecords($selectSupplier, $fromDate, $tillDate);
		$rmLotRecords	= $rmTestDataObj->fetchAllRecordsRMLotId();

		$weighmentRecords	= $dailycatchreportObj->fetchWeighmentRecords($selectSupplier, $fromDate, $tillDate, $billingCompany);

		$challanMainId	= $selWeighmentNo;	
		//$weighNumber	= $selWeighmentNo;

		$dailyCatchReportRecords =	$dailycatchreportObj->fetchAlldailyCatchReportRecords($challanMainId, $fromDate, $tillDate,$viewType);

		//$dailyCatchReport = $dailycatchreportObj->fetchAlldailyCatchReportRecords($challanMainId, $fromDate, $tillDate);
		$dailyCatchReport = $dailyCatchReportRecords;		
 	}

// 	echo "<pre>";
// 	print_r($dailyCatchReportRecords);
// 	echo "</pre>";
	
	//----------------------------------
	// Checking Any Zero Entry Exist for the selected Weigh Number	
	//----------------------------------
	if ($challanMainId!="" || $challanMainId!=0) {				
		$chkZeroEntryExist = $dailycatchreportObj->chkZeroEntryExist($challanMainId);
		if ($chkZeroEntryExist) $zeroEntryExist = true;
	}

	//----------------------------------
	// IF of Declared wt
	//----------------------------------
	if ($challanMainId!="" || $challanMainId!=0) {
		# Payment By (E->Effective Wt && D->Declared Wt)
		$paymentType = $dailycatchreportObj->getPaymentType($challanMainId);

		# Get Supplier Challan Records based on Effective wt / Decl Wt
		$supplierChallanRecords = $dailycatchreportObj->filterSupplierChallanRecords($challanMainId, $paymentType);
		
		#supplier Declared Wt Records(Suplier Memo)
		if ($challanMainId && $selSuppChallanNo) {
			//echo "ghfg";
			$declaredWtRecords = $dailycatchreportObj->getSupplierDeclaredWtRecords($challanMainId, $selSuppChallanNo,$viewType);
		}
	
		# Checking More Sub-Supplier Exist for that challan
		if ($paymentType=='D') $moreSubSupplierExist = $dailycatchreportObj->numOfSubSupplier($challanMainId);
	//echo $moreSubSupplierExist;
		# -------------------- Billing Company starts Here ----------------
		$companySelected = "";
		if ($selBillingCompanyId && (!$isAdmin || !$companySpecific)) $companySelected = "disabled";	
		# -------------------- Billing Company End Here ----------------		
	}
	
	# For local Qty Report
	if ($localQtyReportChk) {
		if ($p["weighNumber"]!="") $weighNumber	= trim($p["weighNumber"]);
		else $weighNumber	= trim($p["selWeighment"]);
		//echo $selViewType;
		$dailyCatchReportResultSetObj = $dailycatchreportObj->filterDailyCatchEntryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $weighNumber, $challanMainId,$selViewType);	
		$dailyCatchLocalQtyReportRecords = $dailyCatchReportResultSetObj->getNumRows();
		$selSuppChallanNo = "";
	}	

	#confirm the dailycatch
	if ($p["cmdConfirm"]!="" && $p["zeroEntryExist"]=="") {	
		$challanMainId	= $p["challanMainId"];
		if ($challanMainId!="" || $challanMainId!=0) {
			$confirmDailyCatchEntry = $dailycatchreportObj->updateDailyCatchMainConfirmRecords($challanMainId);
		}
	}
	
	$ON_LOAD_SAJAX = "Y"; # Loading Ajax

	# Display heading
	if ($editMode)	$heading	= $label_editDailyCatchReports;
	else 		$heading	= $label_addDailyCatchReports;	

	$ON_LOAD_PRINT_JS	= "libjs/dailycatchreport.js";
	require_once("lib/dailycatchreport_ajax.php");
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmDailyCatchReport" action="DailyCatchReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="40" align="center" class="err1" ><?php if ($err!="" ) {?><?=$err;?><?}?></td>
		</tr>
		<?php
		if ( $editMode || $addMode ) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="291" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								    <td width="290" background="images/heading_bg.gif" class="pageName" align="right" >
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td background="images/heading_bg.gif">&nbsp;</td>
												<td background="images/heading_bg.gif" class="listing-item" align="right"><? echo $today=date("j F Y");?>&nbsp;&nbsp;</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="5" ></td>
											</tr>
											<tr>
												<?php if ($editMode) {?>
												<?php } else { ?>
												<td colspan="4" align="center" nowrap="nowrap">
												<? if($print==true){?>	<? }?>
												&nbsp;&nbsp;<? if($print==true){?>
												<? }?>
												</td>
												<? } ?>
											</tr>
											<input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
											<tr>
												<td colspan="3" nowrap height="5"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap valign="top" >
												<fieldset>
												<legend class="listing-item">Weighment Challan Wise Search</legend>
													<table cellpadding="2" cellspacing="0">
														<tr>
															<td class="fieldName" nowrap="nowrap">*Weighment Challan:</td>
															<td nowrap="nowrap">
															  <? $weighNumber = $p["weighNumber"];?>
															  <input name="weighNumber" type="text" id="weighNumber" size="8" value="<?=$weighNumber?>"  onchange="xajax_getBillingCompanyRecs(document.getElementById('weighNumber').value, '<?=$selBillingCompany?>');" autocomplete="off"/>&nbsp;&nbsp;
															</td>
														</tr>
														<tr>
															<TD class="fieldName">*Billing Company:</TD>
															<td>
																<select name="selBillingCompany" id="selBillingCompany" onchange="xajax_getBillingCompanyRecs(document.getElementById('weighNumber').value, document.getElementById('selBillingCompany').value);">
																<option value="">--Select--</option>
																</select>
															</td>
														</tr>
														<tr>
															<TD class="fieldName">*View Type:</TD>
															<td>
																<select name="selViewType" id="selViewType" >
																	<option value="">--Select--</option>
																	<option value="summary" <?=$selsummary?>>Summary</option>
																	<option value="lot" <?=$sellot?>>Lot Based</option>
																</select>
															</td>
														</tr>
														<tr>
															<td class="fieldName" nowrap="nowrap">&nbsp;</td>
															<td nowrap="nowrap"><input type="submit" name="cmdSearch" value=" Search" class="button" onclick="return validateSearch(document.frmDailyCatchReport);" /></td>
														</tr>
														<tr>
															<TD colspan="2">
																<table>
																	<tr>
																		<td><input name="localQtyReportChk" type="checkbox" id="localQtyReportChk" value="LQRC" <?=$localQtyReportChk?> class="chkBox" onClick="this.form.submit();"></td>
																		<td colspan="3" class="listing-item">Local Quantity Report</td>
																	</tr>
																</table>
															</TD>
														</tr>
													</table>
												</fieldset>
											</td>
											<td>&nbsp;</td>
											<td valign="top" class="listing-item">
											<fieldset>
											<legend class="listing-item">Supplier wise Search</legend>
												<table cellpadding="2">
													<tr>
														<td colspan="2" class="fieldName">
															<table width="200">
																<tr>
																	<td class="fieldName" nowrap="nowrap">Date From: </td>
																	<td>
																	<? 
																		if($searchMode ==	true) $dateFrom	="";
																		else $dateFrom = $p["supplyFrom"];
																	?>
																		  <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmDailyCatchReport);"></td>
																	 <td class="fieldName">To:</td>
																	 <td>
																	 <? 
																	  if ($searchMode ==true) $dateTill	=	"";
																	  else $dateTill = $p["supplyTill"];
																	  ?>
																		  <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onchange="submitForm('supplyFrom','supplyTill',document.frmDailyCatchReport);"/></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td class="fieldName" nowrap="nowrap">Supplier:</td>
														<td><!--<select name="selSupplier" onchange="this.form.submit();">-->
															<!--<select name="selSupplier" onchange="getSupplier(this,this.value)">-->
															<select name="selSupplier" onchange="functionLoad(this)">
															  <!--<select name="selSupplier" onchange="xajax_getBillCompany(document.getElementById('selSupplier').value)">-->
																<option value="">--select All--</option>
																<?
																foreach ($supplierRecords as $fr) {
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
															if (sizeof($supplierRecords)>0) {
														?>
														<tr>
															<TD class="fieldName" nowrap="true">Billing Company:</TD>
															<td>
															<!--<select name="billingCompany" onchange="this.form.submit();">-->
																<select name="billingCompany" onchange="functionLoad(this)">
																<option value="">--Select--</option>
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
															}
														?>
														<tr> 
															<td class="fieldName" nowrap="nowrap">*Wt Challan No: </td>
															<td>
																  <!--<select name="selWeighment" onchange="this.form.submit();">-->

																<select name="selWeighment" onchange="functionLoad(this)">
																	<option value="0">-- Select -- </option>
																	<?php
																	foreach ($weighmentRecords as $wr) {
																	$challanId	= $wr[0];
																	$weighmentNo	= $wr[1];
																	$cAlphaCode	= $wr[2];
																	$displayCNum	= $cAlphaCode."&nbsp;".$weighmentNo;
																	$selected	= "";
																	if ($selWeighmentNo==$challanId) 
																	{
																		$selected = "selected";
																	}
																	?>
																		<option value="<?=$challanId?>" <?=$selected?>><?=$displayCNum?></option>
																	<? }?>
																</select>
															</td>
														</tr>
														<?php 
														if (sizeof($supplierChallanRecords)>0 && $paymentType=='D') {
														
														?>
														<tr>
															<td class="fieldName" nowrap>Sup. Challan No</td>
															<td class="listing-item">
															<?php
															if (sizeof($supplierChallanRecords)>0) 
															{
															?>
																<select name="selSuppChallanNo" id="selSuppChallanNo" <? if ($selWeighmentNo!=0) {?> onchange="this.form.submit();" <? } ?>>
																	<option value="">-- Select -- </option>
																	<?
																	foreach($supplierChallanRecords as $scn) 
																	{
																		$sChallanNo = $scn[0];
																		$selected	=	"";
																		if ($selSuppChallanNo == $sChallanNo) {
																			$selected = "selected";
																		}				
																	?>
																	<option value="<?=$sChallanNo?>" <?=$selected?>><?=$sChallanNo?></option>
																	<?
																	}
																	?>
																</select>
															<?
															} 
															?>
															</td>
														</tr>
														<?
														} // Payment type End
														?>
														<tr>
															<TD class="fieldName">*View Type:</TD>
															<td>
																<select name="viewType" id="viewType"   <? if ($selWeighmentNo!=0) {?> onchange="this.form.submit();" <? } ?>>
																	<option value="">--Select--</option>
																	<option value="summary" <?=$summary?>>Summary</option>
																	<option value="lot" <?=$lot?>>Lot Based</option>
																</select>
															</td>
														</tr>
														<?php
															if (sizeof($supplierChallanRecords)>0 && $paymentType=='D') {
														?>
														<?php if ($selWeighmentNo==0) {?>
														<tr id="searchDiv">
															<TD class="fieldName" nowrap="nowrap"></TD>
															<td align="left">					
																<input type="submit" name="cmdSearch" value=" Search" class="button" onclick="return validateSearch(document.frmDailyCatchReport);" />
															</td>
														</tr>
													<?php 
														}
													?>			
												<?php
													} // Payment Type end
												?>	
												<tr>
													<td></td>
													<td></td>
												</tr>
											</table>
									</fieldset>
									</td>
									<td valign="top">&nbsp;</td>
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
											<!--tr>
											  <!td colspan="3"  height="10" class="listing-item" >
											  <? if($confirm==true){?>
											  <? $confirmed = $dailyCatchReportRecords[0][45];?>
											    <input name="cmdConfirm" type="submit" class="button" id="cmdConfirm" value=" Confirm this Weighment Challan No" style="width:220px;" <? if($confirmed==1) echo "disabled";?>>
												<? }?>
											 </td>
										  </tr-->
								<tr><td height="5"></td></tr>
								<?php
									if (sizeof($dailyCatchReportRecords)) {
										$i	=	0;
								?>
								<tr>
									<td colspan="3">
									<?php
																	  
									if (sizeof($dailyCatchReport)>0) {		
									// Finding Supplier Record
									//$dailyCatchReport[0][51];
									
										if($dailyCatchReport[0][51]=='')
										{	
											$supplierId=$dailyCatchReport[0][8];
										}
										else
										{	
											$supplierId=$dailyCatchReport[0][51];
										}
										$supplierRec	= $supplierMasterObj->find($supplierId);
										$supplierName	= $supplierRec[2];
									
										//Finding Landing Center Record
										//echo "hii".$dailyCatchReport[0][7];
										$landCenter=$dailyCatchReport[0][7];
										if($landCenter!="0")
										{
											$centerRec		= $landingcenterObj->find($landCenter);
											$landingCenterName	= stripSlash($centerRec[1]);
										}
										else
										{	$landingCenterName="";
											$centerRec= $dailycatchreportObj->getSupplierLandCenter($supplierId);
											foreach($centerRec as $cr)
											{
												if($landingCenterName=="")
												{
													$landingCenterName.=$cr[1];
												}
												else
												{
													$landingCenterName.=",".$cr[1];
												}
											}
										}
										//echo $landingCenterName;
										// Finding Plant Record
										$plantRec		= $plantandunitObj->find($dailyCatchReport[0][1]);
										$plantName		= stripSlash($plantRec[2]);
											
										$Date1			= explode("-",$dailyCatchReport[0][3]); //2007-06-27
										$date			= date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[2], $Date1[0]));
										
										$selectTime		= explode("-",$dailyCatchReport[0][43]);
										$time			= $selectTime[0].":".$selectTime[1]."&nbsp;".$selectTime[2];
										$selrmlot	= $dailyCatchReport[0][52];
										if($selrmlot!='')
										{
											$rmlotName		= $objManageRMLOTID->getLotName($selrmlot);
										}
										
										$vechNo			= $dailyCatchReport[0][4];
										if($vechNo=="")
										{
											$vehicleRec= $dailycatchreportObj->getLotIDInReceipt($rmlotName);
											foreach($vehicleRec as $vr)
											{
												if($vechNo=="")
												{
													$vechNo.=$vr[0];
												}
												else
												{
													$vechNo.=",".$vr[0];
												}
											}
										}
											$selectedWeighmentNo	= $dailyCatchReport[0][6];
											$selAlphaCode		= $dailyCatchReport[0][49];
											$displaySelCNum = $selAlphaCode."&nbsp;".$selectedWeighmentNo;
										}
										
										?>
										<table width="100%" cellpadding="0" cellspacing="0">
											<tr>
												<td colspan="3">
													<fieldset>
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr> 
																<td valign="top">		  
																	<table cellpadding="0" cellspacing="0">
																		<tr> 
																			<td class="fieldName" nowrap>Supplier:</td>
																			<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$supplierName?></td>
																		</tr>
																	</table>
																</td>
																<td valign="top">
																	<table cellpadding="0" cellspacing="0">
																		<tr>
																			<?php if($landingCenterName!='') {
																			?>
																			<td class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">Landing Center:</td>
																			<td class="listing-item"><?=$landingCenterName?></td>
																			<? }
																			/*else if($selrmlot!='') {
																			?>
																			<td class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">RM Lot Id:</td>
																			<td class="listing-item"><?=$rmlotName?></td>
																			<? } */ ?>
																		</tr>
																	</table>
																</td>
																<td valign="top">
																	<table cellpadding="0" cellspacing="0">
																		<tr> 
																			<td class="fieldName" nowrap style="padding-right:5px;">Wt Challan No:</td>
																			<td class="listing-item" nowrap><?=$displaySelCNum?></td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr> 
																<td class="fieldName">
																	<table cellpadding="0" cellspacing="0">
																		<tr> 
																			<td class="fieldName" nowrap>Supplied At:</td>
																			<td class="listing-item" nowrap="nowrap"><?=$plantName?></td>
																		</tr>
																	</table>
																</td>
																<td class="listing-item">
																	<table cellpadding="0" cellspacing="0">
																		<tr> 
																			<td class="fieldName" style="padding-left:5px; padding-right:5px;">Date/Time:</td>
																			<td class="listing-item" nowrap><?=$date?>-<?=$time?>&nbsp;&nbsp;</td>
																		</tr>
																	</table>
																</td>
																<td class="fieldName">
																	<table cellpadding="0" cellspacing="0">
																		<tr>
																			
																			<td class="fieldName" nowrap>Vehicle No:</td>
																			<td class="listing-item" nowrap>&nbsp;<?=$vechNo?></td>
																			
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
													</fieldset>
												</td>
											</tr>
										</table>
									</td>
								</tr>

								<? if (!$localQtyReportChk && $paymentType=='E') {?>
								<tr>
									<td colspan="3"  height="10" class="listing-item" >
									<?
									$dailyCatchEntryId = "";
									foreach($dailyCatchReportRecords as $dcr){
									$i++;
									
									$catchEntryId	=	$dcr[0];
									$array			=	explode("-",$dcr[3]);
									$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
									
									$supplier		=	$dcr[8];
									$supplierRec		=	$supplierMasterObj->find($supplier);
									$supplierName	=	$supplierRec[3];
									
									$fishId			=	$dcr[11];
									
									$fishRec		=	$fishmasterObj->find($fishId);
									$fishName		=	$fishRec[1];
									
									$count			=	$dcr[13];
									$countAverage		=	$dcr[14];
									
										
									$processCodeRec		=	$processcodeObj->find($dcr[12]);
									$processCode		=	$processCodeRec[2];
									
									$netWt			=	$dcr[27];
									
									$declWt			=	$dcr[29];
									
									$declCount		=	$dcr[30];
									
									$dailyRateRec	=	$supplieraccountObj->findDailyRate($fishId);
									
									$declRate		=	$dailyRateRec[7];
									
									$paidStatus			=	$dcr[35];
									
										
									$selectWeight		=	$dcr[32];
									$selectRate			=	$dcr[33];
									$actualRate			=	$dcr[34];
									
											
									$gradeRec		=	$grademasterObj->find($dcr[37]);
									$gradeCode		=	stripSlash($gradeRec[1]);
									
									 $dailyCatchEntryId	=	$dcr[42];
									
								# -- count all Gross Records -------------------------------------------------------
									$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($dailyCatchEntryId);

									$totalWt	=	"";
									$grandTotalBasketWt = "";
									$netGrossWt	=	"";
										foreach ($countGrossRecords as $cgr){
													$countGrossWt			=	$cgr[1];
													$totalWt				=	$totalWt+$countGrossWt;
													$countGrossBasketWt		=	$cgr[2];
													$grandTotalBasketWt		=	$grandTotalBasketWt + $countGrossBasketWt;
													$netGrossWt				=	$totalWt - $grandTotalBasketWt;
										}
										//In the case of Net Weight Entry
										//echo $entryOption	=	$dcr[41];
										if($dcr[41]=='N'){
											$netGrossWt	=	$dcr[26];
										}
										
									$localQty		=	$dcr[16];
									$wastageQty		=	$dcr[17];
									$softQty		=	$dcr[18];
									$gradeCountAdj		=	$dcr[46];
									$adjustWt		=	$dcr[20];	
									$otherAdjustWt		=	$localQty + $wastageQty + $softQty ;		
									$totalAdjustWt 		=	$adjustWt + $otherAdjustWt + $gradeCountAdj;

									$effectiveWt	=	$netGrossWt - $totalAdjustWt;
									$totEffectiveWt +=	$effectiveWt; //For Total Effective Weight 
									$goodPacking	=	$dcr[21];
									$goodPackQty	=	($effectiveWt*$goodPacking)/100;
										
									$forPeeling	=	$dcr[22];
									$goodPeelQty	=	($effectiveWt*$forPeeling)/100;
										
									$remarks	=	$dcr[23];
										
									$paymentBy	=	$dcr[44];
									$receivedBy	=	$dcr[48];

									$adjustQtyReason = 	$dcr[19];
									$localQtyReason	 = 	$dcr[38];
									$wastageQtyReason = 	$dcr[39];
									$softQtyReason	  =	$dcr[40];	
									$gradeCountAdjReason = 	$dcr[47];
									$selrmlot	= $dailyCatchReport[0][52];
									if($selrmlot!='')
									{
										$rmlotName		= $objManageRMLOTID->getLotName($selrmlot);
									}
									#--------------------------------------------------------------------------
									
									?>
									<table align="center">
										<tr>
											<td>&nbsp;</td>
										</tr>
									</table>
	
									<table width="100%" border="0" cellpadding="3" cellspacing="0" style="border:1px solid;" align="center">
										<tr bgcolor="#FFFFFF">
											<td align="center">
												<table width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<td colspan="9" class="fieldName" valign="top">
															<table>
																<tr>
																<td class="fieldName">Fish:</td>
																<td class="listing-item"><?=$fishName?></td>
																</tr>
															</table>
														</td>
														<td valign="top">
															<table>
																<tr>
																	<td class="listing-item"><?=$processCode?></td>
																	<td class="fieldName">&nbsp;</td>
																</tr>
															</table>
														</td>
														<td class="fieldName" valign="top">
															<table width="50%" cellpadding="0" cellspacing="0">
															<? if($count || $receivedBy=='C' || $receivedBy=='B'){?>
																<tr>
																	<td class="fieldName">Count:</td>
																	<td class="listing-item"><?=$count?></td>
																	<td class="fieldName">&nbsp;Average:</td>
																	<td class="listing-item"><?=$countAverage?></td>
																</tr>
															<? } 
															if($count=="" || $receivedBy=='G' || $receivedBy=='B') 
															{
															?>
																<tr>
																	<td class="fieldName">Grade:</td>
																	<td colspan="3" class="listing-item"><?=$gradeCode?></td>
																</tr>
															<? }?>
														</table>		  
													</td>
													<? if(($lotBasedSearch!="" && $rmlotName!="") || ($lotSupplierBasedSearch!="" && $rmlotName!="")) { ?>
													<td class="fieldName">
														<table width="200" align="right" cellpadding="0" cellspacing="0">
															<tr>
																<td class="fieldName" nowrap="nowrap" align="right">RM Lot Id:</td>
																<td class="listing-item" nowrap="true" style="padding-left:2px;padding-right:2px;"><strong><?=$rmlotName;?></strong></td>
															</tr>
														</table>	
													</td>
													<? } ?>
													<td class="fieldName">
														<table width="200" align="right" cellpadding="0" cellspacing="0">
															<tr>
																<td class="fieldName" nowrap="nowrap" align="right">Wt Challan No:</td>
																<td class="listing-item" nowrap="true" style="padding-left:2px;padding-right:2px;"><strong><?=$dcr[49]."&nbsp;".$dcr[6];?></strong></td>
															</tr>
														</table>	
													</td>
												</tr>
											</table>	
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td align="center">
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
												<? 
												if(sizeof($countGrossRecords) && $dcr[41]=='B'){
												
												$col=4;
												for($i=1;$i<=$col;$i++)
													{
												?>
			
													<td width="9%">
														<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999">
															<tr bgcolor="#f2f2f2" class="listing-head">
																<td width="30%" align="center">No</td>
																<td align="center" width="40%">Gross</td>
																<td align="center" width="30%">Net</td>
															</tr>
															<? 
															$row=sizeof($countGrossRecords);
														
															$size 	=	ceil($row/$col);
															for($j=1;$j<=$size;$j++)
																{
																$id	=(($i-1)*$size)+$j;
																
												
																
																$hidId="";
																$gwt="";
																$bwt="";
																$netCountWt="";
																	
														
															if ( $id <= sizeof($countGrossRecords) )	{
																$rec = $countGrossRecords[$id-1];
																$gwt=$rec[1];
																$bwt=$rec[2];
																$netCountWt	=	$gwt-$bwt;
																
															}	
						
						
															?>
															<tr bgcolor="#FFFFFF">
																<td nowrap class="listing-item" align="center"><? if($gwt=="") { echo 0;} else { echo $id;}?>&nbsp;&nbsp;</td>
																<td class="listing-item" align="right"><? echo number_format($gwt,2);?>&nbsp;&nbsp;&nbsp;</td>
																<td class="listing-item" align="right"><? echo number_format($netCountWt,2);?>&nbsp;&nbsp;&nbsp;</td>
															</tr>
															<? }?>
														</table>			
													</td>
													<? } }?>	
												</tr>
											</table>	
										</td>
									</tr>
									<tr bgcolor=white>
										<td align="center" class="listing-head">
											<table width="100%" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td valign="top">
														<table width="100%" cellpadding="0" cellspacing="0">
														<? if(sizeof($countGrossRecords) && $dcr[41]=='B'){?>
															<tr>
																<td class="fieldName">Total Wt: </td>
																<td class="listing-item" align="right"><? echo number_format($totalWt,2);?></td>
															</tr>
															<tr>
																<td class="fieldName">BKt Wt: </td>
																<td class="listing-item" align="right"><? echo number_format($grandTotalBasketWt,2);?></td>
															</tr>
															<? }?>
															<tr>
																<td class="fieldName">Net Wt: </td>
																<td class="listing-item" align="right"><? echo number_format($netGrossWt,2);?></td>
															</tr>
															<tr>
																<td class="fieldName">Adjustment:</td>
																<td class="listing-item" align="right"><? echo number_format($adjustWt,2);?></td>
																<? if($adjustWt!=0) {?>
																<td>&nbsp;</td>
																<td class="fieldName">Reason:</td>
																<td class="listing-item"><?=$adjustQtyReason?></td>
																<? }?>
															</tr>
															<tr>
        														<TD class="fieldName">Grade/Count Adj</TD>
																<TD class="listing-item" align="right"><? echo number_format($gradeCountAdj,2);?></TD>
																<? if($gradeCountAdj!=0) {?>
																<td>&nbsp;</td>
	    														<td class="fieldName">Reason:</td>
	    														<td class="listing-item"><?=$gradeCountAdjReason?></td>
																<? }?>
															</tr>
															<tr>
																<TD class="fieldName">Local Qty:</TD>
																<TD class="listing-item" align="right"><?=$localQty?></TD>
																<? if($localQty!=0) {?>
																<td>&nbsp;</td>
																<td class="fieldName">Reason:</td>
																<td class="listing-item"><?=$localQtyReason?></td>
																<? }?>
															</tr>
															<tr>
																<TD class="fieldName">Wastage Qty:</TD>
																<TD class="listing-item" align="right"><?=$wastageQty?></TD>
																<? if($wastageQty!=0) {?>
																<td>&nbsp;</td>
																<td class="fieldName">Reason:</td>
																<td class="listing-item"><?=$wastageQtyReason?></td>
																<? }?>
															</tr>
															<tr>
           														<TD class="fieldName">Soft Qty:</TD>
																<TD class="listing-item" align="right"><?=$softQty?></TD>
																<? if($softQty!=0) { ?>
																<td>&nbsp;</td>
																<td class="fieldName">Reason:</td>
																<td class="listing-item"><?=$softQtyReason?></td>
																<? }?>
															</tr>
														</table>
													</td>
													<td valign="top">
														<table width="100" cellpadding="0" cellspacing="0">
															<tr>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
														</table>
													</td>
													<td valign="top">
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td colspan="4" nowrap="nowrap">
																	<table width="100%" cellpadding="0" cellspacing="0">
																		<tr>
																			<td class="fieldName" nowrap="nowrap">Good For PKg:</td>
																			<td class="listing-item" align="right"><?=$goodPacking?>%</td>
																			<td class="fieldName" style="padding-left:20px;">&nbsp;Qty:</td>
																			<td class="listing-item" align="right"><? echo number_format($goodPackQty,3);?></td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr>
																<td colspan="4" nowrap="nowrap">
																	<table width="100%" cellpadding="0" cellspacing="0">
																		<tr>
																			<td class="fieldName" nowrap="nowrap">For Peeling :</td>
																			<td class="listing-item" align="right"><?=$forPeeling?>%</td>
																			<td class="fieldName" style="padding-left:20px;">&nbsp;&nbsp;Qty:</td>
																			<td class="listing-item" align="right"><? echo number_format($goodPeelQty,3);?></td>
																		</tr>
																	</table>              
																</td>
															</tr>
															<tr>
																<td colspan="4" class="fieldName" align="left">
																	<table width="100%" cellpadding="0" cellspacing="0">
																		<tr>
																			<td class="fieldName">Decl Wt: </td>
																			<td class="listing-item"><?=$declWt;?></td>
																			<td class="fieldName" style="padding-left:20px;">Decl Ct: </td>
																			<td class="listing-item"><?=$declCount?></td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr>
																<td class="fieldName">Remarks:</td>
																<td colspan="3" class="listing-item" align="left"><?=$remarks?></td>
															</tr>
														</table>
													</td>
													<td>&nbsp;</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<TD align="center">
											<table cellpadding="0" cellspacing="0">
												<TR>
													<td class="fieldName" nowrap><strong>Effective Wt:</strong> </td>
													<td class="listing-item" nowrap>&nbsp;&nbsp;<strong><? echo number_format($effectiveWt,3);?></strong></td>
												</TR>
											</table>
										</TD>
									</tr>
									<? }?>
									<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
								</table>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3">
								<table width="200" align="center">
								 <? 
								 if(sizeof($dailyCatchReportRecords)>0){?>
									<tr>
										<td class="fieldName" nowrap="nowrap"><strong>Total Effective Weight:</strong></td>
										<td class="listing-item" nowrap="nowrap"><strong>&nbsp;<? echo number_format($totEffectiveWt,3);?>&nbsp;Kg</strong></td>
									</tr>
								<? }?>
								</table>
							</td>
						</tr>
						 <?
							 } else if ( ($dailyCatchLocalQtyReportRecords=="" || (!sizeof($dailyCatchReportRecords))) && $paymentType=='E'){
						?>
 						<tr>
							<td colspan="3" align="center" class="err1"><?=$msgNoRecords;?></td>
						</tr>
					  <? 
					  }
					}
					?>

					<!-- Local Qty Report Start Here -->
					<? if ($dailyCatchLocalQtyReportRecords!="" && ($weighNumber!="" || $selWeighmentNo!="")) {?>
					<tr>
						<TD height="5"></TD>
					</tr>
					<tr>
						<TD colspan="3">
							<table width="99%" cellpadding="2" cellspacing="1" bgcolor="#999999" align="center">
								<tr bgcolor="#f2f2f2" align="center">
									<? if($lotBasedSearch!="") { ?>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">RM Lot ID</th>
									<? } ?>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">Wt Challan No</th>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">FISH</th>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">PROCESS</th>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">COUNT</th>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">GRADE</th>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">REMARKS</th>
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">QUANTITY</th>	
									<!--<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">RATE</th>-->
									<!-- <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">AMOUNT</th>	-->
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Qty</th>
									<!--<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Rate</th> --> 
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Qty</th> 
									<!--<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Rate</th> -->
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Qty</th> 
									<!--<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Rate</th> -->
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Qty</th> 
									<!--<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Rate</th> -->
									<!--<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Total Rate</th>	-->
									<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Total Qty</th>	
								</tr>
								<?
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

								$grandTotalArrivalQty = "";
								$i=0;
								while ($dcr=$dailyCatchReportResultSetObj->getRow()) {
									$i++;	
									$catchEntryId		=	$dcr[0];			
									$enteredDate		= dateFormat($dcr[3]);
									$challanNo		=	$dcr[6];
									$WtChallanNumber 	=	"";
									$displayChallanNum = "";
									if ($prevChallanNo != $challanNo) {
										$WtChallanNumber = $dcr[6];
										$alphaCode	 = $dcr[52];
										$displayChallanNum = $alphaCode.$WtChallanNumber;
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

								$totalArrivalQty = $effectiveWt + $adjustWt + $localQty + $wastageQty + $softQty;
								$grandTotalArrivalQty += $totalArrivalQty;
								$selrmlot=$dcr[53];
								if($selrmlot!='')
								{
									$rmlotName		= $objManageRMLOTID->getLotName($selrmlot);
								}
								?>
								<tr bgcolor="#FFFFFF">
									<? if($lotBasedSearch!="" && $rmlotName!="") { ?>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$rmlotName?></td>
									<? } ?>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$displayChallanNum?></td>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$fishName?></td>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$processCode?></td>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" nowrap="nowrap"><?=$count?></td>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$gradeCode?></td>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$remarks?></td>	
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><? echo number_format($effectiveWt,2);?></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$selectRate?></td>-->
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$actualRate?></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$adjustWt?></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><? echo number_format($adjustWtRate,2,'.','');?></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$localQty?></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalLocalQtyRate?></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$wastageQty?></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalWastageQtyRate?></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$softQty?></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalSoftQtyRate?></td>-->
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalLocalRate,2,'.','');?></strong></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalArrivalQty,2,'.','');?></strong></td>
								</tr>
								<?
								$prevChallanNo = $challanNo;
								$prevFishId	= $selFishId;	  
								}	
								?>
								
								<tr bgcolor="#FFFFFF">
									<? if($lotBasedSearch!="" && $rmlotName!="") { ?>
									<td height='20' class="listing-item">&nbsp;</td>
									<? } ?>
									<td height='20' class="listing-item">&nbsp;</td>
									<td height='20' class="listing-item">&nbsp;</td>
									<td height='20' class="listing-item">&nbsp;</td>
									<td height='20' class="listing-item">&nbsp;</td>
									<td height='20' class="listing-head" align="right" nowrap style="padding-left:2px; padding-right:2px; font-size:7pt;">Total:</td>	
									<td height='20' class="listing-item">&nbsp;</td>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalEffectiveWt,2);?></strong></td>
									<!-- <td height='20' class="listing-item">&nbsp;</td>
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? //echo number_format($grandTotalActualAmount,2);?></strong></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalAdjustWt,2);?></strong></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalAdjstWtRate,2);?></strong></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalLocalQty,2);?></strong></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalLocalQtyRate,2);?></strong></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalWastageQty,2);?></strong></td>
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalWastageQtyRate,2);?></strong></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalSoftQty,2);?></strong></td>
									<!--<td height='20' nowrap="nowrap" class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalSoftQtyRate,2);?></strong></td> -->
									<!--<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><strong><? echo number_format($grandTotalLocalRate,2);?></strong></td>-->
									<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalArrivalQty,2);?></strong></td>	
								</tr>
							</table>
						</TD>
					</tr>
					<? } ?>
					<!-- Local Qty Report End Here	 -->
					<!-- Supplier Challan Details  Starts Here-->
					<?
					//echo $selSuppChallanNo."---".$paymentType;
					if ($selSuppChallanNo!="" && $paymentType=='D') {
					?>
					<tr><TD height="5"></TD></tr>	
					<? if ($moreSubSupplierExist) {?>
					<tr>
						<td height="5" colspan="3" class="listing-item" style="color:#FF0000;" align="center"><strong>The selected Weighment Challan contains more than one sub-supplier</strong></TD>
					</tr>
					<tr><TD height="5"></TD></tr>		
					<? }?>
					<? if(sizeof($declaredWtRecords)>0) { ?>
					<tr>
						<TD colspan="3" style="padding-left:2px; padding-right:2px;">
							<table width="55%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">  
                        		<tr bgcolor="#f2f2f2" align="center"> 
									<? if($lotSupplierBasedSearch!="") { ?>
									<td class="listing-head" style="padding-left:5px; padding-right:5px; " nowrap>RM Lot ID</td>
									<? } ?>
									<td class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.Qty</td>
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
									$sFishId	=	$sdr[1];
									$fishName = "";
									if($prevFishId!=$sFishId){
										$fishName		=	$sdr[11];
									}
									
									$processCodeId	=	$sdr[2];	
									$processCode	= "";
									if($prevProcessCodeId!=$processCodeId){
										$processCode	=	$sdr[12];
									}
												
									$declCount	=	$sdr[10];
									$declWt		=	$sdr[13];
									$totalWt	+=	$declWt;
									$rmlot	=	$sdr[15];
									if($rmlot!='')
									{
										$rmlotNm		= $objManageRMLOTID->getLotName($rmlot);
									}
								
								?>
								<tr bgcolor="#FFFFFF"> 
									<? if($lotSupplierBasedSearch!="" && $rmlotNm!="") { ?>
									<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$rmlotNm?></td>
									<? } ?>
									<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName?></td>
									<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
									<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$declCount?></td>
									<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$declWt?></td>
								</tr>
								<?php
								  $prevFishId = $sFishId;
								  $prevProcessCodeId = $processCodeId;
								 } 
								?>
								<tr bgcolor="#FFFFFF">
									<? if($lotSupplierBasedSearch!="" && $rmlotNm!="") { ?>
									<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</td>
									<? } ?>
									<td colspan="3" nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?></strong></td>
								</tr>								  
							</table>
						</TD>
					</tr>
					<? } ?>
					<?php
						} else if ($paymentType=='D') {
					?>
					<tr><TD height="10"></TD></tr>
					<tr>
						<td colspan="4" align="center" height="10" class="err1">No Declared wt records.</td>
					</tr>
					<?
						}
					?>
					<!-- Supplier Challan Details Ends Here-->
					<tr>
						<td colspan="2" align="center" height="10">&nbsp;</td>
						<td align="center" colspan="2">&nbsp;</td>
					</tr>
  					<tr>
					<? if($editMode){?>
					<?} else{?>
						<td colspan="4" align="center" nowrap="nowrap">
						<? if($confirm==true && ($manageconfirmObj->isRMConfirmEnabled())){?>
   	  					<? $confirmed = $dailyCatchReportRecords[0][45];?>
						<input name="cmdConfirm" type="submit" class="button" id="cmdConfirm" value=" Confirm this Weighment Challan No" style="width:220px;" <? if($confirmed==1 || ( sizeof($dailyCatchReportRecords)==0) || $localQtyReportChk) echo "disabled";?> onClick="return validateDailyCatchReportConfirm(document.frmDailyCatchReport);">
						<? }?>	
						&nbsp;&nbsp;&nbsp;&nbsp;
						<? if($print==true){?>
						<input type="button" name="cmdAddSupplierAccount" class="button" value=" Raw Material Payment Memo " onClick="return printWindow('PrintDailyCatchReportMemo.php?selWeighment=<?=$selWeighmentNo?>&weighNumber=<?=$weighNumber?>&fromDate=<?=$fromDate;?>&tillDate=<?=$tillDate;?>&selectSupplier=<?=$selectSupplier?>&paymentType=<?=$paymentType?>&selSuppChallanNo=<?=$selSuppChallanNo?>&billingCompany=<?=$billingCompany?>&challanMainId=<?=$challanMainId?>',700,600);" style="width:200px" <? if( sizeof($dailyCatchReportRecords)==0 || $localQtyReportChk || ($paymentType=='D' && $selSuppChallanNo=="")) echo $disabled="disabled";?>><? }?>
						&nbsp;&nbsp;
						<? if($print==true){?>	
						<input  type="button" name="Submit" value=" Raw Material Challan " class="button" onClick="return printWindow('PrintDailyCatchReport.php?selWeighment=<?=$selWeighmentNo?>&weighNumber=<?=$weighNumber?>&fromDate=<?=$fromDate;?>&tillDate=<?=$tillDate;?>&selectSupplier=<?=$selectSupplier?>&billingCompany=<?=$billingCompany?>&challanMainId=<?=$challanMainId?>',700,600);" style="width:160px" <? if( sizeof($dailyCatchReportRecords)==0 || $localQtyReportChk) echo $disabled="disabled";?>>
						<? }?>
						</td>
						<input type="hidden" name="hidDailyCatchEntryId" value="<?=$dailyCatchEntryId?>" />
						<input type="hidden" name="cmdAddNew" value="1">
					<?}?>
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
</table>
	<!-- Form fields end   -->
</td>
</tr>
<input type="hidden" name="zeroEntryExist" id="zeroEntryExist" value="<?=$zeroEntryExist?>">	
<input type="hidden" name="challanMainId" id="challanMainId" value="<?=$challanMainId?>">	
	<tr>
		<td height="10" ></td>
	</tr>
	<?
	}
	?>
	<input type="hidden" name="hidWCNumber" value="<?=$wNumber?>">
</table>
	<? 
		if ($selWeighmentNo==0 && $paymentType=='D') {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		//displaySearchBtn();
	</SCRIPT>
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
	<?php
		if ($challanMainId!="" || $challanMainId!=0) {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_chkReportConfirm('<?=$challanMainId?>');
	</script>
	<?php
		}
	?>

	<?php
		if ($challanWiseSearch) {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_getBillingCompanyRecs('<?=$weighNumber?>', '<?=$selBillingCompany?>');
	</script>
	<?php
		}
	?>
</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
