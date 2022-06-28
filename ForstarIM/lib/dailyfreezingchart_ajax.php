<?php
//require_once("lib/databaseConnect.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();
//$xajax->configure('defaultMode', 'synchronous'); // For return value	

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
	

	function getMonitoringParams($installedCapacityId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$dailyFreezingChartObj	= new DailyFreezingChart($databaseConnect);
		$setMonitoringParam_m	= new SetMonitoringParam_model();
		
		$monitorParamRecs = array();
		if ($installedCapacityId) $monitorParamRecs = $setMonitoringParam_m->findAll(array("where"=>"installed_capacity_id='".$installedCapacityId."'", "order"=>"id asc"));
		
		$objResponse->assign("monitoringParamRow","innerHTML",'');
		return $objResponse;
	}



$xajax->registerFunction("getActiveProducts");

// showLoading, hideLoading
$xajax->register(XAJAX_FUNCTION, 'getStockUnitRate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>