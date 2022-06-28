<?php
	require("include/include.php");
	//require_once ('components/base/LoadingPort_model.php');
	//require_once ('components/base/ExporterMaster_model.php');
	require_once("lib/invoice_ajax.php");	
//	$loadingPort_m	= new LoadingPort_model();
	//$exporter_m		= new ExporterMaster_model();

	ob_start();

	$err = $errDel = $mainId = $containerEntryId = $poEntryId = "";
	$editMode = $addMode = $isSearched = $bankCertMode = $debitNoteMode= false;
	$currencyCode = "";

	$selection = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];
	
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
	if($accesscontrolObj->canReEdit()) $reEdit=true;
	//----------------------------------------------------------	
	
		//$plantRecords	= $plantandunitObj->fetchAllRecordsPlantsActive();
		

	#Cancel 	
	if($p["cmdCancel"]!="")
	{
		$addMode	=	false;
		$editMode	=	false;
		$mainId 			=	$p["mainId"];
		$containerEntryId	=	$p["containerEntryId"];
		$poEntryId 			= 	$p["poEntryId"];		
	}

	$debitNoteEnabled = true;
	$brcEnabled = true;
	//if ($p["debitNoteEditId"]!="" && $p["cmdCancel"]=="") {
	if ($g["debitNoteEditId"]!="" && $p["cmdCancel"]=="") {
		//$debitNoteEditId = $p["debitNoteEditId"];
		$debitNoteEditId = $g["debitNoteEditId"];
		$companyId = $g["companyDetail"];
		$debitNoteMode = true;
		
		$invoiceRec	= $invoiceObj->find($debitNoteEditId);
		//$mainId 	= $invoiceRec[0];		
		$invoiceNo 	= $invoiceRec[1];
		$invDate	= $invoiceRec[2];
		$invoiceDate = dateFormat($invDate);
		$selInvDate = ($invoiceNo=="" || $invoiceNo==0)?date('y-m-d'):$invDate;
		$invYearRange = getFinancialYearRange($selInvDate);

		$exporter			= $invoiceRec[25];		
		$exporterName		= $exporterMasterObj->getExporterName($exporter);
		$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporter);
		$invoiceunitid=$invoiceRec[31];
		$invoiceunitno=$plantandunitObj->find($invoiceunitid);
				$invoiceunitno=$invoiceunitno[1];
		$unitalphacode=$invoiceRec[32];

		//echo "---$invoiceunitid";
		if (($invoiceunitid!="") && ($invoiceunitid!=0))
		{
			//if ($exporterAlphaCode=="FFFPL")
				//{
					//$exporterAlphaCode="FFF";
					$exporterAlphaCode=$unitalphacode;
				//}
		$displayInvNum = $exporterAlphaCode."/"."U-$invoiceunitno/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		}
		else {
		$displayInvNum = $exporterAlphaCode."/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		}

		list($sizeVessalRecs, $vessalDetails, $containerType, $sailingDate, $shippingLine, $shippingCompanyCity, $shippingCompanyAddress) = $invoiceObj->getContVessalRecs($debitNoteEditId);
		$containerRecs = $invoiceObj->getContainerRecs($debitNoteEditId);
		$containerNoArr = array();
		$containerNos = "";
		foreach ($containerRecs as $rec) {
			$containerNo 	= $rec[2];
			$containerNoArr[] = $containerNo;
		}
		if (sizeof($containerNoArr)>0) $containerNos = implode(",",$containerNoArr);

		$billLaddingNo		= $invoiceRec[22];
		//$billLaddingDate	= ($invoiceRec[23]!='0000-00-00')?dateFormat($invoiceRec[23]):"";
		$billLaddingDate	= ($invoiceRec[23]!='0000-00-00')?date('d.m.Y', strtotime($invoiceRec[23])):"";
		
		$purchaseOrderId = $invoiceRec[6];
		list($poNo, $poDate, $dischargePort, $paymentMode, $paymentTerms, $portName, $modeOfCarriage, $finalDestCountry, $otherBuyer) = $invoiceObj->getPurchaseOrderRec($purchaseOrderId);


		$dnRec = $invoiceObj->findDNRec($debitNoteEditId);
		if (sizeof($dnRec)>0)
		{
			$dnFreight		= $dnRec[1];
			$dnBkgFreight	= $dnRec[2];
			$dnExRate		= $dnRec[3];
			$dnTotalBkg		= $dnRec[4];

			$dnGrossAmt		= $dnRec[5];
			$dnTdsAmt		= $dnRec[6];
			$dnNetAmt		= $dnRec[7];
			$dnChqNo		= $dnRec[8];
			$dnChqDate		= dateFormat($dnRec[9]);
		}

		$companyDetail=$billingCompanyObj->find($companyId);
		$companyContactDetailsRecs 			= $billingCompanyObj->findContactdetail($companyId);
			if(sizeof($companyContactDetailsRecs)>0)
			{
				$telephoneNo=''; $mobileNo=''; $fax='';
				foreach($companyContactDetailsRecs as $cdt)
				{
					if($cdt[1]!='')
					{
						if($telephoneNo=='')
						{
							$telephoneNo=$cdt[1];
						}
						else
						{
							$telephoneNo.=','.$cdt[1];
						}
					}
					if($cdt[2]!='')
					{
						if($mobileNo=='')
						{
							$mobileNo=$cdt[2];
						}
						else
						{
							$mobileNo.=','.$cdt[2];
						}
					}
					if($cdt[3]!='')
					{
						if($fax=='')
						{
							$fax=$cdt[3];
						}
						else
						{
							$fax.=','.$cdt[3];
						}
					}
				}
			}

	}

	if ($p["cmdDNSaveChange"]!="") {
		$dnInvoiceMainId	= $p["dnInvoiceMainId"];

		$dnFreight		= trim($p["dnFreight"]);
		$dnBkgFreight	= trim($p["dnBkgFreight"]);
		$dnExRate		= trim($p["dnExRate"]);
		$dnTotalBkg		= trim($p["dnTotalBkg"]);
		$dnGrossAmt		= trim($p["dnGrossAmt"]);
		$dnTdsAmt		= trim($p["dnTdsAmt"]);
		$dnNetAmt		= trim($p["dnNetAmt"]);
		$dnChqNo		= trim($p["dnChqNo"]);
		$dnChqDate		= mysqlDateFormat($p["dnChqDate"]);
		
		if ($dnInvoiceMainId>0 && $dnFreight!="" && $dnExRate!="") {
			
			$updateDebitNote = $invoiceObj->updateDebitNoteRec($dnInvoiceMainId,$dnFreight, $dnBkgFreight, $dnExRate, $dnTotalBkg, $dnGrossAmt, $dnTdsAmt, $dnNetAmt, $dnChqNo, $dnChqDate);

			if ($updateDebitNote) {
				$debitNoteMode = false;
				$sessObj->createSession("displayMsg","Successfully updated debit note");
				$sessObj->createSession("nextPage",$url_afterUpdateInvoice.$selection);
			} else {
				$debitNoteMode = true;
				$err		=	"Failed to update debit note";
			}

		}


	}



	$brcEnabled = false;
	if ($p["bankCertificateEditId"]!="" && $p["cmdCancel"]=="") {	

		$brcInvoiceMainId	= $p["bankCertificateEditId"];		
		$bankCertMode = true;

		$invoiceRec	= $invoiceObj->find($brcInvoiceMainId);
		//$mainId 	= $invoiceRec[0];		
		$brcPOId    = $invoiceRec[6];
		$invoiceNo 	= $invoiceRec[1];
		$invDate	= $invoiceRec[2];
		$invoiceDate = dateFormat($invDate);

		//$invYear = ($invoiceNo=="" || $invoiceNo==0)?date('y'):date('y', strtotime($invDate));
		$selInvDate = ($invoiceNo=="" || $invoiceNo==0)?date('y-m-d'):$invDate;
		$invYearRange = getFinancialYearRange($selInvDate);
			

		$brcFinalDestination	= $invoiceRec[10];
		$brcShipBillNo		= $invoiceRec[20];
		$brcShipBillDate	= $invoiceRec[21];
		$brcBillLaddingNo	= $invoiceRec[22];
		$brcBillLaddingDate	= $invoiceRec[23];
		$exporter			= $invoiceRec[25];		
		$exporterAddress	= $exporter_m->getExporterDetails($exporter, 1);
		
		$exporterAddress = preg_replace('#<br\s*/?>#i', " ", $exporterAddress);
		$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporter);

		$displayInvNum = $exporterAlphaCode."/$invoiceNo/$invYearRange";

		
		$totalValueInUSD = $brcBillAmtUSD = $invoiceRec[18];
		$totalValueInRs	 = $brcBillAmtRs = $invoiceRec[30];


		$brcRec = $invoiceObj->findBRCRec($brcInvoiceMainId);
		if (sizeof($brcRec)>0)
		{
			$brcIECCodeNo			= $brcRec[1];
			$brcDEPBEnrolNo			= $brcRec[2];
			$brcExportBillTo		= $brcRec[3];
			$brcGoodsDescription	= $brcRec[4];
			$brcBillAmt				= $brcRec[5];
			$brcFreightAmt			= $brcRec[6];
			$brcInsuranceAmt		= $brcRec[7];
			$brcCommissionDiscount	= $brcRec[8];
			$brcFreeConvert			= $brcRec[9];
			$brcFOBValue			= $brcRec[10];
			$brcRealisationDate		= ($brcRec[11]!='0000-00-00')?dateFormat($brcRec[11]):"";
			$brcLicenceCategory		= $brcRec[12];
			$brcRefNo				= $brcRec[13];
			$brcRefNoDate			= ($brcRec[14]!='0000-00-00')?dateFormat($brcRec[14]):"";
			$brcFgnExDealerCodeNo	= $brcRec[15];			
			$brcExporterName		= $brcRec[16];
			$brcExportDate			= ($brcRec[17]!='0000-00-00')?dateFormat($brcRec[17]):"";
			$brcCertifyAmtDescr		= $brcRec[18];

			$brcFreightAmtUSD		= $brcRec[19];
			$brcFreightAmtRsPerUSD	= $brcRec[20];
			$brcFreightAmtRs		= $brcRec[21];
			$brcInsuranceAmtUSD		= $brcRec[22];
			$brcInsuranceAmtRsPerUSD	= $brcRec[23];
			$brcInsuranceAmtRs			= $brcRec[24];
			$brcCommissionDiscountUSD	= $brcRec[25];
			$brcCommissionDiscountRsPerUSD	= $brcRec[26];
			$brcCommissionDiscountRs	= $brcRec[27];
			$brcFOBValueUSD	= $brcRec[28];
			$brcFOBValueRs	= $brcRec[29];

		}

		$bankACRecs	= $companydetailsObj->fetchAllBankACRecs();
		

		

		if ($brcBillAmt=="") {

			$poRec = $invoiceObj->getPORec($brcPOId);
			$selCurrencyCode	=  $poRec[9];

			// Get Split amt
			$splitupRecs   = $invoiceObj->fetchAllSplitUpAmt($brcInvoiceMainId);
			$splitupArr = array();
			foreach ($splitupRecs as $rec) {
				$currencyAmt	= $rec[0];
				$rsPerCurrency	= $rec[1];
				$totalRs		= $rec[2];
				$splitupArr[] = $selCurrencyCode.". ".$currencyAmt." @".$rsPerCurrency. " RS. ".$totalRs;
			}
			
			if (sizeof($splitupArr)>0) {
				$brcBillAmt = implode("\n",$splitupArr);
				$brcBillAmt .= "\n\nTotal\n$selCurrencyCode."." $totalValueInUSD"."\nRS. ".$totalValueInRs;
			}
		}

	}

	if ($p["cmdBRCSaveChange"]!="") {
		$brcInvoiceMainId	= $p["brcInvoiceMainId"];

		if ($brcInvoiceMainId>0) {
			
			$brcIECCodeNo	= addSlash(trim($p["brcIECCodeNo"]));
			$brcDEPBEnrolNo	= addSlash(trim($p["brcDEPBEnrolNo"]));			
			$brcExportBillTo	= addSlash(trim($p["brcExportBillTo"]));
			$brcGoodsDescription	= addSlash(trim($p["brcGoodsDescription"]));
			$brcBillAmt	= addSlash(trim($p["brcBillAmt"]));
			$brcFreightAmt	= addSlash(trim($p["brcFreightAmt"]));
			$brcInsuranceAmt	= addSlash(trim($p["brcInsuranceAmt"]));
			$brcCommissionDiscount	= addSlash(trim($p["brcCommissionDiscount"]));
			$brcFreeConvert	= addSlash(trim($p["brcFreeConvert"]));
			$brcFOBValue	= addSlash(trim($p["brcFOBValue"]));
			$brcRealisationDate	= mysqlDateFormat($p["brcRealisationDate"]);
			$brcLicenceCategory	= addSlash(trim($p["brcLicenceCategory"]));
			$brcRefNo			= addSlash(trim($p["brcRefNo"]));
			$brcRefNoDate		= mysqlDateFormat($p["brcRefNoDate"]);
			$brcFgnExDealerCodeNo	= addSlash(trim($p["brcFgnExDealerCodeNo"]));
			$brcExporterName	= addSlash(trim($p["brcExporterName"]));
			$brcExportDate		= mysqlDateFormat($p["brcExportDate"]);
			$brcCertifyAmtDescr	= addSlash(trim($p["brcCertifyAmtDescr"]));
			
			$brcFreightAmtUSD		= trim($p["brcFreightAmtUSD"]);
			$brcFreightAmtRsPerUSD	= trim($p["brcFreightAmtRsPerUSD"]);
			$brcFreightAmtRs		= trim($p["brcFreightAmtRs"]);
			$brcInsuranceAmtUSD		= trim($p["brcInsuranceAmtUSD"]);
			$brcInsuranceAmtRsPerUSD	= trim($p["brcInsuranceAmtRsPerUSD"]);
			$brcInsuranceAmtRs			= trim($p["brcInsuranceAmtRs"]);
			$brcCommissionDiscountUSD	= trim($p["brcCommissionDiscountUSD"]);
			$brcCommissionDiscountRsPerUSD	= trim($p["brcCommissionDiscountRsPerUSD"]);
			$brcCommissionDiscountRs	= trim($p["brcCommissionDiscountRs"]);
			$brcFOBValueUSD	= trim($p["brcFOBValueUSD"]);
			$brcFOBValueRs	= trim($p["brcFOBValueRs"]);




			$updateBRCRec = $invoiceObj->updateBRCRec($brcInvoiceMainId, $brcIECCodeNo, $brcDEPBEnrolNo, $brcExportBillTo, $brcGoodsDescription, $brcBillAmt, $brcFreightAmt, $brcInsuranceAmt, $brcCommissionDiscount, $brcFreeConvert, $brcFOBValue, $brcRealisationDate, $brcLicenceCategory, $brcRefNo, $brcRefNoDate, $brcFgnExDealerCodeNo, $brcExporterName, $brcExportDate, $brcCertifyAmtDescr, $brcFreightAmtUSD, $brcFreightAmtRsPerUSD, $brcFreightAmtRs, $brcInsuranceAmtUSD, $brcInsuranceAmtRsPerUSD, $brcInsuranceAmtRs, $brcCommissionDiscountUSD, $brcCommissionDiscountRsPerUSD, $brcCommissionDiscountRs, $brcFOBValueUSD, $brcFOBValueRs);

			if ($updateBRCRec) {
				$bankCertMode = false;
				$sessObj->createSession("displayMsg",$msg_successBRCUpdate);
				$sessObj->createSession("nextPage",$url_afterUpdateInvoice.$selection);
			} else {
				$editMode	=	true;
				$err		=	$msg_failUpdateInvoice;
			}

		} else {
				$bankCertMode = true;
				$err		=	$msg_failBRCUpdate;
		}

		
	}

	
	
	# Add New
	
	if( $p["cmdAddNew"]!="" && $p["cmdCancel"]==""){
	
		$addMode	=	true;
		/*
		if($p["mainId"]=="" && $p["containerEntryId"]=="" && $p["poEntryId"]=="" && list($mId,$coEId,$poEId) = $invoiceObj->checkBlankRecord())
		{
			list($mId,$coEId,$poEId)	=	$invoiceObj->checkBlankRecord();
			$mainId 			=	$mId; 
			$containerEntryId 	= 	$coEId;
			$poEntryId 			=	$poEId;
			
		} else  {
			if($p["mainId"]=="" && $p["containerEntryId"]=="" && $p["poEntryId"]=="") {
				$tempMainTableRecIns=$invoiceObj->addTempDataMainTable();
				if($tempMainTableRecIns!="") {				
					$mainId	=	$databaseConnect->getLastInsertedId();				
				}
				$tempContainerEntryTableRecIns=$invoiceObj->addTempDataContainerEntryTable($mainId);
				if($tempContainerEntryTableRecIns!="") {				
					$containerEntryId	=	$databaseConnect->getLastInsertedId();				
				}
				
				$tempPOEntryTableRecIns=$invoiceObj->addTempDataPOEntryTable($containerEntryId);
				if($tempPOEntryTableRecIns!="") {				
					$poEntryId	=	$databaseConnect->getLastInsertedId();				
				}				
			} else  {
				$mainId 			=	$p["mainId"];
				$containerEntryId	=	$p["containerEntryId"];
				$poEntryId 			= 	$p["poEntryId"];
			}
		}	
		*/
	}

	
	#New Entry
	if ($p["cmdAdd"]!="" || $p["cmdAddNewContainer"]!="" || $p["cmdAddNewPO"]!="") {
	
		$mainId 			=	$p["mainId"];
		$containerEntryId	=	$p["containerEntryId"];
		$poEntryId 			= 	$p["poEntryId"];
		
		$Date1			=	explode("/",$p["selectDate"]);
		$selectDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
		
		$invoiceNo 		= 	$p["invoiceNo"];
		$selCustomerId	=	$p["selCustomer"];
		
		$selContainer	=	$p["selContainer"];
		$selPOId 		= 	$p["selPOId"];
		
		
		$hidRowRMCount	=	$p["hidRowRMCount"];
		
			
		if( $invoiceNo!="" && $selPOId!="" ) {
			$invoiceMainRecUpdate = $invoiceObj->updateInvoiceMainRec($invoiceNo, $selCustomerId, $selectDate, $mainId);
			$invoiceContainerRecUpdate = $invoiceObj->updateInvoiceContainerRec($selContainer, $containerEntryId);
			$invoicePORecUpdate = $invoiceObj->updateInvoicePORec($selPOId, $poEntryId);
			
			$invoiceEntryRecDel	=	$invoiceObj->deleteInvoiceEntryRec($poEntryId);
		}
		for($i=1; $i<=$hidRowRMCount; $i++) {
			$gradeEntryId	= 	$p["gradeEntryId_".$i];

			if( $gradeEntryId!="" ) {		
				$invoiceDetailsIns = $invoiceObj->addInvoiceDetails($poEntryId,$gradeEntryId);
			}
		}
	
			if($invoiceDetailsIns) {
				if($p["cmdAddNewContainer"]!="") {
					$mainId 	=	$p["mainId"];
					
					$tempContainerEntryTableRecIns=$invoiceObj->addTempDataContainerEntryTable($mainId);
					if($tempContainerEntryTableRecIns!="") {				
						$containerEntryId	=	$databaseConnect->getLastInsertedId();				
					}
					
					$tempPOEntryTableRecIns=$invoiceObj->addTempDataPOEntryTable($containerEntryId);
					if($tempPOEntryTableRecIns!="") {				
						$poEntryId	=	$databaseConnect->getLastInsertedId();				
					}
					
					$addMode = true;
					$selContainer	=	"";
					$p["selContainer"] = "";
					$selPOId 		= 	"";
					$p["selPOId"]	=	"";
					
				} else if($p["cmdAddNewPO"]!="") {
					
					$containerEntryId	=	$p["containerEntryId"];
					$tempPOEntryTableRecIns=$invoiceObj->addTempDataPOEntryTable($containerEntryId);
					$poEntryId = "";
					if($tempPOEntryTableRecIns!="") {				
						$poEntryId	=	$databaseConnect->getLastInsertedId();				
					}
					$addMode = true;
					$selPOId 		= 	"";
					$p["selPOId"]	=	"";
				} else if($p["cmdAdd"]!="") {
					$addMode = false;
				}
				//$sessObj->createSession("displayMsg",$msg_succAddInvoice);
				//$sessObj->createSession("nextPage",$url_afterAddInvoice.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddInvoice;
			}
			$invoiceDetailsIns		=	false;		
	}
	
	
	# Edit Invoice	
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {	

		$editId		= $p["editId"];		
		$editMode	= true;

		$invoiceRec	= $invoiceObj->find($editId);

		$mainId 	= $invoiceRec[0];		
		$invoiceNo 	= $invoiceRec[1];
		$invDate	= $invoiceRec[2];
		$invoiceDate = dateFormat($invDate);
		$selDate	= dateFormat($invoiceRec[5]);		
		$selCustomerId	= $invoiceRec[3];
		$proformaNo	= $invoiceRec[4];
		$invoiceType	= $invoiceRec[8];
		$purchaseOrderId = $invoiceRec[6];
		$preCarrierPlace 	= $invoiceRec[9];
		$finalDestination	= $invoiceRec[10];
		$containerMarks		= $invoiceRec[11];
		$goodsDescription	= $invoiceRec[12];

		$discount		= $invoiceRec[13];
		$discountChk		= ($discount=='Y')?"checked":"";
		$discountRemark		= $invoiceRec[14];
		$discountAmt		= $invoiceRec[15];
		$totNetWt		= $invoiceRec[16];
		$totGrossWt		= $invoiceRec[17];
		$totalValueInUSD	= $invoiceRec[18];
		$confirmedStatus	= $invoiceRec[19];

		if ($confirmedStatus=='Y') {
			$editLog = $invoiceRec[27];
			// Upto the current modified date value
			$cDate = date("Y-m-d");
			$modifiedLog = $cDate.":".$userId.":".$totalValueInUSD.":".$totNetWt.":".$totGrossWt;
			$editHistory = (!empty($editLog))?($editLog."|".$modifiedLog):$modifiedLog; 
			$updateLog = $invoiceObj->updateEditLog($mainId, $editHistory);
			// Set Inv Status
			$setInvStatus = $invoiceObj->setInvoiceStatus($mainId, 'N');
		}

		$shipBillNo			= $invoiceRec[20];
		$shipBillDate		= ($invoiceRec[21]!='0000-00-00')?dateFormat($invoiceRec[21]):"";		
		$billLaddingNo		= $invoiceRec[22];
		$billLaddingDate	= ($invoiceRec[23]!='0000-00-00')?dateFormat($invoiceRec[23]):"";
		$loadingPort		= $invoiceRec[24];
		$exporter			= $invoiceRec[25];
		//echo "The value of $exporter";
		$shipInvRemark		= stripSlash($invoiceRec[26]);			
		$termsDeliveryPayment = $invoiceRec[28];
		$pkgListRemark		= stripSlash($invoiceRec[29]);
		$invoiceunitid=$invoiceRec[31];

		$invoiceunitno=$plantandunitObj->find($invoiceunitid);
				$invoiceunitnoinv=$invoiceunitno[1];	

		$unitalphacode=$invoiceRec[32];
		$euCodeId=$invoiceRec[33];
		$invoiceAlpha=$invoiceRec[34];
		$invoiceNumGen=$invoiceRec[35];
		
		//$invYear = ($invoiceNo=="" || $invoiceNo==0)?date('y'):date('y', strtotime($invDate));
		$selInvDate = ($invoiceNo=="" || $invoiceNo==0)?date('y-m-d'):$invDate;
		$invYearRange = getFinancialYearRange($selInvDate);

		if ($invoiceNo=="" || $invoiceNo==0) {
			$invoicedet = $invoiceObj->getNextInvoiceNo($invoiceType);
			$invoiceNo =$invoicedet[0];
			$invoiceAlpha =$invoicedet[1];
			$invoiceNumGen =$invoicedet[2];
			$invoiceDate = "";
		}		

		list($selCustomerName, $custAddress, $custCountry) = $invoiceObj->getCustomerRec($selCustomerId);
		list($poNo, $poDate, $dischargePort, $paymentMode, $paymentTerms, $portName, $modeOfCarriage, $finalDestCountry, $otherBuyer) = $invoiceObj->getPurchaseOrderRec($purchaseOrderId);
		list($sizeVessalRecs, $vessalDetails, $containerType, $sailingDate, $shippingLine) = $invoiceObj->getContVessalRecs($mainId);

		# Get PO Recs
		$poRecs	= $invoiceObj->getInvoiceItemRecs($mainId);
		
		$totalNumMC = $invoiceObj->getInvoiceRec($mainId);

		$purchaseOrderRec = $invoiceObj->getPORec($purchaseOrderId);
		$currencyCode	=  $purchaseOrderRec[9];
		$selUnitId		=  $purchaseOrderRec[10];
		$unitTxt = ($selUnitId>0)?$spoUnitRecs[$selUnitId]:"";
		

		#Get Container Records
		//$containerRecords	= 	$invoiceObj->getContainerRecords($selCustomerId);	
		//if($selContainer) $purchaseOrderRecords	= $invoiceObj->getPORecords($selContainer, $selCustomerId);	
		//if($selPOId) $pORecords = $invoiceObj->filterPOInvoiceRecs($selPOId,$poEntryId);		
		/*
			$purchaseOrderRecords	= $invoiceObj->getPORecords($selContainer);
			$mode = 1;
			$pORecords 				= $invoiceObj->filterPurchaseOrderRecs($selPOId,$mode, $editInvoiceId);
		*/
		if($exporter=="" && $exporter==0)
		{
			$exporterDefault=$exporterMasterObj->getDefaultExporter();
			if($exporterDefault)
			{
				$exporterAddress	= $exporterMasterObj->getExporterDetails($exporterDefault);
				$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporterDefault);
			}
		}
		else
		{
			$exporterAddress	= $exporterMasterObj->getExporterDetails($exporter);
			$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporter);
		}
		//$unitRecords=$invoiceObj->fetchAllRecordsUnitsActive($exporter);
		//print_r($unitRecords);
	}


	# Update
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveAndConfirm"]) {

		$invoiceConfirmed = 'N';	
		if ($p["cmdSaveAndConfirm"]!="") $invoiceConfirmed = 'Y';

		$mainId 	= $p["mainId"];		
		$selectDate	= mysqlDateFormat($p["selectDate"]);				
		$invoiceNo 	= $p["invoiceNo"];
		$invoiceDate	= mysqlDateFormat($p["invoiceDate"]);
		$preCarrierPlace 	= $p["preCarrierPlace"];	
		$finalDestination	= $p["finalDestination"];
		$containerMarks		= $p["containerMarks"];
		$goodsDescription	= addSlash(trim($p["goodsDescription"]));
		
		$discount		= ($p["discount"]!="")?$p["discount"]:"N";
		$discountRemark		= trim($p["discountRemark"]);
		$discountAmt		= trim($p["discountAmt"]);
		$totNetWt		= trim($p["totNetWt"]);
		$totGrossWt		= trim($p["totGrossWt"]);
		$totalValueInUSD	= trim($p["totalValueInUSD"]);

		$containerRowCount 	= $p["hidTableRowCount"]; // Container Count
		$productRowCount	= $p["hidProductItemCount"];
		
		$shipBillNo	= trim($p["shipBillNo"]);
		$shipBillDate	= ($p["shipBillDate"]!="")?mysqlDateFormat($p["shipBillDate"]):"";
		$billLaddingNo	= trim($p["billLaddingNo"]);
		$billLaddingDate	= ($p["billLaddingDate"]!="")?mysqlDateFormat($p["billLaddingDate"]):"";

		$loadingPort	= $p["loadingPort"];
		$exporter		= $p["exporter"];
		$unitid=$p["unitid"];
		$unitalphacode=$p["unitalphacode"];
		$invoiceAlpha=$p["invoiceAlpha"];
		$invoiceNumGen=$p["invoiceNumGen"];

		$invoiceunitno=$plantandunitObj->find($unitid);
		$invoiceunitnoinv=$invoiceunitno[1];	


		$shipInvRemark	= addSlash(trim($p["shipInvRemark"]));
		$pkgListRemark  = addSlash(trim($p["pkgListRemark"]));

		// Convert full invoice Number
		$expInvNum = "";
		$hidProformaNo	= $p["hidProformaNo"];
		$selInvDate = ($invoiceNo=="" || $invoiceNo==0)?date('y-m-d'):$invoiceDate;
		$invYearRange = getFinancialYearRange($selInvDate);
		$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporter);
		$selInvNum = ($invoiceConfirmed=='Y')?sprintf("%02d",$invoiceNo):"P".$hidProformaNo;


		//New Code
		//$exporter			= $invoiceRec[25];		
		//$exporterName		= $exporter_m->getExporterName($exporter);
		//$exporterAlphaCode	= $exporter_m->getExporterAlphaCode($exporter);
		//$invoiceunitid=$invoiceRec[31];
		//$invoiceunitno=$plantandunitObj->find($invoiceunitid);
		//		$invoiceunitno=$invoiceunitno[1];
		//$unitalphacode=$invoiceRec[32];

		//echo "---$invoiceunitid";
		//if (($invoiceunitid!="") && ($invoiceunitid!=0))
		if (($unitid!="") && ($unitid!=0))
		{
			//if ($exporterAlphaCode=="FFFPL")
				//{
					//$exporterAlphaCode="FFF";
					$exporterAlphaCode=$unitalphacode;
				//}
		//$displayInvNum = $exporterAlphaCode."/"."U-$invoiceunitno/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		$displayInvNum = $exporterAlphaCode."/"."U-$invoiceunitnoinv/".$selInvNum."/$invYearRange";
		}
		else {
		//$displayInvNum = $exporterAlphaCode."/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		$displayInvNum = $exporterAlphaCode."/".$selInvNum."/$invYearRange";
		}

		$expInvNum=$displayInvNum;
		//New Code End
		//if ($exporterAlphaCode!="") $expInvNum = $exporterAlphaCode."/".$selInvNum."/$invYearRange";

		if ($mainId!="") {

			$updateInvoiceRec = $invoiceObj->updateInvoiceRec($mainId, $invoiceConfirmed, $preCarrierPlace, $finalDestination, $containerMarks, $goodsDescription, $discount, $discountRemark, $discountAmt, $totNetWt, $totGrossWt, $totalValueInUSD, $invoiceNo, $invoiceDate, $shipBillNo, $shipBillDate, $billLaddingNo, $billLaddingDate, $loadingPort, $exporter, $shipInvRemark, $pkgListRemark, $expInvNum,$unitid,$unitalphacode,$invoiceAlpha,$invoiceNumGen);

			if ($updateInvoiceRec) {

				# Raw item update				
				for ($i=1; $i<=$productRowCount; $i++) {			
					$rowParentId		= $p["hidRowParentId_".$i];
					$invoiceEntryId	= $p["hidInvoiceEntryId_".$i];
					$productDescr		= addSlash(trim($p["productDescr_".$i]));
					$netWt				= trim($p["netWt_".$i]); 
					$grossWt			= trim($p["grossWt_".$i]); 
					$packingEntryId		= $p["hidPackingEntryId_".$i];
					if($packingEntryId>0) $invoiceEntryId = $packingEntryId ;
					$prodOriginType		= isset($p["prodOriginType_".$i])?$p["prodOriginType_".$i]:"";
				
					if ($invoiceEntryId!=0 && ($productDescr!="" ||  $grossWt!=0)) {
						$updateInvoiceEntryRec = $invoiceObj->updateInvoiceEntryRec($invoiceEntryId, $productDescr, $netWt, $grossWt, $prodOriginType);
					}
					else if($invoiceEntryId == 0 && $productDescr!="" ){						
						$insertInvoiceEntryRec  =  $invoiceObj->insertInvoiceEntryRec($mainId, $productDescr, $rowParentId, $prodOriginType);
					}
				} // Loop Ends

				# Container 
				for ($i=0; $i<$containerRowCount; $i++) {
					$status 	= $p["status_".$i];
					$containerEntryId = $p["containerEntryId_".$i];
					if ($status!='N') {
						$selContainerId	= $p["selContainer_".$i];
						if ($mainId && $containerEntryId=="" && $selContainerId!="")  {
							# Insert in Container Entry
							$insertInvoiceInContainer = $invoiceObj->insertInvoice2Container($mainId, $selContainerId);
						}
					} // status check ends
					else if ($status=='N' && $containerEntryId!="") {
						$delInvoiceFromContainer = $invoiceObj->deleteInvoiceFromContainer($containerEntryId);
					}
					
				} // Loop Ends
			} // Update Ends here
		} // Main If Ends here
		
		if ($updateInvoiceRec) {
			$editMode=false;
			$sessObj->createSession("displayMsg",$msg_succUpdateInvoice);
			$sessObj->createSession("nextPage",$url_afterUpdateInvoice.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateInvoice;
		}
		$invoiceDetailsIns	=	false;
	}

	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$invoiceMainId		=	$p["delId_".$i];
			$invoiceStatus		= $p["invoiceStatus_".$i];

			if ($invoiceMainId!="" && $invoiceStatus=='N') {
				$poId=$invoiceObj->getPurchaseOrderId($invoiceMainId);
				$updatePurchaseOrder=$invoiceObj->updatePurchaseOrderStatus($poId);
				$invoiceEntryRecDel	= $invoiceObj->deleteInvoiceEntryRec($invoiceMainId);
				if ($invoiceEntryRecDel) $invoiceMainRecDel	=	$invoiceObj->deleteInvoiceMainRec($invoiceMainId);
			}
		}
		if ($invoiceEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelInvoice);
			$sessObj->createSession("nextPage",$url_afterDelInvoice.$selection);
		} else {
			$errDel	=	$msg_failDelInvoice;
		}
		$invoiceMainRecDel	=	false;
	}	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------

	# select records between selected date
	if($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		// Month First date and last date	
		$dateC	   =	explode("/", date("d/m/Y"));
		$dateFrom   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],1,$dateC[2]));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1], date('t'), $dateC[2]));	
	}

	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		
		$invoiceRecords = $invoiceObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $invoiceTypeFilter);

		# Fetch All Recs
		$fetchAllInvoiceRecs	= $invoiceObj->fetchAllRecords($fromDate, $tillDate, $invoiceTypeFilter);
		$invoiceRecordsize	= sizeof($fetchAllInvoiceRecs);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllInvoiceRecs);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------	
	//echo "The value of $exporter";
	

	if ($editMode) {
		# List All Container Recs
		$containerRecords = $invoiceObj->fetchAllContainerRecs();

		# List Selected Container Recs
		if ($mainId) $selContainerRecs = $invoiceObj->getContainerRecs($mainId);


		# Port of Loading
		//$loadingPortRecs = $loadingPort_m->findAll(array("order"=>"name asc","where"=>"active=1"));	
		$loadingPortRecs =$loadingPortObj->findAll();
		# Exporter 
		$exporterRecs = $exporterMasterObj->getExporterNameActive();
	}

	if ($addMode || $editMode) {
		#List All Customer Records
			//$customerRecords	= $customerObj->fetchAllRecords();		
	}

	#For Printing  Invoice
	//$distinctInvoiceRecords = $invoiceObj->getDistinctInvoiceRecords($fromDate,$tillDate);

	#Get Container Records
	//$containerRecords	= 	$invoiceObj->getContainerRecords();

	/*
	if ($addMode!="") {
		$invoiceNo = $p["invoiceNo"];		
		$selCustomerId=$p["selCustomer"];		
		#Get Container Records
		$containerRecords	= 	$invoiceObj->getContainerRecords($selCustomerId);		
		$selContainer		=	$p["selContainer"];
		if($selContainer) $purchaseOrderRecords	= $invoiceObj->getPORecords($selContainer, $selCustomerId);		
		$selPOId = $p["selPOId"];		
		if($selPOId) $pORecords = $invoiceObj->filterPurchaseOrderRecs($selPOId,$selContainer);
	}
	*/
	#List All Customer Records
	//$customerRecords		=	$invoiceObj->filterDistictCustomerRecs();

	$shipProdTypeArr = array("AC"=>"Aqua Culture", "SC"=>"Seacaught");


	$companyDet=$billingCompanyObj->getCompanyDrActive();

	# Setting the mode
	$mode = "";
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;

	if ($editMode) $heading	= $label_editInvoice;
	else $heading	= $label_addInvoice;
		
	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/invoice.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<script src="libjs/jquery/jquery-1.7.1.js" type="text/javascript"></script>
