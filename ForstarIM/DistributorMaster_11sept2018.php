<?php
	require("include/include.php");
	require_once('lib/DistributorMaster_ajax.php');	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$selection = "?pageNo=".$p["pageNo"]."&selStateFilter=".$p["selStateFilter"]."&selCityFilter=".$p["selCityFilter"];
	$creditBalance = "";

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
		
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}
	//$distributorMasterObj->fetchAllDistBankACs(10);
	// Value setting
	if ($p["code"]!="") $code = $p["code"];
	if ($p["distriName"]!="") $distriName = $p["distriName"];
	if ($p["contactPerson"]!="") $contactPerson = $p["contactPerson"];
	if ($p["address"]!="") $address = $p["address"];
	if ($p["baseState"]!='') $baseState = $p["baseState"];
	if ($p["contactNo"]!="") $contactNo = $p["contactNo"];

	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") 	$hidEditId = $p["editId"];
	else 			$hidEditId = $p["hidEditId"];
	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {
		$code 		= "";
		$distriName 	= "";
		$contactPerson 	= "";
		$address 	= "";
		$baseState 	= "";
		$contactNo 	= "";
		$openingBal	= "";
		$creditLimit	= "";
		$getDistStateRecords	= array();
	}	
		
	# Add a Record
	if ($p["cmdAdd"]!="") {
		$rowCount	= $p["hidTableRowCount"];
		$code 		= "D_".autoGenNum();
		$distriName	= addSlash(trim($p["distriName"]));
		$contactPerson	= addSlash(trim($p["contactPerson"]));
		$address	= addSlash(trim($p["address"]));	// Communication Address
		$contactNo 	= addSlash(trim($p["contactNo"])); 
		$openingBal	= trim($p["openingBal"]);
		$creditLimit	= trim($p["creditLimit"]);
		$creditPeriod	= trim($p["creditPeriod"]);

		$tblRowCount	= $p["hidBankACTbleRowCount"];
		$crPeriodFrom	= $p["crPeriodFrom"];
		$distStartDate	= ($p["distStartDate"]!="")?mysqlDateFormat(trim($p["distStartDate"])):"";
				
		if ($distriName!="") {			
			$distributorRecIns = $distributorMasterObj->addDistributor($code, $distriName, $contactPerson, $address, $userId, $contactNo, $openingBal, $creditLimit, $creditPeriod, $crPeriodFrom, $distStartDate);
			if ($distributorRecIns) {
				#Find the Last Inserted Id From m_distributor Table
				$lastId = $databaseConnect->getLastInsertedId();
							
				for ($i=0; $i<$rowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selStateId	= $p["state_".$i];
						$selCity	= $p["city_".$i];
						$billingAddress = addSlash(trim($p["billingAddress_".$i]));
						$deliveryAddress = addSlash(trim($p["deliveryAddress_".$i]));
						$pinCode	= addSlash(trim($p["pinCode_".$i]));
						$telNo		= addSlash(trim($p["telNo_".$i]));
						$faxNo		= addSlash(trim($p["faxNo_".$i]));
						$mobNo		= addSlash(trim($p["mobNo_".$i]));
						$vatNo		= addSlash(trim($p["vatNo_".$i]));
						$tinNo		= addSlash(trim($p["tinNo_".$i]));
						$cstNo		= addSlash(trim($p["cstNo_".$i]));
						$eccNo		= addSlash(trim($p["eccNo_".$i]));
						$selTaxType	= $p["selTaxType_".$i];
						$billingForm	= $p["billingForm_".$i];
						$billingState	= $p["billState_".$i];
						$sameBillingAdr = ($p["sameBillingAdr_".$i]!="")?$p["sameBillingAdr_".$i]:N;
						$selArea	= $p["area_".$i];
						$octroiApplicable = ($p["octroiApplicable_".$i]!="")?$p["octroiApplicable_".$i]:'N';
						$octroiPercent	  = $p["octroiPercent_".$i];
						$octroiExempted   = ($p["octroiExempted_".$i]=="")?'N':$p["octroiExempted_".$i];
						
						$entryTaxApplicable = ($p["entryTaxApplicable_".$i]!="")?$p["entryTaxApplicable_".$i]:'N';
						$entryTaxPercent	  = $p["entryTaxPercent_".$i];
						$entryTaxExempted   = ($p["entryTaxExempted_".$i]=="")?'N':$p["entryTaxExempted_".$i];
		
						$cityContactPerson  = addSlash(trim($p["cityContactPerson_".$i]));
		
						$openingBalance	= trim($p["openingBalance_".$i]);
						$crLimit	= trim($p["creditLimit_".$i]);
						$lwStatus	= $p["lwStatus_".$i];
						$exBillingForm	= $p["exBillingForm_".$i];

						//$exportEnabled	= ($p["hidExportFlag_".$i]=="")?'N':'Y';
						$lastDistStateEntryId = 0;
						$locationStartDate = ($p["locationStartDate_".$i])?mysqlDateFormat(trim($p["locationStartDate_".$i])):"";
						$exportEnabled	= ($p["export_".$i]=="")?'N':'Y';
						$locationId	= $p["locId_".$i];
						
						if ($lastId!="" && $selStateId!="") {
	
							$lastDistStateEntryId = $distributorMasterObj->addDistributorState($lastId, $selStateId, $selCity, $billingAddress, $deliveryAddress, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo,$selTaxType, $billingForm, $billingState, $sameBillingAdr, $selArea, $octroiApplicable, $octroiPercent, $octroiExempted, $entryTaxApplicable, $entryTaxPercent, $entryTaxExempted, $cityContactPerson, $openingBalance, $crLimit, $lwStatus, $eccNo, $exBillingForm, $exportEnabled, $lastDistStateEntryId, $locationStartDate, $locationId);
							if ($lastDistStateEntryId>0) {
								// Just hide
								//if ($exportEnabled=='Y') insertExportRec($i, $lastId, $lastDistStateEntryId, $p, $distributorMasterObj);
							}
						}
					}
				} // For Loop Ends Here
	
				if ($tblRowCount>0) {					
					for ($i=0; $i<$tblRowCount; $i++) {
						$status = $p["bStatus_".$i];						
						if ($status!='N') {
							$bankName	= trim($p["bankName_".$i]);							
							$accountNo	= trim($p["accountNo_".$i]);
							$branchLocation = trim($p["branchLocation_".$i]);
							$defaultAC	= ($p["defaultAC_".$i]!="")?$p["defaultAC_".$i]:'N';
							$selLocIds	= $p["selLoc_".$i]; // Comma Seperate values
	
							if ($bankName!="" && $accountNo!="") {
								# Add Bank AC
								$distBankACRecIns = $distributorMasterObj->addDistBankAC($lastId, $bankName, $accountNo, $branchLocation, $defaultAC, $selLocIds);				
							}
						} // Status Ends here
					} // For loop ends here
				} // Tble row ends here
			} // Ins Cond ends here
			
			if ($distributorRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddDistributor);
				$sessObj->createSession("nextPage",$url_afterAddDistributor.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddDistributor;
			}
			$distributorRecIns		=	false;
		}
	}

	function insertExportRec($rowId, $lastId, $lastDistStateEntryId, $p, $distributorMasterObj)
	{
		$i = $rowId."_1";		

		$selStateId	= $p["state_".$i];
		$selCity	= $p["city_".$i];
		$billingAddress = addSlash(trim($p["billingAddress_".$i]));
		$deliveryAddress = addSlash(trim($p["deliveryAddress_".$i]));
		$pinCode	= addSlash(trim($p["pinCode_".$i]));
		$telNo		= addSlash(trim($p["telNo_".$i]));
		$faxNo		= addSlash(trim($p["faxNo_".$i]));
		$mobNo		= addSlash(trim($p["mobNo_".$i]));
		$vatNo		= addSlash(trim($p["vatNo_".$i]));
		$tinNo		= addSlash(trim($p["tinNo_".$i]));
		$cstNo		= addSlash(trim($p["cstNo_".$i]));
		$eccNo		= addSlash(trim($p["eccNo_".$i]));
		$selTaxType	= $p["selTaxType_".$i];
		$billingForm	= $p["billingForm_".$i];
		$billingState	= $p["billState_".$i];
		$sameBillingAdr = ($p["sameBillingAdr_".$i]!="")?$p["sameBillingAdr_".$i]:N;
		$selArea	= $p["area_".$i];
		$octroiApplicable = ($p["octroiApplicable_".$i]!="")?$p["octroiApplicable_".$i]:'N';
		$octroiPercent	  = $p["octroiPercent_".$i];
		$octroiExempted   = ($p["octroiExempted_".$i]=="")?'N':$p["octroiExempted_".$i];
		
		$entryTaxApplicable = ($p["entryTaxApplicable_".$i]!="")?$p["entryTaxApplicable_".$i]:'N';
		$entryTaxPercent	  = $p["entryTaxPercent_".$i];
		$entryTaxExempted   = ($p["entryTaxExempted_".$i]=="")?'N':$p["entryTaxExempted_".$i];

		$cityContactPerson  = addSlash(trim($p["cityContactPerson_".$i]));

		$openingBalance	= trim($p["openingBalance_".$i]);
		$crLimit	= trim($p["creditLimit_".$i]);
		$lwStatus	= $p["lwStatus_".$i];
		$exBillingForm	= $p["exBillingForm_".$i];

		$exportEnabled	= 'N';
		//echo "$lastId, $selStateId, $selCity, $billingAddress, $deliveryAddress, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo,$selTaxType, $billingForm, $billingState, $sameBillingAdr, $selArea, $octroiApplicable, $octroiPercent, $octroiExempted, $entryTaxApplicable, $entryTaxPercent, $entryTaxExempted, $cityContactPerson, $openingBalance, $crLimit, $lwStatus, $eccNo, $exBillingForm, $exportEnabled, $lastDistStateEntryId";

		if ($lastId!="" && $selStateId!="") {
			$distributorStateRecIns = $distributorMasterObj->addDistributorState($lastId, $selStateId, $selCity, $billingAddress, $deliveryAddress, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo,$selTaxType, $billingForm, $billingState, $sameBillingAdr, $selArea, $octroiApplicable, $octroiPercent, $octroiExempted, $entryTaxApplicable, $entryTaxPercent, $entryTaxExempted, $cityContactPerson, $openingBalance, $crLimit, $lwStatus, $eccNo, $exBillingForm, $exportEnabled, $lastDistStateEntryId);
		}	
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$addMode 		= false;
		$distributorRec		= $distributorMasterObj->find($editId);
		$editDistributorId	= $distributorRec[0];
		$code			= stripSlash($distributorRec[1]);
		$distriName		= stripSlash($distributorRec[2]);
		$contactPerson		= stripSlash($distributorRec[3]);
		$address		= stripSlash($distributorRec[4]);
		$contactNo		= stripSlash($distributorRec[5]);	
		$openingBal		= $distributorRec[6];
		$creditLimit		= $distributorRec[7];
		$creditPeriod		= $distributorRec[8];
		$crPeriodFrom		= $distributorRec[9];
		$selStartDate		= $distributorRec[10];
		$distStartDate	= ($selStartDate!="0000-00-00")?dateFormat($selStartDate):"";
		
		# Distributor State Records
		$getDistStateRecords  = $distributorMasterObj->getDistributorStateRecords($editDistributorId);

		$dateFrom = date("d/m/Y", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
		$dateTill = date("d/m/Y");
		$creditBalance = $distributorMasterObj->getCreditBalance(mysqlDateFormat($dateFrom), mysqlDateFormat($dateTill), $editDistributorId);
		$multipleCityExist = false;
		if ($creditBalance!=0) $multipleCityExist = $distributorMasterObj->chkDuplicateCity($editDistributorId);

		# Billing company bank ac
		$distBankACRecs = $distributorMasterObj->getDistBankACRecs($editDistributorId);
		$bankACArrSize = sizeof($distBankACRecs);
		
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {
		$rowCount	=	$p["hidTableRowCount"];

		$distributorId	= $p["hidDistributorId"];		
		$distriName	= addSlash(trim($p["distriName"]));
		$contactPerson	= addSlash(trim($p["contactPerson"]));
		$address	= addSlash(trim($p["address"]));
		$contactNo 	= addSlash(trim($p["contactNo"])); 
		$openingBal	= trim($p["openingBal"]);
		$creditLimit	= trim($p["creditLimit"]);		
		$creditPeriod	= trim($p["creditPeriod"]);
		$crPeriodFrom	= $p["crPeriodFrom"];
		$distStartDate	= ($p["distStartDate"]!="")?mysqlDateFormat(trim($p["distStartDate"])):"";

		$tblRowCount		= $p["hidBankACTbleRowCount"];		

		if ($distributorId!="" && $distriName!="") {
			$distributorRecUptd = $distributorMasterObj->updateDistributor($distributorId, $distriName, $contactPerson, $address, $contactNo, $openingBal, $creditLimit, $creditPeriod, $crPeriodFrom, $distStartDate);

			for ($i=0; $i<$rowCount; $i++) {
			    $status = $p["status_".$i];
			    $distStateEntryId = $p["hidDistStateEntryId_".$i];	
			    $distCityEntryId  = $p["distCityEntryId_".$i];	
			    if ($status!='N') {
				$selStateId	= $p["state_".$i];
				$selCity	= $p["city_".$i];
				$billingAddress = addSlash(trim($p["billingAddress_".$i]));
				$deliveryAddress = addSlash(trim($p["deliveryAddress_".$i]));
				$pinCode	= addSlash(trim($p["pinCode_".$i]));
				$telNo		= addSlash(trim($p["telNo_".$i]));
				$faxNo		= addSlash(trim($p["faxNo_".$i]));
				$mobNo		= addSlash(trim($p["mobNo_".$i]));
				$vatNo		= addSlash(trim($p["vatNo_".$i]));
				$tinNo		= addSlash(trim($p["tinNo_".$i]));
				$cstNo		= addSlash(trim($p["cstNo_".$i]));
				$eccNo		= addSlash(trim($p["eccNo_".$i]));
				$selTaxType	= $p["selTaxType_".$i];
				$billingForm	= $p["billingForm_".$i];	
				$billingState	= $p["billState_".$i];
				$sameBillingAdr = ($p["sameBillingAdr_".$i]!="")?$p["sameBillingAdr_".$i]:N;
				$selArea	= $p["area_".$i];
				$hidSelTaxType	= $p["hidSelTaxType_".$i];
				$hidBillingForm	= $p["hidBillingForm_".$i];

				$octroiApplicable = ($p["octroiApplicable_".$i]!="")?$p["octroiApplicable_".$i]:'N';
				$octroiPercent	  = $p["octroiPercent_".$i];
				$octroiExempted   = ($p["octroiExempted_".$i]=="")?'N':$p["octroiExempted_".$i];

				$entryTaxApplicable = ($p["entryTaxApplicable_".$i]!="")?$p["entryTaxApplicable_".$i]:'N';
				$entryTaxPercent	  = $p["entryTaxPercent_".$i];
				$entryTaxExempted   = ($p["entryTaxExempted_".$i]=="")?'N':$p["entryTaxExempted_".$i];

				$cityContactPerson  = addSlash(trim($p["cityContactPerson_".$i]));

				$openingBalance	= trim($p["openingBalance_".$i]);
				$crLimit	= trim($p["creditLimit_".$i]);
				$lwStatus	= $p["lwStatus_".$i];
				$exBillingForm	= $p["exBillingForm_".$i];
				
				//$exportEnabled	= ($p["hidExportFlag_".$i]=="")?'N':'Y';
				$lastDistStateEntryId = 0;
				$locationStartDate = ($p["locationStartDate_".$i])?mysqlDateFormat(trim($p["locationStartDate_".$i])):"";
				$exportEnabled	= ($p["export_".$i]=="")?'N':'Y';
				$locationId	= $p["locId_".$i];
								
				if ($distributorId!="" && $selStateId!="" && $distStateEntryId=="") {
					$lastDistStateEntryId = $distributorMasterObj->addDistributorState($distributorId, $selStateId, $selCity, $billingAddress, $deliveryAddress, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo, $selTaxType, $billingForm, $billingState, $sameBillingAdr, $selArea, $octroiApplicable, $octroiPercent, $octroiExempted, $entryTaxApplicable, $entryTaxPercent, $entryTaxExempted, $cityContactPerson, $openingBalance, $crLimit, $lwStatus,$eccNo, $exBillingForm, $exportEnabled, $lastDistStateEntryId, $locationStartDate, $locationId);
				} else if ($distributorId!="" && $selStateId!="" && $distStateEntryId!="") {
					$updateDistStateRec=$distributorMasterObj->updateDistributorState($distStateEntryId, $selStateId, $selCity, $billingAddress, $deliveryAddress, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo, $selTaxType, $billingForm, $billingState, $sameBillingAdr, $selArea, $distCityEntryId, $octroiApplicable, $octroiPercent, $octroiExempted, $entryTaxApplicable, $entryTaxPercent, $entryTaxExempted, $cityContactPerson, $openingBalance, $crLimit, $lwStatus, $eccNo, $exBillingForm, $exportEnabled, $locationStartDate, $locationId);
					
					# Update Sales Order Rec
					if ($selTaxType!=$hidSelTaxType || $billingForm!=$hidBillingForm) {
						$updateDistributorWiseRecInSO = $changesUpdateMasterObj->updateDistributorWiseSORec($distributorId, $selStateId, $selCity);
					}
				}
			    }
			
			  # Delete the state IF Status=N	
			   if ($status=='N' && $distStateEntryId!="") {
				$selState	= $p["state_".$i];
				# Chk Distributor State Exist
				$distStateExist = $distributorMasterObj->distStateRecExist($distributorId, $selState);	
				if (!$distStateExist) {
					# Delete Removed Rec
					$delRemovedRec = $distributorMasterObj->delRemovedDistRec($distStateEntryId, $distCityEntryId);
				}
			   }	
			} // For Loop Ends Here

			# bank AC
			if ($tblRowCount>0) {		
				for ($i=0; $i<$tblRowCount; $i++) {
					$status 	= $p["bStatus_".$i];
					$bankACEntryId	= $p["bankACEntryId_".$i];	
				
					$bankAcInUse   = $distributorMasterObj->chkDistBankAcInUse($bankACEntryId);

					if ($status!='N') {
						$bankName	= trim($p["bankName_".$i]);							
						$accountNo	= trim($p["accountNo_".$i]);
						$branchLocation = trim($p["branchLocation_".$i]);
						$defaultAC	= ($p["defaultAC_".$i]!="")?$p["defaultAC_".$i]:'N';
						$selLocIds	= $p["selLoc_".$i]; // Comma Seperate values

						if ($bankName!="" && $accountNo!="" && $bankACEntryId=="") {
							# Add Bank AC
							$distBankACRecIns = $distributorMasterObj->addDistBankAC($distributorId, $bankName, $accountNo, $branchLocation, $defaultAC, $selLocIds);				
						} else if ($bankName!="" && $accountNo!="" && $bankACEntryId!="") {
							# update Bank AC
							$updateDistBankACRec = $distributorMasterObj->updateDistBankAC($bankACEntryId, $bankName, $accountNo, $branchLocation, $defaultAC, $selLocIds);				
						}
					} // Status Ends here
					
					# Need to check bank ac in use
					if ($status=='N' && $bankACEntryId!="" && !$bankAcInUse) {
						$delBankACRec = $distributorMasterObj->delDistBankACRec($bankACEntryId);
					} 
				} // For loop ends here
			} // Tble row ends here
		}
	
		if ($distributorRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDistributorUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDistributor.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failDistributorUpdate;
		}
		$distributorRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$distExist = false; 
		$bankACInUse = false;

		for ($i=1; $i<=$rowCount; $i++) {
			$distributorId	=	$p["delId_".$i];
						
			if ($distributorId!="") {
				# Chk Distributor Exist
				$distributorExist = $distributorMasterObj->chkDistributorExist($distributorId);		
				if ($distributorId!="" && $distributorExist!="") $distExist = true;

				if (!$distributorExist) {
					# check rec in use
					$bankACInUse = $distributorMasterObj->chkDistributorBankAcInUse($distributorId);

					if (!$bankACInUse) {

						# Delete Bank AC Recs
						$delBankACRecs = $distributorMasterObj->deleteDistBankACRecs($distributorId);
	
						# Delete Distributor Entry Recs
						$deleteDistributorEntryRecs = $distributorMasterObj->delDistributorEntryRecs($distributorId);
						// Need to check the selected Category is link with any other process
						$distributorRecDel = $distributorMasterObj->deleteDistributor($distributorId);
					}
				}
			}
			
		}
		if ($distributorRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDistributor);
			$sessObj->createSession("nextPage",$url_afterDelDistributor.$selection);
		} else {
			$displayMsg = "<br><span style='font-size:9px;'>Please make sure the distributor does not exist in Distributor Rate List/ Margin/ Product Management/ Product Identifier/ Sales Order/ Distributor Account section</span>";
			if ($distExist) $errDel = $msg_failDelDistributor.$displayMsg;
			else if ($bankACInUse) $errDel = $msg_failDelDistributor."Bank Ac is already in use.";
			//$msgFailDelExistDistributor;
			else $errDel	=	$msg_failDelDistributor;
		}
		//echo "$errDel";
		$distributorRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$distributorId	=	$p["confirmId"];
			if ($distributorId!="") {
				// Checking the selected fish is link with any other process
				$distributorRecConfirm = $distributorMasterObj->updatedistributorconfirm($distributorId);
			}

		}
		if ($distributorRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmdistributor);
			$sessObj->createSession("nextPage",$url_afterDelDistributor.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$distributorId = $p["confirmId"];

			if ($distributorId!="") {
				#Check any entries exist
				
					$distributorRecConfirm = $distributorMasterObj->updatedistributorReleaseconfirm($distributorId);
				
			}
		}
		if ($distributorRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmdistributor);
			$sessObj->createSession("nextPage",$url_afterDelDistributor.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["selStateFilter"]!="") $selStateFilterId = $g["selStateFilter"];
	else $selStateFilterId = $p["selStateFilter"];
	
	if ($g["selCityFilter"]!="") $selCityFilterId = $g["selCityFilter"];
	else $selCityFilterId = $p["selCityFilter"];

	if ($p["selStateFilter"]!=$p["hidSelStateFilter"]) {
		$offset	= 0;
		$selCityFilterId = "";
	}

	if ($p["selCityFilter"]!=$p["hidSelCityFilter"]) {
		$offset	= 0;
	}	

	# List all Distributor
	$distributorResultSetObj = $distributorMasterObj->fetchAllPagingRecords($offset, $limit, $selStateFilterId, $selCityFilterId);
	$distributorRecordSize	 = $distributorResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	//$allDistributorResultSetObj = $distributorMasterObj->fetchAllRecords();
	$filterDistributorResultSetObj = $distributorMasterObj->filterDistributorMasterRecords($selStateFilterId, $selCityFilterId);
	$numrows	=  $filterDistributorResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all State
	if ($addMode || $editMode ) {
		//$stateMasterRecs = $stateMasterObj->fetchAllStateRecords();
		$stateMasterRecs = $stateMasterObj->fetchAllRecordsActiveState();
		$billingStateRecords = $distributorMasterObj->getBillingStateRecords();		
	}
	
	# State Filter Records
	//$stateFilterResultSetObj = $stateMasterObj->fetchAllRecords();
	$stateFilterResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();

	# City Filter Records
	//$cityFilterResultSetObj = $cityMasterObj->fetchAllRecords($selStateFilterId);	
	$cityFilterResultSetObj = $cityMasterObj->fetchAllRecordsCityActive($selStateFilterId);
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;

	$billArr  	= array("FF"=>"Form F", "FC"=>"Form C", "FN"=>"None", "VN"=>"Normal", "ZP"=>"Zero Percent");
	$billFormArr 	= array("VAT"=>array("VN"=>"Normal", "ZP"=>"Zero Percent"), "CST"=>array(""=>"-- Select --", "FF"=>"Form F", "FC"=>"Form C", "FN"=>"Normal", "ZP"=>"Zero Percent"));
	$exBillingFormArr = array(""=>"Normal","FCT1"=>"Form CT1");
	//Invoice Date, Despatch Date(Delivery challan date), Delivery date
	$crPeriodStartDateArr = array("INVD"=>"Invoice Date", "DESPD"=>"Despatch Date", "DELID"=>"Delivery Date");

	if ($editMode)	$heading =	$label_editDistributor;
	else 		$heading =	$label_addDistributor;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/DistributorMaster.js"; 
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDistributorMaster" action="DistributorMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="80%" >
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
		<? if($editMode){?>
			<td colspan="2" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistributorMaster(document.frmDistributorMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistributorMaster(document.frmDistributorMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidDistributorId" value="<?=$editDistributorId;?>">
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD colspan="2" style="padding-left:5px; padding-right:5px;" align="center">
			<table cellpadding="0" cellspacing="0">
				<TR>
					<TD valign="top">
						<table>
							<tr>
								<TD>
								<table>
									<tr>
										<td class="fieldName" nowrap >*Name:</td>
										<td>
											<input type="text" name="distriName" size="20" value="<?=$distriName;?>">
										</td>
										<td class="fieldName" nowrap >&nbsp;*Contact Person Name: </td>
										<td>
											<input type="text" name="contactPerson" size="20" value="<?=$contactPerson;?>">
										</td>
										<td class="fieldName" nowrap="true">&nbsp;Contact No:</td>
										<td>
											<input type="text" name="contactNo" size="20" value="<?=$contactNo;?>">
										</td>
										<td class="fieldName" nowrap >&nbsp;Communication Address:</td>
										<td>
											<textarea name="address"><?=$address;?></textarea>
										</td>
									</tr>
								</table>
								</TD>
							</tr>							
							<tr>
								<TD>
								<table>
									<tr>
										<TD class="fieldName" nowrap="true"><span title="Click inside the box to select distributor Start Date">Start Date:</span></TD>
										<td>
											<input type="text" name="distStartDate" id="distStartDate" size="8" value="<?=$distStartDate;?>" />
										</td>
										<TD class="fieldName" nowrap="true">Total Opening Balance:</TD>
										<td>
											<input type="text" name="openingBal" id="openingBal" size="12" value="<?=$openingBal;?>" style="text-align:right; font-weight:bold; border:none;" readonly>
										</td>
										<TD class="fieldName" nowrap>Total Credit Limit:</TD>
										<td>
											<input type="text" name="creditLimit" id="creditLimit" size="12" value="<?=$creditLimit;?>" style="text-align:right; font-weight:bold; border:none;" readonly>
										</td>
										<td colspan="2">
											<table cellpadding="0" cellspacing="0">
												<TR>
												<TD class="fieldName" nowrap>Credit Period</TD>
												<td class="fieldName" nowrap="true">
													<input type="text" name="creditPeriod" id="creditPeriod" size="3" value="<?=$creditPeriod;?>" style="text-align:right;">&nbsp;days
												</td>
												<td class="fieldName" style="padding:0 2px;">from</td>
												<td nowrap="true" style="padding:0 2px;">
													<select name="crPeriodFrom" id="crPeriodFrom" style="width:100px;">
														<option value="">-- Select --</option>
														<?php	
														foreach ($crPeriodStartDateArr as $cpsdKey=>$cpsdValue) {
															$selected = ($crPeriodFrom==$cpsdKey)?"selected":"";
														?>
														<option value="<?=$cpsdKey?>" <?=$selected?>><?=$cpsdValue?></option>
														<? } ?>
													</select>
												</td>						
												</TR>
											</table>
										</td>								
										<?php 
											if ($creditBalance!="") {
										?>								
										<TD class="fieldName">
											<?php							
											$styleColor = ($creditBalance<=0)?"style='color:red'":"";
											?>
											<span <?=$styleColor?>>Credit Balance:</span>
										</TD>
										<td class="listing-item">
											<span <?=$styleColor?>>&nbsp;
												<strong>Rs.&nbsp;<?=number_format($creditBalance,2,'.',',')?></strong>
											</span>
										</td>
										<?php
											}
										?>
									</tr>
								</table>
								</TD>
							</tr>						
						</table>
					</TD>
					<td width="50"></td>
				</TR>
			</table>
		</TD>
	</tr>		
<!--  Dynamic Row adding starts here-->
<tr>
	<td colspan="2" style="padding-left:5px; padding-right:5px;">
		<table cellspacing="1" bgcolor="#999999" cellpadding="2" id="tblAddDistState">
		<TR bgcolor="#f2f2f2" align="center">
			<td class="listing-head" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Billing</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Billing Address</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Delivery Address</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Contacts</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" title="Tax Details">Tax</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Settings</td>
			<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;" title="Opening Balance">OB</td>
			<td class="listing-head" style="padding-left:5px; padding-right:5px;">Credit Limit</td>-->
			<td>&nbsp;</td>
		</TR>	
	<?php	
	if (sizeof($getDistStateRecords)>0) {		
		$j=0;
		foreach ($getDistStateRecords as $dsr) {
			$distStateEntryId	= $dsr[0];
			$selStateId		= $dsr[2];	
			$billingAddress		= stripSlash($dsr[3]);
			$deliveryAddress	= stripSlash($dsr[4]);
			$pinCode		= stripSlash($dsr[5]);
			$telNo			= stripSlash($dsr[6]);
			$faxNo			= stripSlash($dsr[7]);
			$mobNo			= stripSlash($dsr[8]);
			$vatNo			= stripSlash($dsr[9]);
			$tinNo			= stripSlash($dsr[10]);
			$cstNo			= stripSlash($dsr[11]);
			$selTaxType		= $dsr[12];
			$billingForm		= $dsr[13];	
			$billingStateId		= $dsr[14];
			$sameBillingAdrChk	= $dsr[15];
			list($selCityEntryId, $selCityId) = $distributorMasterObj->getSelCityId($distStateEntryId);
			$octroiApplicable	= $dsr[16];
			$octroiPercent		= $dsr[17];
			$octroiExempted		= $dsr[18];
			$entryTaxApplicable	= $dsr[19];
			$entryTaxPercent	= $dsr[20];
			$entryTaxExempted	= $dsr[21];
			$cntactPerson		= stripSlash(stripSlash($dsr[22]));
			$openingBalance		= $dsr[23];
			$crLimit		= $dsr[24];
			$distLWStatus		= $dsr[25];
			$eccNo			= stripSlash($dsr[26]);
			$selExBillingForm 	= $dsr[27];
			$selLocStartDate	= $dsr[28];
			$locationStartDate	= ($selLocStartDate!="0000-00-00")?dateFormat($selLocStartDate):"";
			$exportOnly		= $dsr[29];
			$active=$dsr[30];
			

			$cityRecords = $distributorMasterObj->getSelectedCityList($selStateId, $distStateEntryId);
			$areaRecords = $distributorMasterObj->getSelectedAreaList($selCityId, $selCityEntryId);
			$billFArr = $billFormArr[$selTaxType];
			$locWiseCrBal = "";
			if ($creditBalance!="") {
				$areaIds = $distributorMasterObj->getAreaList($selCityEntryId);
				$locWiseCrBal = $distributorMasterObj->getCreditBalance(mysqlDateFormat($dateFrom), mysqlDateFormat($dateTill), $editDistributorId, $selCityId, $areaIds, $multipleCityExist);
			}
?>	
<tr align="center" class="whiteRow" id="row_<?=$j?>">
	<td nowrap="" align="center" class="listing-item">
	<table>
		<tr>
			<td nowrap="true" class="listing-head">*State</td>
			<td align="left">
			<select name="state_<?=$j?>" id="state_<?=$j?>" style="width: 125px;" onchange="xajax_getCityList(document.getElementById('state_<?=$j?>').value,'<?=$j?>', '<?=$mode?>'); xajax_chkOctroi(document.getElementById('state_<?=$j?>').value, '', '<?=$j?>'); xajax_chkEntryTax(document.getElementById('state_<?=$j?>').value, '<?=$j?>');" >
				<option value="">--Select--</option>
				<?php					
				foreach($stateMasterRecs as $sr) {
					$stateId = $sr[0];
					$stateName	= stripSlash($sr[2]);	
					$selected = ($selStateId==$stateId)?"selected":"";
				?>					
				<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
				<?php
						}
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td nowrap="true" class="listing-head">*City</td>
			<td align="left">
			<select style="width: 125px;" onchange="xajax_getAreaList(document.getElementById('city_<?=$j?>').value, '<?=$j?>', '<?=$mode?>', ''); xajax_chkOctroi(document.getElementById('state_<?=$j?>').value, document.getElementById('city_<?=$j?>').value, '<?=$j?>');" id="city_<?=$j?>" name="city_<?=$j?>">
				<?php if (sizeof($cityRecords)<=0) {?><option value="0">-- Select --</option><?}?>
				<?php					
				foreach($cityRecords as $cr) {
					$cityId 	= $cr[0];
					$cityName	= stripSlash($cr[1]);	
					$selCityId	= $cr[2];
					$selected = ($selCityId==$cityId)?"selected":"";
				?>					
				<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
				<?php
						}
				?>
				
			</select>
			</td>
		</tr>
		<tr>
			<td nowrap="true" class="listing-head">*Area</td>
			<td align="left">
			<select size="5" multiple="true" id="area_<?=$j?>" name="area_<?=$j?>[]">		
				<?php if (sizeof($areaRecords)<=0) {?> <option value="0">-- Select All--</option><? }?>
				<?php					
				foreach($areaRecords as $ar) {
					$areaId 	= $ar[0];
					$areaName	= stripSlash($ar[1]);	
					$selAreaId	= $ar[2];
					$selected = ($selAreaId==$areaId)?"selected":"";
				?>					
				<option value="<?=$areaId?>" <?=$selected?>><?=$areaName?></option>
				<?php
						}
				?>
			</select>
			</td>
		</tr>
		<tr><td height="5"></td></tr>
		<tr>
			<td nowrap="true" title="Location Id" class="row-listing-head">Loc ID</td>
			<td align="left"><input type="text" size="5" readonly="" style="border:none; font-weight:bold; text-align:center;" value="<?=$j+1?>" id="locId_<?=$j?>" name="locId_<?=$j?>"><!--Location Id--></td>
		</tr>
		</tbody>
	</table>
	</td>	
	<td nowrap="" align="center" class="listing-item">
		<table cellpadding='0' celspacing='0'>
			<tr>
				<td class='listing-item' nowrap='true' style='line-height:normal;'>
				<table>		
			<tr>
				<td nowrap="true" class="listing-head">*Billing From</td>
				<td align="left">
					<select style="width: 95px;" onchange="displayTaxType(0);" id="billState_<?=$j?>" name="billState_<?=$j?>">
					<option value="">--Select--</option>
					<?php
						foreach ($billingStateRecords as $bsr) {
							$billStateId	= $bsr[0];
							$billStateName	= $bsr[1];		
							$selected = ($billingStateId==$billStateId)?"selected":"";
					?>
					<option value="<?=$billStateId?>" <?=$selected?>><?=$billStateName?></option>
					<?php
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">*VAT/CST</td>
				<td align="left">
					<select onchange="displayBilling('<?=$j?>');" id="selTaxType_<?=$j?>" name="selTaxType_<?=$j?>">
						<option value="">--Select--</option>
						<option value='VAT' <?=($selTaxType=="VAT")?"selected":"";?>>VAT</option>
						<option value='CST' <?=($selTaxType=="CST")?"selected":"";?>>CST</option>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">*St.Billing</td>
				<td align="left">
					<select id="billingForm_<?=$j?>" name="billingForm_<?=$j?>">
					<?php
					foreach ($billFArr as $bFTypeKey=>$bFTypeVal) {						
						$selected = ($billingForm==$bFTypeKey)?"selected":"";
					?>
					<option value="<?=$bFTypeKey?>" <?=$selected?>><?=$bFTypeVal?></option>
					<?php
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="true" title="Excise billing form" class="row-listing-head">Ex.Billing</td>
				<td align="left">
					<select id="exBillingForm_<?=$j?>" name="exBillingForm_<?=$j?>">
					<?php
					foreach ($exBillingFormArr as $exBillKey=>$exBillValue) {		
						$selected = ($selExBillingForm==$exBillKey)?"selected":"";
					?>
					<option value="<?=$exBillKey?>" <?=$selected?>><?=$exBillValue?></option>
					<?php
					}
					?>
					</select>
				</td>
			</tr>
		</table>	
				</td>
			</tr>
			<tr>
				<td class='listing-item' nowrap='true' style='line-height:normal;'>
				<fieldset style='border-right:0px; border-left:0px; border-bottom:0px; padding-bottom:0px;'>
				<legend>Tax Reg Nos.</legend>
				<table>		
			<tr>
				<td nowrap="true" class="listing-head">VAT No</td>
				<td align="center">
					<input type="text" size="12" value="<?=$vatNo?>" id="vatNo_<?=$j?>" name="vatNo_<?=$j?>" />
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">TIN No</td>
				<td align="center">
					<input type="text" size="12" value="<?=$tinNo?>" id="tinNo_<?=$j?>" name="tinNo_<?=$j?>" />
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">CST No</td>
				<td align="center">
					<input type="text" size="12" value="<?=$cstNo?>" id="cstNo_<?=$j?>" name="cstNo_<?=$j?>" />
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">ECC No</td>
				<td align="center">
					<input type="text" size="12" value="<?=$eccNo?>" id="eccNo_<?=$j?>" name="eccNo_<?=$j?>" />
				</td>
			</tr>
		</table>
				</fieldset>
				</td>
			</tr>
		</table>		
	</td>
	<td nowrap align="center" class="listing-item">
	<table>
		<tbody>
			<tr>
				<td nowrap="true" colspan="2" class="listing-head">
					<textarea cols="15" rows="5" id="billingAddress_<?=$j?>" name="billingAddress_<?=$j?>"><?=$billingAddress?></textarea>
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">Pin Code</td>
				<td align="center">
					<input type="text" size="5" value="<?=$pinCode?>" id="pinCode_<?=$j?>" name="pinCode_<?=$j?>" />
				</td>
			</tr>
		</tbody>
	</table>
	</td>
	<td nowrap="" align="center" class="listing-item">
	<table>
		<tbody>
			<tr>
				<td nowrap="">
					<input type="checkbox" onclick="deliverySame('<?=$j?>');" value="Y" class="chkBox" id="sameBillingAdr_<?=$j?>" name="sameBillingAdr_<?=$j?>" <?=($sameBillingAdrChk=="Y")?"checked":""?> />
				</td>
				<td style="line-height: normal; font-size: 10px;" class="listing-item">Same as Billing Address</td>
			</tr>
			<tr>
				<td nowrap="true" align="center" colspan="2">
					<textarea cols="15" rows="5" id="deliveryAddress_<?=$j?>" name="deliveryAddress_<?=$j?>"><?=$deliveryAddress?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	</td>
	<td nowrap="" align="center" class="listing-item">
	<table>
		<tbody>
			<tr>
				<td nowrap="true" class="listing-head">Person</td>
				<td align="center">
					<input type="text" size="8" value="<?=$cntactPerson?>" id="cityContactPerson_<?=$j?>" name="cityContactPerson_<?=$j?>" />
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">Tel No</td>
				<td align="center">
					<input type="text" size="8" value="<?=$telNo?>" id="telNo_<?=$j?>" name="telNo_<?=$j?>" />
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">Fax No</td>
				<td align="center">
					<input type="text" size="8" value="<?=$faxNo?>" id="faxNo_<?=$j?>" name="faxNo_<?=$j?>" />
				</td>
			</tr>
			<tr>
				<td nowrap="true" class="listing-head">Mob No</td>
				<td align="center">
					<input type="text" size="8" value="<?=$mobNo?>" id="mobNo_<?=$j?>" name="mobNo_<?=$j?>" />
				</td>
			</tr>
		</tbody>
	</table>
	</td>	
	<td nowrap="" align="center" class="listing-item">
	<table cellpadding="0" celspacing="0">
		<tbody>
			<tr>
				<td nowrap="true" style="line-height: normal;" class="listing-item">
				<fieldset style="border-left: 0px none; border-right: 0px none; border-bottom: 0px none; padding-bottom: 0px;"><legend>Octroi</legend>
				<table cellpadding="0" celspacing="0">
					<tbody>
						<tr>
							<td nowrap="true" class="listing-head">Applicable</td>
							<td align="center">
								<input type="checkbox" value="Y" class="chkBox" id="octroiApplicable_<?=$j?>" name="octroiApplicable_<?=$j?>" <?=($octroiApplicable=="Y")?"checked":""?> />
							</td>
						</tr>
						<tr>
							<td nowrap="true" class="listing-head">Exempted</td>
							<td align="center">
								<input type="checkbox" value="Y" class="chkBox" id="octroiExempted_<?=$j?>" name="octroiExempted_<?=$j?>" <?=($octroiExempted=="Y")?"checked":""?> />
							</td>
						</tr>
					</tbody>
				</table>
				</fieldset>
				</td>
			</tr>
			<tr>
				<td nowrap="true" style="line-height: normal;" class="listing-item">
				<fieldset style="border-left: 0px none; border-right: 0px none; border-bottom: 0px none; padding-bottom: 0px;">
					<legend>Entry Tax</legend>
				<table cellpadding="0" celspacing="0">
					<tbody>
						<tr>
							<td nowrap="true" class="listing-head">Applicable</td>
							<td align="center">
								<input type="checkbox" value="Y" class="chkBox" id="entryTaxApplicable_<?=$j?>"
								name="entryTaxApplicable_<?=$j?>" <?=($entryTaxApplicable=="Y")?"checked":""?> />
							</td>
						</tr>
						<tr>
							<td nowrap="true" class="listing-head">Tax(%)</td>
							<td align="center">
								<input type="text" autocomplete="off" value="<?=($entryTaxPercent!=0)?$entryTaxPercent:""?>" size="2" style="text-align: right;" id="entryTaxPercent_<?=$j?>" name="entryTaxPercent_<?=$j?>" /></td>
						</tr>
						<tr>
							<td nowrap="true" class="listing-head">Exempted</td>
							<td align="center">
								<input type="checkbox" value="Y" class="chkBox" id="entryTaxExempted_<?=$j?>" name="entryTaxExempted_<?=$j?>" <?=($entryTaxExempted=="Y")?"checked":""?> />
							</td>
						</tr>
					</tbody>
				</table>
				</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	</td>
	<td nowrap="" align="center" class="listing-item">
		<table>
			<tr>
				<td nowrap="true" title="Starting Date of Distributor location wise" class="row-listing-head">DOE</td>
				<td align="left">
					<input type="text" value="<?=$locationStartDate?>" size="8" id="locationStartDate_<?=$j?>" name="locationStartDate_<?=$j?>"/>
				</td>
			</tr>
			<tr>
				<td class='listing-head' nowrap='true'>OB</td>
				<td align='left'>
					<input type="text" style="text-align: right;" value="<?=($openingBalance!=0)?$openingBalance:""?>" size="8" id="openingBalance_<?=$j?>" name="openingBalance_<?=$j?>" autocomplete="off" onkeyup='calcOBNCrLimit();' />
				</td>
			</tr>
			<tr>
				<td class='listing-head' nowrap='true'>Credit Limit</td>
				<td align='left'>
					<input type="text" style="text-align: right;" value="<?=($crLimit!=0)?$crLimit:""?>" size="8" id="creditLimit_<?=$j?>" name="creditLimit_<?=$j?>" autocomplete="off" onkeyup='calcOBNCrLimit();' />
				</td>
			</tr>
			<?php
			if ($locWiseCrBal!="") {
			?>
			<tr>
				<TD class="listing-head" nowrap="true">
					<?php							
						$locStyleColor = ($locWiseCrBal<=0)?"style='color:red'":"";
					?>
					<span <?=$locStyleColor?>>Cr Bal:</span>
				</TD>
				<td class="listing-item" align="left" nowrap="true">
					<span <?=$locStyleColor?>>
						<strong>Rs.&nbsp;<?=number_format($locWiseCrBal,2,'.',',')?></strong>
					</span>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td class='listing-head' nowrap='true'>Active</td>
				<td align='left'>
					<select name='lwStatus_<?=$j?>' id='lwStatus_<?=$j?>'>
						<option value='Y' <?=($distLWStatus=='Y')?"selected":"";?>>YES</option>
						<option value='N' <?=($distLWStatus=='N')?"selected":"";?>>NO</option>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="true" height="10" class="row-listing-head">
					<input type="hidden" readonly="" value="" id="hidExportFlag_<?=$j?>" name="hidExportFlag_0"/>
					<input type="hidden" readonly="" value="" id="hidDiffExportRemoved_<?=$j?>" name="hidDiffExportRemoved_0"/>
				</td>
			</tr>
			<tr id="exportOnlyFlag_<?=$j?>">
				<td nowrap="true" title="For export only" class="row-listing-head">Export Only</td>
				<td align="left">
					<input type="checkbox" class="chkBox" value="Y" id="export_<?=$j?>" name="export_<?=$j?>" <?=($exportOnly=='Y')?"checked":"";?> />
				</td>
			</tr>
			<!--tr style="<?=$exExmptStyle?>" id="exEmptRow_<?=$j?>">
				<td nowrap="true" title="EXCISE EXEMPTION FOR EXPORT" class="row-listing-head">Ex.Exmpt</td>
				<td align="left">
					<select id="exExmptType_<?=$j?>" name="exExmptType_<?=$j?>">
					<option value="">--Select--</option>
						<option value='VAT' <?=($exExmptType=="VAT")?"selected":"";?>>VAT</option>
						<option value='CST' <?=($exExmptType=="CST")?"selected":"";?>>CST</option>
					</select>
				</td>
			</tr-->
		</table>
		
	</td>	
	<td nowrap="" align="center" class="listing-item">
		<a onclick="setIngItemStatus('<?=$j?>');" href="###">
			<img border="0" style="border: medium none;" src="images/delIcon.gif" title="Click here to remove this item" /></a>
			<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>" />
			<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>" />
			<input type="hidden" value="<?=$distStateEntryId?>" id="hidDistStateEntryId_<?=$j?>" name="hidDistStateEntryId_<?=$j?>" />
			<input type="hidden" value="<?=$selCityEntryId?>" id="distCityEntryId_<?=$j?>" name="distCityEntryId_<?=$j?>" />
			<input type="hidden" value="<?=$billingForm?>" id="hidBillingForm_<?=$j?>" name="hidBillingForm_<?=$j?>" />
			<input type="hidden" value="<?=$selTaxType?>" id="hidSelTaxType_<?=$j?>" name="hidSelTaxType_<?=$j?>" />
	</td>
</tr>
<script language="JavaScript" type="text/javascript">
calenderSetup('<?=$j?>');
</script>
<?php
		$j++;
		}
	}
?>
		</table>
		<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=sizeof($getDistStateRecords)?>">
<?php
	if (sizeof($getDistStateRecords)>0) {
?>
<script language="JavaScript"> 
fieldId = '<?=sizeof($getDistStateRecords)?>';
maxLocId = '<?=sizeof($getDistStateRecords)?>';
</script>
<?php
}
?>
		</td>
	</tr>
	<tr><TD height="10"></TD></tr>
<tr>
	<TD nowrap style="padding-left:5px; padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewDistStateRow();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
<!--  Dynamic Row adding ends here-->

<!-- Bank AC starts here -->
<tr><TD height="5"></TD></tr>
<tr>
	<td colspan="2" style="padding-left:5px; padding-right:5px;">
	<table>
		<TR><TD>
		<table>
		<TR>
			<TD>
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblBankAc" class="newspaperType">
				<tr align="center">
					<th nowrap style="text-align:center;">Bank Name</th>
					<th nowrap style="text-align:center;">Account No.</th>
					<th nowrap style="text-align:center;">Branch Location</th>
					<th nowrap style="text-align:center;">Default</th>
					<th nowrap style="text-align:center;" title="Tag bank ac to Location">Location</th>
					<th>&nbsp;</th>
				</tr>
	<?php
	if ($bankACArrSize>0) {
		$k = 0;
		$totPendingAmt = 0;
		foreach ($distBankACRecs as $bcb) {
			$bankACEntryId 	= $bcb[0];
			$bankName 	= $bcb[1];
			$accountNo	= $bcb[2];
			$branchLocation = $bcb[3];
			$defaultAC	= $bcb[4];
			$taggedLocIds = $bcb[5];
			$locArr = explode(",",$taggedLocIds);
	?>
	<tr align="center" class="whiteRow" id="bRow_<?=$k?>">
		<td align="center" class="listing-item">
			<input type="text" size="24" id="bankName_<?=$k?>" name="bankName_<?=$k?>" value="<?=$bankName?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="text" autocomplete="off" size="24" id="accountNo_<?=$k?>" name="accountNo_<?=$k?>" value="<?=$accountNo?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="text" autocomplete="off" size="24" id="branchLocation_<?=$k?>" name="branchLocation_<?=$k?>" value="<?=$branchLocation?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="checkbox" name="defaultAC_<?=$k?>" id="defaultAC_<?=$k?>" value="Y" class="chkBox" onclick="bacDefaultChk('<?=$k?>');" <?=($defaultAC=='Y')?"checked":"";?>>
		</td>
		<td align="center" class="listing-item" id="locCellId_<?=$k?>">
			<table cellpadding='0' cellspacing='0'>		
			<tr>
			<?
			for ($lc=1; $lc<=$j; $lc++) {
				$lcFieldName = "locChk_".$lc."_".$k;
				$lcChkd = (in_array($lc, $locArr))?"checked":"";
			?>
				<td class='listing-item' style='border:0px;'><input type='checkbox' class='chkBox' value='<?=$lc?>' id='<?=$lcFieldName?>' name='<?=$lcFieldName?>' onclick='mngLocChk("<?=$k?>");' <?=$lcChkd?> />&nbsp;<?=$lc?></td>
			<?
				}
			?>
			</tr>
			</table>
		</td>
		<td align="center" class="listing-item">
			<a onclick="setBankACItemStatus('<?=$k?>');" href="###">
				<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
			</a>
			<input type="hidden" value="" id="bStatus_<?=$k?>" name="bStatus_<?=$k?>"/>
			<input type="hidden" value="N" id="bIsFromDB_<?=$k?>" name="bIsFromDB_<?=$k?>"/>
			<input type="hidden" name="bankACEntryId_<?=$k?>" id="bankACEntryId_<?=$k?>" value="<?=$bankACEntryId?>" />
			<input type='hidden' name='selLoc_<?=$k?>' id='selLoc_<?=$k?>' value='<?=$taggedLocIds?>' readonly />
		</td>
	</tr>
	<?php
				$k++;
			} // Loop ends here
		}
	?>	
	</table>
	<!--  Hidden Fields-->
	<input type='hidden' name="hidBankACTbleRowCount" id="hidBankACTbleRowCount" value="<?=$bankACArrSize?>" readonly="true">
	</TD>
	</TR>
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD>
			<a href="###" id='addRow' onclick="javascript:addNewBankACItem();"  class="link1" title="Click here to add new bank ac."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
			</table>
		</TD></TR>
	</table>
	</td>
</tr>
<!-- Bank AC ends here -->

	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistributorMaster(document.frmDistributorMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistributorMaster(document.frmDistributorMaster);">	
		</td>
		<input type="hidden" name="cmdAddNew" value="1">
		<?}?>
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
		<?php
			}			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" style="background-repeat: repeat-x">&nbsp;Distributor</td>
									<td background="images/heading_bg.gif" class="pageName" align="right" nowrap valign="top" style="background-repeat: repeat-x">
			<table align="right" cellpadding="0" cellspacing="0">	
			<tr>
				<td align="right" nowrap class="listing-item" style="padding-left:1px;padding-right:1px;">State:</td>
				<td align="right" nowrap valign="top" style="padding-left:1px;padding-right:1px;">
				<select name="selStateFilter" onChange="this.form.submit();">
				 <option value="">-- Select All --</option>
					<?
					while ($sr=$stateFilterResultSetObj->getRow()) {
						$stateId = $sr[0];
						$stateCode	= stripSlash($sr[1]);
						$stateName	= stripSlash($sr[2]);	
						$selected = "";
						if ($selStateFilterId==$stateId) $selected = "Selected";
					?>
                                        <option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
					<? }?>
				  </select>&nbsp;
				</td>
				<td align="right" nowrap class="listing-item" style="padding-left:1px;padding-right:2px;">City:</td>
				<td align="right" nowrap valign="top" style="padding-left:2px;padding-right:1px;">
				<select name="selCityFilter" onChange="this.form.submit();">
				 <option value="">-- Select All --</option>
					<?
					while ($cr=$cityFilterResultSetObj->getRow()) {
						$cityId = $cr[0];
						$cityCode	= stripSlash($cr[1]);
						$cityName	= stripSlash($cr[2]);	
						$stateId	= $cr[3];
						$stateName	= $cr[4];	
						$selected = "";
						if ($selCityFilterId==$cityId) $selected = "Selected";
					?>
                                        <option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
					<? }?>
				  </select>&nbsp;
				</td>
				<td width="4">&nbsp;</td>
				</tr>
				  </table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?>
												<input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distributorRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorMaster.php?selStateFilter=<?=$selStateFilterId?>&selCityFilter=<?=$selCityFilterId?>',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($distributorRecordSize) {
			$i = 0;
	?>

	<? if($maxpage>1){ ?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistributorMaster.php?pageNo=$page&selStateFilter=$selStateFilterId&selCityFilter=$selCityFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorMaster.php?pageNo=$page&selStateFilter=$selStateFilterId&selCityFilter=$selCityFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorMaster.php?pageNo=$page&selStateFilter=$selStateFilterId&selCityFilter=$selCityFilterId\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Name</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Contact<br>Person</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Contact<br>No</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Area</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Cities</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">States</td>
		<? if($edit==true){?>
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Active/<br>Inactive</td>-->		
		<td class="listing-head">&nbsp;</td>
		<? }?>
		<? if($confirm==true){?>
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Active/<br>Inactive</td>-->		
		<td class="listing-head">&nbsp;</td>
		<? }?>
	</tr>
	<?php	
	while ($dr=$distributorResultSetObj->getRow()) {
		$i++;
		$distributorId	 = $dr[0];
		$distributorCode = stripSlash($dr[1]);
		$distributorName = stripSlash($dr[2]);	
		$contactPerson	= stripSlash($dr[3]);
		$contactNo	= stripSlash($dr[5]);
		# Get Selected State Records
		$getSelStateRecords = $distributorMasterObj->getSelectedStateRecords($distributorId);
		# Get City Records
		$getSelCityRecords = $distributorMasterObj->getSelCityRecords($distributorId);
		# Get Area Records
		$getSelAreaRecords = $distributorMasterObj->getSelAreaRecords($distributorId);
		$distStatus = $dr[6];
		$activeDis=$dr[6];

		# Dist Margin rate list
		$distMarginRateListId = $distMarginRateListObj->latestRateList($distributorId);
		
		$displayBankAC = "";
		$showBankAC = $distributorMasterObj->displayDistBankACDtls($distributorId);
		
		if ($showBankAC!="") $displayBankAC = "onMouseover=\"ShowTip('$showBankAC');\" onMouseout=\"UnTip();\" ";
	?>
<tr  <?php if ($activeDis==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } else {?> bgcolor="#ffffff" <?php }?> >
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$distributorId;?>" class="chkBox">
	</td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" <?=$displayBankAC?>>
		<?//=$distributorName;?>
		<?=($displayBankAC!="")?"<a href='###' class='link5'>$distributorName</a>":$distributorName;?>
	</td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$contactPerson;?></td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$contactNo;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;line-height:normal;">
		<table cellpadding="0" cellspacing="0">
				<tr>
				<?php
					$numLine = 2;
					if (sizeof($getSelAreaRecords)>0) {
						$nextRec	=	0;					
						$areaName = "";
						$displayHtml = "";
						foreach ($getSelAreaRecords as $cR) {				
							$areaId = $cR[0];
							$areaName = $cR[1];
							$selCityName	= $cR[2];	
							$lwStateId	= $cR[11];
							$lwCityId	= $cR[12];
							$lwExportActive = $cR[13];
							$active=$cR[14];

							# Dist gn recs
							$distMgnRecs = $distributorMasterObj->getDistMarginRecs($distributorId, $distMarginRateListId, $lwStateId, $lwCityId);
							# get OB and CL
							list($lwOB, $lwCreditLimit, $lwDistStatus) = $distributorMasterObj->getDistRec($distributorId, $lwStateId, $lwCityId);

							if ($areaId==0) $areaName = $selCityName."(All)"; 
							$nextRec++;
							$sTaxType		= $cR[3];
							$sBillingForm		= $billArr[$cR[4]];	
							$sOctroiApplicable	= $getEnumFunction[$cR[5]];
							$sOctroiPercent		= $cR[6];
							$sOctroiExempted	= $getEnumFunction[$cR[7]];
							$sEntryTaxApplicable	= $getEnumFunction[$cR[8]];
							$sEntryTaxPercent	= number_format($cR[9],2,'.','');
							$sEntryTaxExempted	= $getEnumFunction[$cR[10]];
							$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";
							$displayHtml .= "<tr bgcolor=#fffbcc align=center class=listing-head><td colspan=8>$selCityName</td></tr>";
							// Main Row
							$displayHtml .= "<tr bgcolor=#fffbcc align=center class=listing-head><td >Octroi</td><td >Entry Tax</td><td>VAT/CST</td><td>Billing</td><td>Margin<br>(%)</td><td>OB</td><td>Credit<br> Limit</td><td>Active</td></tr>";
							$displayHtml .= "<tr bgcolor=#fffbcc>";
							// Octroi Starts here
							$displayHtml .= "<td valign=top>";
							$displayHtml  .= "<table>";			
							$displayHtml .= "<tr><td class=listing-head>Applicable</td><td class=listing-item><strong>$sOctroiApplicable</strong></td></tr>";
							$displayHtml .= "<tr><td class=listing-head>Exempted</td><td class=listing-item><strong>$sOctroiExempted</strong></td></tr>";	
							$displayHtml  .= "</table>";
							$displayHtml .= "</td>";
							// Octroi Ends here
							// Entry Tax Starts here
							$displayHtml .= "<td>";
							$displayHtml  .= "<table>";			
							$displayHtml .= "<tr><td class=listing-head>Applicable</td><td class=listing-item><strong>$sEntryTaxApplicable</strong></td></tr>";
							$displayHtml .= "<tr><td class=listing-head>Percent</td><td class=listing-item><strong>$sEntryTaxPercent&nbsp;%</strong></td></tr>";
							$displayHtml .= "<tr><td class=listing-head>Exempted</td><td class=listing-item><strong>$sEntryTaxExempted</strong></td></tr>";	
							$displayHtml  .= "</table>";
							$displayHtml .= "</td>";
							// Entry Tax Ends here
							$displayHtml .= "<td class=listing-item align=center nowrap><strong>$sTaxType</strong></td>";
							$displayHtml .= "<td class=listing-item align=center nowrap><strong>$sBillingForm</strong></td>";
							$displayHtml .= "<td class=listing-item align=left style=padding-left:5px;padding-right:5px;><strong>";
							$dmgnNumCol = 2;
							if (sizeof($distMgnRecs)>0) {
								$nRec=	0;						
								$dMgn = "";
								foreach ($distMgnRecs as $dmr) {							
									$dMgn = $dmr[0];
									$nRec++;
									if($nRec>1) $displayHtml .= "&nbsp;,&nbsp;"; 
									$displayHtml .= $dMgn;
									if($nRec%$dmgnNumCol == 0) $displayHtml .= "<br/>";
								}
							}
							$displayHtml .= "</strong></td>";
							$displayHtml .= "<td class=listing-item align=center><strong>";
							$displayHtml .= ($lwOB!=0)?$lwOB:"&nbsp;";
							$displayHtml .= "</strong></td>";
							$displayHtml .= "<td class=listing-item align=center><strong>";
							$displayHtml .= ($lwCreditLimit!=0)?$lwCreditLimit:"&nbsp;";
							$displayHtml .= "</strong></td>";
							$displayHtml .= "<td class=listing-item align=center><strong>";
							$displayHtml .= $getEnumFunction[$lwDistStatus];
							$displayHtml .= "</strong></td>";
							$displayHtml .= "</tr>";
							// Main Row Ends Here
							$displayHtml  .= "</table>";
				?>
				<td class="listing-item" style="line-height:normal;" onMouseover="ShowTip('<?=$displayHtml?>');" onMouseout="UnTip();" nowrap>
					<? if($nextRec>1) echo ",";?><a href="###" class="link1" title="click here to view details" style="text-decoration:none;font-size:9pt;"><?=$areaName?></a><? if ($lwExportActive=='Y') {?><span class="listing-head" style="line-height:normal;font-size:8px;font-weight:normal;">(Export)</span> <? }?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php 
						}	
					 }
					}
				?>
				</tr>
			</table>
	</td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
		<?php
			$numCol = 2;
			if (sizeof($getSelCityRecords)>0) {
				$nextRec=	0;						
				$cityName = "";
				foreach ($getSelCityRecords as $cR) {							
					$cityName = $cR[1];
					$nextRec++;
					if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $cityName;
					if($nextRec%$numCol == 0) echo "<br/>";
				}
			}
		?>
	</td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
		<?php
			$numCol = 2;
			if (sizeof($getSelStateRecords)>0) {
				$nextRec=	0;						
				$stateName = "";
				foreach ($getSelStateRecords as $sR) {							
					$stateName = $sR[1];
					$nextRec++;
					if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $stateName;
					if($nextRec%$numCol == 0) echo "<br/>";
				}
			}
		?>
	</td>	
	<? if($edit==true){?>
		<!--<td align="center" id="statusRow_<?=$i?>">
		<a href="###" class="link5">
			<? if($distStatus=='Y'){?>
				<img src="images/y.png" border="0" onMouseover="ShowTip('Click here to Inactive');" onMouseout="UnTip();" onclick="return validateDistStatus('<?=$distributorId?>','<?=$i?>');"/>
			<? } else { ?>
				<img src="images/x.png" border="0" onMouseover="ShowTip('Click here to activate');" onMouseout="UnTip();" onclick="return validateDistStatus('<?=$distributorId?>','<?=$i?>');"/>
			<? }?>
		</a>
		</td>-->
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;">
			<?php if ($activeDis!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$distributorId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='DistributorMaster.php';"><? } ?>
		</td>	

		 <? if ($confirm==true){?><td <?php if ($activeDis==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php
			//echo "The value of active is $active";	
			
			if ($activeDis==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$distributorId;?>,'confirmId');" >
			<?php } else if ($activeDis==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$distributorId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	<? }?>
</tr>
		<?php			
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistributorMaster.php?pageNo=$page&selStateFilter=$selStateFilterId&selCityFilter=$selCityFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorMaster.php?pageNo=$page&selStateFilter=$selStateFilterId&selCityFilter=$selCityFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorMaster.php?pageNo=$page&selStateFilter=$selStateFilterId&selCityFilter=$selCityFilterId\"  class=\"link1\">>></a> ";
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
												<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distributorRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorMaster.php?selStateFilter=<?=$selStateFilterId?>&selCityFilter=<?=$selCityFilterId?>',700,600);"><? }?></td>
											</tr>
										</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
<input type="hidden" name="hidSelStateFilter" value="<?=$selStateFilterId?>">
<input type="hidden" name="hidSelCityFilter" value="<?=$selCityFilterId?>">
<input type="hidden" name="hidEditId" value="<?=$hidEditId?>">
		<tr>
			<td height="10"></td>
		</tr>
	</table>	
	<?php 
		if ($addMode || $editMode ) {
	?>
		<script language="JavaScript">			
			function addNewDistStateRow()
			{				
				addNewDistributorStateRow('tblAddDistState','','','','','','','','','','','','','<?=$mode?>','','','','','','','','','','','','','','');
			}		
		</script>
	<?php 
		}
	?>
<?php 
	if ($addMode) {
?>
<script language="JavaScript">
	window.onLoad = addNewDistStateRow();
</script>
<?php
	 }
?>
<!-- Edit Record NOW Disabled-->
<script language="JavaScript">	
<?php
	if (sizeof($getDistStateRecordss)>0) {		
		$j=0;
		foreach ($getDistStateRecords as $dsr) {
			$distStateEntryId	= $dsr[0];
			$selStateId		= $dsr[2];	
			$billingAddress		= rawurlencode($dsr[3]);
			$deliveryAddress	= rawurlencode($dsr[4]);
			$pinCode		= stripSlash($dsr[5]);
			$telNo			= stripSlash($dsr[6]);
			$faxNo			= stripSlash($dsr[7]);
			$mobNo			= stripSlash($dsr[8]);
			$vatNo			= stripSlash($dsr[9]);
			$tinNo			= stripSlash($dsr[10]);
			$cstNo			= stripSlash($dsr[11]);
			$selTaxType		= $dsr[12];
			$billingForm		= $dsr[13];	
			$billingStateId		= $dsr[14];
			$sameBillingAdrChk	= $dsr[15];
			list($selCityEntryId, $selCityId) = $distributorMasterObj->getSelCityId($distStateEntryId);
			$octroiApplicable	= $dsr[16];
			$octroiPercent		= $dsr[17];
			$octroiExempted		= $dsr[18];
			$entryTaxApplicable	= $dsr[19];
			$entryTaxPercent	= $dsr[20];
			$entryTaxExempted	= $dsr[21];
			$cntactPerson		= rawurlencode(stripSlash($dsr[22]));
			$openingBalance		= $dsr[23];
			$crLimit		= $dsr[24];
?>	
	addNewDistributorStateRow('tblAddDistState','<?=$selStateId?>','<?=$billingAddress?>','<?=$deliveryAddress?>','<?=$pinCode?>','<?=$telNo?>','<?=$faxNo?>','<?=$mobNo?>','<?=$vatNo?>','<?=$tinNo?>','<?=$cstNo?>','<?=$selTaxType?>','<?=$billingForm?>','<?=$mode?>','<?=$distStateEntryId?>','<?=$billingStateId?>','<?=$sameBillingAdrChk?>', '<?=$selCityEntryId?>', '<?=$octroiApplicable?>', '<?=$octroiPercent?>', '<?=$octroiExempted?>', '<?=$entryTaxApplicable?>', '<?=$entryTaxPercent?>', '<?=$entryTaxExempted?>', '<?=$cntactPerson?>', '<?=$openingBalance?>', '<?=$crLimit?>');
	//Get city list	
	xajax_getCityList('<?=$selStateId?>', '<?=$j?>', '<?=$mode?>', '<?=$distStateEntryId?>'); 	
	xajax_getAreaList('<?=$selCityId?>', '<?=$j?>', '<?=$mode?>', '<?=$selCityEntryId?>');
<?php
		$j++;
		}
	}
?>
</script>

	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewBankACItem()
		{
			addNewBankAC('tblBankAc');			
		}
		
	</SCRIPT>
		<?php
			if (!$bankACArrSize && ($addMode || $editMode)) {
		?>
		<SCRIPT LANGUAGE="JavaScript">
			window.load = addNewBankACItem();			
		</SCRIPT>
		<?php
			} else if ($bankACArrSize>0) {
		?>
		<script language="JavaScript">
			fldId = '<?=$bankACArrSize?>';
		</script>
		<?php
			}
		?>

	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "distStartDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "distStartDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>