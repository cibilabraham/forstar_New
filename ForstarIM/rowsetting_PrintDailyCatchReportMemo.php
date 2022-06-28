<?
	
	require("include/include.php");
	
?>
<html>
<head>
<title>RAW MATERIAL PURCHASE MEMO</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	//document.getElementById("printButton").style.display="none";
	window.print();
	//document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<!--form name="frmPrintDailyCatchReportMemo"-->
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?
	
	if($g["selWeighment"]==""){
			$selWeighmentNo	=	$g["weighNumber"];
			$dailyCatchReport	=	$dailycatchreportObj ->fetchAllCatchReportRecords($selWeighmentNo);
			$dailyCatchReportRecords	=	$dailycatchreportObj ->fetchAllCatchReportRecords($selWeighmentNo);

	}
	else {
		$selWeighmentNo	=	$g["selWeighment"];	

		$fromDate			=	$g["fromDate"];	
		$tillDate			=	$g["tillDate"];
		
		$dailyCatchReport	=	$dailycatchreportObj ->fetchAlldailyCatchReportRecords($selWeighmentNo,$fromDate,$tillDate);
		$dailyCatchReportRecords	=	$dailycatchreportObj ->fetchAlldailyCatchReportRecords($selWeighmentNo,$fromDate,$tillDate);

	}
	
	$confirm=$dailyCatchReport[0][45];
	if($confirm==1){
		$numCopy	=	1;
	}
	else {
	
		$numCopy	=	2;
	}
	
 for($print=0;$print<$numCopy;$print++){
	
	foreach($dailyCatchReport as $dcrs){
	
	// Finding Supplier Record
	
	$supplierRec		=	$supplierMasterObj->find($dcrs[8]);
	$supplierName		=	$supplierRec[2];
	$supplierAddr		=	$supplierRec[3];

	//Finding Landing Center Record
	$centerRec			=	$landingcenterObj->find($dcrs[7]);
	$landingCenterName		=	stripSlash($centerRec[1]);
	
	// Finding Plant Record
	$plantRec			=	$plantandunitObj->find($dcrs[1]);
	$plantName			=	stripSlash($plantRec[2]);
	
	//$weighmentChallanNo	= 	$dcrs[1];
	
	$date		=	date("j M Y");
	$time		=	date("g:i a");	
	//$today = date("F j, Y, g:i a");                 // March 10, 2001, 5:16 pm
	$Date1					=	explode("-",$dcrs[3]); //2007-06-27
	$weighChallanDate		= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[2], $Date1[0]));
	
	$enteredDateF			=	explode(" ",$dcrs[2]); //2007-06-30 16:34:09
	$Time1					=	explode(":",$enteredDateF[1]);
	$weighChallanTime		= 	date("g:i a", mktime($Time1[0],$Time1[1]));

	$vechNo					=		$dcrs[4];		
	$supplierChallanNo		=		$dcrs[5];		
	
	$selectTime				=	explode("-",$dcrs[43]);
	$recordedTime			=	$selectTime[0].":".$selectTime[1]."&nbsp;".$selectTime[2];
	}
	
?>

