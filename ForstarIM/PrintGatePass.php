<?php	
	require("include/include.php");

	# Get Sales Order Id
	$selSOId = $g["selSOId"];
	$gatePassId = $g["gatePassId"];
	$companyId = $g["companyId"]; /*KD*/
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
	if ($selSOId) $pOGenerateId	= ($invoiceType!="S")?(($pOGenerateId!=0)?$pOGenerateId:"P$proformaInvoiceNo"):"S$sampleInvNo";
	$gpassConfirmed = $sORec[46];	
	list($gatePassNo, $partyAddress, $consignmentDetails, $vehicleNo, $gpConfirm, $gpassDate) = $manageGatePassObj->getGatePassRec($selSOId, $gatePassId);

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

	#  Number of Copy
	if ($invoiceConfirmed=='C' || ($selSOId=="" && $gpConfirm)) {
		$numCopy	= 3;
		$confirmed = true;
	} else {
		$numCopy	= 1;
	}	

	//$numCopy = 1;
?>
<html>
<head>
<title>GATE PASS</title>
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
<form name="frmPrintGatePass" id="frmPrintGatePass">
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>
<?php
	# Gate Pass Section Starts Here
	if (($gpassConfirmed=="Y" && $invoiceConfirmed=='C') || $gatePassId) {

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
											<!-- KD Added-->
											 <?
											 if($companyId==15)
											 	{
											 		$forstinsfoods["fifoods"]=$forstinsfoods1["fifoods1"];
											 		$divfrfoods["frfoods"]="";
											 	}
											 	else{
											 		$forstinsfoods["fifoods"]=$forstinsfoods["fifoods"];
											 		$divfrfoods["frfoods"]=$divfrfoods["frfoods"];
											 	} 
											 	?> <!-- KD Added Ends-->
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
								<TD class="print-SOTHead-item" nowrap="true"><?=$addr["ADR2"];?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item" nowrap="true"><?=$addr["ADR3"];?>&nbsp;<?=$addr["ADR4"];?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item" nowrap="true"><?=$addr["ADR5"];?></TD>
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
			<? if ($selSOId!="") {?>
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
			<? } else {?>
			<TD width="38%" style="border-right: 1px solid #000000;" valign="top">
				<table cellspacing='0' cellpadding='0' width="100%">
					<tr>
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						NAME AND ADDRESS
					</td>
					</tr>					
					<tr>
					<td class="listing-item" width='350' height="20" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<?=$partyAddress?>
					</td>
					</tr>								
				</table>
			</TD>
			<? } // Gate Pass Address?>
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
										<?=dateFormat($gpassDate)//($createdDate)?$createdDate:date("d/m/Y");?>
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
							VEHICLE NO :
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

