<?php
	require("include/include.php");
	require_once("lib/GstMaster_ajax.php");

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
		
	$selection 	=	"?pageNo=".$p["pageNo"]."&stateFilter=".$p["stateFilter"]."&gstRateListFilter=".$p["gstRateListFilter"];	
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

	if ($p["state"]!="") $selStateId = $p["state"];	
	if ($p["productCategory"]!="") $selCategoryId = $p["productCategory"];	

	

	#Add a Record
	if ($p["cmdAdd"]!="") {
		

		$tableRowCount		= $p["hidTableRowCount"];
		$gstRateListId	= $p["gstRateList"];
		


		$edRateListId =$gstMasterObj->latestRateList();


		# Creating a New Rate List
		if ($gstRateListId=="" && $edRateListId=="") {			
			$rateListName = "gst"."-".date("dMy");
			$startDate    = date("Y-m-d");
			$gstRateListRecIns = $gstMasterObj->addGstRateList($rateListName, $startDate, '', $userId, '');
			if ($gstRateListRecIns) {
				$gstRateListId =$gstMasterObj->latestRateList();
			}
		} else if ($gstRateListId=="" && $edRateListId!="") {
			$gstRateListId = $edRateListId;
		}						
		

		if ($gstRateListId!=0) {
			

			
			if ($tableRowCount>0) {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selPCategory 	= $p["selProductCategory_".$i];
						$selPState 	= $p["selProductState_".$i];
						$selPGroup 	= $p["selProductGroup_".$i];	
						$gstPercent	= $p["gstPercent_".$i];
						$chapterSubheading = $p["chapterSubheading_".$i];
						$goodsType 	= $p["goodsType_".$i];

						# IF SELECT ALL STATE
						if ($selPCategory!="" && $selPState==0) {							
							# Get Product State Records
							//$prodStateRecords = $productStateObj->fetchAllRecords();
							
	
							$prodStateRecords = $productStateObj->fetchAllRecordsActiveProduct();
							foreach ($prodStateRecords as $cr) {
								$prodStateId	= $cr[0];
								# Chk Prod Group Exist
								$prodGroupExist = $gstMasterObj->checkProductGroupExist($prodStateId);

								if ($prodGroupExist) {
									# Prod Group Recs
									//$prodGroupRecs = $productGroupObj->fetchAllRecords();
									$prodGroupRecs = $productGroupObj->fetchAllRecordsActiveGroup();
									$prodGroupId = 0;
									foreach ($prodGroupRecs as $pgr) {
										$prodGroupId	= $pgr[0];

										$recExist = $gstMasterObj->checkGstExist($selPCategory, $prodStateId, $prodGroupId, $gstRateListId);
										if (!$recExist) {
											$gst_perIns = $gstMasterObj->addGst($selPCategory, $prodStateId, $prodGroupId, $gstPercent,$gstRateListId, $chapterSubheading, $goodsType);
										}
									} # Group Loop Ends Here
								} else {  # If Not Product group
									$prodGroupId = 0;
									$recExist = $gstMasterObj->checkGstExist($selPCategory, $prodStateId, $prodGroupId, $gstRateListId);
									if (!$recExist) {
										$gst_perIns = $gstMasterObj->addGst($selPCategory, $prodStateId, $prodGroupId, $gstPercent, $gstRateListId, $chapterSubheading, $goodsType);
									}
										
								} # Product Goup Chk Ends Here			
							}  # Prod State Loop Ends Here
						}  # If State SELECT ALL
						
						# If group Select All
						if ($selPCategory!="" && $selPState!=0 &&  $selPGroup==0) {
							//echo "<br>Group SELECT ALL";
							# Chk Prod Group Exist
							$prodGroupExist = $gstMasterObj->checkProductGroupExist($selPState);
							if ($prodGroupExist) {					
								# Prod Group Recs
								//$prodGroupRecs = $productGroupObj->fetchAllRecords();
								$prodGroupRecs = $productGroupObj->fetchAllRecordsActiveGroup();
								$prodGroupId = 0;
								foreach ($prodGroupRecs as $pgr) {
									$prodGroupId	= $pgr[0];
									$recExist = $gstMasterObj->checkGstExist($selPCategory, $selPState, $prodGroupId, $gstRateListId);
									if (!$recExist) {
										$gst_perIns = $gstMasterObj->addGst($selPCategory, $selPState, $prodGroupId, $gstPercent, $gstRateListId, $chapterSubheading, $goodsType);
									}
									
								} # Group Loop Ends Here
							} else {
								$prodGroupId = 0;
								$recExist = $gstMasterObj->checkGstExist($selPCategory, $selPState, $prodGroupId, $gstRateListId);
								if (!$recExist) {
									$gst_perIns = $gstMasterObj->addGst($selPCategory, $selPState, $prodGroupId, $gstPercent, $gstRateListId, $chapterSubheading, $goodsType);
								}
								
							}
						}
						# Individual Inserting
						if ($selPCategory!="" && $selPState!=0 && $selPGroup!=0 && $selPGroup!='N') {	
							$recExist = $gstMasterObj->checkGstExist($selPCategory, $selPState, $selPGroup, $gstRateListId);
							
							if (!$recExist) {			
								$gst_perIns = $gstMasterObj->addGst($selPCategory, $selPState, $selPGroup, $gstPercent, $gstRateListId, $chapterSubheading, $goodsType);
							}
						}					
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here
			
			if ($gst_perIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succaddGstMaster);
				$sessObj->createSession("nextPage",$url_afteraddGstMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failaddGstMaster;
			}
			$stateVatRecIns = false;
		} else {
			$addMode = true;
			if ($entryExist) $err = $msg_failaddGstMaster."<br>".$msgFailAddStateVatExistRec;
			else $err = $msg_failaddGstMaster;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$gstId		= $p["hidGstId"];				
		$tableRowCount		= $p["hidTableRowCount"];
		$gstRateListId	= $p["gstRateList"];
		$newRateList		= $p["newRateList"];
		$newErr = "";
		
		if ($gstId!="" && $newRateList=="") {
			$gstPercentChanged = false;
			for ($i=0; $i<$tableRowCount; $i++) {
				$status 	  = $p["status_".$i];
				$edEntryId  = $p["stateVatEntryId_".$i];
				if ($status!='N') {
					$selPCategory 	= $p["selProductCategory_".$i];
					$selPState 	= $p["selProductState_".$i];
					$selPGroup 	= ($p["selProductGroup_".$i]!="")?$p["selProductGroup_".$i]:0;		
					$gstPercent	= $p["gstPercent_".$i];
					$hidGstPercent	= $p["hidgstPercent_".$i];
					$chapterSubheading = $p["chapterSubheading_".$i];
					$goodsType 	= $p["goodsType_".$i];
					
					if ($edEntryId!="" && $gstPercent!=0) {
	
						$updateGstRec = $gstMasterObj->updateGst($edEntryId, $gstPercent, $chapterSubheading, $goodsType);
					} /*else if ($gstId!="" && $selPCategory!="" && $selPState!="" && $edEntryId=="") {
						$gst_perIns = $gstMasterObj->addGst($gstId, $selPCategory, $selPState, $selPGroup, $gstPercent);
					}*/
					if ($gstPercent!=$hidGstPercent) $gstPercentChanged = true;
				} // Status Checking End

				if ($status=='N' && $edEntryId!="") {
					//$delVatEntryRec = $gstMasterObj->delVatEntryRec($edEntryId);
				}
			} // State For Loop ends here			
		}
		
		// New Rate List
		if ($newRateList>0) {
			
			$rateListName = "Gst"."-".date("dMy");
			$startDate    = date("Y-m-d");
			$gstRateListRecIns = $gstMasterObj->addGstRateList($rateListName, $startDate, $gstRateListId, $userId, $gstRateListId);
			if ($gstRateListRecIns) {
				//$gstRateListId =$gstMasterObj->latestRateList();
				for ($i=0; $i<$tableRowCount; $i++) {
					$status   = $p["status_".$i];
					$edEntryId  = $p["stateVatEntryId_".$i];
					if ($status!='N') {
						$gstPercent	= $p["gstPercent_".$i];
						$chapterSubheading = $p["chapterSubheading_".$i];
						$goodsType 	= $p["goodsType_".$i];
						if ($edEntryId!="" && $gstPercent!=0) {
							$updateGstRec = $gstMasterObj->updateGstByBaseId($edEntryId, $gstPercent, $chapterSubheading, $goodsType);
						}
					}
				}
			} else $newErr = " Rate list is already exist for the current day.";
		}
	
		if ($updateGstRec) {
			$sessObj->createSession("displayMsg",$msg_succgstUpdate);
			if ($newRateList>0) $sessObj->createSession("nextPage",$url_afterUpdateGstMaster);
			else $sessObj->createSession("nextPage",$url_afterUpdateGstMaster.$selection);
		} else {
			$editMode	=	true;			
			if ($entryExist) $err = $msg_failgstMasterUpdate."<br>".$msgFailAddStateVatExistRec;
			else $err = $msg_failgstMasterUpdate.$newErr;
		}
		$stateVatRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$stateVatRec	= $gstMasterObj->find($editId);
		$editStateVatId = $stateVatRec[0];		
		$stateWiseVatRateListId = $stateVatRec[2];
		
		# Get Entry Records
		$gstEntryRecords = $gstMasterObj->getGstEntryRecords($editStateVatId);			
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];

		for ($i=1; $i<=$rowCount; $i++) {
			$gstId	= $p["delId_".$i];
					
			if ($gstId!="") {
				
				$recInUse  = $gstMasterObj->checkGstRecInUse($gstId);
				# Delete
				if (!$recInUse) $gstRecDel = $gstMasterObj->deleteGstRec($gstId);
						
			} // State vat id ends here
		} // Loop ends here
		if ($gstRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelGstMaster);
			$sessObj->createSession("nextPage",$url_afterDelGstMaster.$selection);
		} else {
			if ($recInUse) $errDel = $msg_failDelgstMaster."<br>The selected record is alreay in use. ";
			else $errDel	=	$msg_failDelgstMaster;
		}
		$gstRecDel	=	false;
	}	


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$gstId=	$p["confirmId"];


			if ($gstId!="") {
				// Checking the selected fish is link with any other process
				$gstRecConfirm = $gstMasterObj->updateGstconfirm($gstId);
			}

		}
		if ($gstRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmgst);
			$sessObj->createSession("nextPage",$url_afterDelGstMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$gstId = $p["confirmId"];

			if ($gstId!="") {
				#Check any entries exist
				
					$gstRecConfirm = $gstMasterObj->updateGstReleaseconfirm($gstId);
				
			}
		}
		if ($gstRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmgst);
			$sessObj->createSession("nextPage",$url_afterDelExcisableGoodsMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------false
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["gstRateListFilter"]!="") $gstRateListFilterId = $g["gstRateListFilter"];
	else $gstRateListFilterId = $p["gstRateListFilter"];	

	# Resettting offset values
	if ($p["hidStateFilterId"]!=$p["stateFilter"]) {		
		$offset = 0;
		$pageNo = 1;	
		$gstRateListFilterId = "";	
	} else if ($p["hidEDRateListFilterId"]!=$p["gstRateListFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}
		
	
	# List all State Vat Master
	$gst_perResultSetObj = $gstMasterObj->fetchAllPagingRecords($offset, $limit, $gstRateListFilterId);
	$gst_perRecordSize = $gst_perResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$fetchGstMasterResultSetObj = $gstMasterObj->fetchAllRecords($gstRateListFilterId);
	$numrows	=  $fetchGstMasterResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode || $editMode) {	
		
		# List all Product Category
		//$productCategoryRecords	= $productCategoryObj->fetchAllRecords();
		$productCategoryRecords	= $productCategoryObj->fetchAllRecordsActiveCategory();
		# List all Product State Records
		//$productStateRecords = $productStateObj->fetchAllRecords();
		$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();
		
	}

	$gstRLFilterRecs = $gstMasterObj->filterGstRateListRecs();

	$curgstRateListId = $gstMasterObj->latestRateList();	

	if ($gstRateListFilterId!="") $gstRateListId =$gstRateListFilterId;
	else $gstRateListId = $curgstRateListId;	

	if ($gstRateListId) {		 
		$selTmr = $gstMasterObj->getGSTRateListRec($gstRateListId);
		$selStartDate = $selTmr[2];
	}


	if ($p["cmdEDActive"]!="") {
		$edActive = ($p["edActive"] == "") ? N : $p["edActive"];
		$updateGSTFlag = $gstMasterObj->updateGSTFlag($edActive);
	}

	$edFlag = $gstMasterObj->findGst($curgstRateListId);
	$excActive = ($edFlag=='Y')?"checked":"";

	if ($addMode || $editMode) $exGoodsMasterRecs = $excisableGoodsMasterObj->fetchAllRecordsActiveGoods();//$exGoodsMasterRecs = $excisableGoodsMasterObj->fetchAllRecords();

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editgstaster;
	else	       $heading	=	$label_addGstMaster;

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/GstMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmGstMaster" action="GstMaster.php" method="post">
<input type="hidden" name="gstRateList" id="gstRateList" value="<?=$gstRateListId?>" readonly="true" />
<input type="hidden" name="newRateList" id="newRateList" value="" readonly="true" />

	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >	
	<tr><TD height="5"></TD></tr>
		<?php
		//if (!$gstRateListFilterId) {
		?>
		<!--tr> 
			<td  align="center" class="listing-item" style="color:Maroon;">
				<strong>Current Gst Percent list.</strong>
			</td>
		</tr-->
		<?php
		//	}
		?>
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
				<?php	
					$bxHeader = "GST Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="45%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr><TD height="10"></TD></tr>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('GstMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateGstMaster(document.frmGstMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GstMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateGstMaster(document.frmGstMaster);">												</td>

												<?}?>
											</tr>
					<input type="hidden" name="hidGstId" value="<?=$editStateVatId;?>">
	<tr><TD height="10"></TD></tr>
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>	
	<tr id="catRow1">
		<td colspan="2" style="padding-left:5px;padding-right:5px;">
			<table  cellspacing="1" cellpadding="3" id="tblAddProdCategory" class="newspaperType">
			<tr align="center">
				<th class="listing-head" style="padding-left:5px;padding-right:5px;text-align:center; " nowrap="true">*Product Category</th>
				<th class="listing-head" style="padding-left:5px;padding-right:5px;text-align:center;" nowrap="true">*Product State</th>
				<th class="listing-head" style="padding-left:5px;padding-right:5px;text-align:center;" nowrap="true">Product Group</th>
				<th class="listing-head" nowrap style="padding-left:5px;padding-right:5px;text-align:center;">*Gst Percent</th>
				<th class="listing-head" nowrap style="padding-left:5px;padding-right:5px;text-align:center;">Chapter/subheading</th>
				<th class="listing-head" nowrap style="padding-left:5px;padding-right:5px;text-align:center;">Name of goods</th>
				<th>&nbsp;</th>			
			</tr>		
	<?php
	if (sizeof($gstEntryRecords)>0) {
		$j=0;
		foreach ($gstEntryRecords as $ver) {			
			$edEntryId 	= $ver[0];
			$selProdCategory 	= $ver[1]; 
			$selProdState		= $ver[2]; 	
			$selProdGroup		= $ver[3]; 	
			$gst_per		= $ver[4];
			$selChapterSubheading	= $ver[5];
			$selGoodType		= $ver[6];

			# Checking Prouct Group Exist
			$productGroupExist = $gstMasterObj->checkProductGroupExist($selProdState);
			# Product Group Records
			$productGroupRecords = $gstMasterObj->filterProductGroupList($productGroupExist);	
			//printr($productGroupRecords);
	?>	
	<tr align="center" class="whiteRow" id="row_<?=$j?>"> 
		<td class="listing-item" align="center">
			<select name="selProductCategory_<?=$j?>" id="selProductCategory_<?=$j?>" disabled="true">
				<option value="">-- Select --</option>
				<?php
					if (sizeof($productCategoryRecords)>0) {
						$categoryId = "";	
						foreach ($productCategoryRecords as $cr) {
							$categoryId	= $cr[0];
							$categoryName	= stripSlash($cr[1]);
							$selected = ($selProdCategory==$categoryId)?"Selected":"";
				?>					
				<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
				<?php
						}
					}
				?>
			</select>
		</td>
		<td class="listing-item" align="center">
			<select name="selProductState_<?=$j?>" id="selProductState_<?=$j?>" onchange="xajax_getProductGroupExist(document.getElementById('selProductState_<?=$j?>').value,'<?=$j?>',''); " disabled="true">
				<option value="0">-- Select All --</option>
				<?php
					if (sizeof($productStateRecords)>0) {	
						foreach ($productStateRecords as $cr) {
							$prodStateId	= $cr[0];
							$prodStateName	= stripSlash($cr[1]);
							$selected = ($selProdState==$prodStateId)?"Selected":"";
				?>
				<option value="<?=$prodStateId?>" <?=$selected?>><?=$prodStateName?></option>
				<?php
						}
					}
				?>
			</select>
		</td>
		<td class="listing-item" align="center">
			<select name="selProductGroup_<?=$j?>" id="selProductGroup_<?=$j?>" disabled="true">
				<?php if (sizeof($productGroupRecords)<=0) {?><option value="0">-- Select -- </option><?php }?>
				<?php
					foreach ($productGroupRecords as $productGroupId=>$productGroupName) {
						$selected = ($selProdGroup==$productGroupId)?"Selected":"";
				?>
				<option value="<?=$productGroupId?>" <?=$selected?>><?=$productGroupName?></option>
				<?php
					}
				?>
			</select>
		</td>
		<td class="listing-item" align="center">
		<input name="gstPercent_<?=$j?>" id="gstPercent_<?=$j?>" value="<?=$gst_per?>" size="4" style="text-align: right;" type="text">
		</td>
		<td align="center" class="listing-item">
			<input type="text" size="20" value="<?=$selChapterSubheading?>" id="chapterSubheading_<?=$j?>" name="chapterSubheading_<?=$j?>"/>
		</td>
		<td align="center" class="listing-item">
		<select id="goodsType_<?=$j?>" name="goodsType_<?=$j?>">
		<option value="">-- Select --</option>
			<?php	
				foreach ($exGoodsMasterRecs as $egm) {
					$exGoodsId 	= $egm[0];
					$exGoodname	= $egm[1];
					$selected = ($exGoodsId==$selGoodType)?"Selected":"";
			?>
			<option value="<?=$exGoodsId?>" <?=$selected?>><?=$exGoodname?></option>	
			<?php			
				}
			?>
		</select>
		</td>		
		<td class="listing-item" align="center">
			<a href="###" onclick="return false;setProdItemStatus('0');">
				<img title="Click here to remove this item" src="images/delIcon.gif" style="border: medium none ;" border="0">
			</a>
			<input type="hidden" name="status_<?=$j?>" id="status_<?=$j?>" value="">
			<input type="hidden" name="IsFromDB_<?=$j?>" id="IsFromDB_<?=$j?>" value="N" >
			<input type="hidden" name="productStateGroup_<?=$j?>" id="productStateGroup_<?=$j?>" value="<?=$productGroupExist?>" >
			<input type="hidden" name="stateVatEntryId_<?=$j?>" id="stateVatEntryId_<?=$j?>" value="<?=$edEntryId?>">
			<input type="hidden" name="hidgstPercent_<?=$j?>" id="hidgstPercent_<?=$j?>" value="<?=$gst_per?>" size="4" style="text-align: right;" readonly>
		</td>
	</tr>
	<?php
			$j++;
			}
		}
	?>		
				</table>
			</td>
		</tr>
		<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=sizeof($gstEntryRecords)?>" readonly />
<?php
	if (sizeof($gstEntryRecords)>0) {
?>
<script language="JavaScript"> 
fieldId = '<?=sizeof($gstEntryRecords)?>';
</script>
<?php
}
?>
<!--  Dynamic Row Ends Here-->
<tr id="catRow2"><TD height="5"></TD></tr>
<?
if ($addMode) {
?>
<tr id="catRow3">
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewProductCatItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
<?
}
?>
	
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GstMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateGstMaster(document.frmGstMaster);">												</td>
											<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GstMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateGstMaster(document.frmGstMaster);">												</td>
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
						<table cellpadding="4" cellspacing="0">
					  <tr>
					<td nowrap="nowrap">
	<table cellpadding="0" cellspacing="0">
	<tr>
	<td>
		<table>
			<TR>
				<td nowrap>
					<Table cellpadding="0" cellspacing="0">
						<TR>
							<?php
							if ($gstRateListId!=0 && $del) {
							?>
							<TD>
							<a href="###" title="delete rate list" onclick="deleteEDRateList();"><img src="images/trash.png" style="cursor:pointer;" border="0"/></a>
							</TD>
							<?php
							}
							?>
							<td nowrap="true">
								<span class="fieldName">Rate List</span>&nbsp;
							</td>
						</TR>
					</table>
				</td>
				<td>
					<select name="gstRateListFilter" id="gstRateListFilter" onchange="this.form.submit();" style="width:180px;">
						<option value="">-- Select --</option>
						<?
						foreach ($gstRLFilterRecs as $srl) {
							$rateListRecId	=	$srl[0];
							$rateListName	=	stripSlash($srl[1]);				
							$startDate	=	dateFormat($srl[2]);
							$displayRateList = $rateListName."&nbsp;(".$startDate.")";
							$selected = ($gstRateListFilterId==$rateListRecId || $gstRateListId==$rateListRecId)?"Selected":"";
						?>
					<option value="<?=$rateListRecId?>" <?=$selected?>><?=$displayRateList?>
					</option>
					<? }?>
					</select>
				</td>
			</TR>
			<?php
			if ($curgstRateListId==$gstRateListId && $gstRateListId!=0) {
			?>
			<tr>
				<td class="fieldName" nowrap title="Rate list start date" >*Start Date </td>
				<td nowrap="true">
					<INPUT TYPE="text" NAME="startDate" id="startDate" value="<?= ($selStartDate) ? dateFormat($selStartDate) : ""; ?>" size="8" autocomplete="off" onchange="toggleStartDate();"/>
					<INPUT NAME="hidStartDate" TYPE="hidden" id="hidStartDate" value="<?= ($selStartDate) ? dateFormat($selStartDate) : ""; ?>" size="8" autocomplete="off" readonly="true">
					<span id="startDateUptd" style="display:none;"><input type="button" value=" Update " name="cmdSDUpdate" class="button" onclick="updateStartDate();"></span>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
	</td>
			
	<td>&nbsp;</td>
		<td valign="top">
		<?php
			if ($curgstRateListId==$gstRateListId && $gstRateListId!=0 && $edit) {
			?>
		<table>
			<TR>
				<td class="fieldName" nowrap="nowrap" title="Enable/Disable Gst Percent">Active</td>
				<td nowrap="true" align="left" id="edActiveFlag">
					<!--input type="checkbox" id="edActive" name="edActive" class="chkbox" value="Y" <?= $excActive; ?> /-->
					<?php
					if ($excActive!="") {
					?>
						<img onclick="uptdActiveFlag('N');" src="images/y.png" style="cursor:pointer;"/>
					<?php
					} else {
					?>
						<img onclick="uptdActiveFlag('Y');" src="images/x.png" style="cursor:pointer;" />
					<?php
					}
					?>
					
				</td>
				<td>
					<? if($add==true){?>
						<!--input type="submit" value=" Enable/Disable Gst Percent" name="cmdEDActive" class="button"-->

					<? }?>
				</td>
			</TR>
		</table>
		<?
			}
		?>
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
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$gst_perRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?>
												<!--
												<input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintGstMaster.php',700,600);">
												-->
												<? }?></td>
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
	<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($gst_perRecordSize) {
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
      				$nav.= " <a href=\"GstMaster.php?pageNo=$page&gstRateListFilter=$gstRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"GstMaster.php?pageNo=$page&gstRateListFilter=$gstRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"GstMaster.php?pageNo=$page&gstRateListFilter=$gstRateListFilterId\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Product Category</th>		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Product State</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Product Group</th>		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">GST (%)</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Chapter/Subheading</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name of Goods</th>
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
			$prevStateId	= "";
			while ($svr=$gst_perResultSetObj->getRow()) {
				$i++;
				$gstId 	= $svr[0];	
				$gst_per     = $svr[2];					
				$pCatName	= $svr[3];
				$pStateName	= $svr[4];
				$pGroupName	= $svr[5]; 
				$pChapter	= $svr[6];
				$exGoodsType	= $svr[8];
				$exemptionCode	= $svr[9];
				$active=$svr[10];
				//echo strlen($exemptionCode);
			?>
	<tr  <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$gstId;?>" class="chkBox">			
		</td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$pCatName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$pStateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$pGroupName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$gst_per;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="left" width="300px" nowrap="nowrap"><?=$pChapter;?>
			<? 
				if ($exemptionCode!="") {
					if ($pChapter!="") echo ", ";	
			?>
			<?//=$exemptionCode;?>				
			<?=wordwrap($exemptionCode, 100, "<br />");?>
			<? }?>
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$exGoodsType;?></td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$gstId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='GstMaster.php';" ><? }?></td>
<? }?>

 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$gstId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm"  onClick="assignValue(this.form,<?=$gstId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		</tr>
		<?			
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
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
      				$nav.= " <a href=\"GstMaster.php?pageNo=$page&gstRateListFilter=$gstRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"GstMaster.php?pageNo=$page&gstRateListFilter=$gstRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"GstMaster.php?pageNo=$page&gstRateListFilter=$gstRateListFilterId\"  class=\"link1\">>></a> ";
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
			<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$gst_perRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?>
												<!--
												<input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintGstMaster.php',700,600);">
												-->
												<? }?>
												
												
												</td>
											</tr>
										</table>									</td>
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
				<!-- Form fields end   -->	
		</td>
		</tr>	
<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">		
<input type="hidden" name="hidEDRateListFilterId" value="<?=$gstRateListFilterId?>">	
		<tr>
			<td height="10"></td>
		</tr>				
	</table>
	<?php 
		if ($addMode || $editMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		function addNewProductCatItem()
		{
			addNewProductCatItemRow('tblAddProdCategory', '', '', '', '');	
		}
	</SCRIPT>
	<?php 
		} 
	?>

	<?php
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewProductCatItem();
	</SCRIPT>
	<?php 
		}
	?>	
	<?
		if ($editMode && $enabled) {		
	?>
	<script language="JavaScript" type="text/javascript">
		//xajax_getStateVatRateList('<?=$selStateId?>', '<?=$mode?>', '<?=$stateWiseVatRateListId?>');
	</script>	
	<?
		}
	?>
	<SCRIPT LANGUAGE="JavaScript">
		<!--
		Calendar.setup
		(
			{
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate",
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
			}
		);
		//-->		
	</SCRIPT>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>