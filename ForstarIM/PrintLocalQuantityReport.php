<?php
	require("include/include.php");
	
	 #Setting No.of Rows
		$numRows 	=	20;
		
	$dateFrom = $g["supplyFrom"];
	$dateTill = $g["supplyTill"];
	
	$weighNumber	= $g["weighNumber"];
  	
	$selectUnit		=	$g["selUnit"];
	$landingCenterId	=	$g["landingCenter"];
	$selectSupplier		=	$g["supplier"];
	#Finding the Plant Name
	$plantRec		=	$plantandunitObj->find($selectUnit);
	$plantName		=	stripSlash($plantRec[2]);
	#Finding SupplierName
	$supplierRec		=	$supplierMasterObj->find($selectSupplier);
	$supplierName		=	$supplierRec[2];
	#Finding Landing Center Name
	$centerRec		=	$landingcenterObj->find($landingCenterId);
	$landingCenterName	=	stripSlash($centerRec[1]);
	
	$fishId			=	$g["fish"];
	$processId		=	$g["processCode"];
	$fromDate		=	mysqlDateFormat($dateFrom);	
	$tillDate		=	mysqlDateFormat($dateTill);
	$billingCompany		= $g["billingCompany"];
	
	#Filter daily catch records	
	if ($weighNumber || ($dateFrom!="" && $dateTill!="")) {
		 
		$dailyCatchReportResultSetObj = $localquantityreportObj->filterDailyCatchEntryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $weighNumber, $billingCompany);
		
		$dailyCatchReportRecords = $dailyCatchReportResultSetObj->getNumRows();		
	}
	
?>
<html>
<head>
<title>Local Quantity Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
@page
{
	size: landscape;
	margin: 2cm;
}
</style>
<style type="text/css" media="print">
@page
{
	size: landscape;
	margin: 2cm;
}
</style>
</head>
<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head" ><font size="4"><?=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-item"><font size="3">DAILY CATCH 
      REPORT </font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="left" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="left">
	<table width="600" align="center" cellpadding="0" cellspacing="0">
	<tr><td colspan="3">
	<fieldset>
	<table width="500" align="center" cellpadding="0" cellspacing="0">
    <tr> 
          <td valign="top">		  
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Material Received at:</td>
                <td class="listing-item">
				<? 
				if($selectUnit!=0)	echo $plantName;
				else echo "All" ;
				?></td>
              </tr>
            </table></td>
          <td valign="top">
		<? if($weighNumber) {?>
		<table width="200" cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" style="padding-left:5px; padding-right:5px;" nowrap>Weighment Challan No:</td>
                <td class="listing-item" nowrap><?=$weighNumber?></td> 
              </tr>
            </table>
		<? } else {?>
	  <table width="200" cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">From:</td>
                <td class="listing-item" nowrap> 
                  <?=$dateFrom?></td>
                <td class="fieldName" nowrap>&nbsp; Till: </td>
                <td class="listing-item" nowrap>&nbsp;&nbsp; 
                  <?=$dateTill?></td>
              </tr>
            </table>
	<? }?>
	</td>
          </tr>
        <tr> 
          <td>
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Landing Center:</td>
                <td class="listing-item" nowrap="nowrap">
				<? if($landingCenterId!=0){ 
				   echo $landingCenterName;
				} else {?> All <? }?></td>
              </tr>
            </table></td>
          <td><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">Supplier:</td>
                <td class="listing-item" nowrap><? if($selectSupplier!=0){
									echo $supplierName;
									} else {?>All<? }?></td>
              </tr>
            </table></td>
          </tr>
      </table></fieldset></td></tr></table></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="left" height="5"></td>
  </tr>
