<?php	
	require("include/include.php");

	$retId	=	$g["retId"];

	#Find Issuance Records
	$stockRetRec	=	$stockReturnObj->find($retId);		
	$requestNo		=	$stockRetRec[1];	

	#Get all issued Items
	$retRecs = $stockReturnObj->fetchAllStockItem($retId);
?>
<html>
<head>
<title>STOCK ISSUANCE DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintDailyCatchReportMemo">
<!--table width="65%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table-->
<table width='65%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="4"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" height="5"></td>
  </tr>
<!--<tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=REG_NO?></td>
  </tr>	-->
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_ADDRESS?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_PHONE?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			STOCK RETURN DETAILS</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <div id="printMsg">REQUEST NO:&nbsp;<?=$requestNo?></div></td>
		 </tr></table></td>
  </tr>
<tr bgcolor="White"><TD height="5"></TD></tr> 
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="listing-head" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if (sizeof($retRecs)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Quantity</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap>Total <br>Scrap Value</th>
		
      </tr>
      <?
	  	
		$numRows	= 14; // Setting No.of rows
		$j = 0;
		$stkretRecsize = sizeof($retRecs);
		
		$totalPage = ceil($stkretRecsize/$numRows);
		
		$totalAmount = "";
		$subTotalAmt = 0;
		$totQty = 0;
		$totScrapVal = 0;
		foreach ($retRecs as $por) {
			$i++;
			$editStockId	=	$por[2];
			$stockRec	=	$stockObj->find($editStockId);
			$stockName	=	stripSlash($stockRec[2]);			
			$qty	=	$por[4];
			$sv	=	$por[3];
			$totl = 	$qty*$sv;
			$subTotalAmt = $subTotalAmt + $totl;
			$totQty = $totQty + $qty;
			$totScrapVal = $totScrapVal+$sv;
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$stockName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$qty?></td>
		 <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align='right'><?=$sv?></td>
        
      </tr>
	  	<?
		if ($i%$numRows==0 && $stkretRecsize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF"><td height="10"></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='65%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head"><table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			STOCK RETURN DETAILS - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		<div id="printMsg">No:<?=$requestNo?></div></td>		 
		 </tr>
	</table></td>
	    </tr>
	  
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>
	  <tr bgcolor=white> 
   		 <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS
                                        </td>
  		</tr>
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White"><td colspan="17" align="center" class="fieldName">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 		<tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Quantity</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Total <br>Scrap Value</th>
		
      </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
      <tr>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Total</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" align='right'><?=$totQty;?></th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" align='right'><?=number_format($totScrapVal,2,'.','');?></th>
		
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
    <td colspan="17" align="center" class="fieldName" height="10"></td>
  </tr>
</table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>