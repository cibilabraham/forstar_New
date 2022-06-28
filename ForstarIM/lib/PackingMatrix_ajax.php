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
	
	function getInnerContainer($stockId)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		//$objResponse->alert($stockId);
		if($stockId!="")
		{
			$rate = $packingMatrixObj->findInnerContainerRate($stockId);
			$objResponse->assign("innerContainerRate","value","$rate[0]");
			$objResponse->script("displayInnerPackingCost();");
		}
		else
		{
			$objResponse->assign("innerContainerRate","value","");
			$objResponse->script("displayInnerPackingCost();");
		}
		
		return $objResponse;
	}
	
	function getInnerPacking($packId)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($packId!="")
		{
		   
			$packingRate = $packingMatrixObj->findInnerContainerRate($packId);
			$objResponse->assign("innerPackingRate","value","$packingRate[0]");
			$objResponse->script("displayInnerPackingCost();");
		}
		else
		{
			$objResponse->assign("innerPackingRate","value","");
			$objResponse->script("displayInnerPackingCost();");
		}
		
		return $objResponse;
		
	}
	
	function getInnerSample($sampleId)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($sampleId!="")
		{
		    $sampleRate = $packingMatrixObj->findInnerContainerRate($sampleId);
			$objResponse->assign("innerSampleRate","value","$sampleRate[0]");
			$objResponse->script("displayInnerPackingCost();");
		}
		else
		{
			$objResponse->assign("innerSampleRate","value","");
			$objResponse->script("displayInnerPackingCost();");
		}
		
		return $objResponse;
	}
	
	function getInnerLabeling($labelId)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($labelId!="")
		{
		    $labelingRate = $packingMatrixObj->findInnerContainerRate($labelId);
			$objResponse->assign("innerLabelingRate","value","$labelingRate[0]");
			$objResponse->script("displayInnerPackingCost();");
		}
		else
		{
			$objResponse->assign("innerLabelingRate","value","");
			$objResponse->script("displayInnerPackingCost();");
		}
		
		return $objResponse;
	}
	
	function getInnerLeaflet($leafletId)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($leafletId!="")
		{
		    $leafletRate = $packingMatrixObj->findInnerContainerRate($leafletId);
			$objResponse->assign("innerLeafletRate","value","$leafletRate[0]");
			$objResponse->script("displayInnerPackingCost();");
		}
		else
		{
			$objResponse->assign("innerLeafletRate","value","");
			$objResponse->script("displayInnerPackingCost();");
		}
		
		return $objResponse;
	}
	
	function getInnerSealing($sealingVal)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($sealingVal!="")
		{
			$packingCost = $packingMatrixObj->getPackingCost();
			$packingCostArr = array();
			$packingCostArr[]="0";
			foreach ($packingCost as $packing)
			{
				//Putting value of packing cost into an array
				$packingCostArr[] = $packing;
			}
			$packCost = number_format((float)$packingCostArr[$sealingVal], 3, '.', '');
			//$objResponse->alert($packCost);
			$objResponse->assign("innerSealingRate","value","$packCost");
			$objResponse->script("displayInnerPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		else
		{
			$packCost = number_format((float)0, 3, '.', '');
			$objResponse->assign("innerSealingRate","value","$packCost");
			$objResponse->script("displayInnerPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		
		return $objResponse;
	}
	
	function getLabourCost($labourRate)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($labourRate!="")
		{
			$labourCost = $packingMatrixObj->getPackingCost();
			$labourCostArr = array();
			$labourCostArr[] = "0";
			foreach ($labourCost as $labour)
			{
				$labourCostArr[] = $labour;
			}
			$cost = number_format((float)$labourCostArr[$labourRate], 3, '.', '');
			$objResponse->assign("labourCost","value","$cost");
			$objResponse->script("displayInnerPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		else 
		{
			$objResponse->assign("labourCost","value","");
			$objResponse->script("displayInnerPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		
		return $objResponse;
	}
	
	function getDispenserPkg($dispenserId,$shrink)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($dispenserId!="" && $shrink>0)
		{
			$dispenserPkg = $packingMatrixObj->findInnerContainerRate($dispenserId);
			$dispenserValue = number_format((float)$dispenserPkg[0]/$shrink, 3, '.', '');
			$objResponse->assign("dispenserPkg","value","$dispenserValue");
			$objResponse->script("displayOuterPackingCost();");
			
		}
		else
		{
			$dispenserValue = number_format((float)0, 3, '.', '');
			$objResponse->assign("dispenserPkg","value","$dispenserValue");
			$objResponse->script("displayOuterPackingCost();");
		}
		
		return $objResponse;
	}
	
	function getDispenserSeal($shrinkGrp)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		//$costPerDispenser = 0.75;
		
		if($shrinkGrp > 0)
		{
			$costPerDispenser = $packingMatrixObj->getCostPerDispenser();
			//$objResponse->alert($costPerDispenser[0]);
			$dispenserSeal = number_format((float)$costPerDispenser[0]/$shrinkGrp, 3, '.', '');
			$objResponse->assign("dispenserSealing","value","$dispenserSeal");
			$objResponse->script("displayDispenserPkg('".$shrinkGrp."')");
			$objResponse->script("displayOuterPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		else
		{
			$dispenserSeal = number_format((float)0, 3, '.', '');
			$objResponse->assign("dispenserSealing","value","$dispenserSeal");
			$objResponse->script("displayDispenserPkg('".$shrinkGrp."')");
			$objResponse->script("displayOuterPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		
		return $objResponse;
	}
	
	function getMasterPacking($masterId,$numPacks)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($masterId!="" && $numPacks>0)
		{
		    //$objResponse->alert($masterId);
			//$objResponse->alert($numPacks);
			$masterPack = $packingMatrixObj->findInnerContainerRate($masterId);
			$masterPackValue = number_format((float)$masterPack[0]/$numPacks, 3, '.', '');
			$objResponse->assign("masterPackingRate","value","$masterPackValue");
			$objResponse->script("displayOuterPackingCost();");
		}
		else 
		{
			$masterPackValue = number_format((float)0, 3, '.', '');
			$objResponse->assign("masterPackingRate","value","$masterPackValue");
			$objResponse->script("displayOuterPackingCost();");
		}
		
		return $objResponse;
	}

	function getMasterSealing($noOfPacks)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($noOfPacks > 0)
		{
			$tapeCost = $packingMatrixObj->getTapeCost();
			$masterSeal = number_format((float)$tapeCost[0]/$noOfPacks, 3, '.', '');
			$objResponse->assign("masterSealingRate","value","$masterSeal");
			$objResponse->script("displayMasterPacking('".$noOfPacks."');");
			$objResponse->script("displayOuterPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		else
		{
			$masterSeal = number_format((float)0, 3, '.', '');
			$objResponse->assign("masterSealingRate","value","$masterSeal");
			$objResponse->script("displayMasterPacking('".$noOfPacks."');");
			$objResponse->script("displayOuterPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		
		return $objResponse;
	}
	
	function getMasterLoading($grossMC,$numPacks)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$packingMatrixObj		= new PackingMatrix($databaseConnect);
		
		if($grossMC!="" && $numPacks!="")
		{
			$loadingCharge = $packingMatrixObj->getLoadingCharge();
			$masterLoading = (($grossMC/20)*$loadingCharge[0])/$numPacks;
			$masterLoadingCharge = number_format((float)$masterLoading, 3, '.', '');
			$objResponse->assign("masterLoading","value","$masterLoadingCharge");
			$objResponse->script("displayOuterPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		else
		{
			$masterLoadingCharge = number_format((float)0, 3, '.', '');
			$objResponse->assign("masterLoading","value","$masterLoadingCharge");
			$objResponse->script("displayOuterPackingCost();");
			$objResponse->script("labourCostOnly();");
		}
		
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION, 'getInnerContainer', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getInnerPacking', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getInnerSample', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getInnerLabeling', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getInnerLeaflet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getInnerSealing', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getLabourCost', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDispenserPkg', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDispenserSeal', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getMasterPacking', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getMasterSealing', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getMasterLoading', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'calculateInnerPackingCost', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	$xajax->ProcessRequest();
	

?>