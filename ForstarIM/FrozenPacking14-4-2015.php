<?php
	require("include/include.php");
	require_once("libjs/xajax_core/xajax.inc.php");
	$xajax = new xajax();
	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$recUpdated 	= false;
		
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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
//----------------------------------------------------------	
	
	#For Refreshing the main Window when click PopUp window
	if ($g["popupWindow"]=="") $popupWindow = $p["popupWindow"];
	else $popupWindow = $g["popupWindow"];
	
	# Add New	
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	# Variable resetting section	
	if ($p["selFrznPkgCode"]) $selFrznPkgCode = $p["selFrznPkgCode"];

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
		$selFrznPkgCode = "";
		$editId = "";
	}

	# Add
	if( $p["cmdAdd"]!="" ) {	
		$frozenCode		= addSlash(trim($p["frozenCode"]));
		$selUnit		= addslash($p["selUnit"]);
		
		$selFreezing		= explode("_",$p["freezing"]);
		$freezingId		= $selFreezing[0];

		$declWt			= $p["declWt"];
		
		$selGlaze		= explode("_",$p["glaze"]);
		$glazeId		= $selGlaze[0];
		
		$filledWt		= $p["filledWt"];
		$description		= addSlash($p["frozenPackingDescr"]);
		$actualFilledWt		= trim($p["actualFilledWt"]);
				
		if ( $frozenCode!="" ) {
			$frozenPackingRecIns	= $frozenpackingObj->addFrozenPacking($frozenCode, $selUnit, $freezingId, $declWt, $glazeId, $filledWt, $description, $actualFilledWt);
			
			if ($frozenPackingRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddFrozenPacking);
				$sessObj->createSession("nextPage",$url_afterAddFrozenPacking.$selection);
				$recUpdated = true;
				$selFrznPkgCode = "";
				$editId = "";
				$p["editId"] = "";
				$editMode		= false;
				$addMode		= false;
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddFrozenPacking;
			}
			$frozenPackingRecIns	=	false;
		}
	}
		
	
	# Edit a Record	
	if ($p["editId"]!="" || $selFrznPkgCode) {

		if ($selFrznPkgCode) $editId = $selFrznPkgCode;
		else $editId			= $p["editId"];
		if (!$selFrznPkgCode) $editMode		= true;		
		$frozenPackingRec	= $frozenpackingObj->find($editId);
		$editFrozenPackingId = "";
		if (!$selFrznPkgCode) $editFrozenPackingId	= $frozenPackingRec[0];
		$frozenCode		=	$frozenPackingRec[1];
		$unit			=	$frozenPackingRec[2];
		
		$freezingId		=	$frozenPackingRec[3];
		$freezingRec		=	$freezingObj->find($freezingId);
		$glazeOperator		=	$freezingRec[3];
		
		$selFreezing		=	$freezingId."_".$glazeOperator;
		
		$declWt				=	$frozenPackingRec[4];
		
		$glazeId			=	$frozenPackingRec[5];
		$glazeRec		=	$glazeObj->find($glazeId);
		$glaze				=	stripSlash($glazeRec[1]);
		
		$selGlaze		=	$glazeId."_".$glaze;
		
		$filledWt		=	$frozenPackingRec[6];
		$description	=	$frozenPackingRec[7];
		$actualFilledWt		= $frozenPackingRec[8];

	}
	
	# Update
	if ( $p["cmdSaveChange"]!="" ) {
		
		$frozenPackingId		=	$p["hidFrozenPackingId"];
		
		$frozenCode		=	addSlash(trim($p["frozenCode"]));
		$selUnit		=	addslash($p["selUnit"]);
		
		$selFreezing		=	explode("_",$p["freezing"]);
		$freezingId				=	$selFreezing[0];

		$declWt			=	$p["declWt"];
		
		$selGlaze			=	explode("_",$p["glaze"]);
		$glazeId		=	$selGlaze[0];
		
		$filledWt		=	$p["filledWt"];
		$description		=	addSlash($p["frozenPackingDescr"]);
		$actualFilledWt		= trim($p["actualFilledWt"]);
		
		if ( $frozenPackingId!="" && $frozenCode!="" ) {
			$frozenPackingRecUptd	=	$frozenpackingObj->updateFrozenPacking($frozenPackingId,$frozenCode, $selUnit, $freezingId,$declWt,$glazeId, $filledWt,$description, $actualFilledWt);
		}
	
		if ($frozenPackingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateFrozenPacking);
			$sessObj->createSession("nextPage",$url_afterUpdateFrozenPacking.$selection);
			$recUpdated = true;
			$selFrznPkgCode = "";
			$editId = "";
			$p["editId"] = "";
			$editMode		= false;
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFrozenPacking;
		}
		$frozenPackingRecUptd	=	false;
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$frozenPackingId	= $p["delId_".$i];

			if ( $frozenPackingId!="" ) {
				$frozenPackingRecDel = $frozenpackingObj->deleteFrozenPacking($frozenPackingId);
				$recUpdated = true;
			}
		}
		if ($frozenPackingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelFrozenPacking);
			$sessObj->createSession("nextPage",$url_afterDelFrozenPacking.$selection);
			$recUpdated = true;
		} else {
			$errDel	=	$msg_failDelFrozenPacking;
		}
		$frozenPackingRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$frozenPackingId	=	$p["confirmId"];
			if ($frozenPackingId!="") {
				// Checking the selected fish is link with any other process
				$frozenPackingRecConfirm = $frozenpackingObj->updateFrozenPackingconfirm($frozenPackingId);
			}

		}
		if ($frozenPackingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmfrozenPacking);
			$sessObj->createSession("nextPage",$url_afterDelFrozenPacking.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
	$rowCount	=	$p["hidRowCount"];
	for ($i=1; $i<=$rowCount; $i++) {

			$frozenPackingId= $p["confirmId"];

			if ($frozenPackingId!="") {
				#Check any entries exist
				
					$frozenPackingRecConfirm = $frozenpackingObj->updateFrozenPackingReleaseconfirm($frozenPackingId);
				
			}
		}
		if ($frozenPackingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmfrozenPacking);
			$sessObj->createSession("nextPage",$url_afterDelFrozenPacking.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I ------------------
	if ( $p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ( $g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Record	
	$frozenPackingRecords		= $frozenpackingObj->fetchPagingRecords($offset, $limit);
	$frozenPackingRecordSize	= sizeof($frozenPackingRecords);
	$fetchAllFrznCodeRecs		= $frozenpackingObj->fetchAllRecords();
	$fetchAllFrznCodeRecsActive		= $frozenpackingObj->fetchAllRecordsActiveFrozen();

## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllFrznCodeRecs);
	$maxpage	=	ceil($numrows/$limit);
## ----------------- Pagination Settings II End ------------	

	if ($addMode || $editMode) {
		#List Freezing Records
		$freezingRecords	= $freezingObj->fetchAllRecordsActivefreezing();
		
		#List All Glazing Records
		$glazeRecords		= $glazeObj->fetchAllRecordsGlazeActive();
	}

	if ($editMode) $heading	= $label_editFrozenPacking;
	else $heading	= $label_addFrozenPacking;
	

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else $mode = "";

	$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	//$xajax_js = $frozenpackingObj->xajax_js;

	$ON_LOAD_PRINT_JS	= "libjs/frozenpacking.js";

	# Xajax Settings
	//$xajax->registerFunction("chkFrznCodeExist");
 	$xajax->register(XAJAX_FUNCTION, 'chkFrznCodeExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
 	$xajax->ProcessRequest();

	# Include Template [topLeftNav.php]
	//require("template/topLeftNav.php");
	if (!$popupWindow) require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmFrozenPacking" action="FrozenPacking.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
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
								$bxHeader="Frozen Packing Code";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Frozen Packing Code </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="65%" align="center">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFrozenPacking(document.frmFrozenPacking);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateFrozenPacking(document.frmFrozenPacking);">
												<input type="hidden" name="cmdAddNew" value="1">
												</td>

												<?}?>
											</tr>
						<input type="hidden" name="hidFrozenPackingId" value="<?=$editFrozenPackingId;?>">			
								<tr>
									  <td colspan="2" align="center" height="5"></td>
								</tr>
					<tr>
					<td colspan="2" align="center"> 
					<table width="50%">
						<?php
							 if ($addMode) {
						?>
						<tr><TD colspan="2" align="center">
						<table><TR><TD>
						<!--<fieldset><legend class="listing-item" onMouseover="ShowTip('Copy from existing Frozen packing code and save after editing.');" onMouseout="UnTip();">Copy From</legend>-->
			<?php			
				$entryHead = "Copy From";
				require("template/rbTop.php");
			?>
			<table border="0" cellpadding="4" cellspacing="2">
				<TR>
					<TD class="fieldName" onMouseover="ShowTip('Copy from existing Frozen packing code and save after editing.');" onMouseout="UnTip();">Frozen Code:</TD>
					<td>
						<!--<select name="selFrznPkgCode" id="selFrznPkgCode" onchange="this.form.submit();">-->
						<select name="selFrznPkgCode" id="selFrznPkgCode" onchange="frozenPackingLoad(this)" >
							<option value="">--Select--</option>
							<?php
								foreach($fetchAllFrznCodeRecsActive as $fcr) {
									$sFrznPkgCodeId	= $fcr[0];
									$sFrznPkgCode = stripSlash($fcr[1]);
									$selected = ($selFrznPkgCode==$sFrznPkgCodeId)?"selected":"";
							?>
							<option value="<?=$sFrznPkgCodeId?>" <?=$selected?>><?=$sFrznPkgCode?></option>
							<? }?>
						</select>
					</td>
				</TR>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
			</TD></TR></table>
		</TD></tr>
		<?php
			}
		?>
	<tr><TD colspan="2" align="center">
	<table cellpadding="4" cellspacing="0">
		<TR>
		<TD valign="top">
		<table>
			<tr>
                                                  <td class="fieldName" nowrap="nowrap">Frozen Code</td>
                                                  <td class="listing-item" nowrap="true">
							<input name="frozenCode" type="text" id="frozenCode" size="24" value="<?=$frozenCode?>" onchange="xajax_chkFrznCodeExist(document.getElementById('frozenCode').value, '<?=$editFrozenPackingId?>', '<?=$mode?>');">
							<input type="hidden" name="hidFrozenCode" id="hidFrozenCode" value="<?=$frozenCode?>" size="14">
							<span id="divFrznCodeExistMsg" class="err1" style="font-size:11px;line-height:normal;"></span>
						</td>
                                                </tr>
						<tr>
                                                  <td class="fieldName" nowrap="nowrap">Unit of Wt</td>
                                                  <td class="listing-item">
												<select name="selUnit" id="selUnit">
												<option value="">--select--</option>
												<option value="Kg" <?if($unit=="Kg") echo "selected";?>>Kg</option>
												<option value="Lb" <?if($unit=="Lb") echo "selected";?> >Lb</option>
												</select>												</td>
                                                </tr>
						<tr>
                                                  <td class="fieldName" nowrap="nowrap">Freezing</td>
                                                  <td class="listing-item"><select name="freezing" id="freezing" onchange="return calculateFilledWt();">
												   <option value="">--select--</option>
												   <?
												   foreach($freezingRecords as $fr)
													{
														$freezingId		=	$fr[0];
														$freezingName	=	stripSlash($fr[1]);
														$glazeOperator	=	$fr[3];
														
														$optionValue	=	$freezingId."_".$glazeOperator;
														$selected		=	"";
														if($selFreezing==$optionValue) $selected="Selected";
												   ?>
												   
                                                    <option value="<?=$optionValue?>" <?=$selected?>><?=$freezingName?></option>
                                                    <? }?>
                                                  </select>                                                  </td>
                                                </tr>
						<tr>
                                                  <td class="fieldName" nowrap="nowrap">Declared Wt</td>
                                                  <td class="listing-item"><input name="declWt" type="text" id="declWt" size="3"  onkeyup="return calculateFilledWt();" style="text-align:right;" value="<?=$declWt?>"></td>
                                                </tr>
		</table>
		</TD>
		<TD valign="top">
		<table>
		<tr>
                                                  <td class="fieldName" nowrap="nowrap">Glaze %:</td>
                                                  <td class="listing-item">
												  <select name="glaze" id="glaze" onchange="return calculateFilledWt();">
												  	<option value="">--select--</option>
													<?

													foreach($glazeRecords as $gr)
													{
														$glazeId		=	$gr[0];
														$glazePercent	=	stripSlash($gr[1]);
														$displayGlaze	=	$glazePercent."%";
														$glazeValue		=	$glazeId."_".$glazePercent;
														$selected		=	"";
														if($selGlaze==$glazeValue) $selected="Selected";
											?>
                                                    <option value="<?=$glazeValue?>" <?=$selected?>><?=$displayGlaze?></option>
                                                   <? }?>
                                                  </select></td>
                                                </tr>
		<tr>
                                                  <td class="fieldName" nowrap="nowrap">Filled Wt</td>
                                                  <td class="listing-item"><input name="filledWt" type="text" id="filledWt" size="5" readonly style="text-align:right;" value="<?=$filledWt?>"></td>
                 </tr>
		<tr>
                                                  <td class="fieldName" nowrap="nowrap">Actual Filled Wt</td>
                                                  <td class="listing-item"><input name="actualFilledWt" type="text" id="actualFilledWt" size="5" style="text-align:right;" value="<?=$actualFilledWt?>"></td>
		</tr>
		<tr>
                                                  <td class="fieldName" nowrap="nowrap">Description</td>
                                                  <td class="listing-item"><textarea name="frozenPackingDescr" id="frozenPackingDescr"><?=$description?></textarea></td>
		</tr>
		</table>
		</TD>
		</TR>
	</table>
	</TD></tr>
                    </table>
			</td>
		</tr>
										<tr>
											  <td colspan="2" align="center" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateFrozenPacking(document.frmFrozenPacking);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateFrozenPacking(document.frmFrozenPacking);">												</td>

												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
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
			# Listing Grade Starts
		?>
	</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
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
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$frozenPackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFrozenPacking.php',700,600);"><? }?></td>
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
										<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($frozenPackingRecords) > 0 )
												{
													$i	=	0;
											?>
<thead>
<? if($maxpage>1){?>
<tr>
<td colspan="12" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"FrozenPacking.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FrozenPacking.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"FrozenPacking.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th nowrap style="padding-left:5px; padding-right:5px;">Frozen Code</th>
		<th style="padding-left:5px; padding-right:5px;">Unit of Wt</th>
		<th style="padding-left:5px; padding-right:5px;">Freezing</th>
		<th style="padding-left:5px; padding-right:5px;">Declared Wt</th>
		<th style="padding-left:5px; padding-right:5px;">Glaze %</th>
		<th style="padding-left:5px; padding-right:5px;">Operator</th>
		<th style="padding-left:5px; padding-right:5px;">Filled Wt</th>
		<th style="padding-left:5px; padding-right:5px;">Actual<br/> Filled Wt</th>
		<th style="padding-left:5px; padding-right:5px;">Description</th>
		<? if($edit==true){?>
		<th width="45">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($frozenPackingRecords as $fpr) {
		$i++;
		$frozenPackingId		=	$fpr[0];
		$frozenPackingCode		=	stripSlash($fpr[1]);
		$unit			=	$fpr[2];
		$freezingId		=	$fpr[3];
		$freezingCode	=	$freezingObj->findFreezingCode($freezingId);
		$freezingRec	=	$freezingObj->find($freezingId);
		$glazeOperator		=	$freezingRec[3];
		$displayOperator = "";
		if($glazeOperator==1) {
			$displayOperator = "Add";
		} else if($glazeOperator==0) {
			$displayOperator = "Deduct";
		} else {
			$displayOperator = "None";
		}
		$declWt			=	$fpr[4];
		$glazeId		=	$fpr[5];
		$glaze			=	$glazeObj->findGlazePercentage($glazeId);	
		$filledWt		=	$fpr[6];
		$description	=	$fpr[7];
		$selActualFilledWt = ($fpr[8]!=0)?$fpr[8]:"";
		$active=$fpr[9];
		$existingrecords=$fpr[10];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();"  <?php }?>>
		<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$frozenPackingId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$frozenPackingCode;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$unit;?>&nbsp;</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$freezingCode?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$declWt?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$glaze?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$displayOperator?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$filledWt?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$selActualFilledWt?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$description?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$frozenPackingId;?>,'editId'); this.form.action='FrozenPacking.php';"></td>
		  <? }?>
		  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm"  onClick="assignValue(this.form,<?=$frozenPackingId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) { ?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$frozenPackingId;?>,'confirmId');"  >
			<?php } }?>
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
<td colspan="12" style="padding-right:10px" class="navRow">
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
					$nav.= " <a href=\"FrozenPacking.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FrozenPacking.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"FrozenPacking.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$frozenPackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFrozenPacking.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" >
									<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">
								</td>
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
			<td height="10">
				<input type="hidden" name="popupWindow" id="popupWindow" value="<?=$popupWindow?>">
			</td>
		</tr>	
	</table>

	<?php 
		if ($recUpdated && $popupWindow!="") {
	?>
	<script language="JavaScript" type="text/javascript">
		// Shipment purchase order FPC: Frozen Pkg code
		parent.reloadDropDownList('FPC');	
	</script>
	<?php
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if (!$popupWindow) require("template/bottomRightNav.php");
?>