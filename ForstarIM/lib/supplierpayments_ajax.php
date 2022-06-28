<?php
require_once("lib/databaseConnect.php");
require_once("supplierpayments_class.php");
require_once("libjs/xajax_core/xajax.inc.php");
require_once("lib/config.php");

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

		// For Edit Mode
		function addChallanOptions($sSelectId, $options, $selChallans)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				$exCVal = explode(",",$selChallans);
				foreach ($options as $option=>$val) {
					if (in_array($option, $exCVal)) $cId = $option;

					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}	
  		}				
	}	

	# Get Settled Recs
	function setldChallanRecs($supplierId, $dateType, $fromDate, $toDate, $paymentType, $selChallan, $billingCompany, $mode, $selSetldDate)
	{
		$fromDate	= mysqlDateFormat($fromDate);
		$toDate		= mysqlDateFormat($toDate);
		$selSetldDate	= mysqlDateFormat($selSetldDate);

		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();	
		$supplierpaymentsObj	= new SupplierPayments($databaseConnect);

		if ($paymentType=='S') {

			$setldDateRecs = $supplierpaymentsObj->fetchAllSetldDateRecs($dateType, $fromDate, $toDate, $supplierId);
	
			$challanNos	= $supplierpaymentsObj->getChallanNos($dateType, $fromDate, $toDate, $supplierId, $selSetldDate);	
		
			$billingCompanyRecs = $supplierpaymentsObj->getBillingCompnyRecs($dateType, $fromDate, $toDate, $supplierId, $selSetldDate);	

			# Setld Date Recs
			if (sizeof($setldDateRecs)>0) $objResponse->addCreateOptions('selSettlementDate', $setldDateRecs, dateFormat($selSetldDate));
			else $objResponse->script("document.getElementById('selSettlementDate').length=0");

			# billing company Recs
			if (sizeof($billingCompanyRecs)>0) $objResponse->addCreateOptions('billingCompany', $billingCompanyRecs, $billingCompany);
			else $objResponse->script("document.getElementById('billingCompany').length=0");

			# Challan Recs
			if (sizeof($challanNos)>0 && $selChallan=="") $objResponse->addCreateOptions('selChallan', $challanNos, $cId);
			else if (sizeof($challanNos)>0 && $selChallan!="") $objResponse->addChallanOptions('selChallan', $challanNos, $selChallan);
			else $objResponse->script("document.getElementById('selChallan').length=0");	
		} else {
			$objResponse->script("clearSetldFields();");
		}		
		//$objResponse->alert("$supplierId, $dateType, $fromDate, $toDate, $paymentType, $selChallan, $billingCompany, $mode");
		return $objResponse;	
	}

	# Display other Settled recs
	function displayOtherEntry($paymentDate, $supplierId, $cId)
	{
		$paymentDate	= mysqlDateFormat($paymentDate);

		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();	
		$supplierpaymentsObj	= new SupplierPayments($databaseConnect);

		$otherEntryRecs 	= $supplierpaymentsObj->supplierOtherEntryRecs($paymentDate, $supplierId, $cId);
		$disExistList ="";
		if (sizeof($otherEntryRecs)>0) {
			$disExistList = "<table><tr><td>";
			$disExistList .= "<fieldset style='padding:10 10 10 10px;'><legend class='listing-item'>Payment made for same settlement date</legend>";
			$disExistList .= "<table cellpadding='1'  width='90%' cellspacing='1' border='0' align='center' bgcolor='#999999'>
				<tr  bgcolor='#f2f2f2'  align='center'>				
					<td nowrap class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>Payment<br>Method</td>
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Cheque/DD<br> No</td>
					<td nowrap class='listing-head' style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Issuing Bank</td>	
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Payable At</td>
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Payment<br>Type</td>
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Accounts<br>Ref No</td>	
					<td align='right' class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>Amount</td>
					<td align='right' class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>Reason</td>	
				</tr>";
		foreach ($otherEntryRecs as $spr) {			
			$chequeNo	= $spr[2];
			$amountPaid	= $spr[3];			
			$selBankName	= $spr[5];
			$sPayableAt	= $spr[6];
			$spPmtMethod	= ($spr[7]=='CH')?'CHEQUE':'DD';
			$spPmtType	= ($spr[8]=='A')?'ADVANCE':'SETTLEMENT';
			$spACEntryNo	= $spr[9];
			$spReason	= $spr[10];
$disExistList .= "<tr  bgcolor='WHITE'  >
		<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='center'>$spPmtMethod</td>
		<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='center'>$chequeNo</td>
		<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$selBankName</td>	
		<td class='listing-item' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$sPayableAt</td>
		<td class='listing-item' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='center'>$spPmtType</td>
		<td class='listing-item' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$spACEntryNo</td>
		<td class='listing-item'  align='right' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$amountPaid</td>
		<td class='listing-item'  align='right' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$spReason</td>
		</tr>";
		}
$disExistList .= "</table></fieldset></td></tr></table>";

		}
		$objResponse->assign("supplierOtherEntry","innerHTML",$disExistList);
		return $objResponse;
	}


	# Display Setld Recs
	# Format ------------------
	#	BASED ON		COMPANY	:challan nos  
	#	Wt challan date =		: nos
	#				Seacatch  	: nos
	#S	upplier date	=   			: nos
	#				Seacatch  	:  nos
	function displaySetldRecs($supplierId, $paymentType, $fromDate, $toDate, $selSetldDate)
	{
		$fromDate	= mysqlDateFormat($fromDate);
		$toDate		= mysqlDateFormat($toDate);
		$selSetldDate	= mysqlDateFormat($selSetldDate);

		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();	
		$supplierpaymentsObj	= new SupplierPayments($databaseConnect);

		if ($supplierId && $fromDate && $toDate && $selSetldDate) {
			# get Challan Recs			
			$getWtChallanRecs	= $supplierpaymentsObj->getWtChallanRecs($supplierId, $fromDate, $toDate, $selSetldDate);
		}	
		$dispalyList = "";
		if (sizeof($getWtChallanRecs)>0) {
			
			$dispalyList = "<table cellpadding='1'  width='90%' cellspacing='1' border='0' align='center' bgcolor='#999999'>
				<tr  bgcolor='#f2f2f2'  align='center'>				
					<td nowrap class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>Based on</td>
					<td nowrap class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' colspan='2'>Payment from Company / Challan Nos</td>			
				</tr>";
			$basedOn = "";
			$paymentBy = "";
			foreach ($getWtChallanRecs as $gwcr) 
			{
				$paymentBy	= $gwcr[0];
				$basedOn = ($paymentBy=='E')?"Wt Challan Date":"Supplier Date";

				# get Billing Company Recs
				$getCompanyRecs = $supplierpaymentsObj->getBillingCompanyRecs($supplierId, $fromDate, $toDate, $selSetldDate, $paymentBy);
				
			$dispalyList .= "<tr  bgcolor='WHITE'  >
						<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='left'>$basedOn</td>
						<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='center' colspan='2'>
						<table cellpadding='0' cellspacing='0'>	";
						foreach ($getCompanyRecs as $cr) {
							$billingCompanyId = $cr[0];
							$billingCompanyName = $cr[1];
							$getChllanNos = $supplierpaymentsObj->getChllanNos($supplierId, $fromDate, $toDate, $selSetldDate, $paymentBy, $billingCompanyId);
			$dispalyList .=		"<tr><td class='listing-item' nowrap style='padding-left:2px; padding-right:5px; font-size:11px;line-height:normal;' align='left'>$billingCompanyName :</td><td>";					
					$dispalyList	.= "<table cellpadding='0' cellspacing='0'><tr>";
						$numLine = 10;
						if (sizeof($getChllanNos)>0) {
							$nextRec	=	0;				
							$selChallanNo = "";
							foreach ($getChllanNos as $zr) {
								$selChallanNo = $zr[1];
								$nextRec++;
						$dispalyList	.= "<td class='listing-item' nowrap style='padding-left:2px; font-size:10px;line-height:normal;' align='left'>";
									if ($nextRec>1) {
						$dispalyList	.=  ",";	
									}
						$dispalyList	.= "$selChallanNo</td>";
									if($nextRec%$numLine == 0) { 
						$dispalyList	.= "</tr><tr>";
									}	
									}
						} 
						$dispalyList	.= "</tr></table>";
						// Challan Loop ends here
			$dispalyList .= " </td></tr>";		
						} // Company Loop Ends here
			$dispalyList .= "</table></td>	</tr>";	
			} // Based On Loop ends here
			$dispalyList .= "</table>";
		}
		if (sizeof($getWtChallanRecs)>0) $objResponse->script("showSetldRow('Y');");
		else $objResponse->script("showSetldRow('');");
		$objResponse->assign("setldListRow","innerHTML",$dispalyList);
		return $objResponse;
	}

	function getSetldDates($supplierId, $fromDate, $toDate, $selSetldDate)
	{
		$fromDate	= mysqlDateFormat($fromDate);
		$toDate		= mysqlDateFormat($toDate);

		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();	
		$supplierpaymentsObj	= new SupplierPayments($databaseConnect);
		//$objResponse->alert($selSetldDate);
		if ($supplierId && $fromDate && $toDate) 
			$setldDateRecs = $supplierpaymentsObj->getSetldDateRecs($supplierId, $fromDate, $toDate);

		# Setld Date Recs
		if (sizeof($setldDateRecs)>0) $objResponse->addCreateOptions('selSettlementDate', $setldDateRecs, $selSetldDate);
		else $objResponse->script("document.getElementById('selSettlementDate').length=0");

		return $objResponse;
	}


	# Display other Settled recs
	function displayAdvanceEntry($paymentDate, $supplierId, $cId, $fromDate, $toDate, $selSetldDate)
	{
		$paymentDate	= mysqlDateFormat($paymentDate);
		$fromDate	= mysqlDateFormat($fromDate);
		$toDate		= mysqlDateFormat($toDate);
		$selSetldDate	= mysqlDateFormat($selSetldDate);

		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();	
		$supplierpaymentsObj	= new SupplierPayments($databaseConnect);

		# get all advance entry recs
		$advanceEntryRecs 	= $supplierpaymentsObj->supplierAdvanceEntryRecs($paymentDate, $supplierId, $cId);
		$disExistList ="";
		if (sizeof($advanceEntryRecs)>0) {
			$disExistList = "<table><tr><td>";
			$disExistList .= "<fieldset style='padding:10 10 10 10px;'><legend class='listing-item'>Advance payment made</legend>";
			$disExistList .= "<table cellpadding='1'  width='90%' cellspacing='1' border='0' align='center' bgcolor='#999999'>
				<tr><td colspan='11' bgcolor='white' height='30' style='padding-left:10px;'><input type='button' value=' Link to selected Settlement date ' class='button'  name='cmdLinkSetldDate' onClick=\"return cfmLinkSetld(this.form,'advEntryId_','".sizeof($advanceEntryRecs)."', '$paymentDate', '$supplierId', '$fromDate', '$toDate', '$selSetldDate');\" style='width:230px;'></td></tr>
				<tr  bgcolor='#f2f2f2'  align='center'>
					<td width='20' >
						<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick=\"checkAll(this.form,'advEntryId_'); showSetldAmtType();\" class='chkBox'>
					</td>		
					<td nowrap class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>Payment<br>Method</td>
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Cheque/DD<br> No</td>
					<td nowrap class='listing-head' style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Issuing Bank</td>	
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Payable At</td>
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Payment<br>Type</td>
					<td class='listing-head' nowrap style='padding-left:5px; padding-right:5px;  font-size:11px;line-height:normal;'>Accounts<br>Ref No</td>	
					<td align='right' class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>Amount</td>
					<td align='right' class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>Reason</td>
					<td align='right' class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal; display:none;' id='setldAmtTypeHead'></td>	
					<td align='right' class='listing-head' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal; display:none;' id='setldAmtHead'></td>	
				</tr>";
		$i = 0;
		foreach ($advanceEntryRecs as $spr) {			
			$i++;
			$advanceEntryId = $spr[0];
			$chequeNo	= $spr[2];
			$amountPaid	= $spr[3];			
			$selBankName	= $spr[5];
			$sPayableAt	= $spr[6];
			$spPmtMethod	= ($spr[7]=='CH')?'CHEQUE':'DD';
			$spPmtType	= ($spr[8]=='A')?'ADVANCE':'SETTLEMENT';
			$spACEntryNo	= $spr[9];
			$spReason	= $spr[10];
$disExistList .= "<tr  bgcolor='WHITE'  >
		<td width='20' height='25' class='listing-item'>
			<input type='checkbox' name='advEntryId_$i' id='advEntryId_$i' value='$advanceEntryId' class='chkBox' onclick=\"showSingleSetldAmtType('".$i."')\">
		</td>	
		<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='center'>$spPmtMethod</td>
		<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='center'>$chequeNo</td>
		<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$selBankName</td>	
		<td class='listing-item' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$sPayableAt</td>
		<td class='listing-item' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;' align='center'>$spPmtType</td>
		<td class='listing-item' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$spACEntryNo</td>
		<td class='listing-item'  align='right' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$amountPaid
		<input type='hidden' name='amtPaid_$i' id='amtPaid_$i' size='4' value='$amountPaid' style='text-align:right'/>
		</td>
		<td class='listing-item'  align='right' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal;'>$spReason</td>
		<td class='listing-item'  align='right' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal; display:none;' id='setldAmtCol_$i' >
			<select name='stldAmtType_$i' id='stldAmtType_$i' onchange=\"showAmtCol('".$i."')\">
				<option value=''>--select--</option>
				<option value='FA'>Full Amount</option>
				<option value='PA'>Part Amount</option>
			</select>
		</td>
		<td class='listing-item'  align='right' style='padding-left:5px; padding-right:5px; font-size:11px;line-height:normal; display:none;' id='partAmtCol_$i'>
			<input type='text' name='partAmt_$i' id='partAmt_$i' size='4' style='text-align:right'/>
		</td>
		</tr>";
		}
$disExistList .= "</table><input type='hidden' name='advAmtRowCount' id='advAmtRowCount' value='".sizeof($advanceEntryRecs)."'></fieldset></td></tr></table>";

		}
		$objResponse->assign("supplierAdvanceEntry","innerHTML",$disExistList);
		return $objResponse;
	}

	#Update Advance Setled recs
	function updateAdvanceRec($paymentDate, $supplierId, $fromDate, $toDate, $selSetldDate, $advanceEntryId, $stldAmtType, $amtPaid, $partAmt)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();	
		$supplierpaymentsObj	= new SupplierPayments($databaseConnect);
					
		if ($stldAmtType=='PA') {
			# Get Old Rec
			list($chequeNo, $bankName, $payableAt, $paymentMethod, $paymentReason, $accountEntryNo, $userId)=  $supplierpaymentsObj->getSPRec($advanceEntryId);
			# Calc Balance Advance Amt
			$balanceAdvanceAmt = $amtPaid-$partAmt;
			# Insert New Settlement
			$supplierpaymentsObj->addPartSetledRec($supplierId, $chequeNo, $partAmt, $paymentMode, $paymentDate, $bankName, $userId, $payableAt, $paymentMethod, 'S', $paymentReason, $accountEntryNo, $dateType, $fromDate, $toDate, $selChallan, $billingCompany, $selSetldDate, $advanceEntryId);
			# Update balance Amt
			$updateBalanceAmt = $supplierpaymentsObj->updateBalanceAdvanceAmt($advanceEntryId, $balanceAdvanceAmt);
		} else if ($stldAmtType=='FA') {
			# Convert to Settlement
			$updateAdvanceFullAmt = $supplierpaymentsObj->updateAdvanceFullAmt($advanceEntryId, $paymentDate, $selSetldDate);
		}		
		
		// Reload xjax
		$objResponse->script("reloadAdvanceList('".dateFormat($paymentDate)."','$supplierId','$cId','".dateFormat($fromDate)."','".dateFormat($toDate)."','".dateFormat($selSetldDate)."');");		
		return $objResponse;
	}

//
//$xajax->registerFunction("setldChallanRecs");
$xajax->registerFunction("displayOtherEntry");
$xajax->registerFunction("displaySetldRecs");
$xajax->registerFunction("getSetldDates");
$xajax->registerFunction("displayAdvanceEntry");
$xajax->registerFunction("updateAdvanceRec");


//$xajax->register(XAJAX_FUNCTION, 'setldChallanRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'displayOtherEntry', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'displaySetldRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSetldDates', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'displayAdvanceEntry', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>