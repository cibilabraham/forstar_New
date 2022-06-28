<?php
	require("include/include.php");
	require_once("lib/ChangesUpdateMaster_ajax.php");

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;

	$selection 		=	"?pageNo=".$p["pageNo"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	# Add Pre-Processor Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}

	# Insert 
	if ($p["cmdAddPreProcessor"]!="") {
		$Code		=	addSlash($p["preProcessorCode"]);
		$Name		=	addSlash($p["preProcessorName"]);
		$Address	=	addSlash($p["preProcessorAddress"]);
		$Place		=	addSlash($p["preProcessorPlace"]);
		$Pincode	=	addSlash($p["preProcessorPincode"]);
		$TelNo		=	addSlash($p["preProcessorTelNo"]);
		$FaxNo		=	addSlash($p["preProcessorFaxNo"]);
		$Email		=	addSlash($p["preProcessorEmail"]);
		$LstNo		=	addSlash($p["preProcessorLstNo"]);
		$CstNo		=	addSlash($p["preProcessorCstNo"]);
		$PanNo		=	addSlash($p["preProcessorPanNo"]);
		$selPlant	=	$p["selPlant"];
		$selActivity	=	$p["selActivity"];
		$processorStatus = $p["processorStatus"];

		if ($Code!="" && $Name!="") {
			$PreProcessorRecIns	=	$preprocessorObj->addPreProcessor($Code, $Name, $Address, $Place, $Pincode, $TelNo, $FaxNo,$Email, $LstNo, $CstNo, $PanNo, $selPlant, $selActivity, $processorStatus);

			if ($PreProcessorRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddPreProcessor);
				$sessObj->createSession("nextPage",$url_afterAddPreProcessor.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddPreProcessor;
			}
			$PreProcessorRecIns		=	false;
		}
	}

	# Edit Landing Center
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$processorRec	=	$preprocessorObj->find($editId);

		$Id		=	$processorRec[0];
		$Code		=	stripSlash($processorRec[1]);
		$Name		=	stripSlash($processorRec[2]);
		$Address	=	stripSlash($processorRec[3]);
		$Place		=	stripSlash($processorRec[4]);
		$Pincode	=	stripSlash($processorRec[5]);
		$TelNo		=	stripSlash($processorRec[6]);
		$FaxNo		=	stripSlash($processorRec[7]);
		$Email		=	stripSlash($processorRec[8]);
		$LstNo		=	stripSlash($processorRec[9]);
		$CstNo		=	stripSlash($processorRec[10]);
		$PanNo		=	stripSlash($processorRec[11]);
		$processorStatus = 	$processorRec[12];
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		$processorId	=	$p["hidPreProcessorId"];
		$Code		=	addSlash($p["preProcessorCode"]);
		$Name		=	addSlash($p["preProcessorName"]);
		$Address	=	addSlash($p["preProcessorAddress"]);
		$Place		=	addSlash($p["preProcessorPlace"]);
		$Pincode	=	addSlash($p["preProcessorPincode"]);
		$TelNo		=	addSlash($p["preProcessorTelNo"]);
		$FaxNo		=	addSlash($p["preProcessorFaxNo"]);
		$Email		=	addSlash($p["preProcessorEmail"]);
		$LstNo		=	addSlash($p["preProcessorLstNo"]);
		$CstNo		=	addSlash($p["preProcessorCstNo"]);
		$PanNo		=	addSlash($p["preProcessorPanNo"]);
		
		$selPlant	=	$p["selPlant"];
		$selActivity	=	$p["selActivity"];
		$processorStatus = $p["processorStatus"];

		if ($processorId!="" && $Name!="" && $Code!="") {
			$processorRecUptd	=	$preprocessorObj->updateProcessor($processorId, $Code, $Name, $Address,  $Place,$Pincode, $TelNo, $FaxNo, $Email, $LstNo, $CstNo, $PanNo, $selPlant, $selActivity, $processorStatus);		
		}
	
		if ($processorRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProcessorUpdate);
			$sessObj->createSession("nextPage", $url_afterUpdateProcessor.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProcessorUpdate;
		}
		$processorRecUptd	=	false;
	}

	# Delete Pre-Processor
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processorId	=	$p["delId_".$i];
			if ($processorId!="") {
				# Checking the selected Processor is link with any other process 
				$preProcessorRecInUse = $preprocessorObj->preProcessorRecInUse($processorId);
				
				if (!$preProcessorRecInUse) {
					#Delete all Entries regarding the Processor Id				
					$processorPlantRecDel = $preprocessorObj->deleteProcessor2Plant($processorId);
					$processorActivityRecDel = $preprocessorObj->deleteProcessor2Activity($processorId);		
					$processorRecDel = $preprocessorObj->deleteProcessor($processorId);	
				}
				
			}
		}
		if ($processorRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProcessor);
			$sessObj->createSession("nextPage",$url_afterDelProcessor.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		$processorRecDel	=	false;
	}





