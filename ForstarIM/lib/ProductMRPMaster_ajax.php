<?php
require_once("lib/databaseConnect.php");
require_once("ManageProduct_class.php");
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
	
	/*Fetch All Product Group(Using in Prouct MRP Master & Manage Product)*/
	function getProductGroupExist($productStateId, $selId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$manageProductObj	= new ManageProduct($databaseConnect); 
		if ($productStateId) {
			# Checking Prouct Group Exist
			$productGroupExist = $manageProductObj->checkProductGroupExist($productStateId);
			# Product Group Records
			$productGroupRecords = $manageProductObj->filterProductGroupList($productGroupExist);
			
			$objResponse->addCreateOptions("selProductGroup", $productGroupRecords, $selId);		
		}
		return $objResponse;			
	}

	function chkProductMRPExist($selProduct, $productMRPRateList, $selProductMRPId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$productMRPMasterObj	= new ProductMRPMaster($databaseConnect); 

		//$objResponse->alert("$selProduct, $productMRPRateList, $selProductMRPId");	
		if ($selProduct) $recExist = $productMRPMasterObj->chkRecExist($selProduct, $productMRPRateList, $selProductMRPId);

		if ($recExist) {
			$objResponse->assign("productExistMsg","innerHTML","<br>The selected Product MRP is already exist.");
			$objResponse->assign("productExist","value",1);
			
		} else {
			$objResponse->assign("productExistMsg","innerHTML","");
			$objResponse->assign("productExist","value","");
		}
		return $objResponse;
	}
	
	# Get Distributor List
	function getDistributorList($stateId, $rowId, $cDistId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$productMRPMasterObj	= new ProductMRPMaster($databaseConnect); 

		$result	= $productMRPMasterObj->getDistributorRecs($stateId);
		
		$distributorRecords = array('0'=>'--Select All--');
		if (sizeof($result)>0) {
			while (list(,$v) = each($result)) {
				$distributorRecords[$v[0]] = $v[2];
			}
		}
		$objResponse->addCreateOptions("selDistributor_".$rowId, $distributorRecords, $cDistId);

		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'getProductGroupExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkProductMRPExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getDistributorList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>