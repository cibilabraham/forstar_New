<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$qualityId		=	"";	
	$qualityName		=	"";
	$qualityCode		=	"";
	
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
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# Add Qauality Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	# Add
	if ($p["cmdAddQuality"]!="") {

		$includeBilling	=	$p["includeBilling"];
		$qualityCode	=	addSlash($p["qualityCode"]);
		$qualityName	=	addSlash($p["qualityName"]);
		$description	=	$p["description"];
		
		$chkUnique	=	$qualitymasterObj->checkDuplicate($qualityCode,$qualityName);
		
		if ($qualityCode!="" && $qualityName!="") {
			$qualityRecIns	=	$qualitymasterObj->addQuality($includeBilling,$qualityCode,$qualityName,$description);
			//$qualityRecIns	=	$qualitymasterObj->addQuality($qualityCode,$qualityName);

			if ($qualityRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddQuality);
				$sessObj->createSession("nextPage",$url_afterAddQuality.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddQuality;
			}
			$qualityRecIns		=	false;
		}else if ($chkUnique) $err = " Failed to quality master. Please make sure the request data you have entered is not duplicate. ";
	}
	
	# Edit Quality 
	if ($p["editId"]!="") {
		$editIt				=	$p["editId"];
		$editMode			=	true;
		$qualityRec			=	$qualitymasterObj->find($editIt);
	
		$qualityId			=	$qualityRec[0];
		$qualityName		=	stripSlash($qualityRec[1]);
		$qualityCode		=	stripSlash($qualityRec[2]);
		$includeBilling		=	$qualityRec[3];
		$description		=	$qualityRec[4];
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$qualityId		=	$p["hidQualityId"];
		$includeBilling	=	$p["includeBilling"];
	//die();
		$qualityCode	=	addSlash($p["qualityCode"]);
		$qualityName	=	addSlash($p["qualityName"]);
		$description	=	$p["description"];
				
		if ($qualityId!="" && $qualityName!="" && $qualityCode!="") {
			$qualityRecUptd		=	$qualitymasterObj->updateQuality($includeBilling,$qualityId,$qualityName, $qualityCode,$description);
			//$qualityRecUptd		=	$qualitymasterObj->updateQuality($qualityId,$qualityName, $qualityCode);
		}
	
		if ($qualityRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succQualityUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateQuality.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failQualityUpdate;
		}
		$qualityRecUptd	=	false;
	}

	
	# Delete Quality
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$qualityId	= $p["delId_".$i];

			if ($qualityId!="") {
				// Need to check the selected Quality is link with any other process 
				$qualityRecDel		=	$qualitymasterObj->deleteQuality($qualityId);	
			}
		}
		if ($qualityRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelQuality);
			$sessObj->createSession("nextPage",$url_afterDelCenter.$selection);
		} else {
			$errDel	=	 $msg_failDelCenter;
		}
		$qualityRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$qualityId	=	$p["confirmId"];


			if ($qualityId!="") {
				// Checking the selected fish is link with any other process
				$qualityRecConfirm = $qualitymasterObj->updateQualityconfirm($qualityId);
			}

		}
		if ($qualityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmquality);
			$sessObj->createSession("nextPage",$url_afterAddQuality.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmFishCategory;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$qualityId = $p["confirmId"];

			if ($qualityId!="") {
				#Check any entries exist
				
					$qualityRecConfirm = $qualitymasterObj->updateQualityReleaseconfirm($qualityId);
				
			}
		}
		if ($qualityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmquality);
			$sessObj->createSession("nextPage",$url_afterAddQuality.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmFishCategory;
		}
		}

	
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit;
	## ----------------- Pagination Settings I End ------------	

	#List All Quality 	
	$qualityMasterRecords	=	$qualitymasterObj->fetchPagingRecords($offset, $limit);
	$qualityMasterSize	=	sizeof($qualityMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($qualitymasterObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) $heading = $label_editQuality;
	else $heading = $label_addQuality;
		
	$help_lnk="help/hlp_QualityMaster.html";

	$ON_LOAD_PRINT_JS	= "libjs/qualitymaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmQualityMaster" action="QualityMaster.php" method="post">
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
								$bxHeader="Quality Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Quality Master</td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="72%">
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('QualityMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddQuality(document.frmQualityMaster);">
												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('QualityMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddQuality" class="button" value=" Add " onClick="return validateAddQuality(document.frmQualityMaster);">
												</td>

												<?}?>
												
											</tr>
											<input type="hidden" name="hidQualityId" value="<?=$qualityId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>	
											<tr>
												<td colspan="2" align="center">
												<table align="center" width="75%">
												<TR><TD>
													<table align="center">
														<tr>
															<td class="fieldName" nowrap  onMouseOver="ShowTip('Is it need to include in billing');" onMouseOut="UnTip();" >Include in billing</td>
															<td >
															<INPUT TYPE="checkbox" NAME="includeBilling" value="1" id='includeBilling'>
															</td>
														</tr>
																<tr>
															<td class="fieldName" nowrap >*Quality Code</td>
															<td><INPUT TYPE="text" NAME="qualityCode" size="15" value="<?=$qualityCode;?>"></td>
														</tr>
														<tr>
															<td class="fieldName" nowrap >*Quality Name </td>
															<td>
															<INPUT TYPE="text" NAME="qualityName" size="23" maxlength="25" value="<?=$qualityName;?>" >
															<b class="fieldName" onMouseOver="ShowTip('maximum number of characters should be 25');" onMouseOut="UnTip();"  >?</b>
															</td>
														</tr>
														<tr>
															<td class="fieldName" nowrap >Description</td>
															<td >
															<textarea name="description" id="description"><?=$description;?></textarea>
															</td>
														</tr>
													</table>
												</td></tr>
												</table>
												</td>
											</tr>							
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('QualityMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddQuality(document.frmQualityMaster);">
												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('QualityMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddQuality" class="button" value=" Add " onClick="return validateAddQuality(document.frmQualityMaster);">
												</td>
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
			# Listing Quality Starts
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$qualityMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintQualityMaster.php',700,600);"><? }?></td>
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
									<td colspan="2" >
							<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($qualityMasterRecords) > 0 )
												{
													$i	=	0;
											?>
		<thead>
		<? if($maxpage>1){?>
		<tr><td colspan="6" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"QualityMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"QualityMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"QualityMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr>
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
		<th nowrap >Code</th>
		<th>Name</th>
		<th>Description </th>
		<th>Include In Billing </th>
		
		<? if($edit==true){?>
		<th width="50">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
											<?

													foreach($qualityMasterRecords as $fr)
													{
														$i++;
														$qualityId		=	$fr[0];
														$qualityName	=	stripSlash($fr[1]);
														$qualityCode	=	stripSlash($fr[2]);
														$active=$fr[3];
														$existingrecord=$fr[4];
														$billinginclude=$fr[5];
														$change_requirement=$fr[6];
														$description=$fr[7];
											?>
											<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20" align="center">
												<?php if($change_requirement=="0") { 
												if($existingrecord==0){
												?>
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$qualityId;?>" class="chkBox">
												<?php 
												}
												}
												?></td>
												<td class="listing-item" nowrap ><?=$qualityCode;?></td>
												<td class="listing-item" ><?=$qualityName;?></td>
												<td class="listing-item" ><?=$description;?></td>
												<td class="listing-item" ><?php if($billinginclude=="0")
												{
												echo "No";
												}
												else
												{
												echo "Yes";
												}?></td>
												<? 
												if($edit==true){
												?>
												<td class="listing-item" width="50" align="center">
												<?php if($change_requirement=="0") { ?>
												 <?php if ($active!=1) {?>
												<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$qualityId;?>,'editId'); this.form.action='QualityMaster.php';">
												<? } ?>
												<?php }
												?></td>
												<? }?>
												 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if($change_requirement=="0") { ?>
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$qualityId;?>,'confirmId');"  >
			<?php } else if ($active==1){ 
			//if ($existingrecord==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$qualityId;?>,'confirmId');"  >
			<?php 
			//} 
			}?>
			<? }?>
			<?php
			}
			?>
			
			
			
			
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
				<td colspan="6" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"QualityMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"QualityMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"QualityMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$qualityMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintQualityMaster.php',700,600);"><? }?></td>
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
<script >
jQuery(document).ready(function(){
var billing='<?php echo $includeBilling;?>';
//alert(billing);
if( billing!="0" && billing!="")
{
document.getElementById('includeBilling').checked = true;
}
else{
document.getElementById('includeBilling').checked = false;
}

//var billing=includeBilling
});
</script>

