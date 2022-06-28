<?php
	require("include/include.php");
	require_once('lib/RMProcurmentOrder_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	

	$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

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
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}


/*$fromDate = '2012-08-21';
$toDate = '2012-08-30';

$dateArray =$rmProcurmentOrderObj->getAllDatesBetweenTwoDates($fromDate, $toDate);

echo  "<pre>";
    print_r($dateArray);
echo "</pre>";*/

	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;
	//if($accesscontrolObj->canGenerate()) $generate=true;	
	/*-----------------------------------------------------------*/
	
// $schedule_dates='2014-08-21';
// $ProcurementRecords= $rmProcurmentOrderObj->fetchAllDriverName($schedule_dates);

// print_r($ProcurementRecords);



	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") {
		$hidEditId = $p["editId"];
	} else {
		$hidEditId = $p["hidEditId"];
	}

	
	

	# Add Stock Issuance Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
		$entryDate=Date("d/m/Y");
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	
	

	
	
	#Add
	if ($p["cmdAdd"]!="" ) {
			
		
		 $procurmentNo = $p["procurmentNo"];
		$selCompanyName		=	$p["selCompanyName"];
		$entryDate		=	mysqlDateFormat($p["entryDate"]);	
		$schedule_date		=	mysqlDateFormat($p["schedule_date"]);
		$number_gen_id =	$p["number_gen_id"];
		
		//$hidDriverTableRowCount    = $p["hidDriverTableRowCount"];
		//$hidVehicleTableRowCount    = $p["hidVehicleTableRowCount"];
		$hidVehicleAndDriverTableRowCount=	$p["hidVehicleAndDriverTableRowCount"];
		$hidSupplierRowCount=$p["hidSupplierRowCount"];
		$harvestingEquipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$harvestingChemicalTableRowCount    = $p["hidHarvestingChemicalTableRowCount"];
	
		//if ($procurmentNo!="")
		//$chkUnique = $rmProcurmentOrderObj->checkUnique($procurmentNos, "");
		//$requestNo		=	$p["requestNo"];
		//$selRMSupplierGroup		=	$p["selRMSupplierGroup"];
		//$driverName =	$p["driverName"];
		//$vehicleNo =	$p["vehicleNo"];
		//$hidTableRowCount		=	$p["hidTableRowCount"];	
		// $hidChemicalRowCount=$p["hidChemicalRowCount"];	
		//$hidStockItemStatus	= 	$p["hidStockItemStatus"];
		
		
		
		
		//
		
			//if ($procurmentNo!="" && $entryDate!="" && $driverName!="" && $vehicleNo!="" && $procurmentNo!="") {
			if ($procurmentNo!="" && $entryDate!="" && $procurmentNo!="" && $schedule_date!="") {
			$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcumentOrder($selCompanyName,$entryDate,$procurmentNo,$schedule_date,$userId);
			//die();
			//$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcumentOrder($selCompanyName,$entryDate,$driverName,$vehicleNo,$procurmentNo,$userId);
			
				if($rmProcurmentRecIns)					
				$lastId = $databaseConnect->getLastInsertedId();
				#------------------------------------------------------------------------------------------------------
				#change status of vehicle and driver 
				//$rmProcurmentVehicleRecIns	=	$rmProcurmentOrderObj->updateVehiclestatus($vehicleNo,$procurmentNo);
				//$rmProcurmentDriverRecIns	=	$rmProcurmentOrderObj->updateDriverstatus($driverName,$procurmentNo);
				#-----------------------------------------------------------------
				# insert last generated procurement number to manage chellan
		
				preg_match('/\d+/', $procurmentNo, $numMatch);
				$lastnum = $numMatch[0];
				$rmlastIdInsert	=$manageChallanObj->lastGeneratedProcurementId($lastnum,$number_gen_id);
				//die();
		# Multiple driver  Adding
						if ($hidVehicleAndDriverTableRowCount>0 ) {
							for ($i=0; $i<$hidVehicleAndDriverTableRowCount; $i++) {
								$status = $p["dStatus_".$i];
								
								if ($status!='N') {
									$vehicleNumber	= addSlash(trim($p["vehicleNumber_".$i]));
									$driverName	= addSlash(trim($p["driverName_".$i]));
									
									# IF SELECT ALL STATE
									if ($lastId!="" &&  $vehicleNumber!="" && $driverName!="" ) {
										
										$driverIns = $rmProcurmentOrderObj->addProcurmentVehicleAndDriver($lastId,$vehicleNumber,$driverName,$schedule_date);
									}  # If 										
								} # Status check ends here
							} # For Loop Ends Here
						} # Table Row Count Ends Here
						//die();	
		# Multiple vehicle  Adding
						/*if ($hidVehicleTableRowCount>0 ) {
							for ($i=0; $i<$hidVehicleTableRowCount; $i++) {
								$status = $p["vStatus_".$i];
								
								if ($status!='N') {
									$vehicleNumber	= addSlash(trim($p["vehicleNumber_".$i]));
									
									# IF SELECT ALL STATE
									if ($lastId!="" && $vehicleNumber!="" ) {
										
										$driverIns = $rmProcurmentOrderObj->addProcurmentVehicle($lastId, $vehicleNumber,$schedule_date	);
									}  # If 										
								} # Status check ends here
							} # For Loop Ends Here
						} # Table Row Count Ends Here*/
									
				
		# Multiple vehicle Chemical Type Adding
						if ($harvestingChemicalTableRowCount>0 ) {
							for ($i=0; $i<$harvestingChemicalTableRowCount; $i++) {
								$status = $p["bStatus_".$i];
								
								if ($status!='N') {
									$HarvestingChemical	= addSlash(trim($p["harvestingChemical_".$i]));
									$harvestingQty			= addSlash(trim($p["Qty_".$i]));
									
									# IF SELECT ALL STATE
									if ($lastId!="" && $HarvestingChemical!="" && $harvestingQty!="") {
										
										$harvestingEquipmentIns = $rmProcurmentOrderObj->addProcurmentChemical($lastId, $HarvestingChemical,$harvestingQty);
									}  # If 										
								} # Status check ends here
							} # For Loop Ends Here
						} # Table Row Count Ends Here
					
					# Multiple vehicle Harvesting Type Adding
						if ($harvestingEquipmentTableRowCount>0 ) {
							for ($i=0; $i<$harvestingEquipmentTableRowCount; $i++) {
							
								$status = $p["Status_".$i];
								
								if ($status!='N') {
									 $harvestingEquipment	= addSlash(trim($p["harvestingEquipment_".$i]));
									
									 $harvestingQty			= addSlash(trim($p["harvestingQty_".$i]));
									
									# IF SELECT ALL STATE
									if ($lastId!="" && $harvestingEquipment!="" && $harvestingQty!="") {
										
										$harvestingEquipmentIns = $rmProcurmentOrderObj->addProcurmentEquipment($lastId, $harvestingEquipment,$harvestingQty);
									}  # If 										
								} # Status check ends here
							} # For Loop Ends Here
						} # Table Row Count Ends Here


				
				
		//////////*****************************chemical and equipment quantity**************************//////////////////		
				
				/*if ($hidTableRowCount>0 ) {
				//echo $hidTableRowCount;
					for ($k=0; $k<$hidTableRowCount; $k++) {
					//echo "aa";
						$status = $p["status_".$k];
						  if ($status!='N') {
						
						$equipmentNameId		=	$p["equipmentName_".$k];
						
						$equipmentQty		=	($p["equipmentQty_".$k]);
						$equipmentIssued		=	($p["equipmentIssued_".$k]);
						$balanceQty		=	$p["balanceQty_".$k];
						//if ($lastId!="" ) {
						if ($lastId!="" && $equipmentNameId!="" && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!="" ) {
						
							$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcurmentEquipment($lastId, $equipmentNameId, $equipmentQty,$equipmentIssued,$balanceQty);
							
						}
					}
				  }
				}*/
				/* if ($hidChemicalRowCount>0 ) {
			
					for ($j=0; $j<$hidChemicalRowCount; $j++) {
						$status = $p["bstatus_".$j];
						  if ($status!='N') {
						
						
						$chemicalNameId		=	$p["chemicalName_".$j];
						$chemicalQty		=	$p["chemicalQty_".$j];
						$chemicalIssued		=	$p["chemicalIssued_".$j];
					
						//$currentStock = $totalQty - $quantity;
							
						if ($lastId!=""  && $chemicalNameId!="" && $chemicalQty!="" && $chemicalIssued!="" ) {
							$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcurmentChemical($lastId, $chemicalNameId,$chemicalQty,$chemicalIssued);
							
						}
					}
				  }
				}*/				
				
			
			  if ($hidSupplierRowCount>0 ) {
			
					for ($j=0; $j<$hidSupplierRowCount; $j++) {
						$status = $p["sstatus_".$j];
						  if ($status!='N') {
						
						
						$supplierName		=	$p["supplierName_".$j];
						//$supplierAddress		=	$p["supplierAddress_".$j];
						$pondName		=	$p["pondName_".$j];
						//$pondAddress		=	$p["pondAddress_".$j];
						//$currentStock = $totalQty - $quantity;
						
						
						if ($lastId!=""  && $supplierName!="" ) {
							$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcurmentSupplier($lastId, $supplierName,$pondName);
													
						//if ($lastId!=""  && $supplierName!="" && $supplierAddress!="" && $pondName!="" && $pondAddress!="") {
							//$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcurmentSupplier($lastId, $supplierName,$supplierAddress,$pondName,$pondAddress);
							
						}
					}
				  }
			  }
			  
			 
			  
			  
			  
			} else if ($chkUnique) $err = " Failed to add procurement order. Please make sure the request number you have entered is not duplicate. ";

			if ($rmProcurmentRecIns) {
				if( $err!="" ) printJSAlert($err);
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddRMProcurment);
				$sessObj->createSession("nextPage",$url_afterAddRMProcurment.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddRMProcurment;
			}
			$rmProcurmentRecIns		=	false;
			$hidEditId 	=  "";
	}
	

	# Edit Stock Issuance
	if ($p["editId"]!="") {
	$i=0;
		$editId			=	$p["editId"];
		$editMode		=	true;
		$procurmentOrderRec	=	$rmProcurmentOrderObj->find($editId);
		$editProcurmentId	=	$procurmentOrderRec[0];	
		$selCompanyName	=	$procurmentOrderRec[1];
		$procurmentNo=$procurmentOrderRec[2];
		$entryDate	=	dateformat($procurmentOrderRec[3]);
		$schedule_date	=	dateformat($procurmentOrderRec[4]);
		$driverRecVals 			= $rmProcurmentOrderObj->fetchAllDriverName($procurmentOrderRec[4],$editProcurmentId);
		$vehicleRecVals 			= $rmProcurmentOrderObj->fetchAllVehicleName($procurmentOrderRec[4],$editProcurmentId);
		//print_r( $driverRecVals);
		
		
		//$procurementIdauto=	$procurmentOrderRec[0];		
		//$procurmentNo		=	$procurmentOrderRec[1];		
		//$selRMSupplierGroup	=	$procurmentOrderRec[2];
		//getSupplierData($sir[2],$procurementId)
		//$supplierRecs 			= $rmProcurmentOrderObj->getfilterSupplierList($selRMSupplierGroup);
		//$pondRecs		= $rmProcurmentOrderObj->getfilterPondList($supplier_name);
		 //$pondRecs[] 			= $rmProcurmentOrderObj->getfilterPondList($supplier_name);
		/*$supplierRecs 			= $rmProcurmentOrderObj->getfilterSupplierList($selRMSupplierGroup);
		 foreach($supplierRecs as $supplierS )
		{
		$supplierNames=	 $supplierS[1];
		$pondRecs[] 			= $rmProcurmentOrderObj->getfilterPondList($supplierNames);
		// foreach($pondRecValue as $pndval)
		// echo $pondRecs=$pndval[1];
		}*/
		//print_r($pondRecs);
		// $supplierAddress	=	$procurmentOrderRec[5];
		// $pondName	=	$procurmentOrderRec[6];
	    // $pondAddress	=	$procurmentOrderRec[7];
		//$entryDate	=	dateformat($procurmentOrderRec[9]);
		//$driverName =	$procurmentOrderRec[2];
		//$vehicleNo =	$procurmentOrderRec[3];
		/*$procurmnentGatePass 			= $rmProcurmentOrderObj->getGatePassForEdit($procurmentNo);
		if(sizeof($procurmnentGatePass) > 0)
		{
			$procurmentGatePassId = $procurmnentGatePass[0];
			$gate_pass_id         = $procurmnentGatePass[1];
			$out_time             = $procurmnentGatePass[2];
			$seal_no              = $procurmnentGatePass[3];
			$out_seal_no          = '';
			$labours              = $procurmnentGatePass[5];
			$supervisor           = $procurmnentGatePass[6];
			// $sealNo               = $procurmnentGatePass[8];
		}*/
		
		// Get procurment Records
		//$procurmentDetailsRecs = $rmProcurmentOrderObj->fetchAllProcurmentEntries($editProcurmentId);
		
		/*
		 foreach($procurmentSupplierRecs as $supplierS )
		{
		$supplierNames=	 $supplierS[1];
		
		$pondRecs[] 			= $rmProcurmentOrderObj->getfilterPondList($supplierNames);
		
		
		}*/
		$procurmentVehicleAndDriverRecs = $rmProcurmentOrderObj->fetchAllProcurmentVehicleAndDriver($editProcurmentId);
		//$procurmentDriverRecs = $rmProcurmentOrderObj->fetchAllProcurmentDriver($editProcurmentId);
		//$procurmentVehicleRecs = $rmProcurmentOrderObj->fetchAllProcurmentVehicle($editProcurmentId);
		$procurmentSupplierRecs = $rmProcurmentOrderObj->fetchAllProcurmentSupplier($editProcurmentId);
		$harvestingChemicalRec=$rmProcurmentOrderObj->fetchAllProcurmentChemical($editProcurmentId);
		$harvestingEquipmentRec =$rmProcurmentOrderObj->fetchAllProcurmentEquipment($editProcurmentId);
		
		//$driverNameRecords= $rmProcurmentOrderObj->fetchAllDriverNameEdit($driverName);
		//$vehicleNumRecords= $rmProcurmentOrderObj->fetchAllVehicleNumberEdit($vehicleNo);
		//$procurmentEquipmentRecs = $rmProcurmentOrderObj->fetchAllProcurmentEquipment($editProcurmentId);
		//$procurmentChemicalRecs = $rmProcurmentOrderObj->fetchAllProcurmentChemical($editProcurmentId);
		//$issuanceRecs = $rmProcurmentOrderObj->fetchAllProcurmentDetails($editProcurmentId);
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {	
//echo "hiii";	
		$procurementId	=	$p["hidProcurmentId"];
		$procurmentOrderRec	=	$rmProcurmentOrderObj->find($procurementId);
		$procurmentNo = $p["procurmentNo"];
		$selCompanyName		=	$p["selCompanyName"];
		$entryDate		=	mysqlDateFormat($p["entryDate"]);
		$schedule_date		=	mysqlDateFormat($p["schedule_date"]);
	
		//$hidDriverTableRowCount    = $p["hidDriverTableRowCount"];
		//$hidVehicleTableRowCount    = $p["hidVehicleTableRowCount"];
		$hidVehicleAndDriverTableRowCount=	$p["hidVehicleAndDriverTableRowCount"];
		$equipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$chemicalTableRowCount	= $p["hidHarvestingChemicalTableRowCount"];	
		$hidSupplierRowCount=$p["hidSupplierRowCount"];

		//last
		//$driverNameold =	$procurmentOrderRec[2];
		//$vehicleNoold =	$procurmentOrderRec[3];
		//$rmProcurmentVehicleRecIns	=	$rmProcurmentOrderObj->updateVehicleRestatus($vehicleNoold);
		//$rmProcurmentDriverRecIns	=	$rmProcurmentOrderObj->updateDriverRestatus($driverNameold);	
		//$driverName =	$p["driverName"];
		//$vehicleNo =	$p["vehicleNo"];
		
		//$rmProcurmentVehicleRecIns	=	$rmProcurmentOrderObj->updateVehiclestatus($vehicleNo,$procurmentNo);
		//$rmProcurmentDriverRecIns	=	$rmProcurmentOrderObj->updateDriverstatus($driverName,$procurmentNo);
		
		//$selRMSupplierGroup		=	$p["selRMSupplierGroup"];
		// $hidTableRowCount		=	$p["hidTableRowCount"];	
		 //$hidChemicalRowCount=$p["hidChemicalRowCount"];	
		//$itemCount		=	$p["hidTableRowCount"];		
		//$procurmentNo		=	$p["procurmentNo"];
	//die();
		//$procurementIdauto = $p["procurementIdauto"];
				
		// if  ($procurementId!=$procurementIdauto ) 
		// {
			// $chkUnique = $rmProcurmentOrderObj->checkUnique($procurmentNo, $hidProcurementNo);
		// }
		
		
		
		if ($procurementId!=""  && $selCompanyName!="" && $procurmentNo!=""  && $entryDate!="" && $schedule_date!="" ) {
		$procurmentRecUptd	=	$rmProcurmentOrderObj->updateProurmentOrder($procurementId, $selCompanyName,$entryDate,$schedule_date,$procurmentNo);
		
			
		
		//if ($procurementId!=""  && $selCompanyName!="" && $procurmentNo!=""  && $entryDate!="" ) {
		//$procurmentRecUptd	=	$rmProcurmentOrderObj->updateProurmentOrder($procurementId, $selCompanyName,$driverName,$vehicleNo,$entryDate,$procurmentNo);
		
		//if ($procurementId!=""  && $selCompanyName!="" && $selRMSupplierGroup!="" && $supplierName!=""  && $entryDate!=""  && !$chkUnique) {
		
			//$procurmentRecUptd	=	$rmProcurmentOrderObj->updateProurmentOrder($procurementId, $procurmentNo, $selCompanyName,$selRMSupplierGroup,$supplierName,$supplierAddress,$pondName,$pondAddress,$driverName,$vehicleNo,$entryDate);
			
			//die;
			
				# ----------------------------Driver master
					for ($l=0; $l<$hidVehicleAndDriverTableRowCount; $l++) {
						$status 	 	 = $p["dStatus_".$l];
						$editProcurmentDriverId  		= $p["editProcurmentDriverId_".$l];
						if ($status!='N') {
							$driverName= $p["driverName_".$l];
							$vehicleNumber= $p["vehicleNumber_".$l];
							//$equipmentQuantity= addSlash(trim($p["harvestingQty_".$l]));
							
							if ($procurementId!="" && $driverName!=""  && $vehicleNumber!="" && $editProcurmentDriverId!="") {
							//echo 'hi';
								$updateDriverRec = $rmProcurmentOrderObj->updateProcurmentVehicleAndDriver($editProcurmentDriverId,$driverName,$vehicleNumber,$schedule_date);
								
							} else if ($procurementId!="" && $driverName!="" && $vehicleNumber!="" && $editProcurmentDriverId=="") {	
								//echo 'test';
								$updateDriverRec =$rmProcurmentOrderObj->addProcurmentVehicleAndDriver($procurementId,$vehicleNumber,$driverName,$schedule_date);
							}
							//die;
						} // Status Checking End

						if ($status=='N' && $editProcurmentDriverId!="") {
							# Check Test master In use
							//$driverMasterInUse = $driverMasterObj->testMethodRecInUse($vehicleTypeId);
							//if (!$driverMasterInUse) 
							$delProcrmentDriverRec = $rmProcurmentOrderObj->delRMProcurmentVehicleAndDriverRec($editProcurmentDriverId);
								
						}
					} // Brand Loop ends here
			
			//die();
			
					/*# ----------------------------Vehicle master
					for ($m=0; $m<$hidVehicleTableRowCount; $m++) {
						$status 	 	 = $p["vStatus_".$m];
						$editProcurmentVehicleId_  		= $p["editProcurmentVehicleId_".$m];
						if ($status!='N') {
							$vehicleNumber= $p["vehicleNumber_".$m];
								
							if ($procurementId!="" && $vehicleNumber!=""  && $editProcurmentVehicleId_!="") {
							//echo 'hi';
								$updateVehicleRec = $rmProcurmentOrderObj->updateProcurmentVehicle($editProcurmentVehicleId_, $vehicleNumber,$schedule_date);
								
							} else if ($procurementId!="" && $vehicleNumber!="" && $editProcurmentVehicleId_=="") {	
								//echo 'test';
								$updateVehicleRec =$rmProcurmentOrderObj->addProcurmentVehicle($procurementId, $vehicleNumber,$schedule_date);
							}
							//die;
						} // Status Checking End

						if ($status=='N' && $editProcurmentVehicleId_!="") {
							# Check Test master In use
							//$driverMasterInUse = $driverMasterObj->testMethodRecInUse($vehicleTypeId);
							//if (!$driverMasterInUse) 
							$delProcrmentVehicleRec = $rmProcurmentOrderObj->delRMProcurmentVehicleRec($editProcurmentVehicleId_);
								
						}
					} // Brand Loop ends here*/
			
			
			//die();
			
			
			# ----------------------------Vehicle equipment
					for ($i=0; $i<$equipmentTableRowCount; $i++) {
						$status 	 	 = $p["Status_".$i];
						$equipmentId  		= $p["equipmentId_".$i];
						if ($status!='N') {
							$equipmentName= addSlash(trim($p["harvestingEquipment_".$i]));
							$equipmentQuantity= addSlash(trim($p["harvestingQty_".$i]));
							
							if ($procurementId!="" && $equipmentName!="" && $equipmentQuantity!="" && $equipmentId!="") {
							//echo 'hi';
								$updateHarvestingEquipmentRec = $rmProcurmentOrderObj->updateProcurmentEquipment($equipmentId, $equipmentName,$equipmentQuantity);
								
							} else if ($procurementId!="" && $equipmentName!="" && $equipmentQuantity!="" && $equipmentId=="") {	
								//echo 'test';
								$harvestingEquipmentIns =$rmProcurmentOrderObj->addProcurmentEquipment($procurementId, $equipmentName,$equipmentQuantity);
							}
							//die;
						} // Status Checking End

						if ($status=='N' && $equipmentId!="") {
							# Check Test master In use
							//$driverMasterInUse = $driverMasterObj->testMethodRecInUse($vehicleTypeId);
							//if (!$driverMasterInUse) 
							$delProcrmentEquipmentRec = $rmProcurmentOrderObj->delRMProcurmentEquipmentRec($equipmentId);
								
						}
					} // Brand Loop ends here
			//die();
					# ----------------------------Vehicle chemical
						for ($i=0; $i<$chemicalTableRowCount; $i++) {
							$status 	 	 = $p["bStatus_".$i];
							$chemicalId  		= $p["chemicalId_".$i];
							if ($status!='N') {
								$chemicalName= addSlash(trim($p["harvestingChemical_".$i]));
								$chemicalQuantity= addSlash(trim($p["Qty_".$i]));
								
								if ($procurementId!="" && $chemicalName!="" && $chemicalQuantity!="" && $chemicalId!="") {
								//echo 'hi';
									$updateHarvestingChemicalRec = $rmProcurmentOrderObj->updateProcurmentChemical($chemicalId, $chemicalName,$chemicalQuantity);
									
								} else if ($procurementId!="" && $chemicalName!="" && $chemicalQuantity!="" && $chemicalId=="") {	
									//echo 'test';
									$harvestingChemicalIns =$rmProcurmentOrderObj->addProcurmentChemical($procurementId, $chemicalName,$chemicalQuantity);
								}
								//die;
							} // Status Checking End

							if ($status=='N' && $chemicalId!="") {
								# Check Test master In use
								//$driverMasterInUse = $driverMasterObj->testMethodRecInUse($vehicleTypeId);
								//if (!$driverMasterInUse)
								$delProcrmentChemicalRec = $rmProcurmentOrderObj->delRMProcurmentChemicalRec($chemicalId);
									
							}
						}
			
			
			
			
			
			
			# ----------------------------Test master
			/*for ($e=0; $e<$hidTableRowCount; $e++) {
			   $status = $p["status_".$e];
			   $rmId  		= $p["rmId_".$e];
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				
				$equipmentName	=	$p["equipmentName_".$e];
				//$chemicalName	=	$p["chemicalName_".$e];
				$equipmentQty	=	$p["equipmentQty_".$e];
				$equipmentIssued	=	$p["equipmentIssued_".$e];
				$balanceQty	=	$p["balanceQty_".$e];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($procurementId!="" && $equipmentName!="" &&  $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!="" && $rmId!="") {
					
						$updateProcurmentEntryRec = $rmProcurmentOrderObj->updateProcurmentEquipment($rmId,$equipmentName,$equipmentQty,$equipmentIssued,$balanceQty);
						
					} else if  ($procurementId!="" &&  $equipmentName!=""  && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!=""  && $rmId=="")  {	
					
						$detailsIns = $rmProcurmentOrderObj->addProcurmentEquipment($procurementId, $equipmentName, $equipmentQty,$equipmentIssued,$balanceQty);
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					//$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					//if (!$testMethodInUse)
					$delProcrmentEquipmentRec = $rmProcurmentOrderObj->delRMProcurmentEquipmentRec($rmId);
						
				}
			}*/ // Test Master Loop ends here
			// print_r($p);die;
			
			 
			//print_r($p); die();
			/*for ($m=0; $m<$hidChemicalRowCount; $m++) {
			//echo "fii";
			//$status = $p["histatus_".$r];
			 $status = $p["bstatus_".$m];
			 $rmId  		= $p["brmId_".$m];
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				$chemicalNameId		=	$p["chemicalName_".$m];
				$chemicalQty		=	$p["chemicalQty_".$m];
				$chemicalIssued		=	$p["chemicalIssued_".$m];
				
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($procurementId!=""  && $chemicalNameId!="" && $chemicalQty!="" && $chemicalIssued!="" && $rmId!="") {
					//echo "hii";
						$updateProcurmentEntryRec = $rmProcurmentOrderObj->updateProcurmentChemical($rmId,$chemicalNameId,$chemicalQty,$chemicalIssued);
						
					} else if($procurementId!=""  && $chemicalNameId!="" && $chemicalQty!="" && $chemicalIssued!="" && $rmId=="" ) {

					//($procurementId!="" &&  $equipmentName!=""  && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!=""  && $rmId=="")  {	
						$detailsIns	=	$rmProcurmentOrderObj->addProcurmentChemical($procurementId, $chemicalNameId,$chemicalQty,$chemicalIssued);
							
						//$detailsIns = $rmProcurmentOrderObj->addProcurmentEquipment($procurementId, $equipmentName, $equipmentQty,$equipmentIssued,$balanceQty);
					}
					//die;
				} // Status Checking End
				//echo $status;
				//echo $rmId;
				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					//$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					//if (!$testMethodInUse)
					$delProcrmentChemicalRec = $rmProcurmentOrderObj->delRMProcurmentChemicalRec($rmId);
						
				}
			} */
			
			 
			
			
			
			for ($g=0; $g<$hidSupplierRowCount; $g++) {
			  $status = $p["sstatus_".$g];
			   $rmId  		= $p["srmId_".$g];
			   //die();
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				$supplierName		=	$p["supplierName_".$g];
				//$supplierAddress		=	$p["supplierAddress_".$g];
				$pondName		=	$p["pondName_".$g];
				//$pondAddress		=	$p["pondAddress_".$g];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					if ($procurementId!=""  && $supplierName!=""   && $rmId!="") {
						
						$updateProcurmentEntryRec = $rmProcurmentOrderObj->updateProcurmentSupplier($rmId,$supplierName,$pondName);
						
						
					} else if($procurementId!=""  && $supplierName!=""  &&  $rmId=="" ) {

					//($procurementId!="" &&  $equipmentName!=""  && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!=""  && $rmId=="")  {	
						$detailsIns	=	$rmProcurmentOrderObj->addProcurmentSupplier($procurementId, $supplierName,$pondName);
								
						
					}
					
					
					
					/*if ($procurementId!=""  && $supplierName!="" && $supplierAddress!="" && $pondName!="" &&  $pondAddress!=""  && $rmId!="") {
						
						$updateProcurmentEntryRec = $rmProcurmentOrderObj->updateProcurmentSupplier($rmId,$supplierName,$supplierAddress,$pondName,$pondAddress);
						
						
					} else if($procurementId!=""  && $supplierName!="" && $supplierAddress!="" && $pondName!="" &&  $pondAddress!="" && $rmId=="" ) {

					//($procurementId!="" &&  $equipmentName!=""  && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!=""  && $rmId=="")  {	
						$detailsIns	=	$rmProcurmentOrderObj->addProcurmentSupplier($procurementId, $supplierName,$supplierAddress,$pondName,$pondAddress);
								
						
					}*/
					//die;
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
				//echo "hii";
					# Check Test master In use
					/*$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					if (!$testMethodInUse)*/ $delProcrmentSupplierRec = $rmProcurmentOrderObj->delRMProcurmentSupplierRec($rmId);
						
				}
				
			} 
			
				#------ chemical
				
			/*	for ($i=0; $i<$hidTableRowCount; $i++) {
			   $status = $p["status_".$i];
			   $rmId  		= $p["rmId_".$i];
			   if ($status!='N') {
				
				$equipmentName	=	$p["equipmentName_".$i];
				//$chemicalName	=	$p["chemicalName_".$i];
				$equipmentQty	=	$p["equipmentQty_".$i];
				$equipmentIssued	=	$p["equipmentIssued_".$i];
				$balanceQty	=	$p["balanceQty_".$i];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($procurementId!="" && $equipmentName!="" && $chemicalName!="" && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!="" && $chemicalQty!="" && $chemicalIssued!="" && $rmId!="") {
					
						$updateProcurmentEntryRec = $rmProcurmentOrderObj->updateProcurmentDetails($rmId, $driverName,$vehicleNo,$equipmentName,$chemicalName,$equipmentQty,$equipmentIssued,$balanceQty,$chemicalQty,$chemicalIssued);
						
					} else if  ($procurementId!="" && $driverName!=""  && $vehicleNo!=""  && $equipmentName!="" && $chemicalName!="" && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!="" && $chemicalQty!="" && $chemicalIssued!="" && $rmId=="")  {	
						
						$detailsIns = $rmProcurmentOrderObj->addProcurmentEntries($procurementId, $driverName, $vehicleNo, $equipmentName, $equipmentQty,$equipmentIssued,$balanceQty, $chemicalName,$chemicalQty,$chemicalIssued);
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					//$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					//if (!$testMethodInUse)
					$delProcrmentDetailsRec = $rmProcurmentOrderObj->delRMProcurmentEntriesRec($rmId);
						
				}
			} // Test Master Loop ends here
			// print_r($p);*/
			//die;
			
		}
		
	
	
		if ($procurmentRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRMProcurmentUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateRMProcurment.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failRMProcurmentUpdate;
		}
		$procurmentRecUptd	=	false;
		$hidEditId 	= "";
	}
	
	# Delete Procurment
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$procurementId	=	$p["delId_".$i];
		//die();
			if ($procurementId!="" ) {
				$procurmentNoVal=$rmProcurmentOrderObj->ProcurmentNumberFetch($procurementId);
				$deleteProcurmentRecs	=	$rmProcurmentOrderObj->deleteProcurmentGroup($procurementId);
				$ProcurmentRecDelt =	$rmProcurmentOrderObj->deleteProcurmentVehicleDetail($procurementId);
				$ProcurmentRecDel =	$rmProcurmentOrderObj->deleteProcurmentSupplier($procurementId);
				$ProcurmentRecDel2 =$rmProcurmentOrderObj->deleteProcurmentEquipment($procurementId);
				$ProcurmentRecDel3 =$rmProcurmentOrderObj->deleteProcurmentChemical($procurementId);
				
				
				//$driver_name=$procurmentNoVal[0];
			  	//$vehicle_number=$procurmentNoVal[1];
				
				//$rmProcurmentVehicleRecIns	=	$rmProcurmentOrderObj->updateVehicleRestatus($vehicle_number);
				//$rmProcurmentDriverRecIns	=	$rmProcurmentOrderObj->updateDriverRestatus($driver_name);	
				//die();
			}
		}
		if ($deleteProcurmentRecs) {
			$sessObj->createSession("displayMsg",$msg_succDelRMProcurment);
			$sessObj->createSession("nextPage",$url_afterDelRMProcurment.$selection);
		} else {
			$errDel	=	$msg_failDelRMProcurment;
		}
		$deleteProcurmentRecs	=	false;
		$hidEditId 	= "";
	}
	
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$procurementId	=	$p["confirmId"];
			if ($procurementId!="") {
				// Checking the selected fish is link with any other process
				$procurmentRecConfirm = $rmProcurmentOrderObj->updateProcurmentconfirm($procurementId);
				$vehicleDetail=$rmProcurmentOrderObj->getVehicleAndDriverDetails($procurementId);
				
				$procurmentOrderRec	=	$rmProcurmentOrderObj->find($procurementId);
				$procurmentNo=$procurmentOrderRec[2];
				$cnt=sizeof($vehicleDetail);
				if($cnt>0)
				{
					 for($j=0; $j<$cnt; $j++)
					 {
						$vehicleid=$vehicleDetail[$j][1];
						$driverid=$vehicleDetail[$j][2];
						$rmProcurmentDriverRecIns	=	$rmProcurmentOrderObj->updateDriverstatus($driverid,$procurmentNo);
					 }
					 $rmProcurmentVehicleRecIns	=	$rmProcurmentOrderObj->updateVehiclestatus($vehicleid,$procurmentNo);
				}
				//print_r($vehicleDetail);
				//die();
			}
			

		}
		if ($procurmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmProcurmentOrder);
			$sessObj->createSession("nextPage",$url_afterDelRMProcurment.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
	$rowCount	=	$p["hidRowCount"];
	for ($i=1; $i<=$rowCount; $i++) {

			$procurementId = $p["confirmId"];
			if ($procurementId!="") {
				#Check any entries exist
				
					$procurmentRecConfirm = $rmProcurmentOrderObj->updateProcurmentReleaseconfirm($procurementId);
					
					$vehicleDetail=$rmProcurmentOrderObj->getVehicleAndDriverDetails($procurementId);
				
				$procurmentOrderRec	=	$rmProcurmentOrderObj->find($procurementId);
				$procurmentNo=$procurmentOrderRec[2];
				$cnt=sizeof($vehicleDetail);
				if($cnt>0)
				{
					 for($j=0; $j<$cnt; $j++)
					 {
						$vehicleid=$vehicleDetail[$j][1];
						$driverid=$vehicleDetail[$j][2];
						$rmProcurmentDriverRecIns	=	$rmProcurmentOrderObj->updateDriverRestatus($driverid);
					 }
					 $rmProcurmentVehicleRecIns	=	$rmProcurmentOrderObj->updateVehicleRestatus($vehicleid);
				}
					
				
			}
		}
		if ($procurmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmProcurmentOrder);
			$sessObj->createSession("nextPage",$url_afterDelRMProcurment.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
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

		$rmProcurementRecords	= $rmProcurmentOrderObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$rmProcurementSize	= sizeof($rmProcurementRecords);
		$fetchAllProcurmentRecs = $rmProcurmentOrderObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllProcurmentRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Stocks
	//$stockRecords		= $stockObj->fetchAllActiveRecords();
	$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	
	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
	
	# List all companies
	$companyRecords	= $rmProcurmentOrderObj->fetchAllCompanyName();
	//$supplierGroup	= $rmProcurmentOrderObj->fetchAllSupplierGroupName();
	//$supplierRecs	= $rmProcurmentOrderObj->fetchAllRecordsActiveSupplierName();
	$pondNameRecords= $rmProcurmentOrderObj->fetchAllPondName();
	//if ($addMode)
	//{
	//$driverNameRecords= $rmProcurmentOrderObj->fetchAllDriverName();
	//$vehicleNumRecords= $rmProcurmentOrderObj->fetchAllVehicleNumber();
	//}
	$supplierNameRecs = $rmProcurmentOrderObj->fetchAllRecordsActiveSupplierName();
	if($p["editId"]=="")
	{
		$ProcurementRecords= $rmProcurmentOrderObj->fetchAllProcurementValue();
		 sizeof($ProcurementRecords);
		if(sizeof($ProcurementRecords)>0)
		{
		//echo "hii";
		foreach($ProcurementRecords as $procurement)
		{
		 $procurementIdauto=$procurement[0]+1;
		}
		}
		else
		{
		$procurementIdauto=1;
		}
	}
	//$procurementIdauto	
	if ($editMode) $heading	=	$label_editRMProcurment;
	else $heading	=	$label_addRMProcurment;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/RMProcrurmentOrder.js"; // For Printing JS in Head section
	
	
	
	$harvestingEquipmentRecs = $harvestingEquipmentMasterObj->fetchAllRecordsActiveequipmentType();
	$harvestingChemicalRecs = $harvestingChemicalMasterObj->fetchAllChemicalRecordsActive();
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	
	
	
	
	
	
?>
	<!--<script language="javascript" type="text/javascript" src="libjs/datetimepicker1.js"></script>-->
	<form id="RMProcurmentOrder" name="RMProcurmentOrder" action="RMProcurmentOrder.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="72%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
		 
			if ($editMode || $addMode) {
			$sealNoList   = $rmProcurmentOrderObj->getAllSealNos($seal_no);
			$employeeList = $rmProcurmentOrderObj->getAllEmployee();
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="92%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="75%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMProcurmentOrder.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdSaveChange" id="cmdSaveChange2" class="button" value=" Save Changes " onClick="return validateProcurment(document.frmRMProcurmentOrder);">
		</td>
	<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMProcurmentOrder.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd2" class="button" value=" Add " onClick="return validateProcurment(document.RMProcurmentOrder);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
											</tr>
											<input type="hidden" name="hidProcurmentId" id="hidProcurmentId" value="<?=$editProcurmentId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
				<tr><TD nowrap><span class="fieldName" style="color:red; line-height:normal" id="message" name="message"></span></TD></tr>
				
				
				
				
			<!--------------------tqble--------------------->	
				<tr>
				
				
				<td colspan="2"><table width="75%" border="0" cellpadding="1" cellspacing="0" align="center">
				
				
				
				
				
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
			<!-- <table width="70%" border="0" cellpadding="4" cellspacing="0" align="left">-->
			<!--<table width="100%" align="center" >-->
			
			<table width="90%" border="0" cellpadding="4" cellspacing="0" align="center" >
			<tr>
				<td align="center" valign="top">
					<?php
						$left_l=true;
						$entryHead = "";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table width="60%" border="0" cellpadding="4" cellspacing="0" align="center">
					<tr>
					<td class="fieldName" nowrap >*Date of Entry</td>
					
						<TD>
							<input type="text" name="entryDate" id="entryDate" size="9" value="<?=$entryDate;?>"  autocomplete="off" />
						</TD>
					</tr>
					<tr>
					<td class="fieldName" nowrap>*Procurment Number:</td>
						<td class="listing-item"><input name="procurmentNo" type="text" id="procurmentNo" size="10" value="<?=$procurmentNo?>"  readonly="readonly" tabindex="1" ></td>
					</tr>
					<tr>				
						<td class="fieldName" nowrap>*Company Name:&nbsp;</td>
                                        
                                        <td class="listing-item">
					<select name="selCompanyName" id="selCompanyName">
                                        <option value="">--select--</option>
     												
										<?php 
										foreach($companyRecords as $cr)
										{
						$companyId		=	$cr[0];
						$companyName	=	stripSlash($cr[1]);
						$selected="";
						if($selCompanyName==$companyId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$companyId?>" <?=$selected?>><?=$companyName?></option>
                                                    <? }
										
										
										?>
                                                  </select></td>
												  <input type="hidden" name="procurementIdauto" id="procurementIdauto" size="9" value="<?=$procurementIdauto;?>" readonly="readonly"  />
												  <input type="hidden" name="number_gen_id" id="number_gen_id" size="9" value="<?=$number_gen_id;?>" readonly="readonly"  />
												  
						</tr>
						<tr>
						<td class="fieldName" nowrap>*Schedule date:</td>
							<td class="listing-item"><input name="schedule_date" type="text" id="schedule_date" size="10" onchange="return CheckSchedule(<?=$mode;?>,document.getElementById('hidVehicleAndDriverTableRowCount').value);" <?php /*onchange ="xajax_rmProcurmentScheduleDriverAndVehicleDetails(document.getElementById('schedule_date').value,'0','','','',document.getElementById('hidVehicleAndDriverTableRowCount').value,'<?=$mode;?>');"*/?>  value="<?=$schedule_date?>" tabindex="1"></td>
						
							<input type="hidden" name="hide_schedule_date" id="hide_schedule_date" size="9" value="<?=$schedule_date;?>" readonly="readonly"  />
						
						
						</tr>
					</table>
					<?php
			require("template/rbBottom.php");
		?>
				</td>
				<td width="20%"></td>
				<td  valign="top">
					
					<table>
						<tr><td colspan="4"><table width="20%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblVehicleAndDriver" name="tblVehicleAndDriver">
									<tr bgcolor="#f2f2f2" align="center">
									<td class="listing-head" nowrap>Vehicle number </td>
										
										<td class="listing-head" nowrap>Driver name </td>
										
										<td></td>
									</tr>
								<?php if (sizeof($procurmentVehicleAndDriverRecs)>0){
								$hidTableRowCountedit=sizeof($procurmentVehicleAndDriverRecs);
								$p=0;
							
								foreach($procurmentVehicleAndDriverRecs as $procurementDetail) {	
								$editDriverProcurmentId 	= $procurementDetail[0];
								$vehicleid	=$procurementDetail[1];
								$driverid	=$procurementDetail[2];
								
								?>
							
									<tr id="dRow_<?=$p?>" class="whiteRow" align="center">
										<td id="srNo_<?=$p?>" class="listing-item" align="center">
										<select id="vehicleNumber_<?=$p?>" name="vehicleNumber_<?=$p?>">
											<?php 	
												foreach($vehicleRecVals as $veh=>$vehcle)
												{
						
														//alert($sr[0]);
													$vehicleNameId		=	$veh;
													$vehicleValue	=	$vehcle;
													$sel  = ($vehicleid==$vehicleNameId)?"Selected":"";
											?>
											<option value="<?=$vehicleNameId?>" <?=$sel?>><?=$vehicleValue?></option>
											<?
												}
											?>
											
										</select>
										</td>
										<td>
										<select id="driverName_<?=$p?>" name="driverName_<?=$p?>">
											<?php 	
											foreach($driverRecVals as $der=>$des)
											{
					
													//alert($sr[0]);
												$driverNameId		=	$der;
												$driverValue	=	$des;
												$sel  = ($driverid==$driverNameId)?"Selected":"";
									?>
									<option value="<?=$driverNameId?>" <?=$sel?>><?=$driverValue?></option>
																<?
																}
											?>
																		
										</select>
										
										</td>
										<td class="listing-item" align="center">
											<a onclick="setTestRowVehicleAndDriverStatus('<?=$p?>');" href="###">
											<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
											</a>
											<input id="dStatus_<?=$p?>" type="hidden" value="" name="dStatus_<?=$p?>">
											<input id="dIsFromDB_<?=$p?>" type="hidden" value="N" name="dIsFromDB_<?=$p?>">
											<input id="editProcurmentDriverId_<?=$p?>" type="hidden" value="<?=$editDriverProcurmentId?>" name="editProcurmentDriverId_<?=$p?>">
											<input id="driverValId_<?=$p?>" type="hidden" value="<?=$driverid?>" name="driverValId_<?=$p?>">
											<input id="vehicleValId_<?=$p?>" type="hidden" value="<?=$vehicleid?>" name="vehicleValId_<?=$p?>">
											<input type='hidden' name="hidTableRowCountedit" id="hidTableRowCountedit" value="<?=$hidTableRowCountedit?>">
										</td>
									</tr>
									
								<?php
								$p++;	
								}
								}
								?>
								<input type='hidden' name="hidTableRowCountedit" id="hidTableRowCountedit" value="<?=$p?>">	
						</table></td></tr>
						<input type='hidden' name="hidVehicleAndDriverTableRowCount" id="hidVehicleAndDriverTableRowCount" value="<?=$p?>">
						<tr >
						<TD style="padding-left:5px;padding-right:5px;">
										<!--<a href="###" id='addRow' onclick="javascript:addNewVehicleAndDriver();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New </a>-->
						</TD>
						<TD style="padding-left:5px;padding-right:5px;">
										<a href="###" id='addRow' onclick="javascript:addNewVehicleAndDriverCopy();"  class="link1" title="Click here to duplicate value."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New<!--(Copy)--></a>
						</TD>
						
						</tr>
				
				
				</table>
					
					
					
					
					
				
				
				
				
				
				
				
				
				
				</td>
				<?php 
				
				/*
				<td align="left" valign="top">
					<?php
					
					$left_l=true;
					$entryHead = "";
					$rbTopWidth = "";
					require("template/rbTop.php");
					
				?>
					<table width="60%" border="0" cellpadding="4" cellspacing="0" align="left" >
					<tr>
					<td nowrap class="fieldName" >*Driver Name</td>
					<td nowrap>
					<!--<INPUT TYPE="text" NAME="supplierAddress" id="supplierAddress" size="15" value="<?=$supplierGroupName;?>">	-->
						<select name="driverName" id="driverName">
						<option value="">-- Select --</option>
						<?php 
										foreach ($driverNameRecords as $sr) 
										{
						$driverNameId		=	$sr[0];
					$driverNameValue	=	stripSlash($sr[1]);
					$selected="";
						if($driverName==$driverNameId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$driverNameId?>" <?=$selected?>><?=$driverNameValue?></option>
                                                    <? }
										
										
										?>
						
						</select>
					</td>
					</tr>
					
					<tr>
					<td nowrap class="fieldName" >*Vehicle Number</td>
					<td nowrap>
					<!--<INPUT TYPE="text" NAME="supplierAddress" id="supplierAddress" size="15" value="<?=$supplierGroupName;?>">	-->
						<select name="vehicleNo" id="vehicleNo" <?php /*onchange="xajax_getDetails(document.getElementById('vehicleNo').value,'','0',''); xajax_getDetailvalue(document.getElementById('vehicleNo').value,'','0','');"*/?><?php /*>
						
						
						<option value="">-- Select --</option>
						<?php 
										foreach ($vehicleNumRecords as $vh)
										{
						$vehicleNumberId		=	$vh[0];
					$vehicleNumber	=	stripSlash($vh[1]);
					$selected="";
						if($vehicleNo==$vehicleNumberId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$vehicleNumberId?>" <?=$selected?>><?=$vehicleNumber?></option>
                                                    <? }
										
										
										?>
						
						</select>
					</td>
		</tr>
					</table>
					<?php
			require("template/rbBottom.php");
		?>
				</td>
				
				<?php 
				*/?>
			</tr>	
			</table>
			
			
			
			

				</td>
				
	

				</tr>
				
				
				
					<tr>
					
					
					
			<td align="center">
			
			
			
			
			
			
			
			<table>
			<tr>
			<?php /*<td>
				<table>
				<tr><td><table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblDriver" name="tblDriver">
							<tr bgcolor="#f2f2f2" align="center">
								<td class="listing-head" nowrap>Driver name </td>
								
								<td></td>
							</tr>
						<?php if (sizeof($procurmentDriverRecs)>0){
						$p=0;
					
						foreach($procurmentDriverRecs as $drv) {	
						$editDriverProcurmentId 	= $drv[0];
						$driverid	=$drv[1];
						$driverName	=$drv[2];
						
						?>
					
							<tr id="dRow_<?=$p?>" class="whiteRow" align="center">
								<td id="srNo_<?=$p?>" class="listing-item" align="center">
								<select id="driverName_<?=$p?>" name="driverName_<?=$p?>">
									<?php 	
									foreach($driverRecVals as $der=>$des)
									{
			
											//alert($sr[0]);
										$driverNameId		=	$der;
										$driverValue	=	$des;
										$sel  = ($driverid==$driverNameId)?"Selected":"";
							?>
							<option value="<?=$driverNameId?>" <?=$sel?>><?=$driverValue?></option>
														<?
														}
									?>
								
								
								
								
								</select>
								</td>
								<td class="listing-item" align="center">
									<a onclick="setTestRowDriverStatus('<?=$p?>');" href="###">
									<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
									</a>
									<input id="dStatus_<?=$p?>" type="hidden" value="" name="dStatus_<?=$p?>">
									<input id="dIsFromDB_<?=$p?>" type="hidden" value="N" name="dIsFromDB_<?=$p?>">
									<input id="editProcurmentDriverId_<?=$p?>" type="hidden" value="<?=$editDriverProcurmentId?>" name="editProcurmentDriverId_<?=$p?>">
								</td>
							</tr>
							
						<?php
						$p++;	
						}
						}
						?>
							
				</table></td></tr>
				<input type='hidden' name="hidDriverTableRowCount" id="hidDriverTableRowCount" value="<?=$p?>">
				<tr><TD style="padding-left:5px;padding-right:5px;">
								<a href="###" id='addRow' onclick="javascript:addNewDriver();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD></tr>
				
				
				</table>
					
				</td>
				<td width="8%"></td>
				<td><table>
				<tr><td><table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblVehicle" name="tblVehicle">
                	<tr bgcolor="#f2f2f2" align="center">
						<td class="listing-head" nowrap>Vehicle number </td>
						<td></td>
                	</tr>
					<?
					
					if (sizeof($procurmentVehicleRecs)>0){
							$d=0;
							foreach($procurmentVehicleRecs as $vehs) {	
							$editVehicleProcurmentId 	= $vehs[0];
							$vehicleid	=$vehs[1];
							$vehicleName	=$vehs[2];
					?>
						<tr id="vRow_<?=$d?>" class="whiteRow" align="center">
							<td id="srNo_<?=$d?>" class="listing-item" align="center">
							<select id="vehicleNumber_<?=$d?>" name="vehicleNumber_<?=$d?>">
								<?php 	
									foreach($vehicleRecVals as $veh=>$vehcle)
									{
			
											//alert($sr[0]);
										$vehicleNameId		=	$veh;
										$vehicleValue	=	$vehcle;
										$sel  = ($vehicleid==$vehicleNameId)?"Selected":"";
								?>
									<option value="<?=$vehicleNameId?>" <?=$sel?>><?=$vehicleValue?></option>
									<?
														}
									?>
								
							</select>
							</td>
							<td class="listing-item" align="center">
							<a onclick="setTestRowVehicleStatus('<?=$d?>');" href="###">
							<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
							</a>
							<input id="vStatus_<?=$d?>" type="hidden" value="" name="vStatus_<?=$d?>">
							<input id="vIsFromDB_<?=$d?>" type="hidden" value="N" name="vIsFromDB_<?=$d?>">
							<input id="editProcurmentVehicleId_<?=$d?>" type="hidden" value="<?=$editVehicleProcurmentId?>" name="editProcurmentVehicleId_<?=$d?>">
							</td>
						</tr>
					<?php		
					$d++;
					}
				} 
				?>	
				</table></td></tr>
					<input type='hidden' name="hidVehicleTableRowCount" id="hidVehicleTableRowCount" value="<?=$d?>">
				<tr><TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3"  >
				<a href="###" id='addRow' onclick="javascript:addNewVehicle();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD>		
						</tr>
					
					
				</table></td>
				*/
				?>
			</tr>
			</table>
			

			</td>
			</tr>
				
				
				
				
				
				
				
				
				
						<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
				
				<tr align="center">
			<td >
			<table><tr align="center"><td>	
			<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddProcurmentOrderSupplier" name="tblAddProcurmentOrderSupplier">
				<tr bgcolor="#f2f2f2" align="center">
						
						<td class="listing-head" nowrap>Supplier name </td>
						<td class="listing-head" nowrap>Supplier Group</td>
						<td class="listing-head" nowrap>Farm Name </td>
						<td class="listing-head" nowrap>Farm Location </td> 
						<td class="listing-head" nowrap>PHT Certificate Available and balance Qty </td>
						<!--<td class="listing-head" nowrap>Farm Registration </td>
						<td class="listing-head" nowrap>Pond Size </td>
						<td class="listing-head" nowrap>Total Qty  </td>-->
						<td></td>
				</tr>
				<?
				if (sizeof($procurmentSupplierRecs)>0) {
				   $n=0;
					 foreach ($procurmentSupplierRecs as $sp) {				
							 $id=$sp[0];
							$selSupplierId=$sp[1];
							$selPondId=	$sp[2];
							 //$editProcurmentId	=	$sp[1];
							 
							 $supplierGroup=$rmProcurmentOrderObj->getSupplierGroupDetails($selSupplierId);
							 //$supplierGroup=$rmProcurmentOrderObj->getSupplierGroupId($editProcurmentId);
							 $supplierGroupNm=$supplierGroup[0][1];
							$pondLocationRecs 			= $rmProcurmentOrderObj->filterPondLocationList($selPondId);
							$pondlocation=$pondLocationRecs[1]; 	
							$pondQuantityRecs 			= $rmProcurmentOrderObj->filterPondQtyList($selPondId);
							if(sizeof($pondQuantityRecs)>0)
							{
								foreach($pondQuantityRecs as $pondQuantity )
								{
									$pondQnty+=$pondQuantity[1];
								}
								$pondvalue="Yes".' ('.$pondQnty.')';
							}
							else
							{
								$pondvalue="No";
							}
					
							$pondRecs 			= $rmProcurmentOrderObj->getfilterPondList($selSupplierId);
							//echo sizeof($pondRecs);
		?>
				<tr class="whiteRow" id="srow_<?=$n?>">
					<td align="left" class="fieldName">
						<select onchange="xajax_rmProcurmentSupplierGroup(document.getElementById('supplierName_<?=$n?>').value,<?=$n?>,''); " tabindex="1" id="supplierName_<?=$n?>" style="display:display;" name="supplierName_<?=$n?>">
							<option value="">--select--</option>
							<?php 
								foreach($supplierNameRecs as $sr)
								{
									$supplierNameId		=	$sr[0];
									$supplierNameValue	=	stripSlash($sr[1]);
									$sel  = ($selSupplierId==$supplierNameId)?"Selected":"";
							?>
							<option value="<?=$supplierNameId?>" <?=$sel?>><?=$supplierNameValue?></option>";
							<?}?>
						</select>
					</td>
					<td align="center" class="fieldName">
						<input type="text" style="text-align:right; border:none;" readonly="" size="15" value="<?=$supplierGroupNm?>" id="supplierGroup_<?=$n?>" name="supplierGroup_<?=$n?>">
					</td>
					<td align="center" class="fieldName">
					
						<select onchange="xajax_rmProcurmentPondDetails(document.getElementById('pondName_<?=$n?>').value,<?=$n?>); " tabindex="1" id="pondName_<?=$n?>" style="display:display;" name="pondName_<?=$n?>">
							<option value="">--select--</option>
							<?php 	
								foreach($pondRecs as $pnd)
								{
										
										//alert($sr[0]);
									$pondNameId		=	$pnd[1];
									$pondNameValue	=	stripSlash($pnd[2]);
									$sel  = ($selPondId==$pondNameId)?"Selected":"";
						?>
						<option value="<?=$pondNameId?>" <?=$sel?>><?=$pondNameValue?></option>";
                                                    <?
													}
								?>
						</select>
					</td>
					<td align="center" class="fieldName">
						<input type="text" value="<?=$pondlocation?>" tabindex="2" style="text-align:right; border:none;" readonly="" size="15" id="pondLocation_<?=$n?>" name="pondLocation_<?=$n?>">
					</td>
					<td align="center" class="fieldName">
						<input type="text" value="<?=$pondvalue?>" tabindex="2" style="text-align:right; border:none;" readonly="" size="15" id="pondQty_<?=$n?>" name="pondQty_<?=$n?>">
					</td>
					<td align="center" class="fieldName">
						<a onclick="setIssuanceSupplierStatus('<?=$n?>');" href="###">
							<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
						</a>
						<input type="hidden" value="" id="sstatus_<?=$n?>" name="sstatus_<?=$n?>">
						<input type="hidden" value="N" id="IsFromDB_<?=$n?>" name="IsFromDB_<?=$n?>">
						<input type="hidden" value="<?=$id?>" id="srmId_<?=$n?>" name="srmId_<?=$n?>">
					</td>
				</tr>
			<?
			$n++;
			}
			}
			?>				
			</table>
			</td></tr>
					<input type="hidden" name="hidSupplierRowCount" id="hidSupplierRowCount" value="<?=$n?>">																									<tr><TD height="10"></TD></tr>
					
			<tr><td width="40%" valign="top" colspan="3" >
				<a href="###" id='addRow' onclick="javascript:addNewRMProcurmentSupplier();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD></tr>	
			</table>

			</td>
			</tr>
	
				
				
				
				
				
				
					
				
				
				

				
			
				<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	
	<tr>
			<td align="center">
			<table>
			<tr>
			<td valign="top">
				<table>
				<tr><td ><table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblHarvestingEquipment" name="tblAddProcurmentOrder">
												<tr bgcolor="#f2f2f2" align="center">
														
														<td class="listing-head" nowrap>Equipment name </td>
														<td class="listing-head" nowrap>Required quantity</td>
														
														
											<td></td>
												</tr>
									
				</table></td></tr>
				<tr><TD style="padding-left:5px;padding-right:5px;">
								<a href="###" id='addRow' onclick="javascript:addNewHarvestingEquipment();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD></tr>
				<input type='hidden' name="hidHarvestingEquipmentsTableRowCount" id="hidHarvestingEquipmentsTableRowCount" value="">
				
				</table>
					
				</td>
				<td width="8%"></td>
				<td valign="top"><table>
				<tr><td><table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblHarvestingChemical" name="tblHarvestingChemical">
                	<tr bgcolor="#f2f2f2" align="center">
							
                     		<td class="listing-head" nowrap>Chemical name </td>
                     		<td class="listing-head" nowrap>Required quantity(Kgs)</td>
                     		
							
							
							
				<td></td>
                	</tr>
		
				</table></td></tr>
				<tr><TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3"  >
				<a href="###" id='addRow' onclick="javascript:addNewHarvestingChemical();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD>		
						</tr>
						<input type='hidden' name="hidHarvestingChemicalTableRowCount" id="hidHarvestingChemicalTableRowCount" value="">
					
				</table></td>
			</tr>
			</table>
			

			</td>
			</tr>
	
	
	</table></td>
	
	</tr>
	<!--------------------------------------table--------------------------->
	
	
	




			
				
				
				
				<!--<tr>
					<td width="40%" valign="top" colspan="3" align="center">
						<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddProcurmentOrder" name="tblAddProcurmentOrder">
								<tr bgcolor="#f2f2f2" align="center">
										
										<td class="listing-head" nowrap>Equipment name </td>
										<td class="listing-head">Max no: of equipment</td>
										<td class="listing-head">Equipment Issued </td>
										<td class="listing-head">Difference </td>
										
										
							<td></td>
								</tr>
					
					</table>
					<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
				<tr><TD height="10"></TD></tr>
					</td>
				</tr>
				<tr><TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3"  >
						<a href="###" id='addRow' onclick="javascript:addNewRMProcurmentItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
					</TD>
				</tr>-->	

<tr><TD height="10"></TD></tr>



<!--<tr>
<td width="40%" valign="top" colspan="3" align="center">
			<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddProcurmentChemicalOrder" name="tblAddProcurmentChemicalOrder">
                	<tr bgcolor="#f2f2f2" align="center">
							
                     		<td class="listing-head" nowrap>Chemical name </td>
                     		<td class="listing-head">Max Qty</td>
                     		<td class="listing-head">Quantity Issued </td>
							
							
							
				<td></td>
                	</tr>
		
		</table>
	<input type="hidden" name="hidChemicalRowCount" id="hidChemicalRowCount" value="<?=$rowSize?>">
			<tr><TD height="10"></TD></tr>
			
						
</td>
</tr>
<tr><TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3"  >
				<a href="###" id='addRow' onclick="javascript:addNewRMProcurmentChemicalItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
			</TD></tr>-->
											
												
						
                                              <!--</table>
											  </td>
					  
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>-->

					
					
	<!--<tr>
		<TD>
			<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddProcurmentOrder">
                	<tr bgcolor="#f2f2f2" align="center">
							
                     		<td class="listing-head" nowrap>Equipment name </td>
                     		<td class="listing-head">Max no: of equipment</td>
                     		<td class="listing-head">Equipment Issued </td>
							<td class="listing-head">Difference </td>
							<td class="listing-head" nowrap>Chemical name </td>
                     		<td class="listing-head">Max no: of chemical</td>
                     		<td class="listing-head">Chemical Issued </td>
							
				<td></td>
                	</tr>
		</table>
		</TD>
	</tr>
<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
<tr><TD height="10"></TD></tr>
<tr><TD nowrap style="padding-left:5px; padding-right:5px;">
	<a href="###" id='addRow' onclick="javascript:addNewRMProcurmentItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
</TD></tr>-->

	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	
	
	
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMProcurmentOrder.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdSaveChange" id="cmdSaveChange2" class="button" value=" Save Changes " onClick="return validateProcurment(document.frmRMProcurmentOrder);">
		</td>
	<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMProcurmentOrder.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd2" class="button" value=" Add " onClick="return validateProcurment(document.RMProcurmentOrder);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="98%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;RM Procurment Order  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From:</td>
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
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmProcurementSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintRMProcurementOrderAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?>
												<!--<?/* if($del==true && $edit==true){?><td><input type="submit" value=" Generate Gate Pass " name="cmdGenerate" class="button" onClick="return confirmGenerate(this.form,'delId_',<?=$rmProcurementSize;?>);">&nbsp;</td><?php } */?>-->
												
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
									<td colspan="2" >
										<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($rmProcurementRecords) > 0 )
												{
													$i	=	0;
											?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="11" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RMProcurmentOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RMProcurmentOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RMProcurmentOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" >
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Entry Date</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Procument Number</td>
		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Group</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Farm Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Vehicle Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>PROC GATE PASS</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap width="30px"></td>
		
		<!--<td class="listing-head"></td>-->
		<? if($confirm==true && ($manageconfirmObj->procumentOdrConfirmEnabled())){?>
                        <td class="listing-head">&nbsp;</td>
			<? }?>
		<!--<td class="listing-head"></td>-->
		<? if($edit==true){?>
		<!--<td class="listing-head"></td>-->
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($rmProcurementRecords as $sir) {
	
		$i++;
		 $procurementId	=	$sir[0];
		  $procurementNo	=	$sir[2];
		 //$supplierGroup		=$sir[9];
		 $supplierGroup		=$sir[6]; 
		$supplierData	=	$rmProcurmentOrderObj->getSupplierDetails($procurementId);
		$supplierVal=$supplierData[0][0];
		$supplierGroupData	=	$rmProcurmentOrderObj->getSupplierGroupDetails($supplierVal);
		$supplierGroup		=$supplierGroupData[0][1]; 
		$pondData	=	$rmProcurmentOrderObj->getPondDetails($procurementId);
		//$supplierData	=	$rmProcurmentOrderObj->getSupplierData($sir[1],$procurementId);
		//$vehicleNo	=	$sir[7];
		//$driverName	=	$sir[8];
		$VehicleData	=	$rmProcurmentOrderObj->getVehicleAndDriverDetails($procurementId);
		$vehicleid=$VehicleData[0][1];
		$checkActive=$rmProcurmentOrderObj->checkActiveExist($vehicleid);
		$vehicleValueExist=$checkActive[0];
		//$nextgenerate	=	$rmProcurmentOrderObj->displayGenerate($vehicleid);
		//$nextScheduleDate=$nextgenerate[0];
		$gatePassVal=$rmProcurmentOrderObj->getGatePass($procurementId);
		//$vehicleNo	=	$sir[10];
		//$driverName	=	$sir[11];
		//$procurmentNo		=	$sir[1];
		//$department		=	$sir[2];
		// $selCompanyName		=	$companydetailsObj->find($sir[2]);
		// $companyId		=	$selCompanyName[0];
		// $companyName		=	stripSlash($selCompanyName[1]);
		
		
		
		
		// $selRMSupplierGroup = $supplierGroupObj->find($sir[3]);
		// $supplierGroupId		=	$selRMSupplierGroup[0];
		//$supplierGroup		=	stripSlash($selRMSupplierGroup[1]);
		
		// $supplierName = $supplierMasterObj->find($sir[4]);
		// $supplierId		=	$supplierName[0];
		// $supplier		=	stripSlash($supplierName[2]);
		// $supplierAddress		=	stripSlash($supplierName[3]);
		
		// $pondName = $pondMasterObj->find($sir[6]);
		// $pondNameId		=	$pondName[0];
		// $pondNamee		=	stripSlash($pondName[1]);
		// $pondAddress		=	stripSlash($pondName[4]);
		// $alloteName=stripSlash($pondName[3]);
		// $regNumber=stripSlash($pondName[11]);
		// $regDate=dateformat($pondName[12]);
		// $expDate=dateformat($pondName[13]);
		
		// $details="Pond Address:$pondAddress<br>";
		// $details.="Allotee Name:$alloteName<br>"; 
		// $details.="Registration Number:$regNumber<br>";
		// $details.="Registration Date:$regDate<br>";
		// $details.="Expiry Date:$expDate<br>";
		
		//$vehicle = $vehicleMasterObj->find($sir[9]);
		
		
		// $driver=$driverMasterObj->find($sir[8]);
		 // $driverName	=	$driver[1];
		
		 $equipment= $rmProcurmentOrderObj->getEquipment($sir[0]);
		  // $equipmentId=$equipment[1];
		 // $getEquipmentName=$rmProcurmentOrderObj->getharvestingEquipment($equipmentId);
		
		 // $equipmentName=$getEquipmentName[0];
		 // $equipmentIssued=$equipment[3];
		 $chemical= $rmProcurmentOrderObj->getChemical($sir[0]);
		
		// $chemicalId=$chemical[1];
		// $getChemicalName=$rmProcurmentOrderObj->getharvestingChemical($chemicalId);
		// $chemicalName=$getChemicalName[0];
		// $chemicalIssued=$chemical[3];
		
		
		
		 $entryDate		= dateFormat($sir[3]);
		 
		  $active=$sir[4];
		  $generated=$sir[5];
		//$existingrecords=$sir[10];
		$generatedCount = $sir[9];
		$existingrecords=$sir[11];
		$schedule_date=$sir[10];
		
	?>
	<tr  bgcolor="WHITE">
		<td width="20">
		<?php
			//if($generatedCount == 0 || $active != 1)
			//if($generatedCount == 0 && $active ==1 )
			if($generated == 0  )
			{
		?>
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$procurementId;?>" class="chkBox">
				
		<?php		
			}
			else
			{
		?>
				<input type="checkbox" disabled name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$procurementId;?>" class="chkBox">
		<?php
			}
			echo '<input type="hidden" name="generated_count_'.$i.'" id="generated_count_'.$i.'" value="'.$generatedCount.'" />';
			echo '<input type="hidden" name="procurementNo_'.$i.'" id="procurementNo_'.$i.'" value="'.$procurementNo.'" />';
		?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$entryDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$procurementNo;?></td>
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierGroup;?></td>
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
		 <?php
			$numLine = 3;
		//echo	sizeof($supplierData);
			if (sizeof($supplierData)>0) {
				$nextRec = 0;						
				foreach ($supplierData as $cR) {
					$name=$cR[1];
					$address=$cR[2];	
					//$supplier = $cR[1];
					// $supName=$supplierGroupObj->getSupplierName($supplier);
						// $name=$supName[0];
						//address=$supName[1];						
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
				foreach ($pondData as $cR) {
						$supplierPond = $cR[1];
						//$pond = $cR[3];	
						$alloteName=$cR[2];
						
						// $supPond=$supplierGroupObj->getSupplierPond($pond);
						// $supplierPond=$supPond[0];
						// $alloteName=$supPond[1];
						//$regNumber=$supPond[2];
						//$regDate=dateformat($supPond[3]);
						//$expDate=dateformat($supPond[4]);
						
						$details="Allotee Name:$alloteName<br>";
						
						//$details.="Registration Number:$regNumber<br>";
						//$details.="Registration Date:$regDate<br>";
						//$details.="Expiry Date:$expDate<br>";
						
						
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
						// $supPond=$supplierGroupObj->getSupplierPond($pond);
						// $supplierPond=$supPond[0];
						// $alloteName=$supPond[1];
						//$regNumber=$supPond[2];
						//$regDate=dateformat($supPond[3]);
						//$expDate=dateformat($supPond[4]);
						
						//$details="Allotee Name:$alloteName<br>";
						
						//$details.="Registration Number:$regNumber<br>";
						//$details.="Registration Date:$regDate<br>";
						//$details.="Expiry Date:$expDate<br>";
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
				//echo "hii";
				//$chemicalId=$chem[1];
				//$getChemicalName=$rmProcurmentOrderObj->getharvestingChemical($chemicalId);
				
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
				//echo "hii";
				//$chemicalId=$chem[1];
				//$getChemicalName=$rmProcurmentOrderObj->getharvestingChemical($chemicalId);
				
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
		
		//$vehicledetails="Driver Name:$driverName<br>";
		//$vehicledetails.="Equipment Name(Equipment Issued):$equipmentName<br>"; 
		//$vehicledetails.="Equipment Issued:$equipmentIssued<br>";
		//$vehicledetails.="Chemical Name(Qty Issued):$chemicalName<br>";
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
		<?php /*<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<!--<a href="javascript:printWindow('ViewRmProcurmentOrderDetails.php?procurmentId=<?=$procurementId?>&supplierGroup=<?=$supplierGroup?>&supplier=<?=$supplier?>&pondNamee=<?=$pondNamee?>',700,600)" class="link1" title="Click here to view details.">View Details</a>-->
			<? 
			 if($confirm==true && $generated=="0")
			{
				if ($active==1)
				{
					?>
					<input type="button" value="Generate" onClick="return page('RMProcurmentGatePass.php?procurementId=<?=base64_encode($procurementId)?>');">
				<?
			
				
				}
				
			}
			else
				{
					
				}
			
			if($generated!="0")
			{
			?><a href="RMProcurmentGatePass.php?procurementId=<?=base64_encode($procurementId);?>"><?=$gatePassVal[0];?> <!--Generate--> </a><?
			}
			
			?>
			<!--<a href="RMProcurmentGatePass.php?gate_pass_id=<?=base64_encode($procurmentNo);?>"> Generate </a>-->
		</td>*/?>
		<td align="center"><a title="Click here to view details." class="link1" href="javascript:printWindow('PrintRMProcurementOrder.php?id=<?php echo $sir[0];?>',900,750)">Print </a>
		</td>
		<? if ($confirm==true && ($manageconfirmObj->procumentOdrConfirmEnabled())){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?>  <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if($vehicleid!=$vehicleValueExist)
			{
			if ($active=="0" || $active==""){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$procurementId;?>,'confirmId');" >
			<?php } } else if ($active==1 && $generated!="1"){  if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$procurementId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>
		
		<!--<td>
		<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('ViewRmProcurmentOrderDetails.php?procurmentId=<?=$procurementId?>&supplierGroup=<?=$supplierGroup?>&supplier=<?=$supplier?>&pondNamee=<?=$pondNamee?>',700,600);"><? }?>
		</td>-->
	<? 
	
	if($edit==true){
	
	?>
	
		<td class="listing-item" width="60" align="center"><?php if ($active!=1 && $generatedCount == 0 ){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$procurementId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RMProcurmentOrder.php';"><?php }
		?></td>
	<? } ?>
	</tr>
	<?php $equipmentName=""; $chemicalName="";?>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="11" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RMProcurmentOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RMProcurmentOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RMProcurmentOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmProcurementSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintRMProcurementOrderAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?></td>
												<!--<? /*if($del==true && $edit==true){?><td><input type="submit" value=" Generate Gate Pass " name="cmdGenerate" class="button" onClick="return confirmGenerate(this.form,'delId_',<?=$rmProcurementSize;?>);">&nbsp;</td><?php }*/ ?>-->
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<input type="hidden" name="hidStockItemStatus" id="hidStockItemStatus">
		<input type="hidden" name="hidEditId" value="<?=$hidEditId?>">
		<!--<input name='histatus_0' type='hidden' id='histatus_0'>-->
		<tr>
			<td height="10"></td>
		</tr>


	</table>
<? if ($addMode || $editMode) {?>

<SCRIPT LANGUAGE="JavaScript">
	function addNewItem()
		{
	
			//addNewDriver();
			
			//addNewVehicle();
			
			addNewVehicleAndDriver();
			
			
			addNewRMProcurmentSupplier();
			
			addNewHarvestingEquipment();
			
			addNewHarvestingChemical();
			
			//addNewRMProcurmentItem();
			//addNewRMProcurmentChemicalItem();
		}
		
	// function addNewDriver()
	// {
		
		// addDriverRow('tblDriver','','','','addmode');
	// }
	// function addNewVehicle()
	// {
		// addVehicleRow('tblVehicle','','','','addmode');
	// }
	function addNewVehicleAndDriver()
	{
	//alert("hii");
		addVehicleAndDriverRow('tblVehicleAndDriver','','','','','addmode');
	}
	function addNewVehicleAndDriverCopy()
	{
	//alert("hii");
		addVehicleAndDriverRow('tblVehicleAndDriver','','','','Copy','addmode');
	}
	function addNewRMProcurmentSupplier() 
	{
		if(supplierGroup>0)
		{
			alert("Cannot add new supplier");
			return false;
		}
		
		addNewRMProcurmentSupplierRow('tblAddProcurmentOrderSupplier','', '', '', '', '','','addmode');
	}
			
	
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

	  // function addNewRMProcurmentItem() 
	// {
		// addNewProcurmentItemRow('tblAddProcurmentOrder', '','', '', '', '', '','addmode');
	// }			
	 // function addNewRMProcurmentChemicalItem() 
	// {
		// addNewRMProcurmentChemicalItemRow('tblAddProcurmentChemicalOrder', '','', '', '', '','addmode');
	// }
	//balanceQty();
</SCRIPT>	
<? }?> 
<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">

window.onLoad = addNewItem();
window.onLoad =xajax_generateGatePass();

</SCRIPT>
<? }?>

<script language="JavaScript" type="text/javascript">
			<?php
				if (sizeof($procurmentSupplierRecs)>0) {

				// Set Value to Main table
			?>
				fieldvalue = <?=sizeof($procurmentSupplierRecs)?>;
			<?php
				}
			?>
			
			<?php
				if (sizeof($procurmentVehicleAndDriverRecs)>0) {

				// Set Value to Main table
			?>
			
				fdId = <?=sizeof($procurmentVehicleAndDriverRecs)?>;
			<?php
				}
			?>
			
</script>

<? 
	if ($editMode!="") {
	
	
	
	
	/*if (sizeof($procurmentChemicalRecs)>0) {
		$l=0;
			foreach ($procurmentChemicalRecs as $cr) {				
			$id=$cr[0];						
			$editProcurmentId	=	$cr[1];
			$vehicle=$rmProcurmentOrderObj->getVehicleId($editProcurmentId);
			 $vehicleNumber=$vehicle[0];
			// $equipmentName=$pr[2];
			// $equipmentQty	=	$pr[3];
			// $equipmentIssued=	$pr[4];
			// $difference=	$pr[5];
			$chemicalName=	$cr[2];
			$chemicalQty	=	$cr[3];
			$chemicalIssued=	$cr[4];
			
			
?>
	<SCRIPT LANGUAGE="JavaScript">
	 	addNewRMProcurmentChemicalItemRow('tblAddProcurmentChemicalOrder','<?=$id?>', '<?=$vehicleNumber?>', '<?=$chemicalName?>', '<?=$chemicalQty?>', '<?=$chemicalIssued?>','editmode');
		xajax_getDetailvalue('<?=$vehicleNumber?>','','<?=$l?>','<?=$chemicalName?>');
		//xajax_getDetails('<?=$vehicleNumber?>','<?=$chemicalName?>','<?=$j?>');
		//	xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
		//balanceQty();
	</SCRIPT>	
<? 		$l++;
			}
			
		}*/
		?>
		
		<?php
		
		if (sizeof($harvestingEquipmentRec)>0){
					$j=0;
					
						foreach($harvestingEquipmentRec as $ver) {	
						$harvestingEquipmentId 	= $ver[0];
						$harvestingEquipmentName	=$ver[1];
						$harvestingEquipmentQuantity	= $ver[2];
	
	?>
	<SCRIPT LANGUAGE="JavaScript">
	 	addNewRow('tblHarvestingEquipment','<?=$harvestingEquipmentId?>','<?=$harvestingEquipmentName?>','<?=$harvestingEquipmentQuantity?>');		
		// xajax_rmProcurmentSupplierName('<?=$supplierGroupNm?>','<?=$n?>','');
		
		//xajax_getDetails('<?=$vehicleNumber?>','<?=$chemicalName?>','<?=$j?>');
		//	xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
		
	</SCRIPT>	
<? 		$j++;
			}
			
		}
		else
		{
		?>
		<script>
		addNewRow('tblHarvestingEquipment','','','');
		</script>
		<?
		}
	
		if (sizeof($harvestingChemicalRec)>0){
					$j=0;
					
						foreach($harvestingChemicalRec as $ver) {	
						$harvestingChemicalId 	= $ver[0];
						$harvestingChemicalName	=$ver[1];
						$harvestingChemicalQuantity	= $ver[2];
			?>
			<SCRIPT LANGUAGE="JavaScript">
		addChemicalRow('tblHarvestingChemical','<?=$harvestingChemicalId?>','<?=$harvestingChemicalName?>','<?=$harvestingChemicalQuantity?>');		
			</SCRIPT>	
			<?
					$j++;
					}
				}
				else{
				?>
				<script>
				addChemicalRow('tblHarvestingChemical','','','');
				</script>
				<?php
				}				
				?>
				
			<SCRIPT LANGUAGE="JavaScript">
			
		
		
			//addDriverRow('tblDriver','<?=$editDriverProcurmentId?>','<?=$driverid?>','<?=$driverName?>','editmode');	
			//xajax_rmProcurmentScheduleDriverDetails('<?=$schedule_date?>','<?=$p?>','','<?=$editProcurmentId?>');	
		
			</SCRIPT>	
			
			<SCRIPT LANGUAGE="JavaScript">
		//addVehicleRow('tblVehicle','<?=$editVehicleProcurmentId?>','<?=$vehicleid?>','<?=$vehicleName?>','editmode');		
			</SCRIPT>	
			<?
							
	
			if (sizeof($procurmentSupplierRecs)>0) {
		   $n=0;
			 foreach ($procurmentSupplierRecs as $sp) {				
			 $id=$sp[0];
			$supplier_name=$sp[1];
			$pond_name=	$sp[2];
			 //$editProcurmentId	=	$sp[1];
			 
			 $supplierGroup=$rmProcurmentOrderObj->getSupplierGroupDetails($supplier_name);
			 //$supplierGroup=$rmProcurmentOrderObj->getSupplierGroupId($editProcurmentId);
			 $supplierGroupNm=$supplierGroup[0][1];
			$pondLocationRecs 			= $rmProcurmentOrderObj->filterPondLocationList($pond_name);
			$pondlocation=$pondLocationRecs[1]; 	
			$pondQuantityRecs 			= $rmProcurmentOrderObj->filterPondQtyList($pond_name);
			if(sizeof($pondQuantityRecs)>0)
			{
			foreach($pondQuantityRecs as $pondQuantity )
			{
			$pondQnty+=$pondQuantity[1];
			}
			$pondvalue="Yes".' ('.$pondQnty.')';
			}
			else
			{
			$pondvalue="No";
			}
			 //
			 //$supplier_name= $sp[2];
			 //$supplier_address	=	$sp[3];
			 //$pond_name=	$sp[4];
			 //$pond_address=	$sp[5];
			
			// $chemicalName=	$pr[8];
			// $chemicalQty	=	$pr[9];
			// $chemicalIssued=	$pr[10];
			
			
?>
	<SCRIPT LANGUAGE="JavaScript">
	 	//addNewRMProcurmentSupplierRow('tblAddProcurmentOrderSupplier','<?=$id?>',  '<?=$supplier_name?>', '<?=$supplierGroupNm?>', '<?=$pond_name?>', '<?=$pondlocation?>','<?=$pondvalue?>','editmode');
		// xajax_rmProcurmentSupplierName('<?=$supplierGroupNm?>','<?=$n?>','');
		
		//xajax_getDetails('<?=$vehicleNumber?>','<?=$chemicalName?>','<?=$j?>');
		//	xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
		
	</SCRIPT>	
<? 		$n++;
			}
			
		}
	
	
	
	
	
	
		/*if (sizeof($procurmentEquipmentRecs)>0) {
		$m=0;
			foreach ($procurmentEquipmentRecs as $pr) {				
			$id=$pr[0];						
			$editProcurmentId	=	$pr[1];
			$vehicle=$rmProcurmentOrderObj->getVehicleId($editProcurmentId);
			 $vehicleNumber=$vehicle[0];
			$equipmentName=$pr[2];
			$equipmentQty	=	$pr[3];
			$equipmentIssued=	$pr[4];
			$difference=	$pr[5];
			// $chemicalName=	$pr[8];
			// $chemicalQty	=	$pr[9];
			// $chemicalIssued=	$pr[10];
			
			
?>
	<SCRIPT LANGUAGE="JavaScript">
	 	addNewProcurmentItemRow('tblAddProcurmentOrder','<?=$id?>', '<?=$vehicleNumber?>', '<?=$equipmentName?>', '<?=$equipmentQty?>', '<?=$equipmentIssued?>', '<?=$difference?>','editmode');
		xajax_getDetails('<?=$vehicleNumber?>','<?=$equipmentName?>','<?=$m?>','');
		
		//xajax_getDetails('<?=$vehicleNumber?>','<?=$chemicalName?>','<?=$j?>');
		//	xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
		balanceQty();
	</SCRIPT>	
<? 		$m++;
			}
			
		}*/
		
		?>
		<SCRIPT LANGUAGE="JavaScript">

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
	 // function addNewRMProcurmentSupplier() 
	// {
		// addNewRMProcurmentSupplierRow('tblAddProcurmentOrderSupplier', '','', '', '', '', '','','');
	// }
	// function addNewDriver()
	// {
		
		// addDriverRow('tblDriver','','','','addmode');
	// }
	// function addNewVehicle()
	// {
		// addVehicleRow('tblVehicle','','','','addmode');
	// }	
		 // function addNewRMProcurmentItem() 
	// {
		// addNewProcurmentItemRow('tblAddProcurmentOrder', '','', '', '', '', '','');
	// }
	
	 // function addNewRMProcurmentChemicalItem() 
	// {
		// addNewRMProcurmentChemicalItemRow('tblAddProcurmentChemicalOrder', '','', '', '', '','');
	// }
	 
		
		</SCRIPT>
		<?
		
		
		
		
	}
?>

<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "entryDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "entryDate", 
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


Calendar.setup 
	(	
		{
			inputField  : "schedule_date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "schedule_date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);







	// Calendar.setup 
	// (	
		// {
			// inputField  : "out_time",         // ID of the input field
			// eventName	  : "click",	    // name of event
			// button : "out_time", 
			// ifFormat    : "%d/%m/%Y",    // the date format
			// singleClick : true,
			// step : 1
		// }
	// );
	//-->
	
	function page(fileName)
	{
		
			window.location = fileName;
		
	}
	// jQuery(document).ready(function(){
	// jQuery('#RMProcurmentOrder').submit(function(){
		// alert('hi');
		// alert(jQuery("[name='histatus_0']").val());
		////return false;
	// });
// });

 var Base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}
	 
	</SCRIPT>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>