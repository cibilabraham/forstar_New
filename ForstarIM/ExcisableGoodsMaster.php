<?php
	require("include/include.php");	

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

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;

	if ($p["state"]!="") $selStateId = $p["state"];	
	if ($p["productCategory"]!="") $selCategoryId = $p["productCategory"];	

	

	#Add a Record
	if ($p["cmdAdd"]!="") {
		
		$egName		= trim($p["egName"]);		
		
		if ($egName!="") {

			$excisableGoodsIns = $excisableGoodsMasterObj->addExcisableGoods($egName, $userId);
			
			if ($excisableGoodsIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddExcisableGoodsMaster);
				$sessObj->createSession("nextPage",$url_afterAddExcisableGoodsMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddExcisableGoodsMaster;
			}
			$excisableGoodsIns = false;
		} else {
			$addMode = true;
			$err = $msg_failAddExcisableGoodsMaster;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		$exGoodsId	= $p["hidExcisableGoodsId"];				
		$egName		= trim($p["egName"]);
					
		if ($exGoodsId!="" && $egName!="") {
			$updateExcisableGoodsRec = $excisableGoodsMasterObj->updateExcisableGoods($exGoodsId, $egName);
		}
		
		if ($updateExcisableGoodsRec) {
			$sessObj->createSession("displayMsg",$msg_succExcisableGoodsMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateExcisableGoodsMaster.$selection);
		} else {
			$editMode	=	true;			
			$err = $msg_failExcisableGoodsMasterUpdate.$newErr;
		}
		$exGoodsRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$exGoodsRec	= $excisableGoodsMasterObj->find($editId);
		$editExGoodsId = $exGoodsRec[0];		
		$egName 	= $exGoodsRec[1];
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];

		for ($i=1; $i<=$rowCount; $i++) {
			$exGoodsId	= $p["delId_".$i];

			if ($exGoodsId!="") {
				$recInUse  = $excisableGoodsMasterObj->checkExGoodsIdInUse($exGoodsId);
				if (!$recInUse) $exciseDutyRecDel = $excisableGoodsMasterObj->deleteExcisableGoodsRec($exGoodsId);			
			} 
		} // Loop ends here
		if ($exciseDutyRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelExcisableGoodsMaster);
			$sessObj->createSession("nextPage",$url_afterDelExcisableGoodsMaster.$selection);
		} else {
			if ($recInUse) $errDel = $msg_failDelExcisableGoodsMaster."<br>The record is alreay in use. ";
			else $errDel	=	$msg_failDelExcisableGoodsMaster;
		}
		$exciseDutyRecDel	=	false;
	}	


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$exGoodsId	=	$p["confirmId"];
			if ($exGoodsId!="") {
				// Checking the selected fish is link with any other process
				$exGoodsRecConfirm = $excisableGoodsMasterObj->updateexGoodsconfirm($exGoodsId);
			}

		}
		if ($exGoodsRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmexGoods);
			$sessObj->createSession("nextPage",$url_afterDelexGoods.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$exGoodsId = $p["confirmId"];

			if ($exGoodsId!="") {
				#Check any entries exist
				
					$exGoodsRecConfirm = $excisableGoodsMasterObj->updateexGoodsReleaseconfirm($exGoodsId);
				
			}
		}
		if ($exGoodsRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmexGoods);
			$sessObj->createSession("nextPage",$url_afterDelexGoods.$selection);
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
	
	
	# List all State Vat Master
	$excisableGoodsRecs = $excisableGoodsMasterObj->fetchAllPagingRecords($offset, $limit);
	$exciseDutyRecordSize = sizeof($excisableGoodsRecs);

	## -------------- Pagination Settings II -------------------
	$fetchAllEGRecs = $excisableGoodsMasterObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllEGRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editExcisableGoodsMaster;
	else	       $heading	=	$label_addExcisableGoodsMaster;

	
	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ExcisableGoodsMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmExcisableGoodsMaster" action="ExcisableGoodsMaster.php" method="post">
<input type="hidden" name="exciseDutyRateList" id="exciseDutyRateList" value="<?=$exciseDutyRateListId?>" readonly="true" />
<input type="hidden" name="newRateList" id="newRateList" value="" readonly="true" />

	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >	
	<tr><TD height="5"></TD></tr>		
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>
		</tr>
		<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Excisable Goods Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="45%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr><TD height="10"></TD></tr>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('ExcisableGoodsMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateExcisableGoodsMaster(document.frmExcisableGoodsMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExcisableGoodsMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateExcisableGoodsMaster(document.frmExcisableGoodsMaster);">	
											</td>
												<?}?>
											</tr>
					<input type="hidden" name="hidExcisableGoodsId" value="<?=$editExGoodsId;?>">
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD>
			<table>
				<TR>
					<TD class="fieldName">Name:</TD>
					<td>
						<input type="text" name="egName" id="egName" value="<?=$egName?>" size="32" />
					</td>
				</TR>
			</table>
		</TD>
	</tr>
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExcisableGoodsMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateExcisableGoodsMaster(document.frmExcisableGoodsMaster);">												</td>
											<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExcisableGoodsMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateExcisableGoodsMaster(document.frmExcisableGoodsMaster);">												</td>
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
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$exciseDutyRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintExcisableGoodsMaster.php',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($exciseDutyRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="3" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ExcisableGoodsMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ExcisableGoodsMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ExcisableGoodsMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
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
			
			foreach ($excisableGoodsRecs as $svr) {
				$i++;
				$exGoodsId 	= $svr[0];	
				$nameOfEG     	= $svr[1];
				$active=$svr[2];
				$existingrecords=$svr[3];
				
			?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
			<?php 
			
			if($existingrecords==0){
			?>
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$exGoodsId;?>" class="chkBox">			
			<?php 
			}
			?>
		</td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$nameOfEG;?></td>				
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$exGoodsId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ExcisableGoodsMaster.php';" ><? } ?></td>
<? }?>

 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value="  <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$exGoodsId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0){
				?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$exGoodsId;?>,'confirmId');" >
			<?php
			//}
			}?>
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
		<td colspan="3" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ExcisableGoodsMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ExcisableGoodsMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ExcisableGoodsMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$exciseDutyRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintExcisableGoodsMaster.php',700,600);"><? }?></td>
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