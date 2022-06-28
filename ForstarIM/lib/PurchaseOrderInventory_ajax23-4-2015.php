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
		/*if ($mode==1) {
			//$supplierRateListObj		= new SupplierRateList($databaseConnect);			
			//$supplierRateListId		= $supplierstockObj->latestRateListUnit($supplierId);
			$supplierstockObj		=	new SupplierStock($databaseConnect);			
			$supplierRateListId  = $supplierstockObj->latestRateListUnit($supplierId, $stockId);
			$objResponse->assign("hidSupplierRateListId", "value", $supplierRateListId);
		}*/
		
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
		$objResponse->script("getNetTotalerr()");
		return $objResponse;

	}

	function getSupplierStockRecordsAll($selectId, $supplierId, $poItem, $supplierRateListId, $tableRowCount, $hidStockName, $mode,$hidcnt)
	{		
		$objResponse 			= new NxajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$data=$purchaseOrderInventoryObj->getStockUnitDetails($supplierId,$supplierRateListId,$plantUnitid);
		$n=count($data);		
		//$objResponse->alert($selectId."-"."sup--".$supplierId."-"."po".$poItem."-"."sr---".$supplierRateListId."-".$tableRowCount);
		list($fssaiRegNo,$serviceTaxNo,$vatNo,$cstNo,$panNo)=$purchaseOrderInventoryObj->getSupplierData($supplierId);
		/*$fssaiRegNo = $purchaseOrderInventoryObj->getFssaiRegNo($supplierId);
		$serviceTaxNo = $purchaseOrderInventoryObj->getServiceTaxNo($supplierId);
		$vatNo=$purchaseOrderInventoryObj->getVatNo($supplierId);
		$cstNo=$purchaseOrderInventoryObj->getCstNo($supplierId);
		$panNo=$purchaseOrderInventoryObj->getPanNo($supplierId);*/
		$objResponse->assign("fssaiRegnoId", "innerHTML", $fssaiRegNo);	
		$objResponse->assign("serviceTaxId", "innerHTML", $serviceTaxNo);
		$objResponse->assign("vatNoId", "innerHTML", $vatNo);	
		$objResponse->assign("cstNoId", "innerHTML", $cstNo);
		$objResponse->assign("panNoId", "innerHTML",$panNo);
		for ($i=0; $i<=$tableRowCount; $i++) {		       
			   $objResponse->addCreateOptions($selectId.$i,$data,$hidStockName.$i);			  
			   $objResponse->script("calculateTotal(document.frmPurchaseOrderInventory,'',$i)");
			   
		}
		return $objResponse;
	}
	

	function getRowUnitPrice($supplierId,$tableRowCount, $hidStockName, $mode,$stockId,$i,$quantity)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$supplierstockObj=new SupplierStock($databaseConnect);
		if (($mode==1) || ($mode==0)) {
		#Supplier Rate List			
		$supplierRateListId  = $supplierstockObj->latestRateListUnit($supplierId, $stockId);	
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);				
		$total=$unitRate*$quantity;		
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
	function getStockBalanceQty($rowId,$stockId,$supplierId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);			
		$balanceStockQty = $purchaseOrderInventoryObj->getBalanceQty($stockId,$supplierId);
		$objResponse->assign("balanceQty_".$rowId, "innerHTML", $balanceStockQty);	
		$objResponse->assign("hidSelStock_".$rowId, "value", $stockId);// Assign hid stock value		
        return $objResponse;		
	}

	function calculateNetTotalAmount($cntArrVal)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		#Supplier Rate List
		 $supplierstockObj=new SupplierStock($databaseConnect);
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);		
		if ($cntArrVal!="") 
			{					
				$cntArr = explode(",",$cntArrVal);
				if (sizeof($cntArr)>0) 
				{
				$no=sizeof($cntArr);
					for ($i=0;$i<sizeof($cntArr);$i++)
					{							
						$cntDataArr = $cntArr[$i];
						$cntData = explode(":",$cntDataArr);							
						$supplierId=$cntData[2];
						$stockId=$cntData[0];
						$quantity=$cntData[1];
						$supplierRateListId  = $supplierstockObj->latestRateListUnit($supplierId, $stockId);
						$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);
						//$objResponse->alert("val===$unitRate");
						$total=$unitRate*$quantity;
						$netTotal=$netTotal+$total;
					}
				}
			}		
		$objResponse->assign("totalQuantity", "value", $netTotal);
		return $objResponse;

}

