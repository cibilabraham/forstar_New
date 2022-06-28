<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require("lib/IngredientPhysicalStock_ajax.php");
	
	$_SESSION['rownum'] = '';

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$addAnother	= false;
	$layer		= "";	
	$read="";

	$selection = "?pageNo=".$p["pageNo"]."&supplierFilter=".$p["supplierFilter"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) 
	{
		//echo "ACCESS DENIED";
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
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
	if ($p["cmdAddNew"]!="") $addMode	= true;	
	if ($p["cmdCancel"]!="") 
	{
		$addMode	= false;
		$editMode	= false;
	}

	if ($p["selSupplier"]!="") $selSupplierId = $p["selSupplier"];	
	if ($p["selIngredient"]!="") $selIngredient = $p["selIngredient"];
	
	# Insert a Rec	
	if ($p["cmdAdd"]!="" || $p["cmdAddAnother"]!="") 
	{	
		$searchMode		= $p["searchMode"];
		if ($searchMode!="") 
		{

			if($searchMode=='S')		
			{
				$selSupplier=$p["selSupplier"];
				$selIngredient=$p["selIngredient"];
				$expectedQuantity=$p["expectedQuantity"];
				$quantity=$p["quantity"];
				$differenceInQuantity=$p["differenceInQuantity"];
				$effectiveDate=mysqlDateFormat($p["effectiveDate"]);
				$supplierIngId=$p["supplierIngId"];
				$chkExist=$ingredientPhysicalStockObj->checkPhysicalStockExist($date,$userId);
				if(sizeof($chkExist)>0)
				{
					$lastId =$chkExist[0];
				}
				else
				{
					$physicalIng=$ingredientPhysicalStockObj->addPhysicalIngredient($effectiveDate,$userId);
					$lastId = $databaseConnect->getLastInsertedId();
				}
				$physicalIngQty=$ingredientPhysicalStockObj->addPhysicalIngredientQuantity($lastId,$selSupplier,$selIngredient,$quantity,$effectiveDate,$expectedQuantity,$differenceInQuantity);
				$supplierIngQty=$ingredientPhysicalStockObj->getSupplierQty($supplierIngId,$selSupplier,$selIngredient);
				//echo
				//die();
				$diff=$quantity-$supplierIngQty;
				if($diff!='' || $diff!='0')
				{
					$supplierIns	=	$ingredientPhysicalStockObj->addSupplierIng($supplierIngId,$diff,$effectiveDate,$lastId);
				}
			}
			else if($searchMode=='B')
			{
				$ingSize=$p["ingSize"];
				$bulkDate=mysqlDateFormat($p["bulkDate"]);
				$chkExist=$ingredientPhysicalStockObj->checkPhysicalStockExist($bulkDate,$userId);
				if(sizeof($chkExist)>0)
				{
					$lastId =$chkExist[0];
				}
				else
				{
					$physicalIng=$ingredientPhysicalStockObj->addPhysicalIngredient($bulkDate,$userId);
					$lastId = $databaseConnect->getLastInsertedId();
				}

				for($i=0; $i<$ingSize; $i++)
				{
					$ingId=$p["ingId_".$i];
					$supplierSize=$p["supplierSize_".$i];
					for($j=0; $j<$supplierSize; $j++)
					{
						$supplierId=$p["supplierId_".$i."_".$j];
						$expectedQuantity=$p["expectedQuantity_".$i."_".$j];
						$quantity=$p["quantity_".$i."_".$j];
						$supplierIngId=$p["supplierIngId_".$i."_".$j];
						$differenceInQuantity=$p["differenceInQuantity_".$i."_".$j];
						if($quantity!="")
						{
							$physicalIngQty=$ingredientPhysicalStockObj->addPhysicalIngredientQuantity($lastId,$supplierId,$ingId,$quantity,$bulkDate,$expectedQuantity,$differenceInQuantity);
							$supplierIngQty=$ingredientPhysicalStockObj->getSupplierQty($supplierIngId,$supplierId,$ingId);
							//die();
							$diff=$quantity-$supplierIngQty;
							if($diff!='' || $diff!='0')
							{
								$supplierIns	=	$ingredientPhysicalStockObj->addSupplierIng($supplierIngId,$diff,$bulkDate,$lastId);
							}
						}
					}
				}
			}
			if ($physicalIngQty) {
				$sessObj->createSession("displayMsg",$msg_succAddIngPhysicalStock);
				$sessObj->createSession("nextPage",$url_afterAddIngPhysicalStock.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddIngPhysicalStock;
			}
				$physicalIngQty		=	false;
				$addMode	=	false;
		}

	}	
	
	# Edit 	
	if ($p["editId"]!="" && $p["cmdCancel"]=="")
	{
		$editId			=	$p["editId"];
		$editDate			=	$p["editDate"];
		$editMode		=	true;
		$physicalIngredientRec	=	$ingredientPhysicalStockObj->find($editId);	
		//printr($supplierIngredientRec);
		$editSupplierIngredientId	= $physicalIngredientRec[0];				
		$editDate			=  $physicalIngredientRec[1];
		$userId = $physicalIngredientRec[2];	
		/*$physicalStockId	=	$ingredientPhysicalStockObj->findPhysicalId($editDate,$userId);		
		$physicalStock = $ingredientPhysicalStockObj->findPhysicalStock($physicalStockId);
		*/
		$physicalStock = $ingredientPhysicalStockObj->findPhysicalStock($editId);
		//printr($physicalStock);
	}


	#Update //phyStckEntryId_
	if ($p["cmdSaveChange"]!="")
	{	
		$ingSize=$p["ingSize"];
		$bulkDate=mysqlDateFormat($p["bulkDate"]);
		$chkExist=$ingredientPhysicalStockObj->checkPhysicalStockExist($bulkDate,$userId);
		if(sizeof($chkExist)>0)
		{
			$lastId =$chkExist[0];
		}
		else
		{
			$physicalIng=$ingredientPhysicalStockObj->addPhysicalIngredient($bulkDate,$userId);
			$lastId = $databaseConnect->getLastInsertedId();
		}

		for($i=0; $i<$ingSize; $i++)
		{
			$ingId=$p["ingId_".$i];
			$supplierSize=$p["supplierSize_".$i];
			for($j=0; $j<$supplierSize; $j++)
			{
				$supplierId=$p["supplierId_".$i."_".$j];
				$expectedQuantity=$p["expectedQuantity_".$i."_".$j];
				$quantity=$p["quantity_".$i."_".$j];
				$supplierIngId=$p["supplierIngId_".$i."_".$j];
				$differenceInQuantity=$p["differenceInQuantity_".$i."_".$j];
				$phyStckEntryId=$p["phyStckEntryId_".$i."_".$j];
				if($quantity!="" && $phyStckEntryId=="")
				{
					$physicalIngQty=$ingredientPhysicalStockObj->addPhysicalIngredientQuantity($lastId,$supplierId,$ingId,$quantity,$bulkDate,$expectedQuantity,$differenceInQuantity);
					$supplierIngQty=$ingredientPhysicalStockObj->getSupplierQty($supplierIngId,$supplierId,$ingId);
					//die();
					$diff=$quantity-$supplierIngQty;
					if($diff!='' || $diff!='0')
					{
						$supplierIns	=	$ingredientPhysicalStockObj->addSupplierIng($supplierIngId,$diff,$bulkDate,$lastId);
					}
				}
				else if($quantity!="" && $phyStckEntryId!="")
				{
					$physicalIngQty=$ingredientPhysicalStockObj->updatePhysicalIngredientQuantity($phyStckEntryId,$supplierId,$ingId,$quantity,$bulkDate,$expectedQuantity,$differenceInQuantity);
					$supplierIngQty=$ingredientPhysicalStockObj->getSupplierQty($supplierIngId,$supplierId,$ingId);
					//die();
					$diff=$quantity-$supplierIngQty;
					if($diff!='' || $diff!='0')
					{
						$supplierIns	=	$ingredientPhysicalStockObj->addSupplierIng($supplierIngId,$diff,$bulkDate,$lastId);
					}
				}
			}
		}
		//die();
		if ($physicalIngQty) 
		{
			$sessObj->createSession("displayMsg",$msg_succIngPhysicalStockUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateIngPhysicalStock.$selection);
			$editMode = false;
		} 
		else 
		{
			$editMode	=	true;
			if ($uniqueRecords) $err = $msg_failIngPhysicalStockUpdate;
			else $err		=	$msg_failIngPhysicalStockUpdate;			
		}
		$physicalIngQty	=	false;
	}
		

	# Delete Supplier Stock
	if ($p["cmdDelete"]!="")
	{
		$supplierIngUsed = false;
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$physicalId			= $p["delId_".$i];
			if ($physicalId!=""  ) 
			{	
				$physicalIngredientRecDel = $ingredientPhysicalStockObj->deletePhysicalIngredient($physicalId);
				$physicalIngredientEntryRecDel = $ingredientPhysicalStockObj->deletePhysicalIngredientEntry($physicalId);
				$supplierIngredientRecQtyDel = $ingredientPhysicalStockObj->deleteSupplierIngredientQty($physicalId);
			}
		}
		if ($physicalIngredientRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelIngPhysicalStock);
			$sessObj->createSession("nextPage",$url_afterUpdateIngPhysicalStock.$selection);
		} 
		
		$physicalIngredientRecDel	=	false;
	}
		
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$ingPhysicalId	=	$p["confirmId"];
			$physicalIngredientRec	=	$ingredientPhysicalStockObj->find($ingPhysicalId);	
			//printr($supplierIngredientRec);
			$editSupplierIngredientId	= $physicalIngredientRec[0];				
			$editDate			=  $physicalIngredientRec[1];
			$userId = $physicalIngredientRec[2];	
			$physicalStockId	=	$ingredientPhysicalStockObj->findPhysicalId($editDate,$userId);	
			
			if ($physicalStockId!="") 
			{
				// Checking the selected fish is link with any other process
				$physicalRecConfirm = $ingredientPhysicalStockObj-> updatePhysicalIngredientConfirm($physicalStockId);
				$supplierIngRecConfirm = $ingredientPhysicalStockObj-> updateSupplierIngredientsConfirm($physicalStockId);
			}
		}
		if ($physicalRecConfirm) 
		{
			$sessObj->createSession("displayMsg",$msg_succConfirmingPhysicalStock);
			$sessObj->createSession("nextPage",$url_afterDelIngPhysicalStock.$selection);
		} else {
				$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$ingPhysicalId	=	$p["confirmId"];
			$physicalIngredientRec	=	$ingredientPhysicalStockObj->find($ingPhysicalId);	
			//printr($supplierIngredientRec);
			$editSupplierIngredientId	= $physicalIngredientRec[0];				
			$editDate			=  $physicalIngredientRec[1];
			$userId = $physicalIngredientRec[2];	
			$physicalStockId	=	$ingredientPhysicalStockObj->findPhysicalId($editDate,$userId);	
			if ($physicalStockId!="") {
				#Check any entries exist
				$ingPhysicalRecConfirm = $ingredientPhysicalStockObj->updatePhysicalIngredientReleaseconfirm($physicalStockId);
				$supplierIngRecConfirm = $ingredientPhysicalStockObj-> updateSupplierIngredientsReleaseConfirm($physicalStockId);
			}
		}
		if ($ingPhysicalRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmingPhysicalStock);
			$sessObj->createSession("nextPage",$url_afterDelIngPhysicalStock.$selection);
		} 
		else 
		{
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!= "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------

	if ($g["supplierFilter"]!="") $supplierFilterId = $g["supplierFilter"];
	else $supplierFilterId = $p["supplierFilter"];	

	# Resettting offset values
	if ($p["hidSupplierFilterId"]!=$p["supplierFilter"]) {		
		$offset = 0;
		$pageNo = 1;			
	} 	

	if($p["searchMode"]!="")
	{
		$searchMode=$p["searchMode"];
		//echo "hii".$searchMode;
		($searchMode=="S")?$SsearchMode='checked':$SsearchMode='';
		($searchMode=="B")?$BsearchMode='checked':$BsearchMode='';
	}

	#List all Supplier Ingredient
	$supplierIngredientRecords	= $ingredientPhysicalStockObj->fetchAllPagingRecords($offset, $limit, $supplierFilterId);
	$supplierIngredientSize	= sizeof($supplierIngredientRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($ingredientPhysicalStockObj->getAllRecords($supplierFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# List all Supplier
	$supplierRecords	= $supplierMasterObj->fetchAllRecordsActivesupplier("RTE");
		
	#List all Ingredients
	//$ingredientResultSetObj = $ingredientMasterObj->fetchAllRecords();
	$ingredientRecords	= $ingredientPhysicalStockObj->fetchAllIngredientRecords();
	$ingredientResultSetObj = $ingredientMasterObj->fetchAllRecords();
	//$displaySize = ceil($ingredientResultSetObj->getNumRows()/5);
	$displaySize = ceil(sizeof($ingredientResultSetObj)/5);

		
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	if ($editMode)	$heading	= $label_editIngPhysicalStock;
	else		$heading	= $label_addIngPhysicalStock;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/IngredientPhysicalStock.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	/*$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else*/ 
	require("template/btopLeftNav.php");
	?>

<form name="frmIngredientPhysicalStock" action="IngredientPhysicalStock.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<td height="10" align="center">&nbsp;</td>
		</tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>
		</tr>
		<?}?>
		<tr>
			<td align="center">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
						<?	
							$bxHeader="Ingredient Physical Stock";
							include "template/boxTL.php";
						?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<Table width="60%">
										<?
											if ($editMode || $addMode) {
										?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%">
														<tr>
															<td>
																<!-- Form fields start -->
																<?php							
																	$entryHead = $heading;
																	require("template/rbTop.php");
																?>
																<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																	<!--<tr>
																		<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
																		<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
																	</tr>-->
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
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPhysicalStock.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientPhysical(document.frmIngredientPhysicalStock);">												
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center" nowrap="true">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPhysicalStock.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Save & Exit "  onClick="return validateIngredientPhysical(document.frmIngredientPhysicalStock);" title="Save and Close"> &nbsp;&nbsp;<!--<input type="submit" name="cmdAddAnother" class="button" value=" Save & Add Another " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">-->												
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidSupplierIngredientId" value="<?=$editSupplierIngredientId;?>">
																				<tr>
																					<td nowrap height="10">&nbsp;</td>
																				</tr>
																				<tr>
																					<td colspan="3">
																					<? if(!$editMode) { ?>
																						<?php			
																						$entryHead = "";
																						//$rbTopWidth = "35%";
																						require("template/rbTop.php");
																						?>
																						<table >
																							<TR>
																								<TD  align="center" >
																									<table cellpadding="4" cellspacing="0" bgcolor="#fff">
																										<tr>
																											<td nowrap="nowrap">
																												<table cellpadding="0" cellspacing="0" >
																													<tr>
																														<td class="listing-item">
																															<input type="radio" name="searchMode" id="searchMode" value="S" onclick="this.form.submit();" <?=$SsearchMode?>/> Single entry
																														</td>
																														<td>&nbsp;</td>
																														<td  class="listing-item">
																															<input type="radio" name="searchMode" id="searchMode" value="B" onclick="this.form.submit();" <?=$BsearchMode?>/>Bulk entry
																														</td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																								<td>
																							</tr>
																						</table>
																						<?php
																						require("template/rbBottom.php");
																						?>
																						<? } ?>
																					</td>
																				</tr>
																				<tr>
																					<td nowrap height="10">&nbsp;</td>
																				</tr>
																				<? if($searchMode=="S"  || ($addMode && $searchMode!="B" && $searchMode!="") )
																				{
																				?>
																				<tr>
																					<td colspan="2" nowrap>
																						<table width="200" align="center">
																							<tr>
																								<td colspan="2" valign="top">
																									<table>
																										<tr>
																											<td class="fieldName" nowrap>*Supplier</td>
																											<td>
																												<select name="selSupplier" id="selSupplier" onchange="xajax_getSupplier(this.value);">
																													<option value="">--select--</option>
																													<?						  
																													foreach($supplierRecords as $sr) {
																														$supplierId			=	$sr[0];
																														$supplierCode			=	stripSlash($sr[1]);
																														$supplierName			=	stripSlash($sr[2]);
																														$selected ="";
																														if($selSupplierId==$supplierId) $selected="selected";
																													?>
																													<option value="<?=$supplierId?>" <?=$selected;?>>
																													<?=$supplierName?>
																													</option>
																													<? }?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap>*Ingredient</td>
																											<td>
																												<select name="selIngredient" id="selIngredient" onchange="xajax_getIngredientSupplierId(document.getElementById('selIngredient').value,document.getElementById('selSupplier').value);" >
																													<option value="">-- Select --</option>
																													<?					
																													foreach($ingredientResultSetObj as $kVal=>$ir) {
																														$ingredientId = $ir[0];
																														$ingredientCode	= stripSlash($ir[1]);
																														$ingredientName	= stripSlash($ir[2]);
																														# While Sel Supplier
																														//$sIngredientId = "";
																														//$sIngredientId = $ir[3];
																														$selected = ($selIngredient==$ingredientId && $ingredientId!="")?"selected":"";
																														
																													?>
																													<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
																													<? }?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td  class="fieldName" nowrap>*Expected Quantity</td>
																											<td><input name="expectedQuantity" id="expectedQuantity" value="<?=$expectedQuantity?>"  size="10" readonly /></td>
																										</tr>
																										<tr>
																											<td  class="fieldName" nowrap>*Verified Quantity</td>
																											<td><input name="quantity" id="quantity" value="<?=$quantity?>"  size="10" onkeyup="getDifference();"/></td>
																										</tr>
																										<tr>
																											<td  class="fieldName" nowrap>*Difference</td>
																											<td><input name="differenceInQuantity" id="differenceInQuantity" value="<?=$differenceInQuantity?>"  size="10"/></td>
																										</tr>
																										<tr>
																											<td  class="fieldName" nowrap>*Date</td>
																											<td ><input name="effectiveDate" id="effectiveDate" value="<?=$effectiveDate?>"  <?=$read?>size="10"/></td>
																										</tr>
																										<input type="hidden" name="supplierIngId" id="supplierIngId" value="<?=$supplierIngId?>"/>
																										<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>"/>
																									</table>										
																								</tr>
																							</table>
																						</td>
																				  </tr>
																				  <? 
																				} 
																				else if($searchMode=="B" || $editMode)
																				{
																					$bulkDate=dateFormat($editDate);
																				?>
																					
																				<tr>
																					<td>
																						<table width="200" >
																							<tr>
																								<td  class="listing-item" nowrap>*Date</td>
																								<td ><input name="bulkDate" id="bulkDate" value="<?=$bulkDate?>" size="10" onchange="getIngredientPhysicalStock(this.value,document.getElementById('ingSize').value);"/></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td bgcolor="#CCC">
																						<table cellpadding="6" cellspacing="1"  align="center">
																							<tr >
																								<td class="listing-head"  style="background-color:#f2f2f2">Ingredient</td>
																								<td class="listing-head"  style="background-color:#f2f2f2"></td>
																							</tr>
																							<? 
																							$i=0;
																							foreach($ingredientRecords as $ingRec)
																							{
																								$ingId=$ingRec[0];
																								$ingName=$ingRec[1];
																								$supplierIngRec= $ingredientPhysicalStockObj->getSupplierIng($ingId);
																							?>
																							<tr>
																								<td class="listing-item"  style="background-color:#ffffff"><?=$ingName?>
																								<input type="hidden" name="ingId_<?=$i?>" id="ingId_<?=$i?>"  value="<?=$ingId?>" />
																								</td>
																								<td class="listing-item"  style="background-color:#ffffff">	
																									<table bgcolor="#CCC" cellpadding="4" cellspacing="1"  align="center">
																									<?
																									if(sizeof($supplierIngRec)>0)
																									{
																									?>
																										<tr>
																											<td class="listing-head" >Supplier</td>
																											<td class="listing-head" >Expected Quantity</td>
																											<td class="listing-head" >Verified Quantity</td>
																											<td class="listing-head">Difference</td>
																										</tr>
																									<?php
																									$j=0;
																									foreach($supplierIngRec as $supp)
																									{
																										$supplierId= $supp[0];
																										$supplierName= $supp[1];
																										$supplierIngId= $supp[2];
																										$expectedQuantity= $ingredientPhysicalStockObj->getSupplierQty($supplierIngId,$supplierId,$ingId);
																										//$expectedQuantity=""; 
																										$quantity=""; $differenceInQuantity=""; $phyStckEntryId="";
																										foreach($physicalStock  as $phyStck)
																										{
																											$suppId= $phyStck[1];
																											$ingredientId= $phyStck[2];
																											//echo $supplierId.','.$suppId.','.$ingId.','.$ingredientId.'<br/>';	
																											if($supplierId==$suppId && $ingId==$ingredientId)
																											{
																												$phyStckEntryId = $phyStck[0];
																												$expectedQuantity= $phyStck[3];
																												$quantity= $phyStck[4];
																												$differenceInQuantity= $phyStck[5];
																												//echo "hiii".$expectedQuantity.'<br/>';
																											}
																										}
																										?>
																										<tr>
																											<td style="background-color:#ffffff" class="listing-item" >
																											<?=$supplierName?>
																											</td>
																											<td style="background-color:#ffffff" class="listing-item" >
																											<?=$expectedQuantity?>
																											<input  type="hidden" name="expectedQuantity_<?=$i?>_<?=$j?>" id="expectedQuantity_<?=$i?>_<?=$j?>" value="<?=$expectedQuantity?>" size="10"/>
																											</td>
																											<td style="background-color:#ffffff" class="listing-item" >
																												<input  type="text" name="quantity_<?=$i?>_<?=$j?>" id="quantity_<?=$i?>_<?=$j?>" value="<?=$quantity?>" size="10"  onkeyup="getQtyDifference(<?=$i?>,<?=$j?>);"/>
																												<input  type="hidden" name="supplierId_<?=$i?>_<?=$j?>" id="supplierId_<?=$i?>_<?=$j?>" value="<?=$supplierId?>" />
																												<input  type="hidden" name="supplierIngId_<?=$i?>_<?=$j?>" id="supplierIngId_<?=$i?>_<?=$j?>" value="<?=$supplierIngId?>" />
																											</td>
																											<td style="background-color:#ffffff" class="listing-item" >
																												<input  type="text" name="differenceInQuantity_<?=$i?>_<?=$j?>" id="differenceInQuantity_<?=$i?>_<?=$j?>" value="<?=$differenceInQuantity?>" size="10"/>
																											</td>
																											<input  type="hidden" name="phyStckEntryId_<?=$i?>_<?=$j?>" id="phyStckEntryId_<?=$i?>_<?=$j?>" value="<?=$phyStckEntryId?>"/>
																										</tr>
																									<?
																									$j++;
																									}
																									?>
																									<input type="hidden" id="supplierSize_<?=$i?>" name="supplierSize_<?=$i?>" value="<?=$j?>"/>
																									<?
																									}
																									?>
																									</table>
																								</td>
																							</tr>
																							<? 
																							$i++;
																							}
																							?>
																						</table>
																						<input type="hidden" name="ingSize" id="ingSize" value="<?=$i?>"/>
																					</td>
																				</tr>
																				<? 
																				}
																				?>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center" nowrap="true">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPhysicalStock.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes "  onClick="return validateIngredientPhysical(document.frmIngredientPhysicalStock);">												
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientPhysicalStock.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Save & Exit "  onClick="return validateIngredientPhysical(document.frmIngredientPhysicalStock);" title="Save and Close">&nbsp;&nbsp;<!--<input type="submit" name="cmdAddAnother" class="button" value=" Save & Add Another " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">	-->											
																					</td>
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
																<?php
																	require("template/rbBottom.php");
																?>
															</td>
														</tr>
													</table>
													<!-- Form fields end   -->	
												</td>
											</tr>	
											<?
											}			
											# Listing Category Starts
											?>
										</table>
									</td>
								</tr>		
								<tr>
									<td height="10" align="center" ></td>
								</tr>
								<tr>
									<td colspan="3" align="center">
									</td>
								</tr>
								<tr><TD height="10"></TD></tr>			
								<? if (!$newRateListCreated) { ?>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<TR>
									<TD nowrap="true" colspan="3" align="center">
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierIngredientSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientPhysicalStock.php',700,600);"><? }?>
									</TD>
								</TR>
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
									<td colspan="2" style="padding-left:10px;padding-right:10px;" >
										<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" id="newspaper-b1">
										<?
										if (sizeof($supplierIngredientRecords)>0) {
											$i	=	0;
										?>
											<thead>
											<? if($maxpage>1){?>
												<tr>
													<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
														<div align="right">
														<?php
														$nav  = '';
														for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"IngredientPhysicalStock.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"IngredientPhysicalStock.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"IngredientPhysicalStock.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
														} else {
															$next = '&nbsp;'; // we're on the last page, don't print next link
															$last = '&nbsp;'; // nor the last page link
														}
														// print the navigation link
														$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
														echo $first . $prev . $nav . $next . $last . $summary; 
														?>	
														<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
														</div> 
													</td>
												</tr>
												<? }?>
												<tr align="center">
													<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">Date</th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">User</th>
													<th class="listing-head">&nbsp;</th>
													<? if($edit==true){?>
															<th class="listing-head">&nbsp;</th>
													<? }?>
													<? if($confirm==true){?>
															<th class="listing-head">&nbsp;</th>
													<? }?>
												</tr>
											</thead>
											<tbody>
											<?
											$prevSupplierId=	0; $prevIngId=0;
											foreach($supplierIngredientRecords as $ssr) {
												$i++;
												$ingPhysicalStockId= $ssr[0];
												$startDate		= dateFormat($ssr[1]);
												//echo $startDate;
												$active=$ssr[4];
												$userId=$ssr[3];
												$userName= $userObj->getUserName($userId);
												//$ingPhysicalStock=$ingredientPhysicalStockObj->ingPhysicalStockQty($ingPhysicalStockId);
												($i>1)?$disabled="disabled":$disabled="";
											?>
											<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20">
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ingPhysicalStockId;?>" class="chkBox"  <?=$disabled?>>
													<input type="hidden" name="hidSupplierId_<?=$i;?>" id="hidSupplierId_<?=$i;?>" value="<?=$supplierId;?>">
													<input type="hidden" name="hidIngId_<?=$i;?>" id="hidIngId_<?=$i;?>" value="<?=$ingId;?>">			
												</td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<?=$startDate;?>
												</td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<?=$userName?>
												</td>
												<td class="listing-item" nowrap="" align="center" style="padding-left:5px; padding-right:5px; line-height:normal;">
													<a class="link1" title="Click here to View Invoice" href="javascript:printWindow('ViewIngredientPhysicalStock.php?ingPhysicalStockId=<?=$ingPhysicalStockId;?>',700,600)">VIEW</a>
												</td>
												<? if($edit==true){
												?>
												<td class="listing-item" width="60" align="center"><?php if ($active==0){ if($i<=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick=" assignValue(this.form,<?=$ingPhysicalStockId;?>,'editId');   assignValue(this.form,'1','editSelectionChange');  this.form.action='IngredientPhysicalStock.php';"><? }  }?></td>
												<?  } ?>
												<? if ($confirm==true){?>
												<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													<?php if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$ingPhysicalStockId;?>,'confirmId');"  >
													<?php } else if ($active==1){?>
													<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$ingPhysicalStockId;?>,'confirmId');"  >
													<?php }?>
													<? }?>
											</tr>
											<?
												
											}
											?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="<?=$editId?>">
											<input type="hidden" name="confirmId" value="">
											
											
											<input type="hidden" name="editSelectionChange" value="0">
											<? if($maxpage>1){?>
											<tr>
												<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
													<div align="right">
													<?php
													$nav  = '';
													for ($page=1; $page<=$maxpage; $page++)
													{
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"IngredientPhysicalStock.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"IngredientPhysicalStock.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"IngredientPhysicalStock.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
													} else {
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
													?>	
													<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
													</div> 
												</td>
											</tr>
											<? }?>
											<?
											}
											else
											{
											?>
											<tr>
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</tbody>
									</table>						
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<TR>
									<TD nowrap="true" colspan="3" align="center">
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierIngredientSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientPhysicalStock.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
									</TD>
							</TR>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						</table>	
						<?
						include "template/boxBR.php"
						?>		
					</td>
				</tr>
			</table>
	<!-- Form fields end   -->	
		</td>
	</tr>	
	<input type="hidden" name="hidSupplierFilterId" value="<?=$supplierFilterId?>">				
	<tr>
		<td height="10"></td>
	</tr>	
	<? }?>
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
</table>

<script>
Calendar.setup 
	(	
		{
			inputField  : "effectiveDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "effectiveDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

Calendar.setup 
	(	
		{
			inputField  : "bulkDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "bulkDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
</script>
<?php
//echo "hii".$iFrameVal;
	if ($iFrameVal=="") { 
?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	//ensureInFrameset(document.frmRecipeMaster);
	//-->
	</script>
<?php 
	}
?>	

</form>
<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>