<?php
	require("include/include.php");

	$distFilterId = $g["selFilter"];
	#List All Retail Counter records
	$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecords($distFilterId);
	$retailCounterRecordSize  = $retailCounterResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Retail Counter Master</td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;">
<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($retailCounterRecordSize) {
			$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Code</td>-->
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Name</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Contact<br> Person</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="2">Serviced by</td>
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Sales Staff</td>-->
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">State</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">City</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Operational<br> Area</td>		
	</tr>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Sales Staff</td>	
	</tr>
	<?	
	$prevDistributorId = "";
	$prevSalesStaffId	= "";
	$prevStateId		= "";	
	$prevCityId		= "";	
	while ($dr=$retailCounterResultSetObj->getRow()) {
		$i++;
		$retailCounterId	= $dr[0];
		$retailCounterCode 	= stripSlash($dr[1]);
		$retailCounterName 	= stripSlash($dr[2]);
		$contactPerson		= stripSlash($dr[3]);
		$distributorId		= $dr[14];
		$distributorName = "";
		if ($prevDistributorId!=$distributorId) {
			$distributorName	= stripSlash($dr[15]);
		}
		$salesStaffId	= $dr[16];
		$salesStaffName = "";
		if ($prevSalesStaffId!=$salesStaffId) {
			$salesStaffName = stripSlash($dr[17]);
		}
		
		$stateId	= $dr[5];
		$stateName 	= "";
		if ($prevStateId!=$stateId) {
			$stateName	= stripSlash($dr[18]);	
		}

		$cityId		= $dr[6];
		$cityName 	= "";
		if ($prevCityId!=$cityId) {
			$cityName	= stripSlash($dr[19]);	
		}

		# get Operational Area Records
		$selAreaRecords = $retailCounterMasterObj->getOperationalAreaRecords($retailCounterId);
	?>
<tr  bgcolor="WHITE">	
	<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$retailCounterCode;?></td>-->
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$retailCounterName;?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$contactPerson;?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$salesStaffName;?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$stateName;?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$cityName;?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($selAreaRecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach ($selAreaRecords as $areaR) {
							$j++;
							$areaName = $areaR[1];
							$nextRec++;
				?>
				<td class="listing-item" valign="top">
					<? if($nextRec>1) echo ",";?><?=$areaName?></td>
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
			$prevDistributorId	= $distributorId;
			$prevSalesStaffId	= $salesStaffId;
			$prevStateId		= $stateId;
			$prevCityId		= $cityId;
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
