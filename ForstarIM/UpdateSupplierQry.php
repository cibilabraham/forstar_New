<?php
require("include/include.php");
require("lib/UpdateSupplierQry_class.php");
$updateSupplierQryObj	= new UpdateSupplierQry($databaseConnect);


	$updated = false;

	$upateSupplierTable = $updateSupplierQryObj->updateSupplierTable();
		
	if ($upateSupplierTable)  {
		$getOldSupplierRecs = $updateSupplierQryObj->getDailyCatchSupplierRecords();
		echo "<b>Old Supplier, Size=".sizeof($getOldSupplierRecs)."</b><br>";
		foreach ($getOldSupplierRecs as $gos) {
			$supplierOldId	= $gos[0];
			$supCode	= $gos[1];
			$supName	= $gos[2];
			$supAddress	= $gos[3];
			$supTel		= $gos[4];
			$supFax		= $gos[5];
			$supEmail	= $gos[6];
			$supPan		= $gos[7];
			$supPincode	= $gos[8];
			$supNativePlace	= $gos[9];
			$supPaymentBy	= $gos[10];
			$supCreatedBy	= $gos[11];
			$supModifiedHistory = $gos[12];
			//echo "<br>$supplierOldId,$supCode, $supName, $supAddress, $supTel, $supFax, $supEmail, $supPan, $supPincode, $supNativePlace, $supPaymentBy, $supCreatedBy, $supModifiedHistory";
			
			# Insert Supplier
			$insertNewSupplierRec = $updateSupplierQryObj->insertNewSupplierRec($supplierOldId,$supCode, $supName, $supAddress, $supTel, $supFax, $supEmail, $supPan, $supPincode, $supNativePlace, $supPaymentBy, $supCreatedBy, $supModifiedHistory);
			if ($insertNewSupplierRec) {
				echo "Supplier Inserted Id=$supplierOldId:$supName<br>";
			}

			/*if ($insertNewSupplierRec) {
				$supplierMainId = $databaseConnect->getLastInsertedId();
				if ($supplierMainId!="") {					
					# Update Supplier 2 Centre
					$updateSupplier2CenterRec = $updateSupplierQryObj->updateSupplier2CentreRec($supplierOldId, $supplierMainId);	
					# Upate Sub-Supplier Rec
					if ($updateSupplier2CenterRec) {
						$updateSubSupplierRec = $updateSupplierQryObj->updateSubSupplierRec($supplierOldId, $supplierMainId);
					}
					# Update Dailycatchentry Rec
					if ($updateSubSupplierRec) {
						$updateDailyCatchMainRec = $updateSupplierQryObj->upateDailyCatchMainRec($supplierOldId, $supplierMainId);
					}
					if ($updateDailyCatchMainRec) $updated = true;
					echo "<br>Old=$supplierOldId, New=$supplierMainId<br>";			
				}				
			}*/
		}

		/**/
		$getSupplier2CenterRec		= $updateSupplierQryObj->getSupplier2CentreRec();
		echo "<b>Supplier 2 Center, Size=".sizeof($getSupplier2CenterRec)."</b><br>";
		foreach ($getSupplier2CenterRec as $r) {
			$supplier2CenterRecId = $r[0];
			$cSupplierId = $r[1];
			$newSupplierId = $updateSupplierQryObj->getNewSupplierId($cSupplierId);
			# Update Supplier 2 Centre
			$updateSupplier2CenterRec = $updateSupplierQryObj->updateSupplier2CentreRec($supplier2CenterRecId, $newSupplierId);
			if ($updateSupplier2CenterRec) {
				echo "Old Supplier Id=$cSupplierId;New Supplier Id=$newSupplierId;Supplier2EntryId=$supplier2CenterRecId<br>";
				$updated = true;
			}
		}			
		/**/

		/**/
		$getSubSupplierRec = $updateSupplierQryObj->getSubSupplierRec();
		echo "<br><b>Sub-Supplier, Size=".sizeof($getSubSupplierRec)."</b><br>";
		foreach ($getSubSupplierRec as $sr) {
			$subSupplierEntryId = $sr[0];
			$cSupplierId = $sr[1];
			$newSupplierId = $updateSupplierQryObj->getNewSupplierId($cSupplierId);
			# Upate Sub-Supplier Rec
			$updateSubSupplierRec = $updateSupplierQryObj->updateSubSupplierRec($subSupplierEntryId, $newSupplierId);
			if ($updateSubSupplierRec) {
				echo "Old Supplier Id=$cSupplierId;New Supplier Id=$newSupplierId;SubSupplierEntry=$subSupplierEntryId<br>";
				$updated = true;
			}
		}
		/**/

		/**/
		$getDailyCatchMainRec = $updateSupplierQryObj->getDailyCatchMainRec();
		echo "<br><b>Daily Catch, Size=".sizeof($getDailyCatchMainRec)."</b><br>";
		foreach ($getDailyCatchMainRec as $gd) {
			$dailyEntryId = $gd[0];
			$cSupplierId = $gd[1];
			$newSupplierId = $updateSupplierQryObj->getNewSupplierId($cSupplierId);
			$updateDailyCatchMainRec = $updateSupplierQryObj->upateDailyCatchMainRec($dailyEntryId, $newSupplierId);
			if ($updateDailyCatchMainRec) {
				echo "Old Supplier Id=$cSupplierId;New Supplier Id=$newSupplierId;DailyEntryId=$dailyEntryId<br>";
				$updated = true;
			}	
		}
		/**/



		if ($updated) {
			$updateSupplierTable = $updateSupplierQryObj->updateSupplierOldTable();
		}
		if ($updateSupplierTable) echo " <br><b>All Record Updated</b> <br>";	
	}
	
?>
