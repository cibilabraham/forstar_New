<?php	
	require("include/include.php");

	# Get Sales Order Id
	$selSOId = $g["selSOId"];
	$print	 = $g["print"];	
	$confirmed = false;
	$printMode = false;
	if ($print=='Y') $printMode = true;
	
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
	$gstinNo			= stripSlash($distributorRec[17]);	

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

	#  Number of Copy
	if ($invoiceConfirmed=='C' && $printMode) {
		$numCopy	= 3;
		$confirmed = true;
	} else {
		$numCopy	= 1;
	}	

// policies
	list($certifiedAgreementTxt, $termsNConditionTxt, $eOETxt) = $salesOrderObj->getInvoicePolicies();

	//$numCopy = 1;
?>
<html>
<head>
<title>SALES ORDER MEMO</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	background-image: url('images/watermark.png');	
	background-repeat: no-repeat;
	background-attachment:fixed;
	background-position: 50% 50%;	
}
</style> 
<script language="javascript" type="text/javascript">
var key1="119";
var x='';
function handler(e)
{
    var code;
    if (!e) var e = window.event;
    if (e.keyCode) code = e.keyCode;
    else if (e.which) code = e.which;		
    //alert(code);
    if (code=="112") {
		alert("Access denied for printing!!!");
		return false;		
    }
}
if (!document.all){
	window.captureEvents(Event.KEYPRESS);
	window.onkeypress=handler;
} else {
	document.onkeypress = handler;
}
 </script>
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
<?php
if ($printMode) {
?>
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>
<?php
}	
?>
<?php
	# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>
