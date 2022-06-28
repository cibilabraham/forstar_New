<?php
	require("include/include.php");
	require_once("lib/DistMarginStructure_ajax.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$editSelected   = false;
	$sameEntryExist = false;	
	$avgMargin	= "";
	
	$selection = "?pageNo=".$p["pageNo"]."&distributorFilter=".$p["hidDistributorFilterId"]."&distributorRateListFilter=".$p["distributorRateListFilter"];

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
	$reEdit = false;	
	
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

	/*
	//echo "M===>".$calcFinalMargin = ((1- ((1-(2.9126/100))/(1+(3/100))))*100);
	//$getProductRecords = $distMarginStructureObj->getProductRecords(24,3,0);
	*/
	
		
	# Add New Start 
	$urlFrom = "";
	if ($p["cmdAddNew"]!="" || $g["addMode"]!="") {
		$addMode = true;
		$urlFrom = $g["urlFrom"];		
		if ($urlFrom) {			
			$extractUrl = stristr(curPageURL(), "?");	
			//$pmRedirectUrl = "ProductStatus.php?selState=".$g["selState"]."&selDistributor=".$g["selDistributor"];
			$pmRedirectUrl = "ProductStatus.php$extractUrl";
			$sessObj->createSession("pmRedirectUrl",$pmRedirectUrl);
		}
	}
	$redirectUrl = $sessObj->getValue("pmRedirectUrl");
	
	if ($g["selProduct"]!="") 	$selProduct = $g["selProduct"];
	else if ($p["selProduct"]!="")  $selProduct = $p["selProduct"];
	
	if ($g["selState"]!="") 	$pendingStateId = $g["selState"];
	else if ($p["selState"]!="") 	$pendingStateId = $p["selState"];

	# ------------ Coming From Product Management ------------------
	if ($g["urlFrom"]) 		$urlFrom = $g["urlFrom"];
	else if ($p["urlFrom"]) 	$urlFrom = $p["urlFrom"];

	if ($g["selectionType"]) 	$selectionType = $g["selectionType"]; // I/G
	else if ($p["selectionType"]) 	$selectionType = $p["selectionType"];
	
	
	if ($g["selProductIds"]) 	$selProductIds = $g["selProductIds"];
	else if ($p["selProductIds"]) 	$selProductIds = $p["selProductIds"];

	if ($g["selPMCityId"])		$selPMCityId	= $g["selPMCityId"];
	else if ($p["selPMCityId"])	$selPMCityId	= $p["selPMCityId"];

	if ($g["distributorMgnRateList"])	$distributorMgnRateList	= $g["distributorMgnRateList"];
	else if ($p["distributorMgnRateList"])	$distributorMgnRateList	= $p["distributorMgnRateList"];
	$distMStateEntryId = "";
	if ($g["distMStateEntryId"])		$distMStateEntryId	= $g["distMStateEntryId"];
	else if ($p["distMStateEntryId"]) 	$distMStateEntryId	= $p["distMStateEntryId"];


	$disableBtn = "";
	# Product Mgment Var Ends here --------------------------------------------
	
	if ($p["cmdCancel"]!="") {
		$addMode = false;	
		$editMode = false;
		$editSelected = false;
		$p["cmdContinue"] = "";
		$p["editId"] = "";
		$sessObj->updateSession("selRowItem",0);
		$sessObj->updateSession("pmRedirectUrl",'');
		$selProductIds = "";
		$urlFrom = "";
		$selectionType = "";
		$selPMCityId = "";
		$distMStateEntryId = "";
	}

	#Add a Rec
	if ($p["cmdAdd"]!="" || $p["cmdSaveAddNew"]!="") {
		$selDistributor 	= $p["selDistributor"];	
		$selProduct		= $p["selProduct"];		
		$distMarginRateListId	= $p["distMarginRateList"];		

		# Creating a New Rate List
		if ($distMarginRateListId=="") {
			$distributorRec		= $distributorMasterObj->find($selDistributor);
			$distriName = str_replace (" ",'',$distributorRec[2]);
			$selName =substr($distriName, 0,9);	
			$rateListName = $selName."-".date("dMy");
			$startDate    = date("Y-m-d");
			$distMarginRateListRecIns = $distMarginRateListObj->addDistMarginRateList($rateListName, $startDate, $cRList, $userId, $selDistributor, $dCurrentRListId);
			if ($distMarginRateListRecIns) $distMarginRateListId =$distMarginRateListObj->latestRateList($selDistributor);	
		}

		$selPCategory 	= $p["selProductCategory"];
		$selPState 	= $p["selProductState"];
		$selPGroup 	= $p["selProductGroup"];			
		
		$hidDistStateRowCount	= $p["hidDistStateRowCount"];

		$copyFromDistId		= $p["copyFromDistId"];
		$selDistMargin		= $p["selDistMargin"];
		$marginSelection	= $p["marginSelection"];
		$pendingStateId 	= $p["selState"]; 

		# When Adding from Product Management
		$selectionType	= $p["selectionType"];
		$selProductIds	= $p["selProductIds"];
	
		if ($marginSelection=="C") {
			# Current Rate List of Copy Dist
			$copyFromDistRateListId = $distMarginRateListObj->latestRateList($copyFromDistId);	
			
			# Get Copy Records			
			$getCopyFromProductRecords = $distMarginStructureObj->getCopyFromDistRecords($copyFromDistId, $copyFromDistRateListId, $selDistMargin);
			# Get Dist State Records			
			$getDistStateRecords  = $distMarginStructureObj->getDistributorStateRecords($selDistributor, $pendingStateId, $selPMCityId, $distMStateEntryId);
			
			# Get Margin Structure Records
			$marginStructureRecords = $marginStructureObj->fetchAllRecords();
		}
						
		if ($selDistributor!="") {
			# Single Product Add
			if ($selProduct!="") {
				$selDistMarginId 	= "";
				$distMarginLastId 	= "";
				# If Pending State Selected
				if ($pendingStateId) {
					$selDistMarginId = $distMarginStructureObj->getDistMarginId($selDistributor, $selProduct, $distMarginRateListId);
				}
				
				if ($selDistMarginId=="") {
					$sameEntryExist = $distMarginStructureObj->checkEntryExist($selDistributor, $distMarginRateListId, $selProduct, '');
					if (!$sameEntryExist) {
						$distMarginRecIns = $distMarginStructureObj->addDistMarginStructure($selDistributor, $selProduct, $distMarginRateListId, $userId);
						#Find the Last inserted Id From m_distributor_margin
						$distMarginLastId = $databaseConnect->getLastInsertedId();
					}
				} else {
					# When ading Pending State
					$distMarginLastId = $selDistMarginId;
					$distMarginRecIns = true;
				}

				
				
				if ($marginSelection=="C") { /* Copy from*/
					foreach ($getCopyFromProductRecords as $gcpr) {
						$cProduct 	= $gcpr[1];
						$cMgnStructStateEntryId  = $gcpr[2];
						$avgMargin	= $gcpr[4];
						$transportCost	= $gcpr[5];
						$octroi		= $gcpr[6];
						$vat		= $gcpr[7];
						$freight	= $gcpr[8];
						$actualMargin	= $gcpr[10];
						$finalMargin	= $gcpr[11];
						$vatorCstInc	= $gcpr[12];
						$exciseDutyPercent	= $gcpr[13];
						$basicMargin		= $gcpr[14];

						foreach ($getDistStateRecords as $dsr) {
							$distStateEntryId	= $dsr[0];
							$selStateId		= $dsr[2];

							list($selCityEntryId, $selCityId) = $distributorMasterObj->getSelCityId($distStateEntryId);
							# Get areas
							$selAreaIds = $distMarginStructureObj->commaSepAreaList($selDistributor, $selStateId, $selCityId);	

							# Vat $selProduct, $selStateId, $selDistributor
							
							$vat = $distMarginStructureObj->getDistWiseTaxPercent($selProduct, $selStateId, $selDistributor, $distMarginRateListId);

							//$calcFinalMargin = ((1- ((1-(2.9126/100))/(1+($vat/100))))*100);

							if ($distMarginLastId!="" && $selStateId!="") {
								# Insert in m_distributor_margin_state Table	
								$distMarginStateWiseRecIns = $distMarginStructureObj->addDistMarginStateWiseRec($distMarginLastId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin);
								#Find the Last inserted Id From m_distributor_margin_state
								$distMarginStateWiseLastId = $databaseConnect->getLastInsertedId();
							}
							
							foreach ($marginStructureRecords as $msr) {
								$marginStructureId = $msr[0];
								list($dMarginEntryId, $distMarginPercent) = $distMarginStructureObj->getMarginEntryRec($cMgnStructStateEntryId,$marginStructureId);
								
								if ($marginStructureId!="" && $distMarginStateWiseLastId!="") {
									# Insert in m_distributor_margin_entry Table	
									$distMarginEntryRecIns= $distMarginStructureObj->addDistMarginStructureEntry($distMarginStateWiseLastId, $marginStructureId, $distMarginPercent);
								}
							} # Structure Loop Ends Here
						} # State Loop Ends Here
						$distMarginRecIns = true;
					}
				} else { /* Margin Set option*/
				  for ($j=1; $j<=$hidDistStateRowCount; $j++) {
					$selStateId		= $p["selStateId_".$j];
					$avgMargin		= $p["avgMargin_".$j];
					$octroi			= $p["octroi_".$j];
					$vat			= $p["vat_".$j];
					$freight		= $p["freight_".$j];		
					$transportCost		= $p["transportCost_".$j];
					$distStateEntryId	= $p["hidDistStateEntryId_".$j];
					$actualMargin		= $p["actualMargin_".$j];
					$finalMargin		= $p["finalMargin_".$j];
					$selCityId		= $p["selCityId_".$j];
					$selAreaIds		= $p["selAreaIds_".$j]; // Comma seperated vales
					$vatorCstInc		= ($p["vatorCstInc_".$j]=="")?'N':$p["vatorCstInc_".$j];
					$exciseDutyPercent	= $p["exciseDuty_".$j];
					$basicMargin		= $p["basicMargin_".$j];
				 
				
					if ($distMarginLastId!="" && $selStateId!="") {
						$distMarginStateWiseRecIns = $distMarginStructureObj->addDistMarginStateWiseRec($distMarginLastId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin);
						#Find the Last inserted Id From m_distributor_margin_state
						$distMarginStateWiseLastId = $databaseConnect->getLastInsertedId();
					}

					$hidFieldRowCount	= $p["hidFieldRowCount_".$j];
					for ($i=1; $i<=$hidFieldRowCount; $i++) {
						$marginStructureId = $p["marginStructureId_".$i."_".$j];
						$distMarginPercent = $p["distMarginPercent_".$i."_".$j];
						if ($marginStructureId!="" && $distMarginStateWiseLastId!="") {
							$distMarginEntryRecIns= $distMarginStructureObj->addDistMarginStructureEntry($distMarginStateWiseLastId, $marginStructureId, $distMarginPercent);
						}
					}
				}
				} //Magin Set option end
			}  /* Single Product Selection*/

			# multiple Product entry
			if ($selDistributor!="" && $selProduct=="") {
				
				# Get Product based on selection
				if ($selectionType=="") {
					$getProductRecords = $distMarginStructureObj->getProductRecords($selPCategory, $selPState, $selPGroup);
				} else if ($selectionType=="G") {
					$getProductRecords = $distMarginStructureObj->getFilterProducts($selProductIds);	
				}
				
				foreach ($getProductRecords as $gpr) {					
					$selProductId  = $gpr[0];
					$selProductName =  $gpr[1];

					$selDistMarginId = "";
					# If Pending State Selected
					if ($pendingStateId) {
						$selDistMarginId = $distMarginStructureObj->getDistMarginId($selDistributor, $selProductId, $distMarginRateListId);
					}	
					if ($selDistMarginId=="") {
						$sameEntryExist = $distMarginStructureObj->checkEntryExist($selDistributor, $distMarginRateListId, $selProductId, '');  
					}
					if (!$sameEntryExist) {
						$distMarginLastId = "";
						if ($selDistMarginId=="") {
							# Insert Main Rec
							$distMarginRecIns = $distMarginStructureObj->addDistMarginStructure($selDistributor, $selProductId, $distMarginRateListId, $userId);
							#Find the Last inserted Id From m_distributor_margin
							$distMarginLastId = $databaseConnect->getLastInsertedId();
						} else {
							$distMarginLastId = $selDistMarginId;
							$distMarginRecIns = true;
						}

					if ($marginSelection=="C") { /* Copy from*/
						 foreach ($getCopyFromProductRecords as $gcpr) {
							$cProduct 	= $gcpr[1];
							$cMgnStructStateEntryId  = $gcpr[2];
							$avgMargin	= $gcpr[4];
							$transportCost	= $gcpr[5];
							$octroi		= $gcpr[6];
							$vat		= $gcpr[7];
							$freight	= $gcpr[8];
							$actualMargin	= $gcpr[10];
							$finalMargin	= $gcpr[11];
							$vatorCstInc	= $gcpr[12];
							$exciseDutyPercent	= $gcpr[13];
							$basicMargin		= $gcpr[14];

						   foreach ($getDistStateRecords as $dsr) {
							$distStateEntryId	= $dsr[0];
							$selStateId		= $dsr[2];

							list($selCityEntryId, $selCityId) = $distributorMasterObj->getSelCityId($distStateEntryId);
							# Get areas
							$selAreaIds = $distMarginStructureObj->commaSepAreaList($selDistributor, $selStateId, $selCityId);

							# Vat
							$vat = $distMarginStructureObj->getDistWiseTaxPercent($selProductId, $selStateId, $selDistributor, $distMarginRateListId);

							if ($distMarginLastId!="" && $selStateId!="") {
								# Insert in m_distributor_margin_state Table	
								$distMarginStateWiseRecIns = $distMarginStructureObj->addDistMarginStateWiseRec($distMarginLastId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin);
								#Find the Last inserted Id From m_distributor_margin_state
								$distMarginStateWiseLastId = $databaseConnect->getLastInsertedId();
							}
							
							foreach ($marginStructureRecords as $msr) {
								$marginStructureId = $msr[0];
								list($dMarginEntryId, $distMarginPercent) = $distMarginStructureObj->getMarginEntryRec($cMgnStructStateEntryId,$marginStructureId);
		
								if ($marginStructureId!="" && $distMarginStateWiseLastId!="") {
									# Insert in m_distributor_margin_entry Table	
									$distMarginEntryRecIns= $distMarginStructureObj->addDistMarginStructureEntry($distMarginStateWiseLastId, $marginStructureId, $distMarginPercent);
								}
							} # Structure Loop Ends Here
						} # State Loop Ends Here
						$distMarginRecIns = true;
						}
					} else {
						for ($j=1; $j<=$hidDistStateRowCount; $j++) {
							$selStateId		= $p["selStateId_".$j];
							$avgMargin		= $p["avgMargin_".$j];
							$octroi			= $p["octroi_".$j];
							//$vat			= $p["vat_".$j];
							$freight		= $p["freight_".$j];		
							$transportCost		= $p["transportCost_".$j];
							$distStateEntryId	= $p["hidDistStateEntryId_".$j];
							$actualMargin		= $p["actualMargin_".$j];
							$finalMargin		= $p["finalMargin_".$j];
							$selCityId		= $p["selCityId_".$j];
							$selAreaIds		= $p["selAreaIds_".$j]; // Comma seperated vales
							$vatorCstInc		= ($p["vatorCstInc_".$j]=="")?'N':$p["vatorCstInc_".$j];
							$exciseDutyPercent	= $p["exciseDuty_".$j];
							$basicMargin		= $p["basicMargin_".$j];
		
							# Vat
							$vat = $distMarginStructureObj->getDistWiseTaxPercent($selProductId, $selStateId, $selDistributor, $distMarginRateListId);	
							if ($distMarginLastId!="" && $selStateId!="") {
								$distMarginStateWiseRecIns = $distMarginStructureObj->addDistMarginStateWiseRec($distMarginLastId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin);
								#Find the Last inserted Id From m_distributor_margin_state
								$distMarginStateWiseLastId = $databaseConnect->getLastInsertedId();
							}
		
							$hidFieldRowCount	= $p["hidFieldRowCount_".$j];
							for ($i=1; $i<=$hidFieldRowCount; $i++) {
								$marginStructureId = $p["marginStructureId_".$i."_".$j];
								$distMarginPercent = $p["distMarginPercent_".$i."_".$j];
								if ($marginStructureId!="" && $distMarginStateWiseLastId!="") {
									$distMarginEntryRecIns= $distMarginStructureObj->addDistMarginStructureEntry($distMarginStateWiseLastId, $marginStructureId, $distMarginPercent);
								}
							}
						}
					} /* Margin Set Ends Here*/	
					$distMarginRecIns = true;
				   }  /* Same Entry exist*/
				}
			} // mulitple Product entry End
		} // Normal Way Insert Ending

		if ($marginSelection=="C" && $selDistributor) {
			# Update Dist margin recs
			$changesUpdateMasterObj->updateDistributorMgnStructRecs($selDistributor, $distMarginRateListId);

		}

			if ($distMarginRecIns) {				
				$sessObj->createSession("displayMsg",$msg_succAddDistMarginStructure);
				if ($p["cmdSaveAddNew"]!="") {
					$addMode = true;
					$selProduct = "";
					$p["selProduct"] = "";
				} else {
					$distMStateEntryId = "";
					$addMode = false;
					$sessObj->updateSession("pmRedirectUrl",'');
					if ($redirectUrl!="") $sessObj->createSession("nextPage",$redirectUrl);
					else $sessObj->createSession("nextPage",$url_afterAddDistMarginStructure.$selection);
				}
			} else {
				$addMode = true;
				$err	 = $msg_failAddDistMarginStructure;
			}
			$distMarginRecIns = false;
		/*
		} else {
			$err	 = $msg_failAddDistMarginStructure;
		}*/
	}


	#Update a Record
	
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveEdit"]!="" || $p["cmdSwitchMargin"]) {	
		
		$editSelection = $p["hidSelection"];
		$selMargin     = $p["selMargin"];  // DistStateEntryId

		if ($editSelection=='G') {
			/* Format =>$distributorId,$stateId,$cityId,$avgMargin,$distRateListId, $distMarginId*/
			$selRowVal = explode(",",$sessObj->getValue("selRowItem"));		
			$distMarginRateListId	= $selRowVal[4];		
			$distStateId	= $selRowVal[1];
			$locationId	= $selRowVal[2];
			$distAvgMargin	= $selRowVal[3];
			$distributorId	= $selRowVal[0];
			$locExportEnabled 	= $selRowVal[6];
			$distMasterStateEntryId = $selRowVal[7];
			# Get Product Recs
			$getProductRecs = $distMarginStructureObj->getDistMarginProductRecs($distributorId, $distStateId, $locationId, $distAvgMargin, $distMarginRateListId, $locExportEnabled);			
		}

		$distMarginId =	$p["hidDistMarginStructureId"];
		
		$selDistributor		= $p["hidDistributor"];
		$selProduct		= $p["hidProduct"];		
		$distMarginRateListId	= $p["distMarginRateList"];
		$hidDistStateRowCount	= $p["hidDistStateRowCount"];

		if ($distMarginId!="" && $selDistributor!="" && $selProduct!="" && $editSelection=='I' && $selMargin=="") {
			$distMarginRecUptd = $distMarginStructureObj->updateDistMarginStructure($distMarginId, $selDistributor, $selProduct, $distMarginRateListId);
			$distMarginStateEntryId = "";
			for ($j=1; $j<=$hidDistStateRowCount; $j++) {
				$selStateId		= $p["selStateId_".$j];
				$avgMargin		= $p["avgMargin_".$j];
				$octroi			= $p["octroi_".$j];
				//$vat			= $p["vat_".$j];
				$freight		= $p["freight_".$j];		
				$transportCost		= $p["transportCost_".$j];
				$distStateEntryId	= $p["hidDistStateEntryId_".$j];
				
				$distMarginStateEntryId = $p["hidDistMarginStateEntryId_".$j];
				$actualMargin		= $p["actualMargin_".$j];
				$hidAvgMargin		= $p["hidAvgMargin_".$j];

				$finalMargin		= $p["finalMargin_".$j];
				$selCityId		= $p["selCityId_".$j];
				$selAreaIds		= $p["selAreaIds_".$j]; // Comma seperated vales
				$vatorCstInc		= ($p["vatorCstInc_".$j]=="")?'N':$p["vatorCstInc_".$j];
				$exciseDutyPercent	= $p["exciseDuty_".$j];
				$basicMargin		= $p["basicMargin_".$j];
				
				# Vat
				$vat = $distMarginStructureObj->getDistWiseTaxPercent($selProduct, $selStateId, $selDistributor, $distMarginRateListId);

				if ($distMarginId!="" && $selStateId!="" && $distMarginStateEntryId=="") {
					$distMarginStateWiseRecIns = $distMarginStructureObj->addDistMarginStateWiseRec($distMarginId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin);
					#Find the Last inserted Id From m_distributor_margin_state
					$distMarginStateWiseLastId = $databaseConnect->getLastInsertedId();
				} else if ($distMarginId!="" && $selStateId!="" && $distMarginStateEntryId!="") {
					$updateDistMarginStateWiseRec = $distMarginStructureObj->updateDistMarginStateWiseRec($distMarginStateEntryId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin);
					# Entry Id
					$distMarginStateWiseLastId = $distMarginStateEntryId;
					# Update Sales Order  Not Confirmed Rec
					if ($avgMargin!=$hidAvgMargin) {
						# Dist Mgn Entry Id, Distributor , State, Product
						$updateChanges = $changesUpdateMasterObj->updateDistMgnInSORec($distMarginRateListId, $selDistributor, $selStateId, $selProduct, $avgMargin);
					}
				}

			$hidFieldRowCount	= $p["hidFieldRowCount_".$j];
			$distMarginEntryId = "";
			for ($i=1; $i<=$hidFieldRowCount; $i++) {
				$marginStructureId = $p["marginStructureId_".$i."_".$j];
				$distMarginPercent = $p["distMarginPercent_".$i."_".$j];
				$distMarginEntryId = $p["distMarginEntryId_".$i."_".$j];

				if ($marginStructureId!="" && $distMarginStateWiseLastId!="" && $distMarginEntryId=="") {
					$distMarginEntryRecIns= $distMarginStructureObj->addDistMarginStructureEntry($distMarginStateWiseLastId, $marginStructureId, $distMarginPercent);
				} else if ($marginStructureId!="" && $distMarginStateWiseLastId!="" && $distMarginEntryId!="") {
					$updateDistMagnStructureRec = $distMarginStructureObj->updateDistMarginStructureEntry($distMarginEntryId, $distMarginPercent);
				}
			}
			} # State Row Count Ends Here
		}

	/* 
	# Group Adding
	*/
	if ($selDistributor!="" && $editSelection=='G') {
			
		foreach ($getProductRecs as $gpr) {
			$distMarginStateEntryId = $gpr[4];
			$sProductId		= $gpr[0];
		
			for ($j=1; $j<=$hidDistStateRowCount; $j++) {					
				$avgMargin		= $p["avgMargin_".$j];
				$octroi			= $p["octroi_".$j];
				$vat			= $p["vat_".$j];
				$freight		= $p["freight_".$j];		
				$transportCost		= $p["transportCost_".$j];		
				$actualMargin		= $p["actualMargin_".$j];
				$hidAvgMargin		= $p["hidAvgMargin_".$j];
				$finalMargin		= $p["finalMargin_".$j];
				$selCityId		= $p["selCityId_".$j];
				$selAreaIds		= $p["selAreaIds_".$j]; // Comma seperated vales
				$vatorCstInc		= ($p["vatorCstInc_".$j]=="")?'N':$p["vatorCstInc_".$j];	
				$exciseDutyPercent	= $p["exciseDuty_".$j];
				$basicMargin		= $p["basicMargin_".$j];				

				// Check just below section to update Margin

				 if ($distMarginStateEntryId!="") {					
					$updateDistMarginStateWiseRec = $distMarginStructureObj->updateDistMarginStateWiseGroupRec($distMarginStateEntryId, $avgMargin, $octroi, $vat, $freight, $transportCost, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin);	
					# Update Sales Order  Not Confirmed Rec	
					if ($avgMargin!=$hidAvgMargin) {
						# Dist Mgn Entry Id, Distributor , State, Product
						$updateChanges = $changesUpdateMasterObj->updateDistMgnInSORec($distMarginRateListId, $distributorId, $distStateId, $sProductId, $avgMargin);
					}
				}
	
				$hidFieldRowCount	= $p["hidFieldRowCount_".$j];
				$distMarginEntryId = "";
				for ($i=1; $i<=$hidFieldRowCount; $i++) {
					$marginStructureId = $p["marginStructureId_".$i."_".$j];
					$distMarginPercent = $p["distMarginPercent_".$i."_".$j];
					list($distMarginEntryId, $null) = $distMarginStructureObj->getMarginEntryRec($distMarginStateEntryId,$marginStructureId);			 	
					 if ($marginStructureId!="" && $distMarginEntryId!="" && $updateDistMarginStateWiseRec) {				
						$updateDistMagnStructureRec = $distMarginStructureObj->updateDistMarginStructureEntry($distMarginEntryId, $distMarginPercent);
					}
				}
				$distMarginRecUptd = true;
			} // Loop
		} // Product Loop Ends Here

		}
		
		/*
			SWITCH MARGIN	
		*/
		if ($selMargin!="") {
			/* Format =>$distributorId,$stateId,$cityId,$avgMargin,$distRateListId, $distMarginId*/
			$selRowVal = explode(",",$sessObj->getValue("selRowItem"));		
			$distMarginRateListId	= $selRowVal[4];		
			$distStateId	= $selRowVal[1];
			$locationId	= $selRowVal[2];
			$distAvgMargin	= $selRowVal[3];
			$distributorId	= $selRowVal[0];
			$locExportEnabled 	= $selRowVal[6];
			$distMasterStateEntryId = $selRowVal[7];
			# Get All Margin Structures
			$marginStructureRecords = $marginStructureObj->fetchAllRecords();			
			
			# Get the Edited State Entry Id
			$editStateEntryId = $distMarginStructureObj->getDistStateEntryId($distributorId, $selProduct, $distMarginRateListId, $distStateId, $distAvgMargin, $locExportEnabled);	

			# Find the Records	
			list($avgMargin, $octroi, $vat, $freight, $transportCost, $actualMargin, $finalMargin, $vatorCstInc, $exciseDutyPercent, $basicMargin) = $distMarginStructureObj->getDistMarginStateWiseRec($selMargin);
			if ($editStateEntryId) {
				$updateDistMarginStateWiseRec = $distMarginStructureObj->updateDistSwitchedMgnStateWiseRec($editStateEntryId, $avgMargin, $octroi, $vat, $freight, $transportCost, $actualMargin, $finalMargin, $vatorCstInc, $exciseDutyPercent, $basicMargin);

				# Update Sales Order  Not Confirmed Rec	
				# Dist Mgn Entry Id, Distributor , State, Product
					$updateChanges = $changesUpdateMasterObj->updateDistMgnInSORec($distMarginRateListId, $distributorId, $distStateId, $selProduct, $avgMargin);
			}

			foreach ($marginStructureRecords as $msr) {
				$marginStructureId = $msr[0];
				# Get Selected StateEntryId Margin Percent
				list($distMarginEntryId, $distMarginPercent) = $distMarginStructureObj->getMarginEntryRec($selMargin,$marginStructureId);
				# Get edite StateEntryId Margin Percent
				list($distCurrentMarginEntryId, $distCurrentMarginPercent) = $distMarginStructureObj->getMarginEntryRec($editStateEntryId,$marginStructureId);		
				# Change the margin percent to current entry		
				$updateDistMagnStructureRec = $distMarginStructureObj->updateDistMarginStructureEntry($distCurrentMarginEntryId, $distMarginPercent);				
			}
			$distMarginRecUptd = true;
		}   // Switch Margin
	
		if ($distMarginRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDistMarginStructureUpdate);
			if ($p["cmdSaveEdit"]!="") {
				$editMode = true;
				$selProduct = "";
				$p["selProduct"] = "";
			} else {
				$sessObj->createSession("nextPage",$url_afterUpdateDistMarginStructure.$selection);
			}
		} else {
			$editMode	=	true;
			$err		=	$msg_failDistMarginStructureUpdate;
		}
		$distMarginRecUptd	=	false;
	}


	# Edit  a Record
	//$gDistMarginId = $g["distMarginId"];
	/*
	if (($p["editId"]!="" || $gDistMarginId) && $p["cmdCancel"]=="") {
		if ($gDistMarginId!="") $editId	= $gDistMarginId;
		else $editId	=	$p["editId"];
		$editMode	=	true;
		$distMarginRec	=	$distMarginStructureObj->find($editId);
		$editDistMarginStructureId =	$distMarginRec[0];
		//$selDistributor 	= $distMarginRec[1];	
		$selProduct		= $distMarginRec[2];		
		$distMarginRateListId	= $distMarginRec[3];
		$disableField		= "disabled";
	}
	*/
	/*
		Edit Selection
	*/
	if ($p["editSelItem"]!="") {
		$singleProduct  = false;
		$editSelItem	=	$p["editSelItem"];
		$sessObj->createSession("selRowItem",$editSelItem);
		/* Format =>$distributorId,$stateId,$cityId,$avgMargin,$distRateListId, $distMarginId*/
		$selRowVal = explode(",",$sessObj->getValue("selRowItem"));
		$selDistributor		= $selRowVal[0];
		$distMarginRateListId	= $selRowVal[4];
		$editDistMarginStructureId =	$selRowVal[5];
		$distStateId	= $selRowVal[1];
		$locationId	= $selRowVal[2];
		$distAvgMargin	= $selRowVal[3];
		$locExportEnabled 	= $selRowVal[6];
		$distMasterStateEntryId = $selRowVal[7]; 

		# Product Records	
		$productRecords = $distMarginStructureObj->getDistMarginProductRecs($selDistributor, $distStateId, $locationId, $distAvgMargin, $distMarginRateListId, $locExportEnabled);
		if (sizeof($productRecords)>0 && sizeof($productRecords)<=1) {
			$singleProduct = true;
		}		
		$editSelected = true;	
	}
		
	if ($p["cmdBack"]!="") {
		$editSelItem	= "";
		$editSelected = false;	
	}
	
	if ($p["cmdContinue"] || $p["editId"] || $singleProduct) {
		$editSelItem	= "";
		$editSelected = false;
		$editMode = true;
		
		if ($p["editSelection"]!="") $editSelection = $p["editSelection"];
		else $editSelection = $p["hidSelection"];
		if 	($editSelection=='I') $individualSelected = true;
		else if ($editSelection=='G') $groupSelected = true;
		else if ($singleProduct) { /* If Single Product selection*/
			$editSelection = 'I';
			$individualSelected = true;	
		}
		/* Format =>$distributorId,$stateId,$cityId,$avgMargin,$distRateListId, $distMarginId*/
		$selRowVal = explode(",",$sessObj->getValue("selRowItem"));		
		$distMarginRateListId	= $selRowVal[4];
		$editDistMarginStructureId =	$selRowVal[5];
		$distStateId	= $selRowVal[1];
		$locationId	= $selRowVal[2];
		$distAvgMargin	= $selRowVal[3];
		$editId = $editDistMarginStructureId;
		if ($singleProduct) {
			$distMarginRec	= $distMarginStructureObj->find($editDistMarginStructureId);		
			$selProduct	= $distMarginRec[2];
		}
		$locExportEnabled 	= $selRowVal[6];
		$distMasterStateEntryId = $selRowVal[7];

		$disableField		= "disabled";
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$selRow	=	$p["delId_".$i];
			if ($selRow!="") {				
				/* Format =>$distributorId,$stateId,$cityId,$avgMargin,$distRateListId, $distMarginId*/
				$selRowVal = explode(",",$selRow);
				$distMarginRateListId	= $selRowVal[4];		
				$distStateId	= $selRowVal[1];
				$locationId	= $selRowVal[2];
				$distAvgMargin	= $selRowVal[3];
				$distributorId	= $selRowVal[0];
				$locExportEnabled 	= $selRowVal[6];
				$distMasterStateEntryId = $selRowVal[7];	

				$getProductRecs = $distMarginStructureObj->getDistMarginProductRecs($distributorId, $distStateId, $locationId, $distAvgMargin, $distMarginRateListId, $locExportEnabled);
				foreach ($getProductRecs as $gdr) {
					$distMarginStateEntryId = $gdr[4];
					$distMarginId		= $gdr[3];
					# Checking Margin Used
					$marginUseChk = $distMarginStructureObj->chkDistMgnUsed($distMarginStateEntryId);
					
					if ($distMarginStateEntryId && !$marginUseChk) {
						$delDistMarginEntry = $distMarginStructureObj->delDistMarginEntryRec($distMarginStateEntryId);
						//$stateRec
						$delDistMarginStateEntry = $distMarginStructureObj->delDistMarginStateEntryRec($distMarginStateEntryId);
						$chkDistStateRecExist = $distMarginStructureObj->chkDistStateRecSize($distMarginId);
						if (!$chkDistStateRecExist) {
							$distMarginRecDel = $distMarginStructureObj->deleteDistMarginStructure($distMarginId);
						}
					}					
				}

			}
		}
		/*
		for ($i=1; $i<=$rowCount; $i++) {
			$pRowCount = $p["pRowCount_".$i];
			//$distMarginId	=	$p["delId_".$i];
			// Need to check 12-12-08
		   for ($j=1; $j<=$pRowCount; $j++) {	
			//$distMarginId	=	$p["delId_".$j."_".$i];
			if ($distMarginId!="") {
				#Deleting from All Dist Margin Struct Entry table
				$distMarginEntryRecDel = $distMarginStructureObj->delDistMagnStructEntryRecs($distMarginId);
				#del main table
				// Need to check the selected id is link with any other process
				$distMarginRecDel = $distMarginStructureObj->deleteDistMarginStructure($distMarginId);
			}
		    }
		  		
		}
*/
		if ($distMarginRecDel || $delDistMarginStateEntry || $delDistMarginEntry) {
			$sessObj->createSession("displayMsg",$msg_succDelDistMarginStructure);
			$sessObj->createSession("nextPage",$url_afterDelDistMarginStructure.$selection);
		} else {
			$errDel	=	$msg_failDelDistMarginStructure;
		}
		$distMarginRecDel	=	false;
	}

	/* Delete Single Product */
	if ($p["cmdDelMargin"]!="") {
		$distStateWiseEntryId = $p["distStateWiseEntryId"];
		$distMarginId = $p["hidDistMarginStructureId"];	
		# Checking Margin Used
		$marginUseChk = $distMarginStructureObj->chkDistMgnUsed($distStateWiseEntryId);
		
		if ($distStateWiseEntryId && !$marginUseChk) {
			$delDistMarginEntry = $distMarginStructureObj->delDistMarginEntryRec($distStateWiseEntryId);
			//StateRec
			$delDistMarginStateEntryRec = $distMarginStructureObj->delDistMarginStateEntryRec($distStateWiseEntryId);
			
			$chkDistStateRecExist = $distMarginStructureObj->chkDistStateRecSize($distMarginId);
			if (!$chkDistStateRecExist) {
				$distMarginRecDel = $distMarginStructureObj->deleteDistMarginStructure($distMarginId);
			}
		}
		
		if ($delDistMarginStateEntryRec) {
			$sessObj->createSession("displayMsg",$msg_succDelDistMarginStructure);
			$p["selProduct"]="";
			//$sessObj->createSession("nextPage",$url_afterDelDistMarginStructure.$selection);
		} else {
			$errDel	=	$msg_failDelDistMarginStructure;
		}
		$distMarginRecDel	=	false;
	
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo-1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	//$eSelection = $p["hidSelection"];
	if ($g["distributorFilter"]!="") $distributorFilterId = $g["distributorFilter"];		
	else if ($p["distributorFilter"]) $distributorFilterId = $p["distributorFilter"];
	else if ($p["hidDistributorFilterId"]!="") $distributorFilterId = $p["hidDistributorFilterId"];	 

	if ($g["distributorRateListFilter"]!="") $distributorRateListFilterId = $g["distributorRateListFilter"];
	else if ($p["distributorRateListFilter"]) $distributorRateListFilterId = $p["distributorRateListFilter"];	
	else if ($p["hidDistributorRateListFilterId"]!="") $distributorRateListFilterId = $p["hidDistributorRateListFilterId"];

	# Resettting offset values
	if ($p["hidDistributorFilterId"]!=$p["distributorFilter"] && $p["hidDistributorFilterId"]!="") {	
		$offset = 0;
		$pageNo = 1;		
		//$distributorRateListFilterId = "";
	} else if ($p["hidDistributorRateListFilterId"]!=$p["distributorRateListFilter"] && $p["hidDistributorRateListFilterId"]!="") {		
		$offset = 0;
		$pageNo = 1;	
	}

	# List all DistMarginStructure
	$distWiseProuctWiseMarginRecords = $distMarginStructureObj->getDistMarginPagingRecords($offset, $limit, $distributorFilterId, $distributorRateListFilterId);
	$distMarginRecordSize = sizeof($distWiseProuctWiseMarginRecords);	

	## -------------- Pagination Settings II -------------------
	$allDistMarginStructureResultSetObj = $distMarginStructureObj->fetchAllRecords($distributorFilterId, $distributorRateListFilterId);
	$numrows	=  $allDistMarginStructureResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	
	if ($addMode || $editMode) {	
		# List all Distributor
		//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();

		$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	}

	$productionMatrixMaster = "PMM";	
	$pmmRateList = $manageRateListObj->latestRateList($productionMatrixMaster);
	#Get all Production Matrix master Value
	if ($addMode || $editMode) {
		//list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($pmmRateList);

		# List all Combo matrix
		/*$productMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();*/
		
	}

	if ((($p["editSelectionChange"]=='1') || $p["selDistributor"]=="") && $editMode) {
		//$selDistributor = $distMarginRec[1];	
		$selDistributor = $selRowVal[0];		
	} else {		
		if ($g["selDistributor"]!="") $selDistributor = $g["selDistributor"];
		else $selDistributor = $p["selDistributor"];	

		if ($distributorMgnRateList) $distMarginRateListId = $distributorMgnRateList;
		else $distMarginRateListId = $distMarginRateListObj->latestRateList($selDistributor);
	}
	

	if ($editMode && $individualSelected) {
		if ($singleProduct) $selProduct = $distMarginRec[2]; // From Edit Section
		else $selProduct = $p["selProduct"];		
		if ($selDistributor!="" && $selProduct!="" && $distMarginRateListId!="") {
			$editDistMarginStructureId = $distMarginStructureObj->getDistMarginEntryId($selDistributor,$selProduct,$distMarginRateListId);

			# Dist State Wise Entry Id (For Delete section)
			$distStateWiseEntryId = $distMarginStructureObj->getDistMarginStateEntryId($selDistributor, $distStateId, $locationId, $distAvgMargin, $selProduct, $distMarginRateListId, $locExportEnabled);
		}
	}
	
	# List all Products
		$displaySelProduct = "";
		$gpCategoryId = $gpStateId = $gpGroupId = $totalGroupCount = "";

		if ($addMode) {
			//$productRecords = $manageProductObj->fetchAllRecords();	
			if ($selectionType=="") $productRecords = $manageProductObj->getAllProductRecs();
			else {
				$productRecords = $distMarginStructureObj->filterAllProductRecs($selProductIds);
				if ($selectionType=='G') $displaySelProduct = $distMarginStructureObj->implodeFilterProduct();
			}		
				
		} else if ($distMarginRateListId!="") {
			$productRecords = $distMarginStructureObj->getDistMarginProductRecs($selDistributor, $distStateId, $locationId, $distAvgMargin, $distMarginRateListId, $locExportEnabled);
			if ($groupSelected) {
				list($gpCategoryId, $gpStateId, $gpGroupId, $totalGroupCount) = $distMarginStructureObj->getDMGroupedProducts($selDistributor, $distStateId, $locationId, $distAvgMargin, $distMarginRateListId, $locExportEnabled);
				if ($totalGroupCount>1) {
					$err = "Please select individual product and then change the data.<br>Products are not in the same category.";
					$disableBtn = "disabled";
				}
			}
		}
		
	
	if ($selDistributor) {		
		
		# Get Distributor State Records
		if ($addMode) {
			# Get not selected State list
			$getNotSelStateRecords = $distMarginStructureObj->getNotSelStateRecords($selDistributor, $distMarginRateListId, $selProduct, $pendingStateId, $selPMCityId);
			
			$getDistStateRecords  = $distMarginStructureObj->getDistributorStateRecords($selDistributor, $pendingStateId, $selPMCityId, $distMStateEntryId);
			if ($selProduct!="" && $selectionType=='I' && !sizeof($getNotSelStateRecords)) {
				$err = "Please make sure the selected record is not existing.";
				$disableBtn = "disabled";
			}
		}
		else if (($editMode&&$individualSelected&&$selProduct) || ($editMode&&$groupSelected)) {
			//echo "===========================================7777777777==============";
			$getDistStateRecords  = $distMarginStructureObj->getFilterDistStateRecords($selDistributor, $distStateId, $locationId, $distAvgMargin, $distMarginRateListId, $editDistMarginStructureId, $selProduct, $individualSelected, $locExportEnabled);
			/*
				Original
				$getDistStateRecords  = $distMarginStructureObj->getFilterDistStateRecords($selDistributor, $distMarginRateListId, $editDistMarginStructureId);	
			*/
		}

		
		# Get Distinct Margin Records
		if ($editMode&&$individualSelected&&$selProduct) {
			$distinctMarginRecs = $distMarginStructureObj->getDistinctMarginRecs($selDistributor, $selProduct, $distAvgMargin, $distMarginRateListId, $locExportEnabled);
		}

		# Distributor based Margin Rate List
		$distMarginRateListRecords = $distMarginRateListObj->filterDistributorWiseRecords($selDistributor);
		if ($addMode) {
			if ($distributorMgnRateList) $selRateList = $distributorMgnRateList;
			else $selRateList = $distMarginRateListObj->latestRateList($selDistributor);
		}
	}
	
	if ($addMode || $editMode) {
		#List All Margin Structure (Head) Record
		$marginStructureRecords = $marginStructureObj->fetchAllRecords();
	}	

	# Filter all Distributor
	//$distributorResultSetFilterObj = $distributorMasterObj->fetchAllRecords();
	$distributorResultSetFilterObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	if ($distributorFilterId) {
		$distributorRateListFilterRecords = $distMarginRateListObj->filterDistributorWiseRecords($distributorFilterId);
	}

	if ($addMode) {
		# Get Product Category Records
		//$productCategoryRecords	= $productCategoryObj->fetchAllRecords();
		$productCategoryRecords	= $productCategoryObj->fetchAllRecordsActiveCategory();

		# List all Product State Records
		//$productStateRecords = $productStateObj->fetchAllRecords();
		$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();
		
		# Get Enterd Dist Margin Struct
		$getDistributorRecords = $distMarginStructureObj->getDistributorRecords($selRateList, $seDistributor);
	}
	
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	if ($addMode) $rateListId = $selRateList;
	else $rateListId = $distMarginRateListId;
	
	if ($addMode || $editMode) {
		# CST PERCENT From TAX MASTER
		list($dmrStartDate, $dmrEndDate) = $distMarginStructureObj->getDistMgnRateListRec($rateListId);
		$cstPercent = $taxMasterObj->getBaseCst($dmrStartDate);

		# Edu Cess
		list($eduCessPercent, $eduCessRLId) = $distMarginStructureObj->getEduCessDuty($dmrStartDate);
		
		#Sec Edu Cess
		list($secEduCessPercent, $secEduCessRLId) = $distMarginStructureObj->getSecEduCessDuty($dmrStartDate);
	}

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav		

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/DistMarginStructure.js"; 

	#heading Section
	if ($editMode) $heading	=	$label_editDistMarginStructure;
	else	       $heading	=	$label_addDistMarginStructure;

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
	<form name="frmDistMarginStructure" id="frmDistMarginStructure" action="DistMarginStructure.php" method="post">
	<? 
		if (!$editSelected) {
	?>
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >	
	<tr>
		<td align="center">
			<a href="MarginStructure.php" class="link1" title="Click to Manage Margin Structure">Margin Structure</a>&nbsp;&nbsp;
			<a href="DistMarginRateList.php" class="link1" title="Click to Manage Distributor Rate List"> Distributor Margin Rate List</a>	
		</td>
	</tr>
		<?php
		if (!$distributorFilterId && !$distributorRateListFilterId) {
		?>
		<tr> 
			<td align="center" class="listing-item" style="color:Maroon;">
				<strong>Latest Distributor Margin list.</strong>
			</td>
		</tr>
		<?php
			}
		?>
	<tr><td height="5"></td></tr>
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1"><?=$err;?></td>
	</tr>
	<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Distributor Margin Structure";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="50%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="96%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
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
										<table cellpadding="0"  width="85%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('DistMarginStructure.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save & Exit " onclick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);" <?=$disableBtn?>>
<? if ($individualSelected && !$singleProduct) {?>
&nbsp;&nbsp;												<input type="submit" name="cmdSaveEdit" id="cmdSaveEdit" class="button" value=" Save & Edit " onclick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);">
&nbsp;&nbsp;
	<?
		if (sizeof($productRecords)>1) {	
	?>
&nbsp;&nbsp;												<input type="submit" name="cmdDelMargin" id="cmdDelMargin" class="button" value=" Delete Margin " onclick="return valiateDeleteMargin();">
&nbsp;&nbsp;
	<?
		}
	?>
<?
}
?>
</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center" nowrap="true">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistMarginStructure.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Save & Exit " onClick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);" <?=$disableBtn?>>	
	<?php
		if ($selectionType=="I") {
	?>
		&nbsp;&nbsp;
		<input type="submit" name="cmdSaveAddNew" id="cmdSaveAddNew" class="button" value=" Save & Assign New Product Margin " onClick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);" style="width:250px;" <?=$disableBtn?> />		
	<?php
		 }
	?>
	<input type="hidden" name="selProductIds" value="<?=$selProductIds?>" />
	<input type="hidden" name="urlFrom" value="<?=$urlFrom?>" />
	<input type="hidden" name="selPMCityId" value="<?=$selPMCityId?>" />
	</td>
		<?}?>
	</tr>
	<input type="hidden" name="hidDistMarginStructureId" id="hidDistMarginStructureId" value="<?=$editDistMarginStructureId;?>">
	<tr><TD height="10"></TD></tr>
					<tr>
					  	<td colspan="2" class="err1" align="center" nowrap style="padding-left:5px;padding-right:5px;" id="divRecExistTxt">
						</td>
					</tr>
					<tr>
					  	<td colspan="2" class="err1" align="center" nowrap style="padding-left:5px;padding-right:5px;" id="divProdRecExistTxt">
						</td>
					</tr>
					<tr>
					  	<td colspan="2" nowrap style="padding-left:10px;padding-right:10px;">
					<table width="200">
					<tr>
					<td nowrap class="fieldName">*Distributor</td>
					<td nowrap>
                                        <select name="selDistributor" id="selDistributor"  onchange="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>" <?=$disableField?>>
                                        <option value="">-- Select --</option>
					<?	
					while ($dr=$distributorResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorName = stripSlash($dr[2]);	
						$selected = ($selDistributor==$distributorId)?"selected":"";	
					?>
                            		<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? }?>
					</select>
					<input type="hidden" name="hidDistributor" value="<?=$selDistributor?>">
					</td></tr>
			<?php
				if ($displaySelProduct) {
			?>
			<tr>
				<TD colspan="4" class="listing-item" nowrap="true"><fieldset><legend class="fieldName" style="line-height:normal;">Selected Product</legend><b></b><?=$displaySelProduct?></fieldset></TD>
			</tr>
			<?php } ?>
			<?php
				if ($addMode) {
			?>
			<tr id="row0" style="display:none">
				<TD colspan="2" class="listing-item" align="center">[OR]</TD>
			</tr>
			<?php
				}
			?>
			<tr id="row1">
			<?php
				if (!$groupSelected) {
			?>
			<td nowrap class="fieldName">*Product</td>
			<td nowrap>
				<? if ($addMode) {?>
                        	<select name="selProduct" id="selProduct"  onchange="<? if ($addMode) {?>hideProductSpex('<?=$mode?>'); <? }?>xajax_chkEntryExist(document.getElementById('selDistributor').value, document.getElementById('selProduct').value, document.getElementById('distMarginRateList').value,'<?=$mode?>', '<?=$distMarginId?>','<?=$pendingStateId?>');xajax_chkProductRecsExist(document.getElementById('selProductCategory').value, document.getElementById('selProductState').value,  document.getElementById('selProductGroup').value, '<?=$mode?>', document.getElementById('selDistributor').value, '<?=$pendingStateId?>');getStateWiseVat();this.form.submit();" <?=$disableField?>>
				<?
				} else {
				?>
				<select name="selProduct" id="selProduct"  onchange="this.form.editId.value=<?=$editId?>;this.form.submit();">	
				<?
					}
				?>
                                <option value="">-- Select --</option>
				<?				
				foreach ($productRecords as $pr) {
					$mproductId	= $pr[0];					
					$mproductName	= $pr[2];
					$selected = ($selProduct==$mproductId && $mproductId!="")?"Selected":"";
				?>
                            	<option value="<?=$mproductId?>" <?=$selected?>><?=$mproductName?></option>
				<? 
					}
				?>
				</select>
				<input type="hidden" name="hidProduct" value="<?=$selProduct?>">
				</td>
			<?
			} // Group selection
			?>
			<?
				if (!$groupSelected && $editSelection && sizeof($distinctMarginRecs)>0) {
			?>
			<tr>
				<TD colspan="2">
					<table>
						<TR>
							<TD nowrap="true" class="fieldName">Change Margin To</TD>
							<td>
								<select name="selMargin" id="selMargin" onchange="hideListedRows();">
								<option value="">-- Select --</option>
								<?
									foreach ($distinctMarginRecs as $dmr) {
										$distStateEntryId = $dmr[0];
										$distAvgMargin = $dmr[1];
								?>
								<option value="<?=$distStateEntryId?>"><?=$distAvgMargin?></option>
								<?
									}
								?>
								</select>
							</td>
							<td>
								<input type="submit" name="cmdSwitchMargin" id="cmdSwitchMargin" value=" Switch Margin " class="button" onclick="return valiateSwitchMargin();">
							</td>
						</TR>
					</table>
				</TD>
				
			</tr>
			<?
				}
			?>

				<?php
					if ($addMode) {
				?>
				<td class="listing-item" id="column0">[OR]</td>
				<td id="column1">
					<fieldset>
					<legend class="listing-item">Product</legend>
					<table  cellspacing="0" cellpadding="0">
						<tr>
							<td class="fieldName" style="padding-left:2px;padding-right:2px;" nowrap="true">Category</td>
							<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductCategory" id="selProductCategory" onchange="xajax_chkProductRecsExist(document.getElementById('selProductCategory').value, document.getElementById('selProductState').value,  document.getElementById('selProductGroup').value, '<?=$mode?>', document.getElementById('selDistributor').value);getStateVatPercent();">
		<option value=''>-- Select All --</option>";
		<?php
		if (sizeof($productCategoryRecords)>0) {	
			 foreach ($productCategoryRecords as $cr) {
				$categoryId	= $cr[0];
				$categoryName	= stripSlash($cr[1]);
				$selected = ($productCategory==$categoryId)?"Selected":"";
		?>	
		<option value="<?=$categoryId?>" ><?=$categoryName?></option>	
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="fieldName" style="padding-left:2px;padding-right:2px;" nowrap="true">State</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductState" id="selProductState" onChange="xajax_getProductGroupExist(document.getElementById('selProductState').value);xajax_chkProductRecsExist(document.getElementById('selProductCategory').value, document.getElementById('selProductState').value,  document.getElementById('selProductGroup').value, '<?=$mode?>', document.getElementById('selDistributor').value);getStateVatPercent();">
		<option value='0'>-- Select All --</option>";
		<?php
		if (sizeof($productStateRecords)>0) {	
			foreach ($productStateRecords as $cr) {
				$prodStateId	= $cr[0];
				$prodStateName	= stripSlash($cr[1]);
				$selected = ($productState==$prodStateId)?"Selected":"";
		?>	
		<option value="<?=$prodStateId?>"><?=$prodStateName?></option>
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="fieldName" style="padding-left:2px;padding-right:2px;" nowrap="true">Group</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductGroup" id="selProductGroup" onchange="xajax_chkProductRecsExist(document.getElementById('selProductCategory').value, document.getElementById('selProductState').value,  document.getElementById('selProductGroup').value, '<?=$mode?>', document.getElementById('selDistributor').value);getStateVatPercent();">
		<option value='0'>-- Select --</option>
		</select>
	</td>
	</tr>				
	</table>
	</fieldset>
				</td>
	<?php
		}
	?>
			</tr>
				</table>
		</td></tr>
<!-- Copy from Section OR Set Margin	 -->
	<?php
	if ($addMode) {
	?>
	<?php
		if (sizeof($getNotSelStateRecords)>0) {
	?>
	<tr>
		<TD colspan="2" align="left" style="padding-left:10px;padding-right:10px;">
			<table>
				<TR>
					<TD class="fieldName">State not assigned</TD>
					<td style="padding-left:15px;">
						<select name="selState" id="selState" onchange="this.form.submit();">
							<option value="">-- Select All--</option>
							<?php
								foreach ($getNotSelStateRecords as $gnssr) {
									$nsStateId  = $gnssr[0];
									$nsStateName = $gnssr[1];
									$selected = ($pendingStateId==$nsStateId)?"selected":"";		
							?>
							<option value="<?=$nsStateId?>" <?=$selected?>><?=$nsStateName?></option>
							<?php
								}
							?>
						</select>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
	<?php
		} else {
	?>
		<input type="hidden" name="selState" value="<?=$pendingStateId?>"/>
	<? }?>
	<tr>
		<TD colspan="2" align="left" style="padding-left:10px;padding-right:10px;">			
			<table>
				<TR>	
					<TD>
			<fieldset>
			<legend class="listing-item">Margin</legend>
			<table>
				<TR>
					<TD class="listing-item">
						<INPUT type="radio" name="marginSelection" id="marginSelection1" value="C" class="chkBox" onclick="hideCopyFromRows();">&nbsp;Copy
					</TD>					
					<td class="listing-item">&nbsp;(OR)&nbsp;</td>
					<td class="listing-item">
						<INPUT type="radio" name="marginSelection" id="marginSelection2" value="S" class="chkBox" onclick="hideCopyFromRows();">&nbsp;Set
					</td>
				</TR>
			</table>
				</fieldset>
					</TD>
				</TR>
			</table>
		
		</TD>
	</tr>	
	<tr>
		<TD colspan="2" align="left" nowrap="true" style="padding-left:10px;padding-right:10px;">
			<table>
	<tr id="copyFromRow">
				<TD class="fieldName" nowrap="true">Copy From</TD>
				<td nowrap="true" class="fieldName">
					<select name="copyFromDistId" id="copyFromDistId" onchange="xajax_getDistWiseAvgMargin(document.getElementById('copyFromDistId').value, '', '<?=$distributorMgnRateList?>')">
						<option value="">-- Select --</option>
					<?php
						if (sizeof($getDistributorRecords)>0) {
							foreach ($getDistributorRecords as $dr) {
								$sDistid	= $dr[0];
								$distName 	= $dr[1];
					?>
						<option value="<?=$sDistid?>"><?=$distName?></option>
					<?php
							}
						}
					?>
					</select>&nbsp;&nbsp;Margin&nbsp;
					<select name="selDistMargin" id="selDistMargin">
								<option value="">-- Select --</option>
								<?
									foreach ($distinctMarginRecs as $dmr) {
										$distStateEntryId = $dmr[0];
										$distAvgMargin = $dmr[1];
								?>
								<option value="<?=$distStateEntryId?>"><?=$distAvgMargin?></option>
								<?
									}
								?>
								</select>
				</td>
			</tr>
			</table>
		</TD>
	</tr>
	
	<?
	}
	?>
	<tr>
	  	<td colspan="2" align="left" nowrap="true" style="padding-left:10px;padding-right:10px;">
		<table>
	<tr id="row2">
	<?php
	# Distributor State Records || $individualSelected
	if (sizeof($getDistStateRecords)>0) {			
	?>
	<TD colspan="2">
	<table>
		<tr><TD>
		<table cellspacing="1" border="0" align="left" bgcolor="#999999">	
			<tr bgcolor="#f2f2f2">
				<td class="listing-head" align="center" style="padding-left:5px;padding-right:5px;">State</td>
				<?	$j=0;
					$selCityEntryId = "";
					foreach ($getDistStateRecords as $dsr) {
						$j++;
						$distStateEntryId	= $dsr[0];
						$selStateId		= $dsr[2];	
						$selTaxType		= $dsr[12];
						$billingForm		= $dsr[13];
						$stateRec		= $stateMasterObj->find($selStateId);
						$stateName		= stripSlash($stateRec[2]);
						$distributorMgnStateEntryId = $dsr[14];	

						$locExport			= $dsr[16];
						if ($editMode) $locExport	= $dsr[23];

						$selCityRecords	= $distMarginStructureObj->getSelCityList($distStateEntryId);				
						list($selCityEntryId, $selCityId) = $distributorMasterObj->getSelCityId($distStateEntryId);
						$selAreaRecords	= $distMarginStructureObj->getSelAreaList($selDistributor, $selStateId, $selCityId);	
				?>
				<TD class="listing-head" align="center" style="padding-left:5px;padding-right:5px;">
					<?=$stateName?><? if ($locExport=='Y') {?><span class="listing-head" style="line-height:normal;font-size:8px;font-weight:normal;">(Export)</span> <? }?>
					<br>
					<span class="listing-head" style="line-height:normal;font-size:11px;">
					(<?
							$numLine = 3;
							if (sizeof($selCityRecords)>0) {
								$nextRec	=	0;
								$k=0;
								$cityName = "";
								foreach ($selCityRecords as $cR) {
									$selStateCityId = $cR[0];		
									$cityName = $cR[1];
									$nextRec++;
									if($nextRec>1) echo ",&nbsp;";
									echo $cityName;
									if($nextRec%$numLine==0) { 
										echo "<br>";
									}
								}
							}
						?>)
					</span>	
					<br>
					<span class="listing-head" style="line-height:normal;font-size:10px;font-weight:normal;">
					[<?php
							$numLine = 2;
							if (sizeof($selAreaRecords)>0) {
								$nextRec	=	0;
								$k=0;
								$cityName = "";
								$areaArr = array();
								$n = 0;
								foreach ($selAreaRecords as $cR) {		
									$cityName = $cR[1];
									$areaArr[$n] = $cR[0];
									$nextRec++;
									if($nextRec>1) echo ",&nbsp;";
									echo $cityName;
									if($nextRec%$numLine==0) { 
										echo "<br>";
									}
									$n++;
								}
							}
							$selAreaIds =  implode(',',$areaArr);
						?>]
					</span>		
				</TD>
				<input type="hidden" name="selStateId_<?=$j?>" id="selStateId_<?=$j?>" value="<?=$selStateId?>">
				<input type="hidden" name="billingForm_<?=$j?>" id="billingForm_<?=$j?>" value="<?=$billingForm?>">
				<input type="hidden" name="taxType_<?=$j?>" id="taxType_<?=$j?>" value="<?=$selTaxType?>">				
				<input type="hidden" name="hidDistStateEntryId_<?=$j?>" id="hidDistStateEntryId_<?=$j?>" value="<?=$distStateEntryId?>">
				<input type="hidden" name="hidDistMarginStateEntryId_<?=$j?>" id="hidDistMarginStateEntryId_<?=$j?>" value="<?=$distributorMgnStateEntryId?>">
				<input type="hidden" name="selCityId_<?=$j?>" id="selCityId_<?=$j?>" value="<?=$selStateCityId?>">
				<input type="hidden" name="selAreaIds_<?=$j?>" id="selAreaIds_<?=$j?>" value="<?=$selAreaIds?>">
				<?php
					}
				?>
	</tr>
	<?php
		$m=0;
		$prevUseAvgDistMagn = "";
		foreach ($marginStructureRecords as $msr) {
			$m++;
			$marginStructureId = $msr[0];
			$marginStructureName	= stripSlash($msr[1]);
			$mgnStructureDescr	= stripSlash($msr[2]);
			$priceCalcType		= $msr[3];
			$useAvgDistMagn		= $msr[4];			
			$mgnStructBillingOnFormF = $msr[7];
			
			$readOnly = "";
			if ($mgnStructBillingOnFormF=='Y') $readOnly = "readOnly";
			
			if ($prevUseAvgDistMagn != $useAvgDistMagn && $m>1) {
				// VAT/CST Included in Margin 
				echo '<tr bgcolor="white"><td class="fieldname" style="padding-left:5px;	 padding-right:5px;line-height:normal;" nowrap>VAT/CST <br/>Included in Margin</td>';
					$j=0;
					$vatorCstIncMgnChk = "";
					foreach ($getDistStateRecords as $dsr) {
						$j++;
						$vatorCstIncMgnChk = ($dsr[22]=='Y')?"checked":"";
				echo "<td class=\"listing-item\" nowrap=\"true\" style=\"padding-left:5px;padding-right:5px;color:#B80000;\" align=\"center\">
					<input type=\"checkbox\" name=\"vatorCstInc_$j\" id=\"vatorCstInc_$j\" size=\"8\" value=\"Y\" class=\"chkBox\" $vatorCstIncMgnChk onclick=\"calcDistAvgMarginStruct();\"></td>";
					}
				echo '</tr>';
					
				echo '<tr bgcolor="white"><td class="fieldname" style="padding-left:5px; padding-right:5px;color:#B80000;" nowrap><b>Average&nbsp;Margin</b></td>';
					$j=0;
					$avgMargin = "";
					foreach ($getDistStateRecords as $dsr) {
						$j++;
						$avgMargin = $dsr[15];
				echo "<td class=\"listing-item\" nowrap=\"true\" style=\"padding-left:5px;padding-right:5px;color:#B80000;\" align=\"right\">
					<input type=\"text\" name=\"avgMargin_$j\" id=\"avgMargin_$j\" size=\"8\" value=\"$avgMargin\" style=\"text-align:right;border:none;color:#B80000;font-weight:bold;\" readonly>&nbsp;%<input type=\"hidden\" name=\"hidAvgMargin_$j\" id=\"hidAvgMargin_$j\" size=\"5\" value=\"$avgMargin\" style=\"text-align:right;border:none;color:#B80000;font-weight:bold;\" readonly></td>";
					}
				echo '</tr>';

				// Excise Duty
				echo "<tr bgcolor=\"White\"><td class=\"fieldName\" nowrap style=\"padding-left:5px;padding-right:5px;\">Excise&nbsp;Duty</td>";
					$j=0;
					$vat = "";
					foreach ($getDistStateRecords as $dsr) {
						$j++;
						//$sStateId	= $dsr[2];
						
						$exBillingForm = "";
						$exciseDuty = 0;
						if ($editMode) $exBillingForm = $dsr[28];
						else $exBillingForm = $dsr[19];		
						
						if ($exBillingForm!='FCT1') {
							$basicExciseDuty	= $distMarginStructureObj->getExciseDutyPercent($selProduct, $dmrStartDate, $groupSelected, $gpCategoryId, $gpStateId, $gpGroupId);
							$exciseDuty = $basicExciseDuty;
						}

						if ($exciseDuty>0) {
							$eduCessDutyRate 	= number_format(($exciseDuty*($eduCessPercent/100)),2,'.','');
							$exciseDuty += $eduCessDutyRate;
							$secEduCessDutyRate 	= number_format(($exciseDuty*($secEduCessPercent/100)),2,'.','');
							$exciseDuty += $secEduCessDutyRate;
						}
						
				echo "<td class=\"listing-item\" nowrap=\"true\" style=\"padding-left:5px;padding-right:5px;\" align=\"right\">
					<input type=\"text\" name=\"exciseDuty_$j\" id=\"exciseDuty_$j\" size=\"5\" value=\"$exciseDuty\" style=\"text-align:right;border:none;\" onkeydown=\"return focusNextBox(event,'document.frmDistMarginStructure','freight_$j','vat_$j','octroi_$j');\" autocomplete=\"off\" readonly=\"true\" onkeyup=\"calcDistAvgMarginStruct();\">&nbsp;%</td>";
				}
				echo "</tr>";

				// Basic Margin if Excise exist 
				echo '<tr bgcolor="white"><td class="fieldname" style="padding-left:5px; padding-right:5px;color:#B80000;" nowrap><b>Basic&nbsp;Margin</b></td>';
					$j=0;
					$avgMargin = "";
					foreach ($getDistStateRecords as $dsr) {
						$j++;
						$basicMargin = "";
						if ($editMode) $basicMargin = $dsr[25];
				echo "<td class=\"listing-item\" nowrap=\"true\" style=\"padding-left:5px;padding-right:5px;color:#B80000;\" align=\"right\">
					<input type=\"text\" name=\"basicMargin_$j\" id=\"basicMargin_$j\" size=\"8\" value=\"$basicMargin\" style=\"text-align:right;border:none;color:#B80000;font-weight:bold;\" readonly>&nbsp;%<input type=\"hidden\" name=\"hidBasicMargin_$j\" id=\"hidBasicMargin_$j\" size=\"5\" value=\"$basicMargin\" style=\"text-align:right;border:none;color:#B80000;font-weight:bold;\" readonly></td>";
					}
				echo '</tr>';

				// VAT/CST
				echo "<tr bgcolor=\"White\"><td class=\"fieldName\" nowrap style=\"padding-left:5px;padding-right:5px;\">VAT/CST</td>";
					$j=0;
					$vat = "";
					foreach ($getDistStateRecords as $dsr) {
						$j++;
						$sStateId	= $dsr[2];
						$defaultTaxPercent = ($dsr[17]!=0)?$dsr[17]:($distMarginStructureObj->getDistWiseTaxPercent($selProduct, $sStateId, $selDistributor, $rateListId));
						
						$selBillingForm = $dsr[13];	// ZP: Zero Percent
						$vat = ($selBillingForm=='ZP')?0:$defaultTaxPercent;

				echo "<td class=\"listing-item\" nowrap=\"true\" style=\"padding-left:5px;padding-right:5px;\" align=\"right\">
					<input type=\"text\" name=\"vat_$j\" id=\"vat_$j\" size=\"5\" value=\"$vat\" style=\"text-align:right;border:none;\" onkeydown=\"return focusNextBox(event,'document.frmDistMarginStructure','freight_$j','vat_$j','octroi_$j');\" autocomplete=\"off\" readonly=\"true\" onkeyup=\"calcDistAvgMarginStruct();\">&nbsp;%</td>";
				}
				echo "</tr>";
				// Final Margin
				echo '<tr bgcolor="white"><td class="fieldname" style="padding-left:5px; padding-right:5px;color:#B80000;" nowrap><b>Final&nbsp;Margin</b></td>';
					$j=0;
					$avgMargin = "";
					foreach ($getDistStateRecords as $dsr) {
						$j++;
						$finalMargin = ($dsr[21]!="")?$dsr[21]:0;
				echo "<td class=\"listing-item\" nowrap=\"true\" style=\"padding-left:5px;padding-right:5px;color:#B80000;\" align=\"right\">
					<input type=\"text\" name=\"finalMargin_$j\" id=\"finalMargin_$j\" size=\"8\" value=\"$finalMargin\" style=\"text-align:right;border:none;color:#B80000;font-weight:bold;\" readonly>&nbsp;%</td>";
					}
				echo '</tr>';
			}
	?>
	<tr bgcolor="White">
	 <td class="fieldName" nowrap style="padding-left:5px;padding-right:5px;"><?=$marginStructureName?></td>
	<?php
		$j=0;
		$distMarginEntryId = "";
		$distMarginPercent = "";
		foreach ($getDistStateRecords as $dsr) {
			$j++;
			$distributorMgnStateEntryId = $dsr[14];
			if ($editMode) {
				list($distMarginEntryId, $distMarginPercent) = $distMarginStructureObj->getMarginEntryRec($distributorMgnStateEntryId,$marginStructureId);
			}	
	?>
	 <td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right">
		<INPUT TYPE="hidden" NAME="marginStructureId_<?=$m?>_<?=$j?>" id="marginStructureId_<?=$m?>_<?=$j?>" value="<?=$marginStructureId;?>">
		<input type="hidden" name="distMarginEntryId_<?=$m?>_<?=$j?>" value="<?=$distMarginEntryId?>">

		<INPUT TYPE="text" NAME="distMarginPercent_<?=$m?>_<?=$j?>" id="distMarginPercent_<?=$m?>_<?=$j?>" size="5" value="<?=$distMarginPercent;?>" style="text-align:right;" onkeyup="calcDistAvgMarginStruct();" <?=$readOnly?> autoComplete="off" onkeydown="return nextTBox(event,'document.frmDistMarginStructure','distMarginPercent_<?=$m+1?>_<?=$j?>');">&nbsp;%

		<INPUT TYPE="hidden" NAME="priceCalcType_<?=$m?>_<?=$j?>" id="priceCalcType_<?=$m?>_<?=$j?>" size="5" value="<?=$priceCalcType;?>" style="text-align:right;">
		<INPUT TYPE="hidden" NAME="useAvgDistMagn_<?=$m?>_<?=$j?>" id="useAvgDistMagn_<?=$m?>_<?=$j?>" size="5" value="<?=$useAvgDistMagn;?>" style="text-align:right;">
		<INPUT TYPE="hidden" NAME="mgnStructBillingOnFormF_<?=$m?>_<?=$j?>" id="mgnStructBillingOnFormF_<?=$m?>_<?=$j?>" size="5" value="<?=$mgnStructBillingOnFormF;?>">
 	 </td>
		<?php
			}
		?>
	</tr>
	<? 
		//}
		$prevUseAvgDistMagn = $useAvgDistMagn;	
		}  # Margin Structure Loop End
	?>
	<?
		$j=0;
		foreach ($getDistStateRecords as $dsr) {
			$j++;
	?>
		<input type="hidden" name="hidFieldRowCount_<?=$j?>" id="hidFieldRowCount_<?=$j?>" value="<?=$m?>">		
	<?php
		}
	?>		
	<tr bgcolor="White">
		<td class="fieldName" nowrap style="padding-left:5px;padding-right:5px;">Octroi</td>
		<?php
			$j=0;
			$octroi = "";
			foreach ($getDistStateRecords as $dsr) {
				$j++;
				$octroi = 0;
				$octroiApplicable = $octroiExempted = "";
				if ($editMode) {
					$octroi = ($dsr[16]!="")?$dsr[16]:0;
					$octroiApplicable	= $dsr[26]; 
					$octroiExempted		= $dsr[27];
				} else {
					//getDistributorStateRecords
					$octroiApplicable	= $dsr[17]; 
					$octroiExempted		= $dsr[18];
				}
		?>
		<td class="listing-item" nowrap="true" style="padding-left:5px;padding-right:5px;" align="right">		
			<input type="hidden" name="hidOctroiApplicable_<?=$j?>" id="hidOctroiApplicable_<?=$j?>" value="<?=$octroiApplicable?>">
			<input type="hidden" name="hidOctroiExempted_<?=$j?>" id="hidOctroiExempted_<?=$j?>" value="<?=$octroiExempted?>">
				<input type="text" name="octroi_<?=$j?>" id="octroi_<?=$j?>" size="5" value="<?=$octroi;?>" style="text-align:right; border:none;" onkeydown="return focusNextBox(event,'document.frmDistMarginStructure','freight_<?=$j?>','octroi_<?=$j?>','octroi_<?=$j?>');" autocomplete="off" onkeyup="calcDistAvgMarginStruct();" readonly>&nbsp;%</td>
		<?
			}
		?>
	</tr>	
	<tr bgcolor="White">
		<td class="fieldName" nowrap style="padding-left:5px;padding-right:5px;color:#B80000;"><b>Actual Margin</b></td>
		<?php		
		$j=0;
		$avgMargin = "";
		foreach ($getDistStateRecords as $dsr) {
			$j++;
			$actualMgn = ($dsr[20]!="" && $editMode)?$dsr[20]:0; 
		?>
		<td class="listing-item" nowrap="true" style="padding-left:5px;padding-right:5px;color:#B80000;" align="right">
			<input type="text" name="actualMargin_<?=$j?>" id="actualMargin_<?=$j?>" size="8" value="<?=$actualMgn;?>" style="text-align:right;border:none;color:#B80000;font-weight:bold;" readonly>&nbsp;%
		</td>
		<?php
			}
		?>
	</tr>
	<tr bgcolor="White">
		<td class="fieldName" nowrap style="padding-left:5px;padding-right:5px;">Freight</td>
		<?
		$j=0;
		$freight = "";
		foreach ($getDistStateRecords as $dsr) {
			$j++;			
			$freight = ($dsr[18]!="" && $editMode)?$dsr[18]:0; 
		?>
		<td class="listing-item" nowrap="true" style="padding-left:5px;padding-right:5px;" align="right">
			<input type="text" name="freight_<?=$j?>" id="freight_<?=$j?>" size="5" value="<?=$freight;?>" style="text-align:right;" onkeydown="return focusNextBox(event,'document.frmDistMarginStructure','transportCost_<?=$j?>','freight_<?=$j?>','vat_<?=$j?>');" autocomplete="off">&nbsp;%
		</td>
		<?
			}
		?>
	</tr>
	<tr bgcolor="White">
		<td class="fieldName" nowrap style="padding-left:5px;padding-right:5px;">Transportation Cost</td>
		<?
		$j=0;
		$transportCost = "";
		foreach ($getDistStateRecords as $dsr) {
			$j++;			
			$transportCost = ($dsr[19]!="" && $editMode)?$dsr[19]:0; 
		?>
		<td class="listing-item" nowrap="true" style="padding-left:5px;padding-right:20px;" align="right">	
			<input type="text" name="transportCost_<?=$j?>" id="transportCost_<?=$j?>" size="5" value="<?=$transportCost;?>" style="text-align:right;" onkeydown="return focusNextBox(event,'document.frmDistMarginStructure','transportCost_<?=$j?>','transportCost_<?=$j?>','freight_<?=$j?>');" autocomplete="off">
		</td>
		<?
			}
		?>
	</tr>
	</table>
	</TD></TR>	
	</table>	
<!--  State Loop Ends Here-->
	</TD>	
	<?php
		}
	?>	
	<input type="hidden" name="hidDistStateRowCount" id="hidDistStateRowCount" value="<?=$j?>">
	</tr>	
	<tr id="rateListRow">
	<td>			
		<input type="hidden" name="distMarginRateList" id="distMarginRateList" value="<?=$rateListId?>" readonly="true">
	</td>
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
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistMarginStructure.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save & Exit " onclick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);" <?=$disableBtn?>>
<? if ($individualSelected && !$singleProduct) {?>
&nbsp;&nbsp;												<input type="submit" name="cmdSaveEdit" id="cmdSaveEdit" class="button" value=" Save & Edit " onclick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);">
&nbsp;&nbsp;
	<?
		if (sizeof($productRecords)>1) {	
	?>
&nbsp;&nbsp;
	<input type="submit" name="cmdDelMargin" id="cmdDelMargin" class="button" value=" Delete Margin " onclick="return valiateDeleteMargin();">
&nbsp;&nbsp;
	<?
		}
	?>
<?
}
?>		
				</td>
				<?} else{?>
				<td  colspan="2" align="center" nowrap="true">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistMarginStructure.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Save & Exit " onClick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);" <?=$disableBtn?> />
					<?php
						if ($selectionType=="I") {
					?>
						&nbsp;&nbsp;
						<input type="submit" name="cmdSaveAddNew" id="cmdSaveAddNew" class="button" value=" Save & Assign New Product Margin  " onClick="return validateDistMarginStructureMaster(document.frmDistMarginStructure);" style="width:250px;" <?=$disableBtn?> />		
					<?php
						}
					?>							
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
			<?php
				require("template/rbBottom.php");
			?>
			</td>
		</tr>
			</table>
				<!-- Form fields end   -->		
		</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
		?>
			</table>
		</td>
	</tr>
			<tr>
				<td height="10" align="center" ></td>
			</tr>		
			<tr>
				<td colspan="3" align="center">
						<table width="35%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="4">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item" nowrap="true">Distributor&nbsp;</td>
                <td>
		<select name="distributorFilter" id="distributorFilter" onchange="this.form.submit();">
		<option value="">-- Select All --</option>
		<?php	
			while ($dr=$distributorResultSetFilterObj->getRow()) {
				$distributorId	 = $dr[0];
				$distributorName = stripSlash($dr[2]);	
				$selected = ($distributorFilterId==$distributorId)?"selected":"";	
		?>
                <option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
		<?php 
			}
		?>		
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
	   <td class="listing-item" nowrap="true">Rate List&nbsp;</td>
	<td>
		<select name="distributorRateListFilter" id="distributorRateListFilter" onchange="this.form.submit();">
                        <option value="">-- Select All --</option>
			<?
			foreach ($distributorRateListFilterRecords as $srl) {
				$rateListRecId	=	$srl[0];
				$rateListName	=	stripSlash($srl[1]);				
				$startDate	=	dateFormat($srl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = ($distributorRateListFilterId==$rateListRecId)?"Selected":"";
			?>
                      <option value="<?=$rateListRecId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                      </select>
			</td>
                    </tr>
                    </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
				</td>
			</tr>	
		<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
				
					<tr>
						<td   bgcolor="white">
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Distributor Margin Structure  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<tr>
		<TD colspan="3">
			<table width="100%" align="center">
				<TR>
					<TD width="400"></TD>
					<TD width="250">
						<table>
							<TR>
								<TD nowrap="true">
									<? if($del==true){?>
									<input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distMarginRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistMarginStructure.php?distributorFilter=<?=$distributorFilterId?>&distributorRateListFilter=<?=$distributorRateListFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>
					<TD width="300">
						<table border="0">
						<?php
							if ($isAdmin || $reEdit) {
						?>
						<tr>
							<TD style="padding-left:10px;padding-right:10px;" align="right">
								<input type="button" name="refreshDMS" value="Refresh Distributor Margin Structure" class="button" onclick="return updateDistMgnStruct();" title="Click here to revise the distributor Margin structure." style="width:250px;" />
							</TD>
						</tr>
						<?php
							}
						?>
					</table>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if ($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
							<td colspan="2" style="padding-left:5px;padding-right:5px;">
		<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($distMarginRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistMarginStructure.php?pageNo=$page&distributorFilter=$distributorFilterId&distributorRateListFilter=$distributorRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistMarginStructure.php?pageNo=$page&distributorFilter=$distributorFilterId&distributorRateListFilter=$distributorRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistMarginStructure.php?pageNo=$page&distributorFilter=$distributorFilterId&distributorRateListFilter=$distributorRateListFilterId\"  class=\"link1\">>></a> ";
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
	<tr  align="center">
		<th width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
		</th>
		<?php
			if ($distributorFilterId && !$distributorRateListFilterId) {
		?>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Rate List</th>		
		<?php
			}
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">State</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Location</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" onMouseover="ShowTip('Final Margin');" onMouseout="UnTip();">Margin (%)</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Product Codes</th>	
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
		$prevDistributorId 	= "";
		$prevProductId		= "";
		$prevStateId	   	= "";	
		$selCriteria		= "";		
		foreach ($distWiseProuctWiseMarginRecords as $dmr) {
			$i++;
			$distMarginId = $dmr[0];
			$distributorId = $dmr[1];

			$distributorName = "";
			if ($prevDistributorId!=$distributorId) {
				$distributorName = $dmr[4];
			}
			$productId	= $dmr[2];
			$productName	= "";
			if ($prevProductId!=$productId) {
				$productName	= $dmr[5];
			}
			$distMarginStateEntryId = $dmr[6];
			
			$stateId	= $dmr[11];
			$stateName = "";
			if ($prevStateId!=$stateId || $prevDistributorId!=$distributorId) {	
				$stateName	= $dmr[7];
			}
			
			$avgMargin	= $dmr[8];			
			
			$disabled = "";
			if ($distributorFilterId=="" || $distributorRateListFilterId=="") $disabled = "disabled"; 

			$cityId		= $dmr[12];
			$cityName     = $dmr[10];
			$distRateListId   = $dmr[3];
			$distFinalMargin	 = $dmr[13];
			$distMgnRateListName	 = $dmr[14];

			$selDistStateEntryId = $dmr[9];
			$exportEnabled		=  $dmr[15];
			
			# Get Prouct Records	
			$getProductRecs = $distMarginStructureObj->getDistMarginProductRecs($distributorId, $stateId, $cityId, $distFinalMargin, $distRateListId, $exportEnabled);			
			$selCriteria = "$distributorId,$stateId,$cityId,$distFinalMargin,$distRateListId,$distMarginId,$exportEnabled,$selDistStateEntryId";	
	?>
	<tr>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$selCriteria;?>" class="chkBox"></td>
		<?php
		if ($distributorFilterId && !$distributorRateListFilterId) {
		?>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$distMgnRateListName;?></td>	
		<?php
			}
		?>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>			
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$stateName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$cityName;?>
			<? if ($exportEnabled=='Y') {?><br><span class="listing-head" style="line-height:normal;font-size:8px;font-weight:normal;">(Export)</span> <? }?>
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$distFinalMargin;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap="true">
						<?php
							$numLine = 6;
							if (sizeof($getProductRecs)>0) {
								$nextRec	=	0;
								$k=0;
								foreach ($getProductRecs as $cR) {		
									$productCode	= $cR[1];
									$selProdName	= $cR[2];
									$nextRec++;
									if ($nextRec>1) echo ",&nbsp;";
									echo "<span onMouseover=\"ShowTip('$selProdName');\" onMouseout=\"UnTip();\">$productCode</span>";
									if($nextRec%$numLine==0) { 
										echo "<br>";
									}
								}
							}
						?>		
		</td>		
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,'<?=$selCriteria?>','editSelItem');assignValue(this.form,'1','editSelectionChange');this.form.action='DistMarginStructure.php';" >
		</td>
	<? }?>
	</tr>
	<?
		$prevDistributorId = $distributorId;
		$prevProductId	   = $productId;
		$prevStateId	   = $stateId;		
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">		
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistMarginStructure.php?pageNo=$page&distributorFilter=$distributorFilterId&distributorRateListFilter=$distributorRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistMarginStructure.php?pageNo=$page&distributorFilter=$distributorFilterId&distributorRateListFilter=$distributorRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistMarginStructure.php?pageNo=$page&distributorFilter=$distributorFilterId&distributorRateListFilter=$distributorRateListFilterId\"  class=\"link1\">>></a> ";
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
			<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
		</tr>	
		<?
			}
		?>
	</tbody>
	</table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="5">
			<input type="hidden" name="hidCstRate" id="hidCstRate" value="<?=$cstPercent?>">	
			<input type="hidden" name="sizeOfMarginStructureRecs" value="<?=sizeof($marginStructureRecords)?>">
			<input type="hidden" name="hidEduCessPercent" id="hidEduCessPercent" value="<?=$eduCessPercent?>" readonly="true" />
			<input type="hidden" name="hidSecEduCessPercent" id="hidSecEduCessPercent" value="<?=$secEduCessPercent?>" readonly="true" />
		</td>
	</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?>
												<input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distMarginRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistMarginStructure.php?distributorFilter=<?=$distributorFilterId?>&distributorRateListFilter=<?=$distributorRateListFilterId?>',700,600);"><? }?></td>
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
	<input type='hidden' name="productStateGroup" id="productStateGroup" value=''>
	<input type='hidden' name="copyFromEnabled" id="copyFromEnabled" value='0'>
	<input type='hidden' name="singleProdEnabled" id="singleProdEnabled" value=''>
	<input type='hidden' name="switchMarginEnabled" id="switchMarginEnabled" value=''>
	<input type='hidden' name="selectionType" id="selectionType" value='<?=$selectionType?>'>
	<input type='hidden' name="distMStateEntryId" id="distMStateEntryId" value='<?=$distMStateEntryId?>'>

		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td height="10" align="center">
				<a href="MarginStructure.php" class="link1" title="Click to Manage Margin Structure">Margin Structure</a>&nbsp;&nbsp;
				<a href="DistMarginRateList.php" class="link1" title="Click to Manage Distributor Rate List"> Distributor Margin Rate List</a>
			</td>	
		</tr>
	</table>
	<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
	<?
		if ($addMode) {
	?>
	<script language="JavaScript">
		xajax_chkEntryExist(document.getElementById('selDistributor').value, document.getElementById('selProduct').value, document.getElementById('distMarginRateList').value,'<?=$mode?>',document.getElementById('hidDistMarginStructureId').value, '<?=$pendingStateId?>');
		hideProductSpex('<?=$mode?>');
		getStateWiseVat();
		getOctroiPercent();
	</script>
	<?php
		}
	?>
	<?php
		} // Not Edit section ends here
	?>
	<input type="hidden" name="editSelItem" id="editSelItem" value="<?=$editSelItem?>">
	<input type="hidden" name="hidSelection" id="hidSelection" value="<?=$editSelection?>">
	<input type="hidden" name="distStateWiseEntryId" id="distStateWiseEntryId" value="<?=$distStateWiseEntryId?>">
	<input type="hidden" name="hidDistributorFilterId" value="<?=$distributorFilterId?>">	
	<input type="hidden" name="hidDistributorRateListFilterId" value="<?=$distributorRateListFilterId?>">
	
	<?
	// Edit Select
	if ($editSelected) {	
	?>
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><td height="10"></td></tr>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
	<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<?php							
								$entryHead = "Edit Selection";
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Edit Selection  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>

								
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>		
		<tr>
			<td  colspan="3" align="center">
				<input type="submit" name="cmdBack" class="button" value=" Go Back " >&nbsp;&nbsp;
				<input type="submit" name="cmdContinue" class="button" value=" Continue " onClick="return validateEditSelection();"> 
			</td>
		</tr>	
		<tr><TD height="20"></TD></tr>		
		<tr>
			<td width="1" ></td>
			<td colspan="2" style="padding-left:5px;padding-right:5px;">
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<TD align="center">
						<table>
							<TR>
								<TD class="listing-item">
									<INPUT type="radio" name="editSelection" id="editSelection1"  class="chkBox" value="I">&nbsp;Individual
								</TD>
								<TD class="listing-item">
									<INPUT type="radio" name="editSelection" id="editSelection2" class="chkBox" value="G">&nbsp;Group
								</TD>
							</TR>
						</table>
					</TD>
				</tr>
				</table>
			</td>
	</tr>
	<tr><TD height="20"></TD></tr>
	<tr>
		<td  colspan="3" align="center">
			<input type="submit" name="cmdBack" onClick="this.form.action='DistMarginStructure.php';" class="button" value=" Go Back " >&nbsp;&nbsp;<input type="submit" name="cmdContinue" class="button" value=" Continue " onClick="return validateEditSelection();">
		</td>
	</tr>			
								<tr>
									<td colspan="3" height="5" ></td>
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
	</table>
		</td>
	</tr>
	</table>
		<?php
			include "template/boxBR.php"
		?>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<? 
	}
	?>
	<?
	if ($addMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		disableRows();
	</script>
	
	<?
	}
	?>
	<?php
		if ($editSelection=='I' && $editMode && $selProduct!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		getStateWiseVat();
		getOctroiPercent();
	</script>
	<?php
		}
	?>
	</form>
</td>
</tr></table>


<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>