<?php
//require_once("lib/databaseConnect.php");
//require_once("PurchaseOrderInventory_Class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

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
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}		
	}
	# get stock Records
	function getSupplierStockRecords($selectId, $supplierId, $poItem, $supplierRateListId, $tableRowCount, $hidStockName, $mode)
	{		
		$objResponse 			= new NxajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		if ($mode==1) {
			$supplierRateListObj		= new SupplierRateList($databaseConnect);			
			$supplierRateListId		= $supplierRateListObj->latestRateListUnit($supplierId);
			$objResponse->assign("hidSupplierRateListId", "value", $supplierRateListId);
		}
		
		$data = $purchaseOrderInventoryObj->fetchSupplierStockRecords($supplierId, $poItem, $supplierRateListId);		
		//$objResponse->alert($selectId."-"."sup--".$supplierId."-"."po".$poItem."-"."sr---".$supplierRateListId."-".$tableRowCount);		
		$fssaiRegNo = $purchaseOrderInventoryObj->getFssairegno($supplierId);
		$serviceTaxNo = $purchaseOrderInventoryObj->getServicetaxno($supplierId);
		$vatNo=$purchaseOrderInventoryObj->getVatno($supplierId);
		$cstNo=$purchaseOrderInventoryObj->getCstno($supplierId);
		$panNo=$purchaseOrderInventoryObj->getPanno($supplierId);
		$objResponse->assign("fssaiRegnoId", "innerHTML", $fssaiRegNo);	
		$objResponse->assign("serviceTaxId", "innerHTML", $serviceTaxNo);
		$objResponse->assign("vatNoId", "innerHTML", $vatNo);	
		$objResponse->assign("cstNoId", "innerHTML", $cstNo);
		$objResponse->assign("panNoId", "innerHTML",$panNo);
		$balanceStockQty = $purchaseOrderInventoryObj->getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitId);
		$objResponse->addDropDownOptions("selStock_$rowId", $balanceStockQty, $selGradeId);		
		for ($i=0; $i<=$tableRowCount; $i++) {			
		       	$objResponse->addCreateOptions($selectId.$i, $data, $hidStockName.$i);			
		}
		return $objResponse;
	}
	
	function getnetTotal($tableRowCount)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$objResponse->alert("kkkuuuu");
		//$objResponse->script("getSum(document.frmPurchaseOrderInventory,'','')");
		$objResponse->script("getNetTotalerr()");
		return $objResponse;

	}

	function getSupplierStockRecordsAll($selectId, $supplierId, $poItem, $supplierRateListId, $tableRowCount, $hidStockName, $mode)
	{		
		$objResponse 			= new NxajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		 $objResponse->script("showFnLoading()");
		if ($mode==1) {
			$supplierRateListObj		= new SupplierRateList($databaseConnect);
			//$supplierRateListId		= $supplierRateListObj->latestRateList($supplierId);
			$supplierRateListId		= $supplierRateListObj->latestRateListUnit($supplierId);
			$objResponse->assign("hidSupplierRateListId", "value", $supplierRateListId);
		}
		//$objResponse->alert("$selectId");
		//$data = $purchaseOrderInventoryObj->fetchSupplierStockRecords($supplierId, $poItem, $supplierRateListId);
		$data=$purchaseOrderInventoryObj->getStockUnitDetails($supplierId,$supplierRateListId,$plantUnitid);
		$n=count($data);
		//$objResponse->alert($n);
		//$objResponse->alert($selectId."-"."sup--".$supplierId."-"."po".$poItem."-"."sr---".$supplierRateListId."-".$tableRowCount);		
		$fssaiRegNo = $purchaseOrderInventoryObj->getFssaiRegNo($supplierId);
		$serviceTaxNo = $purchaseOrderInventoryObj->getServiceTaxNo($supplierId);
		$vatNo=$purchaseOrderInventoryObj->getVatNo($supplierId);
		$cstNo=$purchaseOrderInventoryObj->getCstNo($supplierId);
		$panNo=$purchaseOrderInventoryObj->getPanNo($supplierId);
		$objResponse->assign("fssaiRegnoId", "innerHTML", $fssaiRegNo);	
		$objResponse->assign("serviceTaxId", "innerHTML", $serviceTaxNo);
		$objResponse->assign("vatNoId", "innerHTML", $vatNo);	
		$objResponse->assign("cstNoId", "innerHTML", $cstNo);
		$objResponse->assign("panNoId", "innerHTML",$panNo);
		for ($i=0; $i<=$tableRowCount; $i++) {		       
			   $objResponse->addCreateOptions($selectId.$i,$data,$hidStockName.$i);
			  
			   $objResponse->script("calculateTotal(document.frmPurchaseOrderInventory,'',$i)");
			   // $objResponse->script("hideFnLoading()");
				//$objResponse->alert("iii".$i);
		}
		//$objResponse->alert("iii".$i);
		 //$objResponse->script("getNetTotal(document.frmPurchaseOrderInventory)");
		// $objResponse->alert("jjj".$i);
		//$objResponse->script("getSum(document.frmPurchaseOrderInventory,'','')");
		//$netTotal=10;
		//$objResponse->assign("totalQuantity", "value", $netTotal);
		return $objResponse;
	}
	

	function getRowUnitPrice($supplierId,$tableRowCount, $hidStockName, $mode,$stockId,$i,$quantity)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$supplierRateListObj = new SupplierRateList($databaseConnect);
		if (($mode==1) || ($mode==0)) {
			#Supplier Rate List			
		$supplierRateListId  = $supplierRateListObj->latestRateListUnit($supplierId, $stockId);	
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);				
		$total=$unitRate*$quantity;
		 //$objResponse->script("hideFnLoading()");
		$objResponse->assign("total_".$i,"value",$total);
		$objResponse->assign("unitPrice_".$i, "value", $unitRate);			
		}		
		return $objResponse;
	}


	//function fetchSelectedSupplierStockRecords($selectSupplierId, $poItem, $supplierRateListId){
	function getStockUnit($plantUnitId,$rowId,$selectSupplierId,$stockid,$supplierRateListId,$mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);		
		$balanceStockQty = $purchaseOrderInventoryObj->getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitId);		
		$objResponse->addDropDownOptions("selStock_$rowId", $balanceStockQty, $selGradeId);
		return $objResponse;
		
	}
	# get Stock Details
	function getStockBalanceQty($stockId, $rowId,$plantUnitId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);	
		//$objResponse->script("showFnLoading();");
		//$objResponse->alert("bal");
		$balanceStockQty = $purchaseOrderInventoryObj->getBalanceQty($stockId,$plantUnitId);
		$objResponse->assign("balanceQty_".$rowId, "innerHTML", $balanceStockQty);	
		$objResponse->assign("hidSelStock_".$rowId, "value", $stockId);	 // Assign hid stock value	
		//$objResponse->script("hideFnLoading();");
		//$objResponse->script("undelay()");
        return $objResponse;		
	}

