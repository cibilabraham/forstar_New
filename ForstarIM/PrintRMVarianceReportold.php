<?php
	require("include/include.php");
	
	$dateFrom 	= $g["dateFrom"];
	$dateTo 	= $g["dateTo"];
	
	$rmlotid		= $g["rmlotid"];	
	$supplier		= $g["supplier"];
	
	$Date1			=	explode("/",$dateFrom);
	$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

	$Date2			=	explode("/",$dateTo);
	$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
	$rmLotRecords = $rmVarianceReportObj->getRmLotRec($fromDate,$tillDate,$rmlotid,$supplier);
	
	# Get Company Details
	list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
	$displayAddress		= "";
	$displayTelNo		= "";
	if ($companyName)	$displayAddress = $address."&nbsp;".$place."&nbsp;".$pinCode;
	if ($telNo)		$displayTelNo	= $telNo;
	if ($faxNo)		$displayTelNo	.= "&nbsp;/&nbsp;".$faxNo;	

	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();	

	$exportAddrArr=$purchaseorderObj->getAllCompany();
	$exportAddrContact=$purchaseorderObj->getAllCompanyContact($exportAddrArr[0]);
?>
<html>
<head>
<title>RM Variance Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function printDoc()
{
	window.print();	
	return false;
}

function printThisPage(printbtn)
{	
	document.getElementById("printButton").style.display="none";	
	if (!printDoc()) {
		document.getElementById("printButton").style.display="block";			
	}		
}
</script>
</head>
<body>
	<table width="90%" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this)" style="display:block"></td>
		</tr>
	</table>
	<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
		<tr>
			<td>
				<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
					<tr bgcolor=white>
						<td colspan="17" align="center" class="listing-head" height="10"><img src="images/ForstarLogo.png" alt=""></td>
					</tr>
					<tr bgcolor=white>
						<td align="right">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<TD>
										<table cellpadding="0" cellspacing="0">
											<TR>
												<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$exportAddrArr[1]?></font></TD>
											</TR>										
										</table>
									</TD>
								</tr>
								<tr>
									<TD class="print-SOTHead-item"><?=$exportAddrArr[2]?></TD>
								</tr>
								<tr>
									<TD class="print-SOTHead-item"><?=$exportAddrArr[3]?>, <?=$exportAddrArr[4]?>,<?=$exportAddrArr[5]?></TD>
								</tr>							
								<tr>
									<TD class="print-SOTHead-item">
									<?php 
									 foreach($exportAddrContact as $expt1)
									 {
										 if($expt1[1]!='') echo $expt1[1].',';
										
									 }
									 ?>
									</TD>
								</tr>
								<tr>
									<TD class="print-SOTHead-item"><?php 
									 foreach($exportAddrContact as $expt2)
									 {
										
										 if($expt2[2]!='')  echo $expt2[2].',';
									 }
									 ?>
									 </TD>
								</tr>
								<tr>
									<TD class="print-SOTHead-item">
									<?php 
									foreach($exportAddrContact as $expt3)
									 {
										 if($expt3[3]!='') echo  $expt3[3].',';
									 }
									 ?>
									 </td>
								</tr>
								<tr>
									<TD class="print-SOTHead-item"><?php 
									 foreach($exportAddrContact as $expt4)
									 {
										 if($expt4[4]!='') echo $expt4[4].',';
									 }
									 ?>
									</TD>
								</tr>
							</table>
						</td>
					</TR>
				</table>
			</TD>
		</tr>
		<tr bgcolor=white>
			<td colspan="17" align="right" class="listing-item">
				<table width="200" cellpadding="0" cellspacing="0">
					<tr> 
						<td class="fieldName">From:</td>
						<td class="listing-item" nowrap> <?=$dateFrom?> </td>
						<td class="fieldName" nowrap>&nbsp; Till: </td>
						<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$dateTo?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor=white>
			<td colspan="17" align="center" class="listing-item" height="10"></td>
		</tr>


	</table>
</td>
</tr>
</table>


