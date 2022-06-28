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


	# Add Employee Master Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Employee Master
	if ($p["cmdAdd"]!="") {

		$name		=	addSlash(trim($p["name"]));
		$designation	=	addSlash(trim($p["designation"]));
		$department		=	addSlash(trim($p["department"]));
		$address		=	addSlash(trim($p["address"]));
		$telephone		=	addSlash(trim($p["telephone"]));
		
		if ($name!="") {
			$employeeMasterRecIns	=	$employeeMasterObj->addEmployeeMaster($name, $designation, $department,$address,$telephone, $userId);

			if ($employeeMasterRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddEmployeeMaster);
				$sessObj->createSession("nextPage", $url_afterAddEmployeeMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddEmployeeMaster;
			}
			$employeeMasterRecIns		=	false;
		}
	}
		
	# Edit Employee Master 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$employeeMasterRec		=	$employeeMasterObj->find($editId);
		$employeeMasterId			=	$employeeMasterRec[0];
		$name			=	stripSlash($employeeMasterRec[1]);
		$designation				=	stripSlash($employeeMasterRec[2]);
		$department	=	stripSlash($employeeMasterRec[3]);
		$address	=	stripSlash($employeeMasterRec[4]);
		$telephone	=	stripSlash($employeeMasterRec[5]);
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$employeeMasterId		=	$p["hidEmployeeMasterId"];
		$name		=	addSlash(trim($p["name"]));
		$designation	=	addSlash(trim($p["designation"]));
		$department		=	addSlash(trim($p["department"]));
		$address		=	addSlash(trim($p["address"]));
		$telephone		=	addSlash(trim($p["telephone"]));
		
		if ($employeeMasterId!="" && $name!="") {
			$employeeMasterRecUptd = $employeeMasterObj->updateEmployeeMaster($employeeMasterId, $name, $designation, $department,$address,$telephone);
		}
	
		if ($employeeMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succEmployeeMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateEmployeeMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failEmployeeMasterUpdate;
		}
		$employeeMasterRecUptd	=	false;
	}


	# Delete Employee Master
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		$recInUse = false;
		for ($i=1; $i<=$rowCount; $i++) {
			$employeeMasterId	=	$p["delId_".$i];

			if ($employeeMasterId!="") {
				// Need to check the selected employee is link with any other process
				$employeeRecInUse = $employeeMasterObj->employeeRecInUse($employeeMasterId);
				if (!$employeeRecInUse) {
					$employeeMasterRecDel =	$employeeMasterObj->deleteEmployeeMaster($employeeMasterId);
				} else {
					$recInUse = true;
				}
			}
		}
		if ($employeeMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelEmployeeMaster);
			$sessObj->createSession("nextPage",$url_afterDelEmployeeMaster.$selection);
		} else {
			if ($recInUse) $errDel	=	$msg_failDelEmployeeInUse;
			else $errDel	=	$msg_failDelEmployeeMaster;
		}
		$employeeMasterRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$employeeMasterId	=	$p["confirmId"];
			if ($employeeMasterId!="") {
				// Checking the selected fish is link with any other process
				$employeeMasterRecConfirm = $employeeMasterObj->updateEmployeeMasterObjconfirm($employeeMasterId);
			}

		}
		if ($employeeMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmEmployeeMaster);
			$sessObj->createSession("nextPage",$url_afterDelEmployeeMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$employeeMasterId = $p["confirmId"];
			if ($employeeMasterId!="") {
				#Check any entries exist
				
					$employeeMasterRecConfirm = $employeeMasterObj->updateEmployeeMasterReleaseconfirm($employeeMasterId);
				
			}
		}
		if ($employeeMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmEmployeeMaster);
			$sessObj->createSession("nextPage",$url_afterDelEmployeeMaster.$selection);
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

	# List all Employee Master ;
	$employeeMasterRecords	=	$employeeMasterObj->fetchAllPagingRecords($offset, $limit);
	$employeeMasterSize		=	sizeof($employeeMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($employeeMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editEmployeeMaster;
	else 		$heading = $label_addEmployeeMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/EmployeeMaster.js";
	
	# Get all designation Recs
		$designationRecs = $designationObj->fetchAllRecordsActiveDesignation();
		
	# Get all department Recs
		$departmentRecs = $departmentObj->fetchAllRecordsActivedept();
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmEmployeeMaster" action="EmployeeMaster.php" method="post">
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
			<tr>
				<td>
				<?php	
					$bxHeader = "Manage Employee Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
			if ( $editMode || $addMode) {
		?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddEmployeeMaster(document.frmEmployeeMaster);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddEmployeeMaster(document.frmEmployeeMaster);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidEmployeeMasterId" value="<?=$employeeMasterId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<td class="fieldName" nowrap >*Name </td>
												<td><INPUT TYPE="text" NAME="name" size="15" value="<?=$name;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*Designation</td>
												<td nowrap>
													<select name="designation" id="designation">
													<option value="">-- Select --</option>
													<?php
													foreach ($designationRecs as $cmr) {
														$designationId 	= $cmr[0];	
														$designationVar	= $cmr[1];
														$selected = ($designation==$designationId)?"selected":""
													?>
													<option value="<?=$designationId?>" <?=$selected?>><?=$designationVar?></option>
													<?  }?>
													</select>
												</td>
											</tr>
											
											<tr>
												<td nowrap class="fieldName" >*Department</td>
												<td nowrap>
													<select name="department" id="department">
													<option value="">-- Select --</option>
													<?php
													foreach ($departmentRecs as $cmr) {
														$departmentId 	= $cmr[0];	
														$departmentVar	= $cmr[1];
														$selected = ($department==$departmentId)?"selected":""
													?>
													<option value="<?=$departmentId?>" <?=$selected?>><?=$departmentVar?></option>
													<?  }?>
													</select>
												</td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*Address</td>
												
												<td ><textarea name="address"><?=$address;?></textarea></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*Telephone No</td>
												<td><INPUT TYPE="text" NAME="telephone" size="15" value="<?=$telephone;?>"></td>
											</tr>
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddEmployeeMaster(document.frmEmployeeMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddEmployeeMaster(document.frmEmployeeMaster);">												</td>

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
		<?
			}
			
			# Listing Employee master Starts
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
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$employeeMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintEmployeeMaster.php',700,600);"><? }?></td>
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
			if ( sizeof($employeeMasterRecords) > 0 ) {
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
      				$nav.= " <a href=\"EmployeeMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"EmployeeMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"EmployeeMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Designation</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Department </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Address </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Telephone Number </th>
		
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
	foreach($employeeMasterRecords as $cr) {
		$i++;
		 $employeeMasterId		=	$cr[0];
		 $name		=	stripSlash($cr[1]);
		 $designation	=	stripSlash($cr[2]);
		 $department		=	stripSlash($cr[3]);
		 $address		=	stripSlash($cr[4]);
		 $telephone		=	stripSlash($cr[5]);
		 $active=$cr[6];
		 $existingrecords=$cr[7];
			
		 $designationRec=$employeeMasterObj->fetchDesignation($designation);
		 $designationName=$designationRec[1];
		 $departmentRec=$employeeMasterObj->fetchDepartment($department);
		 $departmentName=$departmentRec[1];
		
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$employeeMasterId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$designationName;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$departmentName;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$address;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$telephone;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) {
			?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$employeeMasterId;?>,'editId'); this.form.action='EmployeeMaster.php';"  >
		<?php } ?></td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$employeeMasterId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$employeeMasterId;?>,'confirmId');" >
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
      				$nav.= " <a href=\"EmployeeMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"EmployeeMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"EmployeeMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$employeeMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintEmployeeMaster.php',700,600);"><? }?></td>
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
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>