</table>
  <!-- Header Table Ends Here-->
   		<?
			if ($dailyCatchReportRecords>0) {
				
		?>
  <table>
  <tr bgcolor=white> 
    <td colspan="17" align="left">
	<table cellpadding="0" cellspacing="0">     
        <tr bgcolor=white>
          <td colspan="17" align="center" height="5"></td>
        </tr>
        <tr bgcolor=white>
          <td colspan="17" align="center" class="fieldName" style="line-height:15px;"><font size="2">Local Quantity Report</font></td>
        </tr>
        <tr bgcolor=white>
		
          <td colspan="17" align="center" height="5"></td>
        </tr>
      <tr bgcolor=white>
    <td colspan="17" align="center">
	<table class="print">
        <tr bgcolor="#f2f2f2" align="center">
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">Wt Challan No</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">SUPPLIER</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">FISH</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">PROCESS</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">COUNT</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">GRADE</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">REMARKS</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">QUANTITY</th>	
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">RATE</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">AMOUNT</th>	
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Qty</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Rate</th>  
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Total Rate</th>
      </tr>
        <?
	#Finding Total page
	$dailyCatchReportRecordSize = $dailyCatchReportRecords;
	$totalPage = ceil($dailyCatchReportRecordSize/$numRows);
	$i = 0;
	$j = 0;
	
	  	$grandTotalEffectiveWt	=	"";			
		$grandTotalActualAmount = "";	
		$prevFishId	= "";	
		$prevPurchaseSettledDate = "";
		$grandTotalLocalQtyRate = "";
		$grandTotalWastageQtyRate	= "";
		$grandTotalAdjstWtRate = "";
		$totalLocalRate = "";
		$grandTotalLocalRate = "";
		$totalAdjustWt  = "";
		$totalLocalQty = "";
		$totalWastageQty = "";
		$totalSoftQty = "";
		$prevMainSuppId = "";

		while ($dcr=$dailyCatchReportResultSetObj->getRow()) {
			$i++;	
			$catchEntryId		=	$dcr[0];
			$mainSupplierId		= $dcr[8];
			$mainSupplierName	= "";
			if ($prevMainSuppId!=$mainSupplierId) $mainSupplierName = $dcr[56];						
			$challanNo		=	$dcr[52];
			$WtChallanNumber 	=	"";
			if ($prevChallanNo != $challanNo) $WtChallanNumber = $dcr[52];
			$selFishId	= $dcr[11];
			$fishName	= "";
			if ($prevFishId	!= $selFishId) $fishName = $dcr[53];
			$processCode	= $dcr[54];
			$selectRate	= $dcr[33];
			$actualRate	= $dcr[34];		
			$paymentBy	= $dcr[44];
			$receivedBy	= $dcr[48];
			$count		= $dcr[13];
			$countAverage	= $dcr[14];
			$gradeCode = "";
			if ($count == "" || $receivedBy=='B') $gradeCode = stripSlash($dcr[55]);		
			$localQty	= $dcr[16];
			$totalLocalQty += $localQty;

			$wastageQty	= $dcr[17];
			$totalWastageQty += $wastageQty;

			$softQty	= $dcr[18];
			$totalSoftQty   += $softQty;

			#Find the Wastage Rate Percentage
			list($localRatePercent, $wastageRatePercent, $softRatePercent) = $wastageratepercentageObj->getWastageRatePercentage();
			
			$localQtyRate 	= (($selectRate*$localRatePercent/100));
			$wastageQtyRate = (($selectRate*$wastageRatePercent/100));
			$softQtyRate	= (($selectRate*$softRatePercent/100));	

			$totalLocalQtyRate 	= $localQty * $localQtyRate;
			$totalWastageQtyRate 	= $wastageQty * $wastageQtyRate;
			$totalSoftQtyRate	= $softQty * $softQtyRate;

			$grandTotalLocalQtyRate += $totalLocalQtyRate;		
			$grandTotalWastageQtyRate += $totalWastageQtyRate;
			$grandTotalSoftQtyRate	+= $totalSoftQtyRate;

			$gradeCountAdj	=	$dcr[46]; // Don't add $gradeCountAdj in Lcal Qty report (said on 18-01-07)
	
			$adjustWt	=	$dcr[20];

			$totalAdjustWt += $adjustWt;
			$adjustWtRate  	=	$adjustWt * $selectRate;
			$grandTotalAdjstWtRate += $adjustWtRate;

			//Find the Total Wastage Rate
			$totalLocalRate = $adjustWtRate + $totalLocalQtyRate + $totalWastageQtyRate + $totalSoftQtyRate;

			$grandTotalLocalRate += $totalLocalRate;
			
			$actualWt = $effectiveWt	=	$dcr[28];
			$selectWeight	=	$dcr[32];	
			$remarks		=	$dcr[23];
			
			$effectiveWt = ($selectWeight!="" && $selectWeight!=0.00 && $selectWeight>0)?$selectWeight:$effectiveWt;
			$wtDiffStyle = ($actualWt!=$selectWeight)?"font-weight:bold;font-style:italic;":"";

			$grandTotalEffectiveWt	+=	$effectiveWt;
			$grandTotalActualAmount += 	$actualRate;
		?>
   <tr bgcolor="#FFFFFF">
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$WtChallanNumber?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$mainSupplierName?></td>	
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$fishName?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$processCode?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" nowrap="nowrap"><?=$count?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$gradeCode?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$remarks?></td>	
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt; <?=$wtDiffStyle?>" align="right"><?=number_format($effectiveWt,2);?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$selectRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$actualRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$adjustWt?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=number_format($adjustWtRate,2,'.','');?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$localQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalLocalQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$wastageQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalWastageQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$softQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalSoftQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($totalLocalRate,2,'.','');?></strong></td>	
      </tr>
	<?
		if($i%$numRows==0 && $dailyCatchReportRecordSize!=$numRows)
		{
			$j++;
	?>
		    </table></td></tr>
			<tr><td><table width="98%" cellpadding="2">
      
      <tr>
        <td colspan="6" height="10"></td>
        </tr>
      
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:10px">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
	  </table></td></tr></table>
		<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
								  <table>
								  <tr>
								    <td colspan="17" class="listing-head" align="center"><font size="3"><?=$companyArr["Name"];?></font></td>
								    </tr>
								  <tr>
								    <td colspan="17" height="5"></td>
								    </tr>
								  <tr>
								  <td colspan="17">
									<table cellpadding="0" cellspacing="0">
									<tr>
									  <td class="fieldName" style="line-height:15px;" align="center"><font size="2">Local Quantity Report</font>- Cont.</td>
									  </tr>
									<tr><td height="5"></td></tr>
									 <tr>
									 <td colspan="17">
	<table class="print">
         <tr bgcolor="#f2f2f2" align="center">
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">Wt Challan No</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">SUPPLIER</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">FISH</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">PROCESS</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">COUNT</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">GRADE</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">REMARKS</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">QUANTITY</th>	
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">RATE</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">AMOUNT</th>	
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Qty</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Rate</th>  
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Total Rate</th>
      </tr>
                 <?
			}
		  	$prevChallanNo = $challanNo;
	  		$prevFishId	= $fishId;
			$prevMainSuppId = $mainSupplierId;
	  	  }
		 ?>
<tr bgcolor="#FFFFFF">
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-head" align="right" nowrap style="padding-left:2px; padding-right:2px; font-size:7pt;">Total:</td>	
	<td height='20' class="listing-item">&nbsp;</td>
	 <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalEffectiveWt,2);?></strong></td>
        <td height='20' class="listing-item">&nbsp;</td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? //echo number_format($grandTotalActualAmount,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalAdjustWt,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalAdjstWtRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalLocalQty,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalLocalQtyRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalWastageQty,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalWastageQtyRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalSoftQty,2);?></strong></td>
	<td height='20' nowrap="nowrap" class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalSoftQtyRate,2);?></strong></td> 
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><strong><? echo number_format($grandTotalLocalRate,2);?></strong></td>	
      </tr>
    </table>
  </td>
    </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center">	
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#FFFFFF"> 
          <td align="center" height="5"></td>
        </tr>    
		 <tr><td><table width="98%" align="center" cellpadding="2">
      
      <tr>
        <td colspan="6" height="10"></td>
        </tr>
      
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
    </table>
	
	</td></tr></table>
	</td></tr></table>
	 </td>
  </tr>  
<? //}?>
</table></td></tr>
</table>  				
<? }?>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</body></html>