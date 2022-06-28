<?php
require("include/include.php");
require("lib/UpdateQry_class.php");
$updateQryObj	= new UpdateQry($databaseConnect);

$update = $g["update"];
	
	if ($update) {
		#For Updating Debit Amt and Credit Amt
		$getDistACRecs = $updateQryObj->getDACRecs();
		
		foreach ($getDistACRecs as $dr) {
			$amtArr	= array();
			$distACId		= $dr[0];
			$distributorId		= $dr[1];
			$billAmt		= $dr[2];	
			$cod			= $dr[3];
			$invoiceId		= $dr[4];
			$amtArr[$cod]		= $billAmt;
			$refInvRecs	= $updateQryObj->getRefInvRecs($invoiceId);
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
				//echo "<br>$distributorId============>$subDistributorId::$subInvoiceId--Sub=$subDistACId, $pType, $rInvAmt===VAL Date=$valueDate";
			}
			$debitAmt	= $amtArr["D"];			
			$creditAmt	= $amtArr["C"];
			//DID=$distributorId:--INV=$invoiceId::
			echo "<br>ACID=$distACId==>Debit=$debitAmt, Credit=$creditAmt";
			$updateDACRec= $updateQryObj->updateDAcDebCreRec($distACId, $debitAmt, $creditAmt);
			if ($updateDACRec) echo "updated";
		}


	} else echo "Chking---------";

	/*
	# Converting Product MRP to new TABLE m_product_mrp_expt
	$getAllProductMRPList	= $updateQryObj->getProductMRPList();
	$cnt = 0;
	foreach ($getAllProductMRPList as $pml) {
		$productMRPId 	= $pml[0];
		$selMRP		= $pml[1];
		$defaultMRPExist = $updateQryObj->getDefaultMRP($productMRPId);	
		# insert Default Rec
		if (!$defaultMRPExist) {
			$updateQryObj->addProductMRPExpt($productMRPId, 0, 0, $selMRP);
			$cnt++;
		}		
	}
	echo "<br>$cnt::Updated";
	*/
	/*
	# For converting pre-processor rate to new table (Updated in Server
	$getAllPreProcessList = $updateQryObj->getPreProcessList();
	$cnt = 0;
	foreach ($getAllPreProcessList as $ppr) {
		
		$processMainId 	= $ppr[0];
		$ppRate		= $ppr[1];
		$ppCommi	= $ppr[2];
		$ppCriteria	= $ppr[3];
		$rateListId	= $ppr[4];
		$flag		= $ppr[5];
		if ($ppRate==0 || $ppCommi==0) echo "<b>$processMainId</b><br>";
		//echo "<br><b>$processMainId<=====>$flag</b></br>";
		echo "<br><b>Rate List ============>$rateListId</b>::";
		list($defaultExist, $exceptionId) = $updateQryObj->ppDefaultRateExist($processMainId);
		if (!$defaultExist && ($ppRate!=0 || $ppCommi!=0)) {
			$cnt++;
			echo "Insert=>0, $processMainId, $ppRate, $ppCriteria";
			$insertProcessorRate = $updateQryObj->addProcessorExmpt(0, $processMainId, $ppRate, $ppCommi, $ppCriteria);
		} else if ($defaultExist && $exceptionId && ($ppRate!=0 || $ppCommi!=0)) {
			echo "<br>Update===>>=>$exceptionId, $ppRate, $ppCommi, $ppCriteria";
			$updateExptEntry = $updateQryObj->updateProcessorExmpt($exceptionId, $ppRate, $ppCommi, $ppCriteria);
		}
	}
	//echo "<br>$cnt";
	# Convert Pre-process rate Ends Here	
	*/

/*
		$getProductRecs = $updateQryObj->getDistMarginProductRecords();
		echo "<br>Size=".sizeof($getProductRecs)."<br>";
				foreach ($getProductRecs as $gdr) {
					$distMarginId		= $gdr[0];
					$distMarginStateEntryId = $gdr[1];
					$productId	= $gdr[3];
					$stateId	= $gdr[6];
					
					
					$chkSalesOrder = $updateQryObj->chkSOProductUsed($productId, $stateId);
					list($chkStatus, $pstatusId) = $updateQryObj->chkProductStatusUsed($productId, $stateId);	
					# Checking Margin Used
					$marginUseChk = $distMarginStructureObj->chkDistMgnUsed($distMarginStateEntryId);

					echo "<br>$marginUseChk, $distMarginId, $distMarginStateEntryId, P=$productId, St=$stateId, SO=$chSalesOrder, Status=$chkStatus-$pstatusId";
					
					
					if ($distMarginStateEntryId && !$marginUseChk && !$chkSalesOrder) {
						$delDistMarginEntry = $distMarginStructureObj->delDistMarginEntryRec($distMarginStateEntryId);
						//$stateRec
						if ($delDistMarginEntry) {
							$delDistMarginStateEntry = $distMarginStructureObj->delDistMarginStateEntryRec($distMarginStateEntryId);
							if ($delDistMarginStateEntry) echo "==>$distMarginStateEntryId Entry deleted";
						}

						$chkDistStateRecExist = $distMarginStructureObj->chkDistStateRecSize($distMarginId);

						if (!$chkDistStateRecExist) {
							$distMarginRecDel = $distMarginStructureObj->deleteDistMarginStructure($distMarginId);
							if ($distMarginRecDel) echo "==>$distMarginId Main deleted";
						}
						if ($pstatusId) {
							$delStatus = $updateQryObj->delProductStatus($pstatusId);
							if ($delStatus) echo "==>$pstatusId status deleted";
						}
					}
										
				}
*/	
?>
