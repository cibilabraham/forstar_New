<?
	require("include/include.php");

	$selDate	= 	$g["selDate"];
	$offset		=	$g["offset"];
	$limit		=	$g["limit"];
	
	
	#List All Records
	if($selDate) {
		$searchDate	=	mysqlDateFormat($selDate);

		$dailyFreezingChartRecords	=	$dailyFreezingChartObj->fetchPagingActivityChartRecords($searchDate, $offset, $limit);
		$dailyActivityChartRecordSize	=	sizeof($dailyFreezingChartRecords);
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Daily Freezing Chart</td>
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
				if (sizeof($dailyFreezingChartRecords) > 0) {
					$i	=	0;
				?>	
	<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Sl.<br>No </td>
<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">P.F.No/ B.F.No </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Start </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Stop </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Time<br> Diff</td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Core<br> Temp </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Unloading </td>

<? if($edit==true){?>
<td class="listing-head" width="45" rowspan="2"></td>
<? }?>
</tr>
<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Time </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Temp </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Time </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Temp </td>
</tr>

			<?
			foreach($dailyFreezingChartRecords as $dacr)
			{
				$i++;
				$dailyFreezingChartMainId	=	$dacr[0];
				$dailyFreezingChartEntryId	=	$dacr[1];
				$selFreezerId			=	$dacr[4];
				$freezerNo			=	$freezercapacityObj->findFreezer($selFreezerId);
				
				$startTime			=	$dacr[5];
				$startTemp			=	$dacr[6];
				$stopTime			=	$dacr[7];
				$stopTemp			=	$dacr[8];
				$coreTemp			=	$dacr[9];
				$unloadTime			=	$dacr[10];
				
				#----------------------------------------------------
				//Calculating difference between Start and Stoptime
				$freezerTime  = $freezercapacityObj->getFreezerTime($selFreezerId);

				list($startTimeHour, $startTimeMints, $startTimeOption) = explode("-", $startTime);
				$parseStartTime = "$startTimeHour"."-"."$startTimeMints";
				$startTimeStamp = getTimeStamp($parseStartTime); //From Config File

				list($stopTimeHour, $stopTimeMints, $stopTimeOption) = explode("-", $stopTime);
				$parseStopTime 	= "$stopTimeHour"."-"."$stopTimeMints";
				$stopTimeStamp = getTimeStamp($parseStopTime);
				$mode='H';				
				$workedTime = abs(dateDiff($startTimeStamp, $stopTimeStamp, $mode));
				$timeDiff = abs($freezerTime - $workedTime);				
				//echo "$freezerTime-$workedTime<br>";
				$displayDiffTime = "";
				if ($freezerTime<$workedTime) {
					$displayDiffTime = "<span style=\"color:#FF0000\">"."+".$workedTime."</span>";
				} else if ($freezerTime>$workedTime &&  $workedTime!=0) {
					$displayDiffTime = "-".$workedTime;
				} else {
					$displayDiffTime = "";
				}
				//---------------------------------------------------
			?>
<tr  bgcolor="WHITE"  >
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$i;?></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$freezerNo;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$startTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$startTemp;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$stopTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$stopTemp;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$displayDiffTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$coreTemp;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$unloadTime;?></td>
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
												<td colspan="15"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
