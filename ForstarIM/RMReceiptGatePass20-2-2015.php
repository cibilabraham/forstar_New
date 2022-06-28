<?php

	require("include/include.php");
	require_once('lib/RMReceiptPass_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	
	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	

	if (!$accesscontrolObj->canAccess()) {
		echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;

	// echo '<pre>';
	// print_r($p);
	// echo '</pre>';
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/rmReceiptGatePass.js"; // For Printing JS in Head section
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
										
	if($p['cmdAdd'])
	{	
		
		
		$rmReceiptGatePassObj->addRmReceiptGatePassDetails($p,$userId);
							
		$lastId = $databaseConnect->getLastInsertedId();
		//echo $lastId ;
		//die();
		
		$rmReceiptGatePassObj->updatesealstatus($p,$userId);
		
		#-----------------------------------------------------------------
		# insert last generated receipt gate pass number to manage chellan
		$number_gen_id =	$p["number_gen_id"];
		$receiptGatePass = $p["receiptGatePass"];
		preg_match('/\d+/', $receiptGatePass, $numMatch);
		$lastnum = $numMatch[0];
		$rmlastIdInsert	=$manageChallanObj->lastGeneratedProcurementId($lastnum,$number_gen_id);
		$seal_id=$p['in_Seal'];
		$sealDet=$manageSealObj->getSealDetail($seal_id);
		$alpha	=$manageSealObj->getAlphaPrefix($sealDet[3]);
		$alphacode=$alpha[0];
		$sealnumber=$sealDet[2];
		$rm_gate_pass_id=$sealDet[1];
		$seal_status='In seal';
		$status="Used";
		$sealRecConfirm = $manageSealObj->insertReleaseSeal($alphacode,$sealnumber,$seal_id,$rm_gate_pass_id,$seal_status,$userId,$status);
		//die();
		
		
		if($p['procurment_Gate_PassId']!="")
		{	
			$supplierSize=	$p['supplierSize'];
			$equipmentSize=	$p['equipmentSize'];
			$chemicalSize=	$p['chemicalSize'];
			if ($supplierSize>0 )
			{
				for ($i=0; $i<$supplierSize; $i++) 
				{
					$supplier_id	= $p["supplier_id_".$i];
					$pond_id			=$p["pond_id_".$i];
					$challan_no	= $p["challan_no_".$i];
					$challandt=$p["challan_date_".$i];
					$challan_date			=mysqlDateFormat($challandt);
					$Company_Name			=$p["Company_Name_".$i];
					$unit			=$p["unit_".$i];
						# IF SELECT ALL STATE
						if ($lastId!="" && $supplier_id!="" && $pond_id!="" && $challan_no!="" && $challan_date!="" && $Company_Name!="" && $unit!="") {
								$supplierlist = $rmReceiptGatePassObj->addReceiptGatepassSupplier($lastId,$supplier_id,$pond_id,$challan_no,$challan_date,$Company_Name,$unit);
							}  # If 										
				} # Status check ends here
							
			}
			
			if ($equipmentSize>0 )
			{
				for ($j=0; $j<$equipmentSize; $j++) 
				{
					$equipmentId	= $p["equipmentId_".$j];
					//$equipmentIssuedQuantity			=$p["equipmentIssuedQuantity_".$j];
					$equipmentReturnedQuantity	= $p["equipmentReturnedQuantity_".$j];
					$equipmentDifferenceQuantity=$p["equipmentDifferenceQuantity_".$j];
					$equipmentRemarks			=$p["equipmentRemarks_".$j];
					$procurementEquipmentId			=$p["procurementEquipmentId_".$j];
					# IF SELECT ALL STATE
						if ($procurementEquipmentId!="" && $equipmentId!="" && $equipmentReturnedQuantity!="" && $equipmentDifferenceQuantity!="") {
								$equipmentlist = $rmReceiptGatePassObj->updateReceiptEquipment($procurementEquipmentId,$equipmentId,$equipmentReturnedQuantity,$equipmentDifferenceQuantity,$equipmentRemarks);
							}  # If 										
				} # Status check ends here
							
			}
		
			if ($chemicalSize>0 )
			{
				for ($k=0; $k<$chemicalSize; $k++) 
				{
					$chemicalId	= $p["chemicalId_".$k];
					//$chemicalIssuedQuantity			=$p["chemicalIssuedQuantity_".$j];
					$chemicalReturnedQuantity	= $p["chemicalReturnedQuantity_".$k];
					$chemicalUsedQuantity=$p["chemicalDifferenceQuantity_".$k];
					$chemicalRemarks			=$p["chemicalRemarks_".$k];
					$procurementChemicalId			=$p["procurementChemicalId_".$k];
					# IF SELECT ALL STATE
						if ($procurementChemicalId!="" && $chemicalId!="" && $chemicalReturnedQuantity!="" && $chemicalUsedQuantity!="") {
								$chemicallist = $rmReceiptGatePassObj->updateReceiptChemical($procurementChemicalId,$chemicalId,$chemicalReturnedQuantity,$chemicalUsedQuantity,$chemicalRemarks);
							}  # If 										
				} # Status check ends here
							
			}

			###update vehicle status
			$vehicleid=$p['vehicle_id'];
			$updateVehicle=$rmReceiptGatePassObj->updateVehiclestatus($vehicleid);

			###update driver status
			$driverIds=$p['driver_id'];
			//echo $vehicleid.'--------'.$driverIds;
			$driverDetail=explode(",",$driverIds);
			$driverCnt=sizeof($driverDetail);
			//echo $driverCnt;
			for($m=0; $m<$driverCnt; $m++)
			{
				 $driverId=$driverDetail[$m];
				 $updateDriver=$rmReceiptGatePassObj->updateDriverstatus($driverId);
			}

		}
		$msg_succAddRMReceiptGatePass = "RM Receipt gate pass details added successfully";
		$url_afterAddRMReceiptGatePassData = "RMReceiptGatePass.php";
		$sessObj->createSession("displayMsg",$msg_succAddRMReceiptGatePass);
		$sessObj->createSession("nextPage",$url_afterAddRMReceiptGatePassData);
	}
	
	
												
	if($p['cmdUpdate'])
	{
	
		$editDetails = $rmReceiptGatePassObj->getReceiptGatePassInSeal($p['procurment_Gate_PassId']);
		 $in_Seal  = $editDetails[0];
		//die();
		$rmReceiptGatePassObj->updateInsealstatusValue($in_Seal);
		$rmReceiptGatePassObj->updateRmReceiptGatePassDetails($p,$userId);
		$rmReceiptGatePassObj->updatesealstatus($p,$userId);
		
		$seal_id=$p['in_Seal'];
		$sealDet=$manageSealObj->getSealDetail($seal_id);
			$alpha	=$manageSealObj->getAlphaPrefix($sealDet[3]);
			$alphacode=$alpha[0];
		 	$sealnumber=$sealDet[2];
		
			$rm_gate_pass_id=$sealDet[1];
			$seal_status='In seal';
			$status="Used";
			$sealRecConfirm = $manageSealObj->insertReleaseSeal($alphacode,$sealnumber,$seal_id,$rm_gate_pass_id,$seal_status,$userId,$status);
		//die();
				if($p['procurment_Gate_PassId']!="")
				{	
					$supplierSize=	$p['supplierSize'];
					$equipmentSize=	$p['equipmentSize'];
					$chemicalSize=	$p['chemicalSize'];
						if ($supplierSize>0 )
						{
							for ($i=0; $i<$supplierSize; $i++)
							{
								$supplier_id	= $p["supplier_id_".$i];
								$pond_id			=$p["pond_id_".$i];
								$challan_no	= $p["challan_no_".$i];
								$challandt=$p["challan_date_".$i];
								$challan_date			=mysqlDateFormat($challandt);
								$Company_Name			=$p["Company_Name_".$i];
								$unit			=$p["unit_".$i];
								$receipt_id=$p["receipt_".$i];
								# IF SELECT ALL STATE
								if ($receipt_id!="" && $supplier_id!="" && $pond_id!="" && $challan_no!="" && $challan_date!="" && $Company_Name!="" && $unit!="") 
								{
									$supplierlist = $rmReceiptGatePassObj->updateReceiptGatepassSupplier($receipt_id,$supplier_id,$pond_id,$challan_no,$challan_date,$Company_Name,$unit);
								}  # If 										
							} # Status check ends here
						}

						if ($equipmentSize>0 )
						{
							for ($j=0; $j<$equipmentSize; $j++) 
							{
								$equipmentId	= $p["equipmentId_".$j];
								//$equipmentIssuedQuantity			=$p["equipmentIssuedQuantity_".$j];
								$equipmentReturnedQuantity	= $p["equipmentReturnedQuantity_".$j];
								$equipmentDifferenceQuantity=$p["equipmentDifferenceQuantity_".$j];
								$equipmentRemarks			=$p["equipmentRemarks_".$j];
								$procurementEquipmentId			=$p["procurementEquipmentId_".$j];
								# IF SELECT ALL STATE
									if ($procurementEquipmentId!="" && $equipmentId!="" && $equipmentReturnedQuantity!="" && $equipmentDifferenceQuantity!="") {
											$equipmentlist = $rmReceiptGatePassObj->updateReceiptEquipment($procurementEquipmentId,$equipmentId,$equipmentReturnedQuantity,$equipmentDifferenceQuantity,$equipmentRemarks);
										}  # If 										
							} # Status check ends here
										
						}
		
						if ($chemicalSize>0 )
						{
							for ($k=0; $k<$chemicalSize; $k++) 
							{
								$chemicalId	= $p["chemicalId_".$k];
								//$chemicalIssuedQuantity			=$p["chemicalIssuedQuantity_".$j];
								$chemicalReturnedQuantity	= $p["chemicalReturnedQuantity_".$k];
								$chemicalUsedQuantity=$p["chemicalDifferenceQuantity_".$k];
								$chemicalRemarks			=$p["chemicalRemarks_".$k];
								$procurementChemicalId			=$p["procurementChemicalId_".$k];
								# IF SELECT ALL STATE
									if ($procurementChemicalId!="" && $chemicalId!="" && $chemicalReturnedQuantity!="" && $chemicalUsedQuantity!="") {
											$chemicallist = $rmReceiptGatePassObj->updateReceiptChemical($procurementChemicalId,$chemicalId,$chemicalReturnedQuantity,$chemicalUsedQuantity,$chemicalRemarks);
										}  # If 										
							} # Status check ends here
										
						}

		}
		$msg_succAddRMReceiptGatePass = "RM Receipt gate pass details updated successfully";
		$url_afterAddRMReceiptGatePassData = "RMReceiptGatePass.php";
		$sessObj->createSession("displayMsg",$msg_succAddRMReceiptGatePass);
		$sessObj->createSession("nextPage",$url_afterAddRMReceiptGatePassData);
	}
	if(isset($_REQUEST['generateLotID']))
	{
	
		$checkLotIdAval = $rmReceiptGatePassObj->checkLotIdAvailable();
		//nEED TO IMPLEMENT THIS LATER
		// if($checkLotIdAval == 0)
		// {
			// $msg_succAddRMReceiptGatePass = "Lot id expired. Please reset the lot id in settings";
			// $url_afterAddRMReceiptGatePassData = "RMReceiptGatePass.php";
			// $sessObj->createSession("displayMsg",$msg_succAddRMReceiptGatePass);
			// $sessObj->createSession("nextPage",$url_afterAddRMReceiptGatePassData);
		// }
		// else
		// {
			$rmReceiptGatePassObj->generateLotID($_REQUEST['generateLotID'],$checkLotIdAval);
			$msg_succAddRMReceiptGatePass = "Lot id Generated successfully";
			$url_afterAddRMReceiptGatePassData = "RMReceiptGatePass.php";
			$sessObj->createSession("displayMsg",$msg_succAddRMReceiptGatePass);
			$sessObj->createSession("nextPage",$url_afterAddRMReceiptGatePassData);
		//}
	}
	$editDetails = array();
	$id = '';$procurment_Gate_PassId = '';$vehicle_Number = '';$driver = '';$date_Of_Entry = '';$labours = '';$receiptGatePass='';
	$supplier_Challan_No = '';$supplier_Challan_Date = '';$Company_Name = '';$unit = '';$verified = '';
	$out_Seal = '';$in_Seal = '';$seal_No = '';$result = '';
	if(isset($p['editId']))
	{
		$editmode=true;
		$id = $p['editId'];
		$editDetails = $rmReceiptGatePassObj->getReceiptGatePassForEdit($p['editId']);
		// print_r($editDetails);
		if(sizeof($editDetails) > 0)
		{
			$procurment_Gate_PassId = $editDetails[0]['procurment_Gate_PassId'];
			$receiptGatePass = $editDetails[0]['receipt_gatepass_number'];
			$vehicle_Number = $editDetails[0]['vehicle_number'];
			$driver         = $editDetails[0]['driver_name'];
			if($vehicle_Number == '')
			{
				$vehicle_Number = $editDetails[0]['vehicle_number_other'];
				$driver         = $editDetails[0]['driver_name_other'];
			}
			$date_Of_Entry = dateFormat($editDetails[0]['date_Of_Entry']);
			$labours = $editDetails[0]['labours'];
			$supplier_Challan_No = $editDetails[0]['supplier_Challan_No'];
			$supplier_Challan_Date = dateFormat($editDetails[0]['supplier_Challan_Date']);
			$Company_Name  = $editDetails[0]['Company_Name'];
			$unit   =  $editDetails[0]['unit'];
			$verified = $editDetails[0]['verified'];
			$out_Seal = $editDetails[0]['out_Seal'];
			$in_Seal  = $editDetails[0]['in_Seal'];
			$seal_No  = $editDetails[0]['seal_No'];
			$result   = $editDetails[0]['result'];
			$suplier   = $editDetails[0]['supplier_id'];
			$material   = $editDetails[0]['material_type_id'];
		}
	}
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") {
		$pageNo=$p["pageNo"];
	} else if ($g["pageNo"] != "") {
		$pageNo=$g["pageNo"];
	} else {
		$pageNo=1;
	}
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}
	
	#List all Stock Issuance
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		//$rmProcurementRecords	= $rmProcurmentOrderObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$datas	= $rmReceiptGatePassObj->fetchAllPagingRecords($fromDate,$tillDate,$offset, $limit);
		$datasSize	= sizeof($datas);
		$fetchAllProcurmentReceiptRecs = $rmReceiptGatePassObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllProcurmentReceiptRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------





	/*if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
	//echo "hii";
	
	
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$datas	= $rmReceiptGatePassObj->fetchAllPagingRecords($fromDate,$tillDate);
		
		//$rmProcurementSize	= sizeof($rmProcurementRecords);
		//$fetchAllProcurmentRecs = $rmProcurmentOrderObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	else{
			$fromDate = mysqlDateFormat($dateFrom);
			$tillDate = mysqlDateFormat($dateTill);

				$datas	= $rmReceiptGatePassObj->fetchAllPagingRecords($fromDate,$tillDate);
		//$datas	=	$rmReceiptGatePassObj->getAllReceiptGatePass();
		
	}*/

	// echo '<pre>';
	// print_r($p);
	// echo '</pre>';
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	
	$receiptGateConfirmEnabled = $manageconfirmObj->receiptGateConfirmEnabled();
	//echo "hai";
	if(isset($p['cmdAddNew']) || isset($p['cmdEdit']))
	{
	?>
	<form name="frmRMReceiptGatePass" action="RMReceiptGatePass.php" onsubmit="return ReceiptValidate();" method="post">
	<?php
	}
	else
	{
	?>
		<form name="frmRMReceiptGatePass" action="RMReceiptGatePass.php" method="post">
	<?php
	}
	?>
	<table cellspacing="0"  align="center" cellpadding="0" width="81%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		
		<?php
			if(isset($p['cmdAddNew']) || isset($p['cmdEdit']))
			{
				$procurementIDs = $rmReceiptGatePassObj->getAllProcurement();
				$companyNames   = $rmReceiptGatePassObj->getAllCompany();
				$units          = $rmReceiptGatePassObj->getAllUnit();
				$supervisors    = $rmReceiptGatePassObj->getAllSupervisor();
				$materialType   = $rmReceiptGatePassObj->getAllMaterialType();
				$suppliers= $rmReceiptGatePassObj->getAllSupplier();
				$buttonValue = '';$buttonName = '';
				if(isset($p['cmdAddNew']))
				{
					$buttonValue = 'Add';
					$buttonName = 'cmdAdd';
				}
				else if(isset($p['cmdEdit']))
				{
					$buttonValue = 'Update';
					$buttonName = 'cmdUpdate';
				}
		?>
				<tr>
					<td>
						<table width="75%" cellspacing="1" cellpadding="0" border="0" bgcolor="#D3D3D3" align="center">
							<tbody>
								<tr>
									<td bgcolor="white">
										<!-- Form fields start -->
										
										<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
											<tbody>
												<tr >
													<td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; Add New RM Receipt Gate Pass</td>
												</tr>
												<tr>
																  <td colspan="2">&nbsp;</td>
																</tr>
												<tr><TD nowrap><span class="fieldName" style="color:red; line-height:normal" id="message" name="message"></span></TD></tr>				
											<tr>
												<td colspan="4">
													<table  width="83%" border="0" cellpadding="4" cellspacing="0" align="center">
													<tr>
														<td colspan="2" >
																											<?php
																		$left_l=true;
																		$entryHead = "";
																		$rbTopWidth = "";
																		require("template/rbTop.php");
																	?>
																	<table width="80%" border="0" cellpadding="4" cellspacing="0" align="center">
																	
				
																	<tr>
																		<td class="fieldName" nowrap>*Receipt Gate Pass Number:</td>
																		<td class="listing-item"><input name="receiptGatePass" type="text" id="receiptGatePass" size="10" value="<?=$receiptGatePass;?>" tabindex="1"
																		onchange="xajax_checkValidReceiptNumber(document.getElementById('receiptGatePass').value);">
																		</td>
																		
																		<td nowrap="" class="fieldName">*Date of Entry:&nbsp;</td>
																		<td><input type="text" size="15" id="date_Of_Entry" name="date_Of_Entry" value="<?php echo $date_Of_Entry;?>" required>
																		</td>
																	
																	
																	
																	</tr>
																	<tr>				
																		<td nowrap="" class="fieldName">*Name of Supervisor:&nbsp;</td>
																					<td height="5">
																						<select id="verified" name="verified" required>
																							<option value="">--select--</option>
											  											    <?php
																								if(sizeof($supervisors) > 0)
																								{
																									foreach($supervisors as $supervisor)
																									{
																										$sel = '';
																										if($verified == $supervisor['id']) $sel = 'selected';
																										
																										echo '<option '.$sel.' value="'.$supervisor['id'].'">'.$supervisor['name'].'</option>';
																									}
																								}
																							?>
											  										    </select>										      
																					</td>
																								 
																		
																	</tr>
																	</table>
																	<?php
															require("template/rbBottom.php");
																	?>
														</td>
													</tr>
													</table>	
												</td>
											</tr>



											<tr>
												<td colspan="4">
													<table  width="84%" border="0" cellpadding="4" cellspacing="0" align="center">
													<tr>
														<td colspan="2" >
																	<table width="80%" border="0" cellpadding="4" cellspacing="0" align="left">
																	<tr>
																	<td class="fieldName" nowrap colspan="2" valign="top" >
																	<?php
													$left_l=true;
													$entryHead = "";
													$rbTopWidth = "";
													require("template/rbTop.php");
												?>
														<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left">
															<tbody>
																<tr>
																	<td height="10" colspan="2"></td>
																</tr>
																<tr align="left">	
																	<td nowrap="" class="fieldName" >
																		Procurement order available <input onclick="procurementAvlCheck(this.checked);" type="checkbox" name="procure_aval" id="procure_aval" value="1" />
																	</td>
																	<td align="center" colspan="1" >&nbsp;</td>
																</tr>
																<input type="hidden" value="" name="hidunitTransferDataId">
																<tr>
																	<td id="procurement_aval" nowrap="" class="fieldName" colspan="1">
																		
																		<table width="60%" align="left">
																			<tbody>
																				<tr>
																					<td nowrap="" class="fieldName">* Vehicle No :</td>
																					<td height="10">
																						<input type="text" size="15" name="vehicle_Number" id="vehicle_Number" value="<?php echo $vehicle_Number;?>" required/>
																					</td>
																				</tr>
																				<tr>
																				   <td nowrap="" class="fieldName">* Driver Name:</td>
																				   <td>
																				   <textarea id="driver" name="driver" required><?php echo $driver;?></textarea>
																				   
																				   <!--<input type="text" size="15" id="driver" name="driver" value="<?php echo $driver;?>" required>--></td>
																				</tr>
																				
																				<tr>
																					<td nowrap="" class="fieldName">*Supplier Chalan no:</td>
																					<td height="10">
																						<input type="text" size="15" value="<?php echo $supplier_Challan_No;?>" id="supplier_Challan_No" name="supplier_Challan_No" required>
																					</td>
																				</tr>   
																				<? 
																			if ($supplier_Challan_Date=="") $supplier_Challan_Date=date("d/m/Y");
																			?>
																				<tr>
																				   <td nowrap="" class="fieldName">*Chalan Date:</td>
																					<td height="10">
																						<input type="text" size="15" value="<?php echo $supplier_Challan_Date;?>" id="supplier_Challan_Date" name="supplier_Challan_Date" required>
																					</td>
																				</tr>
																				<tr>				
																					<td nowrap="" class="fieldName">*Raw material type</td>
																					<td height="5">
																						<select id="material" name="material" required>
																							<option value="">--select--</option>
											  											    <?php
																								if(sizeof($materialType) > 0)
																								{
																									foreach($materialType as $materialTypes)
																									{
																										$sel = '';
																										if($material == $materialTypes[0]) $sel = 'selected';
																										
																										echo '<option '.$sel.' value="'.$materialTypes[0].'">'.$materialTypes[1].'</option>';
																									}
																								}
																							?>
											  										    </select>										      
																					</td>
																				</tr>
																				<tr>				
																					<td nowrap="" class="fieldName">*Supplier</td>
																					<td height="5">
																						<select id="supplier" name="supplier" required>
																							<option value="">--select--</option>
											  											    <?php
																								if(sizeof($suppliers) > 0)
																								{
																									foreach($suppliers as $supplier)
																									{
																										$sel = '';
																										if($suplier == $supplier[0]) $sel = 'selected';
																										
																										echo '<option '.$sel.' value="'.$supplier[0].'">'.$supplier[1].'</option>';
																									}
																								}
																							?>
											  										    </select>										      
																					</td>
																				</tr>
																				<tr>
																					<td nowrap="" class="fieldName">*Company Name:&nbsp;</td>
																					<td height="5">
																						<select id="Company_Name" name="Company_Name" required>
																							<option value="">--select--</option>																							
																							<?php
																								if(sizeof($companyNames) > 0)
																								{
																									foreach($companyNames as $companyName)
																									{
																										$sel = '';
																										if($Company_Name == $companyName['id']) $sel = 'selected';
																										
																										echo '<option '.$sel.' value="'.$companyName['id'].'">'.$companyName['name'].'</option>';
																									}
																								}
																							?>
											  										    </select>										      
																					</td>
																				</tr>	  
																				<tr>
																					<td nowrap="" class="fieldName">*Unit:&nbsp;</td>
																					<td height="5">
																						<select id="unit" name="unit" required>
																							<option value="">--select--</option>
											  											    <?php
																								if(sizeof($units) > 0)
																								{
																									foreach($units as $unitval)
																									{
																										$sel = '';
																										if($unit == $unitval['id']) $sel = 'selected';
																										
																										echo '<option '.$sel.' value="'.$unitval['id'].'">'.$unitval['name'].'</option>';
																									}
																								}
																							?>
											  										    </select>										      
																					</td>
																				</tr>
																			</tbody>
																		</table>
																		
																		
																	</td>
																	
																</tr>
																<tr>
																  <td colspan="2">&nbsp;</td>
																</tr>	
																<tr>
																	<td height="10" colspan="2"></td>
																</tr>
																
																<tr>
																	<td height="10" colspan="2"></td>
																</tr>
															</tbody>
														</table>
															<?php
												require("template/rbBottom.php");
											?>
															
																	</td>
																								
																		
																	
																	<td colspan="1" id="seal_details" valign="top">
																				<table width="50%" cellspacing="0" cellpadding="0" border="0" align="center">
																					<tbody>
																						
																						<input type="hidden" value="" name="hidunitTransferDataId">
																						<tr>
																							<td nowrap="" class="fieldName" colspan="1">
																							<?php
																						$left_l=true;
																						$entryHead = "";
																						$rbTopWidth = "";
																						require("template/rbTop.php");
																					?>
																								<table width="200" align="left">
																									<tbody>
																										<tr id="outsealrow" style="display:none; float:left">
																											<td nowrap="" class="fieldName">* OUT SEAL:</td>
																											<td  id="alphaCodeOutDisp" class="listing-item"  ><?php echo $alphaCodeOut;?></td>
																											<td height="10">
																											
																												<input type="text" value="<?php echo $out_Seal;?>" size="8" id="out_Seal" name="out_Seal" readonly="readonly" style="border:none;" >
																												<input type="hidden" value="<?php echo $alphaCodeOut;?>" size="2" id="alphaCodeOut" name="alphaCodeOut" readonly="readonly" >
																												
																											</td>
																										</tr>
																										<tr id="insealrow" style="display:none; float:left">
																											<td nowrap="" class="fieldName">* IN SEAL:</td>
																											<td  id="alphaCodeInDisp" class="listing-item"  ><?php echo $alphaCodeIn;?></td>
																											<td height="10">
																											
																												<select id="in_Seal" name="in_Seal" onchange="setInsealId(this.value);">
																													<option value=""> Select </option>
																												</select>
																												<input type="hidden" value="<?php echo $alphaCodeIn;?>" size="2" id="alphaCodeIn" name="alphaCodeIn" readonly="readonly">
																											</td>
																										</tr>
																										<tr id="newsealavlrow" style="display:none; float:left" >
																											<td nowrap="" colspan="2" class="fieldName" id='new_sealhid'>
																												Is new seal for this procurement?
																												<input type="checkbox" value="1" id="newsealaval" name="newsealaval" onclick="showNewReason(this.checked);">
																											</td>
																										</tr>
																										<tr id="newsealrow" style="float:left">
																											<td nowrap="" class="fieldName">* NEW SEAL:</td>
																											<td height="10">
																												
																											<input type="text" value="<?php echo $seal_No;?>" size="15" id="seal_No" name="seal_No" 
																												
																												>
																											</td>
																										</tr>
																										<tr id="newsealreasonrow" style="float:left">
																											<td nowrap="" class="fieldName">* REASON:</td>
																											<td height="10">
																												<textarea rows="5" cols="22" id="result" name="result"><?php echo $result;?></textarea>
																											</td>
																										</tr>
																										<tr >
																											<td nowrap="" height="10" colspan="2"></td>
																											
																										</tr>
																										 <input type="hidden" name="number_gen_id" id="number_gen_id" size="9" value="<?=$number_gen_id;?>" readonly="readonly"  />
																									</tbody>
																								</table>
																								<?php
																						require("template/rbBottom.php");
																					?>
																							
																							</td>
																							
																						</tr>
																						
																						<tr >
																							<td nowrap="" height="20" colspan="2"></td>
																											
																						</tr>
																						<tr>
																						<!--<td colspan="1" id="blocked_seal_details">-->
																						<td>	
																						</td>
																						</tr>
																						<tr>
																						  <td colspan="2">&nbsp;</td>
																						</tr>	
																						<tr>
																							<td height="10" colspan="2"></td>
																						</tr>
																						
																						<tr>
																							<td height="10" colspan="2"></td>
																						</tr>
																					</tbody>
																				</table>									
																		</td>
																		<td valign="top" id="blocked_seal_details">&nbsp;</td>
																	</tr>
																
																	</table>
																	
														</td>
													</tr>
													</table>	
												</td>
												
											</tr>
											
											
											<tr><td colspan="4" height="10%" id='supplier_display' align="left" style="padding-left:10px" >&nbsp;
											
											</td></tr>
											<tr><td colspan="4" >&nbsp;</td></tr>
											<tr><td colspan="4" id='equipment_display'  align="left" style="padding-left:10px"></td></tr>
											<tr><td colspan="4" >&nbsp;</td></tr>
											<tr><td colspan="4" id='chemical_display' align="left" style="padding-left:10px"></td></tr>
											<tr><td colspan="4" >&nbsp;</td></tr>

											<tr>
												<td align="center" colspan="4">
												<input type="button" onclick="return cancelRMRG('RMReceiptGatePass.php');" value=" Cancel " class="button" name="cmdCancel">&nbsp;&nbsp;
												<input type="submit" value="<?php echo $buttonValue;?>" class="button" name="<?php echo $buttonName;?>">
												</td>
											</tr>

											<tr><td colspan="4" height="10%">&nbsp;</td></tr>




											
																
																
																
																
											
												
											</tbody>
										</table>
										
										<!-- Form fields end   -->
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
		<?php
			}
		?>
		
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		
		<!-- List of records -->
		
		<tr>
			<td>
				<table width="80%" cellspacing="1" cellpadding="0" border="0" bgcolor="#D3D3D3" align="center" >
					<tbody>
						<tr bgcolor="white"  >
							<td>
								<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" >
									<tbody>
										<tr>
											<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
											<td nowrap="" background="images/heading_bg.gif" class="pageName">&nbsp; RM Receipt Gate Pass</td>
											<td background="images/heading_bg.gif"> 	<table cellspacing="0" cellpadding="0" align="right">
													<tbody>
													<tr >
													<td colspan="4" ></td>
													
													<td class="listing-item" align="right"> From:</td>
																		<td nowrap="nowrap"> 
																<? 
												if ($dateFrom=="") $dateFrom=date("d/m/Y");
												?>
														<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
													<td class="listing-item">&nbsp;</td>
														<td class="listing-item"> Till:</td>
																<td> 
																  <? 
												   if($dateTill=="") $dateTill=date("d/m/Y");
												  ?>
																  <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
												   <td class="listing-item">&nbsp;</td>
														<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
														<td class="listing-item" nowrap >&nbsp;</td>
													  </tr>
													
													</tbody>
												</table></td>
										</tr>
										<tr>
											<td height="10" colspan="3"></td>
										</tr>
										<tr>	
											<td colspan="3">
												<table cellspacing="0" cellpadding="0" align="center">
													<tbody>
													<tr >
														<td nowrap="" colspan="3" >
														<!--<input type="submit" onclick="return confirmDelete(this.form,'delId_',4);" name="cmdDelete" class="button" value=" Delete ">&nbsp;-->
														<input type="submit" class="button" name="cmdAddNew" value=" Add New ">&nbsp;
														<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRMReceiptGatePassAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
														><? }?>
														<!--<input type="button" onclick="javascript:printWindow('PrintUnitTransfer.php',700,600);" class="button" name="btnPrint" value=" Print "></td>-->
													</tr>
													<tr><td height="10px"></td></tr><tbody>
												</table>
												
											</td>
										</tr>
										
										<tr >
											<td width="1"></td>
											<td colspan="2" style="padding:0px 10px 0px 10px">
												<table width="80%" cellspacing="1" cellpadding="2" border="0" bgcolor="#999999" align="center" >
												<?php if(sizeof($datas) > 0)
													{
												?>
													<? if($maxpage>1){?>
												<tr bgcolor="#f2f2f2">
												<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
												<div align="right">
												<?php
												 $nav  = '';
												for ($page=1; $page<=$maxpage; $page++) {
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"RMReceiptGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"RMReceiptGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"RMReceiptGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												
												
													<tbody> 
														<tr bgcolor="#f2f2f2">
															<td width="20"><input type="checkbox" class="chkBox" onclick="checkAll(this.form,'delId_'); " id="CheckAll" name="CheckAll"></td>
															<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> DATE OF ENTRY </td>
															<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> Receipt Gate Pass Number</td>
															<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> Supervisor</td>
															<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> PROCUREMENT ID</td>
															<td style="padding-left:10px; padding-right:10px;" class="listing-head">  SUPPLIER CHALLAN NO </td>
															<td style="padding-left:10px; padding-right:10px;" class="listing-head"> COMPANY NAME</td>
															<td style="padding-left:10px; padding-right:10px;" class="listing-head"> UNIT </td>
															<td style="padding-left:10px; padding-right:10px;" class="listing-head"> LOT ID </td>
															<?php
																if($receiptGateConfirmEnabled )
																{
																	echo '<td style="padding-left:10px; padding-right:10px;" class="listing-head"> CONFIRM </td>';
																}
															?>
															<td style="padding-left:10px; padding-right:10px;" class="listing-head"> EDIT </td>			
														</tr>
														<?php
															$i = 1;
															
															foreach($datas as $data)
																{
																$supplier= $data['id'];
																$datasupplier=$rmReceiptGatePassObj->getAllReceiptGatePassSupplier($supplier);
														?>
														<tr bgcolor="WHITE">
															<td width="20">
																<input type="checkbox" class="chkBox" value="<?php echo $data['id'];?>" id="delId_<?php echo $i;?>" name="delId_<?php echo $i;?>">
															</td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php 
															$dateval=$data['date_Of_Entry'];
															echo dateFormat($dateval);
															?></td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['receipt_gatepass_number'];?></td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['Supervisor'];?></td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['gate_pass_id'];?></td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
															<?php if($data['gate_pass_id']!="")
															{
																if(sizeof($datasupplier)>0)
																{
																
																foreach($datasupplier as $dataval)
																{
																echo $dataval['challan_no'];
																echo '<br/>';
																}
																}
															
															}
															else{
															echo $data['supplier_Challan_No'];
															}
															?>
															</td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
															<?php if($data['gate_pass_id']!="")
															{
																if(sizeof($datasupplier)>0)
																{
																
																foreach($datasupplier as $dataval)
																{
																	$cmpID= $dataval['company_id'];
																	$Company_Name_Value = $rmReceiptGatePassObj->getCompanyName($cmpID);
																	echo $Company_Name_Value[1];
																	echo '<br/>';
																}
																}
															
															}
															else{
															echo $data['company_name'];
															}
															?>
															
															
															
															</td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
															<?php if($data['gate_pass_id']!="")
															{
																if(sizeof($datasupplier)>0)
																{
																
																foreach($datasupplier as $dataval)
																{
																	$untid= $dataval['unit_id'];
																	$Unit_Name_Value = $rmReceiptGatePassObj->getUnitName($untid);
																	echo $Unit_Name_Value[1];
																	echo '<br/>';
																}
																}
															
															}
															else{
															echo $data['unit_name'];
															}
															?></td>
															<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['lot_Id'];?></td>
																<?php
																	if($receiptGateConfirmEnabled)
																	{
																?>
																		<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
																		<?php
																			if($data['lot_Id'] == '')
																			{
																			$generateLotID= $data['id'];
																			$baseGenerate=base64_encode($generateLotID);
																		?>
																		<input type="button" value="Generate RM LotId" onClick="return page('ManageRMLOTID.php?generateLotID=<?php echo $baseGenerate;?>');">
																		<!--<a title="Click here to generate LotId "  class="link1" 
																		href="javascript:window.location='ManageRMLOTID.php?generateLotID=<?php echo $baseGenerate;?>';"><input type="button" value="Generate RM LotId" >
																		
																		</a>-->
																		<!--<a title="Click here to generate LotId " class="link1"  
																		href="javascript:window.location='RMReceiptGatePass.php?generateLotID=<?php echo $data['id'];?>';" style="text-decoration:none;"><input type="button" value="Generate RM LotId" ></a>-->
				
																		
																		
																		
																		<?php
																			}
																		?>
																		</td>
																<?php 
																	}
																?>
															<?php if($data['lot_Id']=="" || $data['generate']=="1" )
															{
															?>
															<td width="60" align="center" class="listing-item"><input type="submit" onclick="assignValue(this.form,<?php echo $data['id'];?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RMReceiptGatePass.php';" name="cmdEdit" value=" Edit "></td>
															<?php
															}
															else{
															?>
															<td width="60" align="center" class="listing-item">&nbsp;</td>
															<?php
															}
															?>
														</tr>
														<?php
																}
																?>
															
															<input type="hidden" value="<?php echo sizeof($datas);?>" id="hidRowCount" name="hidRowCount">
															
														
														<input type="hidden" value="" name="editId">
														<input type="hidden" value="0" name="editSelectionChange">

														<? if($maxpage>1){?>
															<tr bgcolor="#f2f2f2">
															<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
															<div align="right">
															<?php
															 $nav  = '';
															for ($page=1; $page<=$maxpage; $page++) {
																if ($page==$pageNo) {
																		$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																} else {
																		$nav.= " <a href=\"RMReceiptGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																	//echo $nav;
																}
															}
															if ($pageNo > 1) {
																$page  = $pageNo - 1;
																$prev  = " <a href=\"RMReceiptGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
															} else {
																$prev  = '&nbsp;'; // we're on page one, don't print previous link
																$first = '&nbsp;'; // nor the first page link
															}

															if ($pageNo < $maxpage) {
																$page = $pageNo + 1;
																$next = " <a href=\"RMReceiptGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
														<?php
														}
														else
															{
														?>
														<tr bgcolor="white">
															<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
														</tr>	
														<?
															}
														?>
														
													</tbody>
												</table>									
											</td>
										</tr>
										<tr>
											<td height="5" colspan="3"></td>
										</tr>
										<tr>	
											<td colspan="3">
												<table cellspacing="0" cellpadding="0" align="center">
													<tbody><tr>
														<td nowrap="">
														<!--<input type="submit" onclick="return confirmDelete(this.form,'delId_',4);" name="cmdDelete" class="button" value=" Delete ">&nbsp;-->
														<input type="submit" class="button" name="cmdAddNew" value=" Add New ">&nbsp;
														<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRMReceiptGatePassAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
														><? }?>
														<!--<input type="button" onclick="javascript:printWindow('PrintUnitTransfer.php',700,600);" class="button" name="btnPrint" value=" Print "></td>-->
														
													</tr>
												</tbody></table>									</td>
										</tr>
										<tr>
											<td height="5" colspan="3"></td>
										</tr>
									</tbody>
								</table>						
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		
	<!-- End List of records -->
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	
	<input type="hidden" name="vehicle_id" id="vehicle_id" />
	<input type="hidden" name="driver_id" id="driver_id" />
	<input type="hidden" name="out_seal_id" id="out_seal_id" />
	<input type="hidden" name="in_seal_id" id="in_seal_id" />
	<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
	<? if ($addMode!="") {?>
<SCRIPT LANGUAGE="JavaScript">
//window.onLoad = addNewRMProcurmentItem();
//window.onLoad = xajax_generateGatePass();
window.load = xajax_generateReceiptGatePass();

//alert("hii");
</SCRIPT>
<? }?>

	
	
	<SCRIPT LANGUAGE="JavaScript">
	Calendar.setup 
	(	
		{
			inputField  : "date_Of_Entry",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "date_Of_Entry", 
			ifFormat    : "%d/%m/%Y",        // the date format
			singleClick : true,
			step : 1
		}
	);
	
	</script>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplier_Challan_Date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplier_Challan_Date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
</SCRIPT>
	<script>
	function procurementAvlCheck()
	{ var id='';
		var gate_pass_id='';
		var procure_aval = document.getElementById('procure_aval');
		// alert(procure_aval.checked);
		if(procure_aval.checked == true)
		{
			// jQuery('#procurement_not_aval').hide();
			// jQuery('#procurement_aval').show();
			jQuery('#outsealrow').show();
			jQuery('#insealrow').show();
			jQuery('#newsealavlrow').show();
			jQuery('#newsealrow').hide();
			jQuery('#newsealreasonrow').hide();
			var contentDis = xajax_getLoadContent(1,gate_pass_id);
		}
		else
		{
			// jQuery('#procurement_not_aval').show();
			// jQuery('#procurement_aval').hide();
			jQuery('#outsealrow').hide();
			jQuery('#insealrow').hide();
			jQuery('#newsealavlrow').hide();
			jQuery('#newsealrow').show();
			jQuery('#newsealreasonrow').show();
			jQuery('#supplier_display').hide();
			jQuery('#equipment_display').hide();
			jQuery('#chemical_display').hide();
			jQuery('#blocked_seal_details').hide();
			
			var contentDis = xajax_getLoadContent(2,gate_pass_id);
		}
		 //alert(contentDis);
		 setTimeout(function(){displayCal(id);}, 3000);
		
	}
	function displayCal(id)
	{	//alert(id);
		Calendar.setup 
	(	
		{
			/*inputField  : "challan_date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "challan_date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1*/
			inputField  : "challan_date_"+id,         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "challan_date_"+id, 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//alert(inputField);
	}
	function showNewReason(checkedStatus)
	{
		// alert(checkedStatus);
		if(checkedStatus == true)
		{
			jQuery('#newsealrow').show();
			jQuery('#newsealreasonrow').show();
			jQuery('#insealrow').hide();
		}
		else
		{
			jQuery('#newsealrow').hide();
			jQuery('#newsealreasonrow').hide();
			jQuery('#insealrow').show();
		}
	}
	function cancelRMRG(loc)
	{
		window.location = loc;
	}
	function ReceiptValidate()
	{
		var procurment_Gate_PassId = jQuery('#procurment_Gate_PassId').val();
		// alert(procurment_Gate_PassId);
		if(procurment_Gate_PassId == '')
		{
			var seal_No = jQuery('#seal_No').val();
			if(seal_No == '')
			{
				alert('Please enter seal number');
				return false;
			}
		}
		else
		{
			var in_Seal = jQuery('#in_Seal').val();
			var seal_No = jQuery('#seal_No').val();
			if(in_Seal == '' && seal_No == '')
			{
				alert('Please enter in seal or new seal');
				return false;
			}
		}
		if($('#equipmentSize').length>0)
		{
			var eqpSz= jQuery('#equipmentSize').val();
			//alert(eqpSz);
			for(i=0; i<eqpSz; i++)
			{
				var eqpDiff=jQuery('#equipmentDifferenceQuantity_'+i).val();
				if(eqpDiff!=0)
				{
					var	equipmentRemarks=jQuery('#equipmentRemarks_'+i).val();
					if(equipmentRemarks=="")
					{
						alert("Need to add remarks in equipment");
						$('#equipmentRemarks_'+i).focus();
						return false;
					}
				}
			}
		}

		if($('#chemicalSize').length>0)
		{
			var chmSz= jQuery('#chemicalSize').val();
			for(j=0; j<chmSz; j++)
			{
				var chmDiff=jQuery('#chemicalDifferenceQuantity_'+j).val();
				if(chmDiff==0)
				{
					var	chemicalRemarks=jQuery('#chemicalRemarks_'+j).val();
					if(chemicalRemarks=="")
					{
						alert("Need to add remarks in chemical");
						$('#chemicalRemarks_'+j).focus();
						return false;
					}
				}
			}
		}
		
		// return false;
	}
	function setInsealId(in_seal_id)
	{	
	//alert("hii");
			
			jQuery('.allInSeal').show();
		 //sealval=jQuery('#in_seal_id').val(in_seal_id);
			var sealval=document.getElementById('in_Seal').value;
			
			jQuery('#block_seal_'+sealval).hide();
			if(sealval!="")
			{
			jQuery('#new_sealhid').hide();
			}
			else
			{
			jQuery('#new_sealhid').show();
			}
			//alert(sealval);
	}
	function unblockSeals()
	{
	//alert("hii");
		var block_seals = document.getElementsByName('block_seal[]');
		var selectOne = 0;var blockIds = '';
		for(i=0;i<block_seals.length;i++)
		{
			if(block_seals[i].checked == true)
			{
			//alert(i);
			var vs=block_seals[i].value;;
			 jQuery('#block_seal_'+vs).hide();
				selectOne++;
				if(blockIds == '')
				{
					blockIds = block_seals[i].value;
				}
				else
				{
					blockIds+= ','+block_seals[i].value;
				}
				// break;
			}
		}
		if(selectOne == 0)
		{
			alert('Please select atleast one seal no for block');
		}
		else
		{
			//alert("hii");
			xajax_unblockseals(blockIds);
		}
	}
	jQuery(document).ready(function(){
		var procurment_Gate_PassId = '<?php echo $procurment_Gate_PassId;?>';
		// alert(procurment_Gate_PassId);
		if(procurment_Gate_PassId != '')
		{
			document.getElementById('procure_aval').checked = true;
			jQuery('#outsealrow').show();
			jQuery('#insealrow').show();
			jQuery('#newsealavlrow').show();
			jQuery('#newsealrow').hide();
			jQuery('#newsealreasonrow').hide();
			var contentDis = xajax_getLoadContent(1,procurment_Gate_PassId);
			xajax_getReceiptDetails(procurment_Gate_PassId,'<?php echo $in_Seal;?>');
			xajax_getSupplierDetails(procurment_Gate_PassId),'';
			xajax_getEquipmentDetails(procurment_Gate_PassId),'';
			xajax_getChemicalDetails(procurment_Gate_PassId),'';
			// alert('hi');
			// jQuery('#procurment_Gate_PassId').val(procurment_Gate_PassId);
		}
		
		
		var seal_No = '<?php echo $seal_No;?>';
		// alert(procurment_Gate_PassId);
		if(seal_No != '')
		{
			document.getElementById('newsealaval').checked = true;
			jQuery('#newsealrow').show();
			jQuery('#newsealreasonrow').show();
			jQuery('#insealrow').hide();
			//var contentDis = xajax_getLoadContent(1);
			//xajax_getReceiptDetails(procurment_Gate_PassId,'<?php echo $in_Seal;?>');
			// alert('hi');
			// jQuery('#procurment_Gate_PassId').val(procurment_Gate_PassId);
		}
		
		
	});
	function page(fileName)
	{
		
			window.location = fileName;
		
	}
	/*function showNewReason(checkedStatus)
	{
		// alert(checkedStatus);
		if(checkedStatus == true)
		{
			jQuery('#newsealrow').show();
			jQuery('#newsealreasonrow').show();
			jQuery('#insealrow').hide();
		}
		else
		{
			jQuery('#newsealrow').hide();
			jQuery('#newsealreasonrow').hide();
			jQuery('#insealrow').show();
		}
	}*/
	
	</SCRIPT>
	</form>
<? 
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>