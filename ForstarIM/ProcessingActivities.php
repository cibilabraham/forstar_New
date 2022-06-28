<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;

	$selection 		=	"?pageNo=".$p["pageNo"];

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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# Add a ProcessingActivities	
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	#Insert Record
	if ($p["cmdAddProcessingActivities"]!="") {

		$name		=	addSlash(trim($p["name"]));
		$description	=	addSlash($p["description"]);
		$selSubModule	=	$p["selSubModule"];
		
		if ($name!="") {
			$processingActivityRecIns = $processingactivityObj->addProcessingActivity($name, $description, $selSubModule);

			if ($processingActivityRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddProcessingActivities);
				$sessObj->createSession("nextPage",$url_afterAddProcessingActivities.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddProcessingActivities;
			}
			$processingActivityRecIns	=	false;
		}
	}

	# Edit a Processing Activity
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$processingActivityRec	=	$processingactivityObj->find($editId);
		$processingActivityId	=	$processingActivityRec[0];
		$name			=	stripSlash($processingActivityRec[1]);
		$description		=	stripSlash($processingActivityRec[2]);
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$processingActivityId	=	$p["hidProcessingActivityId"];
		$name		=	addSlash(trim($p["name"]));
		$description	=	addSlash($p["description"]);
		$selSubModule	=	$p["selSubModule"];
		
		if ($processingActivityId!="" && $name!="") {
			$processingActivityRecUptd	=	$processingactivityObj->updateProcessingActivity($processingActivityId, $name, $description, $selSubModule);
		}
	
		if ($processingActivityRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProcessingActivitiesUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProcessingActivities.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProcessingActivitiesUpdate;
		}
		$processingActivityRecUptd	=	false;
	}


	# Delete ProcessingActivities
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processingActivityId	=	$p["delId_".$i];

			if ($processingActivityId!="") {
				if (!$processingactivityObj->moreEntriesExist($processingActivityId)) {
					$subModuleRecDel = $processingactivityObj->deleteActivity2SubModule($processingActivityId);
					// Need to check the selected fish is link with any other process

					$processingActivityRecDel = $processingactivityObj->deleteProcessingActivity($processingActivityId);
				}
			}

		}
		if ($processingActivityRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProcessingActivities);
			$sessObj->createSession("nextPage",$url_afterDelProcessingActivities.$selection);
		} else {
			$errDel	=	$msg_failDelProcessingActivities;
		}
		$processingActivityRecDel	=	false;
	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processingActivityId	=	$p["confirmId"];


			if ($processingActivityId!="") {
				// Checking the selected fish is link with any other process
				$processingactivityRecConfirm = $processingactivityObj->updateprocessingactivityconfirm($processingActivityId);
			}

		}
		if ($processingactivityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmprocessingactivity);
			$sessObj->createSession("nextPage",$url_afterDelProcessingActivities.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmprocessingactivity;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$processingActivityId = $p["confirmId"];

			if ($processingActivityId!="") {
				#Check any entries exist
				
					$processingactivityRecConfirm = $processingactivityObj->updateprocessingactivityReleaseconfirm($processingActivityId);
				
			}
		}
		if ($processingactivityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmprocessingactivity);
			$sessObj->createSession("nextPage",$url_afterDelProcessingActivities.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmprocessingactivity;
		}
		}
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All ProcessingActivitiess	
	$processingActivityRecords	= $processingactivityObj->fetchPagingRecords($offset, $limit);
	$processingActivitySize		= sizeof($processingActivityRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($processingactivityObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Submodule Records
	if ($addMode) {
		$subModuleRecords = $processingactivityObj->fetchAllSubModuleRecords();
	} else if ($editMode) {
		$subModuleRecords = $processingactivityObj->fetchSelectedSubModuleRecords($editId);
	}

	if ($editMode) $heading = $label_editProcessingActivities;
	else $heading = $label_addProcessingActivities;
	
	//$help_lnk="help/hlp_ProcessingActivities.html";

	$ON_LOAD_PRINT_JS	= "libjs/processingactivities.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProcessingActivities" action="ProcessingActivities.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" > <?=$err;?></td>
	</tr>
	<?}?>
	
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
					$bxHeader="Processing Activities";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<!--<tr>
					<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
					<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Processing Activities </td>
				</tr>-->
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
			<!--<tr>
				<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
				<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
			</tr>-->
			<tr>
				<td width="1" ></td>
				<td colspan="2" >
				<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="2" height="10" ></td>
				</tr>
				<tr>
				<? if ($editMode) {?>
					<td colspan="2" align="center">
					<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingActivities.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProcessingActivity(document.frmProcessingActivities);">
					</td>
				<?} else {?>
					<td  colspan="2" align="center">
					<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingActivities.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAddProcessingActivities" class="button" value=" Add " onClick="return validateProcessingActivity(document.frmProcessingActivities);">
					</td>
				<?}?>
				</tr>
				<input type="hidden" name="hidProcessingActivityId" value="<?=$processingActivityId;?>">
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
	<td colspan="2" align="center">
		<table align="center" width="100%">
			<TR><TD>
				<table align="center">
				<tr>
					<td class="fieldName" nowrap >*Name </td>
					<td><INPUT TYPE="text" NAME="name" size="24" value="<?=$name;?>"></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >*Sub Module </td>
					<td>
			<select name="selSubModule[]" size="7" multiple id="selSubModule">
                        <option value="" > Select Sub-Module </option>
                        <?
			$selSubModuleId = "";
			 foreach ($subModuleRecords as $smr) {
				$submoduleId	= $smr[0];
				$submodulename = $smr[1];				
				$selSubModuleId	= $smr[2];
				$selected		= "";
				if ($selSubModuleId==$submoduleId) $selected = "selected";
			?>
                        <option value="<?=$submoduleId;?>" <?=$selected;?>><?=$submodulename;?></option>
                        <?
				}
			?>
                        </select></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >Description</td>
					<td >
						<textarea name="description"><?=$description?></textarea>				
					</td>
				</tr>
				</table>
			</td></tr>
		</table>
	</td>
</tr>
				
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
					<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingActivities.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProcessingActivity(document.frmProcessingActivities);">
					</td>
				<?} else{?>
					<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingActivities.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAddProcessingActivities" class="button" value=" Add " onClick="return validateProcessingActivity(document.frmProcessingActivities);">
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
		# Listing Processing Activities Starts
	?>
		</table>
		</td>
	</tr>
	<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<?php
		}
	?>
			<tr>
				<td colspan="3" height="10" ></td>
			</tr>
			<tr>	
				<td colspan="3">
					<table cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processingActivitySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessingActivities.php',700,600);"><? }?></td>
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
							<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($processingActivityRecords) > 0 )
												{
													$i	=	0;
											?>
	<thead>
	<? if($maxpage>1){?>
<tr>
<td colspan="4" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"ProcessingActivities.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProcessingActivities.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProcessingActivities.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr align="center">
		<th width="30">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Sub-Module</th>
		<th style="padding-left:10px; padding-right:10px;">Description</th>
		<? if($edit==true){?>
		<th width="80">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
			<th>&nbsp;</th>
			<? }?>
	</tr>
	</thead>	
	<tbody>
		<?
		foreach($processingActivityRecords as $pr)
			{
				$i++;
				$processingActivityId		=	$pr[0];
				$name		=	stripSlash($pr[1]);
				$description	=	stripSlash($pr[2]);
				$active=$pr[3];
				#For listing Selected Sub Module Records
				$selSubModuleRecords = $processingactivityObj->filterSelectedSubModule($processingActivityId);
		?>
		<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
			<td width="30" align="center">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$processingActivityId;?>" class="chkBox"></td>
			
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left">
			<?
			$subModuleRecDisplayRow	=	2;
			if (sizeof($selSubModuleRecords)>0) {
				$sModuleNext	=	0;
				foreach ($selSubModuleRecords as $sModuleR) {
					$subModuleName	=	$sModuleR[3];
					$sModuleNext++;
					if($sModuleNext>1) echo "&nbsp;,&nbsp;"; echo $subModuleName;
					if ($sModuleNext%$subModuleRecDisplayRow == 0) echo "<br/>";
				}
			}
		?>		
			</td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
			<? if($edit==true){?>
			<td class="listing-item" width="70" align="center">
			 <?php if ($active!=1) {?>
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$processingActivityId;?>,'editId'); this.form.action='ProcessingActivities.php';">
			<?}?>
			</td>
			<? }?>
			<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$processingActivityId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="ReleaseConfirm " name="btnRlConfirm" onClick="assignValue(this.form,<?=$processingActivityId;?>,'confirmId');"  >
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
<td colspan="4" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"ProcessingActivities.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProcessingActivities.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProcessingActivities.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processingActivitySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessingActivities.php',700,600);"><? }?></td>
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
?>