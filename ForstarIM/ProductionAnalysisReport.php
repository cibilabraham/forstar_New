<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";	
	$searchMode	= false;
	$searchEnabled = false;
	
	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	/**
	* Create Process Seqence
	*/	
	/*
	$processTbleUptdT = $productionAnalysisReportObj->showMProcessTableStatus();
	$processSequenceTbleUptdT = $productionAnalysisReportObj->showPPSequenceTableStatus();
	($processSequenceTbleUptdT<$processTbleUptdT)
	*/
	$processTblesUpdated = $productionAnalysisReportObj->chkProcessTbleUptd();
	$ppSequenceRecExist = $productionAnalysisReportObj->chkProcessSequenceRecs();
	if ($processTblesUpdated || !$ppSequenceRecExist) {
		$productionAnalysisReportObj->getPreProcessMap();
	}
	

	if ($g["dateFrom"]!="" && $g["dateTo"]!="") {
		$dateFrom 	= $g["dateFrom"];
		$dateTo 	= $g["dateTo"];	
		$searchEnabled = true;
	} else {
		$dateFrom 	= $p["dateFrom"];
		$dateTo 	= $p["dateTo"];		
	}
	$selFishId 	= $p["selFish"];// Multiple selection	
	$selFish = "";
	if (sizeof($selFishId)>0) $selFish = implode(",",$selFishId);
	//echo $selFish;

	# Search starts Here
	if ($dateFrom && $dateTo) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTo);

		# Get Selected Fish
		$getFishRecords = $productionAnalysisReportObj->getFishRecords($fromDate, $tillDate);
		
		if ($p["cmdSearch"]!="" || $searchEnabled) {	
			$searchMode = true;
			# Get All Pre-Process Code recs
			if (sizeof($getFishRecords)>0) {
				$processCodeRecs = $productionAnalysisReportObj->processCodeRecs($fromDate, $tillDate, $selFish);
				
				# Get Max Count of Selected Process Code
				//$pcMaxCount = $productionAnalysisReportObj->getMaxTempProcessCode();
			}
		}
	}	

	# Pre-Process Rate List Id
	$preProcessRateListId = $processratelistObj->latestRateList();

	# Display heading	
	$heading = "Production Analysis Report";
	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ProductionAnalysisReport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProductionAnalysisReport" action="ProductionAnalysisReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
					<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="2" height="10" >&nbsp;</td>
                      </tr>
                      <tr> 
                        <td colspan="4" align="center"><? if($print==true){?><!--input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('PrintPreProcessingSummary.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>',700,600);" <? if (sizeof($preProcessingSummaryRecords)==0 && $details=="" &&	$summary=="" && $selProcessorId=="") echo "disabled";?> --><? }?></td>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap class="fieldName" ></td>
                        </tr>
                      <tr>
                        <td colspan="3" nowrap class="fieldName"  align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="3" nowrap align="center">
		<table width="200" border="0">
                          <tr>
                            <td valign="top"><table width="125" border="0">
                              <tr>
                                <td class="fieldName">From:</td>
                                <td nowrap>
              				<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>"  autocomplete="off" onchange="submitForm('dateFrom','dateTo', frmProductionAnalysisReport);" />
				</td>
				<td class="fieldName">To:</td>
                                <td nowrap>
                                    <input type="text" id="dateTo" name="dateTo" size="8"  value="<?=$dateTo?>" autocomplete="off" onchange="submitForm('dateFrom','dateTo', frmProductionAnalysisReport)" />
				</td>
				<td class="fieldName">Fish:</td>
                                <td nowrap>
					<select name="selFish[]" id="selFish" multiple="true" size="7">
						<option value="">--Select All--</option>
						<?php
						foreach ($getFishRecords as $sFishId=>$sFishName) {
							$selected = (in_array($sFishId,$selFishId))?"selected":"";	
						?>
						<option value="<?=$sFishId?>" <?=$selected?>><?=$sFishName?></option>
						<? }?>
					</select>
				</td>
				 <td class="listing-item">
					<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateProductionAnalysisReport(document.frmProductionAnalysisReport);">
				    </td>
                              </tr>
                            </table></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td colspan="3"  height="5"></td>
                      </tr>
	              <tr>
                        <td colspan="2" align="center">&nbsp;</td>
                        <td align="center" colspan="2">&nbsp;</td>
                      </tr>
<?php 
	if (sizeof($processCodeRecs)) {
		$netBgColor = "#CCCCFF";
		$prodBgColor = "#CCFFFF";
		$peeledBgColor = "#FFFFCC";
		$totCSBgColor = "#CCFFCC";
?>
	<? if ($print==true) {?>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">	
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintProductionAnalysisReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>&selFish=<?=$selFish?>',700,600);">
		</td>
		</tr>
		<tr><TD height="30"></TD></tr>
	<? }?>
        <tr>
             <td colspan="4" align="center" style="padding-left:10px; padding-right:10px;">
		<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999"> 	
                        <tr bgcolor="#f2f2f2" align="center">
                                 <td class="listing-head" style="padding-left:5px; padding-right:5px;">Item</td>
                                 <td class="listing-head" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">OB</td>
                                 <td class="listing-head" style="padding-left:5px; padding-right:5px;">ARR</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">PPM</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">RPM</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$netBgColor?>">Net</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$prodBgColor?>">Prodn</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">FP</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$peeledBgColor?>">Peeled</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">Bal</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">Prodn<br>CS</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">PP<br>CS</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">RPM<br>CS</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$totCSBgColor?>">Tot<br>CS</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">D-PPM</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">Net</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">[+] Excess / [-] Shortage<!--HO--></td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">YIELD</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">DIFF</td>
				 <td class="listing-head" style="padding-left:5px; padding-right:5px;">Pkg-Yld</td>
                        </tr>
                        <?php
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
				foreach ($processCodeRecs as $pcr) {
					$i++;
					$processCodeId	= $pcr[0];
					$fishId		= $pcr[1];
					$processCode	= $pcr[2];
					//echo "<br><u><strong>$processCode</strong></u>";
					//$fishName	= $pcr[10];
					$fishName	= $pcr[3];
					if ($i==1) $prevSelFishId = $fishId;
					# get RM Opening Balance
					list($rmOBQty, $displayOBDtls) = $productionAnalysisReportObj->getRMOpeningBalance($processCodeId, $fromDate);
					$totalRMOBQty += $rmOBQty;
					$showOBDtls = "";
					if ($rmOBQty!=0) {
						$showOBDtls = "onMouseover=\"ShowTip('$displayOBDtls');\" onMouseout=\"UnTip();\" ";
					}					

					# get RM Arival Qty
					list($rmArrivalQty, $displayArrival) = $productionAnalysisReportObj->getRMArrivalQty($processCodeId, $fromDate, $tillDate);
					$totalRMArrivalQty += $rmArrivalQty;
					$showArrivalDtls = "";
					if ($rmArrivalQty!=0) {
						$showArrivalDtls = "onMouseover=\"ShowTip('$displayArrival');\" onMouseout=\"UnTip();\" ";
					}
					
					# Get Pre-Processed Qty
					list($preProcessedQty, $displayPPMCalc) = $productionAnalysisReportObj->getRMPreProcessedQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId);
					$totalPreProcessedQty += $preProcessedQty;
					$showPPMCalc = "";
					if ($preProcessedQty!="") {
						$showPPMCalc = "onMouseover=\"ShowTip('$displayPPMCalc');\" onMouseout=\"UnTip();\" ";
					}
										
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
					# If Net Qty=0 and Production available then display diff color
					$prdnColor = "";
					if ($netQty==0 && $prodPackingQty!=0) {
						$prdnColor = "#FEE2E3";
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
					$prodCBQty = $productionAnalysisReportObj->getRMClosingBalance($processCodeId, $fromDate);
					$totalProdCBQty += $prodCBQty;
					# Pre-Process CB
					$ppmCBQty = $productionAnalysisReportObj->getPPRMClosingBalance($processCodeId, $fromDate);
					$totalPPMCBQty  += $ppmCBQty;
					# Re-Process CB
					$rpmCBQty = $productionAnalysisReportObj->getRPRMClosingBalance($processCodeId, $fromDate);
					$totalRPMCBQty  += $rpmCBQty;
					//on Jan 5+$rpmCBQty moni sir asked to remove rpm from cs
					$closingBalanceQty = $prodCBQty+$ppmCBQty;
					$totalClosingBalanceQty += $closingBalanceQty;	
					
					
					# Get Pre-Processed To Qty
					$diffPreProcessQty = $productionAnalysisReportObj->getDiffPreProcessQty($processCodeId, $fromDate, $tillDate, $preProcessRateListId, $fishId);
					$totalDiffPreProcessQty += $diffPreProcessQty;

					# Net Qty After CS
					$netQtyAfterCS = $closingBalanceQty-$balanceRMQty+$diffPreProcessQty;
					$totalNetQtyAfterCS += $netQtyAfterCS;
					
					# Excess/ Shotage section

					// Like settlement Sumary
					// Display Sub Total
					if ($prevSelFishId!=$fishId ) {	
						# Get Process Code recs
						$prevPCRecs = $productionAnalysisReportObj->getSelProcessCodeRecs($prevSelFishId);
	
						if ($pcTotCol % 2 ==0 ) $pcTHBgColor = "#D9F3FF"; // blue
						else if ($pcTotCol % 3 ==0 ) $pcTHBgColor = "#f2f2f2"; 
						else $pcTHBgColor = "#e3f0f7"; // Sky blue

						$disTotalHead = '<tr bgcolor="white"><td class="listing-head" colspan="16" style="padding-left:10px; padding-right:10px; line-height:normal;" nowrap align="right"><b>Total</b></td>
						<td nowrap width="'.$tdWidth.'">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
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
							
							if ($pcMaxSize!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px; line-height:normal";
							else $disStyle="padding-left:3px; padding-right:3px;";
							
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
						
						$sTdWidth = ceil(((100)/$pcMaxSize));
						$tdWidth = $pcMaxSize*100;
						
						if ($pcCol % 2 ==0 ) $pcHBgColor = "#D9F3FF";
						else if ($pcCol % 3 ==0 ) $pcHBgColor = "#f2f2f2";
						else $pcHBgColor = "#e3f0f7";
						
						$disMHead = '<tr bgcolor="white"><td class="fieldname" colspan="16" style="padding-left:10px; padding-right:10px; line-height:normal;" nowrap><b>'.$fishName.'</b></td>
						<td nowrap width="'.$tdWidth.'">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr bgcolor="'.$pcHBgColor.'" align="center">';
						$c = 0;
						//foreach ($pcRecs as $pcr) {
						$dPCHead = "";
						for ($cnt=0; $cnt<$pcMaxSize; $cnt++) {			
							$pcr = $pcRecs[$cnt];
							$c++;
							$disPC	 = $pcr[2];
							$disStyle = "";
							//echo "<br>$pcMaxSize!=$c";
							$dPCHead = ($disPC)?$disPC:'&nbsp;';
							if ($pcMaxSize!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;";
							else $disStyle="padding-left:3px; padding-right:3px;";
	
							$disMHead .= '<td class="listing-head" align="center" style="'.$disStyle.'" width="'.$sTdWidth.'%" nowrap>'.$dPCHead.'</td>
							';	
						}
						$disMHead .= '</tr></table></td><td colspan="3"></td></tr>';
						echo $disMHead;
						$pcCol++;

						# Get PC Position (Level)
						$pcPositionArr = $productionAnalysisReportObj->getPCPostion($fishId, $processCodeId);
						//printr($pcPositionArr);
					}
			?>
        <tr <?=$listRowMouseOverStyle;?>> 
                         <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" <?=$showOBDtls?>>
				<?=($rmOBQty!=0)?"<a href='###' class='link5'>$rmOBQty</a>":""?>
			</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" <?=$showArrivalDtls?>>	
				<?=($rmArrivalQty!=0)?"<a href='###' class='link5'>$rmArrivalQty</a>":""?>
			</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" <?=$showPPMCalc?>>
				<?=($preProcessedQty!=0)?"<a href='###' class='link5'>$preProcessedQty</a>":""?>
			</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$rePreProcessedQty?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$netBgColor?>"><?=($netQty!=0)?number_format($netQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" bgcolor="<?=($prdnColor!="")?$prdnColor:$prodBgColor?>" <?=$showProdnCalc?>><?=($prodPackingQty!=0)?"<a href='###' class='link5'>$prodPackingQty</a>":""?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($fpRMQty!=0)?number_format($fpRMQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$peeledBgColor?>"><?=($peeledQty!=0)?number_format($peeledQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($balanceRMQty!=0)?number_format($balanceRMQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($prodCBQty!=0)?number_format($prodCBQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($ppmCBQty!=0)?number_format($ppmCBQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($rpmCBQty!=0)?number_format($rpmCBQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$totCSBgColor?>"><?=($closingBalanceQty!=0)?number_format($closingBalanceQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($diffPreProcessQty!=0)?number_format($diffPreProcessQty,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($netQtyAfterCS!=0)?number_format($netQtyAfterCS,2,'.',''):"";?></td>
			<td class="listing-item" nowrap align="right" width="<?=$tdWidth;?>">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
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
							if ($pcMaxSize!=$cl) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;";
							else $disStyle="padding-left:3px; padding-right:3px;";						
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
							if ($processCodeId==$sPCId) $samePCQtyBgColor = "bgcolor='#FFFFCC'";	
							//#AA9988
							# Display Calc
							$disESQty = ($processCodeId!=$sPCId)?"<a href='###' class='link5' $showExShrtCalc>".number_format($excessShortQty,2,'.','')."</a>":number_format($excessShortQty,2,'.','');	
						?>
						<td class="listing-item" align="right" style="<?=$disStyle?>" width="<?=$sTdWidth?>%" nowrap <?=$samePCQtyBgColor?>>
							<?=($excessShortQty!=0)?$disESQty:"&nbsp;";?>
						</td>
						<?php	
							$p++;						
							} // Sub loop
						?>						
							</tr>
						</table>
			</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$preProcessQty?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$preProcessQty?></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$preProcessQty?></td>
	</tr>
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
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr bgcolor="<?=$pcTHBgColor?>" align="center">
				<?php
					$c = 0;					
					for ($cnt=0; $cnt<$pcMaxSize; $cnt++) {
						$pcr = $pcRecs[$cnt]; 
						$c++;
						$disStyle = "";
						$totSPCId	= $pcr[0];
						$totDisplay	= number_format(($excShrtQtyArr[$totSPCId]),2,'.','');
		
						if ($pcMaxSize!=$c) $disStyle="border-right:1px solid #999999; padding-left:3px; padding-right:3px;";
						else $disStyle="padding-left:3px; padding-right:3px;";						
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
                        <?php				
				$prevFishId = $fishId;
				$prevSelFishId = $fishId;	
				} // Loop Ends here
			?>
	  <tr bgcolor="white">
		  <td class="listing-head" align="right" >Total:</td>
		  <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalRMOBQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalRMArrivalQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalPreProcessedQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalRePreProcessedQty,2,'.','');?></strong></td>	
		 <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$netBgColor?>"><strong><?=number_format($totalNetQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$prodBgColor?>"><strong><?=number_format($totalProdPackingQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalFPRMQty,2,'.','');?></strong></td>			  
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$peeledBgColor?>"><strong><?=number_format($totalPeeledQty,2,'.','');?></strong></td>	
		 <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalBalanceRMQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalProdCBQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalPPMCBQty,2,'.','');?></strong></td>	
		 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalRPMCBQty,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;" bgcolor="<?=$totCSBgColor?>"><strong><?=number_format($totalClosingBalanceQty,2,'.','');?></strong></td>	
		 <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalDiffPreProcessQty,2,'.','');?></strong></td>  
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap="true"><strong><?=number_format($totalNetQtyAfterCS,2,'.','');?></strong></td>
		  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?//echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>	
		<td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>
		<td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>
		<td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalPreProcessedQty,2,'.','');?></strong></td>
	 </tr>
	</table>
	</td>
        </tr>
	<? if ($print==true) {?>
	<tr><TD height="30"></TD></tr>	
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">	
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintProductionAnalysisReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>&selFish=<?=$selFish?>',700,600);">
		</td>
	</tr>
	<? }?>
	<? } else if (!sizeof($getFishRecords) && $searchMode) {?>
	<tr><TD class="err1"><?=$msgNoRecords?></TD></tr>
	<? }?>
                      <tr> 
                        <td colspan="4" align="center">&nbsp;</td>
                      </tr>
                      <tr> 
                        <td colspan="4" align="center"><? if($print==true){?><!--input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('PrintPreProcessingSummary.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>',700,600);" <? if(sizeof($preProcessingSummaryRecords)==0 && $details=="" &&	$summary=="" && $selProcessorId=="" ) echo "disabled";?> --> <? }?></td>
                        <input type="hidden" name="cmdAddNew" value="1">
                      </tr>
                      <tr> 
                        <td colspan="2"  height="10" ></td>
                      </tr>
                    </table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<tr>
			<td height="10" ></td>
		</tr>
	</table>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTo", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");	
?>
