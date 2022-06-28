<?php
/*
require_once("lib/databaseConnect.php");
*/
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	


	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}		
	}

	# Pkg Instruction Table updated
	function updatePkgInsEditingTime($mainId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$packingInstructionObj	= new PackingInstruction($databaseConnect);
		if ($mainId) {			
			$updateMainRec = $packingInstructionObj->updatePkgInstEditingRec($mainId, '', 'U');	
		}
		return $objResponse;
	}
	
	#Change Pre-Processor Status
	# Processor Master Section
	function changeProcessorStatus($processorId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		$preprocessorObj = new PreProcessor($databaseConnect);
		# Get Current Status
		$cStatus = $preprocessorObj->getProcessorCurrentStatus($processorId);
		$processorStatus = ($cStatus=='Y')?'N':'Y';
		if ($processorId) $uptdStatus = $preprocessorObj->updateProcessorStatus($processorId, $processorStatus);
		if ($uptdStatus) {
			sleep(1);
			$selStatus = $preprocessorObj->getProcessorCurrentStatus($processorId);
			$assignRow = "";
			if ($selStatus=='Y') {
				$assignRow = "<a href='###' class='link5'><img src='images/y.png' onMouseover=\"ShowTip('Click here to Inactive');\" onMouseout=\"UnTip();\" onclick=\"return validateUptdStatus('$processorId','$rowId');\" border='0'/></a>";
			} else {
				$assignRow = "<a href='###' class='link5'><img src='images/x.png' onMouseover=\"ShowTip('Click here to active');\" onMouseout=\"UnTip();\" onclick=\"return validateUptdStatus('$processorId','$rowId');\" border='0'/></a>";
			}		
			$objResponse->assign("statusRow_".$rowId, "innerHTML", $assignRow);			
		}
		return $objResponse;
	}

	#Change Pre-Processor Status
	# Processor Master Section
	function changeSupplierStatus($supplierId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		$supplierMasterObj =	new SupplierMaster($databaseConnect);
		# Get Current Status
		$cStatus = $supplierMasterObj->getSupplierCurrentStatus($supplierId);
		$supplierStatus = ($cStatus=='Y')?'N':'Y';
		
		if ($supplierId) $uptdStatus = $supplierMasterObj->updateSupplierStatus($supplierId, $supplierStatus);
		if ($uptdStatus) {
			sleep(1);
			$selStatus = $supplierMasterObj->getSupplierCurrentStatus($supplierId);
			$assignRow = "";
			if ($selStatus=='Y') {
				$assignRow = "<a href='###' class='link5'><img src='images/y.png' onMouseover=\"ShowTip('Click here to Inactive');\" onMouseout=\"UnTip();\" onclick=\"return validateSuppStatus('$supplierId','$rowId');\" border='0'/></a>";
			} else {
				$assignRow = "<a href='###' class='link5'><img src='images/x.png' onMouseover=\"ShowTip('Click here to active');\" onMouseout=\"UnTip();\" onclick=\"return validateSuppStatus('$supplierId','$rowId');\" border='0'/></a>";
			}		
			$objResponse->assign("statusRow_".$rowId, "innerHTML", $assignRow);			
		}
		return $objResponse;
	}

	function chkLoginStatus()
	{
		$objResponse 	 = new NxajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		if ($_SESSION["userId"]=="") $objResponse->script("doLogout();");
		return $objResponse;
	}

	function changeCompanyStatus($companyId, $rowId)
	{

		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$billingCompanyObj	= new BillingCompanyMaster($databaseConnect);
		# Get Current Status
		$cStatus = $billingCompanyObj->getCompanyCurrentStatus($companyId);
		

		$companyStatus = ($cStatus=='Y')?'N':'Y';
		
		if ($companyId) $uptdStatus = $billingCompanyObj->updateCompanyStatus($companyId, $companyStatus);
		if ($uptdStatus) {
			sleep(1);
			$selStatus = $billingCompanyObj->getCompanyCurrentStatus($companyId);
			$assignRow = "";
			if ($selStatus=='Y') {
				$assignRow = "<a href='###' class='link5'><img src='images/y.png' onMouseover=\"ShowTip('Click here to Inactive Dr');\" onMouseout=\"UnTip();\" onclick=\"return validateCompanyStatus('$companyId','$rowId');\" border='0'/></a>";
			} else {
				$assignRow = "<a href='###' class='link5'><img src='images/x.png' onMouseover=\"ShowTip('Click here to Active Dr');\" onMouseout=\"UnTip();\" onclick=\"return validateCompanyStatus('$companyId','$rowId');\" border='0'/></a>";
			}		
			$objResponse->assign("statusRow_".$rowId, "innerHTML", $assignRow);			
		}
		return $objResponse;
	}

$xajax->registerFunction("updatePkgInsEditingTime");
$xajax->registerFunction("changeProcessorStatus");
$xajax->registerFunction("changeSupplierStatus");
$xajax->registerFunction("chkLoginStatus");

$xajax->registerFunction("changeCompanyStatus");
$xajax->register(XAJAX_FUNCTION, 'updatePkgInsEditingTime', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'changeProcessorStatus', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'changeSupplierStatus', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkLoginStatus', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>