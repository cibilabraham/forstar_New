<?php
	require("include/include.php");
	require_once("lib/PackingGroupMaster_ajax.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
	$selection 	= "?pageNo=".$p["pageNo"];	
	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
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
	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	
	#Add a Record
	if ($p["cmdAdd"]!="") {
		$selPState_L	= $p["selPState_L"];
		$selPGroup_L	= $p["selPGroup_L"];
		$selNetWt_L	= $p["selNetWt_L"];
		$selPState_R	= $p["selPState_R"];
		$selPGroup_R	= $p["selPGroup_R"];
		$selNetWt_R	= $p["selNetWt_R"];
		$pSelLeft	= $selPState_L.",".$selPGroup_L.",".$selNetWt_L; // State,group, net wt
		$pSelRight	= $selPState_R.",".$selPGroup_R.",".$selNetWt_R; // State,group, net wt

		if ($pSelLeft!="" && $pSelRight!="") {						
			$packingGroupRecIns = $packingGroupMasterObj->addPackingGroup($pSelLeft, $pSelRight);
						
			if ($packingGroupRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddPackingGroupMaster);
				$sessObj->createSession("nextPage",$url_afterAddPackingGroupMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddPackingGroupMaster;
			}
			$packingGroupRecIns = false;
		} else {
			$addMode = true;			
			$err = $msg_failAddPackingGroupMaster;
		}
	}

	# Update a Record
	if ($p["cmdSaveChange"]!="") {
		$packingGroupId	= $p["hidMCPkgWtEntryId"];		
		$selPState_L	= $p["selPState_L"];
		$selPGroup_L	= $p["selPGroup_L"];
		$selNetWt_L	= $p["selNetWt_L"];
		$selPState_R	= $p["selPState_R"];
		$selPGroup_R	= $p["selPGroup_R"];
		$selNetWt_R	= $p["selNetWt_R"];
		$pSelLeft	= $selPState_L.",".$selPGroup_L.",".$selNetWt_L; // State,group, net wt
		$pSelRight	= $selPState_R.",".$selPGroup_R.",".$selNetWt_R; // State,group, net wt

		if ($packingGroupId!="" && $pSelLeft!="" && $pSelRight!="" ) {			
			$packingGroupRecUptd = $packingGroupMasterObj->updatePackingGroup($packingGroupId, $pSelLeft, $pSelRight);
		}
	
		if ($packingGroupRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPackingGroupMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingGroupMaster.$selection);
		} else {
			$editMode	=	true;
			$err = $msg_failPackingGroupMasterUpdate;
		}
		$packingGroupRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$packingGroupRec	= $packingGroupMasterObj->find($editId);
		$editPackingGroupEntryId 	= $packingGroupRec[0];	
		$pLSel		= explode(",",$packingGroupRec[1]);
		$selPStateL	= $pLSel[0];
		$selPGroupL	= $pLSel[1];
		$selNetWtL	= $pLSel[2];

		$pRSel		= explode(",",$packingGroupRec[2]);
		$selPStateR	= $pRSel[0];
		$selPGroupR	= $pRSel[1];
		$selNetWtR	= $pRSel[2];
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$packingGroupId	= $p["delId_".$i];

			if ($packingGroupId!="") {
				/*
				//$mcPackRecExist = $packingGroupMasterObj->checkMCPkgSOExist($selMcPkg);
				//if (!$mcPackRecExist) {					
				*/
				// Need to check the selected id is link with any other process
				$packingGroupRecDel = $packingGroupMasterObj->deletePackingGroupRec($packingGroupId);
				//}
			}
		}
		if ($packingGroupRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPackingGroupMaster);
			$sessObj->createSession("nextPage",$url_afterDelPackingGroupMaster.$selection);
		} else {
			$errDel	=	$msg_failDelPackingGroupMaster;
		}
		$packingGroupRecDel	=	false;
	}	


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$packingGroupId		=	$p["confirmId"];


			if ($packingGroupId!="") {
				// Checking the selected fish is link with any other process
				$packingGroupMasterRecConfirm = $packingGroupMasterObj->updatePackingGroupconfirm($packingGroupId);
			}

		}
		if ($packingGroupMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmPackingGroupMaster);
			$sessObj->createSession("nextPage",$url_afterDelPackingGroupMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmFishCategory;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$packingGroupId	 = $p["confirmId"];

			if ($packingGroupId!="") {
				#Check any entries exist
				
					$packingGroupMasterRecConfirm = $packingGroupMasterObj->updatePackingGroupReleaseconfirm($packingGroupId);
				
			}
		}
		if ($packingGroupMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmPackingGroupMaster);
			$sessObj->createSession("nextPage",$url_afterDelPackingGroupMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmFishCategory;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all MC Pkg Wt Master
	$packingGroupRecords 	= $packingGroupMasterObj->fetchAllPagingRecords($offset, $limit);
	$packingGroupRecordSize 	= sizeof($packingGroupRecords);

	## -------------- Pagination Settings II -------------------
	$fetchAllMCPkgWtRecords = $packingGroupMasterObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllMCPkgWtRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode || $editMode) {	
		# List all MC Packing Records
		//$mcpackingRecords	= $mcpackingObj->fetchAllRecords();
		$mcpackingRecords	= $mcpackingObj->fetchAllRecordsActivemcpacking();
		# Get distinct Net Wt
		//$productNetWtRecs	= $manageProductObj->getAllProductNetWt();
	
		# ------
		# List all Product State Records
		//$productStateRecords = $productStateObj->fetchAllRecords();
		$productStateRecords = $productStateObj->fetchAllRecordsActiveProduct();
	}
	

	if ($addMode)		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	= $label_editPackingGroupMaster;
	else	       $heading	= $label_addPackingGroupMaster;

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/PackingGroupMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPackingGroupMaster" action="PackingGroupMaster.php" method="post">
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
					$bxHeader = "Packing Group Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="40%">	
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('PackingGroupMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validatePackingGroupMaster(document.frmPackingGroupMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingGroupMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validatePackingGroupMaster(document.frmPackingGroupMaster);">												</td>
			<?}?>
		</tr>
	<input type="hidden" name="hidMCPkgWtEntryId" value="<?=$editPackingGroupEntryId;?>" />
	<tr><TD height="10"></TD></tr>
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr>
	<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
	<table width="200">
	<tr>
		<TD>
			<table>
			<TR>
				<TD>
				<!--<fieldset>
				<legend class="listing-item">Product</legend>-->
				<?php
					$entryHead = "Product";
					$rbTopWidth = "";
					require("template/rbTop.php");
				?>
				<table>
				<TR>
					<TD class="fieldName" nowrap="true">*State</TD>
					<td>
						<select name="selPState_L" id="selPState_L" onchange="xajax_getProductGroupExist(document.getElementById('selPState_L').value, 'L', '<?=$selId?>');xajax_getNetWtRecs(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, 'L', '<?=$selNetWt?>');xajax_chkSelRecExist(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, document.getElementById('selNetWt_L').value, document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, document.getElementById('selNetWt_R').value, '<?=$mode?>', '<?=$editPackingGroupEntryId?>');">
						<option value="">-- Select --</option>
						<?php
						if (sizeof($productStateRecords)>0) {	
							foreach ($productStateRecords as $cr) {
								$prodStateId	= $cr[0];
								$prodStateName	= stripSlash($cr[1]);
								$selected = "";
								if ($selPStateL==$prodStateId) $selected = "Selected";
						?>
						<option value="<?=$prodStateId?>" <?=$selected?>><?=$prodStateName?></option>
						<?php
							} 
						}
						?>
						</select>
					</td>
				</TR>
				<TR>
					<TD class="fieldName" nowrap="true">*Group</TD>
					<td>
						<select name='selPGroup_L' id='selPGroup_L' onchange="xajax_getNetWtRecs(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, 'L', '<?=$selNetWt?>');xajax_chkSelRecExist(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, document.getElementById('selNetWt_L').value, document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, document.getElementById('selNetWt_R').value, '<?=$mode?>', '<?=$editPackingGroupEntryId?>');">
						<option value='0'>-- Select --</option>
						</select>
						<input type="hidden" name="pGroupExist_L" id="pGroupExist_L"/>
					</td>
				</TR>
				<TR>
					<TD class="fieldName" nowrap="true">*Net Wt</TD>
					<td>
					<select name="selNetWt_L" id="selNetWt_L" onchange="xajax_chkSelRecExist(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, document.getElementById('selNetWt_L').value, document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, document.getElementById('selNetWt_R').value, '<?=$mode?>', '<?=$editPackingGroupEntryId?>');">
					<option value="">-- Select --</option>
					<?php
					if (sizeof($productNetWtRecs)>0) {	
						foreach($productNetWtRecs as $pnr) {
							$productNetWt	= $pnr[0];				
							$selected	= "";
							if($selNetWt==$productNetWt)  $selected	= " selected ";
					?>	
					<option value="<?=$productNetWt?>" <?=$selected?>><?=$productNetWt?></option>
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
			</TD>
			<td class="listing-head"><b>=</b></td>
			<TD>
				<!--<fieldset>
				<legend class="listing-item">Product</legend>-->
				<?php
					$entryHead = "Product";
					$rbTopWidth = "";
					require("template/rbTop.php");
				?>
				<table>
				<TR>
					<TD class="fieldName" nowrap="true">*State</TD>
					<td>
						<select name="selPState_R" id="selPState_R" onchange="xajax_getProductGroupExist(document.getElementById('selPState_R').value, 'R', '<?=$selId?>');xajax_getNetWtRecs(document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, 'R', '<?=$selNetWt?>');xajax_chkSelRecExist(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, document.getElementById('selNetWt_L').value, document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, document.getElementById('selNetWt_R').value, '<?=$mode?>', '<?=$editPackingGroupEntryId?>');">
						<option value="">-- Select --</option>
						<?php
						if (sizeof($productStateRecords)>0) {	
							foreach ($productStateRecords as $cr) {
								$prodStateId	= $cr[0];
								$prodStateName	= stripSlash($cr[1]);
								$selected = "";
								if ($selPStateR==$prodStateId) $selected = "Selected";
						?>
						<option value="<?=$prodStateId?>" <?=$selected?>><?=$prodStateName?></option>
						<?php
							} 
						}
						?>
						</select>
					</td>
				</TR>
				<TR>
					<TD class="fieldName" nowrap="true">*Group</TD>
					<td>
						<select name='selPGroup_R' id='selPGroup_R' onchange="xajax_getNetWtRecs(document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, 'R', '<?=$selNetWt?>');xajax_chkSelRecExist(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, document.getElementById('selNetWt_L').value, document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, document.getElementById('selNetWt_R').value, '<?=$mode?>', '<?=$editPackingGroupEntryId?>');">
						<option value='0'>-- Select --</option>
						</select>
						<input type="hidden" name="pGroupExist_R" id="pGroupExist_R"/>
					</td>
				</TR>
				<TR>
					<TD class="fieldName" nowrap="true">*Net Wt</TD>
					<td>
					<select name="selNetWt_R" id="selNetWt_R" onchange="xajax_chkSelRecExist(document.getElementById('selPState_L').value, document.getElementById('selPGroup_L').value, document.getElementById('selNetWt_L').value, document.getElementById('selPState_R').value, document.getElementById('selPGroup_R').value, document.getElementById('selNetWt_R').value, '<?=$mode?>', '<?=$editPackingGroupEntryId?>');">
					<option value="">-- Select --</option>
					<?php
					if (sizeof($productNetWtRecs)>0) {	
						foreach($productNetWtRecs as $pnr) {
							$productNetWt	= $pnr[0];				
							$selected	= "";
							if($selNetWt==$productNetWt)  $selected	= " selected ";
					?>	
					<option value="<?=$productNetWt?>" <?=$selected?>><?=$productNetWt?></option>
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
			</TD>
			</TR>
		</table>
	</TD>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingGroupMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validatePackingGroupMaster(document.frmPackingGroupMaster);">												</td>
											<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingGroupMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validatePackingGroupMaster(document.frmPackingGroupMaster);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
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
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>	
		</table>
	</td>
	</tr>	
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Group Master</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingGroupRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingGroupMaster.php',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if (sizeof($packingGroupRecords)) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PackingGroupMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingGroupMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingGroupMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Group</th>
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
		<?php			
			foreach ($packingGroupRecords as $pgr) {	
				$i++;
				$packingGroupId = $pgr[0];
				$pLSel		= explode(",",$pgr[1]);
				$pStateRecL	= $productStateObj->find($pLSel[0]);		
				$pStateNameL	= stripSlash($pStateRecL[1]);
				$pGroupRecL	= $productGroupObj->find($pLSel[1]);
				$pGroupNameL	= ($pGroupRecL[1]!="")?stripSlash($pGroupRecL[1]):"No Group";
				$pNetWtL	= $pLSel[2];

				$pRSel		= explode(",",$pgr[2]);
				$pStateRecR	= $productStateObj->find($pRSel[0]);		
				$pStateNameR	= stripSlash($pStateRecR[1]);
				$pGroupRecR	= $productGroupObj->find($pRSel[1]);
				$pGroupNameR	= ($pGroupRecR[1]!="")?stripSlash($pGroupRecR[1]):"No Group";
				$pNetWtR	= $pRSel[2];
				$displayPkgGroup ="";
				$displayPkgGroup = "$pStateNameL,&nbsp;$pGroupNameL,&nbsp;$pNetWtL&nbsp;<b>=</b> &nbsp;$pStateNameR,&nbsp;$pGroupNameR,&nbsp;$pNetWtR";
				$active = $pgr[3];
		?>
	<tr <?php if ($active==0) { ?>  bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> >
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$packingGroupId;?>" class="chkBox">			
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$displayPkgGroup;?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$packingGroupId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='PackingGroupMaster.php';" ><? } ?></td>
		<? }?>
		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$packingGroupId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$packingGroupId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
	<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PackingGroupMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingGroupMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingGroupMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
			<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingGroupRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingGroupMaster.php',700,600);"><? }?></td>
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
<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">		
		<tr>
			<td height="10"></td>
		</tr>		
	</table>	
<?php
	if ($editMode) {	
?>
	<script language="JavaScript" type="text/javascript">
		xajax_getProductGroupExist('<?=$selPStateL?>', 'L', '<?=$selPGroupL?>');
		xajax_getNetWtRecs('<?=$selPStateL?>', '<?=$selPGroupL?>', 'L', '<?=$selNetWtL?>');
		xajax_getProductGroupExist('<?=$selPStateR?>', 'R', '<?=$selPGroupR?>');
		xajax_getNetWtRecs('<?=$selPStateR?>', '<?=$selPGroupR?>', 'R', '<?=$selNetWtR?>');
	</script>
<?php
	}
?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>