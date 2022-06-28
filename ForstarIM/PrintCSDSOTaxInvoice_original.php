<?php	
	require("include/include.php");

	# Get Sales Order Id
	$selSOId = $g["selSOId"];
	$confirmed = false;

	// ----------------------------------------------------------
	# Find PO Records
	$sORec	=	$salesOrderObj->find($selSOId);
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
	$gTotalAmt   	 = $sORec[32];
	//echo "$gTotalAmt";
	$roundVal	 = $sORec[33];
	if ($invoiceType=="S") $grandTotalAmt = 100;
	else $grandTotalAmt = round($grandTotalAmt+$roundVal);	

	$octroiExempted = $sORec[34];
	$oecNo		= $sORec[35];
	$oecValidDate	= dateFormat($sORec[36]);
	$oecIssuedDate  = dateFormat($sORec[37]);
	
	$distMgnRateListId 	= $sORec[10];
	$productPriceRateListId = $sORec[8];
	$deliveryDate		= $sORec[6];

	$deliveryMC = $orderDispatchedObj->getTotalMCPack($selSOId);

	$invoiceConfirmed	= $sORec[38];

	// ----------------------------------------------------------
	
	$cityRec	=	$cityMasterObj->find($selCityId);
	$cityName	=	stripSlash($cityRec[2]);

	$stateRec	= $stateMasterObj->find($selStateId);				
	$stateName	= stripSlash($stateRec[2]);
	
	$areaRec	=	$areaMasterObj->find($selAreaId);
	$areaName	=	stripSlash($areaRec[2]);
	# Supplier Rec
	$distributorRec		= $salesOrderObj->getDistributorRec($distributorId, $selStateId, $selCityId, $selAreaId);	
	$distributorName	= stripSlash($distributorRec[2]);
	$address		= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[12]));	
	$telNo			= stripSlash($distributorRec[11]);
	$vatNo			= stripSlash($distributorRec[8]);
	$tinNo			= stripSlash($distributorRec[9]);	
	$cstNo			= stripSlash($distributorRec[10]);
	$deliveryAddress	= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[13]));
	$sameBillingAddress	= $distributorRec[14];	
	$pinCode		= stripSlash($distributorRec[15]);

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
	/* Company Rec Ends Here */

	#`````````````Report Definition````````````````
	list($productRateMarginId, $groupedMgnIds)  = $orderDispatchedObj->getDistributorReportDefinition($distributorId);

	$discountSplitupRecs = $orderDispatchedObj->getDiscountSplitupRecs($distributorId);
	#````````````````````````````

	#  Number of Copy
	if ($invoiceConfirmed=='C') {
		$numCopy	= 3;
		$confirmed = true;
	} else {
		$numCopy	= 1;
	}
?>
<html>
<head>
<title>SALES ORDER MEMO</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
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
		document.getElementById("printButton").style.display="none";	
		if (!printDoc()) {
			setTimeout("displayBtn()",7000); //3500			
		}		
	}
</script>
</head>
<body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="57px;">
<form name="frmPrintSOTaxInvoice" id="frmPrintSOTaxInvoice">
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>
<?php
	# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>
