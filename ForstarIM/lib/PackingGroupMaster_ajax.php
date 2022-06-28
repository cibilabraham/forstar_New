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

	# Rec Exist
	function chkSelRecExist($selPStateL, $selPGroupL, $selNetWtL, $selPStateR, $selPGroupR, $selNetWtR, $mode, $packingGroupEntryId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$packingGroupMasterObj	= new PackingGroupMaster($databaseConnect);
		if ($selPStateL!="" && $selPGroupL!="" && $selNetWtL!="") $pSelLeft	= $selPStateL.",".$selPGroupL.",".$selNetWtL; // State,group, net wt
		else $pSelLeft = "";
		if ($selPStateR!="" && $selPGroupR!="" && $selNetWtR!="") $pSelRight	= $selPStateR.",".$selPGroupR.",".$selNetWtR; // State,group, net wt
		else $pSelRight = "";
		if ($pSelLeft!="" && $pSelRight!="") {
			$chkRecExist 	= $packingGroupMasterObj->checkPackingGroupRecExist($pSelLeft, $pSelRight, $packingGroupEntryId);
		}
		if ($chkRecExist) {
			$objResponse->assign("divStateIdExistTxt", "innerHTML", "Please make sure the selected Packing Group is not existing.");
			$objResponse->script("disableStateVatButton($mode);");
		} else  {
			$objResponse->assign("divStateIdExistTxt", "innerHTML", "");
			$objResponse->script("enableStateVatButton($mode);");
		}
		//sleep(1);
		return $objResponse;
	}

	#--
	# Get / check Product Group Exist
	function getProductGroupExist($productStateId, $rowId, $selId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$packingGroupMasterObj	= new PackingGroupMaster($databaseConnect);

		# Checking Prouct Group Exist
		$productGroupExist = $packingGroupMasterObj->checkProductGroupExist($productStateId);
		# Product Group Records
		$productGroupRecords = $packingGroupMasterObj->filterProductGroupList($productGroupExist);
		$objResponse->addCreateOptions("selPGroup_".$rowId, $productGroupRecords,$selId);
		$objResponse->assign("pGroupExist_".$rowId, "value", $productGroupExist);
		return $objResponse;			
	}

	# Get Net Wt Recs
	function getNetWtRecs($pStateId, $pGroupId, $rowId, $selNetWt)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$packingGroupMasterObj	= new PackingGroupMaster($databaseConnect);
		//$objResponse->alert("st=$pStateId, Gr=$pGroupId, Row=$rowId");
		$pNetWtRecs = $packingGroupMasterObj->fetchProductNetWtRecs($pStateId, $pGroupId);
		$objResponse->addCreateOptions("selNetWt_".$rowId, $pNetWtRecs, $selNetWt);
		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'chkSelRecExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProductGroupExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getNetWtRecs', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>