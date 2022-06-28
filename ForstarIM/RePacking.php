<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selection 	=	"?pageNo=".$p["pageNo"];
		
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
		$rePackingCode		=	addSlash(trim($p["rePackingCode"]));
		$rePackingReason	=	addSlash(trim($p["rePackingReason"]));
		$hidColumnCount		=	$p["hidColumnCount"];
		
		if ( $rePackingCode!="" ) {
			$rePackingMainRecIns	=	$repackingObj->addRePacking($rePackingCode,$rePackingReason);
			if($rePackingMainRecIns) $lastInsertedId	=	$databaseConnect->getLastInsertedId();
			
			for ($i=1; $i<=$hidColumnCount; $i++) {
			  		$packagingStructureId	=	$p["packagingStructureId_".$i];
					$selRepackType			=	$p["selRepackType_".$i];
					
					if ($selRepackType!="") {
						$rePackingTypeRecIns =	$repackingObj->addRePackingTypes($lastInsertedId,$packagingStructureId, $selRepackType);
					}
			}
			
			if ($rePackingMainRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddRePacking);
				$sessObj->createSession("nextPage",$url_afterAddRePacking.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddRePacking;
			}
			$packagingStructureRecIns	=	false;
		}
	}
		
	# Edit 	
	if( $p["editId"]!="" ){
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$rePackingRec		=	$repackingObj->find($editId);
		
		$editRepackingId	=	$rePackingRec[0];
		$rePackingCode		=	stripSlash($rePackingRec[1]);
		$rePackingReason	=	stripSlash($rePackingRec[2]);
		$packagingStructureRecords  = $repackingObj->getPackagingStructure($editRepackingId);
	}
	
	# Update
	if ( $p["cmdSaveChange"]!="" ) {
		
		$rePackingId		=	$p["hidRepackingId"];
		$rePackingCode		=	addSlash(trim($p["rePackingCode"]));
		$rePackingReason	=	addSlash(trim($p["rePackingReason"]));
		$hidColumnCount		=	$p["hidColumnCount"];
				
		if ($rePackingCode!="") {
			#Delete Re Pacing Entries Recs
			$delRePackingEntriesRec = $repackingObj->deleteRePackingEntries($rePackingId);
			#Update Main Rec
			$rePackingRecUptd	=	$repackingObj->updateRePacking($rePackingId,$rePackingCode,$rePackingReason);
			
			for ($i=1; $i<=$hidColumnCount; $i++) {
			  		$packagingStructureId	=	$p["packagingStructureId_".$i];
					$selRepackType		=	$p["selRepackType_".$i];
					
					if($selRepackType!="") {
						$rePackingTypeRecIns	=	$repackingObj->addRePackingTypes($rePackingId,$packagingStructureId, $selRepackType);
					}
			  }			
		}
	
		if ($rePackingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateRePacking);
			$sessObj->createSession("nextPage",$url_afterUpdateRePacking.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateRePacking;
		}
		$packagingStructureRecUptd	=	false;
	}
	

	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rePackingId	=	$p["delId_".$i];

			if ( $rePackingId!="" ) {
				#Delete Re Pacing Entries Recs
				$delRePackingEntriesRec = $repackingObj->deleteRePackingEntries($rePackingId);
				#Delete Re Packing Main Rec
				$rePackingRecDel		=	$repackingObj->deleteRePackingRec($rePackingId);				
			}
		}
		if ($rePackingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRePacking);
			$sessObj->createSession("nextPage",$url_afterDelRePacking.$selection);
		} else {
			$errDel	=	$msg_failDelRePacking;
		}
		$packagingStructureRecDel	=	false;
	}	


	if ($p["btnConfirm"]!="")	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rePackingId	=	$p["confirmId"];
			if ($rePackingId!="") {
				// Checking the selected fish is link with any other process
				$rePackingRecConfirm = $repackingObj->updateRePackingconfirm($rePackingId);
			}

		}
		if ($rePackingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmrePacking);
			$sessObj->createSession("nextPage",$url_afterDelRePacking.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$rePackingId= $p["confirmId"];

			if ($rePackingId!="") {
				#Check any entries exist
				
					$rePackingRecConfirm = $repackingObj->updateRePackingReleaseconfirm($rePackingId);
				
			}
		}
		if ($rePackingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmrePacking);
			$sessObj->createSession("nextPage",$url_afterDelRePacking.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="")		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "")	$pageNo=$g["pageNo"];
	else				$pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	#List All Record
	$rePackingRecords	= $repackingObj->fetchAllPagingRecords($offset, $limit);
	$rePackingRecordSize	= sizeof($rePackingRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($repackingObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List All Packaging Structure Records (IF ADDMODE)
	if ($addMode) {
		$packagingStructureRecords = $packagingstructureObj->fetchAllRecordsPackingActive();	
	}

	if ($editMode) $heading = $label_editRePacking;
	else $heading = $label_addRePacking;
	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/repacking.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmRePacking" action="RePacking.php" method="post">
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
								$bxHeader="Re-Packing";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Re-Packing </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="75%" align="center">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('RePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddRePacking(document.frmRePacking);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('RePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddRePacking(document.frmRePacking);">												</td>

												<?}?>
											</tr>
									<input type="hidden" name="hidRepackingId" value="<?=$editRepackingId;?>">
										<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;"> 
						<table width="50%">
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">*Code</td>
                                                  <td class="listing-item"><input name="rePackingCode" type="text" id="rePackingCode" value="<?=$rePackingCode?>" size="28"></td>
						  <td>&nbsp;</td>
						<td class="fieldName" nowrap="nowrap">*Reason</td>
                                                  <td class="listing-item"><input name="rePackingReason" type="text" id="rePackingReason" value="<?=$rePackingReason?>" size="40"></td>
                                                </tr>
                                              </table></td>
				</tr>
				<tr>
				  <td colspan="2"  align="center" style="padding-left:10px; padding-right:10px;">
											    <table width="100%" border="0">
                                                						<tr>
												<?
												$numColumn	=	8;
												$nextColumn	=	0;
												$j = 0;
												  foreach($packagingStructureRecords as $psr)
													{
														$j++;
														$packagingStructureId		=	$psr[0];
														$packagingStructureName	=	stripSlash($psr[1]);
														$nextColumn++;
														$rePackType	=	$psr[5];
														$active=$psr[6];
												 ?>
                                                  <td>
												  <table width="100%" border="0">
                                                    <tr>
                                                      <td class="fieldName" style="line-height:normal;"><?=$packagingStructureName?><input type="hidden" name="packagingStructureId_<?=$j?>" value="<?=$packagingStructureId?>"></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" height="20">
                                                        <select name="selRepackType_<?=$j?>" id="selRepackType_<?=$j?>">
                                                          <option value="">-- Select --</option>
														 <option value="A" <? if($rePackType=='A')  echo "Selected"; ?>>Add</option>
														 <option value="C" <? if($rePackType=='C') 	echo "Selected"; ?>>Change</option>
                                                        </select>
                                                      </td>
                                                    </tr>
                                                  </table>
												  </td>
												   <? 
												if($nextColumn%$numColumn == 0)
													{
													?>
													</tr>
													<tr>
												  <? 
												  	}
												  }
												  ?>
                                                </tr><input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$j?>">
                                              </table>
											  </td>
										  </tr>
										<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddRePacking(document.frmRePacking);">	
												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddRePacking(document.frmRePacking);">	
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
			# Listing Grade Starts
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
												<td><? if($del==true){?>
												<input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rePackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRePacking.php',700,600);"><? }?></td>
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
							<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($rePackingRecords) > 0 )
												{
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
      				$nav.= " <a href=\"RePacking.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RePacking.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RePacking.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<th nowrap style="padding-left:10px; padding-right:10px;">Code </th>
												<th style="padding-left:10px; padding-right:10px;">Reason</th>
												<? if($edit==true){?>
												<th width="50">&nbsp;</th>
												<? }?>
												<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
											</tr>
		</thead>
		<tbody>
											<?

													foreach($rePackingRecords as $rpr)
													{
														$i++;
														$rePackingId		=	$rpr[0];
														$rePackingCode		=	stripSlash($rpr[1]);
														$rePackingReason	=	stripSlash($rpr[2]);
														$active=$rpr[3];
											?>
											<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> >
												<td width="20"  align="center">
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$rePackingId;?>" class="chkBox"></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rePackingCode;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rePackingReason;?></td>
												<? if($edit==true){?>
												  <td class="listing-item" width="50" align="center">
												  <?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$rePackingId;?>,'editId');"><? }?></td>
											  <? }?>

											  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$rePackingId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$rePackingId;?>,'confirmId');" >
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
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RePacking.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RePacking.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RePacking.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete ** " style="background-color:#ff0000;color: white;" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rePackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRePacking.php',700,600);"><? }?></td>
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