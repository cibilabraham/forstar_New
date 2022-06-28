<?php
	require("include/include.php");
	require_once("lib/ProductStatus_ajax.php");
	
	$err		= "";
	$errDel		= "";
	$editMode	= true;
	$addMode	= false;
	$searchMode 	= false; 
	
	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
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
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;

	
	if ($g["selState"]!="") 	$selStateId 	= $g["selState"];
	else if ($p["selState"]!="") 	$selStateId 	= $p["selState"];

	if ($g["selDistributor"]!="")		$selDistributorId 	= $g["selDistributor"];
	else if ($p["selDistributor"]!="")	$selDistributorId  = $p["selDistributor"];

	if ($g["selProductCategory"]!="") $selProductCategoryId = $g["selProductCategory"];
	else $selProductCategoryId = $p["selProductCategory"];

	if ($g["selProductState"]!="") $selProductStateId = $g["selProductState"];
	else $selProductStateId = $p["selProductState"];

	if ($g["selProductGroup"]!="") $selProductGroupId = $g["selProductGroup"];
	else $selProductGroupId = $p["selProductGroup"];

	if ($g["distributorMgnRateList"]!="") $selRateList = $g["distributorMgnRateList"];
	else $selRateList = $p["distributorMgnRateList"];
	
	if ($selDistributorId!="" && $selRateList=="") $selRateList = $distMarginRateListObj->latestRateList($selDistributorId);

	if ($p["cmdMultipleAssign"]!="") {
		$hidRowCount	= $p["hidTableRowCount"];
		//$selectionType	= $p["selectionType"];
		$selectionType	= "G"; // Group Updation
		$count=0;
		$pArr = array();	
		for ($j=1; $j<=$hidRowCount; $j++) {
		 	$selProductId = $p["productId_".$j];
			if ($selProductId) {
				$pArr[$count] = $selProductId;				
				$count++;
			} // If Checked
		}
		$selProduct = implode(",",$pArr);	
		$redirectUrl ="selDistributor=$selDistributorId&selProductIds=$selProduct&selState=$selStateId&addMode=1&urlFrom=PM&selectionType=$selectionType&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId&distributorMgnRateList=$selRateList";
		/*
		echo "En=".$val = $userObj->getEncodedString($selProduct);
		echo "<br>DeEn=".$userObj->getDecodedString($val);
		echo "<pre";
		print_r(extractUrl($redirectUrl));
		echo "</pre>";
		*/
		//DistMarginStructure.php?selDistributor=$selDistributorId&selProduct=$mproductId&selState=$selStateId&addMode=1&urlFrom=PM
		header("location:DistMarginStructure.php?$redirectUrl");		
	}

	#List all Product Category Records
	//$productCategoryRecords = $productCategoryObj->fetchAllRecords();
	$productCategoryRecords = $productCategoryObj->fetchAllRecordsActiveCategory();
	#List all Product State Records
	//$productStateRecords = $productStateObj->fetchAllRecords();
	$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();

	# List all Products
	$productRecords = $manageProductObj->fetchAllRecords($selProductCategoryId, $selProductStateId, $selProductGroupId);	

	# List all State
	//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
	$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
	# Get Dist Recs
	$getDistRecs = $productStatusObj->filterDistRecs($selStateId, $selDistributorId);

	if ($p["cmdSearch"]!="" || ($selStateId!="" && $selDistributorId!="")) {
		$searchMode = true; 
		# Distributor RL Recs
		$distMgnRLRecs = $productStatusObj->filterDistMgnRLRecs($selDistributorId);
	}
	

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editProductStatus;
	else	       $heading	=	$label_addProductStatus;

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ProductStatus.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
			
	<form name="frmProductStatus" action="ProductStatus.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1"><?=$err;?></td>
	</tr>
	<?}?>
	<?php
	if ($selDistributorId=="" && $selRateList=="" && $searchMode) {
	?>
	<tr>
		<TD class="listing-item" height="20" align="center" style="color: Maroon;">
			<strong>Latest distributor margin rate list wise product assignment.</strong>
		</TD>
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
					$bxHeader = "Product Management";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
			if ( $editMode || $addMode) {
		?>
		<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
		<tr><TD height="10"></TD></tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
					<tr>
						<td colspan="2" height="10" ></td>
					</tr>			
<!-- Message display row -->
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;" align="center">		
		<table>
			<TR>
			<TD>
			<!--<fieldset>-->
			<?php
				$entryHead = "";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>
			<table>
				<TR>
					<TD class="fieldName">*State</TD>
					<td nowrap="true">
						<select name="selState" id="selState" onchange="xajax_getDistributors(this.value,'');" style="width:145px;">
						<option value="">-- Select --</option>
						<?	
							while ($sr=$stateResultSetObj->getRow()) {
								$stateId 	= $sr[0];
								$stateName	= stripSlash($sr[2]);	
								$selected 	= ($selStateId==$stateId)?"selected":"";	
						?>
						<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
						<? 
							}
						?>
						</select>
					</td></TR>
				<TR><TD class="fieldName">Distributor</TD>
					<td nowrap="true">
						<select name="selDistributor" id="selDistributor" style="width:145px;" onchange="xajax_distributorWiseMgnRL(this.value);">
						<option value="">-- Select All --</option>
						</select>
					</td></TR>
				<TR>
					<TD class="fieldName">Rate List</TD>
					<td nowrap="true">
						<select name="distributorMgnRateList" id="distributorMgnRateList" style="width:145px;">
							<?php if (!sizeof($distMgnRLRecs)) {?><option value="">--Select--</option><? }?>
							<?php
							foreach ($distMgnRLRecs as $rlId=>$rlName) {
								$selected = ($selRateList==$rlId)?"selected":"";
							?>
							<option value="<?=$rlId?>" <?=$selected?>><?=$rlName?></option>
							<?php
								}
							?>
						</select>
					</td></TR>
				<tr><TD height="5"></TD></tr>
				<tr><TD colspan="2" align="center"><input type="submit" name="cmdSearch" id="cmdSearch" value=" Search " class="button" onclick="return validateSearchProductStatus(document.frmProductStatus);" /></TD></tr>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->	
			</TD>
			<td>&nbsp;</td>
			<TD>
			<!--<fieldset><legend class="listing-item">Product</legend>-->
			<?php
				$entryHead = "Product";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>
				<table>
				<TR>
					<td class="fieldName" nowrap="true">Category</td>
					<td nowrap="true">
						<select name="selProductCategory" id="selProductCategory" style="width:145px;">
						<option value=''>-- Select All --</option>";
						<?php
						if (sizeof($productCategoryRecords)>0) {	
							foreach ($productCategoryRecords as $cr) {
								$categoryId	= $cr[0];
								$categoryName	= stripslashes($cr[1]);
								$selected = ($selProductCategoryId==$categoryId)?"Selected":"";
						?>	
						<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>	
						<?php
							}
						}
						?>
						</select>
					</td>
				</TR>
				<TR><td class="fieldName" nowrap="true">State</td>
	<td nowrap="true">
		<select name="selProductState" id="selProductState" onChange="xajax_getProductGroupExist(document.getElementById('selProductState').value, '');" style="width:145px;">
		<option value=''>-- Select All --</option>";
		<?php
		if (sizeof($productStateRecords)>0) {	
			foreach ($productStateRecords as $cr) {
				$prodStateId	= $cr[0];
				$prodStateName	= stripslashes($cr[1]);
				$selected = ($selProductStateId==$prodStateId)?"Selected":"";
		?>	
		<option value="<?=$prodStateId?>" <?=$selected?>><?=$prodStateName?></option>
		<?php
			}
		}
		?>
		</select>
	</td></TR>
				<TR><td class="fieldName" nowrap="true">Group</td>
	<td nowrap="true">
		<select name="selProductGroup" id="selProductGroup" style="width:145px;">
		<option value=''>-- Select All --</option>
		</select>
	</td></TR>
				</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
			</TD>			
			</TR>
		</table>		
		</td>
	</tr>	
	<tr><TD height="10"></TD></tr>
<!--  Dynamic Row Starts Here-->
	<?
	if ($searchMode && $selStateId!="") {
	?>
		<?php
			if ($selDistributorId) {
		?>
		  <tr> 
			<td align="center" class="listing-item" valign="top">
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD align="right"><img src="images/y.png" width="20" height="20"></TD>
						<td class="listing-item">- Active.</td>
						<TD align="right"><img src="images/x.png" width="20" height="20"></TD>
						<td class="listing-item">- Inactive.</td>
					</TR>
				</table>
			</td>
		</tr>		
		<tr><TD height="10"></TD></tr>
		<?php
			}
		?>
		<tr id="catRow1">
			<td colspan="2" style="padding-left:10px;padding-right:10px;">			
				<table  cellspacing="1" cellpadding="3" id="newspaper-b1">
				<?php
					if (sizeof($productRecords)>0) {
				?>
				<tr align="center">
					<?php
					if ($selDistributorId) {
					?>
					<th width="20">
						<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'productId_');" class="chkBox">
					</th>
					<?php }?>
					<th class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">Product</th>
					<?
					foreach ($getDistRecs as $gdr) {						
						$distName	= $gdr[2];
						$cityName	= $gdr[3];
						$exportEnabled	= $gdr[4];
					?>
					<th style="padding-left:5px;padding-right:5px;font-size:12px;" valign="top">
						<table cellpadding="0" cellspacing="0" width="100%" id="newspaper-b1-no-style">
							<TR>
								<th class="listing-head" align="center" style="border-right:0px solid #FFFFFF; padding:1px;"><?=$distName?><? if ($exportEnabled=='Y') {?><span class="listing-head" style="line-height:normal;font-size:8px;font-weight:normal;">(Export)</span> <? }?></th>
							</TR>
							<TR>
								<th class="fieldName" style="line-height:normal;font-size:11px; border-right:0px solid #FFFFFF; padding:1px; text-align:center;;" align="center" nowrap>[<?=$cityName?>]</th>
							</TR>
						</table>						
					</th>
					<?
					}
					?>
				</tr>	
				<?
				$j = 0;	
				foreach ($productRecords as $pr) {
					$j++;
					$mproductId	= $pr[0];					
					$mproductName	= $pr[2];	
				?>			
				<tr>
					<?php
					if ($selDistributorId) {	
					?>
					<td width="20">
						<input type="checkbox" name="productId_<?=$j;?>" id="productId_<?=$j;?>" value="<?=$mproductId;?>" class="chkBox">
					</td>
					<?php }?>
					<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true">
						<?=$mproductName?>
					</td>
					<?
					$xjxRedirectUrl = "";
					$distributorId = "";
					$k = 0;	
					foreach ($getDistRecs as $gdr) {
						$k++;
						$cityId	 	= $gdr[0];		
						$distributorId 	= $gdr[1];	

						$exportEnabled		= $gdr[4];
						$distStateEntryId 	= $gdr[5];
						$distMStateEntryId = "";
						if ($exportEnabled=='Y') $distMStateEntryId = $distStateEntryId;

						if ($selRateList!="") $selRateListId = $selRateList; 
						else $selRateListId = $distMarginRateListObj->latestRateList($distributorId);

						# Check Product Assigned	
						$chkProductAssign = $productStatusObj->chkProductAssign($mproductId, $distributorId, $selStateId, $selRateListId, $cityId, $distMStateEntryId);
						# Testing
						//$productStatusObj->getDistMarginStateRecs($mproductId, $distributorId, $selStateId, $selRateListId);
						$displayAssign = "";
						if ($chkProductAssign) {
							list($distMarginId, $distMarginStateEntryId) = $productStatusObj->getDistMgnRec($mproductId, $distributorId, $selStateId, $selRateListId, $cityId);
							$displayAssign = "<span style='color:#008000'>Assigned</span>";
							//echo "<br>$distMarginId, $distMarginStateEntryId<br>";
						} else {
							$displayAssign = "<span style='color:#FF0000'>Not Assigned</span>";
						}
						# redirect URL
						$xjxRedirectUrl = "DistMarginStructure.php?selDistributor=$selDistributorId&selProduct=$mproductId&selState=$selStateId&addMode=1&urlFrom=PM&selProductCategory=$selProductCategoryId&selProductState=$selProductStateId&selProductGroup=$selProductGroupId&selPMCityId=$cityId&distributorMgnRateList=$selRateListId&distMStateEntryId=$distMStateEntryId";
						if ($selDistributorId && $chkProductAssign && $isAdmin) {	
							$displayAssign = "<a href='###' class='link6' title='Click here to Remove Margin' onclick=\"return validateAssignRemove('$distMarginId','$distMarginStateEntryId','$j$k','$selDistributorId','$mproductId','$selStateId', '$selRateListId', '$xjxRedirectUrl');\">$displayAssign</a>";
						}
						if ($selDistributorId && !$chkProductAssign) {
							$displayAssign = "<a href='$xjxRedirectUrl' class='link1' title='Click here to assign a product'>$displayAssign</a>";
						}
						
						//echo "$j$k";
					?>
					<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true" align="center" id="assignRow_<?=$j1111111111112?>">
						<table cellpadding="0" cellspacing="0" id="newspaper-b1-no-style">
							<TR>
								<TD class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true" id="assignRow_<?=$j.$k?>"><?=$displayAssign?></TD>		
							<?
					# If Single Distibutor
					if ($selDistributorId) {
						# Sel Rate List Id
						//$selRateListId 	= $distMarginRateListObj->latestRateList($distributorId);
						# Chk Product Assign
						//$chkProductAssign = $productStatusObj->chkProductAssign($mproductId, $selDistributorId, $selStateId, $selRateListId);
						#  Chk Product inactive
						$productInactive = $productStatusObj->chkProductInactive($selStateId,$selDistributorId, $mproductId, $cityId);
							
						$displayStatusSymbol = "";
						if (!$productInactive && $chkProductAssign) {
							$displayStatusSymbol = "<img src='images/y.png' onclick=\"xajax_updateProductMgmt('$selStateId','$selDistributorId','$mproductId','$userId','$j$k', '$cityId');\" />";
						} else if ($chkProductAssign || $productInactive) {
							$displayStatusSymbol = "<img src='images/x.png' onclick=\"xajax_removeProductMgmt('$selStateId','$selDistributorId','$mproductId','$userId','$j$k', '$cityId');\" />";
						}
						
					?>					
					<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true" align="center" id="statusRow_<?=$j.$k?>">
						<?=$displayStatusSymbol?>					
					</td>
					<!-- For Checking Product Already Assigned -->
					<td style="display:none;"><input type="text" name="productAssign_<?=$j.$k?>" id="productAssign_<?=$j.$k?>" value="<?=$chkProductAssign?>" /></td>
					</tr>
					<?php
						}
					?>
					
						</table>							
					</td>
					<?php
						} // Dist Loop Ends Here
					?>					
					<?php
					/*
					# If Single Distibutor
					if ($selDistributorId) {
						# Sel Rate List Id
						$selRateListId 	= $distMarginRateListObj->latestRateList($distributorId);
						# Chk Product Assign
						$chkProductAssign = $productStatusObj->chkProductAssign($mproductId, $selDistributorId, $selStateId, $selRateListId);
						#  Chk Product inactive
						$productInactive = $productStatusObj->chkProductInactive($selStateId,$selDistributorId, $mproductId);
							
						$displayStatusSymbol = "";
						if (!$productInactive && $chkProductAssign) {
							$displayStatusSymbol = "<img src='images/y.gif' onclick=\"xajax_updateProductMgmt('$selStateId','$selDistributorId','$mproductId','$userId','$j');\" />";
						} else if ($chkProductAssign || $productInactive) {
							$displayStatusSymbol = "<img src='images/x.gif' onclick=\"xajax_removeProductMgmt('$selStateId','$selDistributorId','$mproductId','$userId','$j');\" />";
						}
						*/
					?>						
					<!--<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true" align="center" id="statusRow_<?=$j?>">
						<?//=$displayStatusSymbol?>					
					</td>					
					<td style="display:none;"><input type="text" name="productAssign_<?=$j?>" id="productAssign_<?=$j?>" value="<?//=$chkProductAssign?>" /></td>-->
					<?php
						//}
					?>
				</tr>	
				<?
					}  // Loop Ends Here
				?>
				<input type="hidden" name="distStateRowCount" id="distStateRowCount" value="<?=sizeof($getDistRecs)?>" />
				<?
				} else if ($searchMode) {
					
				?>
				<tr>
					<td class="err1" height="10" align="center" width="550px"><?=$msgNoRecords;?></td>
				</tr>
				<?php
					}
				?>
				</table>
			</td>
		</tr>
	<?
		} // Search Mode Chk
	?>
		<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$j?>">
<!--  Dynamic Row Ends Here-->
<tr id="catRow2"><TD height="5"></TD></tr>
		<?php
			if ($selDistributorId) {
		?>		  
		<tr><TD height="10"></TD></tr>
		<tr>
			<TD align="center">
			<!--<fieldset>-->
			<?php
				$entryHead = "";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>
				<table cellpadding="0" cellspacing="0">
					<tr><TD height="5"></TD></tr>
					<TR>
						<TD>
		<input type="submit" name="cmdMultipleAssign" id="cmdMultipleAssign" value=" Assign Multiple Product " class="button" onclick="return validateMultipleProduct('productId_',<?=sizeof($productRecords);?>);" style="width:180px;" />	
						</TD>
					</TR>
					<tr><TD height="5"></TD></tr>
				</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
			</TD>
		</tr>
		<tr><TD height="10"></TD></tr>
		<?php
			}
		?>
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
			</td>
		</tr>
		</table>
			<?php
				include "template/boxBR.php"
			?>		
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
	<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">		
	<input type="hidden" name="hidStateFilterId" value="<?=$stateFilterId?>">	
	<input type="hidden" name="hidStateVatRateListFilterId" value="<?=$stateVatRateListFilterId?>">					
	<tr>
		<td height="10"></td>
	</tr>
	</table>
	<?php
	if ($selStateId!="") {	
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_getDistributors('<?=$selStateId?>','<?=$selDistributorId?>');
	</script>
	<?
		}
	?>
	<?php
		if ($selProductStateId!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_getProductGroupExist('<?=$selProductStateId?>', '<?=$selProductGroupId?>');
	</script>
	<?php
		}
	?>
	</form> 
	</td>
	</tr>
	</table>	
	

<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>