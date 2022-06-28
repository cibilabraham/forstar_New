<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$transportationChecked	=	"";	
	$iceBlockChecked	=	"";

	$dateSelection = "?supplyFrom=".$p["supplyFrom"]."&supplyTill=".$p["supplyTill"]."&pageNo=".$p["pageNo"];

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
	
	# Add Category Start 
	if ($p["cmdAddNew"]!="" && $p["cmdAddCancel"]=="") {
		$addMode	=	true;
	}
	
	#Select a Date
	$selDate	=	$p["selDate"];
 	if ($selDate)  {
		$sDate		=	explode("/",$selDate);
		$selectDate	=	$sDate[2]."-".$sDate[1]."-".$sDate[0];
  	}
	#Ice block	
	$chkIceBlock		= $p["chkIceBlock"];
	if ($chkIceBlock) 	$iceBlockChecked  = "Checked";
	#Transportation
	$chkTransportation 	= $p["chkTransportation"];
	if ($chkTransportation)  $transportationChecked = "Checked";
	#Commission
	$chkCommission		=	$p["chkCommission"];
	if ($chkCommission)  	$commissionChecked = "Checked";
	#Handling Cost
	$chkHandling		=	$p["chkHandling"];
	if ($chkHandling)  	$handlingChecked = "Checked";
	
	$selOption 	= $p["selOption"];	
	if ($selOption=='I') $selIndividual = "Checked";
	if ($selOption=='G') $selGroup = "Checked";

	$selCommission 	= $p["selCommission"];	
	if ($selCommission=='D') $selCommiIndividual = "Checked";
	if ($selCommission=='S') $selCommiGroup = "Checked";

	$selHandling 	= $p["selHandling"];	
	if ($selHandling=='D') $selHandlingIndividual = "Checked";
	if ($selHandling=='S') $selHandlingGroup = "Checked";

	$icePaid	= $p["icePaid"];
	if ($icePaid=='Y') $icePaidChk = "Checked";

	$tptPaid	= $p["tptPaid"];	
	if ($tptPaid=='Y') $tptPaidChk = "Checked";
	
	$commiPaid	= $p["commiPaid"];
	if ($commiPaid=='Y') $commiPaidChk = "Checked";

	$hadlngPaid 	= $p["hadlngPaid"];
	if ($hadlngPaid=='Y') $hadlngPaidChk = "Checked";

	$hasCommi	= $p["hasCommi"];
	if ($hasCommi=='Y') $hasCommiChk = "Checked";

	$hasHandling	= $p["hasHandling"];
	if ($hasHandling=='Y') $hasHandlingChk = "Checked";

	// Ice	
	if ($p["numIceBlocks"]!="") $numIceBlocks = $p["numIceBlocks"];
	if ($p["costPerBlock"]!="") $costPerBlock = $p["costPerBlock"];
	if ($p["totalIceCost"]!="") $totalIceCost = $p["totalIceCost"];
	if ($p["fixedIceCost"]!="") $fixedIceCost = $p["fixedIceCost"];

	// Transportation
	if ($p["km"]!="") $landingCenterDistance = $p["km"];
	if ($p["costPerKm"]!="") $costPerKm = $p["costPerKm"];
	if ($p["totalTransAmt"]!="") $totalTransAmt = $p["totalTransAmt"];
	if ($p["fixedTransCost"]!="") $fixedTransCost = $p["fixedTransCost"];

	// Commission
	if ($p["totalQuanty"]!="") $totalQuanty = $p["totalQuanty"];
	if ($p["commissionPerKg"]!="") $commissionPerKg = $p["commissionPerKg"];
	if ($p["totalCommiRate"]!="") $totalCommiRate = $p["totalCommiRate"];
	if ($p["fixedCommiRate"]!="") $fixedCommiRate = $p["fixedCommiRate"];

	// Handling Cost
	if ($p["totalRMQuanty"]!="") $totalRMQuanty = $p["totalRMQuanty"];
	if ($p["handlingRatePerKg"]!="") $handlingRatePerKg = $p["handlingRatePerKg"];
	if ($p["totalHandlingAmt"]!="") $totalHandlingAmt = $p["totalHandlingAmt"];
	if ($p["fixedHandlingAmt"]!="") $fixedHandlingAmt = $p["fixedHandlingAmt"];
	

	#Insert a Record
	if ($p["cmdAdd"]!="") {

		$selOption = $p["selOption"];

		if ($selOption=='I') {
			$selWtChallanId 		=	$p["selWtChallan"];
	
			// Ice	
			$numIceBlocks			=	$p["numIceBlocks"];
			$costPerBlock			=	$p["costPerBlock"];
			$totalIceCost			=	$p["totalIceCost"];

			$chkFixedIceBlock		= $p["chkIceBlock"];
			if ($chkFixedIceBlock) {
				$fixedIceCost		= $p["fixedIceCost"];
				$numIceBlocks		= 0;
				$costPerBlock		= 0;
				$totalIceCost		= 0;			
			} else {
				$fixedIceCost		= 0;	
			}

			// Transportation
			$km				= $p["km"];
			$costPerKm			= $p["costPerKm"];
			$totalTransAmt			= $p["totalTransAmt"];
			
			$chkFixedTransportation 	=  $p["chkTransportation"];
			if ($chkFixedTransportation) {
				$fixedTransCost		= $p["fixedTransCost"];
				$km			= 0;
				$costPerKm		= 0;
				$totalTransAmt		= 0;
			} else {	
				$fixedTransCost		= 0;
			}

			$hasCommi	= $p["hasCommi"];
			$hasHandling	= $p["hasHandling"];

			# Commission 
			$selCommission 	= $p["selCommission"];

		if ($hasCommi!="") {
			if ($selCommission=='D') {
			
				$grandTotalCommiAmt = $p["grandTotalCommiAmt"];
				$hidIvidualCommiRowCount = $p["hidIvidualCommiRowCount"]; //Comision detailed Rec Row Count

				for ($k=1;$k<=$hidIvidualCommiRowCount;$k++) {
					$dailyCatchEntryId = $p["dcEntryId_".$k];
					$processCodeId = $p["processCodeId_".$k];		
					$rate 	  = $p["rate_".$k];
					$totalAmt = $p["totalAmt_".$k];	
					if ($dailyCatchEntryId && $totalAmt!=0) {
						$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryCommissionRec($dailyCatchEntryId, $rate, $totalAmt);
					}
				}
				/*
				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {

					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$recProcessCodeId = $dcr[3];	
						$effectiveWt	  = $dcr[4];

						for ($k=1;$k<=$hidIvidualCommiRowCount;$k++) {
							$processCodeId = $p["processCodeId_".$k];		
							$rate 	  = $p["rate_".$k];
							$calcTotalAmt = $effectiveWt * $rate;
							$totalAmt = number_format($calcTotalAmt,2,'.','');
							//$ttotalAmt = $p["totalAmt_".$k];	
							if ($recProcessCodeId==$processCodeId)	{		
								$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryCommissionRec($dailyCatchEntryId, $rate, $totalAmt);
							}
						}
					}
				}*/		

			} else if ($selCommission=='S') {  // Commission Summary
				// Commission
				$totalQuanty			=	$p["totalQuanty"];
				$commissionPerKg		=	$p["commissionPerKg"];
				$totalCommiRate			=	$p["totalCommiRate"];		

				$chkFixedCommission		=	$p["chkCommission"];
				if ($chkFixedCommission) {
					$fixedCommiRate		=	$p["fixedCommiRate"];
					$totalQuanty		=	0;
					$commissionPerKg	=	0;
					$totalCommiRate		=	0;	
				} else {
					$fixedCommiRate		=	0;
				}
			}
		}

		# Handling 
		$selHandling 	= $p["selHandling"];	
		if ($hasHandling!="") {
			if ($selHandling=='D') {
				$grandTotalHadlngAmt = $p["grandTotalHadlngAmt"];
				$hidDetailedHadlgRowCount = $p["hidDetailedHadlgRowCount"]; //Hadling detiled Rec Row Count
				
				for ($k=1;$k<=$hidDetailedHadlgRowCount;$k++) {
					$hDailyCatchEntryId = $p["hDcEntryId_".$k];
					$hProcessCodeId = $p["hProcessCodeId_".$k];
					$hRate 	  = $p["hRate_".$k];
					$hTotalAmt = $p["hTotalAmt_".$k];
					if ($hDailyCatchEntryId && $hTotalAmt!=0) {
						$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryHandlingRec($hDailyCatchEntryId, $hRate, $hTotalAmt);
					}
				}
				/*
				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$recProcessCodeId = $dcr[3];	
						$effectiveWt	  = $dcr[4];

						for ($k=1;$k<=$hidDetailedHadlgRowCount;$k++) {
							$hProcessCodeId = $p["hProcessCodeId_".$k];
							$hRate 	  = $p["hRate_".$k];				
							$calcTotalAmt = $effectiveWt * $hRate;
							$totalAmt = number_format($calcTotalAmt,2,'.','');

							if ($recProcessCodeId==$hProcessCodeId)	{		
								$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryHandlingRec($dailyCatchEntryId, $hRate, $totalAmt);
							}
						}
					}
				}
				*/				

			} else if ($selHandling=='S') { //Handling Summary

				// Handling Cost
				$totalRMQuanty			=	$p["totalRMQuanty"];
				$handlingRatePerKg		=	$p["handlingRatePerKg"];
				$totalHandlingAmt		=	$p["totalHandlingAmt"];		

				$chkFixedHandling		=	$p["chkHandling"];
				if ($chkFixedHandling) {
					$fixedHandlingAmt	=	$p["fixedHandlingAmt"];
					$totalRMQuanty		=	0;
					$handlingRatePerKg	=	0;
					$totalHandlingAmt	=	0;	
				} else {
					$fixedHandlingAmt	=	0;
				}
			}
		}		
	
			// To Be Paid Or Not
			$icePaid	= ($p["icePaid"]=="")?N:$p["icePaid"];
			$tptPaid	= ($p["tptPaid"]=="")?N:$p["tptPaid"];	
			$commiPaid	= ($p["commiPaid"]=="")?N:$p["commiPaid"];
			$hadlngPaid 	= ($p["hadlngPaid"]=="")?N:$p["hadlngPaid"];

		} else if ($selOption=='G') { // Individual Option End
				
			$selFromDate = $p["dateFrom"];
			$selTillDate = $p["dateTill"];
			$selectSupplier = $p["supplier"];

			$fromDate	= mysqlDateFormat($selFromDate);
			$tillDate	= mysqlDateFormat($selTillDate);

			$hidIvidualCommiRowCount = $p["hidIvidualCommiRowCount"]; //Comision Rec Row Count	
			$hidDetailedHadlgRowCount = $p["hidDetailedHadlgRowCount"]; //Hadling Rec Row Count

			//$grandTotalCommiAmt = $p["grandTotalCommiAmt"];
			//$grandTotalHadlngAmt = $p["grandTotalHadlngAmt"];

			#Get all Recs for the selected criteria
			$getDailyCatchEntryRecs = $rmsupplycostObj->getDailyCatchEntryRecs($fromDate, $tillDate, $selectSupplier);

			if (sizeof($getDailyCatchEntryRecs)>0) {
				$grandTotalCommiAmt = 0;

				$sumCommiArray=array();
				$sumHandleArray = array();
				$l = 0;
				$recArray = array();
				foreach ($getDailyCatchEntryRecs as $gdr) {
					$l++;
					$catchEntryMainId  = $gdr[0];
					$recArray[$catchEntryMainId] = $catchEntryMainId;
					$dailyCatchEntryId = $gdr[1];
					$recProcessCodeId = $gdr[3];	
					$effectiveWt	  = $gdr[4];	

					#Commssion Section
					for ($k=1;$k<=$hidIvidualCommiRowCount;$k++) {
						$processCodeId = $p["processCodeId_".$k];		
						$rate 	  = $p["rate_".$k];
						$calcTotalAmt = $effectiveWt * $rate;
						$totalAmt = number_format($calcTotalAmt,2,'.','');
		
						if ($recProcessCodeId==$processCodeId)	{	
							$sumCommiArray[$catchEntryMainId] +=$totalAmt;	
							//echo "c=$catchEntryMainId=$recProcessCodeId=$processCodeId=$totalAmt<br>";
							$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryCommissionRec($dailyCatchEntryId, $rate, $totalAmt);
						}								
					}							

					# Handling  Section
					for ($k=1;$k<=$hidDetailedHadlgRowCount;$k++) {
						$hProcessCodeId = $p["hProcessCodeId_".$k];
						$hRate 	  = $p["hRate_".$k];				
						$calcTotalAmt = $effectiveWt * $hRate;
						$totalAmt = number_format($calcTotalAmt,2,'.','');

						if ($recProcessCodeId==$hProcessCodeId)	{	
							// Find the Sum of each challan
							$sumHandleArray[$catchEntryMainId] += $totalAmt; 	
							$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryHandlingRec($dailyCatchEntryId, $hRate, $totalAmt);
						}
					}			
				}				
				
				/*echo "<pre>";
				print_r($sumCommiArray);
				print_r($sumHandleArray);
				print_r($recArray);
				echo "</pre>";*/
			}				
		}
		

		if ($selOption) {	
			
			if  ($selOption=='I' && $selWtChallanId!="") {
				$supplyCostRecIns = $rmsupplycostObj->addSupplyCost($selWtChallanId, $numIceBlocks, $costPerBlock, $totalIceCost, $fixedIceCost, $km, $costPerKm, $totalTransAmt, $fixedTransCost, $totalQuanty, $commissionPerKg, $totalCommiRate, $fixedCommiRate, $totalRMQuanty, $handlingRatePerKg, $totalHandlingAmt, $fixedHandlingAmt, $selOption, $selCommission, $grandTotalCommiAmt, $selHandling, $grandTotalHadlngAmt, $icePaid, $tptPaid, $commiPaid,  $hadlngPaid);		
			} else if ($selOption=='G') {
			 if (sizeof($recArray)>0) {
				foreach ($recArray as $id) {  // Here $id as Challan Id
					$selectedOption = "I";
					$selWtChallanId = $id;
					$selCommission = "D";
					$grandTotalCommiAmt = $sumCommiArray[$id];
					$selHandling = "D";
					$grandTotalHadlngAmt = $sumHandleArray[$id];
					//echo "$id=".$id=$sumCommiArray[$id]."-".$sumHandleArray[$id]."<br>";
					$supplyCostRecIns = $rmsupplycostObj->addGroupedSupplyCost($selWtChallanId, $selectedOption, $selCommission, $grandTotalCommiAmt, $selHandling, $grandTotalHadlngAmt);
				}	
			  }	
			}
				
			if ($supplyCostRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddSupplyCost);
				$sessObj->createSession("nextPage",$url_afterAddSupplyCost.$dateSelection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddSupplyCost;
			}
			$supplyCostRecIns	=	false;			
		}			
	}
	

	# Edit 
	if ($p["editId"]!="") {

		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$supplyCostRec		=	$rmsupplycostObj->find($editId);
		$editSupplyCostId	=	$supplyCostRec[0];
		
		$challanDate		=	$supplyCostRec[14];
		$eDate			=	explode("-",$challanDate);
		$selDate		=	$eDate[2]."/".$eDate[1]."/".$eDate[0];

		$selWtChallan		=	$supplyCostRec[1];
		
		$numIceBlocks		=	$supplyCostRec[2];
		$costPerBlock		=	$supplyCostRec[3];
		$totalIceCost		=	$supplyCostRec[4];
		$fixedIceCost		=	$supplyCostRec[5];

		
		$landingCenterDistance	=	$supplyCostRec[6];
		$costPerKm		=	$supplyCostRec[7];
		$totalTransAmt		=	$supplyCostRec[8];
		$fixedTransCost		=	$supplyCostRec[9];

		
		$effectiveQty		=	$supplyCostRec[10];
		 if ($effectiveQty==0) $effectiveQty = $rmsupplycostObj->getTotalWtChallanQty($selWtChallan);

		$commissionPerKg	=	$supplyCostRec[11];
		$totalCommiRate		=	$supplyCostRec[12];
		$fixedCommiRate		=	$supplyCostRec[13];

		$totalRMQuanty		=	$supplyCostRec[15];
		if ($totalRMQuanty==0) $totalRMQuanty = $rmsupplycostObj->getTotalWtChallanQty($selWtChallan);
		$handlingRatePerKg	=	$supplyCostRec[16];
		$totalHandlingAmt	=	$supplyCostRec[17];
		$fixedHandlingAmt	=	$supplyCostRec[18];
		
		#ice Fixed Checking
		if ($fixedIceCost!=0) 	$iceBlockChecked  = "Checked";
		#Transportation
		if ($fixedTransCost!=0)  $transportationChecked = "Checked";
		#Commission
		if ($fixedCommiRate!=0)  $commissionChecked = "Checked";
		#Handling
		if ($fixedHandlingAmt!=0)  $handlingChecked = "Checked";

		#List Challan  based on date
		$catchEntryRecords = $rmsupplycostObj->getCatchEntryRecords($challanDate);
		#Get Challan Details
		$RMRec		= $rmsupplycostObj->getRMRecord($selWtChallan);	

		
		$selOption 	= $supplyCostRec[19];
		if ($selOption=='I') $selIndividual = "Checked";
		if ($selOption=='G') $selGroup = "Checked";

		if ($p["editSelectionChange"]=='1' || $p["selCommission"]=="") {
			$selCommission 	= $supplyCostRec[20];	
		} else {
			$selCommission 	= $p["selCommission"];
		}
		//$selCommission 	= $supplyCostRec[20];	
		if ($selCommission=='D') $selCommiIndividual = "Checked";
		if ($selCommission=='S') $selCommiGroup = "Checked";
		
		$grandTotalCommiAmt = $supplyCostRec[21];


		if ($p["editSelectionChange"]=='1' || $p["selHandling"]=="") {
			$selHandling 	= $supplyCostRec[22];	
		} else {
			$selHandling 	= $p["selHandling"];
		}
		//$selHandling 	= $supplyCostRec[22];	
		if ($selHandling=='D') $selHandlingIndividual = "Checked";
		if ($selHandling=='S') $selHandlingGroup = "Checked";
	
		$grandTotalHadlngAmt = $supplyCostRec[23];

		$icePaid	= $supplyCostRec[24];
		if ($icePaid=='Y') $icePaidChk = "Checked";

		$tptPaid	= $supplyCostRec[25];;	
		if ($tptPaid=='Y') $tptPaidChk = "Checked";
	
		$commiPaid	= $supplyCostRec[26];
		if ($commiPaid=='Y') $commiPaidChk = "Checked";

		$hadlngPaid 	= $supplyCostRec[27];
		if ($hadlngPaid=='Y') $hadlngPaidChk = "Checked";
		
		if ($selCommission!="") $hasCommiChk = "Checked";

		if ($selHandling!="") $hasHandlingChk = "Checked";


		$readOnly 	=	"readOnly";
		$disabled  	=	"disabled";
		
		#For Selecting Process - Summary
		$processSummaryRecords	= $rmsupplycostObj->filterFishProcessSummaryRecords($selWtChallan);
	}
	
	# Update Record
	if ($p["cmdSaveChange"]!="") {
		
		$supplyCostId	=	$p["hidSupplyCostId"];
		
		$selOption = $p["selOption"];

		if ($selOption=='I') {
			$selWtChallanId 		=	$p["selWtChallan"];
	
			// Ice	
			$numIceBlocks			=	$p["numIceBlocks"];
			$costPerBlock			=	$p["costPerBlock"];
			$totalIceCost			=	$p["totalIceCost"];

			$chkFixedIceBlock		= 	$p["chkIceBlock"];
			if ($chkFixedIceBlock) {
				$fixedIceCost		=	$p["fixedIceCost"];
				$numIceBlocks		=	0;
				$costPerBlock		=	0;
				$totalIceCost		=	0;			
			} else {
				$fixedIceCost		=	0;	
			}

			// Transportation
			$km				=	$p["km"];
			$costPerKm			=	$p["costPerKm"];
			$totalTransAmt			=	$p["totalTransAmt"];
			
			$chkFixedTransportation 	= 	$p["chkTransportation"];
			if ($chkFixedTransportation) {
				$fixedTransCost		=	$p["fixedTransCost"];
				$km			=	0;
				$costPerKm		=	0;
				$totalTransAmt		=	0;
			} else {	
				$fixedTransCost		=	0;
			}

			$hasCommi	= $p["hasCommi"];

			$hasHandling	= $p["hasHandling"];

			# Commission 
			if ($hasCommi!="") {
				$selCommission = $p["selCommission"];
			} else {	// Remove all Commssion value from the existing Rec
				$selCommission = "";

				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$updateCatchEntryRec = $rmsupplycostObj->removeCatchEntryCommissionRec($dailyCatchEntryId);
					}
				}
				$grandTotalCommiAmt	= 0;
				$totalQuanty		= 0;
				$commissionPerKg	= 0;
				$totalCommiRate		= 0;	
				$chkFixedCommission	= "";
				$fixedCommiRate		= 0;
				$totalQuanty		= 0;
				$commissionPerKg	= 0;
				$totalCommiRate		= 0;				
			}

			if ($selCommission=='D') {	// If Has Commission Checked
			
				$grandTotalCommiAmt = $p["grandTotalCommiAmt"];
				$hidIvidualCommiRowCount = $p["hidIvidualCommiRowCount"]; //Comision detailed Rec Row Count
				for ($k=1;$k<=$hidIvidualCommiRowCount;$k++) {
					$dailyCatchEntryId = $p["dcEntryId_".$k];
					$processCodeId = $p["processCodeId_".$k];		
					$rate 	  = $p["rate_".$k];
					$totalAmt = $p["totalAmt_".$k];	
					if ($dailyCatchEntryId && $totalAmt!=0) {
						$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryCommissionRec($dailyCatchEntryId, $rate, $totalAmt);
					}
				}
				/*
				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$recProcessCodeId = $dcr[3];	
						$effectiveWt	  = $dcr[4];
						for ($k=1;$k<=$hidIvidualCommiRowCount;$k++) {
							$processCodeId = $p["processCodeId_".$k];		
							$rate 	  = $p["rate_".$k];
							$calcTotalAmt = $effectiveWt * $rate;
							$totalAmt = number_format($calcTotalAmt,2,'.','');
							//$ttotalAmt = $p["totalAmt_".$k];	
							if ($recProcessCodeId==$processCodeId)	{		
								$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryCommissionRec($dailyCatchEntryId, $rate, $totalAmt);
							}
						}
					}
				}
				*/
				// Clear Summary value
				$fixedCommiRate		=	0;
				$totalQuanty		=	0;
				$commissionPerKg	=	0;
				$totalCommiRate		=	0;		

			} else if ($selCommission=='S') {	// Commission Summary

				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$updateCatchEntryRec = $rmsupplycostObj->removeCatchEntryCommissionRec($dailyCatchEntryId);
					}
				}
				$grandTotalCommiAmt	= 0;

				// Commission
				$totalQuanty			=	$p["totalQuanty"];
				$commissionPerKg		=	$p["commissionPerKg"];
				$totalCommiRate			=	$p["totalCommiRate"];	

				$chkFixedCommission		=	$p["chkCommission"];
				if ($chkFixedCommission) {
					$fixedCommiRate		=	$p["fixedCommiRate"];
					$totalQuanty		=	0;
					$commissionPerKg	=	0;
					$totalCommiRate		=	0;	
				} else {
					$fixedCommiRate		=	0;
				}
			}

			# Handling 
			if ($hasHandling!="") {
				$selHandling = $p["selHandling"];
			} else {
				$selHandling = "";
				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$updateCatchEntryRec = $rmsupplycostObj->removeCatchEntryHandlingRec($dailyCatchEntryId);
					}
				}
				$grandTotalHadlngAmt 	= 0;
				$totalRMQuanty		= 0;
				$handlingRatePerKg	= 0;
				$totalHandlingAmt	= 0;		

				$chkFixedHandling	= "";
				$fixedHandlingAmt	= 0;
				$totalRMQuanty		= 0;
				$handlingRatePerKg	= 0;
				$totalHandlingAmt	= 0;	
			}
			
			if ($selHandling=='D') {
				$grandTotalHadlngAmt = $p["grandTotalHadlngAmt"];
				$hidDetailedHadlgRowCount = $p["hidDetailedHadlgRowCount"]; //Hadling detiled Rec Row Count
				
				for ($k=1;$k<=$hidDetailedHadlgRowCount;$k++) {
					$hDailyCatchEntryId = $p["hDcEntryId_".$k];
					$hProcessCodeId = $p["hProcessCodeId_".$k];
					$hRate 	  = $p["hRate_".$k];
					$hTotalAmt = $p["hTotalAmt_".$k];
					if ($hDailyCatchEntryId && $hTotalAmt!=0) {
						$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryHandlingRec($hDailyCatchEntryId, $hRate, $hTotalAmt);
					}
				}

				/*
				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$recProcessCodeId = $dcr[3];	
						$effectiveWt	  = $dcr[4];
						for ($k=1;$k<=$hidDetailedHadlgRowCount;$k++) {
							$hProcessCodeId = $p["hProcessCodeId_".$k];
							$hRate 	  = $p["hRate_".$k];				
							$calcTotalAmt = $effectiveWt * $hRate;
							$totalAmt = number_format($calcTotalAmt,2,'.','');
							if ($recProcessCodeId==$hProcessCodeId)	{		
								$updateCatchEntryRec = $rmsupplycostObj->updateCatchEntryHandlingRec($dailyCatchEntryId, $hRate, $totalAmt);
							}
						}
					}
				}
				*/

				//Clear handling Sumary Value
				$fixedHandlingAmt	=	0;
				$totalRMQuanty		=	0;
				$handlingRatePerKg	=	0;
				$totalHandlingAmt	=	0;				

			} else if ($selHandling=='S') { //Handling Summary

				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);

				if (sizeof($dailyCatchEntryRecords)>0) {

					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$updateCatchEntryRec = $rmsupplycostObj->removeCatchEntryHandlingRec($dailyCatchEntryId);
					}
				}
				$grandTotalHadlngAmt = 0;

				// Handling Cost
				$totalRMQuanty			=	$p["totalRMQuanty"];
				$handlingRatePerKg		=	$p["handlingRatePerKg"];
				$totalHandlingAmt		=	$p["totalHandlingAmt"];		

				$chkFixedHandling		=	$p["chkHandling"];
				if ($chkFixedHandling) {
					$fixedHandlingAmt	=	$p["fixedHandlingAmt"];
					$totalRMQuanty		=	0;
					$handlingRatePerKg	=	0;
					$totalHandlingAmt	=	0;	
				} else {
					$fixedHandlingAmt	=	0;
				}
			}		
	
			// To Be Paid Or Not
			$icePaid	= ($p["icePaid"]=="")?N:$p["icePaid"];
			$tptPaid	= ($p["tptPaid"]=="")?N:$p["tptPaid"];	
			$commiPaid	= ($p["commiPaid"]=="")?N:$p["commiPaid"];
			$hadlngPaid 	= ($p["hadlngPaid"]=="")?N:$p["hadlngPaid"];

		} // Individual Option End
		
		if ($supplyCostId!="") {	
			$supplyCostRecUptd = $rmsupplycostObj->updateSupplyCost($supplyCostId, $numIceBlocks, $costPerBlock, $totalIceCost, $fixedIceCost, $km, $costPerKm, $totalTransAmt, $fixedTransCost, $totalQuanty, $commissionPerKg, $totalCommiRate, $fixedCommiRate, $totalRMQuanty, $handlingRatePerKg, $totalHandlingAmt, $fixedHandlingAmt, $selOption, $selCommission, $grandTotalCommiAmt, $selHandling, $grandTotalHadlngAmt, $icePaid, $tptPaid, $commiPaid,  $hadlngPaid);
		}
	
		if ($supplyCostRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateSupplyCost);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplyCost.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateSupplyCost;
		}
		$supplyCostRecUptd	=	false;
	}
	
	
	# Delete 	
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		$selWtChallanId = "";
		for ($i=1; $i<=$rowCount; $i++) {
			$supplyCostId	=	$p["delId_".$i];
			$selWtChallanId = $p["dailyCatchMainEntryId_".$i];

			if ($supplyCostId!="") {				
				$supplyCostRecDel = $rmsupplycostObj->deleteSupplyCost($supplyCostId);	
				
				// Commission
				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$updateCatchEntryRec = $rmsupplycostObj->removeCatchEntryCommissionRec($dailyCatchEntryId);
					}
				}

				// Handling
				$dailyCatchEntryRecords = $rmsupplycostObj->filterDailyCatchEntryRecords($selWtChallanId);
				if (sizeof($dailyCatchEntryRecords)>0) {
					foreach($dailyCatchEntryRecords as $dcr) {
						$dailyCatchEntryId = $dcr[1];
						$updateCatchEntryRec = $rmsupplycostObj->removeCatchEntryHandlingRec($dailyCatchEntryId);
					}
				}	
			}
		}
		if ($supplyCostRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplyCost);
			$sessObj->createSession("nextPage",$url_afterDelSupplyCost.$dateSelection);
		} else {
			$errDel	=	$msg_failDelSupplyCost;
		}
		$supplyCostRecDel	=	false;
	}

## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
## ----------------- Pagination Settings I End ------------	


# select record between selected date
if($g["supplyFrom"]!="" && $g["supplyTill"]!=""){
	$dateFrom = $g["supplyFrom"];
	$dateTill = $g["supplyTill"];
} else if($p["supplyFrom"]!="" && $p["supplyTill"]!=""){
	$dateFrom = $p["supplyFrom"];
	$dateTill = $p["supplyTill"];
} else {
	$dateFrom = date("d/m/Y");
	$dateTill = date("d/m/Y");
}

#Supply Cost Record Search
if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {	
	//$offset = 0;
	//$page = 0;
		
	$Date1			=	explode("/",$dateFrom);
	$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

	$Date2			=	explode("/",$dateTill);
	$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
	
	$supplyCostRecords 	= $rmsupplycostObj->filterAllSupplyCostRecords($fromDate, $tillDate, $offset, $limit);
	$supplyCostRecordsSize	=	sizeof($supplyCostRecords);

	//For pagination
	$rmSupplyCostRecords	=	$rmsupplycostObj->fetchAllRecords($fromDate,$tillDate);
}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($rmSupplyCostRecords);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

if ($addMode==true) {	
	#Fetch Catch Entry Records based on Date
	if($selDate) $catchEntryRecords = $rmsupplycostObj->getCatchEntryRecords($selectDate);

	$selWtChallan=$p["selWtChallan"];
	if ($selWtChallan) {
	 	$landingCenterDistance 	= $rmsupplycostObj->findLandingcenterKm($selWtChallan);
		$effectiveQty		= $rmsupplycostObj->getTotalWtChallanQty($selWtChallan);
		$RMRec			= $rmsupplycostObj->getRMRecord($selWtChallan);	
	}

	#For Selecting Process - Summary
	$processSummaryRecords	= $rmsupplycostObj->filterFishProcessSummaryRecords($selWtChallan);
	
	$selFromDate = $p["dateFrom"];
	$selTillDate = $p["dateTill"];
	$selectSupplier = $p["supplier"];

	#Date From which challan SCD-> Supplier Challan Date, WCD-> WT challan Date	
	$dateSelectFrom = 'WCD';
	/*
	if ($g["dateSelectFrom"]!="") $dateSelectFrom = $g["dateSelectFrom"];
	else $dateSelectFrom = $p["dateSelectFrom"];
	if ($dateSelectFrom=='SCD') {
		$supplierChallanDate = "Checked";
	} else if($dateSelectFrom=='WCD')  {
		$wtChallanDate = "Checked";
	} else {
		$wtChallanDate = "Checked";
	}
	$searchType	= $p["searchType"];
	*/
	$fromDate	= mysqlDateFormat($selFromDate);
	$tillDate	= mysqlDateFormat($selTillDate);
	if ($selFromDate!="" && $selTillDate!="") {
		$supplierRecords = $rmsupplycostObj->fetchSupplierRecords($fromDate, $tillDate, $dateSelectFrom);
	}
	
	if ($selFromDate!="" && $selTillDate!="" && $selectSupplier!="") {
		$getProcessSummaryRecords = $rmsupplycostObj->getProcessSummaryRecords($fromDate, $tillDate, $selectSupplier, $dateSelectFrom, $searchType);
	}
	//$paymentBy	= $RMRec[9];
}
	
	if ($editMode)	$heading	= $label_editSupplyCost;
	else 		$heading	= $label_addSupplyCost;
	
	//$help_lnk="help/hlp_GradeMaster.html";

	if ($addMode || $editMode) $ON_LOAD_FN = "return supplyCostHide();";

	$ON_LOAD_PRINT_JS	= "libjs/rmsupplycost.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmRMSupplyCost" action="RMSupplyCost.php" method="post">
    <table cellspacing="0"  align="center" cellpadding="0" width="60%">	
    <tr> 
      <td height="10" align="center">&nbsp;</td>
    </tr>
    <tr> 
      <td height="10" align="center" class="err1" > 
        <? if($err!="" ){?>
        <?=$err;?>
        <?}?>
      </td>
    </tr>
	
    <?
	if ($editMode || $addMode) {
    ?>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; 
                    <?=$heading;?>
                  </td>
                </tr>		 
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > 
		<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('RMSupplyCost.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSupplyCost(document.frmRMSupplyCost);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('RMSupplyCost.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSupplyCost(document.frmRMSupplyCost);">  
			 <input type="hidden" name="cmdAddNew" value="1">
		        </td>
                        <?}?>
                      </tr>
                      <input type="hidden" name="hidSupplyCostId" value="<?=$editSupplyCostId;?>">
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>	
			<tr>
			<TD>
			<table width="100" border="0">
                         <tr>
                             <td>
				<input name="selOption" id="selOption0" type="radio" value="I" class="chkBox" <?=$selIndividual?> onclick="this.form.submit();">
			     </td>
                             <td class="listing-item" nowrap> Individual  </td>
                             <td>
			  	<input name="selOption" id="selOption1" type="radio" class="chkBox" value="G" <?=$selGroup?> onclick="this.form.submit();"></td>
                             <td class="listing-item" nowrap> Group </td>
                          </tr>
                      </table></TD></tr>
		<? if ($selIndividual) {?>		
                      <tr>
                        <td colspan="2" >
			<table>				
			  <tr>
				<TD>
				<table><TR><td class="fieldName">Date </td>
                            <td>
				<input name="selDate" type="text" id="selDate" size="8" value="<?=$selDate?>" autocomplete="off" onchange="this.form.submit();" <?=$readOnly?> />
			</td>
                            </tr>
                            <tr>
                              <td class="fieldName">Wt Challan No:</td>
                              <td>
				<? if($addMode) $selWtChallan=$p["selWtChallan"];?>
				<select name="selWtChallan" onchange="this.form.submit();" <?=$disabled?>>
				<option value="">-- Select --</option>
				<?php
				foreach ($catchEntryRecords as $cer) {							$catchEntryId		=	$cer[0];
					$catchEntryWeighChallanNo =	stripSlash($cer[1]);
					$displayChallanNum	= $cer[2];
					$selected="";
					if ($selWtChallan==$catchEntryId) $selected="Selected";
				?>
				<option value="<?=$catchEntryId?>" <?=$selected?>><?=$displayChallanNum?></option>
				<? }?>
                        </select></td></TR>
				</table>				
				</TD>
			  </tr>
	<!-- Display Title Block Start Here -->
	<? if($selWtChallan) {?>
	<tr><TD><table width="100%" cellpadding="0" cellspacing="0">
	<?
	#Finding Supplier Record
	$supplierRec	=	$supplierMasterObj->find($RMRec[7]);
	$supplierName	=	$supplierRec[2];
	
	#Finding Landing Center Record
	$centerRec		=	$landingcenterObj->find($RMRec[6]);
	$landingCenterName	=	stripSlash($centerRec[1]);
	
	#Finding Plant Record
	$plantRec		=	$plantandunitObj->find($RMRec[1]);
	$plantName		=	stripSlash($plantRec[2]);
		
	$Date1			=	explode("-",$RMRec[2]); //2007-06-27
	$date			= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[2], $Date1[0]));
	
	$selectTime		=	explode("-",$RMRec[3]);
	$time			=	$selectTime[0].":".$selectTime[1]."&nbsp;".$selectTime[2];
	
	$vechNo			=	$RMRec[4];	
	//$selectedWeighmentNo	=	$RMRec[5];
	$selectedWeighmentNo	=	$RMRec[8];
	?>
	<tr><td colspan="3">
	<fieldset>
	<table width="100%" cellpadding="0" cellspacing="0">
        <tr> 
         <td valign="top">		  
	  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Supplier:</td>
                <td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$supplierName?></td>
              </tr>
            </table></td>
          <td valign="top">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">Landing Center:</td>
                <td class="listing-item"><?=$landingCenterName?></td>
              </tr>
            </table></td>
          <td valign="top">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap style="padding-right:5px;">Wt Challan No:</td>
                <td class="listing-item" nowrap><?=$selectedWeighmentNo?></td>
              </tr>
            </table></td>
          </tr>
        <tr> 
          <td class="fieldName">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Supplied At:</td>
                <td class="listing-item" nowrap="nowrap"><?=$plantName?></td>
              </tr>
            </table></td>
          <td class="listing-item"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">Date/Time:</td>
                <td class="listing-item" nowrap><?=$date?>-<?=$time?>&nbsp;&nbsp;</td>
              </tr>
            </table></td>
          <td class="fieldName"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Vehicle No:</td>
                <td class="listing-item" nowrap>&nbsp;<?=$vechNo?></td>
              </tr>
            </table></td>
          </tr>
      </table></fieldset></td></tr></table></TD></tr>
	<? }?>
	<!-- title Block End Here -->
                            <tr>
                            	<td  colspan="2" valign="top">
				<table>
				<TR>
				<TD valign="top">
				<fieldset class="fieldName"><legend>Ice <INPUT type="checkbox" name="chkIceBlock" value="I" onclick="return showFixedIceCost();" id="chkIceBlock" <?=$iceBlockChecked?> class="chkBox"><span class="listing-item">Fixed</span> &nbsp;<INPUT type="checkbox" name="icePaid" value="Y" id="icePaid" <?=$icePaidChk?> class="chkBox"><span class="listing-item">Paid</span></legend> 
				<table>
				<tr><TD valign="top">
				<div id="iceBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">No.of blocks:</td>
					<td><INPUT type="text" name="numIceBlocks" size="5" style="text-align:right;" id="numIceBlocks" onkeyup="calcIceBlockTotalRate();" value="<?=$numIceBlocks?>"></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName">Cost/ Block:
                                            </TD>
                                            <TD class="fieldName">
					    <INPUT type="text" name="costPerBlock" size="5" style="text-align:right;" id="costPerBlock" onkeyup="calcIceBlockTotalRate();" value="<?=$costPerBlock?>">
                                            </TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName">Total Cost:</TD>
                                            <TD><INPUT type="text" name="totalIceCost" size="5" style="text-align:right;" readonly id="totalIceCost" value="<?=$totalIceCost?>">
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<tr>
						<td valign="top">
						<div id="fixedIceBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Cost:</td>
							<TD><INPUT type="text" name="fixedIceCost" size="5" style="text-align:right;" value="<?=$fixedIceCost?>">
							</TD></tr>
						</table>
						</div>
						</td>
					</tr>
                                        </table>
				</fieldset>
				</TD>
				<td valign="top" style="padding-left:10px; padding-right:10px;">
				<table>
				<TR>
				<TD valign="top" nowrap>
				<fieldset class="fieldName"><legend>Transportation <INPUT type="checkbox" name="chkTransportation" value="T" onclick="return showFixedTransCost();" id="chkTransportation" <?=$transportationChecked?> class="chkBox"><span class="listing-item">Fixed</span>&nbsp;<INPUT type="checkbox" name="tptPaid" value="Y" id="tptPaid" <?=$tptPaidChk?> class="chkBox"><span class="listing-item">Paid</span></legend> 
				<table>
				<tr><TD valign="top">
				<div id="transportationBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">Km :</td>
					<td><INPUT type="text" name="km" size="5" value="<?=$landingCenterDistance?>" style="text-align:right;" id="km" onkeyup="calcTransportationTotalAmt();"></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName">Cost/ Km:
                                            </TD>
                                            <TD class="fieldName">
					    <INPUT type="text" name="costPerKm" size="5" style="text-align:right;" id="costPerKm" onkeyup="calcTransportationTotalAmt();" value="<?=$costPerKm?>">
                                            </TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Total Amt:</TD>
                                            <TD><INPUT type="text" name="totalTransAmt" size="5" style="text-align:right;" readonly id="totalTransAmt" value="<?=$totalTransAmt?>">
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<tr>
						<td>
						<div id="fixedTransBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Cost:</td>
							<TD><INPUT type="text" name="fixedTransCost" size="5" style="text-align:right;" value="<?=$fixedTransCost?>">
							</TD></tr>
						</table>
						</div>
						</td>
					</tr>
                                        </table>
				</fieldset>
				</TD></TR>
				</table></td>
				<!--td valign="top">
				<table>
				<TR>
				<TD>
				<fieldset class="fieldName"><legend>Commission<INPUT type="checkbox" name="chkCommission" value="C" onclick="return showFixedCommissionCost();" id="chkCommission" <?=$commissionChecked?> class="chkBox"><span class="listing-item">Fixed</span></legend> 
				<table>
				<tr><TD>
				<div id="commissionBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">Total Qty :</td>
					<td><INPUT type="text" name="totalQuanty" size="6" value="<?=$effectiveQty?>" style="text-align:right;" readonly id="totalQuanty" onkeyup="calcCommissionTotalRate();"></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Commi/ Kg:
                                            </TD>
                                            <TD class="fieldName">
					    <INPUT type="text" name="commissionPerKg" size="5" style="text-align:right;" id="commissionPerKg" onkeyup="calcCommissionTotalRate();" value="<?=$commissionPerKg?>">
                                            </TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName">Total Rate:</TD>
                                            <TD><INPUT type="text" name="totalCommiRate" size="5" style="text-align:right;" readonly id="totalCommiRate" value="<?=$totalCommiRate?>">
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<tr>
						<td>
						<div id="fixedCommiBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Rate:</td>
							<TD><INPUT type="text" name="fixedCommiRate" size="5" value="<?=$fixedCommiRate?>" style="text-align:right;">
							</TD></tr>
						</table>
						</div>
						</td>
					</tr>
                                        </table>
				</fieldset>
				</TD></TR>
				</table></td-->
