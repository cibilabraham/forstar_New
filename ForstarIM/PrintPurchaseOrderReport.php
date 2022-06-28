<?php	
	require("include/include.php");
		
	$dateFrom = $g["stockFrom"];
	$dateTill = $g["stockTo"];
	$selSupplierId = $g["selSupplier"];
	$selStatus	= $g["selStatus"];
	
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	if ($fromDate!="" && $tillDate!="") {
		# List all Records
		$pOReportResultSetObj = $purchaseOrderReportObj->fetchPurchaseOrderRecords($fromDate, $tillDate, $selSupplierId, $selStatus);

		$poReportRecords = $pOReportResultSetObj->getNumRows();
	}

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>PURCHASE ORDER REPORT</title>
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
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>PURCHASE ORDER REPORT FROM <?=$dateFrom?> TO <?=$dateTill;?> </td>
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
    <td colspan="17" align="center">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($poReportRecords) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Date </th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">PO Number</th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Supplier</th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Total Amount</th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Status</th>
      </tr>
      <?
		$numRows	= 14; // Setting No.of rows
		$j = 0;
		$poReportRecSize = $poReportRecords;		
		$totalPage = ceil($poReportRecSize/$numRows);

		$prevSupplierName = "";
		while ($pr=$pOReportResultSetObj->getRow()) {
			$i++;
			$createDate = dateFormat($pr[3]);
			$poNumber = $pr[1];
			$totalPOAmount = $pr[4];
			$supplierName = $pr[6];
			$displaySupplierName = "";
			if ($prevSupplierName!=$supplierName) {
				$displaySupplierName = $pr[6];
			}
			$status		=	$pr[5];
			if ($status=='C') $displayStatus	=	"Cancelled";
			else if ($status=='R') $displayStatus	=	"Received";
			else $displayStatus	=	"Pending";
		?>
      <tr bgcolor="#FFFFFF">
		<td  height='30' class="listing-item" nowrap align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$createDate?></td>
		<td  height='30' class="listing-item" nowrap align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$poNumber?></td>
		<td  height='30' class="listing-item" nowrap align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$displaySupplierName?></td>
		<td  height='30' class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$totalPOAmount?></td>
		<td  height='30' class="listing-item" nowrap align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$displayStatus?></td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $poReportRecSize!=$numRows) {
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
    	</table>
	</td></tr>
	<tr bgcolor="White">
		<TD height="5"></TD>
	</tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
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
	    <td colspan="17" align="center" class="printPageHead"><table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			PURCHASE ORDER REPORT - Cont.</td>
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
                <th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Date </th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">PO Number</th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Supplier</th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Total Amount</th>
		<th class="printPageHead" nowrap style="padding-left:5px; padding-right:5px;font-size:8pt">Status</th>
      </tr>
   <?
	#Main Loop ending section 
			
	       }
		$prevSupplierName = $supplierName;
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