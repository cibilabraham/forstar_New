<?php
	require("include/include.php");
	require_once ('components/base/ExporterMaster_model.php');
	$exporter_m		= new ExporterMaster_model();

	$confirmed = false;	
	$selInvoiceId 	= $g["invoiceId"];
	$print		= $g["print"];
	
	$invoiceRec	= $invoiceObj->find($selInvoiceId);
		
		$mainId 	= $invoiceRec[0];		
		$invoiceNo 	= $invoiceRec[1];
		$invDate	= $invoiceRec[2];
		$invoiceDate = dateFormat($invDate);

		//$invYear = ($invoiceNo=="" || $invoiceNo==0)?date('y'):date('y', strtotime($invDate));
		$selInvDate = ($invoiceNo=="" || $invoiceNo==0)?date('y-m-d'):$invDate;
		$invYearRange = getFinancialYearRange($selInvDate);

		//$displayInvNum = "FFFPL/$invoiceNo/$invYearRange";

		$brcFinalDestination	= $invoiceRec[10];
		$brcShipBillNo		= $invoiceRec[20];
		$brcShipBillDate	= $invoiceRec[21];
		$brcBillLaddingNo	= $invoiceRec[22];
		$brcBillLaddingDate	= $invoiceRec[23];
		$exporter			= $invoiceRec[25];		
		$exporterAddress	= $exporter_m->getExporterDetails($exporter, 1);
		$exporterAddress = preg_replace('#<br\s*/?>#i', " ", $exporterAddress);
		$exporterAlphaCode	= $exporter_m->getExporterAlphaCode($exporter);
		$displayInvNum = $exporterAlphaCode."/$invoiceNo/$invYearRange";

		$invoiceTypeId = $invoiceRec[7];
		$invoiceTypeRec	= $invoiceTypeMasterObj->find($invoiceTypeId);
		$invoiceTypeName = $invoiceTypeRec[1];

		$brcRec = $invoiceObj->findBRCRec($mainId);
		if (sizeof($brcRec)>0)
		{
			$brcIECCodeNo			= $brcRec[1];
			$brcDEPBEnrolNo			= $brcRec[2];
			$brcBankId				= $brcRec[3];
			$brcGoodsDescription	= $brcRec[4];
			$brcBillAmt				= $brcRec[5];
			$brcFreightAmt			= $brcRec[6];
			$brcInsuranceAmt		= $brcRec[7];
			$brcCommissionDiscount	= $brcRec[8];
			$brcFreeConvert			= $brcRec[9];
			$brcFOBValue			= $brcRec[10];
			$brcRealisationDate		= ($brcRec[11]!='0000-00-00')?dateFormat($brcRec[11]):"";
			$brcLicenceCategory		= $brcRec[12];
			$brcRefNo				= $brcRec[13];
			$brcRefNoDate			= ($brcRec[14]!='0000-00-00')?dateFormat($brcRec[14]):"";
			$brcFgnExDealerCodeNo	= $brcRec[15];
			$brcExporterName		= $brcRec[16];
			$brcExportDate			= ($brcRec[17]!='0000-00-00')?dateFormat($brcRec[17]):"";
			$brcCertifyAmtDescr		= $brcRec[18];

			$bankRec				= $companydetailsObj->getBankACRec($brcBankId);
			$brcExportBillTo		= $bankRec[2].', '.$bankRec[3];
		}

		$numCopy = 1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title><?="BRC-".strtoupper($invoiceTypeName)."#FFFPL/$invoiceNo/$invYearRange";?></title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
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
<body>
<form name="frmPrintInvoice" id="frmPrintInvoice" action="PrintInvoice.php">
<?php
	if ($print=='Y' || $pkgListEnabled) {
?>
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	}
?>
<!--  Strat table here-->
<?php
	# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>