function hideFunction()
{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$objResponse->script("hideFnLoading();");		
        return $objResponse;

}

function showFunction()
{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$objResponse->script("showFnLoading();");		
        return $objResponse;

}
	function getStockUnitRate_0ld($supplierId, $stockId, $rowId, $supplierRateListId, $mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);		
		if (($mode==1) || ($mode==0)) {
			#Supplier Rate List
			 $supplierstockObj=new SupplierStock($databaseConnect);			
			$supplierRateListId  = $supplierstockObj->latestRateListUnit($supplierId, $stockId);
		}
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);		
		//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId--ur--$unitRate---mode=$mode");		
		$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");		
        return $objResponse;
	}

	function getStockUnitRate($supplierId, $stockId, $rowId, $supplierRateListId, $mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);		
		
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);		
		//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId--ur--$unitRate---mode=$mode");		
		$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");		
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
			 $supplierstockObj=new SupplierStock($databaseConnect);
			
		}
		$netTotal=0;
		foreach ($cArr as $val){		
		$arrValues=explode(":",$val);
		$supplierId=$arrValues[2];
		$stockId=$arrValues[0];
		$quantity=$arrValues[1];
		$supplierRateListId  = $supplierstockObj->latestRateListUnit($supplierId, $stockId);
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);
		$total=$unitRate*$quantity;
		$netTotal=$netTotal+$total;
		}		
		$objResponse->assign("totalQuantity".$rowId, "value", $netTotal);
		$objResponse->script("hideFnLoading();");		
        return $objResponse;
	}

	function getStockUnitRatePlantUnit($supplierId, $stockId, $rowId, $supplierRateListId, $mode)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);
		$objResponse->script("showFnLoading();");
		//$objResponse->alert("entered");
		if ($mode==1) {
			#Supplier Rate List
			 $supplierstockObj=new SupplierStock($databaseConnect);
			$supplierRateListId  = $supplierRateListObj->latestRateList($supplierId);		
		}		
		$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $supplierRateListId);		
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
		//$objResponse->alert("Stock=$stockId, Supplier=$supplierId, PO=$poItem, Row=$rowId");
		$getSupplierStockRecs = $purchaseOrderInventoryObj->getSupplierRec($stockId, $supplierId, $poItem);
		$otherSupplier = "";		
		if (sizeof($getSupplierStockRecs)>0) {		
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
		$otherSupplier .= "<td class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Price</td></tr>";
		
		foreach($getSupplierStockRecs as $gsr) {			
		$supplierName = $gsr[4];
		$supplierNegotiatedPrice = $gsr[3];
		$otherSupplier .= "<tr><TD class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplierName</TD>";
		$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;' align='right'>$supplierNegotiatedPrice</TD></tr>";	
		}		
		
		
		$otherSupplier .= "</table>";		
		} else { // No Supplier
		$otherSupplier = "<span class='err1'>No Suppliers Found</span><input type='hidden' name='hidSupplierCount_$rowId' id='hidSupplierCount_$rowId' value='0'><input type='hidden' name='selSupplier_$k_$rowId' id='selSupplier_$k_$rowId' value='$supplierId'>";		
		}			
		$objResponse->assign("otherSupplierDiv_".$rowId, "innerHTML", $otherSupplier);		
		return $objResponse;	
	
	}


	function getLastPurchaseStockRec($stockId, $supplierId, $poItem, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);		
		list($quantity,$unitprice,$supplier) = $purchaseOrderInventoryObj->getLastPurchasePrice($stockId);		
		if ($supplier=="")
		{
			$valueDisplay="<span class='err1'>No Suppliers Found</span>";
			//$objResponse->script("showFnLoading();");
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

	
	function getitemDescription($stockid,$rowId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);		
		$categorydetails=$purchaseOrderInventoryObj->getCategoryDetails($stockid);		
		$catId=$categorydetails[0];
		$subcatId=$categorydetails[1];		
		$proddesc=$purchaseOrderInventoryObj->getPurchaseOrderDescrption($catId,$subcatId,$stockid);
		$objResponse->assign("proddesc_".$rowId, "innerHTML", $proddesc);		
		return $objResponse;


	}
	
