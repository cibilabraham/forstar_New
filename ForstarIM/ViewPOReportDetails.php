<?php	
	require("include/include.php");

	$selPOId	=	$g["selPOId"];
	$status		=	$g["status"];

	#Find PO Records
	$pORec	=	$purchaseOrderInventoryObj->find($selPOId);
	$pOGenerateId		=	$pORec[1];
	$poNumber		=	$pORec[2];
	$supplierId		=	$pORec[3];

	#Supplier Rec
	$supplierRec		=	$supplierMasterObj->find($supplierId);
	$code			=	$supplierRec[1];
	$supplierName		=	stripSlash($supplierRec[2]);
	$supplierAddress	=	stripSlash($supplierRec[3]);
	$supplierPhone		=	stripSlash($supplierRec[4]);
	$vatNo			=	stripSlash($supplierRec[5]);
	$cstNo			=	stripSlash($supplierRec[6]);

	#Get all PO Items
	$purchaseOrderRecs = $purchaseOrderInventoryObj->fetchAllStockItem($selPOId, $poItem);
	
	if ($status=='R' || $status=='PC') {
		list($getGrnRecordId, $grnNo)	= $purchaseOrderReportObj->getGRNRec($selPOId); 
		$goodsReceiptRecs = $goodsreceiptObj->fetchAllStockItem($getGrnRecordId);
	}
?>
<html>
<head>
<title>PO REPORT DEtAILS</title>
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
<!-- Print start -->
<table width="75%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<!-- Print End -->
<table width='75%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="5"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" height="5"></td>
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
    <td colspan="17" align="RIGHT" class="printPageHead" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='90%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			PURCHASE ORDER DETAILS</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   <div id="printMsg">PO ID:&nbsp;<?=$pOGenerateId?></div></td>
		 </tr></table></td>
  </tr>
<tr bgcolor="White"><TD height="5"></TD></tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='90%' cellpadding='0' cellspacing='0' class="print" align="center">
         <tr>
           <td nowrap="nowrap" align='left' valign='top' style=" padding-left:8px;">
		   <table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
             <tr>
               <td nowrap="nowrap" class="printPageHead" colspan="2" height="25"><font size="2">Name, Address & Phone of Supplier</font> </td>
             </tr>
             <tr>
               <td class="listing-item" nowrap="nowrap" colspan="2" height="25"><font size="3">
                 <?=$supplierName?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-item" width='200' height="35" colspan="2"><font size="3">
                 <?=$supplierAddress?>
               </font></td>
             </tr>
		<tr>
               <td class="listing-item" width='200' height="55" colspan="2"><font size="3">
                 <?=$supplierPhone?>
               </font></td>
             </tr>
           </table></td>	
		   
		 </tr>
	</table>	</td>
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
    <td colspan="17" align="center" class="printPageHead">SUMMARY OF PO ITEMS</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="90%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if (sizeof($purchaseOrderRecs)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</th>
        <th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit Price</th>
        <th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Quantity</th>
	<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Total</th>
      </tr>
      <?
	  	
		$numRows	=	20; // Setting No.of rows
		$j = 0;
		$purchaseOrderRecSize = sizeof($purchaseOrderRecs);
		
		$totalPage = ceil($purchaseOrderRecSize/$numRows);

		$totalAmount = "";
		foreach ($purchaseOrderRecs as $por) {
			$i++;
			$editStockId	=	$por[2];
			$stockRec	=	$stockObj->find($editStockId);
			$stockName	=	stripSlash($stockRec[2]);
			$unitRate	=	$por[3];
			$editQuantity	=	$por[4];
			$total		=	$por[5];
			$totalAmount = $totalAmount + $total;
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$stockName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=number_format($unitRate,2);?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$editQuantity?></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$total?></td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $purchaseOrderRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>	
		<tr bgcolor="#FFFFFF">
		<td height="10"></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='75%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='90%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			<font size="2"></font>Purchase Order - Cont.</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		<div id="printMsg">No:<?=$pOGenerateId?></div></td>		 
		 </tr>
	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		<table width='90%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr><td>
		<table width="100%" cellpadding="0" cellspacing="3" class="tdBoarder">
        <tr> 
          <td valign="top"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="printPageHead" valign="top">Supplier:</td>
                <td class="listing-item" style="line-height:normal;" valign="top"><?=$supplierName?>&nbsp;</td>
              </tr>
            </table></td>
          <td valign="top">&nbsp;</td>
          <td valign="top"></td>
          </tr>
        <tr> 
          <td class="fieldName"> </td>
          </tr>
      </table>
	  </td></tr></table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>
	  <tr bgcolor=white> 
   		 <td colspan="17" align="center" class="printPageHead">
                  SUMMARY OF PO ITEMS
                </td>
  		</tr>
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White"><td colspan="17" align="center" class="fieldName">
	  	  <table width="90%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 		<tr bgcolor="#f2f2f2" align="center">
        <th class="printPageHead" style="padding-left:2px; padding-right:2px; font-size:8pt" width="100">Item</th>
        <th class="printPageHead" style="padding-left:2px; padding-right:2px; font-size:8pt">Unit Price</th>
        <th class="printPageHead" style="padding-left:2px; padding-right:2px; font-size:8pt">Quantity</th>
	<th class="printPageHead" style="padding-left:2px; padding-right:2px; font-size:8pt">Total</th>
      </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="3" nowrap="nowrap" class="printPageHead" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr>
    </table></td>
  </tr>
<!-- GRN -->
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" height="10"></td>
  </tr>
</table>
</td>
</tr>
</table>

<?
	if (sizeof($goodsReceiptRecs)) {
?>
<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='75%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  			<tr bgcolor='white'>
					<td height="10"></td>
 	  			</tr>

 <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
<tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='90%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			<font size="2"></font>GRN DETAILS</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		<div id="printMsg">GRN No:<?=$grnNo?></div></td>		 
		 </tr>
	</table></td>
	    </tr>
 <tr bgcolor=white>
    <td colspan="17" align="center" height="10"></td>
  </tr>
<tr bgcolor=white> 
    <td colspan="17" align="center" class="printPageHead">SUMMARY OF GRN ITEMS</td>
  </tr>
 <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
<tr bgcolor="White">
	<TD>
	<table width="90%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" align="center">	
      <tr bgcolor="#f2f2f2" align="center">
        <th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</th>  
        <th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Quantity</th>
	<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Qty Received</th>
	<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Qty Rejected</th>
	<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Remarks</th>
      </tr>
      <?
		//$numRows	=	25; // Setting No.of rows
		//$j = 0;
		//$purchaseOrderRecSize = sizeof($purchaseOrderRecs);		
		//$totalPage = ceil($purchaseOrderRecSize/$numRows);
		$totalAmount = "";
		foreach ($goodsReceiptRecs as $grn) {
			$i++;
			$editStockId	= $grn[2];
			$stockRec	= $stockObj->find($editStockId);
			$stockName	= stripSlash($stockRec[2]);
			$quantity	= $grn[3];
			$qtyReceived	= $grn[4];
			$qtyRejected	= $grn[5];
			$remarks	= $grn[6];		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$stockName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$quantity;?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$qtyReceived?></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$qtyRejected?></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$remarks?></td>
      </tr>
	<? } ?>
  </table>
	</TD>
</tr>
<tr bgcolor="White"><TD height="10"></TD></tr>
</table>
		</td>
	</tr>
	</table>
<? } // grN End?>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
