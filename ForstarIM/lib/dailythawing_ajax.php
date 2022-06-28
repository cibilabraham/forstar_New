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
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}
	}


	# Get Brand Recs
	function getBrandRecs($customerId, $selBrandId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$brandObj	= new Brand($databaseConnect);

		# get Recs
		$brandRecs     = $brandObj->getBrandRecords($customerId);
		$objResponse->addCreateOptions("brand", $brandRecs, $selBrandId);

		return $objResponse;
	}

	# Get Num of MC Pack
	function getMCNumPack($mcPackingId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$mcpackingObj		= new MCPacking($databaseConnect);

		# Get Num Packs
		$mcpackingRec	= $mcpackingObj->find($mcPackingId);
		$numPacks	= $mcpackingRec[2];

		$objResponse->assign("hidNumPack", "value", $numPacks);		
		return $objResponse;
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

	

	function assignMCPack($mcPackingId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailyfrozenpackingObj	= new DailyFrozenPacking($databaseConnect);	
		$mcpackingObj		= new MCPacking($databaseConnect);
		
		# Get Num Packs
		$numPacks  = $mcpackingObj->numMCPacks($mcPackingId);
	
		$objResponse->assign("numMcPack_$rowId", "value", $numPacks);
		//$objResponse->alert("$mcPackingId, $rowId");		
		$objResponse->script("callProdnCalc();");

		return $objResponse;
	}

	


//$xajax->registerFunction("chkQERecExist");


//$xajax->register(XAJAX_FUNCTION, 'assignMCPack', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getMCNumPack', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>