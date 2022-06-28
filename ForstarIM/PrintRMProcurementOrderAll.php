<?php
	require("include/include.php");
# select record between selected date

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$fetchAllProcurmentRecs = $rmProcurmentOrderObj->fetchAllDateRangeRecords($fromDate, $tillDate);
		$procurmentRecssize	=	sizeof($fetchAllProcurmentRecs);	
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="10" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;
	 RM Procurment Order</td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;" >
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?php
									if($procurmentRecssize > 0 ) {
											$i	=	0;
									?>
									
									<tr  bgcolor="#f2f2f2" align="center">		
										<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;">Entry Date</td>
										<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;">Procument Number</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier Group</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier Name</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Farm Name</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Vehicle Number</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>PROC GATE PASS</td>
									</tr>
									<?php
									foreach ($fetchAllProcurmentRecs as $sir) {
									
										$i++;
										$procurementId	=	$sir[0];
										$procurementNo	=	$sir[2];
										$supplierGroup		=$sir[6]; 
										$supplierData	=	$rmProcurmentOrderObj->getSupplierDetails($procurementId);
										$supplierVal=$supplierData[0][0];
										$supplierGroupData	=	$rmProcurmentOrderObj->getSupplierGroupDetails($supplierVal);
										$supplierGroup		=$supplierGroupData[0][1]; 
										$pondData	=	$rmProcurmentOrderObj->getPondDetails($procurementId);
										$VehicleData	=	$rmProcurmentOrderObj->getVehicleAndDriverDetails($procurementId);
										$vehicleid=$VehicleData[0][1];
										$checkActive=$rmProcurmentOrderObj->checkActiveExist($vehicleid);
										$vehicleValueExist=$checkActive[0];
										$gatePassVal=$rmProcurmentOrderObj->getGatePass($procurementId);
										$equipment= $rmProcurmentOrderObj->getEquipment($sir[0]);
										$chemical= $rmProcurmentOrderObj->getChemical($sir[0]);
										$entryDate		= dateFormat($sir[3]);
										$active=$sir[4];
										$generated=$sir[5];
										$generatedCount = $sir[9];
										$existingrecords=$sir[11];
										$schedule_date=$sir[10];
										
									?>
									<tr  bgcolor="WHITE">
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$entryDate;?></td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$procurementNo;?></td>
										
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierGroup;?></td>
										
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
										 <?php
											$numLine = 3;
											if (sizeof($supplierData)>0) {
												$nextRec = 0;						
												foreach ($supplierData as $cR) 
												{
													$name=$cR[1];
													$address=$cR[2];	
													$nextRec++;
													$detailsvalue="Address:$address<br>"; 
													if($nextRec>1) echo "<br>"; ?> <a onMouseOver="ShowTip('<?=$detailsvalue;?>');" onMouseOut="UnTip();"><?php echo $name;
													if($nextRec%$numLine == 0) echo "<br/>";	
												}
											}
											?>
										</td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
										<?php
											$numLine = 3;
											if (sizeof($pondData)>0) {
												$nextRec = 0;						
												foreach ($pondData as $cR) 
												{
													$supplierPond = $cR[1];
													$alloteName=$cR[2];
													$details="Allotee Name:$alloteName<br>";
													$nextRec++;
													if($nextRec>1) echo "<br>";?><a onMouseOver="ShowTip('<?=$details;?>');" onMouseOut="UnTip();"><? echo $supplierPond;
													if($nextRec%$numLine == 0) echo "<br/>";	
												}
											}
											?>
										</td>
										<?php
											$numLine = 3;
											if (sizeof($VehicleData)>0) {
												$nextRec = 0;						
												foreach ($VehicleData as $vD) {
														$vehicleid = $vD[1];
														//$pond = $cR[3];	
														$vehicleName=$vD[3];
														$driverName=$vD[4];
														if($nextRec==0)
														{
														$driverNametotal=$driverName;
														}
														else{
														$driverNametotal.=','.$driverName;
														}
														$driverNameDetail="Driver:$driverNametotal<br/>";
													$nextRec++;
													if($nextRec>1) 
													if($nextRec%$numLine == 0) ;	
												}
											}
											?>
											<?php
											
											$numLine2 = 3;
											if(sizeof($equipment>0))
											{
												$nextRec2= 0;	
												foreach($equipment as $eqp)
												{
													if($nextRec2=="0")
													{
													$equipmentName=$eqp[3].'('.$eqp[2].')';
													}
													else
													{
													$equipmentName.=','.$eqp[3].'('.$eqp[2].')';
													}
													$vehicleequipmentdetails="Equip:$equipmentName<br/>";
													$nextRec2++;
													if($nextRec2>1) 
													if($nextRec2%$numLine2 == 0) ;
												}
											}
											
											$numLine = 3;
											if(sizeof($chemical>0))
											{
												$nextRec1 = 0;	
												foreach($chemical as $chem)
												{
													if($nextRec1=="0")
													{
													$chemicalName=$chem[3].'('.$chem[2].')';
													}
													else
													{
													$chemicalName.=','.$chem[3].'('.$chem[2].')';
													}
												$vehiclechemicaldetails="Chemicals:$chemicalName<br/>";
												$nextRec1++;
												if($nextRec1>1) 
												if($nextRec1%$numLine == 0) ;
												
												}
											}
										?>	
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" onMouseOver="ShowTip('<?=$driverNameDetail.$vehicleequipmentdetails.$vehiclechemicaldetails;?>');" onMouseOut="UnTip();"><? echo $vehicleName;?>
										</td>
										<td align="center" class="listing-item">
										<?php if ($active==1 && $generated=="0" )
										{
										?>
											<input type="button" value="Generate" onClick="return page('RMProcurmentGatePass.php?procurementId=<?=base64_encode($procurementId)?>');">
											
										<?php
										}
										elseif ($active==1 && $generated=="1")
										{
											echo "Generated";
										}
										else
										{
										}
										?>
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
										<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>	
									<?
									}?>
									</table>
								</td>
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
