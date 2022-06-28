<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	//rekha added code
		if($_GET['srchName']!='')$srchName = $_GET['srchName'];
		else{
			if ($g["$srchName"]!="") $srchName = $g["$srchName"];
			else $srchName = $p["srchName"];
		}
	//echo $srchName;
	
	//end code 
	$selection 	= "?pageNo=".$p["pageNo"]."&categoryFilter=".$p["categoryFilter"]."&mainCategoryFilter=".$p["mainCategoryFilter"]."&srchName=".$p["srchName"];

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

	# Add Category Start 
	if ($p["cmdAddNew"]!="") {
		$addMode  =   true;
		list($ingredientMsg,$ingredientCode,$numbergenId)=$ingredientMasterObj->generateIngredientCode();
		$code=$ingredientCode;
		$err=$ingredientMsg;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode   =  false;
	}

	#Add a stock
	if ($p["cmdAdd"]!="") {
		
		$selCategory	= $p["category"];
		$code		= addSlash(trim($p["ingredientCode"]));
		$name		= addSlash(trim($p["ingredientName"]));
		$surName	= addSlash(trim($p["ingredientSurname"]));
		$descr		= addSlash(trim($p["description"]));
		$Packing_size = addSlash(trim($p["txtpackingsize"]));
		$materialType	= $p["ingMaterialType"];
		/*rekha added code */
			if($materialType=="2"){
				$selraw_ing	= $p["selraw_ing"];
				$yeild	= $p["yeild"];
				$clearing_cost	= $p["cleaning_cost"];
			
			}
		/*end code */
		
		$mainCategoryId = $p["ingMainCategory"];
		$numberGenId=$p["numberGenId"];
		$criticalSize=$p["criticalSize"];

		$ingredientExist = $ingredientMasterObj->checkIngredientExist($name);
		if($ingredientExist)
		{
			$err	 = $msg_duplicateIngredient;
			$addMode = true;
		}

		else if ($code!="" && $name!="" && $selCategory!="" && $mainCategoryId!="") 
		{

			//$ingredientRecIns = $ingredientMasterObj->addIngredient($selCategory, $code, $name, $surName, $descr, $userId, $materialType, $mainCategoryId,$numberGenId);
			//rekha update code 
			
			$ingredientRecIns = $ingredientMasterObj->addIngredient($selCategory, $code, $name, $surName, $descr, $userId, $materialType, $mainCategoryId,$numberGenId,$selraw_ing,$yeild,$clearing_cost,$Packing_size);
			// end code 
			
			$lastId = $databaseConnect->getLastInsertedId();
			preg_match('/\d+/', $code, $numMatch);
			$lastnum = $numMatch[0];
			$lastIdInsert	=$manageChallanObj->lastGeneratedProcurementId($lastnum,$numberGenId);
			for($i=0; $i<$criticalSize; $i++)
			{
				$parameterId=$p["parameterId_".$i];
				$status=$p["critical_".$i];
				$ctype=$p["ctype_".$i];
				if($ctype=="checkbox" || $ctype=="radio")
				{
					($status=="")?$status="N":$status=$status;
				}
				$ingredientRecIns = $ingredientMasterObj->addIngredientCritical($lastId, $parameterId,$status);
			}


			if ($ingredientRecIns)
			{
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddIngredients);
				$sessObj->createSession("nextPage",$url_afterAddIngredients.$selection);
			} 
			else 
			{
				$addMode = true;
				$err	 = $msg_failAddIngredients;
			}
			$ingredientRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$ingredientId	=	$p["hidIngredientId"];
		$selCategory	=	$p["category"];
		$code		=	addSlash(trim($p["ingredientCode"]));
		$name		=	addSlash(trim($p["ingredientName"]));
		$surName	=	addSlash(trim($p["ingredientSurname"]));
		$descr		=	addSlash(trim($p["description"]));
		$openingQty	= 	trim($p["openingQty"]);
		$hidExistingQty = 	trim($p["hidExistingQty"]);
		$mainCategoryId = $p["ingMainCategory"];	
		$numberGenId=$p["numberGenId"];
		$criticalSize=$p["criticalSize"];	
		$Packing_size = $p["txtpackingsize"];
		echo $Packing_size;
		$materialType = $p["ingMaterialType"];

		if ($ingredientId!="" && $name!="" && $code!="" && $selCategory!="") {
			$ingredientRecUptd = $ingredientMasterObj->updateIngredient($ingredientId, $selCategory, $code, $name, $surName, $descr, $openingQty, $hidExistingQty, $mainCategoryId, $materialType, $Packing_size);
			$ingredientCriticalRecUptd = $ingredientMasterObj->deleteCriticalData($ingredientId);
			for($i=0; $i<$criticalSize; $i++)
			{
				$parameterId=$p["parameterId_".$i];
				$status=$p["critical_".$i];
				$ctype=$p["ctype_".$i];
				if($ctype=="checkbox" || $ctype=="radio")
				{
					($status=="")?$status="N":$status=$status;
				}
				$ingredientRecIns = $ingredientMasterObj->addIngredientCritical($ingredientId, $parameterId,$status);
			}
		
		
		
		}
	
		if ($ingredientRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succIngredientsUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateIngredients.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failIngredientsUpdate;
		}
		$ingredientRecUptd	=	false;
	}


	# Edit  an ingredient
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$ingredientRec	=	$ingredientMasterObj->find($editId);
		$editIngredientId =	$ingredientRec[0];
		$code		=	stripSlash($ingredientRec[1]);
		$name		=	stripSlash($ingredientRec[2]);
		$surname	=	stripSlash($ingredientRec[3]);
		$selCategory	=	$ingredientRec[4];
		$descr		=	stripSlash($ingredientRec[5]);
		$openingQty	= 	$ingredientRec[6];
		$selMainCategoyId = 	$ingredientRec[7];
		$materialType = $ingredientRec[8];
		$selraw_ing = $ingredientRec[9];
		$yeild = $ingredientRec[10];
		//cmdEdit
		$clearing_cost = $ingredientRec[11];
		$Packing_size = $ingredientRec[12]; 
		$ingredientCriticalRec=$ingredientMasterObj->getCritical($editId);
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") 
	{

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientId	=	$p["delId_".$i];
			$existIngredientRateMaster = $ingredientMasterObj->checkIngredientRateMaster($ingredientId);
			$existPurchaseOrder = $ingredientMasterObj->checkPurchaseOrder($ingredientId);
			$existSupplierIngredient = $ingredientMasterObj->checkSupplierIngredient($ingredientId);
			$existRecipeMaster = $ingredientMasterObj->checkRecipeMaster($ingredientId);
			
			if ($ingredientId!="" && !$existIngredientRateMaster && $existPurchaseOrder=="" && $existSupplierIngredient=="" && $existRecipeMaster=="") 
			{				
				// Need to check the selected ing is link with any other process
				$ingredientRecDel = $ingredientMasterObj->deleteIngredient($ingredientId);
				$ingredientCriticalRecDel = $ingredientMasterObj->deleteCriticalData($ingredientId);
			}
		}
		if ($ingredientRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelIngredients);
			$sessObj->createSession("nextPage",$url_afterDelIngredients.$selection);
		} 
		else 
		{
			$errDel	=	$msg_failDelIngredients;
		}
		$ingredientRecDel	=	false;
	}
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientId	=	$p["confirmId"];


			if ($ingredientId!="") {
				// Checking the selected fish is link with any other process
				$ingredientRecConfirm =$ingredientMasterObj->updateingredientconfirm($ingredientId);
			}

		}
		if ($ingredientRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmingredient);
			$sessObj->createSession("nextPage",$url_afterDelIngredients.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

	}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$ingredientId= $p["confirmId"];

			if ($ingredientId!="") {
				#Check any entries exist
				
					$ingredientRecConfirm = $ingredientMasterObj->updateingredientReleaseconfirm($ingredientId);
				
			}
		}
		if ($ingredientRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmingredient);
			$sessObj->createSession("nextPage",$url_afterDelIngredients.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	//Rekha added code 
	if($srchName!="" and $g["pageNo"]=="") {
		$pageNo=1;
	}
	// end code 


	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	
	if ($g["mainCategoryFilter"]!="") $mainCategoryFilterId = $g["mainCategoryFilter"];
	else $mainCategoryFilterId = $p["mainCategoryFilter"];

	if ($g["categoryFilter"]!="") $categoryFilterId = $g["categoryFilter"];
	else $categoryFilterId = $p["categoryFilter"];

	
	
	
	# Resettting offset values
	if ($p["hidCategoryFilterId"]!=$p["categoryFilter"] || $p["hidMainCategoryFilterId"]!=$p["mainCategoryFilter"]){
		$offset = 0;
		$pageNo = 1;
	}

	if ($p["hidMainCategoryFilterId"]!=$p["mainCategoryFilter"]) $categoryFilterId = ""; 
	
	//echo $srchName ;
	
	
	
	# List all Ingredients
	$ingredientResultSetObj = $ingredientMasterObj->fetchAllPagingRecords($offset, $limit, $categoryFilterId, $mainCategoryFilterId, $srchName);
	
	/* rekha added */
	# List all Raw Ingredients
	$ingredienRawtype = $ingredientMasterObj->fetch_cleaned_raw($p["ingMainCategory"],$p["category"]);
	/* end code */
	
	$ingredientRecordSize	= $ingredientResultSetObj->getNumRows();

	//echo $ingredientRecordSize;
	
	## -------------- Pagination Settings II -------------------
	$allIngredientResultSetObj = $ingredientMasterObj->ingredientRecFilter($categoryFilterId, $mainCategoryFilterId, $srchName);
	$numrows	=  $allIngredientResultSetObj->getNumRows();
	
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	# Get Main Category Records
	//$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecords();
	$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecordsActiveCategory();
	if ($addMode || $editMode) {
		if ($p["ingMainCategory"]!="") $mainCategoryId = $p["ingMainCategory"];
		else $mainCategoryId = $selMainCategoyId; // Edit Mode
		
		# List all Ingredient Sub-Category	
		if ($mainCategoryId)
			$ingredientCategoryRecords = $ingredientCategoryObj->fetchAllRecordsActiveSubcategory($mainCategoryId); //$ingredientCategoryRecords = $ingredientCategoryObj->fetchAllRecords($mainCategoryId);
	}	
	# List all Ing Category Recs
	//$filterIngCategoryRecords = $ingredientCategoryObj->fetchAllRecords($mainCategoryFilterId);
	$filterIngCategoryRecords = $ingredientCategoryObj->fetchAllRecordsActiveSubcategory($mainCategoryFilterId);
		
	# Find Ing Rate List
	$latestIngRateListId = $ingredientRateListObj->latestRateList();
	$criticalParameters = $ingredientMasterObj->criticalParameters();
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/IngredientsMaster.js"; 

	#heading Section
	if ($editMode) $heading	=	$label_editIngredients;
	else	       $heading	=	$label_addIngredients;

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<!--rekha added code -->
	<script language='javascript'>
	function func_sel(obj){
		if(obj.value=='2'){
			if(confirm('Select the corresponding RAW ingredient?'))
			{
				document.getElementById("divrawIng").style='display:block;padding-bottom:10px;padding-top:10px;'
			}
		}
		else{
			document.getElementById("divrawIng").style='display:none;padding-bottom:10px;padding-top:10px;'
		}
	}
	<!-- end code -->
	</script>
<form name="frmIngredientsMaster" action="IngredientsMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<TD height="10"></TD>
		</tr>
		<!--<tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Sub-Category">Sub-Category</a></td></tr>-->
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
							$bxHeader="Ingredient Master";
							include "template/boxTL.php";
						?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="70%">
										<?
											if ( $editMode || $addMode) {
										?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
														<tr>
															<td>
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
																			<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>

																				  <td colspan="2" align="center">
																					<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('IngredientsMaster.php');" />&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateIngredientMaster(document.frmIngredientsMaster);" /></td>
																					
																					<?} else{?>

																					
																					<td  colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientsMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientMaster(document.frmIngredientsMaster);">												</td>

																					<?}?>
																				</tr>
																				<input type="hidden" name="hidIngredientId" value="<?=$editIngredientId;?>">
																				<tr><TD height="10"></TD></tr>
																				<tr>
																					<TD colspan="2" nowrap align="center">
																						<table width='100%'>
																							<TR>
																								<TD valign="top" width='20%'>
																									<table>
																										<tr>
																											<td class="fieldName" nowrap >*Category</td>
																											<td align="left">
																												<select name="ingMainCategory" onchange="<?if ($addMode) {?> this.form.submit();<? } else {?> this.form.editId.value=<?=$editId?>;this.form.submit(); <? }?>">
																													<option value="">-- Select --</option>
																													<?
																													foreach ($ingMainCategoryRecords as $cr) {
																														$ingMainCategoryId	= $cr[0];
																														$ingCategoryName	= stripSlash($cr[1]);
																														$selected = "";
																														if($p["ingMainCategory"]!=""){
																															$selMainCategoyId = $p["ingMainCategory"];
																														}
																														
																														//$mainCategoryId
																														if ($selMainCategoyId==$ingMainCategoryId) $selected = "selected";
																													?>	
																													<option value="<?=$ingMainCategoryId?>" <?=$selected?>><?=$ingCategoryName?></option>
																													<?
																													}
																													?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td nowrap class="fieldName" >*Sub-Category</td>
																											<td nowrap align="left">
																												<select name="category" onchange="<?if ($addMode) {?> this.form.submit();<? } else {?> this.form.editId.value=<?=$editId?>;this.form.submit(); <? }?>">
																													<option value="">-- Select --</option>
																													<?
																													foreach ($ingredientCategoryRecords as $cr) {
																														//$selCategory	= $p["category"];
																														if($p["category"]!=""){
																															$selCategory = $p["category"];
																														}
																														$categoryId	= $cr[0];
																														$categoryName	= stripSlash($cr[1]);
																														$selected = "";
																														if ($selCategory==$categoryId) $selected = "Selected";
																													?>
																														<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
																													<? }?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Code</td>
																											<td align="left">
																												<INPUT TYPE="text" NAME="ingredientCode" size="15" value="<?=$code;?>">
																												<input type="hidden" name="numberGenId" size="20" value="<?=$numbergenId;?>" />
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Name</td>
																											<td align="left">
																												<input type="text" name="ingredientName" size="20" value="<?=$name;?>" />
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >Local Name</td>
																											<td align="left">
																												<textarea name="ingredientSurname"><?=$surname?></textarea>
																											</td>
																										</tr>
																										<tr>
																											<TD></TD>
																											<td class="listing-item" style="text-align:left;">
																												<span style="vertical-align:middle; line-height:normal"><font size="1">(Please put a comma for every local name)</font></span>
																											</td>
																										</tr>
																										<!--rekha added code -->
																										<tr>
																											<td class="fieldName" nowrap >Packing Size</td>
																											<td align="left">
																												<!--<input type="checkbox" id='chkavl' onclick="javascript:div_txtpacksize.style='display:block;';">
																												<div id='div_txtpacksize' name='div_txtpacksize' style='display:none;'>-->
																													<input type='text' name="txtpackingsize" value="<?=$Packing_size?>">
																												<!--</div>-->
																												
																											</td>
																										</tr>				
																										<!-- end code -->
																									
																									</table>
																								</TD>
																								<td width='5%'>&nbsp;</td>
																								<TD valign="top" width='75%'>
																									<table>
																										<tr>
																											<td class="fieldName" nowrap >Description</td>
																											<td>
																												<textarea name="description"><?=$descr?></textarea>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap valign='top'>*Material Type</td>
																											<td valign='top'>
																											    <select name="ingMaterialType" id="ingMaterialType" onchange='javascript:func_sel(this);'>
																													<option value="">--Select--</option>
																												    <option value="1" <?php if($materialType == 1) echo 'selected = "selected"'; ?>>RAW</option>
																													<option value="2" <?php if($materialType == 2) echo 'selected = "selected"'; ?>>CLEANED</option>
																													<option value="3" <?php if($materialType == 3) echo 'selected = "selected"'; ?>>PROCESSED</option>
																												</select>
																												<?php 
																													if($materialType == 2)
																														$var_style='display:block;padding-bottom:10px;padding-top:10px;';
																													else 
																														$var_style='display:none;padding-bottom:10px;padding-top:10px;';	
																												?>
																												
																												<div class="fieldNameLeft" id='divrawIng' style='<?=$var_style?>'>
																													*Select Raw: 
																													<select name="selraw_ing" id="selraw_ing">
																														<option value=''>------ Select ------</option>
																													<?
																													foreach ($ingredienRawtype as $ing_rt) {
																														$ing_id	= $ing_rt[0];
																														$ing_code = $ing_rt[1];
																														$ing_surname = $ing_rt[2];
																														$selected = "";
																														if ($selraw_ing==$ing_id) $selected = "Selected";
																													?>
																														<option value="<?=$ing_id?>" <?=$selected?>><?=$ing_code?></option>
																													<? }?>
																													<select>
																													<br>
																													*Yeild(%): <input type='textbox' name='yeild' id='yeild' size="6" value="<?=$yeild?>">
																													<br>
																													*Cleaning Cost: <input type='textbox' name='cleaning_cost' id='cleaning_cost' size="6" value="<?=$clearing_cost?>">
																												</div>
																											</td>
																										</tr>
																										<?
																										$i=0;
																										
																										foreach($criticalParameters as $cp)
																										{			
																											$cpId=$cp[0];
																											$cpname=$cp[1];
																											$cptype=$cp[2];
																											(($cptype=="checkbox") ||  ($cptype=="radio"))?$typeVal="Y":$typeVal="";
																											if(sizeof($ingredientCriticalRec)>0)
																											{
																												foreach($ingredientCriticalRec as $icr)
																												{
																													$icrId=$icr[0];
																													$parameterId=$icr[1];
																													$status=$icr[2];
																													if($parameterId==$cpId)
																													{
																														($status=="Y")?$chkStatus="checked":$chkStatus="";
																														($status!="Y" &&  $status!="N")?$typeVal=$status:$typeVal=$typeVal;
																													}
																												}
																											}
																										?>
																										<tr>
																											<td class="fieldName" nowrap><?=$cpname?></td> 
																											<td><input type="<?=$cptype?>" name="critical_<?=$i?>" id="critical_<?=$i?>"	value="<?=$typeVal?>" <?=$chkStatus?>/>
																												<input type="hidden" name="parameterId_<?=$i?>" id="parameterId_<?=$i?>" value="<?=$cpId?>"/></td>
																												<input type="hidden" name="ctype_<?=$i?>" id="ctype_<?=$i?>" value="<?=$cptype?>"/>
																											</td>
																										</tr>
																										<? 
																										$i++;
																										}
																										?>
																										<input type="hidden" name="criticalSize" id="criticalSize" value="<?=$i?>">
																									</table>
																								</TD>
																							</TR>
																						</table>
																					</TD>
																				</tr>				
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>

																					<td colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientsMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientMaster(document.frmIngredientsMaster);">												</td>
																					
																					<?} else{?>

																					<td  colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientsMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientMaster(document.frmIngredientsMaster);">												</td>
																					<input type="hidden" name="cmdAddNew" value="1">
																					<?}?>
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
									<td align="center" colspan="3">
										<table width="40%" align="center">
											<TR>
												<TD align="center">
												<?php			
													$entryHead = "";
													require("template/rbTop.php");
												?>
													<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">
														<tr>
															<td nowrap="nowrap">
																<table cellpadding="0" cellspacing="0">
																	<tr>		
																		<td class="listing-item" nowrap>Category:</td>
																		<td class="listing-item">&nbsp;</td>
																		<td style="padding-left:5px; padding-right:10px;">
																			<select name="mainCategoryFilter" onchange="this.form.submit();" style="width:120px;">
																				<option value="">-- Select All --</option>
																				<?
																				foreach ($ingMainCategoryRecords as $cr) {
																					$mCategoryId	= $cr[0];
																					$mCategoryName	= stripSlash($cr[1]);
																					$selected = "";
																					if ($mainCategoryFilterId==$mCategoryId) $selected = "Selected";
																				?>
																				<option value="<?=$mCategoryId?>" <?=$selected?>><?=$mCategoryName?></option>
																				<? }?>
																		   </select>
																		</td>			
																		<td class="listing-item" nowrap>Sub-Category:</td>
																		<td class="listing-item">&nbsp;</td>
																		<td style="padding-left:5px; padding-right:10px;">
																			<?php 
																				//echo $cr[0];
																				//echo $g["mainCategoryFilter"];
															
																			?>
																			
																			<select name="categoryFilter" onchange="this.form.submit();" style="width:120px;">
																				<option value="">-- Select All --</option>
																				<?
																				foreach ($filterIngCategoryRecords as $cr) {
																					$categoryId	= $cr[0];
																					$categoryName	= stripSlash($cr[1]);
																					$selected = "";
																					if ($categoryFilterId==$categoryId) $selected = "Selected";
																				?>
																				<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
																				<? }?>
																			</select>
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
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientsMaster.php?categoryFilter=<?=$categoryFilterId?>&mainCategoryFilter=<?=$mainCategoryFilterId?>',700,600);"><? }?></td>
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
									<td width="1" ></td>
									<td colspan="2" style="padding-left:10px;padding-right:10px;">

										<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" id="newspaper-b1">
										<?
										if ($ingredientRecordSize) {
											$i	=	0;
										?>
										
										
										<thead>
										<tr>
										<td colspan="10" align="right" style="padding-right:10px;" class="navRow">
										<!-- REKHA ADDED CODE -->
										<div style='float:right;padding:2px;'>
											<input type='text' name="srchName" value="<?=$srchName?>" size="30"> 
											<input type="button" value="Search" onClick="if(this.form.srchName.value==''){ alert('Plaese Enter Serch Key');this.form.srchName.focus();return false; }else this.form.submit();" style='cursor:hand;'>&nbsp;<input type="reset" value="Cancel" onClick="this.form.srchName.value=''; this.form.submit();" style='cursor:hand;'>
										</div>		
									    <!-- END CODE -->
										</td>
										</tr>

										<? if($maxpage>1){?>
											<tr>
												<td colspan="10" align="right" style="padding-right:10px;" class="navRow">
						
												
													<div align="right">
													<?php
													$nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId&srchName=$srchName\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\">>></a> ";
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
												<th width="20">
													<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
												</th>		
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Name</th>
												<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Local Name</th>
												<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Category</th>
												<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Sub-Category</th>
												<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Material Type</th>		
												<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Stock</th>
												<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Rate</th>
												<? if($edit==true){?>
													<th class="listing-head">&nbsp;</th>
												<? }?>
												<? if($confirm==true){?>
													<th class="listing-head">&nbsp;</th>
												<? }?>
											</tr>
										</thead>
										<tbody>
											<?php
											while ($ir=$ingredientResultSetObj->getRow()) {
												$i++;
												$ingredientId = $ir[0];
												$ingredientCode	= stripSlash($ir[1]);
												$ingredientName	= stripSlash($ir[2]);
												$surname	= stripSlash($ir[3]);
												$qtyInStock	= ($ir[4]==0)?"":$ir[4];
												//$stockInHand	= ($ir[5]==0)?"":$ir[5];
												$categoryName	= $ir[6];
												$mainCategoryName = $ir[7];
												$active=$ir[8];
												$mat_type=$ir[9];
												if($mat_type=='1') $display_mat_type= "Raw";
												elseif($mat_type=='2') $display_mat_type= "Cleaned";
												elseif($mat_type=='3') $display_mat_type= "Processed";
												else $display_mat_type=""; 
												$ingredientStock = $ingredientMasterObj->getIngredientStock($ingredientId);

												# Find Ing Rate
												$ingRate = $ingredientMasterObj->getIngCurrentRate($ingredientId,$latestIngRateListId);
											?>
											<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20">
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ingredientId;?>" class="chkBox">
												</td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$ingredientName;?></td>
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" ><?=$surname?></td>
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="left"><?=$mainCategoryName?></td>
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="left"><?=$categoryName?></td>
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="left"><?=$display_mat_type?></td>
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right"><?=$ingredientStock[0]?></td>
												<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right"><?=($ingRate!="")?$ingRate:"";?></td>
												<? if($edit==true){?>
												<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ingredientId;?>,'editId');this.form.action='IngredientsMaster.php';" ><? } ?></td>
												<? }?>
												<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
												<?php if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$ingredientId;?>,'confirmId');"  >
													<?php } else if ($active==1){?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$ingredientId;?>,'confirmId');"  >
													<?php }?>
												</td>
												<? }?>
											</tr>
											<?
												}
											?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
											<? if($maxpage>1){?>
											<tr>
												<td colspan="10" align="right" style="padding-right:10px;" class="navRow">
													<div align="right">
													<?php
													 $nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId&srchName=$srchName\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"IngredientsMaster.php?pageNo=$page&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\">>></a> ";
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
										</tbody>
										<?
											} else {
										?>
										<tr>
											<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
											}
										?>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientsMaster.php?categoryFilter=<?=$categoryFilterId?>&mainCategoryFilter=<?=$mainCategoryFilterId?>',700,600);"><? }?></td>
										</tr>
									</table>									
								</td>
							</tr>
							<input type="hidden" name="hidCategoryFilterId" value="<?=$categoryFilterId?>">
							<input type="hidden" name="hidMainCategoryFilterId" value="<?=$mainCategoryFilterId?>">
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
	<tr>
		<td height="10"></td>
	</tr>
		<!--<tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Sub-Category">Sub-Category</a></td></tr>-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
</table>
<? if ($addMode!="") {?>
<SCRIPT LANGUAGE="JavaScript">
//alert("hii");
//window.load = xajax_generateIngredientCode();

</SCRIPT>
<? }?>
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
	//ensureInFrameset(document.frmIngredientsMaster);
	//-->
	</script>
<?php 
	}
?>
</form>
<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
	//require("template/bottomRightNav.php");
?>
