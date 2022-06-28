<?php
class ChangesUpdateMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Changes Update Master
	*****************************************************************/
	var $databaseConnect;
	var $salesOrderObj;
	var $taxMasterObj;
	var $marginStructureObj;
	var $distMarginStructureObj;
	var $distMarginRateListObj;
	var $manageRateListObj;

	//Constructor, which will create a db instance for this class
	function ChangesUpdateMaster(&$databaseConnect, &$salesOrderObj, &$taxMasterObj, &$marginStructureObj, &$distMarginStructureObj, &$distMarginRateListObj, &$manageRateListObj)
	{
        	$this->databaseConnect 		= &$databaseConnect;
		$this->salesOrderObj 		= &$salesOrderObj;
		$this->taxMasterObj		= &$taxMasterObj;
		$this->marginStructureObj 	= &$marginStructureObj;
		$this->distMarginStructureObj 	= &$distMarginStructureObj;
		$this->distMarginRateListObj 	= &$distMarginRateListObj;
		$this->manageRateListObj	= &$manageRateListObj;	
	}

	# --- Changes Updation through Dist Mgn Structure Starts here --
	# Get Not Confirmed Recs
	function getNotConfirmedSORecs($distMarginRateListId, $selDistributor, $selStateId)
	{
		$qry = " select a.id, a.distributor_id, a.state_id, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.discount, a.discount_percent, a.invoice_date, a.city_id, a.transport_charge_active, a.transport_charge, a.billing_type from t_salesorder a where a.dist_mgn_ratelist_id='$distMarginRateListId' and (a.complete_status<>'C' or a.complete_status is null) and a.distributor_id='$selDistributor' and a.state_id='$selStateId'  ";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get SO Entry Recs
	function getNCSOEntryRecs($soId)
	{
		$qry = " select id, product_id, quantity, free_pkts from t_salesorder_entry  where salesorder_id='$soId' ";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# Update SO Dist Rec
	function updateDistMgnInSORec($distMarginRateListId, $selDistributor, $selStateId, $selProduct, $avgMargin)
	{
		# get SO Recs
		$getNotConfirmedSORecs = $this->getNotConfirmedSORecs($distMarginRateListId, $selDistributor, $selStateId);
		
		if (sizeof($getNotConfirmedSORecs)>0) {	
			$i=0;		
			foreach ($getNotConfirmedSORecs as $gncr) {
				$i++;
				$soId		= $gncr[0];
				$distributorId	= $gncr[1];
				$stateId	= $gncr[2];
				$productPriceRateListId = $gncr[3];

				$discount		= $gncr[5];
				$discountPercent	= $gncr[6];
				$invoiceDate		= $gncr[7];
				$selCityId		= $gncr[8];
				$transportActive	= $gncr[9];
				$transportCharge 	= 0;				
				if ($transportActive=='Y') $transportCharge = $gncr[10];
				$billingType = $gncr[11];
				$exportEnabled = ($billingType=="E")?"Y":"N";

				# Edu Cess
				list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
				# Sec Edu Cess
				list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
				# Excise Duty
				$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);

				# SO Entry Recs
				$soEntryRecs = $this->getNCSOEntryRecs($soId);
				$grandTotalAmount = 0;
				$taxArr	= array();
				$roundVal = "";
				$calcTotalSOAmt = "";
				$gTotalAmount = 0;
				$totalQty     = 0;

				$totExAmt 	= 0;
				$totEduCessAmt	= 0;
				$totSecEduCess	= 0;
				$grTotCentralTaxAmt = 0;

				foreach ($soEntryRecs as $ser) {
					$soEntryId 	= $ser[0];
					$productId	= $ser[1];
					$quantity	= $ser[2];
					$freePkts	= $ser[3];
					
					$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
					
					/*
					if ($selProduct==$productId) $distAvgMargin = $avgMargin; 
					else*/ 
					list($distAvgMargin,$distMgnStateEntryId,$distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
					
					# Find Tax Percent
					$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
					
					# Tax Rate
					$taxRate = ($taxPercent)/100; 
		
					# Get the Tax Type (ie. VAT/CST)
					list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
					if ($billingForm=='ZP') $taxPercent = $taxRate = 0;
					
					if ($taxType=='CST') {
						# CST PERCENT From TAX MASTER
						$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
						$cstRate = ($cstPercent/100);						
						$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
						$distAvgMargin = number_format($calcBasicMgn,4,'.','');
						$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');
						$actualCostToDist = $avgMgnCost;
					} else {
						$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));							
						$calcCostToDist = $avgMgnCost;
						$actualCostToDist = number_format($calcCostToDist,4,'.','');
					}
					if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
					else 	$costToDist = 0;
					
					$basicRate	= $costToDist;
					$calcUnitPrice 	= ($basicRate*$quantity)/($quantity+$freePkts);

					# Total Qty
					$totalQty 	= $quantity+$freePkts;

					# Calculation
					$unitPrice =  number_format($calcUnitPrice,4,'.','');
					$calcTotalAmount = $unitPrice * $totalQty;					
					$grandTotalAmount += $calcTotalAmount;

					// Excise Duty 
					list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
					$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
					if ($exciseDutyActive) {
						list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
						if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
					}

					if ($calcTotalAmount!=0) { //&& $taxPercent!=0
						// calculating Discount (Basic Total-Discount)
						$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
						$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;
						

						// Excise Duty Calculation
						$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
						$calcTotCExDuty = 0;	
						if ($exciseDutyAmt>0) {
							$calcTotCExDuty += $exciseDutyAmt;							
							$totExAmt += $exciseDutyAmt;
							
							if ($eduCess!=0) {
								$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
								$calcTotCExDuty += $eduCessAmt;
								$totEduCessAmt += $eduCessAmt;
							}
		
							if ($secEduCess!=0) {
								$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
								$calcTotCExDuty += $secEduCessAmt;
								$totSecEduCess += $secEduCessAmt;
							}
		
							$cTotalAmount += $calcTotCExDuty;
						}
						$grTotCentralTaxAmt += $calcTotCExDuty;

						// After Discount calc Grand Total Amt
						$gTotalAmount += $cTotalAmount;

						$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;		
						$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
						if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
					}
					
					# Update SO Entry Recs
					$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
					
				}  # Entry Loops Ends here
				$selTaxArr	= array();
				$totalTaxAmt = 0;
				$selTax = "";
				if (sizeof($taxArr)>0) {
					$j = 0;
					foreach ($taxArr as $taxPercent=>$taxAmt) {
						$totalTaxAmt += $taxAmt;
						$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
						$selTaxArr[$j] = $arrVal;				
						$j++;
					}
					$selTax 	= implode(",",$selTaxArr);
				}	
				# Calc Total SO Amt			
				$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;
				//Round off Calculation
				$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));

				$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');
					
				# Update main Table
				$updateSalesOrder = $this->updateSalesOrder($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
			} // Main Loop Ends Here
		} // NC Condition Ends here
	} // Update Changes


	#Update sales Order Items
	function updateSalesOrderentries($salesOrderEntryId, $unitPrice, $totalAmt, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt)
	{
		$qry = "update t_salesorder_entry set rate='$unitPrice', total_amount='$totalAmt', dist_mgn_state_id='$distMgnStateEntryId', basic_rate='$basicRate', tax_percent='$taxPercent', ex_duty_percent='$exciseDutyPercent', ex_duty_amt='$exciseDutyAmt', ex_duty_id='$edEntryId', edu_cess_amt='$eduCessAmt', sec_edu_cess_amt='$secEduCessAmt', tax_amt='$itemTaxAmt' where id='$salesOrderEntryId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;	
	}

	# Update  Sales Order
	function updateSalesOrder($salesOrderId, $totalAmount, $calcTaxAmt, $grandTotalAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt)
	{
		$qry = "update t_salesorder set total_amt='$totalAmount', tax_amt='$calcTaxAmt', grand_total_amt='$grandTotalAmt', tax_applied='$selTax', round_value='$roundVal', discount_amt='$discountAmt', ex_duty_active='$exciseDutyActive', edu_cess_percent='$eduCess', edu_cess_rl_id='$eduCessRLId', sec_edu_cess_percent='$secEduCess', sec_edu_cess_rl_id='$secEduCessRLId', tot_ex_duty_amt='$totExAmt', tot_edu_cess_amt='$totEduCessAmt', tot_sec_edu_cess_amt='$totSecEduCess', grand_tot_central_excise_amt='$grTotCentralTaxAmt' where id='$salesOrderId'";	
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	# ------------------  Dist Mgn Structure Update Ends Here ----------------
	
	# -------------------------- Product Price Update Starts here ----
	function updateProductPriceInSO($productMRPRateList, $selProduct, $selMRP)
	{
		# get SO Recs
		$getNotConfirmedSORecs = $this->getNCSORecs($productMRPRateList, $selProduct);	

		if (sizeof($getNotConfirmedSORecs)>0) {	
			$i=0;		
			foreach ($getNotConfirmedSORecs as $gncr) {
				$i++;
				$soId		= $gncr[0];
				$distributorId	= $gncr[1];
				$stateId	= $gncr[2];
				$productPriceRateListId = $gncr[3];
				$distMarginRateListId = $gncr[4];

				$discount		= $gncr[5];
				$discountPercent	= $gncr[6];
				$invoiceDate		= $gncr[7];
				$selCityId		= $gncr[8];
			
				$transportActive	= $gncr[9];
				$transportCharge 	= 0;				
				if ($transportActive=='Y') $transportCharge = $gncr[10];
				$billingType = $gncr[11];
				$exportEnabled = ($billingType=="E")?"Y":"N";

				# Edu Cess
				list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
				# Sec Edu Cess
				list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
				# Excise Duty
				$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);

				# SO Entry Recs
				$soEntryRecs = $this->getNCSOEntryRecs($soId);
				$grandTotalAmount = 0;
				$taxArr	= array();
				$roundVal = "";
				$calcTotalSOAmt = "";
				$gTotalAmount = 0;
				$totalQty     = 0;

				$totExAmt 	= 0;
				$totEduCessAmt	= 0;
				$totSecEduCess	= 0;
				$grTotCentralTaxAmt = 0;
				foreach ($soEntryRecs as $ser) {
					$soEntryId 	= $ser[0];
					$productId	= $ser[1];
					$quantity	= $ser[2];
					$freePkts	= $ser[3];					
					
					$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);

					list($distAvgMargin,$distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
					
					# Find Tax Percent
					$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
					
					# Tax Rate
					$taxRate = ($taxPercent)/100; 
		
					# Get the Tax Type (ie. VAT/CST)
					list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
					if ($billingForm=='ZP') $taxPercent = $taxRate = 0;
					
					if ($taxType=='CST') {
						# CST PERCENT From TAX MASTER
						$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
						$cstRate = ($cstPercent/100);
						//$calcBasicMgn = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;
						$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
						$distAvgMargin = number_format($calcBasicMgn,4,'.','');
						$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');				
						$actualCostToDist = $avgMgnCost;
					} else {
						$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));	
						//$calcCostToDist = $avgMgnCost/(1+$taxRate);
						$calcCostToDist = $avgMgnCost;
						$actualCostToDist = number_format($calcCostToDist,4,'.','');
					}
					if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
					else 	$costToDist = 0;

					$basicRate	= $costToDist;
					$calcUnitPrice 	= ($basicRate*$quantity)/($quantity+$freePkts);

					# Total Qty
					$totalQty 	= $quantity+$freePkts;

					# Calculation
					$unitPrice =  number_format($calcUnitPrice,4,'.','');
					$calcTotalAmount = $unitPrice * $totalQty;					
					$grandTotalAmount += $calcTotalAmount;

					// Excise Duty 
					list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
					$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
					if ($exciseDutyActive) {
						list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
						if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
					}

					if ($calcTotalAmount!=0 ) { //&& $taxPercent!=0
						// calculating Discount (Basic Total-Discount)
						$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
						$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;

						// Excise Duty Calculation
						$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
						$calcTotCExDuty = 0;	
						if ($exciseDutyAmt>0) {
							$calcTotCExDuty += $exciseDutyAmt;							
							$totExAmt += $exciseDutyAmt;
							
							if ($eduCess!=0) {
								$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
								$calcTotCExDuty += $eduCessAmt;
								$totEduCessAmt += $eduCessAmt;
							}
		
							if ($secEduCess!=0) {
								$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
								$calcTotCExDuty += $secEduCessAmt;
								$totSecEduCess += $secEduCessAmt;
							}
		
							$cTotalAmount += $calcTotCExDuty;
						}
						$grTotCentralTaxAmt += $calcTotCExDuty;
						

						// After Discount calc Grand Total Amt
						$gTotalAmount += $cTotalAmount;

						$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;
						$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
						if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
					}
					
					# Update SO Entry Recs
					$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
					
				}  # Entry Loops Ends here
				
				$selTaxArr	= array();
				$totalTaxAmt = 0;
				$selTax = "";
				if (sizeof($taxArr)>0) {
					$j = 0;
					foreach ($taxArr as $taxPercent=>$taxAmt) {
						$totalTaxAmt += $taxAmt;
						$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
						$selTaxArr[$j] = $arrVal;				
						$j++;
					}
					$selTax 	= implode(",",$selTaxArr);
				}	
			
				#Calc Total SO Amt
				$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;
				//Round off Calculation
				$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));
				# Total Discount Amt
				$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');
	
				# Update main Table
				$updateSalesOrder = $this->updateSalesOrder($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
			} // Main Loop Ends Here
		} // NC Condition Ends here
	}

	# Get Not Confirmed Recs
	function getNCSORecs($productMRPRateList, $selProduct)
	{
		$qry = " select a.id, a.distributor_id, a.state_id, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.discount, a.discount_percent, a.invoice_date, a.city_id, a.transport_charge_active, a.transport_charge, a.billing_type from t_salesorder a, t_salesorder_entry b where a.id=b.salesorder_id and a.rate_list_id='$productMRPRateList' and (a.complete_status<>'C' or a.complete_status is null) and b.product_id='$selProduct' group by a.id ";		
		//echo "$qry<br/>";		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# -------------------------- Product Price Update Ends here ----

	# -------------------------- State Wise Vat Update Starts here ----
	function updateSOVATRec($stateId)
	{
		# get SO Recs
		$getNCStateWiseSORecs = $this->getNCStateWiseSORecs($stateId);	

		if (sizeof($getNCStateWiseSORecs)>0) {	
			$i=0;		
			foreach ($getNCStateWiseSORecs as $gncr) {
				$i++;
				$soId		= $gncr[0];
				$distributorId	= $gncr[1];
				$stateId	= $gncr[2];
				$productPriceRateListId = $gncr[3];
				$distMarginRateListId = $gncr[4];
				$discount		= $gncr[5];
				$discountPercent	= $gncr[6];
				$invoiceDate		= $gncr[7];
				$selCityId		= $gncr[8];

				$transportActive	= $gncr[9];
				$transportCharge 	= 0;				
				if ($transportActive=='Y') $transportCharge = $gncr[10];
				$billingType = $gncr[11];
				$exportEnabled = ($billingType=="E")?"Y":"N";

				# Edu Cess
				list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
				# Sec Edu Cess
				list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
				# Excise Duty
				$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);

				# SO Entry Recs
				$soEntryRecs = $this->getNCSOEntryRecs($soId);
				$grandTotalAmount = 0;
				$taxArr	= array();
				$roundVal = "";
				$calcTotalSOAmt = "";
				$gTotalAmount = 0;
				$totalQty     = 0;

				$totExAmt 	= 0;
				$totEduCessAmt	= 0;
				$totSecEduCess	= 0;
				$grTotCentralTaxAmt = 0;

				foreach ($soEntryRecs as $ser) {
					$soEntryId 	= $ser[0];
					$productId	= $ser[1];
					$quantity	= $ser[2];
					$freePkts	= $ser[3];
					
					$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
					
					list($distAvgMargin,$distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
					
					# Find Tax Percent
					$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
					
					# Tax Rate
					$taxRate = ($taxPercent)/100;

					# Get the Tax Type (ie. VAT/CST)
					list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
					if ($billingForm=='ZP') $taxPercent = $taxRate = 0;

					if ($taxType=='CST') {
						# CST PERCENT From TAX MASTER
						$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
						$cstRate = ($cstPercent/100);
						//$calcBasicMgn = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;
						$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
						$distAvgMargin = number_format($calcBasicMgn,2,'.','');
						$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),3,'.','');
						$actualCostToDist = $avgMgnCost;
					} else {
						$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));	
						//$calcCostToDist = $avgMgnCost/(1+$taxRate);
						$calcCostToDist = $avgMgnCost;
						$actualCostToDist = number_format($calcCostToDist,4,'.','');
					}
					if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
					else 	$costToDist = 0;

					$basicRate	= $costToDist;
					$unitPrice 	= ($basicRate*$quantity)/($quantity+$freePkts);

					# Total Qty
					$totalQty 	= $quantity+$freePkts;
					# Calculation
					$calcTotalAmount = $unitPrice * $totalQty;					
					$grandTotalAmount += $calcTotalAmount;
						
					// Excise Duty 
					list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
					$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
					if ($exciseDutyActive) {
						list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
						if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
					}
					
					if ($calcTotalAmount!=0) { //&& $taxPercent!=0
						// calculating Discount (Basic Total-Discount)
						$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);
						$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;

						// Excise Duty Calculation
						$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
						$calcTotCExDuty = 0;	
						if ($exciseDutyAmt>0) {
							$calcTotCExDuty += $exciseDutyAmt;							
							$totExAmt += $exciseDutyAmt;
							
							if ($eduCess!=0) {
								$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
								$calcTotCExDuty += $eduCessAmt;
								$totEduCessAmt += $eduCessAmt;
							}
		
							if ($secEduCess!=0) {
								$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
								$calcTotCExDuty += $secEduCessAmt;
								$totSecEduCess += $secEduCessAmt;
							}
		
							$cTotalAmount += $calcTotCExDuty;
						}
						$grTotCentralTaxAmt += $calcTotCExDuty;

						// After Discount calc Grand Total Amt
						$gTotalAmount += $cTotalAmount;
						$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;	
						$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
						if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
					}
					
					# Update SO Entry Recs
					if ($calcTotalAmount!=0) {
						$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
					}
				}  # Entry Loops Ends here
				
				$selTaxArr	= array();
				$totalTaxAmt = 0;
				$selTax = "";
				if (sizeof($taxArr)>0) {
					$j = 0;
					foreach ($taxArr as $taxPercent=>$taxAmt) {
						$totalTaxAmt += $taxAmt;
						$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
						$selTaxArr[$j] = $arrVal;				
						$j++;
					}
					$selTax 	= implode(",",$selTaxArr);
				}	

				# Calc Total SO Amt
				$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;
				//Round off Calculation
				$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));

				# Total Discount Amt
				$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');		

				# Update main Table
				$updateSalesOrder = $this->updateSalesOrder($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
			} // Main Loop Ends Here
		} // NC Condition Ends here	
	}

	# Get Not Confirmed Recs
	function getNCStateWiseSORecs($stateId)
	{		
		$qry = " select a.id, a.distributor_id, a.state_id, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.discount, a.discount_percent, a.invoice_date, a.city_id, a.transport_charge_active, a.transport_charge, a.billing_type from t_salesorder a where a.state_id='$stateId' and (a.complete_status<>'C' or a.complete_status is null) ";
		//echo "$qry<br/>";		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}	
	# -------------------------- State Wise Vat Update Ends here ----

	# -------------------------- Base CST Wise Update Starts here ----
	function updateBaseCSTRecInSO()
	{
		# get SO Recs
		$getNCBaseCSTWiseSORecs = $this->getNCBaseCSTSORecs();	

		if (sizeof($getNCBaseCSTWiseSORecs)>0) {	
			$i=0;		
			foreach ($getNCBaseCSTWiseSORecs as $gncr) {
				$i++;
				$soId		= $gncr[0];
				$distributorId	= $gncr[1];
				$stateId	= $gncr[2];
				$productPriceRateListId = $gncr[3];
				$distMarginRateListId = $gncr[4];
				$discount		= $gncr[5];
				$discountPercent	= $gncr[6];
				$invoiceDate		= $gncr[7];
				$selCityId		= $gncr[8];
				
				$transportActive	= $gncr[9];
				$transportCharge 	= 0;				
				if ($transportActive=='Y') $transportCharge = $gncr[10];
				$billingType = $gncr[11];
				$exportEnabled = ($billingType=="E")?"Y":"N";

				# Edu Cess
				list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
				# Sec Edu Cess
				list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
				# Excise Duty
				$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);

				# SO Entry Recs
				$soEntryRecs = $this->getNCSOEntryRecs($soId);
				$grandTotalAmount = 0;
				$taxArr	= array();
				$roundVal = "";
				$calcTotalSOAmt = "";
				$gTotalAmount = 0;
				$totalQty     = 0;

				$totExAmt 	= 0;
				$totEduCessAmt	= 0;
				$totSecEduCess	= 0;
				$grTotCentralTaxAmt = 0;
				foreach ($soEntryRecs as $ser) {
					$soEntryId 	= $ser[0];
					$productId	= $ser[1];
					$quantity	= $ser[2];
					$freePkts	= $ser[3];
					
					$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
					
					list($distAvgMargin,$distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
					
					# Find Tax Percent
					$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
					
					# Tax Rate
					$taxRate = ($taxPercent)/100;

					# Get the Tax Type (ie. VAT/CST)
					list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
					if ($billingForm=='ZP') $taxPercent = $taxRate = 0;
					
					if ($taxType=='CST') {
						# CST PERCENT From TAX MASTER
						$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
						$cstRate = ($cstPercent/100);
						//$calcBasicMgn = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;
						$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
						$distAvgMargin = number_format($calcBasicMgn,4,'.','');
						$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');				
						$actualCostToDist = $avgMgnCost;
					} else {
						$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));	
						//$calcCostToDist = $avgMgnCost/(1+$taxRate);
						$calcCostToDist = $avgMgnCost;
						$actualCostToDist = number_format($calcCostToDist,4,'.','');
					}
					if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
					else 	$costToDist = 0;
				
					$basicRate	= $costToDist;
					$unitPrice 	= ($basicRate*$quantity)/($quantity+$freePkts);
					# Total Qty
					$totalQty 	= $quantity+$freePkts;
					# Calculation
					$calcTotalAmount = $unitPrice * $totalQty;					
					$grandTotalAmount += $calcTotalAmount;
					
					// Excise Duty 
					list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
					$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
					if ($exciseDutyActive) {
						list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
						if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
					}

					if ($calcTotalAmount!=0 ) { //&& $taxPercent!=0
						// calculating Discount (Basic Total-Discount)
						$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
						$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;

						// Excise Duty Calculation
						$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
						$calcTotCExDuty = 0;	
						if ($exciseDutyAmt>0) {
							$calcTotCExDuty += $exciseDutyAmt;							
							$totExAmt += $exciseDutyAmt;
							
							if ($eduCess!=0) {
								$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
								$calcTotCExDuty += $eduCessAmt;
								$totEduCessAmt += $eduCessAmt;
							}
		
							if ($secEduCess!=0) {
								$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
								$calcTotCExDuty += $secEduCessAmt;
								$totSecEduCess += $secEduCessAmt;
							}
		
							$cTotalAmount += $calcTotCExDuty;
						}
						$grTotCentralTaxAmt += $calcTotCExDuty;

						// After Discount calc Grand Total Amt
						$gTotalAmount += $cTotalAmount;
						$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;	
						$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
						if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
					}
					
					# Update SO Entry Recs
					if ($calcTotalAmount!=0) {
						$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
					}
				}  # Entry Loops Ends here

				$selTaxArr	= array();
				$totalTaxAmt = 0;
				$selTax = "";
				if (sizeof($taxArr)>0) {
					$j = 0;
					foreach ($taxArr as $taxPercent=>$taxAmt) {
						$totalTaxAmt += $taxAmt;
						$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
						$selTaxArr[$j] = $arrVal;				
						$j++;
					}
					$selTax 	= implode(",",$selTaxArr);
				}		

				# Calc Total SO Amt
				$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;
				//Round off Calculation
				$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));	
				# Total Discount Amt
				$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');
				# Update main Table
				$updateSalesOrder = $this->updateSalesOrder($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
			} // Main Loop Ends Here
		} // NC Condition Ends here
	}

	# Get Not Confirmed Recs
	function getNCBaseCSTSORecs()
	{		
		$qry = " select a.id, a.distributor_id, a.state_id, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.discount, a.discount_percent, a.invoice_date, a.city_id, a.transport_charge_active, a.transport_charge, a.billing_type from t_salesorder a where (a.complete_status<>'C' or a.complete_status is null) ";
		//echo "$qry<br/>";		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# -------------------------- Base CST Wise Update Ends here ----

	# -------------------------- Distributor Master Wise Update Starts here ----
	function updateDistributorWiseSORec($distributorId, $selStateId, $selCity)
	{
		# get SO Recs
		$getNCDistributorSORecs = $this->getNCDistributorSORecs($distributorId, $selStateId, $selCity);	

		if (sizeof($getNCDistributorSORecs)>0) {	
			$i=0;		
			foreach ($getNCDistributorSORecs as $gncr) {
				$i++;
				$soId		= $gncr[0];
				$distributorId	= $gncr[1];
				$stateId	= $gncr[2];
				$productPriceRateListId = $gncr[3];
				$distMarginRateListId = $gncr[4];
				$discount		= $gncr[5];
				$discountPercent	= $gncr[6];
				$invoiceDate		= $gncr[7];
				$selCityId		= $gncr[8];

				$transportActive	= $gncr[9];
				$transportCharge 	= 0;				
				if ($transportActive=='Y') $transportCharge = $gncr[10];
				$billingType = $gncr[11];
				$exportEnabled = ($billingType=="E")?"Y":"N";

				# Edu Cess
				list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
				# Sec Edu Cess
				list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
				# Excise Duty
				$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);

				# SO Entry Recs
				$soEntryRecs = $this->getNCSOEntryRecs($soId);
				$grandTotalAmount = 0;
				$taxArr	= array();
				$roundVal = "";
				$calcTotalSOAmt = "";
				$gTotalAmount = 0;
				$totalQty     = 0;
			
				$totExAmt 	= 0;
				$totEduCessAmt	= 0;
				$totSecEduCess	= 0;
				$grTotCentralTaxAmt = 0;
				foreach ($soEntryRecs as $ser) {
					$soEntryId 	= $ser[0];
					$productId	= $ser[1];
					$quantity	= $ser[2];
					$freePkts	= $ser[3];
					
					$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
					
					list($distAvgMargin, $distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
					
					# Find Tax Percent
					$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
					
					# Tax Rate
					$taxRate = ($taxPercent)/100;

					# Get the Tax Type (ie. VAT/CST)
					list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
					if ($billingForm=='ZP') $taxPercent = $taxRate = 0;
					
					if ($taxType=='CST') {
						# CST PERCENT From TAX MASTER
						$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
						$cstRate = ($cstPercent/100);
						//$calcBasicMgn = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;
						$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
						$distAvgMargin = number_format($calcBasicMgn,4,'.','');
						$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');				
						$actualCostToDist = $avgMgnCost;
					} else {
						$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));	
						//$calcCostToDist = $avgMgnCost/(1+$taxRate);
						$calcCostToDist = $avgMgnCost;
						$actualCostToDist = number_format($calcCostToDist,4,'.','');
					}
					if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
					else 	$costToDist = 0;

					$basicRate	= $costToDist;
					$unitPrice 	= ($basicRate*$quantity)/($quantity+$freePkts);	
					
					# Total Qty
					$totalQty 	= $quantity+$freePkts;	

					# Calculation
					$calcTotalAmount = $unitPrice * $totalQty;					
					$grandTotalAmount += $calcTotalAmount;
			
					// Excise Duty 
					list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
					$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
					if ($exciseDutyActive) {
						list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
						if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
					}

					if ($calcTotalAmount!=0) { //&& $taxPercent!=0
						// calculating Discount (Basic Total-Discount)
						$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
						$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;

						// Excise Duty Calculation
						$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
						$calcTotCExDuty = 0;	
						if ($exciseDutyAmt>0) {
							$calcTotCExDuty += $exciseDutyAmt;							
							$totExAmt += $exciseDutyAmt;
							
							if ($eduCess!=0) {
								$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
								$calcTotCExDuty += $eduCessAmt;
								$totEduCessAmt += $eduCessAmt;
							}
		
							if ($secEduCess!=0) {
								$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
								$calcTotCExDuty += $secEduCessAmt;
								$totSecEduCess += $secEduCessAmt;
							}
		
							$cTotalAmount += $calcTotCExDuty;
						}
						$grTotCentralTaxAmt += $calcTotCExDuty;

						// After Discount calc Grand Total Amt
						$gTotalAmount += $cTotalAmount;						
						$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;		
						$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
						if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
					}
					
					# Update SO Entry Recs
					if ($calcTotalAmount!=0) {
						$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
					}
				}  # Entry Loops Ends here
				
				$selTaxArr	= array();
				$totalTaxAmt = 0;
				$selTax = "";
				if (sizeof($taxArr)>0) {
					$j = 0;
					foreach ($taxArr as $taxPercent=>$taxAmt) {
						$totalTaxAmt += $taxAmt;
						$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
						$selTaxArr[$j] = $arrVal;				
						$j++;
					}
					$selTax 	= implode(",",$selTaxArr);
				}	

				# Calc Total SO Amt			
				$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;
				//Round off Calculation
				$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));	
				# Total Discount Amt
				$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');

				# Update main Table
				$updateSalesOrder = $this->updateSalesOrder($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
			} // Main Loop Ends Here
		} // NC Condition Ends here
	}

	# Get Not Confirmed Recs
	function getNCDistributorSORecs($distributorId, $selStateId, $selCity)
	{		
		$qry = " select a.id, a.distributor_id, a.state_id, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.discount, a.discount_percent, a.invoice_date, a.city_id, a.transport_charge_active, a.transport_charge, a.billing_type from t_salesorder a where (a.complete_status<>'C' or a.complete_status is null) and a.distributor_id='$distributorId' and a.state_id='$selStateId'  and a.city_id='$selCity'  ";
		//echo "$qry<br/>";		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# -------------------------- Distributor Master Wise Update Ends here ----

	## ------ Mgn Struct Rec Update Starts Here
	/**
	* Desc: This function will update all mgn structure recs
	* @param $distributorId: Distibutor id, @param $selRateListId: Dist mgn rate list id
	**/
	function updateDistributorMgnStructRecs($distributorId=null, $selRateListId=null)
	{
		# Get Dist State Records
		$getDistStateRecords  = $this->filterDistStateRecords($distributorId, $selRateListId);
			# Magin Struct Records
			$marginStructureRecords = $this->marginStructureObj->fetchAllRecords();
			/*
			# CST PERCENT From TAX MASTER
			$cstPercent = $this->taxMasterObj->getBaseCst();
			*/

			$calcTaxRate = 0;
			foreach ($getDistStateRecords as $dsr) {

				$sBillingForm		= $dsr[4];
				$distributorMgnStateEntryId = $dsr[5];	
				//$octroiPercent		= $dsr[7];				
				$selProduct		= $dsr[12];
				$selStateId		= $dsr[2];
				$selDistributor		= $dsr[1];
				$vatOrCSTMgnInclude	= $dsr[13]; // Y/N
				$taxType		= $dsr[3];
				$distStateEntryId	= $dsr[0];
				$distMarginRateList	= $dsr[14];

				$octroiApplicable	= $dsr[15]; 
				$octroiExempted		= $dsr[16];
				$exBillingForm		= $dsr[17];	

				list($selCityEntryId, $selCityId) = $this->getSelCityId($distStateEntryId);
				// Octroi setting based on dist master
				$octroiPercent = 0;
				if ($octroiApplicable=='Y' && $octroiExempted!='Y') $octroiPercent = $this->getOctroiPercent($selCityId);				

				list($startDate, $endDate) = $this->distMarginStructureObj->getDistMgnRateListRec($distMarginRateList);

				# CST PERCENT From TAX MASTER
				$cstPercent = $this->taxMasterObj->getBaseCst($startDate);
				# Get Dist Tax Percent (If Zero Percent)
				if ($sBillingForm=='ZP') {
					$vatOrCstPercent = 0; 
					$cstPercent = 0;
				} else $vatOrCstPercent = $this->distMarginStructureObj->getDistWiseTaxPercent($selProduct, $selStateId, $selDistributor, $distMarginRateList); 
				$vatOrCSTRate = $vatOrCstPercent/100;
				
				$exciseDuty = 0;
				if ($exBillingForm!='FCT1') {
					# Edu Cess
					list($eduCessPercent, $eduCessRLId) = $this->distMarginStructureObj->getEduCessDuty($startDate);		
					#Sec Edu Cess
					list($secEduCessPercent, $secEduCessRLId) = $this->distMarginStructureObj->getSecEduCessDuty($startDate);			
					# Excise Duty calc
					$basicExciseDuty	= $this->distMarginStructureObj->getExciseDutyPercent($selProduct, $startDate, false, '', '', '');
					$exciseDuty = $basicExciseDuty;
				}

				if ($exciseDuty>0) {
					$eduCessDutyRate 	= number_format(($exciseDuty*($eduCessPercent/100)),2,'.','');
					$exciseDuty += $eduCessDutyRate;
					$secEduCessDutyRate 	= number_format(($exciseDuty*($secEduCessPercent/100)),2,'.','');
					$exciseDuty += $secEduCessDutyRate;
				}
				$exciseDutyRate   = ($exciseDuty/100);
			
				$billingForm	= "";
				//Billing Form VN: VAT NO, CFF: Form F, FC:Form C, FN:Form None
				if ($sBillingForm=='FF' || $sBillingForm=='FC' || $sBillingForm=='FN') {
					$billingForm = 'Y';
				} else if ($sBillingForm=='VN') {
					$billingForm = 'N';
				}

				$actualValue = 0;
				$calcDistMargin = 0;	
				$calcMarkUpValue=0;
				$totalMarkUpValue=1;
				$totalMarkDownValue = 1;	
				$distMarginEntryId = "";
				$distMarginPercent = "";
				$avgMargin = "";
				$tMarkUpValue = 1;
				$tMarkDownValue = 1;
				$cMarkUpValue = 0;
				$cMarkDownValue = 0;
				foreach ($marginStructureRecords as $msr) {
					$marginStructureId = $msr[0];
					$marginStructureName	= stripSlash($msr[1]);
					$mgnStructureDescr	= stripSlash($msr[2]);
					$priceCalcType		= $msr[3];
					$useAvgDistMagn		= $msr[4];
					$mgnStructBillingOnFormF = $msr[7];

					list($distMarginEntryId, $distMarginPercent) = $this->distMarginStructureObj->getMarginEntryRec($distributorMgnStateEntryId, $marginStructureId);
					if ($mgnStructBillingOnFormF=='Y' && $billingForm=='Y') {
						$distMarginPercent = $this->taxMasterObj->getBaseCst($startDate);
					} else if($mgnStructBillingOnFormF=='Y' && $billingForm=='N') {
						$distMarginPercent = 0;
					}
					$actualValue =  $distMarginPercent/100;

					if ($useAvgDistMagn=='Y') {				
						if ($priceCalcType=='MU') {
							$calcMarkUpValue = 1+$actualValue;
							$totalMarkUpValue /= $calcMarkUpValue;			
						}		
						if ($priceCalcType=='MD') {
							$calcMarkDownValue = 1-$actualValue;			
							$totalMarkDownValue *= $calcMarkDownValue;
						}
					}
					
					if ($useAvgDistMagn=='N') {		
						if ($priceCalcType=='MU') {
							$cMarkUpValue = 1+$actualValue;
							$tMarkUpValue /= $cMarkUpValue;			
						}
	
						if ($priceCalcType=='MD') {
							$cMarkDownValue = 1-$actualValue;	
							$tMarkDownValue *= $cMarkDownValue;
						}
					}
					//echo "Structure=>$distMarginEntryId, $marginStructureName = $distMarginPercent"."<br>";
					# Update Dist Margin Structure ()
					$updateDistMagnStructureRec = $this->distMarginStructureObj->updateDistMarginStructureEntry($distMarginEntryId, $distMarginPercent);
				}  // Structure Loops Ends Here

				if ($vatOrCSTMgnInclude=='N') {
					if ($taxType=='CST') 		$taxRate = $cstPercent/100;
					else if ($taxType=='VAT')	$taxRate = $vatOrCSTRate;
					//$calcTaxRate = 1+$taxRate;
					$totalMarkUpValue = $totalMarkUpValue/(1+$taxRate);
				}
				# Avg Mgn
				$calcDistMargin = (1-($totalMarkUpValue*$totalMarkDownValue))*100;

				// Excise Duty
				$exDutyMarkUpValue	= (1+$exciseDutyRate);
				$basicMarkDownValue	= $totalMarkDownValue*$exDutyMarkUpValue;
				$calcBasicMgn	= (1-(1-($calcDistMargin/100))*$exDutyMarkUpValue)*100;
				
				// VAT/CST
				$vatMarkUpValue = 1+$vatOrCSTRate;				
				//$finalMarkDownValue = $totalMarkDownValue*$vatMarkUpValue; // Removed for Excise Duty
				$finalMarkDownValue = $basicMarkDownValue*$vatMarkUpValue;
	
				$calcFinalDistMgn = (1-(1-($calcDistMargin/100))*$vatMarkUpValue*$exDutyMarkUpValue)*100;				

				// Calc Total Mgn (Mark up /, Mark down *)
				$calcDiscountMgn = ($totalMarkUpValue/$tMarkUpValue)*($finalMarkDownValue*$tMarkDownValue);
				

				# Calc Actual Mgn				
				$octroiMarkupValue = 0;
				$octroi	= $octroiPercent/100;

				$octroiMarkupValue = 1+$octroi;
				$calcDiscountMgn = $calcDiscountMgn/$octroiMarkupValue;
				
				# Calc Actual Margin							
				$calcActualDistMgn = (1-($calcDiscountMgn))*100;

				if ($calcDistMargin!="" && $calcActualDistMgn!="") {
					$avgMargin 	= number_format($calcDistMargin,4,'.','');	
					$actualMargin 	= number_format($calcActualDistMgn,4,'.','');
					$finalMargin	= number_format($calcFinalDistMgn,4,'.','');
					$basicMargin	= number_format($calcBasicMgn,4,'.','');
					//echo "=====>$distributorMgnStateEntryId,Average=$avgMargin, actual=$actualMargin"."<br>";
					# Update Dist Margin State Average Margin
					$updateDistMarginStateWiseRec = $this->updateDistMarginStateRec($distributorMgnStateEntryId, $avgMargin, $actualMargin, $finalMargin, $vatOrCstPercent, $octroiPercent, $selCityId, $exciseDuty, $basicMargin);
				}
			}
		return true;
	}

	# Get Dist State Records
	function filterDistStateRecords($distributorId, $rateListId=null)
	{
		if ($distributorId) $whr = " a.distributor_id='$distributorId'";		
		if ($distributorId && $rateListId) $whr .= " and c.rate_list_id='$rateListId'";
		
		$qry = " select a.id, a.distributor_id, a.state_id, a.tax_type, a.billing_form, b.id, b.avg_margin, b.octroi, b.vat, b.freight, b.transport_cost, b.actual_margin, c.product_id, b.vat_cst_include, c.rate_list_id as distMarginRateList, a.octroi_applicable, a.octroi_exempted, a.ex_billing_form from m_distributor_state a left join m_distributor_margin_state b on a.id=b.dist_state_entry_id join m_distributor_margin c on b.distributor_margin_id=c.id ";

		if ($whr!="") $qry .= " where ".$whr;

		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSelCityId($distStateEntryId)
	{
		$qry = " select id, city_id from m_distributor_city where dist_state_entry_id='$distStateEntryId'";
		$result	= $this->databaseConnect->getRecord($qry);
		return array($result[0],$result[1]);
	}

	# Get Octroi Percent
	function getOctroiPercent($cityId)
	{
		$qry = "select octroi_percent from m_city where id=$cityId";
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}

	# update Dist Margin State Wise Rec
	function updateDistMarginStateRec($distMarginStateEntryId, $avgMargin, $actualMargin, $finalMargin, $vatOrCstPercent, $octroiPercent, $selCityId, $exciseDuty, $basicMargin)
	{
		$qry = "update m_distributor_margin_state set avg_margin='$avgMargin', actual_margin='$actualMargin', final_margin='$finalMargin', vat='$vatOrCstPercent', octroi='$octroiPercent', city_id='$selCityId', excise_duty='$exciseDuty', basic_margin='$basicMargin' where id='$distMarginStateEntryId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	# ------ Mgn Struct Rec Update Ends Here --

	#--------------------- Confirm release update SO Rec starts here -------------
	function updateSORec($soId)
	{
		# get SO Rec
		list($salesOrderId, $distributorId, $stateId, $productPriceRateListId, $distMarginRateListId, $discount, $discountPercent, $invoiceDate, $selCityId, $transportActive, $trptCharge, $billingType) = $this->getSORec($soId);	

		$transportCharge 	= 0;				
		if ($transportActive=='Y') $transportCharge = $trptCharge;
		$exportEnabled = ($billingType=="E")?"Y":"N";

		if ($salesOrderId!="") {	

			# Edu Cess
			list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
			# Sec Edu Cess
			list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
			# Excise Duty
			$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);
		
			# SO Entry Recs
			$soEntryRecs = $this->getNCSOEntryRecs($soId);
			$grandTotalAmount = 0;
			$taxArr	= array();
			$roundVal = "";
			$calcTotalSOAmt = "";
			$gTotalAmount = 0;
			$totalQty     = 0;

			$totExAmt 	= 0;
			$totEduCessAmt	= 0;
			$totSecEduCess	= 0;
			$grTotCentralTaxAmt = 0;

			foreach ($soEntryRecs as $ser) {
				$soEntryId 	= $ser[0];
				$productId	= $ser[1];
				$quantity	= $ser[2];
				$freePkts	= $ser[3];
					
				$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
					
				list($distAvgMargin,$distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);

				# Find Tax Percent
				$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
					
				# Tax Rate
				$taxRate = ($taxPercent)/100;

				# Get the Tax Type (ie. VAT/CST)
				list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
				if ($billingForm=='ZP') $taxPercent = $taxRate = 0;
					
				if ($taxType=='CST') {
					# CST PERCENT From TAX MASTER
					$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
					$cstRate = ($cstPercent/100);
					//$calcBasicMgn = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;
					$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
					$distAvgMargin = number_format($calcBasicMgn,4,'.','');
					$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');				
					$actualCostToDist = $avgMgnCost;
				} else {
					$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));	
					//$calcCostToDist = $avgMgnCost/(1+$taxRate);
					$calcCostToDist = $avgMgnCost;
					$actualCostToDist = number_format($calcCostToDist,4,'.','');
				}
				if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
				else 	$costToDist = 0;

				$basicRate	= $costToDist;
				$calcUnitPrice 	= number_format((($basicRate*$quantity)/($quantity+$freePkts)),4,'.','');

				# Total Qty
				$totalQty 	= $quantity+$freePkts;
				
				# Calculation
				$unitPrice =  number_format($calcUnitPrice,4,'.','');
				$calcTotalAmount = $unitPrice * $totalQty;					
				$grandTotalAmount += $calcTotalAmount;


				// Excise Duty 
				list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
				$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
				if ($exciseDutyActive) {
					list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
					if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
				}

				if ($calcTotalAmount!=0) { // && $taxPercent!=0
					// calculating Discount (Basic Total-Discount)
					$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
					$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;

					// Excise Duty Calculation
					$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
					$calcTotCExDuty = 0;	
					if ($exciseDutyAmt>0) {
						$calcTotCExDuty += $exciseDutyAmt;							
						$totExAmt += $exciseDutyAmt;
						
						if ($eduCess!=0) {
							$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
							$calcTotCExDuty += $eduCessAmt;
							$totEduCessAmt += $eduCessAmt;
						}
	
						if ($secEduCess!=0) {
							$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
							$calcTotCExDuty += $secEduCessAmt;
							$totSecEduCess += $secEduCessAmt;
						}
	
						$cTotalAmount += $calcTotCExDuty;
					}
					$grTotCentralTaxAmt += $calcTotCExDuty;

					// After Discount calc Grand Total Amt
					$gTotalAmount += $cTotalAmount;

					$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;	
					$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
					if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
				}
				
				# Update SO Entry Recs
				if ($calcTotalAmount!=0) {
					//echo "<br>Entry=$soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate<br>";
					$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
				}
			}  # Entry Loops Ends here
				
			$selTaxArr	= array();
			$totalTaxAmt 	= 0;
			$selTax 	= "";
			if (sizeof($taxArr)>0) {
				$j = 0;
				foreach ($taxArr as $taxPercent=>$taxAmt) {
					$totalTaxAmt += $taxAmt;
					$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
					$selTaxArr[$j] = $arrVal;				
					$j++;
				}
				$selTax 	= implode(",",$selTaxArr);
			}	
			
			#Calc Total SO Amt
			$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;

			#Round off Calculation
			$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));	
			
			#Total Discount Amt
			$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');

			#Update main Table
			//echo "<br>Main=$soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt<br>";
			$updateSalesOrder = $this->updateSalesOrder($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
		} // NC Condition Ends here
	}

	# Get SO Recs
	function getSORec($soId)
	{		
		$qry = " select id, distributor_id, state_id, rate_list_id as pprl, dist_mgn_ratelist_id, discount, discount_percent, invoice_date, city_id, transport_charge_active, transport_charge, billing_type from t_salesorder where (complete_status<>'C' or complete_status is null) and id='$soId'  ";
		//echo "$qry<br/>";		
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4],$rec[5],$rec[6], $rec[7], $rec[8], $rec[9], $rec[10], $rec[11]):array();
	}
	#--------------------- Confirm release update SO Rec Ends here -------------

	#   --- When Dist Margin Rate List Date Changed -- Starts Here --
	function getSORecords($selDate, $prevDate, $selDistributor)
	{
		//echo mysqlDateFormat("01/03/2009");
		if ($selDate) $soRecords = $this->filterNCSORecords($selDate, $prevDate, $selDistributor);
	
		if (sizeof($soRecords)>0) {
			foreach ($soRecords as $sor) {
				$soId			= $sor[0];
				$distributorId		= $sor[1];
				$stateId		= $sor[2];
				$productPriceRateListId = $sor[3];
				$selDistMarginRateListId = $sor[4];
				$invoiceDate		= $sor[5];
				$discount		= $gncr[6];
				$discountPercent	= $gncr[7];	
				$selCityId		= $gncr[8];
				$transportActive	= $gncr[9];
				$transportCharge 	= 0;				
				if ($transportActive=='Y') $transportCharge = $gncr[10];
				$billingType = $gncr[11];
				$exportEnabled = ($billingType=="E")?"Y":"N";

				# Edu Cess
				list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
				# Sec Edu Cess
				list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
				# Excise Duty
				$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);

				$distMarginRateListId   = $this->distMarginRateListObj->getRateList($distributorId, $invoiceDate);
				# If Dist MGN Rate List Different
				if ($selDistMarginRateListId!=$distMarginRateListId) {
					$updateSOMainRec = $this->updateSOMainRec($soId, $distMarginRateListId);
					# SO Entry Recs
					$soEntryRecs = $this->getNCSOEntryRecs($soId);
					$grandTotalAmount = 0;
					$taxArr	= array();
					$roundVal = "";
					$calcTotalSOAmt = "";
					$gTotalAmount = 0;
					$totalQty     = 0;

					$totExAmt 	= 0;
					$totEduCessAmt	= 0;
					$totSecEduCess	= 0;
					$grTotCentralTaxAmt = 0;
					foreach ($soEntryRecs as $ser) {
						$soEntryId 	= $ser[0];
						$productId	= $ser[1];
						$quantity	= $ser[2];
						$freePkts	= $ser[3];
							
						$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
							
						list($distAvgMargin,$distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
								
						# Find Tax Percent
						$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
							
						# Tax Rate
						$taxRate = ($taxPercent)/100;
		
						# Get the Tax Type (ie. VAT/CST)
						list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
						if ($billingForm=='ZP') $taxPercent = $taxRate = 0;
							
						if ($taxType=='CST') {
							# CST PERCENT From TAX MASTER
							$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
							$cstRate = ($cstPercent/100);
							//$calcBasicMgn = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;
							$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
							$distAvgMargin = number_format($calcBasicMgn,4,'.','');
							$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');
							$actualCostToDist = $avgMgnCost;
						} else {
							$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));	
							//$calcCostToDist = $avgMgnCost/(1+$taxRate);
							$calcCostToDist = $avgMgnCost;
							$actualCostToDist = number_format($calcCostToDist,4,'.','');
						}
						if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
						else 	$costToDist = 0;

						$basicRate	= $costToDist;
						$calcUnitPrice 	= ($basicRate*$quantity)/($quantity+$freePkts);

						# Total Qty
						$totalQty 	= $quantity+$freePkts;
						# Calculation
						$unitPrice =  number_format($calcUnitPrice,4,'.','');
						$calcTotalAmount = $unitPrice * $totalQty;					
						$grandTotalAmount += $calcTotalAmount;

						// Excise Duty 
						list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
						$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
						if ($exciseDutyActive) {
							list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
							if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
						}

						if ($calcTotalAmount!=0) { // && $taxPercent!=0
							// calculating Discount (Basic Total-Discount)
							$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
							$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;

							// Excise Duty Calculation
							$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
							$calcTotCExDuty = 0;	
							if ($exciseDutyAmt>0) {
								$calcTotCExDuty += $exciseDutyAmt;							
								$totExAmt += $exciseDutyAmt;
								
								if ($eduCess!=0) {
									$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
									$calcTotCExDuty += $eduCessAmt;
									$totEduCessAmt += $eduCessAmt;
								}
			
								if ($secEduCess!=0) {
									$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
									$calcTotCExDuty += $secEduCessAmt;
									$totSecEduCess += $secEduCessAmt;
								}
			
								$cTotalAmount += $calcTotCExDuty;
							}
							$grTotCentralTaxAmt += $calcTotCExDuty;

							// After Discount calc Grand Total Amt
							$gTotalAmount += $cTotalAmount;

							$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;
							$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
							if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
						}
						
						# Update SO Entry Recs
						if ($calcTotalAmount!=0) {
							$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
						}
					}  # Entry Loops Ends here
						
					$selTaxArr	= array();
					$totalTaxAmt = 0;
					$selTax = "";
					if (sizeof($taxArr)>0) {
						$j = 0;						
						foreach ($taxArr as $taxPercent=>$taxAmt) {
							$totalTaxAmt += $taxAmt;
							$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
							$selTaxArr[$j] = $arrVal;				
							$j++;
						}
						$selTax 	= implode(",",$selTaxArr);
					}		

					# Calc Total SO Amt
					$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;

					//Round off Calculation
					$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));	
					# Total Discount Amt
					$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');

					# Update main Table
					$updateSalesOrder = $this->updateSalesOrder($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
				} # Dist MGn Rate List Not equal to current
			}
		}

	}

	# Filter Not Completed SO Recs
	function filterNCSORecords($selDate, $prevDate, $selDistributor)
	{
		$uptdQry = "";
		if ($prevDate!="" && $selDate>$prevDate) $uptdQry = " and a.invoice_date>='$prevDate' and a.invoice_date<'$selDate' "; 
		else if ($selDate) $uptdQry = " and a.invoice_date>='$selDate' ";

		$qry = " select a.id, a.distributor_id, a.state_id, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.invoice_date, a.discount, a.discount_percent, a.city_id, a.transport_charge_active, a.transport_charge, a.billing_type  from t_salesorder a where (a.complete_status<>'C' or a.complete_status is null) and a.distributor_id='$selDistributor' $uptdQry ";
		//echo $qry ;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Upate ist MGN Rate List Id
	function updateSOMainRec($salesOrderId, $distMarginRateListId)
	{
		$qry = "update t_salesorder set dist_mgn_ratelist_id='$distMarginRateListId' where id='$salesOrderId'";	
		//echo "<br>SOMain=$qry<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	
	#   --- When Dist Margin Rate List Date Changed -- Ends Here --

	# -----------  Update all pending SO Recs Starts here -----------	
	function updateAllPendingSO()
	{		
		# get SO Recs
		$getNotConfirmedSORecs = $this->fetchAllPendingSO();
		
		if (sizeof($getNotConfirmedSORecs)>0) {	
			$i=0;		
			foreach ($getNotConfirmedSORecs as $gncr) {
				$i++;
				$soId		= $gncr[0];
				$distributorId	= $gncr[1];
				$stateId	= $gncr[2];
				$discount		= $gncr[5];
				$discountPercent	= $gncr[6];
				$invoiceDate		= $gncr[7];
				$selCityId		= $gncr[8];
				$transportActive	= $gncr[9];
				$transportCharge 	= 0;				
				if ($transportActive=='Y') $transportCharge = $gncr[10];
				$billingType = $gncr[11];
				$exportEnabled = ($billingType=="E")?"Y":"N";

				# Product Price Rate List (PMRP)	
				$productPriceRateListId = $this->manageRateListObj->getRateList("PMRP", $invoiceDate);
				# Dist Margin Rate List Id
				$distMarginRateListId = $this->distMarginRateListObj->getRateList($distributorId, $invoiceDate);

				# Edu Cess
				list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
				# Sec Edu Cess
				list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
				# Excise Duty
				$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);				

				# SO Entry Recs
				$soEntryRecs = $this->getNCSOEntryRecs($soId);
				$grandTotalAmount = 0;
				$taxArr	= array();
				$roundVal = "";
				$calcTotalSOAmt = "";
				$gTotalAmount = 0;
				$totalQty     = 0;

				$totExAmt 	= 0;
				$totEduCessAmt	= 0;
				$totSecEduCess	= 0;
				$grTotCentralTaxAmt = 0;
				foreach ($soEntryRecs as $ser) {
					$soEntryId 	= $ser[0];
					$productId	= $ser[1];
					$quantity	= $ser[2];
					$freePkts	= $ser[3];
					
					$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
					/*
					if ($selProduct==$productId) $distAvgMargin = $avgMargin; 
					else
						*/
					list($distAvgMargin,$distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
											
					# Find Tax Percent
					$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
					//echo "$soId, $taxPercent<br>";
					//echo "TT=$distributorId, $stateId, $productId, $invoiceDate::$taxPercent<->$soId<br>";
					# Tax Rate
					$taxRate = ($taxPercent)/100; 
					
					# Get the Tax Type (ie. VAT/CST)
					list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
					if ($billingForm=='ZP') $taxPercent = $taxRate = 0;

					if ($taxType=='CST') {
						# CST PERCENT From TAX MASTER
						$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
						$cstRate = ($cstPercent/100);						
						$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
						$distAvgMargin = number_format($calcBasicMgn,4,'.','');
						$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');
						$actualCostToDist = $avgMgnCost;
					} else {
						$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));
						$calcCostToDist = $avgMgnCost;
						$actualCostToDist = number_format($calcCostToDist,4,'.','');
					}
					if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
					else 	$costToDist = 0;
					
					$basicRate	= $costToDist;
					$calcUnitPrice 	= ($basicRate*$quantity)/($quantity+$freePkts);
					
					# Total Qty
					$totalQty 	= $quantity+$freePkts;

					# Calculation
					$unitPrice =  number_format($calcUnitPrice,4,'.','');
					$calcTotalAmount = $unitPrice * $totalQty;					
					$grandTotalAmount += $calcTotalAmount;
					
					// Excise Duty 
					list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
					$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
					if ($exciseDutyActive) {
						list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
						if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
					}

					if ($calcTotalAmount!=0 ) {
						// calculating Discount (Basic Total-Discount)
						$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
						$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;
						
						// Excise Duty Calculation
						$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
						$calcTotCExDuty = 0;	
						if ($exciseDutyAmt>0) {
							$calcTotCExDuty += $exciseDutyAmt;							
							$totExAmt += $exciseDutyAmt;
							
							if ($eduCess!=0) {
								$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
								$calcTotCExDuty += $eduCessAmt;
								$totEduCessAmt += $eduCessAmt;
							}
		
							if ($secEduCess!=0) {
								$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
								$calcTotCExDuty += $secEduCessAmt;
								$totSecEduCess += $secEduCessAmt;
							}
		
							$cTotalAmount += $calcTotCExDuty;
						}
						$grTotCentralTaxAmt += $calcTotCExDuty;

						// After Discount calc Grand Total Amt
						$gTotalAmount += $cTotalAmount;
						$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;	
						$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
						if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
					}

					
					# Update SO Entry Recs					
					//echo "<br>$soId=>SOALLEntry=$soEntryId, $calcUnitPrice=$unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent:::($basicRate*$quantity)/($quantity+$freePkts)<br>";
					$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);		
				}  # Entry Loops Ends here
				
				$totalTaxAmt = 0;
				$selTaxArr	= array();
				$selTax = "";
				if (sizeof($taxArr)>0) {
					$j = 0;
					foreach ($taxArr as $taxPercent=>$taxAmt) {
						$totalTaxAmt += $taxAmt;
						$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
						$selTaxArr[$j] = $arrVal;				
						$j++;
					}
					$selTax 	= implode(",",$selTaxArr);
				}	
				# Calc Total SO Amt			
				$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;
				//Round off Calculation
				$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));

				$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');
					
				# Update main Table
				//echo "<br>Main=$soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt<br>";
				//echo "<br><strong>-------Ends Here------------</strong><br>";				
				$updateSalesOrder = $this->updateSalesOrderMainRec($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $productPriceRateListId, $distMarginRateListId, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
			} // Main Loop Ends Here
		} // NC Condition Ends here
		return true;
	}

	# Get Pending SO Recs
	function fetchAllPendingSO()
	{		
		$qry = " select a.id, a.distributor_id, a.state_id, a.rate_list_id as pprl, a.dist_mgn_ratelist_id, a.discount, a.discount_percent, a.invoice_date, a.city_id, a.transport_charge_active, a.transport_charge, a.billing_type from t_salesorder a where (a.complete_status<>'C' or a.complete_status is null) ";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# ------------- Update all pending SO Recs Ends here --------------

	#--------------------- Order Processing update SO Rec starts here -------------
	function updateSalesOrderRec($soId, $selDate)
	{
		//echo "$soId, $selDate";
		# get SO Rec
		list($salesOrderId, $distributorId, $stateId, $sProductPriceRateListId, $sDistMarginRateListId, $discount, $discountPercent, $invoiceDate, $selCityId, $transportActive, $trptCharge, $billingType) = $this->getSORec($soId);

		$transportCharge 	= 0;				
		if ($transportActive=='Y') $transportCharge = $trptCharge;	
		$exportEnabled = ($billingType=="E")?"Y":"N";

		# Product Price Rate List (PMRP)	
		$productPriceRateListId = $this->manageRateListObj->getRateList("PMRP", $selDate);
		# Dist Margin Rate List Id
		$distMarginRateListId = $this->distMarginRateListObj->getRateList($distributorId, $selDate);

		# Edu Cess
		list($eduCess, $eduCessRLId) = $this->salesOrderObj->getEduCessDuty($invoiceDate);		
		# Sec Edu Cess
		list($secEduCess, $secEduCessRLId) = $this->salesOrderObj->getSecEduCessDuty($invoiceDate);
		# Excise Duty
		$exciseDutyActive = $this->salesOrderObj->chkExciseDutyActive($invoiceDate);

		if ($salesOrderId!="") {	
			# SO Entry Recs
			$soEntryRecs = $this->getNCSOEntryRecs($soId);
			$grandTotalAmount = 0;
			$taxArr	= array();
			$roundVal = "";
			$calcTotalSOAmt = "";
			$gTotalAmount = 0;
			$totalQty     = 0;

			$totExAmt 	= 0;
			$totEduCessAmt	= 0;
			$totSecEduCess	= 0;
			$grTotCentralTaxAmt = 0;
			foreach ($soEntryRecs as $ser) {
				$soEntryId 	= $ser[0];
				$productId	= $ser[1];
				$quantity	= $ser[2];
				$freePkts	= $ser[3];
					
				$mrp = $this->salesOrderObj->findProductPrice($productId, $productPriceRateListId, $distributorId, $stateId);
					
				list($distAvgMargin, $distMgnStateEntryId, $distBasicMargin) = $this->salesOrderObj->getDistAverageMargin($distributorId, $productId, $stateId, $distMarginRateListId, $selCityId, $exportEnabled);
				
				# Find Tax Percent
				$taxPercent = $this->salesOrderObj->getDistributorWiseTax($distributorId, $stateId, $productId, $invoiceDate);
				//echo "<br>TT=$distributorId, $stateId, $productId, $invoiceDate::$taxPercent";
					
				# Tax Rate
				$taxRate = ($taxPercent)/100;

				# Get the Tax Type (ie. VAT/CST)
				list($taxType, $billingForm, $billingStateId, $exBillingForm) 	= $this->salesOrderObj->getDistTaxType($distributorId, $stateId);
				if ($billingForm=='ZP') $taxPercent = $taxRate = 0;
					
				if ($taxType=='CST') {
					# CST PERCENT From TAX MASTER
					$cstPercent = $this->taxMasterObj->getBaseCst($invoiceDate);
					$cstRate = ($cstPercent/100);
					//$calcBasicMgn = (1- ((100-$distAvgMargin)/100)/(1+$cstRate))*100;
					$calcBasicMgn = (1- ((100-$distAvgMargin)/100))*100;
					$distAvgMargin = number_format($calcBasicMgn,4,'.','');
					$avgMgnCost	 = number_format(($mrp * (1-($distAvgMargin/100))),4,'.','');				
					$actualCostToDist = $avgMgnCost;
				} else {
					$avgMgnCost 	= $mrp * (1-($distAvgMargin/100));	
					//$calcCostToDist = $avgMgnCost/(1+$taxRate);
					$calcCostToDist = $avgMgnCost;
					$actualCostToDist = number_format($calcCostToDist,4,'.','');
				}
				if ($distAvgMargin>0) $costToDist = number_format($actualCostToDist,4,'.','');
				else 	$costToDist = 0;

				$basicRate	= $costToDist;
				$unitPrice 	= number_format((($basicRate*$quantity)/($quantity+$freePkts)),4,'.','');

				# Total Qty
				$totalQty 	= $quantity+$freePkts;

				# Calculation
				$calcTotalAmount = $unitPrice * $totalQty;					
				$grandTotalAmount += $calcTotalAmount;

				// Excise Duty 
				list($pCategoryId, $pStateId, $pGroupId) = $this->salesOrderObj->findProductRec($productId);
				$edEntryId=$exDutyRateListId=$exciseDutyPercent = $chapterSubheading="";		
				if ($exciseDutyActive) {
					list($edEntryId, $exDutyRateListId, $exciseDutyPercent, $chapterSubheading, $goodsType) = $this->salesOrderObj->getExciseDuty($invoiceDate, $pCategoryId, $pStateId, $pGroupId);
					if ($exBillingForm=="FCT1") $exciseDutyPercent = 0;
				}

				if ($calcTotalAmount!=0 ) { //&& $taxPercent!=0
					// calculating Discount (Basic Total-Discount)
					$discountCalc = $calcTotalAmount-(($calcTotalAmount*$discountPercent)/100);	
					$cTotalAmount = ($discountPercent=="" || $discountPercent==0)?$calcTotalAmount:$discountCalc;

					// Excise Duty Calculation
					$exciseDutyAmt = number_format((($cTotalAmount*$exciseDutyPercent)/100),2,'.','');
					$calcTotCExDuty = 0;	
					if ($exciseDutyAmt>0) {
						$calcTotCExDuty += $exciseDutyAmt;							
						$totExAmt += $exciseDutyAmt;
						
						if ($eduCess!=0) {
							$eduCessAmt = number_format((($exciseDutyAmt*$eduCess)/100),2,'.','');
							$calcTotCExDuty += $eduCessAmt;
							$totEduCessAmt += $eduCessAmt;
						}
	
						if ($secEduCess!=0) {
							$secEduCessAmt = number_format((($exciseDutyAmt*$secEduCess)/100),2,'.','');
							$calcTotCExDuty += $secEduCessAmt;
							$totSecEduCess += $secEduCessAmt;
						}
	
						$cTotalAmount += $calcTotCExDuty;
					}
					$grTotCentralTaxAmt += $calcTotCExDuty;

					// After Discount calc Grand Total Amt
					$gTotalAmount += $cTotalAmount;
					$calcTaxAmt	= ($cTotalAmount*$taxPercent)/100;	
					$itemTaxAmt = number_format($calcTaxAmt,2,'.','');
					if ($taxPercent!=0 && $calcTaxAmt!=0) $taxArr[$taxPercent] += $itemTaxAmt;
				}
				
				# Update SO Entry Recs
				if ($calcTotalAmount!=0) {
					//echo "<br>Entry=$soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent<br>";
					$updateSOEntryRecs = $this->updateSalesOrderentries($soEntryId, $unitPrice, $calcTotalAmount, $distMgnStateEntryId, $basicRate, $taxPercent, $exciseDutyPercent, $exciseDutyAmt, $edEntryId, $eduCessAmt, $secEduCessAmt, $itemTaxAmt);
				}
			}  # Entry Loops Ends here
				
			$selTaxArr	= array();
			$totalTaxAmt = 0;
			$selTax = "";
			if (sizeof($taxArr)>0) {				
				$j = 0;			
				foreach ($taxArr as $taxPercent=>$taxAmt) {
					$totalTaxAmt += $taxAmt;
					$arrVal =$taxPercent.":".number_format($taxAmt,2,'.','');
					$selTaxArr[$j] = $arrVal;				
					$j++;
				}
				$selTax 	= implode(",",$selTaxArr);
			}	
			
			#Calc Total SO Amt
			$calcTotalSOAmt = $gTotalAmount+$totalTaxAmt+$transportCharge;

			#Round off Calculation
			$roundVal = $this->salesOrderObj->getRoundoffVal(number_format($calcTotalSOAmt,2,'.',''));	
			
			#Total Discount Amt
			$discountAmt = number_format((($grandTotalAmount*$discountPercent)/100),2,'.','');

			#Update main Table
			//echo "<br>Main=$soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $productPriceRateListId, $distMarginRateListId<br>";
			$updateSalesOrder = $this->updateSalesOrderMainRec($soId, $grandTotalAmount, $totalTaxAmt, $calcTotalSOAmt, $selTax, $roundVal, $discountAmt, $productPriceRateListId, $distMarginRateListId, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt);
		} // NC Condition Ends here
		return true;
	}
	# Update  Sales Order
	function updateSalesOrderMainRec($salesOrderId, $totalAmount, $calcTaxAmt, $grandTotalAmt, $selTax, $roundVal, $discountAmt, $productPriceRateListId, $distMarginRateListId, $exciseDutyActive, $eduCess, $eduCessRLId, $secEduCess, $secEduCessRLId, $totExAmt, $totEduCessAmt, $totSecEduCess, $grTotCentralTaxAmt)
	{		
		$qry = "update t_salesorder set total_amt='$totalAmount', tax_amt='$calcTaxAmt', grand_total_amt='$grandTotalAmt', tax_applied='$selTax', round_value='$roundVal', discount_amt='$discountAmt', rate_list_id='$productPriceRateListId', dist_mgn_ratelist_id='$distMarginRateListId', ex_duty_active='$exciseDutyActive', edu_cess_percent='$eduCess', edu_cess_rl_id='$eduCessRLId', sec_edu_cess_percent='$secEduCess', sec_edu_cess_rl_id='$secEduCessRLId', tot_ex_duty_amt='$totExAmt', tot_edu_cess_amt='$totEduCessAmt', tot_sec_edu_cess_amt='$totSecEduCess', grand_tot_central_excise_amt='$grTotCentralTaxAmt' where id='$salesOrderId'";	
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	
	#--------------------- Confirm release update SO Rec Ends here -------------

	# --------------- Dist AC Updation starts here ----------------------
	function uptdDistAcDebitNCreditAmt()
	{
		#For Updating Debit Amt and Credit Amt
		$getDistACRecs = $this->fetchAllDAcRecs();
		
		foreach ($getDistACRecs as $dr) {
			$amtArr	= array();
			$distACId		= $dr[0];
			$distributorId		= $dr[1];
			$billAmt		= $dr[2];	
			$cod			= $dr[3];
			$invoiceId		= $dr[4];
			$amtArr[$cod]		= $billAmt;
			$refInvRecs	= $this->getRefInvRecs($invoiceId);
			$debitAmt = "";
			$creditAmt = "";
			foreach ($refInvRecs as $rir) {
				$subDistACId		= $rir[0];
				$subDistributorId	= $rir[1];
				$rInvAmt	= $rir[2];
				$pType		= $rir[3];
				$subInvoiceId	= $rir[5];
				$valueDate	= $rir[6];
				if ($valueDate!="0000-00-00") $amtArr[$pType]	+= $rInvAmt;
				//echo "<br>============>$subDistributorId::$subInvoiceId--Sub=$subDistACId, $pType, $rInvAmt===VAL Date=$valueDate";
			}
			$debitAmt	= $amtArr["D"];			
			$creditAmt	= $amtArr["C"];
			//DID=$distributorId:--INV=$invoiceId::
			//echo "<br>ACID=$distACId==>Debit=$debitAmt, Credit=$creditAmt";
			$updateDACRec= $this->updateDAcDebCreRec($distACId, $debitAmt, $creditAmt);
			//if ($updateDACRec) echo "updated";
		}
	}

	function fetchAllDAcRecs()
	{
		$qry = "select tdac.id, tdac.distributor_id, tdac.amount, tdac.cod, tdac.so_id from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id join m_common_reason mcr on mcr.id=tdac.reason_id where mcr.de_code='SI' ";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getRefInvRecs($invoiceId)
	{
		$qry = "select tdac.id, tdac.distributor_id, tdac.amount, tdac.cod, tdac.so_id, tdaci.invoice_id, tdac.value_date from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id left join m_common_reason mcr on mcr.id=tdac.reason_id where (mcr.de_code!='SI' or mcr.de_code is null) and tdaci.invoice_id='$invoiceId' and tdac.pmt_type!='M' ";
		//echo "Ref=<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateDAcDebCreRec($distACId, $debitAmt, $creditAmt)
	{
		$qry = "update t_distributor_ac set debit_amt='$debitAmt', credit_amt='$creditAmt' where id='$distACId'";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# --------------- Dist AC Updation Ends here ----------------------
	
}	
?>