<?
	require("include/include.php");

	$dateFrom = $g["frozenPackingFrom"];
	$dateTill = $g["frozenPackingTill"];
	$fromDate = mysqlDateFormat($dateFrom);
	$tillDate = mysqlDateFormat($dateTill);
	#List All Packing Goods
	$dailyFrozenPackingRecs	=	$dailyfrozenpackingObj->getDFPForDateRange($fromDate, $tillDate);
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Frozen Packing</td>
								</tr>
								
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>								
								<tr>
									<td width="1" ></td>
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($dailyFrozenPackingRecs)>0) {
		$i	=	0;
	?>	 
	<tr  bgcolor="#f2f2f2" align="center">	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">RM Lot Id</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Process Code</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Stage</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Code</td>
	<!--<td class="listing-head" style="padding-left:5px;padding-right:5px;">For Pkg</td>-->		
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">No.of<br> MCs</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">MC Pkg</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Qty</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Pkd Qty</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">RM Used</td>
	<!--<td class="listing-head" style="padding-left:5px;padding-right:5px;">Available Qty</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Lot Ids</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Allocated</td>-->		
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">View</td>
	</tr>
	<?php
		//$editCriteria = "";
		$totalPkdQty = 0;
		$totalFrozenQty = 0;
		$totalActualQty = 0;
		$totNumMCs = 0;
		foreach ($dailyFrozenPackingRecs as $dfpr) {
			$i++;
			$dailyFrozenPackingMainId	= $dfpr[0];
			//$dailyFrozenPackingEntryId	= $dfpr[4];
			
			$selProcessCodeId	= $dfpr[3];
			$selProcessCode		= $dfpr[6];	
			$selFreezingStageId	= $dfpr[4];
			$selFreezingStage	= $dfpr[7];
			$selFrozenCodeId	= $dfpr[5];
			$selFrozenCode		= $dfpr[8];
			$selMCPackingId		= $dfpr[11];
			if($dfpr[21]!='0')
			{
				$rmLotIDNm			= $dfpr[21];
			}
			else
			{
				$rmLotIDNm			='';
			}
			$rmLotID			= $dfpr[22];
			
			# MC Pkg Recs
			$mcPkgRecs	= $dailyfrozenpackingObj->getMCPkgRecs($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId);

			# Frozen Lot Ids
			$frznLotIds	= $dailyfrozenpackingObj->getFrznLotIds($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId);

			/*
			$unitId			= $dfpr[2];
			$lotId = "";
			if ($dfpr[3]!=0) $lotId	=	$dfpr[3];
			$fish	=	$fishmasterObj->findFishName($dfpr[5]);
			$processCode = $processcodeObj->findProcessCode($dfpr[6]);
			$freezingStage = $freezingstageObj->findFreezingStageCode($dfpr[7]);
			$eUCode = $eucodeObj->findEUCode($dfpr[8]);
			$brand = $brandObj->findBrandCode($dfpr[9]);
			$frozenCode = $frozenpackingObj->findFrozenPackingCode($dfpr[10]);
			$mCPackingCode = $mcpackingObj->findMCPackingCode($dfpr[11]);
			$exportLotId	=	$dfpr[12];
			*/
			
			$reportConfirmed = 	$dfpr[14];

			$disabled = "";	
			if ($reportConfirmed=='Y' && $reEdit==false) {
				$disabled = "disabled";
			}
			#Find Number of packing Details
			//list($numOfMC, $numLooseSlab) = $dailyfrozenpackingObj->getNumOfPacking($dailyFrozenPackingEntryId);
			
			if($rmLotID=="0")
			{
				list($pkdQty, $numMCs, $frozenQty, $actualQty) = $dailyfrozenpackingObj->getPkdQty($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selMCPackingId);
			}
			else
			{
				list($pkdQty, $numMCs, $frozenQty, $actualQty) = $dailyfrozenpackingObj->getPkdQtyRmlotId($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selMCPackingId,$rmLotID);
			}
			
			//list($pkdQty, $numMCs, $frozenQty, $actualQty) = $dailyfrozenpackingObj->getPkdQty($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId);

			$totalPkdQty += $pkdQty;
			$totalFrozenQty += $frozenQty;
			$totalActualQty += $actualQty;
			$totNumMCs += $numMCs;

			# Edit criteria
			$selEditCriteria	= "$selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selProcessCode - $selFreezingStage - $selFrozenCode";
	?>
	<tr bgcolor="WHITE">
	<!--<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyFrozenPackingMainId;?>" class="chkBox">-->
		<!--<input type="hidden" name="dailyFrozenPackingEntryId_<?=$i;?>" value="<?=$dailyFrozenPackingEntryId?>">-->
	<!--</td>-->
	
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$rmLotIDNm?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selProcessCode?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selFreezingStage?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selFrozenCode?></td>
	<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=$numOfMC?></td>-->	
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($numMCs!=0)?$numMCs:"";?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;">
				<?php
					$numCol = 3;
					if (sizeof($mcPkgRecs)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($mcPkgRecs as $r) {							
							$selName = $r[1];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) {
								echo "<br/>";
							}
						}
					}
				?>
	</td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($frozenQty!=0)?$frozenQty:"";?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($pkdQty!=0)?$pkdQty:"";?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($actualQty!=0)?$actualQty:"";?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;">
				<?php
					$numCol = 3;
					if (sizeof($frznLotIds)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($frznLotIds as $r) {							
							$selName = $r[1];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) {
								echo "<br/>";
							}
						}
					}
				?>
	</td>	
	<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?//=$eUCode;?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?//=$brand?></td>-->		
	</tr>
	<?php
		} // Main loopEnds here
	?>	
	<tr bgcolor="White">
		<TD colspan="4" class="listing-head" align="right" style="padding-left:5px;padding-right:5px;">Total:</TD>
		<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
			<strong><?=$totNumMCs?></strong>
		</td>
		<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
			&nbsp;
		</td>
		<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
			<strong><?=number_format($totalFrozenQty,2,'.','');?></strong>
		</td>
		<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
			<strong><?=number_format($totalPkdQty,2,'.','');?></strong>
		</td>
		<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
			<strong><?=number_format($totalActualQty,2,'.','');?></strong>
		</td>
		<TD colspan="5" class="listing-head" align="right" style="padding-left:5px;padding-right:5px;"></TD>
	</tr>
	<?
		} else 	{
	?>
	<tr bgcolor="white">
	<td colspan="11"  class="err1" height="11" align="center"><?=$msgNoRecords;?></td>
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
