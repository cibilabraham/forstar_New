<?php
	require("include/include.php");
	
	#List All Records
	
	
	$harvestingEquipmentMasterRecords	=	$harvestingEquipmentMasterObj->fetchAllRecords();
	$harvestingEquipmentMasterRecordSize		=	sizeof($harvestingEquipmentMasterRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Area Master</td>
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
								<td colspan="2" >
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($harvestingEquipmentMasterRecordSize) {
			$i	=	0;
		?>
		<tr  bgcolor="#f2f2f2" align="center">
												<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Code</td>-->
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Name of Equipment</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Tare Wt</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Equipment Type </th>
			</tr>
			<?
	foreach($harvestingEquipmentMasterRecords as $cr) {
		$i++;
		 $harvestingEquipmentMasterId		=	$cr[0];
		 $equipmentName		=	stripSlash($cr[1]);
		 $tarWt	=	stripSlash($cr[2]);
		 $equipment		=	stripSlash($cr[3]);
		 
		 $equipmentTypeRec=$harvestingEquipmentMasterObj->fetchEquipmentType($equipment);
		 $equipmentType=$equipmentTypeRec[1];
		 $active=$cr[4];
		$existingrecords=$cr[5];
	?>
			<tr  bgcolor="WHITE">				
				<!--<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$areaCode;?></td>-->
				<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$equipmentName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$tarWt;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$equipmentType;?></td>
		</tr>
		
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<?
		}
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="2"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
