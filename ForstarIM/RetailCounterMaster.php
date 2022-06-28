<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
	$selection 	= "?pageNo=".$p["pageNo"]."&selFilter=".$p["selFilter"];

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

	# Value Re-setting
	if ($p["code"]!="") $code = $p["code"];
	if ($p["retailCounterName"]!="") $retailCounterName = $p["retailCounterName"];
	if ($p["contactPerson"]!="") $contactPerson = $p["contactPerson"];
	if ($p["address"]!="") $address = $p["address"];
	if ($p["distributor"]!="") $selDistributorId = $p["distributor"];
	if ($p["salesStaff"]!="") $selSalesStaffId = $p["salesStaff"];

		
	# Add a Record
	if ($p["cmdAdd"]!="") {
		//$code		=	addSlash(trim($p["code"]));
		$code		= "RC_".autoGenNum();  // Retail Counter
		$retailCounterName	=	addSlash(trim($p["retailCounterName"]));
		$contactPerson	=	addSlash(trim($p["contactPerson"]));
		$address	=	addSlash(trim($p["address"]));
		$selStateId	=	$p["state"];
		$selCityId	=	$p["city"];
		$pinCode	=	addSlash(trim($p["pinCode"]));
		$telNo		=	addSlash(trim($p["telNo"]));
		$faxNo		=	addSlash(trim($p["faxNo"]));
		$mobNo		=	addSlash(trim($p["mobNo"]));
		$vatNo		=	addSlash(trim($p["vatNo"]));
		$tinNo		=	addSlash(trim($p["tinNo"]));
		$cstNo		=	addSlash(trim($p["cstNo"]));
		$selDistributorId 	= $p["distributor"];
		$selSalesStaffId 	= $p["salesStaff"];
		$area			= $p["area"];
		$selRtCtCateogry	= $p["selRtCtCateogry"];
		/*
		$disCharge	= $p["disCharge"];
		$disType	= $p["disType"]; // M-month or D-Date
		$displayLogStatus = "";
		if ($disType=='D') {
			$selectFrom	= mysqlDateFormat($p["selectFrom"]);
			$selectTill	= mysqlDateFormat($p["selectTill"]);
			$displayLogStatus = "$disType-$selectFrom:$selectTill";
		} else {
			$selectFrom	= "";
			$selectTill	= "";
			$displayLogStatus = "$disType";
		}
		, $disCharge, $disType, $selectFrom, $selectTill, $displayLogStatus
		*/	
				
		# Check Duplicate Entry
		$duplicateEntry = $retailCounterMasterObj->chkDuplicateEntry($retailCounterName, $cRtCtId);
			
		if ($code!="" && $retailCounterName!="" && !$duplicateEntry) {
			$retailCounterRecIns = $retailCounterMasterObj->addRetailCounter($code, $retailCounterName, $contactPerson, $address, $selStateId, $selCityId, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo, $selDistributorId, $selSalesStaffId, $area, $userId, $selRtCtCateogry);

			if ($retailCounterRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddRetailCounter);
				$sessObj->createSession("nextPage",$url_afterAddRetailCounter.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddRetailCounter;
			}
			$retailCounterRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddRetailCounter;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$retailCounterRec		= $retailCounterMasterObj->find($editId);
		$editRetailCounterId	= $retailCounterRec[0];
		$code			= stripSlash($retailCounterRec[1]);
		$retailCounterName	= stripSlash($retailCounterRec[2]);
		$contactPerson		= stripSlash($retailCounterRec[3]);
		$address		= stripSlash($retailCounterRec[4]);
		//$selStateId		= $retailCounterRec[5];
		//$selCityId		= $retailCounterRec[6];
		$pinCode		= stripSlash($retailCounterRec[7]);
		$telNo			= stripSlash($retailCounterRec[8]);
		$faxNo			= stripSlash($retailCounterRec[9]);
		$mobNo			= stripSlash($retailCounterRec[10]);
		$vatNo			= stripSlash($retailCounterRec[11]);
		$tinNo			= stripSlash($retailCounterRec[12]);
		$cstNo			= stripSlash($retailCounterRec[13]);
		$selDistributorId	= $retailCounterRec[14];
		$selSalesStaffId	= $retailCounterRec[15];
		$selRtCtCateogry	= $retailCounterRec[16];
		/*
		$disCharge	= $retailCounterRec[16];
		$disType	= $retailCounterRec[17]; // M-month or D-Date
		if ($disType=='D') {
			$selectFrom = dateFormat($retailCounterRec[18]);
			$selectTill = dateFormat($retailCounterRec[19]);
		}
		$cLogStatus	= $retailCounterRec[20];
		*/	
	}


	# Update a record
	if ($p["cmdSaveChange"]!="") {
		$retailCounterId = $p["hidRetailCounterId"];
		//$code		= addSlash(trim($p["code"]));
		$retailCounterName = addSlash(trim($p["retailCounterName"]));
		$contactPerson	= addSlash(trim($p["contactPerson"]));
		$address	= addSlash(trim($p["address"]));
		$selStateId	= $p["state"];
		$selCityId	= $p["city"];
		$pinCode	= addSlash(trim($p["pinCode"]));
		$telNo		= addSlash(trim($p["telNo"]));
		$faxNo		= addSlash(trim($p["faxNo"]));
		$mobNo		= addSlash(trim($p["mobNo"]));
		$vatNo		= addSlash(trim($p["vatNo"]));
		$tinNo		= addSlash(trim($p["tinNo"]));
		$cstNo		= addSlash(trim($p["cstNo"]));
		$selDistributorId = $p["distributor"];
		$selSalesStaffId 	= $p["salesStaff"];
		$area			= $p["area"];
		$selRtCtCateogry	= $p["selRtCtCateogry"];
		/*
		$disCharge	= $p["disCharge"];
		$disType	= $p["disType"]; // M-month or D-Date
		if ($disType=='D') {
			$selectFrom	= mysqlDateFormat($p["selectFrom"]);
			$selectTill	= mysqlDateFormat($p["selectTill"]);
		} else {
			$selectFrom	= "";
			$selectTill	= "";
		}

		$hidChargeLogStatus	= $p["hidChargeLogStatus"];
		*/
		# Check Duplicate Entry
		$duplicateEntry = $retailCounterMasterObj->chkDuplicateEntry($retailCounterName, $retailCounterId);	

		if ($retailCounterId!="" && $retailCounterName!="" && !$duplicateEntry) {
			$retailCounterRecUptd = $retailCounterMasterObj->updateRetailCounter($retailCounterId, $retailCounterName, $contactPerson, $address, $selStateId, $selCityId, $pinCode, $telNo, $faxNo, $mobNo, $vatNo, $tinNo, $cstNo, $selDistributorId, $selSalesStaffId, $area, $selRtCtCateogry);
		}
	
		if ($retailCounterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRetailCounterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateRetailCounter.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failRetailCounterUpdate;
		}
		$retailCounterRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount = $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$retailCounterId = $p["delId_".$i];

			if ($retailCounterId!="") {
				# Check Retail Counter in use
				$rtCounterInUse = $retailCounterMasterObj->retailCounterInUse($retailCounterId);
				if (!$rtCounterInUse) {
					# Need to check the selected Category is link with any other process
					# Delete Operational Area
					$operationalAreaRecDel = $retailCounterMasterObj->deleteOperationalArea($retailCounterId);
					# Delete Main Retail counter rec
					$retailCounterRecDel = $retailCounterMasterObj->deleteRetailCounter($retailCounterId);
				} // Checking ends here
			}
		}
		if ($retailCounterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRetailCounter);
			$sessObj->createSession("nextPage",$url_afterDelRetailCounter.$selection);
		} else {
			$errDel	=	$msg_failDelRetailCounter;
		}
		$retailCounterRecDel	=	false;
	}