<!-- 	handling Charge Cost -->
				<!--td valign="top">
				<table>
				<TR>
				<TD>
				<fieldset class="fieldName"><legend>Handling<INPUT type="checkbox" name="chkHandling" value="H" onclick="return showFixedHandlingCost();" id="chkHandling" <?=$handlingChecked?> class="chkBox"><span class="listing-item">Fixed</span></legend> 
				<table>
				<tr><TD>
				<div id="handlingBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">RM Qty :</td>
					<td><INPUT type="text" name="totalRMQuanty" size="6" value="<?=$effectiveQty?>" style="text-align:right;" readonly id="totalRMQuanty" onkeyup="calcHandlingTotalAmt();"></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Rate:
                                            </TD>
                                            <TD class="fieldName">
					    <INPUT type="text" name="handlingRatePerKg" size="5" style="text-align:right;" id="handlingRatePerKg" onkeyup="calcHandlingTotalAmt();" value="<?=$handlingRatePerKg?>">
                                            </TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Total Amt:</TD>
                                            <TD><INPUT type="text" name="totalHandlingAmt" size="5" style="text-align:right;" readonly id="totalHandlingAmt" value="<?=$totalHandlingAmt?>">
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<tr>
						<td>
						<div id="fixedHandlingBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Amt:</td>
							<TD><INPUT type="text" name="fixedHandlingAmt" size="5" value="<?=$fixedHandlingAmt?>" style="text-align:right;">
							</TD></tr>
						</table>
						</div>
						</td>
					</tr>
                                        </table>
				</fieldset>
				</TD></TR>
				</table></td-->
				</TR>			
				</table>
				</td>	
                            </tr>
			<tr>
		<TD valign="top">
		<table>
			<tr>
				<TD>
					<table>
						<TR>
							<TD>
						<INPUT type="checkbox" name="hasCommi" id="hasCommi" value="Y" class="chkBox" onclick="showHasCommi();" <?=$hasCommiChk?>>
							</TD>
							<TD class="listing-item">Has Commission</TD>
						</TR>
					</table>
				</TD>
				<TD>
					<table>
						<TR>
							<TD>
							<INPUT type="checkbox" name="hasHandling" id="hasHandling" class="chkBox" onclick="showHasHandling();" value="Y" <?=$hasHandlingChk?>>
							</TD>
							<TD class="listing-item">Has Handling</TD>
						</TR>
					</table>
				</TD>
			</tr>
			<TR>
			<TD valign="top">
			<div id="divHasCommi" style="display:block">
			<table>
			<TR>
				<TD><fieldset><legend class="fieldName">Commission<input name="selCommission" id="selCommission0" type="radio" class="chkBox" value="D" <?=$selCommiIndividual?> <? if (!$editId) {?> onclick="this.form.submit();"<? } else {?> onclick="this.form.editId.value=<?=$editId;?>;this.form.submit();" <? }?>><span class="listing-item">Detailed</span><input name="selCommission" id="selCommission1" type="radio" class="chkBox" value="S" <?=$selCommiGroup?> <? if (!$editId) {?> onclick="this.form.submit();"<? } else {?> onclick="this.form.editId.value=<?=$editId;?>;this.form.submit();" <? }?>><span class="listing-item">Summary</span>&nbsp;<INPUT type="checkbox" name="commiPaid" value="Y" id="commiPaid" <?=$commiPaidChk?> class="chkBox"><span class="listing-item">Paid</span></legend>
				<table>
					<?if ($selCommiGroup) {?>
					<TR>
						<TD><table>
				<TR>
				<TD>
				<fieldset class="fieldName"><legend><INPUT type="checkbox" name="chkCommission" value="C" onclick="return showFixedCommissionCost();" id="chkCommission" <?=$commissionChecked?> class="chkBox"><span class="listing-item">Fixed</span></legend> 
				<table>
				<tr><TD>
				<div id="commissionBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">Total Qty :</td>
					<td><INPUT type="text" name="totalQuanty" size="6" value="<?=$effectiveQty?>" style="text-align:right;" readonly id="totalQuanty" onkeyup="calcCommissionTotalRate();"></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Commi/ Kg:
                                            </TD>
                                            <TD class="fieldName">
					    <INPUT type="text" name="commissionPerKg" size="5" style="text-align:right;" id="commissionPerKg" onkeyup="calcCommissionTotalRate();" value="<?=$commissionPerKg?>">
                                            </TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName">Total Rate:</TD>
                                            <TD><INPUT type="text" name="totalCommiRate" size="5" style="text-align:right;" readonly id="totalCommiRate" value="<?=$totalCommiRate?>">
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<tr>
						<td>
						<div id="fixedCommiBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Rate:</td>
							<TD><INPUT type="text" name="fixedCommiRate" size="5" value="<?=$fixedCommiRate?>" style="text-align:right;">
							</TD></tr>
						</table>
						</div>
						</td>
					</tr>
                                        </table>
				</fieldset>
				</TD></TR>
				</table></TD>
					</TR>
					<? }?>
				<? if ($selCommiIndividual) {?>
				<tr><TD>
				<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
				<?
				if( sizeof($processSummaryRecords)){
				?>
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>
				    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</td>
				       <?php
						if ($RMRec[9]=='D') {
					?>	
					    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.Count</td>
					<?php
						}
					?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Qty</td>					
				     <td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>	
				      <td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
                                  </tr>
        <?
	$k=0;
	$totalWt	=	"";
	foreach($processSummaryRecords as $psr){
		$k++;
		$processCodeId	=	$psr[2];
		$processCode	=	$psr[5];
		$totalQty	=	$psr[4];
		$totalWt	+=	$totalQty;
		if ($editMode) {
			$commiRate	=	$psr[6];
			$commiAmt	=	$psr[7];
		}
		if ($p["rate_".$k]!="") $commiRate = $p["rate_".$k]; 
				
		$displayGradeCount = "";
		$countValue = $psr[10];
		if ($countValue!="") {
			$displayGradeCount = $countValue;
		} else {
			$displayGradeCount = $grademasterObj->findGradeCode($psr[11]);
		}
		$paymentBy	= $psr[12];
		$dcEntryId	= $psr[13];		
		if ($paymentBy=='D') {
			$declWtRecords  = $rmsupplycostObj->declWtRecords($dcEntryId);
		}
	?>
       <tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<input type="hidden" name="dcEntryId_<?=$k?>" id="dcEntryId_<?=$k?>" value="<?=$dcEntryId?>">
		<input type="hidden" name="processCodeId_<?=$k?>" id="processCodeId_<?=$k?>" value="<?=$processCodeId?>">
		<?=$processCode?>
	</td> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<?=$displayGradeCount?>
	</td>
	<?php
		if ($paymentBy=='D') {
	?>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<table>
		<tr>
		<?php
			$numLine = 3;
			if (sizeof($declWtRecords)>0) {				
				$nextRec	=	0;				
				$selDeclCount = "";
				foreach ($declWtRecords as $rv) {					
					$selDeclCount = $rv[4];
					$nextRec++;
		?>
		<td class="home-listing-item">
			<? if($nextRec>1) echo ",";?><?=$selDeclCount?>
		</td>
			<?php 
				if($nextRec%$numLine == 0) { 
			?>
			</tr>
		<tr>
		<?php 
				} 	
		    	} # For Loop Ends Here
		 }
		?>
		</tr>
		</table>
	</td>
	<?php
		}
	?>
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" name="totalQty_<?=$k?>" id="totalQty_<?=$k?>" value="<?=$totalQty?>"><?=$totalQty?>
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<input type="text" name="rate_<?=$k?>" id="rate_<?=$k?>" value="<?=$commiRate?>" size="4" onkeyup="calcComisionIdividalRate();" style="text-align:right;" autocomplete="off">
	</td>	
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<input type="text" name="totalAmt_<?=$k?>" id="totalAmt_<?=$k?>" value="<?=$commiAmt?>" readonly size="7" style="text-align:right;border:none;">
	</td>	
       </tr>
       <? } ?>
	<input type="hidden" name="hidIvidualCommiRowCount" id="hidIvidualCommiRowCount" value="<?=$k?>">
  	<tr bgcolor="#FFFFFF">
          <td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;" colspan="2">TOTAL</td>
	  <!--<td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;"></td>-->	
	 <?php
		if ($RMRec[9]=='D') {
	?>	
	    <td class="listing-head" style="padding-left:5px; padding-right:5px;"></td>
	<?php
		}
	?>	
         <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;">
		<strong><? echo number_format($totalWt,2);?> </strong>
	</td>
	  <td></td>
	 <td align="right"><input type="text" name="grandTotalCommiAmt" id="grandTotalCommiAmt" value="<?=$grandTotalCommiAmt?>" size="7" style="text-align:right; border:none;"></td>
        </tr>
	<? }?>
        </table>
	</TD></tr>
	<? }?>
	</table>	
	</fieldset></TD>
	</TR>
	</table>
