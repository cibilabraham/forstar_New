<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/


	# Add Vehicle Type Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Vehicle Type
	if ($p["cmdAdd"]!="") {

		$vehicleNumber		=	addSlash(trim($p["vehicleNumber"]));
		$vehicleType		=	addSlash(trim($p["vehicleType"]));
		
		$harvestingEquipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$harvestingChemicalTableRowCount    = $p["hidHarvestingChemicalTableRowCount"];
		$lastId = "";
		
		
		if ($vehicleNumber!="" && $vehicleType!="" ) {
		
			$vehicleMasterRecIns	=	$vehicleMasterObj->addVehicleMaster($vehicleNumber, $vehicleType, $userId);
		
		#Find the Last inserted Id From m_vehicle_master Table
			if ($vehicleMasterRecIns) $lastId = $databaseConnect->getLastInsertedId();
			
		
			
		# Multiple vehicle Chemical Type Adding
			if ($harvestingChemicalTableRowCount>0 ) {
				for ($i=0; $i<$harvestingChemicalTableRowCount; $i++) {
					$status = $p["bStatus_".$i];
					
					if ($status!='N') {
						$HarvestingChemical	= addSlash(trim($p["harvestingChemical_".$i]));
						$harvestingQty			= addSlash(trim($p["Qty_".$i]));
						
						# IF SELECT ALL STATE
						if ($lastId!="" && $HarvestingChemical!="" && $harvestingQty!="") {
							
							$harvestingEquipmentIns = $vehicleMasterObj->addVehicleChemical($lastId, $HarvestingChemical,$harvestingQty);
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here
		
		# Multiple vehicle Harvesting Type Adding
			if ($harvestingEquipmentTableRowCount>0 ) {
				for ($i=0; $i<$harvestingEquipmentTableRowCount; $i++) {
				
					$status = $p["Status_".$i];
					
					if ($status!='N') {
						echo $harvestingEquipment	= addSlash(trim($p["harvestingEquipment_".$i]));
						
						echo $harvestingQty			= addSlash(trim($p["harvestingQty_".$i]));
						
						# IF SELECT ALL STATE
						if ($lastId!="" && $harvestingEquipment!="" && $harvestingQty!="") {
							
							$harvestingEquipmentIns = $vehicleMasterObj->addVehicleEquipment($lastId, $harvestingEquipment,$harvestingQty);
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here
		
			if ($vehicleMasterRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddVehicleMaster);
				$sessObj->createSession("nextPage", $url_afterAddVehicleMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddVehicleMaster;
			}
			$vehicleMasterRecIns		=	false;
		}
	}
		
	# Edit Vehicle Master 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$vehicleMasterRec		=	$vehicleMasterObj->find($editId);
		$vehicleMasterId			=	$vehicleMasterRec[0];
		$vehicleNumber			=	$vehicleMasterRec[1];
		$vehicleType			=    $vehicleMasterRec[2];
		$harvestingEquipmentRec= $vehicleMasterObj->getharvestingEquipment($vehicleMasterId);
		$harvestingChemicalRec= $vehicleMasterObj->getharvestingChemical($vehicleMasterId);
		
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$vehicleMasterId		=	$p["hidVehicleMasterId"];
		$vehicleNumber		=	addSlash(trim($p["vehicleNumber"]));
		$vehicleType	=	addSlash(trim($p["vehicleType"]));
		$equipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$chemicalTableRowCount	= $p["hidHarvestingChemicalTableRowCount"];
		//$vehicleType		=	addSlash(trim($p["vehicleType"]));
		//$vehicleTypeTableRowCount	= $p["hidVehicleTypeTableRowCount"];
		if ($vehicleMasterId!="" && $vehicleNumber!="" && $vehicleType!="" ) {
			$vehicleMasterRecUptd = $vehicleMasterObj->updateVehicleMaster($vehicleMasterId, $vehicleNumber, $vehicleType);
			//$driverMasterRecUptd = $driverMasterObj->updateDriverMaster($driverMasterId, $name, $permanentAddress, $presentAddress,$telephoneNo,$mobileNo,$drivingLicenceNo,$licenceExpiryDate);
			
			# ----------------------------Vehicle equipment
			for ($i=0; $i<$equipmentTableRowCount; $i++) {
				$status 	 	 = $p["Status_".$i];
				$equipmentId  		= $p["equipmentId_".$i];
				if ($status!='N') {
					$equipmentName= addSlash(trim($p["harvestingEquipment_".$i]));
					$equipmentQuantity= addSlash(trim($p["harvestingQty_".$i]));
					
					if ($vehicleMasterId!="" && $equipmentName!="" && $equipmentQuantity!="" && $equipmentId!="") {
					//echo 'hi';
						$updateHarvestingEquipmentRec = $vehicleMasterObj->updateEquipmentQuantity($equipmentId, $equipmentName,$equipmentQuantity);
						
					} else if ($vehicleMasterId!="" && $equipmentName!="" && $equipmentQuantity!="" && $equipmentId=="") {	
						//echo 'test';
						$harvestingEquipmentIns = $vehicleMasterObj->addVehicleEquipment($vehicleMasterId, $equipmentName,$equipmentQuantity);
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $equipmentId!="") {
					# Check Test master In use
					/*$driverMasterInUse = $driverMasterObj->testMethodRecInUse($vehicleTypeId);
					if (!$driverMasterInUse)*/ $delHarvestingEquipmentRec = $vehicleMasterObj->delHarvestingEquipmentRec($equipmentId);
						
				}
			} // Brand Loop ends here
			
			# ----------------------------Vehicle chemical
			for ($i=0; $i<$chemicalTableRowCount; $i++) {
				$status 	 	 = $p["bStatus_".$i];
				$chemicalId  		= $p["chemicalId_".$i];
				if ($status!='N') {
					$chemicalName= addSlash(trim($p["harvestingChemical_".$i]));
					$chemicalQuantity= addSlash(trim($p["Qty_".$i]));
					
					if ($vehicleMasterId!="" && $chemicalName!="" && $chemicalQuantity!="" && $chemicalId!="") {
					//echo 'hi';
						$updateHarvestingChemicalRec = $vehicleMasterObj->updateChemicalQuantity($chemicalId, $chemicalName,$chemicalQuantity);
						
					} else if ($vehicleMasterId!="" && $chemicalName!="" && $chemicalQuantity!="" && $chemicalId=="") {	
						//echo 'test';
						$harvestingChemicalIns = $vehicleMasterObj->addVehicleChemical($vehicleMasterId, $chemicalName,$chemicalQuantity);
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $chemicalId!="") {
					# Check Test master In use
					/*$driverMasterInUse = $driverMasterObj->testMethodRecInUse($vehicleTypeId);
					if (!$driverMasterInUse)*/ $delHarvestingChemicalRec = $vehicleMasterObj->delHarvestingChemicalRec($chemicalId);
						
				}
			} // Brand Loop ends here
		}
		if ($vehicleMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succVehicleMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateVehicleMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failVehicleMasterUpdate;
		}
		$vehicleMasterRecUptd	=	false;
	}


	# Delete Vehicle Type
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$vehicleMasterId	=	$p["delId_".$i];

			if ($vehicleMasterId!="") {
				// Need to check the selected Department is link with any other process
				$vehicleMasterRecDel	=	$vehicleMasterObj->deleteVehicleMaster($vehicleMasterId);
				# Test Method
					$delHarvestingEquipment	 = $vehicleMasterObj->deleteEquimentRecs($vehicleMasterId);
					
					$delHarvestingChemical	 = $vehicleMasterObj->deleteChemicalRecs($vehicleMasterId);
			}
		}
		if ($vehicleMasterRecDel && $delHarvestingEquipment	&& $delHarvestingChemical) {
			$sessObj->createSession("displayMsg",$msg_succDelVehicleMaster);
			$sessObj->createSession("nextPage",$url_afterDelVehicleMaster.$selection);
		} else {
			$errDel	=	$msg_failDelVehicleMaster;
		}
		$vehicleMasterRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$vehicleMasterId	=	$p["confirmId"];
			if ($vehicleMasterId!="") {
				// Checking the selected fish is link with any other process
				$vehicleMasterRecConfirm = $vehicleMasterObj->updateVehicleMasterconfirm($vehicleMasterId);
			}

		}
		if ($vehicleMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmVehicleMaster);
			$sessObj->createSession("nextPage",$url_afterDelVehicleMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$vehicleMasterId = $p["confirmId"];
			if ($vehicleMasterId!="") {
				#Check any entries exist
				
					$vehicleMasterRecConfirm = $vehicleMasterObj->updateVehicleMasterReleaseconfirm($vehicleMasterId);
				
			}
		}
		if ($vehicleMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmVehicleMaster);
			$sessObj->createSession("nextPage",$url_afterDelVehicleMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Registration type ;
	$vehicleMasterRecords	=	$vehicleMasterObj->fetchAllPagingRecords($offset, $limit);
	$vehicleMasterSize		=	sizeof($vehicleMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($vehicleMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editVehicleMaster;
	else 		$heading = $label_addVehicleMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/VehicleMaster.js";
	
	# Get all vehicle type Recs
		$vehicleTypeRecs = $vehicleTypeObj->fetchAllActiveVehicleType();
		
	# Get all harvesting equipment Recs
		$harvestingEquipmentRecs = $harvestingEquipmentMasterObj->fetchAllRecordsActiveequipmentType();
		
	# Get all harvesting chemical Recs
		$harvestingChemicalRecs = $harvestingChemicalMasterObj->fetchAllChemicalRecordsActive();
	
	$declarVehicleTypeRecords = $driverMasterObj->fetchAlldeclarVehicleType();
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
				
	?>	
	<form name="frmVehicleMaster" action="VehicleMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
	<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Manage Vehicle Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php							
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('VehicleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddVehicleMaster(document.frmVehicleMaster);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('VehicleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddVehicleMaster(document.frmVehicleMaster);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidVehicleMasterId" value="<?=$vehicleMasterId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<td class="fieldName" nowrap >*Vehicle Number</td>
												<td><INPUT TYPE="text" NAME="vehicleNumber" size="15" value="<?=$vehicleNumber;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*Vehicle Type</td>
												<td nowrap>
													<select name="vehicleType" id="vehicleType">
													<option value="">-- Select --</option>
													<?php
													foreach ($vehicleTypeRecs as $cmr) {
														$vehicleTypeId 	= $cmr[0];	
														$vehicleTypeVar	= $cmr[1];
														$selected = ($vehicleType==$vehicleTypeId)?"selected":""
													?>
													<option value="<?=$vehicleTypeId?>" <?=$selected?>><?=$vehicleTypeVar?></option>
													<?  }?>
													</select>
												</td>
											</tr>
											
										
											
											<tr>
												<td class="fieldName" nowrap >Harvesting Equipments List</td>
											<td>
											<table>
												<!--  Dynamic Row Starts Here-->
											<tr id="catRow1">
												<td colspan="2" style="padding-left:5px;padding-right:5px;">
													<table  id="tblHarvestingEquipment">
													<tr bgcolor="#f2f2f2" align="center">
																
														
													</tr>				
													</table>
												</td>
											</tr>
											<input type='hidden' name="hidHarvestingEquipmentsTableRowCount" id="hidHarvestingEquipmentsTableRowCount" value="">
																				<!--  Dynamic Row Ends Here-->
									<tr id="catRow2"><TD height="5"></TD></tr>
									<tr id="catRow3">
										<TD style="padding-left:5px;padding-right:5px;">
											<a href="###" id='addRow' onclick="javascript:addNewHarvestingEquipment();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
										</TD>
									</tr>
										
										</table>
							
								</td>		
								</tr>
								
									<tr>
												<td class="fieldName" nowrap >Harvesting Chemical List</td>
											<td>
											<table>
												<!--  Dynamic Row Starts Here-->
											<tr id="catRow1">
												<td colspan="2" style="padding-left:5px;padding-right:5px;">
													<table  id="tblHarvestingChemical">
													<tr bgcolor="#f2f2f2" align="center">
																
														
													</tr>				
													</table>
												</td>
											</tr>
											<input type='hidden' name="hidHarvestingChemicalTableRowCount" id="hidHarvestingChemicalTableRowCount" value="">
																				<!--  Dynamic Row Ends Here-->
									<tr id="catRow2"><TD height="5"></TD></tr>
									<tr id="catRow3">
										<TD style="padding-left:5px;padding-right:5px;">
											<a href="###" id='addRow' onclick="javascript:addNewHarvestingChemical();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
										</TD>
									</tr>
										
										</table>
							
								</td>		
								</tr>
														
											
											
								
											
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('VehicleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddVehicleMaster(document.frmVehicleMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('VehicleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddVehicleMaster(document.frmVehicleMaster);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>	
							<?php
								require("template/rbBottom.php");
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Vehicle Master Starts
		?>
	</table>
	</td>
	</tr>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Department </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$vehicleMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintVehicleMaster.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
			if ( sizeof($vehicleMasterRecords) > 0 ) {
				$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"VehicleMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"VehicleMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"VehicleMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Vehicle Number</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Vehicle Type</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Harvesting Equipment </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Equipment Quantity </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Harvesting Chemical </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Chemical Quantity</th>
		
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach($vehicleMasterRecords as $cr) {
		$i++;
		 $vehicleMasterId		=	$cr[0];
		 $vehicleNumber		=	stripSlash($cr[1]);
		 $vehicleType       =	stripSlash($cr[2]);
		 $vehicleTypeRec=$vehicleMasterObj->fetchVehicleType($vehicleType);
		 $vehicleTypeName=$vehicleTypeRec[1];
		 $harvestingEquipment= $vehicleMasterObj->getharvestingEquipment($vehicleMasterId);
		 $harvestingChemical= $vehicleMasterObj->getharvestingChemical($vehicleMasterId);
		 //$vehicleType		=	$driverMasterObj->getVehicleType($driverMasterId);
		 $active=$cr[3];
		$existingrecords=$cr[4];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$vehicleMasterId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$vehicleNumber;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$vehicleTypeName;?></td>
		
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
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
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
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
		
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
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
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
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
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$vehicleMasterId;?>,'editId'); this.form.action='VehicleMaster.php';"  >
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$vehicleMasterId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$vehicleMasterId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"VehicleMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"VehicleMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"VehicleMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<?
		} else {
	?>
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</tbody>
	</table>	
								</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$vehicleMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintVehicleMaster.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>
							<?php
								include "template/boxBR.php"
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	<?php 
		//if ($addMode || $editMode) {
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewHarvestingEquipment()
		{
			//addNewRow('tblvehicleType', '', '', '', '','');	
			addNewRow('tblHarvestingEquipment','','','');
		}
		
		function addNewHarvestingChemical()
		{
			//addNewRow('tblvehicleType', '', '', '', '','');	
			addChemicalRow('tblHarvestingChemical','','','');
		}

		
		
		function addNewItems()
		{
			addNewHarvestingEquipment();
			addNewHarvestingChemical();
			
		}
	</SCRIPT>
	<?php 
		} 
	?>

	<?php		
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewItems();
	</SCRIPT>
	<?php 
		}
	?>
	<!-- Edit Record -->
	<script language="JavaScript" type="text/javascript">	
		// Get state
		<?php
		if ($editMode) {
	
		
		if (sizeof($harvestingEquipmentRec)>0){
			$j=0;
			
				foreach($harvestingEquipmentRec as $ver) {	
				$harvestingEquipmentId 	= $ver[0];
				$harvestingEquipmentName	= rawurlencode(stripSlash($ver[1]));
				$harvestingEquipmentQuantity	= rawurlencode(stripSlash($ver[2]));
						
	?>	
		addNewRow('tblHarvestingEquipment','<?=$harvestingEquipmentId?>','<?=$harvestingEquipmentName?>','<?=$harvestingEquipmentQuantity?>');		
	<?
			$j++;
			}
		} 
		
		
		if (sizeof($harvestingChemicalRec)>0){
			$j=0;
			
				foreach($harvestingChemicalRec as $ver) {	
				$harvestingChemicalId 	= $ver[0];
				$harvestingChemicalName	= rawurlencode(stripSlash($ver[1]));
				$harvestingChemicalQuantity	= rawurlencode(stripSlash($ver[2]));
						
	?>	
		addChemicalRow('tblHarvestingChemical','<?=$harvestingChemicalId?>','<?=$harvestingChemicalName?>','<?=$harvestingChemicalQuantity?>');		
	<?
			$j++;
			}
		} 
	?>
		
		
		function addNewHarvestingEquipment()
		{
			//addNewRow('tblvehicleType', '', '', '', '','');	
			addNewRow('tblHarvestingEquipment','','','');
		}

		function addNewHarvestingChemical()
		{
			//addNewRow('tblvehicleType', '', '', '', '','');	
			addChemicalRow('tblHarvestingChemical','','','');
		}
		
		
		
		
	<?
		 }
	?>
	
	
	</script>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "licenceExpiryDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "licenceExpiryDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>