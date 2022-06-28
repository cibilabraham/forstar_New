<?php 
require 'include/include.php';
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
	
	//Page Offset settings
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit;
    //echo $pageNo;	
	//echo $offset;
	
	//List of Employees
	$allRecords = $sulabhaTestObj->fetchAllemployee($offset,$limit);
	$listSize = sizeof($allRecords);
	
	//Pagination Settings
	$numRows = sizeof($sulabhaTestObj->fetchEmployee());
	$maxpage = ceil($numRows/$limit);
	//echo $maxpage;
	
	//Edit Employee Details
	if($p['btnEditActive']!="")
	 {
	   //echo $p['editId'];
	    $editMode = true;
		$editId = $p['editId'];
		$editRecords = $sulabhaTestObj->getEditData($editId);
		//print_r($editRecords);
		$edtId = $editRecords[0];
		$edtName = $editRecords[1];
		$edtDesig = $editRecords[2];
		$edtDept = $editRecords[3];
		
	 }
	 
	//Update Employee Details
	if($p['btnSaveChange'] != "")
	{
	   $updtId = $p['hiddenId'];
	   $updtName = $p['name'];
	   $updtDesig = $p['designation'];
	   $updtDept = $p['department'];
	   
	   $updateRecords = $sulabhaTestObj->updateEmployeeDetail($updtId, $updtName, $updtDesig, $updtDept);
	   if($updateRecords)
	   {
			$sessObj->createSession("displayMsg",$msg_succEmployeeDetailUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateEmployeeDetail.$selection);
	   }
	   else
	   {
			$editMode = true;
			$err = $msg_failEmployeeDetailUpdate;
	   }
	   
	}
	
	//Cancel action
	if($p['btnCancel']!="")
	{
	  $addMode = false;
	  $editMode = false;
	}
	
	//Add New Employee Details
	if($p['empAddNew']!="")
	{
	  $addMode = true;
	}
	
	//Add Employee Details to Database
	if($p['btnAddNew']!="")
	{
	  $addName = $p['name'];
      $addDesignation = $p['designation'];
      $addDept = $p['department'];

      $addEmployeeRecords = $sulabhaTestObj->addNewEmployee($addName, $addDesignation, $addDept);
      if($addEmployeeRecords)
	   {
			$sessObj->createSession("displayMsg",$msg_addEmployeeDetail);
			$sessObj->createSession("nextPage",$url_afterAddEmployeeDetail.$selection);
	   }
	   else
	   {
			$addMode = true;
			$err = $msg_failAddEmployeeDetail;
	   }	  
	}
	
	//Changing value of $editmode
	if($editMode)
			$heading = "Edit Employee Details";
	else
			$heading = "Add Employee Details";
		
	//Confirmation
	if($p['btnPending']!="")
	{
	   $empId = $p['confirmId'];
	   if($empId!="")
	   {
			$empConfirm = $sulabhaTestObj->employeeConfirm($empId);
	   }
	   if($empConfirm)
	   {
			$sessObj->createSession("displayMsg",$msg_succConfirmEmployeeMaster);
			$sessObj->createSession("nextPage",$url_afterEmployeeConfirm.$selection);
	   }
	   else 
	   {
			$errConfirm	=	$msg_failConfirm;
	   }
	}
	
	//Release Confirmation
	if($p['btnConfirm']!="")
	{
	   $empId = $p['confirmId'];
	   if($empId!="")
	   {
			$empConfirm = $sulabhaTestObj->employeeReleaseConfirm($empId);
	   }
	   if($empConfirm)
	   {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmEmployeeMaster);
			$sessObj->createSession("nextPage",$url_afterEmployeeConfirm.$selection);
	   }
	   else 
	   {
			$errReleaseConfirm	=	$msg_failRlConfirm;
	   }
	}
	
	//Delete Employee
	if($p['btnDelete']!="") 
	{
		$rowCount = $p['rowCount'];
		for($i=1; $i<=$rowCount; $i++)
		{
			$empDelId = $p['delId_'.$i];
			if($empDelId!="")
			{
				$empDelSucc = $sulabhaTestObj->employeeDelete($empDelId);
			}
			if($empDelSucc)
			{
				$sessObj->createSession("displayMsg",$msg_succDelEmployeeMaster);
				$sessObj->createSession("nextPage",$url_afterDelEmployee.$selection);
			}
			else
			{
				$errDel	=	$msg_failDelEmployeeMaster;
			}
	    }
	}
	
	$ON_LOAD_PRINT_JS	= "libjs/SulabhaTest.js";
		
	//Get Designations
	$designationRecords = $designationObj->fetchAllRecordsActiveDesignation();
		
    //Get Departments
	$departmentRecords = $departmentObj->fetchAllRecordsActivedept();
	
	
	require("template/topLeftNav.php");
	
	
	//echo $userId;