</div>
	</TD>
	<td valign="top">
	<div id="divHasHandling" style="display:block">
	<table>
		<TR>
				<TD><fieldset><legend class="fieldName">Handling<input name="selHandling" id="selHandling0" type="radio" class="chkBox" value="D" <?=$selHandlingIndividual?> <? if (!$editId) {?> onclick="this.form.submit();"<? } else {?> onclick="this.form.editId.value=<?=$editId;?>;this.form.submit();" <? }?>><span class="listing-item">Detailed</span><input name="selHandling" id="selHandling1" type="radio" class="chkBox" value="S" <?=$selHandlingGroup?> <? if (!$editId) {?> onclick="this.form.submit();"<? } else {?> onclick="this.form.editId.value=<?=$editId;?>;this.form.submit();" <? }?>><span class="listing-item">Summary</span>&nbsp;<INPUT type="checkbox" name="hadlngPaid" value="Y" id="hadlngPaid" <?=$hadlngPaidChk?> class="chkBox"><span class="listing-item">Paid</span></legend>
				<table>
					<?if ($selHandlingGroup) {?>
					<TR>
						<TD><table>
				<TR>
				<TD>
				<fieldset class="fieldName"><legend><INPUT type="checkbox" name="chkHandling" value="H" onclick="return showFixedHandlingCost();" id="chkHandling" <?=$handlingChecked?> class="chkBox"><span class="listing-item">Fixed</span></legend> 
				<table>
				<tr><TD>
				<div id="handlingBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">RM Qty :</td>
					<td><INPUT type="text" name="totalRMQuanty" size="6" value="<?=$effectiveQty?>" style="text-align:right;" readonly id="totalRMQuanty" onkeyup="calcHandlingTotalAmt();"></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Rate:
                                            </TD>
                                            <TD class="fieldName">
					    <INPUT type="text" name="handlingRatePerKg" size="5" style="text-align:right;" id="handlingRatePerKg" onkeyup="calcHandlingTotalAmt();" value="<?=$handlingRatePerKg?>">
                                            </TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Total Amt:</TD>
                                            <TD><INPUT type="text" name="totalHandlingAmt" size="5" style="text-align:right;" readonly id="totalHandlingAmt" value="<?=$totalHandlingAmt?>">
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<tr>
						<td>
						<div id="fixedHandlingBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Amt:</td>
							<TD><INPUT type="text" name="fixedHandlingAmt" size="5" value="<?=$fixedHandlingAmt?>" style="text-align:right;">
							</TD></tr>
						</table>
						</div>
						</td>
					</tr>
                                        </table>
				</fieldset>
				</TD></TR>
				</table></TD>
					</TR>
					<? }?>
				<? if ($selHandlingIndividual) {?>
				<tr><TD>
				<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
				<?
				if( sizeof($processSummaryRecords)){
				?>
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                     <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>
				    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</td>
				       <?php
						if ($RMRec[9]=='D') {
					?>	
					    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.Count</td>
					<?php
						}
					?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Qty</td>					
				     <td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>	
				      <td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
                                  </tr>
        <?
	$m=0;
	$totalWt	=	"";
	foreach($processSummaryRecords as $psr){
		$m++;
		$hProcessCodeId	=	$psr[2];
		$hProcessCode	=	$psr[5];
		$hTotalQty	=	$psr[4];
		$hTotalWt	+=	$hTotalQty;
		if ($editMode) {
			$hRate		=	$psr[8];	
			$hTotalAmt	=	$psr[9];
		}

		if ($p["hRate_".$m]!="") $hRate = $p["hRate_".$m];

		$hDisplayGradeCount = "";
		$hCountValue = $psr[10];
		if ($hCountValue!="") {
			$hDisplayGradeCount = $hCountValue;
		} else {
			$hDisplayGradeCount = $grademasterObj->findGradeCode($psr[11]);
		}
		$hPaymentBy	= $psr[12];
		$hDcEntryId	= $psr[13];		
		if ($hPaymentBy=='D') {
			$declWtRecords  = $rmsupplycostObj->declWtRecords($hDcEntryId);
		}
	?>
       <tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<input type="hidden" name="hDcEntryId_<?=$k?>" id="hDcEntryId_<?=$k?>" value="<?=$hDcEntryId?>">
		<input type="hidden" name="hProcessCodeId_<?=$m?>" id="hProcessCodeId_<?=$m?>" value="<?=$hProcessCodeId?>"><?=$hProcessCode?>
	</td> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<?=$hDisplayGradeCount?>
	</td>
	<?php
		if ($hPaymentBy=='D') {
	?>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<table>
		<tr>
		<?php
			$numLine = 3;
			if (sizeof($declWtRecords)>0) {				
				$nextRec	=	0;				
				$selDeclCount = "";
				foreach ($declWtRecords as $rv) {					
					$selDeclCount = $rv[4];
					$nextRec++;
		?>
		<td class="home-listing-item">
			<? if($nextRec>1) echo ",";?><?=$selDeclCount?>
		</td>
			<?php 
				if($nextRec%$numLine == 0) { 
			?>
			</tr>
		<tr>
		<?php 
				} 	
		    	} # For Loop Ends Here
		 }
		?>
		</tr>
		</table>
	</td>
	<?php
		}
	?>
       <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" name="hTotalQty_<?=$m?>" id="hTotalQty_<?=$m?>" value="<?=$hTotalQty?>"><?=$hTotalQty?>
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<input type="text" name="hRate_<?=$m?>" id="hRate_<?=$m?>" value="<?=$hRate?>" size="4" onkeyup="calcHadlngDetailedRate();" style="text-align:right;" autocomplete="off">
	</td>	
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<input type="text" name="hTotalAmt_<?=$m?>" id="hTotalAmt_<?=$m?>" value="<?=$hTotalAmt?>" readonly size="7" style="text-align:right;border:none;">
	</td>	
       </tr>
       <? } ?>
	<input type="hidden" name="hidDetailedHadlgRowCount" id="hidDetailedHadlgRowCount" value="<?=$m?>">
  	<tr bgcolor="#FFFFFF">
          <td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;" colspan="2">TOTAL</td>
	  <!--<td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;"></td>-->	
	 <?php
		if ($RMRec[9]=='D') {
	?>	
	    <td class="listing-head" style="padding-left:5px; padding-right:5px;"></td>
	<?php
		}
	?>
          <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;">
		<strong><? echo number_format($hTotalWt,2);?> </strong>
	  </td>
	  <td></td>
	 <td align="right"><input type="text" name="grandTotalHadlngAmt" id="grandTotalHadlngAmt" value="<?=$grandTotalHadlngAmt?>" size="7" style="text-align:right; border:none;"></td>
        </tr>
	<? }?>
        </table>
	</TD></tr>
	<? }?>
	</table>	
	</fieldset></TD>
	</TR>
	</table>