<table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor="White"><TD height="5"></TD></tr>
	<tr bgcolor="White">
		<TD style="padding-left:5px; padding-right:5px;">
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
						<?php }?>
						<?php
							if($print==0 && $confirmed){
						?>
						<div id="printMsg" class="printSOMsg">(ORIGINAL)</div>
						<?php
							 } else if ($print==1 && $confirmed) {
						?>
							<div id="printMsg" class="printSOMsg">(DUPLICATE)</div>
						<?php 
							} else if ($confirmed) {
						?>
							<div id="printMsg" class="printSOMsg">(TRIPLICATE)</div>
						<?php
							}
						?>
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
								<TD class="print-SOTHead-item"><?=$addr["ADR1"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$addr["ADR2"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$addr["ADR3"]?>&nbsp;<?=$addr["ADR4"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$companyArr["Email"]?></TD>
							</tr>
						</table>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
 <tr bgcolor='white'>
	<td height="2"></td>
 </tr>
  <tr bgcolor='white'>
    <td colspan="17" align="RIGHT"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2' height="5"></td>
	 </tr>
	</table>
	</td>
  </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center" style="border-bottom-width:0px">
		<tr>
<!--  style="border-bottom-width:0px" -->
			<TD width="35%" >
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						To
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" width='350' height="20" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<?=$address?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
					<?php 
						if ($pinCode!="") {
					?>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
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
						<td class="listing-item" width='200' height='20' colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
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
								<td class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;">CST No:</td>
								<td class="listing-item-print" nowrap="nowrap" style="padding-left:5px;padding-right:5px;font-size:11px;"><?=$cstNo?></td>
							</tr>
						</table>
					</TD></tr>
					<!--<tr><TD height="2"></TD></tr>-->
					<?php
						} else {
					?>
					<tr><TD height="2"></TD></tr>
					<tr><TD colspan="2">
						<table>
							<tr>
								<td class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;">TIN No:</td>
								<td class="listing-item-print" style="padding-left:5px;padding-right:5px;font-size:11px;"><?=$tinNo?></td>
							</tr>
						</table>
					</TD></tr>
					<!--<tr><TD height="2"></TD></tr>-->
					<?php
						}
					?>	
				</table>
			</TD>
			<TD width="45%" style="border-bottom-width:0px" valign="top" rowspan="2">
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
				<tr>
					<TD height="20">
						<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										INVOICE NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$pOGenerateId?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" align="left" width="25%">
										DATE :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$createdDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
					
				</table>
					</TD>
				</tr>
				<tr><TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										CSD ORDER NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="left" nowrap="true">
										DATE :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										I/PERMIT NO. DATE:
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$oecNo?> - <?=$oecIssuedDate?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="left" nowrap="true">
										VALID UPTO :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$oecValidDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										DELIVERY SCHEDULE:
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$deliveryMC?> CASE
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="left" nowrap="true">
										DATE/MONTH :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=dateFormat($deliveryDate);?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										DESPATCHED BY:
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										ROAD
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										&nbsp;
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?//$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										DESPATCHED FROM:
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										TALOJA
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="left" nowrap="true">
										TO :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$cityName?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										LR./RR.NO :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?//$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="left" nowrap="true">
										DATE :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?//$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										CARRIER :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?//$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="left" nowrap="true">
										DATE :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?//$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										FREIGHT PAID OF DOOR DELIVERY TO PAY RS.  :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?//$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										&nbsp;
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?//$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										DATE OF DELIVERY  :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=dateFormat($deliveryDate);?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										&nbsp;
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?//$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				<tr>
					<TD height="20">
					<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										NO. OF CASES  :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$deliveryMC?>
									</td>
								</TR>
							</table>
						</TD>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										CHALLAN  :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$challanNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										DATE :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$challanDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
				</table>
				</TD></tr>
				</table>
			</TD>
		</tr>
		<tr>
			<TD style="border-bottom-width:0px" >
				<?php
					// If different address
					if ($sameBillingAddress=='N') {
			?>
			<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Delivery To
					</td>
					</tr>
					<!--<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="10" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>-->
					<tr>
					<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<?=$deliveryAddress?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
				</table>
			<?php
				}  // Delivery Address check ends here
			?>
			</TD>
		</tr>
		<!--<tr>
			<TD rowspan="6" width="350px;" style="border-bottom-width:0px"> 
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						To
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" width='350' height="20" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<?=$address?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
					<?php 
						if ($pinCode!="") {
					?>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
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
						<td class="listing-item" width='200' height='20' colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
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
								<td class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;">CST No:</td>
								<td class="listing-item-print" nowrap="nowrap" style="padding-left:5px;padding-right:5px;font-size:11px;"><?=$cstNo?></td>
							</tr>
						</table>
					</TD></tr>
					<tr><TD height="2"></TD></tr>
					<?php
						} else {
					?>
					<tr><TD height="2"></TD></tr>
					<tr><TD colspan="2">
						<table>
							<tr>
								<td class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;">TIN No:</td>
								<td class="listing-item-print" style="padding-left:5px;padding-right:5px;font-size:11px;"><?=$tinNo?></td>
							</tr>
						</table>
					</TD></tr>
					<tr><TD height="2"></TD></tr>
					<?php
						}
					?>
				</table>
			</TD>
			<TD style="padding-left:3px;padding-right:3px;" nowrap="true" colspan="4" width="300px" valign="middle">
				<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										INVOICE NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$pOGenerateId?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" align="right" width="20%">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="25%">
										<?=$createdDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>					
					<!--<TR>
						<TD class="csd-print-listing-head"  valign="middle">
							INVOICE NO. & DATE :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle">
							<?=$pOGenerateId.",&nbsp;".$createdDate?>
						</td>
					</TR>-->
				<!--/table>
			</TD>
		</tr>-->		
		<!--<tr>			
			<TD style="padding-left:3px;padding-right:3px;" colspan="4">
				<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle">
										PO NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="20%" align="right" nowrap="true">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="25%">
										<?=$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>
					<!--<TR>
						<TD class="csd-print-listing-head">
							PO NO. & DATE :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?=$poNoDisplay?>
						</td>
					</TR>-->
				<!--/table>
			</TD>
		</tr>-->
		<!--<tr>			
			<TD style="padding-left:3px;padding-right:3px;" colspan="4">
				<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true">
										D.CHALLAN NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$challanNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="csd-print-listing-head"  valign="middle" nowrap="true" width="20%" align="right" nowrap="true">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="25%">
										<?=$challanDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>
					<!--<TR>
						<TD class="csd-print-listing-head">
							D.CHALLAN NO. & DATE :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?=$challanNoDisplay?>
						</td>
					</TR>-->
				<!--/table>
			</TD>
		</tr>-->
		<!--<tr>			
			<TD style="padding-left:3px;padding-right:3px;" colspan="4">
				<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder">
					<TR>
						<TD class="csd-print-listing-head">
							DESTINATION :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?php echo $cityName.",&nbsp;".$stateName;?>
						</td>
					</TR>
				</table>
			</TD>
		</tr>-->
		<!--<tr>			
			<TD style="padding-left:3px;padding-right:3px;border-bottom-width:0px" colspan="4">
				<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder">
					<TR>
						<TD class="csd-print-listing-head">
							DESPATCH THROUGH :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?=$transporterName?>
						</td>
					</TR>
				</table>
			</TD>
		</tr>-->
<!-- 	New Ends Here	 -->
	</table>
	</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?php
		if (sizeof($salesOrderItemRecs)) {
	?>
      <tr bgcolor="#f2f2f2" align="center">
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">SR.<br/>NO</th>
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">INDEX<br/>NO</th>
        <th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="50%" nowrap="true">PARTICULARS</th>
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">CASE<br/> PACK</th>	
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">NO. OF<br/> CASES</th>
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">RATE/PER<br/> CASES</th>
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true" width="7%">GRAND</th>
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="4%" nowrap="true">DELIVERY <br/>SCHEDULE<br/> FOR THE <br/>MONTH <br/>CASES</th>
        <th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">PERIOD</th>
	<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true" width="12%">AMOUNT</th>
      </tr>
      <?php
		$numRows = 20; // Setting No.of rows 18/20
		$j = 0;
	
		$decreaseRow = 1;
		if ($sameBillingAddress=='N') $decreaseRow = 3;
		if ($soRemark!="")	      $decreaseRow += 2	;		
		if ($numRows==sizeof($salesOrderItemRecs)) $numRows = $numRows-$decreaseRow;

		$salesOrderRecSize = sizeof($salesOrderItemRecs);

		# Find Balance Rows		
		$balanceRows = ($salesOrderRecSize%$numRows);
		if ($balanceRows<=1 && $balanceRows!=0) $numRows = $numRows-5; 
		
		$totalPage = ceil($salesOrderRecSize/$numRows);
		$totalAmount 	= 0;
		$totalNumMCPack = 0;
		$totalNumLoosePack = 0;
		$totalQuantity	= 0;
		$totalFreePkts = 0;
		$i = 0;
		$resultNumMCArr = array();
		$resultNumLPArr = array();
		$resultQtyArr   = array();
		$resultTotalArr = array();
		$rTotalFreePkts = array();
		foreach ($salesOrderItemRecs as $por) {
			$i++;
			$selProductId	= $por[2];			
			$unitRate	= $por[3];
			$quantity	= $por[4];
			$totalQuantity  += $quantity;
			//$total		= $por[5];			
			$productName	= "";
			$productRec	= $manageProductObj->find($selProductId);
			$productName	= $productRec[2];
			//$totalAmount 	+= $total;
			$mcPakingId	= $por[6];
			
			$indexNo	= $orderDispatchedObj->getProductIdentifier($distributorId, $selProductId);

			# Find MC Packs Details	---------------------------
			$mcpackingRec	= $mcpackingObj->find($mcPakingId);
			$casePack	= $mcpackingRec[2]; 

			$numMCPack	= $por[7];
			$numLoosePack	= $por[8];
			$freePkts 	= $por[13];

			# Find Tax Percent
			$taxPercent	= $salesOrderObj->getDistributorWiseTax($distributorId, $selStateId, $selProductId, mysqlDateFormat($createdDate));
			# Tax Rate
			 $tRate = ($taxPercent)/100;

			# Get Product margin Percent
			$marginPercent = $orderDispatchedObj->getDistMarginPercent($distributorId, $distMgnRateListId, $selProductId, $selStateId, $selCityId, $groupedMgnIds);

			# Product MRP
			$mrp = $salesOrderObj->findProductPrice($selProductId, $productPriceRateListId, $distributorId, $selStateId);
			
			
			$calcMgnCost = number_format(($mrp-(($mrp*$marginPercent)/100)),2,'.','');
			$calcRatePerCases = $calcMgnCost*$casePack;
			$ratePerCases = number_format($calcRatePerCases,2,'.','');

			//echo "$mrp=>$marginPercent==Mcost=$calcMgnCostRate=$ratePerCases<=>";

			#`````````````````````````
			/*
			$avgMgnCost 	= $mrp*(1-($marginPercent/100));
			//$calcCostToDist = $avgMgnCost/(1+$tRate);
			$actualCostToDist = number_format($avgMgnCost,4,'.','');
			$costToDist = number_format($actualCostToDist,2,'.','');
			$calcRatePerCases = $costToDist*$casePack;
			$ratePerCases = number_format($calcRatePerCases,2,'.','');
			echo "<br>After$mrp=>$marginPercen==Mcost=$calcMgnCostRate=$ratePerCases<=><br>";
			*/
			#```````````````````````````````

			$productTotalAmt = $numMCPack*$ratePerCases;
			$totalAmount 	+= $productTotalAmt;


			$totalNumMCPack += $numMCPack;
			$totalNumLoosePack += $numLoosePack;
			$resultNumMCArr[$j] += $numMCPack;
			$resultNumLPArr[$j] += $numLoosePack;
			$resultQtyArr[$j]   += $quantity;
			$resultTotalArr[$j] += $productTotalAmt;			
			$totalFreePkts 	+= $freePkts;
			$totalFreePktsArr[$j] += $freePkts;

			
	?>
      <tr bgcolor="#FFFFFF">
	<td height='20' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;line-height:normal;" align="center"><?=$i?></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true">
		<?=$indexNo?>
	</td>	
 	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true">
		<?=$productName?>
	</td>	
        <td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$casePack?></td>
	<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$numMCPack?></td>
	<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;">
		<?=number_format($ratePerCases,2,'.','');?>
	</td>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
		<?=number_format($productTotalAmt,2,'.','');?>
	</td>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true" align="center" >
		<?=$numMCPack?>
	</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="center">
		<?//dateFormat($deliveryDate);?>
		<?=date( "d/m/y", strtotime($deliveryDate));?>
	</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
		<?=number_format($productTotalAmt,2,'.','');?>
	</td>
      </tr>
	  	<?php
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
			$j++;
		?>
		<tr bgcolor="#FFFFFF">
			<td height="20" colspan="4" nowrap="nowrap" class="listing-head" align="right">Total:</td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumMCArr[$j-1];?></strong></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumLPArr[$j-1]?></strong></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($resultQtyArr[$j-1],0,'','');?></strong></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$totalFreePktsArr[$j-1]?></strong></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right"></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($resultTotalArr[$j-1],2);?></strong></td>
		</tr>
	    </table></td></tr>
<!--  Sign Starts-->
<tr bgcolor="White">
	<TD colspan="17" align="center" >
		<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center" style="border-top-width:0px">
			<tr>	
				<td style="border-bottom-width:0px;border-top-width:0px">
					<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
						<tr><TD height="5"></TD></tr>
						<tr>
							<td align="left">
		<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-left-width:0px;">
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
						<table width="100%" align="right" cellpadding="0" cellspacing="0" class="print" style="border-right-width:0px; border-top-width:0px; border-left-width:0px;border-bottom-width:0px;" >
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
	<!--<tr>
	<td valign="top">
	<table width="98%" cellpadding="3">
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" style="line-height:11px;" align="center">
		<table cellpadding="0" cellspacing="0" width="100%">
			<TR>
				<TD width="35%" class="fieldName" nowrap="true" align="center" style="line-height:normal;">
				</TD>
				<td width="35%" align="center" class="listing-item">
					<?php
						//if ($totalPage>1) echo "(Page $j of $totalPage)";
					?>
				</td>
				<td width="35%" align="right" class="listing-item" style="font-size:8px;" nowrap="true">&nbsp;</td>
			</TR>
		</table>
		
		</td>
	    </tr>
    </table>
	</td>
	</tr>-->
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
	  <table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
				<td height="5"></td>
 	  		</tr>
	   <tr bgcolor="White">
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
							TAX INVOICE
						</span>
						<? }?>
						<?php //if ($invoiceType=='S') {?>
						<!--<br>
						<span style="font-size:9px;">
							(NOT FOR COMMERCIAL PURPOSE)
						</span>-->
						<?php //}?>
						<?php
							if($print==0){
						?>
						<div id="printMsg" class="printSOMsg">(ORIGINAL) - Cont.</div>
						<?php
							 } else if ($print==1) {
						?>
							<div id="printMsg" class="printSOMsg">(DUPLICATE) - Cont.</div>
						<?php 
							} else  {
						?>
							<div id="printMsg" class="printSOMsg">(TRIPLICATE) - Cont.</div>
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
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2' height="5"></td>
	 </tr>
	</table>
	</td>
  </tr>
	<tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center" style="border-bottom-width:0px">
		<tr>
			<TD rowspan="6" width="400px;" style="border-bottom-width:0px"> 
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						To
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
				</table>
			</TD>
			<TD style="padding-left:3px;padding-right:3px;border-bottom-width:0px;" nowrap="true" colspan="4" width="282px;" valign="middle">
				<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder">
					<TR>
						<TD class="csd-print-listing-head"  valign="middle">
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
	<td colspan="17" align="center">
  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<tr bgcolor="#f2f2f2" align="center">
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">SR.<br/>NO</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">INDEX<br/>NO</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="50%" nowrap="true">PARTICULARS</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">CASE<br/> PACK</th>	
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="6%" nowrap="true">NO. OF<br/> CASES</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="6%" nowrap="true">RATE/PER<br/> CASES</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true" width="7%">GRAND</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="6%" nowrap="true">DELIVERY SCHEDULE<br/> FOR THE MONTH <br/>CASES</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" width="8%" nowrap="true">PERIOD</th>
		<th class="csd-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true" width="12%">AMOUNT</th>
      </tr>
   <?php
	#Main Loop ending section 
			
	       }
	}
			# height 
			//$hgt = ( 10 + 8 ) * 20 - ($numRows * 20 ); // Original
			if ($balanceRows>0) $salesOrderRecSize = $balanceRows; 
			$hgt = ($salesOrderRecSize + (-2)) * 7 - ($numRows * 7 );			
			$defaultHgt = 50; // 80
			//echo "$hgt=>($salesOrderRecSize + (-2)) * 15 - ($numRows * 15 )";
   ?>
	<?php
		if ($salesOrderRecSize<$numRows && abs($hgt)>=$defaultHgt) {
	?>
	
	<tr rowspan='8' height='<?=abs($hgt)?>' >
		<td nowrap="nowrap" class="listing-head" align="right">&nbsp;</td>
		<td nowrap="nowrap" class="listing-head" align="right">&nbsp;</td>	
		<td class="listing-head" align="right">
			<?php if ($invoiceType=='S') {?>
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" height="15" style="padding-left:5px;padding-right:5px;">
						(NOT FOR COMMERCIAL PURPOSE)
					</td>
					</tr>
				</table>
			<?php
				}
			?>
			<?php
					// If different address
					if ($sameBillingAddress=='N' && $defaultHgt<abs($hgt)) {
			?>
			<!--<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Delivery Address
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="10" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<?=$deliveryAddress?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
				</table>-->
			<?php
				}
			?>
			<?php
				if ($soRemark!="") {
			?>
			<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Remarks
					</td>
					</tr>
					<tr>
					<td width="100%" valign="top" class="listing-item" style="padding-left:10px;padding-right:10px;font-size:11px;line-height:normal;">
						<?=$soRemark?>
					</td>
					</tr>					
				</table>		
			<? }?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
	</tr>
	<?php
		} // height Ceck Ends ere
	?>	
	<?php	
		if ($totalPage>1) {
	?>
	<tr bgcolor="#FFFFFF">
        <td height="20" colspan="4" nowrap="nowrap" class="listing-head" align="right">Total&nbsp;(<?=$totalPage?>):</td>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumMCArr[$j]?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumLPArr[$j]?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($resultQtyArr[$j],0,'','');?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
		<strong><?=$totalFreePktsArr[$j]?></strong>
	</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right"></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($resultTotalArr[$j],2);?></strong></td>
      </tr>	
	<?
		for ($p=1;$p<=$totalPage-1;$p++) {
	?>
		<tr bgcolor="#FFFFFF">
		<td height="20" colspan="4" nowrap="nowrap" class="listing-head" align="right">Total&nbsp;(<?=$p?>):</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumMCArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumLPArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($resultQtyArr[$p-1],0,'','');?></strong></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<strong><?=$totalFreePktsArr[$p-1]?></strong>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right"></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($resultTotalArr[$p-1],2);?></strong></td>
	</tr>	
	<?php
		}  // Total Loop
	?>
	<?php
		} // Balance Row check
	?>
      <tr bgcolor="#FFFFFF">
        <td height="20" colspan="4" nowrap="nowrap" class="listing-head" align="right">
		<? if ($totalPage>1) echo "Gross&nbsp;";?> Total:
	</td>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$totalNumMCPack?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$totalNumLoosePack?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?//number_format($totalQuantity,0,'','');?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$totalFreePkts?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right"></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr>
	<?php
		$calcDiscountAmt = 0;
		if ($discount=='Y') {

			$calcDiscountAmt = number_format((($totalAmount*$discountPercent)/100),2,'.','');
			$totalAmount = $totalAmount-$calcDiscountAmt;
	?>
	<tr bgcolor="white">		
		<!--<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;" colspan="7"><?=$discountRemark?></td>-->
        	<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;line-height:normal;" align="right" nowrap="true" colspan="9">Less: &nbsp;DISCOUNT&nbsp;<?=$discountPercent;?>%</td>
                <td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;"><strong><?=$calcDiscountAmt?></strong></td>
	</tr>
	<tr bgcolor="white">		
		<!--<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;" colspan="7">&nbsp;</td>-->
        	<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;line-height:normal;" align="right" nowrap="true" colspan="9">Total</td>
                <td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;"><strong><?=number_format($totalAmount,2,'.',',')?></strong></td>
	</tr>
	<?php
		}
	?>
<!-- 	Discount Splitup -->
	<?php
		$balanceTotalAmt =0; 
		if (sizeof($discountSplitupRecs)>0) {
			$calcTotalDiscountAmt = 0;

			$basicTotal = $totalAmount;	
			foreach ($discountSplitupRecs as $dsr) {
				$marginStructId = $dsr[0];
				//$marginHead	= $dsr[1];
				$marginHead	= $dsr[3]; // display Name
				$pcalcType	= $dsr[2];  // Markup/ Mark doun				

				# Get Product margin Percent
				$discountMarginPercent = $orderDispatchedObj->getDistMgnPercent($distributorId, $distMgnRateListId, '', $selStateId, $selCityId, $marginStructId);

				$discountMarginAmt = number_format((($basicTotal*$discountMarginPercent)/100),2,'.','');

				
				if ($pcalcType=='MU') {
					$displayCalcType = "Add";
					$calcTotalDiscountAmt = $basicTotal+$discountMarginAmt;
				} else {
					$displayCalcType = "Less";
					$calcTotalDiscountAmt = $basicTotal-$discountMarginAmt;
				}

				//$balanceTotalAmt += $calcTotalDiscountAmt;

				
	?>
	<tr bgcolor="white">		
		<!--<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;" colspan="7">&nbsp;</td>-->
        	<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px; line-height:normal;" align="right" nowrap="true"  colspan="9">
			<?=$displayCalcType?>:&nbsp;<?=$marginHead?> &nbsp;<?=$discountMarginPercent;?>%
		</td>
                <td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;"><strong><?=$discountMarginAmt?></strong></td>
	</tr>
	<?php
				$basicTotal = $calcTotalDiscountAmt;
		 	} //Loop ends here
			//echo "H=".$basicTotal;
			if (sizeof($discountSplitupRecs)>0) $totalAmount = $basicTotal;
		} //Size check ends here
	?>
	<?php
	
	if (sizeof($taxApplied)>0) {
		$totalTaxAmt = 0;	
		for ($j=0;$j<sizeof($taxApplied);$j++) {
			$selTax = explode(":",$taxApplied[$j]); // Tax Percent:Amt
			$taxPercent = $selTax[0];
			$calcTaxAmt = number_format((($totalAmount*$taxPercent)/100),2,'.','');

			$totalTaxAmt += $calcTaxAmt;			
	?>
	<!--<tr bgcolor="#FFFFFF">
		<td height="20" colspan="8" nowrap="nowrap" class="listing-head" align="right"></td>
		<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;" align="right" nowrap="true">Add:&nbsp;<?=$taxType?> <?=$selTax[0]?>%</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$selTax[1];?></strong></td>
      </tr>-->
	<tr bgcolor="#FFFFFF">
		<!--<td height="20" colspan="7" nowrap="nowrap" class="listing-head" align="right"></td>-->
		<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;" align="right" nowrap="true" colspan="9">Add:&nbsp;<?=$taxType?> <?=$selTax[0]?>%</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$calcTaxAmt;?></strong></td>
      </tr>
	<?php
		}	// For Loop Ends Here	
		/*
		if (sizeof($discountSplitupRecs)>0) $balanceTotalAmt += $totalTaxAmt;
		else $balanceTotalAmt = $totalAmount+$totalTaxAmt;
		*/
		 $balanceTotalAmt = $totalAmount+$totalTaxAmt;
	} // Tax Size Check Ends Here
	?>	
	<tr>
		<!--<td height='25' colspan="7" nowrap="nowrap"  align="right">-->
			<!--<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
				<tr>
					<TD class="csd-print-listing-head" height="20" style="padding-left:5px;padding-right:5px;">NET WT:&nbsp;<span class="listing-item"><strong><?=$soNetWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;">GR. WT:&nbsp;<span class="listing-item"><strong><?=$soGrossWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;">NO.OF BOXES:&nbsp;<span class="listing-item"><strong><?=$soTNumBox;?></strong></span></TD>
				</tr>
			</table>-->
		<!--</td>-->
		<td height='25' class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;" align="right" colspan="9">Round</td>
		<td height='25' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?php 
				$roundVal = $salesOrderObj->getRoundoffVal(number_format($balanceTotalAmt,2,'.',''));
				$grandTotalAmt = $balanceTotalAmt+$roundVal;
			?> 
			<strong><?=number_format($roundVal,2,'.','');?></strong>
		</td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<!--<td height='25' colspan="7" nowrap="nowrap" align="left">-->
			<!--<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
				<tr>
					<TD class="csd-print-listing-head" height='20' colspan="3" style="padding-left:5px;padding-right:5px;border-left-width:0px;">GR. TOTAL:&nbsp;
						
						
					</TD>
				</tr>
			</table>-->
		<!--</td>-->
		<td height='25' class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;" align="right" colspan="9" nowrap="true"><strong>Net Amount Payable</strong></td>
		<td height='25' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?php echo number_format($grandTotalAmt,2);?></strong></td>
      </tr>
    </table></td>
  </tr>	
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr>
	<? }?>
  <tr bgcolor="White">
	<TD colspan="17" align="center" >
		<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center" style="border-top-width:0px">
			<tr><TD style="border-bottom-width:0px;border-top-width:0px">
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
				<tr>
					<TD class="csd-print-listing-head" height='20' colspan="3" style="padding-left:5px;padding-right:5px;border-left-width:0px;">GR. TOTAL:&nbsp;
						<?php 
							$input = round($grandTotalAmt);
							echo "Rs.".convert($input)."Only";
						?> 
						
					</TD>
				</tr>
			</table>
			</TD></tr>
			<tr><TD style="border-bottom-width:0px;border-top-width:0px">
				<table cellspacing='0' cellpadding='0' width="75%" class="tdBoarder">
				<tr>
					<TD class="csd-print-listing-head" height="20" style="padding-left:5px;padding-right:5px;" nowrap>NET WT:&nbsp;<span class="listing-item"><strong><?=$soNetWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;" nowrap>GR. WT:&nbsp;<span class="listing-item"><strong><?=$soGrossWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;" nowrap>NO.OF BOXES:&nbsp;<span class="listing-item"><strong><?=$soTNumBox;?></strong></span></TD>
				</tr>
			</table>
			</TD>
			</tr>
			<tr><TD style="border-bottom-width:0px;border-top-width:0px">
				<table cellspacing='0' cellpadding='0' width="75%" class="tdBoarder">
				<tr><TD height="15"></TD></tr>
				<tr>
					<td class="listing-item" nowrap style="font-size:9px;">
						NOTE:DELIVERY SCHEDULE PROPONED/POSTPONED VIDE CSD DEPOT LETTER NO ............ DT. ............ FOR ...........
					</td>
				</tr>
				</table>
			</TD>
			</tr>
			<tr><TD style="border-bottom-width:0px;border-top-width:0px">
					<table cellpadding="0" cellspacing="0" class="tdBoarder">
						<TR>
							<TD class="csd-print-listing-head" style="padding-left:5px;padding-right:2px;font-size:7px;line-height:11px;" nowrap="true" valign="middle"><strong>VAT TIN:</strong></TD>
							<td class="listing-item" style="font-size:8px; line-height:11px;" nowrap="true" valign="middle"><strong><?=$vatTin?></strong></td>	
						</TR>
						<TR>
							<td class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;font-size:7px; line-height:11px;" nowrap="true" valign="middle"><strong>CST TIN:</strong></td>
							<td class="listing-item" style="font-size:8px; line-height:11px;" nowrap="true" valign="middle"><strong><?=$cstTin?></strong></td>
						</TR>
					</table>
				</TD>
			</tr>
			<tr>	
				<td style="border-bottom-width:0px;border-top-width:0px">
		<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
		<!--<tr><TD height="5"></TD></tr>-->		
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<?php
					// If different address
					if ($sameBillingAddress=='N' && abs($hgt)<$defaultHgt) {
				?>
				<tr>
					<TD valign="top">
						<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-left-width:0px;">		
							<tr>
								<TD >
								<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Delivery Address
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="10" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<?=$deliveryAddress?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" height="10" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
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
	<!-- 	Remarks	 -->
				<?php
					if ($soRemark!="" && abs($hgt)<$defaultHgt) {
				?>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD valign="top">
						<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-left-width:0px;">		
							<tr>
								<TD >
								<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="csd-print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Remarks
					</td>
					</tr>
					<tr>
					<td width="100%" valign="top" class="listing-item" style="padding-left:10px;padding-right:10px;font-size:11px;line-height:normal;">
						<?=$soRemark?>
					</td>
					</tr>					
				</table>
								</TD>		
							</tr>
						</table>
					</TD>
				</tr>
				<?
					} // Remarks Ends Here
				?>
				<TR>
					<TD valign="top"></td>
					<td rowspan="5" valign="bottom" style="line-height:100px;">
						<table width="100%" align="right" cellpadding="0" cellspacing="0" class="print" style="border-right-width:0px; border-top-width:0px; border-left-width:0px;border-bottom-width:0px;" >
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
		<!--<tr><TD height="5">sadsadsad d sd s353453</TD></tr>-->
		<tr>
			<td valign="top">
<!--  style="border-left-width:0px;border-top-width:0px;border-right-width:0px;border-bottom-width:0px;"-->
			<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC" class="print" style="border-left-width:0px;">	
			<tr>
				<TD class="listing-item" style="padding-left:3px;padding-right:5px;" >	
					<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="listing-item" style="font-size:8px;line-height:11px;">
								I/We hereby certify that my/our registration certificate under the Maharashtra Value Added Act, 2002 is in the force on the date on which the sale of the goods specified in this tax invoice is made by us and that the transaction of sale covered by this tax invoice has been effected by us & it shall be accounted for in the turnover of sales while filing of return and the due tax if any, payable on the sale has been paid or shall be paid.
							</TD>
						</TR>
					</table>
				</TD>
			</tr>
			<!--<tr>
				<TD style="line-height:11px;" align="left">
					<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="csd-print-listing-head" style="padding-left:5px;padding-right:2px;font-size:7px;line-height:11px;" nowrap="true" valign="middle">VAT TIN:</TD>
							<td class="listing-item" style="font-size:8px; line-height:11px;" nowrap="true" valign="middle"><?=$vatTin?></td>	
						</TR>
						<TR>
							<td class="csd-print-listing-head" style="padding-left:5px;padding-right:5px;font-size:7px; line-height:11px;" nowrap="true" valign="middle">CST TIN:</td>
							<td class="listing-item" style="font-size:8px; line-height:11px;" nowrap="true" valign="middle"><?=$cstTin?></td>
						</TR>
					</table>
				</TD>
			</tr>-->
			<!--<tr>
				<td align="left" class="listing-item" style="font-size:8px;padding-left:3px;padding-right:5px;" nowrap="true">
					This Invoice is issued subject to Mumbai Jurisdiction.
				</td>
			</tr>-->
			<!--<tr><TD height="40"></TD></tr>-->
			<!--<tr>
	    <td valign="top" nowrap="nowrap" style="line-height:11px;" align="center">
		<table cellpadding="0" cellspacing="0" width="100%">
			<TR>
				<TD width="50%" class="listing-item" nowrap="true" align="left" style="line-height:normal;padding-left:5px;">
					<?php
						if ($totalPage>1) echo "(Page $totalPage of $totalPage)";
					?>
				</TD>
				<td width="50%" align="center" class="fieldName" style="line-height:normal;">	
					(Receiver 's Sign)
				</td>
			</TR>
		</table>
		
		</td>
	    </tr>-->
			</table>
			</td>
		</tr>
		<tr>
			<td valign="top">
			<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC" class="print" style="border-left-width:0px;border-top-width:0px;border-right-width:0px;border-bottom-width:0px;">	
			<tr><TD height="20"></TD></tr>
			<tr>
				<TD class="listing-item" style="padding-left:3px;padding-right:5px;" >	
				<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<td width="50%" align="left" class="listing-item" style="line-height:normal;font-size:9px;">	
						"OCTROI EXEMPTION CERTIFICATE ATTACHED"
					</td>
				</TR>
				<TR>
					<td width="50%" align="left" class="listing-item" style="line-height:normal;font-size:9px;">	
						(OEC NO. <?=$oecNo?> DT <?=$oecIssuedDate?> VALID UPTO <?=$oecValidDate?>, NO.OF PACKAGES <?=$deliveryMC?>)
					</td>
				</TR>
			</table>
		</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td valign="top">
			<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC" class="print" style="border-left-width:0px;border-top-width:0px;border-right-width:0px;border-bottom-width:0px;">	
			<tr><TD height="40"></TD></tr>
			<tr>
				<TD class="listing-item" style="padding-left:3px;padding-right:5px;" >	
<table cellpadding="0" cellspacing="0" width="100%">
			<TR>
				<TD width="50%" class="listing-item" nowrap="true" align="left" style="line-height:normal;padding-left:5px;">
					<?php
						if ($totalPage>1) echo "(Page $totalPage of $totalPage)";
					?>
				</TD>
				<td width="50%" align="center" class="fieldName" style="line-height:normal;">	
					(Receiver 's Sign)
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
	<td valign="top">
	<table width="98%" cellpadding="3">
	<?php
		if ($sameBillingAddress=='Y' || $soRemark=="") {
	?>	
      <tr>
        <td colspan="6" height="5"></td>
        </tr>
	<? }?>
	  <!--<tr>
	    <td colspan="6" valign="top" nowrap="nowrap" style="line-height:11px;" align="center">
		<table cellpadding="0" cellspacing="0" width="100%">
			<TR>
				<TD width="35%" class="listing-item" nowrap="true" align="left" style="line-height:normal;padding-left:5px;">
					<?php
						if ($totalPage>1) echo "(Page $totalPage of $totalPage)";
					?>
				</TD>
				<td width="35%" align="center" class="fieldName" style="line-height:normal;">	
					(Receiver 's Sign)
				</td>
				<td width="35%" align="right" class="listing-item" style="font-size:8px;" nowrap="true">&nbsp;</td>
			</TR>
		</table>
		
		</td>
	    </tr>-->
    </table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</TD>
  </tr>	
 <tr bgcolor="White"><TD height="5"></TD></tr>  
</TD>
	</tr>
  </table>
</td>
</tr>
</table>
</form>
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body>
</html>
<?php
	} // Num copy Ends here
?>