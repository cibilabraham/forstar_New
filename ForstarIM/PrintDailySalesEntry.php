<?
	require("include/include.php");

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);;
	
	#List all Records
	$dailySalesEntryRecords = $dailySalesEntryObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	$dailySalesEntryRecSize	= sizeof($dailySalesEntryRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="80%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Sales Entry</td>
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
	if (sizeof($dailySalesEntryRecords)>0) {
		$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sales Staff</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Visited Retail<br> Counters</td>			
	</tr>
	<?
	foreach ($dailySalesEntryRecords as $dse) {
		$i++;
		$dailySalesEntryId	= $dse[0];
		$entryDate	= dateFormat($dse[1]);
		$salesStaffName = $dse[3];	
		# get visisted Rt Counter
		$getVisitedRtCounter = $dailySalesEntryObj->getVisitedRtCounter($dailySalesEntryId);
	?>
	<tr  bgcolor="WHITE">	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$entryDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$salesStaffName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getVisitedRtCounter)>0) {
						$nextRec	=	0;
						$k=0;
						foreach ($getVisitedRtCounter as $rtCt) {
							$j++;
							$rtCtName = $rtCt[1];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$rtCtName?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<? 
					}	
						}
					}
				?>
				</tr>
			</table>
		</td>		
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<?
		} else {
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
