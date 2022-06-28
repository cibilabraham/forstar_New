<?php	
	require("include/include.php");

	# Get Sales Order Id
	$selSOId = $g["selSOId"];
	$confirmed = false;
	
	// ----------------------------------------------------------
	# Find PO Records
	$sORec	=	$salesOrderObj->findSORec($selSOId);
	$pOGenerateId	= $sORec[1];
 	$distributorId	= $sORec[2];
	$selStateId	= $sORec[9];	
	$createdDate	= dateFormat($sORec[3]);
	$selCityId	= $sORec[11];
	$taxType	= $sORec[12];
	$billingForm	= $sORec[13];
	$taxAmount	= $sORec[14];
	$soRemark	= stripSlash($sORec[15]);
	$selTaxApplied	= $sORec[16];
	if ($selTaxApplied!="") $taxApplied	= explode(",",$sORec[16]);
	$invoiceType	= $sORec[20]; // T ->Taxable: S->Sample
	$soNetWt	= $sORec[17]; 
	$soGrossWt	= $sORec[18];
	$soTNumBox	= $sORec[19];
	$transporterId  = $sORec[21];
	$transporterName = "";
	if ($transporterId!="") {
		$transporterRec		= $transporterMasterObj->find($transporterId);		
		$transporterName	= stripSlash($transporterRec[2]);
	}
	if ($invoiceType=='S') {
		$additionalItemTotalWt = $sORec[22];
		$soGrossWt += $additionalItemTotalWt;
	}
	$poNo		= $sORec[23];
	$selAreaId	= $sORec[24];

	$poDate		= ($sORec[25]!=0)?dateFormat($sORec[25]):"";
	$challanNo	= $sORec[26];
	$challanDate	= ($sORec[27]!=0)?dateFormat($sORec[27]):"";

	$poNoDisplay	= $poNo;	
	if ($sORec[25]!=0)	$poNoDisplay .= ",&nbsp;$poDate";

	$challanNoDisplay = $challanNo;
	if ($sORec[27]!=0)	$challanNoDisplay .= ",&nbsp;$challanDate";

	$discount  = $sORec[28];
	$discountRemark  = $sORec[29];
	$discountPercent = $sORec[30];
	$discountAmt	 = $sORec[31];
	$grandTotalAmt   = $sORec[32];
	$roundVal	 = $sORec[33];
	if ($invoiceType=="S") $grandTotalAmt = 100;
	else $grandTotalAmt = round($grandTotalAmt+$roundVal);
	$invoiceConfirmed	= $sORec[38];
	$proformaInvoiceNo	= ($sORec[39]!=0)?$sORec[39]:"";
	$sampleInvNo		= $sORec[41];
	$pOGenerateId	= ($invoiceType!="S")?(($pOGenerateId!=0)?$pOGenerateId:"P$proformaInvoiceNo"):$sampleInvNo;
	$gpassConfirmed = $sORec[46];
	if ($gpassConfirmed) {	
		list($gatePassNo, $partyAddress, $consignmentDetails, $vehicleNo, $gpConfirm, $gpassDate) = $manageGatePassObj->getGatePassRec($selSOId, $gatePassId);	
		$gPassPrintDisabled = true; // Asked to diable on 09-09-09
	}	

	$pkgConfirmed	= $sORec[47];
	if ($pkgConfirmed=='Y') $soGrossWt = $salesOrderObj->getActualPkgGrossWt($selSOId);

	$exDutyActive		= $sORec[48];
	$eduCessPercent		= $sORec[49];
	$secEduCessPercent	= $sORec[51];
	$totExDutyAmt		= $sORec[53];
	$totEduCessAmt		= $sORec[54];
	$totSecEduCessAmt	= $sORec[55];
	$grTotCentralExDuty	= $sORec[56];
	$transportChargeActive	= $sORec[57];
	$transportCharge	= $sORec[58];
	$billingType		= $sORec[59];
	$exportEnabled = ($billingType=="E")?"Y":"N";

	// ----------------------------------------------------------	
	$cityRec	=	$cityMasterObj->find($selCityId);
	$cityName	=	stripSlash($cityRec[2]);

	$stateRec	= $stateMasterObj->find($selStateId);				
	$stateName	= stripSlash($stateRec[2]);
	
	$areaRec	=	$areaMasterObj->find($selAreaId);
	$areaName	=	stripSlash($areaRec[2]);
	# Supplier Rec
	$distributorRec		= $salesOrderObj->getDistributorRec($distributorId, $selStateId, $selCityId, $selAreaId, $exportEnabled);	
	$distributorName	= stripSlash($distributorRec[2]);
	$address		= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[12]));	
	$telNo			= stripSlash($distributorRec[11]);
	$vatNo			= stripSlash($distributorRec[8]);
	$tinNo			= stripSlash($distributorRec[9]);	
	$cstNo			= stripSlash($distributorRec[10]);
	$deliveryAddress	= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[13]));
	$sameBillingAddress	= $distributorRec[14];	
	$pinCode		= stripSlash($distributorRec[15]);
	$distEccNo		= $distributorRec[16];

	# Get dist Wise Tax Calc
	list($taxType, $taxRate) = $salesOrderObj->distWiseTaxInvoiceCalc($distributorId, $selStateId);
	
	# Get all SO Items
	if ($selSOId) $salesOrderItemRecs = $salesOrderObj->fetchAllSalesOrderItem($selSOId);

	/* Company Rec Starts Here */
	$companyRec	=	$companydetailsObj->find($editIt);
	$cName		=	stripSlash($companyRec[1]);
	$cAddress	=	stripSlash($companyRec[2]);
	$cPlace		=	stripSlash($companyRec[3]);
	$cPinCode	=	stripSlash($companyRec[4]);
	$cCountry	=	stripSlash($companyRec[5]);
	$cTelNo		=	stripSlash($companyRec[6]);
	$cFaxNo		=	stripSlash($companyRec[7]);
	$vatTin		=	stripSlash($companyRec[8]);
	$cstTin		= 	stripSlash($companyRec[9]);
	
	$range			=	stripSlash($companyRec[10]);
	$division		=	stripSlash($companyRec[11]);
	$commissionerate	=	stripSlash($companyRec[12]);
	$centralExNo		=	stripSlash($companyRec[13]);
	$notificationDetails	=	stripSlash($companyRec[14]);
	$panNo			= 	$companyRec[15];	

	// policies
	list($certifiedAgreementTxt, $termsNConditionTxt, $eOETxt) = $salesOrderObj->getInvoicePolicies();
	
	$nameOfExCommodity = $salesOrderObj->getExcisableCommodity($selSOId);

	/* Company Rec Ends Here */

	#  Number of Copy
	if ($invoiceConfirmed=='C') {
		$numCopy	= 3;
		$confirmed = true;
	} else {
		$numCopy	= 1;
	}	

	//$numCopy = 1;
