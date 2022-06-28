<?php
	require("include/include.php");

	#List All Records	
	$zoneRecords	= $zoneMasterObj->fetchAllRecords();
	$zoneRecordSize  = sizeof($zoneRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Zone Master</td>
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
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($zoneRecordSize) {
			$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Area Demarcation</td>	
	</tr>
	<?php
	foreach ($zoneRecords as $zr) {
		$i++;
		$zoneId	= $zr[0];		
		$name 	= stripSlash($zr[2]);
		# Get selected Area's
		$getselAreaRecs = $zoneMasterObj->getAreaDemarcationRecs($zoneId);
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>	
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getselAreaRecs)>0) {
						$nextRec	=	0;
						$k=0;
						$cityName = "";
						foreach ($getselAreaRecs as $cR) {
							$j++;
							$selName = $cR[0];
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
			}
		?>		
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
