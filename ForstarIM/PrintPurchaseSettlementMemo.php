<?php
	require("include/include.php");	
	ob_start();	
	
	$selSuppChallanNo = $g["selSuppChallanNo"];
	$challanMainId	= $g["challanMainId"];
	$printMode = false;
	# Payment By (E->Effective Wt && D->Declared Wt)
	$paymentBy = $dailycatchreportObj->getPaymentType($challanMainId);

	# -------------------- Billing Company started -------	
	list($billingCompanyId, $selWeighmentNo, $alphaCode, $setlmntPrintStatus) = $purchasereportObj->billingCmpnyRecId($challanMainId);
		
	if ($billingCompanyId>0) {	// Getting Rec from other billing company
		list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $billingCompanyObj->getBillingCompanyRec($billingCompanyId);
	} else {	// Getting Rec from Company Details Rec
		list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
	}	
	$displayAddress		= "";
	$displayTelNo		= "";
	if ($companyName)	$displayAddress = $address."&nbsp;".$place."&nbsp;".$pinCode;
	if ($telNo)		$displayTelNo	= $telNo;
	if ($faxNo)		$displayTelNo	.= "&nbsp;, Fax No:&nbsp;".$faxNo;	
	# -------------------- Billing Company Ends Here -------

	if ($challanMainId!=0) {		
		$dailyCatchReportRecords = $dailycatchreportObj ->fetchAllCatchReportRecords($challanMainId);
		$dailyCatchReport =  $dailyCatchReportRecords;
		
	} 
	
	
	//$confirm = $dailyCatchReport[0][45];
	if ($setlmntPrintStatus=='Y') 	$numCopy	=	1;
	else		 		$numCopy	=	2;
	

	#supplier Declared Wt Records(Suplier Memo)
	if ($paymentBy=='D') {
		$dailyCatchReportRecords = $purchasereportObj->getSuppDecWtRecords($challanMainId, $billingCompanyId);
		# Get Supplier Challan Date
		list($supplierChallanDate, $subSupplierId, $selSuppChallanNo) = $purchasereportObj->getSuppChallanDate($challanMainId, $billingCompanyId);
		$dateS			=	explode("-",$supplierChallanDate); //2007-06-27
		$supplierChallanDate	= 	date("j M Y", mktime(0, 0, 0, $dateS[1], $dateS[2], $dateS[0]));
		$subSupplierName = "";
		if ($subSupplierId!=0) {
			$subsupplierRec		=	$subsupplierObj->find($subSupplierId);
			$subSupplierName	=	stripSlash($subsupplierRec[2]);
		}
	}
		
	if ($paymentBy=='E' && ($challanMainId!="" || $challanMainId!=0)) {
		$subSupplierId = $dailycatchreportObj->getEffectiveWtSupChallanDt($challanMainId);
		if ($subSupplierId!=0) {
			$subsupplierRec		= $subsupplierObj->find($subSupplierId);
			$subSupplierName	= stripSlash($subsupplierRec[2]);
		}
	}	


	// Setting Num of Rows (D=14)
	$numRows =	14; 
	$dailyCatchReportSize = sizeof($dailyCatchReportRecords);
	$totalPage = ceil($dailyCatchReportSize/$numRows);
	//printr($dailyCatchReportRecords);
	//echo "$billingCompanyId, $selWeighmentNo, $alphaCode";
	#Checking Print Mode
	if ($p["printButton"]!="") $printMode = true;
	else if ($g["printButton"]!="") $printMode = true;
	
	#If Printed update the status Y in t_dailycatch_main
	if ($printMode)	$challanRecUptd = $purchasereportObj->updateChallanSetlmntPrintStatus($challanMainId);
