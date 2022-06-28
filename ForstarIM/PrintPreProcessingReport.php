<?php
	require("include/include.php");
	
	$dateFrom 	= $g["dateFrom"];
	$dateTo 	= $g["dateTo"];
	$details	= $g["details"];
	$summary	= $g["summary"];
	
	$Date1			=	explode("/",$dateFrom);
	$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

	$Date2			=	explode("/",$dateTo);
	$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
	
	$selFishId		=	$g["fish"];	
	$processId		=	$g["preProcessCode"];
	$selProcessorId 	= 	$g["selPreProcessor"];
	
	$processorRec	=	$preprocessorObj->find($selProcessorId);

		
		$Address	=	stripSlash($processorRec[3]);
		$Place		=	stripSlash($processorRec[4]);
		$Pincode	=	stripSlash($processorRec[5]);
		$TelNo		=	stripSlash($processorRec[6]);
		$FaxNo		=	stripSlash($processorRec[7]);
		$Email		=	stripSlash($processorRec[8]);
	$processorName = $preprocessorObj->findPreProcessor($selProcessorId);
	#Get All PreProcessor Records
	$getAllPreProcessingRecords	=	 $preprocessingreportObj-> filterPreProcessingRecords($fromDate,$tillDate,$selFishId,$processId,$selProcessorId, $details, $summary);

	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();
?>
<html>
<head>
<title>PRE-PROCESSOR MEMO</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
<tr><td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$processorName?><?//=$companyArr["Name"];?></font> </td>
  </tr>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><?=$Address; ?><?php echo " ";?><?=$Place ;//=$addr["ADR1"]?><?php if ($Pincode!=''){ echo " PinCode-$Pincode";}?><?//=$addr["ADR2"];?></td>
  </tr>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><?
	if ($TelNo!=''){ echo " Tel No-$TelNo";}
	
	//=$addr["ADR3"];?></td>
  </tr>
  
  <tr bgcolor=white>
    <td colspan="17" align="right" class="listing-item" height="5"></td>
  </tr>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2"><b>PRE - PROCESSOR MEMO</b> </font></td>
  </tr>
  
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-item"><table width="100%">
						<tr>
						<td>
                        <table border="0" align="center">
  <tr>
    <td class="fieldName">From:</td>
                                  <td class="listing-item" nowrap> 
                                  <?=$dateFrom?>                </td>
                                  <td class="fieldName" nowrap>&nbsp; Till: </td>
                                  <td class="listing-item" nowrap>&nbsp;&nbsp; 
                                  <?=$dateTo?>                </td>
  </tr>
