<?php
require_once("lib/databaseConnect.php");
require_once("SalesOrder_class.php");
require_once("OrderDispatched_class.php");
require_once("config.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();
//$xajax->configure('debug',true);
//$xajax->configure('defaultMode', 'synchronous'); // For return value	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}

		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}		
	}
	
	# Stock Unit Rate :: &$response
	function getStockUnitRate($distributorId, $productId, $rowId, $stateId, $selDate, $cityId, $response, $billingType)
	{			
		if ($response=="") $objResponse = new NxajaxResponse();
		else $objResponse = &$response;		

		$databaseConnect = new DatabaseConnect();		
		$manageRateListObj	= new ManageRateList($databaseConnect);
		$salesOrderObj		= new SalesOrder($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$taxMasterObj		= new TaxMaster($databaseConnect);
		$sDate			= mysqlDateFormat($selDate);		
		$exportEnabled = ($billingType=="E")?"Y":"N";

		$taxRate = 0;
		# Product Price Rate List 
		$productPriceRateListId = $manageRateListObj->getRateList("PMRP", $sDate);

		# Product MRP
		$mrp = $salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
		
		# Dist Mgn Rate List Id
		$distMarginRateListId	= $distMarginRateListObj->getRateList($distributorId, $sDate);

		list($distAvgMargin, $distMgnStateEntryId, $distBasicMargin) = $salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $cityId, $exportEnabled);
		/* Changed on 15 MAR 12
		# After Excise Duty integration Average margin = Basic  Margin
		//$distAvgMargin = $distBasicMargin;
		*/

		# Find Tax Percent
		$taxPercent	= $salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $sDate);	
		
		
		# Tax Rate
		$taxRate = ($taxPercent)/100;
		# Get the Tax Type (ie. VAT/CST)
		list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $salesOrderObj->getDistTaxType($distributorId, $stateId, $exportEnabled);
		if ($billingForm=='ZP') $taxPercent = $taxRate = 0;

		$actualCostToDist = "";
		$avgMgnCost = "";
		
		if ($taxType=='CST' OR $taxType=='GST' OR $taxType=='IGST') {
			# CST PERCENT From TAX MASTER
			//$cstPercent = $taxMasterObj->getBaseCst($sDate);
			$cstPercent = $taxMasterObj->getBaseCst($sDate,$taxType);

			$cstRate = ($cstPercent/100);
			
			//$calcBasicMgn    = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;Before New strut	
			$calcBasicMgn    = (1- ((100-$distAvgMargin)/100))*100;
			$distAvgMargin   = number_format($calcBasicMgn,4,'.','');
			$avgMgnCost	 = number_format(($mrp*(1-($distAvgMargin/100))),4,'.','');		
			$actualCostToDist = $avgMgnCost;
		} else {
			$avgMgnCost 	= $mrp*(1-($distAvgMargin/100));
			//$calcCostToDist = $avgMgnCost/(1+$taxRate); Before New strut
			$calcCostToDist = $avgMgnCost;
			$actualCostToDist = number_format($calcCostToDist,4,'.','');	
		}

		if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
		else $costToDist = 0;

		if ($productId) {
			# Find the Product Net Wt from m_product_manage
			$productNetWt = $salesOrderObj->getProductNetWt($productId);
			$mcpackingRecords	= $salesOrderObj->getMCPkgRecs($productNetWt);	
			$objResponse->addCreateOptions("selMcPkg_".$rowId, $mcpackingRecords, "hidMCPkg_".$rowId);
			if (sizeof($mcpackingRecords)<=1) {
				$objResponse->alert("MC Pkg Wt not set for the selected product");
			}
		}
			
		# MC PAck Combination
		list($pCategoryId, $pStateId, $pGroupId) = $salesOrderObj->findProductRec($productId);	
		$productCategoryComb = "$pStateId,$pGroupId";
				
		$objResponse->assign("pCategoryComb_".$rowId, "value", $productCategoryComb);
		$objResponse->assign("distMgnStateEntryId_".$rowId, "value", $distMgnStateEntryId);		
		$objResponse->assign("basicRate_".$rowId, "value", $costToDist);
		if ($productId) $objResponse->assign("hidSelStock_".$rowId, "value", $productId); // Assign hid stock value
		$objResponse->assign("taxPercent_".$rowId, "value", $taxPercent);
		$objResponse->assign("mrp_".$rowId, "value", $mrp);

		// Excise Duty
		$exciseDutyActive = $salesOrderObj->chkExciseDutyActive($sDate);
		$edEntryId=$exDutyRateListId=$exciseDuty = $chapterSubheading=""; 
		if ($exciseDutyActive) {
			$exemptionChaptSubhead = $salesOrderObj->getExCodeByProductId($productId);
			list($edEntryId, $exDutyRateListId, $exciseDuty, $orgChapterSubheading, $goodsType) = $salesOrderObj->getExciseDuty($sDate, $pCategoryId, $pStateId, $pGroupId);
			$chapterSubheading = ($exemptionChaptSubhead!="")?$exemptionChaptSubhead:$orgChapterSubheading;
			if ($exBillingForm!="FCT1") {
				$objResponse->assign("exciseDuty_".$rowId, "value", $exciseDuty);
				$objResponse->assign("excDutyEntryId_".$rowId, "value", $edEntryId);
			} else $objResponse->assign("exciseDuty_".$rowId, "value", 0);

			$objResponse->assign("chaptSubhead_".$rowId, "innerHTML", $chapterSubheading);
		}		
		$objResponse->assign("hidExDutyActive", "value", $exciseDutyActive);

		if ($response=="") $objResponse->script("multiplySalesOrderItem();");		
       	return $objResponse;
	}

	# Get Dist State List
	function getDistStateList($distributorId, $cId, $billingType)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$manageRateListObj	= new ManageRateList($databaseConnect);
		$salesOrderObj		= new SalesOrder($databaseConnect);
		if ($distributorId) {

			$exportEnabled = ($billingType=="E")?"Y":"N";
			
			# Get State Recs				
			$stateRecords	= $salesOrderObj->filterStateList($distributorId, $exportEnabled);
			
	
			# Get Dist Master Rec
			list($creditLimit, $creditPeriod, $outStandAmt) = $salesOrderObj->getDistMasterRec($distributorId);
			$objResponse->assign("creditLimit", "value", $creditLimit);
			$objResponse->assign("creditPeriod", "value", $creditPeriod);
			$objResponse->assign("outStandAmt", "value", $outStandAmt);
			
			$creditPeriodOutStandAmt = $salesOrderObj->getCreditPeriodOutStandAmount($distributorId, $creditPeriod);
			$objResponse->assign("cPeriodOutStandAmt", "value", number_format($creditPeriodOutStandAmt,2,'.',''));			
			$objResponse->addDropDownOptions("selState",$stateRecords, $cId);		
			$objResponse->script("multiplySalesOrderItem();");
		}
		return $objResponse;		
	}

	# SO Number Exist
	function chkSONumberExist($soId, $mode, $cSOId, $selDate,  $invoiceType)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$salesOrderObj 		= new SalesOrder($databaseConnect);

		# Check valid SO Num				
		if ($soId) {
		$validSONum = $salesOrderObj->chkValidSONum($selDate, $soId, $invoiceType);
			if ($validSONum) {
				$chkSONumExist = $salesOrderObj->checkSONumberExist($soId, $cSOId, $invoiceType, $selDate);
				if ($chkSONumExist && $soId!="") {
					$objResponse->assign("divSOIdExistTxt", "innerHTML", "$soId is already in use.<br>Please choose another one");					
					$objResponse->script("disableOrderDispatchBtn($mode);");
					$objResponse->assign("validInvoiceNo","value","N");
				} else  {
					# Check for cancelled challan number
					$cancelledInvoice = $salesOrderObj->checkCancelledInvoice($soId, $selDate, $invoiceType);
					if ($cancelledInvoice) {
						$objResponse->assign("divSOIdExistTxt", "innerHTML", "$soId is already cancelled.");			
						$objResponse->script("disableOrderDispatchBtn($mode);");
						$objResponse->assign("validInvoiceNo","value","N");
					} else {
						$objResponse->assign("divSOIdExistTxt", "innerHTML", "");	
						$objResponse->script("enableOrderDispatchBtn($mode);");
						$objResponse->assign("validInvoiceNo","value","Y");
					}
				}
			} else {
				$objResponse->assign("divSOIdExistTxt", "innerHTML", "$soId is not valid.<br>Please check the challan Settings.");
				$objResponse->script("disableOrderDispatchBtn($mode);");
				$objResponse->assign("validInvoiceNo","value","N");
			}
		}
		return $objResponse;
	}


	# Get MC Package Details and Loose Pack details
	function getPackageDetails($selMCPkgRec, $numPkts, $rowId, $selProductId, $freePkts)
	{
		
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		$mcpackingObj	= new MCPacking($databaseConnect);	
		$manageProductObj = new ManageProduct($databaseConnect); 
		$mcPkgWtMasterObj = new MCPkgWtMaster($databaseConnect);
		
		// Split MC Pkg recs
		$selPkgArr = explode("_",$selMCPkgRec);
		$mcPackingId			= $selPkgArr[0];
		$mcPkgWtId				= $selPkgArr[1];		


		# Find MC Packs Details	---------------------------
		$mcpackingRec	= $mcpackingObj->find($mcPackingId);
		$numPacks	= $mcpackingRec[2]; 
		$freePkts 	= ($freePkts!="")?$freePkts:0;
		$numPkts	+= $freePkts;
		$mcPacks 	= floor($numPkts/$numPacks);		
		$loosePacks 	= $numPkts%$numPacks;
		# -------------------------------------
		$productRec	= $manageProductObj->find($selProductId);
		$productNetWt	= $productRec[6];
		# Find Product Gross Wt		
		$productGrossWt	= ($numPkts*$productNetWt)/1000;

		# Find Mc Wt
		$mcPackageWt 	= $mcPkgWtMasterObj->getPackageWt($mcPackingId, $productNetWt, $mcPkgWtId);
		
		
		# MC Pkg Wt
		//$productMCPkgWt = ($mcPacks *$mcPackageWt)/1000;
		$productMCPkgWt = ($mcPacks*$mcPackageWt);

		list($pCategoryId, $pStateId, $pGroupId) = $salesOrderObj->findProductRec($selProductId);	
		$productCategoryComb = "$pStateId,$pGroupId";
		$mcCombination	     = "$pStateId,$pGroupId,$numPacks,$mcPackageWt";

		$leftPkgRule	     = "$pStateId,$pGroupId,$productNetWt";	
		list ($pLeftComb,$pRightComb) = $salesOrderObj->getPkgGroup($leftPkgRule);
		$pkgGroupComb = "";
		if ($pLeftComb!="" && $pRightComb!="") $pkgGroupComb	= "$pLeftComb:$pRightComb";

		# Get Right Packing Rule
		$rightPkgRule	    = $salesOrderObj->getRightPkgRule($leftPkgRule);		
		

		$objResponse->assign("pCategoryComb_".$rowId, "value", $productCategoryComb);
		$objResponse->assign("numPacks_".$rowId, "value", $numPacks);
		$objResponse->assign("mcPackageWt_".$rowId, "value", $mcPackageWt);
		$objResponse->assign("mcpComb_".$rowId, "value", $mcCombination);
		$objResponse->assign("pkgGroup_".$rowId, "value", $pkgGroupComb); // Pkg group Combination
		$objResponse->assign("leftPkgRule_".$rowId, "value", $leftPkgRule);		
		$objResponse->assign("rightPkgRule_".$rowId, "value", $rightPkgRule);
		
		$objResponse->assign("pGrossWt_".$rowId, "value", number_format($productGrossWt,2,'.',''));
		$objResponse->assign("pMCPkgGrossWt_".$rowId, "value", number_format($productMCPkgWt,2,'.',''));

		$objResponse->assign("mcPack_".$rowId, "value", $mcPacks);
		$objResponse->assign("loosePack_".$rowId, "value", $loosePacks);
		$objResponse->assign("hidMCPkg_".$rowId, "value", $mcPackingId);
		$objResponse->assign("hidMCPkgWtId_".$rowId, "value", $mcPkgWtId);
		

		sleep(1);
		$objResponse->script("multiplySalesOrderItem();");
		return $objResponse;			
	}

	# Get MRP Product Records
	function getProductRecs($distributorId, $stateId, $rateListId, $mode, $tableRowCount, $selDate, $selDistMgnRateListId, $cityId, $billingType)
	{
		
		

		
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		$manageRateListObj	= new ManageRateList($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$sDate		= mysqlDateFormat($selDate);
		$exportEnabled = ($billingType=="E")?"Y":"N";		

		//rekha rnd
		//$objResponse->alert($selDate);
		//$objResponse->alert($rateListId);
		
		//end code 
		
		# Product Price Rate List
		$productPriceRateListId = $manageRateListObj->getRateList("PMRP", $sDate);
		
		# Dist wise Margin Rate List Id	
		$distMarginRateListId = $distMarginRateListObj->getRateList($distributorId, $sDate);

		if ($stateId=="") {
			$stateRecs	= $salesOrderObj->getStateRec($distributorId, $exportEnabled);
			if (sizeof($stateRecs)==1) $stateId = $stateRecs[0][0];
		}
		
		if ($cityId=="") {
			$cityRecs = $salesOrderObj->getCityRec($distributorId, $stateId, $exportEnabled);
			if (sizeof($cityRecs)==1) $cityId = $cityRecs[0][0];
		}
		
		# Product MRP Master Records
		$mrpProductRecords = $salesOrderObj->getMRPProducts($distMarginRateListId, $distributorId, $stateId, $productPriceRateListId, $cityId, $exportEnabled);
		$objResponse->script("getItemArr('".json_encode($mrpProductRecords)."','$tableRowCount')");
		/*
		for ($i=0; $i<=$tableRowCount; $i++) {			
		       	$objResponse->addCreateOptions("selProduct_".$i, $mrpProductRecords, "hidSelStock_".$i);
		}
		*/	
		
		$objResponse->script("disUnitRate($stateId, $cityId);");
		$objResponse->script("clearFields();");
		
		return $objResponse;
	}

	# Get MRP Product Records (When Edit Section)
	function getMRPProductRowWise($distributorId, $stateId, $productPriceRateListId, $distMarginRateListId, $rowId)
	{
		$objResponse 	= new NxajaxResponse();
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);		
		# Product MRP Master Records		
		$mrpProductRecords = $salesOrderObj->getMRPProducts($distMarginRateListId, $distributorId, $stateId, $productPriceRateListId);
		//$mrpProductRecords = $salesOrderObj->getSelProducts();
		$objResponse->addCreateOptions("selProduct_".$rowId, $mrpProductRecords, "hidSelStock_".$rowId);
		return $objResponse;
	}
	
	function getActiveProducts($distributorId, $stateId, $productPriceRateListId, $distMarginRateListId)
	{
		$objResponse 	= new NxajaxResponse();		
		//$databaseConnect= new DatabaseConnect();
		//$salesOrderObj 	= new SalesOrder($databaseConnect);
		# Product MRP Master Records
		$mrpProductRecords = $salesOrderObj->getActiveProducts($distMarginRateListId, $distributorId, $stateId, $productPriceRateListId);
		return $objResponse;
	}	

	# Dist MGn Rec
	function getDistMgnRec($distributorId, $mode, $disMgnRateList, $selDate)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$sDate		= mysqlDateFormat($selDate);
		
		$distMarginRateListId = $distMarginRateListObj->getRateList($distributorId, $sDate);
		/*
		if ($mode==1) $distMarginRateListId = $distMarginRateListObj->getRateList($distributorId, $sDate);
		else $distMarginRateListId = $disMgnRateList;
		*/

		$objResponse->assign("distMgnRateListId", "value", $distMarginRateListId);
		return $objResponse;		
	}

	# Get Location List
	function getCityList($distributorId, $stateId, $selCityId, $billingType)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);

		$exportEnabled = ($billingType=="E")?"Y":"N";

		if ($stateId=="") {
			$stateRecs	= $salesOrderObj->getStateRec($distributorId, $exportEnabled);
			if (sizeof($stateRecs)==1) $stateId = $stateRecs[0][0];
			else $objResponse->script("document.getElementById('selState').value=''");
		}
		
		// City Recs
		$distCityListRecs = $salesOrderObj->getDistributorCityRecs($distributorId, $stateId, $exportEnabled);

		list($taxType, $billingForm, $billingStateId, $exBillingForm) = $salesOrderObj->getDistTaxType($distributorId, $stateId, $exportEnabled);
		$objResponse->assign("taxType", "value", $taxType);
		$objResponse->assign("billingForm", "value", $billingForm);
		$objResponse->assign("hidExBillingForm", "value", $exBillingForm);		
		$objResponse->addDropDownOptions("selCity",$distCityListRecs,$selCityId);
		return $objResponse;
	}

	# In Edit Mode Get MC Packing Records
	function getMCPackingRecs($productId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();		
		$salesOrderObj		= new SalesOrder($databaseConnect);
		if ($productId) {
			# Find the Product Net Wt from m_product_manage
			$productNetWt = $salesOrderObj->getProductNetWt($productId);
			$mcpackingRecords	= $salesOrderObj->getMCPkgRecs($productNetWt);	
			$objResponse->addCreateOptions("selMcPkg_".$rowId, $mcpackingRecords, "hidMCPkg_".$rowId);
		}
		return $objResponse;
	}

	# Check Valid Transporter (Using in Sales Order Processing)	
	function checkValidTransporter($transporterId, $selDate)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$orderDispatchedObj	= new OrderDispatched($databaseConnect);
		$transporterRateListObj	= new TransporterRateList($databaseConnect);
		$dispatchDate		= mysqlDateFormat($selDate);
		# Check valid Transporter
		$validTransporter	= $orderDispatchedObj->checkValidTransporter($transporterId);

		$tOtherChargeType = "TOC";	
		$tRateMasterType  = "TRM";

		# Current Transporter Rate List Id
		//$cTRMRateListId = $transporterRateListObj->latestRateList($transporterId,$tRateMasterType);
		$cTRMRateListId = $transporterRateListObj->getValidRateList($transporterId, $tRateMasterType, $dispatchDate);
		# Current Transporter Other Charge Rate List Id
		//$cTOCRateListId = $transporterRateListObj->latestRateList($transporterId,$tOtherChargeType);
		$cTOCRateListId = $transporterRateListObj->getValidRateList($transporterId, $tOtherChargeType, $dispatchDate);

		if (!$validTransporter) {
			$objResponse->alert("Please choose a Valid Transporter.\nRenew the validity of the selected Transpoter.");
			$objResponse->script("clearTransporter();");
			$objResponse->assign("transporterRateListId", "value", '');
			$objResponse->assign("transOtherChargeRateListId", "value", '');
		} else {
			if ($cTOCRateListId=="") {
				$objResponse->alert("Please set the selected Transporter Other Charges.");	
				$objResponse->script("clearTransporter();");
			}
			if ($cTRMRateListId=="") {
				$objResponse->alert("Please set the selected Transporter Rate/Kg.");	
				$objResponse->script("clearTransporter();");
			}
			$objResponse->assign("transporterRateListId", "value", $cTRMRateListId);
			$objResponse->assign("transOtherChargeRateListId", "value", $cTOCRateListId);
		}
		return $objResponse;
	}

	# Chk valid Despatch Date
	function chkValidDespatchDate($selDate)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		$manageChallanObj = new ManageChallan($databaseConnect);
		$sDate		= mysqlDateFormat($selDate);
		$chkValidDate   = $manageChallanObj->chkAllowedSOEntry($sDate);
		if (!$chkValidDate) {
			$objResponse->alert("Please check the selected despatch date.");
			$objResponse->assign("validDespatchDate", "value", 1);
		} else {
			$objResponse->assign("validDespatchDate", "value", 0);
		}		
		return $objResponse;	
	}

	# Chk valid Despatch Date
	function chkValidInvoiceDate($selDate)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		$manageChallanObj = new ManageChallan($databaseConnect);
		$manageRateListObj = new ManageRateList($databaseConnect);
		$sDate		= mysqlDateFormat($selDate);
		$chkValidDate   = $manageChallanObj->chkAllowedSOEntry($sDate);

		# Product Price Rate List (PMRP)	
		$productPriceRateListId = $manageRateListObj->getRateList("PMRP",$sDate);

		# Edu Cess
		list($eduCess, $eduCessRLId) = $salesOrderObj->getEduCessDuty($sDate);
		
		#Sec Edu Cess
		list($secEduCess, $secEduCessRLId) = $salesOrderObj->getSecEduCessDuty($sDate);

		if (!$chkValidDate) {
			$objResponse->alert("Please check the selected Invoice date.");
			$objResponse->assign("validInvoiceDate", "value", 1);
		} else {
			$objResponse->assign("validInvoiceDate", "value", 0);
		}

		$objResponse->assign("productPriceRateList", "value", $productPriceRateListId);
		$objResponse->assign("hidEduCess", "value", $eduCess);
		$objResponse->assign("hidEduCessRLId", "value", $eduCessRLId);
		$objResponse->assign("hidSecEduCess", "value", $secEduCess);
		$objResponse->assign("hidSecEduCessRLId", "value", $secEduCessRLId);
		
		return $objResponse;	
	}


	# PO Number Exist
	function chkPONumberExist($poNo, $mode, $cSOId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$salesOrderObj 		= new SalesOrder($databaseConnect);
		$chkPONumExist = $salesOrderObj->checkPONumberExist($poNo, $cSOId);
		if ($chkPONumExist && $poNo!="") {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "$poNo is already in use.");
			$objResponse->script("disableSOButton($mode);");
		} else  {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "");
			$objResponse->script("enableSOButton($mode);");
		}
		return $objResponse;
	}


	# Get Location List
	function getAreaList($distributorId, $stateId, $cityId, $selAreaId, $billingType)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);

		$exportEnabled = ($billingType=="E")?"Y":"N";

		if ($stateId=="") {
			$stateRecs	= $salesOrderObj->getStateRec($distributorId, $exportEnabled);
			if (sizeof($stateRecs)==1) {
				$stateId = $stateRecs[0][0];
			} 
		}

		if ($cityId=="") {
			$cityRecs	= $salesOrderObj->getCityRec($distributorId, $stateId, $exportEnabled);
			if (sizeof($cityRecs)==1) {
				$cityId = $cityRecs[0][0];
			} 
		}
		# Chk Octroi exempted
		$octroiExempted = $salesOrderObj->chkOctroiExempted($distributorId, $stateId, $cityId);

		if ($octroiExempted) {
			$objResponse->assign("octroiExempted","value",'Y');
			$objResponse->script("OECRow('Y')");
		} else {
			$objResponse->assign("octroiExempted","value",'N');
			$objResponse->script("OECRow('N')");	
		}

		# Credit balance
		$displayResult = "";
		if ($distributorId && $cityId) {
			$creditBalance = $salesOrderObj->getCreditBalance($distributorId, $cityId);
			$displayResult = displayCrBal($creditBalance);
		}		
		$objResponse->assign("creditBalRow", "innerHTML", $displayResult);

		# Get Distibutor status -----
		$distInactive = "";
		if ($distributorId && $stateId && $cityId) {
			$distInactive = $salesOrderObj->chkDistributorInactive($distributorId, $stateId, $cityId);
		}
		$objResponse->assign("distributorInactive", "value", $distInactive);
		# ------------------------ Status ends here

		# Get Area
		$distAreaListRecs = $salesOrderObj->getDistributorAreaRecs($distributorId, $stateId, $cityId, $exportEnabled);	
		$objResponse->addDropDownOptions("selArea", $distAreaListRecs, $selAreaId);
		return $objResponse;
	}

	# Get Sales Orders
	function getSalesOrders($fromDate, $tillDate, $invoiceType, $selSOId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$orderDispatchedObj	= new OrderDispatched($databaseConnect);
		
		$salesOrders	= $orderDispatchedObj->getSalesOrders(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceType);
		$objResponse->addDropDownOptions("selSOId", $salesOrders, $selSOId);
		return $objResponse;
	}

	# Update Pending SO Rec
	function updatePendingSO()
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$salesOrderObj		= new SalesOrder($databaseConnect);
		$taxMasterObj		= new TaxMaster($databaseConnect); 
		$marginStructureObj	= new MarginStructure($databaseConnect);
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$manageRateListObj	= new ManageRateList($databaseConnect);

		$changesUpdateMasterObj	= new ChangesUpdateMaster($databaseConnect, $salesOrderObj, $taxMasterObj, $marginStructureObj, $distMarginStructureObj, $distMarginRateListObj, $manageRateListObj);

		$updatePendingSO = $changesUpdateMasterObj->updateAllPendingSO();
		if ($updatePendingSO) {
			$objResponse->alert("Successfully updated all pending Sales Order details.");
			$objResponse->script("document.getElementById('frmSalesOrder').submit();");
		}
		return $objResponse;
	}

	# Octroi exempted
	function updateSOModifiedTime($salesOrderId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);				
		if ($salesOrderId) {			
			$salesOrderObj->updateModifiedRec($salesOrderId, '', 'U');
		}
		return $objResponse;
	}

	function getProformaInvoiceNo()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		$proformaInvoiceNo 	= $salesOrderObj->getNextProformaInvoiceNo();
		$objResponse->assign("proformaInvoiceNo","value",$proformaInvoiceNo);
		return $objResponse;
	}

	function getSampleInvoiceNo()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		$sampleInvoiceNo	= $salesOrderObj->getNextSampleInvoiceNo();
		$objResponse->assign("sampleInvoiceNo","value",$sampleInvoiceNo);
		return $objResponse;
	}

	# Update SO Main Rec
	function updateSOMainRec($soId, $selDate)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$salesOrderObj		= new SalesOrder($databaseConnect);
		$taxMasterObj		= new TaxMaster($databaseConnect); 
		$marginStructureObj	= new MarginStructure($databaseConnect);
		$distMarginStructureObj	= new DistributorMarginStructure($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		$manageRateListObj	= new ManageRateList($databaseConnect);

		$changesUpdateMasterObj	= new ChangesUpdateMaster($databaseConnect, $salesOrderObj, $taxMasterObj, $marginStructureObj, $distMarginStructureObj, $distMarginRateListObj, $manageRateListObj);

		$updateSOMainRec = $changesUpdateMasterObj->updateSalesOrderRec($soId, mysqlDateFormat($selDate));
		if ($updateSOMainRec) {
			//$objResponse->alert("Successfully updated all pending Sales Order details.");
			//$objResponse->script("document.getElementById('frmOrderDispatched').submit();");
			//$objResponse->script("document.getElementById('frmSalesOrder').submit();");
			//$objResponse->script("redirectUrl($soId);");
		}
		return $objResponse;
	}


	# Proforma Number Exist
	function chkProformaNoExist($invoiceNum, $mode, $cSOId, $selDate)
	{
		$selDate = mysqlDateFormat($selDate);
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$salesOrderObj 		= new SalesOrder($databaseConnect);
		$soYear			= date("Y", strtotime($selDate));
		
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $salesOrderObj->chkValidProformaNum($selDate, $invoiceNum);
			if ($validSONum) {
				$chkSONumExist = $salesOrderObj->checkProformaNumExist($invoiceNum, $cSOId);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt", "innerHTML", "");
					$objResponse->script("enableSOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSOButton($mode);");
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
		$salesOrderObj 		= new SalesOrder($databaseConnect);
		# Check valid SO Num
		if ($invoiceNum) {
			$validSONum = $salesOrderObj->chkValidSampleNum($selDate, $invoiceNum);
			if ($validSONum) {
				$chkSONumExist = $salesOrderObj->checkSampleNumExist($invoiceNum, $cSOId);
				if ($chkSONumExist && $invoiceNum!="") {
					$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is already in use. Please choose another one");
					$objResponse->script("disableSOButton($mode);");
				} else  {
					$objResponse->assign("divNumExistTxt", "innerHTML", "");
					$objResponse->script("enableSOButton($mode);");
				}
			} else {
				$objResponse->assign("divNumExistTxt", "innerHTML", "$invoiceNum is not valid.Please check the challan Settings.");
				$objResponse->script("disableSOButton($mode);");
			}
		}
		return $objResponse;
	}


	# Generate Packing Instruction
	function genPkgInstruction($selSOId, $userId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$salesOrderObj 		= new SalesOrder($databaseConnect);
		if ($selSOId && $userId) {
			$updatePkngInstRec = $salesOrderObj->addPackingInstruction($selSOId, $userId);
		}
		return $objResponse;
	}

	
	# Generate Gate Pass
	function genGatePass($selSOId, $userId,$company,$unit,$number_gen)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		if($selSOId && $userId) 
		{
			$updateGatePassStatus = $salesOrderObj->addGatePass($selSOId, $userId,$company,$unit,$number_gen);
		}
		return $objResponse;
	}

	# Get All Distributor
	function getDistributor($fromDate, $tillDate, $invoiceTypeFilter, $selDistributorId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$salesOrderObj 	= new SalesOrder($databaseConnect);
		
		if ($fromDate!="" && $tillDate!="") {
			$distributorRecs	= $salesOrderObj->getDistributorList(mysqlDateFormat($fromDate), mysqlDateFormat($tillDate), $invoiceTypeFilter);
			$objResponse->addDropDownOptions("distributorFilter", $distributorRecs, $selDistributorId);
		}
		return $objResponse;
	}

	# Displaying details at the time of changing an option
	function displayUnitRate($distributorId, $stateId, $invoiceDate, $selCity, $billingType, $arrVal) 
	{
		$objResponse 	= new NxajaxResponse();
		$uArr = explode(",",$arrVal);
		if (sizeof($uArr)>0) {
			for ($i=0;$i<sizeof($uArr);$i++) {
				$productId =  $uArr[$i];
				getStockUnitRate($distributorId, $productId, $i, $stateId, $invoiceDate, $selCity, $objResponse, $billingType);
			}
		}
		//$objResponse->script("multiplySalesOrderItem();");
		return $objResponse;
	}

	function displayCrBal($creditBalance)
	{
		$displayR 	= '<table><TR><TD class="listing-head">';
		$styleColor 	= ($creditBalance<=0)?"style='color:red'":"style='color:green'";
		$displayR 	.= '<span '.$styleColor.'>Credit Balance:</span></TD>';
		$displayR 	.= '<td class="listing-item"><span '.$styleColor.'>&nbsp;';
		$displayR 	.= '<strong>Rs.&nbsp;'.number_format($creditBalance,2,'.',',').'</strong></span>';
		$displayR 	.= '</td></TR></table>';
		return $displayR ;
	}

	/*
		Get Billing Type : Domestic/Export
	*/
	function getBillingType($distributorId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect	= new DatabaseConnect();
		$salesOrderObj		= new SalesOrder($databaseConnect);
		
		$exportEnabled = $salesOrderObj->chkExportBilling($distributorId);

		if ($exportEnabled) $objResponse->script("$('#billingType').append($('<option></option>').val('E').html('Export'));");
		else $objResponse->script("$(\"#billingType option[value='E']\").remove();");
		
		return $objResponse;
	}

	function getUnit($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("unit",$unit,$cel);
		return $objResponse;	
	}

	#Generate GatePass
	function getPONumber($selDate,$compId,$invUnit)
	{
		$selDate=mysqlDateFormat($selDate);
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$salesOrderObj		    = new SalesOrder($databaseConnect);
		//$objResponse->alert("hiii");
		$checkGateNumberSettingsExist=$salesOrderObj->chkValidGatePassId($selDate,$compId,$invUnit);
		if (sizeof($checkGateNumberSettingsExist)>0){
		$alpId=$checkGateNumberSettingsExist[0][0];
		$alphaCode=$salesOrderObj->getAlphaCode($alpId);
		$alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert($alphaCodePrefix);
		$numbergen=$checkGateNumberSettingsExist[0][0];
		//$objResponse->alert($numbergen);
		$checkExist=$salesOrderObj->checkGatePassDisplayExist($numbergen);
			if ($checkExist>0)
			{
				$getFirstRecord=$salesOrderObj->getmaxGatePassId($numbergen);
				$getFirstRec= $getFirstRecord[0];
				//$objResponse->alert($getFirstRec);
				$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
				//$objResponse->alert($getFirstRecEx[1]);
				$nextGatePassId=$getFirstRecEx[1]+1;
				$validendno=$salesOrderObj->getValidendnoGatePassId($selDate,$compId,$invUnit);
				//$objResponse->alert($nextGatePassId);
				if ($nextGatePassId>$validendno)
				{
					$PurchaseOrderMsg="Please set the Purchase Order Id in Settings,since it reached the end no";
					$objResponse->assign("divPOIdExistTxt","innerHTML",$PurchaseOrderMsg);
				}
				else
				{
					$disGateNo="$alphaCodePrefix$nextGatePassId";
					//$objResponse->alert($disGateNo);
					$objResponse->assign("poNo","value","$disGateNo");	
					$objResponse->assign("number_gen_id","value","$numbergen");	
					$objResponse->assign("divPOIdExistTxt","innerHTML","");
				}
			}
			else
			{
				$validPassNo=$salesOrderObj->getValidGatePassId($selDate,$compId,$invUnit);	
				$checkPassId=$salesOrderObj->chkValidGatePassId($selDate,$compId,$invUnit);
				$disGatePassId="$alphaCodePrefix$validPassNo";
				$objResponse->assign("poNo","value","$disGatePassId");	
				$objResponse->assign("number_gen_id","value","$numbergen");	
				$objResponse->assign("divPOIdExistTxt","innerHTML","");
			}
		}
		else
		{
			//$objResponse->alert("hi");
			$PurchaseOrderMsg="Please set the Purchase Order Id in Settings";
			$objResponse->assign("poNo","value","");	
			$objResponse->assign("divPOIdExistTxt","innerHTML",$PurchaseOrderMsg);
		}
		return $objResponse;
	}
	
	function displayGatepass($company,$unit,$salesOrderId)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$salesOrderObj		    = new SalesOrder($databaseConnect);
		$displayDiv=$salesOrderObj->displayGatepass($company,$unit,$salesOrderId);
		$objResponse->assign("dialog","innerHTML",$displayDiv);
		return $objResponse;
	}

	$xajax->register(XAJAX_FUNCTION, 'displayGatepass', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getPONumber', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->registerFunction("getActiveProducts");
	$xajax->registerFunction("displayUnitRate");

	// showLoading, hideLoading
	$xajax->register(XAJAX_FUNCTION, 'getUnit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

	$xajax->register(XAJAX_FUNCTION, 'getStockUnitRate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDistStateList', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkSONumberExist', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getPackageDetails', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getProductRecs', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getCityList', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getMCPackingRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkValidTransporter', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkValidDespatchDate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkPONumberExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getAreaList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkValidInvoiceDate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getMRPProductRowWise', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getSalesOrders', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'updatePendingSO', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'updateSOModifiedTime', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getProformaInvoiceNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getSampleInvoiceNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'updateSOMainRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkProformaNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chkSampleNoExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDistMgnRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'genPkgInstruction', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'genGatePass', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDistributor', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getBillingType', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

	$xajax->ProcessRequest();
?>