?>
<html>
<head>
<title>PURCHASE SETTLEMENT MEMO</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function printDoc()
{
	window.print();		
	return false;
}
function enblePrintBtn()
{
	document.getElementById("printButton").style.display="block";
}
function printThisPage(printbtn)
{

	enableSymbolRow(<?=$numCopy?>, <?=$totalPage?>);
	document.getElementById("printButton").style.display="none";	

	if (!printDoc()) {
		setTimeout("enblePrintBtn()",4000);
		setTimeout("disableSymbolRow(<?=$numCopy?>, <?=$totalPage?>)",4000);			
	}
}
// Disable row
function disableSymbolRow(numCpy, totalPage)
{	
	for (i=0;i<numCpy; i++) {
		for (j=0;j<totalPage;j++) {			
			document.getElementById("printSymbol_"+i+"_"+j).style.display="none";
			document.getElementById("revPrintSymbol_"+i+"_"+j).style.display="none";
		}	
	}
}
// Enable a Row
function enableSymbolRow(numCpy, totalPage)
{
	for (i=0;i<numCpy; i++) {
		for (j=0;j<totalPage;j++) {			
			document.getElementById("printSymbol_"+i+"_"+j).style.display='';
			document.getElementById("revPrintSymbol_"+i+"_"+j).style.display='';
		}
	}
}

</script>
<!-- KD 13-11-19 -->
<style type="text/css">
#bg-text
{
	z-index:0;
    color:grey;
    font-weight: lighter;
    color: #D3D3D3;
    font-size:80px;
    transform:rotate(340deg);
    -webkit-transform:rotate(340deg);
    position: absolute;
    top :55%;
    left: 42%;
     filter: gray; /* IE */
    -webkit-filter: grayscale(1); /* Old WebKit */
    -webkit-filter: grayscale(100%); /* New WebKit */
    filter: url(resources.svg#desaturate); /* older Firefox */
    filter: grayscale(100%); /* Current draft standard */
}
@media print {
* {
    -webkit-print-color-adjust: exact !important; /*Chrome, Safari */
    color-adjust: exact !important;  /*Firefox*/
  }
}
</style>
<!-- KD 13-11-19 -->
</head>
<body onload="disableSymbolRow(<?=$numCopy?>, <?=$totalPage?>);">
<form name="frmPrintPurchaseSettlementMemo">
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<?if($printMode==true) {?>
<tr>
<td align="right" nowrap valign="top"><input name="printButton" type="button" id="printButton" value="Re-Print" class="button" onClick="printThisPage(this);" style="display:block" title="Click to Print"></td>
</tr>
<? }?>
<? if ($printMode==false && $numCopy>1) {?>
<tr>
<td align="right" nowrap valign="top"><input name="printButton" type="submit" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
<? }?>
<? if ($numCopy==1) {?>
<tr>
<td align="right" nowrap valign="top"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
<? }?>
<!--<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>-->
</table>
<?php	
$xfor = 0; //KD 13-11-19
$yfor = " "; //KD 20-11-19
 for ($print=0;$print<$numCopy;$print++) {
	
	if (sizeof($dailyCatchReport)>0) {			
		// Finding Supplier Record		
		$supplierRec		=	$supplierMasterObj->find($dailyCatchReport[0][8]);
		$supplierName		=	$supplierRec[2];
		$supplierAddr		=	$supplierRec[3];
		$supplierPAN		=	$supplierRec[13];
		// print_r($supplierRec);
		//Finding Landing Center Record
		$centerRec			=	$landingcenterObj->find($dailyCatchReport[0][7]);
		$landingCenterName		=	stripSlash($centerRec[1]);
		
		// Finding Plant Record
		$plantRec			=	$plantandunitObj->find($dailyCatchReport[0][1]);
		$plantName			=	stripSlash($plantRec[3]); //// Rekha added $plantRec[2]
		//$bill_company			=	stripSlash($plantRec[7]);  // rekha added on 8 june 2018	
		$billCompany_AlphaCode = $alphaCode; //$dailyCatchReport[0][49]; // KD 13-10-19
		
		$date		=	date("j M Y");
		$time		=	date("g:i a");
		$Date1				=	explode("-",$dailyCatchReport[0][3]); //2007-06-27
		$weighChallanDate		= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[2], $Date1[0]));
		
		$enteredDateF			=	explode(" ",$dailyCatchReport[0][2]); //2007-06-30 16:34:09
		$Time1				=	explode(":",$enteredDateF[1]);
		$weighChallanTime		= 	date("g:i a", mktime($Time1[0],$Time1[1]));
	
		$vechNo				=	$dailyCatchReport[0][4];		
		$supplierChallanNo		=	($selSuppChallanNo!="")?$selSuppChallanNo:$dailyCatchReport[0][5];

		$selectTime			=	explode("-",$dailyCatchReport[0][43]);
		$recordedTime			=	$selectTime[0].":".$selectTime[1]."&nbsp;".$selectTime[2];
	} // Size check ends here
	
