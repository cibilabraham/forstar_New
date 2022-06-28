<?php
	require("include/include.php");

	$rmReceiptGatePassId	=	$g["rmReceiptGatePassId"];

	
	$rmReceiptDataRec	=	$rmReceiptGatePassObj->fetchAllRmReceiptGatePassItem($rmReceiptGatePassId);		
	
?>
<html>
<head>
<title>RM RM RECEIPT GATEPASS DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmRMReceiptGatePass">
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
			RM RECEIP GATEPASS DETAILS</td>
		   
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
	if (sizeof($rmReceiptDataRec)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Process Type</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Lot Id</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Procurment GatePassId</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Vehicle Number</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Driver</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">InSeal</th>
 <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Result</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Seal No </th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Out Seal</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Verified</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Labours</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">CompanyName</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier ChallanNo</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier Challan Date</th>	
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date Of Entry</th>		
      </tr>
      <?
	  	
		$numRows	= 5; // Setting No.of rows
		$j = 0;
		 $rmReceiptDataRecSize = sizeof($rmReceiptDataRec);
		
		//$totalPage = ceil($rmReceiptDataRecSize/$numRows);

		$totalAmount = "";
		foreach ($rmReceiptDataRec as $sir) {
			$i++;
		$rmReceiptGatePassId	=	$sir[0];
		//$processType		=	$sir[1];
		$type		=	$rmReceiptGatePassObj->findProcessType($sir[1]);
		 $processType		=	$type[1];
		
		$lotId		=	$sir[2];
		//$procurmentGatePassId 		=	$sir[3];
		$gatePass		=	$rmProcurmentOrderObj->find($sir[3]);
		$procurmentGatePassId		=	$gatePass[1];
		
		//$vehicleNumbers		=	$sir[4];
		$vehicle		=	$vehicleMasterObj->find($sir[4]);
		$vehicleNumbers		=	$vehicle[1];
		
		//$driver		=	$sir[5];
		$DriverName		=	$driverMasterObj->find($sir[5]);
		$driver		=	$DriverName[1];
		
		//$inSeal		=	$sir[6];
		$insealNumber		=	$sealNumberObj->find($sir[6]);
		 $inSeal		=	$insealNumber[1];
		
		$result		=	$sir[7];
		
		$sealNo 		=	$sir[8];
		//$sealNum	=	$sealNumberObj->find($sir[8]);
		//$sealNo		=	$sealNum[1];
		
		//$outSeal 		=	$sir[9];
		$oSealNum	=	$sealNumberObj->find($sir[9]);
		$outSeal		=	$oSealNum[1];
		
	   $verif 		=	$sir[10];
		$verifiedBy	=	$employeeMasterObj->find($sir[10]);
		$verified		=	$verifiedBy[1];
		
		$labours 		=	$sir[11];
		//$labour	=	$rmReceiptGatePassObj->find($sir[11]);
		//echo $labours		=	$labour[11];
		
		//$selCompanyName 		=	$sir[12];
		$company	=	$companydetailsObj->find($sir[12]);
		$selCompanyName		=	$company[1];
		
		//$unit		=	$sir[13];
		$untName	=	$plantandunitObj->find($sir[13]);
		$unit		=	$untName[2];
		
		$supplierChallanNo 		=	$sir[14];
		$supplierChallanDate 		=	dateFormat($sir[15]);
		$dateOfEntry		=	dateFormat($sir[16]);
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$processType?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$lotId?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$procurmentGatePassId ?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$vehicleNumbers?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$driver?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$inSeal?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$result ?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$sealNo ?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$outSeal?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$verified?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$labours ?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$selCompanyName?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$unit?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$supplierChallanNo?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$supplierChallanDate?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$dateOfEntry?></td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $rmReceiptDataRecSize!=$numRows) {
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
			RM RECEIPT GATEPASS DETAILS - Cont.</td>
		  		 
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
             <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Process Type</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Lot Id</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Procurment GatePassId</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Vehicle Number</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Driver</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">InSeal</th>
 <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Result</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Seal No </th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Out Seal</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Verified</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Labours</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">CompanyName</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier ChallanNo</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supplier Challan Date</th>	
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date Of Entry</th>		
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