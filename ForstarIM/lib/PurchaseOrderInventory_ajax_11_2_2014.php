<?php
//require_once("lib/databaseConnect.php");
//require_once("PurchaseOrderInventory_Class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->alert($sSelectId);
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
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}		
	}
	# get stock Records
	function getSupplierStockRecords($selectId, $supplierId, $poItem, $supplierRateListId, $tableRowCount, $hidStockName, $mode)
	{		
		$objResponse 			= new NxajaxResponse();
		//$objResponse->alert("hai");
		$databaseConnect 		= new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		if ($mode==1) {
			$supplierRateListObj		= new SupplierRateList($databaseConnect);
			//$supplierRateListId		= $supplierRateListObj->latestRateList($supplierId);
			$supplierRateListId		= $supplierRateListObj->latestRateListUnit($supplierId);
			$objResponse->assign("hidSupplierRateListId", "value", $supplierRateListId);
		}
		//$objResponse->alert("hai");
		$data = $purchaseOrderInventoryObj->fetchSupplierStockRecords($supplierId, $poItem, $supplierRateListId);		
		//$objResponse->alert($selectId."-"."sup--".$supplierId."-"."po".$poItem."-"."sr---".$supplierRateListId."-".$tableRowCount);		
		//$objResponse->alert($supplierRateListId);
		$fssairegno = $purchaseOrderInventoryObj->getFssairegno($supplierId);
		$servicetaxno = $purchaseOrderInventoryObj->getServicetaxno($supplierId);
		$vatno=$purchaseOrderInventoryObj->getVatno($supplierId);
		$cstno=$purchaseOrderInventoryObj->getCstno($supplierId);
		$panno=$purchaseOrderInventoryObj->getPanno($supplierId);
		$objResponse->assign("fssairegnoid", "innerHTML", $fssairegno);	
		$objResponse->assign("servicetaxid", "innerHTML", $servicetaxno);
		$objResponse->assign("vatnoid", "innerHTML", $vatno);	
		$objResponse->assign("cstnoid", "innerHTML", $cstno);
		$objResponse->assign("pannoid", "innerHTML",$panno);
		$balanceStockQty = $purchaseOrderInventoryObj->getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitId);
		$objResponse->addDropDownOptions("selStock_$rowId", $balanceStockQty, $selGradeId);
		
		
		for ($i=0; $i<=$tableRowCount; $i++) {			
		       	$objResponse->addCreateOptions($selectId.$i, $data, $hidStockName.$i);			
		}
		return $objResponse;
	}
	
	

	function getSupplierStockRecordsAll($selectId, $supplierId, $poItem, $supplierRateListId, $tableRowCount, $hidStockName, $mode)
	{		
		$objResponse 			= new NxajaxResponse();
		//$objResponse->alert($hidStockName);
		$databaseConnect 		= new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		if ($mode==1) {
			$supplierRateListObj		= new SupplierRateList($databaseConnect);
			//$supplierRateListId		= $supplierRateListObj->latestRateList($supplierId);
			$supplierRateListId		= $supplierRateListObj->latestRateListUnit($supplierId);
			$objResponse->assign("hidSupplierRateListId", "value", $supplierRateListId);
		}
		$objResponse->alert("$selectId");
		//$data = $purchaseOrderInventoryObj->fetchSupplierStockRecords($supplierId, $poItem, $supplierRateListId);
		$data=$purchaseOrderInventoryObj->getStockUnitDetails($supplierId,$supplierRateListId,$plantUnitid);
		$n=count($data);
$objResponse->alert($n);
		//$objResponse->alert($selectId."-"."sup--".$supplierId."-"."po".$poItem."-"."sr---".$supplierRateListId."-".$tableRowCount);		
		//$objResponse->alert($supplierRateListId);
		$fssairegno = $purchaseOrderInventoryObj->getFssairegno($supplierId);
		$servicetaxno = $purchaseOrderInventoryObj->getServicetaxno($supplierId);
		$vatno=$purchaseOrderInventoryObj->getVatno($supplierId);
		$cstno=$purchaseOrderInventoryObj->getCstno($supplierId);
		$panno=$purchaseOrderInventoryObj->getPanno($supplierId);
		$objResponse->assign("fssairegnoid", "innerHTML", $fssairegno);	
		$objResponse->assign("servicetaxid", "innerHTML", $servicetaxno);
		$objResponse->assign("vatnoid", "innerHTML", $vatno);	
		$objResponse->assign("cstnoid", "innerHTML", $cstno);
		$objResponse->assign("pannoid", "innerHTML",$panno);
		$balanceStockQty = $purchaseOrderInventoryObj->getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitId);
		//$objResponse->addDropDownOptions("selStock_$rowId", $balanceStockQty, $selGradeId);
		
		
		for ($i=0; $i<=$tableRowCount; $i++) {			
		       	$objResponse->addCreateOptions($selectId.$i, $data, $hidStockName.$i);	
				//$objResponse->alert("kkk".$data[0]);
		}