<table width='90%' cellspacing='1' cellpadding='1'  align='center' border="0">
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
   <tr> 
    <td colspan="17" align="center">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
	<DIV id="bankCertDiv" style="padding:0px; margin:0px;">
<TABLE cellpadding=0 cellspacing=0 class="brcTbl0">
<TR class="brcTr0">
	<TD class="brcTd0">&nbsp;</TD>
	<TD class="brcTd1">APPENDIX 22A</TD>
</TR>
<TR class="brcTr1">
	<TD class="brcTd0">BANK CERTIFICATE OF EXPORT AND REALISATION.</TD>
	<TD class="brcTd2">FORM NO. 1</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd0"><FONT class="brcFt0">Note:Please see chapter 4 and 5 of the Import Export Policy Hand book</FONT></TD>
	<TD class="brcTd3">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd0">&nbsp;</TD>
	<TD class="brcTd2">IEC CODE NO:&nbsp;<?=($brcIECCodeNo!="")?$brcIECCodeNo:"0392068460"?></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd0">&nbsp;</TD>
	<TD class="brcTd4">DEPB ENROLMENT NO.&nbsp;<?=($brcDEPBEnrolNo!="")?$brcDEPBEnrolNo:"03/DEPB/235/ALS/VII/AM/99"?></TD>
</TR>
</TABLE>
<P class="brcP0"><FONT class="brcFt1">TO,THE JOINT DIRECTOR GENERAL OF FOREIGN TRADE,MUMBAI</FONT></P>
<P class="brcP1"><FONT class="brcFt1">We <?=$exporterAddress?></FONT><FONT class="brcFt2">hereby declare thatwehave forwarded a documentary export bill to:&nbsp;<?=$brcExportBillTo?> for Collection/Negotiation/Purchase as per particulars given hereunder</FONT></P>
<TABLE cellpadding=0 cellspacing=0 class="brcTbl1">
<TR class="brcTr2">
	<TD colspan=3 class="brcTd5"><FONT class="brcFt3">1.INVOICE NO.:&nbsp;<?=$displayInvNum?></FONT></TD>
	<TD class="brcTd6">&nbsp;</TD>
	<TD class="brcTd7">&nbsp;</TD>
	<TD class="brcTd8">&nbsp;</TD>
	<TD class="brcTd9"><FONT class="brcFt4">2.</FONT>DATE:&nbsp;<?=($invDate!='0000-00-00')?date('d.m.Y', strtotime($invDate)):""?></TD>
</TR>
<TR class="brcTr3">
	<TD colspan=3 class="brcTd10"><FONT class="brcFt5">3.EXPORT PROMOTION COPY OF S/BILL</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd14">&nbsp;</TD>
</TR>
<TR class="brcTr4">
	<TD colspan=3 class="brcTd15"><FONT class="brcFt6">DULY AUTHENTICATED BY CUSTOM NO.:</FONT></TD>
	<TD class="brcTd16">&nbsp;</TD>
	<TD class="brcTd17"><FONT class="brcFt6"><?=$brcShipBillNo?></FONT></TD>
	<TD class="brcTd18">&nbsp;</TD>
	<TD class="brcTd19"><FONT class="brcFt7">4.</FONT><FONT class="brcFt6">DATE:&nbsp;<?=($brcShipBillDate!='0000-00-00')?date('d.m.Y', strtotime($brcShipBillDate)):""?></FONT></TD>
</TR>
<TR class="brcTr5">
	<TD colspan=5 class="brcTd20"><FONT class="brcFt5">5.DESCRIPTION OF GOODS AS GIVEN IN THE CUMSTOMS AUTHENTICATED S/BILLS:</FONT></TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd14">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=5 class="brcTd20"><FONT class="brcFt0">
	<?=nl2br($brcGoodsDescription)?>
