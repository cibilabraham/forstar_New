<?php
require_once("lib/databaseConnect.php");
require_once("FrozenStockAllocation_class.php");
require_once("libjs/xajax_core/xajax.inc.php");
require_once("lib/config.php");

$xajax = new xajax();	

	class NxajaxResponse extends xajaxResponse
	{
		/*
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}	
		*/
		
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   				if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}
	}


	# Check QE Rec Exist
	function chkQERecExist($selectDate, $processor, $fishId, $pCodeId, $qeFrozenCodeId, $qeMCPackingId, $qeQualityId, $rowId)
	{
		$selDate = mysqlDateFormat($selectDate);

		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frozenStockAllocationObj	= new FrozenStockAllocation($databaseConnect);
		
		//$objResponse->alert($qeQualityId);
		$qelRecExist		= $frozenStockAllocationObj->chkQELRecExist($selDate, $processor, $fishId, $pCodeId, $qeFrozenCodeId, $qeMCPackingId, $qeQualityId);
		# 1= Exist, 0 - Not exist
		if ($qelRecExist) {
			$objResponse->assign("recExist_$rowId", "value", 1);
			$objResponse->assign("qelPCErr_$rowId", "innerHTML", "<br>combination exist in db");
			
			//$objResponse->alert("Quick entry combination is already in database.");
		} else {
			$objResponse->assign("recExist_$rowId", "value", 0);
			$objResponse->assign("qelPCErr_$rowId", "innerHTML", "");
		}
		return $objResponse;
	}

	# Get Num of MC Pack
	function getMCNumPack($mcPackingId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frozenStockAllocationObj	= new FrozenStockAllocation($databaseConnect);	
		$mcpackingObj		= new MCPacking($databaseConnect);

		# Get Num Packs
		$mcpackingRec	= $mcpackingObj->find($mcPackingId);
		$numPacks	= $mcpackingRec[2];

		$objResponse->assign("hidNumPack", "value", $numPacks);		
		return $objResponse;
	}

	function assignMCPack($mcPackingId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frozenStockAllocationObj	= new FrozenStockAllocation($databaseConnect);	
		$mcpackingObj		= new MCPacking($databaseConnect);
		
		# Get Num Packs
		$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
	
		$objResponse->assign("numMcPack_$rowId", "value", $numPacks);
		$objResponse->script("callProdnCalc();");

		return $objResponse;
	}

	function assignMCPackChg($mcPackingId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyfrozenpackingObj=new DailyFrozenPackingNew($databaseConnect);
		$mcpackingObj		= new MCPacking($databaseConnect);
		
		# Get Num Packs
		$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
		
		$objResponse->assign("numMcPack_$rowId", "value", $numPacks);		
		$objResponse->script("callPkgChange($rowId);"); // After change the packing change calc
		//$objResponse->script("callProdnCalc();");


		return $objResponse;
	}

	function assignMCPackChgrprg($mcPackingId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyfrozenpackingObj=new DailyFrozenPackingNew($databaseConnect);
		$mcpackingObj		= new MCPacking($databaseConnect);
		
		# Get Num Packs
		$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
		
		$objResponse->assign("numMcPack_$rowId", "value", $numPacks);		
		$objResponse->script("callPkgChangerprg($rowId);"); // After change the packing change calc
		//$objResponse->script("callProdnCalc();");


		return $objResponse;
	}

	function assignMCPackChgrep($mcPackingId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyfrozenpackingObj=new DailyFrozenPackingNew($databaseConnect);
		$mcpackingObj		= new MCPacking($databaseConnect);
		
		# Get Num Packs
		$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
		
		$objResponse->assign("numMcPack_$rowId", "value", $numPacks);		
		$objResponse->script("callPkgChangerep($rowId);"); // After change the packing change calc
		//$objResponse->script("callProdnCalc();");


		return $objResponse;
	}



	function assignFrzPackChg($ffozid,$rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyfrozenpackingObj=new DailyFrozenPackingNew($databaseConnect);
		$frozenpackingObj		=	new FrozenPacking($databaseConnect);
		
		# Get Num Packs
		//$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
		  $ffilledWt=$frozenpackingObj->frznPkgFilledWt($ffozid);
		//$objResponse->assign("numMcPack_$rowId", "value", $numPacks);
		$objResponse->assign("hidffilledWt", "value", $ffilledWt);
		$objResponse->assign("hidcomb", "value", $ffozid);
		$objResponse->script("callPkgChangeFr($rowId);"); // After change the packing change calc
		//$objResponse->script("callProdnCalc();");


		return $objResponse;
	}


