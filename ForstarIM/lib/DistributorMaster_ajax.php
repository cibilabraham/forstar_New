<?php
require_once("lib/databaseConnect.php");
require_once('DistributorMaster_class.php');
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
	# get City List
	function getCityList($stateId, $rowId, $mode, $disStateEntryId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$distributorMasterObj = new DistributorMaster($databaseConnect);
		//$objResponse->alert("$stateId, $rowId, $mode, $disStateEntryId");
		if ($mode==1) {
			$cityRecords = $distributorMasterObj->filterCityRecs($stateId);
	        	$objResponse->addCreateOptions('city_'.$rowId, $cityRecords, $cId);
		} else if ($mode==2) { // Edit Mode
			$cityRecords = $distributorMasterObj->getSelectedCityList($stateId, $disStateEntryId);
	        	$objResponse->addCityOptions('city_'.$rowId, $cityRecords, $cId);
		}		
		$objResponse->script("displayTaxType($rowId);");			
		return $objResponse;
	}

	# get Area List
	function getAreaList($cityId, $rowId, $mode, $disAreaEntryId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$distributorMasterObj = new DistributorMaster($databaseConnect);
		if ($mode==1) {
			$areaRecords = $distributorMasterObj->filterAreaRecs($cityId);
	        	$objResponse->addCreateOptions('area_'.$rowId, $areaRecords, $cId);
		} else if ($mode==2) { // Edit Mode
			$areaRecords = $distributorMasterObj->getSelectedAreaList($cityId, $disAreaEntryId);
	        	$objResponse->addCityOptions('area_'.$rowId, $areaRecords, $cId);
		}		
		$objResponse->script("displayTaxType($rowId);");			
		return $objResponse;
	}

	
	# Octroi Applicable check	
	function chkOctroi($stateId, $cityId, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$distributorMasterObj = new DistributorMaster($databaseConnect);

		$octroiApplicable = $distributorMasterObj->chkOctroi($stateId, $cityId);
		if ($octroiApplicable) $objResponse->script("chkOctroi($rowId, 'Y')");
		else $objResponse->script("chkOctroi($rowId,'N')");
		return $objResponse;
	}

	# Check Enty Tax available
	function chkEntryTax($stateId, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$distributorMasterObj = new DistributorMaster($databaseConnect);

		$entryTaxApplicable = $distributorMasterObj->chkEntryTax($stateId);
		if ($entryTaxApplicable) $objResponse->script("chkEntryTax($rowId, 'Y')");
		else $objResponse->script("chkEntryTax($rowId,'N')");
		return $objResponse;
	}

	#Change Pre-Processor Status
	# Processor Master Section
	function changeDistStatus($distributorId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		$distributorMasterObj = new DistributorMaster($databaseConnect);

		# Get Current Status
		$cStatus = $distributorMasterObj->getDistCurrentStatus($distributorId);
		$distStatus = ($cStatus=='Y')?'N':'Y';
		if ($distributorId) $uptdStatus = $distributorMasterObj->updateDistStatus($distributorId, $distStatus);
		if ($uptdStatus) {
			sleep(1);
			$selStatus = $distributorMasterObj->getDistCurrentStatus($distributorId);
			$assignRow = "";
			if ($selStatus=='Y') {
				$assignRow = "<a href='###' class='link5'><img src='images/y.png' onMouseover=\"ShowTip('Click here to Inactive');\" onMouseout=\"UnTip();\" onclick=\"return validateDistStatus('$distributorId','$rowId');\" border='0'/></a>";
			} else {
				$assignRow = "<a href='###' class='link5'><img src='images/x.png' onMouseover=\"ShowTip('Click here to active');\" onMouseout=\"UnTip();\" onclick=\"return validateDistStatus('$distributorId','$rowId');\" border='0'/></a>";
			}		
			$objResponse->assign("statusRow_".$rowId, "innerHTML", $assignRow);			
		}
		return $objResponse;
	}
	

$xajax->register(XAJAX_FUNCTION, 'getCityList', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getAreaList', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkOctroi', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkEntryTax', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'changeDistStatus', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>