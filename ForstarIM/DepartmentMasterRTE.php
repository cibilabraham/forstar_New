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
	if ($p["cmdAddDepartment"]!="") {

		$name	=	addSlash(trim($p["name"]));
		$description	=	addSlash(trim($p["description"]));
		$type	=	addSlash(trim($p["type"]));
		//echo "hii";
		if ($name!="" && $type!="") {
			$departmentRecIns	=	$departmentMasterObj->addDepartment($name,$description,$type,$userId);

			if ($departmentRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddDepartmentRte);
				$sessObj->createSession("nextPage",$url_afterDelDepartmentRte.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddDepartmentRte;
			}
			$departmentRecIns		=	false;
		}

	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$departmentId	=	$p["confirmId"];


			if ($departmentId!="") {
				// Checking the selected fish is link with any other process
				$departmentRecConfirm = $departmentMasterObj->updateDepartmentconfirm($departmentId);
			}

		}
		if ($departmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmDepartmentRte);
			$sessObj->createSession("nextPage",$url_afterDelDepartmentRte.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$departmentId	=	$p["confirmId"];
			if ($departmentId!="") {
			#Check any entries exist
				$departmentRecConfirm = $departmentMasterObj->updateDepartmentReleaseconfirm($departmentId);
			}
		}
		if ($departmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmDepartmentRte);
			$sessObj->createSession("nextPage",$url_afterDelDepartmentRte.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}

	
	# Edit staff 
	if ($p["editId"]!="") {
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$departmentRec		=	$departmentMasterObj->find($editIt);
		$departmentId			=	$departmentRec[0];
		$name		=	stripSlash($departmentRec[1]);
		$description		=	stripSlash($departmentRec[2]);
		$type		=	stripSlash($departmentRec[3]);
		($type=="production")?$selProduction="selected":$selProduction="";
		($type=="marketing")?$selMarketing="selected":$selMarketing="";
		($type=="operation")?$selOperation="selected":$selOperation="";
		
	}

	if ($p["cmdSaveChange"]!="") {
		
		$departmentId		=	$p["hidDepartmentId"];
		$name	=	addSlash(trim($p["name"]));
		$description	=	addSlash(trim($p["description"]));
		$type	=	addSlash(trim($p["type"]));
		if ($departmentId!="" && $name!="" && $type!="") {
			$departmentRecUptd		=	$departmentMasterObj->updateDepartment($departmentId,$name,$description,$type);
		}
	
		if ($departmentRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDepartmentRteUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDepartmentRte.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failDepartmentRteUpdate;
		}
		$departmentRecUptd	=	false;
	}


	# Delete staff
	if ($p["cmdDelete"]!="") {
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$departmentId	=	$p["delId_".$i];
			if ($departmentId!="") 
			{
				// Checking the selected fish is link with any other process
				$staffRecInUse = $departmentMasterObj->staffRecInUse($departmentId);
				if (!$staffRecInUse) 
				{
					$departmentRecDel = $departmentMasterObj->deleteDepartment($departmentId);	
				}
			}

		}
		if ($departmentRecDel) 
		{
			$sessObj->createSession("displayMsg",$msg_succDelDepartmentRte);
			$sessObj->createSession("nextPage",$url_afterDelDepartmentRte.$selection);
		} 
		else 
		{
			$errDel	=	$msg_failDelDepartmentRte;
		}
		$departmentRecDel	=	false;

	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Fishes		
	$departmentRecords	=	$departmentMasterObj->fetchAllPagingRecords($offset, $limit);
	$departmentMasterSize		=	sizeof($departmentRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($departmentMasterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	/*
	# List all Fish Category;
	$sourceRecords = array();
	//if ($addMode || $editMode) $categoryRecords	= $fishcategoryObj->fetchAllRecords();
	if ($addMode || $editMode) { 
		$categoryRecords	= $fishcategoryObj->fetchAllRecordscategoryActive(); 
		$sourceRecords	    = $departmentMasterObj->fetchAllSourceRecords();
	}
	*/
	if ($editMode) $heading = $label_editDepartment;
	else $heading = $label_addDepartment;

	$help_lnk="help/hlp_addFishMaster.html";

	$ON_LOAD_PRINT_JS	= "libjs/departmentmasterrte.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>


	<form name="frmDepartmentMaster" action="DepartmentMasterRTE.php" method="post">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddDepartment" class="button" value=" Add " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>

												<?}?>
											</tr>
											
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddDepartment" class="button" value=" Add " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>

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
								$bxHeader="Department Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Master</td>
								</tr>-->
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddDepartment" class="button" value=" Add " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>

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
												<td class="fieldName" nowrap >*Type</td>
												<td><select name="type" id="type">
												<option value="">--Select--</option>
												<option value="production" <?=$selProduction?>>Production</option>
												<option value="marketing" <?=$selMarketing?>>Marketing</option>
												<option value="operation" <?=$selOperation?>>Operation</option>
												</select></td>
											</tr>
											<tr>
												<td colspan="2"  height="10" ><input type="hidden" name="hidDepartmentId" value="<?=$departmentId;?>"></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DepartmentMasterRTE.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddDepartment" class="button" value=" Add " onClick="return validateAddDepartment(document.frmDepartmentMaster);">												</td>

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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$staffMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDepartmentMasterRTE.php',700,600);"><? }?></td>
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
											  <td colspan="6" align="right" style="padding-right:10px;" class="navRow">
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
      	$nav.= " <a href=\"DepartmentMasterRTE.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"DepartmentMasterRTE.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"DepartmentMasterRTE.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div> </td>
		  </tr>
										  <? }?>
											<tr >
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th>Name</td>
												<th nowrap>Description</th>
												<th nowrap>Type </th>

											<? if($edit==true){?>	<th class="listing-head"></th><? }?>
											<? if($confirmF==true){?>	<th class="listing-head"></th><? }?>
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
														$type	=	stripSlash($dr[3]);	
														$active=$dr[4];
														
														//echo "existing count is $existingcount";
														//echo $confirmF;
																								
											?>
											<tr   <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>   >
												<? if($name!="R&D")
												{ ?><td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$departmentId;?>" class="chkBox"></td>
												<? }
												else{?>
												  <td width="20" align="center"></td>
												<? }?>
												<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$name;?></td>
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$description;?>&nbsp;</td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$type?></td>
												<? 
												
												if($edit==true){?>
												<td class="listing-item" width="45" align="center"><?php if ($active!=1 && $name!="R&D") { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$departmentId;?>,'editId'); this.form.action='DepartmentMasterRTE.php';" ><?php }
												?></td> 
																	<? }?>

												<? if ($confirmF==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0 && $name!="R&D"){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$departmentId;?>,'confirmId');" >
			<?php } else if ($active==1 && $name!="R&D"){ if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$departmentId;?>,'confirmId');" >
			<?php } ?>
			<?php }?>
			<? }
			?>
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
      	$nav.= " <a href=\"DepartmentMasterRTE.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"DepartmentMasterRTE.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"DepartmentMasterRTE.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$staffMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDepartmentMasterRTE.php',700,600);"><? }?></td>
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

