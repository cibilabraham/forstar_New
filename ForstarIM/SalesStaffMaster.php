<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;

	$selection 	= "?pageNo=".$p["pageNo"];
	$cUserId	= $sessObj->getValue("userId");

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

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
		
	if ($p["cmdCancel"]!="") $addMode = false;

	// Value setting
	if ($p["code"]!="") $code = $p["code"];
	if ($p["salesStaffName"]!="") $salesStaffName = $p["salesStaffName"];
	if ($p["designation"]!="") $designation = $p["designation"];
	if ($p["address"]!="") $address = $p["address"];
	if ($p["pinCode"]!="") $pinCode = $p["pinCode"];
	if ($p["telNo"]!="") $telNo = $p["telNo"];
	if ($p["mobNo"]!="") $mobNo = $p["mobNo"];
	if ($p["area"]!="") $selAreaId = $p["area"];
	//$opArea		= ($p["opArea"]!="")?$p["opArea"]:array(0);
	//print_r($opArea);
	# Add a Record
	if ($p["cmdAdd"]!="") {
		//$code		= addSlash(trim($p["code"]));
		$code		= "SS_".autoGenNum();	// SS - sales staff
		$salesStaffName	= addSlash(trim($p["salesStaffName"]));		
		$address	= addSlash(trim($p["address"]));
		$selStateId	= $p["state"];
		$selCityId	= $p["city"];
		$selArea	= $p["area"];	// Multiple area
		$pinCode	= addSlash(trim($p["pinCode"]));
		$telNo		= addSlash(trim($p["telNo"]));
		$mobNo		= addSlash(trim($p["mobNo"]));	

		$designation	= addSlash(trim($p["designation"]));
		$opState	= $p["opState"];
		$opCity		= $p["opCity"];
		$opArea		= ($p["opArea"]!="")?$p["opArea"]:array(0);
		
		
				
		# Check Duplicate Entry
		$duplicateEntry = $salesStaffMasterObj->chkDuplicateEntry($salesStaffName, $selSalesStaffId);

		if ($code!="" && $salesStaffName!="" && !$duplicateEntry) {
			$salesStaffRecIns = $salesStaffMasterObj->addSalesStaff($code, $salesStaffName, $address, $selStateId, $selCityId, $selArea, $pinCode, $telNo, $mobNo, $cUserId, $opState, $opCity, $opArea, $designation);

			if ($salesStaffRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddSalesStaff);
				$sessObj->createSession("nextPage",$url_afterAddSalesStaff.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSalesStaff;
			}
			$salesStaffRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddSalesStaff;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$salesStaffRec		= $salesStaffMasterObj->find($editId);
		$editSalesStaffId	= $salesStaffRec[0];
		$code			= stripSlash($salesStaffRec[1]);
		$salesStaffName		= stripSlash($salesStaffRec[2]);		
		$address		= stripSlash($salesStaffRec[3]);
		//$selStateId		= $salesStaffRec[4];
		//$selCityId		= $salesStaffRec[5];
		$pinCode		= stripSlash($salesStaffRec[6]);
		$telNo			= stripSlash($salesStaffRec[7]);		
		$mobNo			= stripSlash($salesStaffRec[8]);
		$designation		= stripSlash($salesStaffRec[9]);
		$selAreaId		= $salesStaffRec[10];
		//$opStateId		= $salesStaffRec[11];
	}

	# Update 
	if ($p["cmdSaveChange"]!="") {
		
		$salesStaffId	= $p["hidSalesStaffId"];
		$salesStaffName	= addSlash(trim($p["salesStaffName"]));		
		$address	= addSlash(trim($p["address"]));
		$selStateId	= $p["state"];
		$selCityId	= $p["city"];
		$selArea	= $p["area"];	
		$pinCode	= addSlash(trim($p["pinCode"]));
		$telNo		= addSlash(trim($p["telNo"]));
		$mobNo		= addSlash(trim($p["mobNo"]));			
		
		$designation	= addSlash(trim($p["designation"]));
		$opState	= $p["opState"];
		$opCity		= $p["opCity"];
		$opArea		= ($p["opArea"]!="")?$p["opArea"]:array(0);

		# Check Duplicate Entry
		$duplicateEntry = $salesStaffMasterObj->chkDuplicateEntry($salesStaffName, $salesStaffId);

		if ($salesStaffId!="" && $salesStaffName!="" && !$duplicateEntry) {
			$salesStaffRecUptd = $salesStaffMasterObj->updateSalesStaff($salesStaffId, $salesStaffName, $address, $selStateId, $selCityId, $selArea, $pinCode, $telNo, $mobNo, $opState, $opCity, $opArea, $designation);
		}
	
		if ($salesStaffRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSalesStaffUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSalesStaff.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSalesStaffUpdate;
		}
		$salesStaffRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$salesStaffId	=	$p["delId_".$i];

			if ($salesStaffId!="") {
				# Check Sales staff used any where
				$salesStaffInUse = $salesStaffMasterObj->salesStaffInUse($salesStaffId);
				if (!$salesStaffInUse) {
					# Need to check the selected Category is link with any other process
					# Delete Working Area
					$salesStaffWorkingAreaRecDel = $salesStaffMasterObj->deleteWorkingArea($salesStaffId);
					# Delete main Rec
					$salesStaffRecDel = $salesStaffMasterObj->deleteSalesStaff($salesStaffId);
				}
			}
		}
		if ($salesStaffRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSalesStaff);
			$sessObj->createSession("nextPage",$url_afterDelSalesStaff.$selection);
		} else {
			$errDel	=	$msg_failDelSalesStaff;
		}
		$salesStaffRecDel	=	false;
	}
	



