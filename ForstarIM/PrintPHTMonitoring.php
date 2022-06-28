<?
	require("include/include.php");

	# List all PHT Montoring 
	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$phtMonitoringRecords = $phtMonitorngObj->fetchAllDateRangeRecords($fromDate, $tillDate);
		$phtMonitoringSize		=	sizeof($phtMonitoringRecords);	
	}
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;PHT Monitoring</td>
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
								<td colspan="2" >
									<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
			<?
			if ($phtMonitoringSize > 0) {
				$i	=	0;
			?>
				<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Date</td>
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">PHT Certificate No</td>
		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Group</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Species</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supply Qty</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM Lot ID</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">PHT Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Set off Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Balance Qty</td>
</tr>
<?
	foreach($phtMonitoringRecords as $sir) {
		$i++;
		$phtMonitoringId	=	$sir[0];
		$date		=	dateFormat($sir[1]);
		$selLotId = $sir[2];
		$supplierId=$sir[3];
		$supplier=$sir[7];
		$supplierGroupName=$sir[8];
		$speciousId		=	$sir[5];
		$specious	=	$sir[9];
		$supplyQty		=	$sir[6];
		$phtcetificate=$sir[10];
		$phtcert=$phtMonitorngObj->getCertificateQuantity($sir[0]);
	?>
	<tr  bgcolor="WHITE">
	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$date;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$phtcetificate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplier;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierGroupName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$specious;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplyQty;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[3];
		echo '<br/>';
		}
		?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[0];
		echo '<br/>';
		}
		?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[1];
		echo '<br/>';
		}
		?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[2];
		echo '<br/>';
		}
		?> 
		</td>
	</tr>
	<?
		}
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
