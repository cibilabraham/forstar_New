<?php
require_once("lib/databaseConnect.php");
require_once("Claim_class.php");
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
	}
	
	
	function chkClaimNumberExist($claimId, $mode)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$claimObj 		= new Claim($databaseConnect);
		$chkClaimNoExist = $claimObj->checkClaimNumberExist($claimId);
		if ($chkClaimNoExist) {
			$objResponse->assign("divClaimIdExistTxt", "innerHTML", "$claimId is already in use. Please choose another one");
			$objResponse->script("disableClaimButton($mode);");
		} else  {
			$objResponse->assign("divClaimIdExistTxt", "innerHTML", "");
			$objResponse->script("enableClaimButton($mode);");
		}
		return $objResponse;
	}

	# Get Distributor wise sales orders
	function getDistSalesOrder($distributorId, $fromDate, $tillDate, $tableRowCount)
	{
	    $objResponse 	= new NxajaxResponse();
	    $databaseConnect 	= new DatabaseConnect();
	    $claimObj 		= new Claim($databaseConnect);
	    $data = $claimObj->getSalesOrderList($distributorId, $fromDate, $tillDate);
	
	    for ($i=0; $i<=$tableRowCount; $i++) {		
	    	$objResponse->addCreateOptions("selSalesOrder_".$i, $data, "hidSalesOrderId_".$i);
	    }		
	    return $objResponse;
	}

	# get Sales Ordered List
	function getSalesOrderItems($salesOrderId, $rowId, $mode)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();		
		$claimObj 	 = new Claim($databaseConnect);
		if ($mode==1) $salesOrderedItemRecs = $claimObj->filterSalesOrderRecs($salesOrderId);
		else if ($mode==2) $salesOrderedItemRecs = $claimObj->filterClaimRecs($salesOrderId);
		//$objResponse->alert("$salesOrderId, $rowId, $mode");
		$orderedList = "<table><TR><TD><table  cellspacing=\"1\" bgcolor=\"#999999\" cellpadding=\"3\" id=\"tblAddItem\"><tr bgcolor=\"#f2f2f2\" align=\"center\"><td width=\"20\"><!--INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick=\"checkAll(this.form,'selProduct_$rowId');\"--></td><td class=\"listing-head\" style=\"padding-left:5px;padding-right:5px;\">Product</td><td class=\"listing-head\" nowrap style=\"padding-left:5px;padding-right:5px;\">Rate</td><td class=\"listing-head\" style=\"padding-left:5px;padding-right:5px;\">Purchased Qty</td><td class=\"listing-head\" style=\"padding-left:5px;padding-right:5px;\">Total Amt</td><td class=\"listing-head\" style=\"padding-left:5px;padding-right:5px;\">Return Qty</td><td class=\"listing-head\" style=\"padding-left:5px;padding-right:5px;\">Reason</td></tr>";
		$totalAmount = 0;
		$m = 0;
		$claimEntryId = "";
		$defectQty  = "";
		$defectType = "";
		foreach ($salesOrderedItemRecs as $sor) {
			$m++;
			$salesOrderEntryId = $sor[0];
			$productId	= $sor[2];
			$unitRate 	= $sor[3];
			$editQuantity	= $sor[4];
			$editTotal	= $sor[5];
			$totalAmount = $totalAmount + $editTotal;
			$productName = $sor[6];
			
			// Edit mode
			$claimEntryId = $sor[7];
			$defectQty	= $sor[8];
			$defectType	= $sor[9];
			$checked = "";
			$selectedProduct = false;
			if ($claimEntryId!="") {
				$checked = "Checked";	
				$selectedProduct = true;
			}

		$orderedList .= "<tr bgcolor=\"#FFFFFF\"><td width=\"20\" height=\"25\"><input type=\"checkbox\" name='selProduct_$m"."_$rowId' id='selProduct_$m"."_$rowId' value=\"$productId\" class='chkBox' $checked onclick=\"calcRtQtyAmt();\"><input type=\"hidden\" name='hidSelProduct_$m"."_$rowId' value=\"$selectedProduct\"><input type=\"hidden\" name='salesOrderEntryId_$m"."_$rowId' value=\"$salesOrderEntryId\"><input type=\"hidden\" name='claimEntryId_$m"."_$rowId' value=\"$claimEntryId\"></td><td class=\"fieldName\" style=\"padding-left:5px;padding-right:5px;line-height:normal;\" nowrap>$productName</td> ";

		$orderedList .= "<td style=\"padding-left:5px;padding-right:5px;\"><input name='unitPrice_$m"."_$rowId' type=\"text\" id='unitPrice_$m"."_$rowId' value=\"$unitRate\" size=\"6\" style=\"text-align:right\" autoComplete=\"off\" readonly></td>";

		$orderedList .= "<td style=\"padding-left:5px;padding-right:5px;\"><input name='quantity_$m"."_$rowId' type=\"text\" id='quantity_$m"."_$rowId' size=\"6\" style=\"text-align:right\" value=\"$editQuantity\" readonly></td> ";
		$orderedList .=  "<td style=\"padding-left:5px;padding-right:5px;\"><input name='totalAmount_$m"."_$rowId' type=\"text\" id='totalAmount_$m"."_$rowId' size=\"8\" readonly style=\"text-align:right\" value=\"$editTotal\"></td>";
		$orderedList .= " <td style=\"padding-left:5px;padding-right:5px;\"><input name='defectQty_$m"."_$rowId' type=\"text\" id='defectQty_$m"."_$rowId' size=\"8\" style=\"text-align:right\" value=\"$defectQty\" onkeyup=\"calcRtQtyAmt();\" autocomplete=\"off\"></td>";
		$orderedList .= "<td style=\"padding-left:5px;padding-right:5px;\"><textarea name='defectType_$m"."_$rowId' id='defectType_$m"."_$rowId'>$defectType</textarea></td></tr>";
		}
		$orderedList .= "<tr bgcolor=\"#FFFFFF\" align=\"center\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><input type=\"hidden\" name='hidItemCount_$rowId' id='hidItemCount_$rowId' value=\"$m\">";
		$orderedList .= "<td class=\"listing-head\" align=\"right\">Total:</td>";
		$orderedList .= "<td><input name='grandTotalAmt' type=\"text\" id='grandTotalAmt' size=\"8\" style=\"text-align:right\" readonly value=\"$totalAmount\"></td><td>&nbsp;</td><td>&nbsp;</td></tr></table></TD></TR></table>";
		// Assigning current sales Order Id
		$objResponse->assign("hidSalesOrderId_".$rowId, "value", $salesOrderId);
		$objResponse->assign("salesOrderedListDiv_".$rowId, "innerHTML", $orderedList);		
		$objResponse->script("calcRtQtyAmt();");
        	return $objResponse;			
	}


$xajax->registerFunction("chkClaimNumberExist");
$xajax->registerFunction("getDistSalesOrder");
$xajax->registerFunction("getSalesOrderItems");


$xajax->ProcessRequest();
?>