</div>
	</td>
	</TR>
	</table>
	</TD>
	</tr>
        </table></td>
                      </tr>
			<? }?>
			<? if ($selGroup) {?>
	<tr>
	<TD>
		<table>
		<TR>
			<TD>
				<table>
					<tr><TD>
					<table>
						<tr> 
                                    <td class="fieldName">From:</td>
                                    <td> 
                                      <? $selFromDate = $p["dateFrom"];?>
                                      <input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$selFromDate?>" onchange="this.form.submit();" autoComplete="off"></td>
					 <td class="fieldName">Till:</td>
                                    <td> 
                                      <? $selTillDate = $p["dateTill"];?>
                                      <input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$selTillDate?>" onchange="this.form.submit();" autoComplete="off"></td>
                                  </tr>
					</table>	
					</TD></tr>
					 
		<!--<tr>
					<TD colspan="4">
					<table>
						<TR>
							<TD><fieldset>
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
                                        </table></td>
                                        <td><table width="100" border="0">
                                          <tr>
                                            <td><input name="dateSelectFrom" type="radio" value="WCD" onClick="this.form.submit();" <?=$wtChallanDate?> class="chkBox"></td>
                                            <td nowrap class="listing-item">Wt Challan Date</td>
                                          </tr>
                                        </table>
					</td>
                                      </tr>
                                    </table>
					</fieldset></TD>
						</TR>
					</table>
					</TD>
				</tr>-->
				</table>
			</TD>
			<TD valign="top">
				<table>
					<tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td> 
                                      <? $selectSupplier = $p["supplier"];?>
                                <select name="supplier" id="supplier" onchange="this.form.submit();">
				<option value="">-- Select All --</option>
                                <?
				foreach ($supplierRecords as $fr) {
					$supplierId = $fr[0];
					$supplierName	=	stripSlash($fr[2]);
					$selected	=	"";
					if ($supplierId == $selectSupplier) $selected = "selected";
				?>
                                <option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
                                <? } ?>
                                </select></td>
                                  </tr>
		<?php
			if ($dateSelectFrom=='SCD') {
		?>
			<!--<tr> 
                             <td class="fieldName" nowrap="true">Search:</td>
			     <td nowrap="true">
				<select name="searchType" onchange="this.form.submit();">
					<option value="PS" <?if($searchType=='PS') echo "selected";?>>Process Code wise</option>
					<option value="CS" <?if($searchType=='CS') echo "selected";?>>Count wise</option>
				</select>
				</td>	
			</tr>-->
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
				<TD>
			<table>
				<!--<TR>
				<TD>
				<table>
				 <tr> 
                                    <td class="fieldName"> From:</td>
                                    <td> 
                                      <? $selFromDate = $p["dateFrom"];?>
                                      <input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$selFromDate?>" onchange="this.form.submit();" autoComplete="off"></td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName"> Till:</td>
                                    <td> 
                                      <? $selTillDate = $p["dateTill"];?>
                                      <input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$selTillDate?>" onchange="this.form.submit();" autoComplete="off"></td>
                                  </tr>
				<tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td> 
                                      <? $selectSupplier = $p["supplier"];?>
                                <select name="supplier" id="supplier" onchange="this.form.submit();">
				<option value="">-- Select All --</option>
                                <?
				foreach ($supplierRecords as $fr) {
					$supplierId = $fr[0];
					$supplierName	=	stripSlash($fr[2]);
					$selected	=	"";
					if ($supplierId == $selectSupplier) $selected = "selected";
				?>
                                <option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
                                <? } ?>
                                </select></td>
                                  </tr>
				</table>
					</TD>
					</TR>-->
	<!-- 	Commission Starts here	 -->
				<?
				if (sizeof($getProcessSummaryRecords)) {
				?>	
				<TR>
					<TD>
					<table>
					<TR><TD>
					<fieldset>
					<legend class="listing-item">Commission</legend>
					<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
				
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>
				    <?php
					if ($searchType=='CS') {
				    ?>
				    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</td>
					<?php
					}
					?>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Qty</td>
				     <td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>	
				      <td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
                                  </tr>
        <?
	$k=0;
	$totalWt	=	"";
	$prevProcessCode	= "";
	foreach($getProcessSummaryRecords as $psr) {
		$processCodeId	=	$psr[2];

		$pCode		= 	$psr[5];
		$processCode	= "";
		if ($prevProcessCode!=$pCode) {
			$processCode	=	$psr[5];
		}
		$totalQty	=	$psr[4];			
		$totalWt	+=	$totalQty;
		$declCount	= 	$psr[6];

		#Checking RM Settled
		$challanIdividuallySet = $rmsupplycostObj->checkRMIndividuallySet($fromDate, $tillDate, $selectSupplier, $processCodeId);
		if (!$challanIdividuallySet) {		
			$k++;
	?>
       <tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<input type="hidden" name="processCodeId_<?=$k?>" id="processCodeId_<?=$k?>" value="<?=$processCodeId?>"><?=$processCode?>
	</td>
	 <?php
		if ($searchType=='CS') {
    	?>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<?=$declCount?>
	</td>
	<?php
		}
	?>
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" name="totalQty_<?=$k?>" id="totalQty_<?=$k?>" value="<?=$totalQty?>">
			<?=$totalQty?>
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="rate_<?=$k?>" id="rate_<?=$k?>" value="" size="4" onkeyup="calcComisionIdividalRate();" style="text-align:right;"></td>	
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="totalAmt_<?=$k?>" id="totalAmt_<?=$k?>" value="" readonly size="7" style="text-align:right;border:none;"></td>	
       </tr>
	<? } else {?>
	<tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20"><?=$processCode?></td> 
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$totalQty?></td>
	<td class="err1" style="padding-left:5px; padding-right:5px;" colspan="2" nowrap>Few of the challan individually settled </td>	
       </tr>
	<? }?>
       <?php
		$prevProcessCode = $pCode;
		 }  // Loop Ends here
	?>
	<input type="hidden" name="hidIvidualCommiRowCount" id="hidIvidualCommiRowCount" value="<?=$k?>">
  	<tr bgcolor="#FFFFFF">
          <td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL</td>
	 <?php
		if ($searchType=='CS') {
    	?>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
	<?php
		}
	?>
          <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?> </strong></td>
	  <td></td>
	 <td align="center" style="padding-left:5px; padding-right:5px;" ><input type="text" name="grandTotalCommiAmt" id="grandTotalCommiAmt" value="<?=$grandTotalCommiAmt?>" size="7" style="text-align:right; border:none;"></td>
        </tr>	
        </table>
	</fieldset>
	</TD></TR>
	</table></TD></TR>
	<? }?>
<!-- 	Commission Ends Here-->
				<?
				if (sizeof($getProcessSummaryRecords)) {
				?>	
				<TR>
					<TD>
					<table>
					<TR><TD>
					<fieldset>
					<legend class="listing-item">Handling</legend>
					<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
				
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>
				     <?php
					if ($searchType=='CS') {
				    ?>
				    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</td>
					<?php
					}
					?>				
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Qty</td>
				     <td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>	
				      <td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
                                  </tr>
        <?
	$m=0;
	$totalWt	=	"";
	$hPrevProcessCode	= "";
	foreach($getProcessSummaryRecords as $psr){
		$hProcessCodeId	=	$psr[2];
		
		$pCode		= 	$psr[5];
		$hProcessCode	= "";
		if ($hPrevProcessCode!=$PCode) {
			$hProcessCode	=	$psr[5];
		}
		$hTotalQty	=	$psr[4];
		$hTotalWt	+=	$hTotalQty;
		$hRate		=	$psr[8];	
		$hTotalAmt	=	$psr[9];
		#Checking RM Settled
		$challanIdividuallySet = $rmsupplycostObj->checkRMIndividuallySet($fromDate, $tillDate, $selectSupplier, $hProcessCodeId);
		if (!$challanIdividuallySet) {	
			$m++;
	?>
       <tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20"><input type="hidden" name="hProcessCodeId_<?=$m?>" id="hProcessCodeId_<?=$m?>" value="<?=$hProcessCodeId?>"><?=$hProcessCode?></td> 
	 <?php
		if ($searchType=='CS') {
    	?>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;">
		<?=$declCount?>
	</td>
	<?php
		}
	?>
       <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><input type="hidden" name="hTotalQty_<?=$m?>" id="hTotalQty_<?=$m?>" value="<?=$hTotalQty?>"><?=$hTotalQty?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="hRate_<?=$m?>" id="hRate_<?=$m?>" value="<?=$hRate?>" size="4" onkeyup="calcHadlngDetailedRate();" style="text-align:right;"></td>	
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><input type="text" name="hTotalAmt_<?=$m?>" id="hTotalAmt_<?=$m?>" value="<?=$hTotalAmt?>" readonly size="7" style="text-align:right;border:none;"></td>	
       </tr>
	<? } else {?>
	<tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20"><?=$processCode?></td> 
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$totalQty?></td>
	<td class="err1" style="padding-left:5px; padding-right:5px;" colspan="2" nowrap>Few of the challan individually settled </td>	
       </tr>
       <? } 
	$prevProcessCode = $pCode;
	} ?>
	<input type="hidden" name="hidDetailedHadlgRowCount" id="hidDetailedHadlgRowCount" value="<?=$m?>">
  	<tr bgcolor="#FFFFFF">
          <td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL</td>
          <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($hTotalWt,2);?> </strong></td>
	  <td></td>
	 <td align="center" style="padding-left:5px; padding-right:5px;"><input type="text" name="grandTotalHadlngAmt" id="grandTotalHadlngAmt" value="<?=$grandTotalHadlngAmt?>" size="7" style="text-align:right; border:none;"></td>
        </tr>
	
        </table>
	</fieldset>
	</TD></TR>
	</table></TD></TR>
	<? }?>
				</table>
				</TD>
			</tr>
			<? }?>
                      <tr> 
                        <td  height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMSupplyCost.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSupplyCost(document.frmRMSupplyCost);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('RMSupplyCost.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSupplyCost(document.frmRMSupplyCost);"></td>
                        <?}?>
                      </tr>
                      <tr> 
                        <td  height="10" ></td>
			<td colspan="2"  height="10" ></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
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
      <td height="10" align="center"></td>
    </tr>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" style="background-repeat:repeat-x;" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName"  nowrap style="background-repeat: repeat-x" valign="top" width="400px">&nbsp;RM Supply Cost </td>
                  <td background="images/heading_bg.gif" class="pageName" style="background-repeat: repeat-x" valign="top" width="400px">
		 <table width="200" border="0" >
                  <tr>
                     <td>
			<table cellpadding="0" cellspacing="0" width="200" align="right">
                      	<tr> 
				<td class="listing-item"> From:</td>
                                <td nowrap="nowrap"> 
                            	<? 
					if($dateFrom=="") $dateFrom=date("d/m/Y");
				?>
                            	<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>"></td>
				<td class="listing-item">&nbsp;</td>
				<td class="listing-item"> Till:</td>
                                <td> 
                                 <? 
				    if($dateTill=="") $dateTill=date("d/m/Y");
				  ?>
                                  <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>"></td>
				   <td>&nbsp;</td>
			           <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search "></td>
                            	<td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td>
                          </tr>
                      </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplyCostRecordsSize;?>);" > <? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRMSupplyCost.php?fromDate=<?=$fromDate?>&tillDate=<?=$tillDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <?
		if($errDel!="")
		{
		?>
                <tr> 
                  <td colspan="3" height="15" align="center" class="err1"> 
                    <?=$errDel;?>                  </td>
                </tr>
                <?
			}
		?>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" style="padding-left:10px;padding-right:10px;">
		 <table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                <?
		if (sizeof($supplyCostRecords) > 0) {
			$i	=	0;
		?>
		<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="8" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RMSupplyCost.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RMSupplyCost.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RMSupplyCost.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div></td>
       </tr>
	   <? }?>
                      <tr  bgcolor="#f2f2f2" align="center"> 
                        <td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); "  class="chkBox"></td>
                        <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Wt Challan No </td>
                        <td class="listing-head" style="padding-left:10px; padding-right:10px;">Ice</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Transportation       </td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Commission</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Handling Charge</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total Amt</td>
						<? if($edit==true){?>
                        <td class="listing-head" width="50"></td>
						<? }?>
                      </tr>
                      <?
			$totalAmt = "";
			foreach ($supplyCostRecords as $scr) {
				$i++;
				$supplyCostId	=	$scr[0];
				$challanNumber	=	$scr[1];
				$dailyCatchMainEntryId = $scr[21];
				$totalIceCost	=	$scr[4];
				$fixedIceCost	=	$scr[5];
				$displyIceCost = "";
				if ($fixedIceCost!=0) {
					$displyIceCost  = $fixedIceCost;
				} else {
					$displyIceCost  = $totalIceCost;
				}

				$totalTransCost 	= $scr[8];
				$fixedTransCost		= $scr[9];
				$displyTransCost = "";
				if ($fixedTransCost!=0) {
					$displyTransCost  = $fixedTransCost;
				} else {
					$displyTransCost  = $totalTransCost;
				}	
			
				// Detailed Sum	Section
				$commissionTotalAmt = $scr[19];		
				$handlingTotalAmt   = $scr[20];

				$totalCommiRate		= $scr[12];
				$fixedCommiRate		= $scr[13];
				$displyCommiCost = "";
				if ($fixedCommiRate!=0) {
					$displyCommiCost  = $fixedCommiRate;
				} else if ($totalCommiRate!=0) {
					$displyCommiCost  = $totalCommiRate;
				} else if ($commissionTotalAmt!=0) {
					$displyCommiCost = $commissionTotalAmt;
				}

				$totalHandlingAmt = $scr[17];
				$fixedHandlingAmt = $scr[18];				
				$displayHandlingCost = "";
				if ($fixedHandlingAmt!=0) {
					$displayHandlingCost = $fixedHandlingAmt;
				} else if ($totalHandlingAmt!=0) {
					$displayHandlingCost = $totalHandlingAmt;
				} else if ($handlingTotalAmt!=0) {
					$displayHandlingCost = $handlingTotalAmt;
				}
				//$displayHandlingCost = ($fixedHandlingAmt!=0)?$fixedHandlingAmt:$totalHandlingAmt;

				$totalAmt = $displyIceCost + $displyTransCost + $displyCommiCost + $displayHandlingCost;			
				$paidStatus	=	$scr[14];
				$disabled = "";
				if (($paidStatus=='Y' && $reEdit==true) ||  $reEdit==false) {
					$disabled = "disabled";
				}
				$displayRMChallanNum =  $scr[22];
 			?>
                      <tr  bgcolor="WHITE" > 
                        <td width="20" height="25">
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplyCostId;?>" class="chkBox">
				<input type="hidden" name="dailyCatchMainEntryId_<?=$i?>" value="<?=$dailyCatchMainEntryId?>">
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayRMChallanNum;?></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displyIceCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displyTransCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displyCommiCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displayHandlingCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><b><? echo number_format($totalAmt,2,'.','');?></b></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center" width="40"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplyCostId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');this.form.action='RMSupplyCost.php';" <?=$disabled?>></td>
			<? }?>
                      </tr>
                      	<?
				}
			?>
                <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                <input type="hidden" name="editId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
