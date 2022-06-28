<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"]."&salesZoneFilter=".$p["salesZoneFilter"];
	
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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;

	#Add a Record
	if ($p["cmdAdd"]!="") {
		$stateCode	= "STATE_".autoGenNum(); // calling from config file
		$stateName	= addSlash(trim($p["stateName"]));		
		$billingState   = ($p["billingState"]=="")?N:$p["billingState"]; //y/n
		$entryTax	= ($p["entryTax"]=="")?N:$p["entryTax"]; //y/n
		$salesZone	= $p["salesZone"];

		if ($stateCode!="" &&  $stateName!="") {
			$stateRecIns = $stateMasterObj->addState($stateCode, $stateName, $billingState, $entryTax, $salesZone);
			if ($stateRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddState);
				$sessObj->createSession("nextPage",$url_afterAddState.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddState;
			}
			$stateRecIns = false;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		$stateId	=	$p["hidStateId"];		
		$stateName	=	addSlash(trim($p["stateName"]));		
		$billingState   = ($p["billingState"]=="")?N:$p["billingState"]; //y/n
		$entryTax	= ($p["entryTax"]=="")?N:$p["entryTax"]; //y/n
		$salesZone	= $p["salesZone"];		

		if ($stateId!="" && $stateName!="") {
			$stateRecUptd = $stateMasterObj->updateState($stateId, $stateName, $billingState, $entryTax, $salesZone);
		}
	
		if ($stateRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStateUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateState.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStateUpdate;
		}
		$stateRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$stateRec	= $stateMasterObj->find($editId);
		$editStateId 	= $stateRec[0];
		$stateCode	= stripSlash($stateRec[1]);
		$stateName	= stripSlash($stateRec[2]);
		$billState	= $stateRec[3];	
		if ($billState=='Y') $billingState = "Checked";
		else $billingState = "";
		$eTax	= $stateRec[4];	
		if ($eTax=='Y') $entryTax = "Checked";
		else $entryTax = "";
		$salesZone	= $stateRec[5];
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stateId	= $p["delId_".$i];			
			if ($stateId!="") {
				$stateEntryExist = $stateMasterObj->stateEntryExist($stateId);
				// Need to check the selected State is link with any other process
				if (!$stateEntryExist) {					
					$stateRecDel = $stateMasterObj->deleteState($stateId);
				}
			}
		}
		if ($stateRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelState);
			$sessObj->createSession("nextPage",$url_afterDelState.$selection);
		} else {
			if ($stateEntryExist) $errDel = $msg_failDelState."<br>The state you have selected is already in use.";
			else $errDel = $msg_failDelState;
		}
		$stateRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stateId	=	$p["confirmId"];
			if ($stateId!="") {
				// Checking the selected fish is link with any other process
				$stateRecConfirm = $stateMasterObj->updateStateconfirm($stateId);
			}
		}
		if ($stateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmstate);
			$sessObj->createSession("nextPage",$url_afterDelState.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$stateId = $p["confirmId"];
			if ($stateId!="") {
				#Check any entries exist				
					$stateRecConfirm = $stateMasterObj->updateStateReleaseconfirm($stateId);
				
			}
		}
		if ($stateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmstate);
			$sessObj->createSession("nextPage",$url_afterDelState.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["salesZoneFilter"]!="") $salesZoneFilter = $g["salesZoneFilter"];
	else $salesZoneFilter = $p["salesZoneFilter"];

	if ($p["salesZoneFilter"]!=$p["hidSalesZoneFilter"]) {
		$offset	= 0;
	}

	# List all State
	$stateResultSetObj = $stateMasterObj->fetchAllPagingRecords($offset, $limit, $salesZoneFilter);
	$stateRecordSize = $stateResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allStateResultSetObj = $stateMasterObj->fetchAllRecords($salesZoneFilter);
	$numrows	=  $allStateResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		

	# Sales Zone Records
	//$salesZoneRecords = $salesZoneObj->fetchAllRecords();
	$salesZoneRecords = $salesZoneObj->fetchAllRecordsActiveZone();

	

	#heading Section
	if ($editMode) $heading	=	$label_editState;
	else	       $heading	=	$label_addState;

	$ON_LOAD_PRINT_JS	= "libjs/StateMaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmStateMaster" action="StateMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >	
	<tr><td height="10" align="center"><a href="SalesZoneMaster.php" class="link1" title="Click to manage Sales Zone">Sales Zone Master</a></td></tr>
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
								$bxHeader="State Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
