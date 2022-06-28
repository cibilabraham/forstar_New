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
	
	/*
		Fetch All Product Group
		(Using in Prouct MRP Master, Product Master)
	*/
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

	function chkPCodeExist($pCode, $cId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$manageProductObj	= new ManageProduct($databaseConnect); 	
		if ($pCode) $pCodeExist = $manageProductObj->chkProductCodeExist($pCode, $cId);
		if ($pCodeExist) {
			$objResponse->assign("pcodeExist","innerHTML","<br>The code you have entered is already exist.");
			$objResponse->assign("hidPCodeExist","value",1);
			
		} else {
			$objResponse->assign("pcodeExist","innerHTML","");
			$objResponse->assign("hidPCodeExist","value","");
		}
		return $objResponse;
	}

	function chkIdentifiedNoExist($identifiedNo, $cId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$manageProductObj	= new ManageProduct($databaseConnect); 	
		if ($identifiedNo) $identifiedNoExist = $manageProductObj->chkProductIdentifiedExist($identifiedNo, $cId);
		if ($identifiedNoExist) {
			$objResponse->assign("pIdentifiedNoExist","innerHTML","<br>Identification No you have entered is already exist.");
			$objResponse->assign("hidPIdentifiedNoExist","value",1);
			
		} else {
			$objResponse->assign("pIdentifiedNoExist","innerHTML","");
			$objResponse->assign("hidPIdentifiedNoExist","value","");
		}
		return $objResponse;
	}

	function chkExciseCodeExist($exciseCode, $cId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$manageProductObj	= new ManageProduct($databaseConnect); 	
		if ($exciseCode) $ecExist = $manageProductObj->chkPExciseCodeExist($exciseCode, $cId);
		if ($ecExist) {
			$objResponse->assign("pExciseCodeExist","innerHTML","<br>Excise Code you have entered is already exist.");
			$objResponse->assign("hidPExciseCodeExist","value",1);
			
		} else {
			$objResponse->assign("pExciseCodeExist","innerHTML","");
			$objResponse->assign("hidPExciseCodeExist","value","");
		}
		return $objResponse;
	}


$xajax->registerFunction("getProductGroupExist");
$xajax->registerFunction("chkPCodeExist");
$xajax->registerFunction("chkIdentifiedNoExist");
$xajax->registerFunction("chkExciseCodeExist");

$xajax->ProcessRequest();
?>