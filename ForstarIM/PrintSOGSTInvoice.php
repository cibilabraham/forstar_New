<?php	
	require("include/include.php");
	# Get Sales Order Id
	$selSOId = $g["selSOId"];
	$confirmed = false;
	$addInvSeqNumArr = array(); 
	/*
	If items are more than one page, if true and confirmed then each page have different invoice num 
	*/
	$multiInvoice = false;

	// ----------------------------------------------------------
	# Find PO Records
	$sORec	=	$salesOrderObj->findSORec($selSOId);
	$pOGenerateId	= $sORec[1];
 	$distributorId	= $sORec[2];
	$invoice_created_dt = $sORec[3];
	$selStateId	= $sORec[9];	
	$createdDate	= dateFormat($sORec[3]);
	$selCityId	= $sORec[11];
	$taxType	= $sORec[12];
	//$displayInvoiceType	= ($taxType=='S')?"Sample":"Tax";
	
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
		$transporter_code	= stripSlash($transporterRec[1]);
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
		$gPassPrintDisabled = true; // Asked to disable on 09-09-09
	}	

	$pkgConfirmed	= $sORec[47];
	if ($pkgConfirmed=='Y') $soGrossWt = $salesOrderObj->getActualPkgGrossWt($selSOId);

	$exDutyActive		= $sORec[48];
	$eduCessPercent		= $sORec[49];
	$secEduCessPercent	= $sORec[51];
	$totExDutyAmt		= $sORec[53];
	//$totEduCessAmt		= $sORec[54];
	$totEduCessAmt		= number_format((($totExDutyAmt*$eduCessPercent)/100),2,'.','');
	//$totSecEduCessAmt	= $sORec[55];	
	$totSecEduCessAmt	= number_format((($totExDutyAmt*$secEduCessPercent)/100),2,'.','');
	//$grTotCentralExDuty	= $sORec[56];
	$grTotCentralExDuty = $totExDutyAmt+$totEduCessAmt+$totSecEduCessAmt;

	$transportChargeActive	= $sORec[57];
	$transportCharge	= $sORec[58];
	$billingType		= $sORec[59];
	$exportEnabled = ($billingType=="E")?"Y":"N";
	$invSeqNum		= $sORec[60];
	$invSeqNumArr		= ($invSeqNum!="")?explode(",",$invSeqNum):array();
	$addInvSeqNumArr[] = $pOGenerateId;
	
	//rekha added code 
		$tot_gst_amt		= $sORec[61];
		$tot_cgst_amt		= $sORec[62];
		$tot_sgst_amt		= $sORec[63];
		$tot_igst_amt		= $sORec[64];
		$trans_type			= $sORec[65];
		$ewaybill_no		= $sORec[66];
	// end code 

	// ----------------------------------------------------------	
	$cityRec	=	$cityMasterObj->find($selCityId);
	$cityName	=	stripSlash($cityRec[2]);

	$stateRec	= $stateMasterObj->find($selStateId);				
	$stateName	= stripSlash($stateRec[2]);
	$stateCode	= stripSlash($stateRec[1]);
	
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
	$billingAddress	= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[12]));
	
	$deliveryAddress	= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[13]));
	$sameBillingAddress	= $distributorRec[14];	
	$pinCode		= stripSlash($distributorRec[15]);
	$distEccNo		= $distributorRec[16];
	$gstinNo			= stripSlash($distributorRec[17]);	

	# Distributor Bank details:
	
	
	
	$distributorBankRec		= $distributorMasterObj->getDistBankACRecs($distributorId);	

	
	$bank_name	= stripSlash($distributorBankRec[0][1]);
	$account_no	= stripSlash($distributorBankRec[0][2]);
	$branch_location	= stripSlash($distributorBankRec[0][3]);
	
	//echo("<br><br>rekha".$account_no);
	
	
	
	# Get dist Wise Tax Calc
	//list($taxType, $taxRate) = $salesOrderObj->distWiseTaxInvoiceCalc($distributorId, $selStateId);
	
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
A4 : Width X height = 793.92 X1122.24 / 794
*/
$A4Width	= "794";
$A4Height	= "500";
# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>
<table width='<?=$A4Width?>' cellspacing='1' cellpadding='1' align='center' border="0" id="printMainTbl">
<tr>
	<td STYLE="border: 1px solid #f2f2f2;">
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
	<tr><TD height="5"></TD></tr>
