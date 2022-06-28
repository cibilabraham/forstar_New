<?php
	require("include/include.php");
	require_once("lib/IngredientPO_ajax.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$selStatus	=	"";
	$supplier_Id	=	"";
	//$userId		=	$sessObj->getValue("userId");

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

	# Add Purchase Order Start
	if ($p["cmdAddNew"]!="") $addMode = true;		
	if ($p["cmdCancel"]!="") $addMode = false;
	
	if ($p["selSupplier"]!="") $selSupplierId = $p["selSupplier"];
	else $selSupplierId = $p["hidSelSupplierId"];

	# Auto Id generation enabled or disabled
	$genPoId = $idManagerObj->check("IPO");

	#Add a PO
	if ($p["cmdAdd"]!="") {
		/*
		#Max Value of PO
		$maxValue	= $ingredientPurchaseorderObj-> maxValuePO();
		$purchaseOrderNo = $maxValue +1;
		*/
		if ($genPoId==1) {
			list($isMaxId,$purchaseOrderNo)	= $idManagerObj->generateNumberByType("IPO"); 
			$warning  = ($isMaxId=="Y")? "The generated PO ID is greater than the ending number of Purchase Order ID." : "";
			$chkPOId = $ingredientPurchaseorderObj->checkIngPONumberExist($purchaseOrderNo);
		} else {
			$purchaseOrderNo = $p["textfield"];
			$isMax = $idManagerObj->checkMaxId("IPO",$purchaseOrderNo);			
			if( $isMax=="Y") $warning = "The generated PO ID is greater than the ending number of Purchase Order ID.";			
		}

		$itemCount	=	$p["hidTableRowCount"];
		$selSupplierId	=	$p["selSupplier"];
		$ingredientRateListId = $p["hidIngredientRateListId"];

		if ($purchaseOrderNo!="" && !$chkPOId) {
			if ($selSupplierId!="") {
				$purchaseOrderRecIns = $ingredientPurchaseorderObj->addPurchaseOrder($purchaseOrderNo, $selSupplierId, $userId, $ingredientRateListId);
				#Find the Last inserted Id From ing_purchaseorder Table
				$lastId = $databaseConnect->getLastInsertedId();
			}
			
			for ($i=0; $i<$itemCount; $i++) {
				$status = $p["status_".$i];
			  	if ($status!='N') {
					$ingredientId	=	$p["selIngredient_".$i];
					$unitPrice	=	trim($p["unitPrice_".$i]);
					$quantity	=	trim($p["quantity_".$i]);
					$totalQty	=	$p["total_".$i];

					if ($lastId!="" && $ingredientId!="" && $unitPrice!="" && $quantity!="") {
						$purchaseItemsIns = $ingredientPurchaseorderObj->addPurchaseEntries($lastId, $ingredientId, $unitPrice, $quantity, $totalQty);
					}
			  	}
			}
		}

		if ($purchaseOrderRecIns) {
			if ($warning!="") {
		?>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				alert("<?=$warning;?>");
			//-->
			</SCRIPT>
		<?php
			}	
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddPurchaseOrderIngredient);
			$sessObj->createSession("nextPage",$url_afterAddPurchaseOrderIngredient.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddPurchaseOrderIngredient;
		}
		$purchaseOrderRecIns		=	false;
	}
	
	
	# Edit Purchase Order
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$purchaseOrderRec	=	$ingredientPurchaseorderObj->find($editId);
		
		$editPurchaseOrderId	=	$purchaseOrderRec[0];		
		$editPO			=	$purchaseOrderRec[1];

		if ($p["editSelectionChange"]=='1' || $p["selSupplier"]=="") {
			$selSupplierId		=	$purchaseOrderRec[2];
		} else {
			$selSupplierId		=	$p["selSupplier"];
		}
		
		$selIngRateListId	= $purchaseOrderRec[6];
		$purchaseRecs = $ingredientPurchaseorderObj->fetchAllStockItem($editPurchaseOrderId);	

		$supplierIngRecs = $ingredientPurchaseorderObj->fetchSupplierIngredientRecords($selSupplierId);	
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$purchaseOrderId = $p["hidPurchaseOrderId"];
		$itemCount	=	$p["hidTableRowCount"];		
		$selSupplierId	=	$p["selSupplier"];
		$ingredientRateListId = $p["hidIngredientRateListId"];		
		
		if ($purchaseOrderId!="" && $selSupplierId!="") {
			$purchaseOrderRecUptd = $ingredientPurchaseorderObj->updatePurchaseOrder($purchaseOrderId, $selSupplierId, $ingredientRateListId);
		
			#Delete First all records from purchase order entry table
			//$deleteStockItemRecs = $ingredientPurchaseorderObj->deletePurchaseOrderItemRecs($purchaseOrderId);
			
			for ($i=0; $i<$itemCount; $i++) {
				$status 	= $p["status_".$i];
				$ingPOEntryId	= $p["ingPOEntryId_".$i];

				if ($status!='N') {
					$ingredientId	=	$p["selIngredient_".$i];
					$unitPrice	=	trim($p["unitPrice_".$i]);
					$quantity	=	trim($p["quantity_".$i]);
					$totalQty	=	trim($p["total_".$i]);
					
					if ($purchaseOrderId!="" && $ingredientId!="" && $unitPrice!="" && $quantity!="" && $ingPOEntryId!="") {
						$updatePurchaseItems = $ingredientPurchaseorderObj->updatePurchaseEntries($ingPOEntryId, $ingredientId, $unitPrice, $quantity, $totalQty);
					} else if ($purchaseOrderId!="" && $ingredientId!="" && $unitPrice!="" && $quantity!="" && $ingPOEntryId=="") {
						$purchaseItemsIns = $ingredientPurchaseorderObj->addPurchaseEntries($purchaseOrderId, $ingredientId, $unitPrice, $quantity, $totalQty);
					} 
				} 
				else if ($status=='N' && $ingPOEntryId!="") {
					# Delete Entry
					$delPurchaseItems = $ingredientPurchaseorderObj->delPurchaseEntries($ingPOEntryId);
				}
			}
		}
	
		if ($purchaseOrderRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPurchaseOrderIngredientUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePurchaseOrderIngredient.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPurchaseOrderIngredientUpdate;
		}
		$purchaseOrderRecUptd	=	false;
	}
	

	# Delete Purchase Order
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$purchaseOrderId	= $p["delId_".$i];
			$status  	 	= $p["recStatus_".$i];
			
			if ($purchaseOrderId!="" && $status!='R') {
				$deleteStockItemRecs =	$ingredientPurchaseorderObj->deletePurchaseOrderItemRecs($purchaseOrderId);
				$purchaseOrderRecDel = $ingredientPurchaseorderObj->deletePurchaseOrder($purchaseOrderId);
			}
		}
		if ($purchaseOrderRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPurchaseOrderIngredient);
			$sessObj->createSession("nextPage",$url_afterDelPurchaseOrderIngredient.$dateSelection);
		} else {
			$errDel	=	$msg_failDelPurchaseOrderIngredient;
		}
		$purchaseOrderRecDel	=	false;
	}

## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
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

	#List all Ingredient Purchase Order
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate	=	mysqlDateFormat($dateFrom);
		$tillDate	=	mysqlDateFormat($dateTill);;
		
		$purchaseOrderRecords = $ingredientPurchaseorderObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$purchaseOrderSize = sizeof($purchaseOrderRecords);
	
		// Pagination
		$fetchAllPurchaseOrderRecords = $ingredientPurchaseorderObj->fetchAllRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows = sizeof($fetchAllPurchaseOrderRecords);
	$maxpage = ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------	

	#Get Not completed PO for Printing
	$purchaseOrderPendingRecords = $ingredientPurchaseorderObj->getPORecords();
	
	if ($addMode) {
		#List all Ingredient
		$selIngRateListId = $ingredientRateListObj->latestRateList();
	}
	
	# Get Ingredient Records based on Rate List Id
	$ingredientRecords = $ingredientRateMasterObj->fetchAllIngredientRecords($selIngRateListId);

	# List all Supplier
	//$supplierRecords	=	$supplierMasterObj->fetchAllRecords("RTE");
	$supplierRecords	=	$supplierMasterObj->fetchAllRecordsActivesupplier("RTE");
	# Setting the mode
	if ($addMode || $poItem) $mode = 1;
	else if ($editMode) $mode = 0;
	else $mode = "";	

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/IngredientPO.js"; // For Printing JS in Head SCRIPT section	

	if ($editMode) $heading	= $label_editPurchaseOrderIngredient;
	else	       $heading	= $label_addPurchaseOrderIngredient;
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmIngredientPO" action="IngredientPO.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPO.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientPurchaseOrder(document.frmIngredientPO);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPO.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateIngredientPurchaseOrder(document.frmIngredientPO);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidPurchaseOrderId" value="<?=$editPurchaseOrderId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
						<tr>
						  <td colspan="2" nowrap><table width="200">
                                                <tr>
                                                  <td class="fieldName">PO ID : </td>
                                                  <td class="listing-item">
							<input name="textfield" id="textfield" type="text" size="6" <? if($genPoId!=0 || $editPO){ ?> style="border:none" readonly <?}?>   onKeyUp="xajax_chkIngPONumberExist(document.getElementById('textfield').value, '<?=$mode?>');" value="<? if($editPO) { echo  $editPO;} else if($genPoId==1) { echo "New"; }else { echo $p["textfield"]; }?>">
							<!--<input name="textfield" type="text" size="6" style="border:none" readonly value="<? if($editPO) { echo  $editPO;} else { echo "New"; }?>">-->
						</td>
						<td nowrap><div id="divPOIdExistTxt" style='line-height:normal; font-size:10px; color:red;'></div></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Supplier</td>
                                                  <td class="listing-item">
						  <select name="selSupplier" id="selSupplier" onchange="xajax_supplierIngRecords(document.getElementById('selSupplier').value,document.getElementById('hidTableRowCount').value,'<?=$mode?>');">
                                                    <option value="">--select--</option>
                                                    <?
						foreach($supplierRecords as $sr)
						{
							$supplierId	=	$sr[0];
							$supplierCode	=	stripSlash($sr[1]);
							$supplierName	=	stripSlash($sr[2]);
							$selected ="";
							if ($selSupplierId==$supplierId) $selected="selected";
						?>
                                                <option value="<?=$supplierId?>" <?=$selected;?>>
                                                    <?=$supplierName?>
                                                    </option>
                                                    <? }?>
                                                  </select></td>
                                                </tr>
                                              </table></td>
					</tr>
