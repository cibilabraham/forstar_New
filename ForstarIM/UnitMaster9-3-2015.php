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

	# Add Unit Master 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	# Add
	if ($p["cmdAddUnit"]!="") {
	
		$unit		=	addSlash(trim($p["unit"]));
		$received_type	=	$p["selReceive"];
		
		if ($unit!="" &&  $received_type!="") {
			$unitRecIns	=	$unitmasterObj->addUnit($unit,$received_type);
				
			if ($unitRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddUnit);
				$sessObj->createSession("nextPage",$url_afterAddUnit.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddUnit;
			}
			$unitRecIns	=	false;
		}
	}

	# Edit Unit
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$unitRec		=	$unitmasterObj->find($editId);
		
		$editUnitId		=	$unitRec[0];
		$editUnit		=	stripSlash($unitRec[1]);
		$receivedBy		=	$unitRec[2];
		
	}
	
	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$unitId		=	$p["hidUnitId"];
		
		$unit		=	addSlash(trim($p["unit"]));
		$received_type	=	$p["selReceive"];
		
		if ($unitId!="" && $unit!="") {
			$unitRecUptd	= $unitmasterObj->updateUnit($unit,$received_type,$unitId);
		}
	
		if ($unitRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateUnit);
			$sessObj->createSession("nextPage",$url_afterUpdateUnit.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateUnit;
		}
		$unitRecUptd	=	false;
	}
	

	# Delete Unit
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$unitId	=	$p["delId_".$i];

			if ( $unitId!="" ) {				
				$unitRecDel = $unitmasterObj->deleteUnit($unitId);				
			}
		}
		if ($unitRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelUnit);
			$sessObj->createSession("nextPage",$url_afterDelUnit.$selection);
		} else {
			$errDel	=	$msg_failDelUnit;
		}
		$unitRecDel	=	false;
	}




if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$unitId	=	$p["confirmId"];


			if ($unitId!="") {
				// Checking the selected fish is link with any other process
				$unitRecConfirm = $unitmasterObj->updateUnitconfirm($unitId);
			}

		}
		if ($unitRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmunit);
			$sessObj->createSession("nextPage",$url_afterDelUnit.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$unitId = $p["confirmId"];

			if ($unitId!="") {
				#Check any entries exist
				
					$unitRecConfirm = $unitmasterObj->updateUnitReleaseconfirm($unitId);
				
			}
		}
		if ($unitRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmunit);
			$sessObj->createSession("nextPage",$url_afterDelUnit.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Units
	$unitRecords				=	$unitmasterObj->fetchPagingRecords($offset, $limit);
	$unitRecordsSize			=	sizeof($unitRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($unitmasterObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) $heading = $label_editUnit;
	else $heading = $label_addUnit;
	
	$help_lnk="help/hlp_UnitMaster.html";
	
	$ON_LOAD_PRINT_JS	= "libjs/unitmaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmUniMaster" action="UnitMaster.php" method="post">
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
								$bxHeader="Unit Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Unit Master  </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddUnit(document.frmUniMaster);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddUnit" class="button" value=" Add " onClick="return validateAddUnit(document.frmUniMaster);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidUnitId" value="<?=$editUnitId	;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
											  <td colspan="2" nowrap align="center" >
					<table width="200" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td class="fieldName">*Unit </td>
                                                  <td><input name="unit" type="text" id="unit" value="<?=$editUnit;?>" size="20" /></td>
                                                </tr>
						<tr>
                                                  <td nowrap="nowrap" class="fieldName">Received By</td>
                                                  <td nowrap="nowrap">
										<select name="selReceive">
										     <option value="G"<? if($receivedBy=='G') { echo "selected"; }?>>Grade Only</option>
										     <option value="C" <? if($receivedBy=='C') { echo "selected"; }?>>Count Only</option>
											 <option value="B" <? if($receivedBy=='B') { echo "selected"; }?>>Both</option>
										        </select>
						</td>
                                                </tr>
                                              </table></td>
						  </tr>						
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddUnit(document.frmUniMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishCategory.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddUnit" class="button" value=" Add " onClick="return validateAddUnit(document.frmUniMaster);">												</td>

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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$unitRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintUnitMaster.php',700,600);"><? }?></td>
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
									<td colspan="2" >
							<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($unitRecords) > 0 ) {
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
      	$nav.= " <a href=\"UnitMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"UnitMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"UnitMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<tr>
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th nowrap >Unit </th>
												<th nowrap="nowrap" align="center" > Available for </th>
												<? if($edit==true){?>
												<th width="45">&nbsp;</th>
												<? }?>
												<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
											</tr>
		</thead>
		<tbody>
											<?

													foreach($unitRecords as $ur)
													{
														
														$i++;
														$unitId			=	$ur[0];
														$unit			=	stripSlash($ur[1]);
														$unitReceived	=	$ur[2];
														if($unitReceived=='G'){
														$displayStatus	=	"Grade";
														} else if($unitReceived=='C'){
														$displayStatus	=	"Count";
														}
														else {
														$displayStatus	=	"Grade & Count";
														}
														$active=$ur[3];
														
											?>
											<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$unitId;?>" class="chkBox"></td>
												<td class="listing-item" nowrap ><?=$unit;?></td>
												<td class="listing-item" nowrap="nowrap"><?=$displayStatus?></td>
												<? if($edit==true){?>
												<td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$unitId;?>,'editId'); this.form.action='UnitMaster.php';"></td>
												<? }?>
												 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$unitId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$unitId;?>,'confirmId');"   >
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
      	$nav.= " <a href=\"UnitMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"UnitMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"UnitMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$unitRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintUnitMaster.php',700,600);"><? }?></td>
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