</table>

                        <!--<table width="100%" cellpadding="0" cellspacing="0">
                              <tr>
                                <td colspan="2" height="5"></td>
                                </tr>
                              <tr>
                                <td class="fieldName" nowrap="nowrap">&nbsp;<!--Pre-Processor:--</td>
					      <td colspan="2" nowrap="nowrap" class="listing-item">&nbsp;<b><?//=$processorName?></b> </td>
                              <td class="listing-item" nowrap="nowrap" style="padding-left:10px;">  <table width="200" align="center" cellpadding="0" cellspacing="0">
                                <tr> 
                                  <td class="fieldName">From:</td>
                                  <td class="listing-item" nowrap> 
                                  <?=$dateFrom?>                </td>
                                  <td class="fieldName" nowrap>&nbsp; Till: </td>
                                  <td class="listing-item" nowrap>&nbsp;&nbsp; 
                                  <?=$dateTo?>                </td>
                                </tr>
                               </table></td>
                              </tr>
                            </table>--></td>
						</tr>
						<tr><td>
						<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
                          <tr bgcolor="#f2f2f2" align="center">
						  <? if($details!=""){?>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Date</th>
						 <? }?>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Pre-Process code </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Arrival<br> Qty </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">PreProcess<br> Qty</th>
			    <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Final<br /> Yield</th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Base Rate </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Incentive<!--Base Commn--> </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Rate<br />Amt </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Total <br>Incentive Amt<!--Commn <br />Amt--> </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Total <br />Amt </th>
                          </tr>
			    <?
				$prevFishId = 0;
				$prevProcessId	=	0;
				$totalCost = 0;
				#Setting No.of Rows
				$numRows 	=	45;
							
				$i = 0;
				$j = 0;
							
				$preProcessingReportSize = sizeof($getAllPreProcessingRecords);
		
				$totalPage = ceil($preProcessingReportSize/$numRows);

				$totalArrivalQty = 0;
				$totalPreProcessorQty = 0;
				$totalRateAmount = 0;
				$totalCommissionAmt = 0;
				foreach ($getAllPreProcessingRecords as $ppr) {
					$i++;
					$fishId			=	$ppr[1];
					$fishName		= "";
					if ($prevFishId!=$fishId) {
						$fishRec		=	$fishmasterObj->find($ppr[1]);
						$fishName		=	$fishRec[1];
					}
							
					$recDate		=	$ppr[2];
					$enteredDate	=	"";
							
					if ($details!="") {
						if($prevRecDate!=$recDate) {
							$array			=	explode("-",$recDate);
							$enteredDate	=	$array[2]."/".$array[1]."/".$array[0];
						}
					}
					else
					{
						if($prevRecDate!=$recDate){
							$array			=	explode("-",$recDate);
							$enteredDate	=	$array[2]."/".$array[1]."/".$array[0];
						}
					}
							
							
					$preProcessId			=	stripSlash($ppr[4]);
							
							$processRec			=	$processObj->find($preProcessId);
							//$preProcessCommission	=	$processRec[5];
							$preProcessCriteria		=	$processRec[6];
							$preProcessCode			=	$processRec[7];
							
							$baseRate				=	$processRec[4];
							$baseCommission			=	$processRec[5];
							
							$processorCommission = $ppr[12];			
							$processorRate		=	$ppr[13];
							# Exception Pre Processors
							list($ppeRate, $ppeCommission, $ppeCriteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $selProcessorId);
							if($processorRate!=0){
								$processRate		=	$processorRate;
								$preProcessCriteria = $preProcessCriteria;	
							} else {								
								$processRate = $ppeRate;
								$preProcessCriteria = $ppeCriteria;
							}							
							
							
							if($processorCommission!=0){
								$preProcessCommission	=	$processorCommission;
							} else {
								$preProcessCommission	= $ppeCommission;
							}							
						
							$totalPreProcessedQty	=	$ppr[8];
							
							#Count Number of Pre-Process
							$numPreProcess	=  $ppr[19];			
							
							if ($summary!="") {
								$selProcessRate 	= number_format(($processRate/$numPreProcess),2,'.','');
								$selProcessCommission	= number_format(($preProcessCommission/$numPreProcess),2,'.','');
								
								$actualYield		= number_format(($ppr[9]/$numPreProcess),2,'.','');
								$idealYield		= number_format(($ppr[10]/$numPreProcess),2,'.','');
							} else {
								$selProcessRate 		=	$processRate;
								$selProcessCommission	=	$preProcessCommission;
								
								$actualYield		=	$ppr[9];
								$idealYield		=	$ppr[10];
							}
							
							$diffYield	=	number_format(($actualYield-$idealYield),2);	
							
							$preProcessorQty = $ppr[18];
							$totalPreProcessorQty +=$preProcessorQty;
							
							$arrivalQty = number_format($preProcessorQty/($actualYield/100),2,'.','');

							$totalArrivalQty += $arrivalQty; //Find the total Arrival Qty
											
							#Criteria Calculation 1=> From/ 0=>To
							$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
							$finalYield = "";
							if($preProcessCriteria==1)
							{
								//if (From) and actual yield> ideal yield  then yield=actual yield edited on 15-1-08
								if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
									$totalPreProcessAmt 	=	($totalPreProcessedQty/($actualYield/100)) *$processRate + $totalPreProcessedQty * $preProcessCommission;
									#Calc Rate Amount {FROM)
									$rateAmount = number_format(($preProcessorQty/($actualYield/100))*$selProcessRate,2,'.','');
									$finalYield = $actualYield;
								} else {

									$totalPreProcessAmt 	=	($totalPreProcessedQty/($idealYield/100)) *$processRate + $totalPreProcessedQty * $preProcessCommission;
									#Calc Rate Amount {FROM)
									$rateAmount = number_format(($preProcessorQty/($idealYield/100))*$selProcessRate,2,'.','');
									$finalYield = $idealYield;		
								}						
							} else {
									$totalPreProcessAmt		=	$totalPreProcessedQty*$processRate + $totalPreProcessedQty * $preProcessCommission;
									$rateAmount = number_format(($preProcessorQty*$selProcessRate),2,'.','');	
									$finalYield = $idealYield;		
								}
							
							$KgRate	  =  $totalPreProcessAmt/$totalPreProcessedQty;
							
							$commissionAmount	=	number_format(($preProcessorQty*$selProcessCommission),2,'.','');
							$totalRateAmount += $rateAmount;
							$totalCommissionAmt += $commissionAmount;
							
							if ($ppr[14]!="") {
								$PreProcessorAmt   = $ppr[14];
							} else {
								$PreProcessorAmt = $preProcessorQty * $KgRate;
							}
							
							$totalCost+=$PreProcessorAmt;			
				?>
                          <tr bgcolor="#FFFFFF">
						  <? if($details!=""){?>
                            <td  class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$enteredDate?></td>
							<? }?>
                            <td  class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$preProcessCode?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$arrivalQty?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$preProcessorQty?></td>
			    <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$finalYield?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$selProcessRate?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$selProcessCommission?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$rateAmount?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$commissionAmount?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><? echo number_format($PreProcessorAmt,2);?></td>
                          </tr>
			<?
	  		if ($i%$numRows==0 && $preProcessingReportSize!=$numRows) {
				$j++;
			?>
			</table></td></tr> <tr>
			<td>
			<table width="98%" align="center" cellpadding="2">
	<tr>
        <td colspan="6" height="5"></td>
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
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table>
	</td>
	</tr>
	</table>
	</td>
	</tr></table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
<tr><td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$processorName;//=$companyArr["Name"];?></font> </td>
  </tr>
  

  <tr bgcolor=white>
    <td colspan="17" align="right" class="listing-item" height="5"></td>
  </tr>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2"><b>PRE - PROCESSOR MEMO</b> </font> - Cont.</td>
  </tr>
  
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-item"><table width="100%">
						<tr>
						<td>
                        <table align="center">
                        <tr>
    					<td class="fieldName">From:</td>
                                  <td class="listing-item" nowrap> 
                                  <?=$dateFrom?>                </td>
                                  <td class="fieldName" nowrap>&nbsp; Till: </td>
                                  <td class="listing-item" nowrap>&nbsp;&nbsp; 
                                  <?=$dateTo?>                </td>
  </tr>
                        </table>
                        <!--<table width="100" cellpadding="0" cellspacing="0">
                              <tr>
                                <td colspan="4" height="5"></td>
                                </tr>
                              <tr>
                                <td class="fieldName" nowrap="nowrap">Pre-Processor:</td>
					      <td class="listing-item" nowrap="nowrap">&nbsp;<b><?//=$processorName?></b> </td>
                              <td class="listing-item" nowrap="nowrap">&nbsp;</td>
                              <td class="listing-item" nowrap="nowrap" style="padding-left:10px;"><table width="200" cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName">From:</td>
                <td class="listing-item" nowrap> 
                  <?//=$dateFrom?>                </td>
                <td class="fieldName" nowrap>&nbsp; Till: </td>
                <td class="listing-item" nowrap>&nbsp;&nbsp; 
                  <?//=$dateTo?>                </td>
              </tr>
            </table>--></td>
              </tr>
                            </table></td>
						</tr>
						<tr><td>
						<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
                          <tr bgcolor="#f2f2f2" align="center">
						  <? if($details!=""){?>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Date</th>
						 <? }?>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Pre-Process code </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Arrival<br> Qty </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">PreProcess<br> Qty</th>
			    <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Final<br /> Yield</th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Base Rate </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Incentive<!--Base Commn--> </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Rate<br />Amt </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Total <br>Incentive Amt<!--Commn <br />Amt--> </th>
                            <th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Total <br />Amt </th>
                          </tr>
			<?
					}
				$prevFishId=$fishId;
				$prevRecDate	=	$recDate;
			}
			?>
			<tr bgcolor="#FFFFFF">
			<? if($details!=""){ ?>
                            <td  class="listing-item" nowrap="nowrap" style="padding-left:10px;">&nbsp;</td>
			<? }?>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;" align="right">Total:</td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalArrivalQty,2);?></strong></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalPreProcessorQty,2);?></strong></td>
			    <td  class="listing-item" align="right" style="padding-right:10px;">&nbsp;</td>
                            <td  class="listing-item" align="right" style="padding-right:10px;">&nbsp;</td>
                            <td  class="listing-item" align="right" style="padding-right:10px;">&nbsp;</td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalRateAmount,2);?></strong></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalCommissionAmt,2);?></strong></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalCost,2);?></strong></td>
                          </tr>
                          <!--tr bgcolor="#FFFFFF">
						  <? if($details!=""){?>
                            <td  class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</td>
							<? }?>
                            <td colspan="8" nowrap align="right"  class="listing-head"  style="padding-left:5px; padding-right:5px;">Total:</td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalCost,2);?></strong></td>
                          </tr-->
                        </table></td></tr>
						</table></td>
  </tr>
  
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><table width="98%" cellpadding="2">
      
      <tr>
        <td colspan="6" height="5"></td>
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
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr>
 
  <SCRIPT LANGUAGE="JavaScript">
	
	window.print();
	
	</SCRIPT>
</table>
</td>
</tr>
</table>
</body>
</html>


