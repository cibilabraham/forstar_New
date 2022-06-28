<?php
	require("include/include.php");

	$phtMonitoringId	=	$g["phtMonitoringId"];

	
	$phtMonitoringRec	=	$phtMonitorngObj->fetchAllPhtMonitoringItem($phtMonitoringId);		
	
?>
<html>
<head>
<title>PHT MONITORING DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPhtMonitoring">
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
			PHT MONITORING DETAILS</td>
		   
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
	if (sizeof($phtMonitoringRec)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">PHT Certificate No</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier Name</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier Group</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Species</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Lot ID</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Pht Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Set off Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Balance Qty</th>
		
      </tr>
      <?
	  	
		$numRows	= 5; // Setting No.of rows
		$j = 0;
		 $phtMonitoringRecSize = sizeof($phtMonitoringRec);
		
		//$totalPage = ceil($phtMonitoringRecSize/$numRows);

		$totalAmount = "";
		foreach ($phtMonitoringRec as $sir) {
			$i++;
		$phtMonitoringId	=	$sir[0];
		$date		=	dateFormat($sir[1]);
		$selLotId = $sir[2];
		$supplierId=$sir[3];
		$supplier=$sir[7];
		$supplierGroupName=$sir[8];
		$speciousId		=	$sir[5];
		$specious	=	$sir[9];
		$supplyQty		=	$sir[6];
		$phtcetificate=$sir[10];
		$phtcert=$phtMonitorngObj->getCertificateQuantity($sir[0]);
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$date?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$phtcetificate?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$supplier?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$supplierGroupName?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$specious?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$supplyQty?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right">
		<?
		foreach($phtcert as $detail)
		{
		echo $detail[3];
		echo '<br/>';
		}
		?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[0];
		echo '<br/>';
		}
		?> 
		</td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[1];
		echo '<br/>';
		}
		?> 
		</td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[2];
		echo '<br/>';
		}
		?> 
		</td>
		
      </tr>
	  	<?
		if ($i%$numRows==0 && $phtMonitoringRecSize!=$numRows) {
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
			PHT MONITORING DETAILS - Cont.</td>
		  		 
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
            <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Lot ID</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier Name</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier Group</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Species</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Qty</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">PHT Certificate No</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Qty</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Set off Qty</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Balance Qty</th>
      </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
      
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