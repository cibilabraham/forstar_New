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
	if (!$accesscontrolObj->canAccess()) 
	{
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


	# Add Registration Type Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") 
	{
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Registration Type
	if ($p["cmdAdd"]!="") 
	{

		$testName		=	addSlash(trim($p["testName"]));
		$testMethod	=	addSlash(trim($p["testMethod"]));
		$description		=	addSlash(trim($p["description"]));
		//$testMethodTableRowCount	= $p["hidTestMethodTableRowCount"];
		$lastId = "";
		//echo $p["brand_1"];
		
		if ($testName!="") 
		{
			$rmTestMasterRecIns	=	$rmTestMasterObj->addRMTestMaster($testName,$testMethod, $description, $userId);
			if ($rmTestMasterRecIns) 
			{
				$sessObj->createSession("displayMsg", $msg_succAddRmTestMaster);
				$sessObj->createSession("nextPage", $url_afterAddRMTestMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddRmTestMaster;
			}
			$rmTestMasterRecIns		=	false;
		}
	}
		
	# Edit Registration Type 
	if ($p["editId"]!="" )
	{
		$editId			=	$p["editId"];
		$editMode		=	true;
		$rmTestMasterRec		=	$rmTestMasterObj->find($editId);
		$rmTestMasterId			=	$rmTestMasterRec[0];
		$testName			=	stripSlash($rmTestMasterRec[1]);
		$testMethod				=	stripSlash($rmTestMasterRec[2]);
		$description	=	stripSlash($rmTestMasterRec[3]);
		//$testMethodRecs		= $rmTestMasterObj->getTestMethod($rmTestMasterId);
	}

	#Update
	if ($p["cmdSaveChange"]!="") 
	{
		
		$rmTestMasterId		=	$p["hidRmTestMasterId"];
		$testName		=	addSlash(trim($p["testName"]));
		$testMethod	=	addSlash(trim($p["testMethod"]));
		$description		=	addSlash(trim($p["description"]));
		//$testMethodTableRowCount	= $p["hidTestMethodTableRowCount"];
		if ($testName!="" ) 
		{
			$rmTestMasterRecUptd = $rmTestMasterObj->updateRmTestMaster($rmTestMasterId, $testName,$testMethod	, $description);
		}
	
		if ($rmTestMasterRecUptd) 
		{
			$sessObj->createSession("displayMsg",$msg_succRmTestMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateRmTestMaster.$selection);
		}
		else
		{
			$editMode	=	true;
			$err		=	$msg_failRmTestMaster;
		}
		$rmTestMasterRecUptd	=	false;
	}


	# Delete Registration Type
	if ($p["cmdDelete"]!="") 
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$rmTestMasterId	=	$p["delId_".$i];
			if ($rmTestMasterId!="") 
			{
				// Need to check the selected Department is link with any other process
				$rmTestMasterRecDel	=	$rmTestMasterObj->deleteRmTestMaster($rmTestMasterId);
				# Test Method
				//$delTestMethod	 = $rmTestMasterObj->deleteTestMethodRecs($rmTestMasterId);
			}
		}
		if ($rmTestMasterRecDel) 
		{
			$sessObj->createSession("displayMsg",$msg_succDelRmTestMaster);
			$sessObj->createSession("nextPage",$url_afterDelRmTestMaster.$selection);
		} 
		else
		{
			$errDel	=	$msg_failDelRmTestMaster;
		}
		$rmTestMasterRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$rmTestMasterId	=	$p["confirmId"];
			if ($rmTestMasterId!="") 
			{
				// Checking the selected fish is link with any other process
				$rmTestMasterRecConfirm = $rmTestMasterObj->updatermTestMasterconfirm($rmTestMasterId);
			}

		}
		if ($rmTestMasterRecConfirm) 
		{
			$sessObj->createSession("displayMsg",$msg_succConfirmRmTestMaster);
			$sessObj->createSession("nextPage",$url_afterDelRmTestMaster.$selection);
		} 
		else 
		{
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$rmTestMasterId = $p["confirmId"];
			if ($rmTestMasterId!="") 
			{
				#Check any entries exist
				$rmTestMasterRecConfirm = $rmTestMasterObj->updateRmTestMasterReleaseconfirm($rmTestMasterId);
			}
		}
		if ($rmTestMasterRecConfirm) 
		{
			$sessObj->createSession("displayMsg",$msg_succRelConfirmRmTestMaster);
			$sessObj->createSession("nextPage",$url_afterDelRmTestMaster.$selection);
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

	# List all Registration type ;
	$rmTestMasterRecords	=	$rmTestMasterObj->fetchAllPagingRecords($offset, $limit);
	$rmTestMasterSize		=	sizeof($rmTestMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($rmTestMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editRmTestMaster;
	else 		$heading = $label_addRmTestMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/rmTestMaster.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmRMTestMaster" action="RMTestMaster.php" method="post">
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
								$bxHeader = "Manage RM Test master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="30%">
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
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddRMTestMaster(document.frmRMTestMaster);">			
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddRMTestMaster(document.frmRMTestMaster);">
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidRmTestMasterId" value="<?=$rmTestMasterId;?>">
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Test Name</td>
																					<td><INPUT TYPE="text" NAME="testName" size="15" value="<?=$testName;?>"></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap>*Test Method</td>
																					<td>
																						<INPUT TYPE="text" NAME="testMethod" size="15" value="<?=$testMethod;?>">
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >Description</td>
																					<td ><textarea name="description"><?=$description;?></textarea></td>
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
																			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestMaster.php');">&nbsp;&nbsp;
																			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddRMTestMaster(document.frmRMTestMaster);">						
																		</td>
																		<?} else{?>
																		<td  colspan="2" align="center">
																			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestMaster.php');">&nbsp;&nbsp;
																			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddRMTestMaster(document.frmRMTestMaster);">	
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
								# Listing Registration Type Starts
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10" ></td>
					</tr>
					<tr>	
						<td colspan="3">
							<table cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td>
										<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmTestMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRmTestMaster.php',700,600);"><? }?>
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
						<td colspan="2" style="padding-left:10px; padding-right:10px;" >
							<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
								<?
								if ( sizeof($rmTestMasterRecords) > 0 ) {
									$i	=	0;
								?>
								<thead>
									<? if($maxpage>1){?>
									<tr>
										<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
											<div align="right">
											<?php
											$nav  = '';
											for ($page=1; $page<=$maxpage; $page++) 
											{
												if ($page==$pageNo) 
												{
													$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
												} else {
															$nav.= " <a href=\"RMTestMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) 
												{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"RMTestMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) 
												{
													$page = $pageNo + 1;
													$next = " <a href=\"RMTestMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">Test Name</th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">Test Method</th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Description </th>
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
									foreach($rmTestMasterRecords as $cr) {
										$i++;
										 $testNameId		=	$cr[0];
										 $testName		=	stripSlash($cr[1]);
										 //$testMethod	=	$rmTestMasterObj->getTestMethod($testNameId);
										 $testMethod		=	stripSlash($cr[2]);
										 $description		=	stripSlash($cr[3]);
										 $active=$cr[4];
										$existingrecords=$cr[5];
									?>
										<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
											<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$testNameId;?>" ></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$testName;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$testMethod;?>
											<?php
												/*$numLine = 3;
												if (sizeof($testMethod)>0) {
													$nextRec = 0;						
													foreach ($testMethod as $cR) {					
														$methodName = $cR[1];
														$nextRec++;
														if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $methodName;
														if($nextRec%$numLine == 0) echo "<br/>";	
													}
												}*/
												?>	
											</td>
											<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$description?></td>
											<? if($edit==true){?>
											<td class="listing-item" width="60" align="center">
												<?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$testNameId;?>,'editId'); this.form.action='RMTestMaster.php';"  ><?php } ?>
											</td>
											<? }?>
											<? if ($confirm==true){?>
											<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
												<?php 
												if ($confirm==true)
												{	
													if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$testNameId;?>,'confirmId');" >
													<?php } else if ($active==1){ if ($existingrecords==0) {?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$testNameId;?>,'confirmId');" >
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
												for ($page=1; $page<=$maxpage; $page++) 
												{
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"RMTestMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"RMTestMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"RMTestMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
										<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmTestMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRmTestMaster.php',700,600);"><? }?></td>
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
		
		<!--<tr>
			<td height="10"></td>
		</tr>
	</table>
	</td>
	</tr>-->
	</table>
	
	<?php 
		if ($addMode || $editMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewTestMethod()
		{
			addNewRow('testMethod', '', '', '', '','');	
		}

		
		function addNewItems()
		{
			addNewTestMethod();
			
		}
	</SCRIPT>
	<?php 
		} 
	?>

	<?php		
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewItems();
	</SCRIPT>
	<?php 
		}
	?>
	<!-- Edit Record -->
	<script language="JavaScript" type="text/javascript">	
		// Get state
	<?php
		if (sizeof($testMethodRecs)>0) {
			$j=0;
			foreach ($testMethodRecs as $ver) {			
				$testMethodId 	= $ver[0];
				$testMethodName	= rawurlencode(stripSlash($ver[1]));
						
	?>	
		addNewRow('testMethod','<?=$testMethodId?>', '<?=$testMethodName?>');		
	<?
			$j++;
			}
		} else if ($editMode) {
	?>
		addNewTestMethod();
	<?
		 }
	?>
	
	
	</script>
	</form>
	
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>