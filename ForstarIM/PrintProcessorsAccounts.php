<?
	require("include/include.php");
	
	$processor	=	$g["selProcessor"];
	$selProcessCode	=	$g["selProcessCode"]; 
	$selRmlotId	=	$g["selRmlotId"]; 
	$fromDate	=	$g["fromDate"];
	$tillDate	=	$g["tillDate"];
	$offset		=	$g["offset"];
	$limit		=	$g["limit"];	
	$dailyPreProcessRecords	=	$processorsaccountsObj->fetchAllRecordsNew($fromDate, $tillDate, $processor, $offset, $limit, $selProcessCode,$selRmlotId);
	$processorRec		=	$preprocessorObj->find($processor);
	$processorName		=	$processorRec[2];
	$pageNo		= 1;
	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();	
?>
<html>
	<head>
		<title>Pre-Processor's A/c Settlement</title>
		<link href="libjs/style.css" rel="stylesheet" type="text/css">
	</head>
<body>
	<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#f2f2f2">
		<tr bgcolor="#FFFFFF">
			<td colspan="12" align="center" class="pageName">Statement of Pre-Processor's A/c Settlement</td>
        </tr>
		<?
		if (sizeof($dailyPreProcessRecords)) {
		$i	=	0;
		?>
        <tr bgcolor="#FFFFFF">
           <td colspan="12" align="center" class="fieldName">Of M/s <?=$processorName?></td>
        </tr>
		<tr bgcolor="#FFFFFF">
			<td colspan="12" align="center">&nbsp;</td>
		</tr>				  
		<tr bgcolor="#FFFFFF">
             <th colspan="12" align="center">
				<table width="100%" class="print">				
                    <tr bgcolor="#f2f2f2" align="center">
						<th class="listing-head">No</th>
						<th class="listing-head">RM Lot Id</th>
                        <th class="listing-head">Fish</th>
                        <th class="listing-head">Pre-Process<br /> Code </th>
                        <th class="listing-head">Base <br />Rate(Rs.) </th>
                        <th class="listing-head">Base<br /> Commi (Rs.) </th>
                        <th class="listing-head">Pre Processed <br />Quantity</th>
                        <th class="listing-head">Commi</th>
                        <th class="listing-head">Rate</th>
                        <th class="listing-head">Total</th>
                       <th class="listing-head">Setld date </th>
                    </tr>
                   <?
					$settledAmount = "";
					$duesAmount    = "";
					$totalProcessRate = "";
					foreach ($dailyPreProcessRecords as $dpr) 
					{
						$i++;
						$pDate		=	explode("-",$dpr[2]);
						$setldDate	=	$dpr[18];
						$processorSettledDate = "";
						if ($setldDate!=0) {
						$array			=	explode("-",$setldDate);
						$processorSettledDate	=	$array[2]."/".$array[1]."/".$array[0];
						}
						$fishId			=	$dpr[1];
						$fishRec		=	$fishmasterObj->find($fishId);
						$fishName		=	$fishRec[1];
						$preProcessId		=	stripSlash($dpr[4]);
						$processRec		=	$processObj->find($preProcessId);
						$preProcessCode		=	$processRec[7];
						$preProcessorQtyId	=	$dpr[12];
						$totalArrivalQty	=	$dpr[7];
						$totalPreProcessedQty	=	$dpr[8];
						$preProcessedQty	=	$dpr[13];
						$preProcessorId 	= 	$dpr[20];
						#To Take the Rate & Commi
						/*
						$processRateRec	= $processorsaccountsObj->filterProcessRec($preProcessId);		
						$rate			=	$processRateRec[2];
						$commission		=	$processRateRec[3];
						$criteria		=	$processRateRec[4];
						*/
						list($rate, $commission, $criteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $preProcessorId);
									
						$lanCenterId 		=	$dpr[19];
						######################
						$processYieldRec = $dailypreprocessObj ->findYieldRec($preProcessId, $lanCenterId);
					
						$monthArray	=	array($processYieldRec[3], $processYieldRec[4], $processYieldRec[5], $processYieldRec[6], $processYieldRec[7], $processYieldRec[8], $processYieldRec[9], $processYieldRec[10], $processYieldRec[11], $processYieldRec[12], $processYieldRec[13], $processYieldRec[14]);
						$day	=	"";
						if($pDate[1]<10) $day =	$pDate[1]%10;
						else $day = $pDate[1];

						$idealYield = $monthArray[$day-1];
						#################					
																			
						$selectCommission	=	$dpr[14];
						$selectRate		=	$dpr[15];
						$actualRate		=	$dpr[16];
								
						$paidStatus		=	$dpr[17];
										
						$displayCommission = "";	
						if ($selectCommission!="" && $selectCommission!=0) {
						$displayCommission	=	$selectCommission;
						} else {
						$displayCommission	=	$commission;
						}
									
						$changedRate = "";	
						if ($selectRate!="" && $selectRate!=0) {
						$changedRate	=	$selectRate;
						} else {
						$changedRate		=	$rate;
						}
						$actualYield		=	$dpr[9];
						$diffYield	=	number_format(($actualYield-$idealYield),2,'.','');	
						#Criteria Calculation 1=> From / 0=>To
						$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
						$totalPreProcessAmt = "";
						if ($criteria==1) {
						//if (From) and actual yield> ideal yield  then yield=actual yield
							if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
								$totalPreProcessAmt 	=	($totalPreProcessedQty/($actualYield/100)) * $changedRate + $totalPreProcessedQty * $displayCommission;
							} else {
								$totalPreProcessAmt 	=	($totalPreProcessedQty/($idealYield/100)) * $changedRate + $totalPreProcessedQty * $displayCommission;
							}
						} else {
						$totalPreProcessAmt		=	$totalPreProcessedQty*$changedRate + $totalPreProcessedQty * $displayCommission;
						}
							
						$ratePerKg	=	 $totalPreProcessAmt/$totalPreProcessedQty;

						$amount		=	$preProcessedQty * $ratePerKg;												$totalRate = "";
					
						if ($actualRate!="" && $actualRate!=0 && $paidStatus=='Y') {
							$totalRate	= $actualRate;	
						} else {
							$totalRate	=	number_format($amount,2,'.','');
						}
						# Column Total
						$totalProcessRate	+=$totalRate;
						if ($paidStatus=='Y') {
							$checked	=	"Checked";
							$settledAmount	= $settledAmount +	$totalRate;
						} else {
							$checked	=	"";
							$duesAmount	= $duesAmount +	$totalRate;
						}
						$disabled = "";
						$edited	  = "";
						if ($paidStatus=='Y' && $isAdmin==false && $reEdit==false) {
							$disabled = "readonly";
							$edited	  = 1;
						}
						($dpr[25]!='' && $dpr[25]!='0')?$rmlotName = $dpr[25]:$rmlotName = "";
						($dpr[24]!='' && $dpr[24]!='0')?$rmlotId = $dpr[24]:$rmlotId = "";
														
						?>
                        <tr bgcolor="#FFFFFF">
							<td class="listing-item">&nbsp;&nbsp;<input type="hidden" name="preProcessorQtyId_<?=$i;?>" value="<?=$preProcessorQtyId?>"><?=(($pageNo-1)*$limit)+$i?></td>
                            <td class="listing-item">&nbsp;&nbsp;<?=$rmlotName?></td>
							<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$fishName?></td>
                            <td class="listing-item">&nbsp;&nbsp;<?=$preProcessCode?></td>
                            <td class="listing-item" align="right" style="padding-right:10px;"><? if($rate==""){?><img src="images/x.gif" width="20" height="20"><? } else { echo $rate;}?></td>
                            <td class="listing-item" align="right" style="padding-right:10px;"><?=$commission?></td>
                            <td class="listing-item" align="right"><input type="hidden" name="totalArrivalQty_<?=$i?>" id="totalArrivalQty_<?=$i?>" value="<?=$totalArrivalQty?>"><input type="hidden" name="totalPreProcessedQty_<?=$i?>" id="totalPreProcessedQty_<?=$i?>" value="<?=$totalPreProcessedQty?>"><input type="hidden" name="preProcessedQty_<?=$i?>" id="preProcessedQty_<?=$i?>" value="<?=$preProcessedQty?>"><input type="hidden" name="preProcessRate_<?=$i?>" id="preProcessRate_<?=$i?>" value="<?=$rate?>"><input type="hidden" name="preProcessCommission_<?=$i?>" id="preProcessCommission_<?=$i?>" value="<?=$commission?>"><input type="hidden" name="criteria_<?=$i?>" id="criteria_<?=$i?>" value="<?=$criteria?>"><input type="hidden" name="idealYield_<?=$i?>" id="idealYield_<?=$i?>" value="<?=$idealYield?>"><input type="hidden" name="actualYield_<?=$i?>" id="actualYield_<?=$i?>" value="<?=$actualYield?>">
                            <?=$preProcessedQty?> &nbsp;&nbsp;</td>
                            <td class="listing-item" nowrap align="right"><?=$displayCommission?>&nbsp;&nbsp;</td>
                            <td class="listing-item" nowrap align="right"><?=$changedRate?>&nbsp;&nbsp;</td>
                            <td class="listing-item" nowrap align="right" style="padding-left:2px; padding-right:2px;"><?=$totalRate?></td>
                            <td class="listing-item" nowrap align="center"><?=$processorSettledDate?></td>
                        </tr>
                        <? }?>
                            <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
                        <tr bgcolor="white">
                           <td height="10" colspan="9" align="right" class="listing-head">Total:</td>
                           <td height="10" align="right" style="padding-left:2px; padding-right:2px;" class="listing-item"><strong><? echo number_format($totalProcessRate,2);?></td>
                           <td height="10" align="center"></strong></td>
                       </tr>
					<? }?>
				</table>									
			</td>
        </tr>
		<tr bgcolor="#FFFFFF">
			<td colspan="13" height=1>
				<table width="450">
					<tr>
						<td class="fieldName" nowrap="nowrap">Already Settled:</td>
                        <td class="listing-item" nowrap="nowrap" align="left"><?=$settledAmount;?></td>
                        <td class="fieldName" nowrap="nowrap">Total Dues: </td>
                        <td class="listing-item" nowrap="nowrap" align="left"><?=$duesAmount;?></td>
                        <td class="fieldName" nowrap="nowrap">Net Payable:</td>
                        <td class="listing-item" nowrap="nowrap" align="left"><?=$duesAmount;?></td>
                    </tr>
                 </table>
			</td>
		</tr>
 <SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
</body>
</html>