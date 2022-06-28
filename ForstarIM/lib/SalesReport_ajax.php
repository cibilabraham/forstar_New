<?php
require_once("lib/databaseConnect.php");
require_once("SalesReport_class.php");
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
		$salesReportObj	= new SalesReport($databaseConnect);
				
		if ($selCityIds!="") {
			$distCityListRecs = $salesReportObj->getSelDistCityRecords($distributorId, $selCityIds);
			$objResponse->addCityOptions("selCity",$distCityListRecs,$selCityIds);
		} else {
			$distCityListRecs = $salesReportObj->getDistributorCityRecs($distributorId);
			$objResponse->addDropDownOptions("selCity",$distCityListRecs,$selCityIds);
		}	
		return $objResponse;
	}

	# Get State List
	function getStateList($fromDate, $tillDate, $invoiceType, $selStatus, $selStateId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesReportObj	= new SalesReport($databaseConnect);		
		# SO State Records
		$soStateRecords	= $salesReportObj->getSOStateRecords(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);
		$objResponse->addDropDownOptions("selState", $soStateRecords, $selStateId);
		return $objResponse;
	}
	
	# Get selected distributor
	function distributorList($fromDate, $tillDate, $invoiceType, $selStatus, $selDistributorId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesReportObj	= new SalesReport($databaseConnect);

		# Dist records
		$distRecords = $salesReportObj->getDistributorList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);

		$objResponse->addDropDownOptions("selDistributor", $distRecords, $selDistributorId);
		return $objResponse;
	}

	# Get Transporter List
	function transporterList($fromDate, $tillDate, $invoiceType, $selStatus, $selTransporterId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesReportObj	= new SalesReport($databaseConnect);
		# Transporter Records
		$transporterRecs = $salesReportObj->getTransporterList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);

		$objResponse->addDropDownOptions("selTransporter", $transporterRecs, $selTransporterId);
		return $objResponse;
	}	
	

	function zoneList($fromDate, $tillDate, $invoiceType, $selStatus, $selZoneId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesReportObj	= new SalesReport($databaseConnect);
		# Transporter Records
		$zoneRecs = $salesReportObj->getZoneList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);

		$objResponse->addDropDownOptions("selZone", $zoneRecs, $selZoneId);
		return $objResponse;
	}

	function soCityList($fromDate, $tillDate, $invoiceType, $selStatus, $selCityId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesReportObj	= new SalesReport($databaseConnect);
		# Transporter Records
		$cityRecs = $salesReportObj->getSOCityRecs(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $selStatus);

		$objResponse->addDropDownOptions("selSOCity", $cityRecs, $selCityId);
		return $objResponse;
	}


$xajax->registerFunction("getCityList");
$xajax->registerFunction("getStateList");
$xajax->registerFunction("distributorList");
$xajax->registerFunction("transporterList");
$xajax->registerFunction("zoneList");
$xajax->registerFunction("soCityList");


$xajax->register(XAJAX_FUNCTION, 'getCityList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getStateList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'distributorList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'transporterList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'zoneList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'soCityList', array('onResponseDelay' => 'showLoading','onComplete' => 'hideLoading'));


$xajax->ProcessRequest();
?>