<?php
	require("include/include.php");

	$RequisitionId	=	$g["RequisitionId"];

	#Find Requisition Records
	
	#Get all issued Items
	$RequisitionRecs = $stockRequisitionObj->findData($RequisitionId);
	$exportAddrArr=$purchaseorderObj->getAllCompany();
	$exportAddrContact=$purchaseorderObj->getAllCompanyContact($exportAddrArr[0]);
?>
<html>
<head>
<title>STOCK REQUISITION DETAILS</title>
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
    <td colspan="17" class="listing-head" align="center" ><font size="4"><?=$exportAddrArr[1]?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" height="5"></td>
  </tr>
<!--<tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=REG_NO?></td>
  </tr>	-->
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=$exportAddrArr[2]?>,<?=$exportAddrArr[3]?>, <?=$exportAddrArr[4]?>,<?=$exportAddrArr[5]?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" > Phone:<?php 
			foreach($exportAddrContact as $expt1)
			 {
				if($expt1[1]!='') echo $expt1[1].',';
			}
			?> &nbsp;Mobile:
			<?php 
			foreach($exportAddrContact as $expt2)
			 {
				if($expt2[2]!='')  echo $expt2[2].',';
			 }
			 ?>&nbsp;Fax:
			 <?php 
			foreach($exportAddrContact as $expt3)
			 {
				 if($expt3[3]!='') echo  $expt3[3].',';
			 }
			?>&nbsp;E-mail:
			<?php 
			 foreach($exportAddrContact as $expt4)
			 {
				if($expt4[4]!='') echo $expt4[4].',';
			 }
			 ?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="listing-head" height="10"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			STOCK REQUISITION DETAILS</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   
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
    <td colspan="17"  class="fieldName" width="85%">
<table align="center" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if (sizeof($RequisitionRecs)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <td class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">Date</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Department</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">Company</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">Stock Quantity</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">Available Quantity</td>	
      </tr>
      <?
	  	
			$stockRequisitionId	=	$RequisitionRecs[0];
			$department		=	$RequisitionRecs[2];
			$item		=	$RequisitionRecs[4];
			$company		=	$RequisitionRecs[6];
			$unit		=	$RequisitionRecs[8];
			$qty		=	$RequisitionRecs[10];
			$createdDate		= dateFormat($RequisitionRecs[11]);
			$stockQty	=	$RequisitionRecs[9];
			
		
		?>
    <tr bgcolor="#FFFFFF">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$createdDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$department;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$item;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$company;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$qty;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$stockQty;?></td>
      </tr>
	  	
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
	 <tr bgcolor="White"><td colspan="17" align="center" class="fieldName">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 		
   
      
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

</body></html>