<? if ($maxpage>1) { ?>
<tr bgcolor="#FFFFFF">
        <td colspan="8" style="padding-right:10px;">
	<div align="right">
	<?php 				 			  
	$nav  = '';
	for ($page=1; $page<=$maxpage; $page++) {
		if ($page==$pageNo) {
			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
		} else {
      			$nav.= " <a href=\"RMSupplyCost.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
		}
	}
	if ($pageNo > 1) {
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"RMSupplyCost.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 } else {
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
	}

	if ($pageNo < $maxpage) {
   		$page = $pageNo + 1;
   		$next = " <a href=\"RMSupplyCost.php?supplyFrom=$dateFrom&supplyTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	} else {
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
	}
	// print the navigation link
	$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
	echo $first . $prev . $nav . $next . $last . $summary; 
	?>
	  </div><input type="hidden" name="pageNo" value="<?=$pageNo?>"></td>
       	 	        </tr>
			<? }?>
                      <?
				} else {
			?>
                      <tr bgcolor="white"> 
                        <td colspan="8"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?></td>
                      </tr>
                      	<?
				}
			?>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" >
		<? if ($editMode==true) {?>
		<input type="hidden" name="selWtChallan" value="<?=$selWtChallan?>">
		<? }?>
		</td>
                </tr>
                <tr > 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplyCostRecordsSize;?>);" > <? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRMSupplyCost.php?fromDate=<?=$fromDate?>&tillDate=<?=$tillDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
              </table></td>
          </tr>
        </table>
        <!-- Form fields end   -->
      </td>
    </tr>
    <tr> 
      <td height="10"></td>
    </tr>
  </table>
<? if ($selCommiIndividual) {?>
<script>
	calcComisionIdividalRate();
</script>
<? }?>
<? if ($selHandlingIndividual) {?>
<script>
	calcHadlngDetailedRate();
</script>
<? }?>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
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
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
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
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
  </form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
