<?
	require("include/include.php");
	require("lib/PurchaseOrderInventory_ajax.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$selStatus	=	"";
	$supplier_Id	=	"";
	$userId		=	$sessObj->getValue("userId");
	$fromDate	=	mysqlDateFormat(date("d/m/Y"));
	$tillDate   	=	mysqlDateFormat(date("d/m/Y"));
	$dateSelection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	 $stockItems=$purchaseOrderInventoryObj->getItems();
	
	if($p["searchMode"]!="")
	{
		if($p["searchMode"]=="S")
		{
			$chkSupplier="checked";
			$chkItem="";
		}
		else
		{
			$chkItem="checked";
			$chkSupplier="";
		}
	}





	$disIt="";
	 if ($p["itemSelect"]!="")
	 {
		$disIt=1;
		$stockid=$p["itemSelect"];
		$p["stockItem"] = "";
		$p["supplierSelect"]="";
		$selSupplierId="";
		 $supplierStockRecords	= $purchaseOrderInventoryObj->fetchAllSuppliers($supplierFilterId, $supplierRateListFilterId,$stockid);
		
	 }
	
	if ($g["stockItem"]!="") {
		$poItem = $g["stockItem"];
	} else {
		$poItem = $p["stockItem"];
	}
	
	# Add Purchase Order Start
	if ($p["cmdAddNew"]!="" || $poItem!="") {
		$addMode	=	true;
	}
	
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;
		$poItem 	= "";
		$p["stockItem"] = "";
		$p["supplierSelect"]="";
		$selSupplierId="";
	}

	if ($p["selSupplier"]!="") $selSupplierId = $p["selSupplier"];
	$poNumber	=	$p["poNumber"];

	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") {
		$hidEditId = $p["editId"];
	} else {
		$hidEditId = $p["hidEditId"];
	}

	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {
		$selSupplierId 	= "";
		$poNumber	= "";
		$poItem		= "";
		//$hidEditId 	= "";
	}
	// end

	# Auto Id generation enabled or disabled
	$genPoId = $idManagerObj->check("PO");
	$unitRecords=$plantandunitObj->fetchAllRecordsPlantsActive();
	list($alphaCode,$compId)=$billingCompanyObj->getDefaultAlIdBillingCompany();
	
	
	
	if ($p["cmdAdd"]!="") {
		$poItem = $p["stockItem"];		
		/*if ($genPoId==1) {
			list($isMaxId,$purchaseOrderNo)	= $idManagerObj->generateNumberByType("PO"); 
			
			$warning="";
			$chkPOId = $purchaseOrderInventoryObj->checkPONumberExist($purchaseOrderNo);
		} else {
			$purchaseOrderNo = $p["textfield"];
			$isMax = $idManagerObj->checkMaxId("PO",$purchaseOrderNo);			
			if( $isMax=="Y"){
			
			$warning="";
		}			
		}*/
		$purchaseOrderNo = $p["textfield"];
		$itemCount	= $p["hidTableRowCount"];
		$selSupplierId	= $p["selSupplier"];
		$poNumber	= $p["poNumber"];
		$hidSupplierRateListId = $p["hidSupplierRateListId"];
		$remarks=$p["remarks"];
		$totalQuantity=$p["totalQuantity"];
		$delivarydate=mysqlDateFormat($p["delivarydate"]);
		$deliveredto=$p["unitid"];
		$vat=$p["vat"];
		$transport=$p["transport"];
		$excise=$p["excise"];
		$factory=$p["factory"];
		$bearer=$p["bearer"];
		$unitpo=$p["unitpo"];

		//if ($purchaseOrderNo!="" && !$chkPOId) {
		if ($purchaseOrderNo!=""){
			//if ($selSupplierId!="" && $poItem=="") {
				$purchaseOrderRecIns	=	$purchaseOrderInventoryObj->addPurchaseOrder($purchaseOrderNo, $poNumber, $selSupplierId, $userId, $hidSupplierRateListId,$compId,$remarks,$totalQuantity,$delivarydate,$deliveredto,$vat,$transport,$excise,$factory,$bearer,$unitpo);
				
				$lastId = $databaseConnect->getLastInsertedId();
			//}

			$supplier = array();
			$selSupplier = "";
			for ($i=0; $i<$itemCount; $i++) {
				$status = $p["Status_".$i];
				if( $status != 'N' )
				{
				$hidSupplierCount = $p["hidSupplierCount_".$i];
				$stockId	=	$p["selStock_".$i];
				$unitPrice	=	trim($p["unitPrice_".$i]);
				
				$quantity	=	trim($p["quantity_".$i]);
				$totalQty	=	$p["total_".$i];
				$proddesc=$p["proddesc_".$i];
				$printoutdesc=$p["printdesc_".$i];
				$notover=$p["notover_".$i];
				$plantUnitId=$p["selPlant_".$i];

				for ($j=1;$j<=$hidSupplierCount;$j++) {
					$selSupplier = $p["selSupplier_".$j."_".$i];
				        if ($selSupplier!="" && $quantity!="") {
						$negoPrice = $p["negoPrice_".$j."_".$i];
						
						$supplier[$selSupplier][$stockId] = array($negoPrice, $quantity, $totalQty);
				      	}			
			    	}
				
				if ($lastId!="" && $stockId!="" && $unitPrice!="" && $quantity!="" && $poItem=="") {
					$purchaseItemsIns	=	$purchaseOrderInventoryObj->addPurchaseEntries($lastId, $stockId, $unitPrice, $quantity, $totalQty,$proddesc,$printoutdesc,$notover,$plantUnitId);
				}
			   }
			}
			
			// Create PO From Stock Report
			if ($poItem!="") {

				foreach ($supplier as $supplierId=>$stockArray) {
					/*
					#Max Value of PO
					$maxValuePORec	= $purchaseOrderInventoryObj-> maxValuePO();
					$maxValue	= $maxValuePORec[0];
					$purchaseOrderNo	= $maxValue +1;
					*/
					# Generate PO
					list($isMaxId,$purchaseOrderNo)	= $idManagerObj->generateNumberByType("PO");

					$selRateListId = $supplierRateListObj->latestRateList($supplierId);
					$purchaseOrderRecIns =	$purchaseOrderInventoryObj->addPurchaseOrder($purchaseOrderNo, $poNumber, $supplierId, $userId, $selRateListId,$remarks,$totalQuantity,$delivarydate,$delivarydate,$deliveredto,$vat,$transport,$excise,$factory,$bearer,$unitpo);

					$lastId = $databaseConnect->getLastInsertedId();
				
					foreach ($stockArray as $stockId=>$item)
					{
						$unitPrice = $item[0];
						$quantity  = $item[1];
						$totalQty  = $item[2];
						if ($quantity) {
							$purchaseItemsIns	=	$purchaseOrderInventoryObj->addPurchaseEntries($lastId, $stockId, $unitPrice, $quantity, $totalQty,$proddesc,$printoutdesc,$notover,$plantUnitId);
						}
						
					}
				}
			}
				
		}

		if ($purchaseOrderRecIns) {
			if ($warning !="") {
		?>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				alert("<?=$warning;?>");
			//-->
			</SCRIPT>
		<?
			}
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddPurchaseOrderInventory);
			$sessObj->createSession("nextPage",$url_afterAddPurchaseOrderInventory.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddPurchaseOrderInventory;
		}
		$purchaseOrderRecIns		=	false;
		$hidEditId 	= "";
	}
	
	
	# Edit Purchase Order
	
	if ($p["editId"]!="" ) {
		$editId			= $p["editId"];
		$editMode		= true;
		$purchaseOrderRec	= $purchaseOrderInventoryObj->find($editId);		
		$editPurchaseOrderId	= $purchaseOrderRec[0];		
		$editPO			= $purchaseOrderRec[1];		
		$poNumber		= $purchaseOrderRec[2];
		$supplierRateListId	= $purchaseOrderRec[7];	
		$netRemarks=$purchaseOrderRec[8];
		$totalAmount=$purchaseOrderRec[9];	
		$delivarydate=dateformat($purchaseOrderRec[10]);
		$deliveredto=$purchaseOrderRec[11];
		$vat=$purchaseOrderRec[14];
		$transport=$purchaseOrderRec[12];
		$excise=$purchaseOrderRec[13];
		$factory=$purchaseOrderRec[15];
		$bearer=$purchaseOrderRec[16];
		$unitInv=$purchaseOrderRec[17];
	//	echo "The unit value is $unitInv;";
		if ($p["editSelectionChange"]=='1' || $p["selSupplier"]=="") {
			$selSupplierId		=	$purchaseOrderRec[3];
		} else {
			$selSupplierId		=	$p["selSupplier"];
		}
		$hidSelSupplierId	= $purchaseOrderRec[3];				
		
	}


	#Update Record
	if (($p["cmdSaveChange"]!="" ) || ($p["cmdConfirmSave"]!="")){
		
		if ($p["cmdConfirmSave"]!="")
		{
			$cmdConfirmPurchaseOrderStatus=1;
		}
		$purchaseOrderId	=	$p["hidPurchaseOrderId"];
		$itemCount	=	$p["hidTableRowCount"];		
		$selSupplierId	=	$p["selSupplier"];		
		$delivarydate=mysqlDateFormat($p["delivarydate"]);
		$deliveredto=$p["unitid"];
		$vat=$p["vat"];
		$transport=$p["transport"];
		$excise=$p["excise"];
		$remarks=$p["remarks"];
		$totalQuantity=$p["totalQuantity"];
		//$unitpo=$p["unitpo"];
		$inventoryno=$p["inventoryno"];
		$factory=$p["factory"];
		$bearer=$p["bearer"];
		//echo "the value of unit is $inventoryno";
		
		if ($purchaseOrderId!="" && $selSupplierId!="" ) {
			$purchaseOrderRecUptd	=	$purchaseOrderInventoryObj->updatePurchaseOrder($purchaseOrderId, $poNumber, $selSupplierId,$remarks,$totalQuantity,$delivarydate,$deliveredto,$vat,$transport,$excise,$cmdConfirmPurchaseOrderStatus,$factory,$bearer,$inventoryno);

			//updatePurchaseOrder($purchaseOrderId, $poNumber, $selSupplierId,$remarks,$totalQuantity,$delivarydate,$deliveredto,$vat,$transport,$excise,$cmdConfirmPurchaseOrderStatus,$factory,$bearer,$unitpo)
		
			#Delete First all records from purchase order entry table
			$deleteStockItemRecs	=	$purchaseOrderInventoryObj->deletePurchaseOrderItemRecs($purchaseOrderId);
			if ($deleteStockItemRecs){

				
			for ($i=0; $i<$itemCount; $i++) {
				$status = $p["Status_".$i];
				if( $status != 'N' )
				{
				$stockId		=	$p["selStock_".$i];
				$unitPrice		=	trim($p["unitPrice_".$i]);
				$quantity		=	trim($p["quantity_".$i]);
				$totalQty		=	$p["total_".$i];
				$proddesc=$p["proddesc_".$i];
				$printoutdesc=$p["printdesc_".$i];
				$notover=$p["notover_".$i];
				$plantUnitId=$p["selPlant_".$i];
		
				if ($purchaseOrderId!="" && $stockId!="" && $unitPrice!="" && $quantity!="") {
					$purchaseItemsIns	=	$purchaseOrderInventoryObj->addPurchaseEntries($purchaseOrderId, $stockId, $unitPrice, $quantity, $totalQty,$proddesc,$printoutdesc,$notover,$plantUnitId);
				}
			    }

			}


			}
		}
	
		if ($purchaseOrderRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPurchaseOrderInventoryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePurchaseOrderInventory.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPurchaseOrderInventoryUpdate;
		}
		$purchaseOrderRecUptd	=	false;
	}
	

	# Delete Purchase Order	
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$purchaseOrderId = $p["delId_".$i];
			$status  	 = $p["recStatus_".$i];

			if ($purchaseOrderId!="" && $status!='R' && $poStatus!='PC') {				
				$deleteStockItemRecs =	$purchaseOrderInventoryObj->deletePurchaseOrderItemRecs($purchaseOrderId);
				$purchaseOrderRecDel =	$purchaseOrderInventoryObj->deletePurchaseOrder($purchaseOrderId);
			}
		}
		if ($purchaseOrderRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPurchaseOrderInventory);
			$sessObj->createSession("nextPage",$url_afterDelPurchaseOrderInventory.$dateSelection);
		} else {
			$errDel	=	$msg_failDelPurchaseOrderInventory;
		}
		$purchaseOrderRecDel	=	false;
	}
