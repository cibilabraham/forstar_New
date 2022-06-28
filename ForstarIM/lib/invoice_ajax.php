<?php
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
//$xajax->configure('defaultMode', 'synchronous'); // For return value
$xajax->configure('statusMessages', true); // For display status

//$objResponse->setReturnValue($chkRecExist); // Forretrun a value from ajax function

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}


		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}	
		
		function addDropDownOptionsNew($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}	
	}
	

	# SO Number Exist
	function chkInvoiceNoExist($invoiceNo, $mode, $cSOId, $selDate,  $invoiceType, $exporterId,$unitid)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$invoiceObj		= new Invoice($databaseConnect);
		# Check valid SO Num				
		if ($invoiceNo) {
			//$objResponse->alert($unitid);
			//$objResponse->alert($exporterId);
			$validInvoiceNum = $invoiceObj->chkValidInvoiceNum($selDate, $invoiceNo, $invoiceType, $exporterId);
			//$objResponse->alert("hai".$validInvoiceNum."hai");
			if ($validInvoiceNum) {
				$chkInvoiceNumExist = $invoiceObj->checkInvoiceNumberExist($invoiceNo, $cSOId, $invoiceType, $selDate, $exporterId,$unitid);
				//$chkInvoiceNumExist=true;
				//$objResponse->alert("hai".$chkInvoiceNumExist."hai");
				if ($chkInvoiceNumExist && $invoiceNo!="") {
					$objResponse->assign("divInvoiceExistTxt", "innerHTML", "$invoiceNo is already in use.<br>Please choose another one");					
					$objResponse->script("disableInvoiceButton($mode);");
					$objResponse->assign("validInvoiceNo","value", "N");
				} else  {
					# Check for cancelled challan number
					$cancelledInvoice = $invoiceObj->checkCancelledInvoice($invoiceNo, $selDate, $invoiceType, $exporterId);
					if ($cancelledInvoice) {
						$objResponse->assign("divInvoiceExistTxt", "innerHTML", "$invoiceNo is already cancelled.");			
						$objResponse->script("disableInvoiceButton($mode);");
						$objResponse->assign("validInvoiceNo","value", "N");
					} else {
						$objResponse->assign("divInvoiceExistTxt", "innerHTML", "");	
						$objResponse->script("enableInvoiceButton($mode);");
						$objResponse->assign("validInvoiceNo","value", "Y");
					}
				}
			} else {
				$objResponse->assign("divInvoiceExistTxt", "innerHTML", "$invoiceNo is not valid.<br>Please check the challan Settings.");
				$objResponse->script("disableInvoiceButton($mode);");
				$objResponse->assign("validInvoiceNo","value", "N");
			}
		}
		return $objResponse;
	}


	# Chk valid Despatch Date
	function chkValidInvoiceDate($selDate)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$invoiceObj	= new PurchaseOrder($databaseConnect);
		$manageChallanObj = new ManageChallan($databaseConnect);

		$sDate		= mysqlDateFormat($selDate);
		$chkValidDate   = $manageChallanObj->chkValidDate('SPO', $sDate);

		if (!$chkValidDate) {
			$objResponse->alert("Please check the selected Invoice date.");
			$objResponse->assign("validInvoiceDate", "value", "N");
		} else {
			$objResponse->assign("validInvoiceDate", "value", "Y");
		}
		return $objResponse;	
	}

	function getBankAC($bankACId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$companydetailsObj	= new CompanyDetails($databaseConnect);
		$bankRec			= $companydetailsObj->getBankACRec($bankACId);
		$bankADCode			= $bankRec[4];
		$objResponse->assign("brcFgnExDealerCodeNo", "value", $bankADCode);
		
		return $objResponse;
	}
	
	# Alpha code  Exist
	function chkAlphaCodeExist($alphaId,  $mainId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$manageChallanObj = new ManageChallan($databaseConnect);

		$chkAlphaExist = $manageChallanObj->checkAlphaCodeExist($alphaId, $mainId);
		if ($chkAlphaExist && $alphaId!="") {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "$alphaId is already in use.");
			//$objResponse->script("disableSPOButton($mode);");
		} else  {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "");
			//$objResponse->script("enableSPOButton($mode);");
		}
		return $objResponse;
	}
	
	function chkUnitExist($function,$company,$unitId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$manageChallanObj = new ManageChallan($databaseConnect);
		//$objResponse->alert($function);
		$chkUnitExist = $manageChallanObj->checkUnitExist($function,$company,$unitId);
		//$objResponse->alert($chkUnitExist);
		
		//$objResponse->alert($function);
		$objResponse->assign("alpha_code_prefix", "value", "");

		if($chkUnitExist)
		{
		$objResponse->alert("unit already exist");
		}
		/*rekha added code here dated on 25 july*/
		elseif($function=='')$objResponse->alert("Please select Function");
		elseif($function!='RM'){
			$varalphacode='';
			$BillingComp_alphacode='';
			$unit_alphacode = '';
			
			if($company!=""){
				$billingCompanyObj = new BillingCompanyMaster($databaseConnect);  
				$BillingComp_alphacode = $billingCompanyObj->getBillingCompanyAlphaCode($company);
				//$objResponse->alert("dkdkkd");
				//$objResponse->assign("lbl_compcode", "innerHTML", $BillingComp_alphacode);

				if($varalphacode=='') $varalphacode = $BillingComp_alphacode;
				else $varalphacode = $varalphacode ."-".$BillingComp_alphacode;
			}
			if($unitId!=""){
				$plantandunitObj = new PlantMaster($databaseConnect);
				$unit_alphacode = $plantandunitObj->getunit_alphacode($unitId);
				//$objResponse->assign("lbl_unitcode", "innerHTML", $unit_alphacode);

				if($varalphacode=='') $varalphacode = $unit_alphacode;
				else $varalphacode = $varalphacode ."-".$unit_alphacode;
			}
			if($function!=""){
				if($varalphacode=='') $varalphacode = $function;
				else $varalphacode = $varalphacode ."-".$function;
			}
			
			//$objResponse->alert("rekha");
			//$objResponse->assign("lbl_compcode", "innerHTML", $BillingComp_alphacode);
			$objResponse->assign("lbl_unitcode", "innerHTML", $unit_alphacode);
			$objResponse->assign("alpha_code_prefix", "value", $varalphacode);
		/*end code */
		}
		
		return $objResponse;
	}
	
	
	
	function getExporter($exporterId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$exporterMasterObj=	new ExporterMaster($databaseConnect);
		$exporterAddress	= $exporterMasterObj->getExporterDetails($exporterId);
		$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporterId);
		

		$objResponse->assign("exporterAddr", "innerHTML", $exporterAddress);
		$objResponse->assign("invoiceAlphaCode", "innerHTML", $exporterAlphaCode);
		$objResponse->assign("exporterAddr1", "innerHTML", $exporterId);

		$invoiceObj		= new Invoice($databaseConnect);
		$pcRecs		= $invoiceObj->fetchAllRecordsUnitsActiveExpId($exporterId);
		
		if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("unitid", $pcRecs, $exporterId);

		
		
		//if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("unitid", $pcRecs, $cPCId);
		//return $objResponse;

     /* $exporterId=65;

		$processcodeObj = new ProcessCode($databaseConnect);

		$pcRecs		= $processcodeObj->getProcessCodeRecs($exporterId);
		
		if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("unitid", $pcRecs, $cPCId);*/
		return $objResponse;

		//return $objResponse;
	}

	function getExporterUnit($exporterId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$exporterMasterObj=	new ExporterMaster($databaseConnect);
		$exporterAddress	= $exporterMasterObj->getExporterDetails($exporterId);
		$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporterId);
		

		$objResponse->assign("exporterAddr", "innerHTML", $exporterAddress);
		$objResponse->assign("invoiceAlphaCode", "innerHTML", $exporterAlphaCode);
		$objResponse->assign("exporterAddr1", "innerHTML", $exporterId);

		$invoiceObj		= new Invoice($databaseConnect);
		$pcRecs		= $invoiceObj->fetchAllRecordsUnitsActiveExpId($exporterId);
		
		if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("unitid", $pcRecs, $exporterId);

		
		
		//if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("unitid", $pcRecs, $cPCId);
		//return $objResponse;

     /* $exporterId=65;

		$processcodeObj = new ProcessCode($databaseConnect);

		$pcRecs		= $processcodeObj->getProcessCodeRecs($exporterId);
		
		if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("unitid", $pcRecs, $cPCId);*/
		return $objResponse;

		//return $objResponse;
	}

	//function getAlphacode($exporterId=null)
	function getAlphacode($unitId,$exporterId)
	{

		$objResponse 		= new NxajaxResponse();	
		$databaseConnect	= new DatabaseConnect();
		$invoiceObj			= new Invoice($databaseConnect);
		$unitAlphaCode	= $invoiceObj->fetchAlldata($unitId,$exporterId);
		$objResponse->assign("unitalphacode", "value", $unitAlphaCode);
		return $objResponse;

	}


	/*
	** Terms of Delivery and payment (TDP)
	*/
	function updateTDP($mainId,$content)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$invoiceObj			= new Invoice($databaseConnect);
		
		if (!empty($mainId)) $uptd = $invoiceObj->updateTDP($mainId,trim($content));

		//$objResponse->alert("$mainId,$content");
		return $objResponse;
	}

	
	// Save Split up
	function SaveSplitup($invoiceId, $totValueInRs, $splitupStr)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$invoiceObj	= new Invoice($databaseConnect);

		if ($invoiceId>0 && $splitupStr!="") {

			$recInserted   = $invoiceObj->AddSplitup($invoiceId, $splitupStr);

			if ($recInserted) {

				$updateInvMain = $invoiceObj->UpdateInvValue($invoiceId, $totValueInRs);

				$objResponse->script("closeSIADialogAfterInsert();");
				$objResponse->alert("Invoice amount split-up successfully updated.");
			} else {
				$objResponse->alert("Failed to update Invoice amount split-up.");
			}
		} else {
			$objResponse->alert("Please check the entered values.");
		}
		return $objResponse;	
	}

	// Get Split-up
	function GetSplitup($invoiceId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$invoiceObj	= new Invoice($databaseConnect);

		if ($invoiceId>0) {

			$splitupRecs   = $invoiceObj->fetchAllSplitUpAmt($invoiceId);
			$objResponse->script("getSIAItemArr('".json_encode($splitupRecs)."')");
		} 
		return $objResponse;	
	}

	function getUnit($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj	= new ManageUsers($databaseConnect);
		$sessObj =	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		/* rekha added code dated on 25 july 2018 */
		$billingCompanyObj = new BillingCompanyMaster($databaseConnect);  

		$BillingComp_alphacode = $billingCompanyObj->getBillingCompanyAlphaCode($companyId);

		/* end code */
		
		list($companyRecords,$unitRecords,$departmentRecords,,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("unitidInv",$unit ,$cel);
		//$objResponse->assign("alpha_code_prefix", "value", $BillingComp_alphacode);
		$objResponse->assign("lbl_compcode", "innerHTML", $BillingComp_alphacode);
		$objResponse->assign("lbl_unitcode", "innerHTML", "");
		$objResponse->assign("alpha_code_prefix", "value", "");

		return $objResponse;
		
				

	}

$xajax->register(XAJAX_FUNCTION, 'getUnit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkAlphaCodeExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkInvoiceNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkValidInvoiceDate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBankAC', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getExporter', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateTDP', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'SaveSplitup', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'GetSplitup', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getAlphacode',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getExporterUnit',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'chkUnitExist',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>