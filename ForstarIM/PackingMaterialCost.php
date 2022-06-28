<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
	$packingMaterialCost = "PMC";
	$selection 	= "?pageNo=".$p["pageNo"]."&selRateList=".$p["selRateList"];

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

	# Reset 
	if ($p["category"]) 	$selCategoryId 		= $p["category"];
	if ($p["subCategory"]) 	$selSubCategoryId 	= $p["subCategory"];
	if ($p["selStock"]) 	$selStockId 		= $p["selStock"];
	if ($p["selSupplier"]) 	$selSupplierId 		= $p["selSupplier"];

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") {
		$addMode  = false;	
		$editMode = false;
	}
	
	#Add a Record
	if ($p["cmdAdd"]!="") {
		$selStockId     = $p["selStock"];
		$selSupplierId  = $p["selSupplier"];
		$costPerItem	= $p["costPerItem"];			
		$pmcRateListId	= $p["pmcRateList"];
		$supplierRateListId = $p["hidSupplierRateListId"];

		# Creating a New Rate List
		if ($pmcRateListId=="") {
			$rateListName = "PKGMATERIAL"."(".date("dMy").")";
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $cpyRateList, $userId, $packingMaterialCost, $pCurrentRateListId);
			if ($rateListRecIns) $pmcRateListId = $manageRateListObj->latestRateList($packingMaterialCost);	
		}

		# Check Rec Exist
		$recExist	= $packingMaterialCostObj->chkRecExist($selStockId, $pmcRateListId, $cRecId);

		if ($selStockId!="" && $selSupplierId!="" && $costPerItem!="" && $pmcRateListId!="" && !$recExist) {
			$packingMaterialCostRecIns = $packingMaterialCostObj->addPackingMaterialCost($selStockId, $selSupplierId, $costPerItem, $pmcRateListId, $supplierRateListId);
			if ($packingMaterialCostRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddPackingMaterialCost);
				$sessObj->createSession("nextPage",$url_afterAddPackingMaterialCost.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddPackingMaterialCost;
			}
			$packingMaterialCostRecIns = false;
		} else {
			$addMode = true;
			$err	 = $msg_failAddPackingMaterialCost."<br>"."Please make sure the stock you have selected is not duplicate";
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {		
		$packingMaterialCostRecId	= $p["hidPackingMaterialCostId"];
		$selStockId     = $p["selStock"];
		$selSupplierId  = $p["selSupplier"];
		$costPerItem	= $p["costPerItem"];
		$pmcRateListId	= $p["pmcRateList"];
		$supplierRateListId = $p["hidSupplierRateListId"];

		# Check Rec Exist
		$recExist	= $packingMaterialCostObj->chkRecExist($selStockId, $pmcRateListId, $packingMaterialCostRecId);

		if ($packingMaterialCostRecId!="" && $selStockId!="" && $selSupplierId!="" && $costPerItem!="" && $pmcRateListId!="" && !$recExist) {
			$packingMaterialCostRecUptd = $packingMaterialCostObj->updatePackingMaterialCostRec($packingMaterialCostRecId, $selStockId, $selSupplierId, $costPerItem, $pmcRateListId, $supplierRateListId);
		}
	
		if ($packingMaterialCostRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPackingMaterialCostUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingMaterialCost.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPackingMaterialCostUpdate;
		}
		$packingMaterialCostRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$packingMaterialCostRec	 =	$packingMaterialCostObj->find($editId);
		$editPackingMaterialCostId =	$packingMaterialCostRec[0];

		if ($p["editSelectionChange"]=='1' || $p["category"]=="") {
			$selCategoryId	= $packingMaterialCostRec[4];
		} else {
			$selCategoryId	= $p["category"];
		}	

		if ($p["editSelectionChange"]=='1' || $p["subCategory"]=="") {
			$selSubCategoryId = $packingMaterialCostRec[5];
		} else {
			$selSubCategoryId = $p["subCategory"];
		}

		if ($p["editSelectionChange"]=='1' || $p["selStock"]=="") {
			$selStockId = $packingMaterialCostRec[1];
		} else {
			$selStockId = $p["selStock"];
		}

		if ($p["editSelectionChange"]=='1' || $p["selSupplier"]=="") {
			$selSupplierId = $packingMaterialCostRec[2];
			$supplierRateListId	= $packingMaterialCostRec[7];
		} else {
			$selSupplierId = $p["selSupplier"];
			# current supplier Rate List Id	
			$supplierRateListId = $supplierRateListObj->latestRateList($selSupplierId);
		}

		if ($p["editSelectionChange"]=='1' || $p["costPerItem"]=="") {
			$costPerItem = $packingMaterialCostRec[3];
		} else {
			$costPerItem = $packingMaterialCostObj->getStockCost($selStockId, $selSupplierId, $supplierRateListId);
		}

		$pmcRateListId	= $packingMaterialCostRec[6];

		#List all Sub Category		
		$subCategoryRecords = $subcategoryObj->filterRecords($selCategoryId);
	
		#List all Stock Records
		$stockRecords	= $packingMaterialCostObj->filterStockRecords($selCategoryId, $selSubCategoryId);
	
		#List all Supplier stock
		$supplierStockRecords = $packingMaterialCostObj->filterSupplierRecords($selStockId);		
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$packingMaterialCostRecId	=	$p["delId_".$i];

			if ($packingMaterialCostRecId!="") {
				// Need to check the selected Category is link with any other process
				$packingMaterialCostRecDel = $packingMaterialCostObj->deletePackingMaterialCostRec($packingMaterialCostRecId);
			}
		}
		if ($packingMaterialCostRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPackingMaterialCost);
			$sessObj->createSession("nextPage",$url_afterDelPackingMaterialCost.$selection);
		} else {
			$errDel	=	$msg_failDelPackingMaterialCost;
		}
		$packingMaterialCostRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$packingMaterialCostRecId	=	$p["confirmId"];
			if ($packingMaterialCostRecId!="") {
				// Checking the selected fish is link with any other process
				$packingMaterialCostRecConfirm = $packingMaterialCostObj->updatePackingMaterialconfirm($packingMaterialCostRecId);
			}

		}
		if ($packingMaterialCostRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirm);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$packingMaterialCostRecId = $p["confirmId"];
			if ($packingMaterialCostRecId!="") {
				#Check any entries exist
				
					$packingMaterialCostRecConfirm = $packingMaterialCostObj->updatePackingMaterialReleaseconfirm($packingMaterialCostRecId);
				
			}
		}
		if ($packingMaterialCostRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirm);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}


	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($packingMaterialCost);			
	#--------------------------------------------

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Packing Material Cost
	$packingMaterialCostResultSetObj = $packingMaterialCostObj->fetchAllPagingRecords($offset, $limit, $selRateList);
	$packingMaterialCostRecordSize	= $packingMaterialCostResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allPackingMaterialCostResultSetObj = $packingMaterialCostObj->fetchAllRecords($selRateList);
	$numrows	=  $allPackingMaterialCostResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	# Rate List
	$pmcRateListRecords = $manageRateListObj->fetchAllRecords($packingMaterialCost);
	if ($addMode) $pmcRateListId = $manageRateListObj->latestRateList($packingMaterialCost);

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";


	if ($addMode || $editMode) {
		# List all Category
		//$categoryRecords = $categoryObj->fetchAllRecords();
		$categoryRecords = $categoryObj->fetchAllRecordsActivecategory();
	}

	if ($addMode) {		
		#List all Sub Category
		$selCategoryId	= $p["category"];
		$subCategoryRecords = $subcategoryObj->filterRecords($selCategoryId);
	
		#List all Stock Records
		$selSubCategoryId = $p["subCategory"];
		$stockRecords	= $packingMaterialCostObj->filterStockRecords($selCategoryId, $selSubCategoryId);

		#List all Supplier Records
		$selStockId = $p["selStock"];
		# Get Latest Supplier Stock Records
		$supplierStockRecords = $packingMaterialCostObj->filterSupplierRecords($selStockId);

		#Find the Cost of stock
		$selSupplierId = $p["selSupplier"];
		# current supplier Rate List Id	
		$supplierRateListId = $supplierRateListObj->latestRateList($selSupplierId);
		$costPerItem = $packingMaterialCostObj->getStockCost($selStockId, $selSupplierId, $supplierRateListId);
	}
	
	#heading Section
	if ($editMode) $heading	=	$label_editPackingMaterialCost;
	else	       $heading	=	$label_addPackingMaterialCost;

	$ON_LOAD_PRINT_JS = "libjs/PackingMaterialCost.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmPackingMaterialCost" action="PackingMaterialCost.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?> 
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Packing Material Cost Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="40%">	
		<?
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('PackingMaterialCost.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validatePackingMaterialCost(document.frmPackingMaterialCost);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMaterialCost.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingMaterialCost(document.frmPackingMaterialCost);">												</td>
					<?}?>
				</tr>
	<input type="hidden" name="hidPackingMaterialCostId" value="<?=$editPackingMaterialCostId;?>">
	<tr><TD height="10"></TD></tr>
	<tr>
	  <td colspan="2" nowrap align="center">
	<table width="200">
		<tr>
		<td nowrap class="fieldName" >*Category</td>
		<td nowrap>		
                <select name="category" onchange="<?php if ($addMode==true) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();  <? }?>">
                <option value="">--select--</option>
                <?php
		foreach ($categoryRecords as $cr) {
			$categoryId	=	$cr[0];
			$categoryName	=	stripSlash($cr[1]);
			$selected = ($selCategoryId==$categoryId)?"Selected":"";				
		?>
               <option value="<?=$categoryId?>" <?=$selected;?>><?=$categoryName;?></option>
                 <? }?>
                </select>
			</td>
		</tr>	
		<tr>
			<td nowrap class="fieldName" >*Sub Category</td>
			<td nowrap>			
			<select name="subCategory" onchange="<? if ($addMode==true) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();  <? }?>">
                        <option value="">--select--</option>
                        <?
			foreach ($subCategoryRecords as $scr) {
				$subCategoryId		=	$scr[0];
				$subCategoryName	=	stripSlash($scr[2]);
				$selected = ($selSubCategoryId==$subCategoryId)?"Selected":"";
			?>
                        <option value="<?=$subCategoryId?>" <?=$selected;?>><?=$subCategoryName;?></option>
                         <? }?>
                        </select></td></tr>	
	<tr>
			<td nowrap class="fieldName" >*Stock </td>
			<td nowrap>			
			<select name="selStock" onchange="<? if ($addMode==true) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
                        <option value="">--select--</option>
                        <?php
			foreach ($stockRecords as $sr) {				
				$stockId	= $sr[0];
				$stockName	= stripSlash($sr[2]);
				$selected = ($selStockId==$stockId)?"Selected":"";
			?>
                        <option value="<?=$stockId?>" <?=$selected;?>><?=$stockName;?></option>
                        <? }?>
                                              </select></td></tr>
			<tr>
			<td nowrap class="fieldName" >* Supplier </td>
			<td nowrap>			
			<select name="selSupplier" onchange="<? if ($addMode==true) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
                        <option value="">--select--</option>
                        <?php
			foreach ($supplierStockRecords as $sr) {
				$supplierId	=	$sr[1];
				$supplierName	=	stripSlash($sr[2]);
				$selected = ($selSupplierId==$supplierId)?"Selected":"";
			?>
                        <option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName;?></option>
                        <? }?>
                       </select></td></tr>			
					
					<tr>
					  <td class="fieldName" nowrap >*Cost (in Rs.)</td>
					  <td class="listing-item"><input type="text" name="costPerItem" size="5" id="costPerItem" value="<?=$costPerItem?>" style="text-align:right;"></td>
					</tr>
	<tr><TD colspan="2">
			<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
			<input type="hidden" name="pmcRateList" id="pmcRateList" value="<?=$pmcRateListId?>">
	</TD></tr>
		<!--<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="pmcRateList">
			<?
			/*
			if (sizeof($pmcRateListRecords)>0) {
				foreach ($pmcRateListRecords as $prl) {
					$mRateListId	= $prl[0];
					$rateListName		= stripSlash($prl[1]);
					$startDate		= dateFormat($prl[2]);
					$displayRateList = $rateListName."&nbsp;(".$startDate.")";
					if ($addMode) $rateListId = $selRateList;
					else $rateListId = $pmcRateListId;
					$selected = "";
					if ($rateListId==$mRateListId) $selected = "Selected";
			*/
			?>
                    	  <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      	<? 
			//	}
			?>
			<?
			//} else {
			?>
			 <option value="">-- Select --</option>
			<?
			//}
			?>
                                            </select></td>
						</tr>-->
					</table></td>
					</tr>
					<tr>
						<td colspan="2"  height="5" ></td>
					</tr>
					<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMaterialCost.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePackingMaterialCost(document.frmPackingMaterialCost);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMaterialCost.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingMaterialCost(document.frmPackingMaterialCost);">												</td>
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
		<table width="30%">
		<TR><TD>
		<?php			
			$entryHead = "";
			require("template/rbTop.php");
		?>
		<table cellpadding="4" cellspacing="0">
		<tr>
		<td nowrap="nowrap" style="padding:5px;">
		<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td>
		<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                <option value="">-- Select --</option>
                <?php
		foreach ($pmcRateListRecords as $prl) {
			$mRateListId	= $prl[0];
			$rateListName	= stripSlash($prl[1]);
			$startDate	= dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected =  ($selRateList==$mRateListId)?"Selected":"";
		?>
                <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$packingMaterialCost?>'"></td>
		<? }?>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Material Cost Master  </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingMaterialCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingMaterialCost.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
						<td colspan="2" style="padding-left:10px;padding-right:10px;" >
 		<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($packingMaterialCostRecordSize) {
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
      				$nav.= " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Code</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cost</th>	
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
			while ($pmcr=$packingMaterialCostResultSetObj->getRow()) {
				$i++;
				$packingMaterialCostRecId = $pmcr[0];				
				$materialName		  = $pmcr[4];
				$materialCode		  = $pmcr[5];
				$materialCost		  = $pmcr[3]; 
				$active=$pmcr[8];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> >
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$packingMaterialCostRecId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$materialName?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$materialCode?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$materialCost?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$packingMaterialCostRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange'); this.form.action='PackingMaterialCost.php';" ><? } ?></td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$packingMaterialCostRecId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$packingMaterialCostRecId;?>,'confirmId');" >
			<?php } ?>
			<?php }?>
			<? }?>
			
			
			
			</td>

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
      				$nav.= " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingMaterialCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingMaterialCost.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
<input type="hidden" name="hidSupplierRateListId" value="<?=$supplierRateListId?>">
		<tr>
			<td height="10"></td>
		</tr>
		<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
	<? 
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
	ensureInFrameset(document.frmPackingMaterialCost);
	//-->
	</script>
<? 
	}
?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>
