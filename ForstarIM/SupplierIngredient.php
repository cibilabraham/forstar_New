<?php
	$insideIFrame = "Y";
	require("include/include.php");
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
	if (!$accesscontrolObj->canAccess()) {
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
	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode	= false;
	}

	if ($p["selSupplier"]!="") $selSupplierId = $p["selSupplier"];	
	if ($p["selIngredient"]!="") $selIngredient = $p["selIngredient"];
	
	# Insert a Rec	
	if ($p["cmdAdd"]!="" || $p["cmdAddAnother"]!="") 
	{	
		$selSupplierId		= $p["selSupplier"];
		$selIngredient		= $p["selIngredient"];
		//echo $selIngredient;
		//die();
		$rate		= $p["rate"];
		$quantity		= $p["quantity"];
		$effectiveDate=mysqlDateFormat($p["effectiveDate"]);
		# Check for unique recordschkValidDateEntry($startdate,"",$selSupplierId,$selStockId)
		$uniqueRecords = $supplierIngredientObj->chkUniqueRecords($effectiveDate,$selSupplierId, $selIngredient,"");	
		if (!$uniqueRecords)
		{	
			$chkGreaterRec = $supplierIngredientObj->chkGreaterStartDate($effectiveDate,$selSupplierId, $selIngredient,"");
			if(sizeof($chkGreaterRec)>0)
			{
				$supplierIngId=$chkGreaterRec[0];
				$date=$chkGreaterRec[1];
				$endDate = date('Y-m-d', strtotime($date .' -1 day'));
				//$updateSupplierIng=$supplierIngredientObj->updateSupplierIng($supplierIngId,$prevDate);
				$supplierIngredientRecIns	=	$supplierIngredientObj->addSupplierIngredient($selSupplierId, $selIngredient,$rate,$effectiveDate,$userId,$endDate);		
				$lastId = $databaseConnect->getLastInsertedId();
				$supplierIngredientQtyRecIns	=	$supplierIngredientObj->addSupplierIngredientQty($lastId, $quantity,$effectiveDate);		
				//echo $prev_date;
			}
			else
			{
				$chkLessRec = $supplierIngredientObj->chkLessStartDate($effectiveDate,$selSupplierId, $selIngredient,"");
				if(sizeof($chkLessRec)>0)
				{
					$supplierIngId=$chkLessRec[0];
					$endDate = date('Y-m-d', strtotime($effectiveDate .' -1 day'));
					$updateSupplierIng=$supplierIngredientObj->updateSupplierIng($supplierIngId,$endDate);
					$supplierIngredientRecIns	=	$supplierIngredientObj->addSupplierIngredient($selSupplierId, $selIngredient,$rate,$effectiveDate, $userId);		
					$lastId = $databaseConnect->getLastInsertedId();
					$supplierIngredientQtyRecIns	=	$supplierIngredientObj->addSupplierIngredientQty($lastId, $quantity,$effectiveDate);	
				}
				else
				{
					$supplierIngredientRecIns	=	$supplierIngredientObj->addSupplierIngredient($selSupplierId, $selIngredient,$rate,$effectiveDate, $userId);		
					$lastId = $databaseConnect->getLastInsertedId();
					$supplierIngredientQtyRecIns	=	$supplierIngredientObj->addSupplierIngredientQty($lastId, $quantity,$effectiveDate);	
				}
				
			}
			if ($supplierIngredientRecIns)
			{
				$addMode	=	false;				
				$sessObj->createSession("displayMsg",$msg_succAddSupplierIngredient);
				//$sessObj->createSession("nextPage",$url_afterAddSupplierIngredient.$selection);
				if ($p["cmdAddAnother"]!="") 
				{
					$addMode	=	true;
					$addAnother	=	true;
					$selSupplierId	=	"";
					$selIngredient	=	"";					
				} 
				else if ($p["cmdAdd"]!="") 
				{
					$sessObj->createSession("nextPage",$url_afterAddSupplierIngredient.$selection);
					$selSupplierId	=	"";
				}
			} 
			else 
			{
				$addMode	=	true;
				$err		=	$msg_failAddSupplierIngredient;
			}
			$supplierIngredientRecIns		=	false;
		} 
		else {
			$addMode	=	true;
			if ($uniqueRecords) $err = $msg_failAddSupplierIngredient."<br>".$msgFailSupplierIngExist;
			//$err		=	$msg_failAddSupplierIngredient;
		}
		$uniqueRecords = false;
	}	
	
	# Edit 	
	if ($p["editId"]!="" && $p["cmdCancel"]=="")
	{
		$editId			=	$p["editId"];
		$editMode		=	true;
		$supplierIngredientRec	=	$supplierIngredientObj->find($editId);		
		$editSupplierIngredientId	= $supplierIngredientRec[0];				
		$selSupplierId			= $supplierIngredientRec[1];
		$sIngredientId			= $supplierIngredientRec[2];	
		$rate			= $supplierIngredientRec[3];	
		$effectiveDate			= dateFormat($supplierIngredientRec[4]);		
		$endDate			=  dateFormat($supplierIngredientRec[5]);	
		//echo $endDate	;
		($endDate!="00/00/0000")?$newEffectiveDate=$endDate:$newEffectiveDate="";
		$supplierIngredientQtyRec	=	$supplierIngredientObj->findQty($editId);		
		$quantity			= $supplierIngredientQtyRec[0];		
		$read="readonly=''";
		($endDate!="00/00/0000")?$readNew="readonly=''":$readNew="";
	}


	#Update 
	if ($p["cmdSaveChange"]!="")
	{		
		$supplierIngredientId	=	$p["hidSupplierIngredientId"];

		$selSupplierId		= $p["selSupplier"];
		$selIngredient		= $p["selIngredient"];
		
		$rate		= $p["rate"];
		$quantity		= $p["quantity"];
		$effectiveDate=mysqlDateFormat($p["effectiveDate"]);
		$newEffectiveDate=mysqlDateFormat($p["newEffectiveDate"]);
		$hidNewEffectiveDate=mysqlDateFormat($p["hidNewEffectiveDate"]);
		# Check for unique records
		//$uniqueRecords = $supplierIngredientObj->chkUniqueRecords($selSupplierId, $selIngredient, $supplierIngredientId);	
		//echo  $selSupplierId.','.$selIngredient.','.$newEffectiveDate;
		//die();
		if ($selSupplierId!="" && $selIngredient!="" && $newEffectiveDate!="") 
		{
				//echo "hii";
				if($hidNewEffectiveDate=="")
				{
					$uniqueRecords = $supplierIngredientObj->chkUniqueRecords($newEffectiveDate,$selSupplierId, $selIngredient,"");	
					if (!$uniqueRecords)
					{	
						$chkGreaterRec = $supplierIngredientObj->chkGreaterStartDate($newEffectiveDate,$selSupplierId, $selIngredient,"");
						if(sizeof($chkGreaterRec)>0)
						{
							$supplierIngId=$chkGreaterRec[0];
							$date=$chkGreaterRec[1];
							$endDate = date('Y-m-d', strtotime($date .' -1 day'));
							//$updateSupplierIng=$supplierIngredientObj->updateSupplierIng($supplierIngId,$prevDate);
							$supplierIngredientRecUptd	=	$supplierIngredientObj->addSupplierIngredient($selSupplierId, $selIngredient,$rate,$newEffectiveDate,$userId,$endDate);		
							$lastId = $databaseConnect->getLastInsertedId();
							$supplierIngredientQtyRecIns	=	$supplierIngredientObj->addSupplierIngredientQty($lastId, $quantity,$newEffectiveDate);		
							//echo $prev_date;
						}
						else
						{
							$chkLessRec = $supplierIngredientObj->chkLessStartDate($newEffectiveDate,$selSupplierId, $selIngredient,"");
							if(sizeof($chkLessRec)>0)
							{
								$supplierIngId=$chkLessRec[0];
								$endDate = date('Y-m-d', strtotime($newEffectiveDate .' -1 day'));
								$updateSupplierIng=$supplierIngredientObj->updateSupplierIng($supplierIngId,$endDate);
								$supplierIngredientRecUptd	=	$supplierIngredientObj->addSupplierIngredient($selSupplierId, $selIngredient,$rate,$newEffectiveDate, $userId);		
								$lastId = $databaseConnect->getLastInsertedId();
								$supplierIngredientQtyRecIns	=	$supplierIngredientObj->addSupplierIngredientQty($lastId, $quantity,$newEffectiveDate);	
							}
							
						}
					}
				}
				else if($hidNewEffectiveDate!="")
				{
						$supplierIngredientRecUptd = $supplierIngredientObj->updateSupplierIngredient($selSupplierId, $selIngredient,$rate,$supplierIngredientId	);
						$supplierIngredientQtyRecIns	=	$supplierIngredientObj->updateSupplierIngredientQty($supplierIngredientId, $quantity);		
				}
		}
		if ($supplierIngredientRecUptd) 
		{
				$sessObj->createSession("displayMsg",$msg_succSupplierIngredientUpdate);
				$sessObj->createSession("nextPage",$url_afterUpdateSupplierIngredient.$selection);
				$editMode = false;
		} 
		else 
		{
				$editMode	=	true;
				if ($uniqueRecords) $err = $msg_failSupplierIngredientUpdate."<br>".$msgFailSupplierIngExist;
				else $err		=	$msg_failSupplierIngredientUpdate;			
		}
		$supplierIngredientRecUptd	=	false;
	}
	

	# Delete Supplier Stock
	if ($p["cmdDelete"]!="") {
		$supplierIngUsed = false;
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierIngredientId	= $p["delId_".$i];			
			$supplierId		= $p["hidSupplierId_".$i];	
			$ingId			= $p["hidIngId_".$i];
			if ($supplierIngredientId!=""  ) {	
				$chkIng = $supplierIngredientObj->chkSupplierIngExist($supplierId, $ingId);
				if(!$chkIng)
				{
					$supplierIngredientRecDel = $supplierIngredientObj->deleteSupplierIngredient($supplierIngredientId);
					$supplierIngredientRecQtyDel = $supplierIngredientObj->deleteSupplierIngredientQty($supplierIngredientId);
				}
				//$supplierIngredientRecDel =	$supplierIngredientObj->deleteSupplierIngredient($supplierIngredientId);	
			}
		}
		if ($supplierIngredientRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplierIngredient);
			$sessObj->createSession("nextPage",$url_afterDelSupplierIngredient.$selection);
		} else {
			if ($supplierIngUsed) $errDel = $msg_failDelSupplierIngredient."<br>".$msgForUsingSupplierIng;
			else $errDel	=	$msg_failDelSupplierIngredient;
		}
		$supplierIngredientRecDel	=	false;
	}
	



	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingMainCategoryId	=	$p["confirmId"];


			if ($ingMainCategoryId!="") {
				// Checking the selected fish is link with any other process
				$ingMainCategoryRecConfirm = $supplierIngredientObj->updateSupplierIngredientconfirm($ingMainCategoryId);
			}

		}
		if ($ingMainCategoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmingSupplierIngredient);
			$sessObj->createSession("nextPage",$url_afterDelIngMainCategory.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$ingMainCategoryId= $p["confirmId"];
			if ($ingMainCategoryId!="") {
				#Check any entries exist
				$ingMainCategoryRecConfirm = $supplierIngredientObj->updateSupplierIngredientReleaseconfirm($ingMainCategoryId);
			}
		}
		if ($ingMainCategoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmingSupplierIngredient);
			$sessObj->createSession("nextPage",$url_afterDelIngMainCategory.$selection);
		} else {
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

	#List all Supplier Ingredient
	$supplierIngredientRecords	= $supplierIngredientObj->fetchAllPagingRecords($offset, $limit, $supplierFilterId);
	$supplierIngredientSize	= sizeof($supplierIngredientRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($supplierIngredientObj->getAllRecords($supplierFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# List all Supplier
	$supplierRecords	= $supplierMasterObj->fetchAllRecordsActivesupplier("RTE");
		
	#List all Ingredients
	$ingredientResultSetObj = $ingredientMasterObj->fetchAllRecords();
	/*
	if ($selSupplierId) {
		$ingredientResultSetObj = $supplierIngredientObj->fetchAllSelectedIngRecords($selSupplierId);
	} else {
		$ingredientResultSetObj = $ingredientMasterObj->fetchAllRecords();
	}
	*/
	//$displaySize = ceil($ingredientResultSetObj->getNumRows()/5);
	$displaySize = ceil(sizeof($ingredientResultSetObj)/5);

		
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/SupplierIngredient.js"; 

	if ($editMode)	$heading	= $label_editSupplierIngredient;
	else		$heading	= $label_addSupplierIngredient;
	
		
//$rest= $supplierIngredientObj->chkLessStartDate('2015-07-25','3', '83');
//echo $date=$rest[1];
//$prev_date = date('Y-m-d', strtotime($date .' -1 day'));
//echo $prev_date;
//printr($rest);
//echo sizeof($rest);


	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
<link rel="stylesheet" href="libjs/jquery-ui.css">
<script src="libjs/jquery/jquery-1.10.2.js"></script>
<script src="libjs/jquery/jquery-ui.js"></script>
<script src="libjs/moment.js"></script>
<form name="frmSupplierIngredient" action="SupplierIngredient.php" method="post">
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
							$bxHeader="Ingredient Suppliers";
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
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center" nowrap="true">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Save & Exit " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);" title="Save and Close"> &nbsp;&nbsp;<input type="submit" name="cmdAddAnother" class="button" value=" Save & Add Another " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidSupplierIngredientId" value="<?=$editSupplierIngredientId;?>">
											
																				<tr>
																					<td nowrap height="2">&nbsp;</td>
																				</tr>
																				<tr>
																					<td colspan="2" nowrap>
																						<table width="200" align="center">
																							<tr>
																								<td colspan="2" valign="top">
																									<table>
																										<tr>
																											<td class="fieldName">*Supplier</td>
																											<td>
																												<select name="selSupplier" onchange="this.form.submit();">
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
																											<td class="fieldName">*Ingredient</td>
																											<td>
																												<select name="selIngredient" id="selIngredient" >
																													<option value="">-- Select --</option>
																													<?					
																													foreach($ingredientResultSetObj as $kVal=>$ir) {
																														$ingredientId = $ir[0];
																														$ingredientCode	= stripSlash($ir[1]);
																														$ingredientName	= stripSlash($ir[2]);
																														# While Sel Supplier
																														//$sIngredientId = "";
																														//$sIngredientId = $ir[3];
																														$selected = ($sIngredientId==$ingredientId && $ingredientId!="")?"selected":"";
																														
																													?>
																													<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
																													<? }?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td  class="fieldName">*Rate</td>
																											<td ><input name="rate" id="rate" value="<?=$rate?>"  size="10"/></td>
																										</tr>
																										<tr>
																											<td  class="fieldName">*Quantity</td>
																											<td ><input name="quantity" id="quantity" value="<?=$quantity?>"  size="10"/></td>
																										</tr>
																										<tr>
																											<td  class="fieldName" nowrap>*Effective Date</td>
																											<td ><input name="effectiveDate" id="effectiveDate" value="<?=$effectiveDate?>"  <?=$read?>size="10"/></td>
																										</tr>
																										<? if($editMode){?>
																										<tr>
																											<td  class="fieldName" nowrap>*New Effective Date</td>
																											<td ><input name="newEffectiveDate" id="newEffectiveDate" class="newEffectiveDate" value="<?=$newEffectiveDate?>" size="10" <?if($readNew==""){?>  onmouseover="newEffective();" <? } ?><?=$readNew?>/><input  type="hidden" name="hidNewEffectiveDate" id="hidNewEffectiveDate"  value="<?=$newEffectiveDate?>" size="10" /></td>

																										</tr>
																										<? } ?>
																										<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>"/>
																									</table>										
																								</tr>
																							</table>
																						</td>
																				  </tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center" nowrap="true">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Save & Exit " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);" title="Save and Close">&nbsp;&nbsp;<input type="submit" name="cmdAddAnother" class="button" value=" Save & Add Another " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												
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
									<table width="25%">
										<TR>
											<TD>
											<?php			
												$entryHead = "";
												require("template/rbTop.php");
											?>
												<table cellpadding="4" cellspacing="0">
													<tr>
														<td nowrap="nowrap">
															<table cellpadding="0" cellspacing="0">
																<tr>
																	<td nowrap="nowrap">
																		<table cellpadding="0" cellspacing="0">
																			<tr>
																					<td class="listing-item">Supplier:&nbsp;</td>
																					 <td>
																						<select name="supplierFilter" id="supplierFilter" onchange="this.form.submit();">
																							<option value="">-- Select All --</option>
																							<?						  
																							foreach($supplierRecords as $sr) {
																								$supplierId		=	$sr[0];
																								$supplierCode		=	stripSlash($sr[1]);
																								$supplierName		=	stripSlash($sr[2]);
																								$selected = ($supplierFilterId==$supplierId)?"selected":"";
																							?>
																						   <option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName?></option>
																							<? }?>
																						</select> 
																				   </td>
																					<td class="listing-item">&nbsp;</td>
																					<td>&nbsp;</td>
																				</tr>
																			</table>
																		</td>
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
									</td>
								</tr>
								<tr><TD height="10"></TD></tr>			
								<? if (!$newRateListCreated) { ?>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<TR>
									<TD nowrap="true" colspan="3" align="center">
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierIngredientSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierIngredient.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
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
																$nav.= " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
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
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Ingredients</th>		
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Start Date</th>		
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
											$supplierIngredientId	= $ssr[0];
											$supplierId		= $ssr[1];
											$ingId			= $ssr[2];
											$ingName			= $ssr[4];
											$startDate		= dateFormat($ssr[6]);
										
											$supplierName		= "";
											if ($prevSupplierId!=$supplierId) 
											{
												$supplierName = stripSlash($ssr[3]);				
											}
										
											if ($prevSupplierId!=$supplierId || $prevIngId!=$ingId) 
											{
												$disable="";
											}
											else
											{
												$disable="disabled";
											}
									
											//echo $prevSupplierId.'--'.$supplierId.'--'.$prevIngId.'--'.$ingId.'<br/>';
											//$stockName		= $ssr[4];
											//$getSupplierWiseIngredients = $supplierIngredientObj->getIngreients($supplierId);
											$active=$ssr[5];
											 $supIngId=$supplierIngredientObj->chckSupplierIdInPo($supplierIngredientId);
										?>
											<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20">
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierIngredientId;?>" class="chkBox" <?=$disable?>>
													<input type="hidden" name="hidSupplierId_<?=$i;?>" id="hidSupplierId_<?=$i;?>" value="<?=$supplierId;?>">
													<input type="hidden" name="hidIngId_<?=$i;?>" id="hidIngId_<?=$i;?>" value="<?=$ingId;?>">			
												</td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
													<?=$ingName?>
												</td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
													<? if($startDate!='00/00/0000') {?><?=$startDate?> <? } ?>
												</td>	
													
												<? if($edit==true){?>
												<td class="listing-item" width="60" align="center"><?  if(!$supIngId) { ?><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierIngredientId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierIngredient.php';"><? }  }?></td>
												<? }?>
												<? if ($confirm==true){?>
												<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													<?php if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$supplierIngredientId;?>,'confirmId');"  >
													<?php } else if ($active==1){?>
													<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$supplierIngredientId;?>,'confirmId');"  >
													<?php }?>
													<? }?>
											</tr>
											<?
												$prevSupplierId=$supplierId;
												$prevIngId=$ingId;
											}
											?>
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
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
															$nav.= " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
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
								<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierIngredientSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierIngredient.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
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
<?php 
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
	//ensureInFrameset(document.frmSupplierIngredient);
	//-->
	</script>
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

</script>

<?php 
	}
?>
</form>
<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>