</FONT></TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd14">&nbsp;</TD>
</TR>
<TR class="brcTr3">
	<TD colspan=5 class="brcTd20"><FONT class="brcFt5">6.BILL OF LADING/POST PARCEL RECEIPT/AIRWAY BILL NO.:&nbsp;<?=$brcBillLaddingNo?></FONT></TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd21"><FONT class="brcFt17">7.</FONT><FONT class="brcFt5">DATE:&nbsp;<?=($brcBillLaddingDate!='0000-00-00')?date('d.m.Y', strtotime($brcBillLaddingDate)):""?></FONT></TD>
</TR>
<TR class="brcTr4">
	<TD colspan=4 class="brcTd22">&nbsp;</TD>
	<TD class="brcTd23">&nbsp;</TD>
	<TD class="brcTd18">&nbsp;</TD>
	<TD class="brcTd24">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd25">8.DESTINATION OF GOODS :COUNTRY NAME :&nbsp;<?=strtoupper($brcFinalDestination)?></TD>
	<TD class="brcTd26">&nbsp;</TD>
	<TD class="brcTd27">&nbsp;</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd29">9</TD>
	<TD class="brcTd30">&nbsp;</TD>
	<TD class="brcTd31">10</TD>
	<TD class="brcTd32">11</TD>
	<TD class="brcTd33">12</TD>
	<TD class="brcTd34">13</TD>
	<TD class="brcTd35">14</TD>
</TR>
<TR class="brcTr3">
	<TD class="brcTd36"><FONT class="brcFt5">BILL AMOUNT</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">FREIGHT AMT</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">INSURANCE AMT</FONT></TD>
	<TD class="brcTd38"><FONT class="brcFt5">COMMISSION</FONT></TD>
	<TD class="brcTd39"><FONT class="brcFt5">WHETHER THE</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">FOB VALUE FOR</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd36"><FONT class="brcFt5">CIF/C&F/FOB</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">AS PER BILL OF</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">AS PER INSURANCE</FONT></TD>
	<TD class="brcTd38"><FONT class="brcFt5">DISCOUNT</FONT></TD>
	<TD class="brcTd39"><FONT class="brcFt5">EXPORT IS IN</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">VALUE ACTUALLY</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd36"><FONT class="brcFt5">(IN FOREIGN</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">LADING/FREIGHT</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">COMPANYS BILL/</FONT></TD>
	<TD class="brcTd38"><FONT class="brcFt5">PAID/PAYABLE</FONT></TD>
	<TD class="brcTd39"><FONT class="brcFt5">FREELY CONVER-</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">REALISED INFREE</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd36"><FONT class="brcFt5">EXCHANGE)</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">MEMO</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">RECEIPT</FONT></TD>
	<TD class="brcTd40">&nbsp;</TD>
	<TD class="brcTd39"><FONT class="brcFt5">TIBLE CURRENCY</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">FOREIGN EXCH-</FONT></TD>
</TR>
<TR class="brcTr6">
	<TD class="brcTd41">&nbsp;</TD>
	<TD class="brcTd42">&nbsp;</TD>
	<TD class="brcTd43">&nbsp;</TD>
	<TD class="brcTd44">&nbsp;</TD>
	<TD class="brcTd45">&nbsp;</TD>
	<TD class="brcTd46"><FONT class="brcFt8">OR IN INDIAN RS.</FONT></TD>
	<TD class="brcTd19"><FONT class="brcFt8">ANGE/RS.</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd29" height="200" valign="top" style="vertical-align: top; padding:10px 5px 0; text-align:center;">
		<?=nl2br($brcBillAmt)?>
	</TD>
	<TD class="brcTd58" colspan="2" valign="top" style="vertical-align: top; padding:10px 5px 0; text-align:center;">
		<?=nl2br($brcFreightAmt)?>
	</TD>
	<TD class="brcTd59" valign="top" style="vertical-align: top; padding:10px 5px 0; text-align:center;">
		<?=nl2br($brcInsuranceAmt)?>
	</TD>
	<TD class="brcTd60" valign="top" style="vertical-align: top; padding:10px 5px 0; text-align:center;">
		<?=nl2br($brcCommissionDiscount)?>
	</TD>
	<TD class="brcTd27" valign="top" style="vertical-align: top; padding:10px 5px 0; text-align:center;">
		<?=nl2br($brcFreeConvert)?>
	</TD>
	<TD class="brcTd28" valign="top" style="vertical-align: top; padding:10px 5px 0; text-align:center;">
		<?=nl2br($brcFOBValue)?>
	</TD>
