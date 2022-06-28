<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;

	$centerId		=	"";
	$centerName		=	"";
	$centerCode		=	"";
	$centerDesc		=	"";
	$landingCenterCode	=	"";
	$landingCenterName	=	"";
	$landingCenterDesc	=	"";

	$selection 		=	"?pageNo=".$p["pageNo"];
	
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
	
	# Add Qauality Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	if ($p["cmdAddLandingCenter"]!="") {
		$landingCenterCode	=	addSlash(trim($p["landingCenterCode"]));
		$landingCenterName	=	addSlash(trim($p["landingCenterName"]));
		$landingCenterDesc	=	addSlash($p["landingCenterDesc"]);
		$distance		=	$p["distance"];
		
		if ($landingCenterCode!="" && $landingCenterName!="") {
			$landingCenterRecIns	= $landingcenterObj->addLandingCenter($landingCenterCode, $landingCenterName, $landingCenterDesc, $distance);

			if ($landingCenterRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddLandingCenter);
				$sessObj->createSession("nextPage",$url_afterAddLandingCenter.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddLandingCenter;
			}
			$landingCenterRecIns		=	false;
		}
	}

	# Edit Landing Center 
	if ($p["editId"]!="") {
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$centerRec		=	$landingcenterObj->find($editIt);

		$landingCenterId	=	$centerRec[0];
		$landingCenterName	=	stripSlash($centerRec[1]);
		$landingCenterCode	=	stripSlash($centerRec[2]);
		$landingCenterDesc	=	stripSlash($centerRec[3]);
		$distance		=	$centerRec[4];
	}




	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$centerId	= $p["confirmId"];
			if ($centerId!="") {
				// Checking the selected fish is link with any other process
				$centerRecConfirm = $landingcenterObj->updateCentreconfirm($centerId);
			}

		}
		if ($centerRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmLandingCenter);
			$sessObj->createSession("nextPage",$url_afterUpdateCenter.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$centerId = $p["confirmId"];

			if ($centerId!="") {
				#Check any entries exist
				
					$centerRecConfirm = $landingcenterObj->updateCenterReleaseconfirm($centerId);
				
			}
		}
		if ($centerRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succReConfirmLandingCenter);
			$sessObj->createSession("nextPage",$url_afterUpdateCenter.$selection);
		} else {
			$errConfirm	=	$msg_failRlConfirm;
		}
		}



	# Upate	
	if ($p["cmdSaveChange"]!="" ) {
		
		$centerId	=	$p["hidLandingCenterId"];
		$centerCode	=	addSlash(trim($p["landingCenterCode"]));
		$centerName	=	addSlash(trim($p["landingCenterName"]));
		$centerDesc	=	addSlash($p["landingCenterDesc"]);
		$distance	=	$p["distance"];
		
		if ($centerId!="" && $centerName!="" && $centerCode!="") {
			$centerRecUptd		= $landingcenterObj->updateCenter($centerId, $centerName, $centerCode, $centerDesc, $distance);
		}
	
		if ($centerRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succCenterUpdate);
			$sessObj->createSession("nextPage", $url_afterUpdateCenter.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failCenterUpdate;
		}
		$centerRecUptd	=	false;
	}


	# Delete Landing center
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$centerId	= $p["delId_".$i];
			if ($centerId!="") {
				# Checking the selected Landing Center is link with any other process
				$landingCenterRecInUse = $landingcenterObj->landingCenterRecInUse($centerId);
				if (!$landingCenterRecInUse) {
					$centerRecDel = $landingcenterObj->deleteCenter($centerId);	
				}
			}
		}
		if ($centerRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCenter);
			$sessObj->createSession("nextPage",$url_afterDelCenter.$selection);
		} else {
			$errDel	=	$msg_failDelCenter;
		}
		$centerRecDel	=	false;
	}
	
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Landing Centers	
	$landingCenterRecords	=	$landingcenterObj->fetchPagingRecords($offset,$limit);
	$landingCenterSize	=	sizeof($landingCenterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($landingcenterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode)	$heading	=	$label_editLandingCenter;
	else		$heading	=	$label_addLandingCenter;
	
	$help_lnk="help/hlp_LandingCenter.html";	

	$ON_LOAD_PRINT_JS	= "libjs/landingcenter.js";	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmLandingCenter" action="LandingCenter.php" method="post">
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
								$bxHeader="Landing Center";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Landing Center</td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%">
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
								<td colspan="2" style="pading-left:10px;pading-right:10px;">
									<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

											<td colspan="2" align="center">
											<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('LandingCenter.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddLandingCenter(document.frmLandingCenter);">
											</td>
											
											<?} else{?>

											
											<td  colspan="2" align="center">
											<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('LandingCenter.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddLandingCenter" class="button" value=" Add " onClick="return validateAddLandingCenter(document.frmLandingCenter);">
											</td>

											<?}?>
											
										</tr>
										<input type="hidden" name="hidLandingCenterId" value="<?=$landingCenterId;?>">
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >*Code</td>
											<td><INPUT TYPE="text" NAME="landingCenterCode" size="15" value="<?=$landingCenterCode;?>"></td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >*Name</td>
											<td >
											<INPUT TYPE="text" NAME="landingCenterName" size="25" maxlength="25" value="<?=$landingCenterName;?>">
											</td>
										</tr>
                      <tr>
                        <td class="fieldName" nowrap >Distance</td>
			<td class="listing-item">
				<INPUT TYPE="text" NAME="distance" size="4" maxlength="5" value="<?=$distance;?>" style="text-align:right"> Km
			</td>
                      </tr>
                      <tr>
											<td class="fieldName" nowrap >Description</td>
											<td>
											<textarea name="landingCenterDesc" id="landingCenterDesc" rows="4"><?=$landingCenterDesc?></textarea>			
											<!--<INPUT TYPE="text" NAME="landingCenterDesc" size="40" value="<?//=$landingCenterDesc;?>">-->
											</td>
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

											<td colspan="2" align="center">
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LandingCenter.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddLandingCenter(document.frmLandingCenter);">
											</td>
											
											<?} else{?>

											<td  colspan="2" align="center">
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LandingCenter.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddLandingCenter" class="button" value=" Add " onClick="return validateAddLandingCenter(document.frmLandingCenter);">
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
		
		# Listing LandingCenter Starts
	?>
	</table>
							</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$landingCenterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintLandingCenter.php',700,600);"><? }?></td>
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
									<td colspan="2" style="pading-left:10px;pading-right:10px;">
										<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($landingCenterRecords) > 0 )
												{
													$i	=	0;
											?>
			<thead>
											<? if($maxpage>1){?>
											<tr>
		<td colspan="7" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"LandingCenter.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"LandingCenter.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"LandingCenter.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
											<tr align="center">
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
												<th nowrap style="padding-left:10px; padding-right:10px;">Code</th>
												<th style="padding-left:10px; padding-right:10px;">Name</th>
												<th style="padding-left:10px; padding-right:10px;">Distance<br> (Km)</th>
												<th style="padding-left:10px; padding-right:10px;">Description</th>
												<? if($edit==true){?>
												<th width="40">&nbsp;</th>
												<? }?>

												<? if($confirm==true){?>
												<th width="50"></th><? }?>
											</tr>
		</thead>
		<tbody>
							<?
							foreach($landingCenterRecords as $fr)
							{
								$i++;
								$centerId	=	$fr[0];
								$centerName	=	stripSlash($fr[1]);
								$centerCode	=	stripSlash($fr[2]);
								$centerDesc 	=	stripSlash($fr[3]);
								$distance	=	$fr[4];
								$active=$fr[5];
								$existingcount=$fr[6];
							?>
							<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();"  <?php }?> >
												<td width="20" align="center">
												<?php 
												 if ($existingcount==0) {
												?>		
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$centerId;?>" class="chkBox"></td>
												<?php 
												}
												?>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$centerCode;?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$centerName;?></td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$distance;?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$centerDesc;?></td>
												<? if($edit==true){?>
												<td class="listing-item" width="40" align="center">
												<?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$centerId;?>,'editId'); this.form.action='LandingCenter.php';"  >	<? } ?></td>
												<? }?>


	<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm"  onClick="assignValue(this.form,<?=$centerId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm"  onClick="assignValue(this.form,<?=$centerId;?>,'confirmId');" >
			<?php 
			
			//} ?>
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
			<td colspan="7" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"LandingCenter.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"LandingCenter.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"LandingCenter.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$landingCenterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintLandingCenter.php',700,600);"><? }?></td>
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
