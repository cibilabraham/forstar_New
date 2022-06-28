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
	}
	

	# Zone Exist
	function chkZoneExist($zoneName, $mode, $zoneId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();	
		$salesZoneObj		= new SalesZoneMaster($databaseConnect);
		
		$chkZoneExist = $salesZoneObj->chkDuplicateEntry(addSlash(trim($zoneName)), $zoneId);
		if ($chkZoneExist) {
			$objResponse->assign("divZoneExistTxt", "innerHTML", "Please make sure the zone does not exist.");
			$objResponse->script("disableSalesZoneBtn($mode);");
		} else  {
			$objResponse->assign("divZoneExistTxt", "innerHTML", "");
			$objResponse->script("enableSalesZoneBtn($mode);");
		}		
		return $objResponse;
	}
	


$xajax->register(XAJAX_FUNCTION, 'chkZoneExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>