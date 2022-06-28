<?php
	require("include/include.php");

	# List all Records
	$transporterWeightSlabRecords = $transporterWeightSlabObj->fetchAllRecords();	
	$transporterWeightSlabRecordSize = sizeof($transporterWeightSlabRecords);	

?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Wise Weight Slab</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							<?php
								if($errDel!="") {
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
	<table cellpadding="2"  width="98%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($transporterWeightSlabRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">	
	<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" >Transporter</td>	
	<td class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" >Weight Slab</td>	
	</tr>	
	<?php
		foreach ($transporterWeightSlabRecords as $twsr) {
			$i++;
			$transporterWeightSlabId = $twsr[0];
			$transporterId 		= $twsr[1];

			$transporterName = "";
			if ($prevTransporterId!=$transporterId) {
				$transporterName = $twsr[2];
			}
			
			# Get Wt Slab Records
			$getWtSlabRecs = $transporterWeightSlabObj->getSelWtSlabRecs($transporterWeightSlabId);
			
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true"><?=$transporterName;?></td>			
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true">
			<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getWtSlabRecs)>0) {
						$nextRec	=	0;
						$k=0;
						$cityName = "";
						foreach ($getWtSlabRecs as $cR) {
							$j++;
							$selName = $cR[2];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$selName?></td>
						<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php
							}	
					 	}
					}
				?>
				</tr>
		</table>
		</td>	
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
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