/*for ($i=0; $i<=$tableRowCount; $i++) {
if ($mode==1) {
			#Supplier Rate List
			$supplierRateListObj = new SupplierRateList($databaseConnect);
			$supplierRateListId  = $supplierRateListObj->latestRateList($supplierId);		
		}
		
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $selectId.$i, $supplierRateListId);		
	$objResponse->alert("Supp=$supplierId,Stock=$hidStockName$i,row=$i,RateList=$supplierRateListId,$selectId.$i");

		$objResponse->assign("unitPrice_".$i, "value", $unitRate);
}*/



		return $objResponse;
	}
	
	


	//function fetchSelectedSupplierStockRecords($selectSupplierId, $poItem, $supplierRateListId){
function getStockUnit($plantUnitId,$rowId,$selectSupplierId,$stockid,$supplierRateListId,$mode){

	$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		/*
		#Supplier Rate List
		$supplierRateListObj		= new SupplierRateList($databaseConnect);
		$supplierRateListId = $supplierRateListObj->latestRateList();		
		*/
		//$objResponse->alert("hai-----");
		//$objResponse->alert($plantUnitId);
		//$objResponse->alert($rowid);
		//$objResponse->alert($selectSupplierId);
		//$objResponse->alert($supplierRateListId);
		//$supplierRateListId=2;
		$balanceStockQty = $purchaseOrderInventoryObj->getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitId);
		//$objResponse->alert("<pre>".$balanceStockQty."</pre>");
			$objResponse->addDropDownOptions("selStock_$rowId", $balanceStockQty, $selGradeId);
		return $objResponse;
		//return $objResponse;	
