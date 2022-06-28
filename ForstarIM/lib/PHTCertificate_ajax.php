<?php
//require_once("lib/databaseConnect.php");
//require_once("PHTCertificate_class.php");
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
	
	/*
		Check Unique number
	*/
	function checkPHTCertificateExist($reqNum, $existNum, $mode)
	{
		$objResponse = new NxajaxResponse();
		//$objResponse->alert($existNum);
	    	$databaseConnect = new DatabaseConnect();	
		$phtCertificateObj 	= new PHTCertificate($databaseConnect);
		$chkUnique = $phtCertificateObj->checkUnique($reqNum,$existNum);
		//$objResponse->alert($chkUnique);
		if (sizeof($chkUnique)>0) {
			$msg = "$reqNum is already in use. ";
			$objResponse->assign("requestNumExistTxt", "innerHTML", "$msg");			
			//$objResponse->script("disableStockIssuanceButtons($mode)");			
		} else {
			//$msg ="hai";
			$objResponse->assign("requestNumExistTxt", "innerHTML","");			
			//$objResponse->script("enableStockIssuanceButtons($mode)");			
		}
		return $objResponse;
	}
	
	function supplierName($supplierGroupId,$selSupplierGroupId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$phtCertificateObj 	= new PHTCertificate($databaseConnect);
		$supplierRecs 			= $phtCertificateObj->filterSupplierList($supplierGroupId);
		
		if (sizeof($supplierRecs)>0) addDropDownOptions("supplier", $supplierRecs, $selSupplierGroupId, $objResponse);
		
		
		
		return $objResponse;
	}
	
	function pondName($supplerId,$suplierGroupId,$selPondId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($suplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$phtCertificateObj 	= new PHTCertificate($databaseConnect);
		$pondRecs 			= $phtCertificateObj->filterPondList($supplerId,$suplierGroupId);
		
		if (sizeof($pondRecs)>0) addDropDownOptions("pondName", $pondRecs, $selPondId, $objResponse);
		
		return $objResponse;
	}
	
	function phtQty($pondId,$selPondId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($suplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$phtCertificateObj 	= new PHTCertificate($databaseConnect);
		$qtyRecs 			= $phtCertificateObj->filterPondQty($pondId);
		
		if (sizeof($qtyRecs)>0) addDropDownOptions("phtQuantity", $qtyRecs, $selPondId, $objResponse);
		
		return $objResponse;
	}
	$xajax->register(XAJAX_FUNCTION,'checkPHTCertificateExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'supplierName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'pondName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'phtQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	$xajax->ProcessRequest();
?>