?>
<html>
<head>
<title>FIF EXCISE INVOICE #<?=$pOGenerateId?></title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	background-image: url('images/watermark.png');	
	background-repeat: no-repeat;
	background-attachment:fixed;
	background-position: 50% 50%;	
}
</style> 
<!--script src="libjs/jquery/jquery-1.5.2.min.js" type="text/javascript"></script-->
<script language="javascript" type="text/javascript">
	function printDoc()
	{	
		window.print();	
		return false;
	}
	
	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";			
	}
	
	function printThisPage(printbtn)
	{	
		//$("#printMainTbl").attr("width", "794px");
		document.getElementById("printButton").style.display="none";	
		if (!printDoc()) {
			setTimeout("displayBtn()",7000); //3500			
		}		
	}
</script>
</head>
<body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="45px;">
<form name="frmPrintSOTaxInvoice" id="frmPrintSOTaxInvoice">
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>
<?php
/*
A4 : Width X height = 793.92 X1122.24
*/
# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>
<table width='794px' cellspacing='1' cellpadding='1'  align='center' border="0" id="printMainTbl">
<tr>
	<td STYLE="border-top: 1px solid #f2f2f2; border-left: 1px solid #f2f2f2; border-right: 1px solid #f2f2f2; border-bottom: 1px solid #f2f2f2;">
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000">
		<table width='100%' cellpadding='0' cellspacing='0' align="center">
			<tr>
				<TD width="35%" style="border-right: 1px solid #000000;" valign="middle">
					<table cellpadding="0" cellspacing="0" width="100%">
						<TR>
							<TD align="center" valign="middle">
								<img src="images/ForstarLogo.png" alt=""/>
							</TD>
						</TR>
					</table>
				</TD>
				<TD width="35%" style="border-right: 1px solid #000000; padding:0px 2px;" valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<TD>
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<TR>								
										<td style="padding:0px 5px;" align="center">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<TD align="center">
													<table cellpadding="0" cellspacing="0" width="100%">
														<TR>
															<TD class="listing-head" style="line-height:normal; font-size:12pt;" nowrap="true" align="center"><?=$forstinsfoods["fifoods"];?></TD>
														</TR>
														<TR>
															<TD class="listing-head" style="font-size:9px;text-align:center;" valign="top" nowrap="true" align="center"><?=$divfrfoods["frfoods"];?></TD>
														</TR>
													</table>
												</TD>
											</tr>
											<tr>
												<TD class="print-SOTHead-item" nowrap="true" align="center"><?=$addr["ADR1"]?></TD>
											</tr>
											<tr>
												<TD class="print-SOTHead-item" align="center"><?=$addr["ADR2"]?></TD>
											</tr>
											<tr>
												<TD class="print-SOTHead-item" align="center"><?=$addr["ADR3"]?>&nbsp;<?=$addr["ADR4"]?></TD>
											</tr>
											<tr>
												<TD class="print-SOTHead-item" align="center"><?=$companyArr["Email"]?></TD>
											</tr>
											<tr>
												<TD align="center">
												<table cellpadding="0" cellspacing="0">
													<TR>
														<TD class="ex-print-listing-head" style="font-size:8px;line-height:11px;" nowrap="true" valign="middle">VAT NO:&nbsp;</TD>
														<td class="listing-item" style="font-size:9px; line-height:11px;" nowrap="true" valign="middle"><?=$vatTin?></td>	
													</TR>								
												</table>
												</TD>
											</tr>
											<tr>
												<TD align="center">
												<table cellpadding="0" cellspacing="0">	
													<TR>
														<td class="ex-print-listing-head" style="font-size:8px; line-height:11px;" nowrap="true" valign="middle">CST NO:&nbsp;</td>
														<td class="listing-item" style="font-size:9px; line-height:11px;" nowrap="true" valign="middle"><?=$cstTin?></td>
													</TR>
												</table>
												</TD>
											</tr>
											<tr><TD height="5"></TD></tr>
										</table>
										</td>
									</TR>
											</table>
							</TD>
						</tr>
					</table>	
				</TD>
		<TD width="35%" style="padding:0px 2px;" valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<TD class="pageName" valign="top" style="padding:0px 5px;" align="center" nowrap="true">
						<?php if ($invoiceType=='S') {?>	
						<span style="font-size:14px;">
							SAMPLE INVOICE
						</span>
						<? } else {?>
						<span style="font-size:14px;">
							<?php
								if (!$confirmed) {
							?>
								PROFORMA INVOICE
							<? } else {?>
								TAX INVOICE
							<? }?>
						</span>
						<? }?>						
						<?php
							if($print==0 && $confirmed){
						?>
						<div id="printMsg" class="printSOMsg">(ORIGINAL)</div>
						<?php
							 } else if ($print==1 && $confirmed) {
						?>
							<div id="printMsg" class="printSOMsg">(DUPLICATE)</div>
						<?php 
							} else if ($confirmed)  {
						?>
							<div id="printMsg" class="printSOMsg">(TRIPLICATE)</div>
						<?php
							}
						?>
