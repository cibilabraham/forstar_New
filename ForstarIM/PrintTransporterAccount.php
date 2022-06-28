<?php
	require("include/include.php");	

	# select record between selected date
	$dateFrom = $g["supplyFrom"];
	$dateTill = $g["supplyTill"];
	$selTransporter = $g["transporter"];
	$offset		= $g["offset"];
	$limit		= $g["limit"];
	$pageNo		= $g["pageNo"];
	$billType 	= $g["billType"];

	if ($selTransporter) {
		$transporterRec		= $transporterMasterObj->find($selTransporter);
		$transporterName	= stripSlash($transporterRec[2]);
	}

 	if ($dateFrom && $dateTill) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);	
		# Get Paging Records
		$transporterInvoiceRecords = $transporterAccountObj->fetchTransporterInvoicePagingRecords($selTransporter, $fromDate, $tillDate, $offset, $limit, $billType);
		# Get All Records
		$getAllTransInvoiceRecords = $transporterAccountObj->filterTransporterInvoiceRecords($selTransporter, $fromDate, $tillDate, $billType);
	}  // Search Ends Here

	#Finding Grand Total	
	if (sizeof($getAllTransInvoiceRecords)>0 && $billType=='OD') {

		$grandTotalTransporterAmt = 0;
		$grandTotalSettledAmount  = 0;
		$grandTotalDuesAmount	= 0;
		foreach ($getAllTransInvoiceRecords as $tir) {
			$salesOrderId		= $tir[0];
			$salesOrderNo		= $tir[1];
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.',''); // Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];
			if ($docketNum!="") {
				list($groupedTotalWt, $numGroup) = $transporterAccountObj->getTrptrRecs($offset, $limit, $transporterId, $fromDate, $tillDate,  $billType, $distributorId, $cityId, $docketNum);

				$selGrossWt = ($numGroup>1)?$groupedTotalWt:$totalWt;
				# Find the Transporter rate Per Kg
				list($ratePerKg, $transporterRateEntryId, $rateType) = $transporterAccountObj->getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $selGrossWt);
				
				$freightCost = "";
				$docketCharge = "";
				$FOV = "";
				$total = "";
				$serviceTaxRate = "";
				$grandTotal = "";
				$surchargeAmt = "";
				if ($ratePerKg!="" && $ratePerKg!=0) {
					$freightCost	= $totalWt*$ratePerKg;
					$freightCost 	= ($rateType!="FRC")?$freightCost:$ratePerKg;
					# Get Other Charges
					# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %, Surcharge %
					list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surchargePercent) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);
					$docketCharge 	= $docketCharge/$numGroup;
					$odaCharge 	= $odaCharge/$numGroup;
					$odaApplicable = $tir[45];
					$selOdaRate    = $tir[46];
					$odaRate       = ($odaApplicable=='Y')?(($selOdaRate!=0)?$selOdaRate:$odaCharge):"";

					$FOV	= number_format((($invoiceValue*$fovCharge)/100),2,'.','');	
					$total = $freightCost+$FOV+$docketCharge+$odaRate;
					# Surcharge calc
					$calcSurchargeAmt = $freightCost+($FOV+$docketCharge+$odaRate);
					$surchargeAmt = number_format((($calcSurchargeAmt*$surchargePercent)/100),2,'.','');
					$total += $surchargeAmt;

					$serviceTaxRate = number_format((($total*$serviceTax)/100),2,'.','');		
					$grandTotal = $total+$serviceTaxRate;
					$tActualCost	= ($tir[31]!="" && $tir[31]!=0)?$tir[31]:$grandTotal ;
				
					$billNo	    	= $tir[23];
					$settldStatus	= $tir[24];
		
					$settledDate 	= $tir[25];
					
					if ($settldStatus=='Y') {
						//$checked	=	"Checked";
						$grandTotalSettledAmount += $tActualCost;
					} else {
						//$checked	=	"";
						$grandTotalDuesAmount	+= $tActualCost;
					}
					$disabled = "";
					$edited	  = "";
					if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
						$disabled = "readonly";
						$edited	  = 1;
					}
					$grandTotalTransporterAmt += $tActualCost;
				} // Rate per kg ends here
			} // Docket No ends here
		}
	}

	if (sizeof($getAllTransInvoiceRecords)>0 && $billType=='OC') {

		$grandTotalTransporterAmt = 0;
		$grandTotalSettledAmount  = 0;
		$grandTotalDuesAmount	= 0;
		foreach ($getAllTransInvoiceRecords as $tir) {
			$salesOrderId		= $tir[0];
			$salesOrderNo		= $tir[1];
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.','');	// Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];

			if ($docketNum!="") {
				# Get Other Charges
				# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %
				list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);	
	
				$settledDate 	= $tir[25];
				$octroiExempted = $tir[41];
				$octroiPercent = ($octroiExempted!='Y')?$cityMasterObj->getOctroiPercent($cityId):0;
	
				$octroiValue    = number_format((($invoiceValue*$octroiPercent)/100),2,'.','');
				$serviceTaxRate = number_format((($octroiValue*$octroiServiceCharge)/100),2,'.','');	
				$grandTotal	= $octroiValue+$serviceTaxRate;
	
				$billNo	    	= $tir[38];
				$settldStatus	= $tir[39];
				$settledDate 	= $tir[40];
				$tActualCost	= ($tir[37]!="" && $tir[37]!=0)?$tir[37]:$grandTotal ;
				if ($settldStatus=='Y') {
					//$checked	=	"Checked";
					$grandTotalSettledAmount += $tActualCost;
				} else {
					//$checked	=	"";
					$grandTotalDuesAmount	+= $tActualCost;
				}
				$disabled = "";
				$edited	  = "";
				if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
					$rDisabled = "readonly";
					$rEdited	  = 1;
				}
				$grandTotalTransporterAmt += $tActualCost;
			} // Docket No ends here
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
   <td colspan="17" align="center" class="listing-head" >STATEMENT OF <?=$transporterName?><!--TRANSPORTER--> A/C SETTLEMENT </td>
  </tr><? if ($dateFrom) {?>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">For the Period from <?=$dateFrom?> Till <?=$dateTill?> </td>
  </tr><? }?>
  <tr bgcolor=white>
    <td colspan="17" align="center">&nbsp;</td>
  </tr>
	<?php
	     if (sizeof($transporterInvoiceRecords)>0 && $billType=='OD') {
		  $i = 0;
		$odRowHStyle = "padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;";
		//padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;
		//
	?>
	<tr bgcolor=white>
    <td colspan="17" align="center">
	<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">	
              	<tr bgcolor="#f2f2f2" align="center"> 
                	<th nowrap="nowrap" class="listing-head" style="<?=$odRowHStyle?>">Date</th>
               		<th align="center" class="listing-head" style="<?=$odRowHStyle?>">Invoice No</th>
                	<th class="listing-head" style="<?=$odRowHStyle?>">Inv Value</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Distributor</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">City</th>
			<?php
				if (!$selTransporter) {
			?>
			<th class="listing-head" style="<?=$odRowHStyle?>">Transporter</th>
			<? }?>
			<th class="listing-head" style="<?=$odRowHStyle?>">Docket No.</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">No of Boxes</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Gross Wt</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Adj</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Total Wt</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Rate/Kg</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Freight Cost</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">FOV</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Docu Charges</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">ODA Charges</th>	
			<th class="listing-head" style="<?=$odRowHStyle?>">Surcharge</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Total</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Serv Tax</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Grand Total</th>			
			<th class="listing-head" style="<?=$odRowHStyle?>">Actual Cost</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Bill No</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Setld</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Setl<br/> Date</th>
              </tr>
              <?php
		$totalTransporterAmt = 0;
		$totalActualCost = 0;
		$actualCost = 0;
		foreach($transporterInvoiceRecords as $tir){
			$i++;
			$salesOrderId		= $tir[0];
						
			$soNo 	= $tir[1];
			$invType = $tir[32];			
			$pfNo 	= $tir[33];
			$saNo	= $tir[34];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";
			
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.','');	// Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];
			
			// Trans Id, Function Type, Despatch ate
			if ($transOCRateListId=="") {
				//$transOCRateListId = $transporterRateListObj->latestRateList($transporterId, "TOC");
				//$getId = $transporterRateListObj->getTransporterValidRateListId($transporterId, "TOC", $tir[4]);
			}
			$actualCost = 0;
			if ($docketNum!="") {
				list($groupedTotalWt, $numGroup) = $transporterAccountObj->getTrptrRecs($offset, $limit, $transporterId, $fromDate, $tillDate,  $billType, $distributorId, $cityId, $docketNum);

				$selGrossWt = ($numGroup>1)?$groupedTotalWt:$totalWt;
				//echo "$selGrossWt====$docketNum<br>";

				# Find the Transporter rate Per Kg
				list($ratePerKg, $transporterRateEntryId, $rateType) = $transporterAccountObj->getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $selGrossWt);

				//$ratePerKg = "";
				$freightCost = "";
				$FOV	     = "";	
				$total	     = "";
				$serviceTaxRate = "";
				$grandTotal	= "";
				$docketCharge	= "";	
				$surchargeAmt = "";			
				if ($ratePerKg!="" && $ratePerKg!=0) {
					$freightCost	= $totalWt*$ratePerKg;	
					$freightCost 	= ($rateType!="FRC")?$freightCost:$ratePerKg;
					# Get Other Charges
					# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %
					list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surchargePercent) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);
					$docketCharge 	= number_format(($docketCharge/$numGroup),2,'.','');
					$odaCharge 	= number_format(($odaCharge/$numGroup),2,'.','');
					

					$odaApplicable = $tir[45];
					$selOdaRate    = $tir[46];
					$odaRate       = ($odaApplicable=='Y')?(($selOdaRate!=0)?$selOdaRate:$odaCharge):"";
					$FOV	= number_format((($invoiceValue*$fovCharge)/100),2,'.','');
					$total = $freightCost+$FOV+$docketCharge+$odaRate;
					# Surcharge calc
					$calcSurchargeAmt = $freightCost+($FOV+$docketCharge+$odaRate);
					$surchargeAmt = number_format((($calcSurchargeAmt*$surchargePercent)/100),2,'.','');
					$total += $surchargeAmt;
				
					$serviceTaxRate = number_format((($total*$serviceTax)/100),2,'.','');
					$grandTotal = $total+$serviceTaxRate;
					$totalTransporterAmt += $grandTotal;
					$actualCost	= ($tir[31]!="" && $tir[31]!=0)?$tir[31]:$grandTotal ;
					$totalActualCost +=	$actualCost;	
				}
			} // Docket No check
			$billNo	    	= $tir[23];
			$settldStatus	= $tir[24];
			$settledDate 	= $tir[25];
			
			if ($settldStatus=='Y') {
				$checked	=	"Checked";				
			} else {
				$checked	=	"";
			}			
			$disabled = "";
			$edited	  = "";
			if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
				$disabled = "readonly";
				$edited	  = 1;
			}
			

			$selTransporterName = $tir[29];
			$billRequired    = $tir[30];
			
			$txtStyleDisplay = "";
			$readOnly = "";
			if ($billRequired=='N') {
				$txtReadOnly = " readonly='true' ";
				$txtStyleDisplay = " style='border:none;'";	
			}	

			$rowColor = "";
			$disErrMsg = "";
			if (($ratePerKg=="" || $ratePerKg==0) || $docketNum=="" )  {
				$rowColor = "#FFFFCC";
				//$disErrMsg = "onMouseover=\"ShowTip('Please define Transporter rate per Kg (Zone Wise).');\" onMouseout=\"UnTip();\"";
			} else $rowColor = "#FFFFFF";
			
			$reSetldODDate = $tir[42];
			if ($reSetldODDate!="") $rowColor = "#FFFFCC";

			$deliveryDate	= ($tir[44]!='0000-00-00')?dateFormat($tir[44]):"";
			if ($docketNum=="" || ($ratePerKg=="" || $ratePerKg==0)) $disabled = "readonly";
			//echo "<b>$actualCost";
		?>
               <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$despatchDate?>
			<input type="hidden" name="salesOrderId_<?=$i?>" value="<?=$salesOrderId?>">
			<input type="hidden" name="billRequired_<?=$i?>" id="billRequired_<?=$i?>" value="<?=$billRequired?>">
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$salesOrderNo?>
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$invoiceValue?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$cityName?>
		</td>
		<?php
				if (!$selTransporter) {
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;">
			<?=$selTransporterName?>
		</td>
		<? }?>
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$docketNum:"<span class='err1'><b>NA</b></span>";?>
			<input type="hidden" name="docketNum_<?=$i;?>" id="docketNum_<?=$i;?>" value="<?=$docketNum?>">
			<input type="hidden" name="deliveryDate_<?=$i;?>" id="deliveryDate_<?=$i;?>" value="<?=$deliveryDate?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$numBox:"&nbsp;"?><?//$numBox?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$grossWt:"&nbsp;"?><?//$grossWt?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?number_format($adjWt,2,'.',''):"&nbsp;"?>
			<?//number_format($adjWt,2,'.','')?>
			<input type="hidden" name="adjustWt_<?=$i;?>" value="<?=$adjWt?>">	
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$totalWt?>
			<?=($docketNum!="")?$totalWt:"&nbsp;"?>
			<input type="hidden" name="totalWt_<?=$i;?>" value="<?=$totalWt?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$ratePerKg?>	
			<?=($docketNum!="")?(($ratePerKg!=0)?$ratePerKg:"<span class='err1'><b>NA</b></span>"):"&nbsp;"?>
			<input type="hidden" name="ratePerKg_<?=$i;?>" value="<?=$ratePerKg?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//number_format($freightCost,2,'.','')?>
			<?=($docketNum!="" && $freightCost!=0)?number_format($freightCost,2,'.',''):"&nbsp;"?>
			<input type="hidden" name="freightCost_<?=$i;?>" id="freightCost_<?=$i;?>" value="<?=$freightCost?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$FOV?>
			<?=($docketNum!="")?$FOV:"&nbsp;"?>	
			<input type="hidden" name="fovRate_<?=$i;?>" id="fovRate_<?=$i;?>" value="<?=$FOV?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">			
			<?=($docketNum!="")?$docketCharge:"&nbsp;"?>
			<input type="hidden" name="docketRate_<?=$i;?>" id="docketRate_<?=$i;?>" value="<?=$docketCharge?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$odaRate:""?>
			<input type="hidden" name="odaRate_<?=$i?>" id="odaRate_<?=$i?>" value="<?=$odaRate?>" size="5" style="text-align:right;" autocomplete="off">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="" && $surchargeAmt!=0)?$surchargeAmt:"&nbsp;"?>
			<input type="hidden" name="surcharge_<?=$i?>" id="surcharge_<?=$i?>" value="<?=$surchargeAmt?>" size="5" style="text-align:right;" autocomplete="off" readonly>
		</td>	
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" id="trptrTotalAmtCol_<?=$i;?>">
			<?//number_format($total,2,'.','')?>
			<?=($docketNum!="" && $total!=0)?number_format($total,2,'.',''):"&nbsp;"?>
			<input type="hidden" name="transTotalAmt_<?=$i;?>" id="transTotalAmt_<?=$i;?>" value="<?=$total?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$serviceTaxRate?>
			<?=($docketNum!="")?$serviceTaxRate:"&nbsp;"?>
			<input type="hidden" name="serviceTaxRate_<?=$i;?>" value="<?=$serviceTaxRate?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//number_format($grandTotal,2,'.','')?>
			<?=($docketNum!="")?$grandTotal:"&nbsp;"?>
			<input type="hidden" name="transGrandTotalAmt_<?=$i;?>" value="<?=$grandTotal?>">
		</td>		
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($actualCost!=0)?number_format($actualCost,2,'.',''):"";?>
		</td>		
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$billNo?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<? if($checked){?><img src="images/y.gif" /><? } else {?><img src="images/x.gif" /><?}?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;line-height:normal;">
			<?=($settledDate!="0000-00-00" && $settledDate!="")?dateFormat($settledDate):""?>
			<?php if ($reSetldODDate!="") {?>
				<br/>
				<span class="listing-item" style="line-height:normal;font-size:9px;color:maroon">Resetld On:<?=dateFormat($reSetldODDate);?></span>
			<? }?>
		</td>	
              </tr>
		<?php
			}
		?>
		<?php
			if (!$selTransporter) $totRowColSpan = 19;
			else $totRowColSpan = 18;
		?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;" colspan="<?=$totRowColSpan?>" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;" align="right"><strong><?=$totalTransporterAmt?></strong></td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;" align="right"><strong><?=$totalActualCost?></strong></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>	
		
	<tr bgcolor="#FFFFFF">
