<?php
	require("include/include.php");

	$unitTransferDataId	=	$g["unitTransferDataId"];

	
	$unitTransferDataRec	=	$unitTransferObj->fetchAllUnitTransfer($unitTransferDataId);		
	
?>
<html>
<head>
<title>UNIT TRANSFER DETAILS</title>
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
			UNIT TRANSFER DETAILS</td>
		   
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
	if (sizeof($unitTransferDataRec)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM LOT ID</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Details</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Unit name</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Processing Stage</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Transfer to Unit Name</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Transfer to Processing Stage</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">New RM LOT ID</th>
		
      </tr>
      <?
	  	
		$numRows	= 5; // Setting No.of rows
		$j = 0;
		 $unitTransferRecSize = sizeof($unitTransferDataRec);
		
		//$totalPage = ceil($unitTransferRecSize/$numRows);

		$totalAmount = "";
		foreach ($unitTransferDataRec as $sir) {
			$i++;
		$unitTransferDataId	=	$sir[0];
		//$lotRec		=	$rmTestDataObj->findLot($sir[1]);
		$lotRec		=	$unitTransferObj->findLot($sir[1]);
		$rmlotId		=	$lotRec[1];
		//echo $unit		=	$sir[2];
		//$supplierRec		=	$rmReceiptGatePassObj->find($sir[2]);
		//$supplierDetails		=	$unitRec[14];
		$supplierDetails		=	$sir[2];
		//$rmLotId		=	$sir[2];
		$unitRec		=	$plantandunitObj->find($sir[3]);
		$currentUnitName		=	$unitRec[2];
		//$currentUnitName		=	$sir[3];
		$type		=	$rmReceiptGatePassObj->findProcessType($sir[4]);
		$currentProcessingStage		=	$type[1];
		//$currentProcessingStage		=	$sir[4];
		//$rmTestName		=	$sir[3];
		
		$newUnitRec		=	$plantandunitObj->find($sir[5]);
		$unitName		=	$newUnitRec[2];
		$newProcess		=	$rmReceiptGatePassObj->findProcessType($sir[6]);
		$processType		=	$newProcess[1];
		//$newLotRec		=	$unitTransferObj->findLot($sir[7]);
		$lotId		=	$sir[7];
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$rmlotId?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$supplierDetails?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$currentUnitName?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$currentProcessingStage?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$unitName?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$processType?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$lotId?></td>
	  </tr>
	  	<?
		if ($i%$numRows==0 && $unitTransferRecSize!=$numRows) {
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
			UNIT TRANSFER DETAILS - Cont.</td>
		  		 
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
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Details</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Unit name</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Current Processing Stage</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Transfer to Unit Name</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Transfer to Processing Stage</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">New RM LOT ID</th>
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