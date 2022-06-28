<?php
//require_once("lib/databaseConnect.php");
//require_once("dailyfrozenpacking_class.php");
require_once("libjs/xajax_core/xajax.inc.php");
//require_once("lib/config.php");
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
		$dailyfrozenpackingObj	= new DailyFrozenPacking($databaseConnect);
		
		//$objResponse->alert($qeQualityId);
		$qelRecExist		= $dailyfrozenpackingObj->chkQELRecExist($selDate, $processor, $fishId, $pCodeId, $qeFrozenCodeId, $qeMCPackingId, $qeQualityId);
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
		$dailyfrozenpackingObj	= new DailyFrozenPacking($databaseConnect);	
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
		$dailyfrozenpackingObj	= new DailyFrozenPacking($databaseConnect);	
		$mcpackingObj		= new MCPacking($databaseConnect);
		
		# Get Num Packs
		$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
		
		$objResponse->assign("numMcPack_$rowId", "value", $numPacks);		
		$objResponse->script("callPkgChange($rowId);"); // After change the packing change calc
		//$objResponse->script("callProdnCalc();");


		return $objResponse;
	}

	function assignFrzPackChg($ffozid)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyfrozenpackingObj	= new DailyFrozenPacking($databaseConnect);	
		$frozenpackingObj		=	new FrozenPacking($databaseConnect);
		
		# Get Num Packs
		//$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
		  $ffilledWt=$frozenpackingObj->frznPkgFilledWt($ffozid);
		//$objResponse->assign("numMcPack_$rowId", "value", $numPacks);
		$objResponse->assign("hidffilledWt", "value", $ffilledWt);	
		$objResponse->script("callPkgChangeFr($ffozid);"); // After change the packing change calc
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
				$i++;
			}
		}

		$objResponse->script("SetPOGrades('$selRowId','$gradeArr');");

		return $objResponse;
	}


$xajax->registerFunction("chkQERecExist");


$xajax->register(XAJAX_FUNCTION, 'getMCNumPack', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignMCPack', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPOItems', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignFrzPackChg', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->ProcessRequest();
?>