<?php
	require_once("include/include.php");
	require_once("lib/SalesOrder_ajax.php");
	ob_start();
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$genPoId	= "";
	
	$dateSelection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"]."&invoiceTypeFilter=".$p["invoiceTypeFilter"]."&distributorFilter=".$p["distributorFilter"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//$changesUpdateMasterObj->updateAllPendingSO();
	

	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
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

	list($urlFnId, $urlModuleId, $urlSubModuleId) = $modulemanagerObj->getFunctionIds($currentUrl);	
	$rfrshTimeLimit = $refreshTimeLimitObj->getRefreshTimeLimit($urlSubModuleId, $urlFnId);
	$refreshTimeLimit = ($rfrshTimeLimit!=0)?$rfrshTimeLimit:60;

	# Packing Confirm Enable check
	//$pkgCnfmEnabled = $manageconfirmObj->pkgConfirmEnabled();
	//echo "$urlFnId, $urlModuleId, $urlSubModuleId;;; ==>$refreshTimeLimit";
	/*-----------------------------------------------------------*/

	//$updatePendingSO = $changesUpdateMasterObj->updateAllPendingSO();

	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode = true;
		//$editSO = $salesOrderObj->getNextInvoiceNo();	
		$proformaInvoiceNo 	= $salesOrderObj->getNextProformaInvoiceNo();
		//$sampleInvoiceNo	= $salesOrderObj->getNextSampleInvoiceNo();
	}
	if ($p["cmdCancel"]!="") {

		$addMode = false;		
		$cSOId = $sessObj->getValue("salesOrderId");
		# Update Rec
		if ($cSOId!=0) {
			$updateModifiedRec = $salesOrderObj->updateModifiedRec($cSOId, '', 'U');
			$sessObj->updateSession("salesOrderId",0);
		}
		$editMode = false;
	}

	#Resetting values
	if ($p["selDistributor"]!="") $selDistributorId = $p["selDistributor"];

	# Auto Id generation enabled or disabled Removed 16-05-09
	//$genPoId = $idManagerObj->check("SO");

	# Add a SO
	if ($p["cmdAdd"]!="") {

		/*
		if ($genPoId==1) {
			list($isMaxId,$salesOrderNo)	= $idManagerObj->generateNumberByType("SO"); 
			$warning  = ($isMaxId=="Y")? "The generated SO ID is greater than the ending number of Sales Order ID." : "";
			$chkSOId = $salesOrderObj->checkSONumberExist($salesOrderNo);
		} else {
			$salesOrderNo = $p["invoiceNo"];
			$isMax = $idManagerObj->checkMaxId("SO",$salesOrderNo);			
			if( $isMax=="Y") $warning = "The generated SO ID is greater than the ending number of Sales Order ID.";			
		}
		*/	

		$itemCount	  = $p["hidTableRowCount"];
		$selDistributorId = $p["selDistributor"];		
		$lastDate		= mysqlDateFormat($p["lastDate"]);
		$productPriceRateListId = $p["productPriceRateList"];
		$selState		= $p["selState"];
		$selCity		= $p["selCity"];
		$distMgnRateListId 	= $p["distMgnRateListId"];		
		$totalAmount		= $p["grandTotalAmt"];
		$taxType		= $p["taxType"];
		$billingForm		= $p["billingForm"];
		$taxRowCount		= $p["hidTaxRowCount"];
		$selTaxArr	= array();
		for ($j=0; $j<$taxRowCount; $j++) {
			$taxPercent = $p["hidTaxPercent_".$j];
			$taxAmount  = $p["taxAmount_".$j];		
			$arrVal = $taxPercent.":".$taxAmount;
			$selTaxArr[$j] = $arrVal;				
		}
		$selTax 	= implode(",",$selTaxArr);
		$grandTotalAmt	= $p["totalSOAmt"];	// Grand Total Amt
		$soRemark	= addSlash(trim($p["soRemark"]));
		$totalTaxAmt	= $p["totalTaxAmt"];
		$netWt		= $p["netWt"];
		$grossWt	= $p["grossWt"];
		$numBox		= $p["numBox"];
		$invoiceType	= $p["invoiceType"];
		$additionalItemTotalWt = $p["additionalItemTotalWt"];	
		$adnlRowCount	= $p["hidItemTbleRowCount"];
		$poNo		= trim($p["poNo"]);
		$selArea	= $p["selArea"];
		$invoiceDate	= mysqlDateFormat($p["invoiceDate"]);
		$poDate		= mysqlDateFormat($p["poDate"]);
		$challanNo	= trim($p["challanNo"]);
		$challanDate	= mysqlDateFormat($p["challanDate"]);

		$discount	= $p["discount"];
		$discountRemark = $p["discountRemark"];
		$discountPercent = $p["discountPercent"];
		$discountAmt	= $p["discountAmt"];
	
		$octroiExempted = $p["octroiExempted"];
		$oecNo		= $p["oecNo"];
		$oecValidDate	= mysqlDateFormat($p["oecValidDate"]);
		$oecIssuedDate	= mysqlDateFormat($p["oecIssuedDate"]);

		$selTax		= ($invoiceType=='T')?$selTax:"";

		$proformaInvoiceNo	= $p["proformaInvoiceNo"];
		$proformaInvoiceDate	= mysqlDateFormat($p["proformaInvoiceDate"]);
		$sampleInvoiceNo	= $p["sampleInvoiceNo"];
		$sampleInvoiceDate	= mysqlDateFormat($p["sampleInvoiceDate"]);
		$entryDate		= mysqlDateFormat($p["entryDate"]);
		$invoiceDate = $entryDate;
		$soYear	 = date("Y", strtotime($invoiceDate));

		//Round off Calculation
		$roundVal = $salesOrderObj->getRoundoffVal($grandTotalAmt);

		$exDutyActive 		= $p["hidExDutyActive"];
		$eduCessPercent		= $p["hidEduCess"];
		$eduCessRLId		= $p["hidEduCessRLId"];
		$secEduCessPercent	= $p["hidSecEduCess"];
		$secEduCessRLId		= $p["hidSecEduCessRLId"];
		$totExDuty		= $p["totExDutyAmt"];
		$totEduCess		= $p["totEduCess"];
		$totSecEduCess		= $p["totSecEduCess"];
		$totCentralExDuty	= $p["grandTotCExDuty"];

		$transChargeActive	= $p["chbTransCharge"];
		$transportCharge	= $p["transportCharge"];

		$billingType		= $p["billingType"];
		$company	= $p["company"];
		$unit	= $p["unit"];
		$number_gen_id=$p["number_gen_id"];
		
		
		if ($proformaInvoiceNo || $sampleInvoiceNo) {
			if ($selDistributorId!="") {
				$salesOrderRecIns = $salesOrderObj->addSalesOrder($salesOrderNo, $selDistributorId, $selState, $selCity,  $lastDate, $totalAmount, $taxType, $billingForm, $selTax, $totalTaxAmt, $grandTotalAmt, $productPriceRateListId, $distMgnRateListId, $userId, $soRemark, $netWt, $grossWt, $numBox, $roundVal, $invoiceType, $additionalItemTotalWt, $poNo, $selArea, $invoiceDate, $poDate, $challanNo, $challanDate, $discount, $discountRemark, $discountPercent, $discountAmt, $octroiExempted, $oecNo, $oecValidDate, $oecIssuedDate, $proformaInvoiceNo, $proformaInvoiceDate, $sampleInvoiceNo, $sampleInvoiceDate, $entryDate, $soYear, $exDutyActive, $eduCessPercent, $eduCessRLId, $secEduCessPercent, $secEduCessRLId, $totExDuty, $totEduCess, $totSecEduCess, $totCentralExDuty, $transChargeActive, $transportCharge, $billingType,$company,$unit,$number_gen_id);
				#Find the Last inserted Id From t_salesorder Table
				$lastId = $databaseConnect->getLastInsertedId();
			}
			
			for ($i=0; $i<$itemCount; $i++) {
			   $status = $p["status_".$i];
			   if ($status!='N') {
				$selProductId	=	$p["selProduct_".$i];
				$unitPrice	=	trim($p["unitPrice_".$i]);
				$quantity	=	trim($p["quantity_".$i]);
				$totalAmt	=	$p["totalAmount_".$i];
				$selMcPkgId	=	$p["selMcPkg_".$i];
				$mcPack		=	$p["mcPack_".$i];
				$loosePack	=	$p["loosePack_".$i];
				$distMgnStateEntryId = $p["distMgnStateEntryId_".$i];
				$taxPercent	= $p["taxPercent_".$i];
				$pGrossWt	= $p["pGrossWt_".$i];
				$pMCPkgGrossWt	= $p["pMCPkgGrossWt_".$i];
				$freePkts	= $p["freePkts_".$i];
				$basicRate	= $p["basicRate_".$i];

				$exDutyPercent	= $p["exciseDuty_".$i];
				$exDutyAmt	= $p["excDutyAmt_".$i];
				$exDutyMasterId	= $p["excDutyEntryId_".$i];
				$eduCessAmt	= $p["eduCessAmt_".$i];
				$secEduCessAmt	= $p["secEduCessAmt_".$i];
				$taxAmt			= $p["taxAmt_".$i];
				$mcPkgWtId		= $p["hidMCPkgWtId_".$i];


				if ($lastId!="" && $selProductId!="" && ($quantity!="" || $freePkts!="")) {
					$salesOrderItemsIns = $salesOrderObj->addSalesOrderEntries($lastId, $selProductId, $unitPrice, $quantity, $totalAmt, $selMcPkgId, $mcPack, $loosePack, $distMgnStateEntryId, $taxPercent, $pGrossWt, $pMCPkgGrossWt, $freePkts, $basicRate, $exDutyPercent, $exDutyAmt, $exDutyMasterId, $eduCessAmt, $secEduCessAmt, $taxAmt, $mcPkgWtId);
				}
			   }
			}
			// Adnal Item
			if ($additionalItemTotalWt!="" && $invoiceType=='S') {
				for ($j=0; $j<$adnlRowCount; $j++) {
					$status = $p["status_".$j];
					if ($status!='N') {
						$itemName	= $p["itemName_".$j];
						$itemWt		= $p["itemWt_".$j];
						if ($lastId!="" && $itemName!="" && $itemWt!="") {
							$soAdnlItemRecIns = $salesOrderObj->addSOAnlItemEntries($lastId, $itemName, $itemWt);
						}	
					}
				}	
			}
		}

		if ($salesOrderRecIns) {
			if ($warning!="") {
		?>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				alert("<?=$warning;?>");
			//-->
			</SCRIPT>
		<?php
			}
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddSalesOrder);
			$sessObj->createSession("nextPage",$url_afterAddSalesOrder.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddSalesOrder;
		}
		$salesOrderRecIns		=	false;
	}
	

	# Edit Sales Order
	if ($p["editId"]!="" || $g["editId"]) {

		if ($p["editId"]) $editId = $p["editId"];
		else $editId = $g["editId"];

		$editMode = true;
		# Chk already modified
		$selUsername = $salesOrderObj->chkRecModified($editId);	
		if ($selUsername && $g["editId"]=="") {
			$err	= "<b>$selUsername has been editing this record.</b>";	
			$editMode = false;
			$editId = "";
		}

		$salesOrderRec		= $salesOrderObj->find($editId);
		//printr($salesOrderRec);
		$editSalesOrderId	= $salesOrderRec[0];
		$sessObj->createSession("salesOrderId",$editSalesOrderId);
		# Update Rec
		if ($editSalesOrderId) $updateModifiedRec = $salesOrderObj->updateModifiedRec($editSalesOrderId, $userId, 'E');

		$invoiceNo = $salesOrderRec[1];

		if ($p["editSelectionChange"]=='1' || $p["selDistributor"]=="") {
			$selDistributorId = $salesOrderRec[2];
		} else {
			$selDistributorId = $p["selDistributor"];
		}	
		
		$lastDate	= dateFormat($salesOrderRec[6]);
		$extended	= $salesOrderRec[7];
		if ($extended=='E') $extendedChk = "Checked";
		$productPriceRateListId = $salesOrderRec[8];
		$stateId	= $salesOrderRec[9];
		$distMgnRateListId = $salesOrderRec[10];
		$selCityId	= $salesOrderRec[11];
		$taxType	= $salesOrderRec[12];
		$billingForm	= $salesOrderRec[13];
		$taxAmount	= $salesOrderRec[14];
		$soRemark	= stripSlash($salesOrderRec[15]);

		$netWt		= $salesOrderRec[17];
		$grossWt	= $salesOrderRec[18];
		$numBox		= $salesOrderRec[19];
		$invoiceType	= $salesOrderRec[20];
		$additionalItemTotalWt = $salesOrderRec[22];
		$poNo		= $salesOrderRec[23];
		$selAreaId	= $salesOrderRec[24];
		$invoiceDate	= dateFormat($salesOrderRec[3]);
		$poDate		= ($salesOrderRec[25]!='0000-00-00')?dateFormat($salesOrderRec[25]):"";
				
		$challanNo	= $salesOrderRec[26];
		$challanDate	= ($salesOrderRec[27]!='0000-00-00')?dateFormat($salesOrderRec[27]):"";
		
		$discountChk  = ($salesOrderRec[28]=='Y')?"checked":"";
		$discountRemark  = $salesOrderRec[29];
		$discountPercent = $salesOrderRec[30];
		$discountAmt	 = $salesOrderRec[31];

		$octroiExempted = $salesOrderRec[34];
		$oecNo		= $salesOrderRec[35];				
		$oecValidDate		= ($salesOrderRec[36]!='0000-00-00')?dateFormat($salesOrderRec[36]):"";
		$oecIssuedDate		= ($salesOrderRec[37]!='0000-00-00')?dateFormat($salesOrderRec[37]):"";
		
		$proformaInvoiceNo	= ($salesOrderRec[39]!=0)?$salesOrderRec[39]:"";
		$proformaInvoiceDate	= ($salesOrderRec[40]!='0000-00-00')?dateFormat($salesOrderRec[40]):"";
		$sampleInvoiceNo	= ($salesOrderRec[41]!=0)?$salesOrderRec[41]:"";
		$sampleInvoiceDate	= ($salesOrderRec[42]!='0000-00-00')?dateFormat($salesOrderRec[42]):"";
		$entryDate		= ($salesOrderRec[43]!='0000-00-00')?dateFormat($salesOrderRec[43]):""; 
		$docketNo		= $salesOrderRec[44];

		$exDutyActive		= $salesOrderRec[45];
		$eduCessPercent		= $salesOrderRec[46];
		$eduCessRLId		= $salesOrderRec[47];
		$secEduCessPercent	= $salesOrderRec[48];
		$secEduCessRLId		= $salesOrderRec[49];
		$transportChargeChk	= ($salesOrderRec[50]=='Y')?"checked":"";
		$transportChargeAmt	= $salesOrderRec[51];
		$billingType		= $salesOrderRec[52];
		$exportEnabled = ($billingType=="E")?"Y":"N";
		$toPayChk			= ($salesOrderRec[54]=='Y')?"checked":"";
		$company=$salesOrderRec[55];
		$unit=$salesOrderRec[56];
	
		if ($invoiceType=='S') $invoiceNo = $sampleInvoiceNo;
		# -----------------------------------------------------		
		if ($invoiceNo==0) $invoiceNo = $salesOrderObj->getNextInvoiceNo();		
		$selTransporter	= $salesOrderRec[21];
		# -----------------------------------------------------

		if ($additionalItemTotalWt!="" && $invoiceType=='S') {
			$soAdnlItemRecs = $salesOrderObj->fetchAllAdnlItem($editSalesOrderId);
		}

		# Fetch all sales order Entry records
		$salesOrderItemRecs = $salesOrderObj->fetchAllSalesOrderItem($editSalesOrderId);
	}


	#Update Record // Save & Confirm
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveAndConfirm"]) {

		$soConfirmed = false;	
		if ($p["cmdSaveAndConfirm"]!="") $soConfirmed = true;

		$salesOrderId 		= $p["hidSalesOrderId"];
		$itemCount		= $p["hidTableRowCount"];
		$selDistributorId	= $p["selDistributor"];				
		$lastDate		= mysqlDateFormat($p["lastDate"]);
		$dateExtended		= $p["dateExtended"];
		$productPriceRateListId = $p["productPriceRateList"];
		$selState		= $p["selState"];
		$totalAmount		= $p["grandTotalAmt"];
		
		$selCity		= $p["selCity"];
		$distMgnRateListId 	= $p["distMgnRateListId"];		
		$totalAmount		= $p["grandTotalAmt"];
		$taxType		= $p["taxType"];
		$billingForm		= $p["billingForm"];
		$taxRowCount		= $p["hidTaxRowCount"];
		$selTaxArr		= array();
		for ($j=0; $j<$taxRowCount; $j++) {
			$taxPercent = $p["hidTaxPercent_".$j];
			$taxAmount  = $p["taxAmount_".$j];		
			$arrVal = $taxPercent.":".$taxAmount;
			$selTaxArr[$j] = $arrVal;				
		}
		$selTax 	= implode(",",$selTaxArr);
		$grandTotalAmt	= $p["totalSOAmt"];	// Grand Total Amt	
		$totalTaxAmt	= $p["totalTaxAmt"];
		$soRemark	= addSlash(trim($p["soRemark"]));
		$netWt		= $p["netWt"];
		$grossWt	= $p["grossWt"];
		$numBox		= $p["numBox"];
		$invoiceType	= $p["invoiceType"];
		$additionalItemTotalWt = $p["additionalItemTotalWt"];	
		$adnlRowCount	= $p["hidItemTbleRowCount"];
		$poNo		= trim($p["poNo"]);
		$selArea	= $p["selArea"];
		$invoiceDate	= mysqlDateFormat($p["invoiceDate"]);

		$poDate		= mysqlDateFormat($p["poDate"]);
		$challanNo	= trim($p["challanNo"]);
		$challanDate	= mysqlDateFormat($p["challanDate"]);

		$discount	= $p["discount"];
		$discountRemark = $p["discountRemark"];
		$discountPercent = $p["discountPercent"];
		$discountAmt	= $p["discountAmt"];

		$octroiExempted = $p["octroiExempted"];
		$oecNo		= $p["oecNo"];
		$oecValidDate	= mysqlDateFormat($p["oecValidDate"]);
		$oecIssuedDate	= mysqlDateFormat($p["oecIssuedDate"]);

		$selTax		= ($invoiceType=='T')?$selTax:"";

		$proformaInvoiceNo	= $p["proformaInvoiceNo"];
		$proformaInvoiceDate	= mysqlDateFormat($p["proformaInvoiceDate"]);
		$sampleInvoiceNo	= $p["sampleInvoiceNo"];
		$sampleInvoiceDate	= mysqlDateFormat($p["sampleInvoiceDate"]);
		//Round off Calculation
		$roundVal = $salesOrderObj->getRoundoffVal($grandTotalAmt);
		
		$entryDate		= mysqlDateFormat($p["entryDate"]);
		if (!$soConfirmed) $invoiceDate = $entryDate;
		$soYear	 = date("Y", strtotime($invoiceDate));	
		
		# ------------------------ Confirm
		$selTransporter		= $p["selTransporter"];
		$transporterRateListId 	= $p["transporterRateListId"];
		$transOtherChargeRateListId = $p["transOtherChargeRateListId"];
		$dateExtended		= $p["dateExtended"];
		$invoiceNo 		= $p["invoiceNo"];
		$soTotalAmt = ($invoiceType=='T')?round($grandTotalAmt+$roundVal):100;
		$toPay		= ($p["toPay"]!="")?"Y":"N";
		# --------------

		$exDutyActive 		= $p["hidExDutyActive"];
		$eduCessPercent		= $p["hidEduCess"];
		$eduCessRLId		= $p["hidEduCessRLId"];
		$secEduCessPercent	= $p["hidSecEduCess"];
		$secEduCessRLId		= $p["hidSecEduCessRLId"];
		$totExDuty		= $p["totExDutyAmt"];
		$totEduCess		= $p["totEduCess"];
		$totSecEduCess		= $p["totSecEduCess"];
		$totCentralExDuty	= $p["grandTotCExDuty"];

		$transChargeActive	= $p["chbTransCharge"];
		$transportCharge	= $p["transportCharge"];
	
		$billingType		= $p["billingType"];
		$company	= $p["company"];
		$unit	= $p["unit"];
						
		if ($salesOrderId!="" && $selDistributorId!="") 
		{
			$salesOrderRecUptd = $salesOrderObj->updateSalesOrder($salesOrderId, $selDistributorId, $lastDate, $dateExtended, $productPriceRateListId, $selState, $totalAmount, $taxType, $totalTaxAmt, $grandTotalAmt, $distMgnRateListId, $selCity, $billingForm, $selTax, $soRemark, $netWt, $grossWt, $numBox, $roundVal, $invoiceType, $additionalItemTotalWt, $poNo, $selArea, $invoiceDate, $poDate, $challanNo, $challanDate, $discount, $discountRemark, $discountPercent, $discountAmt, $octroiExempted, $oecNo, $oecValidDate, $oecIssuedDate, $proformaInvoiceNo, $proformaInvoiceDate, $sampleInvoiceNo, $sampleInvoiceDate, $entryDate, $soYear, $exDutyActive, $eduCessPercent, $eduCessRLId, $secEduCessPercent, $secEduCessRLId, $totExDuty, $totEduCess, $totSecEduCess, $totCentralExDuty, $transChargeActive, $transportCharge, $billingType, $toPay,$company,$unit);
			
			if ($salesOrderRecUptd) {
				# Update Rec
				$updateModifiedRec	= $salesOrderObj->updateModifiedRec($salesOrderId, '', 'U');
			}
			$salesOrderEntryId = "";
			for ($i=0; $i<$itemCount; $i++) {
			  	$status = $p["status_".$i];
			  	$salesOrderEntryId = $p["salesOrderEntryId_".$i];
				$selProductId	=	$p["selProduct_".$i];
				$unitPrice	=	trim($p["unitPrice_".$i]);
				$quantity	=	trim($p["quantity_".$i]);
				$totalAmt	=	$p["totalAmount_".$i];
				$selMcPkgId	=	$p["selMcPkg_".$i];
				$mcPack		=	$p["mcPack_".$i];
				$loosePack	=	$p["loosePack_".$i];
				$distMgnStateEntryId = $p["distMgnStateEntryId_".$i];
				$taxPercent	= $p["taxPercent_".$i];
				$pGrossWt	= $p["pGrossWt_".$i];
				$pMCPkgGrossWt	= $p["pMCPkgGrossWt_".$i];
				$freePkts	= $p["freePkts_".$i];
				$basicRate	= $p["basicRate_".$i];

				$exDutyPercent	= $p["exciseDuty_".$i];
				$exDutyAmt	= $p["excDutyAmt_".$i];
				$exDutyMasterId	= $p["excDutyEntryId_".$i];

				$eduCessAmt	= $p["eduCessAmt_".$i];
				$secEduCessAmt	= $p["secEduCessAmt_".$i];
				$taxAmt			= $p["taxAmt_".$i];
				$mcPkgWtId		= $p["hidMCPkgWtId_".$i];

			   if ($status=='N' && $salesOrderEntryId!="") {
				$deleteSalesOrderEntryRec = $salesOrderObj->deleteSalesOrderEntryRec($salesOrderEntryId);
			   }
			    if ($status!='N') {					
				if ($salesOrderId!="" && $selProductId!="" && $salesOrderEntryId=="" && ($quantity!="" || $freePkts!="")) {
					/* If New product Added then insert Record */
					$salesOrderItemsIns = $salesOrderObj->addSalesOrderEntries($salesOrderId, $selProductId, $unitPrice, $quantity, $totalAmt, $selMcPkgId, $mcPack, $loosePack, $distMgnStateEntryId, $taxPercent, $pGrossWt, $pMCPkgGrossWt, $freePkts, $basicRate, $exDutyPercent, $exDutyAmt, $exDutyMasterId, $eduCessAmt, $secEduCessAmt, $taxAmt, $mcPkgWtId);
				} else if ($salesOrderEntryId!="" && $selProductId!="" && ($quantity!="" || $freePkts!="")) {
					/* If existing Update */
					$updateSalesOrderEntries = $salesOrderObj->updateSalesOrderentries($salesOrderEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $selMcPkgId, $mcPack, $loosePack, $distMgnStateEntryId, $taxPercent, $pGrossWt, $pMCPkgGrossWt, $freePkts, $basicRate, $exDutyPercent, $exDutyAmt, $exDutyMasterId, $eduCessAmt, $secEduCessAmt, $taxAmt, $mcPkgWtId);					
				}
                           }
			}

			# Additional Item
			$delAdnlItem = $salesOrderObj->delAdnlItem($salesOrderId);
			if ($additionalItemTotalWt!="" && $invoiceType=='S') {
				for ($j=0; $j<$adnlRowCount; $j++) {
					$status = $p["status_".$j];
					if ($status!='N') {
						$itemName	= $p["itemName_".$j];
						$itemWt		= $p["itemWt_".$j];
						if ($salesOrderId!="" && $itemName!="" && $itemWt!="") {
							$soAdnlItemRecIns = $salesOrderObj->addSOAnlItemEntries($salesOrderId, $itemName, $itemWt);
						}	
					}
				}	
			}

		# Update SO Despatch Details
		if ($salesOrderRecUptd && $soConfirmed) {
			$isComplete = 'C';
			$updateSODespatchRec = $salesOrderObj->updateSODespatchDetails($salesOrderId, $lastDate, $isComplete, $selTransporter,  $transporterRateListId, $dateExtended, $transOtherChargeRateListId, $invoiceNo, $invoiceDate, $invoiceType);
			if ($updateSODespatchRec) {
				# Get Dist Account Id
				$distAccountId = $salesOrderObj->distributorAccountRec($salesOrderId);
				if ($updateSODespatchRec && $isComplete!="" && $distAccountId=="" && $invoiceType=='T') {
					# Get Common Reason Id
					list($defaultCommonReasonId, $dcrCOD, $dcReasonName) = $distributorAccountObj->defaultCommonReason("SI");
					# Add to dist acc
					$valueDate = $invoiceDate;
					$distributorAccountRecIns = $distributorAccountObj->addDistributorAccount($invoiceDate, $selDistributorId, $soTotalAmt, 'D', "Sales Invoice No:$invoiceNo", $userId, $salesOrderId, '', $selCity, 'AD', $defaultCommonReasonId, 'Y', $valueDate, $soTotalAmt);
					if ($distributorAccountRecIns) {
						# Insert For Invoice Ref
						$distACId = $databaseConnect->getLastInsertedId();
						$insDistInvoice = $distributorAccountObj->insertDistAccountInvoice($distACId, $salesOrderId);
					}
				} else if ($distAccountId!="") {
					$updateDistAccount = $salesOrderObj->updateDistributorAccount($distAccountId, $soTotalAmt, $selDistributorId, $selCity);
				}
			
				/*
				if ($isComplete=='P' && $alreadyConfirmed!="") {
					# Update Changes
					$updateChanges = $changesUpdateMasterObj->updateSORec($selSOId);
	
					# Distributor Account Updation (Delete Dist Account Values)
					list($selDistributor, $billAmount, $selCoD) = $salesOrderObj->getDistributorAccountRec($selSOId);
					if ($selDistributor!="" && $billAmount!="") {	
						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
						# delete dist A/c
						$delDistributorAc = $salesOrderObj->delDistributorAccount($selSOId);
					}
				}
				*/
			} // Distributor account updation

		} // Confirmed updation ends here

		}	
		if ($salesOrderRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSalesOrderUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSalesOrder.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSalesOrderUpdate;
		}
		$salesOrderRecUptd	=	false;
	}
	

	/* Delete Sales Order */	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$salesOrderId	=	$p["delId_".$i];
			$hidSalesOrderStatus = $p["hidSalesOrderStatus_".$i];

			if ($salesOrderId!="" && $hidSalesOrderStatus!='C') {

				list($selDistributor, $billAmount, $selCoD, $distributorACId, $distACConfirmed) = $salesOrderObj->getDistAC($salesOrderId);
				if ($selDistributor!="" && $billAmount!="") {	

					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
					# delete dist A/c
					$delDistributorAc = $salesOrderObj->delDistributorAccount($salesOrderId);

					# Delete Reference Invoice
					$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($distributorACId);
				} // Dist AC Check ends here

					# Additional Item
					$delAdnlItem = $salesOrderObj->delAdnlItem($salesOrderId);
					# Delete from Sales Order Items
					$deleteSalesOrderItemRecs =	$salesOrderObj->deleteSalesOrderItemRecs($salesOrderId);
					# Delete sales order main
					$salesOrderRecDel = $salesOrderObj->deleteSalesOrder($salesOrderId);	
					if ($salesOrderRecDel) {
						# Delete Ref Inv section
						$delRefInvoiceRec = $salesOrderObj->delDistACRefInvoice($salesOrderId);
					}			
			}
		} // Loop Ends here
		if ($salesOrderRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSalesOrder);
			$sessObj->createSession("nextPage",$url_afterDelSalesOrder.$dateSelection);
		} else {
			$errDel	=	$msg_failDelSalesOrder;
		}
		$salesOrderRecDel	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
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
		//$dateFrom = date("d/m/Y");
		//$dateTill = date("d/m/Y");
		$dateC	   =	explode("/", date("d/m/Y"));
		$dateFrom   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],1,$dateC[2]));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1], date('t'), $dateC[2]));	
	}

	# Invoice Type Filter // TI, PI, SI
	if ($g["invoiceTypeFilter"]!="") $invoiceTypeFilter = $g["invoiceTypeFilter"];
	else $invoiceTypeFilter = $p["invoiceTypeFilter"];

	if ($g["distributorFilter"]!="") $distributorFilter = $g["distributorFilter"];
	else $distributorFilter = $p["distributorFilter"];
	

	# List all Sales Order
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		if ($p["cmdSearch"]) {
			$offset = 0;
			$page 	= 0;
		}
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$salesOrderRecords = $salesOrderObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $invoiceTypeFilter, $distributorFilter);
		$salesOrderSize = sizeof($salesOrderRecords);

		# For Pagination
		$fetchAllSalesOrder = $salesOrderObj->fetchAllDateRangeRecords($fromDate, $tillDate, $invoiceTypeFilter, $distributorFilter);		

		# get Dist Filter Recs
		$distributorFilterRecs	= $salesOrderObj->getDistributorList($fromDate, $tillDate, $invoiceTypeFilter);

		
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllSalesOrder);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($maxpage>1) {
		# Get Grand total Amount
		$grandTotalSOAmt = $salesOrderObj->getSOGrandTotalAmount($fromDate, $tillDate, $invoiceTypeFilter, $distributorFilter);
	}

	
	if ($editMode) {
		$salesOrderObj->getActiveProducts($distMgnRateListId, $selDistributorId, $stateId, $productPriceRateListId, $selCityId, $exportEnabled);
		$productMRPMasterRecords = $salesOrderObj->getSelProducts();

		# List all Transporter		
		$transporterRecords	= $transporterMasterObj->fetchAllRecords();
	}

	
	if ($addMode || $editMode) {
		# List all Distributor
		$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();

		if ($exportEnabled=='Y') $invBillingTypeArr = array("D"=>"Domestic", "E"=>"Export");
		else $invBillingTypeArr = array("D"=>"Domestic");
	}
	
	if($g["linkGatePass"]!="")
	{
		$gatePass=$g["linkGatePass"];
		$salesOrderId=$g["salesOrderId"];
		$updateSo=$salesOrderObj->updateGatePassStatus($salesOrderId);
		$updateGatePass=$salesOrderObj->updateGatePass($salesOrderId,$userId,$gatePass);
		if($updateGatePass!="")
		{
			$sessObj->createSession("displayMsg",$msg_succSalesOrderLink);
			$sessObj->createSession("nextPage",$url_afterUpdateSalesOrder.$dateSelection);
		}
		else {
			$editMode	=	true;
			$err		=	$msg_failSalesOrderLink;
		}
		$updateGatePass	=	false;
	}

	# Setting the mode
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;
	else 			$mode = "";	

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS 	= "libjs/SalesOrder.js"; // For Printing JS in Head SCRIPT section

	if ($editMode) $heading	= $label_editSalesOrder;
	else	       $heading	= $label_addSalesOrder;

	if ($invoiceTypeFilter=='PI' || $invoiceTypeFilter=='TI') $mainHeading = "TAX INVOICE";
	else $mainHeading = "ORDER ENTRY";
	
	list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>	
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="libjs/jquery/jquery-1.10.2.js"></script>
	<script src="libjs/jquery/jquery-ui.js"></script>
	<script src="libjs/json2.js"></script>