<link href="libjs/jquery/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" />
<script src="libjs/jquery/jquery-ui-1.8.23.custom.min.js" type="text/javascript"></script>

<!-- Invoice Amount splitup -->
<div id="Box_Alert" style="display:none;border:1px solid grey;">	
	<table style="width: 100%; border: 0px;" cellpadding="2" cellspacing="0">
            <tr>
         <td colspan="2" style="padding-left: 15px;">
            <span class="fieldName">Total (<span class="siaCurrencyCode"></span>):</span><b><span id="splitupTotalAmt"></span></b>
         </td>
      </tr>
	  <tr>
		<td colspan="2" align="center">
			<table>
				<tr>
					<td>
						<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblSIAItem">
								<tr bgcolor="#f2f2f2" align="center">
											<td class="listing-head">Sl#</td>
											<td class="listing-head"><span class="siaCurrencyCode"></span></td> 
											<td class="listing-head">@INR</td>
											<td class="listing-head">Rs</td>
											<td>&nbsp;</td>
								</tr>
								<tr bgcolor="#ffffff" align="center">
											<td class="listing-head">Total:</td>
											<td class="listing-head">
												<input type="text" name="hdnSIATotCurrency" id="hdnSIATotCurrency" size="12" value="" style="text-align:right;border:none;" readonly />
											</td> 
											<td class="listing-head"></td>
											<td class="listing-head">
												<input type="text" name="hdnSIATotRs" id="hdnSIATotRs" size="12" value="" style="text-align:right;border:none;" readonly />
											</td>
											<td>&nbsp;</td>
								</tr>
						</table>
						<input type="hidden" name="hidTblSIAItemRowCount" id="hidTblSIAItemRowCount" readonly/>
					</td>
				</tr>
				<tr>
					<td style="padding-left:5px;padding-right:10px;">
						<a href="###" id='addRow' onclick="javascript:addNewSIAItem();"  class="link1" title="Click here to add new row."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>		
					</td>
				</tr>
			</table>
		</td>
	  </tr>
      <tr><td height="10"></td></tr>            
      <tr>
         <td colspan="2" style="text-align: center;">
			<input id="btnDBCancel" type="button" value=" Cancel " onclick="return closeSIADialog();" title="Click here to close" style="font-weight:bold;" />
			&nbsp;
            <input id="btnSubmit" type="button" value=" Save " onclick="return SaveSplitAmt();" title="Click here to print invoice" style="font-weight:bold;" />			
			<input type="hidden" name="hdnSplitupTotalAmt" id="hdnSplitupTotalAmt" value=""  />
			<input type="hidden" name="hdnSplitupBalAmt" id="hdnSplitupBalAmt" value=""  />
			<input type="hidden" name="hdnSplitupVal" id="hdnSplitupVal" value=""  />
			<input type="hidden" name="hdnSIAInvoiceId" id="hdnSIAInvoiceId" value=""  />
         </td>
      </tr>
   </table>	