$stockRecords=$purchaseOrderInventoryObj->getitemSupplierStockRecords($supplierId, $poItem, $supplierRateListId);

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
		
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	#List all Purchase Order
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate	= mysqlDateFormat($dateFrom);
		$tillDate	= mysqlDateFormat($dateTill);
		
		$purchaseOrderRecords = $purchaseOrderInventoryObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$purchaseOrderSize = sizeof($purchaseOrderRecords);

		// pagination
		$fetchAllPurchaseOrderRecs = $purchaseOrderInventoryObj->fetchAllRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows = sizeof($fetchAllPurchaseOrderRecs);
	$maxpage = ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	#Get Not completed PO for Printing
	$purchaseOrderPendingRecords = $purchaseOrderInventoryObj->getPORecords();
	

	#List all Stock Based on Supplier Id
	if ($addMode==true || $poItem) {
		#Supplier Rate List
		//$supplierRateListId = $supplierRateListObj->latestRateList();
		
		# List all Stock records based on Supplier Id
	//$stockRecords = $purchaseOrderInventoryObj->fetchSupplierStocks($selSupplierId, $poItem, $supplierRateListId);			
	}

	# List all Supplier
		//$supplierRecords = $supplierMasterObj->fetchAllRecords("INV");	
		# List all Supplier
		$supplierRecords = $supplierMasterObj->fetchAllRecordsActivesupplier("INV");	
	// Setting the mode
	if ($addMode || $poItem) $mode = 1;
	else if ($editMode) $mode = 0;
	else $mode = "";	
	

	if ($editPurchaseOrderId!="" || $poItem!="") {
		
			$purchaseRecs = $purchaseOrderInventoryObj->fetchAllStockItem($editPurchaseOrderId, $poItem);
	}
	

	//$selectSupplierId=$p["selectSupplier"];
	//echo $p["selectSupplier"];
	if ($p["supplierSelect"]!="")
	{
		$addMode=true;
		$selectSupplierId=$p["supplierSelect"];
		$fssaiRegNo = $purchaseOrderInventoryObj->getFssaiRegno($selectSupplierId);
		$serviceTaxNo = $purchaseOrderInventoryObj->getServiceTaxNo($selectSupplierId);
		$vatNo=$purchaseOrderInventoryObj->getVatNo($selectSupplierId);
		$cstNo=$purchaseOrderInventoryObj->getCstNo($selectSupplierId);
		$panNo=$purchaseOrderInventoryObj->getPanNo($selectSupplierId);
		$supplierRateListId		= $supplierRateListObj->latestRateList($selectSupplierId);
		$dataPlant=$purchaseOrderInventoryObj->fetchAllRecordsPlantsStockActive();
		$data = $purchaseOrderInventoryObj->getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitid);
		$dataLoad = $purchaseOrderInventoryObj->fetchSelectedSupplierStockRecords($selSupplierId,$poItem, $supplierRateListId);
	}
	else if($p["selectSupplier"]!="")
	{	
		$addMode=true;
		$selectSupplierId=$p["selectSupplier"];
		$fssaiRegNo = $purchaseOrderInventoryObj->getFssaiRegno($selectSupplierId);
		$serviceTaxNo = $purchaseOrderInventoryObj->getServiceTaxNo($selectSupplierId);
		$vatNo=$purchaseOrderInventoryObj->getVatNo($selectSupplierId);
		$cstNo=$purchaseOrderInventoryObj->getCstNo($selectSupplierId);
		$panNo=$purchaseOrderInventoryObj->getPanNo($selectSupplierId);
		$supplierRateListId		= $supplierRateListObj->latestRateList($selectSupplierId);
		$dataPlant=$purchaseOrderInventoryObj->fetchAllRecordsPlantsStockActive();
		$data = $purchaseOrderInventoryObj->getStockUnitDetails($selectSupplierId,$supplierRateListId,$plantUnitid);
		$dataLoad = $purchaseOrderInventoryObj->fetchSelectedSupplierStockRecords($selSupplierId,$poItem, $supplierRateListId);
	}
	//if ($selectSupplierId!=""){
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/PurchaseOrderInventory.js"; // For Printing JS in Head SCRIPT section	

	if ($editMode) $heading	= $label_editPurchaseOrderInventory;
	else $heading = $label_addPurchaseOrderInventory;
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
<form name="frmPurchaseOrderInventory" action="PurchaseOrderInventory.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrderInventory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);">
												<input type="submit" name="cmdConfirmSave" id="cmdConfirmSave" class="button" value=" Confirm Purchase Order " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);">	</td>
												<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrderInventory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);"> &nbsp;&nbsp;												</td>
												<?}?>
											</tr>
											<input type="hidden" name="hidPurchaseOrderId" value="<?=$editPurchaseOrderId;?>">
											<tr>
												<td class="fieldName" nowrap >&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td >
													<table cellpadding="1"  width="80%" cellspacing="2" border="0" align="center" bgcolor="#e8edff" style="border:1px #999999 solid; border-radius: 5px;">
														<tr>
															<td class="fieldName" align='right' >Select Unit:&nbsp;</td> 
															<td class="listing-item">
																<select onchange="xajax_getPONumber('<?=$selDate?>','<?=$compId?>',document.getElementById('unitpo').value);" id="unitpo" name="unitpo" <?php if ($editMode){ ?> disabled="true" <?php }?>>
																	<option value=''>--Select--</option>
																	<?php
																		foreach ($unitRecords as $unitd) {
																			$unitId1 		= $unitd[0];
																			$unitName1	= $unitd[2];
																			$selectedunitType1 = ($unitInv==$unitId1)?"selected":"";
																			
																	?>
																	<option value='<?=$unitId1?>'<?=$selectedunitType1?>><?=$unitName1?></option>
																	<?php
																		}
																	?>
																</select>
															</td>
															<td width="10%">&nbsp;</td>
															<td class="fieldName" align='right'>*Supplier:</td>
															<td class="listing-item">	 
																<select name="selSupplier" id="selSupplier" onchange="getOtherSupplierStockRecords('selStock_',document.getElementById('selSupplier').value,'<?=$poItem?>','<?=$supplierRateListId?>',document.getElementById('hidTableRowCount').value,'hidSelStock_','<?=$mode?>');" >
																	<option value="">--select--</option>
																	<? foreach($supplierRecords as $sr)
																		{
																			$supplierId	=	$sr[0];
																			$supplierCode	=	stripSlash($sr[1]);
																			$supplierName	=	stripSlash($sr[2]);
																			$selected ="";
																			//if ($selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
																			if ($selectSupplierId==$supplierId || $selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
																		?>
																	<option value="<?=$supplierId?>" <?=$selected;?>>
																	<?=$supplierName?>
																	</option>
																<? }?>
																</select>
															</td>
															<td nowrap width="10%">&nbsp;</td>
															<td  align='right'  class="fieldName" >*FSSAIRegnNo:</td>
															<td class="listing-item" id="fssaiRegnoId">	<?=$fssaiRegNo;?> </td>
															<td nowrap width="10%">&nbsp;</td>
															<!--</tr>
															<tr id="supRows2" >-->
															
															
															
														</tr>
														<? if (!$poItem) {?>
														<tr id="supRows1" >
															<td class="fieldName" align='right' ><?//=$unitInv?> PO ID:&nbsp;</td>
															<td class="listing-item">
																<?
																if ($editId)
																{ 
																	$valdispurchaseOrderNo=$editPO;	
																}
																else 
																{
																	$valdispurchaseOrderNo=$dispurchaseOrderNo;
																}
																?>
																<?php $styleDisplay = "border:none;";?>
																<input name="textfield" id="textfield" type="text" size="6" value="<?=$valdispurchaseOrderNo;?>" readonly  <? if($genPoId!=0 || $editPO){ ?> style="border:none" readonly <?}?>   onKeyUp="xajax_chkPONumberExist(document.getElementById('textfield').value, '<?=$mode?>');" value="<? if($editPO) { echo  $editPO;} else if($genPoId==1) { echo "New"; }else { echo $p["textfield"]; }?>">
																<div id="divPOIdExistTxt" style='line-height:normal; font-size:10px; color:red;'><?=$PurchaseOrderMsg;?></div>
															</td>
															<td width="10%">&nbsp;</td>	
															<td class="fieldName" align='right' >*ServicetaxNo:</td>
															<td class="listing-item" id="serviceTaxId"><?=$serviceTaxNo;?></td>	 
															<td width="10%">&nbsp;</td>
															<td class="fieldName" align='right'>*VAT No:</td>
															<td class="listing-item" id="vatNoId">	<?=$vatNo;?> </td>
															<td width="10%">&nbsp;</td>
														</tr>
														<tr id="supRows4" >
															<td class="fieldName" align='right'>*CST No:</td>
															<td class="listing-item" id="cstNoId"><?=$cstNo;?></td>	 
															<td width="10%">&nbsp;</td>
															<!--</tr>
															<tr id="supRows5" >-->
															<td class="fieldName" align='right'>*PAN No:</td>
															<td class="listing-item" id="panNoId">	<?=$panNo;?></td>
															<td width="10%">&nbsp;</td>
														</tr>
														

														<?} ?>
													</table>
												</td>
													
											</tr>
											<tr>
												<td height="10"></td>
											</tr>

											<!--<tr>
												<td colspan="2" nowrap class="fieldName" >
													<table  border=0>
														<tr> 
															<td class="fieldName" align='right'>Select Unit:&nbsp;</td> 
															<td class="listing-item">
																<select onchange="xajax_getPONumber('<?=$selDate?>','<?=$compId?>',document.getElementById('unitpo').value);" id="unitpo" name="unitpo" <?php if ($editMode){ ?> disabled="true" <?php }?>>
																	<option value=''>--Select--</option>
																	<?php
																		foreach ($unitRecords as $unitd) {
																			$unitId1 		= $unitd[0];
																			$unitName1	= $unitd[2];
																			$selectedunitType1 = ($unitInv==$unitId1)?"selected":"";
																			
																	?>
																	<option value='<?=$unitId1?>'<?=$selectedunitType1?>><?=$unitName1?></option>
																	<?php
																		}
																	?>
																</select>
															</td>
														</tr>
														<tr>
															<td class="fieldName" align='right'><?//=$unitInv?> PO ID:&nbsp;</td>
															<td class="listing-item">
																<?
																if ($editId)
																{ 
																	$valdispurchaseOrderNo=$editPO;	
																}
																else 
																{
																	$valdispurchaseOrderNo=$dispurchaseOrderNo;
																}
																?>
																<?php $styleDisplay = "border:none;";?>
																<input name="textfield" id="textfield" type="text" size="6" value="<?=$valdispurchaseOrderNo;?>" readonly  <? if($genPoId!=0 || $editPO){ ?> style="border:none" readonly <?}?>   onKeyUp="xajax_chkPONumberExist(document.getElementById('textfield').value, '<?=$mode?>');" value="<? if($editPO) { echo  $editPO;} else if($genPoId==1) { echo "New"; }else { echo $p["textfield"]; }?>">
															</td>
															<td nowrap>
																<div id="divPOIdExistTxt" style='line-height:normal; font-size:10px; color:red;'><?=$PurchaseOrderMsg;?></div>
															</td>
														</tr>
														<? if (!$poItem) {?>
                                                        <tr>
															<td class="fieldName" align='right'>*Supplier:</td>
															<td class="listing-item">	 
																<select name="selSupplier" id="selSupplier" onchange="getOtherSupplierStockRecords('selStock_',document.getElementById('selSupplier').value,'<?=$poItem?>','<?=$supplierRateListId?>',document.getElementById('hidTableRowCount').value,'hidSelStock_','<?=$mode?>');" >
																	<option value="">--select--</option>
																	<? foreach($supplierRecords as $sr)
																		{
																			$supplierId	=	$sr[0];
																			$supplierCode	=	stripSlash($sr[1]);
																			$supplierName	=	stripSlash($sr[2]);
																			$selected ="";
																			//if ($selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
																			if ($selectSupplierId==$supplierId || $selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
																		?>
																	<option value="<?=$supplierId?>" <?=$selected;?>>
																	<?=$supplierName?>
																	</option>
																<? }?>
															</select>
														</td>
														<td>&nbsp;</td>
													</tr>
													<? }?>
												</table>
											</td>
										</tr>	
										<tr>
											<td>
												<table width="200" border=0>
												 <tr id="supRows1" >
				
                <td  align='right'  class="fieldName" >*FSSAIRegnNo:</td>
                <td class="listing-item" id="fssaiRegnoId">	<?=$fssaiRegNo;?> </td>
	<td nowrap>&nbsp;</td>
	
	
                                                </tr>

												<tr id="supRows2" >
 
                <td class="fieldName">*ServicetaxNo:</td>
                <td class="listing-item" id="serviceTaxId"><?=$serviceTaxNo;?></td>	 
	<td>&nbsp;</td>
	
	</tr>
	<tr id="supRows3" >
	
                <td class="fieldName" align='right'>*VAT No:</td>
                <td class="listing-item" id="vatNoId">	<?=$vatNo;?> </td>
	<td>&nbsp;</td>
	
	</tr>
	<tr id="supRows4" >
	 
                <td class="fieldName" align='right'>*CST No:</td>
                <td class="listing-item" id="cstNoId"><?=$cstNo;?></td>	 
	<td>&nbsp;</td>
	
	</tr>
	<tr id="supRows5" >
	
                <td class="fieldName" align='right'>*PAN No:</td>
                <td class="listing-item" id="panNoId">	<?=$panNo;?></td>
	<td>&nbsp;</td>
	
	</tr>
												</table>
											</td>
										</tr>-->
										<tr>
											<td colspan="2" nowrap></td>
										</tr>										
										<!--  Dynamic Row Adding Starts Here-->
										<tr>
											<TD>
												<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddStockItem">
													<tr bgcolor="#f2f2f2" align="center">
														<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="23" >Unit</td>
														<td width="44" class="listing-head" style="padding-left:5px; padding-right:5px;">Item</td>
														<? if (!$poItem) {?>
																<td width="173" nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Unit Price </td>
														<? }?>
																<td width="35" class="listing-head" style="padding-left:5px; padding-right:5px;">Quantity</td>
																<td width="52" class="listing-head" style="padding-left:5px; padding-right:5px;">Total</td>
														<td width="47" class="listing-head" style="padding-left:5px; padding-right:5px;">Qty in Stock</td>		
														<td width="152" class="listing-head" style="padding-left:5px; padding-right:5px;">Other<br>Suppliers</td>
														<td width="235" class="listing-head" style="padding-left:5px; padding-right:5px;">Last Purchase Supplier,Qty,Price</td>
														<td width="170" class="listing-head" style="padding-left:5px; padding-right:5px;">Not Over</td>
														<td width="87" class="listing-head" style="padding-left:5px; padding-right:5px;">Description</td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="54" >Descp in Print Out</td>
														<td width="8" class="listing-head" id="headRemoveTd" style="padding-left:5px; padding-right:5px;"></td>
													</tr>
													 <tr bgcolor="#FFFFFF" align="center">
													   <? if (!$poItem) $colspan=1;
													   else $colspan = 1;
													?>
														<td class="fieldName"><span class="listing-head">Remarks</span></td>
														<td  class="fieldName" class="listing-head" align="right">
															<textarea name="remarks" ><?=$netRemarks;?>
															 </textarea>
														</td>
														<td  class="listing-head" align="right">
															<p class="listing-head">Above rates are inclusive of<p>Transport		  
															 <input type="hidden" name="inventoryno" value=<?php echo $unitInv;?> />
															  <input type="checkbox" name="transport" id="transport" value=1 <?php if ($transport==1){?> checked="true" <?php }?>/>
															   Excise
															   <input type="checkbox" name="excise" id="excise" value=1 <?php if ($excise==1){?> checked="true" <?php }?>/>Vat
															   <input type="checkbox" name="vat" id="vat" value=1  <?php if ($vat==1){?> checked="true" <?php }?>/>
															 </p>
														</td>
														<td colspan="<?=$colspan?>" class="listing-head" align="right">Total:</td>
														<td class="fieldName"><input name="totalQuantity" type="text" id="totalQuantity" size="8" style="text-align:right" readonly value="<?=$totalAmount;?>"></td>			
													    <td class="listing-head" align="right" colspan="3" >Delivery At our factory at M-53,MIDC,Taloja <input type="checkbox" name="factory" id="factory" value=1 <?php if ($factory==1){?> checked="true" <?php }?>/>(OR) To Bearer of this PO<input type="checkbox" name="bearer" id="bearer" value=1 <?php if ($bearer==1){?> checked="true" <?php }?>/></td>
													    <td class="listing-head">&nbsp;Delivary Date&nbsp;<input type="text" name="delivarydate" id="delivarydate" value="<?php echo $delivarydate;?>" /></td>
														<td class="listing-head">&nbsp;Delivered To
															<select onchange="getUnitAlphacode();" id="unitid" name="unitid">
																<option value=''>--Select--</option>
																<?php
																	foreach ($unitRecords as $unitd) {
																		$unitId 		= $unitd[0];
																		$unitName	= $unitd[2];
																		$selectedunitType = ($deliveredto==$unitId)?"selected":"";
																		
																?>
																	<option value='<?=$unitId?>'<?=$selectedunitType?>><?=$unitName?></option>
																<?php
																	}
																?>
															</select>
														</td>
														<td>&nbsp;</td>
														<td id="footerRemoveTd">&nbsp;</td>
													</tr>
												</table>
											</TD>
										</tr>
										<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize;?>">
										<SCRIPT LANGUAGE="JavaScript">
											<!--
												//setfieldId(<?=$rowSize;?>)
											//-->
										</SCRIPT>
										<tr>
											<TD>
												<a href="###" id='addRow' onclick="javascript:addNewStockItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
											</TD>
										</tr>
										<tr>
											<td colspan="2" nowrap class="fieldName" >&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>
											<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrderInventory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);">
												<input type="submit" name="cmdConfirmSave" id="cmdConfirmSave" class="button" value=" Confirm Purchase Order " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);">
											</td>
											<?} else{?>
											<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseOrderInventory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validatePurchaseOrderInventory(document.frmPurchaseOrderInventory);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
									</table>									
								</td>
							</tr>
						</table>						
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?
	}
	?>	
	<tr>
		<td height="10" align="center" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Order  </td>
								<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td nowrap="nowrap">
												<table cellpadding="0" cellspacing="0">
                      								<tr>
					  									<td class="listing-item"> From:</td>
                                    					<td nowrap="nowrap"> 
														<? 
														if ($dateFrom=="") $dateFrom=date("d/m/Y");
														?>
														<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>">
														</td>
														<td class="listing-item">&nbsp;</td>
														<td class="listing-item"> Till:</td>
														<td> 
														<? 
														if($dateTill=="") $dateTill=date("d/m/Y");
														?>
														<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
														<td class="listing-item">&nbsp;</td>
														<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
														<td class="listing-item" nowrap >&nbsp;</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							<tr>
								 <td colspan="3" align="right" style="padding-right:10px;">
									<table width="200" border="0">
										<tr>
											<td>
												<fieldset>
													<legend class="listing-item">Print PO</legend>
													<table width="200" cellpadding="0" cellspacing="0" bgcolor="#999999">
														<tr bgcolor="#FFFFFF">
															<td class="listing-item" nowrap="nowrap" height="25">PO No: </td>
															<td nowrap="nowrap">
															<? //if($selPOId=="") echo $disabled="disabled"; ?>
															<? $selPOId=$p["selPOId"];?>&nbsp;
															<select name="selPOId" id="selPOId" onchange="disablePrintPOButton();">
																<option value="">-- Select --</option>
																<?
																foreach($purchaseOrderPendingRecords as $por)
																	{
																		$poId	=	$por[0];
																		$poGenerateId = 	$por[1];
																		$selected="";
																		if($selPOId==$poId) $selected="Selected";
																?>
																<option value="<?=$poId?>" <?=$selected?>><?=$poGenerateId?></option>
																<? }?>
															</select>
															</td>
															<? if($print==true){?>
															<td nowrap="nowrap">&nbsp;<input name="cmdPrintPO" type="button" class="button" id="cmdPrintPO" onClick="return printPurchaseOrderWindow('PrintPOInventory.php',700,600);" value="Print PO"  ></td>
															<td>&nbsp;</td>
															<? }?>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
									</table>
								</td> 
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							<tr>
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td>
												<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button" onclick="return validatePurchaseOrder(document.frmPurchaseOrderInventory);"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPurchaseOrderInventory.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>&os=<?=$offset;?>&lt=<?=$limit;?>',700,600);"><? }?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<?
							if($errDel!="")
							{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="20">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
								<td class="listing-head"></td>
								<td class="listing-head"></td>
							</tr>
							<tr class="listing-head" >
								<td colspan="5">
									<table cellpadding="0" cellspacing="0" align="center" border=0>
										<tr class="listing-head">
											<td align="center" colspan=3><input name="searchMode" id="searchMode1" type="radio" value="S"  <?=$quickSearch?> class="chkBox" onclick="showSupplierList();" >Supplier<input name="searchMode" id="searchMode1" type="radio" value="I"  <?=$quickSearch?> class="chkBox"   onclick="showItemList()" <?php
												if ($p["itemSelect"]!=""){?> checked="true" <?php }?> >Item
											</td>
										</tr>
										<tr class="listing-head" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;">
											<td align="center">&nbsp;
												<div id="showSp" style="display:none" nowrap>Supplier 
													<select name="supplierSelect"  onChange="supplierLoad(this)" >
														<option value="">--Select--</option>
														 <?
														foreach($supplierRecords as $sr)
														{
															$supplierId	=	$sr[0];
															$supplierCode	=	stripSlash($sr[1]);
															$supplierName	=	stripSlash($sr[2]);
															$selected ="";
															//if ($selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
															if ($selectSupplierId==$supplierId || $selSupplierId==$supplierId || $editSupplierId	== $supplierId) $selected="selected";
														?>
														<option value="<?=$supplierId?>" <?=$selected;?>>
														<?=$supplierName?>
														</option>
														<?php }?>
														</select>
												</div>
												<?php if ($disIt==1) {?>
												<div id="showIt"  style="display:block"  ><?php } else {?>
													<div id="showIt"  style="display:none"  > <?php }?>
													Select Item <select name="itemSelect" onchange="itemLoad(this);" ><option value="">Select</option>
													<?php foreach($stockItems as $si){?>
													<option value="<?=$si[2];?>"  <?php if ($p["itemSelect"]==$si[2]) {?> selected <?php }?>  ><?=$si[1];?></option>

													<?php }?>
												</select>
												</div>
											</td>
										</tr>
										<tr>
											<td width="20">&nbsp;</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
											<td class="listing-head"></td>
											<td class="listing-head"></td>
										</tr>
										<?php
										if ($p["itemSelect"]!=""){?>
										<tr>
											<td><?php if ($disIt==1) {?>
											<div id="showItdetails"  style="display:block"  ><?php } else {?><div id="showItdetails"  style="display:none"  > <?php }?>
											<table width="80%" cellspacing="1" cellpadding="2" border="0" bgcolor="#999999" align="center" >
											<tr bgcolor="#f2f2f2">
											
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" width=200>Supplier</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;"  width=200>Stock</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" width=200>Negoti.Price</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" width=200>Select</td>
										</tr>
										<?php foreach ($supplierStockRecords as $ssr) {
											$supplierName = stripslashes($ssr[12]);
											$stockName		= stripslashes($ssr[13]);
											$negotiatedPrice	= $ssr[4];
											$supplierId		= $ssr[1];
											?>
										<tr bgcolor="White" >
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><?=$supplierName;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><?=$stockName;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><?=$negotiatedPrice;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" width=200><input type="radio" name="selectSupplier" id="selectSupplier" value="<?=$supplierId?>" class="chkBox fsaChkbx" />
											</td>
										</tr>
									<?php }?>
									</table>
								</div>
							</td>
						</tr>
					<?php }?>
				</table>
			</td>
		</tr>
		<tr   align="center">
			<td width="20">&nbsp;</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
			<td class="listing-head"></td>
			<td class="listing-head"></td>
		</tr>
		<tr>
			<td width="1" ></td>
			<td colspan="2" >
				<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
				<?
				if ( sizeof($purchaseOrderRecords) > 0) {
					$i	=	0;
				?>
				<? if($maxpage>1){?>
					<tr  bgcolor="#f2f2f2" align="center">
						<td colspan="7" bgcolor="#FFFFFF" style="padding-right:10px;">
							<div align="right">
							<?php 				 			  
							$nav  = '';
							for ($page=1; $page<=$maxpage; $page++) {
								if ($page==$pageNo) {
										$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
								} else {
										$nav.= " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
									//echo $nav;
								}
							}
							if ($pageNo > 1) {
								$page  = $pageNo - 1;
								$prev  = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
							} else {
								$prev  = '&nbsp;'; // we're on page one, don't print previous link
								$first = '&nbsp;'; // nor the first page link
							}

							if ($pageNo < $maxpage) {
								$page = $pageNo + 1;
								$next = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
							} else {
								$next = '&nbsp;'; // we're on the last page, don't print next link
								$last = '&nbsp;'; // nor the last page link
							}
							// print the navigation link
							$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
							echo $first . $prev . $nav . $next . $last . $summary; 
						  ?>
						</div>
					</td>
				</tr>
			<? }?>
				<tr  bgcolor="#f2f2f2" align="center">
					<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO ID</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">Remarks</td>
					<td class="listing-head"></td>
					<? if($edit==true){?>
					<td class="listing-head"></td>
					<? }?>
				</tr>
				<?
				foreach ($purchaseOrderRecords as $por) {
					$i++;
					$purchaseOrderId	= $por[0];
					$poId			= $por[1];
					$poNumber		= $por[2];				
					$supplierName		= $por[7];	
					$remarks=$por[9];
					$poinvconfirmed=$por[10];
					
					$total_amount = $purchaseOrderInventoryObj->fetchPurchaseOrderAmount($purchaseOrderId);
					
					$status		=	$por[6];
					if ($status=='C') {
						$displayStatus	=	"Cancelled";
					} else if ($status=='R') {
						$displayStatus	=	"Received";
					} else if ($status=='PC') {
						$displayStatus	=	"Partially<br>Completed";
					} else  { //($status=='P')
						$displayStatus	=	"Pending";
					}
					$disabled = "";
					if ($status=='R') $disabled = "disabled";	
					$basePOId	= $por[8];
					if ($basePOId!="") $basePONumber = $purchaseOrderInventoryObj->getPONumber($basePOId);		
					$displaySuppListName = "";
					if ($basePOId!="" && $basePONumber!="") $displaySuppListName = "(Supplementary of PO $basePONumber)";
					if ($poinvconfirmed==1){
					$disabled = "disabled";	
					}
				?>
				<tr bgcolor="White">
					<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$purchaseOrderId;?>" class="chkBox">
					<input type="hidden" name="recStatus_<?=$i?>" value="<?=$status?>">
					</td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
						<?=$poId;?><br>
						<span class="fieldName" style="line-height:normal"><?=$displaySuppListName?></span> 
					</td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
					<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$total_amount;?></td>
					<td class="listing-item" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayStatus?></td>
					<td class="listing-item" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$remarks?></td>
					<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><a href="javascript:printWindow('ViewPOInventoryDetails.php?selPOId=<?=$purchaseOrderId?>',700,600)" class="link1" title="Click here to view details">View Details</a></td>	
					<? if($edit==true && ( $status=='P' || $status=='PC' ) ){?>
					<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$purchaseOrderId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='PurchaseOrderInventory.php';" <?=$disabled?>></td>
					<? } else if ($edit==true && $status=='R') {?>
					<td></td>
					<? }?>
				</tr>
				<?
					}
				?>
				<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
				<input type="hidden" name="editId" value="">
				<input type="hidden" name="editSelectionChange" value="0">
				<? if($maxpage>1){?>
				<tr bgcolor="#FFFFFF">
         			<td colspan="7" style="padding-right:10px;">
						<div align="right">
							<?php 				 			  
							 $nav  = '';
							for ($page=1; $page<=$maxpage; $page++) {
								if ($page==$pageNo) {
										$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
								} else {
										$nav.= " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
								}
							}
							if ($pageNo > 1) {
								$page  = $pageNo - 1;
								$prev  = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
							} else {
								$prev  = '&nbsp;'; // we're on page one, don't print previous link
								$first = '&nbsp;'; // nor the first page link
							}

							if ($pageNo < $maxpage) {
								$page = $pageNo + 1;
								$next = " <a href=\"PurchaseOrderInventory.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
							} else {
								$next = '&nbsp;'; // we're on the last page, don't print next link
								$last = '&nbsp;'; // nor the last page link
							}
							// print the navigation link
							$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
							echo $first . $prev . $nav . $next . $last . $summary; 
						  ?>
						</div>
						<input type="hidden" name="pageNo" value="<?=$pageNo?>">
					</td>
				</tr>
				<? }?>
				<?
			} else {
		?>
		<tr bgcolor="white">
			<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
		</tr>
		<?
			}
		?>
	</table>
	<!--  Store the Stock report value-->
	<input type="hidden" name="stockItem" value="<?=$poItem?>"></td>
</tr>
<tr>
	<td colspan="3" height="5" ></td>
</tr>
<tr >
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td>
					<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button" onClick="return validatePurchaseOrder(document.frmPurchaseOrderInventory);"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPurchaseOrderInventory.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>&os=<?=$offset;?>&lt=<?=$limit;?>, $limit',700,600);"><? }?>
				</td>
			</tr>
		</table>									
	</td>