</TR>
<TR class="brcTr2">
	<TD class="brcTd61">&nbsp;</TD>
	<TD class="brcTd30">&nbsp;</TD>
	<TD class="brcTd58">&nbsp;</TD>
	<TD class="brcTd59">&nbsp;</TD>
	<TD class="brcTd60">&nbsp;</TD>
	<TD class="brcTd27">&nbsp;</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd25">15.DATE OF REALISATION OF EXPORT PROCEEDS :</TD>
	<TD class="brcTd62"><?=$brcRealisationDate?></TD>
	<TD class="brcTd46">16.G.R.FORM NO.</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr8">
	<TD colspan=5 class="brcTd63">17.NO.DATE & CATEGORY OF APPLICABLE LICENCE/AUTHORISATION:&nbsp;<?=$brcLicenceCategory?></TD>
	<TD class="brcTd64">&nbsp;</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr5">
	<TD colspan=4 class="brcTd65"><FONT class="brcFt0">We futher declare that the aforesaid particulars are correct</FONT></TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd65"><FONT class="brcFt0">(Copies of invoices relevant to these exports and custom</FONT></TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd65"><FONT class="brcFt0">attested EP cpy of relevant shipping bill is attached for</FONT></TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD colspan=2 class="brcTd68"><FONT class="brcFt5">(Signature of Exporter)</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD colspan=2 class="brcTd69"><FONT class="brcFt0">verification by the Bank)</FONT></TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=3 class="brcTd71"><FONT class="brcFt5">Name in Block Letters:&nbsp;<?=$brcExporterName?></FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd72"><FONT class="brcFt5">Place: MUMBAI</FONT></TD>
	<TD colspan=2 class="brcTd73"><FONT class="brcFt5">Official</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd74"><FONT class="brcFt0">Designation:</FONT></TD>
	<TD colspan=2 class="brcTd68"><FONT class="brcFt5">AUTHORISED SIGNATORY</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd72"><FONT class="brcFt5">Date:&nbsp;<?=date("d.m.Y");?></FONT></TD>
	<TD colspan=2 class="brcTd73"><FONT class="brcFt0">Seal Stamp</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd75">&nbsp;</TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=3 class="brcTd71"><FONT class="brcFt0" style="white-space:normal;">Full Official Address: <?=$exporterAddress?><!--505A,GALLERIA , Hiranandani Gardens,--></FONT></TD>
</TR>
<TR class="brcTr8">
	<TD class="brcTd75">&nbsp;</TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=2 class="brcTd76"><FONT class="brcFt11">&nbsp;<!--A.S.Marg, Mumbai-400076--></FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd75">&nbsp;</TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=2 class="brcTd77"><FONT class="brcFt0">Full Residental Address:</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr9">
	<TD class="brcTd72"><FONT class="brcFt12">BANK CERTIFICATE</FONT></TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=2 class="brcTd78"><FONT class="brcFt13">Ref No.&nbsp;<?=$brcRefNo?></FONT></TD>
	<TD class="brcTd79"><FONT class="brcFt0">Date.&nbsp;<?=$brcRefNoDate?></FONT></TD>
