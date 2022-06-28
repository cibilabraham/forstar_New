<?php 
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure('statusMessages', true);
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
			
		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	}
	
	function getMaterialType($ingredientId,$mode)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$ingredientRateMasterObj	= new IngredientRateMaster($databaseConnect);
		
		//$objResponse->alert($mode);
		if($ingredientId!="")
		{
			$materialValue = $ingredientRateMasterObj->displayMaterialType($ingredientId);
			$rateRange = $ingredientRateMasterObj->displayHighLowRate($ingredientId);
			
			if($rateRange!="")
			{
				foreach($rateRange as $rate)
				{
					$highestPrice = $rate[0];
					$lowestPrice = $rate[1];
				}
				
				$objResponse->assign("ingHighPrice","value","$highestPrice");
				$objResponse->assign("ingLowPrice","value","$lowestPrice");
				
			}
			//$objResponse->assign("hiddenMaterialType","value","$materialType");
			$objResponse->script("assignMaterialType(".$materialValue.");");
			$objResponse->script("displayRawColumn(".$mode.");");
		}
		else
		{
			//$objResponse->assign("hiddenMaterialType","value","");
			$objResponse->assign("ingHighPrice","value","");
			$objResponse->assign("ingLowPrice","value","");
		}
		
		return $objResponse;
	}
	
	function getRateYieldValue($rawIngredientId)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$ingredientRateMasterObj	= new IngredientRateMaster($databaseConnect);
		$rate = "";
		$yield = "";
		$currentDate = date("Y-m-d");
		
		if($rawIngredientId!="")
		{
			$result = $ingredientRateMasterObj->displayRateYield($rawIngredientId);
			$rate = $result[0];
			$yield = $result[1];
			
			$objResponse->assign("ingRatePerKg","value","$rate");
			$objResponse->assign("ingYield","value","$yield");
			$objResponse->script("calcIngFinalRate();");
		}
		else 
		{
			$objResponse->assign("ingRatePerKg","value","");
			$objResponse->assign("ingYield","value","");
		}
		
		return $objResponse;
	}
	
	$xajax->register(XAJAX_FUNCTION, 'getMaterialType', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getRateYieldValue', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	$xajax->ProcessRequest();