if ($p["btnConfirm"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processorId	=	$p["confirmId"];
			if ($processorId!="") {
				# Checking the selected Processor is link with any other process 
				$preProcessorRecConfirm = $preprocessorObj->updatepreProcessorconfirm($processorId);

					}
		}
		if ($preProcessorRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmProcessor);
			$sessObj->createSession("nextPage",$url_afterDelProcessor.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		$processorRecDel	=	false;
	}

		
				
		if ($p["btnRlConfirm"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processorId	=	$p["confirmId"];
			if ($processorId!="") {
				# Checking the selected Processor is link with any other process 
				$preProcessorRecReConfirm= $preprocessorObj->updatepreProcessorReleaseconfirm($processorId);

					}
		}
		if ($preProcessorRecReConfirm) {
			$sessObj->createSession("displayMsg",$msg_succReConfirmProcessor);
			$sessObj->createSession("nextPage",$url_afterDelProcessor.$selection);
		} else {
			$errConfirm	=	$msg_failRlConfirm;
		}
		$processorRecDel	=	false;
	}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Processors
	$preProcessorRecords	= $preprocessorObj->fetchPagingRecords($offset, $limit,$confirm);
	$preProcessorSize	= sizeof($preProcessorRecords);
	
	## -------------- Pagination Settings II -------------------	
	$numrows	= sizeof($preprocessorObj->fetchAllRecords($confirm));
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode==true) {
		# Plant Records
		$plantRecords	=	$plantandunitObj->fetchAllRecordsPlantsActive();
		//$processingActivityRecords  =	$processingactivityObj->fetchAllRecords();
		$processingActivityRecords  =	$processingactivityObj->fetchAllActiveRecords();
	} else if($editMode) {
		$plantRecords		    =	$preprocessorObj->fetchSelectedPlantRecords($editId);
		$processingActivityRecords  =	$preprocessorObj->fetchSelectedActivityRecords($editId);
	}

	if ($editMode)	$heading	=	$label_editPreProcessor;
	else 		$heading	=	$label_addPreProcessor;
	
	$help_lnk="help/hlp_PreProcessors.html";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS	= "libjs/preprocessor.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmPreProcessor" action="PreProcessors.php" method="post">
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
								$bxHeader="PRE-PROCESSORS MASTER";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;PRE-PROCESSORS MASTER</td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="65%" align="center">
	<?php
		if ($editMode || $addMode) {
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
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
							</tr>-->
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="80%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>
											<td colspan="2" align="center">
											<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessors.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePreProcessor(document.frmPreProcessor);">
											</td>
											
											<?} else{?>

											<td  colspan="2" align="center">
											<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessors.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddPreProcessor" class="button" value=" Add " onClick="return validatePreProcessor(document.frmPreProcessor);">											</td>

											<?}?>
										</tr>
										<input type="hidden" name="hidPreProcessorId" value="<?=$Id;?>">
										<tr>
										  <td colspan="4" nowrap height="10"></td>
								  </tr>
										<tr>
	<tr>
	<TD colspan="2" valign="top">		
		<table cellpadding="4" cellspacing="0" width="100%">
		<TR>
		<TD valign="top">
		<!--<fieldset>-->
		<?php			
			$entryHead = "";
			require("template/rbTop.php");
		?>
		<table style="padding-top:5px;padding-bottom:5px;">
		<tr>
			<td class="fieldName" nowrap >*Code</td>
			<td><INPUT TYPE="text" NAME="preProcessorCode" size="15" value="<?=$Code;?>"></td>
		</tr>
		<tr>
											<td class="fieldName" nowrap >*Name</td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorName" size="25"  maxlength="25" value="<?=$Name;?>">											</td>
										</tr>
		
										<tr>
											<td class="fieldName" nowrap >Address</td>
											<td ><textarea name="preProcessorAddress" cols="27" rows="4"><?=$Address;?></textarea></td>
										</tr>
										
										<tr>
											<td class="fieldName" nowrap >Place</td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorPlace" size="30" value="<?=$Place;?>">											</td>
										</tr>
										<tr>
										<td class="fieldName" nowrap >Pin Code </td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorPincode" size="10" value="<?=$Pincode;?>">											</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >*Tel.No</td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorTelNo" size="30" value="<?=$TelNo;?>">											</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >Fax No</td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorFaxNo" size="30" value="<?=$FaxNo;?>">											</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >Email</td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorEmail" size="30" maxlength="30" value="<?=$Email;?>">
											</td>
										</tr>
		<tr>
			<td class="fieldName" nowrap >Active</td>
			<td>
				<select name="processorStatus">
					<option value="Y" <?=($processorStatus=='Y')?"selected":""?>>Yes</option>
					<option value="N" <?=($processorStatus=='N')?"selected":""?>>No</option>
				</select>
			</td>
		</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		<!--</fieldset>-->
		</TD>
		<td>&nbsp;</td>
		<td valign="top">
		<!--<fieldset>-->
		<?php			
			$entryHead = "";
			require("template/rbTop.php");
		?>
		<table style="padding-top:5px;padding-bottom:5px;">
										<tr>
											<td class="fieldName" nowrap >LST No</td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorLstNo" size="30" value="<?=$LstNo;?>">											</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >CST No </td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorCstNo" size="10" value="<?=$CstNo;?>">											</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >PAN No</td>
											<td >
											<INPUT TYPE="text" NAME="preProcessorPanNo" size="30" value="<?=$PanNo;?>">											</td>
										</tr>		
		<TR>
			<td class="fieldName">*Plants/Units</td>
			<td>
			<select name="selPlant[]" size="7" multiple id="selPlant">
                        <option value="" > Select Unit </option>
                        <?
			if (sizeof($plantRecords)> 0) {
				foreach ($plantRecords as $pr) {
						$plantId		=	$pr[0];
						$plantName		=	$pr[2];
						$selectedPlantId	=	$pr[5];
						$selected		=	"";
						if ($selectedPlantId== $plantId ) {
							$selected	=	" selected ";
						}			
			?>
                        <option value="<?=$plantId;?>" <?=$selected;?>><?=$plantName;?></option>
                        <?
				  	}
			}
			?>
                        </select>
			</td>
			</TR>
			<TR>
			<td class="fieldName" nowrap>*Activities</td>
			<td>
			<select name="selActivity[]" size="7" multiple id="selActivity">
                        <option value="" > Select Activity </option>
                        <?
			if (sizeof($processingActivityRecords)> 0) {
				foreach ($processingActivityRecords as $pa ) {
						$processingActivityId	=	$pa[0];
						$processingActivity	=	$pa[1];
						$recordActivityId	=	$pa[4];	
						
						$selected		=	"";
						if ($recordActivityId== $processingActivityId) {
							$selected	=	" selected ";
						}
			?>
                        <option value="<?=$processingActivityId;?>" <?=$selected;?>><?=$processingActivity;?></option>
                        <?
				  	}
			}
			?>
                        </select>
			</td>
			</TR>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		<!--</fieldset>-->
		</td>
			</TR>
		</table>
	</TD>