<td colspan="24" height="25">
  <table cellpadding="0" cellspacing="0">
  <tr>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Total Value:</td>
    <td class="listing-item" align="left">
	<strong><?=$grandTotalTransporterAmt?></strong>
   </td>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Settled:</td>
  <td class="listing-item">
	<strong><?=$grandTotalSettledAmount?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Pending: </td>
  <td class="listing-item">
	<strong><? echo number_format($grandTotalDuesAmount,2,'.','');?></strong>
   </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Paid:</td>
  <td class="listing-item">
	<strong><? echo number_format($grandTotalSettledAmount,2,'.','');?></strong>
 </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Due:</td>
  <td class="listing-item">
	<strong><? echo number_format($grandTotalDuesAmount,2,'.','');?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Payable</td>
  <td class="listing-item">
	<strong><? echo number_format($grandTotalDuesAmount,2,'.','');?></strong>
  </td>
  </tr>
  </table>
  </td></tr>

      </table></td>
  	</tr>	 
 <? }?>
	<?php
		if (sizeof($transporterInvoiceRecords)>0 && $billType=='OC') {
	?>
	<tr bgcolor=white>
    <td colspan="17" align="center">
	<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">	
              	<tr bgcolor="#f2f2f2" align="center"> 
                	<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Date</th>
               		<th align="center" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Invoice No</th>
                	<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Inv Value</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Distributor</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">City</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Docket No.</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">No of Boxes</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Gross Wt</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Adj</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Total Wt</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Octroi %</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Octroi Value</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Serv Tax</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Grand Total</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Actual Cost</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Bill No</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Setld</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;">Setl<br/> Date</th>
              </tr>
              <?php
		$totalTransporterAmt = 0;
		foreach($transporterInvoiceRecords as $tir){
			$i++;
			$salesOrderId		= $tir[0];
			$soNo 	= $tir[1];
			$invType = $tir[32];			
			$pfNo 	= $tir[33];
			$saNo	= $tir[34];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.',''); // Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];
			
			# Find the Transporter rate Per Kg
			//list($ratePerKg, $transporterRateEntryId, $rateType)		= $transporterAccountObj->getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $totalWt);
			$settldStatus = "";
			$settledDate = "";
			$billNo = "";
			$octroiExempted = "";
			$octroiPercent = "";
			$octroiValue = "";
			$serviceTaxRate = "";
			$grandTotal = "";
			$actualCost = "";
			if ($docketNum!="") {
				# Get Other Charges
				# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %
				list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surchargePercent) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);
	
				$billNo	    	= $tir[38];
				$settldStatus	= $tir[39];
				$settledDate 	= $tir[40];
				$octroiExempted = $tir[41];
				$octroiPercent = ($octroiExempted!='Y')?$cityMasterObj->getOctroiPercent($cityId):0;
	
				$octroiValue    = number_format((($invoiceValue*$octroiPercent)/100),2,'.','');
				$serviceTaxRate = number_format((($octroiValue*$octroiServiceCharge)/100),2,'.','');
				
				$grandTotal	= $octroiValue+$serviceTaxRate;
				$totalTransporterAmt += $grandTotal;
				$actualCost	= ($tir[37]!="" && $tir[37]!=0)?$tir[37]:$grandTotal ;
				$totalActualCost += $actualCost;
			} // Docket No checking ends here
			
			if ($settldStatus=='Y') $checked = "Checked";
			else $checked	=	"";
			
			$disabled = "";
			$edited	  = "";
			if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
				$disabled = "readonly";
				$edited	  = 1;
			}
			
			$rowColor = "";
			$disErrMsg = "";
			if (($octroiPercent=="" || $octroiPercent==0) && $octroiExempted!='Y')  {
				$rowColor = "#FFFFCC";
				$disErrMsg = "onMouseover=\"ShowTip('Please define a Octroi Percent.');\" onMouseout=\"UnTip();\"";
			} else $rowColor = "#FFFFFF";
			//bgcolor="#FFFFFF"
			$selTransporterName = $tir[29];			
			$billRequired    = $tir[30];
			
			$txtStyleDisplay = "";
			$readOnly = "";
			if ($billRequired=='N') {
				$txtReadOnly = " readonly='true' ";
				$txtStyleDisplay = " style='border:none;'";	
			}

			$reSetldOCDate = $tir[43];
			if ($reSetldOCDate!="") $rowColor = "#FFFFCC";
			$deliveryDate	= ($tir[44]!='0000-00-00')?dateFormat($tir[44]):"";

			if ($docketNum=="") {
				$txtReadOnly = " readonly='true' ";
				$disabled = "readonly";
			}			
		?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$despatchDate?>
			<input type="hidden" name="salesOrderId_<?=$i?>" value="<?=$salesOrderId?>">
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$salesOrderNo?>
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$invoiceValue?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$cityName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!=0)?$docketNum:"<span class='err1'><b>NA</b></span>";?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$numBox?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$grossWt?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=number_format($adjWt,2,'.','')?>
			<input type="hidden" name="adjustWt_<?=$i;?>" value="<?=$adjWt?>">	
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$totalWt?>			
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$octroiPercent?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=number_format($octroiValue,2,'.','');?>
			<input type="hidden" name="octroiRate_<?=$i;?>" value="<?=$octroiValue?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=number_format($serviceTaxRate,2,'.','');?>
			<input type="hidden" name="serviceTaxRate_<?=$i;?>" value="<?=$serviceTaxRate?>">
		</td>		
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$grandTotal?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$actualCost?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$billNo?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<? if($checked){?><img src="images/y.gif" /><? } else {?><img src="images/x.gif" /><?}?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($settledDate!="0000-00-00" && $settledDate!="")?dateFormat($settledDate):""?>
		</td>
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;" colspan="13" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;" align="right"><strong><?=number_format($totalTransporterAmt,2,'.','')?></strong></td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:9px;line-height:normal;" align="right"><strong><?=number_format($totalActualCost,2,'.','')?></strong></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>	
		
	<tr bgcolor="#FFFFFF">
<td colspan="24" height="25">
  <table cellpadding="0" cellspacing="0">
  <tr>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Total Value:</td>
    <td class="listing-item" align="left">
	<strong><?=number_format($grandTotalTransporterAmt,2)?></strong>
   </td>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Settled:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalSettledAmount,2)?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Pending: </td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2);?></strong>
   </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Paid:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalSettledAmount,2);?></strong>
 </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Due:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2);?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Payable</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2);?></strong>
  </td>
  </tr>
  </table>
  </td></tr>

      </table></td>
  	</tr>
	<?php
		}
	?>
  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >	  
	  <? //} else { ?>
	 <!-- <tr bgcolor="white"> 
      <td colspan="17"  class="err1" height="10" align="center">
        <?=$msgNoRecords;?>      </td>
    </tr>-->
	<? 
  //}  
  ?> 

</table>
</body>
</html>