?>
<html>
<body>
<form name="formSulabhatest" action="SulabhaTest.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<TD height="10"></TD>
		</tr>
		<?php 
		if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?php } ?>
		<tr>
			<td align="center">
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Manage Employee Details";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<table width="30%">
			<?php
			if($editMode || $addMode) 
			{  ?>
			<tr>
				<td>
					<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
						<tr>
							<td>
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
												<?php 
												if($editMode)
												{
												?>
													<td colspan="2" align="center">
														 <input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('SulabhaTest.php');">&nbsp;&nbsp;
														 <input type="submit" name="btnSaveChange" id="btnSaveChange" class="button" value="Save Changes" onClick="return validateEmployeeDetails(document.formSulabhatest);">
													</td>
												<?php				
												 }
												else
												{
												?>
													<td colspan="2" align="center">
														<input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('SulabhaTest.php');">&nbsp;&nbsp;
														<input type="submit" name="btnAddNew" id="btnAddNew" class="button" value="Add" onClick="return validateEmployeeDetails(document.formSulabhatest);">
													</td>
												<?php	
												}
												?>
												<input type="hidden" name="hiddenId" id="hiddenId" value="<?=$edtId;?>">
												</tr>
												<tr>
													<td colspan="2"  height="10" ></td>
												</tr>
												<tr>
													<td class="fieldName" nowrap >Name</td>
													<td><input type="text" name="name" id="name" value="<?=$edtName; ?>"></td>
												</tr>
												<tr>
													<td class="fieldName" nowrap >Designation</td>
													<td nowrap>
														<select name="designation" id="designation">
															<option value="">---Select---</option>
															<?php
															foreach($designationRecords as $desig)
															{
																$desigId = $desig[0];
																$desigName = $desig[1];
																$selected = ($edtDesig == $desigName)?"selected":"";
																?>
															<option value="<?=$desigName;?>" <?=$selected;?>><?=$desigName;?></option>
															<?php 
															 }					 
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td class="fieldName" nowrap >Department</td>
													<td nowrap>
														<select name="department" id="department">
															<option value="">---Select---</option>
															<?php
															foreach($departmentRecords as $dep)
															{
																$deptId = $dep[0];
																$deptName = $dep[1];
																$selected = ($edtDept == $deptName)?"selected":"";
															?>
															<option value="<?=$deptName;?>" <?=$selected;?>><?=$deptName;?></option>
															<?php 
															}					 
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td colspan="2"  height="10" ></td>
												</tr>
												<tr>
												<?php 		  
												if($editMode)
												{
												?>
													<td colspan="2" align="center">
														<input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('SulabhaTest.php');">&nbsp;&nbsp;
														<input type="submit" name="btnSaveChange" id="btnSaveChange" class="button" value="Save Changes" onClick="return validateEmployeeDetails(document.formSulabhatest);">
													</td>
												<?php				
												 }
												else
												{
												?>
													<td colspan="2" align="center">
														 <input type="submit" name="btnCancel" id="btnCancel" class="button" value="Cancel" onclick="return cancel('SulabhaTest.php');">&nbsp;&nbsp;
														 <input type="submit" name="btnAddNew" id="btnAddNew" class="button" value="Add" onClick="return validateEmployeeDetails(document.formSulabhatest);">
													</td>
												<?php	
												}
												?>
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
				</td>
			</tr>
			<?php   
			}
			?>
			</table>
		</td>
	</tr>
	<tr>
		<td height="10" align="center" ></td>
	</tr>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td>
						<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="btnDelete" onClick="return confirmDelete(this.form,'delId_',<?=$employeeMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" name="empAddNew" value="AddNew" class="button"><? } ?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSulabhaTest.php',700,600);"><? }?>
					</td>
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
		<td colspan="2" style="padding-left:10px; padding-right:10px;" >
			<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
				<?
				if ( sizeof($allRecords) > 0 ) {
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
								$nav.= " <a href=\"SulabhaTest.php?pageNo=$page\" class=\"link1\">$page</a> ";
							//echo $nav;
						}
					}
					if ($pageNo > 1) {
						$page  = $pageNo - 1;
						$prev  = " <a href=\"SulabhaTest.php?pageNo=$page\"  class=\"link1\"><<</a> ";
					} else {
						$prev  = '&nbsp;'; // we're on page one, don't print previous link
						$first = '&nbsp;'; // nor the first page link
					}

					if ($pageNo < $maxpage) {
						$page = $pageNo + 1;
						$next = " <a href=\"SulabhaTest.php?pageNo=$page\"  class=\"link1\">>></a> ";
					} else {
						$next = '&nbsp;'; // we're on the last page, don't print next link
						$last = '&nbsp;'; // nor the last page link
					}
					// print the navigation link
					$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
					echo $first . $prev . $nav . $next . $last . $summary; 
					?>	
					<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
					</div> 
					</td>
				</tr>
	<? }?>
				<tr align="center">
					<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
					<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
					<th class="listing-head" style="padding-left:10px; padding-right:10px;">Designation</th>
					<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Department </th>
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
						foreach($allRecords as $rc)
						{  $i++;
						   $empId = $rc[0];
						   $empName = $rc[1];
						   $empDesig = $rc[2];
						   $empDept = $rc[3];
						   $empActive = $rc[4];
					?>
					<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
						<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$empId;?>"></td>
						<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$empName;?></td>
						<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$empDesig;?></td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$empDept;?></td>
						 <?php
						if($edit==true) {?>
						<td class="listing-item" width="60" align="center">
						<?php
						if($empActive != 1)
						{ 
						?>
						<input type="submit" name="btnEditActive" value="Edit" onClick="assignValue(this.form,<?=$empId;?>,'editId')">
						</td>
						 <?php 
						}
						}
						 if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
						<?php
						if($confirm==true) 
						{ 
						if($empActive==0) {?>
						<input type="submit" name="btnPending" value="Pending" onclick="assignValue(this.form,<?=$empId;?>,'confirmId')">
						<?php }
						else if($empActive==1) { ?>
						<input type="submit" name="btnConfirm" value="Confirmed" onClick="assignValue(this.form,<?=$empId;?>,'confirmId')">
						<?php  
						 } 
						} 
						?>
						</td>
						<?php
					    }
						?>
					</tr>
					<?php
					}
					?>
					<input type="hidden" name="rowCount" id="rowCount" value="<?=$i;?>">
					<input type="hidden" name="editId" id="editId" value="">
					<input type="hidden" name="confirmId" id="confirmId" value="">
					<?php
					if($maxpage>1){?>
					<tr>
						<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
						<div align="right">
						<?php
						 $nav  = '';
						for ($page=1; $page<=$maxpage; $page++) {
							if ($page==$pageNo) {
									$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
							} else {
									$nav.= " <a href=\"SulabhaTest.php?pageNo=$page\" class=\"link1\">$page</a> ";
								//echo $nav;
							}
						}
						if ($pageNo > 1) {
							$page  = $pageNo - 1;
							$prev  = " <a href=\"SulabhaTest.php?pageNo=$page\"  class=\"link1\"><<</a> ";
						} else {
							$prev  = '&nbsp;'; // we're on page one, don't print previous link
							$first = '&nbsp;'; // nor the first page link
						}

						if ($pageNo < $maxpage) {
							$page = $pageNo + 1;
							$next = " <a href=\"SulabhaTest.php?pageNo=$page\"  class=\"link1\">>></a> ";
						} else {
							$next = '&nbsp;'; // we're on the last page, don't print next link
							$last = '&nbsp;'; // nor the last page link
						}
						// print the navigation link
						$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
						echo $first . $prev . $nav . $next . $last . $summary; 
						?>	
						<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
						</div> 
						</td>
					</tr>
					<? 
					}
					} 
					else {
					?>
					<tr>
						<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
					</tr>	
					<?php
					}
					?>
				</tbody>	
			</table>
		</td>
	</tr>
	<tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td>
						<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="btnDelete" onClick="return confirmDelete(this.form,'delId_',<?=$employeeMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" name="empAddNew" value="AddNew" class="button"><? } ?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSulabhaTest.php',700,600);"><? }?>
					</td>
				</tr>
			</table>
		</td>
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
</td>
</tr>	
<tr>
	<td height="10"></td>
</tr>
</table>
</form>
<?php
  # Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>
</body>
</html>