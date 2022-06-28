<?php
	require("include/include.php");

	#List All Records
	$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecords();
	$salesStaffRecordSize	 = $salesStaffResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Sales Staff Master</td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;">
<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($salesStaffRecordSize) {
			$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">			
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Name</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Designation</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="3">Staff Location</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="3">Operational Details</td>		
	</tr>
	<tr  bgcolor="#f2f2f2" align="center">				
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">State</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Area</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">State</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Area</td>	
	</tr>
	<?	
	$prevStateId	= "";
	$prevCityId 	= "";
	while ($ssr=$salesStaffResultSetObj->getRow()) {
		$i++;
		$salesStaffId	 = $ssr[0];
		$salesStaffCode = stripSlash($ssr[1]);
		$salesStaffName = stripSlash($ssr[2]);	
		$stateId	= $ssr[4];		
		$stateName 	= $ssr[9];
		$cityId		= $ssr[5];		
		$cityName 	= $ssr[10];
		$areaId		= $ssr[11];
		$areaRec	=	$areaMasterObj->find($areaId);		
		$areaName	=	stripSlash($areaRec[2]);
		$opSelStateId	= $ssr[12];
		$stateRec	= $stateMasterObj->find($opSelStateId);		
		$opStateName	= stripSlash($stateRec[2]);
		$opSelCityId	= $ssr[13];	
		$opCityName 	= "";
		if ($opSelCityId==0 && $opSelCityId!="") {
			$opCityName	= "All";	
		} else {
			$cityRec	=	$cityMasterObj->find($opSelCityId);		
			$opCityName	=	stripSlash($cityRec[2]);
		}

		$staffDesignation	= $ssr[14];
		//$areaRec	=	$areaMasterObj->find($editId);		
		//$areaName	=	stripSlash($areaRec[2]);
		# get Working Area Records
		$selAreaRecords = $salesStaffMasterObj->getWorkingAreaRecords($salesStaffId);		
	?>
	<tr  bgcolor="WHITE">				
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$salesStaffName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$staffDesignation;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$stateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$cityName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$areaName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$opStateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$opCityName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
			<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($selAreaRecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach ($selAreaRecords as $areaR) {
							$j++;
							$areaName = "";
							if ($areaR[0]==0) {
								$areaName = "All";
							} else $areaName = $areaR[1];
							$nextRec++;
				?>
				<td class="listing-item">
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
		$prevStateId	= $stateId;
		$prevCityId	= $cityId;
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	
		<?
			} else {
		?>
	<tr bgcolor="white">
		<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