function calculateNetTotalAmount($cntArrVal)
{
$objResponse = new NxajaxResponse();
$databaseConnect = new DatabaseConnect();
//$no=count($cArr);
$objResponse->alert("helloentered-$no");

			#Supplier Rate List
			$supplierRateListObj = new SupplierRateList($databaseConnect);
			$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
			
		//$netTotal=9;
		
		if ($cntArrVal!="") {					
					$cntArr = explode(",",$cntArrVal);
					if (sizeof($cntArr)>0) {
						$no=sizeof($cntArr);
						for ($i=0;$i<sizeof($cntArr);$i++) {
							//$objResponse->alert($i);
							
							$cntDataArr = $cntArr[$i];
							$cntData = explode(":",$cntDataArr);
							//$grossId	= $cntData[0];
							//$grossWt	= $cntData[1];
							//$basketWt	= $cntData[2];
							
							$supplierId=$cntData[2];
							$stockId=$cntData[0];
							$quantity=$cntData[1];
							$supplierRateListId  = $supplierRateListObj->latestRateListUnit($supplierId, $stockId);
							$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);
							$objResponse->alert("val===$unitRate");
							$total=$unitRate*$quantity;
							$netTotal=$netTotal+$total;
						}}}
		//$netTotal=5;
		
		$objResponse->assign("totalQuantity", "value", $netTotal);
		return $objResponse;

}
function hideFunction()
{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		//$objResponse->alert("hello");
		$objResponse->script("hideFnLoading();");
		//$objResponse->script("undelay()");
        return $objResponse;

}