</TR>
<TR class="brcTr0">
	<TD colspan=3 class="brcTd80"><FONT class="brcFt0">Authorised Foreign Exchange Dealer Code no.</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd81"><FONT class="brcFt0">Place:BOMBAY</FONT></TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=2 class="brcTd69"><FONT class="brcFt0">alloted to the bank by RBI:</FONT></TD>
	<TD class="brcTd82">
		<FONT class="brcFt0">
			<?=($brcFgnExDealerCodeNo!="")?$brcFgnExDealerCodeNo:""?>
		</FONT>
	</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=6 class="brcTd83"><FONT class="brcFt0">1.This is to certify that we have verified relevant export invoices,Cuistom attested EP copy of shipping bill and other</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=7 class="brcTd84"><FONT class="brcFt5" style="white-space:normal;">relevanbrcTdocuments of M/S . <?=$exporterAddress?></FONT></TD>
</TR>
<TR class="brcTr0">
	<TD colspan=6 class="brcTd83"><FONT class="brcFt0">We further certify that the particulars given in Column No. 1 to 17 have been verified and found to be correct.</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=6 class="brcTd83"><FONT class="brcFt0">We have also verified theFOB value mentioned in Col.14 above with reference to following documents:-</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr4">
	<TD colspan=3 class="brcTd80"><FONT class="brcFt4">(i)Bill Of Lading/PP Receipt/Airways Bill</FONT></TD>
	<TD colspan=3 class="brcTd85"><FONT class="brcFt4">(ii)Insurance Policy/Cover/Insurance Receipt</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
</TABLE>
<P class="brcP2"><FONT class="brcFt1">2.</FONT><FONT class="brcFt14">FOB actually realized and dae of realization of export proceeds are to be given in all cases except where consignment has been sent against confirmed irrevocable letter of credit or exports made against the Government of India/Exim Bank line of Credit or exports made under Deferred Payment/ Suppliers Line of Credit ConbrcTract backed by ECGC Cover .</FONT></P>
<TABLE cellpadding=0 cellspacing=0 class="brcTbl2">
<TR height=0>
	<TD width=14px></TD>
	<TD width=72px></TD>
	<TD width=160px></TD>
	<TD width=360px></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD colspan=3 class="brcTd87">An endorsement to that effect needs to be endorsed in BRC.</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">3.</TD>
	<TD colspan=2 class="brcTd89">We have also verified that the date of export is</TD>
	<TD class="brcTd90"><?=$brcExportDate?></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD colspan=2 class="brcTd89">*Applicable only in respect of exports by air</TD>
	<TD class="brcTd91">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">4.</TD>
	<TD colspan=3 class="brcTd87">This is to certify that we have certified the amount of the Commission Paid/payable, as declared above, by the exporter i.e</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd92"><?=$brcCertifyAmtDescr?></TD>
	<TD colspan=2 class="brcTd93">(in figures and words) with G.R. Forms and found to be correct.</TD>
</TR>
</TABLE>
<P class="brcP0"><FONT class="brcFt15">Note:</FONT></P>
<TABLE cellpadding=0 cellspacing=0 class="brcTbl3">
<TR class="brcTr0">
	<TD class="brcTd88">1.</TD>
	<TD class="brcTd94">Bank can issue a consolidated certificate (consignment wise) for more</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd94">than one consignment.</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">2.</TD>
	<TD class="brcTd94">F.O.B. actually realised and date of realisation of export proceeds are to be</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd94">given in all cases except where consignment has been sent against confirmed</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd94">irrevocable letter of credit.</TD>
	<TD class="brcTd96"><FONT class="brcFt5">(Signature of the Banker's)</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">3.</TD>
	<TD class="brcTd94">This shall be required wherever specifically prescribed in the policy/procedure.</TD>
	<TD class="brcTd97"><FONT class="brcFt16">Full address of the Bankers (Branch &City)</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd98">&nbsp;</TD>
	<TD class="brcTd99"><FONT class="brcFt5">Official Stamp</FONT></TD>
</TR>
</TABLE>
</DIV>
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
<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
<?php
	} // Num copy Ends here
?>
</body>
</html>