<?php
	require("include/include.php");

	# Fetch All Recs
	$fpStkReportGroupListRecs = $fpStkReportGroupListObj->fetchAllRecords();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="20" ></td>
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Report Group List</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr>
									<td width="1" ></td>
	<td colspan="2" style="padding:10px;">
 	<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($fpStkReportGroupListRecs)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">	
		<td class="listing-head" style="padding-left:5px;padding-right:5px;">Name</td>	
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Style</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Stage</td>
	<td class="listing-head" style="padding-left:5px;padding-right:5px;">Quick Entry List</td>
	</tr>
	<?php
	foreach ($fpStkReportGroupListRecs as $srgl) {
			$i++;
			$fPStkReportGroupMainId = $srgl[0];

			$qEntryName	 	= $srgl[1];

			$srgFreezingStyle	= $srgl[5];
			$srgFreezingStage	= $srgl[6];

			# Get Selected Process Coes
			$getQELGroupRecs = $fpStkReportGroupListObj->getQELGroupRecs($fPStkReportGroupMainId);
	?>
	<tr bgcolor="White">	
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$qEntryName?></td>
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$srgFreezingStyle?></td>	
	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$srgFreezingStage?></td>		
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left" nowrap>
		<?php
			$numCol = 6;
			if (sizeof($getQELGroupRecs)>0) {
				$nextRec = 0;
				$pcName = "";
				foreach ($getQELGroupRecs as $cR) {
					$pcName = $cR[1];
					$nextRec++;
					if($nextRec>1) echo ",&nbsp;"; echo $pcName;
					if($nextRec%$numCol == 0) echo "<br/>";
				}
			}						
		?>
	</td>
	</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">	
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="editMode" value="<?=$editMode?>">
	<input type="hidden" name="allocateId" value="<?=$allocateId?>">	
	<?
		} else 	{
	?>
	<tr bgcolor="white">
	<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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