<?php	
	require("include/include.php");
	$debitNoteRecs = array();
		
	$dateFrom = $g["dateFrom"];
	$dateTill = $g["dateTill"];
	$fromDate	= mysqlDateFormat($dateFrom);
	$tillDate	= mysqlDateFormat($dateTill);

	$selShippingLineId	= $g["selShippingLine"];
	
	
	if ($fromDate!="" && $tillDate!="") {
		$debitNoteRecs = $dnReportObj->debitNoteRecs($fromDate, $tillDate, $selShippingLineId);	
	}
		
	
	$pagePrintHead = "NAIR BROTHERS";

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Debit Note Report</title>
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
</head>
<body>
<form name="frmPrintDistributorReport">
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	# Debit Note Report
	if (sizeof($debitNoteRecs)>0) {
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
	<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f2f2f2" align="center">
   <tr bgcolor="white">
    <td align="center" class="listing-head" colspan="17"><font size="6"><?=$debitNoteArr["Name"]?></font></td>
  </tr>
  <tr bgcolor="white">
    <td align="LEFT" class="listing-head" colspan="17">&nbsp;</td>
  </tr>
  <tr bgcolor="white">
    <td align="center" class="listing-item" colspan="17" style="text-transform:uppercase"><?=$debitNoteArr["ADDR1"]?></td>
  </tr>
  <tr bgcolor="white">
    <td align="center" class="listing-item" colspan="17" style="text-transform:uppercase"><?=$debitNoteArr["CONTACT_NUMBER"]?></td>
  </tr>
    <tr bgcolor="white">
    <td align="center" class="listing-item" colspan="17"><?=$debitNoteArr["Email"]?></td>
  </tr>
  <tr bgcolor="white">
    <td align="RIGHT" class="listing-head" colspan="17"></td>
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
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>DEBIT NOTE REPORT FROM <?=$dateFrom?> TO <?=$dateTill?> 
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
    <td colspan="17" align="center">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($debitNoteRecs>0) {
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>D/B<br>NO</th>		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>S/LINE</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>B/L NO</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>FREIGHT</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>BKG<br>(2%)</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>EX.RATE</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>BILL<br> AMOUNT</th>		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>GROSS</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>TDS</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>NET</th>		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>CHQ NO</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>DATE</th>
    </tr>	
      <?
		$numRows = 45; // Setting No.of rows
		$j = 0;
		$debitNoteRecSize = sizeof($debitNoteRecs);
		$totalPage = ceil($debitNoteRecSize/$numRows);

		$totBillAmt = $totGrossAmt = $totTdsAmt = $totNetAmt = $totFreight = $totBkgFreight = 0;
		foreach ($debitNoteRecs as $dnr) {
			$i++;
			$shippingLine = $dnr[1];
			$billLaddingNo		= $dnr[2];		
			$billAmt	= $dnr[4];
			$grossAmt	= $dnr[5];
			$tdsAmt		= $dnr[6];
			$netAmt		= $dnr[7];
			$chqNo		= $dnr[8];
			$chqDate	= ($dnr[9]!='0000-00-00' && $dnr[9]!="")?date('d.m.Y', strtotime($dnr[9])):"";
			$freight	= $dnr[10];
			$bkgFreight = $dnr[11];
			$exRate		= $dnr[12];
			$expInvNum	= $dnr[13];
			
			$totFreight		+= $freight;
			$totBkgFreight	+= $bkgFreight;
			$totBillAmt		+= $billAmt;
			$totGrossAmt	+= $grossAmt;
			$totTdsAmt		+= $tdsAmt;
			$totNetAmt		+= $netAmt;				
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$expInvNum;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$shippingLine;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
			<?php
				if (!preg_match("/^[0]*$/",trim($billLaddingNo))) {
					echo $billLaddingNo;
				}
			?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$freight;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$bkgFreight;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$exRate;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$billAmt;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$grossAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$tdsAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$netAmt;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$chqNo;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$chqDate;?></td>		
      </tr>
	  	<?
		if ($i%$numRows==0 && $debitNoteRecSize!=$numRows) {
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
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=$pagePrintHead?></font></td>
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
			DEBIT NOTE REPORT - Cont.</td>
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
	<tr bgcolor="#f2f2f2">
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>D/B<br> NO</th>		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>S/LINE</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>B/L NO</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>BILL<br> AMOUNT</th>		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>GROSS</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>TDS</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>NET</th>		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>CHQ NO</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px;" nowrap>DATE</th>
	</tr>
   <?
	#Main Loop ending section 
			
	       }		
	}
   ?>  
   <tr bgcolor="WHITE">		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap colspan="3" align="right">Total:</td>		
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap align="right"><b><?=number_format($totFreight,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap align="right"><b><?=number_format($totBkgFreight,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap align="right"><b><?=number_format($totBillAmt,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap align="right"><b><?=number_format($totGrossAmt,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap align="right"><b><?=number_format($totTdsAmt,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap align="right"><b><?=number_format($totNetAmt,2,'.','')?></b></td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>&nbsp;</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>&nbsp;</td>
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
<?
	}
	# Debit Note Report Ends Here
?>

</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
