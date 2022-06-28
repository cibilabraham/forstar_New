<?php
	require("include/include.php");	

	# select record between selected date
	$dateFrom = $g["supplyFrom"];
	$dateTill = $g["supplyTill"];
	$landingCenterId	=	$g["landingCenter"];
	$selectSupplier		=	$g["supplier"];
	$selChallanNo	= 	$g["selChallan"];
	$fishId		=	$g["fish"];
	$processId	=	$g["processCode"];
	$settlementDate	=	$g["selSettlement"];
	$paymentMode 	= 	$g["paymentMode"];
	$viewType	=	$g["viewType"];
	$paidStatus 	= 	$g["selOption"];
	$rmConfirmed	= $g["rmConfirmed"];
	$dateSelectFrom = $g["dateSelectFrom"];
	$offset		= $g["offset"];
	$limit		= $g["limit"];
	$pageNo		= $g["pageNo"];
	$billingCompany	=	$g["billingCompany"];

	$fromDate	= mysqlDateFormat($dateFrom);	
	$tillDate	= mysqlDateFormat($dateTill);

 if ($dateFrom && $dateTill) {
	if ($viewType=='DT' && ($paymentMode=='DWW' || $paymentMode=='EWW') ) {
		$catchEntryRecords	=  $supplieraccountObj->fetchAllCatchEntryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $offset, $limit, $rmConfirmed, $billingCompany);

		#For finding the Grand total and Pagination
		$getAllCatchEntryRecords = $supplieraccountObj ->getDetailedCatchEntryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $rmConfirmed, $billingCompany);

		#Total No.of Records (Pagination)
		$numrows = sizeof($getAllCatchEntryRecords);
	}
	if ($viewType=='SU' && ($paymentMode=='DWW' || $paymentMode=='EWW') ) {
		$catchEntryRecords	= $supplieraccountObj -> getCatchEntrySummaryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $offset, $limit, $rmConfirmed, $billingCompany);
		#For finding the Grand total and Pagination
		$getAllCatchEntryRecords = $supplieraccountObj ->getAllCatchEntrySummaryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $fishId, $processId, $paidStatus, $rmConfirmed, $billingCompany);
		$numrows = sizeof($getAllCatchEntryRecords);		
	}
	
	#supplier Declared Wt Records(Suplier Memo)
	if ($viewType=='SU' && $paymentMode=='DWS') {
		$declaredWtRecords  = $supplieraccountObj->getSupplierDeclaredWtRecords($fromDate,$tillDate, $landingCenterId,$selectSupplier,$selChallanNo,$fishId,$processId,$paidStatus, $rmConfirmed, $dateSelectFrom, $billingCompany);
	}
	
	if ($viewType=='DT' && $paymentMode=='DWS') {
		$declaredWtRecords  = $supplieraccountObj->getDetailedSupplierDeclaredWtRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $fishId, $processId, $paidStatus, $rmConfirmed, $dateSelectFrom, $billingCompany);
	}
}  // Search Ends Here
#Finding Grand Total
if (sizeof($getAllCatchEntryRecords)>0) {

	$grandTotalCatchEntryRate	=	"";
	$grandTotalSettledAmount	= 	"";
	$grandTotalDuesAmount		=	"";
	$totalRate		=	"";
	
	foreach($getAllCatchEntryRecords as $cer) {	
		$weighmentNo	=	$cer[6];
		$supplier		=	$cer[8];
		$gFishId		=	$cer[11];
		$local			=	$cer[16];
		$wastage		=	$cer[17];	
		$soft			=	$cer[18];
		$gradeCountAdj		= 	$cer[44];
		$adjustWt		=	$cer[20] + $gradeCountAdj;			
		$processCodeId		=	$cer[12];
		$processCodeRec		=	$processcodeObj->find($processCodeId);
		$processCode		=	$processCodeRec[2];			
		$netWt			=	$cer[26];		
		$declWt			=	$cer[29];		
		$dailyRateRec		=	$supplieraccountObj->findDailyRate($gFishId);
		$declRate		=	$dailyRateRec[7];		
		$paidStatus		=	$cer[35];	
		
		if ($cer[36]==0) {
			$settelementDate ="";
		} else {
			$settelementDate	=	$cer[36];
		}
		
		$selectWeight		=	$cer[32];
		$selectRate			=	$cer[33];
		$actualRate			=	$cer[34];
		
		if ($selectWeight!="" && $selectWeight!=0.00) {
			$effectiveWt	=	$selectWeight;
		} else {
			$effectiveWt	=	$cer[28];	
		}
		
		$payableWt	=	$cer[28];
		
		if ($selectRate!="" && $selectRate!=0 ) {
			$marketRate	=	$selectRate;
		} else {
			$marketRate		=	$dailyRateRec[6];
		}
		#Number of Grouping
		$numRecords			=	$cer[45];
		if ($summaryView) {
			$selMarketRate	=	number_format(($marketRate/$numRecords),2,'.','');
		} else {
			$selMarketRate	= 	$marketRate;
		}
		$payableRate	=	$dailyRateRec[6];
		
		$totalRate		=	$effectiveWt * $selMarketRate;

		$grandTotalCatchEntryRate	+=$totalRate;	
		
		if($paidStatus=='Y'){
			$checked	=	"Checked";
			$grandTotalSettledAmount	+= $totalRate;
		} else {
			$checked	=	"";
			$grandTotalDuesAmount	+=	$totalRate;
		}
	
	}
}
#End Here

