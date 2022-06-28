<?php
	require("include/include.php");
	ob_start();
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;
	$selChallanNo		=	"";
	$dateFrom		=	"";
	$dateTill		=	"";
	$settledAmount		=	"";
	$duesAmount		=	"";
	$landingCenterId	=	"";
	$selectSupplier		=	"";
	$selChallanNo 		= 	"";	
	$processId		=	"";
	$settlementDate		=	"";
	$selPaid		=	"";
	$accountSettled 	= 	false;
	
	
	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	//----------------------------------------------------------
	#Check RM Confirm Enabled
	$rmConfirmed = $manageconfirmObj->isRMConfirmEnabled();

	
	
	if($g["billingCompany"]) 	$billingCompany	=	$g["billingCompany"];
	else 				$billingCompany	=	$p["billingCompany"];

	#Date From which challan SCD-> Supplier Challan Date, WCD-> WT challan Date
	if($g["dateSelectFrom"]) 	$dateSelectFrom	=	$g["dateSelectFrom"];
	else 				$dateSelectFrom	=	$p["dateSelectFrom"];
	if ($dateSelectFrom=='SCD') {
		$supplierChallanDate = "Checked";
	} else if($dateSelectFrom=='WCD')  {
		$wtChallanDate = "Checked";
	} else {
		$wtChallanDate = "Checked";
	}
	
	if($g["landingCenter"]) 	$landingCenterId	=	$g["landingCenter"];
	else 				$landingCenterId	=	$p["landingCenter"];
	
	if($g["supplier"]!="") 		$selectSupplier		=	$g["supplier"];
	else 				$selectSupplier		=	$p["supplier"];
	
	if($g["selChallan"]!="")	$selChallanNo	= 	$g["selChallan"];
	else 				$selChallanNo 	= 	$p["selChallan"];	
	
	if($g["fish"]!="") 		$selFishId		=	$g["fish"];
	else 				$selFishId		=	$p["fish"];
	
	if($g["processCode"]!="") 	$processId	=	$g["processCode"];
	else 				$processId	=	$p["processCode"];
	
	if($g["selSettlement"]!="") 	$settlementDate	=	$g["selSettlement"];
	else 				$settlementDate	=	$p["selSettlement"];

	//DWW - Declared Weight Weighment Challan No, DWS - Declared weight Supplier Challan No, EWW- Effective Weight WWeighment Challan No

	if($g["paymentMode"]!="") $paymentMode = $g["paymentMode"];
	else 			  $paymentMode = $p["paymentMode"];
	
	if($paymentMode=='DWW')	$declaredWeighNo = "Checked";
	if($paymentMode=='DWS')	$declaredSuppNo	 = "Checked";
	if($paymentMode=='EWW')	$effectiveWeighNo = "Checked";
	
	if($g["viewType"]) $viewType	=	$g["viewType"];
	else 		   $viewType	=	$p["viewType"];

	if($viewType=='SU') $summaryView 	=	"Checked";
	if($viewType=='DT') $detailedView 	=	"Checked";	

	if($g["selOption"]) $paidStatus = $g["selOption"];
	else 		    $paidStatus = $p["selOption"];

	$checked = "";
	if ($paidStatus=='Y') $settledChk = "Checked";
	if ($paidStatus=='N') $unpaid = "Checked";


	# select record between selected date
	if($g["supplyFrom"]!="" && $g["supplyTill"]!=""){
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}
	
	$fromDate	=	mysqlDateFormat($dateFrom);	
	$tillDate	=	mysqlDateFormat($dateTill);

	$pagingSelection 	=	"supplyFrom=$dateFrom&supplyTill=$dateTill&landingCenter=$landingCenterId&supplier=$selectSupplier&selChallan=$selChallanNo&fish=$selFishId&processCode=$processId&selSettlement=$settlementDate&paymentMode=$paymentMode&viewType=$viewType&selOption=$paidStatus&dateSelectFrom=$dateSelectFrom&billingCompany=$billingCompany";

	if ($p["cmdAddSupplierAccount"]!="") {
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$catchEntryId	= $p["catchEntryId_".$i];
			$selectWeight	= $p["weight_".$i];
			$selectRate	= $p["rate_".$i];
			$actualAmount	= $p["totalRate_".$i];
			$reEdited	= $p["reEdit_".$i];
			
			if ($reEdited=="" || $isAdmin==true || $reEdit==true) {
				$paid	= ($p["paid_".$i]=="")?N:$p["paid_".$i];
			} else $paid	= "";
			
			$wfishId		= $p["fishId_".$i];
			$gradeId		= $p["gradeId_".$i];
			$count			= $p["count_".$i];

			//Declared
			$sFishId		= $p["sFishId_".$i];
			
			$processCodeId		= $p["processCodeId_".$i];  //Two types lisitng
			$declCount		= $p["declCount_".$i];
			$declaredEntryId	= $p["declaredEntryId_".$i];
			$challanEntryId		= $p["challanEntryId_".$i];
			
			$rate			= $p["rate_".$i];
			$rateSetld		= $p["hidRate_".$i]; // Already Setld Rate

			$suppSetldDate		=  mysqlDateFormat($p["suppSetldDate_".$i]);

			if ($reEdited=="" || $isAdmin==true || $reEdit==true) {
				$settled	=	($p["settled_".$i]=="")?N:$p["settled_".$i];
			} else {
				$settled 	=	"";
			}
			# Summary Settlement
			if ($viewType=='SU' && ($paymentMode=='EWW'|| $paymentMode=='DWW'))
			{
				# Get Grouped WC Recs
				$weighmentGroupRecs = $supplieraccountObj->getWeighmentChallanGroupRecs($fromDate, $tillDate, $wfishId, $processCodeId, $gradeId, $count, $selectSupplier, $rateSetld, $billingCompany);
				
				if(sizeof($weighmentGroupRecs)>0)
				{
					foreach($weighmentGroupRecs as $wgr)
					{
						$gCatchEntryId = $wgr[0];
						$gSelectWeight	= $wgr[1];
						$gActualAmount = $gSelectWeight * $selectRate;
						# Update Daily Catch Entry Rec
						$catchEntryUpdateRec = $supplieraccountObj->updateCatchEntryActualAmount($gCatchEntryId, $gSelectWeight, $selectRate, $gActualAmount, $paid, $suppSetldDate);	
						$accountSettled = true;	
					}
				}
			}
			else
			{
			#-----------------------------
			# Detailed Updation
			#-----------------------------	
				if ($catchEntryId!="" && $selectWeight!="" && $selectRate!="" && $actualAmount!="" && $paymentMode!='DWS') {
					# Update Catch Entry Table
					$catchEntryUpdateRec = $supplieraccountObj->updateCatchEntryActualAmount($catchEntryId, $selectWeight, $selectRate, $actualAmount, $paid, $suppSetldDate);	
				}
			}
			
			if ($viewType=='SU' && $paymentMode=='DWS') {	
				# Get Declared Group Recs			
				$declaredGroupRecs = $supplieraccountObj->getDeclaredGroupRecs($fromDate, $tillDate, $selectSupplier, $sFishId, $processCodeId, $declCount, $dateSelectFrom, $rateSetld, $billingCompany);
				
				if(sizeof($declaredGroupRecs)>0 && $paymentMode=='DWS')
				{
					$dWDailyCatchEntryId = "";
					foreach($declaredGroupRecs as $dgr)
					{
						$declaredId  = $dgr[14];
						$dWDailyCatchEntryId = $dgr[15]; // Daily Catch Entry Id

						# Update Decl Rate 
						$catchEntryUpdateRec =  $supplieraccountObj->updateDeclaredRec($declaredId, $rate, $settled, $suppSetldDate);

						# Verify all Decl Records updated
						$checkAllSupplierChallanSettled = $supplieraccountObj->challanRecords($dWDailyCatchEntryId);
						if (!$checkAllSupplierChallanSettled) {
							$supplierActualAmount = $supplieraccountObj->calcSupplierActualAmount($dWDailyCatchEntryId);
							$paid='Y';
							$updateDailyCatchEntry = $supplieraccountObj->updateDailyCatchEntry($dWDailyCatchEntryId, $paid, $supplierActualAmount, $suppSetldDate);
						} else {
							$updateDailyCatchEntry = $supplieraccountObj->updateDailyCatchEntry($dWDailyCatchEntryId, $settled, $supplierActualAmount, $suppSetldDate);
						} // Entry Update Ends Here

						$accountSettled = true;
					}			
				}
			} # Summary Supplier Challan Ends Here
			
			if ($viewType=='DT' && $paymentMode=='DWS') {
				$catchEntryUpdateRec	=	$supplieraccountObj->updateDeclaredRec($declaredEntryId, $rate, $settled, $suppSetldDate);
				# Verify all Decl Records Settled
				$checkAllSupplierChallanSettled = $supplieraccountObj->challanRecords($challanEntryId);
				if (!$checkAllSupplierChallanSettled) {
					$supplierActualAmount = $supplieraccountObj->calcSupplierActualAmount($challanEntryId);
					$paid='Y';
					$updateDailyCatchEntry = $supplieraccountObj->updateDailyCatchEntry($challanEntryId, $paid, $supplierActualAmount, $suppSetldDate);
				} else {
					$updateDailyCatchEntry = $supplieraccountObj->updateDailyCatchEntry($challanEntryId, $settled, $supplierActualAmount, $suppSetldDate);
				}	
			} # Detailed in Supplier Challan No Ends Here
		}
		if ($catchEntryUpdateRec) {
			$accountSettled = true;
			//$sessObj->createSession("displayMsg",$msg_succUpdateSupplierSettlement);
			//$addMode=true;
			//$sessObj->createSession("nextPage",$url_afterUpdateSupplierSettlement);
		} else {
			$err	=	$msg_failUpdateSupplierSettlement;
		}
		$catchEntryUpdateRec	=	false;
	}

	if ($dateFrom!="" && $dateTill!="") {
		$landingCenterRecords	= $supplieraccountObj->fetchLandingCenterRecords($fromDate, $tillDate, $settlementDate, $dateSelectFrom,$rmConfirmed);

		$supplierRecords	= $supplieraccountObj->fetchSupplierRecords($fromDate, $tillDate, $landingCenterId, $settlementDate, $dateSelectFrom,$rmConfirmed);

		# Get Billing Comapany  Records
		$billingCompanyRecords = $supplieraccountObj->fetchBillingCompanyRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $settlementDate, $dateSelectFrom,$rmConfirmed);

		$RMChallanRecords	=  $supplieraccountObj->fetchChallanRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $settlementDate, $dateSelectFrom, $billingCompany,$rmConfirmed);

		#List All Fishes
		$fishMasterRecords	= $supplieraccountObj->fetchFishRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $settlementDate, $dateSelectFrom, $billingCompany,$rmConfirmed);

		if ($selFishId!="") {
			$processCodeRecords = $supplieraccountObj->getProcessCodeRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $selFishId, $settlementDate, $dateSelectFrom, $billingCompany,$rmConfirmed);	
		}

		#for selecting settlement  Date
		$settlementDateRecords	=	$supplieraccountObj->fetchAllDateRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	# Search for Records
	if ($p["cmdSearch"]!="" || $accountSettled==true || $pageNo!="") {
		if ($viewType=='DT' && ($paymentMode=='DWW' || $paymentMode=='EWW') ) {
			$catchEntryRecords	= $supplieraccountObj->fetchAllCatchEntryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $selFishId, $processId, $paidStatus, $offset, $limit, $rmConfirmed, $billingCompany);

			#For finding the Grand total and Pagination
			$getAllCatchEntryRecords = $supplieraccountObj->getDetailedCatchEntryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $selFishId, $processId, $paidStatus, $rmConfirmed, $billingCompany);

			#Total No.of Records (Pagination)
			$numrows = sizeof($getAllCatchEntryRecords);
		}
		if ($viewType=='SU' && ($paymentMode=='DWW' || $paymentMode=='EWW') ) {
			$catchEntryRecords = $supplieraccountObj->getCatchEntrySummaryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $selFishId, $processId, $paidStatus, $offset, $limit, $rmConfirmed, $billingCompany);
			#For finding the Grand total and Pagination
			$getAllCatchEntryRecords = $supplieraccountObj->getAllCatchEntrySummaryRecords($fromDate, $tillDate, $selChallanNo, $selectSupplier, $landingCenterId, $settlementDate, $selPaid, $selFishId, $processId, $paidStatus, $rmConfirmed, $billingCompany);
			$numrows = sizeof($getAllCatchEntryRecords);		
		}
		#supplier Declared Wt Records(Suplier Memo)
		if ($viewType=='SU' && $paymentMode=='DWS') {
			$declaredWtRecords  = $supplieraccountObj->getSupplierDeclaredWtRecords($fromDate,$tillDate, $landingCenterId,$selectSupplier,$selChallanNo,$selFishId,$processId,$paidStatus, $rmConfirmed, $dateSelectFrom, $billingCompany);
		}
		if ($viewType=='DT' && $paymentMode=='DWS') {
			$declaredWtRecords  = $supplieraccountObj->getDetailedSupplierDeclaredWtRecords($fromDate, $tillDate, $landingCenterId, $selectSupplier, $selChallanNo, $selFishId, $processId, $paidStatus, $rmConfirmed, $dateSelectFrom, $billingCompany);
		}
	}

	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------	

	#Finding Grand Total
	if (sizeof($getAllCatchEntryRecords)>0) {
		$grandTotalCatchEntryRate	=	"";
		$grandTotalSettledAmount	= 	"";
		$grandTotalDuesAmount		=	"";
		$totalRate		= "";
		$totalMarketValue	= 0;
		$totalDeclaredValue 	= 0;
		$totalLastPaidValue	= 0;
		foreach($getAllCatchEntryRecords as $cer) {	
			$entryDate		= 	$cer[3];
			$weighmentNo	=	$cer[6];
			$supplier		=	$cer[8];
			$gFishId		=	$cer[11];
			$local			=	$cer[16];
			$wastage		=	$cer[17];	
			$soft			=	$cer[18];
			$gradeCountAdj		= 	$cer[44];
			$adjustWt		=	$cer[20] + $gradeCountAdj;			
			$processCodeId		=	$cer[12];
			$processCodeRec		=	$processcodeObj->find($processCodeId);
			$processCode		=	$processCodeRec[2];			
			$netWt			=	$cer[26];		
			$declWt			=	$cer[29];
			$count			=	$cer[13];
			$selLandingCenterId	= 	$cer[7];	
			$countAverage		=	$cer[14];		
			$gradeId 		= 	$cer[37];

			# Find the Last Paid Rate
			$selLastPaidRate = $supplieraccountObj->getLastPaidRate($fromDate, $selLandingCenterId, $supplier, $gFishId, $processCodeId, $count, $countAverage, $gradeId, $viewType);
			# Daily Rate
			$dailyRateRec	= $supplieraccountObj->findDailyRate($gFishId, $processCodeId, $gradeId, $countAverage, $selLandingCenterId, $supplier, $entryDate);
			$higherCount = $dailyRateRec[9];
			$lowerCount  = $dailyRateRec[10];
			$dailyMarketRate = $dailyRateRec[6];
			$dailyDeclaredRate = $dailyRateRec[7];
			$declCountAvg      = $dailyRateRec[8];		
			$actualMarketRate = 0;	
			$actualDeclRate   = 0;
			$balCountAvg 	  = 0;
			if ($higherCount!="" && $lowerCount!="" && $countAverage!="") {
				$balCountAvg = $declCountAvg - $countAverage;
				if ($balCountAvg>0) {
					$actualMarketRate = $dailyMarketRate + ($balCountAvg*$higherCount);
					$actualDeclRate   = $dailyDeclaredRate + ($balCountAvg*$higherCount);
				} else if ($balCountAvg<0) {				
					$actualMarketRate = $dailyMarketRate - ($balCountAvg*$lowerCount);
					$actualDeclRate   = $dailyDeclaredRate- ($balCountAvg*$lowerCount);
				}
			}
			/* --------------------------------------------------*/

			$declRate		=	($actualDeclRate!=0)?$actualDeclRate:$dailyRateRec[7];	
			$paidStatus		=	$cer[35];	
			
			if ($cer[36]==0) {
				$settelementDate ="";
			} else {
				$settelementDate	=	$cer[36];
			}
			
			$selectWeight		=	$cer[32];
			$selectRate			=	$cer[33];
			$actualRate			=	$cer[34];
			
			if ($selectWeight!="" && $selectWeight!=0.00) {
				$effectiveWt	=	$selectWeight;
			} else {
				$effectiveWt	=	$cer[28];	
			}
			
			$payableWt	=	$cer[28];
			
			if ($selectRate!="" && $selectRate!=0 ) {
				$marketRate	=	$selectRate;
			} else {
				$marketRate	=	($actualMarketRate!=0)?$actualMarketRate:$dailyRateRec[6];
			}
			#Number of Grouping
			$numRecords			=	$cer[45];
			if ($summaryView) {
				$selMarketRate	=	number_format(($marketRate/$numRecords),2,'.','');
			} else {
				$selMarketRate	= 	$marketRate;
			}
			$payableRate	=	($actualMarketRate!=0)?$actualMarketRate:$dailyRateRec[6];
			
			$totalRate		=	$effectiveWt * $selMarketRate;
	
			$grandTotalCatchEntryRate	+=$totalRate;	
			
			if($paidStatus=='Y'){
				$checked	=	"Checked";
				$grandTotalSettledAmount	+= $totalRate;
			} else {
				$checked	=	"";
				$grandTotalDuesAmount	+=	$totalRate;
			}

			# Find TotalValue as per
			$mRate	= ($actualMarketRate!=0)?$actualMarketRate:$dailyRateRec[6];
			$calcMarketValue = $effectiveWt * $mRate;
			$totalMarketValue += $calcMarketValue;
			$calcDeclaredValue = $effectiveWt * $declRate;
			$totalDeclaredValue += $calcDeclaredValue;
			$calcLastPaidValue  = $effectiveWt * $selLastPaidRate;
			$totalLastPaidValue += $calcLastPaidValue;
			
		}
	}
	# End Here
	# Display heading
	if ($editMode) 	$heading	= $label_editSupplierSettlement;
	else 		$heading	= $label_addSupplierSettlement;
	
	$ON_LOAD_PRINT_JS	= "libjs/supplieraccount.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmSupplierAccount" action="SupplierAccount.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="98%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><? }?></td>
		</tr>
		<?
		if ( $editMode || $addMode ) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
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
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="99%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ><input type="hidden" name="hidReceived" value="<?=$receivedBy?>" /></td>
											</tr>
											<tr>
											<? if($editMode){?>
												<td colspan="2" align="center">&nbsp;&nbsp;</td>
											<? } else{?>
												<td align="center" colspan="2">&nbsp;&nbsp;
											<? if($edit==true){?>
													<input type="submit" name="cmdAddSupplierAccount" class="button" value=" Save" onClick="return validateSupplierAccount(document.frmSupplierAccount);">
											<? }?>&nbsp;&nbsp;
											<? if($print==true){?>	<input type="button" name="Submit" value="Print" class="button" onClick="return printWindow('PrintSupplierAccount.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&landingCenter=<?=$landingCenterId?>&supplier=<?=$selectSupplier?>&selChallan=<?=$selChallanNo?>&fish=<?=$selFishId?>&processCode=<?=$processId?>&selSettlement=<?=$settlementDate?>&paymentMode=<?=$paymentMode?>&viewType=<?=$viewType?>&selOption=<?=$paidStatus?>&rmConfirmed=<?=$rmConfirmed?>&dateSelectFrom=<?=$dateSelectFrom?>&offset=<?=$offset?>&limit=<?=$limit?>&pageNo=<?=$pageNo?>&billingCompany=<?=$billingCompany?>',700,600);"><? }?></td>
											<? } ?>
											</tr>
												<input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
											<tr>
												<td colspan="3" nowrap height="5"></td>
											</tr>
											<tr>
												<td align="center">
													<table border="0">
														<tr>
															<td nowrap align="center">
															<fieldset>
															<legend class="listing-item">Search</legend>
																<table width="300" border="0">
																	<tr>
																		<td>
																			<table width="100" border="0">
																				<tr>
																					<td>
																						<input name="selOption" type="radio" value="Y" class="chkBox" <?=$settledChk?> onclick="this.form.submit();"></td>
																					<td class="listing-item" nowrap> Settled  </td>
																					<td>
																						<input name="selOption" type="radio" class="chkBox" value="N" <?=$unpaid?> onclick="this.form.submit();"></td>
																					<td class="listing-item" nowrap> Not Settled </td>
																				</tr>
																			</table>	
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" nowrap align="center">
																			<table cellpadding="0" cellspacing="0">
																				<tr>
																					<td>
																						<table border="0" cellpadding="0" cellspacing="0">
																							<tr>
																								<td valign="top"></td>
																								<td colspan="2" valign="top" height="5"></td>
																							</tr>
																							<tr>
																								<td valign="top">
																									<table border="0" cellpadding="2" cellspacing="3">
																										<tr>
																											<td valign="top" style="padding-left:7px; padding-right:7px;">
																									<table>
																							<tr>
																								<td class="fieldName" nowrap>From</td>
																								<td nowrap="nowrap">
																								<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" onchange="submitForm('supplyFrom','supplyTill',document.frmSupplierAccount);"></td>
																							</tr>
																							<tr>
																								<td class="fieldName" nowrap> To </td>
																								<td nowrap="nowrap">
																								<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off" onchange="submitForm('supplyFrom','supplyTill',document.frmSupplierAccount);"/></td>								
																							</tr>
																							<? if($paidStatus=='Y'){?>
																							<tr>
																								<td class="fieldName" nowrap>Settlement Date</td>
																								<td>									
																									<select name="selSettlement" onchange="this.form.submit();">
																									  <option value="">-- Select All --</option>
																										<?php 
																										foreach($settlementDateRecords as $sdr) {				
																											$i++;
																											$settledDate	=	$sdr[0];
																											$Date			=	explode("-",$sdr[0]);
																											$recordDate		=	$Date[2]."/".$Date[1]."/".$Date[0];
																											$selected	=	"";
																											if ($settledDate == $settlementDate) {
																												$selected	=	"selected";
																											}
																											if ($settledDate!=0000-00-00) {
																										?>
																										<option value="<?=$settledDate;?>" <?=$selected;?> ><?=$recordDate;?> </option>
																										<?
																											}
																										}
																										?>
																									</select>									
																								</td>
																							</tr>
																							<? }?>
																							<tr>
																								<TD colspan="2">
																									<table>
																										<TR>
																											<TD>
																											<fieldset>
																											<legend class="listing-item">Date Select From </legend>
																												<table width="200" border="0">
																													<tr>
																														<td>
																															<table width="60" border="0">
																																<tr>
																																	<td><input name="dateSelectFrom" type="radio" value="SCD" onclick="this.form.submit();" <?=$supplierChallanDate?> class="chkBox"></td>
																																	<td nowrap class="listing-item">
																																			Supplier Date
																																	</td>
																																</tr>
																															</table>
																														</td>
																														<td>
																															<table width="100" border="0">
																																<tr>
																																	<td><input name="dateSelectFrom" type="radio" value="WCD" onClick="this.form.submit();" <?=$wtChallanDate?> class="chkBox"></td>
																																	<td nowrap class="listing-item">Wt Challan Date</td>
																																</tr>
																															</table>
																														</td>
																													</tr>
																												</table>
																											</fieldset>
																										</TD>
																									</TR>
																								</table>
																							</TD>
																						</tr>
																					</table>
																				</td>
																				<td valign="top">
																					<table border="0">
																						<tr>
																							<td class="fieldName" nowrap>Landing Center</td>
																							<td>
																								<select name="landingCenter" id="landingCenter" onchange="this.form.submit();">
																									<option value="">-- Select All --</option>
																									<?
																									foreach($landingCenterRecords as $fr)
																									{
																										$centerId	=	$fr[0];
																										$centerName	=	stripSlash($fr[1]);
																										
																										$selected="";
																										if($centerId	== $landingCenterId)
																										{
																										$selected	=	"selected";
																										}
																										
																									?>
																									<option value="<?=$centerId?>" <?=$selected?>>
																									<?=$centerName?>
																									</option>
																									<? } ?>
																							  </select>
																							</td>
																						</tr>
																						<tr>
																							<td class="fieldName" nowrap>Supplier</td>
																							<td>
																								<select name="supplier" onchange="this.form.submit();">
																									<option value="">-- Select All --</option>
																									 <?php
																									 foreach($supplierRecords as $fr)
																									 {
																										$supplierId	=	$fr[0];
																										$supplierName	=	stripSlash($fr[2]);
																										$selected	=	"";
																										if( $supplierId == $selectSupplier){
																											$selected	=	"selected";
																										}
																									 ?>
																									 <option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
																									 <? } ?>
																								</select>
																							</td>
																						</tr>
																						<?php
																							//if (sizeof($supplierRecords)>0) {
																						?>
																						<tr>
																							<TD class="fieldName" nowrap="true">Billing Company:</TD>
																							<td>
																								<select name="billingCompany" onchange="this.form.submit();">		
																									<option value="">--Select--</option>
																									<?
																									foreach ($billingCompanyRecords as $bcr) {
																										$billingCompanyId	= $bcr[0];
																										$displayCName		= $bcr[1];
																										$selected = "";
																										if ($billingCompanyId==$billingCompany) $selected = "selected";
																									?>
																									<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
																									<?	
																									}	
																									?>
																								</select>
																							</td>
																						</tr>
																						<?php
																							//}
																						?>
																						<tr>
																							<td class="fieldName" nowrap>RM Challan No </td>
																							<td>
																								<select name="selChallan" onchange="this.form.submit();">
																									<option value="">-- Select All --</option>
																									  <?php
																									  foreach ($RMChallanRecords as $cr) {
																										$challanId	= $cr[0];
																										$challanNo	= $cr[1];
																										$alphaCode	= $cr[2];
																										$displayCNum	= $alphaCode.$challanNo;
																										$selected	=	"";
																										if ($selChallanNo==$challanId) $selected = "selected";
																										?>
																									  <option value="<?=$challanId?>" <?=$selected?>><?=$displayCNum?></option>
																									  <? }?>
																								</select>
																							</td>
																						</tr>
																					</table>
																				</td>
																				<td valign="top" style="padding-left:7px;">
																					<table border="0">
																						<tr>
																							<td class="fieldName" nowrap>Fish</td>
																							<td>
																								<select name="fish" onchange="this.form.submit();">
																									<option value="">--Select All--</option>
																									<?
																									foreach($fishMasterRecords as $fr)
																									{
																										$Id		=	$fr[0];
																										$fishName	=	stripSlash($fr[1]);
																										$selected	=	"";
																										if( $selFishId==$Id) $selected	="selected";
																							
																									?>
																									<option value="<?=$Id?>" <?=$selected?>><?=$fishName?>
																									</option>
																									<? }?>
																								</select>
																							</td>
																						 </tr>
																						<tr>
																							<td class="fieldName" nowrap>Process Code </td>
																							<td>
																								<select name="processCode" id="processCode" onchange="this.form.submit();">
																									<option value="">-- Select All--</option>
																									 <?
																										foreach ($processCodeRecords as $fl)
																											{
																												$processCodeId		=	$fl[0];
																												$processCode		=	$fl[1];
																												$selected	=	"";
																												if( $processId==$processCodeId){
																												$selected	=	"selected";
																												}
																									?>
																									<option value="<?=$processCodeId;?>" <?=$selected;?> >
																									 <?=$processCode;?>
																									</option>
																									<?	}	?>
																							   </select>
																							</td>
																						</tr>
																						<tr> 
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																	<td valign="top">&nbsp;</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>											  
											</td>
										</tr>
										<tr>
											<td>
												<table width="200" border="0">
													<tr>
														<td class="fieldname">Payment:</td>
														<td>
															<table width="100" border="0">
																<tr>
																	<td><input name="paymentMode" id="paymentMode1" type="radio" value="DWW" class="chkBox" <?=$declaredWeighNo?>></td>
																	<td class="listing-item" nowrap>Decl Wt in W.Challan No </td>
																</tr>
															</table>
														</td>
														<td>
															<table width="100" border="0">
																<tr>
																	<td>
																		<input name="paymentMode" id="paymentMode2" type="radio" value="DWS"  class="chkBox" <?=$declaredSuppNo?>>
																	</td>
																	<td class="listing-item" nowrap>Decl Wt in Supplier.Challan No </td>
																</tr>
															</table>
														</td>
														<td>
															<table width="100" border="0">
																<tr>
																	<td><input name="paymentMode" id="paymentMode3" type="radio" value="EWW" class="chkBox" <?=$effectiveWeighNo?>></td>
																	<td class="listing-item" nowrap>Effective Wt in W.Challan No </td>
																</tr>
															</table>
														</td>
														<td>&nbsp;</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td>
												<table width="200" border="0">
													<tr>
														<td class="fieldname" nowrap>View Type:</td>
														<td>
															<table width="100" border="0">
																<tr>
																	<td><input name="viewType" type="radio" value="SU" class="chkBox" <?=$summaryView?>></td>
																	<td class="listing-item" nowrap>Summary </td>
																</tr>
															</table>
														</td>
														<td>
															<table width="100" border="0">
																<tr>
																	<td><input name="viewType" type="radio" value="DT" class="chkBox" <?=$detailedView?>></td>
																	<td class="listing-item" nowrap>Detailed </td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td align="center">
												<input name="cmdSearch" type="submit" id="cmdSearch" value=" Search" class="button" onclick="return validateSupplierAccountSearch(document.frmSupplierAccount);">
											</td>
										</tr>
									</table>
								</fieldset>											
							</td>
						</tr>
					</table>											
				</td>  
			 <!--Hererere --> 
			</tr>
			<tr>
				<td colspan="3" nowrap class="fieldName" > </td>
			</tr>
			<tr>
				<td class="fieldName" nowrap > </td>
				<td></td>
				<td class="fieldName"></td>
			</tr>
			<tr>
				<td class="fieldName" nowrap ></td>
				<td></td>
				<td class="fieldName"></td>
			</tr>
			<? if($paymentMode!='DWS'){?>
			<tr>
				<td colspan="3"  height="10" class="listing-item">
					<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999">
					<? if( sizeof($catchEntryRecords)){ ?>
						<tr>
							<td colspan="24" align="center" bgcolor="#FFFFFF" class="listing-item">
								<table width="100%">
									<tr>
										<td align="right">
											<table>
												<tr>
													<td align="center" bgcolor="#FFFFFF" class="listing-item">
													<img src="images/x.gif" width="20" height="20">  <span style="vertical-align:top">- The entire transaction Rates are not defined.</span>
													</td>
												</tr>
											</table>
										</td>
										<td align="right">
											<table>
												<tr>
													<td><a href="SettlementSummary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>" class="link1">View Settlement Summary</a></td></tr></table>											   </td>
											   </tr>
											   <? if($maxpage>1){?>
											   <tr>
												<td colspan="2" align="right" style="padding-right:10px;"><div align="right">
												<?php 				 			  
												$nav  = '';
												for($page=1; $page<=$maxpage; $page++)
												{
													if ($page==$pageNo)
													{
														$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													}
													else
													{
														$nav.= " <a href=\"SupplierAccount.php?$pagingSelection&pageNo=$page\" class=\"link1\">$page</a> ";	
													}
												}
												if ($pageNo > 1)
												{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"SupplierAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\"><<</a> ";
												}
												else
												{
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage)
												{
													$page = $pageNo + 1;
													$next = " <a href=\"SupplierAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\">>></a> ";
												}
												else
												{
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
									</table>
								</td>
							</tr>
							<? }?>
							<? if($err!="" ){ ?>
							<tr>
								<td colspan="22" align="center" class="err1" ><?=$err;?></td>
							</tr>
							<? }?>
							<?
								if( sizeof($catchEntryRecords)){
								$i	=	0;
							?>
							<tr bgcolor="#f2f2f2" align="center">
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">No</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Date</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Wt Challan No </td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Supplier</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Fish</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Process</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Grade</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Count</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Decl.<br />Count</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Net Wt </td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Adj</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Local</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Waste</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Soft</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Eff.Wt</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Decl.Wt</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Market<br />Rate </td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Decl.<br />Rate</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Last Paid<br />Rate</td>	
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Final Wt</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Final Rate</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Total</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Setld</td>
								<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Setl Date</td>
							</tr>
							<?
							$grandTotalRate	=	"";
							$settledAmount  = 	"";
							$duesAmount		=	"";
							foreach ($catchEntryRecords as $cer) {
							$i++;		
							$entryDate		= 	$cer[3];
							$enteredDate		= dateFormat($cer[3]);
							$weighmentNo		=	$cer[6];		
							$displayChallanNum	= 	$cer[48];
							$supplier		=	$cer[8];
							$supplierRec		=	$supplierMasterObj->find($supplier);
							$supplierName		=	$supplierRec[2];		
							$fishId			=	$cer[11];		
							$fishRec		=	$fishmasterObj->find($fishId);
							$fishName		=	$fishRec[1];		
							$local			=	$cer[16];
							$wastage		=	$cer[17];	
							$soft			=	$cer[18];		
							$count			=	$cer[13];		
							$gradeCountAdj  	= 	$cer[44];
							$adjustWt		=	$cer[20] + $gradeCountAdj;		
							$declCount		=	$cer[30];		
							$processCodeId		=	$cer[12];
							$processCodeRec		=	$processcodeObj->find($processCodeId);
							$processCode		=	$processCodeRec[2];		
							$netWt			=	$cer[26];		
							$declWt			=	$cer[29];

							$selLandingCenterId	= 	$cer[7];	
							$countAverage		=	$cer[14];		
							$gradeId = $cer[37];
							$gradeCode	=	"";
							if ($count=="") {
								$gradeRec		=	$grademasterObj->find($cer[37]);
								$gradeCode		=	stripSlash($gradeRec[1]);
							}

							# Find the Last Paid Rate
							$lastPaidRate = $supplieraccountObj->getLastPaidRate($fromDate, $selLandingCenterId, $supplier, $fishId, $processCodeId, $count, $countAverage, $gradeId, $viewType);

							# Find Daily Rate -------------------------------
							$dailyRateRec	= $supplieraccountObj->findDailyRate($fishId, $processCodeId, $gradeId, $countAverage, $selLandingCenterId, $supplier, $entryDate);
							$higherCount = $dailyRateRec[9];
							$lowerCount  = $dailyRateRec[10];
							$dailyMarketRate = $dailyRateRec[6];
							$dailyDeclaredRate = $dailyRateRec[7];
							$declCountAvg      = $dailyRateRec[8];		
							$actualMarketRate = 0;	
							$actualDeclRate   = 0;
							$balCountAvg 	  = 0;
							if ($higherCount!="" && $lowerCount!="" && $countAverage!="") {
								$balCountAvg = $declCountAvg - $countAverage;
								if ($balCountAvg>0) {
									$actualMarketRate = $dailyMarketRate + ($balCountAvg*$higherCount);
									$actualDeclRate   = $dailyDeclaredRate + ($balCountAvg*$higherCount);
								} else if ($balCountAvg<0) {				
									$actualMarketRate = $dailyMarketRate - ($balCountAvg*$lowerCount);
									$actualDeclRate   = $dailyDeclaredRate- ($balCountAvg*$lowerCount);
								}
							}
							/* --------------------------------------------------*/
							$declRate		=	($actualDeclRate!=0)?$actualDeclRate:$dailyRateRec[7];	
							$paidStatus		=	$cer[35];	
							if ($cer[36]==0) {
								$settelementDate ="";
							} else {
								$settelementDate	=	$cer[36];
							}
						
							$selectWeight		=	$cer[32];
							$selectRate		=	$cer[33];
							$actualRate		=	$cer[34];
							
							if ($selectWeight!="" && $selectWeight!=0.00) {
								$effectiveWt	=	$selectWeight;
							} else {
								$effectiveWt	=	$cer[28];	
							}
						
							$payableWt	=	$cer[28];		
							if ($selectRate!="" && $selectRate!=0 ){
								$marketRate	=	$selectRate;
							} else {
								$marketRate	=	($actualMarketRate!=0)?$actualMarketRate:$dailyRateRec[6];
							}	
							$payableRate	=	($actualMarketRate!=0)?$actualMarketRate:$dailyRateRec[6];		
							#Number of Grouping
							$numRecords			=	$cer[45];
							if ($summaryView) {
								$selMarketRate	=	number_format(($marketRate/$numRecords),2,'.','');
							} else {
								$selMarketRate	= 	$marketRate;
							}			
							$totalRate		=	$effectiveWt * $selMarketRate;
							
							$grandTotalRate		+= $totalRate;			
							if ($paidStatus=='Y') {
								$checked	=	"Checked";
								$settledAmount	= $settledAmount +	$totalRate;
							} else {
								$checked	=	"";
								$duesAmount	= $duesAmount +	$totalRate;
							}
								
							$dailyCatchEntryId	=	$cer[42];
							
							if ($viewType=='SU') {
								$readonly = "readonly";
							} else {
								$readonly = "";
							}
							$disabled = "";
							$edited	  = "";
							if ($paidStatus=='Y' && $isAdmin==false && $reEdit==false) {
								$disabled = "readonly";
								$edited	  = 1;
							}

							# Resettled Settings
							if ($viewType=='DT') $reSeltledDate = $cer[47];
							$rowColor = "";
							if ($viewType=='DT' && $reSeltledDate!="") 
								$rowColor = "#FFFFCC";
							else $rowColor = "#FFFFFF";
							
							$showWtDiff = "";
							if ($effectiveWt!=$payableWt) {		
								$rowColor = "#CCCCFF";
								$showWtDiff = "onMouseover=\"ShowTip('Mismatch in Effective Wt and Final Wt. Please check the weight before done the settlement ');\" onMouseout=\"UnTip();\" ";
							}
							?>
							<tr bgcolor="<?=$rowColor?>" <?=$showWtDiff?>>
								<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="center">
									<input type="hidden" name="catchEntryId_<?=$i;?>" value="<?=$dailyCatchEntryId;?>"><?=(($pageNo-1)*$limit)+$i?>
								</td>
								<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$enteredDate?></td>
								<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$displayChallanNum?></td>
								<td class="listing-item" style="padding-left:3px; padding-right:3px; line-height:normal"><?=$supplierName?></td>
								<td class="listing-item" style="padding-left:3px; padding-right:3px; line-height:normal;"><?=$fishName?><input type="hidden" name="fishId_<?=$i?>" value="<?=$fishId?>"></td>
								<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$processCode?><input type="hidden" name="processCodeId_<?=$i?>" value="<?=$processCodeId?>"></td>
								<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$gradeCode?><input type="hidden" name="gradeId_<?=$i?>" value="<?=$gradeId?>"></td>
								<td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$count?><input type="hidden" name="count_<?=$i?>" value="<?=$count?>"></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$declCount?></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$netWt?></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$adjustWt?></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$local;?></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$wastage?></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$soft?></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$payableWt?><input type="hidden" name="payableWt_<?=$i?>" id="payableWt_<?=$i?>" value="<?=$payableWt?>"></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$declWt?><input type="hidden" name="declWt_<?=$i?>" id="declWt_<?=$i?>" value="<?=$declWt?>"></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><? if($payableRate==""){?> <img src="images/x.png" width="20" height="20"><? } else { echo $payableRate;}?><input type="hidden" name="payableRate_<?=$i?>" id="payableRate_<?=$i?>" value="<?=$payableRate?>"></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$declRate?><input type="hidden" name="declRate_<?=$i?>" id="declRate_<?=$i?>" value="<?=$declRate?>"></td>
								<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px;"><?=$lastPaidRate?></td>	
								<td nowrap align="right" style="padding-left:3px; padding-right:3px;"><input type="text" name="weight_<?=$i;?>" id="weight_<?=$i;?>" value="<?=$effectiveWt?>" size="3" style="text-align:right;" onKeyUp="return actualAmount(document.frmSupplierAccount);" onkeydown="return nextBox(event,'document.frmSupplierAccount','weight_<?=$i+1;?>');" <?=$readonly?> <?=$disabled?>></td>
								<td nowrap align="right" style="padding-left:3px; padding-right:3px;">
								<input type="text" name="rate_<?=$i;?>" id="rate_<?=$i;?>" value="<?=$selMarketRate?>" size="3" style="text-align:right" onKeyUp="return actualAmount(document.frmSupplierAccount);" onkeydown="return nextBox(event,'document.frmSupplierAccount','rate_<?=$i+1;?>');" <?=$disabled?> autocomplete="off">
								<input type="hidden" name="hidRate_<?=$i;?>" id="hidRate_<?=$i;?>" value="<?=$selMarketRate?>" size="3" readonly="true">	
								
								</td>
								<td nowrap align="right" style="padding-left:3px; padding-right:3px;">
								<input type="text" name="totalRate_<?=$i;?>" id="totalRate_<?=$i;?>" value="<?=$totalRate?>" size="5" style="text-align:right; border:none;" readonly>
								</td>
								<td nowrap align="center">
								<input name="paid_<?=$i;?>" type="checkbox" id="paid_<?=$i;?>" value="Y"  class="chkBox" <?=$checked?> <?=$disabled?>>
								<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>">
								</td>
								<td align="center" class="listing-item">
									<?$settelementDate = ($settelementDate!="")?dateFormat($settelementDate):date("d/m/Y")?>		
									<input type="text" id="suppSetldDate_<?=$i;?>" name="suppSetldDate_<?=$i;?>" size="9" value="<?=$settelementDate?>" autocomplete="off" />

									<? if ($reSeltledDate!="") {?>
									<br>
									<span class="listing-item" style="line-height:normal;font-weight:8px;color:maroon">Resetld On:<?=dateFormat($reSeltledDate);?></span>
									<? }?>
								</td>
							</tr>
							<? }?>
  
							<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
						  <tr bgcolor="#FFFFFF">
							  <td colspan="21" align="right" class="listing-head">Total:&nbsp;</td>
							  <td align="right" style="padding-left:3px; padding-right:3px;"><input name="grandTotalRate" type="text" id="grandTotalRate" size="7" readonly  style="text-align:right; border:none; padding-right:7px;" value="<? echo number_format($grandTotalRate,2);?>"></td>
							  <td>&nbsp;</td>
							  <td>&nbsp;</td>
						  </tr>
						  <tr bgcolor="#FFFFFF">
							<td colspan="8" style="border-right:none;padding-left:5px;">
								<table cellpadding="0" cellspacing="0">
									<TR><TD>
									<fieldset>
									<legend class="listing-item">Total value as per </legend>
										<table cellpadding="0" cellspacing="0">
											<TR>
												<TD class="fieldName" style="line-height:normal" nowrap="true">Market Rate:</TD>
												<TD class="listing-item"><b><?=number_format($totalMarketValue,2,'.',',');?></b></TD>
												<td width="10"></td>
												<TD class="fieldName" style="line-height:normal" nowrap="true">Decl Rate:</TD>
												<TD class="listing-item"><b><?=number_format($totalDeclaredValue,2,'.',',');?></b></TD>
												<td width="10"></td>
												<TD class="fieldName" style="line-height:normal" nowrap="true">Last Paid Rate:</TD>
												<TD class="listing-item"><b><?=number_format($totalLastPaidValue,2,'.',',');?></b></TD>
											</TR>
										</table>
									</fieldset>
									</TD></TR>
								</table>
							</td>			
							<td colspan="13" align="right" class="listing-head" valign="top">Grand Total: </td>
							<td align="right" style="padding-left:3px; padding-right:3px;" class="listing-item" valign="top"><b><? echo number_format($grandTotalCatchEntryRate,2);?></b></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<? if($maxpage>1){?>
						<tr bgcolor="#FFFFFF">
							<td colspan="24" style="padding-right:10px;">
								<div align="right">
								<?php 				 			  
								$nav  = '';
								for($page=1; $page<=$maxpage; $page++)
								{
									if ($page==$pageNo)
									{
										$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
									}
									else
									{
										$nav.= " <a href=\"SupplierAccount.php?$pagingSelection&pageNo=$page\" class=\"link1\">$page</a> ";
									//echo $nav;
									}
								}
								if ($pageNo > 1)
								{
									$page  = $pageNo - 1;
									$prev  = " <a href=\"SupplierAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\"><<</a> ";
								}
								else
								{
									$prev  = '&nbsp;'; // we're on page one, don't print previous link
									$first = '&nbsp;'; // nor the first page link
								}

								if ($pageNo < $maxpage)
								{
									$page = $pageNo + 1;
									$next = " <a href=\"SupplierAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\">>></a> ";
								}
								else
								{
									$next = '&nbsp;'; // we're on the last page, don't print next link
									$last = '&nbsp;'; // nor the last page link
								}
							// print the navigation link
								$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
								echo $first . $prev . $nav . $next . $last . $summary; 
						  ?>
						</div><input type="hidden" name="pageNo" value="<?=$pageNo?>">
					</td>
				</tr>
			  <? }?>
			  <? } else {?>
			<tr bgcolor="white"> 
				<td colspan="24"  class="err1" height="5" align="center"><?=$msgNoSettlementRecords;?></td>
			</tr>
			<? } 
			if( sizeof($catchEntryRecords)){
			?>
			<tr bgcolor="#FFFFFF"><td colspan="24" height=1>
				<table>
					<tr>
						<td class="fieldName">Total Value:</td>
						<td class="fieldName"><input name="totalValue" type="text" id="totalValue" size="7" readonly  style="text-align:right; padding-right:7px;" value="<? echo number_format($grandTotalCatchEntryRate,2,'.','');?>"></td>
						<td class="fieldName"> Settled:</td>
						<td><input name="settledAmount" type="text" id="settledAmount" size="7" readonly value="<? echo number_format($grandTotalSettledAmount,2,'.','');?>" style="text-align:right;"></td>
						<td class="fieldName">Pending: </td>
						<td><input name="duesAmount" type="text" id="duesAmount" size="7" readonly value="<? echo number_format($grandTotalDuesAmount,2,'.','');?>" style="text-align:right"></td>
						<td class="fieldName">Paid:</td>
						<td class="fieldName"><input name="paid" type="text" id="paid" size="7" readonly  style="text-align:right; padding-right:7px;" value="<? echo number_format($grandTotalSettledAmount,2,'.','');?>"></td>
						<td class="fieldName">Due:</td>
						<td class="fieldName">&nbsp;<input name="duesAmount" type="text" id="duesAmount" size="7" readonly value="<? echo number_format($grandTotalDuesAmount,2,'.','');?>" style="text-align:right"></td>
						<td class="fieldName"> Payable</td>
						<td class="fieldName"><input name="netPayable" type="text" id="netPayable" size="7" readonly  style="text-align:right" value="<? echo number_format($grandTotalDuesAmount,2,'.','');?>"></td>
					</tr>
			  </table>
			</td>
		</tr>
		<? }?>
	</table>
</td>
</tr>
  <? } 
  if($paymentMode=='DWS') {
  ?>
										    <tr>
										      <td colspan="2" align="center">&nbsp;</td>
										      <td align="center" colspan="2">&nbsp;</td>
									      </tr>
									      <tr>
											  <td colspan="4" align="center">
											  <table>
											  <? if(sizeof($declaredWtRecords)>0){?>
											  <tr>
											  <td>
											  <table width="55%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
							  	

                                  <tr bgcolor="#f2f2f2" align="center">
								  <? if($viewType=='DT'){?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td> 
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Su. Challan No </td>
									<? }?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.Qty</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Setld</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Setld Date </td>
                                  </tr>
                                  <?php
				  	$j=0;
					$gradeCode="";
					$totalWt	=	"";
					$prevFishId = 0;
					$prevProcessCodeId = 0;
					$totalAmount = 0;
					$selectRate = "";
					$declWt = "";
					foreach($declaredWtRecords as $sdr) {
						$j++;
						$catchEntryId	=	$sdr[0];
								
						$sChallanDate = $sdr[8];
						$array	=	explode("-",$sChallanDate);
						$supplierChallanDate = $array[2]."/".$array[1]."/".$array[0];	
						$supplierChallanNo = $sdr[7];
			
						$sFishId			=	$sdr[1];
						$fishName = "";
						if ($prevFishId!=$sFishId) {
							$fishName		=	$sdr[11];
						}
								
						$processCodeId	=	$sdr[2];	
						$processCode	= "";
						if ($prevProcessCodeId!=$processCodeId) {
							$processCode	=	$sdr[12];
						}
								
						$declCount		=	$sdr[10];
						# Find the Count Average	
						$countAverage = $supplieraccountObj->calcCountAverage($declCount);
								
						$dailyRateRec	=	$supplieraccountObj->findDailyRate($sFishId, $processCodeId, $gradeId, $countAverage, $selLandingCenterId, $supplier, $sChallanDate);	
						$higherCount = $dailyRateRec[9];
						$lowerCount  = $dailyRateRec[10];
						$dailyMarketRate = $dailyRateRec[6];
						$dailyDeclaredRate = $dailyRateRec[7];
						$declCountAvg      = $dailyRateRec[8];		
						$actualMarketRate = 0;	
						$actualDeclRate   = 0;
						$balCountAvg 	  = 0;
						if ($higherCount!="" && $lowerCount!="" && $countAverage!="") {
							$balCountAvg = $declCountAvg - $countAverage;
							if ($balCountAvg>0) {
								$actualMarketRate = $dailyMarketRate + ($balCountAvg*$higherCount);
								$actualDeclRate   = $dailyDeclaredRate + ($balCountAvg*$higherCount);
							} else if ($balCountAvg<0) {				
								$actualMarketRate = $dailyMarketRate - ($balCountAvg*$lowerCount);
								$actualDeclRate   = $dailyDeclaredRate- ($balCountAvg*$lowerCount);
							}
						}
								
						$selectRate		=	$sdr[15];
						$declRate = "";
						if ($selectRate!="") {
							$declRate	=	$sdr[15];
						} else {							
							$declRate = ($actualDeclRate!=0)?$actualDeclRate:$dailyRateRec[7];
						}				
						$declWt	=	$sdr[13];
						$totalWt	+=	$declWt;
						$amount  =   $declWt * $declRate;
						$totalAmount +=$amount;	
								
						$declaredEntryId = 	$sdr[14];
								
						$isSettled	=	$sdr[16];
								
						$amountSettled = "";
						if ($isSettled=='Y') {
							$amountSettled = "Checked";
						}
								
						$settledDate = $sdr[17];
						$supplierSettledDate = "";
						if ($settledDate!=0){
							$array			=	explode("-",$settledDate);
							$supplierSettledDate	=	$array[2]."/".$array[1]."/".$array[0];
						}
								
						$disabled = "";
						$edited	  = "";
						if ($isSettled=='Y' && $isAdmin==false && $reEdit==false) {
							$disabled = "readonly";
							$edited	  = 1;
						}
						# Resettled Settings
						$reSeltledDate = "";
						if ($viewType=='DT') $reSeltledDate = $sdr[18];
						$rowColor = "";
						if ($viewType=='DT' && $reSeltledDate!="") 
							$rowColor = "#FFFFCC";
						else $rowColor = "#FFFFFF";
				?>
                               <tr bgcolor="<?=$rowColor?>">
				   <? if($viewType=='DT'){?>
                                 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" height="25"><?=$supplierChallanDate?></td> 
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$supplierChallanNo?></td>
				  <? }?>
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" height="25"><?=$fishName?>
					<input type="hidden" name="sFishId_<?=$j?>" value="<?=$sFishId?>">
					<input type="hidden" name="challanEntryId_<?=$j?>" value="<?=$catchEntryId?>">
					</td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?>
					<input type="hidden" name="processCodeId_<?=$j?>" value="<?=$processCodeId?>" />
				   </td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$declCount?>
					<input type="hidden" name="declCount_<?=$j?>" value="<?=$declCount?>">
				   </td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$declWt?>
						<input type="hidden" name="declWt_<?=$j?>" id="declWt_<?=$j?>" value="<?=$declWt?>">
						<input type="hidden" name="declaredEntryId_<?=$j?>" value="<?=$declaredEntryId?>">
					</td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
						<input name="rate_<?=$j?>" type="text" id="rate_<?=$j?>" size="5" onKeyUp="return calcAmount(document.frmSupplierAccount);" value="<?=$declRate?>" style="text-align:right" <?=$disabled?> onkeydown="return nextDeclWtBox(event,'document.frmSupplierAccount','rate_<?=$j+1;?>');" autocomplete="off">
						<input name="hidRate_<?=$j?>" type="hidden" id="hidRate_<?=$j?>" size="5" value="<?=$declRate?>" readonly="true" style="text-align:right">
				    </td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
					<input name="amount_<?=$j?>" type="text" id="amount_<?=$j?>" size="8" value="<?=$amount?>" style="text-align:right; border:none" readonly>
				   </td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><input name="settled_<?=$j?>" type="checkbox" id="settled_<?=$j?>" value="Y" class="chkBox" <?=$amountSettled?> <?=$disabled?>><input type="hidden" name="reEdit_<?=$j;?>" value="<?=$edited?>"></td>
                                    <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
					<?php
						if ($supplierSettledDate==0) $supplierSettledDate = date("d/m/Y");
					?>
						<input type="text" id="suppSetldDate_<?=$j;?>" name="suppSetldDate_<?=$j;?>" size="9" value="<?=$supplierSettledDate?>" autocomplete="off" />
					<? if ($reSeltledDate!=0) {?>
					<br>
					<span class="listing-item" style="line-height:normal;font-weight:8px;color:maroon">Resetld On:<?=dateFormat($reSeltledDate);?></span>
					<? }?>
				   </td>
                               </tr>
                                  <? 
								  $prevFishId = $sFishId;
								  $prevProcessCodeId = $processCodeId;
								  } 
								  ?>
								   <tr bgcolor="#FFFFFF"><input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$j?>" >
								   <? if($viewType=='DT'){
								   			$colSpan = 5;
										} else {
											$colSpan = 3;
										}
								   ?>
								   
                                    <td colspan="<?=$colSpan?>" nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:10px;">
						<strong><? echo number_format($totalWt,2);?></strong>
				    </td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
                                    <td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><input name="totalAmount" type="text" id="totalAmount" size="8" style="text-align:right; border:none" readonly value="<?=$totalAmount?>"></td>
                                    <td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
								    <td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
								   </tr>								  
                                </table></td>
								</tr>
								<? } else { ?>
								<tr>
								<td align="center" class="err1"><?=$msgNoSettlementRecords;?></td>
								</tr>
								<? }?>
								</table>
								</td>
								 </tr>
								 <? }?>
											<tr>
											  <td colspan="2" align="center">&nbsp;</td>
											  <td align="center" colspan="2">&nbsp;</td>
										  </tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">&nbsp;&nbsp;</td>
											  <? } else{ ?>
	<td align="center" colspan="2">&nbsp;&nbsp;
		<? if($edit==true){?><input type="submit" name="cmdAddSupplierAccount" class="button" value=" Save " onClick="return validateSupplierAccount(document.frmSupplierAccount);"><? }?>
		&nbsp;&nbsp;
		 <? if($print==true){?><input type="button" name="Submit" value="Print" class="button" onClick="return printWindow('PrintSupplierAccount.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&landingCenter=<?=$landingCenterId?>&supplier=<?=$selectSupplier?>&selChallan=<?=$selChallanNo?>&fish=<?=$selFishId?>&processCode=<?=$processId?>&selSettlement=<?=$settlementDate?>&paymentMode=<?=$paymentMode?>&viewType=<?=$viewType?>&selOption=<?=$paidStatus?>&rmConfirmed=<?=$rmConfirmed?>&dateSelectFrom=<?=$dateSelectFrom?>&offset=<?=$offset?>&limit=<?=$limit?>&pageNo=<?=$pageNo?>&billingCompany=<?=$billingCompany?>',700,600);"><? }?>												</td>
<input type="hidden" name="cmdAddNew" value="1">
												<? }?>
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
		<tr>
			<td height="10" ></td>
		</tr>
		<?php
			}
		?>
	<input type="hidden" name="recExist" id="recExist" value="<?=(sizeof($catchEntryRecords)>0 || sizeof($declaredWtRecords)>0)?1:0?>">
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
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
			inputField  : "supplyTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>	
	<?php
	if (sizeof($catchEntryRecords)>0 || sizeof($declaredWtRecords)>0) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	var rowCount = 	document.getElementById("hidRowCount").value;
	for (i=1;i<=rowCount;i++) {
		Calendar.setup 
		(	
			{
				inputField  : "suppSetldDate_"+i,         // ID of the input field
				eventName	  : "click",	    // name of event
				button : "suppSetldDate_"+i, 
				ifFormat    : "%d/%m/%Y",    // the date format
				singleClick : true,
				step : 1
			}
		);
	}
	//-->	
	</SCRIPT>
	<?php
		}
	?>
		
</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<?php
/*
	$out1 = ob_get_contents(); 
	ob_end_clean();
*/
?>
<?php
/*
# Include Template [topLeftNav.php]
require("template/topLeftNav.php");
	echo $out1;
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
*/
?>
