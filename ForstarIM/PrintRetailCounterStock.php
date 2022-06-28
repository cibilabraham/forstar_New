<?php
	require("include/include.php");

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);;
	
	#List all Records
	$retailCounterStockRecords = $retailCounterStockObj->fetchAllDateRangeRecords($fromDate, $tillDate);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Retail counter stock</td>
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
								<td colspan="2" style="padding:10px;">
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($retailCounterStockRecords)>0) {
		$i = 0;
	?>
	<thead>
	<tr align="center" bgcolor="#f2f2f2">		
		<th class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Date</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Retail Counter</th>
	</tr>
	</thead>
	<tbody>
	<?
	foreach ($retailCounterStockRecords as $rcs) {
		$i++;
		$retailCounterStockId	= $rcs[0];
		$selectedDate	= dateFormat($rcs[1]);
		$distributorName = $rcs[4];
		$retailCounterName = $rcs[5];	
	?>
	<tr bgcolor="White">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selectedDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$distributorName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$retailCounterName;?></td>		
	</tr>
	<?
		}
	?>
	<?
		} else {
	?>
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</tbody>
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
