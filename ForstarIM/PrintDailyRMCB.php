<?php
	require("include/include.php");

	$recordsFilterId				= $g["selFishFilter"];
	$recordsDate					= $g["selFilterDate"];
	$supplierFilterId 				= $g["supplierFilter"];
	$dailyRMCBRecords = $dailyRMCBObj->fetchAllRecords($recordsFilterId, $recordsDate);	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily RM Closing Balance On <?=dateFormat($recordsDate);?></td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;">
									<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if (sizeof($dailyRMCBRecords)>0) {
										$i	=	0;
									?>		
										<tr  bgcolor="#f2f2f2" align="center"  >		
											<!--<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Date</td>-->
											<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Company</td>		
											<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Unit </td>
											<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>		
											<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Process<br/> Code </td>
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Pre-Process<br/> CS (Kg)</td>
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Production<br/> CS (Kg)</td>			
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Re-Process<br/> CS (Kg)</td>		
										</tr>
										<?php
										$totPrdnCS	= 0;
										$totPPCS	= 0;
										$totRPCS	= 0;
										foreach ($dailyRMCBRecords as $drmcb) {
											//echo "first";
											$i++;
											$companyId=$drmcb[1];
											$unitId=$drmcb[2];
											$companyName=$drmcb[3];
											$unitName=$drmcb[4];
											$selectDate=dateFormat($drmcb[5]);
											$dailyRMCBId	= $drmcb[0];
											$selDate=$drmcb[5];
											$fishRec=$dailyRMCBObj->getFishDetail($companyId,$unitId,$selDate);
										?>
										<tr <?=$listRowMouseOverStyle?>>
											<!--<td width="20" height="1" class="listing-item">
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyRMCBId;?>" class="chkBox">
											</td>-->
											<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$selectDate?></td>-->
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$companyName;?></td>		
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$unitName?></td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishName=$fr[1];
												echo $fishName;
											} 
											?>
											</td>		
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$processCode=$pc[1];
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$processCode?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$preProcessCs=$pc[2];
													$totPPCS+=$preProcessCs;
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$preProcessCs?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$productionCs=$pc[3];
													$totPrdnCS+=$productionCs;
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$productionCs?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$reProcessCs=$pc[4];
													$totRPCS+=$reProcessCs;
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$reProcessCs?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<? if($edit==true){?>
											<td class="listing-item" width="45" align="center" style="padding-left:3px; padding-right:3px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyRMCBId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='DailyRMCB.php';"  ></td>
											<? }?>
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
											<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
