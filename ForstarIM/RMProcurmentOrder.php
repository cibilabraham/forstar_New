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
		$unitId =	$p["unitId"];
		
		$hidVehicleAndDriverTableRowCount=	$p["hidVehicleAndDriverTableRowCount"];
		$hidSupplierRowCount=$p["hidSupplierRowCount"];
		$harvestingEquipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$harvestingChemicalTableRowCount    = $p["hidHarvestingChemicalTableRowCount"];
	
			if ($procurmentNo!="" && $entryDate!="" && $procurmentNo!="" && $schedule_date!="") {
			$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcumentOrder($selCompanyName,$entryDate,$procurmentNo,$schedule_date,$userId,$unitId);
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


				
				
		
			  if ($hidSupplierRowCount>0 ) {
			
					for ($j=0; $j<$hidSupplierRowCount; $j++) {
						$status = $p["sstatus_".$j];
						  if ($status!='N') {
						
						
						$supplierName		=	$p["supplierName_".$j];
						//$supplierAddress		=	$p["supplierAddress_".$j];
						$pondName		=	$p["pondName_".$j];
						$location		=	$p["location_".$j];
						//$pondAddress		=	$p["pondAddress_".$j];
						//$currentStock = $totalQty - $quantity;
						
						
						if ($lastId!=""  && $supplierName!="" && $location!='' ) {
							$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcurmentSupplier($lastId, $supplierName,$pondName,$location);
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
		$unitId=$procurmentOrderRec[6];
		$procurmentVehicleAndDriverRecs = $rmProcurmentOrderObj->fetchAllProcurmentVehicleAndDriver($editProcurmentId);
		$procurmentSupplierRecs = $rmProcurmentOrderObj->fetchAllProcurmentSupplier($editProcurmentId);
		$harvestingChemicalRec=$rmProcurmentOrderObj->fetchAllProcurmentChemical($editProcurmentId);
		$harvestingEquipmentRec =$rmProcurmentOrderObj->fetchAllProcurmentEquipment($editProcurmentId);
		
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
		$unitId =	$p["unitId"];
		
		//$hidDriverTableRowCount    = $p["hidDriverTableRowCount"];
		//$hidVehicleTableRowCount    = $p["hidVehicleTableRowCount"];
		$hidVehicleAndDriverTableRowCount=	$p["hidVehicleAndDriverTableRowCount"];
		$equipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$chemicalTableRowCount	= $p["hidHarvestingChemicalTableRowCount"];	
		$hidSupplierRowCount=$p["hidSupplierRowCount"];
		
		if ($procurementId!=""  && $selCompanyName!="" && $procurmentNo!=""  && $entryDate!="" && $schedule_date!="" ) {
		$procurmentRecUptd	=	$rmProcurmentOrderObj->updateProurmentOrder($procurementId, $selCompanyName,$entryDate,$schedule_date,$procurmentNo,$unitId);
		
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
			
			for ($g=0; $g<$hidSupplierRowCount; $g++) {
			  $status = $p["sstatus_".$g];
			   $rmId  		= $p["srmId_".$g];
			   //die();
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				$supplierName		=	$p["supplierName_".$g];
				$pondName		=	$p["pondName_".$g];
				$location		=	$p["location_".$g];
					if ($procurementId!=""  && $supplierName!="" && $location!=""  && $rmId!="") {
						
						$updateProcurmentEntryRec = $rmProcurmentOrderObj->updateProcurmentSupplier($rmId,$supplierName,$pondName,$location);
						
						
					} else if($procurementId!=""  && $supplierName!="" && $location!=""   &&  $rmId=="" ) {

					//($procurementId!="" &&  $equipmentName!=""  && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!=""  && $rmId=="")  {	
						$detailsIns	=	$rmProcurmentOrderObj->addProcurmentSupplier($procurementId, $supplierName,$pondName,$location);
								
						
					}
					
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
				 $delProcrmentSupplierRec = $rmProcurmentOrderObj->delRMProcurmentSupplierRec($rmId);
						
				}
				
			} 
					
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
		for ($i=1; $i<=$rowCount; $i++)
		{
			$procurementId	=	$p["delId_".$i];
			//die();
			if ($procurementId!="" ) 
			{
				$procurmentNoVal=$rmProcurmentOrderObj->ProcurmentNumberFetch($procurementId);
				$deleteProcurmentRecs	=	$rmProcurmentOrderObj->deleteProcurmentGroup($procurementId);
				$ProcurmentRecDelt =	$rmProcurmentOrderObj->deleteProcurmentVehicleDetail($procurementId);
				$ProcurmentRecDel =	$rmProcurmentOrderObj->deleteProcurmentSupplier($procurementId);
				$ProcurmentRecDel2 =$rmProcurmentOrderObj->deleteProcurmentEquipment($procurementId);
				$ProcurmentRecDel3 =$rmProcurmentOrderObj->deleteProcurmentChemical($procurementId);
			}
		}
		if ($deleteProcurmentRecs) 
		{
			$sessObj->createSession("displayMsg",$msg_succDelRMProcurment);
			$sessObj->createSession("nextPage",$url_afterDelRMProcurment.$selection);
		} 
		else 
		{
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
	//$companyRecords	= $rmProcurmentOrderObj->fetchAllCompanyName();
	list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
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
	
	/*$str = 'GTP123';
	$numbers = preg_replace('/[^0-9]/', '', $str);
	$letters = preg_replace('/[^a-zA-Z]/', '', $str);
	echo $numbers."------".$letters;
	*/
?>
<!--<script language="javascript" type="text/javascript" src="libjs/datetimepicker1.js"></script>-->
<form id="RMProcurmentOrder" name="RMProcurmentOrder" action="RMProcurmentOrder.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="72%" >
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		</tr>
		<?
		 if ($editMode || $addMode) {
		//	$sealNoList   = $rmProcurmentOrderObj->getAllSealNos($seal_no);
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
												</td>
											</tr>
											<input type="hidden" name="hidProcurmentId" id="hidProcurmentId" value="<?=$editProcurmentId;?>">
											<tr>
												<td class="fieldName" nowrap >&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<TD nowrap>
													<span class="fieldName" style="color:red; line-height:normal" id="message" name="message"></span>
												</TD>
											</tr>
											<!--------------------tqble--------------------->	
											<tr>
												<td colspan="2">
													<table width="75%" border="0" cellpadding="1" cellspacing="0" align="center">
														<tr>
															<td colspan="2" nowrap class="fieldName" >
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
																					<td class="fieldName" nowrap valign="top">*Procurment Number:</td>
																					<td class="listing-item"><input name="procurmentNo" type="text" id="procurmentNo" size="10" value="<?=$procurmentNo?>" <?php if($editMode){ ?> readonly="readonly" <? } else { ?> onkeyUp="checkProcurementOrder(this.value);" <? } ?> tabindex="1" >eg:-GTP100</td>
																				</tr>
																				
																				<tr>				
																					<td class="fieldName" nowrap>*Company Name:&nbsp;</td>
																					<td class="listing-item">
																						<select name="selCompanyName" id="selCompanyName">
																							<option value="">--select--</option>
																							<?php 
																							foreach($companyRecords as $cr=>$crName)
																							{
																								$companyId		=	$cr;
																								$companyName	=	stripSlash($crName);
																								$selected="";
																								//if($selCompanyName==$companyId ) echo $selected="Selected";
																								if(($selCompanyName == $companyId) || ($selCompanyName=="" && $companyId==$defaultCompany))  echo $selected="Selected";
																							?>
																							<option value="<?=$companyId?>" <?=$selected?>><?=$companyName?></option>
																							<? }
																							?>
																						</select>
																					</td>
																					<input type="hidden" name="procurementIdauto" id="procurementIdauto" size="9" value="<?=$procurementIdauto;?>" readonly="readonly"  />
																					<input type="hidden" name="number_gen_id" id="number_gen_id" size="9" value="<?=$number_gen_id;?>" readonly="readonly"  />
																					<input type="hidden" name="unitId" id="unitId" size="9" value="<?=$unitId;?>" readonly="readonly"  />
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
																				<tr>
																					<td colspan="4">
																						<table width="20%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblVehicleAndDriver" name="tblVehicleAndDriver">
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
																						</table>
																					</td>
																				</tr>
																				<input type='hidden' name="hidVehicleAndDriverTableRowCount" id="hidVehicleAndDriverTableRowCount" value="<?=$p?>">
																				<tr>
																					<TD style="padding-left:5px;padding-right:5px;">
																					<!--<a href="###" id='addRow' onclick="javascript:addNewVehicleAndDriver();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New </a>-->
																					</TD>
																					<TD style="padding-left:5px;padding-right:5px;">
																						<a href="###" id='addRow' onclick="javascript:addNewVehicleAndDriverCopy();"  class="link1" title="Click here to duplicate value."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New<!--(Copy)--></a>
																					</TD>
																				</tr>
																			</table>
																		</td>
																	</tr>	
																</table>
															</td>
														</tr>	
														<tr>
															<td align="center">
																<table>
																	<tr>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td colspan="2"  height="10" ></td>
														</tr>
														<tr align="center">
															<td>
																<table>
																	<tr align="center">
																		<td>	
																			<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddProcurmentOrderSupplier" name="tblAddProcurmentOrderSupplier">
																				<tr bgcolor="#f2f2f2" align="center">
																					<td class="listing-head" nowrap>Supplier name </td>
																					<td class="listing-head" nowrap>Supplier Group</td>
																					<td class="listing-head" nowrap>Farm Name </td>
																					<td class="listing-head" nowrap>Procurement Center </td> 
																					<td class="listing-head" nowrap>PHT Certificate Available and balance Qty </td>
																					<td></td>
																				</tr>
																				<?
																				if (sizeof($procurmentSupplierRecs)>0) {
																				   $n=0;
																					 foreach ($procurmentSupplierRecs as $sp) {				
																						$id=$sp[0];
																						$selSupplierId=$sp[1];
																						$selPondId=	$sp[2];
																						$sellocationId=	$sp[3];
																						//$editProcurmentId	=	$sp[1];
																						$supplierGroup=$rmProcurmentOrderObj->getSupplierGroupDetails($selSupplierId);
																						//$supplierGroup=$rmProcurmentOrderObj->getSupplierGroupId($editProcurmentId);
																						$supplierGroupNm=$supplierGroup[0][1];
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
																						if($selPondId!="")
																						{
																							$locationRecs 	= $rmProcurmentOrderObj->filterPondLocationListEdit($selPondId);	
																						}
																						else
																						{
																							$locationRecs 	= $rmProcurmentOrderObj->getLandingCenterSupplierEdit($selSupplierId);
																						}
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
																						<select  tabindex="1" id="location_<?=$n?>" name="location_<?=$n?>">
																							
																							<?php 	
																							foreach($locationRecs as $lr)
																							{
																								$locationId		=	$lr[0];
																								$locationName	=	stripSlash($lr[1]);
																								$sel  = ($sellocationId==$locationId)?"Selected":"";
																							?>
																							<option value="<?=$locationId?>" <?=$sel?>><?=$locationName?></option>";
																							<?
																							}
																							?>
																						</select>
																					</td>
																					<!--<td align="center" class="fieldName">
																						<input type="text" value="<?=$pondlocation?>" tabindex="2" style="text-align:right; border:none;" readonly="" size="15" id="pondLocation_<?=$n?>" name="pondLocation_<?=$n?>">
																					</td>-->
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
																		</td>
																	</tr>
																	<input type="hidden" name="hidSupplierRowCount" id="hidSupplierRowCount" value="<?=$n?>">																									<tr><TD height="10"></TD></tr>
																	<tr>
																		<td width="40%" valign="top" colspan="3" >
																		<a href="###" id='addRow' onclick="javascript:addNewRMProcurmentSupplier();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																		</TD>
																	</tr>	
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
																				<tr>
																					<td>
																						<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblHarvestingEquipment" name="tblAddProcurmentOrder">
																							<tr bgcolor="#f2f2f2" align="center">
																								<td class="listing-head" nowrap>Equipment name </td>
																								<td class="listing-head" nowrap>Required quantity</td>
																								<td></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<TD style="padding-left:5px;padding-right:5px;">
																					<a href="###" id='addRow' onclick="javascript:addNewHarvestingEquipment();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																					</TD>
																				</tr>
																				<input type='hidden' name="hidHarvestingEquipmentsTableRowCount" id="hidHarvestingEquipmentsTableRowCount" value="">
																			</table>
																		</td>
																		<td width="8%"></td>
																		<td valign="top">
																			<table>
																				<tr>
																					<td>
																						<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblHarvestingChemical" name="tblHarvestingChemical">
																							<tr bgcolor="#f2f2f2" align="center">
																								<td class="listing-head" nowrap>Chemical name </td>
																								<td class="listing-head" nowrap>Required quantity(Kgs)</td>
																								<td></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3"  >
																					<a href="###" id='addRow' onclick="javascript:addNewHarvestingChemical();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																					</TD>		
																				</tr>
																				<input type='hidden' name="hidHarvestingChemicalTableRowCount" id="hidHarvestingChemicalTableRowCount" value="">
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<TD height="10"></TD>
											</tr>
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
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateProcurment(document.RMProcurmentOrder);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
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
		<?
		}
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
															<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>">
														</td>
														<td class="listing-item">&nbsp;</td>
														<td class="listing-item"> Till:</td>
														<td> 
															<? 
															if($dateTill=="") $dateTill=date("d/m/Y");
															?>
															<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>">
														</td>
														<td class="listing-item">&nbsp;</td>
														<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
														<td class="listing-item" nowrap >&nbsp;</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
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
									</table>									
								</td>
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
												</div> 
											</td>
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
												if (sizeof($supplierData)>0) {
													$nextRec = 0;						
													foreach ($supplierData as $cR) {
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
													foreach ($pondData as $cR) {
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
												<? 
												if($edit==true){
												?>
												<td class="listing-item" width="60" align="center"><?php if ($active!=1 && $generatedCount == 0 ){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$procurementId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RMProcurmentOrder.php';"><?php }
												?>
												</td>
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
												</div> 
											</td>
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
											<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmProcurementSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintRMProcurementOrderAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?>
											</td>
												<!--<? /*if($del==true && $edit==true){?><td><input type="submit" value=" Generate Gate Pass " name="cmdGenerate" class="button" onClick="return confirmGenerate(this.form,'delId_',<?=$rmProcurementSize;?>);">&nbsp;</td><?php }*/ ?>-->
										</tr>
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
		addNewVehicleAndDriver();
		addNewRMProcurmentSupplier();
		addNewHarvestingEquipment();
		addNewHarvestingChemical();
	}

	function addNewVehicleAndDriver()
	{
		addVehicleAndDriverRow('tblVehicleAndDriver','','','','','addmode');
	}
	function addNewVehicleAndDriverCopy()
	{
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
		addNewRow('tblHarvestingEquipment','','','');
	}
	
	function addNewHarvestingChemical()
	{
		addChemicalRow('tblHarvestingChemical','','','');
	}

</SCRIPT>	
<? }?> 
<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">

window.onLoad = addNewItem();
//window.onLoad =xajax_generateGatePass();

</SCRIPT>
<? }?>
<script language="JavaScript" type="text/javascript">
<?php
if (sizeof($procurmentSupplierRecs)>0) {
?>
	fieldvalue = <?=sizeof($procurmentSupplierRecs)?>;
<?php
	}
?>
<?php
if (sizeof($procurmentVehicleAndDriverRecs)>0) {
?>
	fdId = <?=sizeof($procurmentVehicleAndDriverRecs)?>;
<?php
	}
?>
</script>

<? 
if ($editMode!="") 
{
	if (sizeof($harvestingEquipmentRec)>0)
	{
		$j=0;
		foreach($harvestingEquipmentRec as $ver) 
		{	
			$harvestingEquipmentId 	= $ver[0];
			$harvestingEquipmentName	=$ver[1];
			$harvestingEquipmentQuantity	= $ver[2];
		?>
		<SCRIPT LANGUAGE="JavaScript">
			addNewRow('tblHarvestingEquipment','<?=$harvestingEquipmentId?>','<?=$harvestingEquipmentName?>','<?=$harvestingEquipmentQuantity?>');		
		</SCRIPT>	
		<? 		
		$j++;
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
	
	if (sizeof($harvestingChemicalRec)>0)
	{
		$j=0;
		foreach($harvestingChemicalRec as $ver) 
		{	
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
	else
	{
	?>
	<script>
		addChemicalRow('tblHarvestingChemical','','','');
	</script>
	<?php
	}				
	?>
	<?
	if (sizeof($procurmentSupplierRecs)>0)
	{
		$n=0;
		foreach ($procurmentSupplierRecs as $sp) 
		{				
			$id=$sp[0];
			$supplier_name=$sp[1];
			$pond_name=	$sp[2];
			$supplierGroup=$rmProcurmentOrderObj->getSupplierGroupDetails($supplier_name);
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
		?>
	
		<? 		
		$n++;
		}
	}
	?>
<SCRIPT LANGUAGE="JavaScript">
	function addNewHarvestingEquipment()
	{
		addNewRow('tblHarvestingEquipment','','','');
	}
	function addNewHarvestingChemical()
	{
		addChemicalRow('tblHarvestingChemical','','','');
	}
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

function page(fileName)
{
	window.location = fileName;
}

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