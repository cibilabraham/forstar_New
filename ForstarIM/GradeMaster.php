<?php
	require("include/include.php");
	ob_start();
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$gradeId		=	"";
	$gradeCode		=	"";
	$min			=	"";
	$max			=	"";
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode	= true;
	}

	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode	= false;
	}



	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$gradeId	=	$p["confirmId"];
			if ($gradeId!="") {
				// Checking the selected fish is link with any other process
				$gradeRecConfirm = $grademasterObj->updateGradeconfirm($gradeId);
			}
		}
		if ($gradeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmGrade);
			$sessObj->createSession("nextPage",$url_afterAddGrade.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
			}


	if ($p["btnRlConfirm"]!="")
	{	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$gradeId = $p["confirmId"];

			if ($gradeId!="") {
				#Check any entries exist
				
					$gradeRecConfirm = $grademasterObj->updateGradeReleaseconfirm($gradeId);
				
			}
		}
		if ($gradeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmGrade);
			$sessObj->createSession("nextPage",$url_afterAddGrade.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}


	# Insert Grade
	if ($p["cmdAddGrade"]!="") {
		$gradeCode	=	addSlash(trim($p["gradeCode"]));
		$secondaryProcessCode=$p["secondaryProcessCode"];
		$min		=	($p["minimum"]=="")?0:$p["minimum"];
		$max		=	($p["maximum"]=="")?0:$p["maximum"]; 
		
		if ($gradeCode!="") {
			# Check Grade Rec Exist				
			$gradeRecExist = $grademasterObj->chkGradeRecExist($gradeCode, $gadeId);
			if (!$gradeRecExist) {
				$gradeRecIns	=	$grademasterObj->addGrade($gradeCode,$min,$max,$secondaryProcessCode);	
				if ($gradeRecIns) {
					$sessObj->createSession("displayMsg",$msg_succAddGrade);
					$sessObj->createSession("nextPage",$url_afterAddGrade.$selection);
				} else {
					$addMode		=	true;
					$err			=	$msg_failAddGrade;
				}
				$gradeRecIns	=	false;
			} else  {
				$addMode		=	true;
				$err			=	$msg_failAddGrade;
			}
			$gradeMasterUniqueRecords	=	false;
		}
	}
	
	
	# Edit Grade 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$gradeRec		=	$grademasterObj->find($editId);
		$gradeId		=	$gradeRec[0];
		$gradeCode		=	stripSlash($gradeRec[1]);
		$max			=	$gradeRec[2];
		$min			=	$gradeRec[3];
		$includeSecondary		=	$gradeRec[4];
		($includeSecondary=="Y")?$chk="checked":$chk="";
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		$gradeId	= $p["hidGradeId"];
		$gradeCode	= addSlash(trim($p["gradeCode"]));
		$min		= ($p["minimum"]=="")?0:$p["minimum"];
		$max		= ($p["maximum"]=="")?0:$p["maximum"]; 
		$secondaryProcessCode=$p["secondaryProcessCode"];
		
		
		if ($gradeId!="" && $gradeCode!="") {
			# Check Rec Exist
			$gradeRecExist = $grademasterObj->chkGradeRecExist($gradeCode, $gradeId);
			if (!$gradeRecExist) {
				$gradeRecUptd	= $grademasterObj->updateGrade($gradeCode, $min, $max, $gradeId,$secondaryProcessCode);
			}
		}
	
		if ($gradeRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateGrade);
			$sessObj->createSession("nextPage",$url_afterUpdateGrade.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateGrade;
		}
		$gradeRecUptd	=	false;
	}


	# Delete Grade
	if ($p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$gradeId = $p["delId_".$i];
			if ($gradeId!="") {
				/* Checking the selected grade is link with any other process - confirm*/
				$gradeRecInUse = $grademasterObj->gradeRecInUse($gradeId);
				if (!$gradeRecInUse) {
					$gradeRecDel = $grademasterObj->deleteGrade($gradeId);
				}			
			}
		}
		if ($gradeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelGrade);
			$sessObj->createSession("nextPage",$url_afterDelGrade.$selection);
		} else {
			$errDel	=	$msg_failDelGrade;
		}
		$gradeRecDel	=	false;
	}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	#List All Grades
	$gradeMasterRecords	= $grademasterObj->fetchPagingRecords($offset, $limit);
	$gradeMasterRecordSize	= sizeof($gradeMasterRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($grademasterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($editMode)	$heading	=	$label_editGrade;
	else		$heading	=	$label_addGrade;
	

	$ON_LOAD_PRINT_JS	= "libjs/grademaster.js";

	$help_lnk="help/hlp_GradeMaster.html";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmGradeMaster" action="GradeMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?
		if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddGrade(document.frmGradeMaster);">
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAddGrade" class="button" value=" Add " onClick="return validateAddGrade(document.frmGradeMaster);">
												</td>
												<?}?>
											</tr>
											<input type="hidden" name="hidGradeId" value="<?=$gradeId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td colspan="2" nowrap align="center">
													<table>
														<tr>
															<td class="fieldName" nowrap >*Grade Code</td>
															<td><INPUT TYPE="text" NAME="gradeCode" size="20" value="<?=$gradeCode;?>" autocomplete="off"></td>
														</tr>
														<tr>
															<td class="fieldName" nowrap >*&nbsp;Min</td>
															<td >
															<INPUT TYPE="text" NAME="minimum" size="10" value="<?=$min;?>" autocomplete="off">
															&nbsp;
															</td>
														</tr>
														<tr>
															<td class="fieldName" nowrap >*&nbsp;Max</td>
															<td >
															<INPUT TYPE="text" NAME="maximum" size="10" value="<?=$max;?>" autocomplete="off">&nbsp;&nbsp;
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddGrade(document.frmGradeMaster);">
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAddGrade" class="button" value=" Add " onClick="return validateAddGrade(document.frmGradeMaster);">
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
			}# Listing Grade Starts
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
								$bxHeader="Grade Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="50%" align="center">
										<?
												if ($editMode || $addMode) {
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
																	<tr>
																		<td width="1" ></td>
																		<td colspan="2" >
																			<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddGrade(document.frmGradeMaster);">
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAddGrade" class="button" value=" Add " onClick="return validateAddGrade(document.frmGradeMaster);">
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidGradeId" value="<?=$gradeId;?>">
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<td colspan="2" nowrap align="center">
																						<table>
																							<tr>
																								<td class="fieldName" nowrap >*Grade Code</td>
																								<td><INPUT TYPE="text" NAME="gradeCode" size="20" value="<?=$gradeCode;?>" autocomplete="off"></td>
																							</tr>
																							<tr>
																								<td class="fieldName">
																	
																								<INPUT TYPE="checkbox" NAME="secondaryProcessCode" size="20" value="Y" autocomplete="off" <?=$chk?>>
																						
																								</td>
																								<td class="fieldName" nowrap >Include in Secondary Processcode</td>
																								
																							</tr>
																							<tr>
																								<td class="fieldName" nowrap >*&nbsp;Min</td>
																								<td >
																								<INPUT TYPE="text" NAME="minimum" size="10" value="<?=$min;?>" autocomplete="off">
																								&nbsp;
																								</td>
																							</tr>
																							<tr>
																								<td class="fieldName" nowrap >*&nbsp;Max</td>
																								<td >
																								<INPUT TYPE="text" NAME="maximum" size="10" value="<?=$max;?>" autocomplete="off">&nbsp;&nbsp;
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddGrade(document.frmGradeMaster);">
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GradeMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAddGrade" class="button" value=" Add " onClick="return validateAddGrade(document.frmGradeMaster);">
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
												}# Listing Grade Starts
											?>	
									</table>
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$gradeMasterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintGradeMaster.php',700,600);"><? }?></td>
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
									<td colspan="2" >
										<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
											if( sizeof($gradeMasterRecords) > 0 )
											{
												$i	=	0;
											?>
											<thead>
											<? if($maxpage>1){?>
												<tr>
													<td colspan="6" style="padding-right:10px;" class="navRow">
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
																$nav.= " <a href=\"GradeMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
															}
														}
														if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"GradeMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"GradeMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<tr align="center" >
													<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
													<th nowrap style="padding-left:10px;padding-right:10px;">Grade Code</th>						
													<th style="padding-left:10px;padding-right:10px;">Min</th>
													<th style="padding-left:10px;padding-right:10px;">Max</th>
													<? if($edit==true){?>
													<th width="50"></th><? }?>
													<? if($confirm==true){?>
													<th width="50"></th><? }?>
												</tr>
											</thead>
											<tbody>
											<?
											foreach($gradeMasterRecords as $gr) 
											{			
												$i++;
												$gradeId	=	$gr[0];
												$gradeCode	=	stripSlash($gr[1]);
												$max		=	$gr[2];
												$min		=	$gr[3];	
												$active=$gr[4];
												$existingcount=$gr[5];
											?>
												<tr  <?php if ($active==0){?> bgcolor="#afddf8"   onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();"<?php }?>>
													<td width="20" align="center">
													<?php 
													
													if ($existingcount==0) {?>
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$gradeId;?>" class="chkBox"></td>
													<?php 
													}
													?>
													<td class="listing-item" nowrap style="padding-left:10px;padding-right:10px;"><?=$gradeCode;?></td>
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;"><?=$min;?></td>
													<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;"><?=$max;?></td>
													<? if($edit==true){?>
													<td class="listing-item" width="50" align="center">
													<? if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$gradeId;?>,'editId'); this.form.action='GradeMaster.php';"  ><?} ?></td><? }?>
													<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													<?php 
													 if ($confirm==true){	
													if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$gradeId;?>,'confirmId');" >
													<?php } else 
														if ($active==1){ 
													//if ($existingcount==0) {?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$gradeId;?>,'confirmId');" >
													<?php //}

													} }?>
													</td>
													<?php }?>
												</tr>
												<?
												}
												?>
												<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
												<input type="hidden" name="editId" value="">
												<input type="hidden" name="confirmId" value="">
												<? if($maxpage>1){?>
												<tr>
													<td colspan="6" style="padding-right:10px" class="navRow">
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
																$nav.= " <a href=\"GradeMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
															}
														}
														if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"GradeMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}
														if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"GradeMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											</tbody>
											<? } else { ?>
											<tr bgcolor="white">
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$gradeMasterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintGradeMaster.php',700,600);"><? }?></td>
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
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>