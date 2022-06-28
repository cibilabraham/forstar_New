<?php
	require("include/include.php");
	require_once("lib/ImportStock_ajax.php");
	$file = $sessObj->getValue("fileName");
	$incHead = $sessObj->getValue("incHeading");
	//$stockType = $sessObj->getValue("stockType");
	$userId = $sessObj->getValue("userId");

	$categoryId		= $sessObj->getValue("CategoryId");
	$subCategoryId  = $sessObj->getValue("SubCategoryId");
	
	// Corresponding DB Field
	$fieldDBArr = array("Name"=>"name", "Description"=>"description", "Re-order Required (Yes/No)"=>"reorder_required", "Reorder Point"=>"reorder", "Quantity in Stock"=>"quantity", "Additional Holding Percent"=>"additional_holding_percent", "Stocking Period (Month)"=>"stocking_period", "No of Layers"=>"layer","Type of Carton"=>"carton","Brand"=>"packing_brand","Color"=>"packing_color","Packing Weight"=>"packing_weight","Packing(Kg x Nos)"=>"packing","Suitable For (Frozen Code)"=>"","No.of Colors"=>"num_colors","Dimension"=>"packing_dimension","Carton Weight"=>"carton_weight", "Basic Unit"=>"unit","Basic Qty"=>"basic_unit_qty","Packed Qty"=>"min_order_unit","Min Order/Package"=>"min_order_qty_per_unit");

	$fieldMandatoryArr = array("Name", "Quantity in Stock", "No of Layers", "Carton Weight", "Basic Unit", "Basic Qty", "Packed Qty", "Min Order/Package");

	$inputTypeArr = array("T"=>"Text", "C"=>"Checkbox", "R"=>"Radio");

	/*
	if ($stockType=='O') $stockListArray = $importStockObj->readOrdinaryStock($file, $incHead);
	else if ($stockType=='P') $stockListArray = $importStockObj->readPackingStock($file, $incHead);
	else $stockListArray="";
	*/
	list($stockListArray,$fieldArr, $stockType) = $importStockObj->readStockFromCSV($file, $incHead, $categoryId, $subCategoryId, $databaseConnect, $stockGroupObj);
	
	

	$errMsg = "";
	// import stocks
	if ($p['cmdImport']!='')
	{
		$addOrdinaryStockRecs = false;
		$addPackingStockRecs = false;
		$rc = $p["RowCount"];

		//printr($p);

//INSERT INTO table SET a=1, b=2, c=3

		//================================================================

		$i = 0;
		$recInserted = false;
		$existCount = 0;
		foreach ($stockListArray as $stl) {
				$import = ( $p["chkImport_".$i]!="" ) ? "Y" : "N";
				if ($import=="Y") {		
						$j=0;
						$staticFieldArr = array();
						$dynamicFieldArr = array();
						$stkName = "";
						foreach ($fieldArr as $key=>$fieldHead) 
						{
							//$rowVal = $stl[$j];
								$dynamicField = false;
								$dynamicFieldName = "";
								if (is_array($fieldHead)) {
									$dhArr = $fieldHead;
									$dynamicField = true;
									$fieldHead = $dhArr[0];
									$dynamicFieldName = $dhArr[1];
									$stkGroupEntryId = $dhArr[2];									
								}
					
								if ($dynamicField) {
									$dynamicFieldId    = $p["stkGroupEntryId__".$i."__".$j]; 
									$dynamicFieldValue = trim($p[$dynamicFieldName."__".$i."__".$j]); 
									$dynamicFieldArr[] = "stk_group_entry_id=$dynamicFieldId, field_value=$dynamicFieldValue";

								} else {
									$staticFieldName = $fieldDBArr[$fieldHead];
									$staticFieldValue = trim($p[$staticFieldName."__".$i."__".$j]);	
									
									if ($staticFieldName=="reorder_required") {
										$staticFieldValue = ($staticFieldValue=="Yes")?"Y":"N";	
									}

									if ($staticFieldName=="name") {
										$stkName = $staticFieldValue;
										$code =	$stockObj->getStockCode($staticFieldValue);
										$staticFieldArr[] = "code='".$code."'";
									}

									$staticFieldArr[] = $staticFieldName."='".$staticFieldValue."'";
								}

								$j++;	
						}

						// Check Rec exist
						$recExist = $stockObj->checkStockExist($stkName);
						if (!$recExist) {
							
							$stockRecIns =	$stockObj->importStockFromCSV($categoryId, $subCategoryId, $staticFieldArr, $stockType, $userId);
							if ($stockRecIns) $recInserted = true;

							if ($stockRecIns && sizeof($dynamicFieldArr)>0) {
								#Find the Last inserted Id 
								$stkId = $databaseConnect->getLastInsertedId();
								if ($stkId>0) {
									$stkDynamicFieldRecIns = $stockObj->importStkDynamicField($stkId, $dynamicFieldArr);
								}
							}
						} else {
							$existCount++;
							$recInserted = true;
						}
				}
				$i++;
			}



		//===============================================================

	

		if ($recInserted) {
			if ($existCount>0) $sessObj->createSession("displayMsg","Some selected stocks are already in database. Please check in the stock entry list.");
			else $sessObj->createSession("displayMsg","Selected stocks imported successfully.");
			$sessObj->createSession("nextPage","StockEntry.php?categoryFilter=$categoryId&subCategoryFilter=$subCategoryId");
		} 
	}
	// cancel process 
	if( $p["cmdCancel"]!="" )
	{
		header("Location:ImportStock.php");
		exit;
	}
