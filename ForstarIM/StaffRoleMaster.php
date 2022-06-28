<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$fishId			=	"";	
	$fishName		=	"";
	$fishCode		=	"";
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	
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
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------

	# Add Department Start 
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAddStaffRole"]!="") {

		$name	=	addSlash(trim($p["name"]));
		$description	=	addSlash(trim($p["description"]));
		//echo "hii";
		if ($name!="") {
			$staffRoleRecIns	=	$staffRoleMasterObj->addStaffRole($name,$description,$userId);

			if ($staffRoleRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddStaffRole);
				$sessObj->createSession("nextPage",$url_afterDelStaffRole.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStaffRole;
			}
			$staffRoleRecIns		=	false;
		}

	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$roleId	=	$p["confirmId"];


			if ($roleId!="") {
				$roleRecConfirm = $staffRoleMasterObj->updateRoleconfirm($roleId);
			}

		}
		if ($roleRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmStaffRole);
			$sessObj->createSession("nextPage",$url_afterDelStaffRole.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$roleId	=	$p["confirmId"];
			if ($roleId!="") {
			#Check any entries exist
				$roleRecConfirm = $staffRoleMasterObj->updateRoleReleaseconfirm($roleId);
			}
		}
		if ($roleRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmStaffRole);
			$sessObj->createSession("nextPage",$url_afterDelStaffRole.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}

	
	# Edit staff 
	if ($p["editId"]!="") {
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$roleRec		=	$staffRoleMasterObj->find($editIt);
		$roleId			=	$roleRec[0];
		$name		=	stripSlash($roleRec[1]);
		$description		=	stripSlash($roleRec[2]);
		//echo $roleId;
		
	}

	if ($p["cmdSaveChange"]!="") {
		
		$roleId		=	$p["hidRoleId"];
		$name	=	addSlash(trim($p["name"]));
		$description	=	addSlash(trim($p["description"]));
		if ($roleId!="" && $name!="") {
			$staffRoleRecUptd		=	$staffRoleMasterObj->updateStaffRole($roleId,$name,$description);
		}
		if ($staffRoleRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStaffRoleUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStaffRole.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStaffRoleUpdate;
		}
		$staffRoleRecUptd	=	false;
	}


	# Delete staff
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$roleId	=	$p["delId_".$i];
			if ($roleId!="") {
				// Checking the selected fish is link with any other process
				//$staffRecInUse = $staffRoleMasterObj->staffRecInUse($staffId);
				//if (!$staffRecInUse) {
					$staffRoleRecDel = $staffRoleMasterObj->deleteStaffRole($roleId);	
				//}
			}
		}
		if ($staffRoleRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStaffRole);
			$sessObj->createSession("nextPage",$url_afterDelStaffRole.$selection);
		} else {
			$errDel	=	$msg_failDelStaffRole;
		}
		$staffRoleRecDel	=	false;
	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Fishes		
	$departmentRecords	=	$staffRoleMasterObj->fetchAllPagingRecords($offset, $limit);
	$departmentMasterSize		=	sizeof($departmentRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($staffRoleMasterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	/*
	# List all Fish Category;
	$sourceRecords = array();
	//if ($addMode || $editMode) $categoryRecords	= $fishcategoryObj->fetchAllRecords();
	if ($addMode || $editMode) { 
		$categoryRecords	= $fishcategoryObj->fetchAllRecordscategoryActive(); 
		$sourceRecords	    = $staffRoleMasterObj->fetchAllSourceRecords();
	}
	*/
	if ($editMode) $heading = $label_editStaffRole;
	else $heading = $label_addStaffRole;

	
	$ON_LOAD_PRINT_JS	= "libjs/staffrolemaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>


<form name="frmStaffRoleMaster" action="StaffRoleMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<!--<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>-->
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
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
													<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAddStaffRole" class="button" value=" Add " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">
												</td>
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">												
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAddStaffRole" class="button" value=" Add " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">
												</td>
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
		# Listing Fish Starts
		?>
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Staff Role Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="50%">
											<?
												if( $editMode || $addMode) {
											?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
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
																		<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
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
																						<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">	
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAddStaffRole" class="button" value=" Add " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">												
																					</td>
																					<?}?>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Name</td>
																					<td><INPUT TYPE="text" NAME="name" size="15" value="<?=$name;?>"></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >Description</td>
																					<td ><textarea name="description"><?=$description;?></textarea></td>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ><input type="hidden" name="hidRoleId" value="<?=$roleId;?>"></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">				
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffRoleMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAddStaffRole" class="button" value=" Add " onClick="return validateAddStaffRole(document.frmStaffRoleMaster);">	
																					</td>
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
													<!-- Form fields end   -->
												</td>
											</tr>	
											<?
											}			
												# Listing Fish Starts
											?>
										</table>
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$staffMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStaffRoleMaster.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="4" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table  cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">							
											<?
											if( sizeof($departmentRecords) > 0 )
											{
												$i	=	0;
											?>
											<thead>
												<? if($maxpage>1){?>
												<tr>
													<td colspan="6" align="right" style="padding-right:10px;">
														<div align="right" class="navRow">
														<?php 				 			  
														$nav  = '';
														for($page=1; $page<=$maxpage; $page++)
														{
															if ($page==$pageNo)
															{
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															}
															else
															{
																$nav.= " <a href=\"StaffRoleMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
															}
														}
														if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"StaffRoleMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"StaffRoleMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
														}
														else
														{
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
											<tr >
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th>Name</td>
												<th nowrap>Description</th>
												<? if($edit==true){?>	
												<th class="listing-head"></th>
												<? }?>
												<? if($confirmF==true){?>	
												<th class="listing-head"></th>
												<? }?>
											</tr>
										</thead>
										<tbody>
										<?
											$displayStatus = "";
											foreach($departmentRecords as $dr)
											{
												$i++;
												$departmentId		=	$dr[0];
												$name	=	stripSlash($dr[1]);
												$description	=	stripSlash($dr[2]);
												$active=$dr[3];
												//echo "existing count is $existingcount";
												//echo $confirmF;
														
											?>
											<tr   <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>   >
												<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$departmentId;?>" class="chkBox"></td>
												<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$name;?></td>
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$description;?>&nbsp;</td>
												<? if($edit==true){?>
												<td class="listing-item" width="45" align="center"><?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$departmentId;?>,'editId'); this.form.action='StaffRoleMaster.php';" ><?php }
												?></td> 
												<? }?>
												<? if ($confirmF==true){?>
												<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													<?php if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$departmentId;?>,'confirmId');" >
													<?php } else if ($active==1){ if ($existingcount==0) {?>
													
													<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$departmentId;?>,'confirmId');" >
													<?php } ?>
													<?php }?>
													<? }?>
												</td>
											</tr>
											<?
											}
											?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="confirmId" value="">
											<? if($maxpage>1){?>
											<tr>
												<td align="right" style="padding-right:10px" colspan="6" class="navRow">
													<div align="right">
													<?php 				 			  
													$nav  = '';
													for($page=1; $page<=$maxpage; $page++)
													{
														if ($page==$pageNo)
														{
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														}
														else
														{
															$nav.= " <a href=\"StaffRoleMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
														}
													}
													if ($pageNo > 1)
													{
														$page  = $pageNo - 1;
														$prev  = " <a href=\"StaffRoleMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
													}
													else
													{
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}
													if ($pageNo < $maxpage)
													{
														$page = $pageNo + 1;
														$next = " <a href=\"StaffRoleMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
													}
													else
													{
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
													?>
													</div>
												</td>
											</tr>
											<? }?>
										</tbody>
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
								<td colspan="4" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="4">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$staffMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStaffRoleMaster.php',700,600);"><? }?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="4" height="5" ></td>
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
			<!--<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>-->
</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

