<?php
	require("include/include.php");
	require_once('lib/ManageUser_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	# Add Category Start 
	if( $p["cmdAddNew"]!="") $addMode	= true;

	# Add		
	if ($p["cmdAddUser"]!="") {
		
		$addUserName		= addSlash(trim($p["userName"]));
		$addUserPassword	= addSlash(trim($p["userPassword"]));	
		$selRole		= $p["selRole"];
		$defaultCompany		= $p["defaultCompany"];
		$hidUnitRowCount		= $p["hidUnitRowCount"];
		
		if ($addUserName!="" && $addUserPassword!="" && $selRole!="") 
		{
			$PWord	=	$userObj->getEncodedString($addUserPassword);				
			$userRecIns	=	$manageusersObj->addUser($addUserName,$PWord,$defaultCompany,$selRole);
			$lastId = $databaseConnect->getLastInsertedId();
			for($i=0; $i<$hidUnitRowCount; $i++)
			{
				$Status		= $p["Status_".$i];
				if($Status!='N')
				{
					$company		= $p["company_".$i];
					$unit			= $p["unit_".$i];
					$department		= $p["department_".$i];
					$userRecInsDetail	=	$manageusersObj->addUserDetail($lastId,$company,$unit,$department);	
				}
			}


				
			if ($userRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddUser);
				$sessObj->createSession("nextPage",$url_afterAddUser);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddUser;
			}
			$userRecIns	=	false;			
		}
	}
	

	# Edit User
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$userRec		=	$manageusersObj->find($editId);
		
		$editUserId		=	$userRec[0];
		$editUserName		=	stripSlash($userRec[1]);
		$editUserPWord		=	$userObj->getDecodedString($userRec[2]);
		
		if ($p["editSelectionChange"]=='1'|| $p["selRole"]=="") {
			$selRole	= $userRec[3];
		} else {
			$selRole	=	$p["selRole"];
		}
		$defaultCompany=$userRec[4];
		$userDetails=$manageusersObj->findDetails($editId);
		
	}
	
	# Cmd Save change
	if ($p["cmdSaveChange"]!="") {

		$userEditId		= $p["hidUserId"];
		
		$upUserName		= addSlash(trim($p["userName"]));
		$upUserPassword		= addSlash(trim($p["userPassword"]));	
		$selRole		= $p["selRole"];
		$defaultCompany		= $p["defaultCompany"];
		$hidUnitRowCount		= $p["hidUnitRowCount"];
		
		if ($upUserName!="" && $upUserPassword!="" && $selRole!="" && $userEditId!="") {
			$PWord			=	$userObj->getEncodedString($upUserPassword);
			$userRecUptd	=	$manageusersObj->updateUser($upUserName,$PWord,$selRole,$defaultCompany,$userEditId);
			for($i=0; $i<$hidUnitRowCount; $i++)
			{
				$status		= $p["Status_".$i];
				$editUserDetail= $p["editUserDetail_".$i];
				if($status!='N')
				{
					$company		= $p["company_".$i];
					$unit			= $p["unit_".$i];
					$department		= $p["department_".$i];
					if($editUserDetail!='')
					{
						$userRecInsDetail	=	$manageusersObj->updateUserDetail($userEditId,$company,$unit,$department,$editUserDetail);	
					}
					else if($editUserDetail=="")
					{
						$userRecInsDetail	=	$manageusersObj->addUserDetail($userEditId,$company,$unit,$department);	
					}
				}

				if ($status=='N' && $editUserDetail!="") {
				 $deluserRec = $manageusersObj->delUserDetailRec($editUserDetail);
				}


			}

		}
	
		if ($userRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateUser);
			$sessObj->createSession("nextPage",$url_afterUpdateUser);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateUser;
		}
		$userRecUptd	=	false;
	}
	
	
	# Delete User
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$userId	=	$p["delId_".$i];

			if ($userId!="") {
				// Checking the selected User is link with any other process
				$recInUse = $manageusersObj->userRecInUse($userId);
				if (!$recInUse) {
					$userRecDel = $manageusersObj->deleteUser($userId);
					$userDetailRecDel = $manageusersObj->delAllUserDetail($userId);
				}
			}
		}

		if ($userRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelUser);
			$sessObj->createSession("nextPage",$url_afterDelUser);
		} else {
			$errDel	=	$msg_failDelUser;
		}
		$userRecDel	=	false;
	}

	
	if ($addMode || $editMode) {

		#List All Role
		$roleRecords	= $manageroleObj->fetchAllRecords();

		#Get All Function Records
		$getFunctionRecords = $manageroleObj->fetchAllFunctionRecords();
	}

	#List all Module
	//$moduleRecords	= $manageroleObj->fetchAllModuleRecords();

	#List All Users
	$userRecords		= $manageusersObj->fetchAllRecords();
	$userRecordsSize	= sizeof($userRecords);

	$companyRecs		= $billingCompanyObj->fetchAllRecordsActivebillingCompany();
	//$unitRecs			= $plantandunitObj->fetchAllRecordsPlantsActive();
	$departmentRecs			= $departmentObj->fetchAllRecordsActivedept();
	

