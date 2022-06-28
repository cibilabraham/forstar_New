<?php
	require("include/include.php");
	require_once("lib/RtCounterMarginStructure_ajax.php");	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$userId		= $sessObj->getValue("userId");
	$avgMargin	= "";
	$selection =  "?pageNo=".$p["pageNo"]."&rtCounterFilter=".$p["rtCounterFilter"]."&rtCounterRateListFilter=".$p["rtCounterRateListFilter"];
	
	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
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

	# Add New Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") {		
		$addMode = false;	
		$editMode = false;
		$editSelected = false;
		$p["cmdContinue"] = "";
		$p["editId"] = "";
		$sessObj->updateSession("selRowItem",0);
		$editId   = "";
	}

	/* $retCtMarginRateListId = retail counter margin rate list */
	#Add a Rec
	if ($p["cmdAdd"]!="") {	
		$selRetailCounter 	= $p["selRetailCounter"];	
		$selProduct		= $p["selProduct"];		
		$margin			= $p["margin"];
		$retCtMarginRateListId	= $p["retCtMarginRateList"];
	
		# Creating a New Rate List
		if ($retCtMarginRateListId=="") {
			$retailCounterRec	= $retailCounterMasterObj->find($selRetailCounter);	
			$retailCounterName 	= str_replace (" ",'',$retailCounterRec[2]);
			$selName 		= substr($retailCounterName, 0,9);	
			$rateListName = $selName."-".date("dMy");
			$startDate    = date("Y-m-d");
			$rtCounterMarginRateListRecIns = $rtCountMarginRateListObj->addRtCounterMarginRateList($rateListName, $startDate, $cyRateList, $userId, $selRetailCounter);
			if ($rtCounterMarginRateListRecIns) $retCtMarginRateListId =$rtCountMarginRateListObj->latestRateList($selRetailCounter);	
		}

		$selPCategory 	= $p["selProductCategory"];
		$selPState 	= $p["selProductState"];
		$selPGroup 	= $p["selProductGroup"];

		#Checking same entry exist in the table
		//$sameEntryExist = $rtCounterMarginStructureObj->checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProduct, '');
		//&& !$sameEntryExist
		$sameEntryExist = "";
		if ($selRetailCounter!="" ) {

			if ($selProduct=="") {	// Multiple Product
				# Get Product based on selection
				$getProductRecords = $rtCounterMarginStructureObj->getProductRecords($selPCategory, $selPState, $selPGroup);
				$selProductId = "";
				foreach ($getProductRecords as $gpr) {
					$selProductId  = $gpr[0];
					$selProductName =  $gpr[1];
					//echo "<br>$selProductId:$selProductName<br>";
					#Checking same entry exist in the table
					$sameEntryExist = $rtCounterMarginStructureObj->checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProductId, '');
					if (!$sameEntryExist) {
						$rtCounterMarginRecIns = $rtCounterMarginStructureObj->addRtCounterMarginStructure($selRetailCounter, $selProductId, $margin, $retCtMarginRateListId, $userId);
					}
					$rtCounterMarginRecIns = true;
				}

			} else if ($selProduct)  {	// If Single Product
				#Checking same entry exist in the table
				$sameEntryExist = $rtCounterMarginStructureObj->checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProduct, '');
				if (!$sameEntryExist) {
					$rtCounterMarginRecIns = $rtCounterMarginStructureObj->addRtCounterMarginStructure($selRetailCounter, $selProduct, $margin, $retCtMarginRateListId, $userId);
				}
			}

			if ($rtCounterMarginRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddRtCounterMarginStructure);
				$sessObj->createSession("nextPage",$url_afterAddRtCounterMarginStructure.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddRtCounterMarginStructure;
			}
			$rtCounterMarginRecIns = false;
		} else {
			if ($sameEntryExist)	$err = $msg_failRtCounterMarginDuplication; // Duplication err
			else 			$err = $msg_failAddRtCounterMarginStructure;
		}
	}


	#Update a Record
	//if ($p["cmdSaveChange"]!="") {
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveEdit"]!="") {

		$editSelection = $p["hidSelection"];

		if ($editSelection=='G') {
			$editSelItemVal = $sessObj->getValue("selRowItem");
			list($selRetailCounter,$marginPercent,$retCtMarginRateListId,$editRtCounterMarginId) = getRowValues($editSelItemVal);
			# Product Records	
			$productRecords = $rtCounterMarginStructureObj->getRtCounterMarginProductRecs($selRetailCounter, $marginPercent, $retCtMarginRateListId);
		}

		$rtCounterMarginId 	= $p["hidDistMarginStructureId"];
		$selRetailCounter 	= $p["selRetailCounter"];	
		$selProduct		= $p["selProduct"];
		$retCtMarginRateListId	= $p["retCtMarginRateList"];
		$margin			= $p["margin"];
		
		#Checking same entry exist in the table
		//$sameEntryExist = $rtCounterMarginStructureObj->checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProduct, $rtCounterMarginId);   && !$sameEntryExist
	
		//if ($rtCounterMarginId!="" && $selRetailCounter!="" && $selProduct!="" ) {
		if ($rtCounterMarginId!="" && $editSelection=='I') {
			//$rtCounterMarginRecUptd = $rtCounterMarginStructureObj->updateRtCounterMarginStructure($rtCounterMarginId, $selRetailCounter, $selProduct, $retCtMarginRateListId, $margin);
			$rtCounterMarginRecUptd = $rtCounterMarginStructureObj->updateRtCtMarginStructure($rtCounterMarginId, $margin);
		} else if ($rtCounterMarginId!="" && $editSelection=='G') {
			
			foreach ($productRecords as $pr) {
				$rtctMgnEntyId	= $pr[3];
				$rtCounterMarginRecUptd = $rtCounterMarginStructureObj->updateRtCtMarginStructure($rtctMgnEntyId, $margin);
			}
			$rtCounterMarginRecUptd = true;
		}
	
		if ($rtCounterMarginRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRtCounterMarginStructureUpdate);
			if ($p["cmdSaveEdit"]!="") {
				$editMode = true;
				$selProduct = "";
				$p["selProduct"] = "";
			} else {
				$sessObj->createSession("nextPage",$url_afterUpdateRtCounterMarginStructure.$selection);
			}
		} else {
			$editMode	=	true;
			if ($sameEntryExist)	$err = $msg_failRtCounterMarginDuplication; // Duplication err
			else $err		=	$msg_failRtCounterMarginStructureUpdate;
		}
		$rtCounterMarginRecUptd	=	false;
	}


	# Edit  a Record
	/*
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$rtCounterMarginRec	= $rtCounterMarginStructureObj->find($editId);
		$editRtCounterMarginId  = $rtCounterMarginRec[0];
		$selRetailCounter 	= $rtCounterMarginRec[1];	
		$selProduct		= $rtCounterMarginRec[2];		
		$retCtMarginRateListId	= $rtCounterMarginRec[3];
		$margin			= $rtCounterMarginRec[4];			
	}
	*/

	/*
	* Spliting the Row Values
	* Format =>"$retailCounterId,$marginPercent,$rtctRateListId,$rtCounterMarginId";
	*/
	function getRowValues($editSelItemVal)
	{
		$selRowVal = explode(",",$editSelItemVal);
		$selRetailCounter		= $selRowVal[0];
		$marginPercent			= $selRowVal[1];
		$retCtMarginRateListId		= $selRowVal[2];
		$editRtCounterMarginId 		= $selRowVal[3];
		return array($selRetailCounter,$marginPercent,$retCtMarginRateListId,$editRtCounterMarginId);
	}

	/* Delete Single Product */
	if ($p["cmdDelMargin"]!="") {

		$rtCounterMarginId 	= $p["hidDistMarginStructureId"];
		# Need to Check whether Margin Used		
		if ($rtCounterMarginId) {
			$rtCounterMarginRecDel = $rtCounterMarginStructureObj->deleteRtCounterMarginStructure($rtCounterMarginId);
		}
		
		if ($rtCounterMarginRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRtCounterMarginStructure);
			$p["selProduct"]="";
			//$sessObj->createSession("nextPage",$url_afterDelDistMarginStructure.$selection);
		} else {
			$err	= $msg_failDelRtCounterMarginStructure;
		}
		$rtCounterMarginRecDel	=	false;	
	}

	/*
		Edit Selection
	*/
	if ($p["editSelItem"]!="") {
		$singleProduct  = false;
		$editSelItem	= $p["editSelItem"];
		$sessObj->createSession("selRowItem",$editSelItem);
		$editSelItemVal = $sessObj->getValue("selRowItem");
		list($selRetailCounter,$marginPercent,$retCtMarginRateListId,$editRtCounterMarginId) = getRowValues($editSelItemVal);
		//echo "$selRetailCounter,$marginPercent,$retCtMarginRateListId,$editRtCounterMarginId";
		
		# Product Records	
		$productRecords = $rtCounterMarginStructureObj->getRtCounterMarginProductRecs($selRetailCounter, $marginPercent, $retCtMarginRateListId);
		if (sizeof($productRecords)>0 && sizeof($productRecords)<=1) {
			$singleProduct = true;
		}
		$editSelected = true;	
	}

	if ($p["cmdBack"]!="") {
		$editSelItem	= "";
		$editSelected = false;	
	}
	
	# Continue
	if ($p["cmdContinue"] || $p["editId"] || $singleProduct) {		
		$editSelItem   = "";
		$editSelected  = false;
		$editMode      = true;
		
		if ($p["editSelection"]!="") $editSelection = $p["editSelection"];
		else $editSelection = $p["hidSelection"];
		if 	($editSelection=='I') $individualSelected = true;
		else if ($editSelection=='G') $groupSelected = true;
		else if ($singleProduct) { /* If Single Product selection*/
			$editSelection = 'I';
			$individualSelected = true;	
		}
		/* Format =>"$retailCounterId,$marginPercent,$rtctRateListId,$rtCounterMarginId";*/
		$editSelItemVal = $sessObj->getValue("selRowItem");
		list($selRetailCounter,$margin,$retCtMarginRateListId,$editRtCounterMarginId) = getRowValues($editSelItemVal);
		# Product Records	
		$productRecords = $rtCounterMarginStructureObj->getRtCounterMarginProductRecs($selRetailCounter, $margin, $retCtMarginRateListId);
		$editId = $editRtCounterMarginId;
		$selProduct = "";
		if ($singleProduct) {
			$rtCounterMarginRec	= $rtCounterMarginStructureObj->find($editRtCounterMarginId);
			$selProduct		= $rtCounterMarginRec[2];	
			//$margin			= $rtCounterMarginRec[4];	
		}
		$disableField		= "disabled";
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			//$rtCounterMarginId	=	$p["delId_".$i];
			$selRow	=	$p["delId_".$i];
			if ($selRow!="") {	
				list($selRetailCounter, $marginPercent, $retCtMarginRateListId, $editRtCounterMarginId) = getRowValues($selRow);
				# Product Records	
				$productRecords = $rtCounterMarginStructureObj->getRtCounterMarginProductRecs($selRetailCounter, $marginPercent, $retCtMarginRateListId);			
				#del main table
				if (sizeof($productRecords)>0) {
					foreach ($productRecords as $pr) {
						$rtCounterMarginId = $pr[3];
						// Need to check the selected id is link with any other process
						$rtCounterMarginRecDel = $rtCounterMarginStructureObj->deleteRtCounterMarginStructure($rtCounterMarginId);	
					}
				}
			}
		}
		if ($rtCounterMarginRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRtCounterMarginStructure);
			$sessObj->createSession("nextPage",$url_afterDelRtCounterMarginStructure.$selection);
		} else {
			$errDel	=	$msg_failDelRtCounterMarginStructure;
		}
		$rtCounterMarginRecDel	=	false;
	}

	
		if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rtCounterMarginId	=	$p["confirmId"];
			//echo $rtCounterMarginId;

			if ($rtCounterMarginId!="") {
				// Checking the selected fish is link with any other process
				$rtCounterMarginRecConfirm =$rtCounterMarginStructureObj->updateRtMarginconfirm($rtCounterMarginId);
			}

		}
		//die();
		if ($rtCounterMarginRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmRtMargin);
			$sessObj->createSession("nextPage",$url_afterDelRtCounterMarginStructure.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

	}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$rtCounterMarginId= $p["confirmId"];

			if ($rtCounterMarginId!="") {
				#Check any entries exist
				
					$rtCounterMarginRecConfirm = $rtCounterMarginStructureObj->updateRtMarginReleaseconfirm($rtCounterMarginId);
				
			}
		}
		if ($rtCounterMarginRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmRtMargin);
			$sessObj->createSession("nextPage",$url_afterDelRtCounterMarginStructure.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}



	#----------------Rate list--------------------	
	/*
		if ($g["selRateList"]!="") 	$selRateList	= $g["selRateList"];
		else if($p["selRateList"]!="")	$selRateList	= $p["selRateList"];
		else $selRateList = $rtCountMarginRateListObj->latestRateList($selRetailCounter);
	*/			
	#------------------------------------


	## -------------- Pagination Settings I -------------------
	/*
	if ($p["pageNo"]!="") 		$pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="")	$pageNo = $g["pageNo"];
	else if ($p["hidPageNo"]!="")   $pageNo = $p["hidPageNo"];
	else				$pageNo = 1;
	*/
	
	if ($g["pageNo"]!="")		$pageNo = $g["pageNo"];
	else if ($p["hidPageNo"]!="") 	$pageNo = $p["hidPageNo"];	
	else if ($p["pageNo"]!="") 	$pageNo = $p["pageNo"];
	else 				$pageNo = 1;

	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	


	if ($g["rtCounterFilter"]!="") 	$rtCounterFilterId = $g["rtCounterFilter"];
	else if ($p["hidRtCounterFilterId"]!="") $rtCounterFilterId = $p["hidRtCounterFilterId"];
	else $rtCounterFilterId = $p["rtCounterFilter"];	

	if ($g["rtCounterRateListFilter"]!="")  $rtCounterRateListFilterId = $g["rtCounterRateListFilter"];
	else if ($p["hidRtCounterRateListFilterId"]!="") $rtCounterRateListFilterId = $p["hidRtCounterRateListFilterId"];
	else $rtCounterRateListFilterId = $p["rtCounterRateListFilter"];	

	# Resettting offset values
	if ( ($p["hidRtCounterFilterId"]!=$p["rtCounterFilter"]) && !$editMode) {		
		$offset = 0;
		$pageNo = 1;	
		$rtCounterRateListFilterId = "";	
	} else if ( ($p["hidRtCounterRateListFilterId"]!=$p["rtCounterRateListFilter"]) && !$editMode) {
		$offset = 0;
		$pageNo = 1;
	}

	# List all Dist Margin Structure
	$rtCounterMarginResultSetObj = $rtCounterMarginStructureObj->fetchAllPagingRecords($offset, $limit, $rtCounterFilterId, $rtCounterRateListFilterId);
	//getRtCounterMarginPagingRecords($offset, $limit, $rtCounterId, $rtCounterRateListFilterId)
	$rtCounterMarginRecordSize   = $rtCounterMarginResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$fetchRtCtMgnStructResultSetObj = $rtCounterMarginStructureObj->fetchAllRecords($rtCounterFilterId, $rtCounterRateListFilterId);
	$numrows	=  $fetchRtCtMgnStructResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	#Retail Counter Margin Rate List
	//$rtCounterMarginRateListRecords = $rtCountMarginRateListObj->fetchAllRecords();
	
	if ($addMode || $editMode) {
		#List all Retail Counter
		//$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecords('');
		$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');
	}

	# List all RT Counter (Filter result)
	//$retailCounterResultSetFilterObj = $retailCounterMasterObj->fetchAllRecords('');
	$retailCounterResultSetFilterObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');
	# Get Rate List Records based on Rt Counter Id
	if ($rtCounterFilterId) {
		//$rtCounterRateListFilterRecords = $rtCountMarginRateListObj->fetchAllRecords($rtCounterFilterId);

		$rtCounterRateListFilterRecords = $rtCountMarginRateListObj->fetchAllRecordsRetailActive($rtCounterFilterId);
	}

	# List all Combo matrix
	//$productMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();
	if ($addMode) {
		$productRecords = $manageProductObj->fetchAllRecords();	
		# Get Product Category Records
		//$productCategoryRecords	= $productCategoryObj->fetchAllRecords();
		$productCategoryRecords	= $productCategoryObj->fetchAllRecordsActiveCategory();
		# List all Product State Records
		//$productStateRecords = $productStateObj->fetchAllRecords();
		$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();
	}

	# Edit Mode
	/*
	if ($editMode && $individualSelected) {
		if ($singleProduct) $selProduct = $rtCounterMarginRec[2]; // From Edit Section
		else $selProduct = $p["selProduct"];
	}
	*/

	#heading Section
	if ($editMode) $heading	=	$label_editRtCounterMarginStructure;
	else	       $heading	=	$label_addRtCounterMarginStructure;

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	$ON_LOAD_SAJAX 	  = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/RtCounterMarginStructure.js";  //Include JS

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmRtCounterMarginStructure" action="RtCounterMarginStructure.php" method="post">
	<?php 
		if (!$editSelected) {
	?>
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	<tr><td height="10"></td></tr>
	<tr>
		<td height="10" align="center"><a href="RtCounterMarginRateList.php" class="link1" title="Click to Manage Rt Counter Margin Rate List">Retail Counter Margin Rate List</a>
		</td>
	</tr>
	<tr>
		<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		<td></tr>
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
	<tr>
		<? if($editMode){?>
		  <td colspan="2" align="center" nowrap="true" style="padding-left:10px;padding-right:10px;">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('RtCounterMarginStructure.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateRtCounterMarginStructure(document.frmRtCounterMarginStructure);">
<? if ($individualSelected && !$singleProduct) {?>
&nbsp;&nbsp;												<input type="submit" name="cmdSaveEdit" id="cmdSaveEdit" class="button" value=" Save & Edit " onclick="return validateRtCounterMarginStructure(document.frmRtCounterMarginStructure);">
&nbsp;&nbsp;
	<?
		if (sizeof($productRecords)>1) {	
	?>
&nbsp;&nbsp;												<input type="submit" name="cmdDelMargin" id="cmdDelMargin" class="button" value=" Delete Margin " onclick="return valiateDeleteMargin();">
&nbsp;&nbsp;
	<?
		}
	?>
<?php
	}
?>
</td>
		<?} else{?>
			<td  colspan="2" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RtCounterMarginStructure.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " id="cmdAdd" onClick="return validateRtCounterMarginStructure(document.frmRtCounterMarginStructure);">
			</td>
		<?}?>
	</tr>
	<input type="hidden" name="hidDistMarginStructureId" id="hidDistMarginStructureId" value="<?=$editRtCounterMarginId;?>">
					<tr>
					  	<td colspan="2" class="err1" align="center" nowrap style="padding-left:5px;padding-right:5px;" id="divRecExistTxt">
						</td>
					</tr>
					<tr>
					  	<td colspan="2" class="err1" align="center" nowrap style="padding-left:5px;padding-right:5px;" id="divProdRecExistTxt">
						</td>
					</tr>
					<tr>
					<td colspan="2" nowrap style="padding-left:10px;padding-right:10px;">
					<table width="200">
					<tr>
					<td nowrap class="fieldName">*Retail Counter</td>
					<td nowrap>
                                        <select name="selRetailCounter" id="selRetailCounter" onchange="xajax_getRtCounterMgnRateList(document.getElementById('selRetailCounter').value);" <?=$disableField?>>
                                        <option value="">-- Select --</option>
					<?	
					while ($rc=$retailCounterResultSetObj->getRow()) {
						$retailCounterId	= $rc[0];
						$retailCounterCode 	= stripSlash($rc[1]);
						$retailCounterName 	= stripSlash($rc[2]);	
						$selected = "";
						if ($selRetailCounter==$retailCounterId) $selected = "selected";	
					?>
                            		<option value="<?=$retailCounterId?>" <?=$selected?>><?=$retailCounterName?></option>
					<? }?>
					</select>
					</td></tr>
					<tr>
			<?php
				if (!$groupSelected) {
			?>
			<td nowrap class="fieldName">*Product</td>
			<td nowrap>
				<select name="selProduct" id="selProduct" onchange="<? if ($addMode) {?>hideProductSpex(); <? }?>xajax_chkEntryExist(document.getElementById('selRetailCounter').value, document.getElementById('selProduct').value, document.getElementById('retCtMarginRateList').value,'<?=$mode?>', '<?=$editRtCounterMarginId?>');">
                                <option value="">-- Select --</option>
				<?php				
				foreach ($productRecords as $pr) {
					$mproductId	= $pr[0];
					$mproductName	= $pr[2];
					$selected = "";
					if ($selProduct==$mproductId) $selected = "Selected";
				?>
                            	<option value="<?=$mproductId?>" <?=$selected?>><?=$mproductName?></option>
				<? }?>
				</select>
				</td>
				<?php
					} // If not Group selected
				?>
				<?php
					if ($addMode) {
				?>
				<td class="listing-item" id="column0">[OR]</td>
				<td id="column1">
					<fieldset>
					<legend class="listing-item">Product</legend>
					<table  cellspacing="0" cellpadding="0">
						<tr bgcolor="white">
							<td class="fieldName" style="padding-left:2px;padding-right:2px;" nowrap="true">Category</td>
							<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductCategory" id="selProductCategory" onchange="xajax_chkProductRecsExist(document.getElementById('selProductCategory').value, document.getElementById('selProductState').value,  document.getElementById('selProductGroup').value, '<?=$mode?>');">
		<option value=''>-- Select All --</option>";
		<?php
		if (sizeof($productCategoryRecords)>0) {	
			 foreach ($productCategoryRecords as $cr) {
				$categoryId	= $cr[0];
				$categoryName	= stripSlash($cr[1]);
				$selected = "";
				if ($productCategory==$categoryId) $selected = "Selected";
		?>	
		<option value="<?=$categoryId?>" ><?=$categoryName?></option>	
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="fieldName" style="padding-left:2px;padding-right:2px;" nowrap="true">State</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductState" id="selProductState" onChange="xajax_getProductGroupExist(document.getElementById('selProductState').value);xajax_chkProductRecsExist(document.getElementById('selProductCategory').value, document.getElementById('selProductState').value,  document.getElementById('selProductGroup').value, '<?=$mode?>');">
		<option value='0'>-- Select All --</option>";
		<?php
		if (sizeof($productStateRecords)>0) {	
			foreach ($productStateRecords as $cr) {
				$prodStateId	= $cr[0];
				$prodStateName	= stripSlash($cr[1]);
				$selected = "";
				if ($productState==$prodStateId) $selected = "Selected";
		?>	
		<option value="<?=$prodStateId?>"><?=$prodStateName?></option>
		<?php
			}
		}
		?>
		</select>
	</td>
	<td class="fieldName" style="padding-left:2px;padding-right:2px;" nowrap="true">Group</td>
	<td style="padding-left:2px;padding-right:2px;">
		<select name="selProductGroup" id="selProductGroup" onchange="xajax_chkProductRecsExist(document.getElementById('selProductCategory').value, document.getElementById('selProductState').value,  document.getElementById('selProductGroup').value, '<?=$mode?>');">
		<option value='0'>-- Select --</option>
		</select>
	</td>
	</tr>				
	</table>
	</fieldset>
				</td>
	<?php
		}
	?>
	</tr>				
	<tr>
		<td class="fieldName" nowrap>Margin</td>
		<td class="listing-item">
			<input type="text" name="margin" id="margin" size="5" value="<?=$margin;?>" style="text-align:right;" >&nbsp;%</td>
	</tr>	
	<tr>
		<TD>
			<?	
				/*if ($addMode) $rateListId = $selRateList;
				else $rateListId = $retCtMarginRateListId;
				*/
			?>
			<input type="hidden" name="retCtMarginRateList" id="retCtMarginRateList" value="<?=$retCtMarginRateListId?>">
		</TD>
	</tr>	
	<!--tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="retCtMarginRateList">
                        <option value="">-- Select --</option>
			<?
			/*
			foreach ($rtCounterMarginRateListRecords as $prl) {
				$ingredientRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$array			=	explode("-",$prl[2]);
				$startDate		=	$array[2]."/".$array[1]."/".$array[0];
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				if ($addMode) $rateListId = $selRateList;
				else $rateListId = $retCtMarginRateListId;
				$selected = "";
				if ($rateListId==$ingredientRateListId) $selected = "Selected";
				*/
			?>
                      <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      <? //}?>
                     </select>
			</td>
		</tr-->
			</table>
			</td>
		  </td>
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center" nowrap="true" style="padding-left:10px;padding-right:10px;">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RtCounterMarginStructure.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateRtCounterMarginStructure(document.frmRtCounterMarginStructure);">
				<? if ($individualSelected && !$singleProduct) {?>
&nbsp;&nbsp;												<input type="submit" name="cmdSaveEdit" id="cmdSaveEdit" class="button" value=" Save & Edit " onclick="return validateRtCounterMarginStructure(document.frmRtCounterMarginStructure);">
&nbsp;&nbsp;
	<?
		if (sizeof($productRecords)>1) {	
	?>
&nbsp;&nbsp;												<input type="submit" name="cmdDelMargin" id="cmdDelMargin" class="button" value=" Delete Margin " onclick="return valiateDeleteMargin();">
&nbsp;&nbsp;
	<?
		}
	?>
<?
}
?>		
					

				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RtCounterMarginStructure.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" class="button" id="cmdAdd1" value=" Add " onClick="return validateRtCounterMarginStructure(document.frmRtCounterMarginStructure);">							
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
			</table></td>
			</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
				<td height="10" align="center">
					<table width="200" border="0">
					<tr>
					<td class="fieldName" nowrap>Rate List </td>
					<td>
					<select name="selRateList" id="selRateList" onchange="this.form.submit();">
					<option value="">-- Select --</option>
					<?
					/*
					foreach ($rtCounterMarginRateListRecords as $prl) {
						$ingredientRateListId	=	$prl[0];
						$rateListName		=	stripSlash($prl[1]);
						$array			=	explode("-",$prl[2]);
						$startDate		=	$array[2]."/".$array[1]."/".$array[0];
						$displayRateList = $rateListName."&nbsp;(".$startDate.")";
						$selected = "";
						if($selRateList==$ingredientRateListId) $selected = "Selected";
					*/
					?>
					<option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?></option>
					<?php
						//  }
					?>
					</select></td>
					<? if($add==true){?>
						<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Margin List" onclick="this.form.action='RtCounterMarginRateList.php?mode=AddNew'"></td>
					<? }?>
					</tr>
					</table>
				</td>
			</tr>-->
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true" >&nbsp;Retail Counter Margin Structure  </td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
	<table cellpadding="0" cellspacing="0">
        <tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item" nowrap="true">Retail Counter:&nbsp;</td>
                <td>
		<select name="rtCounterFilter" id="rtCounterFilter" onchange="this.form.submit();">
			<option value="">-- Select --</option>
			<?php	
				while ($rc=$retailCounterResultSetFilterObj->getRow()) {
					$retailCounterId	= $rc[0];
					$retailCounterCode 	= stripSlash($rc[1]);
					$retailCounterName 	= stripSlash($rc[2]);	
					$selected = "";
					if ($rtCounterFilterId==$retailCounterId) $selected = "selected";	
			?>
                        <option value="<?=$retailCounterId?>" <?=$selected?>><?=$retailCounterName?></option>
			<?php
				 }
			?>
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
	   <td class="listing-item" nowrap="true">Rate List:</td>
	<td>
		<select name="rtCounterRateListFilter" id="rtCounterRateListFilter" onchange="this.form.submit();">
                       <option value="">-- Select --</option>
			<?php			
			foreach ($rtCounterRateListFilterRecords as $rtcrl) {
				$rtCtRateListId	=	$rtcrl[0];
				$rateListName		=	stripSlash($rtcrl[1]);
				$startDate		= dateFormat($rtcrl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if ($rtCtRateListId==$rtCounterRateListFilterId) $selected = "Selected";	
			?>
                      <option value="<?=$rtCtRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      <? }?>
                      </select>
	</td>		
          <td>&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?>
												<input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rtCounterMarginRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRtCounterMarginStructure.php?rtCounterFilter=<?=$rtCounterFilterId?>&rtCounterRateListFilter=<?=$rtCounterRateListFilterId?>',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px;padding-right:10px;" >
		<table cellpadding="2"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($rtCounterMarginRecordSize) {
			$i	=	0;
		?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RtCounterMarginStructure.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId&rtCounterRateListFilter=$rtCounterRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RtCounterMarginStructure.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId&rtCounterRateListFilter=$rtCounterRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RtCounterMarginStructure.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId&rtCounterRateListFilter=$rtCounterRateListFilterId\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" align="center">
	<td width="20">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
	</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Retail Counter</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Margin<br>(%)</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Product</td>	
	<? if($edit==true){?>
		<td class="listing-head"></td>
	<? }?>
	<? if($confirm==true){?>
		<td class="listing-head">&nbsp;</td>
	<? }?>
	</tr>
	<?
		$prevRetailCounterId = "";
		$selCriteria ="";
		while (($rcm=$rtCounterMarginResultSetObj->getRow())) {
			$i++;
			$rtCounterMarginId 	= $rcm[0];
			$retailCounterId	= $rcm[1];
			$retailCounterName	= "";
			if ($prevRetailCounterId!=$retailCounterId) {
				$retailCounterName = $rcm[4];
			}
			//$productName	= $rcm[5];
			$marginPercent	= $rcm[5];	
			$rtctRateListId = $rcm[3];
			$active = $rcm[6];
			$selCriteria = "";
			# Get Product Records	
			$getProductRecs = $rtCounterMarginStructureObj->getRtCounterMarginProductRecs($retailCounterId, $marginPercent, $rtctRateListId);
			# Format 
			$selCriteria = "$retailCounterId,$marginPercent,$rtctRateListId,$rtCounterMarginId";	
	?>
	<tr  bgcolor="WHITE">
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$selCriteria;?>" class="chkBox">
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$retailCounterName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$marginPercent;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
						<?
							$numLine = 6;
							if (sizeof($getProductRecs)>0) {
								$nextRec	=	0;
								$k=0;
								$cityName = "";
								foreach ($getProductRecs as $cR) {		
									$productCode = $cR[1];
									$nextRec++;
									if ($nextRec>1) echo ",&nbsp;";
									echo $productCode;
									if($nextRec%$numLine==0) { 
										echo "<br>";
									}
								}
							}
						?>		
		</td>		
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active==0){ ?>
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,'<?=$selCriteria;?>','editSelItem');this.form.action='RtCounterMarginStructure.php';" >
			<? } ?>
			<!--<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?//$rtCounterMarginId;?>,'editId');this.form.action='RtCounterMarginStructure.php';" >-->
		</td>
	<? }?>
	<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> <?php }?> width="45" align="center" >
		<?php if ($active==0){ ?>
		<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$rtCounterMarginId;?>,'confirmId');"  >
		<?php } else if ($active==1){?>
		<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$rtCounterMarginId;?>,'confirmId');"  >
		<?php }?>
	</td>
	<? }?>
	


	</tr>
	<?
		$prevRetailCounterId = $retailCounterId;
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" id="editId" value="<?=$editId?>">
		<input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RtCounterMarginStructure.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId&rtCounterRateListFilter=$rtCounterRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RtCounterMarginStructure.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId&rtCounterRateListFilter=$rtCounterRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RtCounterMarginStructure.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId&rtCounterRateListFilter=$rtCounterRateListFilterId\"  class=\"link1\">>></a> ";
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
		<td colspan="3" height="5"></td>
	</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rtCounterMarginRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRtCounterMarginStructure.php?rtCounterFilter=<?=$rtCounterFilterId?>&rtCounterRateListFilter=<?=$rtCounterRateListFilterId?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
		<!-- Form fields end   -->	
		</td>
		</tr>		
		<tr>
			<td height="10">				
			</td>
		</tr>
		<tr>
			<td height="10" align="center"><a href="RtCounterMarginRateList.php" class="link1" title="Click to Manage Rt Counter Margin Rate List">Retail Counter Margin Rate List</a>
			</td>
		</tr>	
	<input type='hidden' name="singleProdEnabled" id="singleProdEnabled" value=''>
	<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
	</table>
	<?php
		} // If not Edit Selected
	?>
	<input type="hidden" name="hidRtCounterFilterId" value="<?=$rtCounterFilterId?>">	
	<input type="hidden" name="hidRtCounterRateListFilterId" value="<?=$rtCounterRateListFilterId?>">
	<input type="hidden" name="hidPageNo" value="<?=$pageNo?>"> 
	<input type="hidden" name="editSelItem" id="editSelItem" value="<?=$editSelItem?>">
	<input type="hidden" name="hidSelection" id="hidSelection" value="<?=$editSelection?>">
	<?
	// Edit Select
	if ($editSelected) {	
	?>
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	<tr><td height="10"></td></tr>
	<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
				
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Edit Selection  </td>
<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
	<table cellpadding="0" cellspacing="0">
        <tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item">&nbsp;</td>
                <td>
		 
                 </td>
	   <td class="listing-item">&nbsp;</td>
	   <td class="listing-item"></td>
	<td>
		
	</td>		
          <td>&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>

								
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>		
		<tr>
			<td  colspan="2" align="center">
				<input type="submit" name="cmdBack" class="button" value=" Go Back " >&nbsp;&nbsp;
				<input type="submit" name="cmdContinue" class="button" value=" Continue " onClick="return validateEditSelection();"> &nbsp;&nbsp;
			</td>
		</tr>	
		<tr><TD height="20"></TD></tr>		
		<tr>
			<td width="1" ></td>
			<td colspan="2" style="padding-left:5px;padding-right:5px;">
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<TD align="center">
						<table>
							<TR>
								<TD class="listing-item">
									<INPUT type="radio" name="editSelection" id="editSelection1"  class="chkBox" value="I">&nbsp;Individual
								</TD>
								<TD class="listing-item">
									<INPUT type="radio" name="editSelection" id="editSelection2" class="chkBox" value="G">&nbsp;Group
								</TD>
							</TR>
						</table>
					</TD>
				</tr>
				</table>
			</td>
	</tr>
	<tr><TD height="20"></TD></tr>
	<tr>
			<td  colspan="2" align="center">
				<input type="submit" name="cmdBack" onClick="this.form.action='RtCounterMarginStructure.php';" class="button" value=" Go Back " >&nbsp;&nbsp;
				<input type="submit" name="cmdContinue" class="button" value=" Continue " onClick="return validateEditSelection();"> &nbsp;&nbsp;
			</td>
		</tr>	
	
								<tr >	
									<td colspan="3">
																			</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>
	</table>
	<? 
	}
	?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
