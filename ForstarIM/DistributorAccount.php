<?php
	require("include/include.php");
	require_once("lib/DistributorAccount_ajax.php");

	require_once 'components/base/CommonReason_model.php';
	require_once 'components/base/CommonReasonChkList_model.php';
	$comReason_m = new CommonReason_model();
	$crChkList_m = new CommonReasonChkList_model();
		
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$recUpdated	= false;
	$fieldDisabled  = "";
	$refInvoiceArr  = array();
	$balAdvAmt 	= "";
	
	$dateSelection = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"]."&distributorFilter=".$p["distributorFilter"]."&cityFilter=".$p["cityFilter"]."&invoiceFilter=".$p["invoiceFilter"]."&reasonFilter=".implode(',',$p["reasonFilter"])."&filterType=".$p["filterType"]."&recUpdated=1";

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

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
	/*-----------------------------------------------------------*/
	/*
		Payment received: credit
		Bank Charge: debit
		
		# Post Type
		PRBC	 - Payment Received Bank Charge
		CRBC	- Cheque Return Bank Charge
		CRPC	- Cheque Return Penalty Charge

		PAYMENT MODE => "CHQ"=>"Cheque", "CH"=>"Cash", "RT"=>"RTGS"
	*/

	# Get Default Common Reason
	//list($defaultCommonReasonId, $dcrCOD, $dcReasonName) = $distributorAccountObj->defaultCommonReason("SI");
	//echo "$defaultCommonReasonId, $dcrCOD, $dcReasonName";
		
	//foreach($p as $val =>$key) { echo "<br>$val = $key"; }
		
	# Reset Variables
	$refInvArrSize = "";
	if ($p["entryType"]!="") 	$entryType 	= $p["entryType"];
	if ($p["commonReason"]!="")	$commonReason	= $p["commonReason"];
	if ($p["selDate"]!="")		$selDate	= $p["selDate"];
	if ($p["selDistributor"]!="")	$selDistributorId	= $p["selDistributor"];
	if ($p["selCity"]!="")		$selCity	= $p["selCity"];
	if ($p["pendingCheque"]!="")	$selPendingChequeId = $p["pendingCheque"];
	$setReadOnlyField = "";
	//$setDisabledField = "";
	if ($selPendingChequeId) {
		$setReadOnlyField = "readonly";
		//$setDisabledField = "disabled";
	}

	$advAmtRestrictionEnabled = false;
	# Add New	
	if ($p["cmdAddNew"]!="") {
		$addMode = true;	
		$advAmtRestrictionEnabled = $manageconfirmObj->advAmtRestrictionEnabled();
	}

	if ($p["cmdCancel"]!="" || $p["hidDistributorACId"]!="") {
		$selDistACId = $sessObj->getValue("distributorAccountId");
		# Update Rec
		if ($selDistACId!=0) {
			$updateModifiedRec = $distributorAccountObj->updateDistACPModifiedRec($selDistACId, '', 'U');
			$sessObj->updateSession("distributorAccountId",0);
		}

		$addMode 	= false;
		$editMode 	= false;
		$commonReason = "";
		$p["commonReason"] = "";
		$selPendingChequeId = "";
		$p["pendingCheque"] = "";
		$defaultReasonType = "";
		$entryType 	= "";
		$editId		= "";
		$p["editId"]	= "";
		$editDistributorAccountId = "";
	}
		
	# bethel 24
	//$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($distributorId=24, $invoiceId=179);
		
	# Add
	if ($p["cmdAdd"]!="") {	
		$verified = "N";
		$selDate	= mysqlDateFormat($p["selDate"]);	
		$selDistributor	= $p["selDistributor"]; 
		$amount		= $p["amount"];
		//$debit	= ($p["debit"]!="")?$p["debit"]:"C";
		$amtDescription	= $p["amtDescription"];
	
		$entryType		= $p["entryType"];
		$paymentReceived	= $p["hidPaymentReceived"];
		
		$referenceInvoice	= $p["referenceInvoice"]; // Multiple		
		$debit			= ($entryType=="AD")?"D":"C";
		
		# $entryType = PR
		$paymentMode		= $p["paymentMode"];
		$chqRtgsNo		= $p["chqRtgsNo"];
		$chqDate		= mysqlDateFormat($p["chqDate"]);
		$bankName		= $p["bankName"];
		$accountNo		= $p["accountNo"];
		$branchLocation		= $p["branchLocation"];
		$depositedBankAccount	= $p["depositedBankAccount"];

		$valueDate = "";
		if ($p["valueDate"]!="") $valueDate = mysqlDateFormat($p["valueDate"]);

		$bankCharges		= $p["bankCharges"];
		$bankChargeDescription	= $p["bankChargeDescription"];

		# Others
		$commonReason		= $p["commonReason"];		
		$otherReason		= $p["otherReason"];
		if ($commonReason!="OT") $otherReason = "";

		# Description settings
		$pmtMode 	= $paymentModeArr[$paymentMode];
		$description = $pmtMode;
		if ($pmtMode!="CH") $description .= " No: $chqRtgsNo";
		
		$selCity		= $p["selCity"];
		$chequeReturnEntry 	= $p["hidChequeReturnEntry"];
		$chqReturnBankCharge	= trim($p["chqReturnBankCharge"]);
		$penaltyCharge		= trim($p["penaltyCharge"]);

		$pendingChequeId = "";
		if ($chequeReturnEntry=='CR') $pendingChequeId	= $p["pendingCheque"];

		$chkListRowCount	= $p["chkListRowCount"];
		if (($p["valueDate"]=="" || $p["valueDate"]=="0000-00-00") && $paymentReceived=="") $valueDate = $selDate;

		$pmtType		= $p["pmtType"]; // S-Single invoice/ M- Multiple Invoice/ A-Allocation
		$tblRowCount		= $p["hidTableRowCount"];
		$defaultReasonType	= $p["defaultReasonType"];
		$advPmtStatus = 'N';
		if ($defaultReasonType=='AP') $advPmtStatus = "Y";

		$distBankAccount	= $p["distBankAccount"];

		$balAdvAmt		= $p["balAdvAmt"];

		if ($balAdvAmt!=0) $pmtType='M';
		
		# For Single Invoice selection
		if ($tblRowCount>0 && $pmtType=='S') {
			$referenceInvoice = array();
			for ($i=0; $i<$tblRowCount; $i++) {
				$status = $p["status_".$i];
				if ($status!='N') $referenceInvoice[] = $p["refInv_".$i];					
			} // For loop ends here
		} // Single Inv Tble row ends here


		# PR = Payment Received as Credit Entry
		if (($entryType=="PR" || $paymentReceived!="" || $defaultReasonType=='AP') && $amount!="") {

			# Add Dist Ac
			$distributorAccountRecIns = $distributorAccountObj->addDistAccountRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $userId, $debit, $amount, $description, $selCity, $commonReason, $otherReason, $pmtType, $advPmtStatus, $distBankAccount);

			if ($distributorAccountRecIns) {
				# Dist Ac Id (Main Id)
				$distAccountId = $databaseConnect->getLastInsertedId();

				# bal as Advance Entry
				if ($balAdvAmt!=0) {
		
					list($apCommonReasonId, $apCrCOD, $apReasonName) = $distributorAccountObj->defaultCommonReason("AP");
		
					# $amount = $balAdvAmt;
					$addBalAdvAmtEntry = $distributorAccountObj->addDistAcBalAdvPmtRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $userId, $debit, $balAdvAmt, $description, $selCity, $apCommonReasonId, $otherReason, '', 'Y', $distBankAccount, $distAccountId);
				}

				# Multiple invoice section starts here
				if ($tblRowCount>0 && $pmtType=='M') {
					$referenceInvoice = array();
					for ($i=0; $i<$tblRowCount; $i++) {
						$status = $p["status_".$i];
						
						if ($status!='N') {
							$refInvId	= $p["refInv_".$i];
							if ($refInvId!="ADV") $referenceInvoice[] = $refInvId;
							$refAmt		= $p["refAmt_".$i];

							if ($refInvId!="" && $refInvId!="ADV" && $refAmt!=0 && $refAmt!="") {
								# Add Dist Ac
								$distAccountRecIns = $distributorAccountObj->addDistACRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, '', '', $userId, $debit, $refAmt, $description, $selCity, $commonReason, $otherReason, 'A', $distAccountId, 'N', $distBankAccount);
								$distAcId = "";
								if ($distAccountRecIns) {
									$distAcId = $databaseConnect->getLastInsertedId();
									if ($distAcId) $insertDistInvoice = $distributorAccountObj->insertDistAccountInvoice($distAcId, $refInvId);
								}
							}
						} // Status Ends here
					} // For loop ends here
				} // Tble row ends here
				# -----------------------------Multiple invoice section ENDS here

				# Distributor Bank charge
				if ($bankCharges!=0 && $bankChargeDescription!="") {
					# BC Ref inv 
					$bcRefInvArr = array();
					if ($tblRowCount>1) {
						for ($i=0; $i<$tblRowCount; $i++) {
							$status = $p["status_".$i];
							$bcApplicable = $p["bcApp_".$i];
							if ($status!='N' && $bcApplicable=='Y') {
								$bcRefInvArr[] = $p["refInv_".$i];					
							}
						} // For loop ends here
					}
					if (!sizeof($bcRefInvArr)) $bcRefInvArr = $referenceInvoice;

					$codType = "D";
					$chargesPostType = "PRBC";
					$insDistBankCharge = $distributorAccountObj->addDistACBankCharge($distAccountId, $selDate, $selDistributor, $codType, $bankCharges, $bankChargeDescription, $userId, $selCity, $commonReason, $chargesPostType, $valueDate, $verified);
					if ($insDistBankCharge) {
						$distBankACId = $databaseConnect->getLastInsertedId();
						$distACInvEntryId = "";
						foreach ($bcRefInvArr as $key=>$invoiceId) {
							if ($invoiceId!="") {
								$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distBankACId, $invoiceId);
								# Update debit Amt/Credit Amt
								$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
								if ($distACInvEntryId) {
									$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $bankCharges);
								}
							}
						} // For loop ends here
					}
				} // Bank charge ends here

				# Refer Inv
				if ($distAccountId!=0) {
					$distACInvEntryId = "";
					foreach ($referenceInvoice as $key=>$invoiceId) {
						if ($invoiceId!="") {
							$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distAccountId, $invoiceId);
	
							if ($valueDate!="") {
								# Update debit Amt/Credit Amt
								$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
								if ($distACInvEntryId) {
									$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $debit, $amount);
								}
							}
						}
					}
				}

				# Check List
				for($i=1; $i<=$chkListRowCount; $i++) {
					$chkListId  = $p["chkListId_".$i];
					if ($chkListId!="") {
						$insDistChkListRecs = $distributorAccountObj->insertDistChkList($distAccountId, $chkListId);
					}
				} // chk List loop ends here	
			}			
		} // PR Ends here
				
		# Other Types AD/AC , Debit or credit
		if ($selDate!="" && $selDistributor!="" && $amount!="" && $entryType!="PR" && $paymentReceived=="" && $defaultReasonType!='AP') {
			$distributorAccountRecIns = $distributorAccountObj->addCODDistAccount($selDate, $selDistributor, $amount, $debit, $amtDescription, $userId, '', '', $commonReason, $otherReason, $entryType, $selCity, $chqReturnBankCharge, $penaltyCharge, $pendingChequeId, $valueDate, $pmtType);
			if ($distributorAccountRecIns) {
				$distAccountId = $databaseConnect->getLastInsertedId();

				# Multiple invoice section starts here
				if ($tblRowCount>0 && $pmtType=='M') {
					$referenceInvoice = array();
					for ($i=0; $i<$tblRowCount; $i++) {
						$status = $p["status_".$i];
						
						if ($status!='N') {
							$refInvId	= $p["refInv_".$i];
							if ($refInvId!="ADV") $referenceInvoice[] = $refInvId;
							$refAmt		= $p["refAmt_".$i];

							if ($refInvId!="" && $refInvId!="ADV" && $refAmt!=0 && $refAmt!="") {
								# Add Dist Ac
								$distAccountRecIns = $distributorAccountObj->addDistACRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, '', '', $userId, $debit, $refAmt, $description, $selCity, $commonReason, $otherReason, 'A', $distAccountId, 'N', $distBankAccount);
								$distAcId = "";
								if ($distAccountRecIns) {
									$distAcId = $databaseConnect->getLastInsertedId();
									if ($distAcId) $insertDistInvoice = $distributorAccountObj->insertDistAccountInvoice($distAcId, $refInvId);
								}
							}
						} // Status Ends here
					} // For loop ends here
				} // Tble row ends here
				# -----------------------------Multiple invoice section ENDS here

				if ($distAccountId!=0) {
					$distACInvEntryId = "";
					foreach ($referenceInvoice as $key=>$invoiceId) {
						if ($invoiceId!="") {
							$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distAccountId, $invoiceId);
	
							# Update debit Amt/Credit Amt
							$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
							if ($distACInvEntryId) {
								$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $debit, $amount);
							}
						}
					}
				}
	
				# Check List
				for($i=1; $i<=$chkListRowCount; $i++) {
					$chkListId  = $p["chkListId_".$i];
					if ($chkListId!="") {
						$insDistChkListRecs = $distributorAccountObj->insertDistChkList($distAccountId, $chkListId);
					}
				} // chk List loop ends here

				# Cheque Return Entry
				if ($chequeReturnEntry=='CR') {
					# Update Cheque return status
					if ($pendingChequeId!="") {
						$updatePendingChequeRec = $distributorAccountObj->updatePendingCheque($pendingChequeId, $selDate);
					}
					# Distributor debit entry // 
					if ($chqReturnBankCharge!=0) {

						# BC Ref inv 
						$bcRefInvArr = array();
						if ($tblRowCount>1) {
							for ($i=0; $i<$tblRowCount; $i++) {
								$status = $p["status_".$i];
								$bcApplicable = $p["bcApp_".$i];
								if ($status!='N' && $bcApplicable=='Y') {
									$bcRefInvArr[] = $p["refInv_".$i];					
								}
							} // For loop ends here
						}
						if (!sizeof($bcRefInvArr)) $bcRefInvArr = $referenceInvoice;

						$codType = "D";
						$chqReturnDescription = "CHEQUE RETURN CHARGES";
						$chargesPostType = "CRBC";
						$insDistChqReturnBankCharge = $distributorAccountObj->addDistACBankCharge($distAccountId, $selDate, $selDistributor, $codType, $chqReturnBankCharge, $chqReturnDescription, $userId, $selCity, $commonReason, $chargesPostType, $valueDate, $verified);
						if ($insDistChqReturnBankCharge) {
							$distCRACId = $databaseConnect->getLastInsertedId();
							$distACInvEntryId = "";
							foreach ($bcRefInvArr as $key=>$invoiceId) {
								if ($invoiceId!="") {
									$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distCRACId, $invoiceId);
									# Update debit Amt/Credit Amt
									$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
									if ($distACInvEntryId) {
										$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $chqReturnBankCharge);
									}
								}
							}
						}
					} //Chq return bank charge ends here

					if ($penaltyCharge!=0) {

						# PC Ref inv 
						$pcRefInvArr = array();
						if ($tblRowCount>1) {
							for ($i=0; $i<$tblRowCount; $i++) {
								$status = $p["status_".$i];
								$pcApplicable = $p["pcApp_".$i];
								if ($status!='N' && $pcApplicable=='Y') {
									$pcRefInvArr[] = $p["refInv_".$i];					
								}
							} // For loop ends here
						}
						if (!sizeof($pcRefInvArr)) $pcRefInvArr = $referenceInvoice;

						$codType = "D";
						$penaltyChargeDescr = "CHEQUE RETURN PENALTY";
						$chargesPostType = "CRPC";
						$insDistChqReturnPenaltyCharge = $distributorAccountObj->addDistACBankCharge($distAccountId, $selDate, $selDistributor, $codType, $penaltyCharge, $penaltyChargeDescr, $userId, $selCity, $commonReason, $chargesPostType, $valueDate, $verified);
						if ($insDistChqReturnPenaltyCharge) {
							$distPenaltyACId = $databaseConnect->getLastInsertedId();
							
							$distACInvEntryId = "";	
							foreach ($pcRefInvArr as $key=>$invoiceId) {
								if ($invoiceId!="") {
									$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distPenaltyACId, $invoiceId);
	
									# Update debit Amt/Credit Amt
									$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
									if ($distACInvEntryId) {
										$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $penaltyCharge);
									}
								}
							}
						}
					} // Penalty charge ends here
				} // Chque Return ends here
			} // Dist Rec Ins
			/*
			# (Using in Sales Order and Claim)
			$distributorAccountRecIns = $distributorAccountObj->addDistributorAccount($selDate, $selDistributor, $amount, $debit, $amtDescription, $userId, '', '', '', '', '', '', '');
			*/			
		}

		if ($distributorAccountRecIns) {
			$addMode	= false;
			$sessObj->createSession("displayMsg",$msg_succAddDistributorAccount);
			$sessObj->createSession("nextPage",$url_afterAddDistributorAccount.$dateSelection);
		} else {
			$addMode	= true;
			$err		= $msg_failAddDistributorAccount; 
		}
		$distributorAccountRecIns		=	false;
	}
	
	
	# Edit a Record
	if (($p["editId"]!="" && $p["cmdCancel"]=="") || $selPendingChequeId) {
		$editId			= $p["editId"];
		if (!$selPendingChequeId) $editMode		= true;
		else $editId = $selPendingChequeId;
		if ($editMode) {
			# Chk already modified
			$selUsername = $distributorAccountObj->chkDistACRecModified($editId);	
			if ($selUsername && $g["editId"]=="") {
				$err	= "<b>$selUsername has been editing this record.</b>";	
				$editMode = false;
				$editId = "";
			}
		}

		$distributorAccountRec	= $distributorAccountObj->find($editId);
		$editDistributorAccountId	= $distributorAccountRec[0];

		if ($editMode) {
			$sessObj->createSession("distributorAccountId",$editDistributorAccountId);
			# Update Rec
			if ($editDistributorAccountId) $updateModifiedRec = $distributorAccountObj->updateDistACPModifiedRec($editDistributorAccountId, $userId, 'E');
		}

		if (!$selPendingChequeId) {
			if ($p["editSelectionChange"]=='1' || $p["selDate"]=="") $selDate = dateFormat($distributorAccountRec[1]);
			else $selDate = $p["selDate"];
			
		}
		if (!$selPendingChequeId) {
			if ($p["editSelectionChange"]=='1' || $p["selDistributor"]=="") $selDistributorId = $distributorAccountRec[2];
			else $selDistributorId = $p["selDistributor"];
		}

		$amount		= $distributorAccountRec[3];
		$selCoD		= $distributorAccountRec[4];
		if (!$selPendingChequeId) $debitChk	= ($selCoD=="D")?"Checked":"";
		$amtDescription	= $distributorAccountRec[5];
				
		if (!$selPendingChequeId) {
			if ($p["editSelectionChange"]=='1' || $p["entryType"]=="") $entryType = $distributorAccountRec[6];
			else $entryType = $p["entryType"];	


			if ($p["editSelectionChange"]=='1' || $p["commonReason"]=="") $commonReason = $distributorAccountRec[17];
			else $commonReason = $p["commonReason"];
			$otherReason		= $distributorAccountRec[18];				

			if ($p["editSelectionChange"]=='1' || $p["selCity"]=="") $selCity = $distributorAccountRec[19];
			else $selCity = $p["selCity"];
		}

		$paymentMode		= $distributorAccountRec[7];
		$chqRtgsNo		= $distributorAccountRec[8];
		$chqDate		= ($distributorAccountRec[9]!="0000-00-00")?dateFormat($distributorAccountRec[9]):"";
		$bankName		= $distributorAccountRec[10];
		$accountNo		= $distributorAccountRec[11];
		$branchLocation		= $distributorAccountRec[12];		
		$valueDate		= ($distributorAccountRec[14]!='0000-00-00')?dateFormat($distributorAccountRec[14]):"";
		$bankCharges		= $distributorAccountRec[15];
		$bankChargeDescription	= $distributorAccountRec[16];
		
		
		# Get selected reference invoice
		$refInvoiceArr		= $distributorAccountObj->getSelReferenceInvoice($editDistributorAccountId); 
		$refInvArrSize 		= sizeof($refInvoiceArr);
		
		# get Selected ref chk list
		$chkListArr		= $distributorAccountObj->getSelChkListRecs($editDistributorAccountId);	

		# Cheque Return
		$chqReturnBankCharge	= $distributorAccountRec[20];
		$penaltyCharge		= $distributorAccountRec[21];

		$chqReturnStatus	= $distributorAccountRec[22];
		$readonly = "";
		if ($chqReturnStatus=='Y') $readonly = "readonly";
		$chqReturnDistAcId	= $distributorAccountRec[23];

		$pmtType		= $distributorAccountRec[24];
		if ($refInvArrSize==1) $pmtType = "S";
		/*
		if ($pmtType=='S') {
			$refInvoiceId = $distributorAccountObj->distACSingleRefInv($editDistributorAccountId);
			list($singleInvAmt, $singleInvDespatchDate) = $distributorAccountObj->getInvValue($refInvoiceId);
			if ($singleInvDespatchDate!="" || $singleInvDespatchDate!='0000-00-00') $singleInvDespatchDate = dateFormat($singleInvDespatchDate);
		}
		*/

		if (!$selPendingChequeId) $fieldDisabled = "disabled";

		//$depositedBankAccount	= $distributorAccountRec[13];
		$depositedBankAccount	= $distributorAccountRec[29];
		$distBankAccount	= $distributorAccountRec[30];	

		# Check Advance entry exist
		$advanceEntryExist = $distributorAccountObj->chkBalAdvPmtEntryExist($editDistributorAccountId);	
		if ($advanceEntryExist) {
			$advanceEntryConfirmed = $distributorAccountObj->chkBalAdvPmtEntryConfirmed($editDistributorAccountId);	
		}
	}
	# Edit section ends here
	
	# Update
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveAndConfirm"]!="") {	
	
		$verified = "N";	
		if ($p["cmdSaveAndConfirm"]!="") $verified = "Y";

		$distributorAccountId	= $p["hidDistributorAccountId"];
		
		$selDate	= mysqlDateFormat($p["selDate"]);	
		$selDistributor	= $p["selDistributor"]; 
		$amount		= $p["amount"];
		//$debit		= ($p["debit"]!="")?$p["debit"]:"C";
		$amtDescription	= $p["amtDescription"];
		$hidAmount	= $p["hidAmount"];
		$selCoD		= $p["selCoD"];	

		$entryType		= $p["entryType"];
		$referenceInvoice	= $p["referenceInvoice"]; // Multiple		
		$debit		= ($entryType=="AD")?"D":"C";
		$paymentReceived	= $p["hidPaymentReceived"];		

		# $entryType = PR
		$paymentMode		= $p["paymentMode"];
		$chqRtgsNo		= $p["chqRtgsNo"];
		$chqDate		= mysqlDateFormat($p["chqDate"]);
		$bankName		= $p["bankName"];
		$accountNo		= $p["accountNo"];
		$branchLocation		= $p["branchLocation"];
		$depositedBankAccount	= $p["depositedBankAccount"];
		$valueDate = "";
		if ($p["valueDate"]!="") $valueDate	= mysqlDateFormat($p["valueDate"]);
		$bankCharges		= $p["bankCharges"];
		$hidBankCharges		= $p["hidBankCharges"];
		$bankChargeDescription	= $p["bankChargeDescription"];

		# Others
		$commonReason		= $p["commonReason"];		
		$otherReason		= $p["otherReason"];
		if ($commonReason!="OT") $otherReason = "";

		# Description settings
		$pmtMode 	= $paymentModeArr[$paymentMode];
		$description = $pmtMode;
		if ($pmtMode!="CH") $description .= " No: $chqRtgsNo";
		
		$chkListRowCount	= $p["chkListRowCount"];
		$selCity		= $p["selCity"];

		$chequeReturnEntry 	= $p["hidChequeReturnEntry"];
		$chqReturnBankCharge	= trim($p["chqReturnBankCharge"]);
		$penaltyCharge		= trim($p["penaltyCharge"]);
		$hidChqReturnBankCharge	= trim($p["hidChqReturnBankCharge"]);
		$hidPenaltyCharge	= trim($p["hidPenaltyCharge"]);
		$chqReturnDistAcId	= $p["chqReturnDistAcId"];
		
		if (($p["valueDate"]=="" || $p["valueDate"]=="0000-00-00") && $paymentReceived=="") $valueDate = $selDate;

		$pmtType		= $p["pmtType"]; // S-Single invoice/ M- Multiple Invoice
		$tblRowCount		= $p["hidTableRowCount"];
		$defaultReasonType	= $p["defaultReasonType"];
		$totPmtVal		= $p["totPmtVal"];
		$distBankAccount	= $p["distBankAccount"];
		//$advanceEntryExist	= $p["advanceEntryExist"];
		//if ($advanceEntryExist!="") $pmtType = "M";

		$balAdvAmt		= $p["balAdvAmt"];
		if ($balAdvAmt!=0) $pmtType='M';

		# Convert Advance Payment to Payment Received
		if ($defaultReasonType=='AP' && (!in_array("",$referenceInvoice) || $totPmtVal!=0)) {
			list($defaultCommonReasonId, $dcrCOD, $dcReasonName) = $distributorAccountObj->defaultCommonReason("PR");
			$commonReason = $defaultCommonReasonId;
		}

		if ($entryType!="") {
			# Delete Reference Invoice
			$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($distributorAccountId);
	
			# Delete Sel chk list
			$deleteSelChkList = $distributorAccountObj->delChkList($distributorAccountId);

			# Delete Ref Inv Advance Amt
			$deleteRefInvAdvAmt = $distributorAccountObj->delRefInvAdvAmt($distributorAccountId);
		}

		# For Single Invoice selection
		if ($tblRowCount>0 && $pmtType=='S') {
			$referenceInvoice = array();
			for ($i=0; $i<$tblRowCount; $i++) {
				$status = $p["status_".$i];
				if ($status!='N') $referenceInvoice[] = $p["refInv_".$i];					
			} // For loop ends here
		} // Single Inv Tble row ends here

		# PR = Payment Received
		if ($entryType=="PR" || $paymentReceived!="" || $defaultReasonType=='AP') {

			$distributorAccountRecUptd = $distributorAccountObj->updateDistAccountRecs($distributorAccountId, $entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $commonReason, $otherReason, $verified, $debit, $amount, $description, $selCity, $commonReason, $otherReason, $pmtType, $distBankAccount);

			if ($distributorAccountRecUptd) {

				# bal as Advance Entry
				if ($balAdvAmt!=0) {
		
					list($apCommonReasonId, $apCrCOD, $apReasonName) = $distributorAccountObj->defaultCommonReason("AP");
		
					# $amount = $balAdvAmt;
					$addBalAdvAmtEntry = $distributorAccountObj->addDistAcBalAdvPmtRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, $bankCharges, $bankChargeDescription, $userId, $debit, $balAdvAmt, $description, $selCity, $apCommonReasonId, $otherReason, '', 'Y', $distBankAccount, $distributorAccountId);
				}

				# Get Sub entry recs
				$subEntryRecs = $distributorAccountObj->getSubEntryRecs($distributorAccountId);
				if (sizeof($subEntryRecs)>0) {
					foreach ($subEntryRecs as $crc) {
						$selDistAcId	= $crc[0];
						$selDCCOD	= $crc[1];
						$selDisACAmt	= $crc[2];

						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selDCCOD, $selDisACAmt);

						$refInvRecs = $distributorAccountObj->getRefInvoices($selDistAcId);
						$distACInvEntryId = "";
						foreach ($refInvRecs as $rir) {
							$invoiceId = $rir[2];
							# Update debit Amt/Credit Amt
							$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
							if ($distACInvEntryId) {
								$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $selDCCOD, $selDisACAmt, "Y");
							}
						}

						# Delete Reference Invoice
						$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($selDistAcId);
			
						# Delete Sel chk list
						$deleteSelChkList = $distributorAccountObj->delChkList($selDistAcId);
			
						# Delete From main Table
						$distAcRecDel   = $distributorAccountObj->deleteDistributorAccount($selDistAcId);		
					}
				} // Sub entry Del ends here

				# Multiple invoice section starts here
				if ($tblRowCount>0 && $pmtType=='M') {
					$referenceInvoice = array();
					for ($i=0; $i<$tblRowCount; $i++) {
						$status 	= $p["status_".$i];
						$refInvEntryId	= $p["refInvEntryId_".$i];
						$distAccountRecIns = "";
						if ($status!='N') {
							$refInvId	= $p["refInv_".$i];
							if ($refInvId!="ADV") $referenceInvoice[] = $refInvId;
							$refAmt		= $p["refAmt_".$i];

							if ($refInvId!="" && $refInvId!="ADV" && $refAmt!=0 && $refAmt!="") {
								# Add Dist Ac
								$distAccountRecIns = $distributorAccountObj->addDistACRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, '', '', $userId, $debit, $refAmt, $description, $selCity, $commonReason, $otherReason, 'A', $distributorAccountId, $verified, $distBankAccount);
								$distAcId = "";
								if ($distAccountRecIns) {								
									$distAcId = $databaseConnect->getLastInsertedId();					
									if ($distAcId) $insertDistInvoice = $distributorAccountObj->insertDistAccountInvoice($distAcId, $refInvId);
								}
							}
						} // Status Ends here
					} // For loop ends here
				} // Tble row ends here
				# -----------------------------Multiple invoice section ENDS here
						
				

				# update Distributor Account section
				if ($amount!=$hidAmount ||  $debit!=$selCoD ) {
					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $hidAmount);
									
					# Update Dist Rec
					$manageDistAccount = $distributorAccountObj->manageDistributorAccount($selDistributor, $debit, $amount);
				}

				# Distributor Bank charge
				# BC Ref inv 
						$bcRefInvArr = array();
						if ($tblRowCount>1) {
							for ($i=0; $i<$tblRowCount; $i++) {
								$status = $p["status_".$i];
								$bcApplicable = $p["bcApp_".$i];
								if ($status!='N' && $bcApplicable=='Y') {
									$bcRefInvArr[] = $p["refInv_".$i];					
								}
							} // For loop ends here
						}
						if (!sizeof($bcRefInvArr)) $bcRefInvArr = $referenceInvoice;
				# Get Bank charge ac Id
				list($bankChargeRecId, $selcod, $selBankCharge) = $distributorAccountObj->getBankChargeRec($distributorAccountId);
				$codType = "D";
				$chargesPostType = "PRBC";
				if ($bankCharges!=0 && $bankChargeDescription!="" && $bankChargeRecId=="") {					

					$insDistBankCharge = $distributorAccountObj->addDistACBankCharge($distributorAccountId, $selDate, $selDistributor, $codType, $bankCharges, $bankChargeDescription, $userId, $selCity, $commonReason, $chargesPostType, $valueDate, $verified);
					if ($insDistBankCharge) {
						$distBankACId = $databaseConnect->getLastInsertedId();
						$distACInvEntryId = "";
						foreach ($bcRefInvArr as $key=>$invoiceId) {
							if ($invoiceId!="") {
								$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distBankACId, $invoiceId);

								# Update debit Amt/Credit Amt
								$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
								if ($distACInvEntryId) {
									$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $bankCharges);
								}
							}
						}
					}
				} else if ($bankCharges!=0 && $bankChargeDescription!="" && $bankChargeRecId!="") {

					$updateDistBankCharge = $distributorAccountObj->updateDistACBankCharge($bankChargeRecId, $bankCharges, $bankChargeDescription, $chargesPostType, $valueDate, $verified);

					# update Distributor Account section
					if ($bankCharges!=$hidBankCharges) {
						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $codType, $hidBankCharges);
										
						# Update Dist Rec
						$manageDistAccount = $distributorAccountObj->manageDistributorAccount($selDistributor, $codType, $bankCharges);
					}

					if ($updateDistBankCharge) {
						foreach ($bcRefInvArr as $key=>$invoiceId) {
							# Update debit Amt/Credit Amt
							$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
							if ($distACInvEntryId) {
								//$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $hidBankCharges, "Y");
								$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $bankCharges);
							}
						}
					}
					
				} else if ($bankCharges==0 && $bankChargeRecId!="") {
					$delBankChargeRec = $distributorAccountObj->delBankCharge($bankChargeRecId);
					if ($delBankChargeRec) {
						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selcod, $selBankCharge);	
						/*
						foreach ($referenceInvoice as $key=>$invoiceId) {
							# Update debit Amt/Credit Amt
							$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
							if ($distACInvEntryId) {
								$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $selcod, $selBankCharge, "Y");								
							}
						}
						*/
					}
				}

				# Confirm the bank charge rec
				if ($bankChargeRecId!="") {
					$uptdConfirm = $distributorAccountObj->uptdBankChargeConfirm($bankChargeRecId, $verified);	
				}

				if (sizeof($referenceInvoice)>0 && $distributorAccountId) {
					# Insert Ref Invoice
					foreach ($referenceInvoice as $key=>$invoiceId) {
						if ($invoiceId!="") {
							$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distributorAccountId, $invoiceId);
							
							if ($valueDate!="") {
								# Update debit Amt/Credit Amt
								$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
								if ($distACInvEntryId) {
									//$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $selCoD, $hidAmount, "Y");
									$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $debit, $amount);
								}
							}
						}
					}
				} // Ref chk ends here	

				# Check List
				for($i=1; $i<=$chkListRowCount; $i++) {
					$chkListId  = $p["chkListId_".$i];
					if ($chkListId!="") {
						$insDistChkListRecs = $distributorAccountObj->insertDistChkList($distributorAccountId, $chkListId);
					}
				} // chk List loop ends here
			}			
		} // PR Ends here
		
		# Other Types AD/AC ie Debit or credit
		if ($selDate!="" && $selDistributor!="" && $amount!="" && $entryType!="PR" && $paymentReceived=="" && $defaultReasonType!='AP') {
			
			$distributorAccountRecUptd = $distributorAccountObj->updateCODDistAccount($distributorAccountId, $selDate, $selDistributor, $amount, $debit, $amtDescription, $commonReason, $otherReason, $entryType, $verified, $selCity, $chqReturnBankCharge, $penaltyCharge, $valueDate, $pmtType);

			if ($distributorAccountRecUptd) {
				# Get Sub entry recs
				$subEntryRecs = $distributorAccountObj->getSubEntryRecs($distributorAccountId);
				if (sizeof($subEntryRecs)>0) {
					foreach ($subEntryRecs as $crc) {
						$selDistAcId	= $crc[0];
						$selDCCOD	= $crc[1];
						$selDisACAmt	= $crc[2];

						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selDCCOD, $selDisACAmt);

						$refInvRecs = $distributorAccountObj->getRefInvoices($selDistAcId);
						$distACInvEntryId = "";
						foreach ($refInvRecs as $rir) {
							$invoiceId = $rir[2];
							# Update debit Amt/Credit Amt
							$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
							if ($distACInvEntryId) {
								$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $selDCCOD, $selDisACAmt, "Y");
							}
						}

						# Delete Reference Invoice
						$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($selDistAcId);
			
						# Delete Sel chk list
						$deleteSelChkList = $distributorAccountObj->delChkList($selDistAcId);
			
						# Delete From main Table
						$distAcRecDel   = $distributorAccountObj->deleteDistributorAccount($selDistAcId);		
					}
				} // Sub entry Del ends here

				# Multiple invoice section starts here
				if ($tblRowCount>0 && $pmtType=='M') {
					$referenceInvoice = array();
					for ($i=0; $i<$tblRowCount; $i++) {
						$status 	= $p["status_".$i];
						$refInvEntryId	= $p["refInvEntryId_".$i];
						$distAccountRecIns = "";
						if ($status!='N') {
							$refInvId	= $p["refInv_".$i];
							if ($refInvId!="ADV") $referenceInvoice[] = $refInvId;
							$refAmt		= $p["refAmt_".$i];

							if ($refInvId!="" && $refInvId!="ADV" && $refAmt!=0 && $refAmt!="") {
								# Add Dist Ac
								$distAccountRecIns = $distributorAccountObj->addDistACRecs($entryType, $selDate, $selDistributor, $paymentMode, $chqRtgsNo, $chqDate, $bankName, $accountNo, $branchLocation, $depositedBankAccount, $valueDate, '', '', $userId, $debit, $refAmt, $description, $selCity, $commonReason, $otherReason, 'A', $distributorAccountId, $verified, $distBankAccount);
								$distAcId = "";
								if ($distAccountRecIns) {								
									$distAcId = $databaseConnect->getLastInsertedId();					
									if ($distAcId) $insertDistInvoice = $distributorAccountObj->insertDistAccountInvoice($distAcId, $refInvId);
								}
							}
						} // Status Ends here
					} // For loop ends here
				} // Tble row ends here
				# -----------------------------Multiple invoice section ENDS here

				if (sizeof($referenceInvoice)>0 && $distributorAccountId) {
					foreach ($referenceInvoice as $key=>$invoiceId) {
						if ($invoiceId!="") {
							$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distributorAccountId, $invoiceId);
	
							# Update debit Amt/Credit Amt
							$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
							if ($distACInvEntryId) {
								//$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $selCoD, $hidAmount, "Y");
								$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $debit, $amount);
							}
						}
					}
				}
	
				# Check List
				for($i=1; $i<=$chkListRowCount; $i++) {
					$chkListId  = $p["chkListId_".$i];
					if ($chkListId!="") {
						$insDistChkListRecs = $distributorAccountObj->insertDistChkList($distributorAccountId, $chkListId);
					}
				} // chk List loop ends here

				# update Distributor Account section
				if ($amount!=$hidAmount ||  $debit!=$selCoD ) {
					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $hidAmount);
									
					# Update Dist Rec
					$manageDistAccount = $distributorAccountObj->manageDistributorAccount($selDistributor, $debit, $amount);
				}

				# Cheque Return Entry
				if ($chequeReturnEntry=='CR') {					
					if ($chqReturnDistAcId!="") {
						$updatePendingChequeRec = $distributorAccountObj->updatePendingCheque($chqReturnDistAcId, $selDate);
					}
					/*
					# Get cheque return charges recs
					$subEntryRecs = $distributorAccountObj->getSubEntryRecs($distributorAccountId);
					if (sizeof($subEntryRecs)>0) {
						foreach ($subEntryRecs as $crc) {
							$selDistAcId	= $crc[0];
							# Delete Reference Invoice
							$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($selDistAcId);
			
							# Delete Sel chk list
							$deleteSelChkList = $distributorAccountObj->delChkList($selDistAcId);
			
							# Delete From main Table
							$distAcRecDel   = $distributorAccountObj->deleteDistributorAccount($selDistAcId);
						}
					}
					*/
					# Distributor debit entry // 
					if ($chqReturnBankCharge!=0) {
						# BC Ref inv 
						$bcRefInvArr = array();
						if ($tblRowCount>1) {
							for ($i=0; $i<$tblRowCount; $i++) {
								$status = $p["status_".$i];
								$bcApplicable = $p["bcApp_".$i];
								if ($status!='N' && $bcApplicable=='Y') {
									$bcRefInvArr[] = $p["refInv_".$i];					
								}
							} // For loop ends here
						}
						if (!sizeof($bcRefInvArr)) $bcRefInvArr = $referenceInvoice;

						$codType = "D";
						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $codType, $hidChqReturnBankCharge);

						$chqReturnDescription = "CHEQUE RETURN CHARGES";
						$chargesPostType = "CRBC";
						$insDistChqReturnBankCharge = $distributorAccountObj->addDistACBankCharge($distributorAccountId, $selDate, $selDistributor, $codType, $chqReturnBankCharge, $chqReturnDescription, $userId, $selCity, $commonReason, $chargesPostType, $valueDate, $verified);
						if ($insDistChqReturnBankCharge) {
							$distCRACId = $databaseConnect->getLastInsertedId();
	
							foreach ($bcRefInvArr as $key=>$invoiceId) {
								if ($invoiceId!="") {
									$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distCRACId, $invoiceId);
	
									# Update debit Amt/Credit Amt
									$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
									if ($distACInvEntryId) {
										//$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $hidChqReturnBankCharge, "Y");
										$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $chqReturnBankCharge);
									}
								}
							}
							
							if ($chqReturnBankCharge!=$hidChqReturnBankCharge) {
								# Update Dist Rec
								$manageDistAccount = $distributorAccountObj->manageDistributorAccount($selDistributor, $codType, $chqReturnBankCharge);
							}
							# Update confirm
							$uptdConfirm = $distributorAccountObj->uptdBankChargeConfirm($distCRACId, $verified);
						}
					} // chq return bank charge ends here

					if ($penaltyCharge!=0) {

						# PC Ref inv 
						$pcRefInvArr = array();
						if ($tblRowCount>1) {
							for ($i=0; $i<$tblRowCount; $i++) {
								$status = $p["status_".$i];
								$pcApplicable = $p["pcApp_".$i];
								if ($status!='N' && $pcApplicable=='Y') {
									$pcRefInvArr[] = $p["refInv_".$i];					
								}
							} // For loop ends here
						}
						if (!sizeof($pcRefInvArr)) $pcRefInvArr = $referenceInvoice;

						$codType = "D";
						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $codType, $hidPenaltyCharge);

						$penaltyChargeDescr = "CHEQUE RETURN PENALTY";
						$chargesPostType = "CRPC";
						$insDistChqReturnPenaltyCharge = $distributorAccountObj->addDistACBankCharge($distributorAccountId, $selDate, $selDistributor, $codType, $penaltyCharge, $penaltyChargeDescr, $userId, $selCity, $commonReason, $chargesPostType, $valueDate, $verified);
						if ($insDistChqReturnPenaltyCharge) {
							$distPenaltyACId = $databaseConnect->getLastInsertedId();
	
							foreach ($pcRefInvArr as $key=>$invoiceId) {
								if ($invoiceId!="") {
									$insertDistributorInvoice = $distributorAccountObj->insertDistAccountInvoice($distPenaltyACId, $invoiceId);
									# Update debit Amt/Credit Amt
									$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
									if ($distACInvEntryId) {
										//$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $hidPenaltyCharge, "Y");
										$updateCrORDebitAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $codType, $penaltyCharge);
									}
								}
							}

							if ($penaltyCharge!=$hidPenaltyCharge) {
								# Update Dist Rec
								$manageDistAccount = $distributorAccountObj->manageDistributorAccount($selDistributor, $codType, $penaltyCharge);
							}
							# Update confirm
							$uptdConfirm = $distributorAccountObj->uptdBankChargeConfirm($distPenaltyACId, $verified);
						}
					} // Penalty charge ends here
				} // Chque Return ends here
			} // Dist Rec Uptd ends here
		}
		
		/*
		if ($distributorAccountId!="" && $selDate!="" && $selDistributor!="") {		
			$distributorAccountRecUptd = $distributorAccountObj->updateDistributorAccount($distributorAccountId, $selDate, $selDistributor, $amount, $debit, $amtDescription);	

			if ($amount!=$hidAmount ||  $debit!=$selCoD ) {				
				# Rollback Old Rec
				$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $hidAmount);
								
				# Update Dist Rec
				$manageDistAccount = $distributorAccountObj->manageDistributorAccount($selDistributor, $debit, $amount);		
			}
		}
		*/
		//printr($referenceInvoice);
		if ($distributorAccountRecUptd) {
			$editMode	= false;
			$defaultReasonType = "";
			$entryType = "";
			$sessObj->createSession("displayMsg",$msg_succDistributorAccountUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDistributorAccount.$dateSelection);
		} else {
			$editMode	=	true;
			$err	= $msg_failDistributorAccountUpdate; 
		}
		$distributorAccountRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		$recVerified = false;
		for ($i=1; $i<=$rowCount; $i++) {

			$distributorAccountId 	= $p["delId_".$i];
			$verified		= $p["verified_".$i];
			$chqReturn		= $p["chqReturn_".$i];
			$chqReturnEntryId	= $p["chqReturnEntryId_".$i];
			
			if ($distributorAccountId!="" && $verified=="N" && $chqReturn=="N") {
				# Get the Deleting dist account Rec
				list($selDistributor, $billAmount, $selCoD, $salesOrderId, $claimId) = $distributorAccountObj->getDistributorAccountRec($distributorAccountId);
				
				$refInvRecs = $distributorAccountObj->getRefInvoices($distributorAccountId);
				foreach ($refInvRecs as $rir) {
					$invoiceId = $rir[2];
					# Update debit Amt/Credit Amt
					$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
					if ($distACInvEntryId) {
						$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $selCoD, $billAmount, "Y");
					}
				}

				# Parent Id
				/*
				$parentId = $distributorAccountObj->getParentId($distributorAccountId);
				if ($parentId!=0) {
					$updateBankCharge = $distributorAccountObj->unsetBankCharge($parentId);
				} 
				*/

				# Get Sub entry recs
				$subEntryRecs = $distributorAccountObj->getSubEntryRecs($distributorAccountId);
				if (sizeof($subEntryRecs)>0) {
					foreach ($subEntryRecs as $crc) {
						$selDistAcId	= $crc[0];
						$selDCCOD	= $crc[1];
						$selDisACAmt	= $crc[2];

						# Rollback Old Rec
						$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selDCCOD, $selDisACAmt);

						$refInvRecs = $distributorAccountObj->getRefInvoices($selDistAcId);
						$distACInvEntryId = "";
						foreach ($refInvRecs as $rir) {
							$invoiceId = $rir[2];
							# Update debit Amt/Credit Amt
							$distACInvEntryId = $distributorAccountObj->getDACInvoiceMainEntry($selDistributor, $invoiceId);
							if ($distACInvEntryId) {
								$rollbackAmt = $distributorAccountObj->updateInvDNCAmt($distACInvEntryId, $selDCCOD, $selDisACAmt, "Y");
							}
						}

						# Delete Reference Invoice
						$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($selDistAcId);
			
						# Delete Sel chk list
						$deleteSelChkList = $distributorAccountObj->delChkList($selDistAcId);
			
						# Delete From main Table
						$distAcRecDel   = $distributorAccountObj->deleteDistributorAccount($selDistAcId);		
					}
				}

				# Update Distributor Account	
				if ($selDistributor!="" && $billAmount!="") {	
					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
					# Update Sales Order Rec	
					if ($salesOrderId!=0) {
						$updateSalesOrderPaymentStatus = $distributorAccountObj->updateSOPaymentStatus($salesOrderId);
					}	
					if ($claimId!=0) {
						$updateClaimPaymentStatus = $distributorAccountObj->updateClaimPaymentStatus($claimId);
					}
				}
				/* Need t o check it is linked with any other process */
				# Delete Reference Invoice
				$delRefInvoice = $distributorAccountObj->delRefInvoiceRecs($distributorAccountId);

				# Delete Sel chk list
				$deleteSelChkList = $distributorAccountObj->delChkList($distributorAccountId);
				# Delete Ref Inv Advance Amt
				$deleteRefInvAdvAmt = $distributorAccountObj->delRefInvAdvAmt($distributorAccountId);

				# Delete From main Table
				$distributorAccountRecDel   = $distributorAccountObj->deleteDistributorAccount($distributorAccountId);
				if ($distributorAccountRecDel && $chqReturnEntryId!=0) {
					$updateParentEntry = $distributorAccountObj->updateParentDistACEntry($chqReturnEntryId);
				}	
			} else if ($distributorAccountId!="" && $verified=="Y") {
				$recVerified = true;
			} 
		}
		if ($distributorAccountRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDistributorAccount);
			$sessObj->createSession("nextPage",$url_afterDelDistributorAccount.$dateSelection);
		} else {
			if ($recVerified) $errDel = $msg_failDelDistributorAccount."<br>User Can't delete verified entry.";
			else $errDel	=	$msg_failDelDistributorAccount;
			$addMode = false;
			$editMode = false;
		}
		$distributorAccountRecDel	=	false;
	}

	if ($g["distributorFilter"]!="") $distributorFilterId = $g["distributorFilter"];
	else $distributorFilterId = $p["distributorFilter"];
	if ($selDistributorId=="" && $distributorFilterId!="") $selDistributorId = $distributorFilterId;

	if ($g["cityFilter"]!="") $cityFilterId = $g["cityFilter"];
	else $cityFilterId = $p["cityFilter"];

	if ($g["invoiceFilter"]!="") $invoiceFilterId = $g["invoiceFilter"];
	else $invoiceFilterId = $p["invoiceFilter"];

	# Reason filter
	if ($g["reasonFilter"]!="") $reasonFilterArr = explode(",",$g["reasonFilter"]);
	else $reasonFilterArr = $p["reasonFilter"];
	$reasonFilterIds = "";
	if (sizeof($reasonFilterArr)>0) {
		$reasonFilterIds = implode(",",$reasonFilterArr);
	}	

	if ($g["filterType"]!="") $filterType = $g["filterType"];
	else $filterType = $p["filterType"];

	if ($g["recUpdated"]!="") $recUpdated = true;
	
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
		
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------		

	# Resettting offset values
	if ($p["hidDistributorFilterId"]!=$p["distributorFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		# List default for the current financial year [1st April (Y-1) to March 31st Y]
		# but display up to current date
		//$dateFrom = date("d/m/Y", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
		$dateFrom = financialYear();
		$dateTill = date("d/m/Y");
	}

	
	#List all Recs
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		if ($p["cmdSearch"]) {
			$offset = 0;
			$page 	= 0;
		}
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		#List all Records (If Modified this, then 3 query (fetchAllPagingRecords,fetchDateRangeRecords,getCODGrandTotalAmt) must modify)
		$distributorAccountRecords = $distributorAccountObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $distributorFilterId, $cityFilterId, $invoiceFilterId, $reasonFilterIds, $filterType);
		$distributorAccountRecSize = sizeof($distributorAccountRecords);

		# Get All Recs
		$fetchAllDistributorAccountRecs = $distributorAccountObj->fetchDateRangeRecords($fromDate, $tillDate, $distributorFilterId, $cityFilterId, $invoiceFilterId, $reasonFilterIds, $filterType);
		
		if ($distributorFilterId && !$invoiceFilterId) {
			list($openingBalanceAmt, $postType) = $distributorReportObj->getOpeningBalanceAmt($fromDate, $tillDate, $distributorFilterId, $cityFilterId);
			
			# Get Dist Master Rec ------
			list($creditLimit, $creditPeriod, $totOutStandAmt, $creditPeriodFrom) = $salesOrderObj->getDistMasterRec($distributorFilterId);
			//$creditPeriodOutStandAmt = $salesOrderObj->getCreditPeriodOutStandAmount($distributorFilterId, $creditPeriod);

			$distOutStandAmt = $distributorAccountObj->getOutStandingAmt($fromDate, $tillDate, $distributorFilterId);
			
			#if credit bal then add to credit limit/ if Debit bal, then subtract from credit limit			
			$creditBalance = ($distOutStandAmt<0)?($creditLimit+abs($distOutStandAmt)):($creditLimit-abs($distOutStandAmt));

			# ------------------------- 
		}	
	
		# Chk Ref Inv Assigned
		$refInvNotExist = $distributorAccountObj->chkRefInvAssignStatus($fromDate, $tillDate, $distributorFilterId, $cityFilterId);

		# Get Filtered City List
		$distCityFilterList = $distributorAccountObj->getCityFilterList($fromDate, $tillDate, $distributorFilterId);
		$distCityFilterRecSize = sizeof($distCityFilterList);

		if ($distributorFilterId) {
			$filter = true;			
			# Get Filterd Invoice list
			 $invoiceFilterList = $distributorAccountObj->getInvoiceFilteredList($fromDate, $tillDate, $distributorFilterId, $cityFilterId);
		} 		

		# Get Dist Filter Recs
		$distributorFilterRecs	= $distributorAccountObj->getDistributorList($fromDate, $tillDate);

		# Common Reson
		//$commonReasonFilterRecs = $comReason_m->findAll(array("order"=>"default_entry desc, reason asc"));	

		# Grand Total AC Amt
		list($grandTotalDebitAmt, $grandTotalCreditAmt) = $distributorAccountObj->getCODGrandTotalAmt($fromDate, $tillDate, $distributorFilterId, $cityFilterId, $invoiceFilterId, $reasonFilterIds, $filterType);		
		if ($postType=="C")  $grandTotalCreditAmt += abs($openingBalanceAmt);
		else if ($postType=="D") $grandTotalDebitAmt += abs($openingBalanceAmt);		
		# Grand total ends here
	}

	
	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($fetchAllDistributorAccountRecs);
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode || $editMode) {
		$pendingChequeRecs = array();

		# List all Distributor
		//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
		$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
				

		if ($entryType=="AD") $eType = "D";
		else if ($entryType=="AC") $eType = "C";
		else $eType = "";

		# Common Reson
		$commonReasonRecs = $comReason_m->findAll(array("where"=>"cod='$eType'", "order"=>"default_entry desc, reason asc"));

		if ($commonReason!="" && $commonReason!="OT") {
			$chkListRecs = $crChkList_m->findAll(array("where"=>"common_reason_id='".$commonReason."'", "order"=>"id asc"));
			# payment Received entry
			$paymentReceivedEntry = $distributorAccountObj->DefaultReasonEntry($commonReason);
			
			if (!$paymentReceivedEntry) {
				# Check Return 
				$chequeReturnEntry = $distributorAccountObj->chequeReturnEntry($commonReason);
				if ($chequeReturnEntry) {
					$pendingChequeRecs = $distributorAccountObj->getPendingCheques($selDistributorId, $selCity);
				}
			}
			
			# Default reason type
			$defaultReasonType = $distributorAccountObj->defaultReasonType($commonReason);
		}

		#  Get all Invoice recs
		$distCityList = array();
		if ($selDistributorId!="") {
			$selMode = $addMode;
			if (!sizeof($refInvoiceArr) && $editMode) $selMode = true; 
			$invoiceRecs	= $distributorAccountObj->getInvoiceRecs($selDistributorId, $selCity, '', $defaultReasonType, $selMode);
			# Find Invoice Rec size
			$invRecSize 	= $distributorAccountObj->fetchAllInvoiceRecs($selDistributorId, $selCity, '', $defaultReasonType, $selMode);

			$distCityList = $distributorAccountObj->distributorCityRecs($selDistributorId);
			//printr($distCityList);
			list($cLimit, $cPeriod, $totOutSAmt, $crPeriodFrom) = $salesOrderObj->getDistMasterRec($selDistributorId);
		} 

		# Billing company bank Account
		$billingCompanyBankAcs = $billingCompanyObj->fetchAllCompanyBankACs();
	
		# Dist Bank Ac recs
		$distBankACRecs = $distributorMasterObj->fetchAllDistBankACs($selDistributorId, $selCity);
	} // AddMode/Edit Mode check ends here

	
	# Setting the mode
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;
	else 			$mode = "";

	if ($editMode)	$heading = $label_editDistributorAccount;
	else 		$heading = $label_addDistributorAccount;

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	# On Load Print JS	
	$ON_LOAD_PRINT_JS	= "libjs/DistributorAccount.js";
	
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDistributorAccount" id="frmDistributorAccount" action="DistributorAccount.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
		<?php
		if (sizeof($distributorAccountRecords)>0) {
		?>	
			<tr>
				<td align="center" id="refreshMsgRow" class="err1" style="font-size:9pt;line-height:20px;">	
				</td>			
			</tr>
		<?php
			}
		?>
		<? if($err!="" ){?>
		<tr>
			<td height="20" align="center" class="err1" ><?=$err;?></td>			
		</tr>
		<?}?> 
		<?
			if ($editMode) {
		?>		
		<tr>
			<td align="center" id="timeTickerRow" class="err1" height="20" style="font-size:14pt;" onMouseover="ShowTip('Time remaining to cancel the selected record.');" onMouseout="UnTip();">	
			</td>			
		</tr>
		<tr><TD height="5"></TD></tr>	
		<?
			}
		?>
		<?php
		if ($refInvNotExist) {
		?>
		<tr>
			<td align="center" class="listing-head">
				<span style='color:red'>
					Advance Payment Pending. Kindly account against a Sales Invoice.
					<!--Ref. invoice is not assigned in some entries. So please edit that account entry and assign a invoice.-->
				</span>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		if ($distributorFilterId && !$invoiceFilterId) {
		?>
		<tr>
			<td align="center">
			<table>
				<TR><TD>
				<table>
					<TR>
						<TD class="listing-head">
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
					</TR>
				</table>
				</TD></TR>
			</table>
			</td>
		</tr>
		<?php
		}
		?>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
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
		<!--<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorAccount.php');">&nbsp;&nbsp;
		<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistributorAccount(document.frmDistributorAccount, '');">	-->								
	</td>
	<?} else{?>
	<td  colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorAccount.php');">&nbsp;&nbsp;
		<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateDistributorAccount(document.frmDistributorAccount, '');">
		&nbsp;&nbsp;
	</td>
	<?}?>
	</tr>
	<input type="hidden" name="hidDistributorAccountId" value="<?=$editDistributorAccountId;?>">
	<input type="hidden" name="pmtType" id="pmtType" value="<?=$pmtType;?>" readonly="true">
	<tr><td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" id="overdueAmtMsg" class="err1" align="center"></td></tr>
	<tr>
	<td colspan="2" nowrap style="padding-left:5px; padding-right:5px;">
	<table border="0">
	<TR>
	<TD>
		<table>
			<tr>
			<TD>
			<table>
			<TR>
				<TD class="fieldName" nowrap>*Entry Type</TD>
				<td nowrap>
				<select name="entryType" id="entryType" onchange="this.form.submit()">
				<option value="">--Select--</option>
				<?php
				foreach ($entryTypeArr as $etKey=>$etVal) {
					$selected = ($entryType==$etKey)?"selected":"";
				?>
				<option value="<?=$etKey?>" <?=$selected?>><?=$etVal?></option>
				<?php
				}
				?>	
				</select>
				</td>
			</TR>
			</table>
			</TD>
			<TD>
			<table>
			<TR>
				 <td class="fieldName" nowrap>*Date: </td>
                                <td class="listing-item">
					<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" />
				</td>
			</TR>
			</table>
			</TD>				
			<TD>
			<table>
			<TR>
				<td class="fieldName">*Distributor</td>
                                                  <td class="listing-item">
							<select name="selDistributor" id="selDistributor" onchange="xajax_getInvoices('referenceInvoice', document.getElementById('selDistributor').value, document.getElementById('selCity').value, ''); xajax_cityList('selCity', document.getElementById('selDistributor').value, '', ''); xajax_pendingCheques(document.getElementById('selDistributor').value, document.getElementById('selCity').value); xajax_distDtls(document.getElementById('selDistributor').value); filterRefInv(); distBankAC(); validAdvAmt();">	
							<option value="">--Select--</option>
							<?	
							if ($distributorResultSetObj->getNumRows()>0) {
								while ($dr=$distributorResultSetObj->getRow()) {
									$distributorId	 = $dr[0];		
									$distributorName = stripSlash($dr[2]);	
									$selected = "";
									if ($selDistributorId==$distributorId) $selected = "selected";	
							?>
							<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
							<? 
								}
							}
							?>
							</select>
						</td>
					<td class="fieldName">*City</td>
                                                  <td class="listing-item">
							<select name="selCity" id="selCity" onchange="xajax_getInvoices('referenceInvoice', document.getElementById('selDistributor').value, document.getElementById('selCity').value, '', ''); xajax_pendingCheques(document.getElementById('selDistributor').value, document.getElementById('selCity').value); distBankAC();">	
							<?php if (sizeof($distCityList)<=0) { ?>
								<option value="">--Select--</option>
							<?php } ?>
							<?php
							foreach ($distCityList as $cityId=>$cityName) {
								$selected = ($cityId==$selCity)?"selected":"";
							?>
							<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
							<?php
							}
							?>	
							</select>
						</td>
			</TR>
			</table>
			</TD>
			</tr>
	<tr>
	<TD colspan="3">
	<table><TR>
	<TD class="fieldName" nowrap>*Reason</TD>
	<TD nowrap>
		<table>
		<TR>
			<TD>
				<select name="commonReason" id="commonReason" style="width:100px;" onchange="this.form.submit();" <?=$fieldDisabled?>>
				<option value="">--Select--</option>
				<?php
				foreach ($commonReasonRecs as $crr) {
					$selected = ($crr->id==$commonReason)?"selected":""; 
				?>
				<option value="<?=$crr->id?>" <?=$selected?>><?=$crr->reason?></option>
				<?php
					}	
				?>
				<option value="OT" <?=($commonReason=="OT" || $otherReason!="")?"selected":""?>>OTHER</option>
				</select>
				<?php
				if ($editMode) {
				?>
				<input type="hidden" name="commonReason" id="commonReason" value="<?=($commonReason==0)?'OT':$commonReason;?>" readonly="true" />
				<?php
				} // Edit check ends here
				?>
			</TD>
			<td id="otherRn" style="display:none;">
				<input type="text" name="otherReason" id="otherReason" value="<?=$otherReason?>" />
			</td>
		</TR>
		</table>							
	</TD>	
	<?php
	//printr($pendingChequeRecs);
	if ($chequeReturnEntry=="CR" && $addMode) {
	?>
	<td>
		<table>
		<TR>
		<TD nowrap class="fieldName">*Pending Cheques</TD>
		<td nowrap>
		<select name="pendingCheque" id="pendingCheque" onchange="this.form.submit();">
		<?php // if (sizeof($pendingChequeRecs)<=0) { ?>
		<option value="">--Select--</option>
		<?// }?>
		<?php
		foreach ($pendingChequeRecs as $chequeEntryId=>$pendingChequeNo) {
			$selected = ($selPendingChequeId==$chequeEntryId)?"Selected":"";
		?>
		<option value="<?=$chequeEntryId?>" <?=$selected?>><?=$pendingChequeNo?></option>
		<?php
		}
		?>
		</select>
		</td>
		</TR>
		</table>
	</td>
	<?php
	} // Pending Cheque ends here
	?>
	</TR>
	</table>	
	</TD>
	
	</tr>
	</table>
	</TD>
	</TR>
	<?php
		if ($entryType=="PR" || $paymentReceivedEntry || $defaultReasonType=='AP') {
	?>
	<tr id="PRType">
	<TD>
		<table>
		<tr>
		<TD valign="top">
			<table>	
			<TR>
				<TD class="fieldName" nowrap>*Payment mode</TD>
				<TD nowrap>
				<select name="paymentMode" id="paymentMode" onchange="disPmtMode();">
					<option value="">--Select--</option>
					<?php
					foreach ($paymentModeArr as $pmKey=>$pmVal) {
						$selected = ($paymentMode==$pmKey)?"selected":"";
					?>
					<option value="<?=$pmKey?>" <?=$selected?>><?=$pmVal?></option>
					<?php
					}
					?>
				</select>
				</TD>
			</TR>
			<TR id="chqRTGSRow">
				<TD class="fieldName" nowrap>Cheque/RTGS No.</TD>
				<TD nowrap>
					<input type="text" name="chqRtgsNo" id="chqRtgsNo" size="14" value="<?=$chqRtgsNo?>" <?=$readonly?> />
				</TD>
			</TR>
			<TR id="chqDateRow">
				<TD class="fieldName" nowrap title="Cheque date">Date</TD>
				<TD nowrap>
					<input type="text" name="chqDate" id="chqDate" size="8" value="<?=$chqDate?>" <?=$readonly?> autocomplete="off" />
				</TD>
			</TR>
			<TR id="distBACRow">
				<TD class="fieldName" nowrap>Distributor Bank Account</TD>
				<TD nowrap>
					<select name="distBankAccount" id="distBankAccount">
					<option value="">--Select--</option>
					<?php
					foreach ($distBankACRecs as $dbr) {
						$distBankId 	= $dbr[0];
						$distDefaultAC	= $dbr[4];
						$distBankName	= $dbr[5];
						$selected = ($distBankAccount==$distBankId || $distDefaultAC=='Y')?"selected":"";
					?>
					<option value="<?=$distBankId?>" <?=$selected?>><?=$distBankName?></option>
					<?php
						}
					?>
					</select>
				</TD>
			</TR>	
			<!--<TR>
				<TD class="fieldName" nowrap>Bank</TD>
				<TD nowrap>
					<input type="text" name="bankName" id="bankName" size="24" value="<?=$bankName?>" />
				</TD>
			</TR>
			<TR>
				<TD class="fieldName" nowrap>Account no</TD>
				<TD nowrap>
					<input type="text" name="accountNo" id="accountNo" size="24" value="<?=$accountNo?>" />
				</TD>
			</TR>	-->	
			</table>
		</TD>
		<TD valign="top">
			<table>			
			<!--<TR>
				<TD class="fieldName" nowrap>Branch Location</TD>
				<TD nowrap>
					<input type="text" name="branchLocation" id="branchLocation" size="24" value="<?=$branchLocation?>" />
				</TD>
			</TR>-->
			<TR id="cpnyBACRow">
				<TD class="fieldName" style="line-height:normal;" nowrap>
					Deposited in<br/> COMPANY BANK ACCOUNT	
				</TD>
				<TD nowrap>
					<!--<input type="text" name="depositedBankAccount" id="depositedBankAccount" value="<?=$depositedBankAccount?>" size="24" />-->					
					<select name="depositedBankAccount" id="depositedBankAccount">
					<option value="">--Select--</option>
					<?php
					foreach ($billingCompanyBankAcs as $bcr) {
						$bcBankId 	= $bcr[0];
						$defaultAC	= $bcr[3];
						$disBankName	= $bcr[4];
						$selected = ($depositedBankAccount==$bcBankId || $defaultAC=='Y')?"selected":"";
					?>
					<option value="<?=$bcBankId?>" <?=$selected?>><?=$disBankName?></option>
					<?php
						}
					?>
					</select>
				</TD>
			</TR>
			<TR>
				<TD class="fieldName" nowrap title="Realization date">Value date</TD>
				<TD nowrap>
					<input type="text" name="valueDate" id="valueDate" size="8" autocomplete="off" value="<?=$valueDate?>" <?=$readonly?> />
				</TD>
			</TR>
			<tr>
				<TD class="fieldName" nowrap="true">*Amount</TD>
				<td>
					<input type="text" name="amount" id="amount" value="<?=$amount?>" size="6" style="text-align:right;" autocomplete="off" <?=$readonly?> onkeyup="disRefInvSec(); chkBalAsAdvAmt();" />
					<input type="hidden" name="hidAmount" id="hidAmount" value="<?=$amount?>" size="6" style="text-align:right;">	
				</td>
			</tr>
			<TR>
				<TD class="fieldName" nowrap>Bank Charges</TD>
				<TD nowrap>
					<table>
						<TR>
							<TD nowrap>
							<input type="text" name="bankCharges" id="bankCharges" size="8" value="<?=$bankCharges?>" style="text-align:right;" <?=$readonly?> onkeyup="displayExtraCharge();" />
							<input type="hidden" name="hidBankCharges" id="hidBankCharges" size="8" value="<?=$bankCharges?>" readonly />
							</TD>
							<td class="fieldName">Description</td>
							<td nowrap>
							<textarea name="bankChargeDescription" id="bankChargeDescription" <?=$readonly?>><?=$bankChargeDescription?></textarea>
							</td>
						</TR>
					</table>
				</TD>
			</TR>
			</table>
		</TD>
		<!--<td valign="top">&nbsp;</td>-->
		</tr>		
	<tr id="refInvSection">
	<TD colspan="2">
		<table>
	<?php
	if ($defaultReasonType!='AP' || $editMode) {
	?>
			<!--<tr>
				<TD class="fieldName" nowrap>Payment against</TD>
				<td nowrap="true" class="listing-item">
					<select name="pmtType" id="pmtType" onchange="disPmtType()">
						<option value="S" <?//=($pmtType=='S')?"selected":""?>>SINGLE</option>
						<option value="M" <?//=($pmtType=='M')?"selected":""?>>MULTIPLE</option>
					</select>&nbsp;Invoice
				</td>
			</tr>-->
	<tr id="multipleRefInvRow">
		<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
		<table>
			<TR>
			<TD>
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblRefInv" class="newspaperType">
				<tr align="center">
					<th nowrap style="text-align:center;">Ref.Inv</th>
					<th nowrap style="text-align:center;">Inv Value</th>
					<th nowrap>Pending<br/>Payment Value</th>	
					<th nowrap id="bcAppHCol">Bank Charge<br/>Applicable</th>
					<th nowrap id="pcAppHCol">Penalty<br/>Applicable</th>
					<th>&nbsp;</th>
				</tr>
	<?php	
	if ($pmtType!="" && $refInvArrSize>0) {
		$j = 0;
		$totPendingAmt = 0;
		foreach ($refInvoiceArr as $eId=>$selRefInvId) {
			
			# Inv Amt
			list($invAmt,$despatchDate) = $distributorAccountObj->getInvValue($selRefInvId);
			if ($despatchDate!="" || $despatchDate!='0000-00-00') $despatchDate = dateFormat($despatchDate);
			list($refInvEntryId, $refInvAmt) = $distributorAccountObj->multipleRefInvAmt($editDistributorAccountId, $selRefInvId, $pmtType);
			$refAmtReadonly = "";
			if ($selRefInvId=='ADV') {
				$advPmtRecs = $distributorAccountObj->splitupAdvPmtRecs($editDistributorAccountId);
				$refInvAmt = $advPmtRecs[0][0];
				$balAdvAmt = $refInvAmt;
				$refAmtReadonly = "readonly='true'";
			}

			$totPendingAmt += $refInvAmt;
			$pendingAmt = $distributorAccountObj->pendingAmt($selRefInvId);
			$balDueAmt = $pendingAmt+$refInvAmt;

			$bcChecked = "";
			$pcChecked = "";
			if (sizeof($refInvoiceArr)>1 && ($bankCharges!=0 || $chqReturnBankCharge!=0 || $penaltyCharge!=0)) {
				$extraChargeType = $distributorAccountObj->getExtraChargeAppliedInv($editDistributorAccountId, $selRefInvId);
				if ($extraChargeType=='PRBC' || $extraChargeType=='CRBC') $bcChecked = "checked";
				if ($extraChargeType=='CRPC') $pcChecked = "checked";
			}
	?>
	<tr align="center" class="whiteRow" id="row_<?=$j?>">
		<td align="center" class="listing-item">
			<select id="refInv_<?=$j?>" name="refInv_<?=$j?>" onchange="validateRefInvRepeat();xajax_refInvVal('<?=$j?>', document.getElementById('refInv_<?=$j?>').value)">
			<?php
				foreach ($invoiceRecs as $invoiceId=>$invoiceNo) {
					$selected = ($invoiceId==$selRefInvId || $selRefInvId=='ADV')?"selected":"";
			?>
			<option value='<?=$invoiceId?>' <?=$selected?>><?=$invoiceNo?></option>;
			<?php
				}
			?>
			</select>
		</td>
		<td align="center" class="listing-item">
			<input type="text" readonly style="border: none ; text-align: right;" size="8" id="refInvAmt_<?=$j?>" name="refInvAmt_<?=$j?>" value="<?=$invAmt?>" />
		</td>
		<td align="center" class="listing-item">
				<input type="text" style="text-align: right;" size="8" id="refAmt_<?=$j?>" name="refAmt_<?=$j?>" value="<?=$refInvAmt?>" onkeyup="calcPendingAmt(); chkBalAsAdvAmt();" autocomplete="off" <?=$refAmtReadonly?> />
		</td>
		<td align="center" class="listing-item" id="bcCol_<?=$j?>">
				<input type='checkbox' name='bcApp_<?=$j?>' id='bcApp_<?=$j?>' value='Y' class='chkBox' onclick="bcChk('<?=$j?>');" <?=$bcChecked?>>
		</td>
		<td align="center" class="listing-item" id="pcCol_<?=$j?>">
				<input type='checkbox' name="pcApp_<?=$j?>" id='pcApp_<?=$j?>' value='Y' class='chkBox' onclick="pcChk('<?=$j?>');" <?=$pcChecked?>>
		</td>
		<td align="center" class="listing-item">
			<a onclick="setRefInvItemStatus('<?=$j?>');" href="###">
				<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
			</a>
			<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
			<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>"/>
			<input type="hidden" value="" id="chkListEntryId_<?=$j?>" name="chkListEntryId_<?=$j?>"/>
			<input type="hidden" value="" id="hidRefInvId_<?=$j?>" name="hidRefInvId_<?=$j?>"/>
			<input type="hidden" value="<?=$refInvEntryId?>" id="refInvEntryId_<?=$j?>" name="refInvEntryId_<?=$j?>"/>
			<input name='hidDespatchDate_<?=$j?>' type='hidden' id='hidDespatchDate_<?=$j?>' value='<?=$despatchDate?>' readonly>
			<input name='hidBalDueAmt_<?=$j?>' type='hidden' id='hidBalDueAmt_<?=$j?>' value='<?=number_format($balDueAmt,2,'.','')?>' readonly>
		</td>
	</tr>
	<?php
				$j++;
			} // Loop ends here
		}
	?>
	<tr>
		<TD class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="2" align="right">Total:</TD>
		<td align="center">
			<input type="text" name="totPmtVal" id="totPmtVal" value="<?=number_format($totPendingAmt,2,'.','');?>" size="8" readonly="true" style="border:none; text-align:right;" />
		</td>
		<td id="bcAppFCol" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
		<td id="pcAppFCol" style="padding-left:5px; padding-right:5px;">&nbsp;</td>		
		<td>&nbsp;</td>
	</tr>
	</table>
	<!--  Hidden Fields-->
	<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$refInvArrSize?>" readonly="true">
	</TD>
	</TR>
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD>
			<a href="###" id='addRow' onclick="javascript:addNewRefInvItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
			<!--<TR id="singleRefInvRow">
				<TD class="fieldName" nowrap>*Reference Invoice</TD>
				<TD nowrap class="listing-item">
				<select name="referenceInvoice[]" id="referenceInvoice" style="width:100px;" onchange="xajax_invValue(document.getElementById('referenceInvoice').value);">
					<?php //if (sizeof($invoiceRecs)<=0) { ?>
						<option value="">--Select--</option>
					<?php // } ?>
					<?php
					//foreach ($invoiceRecs as $invoiceId=>$invoiceNo) {
						//$selected = (in_array($invoiceId,$refInvoiceArr))?"selected":"";
					?>
					<option value="<?//=$invoiceId?>" <?//=$selected?>><?//=$invoiceNo?></option>
					<?php
						//}
					?>
				</select>
				<input type="hidden" name="balDueAmt" id="balDueAmt" />
				</TD>
			</TR>		
			<tr id="singleInvRefVal">
				<TD id="singleInvRef" align="left" colspan="2" style="padding-left:25px;"></TD>
			</tr>-->
	<?php
		} // Dist accont Advance payment check ends here
	?>
		</table>	
	</TD>
	</tr>
	</table>
	</TD>
	</tr>
	<?php
		} //$entryType ends here
	?>
	</table>
	</td>
	</tr>
	<?php
		if ($entryType!="" && $entryType!="PR" && $paymentReceivedEntry=="" && $defaultReasonType!='AP') {
	?>
	<tr id="OtherEntryType">
	<td colspan="2" nowrap>
	<table>
	<TR>		
		<TD valign="top">
			<table>
				<tr>
					<TD class="fieldName" nowrap="true">*Amount</TD>
					<td>
						<input type="text" name="amount" id="amount" value="<?=$amount?>" size="6" style="text-align:right;" autocomplete="off" <?=$setReadOnlyField?> onkeyup="disRefInvSec();" />
						<input type="hidden" name="hidAmount" id="hidAmount" value="<?=$amount?>" size="6" style="text-align:right;" />	
					</td>
				</tr>
					<!--<tr>
						<TD class="fieldName">Debit</TD>
						<td>
							<input type="checkbox" name="debit" id="debit" value="D" <?=$debitChk?> class="chkBox">
						</td>
					</tr>-->
						<tr>
							<TD class="fieldName">Description</TD>
							<td>
								<textarea name="amtDescription" style="width:100px;"><?=$amtDescription?></textarea>
							</td>
						</tr>
				<?php
				if ($chequeReturnEntry) {
				?>
				<TR>
				<TD class="fieldName" nowrap>*Bank Charges</TD>
				<TD nowrap>
					<table>
						<TR>
							<TD nowrap>
							<input type="text" name="chqReturnBankCharge" id="chqReturnBankCharge" size="8" value="<?=$chqReturnBankCharge?>" style="text-align:right;" autocomplete="off" onkeyup="displayExtraCharge();" />
							<input type="hidden" name="hidChqReturnBankCharge" id="hidChqReturnBankCharge" size="8" value="<?=$chqReturnBankCharge?>" readonly />
							</TD>
						</TR>
					</table>
					</TD>
				</TR>
				<TR>
				<TD class="fieldName" nowrap>*Penalty</TD>
				<TD nowrap>
					<table>
						<TR>
							<TD nowrap>
							<input type="text" name="penaltyCharge" id="penaltyCharge" size="8" value="<?=$penaltyCharge?>" style="text-align:right;" autocomplete="off" onkeyup="displayExtraCharge();" />
							<input type="hidden" name="hidPenaltyCharge" id="hidPenaltyCharge" size="8" value="<?=$penaltyCharge?>" readonly />
							</TD>
						</TR>
					</table>
				</TD>
			</TR>
			<?php
				} // Check retrun ends here
			?>
			</table>			
		</TD>
<!-- Ref Invoice section starts here -->
		<TD valign="top" id="refInvSection">
			<table>
			<!--<tr>
				<TD class="fieldName" nowrap>Payment against</TD>
				<td nowrap="true" class="listing-item">
					<select name="pmtType" id="pmtType" onchange="disPmtType()">
						<option value="S" <?//=($pmtType=='S')?"selected":""?>>SINGLE</option>
						<option value="M" <?//=($pmtType=='M')?"selected":""?>>MULTIPLE</option>
					</select>&nbsp;Invoice
				</td>
			</tr>-->
	<tr id="multipleRefInvRow">
		<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
		<table>
			<TR>
			<TD>
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblRefInv" class="newspaperType">
				<tr align="center">
					<th nowrap style="text-align:center;">Ref.Inv</th>
					<th nowrap style="text-align:center;">Inv Value</th>
					<th nowrap>Pending<br/>Payment Value</th>	
					<th nowrap id="bcAppHCol">Bank Charge<br/>Applicable</th>
					<th nowrap id="pcAppHCol">Penalty<br/>Applicable</th>
					<th>&nbsp;</th>
				</tr>
	<?php
	if ($pmtType!="" && $refInvArrSize>0) {
		$j = 0;
		$totPendingAmt = 0;
		foreach ($refInvoiceArr as $eId=>$selRefInvId) {
			# Inv Amt
			list($invAmt,$despatchDate) = $distributorAccountObj->getInvValue($selRefInvId);
			if ($despatchDate!="" || $despatchDate!='0000-00-00') $despatchDate = dateFormat($despatchDate);

			list($refInvEntryId, $refInvAmt) = $distributorAccountObj->multipleRefInvAmt($editDistributorAccountId, $selRefInvId, $pmtType);
			$totPendingAmt += $refInvAmt;
			$pendingAmt = $distributorAccountObj->pendingAmt($selRefInvId);
			$balDueAmt = $pendingAmt+$refInvAmt;

			$bcChecked = "";
			$pcChecked = "";
			if (sizeof($refInvoiceArr)>1 && ($bankCharges!=0 || $chqReturnBankCharge!=0 || $penaltyCharge!=0)) {
				$extraChargeType = $distributorAccountObj->getExtraChargeAppliedInv($editDistributorAccountId, $selRefInvId);
				if ($extraChargeType=='PRBC' || $extraChargeType=='CRBC') $bcChecked = "checked";
				if ($extraChargeType=='CRPC') $pcChecked = "checked";
			}
	?>
	<tr align="center" class="whiteRow" id="row_<?=$j?>">
		<td align="center" class="listing-item">
			<select id="refInv_<?=$j?>" name="refInv_<?=$j?>" onchange="validateRefInvRepeat();xajax_refInvVal('<?=$j?>', document.getElementById('refInv_<?=$j?>').value)">
			<?php
				foreach ($invoiceRecs as $invoiceId=>$invoiceNo) {
					$selected = ($invoiceId==$selRefInvId)?"selected":"";
			?>
			<option value='<?=$invoiceId?>' <?=$selected?>><?=$invoiceNo?></option>;
			<?php
				}
			?>
			</select>
		</td>
		<td align="center" class="listing-item">
			<input type="text" readonly style="border: none ; text-align: right;" size="8" id="refInvAmt_<?=$j?>" name="refInvAmt_<?=$j?>" value="<?=$invAmt?>" />
		</td>
		<td align="center" class="listing-item">
				<input type="text" style="text-align: right;" size="8" id="refAmt_<?=$j?>" name="refAmt_<?=$j?>" value="<?=$refInvAmt?>" onkeyup="calcPendingAmt();" />
		</td>
		<td align="center" class="listing-item" id="bcCol_<?=$j?>">
				<input type='checkbox' name='bcApp_<?=$j?>' id='bcApp_<?=$j?>' value='Y' class='chkBox' onclick="bcChk('<?=$j?>');" <?=$bcChecked?> />
		</td>
		<td align="center" class="listing-item" id="pcCol_<?=$j?>">
				<input type='checkbox' name="pcApp_<?=$j?>" id='pcApp_<?=$j?>' value='Y' class='chkBox' onclick="pcChk('<?=$j?>');" <?=$pcChecked?> />
		</td>
		<td align="center" class="listing-item">
			<a onclick="setRefInvItemStatus('<?=$j?>');" href="###">
				<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
			</a>
			<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
			<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>"/>
			<input type="hidden" value="" id="chkListEntryId_<?=$j?>" name="chkListEntryId_<?=$j?>"/>
			<input type="hidden" value="<?=$selRefInvId?>" id="hidRefInvId_<?=$j?>" name="hidRefInvId_<?=$j?>"/>
			<input type="hidden" value="<?=$refInvEntryId?>" id="refInvEntryId_<?=$j?>" name="refInvEntryId_<?=$j?>"/>
			<input name='hidDespatchDate_<?=$j?>' type='hidden' id='hidDespatchDate_<?=$j?>' value='<?=$despatchDate?>' readonly>
			<input name='hidBalDueAmt_<?=$j?>' type='hidden' id='hidBalDueAmt_<?=$j?>' value='<?=number_format($balDueAmt,2,'.','')?>' readonly>
		</td>
	</tr>
	<?php
				$j++;
			} // Loop ends here
		}
	?>
	<tr>
		<TD class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="2" align="right">Total:</TD>
		<td align="center">
			<input type="text" name="totPmtVal" id="totPmtVal" value="<?=number_format($totPendingAmt,2,'.','');?>" size="8" readonly="true" style="border:none; text-align:right;" />
		</td>
		<td id="bcAppFCol" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
		<td id="pcAppFCol" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table>
	<!--  Hidden Fields-->
	<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$refInvArrSize?>" readonly="true">
	</TD>
	</TR>
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD>
			<a href="###" id='addRow' onclick="javascript:addNewRefInvItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
			<!--<TR id="singleRefInvRow">
				<TD class="fieldName" nowrap>*Reference Invoice</TD>
				<TD nowrap class="listing-item">
				<select name="referenceInvoice[]" id="referenceInvoice" style="width:100px;" onchange="xajax_invValue(document.getElementById('referenceInvoice').value);">
					<?php // if (sizeof($invoiceRecs)<=0) { ?>
						<option value="">--Select--</option>
					<?php // } ?>
					<?php
					//foreach ($invoiceRecs as $invoiceId=>$invoiceNo) {
					//	$selected = (in_array($invoiceId,$refInvoiceArr))?"selected":"";
					?>
					<option value="<?//=$invoiceId?>" <?//=$selected?>><?//=$invoiceNo?></option>
					<?php
					//	}
					?>
				</select>
				<input type="hidden" name="balDueAmt" id="balDueAmt" />
				</TD>
			</TR>
			<tr id="singleInvRefVal">
				<TD id="singleInvRef" align="left" colspan="2" style="padding-left:25px;"></TD>
			</tr>-->
		</table>			
		</TD>
	</TR>	
	</table>
	</td>
	</tr>	
	<?php
	} // check ends here
	?>
	<?php
		if (sizeof($chkListRecs)>0) {
	?>
			<tr>
					<TD colspan="2"  align="center">
					<table>
						<TR>
						<TD>
						<fieldset style="padding:10px;">
						<legend class="listing-item">Check List</legend>
						<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
							<TR bgcolor="#f2f2f2" align="center">
								<TD>&nbsp;</TD>
								<TD class="listing-head" nowrap>Check List</TD>
							</TR>
							<?php
							$k = 0;
							foreach ($chkListRecs as $clr) {
								$k++;
								$chked = (in_array($clr->id,$chkListArr))?"checked":"";
							?>
							<TR bgcolor="White">
								<TD>
									<INPUT type='checkbox' name='chkListId_<?=$k?>' id='chkListId_<?=$k?>' class="chkBox" value="<?=$clr->id?>" <?=$chked?> />
									<INPUT type='hidden' name='required_<?=$k?>' id='required_<?=$k?>' value="<?=$clr->required?>" readonly />
									<INPUT type='hidden' name='chkListName_<?=$k?>' id='chkListName_<?=$k?>' value="<?=$clr->name?>" readonly />	
								</TD>
								<TD class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
									<?php if ($clr->required=='Y') echo "*";?><?=$clr->name?>
								</TD>
							</TR>
							<?php
							} // Chk List Recs Ends here
							?>
						</table>						
						</fieldset>
						</TD>
						</TR>	
					</table>
					</TD>
			</tr>
			<?php
				} // chk list ends here
			?>			
