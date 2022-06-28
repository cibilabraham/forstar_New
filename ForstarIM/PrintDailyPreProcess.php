<?php
	require("include/include.php");

	$recordsFilterId	=	$g["selFilter"];
	$recordsDate		=	$g["selDate"];

	$dailyPreProcessRecords		= $dailypreprocessObj->dailyPreProcessRecFilter($recordsFilterId,$recordsDate);
	$dailyPreProcessRecordsSize	= sizeof($dailyPreProcessRecords);
	
	$currentUrl="DailyPreProcess.php"; 
	//$preProcessorRecords	=	$preprocessorObj->fetchAllPreProcessingRecords($currentUrl, '');
	$activeProcessorRecords	= $preprocessorObj->getActiveProcessorRecsForDailyPreProcess($currentUrl, '');
	//$activeProcessorRecords	= $preprocessorObj->getActiveProcessorRecs($currentUrl, '');
	$selProcessors		= $dailypreprocessObj->getSelProcessor($recordsDate);	
	//$preProcessorRecords	= ary_merge($activeProcessorRecords, $selProcessors);
	$preProcessorRecords	= multi_unique(array_merge($activeProcessorRecords, $selProcessors));	
	# sort by name asc
	usort($preProcessorRecords, 'cmp_name');
	
	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Pre-Process </td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							<?
								if($errDel!="")
								{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" >
									<table cellpadding="1"  width="98%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                     			 			<?
										if( sizeof($dailyPreProcessRecords) > 0 )
											{
												$i	=	0;
								$headStyle = "padding-left:5px;padding-right:5px;font-size:11px;line-height:normal;";		
								?>
                      <tr  bgcolor="#f2f2f2" align="center" > 
						<td class="listing-head" nowrap style="padding-left:2px; padding-right:2px;">RM Lot ID</td>
                        <td class="listing-head" nowrap style="<?=$headStyle?>">Fish</td>
                        <td class="listing-head" style="<?=$headStyle?>">Pre-Process Code </td>
						<td class="listing-head" style="padding-left:2px; padding-right:2px;">Available<br> Qty<!--Total Qty--></td>
                        
                        <td class="listing-head" style="<?=$headStyle?>">Actual Qty</td>
						<?
							foreach ($preProcessorRecords as $pr)
									{
										$processorName	=	stripSlash($pr[1]);
						?>
                        <td class="listing-head" style="<?=$headStyle?>"><?=$processorName?></td>
						<? }?>
                        <td class="listing-head" style="<?=$headStyle?>">Total PreProcessed Qty</td>
                        <td class="listing-head" style="<?=$headStyle?>">Actual Yield(%)</td>
                        <td class="listing-head" style="<?=$headStyle?>">Ideal Yield (%)</td>
                        <td class="listing-head" nowrap style="<?=$headStyle?>">Diff (%) </td>
                        <td class="listing-head" style="<?=$headStyle?>">Total PreProcess Amt</td>
                      </tr>
                      <? 
					  			$dailyPreProcessAmount	=	"";
								foreach($dailyPreProcessRecords as $pr)
									{
																								
										$i++;
										$dailyPreProcessId		=	$pr[0];
										$fishId					=	$pr[1];	
										$fishName				=	stripSlash($fishmasterObj->findFishName($fishId));										
										$preProcessId			=	$pr[4];
										
										$processRec				=	$processObj->find($preProcessId);
										
										$preProcessCommission	=	$processRec[5];
										$preProcessCriteria		=	$processRec[6];
										$preProcessCode			=	$processRec[7];
																			
										$processRate		=	$dailypreprocessObj->findProcessRate($preProcessId);		
																		
										$openingBalQty		=	$pr[5];
										$arrivalQty			=	$pr[6];	
										$totalArrivalQty	=	$pr[7];
										
										$dailyPreProcessEntryId	= $pr[3];
										//$preProcessorRecs	=	$dailypreprocessObj->preProcessorRecFilter($dailyPreProcessEntryId);	
										
										$totalPreProcessedQty	=	$pr[8];
										$actualYield			=	$pr[9];
										$idealYield				=	$pr[10];
										$diffYield				=	$pr[11];	
										
										#	Criteria Calculation 1=> From/ 0=>To
										#	HO-HL if  From HOXRate+HL*commi
 										#	HO-HL if  To   HL Xrate + HL * commi
										$IYield	  = ($idealYield/100);	
										$aYield	  = ($actualYield/100);
										$lotIdStatus=$pr[17];
										/*
										if($preProcessCriteria==1){
											$totalPreProcessAmt 	=	($totalPreProcessedQty/$IYield) * $processRate + $totalPreProcessedQty * $preProcessCommission;
										}
										else {
											$totalPreProcessAmt		=	$totalPreProcessedQty*$processRate + $totalPreProcessedQty * $preProcessCommission;
										}
										*/
										# New calculation
				$totalPreProcessAmt = 0;
				foreach ($preProcessorRecords as $ppr) {
					$mPrePId = $ppr[0];
					//$ppRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId,$mPrePId);
					if($lotIdStatus!="available")
					{
						$ppRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId,$mPrePId);
					}
					else
					{
					
						$ppRec = $dailypreprocessObj->findPreProcessorRMlotidRec($dailyPreProcessEntryId,$mPrePId);
					}
					$ppQty = $ppRec[3];
					$preProcessorAmt = 0;
					if ($ppQty!=0) {
						list($ppeRate, $ppeCommission, $ppeCriteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $mPrePId);
						/*
						$selPPRate = ($ppeRate!=0)?$ppeRate:$processRate;
						$selPPCommi = ($ppeCommission!=0)?$ppeCommission:$preProcessCommission;
						$selPPCriteria = ($ppeRate!=0)?$ppeCriteria:$preProcessCriteria;
						*/
						$selPPRate = $ppeRate;
						$selPPCommi = $ppeCommission;
						$selPPCriteria = ($ppeRate!=0)?$ppeCriteria:$preProcessCriteria;
						$selYieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
						//echo $selPPCriteria;
						if ($selPPCriteria==1) {
					//if (From) and actual yield> ideal yield  then yield=actual yield
							//echo "<br>$diffYield<$selYieldTolerance::$selPPCriteria";
							if ($actualYield>$idealYield && $diffYield<$selYieldTolerance) {
								//echo "echo $actualYield>$idealYield && $diffYield<$selYieldTolerance";
								$preProcessorAmt = ($ppQty/$aYield)*$selPPRate+$ppQty*$selPPCommi;
							} else {
								$preProcessorAmt = ($ppQty/$IYield)*$selPPRate+ $ppQty*$selPPCommi;
							}	
								
							if ($actualYield>$idealYield && $diffYield>$selYieldTolerance) $diffCalcUsed = true;
						} else {
							$preProcessorAmt = $ppQty*$selPPRate+$ppQty*$selPPCommi;
						}
						//echo "<br>$selPPRate, $selPPCommi, $selPPCriteria=>$preProcessorAmt<br>";
						$totalPreProcessAmt += $preProcessorAmt;
					}// Qty check ends here
				} // PProcessor Loop Ends here
				$dailyPreProcessAmount += $totalPreProcessAmt;
				
				$selLandingCenter ="";		
				if ($pr[12]!=0) $selLandingCenter = $landingcenterObj->findLandingCenter($pr[12]);	

				$confirmStatus	= $pr[13];
				$editDisabled = "";	
				//if ($confirmStatus=='Y' && $reEdit==false) {
				if ($confirmStatus=='Y') {
					$editDisabled = "disabled";
				}
				$dppAvailableQty = number_format($pr[14],2,'.','');
				$totalDppAvailableQty += $dppAvailableQty;	
				$autoGeneratedCalc = $pr[15];
				
				
				$selProcessFromId = $pr[16];
				$showAvailableCalc = "";
				if (!isset($pFromArr[$selProcessFromId])) {
					# Display Calc
					$displayAvailableCalc = $dailypreprocessObj->disAvailableQtyCalc($selProcessFromId, $recordsDate);
					if ($dppAvailableQty!=0) {
						$showAvailableCalc = "onMouseover=\"ShowTip('$displayAvailableCalc');\" onMouseout=\"UnTip();\" ";
					}
					$pFromArr[$selProcessFromId] = $dppAvailableQty;
				}
			?>
                      <tr  bgcolor="WHITE"  > 
						<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;">
							<? if($lotIdStatus=="available") { echo $pr[18]; } ?>
						</td>
                        <td height="25" nowrap class="listing-item" >&nbsp;&nbsp;<?=$fishName;?></td>
                        <td class="listing-item" nowrap style="padding-left:10px;" ><?=$preProcessCode?></td>
						<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
							<?=($dppAvailableQty!=0)?(($showAvailableCalc!="")?"<a href='###' class='link5' $showAvailableCalc>$dppAvailableQty</a>":$dppAvailableQty):""?>
						</td>
                        
                        <td class="listing-item" align="right" style="padding-right:10px;"><?=$totalArrivalQty?></td>
								<?php
						foreach ($preProcessorRecords as $ppr) {
							$masterPreProcessorId	=	$ppr[0];
							if($lotIdStatus!="available")
								{
									$preProcessorRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId,$masterPreProcessorId);
								}
								else
								{
								
									$preProcessorRec = $dailypreprocessObj->findPreProcessorRMlotidRec($dailyPreProcessEntryId,$masterPreProcessorId);
								}
							
							
							//$preProcessorRec	=	$dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId,$masterPreProcessorId);
							//$preProcessorQty	=	$preProcessorRec[3]; edited 05-01-07
							if ($preProcessorRec[3]!=0) $preProcessorQty = $preProcessorRec[3];
							else $preProcessorQty	= "";
							if ($preProcessorQty!="") $dProcessorArr[$masterPreProcessorId] += $preProcessorQty;
						?>
									<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;" nowrap><?=$preProcessorQty;?></td>
						<?
							}
						?>
                        <td class="listing-item" align="right" style="padding-right:10px;"><?=$totalPreProcessedQty?></td>
                        <td class="listing-item" align="right" style="padding-right:10px;"><?=$actualYield?> </td>
                        <td class="listing-item" align="right" style="padding-right:10px;"><?=$idealYield?></td>
                        <td class="listing-item" align="right" style="padding-right:10px;" ><?=$diffYield?></td>
                        <td class="listing-item" align="right" style="padding-right:10px;"><? echo number_format($totalPreProcessAmt,2);?></td>
						<? if($edit==true){?>                        
                        <? }?>
                      </tr>
                      <?
												}
										?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">
					  <input type="hidden" name="preProcessEntryId" value="">
                      <input type="hidden" name="editSelectionChange" value="0">
                      
                      <tr bgcolor="white">
                        <td height="10" colspan="5" align="center">&nbsp;</td>
                        <?
						
							foreach ($preProcessorRecords as $pr)
									{
						?>
                        <td height="10" align="center">&nbsp;</td>
						<? }?>
                        <td height="10" colspan="4" align="right" class="listing-head">Total:</td>
                        <td height="10" class="listing-item" align="right" style="padding-right:10px;"><strong><? echo number_format($dailyPreProcessAmount,2);?></strong></td>
                        </tr>
					  <?
					  	}
						else
						{
					  ?>
                      <tr bgcolor="white"> 
                        <td colspan="11"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      <?
											}
										?>
                    </table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						
						</table>
					</td>
				</tr>
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
