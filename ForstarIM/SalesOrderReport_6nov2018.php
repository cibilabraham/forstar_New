<?php
	require("include/include.php");
	require_once("lib/SalesOrderReport_ajax.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= true;
	$searchMode 	= false;
	$statusUpdated	= false;
	$getResult	= false;
	$recEditable 	= false;

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	// Cheking access control
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
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
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	if ($isAdmin==true || $reEdit==true) $recEditable = true;
	// Cheking access control end -------------------------

	if ($g["redirect"]!="") {
		$getResult = true;
		$dateSelection=$g["dateSelection"]; // INV - Invoice Date
	}

	// get selected date 
	if ($g["dateFrom"]!="" && $g["dateTill"]!="") {
		$dateFrom = $g["dateFrom"];
		$dateTill = $g["dateTill"];
	} else if ($p["dateFrom"]!="" && $p["dateTill"]!="") {
		$dateFrom = $p["dateFrom"];
		$dateTill = $p["dateTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	if ($p["invoiceType"]!="") $invoiceType = $p["invoiceType"];

	if ($g["reportType"]!="") $reportType = $g["reportType"];
	else $reportType = $p["reportType"];

	if ($g["selStatus"]!="") $selStatus = $g["selStatus"];
	else $selStatus	= $p["selStatus"];

	if ($p["selTransporter"]!="")	$selTransporter 	= $p["selTransporter"];
	if ($p["selDistributor"]!="") 	$selDistributorId 	= $p["selDistributor"];

	if ($p["selState"]!="")		$selState		= $p["selState"];
	if ($p["selCity"]!="")		$selCity		= $p["selCity"];	
	if (sizeof($selCity)>0) {		
		$selCityArr = implode(",",$selCity);
	}

	if ($p["periodType"]!="")	$periodType	= $p["periodType"]; // Yearly/ Month wise

	// IND-invoice date, DSD-Despatch Date, DED-Delivery date
	if ($g["dateSelFrom"]!="") $dateSelFrom	= $g["dateSelFrom"];
	else $dateSelFrom = $p["dateSelFrom"];

	# Update Status
	if ($p["cmdUpdate"]!="") {
		$soNum		= trim($p["soNum"]);
		$changeStatus 	= $p["changeStatus"];
		$invType	= $p["invType"];
		$soYear		= $p["soYear"];

		if ($soNum!="" && $changeStatus!="" && $soYear!="") {
			$updateSalesOrderRec = $salesOrderReportObj->updateSalesOrderStatus($soNum, $invType, $soYear);
			if ($updateSalesOrderRec) $succMsg = "Invoice Status changed successfully. ";
			# Sales Order Id
			$soId =  $salesOrderReportObj->getSOId($soNum, $soYear, $invType);
			# Update packing Details/ Gate Pass
			if ($soId) $updateSOOtherRecs = $salesOrderReportObj->updateSOInvoiceOtherRec($soId);

			if ($soId && $invType=='T') {
				# Update Changes
				$updateChanges = $changesUpdateMasterObj->updateSORec($soId);

				# Distributor Account Updation (Delete Dist Account Values)
				//list($selDistributor, $billAmount, $selCoD) = $salesOrderObj->getDistributorAccountRec($soId);
				list($selDistributor, $billAmount, $selCoD, $distributorACId, $distACConfirmed) = $salesOrderObj->getDistAC($soId);
				if ($selDistributor!="" && $billAmount!="") {	
					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
				
					# delete dist A/c
					$delDistributorAc = $salesOrderObj->delDistributorAccount($soId);
					
					# Delete Reference Invoice
					$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($distributorACId);
				}
			}
		}
		$statusUpdated = true;
	}
	
	# Bulk status update
	if ($p["cmdChangeStatus"]!="") {
		$invRowCount	= $p["invRowCount"];
		for ($i=1; $i<=$invRowCount; $i++) {
			$selSONum	= trim($p["soNo_".$i]);
			$selInvStatus 	= $p["invStatus_".$i];
			$selInvType	= $p["invType_".$i];
			$selSOYear	= $p["invYear_".$i];
			$selInvoiceId 	= trim($p["invoiceId_".$i]);		
			
			if ($selSONum!="" && $selInvStatus!="P" && $selSOYear!="" && $selInvoiceId!="") {
				//echo "<br>h====>$selSONum,$selInvStatus,$selInvType,$selSOYear,$selInvoiceId";
				# Update Main rec
				$updateSalesOrderRec = $salesOrderReportObj->updateSalesOrderStatus($selSONum, $selInvType, $selSOYear);		
				# Update packing Details/ Gate Pass
				if ($selInvoiceId) $updateSOOtherRecs = $salesOrderReportObj->updateSOInvoiceOtherRec($selInvoiceId);			
				if ($selInvoiceId && $selInvType=='T') {
					# Update Changes
					$updateChanges = $changesUpdateMasterObj->updateSORec($selInvoiceId);
					# Distributor Account Updation (Delete Dist Account Values)
					//list($selDistributor, $billAmount, $selCoD) = $salesOrderObj->getDistributorAccountRec($selInvoiceId);
			
					list($selDistributor, $billAmount, $selCoD, $distributorACId, $distACConfirmed) = $salesOrderObj->getDistAC($selInvoiceId);
					if ($selDistributor!="" && $billAmount!="") {	
						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);	
						# delete dist A/c
						$delDistributorAc = $salesOrderObj->delDistributorAccount($selInvoiceId);
						# Delete Reference Invoice
						$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($distributorACId);
					}
				}			
				# Update Other recs ends here
			} // Cndition Ends here
						
		}  // Loop Ends here
		if ($updateSalesOrderRec) $succMsg = "Invoice Status changed successfully. ";	
		$statusUpdated = true;
	} // Bulk Status change ends here
	
	
	# Generate Report
	if ($p["cmdSearch"]!="" || $statusUpdated || $getResult) {
		if ($fromDate!="" && $fromDate!="") {
			if (!$getResult) {
				$salesOrderRecords = $salesOrderReportObj->fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, $dateSelFrom);
				if ($reportType=="TRAN") {
					$trptrSORecords = $salesOrderReportObj->fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, $dateSelFrom);
				}
				$searchMode = true;
			} else if ($getResult) {
				# From Home Page Invoice Section
				if ($dateSelection=='INV') {
					$salesOrderRecords = $salesOrderReportObj->fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, '');
				} else {
					# From home Page Despatch Section
					$salesOrderRecords = $salesOrderReportObj->fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, 'DSD');
					$trptrSORecords = $salesOrderReportObj->fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, 'DED');
				}
			}
		}
	}

	/*
	if ($p["cmdSearch"]!="" && $periodType!="") {
		if ($fromDate!="" && $fromDate!="") {
			$periodWiseInvRecords = $salesOrderReportObj->getPeriodTypeRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, $periodType);
			$searchMode = true;
		}
	}
	*/

	# List all Transporter		
	//$transporterRecords		= $transporterMasterObj->fetchAllRecords();
	# List all Distributor
	//$distributorResultSetObj 	= $distributorMasterObj->fetchAllRecords();
	# List all State
	//$stateResultSetObj 		= $stateMasterObj->fetchAllRecords();

	# Get So Year List
	$soYearList = $salesOrderReportObj->getSOYearList();

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# include JS in template
	$ON_LOAD_PRINT_JS = "libjs/SalesOrderReport.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSalesOrderReport" action="SalesOrderReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?php
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Sales Order Report</td>
									<td background="images/heading_bg.gif"  >
									<table cellpadding="0" cellspacing="0" align="right">	
									<tr>
									</tr>
									</table></td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2" ><table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td height="10" ></td>
                                    </tr>
	<tr>
		<TD colspan="3" align="center">
			<table>
				<TR>
					<TD valign="top" style="padding-left:10px;padding-right:10px;padding-bottom:10px;paing-top:10px;">
						<fieldset>
						<table>
							<TR>
								<td class="fieldName" nowrap align="left">*From&nbsp; </td>
								<td>
							<? 
								if ($dateFrom=="") $dateFrom=date("d/m/Y");
							?>
								<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,'');">&nbsp;&nbsp;
								</td>
							</TR>
							<tr>
								<td class="fieldName"  nowrap align="left">*To&nbsp;</td>
								<td>
								<? 
									if ($dateTill=="") $dateTill=date("d/m/Y");
								?>
								<input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,'');">
								</td>
							</tr>
							<!--<tr>
								<td class="fieldName"  nowrap align="left">Report Type&nbsp;</td>
								<td>
								<select name="periodType" id="periodType">
								  <option value="">--Detailed--</option>
								  <option value="M" <? if ($periodType=='M') echo "selected";?>>Monthly</option>
								  <option value="Y" <? if ($periodType=='Y') echo "selected";?>>Yearly</option>
								</select>
								</td>
							</tr>-->
							<tr>
								<td class="fieldName"  nowrap align="left">Invoice Type&nbsp;</td>
								<td>
									<select name="invoiceType" id="invoiceType" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,'');">
										<option value="">--Select All--</option>
										<option value="T" <? if ($invoiceType=='T') echo "selected";?>>Taxable</option>
										<option value="S" <? if ($invoiceType=='S') echo "selected";?>>Sample</option>
									</select>
								</td>
							</tr>	
						<tr>
								<td class="fieldName"  nowrap align="left">Status&nbsp;</td>
								<td>
									<select name="selStatus" id="selStatus" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,'');">
										<option value="">-- Select All --</option>
										<option value="C" <? if ($selStatus=='C') echo "selected";?>>Complete</option>
										<option value="P" <? if ($selStatus=='P') echo "selected";?>>Pending</option>
									</select>
								</td>
						</tr>	
						<tr>
                                                  <td class="fieldName" nowrap="true" align="left">*Type</td>
                                                  <td class="listing-item">
							<select name="reportType" id="reportType" onchange="showSearchOption();xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,'');">
								<option value="">-- Select --</option>
								<option value="DIST" <? if ($reportType=='DIST') echo "selected";?>>Distributor</option>
								<option value="TRAN" <? if ($reportType=='TRAN') echo "selected";?>>Transporter</option>
								<option value="STAT" <? if ($reportType=='STAT') echo "selected";?>>State</option>
							</select>
					</td>
                                                </tr>
				<tr>
                                       <td class="fieldName" nowrap="true" align="left">Based on</td>
                                       <td class="listing-item">
						<select name="dateSelFrom" id="dateSelFrom">
						<option value="IND" <? if ($dateSelFrom=='IND') echo "selected";?>>Invoice Date</option>
						<option value="DSD" <? if ($dateSelFrom=='DSD') echo "selected";?>>Despatch Date</option>
						<option value="DED" <? if ($dateSelFrom=='DED') echo "selected";?>>Delivery Date</option>
						</select>
					</td>
				</tr>
						</table>
					</fieldset>
					</TD>
	<td width="10">&nbsp;</td>
	<td valign="top">
		<fieldset>
			<legend class="fieldName" style="line-height:normal;">Search Options</legend>
			<table>
				<TR id="transporterRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">*Transporter</td>
						<td nowrap>
							<select name="selTransporter" id="selTransporter">
							<option value="">-- Select All --</option>
							<?php
								foreach ($transporterRecords as $tr) {
									$transporterId	 = $tr[0];
									$transporterName = stripSlash($tr[2]);	
									$selected = "";
									if ($selTransporter==$transporterId) $selected = "selected";	
							?>
							<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>
							<?php 
								}
							?>
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="distributorRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">Distributor</td>
						<td nowrap>
							<select name="selDistributor" id="selDistributor" onchange="xajax_getCityList(document.getElementById('selDistributor').value,'');">
							<option value="">-- Select --</option>
							<?php
							/*	
							while ($dr=$distributorResultSetObj->getRow()) {
								$distributorId	 = $dr[0];
								$distributorCode = stripSlash($dr[1]);
								$distributorName = stripSlash($dr[2]);	
								$selected = "";
								if ($selDistributorId==$distributorId) $selected = "selected";	
							*/
							?>
							<!--<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>-->
							<? //}?>
							</select>
						</td>
						<td nowrap class="fieldName">City</td>
						<td>
							<select name="selCity[]" id="selCity" multiple='true' size='5'>
								<option value="">-- Select All --</option>
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="stateRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">State</td>
						<td nowrap>
							<select name="selState" id="selState">
							<option value="">-- Select All--</option>		
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
	<TR>
		<TD colspan="3" align="center">
			<INPUT TYPE="submit" class="button" name="cmdSearch" value="Generate Report" onclick="return validateTransporterReport();">
		</TD>
	</TR>
	</table>
	</fieldset>
	</td>
	</TR>
	</table>
		</TD>
	</tr>
