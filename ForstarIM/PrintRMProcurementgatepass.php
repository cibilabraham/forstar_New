<?php
	require("include/include.php");
# select record between selected date

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$rmProcurementGatePassRecords = $rmProcurmentGatePassObj->fetchAllDateRecords($fromDate, $tillDate);
		$procurmentRecssize	=	sizeof($rmProcurementGatePassRecords);	
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="10" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;
	  	 RM Procurment GatePass</td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;" >
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?php
									if($procurmentRecssize > 0 ) {
											$i	=	0;
									?>
									
									<tr  bgcolor="#f2f2f2" align="center">		
										<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">ID</td>
										<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Out Date & Time</td>
										
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Out Seal Number</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supervisor</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Other Seals</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Labours</td>
		
									</tr>
									<?php
									
										foreach ($rmProcurementGatePassRecords as $sir) {
										
											$i++; $existReceipt="";
											$procurmentGatePassId	=	$sir[0];
											
											$procurmentGatePass		=	$sir[1];
											$outTime       =	$sir[2];
											$sealNoOutId		=	$sir[3];
											$sealNoOut=$rmProcurmentGatePassObj->getSealNumber($sealNoOutId);
											$sealNo=$sealNoOut[1];
											$supervisorId       =	$sir[4];
											$current_date       =	dateFormat($sir[5]);
											$numbergen=$sir[6];
											$outalpha=$sir[7]; 
											 
											$supervisor=$rmProcurmentGatePassObj->getSupervisor($supervisorId);
											$supervisorName=$supervisor[1];
											$sealNumbers= $rmProcurmentGatePassObj->getSealNumbers($procurmentGatePassId);
											$labours= $rmProcurmentGatePassObj->getLabours($procurmentGatePassId);
											$procurmentorderId=$sir[8]; 
											$existReceipt= $rmProcurmentGatePassObj->checkExistInReceipt($procurmentorderId);
											($existReceipt!='') ? $disabled="disabled" :$disabled="";
											
										?>
										<tr  bgcolor="WHITE">
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$procurmentGatePass;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$current_date;?> & <?=$outTime;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$outalpha.$sealNoOutId;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supervisorName;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
											<?php
												if (sizeof($sealNumbers)>0) {
													$nextRec = 0;						
													foreach ($sealNumbers as $cR) {	
														echo $cR[2].$cR[1].'<br/>';
													}
												}
											?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<?php
												$numLine = 3;
												if (sizeof($labours)>0) {
													$nextRec = 0;						
													foreach ($labours as $cR) {					
														$labourName = $cR[1];	
														
														$nextRec++;
														if($nextRec>1) echo "<br>"; echo $labourName;
														if($nextRec%$numLine == 0) echo "<br/>";	
													}
												}
												?>
											</td>
																					
										</tr>
										<?
											}
										?>
									<?
									}
									else
									{
									?>
									<tr bgcolor="white">
										<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>	
									<?
									}?>
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
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
