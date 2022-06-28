<?php
	$insideIFrame = "Y";
	require("include/include.php");	
	require_once("lib/ProductMRPMaster_ajax.php");
	
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	= "?pageNo=".$p["pageNo"]."&selProductCategory=".$p["selProductCategory"]."&selProductState=".$p["selProductState"]."&selProductGroup=".$p["selProductGroup"];

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
	if ($p["cmdAddNew"]!="") $addMode = true;
	#Cancel
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	#Add a Product
	if ($p["cmdAdd"]!="") {

		$selProduct		= $p["selProduct"];
		$mrp			= $p["mrp"];
		$productMRPRateList 	= $p["productMRPRateList"];
	
		$rowCount		= $p["hidTableRowCount"];
		
		# Check for existing rec
		$recExist	= $productMRPMasterObj->chkRecExist($selProduct, $productMRPRateList, $cId);

		if ($selProduct!="" && $productMRPRateList!="" && !$recExist) {
			$productMRPRecIns = $productMRPMasterObj->addProductMRP($selProduct, $mrp, $productMRPRateList, $userId);
			if ($productMRPRecIns) {
				# MRP Entry Id
				$productMRPEntryId = $databaseConnect->getLastInsertedId();

				for ($j=0; $j<$rowCount; $j++) {
					$selStatus = $p["status_".$j]; 
					if ($selStatus!='N') {
						$selState 	= $p["selState_".$j];
						$selDistributor = $p["selDistributor_".$j];
						$mrp 		= $p["mrp_".$j];
						if ($mrp!=0 && $productMRPEntryId!=0) {
							$addProductMrpExptRecs = $productMRPMasterObj->addProductMRPExpt($productMRPEntryId, $selState, $selDistributor, $mrp);
						}
					} // Status Check Ends here
				} // Row Count Loop Ends here
			} // Rec ins chk	
		}

		if ($productMRPRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddProductMRPMaster);
			$sessObj->createSession("nextPage",$url_afterAddProductMRPMaster.$selection);
		} else {
			$addMode	=	true;
			if ($recExist) $err = $msg_failAddProductMRPMaster."<br>".$msgProductMRPExistRec ;
			else $err	= $msg_failAddProductMRPMaster;
		}
		$productMRPRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$productMRPMasterRec	=	$productMRPMasterObj->find($editId);
		
		$editProductMRPId	= $productMRPMasterRec[0];
		$selProduct		= $productMRPMasterRec[1];
		$mrp			= $productMRPMasterRec[2];
		$productMRPRateListId 	= $productMRPMasterRec[3];	

		# Get All Product MRP recs
		$productExptRecs	= $productMRPMasterObj->getProductMRPExptRecs($editProductMRPId);	
		$productExptRecSize	= sizeof($productExptRecs);
	}

	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$productMRPId = $p["hidProductMRPId"];

		$selProduct		= $p["selProduct"];
		//$mrp			= $p["mrp"];
		$productMRPRateList 	= $p["productMRPRateList"];		
		$hidMRP			= $p["hidMRP"];

		$rowCount		= $p["hidTableRowCount"];


		# Check for existing rec
		$recExist	= $productMRPMasterObj->chkRecExist($selProduct, $productMRPRateList, $productMRPId);	

		if ($productMRPId!="" && $selProduct!="" && $productMRPRateList!="" && !$recExist) {
			$productMRPMasterRecUptd = $productMRPMasterObj->updateProductMRPMaster($productMRPId, $selProduct, $mrp, $productMRPRateList);

			$recUpdated = false;
			if ($productMRPMasterRecUptd) {
				for ($j=0; $j<$rowCount; $j++) {
					$selStatus = $p["status_".$j];
					$productExptEntryId = $p["productExptEntryId_".$j];

					if ($selStatus!='N') {
						$selState 	= $p["selState_".$j];
						$selDistributor = $p["selDistributor_".$j];
						$mrp 		= $p["mrp_".$j];
						if ($mrp!=0 && $productMRPId!=0 && $productExptEntryId=="") {
							$addProductMrpExptRecs = $productMRPMasterObj->addProductMRPExpt($productMRPId, $selState, $selDistributor, $mrp);
							$recUpdated = true;
						} else if ($mrp!=0 && $productMRPId!=0 && $productExptEntryId!="") {
							$uptdProductMRPExptRec = $productMRPMasterObj->updateProductMRPExpt($productExptEntryId, $selState, $selDistributor, $mrp);
							$recUpdated = true;
						}
					} // Status Check Ends here
					else if ($selStatus=='N' && $productExptEntryId!="") {
						$delProductMRPExptRec = $productMRPMasterObj->deleteProductMRPExpt($productExptEntryId);
						$recUpdated = true;
					}
				} // Row Count Loop Ends here				
			}

			# Update Sales Order Not Confirmed Rec
			//if ($mrp!=$hidMRP) {				
			if ($recUpdated) {
				$updateSORecs = $changesUpdateMasterObj->updateProductPriceInSO($productMRPRateList, $selProduct, $mrp);	
			}		
		}
	
		if ($productMRPMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductMRPMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductMRPMaster.$selection);
		} else {
			$editMode	=	true;
			if ($recExist) $err = $msg_failProductMRPMasterUpdate."<br>".$msgProductMRPExistRec ;
			else $err = $msg_failProductMRPMasterUpdate;
		}
		$productMRPMasterRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		$productUsed    = false;
		for ($i=1; $i<=$rowCount; $i++) {
			$productMRPId	=	$p["delId_".$i];
			$selProductId	=	$p["selProductId_".$i];
			$selRateListId	=	$p["selRateListId_".$i];			
			if ($productMRPId!="" ) {	
				$productMRPUsed = $productMRPMasterObj->chkProductMRPUsed($selProductId, $selRateListId);	
				// Need to check
				if (!$productMRPUsed) {	
					$deleteProductMRPExptRec = $productMRPMasterObj->delProductMRPException($productMRPId);
					$productMRPMasterRecDel = $productMRPMasterObj->deleteProductMRPMaster($productMRPId);
				}
				if ($productMRPUsed) $productUsed = true;
			}
		}
		if ($productMRPMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductMRPMaster);
			$sessObj->createSession("nextPage",$url_afterDelProductMRPMaster.$selection);
		} else {
			if ($productUsed) $errDel = $msg_failDelProductMRPMaster." The selected MRP is exist in SO. ";
			else $errDel	=	$msg_failDelProductMRPMaster;
		}
		$productMRPMasterRecDel	=	false;
	}


	$productMRP = "PMRP";
	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($productMRP);			
	#--------------------------------------------

	# Rate List
	$productMRPRateListRecords = $manageRateListObj->fetchAllRecords($productMRP);
	$rateListRec = $manageRateListObj->find($selRateList);
	$rateListStartDate = $rateListRec[2];
	$lastRateListId = $manageRateListObj->getLastRateList($productMRP, $rateListStartDate);
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["selProductCategory"]!="") $selProductCategoryId = $g["selProductCategory"];
	else $selProductCategoryId = $p["selProductCategory"];

	if ($g["selProductState"]!="") $selProductStateId = $g["selProductState"];
	else $selProductStateId = $p["selProductState"];

	if ($g["selProductGroup"]!="") $selProductGroupId = $g["selProductGroup"];
	else $selProductGroupId = $p["selProductGroup"];
		
	if ($p["cmdSearch"]) $offset = 0;

	#List all Records
	$productMRPMasterRecords = $productMRPMasterObj->fetchAllPagingRecords($offset, $limit, $selRateList, $selProductCategoryId, $selProductStateId, $selProductGroupId);
	$productMRPMasterRecordSize    = sizeof($productMRPMasterRecords);

	## -------------- Pagination Settings II -------------------
	$fetchProductMRPMasterRecords = $productMRPMasterObj->fetchAllRecords($selRateList, $selProductCategoryId, $selProductStateId, $selProductGroupId);	// fetch All Records
	$numrows	=  sizeof($fetchProductMRPMasterRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Ingredient
	if ($addMode || $editMode) {
		# Get All Product Records
		/*$productRecords = $manageProductObj->fetchAllRecords();*/
		$productRecords = $manageProductObj->getAllProductRecs();

		# List all State
		$stateRecs = $stateMasterObj->fetchAllStateRecords();

		# List all Distributor
		$distributorRecs = $distributorMasterObj->fetchAllDistributorRecords();
	
	}
	
	#List all Product Category Records
	$productCategoryRecords = $productCategoryObj->fetchAllRecords();
	#List all Product State Records
	$productStateRecords = $productStateObj->fetchAllRecords();

	if ($editMode) $heading	= $label_editProductMRPMaster;
	else	       $heading	= $label_addProductMRPMaster;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/ProductMRPMaster.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	/*$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else*/ 
	require("template/btopLeftNav.php");
?>
	<form name="frmProductMRPMaster" action="ProductMRPMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
	<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product" onclick="parent.openTab('ManageProduct.php');">Manage Product</a></td>	
	</tr>
		<? if($err!="" ){?>
		<tr>
			<td height="20" align="center" class="err1" ><?=$err;?></td>			
		</tr>
		<?}?>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
				<td height="10" align="center">
		<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td>
		<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                <option value="">-- Select --</option>
                <?
		foreach ($productMRPRateListRecords as $prl) {
			$mRateListId	= $prl[0];
			$rateListName	= stripSlash($prl[1]);
			$startDate	= dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected = "";
			if($selRateList==$mRateListId) $selected = "Selected";
		?>
                <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$productMRP?>'"></td>
		<? }?>
                  </tr>
                </table></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">			
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Product MRP Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
	<tr><TD colspan="3" align="center">
	<table width="60%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>	
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>-->
	<tr>
		<td width="1" ></td>
		<td colspan="2" >
		<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
		<tr>
			<td colspan="4" height="10" ></td>
		</tr>
		<tr>
		<? if($editMode){?>
		<td colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMRPMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductMRPMaster(document.frmProductMRPMaster);">	
		</td>
		<?} else{?>
		<td  colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMRPMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductMRPMaster(document.frmProductMRPMaster);">&nbsp;&nbsp;		
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidProductMRPId" value="<?=$editProductMRPId;?>">
		<tr>
			<td nowrap height="5"></td>			
		  </tr>
		<tr><TD class="listing-item" style='line-height:normal; font-size:10px; color:red;' id="productExistMsg" nowrap="true" align="center" colspan="2"></TD></tr>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
					<table width="100%">						
						<tr>
                                                 <td class="fieldName">Product</td>
                                                 <td class="listing-item">
						 <select name="selProduct" id="selProduct" onchange="xajax_chkProductMRPExist(document.getElementById('selProduct').value, document.getElementById('productMRPRateList').value, '<?=$editProductMRPId;?>');">
						 <option value="">--Select--</option>
						 <?
						foreach ($productRecords as $pr) {
							$productId	=	$pr[0];
							$productName	=	$pr[2];
							$selected = "";
							if ($selProduct==$productId && $productId!="") $selected = "Selected";
						 ?>
						 <option value="<?=$productId?>" <?=$selected?>><?=$productName?></option>
						 <? }?>
						 </select>
						 </td>
                                                </tr>
			<!--<tr>
				<td class="fieldName" nowrap>*MRP</td>
				<td>
					<input type="text" size="6" name="mrp" id="mrp" value="<?=$mrp?>" style="text-align:right;" autoComplete="off">
					<input type="hidden" size="6" name="hidMRP" id="hidMRP" value="<?=$mrp?>" style="text-align:right;" autoComplete="off" readonly="true">
				</td>
			</tr>-->
			<tr><TD colspan="2" >
			<table width="100%">
			<TR>
			<TD style="padding-left:5px; padding-right:5px;" align="center">
				<?php			
					$entryHead = "MRP";
					require("template/rbTop.php");
				?>
				<table width="100%" cellpadding="0" cellspacing="0" align="center">
				<tr><TD height="5"></TD></tr>
				<TR>
				<TD style="padding-left:25px; padding-right:5px;" nowrap>
				<table cellspacing="1" cellpadding="2" id="tblMRPExpt" class="newspaperType">						
						<TR align="center">
							<th style="padding-left:5px; padding-right:5px;">State</th>
							<th nowrap style="padding-left:5px; padding-right:5px;">Distributor</th>
							<th nowrap style="padding-left:5px; padding-right:5px;">*MRP</th>
							<th>&nbsp;</th>
						</TR>
				<?php
				if ($productExptRecSize>0) {
					$k = 0;
					foreach ($productExptRecs as $per) {
						$productExptEntryId = $per[0];
						$peStateId		= $per[1];
						$peDistributorId	= $per[2];
						$peMRP			= $per[3];
						if ($peStateId!=0 && $peDistributorId!=0) {							
							$distRecs = $productMRPMasterObj->getDistributorRecs($peStateId);
							$selDistributorRecs = $distRecs;			
						} else $selDistributorRecs = $distributorRecs;
				?>
				<tr id="row_<?=$k?>">
					<td class="listing-item" align="left" nowrap="nowrap">
						<select name="selState_<?=$k?>" id="selState_<?=$k?>" onchange="xajax_getDistributorList('<?=$peStateId?>','<?=$k?>', '');">
						<option value="0">--Select All--</option>
						<?php
							if (sizeof($stateRecs)!="" && $k!=0) {
								foreach($stateRecs as $sr) {
									$stateId 	= $sr[0];
									$stateName	= stripSlash($sr[2]);
									$selected = ($peStateId==$stateId)?"selected":"";
						?>
						<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
						<?php
								}
							}
						?>
						</select>
					</td>
					<td class="listing-item" align="center" nowrap="nowrap">
						<select name="selDistributor_<?=$k?>" id="selDistributor_<?=$k?>">
						<option value="0">--Select All--</option>
						<?php
							if (sizeof($selDistributorRecs)>0 && $k!=0) {
								foreach ($selDistributorRecs as $dr) {
									$distributorId	 = $dr[0];
									$distributorName = stripSlash($dr[2]);
									$selected = ($peDistributorId==$distributorId)?"selected":"";
						?>
						<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
						<?php
								}
							}
						?>
						</select>
					</td>
					<td class="listing-item" align="center" nowrap="nowrap">
						<input name="mrp_<?=$k?>" id="mrp_<?=$k?>" size="3" value="<?=$peMRP?>" style="text-align: right;" autocomplete="off" type="text">
					</td>
					<td class="listing-item" align="center" nowrap="nowrap">
						<input name="status_<?=$k?>" id="status_<?=$k?>" value="" type="hidden" readonly>
						<input name="IsFromDB_<?=$k?>" id="IsFromDB_<?=$k?>" value="N" type="hidden" readonly>
						<input type="hidden" name="productExptEntryId_<?=$k?>" id="productExptEntryId_<?=$k?>" value="<?=$productExptEntryId?>" readonly>
						<?php
							if ($k!=0) {
						?>
						<a href='###' onClick="setItemStatus('<?=$k?>');" ><img title="Click here to remove this item" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>
						<?php
							}
						?>
					</td>
				</tr>
				<?php
						$k++;
					} // Loop Ends here
				}
				?>
						</thead>
				</table>
				<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$productExptRecSize?>" readonly>
				</td>
				</tr>
				<tr>
					<TD nowrap style="padding-left:25px; padding-right:5px;">
						<table>
							<TR>
								<TD>
									<input name="cmdException" type="button" class="button" id="cmdException" style="width:100px;" value="Add Exceptions" onclick="addNewExptRow();">
								</TD>
							</TR>
						</table>
					</TD>
				</tr>
				<tr><TD height="5"></TD></tr>
				</table>
				<?php
					require("template/rbBottom.php");
				?>
			</TD></TR></table>
			</TD></tr>
			<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="productMRPRateList" id="productMRPRateList">
			<?
			if (sizeof($productMRPRateListRecords)>0) {
				foreach ($productMRPRateListRecords as $prl) {
					$mRateListId	= $prl[0];
					$rateListName		= stripSlash($prl[1]);
					$startDate		= dateFormat($prl[2]);
					$displayRateList = $rateListName."&nbsp;(".$startDate.")";
					if ($addMode) $rateListId = $selRateList;
					else $rateListId = $productMRPRateListId;
					$selected = "";
					if ($rateListId==$mRateListId) $selected = "Selected";
			?>
                    	  <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      	<? 
				}
			?>
			<?
			} else {
			?>
			 <option value="">-- Select --</option>
			<?
			}
			?>
                                            </select></td>
						</tr>					
                                              </table>
					</td>					
					</tr>
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
				<td colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMRPMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductMRPMaster(document.frmProductMRPMaster);">	
				</td>
				<?} else{?>
				<td  colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductMRPMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductMRPMaster(document.frmProductMRPMaster);">&nbsp;&nbsp;			</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
				<!--input type="hidden" name="stockType" value="<?=$stockType?>"-->
				</tr>
		<tr>
			<td colspan="2"  height="10" >
				<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyCheked?>">
			</td>
		</tr>
		</table></td>
		</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
	<!-- Form fields end   --></td>
		</tr>
		<?
			}			
			# Listing Category Starts
		?>
	</table>
	</TD></tr>
	<?php
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<?php }?>
							<tr>
								<td colspan="3" align="center">
						<table width="50%" height="50%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table  cellspacing="4" cellpadding="0">
						<tr>
							<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Category</td>
							<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductCategory" id="selProductCategory" style="width:90px;">
		<option value=''>--Select All--</option>";
		<?php
		if (sizeof($productCategoryRecords)>0) {	
			 foreach ($productCategoryRecords as $cr) {
				$categoryId	= $cr[0];
				$categoryName	= stripSlash($cr[1]);
				$selected = "";
				if ($selProductCategoryId==$categoryId) $selected = "Selected";
		?>	
		<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>	
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">State</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductState" id="selProductState" onChange="xajax_getProductGroupExist(document.getElementById('selProductState').value, '');" style="width:90px;">
		<option value=''>--Select All--</option>";
		<?php
		if (sizeof($productStateRecords)>0) {	
			foreach ($productStateRecords as $cr) {
				$prodStateId	= $cr[0];
				$prodStateName	= stripSlash($cr[1]);
				$selected = "";
				if ($selProductStateId==$prodStateId) $selected = "Selected";
		?>	
		<option value="<?=$prodStateId?>" <?=$selected?>><?=$prodStateName?></option>
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Group</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductGroup" id="selProductGroup" style="width:90px;">
		<option value=''>--Select All--</option>
		</select>
	</td>
	<td style="padding-right:10px;">
		<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value=" Search ">
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
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Product MRP Master</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap" style="padding-left:20px;">
		
	</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>						
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMRPMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductMRPMaster.php?selRateList=<?=$selRateList?>&selProductCategory=<?=$selProductCategoryId?>&selProductState=<?=$selProductStateId?>&selProductGroup=<?=$selProductGroupId?>',700,600);"><?}?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="") {
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
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if ( sizeof($productMRPMasterRecords) > 0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductMRPMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductMRPMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductMRPMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
		<th width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</th>		
		<th style="padding-left:10px; padding-right:10px;">Product</th>
		<th style="padding-left:10px; padding-right:10px;">Net Wt</th>
		<th style="padding-left:10px; padding-right:10px;">MRP</th>
		<th style="padding-left:10px; padding-right:10px;">No.of <br>Exception<br>MRP</th>
		<? if($edit==true){?>
		<th>&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	$selProductId = "";
	$selRateListId = "";
	foreach ($productMRPMasterRecords as $pmr) {
		$i++;
		$productMRPId	= $pmr[0];		
		$productName	= $pmr[4];
		$netWt		= $pmr[5];
		//$pMRP		= $pmr[2];
		$pMRP		= $productMRPMasterObj->getDefaultMRP($productMRPId);
		$selProductId	= $pmr[1];
		$selRateListId  = $pmr[3];
		$prevProductMRP = $productMRPMasterObj->getPrevProductMRP($selProductId, $lastRateListId);
		//echo "<br>$prevProductMRP=$pMRP";
		$colBgColor = "#E8EDFF";
		$msgRateChanged = "";
		if ($pMRP!=$prevProductMRP && $prevProductMRP!="") {
			$colBgColor = "#fde89f";
			if ($pMRP>$prevProductMRP) $msgRateChanged = "onMouseover=\"ShowTip('MRP Increased.');\" onMouseout=\"UnTip();\"";
			else if ($pMRP<$prevProductMRP) $msgRateChanged = "onMouseover=\"ShowTip('MRP Decreased.');\" onMouseout=\"UnTip();\"";
		}

		# Exception MRP	
		$disMRPExpt = "";
		list($showMRPExpt, $noOfExptMRP)  = $productMRPMasterObj->displayMRPException($productMRPId);
		if ($noOfExptMRP>0) $disMRPExpt = "<a href='###' onMouseover=\"ShowTip('$showMRPExpt');\" onMouseout=\"UnTip();\" class='link5'>$noOfExptMRP</a> ";
		
	?>
	<tr>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productMRPId;?>" class="chkBox">
			<input type="hidden" name="selProductId_<?=$i;?>" id="selProductId_<?=$i;?>" value="<?=$selProductId?>">
			<input type="hidden" name="selRateListId_<?=$i;?>" id="selRateListId_<?=$i;?>" value="<?=$selRateListId?>">
		</td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$netWt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right" bgcolor="<?=$colBgColor?>" <?=$msgRateChanged?>><?=$pMRP;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$disMRPExpt;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productMRPId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ProductMRPMaster.php';">
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
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductMRPMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductMRPMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductMRPMaster.php?pageNo=$page&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId\"  class=\"link1\">>></a> ";
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
	</tbody>
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productMRPMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductMRPMaster.php?selRateList=<?=$selRateList?>&selProductCategory=<?=$selProductCategoryId?>&selProductState=<?=$selProductStateId?>&selProductGroup=<?=$selProductGroupId?>',700,600);"><?}?></td>
											</tr>
										</table>			</td>
								</tr>
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
				<!-- Form fields end   -->			</td>
		</tr>
		
		<tr>
			<td height="10"></td>
		</tr>
		<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product" onclick="parent.openTab('ManageProduct.php');">Manage Product</a></td>	
	</tr>
<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
<input type="hidden" name="productExist" id="productExist" value="" readonly="true">

	</table> 

	<script language="JavaScript" type="text/javascript">
		xajax_getProductGroupExist('<?=$selProductStateId?>', '<?=$selProductGroupId?>');
	</script>
		
	<script language="JavaScript" type="text/javascript">
		function addNewExptRow() 
		{
			addNewExceptionRow('tblMRPExpt');
		}	
		
	<?php
		if ($productExptRecSize>0) {
	?>
	fieldId = '<?=$productExptRecSize?>';
	<?php
		}
	?>
	<?php
		if (($addMode || $editMode) && !$productExptRecSize) {
	?>	
		window.onLoad = addNewExptRow();	
	<?php
		}
	?>
	</script>
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
	//ensureInFrameset(document.frmProductMRPMaster);
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
