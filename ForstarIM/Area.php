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


	# Add Department Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Area
	if ($p["cmdAdd"]!="") {

		$areaUnitName		=	addSlash(trim($p["areaUnitName"]));
		$baseUnitReference	=	addSlash(trim($p["baseUnitReference"]));
		$values		=	addSlash(trim($p["values"]));
		
		
		if ($areaUnitName!="") {
			$areaRecIns	=	$areaObj->addArea($areaUnitName, $baseUnitReference, $values, $userId);

			if ($areaRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddArea);
				$sessObj->createSession("nextPage", $url_afterAddAreaa.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddArea;
			}
			$areaRecIns		=	false;
		}
	}
		
	# Edit Department 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$areaRec		=	$areaObj->find($editId);
		$areaId		=	$areaRec[0];
		$areaUnitName		=	stripSlash($areaRec[1]);
		$baseUnitReference	=	stripSlash($areaRec[2]);
		$values		=	stripSlash($areaRec[3]);
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$areaId		=	$p["hidAreaId"];
		$areaUnitName		=	addSlash(trim($p["areaUnitName"]));
		$baseUnitReference	=	addSlash(trim($p["baseUnitReference"]));
		$values		=	addSlash(trim($p["values"]));
		
		if ($areaId!="" && $areaUnitName!="") {
			$areaRecUptd = $areaObj->updateArea($areaId, $areaUnitName, $baseUnitReference, $values);
		}
	
		if ($areaRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succAreaUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateAreaa.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failAreaUpdate;
		}
		$areaRecUptd	=	false;
	}


	# Delete Department
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$areaId	=	$p["delId_".$i];

			if ($areaId!="") {
				// Need to check the selected Department is link with any other process
				$areaRecDel	=	$areaObj->deleteArea($areaId);
			}
		}
		if ($areaRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelArea);
			$sessObj->createSession("nextPage",$url_afterDelAreaa.$selection);
		} else {
			$errDel	=	$msg_failDelArea;
		}
		$areaRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$areaId	=	$p["confirmId"];
			if ($areaId!="") {
				// Checking the selected fish is link with any other process
				$area = $areaObj->updateAreaconfirm($areaId);
			}

		}
		if ($areaRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmarea);
			$sessObj->createSession("nextPage",$url_afterDelAreaa.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$areaId = $p["confirmId"];
			if ($areaId!="") {
				#Check any entries exist
				
					$areaRecConfirm = $areaObj->updateAreaReleaseconfirm($areaId);
				
			}
		}
		if ($areaRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmarea);
			$sessObj->createSession("nextPage",$url_afterDelAreaa.$selection);
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

	# List all Department ;
	$areaRecords	=	$areaObj->fetchAllPagingRecords($offset, $limit);
	$areaSize		=	sizeof($areaRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($areaObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
if ($addMode || $editMode) $categoryRecords	= $stockItemUnitObj->fetchAllRecordsunitActive();
	if ($editMode) 	$heading = $label_editArea;
	else 		$heading = $label_addArea;
	
	$ON_LOAD_PRINT_JS	= "libjs/Area.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmArea" action="Area.php" method="post">
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
					$bxHeader = "Manage Area";
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Area.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddArea(document.frmArea);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Area.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddArea(document.frmArea);">								</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidAreaId" value="<?=$areaId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<td class="fieldName" nowrap >*Area Unit Name (Acre/Hectare)</td>
												<td><INPUT TYPE="text" NAME="areaUnitName" size="15" value="<?=$areaUnitName;?>"></td>
											</tr>
	
											<tr>
											  <td  height="10" class="fieldName">*Base Unit Reference (Meter) </td>
										      <td  height="10" ><select name="baseUnitReference">
											  <option value="">--select--</option>
											  <?
												foreach($categoryRecords as $cr)
													{
														$unitId		=	$cr[0];
														$unitName	=	stripSlash($cr[1]);
														
														$selected	=	"";
														if( $unitId == $editCategoryId){
																$selected	=	"selected";
														}
											?>
											  <option value="<?=$unitId?>" <?=$selected?>><?=$unitName?></option>
											  <? }?>
										        </select>										      </td>
										  </tr>
	
	
											<tr>
												<td class="fieldName" nowrap >Value (x meter)</td>
												<td ><textarea name="values"><?=$values;?></textarea></td>
											</tr>
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Area.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddArea(document.frmArea);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Area.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddArea(document.frmArea);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
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
			
			# Listing Department Starts
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
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$areaSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintArea.php',700,600);"><? }?></td>
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
			if ( sizeof($areaRecords) > 0 ) {
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
      				$nav.= " <a href=\"Area.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Area.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Area.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Area Unit Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Base Unit Reference</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Value(x meter) </th>
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
	foreach($areaRecords as $cr) {
		$i++;
		$areaId		=	$cr[0];
		$areaUnitName		=	stripSlash($cr[1]);
		$unit	=	stripSlash($cr[2]);
		$baseUnitReference=$stockItemUnitObj->fetchUnit($unit);
		foreach($baseUnitReference as $base)
		//echo $abc=$base[0];
		$values		=	stripSlash($cr[3]);
		$active=$cr[4];
		$existingrecords=$cr[5];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$areaId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$areaUnitName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$base[0];?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$values?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$areaId;?>,'editId'); this.form.action='Area.php';"  >
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$areaId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$areaId;?>,'confirmId');" >
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
      				$nav.= " <a href=\"Area.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Area.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Area.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$areaSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintArea.php',700,600);"><? }?></td>
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