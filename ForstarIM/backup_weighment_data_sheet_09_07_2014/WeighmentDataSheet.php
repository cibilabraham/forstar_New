<?php
	require("include/include.php");
	require("lib/WeighmentDataSheet_ajax.php");
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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	$hidEditId = "";
	if ($p["editId"]!="") {
		$hidEditId = $p["editId"];
	} else {
		$hidEditId = $p["hidEditId"];
	}

	
	
	$rmLotIds  = $objWeighmentDataSheet->getAllLotIds();
	$gatePassDetails = $objWeighmentDataSheet->getAllGatePassDetails();
	$purchaseSupervisor = $objWeighmentDataSheet->getAllEmployee();
	$getAllUnits = $objWeighmentDataSheet->getAllUnits();
	$processCodeList = $objWeighmentDataSheet->getAllProcessCodes();
	$packageTypeList = $objWeighmentDataSheet->getAllPackageTypes();
	$chemicalDetails = $objWeighmentDataSheet->getAllChemicalDetails();
	$productSpecies  = $objWeighmentDataSheet->getAllFishDetails();
			//echo "hai";
	$supplyAreaList = $objWeighmentDataSheet->getAllSupplyArea();
	
	$datasList = $objWeighmentDataSheet->getAllWeighmentDataSheet();
	
	$harvestingequipmentNameRecs = $harvestingEquipmentMasterObj->fetchAllRecordsActiveequipmentType();

	$procurementGatePass=$objWeighmentDataSheet->fetchAllProcurementGatePass();
	$supplierGroup	= $rmProcurmentOrderObj->fetchAllSupplierGroupName();
	
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	

	
	if ($p["cmdAdd"]!="" ) {
		//if ($procurmentNo!="")
		//$chkUnique = $rmProcurmentOrderObj->checkUnique($procurmentNos, "");	
		//$requestNo		=	$p["requestNo"];
		$rm_lot_id              = 	$p['rm_lot_id'];
		$data_sheet_slno        = 	$p['data_sheet_slno'];
		$data_sheet_date		=mysqlDateFormat($p["data_sheet_date"]);
		
		//$data_sheet_date		=	mysqlDateFormat($p["data_sheet_date"]);
		$receiving_supervisor   = 	$p['receiving_supervisor'];
		$supplyArea				=	$p["supplyArea"];
		$selRMSupplierGroup		=	$p["selRMSupplierGroup"];
		$procurementAvailable=	$p["procurementAvailable"];	
	
		$procurementGatePass  = $p['procurementGatePass'];
		$gate_pass_details	  = $p['gate_pass_details'];
		$farmer_at_harvest    = $p['farmer_at_harvest'];
		$purchase_supervisor  = $p['purchase_supervisor'];
		$total_quantity  = $p['total_quantity'];
		$total_quantitypro  = $p['total_quantitypro'];
		//$selCompanyName		=	$p["selCompanyName"];
		//total_quantity
		
		 $hidTableRowCounts		=	$p["hidTableRowCounts"];	
		 $hidTableRowCountsVal		=	$p["hidTableRowCountsVal"];
		$hidTableRowCount=$p["hidTableRowCount"];
		$hidChemicalRowCount=$p["hidChemicalRowCount"];	
		
		//if ($procurmentNo!="" && !$chkUnique && $selRMSupplierGroup!="" && $supplierName!="" && $supplierAddress!="" && $pondName!="" && $pondAddress!="" && $entryDate!="") {	
			if($procurementAvailable=="")
			{
			//echo "hii";
			
			if ($rm_lot_id!="" && $data_sheet_slno!="" && $data_sheet_date!="" && $receiving_supervisor!="" && $supplyArea!="" && $selRMSupplierGroup!="" && $total_quantity!="" ) {	
			//echo "hii";
			$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentProcurementNo($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$supplyArea, $selRMSupplierGroup,$total_quantity,$userId);
				if($weightmentDatasheetRecIns)					
				$lastId = $databaseConnect->getLastInsertedId();
	//die(); 
				$unitTrans=$objWeighmentDataSheet->fetchUnit($rm_lot_id);
				$free_rm_lotId=$unitTrans[0];
				$unitVal=$unitTrans[1];	
				$process=$objWeighmentDataSheet->fetchProcessID();	
				$processType=$process[0];
				$unit	=	$objWeighmentDataSheet->addUnittransfer($rm_lot_id,$free_rm_lotId,$unitVal,$processType,$data_sheet_slno,$userId);
				
				//add to unit transfer
				
			  if ($hidTableRowCounts>0 ) {
			
					for ($j=0; $j<$hidTableRowCounts; $j++) {
						$status = $p["mstatus_".$j];
						  if ($status!='N') {
						
						
						$supplierName		=	$p["supplierName_".$j];
						$pondName		=	$p["pondName_".$j];
						$product_species		=	$p["product_species_".$j];
						$process_code=$p["process_code_".$j];
						$count_code		=	$p["count_code_".$j];
						$weight		=	$p["weight_".$j];
						$soft_precent		=	$p["soft_precent_".$j];
						$soft_weight		=	$p["soft_weight_".$j];
						
						//$currentStock = $totalQty - $quantity;
							
						if ($lastId!=""  && $supplierName!=""  && $pondName!="" && $product_species!="" && $process_code!="" && $count_code!="" && $weight!="" && $soft_precent!="" && $soft_weight!="") {
							$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentSupplierProcurementNo($lastId, $supplierName,$pondName,$product_species,$process_code,$count_code,$weight,$soft_precent,$soft_weight);
							
							
						}
					}
				  }
			  }
			  
			 
			  
			  
			  
			} else if ($chkUnique) $err = " Failed to add procurement order. Please make sure the request number you have entered is not duplicate. ";
		}
		else
		{
		
		
		/////*************procurementAvailable=1
			if ($rm_lot_id!="" && $data_sheet_slno!="" && $data_sheet_date!="" && $receiving_supervisor!="" && $procurementGatePass!="" && $gate_pass_details!="" && $farmer_at_harvest!="" && $purchase_supervisor!="" && $total_quantitypro!=""  && $procurementAvailable!="") {	
				
				$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentProcurementValue($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$procurementGatePass,$gate_pass_details,$farmer_at_harvest,$purchase_supervisor,$total_quantitypro,$procurementAvailable,$userId);
				//echo "hii"; 
				//die();
				if($weightmentDatasheetRecIns)					
				$lastId = $databaseConnect->getLastInsertedId();
				
				$unitTrans=$objWeighmentDataSheet->fetchUnit($rm_lot_id);
				$free_rm_lotId=$unitTrans[0];
				$unitVal=$unitTrans[1];	
				$process=$objWeighmentDataSheet->fetchProcessID();	
				$processType=$process[0];
				$unit	=	$objWeighmentDataSheet->addUnittransfer($rm_lot_id,$free_rm_lotId,$unitVal,$processType,$data_sheet_slno,$userId);
				
				
				
				if ($hidTableRowCountsVal>0 ) {
			   
					for ($v=0; $v<$hidTableRowCountsVal; $v++) {
						$status = $p["wstatus_".$v];
						  if ($status!='N') {
						
							
						$supplierNamepro		=	$p["supplierNamepro_".$v];
						$pondNamepro		=	$p["pondNamepro_".$v];
						$product_speciespro		=	$p["product_speciespro_".$v];
						$processCodeValue		=	$p["processCodeValue_".$v];
						$count_codepro		=	$p["count_codepro_".$v];
						$weightpro		=	$p["weightpro_".$v];
						$soft_precentpro		=	$p["soft_precentpro_".$v];
						$soft_weightpro		=	$p["soft_weightpro_".$v];
						$pkg_typepro		=	$p["packageTypepro_".$v];
						$pkg_nospro		=	$p["pkg_nospro_".$v];
						//$currentStock = $totalQty - $quantity;
						 //echo "hii";	
						if ($lastId!=""  && $supplierNamepro!=""  && $pondNamepro!="" && $product_speciespro!="" && $processCodeValue!="" && $count_codepro!="" && $weightpro!="" && $soft_precentpro!="" && $soft_weightpro!="" && $pkg_typepro!="" && $pkg_nospro!="") {
						 //echo "hui";
							$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentSupplierProcurementValue($lastId, $supplierNamepro,$pondNamepro,$product_speciespro,$processCodeValue,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$pkg_typepro,$pkg_nospro);
							
						}
					}
				  }
			  }
			  //die();
				 if ($hidTableRowCount>0 ) {
			
					for ($k=0; $k<$hidTableRowCount; $k++) {
						$status = $p["status_".$k];
						  if ($status!='N') {
						
						
						$equipmentNameId		=	$p["equipmentName_".$k];
						$equipmentIssued		=	($p["equipmentIssued_".$k]);
						$equipmentReturned		=	($p["equipmentReturned_".$k]);
						$balanceQty		=	$p["balanceQty_".$k];
						//$currentStock = $totalQty - $quantity;
							
						if ($lastId!="" && $equipmentNameId!=""  && $equipmentIssued!="" && $equipmentReturned!="" && $balanceQty!="" ) {
						
							$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentEquipmentProcurementValue($lastId, $equipmentNameId,$equipmentIssued,$equipmentReturned,$balanceQty);
								
						}
					}
				  }
			  }
				if ($hidChemicalRowCount>0 ) {
			
					for ($g=0; $g<$hidChemicalRowCount; $g++) {
						$status = $p["bstatus_".$g];
						  if ($status!='N') {
						
						
						$chemicalNameId		=	$p["chemicalName_".$g];
						$chemicalIssued		=	$p["chemicalIssued_".$g];
						$chemicalUsed		=	$p["chemicalUsed_".$g];
						$chemicalReturned		=	$p["chemicalReturned_".$g];
						$differenceQty		=	$p["differenceQty_".$g];
						//$currentStock = $totalQty - $quantity;
							
						if ($lastId!=""  && $chemicalNameId!=""  && $chemicalIssued!="" && $chemicalUsed!="" && $chemicalReturned!="" && $differenceQty!="" ) {
							$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentChemicalProcurementValue($lastId, $chemicalNameId,$chemicalIssued,$chemicalUsed,$chemicalReturned,$differenceQty);
								
						}
					}
				  }
			  }
				
			}else if ($chkUnique) $err = " Failed to add procurement order. Please make sure the request number you have entered is not duplicate. ";
		}
		
		
		
		
			if ($weightmentDatasheetRecIns) {
				if( $err!="" ) printJSAlert($err);
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddWeightmentData);
				$sessObj->createSession("nextPage",$url_afterAddWeightmentData.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddWeightmentData;
			}
			$weightmentDatasheetRecIns		=	false;
			$hidEditId 	=  "";
	}
	
	
		if ($p["editId"]!="") {
	$i=0;
		$editId			=	$p["editId"];
		$editMode		=	true;
	
		$editDatas = $objWeighmentDataSheet->getEditDatas((int)$p['editId']);
		 $weightmentId=$editDatas[0];
		$rm_lot_id            = $editDatas[1];
		$data_sheet_slno      = $editDatas[2];
		$data_sheet_date      = dateformat($editDatas[3]);
		$receiving_supervisor = $editDatas[8];
		$procurementAvailable=$editDatas[12];
		
		//$editProcurmentId	=	$procurmentOrderRec[0];	
		
		$selprocurement            = $editDatas[4];
		$gate_pass_details	  = $editDatas[5];
		
		$farmer_at_harvest    = $editDatas[6];
		// $product_species      = $editDatas[9];
		$purchase_supervisor  = $editDatas[7];
		
		$selsupplyList    = $editDatas[9];
		$selRMSupplierGroup  = $editDatas[10];
		//getSupplierData($sir[2],$procurementId)
		if($procurementAvailable=="0")
		{
			$supplierRecsVal 		= $objWeighmentDataSheet->getWeightmentSupplierProNo($weightmentId);
			$supplierRecs 			= $rmProcurmentOrderObj->getfilterSupplierList($selRMSupplierGroup);

			 foreach($supplierRecs as $supplierS )
			{
			$supplierNames=	 $supplierS[1];
			
			$pondRecs[] 			= $objWeighmentDataSheet->getfilterPondList($supplierNames);
			
			foreach($pondRecs as $pndval)
			{
			 foreach($pndval as $pnd)
			 {
			 $pondvals=$pnd[1];
			//echo "ho";
			$speciesRecs[] 			= $objWeighmentDataSheet->getfilterPondSpecies($pondvals,$supplierNames);
			
			foreach($speciesRecs as $speciesVal)
			{
				foreach($speciesVal as $spe)
				{
				// foreach($spe as $spValue)
				// {
				$speciesvals=$spe[1];
				$processCodeRecs[] 			= $objWeighmentDataSheet->getfilterProcessCode($speciesvals);
				//}
				}
				
			}
			
			}
			
			}
			}
		}
		else
		{
		$supplierRecsVal 		= $objWeighmentDataSheet->getWeightmentSupplierProNo($weightmentId);
			$result = $objWeighmentDataSheet->getProcurementOrderID($selprocurement);
			$proID=$result[0];
			//$objResponse->alert($proID);
			$supplierRecs 			= $objWeighmentDataSheet->WeightmentEditSupplier($proID);
			$packageTypeRecs 			= $objWeighmentDataSheet->WeightmentEditPackage($proID);
			//echo $supplySize=sizeof($supplierRecs); 
			?>
			
			<?php
			foreach($supplierRecs as $supplierS )
			{
			$supplierNames=	 $supplierS[1];
			
			$pondRecs[] 			= $objWeighmentDataSheet->WeightmentEditPond($proID);
			
			foreach($pondRecs as $pndval)
			{
			 foreach($pndval as $pnd)
			 {
			 $pondvals=$pnd[1];
			//echo "ho";
			$speciesRecs[] 			= $objWeighmentDataSheet->WeightmentEditSpecies($pondvals);
			foreach($speciesRecs as $speciesVal)
			{
				foreach($speciesVal as $spe)
				{
				// foreach($spe as $spValue)
				// {
				$speciesvals=$spe[1];
				$processCodeRecs[] 			= $objWeighmentDataSheet->WeightmentEditProcessCode($speciesvals);
				//}
				}
				
			}
			}
			}
			}
		$equipmentRecsVal 		= $objWeighmentDataSheet->getWeightmentEditEquipment($weightmentId);	
		$chemicalRecsVal 		= $objWeighmentDataSheet->getWeightmentEditChemical($weightmentId);		
		$harvestingchemicalNameRecs = $objWeighmentDataSheet->WeightmentEditChemicalVal($proID);	
		}
		
		
		$entryDate	=	dateformat($procurmentOrderRec[5]);
		//$entryDate	=	dateformat($procurmentOrderRec[9]);
		$driverName =	$procurmentOrderRec[3];
		$vehicleNo =	$procurmentOrderRec[4];
		
		$procurmnentGatePass 			= $rmProcurmentOrderObj->getGatePassForEdit($procurmentNo);
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
		}
		
		// Get procurment Records
		//$procurmentDetailsRecs = $rmProcurmentOrderObj->fetchAllProcurmentEntries($editProcurmentId);
		$procurmentSupplierRecs = $rmProcurmentOrderObj->fetchAllProcurmentSupplier($editProcurmentId);
		$procurmentEquipmentRecs = $rmProcurmentOrderObj->fetchAllProcurmentEquipment($editProcurmentId);
		$procurmentChemicalRecs = $rmProcurmentOrderObj->fetchAllProcurmentChemical($editProcurmentId);
		//$issuanceRecs = $rmProcurmentOrderObj->fetchAllProcurmentDetails($editProcurmentId);
	}
	
	
	#Update 
	if ($p["cmdUpdate"]!="") {	
//echo "hiii";	
//die();
		$weightmentId	=	$p["hidWeightmentId"];
		$rm_lot_id              = 	$p['rm_lot_id'];
		$data_sheet_slno        = 	$p['data_sheet_slno'];
		$data_sheet_date		=mysqlDateFormat($p["data_sheet_date"]);
		
		//$data_sheet_date		=	mysqlDateFormat($p["data_sheet_date"]);
		$receiving_supervisor   = 	$p['receiving_supervisor'];
		$supplyArea				=	$p["supplyArea"];
		$selRMSupplierGroup		=	$p["selRMSupplierGroup"];
		$procurementAvailable=	$p["procurementAvailable"];	
	
		$procurementGatePass  = $p['procurementGatePass'];
		$gate_pass_details	  = $p['gate_pass_details'];
		$farmer_at_harvest    = $p['farmer_at_harvest'];
		$purchase_supervisor  = $p['purchase_supervisor'];
		$total_quantity  = $p['total_quantity'];
		$total_quantitypro  = $p['total_quantitypro'];
		
		$hidTableRowCounts		=	$p["hidTableRowCounts"];	
		 $hidTableRowCountsVal		=	$p["hidTableRowCountsVal"];
		$hidTableRowCount=$p["hidTableRowCount"];
		$hidChemicalRowCount=$p["hidChemicalRowCount"];	
		
		
		if($procurementAvailable=="")
			{
			//echo "hii";
			
			if ($rm_lot_id!="" && $data_sheet_slno!="" && $data_sheet_date!="" && $receiving_supervisor!="" && $supplyArea!="" && $selRMSupplierGroup!="" && $total_quantity!="" && $weightmentId!="" ) {	
			//echo "hii";
			$weightmentRecUptd	=	$objWeighmentDataSheet->updateWeightmentProcurementNo($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$supplyArea, $selRMSupplierGroup,$total_quantity,$weightmentId);
				
	//die(); 
				$unitTrans=$objWeighmentDataSheet->fetchUnit($rm_lot_id);
				$free_rm_lotId=$unitTrans[0];
				$unitVal=$unitTrans[1];	
				$process=$objWeighmentDataSheet->fetchProcessID();	
				$processType=$process[0];
				$unit	=	$objWeighmentDataSheet->updateUnittransfer($rm_lot_id,$free_rm_lotId,$unitVal,$processType,$data_sheet_slno);
				
				//add to unit transfer
				
				for ($e=0; $e<$hidTableRowCounts; $e++) {
			   $status = $p["mstatus_".$e];
			   $rmId  = $p["mrmId_".$e];
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				
						$supplierName		=	$p["supplierName_".$j];
						$pondName		=	$p["pondName_".$j];
						$product_species		=	$p["product_species_".$j];
						$process_code		=	$p["process_code_".$j];
						$count_code		=	$p["count_code_".$j];
						$weight		=	$p["weight_".$j];
						$soft_precent		=	$p["soft_precent_".$j];
						$soft_weight		=	$p["soft_weight_".$j];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($weightmentId!=""  && $supplierName!=""  && $pondName!="" && $product_species!="" && $process_code && $count_code!="" && $weight!="" && $soft_precent!="" && $soft_weight!="" && $rmId!=""){
					
						$weightmentRecUptd	=	$objWeighmentDataSheet->updateWeightmentSupplierProcurementNo($rmId,$supplierName,$pondName,$product_species,$process_code,$count_code,$weight,$soft_precent,$soft_weight);
						
					} else if ($weightmentId!=""  && $supplierName!=""  && $pondName!="" && $product_species!="" && $process_code!="" && $count_code!="" && $weight!="" && $soft_precent!="" && $soft_weight!="" && $rmId==""){
					
						$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentSupplierProcurementNo($weightmentId,$supplierName,$pondName,$product_species,$process_code,$count_code,$weight,$soft_precent,$soft_weight);
							
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					/*$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					if (!$testMethodInUse)*/ $delSupplierNo = $objWeighmentDataSheet->delProcurementSupplierNo($rmId);
						
				}
			}
	
			  
			} else if ($chkUnique) $err = " Failed to add procurement order. Please make sure the request number you have entered is not duplicate. ";
		}
		else
		{
		
		
		/////*************procurementAvailable=1
			if ($rm_lot_id!="" && $data_sheet_slno!="" && $data_sheet_date!="" && $receiving_supervisor!="" && $procurementGatePass!="" && $gate_pass_details!="" && $farmer_at_harvest!="" && $purchase_supervisor!="" && $total_quantitypro!="" && $procurementAvailable!=""  && $weightmentId!="") {	
				
				$weightmentRecUptd	=	$objWeighmentDataSheet->updateWeightmentProcurementValue($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$procurementGatePass,$gate_pass_details,$farmer_at_harvest,$purchase_supervisor,$total_quantitypro,$procurementAvailable,$weightmentId);
				//die();	
				//echo "hii"; 
				//die();
				$unitTrans=$objWeighmentDataSheet->fetchUnit($rm_lot_id);
				$free_rm_lotId=$unitTrans[0];
				$unitVal=$unitTrans[1];	
				$process=$objWeighmentDataSheet->fetchProcessID();	
				$processType=$process[0];
				$unit	=	$objWeighmentDataSheet->updateUnittransfer($rm_lot_id,$free_rm_lotId,$unitVal,$processType,$data_sheet_slno);
				
				
					for ($v=0; $v<$hidTableRowCountsVal; $v++) {
			   $status = $p["wstatus_".$v];
			   $rmId  = $p["wrmId_".$v];
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				
						$supplierNamepro		=	$p["supplierNamepro_".$v];
						$pondNamepro		=	$p["pondNamepro_".$v];
						$product_speciespro		=	$p["product_speciespro_".$v];
						$processCodeValue		=	$p["processCodeValue_".$v];
						$count_codepro		=	$p["count_codepro_".$v];
						$weightpro		=	$p["weightpro_".$v];
						$soft_precentpro		=	$p["soft_precentpro_".$v];
						$soft_weightpro		=	$p["soft_weightpro_".$v];
						$pkg_typepro		=	$p["packageTypepro_".$v];
						$pkg_nospro		=	$p["pkg_nospro_".$v];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($weightmentId!=""   && $supplierNamepro!=""  && $pondNamepro!="" && $product_speciespro!="" && $processCodeValue!="" && $count_codepro!="" && $weightpro!="" && $soft_precentpro!="" && $soft_weightpro!="" && $pkg_typepro!="" && $pkg_nospro!="" && $rmId!=""){
					
						$weightmentRecUptd	=	$objWeighmentDataSheet->updateWeightmentSupplierProcurementVal($rmId,$supplierNamepro,$pondNamepro,$product_speciespro,$processCodeValue,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$pkg_typepro,$pkg_nospro);
					//die();	
					} else if ($weightmentId!="" && $supplierNamepro!=""  && $pondNamepro!="" && $product_speciespro!="" && $processCodeValue!="" && $count_codepro!="" && $weightpro!="" && $soft_precentpro!="" && $soft_weightpro!="" && $pkg_typepro!="" && $pkg_nospro!="" && $rmId==""){
					
						$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentSupplierProcurementValue($weightmentId,$supplierNamepro,$pondNamepro,$product_speciespro,$processCodeValue,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$pkg_typepro,$pkg_nospro);
							
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					/*$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					if (!$testMethodInUse)*/ $delSupplierVal = $objWeighmentDataSheet->delProcurementSupplierNo($rmId);
						
				}
			}
			
			for ($k=0; $k<$hidTableRowCount; $k++) {
			   $status = $p["status_".$k];
			   $rmId  		= $p["rmId_".$k];
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				
				$equipmentNameId		=	$p["equipmentName_".$k];
				$equipmentIssued		=	($p["equipmentIssued_".$k]);
				$equipmentReturned		=	($p["equipmentReturned_".$k]);
				$balanceQty		=	$p["balanceQty_".$k];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($weightmentId!="" && $equipmentNameId!=""  && $equipmentIssued!="" && $equipmentReturned!="" && $balanceQty!="" && $rmId!="") {
					
						$weightmentRecUptd = $objWeighmentDataSheet->updateEquipmentProcurementValue($rmId,$equipmentNameId,$equipmentIssued,$equipmentReturned,$balanceQty);
					
					
					} else 	if ($weightmentId!="" && $equipmentNameId!=""  && $equipmentIssued!="" && $equipmentReturned!="" && $balanceQty!="" && $rmId=="" ) {
						
							$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentEquipmentProcurementValue($weightmentId, $equipmentNameId,$equipmentIssued,$equipmentReturned,$balanceQty);
						}
					//die;
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					/*$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					if (!$testMethodInUse)*/ $delweightmentEquipmentRec = $objWeighmentDataSheet->delWeighmentEquipmentRec($rmId);
						
				}
			} // Test Master Loop ends here
			// print_r($p);die;
			
			 
			//print_r($p); die();
			for ($g=0; $g<$hidChemicalRowCount; $g++) {
			//echo "fii";
			//$status = $p["histatus_".$r];
			 $status = $p["bstatus_".$g];
			 $rmId  		= $p["brmId_".$g];
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				$chemicalNameId		=	$p["chemicalName_".$g];
						$chemicalIssued		=	$p["chemicalIssued_".$g];
						$chemicalUsed		=	$p["chemicalUsed_".$g];
						$chemicalReturned		=	$p["chemicalReturned_".$g];
						$differenceQty		=	$p["differenceQty_".$g];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($weightmentId!=""  && $chemicalNameId!=""  && $chemicalIssued!="" && $chemicalUsed!="" && $chemicalReturned!="" && $differenceQty!="" && $rmId!="") {
					//echo "hii";
						$weightmentRecUptd = $objWeighmentDataSheet->updateWeightmentChemicalProcurementValue($rmId,$chemicalNameId,$chemicalIssued,$chemicalUsed,$chemicalReturned,$differenceQty);
						
					} else if($weightmentId!=""   && $chemicalNameId!=""  && $chemicalIssued!="" && $chemicalUsed!="" && $chemicalReturned!="" && $differenceQty!="" && $rmId=="" ) {

					//($procurementId!="" &&  $equipmentName!=""  && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!=""  && $rmId=="")  {	
						$weightmentDatasheetRecIns	=	$objWeighmentDataSheet->addWeightmentChemicalProcurementValue($weightmentId,$chemicalNameId,$chemicalIssued,$chemicalUsed,$chemicalReturned,$differenceQty);
							
						//$detailsIns = $rmProcurmentOrderObj->addProcurmentEquipment($procurementId, $equipmentName, $equipmentQty,$equipmentIssued,$balanceQty);
					}
					//die;
				} // Status Checking End
				//echo $status;
				//echo $rmId;
				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					/*$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					if (!$testMethodInUse)*/ $delweightmentDatasheetChemicalRec = $objWeighmentDataSheet->delWeighmentChemicalRec($rmId);
						
				}
			} 

				
			}else if ($chkUnique) $err = " Failed to add procurement order. Please make sure the request number you have entered is not duplicate. ";
		}

		if ($weightmentRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succWeightmentDataUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightmentData.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failWeightmentDataUpdate;
		}
		$weightmentRecUptd	=	false;
		$hidEditId 	= "";
	}
	
	
	
	
	
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$weightmentId	=	$p["confirmId"];
			if ($weightmentId!="") {
				// Checking the selected fish is link with any other process
				$weightmentRecConfirm = $objWeighmentDataSheet->updateWeighmentconfirm($weightmentId);
			}

		}
		if ($procurmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmWeightmentDataSheet);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightmentData.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	


