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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------	
	
	# Add New	
	if ($p["cmdAddNew"]!="") $addMode = true;	

	# Add
	if ($p["cmdAdd"]!="") {	
		$parameterName	= addSlash(trim($p["parameterName"]));
		$unitId		= $p["unitId"];
		
		if ($parameterName!="") {
			$monitoringParameterRecIns = $monitoringParametersObj->addMonitoringParameter($parameterName, $unitId, $userId);
			if ($monitoringParameterRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddMonitoringParameters);
				$sessObj->createSession("nextPage",$url_afterAddMonitoringParameters.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddMonitoringParameters;
			}
			$monitoringParameterRecIns	=	false;
		}
	}
	
	# Edit 	
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$monitoringParameterRec	=	$monitoringParametersObj->find($editId);
		
		$editMonitoringParameterId	=	$monitoringParameterRec[0];
		$parameterName			=	stripSlash($monitoringParameterRec[1]);
		$unitId				=	$monitoringParameterRec[2];
	}

	# Update
	if ($p["cmdSaveChange"]!="") {

		$monitoringParameterId	=	$p["hidMonitoringParametersId"];
		$parameterName		= addSlash(trim($p["parameterName"]));
		$unitId			= $p["unitId"];
		
		if ($monitoringParameterId!="" && $parameterName!="") {
			$monitoringParameterRecUptd	= $monitoringParametersObj->updateMonitoringParameter($monitoringParameterId, $parameterName, $unitId);
		}
	
		if ($monitoringParameterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateMonitoringParameters);
			$sessObj->createSession("nextPage",$url_afterUpdateMonitoringParameters.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateMonitoringParameters;
		}
		$monitoringParameterRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++)	{
			$monitoringParameterId	=	$p["delId_".$i];

			if ( $monitoringParameterId!="" ) {
				$monitoringParameterRecDel = $monitoringParametersObj->deleteTypeOfOperation($monitoringParameterId);
			}
		}
		if ($monitoringParameterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelMonitoringParameters);
			$sessObj->createSession("nextPage",$url_afterDelMonitoringParameters.$selection);
		} else {
			$errDel	=	$msg_failDelMonitoringParameters;
		}
		$monitoringParameterRecDel	=	false;
	}



	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$monitoringParameterId	=	$p["confirmId"];
			if ($monitoringParameterId!="") {
				// Checking the selected fish is link with any other process
				$monitoringParameterRecConfirm = $monitoringParametersObj->updateMonitoringParametersconfirm($monitoringParameterId);
			}

		}
		if ($monitoringParameterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmMonitoringParameters);
			$sessObj->createSession("nextPage",$url_afterDelMonitoringParameters.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$monitoringParameterId = $p["confirmId"];
			if ($monitoringParameterId!="") {
				#Check any entries exist
				
					$monitoringParameterRecConfirm = $monitoringParametersObj->updateMonitoringParametersReleaseconfirm($monitoringParameterId);
				
			}
		}
		if ($monitoringParameterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmMonitoringParameters);
			$sessObj->createSession("nextPage",$url_afterDelMonitoringParameters.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "" ) $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	#List All Record	
	$monitoringParameterRecords	=	$monitoringParametersObj->fetchPagingRecords($offset, $limit);
	$monitoringParameterRecordSize	=	sizeof($monitoringParameterRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($monitoringParametersObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {
		# Unit Records
		$stockUnitRecs = $stockItemUnitObj->fetchAllRecords();
	}

	if ($editMode)	$heading = $label_editMonitoringParameters;
	else $heading = $label_addMonitoringParameters;
	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/MonitoringParameters.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmMonitoringParameters" action="MonitoringParameters.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
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
								$bxHeader="MONITORING Factors";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;EU Code </td>
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('MonitoringParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateMonitoringParameters(document.frmMonitoringParameters);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('MonitoringParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateMonitoringParameters(document.frmMonitoringParameters);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidMonitoringParametersId" value="<?=$editMonitoringParameterId;?>">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center"> <table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*Name</td>
                                                  <td class="listing-item"><input name="parameterName" type="text" id="parameterName" size="28" value="<?=$parameterName?>"></td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*Unit</td>
                                                 <td class="listing-item">
							<select name="unitId" id="unitId">
							<option value="">--Select--</option>
							<?php
							foreach ($stockUnitRecs as $sur) {
								$stkUnitId 	= $sur[0];
								$unitName	= $sur[1];
								$selected = ($stkUnitId==$unitId)?"selected":"";
							?>
							<option value="<?=$stkUnitId?>" <?=$selected?>><?=$unitName?></option>
							<?php
								}
							?>
							</select>
						</td>
                                                </tr>
                                              </table></td>
											</tr>
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MonitoringParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateMonitoringParameters(document.frmMonitoringParameters);">	
												</td>
												<? } else{?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MonitoringParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateMonitoringParameters(document.frmMonitoringParameters);">
												</td>

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
			# Listing Starts
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$monitoringParameterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintMonitoringParameters.php',700,600);"><? }?></td>
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
							<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($monitoringParameterRecords) > 0 )
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
					$nav.= " <a href=\"MonitoringParameters.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MonitoringParameters.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"MonitoringParameters.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
		<th style="padding-left:10px; padding-right:10px;">Unit</th>
		<? if($edit==true){?>
		<th width="45">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
	<?php
		foreach($monitoringParameterRecords as $mpr) {
			$i++;
			$monitoringParameterId	=	$mpr[0];
			$parameterName		=	stripSlash($mpr[1]);
			$unitId			=	stripSlash($mpr[2]);
			$selUnitName		=	stripSlash($mpr[3]);
			$active=$mpr[4];
			$existingrecords=$mpr[5];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$monitoringParameterId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$parameterName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selUnitName;?></td>
		<? if($edit==true){?>
		  <td class="listing-item" width="45" align="center">
		  <?php if ($active!=1) {?>
		  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$monitoringParameterId;?>,'editId');">
		  <? } ?>
		  </td>
		  <? }?>


 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$monitoringParameterId;?>,'confirmId');"  >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$monitoringParameterId;?>,'confirmId');"  >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>

	</tr>
	<?php
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
					$nav.= " <a href=\"MonitoringParameters.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MonitoringParameters.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"MonitoringParameters.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$monitoringParameterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintMonitoringParameters.php',700,600);"><? }?></td>
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