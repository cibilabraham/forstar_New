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
	
	$selSubModule = array();
	$selSubModule	= $p["selSubModule"];

	//printr($selSubModule[0]);

	# Add 	
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}

	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode	= false;
		$p["editId"]	= "";
	}

	# Insert
	if ($p["cmdAdd"]!="") {
		$selSubModule	= $p["selSubModule"]; // array
		$refreshTime	= trim($p["refreshTime"]);
		$sSubModule	= implode(',',$selSubModule); // Comma seperated

		$selFunction	= $p["selFunction"];
		
		# Check Rec Exist
		$recExist	= $refreshTimeLimitObj->chkRecExist($sSubModule, $cId, $selFunction);

		if ($refreshTime!="" && sizeof($selSubModule)>0 && !$recExist) {
			$refreshTimeLimitRecIns = $refreshTimeLimitObj->addRefreshTimeLimit($sSubModule, $refreshTime, $userId, $selFunction);

			if ($refreshTimeLimitRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddRefreshTimeLimit);
				$sessObj->createSession("nextPage",$url_afterAddRefreshTimeLimit.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddRefreshTimeLimit;
			}
			$refreshTimeLimitRecIns	=	false;
		} else if ($recExist) {
			$err		= " Failed to add Refresh Time Limit. <br>The selected sub-modules are already in database. ";
			$addMode	=	true;
		}
	}


	# Edit 
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$refreshTimeLimitRec	=	$refreshTimeLimitObj->find($editId);
		$refreshTimeLimitId	=	$refreshTimeLimitRec[0];
		$submoduleIds		=	$refreshTimeLimitRec[1];
		$refreshTime		=	$refreshTimeLimitRec[2];
		$selFunction		=	$refreshTimeLimitRec[3];
		$selSubModule		= explode(",",$submoduleIds);
		$disabled = "disabled='true'";
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$refreshTimeLimitId	=	$p["hidProcessingActivityId"];
		$sSubModule		= $p["selSubModule"]; // array
		$refreshTime		= trim($p["refreshTime"]);
		//$sSubModule		= implode(',',$selSubModule); // comma seperated
		$selFunction		= $p["selFunction"];
		
		# Check Rec Exist
		$recExist	= $refreshTimeLimitObj->chkRecExist($sSubModule, $refreshTimeLimitId, $selFunction);

		if ($refreshTimeLimitId!="" && $refreshTime!="" && sizeof($selSubModule)>0 && !$recExist) {
			$refreshTimeLimitRecUptd =	$refreshTimeLimitObj->updateRefreshPageLimit($refreshTimeLimitId, $sSubModule, $refreshTime, $selFunction);
		}
	
		if ($refreshTimeLimitRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRefreshTimeLimitUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateRefreshTimeLimit.$selection);
		} else {
			$editMode	=	true;
			if ($recExist) $err = $msg_failRefreshTimeLimitUpdate."<br>The selected sub-modules are already in database.";
			else $err = $msg_failRefreshTimeLimitUpdate;
		}
		$refreshTimeLimitRecUptd	=	false;
	}


	# Delete RefreshTimeLimit
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$refreshTimeLimitId	=	$p["delId_".$i];

			if ($refreshTimeLimitId!="") {
				// Need to check the selected fish is link with any other process
				$refreshTimeLimitRecDel = $refreshTimeLimitObj->deleteRefreshTimeLimit($refreshTimeLimitId);		
			}
		}
		if ($refreshTimeLimitRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRefreshTimeLimit);
			$sessObj->createSession("nextPage",$url_afterDelRefreshTimeLimit.$selection);
		} else {
			$errDel	=	$msg_failDelRefreshTimeLimit;
		}
		$refreshTimeLimitRecDel	=	false;
	}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All RefreshTimeLimits
	
	$refreshTimeLimitRecords	=	$refreshTimeLimitObj->fetchPagingRecords($offset, $limit);
	$refreshTimeLimitRecSize	=	sizeof($refreshTimeLimitRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($refreshTimeLimitObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	$functionRecs = array();
	if (sizeof($selSubModule)>0) {
		$functionRecs = $refreshTimeLimitObj->getFunctionRecs($selSubModule[0]);
	} 

	#List all Submodule Records
	if ($addMode) {
		$subModuleRecords = $refreshTimeLimitObj->fetchAllSubModuleRecords();
	} else if ($editMode) {
		$subModuleRecords = $refreshTimeLimitObj->fetchSelectedSubModuleRecords($editId, $submoduleIds);
	}

	if ($editMode)	$heading	=	$label_editRefreshTimeLimit;
	else		$heading	=	$label_addRefreshTimeLimit;
	

	$ON_LOAD_PRINT_JS	= "libjs/RefreshTimeLimit.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmRefreshTimeLimit" action="RefreshTimeLimit.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="60%">
	<? if($err!="" ){?>
	<tr>
		<td height="40" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
	<?php
		if ($editMode || $addMode) {
	?>
	<tr>
		<td>
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
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
				<table cellpadding="0"  width="50%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="2" height="10" ></td>
				</tr>
				<tr>
				<? if ($editMode) {?>
					<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RefreshTimeLimit.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRefreshTimeLimit(document.frmRefreshTimeLimit);">
					</td>
				<?} else {?>
					<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RefreshTimeLimit.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRefreshTimeLimit(document.frmRefreshTimeLimit);">
					<input type="hidden" name="cmdAddNew" value="1" />
					</td>
				<?}?>
				</tr>
				<input type="hidden" name="hidProcessingActivityId" value="<?=$refreshTimeLimitId;?>">
				<tr><TD height="20"></TD></tr>
				<tr><TD colspan="2">
				<table>
					<tr>
					<td class="fieldName" nowrap >*Sub Module </td>
					<td>
	<!--size="7" multiple-->
				<select name="selSubModule[]" id="selSubModule" onchange="this.form.submit();" <?=$disabled?>>
				<option value="" >--Select--</option>
				<?php
				$selSubModuleId = "";
				foreach ($subModuleRecords as $smr) {
					$submoduleId	= $smr[0];
					$submodulename = $smr[1];
					
					$selSubModuleId	= $smr[2];
					$selected		= "";				
					if ($selSubModuleId!="" || in_array($submoduleId, $selSubModule)) $selected = "selected";
				?>
				<option value="<?=$submoduleId;?>" <?=$selected;?>><?=$submodulename;?></option>
				<?php
					} 
				?>
				</select>
				<?php
				if ($disabled) {
				?>
				<input type="hidden" name="selSubModule" id="selSubModule" value="<?=$submoduleIds;?>" readonly="true">
				<?php
					}
				?>
				</td>
				<td nowrap class="fieldName">Function:</td>
				<td nowrap="true">
					<select name="selFunction" id="selFunction">
						<option value="0">-- Select All --</option>
						<?php
						foreach ($functionRecs as $fr) {
							$smFunctionId 	=  $fr[0];
							$funName	=  $fr[1];
							$selected = ($selFunction==$smFunctionId)?"selected":"";
						?>
						<option value="<?=$smFunctionId?>" <?=$selected?>><?=$funName?></option>
						<?php
						}
						?>
					</select>
				</td>
				</tr>
				<tr>
				<td class="fieldName" nowrap >*Time</td>
				<td class="listing-item" nowrap="true">
					<INPUT TYPE="text" NAME="refreshTime" id="refreshTime" size="5" value="<?=$refreshTime;?>" style="text-align:right;"> Seconds
				</td>
			</tr>
				</table>
				</TD></tr>
				
			
			<tr>
				<td colspan="2"  height="10" ></td>
			</tr>
			<tr>
				<? if($editMode){?>
					<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RefreshTimeLimit.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRefreshTimeLimit(document.frmRefreshTimeLimit);">
					</td>
				<?} else{?>
					<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RefreshTimeLimit.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRefreshTimeLimit(document.frmRefreshTimeLimit);">
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
	# Listing Processing Activities Starts
	?>
	<tr>
		<td height="10" align="center" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
			<tr>
				<td   bgcolor="white">
				<!-- Form fields start -->
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
					<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Refresh Time Limit </td>
				</tr>
			<tr>
				<td colspan="3" height="10" ></td>
			</tr>
			<tr>	
				<td colspan="3">
					<table cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$refreshTimeLimitRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRefreshTimeLimit.php',700,600);"><? }?></td>
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
					<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
					<?
					if( sizeof($refreshTimeLimitRecords) > 0 ) {
						$i	=	0;
											?>
											<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
<td colspan="5" style="padding-right:10px">
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
					$nav.= " <a href=\"RefreshTimeLimit.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RefreshTimeLimit.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"RefreshTimeLimit.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<tr  bgcolor="#f2f2f2" align="center">
		<td width="30"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Sub-Module</td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Function</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Time (Seconds)</td>
		<? if($edit==true){?>
		<td class="listing-head" width="80"></td>
		<? }?>
		</tr>
		<?php
		foreach ($refreshTimeLimitRecords as $pr) {
			$i++;
			$refreshTimeLimitId	= $pr[0];				
			$selSubModuleIds	= stripSlash($pr[1]);
			$sRefreshTime		= $pr[2];
			$selFunctionName	= $pr[3];	
			$selSubModuleName	= $pr[4];

			#For listing Selected Sub Module Records
			//$selSubModuleRecords = $refreshTimeLimitObj->filterSelectedSubModule($refreshTimeLimitId, $selSubModuleIds);
		?>
		<tr  bgcolor="WHITE"  >
			<td width="30" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$refreshTimeLimitId;?>" class="chkBox"></td>			
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selSubModuleName?>
			<!--		<table>
				<tr>
				<?
				/*
				$subModuleRecDisplayRow	=	2;
				if (sizeof($selSubModuleRecords)>0) {
					$sModuleNext	=	0;
					foreach ($selSubModuleRecords as $sModuleR) {
						$subModuleName	=	$sModuleR[1];
						$sModuleNext++;
				*/
				?>
				<td class="listing-item" nowrap><?// if($sModuleNext>1) echo ",";?><?//=$subModuleName?></td>
				<? 
				//if ($sModuleNext%$subModuleRecDisplayRow == 0) {
				?>
					</tr>
					<tr>
				<?
				/* 
					}	
				}
			}
				*/
			?>
			</tr>
			</table>-->
			</td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selFunctionName;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$sRefreshTime;?></td>
			<? if($edit==true){?>
				<td class="listing-item" width="70" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$refreshTimeLimitId;?>,'editId'); this.form.action='RefreshTimeLimit.php';"></td>
			<? }?>
		</tr>
			<?
			}
			?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
<td colspan="5" style="padding-right:10px">
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
					$nav.= " <a href=\"RefreshTimeLimit.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RefreshTimeLimit.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"RefreshTimeLimit.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$refreshTimeLimitRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRefreshTimeLimit.php',700,600);"><? }?></td>
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
