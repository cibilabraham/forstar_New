<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection = "?pageNo=".$p["pageNo"]."&selRateList=".$p["selRateList"]."&categoryFilter=".$p["categoryFilter"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add		= false;
	$edit		= false;
	$del		= false;
	$print		= false;
	$confirm	= false;
	
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

	# Add new
	if ($p["cmdAddNew"]!="") {
		$addMode  =   true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode   =  false;
		$editMode  =  false;
	}

	
	#Add a stock
	if ($p["cmdAdd"]!="") {
		$selIngredient	=	$p["selIngredient"];
		$ingRatePerKg	=	addSlash(trim($p["ingRatePerKg"]));
		$ingYield	=	addSlash(trim($p["ingYield"]));
		$ingHighPrice	=	addSlash(trim($p["ingHighPrice"]));
		$ingLowPrice	=	addSlash(trim($p["ingLowPrice"]));
		$ingLastPrice	=	addSlash(trim($p["ingLastPrice"]));
		$ingRateList	=	$p["ingRateList"];
		$cleanedCost	=    $p["cleanedCost"];	
		$materialType	=    $p["materialType"];	
		//$ingQuantity	=	trim($p["ingQuantity"]);
		//echo "hii".$cleanedCost."--".$materialType;
		//die();
		#Checking same entry exist in the table
		$sameEntryExist = $ingredientRateMasterObj->checkEntryExist($selIngredient, $ingRateList);

		if ($selIngredient!="" && $ingRatePerKg!="" && !$sameEntryExist) {

			$ingredientRateRecIns = $ingredientRateMasterObj->addIngredientRate($selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $ingLastPrice, $ingRateList,$cleanedCost,$materialType,$userId);

			if ($ingredientRateRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddIngredientRate);
				$sessObj->createSession("nextPage",$url_afterAddIngredientRate.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddIngredientRate;
			}
			$ingredientRateRecIns = false;
		} else {
			$err	 = $msg_failAddIngredientRate;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {		
		$ingredientRateId =	$p["hidIngredientRateId"];
		$selIngredient	=	$p["selIngredient"];
		$ingRatePerKg	=	addSlash(trim($p["ingRatePerKg"]));
		$ingYield	=	addSlash(trim($p["ingYield"]));
		$ingHighPrice	=	addSlash(trim($p["ingHighPrice"]));
		$ingLowPrice	=	addSlash(trim($p["ingLowPrice"]));
		$ingLastPrice	=	addSlash(trim($p["ingLastPrice"]));
		$ingRateList	=	$p["ingRateList"];
		$cleanedCost	=    $p["cleanedCost"];	
		$materialType	=    $p["materialType"];	
		//$ingQuantity	=	trim($p["ingQuantity"]);
		//$ingExistingQty	=	$p["ingExistingQty"];
		
		if ($ingredientRateId!="" && $selIngredient!="" && $ingRatePerKg!="") {
			$ingredientRateRecUptd = $ingredientRateMasterObj->updateIngredientRate($ingredientRateId, $selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $ingLastPrice, $ingRateList,$cleanedCost,$materialType);
		}
	
		if ($ingredientRateRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succIngredientRateUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateIngredientRate.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failIngredientRateUpdate;
		}
		$ingredientRateRecUptd	=	false;
	}


	# Edit  an ingredient
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$ingredientRateRec	=	$ingredientRateMasterObj->find($editId);
		$editIngredientRateId =	$ingredientRateRec[0];
		$selIngredient	=	$ingredientRateRec[1];
		$ingRatePerKg	=	$ingredientRateRec[2];
		$ingYield	=	$ingredientRateRec[3];
		$ingHighPrice	=	$ingredientRateRec[4];
		$ingLowPrice	=	$ingredientRateRec[5];
		$ingLastPrice	=	$ingredientRateRec[6];
		$ingRateList	=	$ingredientRateRec[7];
		//$ingQuantity	=	$ingredientRateRec[8];
		$cleanedCost	=	$ingredientRateRec[8];
		$materialType	=	$ingredientRateRec[9];
		($materialType=="rawmaterial")?$selRawmaterial="selected":$selRawmaterial="";
		($materialType=="cleaned")?$selCleaned="selected":$selCleaned="";
		# Last three rate List
		$getRevisedRateListRecs = $ingredientRateMasterObj->getRevisedRateListRecs($selIngredient, $ingRateList);
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientRateId	=	$p["delId_".$i];
			//$moreEntriesExist = $ingredientRateMasterObj->checkMoreEntriesExist($ingredientRateId);
			//&& !$moreEntriesExist
			if ($ingredientRateId!="") {
				// Need to check the selected Category is link with any other process
				$ingredientRateRecDel = $ingredientRateMasterObj->deleteIngredientRate($ingredientRateId);
			}
		}
		if ($ingredientRateRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelIngredientRate);
			$sessObj->createSession("nextPage",$url_afterDelIngredientRate.$selection);
		} else {
			$errDel	=	$msg_failDelIngredientRate;
		}
		$ingredientRateRecDel	=	false;
	}

