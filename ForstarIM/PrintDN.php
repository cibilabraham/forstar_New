<?php
// Debit Note
	require("include/include.php");
	//require_once ('lib/ExporterMaster_class.php');
	//$exporterMasterObj =	new ExporterMaster($databaseConnect);
	//require_once ('components/base/ExporterMaster_model.php');
	//$exporter_m		= new ExporterMaster_model();


	$selInvoiceId 	= $g["invoiceId"];
	$selcompanyId	= $g["companyDetail"];
	$print = "Y";

	$debitNoteEditId = $selInvoiceId;
		$debitNoteMode = true;

		$invoiceRec	= $invoiceObj->find($debitNoteEditId);
		//$mainId 	= $invoiceRec[0];		
		$invoiceNo 	= $invoiceRec[1];
		$invDate	= $invoiceRec[2];
		$invoiceDate = dateFormat($invDate);
		$selInvDate = ($invoiceNo=="" || $invoiceNo==0)?date('y-m-d'):$invDate;
		$invYearRange = getFinancialYearRange($selInvDate);

		$exporter			= $invoiceRec[25];
		$unitid=$invoiceRec[31];
		$invoiceunitno=$plantandunitObj->find($unitid);
				$unitno=$invoiceunitno[1];
		$unitalphacode=$invoiceRec[32];
		$exporterName		= $exporterMasterObj->getExporterName($exporter);
		$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporter);
		if (($unitid!="") && ($unitid!=0))
		{
			//if ($exporterAlphaCode=="FFFPL")
				//{
					//$exporterAlphaCode="FFF";
				//}
				$exporterAlphaCode=$unitalphacode;
		$displayInvNum = $exporterAlphaCode."/"."U-$unitno"."/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		}
		else {
		$displayInvNum = $exporterAlphaCode."/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		}

		list($sizeVessalRecs, $vessalDetails, $containerType, $sailingDate, $shippingLine, $shippingCompanyCity, $shippingCompanyAddress) = $invoiceObj->getContVessalRecs($debitNoteEditId);
		$containerRecs = $invoiceObj->getContainerRecs($debitNoteEditId);
		$containerNoArr = array();
		$containerNos = "";
		foreach ($containerRecs as $rec) {
			$containerNo 	= $rec[2];
			$containerNoArr[] = $containerNo;
		}
		if (sizeof($containerNoArr)>0) $containerNos = implode(",",$containerNoArr);

		$billLaddingNo		= $invoiceRec[22];
		//$billLaddingDate	= ($invoiceRec[23]!='0000-00-00')?dateFormat($invoiceRec[23]):"";
		$billLaddingDate	= ($invoiceRec[23]!='0000-00-00')?date('d.m.Y', strtotime($invoiceRec[23])):"";
		
		$purchaseOrderId = $invoiceRec[6];
		list($poNo, $poDate, $dischargePort, $paymentMode, $paymentTerms, $portName, $modeOfCarriage, $finalDestCountry, $otherBuyer) = $invoiceObj->getPurchaseOrderRec($purchaseOrderId);


		$dnRec = $invoiceObj->findDNRec($debitNoteEditId);
		if (sizeof($dnRec)>0)
		{
			$dnFreight		= $dnRec[1];
			$dnBkgFreight	= $dnRec[2];
			$dnExRate		= $dnRec[3];
			$dnTotalBkg		= $dnRec[4];
		}
			$numCopy = 1;

			$companyDetail=$billingCompanyObj->find($selcompanyId);
			$companyContactDetailsRecs 			= $billingCompanyObj->findContactdetail($selcompanyId);
			if(sizeof($companyContactDetailsRecs)>0)
			{
				$telephoneNo=''; $mobileNo=''; $fax='';
				foreach($companyContactDetailsRecs as $cdt)
				{
					if($cdt[1]!='')
					{
						if($telephoneNo=='')
						{
							$telephoneNo=$cdt[1];
						}
						else
						{
							$telephoneNo.=','.$cdt[1];
						}
					}
					if($cdt[2]!='')
					{
						if($mobileNo=='')
						{
							$mobileNo=$cdt[2];
						}
						else
						{
							$mobileNo.=','.$cdt[2];
						}
					}
					if($cdt[3]!='')
					{
						if($fax=='')
						{
							$fax=$cdt[3];
						}
						else
						{
							$fax.=','.$cdt[3];
						}
					}
				}
			}
			//printr($telephoneNo);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Debit Note <?=" #NB/".$displayInvNum;?></title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>
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
<form name="frmPrintDN" id="frmPrintDN" action="PrintDN.php">
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
<table width='90%' cellspacing='1' cellpadding='1'  align='center' border="0" >
<tr><td height="35"></td></tr>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="17" align="right" class="listing-head" ></td>
  </tr>  
  <tr> 
    <td colspan="17" class="listing-head" STYLE="border-top: 1px solid #f2f2f2; border-left: 1px solid #f2f2f2; border-right: 1px solid #f2f2f2; border-bottom: 1px solid #f2f2f2;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>											
											<input type="hidden" name="dnInvoiceMainId" id="dnInvoiceMainId" value="<?=$debitNoteEditId;?>" readonly />										
											<tr>
											  <td colspan="2" height="10"></td>
										  </tr>	
	<tr>
		<td colspan="2" style="padding-left:10px; padding-right:10px;" align="center">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f2f2f2" align="center">
   <tr bgcolor="white">
    <td align="center" class="listing-head" colspan="17"><font size="5"><?=$companyDetail[1]?> <?php /*$debitNoteArr["Name"]*/ ?></font></td>
  </tr>
  <tr bgcolor="white">
    <td align="LEFT" class="listing-head" colspan="17">&nbsp;</td>
  </tr>
  <tr bgcolor="white">
    <td align="center" class="listing-item" colspan="17" style="text-transform:uppercase"><?=$companyDetail[2].','.$companyDetail[3].','.$companyDetail[4].','.$companyDetail[5]?>
	<?/*=$debitNoteArr["ADDR1"]*/?></td>
  </tr>
  <tr bgcolor="white">
    <td align="center" class="listing-item" colspan="17" style="text-transform:uppercase">
	<? if($telephoneNo!=''){ echo "Tel:".$telephoneNo; }else { echo ''; } ?> 
	<? if ($telephoneNo!='' && $fax!='') { echo ", "; } ?>
	<? if($fax!=''){ echo "FAX: ".$fax;} else { echo ''; } ?>
	<? if ($mobileNo!='' && $fax!='') { echo "<br/>"; } ?>
	<? if($mobileNo!=''){ echo "Cell: ".$mobileNo;} else { echo ''; } ?>
	<?/*=$debitNoteArr["CONTACT_NUMBER"]*/?></td>
  </tr>
  <tr bgcolor="white">
    <td align="RIGHT" class="listing-head" colspan="17"></td>
  </tr>
</table>
		</td>
	</tr>
	<tr><td height="70"></td></tr>
	<tr>
		<td colspan="2" style="padding-left:10px; padding-right:10px;">
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">PAN NO:</td>
								<td class="export-listing-item"><?=$companyDetail[18]?><!--AAAFN9648P--></td>
							</tr>			
						</table>
					</td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">DEBIT NOTE NO:</td>
								<td class="export-listing-item">NB/<?=$displayInvNum?></td>
							</tr>			
							<tr>
								<td class="export-print-listing-head">DATED:</td>
								<td class="export-listing-item"><?=($invDate!='0000-00-00')?date('d.m.Y', strtotime($invDate)):""?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">To,</td>
								<td></td>
							</tr>	
							<tr>								
								<td colspan="2" class="export-listing-item">M/S. <?=$shippingLine?>
									<?php if ($shippingCompanyAddress!="") {?><br><?=$shippingCompanyAddress?><?}?>
									<br><?=$shippingCompanyCity?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="30"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">Dear Sir</td>
								<td></td>
							</tr>	
							<tr>
								<td colspan="2" class="export-listing-item">Being freight brokerage on account of shipment of container A/C<br>M/S <?=$exporterName?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="30"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td >
									<table cellpadding="4" cellspacing="0" border="0" style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000" >
										<tr>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>Vessel Name</td>
											<td class="export-listing-item" nowrap><?=$vessalDetails?></td>
										</tr>
										<tr>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>Container No</td>
											<td class="export-listing-item" nowrap><?=$containerNos?></td>
										</tr>
										<tr>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>B/L No and Date</td>
											<td class="export-listing-item" nowrap>
											<?
												if (!preg_match("/^[0]*$/",trim($billLaddingNo))) {
													echo $billLaddingNo." DTD ".$billLaddingDate;
												}
											?>
											</td>
										</tr>
									</table>
								</td>
							</tr>							
						</table>
					</td>
				</tr>
				<tr><td height="35"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td >
									<table cellpadding="5" cellspacing="0" border="0" style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000" >
										<tr align="center">
											<td height="35" style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>Exp Invoice No</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>PORT</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>FREIGHT</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>BKG (2%)</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head" nowrap>Ex.Rate</td>
											<td class="export-print-listing-head">BROKERAGE<br>(Rs.)</td>
										</tr>										
										<tr>
											<td style="border-right:1px solid #000000;" class="export-listing-item" nowrap><?=$displayInvNum?></td>
											<td style="border-right:1px solid #000000;" class="export-listing-item" nowrap><?=$dischargePort?></td>
											<td style="border-right:1px solid #000000;text-align:right;" class="export-listing-item" nowrap>
												<?=($dnFreight!=0)?$dnFreight:"";?>
											</td>
											<td style="border-right:1px solid #000000;text-align:right;" class="export-listing-item" nowrap>
												<?=$dnBkgFreight?>
											</td>
											<td style="border-right:1px solid #000000;text-align:right;" class="export-listing-item" nowrap>
												<?=($dnExRate!=0)?$dnExRate:"";?>
											</td>
											<td class="export-listing-item" style="text-align:right;" nowrap>
												<?=$dnTotalBkg?>
											</td>
										</tr>
									</table>
								</td>
							</tr>							
						</table>
					</td>
				</tr>	
				<tr><td height="25"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">Rupees: </td>
								<td class="export-listing-item">
								<?
								if ($dnTotalBkg>0) {
									$cExToWords = "";
										list($ceNum, $ceDec) = explode(".", $dnTotalBkg); 
										$cExToWords .= convertNum2Text($ceNum);
										if($ceDec > 0) {
											$cExToWords .= " Paise ";
											$cExToWords .= convertNum2Text($ceDec);
										}										
										echo ucfirst($cExToWords)." only";	
								}
								?>
								</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">KINDLY RELEASE BROKERAGE AT EARLIEST</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">THANKING YOU</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">YOURS TRULY</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">For <?=$companyDetail[9]?><!--For NAIR BROTHERS--></td>
							</tr>								
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
											<tr>
												<td  height="10" ></td>
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
</form>	
<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
<?php
	} // Num copy Ends here
?>
</body>
</html>