if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$weightmentId	=	$p["delId_".$i];

			if ($weightmentId!="" && $isAdmin!="") {
				$deleteweightmentRecs	=	$objWeighmentDataSheet->deleteWeightmentGroup($weightmentId);
				$weightmentRecDel =	$objWeighmentDataSheet->deleteWeightmentSupplier($weightmentId);
				$weightmentRecDel2 =$objWeighmentDataSheet->deleteWeightmentEquipment($weightmentId);
				$weightmentRecDel3 =$objWeighmentDataSheet->deleteWeightmentChemical($weightmentId);	
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
		
		
		
		
		
		
	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$weightmentId = $p["confirmId"];
			if ($weightmentId!="") {
				#Check any entries exist
				
					$weightmentRecConfirm = $objWeighmentDataSheet->updateWeighmentReleaseconfirm($weightmentId);
				
			}
		}
		if ($weightmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmWeightmentDataSheet);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightmentData.$selection);
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

		$WeighmentDataSheetRecords	= $objWeighmentDataSheet->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$WeighmentDataSheetSize	= sizeof($WeighmentDataSheetRecords);
		$fetchAllStockIssuanceRecs = $stockissuanceObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllStockIssuanceRecs);
	$maxpage	=  ceil($numrows/$limit);
	
	
	
	// echo '<pre>';
		// print_r($p);
	// echo '</pre>';
	//$multipleEditDatas = array();
	