function xaddNewStockItemRow($tblAddStockItem,$poItem,$selStockId,$minQty,$nil,$supplierRateListId,$mode,$selPlantId,$nil,$proddesc,$nil,$nil,$nil)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);	
		//$objResponse->alert("hai;");
		$objResponse->script("addNewStockItemRow($tblAddStockItem,$poItem,$selStockId,$minQty,$nil,$supplierRateListId, $mode,$selPlantId,$nil,$proddesc,$nil,$nil,$nil);");
		return $objResponse;
	}

###all functionalities in pop up
function getAllRecords($rowId,$supplierId,$stockId,$plantUnitId,$poItem,$supplierRateListId,$company,$mode)
{
	$objResponse = new NxajaxResponse();
	$databaseConnect = new DatabaseConnect();
	$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);	
	if (($mode==1) || ($mode==0)) {
			#Supplier Rate List
			 $supplierstockObj		=	new SupplierStock($databaseConnect);			
			$mainId  = $supplierstockObj->latestRateListUnit($supplierId, $stockId);
			$objResponse->assign("hidSupplierRateListId", "value", $mainId);
	}
	
	$unitRate = $purchaseOrderInventoryObj->getStockItemRate($supplierId, $stockId, $mainId);	
	$minimumOrderQty = $purchaseOrderInventoryObj->getStockMinOrderQty($stockId);	
	$balanceStockQty = $purchaseOrderInventoryObj->getBalanceQty($stockId,$supplierId);
	$getSupplierStockRecs = $purchaseOrderInventoryObj->getSupplierRec($stockId, $supplierId, $poItem);
	$otherSupplier = "";		
	if (sizeof($getSupplierStockRecs)>0) 
	{		
		$otherSupplier = "<table class='print'>";
		if ($poItem!="") 
		{
			$otherSupplier	.= "<tr><TD>&nbsp;</TD>";
			$k = 0;
			foreach($getSupplierStockRecs as $gsr)
			{			
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
		$otherSupplier .= "<tr align='center'><TD class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Supplier</TD><td class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Price</td></tr>";
		foreach($getSupplierStockRecs as $gsr) 
		{			
			$supplierName = $gsr[4];
			$supplierNegotiatedPrice = $gsr[3];
			$otherSupplier .= "<tr><TD class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplierName</TD>";
			$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;' align='right'>$supplierNegotiatedPrice</TD></tr>";
		}		
		
		$otherSupplier .= "</table>";		
		} 
		else { // No Supplier
			$otherSupplier = "<span class='err1'>No Suppliers Found</span><input type='hidden' name='hidSupplierCount_$rowId' id='hidSupplierCount_$rowId' value='0'><input type='hidden' name='selSupplier_$k_$rowId' id='selSupplier_$k_$rowId' value='$supplierId'>";		
		}
		list($quantity,$unitprice,$supplier) = $purchaseOrderInventoryObj->getLastPurchasePrice($stockId);		
		if ($supplier=="")
		{
			$valueDisplay="<span class='err1'>No Suppliers Found</span>";
			//$objResponse->script("showFnLoading();");
		}
		else {
		/*$valueDisplay="<table class='print'><tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Supplier</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplier</td></tr><tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Quantity</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$quantity</td></tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Unit Price</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$unitprice</td></tr></table>";	*/
		
		$valueDisplay="<table class='print'><tr><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Supplier</td><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Quantity</td><td class='fieldName' style='line-height:normal; style='padding-left:2px; padding-right:2px;'>Unit Price</td><tr><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplier</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$quantity</td><td class='listing-item' style='padding-left:2px; padding-right:2px;'>$unitprice</td></tr></table>";	
		}	
		$categorydetails=$purchaseOrderInventoryObj->getCategoryDetails($stockId);		
		$catId=$categorydetails[0];
		$subcatId=$categorydetails[1];		
		$proddesc=$purchaseOrderInventoryObj->getPurchaseOrderDescrption($catId,$subcatId,$stockId);
		
		$requisitionQty = $purchaseOrderInventoryObj->getRequisitionQty($stockId,$company,$plantUnitId);
		$balanceRequiredQty=$balanceStockQty-$requisitionQty;
		if($balanceRequiredQty>0)
		{
			$minRequiredQty='0';
			$requirementStatus="";
		}
		else
		{
			//$objResponse->alert($requisitionQty.'++++'.$balanceStockQty);
			$minRequiredQty=$requisitionQty-$balanceStockQty;
			if($minimumOrderQty<$minRequiredQty)
			{
				$requirementStatus="cannot complete requisition";
			}
			else
			{
				$requirementStatus="";
			}
		}
		//$objResponse->alert($balanceStockQty.'--'.$requisitionQty);
		$objResponse->assign("minimumRequiredQty_".$rowId, "value", $minRequiredQty );
		$objResponse->assign("requirementStatus_".$rowId, "innerHTML", $requirementStatus);
		$objResponse->assign("proddesc_".$rowId, "innerHTML", $proddesc);	
		$objResponse->assign("LastPurchaseDiv_".$rowId, "innerHTML", $valueDisplay);	
		$objResponse->assign("otherSupplierDiv_".$rowId, "innerHTML", $otherSupplier);	
		$objResponse->assign("balanceQty_".$rowId, "innerHTML", $balanceStockQty);	
		$objResponse->assign("hidSelStock_".$rowId, "value", $stockId);// Assign hid stock value
		$objResponse->assign("quantity_".$rowId, "value", $minimumOrderQty);
		//$objResponse->alert("Supp=$supplierId,Stock=$stockId,row=$rowId,RateList=$supplierRateListId--ur--$unitRate---mode=$mode");		
		$objResponse->assign("unitPrice_".$rowId, "value", $unitRate);
		$objResponse->script("multiplyPOItem(document.frmPurchaseOrderInventory,'')");		
        return $objResponse;
}

function getMinimumRequisitionQty($rowId,$stockId,$company,$plantUnitId,$quantity,$supplierId)
{
	$objResponse = new NxajaxResponse();
	$databaseConnect = new DatabaseConnect();
	$purchaseOrderInventoryObj 	= new PurchaseOrderInventory($databaseConnect);	
	$balanceStockQty = $purchaseOrderInventoryObj->getBalanceQty($stockId,$supplierId);
	$requisitionQty = $purchaseOrderInventoryObj->getRequisitionQty($stockId,$company,$plantUnitId);
	if($quantity=="" || $quantity=="0")
	{
		$minimumOrderQty = $purchaseOrderInventoryObj->getStockMinOrderQty($stockId);
	}
	else
	{
		$minimumOrderQty =$quantity;
	}
	$balanceRequiredQty=$balanceStockQty-$requisitionQty;
	if($balanceRequiredQty>0)
	{
		$minRequiredQty='0';
		$requirementStatus="";
	}
	else
	{
		$minRequiredQty=$requisitionQty-$balanceStockQty;
		//$objResponse->alert($minimumOrderQty.'++++'.$minRequiredQty);
		if($minimumOrderQty<$minRequiredQty)
		{
			$requirementStatus="cannot complete requisition";
		}
		else
		{
			$requirementStatus="";
		}
	}
	//$objResponse->alert($balanceStockQty.'--'.$requisitionQty);
	$objResponse->assign("balanceQty_".$rowId, "innerHTML", $balanceStockQty);	
	$objResponse->assign("hidSelStock_".$rowId, "value", $stockId);// Assign hid stock value	
	$objResponse->assign("minimumRequiredQty_".$rowId, "value", $minRequiredQty);
	$objResponse->assign("requirementStatus_".$rowId, "innerHTML", $requirementStatus);
	return $objResponse;
}




#Generate GatePass
	function getPONumber($selDate,$compId,$invUnit)
	{
		$selDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
			
		$databaseConnect 		= new DatabaseConnect();
		$purchaseOrderInventoryObj = new PurchaseOrderInventory($databaseConnect);
		$checkGateNumberSettingsExist=$purchaseOrderInventoryObj->chkValidGatePassId($selDate,$compId,$invUnit);
		if (sizeof($checkGateNumberSettingsExist)>0){
		$alpId=$checkGateNumberSettingsExist[0][0];
		$alphaCode=$purchaseOrderInventoryObj->getAlphaCode($alpId);
		$alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert($alphaCodePrefix);
		$numbergen=$checkGateNumberSettingsExist[0][0];
		//$objResponse->alert($numbergen);
		$checkExist=$purchaseOrderInventoryObj->checkGatePassDisplayExist($compId,$invUnit);
		if ($checkExist>0){
		$getFirstRecord=$purchaseOrderInventoryObj->getmaxGatePassId($compId,$invUnit);
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextGatePassId=$getFirstRecEx[1]+1;
		$validendno=$purchaseOrderInventoryObj->getValidendnoGatePassId($selDate,$compId,$invUnit);
		//$objResponse->alert($nextGatePassId);
		if ($nextGatePassId>$validendno){
		$PurchaseOrderMsg="Please set the Purchase Order Id in Settings,since it reached the end no";
		$objResponse->assign("divPOIdExistTxt","innerHTML",$PurchaseOrderMsg);
		}
		else{
		
		$disGateNo="$alphaCodePrefix$nextGatePassId";
		//$objResponse->alert($disGateNo);
		$objResponse->assign("textfield","value","$disGateNo");	
		$objResponse->assign("number_gen_id","value","$numbergen");	
		$objResponse->assign("divPOIdExistTxt","innerHTML","");
		}
		
		}
		else{
		
		$validPassNo=$purchaseOrderInventoryObj->getValidGatePassId($selDate,$compId,$invUnit);	
		$checkPassId=$purchaseOrderInventoryObj->chkValidGatePassId($selDate,$compId,$invUnit);
		$disGatePassId="$alphaCodePrefix$validPassNo";
		$objResponse->assign("textfield","value","$disGatePassId");	
		$objResponse->assign("number_gen_id","value","$numbergen");	
		$objResponse->assign("divPOIdExistTxt","innerHTML","");
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$PurchaseOrderMsg="Please set the Purchase Order Id in Settings";
		$objResponse->assign("textfield","value","");	
		$objResponse->assign("divPOIdExistTxt","innerHTML",$PurchaseOrderMsg);
		}
	
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
		$objResponse->addDropDownOptions("unitpo",$unit,$cel);
		return $objResponse;	
	}

	
	function getDeliveredUnit($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("unitid",$unit,$cel);
		return $objResponse;	
	}

$xajax->register(XAJAX_FUNCTION,'getDeliveredUnit',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));	
$xajax->register(XAJAX_FUNCTION,'getUnit',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));	
$xajax->register(XAJAX_FUNCTION,'getMinimumRequisitionQty',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getAllRecords',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getSupplierStockRecords',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getSupplierStockRecordsAll', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,"getStockBalanceQty",array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnitRate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getOtherSuppliersStockRec',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getLastPurchaseStockRec',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockMinimumOrderQty',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getitemDescription',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'hideFunction',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'showFunction',array('onResponseDelay'=>'showFnLoading'));
$xajax->register(XAJAX_FUNCTION,'calculateNetTotalAmount',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'chkStockItemOrderQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getnetTotal',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnitRatePlantUnit',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getRowUnitPrice', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'xaddNewStockItemRow', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockUnitRateTotal',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getPONumber',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();
?>