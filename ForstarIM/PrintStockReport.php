<?php	
	require("include/include.php");
	if ($g["selDate"]!="") {
		$recordsDate	=	$g["selDate"];		
	} else if($p["selDate"]=="") {
		$recordsDate	=	date("d/m/Y");
	} else {
		$recordsDate	=	$p["selDate"];
	}

	$stockReportType=$g["stockReportType"];
	
	$dateS		=	explode("/",$recordsDate);
	$selectDate	=	$dateS[2]."-".$dateS[1]."-".$dateS[0];
	//$selectDate	= mysqlDateFormat($recordsDate);
	$lastDate  	= date("Y-m-d",mktime(0, 0, 0,$dateS[1],$dateS[0]-1,$dateS[2])); //latest record before the date

	# List all Stocks
	$stockRecords = $stockreportObj->fetchStockRecords($selectDate, $stockReportType);
	

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Stocks Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintPurchaseOrder">
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<th>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="5"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" height="5" ></td>
  </tr>
 <!-- <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=REG_NO?></td>
  </tr>	-->
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_ADDRESS?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_PHONE?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="printPageHead" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='3'>STOCKS REPORT ON <?=$recordsDate?> </td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
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
  <!--tr bgcolor=white> 
    <td colspan="17" align="center" class="printPageHead">SUMMARY OF ITEMS</td>
  </tr-->
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" Style="padding-left:5px;padding-right:5px;" >
<table width="95%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if (sizeof($stockRecords)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        	<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Stock Item </th>
                <th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Opening Balance Qty </th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Accepted Qty</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Used Qty</td>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Closing Balance Qty</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Re-Order Point</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Has Supplier(s)</th>
      </tr>
      <?
		$numRows	=	14; // Setting No.of rows
		$j = 0;
		$stockRecSize = sizeof($stockRecords);		
		$totalPage = ceil($stockRecSize/$numRows);

		$totalAmount = "";
		foreach ($stockRecords as $sr) {
			$i++;
			$stockId	=	$sr[0];
			$stockName	=	stripSlash($sr[1]);
			$quantity	=	$sr[2];
			$acceptedQty 	=	$sr[3];
			$usedQty	=	$sr[4];
			#Find the opening Qty
			$openingQty = $stockreportObj->getOpeningQty($stockId, $lastDate);
			$closingBalanceQty = ($openingQty + $acceptedQty)- $usedQty;

			#find the Reorder Point
			list($reOrderPoint, $actualQuantity) = $stockreportObj->findReOrderPoint($stockId);
			//echo "$actualQuantity<$reOrderPoint";
			$displayClosingQty= "";
			$displayTitle = "";
			if ($closingBalanceQty<$reOrderPoint) {
				$displayClosingQty = "<span style=\"color:#FF0000\">".$closingBalanceQty."</span>";
				$displayTitle = "This stock is below Re-order Point";
			} else {
				$displayClosingQty  = $closingBalanceQty;
				$displayTitle = "";
			}

				$suppCount = sizeof($stockreportObj->checkSupplierExistForStock( $stockId ));
				if( $suppCount > 0 ) $displaySupplier = '<IMG SRC="images/y.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="">';
				else $displaySupplier = '<IMG SRC="images/x.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="">';
			

		?>
      <tr bgcolor="#FFFFFF">
		<td  height='30' class="listing-item" align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap><?=$stockName?></td>
               <td  height='30' class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$openingQty?></td>
		<td  height='30' class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$acceptedQty?></td>
		<td  height='30' class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$usedQty?></td>
		<td  height='30' class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$displayClosingQty?></td>
		<td  height='30' class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$reOrderPoint?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$displaySupplier?></td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $stockRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="95%" cellpadding="0" cellspacing="0">
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
	  <table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead"><table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			STOCKS REPORT ON <?=$recordsDate?> - Cont.</td>
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
	  <tr bgcolor="White"><td colspan="17" align="center" Style="padding-left:5px;padding-right:5px;">
	  	  <table width="95%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" >
 		<tr bgcolor="#f2f2f2" align="center">
        	<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Stock Item </th>
                <th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Opening Balance Qty </th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Accepted Qty</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Used Qty</td>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Closing Balance Qty</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Re-Order Point</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" >Has Supplier(s)</th>
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
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center"><table width="95%" cellpadding="0" cellspacing="0">
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