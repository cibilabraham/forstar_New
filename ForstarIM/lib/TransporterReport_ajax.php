<?php
require_once("lib/databaseConnect.php");
require_once("TransporterReport_class.php");
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
		$transporterReportObj	= new TransporterReport($databaseConnect);
				
		if ($selCityIds!="") {
			$distCityListRecs = $transporterReportObj->getSelDistCityRecords($distributorId, $selCityIds);
			$objResponse->addCityOptions("selCity",$distCityListRecs,$selCityIds);
		} else {
			$distCityListRecs = $transporterReportObj->getDistributorCityRecs($distributorId);
			$objResponse->addDropDownOptions("selCity",$distCityListRecs,$selCityIds);
		}	
		return $objResponse;
	}


	# Get State List
	# $statusType => SD-Settled, NS -not settled, PD - paid
	function getStateList($fromDate, $tillDate, $invoiceType, $statusType, $selStateId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$transporterReportObj	= new TransporterReport($databaseConnect);		
		# SO State Records
		$stateRecords	= $transporterReportObj->getStateRecords(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $statusType);
		$objResponse->addDropDownOptions("selState", $stateRecords, $selStateId);
		return $objResponse;
	}
	
	# Get selected distributor
	function distributorList($fromDate, $tillDate, $invoiceType, $statusType, $selDistributorId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$transporterReportObj	= new TransporterReport($databaseConnect);
		# Dist records
		$distRecords = $transporterReportObj->getDistributorList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $statusType);

		$objResponse->addDropDownOptions("selDistributor", $distRecords, $selDistributorId);
		return $objResponse;
	}

	# Get Transporter List
	function transporterList($fromDate, $tillDate, $invoiceType, $statusType, $selTransporterId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$transporterReportObj	= new TransporterReport($databaseConnect);
		# Transporter Records
		$transporterRecs = $transporterReportObj->getTransporterList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType, $statusType);

		$objResponse->addDropDownOptions("selTransporter", $transporterRecs, $selTransporterId);
		return $objResponse;
	}
	

$xajax->registerFunction("getCityList");
$xajax->registerFunction("getStateList");
$xajax->registerFunction("distributorList");
$xajax->registerFunction("transporterList");


$xajax->register(XAJAX_FUNCTION, 'getCityList', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getStateList', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'distributorList', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'transporterList', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));


$xajax->ProcessRequest();
?>