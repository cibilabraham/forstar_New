<?php
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
	

	# Get Dist AC Invoice
	function getShippingCompany($fromDate, $tillDate, $selShippingLineId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$shipmentInvoiceReportObj		= new ShipmentInvoiceReport($databaseConnect);
		
		# inv recs
		$recs = $shipmentInvoiceReportObj->getShippingLineRecs(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate));
		
		$objResponse->addDropDownOptions("selShippingLine", $recs, $selShippingLineId);
		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'getShippingCompany', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>