<!-- Logo Row starts here-->
	<tr>
		<TD style="border: 1px solid #000000;">
		<!-- rekha modified the code -->
		
			<table width='100%' cellpadding='0' cellspacing='0' align="center">
			<tr>
				<TD width="100%" valign="middle">
					<table cellpadding="0" cellspacing="0" width="100%">
						<TR>
							<TD align="left" valign="middle" style="text-align:left;padding:5px;" >
								<img src="images/ForstarLogo.png" alt=""/>
							</TD>

								<td class="pageName" valign="top" style="padding:20px 20px;" align="center" nowrap="nowrap">

		<?php if ($invoiceType=='S') {?>	
						<span style="font-size:18px;">
							SAMPLE INVOICE
						</span>
						<? } else {?>
						<span style="font-size:18px;">
							<?php
							
								if ($invoiceConfirmed=="P") {
							?>
								PROFORMA INVOICE
							<? } else {?>
								TAX INVOICE
							<? }?>
						</span>
						<? }?>
							
							
							</td>

						</TR>
						<!-- KD Added -->
						<?php
							$instastr = "FORSTAR INSTA FOODS<br>(A Div of Forstar Frozen Foods Pvt Ltd)";
							$instapvtstr = "FORSTAR INSTA FOODS PVT LTD";
							$instaPrint = "";
							$instaCIN = "U05000MH1992PTC067511";
							$instapvtCIN = "U05000MH2019PTC319743";
							$instaPrintCIN = "";
							$instaPAN = "AAACF3557C";
							$instapvtPAN ="AADCF6200F";
							$instaPrintPAN = "";
							$instaGST = "27AAACF3557C1ZX";
							$instapvtGST = "27AADCF6200F1Z2";
							$instaPrintGST = "";
							$instapvtcomp = $salesOrderObj->fetchInstaPvtRecs($selSOId);
							$instapvtID = $instapvtcomp[0];	
							if($instapvtID == 15)
							{
								$instaPrint = $instapvtstr;
								$instaPrintCIN = $instapvtCIN;
								$instaPrintPAN = $instapvtPAN;
								$instaPrintGST = $instapvtGST;								
							}
							else
							{								
								$instaPrint = $instastr;	
								$instaPrintCIN = $instaCIN;	
								$instaPrintPAN = $instaPAN;		
								$instaPrintGST = $instaGST;				
							}
						?>
						<!-- KD Added Ends-->
						<tr><td class="listing-head" style="line-height:normal; font-size:12pt;text-align:center;padding:0px;" nowrap="nowrap" colspan='3'>
						<?//=$cName?> <?=$instaPrint; ?> <!-- KD Added -->
						<!-- FORSTAR INSTA FOODS<br>(A Div of Forstar Frozen Foods Pvt Ltd) --> <!-- KD Comment -->
						</td></tr>
						<tr><td class="listing-item" align="center" nowrap="" style='padding:10px;' colspan='3'>
						
						
				
							<strong>PLOT NO - M52, MIDC INDUSTRIAL AREA, TALOJA, TALUKA - PANVEL, NAVI MUMBAI - 410208. STATE - MAHARASHTRA
							<br>
							Tel No. : 022-27410807 / 2736 , FAX NO : 022-2741099, Email Id : instafoods@forstarfoods.com, URL: www.forstarfoods.com
							<br>
							Registered Office: 505A, GALLERIA, HIRANANDANI GARDEN, POWAI, A.S.MARG, POWAI, MUMBAI - 400076, STATE - MAHARASHTRA
							</strong>
				
						</td></tr>
						<tr>
							<!-- <td width='33%' align='center' class="listing-item"><strong>CIN NO: U05000MH1992PTC067511</strong></td> --> <!-- KD Comment -->
							<td width='33%' align='center' class="listing-item"><strong>CIN NO: <?=$instaPrintCIN;?></strong></td> <!-- KD Added -->
							<!-- <td width='33%' align='center' class="listing-item"><strong>PAN NO: AAACF3557C</strong></td> --> <!-- KD Comment -->
							<td width='33%' align='center' class="listing-item"><strong>PAN NO: <?=$instaPrintPAN; ?></strong></td> <!-- KD Added -->
							<!-- <td width='33%' align='center' class="listing-item"><strong>GSTIN NO: 27AAACF3557C1ZX</strong></td> --> <!-- KD Comment -->
							<td width='33%' align='center' class="listing-item"><strong>GSTIN NO: <?=$instaPrintGST; ?></strong></td> <!-- KD Added -->
						</tr>
					</table>
				</TD>
				<tr>
				
					
					<tr><td height="12" class="listing-item" style="border-top: 1px solid #000000;font-size:9px;" valign="top"></td></tr>
					
					</table>
		
		<!-- end code -->
		</TD>
	</tr>	
  	
					
  
  
  <tr>
	<td align="left" valign="top" width='99%' style="border-top: 0px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000">
	<!-- 	EXcise invoice	 -->
	<table width='100%' cellpadding='0' cellspacing='0' align="center">
	<tr>
		<TD width="50%" style="border-right: 1px solid #000000;padding:0px 2px;" valign="top">
					<table cellpadding="0" cellspacing="0" width='90%' align='left' style='padding-top:6px;padding-left:6px;'>
						<TR height='15'>
							<TD width='50%' class="ex-print-listing-head" align='left'><span style="font-size:10px;">INVOICE SERIAL NUMBER:</font>&nbsp;</TD>
							<td width='50%' class="listing-item" style="font-size:10px; font-weight:bold;" align='left'><?=$pOGenerateId?></td>
						</TR>
						<TR height='15'>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">DATE OF INVOICE:&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$createdDate?></td>
						</TR>
						<TR height='15'>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">PUR ORDER NO & DATE:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$poNo?>&nbsp;&nbsp;&nbsp;<?=$poDate?></td>
						</TR>

						<TR height='15'>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">E-WAY BILL N0:(If Applicable)</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$ewaybill_no?></td>
						</TR>
						<TR height='15'>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">DELIVERY CHALLAN NO:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$challanNo?>&nbsp;<?=$gatePassNo?></td>
						</TR>
						
							<tr height='15'>
								<td class="listing-item" style="font-size:10px; font-weight:bold;" colspan='2' >&nbsp;</td>
							</tr>
						
						</table>	
		</TD>
		<TD width="50%" style="padding:0px 2px;" valign="top">
			<table cellpadding="0" cellspacing="0" width='90%' align='left' style='padding-top:6px;padding-left:6px;'>
				<tr height='15'>
					
							<TD width='50%' class="ex-print-listing-head" align='left'><span style="font-size:10px;">TRANSPOTATION MODE:</span>&nbsp;</TD>
							<td width='50%'class="listing-item" style="font-size:10px; font-weight:bold;" align='left'><?=$transporterName?></td>

				</tr>
						<TR height='15'>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">VEHICLE N0:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$$vehicleNo?></td>
						</TR>

				<tr height='15'>
				<TD class="ex-print-listing-head" nowrap="nowrap"><span style="font-size:10px;">PLACE OF SUPPLY:</span></TD>	
				<td class="listing-item" style="font-size:10px; font-weight:bold;"><?//=$areaName?>Taloja&nbsp;</td>


				</tr>
				<tr height='15'>
					<TD class="ex-print-listing-head" nowrap="nowrap"><span style="font-size:10px;">NATURE OF TRANSACTION:</span>&nbsp;</TD>	
						
					<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$trans_type;?></td>
					
				</tr>
			</table>
		</TD>
	</tr>
	<tr>
		<TD align='center' class="listing-item" style="border-top: 1px solid #000000;border-bottom: 1px solid #000000;border-right: 1px solid #000000;font-size:10px;" valign="top">
			<strong>Details of Recipient(BILLED TO)</strong>
		</TD>
		<TD align='center' class="listing-item" style="border-top: 1px solid #000000;border-bottom: 1px solid #000000; border-: 1px solid #000000;font-size:10px;" valign="top">
			<strong>Details of Consignee(SHIPPED TO)</strong>
		</TD>
		
	</tr>

	<!-- rekha added code here -->

	<tr>
		<TD style="border-right: 1px solid #000000; padding-left:6px;" valign="top">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<TD>
									<table cellpadding="0" cellspacing="0">
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head" ><span style="font-size:10px;">Name:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px;font-weight:bold;"><?=$distributorName?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head" valign='top' ><span style="font-size:10px;">Address:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;" valign='top'><?=$billingAddress?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">State:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$stateName?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head" nowrap="nowrap"><span style="font-size:10px;">State Code:</span>&nbsp;</TD>		
								<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$stateCode?></td>
							</TR>
		
							
						</table>
					</TD>
				</tr>
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head" nowrap="nowrap"><span style="font-size:10px;">GSTIN/Unique ID:</span>&nbsp;</TD>	
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$gstinNo?></td>
							</TR>

						</table>
					</TD>
				</tr>
					<tr>
						<td class="listing-item" style="font-size:9px; font-weight:bold;">&nbsp;</td>
						</tr>
				
			</table>
					</TD>
				</tr>	

			</table>	
		</TD>
		<TD style="padding:0px 2px;" valign="top">
			<table cellpadding="0" cellspacing="0">
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">Name:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$distributorName?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head" valign='top'><span style="font-size:10px;">Address:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;" valign='top'><?=$deliveryAddress?></td>
						</TR>
						</table>
					</TD>
				</tr>
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
						<TR>
							<TD class="ex-print-listing-head"><span style="font-size:10px;">State:</span>&nbsp;</TD>
							<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$stateName?></td>
						</TR>
						</table>
					</TD>
				</tr height='20'>
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head" nowrap="nowrap"><span style="font-size:10px;">State Code:</span>&nbsp;</TD>		
								<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$stateCode?>&nbsp;</td>
							</TR>

						</table>
					</TD>
				</tr>
				<tr height='20'>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="ex-print-listing-head" nowrap="nowrap"><span style="font-size:10px;">GSTIN/Unique ID:</span>&nbsp;</TD>	
								<td class="listing-item" style="font-size:10px; font-weight:bold;"><?=$gstinNo?></td>
							</TR>

						</table>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
	

	<!-- end code -->	
	
	
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
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="5%" nowrap="nowrap">SR. NO</td>
	        <td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="35%" nowrap="nowrap">DESCRIPTION OF GOODS/SERVICES</td>
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="10%" nowrap="nowrap" colspan='2'>HSN/SAC<br>CODE</td>

	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="nowrap"><?=$taxType?> %</td>

	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="nowrap">QTY</td>

	
    <td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="8%" nowrap="nowrap">RATE PER <br/>UNIT (RS.)</td>
	<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="nowrap" width="7%">TAXABLE <br> VALUE</td>	

	
	<td class="p-listing-head" style="padding:0px 3px; border-bottom: 1px solid #000000;" nowrap="nowrap" width="12%">TOTAL</td>
      </tr>
      <?php
		$defaultNumRows = 60; 
		$defaultRows = $numRows = 12; // Setting No.of rows 12
		$j = 0;

		$salesOrderRecSize = sizeof($salesOrderItemRecs);		
		//if ($balanceRows<=1 && $balanceRows!=0) $numRows = $numRows-1; 
		//echo $balanceRows = ($salesOrderRecSize<$numRows)?($numRows-$salesOrderRecSize):($salesOrderRecSize%$numRows);
		//echo "($salesOrderRecSize%$numRows)=".($salesOrderRecSize%$numRows);
		if ($salesOrderRecSize>$numRows) {
			$numRows = $numRows+10;
			if ($salesOrderRecSize<$numRows ) $numRows = $numRows-10;
		}

		# Find Balance Rows		
		$balanceRows = ($salesOrderRecSize<$numRows)?($numRows-$salesOrderRecSize):($salesOrderRecSize%$numRows);

		$totalPage = ceil($salesOrderRecSize/$numRows);


		$pwTransportCharge = ($transportChargeActive=="Y")?number_format(($transportCharge/$totalPage),2,'.',''):0;

		$totalAmount 	= 0;
		$totalTaxableAmount 	= 0;
		//$totalNumMCPack = 0;
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
		$totalExDutyAmtArr	= array();
		$totEduCessAmtArr	= array();
		$totSecEduCessAmtArr = array();
		//$taxPercentArr = array();
		//$masterTaxPercentArr = array();
		$taxAmtArr = array();
		$totNetWtArr = array();

		$totalMCPkgGrossWt = 0; 
		$wtArr	= array();
		$pkgWtArr	= array();
		$numP		= array();
		$ruleArr	= array();
		$combArr	= array();
		$totMCPkgGrWtArr = array();
		$pkgArr		= array();
		$totNumMCPackArr = array();
		
		$gst_Arr = array();
		$igst_Arr = array();
		$product_Arr = array();

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
			$productCode	= $productRec[1];
			$productName	= $productRec[2];
			$totalAmount 	= $totalAmount + $total;
			$numMCPack	= $por[7];
			//$totalNumMCPack += $numMCPack;
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
			$exDutyAmt			= $por[16];
			$totalExDutyAmtArr[$j] += $exDutyAmt;

			$eduCessAmt		= $por[18];
			$totEduCessAmtArr[$j] += $eduCessAmt;
			$secEduCessAmt	= $por[19];
			$totSecEduCessAmtArr[$j] += $secEduCessAmt;

			$taxPercent	 = $por[10];
			$taxAmt		 = $por[21];
			/*
			if (!in_array($taxPercent,$taxPercentArr)) {
				$taxPercentArr[] =  $taxPercent;
				$masterTaxPercentArr[$j] = $taxPercentArr;
			}*/
			$taxAmtArr[$j][$taxPercent] += $taxAmt;

			// Net Wt Calculation
			$itemNetWt = $por[11];
			$totNetWtArr[$j] += $itemNetWt;
			
			// Gross Wt Calc
			$mcPakingId = $por[6];
			# Find MC Packs Details	---------------------------
			$mcpackingRec	= $mcpackingObj->find($mcPakingId);
			$numPacks	= $mcpackingRec[2]; 
			$numPkts	= $quantity;
			$numPkts	+= $freePkts;
			$mcPacks 	= floor($numPkts/$numPacks);		
			$loosePacks 	= $numPkts%$numPacks;
			$productNetWt	= $productRec[6];
			# Find Product Gross Wt		
			//$productGrossWt	= ($numPkts*$productNetWt)/1000;

			# Find Mc Wt
			$mcPkgWtId = $por[22];
			$mcPackageWt 	= $mcPkgWtMasterObj->getPackageWt($mcPakingId, $productNetWt, $mcPkgWtId);

			# MC Pkg Wt
			//$productMCPkgWt = ($mcPacks *$mcPackageWt)/1000;
			$productMCPkgWt = ($mcPacks*$mcPackageWt);

				$gst_per = $por[23];
			
				$gst_amt = $por[24];
				$igst_per = $por[25];
			
				$igst_amt = $por[26];
				$cgst_per = $por[27];
				$sgst_per = $por[28];
				$cgst_amt = $por[29];
				$sgst_amt = $por[30];
				
				$display_per = "";
				$display_amt="";
				$total_taxable_value ="";
				if($taxType=='GST'){
					$display_per=$gst_per;
					$display_amt=$gst_amt;
					$total_taxable_value = $display_amt + $total;
					$totalTaxableAmount  = $totalTaxableAmount + $total_taxable_value; 
				}else if($taxType=='IGST'){
					
					$display_per=$igst_per;
					$display_amt=$igst_amt;
					$total_taxable_value = $display_amt + $total;
					$totalTaxableAmount  = $totalTaxableAmount + $total_taxable_value; 
				}
				
			
			//$gst_Arr[$i-1]
			
			$gst_Arr[$i-1]= array($gst_per,$gst_amt,$cgst_per,$cgst_amt,$sgst_per,$sgst_amt);
	
			$igst_Arr[$i-1] = array($igst_per,$igst_amt);
			$gst_Arr[$i-1]= array($gst_per,$gst_amt,$cgst_per,$cgst_amt,$sgst_per,$sgst_amt);
	
			$igst_Arr[$i-1] = array($igst_per,$igst_amt);
			
			
			list($pCategoryId, $pStateId, $pGroupId) = $salesOrderObj->findProductRec($selProductId);	
			$pCategoryComb = "$pStateId,$pGroupId";
			//mcpComb
			$mcpComb	     = "$pStateId,$pGroupId,$numPacks,$mcPackageWt";

			$leftPkgRule	     = "$pStateId,$pGroupId,$productNetWt";	
			list ($pLeftComb,$pRightComb) = $salesOrderObj->getPkgGroup($leftPkgRule);
			$pkgGroup = "";
			if ($pLeftComb!="" && $pRightComb!="") $pkgGroup	= "$pLeftComb:$pRightComb";
			# Get Right Packing Rule
			$rightPkgRule	    = $salesOrderObj->getRightPkgRule($leftPkgRule);

			$joinComb    = "$numPacks,$mcPackageWt";	
			
			if (preg_match("/$pCategoryComb/", $mcpComb) && $loosePacks!=0) {
				$wtArr[$j][$pCategoryComb] = $joinComb;
			}
			if (preg_match("/$leftPkgRule/", $pkgGroup) && $pkgGroup!="") {
				$pkgWtArr[$j][$leftPkgRule] = $numPacks;
				$numP[$j][$numPacks] = $mcPackageWt;
			}

			if ($rightPkgRule!="" && $pkgGroup!="") {
				$ruleArr[$j][$leftPkgRule] = $rightPkgRule;
			}

			if ($loosePacks!=0 && $pkgGroup!="" && $invoiceType=='T') $pkgArr[$j][$leftPkgRule] += $loosePacks;	
			if ($loosePacks!=0 && $pkgGroup=="" && $invoiceType=='T') $combArr[$j][$pCategoryComb] += $loosePacks;
			# -------------------------------------
			$mcPkgWt	= $por[12];
			$totMCPkgGrWtArr[$j] += $mcPkgWt;
			
			$totNumMCPackArr[$j] += $numMCPack;

			$exChapterSubhead = "";
			$exChapterSubhead = $salesOrderObj->getExCodeByProductId($selProductId);
			if ($exChapterSubhead=="") {
				$edRec = $salesOrderObj->getExciseDutyRec($exDutyMasterId);
				$exChapterSubhead	= $edRec[0];
			}

			$soTotalUnits	= $quantity+$freePkts;
			$grTotalUnits += $soTotalUnits;
			$resultTotalUnitsArr[$j] += $soTotalUnits;
	
	
	//for summary details

		//$product_Arr[$i-1] = array($exChapterSubhead,$total_taxable_value,$total,$display_per,$display_amt);
	
		$product_Arr[$i-1] = array($exChapterSubhead,$total_taxable_value,$total,$gst_Arr[$i-1],$igst_Arr[$i-1]);
	
	?>
 <tr>
	<td height='20' class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="center"><?=$i?></td>
 	<td class="listing-item" style="<?=$commonStyle?> line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="nowrap">
		<?=$productName?>
	</td>
	<td class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="nowrap" colspan='2'>
		<?=($exChapterSubhead!="")?$exChapterSubhead:"&nbsp;"?><?//=($productCode!="")?$productCode:"&nbsp;"?>
	</td>
	<td class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">
		<?=$display_per?>
	</td>
	
        <td class="listing-item" align="right" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><?=number_format($quantity,0,'.','');?></td>	


	
		<td class="listing-item" align="right" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><?php if ($invoiceType=='T') {?><?=number_format($unitRate,2,'.','');?><?} else {?>0 <?}?></td>	

        <td class="listing-item" style="<?=$commonStyle?> border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="nowrap" align="right" ><?=$total?>
		
	</td>

	<td class="listing-item" style="<?=$commonStyle?> border-bottom: 1px solid #000000;" align="right">
		<?=$total_taxable_value?>
	</td>
      </tr>
	 <?php
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
			$j++;
			
			if ($invoiceType!="S" && $confirmed && $print==0) {
				$invoiceNo = $salesOrderObj->getNextInvoiceNo();
				$newInvNum = ($invSeqNumArr[$j])?$invSeqNumArr[$j]:$invoiceNo;
				$addInvSeqNumArr[] = $newInvNum; 
				$invSeqNumArr[$j] = $newInvNum; 						
			}
			// revert like normal invoice format on Dec 16 20111
			if ($multiInvoice) $pwPOGenerateId = $invSeqNumArr[$j]; 
			else $pwPOGenerateId = $pOGenerateId;
		?>

	<?php
	/*
	* 
	*/
	if ($multiInvoice) {
		$i = 0;
	?>
<!-- First Page Total starts here -->
<?php
		$totalAmount = $resultTotalArr[$j-1];
		$pwTotExDutyAmt = number_format($totalExDutyAmtArr[$j-1],2,'.','');
		$calcPwEduCessAmt = ($pwTotExDutyAmt*$eduCessPercent)/100;
		$pwTotEduCessAmt = number_format($calcPwEduCessAmt,2,'.','');
		$calcPwSecEduCessAmt = ($pwTotExDutyAmt*$secEduCessPercent)/100;
		$pwTotSecEduCessAmt = number_format($calcPwSecEduCessAmt,2,'.','');
		$pwTotCtrlExDutyAmt = $pwTotExDutyAmt+$pwTotEduCessAmt+$pwTotSecEduCessAmt;
		
		$subTotalAfterExDuty = $totalAmount+$pwTotCtrlExDutyAmt;
		$pwGrandTotalAmt =  $subTotalAfterExDuty;

		if ($discount=='Y') {
			$calcDiscountAmt = ($totalAmount*$discountPercent)/100;
			$discountAmt = number_format($calcDiscountAmt,2,'.','');
			$totalAmount = $totalAmount - $discountAmt;
	?>
	<tr bgcolor="white">		
		<td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" colspan="6"><?=$discountRemark?></td>
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="2">(Less) <br/>DISCOUNT&nbsp;<?=$discountPercent;?>%</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$discountAmt?></strong></td>
	</tr>
	<?php
		}
	?>
	<tr>
        <td height="20" colspan="8" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size:9px;">
		<? //if ($totalPage>1) echo "Gross&nbsp;";?>Sub-Total&nbsp;
	</td>
	<? if ($invoiceType=='T') {?>
	<? }?>
	<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=number_format($totalAmount,2);?></strong></td>
      </tr>
	<?php
		//$subTotalAfterExDuty = $totalAmount+$grTotCentralExDuty;
		if ($exDutyActive>0) {
	?>
	<tr bgcolor="white">		
		<td class="listing-item" align="left" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" colspan="6" rowspan="<?=($grTotCentralExDuty!=0)?3:0?>">
			<table>
				<tr>
					<TD>
						<table border="0" cellpadding="0" cellspacing="0">
							<!--TR>
								<TD class="ex-print-listing-head">Total Duty Payable (in words): </TD>
								<td class="listing-item" align="left" style="padding:0px 3px; font-size:9px; line-height:normal;">
									<strong>
									<?php
										$cExToWords = "";
										list($ceNum, $ceDec) = explode(".", $pwTotCtrlExDutyAmt); 
										$cExToWords .= convertNum2Text($ceNum);
										if($ceDec > 0) {
											$cExToWords .= " paise ";
											$cExToWords .= convertNum2Text($ceDec);
										}
										
										echo "Rs. ".ucfirst($cExToWords)." only";
									?>
									</strong>
								</td>
							</TR-->
							<tr><TD height="5"></TD></tr>
							<tr>
								<td class="ex-print-listing-head">Excise Duty payble</td>
								<td class="listing-item" align="left" style="padding:0px 3px; font-size:9px; line-height:normal; font-weight:bold;">
								<?
									$cExToWords = "";
										list($ceNum, $ceDec) = explode(".", $pwTotExDutyAmt); 
										$cExToWords .= convertNum2Text($ceNum);
										if($ceDec > 0) {
											$cExToWords .= " paise ";
											$cExToWords .= convertNum2Text($ceDec);
										}										
										echo "Rs. ".ucfirst($cExToWords)." only";		
								?>								
								</td>
							</tr>
							<tr><TD height="5"></TD></tr>
							<tr>
								<td class="ex-print-listing-head">Edu.Cess Payable</td>
								<td class="listing-item" align="left" style="padding:0px 3px; font-size:9px; line-height:normal; font-weight:bold;">
								<?
									$cExToWords = "";
										list($ceNum, $ceDec) = explode(".", $pwTotEduCessAmt); 
										$cExToWords .= convertNum2Text($ceNum);
										if($ceDec > 0) {
											$cExToWords .= " paise ";
											$cExToWords .= convertNum2Text($ceDec);
										}										
										echo "Rs. ".ucfirst($cExToWords)." only";		
								?>								
								</td>
							</tr>
							<tr><TD height="5"></TD></tr>
							<tr>
								<td class="ex-print-listing-head">Sec.Edu Cess Rs.</td>
								<td class="listing-item" align="left" style="padding:0px 3px; font-size:9px; line-height:normal; font-weight:bold;">
								<?
									$cExToWords = "";
										list($ceNum, $ceDec) = explode(".", $pwTotSecEduCessAmt); 
										$cExToWords .= convertNum2Text($ceNum);
										if($ceDec > 0) {
											$cExToWords .= " paise ";
											$cExToWords .= convertNum2Text($ceDec);
										}										
										echo "Rs. ".ucfirst($cExToWords)." only";		
								?>								
								</td>
							</tr>
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
        <td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="2">
		<? if ($grTotCentralExDuty!=0) { ?>Basic Excise Duty<? } else { ?>Excise Duty 0%<br> Against Form CT1<? }?>
		</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$pwTotExDutyAmt//$totExDutyAmt.":::::".?></strong></td>
	</tr>
	<?
	if ($grTotCentralExDuty!=0) {
	?>
	<tr bgcolor="white">	
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="2">Edu Cess - <?=$eduCessPercent?>%</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$pwTotEduCessAmt?></strong></td>
	</tr>
	<tr bgcolor="white">		
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="2">Sec. EduCess - <?=$secEduCessPercent?>%</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$pwTotSecEduCessAmt?></strong></td>
	</tr>
	<!--tr bgcolor="white">				
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="2">Total C.Excise Duty</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$pwTotCtrlExDutyAmt?></strong></td>
	</tr-->	
	<tr bgcolor="white">				
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="8">Total (Rs.)</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=number_format($subTotalAfterExDuty,2);?></strong></td>
	</tr>	
	<?php
		}
	}	
	?>	
	<?php
	/*
	if (sizeof($taxApplied)>0 && $invoiceType=='T') {	
		for ($j=0;$j<sizeof($taxApplied);$j++) {
			$selTax = explode(":",$taxApplied[$j]); // Tax Percent:Amt
	*/
		$pwTaxApplied = $taxAmtArr[$j-1]; 
		if (sizeof($pwTaxApplied)>0 && $invoiceType=='T') {
			foreach ($pwTaxApplied as $pwTaxPercent=>$pwTaxAmt) {
				$pwGrandTotalAmt += $pwTaxAmt;
	?>
	<tr>
		<td height="20" colspan="6" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="2">Add:&nbsp;<?=$taxType?> <?=$pwTaxPercent?>%</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=$pwTaxAmt;?></strong></td>
      </tr>
	<?php
		}	// For Loop Ends Here
	} // Tax Size Check Ends Here

		$pwGrandTotalAmt += $pwTransportCharge;

		$pwRoundVal = $salesOrderObj->getRoundoffVal($pwGrandTotalAmt);
		$pwGrandTotalAmt += $pwRoundVal;
		
		$pwPkgArr  = $pkgArr[$j-1];
		$pwCombArr = $combArr[$j-1];
		$pwWtArr   = $wtArr[$j-1];
		$pwRuleArr	= $ruleArr[$j-1];
		$pwPkgWtArr = $pkgWtArr[$j-1];
		$pwNumP		= $numP[$j-1];
		$pwTotNumMCPack = $totNumMCPackArr[$j-1];
		
		$pwTotMCPkgGrWt = $totMCPkgGrWtArr[$j-1];

		foreach ($pwCombArr as $combStr=>$tLoosePack) {
			$wtStr = $pwWtArr[$combStr];
			list($wtCombNumPack, $wtCombPackWt) = explode(",",$wtStr);
			$tlpMCPack  = ceil($tLoosePack/$wtCombNumPack); // Convert to MC Pack
			$eachPackWt = $wtCombPackWt/$wtCombNumPack;  // Find Each pack Wt
			$tlpGrossWt = $tLoosePack*$eachPackWt;  // Find Total Gross Wt
			$pwTotNumMCPack += $tlpMCPack;
			$pwTotMCPkgGrWt += $tlpGrossWt;
		}

		$tLoosePkg = 0;
		foreach ($pwPkgArr as $lpr=>$tLoosePack) {
			if (array_key_exists($lpr, $pwRuleArr)) {
				$tLoosePkg += $tLoosePack;
			}
		}		
		if ($tLoosePkg!=0) {		
			$pgNumPack  = max($pwPkgWtArr);		
			$pgPackWt   = $pwNumP[$pgNumPack];	
			$raMCPack  = ceil($tLoosePkg/$pgNumPack); // Convert to MC Pack		
			$ePackWt = $pgPackWt/$pgNumPack;  // Find Each pack Wt
			$raGrossWt = $tLoosePkg*$ePackWt;  // Find Total Gross Wt
			$pwTotNumMCPack += $raMCPack;
			$pwTotMCPkgGrWt += $raGrossWt;
		}
		$pwTotMCPkgGrWt = number_format($pwTotMCPkgGrWt,2,'.','');
		

		if ($invoiceType!='T') $btmColSpan = 3;
		else $btmColSpan = 6;
	?>
	<?php
		if ($transportChargeActive=="Y") {
	?>
	<tr bgcolor="white">				
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="8">Freight</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$pwTransportCharge;?></strong></td>
	</tr>	
	<?php
	}
	?>
	<tr>
		<td height='25' colspan="<?=$btmColSpan?>" nowrap="nowrap"  align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
			<table cellspacing='0' cellpadding='0' width="100%" >
				<tr>
					<TD class="ex-print-listing-head" height="20" style="padding-left:5px;padding-right:5px;">&nbsp;</strong></span></TD>
					<TD class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">&nbsp;</strong></span></TD>
					<TD class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">&nbsp;</TD>
				</tr>
			</table>
		</td>
		<td height='25' class="listing-head" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" colspan="2">Round</td>
		<td height='25' class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=($invoiceType=='T')?$pwRoundVal:0;?></strong></td>
	</tr>

<tr>
			<TD valign="top" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<TR>
						<TD width="70%" class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:8px;line-height:11px; padding:2px; text-align: justify;">
						<?=$certifiedAgreementTxt?> 
						</TD>
						<TD width="30%" rowspan="3" valign="bottom">
							<table width="100%" align="right" cellpadding="0" cellspacing="0">
							<tr>
								<!-- KD Added -->
								<?
								 $instapvtcomp = $salesOrderObj->fetchInstaPvtRecs($selSOId);
									$instapvtID = $instapvtcomp[0];																				
											 if($instapvtID == 15)
											 	{
											 		$forstinsfoods["fifoods"]=$forstinsfoods1["fifoods1"];
											 		$divfrfoods["frfoods"]="";
											 	}
											 	else{
											 		$forstinsfoods["fifoods"]=$forstinsfoods["fifoods"];
											 		$divfrfoods["frfoods"]=$divfrfoods["frfoods"];
											 	} 
								?>
								<!-- KD Added Ends-->
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
						//if ($totalPage>1) echo "(Page $totalPage of $totalPage)&nbsp;&nbsp;";
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
<!-- First Page Total Ends here-->		
	    </table>
		</td>
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
<!-- Next Page Head Starts Here-->
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
															<TD class="listing-head" style="line-height:normal; font-size:12pt;" nowrap="nowrap" align="center"><?=$forstinsfoods["fifoods"];?></TD>
														</TR>
														<TR>
															<TD class="listing-head" style="font-size:9px;text-align:center;" valign="top" nowrap="nowrap" align="center"><?=$divfrfoods["frfoods"];?></TD>
														</TR>
													</table>
												</TD>
											</tr>
											<tr>
												<TD class="print-SOTHead-item" nowrap="nowrap" align="center"><?=$addr["ADR1"]?></TD>
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
					<TD class="pageName" valign="top" style="padding:0px 5px;" align="center" nowrap="nowrap">
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
								<TD class="ex-print-listing-head" nowrap="nowrap">PREP. OF INVOICE DATE & TIME:</TD>		
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
								<TD class="ex-print-listing-head" nowrap="nowrap">REMOVAL OF GOODS DATE & TIME:</TD>	
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
								<tr><TD height="2"></TD></tr>
							<tr><TD colspan="2">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">GSTIN No:</td>
										<td class="listing-item-print" style="padding-left:5px;padding-right:5px;font-size:9px;"><?=$gstinNo?></td>
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
									<TD class="ex-print-listing-head"  valign="middle" nowrap="nowrap">
										INVOICE NO.:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="nowrap" valign="middle" align="left">
										<?=$pwPOGenerateId;?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="nowrap" align="right" width="25%" style="padding:0 2px;">
										DTD:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="nowrap" valign="middle" align="left" width="75%">
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
									<td class="listing-item-print" style="padding:0 3px;" nowrap="nowrap" valign="middle" align="left">
										<?=$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="nowrap" width="25%" align="right" nowrap="nowrap">
										DTD:
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="nowrap" valign="middle" align="left" width="75%">
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
									<TD class="ex-print-listing-head"  valign="middle" nowrap="nowrap">
										D.CHALLAN NO.:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="nowrap" valign="middle" align="left">
										<?=$gatePassNo//$challanNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="ex-print-listing-head"  valign="middle" nowrap="nowrap" width="25%" align="right" nowrap="nowrap">
										DTD:
									</TD>
									<td class="listing-item-print" style="padding:0 3px;" nowrap="nowrap" valign="middle" align="left" width="75%">
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
				<td style="border-bottom: 0px solid #000000;">
					<table cellspacing='0' cellpadding='0' width="100%">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" >
					<TR>
						<TD class="ex-print-listing-head">
							TRANSPORTER:
						</TD>
						<td class="listing-item-print" style="padding:0 3px;" nowrap="nowrap">
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
<!-- Next Page Head Ends Here-->
<?php
		} else {	
			
	/*
	* Multi invoice disabled
	*/
?>	
	<?php
	if ($i==$defaultRows) {	
		/*
		12 - line-height
		22 - $numRows+10;
		*/
		$dupRowHgt = ($A4Height/12)*(22-$defaultRows);
	?>
	<tr rowspan='8' height='<?=abs($dupRowHgt)?>' >
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>	
		<td class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>		
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	</tr>
	<?php
		}	
	?>
		<tr>
			<td height="20" colspan="8" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; font-size:9px;">Total&nbsp;</td>
			<td class="listing-item" style="padding:0px 3px; font-size:9px;" align="right"><strong><? echo number_format($resultTotalArr[$j-1],2);?></strong></td>
		</tr>
	    </table>
	</td>
</tr>
<!--  Sign Starts-->
<?php
	$signTbleBorder = "";
   if ($j==1) $signTbleBorder = "style=\"border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;\"";	
?>
<tr>
	<TD colspan="17" align="center" <?=$signTbleBorder?>>
	<!--TD colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;"-->
		<table width='99%' cellpadding='0' cellspacing='0' align="center">
			<tr>	
				<td>
					<table cellspacing='0' cellpadding='0' width="100%" >
						<tr><TD height="5"></TD></tr>						
		<tr><TD height="5"></TD></tr>		
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<TR>
					<TD valign="bottom" height="5" width="400">			
					</td>
					<td rowspan="5" valign="bottom" style="line-height:100px;">
						<table width="65%" align="right" cellpadding="0" cellspacing="0">
							<tr>
								<!-- KD Added -->
								<?
								 $instapvtcomp = $salesOrderObj->fetchInstaPvtRecs($selSOId);
									$instapvtID = $instapvtcomp[0];																				
											 if($instapvtID == 15)
											 	{
											 		$forstinsfoods["fifoods"]=$forstinsfoods1["fifoods1"];
											 		$divfrfoods["frfoods"]="";
											 	}
											 	else{
											 		$forstinsfoods["fifoods"]=$forstinsfoods["fifoods"];
											 		$divfrfoods["frfoods"]=$divfrfoods["frfoods"];
											 	} 
								?>
								<!-- KD Added Ends-->
								<td class="listing-item" nowrap align="center" style="padding-left:5px;">
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
	  <table width='<?=$A4Width?>' cellspacing='1' cellpadding='1'  align='center'>
	  <tr>
	  	<td STYLE="border: 1px solid #000000;">
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
	  		<tr bgcolor='white'>
				<td height="5"></td>
 	  		</tr>
	   <tr>
		<TD colspan="17" align="center">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top"><img src="images/ForstarLogoccc.png" alt=""/></TD>
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
					<td align="right" valign="top" style="padding-right:10px;">
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
	<td align="left" valign="top" width='100%' style="border-top: 1px solid #000000; border-bottom: 1px solid #000000;">
	<table width='99%' cellpadding='0' cellspacing='0' align="center">
		<tr>
			<TD rowspan="6" width="400"> 
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
			<TD style="padding-left:3px;padding-right:3px;" nowrap="nowrap" colspan="4" width="282" valign="middle">
				<table cellpadding="0" cellspacing="0" border="0" >
					<TR>
						<TD class="ex-print-listing-head"  valign="middle">
							INVOICE NO. & DATE :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;" nowrap="nowrap" valign="middle">
							<?=$pOGenerateId.",&nbsp;".$createdDate?>
						</td>
					</TR>
				</table>
			</TD>		
		</tr>
	</table>
	</td>
  </tr>
<?php
}
/*
* Multi Invoice Check Ends here
*/
?>
<!--tr>
	<td align="left" valign="top" width="100%" style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">dfdf</td>