?>

<table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="5"><?=$companyName?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center"><?=$displayAddress?><!--M53, MIDC, Taloja, New Bombay 410208--></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" >Tel: <?=$displayTelNo?><!--022 2741 0807 / 2741 2376--></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="3">PURCHASE SETTLEMENT MEMO</font>		   </td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <? 
		   if($setlmntPrintStatus=='Y'){
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
			?>		   </td>		 
		 </tr>
	</table>	</td>
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
               <td class="listing-item" nowrap="nowrap" colspan="2" height="25"><font size="2.5">
                 <?=$supplierName?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-item" width='200' height="55" colspan="2"><font size="2.5">
                 <?=$supplierAddr?>
               </font></td>
             </tr>
	<?
		if ($subSupplierName!="") {
	?>
	 	<tr>
               <td class="listing-head" nowrap="nowrap" height="25">Sub Supplier:</td>
               <td class="listing-item" nowrap="nowrap" height="25"><font size="2">
                 <?=$subSupplierName?>
               </font></td>
             </tr>	
	<?
		}
	?>
             <tr>
               <td class="listing-head" nowrap="nowrap" height="25">PAN No of Party :</td>
               <td class="listing-item" nowrap="nowrap" height="25"><font size="2">
                 <?=$supplierPAN?>
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
	<? if ($selSuppChallanNo!="" && $paymentBy=='D') {?>
	     <tr>
               <td class="listing-head" nowrap="nowrap" height="25">Supplier Challan Date:</td>
               <td class="listing-item" nowrap="nowrap"><?=$supplierChallanDate;?></td>
             </tr>
	<? }?>
           </table></td>		
		   <td class="listing-head" nowrap="nowrap" align='right' valign='top'>			
			<table width="98%" cellpadding="0" cellspacing="0"  class="tdBoarder">
				  <tr>
					<td class="listing-head" height="35">Wt Date/Time:</td>
					<td class="listing-item" nowrap="nowrap"><?=$weighChallanDate?> - <?=$recordedTime?></td>
				  </tr>
				  <tr>
					<td class="listing-head" height="35">Wt Challan No:</td>
					<td class="listing-item"><?=$alphaCode."-".$selWeighmentNo?></td>
				  </tr>
				  
				  <tr>
					<td class="listing-head" height="35">Supplied At:</td>
					<td class="listing-item"><?=$plantName?></td>
				  </tr>
				  
				  <tr>
					<td class="listing-head" nowrap="nowrap" height="35">Accepted By: </td>
					<td class="listing-item" nowrap="nowrap">&nbsp;</td>
				  </tr>
				  <!-- KD 13-11-19 -->
				  <?php
				  if(count($dailyCatchReport) > 0){
				  $TodayDate = date("Y.m.d");
				  $ADate = explode(".",$TodayDate);
				  $YearSplit = $ADate[0];
						$arrYearSplit = str_split($YearSplit, strlen($YearSplit)/2);
					 $TodayDateTime=date("Y-m-d h:i:s A");
					 $WatermarkCode	= $arrYearSplit[1].$ADate[1].$ADate[2]; 
				  $WtChallanNoWM = $alphaCode."-".$selWeighmentNo;
				  $WtDateWM = $weighChallanDate."-".$recordedTime;
				  //$WTUser = $_SESSION["userWaterMark"];
				  $WTUser = $sessObj->getValue("userName");							 
      if($xfor == 0){
		  $yfor = 1; //KD Added 20-11-19
		  $fetchStaffName = $waterMarkObj->fetchStaffNameRec($WTUser);
	   			$StaffName = $fetchStaffName[0][0]; //KD Added 20-11-19
      	$fetchWMrecordsMonth = $waterMarkObj->fetchAllRecords();		
				  if($fetchWMrecordsMonth){
				  	 $dateRecord = date("Ymd", strtotime($fetchWMrecordsMonth[0][6]));
				  	 $a2 = $dateRecord;
				  } else {
				  		$a2 = date("Ymd");
				  }
				  $a1 = date("Ymd");
				  $monthDiff = (substr($a1, 4, 2) - substr($a2, 4, 2));
				  if($monthDiff == 0){
							 if($fetchWMrecordsMonth){
							 	 //$cnt_plus = substr(str_replace($billCompany_AlphaCode, '', $fetchWMrecordsMonth[0][1]), -5);						//$newstring = substr($fetchWMrecordsMonth[0][1], -5);
							 		$newstring = substr($fetchWMrecordsMonth[0][1], -5);
							 		//echo $newstring;
									 $newstring = $newstring + 1;
									 //$sumNewString = $cnt_plus + 1;
									 $WatermarkCode = $WatermarkCode.$newstring;
								} else {
							 		$WatermarkCode = $WatermarkCode."10000";
							 		}							
						} else {
							 $WatermarkCode = $WatermarkCode."10000";
							}
			   //$WatermarkCode = $WatermarkCode.$billCompany_AlphaCode;
				   $WMRecIns = $waterMarkObj->addWatermarkCodeRecs($WatermarkCode,$WtChallanNoWM,$companyName,$plantName,$WtDateWM,$TodayDateTime,$StaffName);
						}						
						} else {
							$WatermarkCode = '';
						}			
				  ?>
				  <tr><td colspan="2">&nbsp;</td></tr>
				  <tr>
				  	<td colspan="2" class="listing-item" nowrap="nowrap" align="right" style="font-size:12px;font-weight: lighter;color: #D3D3D3">&nbsp;				  		
				  		<?php if($xfor == 0){ echo $WatermarkCode; } $xfor++; ?>				  		
				  		</td>
				  </tr>
				 <!-- KD 13-11-19 -->
				</table>		   </td>		 
		 </tr>
	</table>	</td>
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
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
		<table align="center" width="99%" cellpadding="0" cellspacing="1">
		<tr>
			<td colspan="17" align="center" class="printPageHead" style="line-height:normal;vertical-align:bottom;">SUMMARY OF COUNTWISE SUPPLY RECEIVED </td>
		</tr>
		<TR id="printSymbol_<?=$print?>_0">
			<td class="listing-item" style="line-height:10px;font-size:14px;letter-spacing:2px;vertical-align:top;" align="center">
			[AMT_SYM] <?//=0?>	
			</td>
		</TR>
	</table>	
    </td>
  </tr>	
  <!--<tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head">SUMMARY OF COUNTWISE SUPPLY RECEIVED </td>
  </tr>
	<tr bgcolor=white style="line-height:normal;"> 
    <td colspan="17" align="center">
	<table align="center" width="99%" cellpadding="0" cellspacing="0">
		<TR>
			<td class="listing-item" style="line-height:normal;font-size:14px;letter-spacing:2px;" align="center">
			[AMT_SYM]	
			</td>
		</TR>
	</table>	
    </td>
  </tr>-->
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
		if (sizeof($dailyCatchReportRecords)) {
			$i	=	0;
			//$paymentBy	=	$dailyCatchReportRecords[0][44];
	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt" width="100">FISH</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">PROCESS</th>
		<? if($paymentBy=='E') { ?>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">COUNT</th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">GRADE</th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">REMARKS</th> 
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">QUANTITY</th>
		<? } else {?>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">DECL.COUNT</th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">REMARKS</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">DECL.QUANTITY</th>
		<? }?>
        <th class="listing-head" width="80" style="padding-left:10px; padding-right:10px; font-size:8pt">RATE</th>
        <th class="listing-head" width="100" style="padding-left:10px; padding-right:10px; font-size:8pt">AMOUNT</th>
      </tr>
      <?php
	  	$grandTotalEffectiveWt	=	"";
		$j = 0;	
		$grandTotalActualAmount = "";	
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
			/*
			$declWt			=	$dcr[29];
			$declCount		=	$dcr[30];
			*/
	
			$dailyRateRec		=	$supplieraccountObj->findDailyRate($fishId);
			//$declRate		=	$dailyRateRec[7];
	
			$paidStatus			=	$dcr[35];
			
			$selectWeight		=	$dcr[32];
			$selectRate			=	$dcr[33];
			$actualRate			=	$dcr[34];
			$grandTotalActualAmount	+= $actualRate;
	
			$dailyCatchEntryId	=	$dcr[42];
	
			//echo "-->".$paymentBy	=	$dcr[44];
			$receivedBy	=	$dcr[48];
	
			if($paymentBy=='E')
			{	
					$count			=	$dcr[13];
					$countAverage	=	$dcr[14];
					$gradeCode = "";
					if($count == "" || $receivedBy=='B'){
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
						
						if ($dcr[41]=='N') {
							$netGrossWt	=	$dcr[26];
						}
						
						$actualWt = $effectiveWt	=	$netGrossWt - $adjustWt;
		
		} else if ($paymentBy=="D") {	
			$fishName		=	$dcr[11];
			$processCode		=	$dcr[12];
			$actualWt = $declWt		=	$dcr[13]; // From Decl wt record set
			$declCount		=	$dcr[10];	
			$count		=	$declCount;
			$effectiveWt	=	$declWt	;
			$declRate	= $dcr[15];
			$declAmount  = $declWt * $declRate;
			$grandTotalActualAmount += $declAmount;
		}
		/* Modified on 1-1-09
		 $remarks	= $dcr[23];		
		*/
		$remarks = ($paymentBy=='E')?$dcr[23]:$dcr[14];
		$effectiveWt = ($selectWeight!="" && $selectWeight!=0.00 && $selectWeight>0)?$selectWeight:$effectiveWt;

		$wtDiffStyle = ($actualWt!=$selectWeight)?"font-weight:bold;font-style:italic;":"";

		$grandTotalEffectiveWt	+=	$effectiveWt;
		if($effectiveWt!=0){
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;"><?=$fishName?></td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;"><?=$processCode?></td>
		<? if($paymentBy=='E') { ?>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" nowrap="nowrap"><?=$count?></td>
		<td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;"><?=$gradeCode?></td>
		<td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;"><?=$remarks?></td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt; <?=$wtDiffStyle?>" align="right"><?=number_format($effectiveWt,2);?></td>
		<td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><?=$selectRate?></td>
		<td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><?=$actualRate?></td>
		<? } else {?>
		
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;"><?=$declCount?></td>
		<td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;"><?=$remarks?></td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><?=number_format($declWt,2);?></td>
		<td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><?=$declRate?></td>
		<td height='30' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><?=number_format($declAmount,2,'.','');?></td>
		<? }?>
      </tr>
	  
	  	<?	  
		if($i%$numRows==0 && $dailyCatchReportSize!=$numRows){
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td>
		<table width="98%" cellpadding="3">
        <tr>
        <td colspan="6" height="10"></td>
        </tr>
      
      <!--tr>
        <td class="listing-item" nowrap="nowrap" valign="bottom" style="line-height:8px;"><?=$date?>
          <strong>Page <?=$j?> of <?=$totalPage?></strong></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
      </tr-->
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
		<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Verified by</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>	
	  <tr>
		<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><?=$date?></td>
        </tr>
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
	<tr><TD height="10"></TD></tr>	
	<tr>
		<td colspan="6" valign="middle" nowrap="nowrap" class="listing-item" align="left"><strong>This Purchase Settlement is issued subject to Mumbai Jurisdiction.</strong></td>
        </tr>
	<TR id="revPrintSymbol_<?=$print?>_<?=$j-1?>">
			<td class="listing-item" style="line-height:10px;font-size:14px;letter-spacing:8px;vertical-align:top;" align="left" colspan="6">
			[AMT_REV_SYM]	<?//=$j-1?>
			</td>
		</TR>	
    </table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <!--P style="page-break-after:always">&nbsp;</P-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">	  		
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="3"><?=$companyName?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr>
  <!--tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" >M53, MIDC, Taloja, New Bombay 410208</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" >Tel: 022 2741 0807 / 2741 2376</td>
  </tr-->
		</table>		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head"><table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="2">PURCHASE SETTLEMENT MEMO</font> - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <? 
		   if($setlmntPrintStatus=='Y'){
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
			?>		   </td>		 
		 </tr>
	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr><td>
		<table width="100%" cellpadding="0" cellspacing="3" class="tdBoarder">
        <tr> 
          <td valign="top"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="listing-head" valign="top">Supplier:</td>
                <td class="listing-item" style="line-height:normal;" valign="top"><?=$supplierName?>&nbsp;</td>
              </tr>
            </table></td>
          <td valign="top">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="listing-head" nowrap="nowrap">Landing Center:</td>
                <td class="listing-item" nowrap><?=$landingCenterName?>&nbsp;</td>
              </tr>
            </table></td>
          <td valign="top">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="listing-head" nowrap="nowrap">Wt Challan No:</td>
                <td class="listing-item" nowrap><?=$alphaCode.$selWeighmentNo?></td>
              </tr>
            </table></td>
          </tr>
        <tr> 
          <td class="fieldName">
		  <table cellpadding="0" cellspacing="0">
             <tr> 
                <td class="listing-head" nowrap>Supplied At:</td>
                <td class="listing-item" nowrap><?=$plantName?></td>
              </tr>
            </table>
		  </td>
          <td class="listing-item">
	<table cellpadding="0" cellspacing="0">
              <!--<tr> 
                <td class="listing-head">Date/Time:</td>
			<td class="listing-item" nowrap="nowrap"><?=$weighChallanDate?> - <?=$recordedTime?>&nbsp;&nbsp;</td>
              </tr>-->
            </table></td>
          <td class="fieldName"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="listing-head" nowrap>Vehicle No:</td>
                <td class="listing-item" nowrap>&nbsp;<?=$vechNo?>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
      </table>
	  </td></tr></table></td>
	    </tr>	  
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>
		<tr bgcolor=white>
    <td colspan="17" align="center">
		<table align="center" width="99%" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="17" align="center" class="printPageHead" style="line-height:normal;vertical-align:bottom;">SUMMARY OF COUNTWISE SUPPLY RECEIVED </td>
		</tr>
		<TR id="printSymbol_<?=$print?>_<?=$j?>">
			<td class="listing-item" style="line-height:10px;font-size:14px;letter-spacing:2px;vertical-align:top;" align="center">
			[AMT_SYM]<?//=$j?>	
			</td>
		</TR>
	</table>	
    </td>
  </tr>
	<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr><td colspan="17" align="center" class="fieldName">
	  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	    <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt" width="100">FISH</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">PROCESS</th>
		<? if($paymentBy=='E') { ?>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">COUNT</th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">GRADE</th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">REMARKS</th> 
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">QUANTITY</th>
		<? } else {?>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">DECL.COUNT</th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">REMARKS</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">DECL.QUANTITY</th>
		<? }?>
        <th class="listing-head" width="80" style="padding-left:10px; padding-right:10px; font-size:8pt">RATE</th>
        <th class="listing-head" width="100" style="padding-left:10px; padding-right:10px; font-size:8pt">AMOUNT</th>
      </tr>
   <?
	#Main Loop ending section 
			} // Ending Effective wt 0 section edited on 08-01-08
	       }
	}
   ?>
      <tr bgcolor="#FFFFFF">
	  <?
	  $colSpan = "";
	  if($paymentBy=='E') $colSpan = 5;
	  else $colSpan = 4;
	  ?>
        <td height='30' colspan="<?=$colSpan?>" nowrap="nowrap" class="listing-head" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
		<strong><? echo number_format($grandTotalEffectiveWt,2);?></strong>
	</td>
        <td height='30' class="listing-item">&nbsp;</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
		<strong><? echo number_format($grandTotalActualAmount,2);?></strong>
	</td>
      </tr>
    </table>
	<? 
	//echo strlen("SUMMARY OF COUNTWISE SUPPLY RECEIVED");
	$textLength = strlen("SUMMARY OF COUNTWISE SUPPLY RECEIVED");
	//$grandTotalEffectiveWt = 909909;
	#Converting Numbers to Symbol 
	list($amountSymbol,$reverseSymbol) = $dailycatchreportObj->convertAmountToSymbol(number_format($grandTotalEffectiveWt,0,'',''),$textLength);	
	?>
</td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>

  <!--tr bgcolor=white> 
    <td colspan="17" align="center">
	<table align="center" bgcolor="#CCCCCC"  class="print" width="99%">
		<TR>
			<Th class="listing-item" style="line-height:normal;font-size:14px;letter-spacing:2px;">
				------ ----- <span style="font-size:7px;">*</span> --------
				
					
			</Th>
		</TR>
	</table>	
    </td>
  </tr-->
  <tr bgcolor=white style="background-repeat: no-repeat;
  background-position: center center;background-image: url('images/ForstarLogoTRANSPARENT_rotate.png') !important;"><!-- KD.13-11-19 -->
    <td colspan="17" align="center" class="fieldName">
	<table width="98%" cellpadding="3" >
      
      <tr>
        <td colspan="7" height="10"></td>
        </tr>
      
      <!--tr>
        <td class="listing-item" nowrap="nowrap" valign="bottom" style="line-height:8px;"><?=$date?><strong>Page <?=$totalPage?> of <?=$totalPage?></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
      </tr-->
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
		<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Verified by</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>	
	  <tr>
        <td colspan="7" valign="top" nowrap="nowrap" class="listing-item" style="line-height:5px;"><?=$date?></td>
        </tr>
	  <tr>
	    <td colspan="7" valign="top" nowrap="nowrap" class="listing-item" >(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<!-- KD Added 20-11-13-->
	    <?php
	    if($yfor != ""){
	      $fetchStaffName = $waterMarkObj->fetchStaffNameRec($WTUser);
	  			 $StaffName = $fetchStaffName[0][0];
	  			 $yfor="";
	  			?>
	  			<tr><td class="listing-item" nowrap="nowrap">
	    	Printed By : <?=$StaffName?> </td></tr>
	    <?php
	    } else {
					?>
	    <tr><td class="listing-item" nowrap="nowrap">
	    	</td></tr>
					<?php
	    }	    
	    ?>
	    <!-- KD Added Ends-->
	<tr><TD height="10"></TD></tr>	
	<tr>
		<td colspan="7" valign="middle" nowrap="nowrap" class="listing-item" align="left"><strong>This Purchase Settlement is issued subject to Mumbai Jurisdiction.</strong></td>
        </tr>
		<TR id="revPrintSymbol_<?=$print?>_<?=$totalPage-1?>">
			<td class="listing-item" style="line-height:10px;font-size:14px;letter-spacing:8px;vertical-align:top;" align="left" colspan="6">
			[AMT_REV_SYM]	<?//=$totalPage-1?>
			</td>
		</TR>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<input type="hidden" name="challanMainId" value="<?=$challanMainId?>" />
</form>	
<!--P style="page-break-after:always"></P-->
<?php if (($print+1)!=$numCopy) { ?>
<div style="page-break-after:  always">&nbsp;</div>
<?php }?>
</body>
</html>
	<?php
		} // No of copy ends here
	?>
<?php
$out1 = ob_get_contents(); 
ob_end_clean();
$out1 = str_replace("[AMT_SYM]", $amountSymbol, $out1);
$out1 = str_replace("[AMT_REV_SYM]", $reverseSymbol, $out1);
echo $out1;
?>
