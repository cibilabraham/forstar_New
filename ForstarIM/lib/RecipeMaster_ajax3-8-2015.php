<?php
//require_once("lib/databaseConnect.php");
//require_once('ProductMaster_class.php');
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

		function addOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$optionId	= $val[0];
					$optValue	= $val[1];	
					$this->script("addOption('".$cId."','".$sSelectId."','".$optionId."','".$optValue."');");
	       			}
	     		}
  		}
	}

	# get ing List
	function getIngredientRecords($selectId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$ingredientRateListObj   = new IngredientRateList($databaseConnect);
	        $ingredientRateMasterObj = new IngredientRateMaster($databaseConnect);
		$recipeMasterObj=new RecipeMaster($databaseConnect);
		$selRateList = $ingredientRateListObj->latestRateList();
		$data = $recipeMasterObj->fetchAllIngredientRecords($selRateList);
	        $objResponse->addOptions($selectId, $data, $cId);
		return $objResponse;
	}
	
	# get Ing rate and Declared Yield
	function getIngRate($ingId, $rowId, $selIngRateListId)
	{
		$sR = explode("_",$ingId);
		$ingId = $sR[1];
		$selIngType = $sR[0];	# ING- Ingredient / SFP - Semi Finished Product
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$ingredientRateListObj   = new IngredientRateList($databaseConnect);
	        $ingredientRateMasterObj = new IngredientRateMaster($databaseConnect);
		$recipeMasterObj=new RecipeMaster($databaseConnect);
		//$objResponse->alert($ingId);
		$selRateList = "";
		if ($selIngType=='ING') {	# Ingredient
			if ($selIngRateListId!="") $selRateList = $selIngRateListId;
			else $selRateList = $ingredientRateListObj->latestRateList();
			list($lastPrice,$declYield) = $recipeMasterObj->getIngredientRate($ingId, $selRateList);	
		} else if ($selIngType=='SFP') {  # Semi Finished Product
			list($lastPrice,$declYield) = $recipeMasterObj->getSemiFinishRate($ingId);	
		}

		$objResponse->assign("lastPrice_".$rowId, "value", $lastPrice);	
		$objResponse->assign("declYield_".$rowId, "value", $declYield);	
		$objResponse->assign("ingType_".$rowId, "value", $selIngType);
		$objResponse->assign("ratePerKg_".$rowId, "value", $lastPrice);		
			
		$objResponse->script("calcProductRatePerBatch();");		
            	return $objResponse;
	}

	function getProductGroupExist($productStateId, $selId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		//$recipeMasterObj=new RecipeMaster($databaseConnect);
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
		$recipeMasterObj=new RecipeMaster($databaseConnect);	
		if ($pCode) $pCodeExist = $recipeMasterObj->chkProductCodeExist(trim($pCode), $cId);
		if ($pCodeExist) {
			$objResponse->assign("pcodeExist","innerHTML","<br>The code you have entered is already exist.");
			$objResponse->assign("hidPCodeExist","value",1);
			
		} else {
			$objResponse->assign("pcodeExist","innerHTML","");
			$objResponse->assign("hidPCodeExist","value","");
		}
		return $objResponse;
	}

	
$xajax->register(XAJAX_FUNCTION, 'getIngredientRecords', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getIngRate', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProductGroupExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkPCodeExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>