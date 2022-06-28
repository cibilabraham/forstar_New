<?php	
	require("include/include.php");

	$selProduct = $g["selProduct"];
	$selBatch = $g["selBatch"];

	if ($selProduct) {
		$getProductBatchRecords = $productBatchReportObj->getProductBatchRecords($selProduct);
		#Get ingredient Recs
		$productIngredientRecs = $productBatchReportObj->fetchAllIngredients($selBatch);
		list($productGmsPerPouch, $fixedQty, $pouchPerBatch, $startTime, $endTime, $phFactor, $foFactor, $created) = $productBatchReportObj->getProductBatchSummaryRec($selBatch);

		#Gms per Pouch
		$gravyGmsPerPouch = number_format(($productGmsPerPouch-$fixedQty),2,'.','');

		$selDate	=	dateFormat($created);
		#Find the Selected product
		$selProductName = $productMasterObj->getProductName($selProduct);
		#Find the batch No
		$selBatchNo = $productBatchObj->getBatchNo($selBatch);

		$startTime	=	explode("-", $startTime);
		$selStartTime = $startTime[0].":".$startTime[1]."&nbsp;".$startTime[2];

		$stopTime	=	explode("-", $endTime);
		$selStopTime = $stopTime[0].":".$stopTime[1]."&nbsp;".$stopTime[2];


	}

?>
<html>
<head>
<title>Batch Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmProductBatchReport">
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>

<table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
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
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="3">BATCH REPORT</font>		   </td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
			<table>
				<TR>
					<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Code</TD>
					<TD class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$selBatchNo?></strong></TD>
				</TR>
			</table>
		     </td>		 
		 </tr>
	</table>	</td>
  </tr>
  <tr bgcolor=white>
	
	<td align="LEFT" valign="top" width='100%'>
	<table width="99%" cellspacing="1" bgcolor="#999999" cellpadding="3" align="center" class="print">
			     <tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Product:</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$selProductName?></strong></td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selDate?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Net Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$productGmsPerPouch?>&nbsp;gm</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Start Time:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selStartTime?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Fixed Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap=""><?=$fixedQty?>&nbsp;gm</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">End Time:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selStopTime?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Gravy Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$gravyGmsPerPouch?>&nbsp;gm</td>
				<? if ($foFactor==0) {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">No.of Bottles :</td>
				<? } else {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">No.of Pouches:</td>
				<? }?>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$pouchPerBatch?></strong></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="fieldName" style="padding-left:5px; padding-right:5px;" colspan="2"></td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;"></td>
				<? if ($foFactor==0) {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">PH Value:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$phFactor?></td>
				<? } else {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">F0 Value:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$foFactor?></td>
				<? }?>
                              </tr>
                                </table>	
	</td>
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
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head">SUMMARY OF INGREDIENTS USED </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if (sizeof($productIngredientRecs)) {
		$i	=	0;
	?>
     <tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">No.</td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">RM/Ingredients</td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">Qty Used<br>(Before Cleaning)</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Yield(%)</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Purchase Price of RM</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Cost of RM</td>
                              </tr>
	  
      <?	
		$numRows=14;
		$j = 0;
		$productBatchReportSize = sizeof($productIngredientRecs);
		
		$totalPage = ceil($productBatchReportSize/$numRows);
		$totalIngredientCost = 0;
		foreach ($productIngredientRecs as $pir)	{
			$i++;
			$ingredientId	= $pir[2];
			$ingredientName = $pir[5];
			$quantity	= $pir[3];
			$lastPrice = $pir[4];
			$ingredientCost = $quantity*$lastPrice;
			$totalIngredientCost += $ingredientCost;
		
		?>
     <tr bgcolor="#FFFFFF">
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="center"><?=$i?></td>
                                	<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left"><?=$ingredientName?></td>
                                        <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$quantity?></td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap><?=$quangtity?></td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$lastPrice?></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">	<?=$ingredientCost?></td>
                                </tr>
	  
	  	<?
	  
		if($i%$numRows==0 && $productBatchReportSize!=$numRows){
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td>
		<table width="98%" cellpadding="3">
        <tr>
        <td colspan="6" height="10"></td>
        </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Sign of Production In-charge </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Sign of Chef </td>
      </tr>
	  <tr>
		<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><?=$date?></td>
        </tr>
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
    </table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
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
    <td colspan="17" class="listing-head" align="center" ><font size="3"><?=$companyArr["Name"];?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr></table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head"><table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="2">BATCH REPORT</font> - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <table>
				<TR>
					<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Code</TD>
					<TD class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$selBatchNo?></strong></TD>
				</TR>
			</table></td>		 
		 </tr>
	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		<table width="99%" cellspacing="1" bgcolor="#999999" cellpadding="3" align="center" class="print">
			     <tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Product:</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$selProductName?></strong></td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selDate?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Net Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$productGmsPerPouch?>&nbsp;gm</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Start Time:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selStartTime?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Fixed Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap=""><?=$fixedQty?>&nbsp;gm</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">End Time:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selStopTime?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Gravy Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$gravyGmsPerPouch?>&nbsp;gm</td>
				<? if ($foFactor==0) {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">No.of Bottles :</td>
				<? } else {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">No.of Pouches:</td>
				<? }?>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$pouchPerBatch?></strong></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="fieldName" style="padding-left:5px; padding-right:5px;" colspan="2"></td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;"></td>
				<? if ($foFactor==0) {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">PH Value:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$phFactor?></td>
				<? } else {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">F0 Value:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$foFactor?></td>
				<? }?>
                              </tr>
                                </table>
				</td>
	    </tr>
	  
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>
	  <tr bgcolor=white> 
   		 <td colspan="17" align="center" class="listing-head">SUMMARY OF COUNTWISE SUPPLY RECEIVED </td>
  		</tr>
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr><td colspan="17" align="center" class="fieldName">
	  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	   		 <tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">No.</td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">RM/Ingredients</td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">Qty Used<br>(Before Cleaning)</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Yield(%)</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Purchase Price of RM</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Cost of RM</td>
                              </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
      <tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;" align="center">Total Cost</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><?=$totalIngredientCost?></strong></td>
                              </tr>
				<? 
				$costPerPouch = number_format(($totalIngredientCost/$pouchPerBatch),3,'.','');
				?>
				 <tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;" align="center">Cost/Pouch</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><?=$costPerPouch?></strong></td>
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
    <td colspan="17" align="center" class="fieldName"><table width="98%" cellpadding="3">
      
      <tr>
        <td colspan="6" height="10"></td>
        </tr>   
	 <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Remark: </td>
        <td class="fieldName" nowrap="nowrap" valign="top"> </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp; </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp; </td>
      </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Sign of Production In-charge </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Sign of Chef </td>
      </tr>
	  <tr>
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:5px;"><?=date("d/m/Y");?></td>
        </tr>
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" >(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>