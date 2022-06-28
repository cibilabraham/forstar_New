<?php	
	require("include/include.php");
		
	$dateFrom = $g["dateFrom"];
	$dateTill = $g["dateTill"];
	$reportType = $g["reportType"];
	
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	
	if ($fromDate!="" && $tillDate!="") {
		$productionPlanningRecords = $productionPlanningReportObj->getProductionPlannedRecords($fromDate, $tillDate, $reportType);
	}

	

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Production Planning Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn)
{
	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintStockIssuanceReport">
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
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
    <td colspan="17" align="RIGHT"  height="5"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>PRODUCTION PLANNING REPORT<br>FROM <?=$dateFrom?> TO <?=$dateTill?> 
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
	<!--tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' >
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>Of M/s <?=$supplierName?> From <?=$dateFrom?> To <?=$dateTill?> </td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr-->
  
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
  <!--tr bgcolor=white> 
    <td colspan="17" align="center" class="printPageHead">SUMMARY OF ITEMS</td>
  </tr-->
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <th colspan="17" align="center">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($productionPlanningRecords>0) {
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Ingredient</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Qty<br>(Kg)</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Price</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Total Amt<br>(Rs.)</th>	
        </tr>	
      <?
		$numRows	=	14; // Setting No.of rows
		$j = 0;
		$stockIssuanceRecSize = sizeof($productionPlanningRecords);
		$totalPage = ceil($stockIssuanceRecSize/$numRows);
		$grandTotalAmt = 0;
		//foreach ($productionPlanningRecords as $ppr) {
		foreach ($productionPlanningRecords as $ingredientId=>$ppr) {
			$i++;		
			//$ingredientId	= $ppr[1];	
			//$ingName	= $ppr[2];	
			$quantity	= $ppr[0];
			$ingName	= $ppr[1];
			# Find the Lowest Price of the Ing
			$unitPrice	= $productionPlanningReportObj->getIngPrice($ingredientId);
			$totalAmt	= number_format(($quantity*$unitPrice),2,'.','');
			$grandTotalAmt += $totalAmt;	
	
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
			<?=$ingName?>			
		</td>
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right">
			<?=$quantity?>
		</td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">
			<?=$unitPrice?>
		</td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">
			<?=$totalAmt?>
		</td>				
      </tr>
	  	<?
		if ($i%$numRows==0 && $stockIssuanceRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="85%" cellpadding="0" cellspacing="0">
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
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			PRODUCTION PLANNING REPORT - Cont.</td>
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
	  <tr bgcolor="White"><td colspan="17" align="center">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Ingredient</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Qty<br>(Kg)</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Price</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Total Amt<br>(Rs.)</th>	
        </tr>	
   <?
	#Main Loop ending section 
			
	       }		
	}
   ?>
      <!--tr bgcolor="#FFFFFF">
        <td height='30' colspan="3" nowrap="nowrap" class="printPageHead" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr-->
	<tr bgcolor="white">
		<TD class="listing-head" colspan="3" align="right">Grand Total:</TD>
		<TD class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?=number_format($grandTotalAmt,2,'.','');?></strong>
		</TD>
	</tr>
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="0" cellspacing="0">
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
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>