<div class="print-SOTHead-item" style="font-size:11px; font-weight:bold; line-height:normal;">Invoice for removal of<br/>Excisable goods from<br/> factory or warehouse on <br>payment of duty <br>(Rule-11 of C.Ex. Rules 2002)</div>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
		</table>
		</TD>
	</tr>	
  <tr>
	<td align="LEFT" valign="top" width='99%' style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000">
	<!-- 	EXcise invoice	 -->
	<table width='100%' cellpadding='0' cellspacing='0' align="center">
	<tr>
		<TD width="35%" style="border-right: 1px solid #000000;" valign="top">
			<table height="100px" cellpadding="0" cellspacing="0">
				<TR>
					<TD class="ex-print-listing-head" valign="top" style="padding:2px;">AUTHENTICATION STAMP & SIGN</TD>
				</TR>
			</table>
		</TD>
		<TD width="35%" style="border-right: 1px solid #000000; padding:0px 2px;" valign="top">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head">C Excise Regn NO:&nbsp;</TD>
							<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=$centralExNo?></td>
						</TR>
						</table>
					</TD>
				</tr>	
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head">PAN NO:&nbsp;</TD>
							<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=$panNo?></td>
						</TR>
						</table>
					</TD>
				</tr>								
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head">Name of Excisable Commodity:</TD>		
							</TR>
							<tr>
								<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=$nameOfExCommodity?></td>
							</tr>
						</table>
					</TD>
				</tr>
			</table>	
		</TD>
		<TD width="35%" style="padding:0px 2px;" valign="top">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head">COMMISSIONERATE:&nbsp;</TD>
							<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=$commissionerate?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head">RANGE:&nbsp;</TD>
							<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=$range?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head">DIVISION:&nbsp;</TD>
							<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=$division?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head" nowrap="true">PREP. OF INVOICE DATE & TIME:</TD>		
							</TR>
							<tr>
								<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=$createdDate?>&nbsp;<?=date("g:i A"); ?></td>
							</tr>
						</table>
					</TD>
				</tr>
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head" nowrap="true">REMOVAL OF GOODS DATE & TIME:</TD>	
							</TR>
							<tr>
								<td class="listing-item" style="font-size:9px; font-weight:bold;"><?=($gatePassNo)?dateFormat($gpassDate)."&nbsp;".date("g:i A"):"";?></td>
							</tr>
						</table>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
	<tr>
		<TD colspan="3" class="listing-item" style="border-top: 1px solid #000000;border-bottom: 1px solid #000000; font-size:9px;" valign="top">
		No. & Date of Notification under which any concessional rate of duty is claimed:&nbsp;<span class="listing-item" style="font-size:9px; font-weight:bold;"><?=$notificationDetails?></span>
		</TD>
	</tr>
	<tr>
		<TD width="35%" style="border-right: 1px solid #000000;" valign="top">
			<table height="100px" cellpadding="0" cellspacing="0">
				<TR>
					<TD class="ex-print-listing-head" valign="top" style="padding:2px;">NAME & ADDRESS OF BUYER</TD>
				</TR>
				<tr>
					<TD>
						<table cellspacing='0' cellpadding='0' width="100%">
							<!--tr>
							<td nowrap="nowrap" class="ex-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
								To
							</td>
							</tr-->
							<tr>
							<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:9px;">
								<strong>M/S.&nbsp;<?=$distributorName?></strong>
							</td>
							</tr>
							<tr>
							<td class="listing-item" width='350' height="20" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
								<?=$address?>
							</td>
							</tr>
							<tr>
								<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
									<?=$cityName.",&nbsp;".$stateName?>
								</td>
							</tr>
							<?php 
								if ($pinCode!="") {
							?>
							<tr>
								<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
									PIN - &nbsp;<?=$pinCode?>
								</td>
							</tr>
							<?	
								}
							?>
							<?php 
								if ($telNo!="") {
							?>
							<tr>
								<td class="listing-item" width='200' height='20' colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
									TEL - &nbsp;<?=$telNo?>
								</td>
							</tr>
							<?php 
								}
							?>
							<?php
								if ($taxType=='CST') {
							?>
							<tr><TD height="2"></TD></tr>
							<tr><TD colspan="2">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">CST No:</td>
										<td class="listing-item-print" nowrap="nowrap" style="padding-left:5px;padding-right:5px;font-size:9px;"><?=$cstNo?></td>
									</tr>
								</table>
							</TD></tr>
							<tr><TD height="2"></TD></tr>
							<?php
								} else {
							?>
							<tr><TD height="2"></TD></tr>
							<tr><TD colspan="2">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">TIN No:</td>
										<td class="listing-item-print" style="padding-left:5px;padding-right:5px;font-size:9px;"><?=$tinNo?></td>
									</tr>
								</table>
							</TD></tr>
							<tr><TD height="2"></TD></tr>
							<?php
								}
							?>
							<?php
								if ($distEccNo!="") {
							?>
							<tr><TD height="2"></TD></tr>
							<tr><TD colspan="2">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">ECC No:</td>
										<td class="listing-item-print" nowrap="nowrap" style="padding-left:5px;padding-right:5px;font-size:9px;"><?=$distEccNo?></td>
									</tr>
								</table>
							</TD></tr>
							<tr><TD height="2"></TD></tr>
							<?php
								}
							?>
						</table>
					</TD>
				</tr>
			</table>
		</TD>
		<TD width="35%" style="border-right: 1px solid #000000;" valign="top">
			<table height="100px" cellpadding="0" cellspacing="0" border="0" width="100%">
				<TR>
					<TD class="ex-print-listing-head" valign="top" align="center" style="padding:2px; height:30px;">
						FOR DELIVERY AT<br>(DELIVERY ADDRESS / REMARKS)
					</TD>
				</TR>
				<tr>
					<TD valign="top">
						<table cellpadding="0" cellspacing="0">
						<?php
							// If different address
							if ($sameBillingAddress=='N') {
						?>
						<tr>
							<TD valign="top">					
							<table cellspacing='0' cellpadding='0' width="100%" >
									<tr>
									<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
										<?=$deliveryAddress?>
									</td>
									</tr>
									<tr>
										<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
											<?=$cityName.",&nbsp;".$stateName?>
										</td>
									</tr>
								</table>
							</TD>
						</tr>
						<?php
							}
						?>
						<?php
							if ($soRemark!="") {
						?>
						<tr>
							<TD valign="top" style="padding-top:5px;">
								
								<table cellspacing='0' cellpadding='0' width="100%" >
										<tr>
										<td width="100%" valign="top" class="listing-item" style="padding:0 10px;font-size:11px;line-height:normal;">
											<?=$soRemark?>
										</td>
										</tr>					
									</table>								
							</TD>
						</tr>
						<? }?>
						</table>
					</TD>
				</tr>				
			</table>
		</TD>
		<TD width="35%" valign="top">
		<table cellspacing='0' cellpadding='0' width="100%" >
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0"  width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true">
										INVOICE NO.:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="true" valign="middle" align="left">
										<?=$pOGenerateId?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true" align="right" width="25%" style="padding:0 2px;">
										DTD:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$createdDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>	
				</table>	
					</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0"  width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle">
										PO NO.:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="true" valign="middle" align="left">
										<?=$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										DTD:
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>
				</table>	
						</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0"  width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true">
										D.CHALLAN NO.:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="true" valign="middle" align="left">
										<?=$gatePassNo//$challanNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										DTD:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=($gatePassNo)?dateFormat($gpassDate):"";//$challanDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>
				</table>	
				</td>
						</tr>
					</table>
				</td>
				</tr>
				<!--<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" >
					<TR>
						<TD class="ex-print-listing-head">
							DESTINATION :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?php //echo $cityName.",&nbsp;".$stateName;?>
						</td>
					</TR>
				</table>	
							</td>
						</tr>
					</table>
				</td>
				</tr>-->
				<tr>
				<td style="border-bottom: 0px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" >
					<TR>
						<TD class="ex-print-listing-head">
							TRANSPORTER:
						</TD>
						<td class="listing-item-print" style="padding:0 3px;" nowrap="true">
							<?=$transporterName?>
						</td>
					</TR>
				</table>	
							</td>
						</tr>
					</table>
				</td>
				</tr>
				</table>
		</TD>
	</tr>
	</table>	
	</td>
  </tr>
  <tr>
    <td colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
