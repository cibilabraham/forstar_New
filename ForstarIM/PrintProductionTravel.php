<?php
	require("include/include.php");

	$selRateList = $g["selRateList"];

	#List All Records
	$travelCostResultSetObj = $productionTravelObj->fetchAllRecords($selRateList);
	$travelCostRecordSize	= $travelCostResultSetObj->getNumRows();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="85%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Travel Cost Master</td>
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
		<td colspan="2" style="padding-left:10px;padding-right:10px;">
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($travelCostRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">									<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Actual</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Ideal</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Unit Cost</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Total Cost</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Actual Cost</td>
	</tr>
			<?
			while(($tcr=$travelCostResultSetObj->getRow())) {
				$i++;
				$travelCostRecId 	= $tcr[0];
				$headName	= stripSlash($tcr[1]);
				$mcActualUnit	= $tcr[2];
				$mcIdealUnit	= $tcr[3];		
				$mcPuCost	= $tcr[4];
				$mcTotCost	= $tcr[5];
				$mcAvgCost	= $tcr[6]; 	
			?>
											<tr  bgcolor="WHITE">
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$headName;?></td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$mcActualUnit;?></td>
												<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcIdealUnit?></td>
<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcPuCost?></td>
<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcTotCost?></td>
<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcAvgCost?></td>
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
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