</tr-->
  <tr>
	<td colspan="17" align="center" style="border-bottom: 1px solid #000000;border-top: 1px solid #000000;">
  	  <table width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="#f2f2f2" align="center">
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="5%" nowrap="nowrap">SR.<br/>NO</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="15%" nowrap="nowrap">Ex.Chapter/<br>Subheading</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="35%" nowrap="nowrap">DESCRIPTION OF GOODS</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="nowrap">UNITS</td>
		<? if ($invoiceType=='T') {?>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="nowrap">SCHEME</td>	
		<? } ?>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" nowrap="nowrap" width="7%">TOTAL<br>UNITS</td>	
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="8%" nowrap="nowrap">RATE PER <br/>UNIT (RS.)</td>
		<td class="p-listing-head" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" width="6%" nowrap="nowrap">RATE OF<br>DUTY</td>
		<td class="p-listing-head" style="padding:0px 3px; border-bottom: 1px solid #000000;" nowrap="nowrap" width="12%">TOTAL<br> VALUE</td>
      </tr>
   <?php
	#Main Loop ending section 
			
	       }
	}
			# height 
			$calcRows = ($i>$numRows)?($defaultRows-$balanceRows):$balanceRows;
			$hgt = ($calcRows>0)?(($A4Height/14)*$calcRows):0;
			$defaultHgt = 100;
   ?>
	<?php
		if ($balanceRows!=0 && $balanceRows<$numRows && abs($hgt)>=$defaultHgt) {
	?>
	<tr rowspan='8' height='<?=abs($hgt)?>' >
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>	
		<td class="listing-head" align="right" style="border-bottom: 1px solid #000000;">
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
		
		<? if ($invoiceType=='T') {?>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		
		<? }?>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>		
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:8pt;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	</tr>
	<?php
		} // height Ceck Ends ere
	?>	
	<?php	
		if ($totalPage>1 && !$multiInvoice) {
	?>
	<tr>
        <td height="20" colspan="8" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size:9px;">Total&nbsp;(<?=$totalPage?>)</td>
       	<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><? echo number_format($resultTotalArr[$j],2);?></strong></td>
      </tr>	
	<?
		for ($p=1;$p<=$totalPage-1;$p++) {
	?>
	<tr>
		<td height="20" colspan="8" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size:9px;">Total&nbsp;(<?=$p?>)</td>		
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
		if ($multiInvoice) {

			$totalAmount = $resultTotalArr[$j];
			$pwTotExDutyAmt = number_format($totalExDutyAmtArr[$j],2,'.','');
			$calcPwEduCessAmt = ($pwTotExDutyAmt*$eduCessPercent)/100;
			$pwTotEduCessAmt = number_format($calcPwEduCessAmt,2,'.','');
			$calcPwSecEduCessAmt = ($pwTotExDutyAmt*$secEduCessPercent)/100;
			$pwTotSecEduCessAmt = number_format($calcPwSecEduCessAmt,2,'.','');
			$pwTotCtrlExDutyAmt = $pwTotExDutyAmt+$pwTotEduCessAmt+$pwTotSecEduCessAmt;
			
			$subTotalAfterExDuty = $totalAmount+$pwTotCtrlExDutyAmt;
			$pwGrandTotalAmt =  $subTotalAfterExDuty;
		} else {
			$subTotalAfterExDuty = $totalAmount+$grTotCentralExDuty;
			$pwGrandTotalAmt =  $subTotalAfterExDuty;
		}

		if ($discount=='Y') {
			$calcDiscountAmt = ($totalAmount*$discountPercent)/100;
			$discountAmt = number_format($calcDiscountAmt,2,'.','');
			$totalAmount = $totalAmount - $discountAmt;
	?>
	<tr bgcolor="white">		
		<td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" colspan="6"><?=$discountRemark?></td>
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="2">(Less) <br/>DISCOUNT&nbsp;<?=$discountPercent;?>%</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=$discountAmt?></strong></td>
	</tr>
	<?php
		}
	?>

	<!-- rekha hided code from here -->
	
	
	<!-- end code -->

<!-- rekha hidded code here 

	<tr>
		<td height="20" colspan="5" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
TAX
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
RATE
		</td>

		<td COLSPAN='2' class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"></td>
      </tr>
	<?
	/*if($taxType=='GST'){
	foreach ($gst_Arr as $arr){
	
	$gst_per = $arr[0];
	$gst_amt = $arr[1];
	$cgst_per = $arr[2];
	$cgst_amt = $arr[3];
	$sgst_per = $arr[4];
	$sgst_amt = $arr[5];
*/

	?>
	<tr>
		<td height="20" colspan="5" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
CGST
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
<?=$cgst_per?>%
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">

		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=$cgst_amt;?></strong></td>
      </tr>

	
	
	
	
	<tr>
		<td height="20" colspan="5" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
SGST
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
<?=$sgst_per?>%
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">

		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=$sgst_amt;?></strong></td>
      </tr>

	<?
/*
	}
	}
	
	if($taxType=='IGST'){
	foreach ($igst_Arr as $arr1){
			//$igst_Arr[$j] = [$igst_per,$igst_amt];
		
		$igst_per = $arr1[0];
		$igst_amt = $arr1[1];
	
	*/
	?>
	<tr>
		<td height="20" colspan="5" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
IGST
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
<?=$igst_per?>%
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">

		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=$igst_amt;?></strong></td>
      </tr>

	<?
	/*}
	}*/
	?>
	
	
	<tr>
		<td height="20" colspan="5" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
CESS
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
0%
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">

		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=$pwTaxAmt;?></strong></td>
      </tr>
 end code here -->
 
 
<?
if($taxType=='GST'){

?>
	
	<tr>
		<td height="20" colspan="6" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>

		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
Total CGST 
		</td>
		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
<strong><?=$tot_cgst_amt;?></strong> 
		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"></td>
      </tr> 
 
	<tr>
		<td height="20" colspan="6" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>

		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
Total SGST 
		</td>
				<td class="listing-item" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
<strong><?=$tot_sgst_amt;?></strong> 
		</td>
		
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"></td>
      </tr>  
<?
	} 
if($taxType=='IGST'){

?>
	  <tr>
		<td height="20" colspan="6" nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>

		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
Total IGST 
		</td>
				<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
<strong><?=$tot_igst_amt;?></strong>
		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"></td>
      </tr>  	  

<?
}
?>
	  <tr>
		<td height="20" colspan="7" nowrap="nowrap" class="listing-head" align="left" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
		<table cellspacing="0" cellpadding="0" width="100%">
				<tbody><tr>
					<td class="ex-print-listing-head" height="20" style="padding-left:0px;padding-right:5px;">NET WT:&nbsp;<span class="listing-item"><strong><?=$soNetWt?>&nbsp;Kg</strong></span></td>
					<td class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">GR. WT:&nbsp;<span class="listing-item"><strong><?=$soGrossWt?>&nbsp;Kg</strong></span></td>
					<td class="ex-print-listing-head" style="padding-left:5px;padding-right:5px;">NO.OF BOXES:&nbsp;<span class="listing-item"><strong><?=$soTNumBox?></strong></span></td>
				</tr>
			</tbody></table>
		
		</td>

		<td class="listing-head" style="padding:0px 3px; font-size:9px;border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap">
 CESS 
		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong>0.00</strong></td>
      </tr>  
	  

	  <?php
		//}	// For Loop Ends Here
	//} // Tax Size Check Ends Here
		$pwGrandTotalAmt += $pwTransportCharge;
		$pwRoundVal = $salesOrderObj->getRoundoffVal($pwGrandTotalAmt);
		$pwGrandTotalAmt += $pwRoundVal;

		// Same as Page wise settings above
		$pwPkgArr  = $pkgArr[$j];
		$pwCombArr = $combArr[$j];
		$pwWtArr   = $wtArr[$j];
		$pwRuleArr	= $ruleArr[$j];
		$pwPkgWtArr = $pkgWtArr[$j];
		$pwNumP		= $numP[$j];
		$pwTotNumMCPack = $totNumMCPackArr[$j];
		
		$pwTotMCPkgGrWt = $totMCPkgGrWtArr[$j-1];

		foreach ($pwCombArr as $combStr=>$tLoosePack) {
			$wtStr = $pwWtArr[$combStr];
			list($wtCombNumPack, $wtCombPackWt) = explode(",",$wtStr);			
			$tlpMCPack  = ceil($tLoosePack/$wtCombNumPack); // Convert to MC Pack
			$eachPackWt = $wtCombPackWt/$wtCombNumPack;  // Find Each pack Wt
			$tlpGrossWt = $tLoosePack*$eachPackWt;  // Find Total Gross Wt
			$pwTotNumMCPack += $tlpMCPack;
			$pwTotMCPkgGrWt += $tlpGrossWt;
		}

		$tLoosePkg = 0;
		foreach ($pwPkgArr as $lpr=>$tLoosePack) {
			if (array_key_exists($lpr, $pwRuleArr)) {
				$tLoosePkg += $tLoosePack;
			}
		}		
		if ($tLoosePkg!=0) {		
			$pgNumPack  = max($pwPkgWtArr);		
			$pgPackWt   = $pwNumP[$pgNumPack];	
			$raMCPack  = ceil($tLoosePkg/$pgNumPack); // Convert to MC Pack		
			$ePackWt = $pgPackWt/$pgNumPack;  // Find Each pack Wt
			$raGrossWt = $tLoosePkg*$ePackWt;  // Find Total Gross Wt
			$pwTotNumMCPack += $raMCPack;
			$pwTotMCPkgGrWt += $raGrossWt;
		}
		$pwTotMCPkgGrWt = number_format($pwTotMCPkgGrWt,2,'.','');

		if ($invoiceType!='T') $btmColSpan = 3;
		else $btmColSpan = 6;
	?>
	<?php
		if ($transportChargeActive=="Y") {
	?>
	<tr bgcolor="white">				
        	<td class="listing-head" style="padding:0px 3px; font-size:9px;line-height:normal; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" nowrap="nowrap" colspan="8">Freight</td>
                <td class="listing-item" align="right" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;"><strong><?=($multiInvoice)?$pwTransportCharge:$transportCharge;?></strong></td>
	</tr>	
	<?php
	}
	?>
	<tr>
		<td height='25' class="listing-head" colspan="<?=$btmColSpan-4?>" nowrap="nowrap"  align="left" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;">
			Tax is Payable On Reverse Charge :(Yes/No)&nbsp;
		</td>
		<td colspan="4" class="listing-head" nowrap="nowrap"  align="left" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;">
			No&nbsp;
		</td>
		<td height='25' class="listing-head" style="padding:0px 3px; font-size:9px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right" colspan="2">ROUND OFF</td>
		<td height='25' class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right"><strong><?=($invoiceType=='T')?$pwRoundVal:0;?></strong></td>
	</tr>
	  
	  
	  <tr>
        <td class="listing-head" height="20" colspan="7" nowrap="nowrap" class="listing-head" align="left" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000; font-size:9px;">
			Total Invoice Value in Words (Rs.):&nbsp;&nbsp;&nbsp;&nbsp;
			<span class='listing-item' style='padding-right:10px;'>
			<?php 
							//$input = round($grandTotalAmt);
							$input = round($totalTaxableAmount);
							echo "<strong>Rs.".convert($input)."Only</strong>";
			?> 
			</span>
	</td>	
		<td class="listing-item" style='border-bottom: 1px solid #000000;'>
		<? //if ($totalPage>1 && !$multiInvoice) echo "Gross&nbsp;";?>
			<font color='#922B21'><strong>Gr. Total&nbsp;</strong></font>
		</td>
		<td class="listing-item" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" align="right">
			<font color='#922B21'>
				<strong><?=round($totalTaxableAmount);?></strong>
			</font>
		</td>
		
	  
	  </tr>
	  <tr>
		<td height='100' valign='top' class="listing-head" style="padding:0px 3px; font-size:9px; border-bottom: 1px solid #000000;" colspan="<?=$btmColSpan+3?>" nowrap="nowrap" align="left" style="border-right: 1px solid #000000;">
	
			<br>
			<table cellspacing='0' cellpadding='2' border='0' width='100%'>
				
				<tr>
				<?
					$varcolspan="5"	;
					if($taxType=="GST"){
						$varcolspan="7"	;
					}
				?>
				<td colspan='<?=$varcolspan?>' class="listing-item" align='left' style="border-bottom: 1px solid #000000;font-size:9px;line-height:normal;"><STRONG>Outstanding Summary</strong></td>
				</tr>
				<tr>
					<td class="listing-item" style="border-right: 1px solid #000000;padding-top: 6px;font-size:9px;line-height:normal; " align='center'>HSN/SAC&nbsp;CODE</td>
					<td class="listing-item" style="border-right: 1px solid #000000;padding-top: 6px;font-size:9px;line-height:normal; " align='center'>Taxable Amount</td>
					<?
					$varcolspan="2"	;
					if($taxType=="GST"){
						$varcolspan="4"	;
					}
					?>
					<td class="listing-item" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000; padding-top: 6px;font-size:9px;line-height:normal; " align='center' colspan='<?=$varcolspan?>'><?=$taxType?>&nbsp;Tax</td>
					<td class="listing-item" style="padding-top: 6px;font-size:9px;line-height:normal; " align='center'>Total Amount</td>
				</tr>
				<tr>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align='center'></td>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align='center'></td>
					<?if($taxType=="GST"){?>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='center'>CGST Rate</td>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='center'>Tax Amount</td>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='center'>SGST Rate</td>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='center'>Tax Amount</td>
					<?
					}
					else if($taxType=="IGST"){
					?>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='center'>Rate</td>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='center'>Tax Amount</td>
					<?
					}
					?>
					
					<td class="listing-item" style="border-bottom: 1px solid #000000;" align='center'></td>
				</tr>
				<?
				$tt_taxableamt = 0;
				$tt_cgst_amt =0;
				$tt_sgst_amt =0;
				$tt_igst_amt =0;
				$tt_amt = 0;
				$ttt_amt = 0;
				foreach ($product_Arr as $prod_arr){
					//$tt_taxableamt  = $tt_taxableamt + $prod_arr[4];
					$tt_amt  = $tt_amt + $prod_arr[2];
					$ttt_amt  = $ttt_amt + $prod_arr[1];
				?>
				<tr>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; "><?=$prod_arr[0]?></td>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><?=number_format($prod_arr[2],2);?></td>
					<?
					//For GST
					if($taxType=='GST'){
						$tt_taxableamt = $tt_taxableamt + $prod_arr[3][1];
						$tt_cgst_amt = $tt_cgst_amt + $prod_arr[3][3];
						$tt_sgst_amt = $tt_sgst_amt + $prod_arr[3][5];
						echo "<td class='listing-item' style='border-right: 1px solid #000000; border-bottom: 1px solid #000000;padding-left:6px;font-size:9px;line-height:normal; ' align='left'>".
						$prod_arr[3][2].
						"%</td>";
						echo "<td class='listing-item' style='border-right: 1px solid #000000; border-bottom: 1px solid #000000;padding-left:6px;font-size:9px;line-height:normal; ' align='right'>".
						$prod_arr[3][3].
						"</td>";
						echo "<td class='listing-item' style='border-right: 1px solid #000000; border-bottom: 1px solid #000000;padding-left:6px;font-size:9px;line-height:normal; ' align='left'>".
						$prod_arr[3][4].
						"%</td>";	
						echo "<td class='listing-item' style='border-right: 1px solid #000000; border-bottom: 1px solid #000000;padding-left:6px;font-size:9px;line-height:normal; ' align='right'>".
						$prod_arr[3][5].
						"</td>";					
					}	
					//For IGST
					if($taxType=='IGST'){
						$tt_taxableamt = $tt_taxableamt + $prod_arr[4][1];
						echo "<td class='listing-item' style='border-right: 1px solid #000000; border-bottom: 1px solid #000000;padding-left:6px;font-size:9px;line-height:normal;' align='right'>". $prod_arr[4][0] ."%</td>";
						echo"<td class='listing-item' style='border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; ' align='right'>". $prod_arr[4][1] ."</td>";
					
					}
					?>
					<td class="listing-item" align='right' style="border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><?=number_format($prod_arr[1],2);?></td>
				</tr>			
				<?
				}
				?>
		
				<?if($taxType=='GST'){
				?>
				<tr>
					<!--<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Total: </strong></td>-->
					<td colspan='2' class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Assessable Value:&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($tt_amt,2);?></strong></td>
					<td class="listing-item" style="text-align: right;
border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Total CGST Amount:</strong></td>
					<td class="listing-item" style="text-align: right;
border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong><?=number_format($tt_cgst_amt,2);?></strong></td>
					<td class="listing-item" style="text-align: right;
border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Total SGST Amount:</strong></td>
					<td class="listing-item" style="text-align: right;
border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong><?=number_format($tt_sgst_amt,2);?></strong></td>
					<td class="text-align: right;
listing-item" style="border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Total Amount:&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($ttt_amt,2);?></strong></td>
				</tr>			
				<? }?>

		
				
				<?if($taxType=='IGST'){
				?>
				<tr>
					<td colspan='2' class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Assessable Value:&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($tt_amt,2);?></td>
					<!--<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong></strong></td>-->
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Total IGST Amount:</strong></td>
					<td class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong><?=number_format($tt_taxableamt,2);?></strong></td>
					<td class="listing-item" style="border-bottom: 1px solid #000000;font-size:9px;line-height:normal; " align='right'><strong>Total Amount:&nbsp;&nbsp;&nbsp;&nbsp;<?=number_format($ttt_amt,2);?></strong></td>
				</tr>			
				<? }?>
			</table>

			<br>
			
		</td>
	  </tr>

	  </table>
	  <br>
	  </td>
  </tr>
  <? } else {?>
  <tr> 
    <td colspan="17" align="center" class="err1"><?=$msgNoRecords;?></td>
  </tr>
	<? }?>
  <tr>
	<?php
		if ($totalPage>1) {					
	?>
	<TD colspan="17" align="center">
	<?
		} else {
	?>
	<TD colspan="17" align="center" style="border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" >
	<?
		}
	?>
		<table width='100%' cellpadding='0' cellspacing='0' align="center">
			<tr>	
				<td>
		<table cellspacing='0' cellpadding='0' width="100%" >
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
						<TD width="40%" class="listing-item" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;font-size:8px;line-height:11px; padding:2px; text-align: justify;">
						<?=$certifiedAgreementTxt?> 
						</TD>
						
						<TD width="30%" rowspan='3' class="listing-item" style="border-right: 1px solid #000000; font-size:8px;line-height:11px; padding:2px; text-align: justify;">
						 <table cellpadding="0" cellspacing="0" width="100%" height="174px">
								<tbody><tr>
									<td class="listing-item" style="font-size:8px;line-height:11px; padding:5px 2px;" align="center" valign="top">Received the above goods<br>in good condition</td>
								</tr>
								<tr><td height="95px">&nbsp;</td></tr>
								<tr>
									<td class="listing-item" style="font-size:8px;line-height:11px;" valign="top" align="center">									
										<table cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td class="listing-item" style="font-size:8px;" valign="top" align="center">(Receiver's Signature and Stamp)</td>
												</tr>											
											</tbody>
										</table>
									</td>
								</tr>
							</tbody></table>
						</TD>
						
						
						
						<TD width="30%" rowspan="3" valign="middle" align='center' style='padding: 10px;'>
							
							<!--
							<table width="80%" align="center" cellpadding="0" cellspacing="0" border='0' style='border: 1px solid #000;'>
							<tr>
								<td class="listing-item" nowrap align="center" >
								Electronic Reference Number:
																
								</td>
							</tr>
							<tr><TD height="40px">&nbsp;</TD></tr>

							</table>
							
							<br>-->
							<table width="90%" align="center" cellpadding="0" cellspacing="0" border='0' style='border: 1px solid #000;'>
							<tr>
								<!-- KD Added -->
								<?
								 $instapvtcomp = $salesOrderObj->fetchInstaPvtRecs($selSOId);
									$instapvtID = $instapvtcomp[0];																				
											 if($instapvtID == 15)
											 	{
											 		$forstinsfoods["fifoods"]=$forstinsfoods1["fifoods1"];
											 		$divfrfoods["frfoods"]="";
											 	}
											 	else{
											 		$forstinsfoods["fifoods"]=$forstinsfoods["fifoods"];
											 		$divfrfoods["frfoods"]=$divfrfoods["frfoods"];
											 	} 
								?>
								<!-- KD Added Ends-->
								<td class="listing-item" nowrap align="center" >
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
						<!--<b>Note- Returnable Sales Invoice:</b>&nbsp;
						<br>--><BR>
						<?
						//echo $invoice_created_dt;
						$credit_period_dt="";
						if($invoice_created_dt!=''){
							$credit_period_dt = date ("Y-m-d", strtotime ($today ."+90 days"));
						}
						
						?>
						<!-- KD Added -->
						<?php
						 $instapvtcomp = $salesOrderObj->fetchInstaPvtRecs($selSOId);
							$instapvtID = $instapvtcomp[0];		
							$instastr = "Payment by RTGS /A/c payee/ Demand Draft only in favor of “FORSTAR INSTA FOODS”";
							$instapvtstr = "Payment by RTGS /A/c payee/ Demand Draft only in favor of “FORSTAR INSTA FOODS PVT LTD”";
							$instaPrint = "";	
							$instaAcc = "02392560000213";
							$instapvtAcc = "50200038137599";
							$instaPrintAcc = "";
							if($instapvtID == 15)			
							{
									$instaPrint = $instapvtstr;	
									$instaPrintAcc = $instapvtAcc;
							}
							else
							{
									$instaPrint = $instastr;	
									$instaPrintAcc = $instaAcc;
							}
						?>
						<!-- KD Added Ends-->
						<b>Payment Terms</b><br>&nbsp;
						Payment Due by:&nbsp;&nbsp;<?=dateFormat($credit_period_dt);?><BR>
						<p>
							<!-- 1) Payment by RTGS /A/c payee/ Demand Draft only in favor of “FORSTAR INSTA FOODS”<br>	--> <!-- KD Commented -->
							1) <?=$instaPrint; ?><br> <!-- KD Added -->
							2) Interest@24% p.a.will be charged if not paid within the credit period allowed.<br>	
						</p>
						<b>Bank Details:</b><br>  
						Name Of Bank:&nbsp;&nbsp;HDFC BANK LTD<?//=$bank_name?><BR>
						<!-- Account No:&nbsp;&nbsp;,02392560000213<?//=$account_no?><BR> --> <!-- KD Commented -->
						Account No:&nbsp;&nbsp;<?=$instaPrintAcc; ?><?//=$account_no?><BR> <!-- KD Added -->
						IFSC CODE:&nbsp;&nbsp;HDFC0000239<BR>
						Bank Address:&nbsp;&nbsp;POWAI, MUMBAI-76<?//=$branch_location?><BR><BR>
						</TD>
					</TR>				
					<TR>
						<TD  width="70%" class="listing-item" style="border-right: 1px solid #000000; font-size:8px;line-height:11px; padding:0px 2px; text-align: justify;">
						<b> Terms & Conditions:</b>&nbsp;
						<p>
						1) Goods once sold will not be accepted back.<br>					
						<!--2) GST and other taxes will be charged extra wherever and what-ever applicable.<br>					
						-->
						2) We do not take resposiblety for shortage or loss, ones the goods are deliverd by the Transporter.<br>					
						3) Shortage/damage of goods to be notified immediately on Recipt of Goods, and mentioned on Lr copy of trensporter<br>					
						4) Subject to Mumbai Jurisdiction only.	<br><br>
						</p>				
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
	


	if ($confirmed) {
		$newInvSeqNum = implode(",",$addInvSeqNumArr);		
		if (sizeof($invSeqNumArr)!=sizeof($addInvSeqNumArr)) {
			//$salesOrderObj->updateSOInvSeqNum($selSOId, $newInvSeqNum);
		}		
	}

	//print_r($gst_Arr);
	
	?>
</body>
</html>