</tr>
		<tr>
			<td colspan="5"  height="10" ></td>
		</tr>
		<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessors.php');">&nbsp;&nbsp;
		<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePreProcessor(document.frmPreProcessor);">					</td>
		<?} else{?>
		<td  colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessors.php');">&nbsp;&nbsp;
		<input type="submit" name="cmdAddPreProcessor" class="button" value=" Add " onClick="return validatePreProcessor(document.frmPreProcessor);">					</td>

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
		
		# Listing PreProcessors Starts
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
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$preProcessorSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPreProcessor.php',700,600);"><? }?>	
											</td>
											</tr>
										</table>
									</td>
								</tr>
	
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
							<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($preProcessorRecords) > 0 )
												{
													$i	=	0;
											?>
							<thead>
											<? if($maxpage>1){?>
											<tr>
			<td colspan="8" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"PreProcessors.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"PreProcessors.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"PreProcessors.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr><? }?>
	  <tr align="center">
	  <th width="20">
	  <INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
	<th nowrap style="padding-left:10px; padding-right:10px;">Processor Code</th>
	<th nowrap style="padding-left:10px; padding-right:10px;">Processor Name</th>
	<th nowrap style="padding-left:10px; padding-right:10px;">Plants/Units</th>
	<th nowrap style="padding-left:10px; padding-right:10px;">Activities</th>
	<th nowrap style="padding-left:10px; padding-right:10px;">Active/Inactive</th>
	<? if($edit==true){?>
		<th width="50">&nbsp;</th>
	<? }?>
	<? if($confirm==true){?>
		<th width="50">&nbsp;</th>
	<? }?>

	</tr>	
	</thead>
	<tbody>
	<?php
	foreach($preProcessorRecords as $fr) {
		$i++;
		$processorId	 = $fr[0];
		$processorName	 = stripSlash($fr[1]);
		$processorCode	 = stripSlash($fr[2]);
		$processorStatus = $fr[3];
		$plantUnitRecords =  $preprocessorObj->fetchPlantRecords($processorId);
		$activityRecords  =  $preprocessorObj->fetchActivityRecords($processorId);
		$active=$fr[4];
		$existingcount=$fr[5];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> >
	<td width="20" align="center">
<?php 
if ($existingcount==0) {?>		
 <input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$processorId;?>" class="chkBox">
<?php 
}
?>

		</td>
	<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$processorCode;?></td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$processorName;?></td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
	<?
		$plantRecDisplayRow	=	3;
		if (sizeof($plantUnitRecords)>0) {
			$plantNext	=	0;			
			foreach($plantUnitRecords as $plantR) {
				$plant	=	$plantR[5];	
				$plantNext++;
				if($plantNext>1) echo "&nbsp;,&nbsp;"; echo $plant;
				if ($plantNext%$plantRecDisplayRow == 0) echo "<br/>";
			}
		}
		?>
	</td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
	<?php
		$activityRecDisplayRow	=	2;
		if (sizeof($activityRecords)>0) {
			$activityNext	=	0;
			foreach($activityRecords as $activityR) {
				$activity	=	$activityR[4];	
				$activityNext++;
				if ($activityNext>1) echo "&nbsp;,&nbsp;"; echo $activity;
				if ($activityNext%$activityRecDisplayRow == 0) echo "<br/>";
			}
		}	
	?>
	</td>
	<td align="center" id="statusRow_<?=$i?>">
	<a href="###" class="link5">
		<? if($processorStatus=='Y'){?>
			<img src="images/y.png" border="0" onMouseover="ShowTip('Click here to Inactive');" onMouseout="UnTip();" onclick="return validateUptdStatus('<?=$processorId?>','<?=$i?>');"/>
		<? } else { ?>
			<img src="images/x.png" border="0" onMouseover="ShowTip('Click here to activate');" onMouseout="UnTip();" onclick="return validateUptdStatus('<?=$processorId?>','<?=$i?>');"/>
		<? }?>
	</a>
	</td>	
	<? if($edit==true){?>
	<td class="listing-item" width="50" align="center">
	 <?php if ($active!=1) {?>
	<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$processorId;?>,'editId'); this.form.action='PreProcessors.php';">
	<? } ?>
	</td>
	 <? }?>



	 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm"  onClick="assignValue(this.form,<?=$processorId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$processorId;?>,'confirmId');" >
			<?php 
			//} 
			
			} }
			?>
			
			
			
			
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
			<td colspan="8" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"PreProcessors.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"PreProcessors.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"PreProcessors.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr>
		<? }?>
	</tbody>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$preProcessorSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPreProcessor.php',700,600);"><? }?></td>
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
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>