<!-- Change Status -->
	<tr>
	<TD>
		<table>
			<TR>
			<!-- 	Change Status Start Here	 -->
			<? if ($isAdmin==true || $reEdit==true) { ?>
			<td valign="top" nowrap>
			<table>
				<TR>
					<TD>
					<fieldset>
					<legend class="listing-item">Change Status</legend>
						<table>
							<TR>
							<TD>
								<table>
								<TR>
								<!--<TD class="fieldName">Change:</TD>-->
								<td>
								<table>
									<TR>
									<TD class="fieldName" nowrap="nowrap">*Invoice Type</TD>
						<td>
							<select name="invType" id="invType">
								<option value="">-- Select--</option>
								<option value="T" <? if ($invType=='T') echo "Selected";?> >Taxable</option>
								<option value="S" <? if ($invType=='S') echo "Selected";?> >Sample</option>
							</select>
						</td>
									</TR>
								</table>
								</td>
					<td>
								<table>
									<TR>
									<TD class="fieldName" nowrap="nowrap">*Year</TD>
						<td>
							<select name="soYear" id="soYear">
								<?php
								foreach ($soYearList as $soyr) {			
									$sYear	= $soyr[0];
								?>
								<option value="<?=$sYear?>"><?=$sYear?></option>
								<?php }?>
							</select>
						</td>
									</TR>
								</table>
								</td>
								<td>
									<table>
										<TR>
										<TD class="listing-item" nowrap="true">*Invoice No:</TD>
										<td>
											<input type="text" name="soNum" id="soNum" size="4" autocomplete="off">
										</td>
											</TR>
										</table>
									</td>
									<TD>
										<table>
										<TR>
										<TD>
											<INPUT type="radio" name="changeStatus" id="changeStatus" value="SOS" <?=$changeStatus?> class="chkBox">
										</TD>
										<TD class="listing-item" style="line-height:normal;" nowrap>Confirm Release</TD>
										</TR>
										</table>
									</TD>		
									<td>
										<input type="submit" name="cmdUpdate" value=" OK " class="button" onclick="return validateSalesOrderStatusUpdate(document.frmSalesOrderReport);">
									</td>
									</TR>
									</table>

									</TD>
								</TR>
							</table>
						</fieldset>
						</TD>
						<td>	

							<fieldset>
					<legend class="listing-item">Change Invoice No</legend>
						<table>
							<!--<tr><TD class="successMsg" id="successMsg" style="line-height:normal;"></TD></tr>-->
							<!--<tr><TD class="err1" id="errMsg" style="line-height:normal;"></TD></tr>-->
							<span class="err1" id="errMsg1" style='line-height:normal; font-size:10px;'></span>
							<span class="err1" id="errMsg2" style='line-height:normal; font-size:10px;'></span>
							<span class="successMsg" id="successMsg" style='line-height:normal; font-size:10px;'></span>
							<TR>
							<TD>
								<table>
								<TR>
								<td>
									<table>
										<TR>
										<TD class="fieldName" nowrap="true" style="line-height:normal;">From:</TD>
										<td>
				<input type="text" name="existingInvoiceNo" id="existingInvoiceNo" size="4" onMouseover="ShowTip('Please enter an existing Invoice No.');" onMouseout="UnTip();" onkeyup="xajax_validInvoiceNo(document.getElementById('existingInvoiceNo').value);">
				<input type="hidden" name="hidInvoiceNumNotExist" id="hidInvoiceNumNotExist" value="Y">
										</td>
											</TR>
										</table>
									</td>
								<td class="fieldName" style="line-height:normal;">To</td>
									<TD>
										<table>
										<TR>
										<TD>
				<input type="text" name="newInvoiceNo" id="newInvoiceNo" size="4" onMouseover="ShowTip('Please enter an Invoice No. which is not yet allotted');" onMouseout="UnTip();" onkeyup="xajax_invoiceNoExist(document.getElementById('newInvoiceNo').value);">
				<input type="hidden" name="hidNewInvoiceNumExist" id="hidNewInvoiceNumExist" value="Y">
										</TD>				
										</TR>
										</table>
									</TD>		
									<td>
										<input type="button" name="cmdChangeSONo" id="cmdChangeSONo" value=" Change " class="button" onclick="return updateNewInvoiceNo(document.getElementById('existingInvoiceNo').value, document.getElementById('newInvoiceNo').value);">
									</td>
									</TR>
									</table>

									</TD>
								</TR>
							</table>
						</fieldset>
						</td>
					</TR>
				</table>
				</td>
				<? }?>
