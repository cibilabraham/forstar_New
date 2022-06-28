<?php
	require("include/include.php");

	$fznPkngRateRecords = $frznPkgRatingObj->fetchAllRecords();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;FROZEN PACK RATE</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;">
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
										<?
										if (sizeof($fznPkngRateRecords)>0) {
											$i	=	0;
										?>	
										<tr  bgcolor="#f2f2f2" align="center">	
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Name</td>	
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Process Code</td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Code</td>
											<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Stage</td>
										</tr>
										<?
										foreach ($fznPkngRateRecords as $fpqel) 
										{
											$i++;
											$frznCodeRate = $fpqel[0];
											$qEntryName	 = $fpqel[1];
											//echo $qEntryName;
											$sFrozenCode = $frozenpackingObj->findFrozenPackingCode($fpqel[3]);
											$sFreezingStage = $freezingstageObj->findFreezingStageCode($fpqel[2]);
											/*
											$sFishName	 = $fishmasterObj->findFishName($fpqel[2]);
											$sProcessCode = $processcodeObj->findProcessCode($fpqel[3]);
											$frozenLotId = "";
											if ($fpqel[3]!=0) $frozenLotId	=	$fpqel[3];
											$eUCode = $eucodeObj->findEUCode($fpqel[8]);
											$brand = $brandObj->findBrandCode($fpqel[9]);
											$mCPackingCode = $mcpackingObj->findMCPackingCode($fpqel[11]);
											$exportLotId	=	$fpqel[12];
											*/
													
											# Get Selected Process Coes
											$getProcessCodeRecs = $frznPkgRatingObj->getProcessCodeRecs($frznCodeRate);

											$rowColor = "WHITE";
											$displayToolTip = "";
											$displayRowStyle = "";
											# ------- checkng grade list correct/not ---------------------
											# Check any Grade Inserted entry
											$selGradeRecords = $frznPkgRatingObj->getSelGradeRecords('', $frznCodeRate);
											# Default Process Code recs
											$selDefaultPCWiseGradeRecs = $frznPkgRatingObj->getDefaultGradeRecs($frznCodeRate);
											$gradeDiffSize = $frznPkgRatingObj->getGradeRecDiffSize('', $frznCodeRate);		
										?>
										<tr  bgcolor="WHITE"  >	
											<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$qEntryName?></td>
											<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFishName;?></td>-->
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
											<?php
												$numCol = 6;
												if (sizeof($getProcessCodeRecs)>0) {
													$nextRec = 0;
													$pcName = "";
													foreach ($getProcessCodeRecs as $cR) {
														$pcName = $cR[1];
														$nextRec++;
														if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $pcName;
														if($nextRec%$numCol == 0) echo "<br/>";
													}
												}						
											?>
											</td>
											<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFrozenCode?></td>
											<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFreezingStage?></td>
										</tr>
										<?
										}
										?>
										<?
											} else 	{
										?>
										<tr bgcolor="white">
											<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
										}
										?>
										<input type="hidden" name="allocateMode" value="<?=$allocateMode?>">
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