if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$retailCounterId	=	$p["confirmId"];
			if ($retailCounterId!="") {
				// Checking the selected fish is link with any other process
				$retailRecConfirm = $retailCounterMasterObj->updateretailCounterconfirm($retailCounterId);
			}

		}
		if ($retailRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmretail);
			$sessObj->createSession("nextPage",$url_afterDelRetailCounter.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$retailCounterId = $p["confirmId"];
			if ($retailCounterId!="") {
				#Check any entries exist
				
					$retailRecConfirm = $retailCounterMasterObj->updateretailCounterReleaseconfirm($retailCounterId);
				
			}
		}
		if ($retailRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmretail);
			$sessObj->createSession("nextPage",$url_afterDelRetailCounter.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["selFilter"]!="") $distFilterId = $g["selFilter"];
	else $distFilterId = $p["selFilter"];

	# List all Retail Counter
	$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllPagingRecords($offset, $limit, $distFilterId);
	$retailCounterRecordSize  = $retailCounterResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allRetailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecords($distFilterId);
	$numrows	=  $allRetailCounterResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
	if ($addMode || $editMode ) {
		#List all State
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
		# List all Distributor
		//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
		$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
		# List all sales Staff
		//$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecords();
		$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecordsActiveStaff();

		# List all Rt Ct Category ;
		//$rtCtCategoryRecords	= $retailCounterCategoryObj->fetchAllRecords();
		$rtCtCategoryRecords	= $retailCounterCategoryObj->fetchAllActiveRecords();
	}

	if ($p["editSelectionChange"]==1 && $p["state"]=="") $selStateId = $retailCounterRec[5];
	else $selStateId = $p["state"];

	if ($selStateId!="") $cityRecords = $cityMasterObj->filterCityRecs($selStateId);

	if ($p["editSelectionChange"]==1 && $p["city"]=="") $selCityId = $retailCounterRec[6];
	else $selCityId = $p["city"];

	if ($selCityId!="") {
		if ($addMode) $areaRecords = $areaMasterObj->filterAreaRecs($selCityId);
		else if ($editMode) $areaRecords = $retailCounterMasterObj->fetchSelectedAreaRecords($editId, $selCityId);
	}

	# Filter Distributor Wise
	//$filterDistResultSetObj = $distributorMasterObj->fetchAllRecords();
	$filterDistResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	if ($editMode)	$heading =	$label_editRetailCounter;
	else 		$heading =	$label_addRetailCounter;
	
	$ON_LOAD_PRINT_JS	= "libjs/RetailCounterMaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmRetailCounterMaster" action="RetailCounterMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<tr>
		<td align="center">
			<a href="StateMaster.php" class="link1">State</a>&nbsp;&nbsp;
			<a href="CityMaster.php" class="link1">City</a>&nbsp;&nbsp;
			<a href="AreaMaster.php" class="link1">Area</a>
		</td>
	</tr>
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
					$bxHeader = "Retail Counter";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="45%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRetailCounterMaster(document.frmRetailCounterMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRetailCounterMaster(document.frmRetailCounterMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidRetailCounterId" value="<?=$editRetailCounterId;?>">
		<input type="hidden" name="hidChargeLogStatus" value="<?=$cLogStatus;?>">	
	<!--<tr>
		<td class="fieldName" nowrap >*Code</td>
		<td><input type="text" name="code" size="10" value="<?=$code;?>" /></td>
	</tr>-->
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD colspan="2" style="padding-left:10px;padding-right:10px">
			<table>
				<TR>
					<TD>
					<fieldset>
						<table>
							<tr>
								<td class="fieldName" nowrap >*Name </td>
								<td><input type="text" name="retailCounterName" size="20" value="<?=$retailCounterName;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >*Contact Person Name </td>
								<td><input type="text" name="contactPerson" size="20" value="<?=$contactPerson;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Address</td>
								<td><textarea name="address"><?=$address;?></textarea></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Pin Code</td>
								<td><input type="text" name="pinCode" value="<?=$pinCode;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Tel No</td>
								<td><input type="text" name="telNo" value="<?=$telNo;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Fax No</td>
								<td><input type="text" name="faxNo" value="<?=$faxNo;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Mob No</td>
								<td><input type="text" name="mobNo" value="<?=$mobNo;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >VAT No</td>
								<td><input type="text" name="vatNo" value="<?=$vatNo;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >TIN No</td>
								<td><input type="text" name="tinNo" value="<?=$tinNo;?>"></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >CST No</td>
								<td colspan="2"><input type="text" name="cstNo" value="<?=$cstNo;?>"></td>
							</tr>
						</table>
					</fieldset>
					</TD>
					<TD>&nbsp;</TD>
					<TD valign="top">
						<fieldset>
						<table>
							<tr>
								<td class="fieldName" nowrap >*Serviced by Distributor</td>
								<td>
									<select name="distributor">			
									<option value="">--Select--</option>
									<?	
										while ($dr=$distributorResultSetObj->getRow()) {
											$distributorId	 = $dr[0];
											$distributorName = stripSlash($dr[2]);	
											$selected = ($selDistributorId==$distributorId)?"selected":"";
									?>
									<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
									<? }?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >*Serviced by Sales Staff</td>
								<td>
									<select name="salesStaff" id="salesStaff">			
									<option value="">--Select--</option>
									<?	
										while ($ssr=$salesStaffResultSetObj->getRow()) {
											$salesStaffId	 = $ssr[0];
											$salesStaffName = stripSlash($ssr[2]);		
											$selected = ($selSalesStaffId==$salesStaffId)?"selected":"";
									?>
									<option value="<?=$salesStaffId?>" <?=$selected?>><?=$salesStaffName?></option>
									<? }?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >*State</td>
								<td>
									<select name="state" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">
									<option value="">--Select--</option>
									<?
									while ($sr=$stateResultSetObj->getRow()) {
										$stateId = $sr[0];						
										$stateName	= stripSlash($sr[2]);	
										$selected = ($selStateId==$stateId)?"Selected":"";			
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
										$cityName	= stripSlash($cr[2]);				
										$selected = ($selCityId==$cityId)?"Selected":""; 
									?>
									<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
									<? }?>
									</select>
								</td>
							</tr>	
							<tr>
								<td class="fieldName" nowrap >*Operational Area</td>
								<td>
									<select name="area[]" multiple="true" size="6" id="area">
									<option value="">-- Select --</option>
									<?
									foreach ($areaRecords as $ar) {			
										$areaId 	= $ar[0];
										$areaName	= stripSlash($ar[2]);		
										$selAreaId	= $ar[4];
										$selected = ($selAreaId==$areaId)?"Selected":""; 
									?>
									<option value="<?=$areaId?>" <?=$selected?>><?=$areaName?></option>
									<? }?>
									</select>
								</td>
							</tr>	
							<tr>
								<td class="fieldName" nowrap >*Category</td>
								<td>
									<select name="selRtCtCateogry" id="selRtCtCateogry">
									<option value="">--Select--</option>
									<?
									foreach ($rtCtCategoryRecords as $cr) {
										$categoryId	= $cr[0];
										$categoryName	= stripSlash($cr[1]);				
										$selected = ($selRtCtCateogry==$categoryId)?"Selected":""; 
									?>
									<option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
									<? }?>
									</select>
								</td>
							</tr>
						</table>
						</fieldset>
					</TD>					
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
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRetailCounterMaster(document.frmRetailCounterMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRetailCounterMaster(document.frmRetailCounterMaster);">	
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
				<!-- Form fields end   -->			</td>
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
<tr>
	<td colspan="3" align="center">
		<table width="35%">
			<TR>
				<TD>
				<?php			
					$entryHead = "";
					require("template/rbTop.php");
				?>
				<table cellpadding="4" cellspacing="4">
				<tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table align="right" cellpadding="0" cellspacing="0">	
			<tr>
				<td align="right" nowrap="nowrap" class="listing-item">Distributor&nbsp;</td>
				<td align="right" nowrap="true">
				<select name="selFilter" onChange="this.form.submit();">
				<option value="0">-- Select All --</option>
				<?	
				while ($fdr=$filterDistResultSetObj->getRow()) {
					$distributorId	 = $fdr[0];
					$distributorName = stripSlash($fdr[2]);	
					$selected = ($distFilterId==$distributorId)?"selected":"";
				?>
               			<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
				<? }?>
				  </select>
				</td>
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
				</td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						<tr>
							<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
			<td background="images/heading_bg.gif" class="pageName" nowrap="true" style="background-repeat:repeat-x">&nbsp;Retail Counter</td>
			<td background="images/heading_bg.gif" class="pageName" align="right" nowrap="true" style="background-repeat:repeat-x">
			</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$retailCounterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRetailCounterMaster.php?selFilter=<?=$distFilterId?>',700,600);"><? }?></td>
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
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
		if ($retailCounterRecordSize) {
			$i = 0;
	?>
	<? if($maxpage>1){ ?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RetailCounterMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RetailCounterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RetailCounterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
		<td width="20" rowspan="2">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Contact<br> Person</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" colspan="2">Serviced by</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">State</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">City</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Operational Area</td>
		<? if($edit==true){?>
		<td class="listing-head" rowspan="2"></td>
		<? }?>
		<? if($confirm==true){?>
			<td class="listing-head" rowspan="2"></td>
		<? }?>
	</tr>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sales Staff</td>	
	</tr>
	<?	
	$prevDistributorId = "";
	$prevSalesStaffId	= "";
	$prevStateId		= "";	
	$prevCityId		= "";	
	while ($dr=$retailCounterResultSetObj->getRow()) {
		$i++;
		$retailCounterId	= $dr[0];
		$retailCounterCode 	= stripSlash($dr[1]);
		$retailCounterName 	= stripSlash($dr[2]);
		$contactPerson		= stripSlash($dr[3]);
		$distributorId		= $dr[14];
		$distributorName = "";
		if ($prevDistributorId!=$distributorId) {
			$distributorName	= stripSlash($dr[15]);
		}
		$salesStaffId	= $dr[16];
		$salesStaffName = "";
		if ($prevSalesStaffId!=$salesStaffId) {
			$salesStaffName = stripSlash($dr[17]);
		}
		
		$stateId	= $dr[5];
		$stateName 	= "";
		if ($prevStateId!=$stateId) {
			$stateName	= stripSlash($dr[18]);	
		}

		$cityId		= $dr[6];
		$cityName 	= "";
		if ($prevCityId!=$cityId) {
			$cityName	= stripSlash($dr[19]);	
		}
		$active=$dr[20];
		$existingcount=$dr[21];

		# get Operational Area Records
		$selAreaRecords = $retailCounterMasterObj->getOperationalAreaRecords($retailCounterId);
	?>
<tr   <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } else {?>bgcolor="WHITE" <?php }?> >
	<td width="20">
		<?php 
		if($existingcount==0){
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$retailCounterId;?>" class="chkBox">
		<?php 
		}
		?>
	</td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$retailCounterName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$contactPerson;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$distributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$salesStaffName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stateName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$cityName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($selAreaRecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach ($selAreaRecords as $areaR) {
							$j++;
							$areaName = $areaR[1];
							$nextRec++;
				?>
				<td class="listing-item" valign="top">
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
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$retailCounterId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RetailCounterMaster.php';"><? } ?></td>
	<? }?>

	<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$retailCounterId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0){ ?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$retailCounterId;?>,'confirmId');" >
			<?php 
			//}
			}?>
			<? }?>
			
	</tr>
		<?
			$prevDistributorId	= $distributorId;
			$prevSalesStaffId	= $salesStaffId;
			$prevStateId		= $stateId;
			$prevCityId		= $cityId;
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RetailCounterMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RetailCounterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RetailCounterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
												<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$retailCounterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRetailCounterMaster.php?selFilter=<?=$distFilterId?>',700,600);"><? }?></td>
											</tr>
										</table></td>
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
		
		<tr>
			<td height="10"></td>
		</tr>
	<tr>
		<td height="10" align="center">
			<a href="StateMaster.php" class="link1">State</a>&nbsp;&nbsp;<a href="CityMaster.php" class="link1">City</a>&nbsp;&nbsp;<a href="AreaMaster.php" class="link1">Area</a>
		</td>
	</tr>
	</table>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>