<table width='95%' cellspacing='1' cellpadding='1'  align='center'>
<tr>
	<td STYLE="border-top: 1px solid #f2f2f2; border-left: 1px solid #f2f2f2; border-right: 1px solid #f2f2f2; border-bottom: 1px solid #f2f2f2;">
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
	<tr><TD height="5"></TD></tr>
	<tr>
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
							<tr>
								<TD align="center">
								<table cellpadding="0" cellspacing="0">
									<TR>
										<TD class="ex-print-listing-head" style="font-size:8px;line-height:11px;" nowrap="nowrap" valign="middle">VAT NO:&nbsp;</TD>
										<td class="listing-item" style="font-size:9px; line-height:11px;" nowrap="nowrap" valign="middle"><?=$vatTin?></td>	
									</TR>								
								</table>
								</TD>
							</tr>
							<tr>
								<TD align="center">
								<table cellpadding="0" cellspacing="0">	
									<TR>
										<td class="ex-print-listing-head" style="font-size:8px; line-height:11px;" nowrap="nowrap" valign="middle">CST NO:&nbsp;</td>
										<td class="listing-item" style="font-size:9px; line-height:11px;" nowrap="nowrap" valign="middle"><?=$cstTin?></td>
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
 <tr>
	<td height="2"></td>
 </tr>
  <tr>
    <td colspan="17" align="RIGHT"></td>
  </tr>
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
	<td align="LEFT" valign="top" width='99%' style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000">
	<table width='99%' cellpadding='0' cellspacing='0' align="center">
		<tr>
			<TD width="45%" style="border-right: 1px solid #000000;">
				<table cellspacing='0' cellpadding='0' width="100%">
					<tr>
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
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
								<td class="print-listing-head" style="padding-left:5px;padding-right:5px;">CST No:</td>
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
								<td class="print-listing-head" style="padding-left:5px;padding-right:5px;">TIN No:</td>
								<td class="listing-item-print" style="padding-left:5px;padding-right:5px;font-size:11px;"><?=$tinNo?></td>
							</tr>
						</table>
					</TD></tr>
					<tr><TD colspan="2">
						<table>
							<tr>
								<td class="print-listing-head" style="padding-left:5px;padding-right:5px;">GSTIN No:</td>
								<td class="listing-item-print" style="padding-left:5px;padding-right:5px;font-size:11px;"><?=$gstinNo?></td>
							</tr>
						</table>
					</TD></tr>
					<tr><TD height="2"></TD></tr>
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
									<TD class="print-listing-head"  valign="middle">
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
									<TD class="print-listing-head"  valign="middle" nowrap="true" align="right" width="25%">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
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
									<TD class="print-listing-head"  valign="middle">
										PO NO. :
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
									<TD class="print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										DTD :
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
									<TD class="print-listing-head"  valign="middle" nowrap="true">
										D.CHALLAN NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$gatePassNo//$challanNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
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
				<tr>
				<td style="border-bottom: 1px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" >
					<TR>
						<TD class="print-listing-head">
							DESTINATION :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?php echo $cityName.",&nbsp;".$stateName;?>
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
						<TD class="print-listing-head">
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
	<?php
		if (sizeof($salesOrderItemRecs)) {
	?>
      <tr bgcolor="#f2f2f2" align="center">
	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="5%" nowrap="true">SR.<br/>NO</td>
        <td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="50%" nowrap="true">DESCRIPTION OF GOODS</td>
	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">M/C</td>
	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">IND<br/> PKTS</td>
	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true" width="7%">TOTAL<br/> PKTS</td>
	<? if ($invoiceType=='T') {?>
	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">FREE<br/>PKTS</td>
	<? }?>
        <td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="8%" nowrap="true">RATE PER <br/>UNIT (RS.)</td>
	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-bottom: 1px solid #000000;" nowrap="true" width="12%">TOTAL<br/>(RS.)</td>
      </tr>
      <?php
		$defaultNumRows = 20;
		$numRows = 20; // Setting No.of rows 18/20
		$j = 0;
	
		$decreaseRow = 1;
		if ($sameBillingAddress=='N') $decreaseRow = 3;
		if ($soRemark!="")	      $decreaseRow += 2	;		
		if ($numRows==sizeof($salesOrderItemRecs)) $numRows = $numRows-$decreaseRow;

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
	?>
      <tr>
	<td height='20' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="center"><?=$i?></td>
 	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true">
		<?=$productName?>
	</td>	
        <td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><?=$numMCPack?></td>
	<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><?=$numLoosePack?></td>
	<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><?=number_format($quantity,0,'.','');?></td>
	<? if ($invoiceType=='T') {?>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><?=$freePkts?></td>
	<? }?>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true" align="right" >
		<?php if ($invoiceType=='T') {?><?=number_format($unitRate,2,'.','');?><?} else {?>0 <?}?>
	</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;" align="right">
		<?=$total?>
	</td>
      </tr>
	  	<?php
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
			$j++;
		?>
		<tr>
			<td height="20" colspan="2" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000;">Total:</td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong><?=$resultNumMCArr[$j-1];?></strong></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong><?=$resultNumLPArr[$j-1]?></strong></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong><?=number_format($resultQtyArr[$j-1],0,'','');?></strong></td>
			<? if ($invoiceType=='T') {?>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000;" align="right"><strong><?=$totalFreePktsArr[$j-1]?></strong></td>
			<? }?>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;border-right: 1px solid #000000;" align="right">&nbsp;</td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($resultTotalArr[$j-1],2);?></strong></td>
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
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
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
			<TD style="padding-left:3px;padding-right:3px;" nowrap="true" colspan="4" width="282px;" valign="middle">
				<table cellpadding="0" cellspacing="0" border="0" >
					<TR>
						<TD class="print-listing-head"  valign="middle">
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
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="5%" nowrap="true">SR.<br/>NO</td>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="50%" nowrap="true">DESCRIPTION OF GOODS</td>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">M/C</td>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">IND<br/> PKTS</td>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="true" width="7%">TOTAL<br/> PKTS</td>
		<? if ($invoiceType=='T') {?>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="true">FREE<br/>PKTS</td>
		<? }?>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="8%" nowrap="true">RATE PER <br/>UNIT (RS.)</td>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-bottom: 1px solid #000000;" nowrap="true" width="12%">TOTAL<br/>(RS.)</td>
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
   ?>
	<?php
		if ($salesOrderRecSize<$numRows && abs($hgt)>=$defaultHgt) {
	?>
	<tr rowspan='8' height='<?=abs($hgt)?>' >
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>	
		<td class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
			<?php if ($invoiceType=='S') {?>
				<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="print-listing-head" height="15" style="padding-left:5px;padding-right:5px;">
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
			<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Delivery Address
					</td>
					</tr>
					<!--<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="5" style="padding-left:10px;padding-right:10px;font-size:11px; line-height:normal;">
						&nbsp;
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>-->
					<tr><TD height="5"></TD></tr>
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
				}
			?>
			<?php
				if ($soRemark!="") {
			?>
			<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
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
			<?php if ($invoiceType=='T' && $sameBillingAddress=='Y') {?>
				&nbsp;
			<? }?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<? if ($invoiceType=='T') {?>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<? }?>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	</tr>
	<?php
		} // height Ceck Ends ere
	?>	
	<?php	
		if ($totalPage>1) {
	?>
	<tr>
        <td height="20" colspan="2" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">Total&nbsp;(<?=$totalPage?>):</td>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
			<strong><?=($resultNumMCArr[$j])?$resultNumMCArr[$j]:"&nbsp;";?></strong>
	</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<strong><?=($resultNumLPArr[$j])?$resultNumLPArr[$j]:"&nbsp;";?></strong>
	</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<strong><?=($resultQtyArr[$j])?number_format($resultQtyArr[$j],0,'',''):"&nbsp;";?></strong>
	</td>
	<? if ($invoiceType=='T') {?>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<strong><?=($totalFreePktsArr[$j])?$totalFreePktsArr[$j]:"&nbsp;";?></strong>
	</td>
	<? }?>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;" align="right"><strong><? echo number_format($resultTotalArr[$j],2);?></strong></td>
      </tr>	
	<?
		for ($p=1;$p<=$totalPage-1;$p++) {
	?>
		<tr>
		<td height="20" colspan="2" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">Total&nbsp;(<?=$p?>):</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=$resultNumMCArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=$resultNumLPArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=number_format($resultQtyArr[$p-1],0,'','');?></strong></td>
		<? if ($invoiceType=='T') {?>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
			<strong><?=$totalFreePktsArr[$p-1];?></strong>
		</td>
		<? }?>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;" align="right">
			<strong><? echo number_format($resultTotalArr[$p-1],2);?></strong>
		</td>
	</tr>	
	<?php
		}  // Total Loop
	?>
	<?php
		} // Balance Row check
	?>
      <tr>
        <td height="20" colspan="2" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
		<? if ($totalPage>1) echo "Gross&nbsp;";?> Total:
	</td>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=$totalNumMCPack?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=$totalNumLoosePack?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=number_format($totalQuantity,0,'','');?></strong></td>
	<? if ($invoiceType=='T') {?>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right"><strong><?=$totalFreePkts?></strong></td>
	<? }?>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr>
	<?php
		if ($discount=='Y') {
	?>
	<tr bgcolor="white">		
		<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" colspan="6"><?=$discountRemark?></td>
        	<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true">(Less) <br/>DISCOUNT&nbsp;<?=$discountPercent;?>%</td>
                <td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;"><strong><?=$discountAmt?></strong></td>
	</tr>
	<?php
		}
	?>
	<?php
	if (sizeof($taxApplied)>0 && $invoiceType=='T') {	
		for ($j=0;$j<sizeof($taxApplied);$j++) {
			$selTax = explode(":",$taxApplied[$j]); // Tax Percent:Amt
	?>
	<tr>
		<td height="20" colspan="6" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="true">Add:&nbsp;<?=$taxType?> <?=$selTax[0]?>%</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;" align="right"><strong><?=$selTax[1];?></strong></td>
      </tr>
	<?php
		}	// For Loop Ends Here
	} // Tax Size Check Ends Here

		if ($invoiceType!='T') $btmColSpan = 5;
		else $btmColSpan = 6;
	?>	
	<tr>
		<td height='25' colspan="<?=$btmColSpan?>" nowrap="nowrap"  align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
			<table cellspacing='0' cellpadding='0' width="100%" >
				<tr>
					<TD class="print-listing-head" height="20" style="padding-left:5px;padding-right:5px;">NET WT:&nbsp;<span class="listing-item"><strong><?=$soNetWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="print-listing-head" style="padding-left:5px;padding-right:5px;">GR. WT:&nbsp;<span class="listing-item"><strong><?=$soGrossWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="print-listing-head" style="padding-left:5px;padding-right:5px;">NO.OF BOXES:&nbsp;<span class="listing-item"><strong><?=$soTNumBox;?></strong></span></TD>
				</tr>
			</table>
		</td>
		<td height='25' class="listing-head" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">Round</td>
		<td height='25' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-bottom: 1px solid #000000;" align="right"><strong><?=($invoiceType=='T')?$roundVal:0;?></strong></td>
	</tr>
	<tr>
		<td height='25' colspan="<?=$btmColSpan?>" nowrap="nowrap" align="left" style="border-right: 1px solid #000000;">
			<table cellspacing='0' cellpadding='0' width="100%" >
				<tr>
					<TD class="print-listing-head" height='20' colspan="3" style="padding-left:5px;padding-right:5px;">GR. TOTAL:&nbsp;
						<?php 
							$input = round($grandTotalAmt);
							echo "Rs.".convert($input)."Only";
						?> 
						
					</TD>
				</tr>
			</table>
		</td>
		<td height='25' class="listing-head" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000;" align="right">Gr. Total</td>
		<td height='25' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($grandTotalAmt,2);?></strong></td>
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
					<td nowrap="nowrap" class="print-listing-head" height="15" style="padding-left:5px;padding-right:5px;">
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
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						Delivery Address
					</td>
					</tr>
					<tr><TD height="5"></TD></tr>
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
						<table width="99%" cellpadding="2" cellspacing="0">		
							<tr>
								<TD >
								<table cellspacing='0' cellpadding='0' width="100%" >
					<tr>
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
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
				<tr>
			<td>
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr>
						<td width="60%" style="border-right: 1px solid #000000;" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%" border="0">
								<tr>
									<td style="border-bottom: 1px solid #000000;">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
											<tr>
												<td class="listing-item" style="font-size:8px;line-height:11px; padding:5px 2px; text-align: justify;">
													<b><u>Terms & Conditions:</u></b>&nbsp;<?=$termsNConditionTxt?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="border-bottom: 0px solid #000000;">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
											<tr>
												<td class="listing-item" style="font-size:8px;line-height:11px; padding:5px 2px; text-align: justify;">
													<!--b><u>E. & O.E.</u></b--><?=$eOETxt?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<?php
									if ($totalPage>1) {
								?>
								<tr>
									<td class="listing-item" style="font-size: 8px;line-height: 3px;padding-bottom: 5px;padding-left: 5px;" valign="bottom" align="left">
										<?php
											echo "(Page $totalPage of $totalPage)";
										?>
									</td>
								</tr>
								<?php
									}			
								?>
							</table>
						</td>
						<td width="23%" style="border-right: 1px solid #000000; padding:0 2px;" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%" height="205px">
								<TR>
									<TD class="listing-item" style="font-size:8px;line-height:normal; padding:5px 2px;" align="center" valign="top">Received the above goods in good condition</TD>
								</TR>
								<tr><TD height="95px">&nbsp;</TD></tr>
								<TR>
									<TD class="listing-item" style="font-size:8px;line-height:11px;" valign="bottom" align="center">									
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td class="listing-item" style="font-size:8px; line-height:normal;" valign="bottom" align="center">(Receiver's Signature and Stamp)</td>
											</tr>											
										</table>
									</TD>
								</TR>
							</table>
						</td>
						<td width="20%" style="padding:0 2px;" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%" border="0">
								<tr>
									<td style="border-bottom: 1px solid #000000;">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
											<tr>
												<td class="listing-item" style="font-size:8px;line-height:11px; padding:5px 2px; text-align: justify;">
													<?=$certifiedAgreementTxt?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="border-bottom: 0px solid #000000;">
										<table width="100%" align="center" cellpadding="0" cellspacing="0" height="125px">
											<tr>
												<td class="listing-item" nowrap align="center" valign="top">
													<table cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td class="listing-item" nowrap align="center" style="padding:5px 2px; font-size:12px;">
																For <strong><?=$forstinsfoods["fifoods"];?></strong>	
															</td>
														</tr>
														<tr>
															<td class="listing-item" nowrap align="center" style="font-size:8px; line-height:normal;">
																<strong>
																	<?=$divfrfoods["frfoods"];?>
																</strong>
															</td>
														</tr>
													</table>																			
												</td>
											</tr>
											<tr><TD height="45px">&nbsp;</TD></tr>
											<tr>
												<TD valign="bottom" class="listing-item" nowrap align="center">
													(Authorised Signatory)
												</TD>
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
				<!--TR>
					<TD valign="top"></td>
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
				</tr-->
		<!--tr>
			<td valign="top">
			<table width="99%" cellpadding="2" cellspacing="0">	
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
			<tr>
				<TD style="line-height:11px;" align="left">
					<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="print-listing-head" style="padding-left:5px;padding-right:2px;font-size:7px;line-height:11px;" nowrap="true" valign="middle">VAT TIN:</TD>
							<td class="listing-item" style="font-size:8px; line-height:11px;" nowrap="true" valign="middle"><?=$vatTin?></td>	
						</TR>
						<TR>
							<td class="print-listing-head" style="padding-left:5px;padding-right:5px;font-size:7px; line-height:11px;" nowrap="true" valign="middle">CST TIN:</td>
							<td class="listing-item" style="font-size:8px; line-height:11px;" nowrap="true" valign="middle"><?=$cstTin?></td>
						</TR>
					</table>
				</TD>
			</tr>
			<tr>
				<td align="left" class="listing-item" style="font-size:8px;padding-left:3px;padding-right:5px;" nowrap="true">
					This Invoice is issued subject to Mumbai Jurisdiction.
				</td>
			</tr>
			<tr><TD height="40"></TD></tr>
			<tr>
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
	    </tr>
			</table>
			</td>
		</tr-->
			</table>
		</td>
	</tr>
	<tr>
	<td valign="top">
	<table width="98%" cellpadding="3">
	<?php
		if ($sameBillingAddress=='Y' || $soRemark=="") {
	?>	
      	<!--tr>
        	<td colspan="6" height="5"></td>
        </tr-->
	<? }?>
    </table>
	</td>
		</tr>
	</table>
		</td>
			</tr>
		</table>
	</TD>
  </tr>	
 <!--<tr bgcolor="White"><TD height="5"></TD></tr>  -->
<!--</TD>-->
	<!--</tr>-->
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
<!-- <tr>
	<td height="2"></td>
 </tr>-->
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
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						NAME AND ADDRESS
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
									<TD class="print-listing-head"  valign="middle" nowrap="true">
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
									<TD class="print-listing-head"  valign="middle" nowrap="true" align="right">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left">
										<?=dateFormat($gpassDate)//$createdDate?>
									</td>
									<TD class="print-listing-head"  valign="middle" nowrap="true" align="right">
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
						<TD width="20%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="print-listing-head"  valign="middle" nowrap="true">
										INVOICE NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$pOGenerateId?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="68%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="print-listing-head"  valign="middle" nowrap="true" align="right">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left">
										<?=$createdDate?>
									</td>
									<TD class="print-listing-head"  valign="middle" nowrap="true" align="right">
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
						<TD class="print-listing-head">
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
						<TD class="print-listing-head">
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
						<TD class="print-listing-head">
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
     <!-- <tr bgcolor="#f2f2f2" align="center">	
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="50%" nowrap="true">DETAILS OF THE CONSIGNMENT</td>
		<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-bottom: 1px solid #000000;" width="6%" nowrap="true">TOTAL NO OF BOXES</td>	
      </tr>
      <tr>	
 	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt; border-right: 1px solid #000000;" nowrap="true" height="100">
		&nbsp;
	</td>	
        <td class="listing-item" align="center" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$soTNumBox?></td>	
      </tr>-->	 
      <tr>	
 	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000; border-bottom: 0px solid #000000;" width="40%" nowrap="true">
		DETAILS OF THE CONSIGNMENT
	</td>			
        <td class="listing-item" align="left" style="padding-left:10px; padding-right:3px; font-size:8pt; border-bottom: 0px solid #000000;" height="50"><?=$consignmentDetails?></td>	
      </tr>
	 <!--<tr>	
 	<td class="p-listing-head" style="padding-left:3px; padding-right:3px; border-right: 1px solid #000000;" width="40%" nowrap="true">
		TOTAL NO OF BOXES
	</td>
        <td class="listing-item" align="left" style="padding-left:10px; padding-right:3px; font-size:8pt;"><b><?=$soTNumBox?></b></td>	
      </tr>-->
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