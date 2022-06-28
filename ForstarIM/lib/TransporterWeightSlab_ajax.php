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
	}
	
	
	# Check Entry Exist 
	function trptrWtSlabExist($transporterId, $mode, $currentId)
	{
		$objResponse 			= new NxajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$transporterWeightSlabObj	= new TransporterWeightSlab($databaseConnect);

		# Check Rec Exist
		$chkRecExist 	= $transporterWeightSlabObj->checkEntryExist($transporterId, $currentId);
		if ($chkRecExist) {
			$objResponse->assign("divRecExistTxt", "innerHTML", "Please make sure the selected record is not existing.");
			$objResponse->script("disableTrptrWtSlabBtn($mode);");
		} else  {
			$objResponse->assign("divRecExistTxt", "innerHTML", "");
			$objResponse->script("enableTrptrWtSlabBtn($mode);");
		}
		return $objResponse;
	}	

	/*
	function chkTrptrWtSlabInUse($trptrWtSlabEntryId)
	{
		$objResponse 			= new NxajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$transporterWeightSlabObj	= new TransporterWeightSlab($databaseConnect);
		$wtSlabEntryInUse = $transporterWeightSlabObj->chkWtSlabExistInTrptrRate($trptrWtSlabEntryId);
		//$objResponse->script("chkWtSlabInUse($trptrWtSlabEntryId, 'X' ,$wtSlabEntryInUse)");
		return $objResponse;		
	}
	*/

$xajax->register(XAJAX_FUNCTION, 'trptrWtSlabExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION, 'chkTrptrWtSlabInUse', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));		

$xajax->ProcessRequest();
?>