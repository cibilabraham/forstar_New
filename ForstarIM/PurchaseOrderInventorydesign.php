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
	/*
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
*/

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
		
		$hidVehicleAndDriverTableRowCount=	$p["hidVehicleAndDriverTableRowCount"];
		$hidSupplierRowCount=$p["hidSupplierRowCount"];
		$harvestingEquipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$harvestingChemicalTableRowCount    = $p["hidHarvestingChemicalTableRowCount"];
	
			if ($procurmentNo!="" && $entryDate!="" && $procurmentNo!="" && $schedule_date!="") {
			$rmProcurmentRecIns	=	$rmProcurmentOrderObj->addProcumentOrder($selCompanyName,$entryDate,$procurmentNo,$schedule_date,$userId);
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
	
		//$hidDriverTableRowCount    = $p["hidDriverTableRowCount"];
		//$hidVehicleTableRowCount    = $p["hidVehicleTableRowCount"];
		$hidVehicleAndDriverTableRowCount=	$p["hidVehicleAndDriverTableRowCount"];
		$equipmentTableRowCount	= $p["hidHarvestingEquipmentsTableRowCount"];
		$chemicalTableRowCount	= $p["hidHarvestingChemicalTableRowCount"];	
		$hidSupplierRowCount=$p["hidSupplierRowCount"];
		
		if ($procurementId!=""  && $selCompanyName!="" && $procurmentNo!=""  && $entryDate!="" && $schedule_date!="" ) {
		$procurmentRecUptd	=	$rmProcurmentOrderObj->updateProurmentOrder($procurementId, $selCompanyName,$entryDate,$schedule_date,$procurmentNo);
		
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
	$ON_LOAD_PRINT_JS = "libjs/PurchaseOrderInventory.js"; // For Printing JS in Head section
	
	
	
	$harvestingEquipmentRecs = $harvestingEquipmentMasterObj->fetchAllRecordsActiveequipmentType();
	$harvestingChemicalRecs = $harvestingChemicalMasterObj->fetchAllChemicalRecordsActive();
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	
?>
<form name="frmPurchaseOrderInventory" action="PurchaseOrderInventory.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="72%" >
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		</tr>
		<?
		 if ($editMode || $addMode) {
		
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrderInventory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);">
												<input type="submit" name="cmdConfirmSave" id="cmdConfirmSave" class="button" value=" Confirm Purchase Order " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);">	</td>
												<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrderInventory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);"> &nbsp;&nbsp;												</td>
												<?}?>
											</tr>
											<input type="hidden" name="hidPurchaseOrderId" value="<?=$editPurchaseOrderId;?>">
											<tr>
												<td class="fieldName" nowrap >&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<!--------------------tqble--------------------->	
											<tr>
												<td colspan="2">
													<table width="100%" border="0" cellpadding="1" cellspacing="0" align="center">
														<tr>
															<td colspan="2" nowrap class="fieldName" >
																<table cellpadding="1"  width="80%" cellspacing="2" border="0" align="center" bgcolor="#e8edff" style="border:1px #999999 solid; border-radius: 5px;">
																	<tr>
																		<td align="center" valign="top">
																		<?php
																			$left_l=true;
																			$entryHead = "";
																			$rbTopWidth = "";
																			require("template/rbTop.php");
																		?>	
																			<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddProcurmentOrderSupplier" name="tblAddProcurmentOrderSupplier">
																				<tr>
																					<td class="fieldName" align='right' >Select Unit:&nbsp;</td> 
																					<td class="listing-item">
																						<select onchange="xajax_getPONumber('<?=$selDate?>','<?=$compId?>',document.getElementById('unitpo').value);" id="unitpo" name="unitpo" <?php if ($editMode){ ?> disabled="true" <?php }?>>
																							<option value=''>--Select--</option>
																							<?php
																							foreach ($unitRecords as $unitd) {
																								$unitId1 		= $unitd[0];
																								$unitName1	= $unitd[2];
																								$selectedunitType1 = ($unitInv==$unitId1)?"selected":"";
																								
																							?>
																							<option value='<?=$unitId1?>'<?=$selectedunitType1?>><?=$unitName1?></option>
																							<?php
																							}
																							?>
																						</select>
																					</td>
																					<td width="10%">&nbsp;</td>
																					<td class="fieldName" align='right'>*Supplier:</td>
																					<td class="listing-item">	 
																						<select name="selSupplier" id="selSupplier" onchange="getOtherSupplierStockRecords('selStock_',document.getElementById('selSupplier').value,'<?=$poItem?>','<?=$supplierRateListId?>',document.getElementById('hidTableRowCount').value,'hidSelStock_','<?=$mode?>');" >
																							<option value="">--select--</option>
																							<? foreach($supplierRecords as $sr)
																							{
																								$supplierId	=	$sr[0];
																								$supplierCode	=	stripSlash($sr[1]);
																								$supplierName	=	stripSlash($sr[2]);
																								$selected ="";
																								//if ($selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
																								if ($selectSupplierId==$supplierId || $selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
																							?>
																							<option value="<?=$supplierId?>" <?=$selected;?>>
																							<?=$supplierName?>
																							</option>
																							<? }?>
																						</select>
																					</td>
																					<td nowrap width="10%">&nbsp;</td>
																					<td  align='right'  class="fieldName" >*FSSAIRegnNo:</td>
																					<td class="listing-item" id="fssaiRegnoId">	<?=$fssaiRegNo;?> </td>
																					<td nowrap width="10%">&nbsp;</td>
																				</tr>
																				<? if (!$poItem) {?>
																				<tr id="supRows1" >
																					<td class="fieldName" align='right' ><?//=$unitInv?> PO ID:&nbsp;</td>
																					<td class="listing-item">
																					<?
																					if ($editId)
																					{ 
																						$valdispurchaseOrderNo=$editPO;	
																					}
																					else 
																					{
																						$valdispurchaseOrderNo=$dispurchaseOrderNo;
																					}
																					?>
																					<?php $styleDisplay = "border:none;";?>
																					<input name="textfield" id="textfield" type="text" size="6" value="<?=$valdispurchaseOrderNo;?>" readonly  <? if($genPoId!=0 || $editPO){ ?> style="border:none" readonly <?}?>   onKeyUp="xajax_chkPONumberExist(document.getElementById('textfield').value, '<?=$mode?>');" value="<? if($editPO) { echo  $editPO;} else if($genPoId==1) { echo "New"; }else { echo $p["textfield"]; }?>">
																					<div id="divPOIdExistTxt" style='line-height:normal; font-size:10px; color:red;'><?=$PurchaseOrderMsg;?></div>
																					</td>
																					<td width="10%">&nbsp;</td>	
																					<td class="fieldName" align='right' >*ServicetaxNo:</td>
																					<td class="listing-item" id="serviceTaxId"><?=$serviceTaxNo;?></td>	 
																					<td width="10%">&nbsp;</td>
																					<td class="fieldName" align='right'>*VAT No:</td>
																					<td class="listing-item" id="vatNoId">	<?=$vatNo;?> </td>
																					<td width="10%">&nbsp;</td>
																				</tr>
																				<tr id="supRows4" >
																					<td class="fieldName" align='right'>*CST No:</td>
																					<td class="listing-item" id="cstNoId"><?=$cstNo;?></td>	 
																					<td width="10%">&nbsp;</td>
																				<!--</tr>
																				<tr id="supRows5" >-->
																					<td class="fieldName" align='right'>*PAN No:</td>
																					<td class="listing-item" id="panNoId">	<?=$panNo;?></td>
																					<td width="10%">&nbsp;</td>
																				</tr>
																			<?} ?>
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
															<td colspan="2"  height="10" ></td>
														</tr>
														
														<!--  Dynamic Row Adding Starts Here-->
														<tr>
															<TD>
																<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddStockItem">
																	<tr bgcolor="#f2f2f2" align="center">
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="23" >Unit</td>
																		<td width="44" class="listing-head" style="padding-left:5px; padding-right:5px;">Item</td>
																		<? if (!$poItem) {?>
																				<td width="173" nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Unit Price </td>
																		<? }?>
																				<td width="35" class="listing-head" style="padding-left:5px; padding-right:5px;">Quantity</td>
																				<td width="52" class="listing-head" style="padding-left:5px; padding-right:5px;">Total</td>
																		<td width="47" class="listing-head" style="padding-left:5px; padding-right:5px;">Qty in Stock</td>		
																		<td width="152" class="listing-head" style="padding-left:5px; padding-right:5px;">Other<br>Suppliers</td>
																		<td width="235" class="listing-head" style="padding-left:5px; padding-right:5px;">Last Purchase Supplier,Qty,Price</td>
																		<td width="170" class="listing-head" style="padding-left:5px; padding-right:5px;">Not Over</td>
																		<td width="87" class="listing-head" style="padding-left:5px; padding-right:5px;">Description</td>
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="54" >Descp in Print Out</td>
																		<td width="8" class="listing-head" id="headRemoveTd" style="padding-left:5px; padding-right:5px;"></td>
																	</tr>
																	 <tr bgcolor="#FFFFFF" align="center">
																	   <? if (!$poItem) $colspan=1;
																	   else $colspan = 1;
																	?>
																		<td class="fieldName"><span class="listing-head">Remarks</span></td>
																		<td  class="fieldName" class="listing-head" align="right">
																			<textarea name="remarks" ><?=$netRemarks;?>
																			 </textarea>
																		</td>
																		<td  class="listing-head" align="right">
																			<p class="listing-head">Above rates are inclusive of<p>Transport		  
																			 <input type="hidden" name="inventoryno" value=<?php echo $unitInv;?> />
																			  <input type="checkbox" name="transport" id="transport" value=1 <?php if ($transport==1){?> checked="true" <?php }?>/>
																			   Excise
																			   <input type="checkbox" name="excise" id="excise" value=1 <?php if ($excise==1){?> checked="true" <?php }?>/>Vat
																			   <input type="checkbox" name="vat" id="vat" value=1  <?php if ($vat==1){?> checked="true" <?php }?>/>
																			 </p>
																		</td>
																		<td colspan="<?=$colspan?>" class="listing-head" align="right">Total:</td>
																		<td class="fieldName"><input name="totalQuantity" type="text" id="totalQuantity" size="8" style="text-align:right" readonly value="<?=$totalAmount;?>"></td>			
																		<td class="listing-head" align="right" colspan="3" >Delivery At our factory at M-53,MIDC,Taloja <input type="checkbox" name="factory" id="factory" value=1 <?php if ($factory==1){?> checked="true" <?php }?>/>(OR) To Bearer of this PO<input type="checkbox" name="bearer" id="bearer" value=1 <?php if ($bearer==1){?> checked="true" <?php }?>/></td>
																		<td class="listing-head">&nbsp;Delivary Date&nbsp;<input type="text" name="delivarydate" id="delivarydate" value="<?php echo $delivarydate;?>" /></td>
																		<td class="listing-head">&nbsp;Delivered To
																			<select onchange="getUnitAlphacode();" id="unitid" name="unitid">
																				<option value=''>--Select--</option>
																				<?php
																					foreach ($unitRecords as $unitd) {
																						$unitId 		= $unitd[0];
																						$unitName	= $unitd[2];
																						$selectedunitType = ($deliveredto==$unitId)?"selected":"";
																						
																				?>
																					<option value='<?=$unitId?>'<?=$selectedunitType?>><?=$unitName?></option>
																				<?php
																					}
																				?>
																			</select>
																		</td>
																		<td>&nbsp;</td>
																		<td id="footerRemoveTd">&nbsp;</td>
																	</tr>
																</table>
															</TD>
														</tr>
														<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize;?>">
														<SCRIPT LANGUAGE="JavaScript">
															<!--
																//setfieldId(<?=$rowSize;?>)
															//-->
														</SCRIPT>
														<tr>
															<TD>
																<a href="###" id='addRow' onclick="javascript:addNewStockItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
															</TD>
														</tr>
														<tr>
															<td colspan="2" nowrap class="fieldName" >&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2"  height="10" ></td>
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
												<input type="submit" name="cmdAdd" id="cmdAdd2" class="button" value=" Add " onClick="return validateProcurment(document.RMProcurmentOrder);">&nbsp;&nbsp;												</td>
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
								<td background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Order  </td>
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
														<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
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
								 <td colspan="3" align="right" style="padding-right:10px;">
									<table width="200" border="0">
										<tr>
											<td>
												<fieldset>
													<legend class="listing-item">Print PO</legend>
													<table width="200" cellpadding="0" cellspacing="0" bgcolor="#999999">
														<tr bgcolor="#FFFFFF">
															<td class="listing-item" nowrap="nowrap" height="25">PO No: </td>
															<td nowrap="nowrap">
															<? //if($selPOId=="") echo $disabled="disabled"; ?>
															<? $selPOId=$p["selPOId"];?>&nbsp;
															<select name="selPOId" id="selPOId" onchange="disablePrintPOButton();">
																<option value="">-- Select --</option>
																<?
																foreach($purchaseOrderPendingRecords as $por)
																	{
																		$poId	=	$por[0];
																		$poGenerateId = 	$por[1];
																		$selected="";
																		if($selPOId==$poId) $selected="Selected";
																?>
																<option value="<?=$poId?>" <?=$selected?>><?=$poGenerateId?></option>
																<? }?>
															</select>
															</td>
															<? if($print==true){?>
															<td nowrap="nowrap">&nbsp;<input name="cmdPrintPO" type="button" class="button" id="cmdPrintPO" onClick="return printPurchaseOrderWindow('PrintPOInventory.php',700,600);" value="Print PO"  ></td>
															<td>&nbsp;</td>
															<? }?>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
									</table>
								</td> 
							</tr>
									

							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td>
												<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button" onclick="return validatePurchaseOrder(document.frmPurchaseOrderInventory);"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPurchaseOrderInventory.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>&os=<?=$offset;?>&lt=<?=$limit;?>',700,600);"><? }?>
											</td>
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
								<td width="20">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head"></td>
								<td class="listing-head"></td>
							</tr>
							<tr>
								<td colspan="5">
									<table>
													<?php
										if ($p["itemSelect"]!=""){?>
										<tr>
											<td><?php if ($disIt==1) {?>
											<div id="showItdetails"  style="display:block"  ><?php } else {?><div id="showItdetails"  style="display:none"  > <?php }?>
											<table width="80%" cellspacing="1" cellpadding="2" border="0" bgcolor="#999999" align="center" >
											<tr bgcolor="#f2f2f2">
											
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" width=200>Supplier</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;"  width=200>Stock</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" width=200>Negoti.Price</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" width=200>Select</td>
										</tr>
										<?php foreach ($supplierStockRecords as $ssr) {
											$supplierName = stripslashes($ssr[12]);
											$stockName		= stripslashes($ssr[13]);
											$negotiatedPrice	= $ssr[4];
											$supplierId		= $ssr[1];
											?>
										<tr bgcolor="White" >
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><?=$supplierName;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><?=$stockName;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><?=$negotiatedPrice;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><input type="radio" name="selectSupplier" id="selectSupplier" value="<?=$supplierId?>" class="chkBox fsaChkbx" onclick="pageLoad(this);"  />
											</td>
										</tr>
									<?php }?>
									</table>
								</div>
							</td>
						</tr>
					<?php }?>
									<tr   align="center">
										<td width="20">&nbsp;</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
										<td class="listing-head"></td>
										<td class="listing-head"></td>
									</tr>
									<tr>
										<td width="1" ></td>
										<td colspan="2" >
											<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
											if( sizeof($purchaseOrderRecords) > 0 )
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
																	$nav.= " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
														} else {
															$next = '&nbsp;'; // we're on the last page, don't print next link
															$last = '&nbsp;'; // nor the last page link
														}
														// print the navigation link
														$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
														echo $first . $prev . $nav . $next . $last . $summary; 
													  ?>
													</div>
												</td>
											</tr>
										<? }?>
										<tr  bgcolor="#f2f2f2" align="center">
											<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO ID</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Remarks</td>
											<td class="listing-head"></td>
											<? if($edit==true){?>
											<td class="listing-head"></td>
											<? }?>
										</tr>
										<?
										foreach ($purchaseOrderRecords as $por) {
											$i++;
											$purchaseOrderId	= $por[0];
											$poId			= $por[1];
											$poNumber		= $por[2];				
											$supplierName		= $por[7];	
											$remarks=$por[9];
											$poinvconfirmed=$por[10];
											
											$total_amount = $purchaseOrderInventoryObj->fetchPurchaseOrderAmount($purchaseOrderId);
											
											$status		=	$por[6];
											if ($status=='C') {
												$displayStatus	=	"Cancelled";
											} else if ($status=='R') {
												$displayStatus	=	"Received";
											} else if ($status=='PC') {
												$displayStatus	=	"Partially<br>Completed";
											} else  { //($status=='P')
												$displayStatus	=	"Pending";
											}
											$disabled = "";
											if ($status=='R') $disabled = "disabled";	
											$basePOId	= $por[8];
											if ($basePOId!="") $basePONumber = $purchaseOrderInventoryObj->getPONumber($basePOId);		
											$displaySuppListName = "";
											if ($basePOId!="" && $basePONumber!="") $displaySuppListName = "(Supplementary of PO $basePONumber)";
											if ($poinvconfirmed==1){
											$disabled = "disabled";	
											}
										?>
										<tr bgcolor="White">
											<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$purchaseOrderId;?>" class="chkBox">
											<input type="hidden" name="recStatus_<?=$i?>" value="<?=$status?>">
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<?=$poId;?><br>
												<span class="fieldName" style="line-height:normal"><?=$displaySuppListName?></span> 
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$total_amount;?></td>
											<td class="listing-item" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayStatus?></td>
											<td class="listing-item" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$remarks?></td>
											<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><a href="javascript:printWindow('ViewPOInventoryDetails.php?selPOId=<?=$purchaseOrderId?>',700,600)" class="link1" title="Click here to view details">View Details</a></td>	
											<? if($edit==true && ( $status=='P' || $status=='PC' ) ){?>
											<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$purchaseOrderId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='PurchaseOrderInventory.php';" <?=$disabled?>></td>
											<? } else if ($edit==true && $status=='R') {?>
											<td></td>
											<? }?>
										</tr>
										<?
											}
										?>
										<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="">
										<input type="hidden" name="editSelectionChange" value="0">
										<? if($maxpage>1){?>
										<tr bgcolor="#FFFFFF">
											<td colspan="7" style="padding-right:10px;">
												<div align="right">
													<?php 				 			  
													$nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
													} else {
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
												  ?>
											</div>
											<input type="hidden" name="pageNo" value="<?=$pageNo?>">
										</td>
									</tr>
									<? }?>
										<?
									} else {
									?>
									<tr bgcolor="white">
										<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>
									<?
										}
									?>
								</table>
								<input type="hidden" name="stockItem" value="<?=$poItem?>"></td>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5" ></td>
						</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td>
												<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button" onClick="return validatePurchaseOrder(document.frmPurchaseOrderInventory);"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPurchaseOrderInventory.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>&os=<?=$offset;?>&lt=<?=$limit;?>, $limit',700,600);"><? }?>
											</td>
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
		<input type="hidden" name="hidSupplierRateListId" id="hidSupplierRateListId" value="<?=$supplierRateListId?>" >
		<input type="hidden" name="hidEditId" value="<?=$hidEditId?>">
		<input type='hidden' name='genPoId' id='genPoId' value="<?=$genPoId;?>" >
		<!--<input name='histatus_0' type='hidden' id='histatus_0'>-->
		<tr>
			<td height="10" bgcolor="#fff"></td>
		</tr>
		<?php
		if ($p["searchMode"]=="I")
		{
			$selected=true;
		}

		?>
	</table>


</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>