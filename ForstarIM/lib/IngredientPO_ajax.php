<?php
//require_once("lib/databaseConnect.php");
//require_once("IngredientPO_class.php");
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
	

	# get Ing rate
	function getIngRate($supplierId,$ingId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		//$ingredientRateListObj   	= new IngredientRateList($databaseConnect);
	    $ingredientPurchaseorderObj	= new IngredientPurchaseOrder($databaseConnect);
		//$selRateListId = $ingredientRateListObj->latestRateList();
		list($supIngId,$unitPrice) = $ingredientPurchaseorderObj->findIngredientRate($supplierId,$ingId);
		$balanceQty = $ingredientPurchaseorderObj->getBalanceQty($supplierId,$ingId);
		$otherSuppliers= $ingredientPurchaseorderObj->OtherSuppliers($supplierId,$ingId);
		$lastPO= $ingredientPurchaseorderObj->getLastPurchaseOrder($ingId);
		$objResponse->assign("balanceQty_".$rowId, "innerHTML", $balanceQty);
		$objResponse->assign("unitPrice_".$rowId, "value", $unitPrice);	
		$objResponse->assign("hidSelIng_".$rowId, "value", $ingId);	 // Assign hid Ing value
		$objResponse->assign("otherSupplierDiv_".$rowId, "innerHTML", $otherSuppliers);
		$objResponse->assign("LastPurchaseDiv_".$rowId, "innerHTML", $lastPO);
		$objResponse->assign("hidSupplierIng_".$rowId, "value", $supIngId);
		$objResponse->script("multiplyIngPOItem('')");	
		return $objResponse;
	}

	# get Supplier Records
	function supplierIngRecords($supplierId, $tableRowCount, $mode)
	{		
		$objResponse 			= new NxajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$ingredientPurchaseorderObj 	= new IngredientPurchaseOrder($databaseConnect);		
		$data = $ingredientPurchaseorderObj->fetchSupplierIngredientRecords($supplierId);		
				
		$ingIdArr = array();
		$ingNameArr = array();
		$ic = 0;
		foreach ($data as $ingredientId=>$ingName) {
			$ingIdArr[$ic] = $ingredientId;
			$ingNameArr[$ic] = $ingName;
			$ic++;
		}
		$ingIdArr = implode(",",$ingIdArr);
		$ingNameArr = implode(",",$ingNameArr);
		$objResponse->script("suppIngArr = new Array();");
		$objResponse->script("fillIngDropDown('$ingIdArr', '$ingNameArr', '$ic');");	
		//if ($mode==1) 
		$objResponse->script("fillListedDropDown('$tableRowCount')");
		return $objResponse;
	}



	# Get Ing Balance Qty
	/*
	function getIngBalanceQty($ingId, $rowId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$ingredientPurchaseorderObj	= new IngredientPurchaseOrder($databaseConnect);
		$balanceQty = $ingredientPurchaseorderObj->getBalanceQty($ingId);
		$objResponse->assign("balanceQty_".$rowId, "innerHTML", $balanceQty);		
            	return $objResponse;
	}
	*/
	# PO Number Exist
	function chkIngPONumberExist($poId, $mode)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$ingredientPurchaseorderObj = new IngredientPurchaseOrder($databaseConnect);
		$chkPONoExist = $ingredientPurchaseorderObj->checkIngPONumberExist($poId);
		if ($chkPONoExist) {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "$poId is already in use. Please choose another one");
			$objResponse->script("disableIngPOButton($mode);");
		} else  {
			$objResponse->assign("divPOIdExistTxt", "innerHTML", "");
			$objResponse->script("enableIngPOButton($mode);");
		}
		return $objResponse;
	}




	function getQuantitiesOfStock($ingId, $rowId, $suppId)
	{
		$objResponse = new NxajaxResponse();
		//$objResponse->assign("hidStkSelId_$rowId", "value", $ingId);		
		$databaseConnect = new DatabaseConnect();	
		$ingredientPurchaseorderObj = new IngredientPurchaseOrder($databaseConnect);
		$ingredientRateListObj = new IngredientRateList($databaseConnect);
		
		// $ingRateListId = $ingredientRateListObj->latestRateList();
		//  find balace quantity 
		$ingRateListId = $ingredientRateListObj->latestRateList();
		$bq =number_format($ingredientPurchaseorderObj->getBalanceQty($ingId), 0,"","");
		$objResponse->assign("bqty_$rowId", "innerHTML", $bq);
	//$balanceStockQty = $ingredientPurchaseorderObj->getBalanceQty($selIngredientId);
		//  find unit price 
		/*
		$rc = $sc->findIngredientRate($ingId,$ingRateListId);
		if( sizeof($rc) > 0 ) $unitPrice = number_format($rc[4],2,".","");
		else $unitPrice = number_format(0,0,".","");
		*/
		$unitPrice = $ingredientPurchaseorderObj->findIngredientRate($ingId, $ingRateListId);
		$objResponse->assign("unitPrice_".$rowId, "value", $unitPrice);	
		
		//$getSupplierIngRecs = $ingredientPurchaseorderObj->getSupplierIngRecs($selIngredientId, $selSupplierId, $prodPlanItem);
		// find other supplier records
		//$suppStkrecs = $ingredientPurchaseorderObj->getSupplierIngRecs($ingId,$suppId,1);
		$getSupplierIngRecs = $ingredientPurchaseorderObj->getSupplierIngRecs($ingId,$suppId,1);
	
			if (sizeof($getSupplierIngRecs)>0) 
			{
					$otherSupplier = "<table class='print'>";
					$otherSupplier	.= "<tr><TD>&nbsp;</TD>";
				
					$k = 0;
					foreach($getSupplierIngRecs as $gsr) 
					{
						$k++;
						$supplierId = $gsr[1];
						$supplierName = $gsr[4];
						//$supplierNegotiatedPrice = $gsr[3];
						$supplierChkd = "";
						if ($p["selSupplier_".$k."_".$rowId]!="")  $supplierChkd = "checked";
						$prx = "_".$k."_".$rowId;

						$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;'>
						<input type='hidden' name='negoPrice$prx'  id='negoPrice$prx' value='".$supplierNegotiatedPrice."'><input type='checkbox' class='chkBox' name='selSupplier$prx'  id='selSupplier$prx' value='$supplierId' onclick=\"uncheckSelected('selSupplier$prx',$rowId);return multiplyIngPO(document.frmPurchaseSupplier, 'Y');\"  $supplierChkd ></TD>
";
					}
				$otherSupplier .= "<input type='hidden' name='hidSupplierCount_$rowId' id='hidSupplierCount_$rowId' value='$k'></tr>";
			
				$otherSupplier .= "<tr align='center'><TD class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Supplier</TD>";
				foreach($getSupplierIngRecs as $gsr) {
					$supplierName = $gsr[3];
					//$supplierNegotiatedPrice = $gsr[3];
					$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;'>$supplierName</TD>";
				}
				/*$otherSupplier .= "</tr><tr><td class='fieldName' style='line-height:normal; padding-left:2px; padding-right:2px;'>Price</td>";
				foreach($getSupplierIngRecs as $gsr) {
					$supplierName = $gsr[4];
					$supplierNegotiatedPrice = $gsr[3];
					$otherSupplier .= "<TD class='listing-item' style='padding-left:2px; padding-right:2px;' align='right'>$supplierNegotiatedPrice</TD>
	
					";	
				}
				*/
				$otherSupplier .= "</tr></table>";
			}
			else{
				$otherSupplier = "<table height='100%' bgcolor='white' align='center'><tr><td><span class='err1' style='line-height:normal; font-size:9px;'>No Suppliers Found</span><input type='hidden' name='hidSupplierCount_$rowId' id='hidSupplierCount_$rowId' value='0'></td></tr></table>";
			}			
	
		$objResponse->assign("OtherSuppList_$rowId", "innerHTML", $otherSupplier);
		$objResponse->script("multiplyIngPO($mode);");		
		return $objResponse;	
	}

	function checkPOIdExist($poId, $rowId, $mode) 
	{
		$objResponse = new NxajaxResponse();
	   	$databaseConnect 		= new DatabaseConnect();		
		$ingredientPurchaseorderObj 	= new IngredientPurchaseOrder($databaseConnect);
		
		if ($poId!='') $chkPONumExist = $ingredientPurchaseorderObj->checkIngPONumberExist($poId);
		else $chkPONumExist = "" ;
		
		if ($chkPONumExist) {
			$objResponse->assign("isPoExist_$rowId", "value", "Y");	
			$objResponse->assign("msgPOIdExist_$rowId", "innerHTML", "Purchase Order ID $poId already in use.");		
			$objResponse->script("chkUpdateBtnField();");
		} else {
			$objResponse->assign("isPoExist_$rowId", "value", "");		
			$objResponse->assign("msgPOIdExist_$rowId", "innerHTML", "");	
			$objResponse->script("chkUpdateBtnField();");
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
		$objResponse->addDropDownOptions("unit",$unit,$cel);
		return $objResponse;	
}

$xajax->register(XAJAX_FUNCTION,'getUnit',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("getIngRate");
$xajax->register(XAJAX_FUNCTION,'getIngRate',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->registerFunction("getIngBalanceQty");
$xajax->registerFunction("chkIngPONumberExist");
//$xajax->registerFunction("supplierIngRecords");
$xajax->register(XAJAX_FUNCTION,'supplierIngRecords',array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->registerFunction("getQuantitiesOfStock");
$xajax->registerFunction("checkPOIdExist");

$xajax->ProcessRequest();
?>