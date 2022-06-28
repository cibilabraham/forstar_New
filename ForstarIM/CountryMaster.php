<?php
	require("include/include.php");
	require_once("lib/CountryMaster_ajax.php");
	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
		
	$selection 	= "?pageNo=".$p["pageNo"];	

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

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
		$editId	 = "";
		$p["editId"] = "";
	}

	#Add a Record
	if ($p["cmdAdd"]!="") {

		$countryName	= $p["countryName"];	
		$tableRowCount	= $p["hidTableRowCount"];
		
		if ($countryName!="") {						
			$countryRecIns = $countryMasterObj->addCountry($countryName, $userId);
			#Find the Last inserted Id From m_distributor Table
			$lastId = $databaseConnect->getLastInsertedId();
			if ($tableRowCount>0 && $countryRecIns!="") {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$portName	= $p["portName_".$i];
						$portCategory	= $p["portCategory_".$i];						

						if ($lastId!="" && $portName!="") {
							$countryPortIns = $countryMasterObj->addPortEntries($lastId, $portName, $portCategory);		
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here			
			if ($countryRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddCountryMaster);
				$sessObj->createSession("nextPage",$url_afterAddCountryMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddCountryMaster;
			}
			$countryRecIns = false;
		} else {
			$addMode = true;
			if ($entryExist) $err = $msg_failAddCountryMaster."<br>".$msgFailAddCountryExistRec;
			else $err = $msg_failAddCountryMaster;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		$countryId		= $p["hidStateVatId"];		
		$countryName		= $p["countryName"];	
		$tableRowCount		= $p["hidTableRowCount"];
	
		if ($countryId!="" && $countryName!="" ) {

			# Update Main Table			
			$countryRecUptd = $countryMasterObj->updateCountry($countryId, $countryName);
			
			for ($i=0; $i<$tableRowCount; $i++) {
				$status 	  = $p["status_".$i];
				$countryPortEntryId  = $p["countryPortEntryId_".$i];
				if ($status!='N') {
					$portName	= $p["portName_".$i];
					$portCategory	= $p["portCategory_".$i];
					
					if ($countryId!="" && $portName!="" && $countryPortEntryId!="") {
						$updateCountryPortRec = $countryMasterObj->updatePortEntries($countryPortEntryId, $portName, $portCategory);
					} else if ($countryId!="" && $portName!="" && $countryPortEntryId=="") {
						$countryPortIns = $countryMasterObj->addPortEntries($countryId, $portName, $portCategory);
					}
				} // Status Checking End

				if ($status=='N' && $countryPortEntryId!="") {
					$delCountryPortEntryRec = $countryMasterObj->delPortEntryRec($countryPortEntryId);
				}
			} // Loop ends here
		}
	
		if ($countryRecUptd || $countryRecIns) {
			$sessObj->createSession("displayMsg",$msg_succCountryMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateCountryMaster.$selection);
		} else {
			$editMode	=	true;
			//$err		=	$msg_failCountryMasterUpdate;
			if ($entryExist) $err = $msg_failCountryMasterUpdate."<br>".$msgFailAddCountryExistRec;
			else $err = $msg_failCountryMasterUpdate;
		}
		$countryRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$countryRec	= $countryMasterObj->find($editId);
		$editCountryId = $countryRec[0];
		$countryName	= $countryRec[1];
			
		# Get Vat Entry Records
		$countryPortRecs = $countryMasterObj->getPortRecs($editCountryId);			
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		$recInUse 	= false; 

		for ($i=1; $i<=$rowCount; $i++) {
			$countryId	= $p["delId_".$i];
						
			if ($countryId!="") {
				
				# checking the selected country is link with any other process
				$countryRecInUse = $countryMasterObj->countryRecInUse($countryId);				
				if (!$countryRecInUse) {
					# Delete From Entry Table
					$portEntryRecDel = $countryMasterObj->deletePortEntryRec($countryId);
					# Delete From Main Table
					$countryRecDel = $countryMasterObj->deleteCountryRec($countryId);
				} else if ($countryRecInUse) $recInUse = true;
			}
		}

		if ($countryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCountryMaster);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			if ($recInUse)	$errDel	= $msg_failDelCountryMaster."<br>The country you have selected is already in use.";
			else 		$errDel	= $msg_failDelCountryMaster;
		}
		$countryRecDel	=	false;
	}	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$countryId	=	$p["confirmId"];
			if ($countryId!="") {
				// Checking the selected fish is link with any other process
				$fishRecConfirm = $countryMasterObj->updateCountryconfirm($countryId);
			}

		}
		if ($fishRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmFish);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$countryId = $p["confirmId"];
			if ($countryId!="") {
				#Check any entries exist
				
					$fishRecConfirm = $countryMasterObj->updateCountryReleaseconfirm($countryId);
				
			}
		}
		if ($fishRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmFish);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
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
		
	
	# List all Recs
	$countryMasterRecs = $countryMasterObj->fetchAllPagingRecords($offset, $limit);
	$countryRecordSize = sizeof($countryMasterRecs);

	## -------------- Pagination Settings II -------------------
	$fetchAllRecs = $countryMasterObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editCountryMaster;
	else	       $heading	=	$label_addCountryMaster;

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/CountryMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmCountryMaster" action="CountryMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >	
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center" width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Country Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Country Master</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
	</td>
	</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%">
	<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('CountryMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateCountryMaster(document.frmCountryMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CountryMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateCountryMaster(document.frmCountryMaster);">												</td>

												<?}?>
											</tr>
					<input type="hidden" name="hidStateVatId" value="<?=$editCountryId;?>">
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;" align="center"><span id="msgCountryExist" class="err1" style="line-height:normal;"></span></TD></tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;" align="center">
		<table width="200">
								
		<tr>
	  		<td class="fieldName" nowrap >*Name</td>
			<td>
				<input type="text" name="countryName" id="countryName" value="<?=$countryName?>" onblur="xajax_chkCountryExist(document.getElementById('countryName').value, '<?=$mode?>', '<?=$editCountryId?>');" autocomplete="off" />	
			</td>
		</tr>	
		<!--  Dynamic Row Starts Here-->
		<tr id="catRow1">	
			<td colspan="2" style="padding-left:5px;padding-right:5px;">
			<table>
			<TR><TD>
				<table  cellspacing="1" cellpadding="3" id="tblPort" class="newspaperType">
				<tr align="center">
					<th style="padding-left:5px;padding-right:5px;" nowrap="true">*Seaport/Airport name</th>
					<th style="padding-left:5px;padding-right:5px;" nowrap="true">*Port Category</th>		
					<th>&nbsp;</th>			
				</tr>				
				</table>
			</TD></TR>
			<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="">
