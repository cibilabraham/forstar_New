<?php
	require("include/include.php");

	$soakingDataId	=	$g["soakingDataId"];

	
	$soakngDataRec	=	$soakingObj->fetchAllSoaking($soakingDataId);		
	
?>
<html>
<head>
<title>SOAKING DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmUnitTransfer">
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
			SOAKING DETAILS</td>
		   
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
	if (sizeof($soakngDataRec)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM LOT ID</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Processing Stage</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Details</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Available Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Soak in-count</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak in-quantity</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak in-time</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak out-count</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak out-quantity</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak out-time</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Temperature</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Gain%</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical used</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Qty</th>
		
      </tr>
      <?
	  	
		$numRows	= 5; // Setting No.of rows
		$j = 0;
		 $soakingRecSize = sizeof($soakngDataRec);
		
		//$totalPage = ceil($soakingRecSize/$numRows);

		$totalAmount = "";
		foreach ($soakngDataRec as $sir) {
			$i++;
		$soakingDataId	=	$sir[0];
		//$lotRec		=	$rmTestDataObj->findLot($sir[1]);
		$lotRec		=	$unitTransferObj->findLot($sir[1]);
		$rmlotId		=	$lotRec[1];
		$type		=	$rmReceiptGatePassObj->findProcessType($sir[2]);
		$currentProcessingStage		=	$type[1];
		//$supplierRec		=	$rmReceiptGatePassObj->find($sir[3]);
		//$supplierDetails		=	$supplierRec[14];
		$supplierDetails		=	$sir[3];
		$availableQuantity=$sir[4];
		$soakInCount=$sir[5];
		$soakInQuantity=$sir[6];
		$soakInTime=$sir[7];
		$soakOutCount=$sir[8];
		$soakOutQunatity=$sir[9];
		$soakOutTime=$sir[10];
		$temperature=$sir[11];
		$gain=$sir[12];
		//$chemcalUsed=$sir[13];
		$chemical		=	$harvestingChemicalMasterObj->find($sir[13]);
		$chemcalUsed		=	$chemical[1];
		$chemcalQty=$sir[14];
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$rmlotId?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$currentProcessingStage?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$supplierDetails?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$availableQuantity?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$soakInCount?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$soakInQuantity?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$soakInTime?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$soakOutCount?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$soakOutQunatity?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$soakOutTime?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$temperature?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$gain?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$chemcalUsed?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$chemcalQty?></td>
		
	  </tr>
	  	<?
		if ($i%$numRows==0 && $soakingRecSize!=$numRows) {
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
			SOAKING DETAILS - Cont.</td>
		  		 
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
              <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM LOT ID</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Processing Stage</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Details</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Available Qty</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Soak in-count</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak in-quantity</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak in-time</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak out-count</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak out-quantity</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">soak out-time</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Temperature</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Gain%</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical used</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Chemical Qty</th>
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