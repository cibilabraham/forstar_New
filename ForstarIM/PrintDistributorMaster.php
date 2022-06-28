<?php
	require("include/include.php");

	$selStateFilterId	= $g["selStateFilter"];
	$selCityFilterId	= $g["selCityFilter"];	

	#List All Records
	$distributorResultSetObj = $distributorMasterObj->filterDistributorMasterRecords($selStateFilterId, $selCityFilterId);
	$distributorRecordSize	 = $distributorResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Master</td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;">
<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($distributorRecordSize) {
			$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;">Code</td>-->
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Contact<br>Person</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Contact<br>No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Cities</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">States</td>		
	</tr>
	<?php	
	while ($dr=$distributorResultSetObj->getRow()) {
		$i++;
		$distributorId	 = $dr[0];
		$distributorCode = stripSlash($dr[1]);
		$distributorName = stripSlash($dr[2]);	
		$contactPerson	= stripSlash($dr[3]);
		$contactNo	= stripSlash($dr[5]);
		# Get Selected State Records
		$getSelStateRecords = $distributorMasterObj->getSelectedStateRecords($distributorId);
		# Get City Records
		$getSelCityRecords = $distributorMasterObj->getSelCityRecords($distributorId);
	?>
<tr  bgcolor="WHITE">
	<!--<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$distributorCode;?></td>-->
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$distributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$contactPerson;?></td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$contactNo;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getSelCityRecords)>0) {
						$nextRec	=	0;
						$k=0;
						$cityName = "";
						foreach ($getSelCityRecords as $cR) {
							$j++;
							$cityName = $cR[1];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$cityName?></td>
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
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getSelStateRecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach ($getSelStateRecords as $sR) {
							$j++;
							$stateName = $sR[1];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$stateName?></td>
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
		<input type="hidden" name="editId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
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
