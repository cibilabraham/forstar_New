<?php
	require("include/include.php");
	require_once("lib/MCPkgWtMaster_ajax.php");

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
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
	

	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add New Start 
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}
	
	#Add a Record
	if ($p["cmdAdd"]!="") {
		$selMcPkg	= $p["selMcPkg"];
		$packingWt	= $p["packingWt"];
		$selNetWt	= $p["selNetWt"];
		$pkgName	= $p["pkgName"];	
		$pkgWtTolerance = trim($p["pkgWtTolerance"]);	
		
		if ($selMcPkg!="") {						
			$mcPkgWtRecIns = $mcPkgWtMasterObj->addMCPkgWt($selMcPkg, $packingWt, $userId, $selNetWt, $pkgName, $pkgWtTolerance);
						
			if ($mcPkgWtRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddMCPkgWtMaster);
				$sessObj->createSession("nextPage",$url_afterAddMCPkgWtMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddMCPkgWtMaster;
			}
			$mcPkgWtRecIns = false;
		} else {
			$addMode = true;			
			$err = $msg_failAddMCPkgWtMaster;
		}
	}

	# Update a Record
	if ($p["cmdSaveChange"]!="") {
		$mcPkgWtEntryId	= $p["hidMCPkgWtEntryId"];		
		$selMcPkg	= $p["selMcPkg"];
		$packingWt	= $p["packingWt"];
		$selNetWt	= $p["selNetWt"];
		$pkgName	= $p["pkgName"];
		$pkgWtTolerance = trim($p["pkgWtTolerance"]);

		if ($mcPkgWtEntryId!="" && $selMcPkg!="" ) {			
			$mcPkgWtRecUptd = $mcPkgWtMasterObj->updateMCPkgWt($mcPkgWtEntryId, $selMcPkg, $packingWt, $selNetWt, $pkgName, $pkgWtTolerance);		
		}
	
		if ($mcPkgWtRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succMCPkgWtMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateMCPkgWtMaster.$selection);
		} else {
			$editMode	=	true;
			$err = $msg_failMCPkgWtMasterUpdate;
		}
		$mcPkgWtRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$mcPkgWtRec	= $mcPkgWtMasterObj->find($editId);
		$editMCPkgWtEntryId 	= $mcPkgWtRec[0];	
		$selMcPkg		= $mcPkgWtRec[1];	
		$packingWt		= $mcPkgWtRec[2];
		$selNetWt		= $mcPkgWtRec[3];
		$pkgName		= $mcPkgWtRec[4];
		$pkgWtTolerance 	= $mcPkgWtRec[5];
		
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$mcPkgWtEntryId	= $p["delId_".$i];

			if ($mcPkgWtEntryId!="") {
				$mcPkgWtRec	= $mcPkgWtMasterObj->find($mcPkgWtEntryId);
				$selMcPkg	= $mcPkgWtRec[1];
				$mcPackRecExist = $mcPkgWtMasterObj->checkMCPkgSOExist($selMcPkg);
				if (!$mcPackRecExist) {					
					// Need to check the selected id is link with any other process
					$mcPkgWtRecDel = $mcPkgWtMasterObj->deleteMCPkgWtRec($mcPkgWtEntryId);
				}
			}
		}
		if ($mcPkgWtRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelMCPkgWtMaster);
			$sessObj->createSession("nextPage",$url_afterDelMCPkgWtMaster.$selection);
		} else {
			$errDel	=	$msg_failDelMCPkgWtMaster;
		}
		$mcPkgWtRecDel	=	false;
	}	


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$mcPkgWtEntryId	=	$p["confirmId"];
			if ($mcPkgWtEntryId!="") {
				// Checking the selected fish is link with any other process
				$mcPkgWtEntryRecConfirm = $mcPkgWtMasterObj->updatemcPkgWtconfirm($mcPkgWtEntryId);
			}

		}
		if ($mcPkgWtEntryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmmcPkgWtEntry);
			$sessObj->createSession("nextPage",$url_afterDelMCPkgWtMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$mcPkgWtEntryId = $p["confirmId"];
			if ($mcPkgWtEntryId!="") {
				#Check any entries exist				
					$mcPkgWtEntryRecConfirm = $mcPkgWtMasterObj->updatemcPkgWtReleaseconfirm($mcPkgWtEntryId);				
			}
		}
		if ($mcPkgWtEntryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmmcPkgWtEntry);
			$sessObj->createSession("nextPage",$url_afterDelMCPkgWtMaster.$selection);
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

	# List all MC Pkg Wt Master
	$mcPkgWtRecords 	= $mcPkgWtMasterObj->fetchAllPagingRecords($offset, $limit);
	$mcPkgWtRecordSize 	= sizeof($mcPkgWtRecords);

	## -------------- Pagination Settings II -------------------
	$fetchAllMCPkgWtRecords = $mcPkgWtMasterObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllMCPkgWtRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode || $editMode) {	
		# List all MC Packing Records
	//	$mcpackingRecords	= $mcpackingObj->fetchAllRecords();
		$mcpackingRecords	= $mcpackingObj->fetchAllRecordsActivemcpacking();
		# Get distinct Net Wt
		$productNetWtRecs	= $manageProductObj->getAllProductNetWt();
	}
	

	if ($addMode)		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	= $label_editMCPkgWtMaster;
	else	       $heading	= $label_addMCPkgWtMaster;

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/MCPkgWtMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmMCPkgWtMaster" action="MCPkgWtMaster.php" method="post">
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
					$bxHeader = "MC Packing Wt Master";
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('MCPkgWtMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateMCPkgWtMaster(document.frmMCPkgWtMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MCPkgWtMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateMCPkgWtMaster(document.frmMCPkgWtMaster);">												</td>
			<?}?>
		</tr>
	<input type="hidden" name="hidMCPkgWtEntryId" value="<?=$editMCPkgWtEntryId;?>">
	<tr><TD height="10"></TD></tr>
