<?php
require_once("libjs/xajax_core/xajax.inc.php");
require_once 'components/base/CommonReasonChkList_model.php';
require_once 'components/base/CommonReason_model.php';

$xajax = new xajax();
//$xajax->configure('defaultMode', 'synchronous'); // For return value	

	class NxajaxResponse extends xajaxResponse
	{
		//$cId - Hidden field
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}

		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}		
	}
	
	# Get All Distributor
	function getDistributor($fromDate, $tillDate, $selDistributorId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		
		if ($fromDate!="" && $tillDate!="") {
			$distributorRecs = $distributorAccountObj->getDistributorList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate));
			$objResponse->addCreateOptions("distributorFilter", $distributorRecs, "hidDistributorFilterId");
		}
		return $objResponse;
	}

	# Get Distributor Invoices
	function getInvoices($selField, $distributorId, $cityId, $selInvoiceId, $filter)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);			
		if ($cityId=="") {
			$cityRecs = $distributorAccountObj->getCityRec($distributorId);
			if (sizeof($cityRecs)==1) $cityId = $cityRecs[0][0];
		}
		
		if ($filter) $invoiceRecs[] = "--Select All--";	
		else $invoiceRecs[] = "--Select--";	
		if ($distributorId) {
			$invoiceRecs = $distributorAccountObj->getInvoiceRecs($distributorId, $cityId, $filter);
			$objResponse->addDropDownOptions($selField, $invoiceRecs, $selInvoiceId);
		}		
		$objResponse->addDropDownOptions($selField, $invoiceRecs, $selInvoiceId);
		return $objResponse;
	}
	
	# City List
	function cityList($selField, $distributorId, $selCityId, $filter)
	{
		$objResponse 	= new NxajaxResponse();
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		
		$distCityListRecs = $distributorAccountObj->distributorCityRecs($distributorId, $filter);
		$objResponse->addDropDownOptions($selField, $distCityListRecs, $selCityId);
		//$objResponse->addDropDownOptions("selCity",$distCityListRecs,$selCityId);
		return $objResponse;
	}

	# common Reason
	function commonReasonRecs($commonReasonId)
	{
		$objResponse 	= new NxajaxResponse();
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		$crChkList_m = new CommonReasonChkList_model();

		# Get chk List recs
		$chkListRecs = $crChkList_m->findAll(array("where"=>"common_reason_id='".$commonReasonId."'", "order"=>"id asc"));

		# payment Received entry
		$paymentReceivedEntry = $distributorAccountObj->DefaultReasonEntry($commonReasonId);

		$displayHtml = "";
		if (sizeof($chkListRecs)>0) {
			$displayHtml  = "<table><TR><TD><fieldset style='padding:10px;'><legend class='listing-item'>Check List</legend>";
			$displayHtml .= "<table cellpadding='2' width='90%' cellspacing='1' border='0' align='center' bgcolor='#999999'>";
			$displayHtml .= "<TR bgcolor='#f2f2f2' align='center'><TD>&nbsp;</TD><TD class='listing-head' nowrap>Check List</TD></TR>";
			$j = 0;
			foreach ($chkListRecs as $clr) {
				$j++;
				//$chked = (in_array($clr->id,$chkListArr))?"checked":"";
			$displayHtml .= "<TR bgcolor='White'><TD>";
			$displayHtml .= "<INPUT type='checkbox' name='chkListId_$j' id='chkListId_$j' class='chkBox' value='".$clr->id."' $chked/>";
			$displayHtml .= "<INPUT type='hidden' name='required_$j' id='required_$j' value='".$clr->required."' readonly />";
			$displayHtml .= "<INPUT type='hidden' name='chkListName_$j' id='chkListName_$j' value='".$clr->name."' readonly />";
			$displayHtml .= "</TD>";
			$displayHtml .= "<TD class='listing-item' nowrap style='padding-left:5px; padding-right:5px;'>";
			if ($clr->required=='Y') $displayHtml .= "*";
			$displayHtml .= $clr->name;
			$displayHtml .= "</TD></TR>";
			} // Chk List Recs Ends here
			$displayHtml .= "</table></fieldset><input type='hidden' name='chkListRowCount' id='chkListRowCount' value='$j' readonly /></TD></TR></table>";
		} // Chk Ends here
		$objResponse->assign("chkListRow","innerHTML", $displayHtml);
		$objResponse->assign("hidPaymentReceived","value", $paymentReceivedEntry);
		if ($paymentReceivedEntry) {
			$objResponse->script("etOption('$paymentReceivedEntry');");
		} else $objResponse->script("etOption('');");
		return $objResponse;
	}

	function getCommonReason($entryType)
	{
		$objResponse 	= new NxajaxResponse();
		$databaseConnect= new DatabaseConnect();
		$comReason_m = new CommonReason_model();
			
		if ($entryType=="AD") $eType = "D";
		else if ($entryType=="AC") $eType = "C";
		else $eType = "";		
		
		$crRecs = array();
		if ($eType) {
			$crRecs = $comReason_m->findAllForSelect("id", "reason", "--Select--", array("where"=>"cod='$eType'", "order"=>"default_entry desc, reason asc"));
			$crRecs["OT"] = "OTHER";
		} else $crRecs[] = "--Select--";

		$objResponse->addDropDownOptions("commonReason", $crRecs, "");
		return $objResponse;
	}

	# Get City Filter List
	function cityFilterList($fromDate, $tillDate, $distributorId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		
		if ($fromDate!="" && $tillDate!="") {
			$cityRecs = $distributorAccountObj->getCityFilterList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $distributorId);
			$objResponse->addDropDownOptions("cityFilter", $cityRecs, $selDistributorId);
		}
		sleep(1);
		return $objResponse;
	}

	# Get Pendin cheques
	function pendingCheques($distributorId, $cityId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		
		if ($cityId=="") {
			$cityRecs = $distributorAccountObj->getCityRec($distributorId);
			if (sizeof($cityRecs)==1) $cityId = $cityRecs[0][0];
		}

		$pendingRecs = $distributorAccountObj->getPendingCheques($distributorId, $cityId, 1);
		
		$objResponse->addDropDownOptions("pendingCheque", $pendingRecs, '');
		return $objResponse;
	}

	# Get Filtered Invoice List
	function invFilterList($fromDate, $tillDate, $distributorId, $cityId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		
		if ($fromDate!="" && $tillDate!="" && $distributorId!="") {
			$invoiceFilterList = $distributorAccountObj->getInvoiceFilteredList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $distributorId, $cityId);
			$objResponse->addDropDownOptions("invoiceFilter", $invoiceFilterList, '');
		}
		sleep(1);
		return $objResponse;
	}

	function updateDistDebNCrAmt()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$changesUpdateMasterObj	= 	new ChangesUpdateMaster($databaseConnect, $salesOrderObj, $taxMasterObj, $marginStructureObj, $distMarginStructureObj, $distMarginRateListObj, $manageRateListObj);
		$changesUpdateMasterObj->uptdDistAcDebitNCreditAmt();
		return $objResponse;
	}

	function invValue($invoiceId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		list($invAmt,$despatchDate) = $distributorAccountObj->getInvValue($invoiceId);
		if ($despatchDate!="" || $despatchDate!='0000-00-00') $despatchDate = dateFormat($despatchDate);
		if ($invoiceId) $pendingAmt = $distributorAccountObj->pendingAmt($invoiceId);

		$disInvVal = "";
		$displayHtml = "";
		if ($invAmt!=0) {
			//$invVal1 = "Invoice Value";
			//$invVal2 = $invAmt;
			$displayHtml  = "<table align=left>";		
			$displayHtml .= "<tr align=center>";
			$displayHtml .= "<td class=fieldName style='line-height:normal;'>Invoice Value</td>";
			$displayHtml .= "<td class=listing-item align=right>$invAmt</td>";
			$displayHtml .= "</tr>";
			$displayHtml .= "<tr align=center>";
			$displayHtml .= "<td class=fieldName style='line-height:normal;'>Pending<br/>Payment Value</td>";
			$displayHtml .= "<td class=listing-item align=right>$pendingAmt</td>";
			$displayHtml .= "</tr>";
			$displayHtml .= "</table>";
		}
		//$objResponse->assign("invVal1","innerHTML", $invVal1);
		//$objResponse->assign("invVal2","innerHTML", $invVal2);
		$objResponse->assign("despatchDate","value", $despatchDate);
		$objResponse->assign("balDueAmt","value", $pendingAmt);
		$objResponse->assign("singleInvRef","innerHTML", $displayHtml);
		$objResponse->script("chkAdvAmt();");
		
		return $objResponse;
	}

	# ref invoice Allocation, ADV - Advance
	function refInvVal($rowId, $invoiceId)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		
		
			list($invAmt,$despatchDate) = $distributorAccountObj->getInvValue($invoiceId);
			if ($despatchDate!="" || $despatchDate!='0000-00-00') $despatchDate = dateFormat($despatchDate);
			$pendingAmt = $distributorAccountObj->pendingAmt($invoiceId);
			
			$displayPendingAmt = $pendingAmt;
			if ($pendingAmt<0) {
				$displayPendingAmt = "(Cr) ".abs($pendingAmt);
				$objResponse->script("document.getElementById('refAmt_$rowId').readOnly=true;");
			} else if ($pendingAmt==0) {
				$objResponse->script("document.getElementById('refAmt_$rowId').readOnly=true;");
			} else $objResponse->script("document.getElementById('refAmt_$rowId').readOnly=false;");
		
		$objResponse->assign("refInvAmt_$rowId","value", $invAmt);
		$objResponse->assign("refAmt_$rowId","value", $displayPendingAmt);
		$objResponse->assign("hidDespatchDate_$rowId","value", $despatchDate);
		$objResponse->assign("hidBalDueAmt_$rowId","value", $pendingAmt);
		$objResponse->assign("hidRefInvId_$rowId","value", $invoiceId);
		
				
		$objResponse->script("calcPendingAmt();");
		$objResponse->script("chkBalAsAdvAmt();");

		return $objResponse;
	}

	function assignDistFilter($distributorId)
	{
		$objResponse 	= new NxajaxResponse();	
		$objResponse->assign("hidDistributorFilterId","value", $distributorId);
		return $objResponse;
	}
	
	# Get Dist Dtls
	function distDtls($distributorId)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		//$distributorAccountObj 	= new DistributorAccount($databaseConnect);
		$salesOrderObj		= new SalesOrder($databaseConnect);
		
		# Dist Master rec
		list($creditLimit, $creditPeriod, $totOutStandAmt, $crPeriodFrom) = $salesOrderObj->getDistMasterRec($distributorId);
		
		$objResponse->assign("creditPeriod","value", $creditPeriod);
		$objResponse->assign("crPeriodFrom","value", $crPeriodFrom);
		
		return $objResponse;
	}

	function refreshDistAC()
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj 	= new DistributorAccount($databaseConnect);

		$uptdRec = $distributorAccountObj->updateAllDistAC();
		if ($uptdRec) {
			$objResponse->alert("Successfully updated all distributor account entry.");
			$objResponse->script("document.getElementById('frmDistributorAccount').submit();");
		}

		return $objResponse;
	}

	
	function filterRefInv($selDistributorId, $selCity, $defaultReasonType, $selMode, $tableRowCount)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);

		$invoiceRecs	= $distributorAccountObj->getInvoiceRecs($selDistributorId, $selCity, '', $defaultReasonType, $selMode);

		for ($i=0; $i<=$tableRowCount; $i++) {
			$objResponse->addCreateOptions("refInv_".$i, $invoiceRecs, "hidRefInvId_".$i);
		}
			
		return $objResponse;
	}

	function getDistBankAC($distributorId, $cityId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorMasterObj = new DistributorMaster($databaseConnect);
		
		$distBankACRecs = $distributorMasterObj->fetchAllDistBankACs($distributorId, $cityId);
		$resultArr = array(''=>'--Select--');
		foreach ($distBankACRecs as $dbr) {
			$distBankId 	= $dbr[0];
			$distDefaultAC	= $dbr[4];
			$distBankName	= $dbr[5];
			$resultArr[$distBankId] = $distBankName;
			if ($distDefaultAC=='Y') $selDistBankId = $distBankId;
		}

		$objResponse->addDropDownOptions("distBankAccount", $resultArr, $selDistBankId);

		return $objResponse;
	}

	/**
	* Distributor Wise Overdue amt
	*/
	function overdueAmt($distributorId, $mode)
	{
		# Current Financial Year
		$dateFrom = date("Y-m-d", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
		$dateTill = date("Y-m-d");

		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$distributorAccountObj = new DistributorAccount($databaseConnect);
		$dashboardManagerObj   = new ManageDashboard($databaseConnect);

		$overdueAmt = $distributorAccountObj->overdueAmt($dateFrom, $dateTill, $distributorId);
		
		# Dashboard setting
		list($pChqDays, $crBalDisplayLimit, $overdueDisplayLimit) = $dashboardManagerObj->getPendingChqDisplayDays();

		$objResponse->assign("overdueAmt","value",$overdueAmt);
		if ($overdueAmt && $overdueAmt>(float)$overdueDisplayLimit) {
			$objResponse->assign("overdueAmtMsg","innerHTML","The selected distributor Advance Payment entry is restricted due to the Overdue amount <b>(Rs.".number_format($overdueAmt,2,'.',',').")</b>.<br>Please choose another distributor. ");
			$objResponse->script("disableDistACBtn($mode);");
		} else {
			$objResponse->assign("overdueAmtMsg","innerHTML","");	
			$objResponse->script("enableDistACBtn($mode);");	
		}
		return $objResponse;
	}
	



//$xajax->registerFunction("commonReasonRecs");
//$xajax->registerFunction("getCommonReason");
$xajax->registerFunction("updateDistDebNCrAmt");
$xajax->registerFunction("invValue");
$xajax->registerFunction("assignDistFilter");
$xajax->registerFunction("distDtls");
$xajax->registerFunction("filterRefInv");
$xajax->registerFunction("getDistBankAC");

$xajax->register(XAJAX_FUNCTION, 'getDistributor', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getInvoices', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'cityList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'cityFilterList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'pendingCheques', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'invFilterList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'refInvVal', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'refreshDistAC', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'overdueAmt', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>