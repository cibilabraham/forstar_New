<?php	
	require("include/include.php");
		
	$dateFrom 	= $g["dateFrom"];
	$dateTo 	= $g["dateTo"];	
	$selFishId 	= $g["selFish"];
	
	if ($dateFrom && $dateTo) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTo);
	
		$processCodeRecs = $productionAnalysisReportObj->processCodeRecs($fromDate, $tillDate, $selFishId);
		//print_r($processCodeRecs);
	}

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Production Analysis Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";
	}
	function printThisPage(printbtn)
	{
		document.getElementById("printButton").style.display="none";
		window.print();
		setTimeout("displayBtn()",3000);		
	}
</script>
<style type="text/css" media="print">
@page {
  size: A4 landscape;
}
</style>
</head>
<body >
<div>
<form name="frmPrintProductionReport">
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	# Sales Order Report
	if (sizeof($processCodeRecs)) {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" height="5" ></td>
  </tr>
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
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='center' colspan='2'>PRODUCTION ANALYSIS REPORT<br/>
		<span style="font-size:11px;">
		 FROM <?=$dateFrom?> TO <?=$dateTo?> 
		</span>
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
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
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" style="padding-left:5px;padding-right:5px;">
<table width="95%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?php
	if ($processCodeRecs>0) {
		$rowHeadStyle = "padding-left:2px; padding-right:2px;font-size:10px;line-height:normal;";
	?>
 	 <tr bgcolor="#f2f2f2" align="center">
                                 <td class="listing-head" style="<?=$rowHeadStyle?>">Item</td>
                                 <td class="listing-head" nowrap="nowrap" style="<?=$rowHeadStyle?>">OB</td>
                                 <td class="listing-head" style="<?=$rowHeadStyle?>">ARR</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">PPM</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">RPM</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$netBgColor?>">Net</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$prodBgColor?>">Prodn</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">FP</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$peeledBgColor?>">Peeled</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Bal</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Prodn<br>CS</td>
				<td class="listing-head" style="<?=$rowHeadStyle?>">PP<br>CS</td>
				<td class="listing-head" style="<?=$rowHeadStyle?>">RPM<br>CS</td>
				<td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$totCSBgColor?>">Tot<br>CS</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">D-PPM</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Net</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">[+] Excess / [-] Shortage<!--HO--></td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">YIELD</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">DIFF</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Pkg-Yld</td>
                        </tr>	
      <?php
		$numRows	= 30; // Setting No.of rows 14
		$j = 0;
		$selPCRecSize = sizeof($processCodeRecs);
		$totalPage = ceil($selPCRecSize/$numRows);
		
				$totalRMOBQty = 0;
				$totalRMArrivalQty = 0;
				$totalPreProcessedQty = 0;
				$totalRePreProcessedQty = 0;
				$totalNetQty = 0;
				$totalProdPackingQty = 0;
				$totalFPRMQty = 0;
				$totalPeeledQty = 0;
				$totalBalanceRMQty = 0;
				$totalProdCBQty = 0;
				$totalPPMCBQty = 0;
				$totalClosingBalanceQty = 0;
				$totalDiffPreProcessQty = 0;
				$totalNetQtyAfterCS = 0;
				$prevFishId = "";
				$prevSelFishId = "";
				$i = 0;	
				$excShrtQtyArr = array();
				# Find the Max Size of Process code fish wise
				$pcMaxSize = $productionAnalysisReportObj->getMaxSizeOfPCRecs();
				$pcCol = 0;
				$pcTotCol = 0;
				$totalRPMCBQty = 0;
				$totalRPMCBQty = 0;
				foreach ($processCodeRecs as $pcr) {
					$i++;
					$processCodeId	= $pcr[0];
					$fishId		= $pcr[1];
					$processCode	= $pcr[2];
					//$fishName	= $pcr[10];
					$fishName	= $pcr[3];
					if ($i==1) $prevSelFishId = $fishId;
					# get RM Opening Balance
					list($rmOBQty,$displayOBDtls) = $productionAnalysisReportObj->getRMOpeningBalance($processCodeId, $fromDate);
					$totalRMOBQty += $rmOBQty;

					# get RM Arival Qty
					list($rmArrivalQty, $displayArrival) = $productionAnalysisReportObj->getRMArrivalQty($processCodeId, $fromDate, $tillDate);
					$totalRMArrivalQty += $rmArrivalQty;
					
					# Get Pre-Processed Qty
					list($preProcessedQty, $displayPPMCalc) = $productionAnalysisReportObj->getRMPreProcessedQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId);
					$totalPreProcessedQty += $preProcessedQty;
										
					# ReProcessing Qty (RPM)
					$rePreProcessedQty = $productionAnalysisReportObj->getRMThawedQty($processCodeId, $fromDate, $tillDate, $fishId);
					$totalRePreProcessedQty += $rePreProcessedQty;

					# Find Net Qty
					$netQty = $rmOBQty+$rmArrivalQty+$preProcessedQty+$rePreProcessedQty;
					$totalNetQty += $netQty;

					# Packing Qty (Production)
					list($prodPackingQty, $displayProdnCalc) = $productionAnalysisReportObj->getRMPackingQty($processCodeId, $fromDate, $tillDate, $fishId);
					$totalProdPackingQty += $prodPackingQty;
					$showProdnCalc = "";
					if ($prodPackingQty!="") {
						$showProdnCalc = "onMouseover=\"ShowTip('$displayProdnCalc');\" onMouseout=\"UnTip();\" ";

					}

					# For Peeling
					$fpRMQty = $netQty-$prodPackingQty;
					$totalFPRMQty += $fpRMQty;
	
					# Get Pre-Processed From Qty
					$peeledQty = $productionAnalysisReportObj->getPeeledQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId);
					$totalPeeledQty += $peeledQty;
				
					$balanceRMQty = $fpRMQty-$peeledQty;
					$totalBalanceRMQty += $balanceRMQty;

					//Closing Balance
					$prodCBQty
					 =$productionAnalysisReportObj->getRMClosingBalance($processCodeId, $fromDate);
					$totalProdCBQty += $prodCBQty;
					$ppmCBQty =$productionAnalysisReportObj->getPPRMClosingBalance($processCodeId, $fromDate);
					$totalPPMCBQty  += $ppmCBQty;
					# Re-Process CB
					$rpmCBQty = $productionAnalysisReportObj->getRPRMClosingBalance($processCodeId, $fromDate);
					$totalRPMCBQty  += $rpmCBQty;

					$closingBalanceQty = $prodCBQty+$ppmCBQty+$rpmCBQty;
					$totalClosingBalanceQty += $closingBalanceQty;	
					
					
					# Get Pre-Processed To Qty
					$diffPreProcessQty = $productionAnalysisReportObj->getDiffPreProcessQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId);
					$totalDiffPreProcessQty += $diffPreProcessQty;

					# Net Qty After CS
					$netQtyAfterCS = $closingBalanceQty-$balanceRMQty+$diffPreProcessQty;	
					$totalNetQtyAfterCS += $netQtyAfterCS;
					
					# Excess/ Shotage section

					// Like settlement Sumary
					// Dsisplay Sub Total
					if ($prevSelFishId!=$fishId ) {	
						# Get Process Code recs
						$prevPCRecs = $productionAnalysisReportObj->getSelProcessCodeRecs($prevSelFishId);
	
						if ($pcTotCol % 2 ==0 ) $pcTHBgColor = "#D9F3FF"; // blue
						else if ($pcTotCol % 3 ==0 ) $pcTHBgColor = "#f2f2f2"; 
						else $pcTHBgColor = "#e3f0f7"; // Sky blue

						$disTotalHead = '<tr bgcolor="white"><td class="listing-head" colspan="16" style="padding-left:10px; padding-right:10px; line-height:normal;" nowrap align="right"><b>Total</b></td>
						<td nowrap width="'.$tdWidth.'">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tdBoarder">
						<tr bgcolor="'.$pcTHBgColor.'" align="center">
						';
						$c = 0;
						//foreach ($prevPCRecs as $pcr) {
						for ($cnt=0; $cnt<$pcMaxSize; $cnt++) {
							$pcr = $prevPCRecs[$cnt];
							$c++;							
							$disStyle = "";
							$totSPCId	= $pcr[0];
							$totDisplay	= number_format(($excShrtQtyArr[$totSPCId]),2,'.','');
							/*
							if (sizeof($pcRecs)!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;";
							else $disStyle="padding-left:3px; padding-right:3px;";
							*/
							if ($pcMaxSize!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px; line-height:normal; font-size:10px;";
							else $disStyle="padding-left:3px; padding-right:3px; line-height:normal; font-size:10px;";
							
							$dTot = ($totDisplay!=0)?$totDisplay:"&nbsp;";
							$disTotalHead .= '<td class="listing-item" align="right" style="'.$disStyle.'" width="'.$sTdWidth.'%" nowrap><b>'.$dTot.'</b></td>
							';	
						}
						$disTotalHead .= '
							</tr>
						</table>
						</td>
						<td colspan="3"></td>
						</tr>';
						echo $disTotalHead;

						$pcTotCol++;
					}				
				
					// Fish Head Display $pcRecs
					if ($prevFishId!=$fishId) {
						# Get Process Code recs
						$pcRecs = $productionAnalysisReportObj->getSelProcessCodeRecs($fishId);
						
						$sTdWidth = ceil(((80)/$pcMaxSize));
						//$sTdWidth = ceil(((100)/sizeof($pcRecs))); Original
						$tdWidth = $pcMaxSize*80;
						
						if ($pcCol % 2 ==0 ) $pcHBgColor = "#D9F3FF";
						else if ($pcCol % 3 ==0 ) $pcHBgColor = "#f2f2f2";
						else $pcHBgColor = "#e3f0f7";
						
						$disMHead = '<tr bgcolor="white"><td class="fieldname" colspan="16" style="padding-left:10px; padding-right:10px; line-height:normal;" nowrap><b>'.$fishName.'</b></td>
						<td nowrap width="'.$tdWidth.'">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tdBoarder">
						<tr bgcolor="'.$pcHBgColor.'" align="center">';
						$c = 0;
						//foreach ($pcRecs as $pcr) {
						$dPCHead = "";
						for ($cnt=0; $cnt<$pcMaxSize; $cnt++) {			
							$pcr = $pcRecs[$cnt];
							$c++;
							$disPC	 = $pcr[2];
							$disStyle = "";
							/*
							if (sizeof($pcRecs)!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;";
							else $disStyle="padding-left:3px; padding-right:3px;";
							*/
							//echo "<br>$pcMaxSize!=$c";
							$dPCHead = ($disPC)?$disPC:'&nbsp;';
							if ($pcMaxSize!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;font-size:10px;";
							else $disStyle="padding-left:3px; padding-right:3px;font-size:10px;";
	
							$disMHead .= '<td class="listing-head" align="center" style="'.$disStyle.'" width="'.$sTdWidth.'%" nowrap>'.$dPCHead.'</td>
							';	
						}
						$disMHead .= '</tr></table></td><td colspan="3"></td></tr>';
						echo $disMHead;
						$pcCol++;
						# Get PC Position (Level)
						$pcPositionArr = $productionAnalysisReportObj->getPCPostion($fishId, $processCodeId);
					}
			?>
        <tr> 
                         <td class="listing-item" nowrap="nowrap" style="<?=$rowHeadStyle?>"><?=$processCode?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=$rmOBQty?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=$rmArrivalQty?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=$preProcessedQty?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=$rePreProcessedQty?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>" bgcolor="<?=$netBgColor?>"><?=($netQty!=0)?number_format($netQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>" bgcolor="<?=$prodBgColor?>"><?=($prodPackingQty!=0)?$prodPackingQty:""?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=($fpRMQty!=0)?number_format($fpRMQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>" bgcolor="<?=$peeledBgColor?>"><?=($peeledQty!=0)?number_format($peeledQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=($balanceRMQty!=0)?number_format($balanceRMQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=($prodCBQty!=0)?number_format($prodCBQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=($ppmCBQty!=0)?number_format($ppmCBQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=($rpmCBQty!=0)?number_format($rpmCBQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>" bgcolor="<?=$totCSBgColor?>"><?=($closingBalanceQty!=0)?number_format($closingBalanceQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=($diffPreProcessQty!=0)?number_format($diffPreProcessQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=($netQtyAfterCS!=0)?number_format($netQtyAfterCS,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" width="<?=$tdWidth;?>">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tdBoarder">
						<tr bgcolor="white" align="center">
						<?php
						$cl = 0;
						$p = 0;
						$processArr = array();						
						//foreach ($pcRecs as $pcr) {				
						for ($cnt=0; $cnt<$pcMaxSize; $cnt++) {
							$pcr = $pcRecs[$cnt];
							$cl++;
							$disStyle = "";
							if ($pcMaxSize!=$cl) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;font-size:10px;";
							else $disStyle="padding-left:3px; padding-right:3px;font-size:10px;";
							/*
							if (sizeof($pcRecs)!=$cl) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;";
							else $disStyle="padding-left:3px; padding-right:3px;";
							*/
							$sPCId	= $pcr[0];
							$sFId	= $pcr[1];
							$joinProcessCode = $processCodeId.",".$sPCId;	
							$rowPosition = $pcPositionArr[$processCodeId];
							$colPosition = $pcPositionArr[$sPCId];

							# Excess/Short Qty					
							list($excessShortQty, $displayExShrtCalc) = $productionAnalysisReportObj->getExShortQty($processCodeId, $sPCId, $netQtyAfterCS, $joinProcessCode, $rowPosition, $colPosition);	
							$showExShrtCalc = "";
							if ($displayExShrtCalc!="") {
								$showExShrtCalc = "onMouseover=\"ShowTip('$displayExShrtCalc');\" onMouseout=\"UnTip();\" ";		
							}
							$excShrtQtyArr[$sPCId] += $excessShortQty;
							$samePCQtyBgColor = "";
							if ($processCodeId==$sPCId) $samePCQtyBgColor = "bgcolor='#AA9988'";	
							# Display Calc
							$disESQty = ($processCodeId!=$sPCId)?"<a href='###' class='link5' $showExShrtCalc>".number_format($excessShortQty,2,'.','')."</a>":number_format($excessShortQty,2,'.','');	
						?>
						<td class="listing-item" align="right" style="<?=$disStyle?>" width="<?=$sTdWidth?>%" nowrap <?=$samePCQtyBgColor?>>
							<?=($excessShortQty!=0)?number_format($excessShortQty,2,'.',''):"&nbsp;";?>
							
						</td>
						<?php	
							$p++;						
							} // Sub loop
						?>						
							</tr>
						</table>
			</td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=$preProcessQty?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=$preProcessQty?></td>
			<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><?=$preProcessQty?></td>
	</tr>
	  	<?
		if ($i%$numRows==0 && $selPCRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
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
			<td height="10">&nbsp;</td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="2"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			<?=strtoupper($displayHead)?> REPORT - Cont.</td>
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
	  <tr bgcolor="White">
<td colspan="17" align="center" style="padding-left:5px;padding-right:5px;">
	<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<tr bgcolor="#f2f2f2" align="center">
                                 <td class="listing-head" style="<?=$rowHeadStyle?>">Item</td>
                                 <td class="listing-head" nowrap="nowrap" style="<?=$rowHeadStyle?>">OB</td>
                                 <td class="listing-head" style="<?=$rowHeadStyle?>">ARR</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">PPM</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">RPM</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$netBgColor?>">Net</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$prodBgColor?>">Prodn</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">FP</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$peeledBgColor?>">Peeled</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Bal</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Prodn<br>CS</td>
				<td class="listing-head" style="<?=$rowHeadStyle?>">PP<br>CS</td>
				<td class="listing-head" style="<?=$rowHeadStyle?>" bgcolor="<?=$totCSBgColor?>">Tot<br>CS</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">D-PPM</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Net</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">[+] Excess / [-] Shortage<!--HO--></td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">YIELD</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">DIFF</td>
				 <td class="listing-head" style="<?=$rowHeadStyle?>">Pkg-Yld</td>
                        </tr>
   <?php
	#Main Loop ending section 
			
	       }
?>
	<?php	
			# Loop Last Print Sub Total
			if (sizeof($processCodeRecs)==$i && sizeof($excShrtQtyArr)>1) {
				
					if ($pcTotCol % 2 ==0 ) $pcTHBgColor = "#D9F3FF";
						else if ($pcTotCol % 3 ==0 ) $pcTHBgColor = "#f2f2f2";
						else $pcTHBgColor = "#e3f0f7";
			?>
		<tr bgcolor="white">
			<td class="listing-head" colspan="16" style="padding-left:10px; padding-right:10px; line-height:normal;" nowrap align="right">
				<b>Total</b>
			</td>
			<td nowrap width="<?=$tdWidth?>">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tdBoarder">
				<tr bgcolor="<?=$pcTHBgColor?>" align="center">
				<?php
					$c = 0;
					//foreach ($pcRecs as $pcr) {
					for ($cnt=0; $cnt<$pcMaxSize; $cnt++) {
						$pcr = $pcRecs[$cnt]; 
						$c++;
						$disStyle = "";
						$totSPCId	= $pcr[0];
						$totDisplay	= number_format(($excShrtQtyArr[$totSPCId]),2,'.','');
		
						if ($pcMaxSize!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;font-size:10px;";
						else $disStyle="padding-left:3px; padding-right:3px;font-size:10px;";
						/*
						if (sizeof($pcRecs)!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;";
						else $disStyle="padding-left:3px; padding-right:3px;";
						*/
				?>
			<td class="listing-item" align="right" style="<?=$disStyle?>" width="<?=$sTdWidth?>%" nowrap><b><?=($totDisplay!=0)?$totDisplay:"&nbsp;"?></b></td>
				<?php
				    }
				?>
			</tr>
			</table>
			</td>
			<td colspan="3"></td>
			</tr>
		<?php
			} // Sub Total
		?>
	<?
		$prevFishId = $fishId;
		$prevSelFishId = $fishId;		
	} // Main Loop Ends here
   ?>
	<tr bgcolor="white">
		  <td class="listing-head" align="right" >Total:</td>
		  <td class="listing-item" align="right" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalRMOBQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalRMArrivalQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalPreProcessedQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalRePreProcessedQty,2,'.','');?></strong></td>	
		 <td class="listing-item" align="right" style="<?=$rowHeadStyle?>" bgcolor="<?=$netBgColor?>"><strong><?=number_format($totalNetQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>" bgcolor="<?=$prodBgColor?>"><strong><?=number_format($totalProdPackingQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalFPRMQty,2,'.','');?></strong></td>			  
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>" bgcolor="<?=$peeledBgColor?>"><strong><?=number_format($totalPeeledQty,2,'.','');?></strong></td>	
		 <td class="listing-item" align="right" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalBalanceRMQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalProdCBQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalPPMCBQty,2,'.','');?></strong></td>
		<td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalRPMCBQty,2,'.','');?></strong></td>	  
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>" bgcolor="<?=$totCSBgColor?>"><strong><?=number_format($totalClosingBalanceQty,2,'.','');?></strong></td>	
		 <td class="listing-item" align="right" style="<?=$rowHeadStyle?>"><strong><?=number_format($totalDiffPreProcessQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>" nowrap="true"><strong><?=number_format($totalNetQtyAfterCS,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><?//echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>	
		<td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><? echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>
		<td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><? echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>
		<td align="right" class="listing-item" style="<?=$rowHeadStyle?>"><strong><? echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>
	 </tr>
    </table>
   </td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="99%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="<?=$rowHeadStyle?>" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?
	}
	# Sales Order Report Ends Here
?>
</form>	
</div>
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