<!-- Chk List row count -->
			<input type="hidden" name="chkListRowCount" id="chkListRowCount" value="<?=$k?>" readonly />
<!-- Chk List Ends here -->
<tr id="balAdvAmtRow" style="display:none;">
	<TD colspan="2" align="left">
		<table>
			<TR>
				<TD class="fieldName" nowrap="true">Advance Amount</TD>
				<td>
					<input type="text" name="balAdvAmt" id="balAdvAmt" value="<?=$balAdvAmt?>" size="6" style="text-align:right;" autocomplete="off" readonly="true" />
				</td>
			</TR>
		</table>
	</TD>
</tr>
				<tr><TD height="5"></TD></tr>				
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
	<? if($editMode){?>
	<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" id="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorAccount.php');">&nbsp;&nbsp;
		<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistributorAccount(document.frmDistributorAccount, '');">
		<?php
		if ($editMode) {
		?>
		&nbsp;&nbsp;<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" SAVE & CONFIRM " onClick="return validateDistributorAccount(document.frmDistributorAccount, 1);" style="width:110px" />						
		<?php }?>
	</td>
	<?} else{?>
	<td  colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorAccount.php');">&nbsp;&nbsp;
		<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateDistributorAccount(document.frmDistributorAccount,'');">&nbsp;&nbsp;							
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Distributor's Account</td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr>
					<td>&nbsp;</td>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From:</td>
                                    		<td nowrap="nowrap">
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getDistributor(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, ''); xajax_cityFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value);">
					</td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item">Till:</td>
                                <td>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getDistributor(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, ''); xajax_cityFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value);">
				</td>
				<td class="listing-item" nowrap="true">&nbsp;Distributor:&nbsp;</td>
				<td>
				<select name="distributorFilter" id="distributorFilter" style="width:120px;" onchange="xajax_cityFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value); xajax_invFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value, '');">
				<? if (sizeof($distributorFilterRecs)<=0) {?>
				<option value="">--Select All--</option>
				<? } ?>
				<?php
				foreach ($distributorFilterRecs as $distFilterId=>$distName) {
					$selected = ($distributorFilterId==$distFilterId)?"selected":"";
				?>
				<option value="<?=$distFilterId?>" <?=$selected?>><?=$distName?></option>
				<?php
					}
				?>
				</select> 
				</td>
				<td class="listing-item" nowrap="true">&nbsp;City:&nbsp;</td>
				<td align="left">
				<select name="cityFilter" id="cityFilter" onchange="xajax_invFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value, document.getElementById('cityFilter').value);">
					<?php if (sizeof($distCityFilterList)<=0) { ?>
						<option value="">--Select All--</option>
					<?php } ?>
					<?php
						/*
						foreach ($distCityFilterList as $cityId=>$cityName) {
							$selected = ($cityId==$cityFilterId)?"selected":"";
						*/
					?>
						<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
					<?php
						//}
					?>
				</select>
				</td>
				<td class="listing-item" nowrap="true">&nbsp;Invoice:&nbsp;</td>
				<td align="left">
				<select name="invoiceFilter" id="invoiceFilter">					
					<?php if (sizeof($invoiceFilterList)<=0) { ?>
						<option value="">--Select All--</option>
					<?php } ?>
					<?php
					foreach ($invoiceFilterList as $invoiceId=>$invoiceNo) {
						$selected = ($invoiceId==$invoiceFilterId)?"selected":"";
					?>
					<option value="<?=$invoiceId?>" <?=$selected?>><?=$invoiceNo?></option>
					<?php
					}
					?>
				</select>
				</td>
				<td class="listing-item" nowrap="true" style="padding-left:5px;padding-right:5px;">Reason:</td>
				<td align="left">
				<select name="reasonFilter" id="reasonFilter" multiple>					
					<?php// if (sizeof($invoiceFilterList)<=0) { ?>
						<option value="">--Select All--</option>
					<?php// } ?>
					<?php
					/*foreach ($invoiceFilterList as $invoiceId=>$invoiceNo) {
						$selected = ($invoiceId==$invoiceFilterId)?"selected":"";
					*/
					?>
					<option value="<?=$invoiceId?>" <?=$selected?>><?=$invoiceNo?></option>
					<?php
					//}
					?>
				</select>
				</td>
				
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table>
			</td></tr>
		</table></td>
		</tr>-->
		 <tr> 
                  <td colspan="3" background="images/heading_bg.gif" class="pageName" nowrap style="background-repeat: repeat-x" valign="top" width="100%">&nbsp;Distributor's Account</td>					
                </tr>
		<tr><TD height="5"></TD></tr>
		<tr>
		<td colspan="3" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="0" cellspacing="0">
		<tr>
		<td nowrap>
		<table>
		<TR><TD>
		<fieldset style="padding:10px;">
		<legend class="listing-item">Data Search</legend>
		<table cellpadding="0" cellspacing="0">
			<TR>
			<TD>
			<table>
				<TR><TD>
				<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item">From:</td>
                                    		<td nowrap="nowrap">
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getDistributor(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, ''); xajax_cityFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value);">
					</td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item">Till:</td>
                                <td>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getDistributor(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, ''); xajax_cityFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value);">
				</td>
				<td class="listing-item" nowrap="true">&nbsp;Distributor:&nbsp;</td>
				<td>
				<select name="distributorFilter" id="distributorFilter" style="width:120px;" onchange="xajax_cityFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value); xajax_invFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value, ''); xajax_assignDistFilter(document.getElementById('distributorFilter').value);">
				<? if (sizeof($distributorFilterRecs)<=0) {?>
				<option value="">--Select All--</option>
				<? } ?>
				<?php
				foreach ($distributorFilterRecs as $distFilterId=>$distName) {
					$selected = ($distributorFilterId==$distFilterId)?"selected":"";
				?>
				<option value="<?=$distFilterId?>" <?=$selected?>><?=$distName?></option>
				<?php
					}
				?>
				</select>
				</td>
				<td class="listing-item" nowrap="true">&nbsp;City:&nbsp;</td>
				<td align="left">
				<select name="cityFilter" id="cityFilter" onchange="xajax_invFilterList(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('distributorFilter').value, document.getElementById('cityFilter').value);">
					<?php if (sizeof($distCityFilterList)<=1) { ?>
						<option value="">--Select All--</option>
					<?php } ?>
					<?php
						foreach ($distCityFilterList as $cityId=>$cityName) {
							$selected = ($cityId==$cityFilterId)?"selected":"";
					?>
						<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
					<?php
						}
					?>
				</select>
				</td>
				<td class="listing-item" nowrap="true">&nbsp;Invoice:&nbsp;</td>
				<td align="left">
				<select name="invoiceFilter" id="invoiceFilter" onchange="chkFYSelection();">					
					<?php if (sizeof($invoiceFilterList)<=0) { ?>
						<option value="">--Select All--</option>
					<?php } ?>
					<?php
					foreach ($invoiceFilterList as $invoiceId=>$invoiceNo) {
						$selected = ($invoiceId==$invoiceFilterId)?"selected":"";
					?>
					<option value="<?=$invoiceId?>" <?=$selected?>><?=$invoiceNo?></option>
					<?php
					}
					?>
				</select>
				</td>
				<td class="listing-item" nowrap="true" style="padding-left:5px;padding-right:5px;">Reason:</td>
				<td align="left">
				<select name="reasonFilter[]" id="reasonFilter" multiple size="4">
					<option value="">--Select All--</option>
					<?php
					foreach ($commonReasonFilterRecs as $crr) {
						$selected = (in_array($crr->id,$reasonFilterArr))?"selected":"";
					?>
					<option value="<?=$crr->id?>" <?=$selected?>><?=$crr->reason?></option>
					<?php
						}	
					?>
				</select>
				</td>
                          </tr>			
                    </table>
				</TD></TR>
				<tr>
					<TD>
					<table>
						<TR>
							<TD class="listing-item">Filter Type</TD>
							<td>
								<select name="filterType" id="filterType">
									<option value="VE" <?=($filterType=='VE')?"selected":"";?>>Valid ACs</option>
									<option value="PE" <?=($filterType=='PE')?"selected":"";?>>Pending Cheques</option>
									<option value="CHQR" <?=($filterType=='CHQR')?"selected":"";?>>Received Cheque</option>
								</select>
							</td>
						</TR>
					</table>
					</TD>
				</tr>
			</table>
			
			</TD>
			<td style="padding-left:5px;padding-right:5px;">
				<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search">
			</td>
			</TR>
		</table>
		</fieldset>
		</td>
		</tr>
		</table>
			</td></tr>
		</table></td>
		</tr>
							<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
			<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distributorAccountRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorAccount.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&distributorFilter=<?=$distributorFilterId?>&filterType=<?=$filterType?>',700,600);"><? }?>
			<?php// if ($isAdmin || $reEdit) { ?>
				<!--&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="refreshDAC" value="Refresh AC" class="button" onclick="return refreshDistAC();" title="Click here to refresh distributor ac entry. " />-->
			<?php// }?>
