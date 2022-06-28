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

		function addCustDropDownOpts($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
			$this->script("addOption('','".$sSelectId."','','--Select--');");
   			if (sizeof($options) >0) {
				foreach ($options as $ov) {
					$option = $ov[0];
					$val	= $ov[2];
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}				
	}

	
	function chkAgentNameExist($name, $agentId, $mode)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$agentMasterObj		= new AgentMaster($databaseConnect);
		$chkNameExist = $agentMasterObj->chkDuplicateEntry(trim($name), $agentId);		
		if ($chkNameExist) {
			$objResponse->assign("divNameExistMsg", "innerHTML", "Name is already in database.<br>Please choose another one.");
			$objResponse->script("disableCmdButton($mode);");
		} else  {
			$objResponse->assign("divNameExistMsg", "innerHTML", "");
			$objResponse->script("enableCmdButton($mode);");
		}		
		return $objResponse;
	}

	function filterAgentState($cityId, $selCityId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$agentMasterObj		= new AgentMaster($databaseConnect);
		$stateRecs 		= $agentMasterObj->filterStateList($cityId);
		
		//if (sizeof($stateRecs)>0) addDropDownOptions("state", $stateRecs, $selCityId, $objResponse);
		if (sizeof($stateRecs)>0) $objResponse->addCreateOptions("state", $stateRecs, $selCityId);
		
		return $objResponse;
	}

	# Get Customer Recs
	function getCustomers($agentId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$agentMasterObj		= new AgentMaster($databaseConnect);

		$fetchCustomerRecs 	= $agentMasterObj->fetchAllCustomerRecs();
		$selCustomerRecs 	= array();		
		if ($agentId) $selCustomerRecs = $agentMasterObj->getSelCustomerRecs($agentId);
		$customerRecords = ary_diff($fetchCustomerRecs, $selCustomerRecs);

		$objResponse->addCustDropDownOpts("selAllCustomer", $customerRecords, $selId);
		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'chkAgentNameExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'filterAgentState', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCustomers', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));


$xajax->ProcessRequest();
?>