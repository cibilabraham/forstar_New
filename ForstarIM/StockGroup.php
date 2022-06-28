<?php
	require("include/include.php");
	require_once("lib/StockGroup_ajax.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
		
	$selection = "?pageNo=".$p["pageNo"]."&categoryFilter=".$p["categoryFilter"]."&subCategoryFilter=".$p["subCategoryFilter"];
	

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

	
	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") $hidEditId = $p["editId"];
	else $hidEditId = $p["hidEditId"];
	
	# Variable Resetting
	$cpyFrmCategoryId	= $p["cpyFrmCategory"];	
	$cpyFrmSubCategoryId	= $p["cpyFrmSubCategory"];
	$selCategoryId	  	= $p["category"];
	$selSubCategoryId 	= $p["subCategory"];

	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {		
		$name		= "";
		$p["stockName"]	= "";
		$selCategory	= "";
		$p["category"]	= "";
		$selSubCategory	= "";
		$p["subCategory"] = "";		
		$hidEditId	= "";
		$cpyFrmCategoryId 	= "";
		$cpyFrmSubCategoryId 	= "";
		$selCategoryId 		= "";
		$selSubCategoryId 	= "";	
	}
	
	# Add New Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") {
		$addMode 	= false;
		$editMode 	= false;
		$cpyFrmCategoryId 	= "";
		$cpyFrmSubCategoryId 	= "";
		$selCategoryId 		= "";
		$selSubCategoryId 	= "";		
	}

	#Add a stock
	if ($p["cmdAdd"]!="") {		
		$selCategory		= $p["category"];
		$selSubCategory		= $p["subCategory"];
		$basicStkUnit		= $p["basicStkUnit"];

		$itemCount	  	= $p["hidTableRowCount"];		
		
		if ($selCategory!="") {
			$stockGroupRecIns = $stockGroupObj->addStockGroup($selCategory, $selSubCategory, $basicStkUnit, $userId);
			if ($stockGroupRecIns) {  // CHECK HERE
				#Find the Last inserted Id From stock_group Table
				$stkGroupId = $databaseConnect->getLastInsertedId();
				for ($i=0; $i<$itemCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$stkFieldId 		= $p["stkField_".$i];
						$stkFieldValidation	= $p["stkFieldValidation_".$i];
						$stkGroupEntryIds	= explode(",", $p["stkGroupEntryIds_".$i]);
						//print_r($stkGroupEntryIds);
						# Delete All labeles linked with SubCategory
						if (sizeof($stkGroupEntryIds)>0) {
							foreach ($stkGroupEntryIds as $key=>$sgEntryId) {
								//echo "$sgEntryId<br>";
								$stkGroupMainId = $stockGroupObj->getStkGroupMainId($sgEntryId);
								# Delete Stock Group Entry Id
								$delStkGroupEntry = $stockGroupObj->delStkGroupIndividualRec($sgEntryId);	
								# Check More Entry Exist
								$moreStkGroupEntryExist = $stockGroupObj->chkMoreEntryExistInStkGEntry($stkGroupMainId);
								if (!$moreStkGroupEntryExist) {
									$stkGRecDel = $stockGroupObj->deleteStockGroup($stkGroupMainId);
								}
							}
						} // Used Sub-category Check ends here
						
						# Insert New labels
						if ($stkGroupId && $stkFieldId!="") {
							$stockGroupEntryRecIns = $stockGroupObj->addStockGroupEntry($stkGroupId, $stkFieldId, $stkFieldValidation);
						}
					} // Status Conditin Ends here 
				} // Item Row Count Ends Here 
			} // Group Rec Ins Condition Ends Here

			if ($stockGroupRecIns) {		
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddStockGroup);
				$sessObj->createSession("nextPage",$url_afterAddStockGroup.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStockGroup;
			}
			$stockGroupRecIns		=	false;
		}
		$hidEditId = "";
	}

	#Update a Stock Group
	if ($p["cmdSaveChange"]!="") {
		
		$stockGroupId	=	$p["hidStockGroupId"];		
		$selCategory		= $p["category"];
		$selSubCategory		= $p["subCategory"];
		$basicStkUnit		= $p["basicStkUnit"];
		$itemCount	  	= $p["hidTableRowCount"];
			
		
		if ($stockGroupId!="" && $selCategory!="") {
			$stockGroupRecUptd	= $stockGroupObj->updateStockGroup($stockGroupId, $selCategory, $selSubCategory, $basicStkUnit);
			
			if ($stockGroupRecUptd) {
				for ($i=0; $i<$itemCount; $i++) {
					$status = $p["status_".$i];
					$hidStkGroupEntryId = $p["hidStkGroupEntryId_".$i];
					if ($status!='N') {
						$stkFieldId = $p["stkField_".$i];
						$stkFieldValidation	= $p["stkFieldValidation_".$i];
						$stkGroupEntryIds	= explode(",", $p["stkGroupEntryIds_".$i]);
						# Delete All labeles linked with SubCategory
						if (sizeof($stkGroupEntryIds)>0) {
							foreach ($stkGroupEntryIds as $key=>$sgEntryId) {
								echo "$sgEntryId<br>";
								$stkGroupMainId = $stockGroupObj->getStkGroupMainId($sgEntryId);
								# Delete Stock Group Entry Id
								$delStkGroupEntry = $stockGroupObj->delStkGroupIndividualRec($sgEntryId);	
								# Check More Entry Exist
								$moreStkGroupEntryExist = $stockGroupObj->chkMoreEntryExistInStkGEntry($stkGroupMainId);
								if (!$moreStkGroupEntryExist) {
									$stkGRecDel = $stockGroupObj->deleteStockGroup($stkGroupMainId);
								}
							}
						} // Used Sub-category Check ends here
						if ($stockGroupId!="" && $stkFieldId!="" && $hidStkGroupEntryId=="") {
							$stockGroupEntryRecIns = $stockGroupObj->addStockGroupEntry($stockGroupId, $stkFieldId, $stkFieldValidation);
						} else if ($stockGroupId!="" && $stkFieldId!="" && $hidStkGroupEntryId!="") {					
							$stockGroupEntryRecUptd = $stockGroupObj->updateStockGroupEntry($hidStkGroupEntryId, $stkFieldId, $stkFieldValidation);
						} 
					} // Status Conditin Ends here 
					else if ($hidStkGroupEntryId!="") {
						$groupEntryIdInUsed = $stockGroupObj->checkStkGroupIdInUse($hidStkGroupEntryId);
						if (!$groupEntryIdInUsed) {
							$delStkGroupEntryIndividualRec = $stockGroupObj->delStkGroupIndividualRec($hidStkGroupEntryId);
						}
					}
				} // Item Row Count Ends Here 
			} // Upate Check Ends here
		}
	
		if ($stockGroupRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStockGroupUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStockGroup.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStockGroupUpdate;
		}
		$stockGroupRecUptd	=	false;
		$hidEditId	= "";
	}


	# Edit  a Stock
	if ($p["editId"]!="" || $cpyFrmCategoryId) {
		/*
		$addMode	= false;
		$editId		=	$p["editId"];
		$editMode	=	true;
		*/

		if ($cpyFrmCategoryId) {
			$stockGroupRec	= $stockGroupObj->findCatORSubCatWiseRec($cpyFrmCategoryId, $cpyFrmSubCategoryId);
		} else {
			$editId	= $p["editId"];
			$editMode  = true;
			$stockGroupRec	=	$stockGroupObj->find($editId);
		}
		//$stockGroupRec	=	$stockGroupObj->find($editId);
		$editStockGroupId	=	$stockGroupRec[0];		
				
		if ($p["editSelectionChange"]=='1'||$p["category"]=="") {
			$selCategoryId	= $stockGroupRec[1];
		} else {
			$selCategoryId	= $p["category"];
		}
		
		if ($p["editSelectionChange"]=='1' || $p["subCategory"]=="") {
			$selSubCategoryId = $stockGroupRec[2];
		} else {
			$selSubCategoryId = $p["subCategory"];
		}

		$basicStkUnit = $stockGroupRec[3];

		# Get All Stock Entry Recs
		$stkGroupEntryRecs = $stockGroupObj->getAllStockGroupRecs($editStockGroupId);
		# Select ALL Records
		if ($selCategoryId && $selSubCategoryId) {
			$selStkGroupRecs = $stockGroupObj->getSelEditFieldRecs($selCategoryId, $selSubCategoryId);
		}
	}


	# Delete Stock
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockGroupId	=	$p["delId_".$i];

			if ($stockGroupId!="") {
				# Checking rec Exist
				$stkgRecInUse = $stockGroupObj->chkStockGroupRecExist($stockGroupId);
				if (!$stkgRecInUse) {
					$delStkGroupEntryRec = $stockGroupObj->delStockGroupEntryRecs($stockGroupId);	
					// Need to check the selected Category is link with any other process
					if ($delStkGroupEntryRec) {
						$stockGroupRecDel = $stockGroupObj->deleteStockGroup($stockGroupId);
					}
				}
			}
		}

		if ($stockGroupRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStockGroup);
			$sessObj->createSession("nextPage",$url_afterDelStockGroup.$selection);
		} else {
			if ($stkgRecInUse) $errDel = $msg_failDelStockGroup."<br> Stock group is linked with Stock Entry section";
			else $errDel	=	$msg_failDelStockGroup;
		}
		$stockGroupRecDel	=	false;
		$hidEditId	= 	"";
	}
		


		if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockGroupId	=	$p["confirmId"];
			if ($stockGroupId!="") {
				// Checking the selected fish is link with any other process
				$stockGroupRecConfirm = $stockGroupObj->updateStockGroupconfirm($stockGroupId);
			}

		}
		if ($stockGroupRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmstockGroup);
			$sessObj->createSession("nextPage",$url_afterDelStockGroup.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$stockGroupId = $p["confirmId"];
			if ($stockGroupId!="") {
				#Check any entries exist
				
					$stockGroupRecConfirm = $stockGroupObj->updateStockGroupReleaseconfirm($stockGroupId);
				
			}
		}
		if ($stockGroupRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmstockGroup);
			$sessObj->createSession("nextPage",$url_afterDelStockGroup.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["categoryFilter"]!="") $categoryFilterId = $g["categoryFilter"];
	else $categoryFilterId = $p["categoryFilter"];	
	
	# List Sub category filter records
	if ($categoryFilterId!="") $subCategoryFilterRecords  = $subcategoryObj->filterRecords($categoryFilterId);

	if ($g["subCategoryFilter"]!="") $subCategoryFilterId = $g["subCategoryFilter"];
	else $subCategoryFilterId = $p["subCategoryFilter"];

	# Resettting offset values
	if ($p["hidCategoryFilterId"]!=$p["categoryFilter"]) {		
		$offset = 0;
		$pageNo = 1;
		$subCategoryFilterId = "";
	}
	if ($p["hidSubCategoryFilterId"]!=$p["subCategoryFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}

	# List all Stock Group
	$stockGroupRecords	= $stockGroupObj->fetchAllPagingRecords($offset, $limit, $categoryFilterId, $subCategoryFilterId);
	$stockSize		= sizeof($stockGroupRecords);

	## -------------- Pagination Settings II -------------------		
	$numrows	=  sizeof($stockGroupObj->fetchAllRecords($categoryFilterId, $subCategoryFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Category ;
	//$categoryRecords	=	$categoryObj->fetchAllRecords();
	$categoryRecords	=	$categoryObj->fetchAllRecordsActivecategory();
	# Get all Stock Field Recs
	$stkFieldRecs		= $stockFieldObj->fetchAllRecords();

	if ($selCategoryId) $subCategoryRecords = $subcategoryObj->filterRecords($selCategoryId);

	$stkEntryCommonFieldStr = "";
	if ($selSubCategoryId) {
		$unitRecords = $stockGroupObj->filterUnitRecs($selSubCategoryId);

		// Check Carton
		$subCatStockType = $subcategoryObj->getSubCategoryStockType($selSubCategoryId); // Y - Carton ie P)
		$stockType	= ($subCatStockType=='Y')?"P":"O";

		$stkEntryCommonFieldArr = array("Name", "Description", "Re-order Required (Yes/No)", "Reorder Point", "Quantity in Stock", "Additional Holding Percent", "Stocking Period (Month)");
		if ($stockType=='P') {					
			array_push($stkEntryCommonFieldArr,"No of Layers","Type of Carton","Brand","Color","Packing Weight","Packing(Kg x Nos)","Suitable For (Frozen Code)","No.of Colors","Dimension","Carton Weight");
		} else {
			array_push($stkEntryCommonFieldArr,"Basic Unit","Basic Qty","Packed Qty","Min Order/Package");
		}
		$stkEntryCommonFieldStr = implode("<br>",$stkEntryCommonFieldArr);
	}

	# Get Selected Stock Group Records	
	if ($addMode && $selCategoryId && $selSubCategoryId) {
		$selStkGroupRecs = $stockGroupObj->getSelFieldRecs($selCategoryId, $selSubCategoryId);
	}
	
	# Check Rec Exist
	if ($addMode || $editMode) {
		$stkGroupRecExist = $stockGroupObj->chkGroupExist($selCategoryId, $selSubCategoryId, $editStockGroupId);
		$btnDisabled = "";
		if ($stkGroupRecExist) {
			$errRecExist = "Stock Group already exist in database";
			$btnDisabled = " disabled='true'";
		}
	}

	if ($addMode) {
		# Get All Sub-Category Recs
		if ($cpyFrmCategoryId) $getSubCategoryRecs = $subcategoryObj->filterRecords($cpyFrmCategoryId);
	}
	
	# Input type Array
	//$inputTypeArr = array("T"=>"Text", "C"=>"Checkbox", "R"=>"Radio");
	$inputTypeArr = array("T"=>"Text", "C"=>"Checkbox");
	$validationArr = array("N"=>"NO", "Y"=>"YES");

	
	if ($editMode) $heading	= $label_editStockGroup;
	else $heading	= $label_addStockGroup;	
	

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/StockGroup.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	flush();
?>
	<form name="frmStockGroup" action="StockGroup.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>	
		</tr>
		<?}?>
		<?php 
			if ($errRecExist!="" ) {
		?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$errRecExist;?></td>	
		</tr>
		<?php 
			}
		?> 
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Stock Group";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('StockGroup.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateStockGroup(document.frmStockGroup);" <?=$btnDisabled?>/></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockGroup.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStockGroup(document.frmStockGroup);" <?=$btnDisabled?>>												</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidStockGroupId" value="<?=$editStockGroupId;?>">
<tr><TD height="10"></TD></tr>
		<?php
			 if ($addMode) { 
		?>
		<tr>
		  <td colspan="2" nowrap style="padding-left:10px; padding-right:10px;" valign="top">
			<!--<fieldset>
			<legend class="listing-item" onMouseover="ShowTip('Copy from existing entry and save after editing.');" onMouseout="UnTip();">Copy From</legend>-->
			<?php
				$entryHead = "Copy From";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>	
			<table>
				<TR>
					<td class="fieldName" nowrap>
						*Cateogry
					</td>
					<td nowrap="true" style="width:160px;" onMouseover="ShowTip('Copy from existing entry and save after editing.');" onMouseout="UnTip();">
						<!--<select name="cpyFrmCategory" onchange="this.form.submit();">-->
						<select name="cpyFrmCategory" onchange="getLoading(this);">
						<option value="">--Select--</option>
						<?php
						foreach ($categoryRecords as $cr) {
							$cpyCategoryId	= $cr[0];
							$cpyCategoryName	= stripSlash($cr[1]);
							$selected = ($cpyFrmCategoryId==$cpyCategoryId)?"Selected":"";	
						?>
						<option value="<?=$cpyCategoryId?>" <?=$selected;?>><?=$cpyCategoryName;?></option>
						<? }?>
					</select>
					</td>
					<TD class="fieldName"  onMouseover="ShowTip('Copy from existing entry and save after editing.');" onMouseout="UnTip();" nowrap="true">*Sub-Category</TD>
					<td nowrap>
						<!--<select name="cpyFrmSubCategory" id="cpyFrmSubCategory" style="width:160px;" onchange="this.form.submit();">-->
						<select name="cpyFrmSubCategory" id="cpyFrmSubCategory" style="width:160px;" onchange="getLoading(this);">
							<option value="0">--Select--</option>
							<?php
								foreach ($getSubCategoryRecs as $gscr) {
									$sSubCategoryId	= $gscr[0];
									$sSubCategoryName= stripSlash($gscr[2]);
									$selected =  ($cpyFrmSubCategoryId==$sSubCategoryId)?"selected":"";
							?>
							<option value="<?=$sSubCategoryId?>" <?=$selected?>><?=$sSubCategoryName?></option>
							<? }?>
						</select>
					</td>
				</TR>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
		  </td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<?php
			} // Cpy Frm add mode ends here
		?>
		<tr>
			  <td colspan="2" nowrap>
			  <table align="center">
				<tr>
					<TD colspan="2" style="padding-left:5px; padding-right:5px;">
					<table>
						<TR>
							<TD valign="top">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
												<?php
													$entryHead = "";
													$rbTopWidth = "";
													require("template/rbTop.php");
												?>
												<table>	
												<TR>
														 <td nowrap class="fieldName" >*Category</td>
															<td nowrap>			
												<!--<select name="category" id="category" style="width:160px;" <? if ($addMode) {?> onchange="this.form.submit();"<? } else {?> onchange="this.form.editId.value=<?=$editId?>;this.form.submit();"<? }?>>-->	
												


									<select name="category" id="category" style="width:160px;" <? if ($addMode) {?> onchange="getLoading(this);"<? } else {?> onchange="this.form.editId.value=<?=$editId?>;getLoading(this);"<? }?>>
												<option value="">-- Select --</option>
												<?php
												foreach ($categoryRecords as $cr) {
													$categoryId	= $cr[0];
													$categoryName	= stripSlash($cr[1]);
													$selected = ($selCategoryId==$categoryId)?"Selected":"";
												?>
											<option value="<?=$categoryId?>" <?=$selected;?>><?=$categoryName;?></option>
												<? }?>
											</select></td>
													</TR>
											<TR>
												<td nowrap class="fieldName" >Sub-Category</td>
										<td nowrap>			
										<!--<select name="subCategory" id="subCategory" style="width:160px;" <? if ($addMode) {?> onchange="this.form.submit();"<? } else {?> onchange="this.form.editId.value=<?=$editId?>;this.form.submit();"<? }?>>-->
										
							<select name="subCategory" id="subCategory" style="width:160px;" <? if ($addMode) {?> onchange="getLoading(this);"<? } else {?> onchange="this.form.editId.value=<?=$editId?>;getLoading(this);"<? }?>>
													<option value="0">-- Select All --</option>
													<?
										foreach ($subCategoryRecords as $scr) {
											$subCategoryId		=	$scr[0];
											$subCategoryName	=	stripSlash($scr[2]);
											$selected = ($selSubCategoryId==$subCategoryId)?"Selected":"";	
										?>
													<option value="<?=$subCategoryId?>" <?=$selected;?>><?=$subCategoryName;?></option>
													 <? }?>
											   </select></td>
										</TR>
												<TR>
														<td class="fieldName" nowrap >Basic Unit</td>
													<td>
													<select name="basicStkUnit" id="basicStkUnit">
													<option value="">-- Select --</option>
													<?php
														foreach($unitRecords as $ur) {
															$stockItemUnitId = $ur[0];
															$unitName = $ur[1];
															$selected = ($basicStkUnit==$stockItemUnitId)?"Selected":"";
													?>						
																			<option value="<?=$stockItemUnitId?>" <?=$selected?>><?=$unitName?></option>
													<? }?>
																		  </select></td>
													</TR>	
											</table>
											<?php
												require("template/rbBottom.php");
											?>
										</td>
									</tr>
									<?php
									if ($stockType!="") {			
									?>
									<tr><td height="10"></td></tr>
									<tr>
										<td>
											<?php
													$entryHead = "Default Fields in Stock Entry";
													$rbTopWidth = "";
													require("template/rbTop.php");
												?>
													<table width="100%">	
														<TR>
															 <td nowrap class="fieldName" style="text-align:left; padding:0px 5px;"><?=$stkEntryCommonFieldStr?></td>			
														</tr>
													</table>
												<?php
													require("template/rbBottom.php");
												?>

										</td>
									</tr>
									<?php
											}	
									?>
								</table>
							</TD>	
				<td valign="top">
					&nbsp;
				</td>
				<td valign="top">
				<!--<fieldset>-->
				<?php
					$entryHead = "";
					$rbTopWidth = "";
					require("template/rbTop.php");
				?>
				<table>
				<TR>
					<TD style="padding-left:10px;padding-right:10px;">
						<table  cellspacing="1" cellpadding="3" id="tblAddFieldGroupItem" class="newspaperType">
	                <tr align="center">
                                <th class="listing-head" nowrap="true" style="text-align:center;">Label Name</th>
				 <th class="listing-head" nowrap="true" style="text-align:center;">Validation</th> 
				<? if ($selSubCategoryId==0) {?>
				 <th class="listing-head" nowrap="true" onMouseover="ShowTip('Sub-Category');" onMouseout="UnTip();" style="text-align:center;">Usage Status</th>
				<? }?>
				<th>&nbsp;</th>
                        </tr>				
		</table>
						</TD></TR>
						<tr><TD height="5"><input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize;?>"></TD></tr>
						<TR><TD></TD></TR>
						<tr>
							<TD style="padding-left:10px;padding-right:10px;" align="left">
								<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
							</TD>
						</tr>
				</table>
				<?php
					require("template/rbBottom.php");
				?>
				<!--</fieldset>-->
				</td>		
									
						</TR>
					</table>
					</TD>
				</tr>
                </table></td>
	  </tr>	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockGroup.php');">&nbsp;&nbsp;							<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStockGroup(document.frmStockGroup);" <?=$btnDisabled?>>			
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockGroup.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStockGroup(document.frmStockGroup);" <?=$btnDisabled?>>					
		</td>
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
				<td colspan="3" align="center">
						<table width="35%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="4">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table cellpadding="0" cellspacing="0">
                	<tr>
			  	<td class="listing-item" nowrap="true">Category&nbsp;</td>
                      		<td nowrap="nowrap">	
                <!--<select name="categoryFilter" onchange="this.form.submit();" style="width:150px;">-->

				<select name="categoryFilter" onchange="getLoading(this);" style="width:150px;">
                <option value="">-- Select All --</option>
                <?
		foreach ($categoryRecords as $cr) {
			$fCategoryId	=	$cr[0];
			$fCategoryName	=	stripSlash($cr[1]);
			$selected = ($categoryFilterId==$fCategoryId)?"Selected":"";
		?>
               <option value="<?=$fCategoryId?>" <?=$selected;?>><?=$fCategoryName;?></option>
                <? }?>
                </select>
		</td>
		<td class="listing-item">&nbsp;</td>
		<td class="listing-item" nowrap="true">Sub-Category&nbsp;</td>
                <td>
			<!--<select name="subCategoryFilter" onchange="this.form.submit();" style="width:150px;">-->

			<select name="subCategoryFilter" onchange="getLoading(this);" style="width:150px;">
                        <option value="">-- Select All--</option>
                        <?
			foreach ($subCategoryFilterRecords as $scr) {
				$fSubCategoryId		=	$scr[0];
				$fSubCategoryName	=	stripSlash($scr[2]);
				$selected =  ($subCategoryFilterId==$fSubCategoryId)?"Selected":"";
			?>
                        <option value="<?=$fSubCategoryId?>" <?=$selected;?>><?=$fSubCategoryName;?></option>
                        <? }?>
                       </select> 
                 </td>
		    </tr>
                    </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
				</td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Group  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>					
<tr>
		<TD colspan="3">
			<table align="center">
				<TR>					
					<TD>
						<table>
							<TR>
								<TD>
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockGroup.php?categoryFilter=<?=$categoryFilterId?>&subCategoryFilter=<?=$subCategoryFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>					
				</TR>
			</table>
		</TD>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2" width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
			if ( sizeof($stockGroupRecords) > 0 ) {
				$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockGroup.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockGroup.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockGroup.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Category</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Sub-Category</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Basic Unit</th>
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($stockGroupRecords as $sgr) {
		$i++;
		$stockGroupId 	= $sgr[0];
		$catName	= $sgr[4];
		$subCatName	= $sgr[5];
		$basicUnitName	= $sgr[6];
		$active=$sgr[7];
		$existingrecords=$sgr[8];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
		<?php 
		echo $existingrecords ;
		if($existingrecords==0){
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockGroupId;?>" class="chkBox">
		<?php 
		}
		?>
		</td>	
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$catName;?></td>
		<td class="listing-item" nowarp align="left" style="padding-left:10px; padding-right:10px;"><?=($subCatName!="")?$subCatName:"ALL"?></td>
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=$basicUnitName?></td>		
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<? if ($active==0){?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stockGroupId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='StockGroup.php';" ><? } ?></td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stockGroupId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$stockGroupId;?>,'confirmId');"  >
			<?php 
			//} 
			} }?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockGroup.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockGroup.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockGroup.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\">>></a> ";
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
		} else {
	?>
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
	<tr>
		<TD colspan="3">
			<table align="center">
				<TR>					
					<TD>
						<table>
							<TR>
								<TD>
									<? if($del==true){?><?//=$stockSize;?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockGroup.php?categoryFilter=<?=$categoryFilterId?>&subCategoryFilter=<?=$subCategoryFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>
<input type="hidden" name="hidEditId" value="<?=$hidEditId?>">
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>	
							<?php
								include "template/boxBR.php"
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
<input type="hidden" name="hidCategoryFilterId" value="<?=$categoryFilterId?>">	
<input type="hidden" name="hidSubCategoryFilterId" value="<?=$subCategoryFilterId?>">	
		<tr>
			<td height="10"></td>
		</tr>
	</table>	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">	
		function addNewItem()
		{
			addNewItemRow('tblAddFieldGroupItem', '', '', '', '', '<?=$selSubCategoryId?>');
		}
	</script>
	<?php
		 if ($addMode && sizeof($selStkGroupRecs)<=0 && !sizeof($stkGroupEntryRecs)) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewItem();	
	</SCRIPT>
	<?php
		 }
	?>
	<?php
		if (sizeof($stkGroupEntryRecs)) {
			foreach ($stkGroupEntryRecs as $sge) {
				$stkGroupEntryId = $sge[0];
				$stockFieldId	 	= $sge[1];
				$stkFValidation		= $sge[2];
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">	
		addNewItemRow('tblAddFieldGroupItem', '<?=$stkGroupEntryId?>', '<?=$stockFieldId?>', '', '<?=$stkFValidation?>', '<?=$selSubCategoryId?>');		
	</script>
	<?php
			}
		}
	?>
	<?php
		// Already Selected Label Name
		if (sizeof($selStkGroupRecs)) {
			foreach ($selStkGroupRecs as $sge) {
				$stkGroupEntryId = $sge[0];
				$stockFieldId	 = $sge[1];
				$stkFValidation	 = $sge[2];
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">	
		addNewItemRow('tblAddFieldGroupItem', '<?=$stkGroupEntryId?>', '<?=$stockFieldId?>', 'Y', '<?=$stkFValidation?>', '<?=$selSubCategoryId?>');
	</script>
	<?php
			}
		}
	?>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>