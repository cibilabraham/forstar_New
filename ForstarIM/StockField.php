<?php
	require("include/include.php");
	ob_start();
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
	
	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {		
		$labelName		= "";
		$p["labelName"]	= "";
		$inputType	= "";
		$p["inputType"]	= "";
		$stkFieldName	= "";
		$p["stkFieldName"] = "";		
		$hidEditId	= "";
	}
	// End

	# Add New Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}
		

	#Add a stock
	if ($p["cmdAdd"]!="") {
		
		$labelName = addSlash(trim($p["labelName"]));
		$inputType = $p["inputType"];
		$stkFieldName = getValidString($labelName); // Remove Special chars and space
		$stkFieldValue = trim($p["stkFieldValue"]);
		$stkFieldSize = trim($p["stkFieldSize"]);
		//$stkFieldValidation = $p["stkFieldValidation"];
		$fieldDataType	= $p["fieldDataType"];
		$unitGroup	= $p["unitGroup"];
		
		if ($labelName!="" && $inputType && $stkFieldName!="") {

			$stockFieldRecIns = $stockFieldObj->addStockField($labelName, $inputType, $stkFieldName, $stkFieldValue, $stkFieldSize, $fieldDataType, $userId, $unitGroup);
			
			if ($stockFieldRecIns) {		
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddStockField);
				$sessObj->createSession("nextPage",$url_afterAddStockField.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStockField;
			}
			$stockFieldRecIns		=	false;
		}
		$hidEditId = "";
	}

	#Update a Stock Group
	if ($p["cmdSaveChange"]!="") {
		
		$stockFieldId	=	$p["hidStockFieldId"];
		$labelName = addSlash(trim($p["labelName"]));
		$inputType = $p["inputType"];
		$stkFieldName = getValidString($labelName); // Remove Special chars and space
		$stkFieldValue = trim($p["stkFieldValue"]);
		$stkFieldSize = trim($p["stkFieldSize"]);
		//$stkFieldValidation = $p["stkFieldValidation"];
		$fieldDataType	= $p["fieldDataType"];	
		$unitGroup	= $p["unitGroup"];
		
		if ($stockFieldId!="" && $labelName!="" && $inputType && $stkFieldName!="") {
			$stockFieldRecUptd	= $stockFieldObj->updateStockField($stockFieldId, $labelName, $inputType, $stkFieldName, $stkFieldValue, $stkFieldSize, $fieldDataType, $unitGroup);		
		}
	
		if ($stockFieldRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStockFieldUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStockField.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStockFieldUpdate;
		}
		$stockFieldRecUptd	=	false;
		$hidEditId		= 	"";
	}


	# Edit  a Stock
	if ($p["editId"]!="") {
		$addMode	= 	false;
		$editId		=	$p["editId"];
		$editMode	=	true;
		$stockFieldRec	=	$stockFieldObj->find($editId);
		$editStockFieldId	= $stockFieldRec[0];
		$labelName = $stockFieldRec[1];
		$inputType = $stockFieldRec[2];
		$stkFieldName = $stockFieldRec[3];
		$stkFieldValue = $stockFieldRec[4];
		$stkFieldSize = $stockFieldRec[5];
		$fieldDataType = $stockFieldRec[6];
		$unitGroup	= $stockFieldRec[7];
	}


	# Delete Stock
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockFieldId	=	$p["delId_".$i];

			if ($stockFieldId!="") {
				# Checking rec Exist
				$stkFRecInUse = $stockFieldObj->chkStockFieldRecExist($stockFieldId);
				if (!$stkFRecInUse) {					
					// Need to check the selected Category is link with any other process
					$stockFieldRecDel = $stockFieldObj->deleteStockField($stockFieldId);	
				}
			}
		}

		if ($stockFieldRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStockField);
			$sessObj->createSession("nextPage",$url_afterDelStockField.$selection);
		} else {
			if ($stkFRecInUse) $errDel = $msg_failDelStockField."<br> Stock Field is linked with Stock Group section";
			else $errDel	=	$msg_failDelStockField;
		}
		$stockFieldRecDel	=	false;
		$hidEditId	= 	"";
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockFieldId	=	$p["confirmId"];
			if ($stockFieldId!="") {
				// Checking the selected fish is link with any other process
				$stockRecConfirm = $stockFieldObj->updateStockFieldconfirm($stockFieldId);
			}

		}
		if ($stockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmstock);
			$sessObj->createSession("nextPage",$url_afterDelStockField.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$stockFieldId = $p["confirmId"];
			if ($stockFieldId!="") {
				#Check any entries exist
				
					$stockRecConfirm = $stockFieldObj->updateStockFieldReleaseconfirm($stockFieldId);
				
			}
		}
		if ($stockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmstock);
			$sessObj->createSession("nextPage",$url_afterDelStockField.$selection);
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
	

	# List all Stock Field
	$stockFieldRecords	= $stockFieldObj->fetchAllPagingRecords($offset, $limit);
	$stockFieldRecSize	= sizeof($stockFieldRecords);

	## -------------- Pagination Settings II -------------------		
	$numrows	=  sizeof($stockFieldObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	# List all Unit Group 
	//$unitGroupRecords = $unitGroupObj->fetchAllRecords();
	$unitGroupRecords = $unitGroupObj->fetchAllActiveRecords();
	# Input type Array
	//$inpuTypeArr = array("T"=>"Text", "C"=>"Checkbox", "R"=>"Radio");
	$inputTypeArr  = array("T"=>"Textbox", "C"=>"Checkbox");
	$validationArr = array("N"=>"NO", "Y"=>"YES");
	$fieldDTypeArr = array("NUM"=>"NUMBER", "ANUM"=>"ALPHANUMERIC");

	
	if ($editMode) $heading	= $label_editStockField;
	else $heading	= $label_addStockField;	
	
	$ON_LOAD_PRINT_JS	= "libjs/StockField.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

	<form name="frmStockField" action="StockField.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<? if($err!="" ){ ?>
		<tr>
			<td height="10" align="center" class="err1"><?=$err;?></td>	
		</tr>
		<?}?>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Stock Field";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="50%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('StockField.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateStockField(document.frmStockField);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockField.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStockField(document.frmStockField);">												</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidStockFieldId" value="<?=$editStockFieldId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
		<tr>
			<TD colspan="2" nowrap style="padding-left:10px; padding-right:10px;">
			<table>
				<TR>
				<TD valign="top">
				<!--<fieldset>-->
				<?php
					$entryHead = "";
					$rbTopWidth = "";
					require("template/rbTop.php");
				?>
					<table>
						<TR>
							<TD class="fieldName" nowrap="true">*Label Name</TD>
							<td nowrap="true">
							<input name='labelName' type='text' id='labelName' value='<?=$labelName?>' size='24' autoComplete='off'>
							</td>
						</TR>
						<TR>
							<TD class="fieldName" nowrap="true">*Field Type</TD>
							<td nowrap="true">
							<select name='inputType' id='inputType' onchange="showFields();">
							<option value=''>-- Select --</option>
							<?php
							if (sizeof($inputTypeArr)>0) {	
								foreach ($inputTypeArr as $itKey=>$itValue) {
									$selected = "";
									if ($inputType==$itKey) $selected = "Selected";
							?>	
							<option value="<?=$itKey?>" <?=$selected?>><?=$itValue?></option>
							<?php
									}
								}
							?>
							</select>
							</td>
						</TR>
						<TR id="fDataTypeRow">
							<TD class="fieldName" nowrap="true">Field Data Type</TD>
							<td nowrap="true">
								<select name='fieldDataType' id='fieldDataType'>
								<option value=''>-- Select --</option>
								<?php
								if (sizeof($fieldDTypeArr)>0) {	
									foreach ($fieldDTypeArr as $itKey=>$itValue) {
										$selected = "";
										if ($fieldDataType==$itKey) $selected = "Selected";
								?>	
								<option value="<?=$itKey?>" <?=$selected?>><?=$itValue?></option>
								<?php
										}
									}
								?>
								</select>
							</td>
						</TR>						
					</table>
				<?php
					require("template/rbBottom.php");
				?>
				<!--</fieldset>-->
				</TD>
				<td>&nbsp;</td>
				<td valign="top">
				<!--<fieldset>-->
				<?php
					$entryHead = "";
					$rbTopWidth = "";
					require("template/rbTop.php");
				?>
					<table>
						<TR id="fUnitGroupRow">
							<TD class="fieldName" nowrap="true">Unit Group</TD>
							<td nowrap="true">
								<select name="unitGroup" id="unitGroup">
								<option value="">--Select--</option>
								<?php
								foreach ($unitGroupRecords as $cr) {
									$unitGroupId	= $cr[0];
									$groupName	= stripSlash($cr[1]);	
									$selected = "";
									if ($unitGroup==$unitGroupId) $selected = "Selected";		
								?>
								<option value="<?=$unitGroupId?>" <?=$selected?>><?=$groupName?></option>
								<? }?>
								</select>
							</td>
						</TR>
						<TR>
							<TD class="fieldName" nowrap="true">Default Value</TD>
							<td nowrap="true">
								<input name='stkFieldValue' type='text' id='stkFieldValue' size='3' value='<?=$stkFieldValue?>' autocomplete='off'>
							</td>
						</TR>
						<TR id="fSizeRow">
							<TD class="fieldName" nowrap="true">Size</TD>
							<td nowrap="true">
								<input name='stkFieldSize' type='text' id='stkFieldSize' size='3' value='<?=$stkFieldSize?>' autocomplete='off'>
							</td>
						</TR>
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
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockField.php');">&nbsp;&nbsp;							<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStockField(document.frmStockField);">			
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockField.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStockField(document.frmStockField);">					
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
		<?php
			}			
			# Listing Category Starts
		?>
			</table>
		</td>
	</tr>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
		<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Field  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">&nbsp;</td>
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
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockFieldRecSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockField.php',700,600);"><? }?>
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
									if ($errDel!="") {
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
	<table cellpadding="2"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
		if (sizeof($stockFieldRecords) > 0 ) {
			$i	=	0;
	?>
	<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockField.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockField.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockField.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Label Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Field Type</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Default Value</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Size</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Data Type</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Unit Group</th>
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($stockFieldRecords as $sfr) {
		$i++;
		$stockFieldId 	= $sfr[0];		
		$stkLName 	= stripSlash($sfr[1]);
		$fieldType	= $sfr[2];
		$fieldName		= $sfr[3];
		$fieldDefaultValue 	= $sfr[4];
		$fieldSize		= $sfr[5];
		$fieldDTypeValue	= $sfr[6];
		$selUnitGroupName	= $sfr[8];
		$active=$sfr[9];
		$existingrecord=$sfr[10];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
		<?php 
		//echo $existingrecord ;
		if($existingrecord){
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockFieldId;?>" class="chkBox">
		<?php 
		}
		?>
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stkLName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$inputTypeArr[$fieldType];?></td>		
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=$fieldDefaultValue?></td>	
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=($fieldSize!=0)?$fieldSize:"";?></td>
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=$fieldDTypeArr[$fieldDTypeValue]?></td>	
		<td class="listing-item" nowarp align="left" style="padding-left:10px; padding-right:10px;"><?=$selUnitGroupName?></td>			
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<? if ($active==0){  ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stockFieldId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='StockField.php';" >
		<? } ?>
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){  ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stockFieldId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecord==0){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$stockFieldId;?>,'confirmId');" >
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
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockField.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockField.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockField.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\">>></a> ";
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
	<tr>
		<TD colspan="3">
			<table align="center">
				<TR>
					<TD>
						<table>
							<TR>
								<TD>
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockFieldRecSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockField.php',700,600);"><? }?>
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
	<? if ($addMode || $editMode) {?>
		<script>
			showFields();
		</script>
	<? }?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>