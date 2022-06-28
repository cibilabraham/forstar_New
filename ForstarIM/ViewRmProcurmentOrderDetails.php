<?php
	require("include/include.php");

	$procurmentId	=	$g["procurmentId"];
	$supplierGroup	=	$g["supplierGroup"];
	$supplier	=	$g["supplier"];
	$pondNamee	=	$g["pondNamee"];

	#Find Issuance Records
	$procurmentRec	=	$rmProcurmentOrderObj->find($procurmentId);		
	$procurementNo		=	$procurmentRec[1];	

	#Get all issued Items
	$procurmentDetailsRecs = $rmProcurmentOrderObj->fetchAllProcurmentEntries($procurmentId);
?>
<html>
<head>
<title>RM PROCURMENT DETAILS</title>
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
			RM PROCURMENT DETAILS</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <div id="printMsg">PROCURMENT NO:&nbsp;<?=$procurementNo?></div></td>
		 </tr></table></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			Supplier Group Name:&nbsp;<?=$supplierGroup?></td>
		   
		 </tr></table></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			Supplier Name:&nbsp;<?=$supplier?></td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <div id="printMsg">Farm Name:&nbsp;<?=$pondNamee?></div></td>
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
	if (sizeof($procurmentDetailsRecs)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Driver Name</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Vehicle No</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Equipment Name</th>	

		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Equipment Qty</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Equipment Issued</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Balance Qty</th>
				<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Name</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Issued</th>
		
      </tr>
      <?
	  	
		$numRows	= 14; // Setting No.of rows
		$j = 0;
		$procurmentRecSize = sizeof($procurmentDetailsRecs);
		
		$totalPage = ceil($procurmentRecSize/$numRows);

		$totalAmount = "";
		foreach ($procurmentDetailsRecs as $por) {
			$i++;
			$editProcurmentId	=	$por[1];
			$driverId =$por[2];
			$driverRec	=	$driverMasterObj->find($driverId);
			$driverName	=	stripSlash($driverRec[1]);	
			$vehicleRec=$vehicleMasterObj->find($por[3]);
			$vehicleNumber	=	stripSlash($vehicleRec[1]);
			$equipmentRec=$harvestingEquipmentMasterObj->find($por[4]);
			$equipmentName	=	stripSlash($equipmentRec[1]);
			//$equipmentQtyRec=$vehicleMasterObj->getharvestingEquipment($por[5]);
			$equipmentQty	=	$por[5];
			$equipmentIssued=	$por[6];
			$difference=	$por[7];
			$chemicalRec=$harvestingChemicalMasterObj->find($por[8]);
			$chemicalName	=	stripSlash($chemicalRec[1]);
			$chemicalQty	=	$por[9];
			$chemicalIssued=	$por[10];
			
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$driverName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$vehicleNumber?></td>
		 <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$equipmentName?></td>
		  <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$equipmentQty?></td>
		  		  <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$equipmentIssued?></td>
		   <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$difference?></td>
		    <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$chemicalName?></td>
			 <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$chemicalQty?></td> 
			 <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$chemicalIssued?></td>
			 
      </tr>
	  	<?
		if ($i%$numRows==0 && $procurmentRecSize!=$numRows) {
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
			Purchase order details - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		<div id="printMsg">No:<?=$procurementNo?></div></td>		 
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
       <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Driver Name</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Vehicle No</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Equipment Name</th>	
		
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Equipment Qty</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Equipment Issued</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Balance Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Name</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Issued</th>
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