</td>
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
								<?php
									}
								?>
								<tr>
									<!--<td width="1" ></td>-->
									<td colspan="3" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($distributorAccountRecords)>0) {
		$i	=	0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="10" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistributorAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&distributorFilter=$distributorFilterId&cityFilter=$cityFilterId&invoiceFilter=$invoiceFilterId&reasonFilter=$reasonFilterIds&filterType=$filterType\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&distributorFilter=$distributorFilterId&cityFilter=$cityFilterId&invoiceFilter=$invoiceFilterId&reasonFilter=$reasonFilterIds&filterType=$filterType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&distributorFilter=$distributorFilterId&cityFilter=$cityFilterId&invoiceFilter=$invoiceFilterId&reasonFilter=$reasonFilterIds&filterType=$filterType\"  class=\"link1\">>></a> ";
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
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
		<? if (!$distributorFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<? }?>
		<? if (!$cityFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>
		<? }?>
		<? if (!$invoiceFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">REF INVOICE</td>
		<? }?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Reason</td>
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Particulars</td>-->		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">AMOUNT DUE<br>(Debit) (Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">AMOUNT RECEIVED<br>(Credit) (Rs.)</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
		<?php
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;
		//&& ($cityFilterId || $distCityFilterRecSize==1)
		if ($distributorFilterId && !$invoiceFilterId && $openingBalanceAmt!=0 && !$reasonFilterIds && $filterType=="VE" && $pageNo==1) {
			if ($postType=="C")  {								
				$totalCreditAmt += abs($openingBalanceAmt);
				//$grandTotalCreditAmt += abs($openingBalanceAmt);
			} else if ($postType=="D") {		 		
				$totalDebitAmt += abs($openingBalanceAmt);
				//$grandTotalDebitAmt += abs($openingBalanceAmt);
			}
		?>
		<tr  bgcolor="WHITE">
			<td width="20">&nbsp;</td>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$dateFrom;?></td>	
			<? if (!$distributorFilterId) {?>
			<td>&nbsp;</td>
			<? }?>
			<? if (!$cityFilterId) {?>
			<td>&nbsp;</td>
			<? }?>
			<? if (!$invoiceFilterId) {?>	
			<td>&nbsp;</td>
			<? }?>
			<!--<td>&nbsp;</td>-->
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				Opening Balance
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='D')?number_format($openingBalanceAmt,2,'.',''):"&nbsp;"?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='C')?number_format($openingBalanceAmt,2,'.',''):"&nbsp;"?>
			</td>
			<? if($edit==true){?>
				<td class="listing-item" width="60" align="center">&nbsp;</td>
			<? }?>
		</tr>
		<?		
			}
		?>
		<?php	
		$distributorAccountId = "";	
		foreach ($distributorAccountRecords as $dar) {
			$i++;			
			$distributorAccountId	= $dar[0];
			$selectDate		= dateFormat($dar[1]);
			$distributorName	= $dar[6];
			$particulars		= $dar[5];
			$amount			= $dar[3];
			$cod			= $dar[4];
			
			$creditAmt = 0;
			$debitAmt  = 0;	
			if ($cod=="C")  {				
				$creditAmt = number_format(abs($amount),2,'.','');
				$totalCreditAmt += abs($creditAmt);
			} else if ($cod=="D") {
		 		$debitAmt = number_format(abs($amount),2,'.','');
				$totalDebitAmt += abs($debitAmt);
			}

			$entryConfirmed = $dar[7];
			

			$parentACId	= $dar[8];
			$acEntryType	= $dar[9];

			$pmtMode	= $paymentModeArr[$dar[10]];			

			$chqRTGSNo	= $dar[11];
			$chqRTGSDate	= ($dar[12]!="0000-00-00")?dateFormat($dar[12]):"";
			/*
			$bankName	= $dar[13];
			$acNo		= $dar[14];
			$branchLocation 	= $dar[15];
			$depositedBankACNo	= $dar[16];
			*/
			$bankName	= $dar[31];
			$acNo		= $dar[32];
			$branchLocation 	= $dar[33];
			$depositedBankACNo	= $dar[30];

			$trValueDate	= ($dar[17]!="0000-00-00")?dateFormat($dar[17]):"";

			$dacBankCharge = $dar[18];
			$dacBankChargeDescription =  $dar[19];

			$selCityName	= $dar[20];
			$advEntryId	= $dar[37];

			$chequeReturnStatus 	= $dar[24];
			$chequeReturnEntryId 	= $dar[25];

			$dacChargeType  = $dar[26];
			$deReasonType    = $dar[27];

			if ($dacChargeType=="PRBC" || $dacChargeType=="CRBC") 	$selReasonName = "BANK CHARGES"; 
			else if ($dacChargeType=="CRPC") $selReasonName = "PENALTY CHARGES"; 
			else if ($trValueDate!="" && $chequeReturnStatus=='N' && $deReasonType=='PR') $selReasonName = "PAYMENT RECEIVED"; 
			else $selReasonName	= $dar[21];
			
			# Check Advance entry exist
			$refInvAdvEntryExist = $distributorAccountObj->chkBalAdvPmtEntryExist($distributorAccountId);

			if ($refInvAdvEntryExist) $selReasonName .= "<br>(Adv amt adjust is pending)";

			#Ref Invoice
			$referenceInvoiceRecs = array();
			if (!$invoiceFilterId) {
				$referenceInvoiceRecs = $distributorAccountObj->getRefInvoices($distributorAccountId);	
			}
			
			$selCommonReasonId 	= $dar[22];
			$otherReasonDetails 	= $dar[23];
			if ($selCommonReasonId==0 && $otherReasonDetails!="") $selReasonName = $otherReasonDetails;
			# PR Entry	
			$displayChkList = "";
			if ($selCommonReasonId!=0) {
				$acEntryType = $distributorAccountObj->DefaultReasonEntry($selCommonReasonId);
				list($selChkListRecs, $showChkList) = $distributorAccountObj->distChkList($distributorAccountId);
				if ($showChkList!="") $displayChkList = "onMouseover=\"ShowTip('$showChkList');\" onMouseout=\"UnTip();\" ";
			}

			$displayDetails	= "";
			$displayPopup = false;
			$showPmnt  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";
			if (($acEntryType=="PR" || $deReasonType=='AP') && !$parentACId ) {
				// Main Row
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Payment mode</td><td class=listing-item>$pmtMode</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Cheque/RTGS No.</td><td class=listing-item>$chqRTGSNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Date</td><td class=listing-item>$chqRTGSDate</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Bank</td><td class=listing-item>$bankName</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Account no</td><td class=listing-item>$acNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Branch Location</td><td class=listing-item>$branchLocation</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Deposited Account</td><td class=listing-item>$depositedBankACNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Value date</td><td class=listing-item>$trValueDate</td></tr>";
				$displayPopup = true;
			} 
			if ($particulars!="") {
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Particulars</td><td class=listing-item>".trim($particulars)."</td></tr>";
				$displayPopup = true;
			}
			// Main Row Ends Here
			$showPmnt  .= "</table>";

			$showDebitEntry = "";
			if ($debitAmt!=0 && $displayPopup) $showDebitEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";

			$showCreditEntry = "";
			if ($creditAmt!=0 && $displayPopup) $showCreditEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";
			
			$displayRefInvMsg = "";
			if ($entryConfirmed=="Y") {
				$displayDetails = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
			} else if ((sizeof($referenceInvoiceRecs)<=0 && !$invoiceFilterId) || $advEntryId!=0) {
				$displayDetails = "style=\"background-color: #fbb79f;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#fbb79f'\" ";
				$displayRefInvMsg = "onMouseover=\"ShowTip('Please assign a invoice.');\" onMouseout=\"UnTip();\" ";
			} else {
				$displayDetails = "style=\"background-color: #FFFFCC;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#FFFFCC'\" ";
			}

			$rowDisabled = "";
			if ($entryConfirmed=="Y" || $chequeReturnStatus=='Y') $rowDisabled = "disabled";


			# --------------- Edit Section ---------------			
			$editedTimeInSec = ($dar[36]!="")?$dar[36]:0; // In seconds
			if ($editedTimeInSec>=$refreshTimeLimit) { 
				# Update Rec
				$updateModifiedRec = $distributorAccountObj->updateDistACPModifiedRec($distributorAccountId, '', 'U');
			}
			$modifiedBy	= $dar[34];
			$displayEditStatus = "";
			$editDisabled = "";
			if ($modifiedBy!=0) {
				$lockedUser = $manageusersObj->getUsername($modifiedBy);
				$displayEditStatus = "Locked by $lockedUser";
				$editDisabled = "disabled";
			}
			# ------------------------------	
			
		?>
		<tr <?//=$listRowMouseOverStyle?> <?=$displayDetails?>>
			<td width="20">
				<input type="<?php if (!$parentACId) {?>checkbox<? } else {?>hidden<?}?>" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<? if (!$parentACId) echo $distributorAccountId; ?>" class="chkBox">
				<input type="hidden" name="verified_<?=$i;?>" id="verified_<?=$i;?>" value="<?=$entryConfirmed;?>" readonly>
				<input type="hidden" name="chqReturn_<?=$i;?>" id="chqReturn_<?=$i;?>" value="<?=$chequeReturnStatus;?>" readonly>
				<input type="hidden" name="chqReturnEntryId_<?=$i;?>" id="chqReturnEntryId_<?=$i;?>" value="<?=$chequeReturnEntryId;?>" readonly>
			</td>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selectDate;?></td>
			<? if (!$distributorFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>
			<?php }?>
			<? if (!$cityFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selCityName;?></td>
			<?php }?>
			<? if (!$invoiceFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" <?=$displayRefInvMsg?>>
				<?php
					$numCol = 3;
					if (sizeof($referenceInvoiceRecs)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($referenceInvoiceRecs as $r) {							
							$selName = $r[1];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) echo "<br/>";
						}
					}
				?>
			</td>
			<?php }?>			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" <?=$displayChkList?>>
				<?=$selReasonName;?>
			</td>
			<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				<?//=$particulars?>
			</td>-->
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showDebitEntry?>>
				<?=($debitAmt!=0)?(($displayPopup)?"<a href='###' class='link5'>$debitAmt</a>":$debitAmt):""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showCreditEntry?>>
				<?=($creditAmt!=0 )?(($displayPopup)?"<a href='###' class='link5'>$creditAmt</a>":$creditAmt):""?>
			</td>
			<? if($edit==true){?>
			<td class="listing-item" width="60" align="center">			
				<?php
					if (!$parentACId && $rowDisabled=="") {
				?>
				<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$distributorAccountId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='DistributorAccount.php';" <?=$rowDisabled?> <?=$editDisabled?>>
				<?php
				if ($displayEditStatus!="") {
				?>
					<br/>
					<span class="err1" style="line-height:normal;font-size:8px;"><?=$displayEditStatus?></span>
				<?php
					 }
				?>
				<?php
					} else {
				?>
				&nbsp;
				<?php
					}
				?>
			</td>
			<? }?>
		</tr>
		<?php
			}
			# Find Closing Balance Amt
			
			$closingBalAmt = $grandTotalDebitAmt-$grandTotalCreditAmt;			
			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;
			
			if (!$distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 6;
			else if ($distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 5;
			else if (!$distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 5;
			else if ($distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 4;
			else if ($distributorFilterId && $cityFilterId && $invoiceFilterId) $colSpan = 3;
			else $colSpan = 4;
			
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',','):"";?></strong></td>	
			<td></td>
		</tr>
		<?php
		if ($maxpage==$pageNo && $filterType=="VE") {
			$grandTotalDebitAmt 	+= abs($closingDebitAmt);
			$grandTotalCreditAmt 	+= abs($closingCreditAmt);
		?>
		<tr bgcolor="White">			
			<TD  colspan="<?=$colSpan?>" class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" nowrap="true">Closing Balance:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingDebitAmt!="")?number_format(abs($closingDebitAmt),2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingCreditAmt!="")?number_format(abs($closingCreditAmt),2,'.',','):"";?></strong></td>	
			<td></td>
		</tr>
		<?php
			} 
		?>

		<?php
		if ($maxpage==1 && $filterType=="VE") {
		?>	
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalDebitAmt+abs($closingDebitAmt)),2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalCreditAmt+abs($closingCreditAmt)),2,'.',',')?></strong></td>
			<td></td>	
		</tr>
		<?php
			} 
		?>
		
		<?php
		if ($maxpage>1) {
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Grand Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($grandTotalDebitAmt,2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($grandTotalCreditAmt,2,'.',',')?></strong></td>
			<td></td>	
		</tr>
		<?php
		}
		?>
			<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
			<input type="hidden" name="editId" value="<?=$editId?>">
			<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
         	<td colspan="10" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistributorAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&distributorFilter=$distributorFilterId&cityFilter=$cityFilterId&invoiceFilter=$invoiceFilterId&reasonFilter=$reasonFilterIds&filterType=$filterType\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&distributorFilter=$distributorFilterId&cityFilter=$cityFilterId&invoiceFilter=$invoiceFilterId&reasonFilter=$reasonFilterIds&filterType=$filterType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page&distributorFilter=$distributorFilterId&cityFilter=$cityFilterId&invoiceFilter=$invoiceFilterId&reasonFilter=$reasonFilterIds&filterType=$filterType\"  class=\"link1\">>></a> ";
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
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" >
	<input type="hidden" name="hidSelProduct" value="<?=$selProduct?>">	
	<input type="hidden" name="hidProductGmsPerPouch" id="hidProductGmsPerPouch" value="<?=$productGmsPerPouch?>">
	<input type="hidden" name="totalFixedFishQty" id="totalFixedFishQty" value="<?=$totalFixedFishQty?>">	
	</td>
	</tr>
	<tr>	
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
			<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distributorAccountRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorAccount.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&distributorFilter=<?=$distributorFilterId?>&filterType=<?=$filterType?>',700,600);"><? }?>
		</td>
		</tr>
		</table></td></tr>
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
			<td height="10">
				<input type="hidden" name="hidDistributorFilterId" id="hidDistributorFilterId" value="<?=$distributorFilterId?>" readonly>
				<input type="hidden" name="hidPaymentReceived" id="hidPaymentReceived" value="<?=$paymentReceivedEntry?>" readonly>
				<input type="hidden" name="hidChequeReturnEntry" id="hidChequeReturnEntry" value="<?=$chequeReturnEntry?>" readonly>	
				<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" readonly />
				<input type="hidden" name="chqReturnDistAcId" id="chqReturnDistAcId" value="<?=$chqReturnDistAcId?>" readonly />
				<input type="hidden" name="defaultReasonType" id="defaultReasonType" value="<?=$defaultReasonType?>" readonly />
				<input type="hidden" name="currentDate" id="currentDate" value="<?=date("d/m/Y")?>" readonly />
				<input type="hidden" name="creditPeriod" id="creditPeriod" value="<?=$cPeriod?>" readonly />
				<input type="hidden" name="crPeriodFrom" id="crPeriodFrom" value="<?=$crPeriodFrom?>" readonly />
				<!-- Despatch date format: d/m/Y -->
				<input type="hidden" name="despatchDate" id="despatchDate" value="<?=$singleInvDespatchDate?>" readonly="true" />
				<input type="hidden" name="hidDistributorACId" id="hidDistributorACId" value="" readonly="true" />
				<input type="hidden" name="invRecSize" id="invRecSize" value="<?=$invRecSize?>" readonly="true" />
				<input type="hidden" name="selMode" id="selMode" value="<?=$selMode?>" readonly="true" />
				<input type="hidden" name="advanceEntryExist" id="advanceEntryExist" value="<?=$advanceEntryExist?>" readonly="true" />	
				<input type="hidden" name="advanceEntryConfirmed" id="advanceEntryConfirmed" value="<?=$advanceEntryConfirmed?>" readonly="true" />
				<input type="hidden" name="advAmtRestrictionEnabled" id="advAmtRestrictionEnabled" value="<?=$advAmtRestrictionEnabled?>" readonly="true" />
				<input type="hidden" name="overdueAmt" id="overdueAmt" value="<?=$overdueAmt?>" readonly="true" />
				
			</td>
		</tr>
	<input type="hidden" name="selCoD" value="<?=$selCoD?>" readonly>
	</table>
	<?php
		if ($commonReason=="OT" || $otherReason!="") {
	?>
	<script language="JavaScript">
		reasonOT();	
	</script>
	<?php
	}
	?>
	<SCRIPT LANGUAGE="JavaScript">
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
	
	<SCRIPT LANGUAGE="JavaScript">
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
			inputField  : "chqDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "chqDate", 
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
			inputField  : "valueDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "valueDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<?php
	# Updating Distributor Debit amt and credit Amt
	if ($recUpdated) {
	?>
	<script language="JavaScript">
		xajax_updateDistDebNCrAmt();
	</script>
	<?php
		}
	?>	
<?php
	//if (($addMode || $editMode) && ($entryType=="PR" || $paymentReceivedEntry || ($defaultReasonType=='AP' && $editMode))) {

	//if (($addMode || $editMode) && $entryType!="" && ((!$addMode && $defaultReasonType!='AP') || ($editMode && $defaultReasonType=='AP') ) ) {
	if ( ($defaultReasonType!='AP' || $editMode) && $entryType!="") {
?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewRefInvItem()
		{			
			if (!validateRefInvRepeat()) return false;
			addNewRefInv('tblRefInv','','');			
		}
		
	</SCRIPT>
		<?php
			if (!$refInvArrSize) {
		?>
		<SCRIPT LANGUAGE="JavaScript">
			window.load = addNewRefInvItem();			
		</SCRIPT>
		<?php
			} else if ($refInvArrSize>0 && $pmtType!="") {
		?>
		<script language="JavaScript">
			fieldId = '<?=$refInvArrSize?>';
		</script>
		<?php
			} //Ref Inv Size ends here
		?>
	<?php
		}
	?>
	<?php	
	if (($defaultReasonType!='AP' || $editMode) && $entryType!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		//refInvSection
		disPmtType();
		displayExtraCharge();
	</script>
	<?php
	}
	?>
	<?php
		if ($entryType=="PR" || $paymentReceivedEntry || $defaultReasonType=='AP') {
	?>
	<script language="JavaScript" type="text/javascript">
		disPmtMode();
	</script>
	<?php
	}
	?>
	<?php
		if ($editMode) {
	?>
	<script>
		// Set time D=300
		tickTimer(<?=$refreshTimeLimit?>, '<?=$editDistributorAccountId?>');
	</script>
	<? }?>
	<?php
		if (!$addMode && !$editMode && sizeof($distributorAccountRecords)>0) {
	?>
	<script>
		window.load = beginrefresh();
	</script>
	<? }?>
	<?php
	// Advance amt entry check
	if ($addMode && $advAmtRestrictionEnabled) {
	?>
	<script language="JavaScript" type="text/javascript">
		validAdvAmt();
	</script>
	<?php
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>