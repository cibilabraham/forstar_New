<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$userId		= $sessObj->getValue("userId");	
	$dateSelection  = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

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

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;	

	#Create multiple PO
	if ($p["cmdPO"]!="") {		
		$hidRowCount	=	$p["hidRowCount"];
		$count=0;
		for ($j=1; $j<=$hidRowCount; $j++) {
		 	$selPlanId = $p["planId_".$j];
			if ($selPlanId) {
				if ($selPlanId!="" && $count>0) $selPlan .=",";
				$selPlan	.= "$selPlanId";
				$count++;
			}
		}
		# Redirect to the Assign Ing PO		
		header("location:AssignIngPOSupplier.php?selItem=$selPlan");
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
		
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------		
/*
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
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
	}
*/
	// As on Current date any pending Production Planning

	$cDate 	= date("d/m/Y");
	#List all Purchase Order
	if ($cDate!="") {
		$currentDate	= mysqlDateFormat($cDate);

		#List all ProductionPlanning Records
		$productionPlanningRecords = $purchaseIntentObj->fetchAllPagingRecords($currentDate, $offset, $limit);
		$productionPlanningRecSize = sizeof($productionPlanningRecords);

		# Pagination
		$fetchAllPlannedProductionRecs = $purchaseIntentObj->fetchDateRangeRecords($currentDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllPlannedProductionRecs);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	



	if ($editMode)	$heading = $label_editPurchaseIntent;
	else 		$heading = $label_addPurchaseIntent;

	# On Load Print JS	
	$ON_LOAD_PRINT_JS	= "libjs/PurchaseIntent.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
		<form name="frmPurchaseIntent" action="PurchaseIntent.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseIntent.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionPlanning(document.frmPurchaseIntent);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseIntent.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionPlanning(document.frmPurchaseIntent);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidProductBatchId" value="<?=$editProductBatchId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
	<!--<tr>
		  <td colspan="2" nowrap>
					<table width="200">
						<tr>
                                                  <td class="fieldName" nowrap>*Date: </td>
                                                  <td class="listing-item">
							<input name="pDate" type="text" id="pDate" value="<?=$pDate?>" size="9" autoComplete="off">
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Product:</td>
                                                  <td class="listing-item">
						 <select name="selProduct" id="selProduct" onchange="<? if ($addMode) {?>this.form.submit();<? }  else {?>this.form.editId.value=<?=$editId?>; this.form.submit();<?}?>" <?=$optionDisabled?>>
						<option value="">-- Select --</option>
						<?
						foreach ($productMasterRecords as $pmr) {
							$productId	=	$pmr[0];
							$productCode	=	$pmr[1];
							$productName	=	$pmr[2];
							$selected = "";
							if ($selProduct==$productId) $selected = "Selected";
						?>
						<option value="<?=$productId?>" <?=$selected?>><?=$productName?></option>
						  <? }?>
                                                  </select></td>
                                                </tr>			
                                                          </table></td>
				  </tr>-->
				<tr><TD height="5"></TD></tr>
				<!--<tr>
					<TD>
						<table>
							<TR><TD>
							<table>							
							<tr><TD>	
							<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2" align="center">
						<TD class="listing-head"></TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Fixed</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Gravy</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"  bgcolor="orange">
						<input type="text" name="productRatePerPouch" id="productRatePerPouch" style="text-align:right;border:none; background-color:orange;font-weight:bold" readonly value="<?=$productRatePerPouch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerPouch" id="fishRatePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRatePerPouch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerPouch" id="gravyRatePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerPouch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow" nowrap>Gms per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">		
						<input type="text" size="6" style="text-align:right; border:none;background-color:lightblue;" name="productGmsPerPouch" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" onkeyup="getProductionPlanRatePerBatch();" autoComplete="off" readonly></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? //if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch=$p["fishGmsPerPouch"];?>
						<input type="text" size="4" style="text-align:right;background-color:lightblue; border:none;" name="fishGmsPerPouch" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" onkeyup="getProductionPlanRatePerBatch();" autoComplete="off" readonly></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyGmsPerPouch" id="gravyGmsPerPouch" style="text-align:right;border:none" readonly value="<?=$gravyGmsPerPouch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow" >% per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productPercentagePerPouch" id="productPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="fishPercentagePerPouch" id="fishPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyPercentagePerPouch" id="gravyPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyPercentagePerPouch?>" size="5">%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerKgPerBatch" id="productRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerKgPerBatch" id="fishRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerKgPerBatch" id="gravyRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerKgPerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Pouches per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">					
						<input type="text" size="6" style="text-align:right;" name="pouchPerBatch" id="pouchPerBatch" value="<?=$pouchPerBatch?>" onkeyup="ingQtyProportion();" onchange="ingQtyProportion();" autoComplete="off">
						<input type="hidden" size="6" style="text-align:right;" name="hidPouchPerBatch" id="hidPouchPerBatch" value="<?=$pouchPerBatch?>" readonly="true">
						</TD>
						<TD class="listing-item" align="center" colspan="2"></TD>	
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerBatch" id="productRatePerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerBatch" id="fishRatePerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerBatch" id="gravyRatePerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgPerBatch" id="productKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishKgPerBatch" id="fishKgPerBatch" style="text-align:right;border:none" readonly value="<?=$fishKgPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><input type="text" name="gravyKgPerBatch" id="gravyKgPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyKgPerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="productRawPercentagePerPouch" id="productRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productRawPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="fishRawPercentagePerPouch" id="fishRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRawPercentagePerPouch?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="gravyRawPercentagePerPouch" id="gravyRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRawPercentagePerPouch?>" size="5">%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Kg (in Pouch) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgInPouchPerBatch" id="productKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgInPouchPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishKgInPouchPerBatch" id="fishKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$fishKgInPouchPerBatch?>" size="5"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyKgInPouchPerBatch" id="gravyKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyKgInPouchPerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% Yield</TD>
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishPercentageYield" id="fishPercentageYield" style="text-align:right;border:none" readonly value="<?=$fishPercentageYield?>" size="5">%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyPercentageYield" id="gravyPercentageYield" style="text-align:right;border:none" readonly value="<?=$gravyPercentageYield?>" size="5">%</TD>
					</TR>
				</table>
				</TD></tr></table>

							</TD>
						<td></td>
						<td valign="top">
						
						</td>
						</TR>
						</table>
					</TD>
				</tr>-->
				<tr>
				  <td colspan="2" nowrap style="padding-left:2px; padding-right:2px;">
			   <?
			  if (sizeof($productRecs) > 0) {
				$j=0;
			  ?>
			<!--<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
			     <tr bgcolor="#f2f2f2" align="center">
                                   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Ingredient</td>
                  		   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Raw <br>Qty</td>
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Cleaned <br>Qty</td>	
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Fixed<br> Qty</td>
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Existing <br>Qty</td>				   
				   <td class="listing-head" style="padding-left:2px; padding-right:2px;">Qty</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Gms/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Wt/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Cost/<br>Pouch</td>				
                              </tr>
				<?
			 	foreach ($productRecs as $pr)	{
					$j++;
					$ingredientId	= $pr[2];
					/*$ingredientRec	= $ingredientMasterObj->find($ingredientId);
					$ingredientName	= stripSlash($ingredientRec[2]);			
					*/
					$ingredientName	= $pr[12];
					// Find Ingredient Rate
					$lastPrice  	= $pr[13];
					$declYield	= $pr[15];
					// Edit Mode
					if ($editProductBatchId) $rec = $productBatchRecs[$j-1];
								
					if ($p["editSelectionChange"]=='1') {
						$quantity	=	$rec[3];
					} else {
						$quantity	=	$pr[3];
					}

					if ($p["editSelectionChange"]=='1') {
						$fixedQtyChk	=	$rec[4];
					} else {
						$fixedQtyChk	=	$pr[4];
					}

					$checked = "";
					if ($fixedQtyChk=='Y') $checked= "Checked";

					if ($p["editSelectionChange"]=='1') {
						$fixedQty	=	$rec[5];
					} else {
						$fixedQty	=	$pr[5];
					}

					// Refer values
					if ($p["editSelectionChange"]=='1') {
						$percentagePerBatch = $rec[6];
					} else {
						$percentagePerBatch = $pr[6];
					}

					if ($p["editSelectionChange"]=='1') {
						$ratePerBatch	=	$rec[7];
					} else {
						$ratePerBatch	=	$pr[7];
					}

					if ($p["editSelectionChange"]=='1') {
						$ingGmsPerPouch	=	$rec[8];
					} else {
						$ingGmsPerPouch	=	$pr[8];
					}

					if ($p["editSelectionChange"]=='1') {
						$percentageWtPerPouch	=	$rec[9];
					} else {
						$percentageWtPerPouch	=	$pr[9];
					}

					if ($p["editSelectionChange"]=='1') {
						$ratePerPouch	=	$rec[10];
					} else {
						$ratePerPouch	=	$pr[10];
					}

					if ($p["editSelectionChange"]=='1') {
						$percentageCostPerPouch	= $rec[11];
					} else {
						$percentageCostPerPouch	= $pr[11];
					}

					if ($p["editSelectionChange"]=='1') {
						$cleanedQty	= $rec[12];
					} else {
						$cleanedQty	= $pr[14];
					}

					//Find the Existing
					$existingQty = $purchaseIntentObj->getTotalStockQty($ingredientId);

					$checked = "";
					if ($fixedQtyChk=='Y') $checked= "Checked";
					
				?>
                                <tr bgcolor="#FFFFFF" align="center">
                                	<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="left"><?=$ingredientName?>
						<input type="hidden" value="<?=$ingredientId?>" name="ingredientId_<?=$j?>" id="ingredientId_<?=$j?>"></td>
                                        <td style="padding-left:2px; padding-right:2px;">
						<input name="quantity_<?=$j?>" type="text" id="quantity_<?=$j?>" size="4" style="text-align:right;border:none;" value="<?=$quantity?>" readonly>
						<input name="hidQuantity_<?=$j?>" type="hidden" id="hidQuantity_<?=$j?>" size="4" style="text-align:right;" value="<?=$quantity?>">
						<input name="declYield_<?=$j?>" type="hidden" id="declYield_<?=$j?>" value="<?=$declYield;?>" size='4' style="text-align:right;border:none;" autoComplete="off" readonly>
					</td>
					<td style="padding-left:2px; padding-right:2px;">
						<input name="cleanedQty_<?=$j?>" type="text" id="cleanedQty_<?=$j?>" value="<?=$cleanedQty;?>" size="4" style="text-align:right;border:none;" autoComplete="off" readonly="true">
						<input name="hidCleanedQty_<?=$j?>_<?=$i?>" type="hidden" id="hidCleanedQty_<?=$m?>_<?=$i?>" size="4" style="text-align:right;" value="<?=$cleanedQty?>">
					</td>
					<td style="padding-left:2px; padding-right:2px;">
						<? if ($checked!="" ) {?> 
							<img src="images/y.gif">
						<? }?>
						<input name="fixedQtyChk_<?=$j?>" type="hidden" id="fixedQtyChk_<?=$j?>" value="<?=$fixedQtyChk?>" size="4" style="text-align:right">					
					</td>
					<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;">
						<input name="existingQty_<?=$j?>" type="text" id="existingQty_<?=$j?>" size="4" style="text-align:right; border:none;" value="<?=$existingQty?>">
					</td>
					<? if ($fixedQtyChk!="" || $editMode) {?>
				<td style="padding-left:2px; padding-right:2px;">
					<? if ($fixedQtyChk=='Y') {?> 
						<input name="fixedQty_<?=$j?>" type="text" id="fixedQty_<?=$j?>" value="<?=$fixedQty;?>" size="4" style="text-align:right;border:none;" onkeyup="getProductionPlanRatePerBatch();" readonly="true">
					<?}?>
				</td>
				<? }?>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentagePerBatch_<?=$j?>" id="percentagePerBatch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
				</td>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<input type="hidden" name="lastPrice_<?=$j?>" id="lastPrice_<?=$j?>" value="<?=$lastPrice?>">
					<input type="text" name="ratePerBatch_<?=$j?>" id="ratePerBatch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$ratePerBatch?>" size="5">
				</td>
				<td class="listing-item" nowrap style="padding-left:2px; padding-right:4px;" align="right">
					<input type="text" name="ingGmsPerPouch_<?=$j?>" id="ingGmsPerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$ingGmsPerPouch?>" size="5">
				</td>
                                <td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentageWtPerPouch_<?=$j?>" id="percentageWtPerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$percentageWtPerPouch?>" size="5">%
				</td>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<input type="text" name="ratePerPouch_<?=$j?>" id="ratePerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$ratePerPouch?>" size="5">
				</td>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentageCostPerPouch_<?=$j?>" id="percentageCostPerPouch_<?=$j?>" style="text-align:right;border:none" readonly value="<?=$percentageCostPerPouch?>" size="5">%
				</td>					
                                 </tr>
				<?
					 }
				?>
                                </table>-->
			  <? }?>
			  </td>
			    </tr>
				<input type="hidden" name="hidItemCount" id="hidItemCount" value="<?=$j;?>">
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseIntent.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionPlanning(document.frmPurchaseIntent);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PurchaseIntent.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionPlanning(document.frmPurchaseIntent);">&nbsp;&nbsp;												</td>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;PURCHASE INTENT   </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<!--table cellpadding="0" cellspacing="0">
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
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td></tr></table-->
			</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<tr align="center">
				<TD>
				<? if ($edit==true) { ?> 
					<input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validatePurchaseIntent(document.frmPurchaseIntent)" <? if ($productionPlanningRecSize<=0) {?> disabled="true"<? } ?>>
				<? }?>
				</TD>
			</tr>
			<!--<tr>
			<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionPlanningRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionPlanning.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
</td>
	</tr>-->
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
									<td width="1" ></td>
									<td colspan="2" >
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($productionPlanningRecords)>0) {
		$i	=	0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="5" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PurchaseIntent.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PurchaseIntent.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PurchaseIntent.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
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
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Pouches per Batch</td>
		<td width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'planId_'); " class="chkBox">
		</td>
	</tr>
		<?
		foreach ($productionPlanningRecords as $ppr) {
			$i++;
			$productionPlanId	= $ppr[0];
			$plannedDate		= dateFormat($ppr[1]);
			$productName		= $ppr[5];
			$numPouch		= $ppr[6];			
		?>
		<tr  bgcolor="WHITE">			
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$plannedDate;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
				<?=$numPouch?>
			</td>
			<td width="20">
				<input type="checkbox" name="planId_<?=$i;?>" id="planId_<?=$i;?>" value="<?=$productionPlanId;?>" class="chkBox">
			</td>
		</tr>
		<?
			}
		?>
			<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
			<input type="hidden" name="editId" value="<?=$editId?>">
			<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
         	<td colspan="5" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PurchaseIntent.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PurchaseIntent.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PurchaseIntent.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
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
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" >
	<input type="hidden" name="hidSelProduct" value="<?=$selProduct?>">	
	<input type="hidden" name="hidProductGmsPerPouch" id="hidProductGmsPerPouch" value="<?=$productGmsPerPouch?>">
	<input type="hidden" name="totalFixedFishQty" id="totalFixedFishQty" value="<?=$totalFixedFishQty?>">	
	</td>
	</tr>
	<tr>	
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
		<tr align="center">
				<TD>
				<? if ($edit==true) { ?> 
					<input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validatePurchaseIntent(document.frmPurchaseIntent)" <? if ($productionPlanningRecSize<=0) {?> disabled="true"<? } ?>>
				<? }?>
				</TD>
			</tr>
		<!--<tr>
			<td>
			<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionPlanningRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionPlanning.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
		</td>
		</tr>-->
		</table></td></tr>
		<tr>
			<td colspan="3" height="5" ></td>
		</tr>
		</table></td>
					</tr>
				</table>
				<!-- Form fields end   -->
		</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
		<input type="hidden" name="pendingPlanRecSize" id="pendingPlanRecSize" value="<?=sizeof($productionPlanningRecords);?>">
	</table>

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
			inputField  : "pDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "pDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<? if ($addMode || $editMode) {?>
	<script>
	// calc rate
	//getProductionPlanRatePerBatch();
		
	</script>	
	<? }?>
	</form>
	</td></tr>
	</table>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
