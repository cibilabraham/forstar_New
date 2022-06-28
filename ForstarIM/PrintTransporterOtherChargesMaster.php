<?php
	require("include/include.php");

	$transporterFilterId 		= $g["transporterFilter"];
	$transporterRateListFilterId 	= $g["transporterRateListFilter"];	
	#List All Records
	$transporterRateRecords = $transporterOtherChargesObj->fetchAllRecords($transporterFilterId, $transporterRateListFilterId);
	$transporterRateRecordSize = sizeof($transporterRateRecords);

	# Get All Wt Slab Records
	//$weightSlabRecords = $weightSlabMasterObj->fetchAllRecords();	
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="95%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Other Charges Master</td>
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
	<td colspan="2" style="padding-left:3px; padding-right:3px;font-size:9px;">
	<table cellpadding="2"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($transporterRateRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
	<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" rowspan="2">Transporter</td>	
	<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" colspan="5">Other Charges</td>	
	</tr>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">FOV (%)</td>
		<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Docket (Rs.)</td>
		<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Service Tax (%)</td>
		<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Octroi Service (%)</td>
		<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">ODA Charge (Rs.)</td>
	</tr>
	<?php
		foreach ($transporterRateRecords as $trr) {
			$i++;
			$transporterOtherChargeId 	= $trr[0];
			$transporterId 		= $trr[1];

			$transporterName = "";
			if ($prevTransporterId!=$transporterId) {
				$transporterName = $trr[3];
			}
			
			$fovCharge		= $trr[4];
			$docketCharge		= $trr[5];
			$serviceTax		= $trr[6];
			$octroiServiceCharge 	= $trr[7];
			$odaCharge 		= $trr[8];
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true"><?=$transporterName;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$fovCharge;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$docketCharge;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$serviceTax;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$octroiServiceCharge;?></td>	
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=($odaCharge!=0)?$odaCharge:"";?></td>	
	</tr>
	<?php
		$prevTransporterId = $transporterId;		
		}
	?>

											<?
												}
												else
												{
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