<form name="frmSalesOrder" id="frmSalesOrder" action="SalesOrder.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="80%" >
		<?php
		if (sizeof($salesOrderRecords)>0) {
		?>
		<tr>
			<td height="10" align="center">
			<!--<a href="OrderDispatched.php" class="link1" title="Click to manage Sales Order">Sales Order Processing</a>-->
			<a href="AssignDocketNo.php" class="link1" title="Click to manage Transporter Docket No.">Assign Docket No</a>
			</td>
		</tr>
		<tr>
			<td align="center" id="refreshMsgRow" class="err1" style="font-size:9pt;line-height:20px;">	
			</td>			
		</tr>
		<?php
		}
		?>
		<?
		if ($editMode) 
		{
		?>
		<tr>
			<TD height="5"></TD>
		</tr>
		<tr>
			<td align="center" id="timeTickerRow" class="err1" height="20" style="font-size:14pt;" onMouseover="ShowTip('Time remaining to cancel the selected record.');" onMouseout="UnTip();">	
			</td>			
		</tr>
		<?
		}
		?>
		<tr>
			<td height="5" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>	
		</tr>
		<?
		if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
									<td colspan="2">
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<?php
											if ($addMode) 
											{
											?>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<!--<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesOrder.php');">-->
													&nbsp;&nbsp;
													<!--<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSalesOrder(document.frmSalesOrder, '');">-->
													&nbsp;&nbsp;
													<!--<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" Save & Confirm " onClick="return validateSalesOrder(document.frmSalesOrder, 'C');" style="width:110px" />-->
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesOrder.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateSalesOrder(document.frmSalesOrder, '');"> &nbsp;&nbsp;
												</td>
												<?}?>
											</tr>
											<? }?>
											<input type="hidden" name="hidSalesOrderId" value="<?=$editSalesOrderId;?>">
											<!--credit Balance Row-->
											<tr>
												<td colspan="2" id="creditBalRow" align="center"></td>				  
											</tr>
											<!--credit Balance Row Ends here-->
											<tr>
												<td colspan="2" height="5"></td>				  
											</tr>
											<!-- 	Display Error Message -->
											<tr>
												<TD class="listing-item" style='line-height:normal; font-size:10px; color:red;' id="divNumExistTxt" nowrap="true" align="center" colspan="2"></TD>
											</tr>
											<tr>
												<td colspan="2" nowrap>
													<table width="200">
														<tr>
															<TD colspan="2">
																<table>
																	<TR>
																		<TD valign="top">
																			<table>
																				<TR>
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
																				</tr>
																				<?php if ($addMode) {?>
																				<input name="invoiceNo" id="invoiceNo" type="hidden" size="6"  value="<?=$editSO?>" autocomplete="off" />
																				<?php
																				if ($invoiceDate=="") $invoiceDate = date("d/m/Y");
																				?>
																				<input type="hidden" name="invoiceDate" id="invoiceDate" value="<?=$invoiceDate?>" size="8" autocomplete="off"/>
																				<?php }?>
																			</table>
																		</TD>
																		<td>
																			<table>
																				<tr>
																					<td class="fieldName" nowrap="nowrap">*Company</td>
																					<td nowrap>
																						<select name="company" id="company"  onchange="xajax_getUnit(this.value,'','');">
																							<option value="">-- Select --</option>
																							<? foreach($companyRecords as $cr=>$crName)
																								{
																									$companyId	=	$cr;
																									$companyNm	=	stripSlash($crName);
																									$sel="";
																									if(($companyId== $company) || ($company=="" && $companyId==$defaultCompany)) $sel	=	"selected";
																							?>
																							<option value="<?=$companyId?>" <?=$sel?>><?=$companyNm?></option>
																							<? }?>
																						</select>		
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap="nowrap">*Unit</td>
																					<td nowrap>
																						<select id="unit" name="unit" required>
																							<option value="">--select--</option>
																							<?php
																							($Company_Name!="")?$units=$unitRecords[$Company_Name]:$units=$unitRecords[$defaultCompany];
																							if(sizeof($units) > 0)
																							{
																								foreach($units as $untId=>$untName)
																								{
																									$unitId=$untId;
																									$unitName=$untName;
																									$sel = '';
																									if($unit == $unitId) $sel = 'selected';
																									echo '<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																								}
																							}
																							?>
																						</select>	
																					</td>
																				</tr>
																			</table>
																		</td>
																		<TD valign="top">
																			<table>
																				<TR>
																					<td class="fieldName" nowrap="true">PO Date</td>
																					<td nowrap="true">
																						<input type="text" name="poDate" id="poDate" value="<?=$poDate?>" size="8" autocomplete="off"  onchange="xajax_getPONumber(this.value,document.getElementById('company').value,document.getElementById('unit').value);"/>
																						<input type="hidden" name="number_gen_id" id="number_gen_id"/>
																						<br/><span id="divPOIdExistTxt" class="listing-item" style="line-height:normal; font-size:10px; color:red;"></span>
																					</td>
																				</TR>
																				<TR>
																					<td class="fieldName" nowrap="true">PO No.</td>
																					<td nowrap="true">
																						<input name="poNo" id="poNo" type="text" size="6" value="<?=$poNo?>" onKeyUp="xajax_chkPONumberExist(document.getElementById('poNo').value,'<?=$mode?>','<?=$editSalesOrderId?>');" autocomplete="off" />
																						<br/><span id="divPOIdExistTxt" style="line-height:normal; font-size:10px; color:red;"></span>
																					</td>
																				</TR>
																			</table>
																		</TD>
																		<td valign="top">
																			<table>
																				<?php
																				if ($addMode) {
																				?>
																				<tr>
																					<TD class="fieldName" nowrap="nowrap">*Date of Despatch</TD>
																					 <td>
																						<? if($p["lastDate"]!="") $lastDate=$p["lastDate"];?>
																						<input type="text" name="lastDate" id="lastDate" size="8" value="<?=$lastDate?>" autocomplete="off" onchange="xajax_chkValidDespatchDate(document.getElementById('lastDate').value);<? if ($editMode) {?>xajax_checkValidTransporter(document.getElementById('selTransporter').value, document.getElementById('lastDate').value); <?}?>"/>
																						<input type="hidden" name="hideLastDate" id="hideLastDate" value="<?=$lastDate?>">
																						<input type="hidden" name="validDespatchDate" id="validDespatchDate" value="<?=$lastDate?>" />
																						<input name="dateExtended" type="hidden" id="dateExtended" value="">
																					</td>
																				</tr>
																				<?php }?>
																				<tr>
																					<TD class="fieldName" nowrap="nowrap">*Order Entry Date</TD>
																					<td>
																						<?php
																						if ($entryDate=="") $entryDate = date("d/m/Y");
																						?>
																						<input type="text" name="entryDate" id="entryDate" size="8" value="<?=$entryDate?>" autocomplete="off" onchange="changeInvoiceDate(); if (document.getElementById('selDistributor').value) { xajax_getProductRecs(document.getElementById('selDistributor').value,document.getElementById('selState').value, '<?=$productPriceRateListId?>', '<?=$mode?>',document.getElementById('hidTableRowCount').value, document.getElementById('invoiceDate').value, '<?=$distMgnRateListId?>', document.getElementById('selCity').value, document.getElementById('billingType').value); xajax_getDistMgnRec(document.getElementById('selDistributor').value, '<?=$mode?>','<?=$distMgnRateListId?>', document.getElementById('invoiceDate').value);}" />
																					</td>
																				</tr>
																			</table>
																		</td>
																	</TR>
																</table>
															</TD>
														</tr>
														<tr>
															<TD colspan="3">
																<table>
																	<tr>
																		<td class="fieldName">*Distributor</td>
																		<td class="listing-item">
																			<select name="selDistributor" id="selDistributor" onchange="xajax_getDistStateList(document.getElementById('selDistributor').value,document.getElementById('selState').value, document.getElementById('billingType').value); xajax_getProductRecs(document.getElementById('selDistributor').value,'', '<?=$productPriceRateListId?>', '<?=$mode?>',document.getElementById('hidTableRowCount').value, document.getElementById('invoiceDate').value, '<?=$distMgnRateListId?>', '', document.getElementById('billingType').value); xajax_getDistMgnRec(document.getElementById('selDistributor').value, '<?=$mode?>','<?=$distMgnRateListId?>', document.getElementById('invoiceDate').value); xajax_getCityList(document.getElementById('selDistributor').value,'','', document.getElementById('billingType').value); xajax_getAreaList(document.getElementById('selDistributor').value, '', '','', document.getElementById('billingType').value); ">			
																				<option value="">-- Select --</option>
																				<?php	
																				while ($dr=$distributorResultSetObj->getRow()) {
																					$distributorId	 = $dr[0];
																					$distributorCode = stripSlash($dr[1]);
																					$distributorName = stripSlash($dr[2]);	
																					$selected = "";
																					if ($selDistributorId==$distributorId) $selected = "selected";	
																				?>
																				<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
																				<? }?>
																			</select>
																		</td>
																		<td></td>
																		<TD class="fieldName">*Billing</TD>
																		<td>
																			<select name="billingType" id="billingType">
																				<?php
																				foreach ($invBillingTypeArr as $key=>$value) 
																				{
																					$selected = ($key==$billingType)?"selected":"";
																				?>
																				<option value="<?=$key?>" <?=$selected?>><?=$value?></option>
																				<?php } ?>
																			</select>
																		</td>
																		<td></td>
																		<TD class="fieldName">*State</TD>
																		<td>
																			<select name="selState" id="selState" onchange="xajax_getProductRecs(document.getElementById('selDistributor').value, document.getElementById('selState').value, '<?=$productPriceRateListId?>', '<?=$mode?>', document.getElementById('hidTableRowCount').value, document.getElementById('invoiceDate').value, '<?=$distMgnRateListId?>','', document.getElementById('billingType').value); xajax_getCityList(document.getElementById('selDistributor').value, document.getElementById('selState').value,'', document.getElementById('billingType').value); xajax_getAreaList(document.getElementById('selDistributor').value, document.getElementById('selState').value, '','', document.getElementById('billingType').value);">
																			<option value=""> -- select-- </option>
																			</select>
																		</td>
																		<td></td>					
																		<TD class="fieldName">*City</TD>
																		<td>
																			<select name="selCity" id="selCity" onchange="xajax_getAreaList(document.getElementById('selDistributor').value, document.getElementById('selState').value, document.getElementById('selCity').value,'', document.getElementById('billingType').value); xajax_getProductRecs(document.getElementById('selDistributor').value, document.getElementById('selState').value, '<?=$productPriceRateListId?>', '<?=$mode?>', document.getElementById('hidTableRowCount').value, document.getElementById('invoiceDate').value, '<?=$distMgnRateListId?>', document.getElementById('selCity').value, document.getElementById('billingType').value);">
																				<option value="">-- Select --</option>
																			</select>
																		</td>
																		<TD class="fieldName">Area</TD>
																		<td>
																			<select name="selArea" id="selArea">
																				<option value="">-- Select All --</option>
																			</select>
																		</td>
																		<td class="fieldName">
																			<table>
																				<TR>
																					<TD class="fieldName">Discount</TD>
																					<td>
																						<input type="checkbox" name="discount" id="discount" value="Y" class="chkBox" onclick="addDiscountRow('<?=$discountRemark?>', '<?=$discountPercent?>', '<?=$discountAmt?>');" <?=$discountChk?> />
																					</td>	
																				</TR>
																			</table>
																		</td>
																		<td class="fieldName">
																			<table>
																				<TR>
																					<TD class="fieldName" nowrap="true">Freight Charge</TD>
																					<td>
																						<input type="checkbox" name="chbTransCharge" id="chbTransCharge" value="Y" class="chkBox" onclick="addTransportChargeRow('<?=$transportChargeAmt?>', 1);" <?=$transportChargeChk?> />
																					</td>	
																				</TR>
																			</table>
																		</td>
																	</tr>
																</table>
															</TD>
														</tr>
												   </table>
												</td>
											</tr>
											<tr>
												<td colspan="2" height="5"></td>
											</tr>					
											<!--  Dynamic Row Starting Here-->
											<tr>
												<TD style="padding-left:10px;padding-right:10px;">
													<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddSOItem">
														<tr bgcolor="#f2f2f2" align="center">
															<td class="listing-head">Sr.<br>No</td> 
															<td class="listing-head">Product</td>
															<td class="listing-head">Total Pkts</td>
															<td class="listing-head">Pkts<br>Under Scheme</td>
															<td class="listing-head">MC Pkg</td>
															<td class="listing-head">M/C</td>
															<td class="listing-head">Loose Packs</td>
															<td class="listing-head">MRP</td>	
															<td class="listing-head" nowrap>Rate<br>(In Rs.)</td>				
															<td class="listing-head">Excise Duty<br>(%)</td>				
															<td class="listing-head">Chapter/<br>Subheading</td>
															<td class="listing-head">Total<br>(In Rs.)</td>
															<td></td>
														</tr>			
														
														<tr bgcolor="#FFFFFF" align="center" id="totRowId">
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="true">Sub-Total:</td>
															<td>			
																<input name="grandTotalAmt" type="text" id="grandTotalAmt" size="8" style="text-align:right;border:none;font-weight:bold;" readonly value="<?=$totalAmount;?>">
															</td>
															<td></td>
														</tr>			
														
														
														
														<tr bgcolor="#FFFFFF" id="grandTotalRow">						
															<td height='30' class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" align="right" colspan="2">Gr.Total</td>				
															<td nowrap="true" align="center">
																<input name="grTotPkts" type="text" id="grTotPkts" size="8" style="text-align:right;border:none;font-weight:bold;" readonly value="<?=$grTotPkts;?>">
															</td>
															<td>
																<input name="grTotPktsUnderScheme" type="text" id="grTotPktsUnderScheme" size="5" style="text-align:right;border:none;font-weight:bold;" readonly value="<?=$grTotPktsUnderScheme;?>">
															</td>
															<td>&nbsp;</td>
															<td>
																<input name="grTotMC" type="text" id="grTotMC" size="8" style="text-align:right;border:none;font-weight:bold;" readonly value="<?=$grTotMC;?>">
															</td>
															<td>
																<input name="grTotLP" type="text" id="grTotLP" size="8" style="text-align:right;border:none;font-weight:bold;" readonly value="<?=$grTotLP;?>">
															</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right">
																<input name="totalSOAmt" type="text" id="totalSOAmt" size="8" style="text-align:right;border:none;font-weight:bold;" readonly value="<?=$totalSOAmt;?>">
															</td>
															<td></td>
														</tr>
													</table>	
												</TD>
											</tr>
											<!--  Hidden Fields-->
											<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize;?>">
											<input type='hidden' name='distMgnRateListId' id='distMgnRateListId' value="<?=$distMgnRateListId?>">
											<input type='hidden' name='taxType' id='taxType' value="<?=$taxType?>">
											<input type='hidden' name='billingForm' id='billingForm' value="<?=$billingForm?>">
											<input type='hidden' name="hidTaxRowCount" id="hidTaxRowCount" value="0" readonly>
											<input type='hidden' name="totalTaxAmt" id="totalTaxAmt" value="<?=$taxAmount?>">
											<input type='hidden' name="octroiExempted" id="octroiExempted" value="<?=$octroiExempted?>">
											<input type='hidden' name="hidExBillingForm" id="hidExBillingForm" value="" readonly>
											<!--  Dynamic Row Ends Here-->
											<tr><TD height="5"></TD></tr>
											<tr>
												<TD style="padding-left:10px;padding-right:10px;">
													<a href="###" id='addRow' onclick="javascript:addNewSOItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
												</TD>
											</tr>
											<tr><TD></TD></tr>
											<!--  Dynamic Additional Item Row Starting Here-->
											<tr id="additionalItemRow">
												<TD style="padding-left:10px;padding-right:10px;">
													<table>
														<TR>
															<TD>
																<fieldset>
																<legend class="listing-item">Additional Item</legend>
																	<table>
																		<TR>
																			<TD>
																				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblSOAdditionalItem">
																					<tr bgcolor="#f2f2f2" align="center">
																						<td class="listing-head">Item</td>
																						<td class="listing-head">Wt (Kg)</td>
																						<td></td>
																					</tr>
																					<tr bgcolor="white">
																						<td class="listing-head" style="padding-left:5px;padding-right:5px;">Total Wt:</td>
																						<td class="listing-head">
																							<input type='text' name="additionalItemTotalWt" id="additionalItemTotalWt" value="<?=$additionalItemTotalWt?>" size="6" readonly="true" style="border:none;text-align:right;font-weight:bold;">
																						</td>
																						<td></td>
																					</tr>
																				</table>
																				<input type='hidden' name="hidItemTbleRowCount" id="hidItemTbleRowCount" value="">
																			</TD>
																		</TR>
																		<tr><TD height="5"></TD></tr>
																		<tr>
																			<TD style="padding-left:10px;padding-right:10px;">
																				<a href="###" id='addRow' onclick="javascript:addNewAdditionalItem();"  class="link1" title="Click here to add new additional item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add additional Item</a>
																			</TD>
																		</tr>
																	</table>	
																</fieldset>	
															</TD>
														</TR>
													</table>
												</TD>
											</tr>
											<tr>
												<td colspan="2" height="5"></td>
											</tr>
											<tr>
												<TD colspan="2" align="center">
													<fieldset>
														<table>
															<tr>
																<TD  height="20" style="padding-left:5px;padding-right:5px;">
																	<table cellpadding="0" cellspacing="0">
																		<TR>
																			<TD class="listing-head" style="font-size:11px;" nowrap="true">NET WT:&nbsp;</TD>
																			<TD class="listing-item" nowrap="true">
																				<input type='text' name="netWt" id="netWt" value="<?=$netWt?>" size="6" style="font-weight:bold;border:none;" readonly="true">
																			</TD>
																		</TR>
																	</table>
																</TD>
																<TD style="padding-left:5px;padding-right:5px;">
																	<table cellpadding="0" cellspacing="0">
																		<TR>
																			<TD class="listing-head" style="font-size:11px;" nowrap="true">GR. WT:&nbsp;</TD>
																			<TD class="listing-item" nowrap="true">
																				<table cellpadding="0" cellspacing="0">
																					<TR>
																						<TD id="grossWtRow">
																					<!--<input type='text' name="grossWt" id="grossWt" value="<?//$grossWt?>" size="4" style="font-weight:bold;text-align:right;border:none;" readonly="true" onkeyup="calcTotalGrossWt();">-->
																						</TD>
																						<td id="equalTo">&nbsp;=&nbsp;</td>
																						<td>
																							<input type='text' name="totalGrossWt" id="totalGrossWt" value="" size="6" style="font-weight:bold;text-align:right;border:none;" readonly="true">
																						</td>
																					</TR>
																				</table>
																				<!--input type='text' name="grossWt" id="grossWt" value="<?//$grossWt?>" size="4" style="font-weight:bold;text-align:right;border:none;" readonly="true" onkeyup="calcTotalGrossWt();">
																				<span class="listing-item" id="equalTo">&nbsp;=&nbsp;</span>
																				<input type='text' name="totalGrossWt" id="totalGrossWt" value="" size="6" style="font-weight:bold;text-align:right;border:none;" readonly="true"-->
																			</TD>
																		</TR>
																	</table>
																</TD>
																<TD style="padding-left:5px;padding-right:5px;">
																	<table cellpadding="0" cellspacing="0">
																		<TR>
																			<TD class="listing-head" style="font-size:11px;" nowrap="true">NO.OF BOXES:&nbsp;</TD>
																			<TD class="listing-item" nowrap="true" id="numBoxRow">
																				<!--<input type='text' name="numBox" id="numBox" value="<?//$numBox?>" size="3" style="font-weight:bold;border:none;text-align:right;" readonly="true">-->
																			</TD>
																		</TR>
																	</table>
																</TD>
															</tr>
														</table>
													</fieldset>
												</TD>
											</tr>
											<tr>
												<td colspan="2" style="padding-left:5px;padding-right:5px;">
													<table width="100%">
													<TR>
														<TD align="left">
															<table>
																<TR>
																	<TD class="fieldName">
																		Remark
																	</TD>
																	<td>
																		<textarea name="soRemark" id="soRemark" rows="4"><?=$soRemark?></textarea>
																	</td>
																</TR>
															</table>
														</TD>
														<td>&nbsp;</td>
														<td align="right" style="padding-right:10px;">
															<?php if ($editMode) {?>
															<table>
																<tr>
																	<TD>
																		<table>
																			<TR>
																				<TD valign="top">
																					<fieldset>
																					<legend class="listing-item" style="line-height:normal;">Despatch Details</legend>
																						<table>
																							<TR>
																								<td class="fieldName" nowrap="true">*Invoice No.</td>
																								<td class="listing-item" nowrap="true">
																									<input name="invoiceNo" id="invoiceNo" type="text" size="6" onKeyUp="xajax_chkSONumberExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('invoiceDate').value, document.getElementById('invoiceType').value);" value="<?=($invoiceNo!=0)?$invoiceNo:"";?>" autocomplete="off" <?=$fieldReadOnly?>/>
																									<input type="hidden" name="validInvoiceNo" id="validInvoiceNo" value="">
																									<br/>
																									<span id="divSOIdExistTxt" style='line-height:normal; font-size:10px; color:red;'></span>
																									<!-- 	 onchange="updateSOMainRec('<?=$editSalesOrderId?>',document.getElementById('invoiceDate').value);"-->
																								</td>
																								<td class="fieldName" nowrap="true">*Invoice Date</td>
																								<td nowrap="true">
																								<?php
																									if ($invoiceDate=="") $invoiceDate = date("d/m/Y");
																								?>
																								<input type="text" name="invoiceDate" id="invoiceDate" value="<?=$invoiceDate?>" size="8" onchange="xajax_chkValidInvoiceDate(document.getElementById('invoiceDate').value); if (document.getElementById('selDistributor').value) { xajax_getProductRecs(document.getElementById('selDistributor').value,document.getElementById('selState').value, '<?=$productPriceRateListId?>', '<?=$mode?>',document.getElementById('hidTableRowCount').value, document.getElementById('invoiceDate').value, '<?=$distMgnRateListId?>', document.getElementById('selCity').value, document.getElementById('billingType').value); xajax_getDistMgnRec(document.getElementById('selDistributor').value, '<?=$mode?>','<?=$distMgnRateListId?>', document.getElementById('invoiceDate').value);}" autocomplete="off" <?=$fieldReadOnly?> />
																								<input type="hidden" name="validInvoiceDate" id="validInvoiceDate" value="" />
																								</td>
																							</TR>
																							<TR>
																								<TD class="fieldName" nowrap="nowrap">*Date of Despatch</TD>
																								<td>
																									<? if($p["lastDate"]!="") $lastDate=$p["lastDate"];?>
																									<input type="text" name="lastDate" id="lastDate" size="8" value="<?=$lastDate?>" autocomplete="off" onchange="xajax_chkValidDespatchDate(document.getElementById('lastDate').value);<? if ($editMode) {?>xajax_checkValidTransporter(document.getElementById('selTransporter').value, document.getElementById('lastDate').value); <?}?>"/>
																									<input type="hidden" name="hideLastDate" id="hideLastDate" value="<?=$lastDate?>">
																									<input type="hidden" name="validDespatchDate" id="validDespatchDate" value="<?=$lastDate?>" />
																									<input name="dateExtended" type="hidden" id="dateExtended" value="">
																								</td>
																								<TD class="fieldName" nowrap="true">*Sent Through:</TD>
																								<td>
																									<select name="selTransporter" id="selTransporter" onchange="xajax_checkValidTransporter(document.getElementById('selTransporter').value, document.getElementById('lastDate').value);" style="width:150px;">
																										 <option value="">-- Select --</option>
																										<?php
																											foreach ($transporterRecords as $tr) {
																												$transporterId	 = $tr[0];
																												$transporterName = stripSlash($tr[2]);	
																												$selected = "";
																												if ($selTransporter==$transporterId) $selected = "selected";	
																										?>
																										<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>
																										<? }?>
																									</select>
																									<input type="hidden" name="transporterRateListId" id="transporterRateListId" value="<?=$transporterRateListId?>">
																									<input type="hidden" name="transOtherChargeRateListId" id="transOtherChargeRateListId" value="<?=$transOtherChargeRateListId?>">	
																								</td>
																								<td>
																									<table cellpadding="0" cellspacing="0">
																										<tr>
																											<td title="Transporter Payment by customer" class="fieldName" nowrap="nowrap" style='padding-left:5px;'>To Pay</td>
																											<td nowrap>
																												<input type="checkbox" name="toPay" id="toPay" value="Y" class="chkBox" <?=$toPayChk?> />
																											</td>
																										</tr>
																									</table>
																								</td>
																							</TR>
																							<?php
																							if ($docketNo) {
																							?>
																							<tr>
																								<TD class="fieldName" nowrap="true">Transporter Docket No:</TD>
																								<td class="listing-item" nowrap="true">
																									<?=$docketNo?>
																								</td>
																							</tr>
																							<? } ?>
																							<tr><TD height="10"></TD></tr>
																							<tr>
																								<TD colspan="4" align="center">
																									<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" Save & Confirm " onClick="return validateSalesOrder(document.frmSalesOrder, 'C');" style="width:110px" />
																								</TD>
																							</tr>
																						</table>
																					</fieldset>
																				</TD>
																			</TR>
																		</table>
																	</TD>
																</tr>
															</table>
															<? }?>
														</td>
													</TR>
												</table>
											</td>
										 </tr>
										<!-- OEC SECTION STARTS HERE	 -->
										<tr id="oecRow" style="display:none;">
											<TD colspan="2">
												<table>
													<TR>
														<TD class="fieldName" nowrap="true">*OEC No.</TD>
														<td>
															<input type="text" name="oecNo" id="oecNo" size="6" value="<?=$oecNo?>" autocomplete="off">
														</td>
														<td class="fieldName" nowrap="true">*Issued On</td>
														<td>
															<input type="text" name="oecIssuedDate" id="oecIssuedDate" size="8" value="<?=$oecIssuedDate?>" autocomplete="off">
														</td>
														<td class="fieldName" nowrap="true">*Valid Up To</td>
														<td>
															<input type="text" name="oecValidDate" id="oecValidDate" size="8" value="<?=$oecValidDate?>" autocomplete="off">
														</td>
													</TR>
												</table>
											</TD>
										</tr>
										<!-- OEC SECTION ENDS HERE	 -->
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>
											<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesOrder.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateSalesOrder(document.frmSalesOrder, '');">									
												&nbsp;&nbsp;
											</td>
											<?} else{?>
											<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesOrder.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateSalesOrder(document.frmSalesOrder, '');">&nbsp;&nbsp;
											</td>
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
	<?php
	}			
	# Listing Category Starts
	?>
	<tr>
		<td height="10" align="center" ></td>
	</tr>	
	<tr>
		<td>
			<div id="soRefreshList">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap>&nbsp;<?=$mainHeading?></td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td nowrap="nowrap">
													<table cellpadding="0" cellspacing="0">
														<tr>
															<td class="listing-item">From:</td>
															<td nowrap="nowrap"> 
															<?php 
															if ($dateFrom=="") $dateFrom=date("d/m/Y");
															?>
															<input type="text" name="selectFrom" id="selectFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" onchange="xajax_getDistributor(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('invoiceTypeFilter').value, '');" />
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="listing-item" nowrap="true">&nbsp;Till:</td>
															<td> 
															  <? 
															   if($dateTill=="") $dateTill=date("d/m/Y");
															  ?>
																<input type="text" name="selectTill" id="selectTill" size="8"  value="<?=$dateTill?>" autocomplete="off" onchange="xajax_getDistributor(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('invoiceTypeFilter').value, '');" />
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="listing-item" style="padding-left:2px;padding-right:5px;">Invoice Type:</td>
															<td class="listing-item">
																<select name="invoiceTypeFilter" id="invoiceTypeFilter" onchange="xajax_getDistributor(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('invoiceTypeFilter').value, '');">
																	<option value="">--Select All--</option>
																	<?php
																		foreach ($invoiceTypeArr as $itaKey=>$itaValue) {
																			$selected = ($itaKey==$invoiceTypeFilter)?"selected":""
																	?>
																	<option value="<?=$itaKey?>" <?=$selected?>><?=$itaValue?></option>
																	<?php }?>
																</select>
															</td>	
															<td class="listing-item" style="padding-left:2px;padding-right:5px;">Distributor:</td>			
															<td class="listing-item">
																<select name="distributorFilter" id="distributorFilter" style="width:120px;">
																	<? if (sizeof($distributorFilterRecs)<=0) {?>
																	<option value="">--Select All--</option>
																	<? } ?>
																	<?php
																	foreach ($distributorFilterRecs as $distFilterId=>$distName) {
																		$selected = ($distributorFilter==$distFilterId)?"selected":"";
																	?>
																	<option value="<?=$distFilterId?>" <?=$selected?>><?=$distName?></option>
																	<?php
																		}
																	?>
																</select>
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
								  <td colspan="3" align="right" style="padding-right:10px;">
									<table width="200" border="0">
									<?php
									if ($isAdmin || $reEdit) {
									?>
									<tr>
										<TD style="padding-left:10px;padding-right:10px;" align="right">
											<input type="button" name="refreshPO" value="Refresh SO" class="button" onclick="return updatePendingSO();" title="Click here to update all Pending Sales Order details. " />
										</TD>
									</tr>
									<?php
									}
									?>
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
										<td>
										<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$salesOrderSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesOrderList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
											<!--&nbsp;
											<input type="button" name="cmdGenPkngIns" id="cmdGenPkngIns" class="button" value=" Generate Packing Instruction " style="width:210px" onclick="return validateGenPkgIns('delId_', <?=$salesOrderSize;?>, <?=$userId?>);" <? if (sizeof($salesOrderRecords)==0) echo $disabled="disabled";?>>-->
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
							<td width="1" ></td>
							<td colspan="2" style="padding-left:10px;padding-right:10px;">
								<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
								<?
								if (sizeof($salesOrderRecords)>0) {
									$i = 0;
								?>
								<? if($maxpage>1){?>
									<tr  bgcolor="#f2f2f2" align="center">
										<td colspan="13" bgcolor="#FFFFFF" style="padding-right:10px;">
											<div align="right">
											<?php 				 			  
											$nav  = '';
											for ($page=1; $page<=$maxpage; $page++) {
												if ($page==$pageNo) {
														$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
												} else {
														$nav.= " <a href=\"SalesOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&invoiceTypeFilter=$invoiceTypeFilter&distributorFilter=$distributorFilter\" class=\"link1\">$page</a> ";
													//echo $nav;
												}
											}
											if ($pageNo > 1) {
												$page  = $pageNo - 1;
												$prev  = " <a href=\"SalesOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&invoiceTypeFilter=$invoiceTypeFilter&distributorFilter=$distributorFilter\"  class=\"link1\"><<</a> ";
											} else {
												$prev  = '&nbsp;'; // we're on page one, don't print previous link
												$first = '&nbsp;'; // nor the first page link
											}

											if ($pageNo < $maxpage) {
												$page = $pageNo + 1;
												$next = " <a href=\"SalesOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&invoiceTypeFilter=$invoiceTypeFilter&distributorFilter=$distributorFilter\"  class=\"link1\">>></a> ";
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
										<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;" nowrap>Invoice <br>No.</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Invoice<br/> Type</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total</td>		
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Invoice <br/>Date</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Despatch<br/>Date</td>	
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Pkg Instr.<br/>Status</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Gate Pass</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Link to Gate Pass</td>
										<td class="listing-head"></td>
										<? if($edit==true){?>
										<td class="listing-head"></td>
										<? }?>		
									</tr>
									<?php
									$totalSalesOrderAmt = 0;
									//printr($salesOrderRecords);
									foreach ($salesOrderRecords as $sor) {
										$i++;
										$salesOrderId	= $sor[0];
										$soNo		= $sor[1];
										
										$salesOrderTotalAmt 	= $sor[20];	
										$totalSalesOrderAmt += $salesOrderTotalAmt;
										$distributorName 	= $sor[5];
										$soInvoiceType		= $sor[15];
										$selInvoiceType	= ($soInvoiceType=='S')?"Sample":"Taxable";


										/*******************************************************/
										$completeStatus	= 	$sor[13];
										$selStatusId	= 	$sor[12];
										$currentDate	=	date("Y-m-d");
										$cDate		=	explode("-",$currentDate);
										$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);

										$selLastDate	= 	$sor[6]; 	
										$eDate		=	explode("-", $selLastDate);
										$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
										$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

										$dateDiff = floor(($d2-$d1)/86400);
										//$calcDate = dateDiff(strtotime($currentDate), strtotime($selLastDate), 'D');
										//echo "<br>DateDiff=$dateDiff; CalcDateDiff=$calcDate<br>";
										$status = "";
										$statusFlag	=	"";
										$extended	=	$sor[7];
										
										if ($completeStatus=='CN') {
											$status	= "CANCELLED";
											$statusFlag =	'CN';
										} else if ($completeStatus=='CL') {
											$status	= "CLOSED";
											$statusFlag =	'CL';
										} else if ($extended=='E' && ($completeStatus=="" || $completeStatus=='P')) {
											//$status	= "<span class='err1'>PENDING (Extended)</span>";
											$status	= "PENDING (Extended)";
											$statusFlag =	'E';
										} else {
											/*
											if ($statusObj->findStatus($selStatusId)) {
												$status	=	$statusObj->findStatus($selStatusId);
											}
											*/
											if ($completeStatus=='C') {
												$status	= " COMPLETED ";
												$statusFlag = 'C';
											} else if ($dateDiff>0) {
												//$status = "<span class='err1'>DELAYED</span>";
												$status = "DELAYED";
												$statusFlag = 'D';
											} else {
												$status = "PENDING";
												$statusFlag = 'P';
											}
										}		
										$currentLogStatus	=	$sor[8];
										$currentLogDate		=	$sor[9];
										$dispatchLastDate	=	$sor[6];
										if ((($statusFlag=='E') || ($statusFlag=='D')) && strlen($currentLogStatus)<=1 ) {
											if ($currentLogStatus=='D' && $statusFlag=='E') {
												$statusFlag = $currentLogStatus.",".$statusFlag;
												$dispatchLastDate = $currentLogDate.",".$dispatchLastDate;	
											}
											// Log Status Update
											$logStatusUpted = $salesOrderObj->updateSalesOrderLogStatus($salesOrderId, $statusFlag, $dispatchLastDate);
										}
										/*******************************************************/
										$disabledField 	= "";
										$settledStatus	= $sor[16];
										$paidStatus	= $sor[17];
										
										# Edit status starts -------------
										/*
										$timeDiff	= $sor[23];		
										$editMints	= substr($timeDiff,-5,2);
										$timeLimit 	= number_format(($refreshTimeLimit/60),2,'.',''); // Convert to Mints
										*/
										$editedTimeInSec = ($sor[23]!="")?$sor[23]:0; // In seconds
										//echo "<br>$editedTimeInSec>=$refreshTimeLimit";
										if ($editedTimeInSec>=$refreshTimeLimit) { // Get the mints
											# Update Rec
											$updateModifiedRec = $salesOrderObj->updateModifiedRec($salesOrderId, '', 'U');
										}
										$modifiedBy	= $sor[21];
										$displayEditStatus = "";
										if ($modifiedBy!=0 && $modifiedBy!="") {
											$lockedUser = $manageusersObj->getUsername($modifiedBy);
											$displayEditStatus = "Locked by $lockedUser";
										}
										# Edit status ends here -------------

										if ($completeStatus=='C' || ($modifiedBy!=0 && $modifiedBy!="") || $completeStatus=='CN' || $completeStatus=='CL') {			
											$disabledField = "disabled";
										}

										//$areaRec	=	$areaMasterObj->find($sor[18]);
										//$areaName	=	stripSlash($areaRec[2]);

										$soCityId	= $sor[19];
										$cityRec	= $cityMasterObj->find($soCityId);
										$cityName	= stripSlash($cityRec[2]);
										
										$displayColor = "";
										if ($statusFlag=='CN' || $statusFlag=='CL') $displayColor = "gray";
										else if ($statusFlag=='C') $displayColor = "#90EE90"; // LightGreen
										else if ($statusFlag=='D') $displayColor = "#DD7500"; // LightOrange
										else if ($statusFlag=='E') $displayColor = "gray";
										else $displayColor = "white";

										$soInvoiceDate  = $sor[3];
										$proformaNo	= $sor[24];
										$sampleNo	= $sor[25];
										

										$invoiceNo = "";
										if ($soNo!=0) $invoiceNo=$soNo;
										else if ($soInvoiceType=='T') $invoiceNo = "P$proformaNo";
										else if ($soInvoiceType=='S') $invoiceNo = "S$sampleNo";

										$selInvoiceType = "";
										if ($soInvoiceType=='T' && $soNo!=0) $selInvoiceType = $invoiceTypeArr['TI'];
										else if ($soInvoiceType=='T' && $proformaNo!=0) $selInvoiceType = $invoiceTypeArr['PI'];
										else if ($soInvoiceType=='S') $selInvoiceType = $invoiceTypeArr['SI'];

										$pkgGen		= $sor[26];
										$pkgConfirmed	= $sor[27];
										//$pkgInstStatusArr = array("GNP"=>"NEW", "PGP"=>"PENDING", "PGC"=>"COMPLETED");	
										$pkgInstStatus = "";
										$pkgInstMsg    = "";
										if ($pkgConfirmed=='Y') {
											$pkgInstStatus = $pkgInstStatusArr['PGC'];
											$pkngInstBgColor = "#90EE90";
											$pkgInstMsg    = "PACKING COMPLETED";
										} else if ($pkgGen=='Y') {
											$pkgInstStatus = $pkgInstStatusArr['PGP'];
											$pkngInstBgColor = "gray";
											$pkgInstMsg    = "PACKING INSTRUCTION GENERATED & PENDING";
										} else  {
											//$pkgInstStatus = $pkgInstStatusArr['GNP'];
											$pkngInstBgColor = "white";
											$pkgInstMsg    = "Click here to generate new packing instruction.";
											$pkgInstStatus = "<a href='###' onclick='return validatePkgInstGen($salesOrderId, $userId, $i);' class='link2'>".$pkgInstStatusArr['GNP']."</a>";
										}
										
										$gatePassGen		= $sor[28];
										$gatePassConfirmed 	= $sor[29];
										$company 	= $sor[30];
										$unit 	= $sor[31];
										$number_gen_id 	= $sor[32];
										
										$gPassMsg    = "";
										$gPassStatus = "";
										$gPassBgColor = "";
										if ($gatePassConfirmed=='Y') {
											$gPassMsg    = "COMPLETED";
											$gPassStatus = "COMPLETED";
											$gPassBgColor = "#90EE90";
										} else if ($gatePassGen=='Y') {						
											$gPassMsg    = "GENERATED";
											$gPassStatus = "PENDING";
											$gPassBgColor = "gray";
										} else  {					
											$gPassMsg    = "Click here to generate new gate pass.";
											$gPassStatus = "<a href='###' onclick=\"return validateGatePassGen('$salesOrderId', '$userId', '$i', '$dateFrom', '$dateTill', '$page', '$invoiceTypeFilter','$company','$unit','$number_gen_id');\" class='link2'>GENERATE</a>";
											$gPassBgColor = "white";
										}	
										list($gatePassId,$gatePassNo)= $salesOrderObj->checkIdExistInGatepass($salesOrderId);
										if($gatePassId!="")
										{
											$gPassLink =$gatePassNo;
										}
										else
										{
											$gPassLink = "<a href='###' onclick=\"displayPopUp('$company','$unit','$salesOrderId');\" class='link2'>LINKGATEPASS</a>";
											$gPassBgColor = "white";
										}
										//echo $gatePassId;										
										?>
										<!--   bgcolor="WHITE" -->
										<tr <?=$listRowMouseOverStyle?>>
											<td width="20">
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$salesOrderId;?>" class="chkBox">
												<input type="hidden" name="hidSalesOrderStatus_<?=$i;?>" id="hidSalesOrderStatus_<?=$i;?>" value="<?=$completeStatus?>">
												<input type="hidden" name="pkgGen_<?=$i;?>" id="pkgGen_<?=$i;?>" value="<?=$pkgGen?>">
											</td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" bgcolor="<?=$displayColor?>" onMouseover="ShowTip('<?=$status?>');" onMouseout="UnTip();">
												<?=$invoiceNo?>			
											</td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$selInvoiceType;?></td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
												<?=$cityName;?>
											</td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$salesOrderTotalAmt;?></td>		
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=dateFormat($soInvoiceDate);?></td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$lastDate;?></td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center" bgcolor="<?=$pkngInstBgColor?>" onMouseover="ShowTip('<?=$pkgInstMsg?>');" onMouseout="UnTip();" id="pkgInstCol_<?=$i?>"><?=$pkgInstStatus;?></td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"  onMouseover="ShowTip('<?=$gPassMsg?>');" onMouseout="UnTip();" id="gatePassCol_<?=$i?>" bgcolor="<?=$gPassBgColor?>">
												<?=$gPassStatus;?>
											</td>
											<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
											<?=$gPassLink?>
											</td>
											<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px; line-height:normal;" nowrap="true">
												<a href="javascript:printWindow('PrintSOTaxInvoice.php?selSOId=<?=$salesOrderId?>&print=N',700,600)" class="link1" title="Click here to View Invoice">
													VIEW
												</a>
												<? if($print==true){?>
												&nbsp;/
												<a href="javascript:printWindow('PrintSOTaxInvoice.php?selSOId=<?=$salesOrderId?>&print=Y',700,600)" class="link1" title="Click here to Print the Invoice">
													PRINT
												</a>
												<? }?>
												<? if($print==true){?>
												&nbsp;
												<!--
												<a href="javascript:printWindow('PrintSOExciseInvoice.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to Print the Excise Invoice">
													EXCISE
												</a>
												&nbsp;/&nbsp;-->
												<a href="javascript:printWindow('PrintSOExciseInvoice_New.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to Print the Excise Invoice">
													EXCISE
												</a>
												<? }?>
											</td>		
											<? if($edit==true){?>
											<td class="listing-item" width="60" align="center" style="line-height:normal;font-size:8px;">
												<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$salesOrderId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SalesOrder.php';" <?=$disabledField?>>
												<?php
													if ($displayEditStatus!="") {
												?>
												<br/>
												<span class="err1" style="line-height:normal;font-size:8px;"><?=$displayEditStatus?></span>
												<? }?>
											</td>
											<? }?>		
										</tr>
										<?php
											} // Loop ends here
										?>
										<tr bgcolor="WHITE">
											<td class="listing-head" style="padding-left:5px; padding-right:5px;" align="right" colspan="5">Total:</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><?=number_format($totalSalesOrderAmt,2,'.',',');?></strong></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>	
											<td></td>												
										</tr>
										<?php 
											if ($maxpage>1 && $grandTotalSOAmt!=0) {
										?>
										<tr bgcolor="WHITE">
											<td class="listing-head" style="padding-left:5px; padding-right:5px;" align="right" colspan="5">Grand Total:</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><?=number_format($grandTotalSOAmt,2,'.',',');?></strong></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>				
										</tr>
										<?php
											}
										?>
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="">
										<input type="hidden" name="editSelectionChange" value="0">	
										<? if($maxpage>1){?>
										<tr bgcolor="#FFFFFF">
											<td colspan="13" style="padding-right:10px;">
												<div align="right">
												<?php 				 			  
												 $nav  = '';
												for ($page=1; $page<=$maxpage; $page++) 
												{
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"SalesOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&invoiceTypeFilter=$invoiceTypeFilter&distributorFilter=$distributorFilter\" class=\"link1\">$page</a> ";				
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"SalesOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&invoiceTypeFilter=$invoiceTypeFilter&distributorFilter=$distributorFilter\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"SalesOrder.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&invoiceTypeFilter=$invoiceTypeFilter&distributorFilter=$distributorFilter\"  class=\"link1\">>></a> ";
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
											}
											else 
											{
											?>
											<tr bgcolor="white">
												<td colspan="13"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
													<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$salesOrderSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesOrderList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
													<!--&nbsp;
													<input type="button" name="cmdGenPkngIns" id="cmdGenPkngIns" class="button" value=" Generate Packing Instruction " style="width:210px" onclick="return validateGenPkgIns('delId_', <?=$salesOrderSize;?>, <?=$userId?>);" <? if (sizeof($salesOrderRecords)==0) echo $disabled="disabled";?> />-->
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" >
										<input type="hidden" name="editMode" value="<?=$editMode?>" readonly />
										<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" readonly />
										<input type="hidden" name="productPriceRateList" id="productPriceRateList" value="<?=$productPriceRateListId?>" readonly />
										<input type="hidden" name="hidEduCess" id="hidEduCess" value="<?=$eduCessPercent?>" readonly />
										<input type="hidden" name="hidEduCessRLId" id="hidEduCessRLId" value="<?=$eduCessRLId?>" readonly />
										<input type="hidden" name="hidSecEduCess" id="hidSecEduCess" value="<?=$secEduCessPercent?>" readonly />
										<input type="hidden" name="hidSecEduCessRLId" id="hidSecEduCessRLId" value="<?=$secEduCessRLId?>" readonly />
										<input type="hidden" name="hidExDutyActive" id="hidExDutyActive" value="<?=$exDutyActive?>" readonly />
									</td>
								</tr>
							</table>						
						</td>
					</tr>
				</table>
			</div>
			<!-- Form fields end   -->		
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<?php
	if (sizeof($salesOrderRecords)>0) 
	{
	?>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td height="10" align="center">
			<a href="AssignDocketNo.php" class="link1" title="Click to manage Transporter Docket No.">Assign Docket No</a>
		</td>
	</tr>
	<?php
		}
	?>
		<input type='hidden' name='genPoId' id='genPoId' value="<?=$genPoId;?>" readonly />
		<input type='hidden' name='creditLimit' id='creditLimit' value="" readonly />
		<input type='hidden' name='creditPeriod' id='creditPeriod' value="" readonly />
		<input type='hidden' name='outStandAmt' id='outStandAmt' value="" readonly />
		<input type='hidden' name='cPeriodOutStandAmt' id='cPeriodOutStandAmt' value="" readonly />
		<input type='hidden' name='distributorInactive' id='distributorInactive' value="" readonly />
</table>
	
		<?php
		if ($discountChk) {
		?>
		<script language="JavaScript" type="text/javascript">
			addDiscountRow('<?=$discountRemark?>', '<?=$discountPercent?>', '<?=$discountAmt?>');
		</script>	
		<?php
			} // Discount
		?>
		<?php
		if ($transportChargeChk) {
		?>
		<script language="JavaScript" type="text/javascript">
			addTransportChargeRow('<?=$transportChargeAmt?>','');
		</script>	
		<?php
			} // Trans Charge
		?>

	<?php
		if ($addMode || $editMode) {
	?>		
	<script language="JavaScript" type="text/javascript">
		showInvRow('<?=$mode?>', '<?=$proformaInvoiceNo?>', '<?=$sampleInvoiceNo?>');
		showInvoiceType();		
	</script>
	<?php
		}
	?>
<?php
	 if ($addMode) {
?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		// For applying Product Price Rate List
		xajax_chkValidInvoiceDate(document.getElementById('invoiceDate').value);
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">	
		function addNewSOItem()
		{
			addNewSOItemRow('tblAddSOItem', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			xajax_getProductRecs(document.getElementById('selDistributor').value, document.getElementById('selState').value, '<?=$productPriceRateListId?>', '<?=$mode?>', document.getElementById('hidTableRowCount').value, document.getElementById('invoiceDate').value, '<?=$distMgnRateListId?>', document.getElementById('selCity').value, document.getElementById('billingType').value);
		}
		// Add New Aditional Item
		function addNewAdditionalItem()
		{
			addNewSOAdditionalItemRow('tblSOAdditionalItem','','');
		}	
		
	</SCRIPT>
<?php
	 } else if ($editMode) {
?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">		
		function addNewSOItem()
		{
			addNewSOItemRow('tblAddSOItem', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		}
		// Add New Aditional Item
		function addNewAdditionalItem()
		{
			addNewSOAdditionalItemRow('tblSOAdditionalItem','','');
		}
		// Set time D=300
		tickTimer(<?=$refreshTimeLimit?>, '<?=$editSalesOrderId?>');
	</SCRIPT>
<?php
	 }
?>
<? if ($addMode) {?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewSOItem();
		window.load = addNewAdditionalItem();
	</SCRIPT>
<? }?>
<?php		
	if (sizeof($salesOrderItemRecs)>0) {
?>
	<script language="JavaScript" type="text/javascript">
		xajax_getDistStateList('<?=$selDistributorId?>','<?=$stateId?>', '<?=$billingType?>');
		xajax_getCityList('<?=$selDistributorId?>', '<?=$stateId?>','<?=$selCityId?>', '<?=$billingType?>');
		xajax_getAreaList('<?=$selDistributorId?>', '<?=$stateId?>', '<?=$selCityId?>', '<?=$selAreaId?>', '<?=$billingType?>');
	</script>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<?php
		// When Edit Mode Products are loading from Top function
		$totalAmount = 0;
		$k = 0;
	foreach ($salesOrderItemRecs as $rec) {
			$salesOrderEntryId = $rec[0];
			$selProductId  = $rec[2];
			$quantity	= $rec[4];
			$unitRate	= $rec[3];
			$totalAmt	= $rec[5];
			$selMCPackId	= $rec[6];
			$numMCPack	= $rec[7];
			$numLoosePack	= $rec[8];
			$distMgnStateEntryId = $rec[9];
			$taxPercent	= $rec[10];
			if ($taxPercent==0) {
				//$taxPercent = $salesOrderObj->getDistributorWiseTax($selDistributorId, $stateId, $selProductId);
			}
			$pGrossWt	= $rec[11];
			$pMCPkgGrossWt	= $rec[12];
			if ($distMgnStateEntryId=="") {
				list($distAvgMargin,$distMgnStateEntryId) = $salesOrderObj->getDistAverageMargin($selDistributorId, $selProductId, $stateId, $distMgnRateListId, $exportEnabled);
			}
			$grandTotalAmount += $totalAmt;

			# Find MC Packs Details	---------------------------
			$mcpackingRec	= $mcpackingObj->find($selMCPackId);
			$numPacks	= $mcpackingRec[2]; 
			$productRec	= $manageProductObj->find($selProductId);
			$productNetWt	= $productRec[6];
			# Find Mc Wt
			$mcPkgWtId = $rec[22];
			if ($mcPkgWtId=="" || $mcPkgWtId==0)
			{
				$mcPkgWtId = $mcPkgWtMasterObj->getPkgWtId($selMCPackId, $productNetWt);
			}

			$mcPackageWt ="";
			$mcPackageWt 	= $mcPkgWtMasterObj->getPackageWt($selMCPackId, $productNetWt, $mcPkgWtId);			

			$productCategoryComb = "";
			$mcCombination = "";
			list($pCategoryId, $pStateId, $pGroupId) = $salesOrderObj->findProductRec($selProductId);	
			$productCategoryComb = "$pStateId,$pGroupId";
			$mcCombination	     = "$pStateId,$pGroupId,$numPacks,$mcPackageWt";
			$leftPkgRule	     = "$pStateId,$pGroupId,$productNetWt";
			list ($pLeftComb,$pRightComb) = $salesOrderObj->getPkgGroup($leftPkgRule);
			$pkgGroupComb = "";
			if ($pLeftComb!="" && $pRightComb!="") $pkgGroupComb	= "$pLeftComb:$pRightComb";

			# Get Right Packing Rule
			$rightPkgRule	    = $salesOrderObj->getRightPkgRule($leftPkgRule);	

			$freePkts	= $rec[13];
			$basicRate	= $rec[14];

			# Product MRP
			$mrp = $salesOrderObj->findProductPrice($selProductId, $productPriceRateListId, $selDistributorId, $stateId);

			$exDutyPercent	= $rec[15];
			$exDutyAmt	= $rec[16];
			$exDutyMasterId	= $rec[17];
			$exChapterSubhead = "";
			$exChapterSubhead = $salesOrderObj->getExCodeByProductId($selProductId);			
			if ($exDutyMasterId>0 && $exChapterSubhead=="") {
			   $edMRec = $salesOrderObj->getExciseDutyRec($exDutyMasterId);
			   $exChapterSubhead = 	$edMRec[0];
			}
?>		
		//addNewSOItemRow('tblAddSOItem','<?=$selProductId?>','<?=$unitRate?>','<?=$quantity?>', '<?=$totalAmt?>', '<?=$salesOrderEntryId?>', '<?=$selMCPackId?>', '<?=$numMCPack?>', '<?=$numLoosePack?>','<?=$distMgnStateEntryId?>', '<?=$taxPercent?>', '<?=$pGrossWt?>', '<?=$pMCPkgGrossWt?>','<?=$productCategoryComb?>','<?=$numPacks?>','<?=$mcPackageWt?>','<?=$mcCombination?>', '<?=$pkgGroupComb?>', '<?=$leftPkgRule?>', '<?=$rightPkgRule?>', '<?=$selDistributorId?>', '<?=$stateId?>', '<?=$productPriceRateListId?>', '<?=$mode?>', '<?=$distMgnRateListId?>', '<?=$freePkts?>', '<?=$basicRate?>', '<?=$mrp?>', '<?=$exDutyPercent?>', '<?=$exDutyAmt?>', '<?=$exDutyMasterId?>', '<?=$exChapterSubhead?>', '<?=$mcPkgWtId?>');	
	
<?php 
	$k++;
	}  // SO Loop Ends here
?>
	
	</SCRIPT>

	<script language="JavaScript" type="text/javascript">				
		setTimeout("multiplySalesOrderItem()",1500); //Multiply the Item		
	</script>
<?php
   } // SO Size Check
?>
 	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
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
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
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
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
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
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "invoiceDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "invoiceDate", 
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
 	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "challanDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "challanDate", 
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
			inputField  : "oecValidDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "oecValidDate", 
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
			inputField  : "oecIssuedDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "oecIssuedDate", 
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
			inputField  : "entryDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "entryDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<?php
		if (sizeof($soAdnlItemRecs)) {
	?>
	<script language="JavaScript">
	<?php
			foreach ($soAdnlItemRecs as $sar) {
				$adnlItemEntryId = $sar[0];
				$itemName	 = $sar[2];
				$itemWt		 = $sar[3];
	?>		
		addNewSOAdditionalItemRow('tblSOAdditionalItem','<?=$itemName?>','<?=$itemWt?>');
	<?php
			}
	?>
	</script>
	<?php
		} else if ($editMode && $invoiceType=='S') {
	?>
	<script language="JavaScript">
		addNewAdditionalItem();
	<?php
		}
	?>
	</script>
	<?php
	?>
	<?php
		if (!$addMode && !$editMode && sizeof($salesOrderRecords)>0) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = beginrefresh();
	</script>
	<? }?>
	<? if ($editMode) {?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<? if ($selTransporter) {?>
			xajax_checkValidTransporter(document.getElementById('selTransporter').value, document.getElementById('lastDate').value);
		<? }?>
		<? if ($editSalesOrderId) {?>
			xajax_chkSONumberExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('invoiceDate').value, document.getElementById('invoiceType').value);
			xajax_chkValidInvoiceDate(document.getElementById('invoiceDate').value);
		<? }?>
	</script>
	<? }?>	
	<?php
	if ($addMode || $editMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		$(document).ready(function() {
			$('#selDistributor').change(function() {
				var distributorId = $(this).val();
				xajax_getBillingType(distributorId);
			});

			$('#billingType').change(function() {
				var billingType = $(this).val();	
				var invDistributorId = document.getElementById('selDistributor').value;	
		
				xajax_getDistStateList(invDistributorId,'', billingType);
				xajax_getCityList(invDistributorId,'','', billingType); 
				xajax_getAreaList(invDistributorId, '', '','', billingType);
				xajax_getProductRecs(invDistributorId,'', '<?=$productPriceRateListId?>', '<?=$mode?>',document.getElementById('hidTableRowCount').value, document.getElementById('invoiceDate').value, '<?=$distMgnRateListId?>', '', billingType);
			});
		});	
	</script>
	<?php
	}
	?>
	</form>
	
	<div id="dialog" title="LINK GATE PASS" style="display:none" >
		
	</div>

<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>