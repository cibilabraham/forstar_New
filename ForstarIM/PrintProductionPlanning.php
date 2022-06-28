<?php
	require("include/include.php");
	
	$dateFrom = mysqlDateFormat($g["selectFrom"]);
	$dateTill = mysqlDateFormat($g["selectTill"]);
	#List All Fishes
	$productionPlanningRecords	=	$productionPlanningObj->fetchDateRangeRecords($dateFrom, $dateTill);
	$productionPlanningRecSize	=	sizeof($productionPlanningRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Production Planning From:<?=dateFormat($dateFrom);?> to:<?=dateFormat($dateTill);?></td>
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
								<td colspan="2" style="padding-left:5px;padding-right:5px;">
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($productionPlanningRecords)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Pouches per Batch</td>		
	</tr>
		<?
		foreach ($productionPlanningRecords as $ppr) {
			$i++;
			$productionPlanId	= $ppr[0];
			$plannedDate		= dateFormat($ppr[1]);
			$productName		= $ppr[5];
			$numPouch		= $ppr[6];			
		?>
		<tr  bgcolor="WHITE">			
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$plannedDate;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
				<?=$numPouch?>
			</td>			
		</tr>
		<?
			}
		?>
			<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
			<input type="hidden" name="editId" value="<?=$editId?>">
			<input type="hidden" name="editSelectionChange" value="0">
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
