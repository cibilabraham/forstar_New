<?php	
	require("include/include.php");

	# Find Average Period type
	list($averagePeriodType,$excessStockTolerance) = $stockHoldingCostReportObj->getAveragePeriodType();
	if ($averagePeriodType=='Q') {	
		$quarterlyType = "checked";
		$month = 4;
	} else if ($averagePeriodType=='H') {
		$halfYearlyType = "checked";  
		$month = 6;
	} else if ($averagePeriodType=='Y') { 
		$yearlyType = "checked";
		$month = 12;
	}
	
	# List all Stocks
	$stockRecords =	$stockHoldingCostReportObj->fetchAllRecords();
	

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
<form name="frmPrintStockHoldingCostReport">
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
    <td colspan="17" class="listing-head" align="center" ><font size="4"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" height="5" ></td>
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
    <td colspan="17" align="RIGHT" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='3'>STOCK HOLDING COST REPORT</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
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
  <!--tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS</td>
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
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Stock Item</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Qty <br>[A]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Stocking Freq<br>uency <br>(In Months)<br>[B] </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Average Con-<br>sumption <br> (Last <?=$month?> Months)<br>[C]</td>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Expected Consu-<br>mption<br>[D]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Average avoidable <br>Return Qty<br>[E]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Excess <br>Stock In<br> Hand<br>[F=A-(<br>(C-E)*B)]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit Price <br>[G]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Holding Cost<br>[A*G]</td>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Excess <br>Holding Cost<br>[F*G]</td>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Excess Holding (%)</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Price Fluc-<br>tuation Indicator<br>(%)</th>	
      </tr>
      <?
		$numRows	=	14; // Setting No.of rows
		$j = 0;
		$stockRecSize = sizeof($stockRecords);		
		$totalPage = ceil($stockRecSize/$numRows);

		$totalExcessHoldingcost = 0;
		$totalHoldingCost = 0;
		$excessHoldingCost = 0;
		foreach ($stockRecords as $sr) {
		$i++;
		$stockId	= $sr[0];
		$stockName	= stripSlash($sr[2]);
		$additionalHoldingPercent = $sr[10];
		$stockingPeriod	= $sr[11];

		#Find the opening Qty (A)
		$openingQty = $stockHoldingCostReportObj->getOpeningQty($stockId);
		# Stock Consumed Qty (B)		
		$averageConsumedQty = $stockHoldingCostReportObj->getStockConsumedQty($stockId, $averagePeriodType);
		# Expected Consumed Qty
		$expectedConsumedQty = $stockingPeriod*$averageConsumedQty;
		# Last Price  (E)
		$unitPrice = $stockHoldingCostReportObj->getUnitPriceOfStock($stockId);
		//echo "$stockId-$stockName-$unitPrice<br>";
		# get average return Qty (C)
		$averageReturnQty = $stockHoldingCostReportObj->getAverageReturnQty($stockId, $stockingPeriod);

		// Excess Stock in Hand (Cqty-(AConsumedQty-AverageReturnQty)=>D = A-(B-C))	
		$excessStockInHand = "";
		$calcExcessStockInHand	= $openingQty-(($averageConsumedQty-$averageReturnQty)*$stockingPeriod);
		if ($calcExcessStockInHand>0) $excessStockInHand = $calcExcessStockInHand;
		else $excessStockInHand = 0;

		// Holding Cost (CQty*Unit Price=>A*E)
		$holdingCost = $openingQty*$unitPrice;
		$totalHoldingCost += $holdingCost;

		// Excess holding Cost (Excess SH*$unit Price=>D*E)
		$excessHoldingCost = $excessStockInHand*$unitPrice;
		
		$totalExcessHoldingcost += $excessHoldingCost;
		// Excess Holding Percent "-$additionalHoldingPercent"
		$excessHoldingPercent = ((($openingQty-$averageConsumedQty)*100)/$openingQty);
		//echo "$averageConsumedQty-((($openingQty-$averageConsumedQty)*100)/$openingQty)-$additionalHoldingPercent<br>";		

		// Over Stock = Orange, under stock = Red, zero = Greeen
		
		if ($averageConsumedQty<=0) {
			$tdHPercentBgColor= "bgcolor=\"#ffffff\"";
			$excessHoldingPercent = 0;
		}
		
		
		$subtractExcessPercent = $additionalHoldingPercent-$excessStockTolerance;
		$addExcessPercent      = $additionalHoldingPercent+$excessStockTolerance;
		if (($excessHoldingPercent>=$subtractExcessPercent && $excessHoldingPercent<=$addExcessPercent) && $excessHoldingPercent!=0) {			
			$tdHPercentBgColor = "#008000";		// Green
		} else if ($excessHoldingPercent>0 && $excessHoldingPercent>=$addExcessPercent) {
			$tdHPercentBgColor = 	"#FFA500";	// orange	
		} else if ($excessHoldingPercent>0) {
			$tdHPercentBgColor = 	"#CC3300";	// Red	
		} 

		# Stock Item Price Variation
		$calcPriceFluctuationPercent = 0;
		list($currentStockPrice, $yearlyAveragePrice)= $stockHoldingCostReportObj->getStockItemPriceVariation($stockId);
		//$priceVariationAmt = $stockHoldingCostReportObj->getStockItemPriceVariation($stockId);
		if ($currentStockPrice>0)
		$calcPriceFluctuationPercent = (($yearlyAveragePrice-$currentStockPrice)*100)/$currentStockPrice;
		$priceVariationPercent = ($calcPriceFluctuationPercent)>0?number_format($calcPriceFluctuationPercent,0,'',''):"";
		$displayPriceVariation = "";
		if ($priceVariationPercent>0) {
			$displayPriceVariation = "<span style=\"color:#FF0000\">".$priceVariationPercent."</span>";
		} else {
			$displayPriceVariation 	= "<span style=\"color:#0ecd0e\">".$priceVariationPercent."</span>";
		}

		?>
      <tr bgcolor="#FFFFFF">
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;font-size:8pt;" align="left"><?=$stockName?></td>
               <td height='30' class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$openingQty?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;font-size:8pt;"><?=($stockingPeriod>0)?$stockingPeriod:"";?></td>
		<td height='30' class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=($averageConsumedQty>0)?number_format($averageConsumedQty,2,'.',','):"";?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;font-size:8pt;"><?=($expectedConsumedQty>0)?number_format($expectedConsumedQty,2,'.',','):"";?></td>	
		<td height='30' class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$averageReturnQty?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=number_format($excessStockInHand,2,'.','');?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$unitPrice?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;" ><?=($holdingCost>0)?number_format($holdingCost,2,'.',','):"";?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=($excessHoldingCost!=0)?number_format($excessHoldingCost,2,'.',','):"";?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;"><span style="color:<?=$tdHPercentBgColor?>; font-weight:bold;font-size:12px;"><?=($excessHoldingPercent>0)?number_format($excessHoldingPercent,0,'',','):"";?></span></td>		
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$displayPriceVariation;?></td>	
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
	    <td colspan="17" align="center" class="listing-head">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="3"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head"><table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			STOCK HOLDING COST REPORT - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
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
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Stock Item</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Qty <br>[A]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Stocking Freq<br>uency <br>(In Months)<br>[B] </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Average Con-<br>sumption <br> (Last <?=$month?> Months)<br>[C]</td>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Expected Consu-<br>mption<br>[D]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Average avoidable <br>Return Qty<br>[E]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Excess <br>Stock In<br> Hand<br>[F=A-(<br>(C-E)*B)]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit Price <br>[G]</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Holding Cost<br>[A*G]</td>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Excess <br>Holding Cost<br>[F*G]</td>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Excess Holding (%)</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Price Fluc-<br>tuation Indicator<br>(%)</th>	
      </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
      <!--tr bgcolor="#FFFFFF">
        <td height='30' colspan="3" nowrap="nowrap" class="listing-head" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr-->
	<tr bgcolor="White">
			<TD colspan="8" class="listing-head" align="right" height='30'>Grand Total:</TD>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" height='30'><?=number_format($totalHoldingCost,2,'.',',')?></td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" height='30'><?=number_format($totalExcessHoldingcost,2,'.',',')?></td>
			<td></td>
			<td></td>
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
    <td colspan="17" align="center"><table width="95%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right">
                          <table align="right" cellpadding="0" cellspacing="0">
	<TR>
		<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Printed by &nbsp;<span class="listing-item" style="line-height:normal;font-size:11px;"><?=$userName?></span></td>
		<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;On &nbsp;<span class="listing-item" style="line-height:normal;font-size:11px;"><?=date("j F Y, g:i A"); ?></span></td>
	
	</TR>
</table>
		</TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body>
</html>