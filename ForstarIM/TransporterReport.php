<?php
	require("include/include.php");
	require_once("lib/TransporterReport_ajax.php");

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;
	$searchMode 		= false;

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
	// Cheking access control end 

			

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

	if ($p["invoiceType"]!="") 	$invoiceType 	= $p["invoiceType"];
	if ($p["reportType"]!="") 	$reportType 	= $p["reportType"];
	if ($p["selTransporter"]!="")	$selTransporter 	= $p["selTransporter"];
	if ($p["selDistributor"]!="") 	$selDistributorId 	= $p["selDistributor"];
	if ($p["selState"]!="")		$selState		= $p["selState"];
	if ($p["selCity"]!="")		$selCity		= $p["selCity"];
	if ($p["billType"]!="") 	$billType 		= $p["billType"];
	// Status Type = SD-Settled, NS -not settled, PD - paid
	if ($p["statusType"]!="")	$statusType		= $p["statusType"]; 
	
	if (sizeof($selCity)>0) {		
		$selCityArr = implode(",",$selCity);
	}

		
	# Generate Report
	if ($p["cmdSearch"]!="") {
		if ($fromDate!="" && $fromDate!="") {
			$transporterInvoiceRecords = $transporterReportObj->fetchTransporterInvoiceRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $billType, $statusType);
			$searchMode = true;
		}
	}
	

	# List all Transporter		
	//$transporterRecords		= $transporterMasterObj->fetchAllRecords();
	# List all Distributor
	//$distributorResultSetObj 	= $distributorMasterObj->fetchAllRecords();
	# List all State
	//$stateResultSetObj 		= $stateMasterObj->fetchAllRecords();

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# include JS in template
	$ON_LOAD_PRINT_JS = "libjs/TransporterReport.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmTransporterReport" action="TransporterReport.php" method="post">
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Report</td>
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
								<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getStateList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_distributorList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_transporterList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value, document.getElementById('statusType').value, '');" autocomplete="off"/>&nbsp;&nbsp;
								</td>
							</TR>
							<tr>
								<td class="fieldName"  nowrap align="left">*To&nbsp;</td>
								<td>
								<? 
									if ($dateTill=="") $dateTill=date("d/m/Y");
								?>
								<input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_transporterList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value, document.getElementById('statusType').value, '');" autocomplete="off" />
								</td>
							</tr>
							<tr>
								<td class="fieldName"  nowrap align="left">Invoice Type&nbsp;</td>
								<td>
									<select name="invoiceType" id="invoiceType" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_distributorList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_transporterList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value,document.getElementById('statusType').value, '');">
										<option value="">--Select All--</option>
										<option value="T" <? if ($invoiceType=='T') echo "selected";?>>Taxable</option>
										<option value="S" <? if ($invoiceType=='S') echo "selected";?>>Sample</option>
									</select>
								</td>
							</tr>	
							<tr>
                                                  <td class="fieldName" nowrap="true" align="left">*Status</td>
                                                  <td class="listing-item">
							<select name="statusType" id="statusType" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_distributorList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_transporterList(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('invoiceType').value,document.getElementById('statusType').value, '');">
								<option value="">-- Select All --</option>
								<option value="SD" <? if ($statusType=='SD') echo "selected";?>>Settled</option>
								<option value="NS" <? if ($statusType=='NS') echo "selected";?>>Not Settled</option>
								<option value="PD" <? if ($statusType=='PD') echo "selected";?>>Paid</option>
							</select>
					</td>
                                                </tr>
							<tr>
                                                  <td class="fieldName" nowrap="true" align="left">*Type</td>
                                                  <td class="listing-item">
							<select name="reportType" id="reportType" onchange="showSearchOption(); xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value, document.getElementById('statusType').value, ''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value, document.getElementById('statusType').value, '');">
								<option value="">-- Select --</option>
								<option value="TRAN" <? if ($reportType=='TRAN') echo "selected";?>>Transporter</option>
								<option value="DIST" <? if ($reportType=='DIST') echo "selected";?>>Distributor</option>
								<option value="STAT" <? if ($reportType=='STAT') echo "selected";?>>State</option>
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
							<option value="">-- Select --</option>
							<?php
								/*
								foreach ($transporterRecords as $tr) {
									$transporterId	 = $tr[0];
									$transporterName = stripSlash($tr[2]);	
									$selected = "";
									if ($selTransporter==$transporterId) $selected = "selected";	
								*/
							?>
							<!--<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>-->
							<? 
								//}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<TD colspan="2">
							<table cellpadding="0" cellspacing="0">
		<TR>
			<TD class="fieldName" nowrap="true" style="padding-left:5px;padding-right:5px;">
				Bill Type:
			</TD>
			<td>
				<select name="billType" id="billType">
					<option value="">-- Select All --</option>
					<option value="OD" <? if ($billType=='OD') echo "selected"; ?>>Ordinary Bill</option>
					<option value="OC" <? if ($billType=='OC') echo "selected"; ?>>Octroi Bill</option>
				</select>
			</td>
		</TR>
		</table>
						</TD>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="distributorRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">*Distributor</td>
						<td nowrap>
							<select name="selDistributor" id="selDistributor" onchange="xajax_getCityList(document.getElementById('selDistributor').value,'');">
							<option value="">-- Select --</option>
							<?
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
								<option value="">-- Select --</option>
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
						<td nowrap class="fieldName">*State</td>
						<td nowrap>
							<select name="selState" id="selState">
							<option value="">-- Select --</option>
							<?
							/*	
								while ($sr=$stateResultSetObj->getRow()) {
									$stateId 	= $sr[0];
									$stateName	= stripSlash($sr[2]);	
									$selected 	= "";
									if ($selState==$stateId) $selected = "selected";	
							*/
							?>
							<!--<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>-->
							<? 
								//}
							?>
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
        <tr>
               <td colspan="3" nowrap>&nbsp;</td>
       </tr>
	<?php
	if (sizeof($transporterInvoiceRecords)>0) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintTransporterReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&invoiceType=<?=$invoiceType?>&reportType=<?=$reportType?>&selTransporter=<?=$selTransporter?>&selDistributor=<?=$selDistributorId?>&selState=<?=$selState?>&selCity=<?=$selCity?>&selCityArr=<?=$selCityArr?>&billType=<?=$billType?>&statusType=<?=$statusType?>',700,600);">
		<? }?>
		</td>
		</tr>
	<tr>
               <td  height="10" colspan="4" style="padding:left:10px; padding-right:10px;">
		</td>
	</tr>
        <tr>
               <td colspan="5" style="padding-left:10px; padding-right:10px;">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
	      	<tr bgcolor="#f2f2f2" align="center"> 
			<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Sr.No</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Bill No</th>
			<? if ($selDistributorId || $selState) {?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Transporter</th>
			<? }?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Location</th>
                	<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Date</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Docket No.</th>
			<th align="center" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice No</th>
                	<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv Value</th>
			<?php 
				if ($billType!='OC') {
			?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Weight</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Rate</th>			
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Total</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">FOV</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Docket <br/>Charges</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">ODA <br>Charges</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Sur-<br>charge</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Sub:<br/>Total</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Service<br/> Tax</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Gr.<br/> Total</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Actual<br/>Cost</th>
			<?php
				} else {
			?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Octroi %</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Octroi Value</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Serv Tax</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Grand Total</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Actual<br/>Cost</th>
			<?php
				}
			?>
              </tr>
              <?php
		$totalTransporterAmt = 0;
		$grandTotalActualCost = 0;
		//printr($transporterInvoiceRecords);
		foreach($transporterInvoiceRecords as $tir){
			$i++;
			$salesOrderId		= $tir[0];
			$soNo 	= $tir[1];
			$invType = $tir[39];			
			$pfNo 	= $tir[40];
			$saNo	= $tir[41];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";

			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.',''); // Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			$transporterRateListId  = $tir[22];
			
			$billNo = ($billType!='OC')?$tir[23]:$tir[45];
			//Round off Calculation
			/*
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			*/
			$totalWt		= $tir[25];			
			# Find the Transporter rate Per Kg
			//$ratePerKg		= $transporterAccountObj->getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $totalWt);
			$ratePerKg		= $tir[26];

			//$freightCost	= $totalWt*$ratePerKg;
			$freightCost	= $tir[27];

			# Get Other Charges
			# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %
			//list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transporterRateListId);
			//echo "$fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge";
			//$FOV	= number_format((($invoiceValue*$fovCharge)/100),2,'.','');			
			$FOV		= $tir[28];
			$docketRate	= $tir[29];
			$octroiRate	= $tir[30];
			$odaRate	= $tir[49];
			$surcharge	= $tir[50];
			//$octroiRate	= number_format((($invoiceValue*$octroiServiceCharge)/100),2,'.','');
			//$total = $freightCost+$FOV+$docketCharge+$octroiRate;
			$total		= $tir[31];
			//$serviceTaxRate = number_format((($total*$serviceTax)/100),2,'.','');
			$serviceTaxRate	= ($billType!='OC')?$tir[32]:$tir[42];
			//$grandTotal = $total+$serviceTaxRate;
			$grandTotal 	= ($billType!='OC')?$tir[33]:$tir[43];
			$totalTransporterAmt += $grandTotal;

			$octroiPercent 	= $tir[37];
			$actualCost	= ($billType!='OC')?$tir[38]:$tir[44];
			$grandTotalActualCost += $actualCost;
			if ($transporterId) {
				$transporterRec		= $transporterMasterObj->find($transporterId);
				$transporterName	= stripSlash($transporterRec[2]);
			}
			
		?>
              <tr bgcolor="#FFFFFF"> 
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$i?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$billNo?>
		</td>
		<? if ($selDistributorId || $selState) {?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$transporterName?>
		</td>
		<?
			}
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$cityName?>
		</td>
                <td class="listing-item" nowrap height='25' style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$despatchDate?>			
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$docketNum?>
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$salesOrderNo?>
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$invoiceValue?>
		</td>
		<?php 
			if ($billType!='OC') {
		?>	
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$totalWt?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$ratePerKg?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$freightCost?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$FOV?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$docketRate?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=($odaRate!=0)?$odaRate:""?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=($surcharge!=0)?$surcharge:""?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$total?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$serviceTaxRate?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$grandTotal?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$actualCost?>
		</td>			
		<?php
			} else {
		?>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$octroiPercent?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=number_format($octroiRate,2,'.','');?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=number_format($serviceTaxRate,2,'.','');?>			
		</td>		
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$grandTotal?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$actualCost?>
		</td>
		<?php
				}
		?>
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<?php
			$colspan = "";
			if ($selDistributorId || $selState) $colspan = 18;
			else if ($billType!='OC') $colspan = 17;
			else $colspan = 11;
		?>
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="<?=$colspan?>" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=number_format($totalTransporterAmt,2,'.',',')?></strong></td>	
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=number_format($grandTotalActualCost,2,'.',',')?></strong></td>	
	</tr>
      </table>
	</td>
	</tr>	
	<tr>
               <td  height="10" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintTransporterReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&invoiceType=<?=$invoiceType?>&reportType=<?=$reportType?>&selTransporter=<?=$selTransporter?>&selDistributor=<?=$selDistributorId?>&selState=<?=$selState?>&selCity=<?=$selCity?>&selCityArr=<?=$selCityArr?>&billType=<?=$billType?>&statusType=<?=$statusType?>',700,600);">
<? }?>
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
	</SCRIPT>
	<?php
		if ($dateFrom!="" && $dateTill!="" && $searchMode) {		
	?>	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		xajax_getCityList('<?=$selDistributorId?>','<?=$selCityArr?>');
		xajax_getStateList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$statusType?>', '<?=$selState?>');
		xajax_distributorList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$statusType?>', '<?=$selDistributorId?>');
		xajax_transporterList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$statusType?>', '<?=$selTransporter?>');
	</SCRIPT>
	<?php
		}
	?>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>