<?php
	require("include/include.php");

	$fznPkngQuickEntryListRecords = $frznPkngQuickEntryListObj->fetchAllRecords();
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;QUICK ENTRY LIST</td>
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
	if (sizeof($fznPkngQuickEntryListRecords)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Name</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Process Code</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Code</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Stage</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap>MC Pkg</td>		
	</tr>
	<?
	foreach ($fznPkngQuickEntryListRecords as $fpqel) {
			$i++;
			$fznPkngQuickEntryListId = $fpqel[0];
			$qEntryName	 = $fpqel[1];
			$sFrozenCode = $frozenpackingObj->findFrozenPackingCode($fpqel[5]);
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
			$mCPackingCode = $mcpackingObj->findMCPackingCode($fpqel[6]);
			# Get Selected Process Coes
			$getProcessCodeRecs = $frznPkngQuickEntryListObj->getProcessCodeRecs($fznPkngQuickEntryListId);
	?>
	<tr  bgcolor="WHITE"  >	
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$qEntryName;?></td>
	<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFishName;?></td>-->
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="left">
		<table>
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($getProcessCodeRecs)>0) {
						$nextRec	=	0;
						$k=0;
						$pcName = "";
						foreach ($getProcessCodeRecs as $cR) {
							$j++;
							$pcName = $cR[1];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$pcName?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php 
						}	
					 }
					}
				?>
				</tr>
		</table>
	</td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFrozenCode?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$sFreezingStage?></td>	
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=$mCPackingCode?></td>	
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