</div>
<!-- Amount Split up ends here-->
<div id="Box_shipInvDialog" style="display:none;border:1px solid grey; ">
   <table cellpadding="2" cellspacing="0" >    
      <tr>
         <td colspan="2" style="padding-left: 15px;">
            <b>Change invoice heading</b>
         </td>
      </tr>
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td colspan="2" style="padding-left: 15px;" align="center">			
            <input type="text" name="txtNewInvoiceHead" id="txtNewInvoiceHead" size="50" />
			<input type="hidden" name="printInvoiceId" id="printInvoiceId"  readonly />
			<br><span style="font-size:12px;">(Leave blank if you wish to use default heading)</span>
         </td>
      </tr>
	  <tr><td height="5"></td></tr>
      <tr>
         <td colspan="2" style="text-align: center;">
			<input id="btnBACancel" type="button" value=" Cancel " onclick="return closePrintDialog();" title="Click here to close" style="font-weight:bold;" />
			&nbsp;
            <input id="btnSubmit" type="button" value=" Print " onclick="return printNewInvoiceHead();" title="Click here to print invoice" style="font-weight:bold;" />
         </td>
      </tr>	 
   </table>
</div>


<!-- Amount Split up ends here-->
<!--Design of pop up in DR -->
<div id="Box_shipInvDialogDr" style="display:none;border:1px solid grey; ">
   <table cellpadding="2" cellspacing="0" align="center">    
      <tr>
         <td colspan="2" style="padding-left: 15px;">
            <b>Select Company </b>
         </td>
      </tr>
     <!-- <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>-->
      <tr>
         <td colspan="2" style="padding-left: 15px;" align="center">
			<select name="companyDet" id="companyDet">
				<option value='0'>--select--</option>
				<?php
				foreach($companyDet as $cd)
				{
					$comId=$cd[0];
					$comName=$cd[1];
					
				?>
				<option value='<?=$comId?>'><?=$comName?></option>
				<?php
				}
				?>
			</select><input type="hidden" name="printDrInvoiceId" id="printDrInvoiceId"  readonly />
           <!-- <input type="text" name="txtNewInvoiceHead" id="txtNewInvoiceHead" size="50" />
			
			<br><span style="font-size:12px;">(Leave blank if you wish to use default heading)</span>-->
         </td>
      </tr>
	  <tr><td height="5"></td></tr>
      <tr>
         <td colspan="2" style="text-align: center;">
			<input id="btnBACancel" type="button" value=" Cancel " onclick="return closePrintDialogDr();" title="Click here to close" style="font-weight:bold;" />
			&nbsp;
            <input id="btnSubmit" type="button" value=" Print " onclick="return printNewDRInvoiceHead();" title="Click here to print invoice" style="font-weight:bold;" />
         </td>
      </tr>	 
   </table>
</div>


<!--Design of pop up in DR -->
<div id="Box_shipInvDialogDrNote" style="display:none;border:1px solid grey; ">
   <table cellpadding="2" cellspacing="0" align="center">    
      <tr>
         <td colspan="2" style="padding-left: 15px;">
            <b>Select Company </b>
         </td>
      </tr>
     <!-- <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>-->
      <tr>
         <td colspan="2" style="padding-left: 15px;" align="center">
			<select name="companyDetail" id="companyDetail">
				<option value='0'>--select--</option>
				<?php
				foreach($companyDet as $cd)
				{
					$comId=$cd[0];
					$comName=$cd[1];
					
				?>
				<option value='<?=$comId?>'><?=$comName?></option>
				<?php
				}
				?>
			</select><input type="hidden" name="printDrNoteInvoiceId" id="printDrNoteInvoiceId"  readonly />
           <!-- <input type="text" name="txtNewInvoiceHead" id="txtNewInvoiceHead" size="50" />
			
			<br><span style="font-size:12px;">(Leave blank if you wish to use default heading)</span>-->
         </td>
      </tr>
	  <tr><td height="5"></td></tr>
      <tr>
         <td colspan="2" style="text-align: center;">
			<input id="btnBACancel" type="button" value=" Cancel " onclick="return closePrintDialogDrNote();" title="Click here to close" style="font-weight:bold;" />
			&nbsp;
            <input id="btnSubmit" type="button" value=" Print " onclick="return printNewDRNoteInvoiceHead();" title="Click here to print invoice" style="font-weight:bold;" />
         </td>
      </tr>	 
   </table>
</div>






