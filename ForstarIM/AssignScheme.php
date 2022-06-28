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
		
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	// Value setting
	if ($p["selScheme"]!="") 	$selSchemeId	= $p["selScheme"];
	if ($p["schemeCategory"]!="") 	$schemeCategory	= $p["schemeCategory"];
	if ($p["selectFrom"]!="") 	$selectFrom	= $p["selectFrom"];
	if ($p["selectTill"]!="") 	$selectTill	= $p["selectTill"];
	if ($p["selState"]!="") 	$selStateId	= $p["selState"];
	if ($p["selCity"]!="") 		$selCityId	= $p["selCity"];
	if ($p["selArea"]!="") 		$selAreaId	= $p["selArea"];
	if ($p["distributor"]!="") 	$selDistributorId   = $p["distributor"];
	if ($p["selRetailCounter"]!="") $selRetailCounterId = $p["selRetailCounter"];

	# Add a Record
	if ($p["cmdAdd"]!="") {	
		$selScheme	= $p["selScheme"];
		$schemeCategory	= $p["schemeCategory"];
		$selectFrom	= mysqlDateFormat($p["selectFrom"]);
		$selectTill	= mysqlDateFormat($p["selectTill"]);
		$selState	= $p["selState"];
		$selCity	= $p["selCity"];
		$selArea	= $p["selArea"];
		$distributor	= $p["distributor"];
		$selRetailCounter = $p["selRetailCounter"];	
		
		# Checking any entry  of scheme exist for the selected period
		$chkRecExist   = $assignSchemeObj->chkEntryExist($selScheme, $selectFrom, $selectTill, '');

		if ($selScheme!="" && $schemeCategory!="" && $selectFrom!="" && $selectTill!="" && !$chkRecExist) {
			$assignSchemeRecIns = $assignSchemeObj->addSchemeAssign($selScheme, $schemeCategory, $selectFrom, $selectTill, $selState, $selCity, $selArea, $distributor, $selRetailCounter, $cUserId);

			if ($assignSchemeRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddAssignScheme);
				$sessObj->createSession("nextPage",$url_afterAddAssignScheme.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddAssignScheme;
			}
			$assignSchemeRecIns		=	false;
		} else {
			if ($chkRecExist) $err  = $msg_failAssignSchemeEntryExist;	
		}
	}

	# Edit a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$assignSchemeRec	= $assignSchemeObj->find($editId);
		$editAssignSchemeId	= $assignSchemeRec[0];
		$selSchemeId	= $assignSchemeRec[1];
		$schemeCategory	= $assignSchemeRec[2];
		$selectFrom	= dateFormat($assignSchemeRec[3]);
		$selectTill	= dateFormat($assignSchemeRec[4]);
		$selStateId	= $assignSchemeRec[5];
		$selCityId	= $assignSchemeRec[6];
		$selAreaId	= $assignSchemeRec[7];
		$selDistributorId   = $assignSchemeRec[8];
		$selRetailCounterId = $assignSchemeRec[9];		
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {
		$assignSchemeId	= $p["hidAssignSchemeId"];
		
		$selScheme	= $p["selScheme"];
		$schemeCategory	= $p["schemeCategory"];
		$selectFrom	= mysqlDateFormat($p["selectFrom"]);
		$selectTill	= mysqlDateFormat($p["selectTill"]);
		$selState	= $p["selState"];
		$selCity	= $p["selCity"];
		$selArea	= $p["selArea"];
		$distributor	= $p["distributor"];
		$selRetailCounter = $p["selRetailCounter"];
		
		# Checking any entry  of scheme exist for the selected period
		$chkRecExist   = $assignSchemeObj->chkEntryExist($selScheme, $selectFrom, $selectTill, $assignSchemeId);

		if ($selScheme!="" && $schemeCategory!="" && $selectFrom!="" && $selectTill!="" && !$chkRecExist) {
			$assignSchemeRecUptd = $assignSchemeObj->updateAssignScheme($assignSchemeId, $selScheme, $schemeCategory, $selectFrom, $selectTill, $selState, $selCity, $selArea, $distributor, $selRetailCounter);
		}
	
		if ($assignSchemeRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succAssignSchemeUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateAssignScheme.$selection);
		} else {
			$editMode	=	true;
			if ($chkRecExist) $err  = $msg_failAssignSchemeEntryExist;	
			else $err	= $msg_failAssignSchemeUpdate;
		}
		$assignSchemeRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$assignSchemeId	=	$p["delId_".$i];

			if ($assignSchemeId!="") {
				// Need to check the selected Category is link with any other process		
				$assignSchemeRecDel = $assignSchemeObj->deleteAssignScheme($assignSchemeId);
			}
		}
		if ($assignSchemeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelAssignScheme);
			$sessObj->createSession("nextPage",$url_afterDelAssignScheme.$selection);
		} else {
			$errDel	=	$msg_failDelAssignScheme;
		}
		$assignSchemeRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$assignSchemeId	=	$p["delId_".$i];
			if ($assignSchemeId!="") {
				// Checking the selected fish is link with any other process
				$assignSchemeRecConfirm = $assignSchemeObj->updateassignSchemeconfirm($assignSchemeId);
			}

		}
		if ($assignSchemeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmAssignScheme);
			$sessObj->createSession("nextPage",$url_afterDelRtCounterMarginStructure.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmFishCategory;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$assignSchemeId = $p["delId_".$i];
			if ($assignSchemeId!="") {
				#Check any entries exist
				
					$assignSchemeRecRlConfirm = $assignSchemeObj->updateassignSchemeReleaseconfirm($assignSchemeId);
				
			}
		}
		if ($assignSchemeRecRlConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRlConfirmAssignScheme);
			$sessObj->createSession("nextPage",$url_afterDelRtCounterMarginStructure.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmFishCategory;
		}
		}
	

	## -------------- Pagination Settings I -------------------
	if 	($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else 	$pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Assign Scheme
	$assignSchemeResultSetObj 	= $assignSchemeObj->fetchAllPagingRecords($offset, $limit);
	$assignSchemeRecordSize	 	= $assignSchemeResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$fetchAllAssignSchemeResultSetObj = $assignSchemeObj->fetchAllRecords();
	$numrows	=  $fetchAllAssignSchemeResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	# List all Scheme Master
	$schemeMasterResultSetObj = $schemeMasterObj->fetchAllRecordsActiveScheme();	

	if ($addMode || $editMode ) {
		#List all State
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
		# List all Distributor
		$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
		# List all sales Staff
		$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecordsActiveStaff();
		#List all Retail Counter
		$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');
	}

	if ($p["editSelectionChange"]==1 && $p["selState"]=="") $selStateId = $assignSchemeRec[5];
	else $selStateId = $p["selState"];

	if ($selStateId!="") $cityRecords = $cityMasterObj->filterCityRecs($selStateId);

	if ($p["editSelectionChange"]==1 && $p["selCity"]=="") $selCityId = $assignSchemeRec[6];
	else $selCityId = $p["selCity"];

	if ($selCityId!="") $areaRecords = $areaMasterObj->filterAreaRecs($selCityId);

	if ($editMode)	$heading = $label_editAssignScheme;
	else 		$heading = $label_addAssignScheme;
	
	$ON_LOAD_PRINT_JS	= "libjs/AssignScheme.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmAssignScheme" action="AssignScheme.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignScheme.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAssignSchemeMaster(document.frmAssignScheme);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignScheme.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAssignSchemeMaster(document.frmAssignScheme);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidAssignSchemeId" value="<?=$editAssignSchemeId;?>">
	<!--<tr>
		<td class="fieldName" nowrap >*Code</td>
		<td><input type="text" name="code" size="10" value="<?=$code;?>" /></td>
	</tr>-->
	<tr>
		<td class="fieldName" nowrap >*Scheme </td>
		<td>
			<select name="selScheme" id="selScheme">
				<option value="">-- Select --</option>
				<?
				while ($smr=$schemeMasterResultSetObj->getRow()) {
					$schemeMasterId = $smr[0];
					$schemeName	= $smr[1];
					$selected 	= "";
					if ($selSchemeId==$schemeMasterId) $selected = "selected";
				?>
				<option value="<?=$schemeMasterId?>" <?=$selected?>><?=$schemeName?></option>
				<?
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Scheme For</td>
		<td>
			<select name="schemeCategory" id="schemeCategory">
				<option value="">-- Select --</option>
				<option value="C" <? if ($schemeCategory=='C') echo "selected";?>>Customer</option>
				<option value="R" <? if ($schemeCategory=='R') echo "selected";?>>Retailer</option>
				<option value="D" <? if ($schemeCategory=='D') echo "selected";?>>Distributor</option>
			</select>
		</td>
	</tr>	
	<tr>
		<td class="fieldName">*From</td>
		<td>
			<table cellpadding="0" cellspacing="0">
				<TR>
					<TD>
					<?
					if ($p["selectFrom"]!="") 	$selectFrom	= $p["selectFrom"];
					?>
						<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$selectFrom?>">
					</TD>
					<td class="fieldName" nowrap="true">&nbsp;*To&nbsp;</td>
					<td> 
					<?
						if ($p["selectTill"]!="") 	$selectTill	= $p["selectTill"];
					?>
						<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$selectTill?>">
					</td>
				</TR>
			</table>			
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*State</td>
		<td>
			<select name="selState" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">
			<option value="0">-- Select All--</option>
			<?
			while (($sr=$stateResultSetObj->getRow())) {
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
			<select name="selCity" onchange="<? if ($addMode) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>">
			<option value="0">-- Select All --</option>
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
		<td class="fieldName" nowrap >*Location</td>
		<td>
			<select name="selArea" id="selArea">
			<option value="0">-- Select All --</option>
			<?
			foreach ($areaRecords as $ar) {			
				$areaId 	= $ar[0];
				$areaCode	= stripSlash($ar[1]);
				$areaName	= stripSlash($ar[2]);						
				$selected = "";
				if ($selAreaId==$areaId) $selected = "Selected"; 
			?>
			<option value="<?=$areaId?>" <?=$selected?>><?=$areaName?></option>
			<? }?>
			</select>
		</td>
  	</tr>
	<tr>
		<td class="fieldName" nowrap >*Distributor</td>
		<td>
			<select name="distributor">			
			<option value="0">-- Select All --</option>
			<?	
				while ($dr=$distributorResultSetObj->getRow()) {
					$distributorId	 = $dr[0];
					$distributorCode = stripSlash($dr[1]);
					$distributorName = stripSlash($dr[2]);	
					$selected = "";
					if ($selDistributorId==$distributorId) $selected = "selected";
			?>
               		<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
			<? }?>
			</select>
		</td>
  	</tr>
	<tr>
					<td nowrap class="fieldName">*Retail Counter</td>
					<td nowrap>
                                        <select name="selRetailCounter">
                                        <option value="0">-- Select All --</option>
					<?	
					while ($rc=$retailCounterResultSetObj->getRow()) {
						$retailCounterId	= $rc[0];
						$retailCounterCode 	= stripSlash($rc[1]);
						$retailCounterName 	= stripSlash($rc[2]);	
						$selected = "";
						if ($selRetailCounterId==$retailCounterId) $selected = "selected";	
					?>
                            		<option value="<?=$retailCounterId?>" <?=$selected?>><?=$retailCounterName?></option>
					<? }?>
					</select>
					</td></tr>	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignScheme.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAssignSchemeMaster(document.frmAssignScheme);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignScheme.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAssignSchemeMaster(document.frmAssignScheme);">	
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Assign Scheme Master</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$assignSchemeRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAssignScheme.php',700,600);"><? }?></td>
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
		if ($assignSchemeRecordSize) {
			$i = 0;
	?>

	<? if($maxpage>1){ ?>
		<tr bgcolor="#FFFFFF">
		<td colspan="11" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AssignScheme.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AssignScheme.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AssignScheme.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Scheme</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Category</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">From</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">To</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">State</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">City</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Location</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Retailer</td>
		<? if($edit==true){?>
			<td class="listing-head"></td>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	<?	
	while ($asr=$assignSchemeResultSetObj->getRow()) {
		$i++;
		$assignSchemeId		= $asr[0];
		$schemeName		= $asr[10];
		$schemeCategory		= $asr[2];
		$disSchemeCategory 	= "";
		if ($schemeCategory=='C') $disSchemeCategory = "Customer";
		else if ($schemeCategory=='R') $disSchemeCategory = "Retailer"; 
		else if ($schemeCategory=='D') $disSchemeCategory = "Distributor";

		$fromDate	= dateFormat($asr[3]);
		$tillDate	= dateFormat($asr[4]);

		$stateId	= $asr[5];
		$stateRec	= $stateMasterObj->find($stateId);
		$stateName	= ($stateRec[2]!="")?$stateRec[2]:"ALL INDIA";	

		$cityId		= $asr[6];
		$cityRec	= $cityMasterObj->find($cityId);		
		$cityName	= ($cityRec[2]!="")?$cityRec[2]:"ALL CITIES";	

		$areaId		= $asr[7];
		$areaRec	= $areaMasterObj->find($areaId);
		$areaName	= ($areaRec[2]!="")?$areaRec[2]:"ALL LOCATIONS";	

		$distributorId	= $asr[8];
		$distributorRec	= $distributorMasterObj->find($distributorId);
		$distriName	= ($distributorRec[2]!="")?$distributorRec[2]:"ALL DSTRIBUTORS";

		$retailCounterId = $asr[9];
		$retailCounterRec	= $retailCounterMasterObj->find($retailCounterId);
		$retailCounterName	= ($retailCounterRec[2]!="")?$retailCounterRec[2]:"ALL RETAILERS";

		$active = $asr[11];
		
	?>
	<tr  bgcolor="WHITE" <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$assignSchemeId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$schemeName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$disSchemeCategory;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$fromDate;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$tillDate;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$cityName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$areaName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$distriName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$retailCounterName;?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="80" align="center">
				<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$assignSchemeId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='AssignScheme.php';">
			</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" Confirm " name="btnConfirm"  >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm"  >
			<?php
			//} ?>
			<?php }?>
			<? }?>
	</tr>
	<?
	
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="11" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AssignScheme.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AssignScheme.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AssignScheme.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$assignSchemeRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAssignScheme.php',700,600);"><? }?></td>
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