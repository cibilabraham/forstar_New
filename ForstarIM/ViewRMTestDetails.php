<?php
	require("include/include.php");

	$rmTestDataId	=	$g["rmTestDataId"];

	
	$rmTestDataRec	=	$rmTestDataObj->fetchAllRmTestItem($rmTestDataId);		
	
?>
<html>
<head>
<title>RM TEST DATA DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmRMTestData">
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
			RM TEST DATA DETAILS</td>
		   
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
	if (sizeof($rmTestDataRec)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit Name</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Lot ID</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Test Name</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Test Method</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date of Testing</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RESULT</th>
		
      </tr>
      <?
	  	
		$numRows	= 5; // Setting No.of rows
		$j = 0;
		 $rmTestDataRecSize = sizeof($rmTestDataRec);
		
		//$totalPage = ceil($rmTestDataRecSize/$numRows);

		$totalAmount = "";
		foreach ($rmTestDataRec as $sir) {
			$i++;
		$rmTestDataId	=	$sir[0];
		//$unit		=	$sir[1];
		$unitRec		=	$plantandunitObj->find($sir[1]);
		$unit		=	$unitRec[2];
		//$rmLotId		=	$sir[2];
		$lotRec		=	$rmTestDataObj->findLot($sir[2]);
		$rmLotId		=	$lotRec[1];

		//$rmTestName		=	$sir[3];
		$testNameRec		=	$rmTestMasterObj->find($sir[3]);
		$rmTestName		=	$testNameRec[1];
		$rmtestMethod		=	$testNameRec[2];
		$dateOfTesting		=	dateFormat($sir[5]);
		$result		=	$sir[6];
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$unit?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$rmLotId?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$rmTestName?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$rmtestMethod?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$dateOfTesting?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$result?></td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $rmTestDataRecSize!=$numRows) {
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
			RM TEST DATA DETAILS - Cont.</td>
		  		 
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
             <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit Name</th>  
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Lot ID</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Test Name</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Test Method</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date of Testing</th>
<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RESULT</th>
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