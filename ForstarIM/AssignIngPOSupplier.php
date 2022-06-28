<?php
	require("include/include.php");
	require_once("lib/IngredientPO_ajax.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$selStatus	=	"";
	$supplier_Id	=	"";
	$userId		=	$sessObj->getValue("userId");
	$fromDate	=	mysqlDateFormat(date("d/m/Y"));
	$tillDate   	=	mysqlDateFormat(date("d/m/Y"));
	//$dateSelection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];
	
	$rt = ( $g["stockReportType"]=="") ? $p["stockReportType"] : $g["stockReportType"];
	$sd = ( $g["selDate"]=="") ? $p["selDate"] : $g["selDate"];
	
	/*
	if ($g["stockItem"]!="") {
		$poItem = $g["stockItem"];
	} else {
		$poItem = $p["stockItem"];
	}
	*/
	$prodPlanItem = "";
	if ($g["selItem"]!="") $prodPlanItem = $g["selItem"]; 
	else $prodPlanItem = $p["selItem"]; 

	/*
	$pItemArray = explode(",",$prodPlanItem);
	if (sizeof($pItemArray)>0) {
		foreach ($pItemArray as $prodPlanId) {
			echo "H=".$prodPlanId;
		}
	}
	*/

	# Fin the Current Rate List of Ingredients
	$ingRateListId = $ingredientRateListObj->latestRateList();
	/*
		Modified on 26-12-08
		//$getProductionIngRecords = $ingredientPurchaseorderObj->fetchIngredientRecords($prodPlanItem, $ingRateListId);
	*/
	/*****************************/
	# Production Plan Records
	$productionPlanRecords = $ingredientPurchaseorderObj->getProductionPlanRecords($prodPlanItem);	
		$selIngredients	= array();
		foreach ($productionPlanRecords as $ppr) {
			$ingredientId = $ppr[1];
			$ingQty	      = $ppr[2];
			$selIngType   = $ppr[3];
			$ingredientName = "";
			if ($selIngType=='ING') {	# If ING
				$ingredientName	= $ingredientMasterObj->getIngName($ingredientId);		
				$selIngredients[$ingredientId] += $ingQty;				
			} else if ($selIngType=='SFP') { # If Semi Finished
				# SF Product Records
				$sfIngRecords = $ingredientPurchaseorderObj->getSemiFinishIngRecords($ingredientId);
				if (sizeof($sfIngRecords)>0) {
					$totalQty 	= 0;
					$currentStock 	= 0;
					$calcQty	= 0;
					foreach ($sfIngRecords as $r) {
						$ingredientId 	 = $r[2];				
						$percentPerBatch = $r[4];	
						$calcQty 	= ($ingQty*$percentPerBatch)/100;
						$quantity 	= number_format($calcQty,2,'.','');		
						$selIngredients[$ingredientId] += $quantity;
					}
				}
			}
			
		} // Production Plan Loop ends here 
	/*
	echo "Size==".sizeof($selIngredients);
	echo "<pre>";
	print_r($selIngredients);		
	echo "</pre>";
	*/
	/*****************************/
	# Get Ingredient Records based on Rate List Id
	$ingredientRecords = $ingredientRateMasterObj->fetchAllIngredientRecords($ingRateListId);

	# Add Purchase Order Start
	if ($p["cmdAddNew"]!="" || $prodPlanItem!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;
		$prodPlanItem 	= "";
		$p["selItem"] = "";
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
		$prodPlanItem		= "";
		//$hidEditId 	= "";
	}
	$supplier = array();
	$genPoId = $idManagerObj->check("IPO");
	$hideSelection=false;
	
	# Continue Orders
	if ($p["cmdContinue"]!="") {

		$prodPlanItem = $p["selItem"];
		$itemCount	= $p["hidTableRowCount"];
		$selSupplierId	= $p["selSupplier"];
		$poNumber	= $p["poNumber"];
		//$hidSupplierRateListId = $p["hidSupplierRateListId"];		
		
		$selSupplier = "";
		for ($i=0; $i<$itemCount; $i++) {
			$status	=	trim($p["status_".$i]);		

			if ($status!="N") {
				$hidSupplierCount = $p["hidSupplierCount_".$i];
				$stockId	=	$p["selIngredient_".$i];
				$unitPrice	=	trim($p["unitPrice_".$i]);
				$quantity	=	trim($p["quantity_".$i]);
				$totalQty	=	$p["total_".$i];
				$bqty = $p["hBlnQty_".$i];

				for ($j=1;$j<=$hidSupplierCount;$j++) {
					$selSupplier = $p["selSupplier_".$j."_".$i];
					if ($selSupplier!="" && $quantity!="") {
						//$negoPrice = $p["negoPrice_".$j."_".$i];
						$negoPrice = $unitPrice;
						$supplier[$selSupplier][$stockId]=array($negoPrice, $quantity, $totalQty,$bqty);
					}			
				}
			}
		}
		if (sizeof($supplier)>0) {
			$hideSelection= true;
		}
	}
	
	$warningMsg = "";
		
	if ($p["cmdUpdateOrder"]!="") {
		$rc = $p["hidSuppCount"];
		$cnt = 0;

		for ($s=0; $s<$rc; $s++) {
			$stockCount = $p["hidSupplierStockCount_".$s];
			$supplierId = $p["hidSupplierId_".$s];
			$poNumber = ""; 
			$isPoExist = $p["isPoExist_".$s];		

			if ($genPoId ==0) {
				$poId = $p["inpPOid_".$s];
				if ($idManagerObj->checkMaxId("IPO",$poId)=="Y") $warningMsg = "The generated PO ID is greater than the ending number of Purchase Order.";
			} else {
				list($isMaxId,$poId) = $idManagerObj->generateNumberByType("IPO");
				if ($isMaxId=='Y') $warningMsg = "The generated PO ID is greater than the ending number of Purchase Order.";
			}
			if ($genPoId==0) $apoid = $poId;
			else $apoid = "";

			if ($isPoExist=="") $err = "Purchase Order ID cannot be duplicate.";

			if ($supplierId!='') {
				if ($isPoExist=="") {
					$purchaseOrderRecIns =	$ingredientPurchaseorderObj->addPurchaseOrder($poId, $supplierId, $userId, $ingRateListId);
					$lastId = $databaseConnect->getLastInsertedId();
				}
 				
				for ($k=1; $k<=$stockCount; $k++) {
					$stkId = $p["hidSupplierStockId_".$s."_".$k];
					$untPrice = $p["hidSupplierUnitPrice_".$s."_".$k];
					$qty = $p["hidSupplierQuantity_".$s."_".$k];	
					$totQty = $p["hidSupplierTotalPrice_".$s."_".$k];
					$supplier[$supplierId][$stkId] 	= array($untPrice, $qty, $totQty,0);
					if ($isPoExist=="") {
						if ($qty!='' && $lastId!='') {
							$purchaseItemsIns =	$ingredientPurchaseorderObj->addPurchaseEntries($lastId, $stkId, $untPrice, $qty, $totQty);
						}
					}
				} // end for 
			}
		}		
		
		if ($purchaseOrderRecIns) {
			# Updating Prouction Plan Status (Completed)
			if ($prodPlanItem!="") {	
				$pItemArray = explode(",",$prodPlanItem);
				if (sizeof($pItemArray)>0) {
					$prodPlanstatus = 'C';	
					foreach ($pItemArray as $prodPlanId) {
						$updateProductionPlanRec = $ingredientPurchaseorderObj->updateProductionPlanRec($prodPlanId,$prodPlanstatus);
					}
				}	
			}		

			if ($warningMsg !="") {
				printJSalert($warningMsg);
			}
			$sessObj->createSession("displayMsg",$msg_succAddPurchaseOrderIngredient);
			$sessObj->createSession("nextPage",$url_afterAddPurchaseOrderIngredient);
		} 
		else {
			$err		=	$msg_failAddPurchaseOrderIngredient;
			if (sizeof($supplier)>0) {
				$hideSelection= true;
			}
		}
		$purchaseOrderRecIns		=	false;
	}
	
	$heading = "Assign Suppliers";
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/IngredientPO.js"; // For Printing JS in Head SCRIPT section	

	require("template/topLeftNav.php");
?>
	<form name="frmAssignIngPOSupplier" action="AssignIngPOSupplier.php" method="post">
	<input type='hidden' name='hidSupplierIdRec'  id='hidSupplierIdRec' value="<?=$selSupplierId;?>">
	<input type="hidden" name="selItem" value="<?=$prodPlanItem;?>">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if (( $editMode || $addMode ) && !$hideSelection)
			{
		?>
		<tr>
			<td align="center">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
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
									<td colspan="2" align='center' >
										<table cellpadding="0"  width="80%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
														
												<td  colspan="2" align="center">
												<input type="submit" name="cmdBack" onClick="this.form.action='PurchaseIntent.php';" class="button" value=" Go Back " >&nbsp;&nbsp;
												<input type="submit" name="cmdContinue" class="button" value=" Continue " onClick="return validateUpdateIngPO(document.frmAssignIngPOSupplier);"> &nbsp;&nbsp;</td>
											
											</tr>
											<input type="hidden" name="hidPurchaseOrderId" value="<?=$editPurchaseOrderId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>			
					<tr>
					  <td colspan="2" nowrap>
					<table width="100%">
					<TR><TD>
					  <table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
                                            <tr bgcolor="#f2f2f2" align="center">
                                                  <td class="listing-head">Item</td>				
                                                  <td class="listing-head" nowrap="nowrap">Unit Price </td>	
                                                  <td class="listing-head">Quantity</td>
                                                  <td class="listing-head">Total</td>
						<td class="listing-head">Balance Qty</td>
						<td class="listing-head">Other Suppliers</td>			
						<td class="listing-head" nowrap >Remove</td>
                                                </tr>
		<?
		//if ($prodPlanItem!="") $numRows =sizeof($getProductionIngRecords);
		
		$granTotalAmt = 0;
		$totalAmt	= 0;
		$selIngredientId = "";		
		//for ($m=0; $m<$numRows; $m++) {
		$m=0;
		foreach ($selIngredients as $selIngredientId=>$quantity) {					
			// Edit	Mode and PO Item 
			/*if (sizeof($getProductionIngRecords)>0) $rec = $getProductionIngRecords[$m];
			$selIngredientId = $rec[1];
			*/
	
			#Getting the supplier stock of same stock
			$getSupplierIngRecs = $ingredientPurchaseorderObj->getSupplierIngRecs($selIngredientId, $selSupplierId, $prodPlanItem);

				$balanceStockQty = $ingredientPurchaseorderObj->getBalanceQty($selIngredientId);
				//$unitRate	= $rec[4];
				list($unitRate,$declYield) = $productMasterObj->getIngredientRate($selIngredientId, $ingRateListId);
				//$quantity	= $rec[3];				
				$totalAmt	= $unitRate*$quantity;				

				$granTotalAmt += $totalAmt;
							
			?>
                        <tr bgcolor="#FFFFFF" align="center" id="row_<?=$m;?>">
                               <td >
				<select name="selIngredient_<?=$m?>" id="selIngredient_<?=$m?>" onChange="xajax_getQuantitiesOfStock(document.getElementById('selIngredient_<?=$m?>').value, <?=$m?>, document.getElementById('hidSupplierIdRec').value);" >	
                                 <option value="">--select--</option>
                                 <?
				 $ingredientId = "";
				foreach ($ingredientRecords as $irr) {					
					$ingredientId   = $irr[1];					
					$ingredientName	= $irr[8];
					$selected	= "";	
					if ($selIngredientId==$ingredientId) $selected="selected";
				 ?>
                                 <option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
                                                    <? }?>
                                 </select>				
				</td>								
                                 <td class="fieldName">
					<input name="unitPrice_<?=$m?>" type="text" id="unitPrice_<?=$m?>" value="<?=$unitRate;?>" size="6" readonly style="text-align:right;border:none;"/>
				</td>				
                                 <td class="fieldName">
					<input name="quantity_<?=$m?>" type="text" id="quantity_<?=$m?>" size="6" style="text-align:right" value="<?=$quantity?>" onKeyUp="return multiplyIngPO(document.frmAssignIngPOSupplier, '<?=$prodPlanItem?>');">
				</td>
                                <td class="fieldName">
					<input name="total_<?=$m?>" type="text" id="total_<?=$m?>" size="6" readonly style="text-align:right" value="<?=number_format($totalAmt,2,'.','');?>">
				</td>
				<td class="fieldName" align="right" style="padding-left:5px; padding-right:5px">
					<div id="bqty_<?=$m;?>"><?=($balanceStockQty)?$balanceStockQty:0;?></div></td>
					<input type='hidden' value="<?=($balanceStockQty)?$balanceStockQty:0;?>"  name="hBlnQty_<?=$m?>">
				
				<td align="left"  id="OtherSuppList_<?=$m;?>" >
					<? 					
				 	if (sizeof($getSupplierIngRecs)>0 ) {
					?>
					<table class="print">
					<? if (sizeof($getSupplierIngRecs)>0) {?>
						<? if ($prodPlanItem!="") {?>
						<tr>
							<TD>&nbsp;</TD>
							<?
							$k = 0;
							foreach($getSupplierIngRecs as $gsr) {
								$k++;
								$supplierId = $gsr[1];
								$supplierName = $gsr[4];
								$supplierNegotiatedPrice = $gsr[3];

								$supplierChkd = "";
								if ($p["selSupplier_".$k."_".$m]!="") {
									$supplierChkd = "checked";
								}
							?>
				<TD class="listing-item" style="padding-left:2px; padding-right:2px;">
							<input type="checkbox" name="selSupplier_<?=$k?>_<?=$m?>" class='chkBox' id="selSupplier_<?=$k?>_<?=$m?>" value="<?=$supplierId?>" onclick="uncheckSelected('selSupplier_<?=$k?>_<?=$m?>',<?=$m?>);return multiplyIngPO(document.frmAssignIngPOSupplier, '<?=$prodPlanItem?>');" <?=$supplierChkd?>></TD>
							<!--<input type="hidden" name="negoPrice_<?=$k?>_<?=$m?>" id="negoPrice_<?=$k?>_<?=$m?>" value="<?=$supplierNegotiatedPrice?>">-->
							<? }?>
							<input type="hidden" name="hidSupplierCount_<?=$m?>" id="hidSupplierCount_<?=$m?>" value="<?=$k?>">
						</tr>
						<? }?>
						<tr align="center">
							<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;">Supplier</TD>
							<?
								foreach($getSupplierIngRecs as $gsr) {

									$supplierName = $gsr[3];
									//$supplierNegotiatedPrice = $gsr[3];
								?>
							<TD class="listing-item" style="padding-left:2px; padding-right:2px;"><?=$supplierName?></TD>
							<? }?>
						</tr>
						<!--<tr>
							<td class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;">Price</td>
							<?
								foreach($getSupplierIngRecs as $gsr) {

									$supplierName = $gsr[4];
									$supplierNegotiatedPrice = $gsr[3];
								?>
							<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><?=$supplierNegotiatedPrice?></TD>
							<? }?>
						</tr>-->						
						<?
							}
						
						?>
					</table>
						<? 
						} else { // No Supplier
						?>
					<table bgcolor='white' align='center'>
						<TR>
							<td bgcolor='white' class='err1' nowrap="true"><span style="line-height:normal; font-size:9px;">No suppliers found</span></td>
							<input type="hidden" name="hidSupplierCount_<?=$m?>" id="hidSupplierCount_<?=$m?>" value="0">
							<input type="hidden" name="selSupplier_<?=$k?>_<?=$m?>" id="selSupplier_<?=$k?>_<?=$m?>" value="<?=$supplierId?>" ></TR>
						</table>
						<? 
						}
						
						?>
						</td>					
						<td align='center'>
						<input name="status_<?=$m?>" type='hidden' id="status_<?=$m?>" value=''>
						<input name="IsFromDB_<?=$m?>" type='hidden' id="IsFromDB_<?=$m?>" value='N'>
						<?
							if( $m!=0 ){
						?>
						<a href='#' onClick="POIngStatus('<?=$m;?>');" ><img SRC='images/delIcon.gif' BORDER='0' style='border:none;' title="Click here to remove this item."></a>
						<?}?>
						</td>
                                                </tr>
						<?	
							$m++;
							}  // Loop Ends Here
						?>
                                                <tr bgcolor="#FFFFFF" align="center">				
                                                  <td colspan="3" class="listing-head" align="right">Total:</td>
                                                  <td class="fieldName">
							<input name="totalQuantity" type="text" id="totalQuantity" size="6" style="text-align:right" readonly value="<?=number_format($granTotalAmt,2,'.','');?>">
						</td>			
						<td>&nbsp;</td>
						<td>&nbsp;</td>					
						  <td>&nbsp;</td>
                                                </tr>
                                              </table>
					<!---  table 2 end Here-->
						</TD>
						</TR>
						</table>					
						</td>
						   </tr>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			setfieldId(<?=$m;?>)
		//-->
		</SCRIPT>
		<input type='hidden' name='hidTableRowCount' id='hidTableRowCount' value="<?=$m;?>">
		<input type='hidden' name='totalRowCount' id='totalRowCount' value="<?=$m;?>">	
		<? // Edit Mode ?>
		<input type="hidden" name="hidSelSupplierId" value="<?=$purchaseOrderRec[3];?>" />
		<tr>
			 <td colspan="2" nowrap class="fieldName">
				<a href="#" id='addNewPOItemLink' onclick="javascript:addNewUpdatePOItem('tblAddItem',document.frmAssignIngPOSupplier,1);"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
			</td>
	  	</tr>
		<tr>
			  <td colspan="2" nowrap class="fieldName" >&nbsp;</td>
	  	</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
														
												<td  colspan="2" align="center">
												<input type="submit" name="cmdBack" onClick="this.form.action='PurchaseIntent.php';" class="button" value=" Go Back " >&nbsp;&nbsp;
												<input type="submit" name="cmdContinue" class="button" value=" Continue " onClick="return validateUpdateIngPO(document.frmAssignIngPOSupplier);"> &nbsp;&nbsp;</td>
											
											</tr>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
											
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
			
			if( $hideSelection )
			{
		?>
		<tr>
			<td >
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white" >
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Update Orders</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" align='center' Style="padding-bottom:20px;" >
										<table cellpadding="0"  width="80%" cellspacing="0" border="0" align="center">
											<tr>
												<td id="showMsgPo" class="err1" align='center' height='20'></td>
											</tr>

											<tr>
												<td colspan='2' align='center'>
													<input type="submit" name="cmdUpdateOrder" id="cmdUpdateOrder" class="button" value=" Update Orders " onClick="return validateIngPOId(document.frmAssignIngPOSupplier);">&nbsp;&nbsp;</td>
												</td>
											</tr>
											<tr>
												<td colspan='2' height='10'  ></td>
											</tr>
											<tr>
												<td colspan="2" height="10" >
													
													<table  cellspacing="1" bgcolor="#999999" cellpadding="3"  width="100%" id="tblAddItem">
	<tr bgcolor="#f2f2f2" align="center">
		<td class="listing-head" >Supplier</td>
		<? if( $genPoId!=1) {?><td class="listing-head" >PO ID</td><?}?>
		<td class="listing-head" >Item</td>
		<td class="listing-head">Unit Price</td>
		<td class="listing-head">Quantity</td>
		<td class="listing-head">Total</td>
	</tr>	
	<input type='hidden' name='hidSuppCount' id='hidSuppCount' value="<?=sizeof($supplier);?>" >
	<input type='hidden' name='genPoid' id='genPoid' value="<?=$genPoId;?>" >
	<?
		$prevSuppId = 0;
		$ovTotalUnitPrice = 0;
		$ovTotalQty = 0;
		$ovTotalPrice = 0; 
		$po = 0;
		foreach ($supplier as $supplierId=>$stockArray) {
			$subUnitPrice = 0.00;
			$subTotalQty = 0;
			$subTotPrice = 0;
			$p = 0;
			foreach ($stockArray as $stockId=>$item) {
				$p++;
				$untp = $item[0];
				$qty  = $item[1];
				$tqty  = $item[2];
				$totPrice = ($untp*$qty);
				$srec	=	$ingredientMasterObj->find($stockId);
				$stname	=	stripSlash($srec[2]);
				$subUnitPrice = number_format($subUnitPrice+$untp,2,".","");
				$subTotalQty = $subTotalQty+$qty;
				$subTotPrice = number_format($subTotPrice+$totPrice, 2,".","");
				$sup += $subUnitPrice;
				$stq += $subTotalQty;
				$stp += $subTotPrice;
	?>
	<tr bgcolor='white' align="center">
	<?
		if ($prevSuppId!=$supplierId ) {
		if ($genPoId!=1) $cl = 2;
		else $cl = 1;
	?>
	<input type="hidden" name="hidSupplierId_<?=$po;?>" value="<?=$supplierId;?>">
	<?
		$sr = $supplierMasterObj->find( $supplierId );
		if ($sr!="") 											echo "<td align='left' class=\"fieldName\" >".$sr[2]."</td>";
	?>
	<?	
		if ($genPoId!=1) {
	?>
	<td class="listing-item" style="padding-left:2px; padding-right:2px;" >
		<input type='text' name="inpPOid_<?=$po;?>" id="inpPOid_<?=$po;?>"  value="<?=$HTTP_POST_VARS["inpPOid_$po"];?>" size='7'  onChange="xajax_checkPOIdExist(document.getElementById('inpPOid_<?=$po;?>').value, <?=$po;?>,1);" >
		<input type="hidden" name="isPoExist_<?=$po;?>" id="isPoExist_<?=$po;?>" value=""><br>
		<span id="msgPOIdExist_<?=$po;?>" style="line-height:normal; font-size:9px;" class="err1"></span>
	</td>
	<?
		}
	?>
		<input type='hidden' name="inpPurchaseNumber_<?=$po;?>" id="inpPurchaseNumber_<?=$po;?>" size='7'   value="<?=$HTTP_POST_VARS["inpPurchaseNumber_$po"];?>"  >
	</td>
		<input type="hidden" name="hidSupplierStockCount_<?=$po;?>" value="<?=sizeof($stockArray);?>" >
	<?
		}
		else echo "<td align='left' colspan='".$cl."' ></td>";
		$prevSuppId = $supplierId;
	?>
	<input type="hidden" name="hidSupplierStockId_<?=$po."_".$p;?>" value="<?=$stockId;?>" >
	<input type="hidden" name="hidSupplierQuantity_<?=$po."_".$p;?>" value="<?=$qty;?>" >
	<input type="hidden" name="hidSupplierUnitPrice_<?=$po."_".$p;?>" value="<?=$untp;?>" >
	<input type="hidden" name="hidSupplierTotalPrice_<?=$po."_".$p;?>" value="<?=$totPrice;?>" >
	<td  nowrap  align='left' Style="padding-left:10px;"  class="listing-item" ><?=$stname;?></td>
	<td  nowrap  align='right' Style="padding-left:10px;"  class="listing-item" ><?=$untp;?>
	</td>
	<td class="listing-item" align='center' style="padding-left:2px; padding-right:2px;"><?=$qty ;?></td>
	<td class="listing-item" style="padding-left:2px; padding-right:2px;" align='right'><?=number_format($totPrice,2,".","");?></td>
	</tr>	
	<?
		if ($p == sizeof( $stockArray)) {
			if ($genPoId!=1)  $cp = 3;
			else $cp = 2;
			echo "<tr bgcolor='white' >
			<td colspan='$cp' class='fieldName'  align='right'><B>Total:</B> </td>
			<td class='fieldName' style='padding-left:2px; padding-right:2px;' align='right'><B>$subUnitPrice</B> </td>
			<input type='hidden' name='hidTotUnitPrice_$po' id='hidTotUnitPrice_$po' value='$subUnitPrice'>
			<input type='hidden' name='hidTotPrice_$po' id='hidTotPrice_$po' value='$subTotPrice'>
			<input type='hidden' name='hidTotQuantity_$po' id='hidTotQuantity_$po' value='$subTotalQty'>
			<td class='fieldName'  align='center' ><B>$subTotalQty</B></td>
			<td class='fieldName'  style='padding-left:2px; padding-right:2px;' align='right'><B>$subTotPrice</B></td>
			</tr>";
		}
	}
	$po++;
	}
	if ($genPoId!=1)  $cp = 3;
	else $cp = 2;
	?>
	</td>
	</tr>
	<tr bgcolor='white' align="center">
		<td colspan='<?=$cp;?>' align='right' class="listing-head" >Sub Total: </td>
		<td style='padding-left:2px; padding-right:2px;' align='right' class="listing-head"><span id='subTotalUP'></span></td>
		<td class="listing-head" style="padding-left:2px; padding-right:2px;"><span id='subTotalQTY'></span></td>
		<td class="listing-head" style='padding-left:2px; padding-right:2px;' align='right' ><span id='subTotalTTL'></span></td>
	</tr>	
	<input type="hidden" name='hidConf' id='hidConf'  value="">
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		calcTotalValues(document.frmAssignIngPOSupplier);
	//-->
	</SCRIPT>
	<?
		if ($genPoId==1) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		document.getElementById("hidConf").value = "Y";
		document.getElementById("cmdUpdateOrder").click();
	//-->
	</SCRIPT>
	<?
		}	
	?>
	</table>
	</td>
	</tr>
	<tr>
		<td colspan='2' height='10'  ></td>
	</tr>
	<tr>
		<td colspan='2'  align='center' >
			<input type="submit" name="cmdUpdateOrder" class="button" id="cmdUpdateOrder1"  value=" Update Orders " onClick="return validateIngPOId(document.frmAssignIngPOSupplier);">&nbsp;&nbsp;</td>
		</td>
	</tr>
							</table>
						</td>
					</tr>
				</table>

			</td>
		</tr>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			function checkAnyExist()
			{
				
			}
		//-->
		</SCRIPT>
		<?
			}
		?>
		
		</table>
		<input type='hidden' value="<?=$rt;?>" name="stockReportType">
		<input type='hidden' value="<?=$sd;?>" name="selDate">
		<!--<input type="hidden" name="isPoExist" id="isPoExist" value="">-->
	</form>
	<br /><br /><br /><br /><br />
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
