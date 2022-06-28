<?php
	require("include/include.php");
	$err = "";
	$errDel = "";
	
	$editTaxMasterRecId = "";
	$taxRecId = "";
	$baseCst = "";
	
	$editExcTaxMasterRecId = "";
	$excTaxRecId = "";
	$excBaseCst = "";
	
	$editECessRecId = "";
	$eCessRecId = "";
	$eCess = "";
	
	$editSecECessRecId = "";
	$secECessRecId		= "";
	$secECess				= "";
	
	$editMode = true;
	$addMode = false;
	
	#-------------------Admin Checking--------------------------------------
	$isAdmin = false;
	$role = $manageroleObj->findRoleName($roleId);
	if (strtolower($role) == "admin" || strtolower($role) == "administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	//------------  Checking Access Control Level  ----------------
	$add = false;
	$edit = false;
	$del = false;
	$print = false;
	$confirm = false;
	
	list($moduleId, $functionId) = $modulemanagerObj->resolveIds($currentUrl);
	
	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	if ($accesscontrolObj->canAdd()) $add = true;
	if ($accesscontrolObj->canEdit()) $edit = true;
	if ($accesscontrolObj->canDel()) $del = true;
	if ($accesscontrolObj->canPrint()) $print = true;
	if ($accesscontrolObj->canConfirm()) $confirm = true;
	//----------------------------------------------------------
	
	$selBaseCst	= "?tt=BCST";
	$selEduCess	= "?tt=EDUC";
	$selSecEduCess	= "?tt=SEDUC";
	$taxType	= $g["tt"];

	$insertTaxRecs = "";
	$insertExcTaxRecs = "";
	$insertECessRecs	=	"";
	$insertSecECessRecs	=	"";
	
	//--------Excise Duty Section----------
	if ($p["cmdSaveExcChange"]) {
	$excTaxRecId = $p["hidExcTaxMasterRecId"];
	$excBaseCst = $p["excBaseCst"];
	$excCstActive = ($p["excCstActive"] == "") ? N : $p["excCstActive"];
	
	$hidExcBaseCst = $p["hidExcBaseCst"];
	$hidExcCSTActive = $p["hidExcCSTActive"];
	
	$excCstRateListId = $p["excCstRateListId"];
	//echo("excTaxRecId" . $excTaxRecId . "excBaseCst" . $excBaseCst . "excCstActive" . $excCstActive);
	
	if ($excCstRateListId == "" && $p["excStartDate"] != "") {
		$excStartDate = mysqlDateFormat($p["excStartDate"]);
		$excRateListName = "TAX-" . date("dMy", strtotime($excStartDate));
	
		$excTaxRateListRecIns = $taxMasterObj->addExcTaxRateList($excRateListName, $excStartDate, $userId);
	
		if ($excTaxRateListRecIns)
		$excCstRateListId = $taxMasterObj->latestExcRateList();
	}
	if (($excBaseCst != $hidExcBaseCst || $excCstActive != $hidExcCSTActive) && $p["excCstRateListId"] != "") {
		$excEffectType = $p["excEffectType"];
		$excSDate = mysqlDateFormat($p["excSDate"]);
	
	
		# Future
		if ($excEffectType == 'EF') {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidExcRateListDate($excSDate);
		if (!$recExist) {
			$excRateListName = "TAX-" . date("dMy", strtotime($excSDate));
			$excTaxRateListRecIns = $taxMasterObj->addExcTaxRateList($excRateListName, $excSDate, $userId);
			if ($excTaxRateListRecIns) {
			$latestExcRateListId = $taxMasterObj->latestExcRateList();
			$updatePrevExcRateListRec = $taxMasterObj->updateExcTaxRateListRec($excCstRateListId, $excSDate);
	
			if ($excBaseCst != "") {
	
				$insertExcTaxRecs = $taxMasterObj->addExcTaxMasterRec($excBaseCst, $excCstActive, $latestExcRateListId);
			}
			}
		} else {
			$errMsg = "Please select a valid date.";
		}
		// Update Present
		} else {
		//$taxRecUptd	= $taxMasterObj->updateTaxMasterRec($taxRecId, $baseCst, $cstActive, $cstRateListId);
		if ($excBaseCst != "" && $excTaxRecId != "") {
			$taxExcRecUptd = $taxMasterObj->updateExcTaxMasterRec($excTaxRecId, $excBaseCst, $excCstActive, $excCstRateListId);
		} else {
			$taxExcRecUptd = $taxMasterObj->addExcTaxMasterRec($excBaseCst, $excCstActive, $excCstRateListId);
		}
		}
	
		//echo "-------------------------------- Inside ------------------";
	} else {
	
		$excStartDate = mysqlDateFormat($p["excStartDate"]);
		$hidExcStartDate = mysqlDateFormat($p["hidExcStartDate"]);
		if ($p["hidExcStartDate"] != "" && $p["excStartDate"] != $p["hidExcStartDate"] && $excCstRateListId != "") {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidExcRateListDate($excStartDate, $excCstRateListId);
		if (!$recExist) {
			$updateExcRateListRec = $taxMasterObj->updateExcRateListRec($excCstRateListId, $excStartDate);
		}
		}
	
		if ($excBaseCst != "" && $excTaxRecId != "") {
		$taxExcRecUptd = $taxMasterObj->updateExcTaxMasterRec($excTaxRecId, $excBaseCst, $excCstActive, $excCstRateListId);
		} else {
		$taxExcRecUptd = $taxMasterObj->addExcTaxMasterRec($excBaseCst, $excCstActive, $excCstRateListId);
		}
	}
	$taxExcRecUptd = false;
	}
	
	# Get Latest excise rate list id
	if ($g["selExciseRateList"] != "")
	$excCstRateListId = $g["selExciseRateList"];
	else if ($p["selExciseRateList"] != "")
	$excCstRateListId = $p["selExciseRateList"];
	else
	$excCstRateListId = $taxMasterObj->latestExcRateList();
	
	
	# Edit
	$taxExcMasterRec = $taxMasterObj->findExc($excCstRateListId);
	//echo("taxesMastet" . $taxExcMasterRec);
	$editExcTaxMasterRecId = $taxExcMasterRec[0];
	$excBaseCst = $taxExcMasterRec[1];
	$excCstActive = $taxExcMasterRec[2];
	$excActive = "";
	if ($excCstActive == 'Y')
	$excActive = "checked";
	$selExcTmr = $taxMasterObj->excTaxRLRec($excCstRateListId);
	$selExcStartDate = $selExcTmr[2];
	//$readOnly   = ($selTmr[3]!='0000-00-00' && $selTmr[3]!="")?"readonly":"";
	//$disabled   = ($selTmr[3]!='0000-00-00' && $selTmr[3]!="")?"disabled='true'":"";
	# Get All Rate list
	$excCstRateListRecs = $taxMasterObj->fetchAllExciseCSTRateListRecs();
	
	//-------------Excise Duty Section Ends Here-------------------------
	
	//-------------Cst section Begins Here----------------------------------
	# Update
	if ($p["cmdSaveChange"] != "") {
	$taxRecId = $p["hidTaxMasterRecId"];
	$baseCst = $p["baseCst"];
	$cstActive = ($p["cstActive"] == "") ? N : $p["cstActive"];
	
	$hidBaseCst = $p["hidBaseCst"];
	$hidCSTActive = $p["hidCSTActive"];
	
	$cstRateListId = $p["cstRateListId"];
	
	if ($cstRateListId == "" && $p["startDate"] != "") {
		$startDate = mysqlDateFormat($p["startDate"]);
		$rateListName = "TAX-" . date("dMy", strtotime($startDate));
	
		$taxRateListRecIns = $taxMasterObj->addTaxRateList($rateListName, $startDate, $userId);
	
		if ($taxRateListRecIns)
		$cstRateListId = $taxMasterObj->latestRateList();
	}
	
	if (($baseCst != $hidBaseCst || $cstActive != $hidCSTActive) && $p["cstRateListId"] != "") {
		$effectType = $p["effectType"];
		$sDate = mysqlDateFormat($p["sDate"]);
	
	
		# Future
		if ($effectType == 'F') {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidRateListDate($sDate);
		if (!$recExist) {
			$rateListName = "TAX-" . date("dMy", strtotime($sDate));
			$taxRateListRecIns = $taxMasterObj->addTaxRateList($rateListName, $sDate, $userId);
			if ($taxRateListRecIns) {
			$latestRateListId = $taxMasterObj->latestRateList();
			$updatePrevRateListRec = $taxMasterObj->updateTaxRateListRec($cstRateListId, $sDate);
			/*
			$baseCst = "";
			$cstActive = "";
			$taxPrevRec = $taxMasterObj->find($cstRateListId);
			$baseCst	= $taxPrevRec[1];
			$cstActive	= $taxPrevRec[2];
			*/
			if ($baseCst != "") {
				$insertTaxRecs = $taxMasterObj->addTaxMasterRec($baseCst, $cstActive, $latestRateListId);
			}
			}
		} else {
			$errMsg = "Please select a valid date.";
		}
		// Update Present
		} else {
		//$taxRecUptd	= $taxMasterObj->updateTaxMasterRec($taxRecId, $baseCst, $cstActive, $cstRateListId);
		if ($baseCst != "" && $taxRecId != "") {
			$taxRecUptd = $taxMasterObj->updateTaxMasterRec($taxRecId, $baseCst, $cstActive, $cstRateListId);
		} else {
			$taxRecUptd = $taxMasterObj->addTaxMasterRec($baseCst, $cstActive, $cstRateListId);
		}
		}
	
		//echo "-------------------------------- Inside ------------------";
	} else {
	
		$startDate = mysqlDateFormat($p["startDate"]);
		$hidStartDate = mysqlDateFormat($p["hidStartDate"]);
		if ($p["hidStartDate"] != "" && $p["startDate"] != $p["hidStartDate"] && $cstRateListId != "") {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidRateListDate($startDate, $cstRateListId);
		if (!$recExist) {
			$updateRateListRec = $taxMasterObj->updateRateListRec($cstRateListId, $startDate);
		}
		}
	
		if ($baseCst != "" && $taxRecId != "") {
		$taxRecUptd = $taxMasterObj->updateTaxMasterRec($taxRecId, $baseCst, $cstActive, $cstRateListId);
		} else {
		$taxRecUptd = $taxMasterObj->addTaxMasterRec($baseCst, $cstActive, $cstRateListId);
		}
	}
	
	
	if ($taxRecUptd || $insertTaxRecs) {
		if ($taxRecUptd)
		$sessObj->createSession("displayMsg", $msg_succTaxMasterUpdate);
		else if ($insertTaxRecs)
		$sessObj->createSession("displayMsg", $msg_succTaxMasterInsert);
		# Update SO REC
		if ($baseCst != $hidBaseCst || $cstActive != $hidCSTActive) {
		$updateBaseCSTInSORec = $changesUpdateMasterObj->updateBaseCSTRecInSO();
		}
		$sessObj->createSession("nextPage", $url_afterUpdateTax);
	} else {
		$editMode = true;
		$err = $msg_failTaxMasterUpdate;
		if ($errMsg)
		$err .= "<br>$errMsg";
	}
		$sessObj->createSession("displayMsg", $msg_succTaxMasterUpdate);
		$sessObj->createSession("nextPage",$url_afterUpdateTax.$selBaseCst);
		$taxRecUptd = false;
	}
	
	# Delete rate list
	// Delete Base CST 
	if ($p["cmdDelRateList"] != "") {
		$selRateList = $p["selRateList"];
		$tmr = $taxMasterObj->taxRLRec($selRateList);
		$srlStartDate = $tmr[2];
		
		# chk rate list in use
		$chkRateListInUse = $taxMasterObj->chkRateListInUse($srlStartDate);
		if (!$chkRateListInUse) {
			$delRateListRec = $taxMasterObj->delRateListRec($selRateList);
		}
		
		if ($delRateListRec) {
			$cstRateListId = "";
			$p["selRateList"] = "";
			$sessObj->createSession("displayMsg", $msg_succDelTaxMaster);
			$sessObj->createSession("nextPage",$url_afterUpdateTax.$selBaseCst);
		} else {
			$err = $msg_failDelTaxMaster;
		}
	}
	
	
	# Get Latest rate list id
	if ($g["selRateList"] != "") $cstRateListId = $g["selRateList"];
	else if ($p["selRateList"] != "") $cstRateListId = $p["selRateList"];
	else $cstRateListId = $taxMasterObj->latestRateList();
	
	
	# Edit
	$taxMasterRec = $taxMasterObj->find($cstRateListId);
	$editTaxMasterRecId = $taxMasterRec[0];
	$baseCst = $taxMasterRec[1];
	$cstActive = $taxMasterRec[2];
	$active = "";
	if ($cstActive == 'Y') $active = "checked";
	$selTmr = $taxMasterObj->taxRLRec($cstRateListId);
	$selStartDate = $selTmr[2];
	$readOnly = ($selTmr[3] != '0000-00-00' && $selTmr[3] != "") ? "readonly" : "";
	$disabled = ($selTmr[3] != '0000-00-00' && $selTmr[3] != "") ? "disabled='true'" : "";
	
	# Get All Rate list
	$cstRateListRecs = $taxMasterObj->fetchAllCSTRateListRecs();
	
	//echo "H=".$cstPercent	= $taxMasterObj->getBaseCst();
	//$latestCSTRateListId = $taxMasterObj->latestRateList();
	
	//----------------CST section ends here-------------------------------------------------
	
	//---------Edu Cess Duty Section starts here----------------------------------------------
	if ($p["cmdEditEduCess"]) {
	$eCessRecId = $p["hidECessRecId"];
	$eCess		 = $p["eCess"];
	$eCessActive = ($p["eCessActive"] == "") ? N : $p["eCessActive"];
	
	$hidECess = $p["hidECess"];
	$hidECessActive = $p["hidECessActive"];
	
	$eCessRateListId = $p["eCessRateListId"];
	
	if ($eCessRateListId == "" && $p["eCessStartDate"] != "") {
		$eCessStartDate = mysqlDateFormat($p["eCessStartDate"]);
		$eCessRateListName = "TAX-" . date("dMy", strtotime($eCessStartDate));
	
		$eCessRateListRecIns = $taxMasterObj->addECessRateList($eCessRateListName, $eCessStartDate, $userId);
	
		if ($eCessRateListRecIns)
		$eCessRateListId = $taxMasterObj->latestECessRateList();
	}
	if (($eCess != $hidECess || $eCessActive != $hidECessActive) && $p["eCessRateListId"] != "") {
		$eCessEffectType = $p["eCessEffectType"];
		$eCessSDate = mysqlDateFormat($p["eCessSDate"]);
	
	
		# Future
		if ($eCessEffectType == 'ECF') {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidECessRateListDate($eCessSDate);
		if (!$recExist) {
			$eCessRateListName = "TAX-" . date("dMy", strtotime($eCessSDate));
			$eCessRateListRecIns = $taxMasterObj->addECessRateList($eCessRateListName, $eCessSDate, $userId);
			if ($eCessRateListRecIns) {
			$latestECessRateListId = $taxMasterObj->latestECessRateList();
			$updatePrevECessRateListRec = $taxMasterObj->updateECessEdRateListRec($eCessRateListId, $eCessSDate);
	
			if ($eCess != "") {
	
				$insertECessRecs = $taxMasterObj->addECessRec($eCess, $eCessActive, $latestECessRateListId);
			}
			}
		} else {
			$errMsg = "Please select a valid date.";
		}
		// Update Present
		} else {
		//$taxRecUptd	= $taxMasterObj->updateTaxMasterRec($taxRecId, $baseCst, $cstActive, $cstRateListId);
		if ($eCess != "" && $eCessRecId != "") {
			$eCessRecUptd = $taxMasterObj->updateECessRec($eCessRecId, $eCess, $eCessActive, $eCessRateListId);
		} else {
			$eCessRecUptd = $taxMasterObj->addECessRec($eCess, $eCessActive, $eCessRateListId);
		}
		}
	
		//echo "-------------------------------- Inside ------------------";
	} else {
	
		$eCessStartDate = mysqlDateFormat($p["eCessStartDate"]);
		$hidECessStartDate = mysqlDateFormat($p["hidECessStartDate"]);
		if ($p["hidECessStartDate"] != "" && $p["eCessStartDate"] != $p["hidECessStartDate"] && $eCessRateListId != "") {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidECessRateListDate($eCessStartDate, $eCessRateListId);
		if (!$recExist) {
			$updateECessRateListRec = $taxMasterObj->updateECessSdRateListRec($eCessRateListId, $eCessStartDate);
		}
		}
	
		if ($eCess != "" && $eCessRecId != "") {
			$eCessRecUptd = $taxMasterObj->updateECessRec($eCessRecId, $eCess, $eCessActive, $eCessRateListId);
		} else {
			$eCessRecUptd = $taxMasterObj->addECessRec($eCess, $eCessActive, $eCessRateListId);
		}
	}
	
		$sessObj->createSession("displayMsg", $msg_succTaxMasterUpdate);
		$sessObj->createSession("nextPage",$url_afterUpdateTax.$selEduCess);
		$eCessRecUptd = false;		
	}

	// Delete Edu Cess ratelist
	if ($p["cmdDelECessRateList"] != "") {
		$selECessRateList = $p["selECessRateList"];
		$tmr = $taxMasterObj->eCessRLRec($selECessRateList);
		$srlStartDate = $tmr[2];
		
		# chk rate list in use
		$chkRateListInUse = $taxMasterObj->chkRateListInUse($srlStartDate);
		if (!$chkRateListInUse) {
			$delRateListRec = $taxMasterObj->delECessRateListRec($selECessRateList);
		}
		
		if ($delRateListRec) {
			$selECessRateList = "";
			$p["selECessRateList"] = "";
			$sessObj->createSession("displayMsg", $msg_succDelTaxMaster);
			$sessObj->createSession("nextPage",$url_afterUpdateTax.$selEduCess);
		} else {
			$err = $msg_failDelTaxMaster;
		}
	}
	
	# Get Latest excise rate list id
	if ($g["selECessRateList"] != "") $eCessRateListId = $g["selECessRateList"];
	else if ($p["selECessRateList"] != "") $eCessRateListId = $p["selECessRateList"];
	else $eCessRateListId = $taxMasterObj->latestECessRateList();
	
	
	# Edit
	$eCessRec = $taxMasterObj->findECess($eCessRateListId);
	$editECessRecId = $eCessRec[0];
	$eCess = $eCessRec[1];
	//echo("ecess="+$eCess);
	$eCessActive = $eCessRec[2];
	$eCActive = "";
	if ($eCessActive == 'Y') $eCActive = "checked";
	$selECessTmr = $taxMasterObj->eCessRLRec($eCessRateListId);
	$selECessStartDate = $selECessTmr[2];
	//$readOnly   = ($selTmr[3]!='0000-00-00' && $selTmr[3]!="")?"readonly":"";
	//$disabled   = ($selTmr[3]!='0000-00-00' && $selTmr[3]!="")?"disabled='true'":"";
	# Get All Rate list
	$eCessRateListRecs = $taxMasterObj->fetchAllECessRateListRecs();
	
	//-------------Edu Cess Duty Section Ends Here-------------------------
	
	//---------Secondray Edu Cess Duty Section starts here----------------------------------------------
	if ($p["cmdEditSecECess"]!="") {

	$secECessRecId = $p["hidSecECessRecId"];
	$secECess = $p["secECess"];
	$secECessActive	 = ($p["secECessActive"] == "") ? N : $p["secECessActive"];
	
	$hidSecECess	= $p["hidSecECess"];
	$hidSecECessActive = $p["hidSecECessActive"];
	
	$secECessRateListId = $p["secECessRateListId"];
	//echo("excTaxRecId" . $excTaxRecId . "excBaseCst" . $excBaseCst . "excCstActive" . $excCstActive);
	
	if ($secECessRateListId == "" && $p["secECessStartDate"] != "") {
		$secECessStartDate = mysqlDateFormat($p["secECessStartDate"]);
		$secECessRateListName = "TAX-" . date("dMy", strtotime($secECessStartDate));
	
		$secECessRateListRecIns = $taxMasterObj->addSecECessRateList($secECessRateListName, $secECessStartDate, $userId);
	
		if ($secECessRateListRecIns)
		$secECessRateListId = $taxMasterObj->latestSecECessRateList();
	}
	if (($secECess != $hidSecECess || $secECessActive != $hidSecECessActive) && $p["secECessRateListId"] != "") {
		$secECessEffectType = $p["secECessEffectType"];
		$secECessSDate = mysqlDateFormat($p["secECessSDate"]);
		
	
		# Future
		if ($secECessEffectType == 'SECF') {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidSecECessRateListDate($secECessSDate);
		if (!$recExist) {
			$secECessRateListName = "TAX-" . date("dMy", strtotime($secECessSDate));
			$secECessRateListRecIns = $taxMasterObj->addSecECessRateList($secECessRateListName, $secECessSDate, $userId);
			if ($secECessRateListRecIns) {
				$latestSecECessRateListId = $taxMasterObj->latestSecECessRateList();
				$updatePrevSEcECessRateListRec = $taxMasterObj->updateSecEdRateListRec($secECessRateListId, $secECessSDate);
		
				if ($secECess != "") {	
					$insertSecECessRecs = $taxMasterObj->addSecECessRec($secECess, $secECessActive, $latestSecECessRateListId);
				}
			}
		} else {
			$errMsg = "Please select a valid date.";
		}
		// Update
		} else {
			if ($secECess != "" && $secECessRecId != "") {
				$secECessRecUptd = $taxMasterObj->updateSecECessRec($secECessRecId, $secECess, $secECessActive, $secECessRateListId);
			} else {
				$secECessRecUptd = $taxMasterObj->addSecECessRec($secECess, $secECessActive, $secECessRateListId);
			}
		}
	} else {
	
		$secECessStartDate = mysqlDateFormat($p["secECessStartDate"]);
		$hidSecECessStartDate = mysqlDateFormat($p["hidSecECessStartDate"]);
		if ($p["hidSecECessStartDate"] != "" && $p["secECessStartDate"] != $p["hidSecECessStartDate"] && $secECessRateListId != "") {
		#Check valid rate list
		$recExist = $taxMasterObj->chkValidSecECessRateListDate($secECessStartDate, $secECessRateListId);
		if (!$recExist) {
			$updateSecECessRateListRec = $taxMasterObj->updateSecSdRateListRec($secECessRateListId, $secECessStartDate);
		}
		}
	
		if ($secECess != "" && $secECessRecId != "") {
			$secECessRecUptd = $taxMasterObj->updatesecECessRec($secECessRecId, $secECess, $secECessActive, $secECessRateListId);
		} else {
			$secECessRecUptd = $taxMasterObj->addsecECessRec($secECess, $secECessActive, $secECessRateListId);
		}
	}

		
			
		$sessObj->createSession("displayMsg", $msg_succTaxMasterUpdate);
		$sessObj->createSession("nextPage",$url_afterUpdateTax.$selSecEduCess);		
		$secECessRecUptd = false;	
	}
	

	// Delete Sec Edu Cess ratelist
	if ($p["cmdDelSecECessRateList"] != "") {
		$selSecECessRateList = $p["selSecECessRateList"];
		$tmr = $taxMasterObj->secECessRLRec($selSecECessRateList);
		$srlStartDate = $tmr[2];
		
		# chk rate list in use
		$chkRateListInUse = $taxMasterObj->chkRateListInUse($srlStartDate);
		$delRateListRec = false;
		if (!$chkRateListInUse) {
			$delRateListRec = $taxMasterObj->delSecECessRateListRec($selSecECessRateList);
		}
		
		if ($delRateListRec) {
			$selSecECessRateList = "";
			$p["selSecECessRateList"] = "";
			$sessObj->createSession("displayMsg", $msg_succDelTaxMaster);
			$sessObj->createSession("nextPage",$url_afterUpdateTax.$selSecEduCess);
		} else {
			$err = $msg_failDelTaxMaster;
		}
	}

	# Get Latest excise rate list id
	if ($g["selSecECessRateList"] != "") $secECessRateListId = $g["selSecECessRateList"];
	else if ($p["selSecECessRateList"] != "") $secECessRateListId = $p["selSecECessRateList"];
	else $secECessRateListId = $taxMasterObj->latestSecECessRateList();
	
	
	# Edit
	$secECessRec = $taxMasterObj->findSecECess($secECessRateListId);
	//echo("taxesMastet" . $taxExcMasterRec);
	$editSecECessRecId = $secECessRec[0];
	$secECess = $secECessRec[1];
	$secECessActive = $secECessRec[2];
	$secActive = "";
	if ($secECessActive == 'Y') $secActive = "checked";
	$selSecECessTmr = $taxMasterObj->secECessRLRec($secECessRateListId);
	$selSecECessStartDate = $selSecECessTmr[2];
	//$readOnly   = ($selSecECessTmr[3]!='0000-00-00' && $selSecECessTmr[3]!="")?"readonly":"";
	//$disabled   = ($selSecECessTmr[3]!='0000-00-00' && $selSecECessTmr[3]!="")?"disabled='true'":"";
	# Get All Rate list
	$secECessRateListRecs = $taxMasterObj->fetchAllSecECessRateListRecs();
	
	//-------------Secondray Edu Cess Duty Section Ends Here--------------------------------------------
	
	# Include JS
	$ON_LOAD_PRINT_JS = "libjs/TaxMaster.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmTaxMaster" action="TaxMaster.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%">
<? if ($err != "") { ?>
	<tr>
	<td height="10" align="center" class="err1" ><?= $err; ?></td>
	</tr>
			<? } ?>

	<tr>
	<td>
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
		<tr>
			<td>
			<!-- Form fields start -->
<?php
			$bxHeader = "Tax Master";
			include "template/boxTL.php";
?>
			<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
				<td width="1" ></td>
				<td colspan="2" >
					<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
					<tr>
						<td colspan="2" height="10" ></td>
					</tr>

					<input type="hidden" name="hidTaxMasterRecId" value="<?= $editTaxMasterRecId; ?>">
					<input type="hidden" name="cstRateListId" id="cstRateListId" value="<?= $cstRateListId ?>" readonly="true">
					<tr>
						<td colspan="2" nowrap height="10"></td>
					</tr>
					<tr>
						<td colspan="2" nowrap>
							<div id="taxSummaryDiv">
							<table width="35%" align="center" cellpadding="0" cellspacing="0">
							<TR>
							<TD>
								<?php
								$entryHead = "";
								require("template/rbTop.php");
								?>
								<table width="200" align="center">
								<tr>
									<td background="images/heading_bg.gif" class="pageName" align="left" width="100%" colspan="3">&nbsp;Summary </td>
								</tr>
								<tr>
									<td>
										<table>
											<TR>
												<TD class="fieldName" nowrap="true">Base CST:</TD>
												<td class="listing-item" nowrap="true">
													<strong><?=$baseCst;?>%</strong>&nbsp;(Start date: <?= ($selStartDate) ? dateFormat($selStartDate) : ""; ?>)
												</td>
												<td>
													<?php
													if ($active!="") {
													?>
														<img src="images/y.png" border="0" title="Active"/>
													<?php
													} else {
													?>
														<img src="images/x.png" border="0" title="disabled"/>
													<?php
													}
													?>
												</td>
												<td>
												<a href="###" onclick="showTax('BCST');" class="link1">Manage</a>
												</td>
											</TR>
											<TR>
												<TD class="fieldName" nowrap="true">Edu Cess:</TD>
												<td class="listing-item" nowrap="true">
													<strong><?=($eCess!=0)?$eCess:0;?>%</strong>&nbsp;<?=($selECessStartDate) ?"(Start date: ".dateFormat($selECessStartDate).")" : ""; ?>
												</td>
												<td>
													<?php
													if ($eCActive!="") {
													?>
														<img src="images/y.png" border="0" title="Active"/>
													<?php
													} else {
													?>
														<img src="images/x.png" border="0" title="disabled"/>
													<?php
													}
													?>
												</td>
												<td>
												<a href="###" onclick="showTax('EDUC');" class="link1">Manage</a>
												</td>
											</TR>
											<TR>
												<TD class="fieldName" nowrap="true">Sec.Edu Cess:</TD>
												<td class="listing-item" nowrap="true">
													<strong><?=($secECess!=0)?$secECess:0;?>%</strong>&nbsp;<?= ($selSecECessStartDate) ? "(Start date: ".dateFormat($selSecECessStartDate).")" : ""; ?>
												</td>
												<td>
													<?php
													if ($secActive!="") {
													?>
														<img src="images/y.png" border="0" title="Active"/>
													<?php
													} else {
													?>
														<img src="images/x.png" border="0" title="disabled"/>
													<?php
													}
													?>
												</td>
												<td>
												<a href="###" onclick="showTax('SEDUC');" class="link1">Manage</a>
												</td>
											</TR>
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
						</div>
<!-- Summary Ends here  -->	
						</td>
					</tr>					
					<tr>
						<td colspan="2" nowrap valign="top">
						<div id="baseCSTDiv" style="display:none;">
						<table width="35%" align="center" cellpadding="0" cellspacing="0">
							<TR>
							<TD>
										<?php
										$entryHead = "";
										require("template/rbTop.php");
										?>
								<table width="200" align="center">
								<tr>
									<td background="images/heading_bg.gif" class="pageName" align="left" width="100%" colspan="3">&nbsp;BASE CST</td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>Rate List </td>
									<td>
									<select name="selRateList" id="selRateList" onchange="this.form.submit();">
										<option value="">--Select--</option>
<?php
										foreach ($cstRateListRecs as $crl) {
										$rateListId = $crl[0];
										$rateListName = stripSlash($crl[1]);
										$rlStartDate = dateFormat($crl[2]);
										$displayRateList = $rateListName . "&nbsp;(" . $rlStartDate . ")";
										$selected = ($cstRateListId == $rateListId) ? "Selected" : "";
?>
											<option value="<?= $rateListId ?>" <?= $selected ?>><?= $displayRateList ?></option>
<? } ?>
										</select>
										</td>
<? if ($del == true && sizeof($cstRateListRecs) > 1) { ?>
										<td>
											<input name="cmdDelRateList" type="submit" class="button" id="cmdDelRateList" value="Delete Rate List" title="click here to delete the selected rate list " onclick="return cfmDel();" />
										</td>
<? } ?>
									</tr>
									<tr>
										<td class="fieldName" nowrap>*Base CST</td>
										<td class="listing-item" align="left">
										<INPUT NAME="baseCst" TYPE="text" id="baseCst" value="<?= $baseCst; ?>" size="4" style="text-align:right;" onblur="chkCSTChange();" autocomplete="off">&nbsp;%
										<INPUT NAME="hidBaseCst" TYPE="hidden" id="hidBaseCst" value="<?= $baseCst; ?>" size="4" style="text-align:right;" readonly="true">
										</td>
									</tr>
									<tr>
										<td class="fieldName" nowrap="nowrap">Active</td>
										<td nowrap="true" align="left">
										<input name="cstActive" type="checkbox" id="cstActive" value="Y" <?= $active ?> class="chkBox" onclick="chkCSTChange();">&nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal" class="fieldName"><font size="1">(If Yes, please give tick mark)</font></span>
										<input name="hidCSTActive" type="hidden" id="hidCSTActive" value="<?= $cstActive ?>" readonly="true">
										</td>
									</tr>
									<tr>
										<TD colspan="2">
										<fieldset>
											<legend class="listing-item">Rate List</legend>
											<table>
											<tr>
												<td class="fieldName" nowrap title="Rate list start date" >*Start Date </td>
												<td>
												<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?= ($selStartDate) ? dateFormat($selStartDate) : ""; ?>" size="8" autocomplete="off" <?= $readOnly ?>>
												<INPUT NAME="hidStartDate" TYPE="hidden" id="hidStartDate" value="<?= ($selStartDate) ? dateFormat($selStartDate) : ""; ?>" size="8" autocomplete="off" readonly="true">
												</td>
											</tr>
											</table>
										</fieldset>
										</TD>
									</tr>
									<tr id="rateListRow">
										<TD colspan="2">
										<fieldset>
											<legend class="listing-item">Rate List section</legend>
											<table>
											<tr>
												<td class="fieldName" nowrap style="line-height:normal;">*When does the change <br>come into effect?</td>
												<td>
												<select name="effectType" id="effectType" onchange="changeEffectType()">
													<option value="">--Select--</option>
													<option value="F">Future</option>
													<option value="P">Present</option>
												</select>
												</td>
											</tr>
											<tr id="futureRow">
												<td class="fieldName" nowrap title="Rate list start date" >*Start Date </td>
												<td>
												<INPUT NAME="sDate" TYPE="text" id="sDate" value="" size="8" autocomplete="off">
												</td>
											</tr>
											</table>
										</fieldset>
										</TD>
									</tr>
									<tr>
										<td colspan="3" height="10"></td>
									</tr>
									<tr>
								<? if ($editMode) {
?>
											<td colspan="3" align="center">
												<? if ($edit == true && $isAdmin == true) { ?>&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validateTaxMaster(document.frmTaxMaster);" <?= $disabled ?>><? } ?>
												&nbsp;<input type="button" name="cmdCancelTax" id="cmdCancelTax" class="button" value=" cancel " onclick="cancelTax();" />
												</td>
<? } else { ?>
											<td align="center">&nbsp;&nbsp;</td>
<? } ?>
										</tr>
										<tr>
											<td colspan="3" height="10" align="center"></td>
										</tr>
										</table>
<?php
										require("template/rbBottom.php");
?>
							</TD>
							</TR>
						</table>
						</div>
						<div id="eduCessDiv" style="display:none;">
						<table width="35%" align="center" cellpadding="0" cellspacing="0">
									<TR>
									<TD>
										<?php
										$entryHead = "";
										require("template/rbTop.php");
										?>
								<table width="200" align="center">
								<tr>
									<td background="images/heading_bg.gif" class="pageName" align="left" width="100%" colspan="3">&nbsp;Edu Cess</td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>Rate List </td>
									<td>
									<select name="selECessRateList" id="selECessRateList" onchange="this.form.submit();">
										<option value="">--Select--</option>
<?php
										foreach ($eCessRateListRecs as $recs) {
										$eRateListId = $recs[0];
										$eCessRateListName = stripSlash($recs[1]);
										$rlStartDate = dateFormat($recs[2]);
										$displayECessRateList = $eCessRateListName . "&nbsp;(" . $rlStartDate . ")";
										$selected = ($eCessRateListId == $eRateListId) ? "Selected" : "";
?>
										<option value="<?= $eRateListId ?>" <?= $selected ?>><?= $displayECessRateList ?></option>
<?php
										}
?>
									</select>
									</td>
									<td>
									<input name="cmdDelECessRateList" type="submit" class="button" id="cmdECessDelRateList" value="Delete Rate List" title="click here to delete the selected rate list "  onclick="return cfmDel();" />
									</td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>*Edu.Cess</td>
									<td class="listing-item" align="left">
									<input type="text" id="eCess" name="eCess" value="<?= $eCess; ?>" size="4" style="text-align:right;" onblur="chkECessChange();" autocomplete="off">&nbsp;%
									<input type="hidden" id="hidECess" name="hidECess" value="<?= $eCess; ?>" size="4" style="text-align:right;" readonly="true"/>
									</td>

								</tr>
								<tr>
									<td class="fieldName" nowrap="nowrap">Active</td>
									<td nowrap="true" align="left">
									<input type="checkbox" id="eCessActive" name="eCessActive" onclick="chkECessChange();" class="chkbox"
										value="Y" <?= $eCActive; ?> />&nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal" class="fieldName"><font size="1">(If Yes, please give tick mark)</font></span>
									<input type="hidden" id="hidECessActive" name="hidECessActive" value="<?= $eCessActive; ?>" readonly="true"/>
									</td>
								</tr>
								<tr>
									<td colspan="2">
									<fieldset>
										<legend class="listing-item">Rate list</legend>
										<table>
										<tr>
											<td class="fieldName" nowrap title="Edu Cess Rate list start date">*Start Date</td>
											<td>
											<input type="text" id="eCessStartDate" name="eCessStartDate" 								value="<?= ($selECessStartDate) ? dateFormat($selECessStartDate) : ""; ?>" autocomplete="off" size="8"/>
											<input type="hidden" id="hidECessStartDate" name="hidECessStartDate" value="<?= ($selECessStartDate) ? dateFormat($selECessStartDate) : ""; ?>" autocomplete="off" readonly="true" size="8"/>
											</td>
										</tr>
										</table>
									</fieldset>
									</td>
								</tr>
								<tr  id="eCessRateRow">
									<td colspan="2">
									<fieldset>
										<legend class="listing-item">Rate List Section</legend>
										<table>
										<tr>
											<td class="fieldName" nowrap style="line-height:normal;">*When does the change <br>come into effect?</td>
											<td>
											<select name="eCessEffectType" id="eCessEffectType" onchange="changeECessEffectType();">
												<option value="">--Select--</option>
												<option value="ECF">Future</option>
												<option value="ECP">Present</option>
											</select>
											</td>
										</tr>
										<tr id="eCessFutureRow">
											<td class="fieldName" nowrap title="Edu Cess Rate list start date" >*Start Date </td>
											<td>
											<input type="text" id="eCessSDate" name="eCessSDate" value="" size="8" autocomplete="off">
											</td>
										</tr>
										</table>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10"><td>
								</tr>
								<tr>
									<td colspan="3" align="center">
									<? if ($edit == true && $isAdmin == true) { ?>
									<input type="submit" name="cmdEditEduCess" class="button" value=" Save Changes " onclick=" return validateECess(document.frmTaxMaster);" />
									<? }?>
									&nbsp;<input type="button" name="cmdCancelTax" id="cmdCancelTax" class="button" value=" cancel " onclick="cancelTax();" />
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10">
									<input type="hidden" name="hidECessRecId" value="<?= $editECessRecId; ?>">
										<input type="hidden" name="eCessRateListId" id="eCessRateListId" value="<?= $eCessRateListId; ?>" readonly="true">
									<td>
								</tr>
								</table>
								<?php
									require("template/rbBottom.php");
								?>
												</TD>
											</TR>
											</table>
						</div>
						<div id="secEduCessDiv" style="display:none;">
						<table width="35%" align="center" cellpadding="0" cellspacing="0" border="0">
									<TR>
									<TD>
										<?php
										$entryHead = "";
										require("template/rbTop.php");
										?>
<table width="200" align="center">
								<tr>
									<td background="images/heading_bg.gif" class="pageName" align="left" width="100%" colspan="3">&nbsp;Sec.Edu Cess</td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>Rate List </td>
									<td>
									<select name="selSecECessRateList" id="selSecECessRateList" onchange="this.form.submit();">
										<option value="">--Select--</option>
<?php
										foreach ($secECessRateListRecs as $recs) {
										$eSecRateListId = $recs[0];
										$secECessRateListName = stripSlash($recs[1]);
										$rlStartDate = dateFormat($recs[2]);
										$displaySecECessRateList = $secECessRateListName . "&nbsp;(" . $rlStartDate . ")";
										$selected = ($secECessRateListId == $eSecRateListId) ? "Selected" : "";
?>
										<option value="<?= $eSecRateListId?>" <?= $selected ?>><?= $displaySecECessRateList ?></option>
<?php
										}
?>
									</select>
									</td>
									<td>
									<input name="cmdDelSecECessRateList" type="submit" class="button" id="cmdDelSecECessRateList" value="Delete Rate List" title="click here to delete the selected rate list" onclick="return cfmDel();" />
									</td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>*Sec.Edu Cess</td>
									<td class="listing-item" align="left">
									<input type="text" id="secECess" name="secECess" value="<?= $secECess; ?>" size="4" style="text-align:right;" onblur="chkSecECessChange();" autocomplete="off">&nbsp;%
									<input type="hidden" id="hidSecECess" name="hidSecECess" value="<?= $secECess; ?>" size="4" style="text-align:right;" readonly="true"/>
									</td>

								</tr>
								<tr>
									<td class="fieldName" nowrap="nowrap">Active</td>
									<td nowrap="true" align="left">
									<input type="checkbox" id="secECessActive" name="secECessActive" onclick="chkSecECessChange();" class="chkbox"
										value="Y" <?= $secActive; ?> />&nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal" class="fieldName"><font size="1">(If Yes, please give tick mark)</font></span>
									<input type="hidden" id="hidSecECessActive" name="hidSecECessActive" value="<?= $secECessActive; ?>" readonly="true"/>
									</td>
								</tr>
								<tr>
									<td colspan="2">
									<fieldset>
										<legend class="listing-item">Rate list</legend>
										<table>
										<tr>
											<td class="fieldName" nowrap title="Sec.Edu Cess Rate list start date">*Start Date</td>
											<td>
											<input type="text" id="secECessStartDate" name="secECessStartDate" 								value="<?= ($selSecECessStartDate) ? dateFormat($selSecECessStartDate) : ""; ?>" autocomplete="off" size="8"/>
											<input type="hidden" id="hidSecECessStartDate" name="hidSecECessStartDate" value="<?= ($selSecECessStartDate) ? dateFormat($selSecECessStartDate) : ""; ?>" autocomplete="off" readonly="true" size="8"/>
											</td>
										</tr>
										</table>
									</fieldset>
									</td>
								</tr>
								<tr  id="secECessRateRow">
									<td colspan="2">
									<fieldset>
										<legend class="listing-item">Rate List Section</legend>
										<table>
										<tr>
											<td class="fieldName" nowrap style="line-height:normal;">*When does the change <br>come into effect?</td>
											<td>
											<select name="secECessEffectType" id="secECessEffectType" onchange="changeSecECessEffectType();">
												<option value="">--Select--</option>
												<option value="SECF">Future</option>
												<option value="SECP">Present</option>
											</select>
											</td>
										</tr>
										<tr id="secECessFutureRow">
											<td class="fieldName" nowrap title="Sec.Edu Cess Rate list start date" >*Start Date </td>
											<td>
											<input type="text" id="secECessSDate" name="secECessSDate" value="" size="8" autocomplete="off">
											</td>
										</tr>
										</table>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10"><td>
								</tr>
								<tr>
									<td colspan="3" align="center">
									<? if ($edit == true && $isAdmin == true) { ?>
									<input type="submit" name="cmdEditSecECess" class="button" value=" Save Changes " onclick=" return validateSecECess(document.frmTaxMaster);" />
									<? }?>
									&nbsp;<input type="button" name="cmdCancelTax" id="cmdCancelTax" class="button" value=" cancel " onclick="cancelTax();" />
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10">
										<input type="hidden" name="hidSecECessRecId" value="<?= $editSecECessRecId; ?>">
										<input type="hidden" name="secECessRateListId" id="secECessRateListId" value="<?= $secECessRateListId; ?>" readonly="true"></td>
								</tr>
								</table>
<?php
										require("template/rbBottom.php");
?>
												</TD>
											</TR>
											</table>
						</div>
						</td>
						</tr>							
												<tr>
												<td colspan="2"  height="20" ></td>
												</tr>
							
												<tr>
												<td colspan="2"  height="10" ></td>
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
<!--table width="200" align="center">
								<tr>
									<td background="images/heading_bg.gif" class="pageName" align="left" width="100%" colspan="3">&nbsp;Basic Excise Duty </td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>Rate List </td>
									<td>
									<select name="selExciseRateList" id="selExciseRateList" onchange="this.form.submit();">
										<option value="">--Select--</option>
<?php
										/*
										foreach ($excCstRateListRecs as $excRecs) {
										$excRateListId = $excRecs[0];
										$excRateListName = stripSlash($excRecs[1]);
										$rlStartDate = dateFormat($excRecs[2]);
										$displayExcRateList = $excRateListName . "&nbsp;(" . $rlStartDate . ")";
										$selected = ($excCstRateListId == $excRateListId) ? "Selected" : "";
										*/
?>
										<option value="<?= $excRateListId ?>" <?= $selected ?>><?= $displayExcRateList ?></option>
<?php
										//}
?>
									</select>
									</td>
									<td>
									<input name="cmdDelExcRateList" type="submit" class="button" id="cmdExcDelRateList" value="Delete Rate List" title="click here to delete the selected rate list "  />
									</td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>*Base CST</td>
									<td class="listing-item" align="left">
									<input type="text" id="excBaseCst" name="excBaseCst" value="<?= $excBaseCst; ?>" size="4" style="text-align:right;" onblur="chkExcCSTChange();" autocomplete="off">&nbsp;%
									<input type="hidden" id="hidExcBaseCst" name="hidExcBaseCst" value="<?= $excBaseCst; ?>" size="4" style="text-align:right;" readonly="true"/>
									</td>

								</tr>
								<tr>
									<td class="fieldName" nowrap="nowrap">Active</td>
									<td nowrap="true" align="left">
									<input type="checkbox" id="excCstActive" name="excCstActive" onclick="chkExcCSTChange();" class="chkbox"
										value="Y" <?= $excActive; ?> />&nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal" class="fieldName"><font size="1">(If Yes, please give tick mark)</font></span>
									<input type="hidden" id="hidExcCstActive" name="hiExcCstActive" value="<?= $excCstActive; ?>" readonly="true"/>
									</td>
								</tr>
								<tr>
									<td colspan="2">
									<fieldset>
										<legend class="listing-item">Rate list</legend>
										<table>
										<tr>
											<td class="fieldName" nowrap title="Excise Rate list start date">*Start Date</td>
											<td>
											<input type="text" id="excStartDate" name="excStartDate" 								value="<?= ($selExcStartDate) ? dateFormat($selExcStartDate) : ""; ?>" autocomplete="off" size="8"/>
											<input type="hidden" id="hidExcStartDate" name="hidExcStartDate" value="<?= ($selExcStartDate) ? dateFormat($selExcStartDate) : ""; ?>" autocomplete="off" readonly="true" size="8"/>
											</td>
										</tr>
										</table>
									</fieldset>
									</td>
								</tr>
								<tr  id="excRateRow">
									<td colspan="2">
									<fieldset>
										<legend class="listing-item">Rate List Section</legend>
										<table>
										<tr>
											<td class="fieldName" nowrap style="line-height:normal;">*When does the change <br>come into effect?</td>
											<td>
											<select name="excEffectType" id="excEffectType" onchange="changeExcEffectType();">
												<option value="">--Select--</option>
												<option value="EF">Future</option>
												<option value="EP">Present</option>
											</select>
											</td>
										</tr>
										<tr id="excFutureRow">
											<td class="fieldName" nowrap title="Excise Rate list start date" >*Start Date </td>
											<td>
											<input type="text" id="excSDate" name="excSDate" value="" size="8" autocomplete="off">
											</td>
										</tr>
										</table>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10"><td>
								</tr>
								<tr>
									<td colspan="3" align="center">
									<input type="submit" name="cmdSaveExcChange" class="button" value=" Save Changes " onclick=" return validateExciseTaxMaster(document.frmTaxMaster);" </>
									</td>
								</tr>
								<input type="hidden" name="hidExcTaxMasterRecId" value="<?= $editExcTaxMasterRecId; ?>">
								<input type="hidden" name="excCstRateListId" id="excCstRateListId" value="<?= $excCstRateListId; ?>" readonly="true">
								<tr>
									<td colspan="3" height="10"><td>
								</tr>
								</table-->
</td>
</tr>
</table>

											<!-- Form fields end   -->

									<SCRIPT LANGUAGE="JavaScript">
									<!--
									Calendar.setup
									(
									{
										inputField  : "startDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "startDate",
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
										inputField  : "sDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "sDate",
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
										inputField  : "excStartDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "excStartDate",
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
										inputField  : "excSDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "excSDate",
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
										inputField  : "eCessSDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "eCessSDate",
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
										inputField  : "eCessStartDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "eCessStartDate",
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
										inputField  : "secECessSDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "secECessSDate",
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
										inputField  : "secECessStartDate",         // ID of the input field
										eventName	  : "click",	    // name of event
										button : "secECessStartDate",
										ifFormat    : "%d/%m/%Y",    // the date format
										singleClick : true,
										step : 1
									}
									);
									//-->
									</SCRIPT>
						<script language="JavaScript" type="text/javascript">
							chkCSTChange();
							//chkExcCSTChange();
							chkECessChange();
							chkSecECessChange();
						</script>
<?
if ($taxType!="") {
?>
<script language="JavaScript" type="text/javascript">
showTax('<?=$taxType?>');
</script>
<? }?>
</form>

<?php
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>