list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
	//printr($companyRecords);
	//echo "<br/>";
	//printr($unitRecords);
	//echo "<br/>";
	//printr($departmentRecords);
	//echo "<br/>";
	if ($editMode)	$heading	=	$label_editUser;
	else $heading	=	$label_addUser;
	

	//$help_lnk="help/hlp_GradeMaster.html";
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/manageusers.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmManageUsers" action="ManageUsers.php" method="post">
<? if($editMode==true) {?>
	<table cellspacing="0"  align="center" cellpadding="0" width="85%">
	<? } else {?>
    <table cellspacing="0"  align="center" cellpadding="0" width="50%">
	<? }?>
		<tr> 
			<td height="10" align="center">&nbsp;</td>
		</tr>
		<tr> 
		  <td height="10" align="center" class="err1" > 
			<? if($err!="" ){?>
			<?=$err;?>
			<?}?>
		  </td>
		</tr>
		<?
		if( $editMode || $addMode)
		{
		?>
		<tr> 
			<td> 
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr> 
						<td   bgcolor="white"> 
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr> 
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; 
									<?=$heading;?>
									</td>
								</tr>
								<tr> 
									<td width="1" ></td>
									<td colspan="2" > 
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr> 
												<td colspan="2" height="10" ></td>
											</tr>
											<tr> 
												<? if($editMode){?>
												<td colspan="2" align="center"> <input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ManageUsers.php');"> 
												  &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddUser(document.frmManageUsers);">                       
												</td>
												<?} else{?>
												<td  colspan="2" align="center"> <input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ManageUsers.php');"> 
												&nbsp;&nbsp; <input type="submit" name="cmdAddUser" class="button" value=" Add " onClick="return validateAddUser(document.frmManageUsers);">                        </td>
												<?}?>
											</tr>
												<input type="hidden" name="hidUserId" value="<?=$editUserId;?>">
											<tr>
												<td class="fieldName" >&nbsp;</td>
												<td >&nbsp;</td>
											</tr>
											<tr>
												<td  height="10" colspan="2" class="fieldName" >
													<table width="200">
														<tr>
															<td class="fieldName" nowrap>* Username</td>
															<td><input name="userName" type="text" id="userName" value="<?=$editUserName;?>" size="20" /></td>
														</tr>
														<tr>
															<td class="fieldName" nowrap>* Password</td>
															<td><input name="userPassword" type="password" id="userPassword" value="<?=$editUserPWord;?>" size="20" /></td>
														</tr>
														<tr>
															<td class="fieldName" nowrap>* Retype Password</td>
															<td><input name="userRePassword" type="password" id="userRePassword" value="<?=$editUserPWord;?>" size="20" /></td>
														</tr>
														<tr>
															<td class="fieldName" nowrap>* Role </td>
															<td>
																<?
																//if($selRole=="") $selRole	=	$p["selRole"];
																if($addMode==true){
																?>
																<select name="selRole" id="selRole">
																<? } else {?>
																<select name="selRole" id="selRole"  onChange="this.form.editId.value=<?=$editId?>;this.form.submit();">
																<? }?>
																	<option value="">--Select--</option>
																	<?
																	foreach($roleRecords as $rrec)
																	{
																	$roleId				=	$rrec[0];
																	$roleName			=	stripSlash($rrec[1]);
																	$roleDescription	=	stripSlash($rrec[2]);
																	$selected	=	"";
																	if($selRole==$roleId) $selected="Selected";
																	?>
																	<option value="<?=$roleId?>" <?=$selected?>>
																	<?=$roleName?>
																	</option>
																	<? }?>
																</select>
															</td>
														</tr>
														<tr>
															<td class="fieldName" nowrap>Default Company</td>
															<td><select id="defaultCompany" name="defaultCompany">
																	<option value="0">--Select--</option>
																	<?php
																	if (sizeof($companyRecs)>0) {	
																		foreach ($companyRecs as $cr) {
																			$companyIds		= $cr[0];
																			$companyName	= stripSlash($cr[9]);
																			$sel  = ($defaultCompany==$companyIds)?"Selected":"";
																	?>	
																	<option value="<?=$companyIds?>" <?=$sel?>><?=$companyName?></option>
																	<?
																	} }
																	?>
																</select>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="4" height="10">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="4" align="left">
													<table width="20%" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblUser" name="tblUser">
														<tr bgcolor="#f2f2f2" align="center">
															<td class="listing-head" nowrap>Company name </td>
															<td class="listing-head" nowrap>Unit </td>
															<td class="listing-head" nowrap>Department </td>
															<td></td>
														</tr>
														<?php if (sizeof($userDetails)>0){
														$hidTableRowCountedit=sizeof($userDetails);
														$p=0;
																						
														foreach($userDetails as $ud) {	
														$editUserDetail 	= $ud[0];
														$companyId	=$ud[2];
														$unitId	=$ud[3];
														$departmentId	=$ud[4];
														$unitRecs=$manageusersObj->getUserCompany($companyId);									
														?>
														<tr id="Row_<?=$p?>" class="whiteRow" align="center">
															<td id="srNo_<?=$p?>" class="listing-item" align="center">
																<select id="company_<?=$p?>" onchange="xajax_addNewRow(document.getElementById('company_<?=$p?>').value,document.getElementById('unit_<?=$p?>').value,document.getElementById('department_<?=$p?>').value); xajax_getAllUnits(document.getElementById('company_<?=$p?>').value,'<?=$p?>')" name="company_<?=$p?>">
																	<option value="0">--Select All--</option>
																	<?php
																	if (sizeof($companyRecs)>0) {	
																		foreach ($companyRecs as $cr) {
																			$companyIds		= $cr[0];
																			$companyName	= stripSlash($cr[9]);
																			$sel  = ($companyId==$companyIds)?"Selected":"";
																	?>	
																	<option value="<?=$companyIds?>" <?=$sel?>><?=$companyName?></option>
																	<?
																	} }
																	?>
																</select>
															</td>
															<td>
																<select id="unit_<?=$p?>" onchange="xajax_addNewRow(document.getElementById('company_<?=$p?>').value,document.getElementById('unit_<?=$p?>').value,document.getElementById('department_<?=$p?>').value);" name="unit_<?=$p?>">
																	<option value="0">--Select All--</option>
																	<?php
																	if (sizeof($unitRecs)>0) {	
																		foreach ($unitRecs as $ur) {
																			$unitIds		= $ur[0];
																			$unitName	= stripSlash($ur[1]);
																			$sel  = ($unitId==$unitIds)?"Selected":"";
																	?>	
																	<option value="<?=$unitIds?>" <?=$sel?>><?=$unitName?></option>
																	<?
																	} }
																	?>
																</select>
															</td>
															<td>
																<select id="department_<?=$p?>" onchange="xajax_addNewRow(document.getElementById('company_<?=$p?>').value,document.getElementById('unit_<?=$p?>').value,document.getElementById('department_<?=$p?>').value);" name="department_<?=$p?>">
																	<option value="0">--Select All--</option>
																	<?php
																	if (sizeof($departmentRecs)>0) {	
																		foreach($departmentRecs as $dr) {
																			$departmentIds		= $dr[0];
																			$departmentName	= stripSlash($dr[1]);
																			$sel  = ($departmentId==$departmentIds)?"Selected":"";
																	?>	
																	<option value="<?=$departmentIds?>" <?=$sel?>><?=$departmentName?></option>
																	<?
																	} }
																	?>
																</select>
															</td>
														
															<td class="listing-item" align="center">
																<a onclick="setTestRowItemStatusVal('<?=$p?>');" href="###">
																	<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
																</a>
																<input id="Status_<?=$p?>" type="hidden" value="" name="Status_<?=$p?>">
																<input id="IsFromDB_<?=$p?>" type="hidden" value="N" name="IsFromDB_<?=$p?>">
																<input id="editUserDetail_<?=$p?>" type="hidden" value="<?=$editUserDetail?>" name="editUserDetail_<?=$p?>">
																<input type='hidden' name="hidTableRowCountedit" id="hidTableRowCountedit" value="<?=$hidTableRowCountedit?>">
															</td>
														</tr>
														<?php
														$p++;	
														}
														}
														?>
														<input type='hidden' name="hidTableRowCountedit" id="hidTableRowCountedit" value="<?=$p?>">	
													</table>
												</td>
											</tr>
											<tr>
												<td height="10">&nbsp;</td>
											</tr>
												<input type='hidden' name="hidUnitRowCount" id="hidUnitRowCount" value="<?=$p?>">
											<tr id="rowUser" <? if($addMode){?> style="display:none"<? } ?>>
												
												<TD style="padding-left:5px;padding-right:5px;">
													<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to duplicate value."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New<!--(Copy)--></a>
												</TD>
												<TD style="padding-left:5px;padding-right:5px;">
												</TD>
											</tr>
											
											<tr>
												<td  height="10" class="fieldName" ></td>
												<td  height="10" ></td>
											</tr>
											<? if($editMode==true){?>
											<tr>
												<td  height="10" colspan="2" class="fieldName">&nbsp;<img src="images/y.gif"> - Shows the User Control.</td>
											</tr>
											<!-- 	New Start Here	 -->
											<tr>
												<TD style="padding-left:5px;padding-right:5px;">
													<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999">
													<?
													$k=0;
													$prevModuleId=0;
													$prevPmenu_id = 0;

													//$j=0;
													$count = 0;
													foreach($getFunctionRecords as $gfr) 
													{
														//$j++;
														$functionId = $gfr[0];
														$moduleId = $gfr[2];
														$pmenu_id = $gfr[10];
														$functionName = $gfr[1];
														$extraflag1=$gfr[13];
														$selActive	=	"";
														$selFunction	=	"";
														$moduleName = "";
														if ($prevModuleId!=$moduleId || $prevPmenu_id!=$pmenu_id) {
														if ( $k > 0) {
													?>
														<input type="hidden" name="hidRowCount1_<?=$k?>" id="hidRowCount1_<?=$k?>" value="<?=$j-1;?>">
													<?
													}
														$j=1;
														$k++;
														$moduleName = $gfr[11];
														$subMenu = $manageroleObj->findSubMenu($pmenu_id);
														$selActive	=	"";
														$selFunction	=	"";
													if ($editMode==true) {
														$roleRec = $manageroleObj->findRoleRecs($moduleId, 0, $selRole, $pmenu_id);
														$roleFunctionId	=	$roleRec[0];
														if ($roleRec[3]==0 && $roleRec[3]!="") $selFunction = "Checked";
														else  $selFunction	=	"";
														if($roleRec[4]=='Y')	$selAccess	= 	"Checked";
														else $selAccess	=	"";

														if ($roleRec[5]=='Y')	$selAdd		=	"Checked";
														else $selAdd	=	"";

														if ($roleRec[6]=='Y')	$selEdit	=	"Checked";
														else $selEdit	= 	"";

														if ($roleRec[7]=='Y') $selPrint	=	"Checked";
														else $selPrint	=	"";

														if ($roleRec[8]=='Y')	$selDelete	=	"Checked";
														else $selDelete	=	"";

														if ($roleRec[9]=='Y')	$selConfirm	=	"Checked";
														else $selConfirm	= "";

														if ($roleRec[10]=='Y')	$selActive	=	"Checked";
														else $selActive	= "";

														if ($roleRec[11]=='Y')	$selReEdit	=	"Checked";
														else $selReEdit	= "";
																	
														if ($roleRec[12]=='Y')	$selCompanySpecific	=	"Checked";
														else $selCompanySpecific	= "";

														$selAll = "";
														if ($roleRec[3]==0 && $roleRec[3]!="" && $roleRec[5]=='Y' && $roleRec[6]=='Y' && $roleRec[7]=='Y' && $roleRec[8]=='Y' && $roleRec[9]=='Y' && $roleRec[11]=='Y') $selAll = "Checked";
														else $selAll = "";
													}
													?>
													<tr bgcolor="#f2f2f2" align="center">
														<td class="listing-head">Module</td>
														<td class="listing-head">Sub Menu</td>
														<td class="listing-head">Function</td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Access</td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Add</td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Edit</td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Del</td>
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Print</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Confirm</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Re-Edit</td>
														<td class="listing-head" style="padding-left:3px; padding-right:3px;">Company <br/>Specific</td>
													</tr>
													<tr bgcolor="#FFFFFF">
														<td class="listing-item" style="padding-left:5px;" height="25"><?=$moduleName?><input type="hidden" name="moduleId_<?=$k?>" value="<?=$moduleId?>"><input type="hidden" name="subModuleId_<?=$k?>" value="<?=$pmenu_id?>"></td>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="25"><?=$subMenu?></td>
														<td class="listing-item" style="padding-left:5px;" nowrap="nowrap"><? if($selFunction) {?> <img src="images/y.gif" /><? } else {?><img src="images/x.gif" /><? }?>&nbsp;All Function</td>
														<td class="listing-item" align="center">
															<? if($selAccess) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selAdd) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selEdit) {?>
														  <img src="images/y.gif" />
														  <? } else {?>
														  <img src="images/x.gif" />
														  <? }?>
														</td>
														<td class="listing-item" align="center"><? if($selDelete) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selPrint) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center">
														<? if($selConfirm) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selReEdit) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selCompanySpecific) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
													</tr>
													<?
													}

													$selActive = "";
													//if($addMode==true) $selActive	=	"Checked";
													if ($editMode==true) {
														$roleRec = $manageroleObj->findRoleRecs($moduleId, $functionId, $selRole, $pmenu_id);
														$roleFunctionId	=	$roleRec[0];

														if ($roleRec[3]==$functionId) $selFunction = "Checked";
														else $selFunction	=	"";

														if ($roleRec[4]=='Y')	$selAccess	= 	"Checked";
														else $selAccess	=	"";

														if ($roleRec[5]=='Y')	$selAdd		=	"Checked";
														else $selAdd	=	"";

														if ($roleRec[6]=='Y')	$selEdit	=	"Checked";
														else $selEdit	= 	"";

														if ($roleRec[7]=='Y') $selPrint	=	"Checked";
														else $selPrint	=	"";

														if ($roleRec[8]=='Y')	$selDelete	=	"Checked";
														else $selDelete	=	"";

														if ($roleRec[9]=='Y')	$selConfirm	=	"Checked";
														else $selConfirm	= "";

														if ($roleRec[10]=='Y')	$selActive	=	"Checked";
														else $selActive	= "";

														if ($roleRec[11]=='Y')	$selReEdit	=	"Checked";
														else $selReEdit	= "";

														if ($roleRec[12]=='Y')	$selCompanySpecific	=	"Checked";
														else $selCompanySpecific	= "";
													}

													$selAll = "";
													if ($roleRec[3]==$functionId && $roleRec[5]=='Y' && $roleRec[6]=='Y' && $roleRec[7]=='Y' && $roleRec[8]=='Y' && $roleRec[9]=='Y' && $roleRec[11]=='Y') $selAll = "Checked";
													else $selAll = "";

													$formAdd 	= $fr[4];
													$displayAdd = "";
													if ($formAdd=='N') {
														$displayAdd = "hidden";
													} else {
														$displayAdd = "Checkbox";
													}

													$formEdit 	= $fr[5];
													$displayEdit = "";
													if ($formEdit=='N') {
														$displayEdit = "hidden";
													} else {
														$displayEdit = "Checkbox";
													}

													$formDelete	= $fr[6];
													$displayDelete = "";
													if ($formDelete=='N') {
														$displayDelete = "hidden";
													} else {
														$displayDelete = "Checkbox";
													}

													$formPrint 	= $fr[7];
													$displayPrint = "";
													if ($formPrint=='N') {
														$displayPrint = "hidden";
													} else {
														$displayPrint = "Checkbox";
													}

													$formConfirm = $fr[8];
													$displayConfirm = "";
													if ($formConfirm=='N') {
														$displayConfirm = "hidden";
													} else {
														$displayConfirm = "Checkbox";
													}

													$formReedit  = $fr[9];
													$displayReEdit = "";
													if ($formReedit=='N') {
														$displayReEdit = "hidden";
													} else {
														$displayReEdit = "Checkbox";
													}
													?>
													<tr bgcolor="#FFFFFF">
														<td class="listing-item" style="padding-left:5px;" height="25"></td>
														<td class="listing-item" style="padding-left:5px;" height="25"><?//=$displaySubMenu?></td>
														<td class="listing-item" style="padding-left:5px;">
														<? if($selFunction) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														  &nbsp;<?=$functionName?>
														</td>
														<td class="listing-item" align="center"><? if($selAccess) {?>
														  <img src="images/y.gif" />
														  <? } else {?>
														  <img src="images/x.gif" />
														  <? }?>
														</td>
														<td class="listing-item" align="center"><? if($selAdd) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selEdit) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selDelete) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selPrint) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selConfirm) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selReEdit) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>
														<td class="listing-item" align="center"><? if($selCompanySpecific) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														</td>	
													</tr>		
													<?php
													if ($functionId==162){
													// $moduleId, $functionId, $roleId, $subModuleId
													$roleRecsup = $manageroleObj->findRoleRecssup($moduleId, $functionId, $selRole, $pmenu_id);
													foreach ($roleRecsup as $rrS) 
													{
														//echo "entered";
														if ($rrS[13]=="INV")
														{
															if($rrS[4]=='Y')	$selAccess1	= 	"Checked";
															else $selAccess1	=	"";

															if ($rrS[5]=='Y')	$selAdd1		=	"Checked";
															else $selAdd1	=	"";

															if ($rrS[6]=='Y')	$selEdit1	=	"Checked";
															else $selEdit1	= 	"";

															if ($rrS[7]=='Y') $selPrint1	=	"Checked";
															else $selPrint1	=	"";

															if ($rrS[8]=='Y')	$selDelete1	=	"Checked";
															else $selDelete1	=	"";

															if ($rrS[9]=='Y')	$selConfirm1	=	"Checked";
															else $selConfirm1	= "";

															if ($rrS[10]=='Y')	$selActive1	=	"Checked";
															else $selActive1	= "";

															if ($rrS[11]=='Y')	$selReEdit1	=	"Checked";
															else $selReEdit1	= "";
														
															if ($rrS[12]=='Y')	$selCompanySpecific1	=	"Checked";
															else $selCompanySpecific1	= "";

														}

														if ($rrS[13]=="FRN")
														{
															if($rrS[4]=='Y')	$selAccess2	= 	"Checked";
															else $selAccess2	=	"";

															if ($rrS[5]=='Y')	$selAdd2		=	"Checked";
															else $selAdd2	=	"";

															if ($rrS[6]=='Y')	$selEdit2	=	"Checked";
															else $selEdit2	= 	"";

															if ($rrS[7]=='Y') $selPrint2	=	"Checked";
															else $selPrint2	=	"";

															if ($rrS[8]=='Y')	$selDelete2	=	"Checked";
															else $selDelete2	=	"";

															if ($rrS[9]=='Y')	$selConfirm2	=	"Checked";
															else $selConfirm2	= "";

															if ($rrS[10]=='Y')	$selActive2	=	"Checked";
															else $selActive2	= "";

															if ($rrS[11]=='Y')	$selReEdit2	=	"Checked";
															else $selReEdit2	= "";
															
															if ($rrS[12]=='Y')	$selCompanySpecific2	=	"Checked";
															else $selCompanySpecific2	= "";
														}

														if ($rrS[13]=="RTE")
														{
															if($rrS[4]=='Y')	$selAccess3	= 	"Checked";
															else $selAccess3	=	"";

															if ($rrS[5]=='Y')	$selAdd3		=	"Checked";
															else $selAdd3	=	"";

															if ($rrS[6]=='Y')	$selEdit3	=	"Checked";
															else $selEdit3	= 	"";

															if ($rrS[7]=='Y') $selPrint3	=	"Checked";
															else $selPrint3	=	"";

															if ($rrS[8]=='Y')	$selDelete3	=	"Checked";
															else $selDelete3	=	"";

															if ($rrS[9]=='Y')	$selConfirm3	=	"Checked";
															else $selConfirm3	= "";

															if ($rrS[10]=='Y')	$selActive3	=	"Checked";
															else $selActive3	= "";

															if ($rrS[11]=='Y')	$selReEdit3	=	"Checked";
															else $selReEdit3	= "";
															
															if ($rrS[12]=='Y')	$selCompanySpecific3	=	"Checked";
															else $selCompanySpecific3	= "";
														}

													}
													?>
													<tr bgcolor="#FFFFFF">
														<td class="listing-item" style="padding-left:5px;" height="25"></td>
														<td class="listing-item" style="padding-left:5px;" height="25"><?//=$displaySubMenu?></td>
														<td class="listing-item" style="padding-left:5px;">
															<? if($selFunction) {?>
															<img src="images/y.gif" />
															<? } else {?>
															<img src="images/x.gif" />
															<? }?>
														  &nbsp;<?=$functionName?>-Inventory
													</td>
													<td class="listing-item" align="center"><? if($selAccess1) {?>
													  <img src="images/y.gif" />
													  <? } else {?>
													  <img src="images/x.gif" />
													  <? }?>
													</td>
													<td class="listing-item" align="center"><? if($selAdd1) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selEdit1) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selDelete1) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selPrint1) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selConfirm1) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selReEdit1) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selCompanySpecific1) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>	
												</tr>	
												<tr bgcolor="#FFFFFF">
													<td class="listing-item" style="padding-left:5px;" height="25"></td>
													<td class="listing-item" style="padding-left:5px;" height="25"><?//=$displaySubMenu?></td>
													<td class="listing-item" style="padding-left:5px;">
														<? if($selFunction) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													  &nbsp;<?=$functionName?>-Frozen
													</td>
													<td class="listing-item" align="center">
														<? if($selAccess2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selAdd2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selEdit2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selDelete2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selPrint2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selConfirm2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selReEdit2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selCompanySpecific2) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>	
												</tr>		
												<tr bgcolor="#FFFFFF">
													<td class="listing-item" style="padding-left:5px;" height="25"></td>
													<td class="listing-item" style="padding-left:5px;" height="25"><?//=$displaySubMenu?></td>
													<td class="listing-item" style="padding-left:5px;">
														<? if($selFunction) {?>
														<img src="images/y.gif" />
														<? } else {?>
														 <img src="images/x.gif" />
														<? }?>
														&nbsp;<?=$functionName?>-RTE
													</td>
													<td class="listing-item" align="center">
														<? if($selAccess3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selAdd3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selEdit3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selDelete3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selPrint3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selConfirm3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selReEdit3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>
													<td class="listing-item" align="center">
														<? if($selCompanySpecific3) {?>
														<img src="images/y.gif" />
														<? } else {?>
														<img src="images/x.gif" />
														<? }?>
													</td>	
												</tr>		
												<?php }  ?>
												<?
											
												$prevPmenu_id = $pmenu_id;
												$prevModuleId=$moduleId;
												$j++;
												 }
												?>
											</table>
										</TD>
									</tr>
									<!-- 	New End Here	 -->
									<? }?>
									<tr> 
										<td colspan="2"  height="10" ></td>
									</tr>
									<tr> 
										<? if($editMode){?>
										<td colspan="2" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageUsers.php');"> 
										&nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddUser(document.frmManageUsers);">                        </td>
										<?} else{?>
										<td  colspan="2" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageUsers.php');"> 
										&nbsp;&nbsp; <input type="submit" name="cmdAddUser" class="button" value=" Add " onClick="return validateAddUser(document.frmManageUsers);">                        </td>
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
        <!-- Form fields end   -->
	</td>
 </tr>
 <?
 }