<!--  Dynamic Row Starting Here-->
<tr>
	<TD>
		<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddPOIngItem">
                	<tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head">Sr.<br>No</td>
                        	<td class="listing-head">Ingredient</td>
				<td class="listing-head" nowrap>Rate/Kg</td>
                                <td class="listing-head">Quantity</td>
                                <td class="listing-head">Total</td>
				<td class="listing-head">Balance Qty</td>
				<td>&nbsp;</td>
                        </tr>
	<?
	if (sizeof($purchaseRecs)>0) {
		$k = 0;
		$totalAmount = 0;	
		foreach ($purchaseRecs as $pr) {
			$ingPOEntryId	= $pr[0];
			$selIngId	= $pr[2];
			$selRate	= $pr[3];
			$quantity	= $pr[4];
			$totalAmt	= $pr[5];
			$totalAmount += $totalAmt;	
			$balanceQty = $ingredientPurchaseorderObj->getBalanceQty($selIngId);	
	?>
<tr align="center" class="whiteRow" id="row_<?=$k?>">
	<td align="center" class="listing-item" id="srNo_<?=$k?>">
		<?=($k+1)?>
	</td>	
	<td align="center" class="listing-item">
		<select onchange="xajax_getIngRate(document.getElementById('selIngredient_<?=$k?>').value, '<?=$k?>');" id="selIngredient_<?=$k?>" name="selIngredient_<?=$k?>">
		<?php
			if (sizeof($supplierIngRecs)<=0) {
		?>
		<option value="">-- Select --</option>
		<?php
		}
		?>
		<?php
		foreach ($supplierIngRecs as $ingId=>$ingName) {
			$selected = ($selIngId==$ingId)?"selected":"";
		?>
		<option value="<?=$ingId?>" <?=$selected?>><?=$ingName?></option>
		<? }?>
		</select>
	</td>
	<td align="center" class="listing-item">
		<input type="text" readonly="" style="text-align: right;" size="6" value="<?=$selRate?>" id="unitPrice_<?=$k?>" name="unitPrice_<?=$k?>"/>
	</td>
	<td align="center" class="listing-item">
		<input type="text" onkeyup="multiplyIngPOItem('');" autocomplete="off" style="text-align: right;" size="6" value="<?=$quantity?>" id="quantity_<?=$k?>" name="quantity_<?=$k?>"/>
		<input type="hidden" value="" id="status_<?=$k?>" name="status_<?=$k?>"/>
		<input type="hidden" value="N" id="IsFromDB_<?=$k?>" name="IsFromDB_<?=$k?>"/>
		<input type="hidden" value="<?=$selIngId?>" readonly="" id="hidSelIng_<?=$k?>" name="hidSelIng_<?=$k?>"/>
		<input type="hidden" name="ingPOEntryId_<?=$k?>" id="ingPOEntryId_<?=$k?>" value="<?=$ingPOEntryId?>" readonly />
	</td>
	<td align="center" class="listing-item">
		<input type="text" value="<?=$totalAmt?>" style="text-align: right;" readonly="" size="6" id="total_<?=$k?>" name="total_<?=$k?>"/>
	</td>
	<td nowrap="" align="center" class="listing-item">
		<div id="balanceQty_<?=$k?>"><?=number_format($balanceQty,2,'.','');?></div>
	</td>
	<td align="center" class="listing-item">
		<a onclick="setPOIngItemStatus('<?=$k?>');" href="###">
			<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
		</a>
	</td>
</tr>
<?php
		$k++;
	} // Loop Ends here
  } // Cond ends here