<table width="100%" cellpadding="2" cellspacing="0">
	<?php
		if (sizeof($salesOrderItemRecs)) {
	?>
      <tr bgcolor="#f2f2f2" align="center">
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="5%" nowrap="true">SR.<br/>NO</td>
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="15%" nowrap="true">Ex.Chapter/<br>Subheading</td>
        <td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="35%" nowrap="true">DESCRIPTION OF GOODS</td>
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">UNITS</td>
	<? if ($invoiceType=='T') {?>
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">SCHEME</td>	
	<? } ?>
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true" width="7%">TOTAL<br>UNITS</td>	
        <td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="8%" nowrap="true">RATE PER <br/>UNIT (RS.)</td>
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">RATE OF<br>DUTY</td>
	<td class="p-listing-head" style="padding:0px 3px; border-bottom: 1px solid #000000;" nowrap="true" width="12%">TOTAL<br> VALUE</td>
      </tr>
      <?php
		$defaultNumRows = 10; // 20
		$numRows = 8; // Setting No.of rows 20/12
		$j = 0;
	
		//$decreaseRow = 1;
		//if ($sameBillingAddress=='N') $decreaseRow = 3;
		//if ($soRemark!="")	      $decreaseRow += 2	;		
		//if ($numRows==sizeof($salesOrderItemRecs)) $numRows = $numRows-$decreaseRow;

		$salesOrderRecSize = sizeof($salesOrderItemRecs);

		# Find Balance Rows		
		$balanceRows = ($salesOrderRecSize%$numRows);
		if ($balanceRows<=1 && $balanceRows!=0) $numRows = $numRows-1; 
		
		$totalPage = ceil($salesOrderRecSize/$numRows);
		$totalAmount 	= 0;
		$totalNumMCPack = 0;
		$totalNumLoosePack = 0;
		$totalQuantity	= 0;
		$totalFreePkts = 0;
		$grTotalUnits = 0;
		$i = 0;
		$resultNumMCArr = array();
		$resultNumLPArr = array();
		$resultQtyArr   = array();
		$resultTotalArr = array();
		$rTotalFreePkts = array();
		$resultTotalUnitsArr = array();

		$commonStyle = "padding:0px 3px; font-size:9px;line-height:normal;";

		foreach ($salesOrderItemRecs as $por) {
			$i++;
			$selProductId	= $por[2];			
			$unitRate	= $por[3];
			$quantity	= $por[4];
			$totalQuantity  += $quantity;
			$total		= ($invoiceType=='T')?$por[5]:0;			
			$productName	= "";
			$productRec	= $manageProductObj->find($selProductId);
			$productName	= $productRec[2];
			$totalAmount 	= $totalAmount + $total;
			$numMCPack	= $por[7];
			$totalNumMCPack += $numMCPack;
			$numLoosePack	= $por[8];
			$totalNumLoosePack += $numLoosePack;
			$resultNumMCArr[$j] += $numMCPack;
			$resultNumLPArr[$j] += $numLoosePack;
			$resultQtyArr[$j]   += $quantity;
			$resultTotalArr[$j] += $total;

			$freePkts 	= $por[13];
			$totalFreePkts += $freePkts;
			$totalFreePktsArr[$j] += $freePkts;

			$exDutyPercent		= $por[15];	
			$exDutyMasterId		= $por[17];

			$exChapterSubhead = "";
			$exChapterSubhead = $salesOrderObj->getExCodeByProductId($selProductId);
			if ($exChapterSubhead=="") {
				$edRec = $salesOrderObj->getExciseDutyRec($exDutyMasterId);
				$exChapterSubhead	= $edRec[0];
			}

			$soTotalUnits	= $quantity+$freePkts;
			$grTotalUnits += $soTotalUnits;
			$resultTotalUnitsArr[$j] += $soTotalUnits;
	?>
      <tr>
	<td height='20' class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="center"><?=$i?></td>
	<td class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true">
		<?=($exChapterSubhead!="")?$exChapterSubhead:"&nbsp;"?>
	</td>
 	<td class="listing-item" style="<?=$commonStyle?> line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true">
		<?=$productName?>
	</td>	
        <td class="listing-item" align="right" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><?=number_format($quantity,0,'.','');?></td>	
	<? if ($invoiceType=='T') {?>
	<td class="listing-item" align="right" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><?=$freePkts?></td>	
	<? }?>
	<td class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<?=$soTotalUnits?>
	</td>	
        <td class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true" align="right" >
		<?php if ($invoiceType=='T') {?><?=number_format($unitRate,2,'.','');?><?} else {?>0 <?}?>
	</td>
	<td class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<?=($exDutyPercent!=0)?$exDutyPercent."%":number_format($exDutyPercent,0,'.','')."%";?>
	</td>
	<td class="listing-item" style="<?=$commonStyle?> border-bottom: 1px solid #000000;" align="right">
		<?=$total?>
	</td>
      </tr>
	  	<?php
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
			$j++;
		?>
		<tr>
			<td height="20" colspan="3" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000;">Total:</td>
			<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong>&nbsp;<?//=$resultNumMCArr[$j-1];?></strong></td>
			<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong>&nbsp;<?//=$resultNumLPArr[$j-1]?></strong></td>
			<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong><?=$resultTotalUnitsArr[$j-1]?><?//=number_format($resultQtyArr[$j-1],0,'','');?></strong></td>
			<? if ($invoiceType=='T') {?>
			<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong>&nbsp;<?//=$totalFreePktsArr[$j-1]?></strong></td>
			<? }?>
			<td class="listing-item" style="padding:0px 3px; font-size:8px;border-right: 1px solid #000000;" align="right">&nbsp;</td>
			<td class="listing-item" style="padding:0px 3px; font-size:9px;" align="right"><strong><? echo number_format($resultTotalArr[$j-1],2);?></strong></td>
		</tr>
	    </table></td></tr>
<!--  Sign Starts-->
<tr>
	<TD colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
		<table width='99%' cellpadding='0' cellspacing='0' align="center">
			<tr>	
				<td>
					<table cellspacing='0' cellpadding='0' width="100%" >
						<tr><TD height="5"></TD></tr>
						<tr>
							<td align="left">
		<table width="85%" cellpadding="2" cellspacing="0">
		</table>
			</td>
		</tr>
		<tr><TD height="5"></TD></tr>		
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<TR>
					<TD valign="bottom" height="5" width="400px">			
					</td>
					<td rowspan="5" valign="bottom" style="line-height:100px;">
						<table width="100%" align="right" cellpadding="0" cellspacing="0">
							<tr>
								<td class="listing-item" nowrap align="left" style="padding-left:5px;">
								For <strong><?=$forstinsfoods["fifoods"];?> <br>
								<span style="font-size:7pt;"><?=$divfrfoods["frfoods"];?></span>
								</strong>
								<br><br><br><br><div align="center">Authorised Signatory</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
		<tr>
			<TD valign="bottom">
				<table width="98%" cellpadding="0" cellspacing="0">
					<TR>
						<TD valign="bottom" class="listing-item" style="padding-left:5px;">
						<?php
							if ($totalPage>1) echo "(Page $j of $totalPage)";
						?>
						</TD>
					</TR>
				</table>
			</TD>
		</tr>
		</table>
		</td>
	</tr>	
	</table>
	</td>
	</tr>
	</table>
	</TD>
  </tr>
<!-- Ends Here -->	
	</table>
	</td></tr>
     </table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='95%' cellspacing='1' cellpadding='1'  align='center'>
	  <tr>
	  	<td STYLE="border-top: 1px solid #f2f2f2; border-left: 1px solid #f2f2f2; border-right: 1px solid #f2f2f2; border-bottom: 1px solid #f2f2f2;">
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
	  		<tr bgcolor='white'>
				<td height="5"></td>
 	  		</tr>
	   <tr>
		<TD colspan="17" align="center">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top"><img src="images/ForstarLogo.png" alt=""/></TD>
					<td class="pageName" valign="bottom" align="center">
						<?php if ($invoiceType=='S') {?>	
						<span style="font-size:16px;">
							SAMPLE INVOICE
						</span>
						<? } else {?>
						<span style="font-size:16px;">
							<?php
								if (!$confirmed) {
							?>
								PROFORMA INVOICE
							<? } else {?>
								TAX INVOICE
							<? }?>
						</span>
						<? }?>						
						<?php
							if($print==0 && $confirmed){
						?>
						<div id="printMsg" class="printSOMsg">(ORIGINAL) - Cont.</div>
						<?php
							 } else if ($print==1 && $confirmed) {
						?>
							<div id="printMsg" class="printSOMsg">(DUPLICATE) - Cont.</div>
						<?php 
							} else if ($confirmed)  {
						?>
							<div id="printMsg" class="printSOMsg">(TRIPLICATE) - Cont.</div>
						<?php
							} else {
						?> 
							<div id="printMsg" class="printSOMsg">(Cont.)</div>
						<?php
							}
						?>
					</td>
					<td align="right" valign="top">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<TD>
									<table cellpadding="0" cellspacing="0">
										<TR>
											<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$forstinsfoods["fifoods"];?></font></TD>
										</TR>
										<TR>
											<TD class="listing-head" style="font-size:9px;text-align:center;" valign="top"><?=$divfrfoods["frfoods"];?></TD>
										</TR>
									</table>
								</TD>
							</tr>
						</table>
					</td>
				</TR>
			</table>
		</TD>
	  </tr>
	<tr bgcolor="White"><TD height="5"></TD></tr>
	<tr>
	<td align="center" valign="top" width='100%'>
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2' height="5"></td>
	 </tr>
	</table>
	</td>
  </tr>
	<tr>
	<td align="LEFT" valign="top" width='100%' style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000">
	<table width='99%' cellpadding='0' cellspacing='0' align="center">
		<tr>
			<TD rowspan="6" width="400px;"> 
				<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="ex-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						To
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:9px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
				</table>
			</TD>
			<TD style="padding-left:3px;padding-right:3px;" nowrap="true" colspan="4" width="282px;" valign="middle">
				<table cellpadding="0" cellspacing="0" border="0" >
					<TR>
						<TD class="ex-print-listing-head"  valign="middle">
							INVOICE NO. & DATE :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle">
							<?=$pOGenerateId.",&nbsp;".$createdDate?>
						</td>
					</TR>
				</table>
			</TD>		
		</tr>
	</table>
	</td>
  </tr>
  <tr>
	<td colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
  	  <table width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="#f2f2f2" align="center">
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="5%" nowrap="true">SR.<br/>NO</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="15%" nowrap="true">Ex.Chapter/<br>Subheading</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="35%" nowrap="true">DESCRIPTION OF GOODS</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">UNITS</td>
		<? if ($invoiceType=='T') {?>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">SCHEME</td>	
		<? } ?>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true" width="7%">TOTAL<br>UNITS</td>	
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="8%" nowrap="true">RATE PER <br/>UNIT (RS.)</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">RATE OF<br>DUTY</td>
		<td class="p-listing-head" style="padding:0px 3px; border-bottom: 1px solid #000000;" nowrap="true" width="12%">TOTAL<br> VALUE</td>
      </tr>
   <?php
	#Main Loop ending section 
			
	       }
	}
			# height 
			//$hgt = ( 10 + 8 ) * 20 - ($numRows * 20 ); // Original
			if ($balanceRows>0) $salesOrderRecSize = $balanceRows; 
			//$mValue = number_format(($numRows-$salesOrderRecSize),0); $mValue Replaced	$defaultNumRows		
			$hgt = ($salesOrderRecSize+(-2))*$defaultNumRows-($numRows*$defaultNumRows);
			$defaultHgt = 80; // 80
			//echo abs($hgt).">=".$defaultHgt;
   ?>
	<?php
		if ($salesOrderRecSize<$numRows && abs($hgt)>=$defaultHgt) {
	?>
	<tr rowspan='8' height='<?=abs($hgt)?>' >
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>	
		<td class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
			<?php if ($invoiceType=='S') {?>
				<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="ex-print-listing-head" height="15" style="padding-left:5px;padding-right:5px;">
						(NOT FOR COMMERCIAL PURPOSE)
					</td>
					</tr>
				</table>
			<?php
				} else echo "&nbsp;";
			?>		
		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>		
		<? if ($invoiceType=='T') {?>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<? }?>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	</tr>
	<?php
		} // height Ceck Ends ere
	?>	
	<?php	
		if ($totalPage>1) {
	?>
	<tr>
        <td height="20" colspan="3" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">Total&nbsp;(<?=$totalPage?>):</td>
        <td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
			<strong>&nbsp;<?//=($resultNumMCArr[$j])?$resultNumMCArr[$j]:"&nbsp;";?></strong>
	</td>
	<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<strong>&nbsp;<?//=($resultNumLPArr[$j])?$resultNumLPArr[$j]:"&nbsp;";?></strong>
	</td>
	<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<strong><?=($resultTotalUnitsArr[$j])?$resultTotalUnitsArr[$j]:"&nbsp;";?><?//=($resultQtyArr[$j])?number_format($resultQtyArr[$j],0,'',''):"&nbsp;";?></strong>
	</td>
	<? if ($invoiceType=='T') {?>
	<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<strong>&nbsp;<?//=($totalFreePktsArr[$j])?$totalFreePktsArr[$j]:"&nbsp;";?></strong>
	</td>
	<? }?>		
	<td class="listing-item" style="padding:0px 3px; font-size:8px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><? echo number_format($resultTotalArr[$j],2);?></strong></td>
      </tr>	
	<?
		for ($p=1;$p<=$totalPage-1;$p++) {
	?>
		<tr>
		<td height="20" colspan="3" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">Total&nbsp;(<?=$p?>):</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong>&nbsp;<?//=$resultNumMCArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong>&nbsp;<?//=$resultNumLPArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=($resultTotalUnitsArr[$p-1])?$resultTotalUnitsArr[$p-1]:"&nbsp;";?><?//=number_format($resultQtyArr[$p-1],0,'','');?></strong></td>
		<? if ($invoiceType=='T') {?>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
			<strong>&nbsp;<?//=$totalFreePktsArr[$p-1];?></strong>
		</td>
		<? }?>		
		<td class="listing-item" style="padding:0px 3px; font-size:8px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right">
			<strong><? echo number_format($resultTotalArr[$p-1],2);?></strong>
		</td>
	</tr>	
	<?php
		}  // Total Loop
	?>
	<?php
		} // Balance Row check
	?>
