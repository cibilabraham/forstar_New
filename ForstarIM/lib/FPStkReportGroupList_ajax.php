<?php
require_once("libjs/xajax_core/xajax.inc.php");

	$xajax = new xajax();	
	//$xajax->configure('defaultMode', 'synchronous' ); // For return value

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   				if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}	

		function addDropDownOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".addslashes($val)."');");
	       			}
	     		}			
  		}			
	}

	# Get Process Code Records
	function getProcessCodeRecords($fishId, $rowId, $selPCId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();		
		$processcodeObj		= new ProcessCode($databaseConnect);		
		
		# Process Code Records
		$pcRecords = $processcodeObj->getProcessCodeRecs($fishId);
		$objResponse->addCreateOptions("selProcessCode_".$rowId, $pcRecords,$selPCId);		
		return $objResponse;			
	} 

	# Get Brand Recs
	/*
	function getBrandRecs($customerId, $selBrandId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$brandObj	 = new Brand($databaseConnect);
		# get Recs
		$brandRecs     = $brandObj->getBrandRecords($customerId);
		$objResponse->addCreateOptions("brand", $brandRecs, $selBrandId);

		return $objResponse;
	}*/

	function chkSortOrder($sortOrder, $groupListMainId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$fpStkReportGroupListObj = new FPStkReportGroupList($databaseConnect);

		$sortOrderExist = $fpStkReportGroupListObj->chkSortOrderExist(trim($sortOrder), $groupListMainId);

		if ($sortOrderExist) {
			$objResponse->assign("sortOrderMSg","innerHTML","Sort Order already in database.");
			$objResponse->assign("hideSortOrder","value",true);			
		} else {
			$objResponse->assign("sortOrderMSg","innerHTML","");
			$objResponse->assign("hideSortOrder","value",false);
		}

		return $objResponse;
	}

	# Check Group Name exist
	function chkGroupName($groupName, $groupListMainId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$fpStkReportGroupListObj = new FPStkReportGroupList($databaseConnect);

		$groupNameExist = $fpStkReportGroupListObj->chkGroupNameExist(trim($groupName), $groupListMainId);

		if ($groupNameExist) $objResponse->assign("groupNameExistMSg","innerHTML","Group name already in database.");
		else $objResponse->assign("groupNameExistMSg","innerHTML","");

		return $objResponse;
	}

	# get QEL Li
	function filterQEList($tableRowCount, $freezingStyleId, $freezingStageId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$fpStkReportGroupListObj = new FPStkReportGroupList($databaseConnect);
		//$objResponse->alert("$tableRowCount, $freezingStyleId, $freezingStageId");
		$qelRecs = array();
		if ($freezingStyleId && $freezingStageId) {
			$qelRecs = $fpStkReportGroupListObj->fetchAllQELRecords($freezingStyleId, $freezingStageId);
		}

		if (sizeof($qelRecs)>0) {
			for ($i=0; $i<=$tableRowCount; $i++) {
				$objResponse->addDropDownOptions("selQEL_".$i, $qelRecs, "hidSelQEL_".$i);
			}
		}
		return $objResponse;
	}

	function setQELId($rowId, $qelId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$fpStkReportGroupListObj = new FPStkReportGroupList($databaseConnect);

		$objResponse->assign("hidSelQEL_$rowId", "value", $qelId);
		return $objResponse;
	}



$xajax->register(XAJAX_FUNCTION, 'getProcessCodeRecords', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkSortOrder', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkGroupName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'filterQEList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'setQELId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

//$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>