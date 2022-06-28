<?
	require("include/include.php");

	# List all Departments 
	$vehicleMasterRecords	=	$vehicleMasterObj->fetchAllRecords();
	$vehicleMasterSize		=	sizeof($vehicleMasterRecords);
	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Vehicle Master</td>
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
			if (sizeof($vehicleMasterRecords) > 0) {
				$i	=	0;
			?>
											<tr  bgcolor="#f2f2f2" align="center">
											<th class="listing-head" style="padding-left:5px; padding-right:5px;">Vehicle Number</th>
											<th class="listing-head" style="padding-left:5px; padding-right:5px;">Vehicle Type</th>
											<th class="listing-head" style="padding-left:5px; padding-right:5px;">Registration Number</th>
											<th class="listing-head" style="padding-left:5px; padding-right:5px;">Description</th>
											<th class="listing-head" style="padding-left:5px; padding-right:5px;">Current Status</th>

											<!--<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Harvesting Equipment </th>
											<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Equipment Quantity </th>
											<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Harvesting Chemical </th>
											<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Chemical Quantity</th>-->
</tr>
<?
	foreach($vehicleMasterRecords as $cr) {
		$i++;
		 $vehicleMasterId		=	$cr[0];
		 $vehicleNumber		=	stripSlash($cr[1]);
		 $vehicleType       =	stripSlash($cr[2]);
		 $vehicleTypeRec=$vehicleMasterObj->fetchVehicleType($vehicleType);
		 $vehicleTypeName=$vehicleTypeRec[1];
		 $registrationNumber       =	stripSlash($cr[3]);
		 $description       =	stripSlash($cr[4]);
		 $currentstatus       =	stripSlash($cr[5]);
		//****************************Harvesting Equipment & Chemical
		// $harvestingEquipment= $vehicleMasterObj->getharvestingEquipment($vehicleMasterId);
		 //$harvestingChemical= $vehicleMasterObj->getharvestingChemical($vehicleMasterId);
		 //$vehicleType		=	$driverMasterObj->getVehicleType($driverMasterId);
		 
	?>
	<tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$vehicleNumber;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$vehicleTypeName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$registrationNumber;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?if ($currentstatus=="0")
		{
		echo "FREE";
		}
		else
		{
		echo "BLOCKED";
		}
		?>
		</td>
		<!------------------------------------------Harvesting Equipment & Chemical----------------------------------------->
		<?php/*
		<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($harvestingEquipment)>0) {
				$nextRec = 0;						
				foreach ($harvestingEquipment as $cR) {					
					$equipment = $cR[1];
					$harvestEquipment=$vehicleMasterObj->getEquipmentName($equipment);
					$harvestEquipmentName=$harvestEquipment[0];
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $harvestEquipmentName;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>	
		
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($harvestingEquipment)>0) {
				$nextRec = 0;						
				foreach ($harvestingEquipment as $cR) {					
					$quantity = $cR[2];
					
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $quantity;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>	
		
		</td>
		
		<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($harvestingChemical)>0) {
				$nextRec = 0;						
				foreach ($harvestingChemical as $cR) {					
					$chemical = $cR[1];
					$harvestChemical=$vehicleMasterObj->getChemicalName($chemical);
					$harvestChemicalName=$harvestChemical[0];
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $harvestChemicalName;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>	
		
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($harvestingChemical)>0) {
				$nextRec = 0;						
				foreach ($harvestingChemical as $cR) {					
					$chemical = $cR[2];
					
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $chemical;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>	
		
		</td>
			*/?>
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