//*****************************************************************************************************************************/
	
	/*if(isset($p['rm_lot_id']))
	{
		$rm_lot_id            = $p['rm_lot_id'];
		$data_sheet_slno      = $p['data_sheet_slno'];
		$data_sheet_date      = $p['data_sheet_date'];
		$gate_pass            = $p['gate_pass'];
		$gate_pass_details	  = $p['gate_pass_details'];
		$pond_id              = $p['pond_id'];
		$pond_details         = $p['pond_details'];
		$farmer_at_harvest    = $p['farmer_at_harvest'];
		$product_species      = $p['product_species'];
		$purchase_supervisor  = $p['purchase_supervisor'];
		$process_code         = $p['process_code'];
		$grade_count          = $p['grade_count'];
		$count_code           = $p['count_code'];
		$weight               = $p['weight'];
		$soft_precent         = $p['soft_precent'];
		$soft_weight          = $p['soft_weight'];
		$package_type         = $p['package_type'];
		$pkg_nos              = $p['pkg_nos'];
		$total_quantity       = $p['total_quantity'];
		$received_at_unit     = $p['received_at_unit'];
		$receiving_supervisor = $p['receiving_supervisor'];
		$harvesting_equipment = $p['harvesting_equipment'];
		$issued               = $p['issued'];
		$used                 = $p['used'];
		$returned             = $p['returned'];
		$different            = $p['different'];
	}
	else if(isset($p['editId']) && (int)$p['editId'] != 0)
	{
	
		$editMode		=	true;
		$editDatas = $objWeighmentDataSheet->getEditDatas((int)$p['editId']);
		$rm_lot_id            = $editDatas[1];
		$data_sheet_slno      = $editDatas[2];
		$data_sheet_date      = $editDatas[3];
		$gate_pass            = $editDatas[4];
		$gate_pass_details	  = $editDatas[5];
		$pond_id              = $editDatas[6];
		$pond_details         = $editDatas[7];
		$farmer_at_harvest    = $editDatas[8];
		// $product_species      = $editDatas[9];
		$purchase_supervisor  = $editDatas[10];
		// $process_code         = $editDatas[11];
		
		
		$multipleEditDatas          = $objWeighmentDataSheet->getEditDatasMultiple('*','t_weightment_data_entries',(int)$p['editId']);//$editDatas[11];
		//echo '<pre>';
		//	print_r($multipleEditDatas);
		//echo '</pre>';
		// $grade_count = array_map('current', $grade_count);
		
		// $count_code          = $objWeighmentDataSheet->getEditDatasMultiple('count_code','weighment_data_sheet_count_code',(int)$p['editId']);//$editDatas[11];
		// $count_code = array_map('current', $count_code);
		
		// $weight          = $objWeighmentDataSheet->getEditDatasMultiple('weight','weighment_data_sheet_weight',(int)$p['editId']);//$editDatas[11];
		// $weight = array_map('current', $weight);
		
		// $soft_precent          = $objWeighmentDataSheet->getEditDatasMultiple('soft_precent','weighment_data_sheet_soft_precent',(int)$p['editId']);//$editDatas[11];
		// $soft_precent = array_map('current', $soft_precent);
		
		// $soft_weight          = $objWeighmentDataSheet->getEditDatasMultiple('soft_weight','weighment_data_sheet_soft_weight',(int)$p['editId']);//$editDatas[11];
		// $soft_weight = array_map('current', $soft_weight);
		
		// $pkg_nos          = $objWeighmentDataSheet->getEditDatasMultiple('pkg_nos','weighment_data_sheet_pkg_nos',(int)$p['editId']);//$editDatas[11];
		// $pkg_nos = array_map('current', $pkg_nos);
		
		// $package_type         = $editDatas[17];
		$total_quantity       = $editDatas[19];
		$received_at_unit     = $editDatas[20];
		$receiving_supervisor = $editDatas[21];
		$harvesting_equipment = $editDatas[22];
		$issued               = $editDatas[23];
		$used                 = $editDatas[24];
		$returned             = $editDatas[25];
		$different            = $editDatas[26];
	}*/
	if ($p["cmdDelete"]!="") 
	{
		$rowCount	=	$p["hidRowCount"];
		$deleteIDS = '';
		for ($i=1; $i<=$rowCount; $i++) 
		{
			if(isset($p["delId_".$i]))
			{
				if($deleteIDS == '')
				{	
					$deleteIDS = $p["delId_".$i];
				}
				else
				{
					$deleteIDS.= ','.$p["delId_".$i];
				}
			}
			if($deleteIDS != '')
			{
				$objWeighmentDataSheet->deleteData($deleteIDS);
				$objWeighmentDataSheet->deleteMultipleDatas('t_weightment_data_entries',$deleteIDS);
				// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_count_code',$deleteIDS);
				// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_weight',$deleteIDS);
				// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_soft_precent',$deleteIDS);
				// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_soft_weight',$deleteIDS);
				// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_pkg_nos',$deleteIDS);
				$msg_succAdd = 'Weighment data sheet deleted successfully';
				$url = 'WeighmentDataSheet.php';
				$sessObj->createSession("displayMsg",$msg_succAdd);
				$sessObj->createSession("nextPage",$url);
			}
		}
	}
	
	/*if(isset($p['cmdAdd']) && $p['cmdAdd'] == 'Add')
	{
		$insertArray = array('rm_lot_id'            => $p['rm_lot_id'],
							 'data_sheet_sl_no'     => $p['data_sheet_slno'],
							 'data_sheet_date'      => $p['data_sheet_date'],
							 'gate_pass'            => $p['gate_pass'],
							 'gatepass_details'	=> $p['gate_pass_details'],
							 'pond_id'              => $p['pond_id'],
							 'pond_details'         => $p['pond_details'],
							 'farmer_at_harvest'    => $p['farmer_at_harvest'],
							 'purchase_supervisor'  => $p['purchase_supervisor'],							 
							 'total_quantity'       => $p['total_quantity'],
							 'received_at_unit'     => $p['received_at_unit'],
							 'receiving_supervisor' => $p['receiving_supervisor'],
							 'harvesting_equipment' => $p['harvesting_equipment'],
							 'issued'               => $p['issued'],
							 'used'                 => $p['used'],
							 'returned'             => $p['returned'],
							 'different'            => $p['different']
							);
		$addStatus = $objWeighmentDataSheet->addData($insertArray);
		
		$grade_count     = $p['grade_count'];
		$count_code      = $p['count_code'];
		$weight          = $p['weight'];
		$soft_precent    = $p['soft_precent'];
		$soft_weight     = $p['soft_weight'];
		$pkg_nos         = $p['pkg_nos'];
		$package_type    = $p['package_type'];
		$process_code    = $p['process_code'];
		$product_species = $p['product_species'];
		
		if ($addStatus) $mainID = $databaseConnect->getLastInsertedId();
		
		if(sizeof($grade_count) > 0)
		{	
			$insertQry = "INSERT INTO t_weightment_data_entries  
							 (weightment_data_sheet_id,product_species,product_code,packaging_type,grade_count,
							 count_code, weight,soft_per,soft_weight,package_nos) VALUES ";
			$i = 0;
			foreach($grade_count as $gcount)
			{
				if($i == 0)
				{
					$insertQry.= "('".$mainID."','".$product_species[$i]."','".$process_code[$i]."','".$package_type[$i]."',
								   '".$gcount."','".$count_code[$i]."','".$weight[$i]."',
								   '".$soft_precent[$i]."','".$soft_weight[$i]."','".$pkg_nos[$i]."')";
				}
				else
				{
					$insertQry.= ",('".$mainID."','".$product_species[$i]."','".$process_code[$i]."','".$package_type[$i]."',
								   '".$gcount."','".$count_code[$i]."','".$weight[$i]."',
								   '".$soft_precent[$i]."','".$soft_weight[$i]."','".$pkg_nos[$i]."')";
				}
				$i++;
			}
			$objWeighmentDataSheet->addMultipleData($insertQry);
		}
				$msg_succAdd = 'Weighment data sheet details added successfully';
		$url = 'WeighmentDataSheet.php';
		$sessObj->createSession("displayMsg",$msg_succAdd);
		$sessObj->createSession("nextPage",$url);
	}*/
	/*if(isset($p['cmdUpdate']))
	{
		$updateArray = array('rm_lot_id'            => $p['rm_lot_id'],
							 'data_sheet_sl_no'     => $p['data_sheet_slno'],
							 'data_sheet_date'      => $p['data_sheet_date'],
							 'gate_pass'            => $p['gate_pass'],
							 'gatepass_details'	 => $p['gate_pass_details'],
							 'pond_id'              => $p['pond_id'],
							 'pond_details'         => $p['pond_details'],
							 'farmer_at_harvest'    => $p['farmer_at_harvest'],							 
							 'purchase_supervisor'  => $p['purchase_supervisor'],								 
							 'total_quantity'       => $p['total_quantity'],
							 'received_at_unit'     => $p['received_at_unit'],
							 'receiving_supervisor' => $p['receiving_supervisor'],
							 'harvesting_equipment' => $p['harvesting_equipment'],
							 'issued'               => $p['issued'],
							 'used'                 => $p['used'],
							 'returned'             => $p['returned'],
							 'different'            => $p['different']
							);
		$id = $p['editId'];
		$objWeighmentDataSheet->updateData($updateArray,$id);
		$mainID = $id;
		
		if(sizeof($grade_count) > 0)
		{
			$objWeighmentDataSheet->deleteMultipleDatas('t_weightment_data_entries',$mainID);
			$insertQry = "INSERT INTO t_weightment_data_entries  
							 (weightment_data_sheet_id,product_species,product_code,packaging_type,grade_count,
							 count_code, weight,soft_per,soft_weight,package_nos) VALUES ";
			$i = 0;
			foreach($grade_count as $gcount)
			{
				if($i == 0)
				{
					$insertQry.= "('".$mainID."','".$product_species[$i]."','".$process_code[$i]."','".$package_type[$i]."',
								   '".$gcount."','".$count_code[$i]."','".$weight[$i]."',
								   '".$soft_precent[$i]."','".$soft_weight[$i]."','".$pkg_nos[$i]."')";
				}
				else
				{
					$insertQry.= ",('".$mainID."','".$product_species[$i]."','".$process_code[$i]."','".$package_type[$i]."',
								   '".$gcount."','".$count_code[$i]."','".$weight[$i]."',
								   '".$soft_precent[$i]."','".$soft_weight[$i]."','".$pkg_nos[$i]."')";
				}
				$i++;
			}
			$objWeighmentDataSheet->addMultipleData($insertQry);
		}*/
		
		
		
		// if(sizeof($count_code) > 0)
		// {	
			// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_count_code',$mainID);
			// $insertQry = "INSERT INTO weighment_data_sheet_count_code 
							 // (main_id,count_code) VALUES ";
			// $i = 0;
			// foreach($count_code as $gcount)
			// {
				// if($i == 0)
				// {
					// $insertQry.= "('".$mainID."','".$gcount."')";
				// }
				// else
				// {
					// $insertQry.= ",('".$mainID."','".$gcount."')";
				// }
				// $i++;
			// }
			// $objWeighmentDataSheet->addMultipleData($insertQry);
		// }
		// if(sizeof($weight) > 0)
		// {	
			// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_weight',$mainID);
			// $insertQry = "INSERT INTO weighment_data_sheet_weight 
							 // (main_id,weight) VALUES ";
			// $i = 0;
			// foreach($weight as $gcount)
			// {
				// if($i == 0)
				// {
					// $insertQry.= "('".$mainID."','".$gcount."')";
				// }
				// else
				// {
					// $insertQry.= ",('".$mainID."','".$gcount."')";
				// }
				// $i++;
			// }
			// $objWeighmentDataSheet->addMultipleData($insertQry);
		// }
		// if(sizeof($soft_precent) > 0)
		// {	
			// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_soft_precent',$mainID);
			// $insertQry = "INSERT INTO weighment_data_sheet_soft_precent 
							 // (main_id,soft_precent) VALUES ";
			// $i = 0;
			// foreach($soft_precent as $gcount)
			// {
				// if($i == 0)
				// {
					// $insertQry.= "('".$mainID."','".$gcount."')";
				// }
				// else
				// {
					// $insertQry.= ",('".$mainID."','".$gcount."')";
				// }
				// $i++;
			// }
			// $objWeighmentDataSheet->addMultipleData($insertQry);
		// }
		// if(sizeof($soft_weight) > 0)
		// {	
			// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_soft_weight',$mainID);
			// $insertQry = "INSERT INTO weighment_data_sheet_soft_weight 
							 // (main_id,soft_weight) VALUES ";
			// $i = 0;
			// foreach($soft_weight as $gcount)
			// {
				// if($i == 0)
				// {
					// $insertQry.= "('".$mainID."','".$gcount."')";
				// }
				// else
				// {
					// $insertQry.= ",('".$mainID."','".$gcount."')";
				// }
				// $i++;
			// }
			// $objWeighmentDataSheet->addMultipleData($insertQry);
		// }
		// if(sizeof($pkg_nos) > 0)
		// {	
			// $objWeighmentDataSheet->deleteMultipleDatas('weighment_data_sheet_pkg_nos',$mainID);
			// $insertQry = "INSERT INTO weighment_data_sheet_pkg_nos 
							 // (main_id,pkg_nos) VALUES ";
			// $i = 0;
			// foreach($pkg_nos as $gcount)
			// {
				// if($i == 0)
				// {
					// $insertQry.= "('".$mainID."','".$gcount."')";
				// }
				// else
				// {
					// $insertQry.= ",('".$mainID."','".$gcount."')";
				// }
				// $i++;
			// }
			// $objWeighmentDataSheet->addMultipleData($insertQry);
		// }
		/*$msg_succAdd = 'Weighment data sheet details update successfully';
		$url = 'WeighmentDataSheet.php';
		$sessObj->createSession("displayMsg",$msg_succAdd);
		$sessObj->createSession("nextPage",$url);
	}*/
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/WeightmentDatasheet.js"; // For Printing JS in Head section
	
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="WeighmentDataSheet" id="WeighmentDataSheet" action="WeighmentDataSheet.php" method="post" 
<?php
if(isset($p['cmdAddNew']) || (isset($p['editId']) && (int)$p['editId'] != 0))
{ 
?>  
  onsubmit="return validateWeightmentDatasheet(document.WeighmentDataSheet);"  
<?php
}
?>

