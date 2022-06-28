<?php
	require("include/include.php");

	#List All Records
	$assignSchemeResultSetObj = $assignSchemeObj->fetchAllRecords();
	$assignSchemeRecordSize	 = $assignSchemeResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Assign Scheme Master</td>
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
<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($assignSchemeRecordSize) {
			$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Scheme</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Category</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">From</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">To</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">State</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">City</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Location</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Distributor</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;">Retailer</td>
	</tr>
	<?	
	while ($asr=$assignSchemeResultSetObj->getRow()) {
		$i++;
		$assignSchemeId		= $asr[0];
		$schemeName		= $asr[10];
		$schemeCategory		= $asr[2];
		$disSchemeCategory 	= "";
		if ($schemeCategory=='C') $disSchemeCategory = "Customer";
		else if ($schemeCategory=='R') $disSchemeCategory = "Retailer"; 
		else if ($schemeCategory=='D') $disSchemeCategory = "Distributor";

		$fromDate	= dateFormat($asr[3]);
		$tillDate	= dateFormat($asr[4]);

		$stateId	= $asr[5];
		$stateRec	= $stateMasterObj->find($stateId);
		$stateName	= ($stateRec[2]!="")?$stateRec[2]:"ALL INDIA";	

		$cityId		= $asr[6];
		$cityRec	= $cityMasterObj->find($cityId);		
		$cityName	= ($cityRec[2]!="")?$cityRec[2]:"ALL CITIES";	

		$areaId		= $asr[7];
		$areaRec	= $areaMasterObj->find($areaId);
		$areaName	= ($areaRec[2]!="")?$areaRec[2]:"ALL LOCATIONS";	

		$distributorId	= $asr[8];
		$distributorRec	= $distributorMasterObj->find($distributorId);
		$distriName	= ($distributorRec[2]!="")?$distributorRec[2]:"ALL DSTRIBUTORS";

		$retailCounterId = $asr[9];
		$retailCounterRec	= $retailCounterMasterObj->find($retailCounterId);
		$retailCounterName	= ($retailCounterRec[2]!="")?$retailCounterRec[2]:"ALL RETAILERS";
		
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$schemeName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$disSchemeCategory;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$fromDate;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$tillDate;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$stateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$cityName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$areaName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$distriName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px;"><?=$retailCounterName;?></td>		
	</tr>
	<?
	
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<?
			} else {
		?>
	<tr bgcolor="white">
		<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