function showFunction()
{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		//$objResponse->alert("hello");
		$objResponse->script("showFnLoading();");
		//$objResponse->script("undelay()");
        return $objResponse;

}
	function getStockUnitRate($supplierId, $stockId, $rowId, $supplierRateListId, $mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		//$objResponse->script("showFnLoading();");
		//$objResponse->alert("unitrate");
		if (($mode==1) || ($mode==0)) {
			#Supplier Rate List
			$supplierRateListObj = new SupplierRateList($databaseConnect);
			//$supplierRateListId  = $supplierRateListObj->latestRateList($supplierId);	
			$supplierRateListId  = $supplierRateListObj->latestRateListUnit($supplierId, $stockId);
		}
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);		
		//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId--ur--$unitRate---mode=$mode");
		
		//$objResponse->alert("entered");
		$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");
		//$objResponse->script("hideFnLoading();");
        return $objResponse;
	}

	function getStockUnitRateTotal($cArr)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$objResponse->script("showFnLoading();");
		if (($mode==1) || ($mode==0)) {
			#Supplier Rate List
			$supplierRateListObj = new SupplierRateList($databaseConnect);
			//$supplierRateListId  = $supplierRateListObj->latestRateList($supplierId);	
			//$supplierRateListId  = $supplierRateListObj->latestRateListUnit($supplierId, $stockId);
		}
		$netTotal=0;
		foreach ($cArr as $val){
		//$objResponse->alert($val);
		$arrValues=explode(":",$val);
		$supplierId=$arrValues[2];
		$stockId=$arrValues[0];
		$quantity=$arrValues[1];
		$supplierRateListId  = $supplierRateListObj->latestRateListUnit($supplierId, $stockId);
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);
		$total=$unitRate*$quantity;
		$netTotal=$netTotal+$total;
		}
		//$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);
		//$objResponse->alert("hello");
		//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId--ur--$unitRate---mode=$mode");
		//$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->assign("totalQuantity".$rowId, "value", $netTotal);
		$objResponse->script("hideFnLoading();");
		//$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");		
        return $objResponse;
	}

	function getStockUnitRatePlantUnit($supplierId, $stockId, $rowId, $supplierRateListId, $mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$objResponse->script("showFnLoading();");
		$objResponse->alert("entered");
		if ($mode==1) {
			#Supplier Rate List
			$supplierRateListObj = new SupplierRateList($databaseConnect);
			$supplierRateListId  = $supplierRateListObj->latestRateList($supplierId);		
		}
		//$supplierRateListId=6;
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);		
		//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId");		
		$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");
		// $objResponse->script("hideFnLoading()");
		//$objResponse->script("undelay()");
        return $objResponse;
	}



	function getOtherSuppliersStockRec($stockId, $supplierId, $poItem, $rowId)
	{
		 
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		// $objResponse->script("showFnLoading()");
		//$objResponse->script("showFnLoading();");
		//$objResponse->alert("other");
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
			//$objResponse->script("showFnLoading();");
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
			//$objResponse->script("showFnLoading();");
		$supplierName = $gsr[4];
		$supplierNegotiatedPrice = $gsr[3];
		$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplierName</TD>";
		}
		//$objResponse->alert($supplierName);
		$otherSupplier .= "</tr><tr><td class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Price</td>";
		foreach($getSupplierStockRecs as $gsr) {
			//$objResponse->script("showFnLoading();");
		$supplierName = $gsr[4];
		$supplierNegotiatedPrice = $gsr[3];
		$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;' align='right'>$supplierNegotiatedPrice</TD>";	
		}
		$otherSupplier .= "</tr></table>";
		//$otherSupplier .= "";
		} else { // No Supplier
		$otherSupplier = "<span class='err1'>No Suppliers Found</span><input type='hidden' name='hidSupplierCount_$rowId' id='hidSupplierCount_$rowId' value='0'><input type='hidden' name='selSupplier_$k_$rowId' id='selSupplier_$k_$rowId' value='$supplierId'>";
		//$objResponse->script("showFnLoading();");
		}
		//$objResponse->script("showFnLoading();");
		//$objResponse->script("showFnLoading();");
		//$objResponse->script("showFnLoading();");
			
		$objResponse->assign("otherSupplierDiv_".$rowId, "innerHTML", $otherSupplier);	
		//$objResponse->script("hideFnLoading();");
		return $objResponse;	
	
	}


	function getLastPurchaseStockRec($stockId, $supplierId, $poItem, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		//$objResponse->alert($stockId);
		//$objResponse->script("showFnLoading();");
		//$objResponse->alert("last");
		list($quantity,$unitprice,$supplier) = $purchaseOrderInventoryObj->getLastPurchasePrice($stockId);
		//$objResponse->script("showFnLoading();");
		if ($supplier=="")
		{
			$valueDisplay="<span class='err1'>No Suppliers Found</span>";
			$objResponse->script("showFnLoading();");
		}
		else {
		$valueDisplay="<table class='print'><tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Supplier</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplier</td></tr><tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Quantity</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$quantity</td></tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Unit Price</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$unitprice</td></tr></table>";
		//$objResponse->script("showFnLoading();");
		}
		//$objResponse->script("showFnLoading();");
		$objResponse->assign("LastPurchaseDiv_".$rowId, "innerHTML", $valueDisplay);
		//$objResponse->script("hideFnLoading();");
		return $objResponse;
	}

	function getStockMinimumOrderQty($stockId, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		//$objResponse->script("showFnLoading();");
		//$objResponse->alert("minimum");
		$minimumOrderQty = $purchaseOrderInventoryObj->getStockMinOrderQty($stockId);	
		
		$objResponse->assign("quantity_".$rowId, "value", $minimumOrderQty);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");	
		//$objResponse->script("hideFnLoading();");
        return $objResponse;		
	}

	function chkStockItemOrderQty($stockId, $rowId, $qty, $mode)
	{		
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);
		//$objResponse->script("showFnLoading();");
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

	
	function getitemDescription($stockid,$rowId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);
		//$objResponse->script("delay()");
		//$objResponse->script("hideFnLoading();");
		//$objResponse->script("showFnLoading();");
		$categorydetails=$purchaseOrderInventoryObj->getCategoryDetails($stockid);
		//$objResponse->alert($stockid);
		$catId=$categorydetails[0];
		$subcatId=$categorydetails[1];		
		$proddesc=$purchaseOrderInventoryObj->getPurchaseOrderDescrption($catId,$subcatId,$stock_main_id);
		$objResponse->assign("proddesc_".$rowId, "innerHTML", $proddesc);
		//$objResponse->script("hideFnLoading();");
		return $objResponse;


	}


