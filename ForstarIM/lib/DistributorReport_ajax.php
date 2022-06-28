<?php
//require_once("lib/databaseConnect.php");
//require_once("DistributorReport_class.php");
//require_once("config.php");
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
	}	

	/*
	# Get Sales Orders
	function getDistributorList($fromDate, $tillDate, $selDistId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$distributorReportObj	= new DistributorReport($databaseConnect);
		
		$distRecs = $distributorReportObj->getSalesOrders(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType);
		$objResponse->addDropDownOptions("selSOId", $salesOrders, $selDistId);
		return $objResponse;
	}
	*/

	# Get Dist AC Invoice
	function getDistACInvoice($fromDate, $tillDate, $selDistributorId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$distributorReportObj	= new DistributorReport($databaseConnect);
		
		# inv recs
		$invRecs = $distributorReportObj->distACInvoiceRecs(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $selDistributorId);
		
		$objResponse->addDropDownOptions("invoiceFilter", $invRecs, $selDistId);
		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'getDistACInvoice', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>