<!--  Change Status End here   -->
				</TR>
			</table>
		</TD>
	</tr>
<!-- Change Status -->
        <tr>
               <td colspan="3" nowrap>&nbsp;</td>
       </tr>
	<? if ($succMsg) {?>
		<tr>
			<td colspan="3" nowrap class="successMsg" align="center"><strong><?=$succMsg?></strong></td>
		</tr>
	<? }?>	
	<?php
	if (sizeof($salesOrderRecords)>0 || sizeof($trptrSORecords)>0) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
			<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintSalesOrderReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&invoiceType=<?=$invoiceType?>&reportType=<?=$reportType?>&selTransporter=<?=$selTransporter?>&selDistributor=<?=$selDistributorId?>&selState=<?=$selState?>&selCity=<?=$selCity?>&selCityArr=<?=$selCityArr?>&selStatus=<?=$selStatus?>',700,600);">
		<? }?>
		<?php
			if ($recEditable && sizeof($salesOrderRecords)>0) {
		?>
			&nbsp;
			<input type="submit" name="cmdChangeStatus" class="button" value=" Confirm Release " onClick="return confirmChangeStatus('invoiceId_', '<?=sizeof($salesOrderRecords)?>');">
		<?php
			}
		?>
		</td>
	</tr>
	<tr>
               <td  height="10" colspan="4" style="padding:left:10px; padding-right:10px;"></td>
	</tr>
	<?php
	
		if (($reportType!="TRAN" || $getResult) && sizeof($salesOrderRecords)>0) {
	?>
        <tr>
               <td colspan="5" style="padding-left:10px; padding-right:10px;">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
		<? if ($getResult) { ?>
		<tr bgcolor="white" align="center"> 
			<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true" colspan="14" align="left" height="20">Invoice Report</th>
		</tr>
		<? }?>
	      	<tr bgcolor="#f2f2f2" align="center"> 
			<!--<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
				<input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'invoiceId_');" class="chkBox">
			</th>-->
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
				Sr.No
				<?php
				if ($recEditable && sizeof($salesOrderRecords)>0) {
				?>
				<br>
				<input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'invoiceId_');" class="chkBox">
				<?php
				}
				?>
			</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv. Date</th>		
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Location</th>
			<th align="center" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice No</th>
                	<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv Value</th>
			<? if ($selDistributorId || $selState) {?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Transporter</th>			
			<? }?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Docket No.</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Status</th>
			<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice</td>
			<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Pkg Instr.</td>
			<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Gate Pass</td>
              </tr>
              <?php
		$totalInvoiceValue = 0;
		$invType = "";
		foreach($salesOrderRecords as $sor){
			$i++;
			$salesOrderId	= $sor[0];
			$soNo 	 = $sor[1];
			$invType = $sor[20];
			$pfNo 	 = $sor[21];
			$saNo	 = $sor[22];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";

			$distributorId		= $sor[2];
			$soDate			= dateFormat($sor[3]);
			$despatchDate		= dateFormat($sor[4]);			
	
			$invoiceValue		= $sor[17];	// Grand Total Invoice Amt $sor[6];
			$cityId			= $sor[7];
			$grossWt		= $sor[9];
			$numBox			= $sor[10];			
			$docketNum		= $sor[12];

			$cityName		= $sor[15];
			$distributorName	= $sor[13];
			$transporterId		= $sor[11];
			$transporterName 	= "";
			if ($transporterId) {
				$transporterRec		= $transporterMasterObj->find($transporterId);
				$transporterName	= stripSlash($transporterRec[2]);
			}

			$totalInvoiceValue  += $invoiceValue;
			# ----------------------- Status --------
			$completeStatus = $sor[16];
			$selLastDate	= $sor[18];
			$extended	= $sor[19];
			$currentDate	= date("Y-m-d");
			$dateDiff = dateDiff(strtotime($currentDate), strtotime($selLastDate), 'D');
			$displayColor = ""; 
			$invStatus = "";
			if ($extended=='E' && ($completeStatus=="" || $completeStatus=='P')) {
				$status	= "PENDING (Extended)";
				$displayColor = "Grey";
				$invStatus = "P";
			} else {
				if ($completeStatus=='C') {
					$status	= " COMPLETED ";					
					$displayColor = "#90EE90"; // LightGreen	
					$invStatus = "C";
				} else if ($dateDiff<0) {
					$status = "DELAYED";
					$displayColor = "#DD7500"; // LightOrange
					$invStatus = "P";
				} else {
					$status = "PENDING";			
					$displayColor = "White";
					$invStatus = "P";
				}
			}			
			# ----------------------- Status Ends--------

			$gPassConfirmStatus = $sor[24];
			$invYear = date("Y", strtotime($sor[3]));
		?>
              <tr bgcolor="#FFFFFF">
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">			
			<table>
				<TR>
					<?php
						if ($recEditable && sizeof($salesOrderRecords)>0) {
					?>
					<TD>
						<input type="checkbox" name="invoiceId_<?=$i?>" id="invoiceId_<?=$i?>" value="<?=$salesOrderId;?>" class="chkBox">	
						<input type="hidden" name="invStatus_<?=$i?>" id="invStatus_<?=$i?>" value="<?=$invStatus;?>" readonly="true">	
						<input type="hidden" name="invType_<?=$i?>" id="invType_<?=$i?>" value="<?=$invType;?>" readonly="true">
						<input type="hidden" name="invYear_<?=$i?>" id="invYear_<?=$i?>" value="<?=$invYear;?>" readonly="true">
						<input type="hidden" name="soNo_<?=$i?>" id="soNo_<?=$i?>" value="<?=($soNo!=0)?$soNo:$saNo?>" readonly="true">
					</TD>
					<?php
						}
					?>
					<TD class="listing-item"><?=$i?></TD>
				</TR>
			</table>			
		</td>		
		<td class="listing-item" height='25' style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$soDate?>			
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$cityName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$salesOrderNo?>
		</td>
                <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$invoiceValue?>
		</td>	
		<? if ($selDistributorId || $selState) {?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$transporterName?>
		</td>		
		<?
			}
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$docketNum?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true" bgcolor="<?=$displayColor?>">
			<?=$status?>			
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px;">
			<a href="javascript:printWindow('ViewSOTaxInvoice.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to View the Tax Invoice">
				View
			</a>
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px;">
			<a href="javascript:printWindow('ViewPkgInstruction.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to View the Tax Invoice">
				View
			</a>
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px;">
			<?php if ($gPassConfirmStatus=='Y') {?>
			<a href="javascript:printWindow('ViewGatePass.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to View the Tax Invoice">
				View
			</a>
			<?php } ?>
		</td>			
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="5" align="right">
			<input type="hidden" name="invRowCount" id="invRowCount" value="<?=$i?>" readonly="true" />
			Total:
		</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=number_format($totalInvoiceValue,2,'.',',')?></strong></td>
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="6" align="right"></td>		
	</tr>	
      </table>
	</td>
	</tr>	
	<?php 
		} // Type Select Ends here
	?>
	<?php
	
		# If  transporter 
		if (($reportType=="TRAN" || $getResult) && sizeof($trptrSORecords)>0) {
	?>
	<tr><TD height="10"></TD></tr>
        <tr>
               <td colspan="5" style="padding-left:10px; padding-right:10px;">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
		<? if ($getResult) { ?>
		<tr bgcolor="white" align="center"> 
			<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true" colspan="14" align="left" height="20">Transporter Report</th>
		</tr>
		<? }?>
	      	<tr bgcolor="#f2f2f2" align="center"> 
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Sr.No</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv. Date</th>	
			<th align="center" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice<br> No</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv Value</th>		
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Location</th>
			<? if (!$selTransporter) {?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Transporter</th>			
			<? }?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Docket No.</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Delivery<br> Date</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Remarks</th>
			<!--<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Status</th>-->
			<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice</td>
			<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Pkg<br> Instr.</td>
			<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Gate<br>Pass</td>
              </tr>
              <?php
		$totalInvoiceValue = 0;
		$invType = "";
		foreach($trptrSORecords as $sor){
			$i++;
			$salesOrderId	= $sor[0];
			$soNo 	 = $sor[1];
			$invType = $sor[20];
			$pfNo 	 = $sor[21];
			$saNo	 = $sor[22];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";

			$distributorId		= $sor[2];
			$soDate			= dateFormat($sor[3]);
			$despatchDate		= dateFormat($sor[4]);			
	
			$invoiceValue		= $sor[17];	// Grand Total Invoice Amt $sor[6];
			$cityId			= $sor[7];
			$grossWt		= $sor[9];
			$numBox			= $sor[10];			
			$docketNum		= $sor[12];

			$cityName		= $sor[15];
			$distributorName	= $sor[13];
			$transporterId		= $sor[11];
			$transporterName 	= "";
			if ($transporterId) {
				$transporterRec		= $transporterMasterObj->find($transporterId);
				$transporterName	= stripSlash($transporterRec[2]);
			}

			$totalInvoiceValue  += $invoiceValue;
			# ----------------------- Status --------
			$completeStatus = $sor[16];
			$selLastDate	= $sor[18];
			$extended	= $sor[19];
			$currentDate	= date("Y-m-d");
			$dateDiff = dateDiff(strtotime($currentDate), strtotime($selLastDate), 'D');
			$displayColor = ""; 
			if ($extended=='E' && ($completeStatus=="" || $completeStatus=='P')) {
				$status	= "PENDING (Extended)";
				$displayColor = "Grey";
			} else {
				if ($completeStatus=='C') {
					$status	= " COMPLETED ";					
					$displayColor = "#90EE90"; // LightGreen	
				} else if ($dateDiff<0) {
					$status = "DELAYED";
					$displayColor = "#DD7500"; // LightOrange
				} else {
					$status = "PENDING";			
					$displayColor = "White";
				}
			}			
			# ----------------------- Status Ends--------

			$gPassConfirmStatus = $sor[24];
			$deliveryDate	    = dateFormat($sor[25]);
			$deliveryRemarks    = $sor[26];	
		?>
              <tr bgcolor="#FFFFFF"> 
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$i?>
		</td>		
		<td class="listing-item" height='25' style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$soDate?>			
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$salesOrderNo?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$invoiceValue?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$cityName?>
		</td>
		<? if (!$selTransporter) {?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$transporterName?>
		</td>		
		<?
			}
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$docketNum?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$deliveryDate?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true" width="100">
			<?=$deliveryRemarks?>
		</td>
		<!--<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true" bgcolor="<?=$displayColor?>">
			<?=$status?>			
		</td>-->
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px;">
			<a href="javascript:printWindow('ViewSOTaxInvoice.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to View the Tax Invoice">
				View
			</a>
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px;">
			<a href="javascript:printWindow('ViewPkgInstruction.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to View the Tax Invoice">
				View
			</a>
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px;">
			<?php if ($gPassConfirmStatus=='Y') {?>
			<a href="javascript:printWindow('ViewGatePass.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to View the Tax Invoice">
				View
			</a>
			<?php } ?>
		</td>			
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="3" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=number_format($totalInvoiceValue,2,'.',',')?></strong></td>
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="9" align="right"></td>		
	</tr>	
      </table>
	</td>
	</tr>	
	<?php 
		} // Type Select Ends here
	?>
	<tr>
               <td  height="10" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintSalesOrderReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&invoiceType=<?=$invoiceType?>&reportType=<?=$reportType?>&selTransporter=<?=$selTransporter?>&selDistributor=<?=$selDistributorId?>&selState=<?=$selState?>&selCity=<?=$selCity?>&selCityArr=<?=$selCityArr?>&selStatus=<?=$selStatus?>',700,600);">
<? }?>
		<?php
			if ($recEditable && sizeof($salesOrderRecords)>0) {
		?>
			&nbsp;
			<input type="submit" name="cmdChangeStatus" class="button" value=" Confirm Release " onClick="return confirmChangeStatus('invoiceId_', '<?=sizeof($salesOrderRecords)?>');">
		<?php
			}
		?>
		</td>
		</tr>
	<?php 
		} else if ($dateFrom!="" && $dateTill!="" && $searchMode) {			
	?>
	<tr>
		<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<? }?>
				    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" ></td>
                                    </tr>
                                  </table></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
		<tr>
			<td height="10"></td>
		</tr>
	</table>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
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
			inputField  : "dateTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">		
		disableSerachOptions();
		showSearchOption();
		xajax_getCityList('<?=$selDistributorId?>','<?=$selCityArr?>');	
	</SCRIPT>
	<?php
		if ($dateFrom!="" && $dateTill!="") {
		//$invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus
	?>	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		xajax_getStateList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selState?>');
		xajax_distributorList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selDistributorId?>');
		xajax_transporterList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selTransporter?>');
	</SCRIPT>
	<?php
		}
	?>
	<? if ($isAdmin==true || $reEdit==true) { ?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		changeSO();
	</SCRIPT>
	<?php
		}
	?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>