?>
			<tr bgcolor="#FFFFFF" align="center">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
                                <td class="listing-head" align="right">Total:</td>
                                <td nowrap><input name="totalQuantity" type="text" id="totalQuantity" size="7" style="text-align:right" readonly value="<?=number_format($totalAmount,2,'.','');?>"></td>
				<td>&nbsp;</td>
				<td></td>
                        </tr>
		</table>
	</TD>
</tr>
<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$k?>">
<!--  Dynamic Row Ends Here-->
<tr><TD height="5"></TD></tr>
<tr>
	<TD>
		<a href="###" id='addRow' onclick="javascript:addNewIngItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPO.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateIngredientPurchaseOrder(document.frmIngredientPO);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPO.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateIngredientPurchaseOrder(document.frmIngredientPO);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Purchase Order  </td>
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
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value=" Search " ></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
						<tr>
								  <td colspan="3" align="right" style="padding-right:10px;">
								  <table width="200" border="0">
                        <tr>
                          <td><fieldset>
                            <legend class="listing-item">Print PO</legend>
							<table width="200" cellpadding="0" cellspacing="0" bgcolor="#999999">
					
                      <tr bgcolor="#FFFFFF">
                        <td class="listing-item" nowrap="nowrap" height="25">PO No: </td>
                        <td nowrap="nowrap"><? $selPOId=$p["selPOId"];?>&nbsp;
						<select name="selPOId" id="selPOId" onchange="disablePrintPOButton();">
						<option value="">-- Select --</option>
						<?
						foreach ($purchaseOrderPendingRecords as $por) {
							$poId	      =	$por[0];
							$poGenerateId =	$por[1];
							$selected="";
							if($selPOId==$poId) $selected="Selected";
						?>
						<option value="<?=$poId?>" <?=$selected?>><?=$poGenerateId?></option>
						<? }?>
                        			</select>
						</td>
						<? if($print==true){?>
						<td nowrap="nowrap">&nbsp;<input name="cmdPrintInvoice" type="button" class="button" id="cmdPrintPO" onClick="return printIngPurchaseOrderWindow('PrintIngredientPO.php',700,600);" value="Print PO" <? if($selPOId=="") echo $disabled="disabled"; ?> ></td>
						<td>&nbsp;</td>
						<? }?>
                      </tr>
                    </table></fieldset></td>
                          </tr>
                      </table></td> </tr>
			<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientPOList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
									<td width="1" ></td>
									<td colspan="2" >
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if ( sizeof($purchaseOrderRecords) > 0) {
		$i	=	0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="6" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   			{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			}
   			else
   			{
      				$nav.= " <a href=\"IngredientPO.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientPO.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientPO.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div></td>
       </tr>
	   <? }?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">PO ID</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($purchaseOrderRecords as $por) {
		$i++;
		$purchaseOrderId	=	$por[0];
		$poId			=	$por[1];

		$total_amount = $ingredientPurchaseorderObj->getPurchaseOrderAmount($purchaseOrderId);

		$status		=	$por[5];
		$displayStatus = "";
		if 	($status=='C') $displayStatus = "Cancelled";
		else if ($status=='R') $displayStatus = "Received";
		else if ($status=='P') $displayStatus = "Pending";

		$supplierName	=	$por[6];
	?>
	<tr  bgcolor="WHITE">
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$purchaseOrderId;?>" class="chkBox">
			<input type="hidden" name="recStatus_<?=$i?>" value="<?=$status?>">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$poId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$total_amount;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayStatus?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<? if ($status!='R') { ?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$purchaseOrderId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='IngredientPO.php';">
			<? }?>
		</td>
		<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	 <? if($maxpage>1){?>
         	 	      <tr bgcolor="#FFFFFF">
         	 	        <td colspan="6" style="padding-right:10px;">
				<div align="right">
				  <?php 				 			  
				 $nav  = '';
		for($page=1; $page<=$maxpage; $page++)
			{
				if ($page==$pageNo)
   				{
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				}
   				else
   				{
      	$nav.= " <a href=\"IngredientPO.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"IngredientPO.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"IngredientPO.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div><input type="hidden" name="pageNo" value="<?=$pageNo?>"></td>
       	 	        </tr>
			<? }?>
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$purchaseOrderSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientPOList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
</td>
											</tr>
										</table>									</td>
								</tr>
<input type="hidden" name="hidIngredientRateListId" id="hidIngredientRateListId" value="<?=$selIngRateListId?>" >
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>
		
		<tr>
			<td height="10"></td>
		</tr>
	<input type='hidden' name='genPoId' id='genPoId' value="<?=$genPoId;?>" >
	</table>
<SCRIPT LANGUAGE="JavaScript">
		function addNewIngItem()
		{
			addNewIngredientItemRow('tblAddPOIngItem', '', '', '', '');
			//xajax_supplierIngRecords(document.getElementById('selSupplier').value,document.getElementById('hidTableRowCount').value,'<?=$mode?>');
			//alert("hh====>"+suppIngArr);
		}
</SCRIPT>
<? if ($addMode) {?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewIngItem();
	</SCRIPT>
<? }?>
<?
	if (sizeof($purchaseRecs)>0) {
?>
<script language="Javascript">
	xajax_supplierIngRecords('<?=$selSupplierId?>',<?=sizeof($purchaseRecs)?>,'<?=$mode?>');
	fieldId = '<?=sizeof($purchaseRecs)?>';
</script>
	<?
	/*
		$k = 0;
		$totalAmount = 0;	
		foreach ($purchaseRecs as $pr) {		
			$selIngId	= $pr[2];
			$selRate	= $pr[3];
			$quantity	= $pr[4];
			$totalAmt	= $pr[5];		
	*/
	?>

	<!--SCRIPT LANGUAGE="JavaScript">
		addNewIngredientItemRow('tblAddPOIngItem', '', '<?//=$selIngId?>', '<?//=$quantity?>', '<?//=$totalAmt?>');
		//xajax_supplierIngRecords('<?//=$selSupplierId?>',<?//=sizeof($purchaseRecs)?>,'<?//=$mode?>');
		xajax_getIngRate('<?//=$selIngId?>','<?//=$k?>'); // Find Ing Rate
		xajax_getIngBalanceQty('<?//=$selIngId?>','<?//=$k?>'); // Find balance qty		
	</SCRIPT-->
<?php
	/*
		$k++;
		}	
	*/
	}
?>

 <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup
	(
		{
			inputField  : "schedule",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "schedule",
			ifFormat    : "%m/%d/%Y",    // the date format
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

	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>