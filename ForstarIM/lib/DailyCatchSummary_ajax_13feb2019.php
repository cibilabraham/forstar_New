<?php
//require_once("lib/databaseConnect.php");
//require_once("purchaseorder_class.php");
//require_once("config.php");
require_once("libjs/xajax_core/xajax.inc.php");
//require_once("libjs/xajax_core/xajax_fallback.php");
$xajax = new xajax();	
//$xajax->configure('defaultMode', 'synchronous'); // For return value
$xajax->configure('statusMessages', true); // For display status

//$objResponse->setReturnValue($chkRecExist); // Forretrun a value from ajax function

class NxajaxResponse extends xajaxResponse
	{
		/*function addCreateOptions($sSelectId, $options, $cId)
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
	}
	
	function getProformaInvoiceNo()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$purchaseorderObj	= new PurchaseOrder($databaseConnect);
		$proformaInvoiceNo 	= $purchaseorderObj->getNextProformaInvoiceNo();
		$objResponse->assign("proformaInvoiceNo","value",$proformaInvoiceNo);
		return $objResponse;
	}

	function getSampleInvoiceNo()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$purchaseorderObj		=	new PurchaseOrder($databaseConnect);
		$sampleInvoiceNo	= $purchaseorderObj->getNextSampleInvoiceNo();
		$objResponse->assign("sampleInvoiceNo","value",$sampleInvoiceNo);
		return $objResponse;
	}


	# Proforma Number Exist
	function chkProformaNoExist($invoiceNum, $mode, $cSOId, $selDate)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj 	= new PurchaseOrder($databaseConnect);
		$soYear			= date("Y", strtotime($selDate));
		
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $purchaseorderObj->chkValidProformaNum($selDate, $invoiceNum);			
			if ($validSONum) {
				$chkSONumExist = $purchaseorderObj->checkProformaNumExist($invoiceNum, $cSOId);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSPOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt", "innerHTML", "");
					$objResponse->script("enableSPOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSPOButton($mode);");
			}
		}
		return $objResponse;
	}

	# Sample Number Exist
	function chkSampleNoExist($invoiceNum, $mode, $cSOId, $selDate)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj 		= new PurchaseOrder($databaseConnect);
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $purchaseorderObj->chkValidSampleNum($selDate, $invoiceNum);
			if ($validSONum) {
				$chkSONumExist = $purchaseorderObj->checkSampleNumExist($invoiceNum, $cSOId);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSPOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt", "innerHTML", "");
					$objResponse->script("enableSPOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSPOButton($mode);");
			}
		}
		return $objResponse;
	}

	# Chk valid Despatch Date
	function chkValidDespatchDate($selDate)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		$manageChallanObj = new ManageChallan($databaseConnect);
		$sDate		= mysqlDateFormat($selDate);
		$chkValidDate   = $manageChallanObj->chkValidDate('SPO',$sDate);
		if (!$chkValidDate) {
			$objResponse->alert("Please check the selected despatch date.");
			$objResponse->assign("validDespatchDate", "value", 1);
		} else {
			$objResponse->assign("validDespatchDate", "value", 0);
		}		
		return $objResponse;	
	}

	# Get Process Codes	*/
	/*function getProcessCodes($fishId, $rowId, $cPCId)
	{
		$objResponse 	 = new NxajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		$processcodeObj = new ProcessCode($databaseConnect);

		$pcRecs		= $processcodeObj->getProcessCodeRecs($fishId);
		
		if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("selProcessCode_$rowId", $pcRecs, $cPCId);
		return $objResponse;
	}*/

	function getbillCompany($fromDate, $tillDate, $selectSupplier)
	{
		$objResponse 	 = new NxajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		//$purchaseorderObj = new PurchaseOrder($databaseConnect);
		$settlementHistoryObj=new SetlementHistory($databaseConnect);
		//$processcodeObj = new ProcessCode($databaseConnect);
		$objResponse->alert("hai");
		//$pcRecs		= $processcodeObj->getProcessCodeRecs($fishId);
		//$pcRecs		= $settlementHistoryObj->getProcessCodeRecs($fishId);
		//$pcRecs	=$settlementHistoryObj->fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier);
		//if (sizeof($pcRecs)>0) $objResponse->addDropDownOptions("billingCompany", $pcRecs, $cPCId);
		//return $objResponse;
	}

	/*# Get Brand Recs
	function getBrandRecs($tableRowCount, $customerId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		# get Recs
		$brandRecs     = $purchaseorderObj->getBrandRecords($customerId);
		for ($i=0; $i<=$tableRowCount; $i++) {			
			$objResponse->addCreateOptions("selBrand_".$i, $brandRecs, "hidBrandId_".$i);
		}		
		return $objResponse;
	}

	# Assign Selected Brand value
	function assignBrand($brandId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$objResponse->assign("hidBrandId_$rowId","value",$brandId);
		return $objResponse;
	}

	# Get Grade Recs
	function getGradeRecs($processCodeId, $rowId, $selGradeId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		$gradeRecs		= $purchaseorderObj->getFrozenGradeRecs($processCodeId);		
		$objResponse->addDropDownOptions("selGrade_$rowId", $gradeRecs, $selGradeId);
		return $objResponse;
	}

	# Get frozen Code Filled wt
	function getFilledWt($frozenCodeId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);

		list($filledWt, $declaredWt, $unit) = $purchaseorderObj->getFrznPkgFilledWt($frozenCodeId);

		$objResponse->assign("frznPkgFilledWt_$rowId","value",($filledWt!="")?$filledWt:0);
		$objResponse->assign("frznPkgDeclaredWt_$rowId","value",($declaredWt!="")?$declaredWt:0);
		$objResponse->assign("frznPkgUnit_$rowId","value",$unit);
		

		$objResponse->script("totRowVal($rowId);");
		return $objResponse;		
	}

	# Get Num of MC Packing
	function getNumMC($mcPackingId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);

		$numPacks  = $purchaseorderObj->numOfPacks($mcPackingId);
		if ($numPacks) $objResponse->assign("numPacks_$rowId","value",$numPacks);
		else $objResponse->assign("numPacks_$rowId","value",0);
		$objResponse->script("totRowVal($rowId);");
		return $objResponse;		
	}

	# Get Port List
	function getPortList($countryId, $selPortId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		
		$portRecs = $purchaseorderObj->getPortRecs($countryId);

		$objResponse->addDropDownOptions("selPort", $portRecs, $selPortId);
		return $objResponse;
	}

	function getAgentList($customerId, $selAgentId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		
		$agentRecs = $purchaseorderObj->getAgentRecs($customerId);

		$objResponse->addDropDownOptions("selAgent", $agentRecs, $selAgentId);
		return $objResponse;
	}

	function reloadCustomer()
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		
		$custRecs = $purchaseorderObj->getCustomerRecs();

		$objResponse->addDropDownOptions("selCustomer", $custRecs, '');
		return $objResponse;
	}
*/

	# Customer Payment Terms
	/*function getCustPaymentTerm($customerId, $selPaymentTermId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		
		if ($customerId) {
			$paymentTermRecs = $purchaseorderObj->getPaymentTermRecs($customerId);
			/*
			$objResponse->alert(sizeof($paymentTermRecs)."h=".$paymentTermRecs[1]);
			if (sizeof($paymentTermRecs)==2 && $selPaymentTermId=="") $selPaymentTermId = $paymentTermRecs[0][0]; // Select default
			*/
	/*	}

		$objResponse->addDropDownOptions("paymentTerms", $paymentTermRecs, $selPaymentTermId);
		return $objResponse;
	}*/

	
	# Check Num MC Exist
	/*function getNumMCExist($poId, $fishId, $processCodeId, $gradeId, $rowId)
	{	
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);

		# Get Num of Available MC
		$numMC	= $purchaseorderObj->chkFrznPkngMCExist($poId, $fishId, $processCodeId, $gradeId);

		$objResponse->assign("availableNumMC_$rowId", "value", $numMC);

		return $objResponse;
	}

	
	# Split Proforma Number Exist
	function chkSplitPfrmaNoExist($invoiceNum, $mode, $cSOId, $selDate, $splitRowId)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj 	= new PurchaseOrder($databaseConnect);
		$soYear			= date("Y", strtotime($selDate));
		
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $purchaseorderObj->chkValidProformaNum($selDate, $invoiceNum);			
			if ($validSONum) {
				$chkSONumExist = $purchaseorderObj->checkProformaNumExist($invoiceNum, $cSOId);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt_$splitRowId", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSPOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt_$splitRowId", "innerHTML", "");
					$objResponse->script("enableSPOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt_$splitRowId", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSPOButton($mode);");
			}
		}
		return $objResponse;
	}
*/
	# Sample Number Exist
/*	function chkSplitSampleNoExist($invoiceNum, $mode, $cSOId, $selDate, $splitRowId)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj 		= new PurchaseOrder($databaseConnect);
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $purchaseorderObj->chkValidSampleNum($selDate, $invoiceNum);
			if ($validSONum) {
				$chkSONumExist = $purchaseorderObj->checkSampleNumExist($invoiceNum, $cSOId);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt_$splitRowId", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSPOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt_$splitRowId", "innerHTML", "");
					$objResponse->script("enableSPOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt_$splitRowId", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSPOButton($mode);");
			}
		}
		return $objResponse;
	}*/

	# SO Number Exist
	/*function chkSONumberExist($soId, $mode, $cSOId, $selDate,  $invoiceType)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj 	= new PurchaseOrder($databaseConnect);

		# Check valid SO Num				
		if ($soId) {
		$validSONum = $purchaseorderObj->chkValidSONum($selDate, $soId, $invoiceType);
			if ($validSONum) {
				$chkSONumExist = $purchaseorderObj->checkSONumberExist($soId, $cSOId, $invoiceType, $selDate);
				if ($chkSONumExist && $soId!="") {
					$objResponse->assign("divSOIdExistTxt", "innerHTML", "$soId is already in use.<br>Please choose another one");					
					$objResponse->script("disableOrderDispatchBtn($mode);");
					$objResponse->assign("validInvoiceNo","value","N");
				} else  {
					# Check for cancelled challan number
					$cancelledInvoice = $purchaseorderObj->checkCancelledInvoice($soId, $selDate, $invoiceType);
					if ($cancelledInvoice) {
						$objResponse->assign("divSOIdExistTxt", "innerHTML", "$soId is already cancelled.");			
						$objResponse->script("disableOrderDispatchBtn($mode);");
						$objResponse->assign("validInvoiceNo","value","N");
					} else {
						$objResponse->assign("divSOIdExistTxt", "innerHTML", "");	
						$objResponse->script("enableOrderDispatchBtn($mode);");
						$objResponse->assign("validInvoiceNo","value","Y");
					}
				}
			} else {
				$objResponse->assign("divSOIdExistTxt", "innerHTML", "$soId is not valid.<br>Please check the challan Settings.");
				$objResponse->script("disableOrderDispatchBtn($mode);");
				$objResponse->assign("validInvoiceNo","value","N");
			}
		}
		return $objResponse;
	}
*/
	/*function getInvoiceEntryRecs($invoiceId, $poEntryId)
	{
		//$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj 	= new PurchaseOrder($databaseConnect);
		
		$recArr = array();
		//list($invoiceEntryId, $mcInPO, $mcInInvoice)
		$recArr = $purchaseorderObj->getInvoiceRec($invoiceId, $poEntryId);
		/*
		$recArr[0] = $invoiceEntryId;
		$recArr[1] = $mcInPO;		
		$recArr[2] = $mcInInvoice;
		*/
		/*$objResponse->setReturnValue($recArr); // Set value
		return $objResponse;
	}*/

	# generate QEL
/*	function genQuickEntryList($poMainId, $userId, $lastDate)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj 	= new PurchaseOrder($databaseConnect);

		if ($poMainId && $userId) $genQel = $purchaseorderObj->genQEL($poMainId, $userId, $lastDate);
		
		return $objResponse;
	}

	# Split PF Invoice No
	function getSplitPFInvoiceNo($rowId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$purchaseorderObj	= new PurchaseOrder($databaseConnect);
		$proformaInvoiceNo 	= $purchaseorderObj->getNextProformaInvoiceNo();
		$objResponse->assign("proformaInvoiceNo_$rowId","value",$proformaInvoiceNo);
		
		if ($proformaInvoiceNo) $objResponse->script("incPFNO($rowId, $proformaInvoiceNo);");
			
		return $objResponse;
	}

	function getFrznPkgCode($rowId, $selFrznPkgCodeId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);

		$frznPkgCodeRecs = $purchaseorderObj->getFrznPkgCodeRecs();
		
		$objResponse->addDropDownOptions("selFrozenCode_$rowId", $frznPkgCodeRecs, $selFrznPkgCodeId);
		return $objResponse;
	}

	function getMCPkg($rowId, $selMCPkgId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);

		$mcPkgRecs = $purchaseorderObj->getMCPkgRecs();
		
		$objResponse->addDropDownOptions("selMCPacking_$rowId", $mcPkgRecs, $selMCPkgId);
		return $objResponse;
	}


	# PO Number Exist
	function chkPONumberExist($poNo, $mode, $cSOId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);

		$chkPONumExist = $purchaseorderObj->checkPONumberExist($poNo, $cSOId);
		if ($chkPONumExist && $poNo!="") {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "$poNo is already in use.");
			$objResponse->script("disableSPOButton($mode);");
		} else  {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "");
			$objResponse->script("enableSPOButton($mode);");
		}
		return $objResponse;
	}

	function getFrznCodes($rowId, $fishId, $processCodeId, $selFrznCodeId=null)
	{	
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		
		if ($fishId && $processCodeId) {
			$frznCodeRecs = $purchaseorderObj->qelFrzncode($fishId, $processCodeId);
			$objResponse->addDropDownOptions("selFrozenCode_$rowId", $frznCodeRecs, $selFrznCodeId);
		}
		
		return $objResponse;
	}

	function getMCPkgs($rowId, $frozenCodeId, $selMCPkgId=null)
	{	
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
				
		$mcPkgRecs = $purchaseorderObj->qelMCPkg($frozenCodeId);
		$objResponse->addDropDownOptions("selMCPacking_$rowId", $mcPkgRecs, $selMCPkgId);

		if (sizeof($mcPkgRecs)==1) {
			$mcPkgId = key($mcPkgRecs);
			$objResponse->script("updateNumMC($mcPkgId, $rowId);");
		}		

		return $objResponse;
	}

	function getCurrency($currencyId, $selDate)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$usdvalueObj	 = new USDValue($databaseConnect);
		$selDate = mysqlDateFormat($selDate);

		list($cyRateListId, $cyCode, $cyValue) = $usdvalueObj->getCYRateList($currencyId, $selDate);
		$objResponse->assign("oneUSDToINR","value",$cyValue);
		$objResponse->assign("currencyRateListId","value",$cyRateListId);
		
		return $objResponse;
	}
	*/


/*$xajax->register(XAJAX_FUNCTION, 'getProformaInvoiceNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSampleInvoiceNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkProformaNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkSampleNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkValidDespatchDate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCodes', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignBrand', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getGradeRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getFilledWt', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getNumMC', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPortList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getAgentList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'reloadCustomer', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getNumMCExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCustPaymentTerm', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkSplitPfrmaNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkSplitSampleNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkSONumberExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getInvoiceEntryRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'genQuickEntryList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSplitPFInvoiceNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getFrznPkgCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getMCPkg', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkPONumberExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getFrznCodes', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getMCPkgs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCurrency', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));*/

//$xajax->ProcessRequest();
	}
?>