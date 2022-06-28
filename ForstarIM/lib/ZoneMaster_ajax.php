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
	# get ing List
	function getCityList($stateId, $rowId, $mode, $disStateEntryId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();	
		$zoneMasterObj	= new ZoneMaster($databaseConnect);			
		if ($mode==1) {
			$cityRecords = $zoneMasterObj->filterCityRecs($stateId);
	        	$objResponse->addCreateOptions('city_'.$rowId, $cityRecords, $cId);
		} else if ($mode==2) { // Edit Mode
			$cityRecords = $zoneMasterObj->getSelectedCityList($stateId, $disStateEntryId);
	        	$objResponse->addCityOptions('city_'.$rowId, $cityRecords, $cId);
		}		
		//$objResponse->alert("$stateId, $rowId, $mode, $disStateEntryId");			
		return $objResponse;
	}

	# State Exist
	function chkZoneExist($zoneName, $mode, $zoneId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$zoneMasterObj	= 	new ZoneMaster($databaseConnect);
		
		$chkZoneExist = $zoneMasterObj->chkDuplicateEntry(trim($zoneName), $zoneId);
		//$objResponse->alert("$zoneName, $mode, $zoneId");
		if ($chkZoneExist) {
			$objResponse->assign("divStateIdExistTxt", "innerHTML", "Please make sure the zone does not exist.");
			$objResponse->script("disableAreaDemarcationBtn($mode);");
		} else  {
			$objResponse->assign("divStateIdExistTxt", "innerHTML", "");
			$objResponse->script("enableAreaDemarcationBtn($mode);");
		}		
		return $objResponse;
	}
	

$xajax->register(XAJAX_FUNCTION, 'getCityList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkZoneExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>