<?php
	require("include/include.php");
	require_once("lib/purchaseorder_ajax.php");
	//require_once ('components/base/DocumentationInstructions_model.php');
	//$docInstructions_m = new DocumentationInstructions_model();

	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
		
	$dateSelection = "?frozenPackingFrom=".$p["frozenPackingFrom"]."&frozenPackingTill=".$p["frozenPackingTill"]."&pageNo=".$p["pageNo"];	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	

	//------------  Checking Access Control Level  ----------------
	$add=$edit=$del=$print=$confirm=$reEdit=false;	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}		
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	

	list($urlFnId, $urlModuleId, $urlSubModuleId) = $modulemanagerObj->getFunctionIds($currentUrl);	
	$rfrshTimeLimit = $refreshTimeLimitObj->getRefreshTimeLimit($urlSubModuleId,$urlFnId);
	$refreshTimeLimit = ($rfrshTimeLimit!=0)?$rfrshTimeLimit:60;	
	//----------------------------------------------------------	
	$cyRateListId=$cyCode=$cyValue = "";

	//$rec = $purchaseorderObj->qelMCPkg($frozencodeId=144);
	//echo "h=>".key($rec);
	//$frzRecs = $purchaseorderObj->qelFrzncode($fishId=33, $processCodeId=20);
	//printr($frzRecs);
	

	//echo "NPF==>".$proformaInvoiceNo 	= $purchaseorderObj->getNextProformaInvoiceNo();
	//echo "Next invoice==".$invNo = $purchaseorderObj->getNextInvoiceNo();
	# Gen Qel
	// $genQel = $purchaseorderObj->genQEL(21, 1);
	//$purchaseorderObj->getProductsInPO(19);
	
	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode = true;
		//$editSO = $purchaseorderObj->getNextInvoiceNo();	
		//$proformaInvoiceNo 	= $purchaseorderObj->getNextProformaInvoiceNo();
		//$sampleInvoiceNo	= $purchaseorderObj->getNextSampleInvoiceNo();
	}
	
	
	if ($p["cmdCancel"]!="") {	
		$addMode	= false;
		$editMode	= false;
		$editId		= "";
		$mainId		= "";
	}
	
	
	# Add New
	if ($p["cmdAdd"]!="") {
		$poNo		= $p["poNo"];
		$poDate		= mysqlDateFormat($p["poDate"]);
		$selCustomer		= $p["selCustomer"];
		$dischargePort		= $p["dischargePort"];
		$paymentTerms		= $p["paymentTerms"];
		$lastDate		= mysqlDateFormat($p["lastDate"]);
		$selectDate		= mysqlDateFormat($p["selectDate"]);
		$totalNumMC		= $p["totalNumMC"];
		$totalValUSD		= $p["totalValUSD"];
		$totalValINR		= $p["totalValINR"];
		$selCountry	= $p["selCountry"];
		$selPort	= $p["selPort"];
		$selAgent	= $p["selAgent"];
		
		$shipmentInstrs	= $p["shipmentInstrs"];
		$documentInstrs = $p["documentInstrs"];
		$surveyInstrs	= $p["surveyInstrs"];
		$commnPaymentInstrs = $p["commnPaymentInstrs"];			
		$varients		= $p["varients"];
		$selCarriageMode	= $p["selCarriageMode"];
		$otherBuyer		= addSlash(trim($p["otherBuyer"]));

		$currencyId			= $p["selCurrency"];
		$currencyRateListId	= $p["currencyRateListId"];

		$selUnit		= $p["selUnit"];

		$itemCount	  = $p["hidTableRowCount"];
		$chkListRowCount	= $p["chkListRowCount"];
		
		if ($poNo!="") {
			
			if ($selCustomer) {
				$purchaseOrderRecIns = $purchaseorderObj->addPurchaseOrder($selCustomer, $dischargePort, $paymentTerms, $lastDate, $selectDate, $totalNumMC, $totalValUSD, $totalValINR, $selCountry, $selPort, $selAgent, $poNo, $poDate, $shipmentInstrs, $documentInstrs, $surveyInstrs, $commnPaymentInstrs, $varients, $selCarriageMode, $otherBuyer, $userId, $currencyId, $currencyRateListId, $selUnit);
				
				if ($purchaseOrderRecIns) {
					#Find the Last inserted Id From Main Table
					$lastInsId = $databaseConnect->getLastInsertedId();

					for ($i=0; $i<$itemCount; $i++) {
						$status = $p["status_".$i];
						if ($status!='N') {
							$selFish	= $p["selFish_".$i];
							$selProcessCode	= $p["selProcessCode_".$i];
							$selEuCode	= $p["selEuCode_".$i];
							$sBrand		= explode("_",$p["selBrand_".$i]);
							$selBrand	= $sBrand[0];
							$brandFrom	= $sBrand[1];
							$selGrade	= $p["selGrade_".$i];
							$selFreezingStage = $p["selFreezingStage_".$i];
							$selFrozenCode	= $p["selFrozenCode_".$i];
							$selMCPacking	= $p["selMCPacking_".$i];
							$numMC		= $p["numMC_".$i];
							$pricePerKg	= $p["pricePerKg_".$i];
							$valueInUSD	= $p["valueInUSD_".$i];
							$valueInINR	= $p["valueInINR_".$i];
							$wtType		= $p["wtType_".$i];

							if ($lastInsId && $selFish && $selProcessCode) {
								$purchaseOrderRawEntryRecIns = $purchaseorderObj->addPurchaseOrderRawEntries($lastInsId, $selFish, $selProcessCode, $selEuCode, $selBrand, $selGrade, $selFreezingStage, $selFrozenCode, $selMCPacking, $numMC, $pricePerKg, $valueInUSD, $valueInINR, $brandFrom, $wtType);	
							}
						} // Status Check Ends here
					} // For Loop Ends

					# Doc Instruction Check List
					for($i=1; $i<=$chkListRowCount; $i++) {
						$chkListId  = $p["chkListId_".$i];
						if ($chkListId!="" && $lastInsId!=0) {
							$insDocChkListRecs = $purchaseorderObj->insertDocInstuctionsChkList($lastInsId, $chkListId);
						}
					} // chk List loop ends here	
				}
			} // Customer condition ends here
			
			if ($purchaseOrderRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddPurchaseOrder);
				$sessObj->createSession("nextPage",$url_afterAddPurchaseOrder);
			} else {
				$addMode = true;
				$err	 = $msg_failAddPurchaseOrder;
			}
			$purchaseOrderRecIns	= false;
		}
	}


	# Edit 
	if ($p["editId"]!="" ) {

		$editId			=	$p["editId"];
				
		$editMode		= true;		
		$purchaseOrderRec	= $purchaseorderObj->find($editId);
			
		//$purchaseOrderId	= $purchaseOrderRec[0];
		$mainId 		= $purchaseOrderRec[0];
		$selCustomerId		= $purchaseOrderRec[1];
		$dischargePort		= $purchaseOrderRec[2];
		$paymentTerms		= $purchaseOrderRec[3];
		$lastDate		= dateFormat($purchaseOrderRec[4]);
		$selectedDate	= $purchaseOrderRec[5];
		$selDate		= dateFormat($selectedDate);
		$totalNumMC		= $purchaseOrderRec[6];
		$totalValUSD		= $purchaseOrderRec[7];
		$totalValINR		= $purchaseOrderRec[8];
		$selCountry	= $purchaseOrderRec[9];
		$selPort	= $purchaseOrderRec[10];
		$selAgent	= $purchaseOrderRec[11];
		$poNo		= $purchaseOrderRec[12];
		$poDate		= ($purchaseOrderRec[13]!='0000-00-00')?dateFormat($purchaseOrderRec[13]):"";
		$shipmentInstrs	= $purchaseOrderRec[14];
		$documentInstrs = $purchaseOrderRec[15];
		$surveyInstrs	= $purchaseOrderRec[16];
		$commnPaymentInstrs = $purchaseOrderRec[17];
		$varients	= $purchaseOrderRec[18];
		$selCarriageMode	= $purchaseOrderRec[19];
		$otherBuyer			= stripSlash($purchaseOrderRec[20]);
		$selCurrencyId		= $purchaseOrderRec[21];	 
		$selUnit			= $purchaseOrderRec[23];	 

		list($cyRateListId, $cyCode, $cyValue) = $usdvalueObj->getCYRateList($selCurrencyId, $selectedDate);
		$oneUSD = ($cyValue!=0)?$cyValue:$oneUSD;

		# Get PO Entries item
		$poRawItemRecs = $purchaseorderObj->fetchAllPOItem($mainId);

		# Get Split invoice recs
		$splitInvoiceMainRecs = $purchaseorderObj->getSplitInvoiceMainRecs($mainId);		

		# get Selected ref chk list
		$chkListArr	= $purchaseorderObj->getSelChkListRecs($mainId);
	}

	# Update
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveAndConfirm"]) {
			
		$poCompleted = false;	
		if ($p["cmdSaveAndConfirm"]!="") $poCompleted = true;

		$completedStatus = ($poCompleted)?'C':"";

		$poMainId 	=	$p["mainId"];
		$selCustomer		= $p["selCustomer"];
		$dischargePort		= $p["dischargePort"];
		$paymentTerms		= $p["paymentTerms"];
		$lastDate		= mysqlDateFormat($p["lastDate"]);
		$selectDate		= mysqlDateFormat($p["selectDate"]);
		$totalNumMC		= $p["totalNumMC"];
		$totalValUSD		= $p["totalValUSD"];
		$totalValINR		= $p["totalValINR"];
		$selCountry	= $p["selCountry"];
		$selPort	= $p["selPort"];
		$selAgent	= $p["selAgent"];
		$poNo		= $p["poNo"];
		$poDate		= mysqlDateFormat($p["poDate"]);
		$shipmentInstrs	= $p["shipmentInstrs"];
		$documentInstrs = $p["documentInstrs"];
		$surveyInstrs	= $p["surveyInstrs"];
		$commnPaymentInstrs = $p["commnPaymentInstrs"];	
		$varients	= $p["varients"];
		$selCarriageMode	= $p["selCarriageMode"];
		$otherBuyer		= addSlash(trim($p["otherBuyer"]));
		$currencyId			= $p["selCurrency"];
		$currencyRateListId	= $p["currencyRateListId"];
		$selUnit			= $p["selUnit"];
		$itemCount	  = $p["hidTableRowCount"];
		$splitTbleRowCount	= $p["splitTbleRowCount"];
		$chkListRowCount	= $p["chkListRowCount"];
		
		
		if ($poNo) {
		
			if ($selCustomer) {

				#Main table Updation
				$purchaseOrderMainUpdted = $purchaseorderObj->updatePurchaseOrderMain($poMainId, $selCustomer, $dischargePort, $paymentTerms, $lastDate, $selectDate, $totalNumMC, $totalValUSD, $totalValINR, $selCountry, $selPort, $selAgent, $poNo, $poDate, $shipmentInstrs, $documentInstrs, $surveyInstrs, $commnPaymentInstrs, $varients, $userId, $completedStatus, $selCarriageMode, $otherBuyer, $currencyId, $currencyRateListId, $selUnit);

				if ($purchaseOrderMainUpdted) {
					for ($i=0; $i<$itemCount; $i++) {
						$status 	= $p["status_".$i];
						$poEntryId	= $p["poEntryId_".$i];

						if ($status!='N') {
							$selFish	= $p["selFish_".$i];
							$selProcessCode	= $p["selProcessCode_".$i];
							$selEuCode	= $p["selEuCode_".$i];
							$sBrand		= explode("_",$p["selBrand_".$i]);
							$selBrand	= $sBrand[0];
							$brandFrom	= $sBrand[1];

							$selGrade	= $p["selGrade_".$i];
							$selFreezingStage = $p["selFreezingStage_".$i];
							$selFrozenCode	= $p["selFrozenCode_".$i];
							$selMCPacking	= $p["selMCPacking_".$i];
							$numMC		= $p["numMC_".$i];
							$pricePerKg	= $p["pricePerKg_".$i];
							$valueInUSD	= $p["valueInUSD_".$i];
							$valueInINR	= $p["valueInINR_".$i];
						

							if ($poMainId && $selFish && $selProcessCode && $poEntryId=="") {
								$purchaseOrderRawEntryRecIns = $purchaseorderObj->addPurchaseOrderRawEntries($poMainId, $selFish, $selProcessCode, $selEuCode, $selBrand, $selGrade, $selFreezingStage, $selFrozenCode, $selMCPacking, $numMC, $pricePerKg, $valueInUSD, $valueInINR, $brandFrom);	
							} else if ($poMainId && $selFish && $selProcessCode && $poEntryId!="") {
								$updatePORawEntry	= $purchaseorderObj->updatePORawEntries($poEntryId, $selFish, $selProcessCode, $selEuCode, $selBrand, $selGrade, $selFreezingStage, $selFrozenCode, $selMCPacking, $numMC, $pricePerKg, $valueInUSD, $valueInINR, $brandFrom);
							}
						} // Status Check Ends here
						else if ($status=='N' && $poEntryId!="") {
							$delPORawEntry = $purchaseorderObj->deletePORawEntry($poEntryId);
						}
					} // For Loop Ends

					# Split Invoice Section --------
					for ($i=0; $i<$splitTbleRowCount; $i++) {
						$spoStatus 	= $p["spoStatus_".$i];
						$selInvoiceId	= $p["hidInvoiceId_".$i];
						
						if ($spoStatus!='N') {
							$spoInvoiceTypeId 	= $p["invoiceType_".$i];
							$eucode=$p["selEuCode_".$i];
							$spoSampleInvoiceNo	= $p["sampleInvoiceNo_".$i];
							$spoProformaInvoiceNo	= $p["proformaInvoiceNo_".$i];
							$spoInvoiceNo		= $p["invoiceNo_".$i];
							$spoInvoiceDate		= mysqlDateFormat($p["entryDate_".$i]);					
							$spoEntryDate		= mysqlDateFormat($p["entryDate_".$i]);
							$spoInvoiceType = $purchaseorderObj->getInvoiceType($spoInvoiceTypeId);
							
							if ($spoInvoiceType && ($spoProformaInvoiceNo || $spoSampleInvoiceNo)) {
	
								if ($selInvoiceId=="") {
									$insertSplitInvoiceRec  = $purchaseorderObj->insertSplitInvoiceRec($poMainId, $selCustomer, $spoInvoiceType, $spoSampleInvoiceNo, $spoProformaInvoiceNo, $spoInvoiceNo, $spoInvoiceDate, $spoEntryDate, $spoInvoiceTypeId, $userId,$eucode);
								} else if ($selInvoiceId!="") {
									$updateSplitInvoiceRec = $purchaseorderObj->updateSplitInvoiceRec($selInvoiceId, $poMainId, $selCustomer, $spoInvoiceType, $spoSampleInvoiceNo, $spoProformaInvoiceNo, $spoInvoiceNo, $spoInvoiceDate, $spoEntryDate, $spoInvoiceTypeId, $userId,$eucode);
								}
	
								#Find the Last inserted Id From invoice Main Table
								$invoiceMainId = "";
								if ($insertSplitInvoiceRec && $selInvoiceId=="") $invoiceMainId = $databaseConnect->getLastInsertedId();
		
								for ($j=0; $j<$itemCount; $j++) {	
	
									$sPOEntryId	= $p["hidPOEntryId_".$j."_".$i];
									//$selPCId	= $p["selPCId_".$j."_".$i];
									$MCInPO		= $p["MCInPO_".$j."_".$i];

									//echo "----$MCInPO";
									$splitNumMC	= $p["MCInInv_".$j."_".$i];
									$invoiceEntryId	= $p["hidInvoiceEntryId_".$j."_".$i];
									$pricePerKg	= $p["pricePerKg_".$j."_".$i];
									$valueInUSD	= $p["valueInUSD_".$j."_".$i];
									$valueInINR	= $p["valueInINR_".$j."_".$i];
									
									
									if ($invoiceMainId && $splitNumMC!=0 && $invoiceEntryId=="") {
										$insertSPOEntries = $purchaseorderObj->insertSplitInvEntries($invoiceMainId, $sPOEntryId, $MCInPO, $splitNumMC, $pricePerKg, $valueInUSD, $valueInINR);
									} else if ($splitNumMC!=0 && $invoiceEntryId!="") {
										$updateSPOEntries = $purchaseorderObj->updateSplitInvEntries($invoiceEntryId, $MCInPO, $splitNumMC, $pricePerKg, $valueInUSD, $valueInINR);
									}
									
								} // Split Row PC Ends Here
							} // Check PO no/Sample No ends here
						} // Status check ends here
						else if ($spoStatus=='N' && $selInvoiceId!="") {
							# Delete All Invoice Entry
							$delSplitInvoiceRec = $purchaseorderObj->deleteSplitInvoiceRec($selInvoiceId);
						}

					} // Split PO Loop ends here
					
				}
			} // Condition ends here

			# Doc Instruction Check List
			# Delete Sel chk list
			$deleteSelChkList = $purchaseorderObj->delChkList($poMainId);

			for($i=1; $i<=$chkListRowCount; $i++) {
				$chkListId  = $p["chkListId_".$i];
				if ($chkListId!="" && $poMainId!=0) {
					$insDocChkListRecs = $purchaseorderObj->insertDocInstuctionsChkList($poMainId, $chkListId);
				}
			} // chk List loop ends here
		} // If main Condition Ends here
	
		if ($purchaseOrderMainUpdted) {
			$sessObj->createSession("displayMsg",$msg_succUpdatePurchaseOrder);
			$sessObj->createSession("nextPage",$url_afterUpdatePurchaseOrder.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdatePurchaseOrder;
		}
		$purchaseOrderRecUptd	=	false;
	}
	

	# Delete 
	if ($p["cmdDelete"]!="") 
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++)
		{
			$poMainId		=	$p["delId_".$i];
			if($poMainId!="") 
			{
				$delPo=$purchaseorderObj->checkPurchaseOrderAllocation($poMainId);
				//echo "hiii";
				if($delPo)
				{
					//echo "hui";
					//die();
					# Delete Sel chk list
					$deleteSelChkList = $purchaseorderObj->delChkList($poMainId);
					$purchaseOrderRMEntryRecDel = $purchaseorderObj->deleteRMEntry($poMainId);					
					$purchaseOrderMainRecDel    = $purchaseorderObj->deletePurchaseOrderMainRec($poMainId);	
				}
			}
		}
		if ($purchaseOrderMainRecDel || $purchaseOrderGradeEntryRecDel || $purchaseOrderRMEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPurchaseOrder);
			$sessObj->createSession("nextPage",$url_afterDelPurchaseOrder.$dateSelection);
		} else {
			$errDel	=	$msg_failDelPurchaseOrder;
		}
		$purchaseOrderMainRecDel	=	false;
	}
		
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------
	

	# select records between selected date
	if($g["frozenPackingFrom"]!="" && $g["frozenPackingTill"]!="") {
		$dateFrom = $g["frozenPackingFrom"];
		$dateTill = $g["frozenPackingTill"];
	} else if ($p["frozenPackingFrom"]!="" && $p["frozenPackingTill"]!="") {
		$dateFrom = $p["frozenPackingFrom"];
		$dateTill = $p["frozenPackingTill"];
	} else {
		//$dateFrom = date("d/m/Y");
		//$dateTill = date("d/m/Y");
		$dateC	   =	explode("/", date("d/m/Y"));
		$dateFrom   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],1,$dateC[2]));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1], date('t'), $dateC[2]));	
	}


	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		
		$purchaseOrderRecords = $purchaseorderObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);

		# Fetch All Recs
		$fetchAllPORecords		= $purchaseorderObj->fetchAllRecords($fromDate, $tillDate);
		$purchaseOrderRecordSize	= sizeof($fetchAllPORecords);
	}
	

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllPORecords);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) { 
		#List All Fishes
		//$fishMasterRecords	=	$fishmasterObj->fetchAllRecords();
		$fishMasterRecords	=	$fishmasterObj->fetchAllRecordsFishactive();
		#List All Freezing Stage Record
		//$freezingStageRecords	= $freezingstageObj->fetchAllRecords();
		$freezingStageRecords	= $freezingstageObj->fetchAllRecordsActivefreezingstage();
		#List All EU Code Records
			//$euCodeRecords		= $eucodeObj->fetchAllRecords();
			$euCodeRecords		= $eucodeObj->fetchAllRecordsActiveEucode();
		#List All Brand Records
		//	$brandRecords		= $brandObj->fetchAllRecords();
			
		#List All Frozen Code Records
			//$frozenPackingRecords	= $frozenpackingObj->fetchAllRecords();
			
		#List All MC Packing Records
			//$mcpackingRecords	= $mcpackingObj->fetchAllRecords();
		
		#List All Customer Records
			//$customerRecords	= $customerObj->fetchAllRecords();
			$customerRecords	= $customerObj->fetchAllRecordsActiveCustomer();
		#List All payment Terms Record
			$paymentTermRecords	= $paymenttermsObj->fetchAllRecords();
			//$paymentTermRecs	= $paymenttermsObj->fetchAllRecordsActivePayment();

		# List All Country
			//$countryMasterRecs	= $countryMasterObj->fetchAllRecords(); 
		$countryMasterRecs	= $countryMasterObj->fetchAllRecordsActivecountry();
		# Get Invoice Type Recs
		//$invoiceTypeMasterRecs	= $invoiceTypeMasterObj->fetchAllRecords();
		$invoiceTypeMasterRecs	= $invoiceTypeMasterObj->fetchAllRecordsActiveinvoice();
		# List Carriage Mode Recs
		//$carriageModeRecs = $carriageModeObj->fetchAllRecords();
		$carriageModeRecs = $carriageModeObj->fetchAllRecordsActivecarriagemode();
		# Document Instruction check list
		$docInstructionsChkList =$docInstructionsObj->findAll();

		# Get All Currency Recs
		$currencyRecs	= $usdvalueObj->getCYRecs();
		
		/*
		# Get Purchase Order Unit Recs 
		$spoUnitRecs = array("1"=>"Kgs","2"=>"Lbs");
		*/
	}

	if ($editMode) {
		# get Brand Recs
		$brandRecs     = $purchaseorderObj->getBrandRecords($selCustomerId);
		# Get Agent
		$agentRecs = $purchaseorderObj->getAgentRecs($selCustomerId);
		# Port List
		$portRecs = $purchaseorderObj->getPortRecs($selCountry);
		# Payment terms
		$paymentTermRecs = $purchaseorderObj->getPaymentTermRecs($selCustomerId);
	}

	/* Wt Type array */
	$wtTypeArr = array("FW"=>"Frozen Wt", "NW"=>"Net Wt");

	if ($editMode)	$heading = $label_editPurchaseOrder;
	else 		$heading = $label_addPurchaseOrder;
	
	
	# Setting the mode
	$mode = "";
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/purchaseorder.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<div id="spo-filter"></div>
<div id="spo-box">
  <iframe width="95%" height="400" id="addNewIFrame" src="" style="border:none;" frameborder="0"></iframe>	
  <p align="center"> 
      <input type="button" name="cancel" value="Close" onClick="closeLightBox()">
  </p>
