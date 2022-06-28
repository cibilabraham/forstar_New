<?php
require_once("lib/databaseConnect.php");
require_once("DailySalesEntry_class.php");
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
	
	# Get Balance Stk
	function getBalStock($rtCounterId, $productId, $cStockNum, $subTbleRId, $mainTbleRId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailySalesEntryObj 	= new DailySalesEntry($databaseConnect);
		//$objResponse->alert("$rtCounterId,$productId,$cStockNum,$subTbleRId, $mainTbleRId");		
		list($stockNum,$orderNum) = $dailySalesEntryObj->getPrevStock($rtCounterId, $productId);
		// Find Balance Stock
		$balStock = ($stockNum+$orderNum)-$cStockNum;
		$displayBalStk = ($balStock>0)?$balStock:0;		
		$objResponse->assign("balStk_".$subTbleRId."_".$mainTbleRId, "value", $displayBalStk);	
		$objResponse->script("calcProductOrderedValue()");	
            	return $objResponse;
	}

	function getBalStkOfRtCt($rtCounterId, $subTbleRowCount, $rowId)
	{	
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailySalesEntryObj 	= new DailySalesEntry($databaseConnect);		
		$objResponse->script("findBalStk($rtCounterId,$subTbleRowCount,$rowId)");
		$objResponse->script("calcProductOrderedValue()");		
		return $objResponse;
	}

	function getOrderValue($rtCounterId, $productId, $cOrderNum, $subTbleRId, $mainTbleRId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailySalesEntryObj 	= new DailySalesEntry($databaseConnect);
		$rtCountMarginRateListObj = new RetailCounterMarginRateList($databaseConnect);
		$selRateList = $rtCountMarginRateListObj->latestRateList();
		# Find the Selected Product Value (Except Margin)
		$selProductValue = $dailySalesEntryObj->getProductValue($rtCounterId, $productId,$selRateList);
		$calTotalProductValue = $selProductValue * $cOrderNum;
		$objResponse->assign("productValue_".$subTbleRId."_".$mainTbleRId, "value", $calTotalProductValue);	
		$objResponse->script("calcProductOrderedValue()");
		return $objResponse;
	}
	
	# Chking Rt Scheme Eligible
	function chkRtSchemeEligible($rtCounterId, $mainTbleRId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailySalesEntryObj 	= new DailySalesEntry($databaseConnect);
		//$objResponse->alert("$rtCounterId,$mainTbleRId");	
		$schemeRecords		= $dailySalesEntryObj->getEligibleSchemes($rtCounterId); 
		if (sizeof($schemeRecords)>0) {
			$schemeTbl = "<table cellspacing=\"1\" bgcolor=\"#999999\" cellpadding=\"3\">";
			$schemeTbl .= "<tr bgcolor=\"#f2f2f2\" align='center'><td class=\"listing-head\" style='line-height:normal;font-size:11px;'>Scheme</td><td class=\"listing-head\" style='line-height:normal;font-size:11px;'>Valid Till</td></tr>";
			foreach ($schemeRecords as $sr) {
				$schemeId	= $sr[0];
				$schemeName	= $sr[1];
				$tillDate	= $sr[3];
				$sDate			=	explode("-", $tillDate);
				//$formatedDate	=	$sDate[2]."/".$sDate[1]."/".$sDate[0]; 2008-07-03-03/07/08
				$validTill = date("jS M y", mktime(0, 0, 0, $sDate[1], $sDate[2], $sDate[0]));
				//$selFromDate	= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[0], $Date1[2]));
				$schemeTbl .= "<tr bgcolor=\"white\"><td class=\"listing-item\" style='line-height:normal;font-size:11px;'>$schemeName</td><td class=\"listing-item\" noWrap style='line-height:normal;font-size:11px;'>$validTill</td></tr>";
			}
			$schemeTbl .= "</table>";
		} else {
			$schemeTbl = "<span class='err1'>No Scheme available</span>";
		}

		/*
		for ($i=0; $i<=$tableRowCount; $i++) {			
		       	$objResponse->addCreateOptions($selectId.$i, $data, $hidStockName.$i);			
		}
		*/
		$objResponse->assign("schemeAvailableDiv_".$mainTbleRId, "innerHTML", $schemeTbl);	
		return $objResponse;
	}

	function disChargeEligible($rtCounterId, $mainTbleRId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$dailySalesEntryObj 	= new DailySalesEntry($databaseConnect);
		$disCharge		= $dailySalesEntryObj->getEligibleDisplayCharge($rtCounterId);
		if ($disCharge!="") $eligibleDisCharge = "<span class='listing-item'>Rs.$disCharge</span>";
		else $eligibleDisCharge = "<span class='err1' style='line-height:normal;font-size:11px;'>No Dis.Charge</span>";
		$objResponse->assign("disChargeAvailDiv_".$mainTbleRId, "innerHTML", $eligibleDisCharge);	
		return $objResponse;
	}

$xajax->registerFunction("getBalStock");
$xajax->registerFunction("getBalStkOfRtCt");
$xajax->registerFunction("getOrderValue");
$xajax->registerFunction("chkRtSchemeEligible");
$xajax->registerFunction("disChargeEligible");

$xajax->ProcessRequest();
?>