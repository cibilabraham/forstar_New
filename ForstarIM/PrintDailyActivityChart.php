<?
	require("include/include.php");

	$selDate	= 	$g["selDate"];
	$offset		=	$g["offset"];
	$limit		=	$g["limit"];
	
	
	#List All Records
	if($selDate) {
		$searchDate	=	mysqlDateFormat($selDate);

		$dailyActivityChartRecords	=	$dailyactivitychartObj->fetchPagingActivityChartRecords($searchDate, $offset, $limit);
		$dailyActivityChartRecordSize	=	sizeof($dailyActivityChartRecords);
	}
	
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Daily Activity Chart</td>
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
									<td colspan="2" >
<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
				<?
				if (sizeof($dailyActivityChartRecords) > 0) {
					$i	=	0;
				?>

	<tr  bgcolor="#f2f2f2" align="center">
	
<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">P.F.No/ B.F.No </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" colspan="2">Start </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" colspan="2">Stop </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Core Temp </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Unloading </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Volt </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Amps </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" colspan="2">Consumption</td>
</tr>
<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Time </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Temp </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Time </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Temp </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Electricity </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Water</td>
</tr>

			<?
			foreach($dailyActivityChartRecords as $dacr)
			{
				$i++;
				$dailyActivityChartMainId	=	$dacr[0];
				$dailyActivityChartEntryId	=	$dacr[1];
				$selFreezerId			=	$dacr[36];
				$freezerNo			=	$freezercapacityObj->findFreezer($dacr[36]);
				
				$startTime			=	$dacr[37];
				$startTemp			=	$dacr[38];
				$stopTime			=	$dacr[39];
				$stopTemp			=	$dacr[40];
				$coreTemp			=	$dacr[41];
				$unloadTime			=	$dacr[42];
				$volt				=	$dacr[43];
				$ampere				=	$dacr[44];
				$consumElectricity		=	$dacr[45];
				$consumWater			=	$dacr[46];

			?>
<tr  bgcolor="WHITE"  >

<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$freezerNo;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$startTime;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$startTemp;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$stopTime;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$stopTemp;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$coreTemp;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$unloadTime;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$volt;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$ampere;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$consumElectricity;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$consumWater;?></td>
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
												<td colspan="13"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