<!-- Single Page total section -->
<?php
		if ($discount=='Y') {
		$totalAmount = $totalAmount - $discountAmt;
	?>
	<tr bgcolor="white">		
		<td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" colspan="6"><?=$discountRemark?></td>
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="2">(Less) <br/>DISCOUNT&nbsp;<?=$discountPercent;?>%</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$discountAmt?></strong></td>
	</tr>
	<?php
		}
	?>
      <tr>
        <td height="20" colspan="8" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size:9px;">
		<? if ($totalPage>1) echo "Gross&nbsp;";?> Sub-Total
	</td>
       	<!--td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=number_format($totalQuantity,0,'','');?></strong></td-->	
	<? if ($invoiceType=='T') {?>
	<!--td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=$totalFreePkts?></strong></td-->
	<? }?>
	<!--td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=$grTotalUnits?></strong></td>
	<td class="listing-item" style="padding:0px 3px; font-size:8px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding:0px 3px; font-size:8px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td-->
	<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr>
	<?php
		$subTotalAfterExDuty = $totalAmount+$grTotCentralExDuty;
	
		//$formCTActive = false;
		//if ($grTotCentralExDuty==0)
		
	if ($exDutyActive>0) {
	?>
	<tr bgcolor="white">		
		<td class="listing-item" align="left" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" colspan="6" rowspan="<?=($grTotCentralExDuty!=0)?4:0?>">
			<table>
				<tr>
					<TD>
						<table border="0" cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head">Total Duty Payable (in words): </TD>
								<td class="listing-item" align="left" style="padding:0px 3px; font-size:9px; line-height:normal;">
									<strong>
									<?php
										$cExToWords = "";
										list($ceNum, $ceDec) = explode(".", $grTotCentralExDuty); 
										$cExToWords .= convertNum2Text($ceNum);
										if($ceDec > 0) {
											$cExToWords .= " paise ";
											$cExToWords .= convertNum2Text($ceDec);
										}
										
										echo "Rs. ".ucfirst($cExToWords)." only";
									?>
									</strong>
								</td>
							</TR>
						</table>
					</TD>
				</tr>	
				<tr><TD height="5"></TD></tr>			
				<TR>
					<TD class="ex-print-listing-head" colspan="2">Serial No & Debit Entry in:</TD>					
				</TR>
				<tr>
					<TD class="ex-print-listing-head">PLA:</TD>
				</tr>
				<tr>
					<TD class="ex-print-listing-head">RG 23A:</TD>
				</tr>
				<tr>
					<TD class="ex-print-listing-head">RG 23C:</TD>
				</tr>
			</table>
		</td>
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="2">
		<? if ($grTotCentralExDuty!=0) { ?>Basic Excise Duty<? } else { ?>Excise Duty 0%<br> Against Form CT1<? }?>
		</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$totExDutyAmt?></strong></td>
	</tr>
	<?
	if ($grTotCentralExDuty!=0) {
	?>
	<tr bgcolor="white">	
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="2">Edu Cess - <?=$eduCessPercent?>%</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$totEduCessAmt?></strong></td>
	</tr>
	<tr bgcolor="white">		
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="2">Sec. EduCess - <?=$secEduCessPercent?>%</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$totSecEduCessAmt?></strong></td>
	</tr>
	<tr bgcolor="white">				
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="2">Total C.Excise Duty</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$grTotCentralExDuty?></strong></td>
	</tr>	
	<tr bgcolor="white">				
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="8">Total (Rs.)</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><? echo number_format($subTotalAfterExDuty,2);?></strong></td>
	</tr>	
	<?php
		}
	}	
	?>
	
	<?php
	if (sizeof($taxApplied)>0 && $invoiceType=='T') {	
		for ($j=0;$j<sizeof($taxApplied);$j++) {
			$selTax = explode(":",$taxApplied[$j]); // Tax Percent:Amt
	?>
	<tr>
		<td height="20" colspan="6" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="2">Add:&nbsp;<?=$taxType?> <?=$selTax[0]?>%</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=$selTax[1];?></strong></td>
      </tr>
	<?php
		}	// For Loop Ends Here
	} // Tax Size Check Ends Here

		if ($invoiceType!='T') $btmColSpan = 3;
		else $btmColSpan = 6;
	?>
	<?php
		if ($transportChargeActive=="Y") {
	?>
	<tr bgcolor="white">				
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true" colspan="8">Freight</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$transportCharge;?></strong></td>
	</tr>	
	<?php
	}
	?>
	<tr>
		<td height='25' colspan="<?=$btmColSpan?>" nowrap="nowrap"  align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
			<table cellspacing='0' cellpadding='0' width="100%" >
				<tr>
					<TD class="ex-print-listing-head" height="20" style="padding-left:5px;padding-right:5px;">NET WT:&nbsp;<span class="listing-item"><strong><?=$soNetWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">GR. WT:&nbsp;<span class="listing-item"><strong><?=$soGrossWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">NO.OF BOXES:&nbsp;<span class="listing-item"><strong><?=$soTNumBox;?></strong></span></TD>
				</tr>
			</table>
		</td>
		<td height='25' class="listing-head" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" colspan="2">Round</td>
		<td height='25' class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=($invoiceType=='T')?$roundVal:0;?></strong></td>
	</tr>
	<tr>
		<td height='25' colspan="<?=$btmColSpan?>" nowrap="nowrap" align="left" style="border-right: 1px solid #000000;">
			<table cellspacing='0' cellpadding='0' width="100%" >
				<tr>
					<TD class="ex-print-listing-head" height='20' colspan="3" style="padding-left:5px;padding-right:5px;">Invoice Value (in Words):&nbsp;
						<?php 
							$input = round($grandTotalAmt);
							echo "Rs.".convert($input)."Only";
						?> 
						
					</TD>
				</tr>
			</table>
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; line-height:normal;" align="right" colspan="2">Total Value of Goods (Rs.)</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px;" align="right"><strong><? echo number_format($grandTotalAmt,2);?></strong></td>
      </tr>
    </table></td>
  </tr>
  <? } else {?>
  <tr> 
    <td colspan="17" align="center" class="err1"><?=$msgNoRecords;?></td>
  </tr>
	<? }?>
  <tr>
	<TD colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" >
		<table width='99%' cellpadding='0' cellspacing='0' align="center">
			<tr>	
				<td>
		<table cellspacing='0' cellpadding='0' width="100%" >
		<!--<tr><TD height="5"></TD></tr>-->		
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<?php
					// If different address
					if ($sameBillingAddress=='N' && abs($hgt)<$defaultHgt) {
				?>
				<?php if ($invoiceType=='S') {?>
				<tr><TD height="10"></TD></tr>
				<tr><TD valign="top">
				<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="ex-print-listing-head" height="15" style="padding-left:5px;padding-right:5px;">
						(NOT FOR COMMERCIAL PURPOSE)
					</td>
					</tr>
				</table>
				</TD></tr>
			<?php
				}
			?>
				<tr>
					<TD valign="top">
						<table width="99%" cellpadding="2" cellspacing="0">		
							<tr>
								<TD >
								<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="ex-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Delivery Address
					</td>
					</tr>
					<tr><TD height="5"></TD></tr>					
					<tr>
					<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
						<?=$deliveryAddress?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
				</table>
				</TD>		
				</tr>
				</table>
			</TD>
			</tr>
			<?php	
				} // Delivery Ends Here
			?>	
		<tr>
			<TD valign="top">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<TR>

						<TD width="70%" class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:8px;line-height:11px; padding:2px; text-align: justify;">
						<?=$certifiedAgreementTxt?> 
						</TD>
						<TD width="30%" rowspan="3" valign="bottom">
							<table width="100%" align="right" cellpadding="0" cellspacing="0">
							<tr>
								<td class="listing-item" nowrap align="center" style="padding-left:5px; font-size:9px;">
								For <strong><?=$forstinsfoods["fifoods"];?> <br>
								<span style="font-size:7px;"><?=$divfrfoods["frfoods"];?></span>
								</strong>								
								</td>
							</tr>
							<tr><TD height="90px">&nbsp;</TD></tr>
							<tr>
								<TD valign="bottom" class="listing-item" nowrap align="left" style="padding-left:5px;" ><div align="center">(Authorised Signatory)</div></TD>
							</tr>
						</table>
						</TD>
					</TR>
					<TR>
						<TD  width="70%" class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:8px;line-height:11px; padding:0px 2px; text-align: justify;">
						<b><u>Terms & Conditions:</u></b>&nbsp;<?=$termsNConditionTxt?>
						</TD>
					</TR>
					<TR>
						<TD  style="border-right: 1px solid #000000;" valign="top">
							<table width="100%" cellpadding="0" cellspacing="0">
								<TR>
									<TD class="listing-item" style="font-size:8px;line-height:11px; border-right: 1px solid #000000; text-align: justify; padding:0px 2px;" width="50%" valign="top">
										<b><u>E. & O.E.</u></b>&nbsp;<?=$eOETxt?>
									</TD>
									<td width="25%" valign="top" valign="bottom">
										<table cellpadding="0" cellspacing="0">
											<TR>
												<TD class="listing-item" style="font-size:8px;line-height:11px;" align="center">Received the above goods<br>in good condition</TD>
											</TR>
											<tr><TD height="50px">&nbsp;</TD></tr>
											<TR>
												<TD class="listing-item" style="font-size:8px;line-height:11px;" valign="bottom" align="center">
												<?php
						if ($totalPage>1) echo "(Page $totalPage of $totalPage)&nbsp;&nbsp;";
					?> (Receiver's Signature and Stamp)
												</TD>
											</TR>
										</table>
									</td>
								</TR>
							</table>
						</TD>
					</TR>
				</table>
			</TD>
		</tr>		
			</table>
		</td>
	</tr>	
	</table>
		</td>
			</tr>
		</table>
	</TD>
  </tr>
  </table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
<?php
	} // Num copy Ends here
