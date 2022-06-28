<?php
require("include/include.php");
require("lib/UpdateQry_class.php");
$updateQryObj	= new UpdateQry($databaseConnect);

$update = $g["update"];

//Show Table Status like "a_test";

	$tbleType = $g["tableType"];
	if ($tbleType=="Y") {
		# Change Table Type
		$updateQryObj->changeTableType("InnoDB");
	}


	// Update Invoice Main Export Invoice Number
	if ($update=='SEIN') {
	
		$invoiceRecords = $updateQryObj->getAllShipmentInvRecs();
		$successCount = 0;
		foreach ($invoiceRecords as $ir) {
			$invoiceMainId	= $ir[0];
			$sInvoiceNo		= $ir[1];	
			$sInvoiceDate	= $ir[2];
			$iProformaNo	= $ir[3];
			$invoiceStatus 	= $ir[4];
			$exporterAlphaCode = $ir[5];
		
			$invoiceNo =  ($sInvoiceNo!=0 && $invoiceStatus=='Y')?sprintf("%02d",$sInvoiceNo):"P$iProformaNo";
			$sInvDate = ($sInvoiceNo=="" && $sInvoiceNo==0)?date('y-m-d'):$sInvoiceDate;

			$sInvYearRange = getFinancialYearRange($sInvDate);
			$exportInvNum = "";
			if (!empty($exporterAlphaCode)) $exportInvNum = $exporterAlphaCode."/".$invoiceNo."/".$sInvYearRange;
			
			if ($exportInvNum!="") 	{
				$uptd = $updateQryObj->updateShipmentInvoiceRec($invoiceMainId, $exportInvNum);
				if ($uptd) $successCount++;
			}
			//echo "<br>$exportInvNum";

		}
		echo "successCount=".$successCount;
	}



	/*
	if ($update=='CU') {
	
		$updateQryObj->updateContainer();

		$recs = $updateQryObj->getContainerRecs();
		foreach ($recs as $r) {
			$id			= $r[0];
			$cId		= $r[1];
			$selDate	= $r[2];
			$createdDate = $r[3];
			echo "<br>$id=$cId==".$selDate."==".$createdDate;
			$containerId 	= $containerObj->getNextProformaInvoiceNo();
			$updateQryObj->updateContainerRec($id, $containerId);
			//die();
		}
	}


	if ($update=='LN') {
		# Dist Master Loc Name updation		
		$distRecs = $updateQryObj->getDistRecs();
		foreach ($distRecs as $dr) {
			$distributorId = $dr[0];
			$distStateEntryId = $dr[1];
			$selLocName	 = $dr[2];
			
			$cR = $updateQryObj->getSelAreaRecs($distributorId,$distStateEntryId);
			$areaId = $cR[0];
			$areaName = $cR[1];
			$selCityName = $cR[2];
			if ($areaId==0) $areaName = $selCityName."All";
			$locationName = substr($areaName,0,3);
			echo "<br>$locationName";
			if ($selLocName=="") $updateDistMRec = $updateQryObj->updateLocationName($distStateEntryId, $locationName);
		}
	} 
	*/
	


	/*
	if ($update) {
		# VALUE DATE SECTION		
		$distAcRecs = $updateQryObj->getNotPRRecs();
		foreach ($distAcRecs as $dar) {
			$distAcId 	= $dar[0];
			$reasonId	= $dar[1];	
			$selDate	= $dar[2];
			$parentACId	= $dar[3];	
			$valueDate	= $dar[4];

			$paymentReceivedEntry = $distributorAccountObj->DefaultReasonEntry($reasonId);

			echo "<br>$distAcId, $reasonId, $paymentReceivedEntry, $selDate, ParentID=$parentACId, $valueDate";
			if ($paymentReceivedEntry=="") {
				$updateDistAC = $updateQryObj->updateDistAC($distAcId, $selDate);
				if ($updateDistAC) echo "<br>=========>$distAcId, $reasonId, $paymentReceivedEntry, $selDate --- Updated";
			}

			if ($paymentReceivedEntry=="PR" && $parentACId!="") {
				$selValDate = $updateQryObj->getParentRec($parentACId);
				$updateDistAC = $updateQryObj->updateDistAC($distAcId, $selValDate);
				echo "<br>--------------------------------------$selValDate";
			}
			

		}
	} else echo "Checking---------"
	*/
	
	/*
	# Change Table Type
		$updateQryObj->changeTableType();
	# Re-generate All distributor account entry from Sales Order
	//truncate table `t_distributor_ac_invoice`; truncate table `t_distributor_ac_chk_list`; truncate table `t_distributor_ac`;
	if ($update) {
		# Remove
		$updateQryObj->truncateDistACMainTable();
		
		# Update Master
		$updateDistributorMasterAmount = $updateQryObj->updateDistributorMasterRec();
	
		$soRecs = $updateQryObj->getConfirmedSalesOrderRecs();
		if (sizeof($soRecs)>0) {
			# Get Common Reason Id
			list($defaultCommonReasonId, $dcrCOD, $dcReasonName) = $distributorAccountObj->defaultCommonReason("SI");
			//echo "$defaultCommonReasonId, $dcrCOD, $dcReasonName";
			$i = 0;
			foreach ($soRecs as $sr) {
				$i++;
				$salesOrderId	 	= $sr[0];
				$selDistributorId 	= $sr[1];
				$invoiceDate    = $sr[2];
				$invoiceNo	= $sr[3];
				$soTotalAmt	= $sr[4];
				$createdUserId	= $sr[5];
				$selCity	= $sr[6];
				//echo "<br>$salesOrderId, $distributorId, $invoiceDate, $invoiceNum, $soTotAmt, $createdUserId, $cityId";
	
				# Add to dist acc
				$distributorAccountRecIns = $distributorAccountObj->addDistributorAccount($invoiceDate, $selDistributorId, $soTotalAmt, 'D', "Sales Invoice No:$invoiceNo", $createdUserId, $salesOrderId, '', $selCity, 'AD', $defaultCommonReasonId, 'Y');
				$distACId = "";
				if ($distributorAccountRecIns) {
					# Insert For Invoice Ref
					$distACId = $databaseConnect->getLastInsertedId();
					$insDistInvoice = $distributorAccountObj->insertDistAccountInvoice($distACId, $salesOrderId);
				}
	
			}
			echo "==>$i";
		}
	} // Update check ends here
	else echo "Access denied";
	# Re-generate Ends here	
	*/

	/*
	# For Updating in Daily pre-process Actual qty
	# Update Daily Pre-Process
	$fetchAllDPPMRecs	= $updateQryObj->getDailyPProcessRecs();
	$i = 0;
	foreach ($fetchAllDPPMRecs as $dpr) {
		$i++;
		$dailyPreProcessEntryId =  $dpr[3];
		echo "<br><b><$i>\n";
		
		$selectDate = $dpr[2];					
		$pDate	    = explode("-",$selectDate);
		$fishId			=	$dpr[1];		
		$preProcessId		=	stripSlash($dpr[4]);
		
		$processSeq = $dpr[16];
		$process = explode(",",$processSeq);
		$processFrom = $process[0];
		
		$arrivalQty		= 	$dpr[6];
		$totalQty		=	$dpr[7];
		$totalPreProcessedQty	=	$dpr[8];
		$grandTotalPreProcessesQty += $preProcessedQty; 	

		$dailyCatchEntryRecords = $dailypreprocessObj ->dailyCatchEntryArrivalWeight($fishId, $processFrom, $selectDate);
		$catchEntryWeight	= $dailyCatchEntryRecords[4];
		$totalPPMOBQty		= $dailypreprocessObj->getTotalPPMOBQty($processFrom, $selectDate);
		$todaysProductionQty 	= $dailypreprocessObj->getPkgQty($processFrom, $selectDate);
		$todaysPPMQty		= $dailypreprocessObj->getTodaysPPQty($processFrom, $selectDate);
		$todaysRPMQty		= $dailypreprocessObj->getRPMQty($processFrom, $selectDate);
		$totalCSQty 		= $dailypreprocessObj->getTotalCSQty($processFrom, $selectDate);		
		$todaysAvailableQty 	= ($totalPPMOBQty+$catchEntryWeight+$todaysPPMQty+$todaysRPMQty)-($todaysProductionQty+$totalCSQty);
		#If raw material Qty = use calc Available Qty ELSE Other wise Actual used Qty
		if ($catchEntryWeight!=0 && $todaysAvailableQty>0) $availableQty = $todaysAvailableQty;
		else $availableQty = $arrivalQty;
		$totalQty	=  $availableQty;
		//echo "<br>====>$todaysAvailableQty =  ($totalPPMOBQty+$catchEntryWeight+$todaysPPMQty+$todaysRPMQty)-($todaysProductionQty+$totalCSQty)=".($totalPPMOBQty+$catchEntryWeight+$todaysPPMQty+$todaysRPMQty)."-".($todaysProductionQty+$totalCSQty)."=$todaysTotalQty:::$dppMainId, $processEntryId";

		#To Take the Rate & Commi		
		$lanCenterId 		= $dpr[12];
		$criteria		= $dpr[13];	
		$rate			= $dpr[14];
		$commission		= $dpr[15];
		
		######################
		$processYieldRec = $dailypreprocessObj->findYieldRec($preProcessId, $lanCenterId);					
		$monthArray	=	array($processYieldRec[3], $processYieldRec[4], $processYieldRec[5], $processYieldRec[6], $processYieldRec[7], $processYieldRec[8], $processYieldRec[9], $processYieldRec[10], $processYieldRec[11], $processYieldRec[12], $processYieldRec[13], $processYieldRec[14]);
		$day	=	"";
		if ($pDate[1]<10) $day = $pDate[1]%10;
		else $day = $pDate[1];		
		$idealYield = $monthArray[$day-1];
		#################		
		//$actualYield		=	$dpr[9];
		//$actualYield		= 	number_format(abs(($totalPreProcessedQty/$availableQty)*100),2,'.',''); // With total qty
		$actualYield		= 	number_format(abs(($totalPreProcessedQty/$arrivalQty)*100),2,'.',''); // Actual used qty
		$diffYield		=	abs($actualYield-$idealYield);
		//echo "<br>YIELD=======>".$dpr[9]."=$actualYield-$idealYield::$diffYield";
		# QTY Recs
		$ppQtyRecs = $updateQryObj->getPPMQtyRecs($dailyPreProcessEntryId);
		//echo "$preProcessorQtyId, $preProcessedQty, $selectCommission, $selectRate, $actualRate, $paidStatus, $setldDate, $preProcessorId";
		$totalAmount = "";
		#Criteria Calculation 1=> From / 0=>To
		foreach ($ppQtyRecs as $ppq) {
			$preProcessorQtyId 	= $ppq[0];
			$preProcessedQty 	= $ppq[1]; 
			$selectCommission	= $ppq[2];
			$selectRate		= $ppq[3];
			$actualRate		= $ppq[4];
			$paidStatus		= $ppq[5];
			$setldDate		= $ppq[6]; 
			$preProcessorId		= $ppq[7];

			$totalPreProcessAmt = "";			
			if ($criteria==1) {
				if ($actualYield>$idealYield) {
					$totalPreProcessAmt = ($totalPreProcessedQty/($actualYield/100)) * $selectRate + $totalPreProcessedQty * $selectCommission;					
				} else {
					$totalPreProcessAmt = ($totalPreProcessedQty/($idealYield/100)) * $selectRate + $totalPreProcessedQty * $selectCommission;					
				}
			} else {
				$totalPreProcessAmt = $totalPreProcessedQty*$selectRate + $totalPreProcessedQty * $selectCommission;
				
			}
	
			$ratePerKg	= $totalPreProcessAmt/$totalPreProcessedQty;
			$amount		= $preProcessedQty * $ratePerKg;
			$totalAmount 	= number_format($amount,2,'.','');	
	
			if ($paidStatus=='Y') {					
				$dailyPreProcessUpdateRec = $updateQryObj->updatePPQtyRec($preProcessorQtyId, $totalAmount);
				//echo "<br>Amt=>$preProcessorQtyId, $totalAmount";
			}
		} // Qty Loop ends here
		//echo "<br>$dailyPreProcessEntryId, $actualYield, $diffYield, $todaysAvailableQty, $totalQty";
		# Update Entry Rec
		$updateEntryRec = $updateQryObj->updatePPEntryRec($dailyPreProcessEntryId, $actualYield, $diffYield, $todaysAvailableQty, $totalQty);
		//if ($updateEntryRec) echo "<br>Entry Table Updated";
	}
	echo "<Strong>Recs Updated</Strong>";
	# --------------------------------------<=>-------------------------------
	*/

	/*
		# Change Table Type
		$updateQryObj->changeTableType();
	*/
	/*
	# ------------- Update year in SO ---------
	$soInvoiceRecords = $updateQryObj->getSOInvoiceRecords();
	foreach ($soInvoiceRecords as $soir) {
		$soinvId 	= $soir[0];
		$soInvDate	= $soir[1];
		$soYear		= date("Y", strtotime($soInvDate));
		$uptdSOInvoice  = $updateQryObj->uptdSOInvoice($soinvId, $soYear);		
	}
	echo "Year Updated";
	*/

	# ------------- Update year in SO Ends here---------

	/*
	# ----------- For updating CITY in Product Status --------------
	$productStatusRecs = $updateQryObj->getProductStatusRecs();
	$psId = "";
	$selCity = "";
	foreach ($productStatusRecs as $psr) {
		$psId 		= $psr[0];
		$productId 	= $psr[1];
		$distributorId	= $psr[2];
		$stateId	= $psr[3];
		$distName	= $psr[4];
		$distMasterId	= $psr[5];
		# Delete
		if ($distMasterId=="") $deletePSRec = $updateQryObj->delPSRec($psId);
		$selRateListId 	= $distMarginRateListObj->latestRateList($distributorId);
		$cityId		= $updateQryObj->getDistMgnCityId($productId, $distributorId, $stateId, $selRateListId);
		$dCityId = "";
		if ($cityId=="") $dCityId =  $updateQryObj->getDistCity($distributorId, $stateId);
		$selCity = ($cityId!="")?$cityId:$dCityId;
		echo "<br>$psId, P=$productId, Dist=$distributorId:$distName<=>$distMasterId, STATE=$stateId, City=$cityId, SelCity=$selCity";
		# Update City Id	
		if ($selCity) $updatePSRec = $updateQryObj->updatePSRec($psId, $selCity);
	}	
	# ---------------------------
	*/
	/*
	# Area Demarcation Club
	$areaDemarcRecs = $updateQryObj->getAreaDemarcationRecs();
	$adId = "";	
	$zoneId = "";

	# Add Column
	$updateQryObj->addColumnInADStateTable();

	foreach ($areaDemarcRecs as  $r) {
		$adId 	= $r[0];
		$zoneId = $r[1];
		$zoneName =  $r[2];
		echo "<br>id=$adId, Zone=$zoneId:$zoneName<br>";
		$getADStateRecs = $updateQryObj->getADStateRecs($adId);
		//echo "Size=".sizeof($getADStateRecs);
		$adsRId = "";
		$updateADSRec = "";
		foreach ($getADStateRecs as $adsR) {
			$adsRId = $adsR[0];
			$mainId = $adsR[1];
			$updateADSRec = $updateQryObj->updateAdSRec($adsRId, $zoneId);
			//echo "<br>id=$adId=$mainId, SEId=$adsRId, UZone:$zoneId<br>";
		}	
		
		//$updateAreaStateRec = $updateQryObj->updateADState($adId, $zoneId);
	}
	# Remove Column
	$updateQryObj->removeColumnInADStateTable();
	$updateADT = $updateQryObj->renameADOldTable();
	if ($updateADT) echo "<br><b>UPDATED SUCCESSFULLY</b>";
	*/
	# Area Demarcation Club ------------------------------- Ends Here




	# UPDATE ENTRY DATE
	/*
	$getSORecs = $updateQryObj->getAllSORecs();	
	foreach ($getSORecs as $sor) {		
		$invoiceId 	= $sor[0];		
		$invoiceDate	= $sor[6];
		$createdOn	= $sor[5];
		$insInvoiceDate = ($createdOn!=0&&$createdOn!="")?$createdOn:$invoiceDate;
	
		$uptdSORec	= $updateQryObj->uptdSORec($invoiceId, $insInvoiceDate);	
		echo "<br>=====>$invoiceId, INVDATE=$invoiceDate, CREATEDON=$createdOn, INSERT DATE=$insInvoiceDate<br>";
	}
	*/
	//die("hhhhhhhhhhh");
	# UPDATE ENTRY DATE ENDS HERE	
	/*
	# Get SO
	$soRecs = $updateQryObj->getSalesOrderRecs();
	$soId = "";	
	foreach ($soRecs as $sor) {
		$soId = $sor[0];
		$dispatchDate = $sor[1];
		$TRMRateListId = $sor[2];
		$TOCRateListId = $sor[3];
		$transporterId = $sor[4];
		$soNum	       = $sor[5];

		$tOtherChargeType = "TOC";	
		$tRateMasterType  = "TRM";
		//echo "<br>$soId==>$dispatchDate, TRM:$TRMRateListId,  TOC:$TOCRateListId, ID:$transporterId ======";
		echo "<br>O->$soNum:::$soId==>$dispatchDate, TRM=$TRMRateListId,  TOC=$TOCRateListId, T=$transporterId ";

		# Current Transporter Rate List Id
		$cTRMRateListId = $transporterRateListObj->getValidRateList($transporterId, $tRateMasterType, $dispatchDate);
		# Current Transporter Other Charge Rate List Id
		$cTOCRateListId = $transporterRateListObj->getValidRateList($transporterId, $tOtherChargeType, $dispatchDate);
		//echo "<br>$soId==>$dispatchDate, TRM:$cTRMRateListId,  TOC:$cTOCRateListId, ID:$transporterId<br>";
		echo "<br>C->$soNum:::$soId==>$dispatchDate, TRM=$cTRMRateListId,  TOC=$cTOCRateListId, T=$transporterId  <br>";
	
		if ($TRMRateListId==0 && $TOCRateListId==0 && $cTRMRateListId!="" && $cTOCRateListId!="") {
			$updateSORLR = $updateQryObj->updateSORLRec($soId, $cTRMRateListId, $cTOCRateListId);
			echo "$soNum updated<br>";
		}
	}
	# Transporter Rate List updation Ends Here
	*/
	
	# ```````````````` dist Account Upation starts here ```````````````
	# Get Dist account recs
	/*
	$getDistAccountRecs = $updateQryObj->getDistAccountRecs();
	foreach ($getDistAccountRecs as $r) {
		$distAcId		= $r[0];
		$selDistributorId	= $r[2];
		$uptAmt			= $r[3];
		$soId			= $r[6];
		$invDescr		= $r[5];
		//echo "<br/>Main======>$distAcId,$selDistributorId,$soId";
		list($cSOId, $grandTotalAmt, $invoiceType, $invoiceNo) = $updateQryObj->getSORec($soId);
		if ($cSOId) {
			echo "<br>Ctd==>DistId=$distAcId,SO=$soId,$uptAmt=".number_format($grandTotalAmt,2,'.','')."InNO=$invoiceNo, $invDescr";
			$uptdDistAccountRec = $updateQryObj->uptdDistributorAccount($distAcId, "Sales Invoice No:$invoiceNo");
			//echo "<br>$distAcId,".number_format($grandTotalAmt,2,'.','');
			//$gtotalAmt = ($invoiceType=='T')?number_format($grandTotalAmt,2,'.',''):100;
			# Update Dist Account Amt
			//$updateDistAccount = $orderDispatchedObj->updateDistAccount($distAcId, $gtotalAmt);
		} else {
			echo "<br><b>Del===>DistId=$distAcId,$soId</b>";
			list($selDistributor, $billAmount, $selCoD) = $salesOrderObj->getDistributorAccountRec($soId);
			
			if ($selDistributor!="" && $billAmount!="") {	
				# Rollback Old Rec
				//$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
				# delete dist A/c
				//$delDistributorAc = $salesOrderObj->delDistributorAccount($soId);
			}
			
		}
	}
	*/
	
	# ```````````````` dist Account Upation ends here ```````````````
	/*
	# ~~~~~~~~~ Sales Order updation
	//update t_salesorder set proforma_no=0, proforma_date=0, sample_invoice_no=0, sample_invoice_date=0
	echo "<br>Sales Order updation<br>";
	$updateQryObj->updateMainSORec();
	$getSORecs = $updateQryObj->getAllSORecs();
	$i =0;
	$j = 0;
	foreach ($getSORecs as $sor) {
		
		$invoiceId 	= $sor[0];
		$siType 	= $sor[1];
		$invoiceDate	= $sor[6];
		$createdOn	= $sor[5];
		$insInvoiceDate = ($createdOn!=0&&$createdOn!="")?$createdOn:$invoiceDate;

		$proformaNo 	= "";
		$sampleNo	= "";

		if ($siType=='T') {
			$i++;	
			
			if ($i==1) $proformaNo = $salesOrderObj->getCurrentProformaNum($invoiceDate);
			else $proformaNo = $updateQryObj->getNextProformaNo();
			$updateProformaSO = $updateQryObj->updateSORec($invoiceId, $proformaNo, $siType, $insInvoiceDate);
			//$updateQryObj->getCurrentSampleNum($selDate);	
		} else if ($siType=='S') {
			$j++;
			
			if ($j==1) $sampleNo = $salesOrderObj->getCurrentSampleNum($invoiceDate);
			else {
				$sampleNo = $updateQryObj->getNextSampleNo();
				//echo "<br>$j=$sampleNo<br>";
			}		
			$updateProformaSO = $updateQryObj->updateSORec($invoiceId, $sampleNo, $siType, $insInvoiceDate);	
		}
	
		echo "=====$invoiceId, $siType, $invoiceDate, $i=$proformaNo, $j=$sampleNo";
	}
	*/


	# ~~~~~~~~~ Sales Order updation Ends

	/*
	$recInsert = false;
	# Get Records	
	$getRecords = $updateQryObj->getDailyRates();
		foreach ($getRecords as $r) {
			$mainId = $r[0];
			$gradeId = $r[1];
			$mktRate = $r[2];
			$declRate = $r[3];
			$countAvg = $r[4];		
			if ($mainId) 
			{				
				$insertRec = $updateQryObj->insertRec($mainId, $gradeId, $mktRate, $declRate, $countAvg);
				echo "$mainId, $gradeId, $mktRate, $declRate, $countAvg<br>";
				if ($insertRec) $recInsert = true;
			}
		}
	if ($recInsert) {
		$updateTable = $updateQryObj->updateDailyRatesTable();
		if ($updateTable) echo "<br> Table Modified";
	}
	*/

	/*
	$updated = false;
	$upateMainRec = $updateQryObj->updateDailyCatchMainTable();
	if ($upateMainRec) {
		# Get Records
		$getRecords = $updateQryObj->getMainTableRecords();
		foreach ($getRecords as $r) {
			$mainId = $r[0];
			$mainSupplier = $r[1];
			$subSupp      = $r[2];	
	
			list($subSupplierId,$supplierChallanNo) = $updateQryObj->getEntryRecords($mainId);
	
			$updateMainRec = $updateQryObj->updateMainRec($mainId, $subSupplierId, $supplierChallanNo);
			//echo "<br>Supp=$mainSupplier, Sub=$subSupp, $subSupplierId,$supplierChallanNo<br>";
		}
	
		# Get Decl Records
		$getDeclRecords = $updateQryObj->getDeclaredRecs();
		foreach ($getDeclRecords as $r) {
			$entryId	= $r[1];
			$selSubSupplierId  = $r[2];
			$updateDeclaredRec = $updateQryObj->updateDeclaredRec($entryId, $selSubSupplierId);
			if ($updateDeclaredRec) $updated = true;
		}

		if ($updated) {
			$updateMainTable = $updateQryObj->updateDailyCatchEntryTable();
		}
		if ($updateMainTable) echo " All Record Updated ";
	}
	*/
	
	

	# 
	/*
	$getRecords = $updateQryObj->getDailyCatchEntryRecords();
	$i=0;
	$updated = false;
	foreach ($getRecords as $r) {
		$i++;
		$entyId 	= $r[3];
		$subSupplier	= $r[1];
		$supplierChallanNo = 	$r[2];
		//echo "$functionId-$i<br>";
		$updateRec = $updateQryObj->updateCatchEntryRec($entyId, $subSupplier, $supplierChallanNo);	
		if ($updateRec) $updated = true;
	}	

	if ($updated) {
		$updateMainTable = $updateQryObj->updateMainTable();
	}
	if ($updateMainTable) echo " All Record Updated ";
	*/
	
	/*
		//This session we used for migrate submodule with function table
		//$mdulemanagerObj->updateRecord();
	*/

?>