<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;

	$userId		=	$sessObj->getValue("userId");
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") $addMode = false;



	#Setting the Values
	if ($p["selProduct"]!="") $selProduct = $p["selProduct"];
	if ($p["selBatch"]!="") $selBatch = $p["selBatch"];


/*
	if ($p["batchNo"]!="") $batchNo = $p["batchNo"];
	
	if ($p["productGmsPerPouch"]!="") $productGmsPerPouch = $p["productGmsPerPouch"];
	if ($p["fishGmsPerPouch"]!="") $fishGmsPerPouch = $p["fishGmsPerPouch"];
	if ($p["pouchPerBatch"]!="") $pouchPerBatch = $p["pouchPerBatch"];

	#Add
	if ($p["cmdAdd"]!="" ) {
	
		$itemCount	=	$p["hidItemCount"];

		$batchNo = $p["batchNo"];
		$selProduct = $p["selProduct"];

		$productGmsPerPouch 	= $p["productGmsPerPouch"];
		$fishGmsPerPouch 	= $p["fishGmsPerPouch"];
	 	$pouchPerBatch 		= $p["pouchPerBatch"];

		if ($batchNo!="" && $selProduct!="") {

			$productBatchRecIns = $productDetailsObj->addProductBatch($batchNo, $selProduct, $productGmsPerPouch, $fishGmsPerPouch, $pouchPerBatch, $userId);
									
			$lastId = $databaseConnect->getLastInsertedId();
				
			for($i=1; $i<=$itemCount; $i++)
			{
				$ingredientId	=	$p["ingredientId_".$i];
				$quantity	=	trim($p["quantity_".$i]);

				if ($lastId!="" && $ingredientId!="" && $quantity!="") {
					$ingredientRecIns = $productDetailsObj->addIngredientRec($lastId, $ingredientId, $quantity);
				}
			}
		}

		if ($productBatchRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddProductBatch);
			$sessObj->createSession("nextPage",$url_afterAddProductBatch);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddProductBatch;
		}
		$productBatchRecIns		=	false;
	}
	
	
	# Edit a Record
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$productBatchRec	=	$productDetailsObj->find($editId);
		
		$editProductBatchId	=	$productBatchRec[0];

		$batchNo 		=  $productBatchRec[1];

		if ($p["editSelectionChange"]=='1' || $p["selProduct"]=="") {
			$selProduct 	=  $productBatchRec[2];
		} else {
			$selProduct 	=  $p["selProduct"];
		}

		$productGmsPerPouch 	= $productBatchRec[5];
	 	$fishGmsPerPouch 	= $productBatchRec[6];
		$pouchPerBatch 		= $productBatchRec[7];
			
		$productBatchRecs = $productDetailsObj->fetchAllIngredients($editProductBatchId);
	}


	#Update A Record
	if ($p["cmdSaveChange"]!="") {
		
		$productBatchId	=	$p["hidProductBatchId"];

		$itemCount	=	$p["hidItemCount"];

		$batchNo 	= $p["batchNo"];
		$selProduct 	= $p["selProduct"];

		$productGmsPerPouch 	= $p["productGmsPerPouch"];
		$fishGmsPerPouch 	= $p["fishGmsPerPouch"];
	 	$pouchPerBatch 		= $p["pouchPerBatch"];

		if ($productBatchId!="" && $batchNo!="" && $selProduct!="")
		{
			$productBatchRecUptd = $productDetailsObj->updateProductBatch($productBatchId, $batchNo, $selProduct, $productGmsPerPouch, $fishGmsPerPouch, $pouchPerBatch);
		
			#Delete First all records from Entry table
			$deleteIngredientItem = $productDetailsObj->deleteIngredientRecs($productBatchId);
			
			for ($i=1; $i<=$itemCount; $i++) {
				$ingredientId	=	$p["ingredientId_".$i];
				$quantity	=	trim($p["quantity_".$i]);

				if ($productBatchId!="" && $ingredientId!="" && $quantity!="") {
					$ingredientRecIns = $productDetailsObj->addIngredientRec($productBatchId, $ingredientId, $quantity);
				}
			}
		}
	
		if ($productBatchRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductBatchUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductBatch);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductBatchUpdate;
		}
		$productBatchRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$productBatchId	=	$p["delId_".$i];
			
			if ($productBatchId!="") {
				$deleteIngredientItem	=	$productDetailsObj->deleteIngredientRecs($productBatchId);
				$productBatchRecDel =	$productDetailsObj->deleteProductBatch($productBatchId);
			}
		}
		if ($productBatchRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductBatch);
			$sessObj->createSession("nextPage",$url_afterDelProductBatch);
		} else {
			$errDel	=	$msg_failDelProductBatch;
		}
		$productBatchRecDel	=	false;
	}

	#Fetch all Item
	if ($selProduct) $productIngredientRecs = $productMasterObj->fetchAllIngredients($selProduct);

	#List all Ingredient Receipt Note
	
	$productBatchRecords = $productDetailsObj->fetchAllRecords();
	$productBatchRecSize = sizeof($productBatchRecords);
*/


	if ($addMode || $editMode) {
		#List all Product Records
		$productMasterRecords = $productMasterObj->fetchAllRecords();
	}

	if ($selProduct) {
		$getProductBatchRecords = $productDetailsObj->getProductBatchRecords($selProduct);
		#Get ingredient Recs
		$productIngredientRecs = $productDetailsObj->fetchAllIngredients($selBatch);
		list($productGmsPerPouch, $fishGmsPerPouch, $pouchPerBatch) = $productDetailsObj->getProductBatchSummaryRec($selBatch);

		#Gms per Pouch
		$gravyGmsPerPouch = $productGmsPerPouch-$fishGmsPerPouch;

		#% per Pouch
		$fishPercentagePerPouch = number_format(($fishGmsPerPouch/$productGmsPerPouch)*100,0);
		$gravyPercentagePerPouch = number_format(($gravyGmsPerPouch/$productGmsPerPouch)*100,0);
		$productPercentagePerPouch = $fishPercentagePerPouch+$gravyPercentagePerPouch;


		#Rs. Per Batch
		$fishRatePerBatch = $productDetailsObj->getFishRatePerBatch($selBatch);
		$gravyRatePerBatch = $productDetailsObj->getGravyRatePerBatch($selBatch);
		$productRatePerBatch = $fishRatePerBatch+$gravyRatePerBatch;

		#Kg (Raw) per Batch
		$fishKgPerbatch  = $productDetailsObj->getfishKgPerbatch($selBatch);
		$gravyKgPerbatch  = $productDetailsObj->getGravyKgPerbatch($selBatch);
		$productKgPerbatch = $fishKgPerbatch+$gravyKgPerbatch;

		#% (Raw) per Batch
		$fishRawPercentagePerPouch = number_format(($fishKgPerbatch/$productKgPerbatch)*100,0);
		$gravyRawPercentagePerPouch = number_format(($gravyKgPerbatch/$productKgPerbatch)*100,0);
		$productRawPercentagePerPouch = $fishRawPercentagePerPouch+$gravyRawPercentagePerPouch;

		#Kg (in Pouch) per Batch
		//echo "$fishGmsPerPouch*$pouchPerBatch";
		$fishKgInPouchPerBatch = ceil($fishGmsPerPouch * $pouchPerBatch);
		$gravyKgInPouchPerBatch = ceil($gravyGmsPerPouch * $pouchPerBatch);
		$productKgInPouchPerBatch = $fishKgInPouchPerBatch +$gravyKgInPouchPerBatch;

		#Rs. Per Kg per Batch
		//echo "$fishRatePerBatch/$fishKgInPouchPerBatch";
		$fishRatePerKgPerbatch = number_format(abs($fishRatePerBatch/$fishKgInPouchPerBatch),3,'.','');
		$gravyRatePerKgPerbatch = number_format(abs($gravyRatePerBatch/$gravyKgInPouchPerBatch),3,'.','');
		$productRatePerKgPerbatch = $fishRatePerKgPerbatch+$gravyRatePerKgPerbatch;

		
		#Rs. Per Pouch
		$fishRatePerPouch = number_format(($fishRatePerKgPerbatch*$fishGmsPerPouch),2);
		$gravyRatePerPouch = number_format(($gravyRatePerKgPerbatch*$gravyGmsPerPouch),2);
		$productRatePerPouch = number_format(($productRatePerBatch/$pouchPerBatch),2);

		#% Yield
		$fishPercentageYield = number_format(($fishKgInPouchPerBatch/$fishKgPerbatch),0);
		$gravyPercentageYield = number_format(($gravyKgInPouchPerBatch/$gravyKgPerbatch),0);
	}

	$ON_LOAD_PRINT_JS	= "libjs/ProductDetails.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProductDetails" action="ProductDetails.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Details</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
					</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center"><!--input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProductDetails.php?selBatch=<?=$selBatch?>&selProduct=<?=$selProduct?>',700,600);"-->
 &nbsp;&nbsp;											</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidProductBatchId" value="<?=$editProductBatchId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
					<table width="200">
						<tr>
                                                  <td class="fieldName">*Product:</td>
                                                  <td class="listing-item">
						 <select name="selProduct" id="selProduct" onchange="this.form.submit();">
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
						<tr>
                                                  <td class="fieldName" nowrap>*Batch No: </td>
                                                  <td class="listing-item">
						 <select name="selBatch" id="selBatch" onchange="this.form.submit();">
						<option value="">-- Select --</option>
						<?
						foreach ($getProductBatchRecords as $pbr) {
							$batchId	=	$pbr[0];
							$batchNo	=	$pbr[1];
							$selected = "";
							if ($selBatch==$batchId) $selected = "Selected";
						?>
						<option value="<?=$batchId?>" <?=$selected?>><?=$batchNo?></option>
						  <? }?>
                                                  </select></td>
                                                </tr>

                                                                      <tr>
                                                                           <TD>
                                                                                <br>
                                                                           </TD>
                                                                           <TD>
                                                                           </TD>
                                                                      </tr>
                                                                 </table></td>
				  </tr>
			<?
			  if (sizeof($productIngredientRecs) > 0) {
				$i=0;
			  ?>

				<tr><TD valign="top">
				<table>
				<tr>
					<TD height="25">&nbsp;</TD>
				</tr>
				<TR><TD valign="top">
				<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2">
						<TD class="listing-head"></TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Gravy</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"  bgcolor="orange">
						<input type="text" name="productRatePerPouch" id="productRatePerPouch" style="text-align:right;border:none; background-color:orange;font-weight:bold" readonly value="<?=$productRatePerPouch?>" size="5"><strong><?//$productRatePerPouch?></strong></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerPouch" id="fishRatePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRatePerPouch?>" size="5"><?//$fishRatePerPouch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerPouch" id="gravyRatePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerPouch?>" size="5"><?//$gravyRatePerPouch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow" nowrap>Gms per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="4" style="text-align:right;" name="productGmsPerPouch" id="productGmsPerPouch" value="<?=$productGmsPerPouch?>" readonly></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="4" style="text-align:right;" name="fishGmsPerPouch" id="fishGmsPerPouch" value="<?=$fishGmsPerPouch?>" readonly></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyGmsPerPouch" id="gravyGmsPerPouch" style="text-align:right;border:none" readonly value="<?=$gravyGmsPerPouch?>" size="5"><?//$gravyGmsPerPouch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow" >% per Pouch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productPercentagePerPouch" id="productPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productPercentagePerPouch?>" size="5"><?//$productPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="fishPercentagePerPouch" id="fishPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishPercentagePerPouch?>" size="5"><?//$fishPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyPercentagePerPouch" id="gravyPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyPercentagePerPouch?>" size="5"><?//$gravyPercentagePerPouch?>%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerKgPerBatch" id="productRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerKgPerbatch?>" size="5"><?//$productRatePerKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerKgPerBatch" id="fishRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerKgPerbatch?>" size="5"><?//$fishRatePerKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerKgPerBatch" id="gravyRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerKgPerbatch?>" size="5"><?//$gravyRatePerKgPerbatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Pouches per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="4" style="text-align:right;" name="pouchPerBatch" id="pouchPerBatch" value="<?=$pouchPerBatch?>" readonly></TD>
						<TD class="listing-item" colspan="2"></TD>
						<!--<TD class="listing-item"></TD>-->
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerBatch" id="productRatePerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerBatch?>" size="5"><?//$productRatePerBatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishRatePerBatch" id="fishRatePerBatch" style="text-align:right;border:none" readonly value="<?=$fishRatePerBatch?>" size="5"><?//$fishRatePerBatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyRatePerBatch" id="gravyRatePerBatch" style="text-align:right;border:none" readonly value="<?=$gravyRatePerBatch?>" size="5"><?//$gravyRatePerBatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgPerbatch" id="productKgPerbatch" style="text-align:right;border:none" readonly value="<?=$productKgPerbatch?>" size="5">
						<?//$productKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishKgPerbatch" id="fishKgPerbatch" style="text-align:right;border:none" readonly value="<?=$fishKgPerbatch?>" size="5"><?//$fishKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><input type="text" name="gravyKgPerbatch" id="gravyKgPerbatch" style="text-align:right;border:none" readonly value="<?=$gravyKgPerbatch?>" size="5"><?//$gravyKgPerbatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="productRawPercentagePerPouch" id="productRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$productRawPercentagePerPouch?>" size="5"><?//$productRawPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="fishRawPercentagePerPouch" id="fishRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$fishRawPercentagePerPouch?>" size="5"><?//$fishRawPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
						<input type="text" name="gravyRawPercentagePerPouch" id="gravyRawPercentagePerPouch" style="text-align:right;border:none" readonly value="<?=$gravyRawPercentagePerPouch?>" size="5"><?//$gravyRawPercentagePerPouch?>%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Kg (in Pouch) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgInPouchPerBatch" id="productKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgInPouchPerBatch?>" size="5"><?//$productKgInPouchPerBatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishKgInPouchPerBatch" id="fishKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$fishKgInPouchPerBatch?>" size="5"><?//$fishKgInPouchPerBatch?></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyKgInPouchPerBatch" id="gravyKgInPouchPerBatch" style="text-align:right;border:none" readonly value="<?=$gravyKgInPouchPerBatch?>" size="5"><?//$gravyKgInPouchPerBatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% Yield</TD>
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="fishPercentageYield" id="fishPercentageYield" style="text-align:right;border:none" readonly value="<?=$fishPercentageYield?>" size="5"><?//$fishPercentageYield?>%</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="gravyPercentageYield" id="gravyPercentageYield" style="text-align:right;border:none" readonly value="<?=$gravyPercentageYield?>" size="5"><?//$gravyPercentageYield?>%</TD>
					</TR>
				</table>
				</TD></TR>
				</table>
				</TD>
				<td>
					<table>
						<tr>
							<TD></TD>
							<TD class="listing-head" align="center">For pouches</TD>
							<TD></TD>
							<TD nowrap class="listing-head" align="center">For Fish</TD>
						</tr>
						<TR>
							<td width="100"></td>
							<td valign="top">
					<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2">
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Gravy</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="orange"><strong><div id="pouchesProductRatePerPouch"></div></strong></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishRatePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyRatePerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="4" style="text-align:right;" name="pouchesProductGmsPerPouch" id="pouchesProductGmsPerPouch" onkeyup="calculateProductForPouch();" value="<?=$productGmsPerPouch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<input type="text" size="4" style="text-align:right;" bgcolor="lightblue" name="pouchesFishGmsPerPouch" id="pouchesFishGmsPerPouch" onkeyup="calculateProductForPouch();" value="<?=$fishGmsPerPouch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyGmsPerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap><div id="pouchesFishPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyPercentagePerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductRatePerKgPerbatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishRatePerKgPerbatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyRatePerKgPerbatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="4" style="text-align:right;" name="pouchesPerBatch" id="pouchesPerBatch" onkeyup="calculateProductForPouch();" value="<?=$pouchPerBatch?>"> </TD>
						<TD class="listing-item" colspan="2"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductRatePerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishRatePerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyRatePerBatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductKgPerbatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishKgPerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyKgPerBatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<div id="pouchesProductRawPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<div id="pouchesFishRawPercentagePerPouch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<div id="pouchesGravyRawPercentagePerPouch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesProductKgInPouchPerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishKgInPouchPerBatch"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyKgInPouchPerBatch"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesFishPercentageYield"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesGravyPercentageYield"></div></TD>
					</TR>
					</table>
					</td>
					<td></td>
					<!-- FOR FISH STARTS HERE -->
					<td valign="top">
					<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2">
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Gravy</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="orange"><strong><div id="productRatePerPouchForFish"></div></strong></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRatePerPouchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyRatePerPouchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<input type="text" size="4" style="text-align:right;" name="productGmsPerPouchForFish" id="productGmsPerPouchForFish" onkeyup="calculateProductForFish();" value="<?=$productGmsPerPouch?>">
						</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<input type="text" size="4" style="text-align:right;" bgcolor="lightblue" name="fishGmsPerPouchForFish" id="fishGmsPerPouchForFish" onkeyup="calculateProductForFish();" value="<?=$fishGmsPerPouch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyGmsPerPouchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productPercentagePerPouchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishPercentagePerPouchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyPercentagePerPouchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productRatePerKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRatePerKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyRatePerKgPerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchPerBatchForFish"></div></TD>
						<TD class="listing-item" colspan="2"></TD>
						<!--<TD class="listing-item"></TD>-->
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productRatePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRatePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"> <div id="gravyRatePerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishKgPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyKgPerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productRawPercentagePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishRawPercentagePerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyRawPercentagePerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="productKgInPouchPerBatchForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue"><input type="text" size="4" style="text-align:right;" name="fishKgInPouchPerBatchForFish" id="fishKgInPouchPerBatchForFish" onkeyup="calculateProductForFish();" value="<?=$fishKgInPouchPerBatch?>"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyKgInPouchPerBatchForFish"></div></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="fishPercentageYieldForFish"></div></TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="gravyPercentageYieldForFish"></div></TD>
					</TR>
				</table></td>
						</TR>
					</table>

				</td>
				</tr>
				<tr><TD height="10"></TD></tr>
				<tr>
				  <td colspan="2" nowrap>
			   <?
			 // if (sizeof($productIngredientRecs) > 0) {
			//	$i=0;
			  ?>
			<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
			     <tr bgcolor="#f2f2f2" align="center">
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">Ingredient</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Type</td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">Kg<br>(Raw)</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Gms/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Wt/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs/<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Cost/<br>Pouch</td>
				<td></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Kg/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs./<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Wt/<br>Pouch</td>
				<td></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Kg/<br>Batch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Rs./<br>Pouch</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">%Wt/<br>Pouch</td>
                              </tr>
				<?
			 	foreach ($productIngredientRecs as $pir)	{
					$i++;
					$ingredientId	= $pir[2];
					$ingredientName = $pir[5];
					$quantity	= $pir[3];
					//$ingType	= $pir[6];
					/*$displayIngType = "";
					if ($ingType=='S') $displayIngType = "F";
					else $displayIngType = "I";*/

					$lastPrice = $pir[4];
					$ratePerBatch = $quantity*$lastPrice;
					//$ratePerBatch = $pir[7];

					$fixedQty = $pir[8];	// Y/N
					$ingCategoryName = $pir[9];

					#%/Batch
					$percentagePerBatch = number_format(($quantity/$productKgPerbatch)*100,0);
					$gmsPerPouch = number_format(($quantity/$pouchPerBatch),3);
					$percentageWtPerPouch = number_format(($gmsPerPouch/$productGmsPerPouch)*100,2);
					$ratePerPouch = number_format(($ratePerBatch/$pouchPerBatch),2);
					$percentageCostPerPouch = number_format(($ratePerPouch/$productRatePerPouch)*100,0);
				?>
                                <tr bgcolor="#FFFFFF">
                                	<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="left"><?=$ingredientName?></td>
					<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$ingCategoryName?>
					<!--input type="hidden" name="ingType_<?=$i?>" id="ingType_<?=$i?>" value="<?=$ingType?>"-->
					<input type="hidden" name="fixedQty_<?=$i?>" id="fixedQty_<?=$i?>" value="<?=$fixedQty?>">
					</td>
                                        <td class="listing-item" style="padding-left:2spx; padding-right:2px;" align="right"><input type="text" name="quantity_<?=$i?>" id="quantity_<?=$i?>" value="<?=$quantity?>" style="text-align:right;" size="6" onkeyup="calculateProductForPouch();calculateProductForFish();calcRatePerBatch();"></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentagePerBatch_<?=$i?>" id="percentagePerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5"><?//$percentagePerBatch?>%</td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><?//$ratePerBatch?>
					<input type="hidden" name="lastPrice_<?=$i?>" id="lastPrice_<?=$i?>" value="<?=$lastPrice?>">
					<input type="text" name="ratePerBatch_<?=$i?>" id="ratePerBatch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$ratePerBatch?>" size="5"></td>
					<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="right">
					<input type="text" name="gmsPerPouch_<?=$i?>" id="gmsPerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$gmsPerPouch?>" size="5"><?//$gmsPerPouch?></td>
                                        <td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentageWtPerPouch_<?=$i?>" id="percentageWtPerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$percentageWtPerPouch?>" size="5"><?//$percentageWtPerPouch?>%</td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
					<input type="text" name="ratePerPouch_<?=$i?>" id="ratePerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$ratePerPouch?>" size="5"><?//$ratePerPouch?></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
					<input type="text" name="percentageCostPerPouch_<?=$i?>" id="percentageCostPerPouch_<?=$i?>" style="text-align:right;border:none" readonly value="<?=$percentageCostPerPouch?>" size="5"><?//$percentageCostPerPouch?>%</td>
					<td></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesKgPerBatch_<?=$i?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="pouchesRatePerBatch_<?=$i?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap><div id="pouchesWtPerBatch_<?=$i?>"></div></td>
					<td></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="kgPerBatchForFish_<?=$i?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="ratePerBatchForFish_<?=$i?>"></div></td>
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><div id="wtPerBatchForFish_<?=$i?>"></div></td>
					
                                 </tr>
				<?
					 }
				?>
                                </table>
			  <? }?>
			  </td>
			    </tr>
				<input type="hidden" name="hidItemCount" id="hidItemCount" value="<?=$i;?>">
				<input type="hidden" name="fishKgInPouchPerBatch" id="fishKgInPouchPerBatch" value="<?=$fishKgInPouchPerBatch?>">
				<input type="hidden" name="gravyKgInPouchPerBatch" id="gravyKgInPouchPerBatch" value="<?=$gravyKgInPouchPerBatch?>">				
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">					</td>
												
												<?} else{?>

												<td  colspan="2" align="center">

<!--input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProductDetails.php?selBatch=<?=$selBatch?>&selProduct=<?=$selProduct?>',700,600);"-->										</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
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
			<td></td>
		</tr>
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
<?
  if (sizeof($productIngredientRecs) > 0) {
  ?>
	<SCRIPT LANGUAGE="JavaScript">
	calculateProductForPouch();
	calculateProductForFish();
	</SCRIPT>
<? }?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
