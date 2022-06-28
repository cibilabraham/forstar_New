<?php
	require("include/include.php");

	#List All Records
	$assignRtCtDisChargeResultSetObj = $assignRtCtDisChargeObj->fetchAllRecords();
	$assignRtCtDisChargeRecordSize	 = $assignRtCtDisChargeResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Assign Display Charge Master</td>
							</tr>
							<tr>
								<td colspan="3" height="15" ></td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;">
<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($assignRtCtDisChargeRecordSize) {
			$i = 0;
	?>

	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Retail Counter</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Dis.Charge<br>(Rs.)</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Period</td>		
	</tr>
	<?	
	while ($adc=$assignRtCtDisChargeResultSetObj->getRow()) {
		$i++;
		$assignRtCtDisChargeId		= $adc[0];
		$retailCounterName		= $adc[6];
		$rtCtDisplayCharge		= $adc[2];
		$disChargeType			= $adc[3];
		$fromDate			= dateFormat($adc[4]);
		$tillDate			= dateFormat($adc[5]);

		$disPeriod	= "";
		if ($disChargeType=='M') {
			$disPeriod = "Per Month";
		}
		else if ($disChargeType=='D') {
			$disPeriod = "From:$fromDate To:$tillDate";
		}
		
	?>
	<tr  bgcolor="WHITE">				
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$retailCounterName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$rtCtDisplayCharge;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$disPeriod;?></td>		
	</tr>
	<?
	
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">

		<?
			} else {
		?>
	<tr bgcolor="white">
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