?>

<?
	$ON_LOAD_SAJAX = "Y";
	$ON_LOAD_PRINT_JS	= "libjs/ImportStock.js";
	require("template/topLeftNav.php");
?>
<form name="FrmStockCategory" id="FrmStockCategory" action="AssignStockCategories.php" method="post" >
<table cellspacing="0"  align="center" cellpadding="0" width="80%">
	<tr>
		<td height="40" align="center" class="err1" ><? if($errMsg!="" ) echo $errMsg; ?></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Step 2: Assign Stock</td>
							</tr>
							<tr>
								<td class="fieldName"  align='center' colspan="3" nowrap >
								<input type="submit" class='button'  name="cmdCancel" value="Cancel" onClick="return cancel('ImportStock.php');" >&nbsp;&nbsp;
								<input type="submit" class='button'  name="cmdImport" value="Import" onClick="return validateBulkStockEntry(this.form);">
								</td>
							</tr>
<!-- Dynamic Import Starts Here ------------------------------------------------>
<? if (sizeof($stockListArray) > 0 && sizeof($fieldArr) > 0) {?>
	<tr>
		<td width="1" ></td>
		<td colspan="2" Style="padding-top:10px;padding-left:10px;padding-right:10px;padding-bottom:10px;" >
		<table cellpadding="0"  width="100%" cellspacing="1" cellpadding='3' bgcolor="#D3D3D3" border="0" align="center" id="dataTable">
	<?
	if( sizeof($stockListArray) > 0 ) {
		$tdPaddingHead = "style='padding-left:5px;padding-right:5px;'";
	?>
	<tr bgcolor='#F5F5F5'>
		<td class="listing-head" nowrap align='center' <?=$tdPaddingHead;?>>Import<br>
			<INPUT TYPE="checkbox" class="chkBox" name="chkAllStks" id="chkAllStks" onClick="checkAll(this.form,'chkImport_'); ">
		</td>
		<?php
			foreach ($fieldArr as $key=>$fieldHead) 
			{
				$mandatory = "";
				if (in_array($fieldHead, $fieldMandatoryArr)) $mandatory="*";

				if (is_array($fieldHead)) $fieldHead = $fieldHead[0];
		?>
			<td class="listing-head" align='center' <?=$tdPaddingHead;?>><?=$mandatory?><?=$fieldHead;?></td>			
		<?php
			}
		?>
	</tr>
	
	<?php
		$i = 0;
		foreach ($stockListArray as $stl) {

			$recExist = $stockObj->checkStockExist(trim($stl[0]));
			
			$title="";
			if ($recExist) {
				$tdPaddingChk = "Style='padding-left:2px;padding-right:2px; background-color:red;'";	
				$tittle = "This record is already in database";
			} else {
				$tdPaddingChk = "Style='padding-left:2px;padding-right:2px;'";	
			}

			$tdPadding = "Style='padding-left:2px;padding-right:2px;'";
	?>
	<tr bgcolor="white" align='center' title="<?=$tittle?>">
		<td <?=$tdPaddingChk;?> >
			<INPUT TYPE="checkbox" class="chkBox" value="Y" name="chkImport_<?=$i;?>" id="chkImport_<?=$i;?>" rel="validate">
			<INPUT TYPE="hidden" value="<?=$recExist?>" name="hdnRecExist_<?=$i;?>" id="hdnRecExist_<?=$i;?>" />
		</td>
		<?php
			$j=0;
			foreach ($fieldArr as $key=>$fieldHead) 
			{
				$rowVal = $stl[$j];

				$dynamicField = false;
				$dynamicFieldName = "";
				if (is_array($fieldHead)) {
					$dhArr = $fieldHead;
					$dynamicField = true;
					$fieldHead = $dhArr[0];
					$dynamicFieldName = $dhArr[1];
					$stkGroupEntryId = $dhArr[2];
					$stkFieldType	= $inputTypeArr[ $dhArr[3]];
					$stkFieldVDation = $dhArr[4];
				}

		?>	
			<td <?=$tdPadding;?> id="<?=$i;?>_<?=$j;?>">
				<?php
				if ($dynamicField) {
					$validateFlag = "";
					if ($stkFieldVDation=="Y") $validateFlag = "validate_".$i."_".$j;
					
				?>
					<input type="hidden" name="stkGroupEntryId__<?=$i;?>__<?=$j;?>" id="stkGroupEntryId__<?=$i;?>__<?=$j;?>" value="<?=$stkGroupEntryId?>" >
					<input name="<?=$dynamicFieldName?>__<?=$i;?>__<?=$j;?>" type="<?=$stkFieldType?>" id="<?=$dynamicFieldName?>__<?=$i;?>__<?=$j;?>" value="<?=$rowVal;?>" rel="<?=$validateFlag;?>">
				<?php 
					} else {
						 $fieldName = $fieldDBArr[$fieldHead];

							
							$validateFlag = "";
							if (in_array($fieldHead, $fieldMandatoryArr)) $validateFlag = "validate_".$i."_".$j;

							// Basic Unit Drop Down
							if ($fieldName=="unit") {
								$unitRecords = $stockObj->filterUnitRecs($subCategoryId);
				?>
									<select name="<?=$fieldName?>__<?=$i;?>__<?=$j;?>" id="<?=$fieldName?>__<?=$i;?>__<?=$j;?>" rel="<?=$validateFlag;?>">
									<option value="">-- Select --</option>
									<? 
										foreach($unitRecords as $ur) {
											$stockItemUnitId = $ur[0];
											$unitName = $ur[1];
											$selected = ($rowVal==$unitName)?"Selected":"";
									?>						
										<option value="<?=$stockItemUnitId?>" <?=$selected?>><?=$unitName?></option>
										<? }?>
									  </select>	
							<?php
								// Type of Carton Drop down
								} else if ($fieldName=="carton") {	
							?>	
							<select name="<?=$fieldName?>__<?=$i;?>__<?=$j;?>" id="<?=$fieldName?>__<?=$i;?>__<?=$j;?>" rel="<?=$validateFlag;?>">
								<option value="U" <? if ($importStockObj->startsWith($rowVal,"U",false)) echo "selected";?>>Univ</option>
								<option value="T" <? if ($importStockObj->startsWith($rowVal,"T",false)) echo "selected";?>>Top</option>
								<option value="B" <? if ($importStockObj->startsWith($rowVal,"B",false)) echo "selected";?>>Bottom</option>
							</select>
							<?php
								} else {	
							?>
								<input type="text" name="<?=$fieldName?>__<?=$i;?>__<?=$j;?>" id="<?=$fieldName?>__<?=$i;?>__<?=$j;?>" size="12" value="<?=$rowVal;?>" tabindex="<?=$i;?>" rel="<?=$validateFlag;?>" >
					<?php 
							}	
						} ?>
			</td>
		<?php
				$j++;	
			}
		?>
	</tr>
		<?
			$i++;
			}
		}
		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			document.getElementById("chkAllStks").click();
			$(document).ready(function () {
				uncheckExistingRec();
			});
		//-->
		</SCRIPT>
		<input type="hidden" name="RowCount" id="RowCount" value="<?=$i?>" >
		<input type="hidden" name="FieldRowCount" id="FieldRowCount" value="<?=sizeof($fieldArr)?>" >
	</table>
	</td>
		</tr>
<? }?>
<!-- Dynamic Import Ends Here ----------------------------------------------------->

		<tr>
	<td class="fieldName"  align='center' colspan="3" nowrap >
		<input type="submit" class='button'  name="cmdCancel" value="Cancel" onClick="return cancel('ImportStock.php');" >&nbsp;&nbsp;
		<input type="submit" class='button'  name="cmdImport" value="Import" onClick="return validateBulkStockEntry(this.form);">
	</td>
							</tr>
							<tr>
								<tD colspan="3" height="10" ></tD>
							</tr>
					  </table>
					</td>
				</tr>
				
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<tr>
				<td height="10" align="center" ></td>
	</tr>
		<tr>
			<td height="10"></td>
		</tr>	
<input type="hidden" name="hidStockType" id="hidStockType" value="<?=$stockType?>">
  </table>
</form>
<?
require("template/bottomRightNav.php");
?>