?>
<?php
	# Gate Pass Section Starts Here
	if ($gatePassNo!="" && $gatePassNo!=0 && $invoiceConfirmed=='C' && !$gPassPrintDisabled) {

	# Number of Copy	
	for ($print=0;$print<$numCopy;$print++) {
?>
<table width='95%' cellspacing='1' cellpadding='1'  align='center'>
<tr>
	<td STYLE="border-top: 1px solid #f2f2f2; border-left: 1px solid #f2f2f2; border-right: 1px solid #f2f2f2; border-bottom: 1px solid #f2f2f2;">
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
	<!--<tr><TD height="5"></TD></tr>-->
	<tr>
		<TD style="padding-left:5px; padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top">
						<img src="images/ForstarLogo.png" alt=""/>
					</TD>
					<td  valign="bottom" align="center">
						<table>
							<TR><TD class="pageName" align="center" style="font-size:12px; line-height:normal;">
							EXIT PASS CUM <br>DELIVERY CHALLAN	
							</TD>
							</TR>
							<TR><TD class="printSOMsg" align="center">
								<?php
							if($print==0 && $confirmed) echo "(ORIGINAL)";
							else if ($print==1 && $confirmed) echo "(DUPLICATE)";
							else if ($confirmed)  echo "(TRIPLICATE)";
								?>
							</TD>
							</TR>
						</table>						
					</td>
					<td align="right">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<TD>
									<table cellpadding="0" cellspacing="0">
										<TR>
											<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$forstinsfoods["fifoods"];?></font></TD>
										</TR>
										<TR>
											<TD class="listing-head" style="font-size:9px;text-align:center;" valign="top"><?=$divfrfoods["frfoods"];?></TD>
										</TR>
									</table>
								</TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item" nowrap="true"><?=$addr["ADR1"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item" nowrap="true"><?=$addr["ADR2"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item" nowrap="true"><?=$addr["ADR3"]?>&nbsp;<?=$addr["ADR4"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item" nowrap="true"><?=$companyArr["Email"]?></TD>
							</tr>
						</table>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
  <tr>
    <td colspan="17" align="RIGHT"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%'>
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2' height="2"></td>
	 </tr>
	</table>
	</td>
  </tr>
  <tr>
	<td align="LEFT" valign="top" width='99%' style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000">
	<table width='99%' cellpadding='0' cellspacing='0' align="center">
		<tr>
			<TD width="38%" style="border-right: 1px solid #000000;" valign="top">
				<table cellspacing='0' cellpadding='0' width="100%">
					<tr>
					<td nowrap="nowrap" class="ex-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						NAME AND ADDRESS
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:9px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" width='350' height="20" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
						<?=$address?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
					<?php 
						if ($pinCode!="") {
					?>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
							PIN - &nbsp;<?=$pinCode?>
						</td>
					</tr>
					<?	
						}
					?>
					<?php 
						if ($telNo!="") {
					?>
					<tr>
						<td class="listing-item" width='200' height='20' colspan="2" style="padding-left:10px;padding-right:10px;font-size:9px;">
							TEL - &nbsp;<?=$telNo?>
						</td>
					</tr>
					<?php 
						}
					?>					
				</table>
			</TD>
			<TD width="45%" valign="top">
				<table cellspacing='0' cellpadding='0' width="100%" >
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0"  width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true">
										GATE PASS NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$gatePassNo?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true" align="right">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left">
										<?=dateFormat($gpassDate)//$createdDate?>
									</td>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true" align="right">
										TIME :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left">
										<?=date("g:i A");?>
									</td>
								</TR>
							</table>
						</td>
					</tr>	
				</table>	
					</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0"  width="100%">
					<tr>
						<TD width="20%" nowrap="true">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<td class="ex-print-listing-head" valign="middle" nowrap>
										INVOICE NO. :
									</td>
									<td class="listing-item-print" style="padding:0px 3px;" nowrap="true" valign="middle" align="left">
										<?=$pOGenerateId?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="68%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true" align="right" style="padding:0px 3px;">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left">
										<?=$createdDate?>
									</td>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="true" align="right">
										&nbsp;
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left">
										&nbsp;
									</td>
								</TR>
							</table>
						</td>
					</tr>
				</table>	
						</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
					<tr>	
					<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
						<table cellpadding="0" cellspacing="0" border="0" >
						<TR>
						<TD class="ex-print-listing-head">
							DESPATCH THROUGH :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?=$transporterName?>
						</td>
						</TR>
						</table>
					</td>
					</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
					<tr>	
					<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
						<table cellpadding="0" cellspacing="0" border="0" >
						<TR>
						<TD class="ex-print-listing-head">
							TOTAL NO OF BOXES :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<b><?=$soTNumBox?></b>
						</td>
						</TR>
						</table>
					</td>
					</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td style="border-bottom: 0px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
					<tr>	
					<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
						<table cellpadding="0" cellspacing="0" border="0" >
						<TR>
						<TD class="ex-print-listing-head">
							VEHICLE NO: 
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<b><?=$vehicleNo?></b>
						</td>
						</TR>
						</table>
					</td>
					</tr>
					</table>
				</td>
				</tr>
				</table>
			</TD>
		</tr>
<!-- 	New Ends Here	 -->
	</table>
	</td>
  </tr>
  <tr>
    <td colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
<table width="100%" cellpadding="2" cellspacing="0">
      <tr>	
 	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 0px solid #000000;" width="40%" nowrap="true">
		DETAILS OF THE CONSIGNMENT
	</td>			
        <td class="listing-item" align="left" style="padding-left:10px; padding-right:3px; font-size:9px; border-bottom: 0px solid #000000;" height="50"><?=$consignmentDetails?></td>	
      </tr>	 
    </table>
  </td></tr>
<!--  Sign Starts-->
<tr>
	<TD colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
		<table width='99%' cellpadding='0' cellspacing='0' align="center">
			<tr><TD height="5">&nbsp;</TD></tr>
			<tr>	
				<td>
					<table cellspacing='0' cellpadding='0' width="100%" >
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">				
			<tr>
			<td valign="top">
			<table width="99%" cellpadding="2" cellspacing="0">			
			<tr><TD height="10"></TD></tr>
			<tr>
	    <td valign="top" nowrap="nowrap" style="line-height:11px;" align="center">
		<table cellpadding="0" cellspacing="0" width="100%">
			<TR>
				<td align="left" class="listing-item" style="padding-left:10px;" width="30%">	
					Receiver's Signature
				</td>
				<td align="center" class="listing-item" nowrap="true" style="padding-left:5px; padding-right:5px;" width="33%">	
					Prepared By
				</td>
				<td class="listing-item" nowrap align="right" style="padding-right:10px;" width="33%">
					Authorised Signatory
				</td>
			</TR>
		</table>		
		</td>
	    </tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>	
	</table>
	</td>
	</tr>
	</table>
	</TD>
  </tr>
<!-- Ends Here -->	
	</table>
	</td></tr>
     </table>
<?php
	echo "<br>";
	 } // Print Loop Ends here
	} // Gate Pass Check ends Here
?>

</body>
</html>