<table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td>&nbsp;</td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="5"><?=$companyArr["Name"];?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" >M53, MIDC, Taloja, New Bombay 412208</td>
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
			<font size="3">RAW MATERIAL PURCHASE MEMO</font>
		   </td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <? 
		   if($confirm==1){
		   ?>
		   <div id="printMsg">DUPLICATE</div>
		   <?
		   }
		   else {
		   	if($print==0){
			?>
			<div id="printMsg">ORIGINAL</div>
			<? } else {?>
			<div id="printMsg">SUPPLIER'S COPY</div>
			<? }
			}
			?>
		   </td>		 
		 </tr>
	</table>
	</td>
  </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
         
         <tr>
           <td nowrap="nowrap" align='left' valign='top' style=" padding-left:8px;">
		   <table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
             <tr>
               <td nowrap="nowrap" class="listing-head" colspan="2" height="25"><font size="2">Name & Address of Party</font> </td>
             </tr>
             <tr>
               <td class="listing-item" nowrap="nowrap" colspan="2" height="25"><font size="3">
                 <?=$supplierName?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-item" width='200' height="55" colspan="2"><font size="3">
                 <?=$supplierAddr?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-head" nowrap="nowrap" height="25">Landing Center:</td>
               <td class="listing-item" nowrap="nowrap" height="25"><font size="2">
                 <?=$landingCenterName?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-head" height="25">Vehicle No:</td>
               <td class="listing-item"><?=$vechNo?></td>
             </tr>
             <tr>
               <td class="listing-head" nowrap="nowrap" height="25">Supplier Challan No:</td>
               <td class="listing-item" nowrap="nowrap"><?=$supplierChallanNo?></td>
             </tr>
           </table></td>		
		   <td class="listing-head" nowrap="nowrap" align='right' valign='top'>			
			<table width="98%" cellpadding="0" cellspacing="0"  class="tdBoarder">
				  <tr>
					<td class="listing-head" height="35">Wt Date/Time:</td>
					<td class="listing-item" nowrap="nowrap"><?=$weighChallanDate?> - <?=$recordedTime?></td>
				  </tr>
				  <tr>
					<td class="listing-head" height="35">Wt Challan No:</td>
					<td class="listing-item"><?=$selWeighmentNo?></td>
				  </tr>
				  
				  <tr>
					<td class="listing-head" height="35">Supplied At:</td>
					<td class="listing-item"><?=$plantName?></td>
				  </tr>
				  
				  <tr>
					<td class="listing-head" nowrap="nowrap" height="35">Accepted By: </td>
					<td class="listing-item" nowrap="nowrap">&nbsp;</td>
				  </tr>
				</table>		   </td>		 
		 </tr>
	</table>
	</td>
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
    <td colspan="17" align="center" class="listing-head">SUMMARY OF COUNTWISE SUPPLY RECEIVED </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
						<?
	
								if( sizeof($dailyCatchReportRecords)){
									$i	=	0;
									$paymentBy	=	$dailyCatchReportRecords[0][44];
									
						?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px;">VARIETY</th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px;">TYPE</th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px;">REMARKS</th>
		<? if($paymentBy=='E') { ?>
        <th class="listing-head" style="padding-left:5px; padding-right:5px;">COUNT</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">GRADE</th> 
        <th class="listing-head" style="padding-left:5px; padding-right:5px;">QUANTITY</th>
		<? } else {?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">DECL.COUNT</th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px;">DECL.QUANTITY</th>
		<? }?>
        <th class="listing-head" width="80" style="padding-left:10px; padding-right:10px;">RATE</th>
        <th class="listing-head" width="100" style="padding-left:10px; padding-right:10px;">AMOUNT</th>
      </tr>
	  
      <?
	  	$grandTotalEffectiveWt	=	"";
	$numRows		=	2;
	foreach($dailyCatchReportRecords as $dcr){
	$i++;
	
	$catchEntryId	=	$dcr[0];
	$array			=	explode("-",$dcr[3]);
	$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
	
		
	$fishId			=	$dcr[11];
	
	$fishRec		=	$fishmasterObj->find($fishId);
	$fishName		=	$fishRec[1];
	
	
	
	
	
	$processCodeRec		=	$processcodeObj->find($dcr[12]);
	$processCode		=	$processCodeRec[2];
	
	$netWt			=	$dcr[27];
	
	$declWt			=	$dcr[29];
	$declCount		=	$dcr[30];
	
	$dailyRateRec	=	$supplieraccountObj->findDailyRate($fishId);
	
	$declRate		=	$dailyRateRec[7];
	
	$paidStatus			=	$dcr[35];
	
		
	$selectWeight		=	$dcr[32];
	$selectRate			=	$dcr[33];
	$actualRate			=	$dcr[34];
	
	
	$dailyCatchEntryId	=	$dcr[42];
	
	$paymentBy	=	$dcr[44];

	if($paymentBy=='E'){
	
	
		$count			=	$dcr[13];
		$countAverage	=	$dcr[14];
		$gradeCode = "";
		if($count == ""){
			$gradeRec		=	$grademasterObj->find($dcr[37]);
			$gradeCode		=	stripSlash($gradeRec[1]);
		}
# -- count all Gross Records -------------------------------------------------------
		$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($dailyCatchEntryId);

		$totalWt	=	"";
		$grandTotalBasketWt = "";
		$netGrossWt	=	"";
		foreach ($countGrossRecords as $cgr){
					$countGrossWt			=	$cgr[1];
					$totalWt				=	$totalWt+$countGrossWt;
					$countGrossBasketWt		=	$cgr[2];
					$grandTotalBasketWt		=	$grandTotalBasketWt + $countGrossBasketWt;
					$netGrossWt				=	$totalWt - $grandTotalBasketWt;
		}
		
		$localQty		=	$dcr[16];
		$wastageQty		=	$dcr[17];
		$softQty		=	$dcr[18];
		$gradeCountAdj	=	$dcr[46];
		$adjustWt		=	$dcr[20] + $localQty + $wastageQty + $softQty + $gradeCountAdj;
		
		if($netGrossWt==""){
			$netGrossWt	=	$dcr[26];
		}
		
		$effectiveWt	=	$netGrossWt - $adjustWt;
		
		}
		else { 
		
			$count			=	$declCount;
			$effectiveWt	=	$declWt	;
		}
		$remarks		=	$dcr[23];
		
		$grandTotalEffectiveWt	+=	$effectiveWt;
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$fishName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$remarks?></td>
		<? if($paymentBy=='E') { ?>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap="nowrap"><?=$count?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$gradeCode?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($effectiveWt,2);?></td>
		<td height='30' class="listing-item">&nbsp;</td>
		<td height='30' class="listing-item">&nbsp;</td>
		<? } else {?>
		
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$declCount?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($declWt,2);?></td>
		<td height='30' class="listing-item">&nbsp;</td>
		<td height='30' class="listing-item">&nbsp;</td>
		<? }
		?>
		</tr>
		<?
		if($i%$numRows == 0){
		?>
		      
	    </table></td></tr>
				  <tr><td>
		<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
		</td></tr>
				  
		<tr><td colspan="17" align="center" class="fieldName">
		
	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	  	<tr bgcolor="#f2f2f2" align="center">
			<th class="listing-head" style="padding-left:5px; padding-right:5px;">VARIETY</th>
        	<th class="listing-head" style="padding-left:5px; padding-right:5px;">TYPE</th>
        	<th class="listing-head" style="padding-left:5px; padding-right:5px;">REMARKS</th>
			<? if($paymentBy=='E') { ?>
       		 <th class="listing-head" style="padding-left:5px; padding-right:5px;">COUNT</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px;">GRADE</th> 
        	<th class="listing-head" style="padding-left:5px; padding-right:5px;">QUANTITY</th>
			<? } else {?>
			<th class="listing-head" style="padding-left:5px; padding-right:5px;">DECL.COUNT</th>
        	<th class="listing-head" style="padding-left:5px; padding-right:5px;">DECL.QUANTITY</th>
			<? }?>
        	<th class="listing-head" width="80" style="padding-left:10px; padding-right:10px;">RATE</th>
        	<th class="listing-head" width="100" style="padding-left:10px; padding-right:10px;">AMOUNT</th>
      </tr>
	 <?		
	  
	 		//echo "here";
	 	}
	 ?>

	   <? } ?>
	   
      <tr bgcolor="#FFFFFF">
	  <?
	  $colSpan = "";
	  if($paymentBy=='E') $colSpan = 5;
	  else $colSpan = 4;
	  ?>
        <td height='30' colspan="<?=$colSpan?>" nowrap="nowrap" class="listing-head" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><? echo number_format($grandTotalEffectiveWt,2);?></strong></td>
        <td height='30' class="listing-item">&nbsp;</td>
        <td height='30' class="listing-item">&nbsp;</td>
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
        <td colspan="6" class="fieldName">&nbsp;</td>
        </tr>
      
      <tr>
        <td class="listing-item" nowrap="nowrap" valign="bottom" style="line-height:8px;"><?=$date?></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
      </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
    </table></td>
  </tr>
  
</table>
</td>
</tr>
</table>
<!--/form-->
	<!--SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	<!--/SCRIPT-->
	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	<? }?>
</body></html>