/*if ($poItem) {
			$qry = "select id, code, name from m_stock order by name asc";
		} else {
			$qry = "select a.stock_id, b.code, b.name from supplier_stock a, m_stock b where b.id=a.stock_id and a.supplier_id='$selectSupplierId' and a.rate_list_id='$supplierRateListId' order by b.name asc";
		}		
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;*/
}
	# get Stock Details
	function getStockBalanceQty($stockId, $rowId,$plantUnitId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		/*
		#Supplier Rate List
		$supplierRateListObj		= new SupplierRateList($databaseConnect);
		$supplierRateListId = $supplierRateListObj->latestRateList();		
		*/
		//$objResponse->alert($plantUnitId);
		$balanceStockQty = $purchaseOrderInventoryObj->getBalanceQty($stockId,$plantUnitId);
		$objResponse->assign("balanceQty_".$rowId, "innerHTML", $balanceStockQty);	
		$objResponse->assign("hidSelStock_".$rowId, "value", $stockId);	 // Assign hid stock value
		//$objResponse->alert($stockId);			
            	return $objResponse;		
	}

	function getStockUnitRate($supplierId, $stockId, $rowId, $supplierRateListId, $mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		if (($mode==1) || ($mode==0)) {
			#Supplier Rate List
			$supplierRateListObj = new SupplierRateList($databaseConnect);
			//$supplierRateListId  = $supplierRateListObj->latestRateList($supplierId);	
			$supplierRateListId  = $supplierRateListObj->latestRateListUnit($supplierId, $stockId);
		}
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);
		//$unitRate = $purchaseOrderInventoryObj->getStockItemMaxRate($supplierId, $stockId);
		//$proddesc=
		//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId--ur--$unitRate---mode=$mode");
		$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");		
            	return $objResponse;
	}

	function getStockUnitRatePlantUnit($supplierId, $stockId, $rowId, $supplierRateListId, $mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		if ($mode==1) {
			#Supplier Rate List
			$supplierRateListObj = new SupplierRateList($databaseConnect);
			$supplierRateListId  = $supplierRateListObj->latestRateList($supplierId);		
		}
		//$supplierRateListId=6;
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);
		//$unitRate = $purchaseOrderInventoryObj->getStockItemMaxRate($supplierId, $stockId);
		//$proddesc=
	//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId");
		$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");		
            	return $objResponse;
	}



	function getOtherSuppliersStockRec($stockId, $supplierId, $poItem, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		//$objResponse->alert("hai");
		/*
		#Supplier Rate List
		$supplierRateListObj		= new SupplierRateList($databaseConnect);
		$supplierRateListId = $supplierRateListObj->latestRateList();	
		, $supplierRateListId
		*/
		//$objResponse->alert("Stock=$stockId, Supplier=$supplierId, PO=$poItem, Row=$rowId");
		$getSupplierStockRecs = $purchaseOrderInventoryObj->getSupplierRec($stockId, $supplierId, $poItem);

		$otherSupplier = "";
		//$otherSupplier = "<table class='print'>";
		if (sizeof($getSupplierStockRecs)>0) {
			//$objResponse->alert("hai1");
			$otherSupplier = "<table class='print'>";
			if ($poItem!="") {
				$otherSupplier	.= "<tr><TD>&nbsp;</TD>";
				$k = 0;
		foreach($getSupplierStockRecs as $gsr) {
			$k++;
			$supplierId = $gsr[1];
			$supplierName = $gsr[4];
			$supplierNegotiatedPrice = $gsr[3];
			$supplierChkd = "";
			if ($p["selSupplier_".$k."_".$rowId]!="") {
				$supplierChkd = "checked";
			}
			$selSupplierRateList = $gsr[5];
	
			$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;' align='center'><input type=\"checkbox\" name=\"selSupplier_".$k."_$rowId\" id=\"selSupplier_".$k."_$rowId\" value=\"$supplierId\" onclick=\"uncheckSelected('selSupplier_$k"."_$rowId',$rowId);return multiplyPOItem(document.frmPurchaseOrderInventory, '$poItem');\" $supplierChkd class='chkBox'></TD><input type='hidden' name='negoPrice_$k"."_$rowId' id='negoPrice_$k"."_$rowId' value='$supplierNegotiatedPrice'><!--input type='hidden' name='hidRateListId_$k"."_$rowId' id='hidRateListId_$k"."_$rowId' value='$selSupplierRateList'-->";
		}
		$otherSupplier .= "<input type='hidden' name='hidSupplierCount_$rowId' id='hidSupplierCount_$rowId' value='$k'></tr>";
	}
	$otherSupplier .= "<tr align='center'><TD class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Supplier</TD>";
	foreach($getSupplierStockRecs as $gsr) {
		$supplierName = $gsr[4];
		$supplierNegotiatedPrice = $gsr[3];
	$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplierName</TD>";
	}
	//$objResponse->alert($supplierName);
	$otherSupplier .= "</tr><tr><td class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Price</td>";
	foreach($getSupplierStockRecs as $gsr) {
		$supplierName = $gsr[4];
		$supplierNegotiatedPrice = $gsr[3];
	$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;' align='right'>$supplierNegotiatedPrice</TD>";	
		}
	$otherSupplier .= "</tr></table>";
	//$otherSupplier .= "";
	} else { // No Supplier
	$otherSupplier = "<span class='err1'>No Suppliers Found</span><input type='hidden' name='hidSupplierCount_$rowId' id='hidSupplierCount_$rowId' value='0'><input type='hidden' name='selSupplier_$k_$rowId' id='selSupplier_$k_$rowId' value='$supplierId'>";
	}	
	//$otherSupplier .= "</table>";
	//$objResponse->alert($rowId);
	//$objResponse->alert($otherSupplier);
	$objResponse->assign("otherSupplierDiv_".$rowId, "innerHTML", $otherSupplier);		
        return $objResponse;	
	
	}


	function getLastPurchaseStockRec($stockId, $supplierId, $poItem, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		//$objResponse->alert("hai");
		list($quantity,$unitprice,$supplier) = $purchaseOrderInventoryObj->getLastPurchasePrice($stockId);
		if ($supplier=="")
		{
			$valueDisplay="<span class='err1'>No Suppliers Found</span>";
		}
		else {
		$valueDisplay="<table class='print'><tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Supplier</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplier</td></tr><tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Quantity</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$quantity</td></tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Unit Price</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$unitprice</td></tr></table>";
		}
		$objResponse->assign("LastPurchaseDiv_".$rowId, "innerHTML", $valueDisplay);
		return $objResponse;
	}

	function getStockMinimumOrderQty($stockId, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$minimumOrderQty = $purchaseOrderInventoryObj->getStockMinOrderQty($stockId);		
		$objResponse->assign("quantity_".$rowId, "value", $minimumOrderQty);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");		
            	return $objResponse;		
	}

	function chkStockItemOrderQty($stockId, $rowId, $qty, $mode)
	{		
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);
		$minimumOrderQty = $purchaseOrderInventoryObj->getStockMinOrderQty($stockId);
		$stockRec		= $purchaseOrderInventoryObj->getStockItemRec($stockId);		
		$minOrderUnit		= $stockRec[0];
		$minOrderQtyPerUnit	= $stockRec[1];
		if ($qty%$minOrderUnit!=0 || $qty<$minimumOrderQty) {			
			$msg = "";
			if ($qty<$minimumOrderQty) $msg = "<br>Minimum order qty is $minimumOrderQty";
			else $msg = "<br>Order Qty should be the increment of $minOrderUnit ";
			
			$objResponse->assign("orderQtyDivId_".$rowId, "innerHTML", $msg);	
			$objResponse->assign("hidStockStatus_".$rowId, "value", 1);
		} else {
			$objResponse->assign("orderQtyDivId_".$rowId, "innerHTML", "&nbsp;");
			$objResponse->assign("hidStockStatus_".$rowId, "value", "");			
		}
		return $objResponse;
	}

	
	function chkPONumberExist($poId, $mode)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);
		$chkPONoExist = $purchaseOrderInventoryObj->checkPONumberExist($poId);
		if ($chkPONoExist) {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "$poId is already in use. Please choose another one");
			$objResponse->script("disablePOButton($mode);");
		} else  {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "");
			$objResponse->script("enablePOButton($mode);");
		}
		return $objResponse;
	}

	/*function getSupplierStockRecords($supplierId, $poItem, $supplierRateListId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);
	}*/

	function getitemDescription($stockid,$rowId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);
		//$objResponse->alert("haitem");
		$categorydetails=$purchaseOrderInventoryObj->getCategoryDetails($stockid);
		//$objResponse->alert($stockid);
		$catId=$categorydetails[0];
		$subcatId=$categorydetails[1];
		//$objResponse->alert("kkk".$catId);
		//$objResponse->alert("kkk1".$subcatId);
		//$objResponse->alert($stock_main_id);
		$proddesc=$purchaseOrderInventoryObj->getPurchaseOrderdescr($catId,$subcatId,$stock_main_id);
		$objResponse->assign("proddesc_".$rowId, "innerHTML", $proddesc);
		return $objResponse;


	}

$xajax->registerFunction("getSupplierStockRecords");
$xajax->register(XAJAX_FUNCTION, 'getSupplierStockRecordsAll', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("getSupplierStockRecordsAll");
//$xajax->registerFunction("getStockBalanceQty");
$xajax->register(XAJAX_FUNCTION, 'getStockBalanceQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("getStockUnitRate");
$xajax->register(XAJAX_FUNCTION, 'getStockUnitRate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("getOtherSuppliersStockRec");
$xajax->register(XAJAX_FUNCTION, 'getOtherSuppliersStockRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("getStockMinimumOrderQty");
$xajax->register(XAJAX_FUNCTION, 'getStockMinimumOrderQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->registerFunction("chkStockItemOrderQty");
$xajax->registerFunction("chkPONumberExist");
//$xajax->registerFunction("getLastPurchaseStockRec");
$xajax->register(XAJAX_FUNCTION, 'getLastPurchaseStockRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("getitemDescription");
$xajax->register(XAJAX_FUNCTION, 'getitemDescription', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->registerFunction("getStockUnit");
$xajax->registerFunction("getStockUnitRatePlantUnit");
$xajax->ProcessRequest();
?>