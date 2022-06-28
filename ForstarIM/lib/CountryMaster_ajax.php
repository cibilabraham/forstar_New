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

	

	# Duplicate Exist
	function chkCountryExist($countryName, $mode, $countryId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$countryMasterObj	= new CountryMaster($databaseConnect);		

		$chkCountryExist = $countryMasterObj->chkCountryExist(trim($countryName), $countryId);

		if ($chkCountryExist) {
			$objResponse->assign("msgCountryExist", "innerHTML", "The Country you have entered is already in database.");
			$objResponse->script("disableCountryMasterButton($mode);");
		} else  {
			$objResponse->assign("msgCountryExist", "innerHTML", "");
			$objResponse->script("enableStateVatButton($mode);");
		}
		return $objResponse;
	}

	# Check port duplicate entry
	function chkPortExist($portName, $rowId, $portEntryId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$countryMasterObj	= new CountryMaster($databaseConnect);		
		
		$chkPortExist = $countryMasterObj->chkCountryPortExist(trim($portName), $portEntryId);

		if ($chkPortExist) {
			$objResponse->assign("portExist_$rowId", "value", true);
			$objResponse->assign("row_$rowId", "style.backgroundColor", "#ef9595");
		} else  {
			$objResponse->assign("portExist_$rowId", "value", false);
			$objResponse->assign("row_$rowId", "style.backgroundColor", "");
		}
		return $objResponse;
	}




$xajax->registerFunction("chkCountryExist");
$xajax->registerFunction("chkPortExist");

$xajax->ProcessRequest();
?>