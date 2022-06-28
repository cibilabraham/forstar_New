<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$registrations = array();
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/


	# Add Pond Master Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}


	#Add a Pond Master
	if ($p["cmdAdd"]!="" || $p['cmdSaveAddNew']) {
		
		$pondName		=	addSlash(trim($p["pondName"]));
		$supplier	=	addSlash(trim($p["supplier"]));
		$alloteeName		=	addSlash(trim($p["alloteeName"]));
		$address		=	addSlash(trim($p["address"]));
		$state		=	addSlash(trim($p["state"]));
		$district		=	addSlash(trim($p["district"]));
		$taluk		=	addSlash(trim($p["taluk"]));
		$village		=	addSlash(trim($p["village"]));
		
		$location		=	addSlash(trim($p["location"]));
		$registrationType		=	$p["registrationType"];
		$registrationNo		=	$p["registrationNo"];
		$registrationDate		=	$p["registrationDate"];
		$registrationExpiryDate		=	$p["registrationExpiryDate"];
		$pondSize		=	addSlash(trim($p["pondSize"]));
		$pondSizeUnit		=	addSlash(trim($p["pondSizeUnit"]));
		$pondQty		=	addSlash(trim($p["pondQty"]));
		$returnDays     =	$p["returnDays"];
		
		
		$pondMasterDuplicate	=	$pondMasterObj->CheckDuplicate($pondName);
		
		if(sizeof($pondMasterDuplicate)>0)
		{
			$err = " Failed to add Farm. Please make sure the Farm Name you have entered is not duplicate. ";

		}
		else
		{
		
			if ($pondName!="" && $supplier!="" && $alloteeName!="" && $address!="" && $state!="" && $district!="" && $taluk!="" && $village!=""  && $location!="" && $pondSize!="" && $pondSizeUnit!="" && $pondQty!="" && $returnDays!='') {
				$pondMasterRecIns	=	$pondMasterObj->addPondMaster($pondName, $supplier, $alloteeName,$address,$state,$district,$taluk,$village,$location,$pondSize,$pondSizeUnit,$pondQty,$returnDays, $userId);

				if ($pondMasterRecIns) {
					$pond_id = $databaseConnect->getLastInsertedId();
					$pondMasterObj->addPondRegistration($pond_id,$registrationType,$registrationNo,$registrationDate,$registrationExpiryDate);
					if($p['cmdSaveAddNew'])
					{
						$addMode = true;
						$pondMasterRec		=	$pondMasterObj->find($pond_id);
						// echo '<pre>';print_r($pondMasterRec);echo '</pre>';
						$pondMasterId			=	$pondMasterRec[0];
						$pondName			=	'';//stripSlash($pondMasterRec[1]);
						$supplier				=	stripSlash($pondMasterRec[2]);
						$alloteeName	=	stripSlash($pondMasterRec[3]);
						$address	=	stripSlash($pondMasterRec[4]);
						$state	=	stripSlash($pondMasterRec[5]);
						$district	=	stripSlash($pondMasterRec[6]);
						$taluk	=	stripSlash($pondMasterRec[7]);
						$village	=	stripSlash($pondMasterRec[8]);
						
						$location	=	stripSlash($pondMasterRec[9]);
						// $registrationType	=	stripSlash($pondMasterRec[10]);
						// $registrationNo	=	stripSlash($pondMasterRec[11]);
						// $registrationDate	=	dateformat($pondMasterRec[12]);
						// $registrationExpiryDate	=	dateformat($pondMasterRec[13]);
						$pondSize	=	stripSlash($pondMasterRec[10]);
						$pondSizeUnit	=	stripSlash($pondMasterRec[11]);
						$pondQty	=	stripSlash($pondMasterRec[12]);
						$returnDays=$pondMasterRec[14];
						if($pondMasterRec[13] != '')
						{
							$registrations = explode(',',$pondMasterRec[13]);
						}	
					}
					else
					{
						$sessObj->createSession("displayMsg", $msg_succAddPondMaster);
						$sessObj->createSession("nextPage", $url_afterAddPondMaster.$selection);
					}
				} else {
					$addMode	=	true;
					$err		=	$msg_failAddPondMaster;
				}
				$pondMasterRecIns		=	false;
			}
		}
		
		
		
		
	}
		//echo $p["editId"];
		
	# Edit Pond Master 
	if ($p["editId"]!="" ) {
		 $editId			=	$p["editId"];
		$editMode		=	true;
		$pondMasterRec		=	$pondMasterObj->find($editId);
		// echo '<pre>';print_r($pondMasterRec);echo '</pre>';
		$pondMasterId			=	$pondMasterRec[0];
		$pondName			=	stripSlash($pondMasterRec[1]);
		$supplier				=	stripSlash($pondMasterRec[2]);
		$alloteeName	=	stripSlash($pondMasterRec[3]);
		$address	=	stripSlash($pondMasterRec[4]);
		$state	=	stripSlash($pondMasterRec[5]);
		$district	=	stripSlash($pondMasterRec[6]);
		$taluk	=	stripSlash($pondMasterRec[7]);
		$village	=	stripSlash($pondMasterRec[8]);
		
		$location	=	stripSlash($pondMasterRec[9]);
		// $registrationType	=	stripSlash($pondMasterRec[10]);
		// $registrationNo	=	stripSlash($pondMasterRec[11]);
		// $registrationDate	=	dateformat($pondMasterRec[12]);
		// $registrationExpiryDate	=	dateformat($pondMasterRec[13]);
		$pondSize	=	stripSlash($pondMasterRec[10]);
		$pondSizeUnit	=	stripSlash($pondMasterRec[11]);
		$pondQty	=	stripSlash($pondMasterRec[12]);
		if($pondMasterRec[13] != '')
		{
			$registrations = explode(',',$pondMasterRec[13]);
		}
		$returnDays=$pondMasterRec[14];		
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$pondMasterId		=	$p["hidPondMasterId"];
		$pondName		=	addSlash(trim($p["pondName"]));
		$supplier	=	addSlash(trim($p["supplier"]));
		$alloteeName		=	addSlash(trim($p["alloteeName"]));
		$address		=	addSlash(trim($p["address"]));
		$state		=	addSlash(trim($p["state"]));
		$district		=	addSlash(trim($p["district"]));
		$taluk		=	addSlash(trim($p["taluk"]));
		$village		=	addSlash(trim($p["village"]));
		$location		=	addSlash(trim($p["location"]));
		$registrationType		=	$p["registrationType"];		
		$registrationNo		=	$p["registrationNo"];
		$registrationDate		=	$p["registrationDate"];
		$registrationExpiryDate		=	$p["registrationExpiryDate"];
		$registrationIds  = $p['registration_ids'];
		$pondSize		=	addSlash(trim($p["pondSize"]));
		$pondSizeUnit		=	addSlash(trim($p["pondSizeUnit"]));
		$pondQty		=	addSlash(trim($p["pondQty"]));
		$returnDays     =	$p["returnDays"];
		
		if ($pondName!="" && $supplier!="" && $alloteeName!="" && $address!="" && $state!="" && $district!="" && $taluk!="" && $village!="" && $location!="" && $pondSize!="" && $pondSizeUnit!="" && $pondQty!="" && $returnDays!="") {
			$pondMasterRecUptd = $pondMasterObj->updatePondMaster($pondMasterId,$pondName, $supplier, $alloteeName, $address,$state,$district,$taluk,$village,$location,$pondSize,$pondSizeUnit,$pondQty,$returnDays);
			$pondMasterObj->updatePondRegistration($registrationIds,$pondMasterId,$registrationType,$registrationNo,$registrationDate,$registrationExpiryDate);
		}
	
		if ($pondMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPondMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePondMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPondMasterUpdate;
		}
		$pondMasterRecUptd	=	false;
	}


	# Delete Pond Master
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		$notDeleted = '';
		for ($i=1; $i<=$rowCount; $i++) {
		$pondMasterId	=	$p["delId_".$i];
			
			if ($pondMasterId!="") {
				$check = $pondMasterObj->checkPondUsed($pondMasterId);
				// Need to check the selected Department is link with any other process
				if($check == 0)
				{
					$pondMasterRecDel	=	$pondMasterObj->deletePondMaster($pondMasterId);
				}
				else
				{
					$notDeleted.= $i."th row,";
				}
			}
		}
	
		if ($pondMasterRecDel) {
		
			if($notDeleted != '')
				$msg_succDelPondMaster = $notDeleted.' could not deleted. Its used in procurement order';
				
			$sessObj->createSession("displayMsg",$msg_succDelPondMaster);
			$sessObj->createSession("nextPage",$url_afterDelPondMaster.$selection);
		} else {
			$errDel	=	$msg_failDelPondMaster;
		}
		$pondMasterRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$pondMasterId	=	$p["confirmId"];
			if ($pondMasterId!="") {
				// Checking the selected fish is link with any other process
				$pondMasterRecConfirm = $pondMasterObj->updatepondMasterconfirm($pondMasterId);
			}

		}
		if ($pondMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmPondMaster);
			$sessObj->createSession("nextPage",$url_afterDelPondMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$pondMasterId = $p["confirmId"];
			if ($pondMasterId!="") {
				#Check any entries exist
				
					$pondMasterRecConfirm = $pondMasterObj->updatePondMasterReleaseconfirm($pondMasterId);
				
			}
		}
		if ($pondMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmPondMaster);
			$sessObj->createSession("nextPage",$url_afterDelPondMaster.$selection);
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

	# List all Pond Master ;
	$pondMasterRecords	=	$pondMasterObj->fetchAllPagingRecords($offset, $limit);
	$pondMasterSize		=	sizeof($pondMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($registrationTypeObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	if ($addMode || $editMode)
	$stateRecords	= $stateMasterObj->fetchAllRecordsunitActive();
	$locationRecs=$landingcenterObj->fetchAllRecordsActiveLanding();
	$registrationTypeRecords	= $registrationTypeObj->fetchAllRecordsunitActive();
	$supplierRecords	= $supplierMasterObj->fetchAllRMSupplierActive();
	//$pondSizeRecords	= $areaObj->fetchAllRecordsunitActive();
	$pondSizeRecords	= $stockItemUnitObj->fetchAllRecordsunitActive();

	if ($editMode) 	$heading = $label_editPondMaster;
	else 		$heading = $label_addPondMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/pondmaster.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPondMaster" action="PondMaster.php" method="post">
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
			<tr bgcolor="#FFFFFF">
				<td>
				<?php	
					$bxHeader = "Manage Farm Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr >
						<td>
							<!-- Form fields start -->
							<?php		$rbTopWidth = '90%';					
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
								<tr >
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PondMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPondMaster(document.frmPondMaster);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PondMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPondMaster(document.frmPondMaster);">&nbsp;&nbsp;												
												<input type="submit" name="cmdSaveAddNew" class="button" value="Save Add New" onClick="return validateAddPondMaster(document.frmPondMaster);">
												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidPondMasterId" value="<?=$pondMasterId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>	
											<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
							?>
					<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
												<tr>
												<td class="fieldName" nowrap >*Farm Name</td>
												<td><INPUT TYPE="text" NAME="pondName" size="15" value="<?=$pondName;?>"></td>
											<!--</tr>
		
											<tr>-->
											  <td  height="10" class="fieldName">*Supplier </td>
										      <td  height="10" ><select name="supplier">
											  <option value="">--select--</option>
											  <?
												foreach($supplierRecords as $supplierValue)
													{
														$supplierId		=	$supplierValue[0];
														$supplierName	=	stripSlash($supplierValue[1]);
															$selected = ($supplier==$supplierId)?"selected":""
														/*$selected	=	"";
														if( $supplierId == $editCategoryId){
																$selected	=	"selected";
														}*/
											?>
											  <option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
											  <? }?>
										        </select>										      </td>
										 </tr>
											
											<tr>
												<td class="fieldName" nowrap >*Allotee Name</td>
												<td><INPUT TYPE="text" NAME="alloteeName" size="15" value="<?=$alloteeName;?>"></td>
												
											<!--</tr>
											<tr>-->
												<td class="fieldName" nowrap >*Address</td>
												<td ><textarea name="address"><?=$address;?></textarea></td>
											</tr>
											
											<tr>
											  <td  height="10" class="fieldName">*State </td>
										      <td  height="10" ><select name="state">
											  <option value="">--select--</option>
											  <?
												foreach($stateRecords as $stateValue)
													{
														$stateId		=	$stateValue[0];
														$stateName	=	stripSlash($stateValue[1]);
														$selected = ($state==$stateId)?"selected":""
														
											?>
											  <option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
											  <? }?>
										        </select>										      </td>
										 <!-- </tr>
											
											
											<tr>-->
												<td class="fieldName" nowrap >*District</td>
												<td><INPUT TYPE="text" NAME="district" size="15" value="<?=$district;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Taluk</td>
												<td><INPUT TYPE="text" NAME="taluk" size="15" value="<?=$taluk;?>"></td>
											<!--</tr>
											<tr>-->
												<td class="fieldName" nowrap >*Village</td>
												<td><INPUT TYPE="text" NAME="village" size="15" value="<?=$village;?>"></td>
											</tr>
											
											<tr>
												<td class="fieldName" nowrap >*Location</td>
												
												<td  height="10" ><select name="location">
											  <option value="">--select--</option>
											  <?
												foreach($locationRecs as $loc)
													{
														$locationId		=	$loc[0];
														$locationName	=	stripSlash($loc[1]);
															$selected = ($location==$locationId)?"selected":""
														
											?>
											  <option value="<?=$locationId?>" <?=$selected?>><?=$locationName?></option>
											  <? }?>
										        </select>										      </td>
												
											</tr>
											</table>
											<?php
								require("template/rbBottom.php");
							?>
											</td></tr>
										<tr>
												<td colspan="2"  height="10" ></td>
											</tr>	
											
											
											
											
											
											<!--<tr>
												<td class="fieldName" nowrap >*Registration No</td>
												<td><INPUT TYPE="text" NAME="registrationNo" size="15" value="<?=$registrationNo;?>"></td>
											</tr>-->
											
					<tr colspan="2">		
				
				<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
							?>
					<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
											<tr>
												<td class="fieldName" nowrap >*Farm size</td>
												<td><INPUT TYPE="text" NAME="pondSize" size="15" value="<?=$pondSize;?>"></td>
											
											  <td  height="10" class="fieldName" nowrap>*Farm Size Unit </td>
										      <td  height="10" ><select name="pondSizeUnit">
											  <option value="">--select--</option>
											  <?
												foreach($pondSizeRecords as $pondSize)
													{
														$pondSizeId		=	$pondSize[0];
														$areaUnitName	=	stripSlash($pondSize[1]);
														$selected = ($pondSizeUnit==$pondSizeId)?"selected":""
														
											?>
											  <option value="<?=$pondSizeId?>" <?=$selected?>><?=$areaUnitName?></option>
											  <? }?>
										        </select>										      </td>
										 </tr>
											<tr>
												<td class="fieldName" nowrap>*Available Raw material Quantity (kg) </td>
												<td><INPUT TYPE="text" NAME="pondQty" size="15" value="<?=$pondQty;?>"></td>
												<td class="fieldName" nowrap >*Return days</td>
												<td><INPUT TYPE="text" NAME="returnDays" size="15" value="<?=$returnDays;?>"></td>
											</tr>
						</table>
					<?php
								require("template/rbBottom.php");
							?>
				</td>
				
				</tr>
				
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>	
				
				<tr colspan="2">		
				
				<td colspan="2"  align="center">
				<?php							
					// $entryHead = "";
					// require("template/rbTop.php");
				?>
				
				<table width="<?php echo $rbTopWidth;?>%" bgcolor="#999999" cellspacing="1" cellpadding="3" name="tblPondRegMultiple" id="tblPondRegMultiple">
					<tbody>
						<tr bgcolor="#f2f2f2" align="center">
							<td class="listing-head" nowrap>Registration Type</td>
							<td class="listing-head" nowrap>Registration No</td>
							<td class="listing-head" nowrap>Registration Date</td>
							<td class="listing-head">Registration Expiry Date</td>																	
							<td></td>
						</tr>
						<?php
							if(sizeof($registrations) > 0)
							{
								$i=0;
								foreach($registrations as $registrationDet)
								{
									$registrationDetails = explode('$$',$registrationDet);
									$registrationDates = explode('-',$registrationDetails[2]);
									$registrationDateDis = $registrationDates[2].'/'.$registrationDates[1].'/'.$registrationDates[0];
									$registrationExpiryDates = explode('-',$registrationDetails[3]);
									$registrationExpiryDateDis = $registrationExpiryDates[2].'/'.$registrationExpiryDates[1].'/'.$registrationExpiryDates[0];
						?>
									<tr class="whiteRow" id="mrow_<?php echo $i;?>">
										<td align="left" class="fieldName">	
										<input type="hidden" name="registration_ids[]" value="<?php echo $registrationDetails[4];?>" />
											<select name="registrationType[]">
												<option value="">--select--</option>
												<?php 
													foreach($registrationTypeRecords as $registration)
													{
														$registrationTypeId		=	$registration[0];
														$registrationTypeName	=	stripSlash($registration[1]);
														$selected = ($registrationDetails[0]==$registrationTypeId)?"selected":""			
												?>
														<option value="<?=$registrationTypeId?>" <?=$selected?>><?=$registrationTypeName?></option>
												<? }?>
											</select>
										</td>
										<td align="left" class="fieldName">
											<INPUT TYPE="text" NAME="registrationNo[]" size="15" value="<?php echo $registrationDetails[1];?>">
										</td>
										<td align="left" class="fieldName">
											<input type="text" name="registrationDate[]" value="<?php echo $registrationDateDis;?>" id="registrationDate<?php echo $i+1;?>" autocomplete="off" />
										</td>
										<td align="left" class="fieldName">
											<input type="text" name="registrationExpiryDate[]" value="<?php echo $registrationExpiryDateDis;?>" id="registrationExpiryDate<?php echo $i+1;?>" autocomplete="off" />
										</td>
										<td align="center" class="fieldName">
											<a onclick="setIssuanceItemStatusWeight('<?php echo $i;?>');" href="javascript:void(0);">
												<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item" />
											</a>
											<input type="hidden" value="" id="mstatus_0" name="mstatus_0">
											<input type="hidden" value="N" id="IsFromDB_0" name="IsFromDB_0">
											<input type="hidden" value="" id="mrmId_0" name="mrmId_0">
										</td>
									</tr>
						<?php
									$i++;
									if($i != 1)
									{
						?>
									<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
									<!--
									Calendar.setup 
									(	
										{
											inputField  : "registrationDate<?php echo $i;?>",         // ID of the input field
											eventName	  : "click",	    // name of event
											button : "registrationDate<?php echo $i;?>", 
											ifFormat    : "%d/%m/%Y",    // the date format
											singleClick : true,
											step : 1
										}
									);
									Calendar.setup 
									(	
										{
											inputField  : "registrationExpiryDate<?php echo $i;?>",         // ID of the input field
											eventName	  : "click",	    // name of event
											button : "registrationExpiryDate<?php echo $i;?>", 
											ifFormat    : "%d/%m/%Y",    // the date format
											singleClick : true,
											step : 1
										}
									);
									//-->
									</SCRIPT>
						<?php
									}
								}
						?>
								<script>fieldvalue='<?php echo sizeof($registrations) - 1;?>';</script>
						<?php
							}
							else
							{
						?>
						<tr class="whiteRow" id="mrow_0">
							<td align="left" class="fieldName">				
								<select name="registrationType[]">
									<option value="">--select--</option>
									<?php 
										foreach($registrationTypeRecords as $registration)
										{
											$registrationTypeId		=	$registration[0];
											$registrationTypeName	=	stripSlash($registration[1]);
											$selected = ($registrationType==$registrationTypeId)?"selected":""			
									?>
											<option value="<?=$registrationTypeId?>" <?=$selected?>><?=$registrationTypeName?></option>
									<? }?>
								</select>
							</td>
							<td align="left" class="fieldName">
								<INPUT TYPE="text" NAME="registrationNo[]" size="15">
							</td>
							<td align="left" class="fieldName">
								<input type="text" name="registrationDate[]" id="registrationDate1" autocomplete="off" />
							</td>
							<td align="left" class="fieldName">
								<input type="text" name="registrationExpiryDate[]" id="registrationExpiryDate1" autocomplete="off" />
							</td>
							<td align="center" class="fieldName">
								<a onclick="setIssuanceItemStatusWeight('0');" href="javascript:void(0);">
									<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item" />
								</a>
								<input type="hidden" value="" id="mstatus_0" name="mstatus_0">
								<input type="hidden" value="N" id="IsFromDB_0" name="IsFromDB_0">
								<input type="hidden" value="" id="mrmId_0" name="mrmId_0">
							</td>
						</tr>
						<?php
							}
						?>
					</table>									
					<!--<table cellpadding="0"  cellspacing="1" border="1"   width="100%"  align="center" bgcolor="#999999">
						<tr>
							<td class="listing-head" nowrap>Registration Type</td>
							<td class="listing-head" nowrap>Registration No</td>
							<td class="listing-head" nowrap>Registration Date</td>
							<td class="listing-head" nowrap>Registration Expiry Date</td>
							<td class="listing-head" nowrap></td>
						</tr>
						<tr>
							<td  height="10" >
								<select name="registrationType">
									<option value="">--select--</option>
									<?php 
										foreach($registrationTypeRecords as $registration)
										{
											$registrationTypeId		=	$registration[0];
											$registrationTypeName	=	stripSlash($registration[1]);
											$selected = ($registrationType==$registrationTypeId)?"selected":""			
									?>
											<option value="<?=$registrationTypeId?>" <?=$selected?>><?=$registrationTypeName?></option>
									<? }?>
								</select>										      
							</td>
							<td><INPUT TYPE="text" NAME="registrationNo" size="15" value="<?=$registrationNo;?>"></td>
							<TD>
								<input type="text" name="registrationDate" id="registrationDate" size="9" value="<?=$registrationDate;?>" autocomplete="off" />
							</TD>
							<TD>
								<input type="text" name="registrationExpiryDate" id="registrationExpiryDate" size="9" value="<?=$registrationExpiryDate;?>" autocomplete="off" />
							</TD>
						</tr>
					</table>-->
					<?php
								// require("template/rbBottom.php");
							?>
				</td>
				
				</tr>
				
										<!--	<tr>
												<td class="fieldName" nowrap >*Registration Date</td>
												
												<TD>
													<input type="text" name="registrationDate" id="registrationDate" size="9" value="<?=$registrationDate;?>" autocomplete="off" />
												</TD>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Registration Expiry Date</td>
												<TD>
													<input type="text" name="registrationExpiryDate" id="registrationExpiryDate" size="9" value="<?=$registrationExpiryDate;?>" autocomplete="off" />
												</TD>
												
											</tr>-->
											
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
			<td colspan="1"  style="margin-left:5px;" align="left">
			<!--<td nowrap="" style="padding-left:5px; padding-right:5px;">-->
				<a href="javascript:void(0);" id="addRow" onclick="javascript:addNewPondRegMultipleRow();" class="link1" title="Click here to add new item.">
					<img border="0" src="images/addIcon.gif" style="border:none;padding-right:4px;vertical-align:middle;">Add New Item
				</a>
			</td>
		</tr>
		
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PondMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPondMaster(document.frmPondMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PondMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPondMaster(document.frmPondMaster);">&nbsp;&nbsp;												
												<input type="submit" name="cmdSaveAddNew" class="button" value="Save Add New" onClick="return validateAddPondMaster(document.frmPondMaster);">
												</td>

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
			
			# Listing Pond Master Starts
		?>
	</table>
	</td>
	</tr>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Department </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=sizeof($pondMasterRecords);?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPondMaster.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
			if ( sizeof($pondMasterRecords) > 0 ) {
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
      				$nav.= " <a href=\"PondMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PondMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PondMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Farm Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Location</th>	
		<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;">Registration No</th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Registration Expiry Date</th>-->
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Farm Qty </th>
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach($pondMasterRecords as $cr) {
		$i++;
		$pondmasterId		=	$cr[0];
		 $pondName		=	stripSlash($cr[1]);
		 $suppliercode	=	stripSlash($cr[2]);
		 $supplier1=$supplierMasterObj->fetchSupplier($suppliercode);
		foreach($supplier1 as $supplier)
		 $alloteeName		=	stripSlash($cr[3]);
		 $address		=	stripSlash($cr[4]);
		 $statecode		=	stripSlash($cr[5]);
		 $state1=$stateMasterObj->fetchState($statecode);
		foreach($state1 as $state)
		 $district		=	stripSlash($cr[6]);
		 $taluk		=	stripSlash($cr[7]);
		 $village		=	stripSlash($cr[8]);
		
		 $location		=	stripSlash($cr[9]);
		 $locationType1=$landingcenterObj->fetchLocationType($location);
		 foreach($locationType1 as $locationType)
		 $registrationTypecode		=	stripSlash($cr[10]);
		 $registrationType1=$registrationTypeObj->fetchRegistartionType($registrationTypecode);
		foreach($registrationType1 as $registrationType)
		 // $registrationNo		=	stripSlash($cr[11]);
		 // $registrationDate		=	dateformat($cr[12]);
		 // $registrationExpiryDate		=dateformat($cr[13]);
		 $pondSize		=	stripSlash($cr[10]);
		 $pondSizeUnitcode		=	stripSlash($cr[11]);
		 //$pondSizeUnit1=$areaObj->fetchPondSizeUnit($pondSizeUnitcode);
		 $pondSizeUnit1=$stockItemUnitObj->fetchUnit($pondSizeUnitcode);
		foreach($pondSizeUnit1 as $pondSizeUnit)
		 $pondQty		=	stripSlash($cr[12]);
		 
		 $registrationDate=($cr[13]);
		  $todays_date = date("Y-m-d");
		 $currentDate = strtotime($todays_date);
		 $expiryDate=strtotime($registrationDate);
		 $timeDiff = abs($expiryDate - $currentDate);

		$numberDays = $timeDiff/86400;  // 86400 seconds in one day

		// and you might want to convert to integer
		 $numberDays = intval($numberDays);
		 
		 
		 
		 $active=$cr[13];
		 $pondNameMouseOver = "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><th>Registration Type</th><th>Registration No</th><th>Registration Date</th><th>Registration Expiry Date</th></tr>";
		 $registrationTypes = array();$registrationNos = array();
		 $registrationDates = array();$registrationExDates = array();
		 if($cr[14] != '')
		 {
			$registrations = explode(',',$cr[14]);
			if(sizeof($registrations) > 0)
			{
				foreach($registrations as $reg)
				{
					$recodrs = explode('$$',$reg);
					if(isset($recodrs[0]))
						$registrationTypes[]   = $recodrs[0];
						
					if(isset($recodrs[1]))
						$registrationNos[]     = $recodrs[1];
					
					if(isset($recodrs[2]))
						$registrationDates[]   = $recodrs[2];
					
					if(isset($recodrs[3]))
						$registrationExDates[] = $recodrs[3];
				}
			}
		}
		
		if(sizeof($registrationTypes) > 0)
		{	
			$j = 0;
			foreach($registrationTypes as $regType)
			{
				$pondNameMouseOver.= "<tr bgcolor=#fffbcc>";
				$pondNameMouseOver.= "<td class=listing-item>".$regType."</td>";
				if(isset($registrationNos[$j]))
				{	
					$pondNameMouseOver.= "<td class=listing-item>".$registrationNos[$j]."</td>";
				}
				else
				{
					$pondNameMouseOver.= "<td class=listing-item> &nbsp; </td>";
				}
				if(isset($registrationDates[$j]))
				{	
					$registrationDatesDisVals = explode('-',$registrationDates[$j]);
					$registrationDatesDis = $registrationDatesDisVals[2].'/'.$registrationDatesDisVals[1].'/'.$registrationDatesDisVals[0];
					$pondNameMouseOver.= "<td class=listing-item>".$registrationDatesDis."</td>";
				}
				else
				{
					$pondNameMouseOver.= "<td class=listing-item> &nbsp; </td>";
				}
				if(isset($registrationExDates[$j]))
				{	
					$registrationExDatesDisVals = explode('-',$registrationExDates[$j]);
					$registrationExDatesDis = $registrationExDatesDisVals[2].'/'.$registrationExDatesDisVals[1].'/'.$registrationExDatesDisVals[0];
					$pondNameMouseOver.= "<td class=listing-item>".$registrationExDatesDis."</td>";
				}
				else
				{
					$pondNameMouseOver.= "<td class=listing-item> &nbsp; </td>";
				}
				$pondNameMouseOver.= "</tr>";
				$j++;
			}
			
		}	
		else
		{
			$pondNameMouseOver.= "<tr bgcolor=#fffbcc><td class=listing-item align=center colspan=4> No registration details found </td></tr>";
		}
		$pondNameMouseOver.= "</table>";
		$existingrecords=$cr[14];
		$locationHover="Address:$address<br>"; 
		$locationHover.="State:$state[0]<br>";
		$locationHover.="District:$district<br>";
		$locationHover.="Taluk:$taluk<br>";
		$locationHover.="Village:$village<br>";
		
		$registrationHover="Registration Type:$registrationType[0]<br>";
		$registrationHover.="Registration Date:$registrationDate<br>";
		
		$pondHover="Farm Size:$pondSize<br>";
		$pondHover.="Farm Unit:$pondSizeUnit[0]<br>";
	?>
		
							
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$pondmasterId;?>" ></td>
		<?php
			if($pondNameMouseOver != '')
			{
		?>
			<td onMouseOver="ShowTip('<?=$pondNameMouseOver?>');" onMouseOut="UnTip();" class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pondName;?></td>
		<?php
			}
			else
			{
		?>
			<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pondName;?></td>
		<?php
			}
		?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" onMouseOver="ShowTip('Allotee Name:<?=$alloteeName;?>');" onMouseOut="UnTip();"><?=$supplier[0];?></td>	
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" onMouseOver="ShowTip('<?=$locationHover;?>');" onMouseOut="UnTip();"><?=$locationType[0]?></td>
		<!--<td class="listing-item" style="padding-left:10px; padding-right:10px;" onMouseOver="ShowTip('<?=$registrationHover;?>');" onMouseOut="UnTip();"><?=$registrationNo?></td>	
			<?php if($expiryDate < $currentDate)
			{  ?>
		<td class="listing-item" style="padding-left:10px; padding-right:10px; background-color:red;"><?=$registrationExpiryDate?></td>	
			<?php 		
				} else if ($numberDays<7) {?>
			  <td class="listing-item" style="padding-left:10px; padding-right:10px; background-color:orange;"><?=$registrationExpiryDate?></td>
			<?php } else { ?>
			  <td class="listing-item" style="padding-left:10px; padding-right:10px; "><?=$registrationExpiryDate?></td>
			<?php }  ?>-->
			
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" onMouseOver="ShowTip('<?=$pondHover;?>');" onMouseOut="UnTip();"><?=$pondQty?></td>
		
		<? if($edit==true){  ?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$pondmasterId;?>,'editId'); this.form.action='PondMaster.php';"  >
			<?php }
			?>
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?> 
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$pondmasterId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$pondmasterId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"PondMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PondMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PondMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=sizeof($pondMasterRecords);?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPondMaster.php',700,600);"><? }?></td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "registrationDate1",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "registrationDate1", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "registrationExpiryDate1",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "registrationExpiryDate1", 
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