</tr>
<input type="hidden" name="hidSupplierRateListId" id="hidSupplierRateListId" value="<?=$supplierRateListId?>" >
<input type="hidden" name="hidEditId" value="<?=$hidEditId?>">
<tr>
	<td colspan="3" height="5" ></td>
</tr>
</table>						
</td>
</tr>
</table>
</td>
</tr>
		
<tr><td align="center">&nbsp;&nbsp;</td></tr>

<?php
if ($p["searchMode"]=="I")
{
	$selected=true;
}

?>
		<tr class="listing-head" ><td align="center">&nbsp;<!--<input name="searchMode" id="searchMode1" type="radio" value="S"  <?=$quickSearch?> class="chkBox" onclick="showSupplierList();" >Supplier-->&nbsp;<!--<input name="searchMode" id="searchMode1" type="radio" value="I"  <?=$quickSearch?> class="chkBox"   onclick="showItemList()" <?php
		if ($p["itemSelect"]!=""){?> checked="true" <?php }?> >Item--></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr class="listing-head" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;"><td align="center">&nbsp;
		</td></tr>
		<tr><td>&nbsp;</td>

	
	<?php //}?>
	</table>
	
	<!--</div>-->
	</td>
		


		

	</tr>
	
<?php //}?>


		<tr>
			<td height="10"></td>
		</tr>

	<input type='hidden' name='genPoId' id='genPoId' value="<?=$genPoId;?>" >
	</table>
	<?if ($addMode && $poItem=="") {
		$selStockId=$p["itemSelect"];
		$categorydetails=$purchaseOrderInventoryObj->getCategoryDetails($selStockId);		
		$catId=$categorydetails[0];
		$subcatId=$categorydetails[1];		
		$proddesc=$purchaseOrderInventoryObj->getPurchaseOrderDescrption($catId,$subcatId,$selStockId);
		//$minQty=$purchaseOrderInventoryObj->getStockMinOrderQty($selStockId);
		?>
	<SCRIPT LANGUAGE="JavaScript">
		function addNewStockItem() 
		{
		//xajax_showFunction();
		var hidcount=document.getElementById('hidTableRowCount').value;
		if (hidcount=="")
		{
			//alert("<?=$proddesc?>");
			xajax_getLastPurchaseStockRec('<?=$selStockId?>',document.getElementById('selSupplier').value,'<?=$poItem?>',1);
			addNewStockItemRow('tblAddStockItem', '<?=$poItem?>', '<?=$selStockId?>', '<?=$minQty?>', '', '<?=$supplierRateListId?>', '<?=$mode?>','<?=$selPlantId?>','','<?=$proddesc?>');
			//xajax_xaddNewStockItemRow('tblAddStockItem', '<?=$poItem?>', '<?=$selStockId?>', '<?=$minQty?>', '', '<?=$supplierRateListId?>', '<?=$mode?>','<?=$selPlantId?>','','<?=$proddesc?>','','','');
			//xajax_hideFunction();

				
		}
		else {
		addNewStockItemRow('tblAddStockItem', '<?=$poItem?>', '', '', '', '<?=$supplierRateListId?>', '<?=$mode?>','<?=$selPlantId?>');
		//xajax_xaddNewStockItemRow('tblAddStockItem', '<?=$poItem?>', '', '', '', '<?=$supplierRateListId?>', '<?=$mode?>','<?=$selPlantId?>','','','','','');
		}
			
			//addNewStockItemRow('tblAddStockItem', '<?=$poItem?>', '', '', '', '<?=$supplierRateListId?>', '<?=$mode?>','<?=$selPlantId?>');			
			xajax_getSupplierStockRecordsAll('selStock_',document.getElementById('selSupplier').value,'<?=$poItem?>','<?=$supplierRateListId?>',document.getElementById('hidTableRowCount').value,'hidSelStock_','<?=$mode?>');
			//xajax_hideFunction();
				
		}
	</SCRIPT>
	<? }?>
	<? if ($poItem!="" || $editMode) {?>
	<SCRIPT LANGUAGE="JavaScript">
	function addNewStockItem() 
		{
		
			//xajax_showFunction();
			addNewStockItemRow('tblAddStockItem', '<?=$poItem?>', '<?=$selStockId?>', '<?=$quantity?>', '<?=$totalAmt?>', '<?=$supplierRateListId?>', '<?=$mode?>','<?=$selPlantId?>','<?=$notover?>','<?=$printoutdescrip?>','<?=$unitprice?>','<?=$prindesc?>','<?=$stkQty?>');			
			xajax_getSupplierStockRecordsAll('selStock_',document.getElementById('selSupplier').value,'<?=$poItem?>','<?=$supplierRateListId?>',document.getElementById('hidTableRowCount').value,'hidSelStock_','<?=$mode?>');
			//xajax_hideFunction();
	}
	</SCRIPT>
	<?}?>

	<? if ($addMode && !$poItem) {?>
	<script language="JavaScript">
		window.onLoad = addNewStockItem();		
	</script>
	<? }?>
	<? 
	if(sizeof($purchaseRecs)>0)
	{
	$totalAmount = 0;	
	for ($k=0;$k<sizeof($purchaseRecs);$k++) {	
		$pr = $purchaseRecs[$k];
		$selStockId = $pr[2];
		$quantity	=	$pr[4];
		$totalAmt	=	$pr[5];
		$selPlantId=$pr[9];
		$printoutdescrip=$pr[6];
			$notover=$pr[8];
			$unitprice=$pr[3];
			$prindesc=$pr[7];

			$stkQty=$purchaseOrderInventoryObj->getBalanceQty($selStockId,$selPlantId);
		
	?>
	<script language="JavaScript">	
		
		addNewStockItemRow('tblAddStockItem', '<?=$poItem?>', '<?=$selStockId?>', '<?=$quantity?>', '<?=$totalAmt?>', '<?=$supplierRateListId?>', '<?=$mode?>','<?=$selPlantId?>','<?=$notover?>','<?=$printoutdescrip?>','<?=$unitprice?>','<?=$prindesc?>','<?=$stkQty?>');	
		xajax_showFunction();
		xajax_getSupplierStockRecordsAll('selStock_',document.getElementById('selSupplier').value,'<?=$poItem?>','<?=$supplierRateListId?>',document.getElementById('hidTableRowCount').value,'hidSelStock_','<?=$mode?>');		
		xajax_getStockBalanceQty('<?=$selStockId?>','<?=$k?>','<?=$selPlantId?>');		
		xajax_getStockUnitRate(document.getElementById('selSupplier').value,'<?=$selStockId?>','<?=$k?>','<?=$supplierRateListId?>', '<?=$mode?>');		
		xajax_getOtherSuppliersStockRec('<?=$selStockId?>','<?=$selSupplierId?>','<?=$poItem?>','<?=$k?>');	
		xajax_getLastPurchaseStockRec('<?=$selStockId?>','<?=$selSupplierId?>','<?=$poItem?>','<?=$k?>');
		
	
	</script>
	<? 
	//$k++;
	}
	}
	?>
	
<SCRIPT LANGUAGE="JavaScript">
<!--
Calendar.setup 
(	
{
inputField  : "selectFrom",         // ID of the input field
eventName	  : "click",	    // name of event
button : "selectFrom", 
ifFormat    : "%d/%m/%Y",    // the date format
singleClick : true,
step : 1
}
);
	//-->
	</SCRIPT>

	
<SCRIPT LANGUAGE="JavaScript">
<!--
Calendar.setup 
(	
{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
}
);
//-->
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
<!--
Calendar.setup 
(	
{
inputField  : "delivarydate",         // ID of the input field
eventName	  : "click",	    // name of event
button : "delivarydate", 
ifFormat    : "%d/%m/%Y",    // the date format
singleClick : true,
step : 1
}
);
	//-->
	</SCRIPT>

<script language="javascript">
$(document).ready(function(){
var $unique = $('input.fsaChkbx');
$unique.click(function() {
$unique.filter(':checked').not(this).removeAttr('checked');
});
});</script>

<?  if($addMode || $editMode) { ?>
<script>

// When no selection disable print button
disablePrintPOButton();
</script>
<? } ?>
</form>
<table width=100%>
<tr>
<td>
<?
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>