<form name="frmInvoice" id="frmInvoice" action="Invoice.php" method="post">
<script src="libjs/jquery/jquery.jeditable.js" type="text/javascript"></script>
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">
			<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
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

												<td align="center">
												<!--<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Invoice.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateShipmentInvoice(document.frmInvoice, '');">-->
												</td>
												<?} else{?>
											  <td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Invoice.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateShipmentInvoice(document.frmInvoice, '');">&nbsp;&nbsp;
											</td>
										<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
											</tr>
											<input type="hidden" name="hidInvoiceId" value="<?=$editInvoiceId;?>">
											<tr>
											  <td nowrap class="export-print-listing-head"></td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="10"></td>
										  </tr>
					<!--<tr>
						  <td colspan="2" style="padding-left:60px;"><table width="200" border="0" align="right">
                                                <tr>
                                                  <td nowrap><input name="cmdAddNewContainer" type="submit" class="button" id="cmdAddNewContainer" style="width:200px;" onclick="return validatePurchaseOrder(document.frmPurchaseOrders);" value="Save &amp; Add New Container">
                                                  &nbsp;&nbsp;
                                                  <input name="cmdAddNewPO" type="submit" class="button" id="cmdAddNewPO" style="width:150px;" onclick="return validatePurchaseOrder(document.frmPurchaseOrders);" value="Save &amp; Add New PO" /></td>
                                                </tr>
                                              </table></td>
					  </tr>-->
					<!--<tr>
						  <td colspan="2" style="padding-left:60px;">&nbsp;</td>
					  </tr>-->
			<tr style="display:none;">
			  <td colspan="2" align="center">
					<table width="75%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td valign="top" align="left">
						<table><tr><td>
						<fieldset>
						<table width="200">
                                                    <tr>
                                                      <td class="export-print-listing-head" nowrap="nowrap">Entry Date</td>
                                                      <td class="listing-item">
								<?=$selDate?> 
					                      <input type="hidden" id="selectDate" name="selectDate" size="8" value="<?=$selDate?>" readonly="true">
							</td>
                                                    </tr>							
						<tr>
                                                      <td class="export-print-listing-head" nowrap="nowrap">Proforma No</td>
                                                      <td class="listing-item"><?=$proformaNo?></td>
                                                    </tr>	
                                                    <!--<tr>
                                                      <td class="export-print-listing-head" nowrap="nowrap">Invoice no </td>
                                                      <td>
								  <input name="invoiceNo" type="text" id="invoiceNo" value="<?=$invoiceNo?>" size="6">
							</td>
                                                    </tr>-->
                                                    <tr>
                                                      <td class="export-print-listing-head" nowrap="nowrap">Customer</td>
                                                      <td class="listing-item"><?=$selCustomerName?>
							  <!--<select name="selCustomer" id="selCustomer">
							  <option value="">-- select --</option>
							<?php
								foreach($customerRecords as $cr) {
									$customerId	= $cr[0];
									$customerName	= stripSlash($cr[2]);
									$selected 	= ($selCustomerId==$customerId)?"Selected":"";
							?>
							<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
								<?php
									 }
								?>
                                                      </select>-->
							</td>
                                                    </tr>
                                                    </table>
						</fieldset>
						</td></tr>
						</table>
						</td>
                                                </tr>
                                              </table>
			</td>
	  </tr>
	<?php
	  if (sizeof($poRecs) > 0) {
	?>
<!-- New Invoice Format Starts here -->
	<tr><td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="print">
          <tr>
            <td width="400" colspan="2" rowspan="3" valign="top">
			<table width="200" border="0" class="tdBoarder">
              <tr>
                <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" nowrap>*Exporter
				&nbsp;
				
						<select name="exporter" id="exporter" onchange="displayExporter();">
							  <option value="">-- Select --</option>
							<?php
								foreach($exporterRecs as $er) {
									//$exporterId		= $er->id;
									//$exporterName	= stripSlash($er->name);
									//$exporterDisplayName = stripSlash($er->display_name);
									//$defaultChk		= $er->default_row;
									$exporterId=$er[0];
									$exporterName=$er[1];
									$exporterDisplayName =$er[2];
									$defaultChk=$er[3];
									$selected 	= (($exporter==$exporterId) || ($defaultChk=='Y' && $exporter=="" && $exporter==0 ))?"Selected":"";
							?>
							<option value="<?=$exporterId?>" <?=$selected?>><?=$exporterDisplayName?></option>
							<?php
								 }
							?>
							</select><?php //echo "the valuei $exporterId";
							$unitRecords=$invoiceObj->fetchAllRecordsUnitsActive($exporter);
							
							?>
				</td>
              </tr>
              <tr>
                <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
				<input type="hidden" name="hidexportunitno" value=""/>
				<strong>
					<span id="exporterAddr"><?=$exporterAddress?></span>
					<span id="exporterAddr1"></span>
					<!--
					<?=$exportAddrArr["ADRH"]?><br><?=$exportAddrArr["ADR1"]?>,<br><?=$exportAddrArr["ADR2"]?>,<br><?=$exportAddrArr["ADR3"]?>
					-->
				</strong>
		</td>
              </tr>
            </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">*Invoice No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center">
				<table>
						<tr>
						<td class="listing-item" nowrap="true">
						
							<span id="invoiceAlphaCode">
							<?php if ( ($invoiceunitid!="") && ($invoiceunitid!=0) ){
								//if ($exporterAlphaCode=="FFFPL")
				//{
					//$exporterAlphaCode="FFF";
					$exporterAlphaCode=$unitalphacode;
				//}
								
								?>
							<?=$exporterAlphaCode?></span>/U-<?=$invoiceunitnoinv?>
							<?php } else {?><?=$exporterAlphaCode?></span><?php }?>
							/
							<input type="text" name="invoiceAlpha" id="invoiceAlpha" value="<?=$invoiceAlpha?>" size="4">
							<input type="hidden" name="invoiceNumGen" id="invoiceNumGen" value="<?=$invoiceNumGen?>" size="4">
							<input name="invoiceNo" id="invoiceNo" type="text" size="6" onKeyUp="xajax_chkInvoiceNoExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$mainId?>', document.getElementById('invoiceDate').value, document.getElementById('hidInvoiceType').value, document.getElementById('exporter').value,document.getElementById('unitid').value);" value="<?=($invoiceNo!=0)?$invoiceNo:"";?>" autocomplete="off" <?=$fieldReadOnly?>/>
							
							<input type="hidden" name="validInvoiceNo" id="validInvoiceNo" value=""> / <?=$invYearRange?>
							<br/>
							<span id="divInvoiceExistTxt" style='line-height:normal; font-size:10px; color:red;'></span>
						
						</td>
						
						<td class="export-print-listing-head" nowrap="true">



						 <select onchange="getUnitAlphacode();" id="unitid" name="unitid">
					<!--<option selected="true" value="T">Taxable</option>
					<option value="S">Sample</option>-->
				<option value=''>--Select--</option>
			<?php
				foreach ($unitRecords as $unitd) {
					$unitId 		= $unitd[0];
					$unitName	= $unitd[1];
					$selectedunitType = ($invoiceunitid==$unitId)?"selected":"";
					
			?>
				<option value='<?=$unitId?>'<?=$selectedunitType?>><?=$unitName?></option>
			<?php
				}
			?>
			<?php
			foreach ($pcRecs as $unitno=>$name) {
				$selPC = ($selProcessCodeId==$pcrId)?"selected":"";
		?>
			<option value="<?=$unitno?>" <?=$selPC?>><?=$name?></option>
		<?php
			}
		?>
				</select>

				<input type="text" name="unitalphacode" id="unitalphacode" value="<?=$unitalphacode?>" readonly="readonly">
						
						</td>
							<td nowrap="true" class="export-print-listing-head">
							&nbsp;&amp;&nbsp;
							<?php
								if ($invoiceDate=="") $invoiceDate = date("d/m/Y");
							?>
							<input type="text" name="invoiceDate" id="invoiceDate" value="<?=$invoiceDate?>" size="8" onchange="xajax_chkValidInvoiceDate(document.getElementById('invoiceDate').value);" autocomplete="off" <?=$fieldReadOnly?>/>
							<input type="hidden" name="validInvoiceDate" id="validInvoiceDate" value="">
							</td>
						</tr>
					</table>
			</td>
                  </tr>
                </table>
	</td>
        <td valign="top">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Exporter's Ref </td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td colspan="2">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
              <tr>
                <td></td>
                <td>				</td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="export-print-listing-head" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">Buyer's Ref.No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;">
					<table>
						<tr>						
						<td class="listing-item" nowrap="true">
							<?=$poNo?>
						</td>
						<td class="export-print-listing-head" nowrap="true">&nbsp;&amp;&nbsp;</td>
							<td nowrap="true" class="listing-item">
								<?=dateFormat($poDate)?>
							</td>
						</tr>
					</table>
			</td>
                  </tr>
                </table></td>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="export-print-listing-head">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="listing-item" align="right" style="padding-right:10px">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
              <tr>
                <td></td>
                <td>				</td>
              </tr>
              
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Other Reference(s)  </td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                </table></td>
                <td>&nbsp;</td>
              </tr>
              </table></td>
          </tr>
          <tr>
            <td width="400" colspan="2" rowspan="2" valign="top">
			<table width="200" border="0" class="tdBoarder">
              <tr>
                <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Consignee</td>
              </tr>
		<tr>
			<td class="listing-item" nowrap="nowrap" height="20" style="padding-left:5px;padding-right:5px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$selCustomerName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" width='350' height="20" colspan="2" style="padding-left:5px;padding-right:5px;font-size:11px;">
						<?=$custAddress?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:5px;padding-right:5px;font-size:11px;">
							<?=$custCountry?>
						</td>
					</tr>
            </table>
		</td>
            <td width="500" colspan="2" style="padding-left:2px;" valign="top">
			<table width="200" border="0" class="tdBoarder">
              <tr>
                <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Buyer (if other than Consignee) </td>
              </tr>
              <tr>
                <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
			<?=$otherBuyer;?>
		</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Country of Origin of Goods </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;">India</td>
                  </tr>
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Country of Final Destination  </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$finalDestCountry?></td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Pre-Carriage by </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;">By <?=$modeOfCarriage?> </td>
                  </tr>
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Place of Receipt by Pre-carrier </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;">
			<input type="text" size="24" name="preCarrierPlace" id="preCarrierPlace" value="<?=$preCarrierPlace?>" />
			</td>
                  </tr>
                </table></td>
            <td colspan="2" rowspan="3" valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Terms of Delivery and payment&nbsp;<span style="font-size:11px;color:black;">(For editing the below text click above the text)</span></td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;">
					<div class="editable_textarea" title="click here to edit text"><?php if (!empty($termsDeliveryPayment)) { echo html_entity_decode($termsDeliveryPayment); } else { ?>C&amp;F&nbsp;<?=$dischargePort?><br>
                    PAYMENT BY <?=$paymentMode?> &nbsp;<?=$paymentTerms?><br>VESSEL <?=$shippingLine?> ON DATE: <?=($sailingDate!="0000-00-00")?date("d.m.Y",strtotime($sailingDate)):""?> 
					<?php
					}	
					?></div>
					</td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Vessel/ Flight No. </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$vessalDetails?></td>
                  </tr>
                </table></td>
            <td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Port of Loading<!-- J.N.P.T, INDIA--></td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;">						
						<select name="loadingPort" id="loadingPort">
							  <option value="">-- Select --</option>
							<?php
								foreach($loadingPortRecs as $lpr) {
									$loadingPortId		= $lpr[0];
									$loadingPortName	= stripSlash($lpr[1]);
									$selected 	= ($loadingPort==$loadingPortId)?"Selected":"";
							?>
							<option value="<?=$loadingPortId?>" <?=$selected?>><?=$loadingPortName?></option>
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
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Port of Discharge </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$dischargePort?></td>
                  </tr>
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Final Destination </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px; padding-right:5px;">
				<input type="text" size="24" name="finalDestination" id="finalDestination" value="<?=(!$finalDestination)?$finalDestCountry:$finalDestination?>" />			
			</td>
                  </tr>
                </table></td>
            </tr>         
          <tr>
            <td colspan="4" style="line-height:normal">
	<table width="100%" align="center" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-color:#FFFFFF">	
	<tr bgcolor="white" align="center">
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt;" width="100">Marks &amp; Nos/ Container No </th>
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt;" width="10%">No. &amp; kind of PKgs </th>
	    	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt;">Description of Goods </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt;" rowspan="2" valign="top">Quantity<br/><br/>In <span class="replaceUnitTxt">KGS</span></th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt;" rowspan="2" valign="top">Gross Wt<br/><br/>In <span class="replaceUnitTxt">KGS</span></th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt;" rowspan="2" valign="top">Rate<br/><br/><span class="replaceCY">US$</span><br/>Per <span class="replaceUnitTxt">KGS</span></th> 
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt;" rowspan="2" valign="top">Amount<br/><br/>C&amp;F <span class="replaceCY">US$</span></th>
	</tr>
	<tr bgcolor="White" align="center">
		<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">
			<table class="tdBoarder">
				<tr><td class="listing-item">
					<input type="text" size="24" name="containerMarks" id="containerMarks" value="<?=$containerMarks?>" />
				</td></tr>
				<?php
				foreach ($selContainerRecs as $rec) {
					$containerNo 	= $rec[2];
					$sealNo		= $rec[4];
				?>
				<tr><td class="listing-item">Container No:<?=$containerNo?></td></tr>
				<tr><td class="listing-item">Seal No:<?=$sealNo?></td></tr>
				<?php
					}
				?>
			</table>
			
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" >			
			<?=$totalNumMC?>&nbsp;Cartons
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt; " align="left" width="50%">
			<textarea name="goodsDescription" id="goodsDescription" rows="4" style="width:80%;"><?=$goodsDescription?></textarea>
		</td>		
	</tr>
	<?php 
		$p = 0;
        $k = 0;
		$totalNetWt = 0;
        $prev ="";
        $rowSpanCount = sizeof($poRecs);
		$euCodeArr = array();
            
		foreach($poRecs as $poi) {
			$p++;
            $k++;
			$next           = $poRecs[$p][2];
			$sPOEntryId 	= $poi[0];
			$sFish 		= $poi[1];
			$sProcessCode 	= $poi[2];
			$sEuCode	= $poi[3];
			$sEuCodeId	 = $poi[28];
			$sEuCodeAddr = $poi[29];
			$euCodeArr[$sEuCode] = $sEuCodeAddr;
 
			$sBrand		= $poi[4];
			$sBrdFrom	= $poi[13];					
			$sGrade	  	= $poi[5];
			$sFreezingStage = $poi[6];
			$sFrozenCode    = $poi[7];
			$sMCPacking	= $poi[8];
			$selPCId	= $poi[14];
			$mcInPO		= $poi[16];
			$mcInInvoice	= $poi[17];
			$totalMC	+= $mcInInvoice;
			$pricePerKg	= $poi[18];
			$valueInUSD	= $poi[19];
			$totalValueInUSD += $valueInUSD;
			$valueInINR	= $poi[20];
			$totalValueInINR += $valueInINR;
			$filledWt	= $poi[21];
			$declWt = $poi[27];
			$weightType = $poi[26];
			$calWt = ($weightType=='NW')?$declWt:$filledWt;
			$calWt = ($selUnitId==2)?number_format((KG2LBS*$calWt),3,'.',''):$calWt;
			$numPacks	= $poi[22];
			$valueInUSD	= $calWt*$numPacks*$mcInInvoice*$pricePerKg;
			$valueInINR	= $valueInUSD * $oneUSD;
			$invoiceRawEntryId = $poi[15];
			
			$disProdItem	= $sProcessCode."&nbsp;".$sEuCode."&nbsp;".$sBrand."&nbsp;".$sGrade."&nbsp;".$sFreezingStage."&nbsp;".$sFrozenCode."&nbsp;".$sMCPacking;

			$qtyInKg	= $calWt*$numPacks*$mcInInvoice;			
			$displayQtyInKg = number_format($qtyInKg,3,'.','');			
			$totalNetWt += $displayQtyInKg;

			$productDescr = stripSlash($poi[23]);
			$grossWt = $poi[25];

			$packingDescRecs = $invoiceObj->getPackingDescription($mainId,$invoiceRawEntryId);
			$packingDescRowId = $packingDescription = $prdOriginType = "";
			if(sizeof($packingDescRecs)>0)	{				
					$packingDescRowId = $packingDescRecs[0][0];
					$packingDescription = $packingDescRecs[0][2];	
					$prdOriginType	= $packingDescRecs[0][4];	
			} 
			$invoiceunitid=$poi[30];
			$invoiceunitno=$plantandunitObj->find($invoiceunitid);
				$invoiceunitnoinv=$invoiceunitno[1];	

			$unitalphacode=$poi[31];

			//echo "The unit id is $invoiceunitnoinv";
	?>
  <tr bgcolor="#FFFFFF">
        <?php
        if($p==1)   {
			//$prev = $sProcessCode;
         ?>
            <td id="invoiceItemSpan" height='100px;' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" rowspan="<?=$rowSpanCount;?>"></td>
        <?php
        }
        ?>
	
        <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="center"><?=$mcInInvoice?>&nbsp;Cartons</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap">
		<?=$disProdItem?><br/>
         <input type="hidden" name="hidRowParentId_<?=$k?>" id="hidRowParentId_<?=$k?>" value=""/>
		 <input type="hidden" name="prodOriginType_<?=$k?>" id="prodOriginType_<?=$k?>" value=""/>
		<input type="hidden" name="hidInvoiceEntryId_<?=$k?>" id="hidInvoiceEntryId_<?=$k?>" value="<?=$invoiceRawEntryId?>" />
		<input type="text" name="productDescr_<?=$k?>" id="productDescr_<?=$k?>" style="width:80%;" value="<?=($productDescr!="")?$productDescr:$disProdItem?>" />
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right">
		<?=$displayQtyInKg;?>
		<input type="hidden" name="netWt_<?=$k?>" id="netWt_<?=$k?>" value="<?=$displayQtyInKg;?>" size="10" style="text-align:right;" readonly />
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap">
		<input type="text" name="grossWt_<?=$k?>" id="grossWt_<?=$k?>" value="<?=($grossWt!=0)?$grossWt:''?>" size="10" style="text-align:right;" onkeyup="calcTotGrossWt();" />
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=number_format($pricePerKg,3,'.','');?></td>
       <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right">
			<?=number_format($valueInUSD,2,'.','');?>
			<input type="hidden" name="valueInUSD_<?=$k?>" id="valueInUSD_<?=$k?>" value="<?=number_format($valueInUSD,2,'.','');?>" />
	</td>
  </tr>
  <?php		
  if(($prev=="" && $sProcessCode!=$next) || ($prev!="" && $sProcessCode!=$next))    {
      $k++;
      $rowSpanCount++;    
  ?>
  <tr bgcolor="#FFFFFF" style="padding:2px 0px 2px 0px">      
        <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;border-bottom:solid 1px" align="center" nowrap="nowrap"></td>
	<td class="listing-item" style="padding:2px 5px 2px 5px; font-size:8pt;border-bottom:solid 1px" nowrap="nowrap">
				 <input type="hidden" name="hidPackingEntryId_<?=$k?>" id="hidPackingEntryId_<?=$k?>" value="<?=($packingDescRowId>0)?$packingDescRowId:0 ;?>" />
                <input type="hidden" name="hidInvoiceEntryId_<?=$k?>" id="hidInvoiceEntryId_<?=$k?>" value="0" />
                <input type="hidden" name="hidRowParentId_<?=$k?>" id="hidRowParentId_<?=$k?>" value="<?=$invoiceRawEntryId?>" />
				<table cellpadding="0" cellspacing="0" class="tdBoarder" width="100%">
					<tr>
						<td class="listing-item">Packing:</td>
						<td>
								<textarea style="width:283px;" rows="2" id="productDescr_<?=$k;?>" name="productDescr_<?=$k;?>"><?=$packingDescription;?></textarea>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td class="listing-item">Product Type:</td>
						<td>
								<select name="prodOriginType_<?=$k;?>" id="prodOriginType_<?=$k;?>" onchange="displaySCMsg(this.value,'SCMsg_<?=$k;?>');">
									<option value="">--Select--</option>
									<?php
									foreach ($shipProdTypeArr as $key=>$value) {
											$selected = ($prdOriginType==$key)?"selected":"";
									?>
									<option value="<?=$key?>" <?=$selected?>><?=$value?></option>
									<?php
									}	
									?>
								</select>
						</td>
					</tr>
					<?php
					$scMsgStyle= "display:none;";
					if ($prdOriginType=='SC') $scMsgStyle= "";					
					?>
					<tr id="SCMsg_<?=$k;?>" style="<?=$scMsgStyle?>">
						<td class="listing-item" colspan="2">CAUGHT IN INDIAN OCEAN FAO AREA ZONE 51</td>						
					</tr>
				</table>
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;border-bottom:solid 1px" align="right">
		<input type="hidden" name="netWt_<?=$k?>" id="netWt_<?=$k?>" value="0" size="10" style="text-align:right;" readonly />
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;border-bottom:solid 1px" nowrap="nowrap">
		<input type="hidden" name="grossWt_<?=$k?>" id="grossWt_<?=$k?>" value="0'' size="10" style="text-align:right;" onkeyup="calcTotGrossWt();" />
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;border-bottom:solid 1px" align="right"></td>
        <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;border-bottom:solid 1px  " align="right">
		<input type="hidden" name="valueInUSD_<?=$k?>" id="valueInUSD_<?=$k?>" value="0" />
	</td>
  </tr>

		<?php
  }
  ?>
  <?php
  $prev = $sProcessCode;
  }	?>
	<input type="hidden" name="hidProductItemCount" id="hidProductItemCount" value="<?=$k?>" />
	<tr bgcolor="#FFFFFF" id="discountRow"> <!--style="display:none;"-->
	    <td height='30' colspan="3" align="center" style="padding-left: 175px;" class="listing-head">
			<!--Discount-->Remark:
			<input type="text" name="discountRemark" id="discountRemark" size="38"  value="<?=$discountRemark?>" />
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	    <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
			<input type="text" name="discountAmt" id="discountAmt" size="6" value="<?=$discountAmt?>" style="text-align:right;" onkeyup="calcTotalUSDAmt();" />
		</td>
	  </tr>
	<tr bgcolor="#FFFFFF">
		<td height='30' colspan="3" align="center" style="padding-left: 175px;">
			<table class="tdBoarder">
				<!--tr><td class="listing-item" colspan="2" align="left">Brought Forward</td></TR-->
				<tr>
					<td class="listing-item" align="left">Total Net Weight:</td>
					<td class="listing-item">
						<input type="hidden" name="totNetWt" id="totNetWt" size="10" value="<?=$totalNetWt?>" />
						<strong><?=number_format($totalNetWt,3,'.','');?>&nbsp; <span class="replaceUnitTxt">KGS</span></strong>
					</td>
				</tr>
				<tr>
					<td class="listing-item" align="left">Total Gross Weight:</td>
					<td class="listing-item">
						<strong>
							<input type="text" name="totGrossWt" id="totGrossWt" size="10" value="<?=$totGrossWt?>" style="text-align:right; font-weight:bold; border:none" readonly />&nbsp; <span class="replaceUnitTxt">KGS</span></strong>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table class="tdBoarder">
						<tr>
							<td class="listing-item" align="left">Shp/Bl No:</td>
							<td class="listing-item">
								<input name="shipBillNo" id="shipBillNo" type="text" size="6" maxlength="7" value="<?=$shipBillNo?>" />
							</td>
							<td class="listing-item" align="left">Dated</td>
							<td class="listing-item" align="left">
								<input name="shipBillDate" id="shipBillDate" type="text" size="8" value="<?=$shipBillDate?>" />
							</td>
						</tr>
						<tr>
							<td class="listing-item" align="left" title="Bill of Ladding">BL No:</td>
							<td class="listing-item">
								<input name="billLaddingNo" id="billLaddingNo" type="text" size="10" value="<?=$billLaddingNo?>" />
							</td>
							<td class="listing-item" align="left">Dated</td>
							<td class="listing-item" align="left">
								<input name="billLaddingDate" id="billLaddingDate" type="text" size="8" value="<?=$billLaddingDate?>" />
							</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>	
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	    <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
			&nbsp;
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
	    <td height='30' colspan="5" align="right">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" nowrap>Amount in Words  </td>
                    <td class="listing-head" nowrap align="right" valign="bottom" rowspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px;">C&amp;F <span class="replaceCY">US$</span> 
						<?php /* $input = ceil($totalValueInUSD); echo convert($input);*/ echo makewords($totalValueInUSD);?> Only
					</td>
                 </tr>
				<tr>
					<td colspan="2">
						<table>
							<tr>
								<td class="listing-head">MANUFACTURER/PROCESSOR/PACKER:</td>								
							</tr>
							<tr>
								<td class="listing-item" style="padding-left:5px;">
									<?php										
										//$euCodeResultArr = array_unique($euCodeArr);		
										echo implode(",",array_values($euCodeArr))
									?>
								</td>
							</tr>
							<tr>
								<td class="listing-item" style="padding-left:5px;">
									<span class="listing-head">EIC APPROVAL NO:</span>
									<?php										
										//echo implode(",",array_keys($euCodeArr))
										echo $euCodeId;
									?>
								</td>
							</tr>
						</table>						
					</td>
				</tr>
        </table>
		</td>
		<td class="listing-head" nowrap align="right" valign="middle">Total:</td>
	    <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right" valign="middle">
			<input type="text" name="totalValueInUSD" id="totalValueInUSD" size="10" value="<?=number_format($totalValueInUSD,2,'.','');?>" style="text-align:right; font-weight:bold; border:none;" readonly="true" />
		</td>
	    </tr>
	     </table></td>
            </tr>
          <tr style="display:none;">
            <td colspan="4">
		<table width="100%" align="center" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-color:#FFFFFF">
	  <tr bgcolor="#FFFFFF">
	    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td colspan="2" nowrap class="listing-item">SEA CAUGHT PRODUCT. CAUGHT IN INDIAN OCEAN AREA FAO NO.51<BR>MANUFACTURER/PROCESSOR/PACKER:<BR><?= $companyArr["Name"];?><BR>
				<?=$addr["ADR2"];?></td>
                    </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="listing-item"><table width="300" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="export-print-listing-head" style="line-height:normal">Declaration:</td>
                  </tr>
                  <tr>
                    <td class="listing-item">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct </td>
                  </tr>
                </table>                    </td>
                    <td class="listing-item" valign="top"><table width="200" align="right" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="listing-item" nowrap><strong>For <?=$companyArr["Name"];?></strong><br><br><br><br><div align="right">Authorised Signatory</div></td>
                  </tr>
                </table></td>
                  </tr>
                </table></td>
	    </tr>
	     </table></td>
            </tr>
        </table>
	</td></tr>
 <? }?>
<!-- New Invoice Format Ends here -->
	<tr><td height="10"></td></tr>	
<tr><td height="10"></td></tr>
<tr><td colspan="2" style="padding-left:5px; padding-right:5px;">
<table>		
	<tr>
	<td valign="top">
		<fieldset>
						<table width="200">
                                                    <tr>
                                                      <td class="export-print-listing-head" nowrap="nowrap">Entry Date</td>
                                                      <td class="listing-item">
								<?=$selDate?> 
					                      <input type="hidden" id="selectDate" name="selectDate" size="8" value="<?=$selDate?>" readonly="true">
							</td>
                                                    </tr>							
						<tr>
                                                      <td class="export-print-listing-head" nowrap="nowrap">Proforma No</td>
                                                      <td class="listing-item"><?=$proformaNo?><input type="hidden" id="hidProformaNo" name="hidProformaNo"  value="<?=$proformaNo?>" readonly /></td>
                                                    </tr>                                                  
                                                    <tr>
                                                      <td class="export-print-listing-head" nowrap="nowrap">Customer</td>
                                                      <td class="listing-item"><?=$selCustomerName?></td>
                                                    </tr>
							<tr>
								<td class="export-print-listing-head">Discount</td>
								<td>
									<input type="checkbox" name="discount" id="discount" value="Y" class="chkBox" onclick="showDiscount();" <?=$discountChk?> />
								</td>	
							</tr>	
                                                    </table>
						</fieldset>
	</td>
	<td valign="top">
	<fieldset><legend class="listing-item">Container</legend>
	<table>
		<tr>
	<td style="padding-left:10px;padding-right:10px;">
		<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblPOItem">
	                <tr bgcolor="#f2f2f2" align="center">
                                <td class="listing-head">Sr.<br>No</td> 
				<td class="listing-head">Container</td>
				<td>&nbsp;</td>
                        </tr>
	<?php
		// When Edit Mode Products are loading from Top function		
		$k = 0;
		$selContainerSize = sizeof($selContainerRecs);
		foreach ($selContainerRecs as $rec) {
			$containerId = $rec[0];
			$containerNo = $rec[2];
			$containerEntryId = $rec[3];
	?>
<tr align="center" class="whiteRow" id="row_<?=$k?>">
      <td align="center" id="srNo_<?=$k?>" class="listing-item">
         <?=$k+1?> 
      </td>
      <td align="center" class="listing-item" align="left">
		<?=$containerNo?>
		<input type="hidden" name="selContainer_<?=$k?>" id="selContainer_<?=$k?>" value="<?=$containerId?>" />
      </td>
      <td nowrap="" align="center" class="listing-item">
        <a onclick="setRowItemStatus('<?=$k?>');" href="###"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a><input type="hidden" value="" id="status_<?=$k?>" name="status_<?=$k?>"/><input type="hidden" value="N" id="IsFromDB_<?=$k?>" name="IsFromDB_<?=$k?>"/><input type="hidden" id="containerEntryId_<?=$k?>" name="containerEntryId_<?=$k?>" value="<?=$containerEntryId?>" />
      </td>
</tr>	
<?php
		$k++;		
	} // Container Loop Ends here
?>	
		</table>	
	</td>
</tr>
<!--  Hidden Fields-->
<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$selContainerSize;?>">
<!--  Container Dynamic Row Ends Here-->
<tr><td height="5"></td></tr>
<tr>
	<td style="padding-left:10px;padding-right:10px;">
		<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>		
	</td>
</tr>
	</table>	
	</fieldset>
	</td>	
	<td valign="top" style="padding-left:5px;padding-right:5px;">
			<table>
				<TR>
					<TD class="export-print-listing-head" nowrap="nowrap" title="Remark for Invoice">
						Remark
					</TD>
					<td>
						<textarea name="shipInvRemark" id="shipInvRemark" rows="4" style="height: 100px; width: 250px;"><?=$shipInvRemark?></textarea>
					</td>
				</TR>
			</table>
	</td>
	<td valign="top" style="padding-left:5px;padding-right:5px;">
			<table>
				<TR>
					<TD class="export-print-listing-head" nowrap="nowrap" title="Remark for packing list printing">
						Packing <br>Remark
					</TD>
					<td>
						<textarea name="pkgListRemark" id="pkgListRemark" rows="4" style="height: 100px; width: 250px;"><?=$pkgListRemark?></textarea>
					</td>
				</TR>
			</table>
	</td>
	</tr>
</table>
</td></tr>	
											<tr>
											  <td align="center">&nbsp;</td>
											  <td align="center">&nbsp;</td>
										  </tr>
											<tr>
												<? if($editMode){?>

											<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Invoice.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateShipmentInvoice(document.frmInvoice, '');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" Save & Confirm " onClick="return validateShipmentInvoice(document.frmPurchaseOrder, '1');" style="width:110px" />
											</td>
												<? } else{?>

											<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Invoice.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Save &amp; Exit " onClick="return validateShipmentInvoice(document.frmInvoice, '');">
												&nbsp;&nbsp;
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
			# Listing Starts
		?>
<!-- 
Debit Note Starts here
-->
<?php
	# Debit note Mode
	if ($debitNoteMode) {
?>
<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Debit Note</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>											
											<input type="hidden" name="dnInvoiceMainId" id="dnInvoiceMainId" value="<?=$debitNoteEditId;?>" readonly />										
											<tr>
											  <td colspan="2" height="10"></td>
										  </tr>	
	<tr>
		<td colspan="2" style="padding-left:10px; padding-right:10px;" align="center">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f2f2f2" align="center">
   <tr bgcolor="white">
    <td align="center" class="listing-head" colspan="17"><font size="6"><?=$companyDetail[1]?> <?php /*$debitNoteArr["Name"]*/ ?></font></td>
  </tr>
  <tr bgcolor="white">
    <td align="LEFT" class="listing-head" colspan="17">&nbsp;</td>
  </tr>
  <tr bgcolor="white">
    <td align="center" class="listing-item" colspan="17" style="text-transform:uppercase"><?=$companyDetail[2].','.$companyDetail[3].','.$companyDetail[4].','.$companyDetail[5]?></td>
  </tr>
  <tr bgcolor="white">
    <td align="center" class="listing-item" colspan="17" style="text-transform:uppercase">
	<? if($telephoneNo!=''){ echo "Tel:".$telephoneNo; }else { echo ''; } ?> 
	<? if ($telephoneNo!='' && $fax!='') { echo ", "; } ?>
	<? if($fax!=''){ echo "FAX: ".$fax;} else { echo ''; } ?>
	<? if ($mobileNo!='' && $fax!='') { echo "<br/>"; } ?>
	<? if($mobileNo!=''){ echo "Cell: ".$mobileNo;} else { echo ''; } ?>
	<?/*=$debitNoteArr["CONTACT_NUMBER"]*/?></td>
  </tr>
  <tr bgcolor="white">
    <td align="RIGHT" class="listing-head" colspan="17"></td>
  </tr>
</table>
		</td>
	</tr>
	<tr><td height="30"></td></tr>
	<tr>
		<td colspan="2" style="padding-left:10px; padding-right:10px;">
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">PAN NO:</td>
								<td class="export-listing-item"><?=$companyDetail[18]?><!--AAAFN9648P--></td>
							</tr>			
						</table>
					</td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">DEBIT NOTE NO:</td>
								<td class="export-listing-item">NB/<?=$displayInvNum?></td>
							</tr>			
							<tr>
								<td class="export-print-listing-head">DATED:</td>
								<td class="export-listing-item"><?=($invDate!='0000-00-00')?date('d.m.Y', strtotime($invDate)):""?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">To,</td>
								<td></td>
							</tr>	
							<tr>								
								<td colspan="2" class="export-listing-item">M/S. <?=$shippingLine?>
								<?php if ($shippingCompanyAddress!="") {?><br><?=$shippingCompanyAddress?><?}?>
								<br><?=$shippingCompanyCity?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">Dear Sir</td>
								<td></td>
							</tr>	
							<tr>
								<td colspan="2" class="export-listing-item">Being freight brokerage on account of shipment of container A/C<br>M/S <?=$exporterName?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td >
									<table cellpadding="4" cellspacing="0" border="0" style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000" >
										<tr>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head">Vessel Name</td>
											<td class="export-listing-item"><?=$vessalDetails?></td>
										</tr>
										<tr>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head">Container No</td>
											<td class="export-listing-item"><?=$containerNos?></td>
										</tr>
										<tr>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head">B/L No and Date</td>
											<td class="export-listing-item">
											<?
												if (!preg_match("/^[0]*$/",trim($billLaddingNo))) {
													echo $billLaddingNo." DTD ".$billLaddingDate;
												}
											?>
											</td>
										</tr>
									</table>
								</td>
							</tr>							
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td >
									<table cellpadding="5" cellspacing="0" border="0" style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000" >
										<tr align="center">
											<td height="35" style="border-right:1px solid #000000;" class="export-print-listing-head">Exp Invoice No</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head">PORT</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head">FREIGHT</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head">BKG (2%)</td>
											<td style="border-right:1px solid #000000;" class="export-print-listing-head">Ex.Rate</td>
											<td class="export-print-listing-head" style="border-right:1px solid #000000;">BROKERAGE</td>
											<td class="export-print-listing-head" style="border-right:1px solid #000000;">GROSS</td>
											<td class="export-print-listing-head" style="border-right:1px solid #000000;">TDS</td>
											<td class="export-print-listing-head" style="border-right:1px solid #000000;">NET</td>
											<td class="export-print-listing-head" style="border-right:1px solid #000000;">CHQ NO</td>
											<td class="export-print-listing-head">DATE</td>
										</tr>										
										<tr>
											<td style="border-right:1px solid #000000;" class="export-listing-item"><?=$displayInvNum?></td>
											<td style="border-right:1px solid #000000;" class="export-listing-item"><?=$dischargePort?></td>
											<td style="border-right:1px solid #000000;" class="export-listing-item">
												<input type="text" name="dnFreight" id="dnFreight" size="10" style="text-align:right" onKeyUp="calcDNBkgFreight();" value="<?=($dnFreight!=0)?$dnFreight:"";?>" autocomplete="off">
											</td>
											<td style="border-right:1px solid #000000;" class="export-listing-item">
												<input type="text" name="dnBkgFreight" id="dnBkgFreight" size="5" style="text-align:right;border:none;" readonly value="<?=$dnBkgFreight?>">
											</td>
											<td style="border-right:1px solid #000000;" class="export-listing-item">
												<input type="text" name="dnExRate" id="dnExRate" size="5" style="text-align:right" onKeyUp="calcDNBkg();" value="<?=($dnExRate!=0)?$dnExRate:"";?>" autocomplete="off">
											</td>
											<td class="export-listing-item" style="border-right:1px solid #000000;">
												<input type="text" name="dnTotalBkg" id="dnTotalBkg" size="10" style="text-align:right;border:none;" readonly value="<?=$dnTotalBkg?>">
											</td>
											<td class="export-listing-item" style="border-right:1px solid #000000;">
												<input type="text" name="dnGrossAmt" id="dnGrossAmt" size="10" style="text-align:right;" value="<?=$dnGrossAmt?>" onKeyUp="calcDNNetAmt();" autocomplete="off" >
											</td>
											<td class="export-listing-item" style="border-right:1px solid #000000;">
												<input type="text" name="dnTdsAmt" id="dnTdsAmt" size="10" style="text-align:right;" value="<?=$dnTdsAmt?>" onKeyUp="calcDNNetAmt();" autocomplete="off">
											</td>
											<td class="export-listing-item" style="border-right:1px solid #000000;">
												<input type="text" name="dnNetAmt" id="dnNetAmt" size="10" style="text-align:right;border:none;" value="<?=$dnNetAmt?>" autocomplete="off" readonly />
											</td>
											<td class="export-listing-item" style="border-right:1px solid #000000;">
												<input type="text" name="dnChqNo" id="dnChqNo" size="10" value="<?=$dnChqNo?>" >
											</td>
											<td class="export-listing-item">
												<input type="text" name="dnChqDate" id="dnChqDate" size="10" value="<?=$dnChqDate?>">
											</td>
										</tr>
									</table>
								</td>
							</tr>							
						</table>
					</td>
				</tr>				
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-print-listing-head">Rupees: </td>
								<td class="export-listing-item">
								<?
								if ($dnTotalBkg>0) {
									$cExToWords = "";
										list($ceNum, $ceDec) = explode(".", $dnTotalBkg); 
										$cExToWords .= convertNum2Text($ceNum);
										if($ceDec > 0) {
											$cExToWords .= " Paise ";
											$cExToWords .= convertNum2Text($ceDec);
										}										
										echo ucfirst($cExToWords)." only";	
								}
								?>
								</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">KINDLY RELEASE BROKERAGE AT EARLIEST</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">THANKING YOU</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">YOURS TRULY</td>
							</tr>								
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item">For <?=$companyDetail[9]?><!--For NAIR BROTHERS--></td>
							</tr>								
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
							
								<tr>												
									<td align="center">
										<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Invoice.php');">&nbsp;&nbsp;
										<input type="submit" name="cmdDNSaveChange" id="cmdDNSaveChange" class="button" value=" Save Changes " onClick="return validateDebitNote();">
									</td>
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
			# Listing Starts
		?>


<!-- 
Debit Note Ends here
-->

<!-- 
	Bank Certificate Starts Here
-->
	<?php
		/*			
		* =========================================================Bank Certificate==================================================			
		*/
		if ($bankCertMode) {
	?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Modify Bank Certificate </td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>											
											<input type="hidden" name="brcInvoiceMainId" id="brcInvoiceMainId" value="<?=$brcInvoiceMainId;?>" readonly />										
											<tr>
											  <td colspan="2" height="10"></td>
										  </tr>	
	<tr>
		<td colspan="2" style="padding-left:10px; padding-right:10px;" align="center">
			<DIV id="bankCertDiv" style="width: auto;">
<TABLE cellpadding=0 cellspacing=0 class="brcTbl0">
<TR class="brcTr0">
	<TD class="brcTd0">&nbsp;</TD>
	<TD class="brcTd1">APPENDIX 22A</TD>
</TR>
<TR class="brcTr1">
	<TD class="brcTd0">BANK CERTIFICATE OF EXPORT AND REALISATION.</TD>
	<TD class="brcTd2">FORM NO. 1</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd0"><FONT class="brcFt0">Note:Please see chapter 4 and 5 of the Import Export Policy Hand book</FONT></TD>
	<TD class="brcTd3">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd0">&nbsp;</TD>
	<TD class="brcTd2">IEC CODE NO:<input tyepe="text" name="brcIECCodeNo" id="brcIECCodeNo" size="12" value="<?=($brcIECCodeNo!="")?$brcIECCodeNo:"0392068460"?>" style="border:none;" readonly /></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd0">&nbsp;</TD>
	<TD class="brcTd4">DEPB ENROLMENT NO.<input tyepe="text" name="brcDEPBEnrolNo" id="brcDEPBEnrolNo" size="26" value="<?=($brcDEPBEnrolNo!="")?$brcDEPBEnrolNo:"03/DEPB/235/ALS/VII/AM/99"?>" style="border:none;" readonly /></TD>
</TR>
</TABLE>
<P class="brcP0"><FONT class="brcFt1">TO,THE JOINT DIRECTOR GENERAL OF FOREIGN TRADE,MUMBAI</FONT></P>
<P class="brcP1"><FONT class="brcFt1">We, <?=$exporterAddress?></FONT><FONT class="brcFt2">hereby declare thatwehave forwarded a documentary export bill to:&nbsp;
	<select name="brcExportBillTo" id="brcExportBillTo" onchange="xajax_getBankAC(this.value);">
		<option value="">--Select--</option>
		<?php
		foreach ($bankACRecs as $bcb) {
			$bankACEntryId 	= $bcb[0];
			$accountNo		= $bcb[1];
			$bankName 		= $bcb[2];			
			$bankAddress	= $bcb[3];
			$bankADCode		= $bcb[4];
			$selected = ($brcExportBillTo==$bankACEntryId)?"selected":"";
		?>
		<option value="<?=$bankACEntryId?>" <?=$selected?>><?=$bankName?></option>
		<?php
		}	
		?>
	</select>&nbsp;for Collection/Negotiation/Purchase as per particulars given hereunder</FONT></P>
<TABLE cellpadding=0 cellspacing=0 class="brcTbl1">
<TR class="brcTr2">
	<TD colspan=3 class="brcTd5"><FONT class="brcFt3">1.INVOICE NO.:&nbsp;<?=$displayInvNum?></FONT></TD>
	<TD class="brcTd6">&nbsp;</TD>
	<TD class="brcTd7">&nbsp;</TD>
	<TD class="brcTd8">&nbsp;</TD>
	<TD class="brcTd9"><FONT class="brcFt4">2.</FONT>DATE:&nbsp;<?=($invDate!='0000-00-00')?date('d.m.Y', strtotime($invDate)):""?></TD>
</TR>
<TR class="brcTr3">
	<TD colspan=3 class="brcTd10"><FONT class="brcFt5">3.EXPORT PROMOTION COPY OF S/BILL</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd14">&nbsp;</TD>
</TR>
<TR class="brcTr4">
	<TD colspan=3 class="brcTd15"><FONT class="brcFt6">DULY AUTHENTICATED BY CUSTOM NO.:</FONT></TD>
	<TD class="brcTd16">&nbsp;</TD>
	<TD class="brcTd17"><FONT class="brcFt6"><?=$brcShipBillNo?></FONT></TD>
	<TD class="brcTd18">&nbsp;</TD>
	<TD class="brcTd19"><FONT class="brcFt7">4.</FONT><FONT class="brcFt6">DATE:&nbsp;<?=($brcShipBillDate!='0000-00-00')?date('d.m.Y', strtotime($brcShipBillDate)):""?></FONT></TD>
</TR>
<TR class="brcTr5">
	<TD colspan=5 class="brcTd20"><FONT class="brcFt5">5.DESCRIPTION OF GOODS AS GIVEN IN THE CUMSTOMS AUTHENTICATED S/BILLS:</FONT></TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd14">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=5 class="brcTd20"><FONT class="brcFt0">
	<textarea style="width:80%;" rows="3" id="brcGoodsDescription" name="brcGoodsDescription"><?=$brcGoodsDescription?></textarea>
</FONT></TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd14">&nbsp;</TD>
</TR>
<TR class="brcTr3">
	<TD colspan=5 class="brcTd20"><FONT class="brcFt5">6.BILL OF LADING/POST PARCEL RECEIPT/AIRWAY BILL NO.:&nbsp;<?=$brcBillLaddingNo?></FONT></TD>
	<TD class="brcTd13">&nbsp;</TD>
	<TD class="brcTd21"><FONT class="brcFt17">7.</FONT><FONT class="brcFt5">DATE:&nbsp;<?=($brcBillLaddingDate!='0000-00-00')?date('d.m.Y', strtotime($brcBillLaddingDate)):""?></FONT></TD>
</TR>
<TR class="brcTr4">
	<TD colspan=4 class="brcTd22">&nbsp;</TD>
	<TD class="brcTd23">&nbsp;</TD>
	<TD class="brcTd18">&nbsp;</TD>
	<TD class="brcTd24">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd25">8.DESTINATION OF GOODS :COUNTRY NAME :&nbsp;<?=strtoupper($brcFinalDestination)?></TD>
	<TD class="brcTd26">&nbsp;</TD>
	<TD class="brcTd27">&nbsp;</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd29">9</TD>
	<TD class="brcTd30">&nbsp;</TD>
	<TD class="brcTd31">10</TD>
	<TD class="brcTd32">11</TD>
	<TD class="brcTd33">12</TD>
	<TD class="brcTd34">13</TD>
	<TD class="brcTd35">14</TD>
</TR>
<TR class="brcTr3">
	<TD class="brcTd36"><FONT class="brcFt5">BILL AMOUNT</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">FREIGHT AMT</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">INSURANCE AMT</FONT></TD>
	<TD class="brcTd38"><FONT class="brcFt5">COMMISSION</FONT></TD>
	<TD class="brcTd39"><FONT class="brcFt5">WHETHER THE</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">FOB VALUE FOR</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd36"><FONT class="brcFt5">CIF/C&F/FOB</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">AS PER BILL OF</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">AS PER INSURANCE</FONT></TD>
	<TD class="brcTd38"><FONT class="brcFt5">DISCOUNT</FONT></TD>
	<TD class="brcTd39"><FONT class="brcFt5">EXPORT IS IN</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">VALUE ACTUALLY</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd36"><FONT class="brcFt5">(IN FOREIGN</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">LADING/FREIGHT</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">COMPANYS BILL/</FONT></TD>
	<TD class="brcTd38"><FONT class="brcFt5">PAID/PAYABLE</FONT></TD>
	<TD class="brcTd39"><FONT class="brcFt5">FREELY CONVER-</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">REALISED INFREE</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd36"><FONT class="brcFt5">EXCHANGE)</FONT></TD>
	<TD colspan=2 class="brcTd37"><FONT class="brcFt5">MEMO</FONT></TD>
	<TD class="brcTd37"><FONT class="brcFt5">RECEIPT</FONT></TD>
	<TD class="brcTd40">&nbsp;</TD>
	<TD class="brcTd39"><FONT class="brcFt5">TIBLE CURRENCY</FONT></TD>
	<TD class="brcTd21"><FONT class="brcFt5">FOREIGN EXCH-</FONT></TD>
</TR>
<TR class="brcTr6">
	<TD class="brcTd41">&nbsp;</TD>
	<TD class="brcTd42">&nbsp;</TD>
	<TD class="brcTd43">&nbsp;</TD>
	<TD class="brcTd44">&nbsp;</TD>
	<TD class="brcTd45">&nbsp;</TD>
	<TD class="brcTd46"><FONT class="brcFt8">OR IN INDIAN RS.</FONT></TD>
	<TD class="brcTd19"><FONT class="brcFt8">ANGE/RS.</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd29" height="290" valign="top" style="vertical-align: middle; padding:0 5px;">
		<textarea name="brcBillAmt" id="brcBillAmt" rows="4" style=" height: 250px;width: 200px;"><?=$brcBillAmt?></textarea>
	</TD>
	<TD class="brcTd58" colspan="2" valign="top" style="vertical-align: middle; padding:0 5px;">
		<textarea name="brcFreightAmt" id="brcFreightAmt" rows="4" style=" height: 250px;width: 200px;"><?=$brcFreightAmt?></textarea>
	</TD>
	<TD class="brcTd59" valign="top" style="vertical-align: middle; padding:0 5px;">
		<textarea name="brcInsuranceAmt" id="brcInsuranceAmt" rows="4" style=" height: 250px;width: 200px;"><?=$brcInsuranceAmt?></textarea>
	</TD>
	<TD class="brcTd60" valign="top" style="vertical-align: middle; padding:0 5px;">
		<textarea name="brcCommissionDiscount" id="brcCommissionDiscount" rows="4" style=" height: 250px;width: 200px;"><?=$brcCommissionDiscount?></textarea>
	</TD>
	<TD class="brcTd27" valign="top" style="vertical-align: middle; padding:0 5px;">
		<textarea name="brcFreeConvert" id="brcFreeConvert" rows="4" style=" height: 250px;width: 200px;"><?=$brcFreeConvert?></textarea>
	</TD>
	<TD class="brcTd28" valign="top" style="vertical-align: middle; padding:0 5px;">
		<textarea name="brcFOBValue" id="brcFOBValue" rows="4" style=" height: 250px;width: 200px;"><?=$brcFOBValue?></textarea>
	</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd29" valign="top" style="vertical-align: top; padding:0 5px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="fieldName">USD:&nbsp;<input type="text" name="brcBillAmtUSD" id="brcBillAmtUSD" value="<?=$brcBillAmtUSD?>" size="12" style="text-align:right; border:none;"  readonly /></td>		
			</tr>
			<tr>
				<td class="fieldName">Rs:&nbsp;<input type="text" name="brcBillAmtRs" id="brcBillAmtRs" value="<?=$brcBillAmtRs?>" size="12" style="text-align:right; border:none;"  readonly /></td>
			</tr>
		</table>
	</TD>
	<TD class="brcTd58" colspan="2" valign="top" style="vertical-align: top; padding:0 5px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="fieldName">USD:&nbsp;<input type="text" name="brcFreightAmtUSD" id="brcFreightAmtUSD" value="<?=$brcFreightAmtUSD?>" size="12" style="text-align:right;" onkeyup="calcBRCRs('brcFreightAmtUSD','brcFreightAmtRsPerUSD','brcFreightAmtRs');" /></td>		
			</tr>
			<tr>
				<td class="fieldName"><span title="Rs per USD">@INR</span>&nbsp;<input type="text" name="brcFreightAmtRsPerUSD" id="brcFreightAmtRsPerUSD" value="<?=$brcFreightAmtRsPerUSD?>" size="6" style="text-align:right;" onkeyup="calcBRCRs('brcFreightAmtUSD','brcFreightAmtRsPerUSD','brcFreightAmtRs');" /></td>		
			</tr>
			<tr>
				<td class="fieldName">Rs:&nbsp;<input type="text" name="brcFreightAmtRs" id="brcFreightAmtRs" value="<?=$brcFreightAmtRs?>" size="12" style="text-align:right; border:none" readonly /></td>
			</tr>
		</table>
	</TD>
	<TD class="brcTd59" valign="top" style="vertical-align: top; padding:0 5px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="fieldName">USD:&nbsp;<input type="text" name="brcInsuranceAmtUSD" id="brcInsuranceAmtUSD" value="<?=$brcInsuranceAmtUSD?>" size="12" style="text-align:right;" onkeyup="calcBRCRs('brcInsuranceAmtUSD','brcInsuranceAmtRsPerUSD','brcInsuranceAmtRs');" /></td>		
			</tr>
			<tr>
				<td class="fieldName"><span title="Rs per USD">@INR</span>&nbsp;<input type="text" name="brcInsuranceAmtRsPerUSD" id="brcInsuranceAmtRsPerUSD" value="<?=$brcInsuranceAmtRsPerUSD?>" size="6" style="text-align:right;" onkeyup="calcBRCRs('brcInsuranceAmtUSD','brcInsuranceAmtRsPerUSD','brcInsuranceAmtRs');" /></td>		
			</tr>
			<tr>
				<td class="fieldName">Rs:&nbsp;<input type="text" name="brcInsuranceAmtRs" id="brcInsuranceAmtRs" value="<?=$brcInsuranceAmtRs?>" size="12" style="text-align:right; border:none;" readonly /></td>
			</tr>
		</table>
	</TD>
	<TD class="brcTd60" valign="top" style="vertical-align: top; padding:0 5px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="fieldName">USD:&nbsp;<input type="text" name="brcCommissionDiscountUSD" id="brcCommissionDiscountUSD" value="<?=$brcCommissionDiscountUSD?>" size="12" style="text-align:right;" onkeyup="calcBRCRs('brcCommissionDiscountUSD','brcCommissionDiscountRsPerUSD','brcCommissionDiscountRs');" /></td>		
			</tr>
			<tr>
				<td class="fieldName"><span title="Rs per USD">@INR</span>&nbsp;<input type="text" name="brcCommissionDiscountRsPerUSD" id="brcCommissionDiscountRsPerUSD" value="<?=$brcCommissionDiscountRsPerUSD?>" size="6" style="text-align:right;" onkeyup="calcBRCRs('brcCommissionDiscountUSD','brcCommissionDiscountRsPerUSD','brcCommissionDiscountRs');" /></td>		
			</tr>
			<tr>
				<td class="fieldName">Rs:&nbsp;<input type="text" name="brcCommissionDiscountRs" id="brcCommissionDiscountRs" value="<?=$brcCommissionDiscountRs?>" size="12" style="text-align:right; border:none;" readonly /></td>
			</tr>
		</table>
	</TD>
	<TD class="brcTd27" valign="top" style="vertical-align: top; padding:0 5px;">
		&nbsp;
	</TD>
	<TD class="brcTd28" valign="top" style="vertical-align: top; padding:0 5px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="fieldName">USD:&nbsp;<input type="text" name="brcFOBValueUSD" id="brcFOBValueUSD" value="<?=$brcFOBValueUSD?>" size="12" style="text-align:right; border:none;" readonly /></td>		
			</tr>
			<tr>
				<td class="fieldName">Rs:&nbsp;<input type="text" name="brcFOBValueRs" id="brcFOBValueRs" value="<?=$brcFOBValueRs?>" size="12" style="text-align:right; border:none;" readonly /></td>
			</tr>
		</table>
	</TD>
</TR>
<TR class="brcTr2">
	<TD class="brcTd61">&nbsp;</TD>
	<TD class="brcTd30">&nbsp;</TD>
	<TD class="brcTd58">&nbsp;</TD>
	<TD class="brcTd59">&nbsp;</TD>
	<TD class="brcTd60">&nbsp;</TD>
	<TD class="brcTd27">&nbsp;</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd25">15.DATE OF REALISATION OF EXPORT PROCEEDS :</TD>
	<TD class="brcTd62"><input name="brcRealisationDate" id="brcRealisationDate" type="text" size="8" value="<?=$brcRealisationDate?>" /></TD>
	<TD class="brcTd46">16.G.R.FORM NO.</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr8">
	<TD colspan=5 class="brcTd63">17.NO.DATE & CATEGORY OF APPLICABLE LICENCE/AUTHORISATION:&nbsp;<input name="brcLicenceCategory" id="brcLicenceCategory" type="text" size="12" value="<?=$brcLicenceCategory?>" /></TD>
	<TD class="brcTd64">&nbsp;</TD>
	<TD class="brcTd28">&nbsp;</TD>
</TR>
<TR class="brcTr5">
	<TD colspan=4 class="brcTd65"><FONT class="brcFt0">We futher declare that the aforesaid particulars are correct</FONT></TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd65"><FONT class="brcFt0">(Copies of invoices relevant to these exports and custom</FONT></TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=4 class="brcTd65"><FONT class="brcFt0">attested EP cpy of relevant shipping bill is attached for</FONT></TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD colspan=2 class="brcTd68"><FONT class="brcFt5">(Signature of Exporter)</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD colspan=2 class="brcTd69"><FONT class="brcFt0">verification by the Bank)</FONT></TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=3 class="brcTd71"><FONT class="brcFt5">Name in Block Letters: <input name="brcExporterName" id="brcExporterName" type="text" size="40" value="<?=$brcExporterName?>" /></FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd72"><FONT class="brcFt5">Place: MUMBAI</FONT></TD>
	<TD colspan=2 class="brcTd73"><FONT class="brcFt5">Official</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd74"><FONT class="brcFt0">Designation:</FONT></TD>
	<TD colspan=2 class="brcTd68"><FONT class="brcFt5">AUTHORISED SIGNATORY</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd72"><FONT class="brcFt5">Date:&nbsp;<?=date("d.m.Y");?></FONT></TD>
	<TD colspan=2 class="brcTd73"><FONT class="brcFt0">Seal Stamp</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd75">&nbsp;</TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=3 class="brcTd71"><FONT class="brcFt0" style="white-space:normal;">Full Official Address: <?=$exporterAddress?><!--505A,GALLERIA , Hiranandani Gardens,--></FONT></TD>
</TR>
<TR class="brcTr8">
	<TD class="brcTd75">&nbsp;</TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=2 class="brcTd76"><FONT class="brcFt11"><!--A.S.Marg, Mumbai-400076--></FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd75">&nbsp;</TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=2 class="brcTd77"><FONT class="brcFt0">Full Residental Address:</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr9">
	<TD class="brcTd72"><FONT class="brcFt12">BANK CERTIFICATE</FONT></TD>
	<TD class="brcTd48">&nbsp;</TD>
	<TD class="brcTd70">&nbsp;</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD colspan=2 class="brcTd78"><FONT class="brcFt13">Ref No.&nbsp;<input name="brcRefNo" id="brcRefNo" type="text" size="18" value="<?=$brcRefNo?>" /></FONT></TD>
	<TD class="brcTd79"><FONT class="brcFt0">Date.&nbsp;<input name="brcRefNoDate" id="brcRefNoDate" type="text" size="8" value="<?=$brcRefNoDate?>" /></FONT></TD>
</TR>
<TR class="brcTr0">
	<TD colspan=3 class="brcTd80"><FONT class="brcFt0">Authorised Foreign Exchange Dealer Code no.</FONT></TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd81"><FONT class="brcFt0">Place:BOMBAY</FONT></TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=2 class="brcTd69"><FONT class="brcFt0">alloted to the bank by RBI:</FONT></TD>
	<TD class="brcTd82">
		<FONT class="brcFt0">
			<input name="brcFgnExDealerCodeNo" id="brcFgnExDealerCodeNo" type="text" size="18" value="<?=($brcFgnExDealerCodeNo!="")?$brcFgnExDealerCodeNo:""?>" style="border:none;" readonly />
		</FONT>
	</TD>
	<TD class="brcTd11">&nbsp;</TD>
	<TD class="brcTd12">&nbsp;</TD>
	<TD class="brcTd66">&nbsp;</TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=6 class="brcTd83"><FONT class="brcFt0">1.This is to certify that we have verified relevant export invoices,Cuistom attested EP copy of shipping bill and other</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=7 class="brcTd84"><FONT class="brcFt5" style="white-space:normal;">relevanbrcTdocuments of M/S . <?=$exporterAddress?></FONT></TD>
</TR>
<TR class="brcTr0">
	<TD colspan=6 class="brcTd83"><FONT class="brcFt0">We further certify that the particulars given in Column No. 1 to 17 have been verified and found to be correct.</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD colspan=6 class="brcTd83"><FONT class="brcFt0">We have also verified theFOB value mentioned in Col.14 above with reference to following documents:-</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
<TR class="brcTr4">
	<TD colspan=3 class="brcTd80"><FONT class="brcFt4">(i)Bill Of Lading/PP Receipt/Airways Bill</FONT></TD>
	<TD colspan=3 class="brcTd85"><FONT class="brcFt4">(ii)Insurance Policy/Cover/Insurance Receipt</FONT></TD>
	<TD class="brcTd67">&nbsp;</TD>
</TR>
</TABLE>
<P class="brcP2"><FONT class="brcFt1">2.</FONT><FONT class="brcFt14">FOB actually realized and dae of realization of export proceeds are to be given in all cases except where consignment has been sent against confirmed irrevocable letter of credit or exports made against the Government of India/Exim Bank line of Credit or exports made under Deferred Payment/ Suppliers Line of Credit ConbrcTract backed by ECGC Cover .</FONT></P>
<TABLE cellpadding=0 cellspacing=0 class="brcTbl2">
<TR height=0>
	<TD width=14px></TD>
	<TD width=72px></TD>
	<TD width=160px></TD>
	<TD width=360px></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD colspan=3 class="brcTd87">An endorsement to that effect needs to be endorsed in BRC.</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">3.</TD>
	<TD colspan=2 class="brcTd89">We have also verified that the date of export is</TD>
	<TD class="brcTd90" nowrap><input name="brcExportDate" id="brcExportDate" type="text" size="8" value="<?=($brcExportDate!="")?$brcExportDate:(($invDate!='0000-00-00')?date('d/m/Y', strtotime($invDate)):"")?>" /></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD colspan=2 class="brcTd89">*Applicable only in respect of exports by air</TD>
	<TD class="brcTd91">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">4.</TD>
	<TD colspan=3 class="brcTd87">This is to certify that we have certified the amount of the Commission Paid/payable, as declared above, by the exporter i.e</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd92" nowrap><input name="brcCertifyAmtDescr" id="brcCertifyAmtDescr" type="text" size="35" value="<?=($brcCertifyAmtDescr!="")?$brcCertifyAmtDescr:"----NIL-----"?>" /></TD>
	<TD colspan=2 class="brcTd93">(in figures and words) with G.R. Forms and found to be correct.</TD>
</TR>
</TABLE>
<P class="brcP0"><FONT class="brcFt15">Note:</FONT></P>
<TABLE cellpadding=0 cellspacing=0 class="brcTbl3">
<TR class="brcTr0">
	<TD class="brcTd88">1.</TD>
	<TD class="brcTd94">Bank can issue a consolidated certificate (consignment wise) for more</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd94">than one consignment.</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">2.</TD>
	<TD class="brcTd94">F.O.B. actually realised and date of realisation of export proceeds are to be</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd94">given in all cases except where consignment has been sent against confirmed</TD>
	<TD class="brcTd95">&nbsp;</TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd94">irrevocable letter of credit.</TD>
	<TD class="brcTd96"><FONT class="brcFt5">(Signature of the Banker's)</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd88">3.</TD>
	<TD class="brcTd94">This shall be required wherever specifically prescribed in the policy/procedure.</TD>
	<TD class="brcTd97"><FONT class="brcFt16">Full address of the Bankers (Branch &City)</FONT></TD>
</TR>
<TR class="brcTr0">
	<TD class="brcTd86">&nbsp;</TD>
	<TD class="brcTd98">&nbsp;</TD>
	<TD class="brcTd99"><FONT class="brcFt5">Official Stamp</FONT></TD>
</TR>
</TABLE>
</DIV>
		</td>
	</tr>
							
								<tr>												
									<td align="center">
										<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Invoice.php');">&nbsp;&nbsp;
										<input type="submit" name="cmdBRCSaveChange" id="cmdBRCSaveChange" class="button" value=" Save Changes " onClick="return validateBRCInvoice();">
									</td>
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
			# Listing Starts
		?>


<!-- 
	Bank Certificate Ends Here
-->
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Invoice </td>
								    <td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr><td nowrap="nowrap">							
									<table cellpadding="0" cellspacing="0">
                      <tr> 
					  	<td class="listing-item"> From:</td>
                                    <td nowrap="nowrap"> 
                            <? 
							if($dateFrom=="") $dateFrom=date("d/m/Y");
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
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search " onclick="return validateInvoiceSearch(document.frmInvoice);"></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_','<?=$invoiceRecordsize;?>');"><? }?>&nbsp;
								<? if($add==true){?>
									<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button"> &nbsp;-->
								<? }?>
								<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintInvoiceList.php?selectFrom=<?=$fromDate?>&selectTill=<?=$tillDate?>&pageNo=<?=$page?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?php
									if ($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?php
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
		if (sizeof($invoiceRecords)>0) {
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
      				$nav.= " <a href=\"Invoice.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Invoice.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Invoice.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Invoice No </td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Invoice Type</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Customer</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total (<span class="replaceCY">US$</span>)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Container</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
		<? if(($add || $edit) && $brcEnabled){ ?>
			<td class="listing-head" width="45">&nbsp;</td>
		<? }?>
		<? if($edit && $debitNoteEnabled){ ?>
			<td class="listing-head" width="45">&nbsp;</td>
		<? }?>
		<? if($edit==true){?>
			<td class="listing-head" width="45">&nbsp;</td>
		<? }?>
	</tr>
	<?php
		$invoiceMainId = "";
		foreach ($invoiceRecords as $ir) {
			$i++;
			$invoiceMainId	= $ir[0];
			$sInvoiceNo		= $ir[1];	
			$sInvoiceDate	= $ir[2];
			$iCustomerId	= $ir[3];
			$iProformaNo	= $ir[4];
			$ientryDate	= $ir[5];
			$iPOId		= $ir[6];
			$iInvoiceTypeId	= $ir[7];
			
			$invoiceTypeName= $ir[8];
			$customer	= $ir[9];
			$invoiceStatus 	= $ir[10];
			//$disableEdit = ($invoiceStatus=='Y')?"disabled":"";

			$totalUSDAmt	= $ir[11];

			# Get Sel container
			$invContainerRecs = $invoiceObj->getContainerRecs($invoiceMainId);

			$invoiceNo = "";
			if ($sInvoiceNo!=0 && $invoiceStatus=='Y') $invoiceNo=sprintf("%02d",$sInvoiceNo);
			else if ($iProformaNo) $invoiceNo = "P$iProformaNo";

			$shipBillNo		= $ir[12];
			$billLaddingNo	= $ir[13];
			$exporterAlphaCode = $ir[14];
			$invoiceunitid=$ir[15];
			$invoiceunitno=$plantandunitObj->find($invoiceunitid);
				$invoiceunitno=$invoiceunitno[1];
			$unitalphacode=$ir[16];
			//echo "The invoiceid is $invoiceunitid";

			$sInvDate = ($sInvoiceNo=="" && $sInvoiceNo==0)?date('y-m-d'):$sInvoiceDate;
			$sInvYearRange = getFinancialYearRange($sInvDate);
			if (($invoiceunitid!="") && ($invoiceunitid!="0"))
			{
				//if ($exporterAlphaCode=="FFFPL")
				//{
					$exporterAlphaCode=$unitalphacode;
				//}
			if (!empty($exporterAlphaCode)) $invoiceNo = $exporterAlphaCode."/"."U-".$invoiceunitno."/".$invoiceNo."/".$sInvYearRange;
			}
			else
			{
			if (!empty($exporterAlphaCode)) $invoiceNo = $exporterAlphaCode."/".$invoiceNo."/".$sInvYearRange;
			}
			//echo "----------$invoiceNo";
			$disableEdit = "";
			if ($invoiceStatus=='Y' && !empty($shipBillNo) && !empty($billLaddingNo)) {
				$disableEdit = "disabled";
			}
			if ($reEdit) $disableEdit = "";

			$poRec = $invoiceObj->getPORec($iPOId);
			$selCurrencyCode	=  $poRec[9];

	?>
	<tr <?=$listRowMouseOverStyle?>>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$invoiceMainId;?>" class="chkBox" />
			<!--<input type="hidden" name="invoiceContainerId_<?=$i;?>" value="<?=$invoiceContainerId?>">
			<input type="hidden" name="invoicePOId_<?=$i;?>" value="<?=$invoicePOId?>">-->
			<input type="hidden" name="invoiceStatus_<?=$i;?>" id="invoiceStatus_<?=$i;?>" value="<?=$invoiceStatus?>" readonly="true">
			<input type="hidden" name="invAmt_<?=$invoiceMainId;?>" id="invAmt_<?=$invoiceMainId;?>" value="<?=$totalUSDAmt?>" readonly="true">
			<input type="hidden" name="invCurrencyCode_<?=$invoiceMainId;?>" id="invCurrencyCode_<?=$invoiceMainId;?>" value="<?=$selCurrencyCode?>" readonly="true">
			<input type="hidden" name="hdnInvoiceNumber_<?=$invoiceMainId;?>" id="hdnInvoiceNumber_<?=$invoiceMainId;?>" value="<?=$invoiceNo?>" readonly="true">
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$invoiceNo;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$invoiceTypeName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$customer?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=number_format($totalUSDAmt,2,'.','')?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
			<?php
					$numCol = 3;
					if (sizeof($invContainerRecs)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($invContainerRecs as $r) {							
							$selName = $r[2];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) {
								echo "<br/>";
							}
						}
					}
			?>
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px; line-height:normal;" nowrap="true">
			<a href="javascript:printWindow('PrintInvoice.php?invoiceId=<?=$invoiceMainId?>&print=N',700,600)" class="link1" title="Click here to View Invoice">VIEW</a>
			<? if($print==true){?>
			/
			<a href="javascript:void(0);" class="link1" title="Click here to Print the Invoice" onclick="return printShipmentInvoice('<?=$invoiceMainId?>','<?=$invoiceStatus?>');">PRINT</a>
			<!--a href="javascript:printWindow('PrintInvoice.php?invoiceId=<?=$invoiceMainId?>&print=Y',700,600)" class="link1" title="Click here to Print the Invoice">PRINT</a-->
			/
			<a href="javascript:printWindow('PrintInvoice.php?invoiceId=<?=$invoiceMainId?>&packingList=Y',700,600)" class="link1" title="Click here to Print the Packing List">PKG LIST</a>
			<? if(($add || $edit) && $brcEnabled){ ?>
			/
			<a href="javascript:printWindow('PrintInvoiceBRC.php?invoiceId=<?=$invoiceMainId?>&print=Y',700,600)" class="link1" title="Click here to Print Bank Realisation Certificate">BRC</a>
			<? }?>
			/

			<!--<a href="javascript:printWindow('PrintDN.php?invoiceId=<?=$invoiceMainId?>',700,600)" class="link1" title="Click here to Print Debit Note ">Dr Note</a>-->
			<a href="javascript:void(0);" class="link1" title="Click here to Print the Invoice" onclick="return printDRInvoice('<?=$invoiceMainId?>','<?=$invoiceStatus?>');">Dr Note</a>
			<? }?>	
			/
			<a href="javascript:void(0);" class="link1" title="Click here to Split the Invoice amount" onclick="return splitInvoiceAmt('<?=$invoiceMainId?>','<?=$invoiceStatus?>');">Split</a>
			
		</td>
		<? if(($add || $edit) && $brcEnabled){ ?>
			<td class="listing-item" width="45" align="center" style="padding:0 5px; line-height:normal;">
				
					<input type="submit" value=" BRC " name="cmdBankCertificate" onClick="assignValue(this.form,'<?=$invoiceMainId;?>','bankCertificateEditId');this.form.action='Invoice.php';" title="Update Bank Realisation Certificate" />
			</td>
		<? }?>
		<? if($edit && $debitNoteEnabled){ ?>
			<td class="listing-item" width="45" align="center" style="padding:0 5px; line-height:normal;">
					<!--<a href="javascript:void(0);" class="link1" title="Click here to Print the Invoice" onclick="return printDRNoteInvoice('<?=$invoiceMainId?>');">Debit Note</a>-->
					<input type="button" value=" Debit Note " name="cmdBankCertificate" onclick="return printDRNoteInvoice('<?=$invoiceMainId?>');" title="Update Debit Note" />
					<!--<input type="submit" value=" Debit Note " name="cmdBankCertificate" onClick="assignValue(this.form,'<?=$invoiceMainId;?>','debitNoteEditId');this.form.action='Invoice.php';" title="Update Debit Note" />-->
			</td>
		<? }?>
		<? if($edit==true){?>
		<td class="listing-item" width="45" align="center" style="padding:0 5px; line-height:normal;">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,'<?=$invoiceMainId;?>','editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$invoiceContainerId;?>','editContainerId'); assignValue(this.form,'<?=$invoicePOId;?>','editPOId'); this.form.action='Invoice.php';" <?=$disableEdit?> />
		</td>
		<? }?>
	</tr>
	<?php
			}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="editContainerId" value="<?=$editContainerId?>">
	<input type="hidden" name="editPOId" value="<?=$editPOId;?>">				
	<input type="hidden" name="editMode" value="<?=$editMode?>">
	<input type="hidden" name="bankCertificateEditId" id="bankCertificateEditId" value="" readonly />
	<input type="hidden" name="debitNoteEditId" id="debitNoteEditId" value="" readonly />
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
      				$nav.= " <a href=\"Invoice.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Invoice.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Invoice.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
	<?php
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?php
		}
	?>
	</table>
	  </td>
	</tr>
	<tr>
		<td colspan="3" height="5" >
			<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
			<input type="hidden" name="containerEntryId" id="containerEntryId" value="<?=$containerEntryId?>">
			<input type="hidden" name="poEntryId" id="poEntryId" value="<?=$poEntryId?>">
		</td>
	</tr>
	<tr >	
	<td colspan="3">
	<table cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
				<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_','<?=$invoiceRecordsize;?>');"><? }?>&nbsp;
					<? if($add==true){?>
						<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button">&nbsp;-->
					<? }?>
					
					<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintInvoiceList.php?selectFrom=<?=$fromDate?>&selectTill=<?=$tillDate?>&pageNo=<?=$page?>',700,600);"><? }?>
			</td>
		</tr>
	</table>
	</td>
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
		<tr>
			<td height="10">
				<input type="hidden" name="hidInvoiceType" id="hidInvoiceType" value="<?=$invoiceType?>" />
				<input type="hidden" name="vessalRecSize" id="vessalRecSize" value="<?=$sizeVessalRecs?>" />
				<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" readonly/>				
			</td>
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
			inputField  : "invoiceDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "invoiceDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	Calendar.setup 
	(	
		{
			inputField  : "shipBillDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "shipBillDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	Calendar.setup 
	(	
		{
			inputField  : "billLaddingDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "billLaddingDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	Calendar.setup 
	(	
		{
			inputField  : "brcRealisationDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "brcRealisationDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	Calendar.setup 
	(	
		{
			inputField  : "brcRefNoDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "brcRefNoDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	Calendar.setup 
	(	
		{
			inputField  : "brcExportDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "brcExportDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	Calendar.setup 
	(	
		{
			inputField  : "dnChqDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dnChqDate", 
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
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">		
		$(document).ready(function()	 {
			var rowSpanCount = '<?php echo $rowSpanCount ;?>';			
			$('#invoiceItemSpan').attr('rowspan',rowSpanCount);		
			
			 <?php
			if ($currencyCode!="") {	
			?>
			$(".replaceCY").html('<?=$currencyCode?>');
			<?php
			}	
			?>
			<?php
			if ($unitTxt!="")
			{	
			?>
			$(".replaceUnitTxt").html('<?=$unitTxt?>');
			<?php
			}	
			?>
		});

			function addNewItem()
			{				
				addNewRow('tblPOItem');			
			}
			<?php
				if (sizeof($selContainerRecs)>0) {
				// Set Value 
			?>
				fieldId = <?=sizeof($selContainerRecs)?>;
			<?php
				} else {
			?>
			window.load = addNewItem();
			<?php
				}
			?>
		
		showDiscount();

		<?php
			if ($mainId>0)
			{
		?>
		xajax_chkInvoiceNoExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$mainId?>', document.getElementById('invoiceDate').value, document.getElementById('hidInvoiceType').value,document.getElementById('exporter').value);
		<?php
			}	
		?>

	$(".editable_textarea").editable(function(value, settings) { 		 
		 xajax_updateTDP('<?php echo $mainId;?>',value);
		 return(value);
	  }, { 
		  indicator : "<img src='images/loading.gif'>",
		  type   : 'textarea',
		  submitdata: { _method: "put" },
		  select : true,
		  submit : 'OK',
		  cancel : 'cancel',
		  cssclass : "editable"
	});

	</SCRIPT>
	<?php
		}
	?>
<script type="text/javascript">
   $(document).ready(function ()
   {
	   $("#btnClose").click(function (e)
      {
         HideDialog();
         e.preventDefault();
      });
	   
	   
   });  
   <?php
   if ($debitNoteMode)
   {   
   ?>
	calcDNBkgFreight();
  <?}?>
	<?php
   if ( $bankCertMode)
   {   
   ?>
	$(document).ready(function ()
   {
	calcBRCVal();
   });
  <?}?>

</script>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>