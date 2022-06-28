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

		# multi-dimensional array
		function addDropDownOpts($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
			$this->script("addOption('','".$sSelectId."','','--Select--');");
   			if (sizeof($options) >0) {
				foreach ($options as $ov) {
					$option = $ov[0];
					$val	= $ov[1];
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}				
	}

	
	function chkCustomerNameExist($name, $customerId, $mode)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$customerObj		= new Customer($databaseConnect);
		$chkNameExist = $customerObj->chkDuplicateEntry(trim($name), $customerId);	
		//$objResponse->alert("$name, $customerId, $mode");	
		if ($chkNameExist) {
			$objResponse->assign("divNameExistMsg", "innerHTML", "Name is already in database.<br>Please choose another one.");
			$objResponse->script("disableCmdButton($mode);");
		} else  {
			$objResponse->assign("divNameExistMsg", "innerHTML", "");
			$objResponse->script("enableCmdButton($mode);");
		}		
		return $objResponse;
	}

	function filterCustomerState($cityId, $selCityId)
	{
		$objResponse 		= &new xajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$customerObj		= new Customer($databaseConnect);
		$stateRecs 		= $customerObj->filterStateList($cityId);
		
		if (sizeof($stateRecs)>0) addDropDownOptions("state", $stateRecs, $selCityId, $objResponse);		
		return $objResponse;
	}

	# Get Agent Recs
	function getAgents($customerId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$customerObj		= new Customer($databaseConnect);

		$fetchAgentRecs  = $customerObj->fetchAllAgentRecs();
		$selAgentRecs 	= array();
		if ($customerId) $selAgentRecs = $customerObj->getAgentList($customerId);
		$agentRecords = ary_diff($fetchAgentRecs, $selAgentRecs);

		$objResponse->addDropDownOpts("selAllAgent", $agentRecords, $selAgentId);
		return $objResponse;
	}

	# Get Shipping Line Recs
	function getShippingLine($customerId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$customerObj		= new Customer($databaseConnect);

		$fetchShippingCompanyRecs = $customerObj->getShippingComapnyRecords();
		$selShippingRecs 	= array();		
		if ($customerId) $selShippingRecs = $customerObj->getSelShippingRecs($customerId);
		$shippingCompanyRecords = ary_diff($fetchShippingCompanyRecs, $selShippingRecs);

		$objResponse->addDropDownOpts("selAllShipping", $shippingCompanyRecords, $selId);
		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'chkCustomerNameExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
//$xajax->register(XAJAX_FUNCTION, 'filterCustomerState', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getAgents', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getShippingLine', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>