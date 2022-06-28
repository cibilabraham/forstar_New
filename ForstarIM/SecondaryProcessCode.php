<?php
	require("include/include.php");
	require_once('lib/SecondaryProcessCode_ajax.php');
		
	
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

	# Add Fish Start 
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAddSecondaryProcessCode"]!="") {

		$name	=	addSlash(trim($p["name"]));
		$secondaryGrade=$p["secondaryGrade"];
		$secondaryCount	=	$p["hidSecondaryRowCount"];
		if ($name!="" ) {
			
			$secondaryRecIns	=	$secondaryProcessCodeObj->addSecondaryProcessCode($name,$userId,$secondaryGrade);
			$lastId = $databaseConnect->getLastInsertedId();
			if($secondaryRecIns)
			{
				for($i=0; $i<$secondaryCount	; $i++)
				{
					$sstatus=$p["sstatus_".$i];
					if($sstatus!="N")
					{
						$fish=$p["fish_".$i];
						$processCode=$p["processCode_".$i];
						$grade=$p["grade_".$i];
						$percentage=$p["percentage_".$i];
						$secondaryEntryRecIns	=	$secondaryProcessCodeObj->addSecondaryProcessCodeEntry($lastId,$fish,$processCode,$grade,$percentage);
					}
					
				}
			}

			if ($secondaryEntryRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddSecondaryProcessCode);
				$sessObj->createSession("nextPage",$url_afterAddSecondaryProcessCode.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSecondaryProcessCode;
			}
			$secondaryEntryRecIns		=	false;
		}

	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$secondaryId	=	$p["confirmId"];
			if ($secondaryId!="") {
				// Checking the selected fish is link with any other process
				$secondaryRecConfirm = $secondaryProcessCodeObj->updateSecondaryconfirm($secondaryId);
			}
		}
		if ($secondaryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmSecondaryProcessCode);
			$sessObj->createSession("nextPage",$url_afterSecondaryProcessCode.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$secondaryId	=	$p["confirmId"];
			if ($secondaryId!="") 
			{
				#Check any entries exist
				$secondaryProcessCodeRecConfirm = $secondaryProcessCodeObj->updateSecondaryReleaseconfirm($secondaryId);
			}
		}
		if ($secondaryProcessCodeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmSecondaryProcessCode);
			$sessObj->createSession("nextPage",$url_afterDelSecondaryProcessCode.$selection);
		} else {
				$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}

	
	# Edit Fish 
	if ($p["editId"]!="") 
	{
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$secondaryRec		=	$secondaryProcessCodeObj->find($editIt);
		$secondaryId			=	$secondaryRec[0];
		$name		=	stripSlash($secondaryRec[1]);
		$secondaryGrade		=	stripSlash($secondaryRec[2]);
		$secondaryEntryRec=$secondaryProcessCodeObj->getSecondaryProcessEntry($secondaryId);
	}

	if ($p["cmdSaveChange"]!="") 
	{
		
		$secondaryId		=	$p["hidSecondaryId"];
		$name	=	addSlash(trim($p["name"]));
		$secondaryGrade=$p["secondaryGrade"];
		$secondaryCount	=	$p["hidSecondaryRowCount"];
		
		if ($secondaryId!="" && $name!="")
		{
			
			$secondaryRecUptd		=	$secondaryProcessCodeObj->updateSecondaryProcessCode($secondaryId,$name,$secondaryGrade);
			for($i=0; $i<$secondaryCount	; $i++)
			{
					$sstatus=$p["sstatus_".$i];
					$entryId=$p["entryId_".$i];
					$fish=$p["fish_".$i];
					$processCode=$p["processCode_".$i];
					$grade=$p["grade_".$i];
					$percentage=$p["percentage_".$i];
					if($sstatus!="N")
					{
						if($entryId=="")
						{
							
							$secondaryEntryRecIns	=	$secondaryProcessCodeObj->addSecondaryProcessCodeEntry($secondaryId,$fish,$processCode,$grade,$percentage);
						}
						else if ($entryId!="" ) 
						{	
							$secondaryEntryRecIns =$secondaryProcessCodeObj->updateSecondaryProcessCodeEntry($entryId,$secondaryId,$fish,$processCode,$grade,$percentage);
						}

					}
					if($sstatus=="N" && $entryId!="")
					{
							$delSecondaryEntryRecIns =$secondaryProcessCodeObj->delSecondaryProcessCodeEntryId($entryId);
					}
					
				}

		}
	
		if ($secondaryEntryRecIns) {
			$sessObj->createSession("displayMsg",$msg_succSecondaryProcessCodeUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSecondaryProcessCode.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSecondaryProcessCodeUpdate;
		}
		$secondaryEntryRecIns	=	false;
	}


	# Delete Fish
	if ($p["cmdDelete"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		//echo $rowCount;
		//die();
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$secondaryId	=	$p["delId_".$i];

			if ($secondaryId!="") {
				// Checking the selected fish is link with any other process
				//$fishRecInUse = $secondaryProcessCodeObj->fishRecInUse($fishId);
				//if (!$fishRecInUse) {
					$secondaryRecDel = $secondaryProcessCodeObj->delSecondaryProcessCode($secondaryId);	
					$secondaryEntryRecDel = $secondaryProcessCodeObj->delSecondaryProcessCodeEntry($secondaryId);	
				//}
			}
		}
		if ($secondaryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSecondaryProcessCode);
			$sessObj->createSession("nextPage",$url_afterDelSecondaryProcessCode.$selection);
		} else {
			$errDel	=	$msg_failDelSecondaryProcessCode;
		}
		$secondaryRecDel	=	false;
	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Fishes		
	$secondaryProcessCodeRecs	=	$secondaryProcessCodeObj->fetchAllPagingRecords($offset, $limit);
	$secondaryProcessCodeSize		=	sizeof($secondaryProcessCodeRecs);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($secondaryProcessCodeObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	# List all Fish Category;
	$sourceRecords = array();
	//if ($addMode || $editMode) $categoryRecords	= $fishcategoryObj->fetchAllRecords();
	if ($addMode || $editMode) { 
		$categoryRecords	= $fishcategoryObj->fetchAllRecordscategoryActive(); 
		$sourceRecords	    = $fishmasterObj->fetchAllSourceRecords();
	}
	if ($editMode) $heading = $label_editSecondaryProcessCode;
	else $heading = $label_addSecondaryProcessCode;

	$secondaryGradeRec=$secondaryProcessCodeObj->getSecondaryGrade();

	$fishRecs=$fishmasterObj->fetchAllRecordsFishactive();
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/SecondaryProcessCode.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>


<form name="frmSecondaryProcessCode" action="SecondaryProcessCode.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
												<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddSecondaryProcessCode" class="button" value=" Add " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
												<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddSecondaryProcessCode" class="button" value=" Add " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
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
		}# Listing Fish Starts
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
								$bxHeader="Secondary Process Code";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Master</td>
								</tr>-->
								<input type="hidden" name="hidSecondaryId"  id="hidSecondaryId" value="<?=$secondaryId;?>">
											
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
																							<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
																							<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
																						<?} else{?>
																						<td  colspan="2" align="center">
																							<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
																							<input type="submit" name="cmdAddSecondaryProcessCode" class="button" value=" Add " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
																						<?}?>
																					</tr>
																					<input type="hidden" name="hidFishId" value="<?=$fishId;?>">
																					<tr>
																						<td colspan="2"  height="10" ></td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >*Name</td>
																						<td >
																						<INPUT TYPE="text" NAME="name" size="25"  maxlength="25" value="<?=$name;?>">												
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >*Grade</td>
																						<td >
																							<select id="secondaryGrade" tabindex="1" style="display:display;" name="secondaryGrade">
																								<option value="">--select--</option><?php 	
																								foreach($secondaryGradeRec as $grades)
																								{
																									$grdIds		=	$grades[0];
																									$grd	=	$grades[1];
																									$sel  = ($secondaryGrade==$grdIds)?"Selected":"";
																								?>
																								<option value="<?=$grdIds?>" <?=$sel?>><?=$grd?></option>";
																								<?
																								}
																								?>
																							</select>
																						</td>
																					</tr>
																					<tr align="center">
																						<td colspan="2">
																							<table>
																								<tr align="center">
																									<td>	
																										<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddNewSPC" name="tblAddNewSPC">
																											<tr bgcolor="#f2f2f2" align="center">
																												<td class="listing-head" nowrap>Fish</td>
																												<td class="listing-head" nowrap>Process Code</td>
																												<td class="listing-head" nowrap>Grade </td>
																												<td class="listing-head" nowrap>Percentage</td>
																												<td></td>
																											</tr>
																											<?
																											if (sizeof($secondaryEntryRec)>0) {
																											   $n=0; $totalPercentage=0;
																												 foreach ($secondaryEntryRec as $ser) {				
																													$id=$ser[0];
																													$fishId=$ser[1];
																													$processCodeId=	$ser[2];
																													$gradeId=	$ser[3];
																													$percentage=	$ser[4];
																													$processCodeRec = $secondaryProcessCodeObj->getProcessCode($fishId);
																													$gradeRec = $secondaryProcessCodeObj->getGrade($fishId,$processCodeId);
																													$totalPercentage+=$percentage;
																												?>
																												<tr class="whiteRow" id="srow_<?=$n?>">
																													<td align="left" class="fieldName">
																														<select id="fish_<?=$n?>" onchange="xajax_getProcessCode(document.getElementById('fish_<?=$n?>').value,<?=$n?>,''); " tabindex="1" style="display:display;" name="fish_<?=$n?>">
																															<option value="">--select--</option>
																															<?php 
																															foreach($fishRecs as $fr)
																															{
																																//alert($sr[0]);
																																$fishIds		=	$fr[0];
																																$fishName	=	stripSlash($fr[1]);
																																$sel  = ($fishId==$fishIds)?"Selected":"";
																															?>
																															<option value="<?=$fishIds?>" <?=$sel?>><?=$fishName?></option>";
																															<?}?>
																														</select>
																													</td>
																													
																													<td align="center" class="fieldName">
																														<select id="processCode_<?=$n?>" onchange="xajax_getGrade(document.getElementById('fish_<?=$n?>').value,document.getElementById('processCode_<?=$n?>').value,<?=$n?>,''); " tabindex="1" style="display:display;" name="processCode_<?=$n?>"><option value="">--select--</option>
																															<?php 	
																															foreach($processCodeRec as $processCodesId=>$processCode)
																															{
																																$processNameId		=	$processCodesId;
																																$processName	=	$processCode;
																																$sel  = ($processCodeId==$processNameId)?"Selected":"";
																															?>
																															<option value="<?=$processNameId?>" <?=$sel?>><?=$processName?></option>";
																															<?
																															}
																															?>
																														</select>
																													</td>
																													<td align="center" class="fieldName">
																														<select id="grade_<?=$n?>" tabindex="1" style="display:display;" name="grade_<?=$n?>">
																														<option value="">--select--</option><?php 	
																															foreach($gradeRec as $grd=>$grdName)
																															{
																																$gradeIds		=	$grd;
																																$grade	=	$grdName;
																																$sel  = ($gradeId==$gradeIds)?"Selected":"";
																															?>
																															<option value="<?=$gradeIds?>" <?=$sel?>><?=$grade?></option>";
																															<?
																															}
																															?>
																														</select>
																													</td>
																													<td align="center" class="fieldName" nowrap>
																														<input id="percentage_<?=$n?>" type="text" style="text-align:right; " onkeyup="totPercentage();" size="15" value="<?=$percentage?>" name="percentage_<?=$n?>">%
																													</td>
																													<!--<td align="center" class="fieldName">
																														<input type="text" value="<?=$pondlocation?>" tabindex="2" style="text-align:right; border:none;" readonly="" size="15" id="pondLocation_<?=$n?>" name="pondLocation_<?=$n?>">
																													</td>-->
																													
																													<td align="center" class="fieldName">
																														<a onclick="setSecondaryStatus('<?=$n?>');" href="###">
																															<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
																														</a>
																														<input type="hidden" value="" id="sstatus_<?=$n?>" name="sstatus_<?=$n?>">
																														<input type="hidden" value="N" id="IsFromDB_<?=$n?>" name="IsFromDB_<?=$n?>">
																														<input type="hidden" value="<?=$id?>" id="entryId_<?=$n?>" name="entryId_<?=$n?>">
																													</td>
																												</tr>
																												<?
																												$n++;
																												}
																												}
																												?>	
																											</table>
																										</td>
																									</tr>
																									
																									<input type="hidden" name="hidSecondaryRowCount" id="hidSecondaryRowCount" value="<?=$n?>">
																									<tr><TD height="10"></TD></tr>
																									
																									
																								</table>
																							</td>
																						</tr>
																						<tr>
																							<td  class="fieldName" >Total Percentage</td>
																							<td  class="fieldName" align="right" style="padding-right:32px" ><input type="text" name="totalPercentage" id="totalPercentage" value="<?=round($totalPercentage)?>" size="15" style="text-align:right">%																									<tr><TD height="10"></TD></tr>
																							</td>
																							<td>&nbsp;</td>
																						
																						</tr>
																						<tr>
																							<td width="40%" valign="top" colspan="3" >
																								<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																							</TD>
																						</tr>	
																						<tr>
																						<? if($editMode){?>
																						<td colspan="2" align="center">
																							<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
																							<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
																						<?} else{?>
																						<td  colspan="2" align="center">
																							<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SecondaryProcessCode.php');">&nbsp;&nbsp;
																							<input type="submit" name="cmdAddSecondaryProcessCode" class="button" value=" Add " onClick="return validateAddSecondaryProcessCode(document.frmSecondaryProcessCode);">												</td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fishMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSecondaryProcessCode.php',700,600);"><? }?></td>
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
											if( sizeof($secondaryProcessCodeRecs) > 0 )
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
																$nav.= " <a href=\"SecondaryProcessCode.php?pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"SecondaryProcessCode.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"SecondaryProcessCode.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
													<th>Secondary Grade</th>
													<th nowrap>Process Code </th>
													<th >Percentage</th>
													<? if($edit==true){?>	<th class="listing-head"></th><? }?>
													<? if($confirmF==true){?>	<th class="listing-head"></th><? }?>
												</tr>
											</thead>
											<tbody>
											<?
											$displayStatus = "";
											foreach($secondaryProcessCodeRecs as $spr)
											{
												$i++;
												$secondaryProcessId		=	$spr[0];
												$name	=	stripSlash($spr[1]);
												$active=$spr[2];
												$secondaryGrade=$spr[3];
												$secGrade=$secondaryProcessCodeObj->findSecondaryGrade($secondaryGrade);
												$secondaryEntryRecs=$secondaryProcessCodeObj->getSecondaryProcessEntry($secondaryProcessId);
											?>
												<tr   <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>   >
													<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$secondaryProcessId;?>" class="chkBox"></td>
													<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$name;?></td>
													<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$secGrade;?></td>
													<td class="listing-item" nowrap="nowrap">
													<? 
													foreach($secondaryEntryRecs as $secondaryEntry)
													{
														$processCode=$secondaryEntry[5];
														echo $processCode.'<br/>';
													}
													?>
													</td>
													<td class="listing-item" nowrap="nowrap">
													<? 
													foreach($secondaryEntryRecs as $secondaryEntry)
													{
														$percentage=$secondaryEntry[4];
														echo $percentage.'<br/>';
													}
													?>
													</td>
													<? if($edit==true){?>
													<td class="listing-item" width="45" align="center"><?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$secondaryProcessId;?>,'editId'); this.form.action='SecondaryProcessCode.php';" ><?php }
													?></td> 
													<? }?>
													<? if ($confirmF==true){?>
													<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													<?php if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$secondaryProcessId;?>,'confirmId');" >
													<?php } else if ($active==1){ 
													//if ($existingcount==0) {?>
												
													<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$secondaryProcessId;?>,'confirmId');" >
													<?php 
													
													//} ?>
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
																$nav.= " <a href=\"SecondaryProcessCode.php?pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"SecondaryProcessCode.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"SecondaryProcessCode.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fishMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSecondaryProcessCode.php',700,600);"><? }?></td>
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
	</table>
	<script>
	<?php
if (sizeof($secondaryEntryRec)>0) {
?>
	fieldvalue = <?=sizeof($secondaryEntryRec)?>;
<?php
	}
?>
</script>
	<script>
	function addNewItem()
	{
		addNew('tblAddNewSPC','', '', '', '','', 'addmode');
	}
</script>
	<? if ($addMode) {?>
	<script>
	window.onLoad = addNewItem();
	</script>
	<? } ?>
</form>

<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