//$xajax->registerFunction("getOtherSuppliersStockRec");
$xajax->register(XAJAX_FUNCTION,'getSupplierStockRecords',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getSupplierStockRecordsAll', array('onResponseDelay'=>'showFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'getStockBalanceQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,"getStockBalanceQty",array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnitRate', array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getOtherSuppliersStockRec',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getLastPurchaseStockRec',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockMinimumOrderQty',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getitemDescription',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'hideFunction',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'showFunction',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'calculateNetTotalAmount',array('onResponseDelay'=>'showFnLoading'));
/*$xajax->registerFunction(XAJAX_FUNCTION,"getStockBalanceQty");
$xajax->registerFunction(XAJAX_FUNCTION,"getStockUnitRate");
$xajax->registerFunction(XAJAX_FUNCTION,"getOtherSuppliersStockRec");
$xajax->registerFunction(XAJAX_FUNCTION,"getLastPurchaseStockRec");
$xajax->registerFunction(XAJAX_FUNCTION,"getStockMinimumOrderQty");
$xajax->registerFunction(XAJAX_FUNCTION,"getitemDescription");*/
//$xajax->register(XAJAX_FUNCTION,'getStockUnitRate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'getOtherSuppliersStockRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'getStockMinimumOrderQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'chkStockItemOrderQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'getLastPurchaseStockRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'getitemDescription', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getnetTotal',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnitRatePlantUnit',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'getRowUnitPrice', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getRowUnitPrice', array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnitRateTotal',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'latestRateListUnit',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION,'getStockItemRate',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>