<tr id="catRow2"><TD height="5"></TD></tr>
<tr id="catRow3">
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
			</table>
			</td>
		</tr>	
	<!--  Dynamic Row Ends Here-->
               </table>
		</td>
		</tr>		
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CountryMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateCountryMaster(document.frmCountryMaster);">												</td>
											<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CountryMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateCountryMaster(document.frmCountryMaster);">												</td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$countryRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintCountryMaster.php',700,600);"><? }?></td>
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
	<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
		if ($countryRecordSize) {
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
      				$nav.= " <a href=\"CountryMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"CountryMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"CountryMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th style="padding-left:10px; padding-right:10px;">Name</th>		
		<th style="padding-left:10px; padding-right:10px;">Port</th>			
		<? if($edit==true){?>
			<th>&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
			<?php			
			foreach ($countryMasterRecs as $svr) {
				$i++;
				$countryId 	= $svr[0];	
				$cntryName	= $svr[1];							
				# No .of Combination
				$portRecs	= $countryMasterObj->getPortRecs($countryId);
				$active=$svr[2];
				$existingrecords=$svr[3];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
	<?php
     if ($existingrecords==0) {?>
	<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$countryId;?>" class="chkBox">			
	<?php }
	
	?>
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$cntryName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">
			<?php
			$numLine = 3;
			if (sizeof($portRecs)>0) {
				$nextRec = 0;						
				foreach ($portRecs as $cR) {
					$prtName = $cR[1];
					$nextRec++;
					if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $prtName;
					if($nextRec%$numLine == 0) echo "<br/>";
				}	
			 }				
			?>			
		</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		 <?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$countryId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='CountryMaster.php';" >
		<? } ?>
		</td>
<? }?>

<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$countryId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$countryId;?>,'confirmId');" >
			<?php //}
			} }?>
			
			
			
			
			</td>
												
<? }?>
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
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"CountryMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"CountryMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"CountryMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$countryRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintCountryMaster.php',700,600);"><? }?></td>
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
<input type="hidden" name="hidStateFilterId" value="<?=$stateFilterId?>">	
<input type="hidden" name="hidStateVatRateListFilterId" value="<?=$stateVatRateListFilterId?>">	
		<tr>
			<td height="10"></td>
		</tr>			
	</table>
	<?php 
		if ($addMode || $editMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		function addNewItem()
		{
			addNewRow('tblPort', '', '', '', '');	
		}
	</SCRIPT>
	<?php 
		} 
	?>

	<?php
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewItem();
	</SCRIPT>
	<?php 
		}
	?>
	<!-- Edit Record -->
	<script language="JavaScript">
	<?php
		if (sizeof($countryPortRecs)>0) {
			$j=0;
			foreach ($countryPortRecs as $ver) {			
				$countryPortEntryId 	= $ver[0];				
				$cntryPortName		= $ver[1];
				$cntryPortCategory	= $ver[2];
	?>	
		addNewRow('tblPort','<?=$countryPortEntryId?>', '<?=$cntryPortName?>', '<?=$cntryPortCategory?>');		
	<?php
			$j++;
			}
		}
	?>
	</script>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>