</div>
<form name="frmPurchaseOrder" id="frmPurchaseOrder" action="PurchaseOrders.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">	
	<? if($err!="" ){?>
	<tr>
		<td height="20" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
		<?php
			if ($editMode || $addMode) {
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center" style="display:none;">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrders.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePurchaseOrder(document.frmPurchaseOrder, '');">
												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrders.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Save &amp; Exit" onClick="return validatePurchaseOrder(document.frmPurchaseOrder, '');"></td>
<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<input type="hidden" name="hidPurchaseOrderId" value="<?=$purchaseOrderId;?>">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
										<tr>
											  <td colspan="2" height="10"></td>
										  </tr>											
	<!-- 	Display Error Message -->
		<tr><TD class="listing-item" style='line-height:normal; font-size:10px; color:red;' id="divNumExistTxt" nowrap="true" align="center" colspan="2"></TD></tr>
	<tr>
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table>
	<TR>
		<TD>
		<table>
		<TR>
			<TD valign="top">
			<fieldset>
			<table>
					<!--<TR>
						<TD class="fieldName" nowrap="nowrap">Invoice Type</TD>
						<td>
							<select name="invoiceType" id="invoiceType" onchange="showInvoiceType();showInvRow('<?=$mode?>', '<?=$proformaInvoiceNo?>', '<?=$sampleInvoiceNo?>');">
								<option value="T" <? if ($invoiceType=='T') echo "Selected";?> >Taxable</option>
								<option value="S" <? if ($invoiceType=='S') echo "Selected";?> >Sample</option>
							</select>
						</td>
					</TR>
					<tr id="sampleInvNoRow">
						<td class="fieldName" nowrap="true">*Sample Invoice No.</td>
						<td nowrap="true">
							<input type="text" name="sampleInvoiceNo" id="sampleInvoiceNo" size="6" onkeyup="xajax_chkSampleNoExist(document.getElementById('sampleInvoiceNo').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('invoiceDate').value);" value="<?=($sampleInvoiceNo!=0)?$sampleInvoiceNo:"";?>">
							<?php
								if ($sampleInvoiceDate=="") $sampleInvoiceDate = date("d/m/Y");
							?>
							<input type="hidden" name="sampleInvoiceDate" id="sampleInvoiceDate" size="8" value="<?=$sampleInvoiceDate?>">
						</td>
					</tr>
					<tr id="proformaInvNoRow">
						<td class="fieldName" nowrap="true">*Proforma No.</td>
						<td nowrap="true">
							<input type="text" name="proformaInvoiceNo" id="proformaInvoiceNo" size="6" value="<?=($proformaInvoiceNo!=0)?$proformaInvoiceNo:""?>" onkeyup="xajax_chkProformaNoExist(document.getElementById('proformaInvoiceNo').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('invoiceDate').value);">
							<?php
								if ($proformaInvoiceDate=="") $proformaInvoiceDate = date("d/m/Y");
							?>
							<input type="hidden" name="proformaInvoiceDate" id="proformaInvoiceDate" size="8" value="<?=$proformaInvoiceDate?>">
						</td>
					</tr>-->
					<?php if ($addMode) {?>
						<!--<input name="invoiceNo" id="invoiceNo" type="hidden" size="6"  value="<?=$editSO?>" autocomplete="off" />
						<?php
							if ($invoiceDate=="") $invoiceDate = date("d/m/Y");
						?>
						<input type="hidden" name="invoiceDate" id="invoiceDate" value="<?=$invoiceDate?>" size="8" autocomplete="off"/>-->
					<?php }?>
		<TR>
					<td class="fieldName" nowrap="true">*PO No.</td>
					<td nowrap="true">
						<input name="poNo" id="poNo" type="text" size="12" value="<?=$poNo?>" onKeyUp="xajax_chkPONumberExist(document.getElementById('poNo').value,'<?=$mode?>','<?=$mainId?>');" autocomplete="off" />
						<br/><span id="divPOIdExistTxt" class="listing-item" style="line-height:normal; font-size:10px; color:red;"></span>
					</td>
				</TR>
				<TR>
					<td class="fieldName" nowrap="true">*PO Date</td>
					<td nowrap="true">
						<input type="text" name="poDate" id="poDate" value="<?=$poDate?>" size="8" autocomplete="off"/>
					</td>
				</TR>
		<tr>
        	                 <td class="fieldName" nowrap="nowrap" style="line-height:normal;">
					*Customer<br/>
				<span><a href="###" class="link1" title="Click to add New Customer." onClick="return printWindow('Customer.php?popupWindow=1',700,600);" onMouseover="ShowTip('Click to add New Customer.');" onMouseout="UnTip();">New Customer</a></span>
				</td>
                		 <td>
					  <select name="selCustomer" id="selCustomer" onchange="xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value); xajax_getAgentList(document.getElementById('selCustomer').value, ''); xajax_getCustPaymentTerm(document.getElementById('selCustomer').value, '');">
					  <option value="">--Select--</option>
					  <?php
						foreach($customerRecords as $cr) {
							$customerId		=	$cr[0];
							$customerCode	=	$cr[1];
							$customerName	=	stripSlash($cr[2]);
							$selected 	=	"";
							if ($selCustomerId==$customerId) $selected = "Selected";
					?>
					<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
						<? }?>
                                         </select>
				 </td>
                            </tr>
				<tr>
					<td class="fieldName" nowrap="nowrap">*Country</td>
					<td nowrap="true">
						<select name="selCountry" id="selCountry" onchange="xajax_getPortList(document.getElementById('selCountry').value, '');">
					  	<option value="">--Select--</option>
					  	<?php
						foreach($countryMasterRecs as $cmr) {
							$countryId 	= $cmr[0];	
							$cntryName	= $cmr[1];
							$selected = ($selCountry==$countryId)?"Selected":"";
						?>
						<option value="<?=$countryId?>" <?=$selected?>><?=$cntryName?></option>
						<?php
							 }
						?>
                                         </select>
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap="nowrap">*Port</td>
					<td nowrap="true">
					<select name="selPort" id="selPort">
						<? if (!sizeof($portRecs)) {?><option value="">--Select--</option><? }?>
							<?php
								foreach ($portRecs as $portId=>$portName) {
									$selectedPort = ($selPort==$portId)?"selected":""; 
							?>
								<option value="<?=$portId?>" <?=$selectedPort?>><?=$portName?></option>
							<?php
								}
							?>
                                        </select>
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap="nowrap" style="line-height:normal;">*Agent
					<br/>
				<span>
					<a href="###" class="link1" title="Click to add New Agent." onClick="return printWindow('AgentMaster.php?popupWindow=1',700,600);" onMouseover="ShowTip('Click to add New Agent.');" onMouseout="UnTip();">New Agent</a>
				</span>
					</td>
					<td nowrap="true">
						<select name="selAgent" id="selAgent">
					  		<? if (!sizeof($agentRecs)) {?><option value="">--Select--</option><? }?>
							<?php
								foreach ($agentRecs as $agentId=>$agentName) {
									$selectedAgent = ($selAgent==$agentId)?"selected":""; 
							?>
								<option value="<?=$agentId?>" <?=$selectedAgent?>><?=$agentName?></option>
							<?php
								}
							?>
                                         	</select>
					</td>
				</tr>
				</table>
			</fieldset>
			</TD>
			<td>&nbsp;</td>
			<TD valign="top">
			<fieldset>
			<table>
				
			<tr>
                               <td class="fieldName" nowrap="nowrap">*Port of discharge</td>
				<td>
					  <input name="dischargePort" type="text" id="dischargePort" size="25" value="<?=$dischargePort?>">
				</td>
                         </tr>
                         <tr>
                              <td class="fieldName" nowrap="nowrap">*Payment Terms</td>
                              <td>
				  <select name="paymentTerms" id="paymentTerms">
						<? if (!sizeof($paymentTermRecs)) {?><option value="">--Select--</option><? }?>
						<?php
							foreach ($paymentTermRecs as $paymentTermId=>$paymentTerm) {
								$selectedPTerm = ($paymentTerms==$paymentTermId)?"selected":""; 
						?>
							<option value="<?=$paymentTermId?>" <?=$selectedPTerm?>><?=$paymentTerm?></option>
						<?php
							}
						?>
                                    </select>
				</td>
                               </tr>				
				<?php
						// if ($addMode) {
					?>
					<tr>
						<TD class="fieldName" nowrap="nowrap">*Last Date for Shipment</TD>
						 <td>
							<? if($p["lastDate"]!="") $lastDate=$p["lastDate"];?>						
							<input type="text" id="lastDate" name="lastDate" size="8" autocomplete="off" value="<?=$lastDate?>" onchange="xajax_chkValidDespatchDate(document.getElementById('lastDate').value);" />

							<input type="hidden" name="lastDateStatus" id="lastDateStatus" value="<?=$lastDate?>" />
							<input type="hidden" name="hideLastDate" id="hideLastDate" value="<?=$lastDate?>">
							<input type="hidden" name="validDespatchDate" id="validDespatchDate" value="<?=$lastDate?>" />

							<input name="dateExtended" type="hidden" id="dateExtended" value="">				
						</td>
					</tr>
					<?php //}?>
					<tr>
						<TD class="fieldName" nowrap="nowrap">*Order Entry Date</TD>
						 <td>
							<?php
								if ($selDate=="") $selDate = date("d/m/Y");
							?>
							 <input type="text" id="selectDate" name="selectDate" size="8" value="<?=$selDate?>" >
						</td>
					</tr>
					<tr>
						<TD class="fieldName" nowrap="nowrap">Varients</TD>
						 <td class="listing-item" nowrap="true">
							 <input type="text" id="varients" name="varients" size="3" value="<?=$varients?>" style="text-align:right;" <? if ($editMode) {?> onkeyup="chkMCQty();" <? }?> >&nbsp;%
						</td>
					</tr>
					<tr>
						<TD class="fieldName" nowrap="nowrap">Mode of carriage</TD>
						 <td class="listing-item" nowrap="true">
							<select name="selCarriageMode" id="selCarriageMode">
								<option value="">--Select--</option>
								<?php
									foreach($carriageModeRecs as $cmr) {
										$carriageModeId	 = $cmr[0];
										$carriageModeName = $cmr[1];
										$defaultMode = $cmr[2];
							
										$selectedMode = ($selCarriageMode==$carriageModeId || (!$selCarriageMode && $defaultMode=='Y'))?"Selected":"";
								?>
								<option value="<?=$carriageModeId?>" <?=$selectedMode?>><?=$carriageModeName?></option>
								<? }?>
							</select>
						</td>
					</tr>
					<tr>
						<TD class="fieldName" nowrap="nowrap">*Currency</TD>
						 <td class="listing-item" nowrap="true">
							<select name="selCurrency" id="selCurrency" onchange="setCurrency();">
								<option value="">--Select--</option>
								<?php
									foreach($currencyRecs as $cr) {
										$currencyId		= $cr[0];
										$currencyCode	= $cr[1];
										$selected	= ($selCurrencyId==$currencyId)?"Selected":"";
								?>
								<option value="<?=$currencyId?>" <?=$selected?>><?=$currencyCode?></option>
								<? }?>
							</select>
						</td>
					</tr>
					<tr>
						<TD class="fieldName" nowrap="nowrap">*Unit</TD>
						 <td class="listing-item" nowrap="true">
							<select name="selUnit" id="selUnit">
								<!--option value="">--Select--</option-->
								<?php
									foreach($spoUnitRecs as $unitId=>$unitVal) {										
										$selected	= ($selUnit==$unitId)?"Selected":"";
								?>
								<option value="<?=$unitId?>" <?=$selected?>><?=$unitVal?></option>
								<? }?>
							</select>
						</td>
					</tr>
			</table>
			</fieldset>
			</TD>
			<td>&nbsp;</td>
			<TD valign="top">
			<fieldset>
			<table>
				<tr>
					<TD class="fieldName" nowrap="nowrap">Shipment Instructions</TD>
					<td>
						 <textarea name="shipmentInstrs" id="shipmentInstrs" rows="3" style="width:180px;"><?=$shipmentInstrs?></textarea>
					</td>
				</tr>
				<tr>
					<TD colspan="2" nowrap="true">
						<table>
							<TR>
								<TD valign="top">
									<table>
									<tr>
										<TD class="fieldName" nowrap="nowrap">Document Instructions</TD>
										<td>
											<textarea name="documentInstrs" rows="3" style="width:180px;"><?=$documentInstrs?></textarea>
										</td>
									</tr>
									</table>
								</TD>
								<TD valign="top">
									<table>
										<TR><TD>
										<fieldset>
										<legend class="listing-item">Check List</legend>
										<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
										<?php
										if (sizeof($docInstructionsChkList)) {
										?>
										<TR bgcolor="#f2f2f2" align="center">
											<TD>&nbsp;</TD>
											<TD class="listing-head" nowrap>Check List</TD>
										</TR>
										<?php
										$rc = 0;
										foreach ($docInstructionsChkList as $clr) {
											$rc++;
											$chked = (in_array($clr[0],$chkListArr))?"checked":"";
										?>
										<TR bgcolor="White">
											<TD>
												<INPUT type='checkbox' name='chkListId_<?=$rc?>' id='chkListId_<?=$rc?>' class="chkBox" value="<?=$clr[0]?>" <?=$chked?> />
												<INPUT type='hidden' name='required_<?=$rc?>' id='required_<?=$rc?>' value="<?=$clr[2]?>" readonly />
												<INPUT type='hidden' name='chkListName_<?=$rc?>' id='chkListName_<?=$rc?>' value="<?=$clr[1]?>" readonly />	
											</TD>
											<TD class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
												<?php if ($clr[2]=='Y') echo "*";?><?=$clr[1]?>
											</TD>
										</TR>
										<?php
										} // Chk List Recs Ends here
										?>
										<?php
											} else {
										?>
										<tr bgcolor="White"><TD class="err1" nowrap="true">No Records</TD></tr>
										<?php
											}
										?>
									</table>
										</fieldset>
										</TD>
								<input type="hidden" name="chkListRowCount" id="chkListRowCount" value="<?=$rc?>" readonly />
										</TR>
									</table>
								</TD>
							</TR>
						</table>
					</TD>
				</tr>			
				<!--<tr>
					<TD class="fieldName" nowrap="nowrap">Document Instructions</TD>
					<td>
						 <textarea name="documentInstrs" rows="3" style="width:180px;"><?=$documentInstrs?></textarea>
					</td>
				</tr>-->
				
				<tr>
					<TD class="fieldName" nowrap="nowrap">Third Party Survey Instructions</TD>
					<td>
						 <textarea name="surveyInstrs" rows="3" style="width:180px;"><?=$surveyInstrs?></textarea>
					</td>
				</tr>
				
				<tr>
					<TD class="fieldName" nowrap="nowrap">Common Payment Instructions</TD>
					<td>
						 <textarea name="commnPaymentInstrs" rows="3" style="width:180px;"><?=$commnPaymentInstrs?></textarea>
					</td>
				</tr>
				<tr>
					<TD class="fieldName" nowrap="nowrap">Buyer (if other than Consignee)</TD>
					<td>
						 <textarea name="otherBuyer" id="otherBuyer" rows="3" style="width:180px;"><?=$otherBuyer?></textarea>
					</td>
				</tr>
			</table>
			</fieldset>
			</TD>
			<!--<td valign="top">
				<table>
				
				</table>
			</td>-->
		</ta>
		</table>
		</TD>
	</TR>
	<TR><TD>&nbsp;</TD></TR>
	</table>
	</td>
	</tr>
	<tr><TD height="5"></TD></tr>
	<tr>
	<td valign="top">
	<table>
		<tr>
	<TD style="padding-left:10px;padding-right:10px;">
		<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblPOItem">
	                <tr bgcolor="#f2f2f2" align="center">
                                <td class="listing-head">Sr.<br>No</td> 
				<td class="listing-head">Fish</td>
				<td class="listing-head">Process Code</td>				
				<!--<td class="listing-head">EU Code</td>-->
				<td class="listing-head">Brand</td>
				<td class="listing-head">							
					Grade
					<br>
					<span><a href="###" class="link1" title="Click to add New Frozen Grade." onClick="loadFrznGrade();" onMouseover="ShowTip('Click to add New Frozen Grade.');" onMouseout="UnTip();">New Grade</a></span>
				</td>
				<td class="listing-head">Freezing Stage</td>
				<td class="listing-head">
					Frozen Code
					<br>
					<span><a href="###" class="link1" title="Click to add New Frozen Packing Code." onClick="loadFrznPkgCode();" onMouseover="ShowTip('Click to add New Frozen Packing Code.');" onMouseout="UnTip();">New Frozen Code</a></span>
				</td>	
				<td class="listing-head">
					MC Pkg
					<br>
					<span><a href="###" class="link1" title="Click to add New MC Pkg." onClick="loadMCPkg();" onMouseover="ShowTip('Click to add New MC Pkg.');" onMouseout="UnTip();">New MC Pkg</a></span>
				</td>
				 <td class="listing-head" title="Weight Type">Wt Type</td>
                 <td class="listing-head">No of MC</td>
				<td class="listing-head">Total Wt<br>(<span class="replaceUnitTxt">Kg</span>)</td>
				 <td class="listing-head">Price per <span class="replaceUnitTxt">Kg</span> in <span class="replaceCY">USD</span></td>
				 <td class="listing-head">Value in <span class="replaceCY">USD</span></td>
				 <td class="listing-head">Value in INR</td>
				<?php
				if ($editMode) {
				?>
				<td class="listing-head">Available MC</td>
				<?php
				} 
				?>
				<td>&nbsp;</td>
                        </tr>
	<?php
		// When Edit Mode Products are loading from Top function
		$totalAmount = 0;
		$k = 0;
		$rawItemSize = sizeof($poRawItemRecs);
		foreach ($poRawItemRecs as $rec) {
			$poEntryId = $rec[0];
			$selFishId = $rec[1];
			$selProcessCodeId = $rec[2];
			//$selEuCodeId	  = $rec[3];

			//$selBrd		  = $rec[4];
			$selBrd		  = $rec[3];
			//$selBrdFrom	  = $rec[13];
			$selBrdFrom	  = $rec[12];
			$selBrandId	  = $selBrd."_".$selBrdFrom;

			/*$selGradeId	  = $rec[5];
			$selFreezingStageId = $rec[6];
			$selFrozenCodeId  = $rec[7];
			$selMCPackingId	  = $rec[8];
			$numMC		= $rec[9];*/
			$selGradeId	  = $rec[4];
			$selFreezingStageId = $rec[5];
			$selFrozenCodeId  = $rec[6];
			$selMCPackingId	  = $rec[7];
			$numMC		= $rec[8];
			$pricePerKg	= $rec[9];
			$valueInUSD	= $rec[10];
			$valueInINR	= $rec[11];
			$wtType		= $rec[13];

			# PC Recs
			$pcRecs		= $processcodeObj->getProcessCodeRecs($selFishId);
			
			# Grade Recs
			$gradeRecs	= $purchaseorderObj->getFrozenGradeRecs($selProcessCodeId);

			# Filled Wt
			list($filledWt, $declaredWt, $frznCodeUnit) = $purchaseorderObj->getFrznPkgFilledWt($selFrozenCodeId);

			# Get Num of Packs
			$numPacks  = $purchaseorderObj->numOfPacks($selMCPackingId);

			# Get Num of Available MC
			
			$availableMC	= $purchaseorderObj->chkFrznPkngMCExist($poEntryId);
			//$availableMC	= $purchaseorderObj->chkFrznPkngMCExist($mainId, $selFishId, $selProcessCodeId, $selGradeId);

			# QEL Wise Recs
			$frznCodeRecs = $purchaseorderObj->qelFrzncode($selFishId, $selProcessCodeId);
	
			# QEL MC Pkg
			$mcPkgRecs = $purchaseorderObj->qelMCPkg($selFrozenCodeId);

			$selPrdWt = ($wtType=='NW')?$declaredWt:$filledWt;
			$selPrdWt = ($selUnit==2 && $frznCodeUnit=='Kg')?number_format((KG2LBS*$selPrdWt),3,'.',''):$selPrdWt;
			$prdWt	  = $selPrdWt*$numPacks*$numMC;
			$poItems=$selProcessCodeId.'_'.$selBrd.'_'.$selGradeId.'_'.$selFreezingStageId.'_'.$selFrozenCodeId.'_'.$selMCPackingId;
			//$poItems=$selGradeId;
			//echo $poItems.'<br/>';

		?>
<tr align="center" class="whiteRow" id="row_<?=$k?>">
      <td align="center" id="srNo_<?=$k?>" class="listing-item">
         <?=$k+1?> 
      </td>
      <td align="center" class="listing-item">
        <select id="selFish_<?=$k?>" name="selFish_<?=$k?>" onchange="xajax_getProcessCodes(document.getElementById('selFish_<?=$k?>').value, '<?=$k?>', ''); xajax_getFrznCodes('<?=$k?>', document.getElementById('selFish_<?=$k?>').value, '', '');" >
		<option value="">--Select--</option>
		<?php
		if (sizeof($fishMasterRecords)>0) {	
			foreach($fishMasterRecords as $fr) {
				$fishId		= $fr[0];
				$fishName	= stripSlash($fr[1]);
				$selFR = ($selFishId==$fishId)?"selected":"";
		?>
		<option value="<?=$fishId?>" <?=$selFR?>><?=$fishName?></option>	
		<?php
				}
			}
		?>
	</select>
      </td>
      <td align="center" class="listing-item">
        <select id="selProcessCode_<?=$k?>" name="selProcessCode_<?=$k?>" onchange="xajax_getGradeRecs(document.getElementById('selProcessCode_<?=$k?>').value, '<?=$k?>', ''); xajax_getFrznCodes('<?=$k?>', document.getElementById('selFish_<?=$k?>').value, document.getElementById('selProcessCode_<?=$k?>').value, '');" >
		<?php
		if (!sizeof($pcRecs)) {
		?>
		<option value="">-- Select --</option>
		<?php
		}
		?>
		<?php
			foreach ($pcRecs as $pcrId=>$pCode) {
				$selPC = ($selProcessCodeId==$pcrId)?"selected":"";
		?>
			<option value="<?=$pcrId?>" <?=$selPC?>><?=$pCode?></option>
		<?php
			}
		?>
	</select>
      </td>
     <!-- <td align="center" class="listing-item">
        <select id="selEuCode_<?=$k?>" name="selEuCode_<?=$k?>">
		<option value="">--Select--</option>
		<?php
		if (sizeof($euCodeRecords)>0) {	
			foreach($euCodeRecords as $eucr) {
				$euCodeId	= $eucr[0];
				$euCode		= stripSlash($eucr[1]);
				$selEUR = ($selEuCodeId==$euCodeId)?"selected":"";
		?>	
			<option value="<?=$euCodeId?>" <?=$selEUR?>><?=$euCode?></option>	
		<?php
				}
			}
		?>
	</select>
      </td>-->
      <td nowrap="" align="center" class="listing-item">
        <select onchange="xajax_assignBrand(document.getElementById('selBrand_<?=$k?>').value, '<?=$k?>');" id="selBrand_<?=$k?>" name="selBrand_<?=$k?>">
		<?php
		if (!sizeof($brandRecs)) {
		?>
		<option value="">-- Select --</option>
		<?php
		}
		?>
		<?php
			foreach ($brandRecs as $brndId=>$brndName) {
				$selectedBrnd = ($selBrandId==$brndId)?"selected":""; 
		?>
			<option value="<?=$brndId?>" <?=$selectedBrnd?>><?=$brndName?></option>
		<?php
			}
		?>
	</select>
      </td>
      <td nowrap="" align="center" class="listing-item">
        <select id="selGrade_<?=$k?>" name="selGrade_<?=$k?>">
		<?php
		if (!sizeof($gradeRecs)) {
		?>
		<option value="">-- Select --</option>
		<?php
		}
		?>
		<?php
			foreach ($gradeRecs as $grdId=>$grdName) {
				$selGrade = ($selGradeId==$grdId)?"selected":""; 
		?>
			<option value="<?=$grdId?>" <?=$selGrade?>><?=$grdName?></option>
		<?php
			}
		?>
	</select>
      </td>
      <td nowrap="" align="center" class="listing-item">
        <select id="selFreezingStage_<?=$k?>" name="selFreezingStage_<?=$k?>">
		<option value="">--Select--</option>
		<?php
		if (sizeof($freezingStageRecords)>0) {	
			foreach($freezingStageRecords as $fsr) {
				$freezingStageId	= $fsr[0];
				$freezingStageCode	= stripSlash($fsr[1]);
				
				$selFrzngStage = ($selFreezingStageId==$freezingStageId)?"selected":""; 
		?>	
			<option value="<?=$freezingStageId?>" <?=$selFrzngStage?>><?=$freezingStageCode?></option>
		<?php
				}
			}
		?>
	</select>
      </td>
      <td nowrap="" align="center" class="listing-item">
        <select id="selFrozenCode_<?=$k?>" name="selFrozenCode_<?=$k?>" onchange="xajax_getFilledWt(document.getElementById('selFrozenCode_<?=$k?>').value, '<?=$k?>'); xajax_getMCPkgs('<?=$k?>', document.getElementById('selFrozenCode_<?=$k?>').value, '');" >
		<?php		
		if (sizeof($frznCodeRecs)>0) {	
			 foreach($frznCodeRecs as $frozenPackingId=>$frozenPackingCode) {
				$selFrznPkg = ($selFrozenCodeId==$frozenPackingId)?"selected":"";
		?>	
			<option value="<?=$frozenPackingId?>" <?=$selFrznPkg?>><?=stripslashes($frozenPackingCode)?></option>	
		<?php
				}
		} else {
		?>
		<option value="">-- Select --</option>
		<?php
		}
		?>	
		</select>
      </td>
      <td nowrap align="center" class="listing-item">
        <select onchange="xajax_getNumMC(document.getElementById('selMCPacking_<?=$k?>').value, '<?=$k?>');" id="selMCPacking_<?=$k?>" name="selMCPacking_<?=$k?>">
		<?php
		if (sizeof($mcPkgRecs)>0) {	
			 foreach($mcPkgRecs as $mcpackingId=>$mcpackingCode) {
				$selMCPkg = ($selMCPackingId==$mcpackingId)?"selected":"";
		?>	
			<option value="<?=$mcpackingId?>" <?=$selMCPkg?>><?=$mcpackingCode?></option>	
		<?php
				}
			} else {
		?>
		<option value="">--Select--</option>
		<?php
		}
		?>
	</select>
      </td>
		<td nowrap align="center" class="listing-item">
			<select id="wtType_<?=$k?>" name="wtType_<?=$k?>" onchange="totRowVal(<?=$k?>);">
				<?php
					if (sizeof($wtTypeArr)>0) {	
						 foreach($wtTypeArr as $wtTypeKey=>$wtTypeVal) {
								$selected = ($wtType==$wtTypeKey)?"selected":"";
				?>	
				<option value="<?=$wtTypeKey?>" <?=$selected?>><?=$wtTypeVal?></option>	
				<?php
						}
					}
				?>
			</select>
		</td>
      <td nowrap="" align="center" class="listing-item">
        <input type="text" style="text-align: right;" onkeyup="totRowVal('<?=$k?>');" value="<?=$numMC?>" size="6" id="numMC_<?=$k?>" name="numMC_<?=$k?>"/>
      </td>
	  <td nowrap="" align="center" class="listing-item">
		<input type="text" style="text-align:right; border:none;" readonly="" value="<?=$prdWt?>" size="8" id="prdTotalWt_<?=$k?>" name="prdTotalWt_<?=$k?>">
	  </td>
      <td nowrap="" align="center" class="listing-item">
        <input type="text" style="text-align: right;" onkeyup="totRowVal('<?=$k?>');" value="<?=$pricePerKg?>" size="6" id="pricePerKg_<?=$k?>" name="pricePerKg_<?=$k?>"/>
      </td>
      <td nowrap="" align="center" class="listing-item">
        <input type="text" style="border: medium none ; text-align: right;" readonly="" value="<?=$valueInUSD?>" size="8" id="valueInUSD_<?=$k?>" name="valueInUSD_<?=$k?>"/>
      </td>
      <td nowrap="" align="center" class="listing-item">
        <input type="text" style="border: medium none ; text-align: right;" readonly="" value="<?=$valueInINR?>" size="8" id="valueInINR_<?=$k?>" name="valueInINR_<?=$k?>"/>
      </td>
      <td nowrap="" align="center" class="listing-item">
        <input type="text" style="border: medium none ; text-align: right;" readonly="" value="<?=$availableMC?>" size="6" id="availableNumMC_<?=$k?>" name="availableNumMC_<?=$k?>"/>
		<input type="hidden" style="border: medium none ; text-align: right;" readonly="" value="<?=$poItems?>" size="6" id="poItems_<?=$k?>" name="poItems_<?=$k?>"/>
		
      </td>
      <td nowrap="" align="center" class="listing-item">
        <a onclick="setPOItemStatus('<?=$k?>');" href="###"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a><input type="hidden" value="" id="status_<?=$k?>" name="status_<?=$k?>"/><input type="hidden" value="N" id="IsFromDB_<?=$k?>" name="IsFromDB_<?=$k?>"/><input type="hidden" id="poEntryId_<?=$k?>" name="poEntryId_<?=$k?>" value="<?=$poEntryId?>" /><input type="hidden" id="hidBrandId_<?=$k?>" name="hidBrandId_<?=$k?>" value="<?=$selBrandId?>"/><input type="hidden" id="frznPkgFilledWt_<?=$k?>" name="frznPkgFilledWt_<?=$k?>" value="<?=($filledWt)?$filledWt:0;?>" /><input type="hidden" id="numPacks_<?=$k?>" name="numPacks_<?=$k?>" value="<?=($numPacks)?$numPacks:0;?>" /><input type='hidden' name='frznPkgDeclaredWt_<?=$k?>' id='frznPkgDeclaredWt_<?=$k?>' value='<?=($declaredWt)?$declaredWt:0;?>' readonly><input type="hidden" name="frznPkgUnit_<?=$k?>" id="frznPkgUnit_<?=$k?>" value="<?=$frznCodeUnit?>" readonly>
      </td>
</tr>	
<?php
		$k++;		
	} // Product Loop Ends here
?>
			
			
			<tr bgcolor="#FFFFFF" align="center" id="totRowId">
				<td>&nbsp;</td>
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
                <td class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;">Total:</td>
				<td>			
					<input name="totalNumMC" type="text" id="totalNumMC" size="8" style="text-align:right;border:none;" readonly value="<?=$totalNumMC;?>">
				</td>
				<td>			
					<input name="totalNetWt" type="text" id="totalNetWt" size="8" style="text-align:right;border:none;" readonly value="<?=$totalNetWt;?>">
				</td>
				<td>&nbsp;</td>
				<td>
					<input name="totalValUSD" type="text" id="totalValUSD" size="8" style="text-align:right;border:none;" readonly value="<?=$totalValUSD;?>">
				</td>
				<td>
					<input name="totalValINR" type="text" id="totalValINR" size="8" style="text-align:right;border:none;" readonly value="<?=$totalValINR;?>">
				</td>
				<?php
					if ($editMode) {
				?>
					<td>&nbsp;</td>
				<?php
					}
				?>
				<td>&nbsp;</td>
		        </tr>
		</table>	
	</TD>
</tr>
<!--  Hidden Fields-->
<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rawItemSize;?>">
<!--  Dynamic Row Ends Here-->
<tr><TD height="5"></TD></tr>
<tr>
	<TD style="padding-left:10px;padding-right:10px;">
		<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add Item(New)</a>
		&nbsp;&nbsp;
		<a href="###" id='addRow' onclick="javascript:addNewItemCpy();"  class="link1" title="Click here to copy item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add Item(Copy)</a>
	</TD>
</tr>
	</table>	
	</td>
	</tr>
	<tr>
		  <td colspan="2" align="center">
		  <table width="75%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td></td>
                                                  <td valign="top"></td>
                                                </tr>
                                 </table></td>
			  </tr>
			<tr><TD height="10"></TD></tr>
<!-- Close PO as Single INVOICE -->
		<tr id="poInSingleInv" style="display:none;">
			<TD colspan="2" style="padding-left:10px;padding-right:10px;">
				<table>
				<TR>
					<TD valign="top">
					<fieldset>
						<!--<legend class="listing-item" style="line-height:normal;">Despatch Details</legend>-->
						<table>
						<TR>
						<td class="fieldName" nowrap="true">*Invoice No.</td>
						<td class="listing-item" nowrap="true">
							<input name="invoiceNo" id="invoiceNo" type="text" size="6" onKeyUp="xajax_chkSONumberExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('invoiceDate').value, document.getElementById('invoiceType').value);" value="<?=($invoiceNo!=0)?$invoiceNo:"";?>" autocomplete="off" <?=$fieldReadOnly?>/>
							<input type="hidden" name="validInvoiceNo" id="validInvoiceNo" value="">
							<br/>
							<span id="divSOIdExistTxt" style='line-height:normal; font-size:10px; color:red;'></span>
						</td>
						<td class="fieldName" nowrap="true">*Invoice Date</td>
							<td nowrap="true">
							<?php
								if ($invoiceDate=="") $invoiceDate = date("d/m/Y");
							?>
							<input type="text" name="invoiceDate" id="invoiceDate" value="<?=$invoiceDate?>" size="8" onchange="xajax_chkValidInvoiceDate(document.getElementById('invoiceDate').value);" autocomplete="off" <?=$fieldReadOnly?>/>
							<input type="hidden" name="validInvoiceDate" id="validInvoiceDate" value="">
							</td>
						</TR>						
							<tr><TD height="10"></TD></tr>
							<tr>
								<TD colspan="4" align="center">
									<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" Save & Confirm " onClick="return validatePurchaseOrder(document.frmPurchaseOrder, '1');" style="width:110px" />
								</TD>
							</tr>
						</table>
					</fieldset>
					</TD>
				</TR>
			</table>
		</TD>
		</tr>
<tr id="splitPORow">
	<TD colspan="2" style="padding-left:10px;padding-right:10px;">
	<table>
	<TR><TD>
	<table id="tblSplitItem">
	<?php
		# Split invoice section && $k=sizeof($poRawItemRecs)	
		if (sizeof($splitInvoiceMainRecs)>0) {
			# Get All PO Item Recs
			$poItemRecs = $purchaseorderObj->getProductsInPO($mainId);				

			$l = 0;
			foreach ($splitInvoiceMainRecs as $sir) {
				$invoiceId 	= $sir[0];
				$invNo		= $sir[1];
				$invDate	= dateFormat($sir[2]);
				$invType	= $sir[3];
				$invProfomaNo	= $sir[4];
				$invSampleNo	= $sir[5];
				$entryDate	= $sir[6];
				$sInvoiceTypeId	= $sir[7];
				$sEucodeId=$sir[8];
	?>
	<tr align="center" class="whiteRow" id="spoRow_<?=$l?>">
      		<td align="center" id="srNo_<?=$l?>" class="listing-item">
        <fieldset>
          <table>
            <tbody>
              <tr>
                <td>
                  <table border="0">
                    <tbody>
                      <tr>
                        <td>
                          <table>
                            <tbody>
                              <tr>
                                <td nowrap="" class="fieldName">
                                   *Invoice Type 
                                </td>
                                <td>
                                <select onchange="showSplitInvRow('', '', '', '<?=$l?>');" id="invoiceType_<?=$l?>" name="invoiceType_<?=$l?>">
					<!--<option selected="true" value="T">Taxable</option>
					<option value="S">Sample</option>-->
				<option value=''>--Select--</option>
			<?php
				foreach ($invoiceTypeMasterRecs as $itm) {
					$invoiceTypeId 		= $itm[0];
					$invoiceTypeName	= $itm[1];
					$selectedInvType = ($sInvoiceTypeId==$invoiceTypeId)?"selected":"";
			?>
				<option value='<?=$invoiceTypeId?>' <?=$selectedInvType?>><?=$invoiceTypeName?></option>
			<?php
				}
			?>
				</select>
                                </td>
                              </tr>
                              <tr id="sampleInvNoRow_<?=$l?>" style="display: none;">
                                <td nowrap="" class="fieldName">
                                   *Sample Invoice No. 
                                </td>
                                <td nowrap="true">
                                  <input type="text" value="0" onkeyup="xajax_chkSplitSampleNoExist(document.getElementById('sampleInvoiceNo_<?=$l?>').value, '0', '', document.getElementById('invoiceDate_<?=$l?>').value, '0');" size="6" id="sampleInvoiceNo_<?=$l?>" name="sampleInvoiceNo_<?=$l?>"/>
                                </td>
                              </tr>
                              <tr id="proformaInvNoRow_<?=$l?>">
                                <td nowrap="true" class="fieldName">
                                   *Proforma No.
                                </td>
                                <td nowrap="true">
                                  <input type="text" autocomplete="off" onkeyup="xajax_chkSplitPfrmaNoExist(document.getElementById('proformaInvoiceNo_<?=$l?>').value, '0', '', document.getElementById('entryDate_<?=$l?>').value, '0');" value="<?=$invProfomaNo?>" size="6" id="proformaInvoiceNo_<?=$l?>" name="proformaInvoiceNo_<?=$l?>"/>
									 <span style="font-family:'Trebuchet','Verdana','Arial','Helvetica','sans-serif';font-size: 10px;font-style: normal;font-variant: normal;font-weight: bold;letter-spacing: 0.05em;line-height: 26px;padding-right: 5px;text-align: right;    text-decoration: none; text-transform: none;"> Eucode</span>
								   <select id="selEuCode_<?=$l?>" name="selEuCode_<?=$l?>">
		<option value="">--Select--</option>
		<?php
		if (sizeof($euCodeRecords)>0) {	
			foreach($euCodeRecords as $eucr) {
				$euCodeId	= $eucr[0];
				$euCode		= stripSlash($eucr[1]);
				//$selEUR = ($selEuCodeId==$euCodeId)?"selected":"";
				$selEUR = ($sEucodeId==$euCodeId)?"selected":"";
		?>	
			<option value="<?=$euCodeId?>" <?=$selEUR?>><?=$euCode?></option>	
		<?php
				}
			}
		?>
	</select>
                                </td>

								<td nowrap="true" class="fieldName">
                                 
                                </td>
                                <td nowrap="true">
                                 
                                </td>
                              </tr>
                             <input type="hidden" value="<?=dateFormat($entryDate)?>" size="8" id="entryDate_<?=$l?>" name="entryDate_<?=$l?>"/>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td nowrap="true" align="center" colspan="2" id="divNumExistTxt_<?=$l?>" style="line-height: normal; font-size: 10px; color: red;" class="listing-item"/>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td>
                  <table cellspacing="1" cellpadding="3" bgcolor="#999999" id="tblPOItem">
                    <tbody>
                      <tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head">
				Description of Goods
				</td>
				<td class="listing-head">
				MC to be <br/>shipped 
				</td>
				<td class="listing-head">
				MC in this<br/> Invoice 
				</td>
				<td class="listing-head">
				Price per <span class="replaceUnitTxt">Kg</span> 
				<br/>
				in <span class="replaceCY">USD</span> 
				</td>
				<td class="listing-head">
				Value in 
				<br/>
				<span class="replaceCY">USD</span> 
				</td>
				<td class="listing-head">
				Value in 
				<br/>
				INR 
				</td>
				<td class="listing-head">
				Balance MC 
				</td>
                      </tr>
			<?php
				$i=0;
								
				foreach ($poItemRecs as $poi) {
					$poEntryId 	= $poi[0];
					$sFish 		= $poi[1];
					$sProcessCode 	= $poi[2];
					$sEuCode	= $poi[3];
					$sBrand		= $poi[4];
					$sBrdFrom	= $poi[13];					
					$sGrade	  	= $poi[5];
					$sFreezingStage = $poi[6];
					$sFrozenCode    = $poi[7];
					$sMCPacking	 = $poi[8];
					$selPCId	= $poi[14];
					$sFrozenCodeId	= $poi[15];
					$sMCPackingId	= $poi[16];
					$wtType			= $poi[17];
					$sGradeId			= $poi[18];
					$sBrandId			= $poi[20];
					$sFreezingStageId =  $poi[21];
					$hidPOItems	=$selPCId.'_'.$sBrandId.'_'.$sGradeId.'_'.$sFreezingStageId.'_'.$sFrozenCodeId.'_'.$sMCPackingId; 
					//echo $hidPOItems.'<br/>';
					//$disProdItem	= $sProcessCode."&nbsp;".$sEuCode."&nbsp;".$sBrand."&nbsp;".$sGrade."&nbsp;".$sFreezingStage."&nbsp;".$sFrozenCode."&nbsp;".$sMCPacking;

					$disProdItem	= $sProcessCode."&nbsp;".$sBrand."&nbsp;".$sGrade."&nbsp;".$sFreezingStage."&nbsp;".$sFrozenCode."&nbsp;".$sMCPacking;

					# Filled Wt
					list($filledWt, $declaredWt, $frznCodeUnit) = $purchaseorderObj->getFrznPkgFilledWt($sFrozenCodeId);

					# Get Num of Packs
					$numPacks  = $purchaseorderObj->numOfPacks($sMCPackingId);

					# Get Invoice Entry Rec
					list($invoiceEntryId, $mcInPO, $mcInInvoice, $invPricePerKg, $invValUSD, $invValINR) = $purchaseorderObj->getInvoiceRec($invoiceId, $poEntryId);
					
			?>
	<tr class="whiteRow">
                        <td class="listing-item">
						<?php //print_r($poItemRecs);?>
                          	<?=$disProdItem?>
				<input type="hidden" size="6" id="selPCId_<?=$i?>_<?=$l?>" name="selPCId_<?=$i?>_<?=$l?>" value="<?=$selPCId?>"/>
				<input type="hidden" size="6" id="hidPOEntryId_<?=$i?>_<?=$l?>" name="hidPOEntryId_<?=$i?>_<?=$l?>" value="<?=$poEntryId?>" />
				<input type="hidden" size="6" id="hidInvoiceEntryId_<?=$i?>_<?=$l?>" name="hidInvoiceEntryId_<?=$i?>_<?=$l?>" value="<?=$invoiceEntryId?>" />
				<input type="hidden" value="<?=$filledWt?>" name="frznPkgFilledWt_<?=$i?>_<?=$l?>" id="frznPkgFilledWt_<?=$i?>_<?=$l?>"/>
				<input type="hidden" value="<?=$numPacks?>" name="numPacks_<?=$i?>_<?=$l?>" id="numPacks_<?=$i?>_<?=$l?>"/>
				<input type='hidden' name='frznPkgDeclaredWt_<?=$i?>_<?=$l?>' id='frznPkgDeclaredWt_<?=$i?>_<?=$l?>' value='<?=$declaredWt?>' readonly>
				<input type='hidden' name='wtType_<?=$i?>_<?=$l?>' id='wtType_<?=$i?>_<?=$l?>' value='<?=$wtType?>' readonly>
				<input type='hidden' name='frznPkgUnit_<?=$i?>_<?=$l?>' id='frznPkgUnit_<?=$i?>_<?=$l?>' value='<?=$frznCodeUnit?>' readonly>
				<input type='hidden' name='hidPOItems_<?=$i?>_<?=$l?>' id='hidPOItems_<?=$i?>_<?=$l?>' value='<?=$hidPOItems?>' readonly>
                        </td>
                        <td align="right" class="listing-item">
                         <input type="text" style="border: medium none ; text-align: right;" size="6" id="MCInPO_<?=$i?>_<?=$l?>" name="MCInPO_<?=$i?>_<?=$l?>" value="<?=$mcInPO?>" autocomplete="off" readonly="true" />
                        </td>
                        <td align="right" class="listing-item">
                        	<input type="text"  style="text-align: right;" size="6" id="MCInInv_<?=$i?>_<?=$l?>" name="MCInInv_<?=$i?>_<?=$l?>" value="<?=$mcInInvoice?>" autocomplete="off" onkeyup="chkMCQty();" />
                        </td>
			<td align="right" class="listing-item">
				<input type="text" autocomplete="off" onkeyup="chkMCQty();" style="text-align: right;" value="<?=$invPricePerKg?>" size="6" id="pricePerKg_<?=$i?>_<?=$l?>" name="pricePerKg_<?=$i?>_<?=$l?>"/>
			</td>
			<td align="right" class="listing-item">
				<input type="text" readonly="" autocomplete="off" style="border: medium none ; text-align: right;" value="<?=$invValUSD?>" size="8" id="valueInUSD_<?=$i?>_<?=$l?>" name="valueInUSD_<?=$i?>_<?=$l?>"/>
			</td>
			<td align="right" class="listing-item">
				<input type="text" readonly="" autocomplete="off" style="border: medium none ; text-align: right;" value="<?=$invValINR?>" size="8" id="valueInINR_<?=$i?>_<?=$l?>" name="valueInINR_<?=$i?>_<?=$l?>"/>
			</td>
                        <td align="right" class="listing-item">
                          	<input type="text" value="" size="8" id="balanceMc_<?=$i?>_<?=$l?>" name="balanceMc_<?=$i?>_<?=$l?>" style="text-align:right; border:none;" readonly="true"/>
                        </td>
            </tr>			
			<?php
				$i++;
				}
			?>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </fieldset>
      </td>
      <td align="center" class="listing-item">
        <a onclick="setSplitPOStatus('<?=$l?>');" href="###"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
	<input type="hidden" value="" id="spoStatus_<?=$l?>" name="spoStatus_<?=$l?>"/>
	<input type="hidden" value="N" id="spoIsFromDB_<?=$l?>" name="spoIsFromDB_<?=$l?>"/>
	<input type="hidden" value="<?=$invoiceId?>" id="hidInvoiceId_<?=$l?>" name="hidInvoiceId_<?=$l?>"/>
	<input name='spoMCStatus_<?=$l?>' type='hidden' id='spoMCStatus_<?=$l?>' value='Y'>
      </td>
    </tr>	
	<?php 
			$l++;
			}  // Split invoice Loop Ends here
		
		} // Split invoice Size Check
	?>			
	</table>
	<input type='hidden' name="splitTbleRowCount" id="splitTbleRowCount" value="<?=sizeof($splitInvoiceMainRecs)?>" />
	</TD></TR>
	</table>
</TD></tr>
										<tr>
											  <td align="center">&nbsp;</td>
											  <td align="center">&nbsp;</td>
										  </tr>
											<tr>
												<? if($editMode){?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrders.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validatePurchaseOrder(document.frmPurchaseOrder, '');">
													&nbsp;&nbsp;
													<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" Complete PO " onClick="return validatePurchaseOrder(document.frmPurchaseOrder, '1');" style="width:110px" />	
													<!--&nbsp;&nbsp;<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" Save & Confirm " onClick="return validatePurchaseOrder(document.frmPurchaseOrder, '');" style="width:110px" />-->
													<!--&nbsp;&nbsp;
													<input type="button" name="cmdSpl" id="cmdSplitPO" class="button" value=" Generate Single " onClick="genSingleInv(1);" style="width:110px" />-->
													&nbsp;&nbsp;
													<input type="button" name="cmdSplitPO" id="cmdSplitPO" class="button" value=" Proforma In Lot " onClick="genSingleInv(''); addSplitRow();" style="width:110px" />
													<!--<input type="button" name="cmdSplitPO" id="cmdSplitPO" class="button" value=" Split PO " onClick="addSplitRow();" style="width:110px" />-->
												</td>
												<? } else{?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrders.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Save &amp; Exit " onClick="return validatePurchaseOrder(document.frmPurchaseOrder, '');">
												</td>
												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
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
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Orders  </td>
								    <td background="images/heading_bg.gif" class="pageName" align="right" ><table cellpadding="0" cellspacing="0" width="40%">
                      <tr> 
					  	<td class="listing-item"> From:</td>
                                    <td nowrap="nowrap"> 
                            <? 
							if($dateFrom=="") $dateFrom=date("d/m/Y");
							?>
                            <input type="text" id="frozenPackingFrom" name="frozenPackingFrom" size="8" value="<?=$dateFrom?>"></td>
						            <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td nowrap> 
                                      <? 
									     if($dateTill=="") $dateTill=date("d/m/Y");
									  ?>
                                      <input type="text" id="frozenPackingTill" name="frozenPackingTill" size="8"  value="<?=$dateTill?>"></td>
					<td class="listing-item">&nbsp;</td>
					<!--<td class="listing-item" style="padding-left:2px;padding-right:5px;">Filter:</td>
					<td class="listing-item">
						<select name="invoiceTypeFilter">
							<option value="">--Select All--</option>
							<?php
								foreach ($invoiceTypeArr as $itaKey=>$itaValue) {
									$selected = ($itaKey==$invoiceTypeFilter)?"selected":""
							?>
							<option value="<?=$itaKey?>" <?=$selected?>><?=$itaValue?></option>
							<?php }?>
						</select>
					</td>-->
							        <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintPurchaseOrders.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?></td>
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
	<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if (sizeof($purchaseOrderRecords)>0) {
			$i	=	0;
	?>
		<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="12" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PurchaseOrders.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PurchaseOrders.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PurchaseOrders.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
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
	<?php
		 }
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;" nowrap>PO No.</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>PO Date</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Customer</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total Num MC</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Value in USD</td>				
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Value in INR</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Entry<br/>Date</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Despatch<br/>Date</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Quick Entry List</td>	
		<td class="listing-head">&nbsp;</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>		
	</tr>
	<?php
	$totalValueInUSD = "";
	$totalValueInINR = "";
	$totalNumPC = "";	
	foreach ($purchaseOrderRecords as $por) {
		$i++;
		$poMainId = $por[0];
		$custName 	= $por[6];
		$poNumMC	= $por[1];
		$totalNumPC	+= $poNumMC;

		$poValueUSD	= $por[2];
		$totalValueInUSD += $poValueUSD;

		$poValueINR	= $por[3];
		$totalValueInINR += $poValueINR;

		$poLastDate	= $por[4];
		$soEntryDate  	= $por[5];

		# ----- QEL Gen Starts ---------
		$qelGen		= $por[7];
		$qelConfirmed 	= $por[8];
		//echo "$qelGen, $qelConfirmed";
		$qelMsg    = "";
		$qelStatus = "";
		$qelBgColor = "";
		if ($qelConfirmed=='Y') {
			$qelMsg    = "COMPLETED";
			$qelStatus = "COMPLETED";
			$qelBgColor = "#90EE90";
		} else if ($qelGen=='Y') {						
			$qelMsg    = "GENERATED";
			$qelStatus = "PENDING";
			$qelBgColor = "gray";
		} else  {					
			$qelMsg    = "Click here to generate new Quick Entry List.";
			$qelStatus = "<a href='###' onclick=\"return validateQELGen('$poMainId', '$userId', '$i', '$poLastDate');\" class='link2'>GENERATE</a>";
			$qelBgColor = "white";
		}

		/*Disable edit only after all purchase Order invoice Confirmed*/
		$notConfirmedCount = $por[12];
		$invoiceCount = $por[13];
		$poCompletedStatus = $por[9];
		$disableEdit = ($poCompletedStatus=='C' && ($invoiceCount>0 && $notConfirmedCount<=0))?"disabled":"";
		$purchaseOrderNo	= $por[10];
		$purchaseOrderDate	= dateFormat($por[11]);

		# ----- QEL Gen Ends ---------

		/*
		$PORMEntryId 	= 	$por[6];
		$POGradeEntryId =	$por[11];		
		$pOId			=	$por[1];
		$customer		=	$customerObj->findCustomer($por[2]);
		$fish	=	$fishmasterObj->findFishName($por[7]);
		$processCode = $processcodeObj->findProcessCode($por[8]);
		$selGrade = $grademasterObj->findGradeCode($por[12]);
		$freezingStage = $freezingstageObj->findFreezingStageCode($por[13]);		
		$brand = $brandObj->findBrandCode($por[10]);
		$frozenCode = $frozenpackingObj->findFrozenPackingCode($por[14]);
		$mCPackingCode = $mcpackingObj->findMCPackingCode($por[15]);
		$numMC			=	$por[16];
		$PricePerKg		=	$por[17];
		$valueInUSD		=	$por[18];
		$totalValueInUSD += $valueInUSD;
		$valueInINR		=	$por[19];
		$totalValueInINR	+= $valueInINR;
		//echo $por[23];
		$currentDate	=	 date("Y-m-d");
		$cDate			=	explode("-",$currentDate);
		$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);
		$eDate		=	explode("-",$por[4]);
		$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);
		// mktime(0,0,0,$MONTH,$DAY, $YEAR)
		// echo $val = ($eDate[1]/10)*10;
		$dateDiff = floor(($d2-$d1)/86400);
		$status = "";
		$statusFlag	=	"";
		$extended	=	$por[20];
		if ($extended=='E' && $por[23]=="") {
			$status	=	"<span class='err1'>Extended & Pending </span>";
			$statusFlag =	'E';
		} else {
			if ($statusObj->findStatus($por[23])) {
				$status	=	$statusObj->findStatus($por[23]);
			} else if ($dateDiff>0) {
				$status 	= "<span class='err1'>Delayed</span>";
				$statusFlag =	'D';
			} else {
				$status = "Pending";
			}
		}
		$invoiceNo	=	$por[24];
		*/
		/*
		$currentLogStatus	=	$por[21];
		$currentLogDate		=	$por[22];
		$shipmentDate		=	$por[4];
		if ((($statusFlag=='E') || ($statusFlag=='D')) && strlen($currentLogStatus)<=1 ) {
			if ($currentLogStatus=='D' && $statusFlag=='E') {
				$statusFlag = $currentLogStatus.",".$statusFlag;
				$shipmentDate	= $currentLogDate.",".$shipmentDate;	
			}
			$logStatusUpted = $purchaseorderObj->updateLogStatus($poMainId,$statusFlag,$shipmentDate);
		}
		*/
	?>
	<tr  <?=$listRowMouseOverStyle?>>
		<td width="20" height="25">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$poMainId;?>" class="chkBox" />
			<input type="hidden" name="PORMEntryId_<?=$i;?>" value="<?=$PORMEntryId?>">
			<input type="hidden" name="POGradeEntryId_<?=$i;?>" value="<?=$POGradeEntryId?>">
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=($purchaseOrderNo!="")?$purchaseOrderNo:"";?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=($purchaseOrderDate!='00/00/0000')?$purchaseOrderDate:"";?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$custName?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poNumMC?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poValueUSD?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poValueINR?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=dateFormat($soEntryDate)?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=dateFormat($poLastDate)?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"  onMouseover="ShowTip('<?=$qelMsg?>');" onMouseout="UnTip();" id="qelCol_<?=$i?>" bgcolor="<?=$qelBgColor?>">
			<?=$qelStatus;?>
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px; line-height:normal;" nowrap="true">
			<a href="javascript:printWindow('ViewPO.php?selPOId=<?=$poMainId?>',700,600)" class="link1" title="Click here to View PO">
				VIEW
			</a>
			<?php 
				if($print==true){
			?>
			<!--/-->
			<!--<a href="javascript:printWindow('PrintSOTaxInvoice.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to Print the Invoice">
				PRINT
			</a>-->
			<? }?>
		</td>		
		<? 
		//echo $edit;
			if($edit==true){?>
			  <td class="listing-item" width="45" align="center">
				<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$poMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='PurchaseOrders.php';" <?=$disableEdit?>>
			</td>
		  <? }?>
	</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<tr bgcolor="white">
		<td height="10" colspan="4" align="right" class="listing-head">Total :</td>
		<td  height="10" align="right" class="listing-item" style="padding-right:5px; padding-left:5px;">
			<strong><?=number_format($totalNumPC,0);?></strong>
		</td>
		<td class="listing-item" style="padding-right:5px; padding-left:5px;" align="right">
			<strong><?=number_format($totalValueInUSD,2);?></strong>
		</td>
		<td class="listing-item" style="padding-right:5px; padding-left:5px;" align="right">
			<strong><?=number_format($totalValueInINR,2);?></strong>
		</td>
		<td class="listing-item" style="padding-right:5px; padding-left:5px;">
			&nbsp;
		</td>
		<td class="listing-item" style="padding-right:5px; padding-left:5px;">
			&nbsp;
		</td>
		<td class="listing-head"></td>
		<td class="listing-head">&nbsp;</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
      </tr>
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="12" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PurchaseOrders.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PurchaseOrders.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PurchaseOrders.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
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
	<tr bgcolor="white">
		<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="5" >
			<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
			<input type="hidden" name="rmEntryId" id="rmEntryId" value="<?=$rmEntryId?>"><input type="hidden" name="gradeEntryId" id="gradeEntryId" value="<?=$gradeEntryId?>">
		</td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPurchaseOrders.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
<input type="hidden" name="editPORMEntryId" value="<?=$editPORMEntryId?>">
<input type="hidden" name="editPOGradeEntryId" value="<?=$editPOGradeEntryId?>">
<input type="hidden" name="editSelectionChange" value="0">							
<input type="hidden" name="editMode" value="<?=$editMode?>">
<input type="hidden" name="oneUSDToINR" id="oneUSDToINR" value="<?=$oneUSD?>" readonly>
<input type="hidden" name="currencyRateListId" id="currencyRateListId" value="<?=$cyRateListId?>" readonly>
<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" />
<input type="hidden" name="hidKG2LBS" id="hidKG2LBS" value="<?=KG2LBS?>" />
		<tr>
			<td height="10"></td>
		</tr>	
	</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectDate", 
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
			inputField  : "lastDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "lastDate", 
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
			inputField  : "frozenPackingFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingFrom", 
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
			inputField  : "frozenPackingTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "poDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "poDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<?php
		if ($addMode || $editMode) {
	?>		
	<script language="JavaScript" type="text/javascript">
		//showInvRow('<?=$mode?>', '<?=$proformaInvoiceNo?>', '<?=$sampleInvoiceNo?>');

		// Split Row
		function addSplitRow()
		{
			splitPO('tblSplitItem', '', '', '', '', '', '', '');			
		}
	</script>
	<?php
		}
	?>
		<script language="JavaScript" type="text/javascript">
			<?php
				if (sizeof($poRawItemRecs)>0) {

				// Set Value to Main table
			?>
				fieldId = <?=sizeof($poRawItemRecs)?>;
			<?php
				}
			?>
			<?php
				if (sizeof($splitInvoiceMainRecs)>0) {
					
				// Set value to Split Row
			?>
				fldId = <?=sizeof($splitInvoiceMainRecs)?>;
				chkMCQty();
			<?php
				} else if ($editMode && !sizeof($splitInvoiceMainRecs)) {
			?>
				// Display Split Row section
				addSplitRow(); 
			<?php
				}
			?>
			
			function addNewItem()
			{				
				addNewPOItem('tblPOItem', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '<?=$mode?>');
				xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
			}
			function addNewItemCpy()
			{
				addNewPOItem('tblPOItem', '', '', '', '', '', '', '', '', '', '', '', '', '', 'C', '<?=$mode?>');
				xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
			}
		</script>
	<?php 
		if ($addMode) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			window.load = addNewItem();
		</SCRIPT>
	<?php 
		}
	?>

	<?php
		if (sizeof($poRawItemRecs)>0 && $enabled) {			
	?>
	<script language="JavaScript" type="text/javascript">
		//xajax_getBrandRecs('<?=sizeof($poRawItemRecs);?>', '<?=$selCustomerId?>');
	</script>
	<!--SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<?php
		// When Edit Mode Products are loading from Top function
		$totalAmount = 0;
		$k = 0;
		foreach ($poRawItemRecs as $rec) {
			$poEntryId = $rec[0];
			$selFishId = $rec[1];
			$selProcessCodeId = $rec[2];
			$selEuCodeId	  = $rec[3];

			$selBrd		  = $rec[4];
			$selBrdFrom	  = $rec[13];
			$selBrandId	  = $selBrd."_".$selBrdFrom;

			$selGradeId	  = $rec[5];
			$selFreezingStageId = $rec[6];
			$selFrozenCodeId  = $rec[7];
			$selMCPackingId	  = $rec[8];
			$numMC		= $rec[9];
			$pricePerKg	= $rec[10];
			$valueInUSD	= $rec[11];
			$valueInINR	= $rec[12];
		?>					
			addNewPOItem('tblPOItem', '<?=$poEntryId?>', '<?=$selFishId?>', '<?=$selProcessCodeId?>', '<?=$selEuCodeId?>', '<?=$selBrandId?>', '<?=$selGradeId?>', '<?=$selFreezingStageId?>', '<?=$selFrozenCodeId?>', '<?=$selMCPackingId?>', '<?=$numMC?>', '<?=$pricePerKg?>', '<?=$valueInUSD?>', '<?=$valueInINR?>', '', '<?=$mode?>');
			xajax_getProcessCodes('<?=$selFishId?>', '<?=$k?>', '<?=$selProcessCodeId?>');
			xajax_getGradeRecs('<?=$selProcessCodeId?>', '<?=$k?>', '<?=$selGradeId?>');
			xajax_getFilledWt('<?=$selFrozenCodeId?>', '<?=$k?>');
			xajax_getNumMC('<?=$selMCPackingId?>', '<?=$k?>');
			xajax_getNumMCExist('<?=$mainId?>', '<?=$selFishId?>', '<?=$selProcessCodeId?>', '<?=$selGradeId?>', '<?=$k?>');
		<?php 
			$k++;
			}  // SO Loop Ends here
		?>
			xajax_getBrandRecs('<?=sizeof($poRawItemRecs);?>', '<?=$selCustomerId?>');
	</SCRIPT-->
	<?php
		} // SO Size Check
	?>
	<script language="JavaScript" type="text/javascript">
		function reLoadCustomer() 
		{
			xajax_reloadCustomer();
		}

		function reLoadAgent()
		{
			xajax_getAgentList(document.getElementById('selCustomer').value, '');
		}
	</script>
	<?php
	 if ($editMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		//xajax_getAgentList('<?=$selCustomerId?>', '<?=$selAgent?>');
		//xajax_getPortList('<?=$selCountry?>', '<?=$selPort?>');
		//xajax_getCustPaymentTerm('<?=$selCustomerId?>', '<?=$paymentTerms?>');
	</script>
	<?php
		}
	?>

	<?php
		# Split invoice section && $k=sizeof($poRawItemRecs)	
		if (sizeof($splitInvoiceMainRecs)>0 && $enabled) {
	?>	
	<!--SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<?php
			$l = 0;
			foreach ($splitInvoiceMainRecs as $sir) {
				$invoiceId 	= $sir[0];
				$invNo		= $sir[1];
				$invDate	= dateFormat($sir[2]);
				$invType	= $sir[3];
				$invProfomaNo	= $sir[4];
				$invSampleNo	= $sir[5];
				$entryDate	= $sir[6];
		?>
			splitPO('tblSplitItem', '<?=$invoiceId?>', '<?=$invNo?>', '<?=$invDate?>', '<?=$invType?>', '<?=$invProfomaNo?>', '<?=$invSampleNo?>', '<?=$entryDate?>');
		<?php 
			$l++;
			}  // Split invoice Loop Ends here
		?>
	</SCRIPT-->	
	<?php
		} // Split invoice Size Check
	?>

	<script language="JavaScript" type="text/javascript">
		$(document).ready(function () {
			<?php
			if ($cyCode!="")
			{	
			?>
			$(".replaceCY").html('<?=$cyCode?>');
			<?php
			}	
			?>			

			$('#selUnit').change(function() {
			  changeUnitTxt();
			});

			<?php
			if ($editMode)
			{	
			?>
			changeUnitTxt();
			calcAllRowVal();
			<?php
			}	
			?>
		});
	</script>

	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>