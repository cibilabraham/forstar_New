<?php	
	require("include/include.php");
		
	$dateFrom = $g["dateFrom"];
	$dateTill = $g["dateTill"];
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	$invoiceType 		= $g["invoiceType"];
	$reportType 		= $g["reportType"];
	$selTransporter 	= $g["selTransporter"];
	$selDistributorId 	= $g["selDistributor"];
	$selState		= $g["selState"];
	$selCity		= $g["selCity"];
	$selCityArr 		= $g["selCityArr"];
	$billType 		= $g["billType"];
	$statusType		= $g["statusType"]; 

	if ($selTransporter) {
		$transporterRec		= $transporterMasterObj->find($selTransporter);
		$transporterName	= stripSlash($transporterRec[2]);
	}
	if ($selDistributorId) {
		$distributorRec		= $distributorMasterObj->find($selDistributorId);	
		$distriName		= stripSlash($distributorRec[2]);
	}

	$displayHead = "";
	if ($selTransporter) $displayHead = $transporterName;
	else if ($selDistributorId) $displayHead = $distriName;
		
	if ($fromDate!="" && $tillDate!="") {
		$transporterInvoiceRecords = $transporterReportObj->fetchTransporterInvoiceRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $billType, $statusType);
	}

	# Get Distributor Rec
	$distributorRec		= $distributorMasterObj->find($selDistributorId);
	$distributorName	= stripSlash($distributorRec[2]);


	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Transporter Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";
	}
	function printThisPage(printbtn)
	{
		document.getElementById("printButton").style.display="none";
		window.print();
		setTimeout("displayBtn()",2000);
		//document.getElementById("printButton").style.display="block";
	}
</script>
<style type="text/css" media="print">
@page {
  size: A4 landscape;
}
</style>
</head>
<body >
<div>
<form name="frmPrintTransporterReport">
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	# Sales Order Report
	if (sizeof($transporterInvoiceRecords)) {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor="white">
    <td colspan="17" align="center">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<TD align="center">
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD class="printPageHead" style="line-height:normal;"><font size="4px"><?=FIF_COMPANY_NAME?></font></TD>
					</TR>
					<TR>
						<TD class="printPageHead" style="font-size:11px;text-align:center;" valign="top"><?=FIF_SUB_HEAD?></TD>
					</TR>
				</table>
			</TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS1?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS2?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_FAX?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_EMAIL?></TD>
		</tr>
	</table>
</TD>
</tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" height="5"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='center' colspan='2'><?=strtoupper($displayHead)?><br/>
		<span style="font-size:11px;">
		 PERIOD FROM <?=$dateFrom?> TO <?=$dateTill?> 
		</span>
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="printPageHead" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="printPageHead"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" style="padding-left:5px;padding-right:5px;">
<table width="95%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?php
	if ($transporterInvoiceRecords>0) {
		$rowHeadStyle = "padding-left:2px; padding-right:2px;font-size:10px;line-height:normal;";
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
			<th nowrap="nowrap" class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Sr.No</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Bill No</th>
			<? if ($selDistributorId || $selState) {?>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Transporter</th>
			<? }?>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Location</th>
                	<th nowrap="nowrap" class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Date</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Docket No.</th>
			<th align="center" class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Invoice No</th>
                	<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Inv Value</th>
			<?php 
				if ($billType!='OC') {
			?>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Weight</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Rate</th>			
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">FOV</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Docket <br/>Charges</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">ODA<br/>Charges</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Sur-<br/>Charge</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Sub:<br/>Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Service<br/> Tax</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Gr.<br/> Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Actual<br/>Cost</th>
			<?php
				} else {
			?>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Octroi %</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Octroi Value</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Serv Tax</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Grand Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Actual<br/>Cost</th>
			<?php
				}
			?>
        </tr>	
      <?php
		$numRows	= 14; // Setting No.of rows 14
		$j = 0;
		$transporterInvoiceRecSize = sizeof($transporterInvoiceRecords);
		$totalPage = ceil($transporterInvoiceRecSize/$numRows);
		$grandTotalActualCost = 0;
		foreach ($transporterInvoiceRecords as $tir) {
			$i++;
			$salesOrderId		= $tir[0];
			$salesOrderNo		= $tir[1];
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.','');	// Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			$transporterRateListId  = $tir[22];
			$billNo = ($billType!='OC')?$tir[23]:$tir[45];
			$totalWt		= $tir[25];						
			$ratePerKg		= $tir[26];
			$freightCost	= $tir[27];			
			$FOV		= $tir[28];
			$docketRate	= $tir[29];
			$octroiRate	= $tir[30];
			$odaRate	= $tir[49];
			$surcharge	= $tir[50];
			$total		= $tir[31];
			$serviceTaxRate	= ($billType!='OC')?$tir[32]:$tir[42];
			$grandTotal 	= ($billType!='OC')?$tir[33]:$tir[43];
			$totalTransporterAmt += $grandTotal;
			if ($transporterId) {
				$transporterRec		= $transporterMasterObj->find($transporterId);
				$transporterName	= stripSlash($transporterRec[2]);
			}	
			$octroiPercent = $tir[37];
			$actualCost	= ($billType!='OC')?$tir[38]:$tir[44];
			$grandTotalActualCost += $actualCost;
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
	  	<?
		if ($i%$numRows==0 && $transporterInvoiceRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="10"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10" colspan="17"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=FIF_COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			<?=strtoupper($displayHead)?> REPORT - Cont.</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		</td>		 
		 </tr>
	</table></td>
	    </tr>
	
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>	  
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White">
<td colspan="17" align="center" style="padding-left:5px;padding-right:5px;">
	<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<tr bgcolor="#f2f2f2">
			<th nowrap="nowrap" class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Sr.No</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Bill No</th>
			<? if ($selDistributorId || $selState) {?>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Transporter</th>
			<? }?>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Location</th>
                	<th nowrap="nowrap" class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Date</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Docket No.</th>
			<th align="center" class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Invoice No</th>
                	<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Inv Value</th>
			<?php 
				if ($billType!='OC') {
			?>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Weight</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Rate</th>			
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">FOV</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Docket <br/>Charges</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">ODA<br/>Charges</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Sur-<br/>Charge</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Sub:<br/>Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Service<br/> Tax</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Gr.<br/> Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Actual<br/>Cost</th>
			<?php
				} else {
			?>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Octroi %</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Octroi Value</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Serv Tax</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>">Grand Total</th>
			<th class="listing-head" style="<?=$rowHeadStyle?>" nowrap="true">Actual<br/>Cost</th>
			<?php
				}
			?>
	</tr>
   <?php
	#Main Loop ending section 
			
	       }		
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
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="99%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?php
	}
	# Sales Order Report Ends Here
?>
</form>	
</div>
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>