<!--  Detailed / Summary View -->
	<?php
	if (sizeof($rmLotRecords)>0) {
	?>
	<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
		<TR>
			<TD>
				<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
					
					<TR>
					
					<tr bgcolor=white>
						<td colspan="17" align="center" class="listing-item" height="10"></td>
					</tr>
					<tr bgcolor=white> 
						<td colspan="17" align="center" class="listing-item">
							<table width="100%">
								<tr>
									<td height="10"></td>
								</tr>
								<tr>
									<td>
										<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
											<tr  bgcolor="#f2f2f2" align="center">	
												<td class="listing-head" style="padding-left:5px; padding-right:5px; height:40px" colspan="4" >Procurement</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px; " colspan="7" >Receiving</td>
											</tr>
											<tr  bgcolor="#f2f2f2" align="center" >
												<!--td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">PO</td-->
												<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px"  nowrap>RM Lot Id</td>
												<td colspan="3" >
													<table width="332px">
														<tr>
															<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999;"  nowrap>Supplier</td>
															<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999;"  >Gate Supervisor</td>
															<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px; "  nowrap>Date</td>
														</tr>
													</table>
												</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999;"  nowrap>Supervisor</td>
												<td colspan="6" >
													<table width="600px">
														<tr >	
															<td  class="listing-head"   style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999;" nowrap>Date</td>
															<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999; height: 38px" nowrap>Variety </td>
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:170px; border-right:1px solid #999999;" nowrap>Item Name</td>
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999;" nowrap>Count</td>
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999;"  nowrap>Qty</td>	
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; " nowrap>Soft%</td>
														</tr>
													</table>
												</td>
											</tr>
											<?php
												# Setting No.of Rows //25
												$numRows 	= 25;
																
												$i = 0;
												$j = 0;
												$oldj='';				
												$rmLotRecordsSize = sizeof($rmLotRecords);
											
												$totalPage = ceil($rmLotRecordsSize/$numRows);
															
												$prevFishId 	= 0;	
												$prevProcessId	= 0;
												$prevRecDate 	= "";				 
												foreach ($rmLotRecords as $ppr) {
													$i++;
													$fishId			=	$ppr[1];
													$fishName		= "";
													//echo "hii".$oldj."hui".$j;
													if (($prevFishId!=$fishId) || ($oldj!=$j)){
														$fishRec	=	$fishmasterObj->find($ppr[1]);
														$fishName	=	$fishRec[1];
													}
													$enteredDate	=	"";
													$recDate	= $ppr[2];	
													if (($prevRecDate!=$recDate) || ($oldj!=$j)) {
														$enteredDate	= dateFormat($recDate);
													}
													$preProcessId		=	stripSlash($ppr[4]);
													$processRec		=	$processObj->find($preProcessId);
													$preProcessCommission	=	$processRec[5];
													$preProcessCriteria	=	$processRec[6];
													$preProcessCode		=	$processRec[7];			
														
													$processRate		=	$dailypreprocessObj->findProcessRate($preProcessId);		
													$commissionRate		=	$preprocessingreportObj->findCommissionRate($preProcessId);	
														
													$dailyPreProcessEntryId	= 	$ppr[3];	
													$totalArrivalQty	=	$ppr[7];
													$totalPreProcessedQty	=	$ppr[8];
																	
													$actualYield		=	$ppr[9];
													$idealYield		=	$ppr[10];
													//echo "<br>Bef--".$preProcessCode."=".$actualYield."=>".$idealYield;
													# Count Number of Pre-Process
													$numPreProcess	=  $ppr[12];
													if ($summary!="") {					
														$actualYield =	number_format(($actualYield/$numPreProcess),2,'.','');
														$idealYield	 =	number_format(($idealYield/$numPreProcess),2,'.','');
													}								
													$diffYield = number_format(($actualYield-$idealYield),2);
													$IYield	  = ($idealYield/100);
													$aYield	  = ($actualYield/100);
															
													# Criteria Calculation 1=> From/ 0=>To				
													$totalPreProcessAmt = 0;
													$cnt = 0;
													$calcRateAmt = 0;
													$calcCommiAmt = 0;
													foreach ($preProcessorRecords as $ppr)
													{
														$mPProcessorId	=	$ppr[0];
														$ppQty = "";
														if ($details!="") {
															$preProcessorRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId, $mPProcessorId);
															$ppQty	=	$preProcessorRec[3];
														} else {
															$preProcessorHOQty = $preprocessingreportObj->findPreProcessorHOQty($fishId, $preProcessId, $fromDate, $tillDate, $mPProcessorId);
															$ppQty	=	$preProcessorHOQty[1];	
														}
														$preProcessorAmt = 0;
														if ($ppQty!=0) {
															$cnt++;
															list($ppeRate, $ppeCommission, $ppeCriteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $mPProcessorId);					
															$selPPRate = $ppeRate;
															$calcRateAmt += $selPPRate;
															$selPPCommi = $ppeCommission;
															$calcCommiAmt += $selPPCommi;
															$selPPCriteria = $ppeCriteria;
															$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
															if ($selPPCriteria==1) {
														//if (From) and actual yield> ideal yield  then yield=actual yield
																if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
																	$preProcessorAmt = ($ppQty/$aYield)*$selPPRate+$ppQty*$selPPCommi;
																} else {
																	$preProcessorAmt = ($ppQty/$IYield)*$selPPRate+ $ppQty*$selPPCommi;
																}					
															} else {
																$preProcessorAmt = $ppQty*$selPPRate+$ppQty*$selPPCommi;
															}
															$totalPreProcessAmt += $preProcessorAmt;
														} // Qty check ends here
													} // Pre Processor Loops Ends here
													$calcRateAmt = ($calcRateAmt/$cnt);
													$calcCommiAmt = ($calcCommiAmt/$cnt);
													$totalPreProcessCost+=$totalPreProcessAmt;
												?>
												<tr bgcolor="#FFFFFF"> 
													<td  class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$enteredDate?>&nbsp;</td>
													<td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$fishName?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$preProcessCode?></td>
													<?
													foreach ($preProcessorRecords as $pr)
													{
														$masterPreProcessorId	=	$pr[0];
														if ($details!="") {
															$preProcessorRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId, $masterPreProcessorId);
															$preProcessorQty	=	$preProcessorRec[3];
														} else {
															$preProcessorHOQty = $preprocessingreportObj->findPreProcessorHOQty($fishId, $preProcessId, $fromDate, $tillDate, $masterPreProcessorId);
															$preProcessorQty	=	$preProcessorHOQty[1];	
														}
													?>
													<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$preProcessorQty?></td>
															<? }?>
													<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$totalPreProcessedQty?></td>
													<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$actualYield?></td>
													<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$diffYield?></td>
													<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=number_format($calcRateAmt,2,'.','');//$selPPRate?></td>
													<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=number_format($calcCommiAmt,2,'.','');//$selPPCommi?></td>
													<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><? echo number_format($totalPreProcessAmt,2);?></td>
												</tr>
												<?
												$oldj=$j;
												if($i%$numRows==0 && $preProcessingReportSize!=$numRows){
													$j++;
													
												?>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table width="98%" cellpadding="3">
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
													<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
												</tr>
												<tr valign="top">
													<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<!-- Setting Page Break start Here-->
		<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
		<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
			<tr>
				<td>
					<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-head" height="10"></td>
						</tr>
						<tr bgcolor=white> 
							<td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="right" class="listing-item">
								<table width="200" cellpadding="0" cellspacing="0">
									<tr> 
										<td class="fieldName">From:</td>
										<td class="listing-item" nowrap> 
										  <?=$dateFrom?>                
										</td>
										<td class="fieldName" nowrap>&nbsp; Till: </td>
										<td class="listing-item" nowrap>&nbsp;&nbsp; 
										  <?=$dateTo?>                
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item" height="10"></td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item"><font size="3"><b><? if ($details!="") {?>DETAILED REPORT<? } else if ($summary!="") {?>SUMMARY REPORT<? }?></b> </font> - Cont.</td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item" height="10"></td>
						</tr>
						<tr bgcolor=white> 
							<td colspan="17" align="center" class="listing-item">
								<table width="100%">
									<tr>
										<td height="10"></td>
									</tr>
									<tr>
										<td>
											<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
												<tr bgcolor="#f2f2f2" align="center"> 
													<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Date</th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Fish</th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Code </th>
                                   					<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Total PreProcessed Qty</th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Actual Yield </th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Diff. Yield </th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Rate <br>Amt</th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Commn <br>Amt</th>
													<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Total Amt </th>
												</tr>
												<?
												
												}
												$prevFishId=$fishId;
												$prevRecDate	=	$recDate;
												}
												?>
												<tr bgcolor="white">
													<td>&nbsp;</td>
													<td class="listing-head" align="center">&nbsp;</td><? }?>
													<td align="right" class="listing-item">&nbsp;</td>
													<td align="right" class="listing-item">&nbsp;</td>
													<td align="right" class="listing-head" style="padding-right:10px;">&nbsp;</td>
													<td align="right" class="listing-item">&nbsp;</td>
													<td align="right"><span class="listing-head" style="padding-right:10px;">Total:</span></td>
													<td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalPreProcessCost,2);?></strong></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor=white> 
							<td colspan="17" align="center" class="fieldName">
								<table width="98%" cellpadding="3">
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
										<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
									</tr>
									<tr valign="top">
										<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
									</tr>
								</table>
							</td>
						</tr> 
					</table>
				</td>
			</tr>
		</table>
		


</body>
</html>


