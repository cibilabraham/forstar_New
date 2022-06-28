<?php	
	require("include/include.php");
		
	$dateFrom = $g["dateFrom"];
	$dateTill = $g["dateTill"];
	$selStockId		= $g["selStock"];
	$selDepartmentId	= $g["selDepartment"];
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	
	if ($fromDate!="" && $tillDate!="") {
		$stockIssuanceRecords = $stockIssuanceReportObj->getStockIssunaceRecords($fromDate, $tillDate, $selStockId, $selDepartmentId);
	}

	if ($selStockId!="") {
		$stockRec	=	$stockObj->find($selStockId);
		//$stockName	=	stripSlash($stockRec[2]);
		$displayHeadName = "STOCK (".stripSlash($stockRec[2]).")";
	} else if ($selDepartmentId) {
		$departmentRec		=	$departmentObj->find($selDepartmentId);		
		//$departmentName		=	stripSlash($departmentRec[1]);
		$displayHeadName	= "DEP. (".stripSlash($departmentRec[1]).")";
	} else $displayHeadName = "";

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Stock Issuance Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn)
{
	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintStockIssuanceReport">
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
    <td colspan="17" class="printPageHead" align="center" ><font size="4"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" height="5" ></td>
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
    <td colspan="17" align="RIGHT" class="printPageHead" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>STOCK ISSUANCE & RETURN REPORT <br>
		OF <?=$displayHeadName?> FROM <?=$dateFrom?> TO <?=$dateTill?> 
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
	<!--tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' >
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>Of M/s <?=$supplierName?> From <?=$dateFrom?> To <?=$dateTill?> </td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr-->
  
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
	if ($stockIssuanceRecords>0) {
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" rowspan="2">
			<? if ($selStockId!="") {?>
				Department 
			<? } else if($selDepartmentId!="") { ?>
				Stock Name 
			<? }?>
		</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" rowspan="2">Issued Qty</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="5">Return Qty</th>		
        </tr>
	<tr bgcolor="#f2f2f2">
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Lost </th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Stolen</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Damaged</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Deteriorated</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total</th>
	</tr>
      <?
		$numRows	=	14; // Setting No.of rows
		$j = 0;
		$stockIssuanceRecSize = sizeof($stockIssuanceRecords);
		$totalPage = ceil($stockIssuanceRecSize/$numRows);

		foreach ($stockIssuanceRecords as $sir) {
		$i++;		
		$stockId 	= $sir[0];
		$departmentId 	= $sir[1];
		$issuedQty 	= $sir[2];
		$stkName	= $sir[3];
		$departmentName = $sir[4];

		$wastageRecs = $stockIssuanceReportObj->getWastageRecDetials($fromDate, $tillDate, $stockId, $departmentId);
	
			$lostQty = 0;
			$stolenQty = 0;
			$dmgdQty = 0;
			$deterioQty = 0;
		if (sizeof($wastageRecs)>0) {			 
			
			foreach($wastageRecs as $tqr ) {			
				$reasonType = $tqr[3];
				if ($reasonType=='L')  $lostQty += $tqr[0];
				else if( $reasonType=='S' )  $stolenQty += $tqr[0];
				else if( $reasonType=='D' )  $dmgdQty += $tqr[0];
				else if( $reasonType=='DR' )  $deterioQty += $tqr[0];
				else  {
					$lostQty = 0;
					$stolenQty = 0;
					$dmgdQty = 0;
					$deterioQty = 0;
				}
			}
			$totalQuantity = ( $lostQty + $stolenQty ) + ( $dmgdQty + $deterioQty );	
		}
	
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap>
			<? if ($selStockId!="") {?>
				<?=$departmentName?>
			<? } else if($selDepartmentId!="") { ?>
				<?=$stkName?>
			<? }?>
		</td>
               <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap align="right"><?=$issuedQty?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;">
				<?
					if( $lostQty!=0 ) echo $lostQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;">
				<?
					if( $stolenQty!=0 ) echo $stolenQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;">
				<?
					if( $dmgdQty!=0 ) echo $dmgdQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;">
				<?
					if( $deterioQty!=0) echo $deterioQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$totalQuantity?></td>			
      </tr>
	  	<?
		if ($i%$numRows==0 && $stockIssuanceRecSize!=$numRows) {
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
    	</table></td></tr>
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
	    <td colspan="17" align="center" class="printPageHead">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			STOCK ISSUANCE & RETURN REPORT - Cont.</td>
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
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" rowspan="2">
			<? if ($selStockId!="") {?>
				Department 
			<? } else if($selDepartmentId!="") { ?>
				Stock Name 
			<? }?>
		</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" rowspan="2">Issued Qty</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="5">Return Qty</th>		
        </tr>
	<tr bgcolor="#f2f2f2">
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Lost </th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Stolen</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Damaged</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt">Deteriorated</th>
		<th class="printPageHead" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total</th>
	</tr>
   <?
	#Main Loop ending section 
			
	       }
		$prevStockId = $stockId;
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