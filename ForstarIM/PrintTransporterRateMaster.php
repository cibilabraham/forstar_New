<?php
	require("include/include.php");

	$transporterFilterId 		= $g["transporterFilter"];
	$transporterRateListFilterId 	= $g["transporterRateListFilter"];
	
	#List All Records
	$transporterRateRecords = $transporterRateMasterObj->fetchAllRecords($transporterFilterId, $transporterRateListFilterId);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Rate Master</td>
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
	<table cellpadding="2"  width="88%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($transporterRateRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
	<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Transporter</td>
	<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Zone</td>
	<!--<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Type</td>-->
	<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Weight Slab&nbsp;/&nbsp;Rate</td>		
	</tr>
	
	<?php
		foreach ($transporterRateRecords as $trr) {
			$i++;
			$transporterRateId 	= $trr[0];
			$transporterId 		= $trr[1];

			$transporterName = "";
			if ($prevTransporterId!=$transporterId) {
				$transporterName = $trr[4];
			}
			$zoneId		= $trr[2];
			$zoneName	= $trr[5];
		
			# Get Wt Slab Records
			//$getWtSlabRecs = $transporterRateMasterObj->getSelWtSlabRecs($transporterRateId);
			# Zone Wise Area Recs
			$displayAreaDemarcation = $transporterRateMasterObj->displayArea($zoneId);

			# Trptr Rate Slab
			 $disTrptrRateSlab	= $transporterRateMasterObj->displayTransporterRate($transporterRateId, $transporterId);
			$disTrptrRateSlab	= str_replace("id=newspaper-b1", "bgcolor=#999999", $disTrptrRateSlab);
			$disTrptrRateSlab	= str_replace("id=ROW_R", "bgcolor=white", $disTrptrRateSlab);
			$rateType		= ($trr[6]=='RPW')?"Rate Per Kg":"Fixed Rate";
			
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true"><?=$transporterName;?></td>			
		<td class="listing-item" style="padding-left:3px; padding-right:3px;">			
			<?=$zoneName;?>			
		</td>
		<!--<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true" align="center"><?=$rateType;?></td>	-->
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true" align="left"><?=$disTrptrRateSlab?></td>		
	</tr>
	<?php
		$prevTransporterId = $transporterId;		
		}
	?>
	
											<?php
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