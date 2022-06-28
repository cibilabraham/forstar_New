<?php
	require("include/include.php");
	require_once("lib/StateVatMaster_ajax.php");

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
		
	$selection 	=	"?pageNo=".$p["pageNo"]."&stateFilter=".$p["stateFilter"]."&stateVatRateListFilter=".$p["stateVatRateListFilter"];	
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

	/**************/
	//echo "h====<br>";
	//$stateVatMasterObj->updateDistMarginRecs(1);	
	/**************/

	//$svRateListId =$stateVatRateListObj->latestRateList($state);

	//$stateVatMasterObj->distMgnStateRecs($stateId=1, $userId=1); //1 - 4	
	
	#Add a Record
	if ($p["cmdAdd"]!="") {
		$state			= $p["state"];	
		$tableRowCount		= $p["hidTableRowCount"];
		$copyFromStateId		= $p["copyFromStateId"];
		$copyFromStateVatRateList 	= $p["copyFromStateVatRateList"]; 
		$copyFromStateVatId = "";
		if ($copyFromStateId!="" && $copyFromStateVatRateList!="") {
			$copyFromStateVatId	= $stateVatMasterObj->getStateVatEntryId($copyFromStateId,$copyFromStateVatRateList);
		}

		$stateVatRateListId	= $p["stateVatRateList"];
		
		$svRateListId =$stateVatRateListObj->latestRateList($state);

		# Creating a New Rate List
		if ($stateVatRateListId=="" && $svRateListId=="") {
			$stateRec	= $stateMasterObj->find($state);
			$stateName = str_replace (" ",'', $stateRec[2]);
			$selName =substr($stateName, 0,9);	
			$rateListName = $selName."-".date("dMy");
			$startDate    = date("Y-m-d");
			$stateVatRateListRecIns = $stateVatRateListObj->addStateVatRateList($rateListName, $startDate, '', $userId, $state, '');
			if ($stateVatRateListRecIns) {
				$stateVatRateListId =$stateVatRateListObj->latestRateList($state);	
				#Upate Dist Margin State Rec
				$upateDistMarginState =	$stateVatMasterObj->updateDistMarginRecs($stateVatRateListId);
			}
		} else if ($stateVatRateListId=="" && $svRateListId!="") {
			$stateVatRateListId = $svRateListId;
		}						
		

		if ($state!="" && $stateVatRateListId!=0) {
			$stateVatRecIns = $stateVatMasterObj->addStateVat($state, $userId, $copyFromStateVatId, $stateVatRateListId);
			#Find the Last inserted Id From m_distributor Table
			$lastId = $databaseConnect->getLastInsertedId();
			if ($tableRowCount>0 && $copyFromStateVatId=="") {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selPCategory 	= $p["selProductCategory_".$i];
						$selPState 	= $p["selProductState_".$i];
						$selPGroup 	= $p["selProductGroup_".$i];	
						$vatPercent	= $p["vatPercent_".$i];

						# IF SELECT ALL STATE
						if ($lastId!="" && $selPCategory!="" && $selPState==0) {
							//echo "STATE SELECT ALL";
							# Get Product State Records
							//$prodStateRecords = $productStateObj->fetchAllRecords();
							$prodStateRecords = $productStateObj->fetchAllRecordsActiveProduct();
							
							foreach ($prodStateRecords as $cr) {
								$prodStateId	= $cr[0];
								# Chk Prod Group Exist
								$prodGroupExist = $stateVatMasterObj->checkProductGroupExist($prodStateId);
								if ($prodGroupExist) {
									# Prod Group Recs
									//$prodGroupRecs = $productGroupObj->fetchAllRecords();
									$prodGroupRecs = $productGroupObj->fetchAllRecordsActiveGroup();
									$prodGroupId = 0;
									foreach ($prodGroupRecs as $pgr) {
										$prodGroupId	= $pgr[0];
										if ($lastId!="") {
											$vatEntryIns = $stateVatMasterObj->addVatEntries($lastId, $selPCategory, $prodStateId, $prodGroupId, $vatPercent);
										}
									} # Group Loop Ends Here
								} else {  # If Not Product group
									$prodGroupId = 0;
									if ($lastId!="") {
										$vatEntryIns = $stateVatMasterObj->addVatEntries($lastId, $selPCategory, $prodStateId, $prodGroupId, $vatPercent);
									}	
								} # Product Goup Chk Ends Here			
							}  # Prod State Loop Ends Here
						}  # If State SELECT ALL
						
						# If group Select All
						if ($lastId!="" && $selPCategory!="" && $selPState!=0 &&  $selPGroup==0) {
							//echo "<br>Group SELECT ALL";
							# Chk Prod Group Exist
							$prodGroupExist = $stateVatMasterObj->checkProductGroupExist($selPState);
							if ($prodGroupExist) {					
								# Prod Group Recs
								//$prodGroupRecs = $productGroupObj->fetchAllRecords();
								$prodGroupRecs = $productGroupObj->fetchAllRecordsActiveGroup();
								$prodGroupId = 0;
								foreach ($prodGroupRecs as $pgr) {
									$prodGroupId	= $pgr[0];
									if ($lastId!="") {
										$vatEntryIns = $stateVatMasterObj->addVatEntries($lastId, $selPCategory, $selPState, $prodGroupId, $vatPercent);
									}
								} # Group Loop Ends Here
							} else {
								$prodGroupId = 0;
								if ($lastId!="") {
									$vatEntryIns = $stateVatMasterObj->addVatEntries($lastId, $selPCategory, $selPState, $prodGroupId, $vatPercent);
								}
							}
						}

						# Individual Inserting
						if ($lastId!="" && $selPCategory!="" && $selPState!=0 && $selPGroup!=0 && $selPGroup!='N') {
							//echo "<br>Indi";
							$vatEntryIns = $stateVatMasterObj->addVatEntries($lastId, $selPCategory, $selPState, $selPGroup, $vatPercent);
						}					
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here
			#Upate Dist Margin State Rec
			$upateDistMarginState =	$stateVatMasterObj->updateDistMarginRecs($stateVatRateListId);
			if ($stateVatRecIns || $stateVatRecUptd) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddStateVatMaster);
				$sessObj->createSession("nextPage",$url_afterAddStateVatMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddStateVatMaster;
			}
			$stateVatRecIns = false;
		} else {
			$addMode = true;
			if ($entryExist) $err = $msg_failAddStateVatMaster."<br>".$msgFailAddStateVatExistRec;
			else $err = $msg_failAddStateVatMaster;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		$stateVatId		= $p["hidStateVatId"];		
		$state			= $p["state"];			
		$tableRowCount		= $p["hidTableRowCount"];
		$stateVatRateListId	= $p["stateVatRateList"];
	
		if ($stateVatId!="" && $state!="" ) {
			# Update Main Table			
			$stateVatRecUptd = $stateVatMasterObj->updateStateVat($stateVatId, $state, $stateVatRateListId);
			$vatPercentChanged = false;
			for ($i=0; $i<$tableRowCount; $i++) {
				$status 	  = $p["status_".$i];
				$stateVatEntryId  = $p["stateVatEntryId_".$i];
				if ($status!='N') {
					$selPCategory 	= $p["selProductCategory_".$i];
					$selPState 	= $p["selProductState_".$i];
					$selPGroup 	= ($p["selProductGroup_".$i]!="")?$p["selProductGroup_".$i]:0;		
					$vatPercent	= $p["vatPercent_".$i];
					$hidVatPercent	= $p["hidVatPercent_".$i];
					
					if ($stateVatId!="" && $selPCategory!="" && $selPState!="" && $stateVatEntryId!="") {
						$updateVatEntryRec = $stateVatMasterObj->updateVatEntries($stateVatEntryId, $selPCategory, $selPState, $selPGroup, $vatPercent);
					} else if ($stateVatId!="" && $selPCategory!="" && $selPState!="" && $stateVatEntryId=="") {
						$vatEntryIns = $stateVatMasterObj->addVatEntries($stateVatId, $selPCategory, $selPState, $selPGroup, $vatPercent);
					}
					if ($vatPercent!=$hidVatPercent) $vatPercentChanged = true;
				} // Status Checking End

				if ($status=='N' && $stateVatEntryId!="") {
					$delVatEntryRec = $stateVatMasterObj->delVatEntryRec($stateVatEntryId);
				}
			} // State For Loop ends here
			#Update Dist Margin State Rec
			$updateDistMarginState =	$stateVatMasterObj->updateDistMarginRecs($stateVatRateListId);
			# Update SO Rec
			if ($vatPercentChanged) {
				$updateVatInSORec = $changesUpdateMasterObj->updateSOVATRec($state);
			}
		}
	
		if ($stateVatRecUptd || $stateVatRecIns) {
			$sessObj->createSession("displayMsg",$msg_succStateVatMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStateVatMaster.$selection);
		} else {
			$editMode	=	true;
			//$err		=	$msg_failStateVatMasterUpdate;
			if ($entryExist) $err = $msg_failStateVatMasterUpdate."<br>".$msgFailAddStateVatExistRec;
			else $err = $msg_failStateVatMasterUpdate;
		}
		$stateVatRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$stateVatRec	= $stateVatMasterObj->find($editId);
		$editStateVatId = $stateVatRec[0];
		if ($p["editSelectionChange"]=='1' || $p["state"]=="") {
			$selStateId	= $stateVatRec[1];
		} else {
			$selStateId	= $p["state"];
		}
		
		$stateWiseVatRateListId = $stateVatRec[2];
		
		# Get Vat Entry Records
		$vatEntryRecords = $stateVatMasterObj->getVatEntryRecords($editStateVatId);	

		# Get Rate List
		$stateWiseVatRateListRecs	= $stateVatRateListObj->getStateWiseVatFilterRateListRecs($selStateId);
		//printr($stateWiseVatRateListRecs);
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];

		for ($i=1; $i<=$rowCount; $i++) {
			$stateVatId	= $p["delId_".$i];
			
			//$stateEntryExist = $stateVatMasterObj->stateEntryExist($stateVatId); && !$stateEntryExist			
			if ($stateVatId!="") {

				$sVatRec = $stateVatMasterObj->find($stateVatId);
				$stateVatRateListId = $sVatRec[2];

				$sVatRLRec = $stateVatRateListObj->find($stateVatRateListId);
				$svrlStartDate = $sVatRLRec[2];
				//echo "$stateVatRateListId===$svrlStartDate";
				
				# Check state wise vat entry in use
				$stateVatEntryInUse = $stateVatMasterObj->chkStateVatRecInUse($svrlStartDate);
				
				if (!$stateVatEntryInUse) {		
					# Delete From Entry Table
					$stateVatEntryRecDel = $stateVatMasterObj->deleteStateVatEntryRec($stateVatId);
					# Delete From Main Table
					$stateVatRecDel = $stateVatMasterObj->deleteStateVatRec($stateVatId);
				}				
			} // State vat id ends here
		} // Loop ends here
		if ($stateVatRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStateVatMaster);
			$sessObj->createSession("nextPage",$url_afterDelStateVatMaster.$selection);
		} else {
			if ($stateVatEntryInUse) $errDel = $msg_failDelStateVatMaster."<br>State wise vat is alreay in use. ";
			else $errDel	=	$msg_failDelStateVatMaster;
		}
		$stateVatRecDel	=	false;
	}	


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stateVatId		=	$p["confirmId"];


			if ($stateVatId!="") {
				// Checking the selected fish is link with any other process
				$stateVatMasterRecConfirm = $stateVatMasterObj->updatestateVatconfirm($stateVatId);
			}

		}
		if ($stateVatMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmstateVatMaster);
			$sessObj->createSession("nextPage",$url_afterDelStateVatMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$stateVatId = $p["confirmId"];
			if ($stateVatId!="") {
				#Check any entries exist
				
					$stateVatMasterRecConfirm = $stateVatMasterObj->updatestateVatReleaseconfirm($stateVatId);
				
			}
		}
		if ($stateVatMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmstateVatMaster);
			$sessObj->createSession("nextPage",$url_afterDelStateVatMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["stateFilter"]!="") $stateFilterId = $g["stateFilter"];
	else $stateFilterId = $p["stateFilter"];	

	if ($g["stateVatRateListFilter"]!="") $stateVatRateListFilterId = $g["stateVatRateListFilter"];
	else $stateVatRateListFilterId = $p["stateVatRateListFilter"];	

	# Resettting offset values
	if ($p["hidStateFilterId"]!=$p["stateFilter"]) {		
		$offset = 0;
		$pageNo = 1;	
		$stateVatRateListFilterId = "";	
	} else if ($p["hidStateVatRateListFilterId"]!=$p["stateVatRateListFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}
		
	
	# List all State Vat Master
	$stateVatResultSetObj = $stateVatMasterObj->fetchAllPagingRecords($offset, $limit, $stateFilterId, $stateVatRateListFilterId);
	$stateVatRecordSize = $stateVatResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$fetchStateVatMasterResultSetObj = $stateVatMasterObj->fetchAllRecords($stateFilterId, $stateVatRateListFilterId);
	$numrows	=  $fetchStateVatMasterResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode || $editMode) {	
		# List all State
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
		# List all Product Category
		//$productCategoryRecords	= $productCategoryObj->fetchAllRecords();

		$productCategoryRecords	= $productCategoryObj->fetchAllRecordsActiveCategory();
		# List all Product State Records
		//$productStateRecords = $productStateObj->fetchAllRecords();
		$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();
		# List all Product Group Records
		//$productGroupRecords =$productGroupObj->fetchAllRecords();
		
		# Distributor based Margin Rate List		
		//if ($addMode) $selRateList = $distMarginRateListObj->latestRateList($selDistributor);
	}

	if ($stateFilterId) {
		$stateVatRateListFilterRecords = $stateVatRateListObj->filterStateWiseVatRateListRecords($stateFilterId);
	}

	# List all State
		//$stateFilterResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateFilterResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();

	if ($addMode) {
		$stateVatRecords = $stateVatMasterObj->getStateVatRecords();
	}

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editStateVatMaster;
	else	       $heading	=	$label_addStateVatMaster;

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/StateVatMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmStateVatMaster" action="StateVatMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr>
		<td height="10" align="center"><a href="StateVatRateList.php" class="link1" title="Click to manage Rate List">State Wise Vat Rate List</a></td>
	</tr>
	<tr><TD height="5"></TD></tr>
		<?php
		if (!$stateFilterId && !$stateVatRateListFilterId) {
		?>
		<tr> 
			<td  align="center" class="listing-item" style="color:Maroon;">
				<strong>Current State wise vat list.</strong>
			</td>
		</tr>
		<?php
			}
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
					$bxHeader = "State wise Vat Master";
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('StateVatMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateStateVatMaster(document.frmStateVatMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateVatMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateStateVatMaster(document.frmStateVatMaster);">												</td>

												<?}?>
											</tr>
					<input type="hidden" name="hidStateVatId" value="<?=$editStateVatId;?>">
	<tr><TD height="10"></TD></tr>
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
		<table width="200">
								
		<tr>
	  		<td class="fieldName" nowrap >*State</td>
			<td>
				<select name="state" id="state" onchange="xajax_chkSelStateExist(document.getElementById('state').value,'<?=$mode?>','<?=$editStateVatId?>',document.getElementById('stateVatRateList').value); xajax_getStateVatRateList(document.getElementById('state').value, '<?=$mode?>', '');" style="width:110px;">
				<option value="">--Select--</option>
				<?php				
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
			</td>
		</tr>	
		<?
			if ($addMode) {
		?>	
		<tr>
	  		<td class="fieldName" nowrap >Copy From</td>
			<td nowrap="true" class="listing-item">
				<select name="copyFromStateId" id="copyFromStateId" onchange="hideCategoryRows();xajax_getCopyFromStateVatRateList(document.getElementById('copyFromStateId').value);" style="width:110px;">
				<option value="">-- Select State--</option>
				<?				
				foreach ($stateVatRecords as $sr) {
					$selStateVatId	= $sr[0];
					$selStateId	= $sr[1];	
					$selStatName	= stripSlash($sr[2]);	
					$selected 	= "";
					//if ($selCategoryId==$pCategoryId) $selected = "selected";
				?>
				<option value="<?=$selStateId?>" <?=$selected?>><?=$selStatName?></option>
				<?
				}
				?>
				</select>
				&nbsp;
				<span class="fieldName">Rate list</span>&nbsp;
				<select name="copyFromStateVatRateList" id="copyFromStateVatRateList" style="width:110px;">
                        		<option value="">-- Select --</option>			
                                </select>
			</td>
		</tr>
		<?
			}
		?>
               </table>
		</td>
		</tr>
		<?
			if ($addMode) {
		?>
		<tr id="catRow0"><TD class="listing-item" colspan="2" align="center">[OR]</TD></tr>
		<? } ?>
<!--  Dynamic Row Starts Here-->
	<tr id="catRow1">
		<td colspan="2" style="padding-left:5px;padding-right:5px;">
			<table  cellspacing="1" cellpadding="3" id="tblAddProdCategory" class="newspaperType">
			<tr align="center">
				<th class="listing-head" style="padding-left:5px;padding-right:5px;text-align:center; " nowrap="true">*Product Category</th>
				<th class="listing-head" style="padding-left:5px;padding-right:5px;text-align:center;" nowrap="true">*Product State</th>
				<th class="listing-head" style="padding-left:5px;padding-right:5px;text-align:center;" nowrap="true">Product Group</th>
				<th class="listing-head" nowrap style="padding-left:5px;padding-right:5px;text-align:center;">*VAT</th>	
				<th>&nbsp;</th>			
			</tr>		
	<?php
	if (sizeof($vatEntryRecords)>0) {
		$j=0;
		foreach ($vatEntryRecords as $ver) {			
			$stateVatEntryId 	= $ver[0];
			$selProdCategory 	= $ver[2]; 
			$selProdState		= $ver[3]; 	
			$selProdGroup		= $ver[4]; 	
			$vat			= $ver[5];

			# Checking Prouct Group Exist
			$productGroupExist = $stateVatMasterObj->checkProductGroupExist($selProdState);
			# Product Group Records
			$productGroupRecords = $stateVatMasterObj->filterProductGroupList($productGroupExist);	
			//printr($productGroupRecords);
	?>	
	<tr align="center" class="whiteRow" id="row_<?=$j?>"> 
		<td class="listing-item" align="center">
			<select name="selProductCategory_<?=$j?>" id="selProductCategory_<?=$j?>">
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
			<select name="selProductState_<?=$j?>" id="selProductState_<?=$j?>" onchange="xajax_getProductGroupExist(document.getElementById('selProductState_<?=$j?>').value,'<?=$j?>',''); ">
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
			<select name="selProductGroup_<?=$j?>" id="selProductGroup_<?=$j?>">
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
		<input name="vatPercent_<?=$j?>" id="vatPercent_<?=$j?>" value="<?=$vat?>" size="4" style="text-align: right;" type="text">
		</td>
		<td class="listing-item" align="center">
			<a href="###" onclick="setProdItemStatus('0');">
				<img title="Click here to remove this item" src="images/delIcon.gif" style="border: medium none ;" border="0">
			</a>
			<input type="hidden" name="status_<?=$j?>" id="status_<?=$j?>" value="">
			<input type="hidden" name="IsFromDB_<?=$j?>" id="IsFromDB_<?=$j?>" value="N" >
			<input type="hidden" name="productStateGroup_<?=$j?>" id="productStateGroup_<?=$j?>" value="<?=$productGroupExist?>" >
			<input type="hidden" name="stateVatEntryId_<?=$j?>" id="stateVatEntryId_<?=$j?>" value="<?=$stateVatEntryId?>">
			<input type="hidden" name="hidVatPercent_<?=$j?>" id="hidVatPercent_<?=$j?>" value="<?=$vat?>" size="4" style="text-align: right;" readonly>
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
		<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=sizeof($vatEntryRecords)?>" readonly />
<?php
	if (sizeof($vatEntryRecords)>0) {
?>
<script language="JavaScript"> 
fieldId = '<?=sizeof($vatEntryRecords)?>';
</script>
<?php
}
?>
<!--  Dynamic Row Ends Here-->
<tr id="catRow2"><TD height="5"></TD></tr>
<tr id="catRow3">
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewProductCatItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
	<tr>
		<TD colspan="2" style="padding-left:5px;padding-right:5px;">
			<table>
				<tr id="rateListRow">
				<td class="fieldName" nowrap>*Rate list</td>
				<td nowrap>
				<select name="stateVatRateList" id="stateVatRateList" onchange="xajax_chkSelStateExist(document.getElementById('state').value,'<?=$mode?>','<?=$editStateVatId?>',document.getElementById('stateVatRateList').value);" style="width:110px;">
                        		<?php if (sizeof($stateWiseVatRateListRecs)<=0) {?><option value="">-- Select --</option><? }?>
					<?php
					foreach ($stateWiseVatRateListRecs as $swvRateListId=>$swvRateName) {
						$selected = ($stateWiseVatRateListId==$swvRateListId)?"selected":"";
					?>
					<option value="<?=$swvRateListId?>" <?=$selected?>><?=$swvRateName?></option>
					<?php
					}
					?>
                                </select>
				</td>
						</tr>
			</table>
		</TD>
	</tr>
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateVatMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateStateVatMaster(document.frmStateVatMaster);">												</td>
											<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateVatMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateStateVatMaster(document.frmStateVatMaster);">												</td>
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
		<td class="listing-item" nowrap>State&nbsp;</td>
                <td>
		<select name="stateFilter" id="stateFilter" onchange="this.form.submit();" style="width:120px;">
		<option value="">-- Select All --</option>
		<?	
			while ($sr=$stateFilterResultSetObj->getRow()) {
				$stateId 	= $sr[0];
				$stateName	= stripSlash($sr[2]);	
				$selected 	= ($stateFilterId==$stateId)?"selected":"";	
		?>
                <option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
		<? 
			}
		?>		
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
	   <td class="listing-item" nowrap>Rate List&nbsp;</td>
	<td>
		<select name="stateVatRateListFilter" id="stateVatRateListFilter" onchange="this.form.submit();" style="width:120px;">
                        <option value="">-- Select All --</option>
			<?
			foreach ($stateVatRateListFilterRecords as $srl) {
				$rateListRecId	=	$srl[0];
				$rateListName	=	stripSlash($srl[1]);				
				$startDate	=	dateFormat($srl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = ($stateVatRateListFilterId==$rateListRecId)?"Selected":"";
			?>
                      <option value="<?=$rateListRecId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                      </select>
	</td>		
          <td>&nbsp;</td>
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
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;State wise Vat Master</td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stateVatRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStateVatMaster.php',700,600);"><? }?></td>
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
		if ($stateVatRecordSize) {
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
      				$nav.= " <a href=\"StateVatMaster.php?pageNo=$page&stateFilter=$stateFilterId&stateVatRateListFilter=$stateVatRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StateVatMaster.php?pageNo=$page&stateFilter=$stateFilterId&stateVatRateListFilter=$stateVatRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StateVatMaster.php?pageNo=$page&stateFilter=$stateFilterId&stateVatRateListFilter=$stateVatRateListFilterId\"  class=\"link1\">>></a> ";
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
		<?php
		if ($stateFilterId!="" && !$stateVatRateListFilterId) {
		?>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Rate List</th>		
		<?php
			}
		?>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">State</th>		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">No. of<br> Combination</th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">VAT&nbsp;(%)</th>	
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
			while ($svr=$stateVatResultSetObj->getRow()) {
				$i++;
				$stateVatId 	= $svr[0];	
				$sStateId	= $svr[1];
				$selStateName	= $svr[2];	
				$selRateListName = $svr[4];
				$active=$svr[5];
				# No .of Combination
				$noOfCombination = sizeof($stateVatMasterObj->getCombination($stateVatId));

				# Get State Wise Vat Percent
				$getVatRates = $stateVatMasterObj->getVatRates($stateVatId);
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" <?php }?>>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stateVatId;?>" class="chkBox">			
		</td>
		<?php
		if ($stateFilterId!="" && !$stateVatRateListFilterId) {
		?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$selRateListName?></td>		
		<?php
			}
		?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$selStateName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$noOfCombination;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">
			<table id="newspaper-b1-no-style">
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getVatRates)>0) {
						$nextRec	=	0;
						$k=0;
						$vatPercent = "";
						foreach ($getVatRates as $cR) {
							$j++;
							$vatPercent = $cR[0];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$vatPercent?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<? 
						}	
					 }
					}
				?>
				</tr>
			</table>
		</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stateVatId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='StateVatMaster.php';" ><? } ?></td>
<? }?>
 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stateVatId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$stateVatId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		</tr>
		<?			
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<input type="hidden" name="editSelectionChange" value="0"><input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"StateVatMaster.php?pageNo=$page&stateFilter=$stateFilterId&stateVatRateListFilter=$stateVatRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StateVatMaster.php?pageNo=$page&stateFilter=$stateFilterId&stateVatRateListFilter=$stateVatRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StateVatMaster.php?pageNo=$page&stateFilter=$stateFilterId&stateVatRateListFilter=$stateVatRateListFilterId\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stateVatRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStateVatMaster.php',700,600);"><? }?></td>
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
<input type="hidden" name="hidStateFilterId" value="<?=$stateFilterId?>">	
<input type="hidden" name="hidStateVatRateListFilterId" value="<?=$stateVatRateListFilterId?>">	
		<tr>
			<td height="10"></td>
		</tr>	
		<tr><td height="10" align="center"><a href="StateVatRateList.php" class="link1" title="Click to manage Rate List">State Wise Vat Rate List</a></td></tr>	
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
	<!-- Edit Record -->
	<script language="JavaScript">
	<?
		if (sizeof($vatEntryRecords56566)>0) {
			$j=0;
			foreach ($vatEntryRecords as $ver) {			
				$stateVatEntryId 	= $ver[0];
				$selProdCategory 	= $ver[2]; 
				$selProdState		= $ver[3]; 	
				$selProdGroup		= $ver[4]; 	
				$vat			= $ver[5];			
	?>	
		addNewProductCatItemRow('tblAddProdCategory',<?=$selProdCategory?>, <?=$selProdState?>,<?=$vat?>, <?=$stateVatEntryId?>);
		xajax_getProductGroupExist('<?=$selProdState?>',<?=$j?>,<?=$selProdGroup?>);
	<?php
			$j++;
			}
		}
	?>
	</script>
	<?
		if ($editMode && $enabled) {		
	?>
	<script language="JavaScript" type="text/javascript">
		//xajax_getStateVatRateList('<?=$selStateId?>', '<?=$mode?>', '<?=$stateWiseVatRateListId?>');
	</script>	
	<?
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>