<!-- Msg display row -->	
	<tr>
		<td colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;">
			<div id="displayExistingRec" style="display:none;"><!-- xajax values --></div>
		</td>
	</tr>
	<tr>
		<TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;" align="center">
			<span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"><!-- xajax values --></span>
		</TD>
	</tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;" align="center">
			<table>
			<tr>
				<td class="fieldName" nowrap >*Name</td>
				<td nowrap="true">
					<input type="text" name="pkgName" size="20" value="<?=$pkgName;?>" />
					<input type="hidden" name="existPkgName" id="existPkgName" value="" size="6" style="text-align:right;" autocomplete="off" readonly>
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap >*Net Wt</td>
				<td nowrap="true">
					<select name="selNetWt" id="selNetWt" onchange="xajax_chkSelRecExist(document.getElementById('selMcPkg').value,'<?=$mode?>','<?=$editMCPkgWtEntryId?>', document.getElementById('selNetWt').value)">
						<option value="">-- Select --</option>
						<?php
						if (sizeof($productNetWtRecs)>0) {	
							foreach($productNetWtRecs as $pnr) {
								$productNetWt	= $pnr[0];				
								$selected	= ($selNetWt==$productNetWt)?"selected":"";
						?>	
						<option value="<?=$productNetWt?>" <?=$selected?>><?=$productNetWt?></option>
						<?
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap >*MC Packing</td>
				<td nowrap="true">				
					<select name="selMcPkg" id="selMcPkg" onchange="xajax_chkSelRecExist(document.getElementById('selMcPkg').value,'<?=$mode?>','<?=$editMCPkgWtEntryId?>', document.getElementById('selNetWt').value)">
					<option value="">--Select--</option>
					<?php
					if (sizeof($mcpackingRecords)>0) {	
						foreach($mcpackingRecords as $mcp) {
							$mcpackingId	= $mcp[0];
							$mcpackingCode	= stripSlash($mcp[1]);
							$selected	= ($selMcPkg==$mcpackingId)?"selected":"";
					?>	
					<option value="<?=$mcpackingId?>" <?=$selected?>><?=$mcpackingCode?></option>
					<?
						}
					}
					?>
					</select>
				</td>
			</tr>	
			<tr>
				<td class="fieldName" nowrap >*Packing Weight</td>			
				<td class="listing-item" nowrap="true">
					<input type="text" name="packingWt" id="packingWt" value="<?=$packingWt?>" size="6" style="text-align:right;" autocomplete="off">&nbsp;Kg
					<input type="hidden" name="existPkgWt" id="existPkgWt" value="" size="6" style="text-align:right;" autocomplete="off" readonly />
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>Pkg Wt Tolerance</td>			
				<td class="listing-item" nowrap>
					<strong>+/-</strong>&nbsp;
					<input type="text" name="pkgWtTolerance" id="pkgWtTolerance" value="<?=$pkgWtTolerance?>" size="4" style="text-align:right;" autocomplete="off">&nbsp;Gms
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MCPkgWtMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateMCPkgWtMaster(document.frmMCPkgWtMaster);">												</td>
			<?} else{?>
				<td  colspan="2" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MCPkgWtMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateMCPkgWtMaster(document.frmMCPkgWtMaster);">												</td>
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;MC Packing Wt Master</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$mcPkgWtRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintMCPkgWtMaster.php',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px;pading-right:10px;">
	<table cellpadding="2"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if (sizeof($mcPkgWtRecords)) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"MCPkgWtMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MCPkgWtMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"MCPkgWtMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Name</th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Net Wt<br/>(Gm)</th>		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">MC<br> Pack</th>		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Package Wt<br/>(Kg)</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Pkg Wt <br/>Tolerance<br> [+/-] (Gms)</th>
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
			foreach ($mcPkgWtRecords as $mpwr) {	
				$i++;
				$mcPkgWtEntryId = $mpwr[0];
				$mcPackingCode	= $mpwr[3];	
				$packageWt	= $mpwr[2];
				$netWtUnit	= $mpwr[4];
				$name		= $mpwr[5];
				$mcPkgWtTolerance = $mpwr[6];
				$active=$mpwr[7];
			?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$mcPkgWtEntryId;?>" class="chkBox">			
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$name;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$netWtUnit;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$mcPackingCode;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$packageWt;?></td>	
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=($mcPkgWtTolerance!=0)?$mcPkgWtTolerance:"";?></td>		
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$mcPkgWtEntryId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='MCPkgWtMaster.php';" ><? } ?></td>
<? }?>

 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$mcPkgWtEntryId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$mcPkgWtEntryId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		</tr>
		<?			
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"MCPkgWtMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MCPkgWtMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"MCPkgWtMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
			<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$mcPkgWtRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintMCPkgWtMaster.php',700,600);"><? }?></td>
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
<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">		
		<tr>
			<td height="10"></td>
		</tr>		
	</table>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>