function assignFrzPackChgrep($ffozid,$rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyfrozenpackingObj=new DailyFrozenPackingNew($databaseConnect);
		$frozenpackingObj		=	new FrozenPacking($databaseConnect);
		
		# Get Num Packs
		//$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
		  $ffilledWt=$frozenpackingObj->frznPkgFilledWt($ffozid);
		//$objResponse->assign("numMcPack_$rowId", "value", $numPacks);
		$objResponse->assign("hidffilledWt", "value", $ffilledWt);
		$objResponse->assign("hidcomb", "value", $ffozid);
		$objResponse->script("callPkgChangeFrrep($rowId);"); // After change the packing change calc
		//$objResponse->script("callProdnCalc();");


		return $objResponse;
	}

	# Get Brand Recs
	function getBrandRecs($customerId, $selBrandId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$frznPkngQuickEntryListObj 	= new FrozenPackingQuickEntryList($databaseConnect);
		$brandObj			= new Brand($databaseConnect);

		# get Recs
		$brandRecs     = $brandObj->getBrandRecords($customerId);
		$objResponse->addCreateOptions("brand", $brandRecs, $selBrandId);

		return $objResponse;
	}

	function getPOItems($poId, $selRowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		//$objResponse->alert($poId);
		$poItemRecs = $purchaseorderObj->getProductsInPO($poId);
		
		$gradeArr = "";
		if (sizeof($poItemRecs)>0) {
			$i = 0;
			foreach ($poItemRecs as $poi) {
				$numMC		= $poi[9];
				$gradeId = $poi[18];
				//$objResponse->script("alert($numMC);");
				if ($i>0) $gradeArr .= ",";
				$gradeArr .= "$gradeId:$numMC";
				//$objResponse->alert($gradeArr);
				$i++;
			}
		}

		$objResponse->script("SetPOGrades('$selRowId','$gradeArr');");

		return $objResponse;
	}
	function getPOAvailableItems($gradeId,$ProdnId,$processId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseorderObj = new PurchaseOrder($databaseConnect);
		//$objResponse->alert($poId);
		//$objResponse->alert($gradeId);
		$gradeIds=explode(',',$gradeId);
		$gradeIdCnt=sizeof($gradeIds);
		//$objResponse->alert($gradeIdCnt);
		$j=1;
		for($i=0; $i<$gradeIdCnt; $i++)
		{
			$grade=$gradeIds[$i];
			$poItemRecs = $purchaseorderObj->availableQntyForGrade($grade,$ProdnId,$processId);
			$poAval=$poItemRecs[0];
			
			//$objResponse->alert($j);
			//$objResponse->alert($grade);
		//$objResponse->alert($grade.$i);
		$objResponse->assign("purchaseQnty_$j", "innerHTML", $poAval);
		$j++;
		}
		
		
		/*$poItemRecs = $purchaseorderObj->getProductsInPO($poId);
		
		$gradeArr = "";
		if (sizeof($poItemRecs)>0) {
			$i = 0;
			foreach ($poItemRecs as $poi) {
				$numMC		= $poi[9];
				$gradeId = $poi[18];
				//$objResponse->script("alert($numMC);");
				if ($i>0) $gradeArr .= ",";
				$gradeArr .= "$gradeId:$numMC";
				//$objResponse->alert($gradeArr);
				$i++;
			}
		}*/

		//$objResponse->script("SetPOGrades('$selRowId','$gradeArr');");

		return $objResponse;
	}

	function getNewLot($company,$unit)
	{
		$selDate=date("Y-m-d");
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		$newLotRecs = $objManageRMLOTID-> chkValidGatePassId($selDate,$company,$unit);
		if (sizeof($newLotRecs)>0)
		{
			$alphaCode=$objManageRMLOTID->getAlphaCode($selDate,$company,$unit);
			$alphaCodePrefix= $alphaCode[0];
			$checkExist=$objManageRMLOTID->getAvailableLotIdNos($company,$unit);
			if ($checkExist>0)
			{
				$nextGatePassId=$checkExist[0];
				$validendno=$objManageRMLOTID->getValidendnoGatePassId($selDate,$company,$unit);	
				if ($nextGatePassId>$validendno)
				{
				$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				}
				else{
				$numbergen=$newLotRecs[0][0];
				$disGateNo="$alphaCodePrefix$nextGatePassId";
				//$objResponse->alert("hiii".$disGateNo);
				$tempStore=$objManageRMLOTID->addLotIdTemporary($nextGatePassId,$checkGateNumberSettingsExist[0][0]);
				$objResponse->assign("rmLotIdNew","value",$disGateNo);
				$objResponse->assign("number_gen","value",$numbergen);
				$objResponse->assign("rmLotIdChar","value",$alphaCodePrefix);
				$objResponse->assign("rmLotIdNum","value",$nextGatePassId);
				}
			}
			else
			{
			$numbergen=$newLotRecs[0][0];
			$validPassNo=$objManageRMLOTID->getValidGatePassId($selDate,$company,$unit);	
			$lotVal="$alphaCodePrefix$validPassNo";
			//$objResponse->alert("huuuuii".$lotVal);
			$checkPassId=$objManageRMLOTID->chkValidGatePassId($selDate,$company,$unit);
			$tempStore=$objManageRMLOTID->addLotIdTemporary($validPassNo,$checkGateNumberSettingsExist[0][0]);
			$objResponse->assign("rmLotIdNew","value",$lotVal);
			$objResponse->assign("number_gen","value",$numbergen);
			$objResponse->assign("rmLotIdChar","value",$alphaCodePrefix);
			$objResponse->assign("rmLotIdNum","value",$validPassNo);
			}
		}
		else
		{
			//$objResponse->alert("hi");
			$GatePassMsg="Please set the gate pass in Settings";
			$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
		return $objResponse;
	}

$xajax->registerFunction("chkQERecExist");

$xajax->register(XAJAX_FUNCTION, 'getNewLot', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPOAvailableItems', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getMCNumPack', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignMCPack', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignMCPackChg', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
//$xajax->register(XAJAX_FUNCTION, 'getPOItems', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPOItems', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION, 'assignFrzPackChg', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignFrzPackChg', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION, 'assignFrzPackChgrep', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignFrzPackChgrep', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignMCPackChgrprg', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignMCPackChgrep',array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->ProcessRequest();
?>