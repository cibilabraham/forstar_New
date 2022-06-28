<?php
//require_once("lib/databaseConnect.php");
//require_once("purchaseorder_class.php");
//require_once("config.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
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
	
	function getProformaInvoiceNo()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$containerObj	= new Container($databaseConnect);
		$proformaInvoiceNo 	= $containerObj->getNextProformaInvoiceNo();
		$objResponse->assign("proformaInvoiceNo","value",$proformaInvoiceNo);
		return $objResponse;
	}

	function getSampleInvoiceNo()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$containerObj		=	new Container($databaseConnect);
		$sampleInvoiceNo	= $containerObj->getNextSampleInvoiceNo();
		$objResponse->assign("sampleInvoiceNo","value",$sampleInvoiceNo);
		return $objResponse;
	}


	# Proforma Number Exist
	function chkProformaNoExist($invoiceNum, $mode, $cSOId, $selDate)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();		 
		$containerObj 	= new Container($databaseConnect);
		$soYear			= date("Y", strtotime($selDate));
		
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $containerObj->chkValidProformaNum($selDate, $invoiceNum);			
			if ($validSONum) {
				$chkSONumExist = $containerObj->checkProformaNumExist($invoiceNum, $cSOId);
				//objResponse->alert($chkSONumExist);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSPOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt", "innerHTML", "");
					$objResponse->script("enableSPOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSPOButton($mode);");
			}
		}
		return $objResponse;
	}

	# Sample Number Exist
	function chkSampleNoExist($invoiceNum, $mode, $cSOId, $selDate)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$containerObj 		= new Container($databaseConnect);
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $containerObj->chkValidSampleNum($selDate, $invoiceNum);
			if ($validSONum) {
				$chkSONumExist = $containerObj->checkSampleNumExist($invoiceNum, $cSOId);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSPOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt", "innerHTML", "");
					$objResponse->script("enableSPOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSPOButton($mode);");
			}
		}
		return $objResponse;
	}

	# Chk valid Despatch Date
	function chkValidDespatchDate($selDate)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$containerObj = new Container($databaseConnect);
		$manageChallanObj = new ManageChallan($databaseConnect);
		$sDate		= mysqlDateFormat($selDate);
		$chkValidDate   = $manageChallanObj->chkValidDate('SPO',$sDate);
		if (!$chkValidDate) {
			$objResponse->alert("Please check the selected despatch date.");
			$objResponse->assign("validDespatchDate", "value", 1);
		} else {
			$objResponse->assign("validDespatchDate", "value", 0);
		}		
		return $objResponse;	
	}

	# Get Process Codes	
	function getProcessCodes($fishId, $rowId, $cPCId)
	{
		$objResponse 	 = new NxajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		$containerObj = new Container($databaseConnect);
		$processcodeObj = new ProcessCode($databaseConnect);

		$pcRecs		= $processcodeObj->getProcessCodeRecs($fishId);
		
		$objResponse->addDropDownOptions("selProcessCode_$rowId", $pcRecs, $cPCId);

		return $objResponse;
	}

	# Get Brand Recs
	function getBrandRecs($tableRowCount, $customerId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$containerObj = new Container($databaseConnect);
		# get Recs
		$brandRecs     = $containerObj->getBrandRecords($customerId);
		for ($i=0; $i<=$tableRowCount; $i++) {			
			$objResponse->addCreateOptions("selBrand_".$i, $brandRecs, "hidBrandId_".$i);
		}		
		return $objResponse;
	}

	# Assign Selected Brand value
	function assignBrand($brandId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$objResponse->assign("hidBrandId_$rowId","value",$brandId);
		return $objResponse;
	}

	# Get Grade Recs
	function getGradeRecs($processCodeId, $rowId, $selGradeId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$containerObj = new Container($databaseConnect);
		$gradeRecs		= $containerObj->getFrozenGradeRecs($processCodeId);
		//$objResponse->alert("size=".sizeof($gradeRecs));
		$objResponse->addDropDownOptions("selGrade_$rowId", $gradeRecs, $selGradeId);

		return $objResponse;
	}

	# Get frozen Code Filled wt
	function getFilledWt($frozenCodeId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$containerObj = new Container($databaseConnect);

		$filledWt = $containerObj->getFrznPkgFilledWt($frozenCodeId);
		if ($filledWt) $objResponse->assign("frznPkgFilledWt_$rowId","value",$filledWt);
		else $objResponse->assign("frznPkgFilledWt_$rowId","value",0);

		$objResponse->script("totRowVal($rowId);");
		return $objResponse;		
	}

	# Get Num of MC Packing
	function getNumMC($mcPackingId, $rowId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$containerObj = new Container($databaseConnect);

		$numPacks  = $containerObj->numOfPacks($mcPackingId);
		if ($numPacks) $objResponse->assign("numPacks_$rowId","value",$numPacks);
		else $objResponse->assign("numPacks_$rowId","value",0);
		$objResponse->script("totRowVal($rowId);");
		return $objResponse;		
	}

	function container()
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$containerObj = new Container($databaseConnect);
		$numPacks  = $containerObj->numOfPacks($mcPackingId);
		
		return $objResponse;
	}

$xajax->register(XAJAX_FUNCTION, 'container', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProformaInvoiceNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSampleInvoiceNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkProformaNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkSampleNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chkValidDespatchDate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCodes', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'assignBrand', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getGradeRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getFilledWt', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getNumMC', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));


$xajax->ProcessRequest();
?>