>
	<table width="70%" align="center" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td height="20" align="center" class="err1"> </td>
			</tr>	
			<?php
			if(isset($p['cmdAddNew']) || (isset($p['editId']) && (int)$p['editId'] != 0))
			{
				$buttonValue = "Add";
				$buttonName  = "cmdAdd";
				if(isset($p['editId']) && (int)$p['editId'] != 0)
				{
					$buttonValue = "Update";
					$buttonName  = "cmdUpdate";
				}
			?>
				<tr>
					<td>
						<table width="70%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
							<tbody>
								<tr>
									<td bgcolor="white">
									<!-- Form fields start -->
										<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
											<tbody>
												<tr>
													<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
													<td width="581" background="images/heading_bg.gif" class="pageName" colspan="2"> Add New Weighment Data Sheet </td>
												</tr>												
												<tr>
													<td height="10"></td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													
													<td align="center" colspan="2">
														<input type="button" onclick="return cancel('WeighmentDataSheet.php');" value=" Cancel " class="button" name="cmdCancel">&nbsp;&nbsp;
														<input type="submit" value="<?php echo $buttonValue;?>" class="button" id="cmdAdd1" name="<?php echo $buttonName;?>" > &nbsp;&nbsp;												
													</td>													
												</tr>
												
												<tr>
													<td>
														<span id="requestNumExistTxt" style="color:red; line-height:normal" class="fieldName"></span>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td width="122" colspan="2">
																								<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
		<table align="center" cellpadding="0" cellspacing="0" width="100%">
               <tr>
						<td class="fieldName" nowrap >*Date of Entry</td>
												
						<TD>
								<input type="text" name="data_sheet_date" id="data_sheet_date" size="9" value="<?=$data_sheet_date;?>"  <?php /*onchange="xajax_generateGatePass(document.getElementById('entryDate').value);" */?> autocomplete="off" />
						</TD>
						
						<td nowrap="" class="fieldName"> Receiving supervisor : </td>
																<td nowrap="">
																	<?php
																		if(sizeof($purchaseSupervisor) > 0)
																		{
																			echo '<select id="receiving_supervisor" name="receiving_supervisor" required>';
																			echo '<option value=""> -- Select Received supervisor --</option>';
																			foreach($purchaseSupervisor as $lotID)
																			{	
																				$sel = '';
																				if($receiving_supervisor == $lotID[0]) $sel = 'selected="selected"';
																				
																				echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																			}
																			echo '</select>';
																		}
																	?>
																</td>
				</tr>
				<tr>
						
												
						
						<td align="right" class="fieldName">* Data Sheet SL NO : </td>
						<td class="listing-item">
						<input type="text" name="data_sheet_slno" id="data_sheet_slno" value="<?php echo $data_sheet_slno;?>" required/>
						</td>
						
						<td width="180" class="fieldName">* Rm Lot ID :</td>
																<td class="listing-item">
																	<select  id="rm_lot_id" name="rm_lot_id" required>
																		<option value=""> -- Select Lot ID --</option>
																	<?php
																		if(sizeof($rmLotIds) > 0)
																		{
																			
																			foreach($rmLotIds as $lotID)
																			{	
																				$sel = '';
																				if($rm_lot_id == $lotID[0]) $sel = 'selected="selected"';
																				
																				echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																			}
																			
																		}
																	?>
																	</select>
																</td>	
				</tr>
			
				<input type="hidden" name="hidWeightmentId" value="<?=$weightmentId?>">
				</table>
				<?php
			require("template/rbBottom.php");
		?>
													</td>
														
												</tr>
				<tr><td height="10%" colspan="2">&nbsp;</td></tr>
						<tr align="center">
							<td width="122" >
		
							
							
							
							
							
							
		<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
		<table align="center" cellpadding="0" cellspacing="0" width="100%">
               <tr>
						<td class="fieldName" nowrap>Procurement Gate Pass Available</td>
												
						<TD  colspan="3" >
						<input type="checkbox" name="procurementAvailable" id="checkbox1" value="1" onclick="procurementAvlCheck(this.checked);"  />
						
								
						</TD>
						
						
				</tr>
			
			
				</table>
				<?php
			require("template/rbBottom.php");
		?>							</td>
														
												</tr>	
				
				
				
														
											
				
			<tr><td height="10%" colspan="2">&nbsp;</td></tr>
					<tr id="autoUpdate" class="autoUpdate">
													<td width="122" colspan="2">
				<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>

		
		<table ><tr><td colspan="2">										
													
													
		<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
		
		<table align="center" cellpadding="0" cellspacing="0" width="100%">
               <tr>
						<td align="right" class="fieldName">* Supply Area : </td>
						<td class="listing-item">
						<select name="supplyArea" id="supplyArea" >
                                        <option value="">--select--</option>
             									
										<?php 
										foreach($supplyAreaList as $splr)
										{
						$supplyAreaId		=	$splr[0];
						$supplyArea	=	stripSlash($splr[1]);
						$selected="";
						if($selsupplyList==$supplyAreaId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$supplyAreaId?>" <?=$selected?>><?=$supplyArea?></option>
                                                    <? }
										
										
										?>
                                                  </select>
						</td>
						
						<td nowrap="" class="fieldName"> Supply group: </td>
																<td nowrap="">
																	<select name="selRMSupplierGroup" id="selRMSupplierGroup" onchange="xajax_weightmentSupplierName(document.getElementById('selRMSupplierGroup').value,'0','');">
                                        <option value="">--select--</option>
             									
										<?php 
										foreach($supplierGroup as $sp)
										{
						$supplierGroupId		=	$sp[0];
						$supplierGroup	=	stripSlash($sp[1]);
						$selected="";
						if($selRMSupplierGroup==$supplierGroupId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$supplierGroupId?>" <?=$selected?>><?=$supplierGroup?></option>
                                                    <? }
										
										
										?>
                                                  </select>
																</td>
				</tr>
				
			
				</table>
				<?php
			require("template/rbBottom.php");
		?>							</td>
														
												</tr>
												<tr>
										<td width="40%" valign="top" colspan="3" align="center">
											<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblWeighmentMultiple" name="tblWeighmentMultiple">
													<tr bgcolor="#f2f2f2" align="center">
																	<td class="listing-head"> Supplier </td>
																	<td class="listing-head"> Farm Name </td>
																	<td class="listing-head"> Species </td>
																	<td class="listing-head"> Process Code </td>
																	<td class="listing-head"> Count Code </td>
																	
																	<td class="listing-head">  Weight </td>
																	<td class="listing-head"> Soft % </td>
																	<td nowrap="" class="listing-head"> Soft Weight </td>
																	
																	<td></td>
																</tr>
										
										
										</table>
										<tr>
													<td height="10">
														<input type="hidden" name="hidTableRowCounts" id="hidTableRowCounts" value="<?=$rowSize?>">
													</td>
												</tr>
												<tr>
													<td nowrap="" style="padding-left:5px; padding-right:5px;">
															<a title="Click here to add new item." class="link1" onclick="javascript:addNewWeighmentMultipleRow();" id="addRow" href="javascript:void(0);">
																<img border="0" style="border:none;padding-right:4px;vertical-align:middle;" src="images/addIcon.gif">Add New Item
															</a>
													</td>
												</tr>
										
										
									
												
												
												<tr>
													<td height="10"></td>
												</tr>
												
				
												<tr>
													<td height="10"></td>
												</tr>

												<tr>
													<td>
														<table>
															<tr>
															<td nowrap="" class="fieldName" width="47%"> &nbsp;</td>
																<td nowrap="" class="fieldName"> Total weight : </td>
																<td nowrap="">
																	<input type="text" readonly="readonly" value="<?php echo $total_quantity;?>" id="total_quantity" name="total_quantity">																						
																</td>
																<td nowrap="" class="fieldName"> Total soft weight% </td>
																<td nowrap="">
																	<input type="text" readonly="readonly" value="<?php echo $total_soft;?>" id="total_soft" name="total_soft">																						
																</td>
															</tr>
														</table>
													</td>
													<td></td>
												</tr>
												<tr>
													<td height="10"></td>
												</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>		
			</td></tr>
												

			<tr><td height="10%" colspan="2">&nbsp;</td></tr>
				
												
												
												
											
<!--------------------------------------------------------------------------------------------------------------->
															<tr id="autoUpdate2" class="autoUpdate2" style="display:none">
													<td width="122" colspan="2" align="center" >
		
													
	<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
	<table ><tr><td  align="center">
		
													
	<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
		<table align="center" cellpadding="0" cellspacing="0" width="100%">
               <tr>
						<td class="fieldName" nowrap >*Procurement Gate Pass</td>
												
						<TD>
						<select name="procurementGatePass" id="procurementGatePass" onchange="xajax_rmprocurementdet(document.getElementById('procurementGatePass').value,'0',''); xajax_ProcurmentDetail(document.getElementById('procurementGatePass').value,'0',''); xajax_ProcurmentDetailEquipment(document.getElementById('procurementGatePass').value,'0',''); xajax_ProcurmentDetailChemical(document.getElementById('procurementGatePass').value,'0','');">
                                        <option value="">--select--</option>
             									
								<?php 
										foreach($procurementGatePass as $prc)
										{
						$procurementGatePassId		=	$prc[0];
						$procurementGatePassName	=	stripSlash($prc[1]);
						$selected="";

						if($selprocurement==$procurementGatePassId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$procurementGatePassId?>" <?=$selected?>><?=$procurementGatePassName?></option>
                                                    <? }
										
										
										?>
							</select>			
										
										</TD>
						
						<td nowrap="" class="fieldName"> * Purchase Supervisor : </td>
																<td nowrap="">
																	
																	
																	<?php
																		echo '<select id="purchase_supervisor" name="purchase_supervisor" >';
																		echo '<option value=""> -- Select Purchase supervisor --</option>';
																		if(sizeof($purchaseSupervisor) > 0)
																		{
																			foreach($purchaseSupervisor as $lotID)
																			{	
																				$sel = '';
																				if($purchase_supervisor == $lotID[0]) $sel = 'selected="selected"';
																				
																				echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																			}																			
																		}
																		echo '</select>';
																	?>
																	
								
																							</td>
						
						
						
				</tr>
				<tr>
						
												
																<td class="fieldName" nowrap >*Gate Pass Details</td>
												
																<TD>
																		<textarea readonly="readonly" rows="10" cols="54" id="gate_pass_details" name="gate_pass_details">
																	<?php echo $gate_pass_details;?>
																	</textarea>	
																</TD>
																<td nowrap="" class="fieldName">Farmer At Harvest : </td>
																<td nowrap="">
																	<input type="text" value="<?php echo $farmer_at_harvest;?>" id="farmer_at_harvest" name="farmer_at_harvest">																					
																</td>
				</tr>
			
				</table>
				<?php
			require("template/rbBottom.php");
		?>
										</td>
														
												</tr>	

			<tr><td height="10%" colspan="2">&nbsp;</td></tr>
												<tr>
										<td width="40%" valign="top" colspan="3" align="center">
											<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblWeighmentMultipleVal" name="tblWeighmentMultipleVal">
													<tr bgcolor="#f2f2f2" align="center">
																	<td class="listing-head"> Supplier </td>
																	<td class="listing-head"> Farm Name </td>
																	<td class="listing-head"> Species </td>
																	<td class="listing-head"> Process Code </td>
																	
																	<td class="listing-head"> Count Code </td>
																	
																	<td class="listing-head">  Weight </td>
																	<td class="listing-head"> Soft % </td>
																	<td nowrap="" class="listing-head"> Soft Weight </td>
																	<td nowrap="" class="listing-head"> Package Type</td>
																	
																	<td class="listing-head"> Package Nos </td>	
																	<td></td>
																</tr>
										
										
										</table>
										<tr>
													<td height="10">
														<input type="hidden" name="hidTableRowCountsVal" id="hidTableRowCountsVal" value="<?=$rowSize?>">
														<input type="hidden" name="hidTableRowCountsValhid" id="hidTableRowCountsValhid" >
													</td>
												</tr>
												<tr id="hiderow">
													<td nowrap="" style="padding-left:5px; padding-right:5px;" >
															<a title="Click here to add new item." class="link1" onclick="javascript:addNewWeighmentMultipleRowVal();" id="addRow" href="javascript:void(0);">
																<img border="0" style="border:none;padding-right:4px;vertical-align:middle;" src="images/addIcon.gif">Add New Item
															</a>
													</td>
												</tr>
										
										
									
												
												
												<tr>
													<td height="10"></td>
												</tr>
												
				
												

												<tr>
													<td>
														<table>
															<tr>
															<td nowrap="" class="fieldName" width="47%"> &nbsp;</td>
																<td nowrap="" class="fieldName"> Total weight : </td>
																<td nowrap="">
																	<input type="text" readonly="readonly" value="<?php echo $total_quantitypro;?>" id="total_quantitypro" name="total_quantitypro">																						
																</td>
																<td nowrap="" class="fieldName"> Total soft weight% </td>
																<td nowrap="">
																	<input type="text" readonly="readonly" value="<?php echo $total_softpro;?>" id="total_softpro" name="total_softpro">																						
																</td>
															</tr>
														</table>
													</td>
													<td></td>
												</tr>
												<tr>
													<td height="10"></td>
												</tr>
												
																					
										<tr>
										<td width="40%" valign="top" colspan="3" align="center">
											<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddProcurmentOrder" name="tblAddProcurmentOrder">
													<tr bgcolor="#f2f2f2" align="center">
															
															<td class="listing-head" nowrap>Equipment name </td>
															<td class="listing-head">Issued </td>
															<td class="listing-head">Returned</td>
															<td class="listing-head">Difference</td>
															
															
												<td></td>
													</tr>
										
										</table>
										
									<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
									<input type="hidden" name="hidTableRowCounthid" id="hidTableRowCounthid" >
											<tr><TD height="10"></TD></tr>
											<tr id="hiderowequipment"><TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3" align="center" >
												<a href="###" id='addRow' onclick="javascript:addNewRMProcurmentItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
											</TD></tr>
												
												
												<tr>
													<td height="10"></td>
												</tr>
												
												<tr>
													<td width="40%" valign="top" colspan="3" align="center">
			<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddProcurmentChemicalOrder" name="tblAddProcurmentChemicalOrder">
                	<tr bgcolor="#f2f2f2" align="center">
							
                     		<td class="listing-head" nowrap>Chemical name </td>
                     		<td class="listing-head">Issued </td>
							<td class="listing-head">Used</td>
							<td class="listing-head">Returned</td>
							<td class="listing-head">Difference</td>
				<td></td>
                	</tr>
		
		</table>
	<input type="hidden" name="hidChemicalRowCount" id="hidChemicalRowCount" value="<?=$rowSize?>">
	<input type="hidden" name="hidChemicalRowCounthid" id="hidChemicalRowCounthid" >
			<tr><TD height="10"></TD></tr>
			<tr id="hiderowchemical"><TD nowrap style="padding-left:5px; padding-right:5px;" colspan="3" align="center" >
				<a href="###" id='addRow' onclick="javascript:addNewRMProcurmentChemicalItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
			</TD></tr>
						
</td></tr>
					
												
												
												
												
												</table>
												<?php
													require("template/rbBottom.php");
												?>
												
												</td></tr>
												<tr>
													
													<td align="center" colspan="2">
														<input type="button" onclick="return cancel('WeighmentDataSheet.php');" value=" Cancel " class="button" name="cmdCancel">&nbsp;&nbsp;
														<input type="submit" value="<?php echo $buttonValue;?>" class="button" id="cmdAdd1" name="<?php echo $buttonName;?>" > &nbsp;&nbsp;												
													</td>													
												</tr>
												<tr>
													<td>
														<span id="requestNumExistTxt" style="color:red; line-height:normal" class="fieldName"></span>
													</td>
													<td>&nbsp;</td>
												</tr>
												
												
												
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
				<!-- Form fields end   -->	
					</td>
				</tr>
		<?php
			}
		?>
			<tr>
				<td height="10" align="center"></td>
			</tr>
			<tr>
				<td>
				
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Weighment Data Sheet (Farm)  </td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmProcurementSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;</td>
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
												if( sizeof($WeighmentDataSheetRecords) > 0 )
												{
													$i	=	0;
											?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="6" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
	<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">Data Sheet No </td>
	<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">LOT ID</td>
	<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">Process Code</td>
	<td style="padding-left:10px; padding-right:10px;" class="listing-head">Count </td>
	<td style="padding-left:10px; padding-right:10px;" class="listing-head">Qty  </td>
	<td style="padding-left:10px; padding-right:10px;" class="listing-head">Soft% </td>
	<td style="padding-left:10px; padding-right:10px;" class="listing-head">Soft Qty </td>
	<td style="padding-left:10px; padding-right:10px;" class="listing-head">view </td>	
	
	
		<td class="listing-head"></td>
		<? if($confirm==true && ($manageconfirmObj->weightmentDataConfirmEnabled())){?>
                        <td class="listing-head">&nbsp;</td>
			<? }?>
		<!--<td class="listing-head"></td>-->
		<? if($edit==true){?>
		<!--<td class="listing-head"></td>-->
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($WeighmentDataSheetRecords as $sir) {
	
		$i++;
		$weightmentId	=	$sir[0];
		$rm_lot_id		=$sir[17];
		$data_sheet_sl_no		=$sir[2];
		$supplierData	=	$objWeighmentDataSheet->getSupplierData($sir[0]);
		
		
		
		 
		
		
		 $entryDate		= dateFormat($sir[5]);
		 
		  $active=$sir[16];
		  
		//$existingrecords=$sir[10];
		
		
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$weightmentId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$data_sheet_sl_no;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rm_lot_id;?></td>
			
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
		 <?php
			if (sizeof($supplierData)>0) {
					foreach ($supplierData as $cR) {					
					$species = $cR[13];
					
					echo $species;
					echo "<br/>";	
				}
			}
			?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php
			if (sizeof($supplierData)>0) {
					foreach ($supplierData as $cR) {					
					$species = $cR[6];
					
					echo $species;
					echo "<br/>";	
				}
			}
			?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php
			if (sizeof($supplierData)>0) {
					foreach ($supplierData as $cR) {					
					$species = $cR[7];
					echo $species;
					echo "<br/>";	
				}
			}
			?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
		<?php
			if (sizeof($supplierData)>0) {
					foreach ($supplierData as $cR) {					
					$species = $cR[8];
					echo $species;
					echo "<br/>";	
				}
			}
			?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
		<?php
			if (sizeof($supplierData)>0) {
					foreach ($supplierData as $cR) {					
					$species = $cR[9];
					echo $species;
					echo "<br/>";	
				}
			}
			?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<!--<a href="javascript:printWindow('ViewRmProcurmentOrderDetails.php?procurmentId=<?=$procurementId?>&supplierGroup=<?=$supplierGroup?>&supplier=<?=$supplier?>&pondNamee=<?=$pondNamee?>',700,600)" class="link1" title="Click here to view details.">View Details</a>-->
			<a title="Click here to view details." class="link1" href="javascript:printWindow('ViewWeighmentDataSheetDetails.php?id=<?php echo $sir[0];?>',900,750)">View Details</a>
			
		</td>
		<? if ($confirm==true && ($manageconfirmObj->weightmentDataConfirmEnabled())){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?>  <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active=="0"){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$weightmentId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$weightmentId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>
		</td>
		<!--<td>
		<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('ViewRmProcurmentOrderDetails.php?procurmentId=<?=$procurementId?>&supplierGroup=<?=$supplierGroup?>&supplier=<?=$supplier?>&pondNamee=<?=$pondNamee?>',700,600);"><? }?>
		</td>-->
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$weightmentId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='WeighmentDataSheet.php';"></td>
	<? }?>
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
		<td colspan="6" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmProcurementSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;</td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
					<?php /*<table width="75%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
						<tbody>
							<tr>
								<td bgcolor="white">
								<!-- Form fields start -->
									<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
										<tbody>
											<tr>
												<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
												<td nowrap="" background="images/heading_bg.gif" class="pageName">
												&nbsp;Weighment Data Sheet (Farm)  </td>
											</tr>
											<tr>
												<td height="10" colspan="3"></td>
											</tr>
											<tr>	
												<td colspan="3">
													<table align="center" cellspacing="0" cellpadding="0">
														<tbody>
															<tr>
																<td nowrap="">
																<input type="submit" onclick="return confirmDelete(this.form,'delId_',<?php echo sizeof($datasList);?>);" name="cmdDelete" class="button" value=" Delete ">
																&nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New ">&nbsp;</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
											<tr>
												<td height="5" colspan="3"></td>
											</tr>
											<tr>
												<td width="1"></td>
												<td colspan="2">
													<table width="80%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
														<tbody>
															<tr bgcolor="#f2f2f2">
																<td width="20"><input type="checkbox" class="chkBox" onclick="checkAll(this.form,'delId_'); " id="CheckAll" name="CheckAll"></td>
																<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">LOT ID</td>
																<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">Sl NO</td>
																<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">Date</td>
																<td style="padding-left:10px; padding-right:10px;" class="listing-head">Farm Name</td>
																<td style="padding-left:10px; padding-right:10px;" class="listing-head">Gate pass</td>
																<td style="padding-left:10px; padding-right:10px;" class="listing-head">Purchase Supervisor</td>
																<td style="padding-left:10px; padding-right:10px;" class="listing-head">Received at Unit</td>
																<td style="padding-left:10px; padding-right:10px;" class="listing-head">Receiving Supervisor</td>	
																<td class="listing-head"></td>
																<td class="listing-head"></td>					
															</tr>
															<?php
																$i = 0;
																
																if(sizeof($datasList))
																{
																	
																	foreach($datasList as $data)
																	{
																		$i++;
															?>
															<tr bgcolor="WHITE">
																<td width="20"><input type="checkbox" class="chkBox" value="<?php echo $data[0];?>" id="delId_<?=$i;?>" name="delId_<?=$i;?>"></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[1];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[2];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[3];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[5];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[6];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[7];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[8];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data[9];?></td>
																<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
																	<a title="Click here to view details." class="link1" href="javascript:printWindow('ViewWeighmentDataSheetDetails.php?id=<?php echo $data[0];?>',900,750)">View Details</a>
																</td>
																<td width="60" align="center" class="listing-item">
																	<input type="submit" onclick="assignValue(this.form,<?php echo $data[0];?>,'editId');" name="cmdEdit" value="Edit">
																</td>
															</tr>
															<?php
																	}
																}
															?>
															<input type="hidden" value="<?php echo sizeof($datasList);?>" id="hidRowCount" name="hidRowCount">
															<input type="hidden" value="<?php echo $p['editId'];?>" name="editId">
															<input type="hidden" value="0" name="editSelectionChange">
														</tbody>
													</table> 								
												</td>
											</tr>
											<tr>
												<td height="5" colspan="3"></td>
											</tr>
											<tr>	
												<td colspan="3">
													<table align="center" cellspacing="0" cellpadding="0">
														<tbody><tr>
															<td nowrap=""><input type="submit" onclick="return confirmDelete(this.form,'delId_',10);" name="cmdDelete" class="button" value=" Delete ">&nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New ">&nbsp;</td>
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
					</table>*/?>
				<!-- Form fields end   -->			
				</td>
			</tr>	
			<input type="hidden" id="hidStockItemStatus" name="hidStockItemStatus">
			<input type="hidden" value="" name="hidEditId">
			<tr>
				<td height="10"></td>
			</tr>
		</tbody>
	</table>
</form>


<? if ($addMode || $editMode) {?>

<SCRIPT LANGUAGE="JavaScript">
	function addNewItem()
		{	
			addNewRMProcurmentItem();
			addNewWeighmentMultipleRow();
			addNewWeighmentMultipleRowVal();
			addNewRMProcurmentChemicalItem();
			
		}
		
	 function addNewRMProcurmentItem() 
	{ //alert("equipment");
		addNewProcurmentItemRow('tblAddProcurmentOrder', '','', '', '', '', '','addmode');
		//return true;
	}
	
	 function addNewRMProcurmentChemicalItem() 
	{ //alert("chemical");
		addNewRMProcurmentChemicalItemRow('tblAddProcurmentChemicalOrder', '','', '', '', '','','','addmode');
	}
	function addNewWeighmentMultipleRow()
	{
		addNewWeighmentMultiple('tblWeighmentMultiple', '','', '', '', '', '', '', '','', '', 'addmode');
	}
	function addNewWeighmentMultipleRowVal()
	{
		addNewWeighmentMultipleVal('tblWeighmentMultipleVal', '','','','', '', '', '', '', '', '','', 'addmode');
	}
		
		balanceQty();
</SCRIPT>		
<? }?> 
<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">
window.onLoad = addNewItem();
window.load = xajax_generateDatasheet();
//window.onLoad = addNewRMProcurmentChemicalItem();

</SCRIPT>
<? }?>
 <? 
	if ($editMode!="") {
	$weightsum=0;$soft_persum=0;
			if (sizeof($supplierRecsVal)>0) {
		   $n=0;
			 foreach ($supplierRecsVal as $sp) {				
			 $id=$sp[0];						
			 
			
			$supplier_name= $sp[2];
			$pond_name	=	$sp[3];
			$product_species=	$sp[4];
			$process_code=	$sp[5];
			$count_code=	$sp[6];
			$weight=	$sp[7];
			$soft_per=	$sp[8];
			$soft_weight=	$sp[9];
			$packaging_type=$sp[10];
			$package_nos=$sp[11];
			$weightsum+=$sp[7];
			$soft_persum+=$sp[9];
			
?>
<?php if($package_nos!="")
{
?>
<SCRIPT LANGUAGE="JavaScript">
	 	//addNewRMProcurmentSupplierRow('tblAddProcurmentOrderSupplier','<?=$id?>', '<?=$supplierGroupNm?>', '<?=$supplier_name?>', '<?=$supplier_address?>', '<?=$pond_name?>', '<?=$pond_address?>','editmode');
		
		addNewWeighmentMultipleVal('tblWeighmentMultipleVal', '<?=$id?>','<?=$supplier_name?>', '<?=$pond_name?>', '<?=$product_species?>','<?=$process_code?>', '<?=$count_code?>', '<?=$weight?>', '<?=$soft_per?>', '<?=$soft_weight?>','<?=$packaging_type?>', '<?=$package_nos?>','editmode');
		
		//xajax_getDetails('<?=$vehicleNumber?>','<?=$chemicalName?>','<?=$j?>');
		//	xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
		addNewWeighmentMultiple('tblWeighmentMultiple', '','','', '', '', '', '', '', '', '', 'addmode');
	</SCRIPT>
<?php
}
else
{
?>
	<SCRIPT LANGUAGE="JavaScript">
	 	//addNewRMProcurmentSupplierRow('tblAddProcurmentOrderSupplier','<?=$id?>', '<?=$supplierGroupNm?>', '<?=$supplier_name?>', '<?=$supplier_address?>', '<?=$pond_name?>', '<?=$pond_address?>','editmode');
		
		addNewWeighmentMultiple('tblWeighmentMultiple', '<?=$id?>','<?=$supplier_name?>', '<?=$pond_name?>', '<?=$product_species?>','<?=$process_code?>', '<?=$count_code?>', '<?=$weight?>', '<?=$soft_per?>', '<?=$soft_weight?>','<?=$packaging_type?>', 'editmode');
		
		addNewWeighmentMultipleVal('tblWeighmentMultipleVal', '','', '','', '', '', '', '', '', '','', 'addmode');
		addNewRMProcurmentChemicalItemRow('tblAddProcurmentChemicalOrder', '','', '', '', '','','','addmode');
		addNewProcurmentItemRow('tblAddProcurmentOrder', '','', '', '', '', '','addmode');
		//xajax_getDetails('<?=$vehicleNumber?>','<?=$chemicalName?>','<?=$j?>');
		//	xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
		
	</SCRIPT>
<?php
}
?>	
<? 		$n++;
			}
			
		}
		?>
	<?php if($package_nos!="")
	{
	?>
		<SCRIPT LANGUAGE="JavaScript">	
		document.getElementById('total_quantitypro').value=<?php echo $weightsum;?>;
		document.getElementById('total_softpro').value=<?php echo $soft_persum;?>;
		</SCRIPT>	
	<?php
	}
	else	
	{
	?>
		<SCRIPT LANGUAGE="JavaScript">	
		document.getElementById('total_quantity').value=<?php echo $weightsum;?>;
		document.getElementById('total_soft').value=<?php echo $soft_persum;?>;
		</SCRIPT>	
	<?php
	}
	?>	
	
	<?php
	if (sizeof($equipmentRecsVal)>0) {
		$m=0;
			foreach ($equipmentRecsVal as $pr) {				
			$id=$pr[0];						
			$editWeightmentId	=	$pr[1];
			$equipmentName=$pr[2];
			$equipmentIssued=	$pr[3];
			$equipmentReturned	=	$pr[4];
			$difference=	$pr[5];
			
			
?>
	<SCRIPT LANGUAGE="JavaScript">
	 	addNewProcurmentItemRow('tblAddProcurmentOrder', '<?=$id?>','<?=$equipmentName?>', '<?=$equipmentIssued?>', '<?=$equipmentReturned?>', '<?=$difference?>', '<?=$editWeightmentId?>','editmode');
		
		//xajax_getDetails('<?=$vehicleNumber?>','<?=$chemicalName?>','<?=$j?>');
		//	xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
		balanceQty();
	</SCRIPT>	
<? 		$m++;
			}
			
		}
		
		
	
	
	
	
	
	if (sizeof($chemicalRecsVal)>0) {
		$l=0;
			foreach ($chemicalRecsVal as $cr) {				
			$id=$cr[0];						
			$editWeightmentId	=	$cr[1];
			$chemicalName=	$cr[2];
			$chemicalIssued=	$cr[3];
			$chemicalUsed	=	$cr[4];
			$chemicalReturned	=	$cr[5];
			$chemicaldifference	=	$cr[6];
			
			
?>
	<SCRIPT LANGUAGE="JavaScript">
	 	addNewRMProcurmentChemicalItemRow('tblAddProcurmentChemicalOrder','<?=$id?>', '<?=$chemicalName?>', '<?=$chemicalIssued?>', '<?=$chemicalUsed?>', '<?=$chemicalReturned?>','<?=$chemicaldifference?>','<?=$editWeightmentId?>','editmode');
		differenceQty();
	</SCRIPT>	
<? 		$l++;
			}
			
		}
		
	?>
	
	
	
	
	
	
	
	
		
		<?php /*<SCRIPT LANGUAGE="JavaScript">
		  function addNewRMProcurmentItem() 
	{ //alert("equipment");
		addNewProcurmentItemRow('tblAddProcurmentOrder', '','', '', '', '', '','addmode');
		//return true;
	}
	
	 function addNewRMProcurmentChemicalItem() 
	{ //alert("chemical");
		addNewRMProcurmentChemicalItemRow('tblAddProcurmentChemicalOrder', '','', '', '', '','','','addmode');
	}
	function addNewWeighmentMultipleRow()
	{
		addNewWeighmentMultiple('tblWeighmentMultiple', '','', '', '', '', '', '', '', '', '');
	}
	function addNewWeighmentMultipleRowVal()
	{
		addNewWeighmentMultipleVal('tblWeighmentMultipleVal', '','', '', '', '', '', '', '', '', '');
	}
		
		</SCRIPT>*/?>
		<?
		
		
		
		
	}
?>

<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	var fldId  = 0;
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "data_sheet_date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "data_sheet_date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	
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

	
	
	
	function calTotalQty()
	{
		var totalQty = 0;
		var weight = document.getElementsByName('weight[]');
		for(i=0;i<weight.length;i++)
		{	
			if(weight[i].value != '')
			{
				totalQty = parseInt(totalQty) + parseInt(weight[i].value);
			}
		}
		document.getElementById('total_quantity').value = totalQty;
	}
	function calDiff()
	{
		var issued = document.getElementById('issued').value;
		var used = document.getElementById('used').value;
		var returned = document.getElementById('returned').value;
		
		different = parseInt(issued) - parseInt(used);
		if(returned != '')
		{
			different = different - parseInt(returned);
		}		
		document.getElementById('different').value = different;
	}
	
	</SCRIPT>
	<script>
function procurementAvlCheck()
	{
		var procure_aval = document.getElementById('checkbox1');
		// alert(procure_aval.checked);
		if(procure_aval.checked == true)
		{
			jQuery('#autoUpdate').hide();
			jQuery('#autoUpdate2').show();
			
		}
		else
		{
			jQuery('#autoUpdate2').hide();
			jQuery('#autoUpdate').show();
			
		}
		// alert(contentDis);
	}
	jQuery(document).ready(function(){
		var procurementAvailable = '<?php echo $procurementAvailable;?>';
		//alert(procurementAvailable);
		if(procurementAvailable == '1')
		{
			document.getElementById('checkbox1').checked = true;
			jQuery('#autoUpdate').hide();
			jQuery('#autoUpdate2').show();
			
		}
		
		xajax_ProcurmentDetail('<?php echo $selprocurement;?>','','');
		xajax_ProcurmentDetailEquipment('<?php echo $selprocurement;?>','','');
		xajax_ProcurmentDetailChemical('<?php echo $selprocurement;?>' ,'','');
	});

</script>


<script>
function cancel(fileName)
	{
		var con=confirm('Do you want to cancel?');
		if(con==true)
		{
			window.location = fileName;
		}
	}
</script>
<?php 
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>