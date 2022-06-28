<?php
require_once("lib/databaseConnect.php");
require_once("TransporterReport_class.php");
require_once("config.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

	class NxajaxResponse extends xajaxResponse
	{
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

		// For Edit Mode
		function addCityOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $ov) {
					$this->script("addOption('".$ov[2]."','".$sSelectId."','".$ov[0]."','".$ov[1]."');");
	       			}
	     		}
  		}		
	}
	
	# Get Location List
	function getCityList($distributorId, $selCityIds)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderReportObj	= new SalesOrderReport($databaseConnect);
				
		if ($selCityIds!="") {
			$distCityListRecs = $salesOrderReportObj->getSelDistCityRecords($distributorId, $selCityIds);
			$objResponse->addCityOptions("selCity",$distCityListRecs,$selCityIds);
		} else {
			$distCityListRecs = $salesOrderReportObj->getDistributorCityRecs($distributorId);
			$objResponse->addDropDownOptions("selCity",$distCityListRecs,$selCityIds);
		}	
		return $objResponse;
	}

	# Get State List
	function getStateList($fromDate, $tillDate, $invoiceType, $selStatus, $selStateId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderReportObj	= new SalesOrderReport($databaseConnect);		
		# SO State Records
		$soStateRecords	= $salesOrderReportObj->getSOStateRecords(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);
		$objResponse->addDropDownOptions("selState", $soStateRecords, $selStateId);
		return $objResponse;
	}
	
	# Get selected distributor
	function distributorList($fromDate, $tillDate, $invoiceType, $selStatus, $selDistributorId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderReportObj	= new SalesOrderReport($databaseConnect);

		# Dist records
		$distRecords = $salesOrderReportObj->getDistributorList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);

		$objResponse->addDropDownOptions("selDistributor", $distRecords, $selDistributorId);
		return $objResponse;
	}

	# Get Transporter List
	function transporterList($fromDate, $tillDate, $invoiceType, $selStatus, $selTransporterId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderReportObj	= new SalesOrderReport($databaseConnect);
		# Transporter Records
		$transporterRecs = $salesOrderReportObj->getTransporterList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);

		$objResponse->addDropDownOptions("selTransporter", $transporterRecs, $selTransporterId);
		return $objResponse;
	}	

	# Check for valid invoice number
	function validInvoiceNo($invoiceNo)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderReportObj	= new SalesOrderReport($databaseConnect);
		//$objResponse->alert("$invoiceNo");
		$validInvoiceNo = $salesOrderReportObj->chkValidInvoiceNo($invoiceNo);
		if (!$validInvoiceNo && $invoiceNo!="") {
			$objResponse->assign("errMsg1","innerHTML","$invoiceNo is not existing in our database.");
			$objResponse->assign("hidInvoiceNumNotExist","value","Y");
		} else {
			$objResponse->assign("errMsg1","innerHTML","");
			$objResponse->assign("hidInvoiceNumNotExist","value","N");
		}
		$objResponse->script("changeSO();");
		$objResponse->assign("successMsg","innerHTML","");
		return $objResponse;
	}

	function invoiceNoExist($invoiceNo)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderReportObj	= new SalesOrderReport($databaseConnect);
		
		$validInvoiceNo = $salesOrderReportObj->chkValidInvoiceNo($invoiceNo);
		if ($validInvoiceNo && $invoiceNo!="") {
			$objResponse->assign("errMsg2","innerHTML","$invoiceNo is already in use. Please choose another one");
			$objResponse->assign("hidNewInvoiceNumExist","value","Y");
		} else {
			if ($invoiceNo!="") $validNum = $salesOrderReportObj->validSONum($invoiceNo);
			if (!$validNum && $invoiceNo!="") {
				$objResponse->assign("errMsg2","innerHTML","$invoiceNo is not a valid Invoice number.");
				$objResponse->assign("hidNewInvoiceNumExist","value","Y");
			} else {
				$objResponse->assign("errMsg2","innerHTML","");
				$objResponse->assign("hidNewInvoiceNumExist","value","N");
			}
		}
		$objResponse->script("changeSO();");
		$objResponse->assign("successMsg","innerHTML","");
		return $objResponse;
	}

	function updateNewInvoiceNo($existingInvoiceNo, $newInvoiceNo)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderReportObj	= new SalesOrderReport($databaseConnect);
		if ($existingInvoiceNo!="" && $newInvoiceNo!="") {
			$existingInvoiceId = $salesOrderReportObj->getSOId($existingInvoiceNo);
			if ($existingInvoiceId) $updateSalesOrderNo = $salesOrderReportObj->updateNewInvoiceNo($existingInvoiceId, $newInvoiceNo);
			if ($updateSalesOrderNo) $objResponse->assign("successMsg","innerHTML","Successfully updated new invoice number.");
		} else $objResponse->assign("successMsg","innerHTML","");
		return $objResponse;
	}


$xajax->registerFunction("getCityList");
$xajax->registerFunction("getStateList");
$xajax->registerFunction("distributorList");
$xajax->registerFunction("transporterList");
$xajax->registerFunction("validInvoiceNo");
$xajax->registerFunction("invoiceNoExist");
$xajax->registerFunction("updateNewInvoiceNo");

$xajax->register(XAJAX_FUNCTION, 'getCityList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getStateList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'distributorList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'transporterList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'validInvoiceNo', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'invoiceNoExist', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateNewInvoiceNo', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));


$xajax->ProcessRequest();
?>