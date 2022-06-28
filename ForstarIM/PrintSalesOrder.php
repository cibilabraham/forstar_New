<?php	
	require("include/include.php");

	$selSOId = $g["selSOId"];

	#Find PO Records
	$sORec	=	$salesOrderObj->find($selSOId);
	$pOGenerateId		= $sORec[1];
	$distributorId		= $sORec[2];
	$selStateId		= $sORec[10];	

	#Supplier Rec
	$distributorRec		= $salesOrderObj->getDistributorRec($distributorId, $selStateId);
	$code			= $distributorRec[1];
	$distributorName	= stripSlash($distributorRec[2]);
	$address		= stripSlash($distributorRec[4]);
	$telNo			= stripSlash($distributorRec[5]);
	$vatNo			= stripSlash($distributorRec[8]);
	$cstNo			= stripSlash($distributorRec[10]);

	# Get all SO Items
	$salesOrderItemRecs = $salesOrderObj->fetchAllSalesOrderItem($selSOId);
?>
<html>
<head>
<title>SALES ORDER MEMO</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<!--script language="javascript" type="text/javascript">
 function printThisPage(printbtn)
{
	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script-->
<script language="javascript" type="text/javascript">
function printDoc()
{	
	window.print();	
	return false;
}

function displayBtn()
{
	document.getElementById("printButton").style.display="block";			
}

function printThisPage(printbtn)
{	
	document.getElementById("printButton").style.display="none";	
	if (!printDoc()) {
		setTimeout("displayBtn()",3000);			
	}		
}
</script>
</head>
<body>
<form name="frmPrintDailyCatchReportMemo">
<table width="75%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>
<table width='75%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="5"><?=$companyArr["Name"];?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" >M53, MIDC, Taloja, New Bombay 410208</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" >Tel: 022 2741 0807 / 2741 2376</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="3">SALES ORDER</font>		   </td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <div id="printMsg">No:<?=$pOGenerateId?></div></td>
		 </tr></table></td>
  </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
         <tr>
           <td nowrap="nowrap" align='left' valign='top' style=" padding-left:8px;">
		   <table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
             <tr>
               <td nowrap="nowrap" class="listing-head" colspan="2" height="25"><font size="2">Name, Address & Phone of Distributor</font> </td>
             </tr>
             <tr>
               <td class="listing-item" nowrap="nowrap" colspan="2" height="25"><font size="3">
                 <?=$distributorName?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-item" width='200' height="55" colspan="2"><font size="3">
                 <?=$address?>
               </font></td>
             </tr>
		<tr>
               <td class="listing-item" width='200' height="55" colspan="2"><font size="3">
                 <?=$telNo?>
               </font></td>
             </tr>
           </table></td>		
		   <td class="listing-head" nowrap="nowrap" align='right' valign='top'>			
			<table width="98%" cellpadding="0" cellspacing="0"  class="tdBoarder">
				  <tr>
					<td class="listing-head" height="35">VAT No:</td>
					<td class="listing-item" nowrap="nowrap"><?=$vatNo?></td>
				  </tr>
				  <tr>
					<td class="listing-head" height="35">CST No:</td>
					<td class="listing-item"><?=$cstNo?></td>
				  </tr>
				</table></td>
		 </tr>
	</table>	</td>
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
    <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?php
		if (sizeof($salesOrderItemRecs)) {
	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Product</th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Rate</th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Quantity</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Total</th>
      </tr>
      <?
		$numRows = 14; // Setting No.of rows 14
		$j = 0;

		$salesOrderRecSize = sizeof($salesOrderItemRecs);		
		$totalPage = ceil($salesOrderRecSize/$numRows);
		$totalAmount = "";
		foreach ($salesOrderItemRecs as $por) {
			$i++;
			$selProductId	= $por[2];			
			$unitRate	= $por[3];
			$editQuantity	= $por[4];
			$total		= $por[5];			
			$productName	= "";
			$productRec	= $manageProductObj->find($selProductId);
			$productName	= $productRec[2];
			$totalAmount = $totalAmount + $total;		
	?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$productName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=number_format($unitRate,2);?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$editQuantity?></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$total?></td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
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
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
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
	  <table width='75%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
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
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head"><table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="2"></font>Sales Order - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		<div id="printMsg">No:<?=$pOGenerateId?></div></td>		 
		 </tr>
	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr><td>
		<table width="100%" cellpadding="0" cellspacing="3" class="tdBoarder">
        <tr> 
          <td valign="top"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="listing-head" valign="top">Distributor:</td>
                <td class="listing-item" style="line-height:normal;" valign="top"><?=$distributorName?>&nbsp;</td>
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
   		 <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS
                                        </td>
  		</tr>
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr><td colspan="17" align="center" class="fieldName">
	  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 		<tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt" width="100">Item</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Unit Price</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Quantity</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Total</th>
      </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="3" nowrap="nowrap" class="listing-head" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
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
      
      <!--tr>
        <td class="listing-item" nowrap="nowrap" valign="bottom" style="line-height:8px;"><?=$date?><strong>Page <?=$totalPage?> of <?=$totalPage?></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
      </tr-->
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
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