?>
<tr> 
	<td height="10" align="center" ></td>
</tr>
<tr> 
    <td> 
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
			<tr> 
				<td   bgcolor="white"> 
					<!-- Form fields start -->
					<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						<tr> 
							<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
							<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage 
							Users </td>
						</tr>
						<tr> 
							<td colspan="3" height="10" ></td>
						</tr>
						<tr> 
							<td colspan="3"> 
								<table cellpadding="0" cellspacing="0" align="center">
									<tr> 
										<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$userRecordsSize;?>);" > <? }?>
											&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
											&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageUsers.php',700,600);"><? }?></td>
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
							<td colspan="3" height="15" align="center" class="err1"> 
							<?=$errDel;?>
							</td>
						</tr>
						<?
						}
						?>
						<tr> 
							<td width="1" ></td>
							<td colspan="2" > 
								<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
								<?
								if( sizeof($userRecords) > 0 )
								{
									$i	=	0;
								?>
									<tr  bgcolor="#f2f2f2"  > 
										<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
										<td class="listing-head" nowrap>&nbsp;&nbsp;Username </td>
										<td class="listing-head" align="center">Role</td>
										<? if($edit==true){?>
										<td class="listing-head" width="40"></td>
										<? }?>
									</tr>
									<?
									foreach($userRecords as $ur)
									{
										$i++;
										$userId		=	$ur[0];
										$uName		=	stripSlash($ur[1]);
										//$uLevel			=	($ur[3]==0)?"Admin":"User";	
										$roleRec =	$manageroleObj->find($ur[3]);
										$uRole	=	stripSlash($roleRec[1]);
									?>
									<tr  bgcolor="WHITE" > 
										<td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$userId;?>" ></td>
										<td class="listing-item" nowrap>&nbsp;&nbsp; 
										<?=$uName;?>
										</td>
										<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$uRole?></td>
										<? if($edit==true){?>
										<td class="listing-item" align="center" width="40"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$userId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');this.form.action='ManageUsers.php';"></td>
										<? }?>
									</tr>
									<?
									}
									?>
									<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
									<input type="hidden" name="editId" value="">
									<input type="hidden" name="editSelectionChange" value="0">
									<?
									}
									else
									{
									?>
									<tr bgcolor="white"> 
										<td colspan="7"  class="err1" height="10" align="center"> 
										<?=$msgNoRecords;?>
										</td>
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
						<tr> 
							<td colspan="3"> 
								<table cellpadding="0" cellspacing="0" align="center">
									<tr> 
										<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$userRecordsSize;?>);" > <? }?>
										&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
										&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageUsers.php',700,600);"><? }?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr> 
							<td colspan="3" height="5" ></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
        <!-- Form fields end   -->
	</td>
</tr>
<tr> 
    <td height="10"></td>
</tr>
</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<script>
function addNewItem()
{
	addNewRow('tblUser','','','','');
}
</script>
<? if($addMode)
{
?>
<script>
window.onLoad = addNewItem();
</script>
<?
}
?>
<script>
<?php
if (sizeof($userDetails)>0) {
?>
	fldId = <?=sizeof($userDetails)?>;
<?php
	}
else if($editMode)
{
?>
addNewItem();
<?
}
?>
</script>