if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$salesStaffId	=	$p["confirmId"];
			if ($salesStaffId!="") {
				// Checking the selected fish is link with any other process
				$salesRecConfirm =  $salesStaffMasterObj->updateSalesStaffconfirm($salesStaffId);
			}

		}
		if ($salesRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmsales);
			$sessObj->createSession("nextPage",$url_afterDelSalesStaff.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$salesStaffId = $p["confirmId"];
			if ($salesStaffId!="") {
				#Check any entries exist
				
					$salesRecConfirm =  $salesStaffMasterObj->updateSalesStaffReleaseconfirm($salesStaffId);
				
			}
		}
		if ($salesRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmsales);
			$sessObj->createSession("nextPage",$url_afterDelSalesStaff.$selection);
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

	# List all Sales Staff
	$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllPagingRecords($offset, $limit);
	$salesStaffRecordSize	 = $salesStaffResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	//$fetchAllsalesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecords();
	$fetchAllsalesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecordsActiveStaff();

	$numrows	=  $fetchAllsalesStaffResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	#List all State
	if ($addMode || $editMode ) {
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();

		//$opStateResultSetObj = $stateMasterObj->fetchAllRecords();
		$opStateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
		
	}

	if ($p["editSelectionChange"]==1 && $p["state"]=="") $selStateId = $salesStaffRec[4];
	else $selStateId = $p["state"];

	if ($p["editSelectionChange"]==1 && $p["opState"]=="") $opStateId = $salesStaffRec[11];
	else $opStateId = $p["opState"];	
		
	if ($selStateId!="") {
		$cityRecords = $cityMasterObj->filterCityRecs($selStateId);		
	}

	if ($opStateId) {
		$opCityRecords = $cityMasterObj->filterCityRecs($opStateId);
	}	

	if ($p["editSelectionChange"]==1 && $p["city"]=="") $selCityId = $salesStaffRec[5];
	else $selCityId = $p["city"];
	
	if ($p["editSelectionChange"]==1 && $p["opCity"]=="") $opCityId = $salesStaffRec[12];
	else $opCityId = $p["opCity"];
		

	if ($selCityId!="" || $opCityId) {
		$areaRecords = $areaMasterObj->filterAreaRecs($selCityId);
		if ($addMode) {
			
			$opAreaRecords = $areaMasterObj->filterAreaRecs($opCityId);
		}
		else if ($editMode) {
			$opAreaRecords = $salesStaffMasterObj->fetchSelectedAreaRecords($editId, $opCityId);
		}
	}

	if ($editMode)	$heading = $label_editSalesStaff;
	else 		$heading = $label_addSalesStaff;
	
	$ON_LOAD_PRINT_JS	= "libjs/SalesStaffMaster.js";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSalesStaffMaster" action="SalesStaffMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="80%" >
	<tr><TD height="10"></TD></tr>
	<tr>
		<td height="10" align="center">
			<a href="StateMaster.php" class="link1">State</a>&nbsp;&nbsp;<a href="CityMaster.php" class="link1">City</a>&nbsp;&nbsp;<a href="AreaMaster.php" class="link1">Area</a>
		</td>
	</tr>
		<tr>
			<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		<td>		</tr>
		<?
			if( $editMode || $addMode)
			{
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
			<td colspan="2" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesStaffMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSalesStaffMaster(document.frmSalesStaffMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesStaffMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSalesStaffMaster(document.frmSalesStaffMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidSalesStaffId" value="<?=$editSalesStaffId;?>">
	<!--<tr>
		<td class="fieldName" nowrap >*Code</td>
		<td><input type="text" name="code" size="10" value="<?=$code;?>" /></td>
	</tr>-->
	<tr>
		<TD colspan="2">
			<table>
				<TR>
					<TD>
						<table>
							<tr>
							<td class="fieldName" nowrap >*Name </td>
							<td><input type="text" name="salesStaffName" size="20" value="<?=$salesStaffName;?>"></td>
						</tr>	
						<tr>
							<td class="fieldName" nowrap >*Designation </td>
							<td>
								<input type="text" name="designation" id="designation" size="20" value="<?=$designation;?>">
							</td>
						</tr>
						<tr>
							<td class="fieldName" nowrap >Residential Address</td>
							<td><textarea name="address"><?=$address;?></textarea></td>
						</tr>	
						</table>
					</TD>
					<TD valign="top">
						<table>
							<tr>
								<td class="fieldName" nowrap >Pin Code</td>
								<td><input type="text" name="pinCode" value="<?=$pinCode;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Home Tel No</td>
								<td><input type="text" name="telNo" value="<?=$telNo;?>"></td>
							</tr>	
							<tr>
								<td class="fieldName" nowrap >Mob No</td>
								<td><input type="text" name="mobNo" value="<?=$mobNo;?>"></td>
							</tr>
						</table>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>
	<!--tr>
		<td class="fieldName" nowrap >*Name </td>
		<td><input type="text" name="salesStaffName" size="20" value="<?=$salesStaffName;?>"></td>
	</tr>	
	<tr>
		<td class="fieldName" nowrap >*Designation </td>
		<td>
			<input type="text" name="designation" id="designation" size="20" value="<?=$designation;?>">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Residential Address</td>
		<td><textarea name="address"><?=$address;?></textarea></td>
	</tr-->	
	<!--tr>
		<td class="fieldName" nowrap >*State</td>
		<td>
			<select name="state" id="state" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">			
			<option value="">-- Select --</option>
			<?
			/*
			while (($sr=$stateResultSetObj->getRow())) {
				$stateId = $sr[0];
				$stateCode	= stripSlash($sr[1]);
				$stateName	= stripSlash($sr[2]);	
				$selected = "";
				if ($selStateId==$stateId) $selected = "Selected";
			*/			
			?>
			<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
			<? //}?>
			</select>
		</td>
  	</tr>
	<tr>
		<td class="fieldName" nowrap >*City</td>
		<td>
			<select name="city" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">
			<option value="">--Select--</option>
			<?
			/*
			foreach ($cityRecords as $cr) {			
				$cityId 	= $cr[0];
				$cityCode	= stripSlash($cr[1]);
				$cityName	= stripSlash($cr[2]);				
				$selected = "";
				if ($selCityId==$cityId) $selected = "Selected"; 
			*/
			?>
			<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
			<? //}?>
			</select>
		</td>
  	</tr>	
	<tr>
		<td class="fieldName" nowrap >*Working Area</td>
		<td>
			<select name="area[]" multiple="true" size="6" id="area">
			<option value="">-- Select --</option>
			<?
			/*
			foreach ($areaRecords as $ar) {			
				$areaId 	= $ar[0];
				$areaCode	= stripSlash($ar[1]);
				$areaName	= stripSlash($ar[2]);		
				$selAreaId	= $ar[4];
				$selected = "";
				if ($selAreaId==$areaId) $selected = "Selected"; 
			*/
			?>
			<option value="<?=$areaId?>" <?=$selected?>><?=$areaName?></option>
			<? //}?>
			</select>
		</td>
  	</tr-->
	<!--tr>
		<td class="fieldName" nowrap >Pin Code</td>
		<td><input type="text" name="pinCode" value="<?=$pinCode;?>"></td>
  	</tr>
	<tr>
		<td class="fieldName" nowrap >Home Tel No</td>
		<td><input type="text" name="telNo" value="<?=$telNo;?>"></td>
  	</tr>	
	<tr>
		<td class="fieldName" nowrap >Mob No</td>
		<td><input type="text" name="mobNo" value="<?=$mobNo;?>"></td>
  	</tr-->	
	<tr>
		<TD colspan="4" align="center">
			<table>
				<TR>
					<TD valign="top">
						<fieldset>
							<legend class="listing-item">Staff Location</legend>
							<table>
								<tr>
		<td class="fieldName" nowrap >*State</td>
		<td>
			<select name="state" id="state" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">			
			<option value="">-- Select --</option>
			<?
			while ($sr=$stateResultSetObj->getRow()) {
				$stateId = $sr[0];
				$stateCode	= stripSlash($sr[1]);
				$stateName	= stripSlash($sr[2]);	
				$selected = "";
				if ($selStateId==$stateId) $selected = "Selected";			
			?>
			<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
			<? }?>
			</select>
		</td>
  	</tr>
	<tr>
		<td class="fieldName" nowrap >*City</td>
		<td>
			<select name="city" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">
			<option value="">--Select--</option>
			<?
			foreach ($cityRecords as $cr) {			
				$cityId 	= $cr[0];
				$cityCode	= stripSlash($cr[1]);
				$cityName	= stripSlash($cr[2]);				
				$selected = "";
				if ($selCityId==$cityId) $selected = "Selected"; 
			?>
			<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
			<? }?>
			</select>
		</td>
  	</tr>	
	<tr>
		<td class="fieldName" nowrap >*Area</td>
		<td>
			<select name="area" id="area">
			<option value="">-- Select --</option>
			<?
			foreach ($areaRecords as $ar) {			
				$areaId 	= $ar[0];
				$areaCode	= stripSlash($ar[1]);
				$areaName	= stripSlash($ar[2]);		
				//$selAreaId	= $ar[4];
				$selected = "";
				if ($selAreaId==$areaId) $selected = "Selected"; 
			?>
			<option value="<?=$areaId?>" <?=$selected?>><?=$areaName?></option>
			<? }?>
			</select>
		</td>
  	</tr>
							</table>	
						</fieldset>		
					</TD>
					<td>
						<fieldset>
							<legend class="listing-item">Operational Details</legend>
							<table>
								<tr>
		<td class="fieldName" nowrap >*State</td>
		<td>
			<select name="opState" id="opState" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">			
			<option value="">-- Select --</option>
			<?
			while (($sr=$opStateResultSetObj->getRow())) {
				$stateId = $sr[0];
				$stateCode	= stripSlash($sr[1]);
				$stateName	= stripSlash($sr[2]);	
				$selected = "";
				if ($opStateId==$stateId) $selected = "Selected";			
			?>
			<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
			<? }?>
			</select>
		</td>
  	</tr>
	<tr>
		<td class="fieldName" nowrap >City</td>
		<td>
			<select name="opCity" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">
			<option value="0">--Select All--</option>
			<?
			foreach ($opCityRecords as $cr) {			
				$cityId 	= $cr[0];
				$cityCode	= stripSlash($cr[1]);
				$cityName	= stripSlash($cr[2]);				
				$selected = "";
				if ($opCityId==$cityId) $selected = "Selected"; 
			?>
			<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
			<? }?>
			</select>
		</td>
  	</tr>	
	<tr>
		<td class="fieldName" nowrap >Area</td>
		<td>
			<select name="opArea[]" multiple="true" size="6" id="opArea">
			<option value="0">-- Select All --</option>
			<?
			foreach ($opAreaRecords as $ar) {			
				$areaId 	= $ar[0];
				$areaCode	= stripSlash($ar[1]);
				$areaName	= stripSlash($ar[2]);		
				$opAreaId	= $ar[4];
				$selected = "";
				if ($opAreaId==$areaId) $selected = "Selected"; 
			?>
			<option value="<?=$areaId?>" <?=$selected?>><?=$areaName?></option>
			<? }?>
			</select>
		</td>
  	</tr>
							</table>
						</fieldset>		
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
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesStaffMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSalesStaffMaster(document.frmSalesStaffMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SalesStaffMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSalesStaffMaster(document.frmSalesStaffMaster);">	
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
			</td>
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
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Sales Staff Master</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$salesStaffRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesStaffMaster.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($salesStaffRecordSize) {
			$i = 0;
	?>

	<? if($maxpage>1){ ?>
		<tr bgcolor="#FFFFFF">
		<td colspan="10" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SalesStaffMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SalesStaffMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SalesStaffMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td width="20" rowspan="2"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Name</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Designation</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="3">Staff Location</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="3">Operational Details</td>
		<? if($edit==true){?>
			<td class="listing-head" rowspan="2"></td>
		<? }?>
		<? if($confirm==true){?>
			<td class="listing-head" rowspan="2"></td>
		<? }?>
	</tr>
	<tr  bgcolor="#f2f2f2" align="center">				
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">State</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Area</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">State</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Area</td>		
	</tr>
	<?	
	$prevStateId	= "";
	$prevCityId 	= "";
	while ($ssr=$salesStaffResultSetObj->getRow()) {
		$i++;
		$salesStaffId	 = $ssr[0];
		$salesStaffCode = stripSlash($ssr[1]);
		$salesStaffName = stripSlash($ssr[2]);	
		$stateId	= $ssr[4];		
		$stateName 	= $ssr[9];
		$cityId		= $ssr[5];		
		$cityName 	= $ssr[10];
		$areaId		= $ssr[11];
		$areaRec	=	$areaMasterObj->find($areaId);		
		$areaName	=	stripSlash($areaRec[2]);
		$opSelStateId	= $ssr[12];
		$stateRec	= $stateMasterObj->find($opSelStateId);		
		$opStateName	= stripSlash($stateRec[2]);
		$opSelCityId	= $ssr[13];	
		$opCityName 	= "";
		if ($opSelCityId==0 && $opSelCityId!="") {
			$opCityName	= "All";	
		} else {
			$cityRec	=	$cityMasterObj->find($opSelCityId);		
			$opCityName	=	stripSlash($cityRec[2]);
		}

		$staffDesignation	= $ssr[14];
		//$areaRec	=	$areaMasterObj->find($editId);		
		//$areaName	=	stripSlash($areaRec[2]);
		# get Working Area Records
		$selAreaRecords = $salesStaffMasterObj->getWorkingAreaRecords($salesStaffId);
		$active=$ssr[15];
		$existingrecords=$ssr[16];
	?>
	<tr   <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } else {?>bgcolor="WHITE"<?php }?> >
		<td width="20">
		<?php 
		if($existingrecords==0){?>
		  <input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$salesStaffId;?>" class="chkBox"></td>		
		<?php 
		}
		?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$salesStaffName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$staffDesignation;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$stateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$cityName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$areaName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$opStateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$opCityName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
			<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($selAreaRecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach ($selAreaRecords as $areaR) {
							$j++;
							$areaName = "";
							if ($areaR[0]==0) {
								$areaName = "All";
							} else $areaName = $areaR[1];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$areaName?></td>
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
			<td class="listing-item" width="60" align="center"> <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$salesStaffId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SalesStaffMaster.php';"><? } ?></td>
		<? }?>
		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$salesStaffId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$salesStaffId;?>,'confirmId');" >
			<?php 
			//}
			}?>
			<? }?>
			
	</tr>
	<?
		$prevStateId	= $stateId;
		$prevCityId	= $cityId;
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="10" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SalesStaffMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SalesStaffMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SalesStaffMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr bgcolor="white">
		<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$salesStaffRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesStaffMaster.php',700,600);"><? }?></td>
											</tr>
										</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
		<!--<tr><TD height="10"></TD></tr>-->
	<tr>
		<td height="10" align="center">
			<a href="StateMaster.php" class="link1">State</a>&nbsp;&nbsp;<a href="CityMaster.php" class="link1">City</a>&nbsp;&nbsp;<a href="AreaMaster.php" class="link1">Area</a>
		</td>
	</tr>
	</table>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>