if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientRateId	=	$p["confirmId"];


			if ($ingredientRateId!="") {
				// Checking the selected fish is link with any other process
				$ingredientRecConfirm = $ingredientRateMasterObj->updateRateListconfirm($ingredientRateId);
			}

		}
		if ($ingredientRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmingredient);
			$sessObj->createSession("nextPage",$url_afterDelIngredientRate.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$ingredientRateId= $p["confirmId"];

			if ($ingredientRateId!="") {
				#Check any entries exist
				
					$ingredientRecConfirm = $ingredientRateMasterObj->updateRateListReleaseconfirm($ingredientRateId);
				
			}
		}
		if ($ingredientRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmingredient);
			$sessObj->createSession("nextPage",$url_afterDelIngredientRate.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if ($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $ingredientRateListObj->latestRateList();
	#------------------------------------

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
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

	# List all IngredientRate
	$ingredientRateResultSetObj = $ingredientRateMasterObj->fetchAllPagingRecords($selRateList, $offset, $limit, $categoryFilterId, $mainCategoryFilterId);
	$ingredientRateRecordSize   = $ingredientRateResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	//$ingredientRateMasterObj->fetchAllRecords($selRateList)
	$allIngredientRateResultSetObj = $ingredientRateMasterObj->ingredientRateRecFilter($selRateList, $categoryFilterId, $mainCategoryFilterId);
	$numrows	=  $allIngredientRateResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	#List all Ingredients
	//$ingredientResultSetObj = $ingredientMasterObj->fetchAllRecords();
	$ingredientResultSetObj = $ingredientMasterObj->fetchAllRecords();	

	#Ing Rate List
	$ingredientRateListRecords	=	$ingredientRateListObj->fetchAllRecords();
	
	# Ing category Records (Using in Filter)
	//$ingredientCategoryRecords = $ingredientCategoryObj->fetchAllRecords($mainCategoryFilterId);

	$ingredientCategoryRecords = $ingredientCategoryObj->fetchAllRecordsActiveSubcategory($mainCategoryFilterId);

	# Get Main Category Records
	//$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecords();
	$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecordsActiveCategory();


	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/IngredientRateMaster.js"; 

	#heading Section
	if ($editMode) $heading	=	$label_editIngredientRate;
	else	       $heading	=	$label_addIngredientRate;

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmIngredientRateMaster" action="IngredientRateMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>

	<tr>
				<td height="10" align="center">
		<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td><select name="selRateList" id="selRateList" onchange="this.form.submit();">
                      <option value="">--Select--</option>
                      	<?php
			foreach($ingredientRateListRecords as $prl) {
				$ingredientRateListId	= $prl[0];
				$rateListName		= stripSlash($prl[1]);
				$startDate		= dateFormat($prl[2]);
				$displayRateList 	= $rateListName."&nbsp;(".$startDate.")";
				$selected = ($selRateList==$ingredientRateListId)?"Selected":"";
			?>
                      <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      <? }?>
                    </select></td>
		   <? if($add==true){?>
<!--onclick="this.form.action='IngredientRateList.php?mode=AddNew'"  -->
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="parent.moveTab('IngredientRateList.php')"></td>
		<? }?>
                  </tr>
                </table></td>
	</tr>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?	
					$bxHeader="Ingredient Rate Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('IngredientRateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateIngredientRateMaster(document.frmIngredientRateMaster);"></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientRateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientRateMaster(document.frmIngredientRateMaster);">												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidIngredientRateId" value="<?=$editIngredientRateId;?>">
						<tr>
							<td colspan="2"  height="10" ></td>
						</tr>
	<tr>
	<td colspan="2" nowrap align="center">
		<table>
		<TR>
			<TD valign="top" nowrap>
			<?php
				$entryHead = "";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>
			<table>
				<tr>
					<td nowrap class="fieldName">*Ingredient</td>
					<td nowrap>
						<select name="selIngredient" id="selIngredient" style="width:120px;">
						<option value="">-- Select --</option>
						<?
						foreach($ingredientResultSetObj as $kVal=>$ir) {
							$ingredientId = $ir[0];
							$ingredientCode	= stripSlash($ir[1]);
							$ingredientName	= stripSlash($ir[2]);
							$selected = ($selIngredient==$ingredientId && $ingredientId!="")?"selected":"";
						?>
						<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
						<? }?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >*Rate/Kg</td>
					<td>
						<INPUT TYPE="text" NAME="ingRatePerKg" id="ingRatePerKg" size="5" value="<?=$ingRatePerKg;?>" style="text-align:right;" onkeyup="calcCleanRatePerKg();">
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >*Yield</td>
					<td class="listing-item">
						<input type="text" name="ingYield" id="ingYield" size="5" value="<?=$ingYield;?>" style="text-align:right;" onkeyup="calcCleanRatePerKg();">&nbsp;%
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >*Cleaned cost</td>
					<td class="listing-item">
						<input type="text" name="cleanedCost" id="cleanedCost" size="5" value="<?=$cleanedCost;?>" style="text-align:right;" onkeyup="calcCleanRatePerKg();">
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap>*Material Type:-</td>
					<td>
						<select name="materialType" id="materialType" onchange="calcCleanRatePerKg();">
							<option value="">--select--</option>
							<option value="rawmaterial" <?=$selRawmaterial?>>Raw material</option>
							<option value="cleaned" <?=$selCleaned?>>Cleaned</option>
						</select>
					</td>
				<tr>
				<!--<tr>
					<td colspan="2" class="fieldName">
						<table align="center">
							<tr>
								<td class="fieldName" nowrap >*Material Type:-</td>
								<td class="fieldName" nowrap >
								<input type="radio" name="materialType" id="materialType" size="5" value="rawmaterial" onclick="calcCleanRatePerKg();">Raw material
								</td>
							</tr>
							<tr>
								<td  nowrap >&nbsp;</td>
								<td  nowrap align="left">
								<input type="radio" name="materialType" id="materialType" size="5" value="cleaned" onclick="calcCleanRatePerKg();"><b class="fieldName" >Cleaned</b>
								</td>
							</tr>
						</table>
					</td>
				</tr>-->				

				<tr>
					<td class="fieldName" nowrap >*Clean Rate/Kg</td>
					<td nowrap>
						<input type="text" name="ingLastPrice" id="ingLastPrice" size="5" value="<?=$ingLastPrice;?>" style="text-align:right;border:none;" readonly>
					</td>
				</tr>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			</TD>
			<TD valign="top" nowrap>
			<?php
				$entryHead = "";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>
			<table>
				<tr>
						  <td class="fieldName" nowrap >Highest Price</td>
						  <td>
						  <input type="text" name="ingHighPrice" size="5" value="<?=$ingHighPrice;?>" style="text-align:right;"></td>
						</tr>
						<tr>
							<td class="fieldName" nowrap >Lowest Price</td>
							<td><input type="text" name="ingLowPrice" size="5" value="<?=$ingLowPrice;?>" style="text-align:right;"></td>
						</tr>
					  <tr>
						<td class="fieldName" nowrap >Rate list</td>
					    <td nowrap="true" align="left">
					    <select name="ingRateList" id="ingRateList" style="width:120px;">
                                            <option value="">-- Select --</option>
					   	<?php
						foreach ($ingredientRateListRecords as $prl) {
							$ingredientRateListId	=	$prl[0];
							$rateListName		=	stripSlash($prl[1]);
							$startDate		= 	dateFormat($prl[2]);
							$displayRateList = $rateListName."&nbsp;(".$startDate.")";
							$rateListId = ($addMode)?$selRateList:$ingRateList;
							$selected = ($rateListId==$ingredientRateListId)?"Selected":"";
						?>
                      				<option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      				<? }?>
                                            	</select>
						</td>
						</tr>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			</TD>
		</TR>
		</table>
	</td>
	</tr>	
	<tr>
		<td colspan="2" nowrap >		
<!-- 	Last Three Rate List Starts Here -->
	<?php
		if (sizeof($getRevisedRateListRecs)>0) {
	?>	
	<table width="50%" cellpadding="0" cellspacing="3">
	<TR>
	<TD>
	<?php
		$entryHead = "Last 3 Rate List";
		$rbTopWidth = "";
		require("template/rbTop.php");
	?>
	<table cellpadding="2"  cellspacing="1" border="0" align="center" id="newspaper-b1">
		<thead>
		<tr align="center">
			<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;" nowrap>Revised Date</th>
			<th style="padding-left:5px;padding-right:5px;font-size:11px; color:#666699;">Rate</th>
		</tr>
		</thead>
		<tbody>
		<?php
			foreach($getRevisedRateListRecs as $grr) {
				$revRate = $grr[0];
				$revDate = dateFormat($grr[1]);
		?>
		<tr>
			<TD class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$revDate?></TD>
			<TD class="listing-item" align="right" style="padding-left:5px;padding-right:5px;"><?=$revRate?></TD>
		</tr>
		<?php
			}
		?>
		</tbody>
	</table>
	<?php
		require("template/rbBottom.php");
	?>
	</TD>
	</TR>
	</table>	
	<?php
		} else if ($editMode!="") {
	?>
		<table>
		<TR>
			<td class="err1" nowrap style="line-height:normal;font-size:11px;">No old rate list found.</td>
		</TR>
		</table>
	<? }?>
		</td>
	</tr>

						<tr>
							<td colspan="2"  height="10" ></td>
						</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientRateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientRateMaster(document.frmIngredientRateMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientRateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientRateMaster(document.frmIngredientRateMaster);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>
							<?php
								require("template/rbBottom.php");
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
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
						<table width="35%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>			
					 <td class="listing-item">Category</td>
					 <td class="listing-item">&nbsp;</td>
					<td style="padding-left:5px; padding-right:10px;">
					<select name="mainCategoryFilter" onchange="this.form.submit();">
                                        <option value="">-- Select All --</option>
					<?
					foreach ($ingMainCategoryRecords as $cr) {
						$mCategoryId	= $cr[0];
						$mCategoryName	= stripSlash($cr[1]);
						$selected = ($mainCategoryFilterId==$mCategoryId)?"Selected":"";
					?>
                                        <option value="<?=$mCategoryId?>" <?=$selected?>><?=$mCategoryName?></option>
					<? }?>
                                        </select></td>		
                                    <td class="listing-item">Sub-Category</td>
					   <td class="listing-item">&nbsp;</td>
					<td style="padding-left:5px; padding-right:10px;">
					<select name="categoryFilter" onchange="this.form.submit();">
                                        <option value="">-- Select All --</option>
					<?
					foreach ($ingredientCategoryRecords as $cr) {
						$categoryId	= $cr[0];
						$categoryName	= stripSlash($cr[1]);
						$selected =  ($categoryFilterId==$categoryId)?"Selected":"";
					?>
                                        <option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
					<? }?>
                                        </select></td>
                          </tr>
                    </table></td></tr></table>
		<?php
			require("template/rbBottom.php");
		?>
						</td>
						</tr>
						</table>
								</td>
							</tr>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
				
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td   background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Ingredient Rate Master  </td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>

								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientRateMaster.php?categoryFilter=<?=$categoryFilterId?>&selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
			<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($ingredientRateRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"IngredientRateMaster.php?pageNo=$page&selRateList=$selRateList&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientRateMaster.php?pageNo=$page&selRateList=$selRateList&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientRateMaster.php?pageNo=$page&selRateList=$selRateList&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
	<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Ingredient</th>
	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Rate/Kg</th>
	<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Yield<br>%</th>
	<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">High</th>
	<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Low</th>
	<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Clean<br>Rate/Kg</th>
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
		while ($irr=$ingredientRateResultSetObj->getRow()) {
			$i++;
			$ingredientRateId = $irr[0];
			$ingRatePerKg	=	$irr[2];
			$ingYield	=	$irr[3];
			$ingHighPrice	=	$irr[4];
			$ingLowPrice	=	$irr[5];
			$ingLastPrice	=	$irr[6];
			$ingredientName	=	$irr[8];
			$active=$irr[9];
			?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ingredientRateId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$ingredientName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$ingRatePerKg;?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingYield?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingHighPrice?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingLowPrice?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingLastPrice?></td>
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ingredientRateId;?>,'editId');this.form.action='IngredientRateMaster.php';" ><? } ?></td>
	<? }?>


	 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$ingredientRateId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$ingredientRateId;?>,'confirmId');"  >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"IngredientRateMaster.php?pageNo=$page&selRateList=$selRateList&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientRateMaster.php?pageNo=$page&selRateList=$selRateList&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientRateMaster.php?pageNo=$page&selRateList=$selRateList&categoryFilter=$categoryFilterId&mainCategoryFilter=$mainCategoryFilterId\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
<? }?>
	<?
		}
		else
		{
	?>
	<tr>
		<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientRateMaster.php?categoryFilter=<?=$categoryFilterId?>&selRateList=<?=$selRateList?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
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
				<!-- Form fields end   -->			</td>
		</tr>	
<input type="hidden" name="hidCategoryFilterId" value="<?=$categoryFilterId?>">
<input type="hidden" name="hidMainCategoryFilterId" value="<?=$mainCategoryFilterId?>">
		<tr>
			<td height="10"></td>
		</tr>
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
	//ensureInFrameset(document.frmIngredientRateMaster);
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
