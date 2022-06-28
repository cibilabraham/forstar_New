<?
	require("include/include.php");

	# List all Departments 
	$driverMasterRecords	=	$driverMasterObj->fetchAllRecords();
	$driverMasterSize		=	sizeof($driverMasterRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Driver Master</td>
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
			if (sizeof($driverMasterRecords) > 0) {
				$i	=	0;
			?>
											<tr  bgcolor="#f2f2f2" align="center">
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Name of Person</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Permanent Address</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Present address </th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Telephone no </th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Mobile no </th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Driving Licence no </th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Licence Expiry Date </th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Vehicle Type </th>
</tr>
<?
	foreach($driverMasterRecords as $cr) {
		$i++;
		 $driverMasterId		=	$cr[0];
		 $name		=	stripSlash($cr[1]);
		 $permanentAddress	=	stripSlash($cr[2]);
		 $presentAddress		=	stripSlash($cr[3]);
		 $telephoneNo		=	stripSlash($cr[4]);
		 $mobileNo		=	stripSlash($cr[5]);
		 $drivingLicenceNo		=	stripSlash($cr[6]);
		 $licenceExpiryDate		=	stripSlash($cr[7]);
		 $vehicleType		=	$driverMasterObj->getVehicleType($driverMasterId);
		 $active=$cr[8];
		$existingrecords=$cr[9];
	?>
	<tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$permanentAddress;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$presentAddress?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$telephoneNo?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$mobileNo?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$drivingLicenceNo?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$licenceExpiryDate?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
		

				<?php
							$numLine = 3;
							if (sizeof($vehicleType)>0) {
								$nextRec = 0;						
								foreach ($vehicleType as $cR) {					
									$type = $cR[1];
									$vehicle=$driverMasterObj->getVehicleTypeName($type);
									$vehicleType1=$vehicle[0];
									$nextRec++;
									if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $vehicleType1;
									if($nextRec%$numLine == 0) echo "<br/>";	
								}
							}
							?>				
		
		</td>
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