?>
<html>
<title>Supplier Account</title>
<head>
<style type="text/css" media="print">
<!--
/*div.page{
	writing-mode: tb-rl;
}*/

@page port {size: portrait;}
@page land {size: landscape;}

.portrait {page: port;}

.landscape {page: land;}
-->
</style>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<!--link rel=alternate media=print href="PrintSupplierAccount.php"-->
<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</head>
<body class="landscape">

<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2" class="landscape">
  <tr bgcolor=white>
   <td colspan="17" align="center" class="listing-head" >STATEMENT OF SUPPLIERS A/C SETTLEMENT </td>
  </tr><? if ($dateFrom) {?>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">For the Period from <?=$dateFrom?> Till <?=$dateTill?> </td>
  </tr><? }?>
  <tr bgcolor=white>
    <td colspan="17" align="center">&nbsp;</td>
  </tr>
						<?
	
								if( sizeof($catchEntryRecords)){
									$i	=	0;
						?>
	<tr bgcolor=white>
    <td colspan="17" align="center">
	<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999">
	<?
		if( sizeof($catchEntryRecords)){
		$i	=	0;
	?>
  <tr bgcolor="#f2f2f2" align="center">
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">No</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Date</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Wt Challan No </td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Supplier</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Fish</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Process</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Grade</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Count</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Decl.<br />Count</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Net Wt </td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Adj</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Local</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Waste</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Soft</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Eff.Wt</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Decl.Wt</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Market<br />Rate </td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Decl.<br />Rate</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Final Wt</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Final Rate</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Total</td>
    <td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Setld</td>
	<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:10px;">Setl Date</td>
    </tr>
	<?
	$grandTotalRate	=	"";
	$settledAmount  = 	"";
	$duesAmount		=	"";
	foreach ($catchEntryRecords as $cer) {
		$i++;
		$enteredDate		= dateFormat($cer[3]);	
		$weighmentNo		=	$cer[6];
		$displayChallanNum	= 	$cer[48];
		$supplier		=	$cer[8];
		$supplierRec		=	$supplierMasterObj->find($supplier);
		$supplierName		=	$supplierRec[2];		
		$fishId			=	$cer[11];		
		$fishRec		=	$fishmasterObj->find($fishId);
		$fishName		=	$fishRec[1];		
		$local			=	$cer[16];
		$wastage		=	$cer[17];	
		$soft			=	$cer[18];		
		$count			=	$cer[13];		
		$gradeCountAdj  	= 	$cer[44];
		$adjustWt		=	$cer[20] + $gradeCountAdj;		
		$declCount		=	$cer[30];		
		$processCodeId		=	$cer[12];
		$processCodeRec		=	$processcodeObj->find($processCodeId);
		$processCode		=	$processCodeRec[2];		
		$netWt			=	$cer[26];		
		$declWt			=	$cer[29];		
		$dailyRateRec		=	$supplieraccountObj->findDailyRate($fishId);
		$declRate		=	$dailyRateRec[7];		
		$paidStatus		=	$cer[35];	
		if ($cer[36]==0) {
			$settelementDate ="";
		} else {
			$settelementDate	=	$cer[36];
		}
	
		$selectWeight		=	$cer[32];
		$selectRate		=	$cer[33];
		$actualRate		=	$cer[34];
		
		if ($selectWeight!="" && $selectWeight!=0.00) {
			$effectiveWt	=	$selectWeight;
		} else {
			$effectiveWt	=	$cer[28];	
		}
	
		$payableWt	=	$cer[28];		
		if ($selectRate!="" && $selectRate!=0 ){
			$marketRate	=	$selectRate;
		} else {
			$marketRate		=	$dailyRateRec[6];
		}	
		$payableRate	=	$dailyRateRec[6];		
		#Number of Grouping
		$numRecords			=	$cer[45];
		if ($summaryView) {
			$selMarketRate	=	number_format(($marketRate/$numRecords),2,'.','');
		} else {
			$selMarketRate	= 	$marketRate;
		}			
		$totalRate		=	$effectiveWt * $selMarketRate;
		
		$grandTotalRate		+= $totalRate;			
		if ($paidStatus=='Y') {
			$checked	=	"Checked";
			$settledAmount	= $settledAmount +	$totalRate;
		} else {
			$checked	=	"";
			$duesAmount	= $duesAmount +	$totalRate;
		}
	
		$gradeId = $cer[37];
		$gradeCode	=	"";
		if ($count=="") {
			$gradeRec		=	$grademasterObj->find($cer[37]);
			$gradeCode		=	stripSlash($gradeRec[1]);
		}
		
		$dailyCatchEntryId	=	$cer[42];
		
		if ($viewType=='SU') {
			$readonly = "readonly";
		} else {
			$readonly = "";
		}
		$disabled = "";
		$edited	  = "";
		if ($paidStatus=='Y' && $isAdmin==false && $reEdit==false) {
			$disabled = "readonly";
			$edited	  = 1;
		}

		# Resettled Settings
		if ($viewType=='DT') $reSeltledDate = $cer[47];
		$rowColor = "";
		if ($viewType=='DT' && $reSeltledDate!="") 
			$rowColor = "#FFFFCC";
		else $rowColor = "#FFFFFF";
		
	?>
  <tr bgcolor="#FFFFFF">
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px;" align="center">
		<input type="hidden" name="catchEntryId_<?=$i;?>" value="<?=$dailyCatchEntryId;?>"><?=(($pageNo-1)*$limit)+$i?>
    </td>
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$enteredDate?></td>
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$displayChallanNum?></td>
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px; line-height:normal"><?=$supplierName?></td>
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px; line-height:normal;"><?=$fishName?><input type="hidden" name="fishId_<?=$i?>" value="<?=$fishId?>"></td>
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$processCode?><input type="hidden" name="processCodeId_<?=$i?>" value="<?=$processCodeId?>"></td>
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$gradeCode?><input type="hidden" name="gradeId_<?=$i?>" value="<?=$gradeId?>"></td>
    <td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$count?><input type="hidden" name="count_<?=$i?>" value="<?=$count?>"></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$declCount?></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$netWt?></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$adjustWt?></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$local;?></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$wastage?></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$soft?></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$payableWt?><input type="hidden" name="payableWt_<?=$i?>" id="payableWt_<?=$i?>" value="<?=$payableWt?>"></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$declWt?><input type="hidden" name="declWt_<?=$i?>" id="declWt_<?=$i?>" value="<?=$declWt?>"></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><? if($payableRate==""){?> <img src="images/x.gif" width="20" height="20"><? } else { echo $payableRate;}?><input type="hidden" name="payableRate_<?=$i?>" id="payableRate_<?=$i?>" value="<?=$payableRate?>"></td>
    <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><?=$declRate?><input type="hidden" name="declRate_<?=$i?>" id="declRate_<?=$i?>" value="<?=$declRate?>"></td>
    <td nowrap align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><input type="text" name="weight_<?=$i;?>" id="weight_<?=$i;?>" value="<?=$effectiveWt?>" size="3" style="text-align:right;border:none;" onKeyUp="return actualAmount(document.frmSupplierAccount);" onkeydown="return nextBox(event,'document.frmSupplierAccount','weight_<?=$i+1;?>');" <?=$readonly?> <?=$disabled?>></td>
    <td nowrap align="right" style="padding-left:2px; padding-right:2px;font-size:10px;"><input type="text" name="rate_<?=$i;?>" id="rate_<?=$i;?>" value="<?=$marketRate?>" size="3" style="text-align:right;border:none;" onKeyUp="return actualAmount(document.frmSupplierAccount);" onkeydown="return nextBox(event,'document.frmSupplierAccount','rate_<?=$i+1;?>');" <?=$disabled?>></td>
    <td nowrap align="right" style="padding-left:2px; padding-right:2px;font-size:10px;" class="listing-item"><?=$totalRate?>
	<!--<input type="text" name="totalRate_<?=$i;?>" id="totalRate_<?=$i;?>" value="<?=$totalRate?>" size="5" style="text-align:right; border:none;" readonly>-->
    </td>
    <td nowrap align="center">
	<? if($checked){?><img src="images/y.gif" /><? } else {?><img src="images/x.gif" /><? }?><!--
	<input name="paid_<?=$i;?>" type="checkbox" id="paid_<?=$i;?>" value="Y"  class="chkBox" <?=$checked?> <?=$disabled?>>-->
	<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>">
    </td>
	<td align="center" class="listing-item">
		<?=($settelementDate!="")?dateFormat($settelementDate):""?>
		<? if ($reSeltledDate!="") {?>
		<br>
		<span class="listing-item" style="line-height:normal;font-weight:8px;color:maroon">Resetld On:<?=dateFormat($reSeltledDate);?></span>
		<? }?>
	</td>
    </tr>
	
  <? }?>
  
  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	  <tr bgcolor="#FFFFFF">
	  <td colspan="20" align="right" class="listing-head">Total:&nbsp;</td>
	  <td align="right" style="padding-left:2px; padding-right:2px;font-size:10px;" class="listing-item">
		<? echo number_format($grandTotalRate,2);?><!--
		<input name="grandTotalRate" type="text" id="grandTotalRate" size="7" readonly  style="text-align:right; border:none; padding-right:7px;" value="<? echo number_format($grandTotalRate,2);?>">-->
	</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
	    <td colspan="20" align="right" class="listing-head">Grand Total: </td>
	    <td align="right" style="padding-left:2px; padding-right:2px;font-size:10px;" class="listing-item"><b><? echo number_format($grandTotalCatchEntryRate,2);?></b></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    </tr>	
	  <? } else {?>
	  <tr bgcolor="white"> 
      <td colspan="23"  class="err1" height="5" align="center"><?=$msgNoSettlementRecords;?></td>
    </tr>
	<? } 
	if( sizeof($catchEntryRecords)){
	?>
  <tr bgcolor="#FFFFFF"><td colspan="23" height=1>
  <table>
  <tr>
    <td class="fieldName">Total Value:</td>
    <td class="fieldName"><input name="totalValue" type="text" id="totalValue" size="7" readonly  style="text-align:right; padding-right:7px;border:none;" value="<? echo number_format($grandTotalCatchEntryRate,2,'.','');?>"></td>
    <td class="fieldName"> Settled:</td>
  <td><input name="settledAmount" type="text" id="settledAmount" size="7" readonly value="<? echo number_format($grandTotalSettledAmount,2,'.','');?>" style="text-align:right;border:none;"></td>
  <td class="fieldName">Pending: </td>
  <td><input name="duesAmount" type="text" id="duesAmount" size="7" readonly value="<? echo number_format($grandTotalDuesAmount,2,'.','');?>" style="text-align:right;border:none;"></td>
  <td class="fieldName">Paid:</td>
  <td class="fieldName"><input name="paid" type="text" id="paid" size="7" readonly  style="text-align:right; padding-right:7px;border:none;" value="<? echo number_format($grandTotalSettledAmount,2,'.','');?>"></td>
  <td class="fieldName">Due:</td>
  <td class="fieldName">&nbsp;<input name="duesAmount" type="text" id="duesAmount" size="7" readonly value="<? echo number_format($grandTotalDuesAmount,2,'.','');?>" style="text-align:right;border:none;"></td>
  <td class="fieldName"> Payable</td>
  <td class="fieldName"><input name="netPayable" type="text" id="netPayable" size="7" readonly  style="text-align:right;border:none;" value="<? echo number_format($grandTotalDuesAmount,2,'.','');?>"></td>
  </tr>
  </table>  
  </td></tr><? }?></table></td>
  	</tr>	  
  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >	  
	  <? } else { ?>
	 <!-- <tr bgcolor="white"> 
      <td colspan="17"  class="err1" height="10" align="center">
        <?=$msgNoRecords;?>      </td>
    </tr>-->
	<? 
  } 
  if($paymentMode=='DWS') {
  ?>    
      <tr bgcolor="white">
	  <td colspan="4" align="center">
	  <table>
	  <? if(sizeof($declaredWtRecords)>0){?>
	  <tr>
	  <td>
		  <table width="55%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
                                  <tr bgcolor="#f2f2f2" align="center">
								  <? if($viewType=='DT'){?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Date</td> 
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Su. Challan No </td>
									<? }?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Fish</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Process Code</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Grade/Count</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Decl.Qty</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Rate</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Amount</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Setld</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Setld Date </td>
                                  </tr>
                                  <?
								  $j=0;
								  $gradeCode="";
								  $totalWt	=	"";
								  $prevFishId = 0;
								  $prevProcessCodeId = 0;
								foreach($declaredWtRecords as $sdr){
								$j++;
								
								$catchEntryId	=	$sdr[0];
								
								$sChallanDate = $sdr[8];
								$array			=	explode("-",$sChallanDate);
								$supplierChallanDate	=	$array[2]."/".$array[1]."/".$array[0];
								
								$supplierChallanNo = $sdr[7];
								
								$sFishId			=	$sdr[1];
								$fishName = "";
								if($prevFishId!=$sFishId){
									$fishName		=	$sdr[11];
								}
								
								
								$dailyRateRec	=	$supplieraccountObj->findDailyRate($sFishId);
								//$declRate		=	$dailyRateRec[7];
								
								$selectRate		=	$sdr[15];
								$declRate = "";
								if ($selectRate!="") {
									$declRate	=	$sdr[15];
								} else {
									$declRate		=	$dailyRateRec[7];
								}
								
								$processCodeId	=	$sdr[2];	
								$processCode	= "";
								if ($prevProcessCodeId!=$processCodeId) {
									$processCode	=	$sdr[12];
								}
									
								$declCount		=	$sdr[10];
												
								$declWt	=	$sdr[13];
								$totalWt	+=	$declWt;
								$amount  =   $declWt * $declRate;
								$totalAmount +=$amount;	
								
								$declaredEntryId = 	$sdr[14];
								
								$isSettled	=	$sdr[16];
								
								$amountSettled = "";
								if($isSettled=='Y'){
									$amountSettled = "Checked";
								}
								
								$settledDate = $sdr[17];
								$supplierSettledDate = "";
								if ($settledDate!=0){
									$array			=	explode("-",$settledDate);
									$supplierSettledDate	=	$array[2]."/".$array[1]."/".$array[0];
								}
								
								$disabled = "";
								$edited	  = "";
								if($isSettled=='Y' && $isAdmin==false && $reEdit==false){
									$disabled = "readonly";
									$edited	  = 1;
								}
				# Resettled Settings
				$reSeltledDate = "";
				if ($viewType=='DT') $reSeltledDate = $sdr[18];
				$rowColor = "";
				if ($viewType=='DT' && $reSeltledDate!="") 
					$rowColor = "#FFFFCC";
				else $rowColor = "#FFFFFF";
			?>
                               <tr bgcolor="#FFFFFF">
							   <? if($viewType=='DT'){?>
                                 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;" height="25"><?=$supplierChallanDate?></td> 
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$supplierChallanNo?></td>
								  <? }?>
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;" height="25"><?=$fishName?><input type="hidden" name="sFishId_<?=$j?>" value="<?=$sFishId?>"><input type="hidden" name="challanEntryId_<?=$j?>" value="<?=$catchEntryId?>"></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$processCode?><input type="hidden" name="processCodeId_<?=$j?>" value="<?=$processCodeId?>" /></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$declCount?><input type="hidden" name="declCount_<?=$j?>" value="<?=$declCount?>"></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$declWt?><input type="hidden" name="declWt_<?=$j?>" id="declWt_<?=$j?>" value="<?=$declWt?>"><input type="hidden" name="declaredEntryId_<?=$j?>" value="<?=$declaredEntryId?>"></td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$declRate?>
					<!--<input name="rate_<?=$j?>" type="text" id="rate_<?=$j?>" size="4" onKeyUp="return calcAmount(document.frmSupplierAccount);" value="<?=$declRate?>" style="text-align:right" <?=$disabled?> onkeydown="return nextDeclWtBox(event,'document.frmSupplierAccount','rate_<?=$j+1;?>');" autocomplete="off">-->
				</td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$amount?>
					<!--<input name="amount_<?=$j?>" type="text" id="amount_<?=$j?>" size="4" value="<?=$amount?>" style="text-align:right; border:none" readonly>-->
					</td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;font-size:11px;">
						<? if($amountSettled){?><img src="images/y.gif" /><? } else {?><img src="images/x.gif" /><? }?> 
						<!--<input name="settled_<?=$j?>" type="checkbox" id="settled_<?=$j?>" value="Y" class="chkBox" <?=$amountSettled?> <?=$disabled?>><input type="hidden" name="reEdit_<?=$j;?>" value="<?=$edited?>">-->
				</td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;font-size:11px;">
					<?=$supplierSettledDate?>
					<? if ($reSeltledDate!=0) {?>
					<br>
					<span class="listing-item" style="line-height:normal;font-weight:8px;color:maroon">Resetld On:<?=dateFormat($reSeltledDate);?></span>
					<? }?>
				   </td>
                               </tr>
                                  <? 
								  $prevFishId = $sFishId;
								  $prevProcessCodeId = $processCodeId;
								  } 
								  ?>
								   <tr bgcolor="#FFFFFF"><input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$j?>" >
								   <? if($viewType=='DT'){
								   			$colSpan = 5;
										} else {
											$colSpan = 3;
										}
								   ?>
								   
                                    <td colspan="<?=$colSpan?>" nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;font-size:11px;">TOTAL:</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><span class="listing-item" style="padding-left:5px; padding-right:5px;font-size:11px;"><strong><? echo number_format($totalWt,2);?></strong></span></td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;">&nbsp;</td>
                                    <td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><input name="totalAmount" type="text" id="totalAmount" size="6" style="text-align:right; border:none" readonly value="<?=$totalAmount?>"></td>
                                    <td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;">&nbsp;</td>
								    <td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;">&nbsp;</td>
								   </tr>								  
                                </table></td>
								</tr>
								<? } else { ?>
								<tr>
								<td align="center" class="err1"><?=$msgNoSettlementRecords;?></td>
								</tr>
								<? }?>
								</table>
								</td>
								 </tr>
								 <? }?>

</table>
</body>
</html>
