<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"]."&selDistributorFilter=".$p["selDistributorFilter"];

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
	if ($p["cmdAddNew"]!="") $addMode  =   true;
	if ($p["cmdCancel"]!="") {
		$addMode   	= false;
		$editMode 	= false;
	}

	if ($p["selDistributor"]!="")	$selDistributor = $p["selDistributor"];
	if ($p["selProductMgn"]!="") 	$selProductMgn	= $p["selProductMgn"];
	if ($p["selOptionValue"]!="") 	$groupedOptionValues	= $p["selOptionValue"];
	
	# Add a Record
	if ($p["cmdAdd"]!="") {	
		$selDistributor = $p["selDistributor"];
		$selProductMgn	= $p["selProductMgn"];  // Margin Id
		$tableRowCount	= $p["hidTableRowCount"];
		$groupedOptionValues	= $p["selOptionValue"];

		# check Report Definition Exist exist
		$distReportDefinitionExist = $distReportDefinitionObj->chkDistReportDefinitionExist($selDistributor, $cId); 

		if ($selDistributor!="" && !$distReportDefinitionExist) {
			# Ins Main Rec
			$distReportDefinitionRecIns = $distReportDefinitionObj->addDistReportDefinition($selDistributor, $selProductMgn, $userId, $groupedOptionValues);
			
			if ($distReportDefinitionRecIns) {
				#Find the Last inserted Id From  Table
				$lastId = $databaseConnect->getLastInsertedId();
				if ($tableRowCount>0) {
					for ($i=0; $i<$tableRowCount; $i++) {
						$mgnStructId	= $p["mgnStructId_".$i];
						$mgnName	= $p["mgnStructDisplayName_".$i];	
						if ($lastId && $mgnStructId!="" && $mgnName!="") {
							$distReportDefinitionEntryRecIns = $distReportDefinitionObj->addDistReportDefinitionEntry ($lastId, $mgnStructId, $mgnName);
						}
					}
				}
			}
			if ($distReportDefinitionRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddDistReportDefinition);
				$sessObj->createSession("nextPage",$url_afterAddDistReportDefinition.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddDistReportDefinition;
			}
			$distReportDefinitionRecIns = false;
		} else {
			$addMode = true;
			if ($distReportDefinitionExist) $err = $msg_failAddDistReportDefinition."<br/>The selected records existing in our database.";
			else $err = $msg_failAddDistReportDefinition;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$distReportDefinitionId = $p["hidDistReportDefinitionId"];
		$selDistributor = $p["selDistributor"];
		$selProductMgn	= $p["selProductMgn"];  // Margin Id
		$groupedOptionValues	= $p["selOptionValue"];

		$tableRowCount	= $p["hidTableRowCount"];

		# check Report Definition Exist 
		$distReportDefinitionExist = $distReportDefinitionObj->chkDistReportDefinitionExist($selDistributor, $distReportDefinitionId); 		

		if ($distReportDefinitionId!="" && $selDistributor!="" && !$distReportDefinitionExist) {
			$distReportDefinitionRecUptd = $distReportDefinitionObj->updateDistReportDefinition($distReportDefinitionId, $selDistributor, $selProductMgn, $groupedOptionValues);

			if ($distReportDefinitionRecUptd) {
				# Delete Entry Recs
				$deleteDistReportDefinitionEntryRecs = $distReportDefinitionObj->deleteDistReportDefinitionEntryRecs($distReportDefinitionId);
				if ($tableRowCount>0) {
					for ($i=0; $i<$tableRowCount; $i++) {
						$mgnStructId	= $p["mgnStructId_".$i];
						$mgnName	= $p["mgnStructDisplayName_".$i];
						if ($distReportDefinitionId && $mgnStructId!="" && $mgnName!="") {
							$distReportDefinitionEntryRecIns = $distReportDefinitionObj->addDistReportDefinitionEntry ($distReportDefinitionId, $mgnStructId, $mgnName);
						}
					}
				} 
			} 
		}	
		if ($distReportDefinitionRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDistReportDefinitionUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDistReportDefinition.$selection);
		} else {
			$editMode	=	true;
			if  ($distReportDefinitionExist) $err = $msg_failDistReportDefinitionUpdate."<br/>The selected records existing in our database.";
			else $err = $msg_failDistReportDefinitionUpdate;
		}
		$distReportDefinitionRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$distReportDefinitionRec = $distReportDefinitionObj->find($editId);
		$editDistReportDefinitionId = $distReportDefinitionRec[0];
		$selDistributor	= $distReportDefinitionRec[1];
		if ($p["selProductMgn"]=="") {
			$selProductMgn = $distReportDefinitionRec[2];
			$groupedOptionValues	= $distReportDefinitionRec[3];
		} else {
			$selProductMgn		=	$p["selProductMgn"];
			$groupedOptionValues	= $p["selOptionValue"];
		}
		
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$distReportDefinitionId	=	$p["delId_".$i];

			if ($distReportDefinitionId!="") {
				// Need to check , is it link with any other process?
				# Delete Entry Recs
				$deleteDistReportDefinitionEntryRecs = $distReportDefinitionObj->deleteDistReportDefinitionEntryRecs($distReportDefinitionId);
				# Delete Main Rec
				$distReportDefinitionRecDel = $distReportDefinitionObj->deleteDistReportDefinition($distReportDefinitionId);
			}
		}
		if ($distReportDefinitionRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDistReportDefinition);
			$sessObj->createSession("nextPage",$url_afterDelDistReportDefinition.$selection);
		} else {
			$errDel	=	$msg_failDelDistReportDefinition;
		}
		$distReportDefinitionRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$distReportDefinitionId	=	$p["confirmId"];


			if ($distReportDefinitionId!="") {
				// Checking the selected fish is link with any other process
				$distReportRecConfirm = $distReportDefinitionObj->updatedistReportconfirm($distReportDefinitionId);
			}

		}
		if ($distReportRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmdistReport);
			$sessObj->createSession("nextPage",$url_afterDelDistReportDefinition.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$distReportDefinitionId = $p["confirmId"];

			if ($distReportDefinitionId!="") {
				#Check any entries exist
				
					$distReportRecConfirm = $distReportDefinitionObj->updatedistReportReleaseconfirm($distReportDefinitionId);
				
			}
		}
		if ($distReportRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmdistReport);
			$sessObj->createSession("nextPage",$url_afterDelDistReportDefinition.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo = 1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["selDistributorFilter"]!="") $selDistributorFilter = $g["selDistributorFilter"];
	else $selDistributorFilter = $p["selDistributorFilter"];

	if ($p["selDistributorFilter"]!=$p["hidSelDistributorFilter"]) {
		$offset	= 0;
	}

	# List all Recs
	$distReportDefinitionRecords = $distReportDefinitionObj->fetchAllPagingRecords($offset, $limit, $selDistributorFilter);
	$distReportDefinitionRecordSize = sizeof($distReportDefinitionRecords);

	## -------------- Pagination Settings II -------------------
	$fetchAllProductIdentifierRecs = $distReportDefinitionObj->fetchAllRecords($selDistributorFilter);
	$numrows	=  sizeof($fetchAllProductIdentifierRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Distributor	
	//$distributorFilterResultSetObj = $distributorMasterObj->fetchAllRecords();
	$distributorFilterResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	# Setting Mode	
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;


	if ($addMode || $editMode) {	
		# List all Distributor
		//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
		$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
		$mgnStructureRecords = $marginStructureObj->fetchAllRecords();
	}

	# For First Process Need to remove after checking of database field (grouped_mgn_ids)
	if ($selProductMgn!="" && $groupedOptionValues=="") {
		$nArr = array();
		$k = 0;
		foreach ($mgnStructureRecords as $r) {
			$id = $r[0];
			$nArr[$k] = $id;
			if ($id==$selProductMgn) break;
			$k++;
		}
		if (sizeof($nArr)>0) $groupedOptionValues = implode(",",$nArr);
	}
	
	# Get Margin Structure Records
	if ($addMode) {
		//$marginStructureRecords = $marginStructureObj->fetchAllRecords();		
		$marginStructureRecords = $distReportDefinitionObj->getAllMgnStructureRecords($groupedOptionValues);	
	}
	else if ($editMode) $marginStructureRecords = $distReportDefinitionObj->getMarginStructureRecords($editDistReportDefinitionId, $groupedOptionValues);

	
	# Heading Section
	if ($editMode) $heading	= $label_editDistReportDefinition;
	else	       $heading	= $label_addDistReportDefinition;

	$ON_LOAD_PRINT_JS	= "libjs/DistributorReportDefinition.js";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDistributorReportDefinition" id="frmDistributorReportDefinition" action="DistributorReportDefinition.php" method="post">
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
					$bxHeader = "Distributor Wise Report Definition";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="45%">
		<?php
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
										<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('DistributorReportDefinition.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateDistReportDefinition(document.frmDistributorReportDefinition);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorReportDefinition.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistReportDefinition(document.frmDistributorReportDefinition);">												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidDistReportDefinitionId" value="<?=$editDistReportDefinitionId;?>">
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD colspan="2" nowrap align="center">
		<table>
			<tr>
  		<td class="fieldName" nowrap >*Distributor</td>
		<td>
		   <select name="selDistributor" id="selDistributor">
                                        <option value="">-- Select --</option>
					<?php	
					while ($dr=$distributorResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorName = stripSlash($dr[2]);	
						$selected = "";
						if ($selDistributor==$distributorId) $selected = "selected";	
					?>
                            		<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? }?>
					</select>
					<input type="hidden" name="hidDistributor" value="<?=$selDistributor?>">
		</td>
	</tr>
	<tr>
					<td nowrap class="fieldName" onMouseover="ShowTip('Please select a Margin head for calulating Rate per cases.');" onMouseout="UnTip();" style="line-height:normal;">*Product Margin<br> Based on</td>
					<td nowrap>
					<!-- this.form.submit(); -->
                                        <select name="selProductMgn" id="selProductMgn" onchange="assignOptionValue('<?=$mode?>', '<?=$editDistReportDefinitionId?>')">
                                        <option value="">-- Select --</option>
					<?php
					foreach ($mgnStructureRecords as $msr) {
						$marginStructureId 	= $msr[0];
						$marginStructureName	= stripSlash($msr[1]);
						$selected 	= "";
						if ($selProductMgn==$marginStructureId) $selected = "Selected";
					?>
                                 	<option value="<?=$marginStructureId?>" <?=$selected?>><?=$marginStructureName?></option>
					<? }?>
                                        </select>
		<input type="hidden" name="selOptionValue" id="selOptionValue" value="<?=$groupedOptionValues?>" />
					</td>
				</tr>
		</table>
		</TD>
	</tr>
	<tr>
		<td colspan="2" nowrap align="center">
		<table width="80%">
	
		<?php
			if ($selProductMgn) {
		?>
		<tr>
		<TD colspan="2" align="center">
			<table width="100%">
			<TR>
			<TD>
				<?php
					$entryHead = "Discount Splitup";
					$rbTopWidth = "";
					require("template/rbTop.php");
				?>
				<!--<fieldset>
					<legend class="listing-item">Discount Splitup</legend>-->
					<table>
					<TR><TD style="padding:15px;">
					<table  cellspacing="1" cellpadding="3" id="tblAddDiscount" class="newspaperType">
						<tr align="center">
							<th>&nbsp;</th>
							<th class="listing-head" style="padding-left:5px;padding-right:5px; text-align:center" nowrap="true">Margin</th>
							<th class="listing-head" style="padding-left:5px;padding-right:5px;text-align:center" nowrap="true">Display Name</th>
						</tr>		
						<?php
						if (sizeof($marginStructureRecords)>0) {	
							$m = 0;
							foreach ($marginStructureRecords as $msr) {
								$m++;
								$marginStructureId 	= $msr[0];
								$marginStructureName	= stripSlash($msr[1]);

								$selected = "";
								if ($editMode) {
									$distReportDefinitionEntryId = $msr[8];
									$selMarginHeadId = $msr[9];
									if ($marginStructureId==$selMarginHeadId) {
										$selected = "checked";
									}
									$displayName = $msr[10];
								}
						?>	
						<tr align="center">
							<td>
								<input type="checkbox" name="mgnStructId_<?=$m;?>" id="mgnStructId_<?=$m;?>" value="<?=$marginStructureId;?>" class="chkBox" <?=$selected?> />
							</td>
							<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true" align="left"><?=$marginStructureName?></td>
							<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true" align="left">
								<input type="text" name="mgnStructDisplayName_<?=$m;?>" id="mgnStructDisplayName_<?=$m;?>" size="18" value="<?=$displayName?>" />
							</td>				
						</tr>			
						<?php
							}
						}
						?>
						</table>
					
					</TD></TR>
					<!--<tr><TD height="5"></TD></tr>
					<tr>
						<TD style="padding-left:5px;padding-right:5px;">
							<a href="###" id='addRow' onclick="javascript:addNewMgnItemRow();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
						</TD>
					</tr>-->
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
		<?php
			} // checking ends
		?>
		<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$m?>">
                </table>
		</td>
				  </tr>
					<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorReportDefinition.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistReportDefinition(document.frmDistributorReportDefinition);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorReportDefinition.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistReportDefinition(document.frmDistributorReportDefinition);">												</td>
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
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
				<td colspan="3" align="center">
						<table width="20%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="0">
					  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">	
			<tr>
				<td align="right" nowrap class="listing-item">Distributor&nbsp;</td>
				<td align="right" nowrap valign="top">
				<select name="selDistributorFilter" onChange="this.form.submit();">
				 	<option value="">-- Select All --</option>
					<?php
					while ($dr=$distributorFilterResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorName = stripSlash($dr[2]);		
						$selected = ($selDistributorFilter==$distributorId)?"Selected":"";
					?>
                                        <option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? }?>
				  </select>
				</td>
				</tr>
			  </table>
		</td></tr>
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
			<td>
	<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
			<tr>
		<td nowrap="true">
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
		<tr>
			<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
			<td background="images/heading_bg.gif" class="pageName" nowrap style="background-repeat: repeat-x" valign="top" >&nbsp;Distributor Wise Report Definition&nbsp;</td>
			<td background="images/heading_bg.gif" class="pageName" align="right" nowrap valign="top" style="background-repeat: repeat-x">
			</td>
		</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distReportDefinitionRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorReportDefinition.php?selDistributorFilter=<?=$selDistributorFilter?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if ($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
	<tr>
		<td width="1" ></td>
		<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="2"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
		if ($distReportDefinitionRecordSize) {
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
      				$nav.= " <a href=\"DistributorReportDefinition.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorReportDefinition.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorReportDefinition.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\">>></a> ";
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
	<th width="20">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
	</th>
	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</th>
	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Product Margin Based on</th>
	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Discount Splitup</th>
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
			foreach ($distReportDefinitionRecords as $drd) {
				$i++;
				$distReportDefinitionId = $drd[0];
				$sDistributorName    = $drd[3];
				$rateMarginHead	     = $drd[4];
				$active=$drd[5];
				# Get Split up recs
				$getDiscountSplitupRecs = $distReportDefinitionObj->getDiscountSplitupRecs($distReportDefinitionId);
			?>
 <tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$distReportDefinitionId;?>" class="chkBox">
	</td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sDistributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$rateMarginHead;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="0" cellspacing="0" id="newspaper-b1-no-style">
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($getDiscountSplitupRecs)>0) {
						$nextRec = 0;
						$selMgnHead = "";
						foreach ($getDiscountSplitupRecs as $dR) {
							$selMgnHead = $dR[0];
							$nextRec++;
				?>
				<td class="listing-item" style="line-height:normal; font-size:9px;">
					<? if($nextRec>1) echo ",";?><?=$selMgnHead?></td>
					<? if($nextRec%$numLine == 0) { ?>
					</tr>
					<tr>
				<?php 
						}	
					 }
				 }
				?>
				</tr>
			</table>
	</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$distReportDefinitionId;?>,'editId');this.form.action='DistributorReportDefinition.php';" ><? } ?></td>
<? }?>

 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$distReportDefinitionId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$distReportDefinitionId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" id="editId" value=""><input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistributorReportDefinition.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorReportDefinition.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorReportDefinition.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\">>></a> ";
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
	<?php
		} else {
	?>
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?php
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
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distReportDefinitionRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorReportDefinition.php?selDistributorFilter=<?=$selDistributorFilter?>',700,600);"><? }?></td>
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
				<!-- Form fields end   -->
			</td>
		</tr>	
<input type="hidden" name="hidSelDistributorFilter" value="<?=$selDistributorFilter?>">
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	<?php 
		if ($addMode || $editMode) {
	?>
	<!--<SCRIPT LANGUAGE="JavaScript">
		function addNewMgnItemRow()
		{
			addNewItemRow('tblAddDiscount', '', '', '', '');	
		}
	</SCRIPT>-->
	<?php 
		} 
	?>

	<?php
		if ($addMode) {
	?>
	<!--<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewMgnItemRow();
	</SCRIPT>-->
	<?php 
		}
	?>

	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>