<tr>
		<td colspan="3" align="center">
		<table width="50%" align="center">
		<?
			if ( $editMode || $addMode) {
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('StateMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateStateMaster(document.frmStateMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStateMaster(document.frmStateMaster);">												</td>

												<?}?>
											</tr>
					<input type="hidden" name="hidStateId" value="<?=$editStateId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
		<tr>
		<td colspan="2" nowrap align="center">
						<table width="200">		
		<tr>
	  		<td class="fieldName" nowrap >*Name</td>
			<td>
				<input type="text" name="stateName" size="20" value="<?=$stateName;?>" />
			</td>
		</tr>
		<tr>
	  		<td class="fieldName" nowrap >*Zone</td>
			<td nowrap="true">
				<select name="salesZone" id="salesZone">
					<option value="">--Select--</option>
					<?php
					 foreach ($salesZoneRecords as $szr) {
						$salesZoneId 	= $szr[0];
						$salesZoneName	= $szr[2];
						$selected = ($salesZoneId==$salesZone)?"selected":"";
					?>
					<option value="<?=$salesZoneId?>" <?=$selected?>><?=$salesZoneName?></option>
					<?php
						 }
					?>
				</select>				
			</td>
		</tr>		
	 	<tr>
                	<td class="fieldName" nowrap="nowrap">Billing State </td>
                        <td nowrap="true">
				  <input name="billingState" type="checkbox" id="billingState" value="Y" <?=$billingState?> class="chkBox"> &nbsp;&nbsp;
				<span style="vertical-align:middle; line-height:normal" class="fieldName"><font size="1">(If Yes, please give tick mark)</font></span>
			</td>
                </tr>
		<tr>
                	<td class="fieldName" nowrap="nowrap">Entry Tax</td>
                        <td nowrap="true">
				  <input name="entryTax" type="checkbox" id="entryTax" value="Y" <?=$entryTax?> class="chkBox"> &nbsp;&nbsp;
				<span style="vertical-align:middle; line-height:normal" class="fieldName"><font size="1">(If Yes, please give tick mark)</font></span>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStateMaster(document.frmStateMaster);">	
											</td>
											<?} else{?>
										<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStateMaster(document.frmStateMaster);">	
											</td>
												<input type="hidden" name="cmdAddNew" value="1">
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
				<!-- Form fields end   -->		
			</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
		?>
		</table>
		</td>
	</tr>
	<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="15" ></td>
	</tr>
	<?php
		}
	?>	
<tr>
	<td colspan="3" align="center">
		<table width="20%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">	
				<tr>
					<td align="right" nowrap class="listing-item">Zone</td>
				<td align="right" nowrap valign="top">
				<select name="salesZoneFilter" onChange="this.form.submit();">
				 <option value="">--Select All--</option>					
					<?php
					 foreach ($salesZoneRecords as $szr) {
						$salesZoneId 	= $szr[0];
						$salesZoneName	= $szr[2];
						$selected = ($salesZoneId==$salesZoneFilter)?"selected":"";
					?>
					<option value="<?=$salesZoneId?>" <?=$selected?>><?=$salesZoneName?></option>
					<?php
						 }
					?>
				  </select>
				</td>				
				</tr>
			  </table>
			<?php
				require("template/rbBottom.php");
			?>
		</td>
		</tr>
		</table>	
	</td>
</tr>	
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;State Master</td>
	<td background="images/heading_bg.gif" class="pageName" align="right" nowrap valign="top" style="background-repeat: repeat-x">
			<table align="right" cellpadding="0" cellspacing="0">	
			<tr>
				
				<td width="4">&nbsp;</td>
				</tr>
										
								  </table></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStateMaster.php?salesZoneFilter=<?=$salesZoneFilter?>',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px;pading-right:10px;">
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($stateRecordSize) {
			$i	=	0;
		?>
	<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StateMaster.php?pageNo=$page&salesZoneFilter=$salesZoneFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StateMaster.php?pageNo=$page&salesZoneFilter=$salesZoneFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StateMaster.php?pageNo=$page&salesZoneFilter=$salesZoneFilter\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th style="padding-left:10px; padding-right:10px;">Name</th>		
		<th style="padding-left:10px; padding-right:10px;">Zone</th>
		<th style="padding-left:10px; padding-right:10px;">Billing</th>
		<th style="padding-left:10px; padding-right:10px;">Entry Tax</th>	
		<? if($edit==true){?>
		<th>&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th>&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
			<?php
			while ($sr=$stateResultSetObj->getRow()) {
				$i++;
				$stateId = $sr[0];
				$stateCode	= stripSlash($sr[1]);
				$stateName	= stripSlash($sr[2]);
				$billing	= ($sr[3]=='Y')?"YES":"NO";
				$sEntryTax	= ($sr[4]=='Y')?"YES":"NO";
				$sZoneName	= stripSlash($sr[6]);
				$active=$sr[7];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" <?php }?>>
		<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stateId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sZoneName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$billing;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$sEntryTax;?></td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		 <?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stateId;?>,'editId');this.form.action='StateMaster.php';" >
		<? } ?>
		</td>
<? }?>
 <? if ($confirm==true){?>
 
			<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stateId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$stateId;?>,'confirmId');" >
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
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StateMaster.php?pageNo=$page&salesZoneFilter=$salesZoneFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StateMaster.php?pageNo=$page&salesZoneFilter=$salesZoneFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StateMaster.php?pageNo=$page&salesZoneFilter=$salesZoneFilter\"  class=\"link1\">>></a> ";
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
												}
												else
												{
											?>
											<tr>
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStateMaster.php?salesZoneFilter=<?=$salesZoneFilter?>',700,600);"><? }?></td>
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
		<input type="hidden" name="hidSalesZoneFilter" value="<?=$salesZoneFilter?>">
		<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	</table>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
