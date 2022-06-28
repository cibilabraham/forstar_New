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
	$selStatus		= $g["selStatus"];

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
		$salesOrderRecords = $salesOrderReportObj->fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus);
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
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	# Sales Order Report
	if (sizeof($salesOrderRecords)) {
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
			<TD class="listing-item" align="center"><?=FIF_PHONE?></TD>
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
	if ($salesOrderRecords>0) {
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
			<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Sr.No</th>
			<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Date</th>			
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Location</th>
			<th align="center" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice No</th>
                	<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv Value</th>
			<? if ($selDistributorId || $selState) {?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Transporter</th>			
			<? }?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Docket No.</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Status</th>
        </tr>	
      <?
		$numRows	=	14; // Setting No.of rows 14
		$j = 0;
		$salesOrderRecSize = sizeof($salesOrderRecords);
		$totalPage = ceil($salesOrderRecSize/$numRows);
		$totalInvoiceValue = 0;
		foreach ($salesOrderRecords as $sor) {
			$i++;
			$salesOrderId		= $sor[0];
			//$salesOrderNo		= $sor[1];
			$soNo 	= $sor[1];		
			$invType = $sor[20];			
			$pfNo 	= $sor[21];
			$saNo	= $sor[22];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";
			$distributorId		= $sor[2];
			$soDate			= dateFormat($sor[3]);
			$despatchDate		= dateFormat($sor[4]);			
	
			$invoiceValue		= $sor[17];	// Grand Total Invoice Amt$sor[6];
			$cityId			= $sor[7];
			$grossWt		= $sor[9];
			$numBox			= $sor[10];			
			$docketNum		= $sor[12];

			$cityName		= $sor[15];
			$distributorName	= $sor[13];
			$transporterId		= $sor[11];
			$transporterName = "";		
			if ($transporterId) {
				$transporterRec		= $transporterMasterObj->find($transporterId);
				$transporterName	= stripSlash($transporterRec[2]);
			}
			
			$status = ($sor[16]=='C')?"<span style=\"color:#003300\">Complete</span>":"<span class='err1'>Pending</span>";

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
				//$status  = "<span style=\"color:Grey\">PENDING (Extended)</span>";
				$displayColor = "Grey";
			} else {
				if ($completeStatus=='C') {
					$status	= " COMPLETED ";
					//$status  = "<span style=\"color:#90EE90\">COMPLETED</span>";
					$displayColor = "#90EE90"; // LightGreen	
				} else if ($dateDiff<0) {
					$status = "DELAYED";
					//$status  = "<span style=\"color:#DD7500\">DELAYED</span>";
					$displayColor = "#DD7500"; // LightOrange
				} else {
					$status = "PENDING";	
					//$status  = "<span style=\"color:Black\">PENDING</span>";		
					$displayColor = "White";
				}
			}
			# ----------------------- Status Ends--------
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$i?>
		</td>		
		<td class="listing-item" nowrap height='25' style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
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
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$status?>			
		</td>		
      </tr>
	  	<?
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
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
			<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Sr.No</th>
			<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Date</th>			
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Location</th>
			<th align="center" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice No</th>
                	<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv Value</th>
			<? if ($selDistributorId || $selState) {?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Transporter</th>			
			<? }?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Docket No.</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Status</th>
	</tr>
   <?php
	#Main Loop ending section 
			
	       }		
	}
   ?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="5" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=number_format($totalInvoiceValue,2,'.',',')?></strong></td>
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="3" align="right"></td>		
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
<?
	}
	# Sales Order Report Ends Here
?>
</form>	
</div>
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
