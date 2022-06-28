<?php
require_once("lib/databaseConnect.php");
require_once("dailycatchreport_class.php");
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

	
	# SO Number Exist
	function updateBillingCompanyRec($weighNumber, $billingCompanyId, $challanConfirm)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailycatchreportObj 	= new DailyCatchReport($databaseConnect);
		$chkSONumExist = $dailycatchreportObj->updateBillingCompanyRec($weighNumber, $billingCompanyId);		
		return $objResponse;
	}

	function chkReportConfirm($challanMainId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailycatchreportObj 	= new DailyCatchReport($databaseConnect);
		$confirm = $dailycatchreportObj->chkChallanConfirmed($challanMainId);
		if ($confirm==1) {			
			$objResponse->script("disableConfirmButton();");
		} else  {			
			$objResponse->script("enableConfirmButton();");
		}
		return $objResponse;
	}

	function getBillingCompanyRecs($weighNumber, $cBillingCompanyId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailycatchreportObj 	= new DailyCatchReport($databaseConnect);		
		$billingCompanyRecs	= $dailycatchreportObj->getSelBillingCompanyRecs($weighNumber);
		if (sizeof($billingCompanyRecs)==1) {
			$cBillingCompanyId= $dailycatchreportObj->getBillingCompanyId($weighNumber);
		}
		# Get Main Id
		$cMainId = $dailycatchreportObj->getChallanMainId($weighNumber, $cBillingCompanyId);
		$objResponse->assign("challanMainId", "value", $cMainId);
		$objResponse->addDropDownOptions("selBillingCompany", $billingCompanyRecs, $cBillingCompanyId);
		return $objResponse;
	}

	function updateRMPMemoPrintCount($challanMainId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$dailycatchreportObj 	= new DailyCatchReport($databaseConnect);
		$updateCount 		= $dailycatchreportObj->updateRMPMemoPrintCount($challanMainId);		
		return $objResponse;
	}

function getBillCompany($selectSupplier, $fromDate, $tillDate)
	{
		$objResponse->alert("hai");
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		//$purchaseorderObj = new PurchaseOrder($databaseConnect);
		$dailycatchreportObj 	= new DailyCatchReport($databaseConnect);
		//$portRecs = $purchaseorderObj->getPortRecs($countryId);
		$billingCompanyRecords =$dailycatchreportObj->fetchBillingCompanyRecords($selectSupplier, $fromDate, $tillDate);
		$objResponse->addDropDownOptions("billingCompany", $billingCompanyRecords, $selPortId);
		//$objResponse->addDropDownOptions("selPort", $portRecs, $selPortId);
		//return $objResponse;
	}
$xajax->registerFunction("updateBillingCompanyRec");
$xajax->registerFunction("chkReportConfirm");
$xajax->registerFunction("getBillingCompanyRecs");
$xajax->registerFunction("updateRMPMemoPrintCount");

$xajax->register(XAJAX_FUNCTION, 'getBillingCompanyRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkReportConfirm', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getBillCompany', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();
?>