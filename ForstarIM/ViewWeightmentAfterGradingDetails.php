<?php
	require("include/include.php");

	$WeightGradingId	=	$g["WeightGradingId"];

	
	$WeightGradingRec	=	$weightmentAfterGradingObj->fetchAllWeightAfterGrading($WeightGradingId); $rmLotId=$WeightGradingRec[0][8];		
	$result 			= $weightmentAfterGradingObj->getCompayAndUnit($rmLotId);
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
    <td colspan="17" align="RIGHT" class="listing-head" height="10" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			WEIGHTMENT AFTER GRADING DETAILS</td>
		   
		 </tr></table></td>
  </tr>
<tr bgcolor="White"><TD height="25"></TD></tr> 
   <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' cellpadding="2" cellspacing="3" bgcolor="#f2f2f2" align="center" class="print" >
         <tr  >
           <td class="listing-head" nowrap="nowrap" align='left' style="background-color:#f2f2f2;"  >
			Company Name:- &nbsp;</td>
		   <td class="listing-head" nowrap="nowrap" align='left' >&nbsp;
			<?php echo $result[1];?></td>
		</tr>
		<tr >
           <td class="listing-head" nowrap="nowrap" align='left' style="background-color:#f2f2f2;">
			Unit:- &nbsp;</td>
		   <td class="listing-head" nowrap="nowrap" align='left' >&nbsp;
			<?php echo $result[3];?></td>
		 </tr>
	</table></td>
  </tr>
  <tr bgcolor="White"><TD height="25"></TD></tr> 
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head"><u>SUMMARY OF ITEMS</u></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
   
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  align="center" class="print">
	<?
	if (sizeof($WeightGradingRec)) {

	?>
	
	

      <tr bgcolor="#f2f2f2" align="center">
      
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Lot ID</th>
		<!--<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Details</th>  -->
		<th class="listing-head" class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Fish</th>
		<th class="listing-head" class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Process code</th>
		<!--<th class="listing-head" class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Count Code</th>-->
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Grade</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Weight</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Total Wt</th>
		<!--<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Diff in Weight</th>-->
				
      </tr>
      <?
	  	
		$numRows	= 5; // Setting No.of rows
		$j = 0;
		 $WeightGradingRecSize = sizeof($WeightGradingRec);
		
		//$totalPage = ceil($rmTestDataRecSize/$numRows);

		$totalAmount = "";
		foreach ($WeightGradingRec as $sir) {
			$i++;
		$WeightGradingId	=	$sir[0];
		$LotId		=	$sir[1];
		$alpha		=	$sir[2];
		//$lot = $weightmentAfterGradingObj->getLotNm($LotId);
		//$newLot=$lot[1];
		$supplierName		=	$sir[3];
		$PondName		=	$sir[4];
		$sumtotal		=	$sir[5];
		$differ		=	$sir[7];
		$method = $weightmentAfterGradingObj->getWeightAfterGradingDetail($WeightGradingId);
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$alpha.$LotId;?></td>
        <!--<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><a onMouseOver="ShowTip('<?=$PondName;?>');" onMouseOut="UnTip();"><?=$supplierName;?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
			<?php 
			foreach($method as $detail)
			{
			echo $fish=$detail[8];
			echo '<br/>';
			}
			?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
			<?php 
			foreach($method as $detail)
			{
			echo $processcode=$detail[9];
			echo '<br/>';
			}
			?> 
		</td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
			<?php 
			foreach($method as $detail)
			{
			echo $countcode=$detail[5];
			echo '<br/>';
			}
			?> 
		</td>-->
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?php 
		foreach($method as $detail)
		{
		$gradeID=$detail[1];
		$grade = $weightmentAfterGradingObj->getGradeNm($gradeID);
		echo $grade[1];
		echo '<br/>';
		}
		?> </td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?php 
		foreach($method as $detail)
		{
		echo $detail[2];
		echo '<br/>';
		}
		?> </td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$sumtotal;?></td>
		<!--<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$differ;?></td>-->
      </tr>
	  	<?
		if ($i%$numRows==0 && $WeightGradingRecSize!=$numRows) {
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
			WEIGHTMENT AFTER GRADING DETAILS - Cont.</td>
		  		 
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
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">RM Lot ID</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Supply Details</th>  
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Grade</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Weight</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Total Wt</th>
		<!--<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Diff in Weight</th>>-->
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